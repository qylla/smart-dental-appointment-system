<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\SymptomCheck;
use App\Models\User;
use App\Services\GroqService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * How many days ahead patients can book.
     */
    private const DAYS_AHEAD = 14;

    public function __construct(private GroqService $groq)
    {
    }

    /**
     * Show the booking flow.
     *
     * The slot grid is built from real availability rather than a hardcoded
     * list, so a patient never sees a time that someone else already took.
     */
    public function index(): View
    {
        return view('booking', [
            'slots'        => ClinicSetting::get('slots'),
            'availability' => $this->buildAvailability(),
        ]);
    }

    /**
     * Guest-facing symptom check, used by the public /symptom-checker page.
     *
     * No login required. The check is tied to a guest_token (stored in the
     * session) rather than a user_id, so it can be claimed later if the
     * visitor registers — see AuthController::claimGuestSymptomCheck().
     */
    public function checkSymptomsGuest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'symptoms'    => ['nullable', 'array'],
            'symptoms.*'  => ['string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $symptoms = $data['symptoms'] ?? [];

        $result = $this->analyse($symptoms, $data['description'] ?? '');

        $guestToken = $request->session()->get('guest_token');

        if (! $guestToken) {
            $guestToken = (string) \Illuminate\Support\Str::uuid();
            $request->session()->put('guest_token', $guestToken);
        }

        SymptomCheck::create([
            'user_id'     => Auth::id(), // null for guests, filled in if already logged in
            'guest_token' => Auth::check() ? null : $guestToken,
            'symptoms'    => $symptoms,
            'description' => $data['description'] ?? null,
            'severity'    => $result['severity'],
            'verdict'     => $result['verdict'],
            'advice'      => $result['advice'],
        ]);

        return response()->json([
            'severity' => $result['severity'],
            'verdict'  => $result['verdict'],
            'advice'   => $result['advice'],
            'on_topic' => $result['on_topic'],
            'source'   => $result['source'], // 'ai' or 'rule-based' — for debugging/demo only
        ]);
    }

    /**
     * Store the symptom check on its own, before the patient picks a slot.
     * Called by fetch() from step 1 of the booking flow.
     */
    public function storeSymptomCheck(Request $request): JsonResponse
    {
        $data = $request->validate([
            'symptoms'    => ['nullable', 'array'],
            'symptoms.*'  => ['string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $symptoms = $data['symptoms'] ?? [];

        $result = $this->analyse($symptoms, $data['description'] ?? '');

        $check = SymptomCheck::create([
            'user_id'     => Auth::id(),
            'symptoms'    => $symptoms,
            'description' => $data['description'] ?? null,
            'severity'    => $result['severity'],
            'verdict'     => $result['verdict'],
            'advice'      => $result['advice'],
        ]);

        return response()->json([
            'id'       => $check->id,
            'severity' => $result['severity'],
            'verdict'  => $result['verdict'],
            'advice'   => $result['advice'],
            'on_topic' => $result['on_topic'],
        ]);
    }

    /**
     * Confirm the booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'symptom_check_id' => ['nullable', 'exists:symptom_checks,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'in:' . implode(',', ClinicSetting::get('slots'))],
            'patient_note'     => ['nullable', 'string', 'max:1000'],
        ]);

        $check = $data['symptom_check_id']
            ? SymptomCheck::find($data['symptom_check_id'])
            : null;

        // Only accept a symptom check that actually belongs to this patient.
        if ($check && $check->user_id !== Auth::id()) {
            $check = null;
        }

        $doctors = User::doctors()->pluck('id');

        if ($doctors->isEmpty()) {
            return back()->withErrors([
                'slot' => 'No dentist is available right now. Please contact the clinic.',
            ]);
        }

        try {
            $appointment = DB::transaction(function () use ($data, $check, $doctors) {
                // Lock every booking for this exact slot so two people booking
                // at the same moment are assigned different doctors, not the same one.
                $busyDoctorIds = Appointment::where('appointment_date', $data['appointment_date'])
                    ->where('appointment_time', $data['appointment_time'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->lockForUpdate()
                    ->pluck('doctor_id')
                    ->filter()
                    ->all();

                // Doctors with no booking in this slot are the ones we can assign.
                $freeDoctors = $doctors->reject(fn ($id) => in_array($id, $busyDoctorIds, true));

                if ($freeDoctors->isEmpty()) {
                    throw new \RuntimeException('slot_taken');
                }

                // Load balance: pick the free doctor with the fewest appointments
                // on that day, so patients are spread evenly across the team.
                $loads = Appointment::whereDate('appointment_date', $data['appointment_date'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereIn('doctor_id', $freeDoctors)
                    ->selectRaw('doctor_id, count(*) as total')
                    ->groupBy('doctor_id')
                    ->pluck('total', 'doctor_id');

                $doctorId = $freeDoctors
                    ->sortBy(fn ($id) => $loads[$id] ?? 0)
                    ->first();

                return Appointment::create([
                    'user_id'          => Auth::id(),
                    'doctor_id'        => $doctorId,
                    'symptom_check_id' => $check?->id,
                    'appointment_date' => $data['appointment_date'],
                    'appointment_time' => $data['appointment_time'],
                    'status'           => 'pending',
                    'urgency'          => $check?->severity ?? 'routine',
                    'patient_note'     => $data['patient_note'] ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors([
                'slot' => 'Sorry, that slot was just taken. Please pick another time.',
            ]);
        } catch (QueryException $e) {
            // The unique index on the appointments table is the final guard.
            return back()->withErrors([
                'slot' => 'That slot is no longer available. Please pick another time.',
            ]);
        }

        return redirect()->route('settings')
            ->with('status', 'Appointment booked for '
                . $appointment->appointment_date->format('D, j M') . ' at '
                . $appointment->time_label . '. The clinic will confirm shortly.');
    }

    /* ---------------- Helpers ---------------- */

    /**
     * Build a date => [slots] map for the next fortnight.
     *
     * With several dentists, a slot is only "booked" once every one of them
     * is taken at that time. So we count how many doctors are busy per slot
     * and compare against the total number of doctors.
     */
    private function buildAvailability(): array
    {
        $start       = today();
        $end         = today()->addDays(self::DAYS_AHEAD);
        $openDays    = ClinicSetting::get('open_days');
        $slots       = ClinicSetting::get('slots');
        $doctorCount = User::doctors()->count();

        // date => time => number of doctors already booked.
        $taken = Appointment::whereBetween('appointment_date', [$start, $end])
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('doctor_id')
            ->get(['appointment_date', 'appointment_time'])
            ->groupBy(fn ($a) => $a->appointment_date->toDateString())
            ->map(fn ($group) => $group
                ->groupBy(fn ($a) => substr((string) $a->appointment_time, 0, 5))
                ->map(fn ($slot) => $slot->count()));

        $availability = [];

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            // Skip days the clinic is closed, per admin settings.
            if (! in_array($day->dayOfWeek, $openDays, true)) {
                continue;
            }

            $key      = $day->toDateString();
            $dayTaken = $taken[$key] ?? collect();

            $availability[] = [
                'date'  => $key,
                'dow'   => $day->format('D'),
                'dnum'  => $day->format('j'),
                'label' => $day->format('D, j M'),
                'slots' => collect($slots)->map(fn ($slot) => [
                    'time'   => $slot,
                    'label'  => date('g:i A', strtotime($slot)),
                    // Full only when as many doctors are booked as exist.
                    'booked' => ($dayTaken[$slot] ?? 0) >= $doctorCount,
                ])->all(),
            ];
        }

        return $availability;
    }

    /**
     * Work out how urgent the reported symptoms are.
     *
     * Tries the Groq API first for a more nuanced read of the patient's
     * own description. If Groq is unavailable, slow, misconfigured, or
     * returns something we can't trust, we silently fall back to the
     * rule-based analyser below — booking must never break because of the AI.
     *
     * The 'source' key ('ai' or 'rule-based') lets callers/tests confirm
     * which path actually ran, instead of guessing from wording alone.
     */
    private function analyse(array $symptoms, string $description): array
    {
        $aiResult = $this->groq->analyseSymptoms($symptoms, $description);

        if ($aiResult) {
            return $aiResult + ['source' => 'ai'];
        }

        return $this->analyseRuleBased($symptoms, $description) + ['source' => 'rule-based'];
    }

    /**
     * The original rule-based fallback. Kept exactly as-is so behaviour is
     * unchanged whenever the Gemini API is unavailable.
     */
    private function analyseRuleBased(array $symptoms, string $description): array
    {
        $rank = ['routine' => 1, 'urgent' => 2, 'critical' => 3];

        $map = [
            'Toothache'                  => 'routine',
            'Tooth sensitivity'          => 'routine',
            'Just a check-up'            => 'routine',
            'Bleeding gums'              => 'urgent',
            'Swollen gums'               => 'urgent',
            'Broken / knocked-out tooth' => 'critical',
            'Severe pain'                => 'critical',
        ];

        $worst = 'routine';

        foreach ($symptoms as $symptom) {
            $level = $map[$symptom] ?? 'routine';
            if ($rank[$level] > $rank[$worst]) {
                $worst = $level;
            }
        }

        // Escalate if the free-text mentions something serious.
        if ($worst === 'routine'
            && preg_match('/severe|unbearable|swelling|broken|knocked|bengkak|teruk|sangat sakit/i', $description)) {
            $worst = 'urgent';
        }

        return match ($worst) {
            'critical' => [
                'severity' => 'critical',
                'verdict'  => 'Critical — see as soon as possible',
                'advice'   => "We recommend the earliest slot available. If pain is severe or you're bleeding heavily, consider visiting an emergency clinic now.",
                'on_topic' => true,
            ],
            'urgent' => [
                'severity' => 'urgent',
                'verdict'  => 'Best seen in the next day or two',
                'advice'   => "We've highlighted the soonest available slots for you below.",
                'on_topic' => true,
            ],
            default => [
                'severity' => 'routine',
                'verdict'  => 'Looks like a routine case',
                'advice'   => 'A regular appointment slot should work well for this. Pick any time that suits you.',
                'on_topic' => true,
            ],
        };
    }
}
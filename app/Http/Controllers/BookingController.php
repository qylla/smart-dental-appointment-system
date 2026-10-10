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
use App\Services\AppointmentMailer;

class BookingController extends Controller
{
    /**
     * How many days ahead patients can book.
     */
    private const DAYS_AHEAD = 14;

    public function __construct(
        private GroqService $groq,
        private \App\Services\SlotService $slots,
    ) {}

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
            'availability' => $this->slots->availability(),
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
    public function store(Request $request, AppointmentMailer $mailer): RedirectResponse
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

        try {
            $appointment = $this->slots->book([
                'user_id'          => Auth::id(),
                'symptom_check_id' => $check?->id,
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'urgency'          => $check?->severity ?? 'routine',
                'patient_note'     => $data['patient_note'] ?? null,
            ]);
        } catch (QueryException $e) {
            // A real database error, not a clash. Let it surface.
            throw $e;
        } catch (\RuntimeException $e) {
            return back()->withErrors([
                'slot' => match ($e->getMessage()) {
                    'no_doctors' => 'No dentist is available right now. Please contact the clinic.',
                    'slot_past'  => 'That time has already passed. Please pick a later slot.',
                    'slot_closed' => 'The clinic is closed at that time. Please pick another slot.',
                    default      => 'Sorry, that slot was just taken. Please pick another time.',
                },
            ]);
        }
        // The dentist hears about it now. The patient gets their email
        // later, when the clinic confirms the booking.
        $mailer->newForDoctor($appointment);
        return redirect()->route('settings')
            ->with('status', 'Appointment booked for '
                . $appointment->appointment_date->format('D, j M') . ' at '
                . $appointment->time_label . '. The clinic will confirm shortly.');
    }

    /* ---------------- Helpers ---------------- */
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
        if (
            $worst === 'routine'
            && preg_match('/severe|unbearable|swelling|broken|knocked|bengkak|teruk|sangat sakit/i', $description)
        ) {
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

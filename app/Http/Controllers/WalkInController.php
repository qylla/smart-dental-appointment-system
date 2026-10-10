<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\User;
use App\Services\AppointmentMailer;
use App\Services\SlotService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Lets counter staff book an appointment on a patient's behalf, for
 * patients who walk in or phone the clinic.
 *
 * Uses the same SlotService as online booking, so all three channels share
 * one schedule: a slot booked here disappears from online availability
 * straight away, and the same clash checks apply.
 */
class WalkInController extends Controller
{
    public function __construct(
        private SlotService $slots,
        private AppointmentMailer $mailer,
    ) {
    }

    public function create(): View
    {
        return view('admin.walk-in', [
            'availability' => $this->slots->availability(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'channel'          => ['required', Rule::in(['walk_in', 'phone'])],
            'name'             => ['required', 'string', 'max:255'],
            'phone'            => ['required', 'string', 'max:30'],
            'email'            => ['nullable', 'email', 'max:255'],
            'date_of_birth'    => ['nullable', 'date', 'before:today'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', Rule::in(ClinicSetting::get('slots'))],
            'urgency'          => ['required', Rule::in(['routine', 'urgent', 'critical'])],
            'admin_note'       => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            // One transaction: if the slot turns out to be taken, a patient
            // account created a moment earlier is rolled back as well.
            [$appointment, $patient, $isNew] = DB::transaction(function () use ($data) {
                [$patient, $isNew] = $this->findOrCreatePatient($data);

                $appointment = $this->slots->book([
                    'user_id'          => $patient->id,
                    'appointment_date' => $data['appointment_date'],
                    'appointment_time' => $data['appointment_time'],
                    // Staff entered it themselves, so no approval step needed.
                    'status'           => 'confirmed',
                    'channel'          => $data['channel'],
                    'urgency'          => $data['urgency'],
                    'admin_note'       => $data['admin_note'] ?? null,
                ]);

                return [$appointment, $patient, $isNew];
            });
        } catch (QueryException $e) {
            // QueryException is itself a RuntimeException. Re-throw it so a
            // real database problem shows up as an error (and in laravel.log)
            // instead of being disguised as "slot taken" below.
            throw $e;
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors([
                'appointment_time' => match ($e->getMessage()) {
                    'no_doctors'  => 'No dentist accounts exist yet. Add one under Settings > Staff.',
                    'slot_past'   => 'That time has already passed. Please pick a later slot.',
                    'slot_closed' => 'The clinic is closed at that time. Please pick another slot.',
                    default       => 'That slot was just taken. Please pick another time.',
                },
            ]);
        }

        $appointment->load('doctor');

        // Walk-ins are confirmed straight away, so the patient (if they gave
        // an email) gets the confirmation now, and the dentist is told.
        // Neither can fail the booking: AppointmentMailer catches errors.
        $this->mailer->confirmed($appointment);
        $this->mailer->newForDoctor($appointment);

        $message = ($data['channel'] === 'phone' ? 'Phone booking' : 'Walk-in')
            . " saved: {$patient->name}, "
            . $appointment->appointment_date->format('D, j M') . ' at ' . $appointment->time_label
            . ' with ' . ($appointment->doctor?->name ?? 'an available dentist') . '.'
            . ($isNew ? ' New patient record created.' : ' Matched an existing patient record.');

        return redirect()
            ->route('admin.dashboard', [
                'day' => $appointment->appointment_date->isToday() ? 'today' : 'upcoming',
            ])
            ->withFragment('appointments')
            ->with('status', $message);
    }

    /**
     * Reuse the patient if they already exist, otherwise create them.
     *
     * Same phone number = same person. Numbers are compared as digits only,
     * so "012-345 6789" and "0123456789" match. Done in PHP rather than SQL,
     * like the patients list, since a clinic's patient table is small.
     *
     * @return array{0: User, 1: bool} the patient, and whether they are new
     */
    private function findOrCreatePatient(array $data): array
    {
        $digits = preg_replace('/\D/', '', $data['phone']);

        $existing = User::patients()->get()
            ->first(fn (User $u) => $u->phone && preg_replace('/\D/', '', $u->phone) === $digits);

        if (! $existing && ! empty($data['email'])) {
            $existing = User::where('email', $data['email'])->first();

            if ($existing && ! $existing->isPatient()) {
                throw ValidationException::withMessages([
                    'email' => 'That email belongs to a staff account. Leave it blank or use another.',
                ]);
            }
        }

        if ($existing) {
            return [$existing, false];
        }

        $patient = User::create([
            'name'          => $data['name'],
            'phone'         => $data['phone'],
            'email'         => $data['email'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'role'          => 'patient',
            // Record-only account: nobody knows this password, so it can't be
            // used to log in. Giving walk-in patients online access later
            // would need a password reset flow, which is future scope.
            'password'      => Str::random(40),
        ]);

        return [$patient, true];
    }
}
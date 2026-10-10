<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Services\AppointmentMailer;
use App\Services\SlotService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Lets the front desk move an appointment to another date or time,
 * for patients who call or message the clinic to reschedule.
 *
 * Patients can't reschedule themselves, so this keeps the clinic in control
 * of the schedule. The move goes through SlotService, so it gets the same
 * clash, leave and closure checks as a new booking.
 */
class RescheduleController extends Controller
{
    public function __construct(
        private SlotService $slots,
        private AppointmentMailer $mailer,
    ) {
    }

    public function edit(Appointment $appointment): View|RedirectResponse
    {
        if (! $this->canMove($appointment)) {
            return $this->notMovable();
        }

        $appointment->load(['patient', 'doctor']);

        return view('admin.reschedule', [
            'appointment'  => $appointment,
            'availability' => $this->slots->availability(),
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        if (! $this->canMove($appointment)) {
            return $this->notMovable();
        }

        $data = $request->validate([
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', Rule::in(ClinicSetting::get('slots'))],
            'reason'           => ['nullable', 'string', 'max:255'],
        ]);

        $sameSlot = $appointment->appointment_date->toDateString() === $data['appointment_date']
            && substr((string) $appointment->appointment_time, 0, 5) === $data['appointment_time'];

        if ($sameSlot) {
            return back()->withInput()->withErrors([
                'slot' => 'That is already the appointment time. Pick a different slot.',
            ]);
        }

        $from        = $appointment->appointment_date->format('D, j M') . ', ' . $appointment->time_label;
        $oldDoctorId = $appointment->doctor_id;
        $oldDoctor   = $appointment->doctor;

        try {
            $this->slots->move($appointment, $data['appointment_date'], $data['appointment_time']);
        } catch (QueryException $e) {
            // A real database error, not a clash. Let it surface.
            throw $e;
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors([
                'slot' => match ($e->getMessage()) {
                    'slot_past'   => 'That time has already passed. Pick a later slot.',
                    'slot_closed' => 'The clinic is closed at that time. Pick another slot.',
                    default       => 'No dentist is free at that time any more. Pick another slot.',
                },
            ]);
        }

        // Keep a short record of the change on the appointment itself.
        $line = 'Rescheduled from ' . $from
            . (! empty($data['reason']) ? ' (' . $data['reason'] . ')' : '') . '.';

        $appointment->update([
            'admin_note' => trim(($appointment->admin_note ? $appointment->admin_note . "\n" : '') . $line),
        ]);

        $appointment->refresh()->load(['patient', 'doctor']);

        // A confirmed patient is told about the new time. A pending one will
        // get the normal confirmation email, with the new time, once confirmed.
        if ($appointment->status === 'confirmed') {
            $this->mailer->rescheduled($appointment, $from);
        }

        // Dentists hear about it too (if their notifications are on).
        if ($appointment->doctor_id !== $oldDoctorId) {
            // Handed to another dentist: the new one gets a normal
            // new-appointment email, the old one is told it's off their list.
            $this->mailer->newForDoctor($appointment);

            if ($oldDoctor) {
                $this->mailer->rescheduledForDoctor($appointment, $oldDoctor, $from);
            }
        } elseif ($appointment->doctor) {
            // Same dentist, new time.
            $this->mailer->rescheduledForDoctor($appointment, $appointment->doctor, $from);
        }

        return redirect()
            ->to(route('admin.dashboard', ['day' => 'all']) . '#appointments')
            ->with('status', $appointment->patient->name . "'s appointment moved to "
                . $appointment->appointment_date->format('D, j M') . ', ' . $appointment->time_label
                . ' with ' . ($appointment->doctor?->name ?? 'no dentist') . '.');
    }

    /** Only upcoming, active appointments can be moved. */
    private function canMove(Appointment $appointment): bool
    {
        return in_array($appointment->status, ['pending', 'confirmed'], true);
    }

    private function notMovable(): RedirectResponse
    {
        return redirect()
            ->to(route('admin.dashboard') . '#appointments')
            ->withErrors(['reassign' => 'Only pending or confirmed appointments can be rescheduled.']);
    }
}
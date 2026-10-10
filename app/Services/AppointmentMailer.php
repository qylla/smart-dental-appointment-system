<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Mail\AppointmentConfirmed;
use App\Mail\AppointmentRescheduled;
use App\Mail\AppointmentRescheduledForDoctor;
use App\Mail\NewAppointmentForDoctor;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends appointment emails, and makes sure a mail problem can never break
 * a booking.
 *
 * Same principle as the AI fallback: email is an extra, not a requirement.
 * If Gmail is down, the password is wrong, or the patient has no email
 * (e.g. a walk-in), the booking still goes through and the failure is
 * written to laravel.log instead.
 */
class AppointmentMailer
{
    /**
     * Tell the patient their appointment is confirmed.
     */
    public function confirmed(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient', 'doctor']);

        $email = $appointment->patient?->email;

        // Walk-in and phone patients often have no email. Skip quietly.
        if (! $email) {
            return;
        }

        $this->send($email, new AppointmentConfirmed($appointment), $appointment);
    }

    /**
     * Tell the patient their confirmed appointment was moved by the clinic.
     *
     * @param string $previous The old date and time, shown in the email.
     */
    public function rescheduled(Appointment $appointment, string $previous): void
    {
        $appointment->loadMissing(['patient', 'doctor']);

        $email = $appointment->patient?->email;

        if (! $email) {
            return;
        }

        $this->send($email, new AppointmentRescheduled($appointment, $previous), $appointment);
    }

    /**
     * Tell a dentist that one of their appointments was moved.
     * Uses the same on/off setting as new-appointment emails.
     *
     * @param string $previous The old date and time, shown in the email.
     */
    public function rescheduledForDoctor(Appointment $appointment, User $doctor, string $previous): void
    {
        $appointment->loadMissing(['patient', 'doctor']);

        if (! $doctor->email || ! $doctor->notify_new_appointment) {
            return;
        }

        $this->send($doctor->email, new AppointmentRescheduledForDoctor($appointment, $doctor, $previous), $appointment);
    }

    /**
     * Tell the assigned dentist about a new appointment, if they want to know.
     */
    public function newForDoctor(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient', 'doctor']);

        $doctor = $appointment->doctor;

        // Respect the dentist's own notification setting.
        if (! $doctor?->email || ! $doctor->notify_new_appointment) {
            return;
        }

        $this->send($doctor->email, new NewAppointmentForDoctor($appointment), $appointment);
    }

    private function send(string $to, Mailable $mail, Appointment $appointment): void
    {
        try {
            Mail::to($to)->send($mail);
        } catch (\Throwable $e) {
            Log::warning('Appointment email not sent: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'mail'           => class_basename($mail),
            ]);
        }
    }
}
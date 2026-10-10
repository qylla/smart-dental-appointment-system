<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to a dentist when the clinic moves one of their appointments.
 *
 * $doctor is who receives it. Usually that is still the appointment's
 * dentist; if the move handed the patient to someone else, the original
 * dentist gets this so they know the patient is off their schedule.
 */
class AppointmentRescheduledForDoctor extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $previous The old date and time, e.g. "Tue, 30 Sep, 9:00 AM"
     */
    public function __construct(
        public Appointment $appointment,
        public User $doctor,
        public string $previous,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Appointment rescheduled - KPZ Dentalcare');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.appointment-rescheduled-doctor', with: [
            'handedOver' => $this->appointment->doctor_id !== $this->doctor->id,
        ]);
    }
}
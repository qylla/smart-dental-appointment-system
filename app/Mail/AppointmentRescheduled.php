<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the patient when the clinic moves a confirmed appointment.
 *
 * Like the confirmation email, it holds no symptoms or clinical detail.
 */
class AppointmentRescheduled extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $previous The old date and time, e.g. "Tue, 30 Sep, 9:00 AM"
     */
    public function __construct(public Appointment $appointment, public string $previous)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your appointment has been rescheduled - KPZ Dentalcare');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.appointment-rescheduled');
    }
}
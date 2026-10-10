<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to a dentist when a new appointment is assigned to them, if they
 * have "new appointment" notifications switched on in their settings.
 */
class NewAppointmentForDoctor extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New appointment assigned - KPZ Dentalcare');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.new-appointment-doctor');
    }
}
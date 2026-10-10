<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the patient once the clinic confirms their appointment.
 *
 * Deliberately contains no symptoms or clinical detail: just when, where
 * and with whom. Health information doesn't belong in an email inbox.
 */
class AppointmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your appointment is confirmed - KPZ Dentalcare');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.appointment-confirmed');
    }
}
<x-mail::message>
# Your appointment has a new time

Hi {{ $appointment->patient->name }},

As requested, the clinic has moved your appointment at **Klinik Pergigian Dr. Zuraida**.

<x-mail::panel>
**New date:** {{ $appointment->appointment_date->format('l, j F Y') }}<br>
**New time:** {{ $appointment->time_label }}<br>
**Dentist:** {{ $appointment->doctor?->name ?? 'Assigned when you arrive' }}
</x-mail::panel>

Your previous slot ({{ $previous }}) has been released.

If this doesn't look right, please contact the clinic.

@if (config('clinic.whatsapp'))
<x-mail::button :url="'https://wa.me/' . config('clinic.whatsapp')">
Contact the clinic on WhatsApp
</x-mail::button>
@endif

Thank you,<br>
KPZ Dentalcare
</x-mail::message>
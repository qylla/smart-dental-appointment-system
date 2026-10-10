<x-mail::message>
# Your appointment is confirmed

Hi {{ $appointment->patient->name }},

Your appointment at **Klinik Pergigian Dr. Zuraida** is confirmed.

<x-mail::panel>
**Date:** {{ $appointment->appointment_date->format('l, j F Y') }}<br>
**Time:** {{ $appointment->time_label }}<br>
**Dentist:** {{ $appointment->doctor?->name ?? 'Assigned when you arrive' }}
</x-mail::panel>

Please arrive about 10 minutes early. If you can't make it, let the clinic know so the slot can go to someone else.

@if (config('clinic.whatsapp'))
<x-mail::button :url="'https://wa.me/' . config('clinic.whatsapp')">
Contact the clinic on WhatsApp
</x-mail::button>
@endif

If you have severe pain or heavy bleeding before your appointment, don't wait. Seek emergency care straight away.

Thank you,<br>
KPZ Dentalcare
</x-mail::message>
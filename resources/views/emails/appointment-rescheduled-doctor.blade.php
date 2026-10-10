<x-mail::message>
# Appointment rescheduled

Hi {{ $doctor->name }},

@if ($handedOver)
An appointment of yours has been moved by the front desk and is now with **{{ $appointment->doctor?->name ?? 'another dentist' }}**. It is no longer on your schedule.
@else
The front desk has moved one of your appointments to a new time.
@endif

<x-mail::panel>
**Patient:** {{ $appointment->patient->name }}<br>
**Was:** {{ $previous }}<br>
**Now:** {{ $appointment->appointment_date->format('l, j F Y') }}, {{ $appointment->time_label }}
</x-mail::panel>

<x-mail::button :url="route('doctor.dashboard')">
Open your dashboard
</x-mail::button>

You're getting this because new-appointment notifications are on in your settings.

KPZ Dentalcare
</x-mail::message>
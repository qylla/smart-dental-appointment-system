<x-mail::message>
# New appointment

Hi {{ $appointment->doctor->name }},

A new appointment has been assigned to you.

<x-mail::panel>
**Patient:** {{ $appointment->patient->name }}<br>
**Date:** {{ $appointment->appointment_date->format('l, j F Y') }}<br>
**Time:** {{ $appointment->time_label }}<br>
**Urgency:** {{ ucfirst($appointment->urgency) }}<br>
**Booked via:** {{ ['online' => 'Online', 'walk_in' => 'Walk-in', 'phone' => 'Phone call'][$appointment->channel ?? 'online'] ?? 'Online' }}
</x-mail::panel>

<x-mail::button :url="route('doctor.dashboard')">
Open your dashboard
</x-mail::button>

You're getting this because new-appointment notifications are on in your settings.

KPZ Dentalcare
</x-mail::message>
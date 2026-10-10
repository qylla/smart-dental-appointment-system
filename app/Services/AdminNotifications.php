<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorLeave;
use Illuminate\Support\Str;

/**
 * Notifications for the admin bell.
 *
 * Nothing is stored. Each item is worked out from the current state of the
 * data every time the dashboard loads, so an item disappears by itself once
 * the problem is dealt with (e.g. confirming every pending booking clears
 * the "pending approval" item). No "mark as read", nothing to go stale.
 */
class AdminNotifications
{
    /** Active statuses, i.e. a booking that still needs a dentist and a slot. */
    private const ACTIVE = ['pending', 'confirmed'];

    /**
     * Most important first.
     *
     * @return array<int, array{icon:string, tone:string, title:string, text:string, url:string}>
     */
    public function items(): array
    {
        return array_values(array_filter([
            ...$this->leaveConflicts(),
            $this->urgentWaiting(),
            $this->pendingApproval(),
            $this->newOnlineBookings(),
        ]));
    }

    /**
     * Upcoming bookings assigned to a dentist who has marked themselves
     * away for that date and session. These need reassigning.
     */
    private function leaveConflicts(): array
    {
        $leaves = DoctorLeave::whereDate('date', '>=', today())
            ->get(['doctor_id', 'date', 'session']);

        if ($leaves->isEmpty()) {
            return [];
        }

        // "doctorId|Y-m-d" => that dentist's leave records for that date
        $byKey = $leaves->groupBy(fn ($l) => $l->doctor_id . '|' . $l->date->toDateString());

        return Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', '>=', today())
            ->whereIn('status', self::ACTIVE)
            ->whereIn('doctor_id', $leaves->pluck('doctor_id')->unique())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->filter(function (Appointment $a) use ($byKey) {
                $dayLeaves = $byKey[$a->doctor_id . '|' . $a->appointment_date->toDateString()] ?? collect();
                $session   = SlotService::sessionOf(substr((string) $a->appointment_time, 0, 5));

                return $dayLeaves->contains(fn ($l) => $l->session === null || $l->session === $session);
            })
            ->take(5)
            ->map(fn (Appointment $a) => [
                'icon'  => 'bx-user-x',
                'tone'  => 'crit',
                'title' => ($a->doctor?->name ?? 'A dentist') . ' is away',
                'text'  => $a->patient->name . ' on ' . $a->appointment_date->format('D, j M')
                    . ' at ' . $a->time_label . ' needs another dentist.',
                'url'   => route('admin.dashboard', ['day' => 'all']) . '#appointments',
            ])
            ->values()
            ->all();
    }

    /** Urgent or critical bookings nobody has confirmed yet. */
    private function urgentWaiting(): ?array
    {
        $count = Appointment::where('status', 'pending')
            ->whereIn('urgency', ['urgent', 'critical'])
            ->whereDate('appointment_date', '>=', today())
            ->count();

        if ($count === 0) {
            return null;
        }

        return [
            'icon'  => 'bx-error-circle',
            'tone'  => 'warn',
            'title' => $count . ' urgent ' . Str::plural('booking', $count) . ' waiting',
            'text'  => 'Flagged urgent or critical by the symptom checker, not confirmed yet.',
            'url'   => route('admin.dashboard', ['day' => 'all', 'filter' => 'pending']) . '#appointments',
        ];
    }

    /** Upcoming bookings still waiting for the clinic to confirm. */
    private function pendingApproval(): ?array
    {
        $count = Appointment::where('status', 'pending')
            ->whereDate('appointment_date', '>=', today())
            ->count();

        if ($count === 0) {
            return null;
        }

        return [
            'icon'  => 'bx-time-five',
            'tone'  => 'pend',
            'title' => $count . ' ' . Str::plural('booking', $count) . ' pending approval',
            'text'  => 'Confirm them so patients get their confirmation email.',
            'url'   => route('admin.dashboard', ['day' => 'all', 'filter' => 'pending']) . '#appointments',
        ];
    }

    /** Bookings patients made online in the last 24 hours. */
    private function newOnlineBookings(): ?array
    {
        $count = Appointment::where('channel', 'online')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($count === 0) {
            return null;
        }

        return [
            'icon'  => 'bx-globe',
            'tone'  => 'info',
            'title' => $count . ' new online ' . Str::plural('booking', $count),
            'text'  => 'Made by patients in the last 24 hours.',
            'url'   => route('admin.dashboard', ['day' => 'upcoming', 'channel' => 'online']) . '#appointments',
        ];
    }
}
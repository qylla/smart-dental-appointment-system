<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\DoctorLeave;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for slot availability and doctor assignment.
 *
 * Used by the online booking flow, the admin walk-in / phone form, admin
 * rescheduling and the landing page, so every channel shares one schedule
 * and one set of rules.
 */
class SlotService
{
    /**
     * How many days ahead appointments can be booked.
     */
    public const DAYS_AHEAD = 14;

    /**
     * Slots before this time belong to the day session, the rest to the
     * night session. Must match the split used on the booking pages.
     */
    public const SESSION_SPLIT = '18:00';

    /**
     * Build a date => [slots] map for the next fortnight.
     *
     * A slot can be booked only if at least one dentist is free for it:
     * not already booked at that time, and not on leave for that session.
     * Slots that have passed or that the admin closed are never bookable,
     * and a day the admin closed completely is left out.
     */
    public function availability(): array
    {
        $start     = today();
        $end       = today()->addDays(self::DAYS_AHEAD);
        $openDays  = ClinicSetting::get('open_days');
        $slots     = ClinicSetting::get('slots');
        $doctorIds = User::doctors()->pluck('id')->all();
        $now       = now();

        $closures      = collect(ClinicSetting::get('closures') ?? []);
        $closedDates   = $closures->whereNull('time')->pluck('date')->all();
        $closedSlotMap = $closures->whereNotNull('time')
            ->groupBy('date')
            ->map(fn ($group) => $group->pluck('time')->all());

        // date => time => [ids of dentists already booked at that time]
        $busy = Appointment::whereBetween('appointment_date', [$start, $end])
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('doctor_id')
            ->get(['appointment_date', 'appointment_time', 'doctor_id'])
            ->groupBy(fn ($a) => $a->appointment_date->toDateString())
            ->map(fn ($group) => $group
                ->groupBy(fn ($a) => substr((string) $a->appointment_time, 0, 5))
                ->map(fn ($slot) => $slot->pluck('doctor_id')->all()));

        // date => leaves that day
        $leaves = DoctorLeave::whereBetween('date', [$start, $end])
            ->get(['doctor_id', 'date', 'session'])
            ->groupBy(fn ($l) => $l->date->toDateString());

        $availability = [];

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $key = $day->toDateString();

            // Skip weekdays the clinic never opens, and dates the admin closed.
            if (! in_array($day->dayOfWeek, $openDays, true) || in_array($key, $closedDates, true)) {
                continue;
            }

            $dayBusy   = $busy[$key] ?? collect();
            $dayClosed = $closedSlotMap[$key] ?? [];
            $dayLeaves = $leaves[$key] ?? collect();

            $daySlots = collect($slots)->map(function ($slot) use ($day, $dayBusy, $dayClosed, $dayLeaves, $doctorIds, $now) {
                // Today's slots that have already started can't be booked.
                $isPast = $day->isToday()
                    && $day->copy()->setTimeFromTimeString($slot)->lte($now);

                $isClosed = in_array($slot, $dayClosed, true);

                // Dentists who could take this slot: not busy, not on leave.
                $free = array_diff(
                    $doctorIds,
                    $dayBusy[$slot] ?? [],
                    $this->awayDoctorIds($dayLeaves, $slot)
                );

                $isFull = count($free) === 0;

                return [
                    'time'   => $slot,
                    'label'  => date('g:i A', strtotime($slot)),
                    'booked' => $isFull || $isPast || $isClosed,
                    'past'   => $isPast,
                    'closed' => $isClosed,
                ];
            })->all();

            $availability[] = [
                'date'  => $key,
                'dow'   => $day->format('D'),
                'dnum'  => $day->format('j'),
                'label' => $day->format('D, j M'),
                'slots' => $daySlots,
                // True when nothing on this day can be booked.
                'full'  => collect($daySlots)->every(fn ($s) => $s['booked']),
            ];
        }

        return $availability;
    }

    /**
     * The earliest slot anyone could book right now, or null if the next
     * fortnight is full. Shown on the landing page hero chip.
     */
    public function nextAvailable(): ?array
    {
        foreach ($this->availability() as $day) {
            foreach ($day['slots'] as $slot) {
                if (! $slot['booked']) {
                    return [
                        'date'  => Carbon::parse($day['date'] . ' ' . $slot['time']),
                        'label' => $slot['label'],
                    ];
                }
            }
        }

        return null;
    }

    /**
     * 'day' or 'night', for a slot time like "08:30" or "20:00".
     */
    public static function sessionOf(string $time): string
    {
        return substr($time, 0, 5) < self::SESSION_SPLIT ? 'day' : 'night';
    }

    /**
     * Ids of dentists on leave at this slot, from one day's leave records.
     * A leave with no session covers the whole day.
     */
    private function awayDoctorIds(Collection $dayLeaves, string $slot): array
    {
        $session = self::sessionOf($slot);

        return $dayLeaves
            ->filter(fn ($l) => $l->session === null || $l->session === $session)
            ->pluck('doctor_id')
            ->unique()
            ->all();
    }

    /**
     * Has the admin closed this whole date, or this one slot on it?
     */
    public function isClosed(string $date, string $time): bool
    {
        $date = Carbon::parse($date)->toDateString();
        $time = substr($time, 0, 5);

        return collect(ClinicSetting::get('closures') ?? [])->contains(
            fn ($c) => $c['date'] === $date && ($c['time'] === null || $c['time'] === $time)
        );
    }

    /**
     * Book a slot and assign the least-busy free doctor.
     *
     * $attributes must include user_id, appointment_date, appointment_time.
     * Optional: symptom_check_id, status, channel, urgency, patient_note, admin_note.
     *
     * @throws \RuntimeException 'no_doctors', 'slot_past', 'slot_closed' or 'slot_taken'
     */
    public function book(array $attributes): Appointment
    {
        $doctors = User::doctors()->pluck('id');

        if ($doctors->isEmpty()) {
            throw new \RuntimeException('no_doctors');
        }

        $date = $attributes['appointment_date'];
        $time = $attributes['appointment_time'];

        // Re-check on save: a page left open can still show a slot
        // that has since passed or been closed.
        if (Carbon::parse("{$date} {$time}")->lte(now())) {
            throw new \RuntimeException('slot_past');
        }

        if ($this->isClosed($date, $time)) {
            throw new \RuntimeException('slot_closed');
        }

        // Leave out dentists who are away for this session.
        $away = $this->awayDoctorIds(
            DoctorLeave::whereDate('date', $date)->get(['doctor_id', 'date', 'session']),
            $time
        );
        $doctors = $doctors->reject(fn ($id) => in_array($id, $away, true))->values();

        if ($doctors->isEmpty()) {
            throw new \RuntimeException('slot_taken');
        }

        try {
            return DB::transaction(function () use ($attributes, $doctors, $date, $time) {
                // Lock every booking for this exact slot so two people booking
                // at the same moment are assigned different doctors.
                $busyDoctorIds = Appointment::where('appointment_date', $date)
                    ->where('appointment_time', $time)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->lockForUpdate()
                    ->pluck('doctor_id')
                    ->filter()
                    ->all();

                $freeDoctors = $doctors->reject(fn ($id) => in_array($id, $busyDoctorIds, true));

                if ($freeDoctors->isEmpty()) {
                    throw new \RuntimeException('slot_taken');
                }

                return Appointment::create($attributes + [
                    'doctor_id' => $this->leastBusyDoctor($freeDoctors, $date),
                    'status'    => 'pending',
                    'urgency'   => 'routine',
                ]);
            });
        } catch (QueryException $e) {
            // 1062 = MySQL duplicate entry, i.e. the unique_active_doctor_slot
            // index caught a clash. Any other database error is a real bug.
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('slot_taken');
            }

            throw $e;
        }
    }

    /**
     * Move an active appointment to another slot (admin reschedule, when a
     * patient phones in to change their date or time).
     *
     * Same checks as book(). The patient keeps their dentist if that dentist
     * is free at the new time; otherwise the least-busy free dentist takes it.
     *
     * @throws \RuntimeException 'slot_past', 'slot_closed' or 'slot_taken'
     */
    public function move(Appointment $appointment, string $date, string $time): Appointment
    {
        if (Carbon::parse("{$date} {$time}")->lte(now())) {
            throw new \RuntimeException('slot_past');
        }

        if ($this->isClosed($date, $time)) {
            throw new \RuntimeException('slot_closed');
        }

        $away = $this->awayDoctorIds(
            DoctorLeave::whereDate('date', $date)->get(['doctor_id', 'date', 'session']),
            $time
        );
        $doctors = User::doctors()->pluck('id')
            ->reject(fn ($id) => in_array($id, $away, true))
            ->values();

        if ($doctors->isEmpty()) {
            throw new \RuntimeException('slot_taken');
        }

        try {
            return DB::transaction(function () use ($appointment, $doctors, $date, $time) {
                // Same lock as book(), ignoring this appointment itself.
                $busyDoctorIds = Appointment::whereDate('appointment_date', $date)
                    ->where('appointment_time', $time)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('id', '!=', $appointment->id)
                    ->lockForUpdate()
                    ->pluck('doctor_id')
                    ->filter()
                    ->all();

                $freeDoctors = $doctors->reject(fn ($id) => in_array($id, $busyDoctorIds, true))->values();

                if ($freeDoctors->isEmpty()) {
                    throw new \RuntimeException('slot_taken');
                }

                // Keep the same dentist when possible, for continuity of care.
                $doctorId = $freeDoctors->contains($appointment->doctor_id)
                    ? $appointment->doctor_id
                    : $this->leastBusyDoctor($freeDoctors, $date);

                $appointment->update([
                    'appointment_date' => $date,
                    'appointment_time' => $time,
                    'doctor_id'        => $doctorId,
                ]);

                return $appointment;
            });
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new \RuntimeException('slot_taken');
            }

            throw $e;
        }
    }

    /**
     * Load balance: the free dentist with the fewest active appointments
     * that day. Shared by book() and move().
     */
    private function leastBusyDoctor(Collection $freeDoctors, string $date): int
    {
        $loads = Appointment::whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereIn('doctor_id', $freeDoctors)
            ->selectRaw('doctor_id, count(*) as total')
            ->groupBy('doctor_id')
            ->pluck('total', 'doctor_id');

        return $freeDoctors
            ->sortBy(fn ($id) => $loads[$id] ?? 0)
            ->first();
    }
}
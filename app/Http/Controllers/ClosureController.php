<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Lets the admin close a specific date (public holiday, clinic closed)
 * or a single slot on a date (meeting, equipment down).
 *
 * Stored as a list under the 'closures' key in clinic_settings, so no new
 * table is needed. A closure with time = null closes the whole day.
 *
 * Walk-in patients should NOT be handled here: closing a slot removes it
 * for every dentist, while a walk-in only takes one. Walk-ins go through
 * the walk-in booking form instead.
 */
class ClosureController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Own error bag, so these errors don't show up on the profile form.
        $data = $request->validateWithBag('closure', [
            'date'   => ['required', 'date', 'after_or_equal:today'],
            'time'   => ['nullable', Rule::in(ClinicSetting::ALL_SLOTS)],
            'reason' => ['nullable', 'string', 'max:100'],
        ]);

        $date = Carbon::parse($data['date'])->toDateString();
        $time = $data['time'] ?? null;

        $closures = $this->current();

        $alreadyThere = $closures->contains(
            fn ($c) => $c['date'] === $date && $c['time'] === $time
        );

        if (! $alreadyThere) {
            $closures->push([
                'id'     => (string) Str::ulid(),
                'date'   => $date,
                'time'   => $time,
                'reason' => $data['reason'] ?? null,
            ]);
        }

        ClinicSetting::put('closures', $closures
            ->sortBy(fn ($c) => $c['date'] . ' ' . ($c['time'] ?? '00:00'))
            ->values()
            ->all());

        // Closing doesn't cancel existing bookings. Tell the admin so they
        // can contact those patients themselves.
        $affected = Appointment::whereDate('appointment_date', $date)
            ->when($time, fn ($q) => $q->where('appointment_time', $time))
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $message = 'Closure saved.';

        if ($affected > 0) {
            $message .= " Note: {$affected} existing appointment(s) fall in this time. "
                . 'They were not cancelled, please contact those patients.';
        }

        return back()->with('status', $message)->with('tab', 'schedule');
    }

    public function destroy(string $id): RedirectResponse
    {
        ClinicSetting::put('closures', $this->current()
            ->reject(fn ($c) => $c['id'] === $id)
            ->values()
            ->all());

        return back()->with('status', 'Closure removed.')->with('tab', 'schedule');
    }

    /**
     * Current closures, with past dates dropped so the list stays short.
     */
    private function current(): Collection
    {
        $today = today()->toDateString();

        return collect(ClinicSetting::get('closures') ?? [])
            ->filter(fn ($c) => $c['date'] >= $today)
            ->values();
    }
}
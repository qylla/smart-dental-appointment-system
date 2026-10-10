<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorLeave;
use App\Services\SlotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * "My Availability": a dentist marks the dates or sessions they are away.
 *
 * Nothing else needs to change for this to take effect. SlotService reads
 * doctor_leaves, so online booking, walk-in booking and the landing page
 * all stop offering this dentist at those times straight away.
 */
class DoctorAvailabilityController extends Controller
{
    public function index(): View
    {
        $leaves = DoctorLeave::where('doctor_id', Auth::id())
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('session')
            ->get();

        // How many of this dentist's active bookings fall inside each leave,
        // so they can see what still needs reassigning.
        $leaves->each(function (DoctorLeave $leave) {
            $leave->affected = $this->affectedAppointments($leave->date, $leave->session)->count();
        });

        return view('doctor.availability', ['leaves' => $leaves]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date'    => ['required', 'date', 'after_or_equal:today'],
            'session' => ['nullable', 'in:day,night'],
            'reason'  => ['nullable', 'string', 'max:100'],
        ]);

        $date    = Carbon::parse($data['date'])->toDateString();
        $session = $data['session'] ?? null;

        $existing = DoctorLeave::where('doctor_id', Auth::id())
            ->whereDate('date', $date)
            ->get();

        // Already covered: a whole-day leave, or the same session twice.
        $covered = $existing->contains(fn ($l) => $l->session === null || $l->session === $session);

        if (! $covered) {
            // A new whole-day leave replaces any half-day ones on that date.
            if ($session === null) {
                $existing->each->delete();
            }

            DoctorLeave::create([
                'doctor_id' => Auth::id(),
                'date'      => $date,
                'session'   => $session,
                'reason'    => $data['reason'] ?? null,
            ]);
        }

        // Leave doesn't cancel anything. Tell the dentist if patients are
        // already booked with them in that time, so the front desk can move them.
        $affected = $this->affectedAppointments(Carbon::parse($date), $session)->count();

        $message = $covered ? 'You were already marked away for that time.' : 'Saved. Patients can no longer book you for that time.';

        if ($affected > 0) {
            $message .= " You still have {$affected} appointment(s) in that time. "
                . 'Please ask the front desk to reassign them to another dentist.';
        }

        return back()->with('status', $message);
    }

    public function destroy(DoctorLeave $leave): RedirectResponse
    {
        // A dentist can only remove their own leave.
        if ($leave->doctor_id !== Auth::id()) {
            abort(403);
        }

        $leave->delete();

        return back()->with('status', 'Removed. You are available for booking at that time again.');
    }

    /**
     * This dentist's active appointments inside a date (and session).
     */
    private function affectedAppointments(Carbon $date, ?string $session)
    {
        return Appointment::where('doctor_id', Auth::id())
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['id', 'appointment_time'])
            ->filter(fn ($a) => $session === null
                || SlotService::sessionOf(substr((string) $a->appointment_time, 0, 5)) === $session);
    }
}
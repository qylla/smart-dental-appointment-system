<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\SymptomCheck;
use App\Models\User;
use App\Services\AdminNotifications;
use App\Services\AppointmentMailer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * The three ways an appointment can reach the clinic.
     */
    public const CHANNELS = [
        'online'  => 'Online',
        'walk_in' => 'Walk-in',
        'phone'   => 'Phone',
    ];

    public function dashboard(Request $request, AdminNotifications $notifications): View
    {
        $weekStart = today()->startOfWeek();

        return view('admin.dashboard', [
            'notifications' => $notifications->items(),
            'stats'        => $this->stats($weekStart),
            'charts'       => $this->charts(),
            'appointments' => $this->appointments($request),
            'doctors'      => User::doctors()->orderBy('name')->get(),
            'filter'       => $request->query('filter', 'all'),
            'day'          => $request->query('day', 'today'),
            'channel'      => $request->query('channel', 'all'),
            'channels'     => self::CHANNELS,
        ]);
    }

    /**
     * List all patients, with a quick view of their booking activity.
     *
     * Kept as a simple per-patient lookup (not a single big join) since a
     * clinic's patient list is small enough that this stays fast, and it
     * keeps the query easy to read and defend.
     */
    public function patients(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $patients = User::where('role', 'patient')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $patients->each(function (User $patient) {
            $patient->total_appointments = Appointment::where('user_id', $patient->id)->count();
            $patient->last_appointment   = Appointment::where('user_id', $patient->id)
                ->orderByDesc('appointment_date')
                ->orderByDesc('appointment_time')
                ->first();
            $patient->last_symptom_check = SymptomCheck::where('user_id', $patient->id)
                ->latest()
                ->first();
        });

        return view('admin.patients', [
            'patients' => $patients,
            'search'   => $search,
        ]);
    }



    /**
     * Update the status of a booking. This is the clinic approving,
     * rejecting or closing off an appointment.
     */
    public function updateStatus(Request $request, Appointment $appointment, AppointmentMailer $mailer): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        // Only active appointments can change, and only to a valid next status.
        // Completed, cancelled and no-show are final, so they never appear here.
        $allowed = [
            'pending'   => ['confirmed', 'completed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled', 'no_show'],
        ];

        if (! in_array($data['status'], $allowed[$appointment->status] ?? [], true)) {
            return back()->withErrors([
                'reassign' => 'This appointment is already ' . str_replace('_', ' ', $appointment->status)
                    . ' and can no longer be changed.',
            ]);
        }

        $wasConfirmed = $appointment->status === 'confirmed';

        try {
            $appointment->update(['status' => $data['status']]);
        } catch (QueryException $e) {
            // 1062 = the unique_active_doctor_slot index. Happens when a
            // cancelled appointment is brought back but its dentist has since
            // been booked for that same slot by someone else.
            if (($e->errorInfo[1] ?? null) === 1062) {
                return back()->withErrors([
                    'reassign' => 'That dentist already has another booking at this time. '
                        . 'Reassign this appointment to a different dentist first, then confirm it.',
                ]);
            }

            throw $e;
        }

        // Email the patient only when the appointment actually becomes
        // confirmed, not every time the button is pressed.
        if ($data['status'] === 'confirmed' && ! $wasConfirmed) {
            $mailer->confirmed($appointment);
        }

        $labels = [
            'confirmed' => 'confirmed',
            'completed' => 'marked as completed',
            'cancelled' => 'cancelled',
            'no_show'   => 'marked as a no-show',
            'pending'   => 'moved back to pending',
        ];

        return back()->with(
            'status',
            $appointment->patient->name . "'s appointment has been " . $labels[$data['status']] . '.'
        );
    }

    /**
     * Move an appointment to a different dentist.
     *
     * Blocked if the target dentist already has that exact slot, otherwise
     * the same patient could end up double-booked against one doctor.
     */
    public function reassignDoctor(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'doctor_id' => ['required', 'exists:users,id'],
        ]);

        // Only active bookings count. A cancelled or completed appointment
        // in that slot doesn't stop the dentist taking this one.
        $clash = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('appointment_date', $appointment->appointment_date)
            ->where('appointment_time', $appointment->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($clash) {
            return back()->withErrors([
                'reassign' => 'That dentist already has a booking at this time.',
            ]);
        }

        $appointment->update(['doctor_id' => $data['doctor_id']]);

        return back()->with(
            'status',
            $appointment->patient->name . ' reassigned to ' . $appointment->doctor->name . '.'
        );
    }

    /* ---------------- Stat cards ---------------- */

    private function stats(Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->endOfWeek();

        $thisWeek = Appointment::whereBetween('appointment_date', [$weekStart, $weekEnd])->count();

        $lastWeek = Appointment::whereBetween('appointment_date', [
            $weekStart->copy()->subWeek(),
            $weekEnd->copy()->subWeek(),
        ])->count();

        // Percentage change against last week, guarding against divide-by-zero.
        $change = $lastWeek > 0
            ? round((($thisWeek - $lastWeek) / $lastWeek) * 100)
            : ($thisWeek > 0 ? 100 : 0);

        return [
            'week'       => $thisWeek,
            'change'     => $change,
            'pending'    => Appointment::pending()->count(),
            'critical'   => Appointment::where('urgency', 'critical')
                ->whereIn('status', ['pending', 'confirmed'])->count(),
            'no_show'    => Appointment::where('status', 'no_show')
                ->whereBetween('appointment_date', [$weekStart, $weekEnd])->count(),
        ];
    }

    /* ---------------- Chart data ---------------- */

    private function charts(): array
    {
        return [
            'bookings' => $this->bookingsSeries(),
            'symptoms' => $this->symptomSeries(),
            'urgency'  => $this->urgencySeries(),
            'peak'     => $this->peakSeries(),
            'age'      => $this->ageSeries(),
            'channel'  => $this->channelSeries(),
        ];
    }

    /** Appointments created per day this week, and per week this month. */
    private function bookingsSeries(): array
    {
        $weekLabels = [];
        $weekData   = [];

        for ($d = today()->startOfWeek(); $d->lte(today()->endOfWeek()); $d->addDay()) {
            $weekLabels[] = $d->format('D');
            $weekData[]   = Appointment::whereDate('appointment_date', $d)->count();
        }

        $monthLabels = [];
        $monthData   = [];

        for ($w = 3; $w >= 0; $w--) {
            $start = today()->startOfWeek()->subWeeks($w);
            $end   = $start->copy()->endOfWeek();
            $monthLabels[] = 'W' . (4 - $w);
            $monthData[]   = Appointment::whereBetween('appointment_date', [$start, $end])->count();
        }

        return [
            'week'  => ['labels' => $weekLabels,  'data' => $weekData],
            'month' => ['labels' => $monthLabels, 'data' => $monthData],
        ];
    }

    /**
     * Count how often each symptom appears. Symptoms are stored as a JSON
     * array, so they are counted in PHP rather than SQL.
     */
    private function symptomSeries(): array
    {
        $counts = [];

        SymptomCheck::where('created_at', '>=', today()->subDays(30))
            ->pluck('symptoms')
            ->each(function ($symptoms) use (&$counts) {
                foreach ((array) $symptoms as $s) {
                    $counts[$s] = ($counts[$s] ?? 0) + 1;
                }
            });

        arsort($counts);
        $counts = array_slice($counts, 0, 5, true);

        return [
            'labels' => array_keys($counts) ?: ['No data yet'],
            'data'   => array_values($counts) ?: [0],
        ];
    }

    private function urgencySeries(): array
    {
        $rows = SymptomCheck::select('severity', DB::raw('count(*) as total'))
            ->groupBy('severity')
            ->pluck('total', 'severity');

        return [
            'routine'  => (int) ($rows['routine']  ?? 0),
            'urgent'   => (int) ($rows['urgent']   ?? 0),
            'critical' => (int) ($rows['critical'] ?? 0),
        ];
    }

    /**
     * Which slots patients actually pick.
     *
     * Reads the slot list from clinic settings, so the chart follows
     * whatever schedule the admin has configured.
     */
    private function peakSeries(): array
    {
        $rows = Appointment::select('appointment_time', DB::raw('count(*) as total'))
            ->groupBy('appointment_time')
            ->pluck('total', 'appointment_time');

        $labels = [];
        $data   = [];

        foreach (ClinicSetting::get('slots') as $slot) {
            $labels[] = date('g:i', strtotime($slot));

            // Time values may come back as "09:00" or "09:00:00".
            $data[] = (int) ($rows[$slot] ?? $rows[$slot . ':00'] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Patient age distribution, worked out from date of birth so the
     * figures stay correct over time rather than going stale.
     *
     * Counted in PHP for the same reason as the patients list: a single
     * clinic's patient table is small, and this keeps the buckets easy
     * to read and adjust.
     */
    private function ageSeries(): array
    {
        $buckets = [
            'Under 18'     => 0,
            '18-30'        => 0,
            '31-45'        => 0,
            '46-60'        => 0,
            'Over 60'      => 0,
            'Not given'    => 0,
        ];

        User::where('role', 'patient')
            ->pluck('date_of_birth')
            ->each(function ($dob) use (&$buckets) {
                if (! $dob) {
                    $buckets['Not given']++;
                    return;
                }

                $age = Carbon::parse($dob)->age;

                $key = match (true) {
                    $age < 18  => 'Under 18',
                    $age <= 30 => '18-30',
                    $age <= 45 => '31-45',
                    $age <= 60 => '46-60',
                    default    => 'Over 60',
                };

                $buckets[$key]++;
            });

        return [
            'labels' => array_keys($buckets),
            'data'   => array_values($buckets),
        ];
    }

    /**
     * How appointments reach the clinic: online, walk-in or phone.
     *
     * Tracks whether online booking is actually being adopted, against the
     * survey baseline where most patients still walked in.
     */
    private function channelSeries(): array
    {
        $rows = Appointment::select('channel', DB::raw('count(*) as total'))
            ->groupBy('channel')
            ->pluck('total', 'channel');

        return [
            'labels' => array_values(self::CHANNELS),
            'data'   => array_map(
                fn($key) => (int) ($rows[$key] ?? 0),
                array_keys(self::CHANNELS)
            ),
        ];
    }

    /* ---------------- Appointment table ---------------- */

    private function appointments(Request $request)
    {
        $day     = $request->query('day', 'today');
        $filter  = $request->query('filter', 'all');
        $channel = $request->query('channel', 'all');

        return Appointment::with(['patient', 'symptomCheck', 'doctor'])
            ->when($day === 'today',    fn($q) => $q->whereDate('appointment_date', today()))
            ->when($day === 'upcoming', fn($q) => $q->whereDate('appointment_date', '>', today()))
            ->when($filter !== 'all',   fn($q) => $q->where('status', $filter))
            ->when(array_key_exists($channel, self::CHANNELS), fn($q) => $q->where('channel', $channel))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }
}

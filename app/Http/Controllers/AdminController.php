<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\SymptomCheck;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $weekStart = today()->startOfWeek();

        return view('admin.dashboard', [
            'stats'        => $this->stats($weekStart),
            'charts'       => $this->charts(),
            'appointments' => $this->appointments($request),
            'doctors'      => User::doctors()->orderBy('name')->get(),
            'filter'       => $request->query('filter', 'all'),
            'day'          => $request->query('day', 'today'),
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
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $appointment->update(['status' => $data['status']]);

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

        $clash = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('appointment_date', $appointment->appointment_date)
            ->where('appointment_time', $appointment->appointment_time)
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

    /** Which slots patients actually pick. */
    private function peakSeries(): array
    {
        $rows = Appointment::select('appointment_time', DB::raw('count(*) as total'))
            ->groupBy('appointment_time')
            ->pluck('total', 'appointment_time');

        $labels = [];
        $data   = [];

        foreach (Appointment::SLOTS as $slot) {
            $labels[] = date('g A', strtotime($slot));

            // Time values may come back as "09:00" or "09:00:00".
            $data[] = (int) ($rows[$slot] ?? $rows[$slot . ':00'] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /* ---------------- Appointment table ---------------- */

    private function appointments(Request $request)
    {
        $day    = $request->query('day', 'today');
        $filter = $request->query('filter', 'all');

        return Appointment::with(['patient', 'symptomCheck', 'doctor'])
            ->when($day === 'today',    fn($q) => $q->whereDate('appointment_date', today()))
            ->when($day === 'upcoming', fn($q) => $q->whereDate('appointment_date', '>', today()))
            ->when($filter !== 'all',   fn($q) => $q->where('status', $filter))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }
}
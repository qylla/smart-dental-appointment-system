<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function dashboard(): View
    {
        $doctorId = Auth::id();

        $today = Appointment::with(['patient', 'symptomCheck', 'consultation'])
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->orderBy('appointment_time')
            ->get();

        return view('doctor.dashboard', [
            'queue' => $today->map(fn ($a) => $this->present($a))->values(),
            'stats' => [
                'total'   => $today->count(),
                'done'    => $today->where('status', 'completed')->count(),
                'urgent'  => $today->whereIn('urgency', ['urgent', 'critical'])->count(),
                'remain'  => $today->where('status', '!=', 'completed')->count(),
            ],
        ]);
    }

    /**
     * Save what the dentist recorded for a visit.
     * Creates the consultation on first save, updates it after that.
     */
    public function storeConsultation(Request $request, Appointment $appointment): RedirectResponse
    {
        // A dentist may only write notes against their own appointments.
        if ($appointment->doctor_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'diagnosis'     => ['nullable', 'string', 'max:255'],
            'treatment'     => ['nullable', 'string', 'max:2000'],
            'followup'      => ['required', 'in:none,1w,2w,1m,6m'],
            'outcome'       => ['nullable', 'in:resolved,ongoing,referred'],
            'private_notes' => ['nullable', 'string', 'max:2000'],
            'complete'      => ['nullable', 'boolean'],
        ]);

        Consultation::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'doctor_id'     => Auth::id(),
                'diagnosis'     => $data['diagnosis'] ?? null,
                'treatment'     => $data['treatment'] ?? null,
                'followup'      => $data['followup'],
                'outcome'       => $data['outcome'] ?? null,
                'private_notes' => $data['private_notes'] ?? null,
            ]
        );

        if ($request->boolean('complete')) {
            $appointment->update(['status' => 'completed']);

            return back()->with('status',
                'Consultation saved and ' . $appointment->patient->name . ' marked as completed.');
        }

        return back()->with('status', 'Draft saved.');
    }

    /* ---------------- Helpers ---------------- */

    /**
     * Flatten an appointment into the shape the page's JavaScript expects,
     * including the patient's past visits.
     */
    private function present(Appointment $a): array
    {
        $check = $a->symptomCheck;
        $note  = $a->consultation;

        $past = Appointment::with('consultation')
            ->where('user_id', $a->user_id)
            ->where('id', '!=', $a->id)
            ->where('status', 'completed')
            ->orderByDesc('appointment_date')
            ->get();

        return [
            'id'          => $a->id,
            'name'        => $a->patient->name,
            'time'        => $a->time_label,
            'status'      => $a->status === 'completed' ? 'done' : 'waiting',
            'urgency'     => $a->urgency,
            'submitted'   => $check?->created_at->diffForHumans() ?? 'No symptom check submitted',
            'symptoms'    => $check?->symptoms ?? [],
            'description' => $check?->description ?: 'No description given.',
            'verdict'     => $check?->verdict ?? 'Booked without a symptom check',
            'advice'      => $check?->advice ?? 'The patient booked directly, so there is no pre-visit assessment.',
            'lastVisit'   => $past->first()?->appointment_date->format('j M Y') ?? 'First visit',
            'visits'      => $past->count(),
            'phone'       => $a->patient->phone ?? '—',

            // Existing notes, so a saved draft reappears when reselected.
            'notes' => [
                'diagnosis'     => $note?->diagnosis ?? '',
                'treatment'     => $note?->treatment ?? '',
                'followup'      => $note?->followup ?? 'none',
                'outcome'       => $note?->outcome ?? 'resolved',
                'private_notes' => $note?->private_notes ?? '',
            ],

            'history' => $past->map(fn ($p) => [
                'date'  => $p->appointment_date->format('j M Y'),
                'title' => $p->consultation?->diagnosis ?: 'Consultation',
                'note'  => $p->consultation?->treatment ?: 'No treatment notes recorded.',
                'tags'  => array_values(array_filter([
                    ucfirst($p->urgency),
                    $p->consultation?->outcome ? ucfirst($p->consultation->outcome) : null,
                ])),
            ])->values()->all(),
        ];
    }
}
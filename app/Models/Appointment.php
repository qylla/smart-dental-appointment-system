<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_id',
        'symptom_check_id',
        'appointment_date',
        'appointment_time',
        'status',
        'urgency',
        'patient_note',
        'admin_note',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'reminder_sent_at' => 'datetime',
        ];
    }

    /**
     * The slots the clinic offers each working day.
     * Kept here so both the booking page and the admin side agree
     * on what a valid slot looks like.
     */
    public const SLOTS = [
        '09:00', '10:00', '11:00',
        '14:00', '15:00', '16:00',
    ];

    /* ---------------- Relationships ---------------- */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function symptomCheck(): BelongsTo
    {
        return $this->belongsTo(SymptomCheck::class);
    }

    public function consultation(): HasOne
    {
        return $this->hasOne(Consultation::class);
    }

    /* ---------------- Scopes ---------------- */

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('appointment_date', '>=', today())
                     ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('urgency', ['urgent', 'critical']);
    }

    /* ---------------- Helpers ---------------- */

    /**
     * Which of the clinic's slots are still free on a given date.
     * This is what the booking page should call instead of hardcoding times.
     */
    public static function availableSlots(string $date, ?int $doctorId = null): array
    {
        $taken = static::whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->pluck('appointment_time')
            ->map(fn ($t) => substr((string) $t, 0, 5))
            ->all();

        return array_values(array_diff(static::SLOTS, $taken));
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTimeLabelAttribute(): string
    {
        return date('g:i A', strtotime((string) $this->appointment_time));
    }
}
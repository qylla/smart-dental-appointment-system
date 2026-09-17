<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'diagnosis',
        'treatment',
        'followup',
        'outcome',
        'private_notes',
    ];

    public const FOLLOWUP_LABELS = [
        'none' => 'No follow-up',
        '1w'   => 'In 1 week',
        '2w'   => 'In 2 weeks',
        '1m'   => 'In 1 month',
        '6m'   => 'Routine, 6 months',
    ];

    /* ---------------- Relationships ---------------- */

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /* ---------------- Helpers ---------------- */

    public function needsFollowup(): bool
    {
        return $this->followup !== 'none';
    }

    public function getFollowupLabelAttribute(): string
    {
        return self::FOLLOWUP_LABELS[$this->followup] ?? 'No follow-up';
    }
}
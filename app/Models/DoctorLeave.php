<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A stretch of time a dentist is away. See the doctor_leaves migration.
 */
class DoctorLeave extends Model
{
    protected $fillable = ['doctor_id', 'date', 'session', 'reason'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /** Human label for the session, used on the availability page. */
    public function getSessionLabelAttribute(): string
    {
        return match ($this->session) {
            'day'   => 'Day session (8:30 AM – 5 PM)',
            'night' => 'Night session (8 PM – 10 PM)',
            default => 'Whole day',
        };
    }
}
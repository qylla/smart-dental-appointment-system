<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SymptomCheck extends Model
{
    protected $fillable = [
        'user_id',
        'guest_token',
        'symptoms',
        'description',
        'severity',
        'verdict',
        'advice',
        'ai_response',
    ];

    protected function casts(): array
    {
        return [
            'symptoms'    => 'array',
            'ai_response' => 'array',
        ];
    }

    /* ---------------- Relationships ---------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class);
    }

    /* ---------------- Helpers ---------------- */

    public function isUrgent(): bool
    {
        return in_array($this->severity, ['urgent', 'critical'], true);
    }

    /** Was this submitted before the person had an account? */
    public function isGuestCheck(): bool
    {
        return is_null($this->user_id);
    }

    public function getSymptomListAttribute(): string
    {
        return implode(', ', $this->symptoms ?? []);
    }

    /* ---------------- Scopes ---------------- */

    public function scopeUrgent($query)
    {
        return $query->whereIn('severity', ['urgent', 'critical']);
    }
}
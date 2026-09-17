<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    /**
     * Defaults used when the admin has not saved anything yet.
     * These mirror the values the booking system assumed before settings existed.
     */
    public const DEFAULTS = [
        'open_days' => [1, 2, 3, 4, 5, 6], // Mon–Sat (0 = Sunday)
        'slots'     => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'],
    ];

    public const ALL_SLOTS = [
        '08:00', '09:00', '10:00', '11:00', '12:00',
        '14:00', '15:00', '16:00', '17:00', '18:00',
    ];

    public const DAY_NAMES = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday',
    ];

    public static function get(string $key)
    {
        $row = static::where('key', $key)->first();

        return $row ? $row->value : (self::DEFAULTS[$key] ?? null);
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
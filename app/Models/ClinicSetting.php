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
     * Every slot the clinic could offer, every 30 minutes.
     * Day session 8:30 AM - 5:00 PM (last booking 4:30 PM),
     * night session 8:00 PM - 10:00 PM (last booking 9:30 PM).
     */
    public const ALL_SLOTS = [
        // Day session
        '08:30', '09:00', '09:30', '10:00', '10:30', '11:00',
        '11:30', '12:00', '12:30', '13:00', '13:30', '14:00',
        '14:30', '15:00', '15:30', '16:00', '16:30',
        // Night session
        '20:00', '20:30', '21:00', '21:30',
    ];

    /**
     * Defaults used when the admin has not saved anything yet.
     */
    public const DEFAULTS = [
        'open_days' => [0, 1, 2, 3, 4, 5, 6], // every day (0 = Sunday)
        'slots'     => self::ALL_SLOTS,
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'phone',
    'date_of_birth',
    'specialisation',
    'notify_new_appointment',
    'notify_cancellation',
    'notify_daily_summary',
];

protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'date_of_birth' => 'date',
    'notify_new_appointment' => 'boolean',
    'notify_cancellation' => 'boolean',
    'notify_daily_summary' => 'boolean',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'date_of_birth'     => 'date',
        ];
    }

    /* ---------------- Role helpers ---------------- */

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /* ---------------- Relationships ---------------- */

    /** Appointments this user booked as a patient. */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderByDesc('appointment_date');
    }

    /** Appointments assigned to this user as a dentist. */
    public function doctorAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /** Symptom checks this user submitted. */
    public function symptomChecks(): HasMany
    {
        return $this->hasMany(SymptomCheck::class)->latest();
    }

    /** Consultations this user recorded as a dentist. */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    public function scopePatients($query)
    {
        return $query->where('role', 'patient');
    }

    /* ---------------- Date of Birth ---------------- */
        /**
     * Umur dikira daripada tarikh lahir, bukan disimpan — supaya ia
     * tidak menjadi basi. Null bagi akaun tanpa tarikh lahir.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    /**
     * Kumpulan umur untuk laporan admin.
     */
    public function getAgeGroupAttribute(): string
    {
        $age = $this->age;

        return match (true) {
            $age === null => 'Tidak dinyatakan',
            $age < 18     => 'Bawah 18',
            $age <= 30    => '18–30',
            $age <= 45    => '31–45',
            $age <= 60    => '46–60',
            default       => '60 ke atas',
        };
    }
}


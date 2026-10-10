<?php

namespace Tests\Feature\Api;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Automated checks for the public REST API (v1).
 *
 * Run with:  php artisan test --filter=ClinicApiTest
 */
class ClinicApiTest extends TestCase
{
    use RefreshDatabase;

    private function doctor(): User
    {
        return User::create([
            'name' => 'Dr. Test', 'email' => 'dr.test@example.com', 'phone' => '0110000000',
            'role' => 'doctor', 'password' => 'password',
        ]);
    }

    private function patient(): User
    {
        return User::create([
            'name' => 'Secret Patient Name', 'email' => 'patient@example.com', 'phone' => '0120000000',
            'role' => 'patient', 'password' => 'password',
        ]);
    }

    public function test_index_lists_the_endpoints(): void
    {
        $this->getJson('/api/v1')
            ->assertOk()
            ->assertJsonPath('version', 'v1')
            ->assertJsonCount(3, 'endpoints');
    }

    public function test_clinic_details_include_contact_and_prices(): void
    {
        $this->getJson('/api/v1/clinic')
            ->assertOk()
            ->assertJsonPath('data.phone', config('clinic.phone'))
            ->assertJsonPath('data.slot_minutes', 30)
            ->assertJsonPath('data.prices.0.currency', 'MYR')
            ->assertJsonStructure(['data' => ['name', 'open_days', 'sessions' => ['day', 'night'], 'prices']]);
    }

    public function test_slots_cover_the_booking_window(): void
    {
        $this->doctor();

        $this->getJson('/api/v1/slots')
            ->assertOk()
            ->assertJsonPath('meta.days_ahead', 14)
            ->assertJsonStructure([
                'data' => [['date', 'day', 'full', 'available_count', 'slots' => [['time', 'label', 'session', 'available', 'reason']]]],
                'meta' => ['days', 'timezone', 'generated_at'],
            ]);
    }

    public function test_a_slot_is_full_when_every_dentist_is_booked(): void
    {
        $doctor   = $this->doctor();
        $tomorrow = today()->addDay()->toDateString();

        Appointment::create([
            'user_id' => $this->patient()->id, 'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow, 'appointment_time' => '10:00',
            'status' => 'confirmed', 'urgency' => 'routine', 'channel' => 'online',
        ]);

        $slot = collect($this->getJson("/api/v1/slots?date={$tomorrow}")
            ->assertOk()
            ->json('data.0.slots'))->firstWhere('time', '10:00');

        $this->assertFalse($slot['available']);
        $this->assertSame('full', $slot['reason']);
    }

    public function test_available_filter_hides_unbookable_slots(): void
    {
        $this->doctor();
        $tomorrow = today()->addDay()->toDateString();

        $slots = $this->getJson("/api/v1/slots?date={$tomorrow}&available=1")->assertOk()->json('data.0.slots');

        $this->assertNotEmpty($slots);
        $this->assertTrue(collect($slots)->every(fn ($s) => $s['available'] === true));
    }

    public function test_the_api_never_exposes_patient_details(): void
    {
        $doctor = $this->doctor();

        Appointment::create([
            'user_id' => $this->patient()->id, 'doctor_id' => $doctor->id,
            'appointment_date' => today()->addDay()->toDateString(), 'appointment_time' => '10:00',
            'status' => 'confirmed', 'urgency' => 'routine', 'channel' => 'online',
        ]);

        foreach (['/api/v1/slots', '/api/v1/slots/next', '/api/v1/clinic'] as $url) {
            $body = $this->getJson($url)->assertOk()->getContent();

            $this->assertStringNotContainsString('Secret Patient Name', $body);
            $this->assertStringNotContainsString('patient@example.com', $body);
            $this->assertStringNotContainsString('Dr. Test', $body);
        }
    }

    public function test_bad_and_unknown_dates_return_json_errors(): void
    {
        $this->getJson('/api/v1/slots?date=01-10-2026')
            ->assertStatus(422)
            ->assertJsonValidationErrors('date');

        $this->getJson('/api/v1/slots?date=' . today()->addYear()->toDateString())
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    public function test_next_slot_returns_a_future_time(): void
    {
        $this->doctor();

        $next = $this->getJson('/api/v1/slots/next')->assertOk()->json('data');

        $this->assertNotNull($next);
        $this->assertTrue(now()->lt(\Illuminate\Support\Carbon::parse($next['starts_at'])));
    }

    public function test_the_api_is_read_only(): void
    {
        $this->postJson('/api/v1/slots', ['date' => today()->toDateString()])->assertStatus(405);
    }
}
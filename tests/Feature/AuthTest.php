<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Automated checks for login, registration, logout and role access.
 *
 * Run with:  php artisan test --filter=AuthTest
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'patient', string $email = 'aisyah@example.com'): User
    {
        return User::create([
            'name'     => 'Test User',
            'email'    => $email,
            'phone'    => '0123456789',
            'role'     => $role,
            'password' => Hash::make('password123'),
        ]);
    }

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'Siti Nurhaliza',
            'email'                 => 'siti@example.com',
            'phone'                 => '0112345678',
            'date_of_birth'         => '2000-05-12',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_patient_can_log_in_and_goes_to_booking(): void
    {
        $patient = $this->makeUser('patient');

        $this->post('/login', ['email' => $patient->email, 'password' => 'password123'])
            ->assertRedirect(route('booking'));

        $this->assertAuthenticatedAs($patient);
    }

    public function test_admin_log_in_goes_to_admin_dashboard(): void
    {
        $admin = $this->makeUser('admin', 'admin@example.com');

        $this->post('/login', ['email' => $admin->email, 'password' => 'password123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $patient = $this->makeUser('patient');

        $this->post('/login', ['email' => $patient->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_new_patient_can_register(): void
    {
        $this->post('/register', $this->registrationData())
            ->assertRedirect(route('login'));

        $user = User::where('email', 'siti@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('patient', $user->role);
        // The password is stored as a hash, never as plain text.
        $this->assertNotSame('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_duplicate_email_cannot_register(): void
    {
        $this->makeUser('patient', 'siti@example.com');

        $this->post('/register', $this->registrationData())
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('email', 'siti@example.com')->count());
    }

    public function test_passwords_must_match_to_register(): void
    {
        $this->post('/register', $this->registrationData(['password_confirmation' => 'different123']))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'siti@example.com']);
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/booking')->assertRedirect(route('login'));
    }

    public function test_patient_cannot_open_admin_pages(): void
    {
        $this->actingAs($this->makeUser('patient'))
            ->get('/admin')
            ->assertRedirect(route('booking'));
    }

    public function test_login_is_limited_to_five_tries_a_minute(): void
    {
        $patient = $this->makeUser('patient');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $patient->email, 'password' => 'wrong-password']);
        }

        $this->post('/login', ['email' => $patient->email, 'password' => 'wrong-password'])
            ->assertStatus(429);
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs($this->makeUser('patient'))
            ->post('/logout')
            ->assertRedirect();

        $this->assertGuest();
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\SymptomCheck;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the combined login / register page.
     */
    public function show(): View
    {
        return view('auth.auth');
    }

    /* ---------------- REGISTER ---------------- */

        public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique'        => 'That email is already registered. Try logging in instead.',
            'password.confirmed'  => 'The two passwords do not match.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'date_of_birth' => $data['date_of_birth'],
            'password' => Hash::make($data['password']),
            'role'     => 'patient', // staff accounts are created by the clinic, not self-registered
        ]);

        // Claim any guest symptom check before touching the session,
        // so the guest token is still readable.
        $this->claimGuestSymptomCheck($request, $user);

        // The new account is NOT signed in — they log in themselves on the
        // next screen. Guard against any stale session from an earlier visit,
        // otherwise the guest-only login route bounces them to the home page.
        Auth::logout();
        $request->session()->regenerate();

        return redirect()->route('login')
            ->with('status', 'Account created. You can log in now.');
    }

    /* ---------------- LOGIN ---------------- */

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Those details do not match our records.']);
        }

        $request->session()->regenerate();

        $this->claimGuestSymptomCheck($request, Auth::user());

        return redirect()->intended($this->homeFor(Auth::user()));
    }

    /* ---------------- LOGOUT ---------------- */

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'You have been logged out.');
    }

    /* ---------------- HELPERS ---------------- */

    /**
     * Where each role lands after signing in.
     */
    public static function homeFor(User $user): string
    {
        return match ($user->role) {
            'admin'  => route('admin.dashboard'),
            'doctor' => route('doctor.dashboard'),
            default  => route('booking'),
        };
    }

    /**
     * The symptom checker is public, so a visitor may have run a check
     * before creating an account. If they did, attach that check to them
     * now so it carries into their booking.
     */
    private function claimGuestSymptomCheck(Request $request, User $user): void
    {
        $token = $request->session()->pull('guest_token');

        if (! $token) {
            return;
        }

        SymptomCheck::whereNull('user_id')
            ->where('guest_token', $token)
            ->update(['user_id' => $user->id]);
    }
}

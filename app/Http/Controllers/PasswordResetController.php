<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * "Forgot password" flow, using Laravel's built-in password broker.
 *
 * The broker creates a one-time token (stored hashed in the
 * password_reset_tokens table), emails a link containing it, and checks
 * it again on the way back. Links expire after 60 minutes by default.
 */
class PasswordResetController extends Controller
{
    /** Step 1: ask for the email address. */
    public function request(): View
    {
        return view('auth.forgot-password');
    }

    /** Step 2: email a reset link. */
    public function email(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            $status = Password::sendResetLink($request->only('email'));

            // TEMP: find out why a reset email didn't arrive. Remove after testing.
            Log::info('Password reset status: ' . $status);
        } catch (\Throwable $e) {
            Log::warning('Password reset email not sent: ' . $e->getMessage());

            return back()->withInput()->withErrors([
                'email' => "We couldn't send the email right now. Please try again later, or contact the clinic.",
            ]);
        }

        // Same message whether or not the email has an account, so this
        // form can't be used to find out who is registered at the clinic.
        return back()->with(
            'status',
            'If that email has an account, a reset link is on its way. Check your inbox and spam folder.'
        );
    }

    /** Step 3: the link in the email lands here. */
    public function reset(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /** Step 4: save the new password. */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // The model's 'hashed' cast hashes this on save.
                $user->forceFill([
                    'password'       => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Password updated. You can log in with your new password now.');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => $status === Password::INVALID_TOKEN
                ? 'This reset link is invalid or has expired. Please request a new one.'
                : "We couldn't reset the password for that email.",
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Account settings page for a patient.
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('settings', [
            'user'         => $user,
            'appointments' => $user->appointments()->with('symptomCheck')->get(),
            'lastCheck'    => $user->symptomChecks()->first(),
        ]);
    }

    
    /**
     * Update name, email, phone, and date of birth.
     *
     * Date of birth can only be set once. After that it is locked, so a
     * patient's age record can't be changed casually. Corrections go
     * through the clinic.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
        ];

        // Only accept a date of birth if none is saved yet. validate() returns
        // only the fields it has rules for, so once this rule is left out,
        // any date_of_birth sent in the request is simply dropped.
        if ($user->date_of_birth === null) {
            $rules['date_of_birth'] = ['nullable', 'date', 'before:today'];
        }

        $data = $request->validate($rules, [
            'email.unique' => 'That email is already used by another account.',
        ]);

        $user->update($data);

        return back()->with('status', 'Your details have been updated.');
    }

    /**
     * Change password. The current password is required so that someone
     * on a shared or unattended device cannot silently take over the account.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.confirmed' => 'The two new passwords do not match.',
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors([
                'current_password' => 'That is not your current password.',
            ], 'password');
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Your password has been changed.');
    }

    /**
     * Let a patient close their own account.
     * Appointments cascade on delete, so their bookings go with them.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'delete_password' => ['required'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->delete_password, $user->password)) {
            return back()->withErrors([
                'delete_password' => 'Password incorrect. Your account was not deleted.',
            ], 'delete');
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('status', 'Your account has been deleted.');
    }
}
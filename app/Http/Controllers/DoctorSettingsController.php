<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class DoctorSettingsController extends Controller
{
    public function index()
    {
        $doctor = Auth::user();

        return view('doctor.settings', [
            'doctor' => $doctor,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $doctor = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($doctor->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'specialisation' => ['nullable', 'string', 'max:255'],
        ]);

        $doctor->update($validated);

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $doctor = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $doctor->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function updateNotifications(Request $request)
    {
        $doctor = Auth::user();

        // Checkboxes only send a value when checked, so absent = false
        $doctor->update([
            'notify_new_appointment' => $request->boolean('notify_new_appointment'),
            'notify_cancellation' => $request->boolean('notify_cancellation'),
            'notify_daily_summary' => $request->boolean('notify_daily_summary'),
        ]);

        return back()->with('status', 'notifications-updated');
    }
}
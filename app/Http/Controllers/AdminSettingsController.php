<?php

namespace App\Http\Controllers;

use App\Models\ClinicSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings', [
            'admin'    => Auth::user(),
            'staff'    => User::whereIn('role', ['admin', 'doctor'])
                             ->orderBy('role')->orderBy('name')->get(),
            'openDays' => ClinicSetting::get('open_days'),
            'slots'    => ClinicSetting::get('slots'),
            'allSlots' => ClinicSetting::ALL_SLOTS,
            'dayNames' => ClinicSetting::DAY_NAMES,
        ]);
    }

    /* ---------------- 1. Admin's own profile ---------------- */

    public function updateProfile(Request $request): RedirectResponse
    {
        $admin = Auth::user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $admin->update($data);

        return back()->with('status', 'Your profile has been updated.')->with('tab', 'profile');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()
                ->withErrors(['current_password' => 'That is not your current password.'], 'password')
                ->with('tab', 'profile');
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'Your password has been changed.')->with('tab', 'profile');
    }

    /* ---------------- 2. Clinic schedule ---------------- */

    public function updateSchedule(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'open_days'   => ['required', 'array', 'min:1'],
            'open_days.*' => ['integer', 'between:0,6'],
            'slots'       => ['required', 'array', 'min:1'],
            'slots.*'     => [Rule::in(ClinicSetting::ALL_SLOTS)],
        ], [
            'open_days.required' => 'Pick at least one day the clinic opens.',
            'slots.required'     => 'Pick at least one time slot.',
        ]);

        ClinicSetting::put('open_days', array_map('intval', $data['open_days']));
        ClinicSetting::put('slots', array_values($data['slots']));

        return back()->with('status', 'Clinic schedule updated.')->with('tab', 'schedule');
    }

    /* ---------------- 3. Staff management ---------------- */

    public function storeStaff(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'role'           => ['required', 'in:admin,doctor'],
            'specialisation' => ['nullable', 'string', 'max:255'],
            'password'       => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'] ?? null,
            'role'           => $data['role'],
            'specialisation' => $data['role'] === 'doctor' ? ($data['specialisation'] ?? null) : null,
            'password'       => Hash::make($data['password']),
        ]);

        return back()
            ->with('status', $data['name'] . ' has been added as a ' . $data['role'] . '.')
            ->with('tab', 'staff');
    }

    public function destroyStaff(Request $request, User $user): RedirectResponse
    {
        // Guard against removing your own account or a patient from here.
        if ($user->id === Auth::id()) {
            return back()
                ->withErrors(['staff' => 'You cannot remove your own account.'], 'staff')
                ->with('tab', 'staff');
        }

        if ($user->isPatient()) {
            abort(403);
        }

        // Never leave the clinic with no admins at all.
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()
                ->withErrors(['staff' => 'You cannot remove the last admin account.'], 'staff')
                ->with('tab', 'staff');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('status', $name . ' has been removed.')->with('tab', 'staff');
    }
}
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorSettingsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClosureController;
use App\Http\Controllers\WalkInController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DoctorAvailabilityController;
use App\Http\Controllers\RescheduleController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
| The symptom checker is deliberately open to everyone. A patient should be
| able to check how urgent their problem is before deciding to sign up.
*/

Route::get('/', [LandingController::class, '__invoke'])->name('home');
Route::view('/symptom-checker', 'symptom-checker')->name('symptom-checker');

Route::post('/symptom-checker/analyze', [BookingController::class, 'checkSymptomsGuest'])
    ->name('symptom-checker.analyze');

/*
|--------------------------------------------------------------------------
| Guest only
|--------------------------------------------------------------------------
*/

    Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
        ->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Patient
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/symptom-check', [BookingController::class, 'storeSymptomCheck'])->name('booking.symptom');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::delete('/settings', [SettingsController::class, 'destroy'])->name('settings.destroy');
});

/*
|--------------------------------------------------------------------------
| Clinic admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/patients', [AdminController::class, 'patients'])->name('admin.patients');
    Route::patch('/admin/appointment/{appointment}', [AdminController::class, 'updateStatus'])
        ->name('admin.appointment.status');
    Route::patch('/admin/appointment/{appointment}/reassign', [AdminController::class, 'reassignDoctor'])
        ->name('admin.appointment.reassign');
    Route::get('/admin/appointment/{appointment}/reschedule', [RescheduleController::class, 'edit'])
        ->name('admin.appointment.reschedule');
    Route::patch('/admin/appointment/{appointment}/reschedule', [RescheduleController::class, 'update'])
        ->name('admin.appointment.reschedule.update');
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::patch('/admin/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('admin.settings.profile');
    Route::put('/admin/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('admin.settings.password');
    Route::patch('/admin/settings/schedule', [AdminSettingsController::class, 'updateSchedule'])->name('admin.settings.schedule');
    Route::post('/admin/settings/staff', [AdminSettingsController::class, 'storeStaff'])->name('admin.settings.staff.store');
    Route::delete('/admin/settings/staff/{user}', [AdminSettingsController::class, 'destroyStaff'])->name('admin.settings.staff.destroy');
    Route::post('/admin/settings/closures', [ClosureController::class, 'store'])->name('admin.settings.closures.store');
    Route::delete('/admin/settings/closures/{id}', [ClosureController::class, 'destroy'])->name('admin.settings.closures.destroy');
        Route::get('/admin/walk-in', [WalkInController::class, 'create'])->name('admin.walkin');
    Route::post('/admin/walk-in', [WalkInController::class, 'store'])->name('admin.walkin.store');
    });

/*
|--------------------------------------------------------------------------
| Dentist
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::patch('/doctor/consultation/{appointment}', [DoctorController::class, 'storeConsultation'])
        ->name('doctor.consultation');
    Route::get('/doctor/settings', [DoctorSettingsController::class, 'index'])->name('doctor.settings');
    Route::patch('/doctor/settings/profile', [DoctorSettingsController::class, 'updateProfile'])->name('doctor.settings.profile');
    Route::put('/doctor/settings/password', [DoctorSettingsController::class, 'updatePassword'])->name('doctor.settings.password');
    Route::patch('/doctor/settings/notifications', [DoctorSettingsController::class, 'updateNotifications'])->name('doctor.settings.notifications');
    Route::get('/doctor/availability', [DoctorAvailabilityController::class, 'index'])->name('doctor.availability');
    Route::post('/doctor/availability', [DoctorAvailabilityController::class, 'store'])->name('doctor.availability.store');
    Route::delete('/doctor/availability/{leave}', [DoctorAvailabilityController::class, 'destroy'])->name('doctor.availability.destroy');
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ms'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');
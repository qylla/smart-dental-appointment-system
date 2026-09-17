<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorSettingsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
| The symptom checker is deliberately open to everyone. A patient should be
| able to check how urgent their problem is before deciding to sign up.
*/

Route::view('/', 'landing')->name('home');

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
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
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

    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::patch('/admin/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('admin.settings.profile');
    Route::put('/admin/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('admin.settings.password');
    Route::patch('/admin/settings/schedule', [AdminSettingsController::class, 'updateSchedule'])->name('admin.settings.schedule');
    Route::post('/admin/settings/staff', [AdminSettingsController::class, 'storeStaff'])->name('admin.settings.staff.store');
    Route::delete('/admin/settings/staff/{user}', [AdminSettingsController::class, 'destroyStaff'])->name('admin.settings.staff.destroy');
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
});

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ms'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');
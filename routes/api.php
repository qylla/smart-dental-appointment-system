<?php

use App\Http\Controllers\Api\V1\ClinicApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public REST API, version 1
|--------------------------------------------------------------------------
|
| Read-only. Every path here starts with /api (added by bootstrap/app.php),
| so the full URLs are /api/v1/... Errors come back as JSON too, because
| bootstrap/app.php renders JSON for anything under api/*.
|
| throttle:60,1 = at most 60 requests per minute from one IP address.
|
*/

Route::prefix('v1')->name('api.v1.')->middleware('throttle:60,1')->group(function () {
    Route::get('/', [ClinicApiController::class, 'index'])->name('index');
    Route::get('/clinic', [ClinicApiController::class, 'clinic'])->name('clinic');
    Route::get('/slots', [ClinicApiController::class, 'slots'])->name('slots');
    Route::get('/slots/next', [ClinicApiController::class, 'nextSlot'])->name('slots.next');
});
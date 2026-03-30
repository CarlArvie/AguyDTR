<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\TimeEntryController;



Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth.user')->group(function () {
    Route::get('/dashboard', [TimeEntryController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [TimeEntryController::class, 'data'])->name('dashboard.data');

    Route::get('/time-entry', [TimeEntryController::class, 'create'])->name('time-entries.create');
    Route::get('/time-entry/data', [TimeEntryController::class, 'entryData'])->name('time-entries.data');
    Route::get('/time-entry/{timeEntry}/data', [TimeEntryController::class, 'showData'])->name('time-entries.show-data');
    Route::get('/time-entry/{timeEntry}', [TimeEntryController::class, 'show'])
        ->whereNumber('timeEntry')
        ->name('time-entries.show');
    Route::post('/time-entry', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::delete('/time-entry/{timeEntry}', [TimeEntryController::class, 'destroy'])
        ->whereNumber('timeEntry')
        ->name('time-entries.destroy');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/about', function () {
        return view('about');
    })->name('about');
});

Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.verify');
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.check');

Route::get('/login/verify-otp', [OtpController::class, 'showLoginOtp'])
    ->name('otp.verify');


Route::post('/login/verify-otp', [OtpController::class, 'verifyLoginOtp'])
    ->name('otp.check');

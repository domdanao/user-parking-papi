<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TakeSlot;
use App\Http\Controllers\ParkingPaymentController;

Route::middleware('web')->group(function () {
    // Load auth routes first
    require base_path('routes/auth.php');

    // Load parking slot owner routes
    require base_path('routes/parking-slot-owner.php');

    Route::view('/', 'welcome');

    Route::view('dashboard', 'dashboard')
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');

    Route::view('/scan', 'scan');
    Route::view('/generate', 'generate');

    Route::get('/slot/{identifier}', TakeSlot::class)->name('take-slot');

    // Payment routes
    Route::get('/parking/success', [ParkingPaymentController::class, 'success'])->name('parking.success');
    Route::get('/parking/cancel', [ParkingPaymentController::class, 'cancel'])->name('parking.cancel');
});

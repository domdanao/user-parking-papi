<?php

use App\Livewire\Pages\ParkingSlotOwner\Auth\Login;
use App\Livewire\Pages\ParkingSlotOwner\Auth\Register;
use App\Livewire\ParkingSlotOwner\Auth\Logout;
use App\Livewire\ParkingSlotOwner\CreateSlot;
use App\Livewire\ParkingSlotOwner\Dashboard;
use App\Livewire\ParkingSlotOwner\SlotList;
use Illuminate\Support\Facades\Route;

Route::prefix('owner')->name('parking-slot-owner.')->group(function () {
    // Guest routes
    Route::middleware('guest:parking-slot-owner')->group(function () {
        Route::get('register', Register::class)->name('register');
        Route::get('login', Login::class)->name('login');
    });

    // Authenticated routes
    Route::middleware('auth:parking-slot-owner')->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
        Route::post('logout', [Logout::class, 'logout'])->name('logout');
        
        // Slots routes
        Route::get('slots', SlotList::class)->name('slots.index');
        Route::get('slots/create', CreateSlot::class)->name('slots.create');
        
        // Rate Cards routes will be added here
        // Route::get('slots/{slot}/rate-cards', RateCardList::class)->name('rate-cards.index');
        // Route::get('slots/{slot}/rate-cards/create', CreateRateCard::class)->name('rate-cards.create');
        // Route::get('rate-cards/{rateCard}/edit', EditRateCard::class)->name('rate-cards.edit');
    });
});

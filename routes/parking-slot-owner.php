<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\ParkingSlotOwner\Auth\Login;
use App\Livewire\ParkingSlotOwner\Auth\Logout;
use App\Livewire\Pages\ParkingSlotOwner\Auth\Register;
use App\Livewire\ParkingSlotOwner\CreateSlot;
use App\Livewire\ParkingSlotOwner\EditSlot;
use App\Livewire\Pages\ParkingSlotOwner\Dashboard;
use App\Livewire\ParkingSlotOwner\SlotList;
use App\Livewire\ParkingSlotOwner\RateCardList;
use App\Livewire\ParkingSlotOwner\CreateRateCard;
use App\Livewire\ParkingSlotOwner\EditRateCard;

Route::middleware('web')->prefix('owner')->name('parking-slot-owner.')->group(function () {
    // Guest routes
    Route::middleware('guest:parking-slot-owner')->group(function () {
        Route::get('login', Login::class)->name('login');
        Route::get('register', Register::class)->name('register');
    });

    // Authenticated routes
    Route::middleware('auth:parking-slot-owner')->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
        Route::post('logout', [Logout::class, 'logout'])->name('logout');
        
        // Slots routes
        Route::get('slots', SlotList::class)->name('slots.index');
        Route::get('slots/create', CreateSlot::class)->name('slots.create');
        Route::get('slots/{slot}/edit', EditSlot::class)->name('slots.edit');
        
        // Rate Cards routes
        Route::prefix('rate-cards')->name('rate-cards.')->group(function () {
            // Template routes
            Route::get('/', RateCardList::class)->name('index');
            Route::get('/create', CreateRateCard::class)->name('create');
            Route::get('/{rateCard}/edit', EditRateCard::class)
                ->name('edit')
                ->where('rateCard', '[0-9]+');
            
            // Slot-specific rate card routes
            Route::prefix('slots/{slot}')->name('slots.')->group(function () {
                Route::get('/', RateCardList::class)->name('index');
                Route::get('/create', CreateRateCard::class)->name('create');
            })->where('slot', '[0-9]+');
        });
    });
});

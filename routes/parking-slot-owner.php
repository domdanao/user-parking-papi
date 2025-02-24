<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ParkingSlotOwner\Auth\Login;
use App\Livewire\ParkingSlotOwner\Auth\Logout;
use App\Livewire\ParkingSlotOwner\CreateSlot;
use App\Livewire\ParkingSlotOwner\Dashboard;
use App\Livewire\ParkingSlotOwner\SlotList;
use App\Livewire\ParkingSlotOwner\RateCardList;
use App\Livewire\ParkingSlotOwner\CreateRateCard;
use App\Livewire\ParkingSlotOwner\EditRateCard;

Route::prefix('owner')->name('parking-slot-owner.')->group(function () {
    // Guest routes
    Route::middleware('guest.parking-slot-owner')->group(function () {
        Route::get('login', Login::class)->name('login');
    });

    // Authenticated routes
    Route::middleware('auth:parking-slot-owner')->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
        Route::post('logout', [Logout::class, 'logout'])->name('logout');
        
        // Slots routes
        Route::get('slots', SlotList::class)->name('slots.index');
        Route::get('slots/create', CreateSlot::class)->name('slots.create');
        
        // Rate Cards routes
        Route::prefix('rate-cards')->name('rate-cards.')->group(function () {
            // Root route must come first
            Route::get('/', RateCardList::class)->name('index');
            
            // Create route
            Route::get('/create', CreateRateCard::class)->name('create');
            
            // Slot-specific routes
            Route::prefix('slots')->name('slots.')->group(function () {
                Route::get('/{slot}', RateCardList::class)->name('index')->where('slot', '[0-9]+');
                Route::get('/{slot}/create', CreateRateCard::class)->name('create')->where('slot', '[0-9]+');
            });
            
            // Edit route must come last to avoid conflicts
            Route::get('/{rateCard}/edit', EditRateCard::class)->name('edit')->where('rateCard', '[0-9]+');
        });
    });
});

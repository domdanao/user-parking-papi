<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TakeSlot;

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

require __DIR__.'/auth.php';
require __DIR__.'/parking-slot-owner.php';

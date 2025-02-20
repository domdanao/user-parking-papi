<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('/scan', 'scan');
Route::view('/generate', 'generate');	

require __DIR__.'/auth.php';
require __DIR__.'/parking-slot-owner.php';

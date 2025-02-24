<?php

namespace App\Providers;

use App\Services\ZipService;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Pages\ParkingSlotOwner\Auth\Login;
use App\Livewire\Pages\ParkingSlotOwner\Dashboard;
use App\Livewire\Pages\ParkingSlotOwner\Auth\Register;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ZipService::class, function ($app) {
            return new ZipService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('pages.parking-slot-owner.auth.login', Login::class);
        Livewire::component('pages.parking-slot-owner.dashboard', Dashboard::class);
        Livewire::component('pages.parking-slot-owner.auth.register', Register::class);
    }
}

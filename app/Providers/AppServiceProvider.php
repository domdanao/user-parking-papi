<?php

namespace App\Providers;

use App\Services\ZipService;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Pages\ParkingSlotOwner\Auth\Login;

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
    }
}

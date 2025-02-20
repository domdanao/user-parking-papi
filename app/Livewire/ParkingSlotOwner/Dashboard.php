<?php

namespace App\Livewire\ParkingSlotOwner;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        return view('pages.parking-slot-owner.dashboard');
    }
}

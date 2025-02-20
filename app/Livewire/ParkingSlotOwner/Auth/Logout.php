<?php

namespace App\Livewire\ParkingSlotOwner\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public function logout()
    {
        Auth::guard('parking-slot-owner')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('parking-slot-owner.login');
    }
}

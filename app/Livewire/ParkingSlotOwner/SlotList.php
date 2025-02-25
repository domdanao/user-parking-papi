<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class SlotList extends Component
{
    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();
        $slots = Slot::where('parking_slot_owner_id', $owner->id)
            ->latest()
            ->get();

        return view('livewire.parking-slot-owner.slot-list', [
            'slots' => $slots
        ]);
    }
}

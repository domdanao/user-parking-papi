<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use Livewire\Component;
use Livewire\Attributes\Layout;

class SlotList extends Component
{
    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        $slots = Slot::where('parking_slot_owner_id', auth('parking-slot-owner')->id())
            ->latest()
            ->get();

        return view('livewire.parking-slot-owner.slot-list', [
            'slots' => $slots
        ]);
    }
}

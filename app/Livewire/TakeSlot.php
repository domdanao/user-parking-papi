<?php

namespace App\Livewire;

use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;

class TakeSlot extends Component
{
    public Slot $slot;

    public function mount($identifier)
    {
        $this->slot = Slot::with('owner')->where('identifier', $identifier)->firstOrFail();
    }

    #[Layout('layouts.minimal')]
    public function render()
    {
        $location = json_decode($this->slot->location, true);
        $owner = $this->slot->owner;
        
        return view('livewire.take-slot', [
            'slot' => $this->slot,
            'location' => $location,
            'owner' => $owner
        ]);
    }
}

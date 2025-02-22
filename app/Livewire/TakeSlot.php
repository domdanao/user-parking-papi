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
        if(gettype($this->slot->location) === 'string') {
            $location = json_decode($this->slot->location, true);
        } else {
            $location = $this->slot->location;
        }

        $owner = $this->slot->owner;
        
        return view('livewire.take-slot', [
            'slot' => $this->slot,
            'location' => $location,
            'owner' => $owner
        ]);
    }
}

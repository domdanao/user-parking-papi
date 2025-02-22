<?php

namespace App\Livewire;

use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

class TakeSlot extends Component
{
    public Slot $slot;
    public $duration = 6000; // Default to 2 hours (₱60.00)
    public $plate_no = '';

    public function mount($identifier)
    {
        $this->slot = Slot::with('owner')->where('identifier', $identifier)->firstOrFail();
    }

    public function updatedDuration($value)
    {
        $this->duration = (int) $value;
    }

    public function isPlateNumberValid()
    {
        return strlen(trim($this->plate_no)) >= 7;
    }

    public function pay()
    {
        // Validate required fields
        $this->validate([
            'plate_no' => 'required|min:4',
        ]);

        // TODO: Implement payment processing
        // For now, we'll just validate the fields
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

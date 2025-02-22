<?php

namespace App\Livewire;

use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;

class TakeSlot extends Component
{
    public Slot $slot;
    public $duration = 6000; // Default to 2 hours (₱60.00)
	public $plate_no = '';

    public function mount($identifier)
    {
        $this->slot = Slot::with('owner')->where('identifier', $identifier)->firstOrFail();
        $this->duration = 6000; // Ensure duration is set in mount
    }

	// On update of duration, update the slot's duration
	public function updatedDuration($value)
	{
		$this->duration = $value;
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

<?php

namespace App\Livewire;

use App\Models\Slot;
use Livewire\Component;
use Livewire\Attributes\Layout;

class TakeSlot extends Component
{
    public Slot $slot;

    public function mount($identifier)
    {
        $this->slot = Slot::where('identifier', $identifier)->firstOrFail();
    }

    #[Layout('layouts.minimal')]
    public function render()
    {
		$location = json_decode($this->slot->location, true);
        return view('livewire.take-slot', [
            'slot' => $this->slot,
			'location' => $location
        ]);
    }
}

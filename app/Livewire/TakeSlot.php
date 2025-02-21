<?php

namespace App\Livewire;

use App\Models\Slot;
use Livewire\Component;
use Livewire\Attributes\Layout;

class TakeSlot extends Component
{
    public Slot $slot;

    public function mount(Slot $slot)
    {
        $this->slot = $slot;
    }

    #[Layout('layouts.minimal')]
    public function render()
    {
        return view('livewire.take-slot', [
            'slot' => $this->slot
        ]);
    }
}

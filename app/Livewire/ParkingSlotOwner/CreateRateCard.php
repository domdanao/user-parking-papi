<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

class CreateRateCard extends Component
{
    public ?Slot $slot = null;

    #[Rule('boolean')]
    public $is_template = false;

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    #[Rule('required|integer|min:1')]
    public $hour_block = 1;

    #[Rule('required|integer|min:1')]
    public $rate = '';

    #[Rule('boolean')]
    public $is_active = true;

    public function mount(?Slot $slot = null)
    {
        $this->slot = $slot;
        // If creating for a specific slot, ensure ownership
        if ($this->slot && $this->slot->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }
        
        // If no slot provided, we're creating a template
        if (!$this->slot) {
            $this->is_template = true;
        }
    }

    public function save()
    {
        $this->validate();

        $rateCard = new \App\Models\RateCard([
            'parking_slot_owner_id' => auth('parking-slot-owner')->id(),
            'name' => $this->name,
            'description' => $this->description,
            'hour_block' => $this->hour_block,
            'rate' => $this->rate,
            'is_active' => $this->is_active,
            'is_template' => $this->is_template,
        ]);

        $rateCard->save();

        if ($this->slot) {
            $this->slot->rate_card_id = $rateCard->id;
            $this->slot->save();
        }

        session()->flash('status', $this->is_template ? 'Rate card template created successfully.' : 'Rate card created successfully.');

        return $this->slot 
            ? redirect()->route('parking-slot-owner.rate-cards.index', $this->slot)
            : redirect()->route('parking-slot-owner.rate-cards.index');
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        return view('livewire.parking-slot-owner.create-rate-card');
    }
}

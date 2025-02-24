<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\RateCard;
use App\Models\Slot;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

class EditRateCard extends Component
{
    public RateCard $rateCard;
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

    public function mount(RateCard $rateCard)
    {
        $this->rateCard = $rateCard;
        
        // Ensure the authenticated owner owns this rate card
        if ($this->rateCard->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }

        // Get the slot if this rate card is assigned to one
        $this->slot = Slot::where('rate_card_id', $this->rateCard->id)->first();

        // Load existing values
        $this->name = $rateCard->name;
        $this->description = $rateCard->description;
        $this->hour_block = $rateCard->hour_block;
        $this->rate = $rateCard->rate;
        $this->is_active = $rateCard->is_active;
        $this->is_template = $rateCard->is_template;
    }

    public function save()
    {
        $this->validate();

        $this->rateCard->update([
            'name' => $this->name,
            'description' => $this->description,
            'hour_block' => $this->hour_block,
            'rate' => $this->rate,
            'is_active' => $this->is_active,
        ]);

        session()->flash('status', 'Rate card updated successfully.');

        return $this->slot 
            ? redirect()->route('parking-slot-owner.rate-cards.index', $this->slot)
            : redirect()->route('parking-slot-owner.rate-cards.index');
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        return view('livewire.parking-slot-owner.edit-rate-card');
    }
}

<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\RateCard;
use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\{Auth, Log};

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
        // Check authentication first
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        $this->rateCard = $rateCard;
        
        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();
        
        // Ensure the authenticated owner owns this rate card
        if ($this->rateCard->parking_slot_owner_id !== $owner->id) {
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

        Log::info('EditRateCard mounted', [
            'rate_card_id' => $rateCard->id,
            'is_template' => $rateCard->is_template,
            'slot_id' => $this->slot?->id,
            'user_id' => $owner->id
        ]);
    }

    public function save()
    {
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        $this->validate();

        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();

        Log::info('Updating rate card', [
            'rate_card_id' => $this->rateCard->id,
            'is_template' => $this->is_template,
            'slot_id' => $this->slot?->id,
            'user_id' => $owner->id
        ]);

        try {
            $this->rateCard->update([
                'name' => $this->name,
                'description' => $this->description,
                'hour_block' => $this->hour_block,
                'rate' => $this->rate,
                'is_active' => $this->is_active,
            ]);

            session()->flash('status', $this->is_template ? 'Rate card template updated successfully.' : 'Rate card updated successfully.');

            return $this->slot 
                ? redirect()->route('parking-slot-owner.rate-cards.slots.index', $this->slot)
                : redirect()->route('parking-slot-owner.rate-cards.index');

        } catch (\Exception $e) {
            Log::error('Failed to update rate card', [
                'error' => $e->getMessage(),
                'rate_card_id' => $this->rateCard->id,
                'is_template' => $this->is_template,
                'slot_id' => $this->slot?->id,
                'user_id' => $owner->id
            ]);

            session()->flash('error', 'Failed to update rate card. Please try again.');
            return null;
        }
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        Log::info('EditRateCard rendering', [
            'route' => request()->route()->getName(),
            'authenticated' => auth('parking-slot-owner')->check(),
            'user_id' => auth('parking-slot-owner')->id()
        ]);

        return view('livewire.parking-slot-owner.edit-rate-card');
    }
}

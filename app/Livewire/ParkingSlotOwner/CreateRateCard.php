<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use App\Models\RateCard;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\{Auth, Log};

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
        // Check authentication first
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        $this->slot = $slot;
        
        // If creating for a specific slot, ensure ownership
        if ($this->slot && $this->slot->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }
        
        // If no slot provided, we're creating a template
        if (!$this->slot) {
            $this->is_template = true;
        }

        // Log mount parameters for debugging
        Log::info('CreateRateCard mounted', [
            'slot' => $slot ? $slot->toArray() : null,
            'is_template' => $this->is_template,
            'authenticated' => auth('parking-slot-owner')->check(),
            'user_id' => auth('parking-slot-owner')->id()
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

        Log::info('Creating rate card', [
            'data' => [
                'name' => $this->name,
                'is_template' => $this->is_template,
                'slot_id' => $this->slot?->id,
                'user_id' => $owner->id
            ]
        ]);

        try {
            $rateCard = new RateCard([
                'parking_slot_owner_id' => $owner->id,
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
                ? redirect()->route('parking-slot-owner.rate-cards.slots.index', $this->slot)
                : redirect()->route('parking-slot-owner.rate-cards.index');

        } catch (\Exception $e) {
            Log::error('Failed to create rate card', [
                'error' => $e->getMessage(),
                'data' => [
                    'name' => $this->name,
                    'is_template' => $this->is_template,
                    'slot_id' => $this->slot?->id,
                    'user_id' => $owner->id
                ]
            ]);

            session()->flash('error', 'Failed to create rate card. Please try again.');
            return null;
        }
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        Log::info('CreateRateCard rendering', [
            'route' => request()->route()->getName(),
            'authenticated' => auth('parking-slot-owner')->check(),
            'user_id' => auth('parking-slot-owner')->id()
        ]);

        return view('livewire.parking-slot-owner.create-rate-card');
    }
}

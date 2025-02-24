<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use App\Models\RateCard;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

class CreateSlot extends Component
{
    public $name = '';
    public $identifier = '';
    public $latitude = '';
    public $longitude = '';
    public $rateCardTemplateId = '';
    
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['nullable', 'string', 'max:255', 'unique:slots,identifier'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'rateCardTemplateId' => ['nullable', 'exists:rate_cards,id'],
        ];
    }

    public function save()
    {
        $this->validate();

        $slot = new Slot();
        $slot->parking_slot_owner_id = auth('parking-slot-owner')->id();
        $slot->name = $this->name;
        $slot->identifier = $this->identifier ?: Str::random(8);
        $slot->status = 'available';
        
        $slot->location = [
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
        ];

        $slot->save();

        session()->flash('status', 'Slot created successfully.');

        // Assign the rate card template if selected
        if ($this->rateCardTemplateId) {
            try {
                $template = RateCard::findOrFail($this->rateCardTemplateId);
                $slot->assignRateCardTemplate($template);
                return $this->redirect(route('parking-slot-owner.slots.index'), navigate: true);
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
                return $this->redirect(route('parking-slot-owner.slots.index'), navigate: true);
            }
        }

        return $this->redirect(route('parking-slot-owner.slots.index'), navigate: true);
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        return view('livewire.parking-slot-owner.create-slot', [
            'rateCardTemplates' => RateCard::where('is_template', true)
                ->where('parking_slot_owner_id', auth('parking-slot-owner')->id())
                ->get()
        ]);
    }
}

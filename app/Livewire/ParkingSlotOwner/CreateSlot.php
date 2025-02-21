<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

class CreateSlot extends Component
{
    public $name = '';
    public $identifier = '';
    public $latitude = '';
    public $longitude = '';
    
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['nullable', 'string', 'max:255', 'unique:slots,identifier'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function save()
    {
        $this->validate();

        $slot = new Slot();
        $slot->parking_slot_owner_id = auth('parking-slot-owner')->id();
        $slot->name = $this->name;
        $slot->identifier = $this->identifier ?: Str::random(8);
        
        if ($this->latitude && $this->longitude) {
            $slot->location = [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ];
        }

        $slot->save();

        session()->flash('status', 'Slot created successfully.');

        return redirect()->route('parking-slot-owner.slots.index');
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        return view('livewire.parking-slot-owner.create-slot');
    }
}

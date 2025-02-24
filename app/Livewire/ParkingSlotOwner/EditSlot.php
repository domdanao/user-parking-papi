<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use App\Models\RateCard;
use Livewire\Component;
use Livewire\Attributes\Layout;

class EditSlot extends Component
{
    public Slot $slot;
    public $name = '';
    public $identifier = '';
    public $latitude = '';
    public $longitude = '';
    public $rateCardTemplateId = '';

    public function confirmDeletion()
    {
        $this->authorize('delete', $this->slot);

        if ($this->slot->status === 'occupied') {
            session()->flash('error', 'Cannot delete an occupied slot.');
            return;
        }

        $this->js(<<<'JS'
            if (confirm('Are you sure you want to delete this slot? This action cannot be undone.')) {
                $wire.deleteSlot()
            }
        JS);
    }

    public function deleteSlot()
    {
        $this->authorize('delete', $this->slot);
        
        $this->slot->delete();
        
        session()->flash('status', 'Slot deleted successfully.');
        return $this->redirect(route('parking-slot-owner.slots.index'), navigate: true);
    }

    public function mount(Slot $slot)
    {
        $this->authorize('update', $slot);
        $this->slot = $slot;
        $this->name = $slot->name;
        $this->identifier = $slot->identifier;
        $this->latitude = $slot->location['latitude'] ?? '';
        $this->longitude = $slot->location['longitude'] ?? '';
        $this->rateCardTemplateId = $slot->rate_card_id;
    }
    
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['required', 'string', 'max:255', 'unique:slots,identifier,' . $this->slot->id],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'rateCardTemplateId' => ['nullable', 'exists:rate_cards,id'],
        ];
    }

    public function save()
    {
        $this->validate();

        $this->slot->name = $this->name;
        $this->slot->identifier = $this->identifier;
        $this->slot->location = [
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
        ];

        $this->slot->save();

        // Update rate card template if changed
        if ($this->rateCardTemplateId !== $this->slot->rate_card_id) {
            if ($this->rateCardTemplateId) {
                try {
                    $template = RateCard::findOrFail($this->rateCardTemplateId);
                    $this->slot->assignRateCardTemplate($template);
                } catch (\Exception $e) {
                    session()->flash('error', $e->getMessage());
                    return $this->redirect(route('parking-slot-owner.slots.index'), navigate: true);
                }
            } else {
                // Remove rate card if none selected
                $this->slot->rate_card_id = null;
                $this->slot->save();
            }
        }

        session()->flash('status', 'Slot updated successfully.');
        return $this->redirect(route('parking-slot-owner.slots.index'), navigate: true);
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        return view('livewire.parking-slot-owner.edit-slot', [
            'rateCardTemplates' => RateCard::where('is_template', true)
                ->where('parking_slot_owner_id', auth('parking-slot-owner')->id())
                ->get()
        ]);
    }
}

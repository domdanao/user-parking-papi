<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\RateCard;
use App\Models\Slot;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

class RateCardList extends Component
{
    public ?Slot $slot = null;
    public bool $showTemplates = true;

    public function mount(?Slot $slot = null)
    {
        $this->slot = $slot;
        
        // If viewing slot-specific rate cards, ensure ownership
        if (!auth('parking-slot-owner')->check()) {
            abort(401);
        }

        if ($this->slot && $this->slot->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }

        // Always show templates on the root rate-cards route
        // Show slot rate cards only when viewing a specific slot
        $this->showTemplates = !$this->slot;

    }

    #[Computed]
    public function templates()
    {
        if (!auth('parking-slot-owner')->check()) {
            return collect();
        }

        return RateCard::query()
            ->where('parking_slot_owner_id', auth('parking-slot-owner')->id())
            ->where('is_template', true)  // Explicitly check for templates
            ->latest()
            ->get();
    }

    #[Computed]
    public function slotRateCards()
    {
        if (!$this->slot || !auth('parking-slot-owner')->check()) {
            return collect();
        }

        return RateCard::query()
            ->where('parking_slot_owner_id', auth('parking-slot-owner')->id())
            ->whereIn('id', [$this->slot->rate_card_id])
            ->latest()
            ->get();
    }

    public function toggleStatus(RateCard $rateCard)
    {
        if (!auth('parking-slot-owner')->check()) {
            abort(401);
        }

        if ($rateCard->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }

        $rateCard->update([
            'is_active' => !$rateCard->is_active
        ]);

        session()->flash('status', 'Rate card status updated.');
    }

    public function deleteRateCard(RateCard $rateCard)
    {
        if (!auth('parking-slot-owner')->check()) {
            abort(401);
        }

        if ($rateCard->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }

        if ($rateCard->is_template && $rateCard->slots()->exists()) {
            session()->flash('error', 'Cannot delete template: it is being used by slots.');
            return;
        }

        $rateCard->delete();
        session()->flash('status', 'Rate card deleted.');
    }

    public function assignTemplate(RateCard $template)
    {
        if (!auth('parking-slot-owner')->check()) {
            abort(401);
        }

        if (!$this->slot) {
            return;
        }

        if ($template->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }

        try {
            $this->slot->assignRateCardTemplate($template);
            session()->flash('status', 'Rate card template assigned.');
            return $this->redirect(route('parking-slot-owner.rate-cards.slots.index', $this->slot), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return null;
        }
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        return view('livewire.parking-slot-owner.rate-card-list', [
            'rateCards' => $this->showTemplates ? $this->templates : $this->slotRateCards,
            'showCreateButton' => $this->showTemplates,
        ]);
    }
}

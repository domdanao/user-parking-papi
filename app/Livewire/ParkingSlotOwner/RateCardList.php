<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\RateCard;
use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RateCardList extends Component
{
    // public ?Slot $slot = null;
    // public bool $showTemplates = true;

    // public function mount(?Slot $slot = null)
    // {
	//    if (!auth('parking-slot-owner')->check()) {
    //         Log::warning('Unauthenticated access attempt to RateCardList', [
    //             'route' => request()->route()->getName(),
    //             'url' => request()->url()
    //         ]);
    //         return redirect()->route('parking-slot-owner.login');
    //     }

    //     $this->slot = $slot;
    //     $this->showTemplates = !$this->slot;

    //     // If viewing slot-specific rate cards, ensure ownership
    //     if ($this->slot && $this->slot->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
    //         Log::warning('Unauthorized access attempt to slot rate cards', [
    //             'user_id' => auth('parking-slot-owner')->id(),
    //             'slot_id' => $this->slot->id,
    //             'slot_owner_id' => $this->slot->parking_slot_owner_id
    //         ]);
    //         abort(403);
    //     }

    //     Log::info('RateCardList mounted', [
    //         'user_id' => auth('parking-slot-owner')->id(),
    //         'show_templates' => $this->showTemplates,
    //         'slot' => $slot ? [
    //             'id' => $slot->id,
    //             'name' => $slot->name,
    //             'owner_id' => $slot->parking_slot_owner_id
    //         ] : null,
    //         'route' => request()->route()->getName()
    //     ]);
    // }

    // #[Computed]
    // public function templates()
    // {
    //     /** @var ParkingSlotOwner $owner */
    //     $owner = Auth::guard('parking-slot-owner')->user();
    //     return $owner ? RateCard::where('parking_slot_owner_id', $owner->id)
    //         ->where('is_template', true)
    //         ->latest()
    //         ->get() : collect();
    // }

    // #[Computed]
    // public function slotsNeedingUpdate()
    // {
    //     /** @var ParkingSlotOwner $owner */
    //     $owner = Auth::guard('parking-slot-owner')->user();
    //     return $owner ? $owner->getSlotsNeedingRateCardUpdate() : collect();
    // }

    // #[Computed]
    // public function slotRateCards()
    // {
    //     if (!$this->slot) {
    //         return collect();
    //     }

    //     /** @var ParkingSlotOwner $owner */
    //     $owner = Auth::guard('parking-slot-owner')->user();
    //     return $owner ? RateCard::where('parking_slot_owner_id', $owner->id)
    //         ->when($this->slot->rate_card_id, function ($query) {
    //             return $query->whereIn('id', [$this->slot->rate_card_id]);
    //         })
    //         ->latest()
    //         ->get() : collect();
    // }

    // public function toggleStatus(RateCard $rateCard)
    // {
    //     /** @var ParkingSlotOwner $owner */
    //     $owner = Auth::guard('parking-slot-owner')->user();
    //     if (!$owner || $rateCard->parking_slot_owner_id !== $owner->id) {
    //         abort(403);
    //     }

    //     try {
    //         $newStatus = !$rateCard->is_active;

    //         // If trying to deactivate
    //         if (!$newStatus) {
    //             if (!$rateCard->canBeDeactivated()) {
    //                 session()->flash('error', __('Cannot deactivate template: it is being used by :count slots. Please update or remove the rate card from these slots first.', [
    //                     'count' => $rateCard->usage_count
    //                 ]));
    //                 return;
    //             }
    //         }

    //         $rateCard->update([
    //             'is_active' => $newStatus
    //         ]);

    //         $message = $newStatus
    //             ? __('Rate card activated successfully.')
    //             : __('Rate card deactivated successfully.');

    //         if ($rateCard->is_template) {
    //             $message = $newStatus
    //                 ? __('Template activated and is now available for assignment.')
    //                 : __('Template deactivated and can no longer be assigned to slots.');
    //         }

    //         session()->flash('status', $message);

    //         Log::info('Rate card status updated', [
    //             'rate_card_id' => $rateCard->id,
    //             'rate_card_name' => $rateCard->name,
    //             'rate_card_rate' => $rateCard->getFormattedRate(),
    //             'is_template' => $rateCard->is_template,
    //             'old_status' => !$newStatus,
    //             'new_status' => $newStatus,
    //             'usage_count' => $rateCard->usage_count,
    //             'slots_using' => $rateCard->is_template ? $rateCard->slots()->pluck('name')->toArray() : [],
    //             'user_id' => $owner->id,
    //             'stack_trace' => (new \Exception())->getTraceAsString()
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to update rate card status', [
    //             'error' => $e->getMessage(),
    //             'rate_card_id' => $rateCard->id,
    //             'rate_card_name' => $rateCard->name,
    //             'rate_card_rate' => $rateCard->getFormattedRate(),
    //             'is_template' => $rateCard->is_template,
    //             'old_status' => $rateCard->is_active,
    //             'new_status' => $newStatus,
    //             'usage_count' => $rateCard->usage_count,
    //             'slots_using' => $rateCard->is_template ? $rateCard->slots()->pluck('name')->toArray() : [],
    //             'can_be_deactivated' => $rateCard->canBeDeactivated(),
    //             'user_id' => $owner->id,
    //             'stack_trace' => $e->getTraceAsString()
    //         ]);

    //         session()->flash('error', __('Failed to update rate card status. Please try again.'));
    //     }
    // }

    // public function deleteRateCard(RateCard $rateCard)
    // {
    //     /** @var ParkingSlotOwner $owner */
    //     $owner = Auth::guard('parking-slot-owner')->user();
    //     if (!$owner || $rateCard->parking_slot_owner_id !== $owner->id) {
    //         abort(403);
    //     }

    //     try {
    //         if (!$rateCard->canBeDeleted()) {
    //             session()->flash('error', $rateCard->getDeletionWarning());
    //             return;
    //         }

    //         $name = $rateCard->name;
    //         $isTemplate = $rateCard->is_template;
    //         $usageCount = $rateCard->usage_count;

    //         $rateCard->delete();
            
    //         $message = $isTemplate
    //             ? __('Rate card template ":name" deleted successfully.', ['name' => $name])
    //             : __('Rate card ":name" deleted successfully.', ['name' => $name]);

    //         session()->flash('status', $message);

    //         Log::info('Rate card deleted', [
    //             'rate_card_id' => $rateCard->id,
    //             'rate_card_name' => $name,
    //             'rate_card_rate' => $rateCard->getFormattedRate(),
    //             'is_template' => $isTemplate,
    //             'usage_count' => $usageCount,
    //             'slots_using' => $isTemplate ? $rateCard->slots()->pluck('name')->toArray() : [],
    //             'can_be_deleted' => $rateCard->canBeDeleted(),
    //             'deletion_warning' => $rateCard->getDeletionWarning(),
    //             'user_id' => $owner->id,
    //             'stack_trace' => (new \Exception())->getTraceAsString()
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to delete rate card', [
    //             'error' => $e->getMessage(),
    //             'rate_card_id' => $rateCard->id,
    //             'rate_card_name' => $rateCard->name,
    //             'rate_card_rate' => $rateCard->getFormattedRate(),
    //             'is_template' => $rateCard->is_template,
    //             'usage_count' => $rateCard->usage_count,
    //             'slots_using' => $rateCard->is_template ? $rateCard->slots()->pluck('name')->toArray() : [],
    //             'can_be_deleted' => $rateCard->canBeDeleted(),
    //             'deletion_warning' => $rateCard->getDeletionWarning(),
    //             'user_id' => $owner->id,
    //             'stack_trace' => $e->getTraceAsString()
    //         ]);

    //         session()->flash('error', __('Failed to delete rate card ":name". Please try again.', [
    //             'name' => $rateCard->name
    //         ]));
    //     }
    // }

    // public function assignTemplate(RateCard $template)
    // {
    //     if (!$this->slot) {
    //         return;
    //     }

    //     /** @var ParkingSlotOwner $owner */
    //     $owner = Auth::guard('parking-slot-owner')->user();
    //     if (!$owner || $template->parking_slot_owner_id !== $owner->id) {
    //         abort(403);
    //     }

    //     try {
    //         // Check if template can be assigned to this slot
    //         $status = $template->getAssignmentStatus($this->slot);
    //         if (!$status['can_assign']) {
    //             session()->flash('error', $status['message']);
    //             return;
    //         }

    //         // Log the assignment attempt
    //         Log::info('Attempting to assign rate card template', [
    //             'template_id' => $template->id,
    //             'template_name' => $template->name,
    //             'slot_id' => $this->slot->id,
    //             'slot_name' => $this->slot->name,
    //             'current_rate_card' => $this->slot->hasRateCard() ? $this->slot->getFormattedRate() : null,
    //             'new_rate_card' => $template->getFormattedRate(),
    //             'user_id' => $owner->id,
    //             'stack_trace' => (new \Exception())->getTraceAsString()
    //         ]);

    //         $oldRateCard = $this->slot->rateCard;
    //         $this->slot->assignRateCardTemplate($template);

    //         if ($oldRateCard && $oldRateCard->is_template && $oldRateCard->id !== $template->id) {
    //             session()->flash('status', __('Rate card updated from :old to :new', [
    //                 'old' => $oldRateCard->getFormattedRate(),
    //                 'new' => $template->getFormattedRate()
    //             ]));
    //         } else {
    //             session()->flash('status', __('Rate card template ":name" assigned successfully.', [
    //                 'name' => $template->name
    //             ]));
    //         }

    //         Log::info('Rate card template assigned', [
    //             'template_id' => $template->id,
    //             'template_name' => $template->name,
    //             'template_rate' => $template->getFormattedRate(),
    //             'slot_id' => $this->slot->id,
    //             'slot_name' => $this->slot->name,
    //             'old_rate_card' => $oldRateCard ? [
    //                 'id' => $oldRateCard->id,
    //                 'name' => $oldRateCard->name,
    //                 'rate' => $oldRateCard->getFormattedRate(),
    //                 'is_template' => $oldRateCard->is_template,
    //             ] : null,
    //             'user_id' => $owner->id,
    //             'stack_trace' => (new \Exception())->getTraceAsString()
    //         ]);

    //         return $this->redirect(route('parking-slot-owner.rate-cards.slots.index', $this->slot), navigate: true);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to assign rate card template', [
    //             'error' => $e->getMessage(),
    //             'template_id' => $template->id,
    //             'template_name' => $template->name,
    //             'template_rate' => $template->getFormattedRate(),
    //             'slot_id' => $this->slot->id,
    //             'slot_name' => $this->slot->name,
    //             'current_rate_card' => $this->slot->hasRateCard() ? [
    //                 'id' => $this->slot->rateCard->id,
    //                 'name' => $this->slot->rateCard->name,
    //                 'rate' => $this->slot->rateCard->getFormattedRate(),
    //                 'is_template' => $this->slot->rateCard->is_template,
    //             ] : null,
    //             'user_id' => $owner->id,
    //             'stack_trace' => $e->getTraceAsString()
    //         ]);

    //         session()->flash('error', __('Failed to assign rate card template. Please try again.'));
    //         return null;
    //     }
    // }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        if (!auth('parking-slot-owner')->check()) {
            Log::warning('Unauthenticated access attempt to RateCardList render', [
                'route' => request()->route()->getName(),
                'url' => request()->url()
            ]);
            return redirect()->route('parking-slot-owner.login');
        }

        Log::info('RateCardList rendering', [
            'route' => request()->route()->getName(),
            'authenticated' => auth('parking-slot-owner')->check(),
            'user_id' => auth('parking-slot-owner')->id()
        ]);

        return view('livewire.parking-slot-owner.rate-card-list');
		// , [
        //     'rateCards' => $this->showTemplates ? $this->templates : $this->slotRateCards,
        //     'showCreateButton' => $this->showTemplates,
        //     'slotsNeedingUpdate' => $this->slotsNeedingUpdate,
        // ]);
    }
}

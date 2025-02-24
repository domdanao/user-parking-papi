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
    public ?Slot $slot = null;
    public bool $showTemplates = true;

    public function mount(?Slot $slot = null)
    {
        // Check authentication first
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        $this->slot = $slot;
        
        // If viewing slot-specific rate cards, ensure ownership
        if ($this->slot && $this->slot->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }

        // Always show templates on the root rate-cards route
        // Show slot rate cards only when viewing a specific slot
        $this->showTemplates = !$this->slot;

        // Log mount parameters for debugging
        Log::info('RateCardList mounted', [
            'user_id' => auth('parking-slot-owner')->id(),
            'show_templates' => $this->showTemplates,
            'slot' => $slot ? [
                'id' => $slot->id,
                'name' => $slot->name,
                'has_rate_card' => $slot->hasRateCard(),
                'current_rate' => $slot->hasRateCard() ? $slot->getFormattedRate() : null,
                'needs_update' => $slot->hasRateCard() ? $slot->needsRateCardUpdate() : false,
                'rate_card' => $slot->rateCard ? [
                    'id' => $slot->rateCard->id,
                    'name' => $slot->rateCard->name,
                    'rate' => $slot->rateCard->getFormattedRate(),
                    'is_template' => $slot->rateCard->is_template,
                    'is_active' => $slot->rateCard->is_active,
                ] : null
            ] : null,
            'stack_trace' => (new \Exception())->getTraceAsString()
        ]);
    }

    #[Computed]
    public function templates()
    {
        if (!auth('parking-slot-owner')->check()) {
            return collect();
        }

        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();

        try {
            $templates = RateCard::where('parking_slot_owner_id', $owner->id)
                ->where('is_template', true)
                ->latest()
                ->get();

            if ($this->slot) {
                $templates->each(function ($template) {
                    $status = $template->getAssignmentStatus($this->slot);
                    $template->assignment_status = $status;

                    Log::debug('Template assignment status', [
                        'template_id' => $template->id,
                        'template_name' => $template->name,
                        'slot_id' => $this->slot->id,
                        'slot_name' => $this->slot->name,
                        'can_assign' => $status['can_assign'],
                        'message' => $status['message'],
                        'stack_trace' => (new \Exception())->getTraceAsString()
                    ]);
                });
            }

            Log::info('Fetched rate card templates with stats', [
                'user_id' => $owner->id,
                'template_count' => $templates->count(),
                'active_templates' => $templates->where('is_active', true)->count(),
                'templates_in_use' => $templates->where('usage_count', '>', 0)->count(),
                'total_slots_using_templates' => $templates->sum('usage_count'),
                'viewing_slot' => $this->slot ? [
                    'id' => $this->slot->id,
                    'name' => $this->slot->name,
                    'has_rate_card' => $this->slot->hasRateCard(),
                    'current_rate' => $this->slot->hasRateCard() ? $this->slot->getFormattedRate() : null,
                    'needs_update' => $this->slot->hasRateCard() ? $this->slot->needsRateCardUpdate() : false,
                ] : null,
                'stack_trace' => (new \Exception())->getTraceAsString()
            ]);

            return $templates;
        } catch (\Exception $e) {
            Log::error('Failed to fetch rate card templates', [
                'error' => $e->getMessage(),
                'user_id' => $owner->id,
                'viewing_slot' => $this->slot ? [
                    'id' => $this->slot->id,
                    'name' => $this->slot->name,
                    'has_rate_card' => $this->slot->hasRateCard(),
                    'current_rate' => $this->slot->hasRateCard() ? $this->slot->getFormattedRate() : null,
                    'needs_update' => $this->slot->hasRateCard() ? $this->slot->needsRateCardUpdate() : false,
                ] : null,
                'stack_trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', __('Failed to load rate card templates. Please try again.'));
            return collect();
        }
    }

    #[Computed]
    public function slotsNeedingUpdate()
    {
        if (!auth('parking-slot-owner')->check()) {
            return collect();
        }

        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();

        try {
            $slots = $owner->getSlotsNeedingRateCardUpdate();

            if ($slots->isNotEmpty()) {
                Log::warning('Found slots with inactive rate cards', [
                    'user_id' => $owner->id,
                    'slot_count' => $slots->count(),
                    'slots' => $slots->map(function ($slot) {
                        return [
                            'id' => $slot->id,
                            'name' => $slot->name,
                            'rate_card' => $slot->rateCard ? [
                                'id' => $slot->rateCard->id,
                                'name' => $slot->rateCard->name,
                                'rate' => $slot->rateCard->getFormattedRate(),
                                'is_template' => $slot->rateCard->is_template,
                                'is_active' => $slot->rateCard->is_active,
                            ] : null
                        ];
                    })->toArray(),
                    'viewing_slot' => $this->slot ? [
                        'id' => $this->slot->id,
                        'name' => $this->slot->name,
                        'has_rate_card' => $this->slot->hasRateCard(),
                        'current_rate' => $this->slot->hasRateCard() ? $this->slot->getFormattedRate() : null,
                        'needs_update' => $this->slot->hasRateCard() ? $this->slot->needsRateCardUpdate() : false,
                    ] : null,
                    'stack_trace' => (new \Exception())->getTraceAsString()
                ]);
            }

            return $slots;
        } catch (\Exception $e) {
            Log::error('Failed to check for slots needing rate card updates', [
                'error' => $e->getMessage(),
                'user_id' => $owner->id,
                'viewing_slot' => $this->slot ? [
                    'id' => $this->slot->id,
                    'name' => $this->slot->name,
                    'has_rate_card' => $this->slot->hasRateCard(),
                    'current_rate' => $this->slot->hasRateCard() ? $this->slot->getFormattedRate() : null,
                    'needs_update' => $this->slot->hasRateCard() ? $this->slot->needsRateCardUpdate() : false,
                ] : null,
                'stack_trace' => $e->getTraceAsString()
            ]);

            return collect();
        }
    }

    #[Computed]
    public function slotRateCards()
    {
        if (!$this->slot || !auth('parking-slot-owner')->check()) {
            return collect();
        }

        Log::info('Fetching slot rate cards', [
            'user_id' => auth('parking-slot-owner')->id(),
            'slot' => [
                'id' => $this->slot->id,
                'name' => $this->slot->name,
                'has_rate_card' => $this->slot->hasRateCard(),
                'current_rate' => $this->slot->hasRateCard() ? $this->slot->getFormattedRate() : null,
                'needs_update' => $this->slot->hasRateCard() ? $this->slot->needsRateCardUpdate() : false,
                'rate_card' => $this->slot->rateCard ? [
                    'id' => $this->slot->rateCard->id,
                    'name' => $this->slot->rateCard->name,
                    'rate' => $this->slot->rateCard->getFormattedRate(),
                    'is_template' => $this->slot->rateCard->is_template,
                    'is_active' => $this->slot->rateCard->is_active,
                ] : null
            ],
            'stack_trace' => (new \Exception())->getTraceAsString()
        ]);

        return RateCard::query()
            ->where('parking_slot_owner_id', auth('parking-slot-owner')->id())
            ->when($this->slot->rate_card_id, function ($query) {
                return $query->whereIn('id', [$this->slot->rate_card_id]);
            })
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

        try {
            $newStatus = !$rateCard->is_active;

            // If trying to deactivate
            if (!$newStatus) {
                if (!$rateCard->canBeDeactivated()) {
                    session()->flash('error', __('Cannot deactivate template: it is being used by :count slots. Please update or remove the rate card from these slots first.', [
                        'count' => $rateCard->usage_count
                    ]));
                    return;
                }
            }

            $rateCard->update([
                'is_active' => $newStatus
            ]);

            $message = $newStatus
                ? __('Rate card activated successfully.')
                : __('Rate card deactivated successfully.');

            if ($rateCard->is_template) {
                $message = $newStatus
                    ? __('Template activated and is now available for assignment.')
                    : __('Template deactivated and can no longer be assigned to slots.');
            }

            session()->flash('status', $message);

            Log::info('Rate card status updated', [
                'rate_card_id' => $rateCard->id,
                'rate_card_name' => $rateCard->name,
                'rate_card_rate' => $rateCard->getFormattedRate(),
                'is_template' => $rateCard->is_template,
                'old_status' => !$newStatus,
                'new_status' => $newStatus,
                'usage_count' => $rateCard->usage_count,
                'slots_using' => $rateCard->is_template ? $rateCard->slots()->pluck('name')->toArray() : [],
                'user_id' => auth('parking-slot-owner')->id(),
                'stack_trace' => (new \Exception())->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update rate card status', [
                'error' => $e->getMessage(),
                'rate_card_id' => $rateCard->id,
                'rate_card_name' => $rateCard->name,
                'rate_card_rate' => $rateCard->getFormattedRate(),
                'is_template' => $rateCard->is_template,
                'old_status' => $rateCard->is_active,
                'new_status' => $newStatus,
                'usage_count' => $rateCard->usage_count,
                'slots_using' => $rateCard->is_template ? $rateCard->slots()->pluck('name')->toArray() : [],
                'can_be_deactivated' => $rateCard->canBeDeactivated(),
                'user_id' => auth('parking-slot-owner')->id(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', __('Failed to update rate card status. Please try again.'));
        }
    }

    public function deleteRateCard(RateCard $rateCard)
    {
        if (!auth('parking-slot-owner')->check()) {
            abort(401);
        }

        if ($rateCard->parking_slot_owner_id !== auth('parking-slot-owner')->id()) {
            abort(403);
        }

        try {
            if (!$rateCard->canBeDeleted()) {
                session()->flash('error', $rateCard->getDeletionWarning());
                return;
            }

            $name = $rateCard->name;
            $isTemplate = $rateCard->is_template;
            $usageCount = $rateCard->usage_count;

            $rateCard->delete();
            
            $message = $isTemplate
                ? __('Rate card template ":name" deleted successfully.', ['name' => $name])
                : __('Rate card ":name" deleted successfully.', ['name' => $name]);

            session()->flash('status', $message);

            Log::info('Rate card deleted', [
                'rate_card_id' => $rateCard->id,
                'rate_card_name' => $name,
                'rate_card_rate' => $rateCard->getFormattedRate(),
                'is_template' => $isTemplate,
                'usage_count' => $usageCount,
                'slots_using' => $isTemplate ? $rateCard->slots()->pluck('name')->toArray() : [],
                'can_be_deleted' => $rateCard->canBeDeleted(),
                'deletion_warning' => $rateCard->getDeletionWarning(),
                'user_id' => auth('parking-slot-owner')->id(),
                'stack_trace' => (new \Exception())->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete rate card', [
                'error' => $e->getMessage(),
                'rate_card_id' => $rateCard->id,
                'rate_card_name' => $rateCard->name,
                'rate_card_rate' => $rateCard->getFormattedRate(),
                'is_template' => $rateCard->is_template,
                'usage_count' => $rateCard->usage_count,
                'slots_using' => $rateCard->is_template ? $rateCard->slots()->pluck('name')->toArray() : [],
                'can_be_deleted' => $rateCard->canBeDeleted(),
                'deletion_warning' => $rateCard->getDeletionWarning(),
                'user_id' => auth('parking-slot-owner')->id(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', __('Failed to delete rate card ":name". Please try again.', [
                'name' => $rateCard->name
            ]));
        }
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
            // Check if template can be assigned to this slot
            $status = $template->getAssignmentStatus($this->slot);
            if (!$status['can_assign']) {
                session()->flash('error', $status['message']);
                return;
            }

            // Log the assignment attempt
            Log::info('Attempting to assign rate card template', [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'slot_id' => $this->slot->id,
                'slot_name' => $this->slot->name,
                'current_rate_card' => $this->slot->hasRateCard() ? $this->slot->getFormattedRate() : null,
                'new_rate_card' => $template->getFormattedRate(),
                'user_id' => auth('parking-slot-owner')->id(),
                'stack_trace' => (new \Exception())->getTraceAsString()
            ]);

            $oldRateCard = $this->slot->rateCard;
            $this->slot->assignRateCardTemplate($template);

            if ($oldRateCard && $oldRateCard->is_template && $oldRateCard->id !== $template->id) {
                session()->flash('status', __('Rate card updated from :old to :new', [
                    'old' => $oldRateCard->getFormattedRate(),
                    'new' => $template->getFormattedRate()
                ]));
            } else {
                session()->flash('status', __('Rate card template ":name" assigned successfully.', [
                    'name' => $template->name
                ]));
            }

            Log::info('Rate card template assigned', [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'template_rate' => $template->getFormattedRate(),
                'slot_id' => $this->slot->id,
                'slot_name' => $this->slot->name,
                'old_rate_card' => $oldRateCard ? [
                    'id' => $oldRateCard->id,
                    'name' => $oldRateCard->name,
                    'rate' => $oldRateCard->getFormattedRate(),
                    'is_template' => $oldRateCard->is_template,
                ] : null,
                'user_id' => auth('parking-slot-owner')->id(),
                'stack_trace' => (new \Exception())->getTraceAsString()
            ]);

            return $this->redirect(route('parking-slot-owner.rate-cards.slots.index', $this->slot), navigate: true);
        } catch (\Exception $e) {
            Log::error('Failed to assign rate card template', [
                'error' => $e->getMessage(),
                'template_id' => $template->id,
                'template_name' => $template->name,
                'template_rate' => $template->getFormattedRate(),
                'slot_id' => $this->slot->id,
                'slot_name' => $this->slot->name,
                'current_rate_card' => $this->slot->hasRateCard() ? [
                    'id' => $this->slot->rateCard->id,
                    'name' => $this->slot->rateCard->name,
                    'rate' => $this->slot->rateCard->getFormattedRate(),
                    'is_template' => $this->slot->rateCard->is_template,
                ] : null,
                'user_id' => auth('parking-slot-owner')->id(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', __('Failed to assign rate card template. Please try again.'));
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
            'slotsNeedingUpdate' => $this->slotsNeedingUpdate,
        ]);
    }
}

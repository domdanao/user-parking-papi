<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class RateCard extends Model
{
    protected $fillable = [
        'parking_slot_owner_id',
        'name',
        'description',
        'hour_block',
        'rate',
        'is_active',
        'is_template',
    ];

    protected $casts = [
        'hour_block' => 'integer',
        'rate' => 'integer',
        'is_active' => 'boolean',
        'is_template' => 'boolean',
    ];

    protected $appends = [
        'usage_count',
    ];

    /**
     * Get the owner of this rate card.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(ParkingSlotOwner::class, 'parking_slot_owner_id');
    }

    /**
     * Get the slots using this rate card.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    /**
     * Get the number of slots using this template
     */
    public function getUsageCountAttribute(): int
    {
        if (!$this->is_template) {
            return 0;
        }

        return $this->slots()->count();
    }

    /**
     * Get the formatted rate for display
     */
    public function getFormattedRate(): string
    {
        return '₱' . number_format($this->rate / 100, 2) . ' / ' . $this->hour_block . 'hr';
    }

    /**
     * Calculate total rate for a duration
     */
    public function calculateTotalRate(float $duration): int
    {
        $blocks = ceil($duration / $this->hour_block);
        return $blocks * $this->rate;
    }

    /**
     * Create a new rate card from this template
     */
    public function createFromTemplate(): self
    {
        if (!$this->is_template) {
            throw new \Exception(__('Can only create new rate cards from templates'));
        }

        if (!$this->is_active) {
            throw new \Exception(__('Cannot create rate cards from inactive templates'));
        }

        try {
            $newRateCard = self::create([
                'parking_slot_owner_id' => $this->parking_slot_owner_id,
                'name' => $this->name,
                'description' => $this->description,
                'hour_block' => $this->hour_block,
                'rate' => $this->rate,
                'is_active' => true,
                'is_template' => false,
            ]);

            Log::info('Created rate card from template', [
                'template_id' => $this->id,
                'new_rate_card_id' => $newRateCard->id,
                'owner_id' => $this->parking_slot_owner_id
            ]);

            return $newRateCard;
        } catch (\Exception $e) {
            Log::error('Failed to create rate card from template', [
                'error' => $e->getMessage(),
                'template_id' => $this->id,
                'owner_id' => $this->parking_slot_owner_id
            ]);

            throw new \Exception(__('Failed to create rate card from template. Please try again.'));
        }
    }

    /**
     * Check if this template is being used by any slots
     */
    public function isInUse(): bool
    {
        return $this->is_template && $this->slots()->count() > 0;
    }

    /**
     * Check if this template can be assigned to slots
     */
    public function canBeAssigned(): bool
    {
        return $this->is_template && $this->is_active;
    }

    /**
     * Get the assignment status for a specific slot
     */
    public function getAssignmentStatus(Slot $slot): array
    {
        if ($this->parking_slot_owner_id !== $slot->parking_slot_owner_id) {
            return [
                'can_assign' => false,
                'message' => __('Cannot assign template to slots owned by other users')
            ];
        }

        if (!$this->is_template) {
            return [
                'can_assign' => false,
                'message' => __('Cannot assign non-template rate card')
            ];
        }

        if (!$this->is_active) {
            return [
                'can_assign' => false,
                'message' => __('Cannot assign inactive template')
            ];
        }

        if ($slot->hasRateCard() && $slot->rateCard->is_template) {
            if ($slot->rateCard->id === $this->id) {
                if ($slot->needsRateCardUpdate()) {
                    return [
                        'can_assign' => true,
                        'message' => __('Update rate card to latest version?')
                    ];
                }
                return [
                    'can_assign' => false,
                    'message' => __('Already using this template')
                ];
            }
        }

        if ($slot->hasRateCard()) {
            return [
                'can_assign' => true,
                'message' => __('Will replace :current with :new', [
                    'current' => $slot->getFormattedRate(),
                    'new' => $this->getFormattedRate()
                ])
            ];
        }

        return [
            'can_assign' => true,
            'message' => __('Assign :rate to slot', ['rate' => $this->getFormattedRate()])
        ];
    }

    /**
     * Check if this template can be deactivated
     */
    public function canBeDeactivated(): bool
    {
        return !$this->is_template || !$this->isInUse();
    }

    /**
     * Check if this rate card can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_template || !$this->isInUse();
    }

    /**
     * Get the deletion warning message
     */
    public function getDeletionWarning(): string
    {
        if (!$this->canBeDeleted()) {
            return __('Cannot delete template: it is being used by :count slots. Please update or remove the rate card from these slots first.', [
                'count' => $this->slots()->count()
            ]);
        }

        if ($this->is_template) {
            return __('Are you sure you want to delete this template? This action cannot be undone.');
        }

        return __('Are you sure you want to delete this rate card? This action cannot be undone.');
    }

    /**
     * Get the status message for this rate card
     */
    public function getStatusMessage(): string
    {
        if (!$this->is_template) {
            return $this->is_active ? __('Active') : __('Inactive');
        }

        if (!$this->is_active) {
            return __('Inactive Template');
        }

        $count = $this->slots()->count();
        if ($count > 0) {
            return trans_choice(
                'Active Template (:count slot)|Active Template (:count slots)', 
                $count,
                ['count' => $count]
            );
        }

        return __('Active Template');
    }

    /**
     * Scope a query to only include templates.
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope a query to only include non-templates.
     */
    public function scopeNonTemplates($query)
    {
        return $query->where('is_template', false);
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActiveTemplates($query)
    {
        return $query->where('is_template', true)
            ->where('is_active', true);
    }

    /**
     * Get the template status for this rate card
     */
    public function getTemplateStatus(): string
    {
        if (!$this->is_template) {
            return 'non_template';
        }

        return $this->is_active ? 'active' : 'inactive';
    }

    /**
     * Check if this rate card is a template
     */
    public function isTemplate(): bool
    {
        return $this->is_template;
    }

    /**
     * Check if this rate card is an active template
     */
    public function isActiveTemplate(): bool
    {
        return $this->is_template && $this->is_active;
    }
}

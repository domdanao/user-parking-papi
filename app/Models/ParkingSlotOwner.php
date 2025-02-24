<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\RateCard;

class ParkingSlotOwner extends Authenticatable
{
    protected static function booted()
    {
        static::created(function ($owner) {
            // Create default rate card template for new owners
            RateCard::create([
                'parking_slot_owner_id' => $owner->id,
                'name' => 'Default Hourly Rate',
                'description' => 'Standard hourly rate for parking slots',
                'hour_block' => 1,
                'rate' => 5000, // ₱50.00
                'is_active' => true,
                'is_template' => true,
            ]);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_number',
        'business_name',
        'business_address',
        'payment_details',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_details' => 'json',
        'password' => 'hashed',
    ];

    /**
     * Get the slots owned by this parking slot owner.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class, 'parking_slot_owner_id');
    }

    /**
     * Get all rate cards created by this parking slot owner.
     */
    public function rateCards(): HasMany
    {
        return $this->hasMany(RateCard::class, 'parking_slot_owner_id');
    }

    /**
     * Get all rate card templates for this owner
     */
    public function rateCardTemplates(): HasMany
    {
        return $this->rateCards()->templates();
    }

    /**
     * Get rate card templates with their usage statistics
     */
    public function rateCardTemplatesWithStats(): Collection
    {
        return $this->rateCardTemplates()
            ->withCount(['slots as usage_count'])
            ->get()
            ->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'description' => $template->description,
                    'hour_block' => $template->hour_block,
                    'rate' => $template->rate,
                    'is_active' => $template->is_active,
                    'is_template' => $template->is_template,
                    'usage_count' => $template->usage_count,
                    'can_be_deactivated' => $template->canBeDeactivated(),
                    'can_be_deleted' => $template->canBeDeleted(),
                    'can_be_assigned' => $template->canBeAssigned(),
                    'status_message' => $template->getStatusMessage(),
                    'formatted_rate' => $template->getFormattedRate(),
                    'created_at' => $template->created_at,
                    'updated_at' => $template->updated_at,
                ];
            });
    }

    /**
     * Get slots with rate cards that need updating
     */
    public function getSlotsNeedingRateCardUpdate(): Collection
    {
        return $this->slots()
            ->whereHas('rateCard', function ($query) {
                $query->where('is_active', false)
                    ->orWhere(function ($query) {
                        $query->where('is_template', true)
                            ->whereColumn('rate_cards.updated_at', '>', 'slots.updated_at');
                    });
            })
            ->with('rateCard')
            ->get();
    }

    /**
     * Get only the active rate cards created by this parking slot owner.
     */
    public function activeRateCards(): HasMany
    {
        return $this->hasMany(RateCard::class, 'parking_slot_owner_id')
            ->where('is_active', true)
            ->where('is_template', false);
    }

    /**
     * Get assignable rate card templates (active templates)
     */
    public function assignableRateCardTemplates(): HasMany
    {
        return $this->rateCards()->activeTemplates();
    }
}

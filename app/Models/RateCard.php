<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Create a new rate card from this template
     */
    public function createFromTemplate(): self
    {
        if (!$this->is_template) {
            throw new \Exception('Can only create new rate cards from templates');
        }

        $newRateCard = self::create([
            'parking_slot_owner_id' => $this->parking_slot_owner_id,
            'name' => $this->name,
            'description' => $this->description,
            'hour_block' => $this->hour_block,
            'rate' => $this->rate,
            'is_active' => true,
            'is_template' => false,
        ]);

        return $newRateCard;
    }

    /**
     * Get the rate for a specific hour block
     */
    public function getRateForHour(int $hour): int
    {
        if ($hour === $this->hour_block) {
            return $this->rate;
        }
        return 0;
    }

    /**
     * Calculate the total rate for a given duration in hours
     */
    public function calculateTotalRate(float $duration): int
    {
        $fullHours = ceil($duration);
        return $this->rate * $fullHours;
    }
}

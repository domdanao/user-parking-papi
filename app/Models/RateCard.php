<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateCard extends Model
{
    protected $fillable = [
        'parking_slot_owner_id',
        'slot_id',
        'name',
        'description',
        'hour_block',
        'rate',
        'is_active',
    ];

    protected $casts = [
        'hour_block' => 'integer',
        'rate' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the owner of this rate card.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(ParkingSlotOwner::class, 'parking_slot_owner_id');
    }

    /**
     * Get the slot this rate card belongs to.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Get the rate for a specific hour block in a specific slot
     */
    public static function getRateForHour(int $hour, int $slotId): int
    {
        $rate = static::where('slot_id', $slotId)
            ->where('hour_block', $hour)
            ->where('is_active', true)
            ->first();
        
        // If no specific rate found for this hour, use the highest hour block rate for this slot
        if (!$rate) {
            $rate = static::where('slot_id', $slotId)
                ->where('is_active', true)
                ->orderByDesc('hour_block')
                ->first();
        }

        return $rate ? $rate->rate : 0;
    }

    /**
     * Calculate the total rate for a given duration in hours for a specific slot
     */
    public static function calculateTotalRate(float $duration, int $slotId): int
    {
        $totalRate = 0;
        $fullHours = ceil($duration);

        // Calculate rate for each hour
        for ($hour = 1; $hour <= $fullHours; $hour++) {
            $totalRate += static::getRateForHour($hour, $slotId);
        }

        return $totalRate;
    }
}

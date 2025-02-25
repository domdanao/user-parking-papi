<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Slot extends Model
{
    protected $fillable = [
        'parking_slot_owner_id',
        'identifier',
        'name',
        'location',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'location' => 'array',
    ];

    /**
     * Get the owner of this parking slot.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(ParkingSlotOwner::class, 'parking_slot_owner_id');
    }

    /**
     * Get the rate card for this slot.
     */
    public function rateCard(): BelongsTo
    {
        return $this->belongsTo(RateCard::class);
    }

    /**
     * Assign a rate card template to this slot
     */
    public function assignRateCardTemplate(RateCard $template): void
    {
        if (!$template->is_template) {
            throw new \Exception(__('Can only assign rate card templates'));
        }

        if (!$template->is_active) {
            throw new \Exception(__('Cannot assign inactive template'));
        }

        // If slot already has a rate card, delete it first
        if ($this->rate_card_id) {
            $oldRateCard = $this->rateCard;
            if ($oldRateCard) {
                $oldRateCard->delete();
            }
        }

        $newRateCard = $template->createFromTemplate();
        $this->rate_card_id = $newRateCard->id;
        $this->save();
    }

    /**
     * Check if this slot has a rate card assigned
     */
    public function hasRateCard(): bool
    {
        return $this->rate_card_id !== null;
    }

    /**
     * Check if this slot has an active rate card
     */
    public function hasActiveRateCard(): bool
    {
        return $this->hasRateCard() && $this->rateCard->is_active;
    }

    /**
     * Get the rate card status for this slot
     */
    public function getRateCardStatus(): string
    {
        if (!$this->hasRateCard()) {
            return 'none';
        }

        return $this->rateCard->is_active ? 'active' : 'inactive';
    }

    /**
     * Check if this slot needs a rate card update
     */
    public function needsRateCardUpdate(): bool
    {
        if (!$this->hasRateCard()) {
            return false;
        }

        // Slot needs update if rate card is inactive
        if (!$this->rateCard->is_active) {
            return true;
        }

        // Slot needs update if rate card is a template and has been updated
        if ($this->rateCard->is_template && $this->rateCard->updated_at > $this->updated_at) {
            return true;
        }

        return false;
    }

    /**
     * Get the rate card update status message
     */
    public function getRateCardUpdateMessage(): ?string
    {
        if (!$this->needsRateCardUpdate()) {
            return null;
        }

        if (!$this->rateCard->is_active) {
            return __('Rate card is inactive');
        }

        if ($this->rateCard->is_template && $this->rateCard->updated_at > $this->updated_at) {
            return __('Template has been updated');
        }

        return null;
    }

    /**
     * Get the formatted rate for display
     */
    public function getFormattedRate(): string
    {
        if (!$this->hasRateCard()) {
            return '-';
        }
        return '₱' . number_format($this->rateCard->rate / 100, 2) . ' / ' . $this->rateCard->hour_block . 'hr';
    }

    /**
     * Get the current rate for this slot
     */
    public function getCurrentRate(): int
    {
        return $this->rateCard?->rate ?? 0;
    }

    /**
     * Calculate parking fee for a duration
     */
    public function calculateParkingFee(float $duration): int
    {
        return $this->rateCard?->calculateTotalRate($duration) ?? 0;
    }

    /**
     * Calculate distance between two points using Haversine formula
     * @return float Distance in meters
     */
    private static function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000; // Earth's radius in meters

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get all parking sessions for this slot
     */
    public function parkingSessions(): HasMany
    {
        return $this->hasMany(ParkingSession::class);
    }

    /**
     * Check if the slot is available for parking
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if the slot is currently occupied
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Check if the slot is unavailable (e.g., under maintenance)
     */
    public function isUnavailable(): bool
    {
        return $this->status === 'unavailable';
    }

    /**
     * Get the active parking session for this slot, if any
     */
    public function getActiveParkingSession()
    {
        return $this->parkingSessions()
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    /**
     * Mark the slot as occupied
     */
    public function markAsOccupied(): void
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Slot is not available');
        }
        
        $this->update(['status' => 'occupied']);
    }

    /**
     * Mark the slot as available
     */
    public function markAsAvailable(): void
    {
        if ($this->isOccupied() && $this->getActiveParkingSession()) {
            throw new \Exception('Cannot mark as available: slot has active parking session');
        }
        
        $this->update(['status' => 'available']);
    }

    /**
     * Mark the slot as unavailable (e.g., for maintenance)
     */
    public function markAsUnavailable(): void
    {
        if ($this->isOccupied() && $this->getActiveParkingSession()) {
            throw new \Exception('Cannot mark as unavailable: slot has active parking session');
        }
        
        $this->update(['status' => 'unavailable']);
    }

    /**
     * Get distance from given coordinates in meters
     */
    public function getDistanceFrom(float $latitude, float $longitude): ?float
    {
        if (!$this->location || !isset($this->location['latitude']) || !isset($this->location['longitude'])) {
            return null;
        }

        return self::calculateDistance(
            $this->location['latitude'],
            $this->location['longitude'],
            $latitude,
            $longitude
        );
    }

    /**
     * Scope a query to find slots within a radius of coordinates
     * Note: This loads all slots and filters in PHP, consider pagination for large datasets
     */
    public function scopeNearby($query, float $latitude, float $longitude, float $radius = 1000)
    {
        return $query->get()->filter(function ($slot) use ($latitude, $longitude, $radius) {
            $distance = $slot->getDistanceFrom($latitude, $longitude);
            return $distance !== null && $distance <= $radius;
        });
    }

    /**
     * Scope a query to order by distance from coordinates
     * Note: This loads all slots and sorts in PHP, consider pagination for large datasets
     */
    public function scopeOrderByDistance($query, float $latitude, float $longitude): Collection
    {
        return $query->get()->sortBy(function ($slot) use ($latitude, $longitude) {
            return $slot->getDistanceFrom($latitude, $longitude) ?? PHP_FLOAT_MAX;
        });
    }

    /**
     * Scope a query to only include available slots
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include occupied slots
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    /**
     * Scope a query to only include unavailable slots
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', 'unavailable');
    }
}

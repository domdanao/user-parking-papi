<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ParkingSession extends Model
{
    protected $fillable = [
        'slot_id',
        'user_id',
        'wallet_id',
        'plate_number',
        'start_time',
        'end_time',
        'status',
        'total_amount',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_amount' => 'integer',
    ];

    protected $appends = [
        'remaining_time',
        'duration_hours',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($session) {
            if (!preg_match('/^[A-Z0-9\s]{3,20}$/', $session->plate_number)) {
                throw new \InvalidArgumentException('Invalid plate number format');
            }
        });
    }

    /**
     * Get the slot associated with this parking session
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /**
     * Get the user who created this parking session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet used for this parking session
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the wallet transactions associated with this parking session
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get the remaining time in seconds
     */
    public function getRemainingTimeAttribute(): ?int
    {
        if ($this->status !== 'active' || !$this->end_time) {
            return null;
        }
        return max(0, now()->diffInSeconds($this->end_time));
    }

    /**
     * Get the duration in hours (float)
     */
    public function getDurationHoursAttribute(): float
    {
        if (!$this->end_time) {
            return 0;
        }

        return $this->start_time->floatDiffInHours($this->end_time);
    }

    /**
     * Calculate the rate for the current duration
     */
    public function calculateRate(): int
    {
        return RateCard::calculateTotalRate($this->duration_hours);
    }

    /**
     * Extend the parking session
     */
    public function extend(Carbon $newEndTime): void
    {
        if ($this->status !== 'active') {
            throw new \Exception('Cannot extend a non-active parking session');
        }

        $oldDuration = $this->duration_hours;
        $this->end_time = $newEndTime;
        $newDuration = $this->duration_hours;

        $additionalCharge = RateCard::calculateTotalRate($newDuration) - RateCard::calculateTotalRate($oldDuration);

        if ($additionalCharge > 0) {
            $this->wallet->withdraw(
                $additionalCharge,
                "Extended parking session #{$this->id}",
                $this
            );
            $this->total_amount += $additionalCharge;
        }

        $this->save();
    }

    /**
     * Complete the parking session
     */
    public function complete(): void
    {
        if ($this->status !== 'active') {
            throw new \Exception('Cannot complete a non-active parking session');
        }

        $this->status = 'completed';
        $this->save();

        // Release the slot
        $this->slot->update(['status' => 'available']);
    }

    /**
     * Scope a query to only include active parking sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed parking sessions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}

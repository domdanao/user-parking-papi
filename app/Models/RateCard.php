<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateCard extends Model
{
    protected $fillable = [
        'hour_block',
        'rate',
    ];

    protected $casts = [
        'hour_block' => 'integer',
        'rate' => 'integer',
    ];

    /**
     * Get the rate for a specific hour block
     */
    public static function getRateForHour(int $hour): int
    {
        $rate = static::where('hour_block', $hour)->first();
        
        // If no specific rate found for this hour, use the rate for hour 9 (after 8th hour)
        if (!$rate) {
            $rate = static::where('hour_block', 9)->first();
        }

        return $rate ? $rate->rate : 0;
    }

    /**
     * Calculate the total rate for a given duration in hours
     */
    public static function calculateTotalRate(float $duration): int
    {
        $totalRate = 0;
        $fullHours = ceil($duration);

        // Calculate rate for each hour
        for ($hour = 1; $hour <= $fullHours; $hour++) {
            $totalRate += static::getRateForHour($hour);
        }

        return $totalRate;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingSlotOwner extends Model
{
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
        return $this->hasMany(Slot::class);
    }

    /**
     * Get the rate cards created by this parking slot owner.
     */
    public function rateCards(): HasMany
    {
        return $this->hasMany(RateCard::class);
    }
}

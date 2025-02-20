<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'reference',
        'parking_session_id',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    /**
     * The possible transaction types
     */
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($transaction) {
            if (!in_array($transaction->type, [self::TYPE_CREDIT, self::TYPE_DEBIT])) {
                throw new \InvalidArgumentException('Invalid transaction type');
            }

            if ($transaction->amount <= 0) {
                throw new \InvalidArgumentException('Transaction amount must be positive');
            }
        });
    }

    /**
     * Get the wallet that owns the transaction
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the parking session associated with this transaction
     */
    public function parkingSession(): BelongsTo
    {
        return $this->belongsTo(ParkingSession::class);
    }

    /**
     * Check if this is a credit transaction
     */
    public function isCredit(): bool
    {
        return $this->type === self::TYPE_CREDIT;
    }

    /**
     * Check if this is a debit transaction
     */
    public function isDebit(): bool
    {
        return $this->type === self::TYPE_DEBIT;
    }

    /**
     * Check if this transaction is related to parking
     */
    public function isParking(): bool
    {
        return !is_null($this->parking_session_id);
    }

    /**
     * Get formatted amount with sign
     */
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->isCredit() ? '+' : '-';
        return "{$sign}₱{$this->amount}";
    }

    /**
     * Scope a query to only include credit transactions
     */
    public function scopeCredits($query)
    {
        return $query->where('type', self::TYPE_CREDIT);
    }

    /**
     * Scope a query to only include debit transactions
     */
    public function scopeDebits($query)
    {
        return $query->where('type', self::TYPE_DEBIT);
    }

    /**
     * Scope a query to only include parking transactions
     */
    public function scopeParking($query)
    {
        return $query->whereNotNull('parking_session_id');
    }

    /**
     * Scope a query to only include non-parking transactions
     */
    public function scopeNonParking($query)
    {
        return $query->whereNull('parking_session_id');
    }

    /**
     * Scope a query to order by most recent first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

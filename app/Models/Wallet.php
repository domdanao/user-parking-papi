<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    /**
     * Get the user that owns the wallet
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for this wallet
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get the parking sessions paid through this wallet
     */
    public function parkingSessions(): HasMany
    {
        return $this->hasMany(ParkingSession::class);
    }

    /**
     * Check if wallet has sufficient balance
     */
    public function hasSufficientBalance(int $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Add funds to the wallet
     * @throws \Exception if amount is negative
     */
    public function deposit(int $amount, string $reference): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deposit amount must be positive');
        }

        return DB::transaction(function () use ($amount, $reference) {
            $this->balance += $amount;
            $this->save();

            return $this->transactions()->create([
                'amount' => $amount,
                'type' => 'credit',
                'reference' => $reference,
            ]);
        });
    }

    /**
     * Remove funds from the wallet
     * @throws \Exception if insufficient balance or negative amount
     */
    public function withdraw(int $amount, string $reference, ?ParkingSession $parkingSession = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Withdrawal amount must be positive');
        }

        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient funds');
        }

        return DB::transaction(function () use ($amount, $reference, $parkingSession) {
            $this->balance -= $amount;
            $this->save();

            return $this->transactions()->create([
                'amount' => $amount,
                'type' => 'debit',
                'reference' => $reference,
                'parking_session_id' => $parkingSession?->id,
            ]);
        });
    }

    /**
     * Get total credits (deposits)
     */
    public function getTotalCredits(): int
    {
        return $this->transactions()
            ->where('type', 'credit')
            ->sum('amount');
    }

    /**
     * Get total debits (withdrawals)
     */
    public function getTotalDebits(): int
    {
        return $this->transactions()
            ->where('type', 'debit')
            ->sum('amount');
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 10)
    {
        return $this->transactions()
            ->with('parkingSession')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get active parking sessions
     */
    public function getActiveParkingSessions()
    {
        return $this->parkingSessions()
            ->where('status', 'active')
            ->get();
    }

    /**
     * Calculate total spent on parking
     */
    public function getTotalSpentOnParking(): int
    {
        return $this->transactions()
            ->whereNotNull('parking_session_id')
            ->where('type', 'debit')
            ->sum('amount');
    }
}

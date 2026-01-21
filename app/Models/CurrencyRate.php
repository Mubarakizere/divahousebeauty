<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency',
        'target_currency',
        'exchange_rate',
        'last_updated',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:10',
        'last_updated' => 'datetime',
    ];

    /**
     * Get exchange rate from base to target currency
     */
    public static function getRate(string $baseCurrency, string $targetCurrency): ?float
    {
        if ($baseCurrency === $targetCurrency) {
            return 1.0;
        }

        $rate = self::where('base_currency', $baseCurrency)
            ->where('target_currency', $targetCurrency)
            ->first();

        return $rate ? (float) $rate->exchange_rate : null;
    }

    /**
     * Check if rates are stale (older than 24 hours)
     */
    public static function areRatesStale(): bool
    {
        $latestRate = self::orderBy('last_updated', 'desc')->first();
        
        if (!$latestRate) {
            return true;
        }

        return $latestRate->last_updated->diffInHours(now()) > 24;
    }
}

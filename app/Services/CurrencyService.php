<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    private const API_URL = 'https://v6.exchangerate-api.com/v6/';
    private const SUPPORTED_CURRENCIES = ['USD', 'EUR', 'KES', 'RWF'];
    private const BASE_CURRENCY = 'RWF';

    /**
     * Fetch latest exchange rates from API
     */
    public function fetchRatesFromAPI(): array
    {
        $apiKey = config('services.exchange_rate.api_key');
        
        if (!$apiKey) {
            Log::error('Exchange Rate API key not configured');
            return [];
        }

        try {
            $response = Http::timeout(10)->get(self::API_URL . $apiKey . '/latest/' . self::BASE_CURRENCY);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['conversion_rates'])) {
                    return $data['conversion_rates'];
                }
            }

            Log::error('Failed to fetch exchange rates', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Exception fetching exchange rates: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update cached rates in database
     */
    public function updateCachedRates(): bool
    {
        $rates = $this->fetchRatesFromAPI();

        if (empty($rates)) {
            Log::warning('No rates fetched from API, keeping existing cache');
            return false;
        }

        $updated = 0;
        $timestamp = now();

        foreach (self::SUPPORTED_CURRENCIES as $currency) {
            if ($currency === self::BASE_CURRENCY) {
                continue; // Skip base currency
            }

            if (isset($rates[$currency])) {
                CurrencyRate::updateOrCreate(
                    [
                        'base_currency' => self::BASE_CURRENCY,
                        'target_currency' => $currency,
                    ],
                    [
                        'exchange_rate' => $rates[$currency],
                        'last_updated' => $timestamp,
                    ]
                );
                $updated++;
            }
        }

        // Clear cache
        Cache::forget('currency_rates');

        Log::info("Updated $updated currency rates");
        return true;
    }

    /**
     * Get exchange rate between two currencies
     */
    public function getExchangeRate(string $from, string $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        // If converting from base currency
        if ($from === self::BASE_CURRENCY) {
            return CurrencyRate::getRate($from, $to) ?? 1.0;
        }

        // If converting to base currency
        if ($to === self::BASE_CURRENCY) {
            $rate = CurrencyRate::getRate(self::BASE_CURRENCY, $from);
            return $rate ? (1 / $rate) : 1.0;
        }

        // Cross conversion (e.g., USD to EUR)
        $fromRateToBase = CurrencyRate::getRate(self::BASE_CURRENCY, $from);
        $toRateToBase = CurrencyRate::getRate(self::BASE_CURRENCY, $to);

        if ($fromRateToBase && $toRateToBase) {
            return $toRateToBase / $fromRateToBase;
        }

        return 1.0;
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return $amount * $rate;
    }

    /**
     * Get all current rates for supported currencies
     */
    public function getAllRates(): array
    {
        return Cache::remember('currency_rates', 3600, function () {
            $rates = [];
            
            foreach (self::SUPPORTED_CURRENCIES as $currency) {
                if ($currency !== self::BASE_CURRENCY) {
                    $rates[$currency] = CurrencyRate::getRate(self::BASE_CURRENCY, $currency) ?? 0;
                }
            }
            
            $rates[self::BASE_CURRENCY] = 1.0;

            // Fallback for KES if not in DB/API
            if (empty($rates['KES'])) {
                $rates['KES'] = 0.10; // Approx 1 RWF = 0.10 KES (1 KES = 10 RWF)
            }
            
            return $rates;
        });
    }

    /**
     * Get supported currencies with metadata
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'US Dollar',
                'flag' => '🇺🇸',
                'decimals' => 2,
            ],
            'EUR' => [
                'code' => 'EUR',
                'symbol' => '€',
                'name' => 'Euro',
                'flag' => '🇪🇺',
                'decimals' => 2,
            ],
            'KES' => [
                'code' => 'KES',
                'symbol' => 'KSh',
                'name' => 'Kenyan Shilling',
                'flag' => '🇰🇪',
                'decimals' => 0,
            ],
            'RWF' => [
                'code' => 'RWF',
                'symbol' => 'RWF',
                'name' => 'Rwandan Franc',
                'flag' => '🇷🇼',
                'decimals' => 0,
            ],
        ];
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(string $currency): string
    {
        $currencies = $this->getSupportedCurrencies();
        return $currencies[$currency]['symbol'] ?? $currency;
    }

    /**
     * Check if rates need updating
     */
    public function shouldUpdateRates(): bool
    {
        return CurrencyRate::areRatesStale();
    }
}

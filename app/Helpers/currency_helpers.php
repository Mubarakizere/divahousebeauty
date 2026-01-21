<?php

use App\Services\CurrencyService;

if (!function_exists('convert_currency')) {
    /**
     * Convert amount from one currency to another
     */
    function convert_currency(float $amount, string $from = 'RWF', string $to = 'USD'): float
    {
        $service = app(CurrencyService::class);
        return $service->convert($amount, $from, $to);
    }
}

if (!function_exists('format_price')) {
    /**
     * Format price with currency symbol
     */
    function format_price(float $amount, string $currency = 'USD', bool $convertFromRWF = false): string
    {
        $service = app(CurrencyService::class);
        
        // Convert from RWF if needed
        if ($convertFromRWF && $currency !== 'RWF') {
            $amount = $service->convert($amount, 'RWF', $currency);
        }
        
        $currencies = $service->getSupportedCurrencies();
        $currencyData = $currencies[$currency] ?? ['symbol' => $currency, 'decimals' => 2];
        
        $symbol = $currencyData['symbol'];
        $decimals = $currencyData['decimals'];
        
        $formatted = number_format($amount, $decimals);
        
        // Format based on currency
        if ($currency === 'RWF') {
            return "RWF {$formatted}";
        }
        
        return "{$symbol}{$formatted}";
    }
}

if (!function_exists('get_currency_symbol')) {
    /**
     * Get currency symbol
     */
    function get_currency_symbol(string $currency): string
    {
        $service = app(CurrencyService::class);
        return $service->getCurrencySymbol($currency);
    }
}

if (!function_exists('get_currency_data')) {
    /**
     * Get full currency data
     */
    function get_currency_data(string $currency): array
    {
        $service = app(CurrencyService::class);
        $currencies = $service->getSupportedCurrencies();
        return $currencies[$currency] ?? [];
    }
}

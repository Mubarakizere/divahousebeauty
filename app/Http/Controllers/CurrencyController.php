<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Get current exchange rates
     */
    public function getRates(): JsonResponse
    {
        $rates = $this->currencyService->getAllRates();
        $currencies = $this->currencyService->getSupportedCurrencies();

        return response()->json([
            'success' => true,
            'base_currency' => 'RWF',
            'rates' => $rates,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Convert amount between currencies
     */
    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $amount = (float) $request->input('amount');
        $from = strtoupper($request->input('from'));
        $to = strtoupper($request->input('to'));

        $converted = $this->currencyService->convert($amount, $from, $to);
        $formatted = format_price($converted, $to);

        return response()->json([
            'success' => true,
            'original' => [
                'amount' => $amount,
                'currency' => $from,
            ],
            'converted' => [
                'amount' => $converted,
                'currency' => $to,
                'formatted' => $formatted,
            ],
            'rate' => $this->currencyService->getExchangeRate($from, $to),
        ]);
    }
}

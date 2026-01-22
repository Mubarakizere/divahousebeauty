<?php

use App\Models\CurrencyRate;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Manually set KES rate (approximate: 1 RWF = 0.1 KES)
    // Adjust this value as needed. 1 KES ~ 10 RWF. So 1 RWF = 0.1 KES.
    $kesRate = 0.1; 
    
    CurrencyRate::updateOrCreate(
        [
            'base_currency' => 'RWF',
            'target_currency' => 'KES',
        ],
        [
            'exchange_rate' => $kesRate,
            'last_updated' => now(),
        ]
    );

    echo "Successfully seeded KES rate: $kesRate\n";
    
    // Clear cache to ensure new rate is picked up
    Illuminate\Support\Facades\Cache::forget('currency_rates');
    echo "Cleared currency_rates cache.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

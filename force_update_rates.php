<?php

use App\Services\CurrencyService;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $service = app(CurrencyService::class);
    echo "Updating rates...\n";
    $success = $service->updateCachedRates();
    
    if ($success) {
        echo "Successfully updated rates.\n";
        $rates = $service->getAllRates();
        print_r($rates);
    } else {
        echo "Failed to update rates.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

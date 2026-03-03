<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

echo "--- OCR Diagnostic ---\n";

// 1. Check Google Vision Key
$key = config('services.google_vision.api_key');
echo "Google Vision Key: " . ($key ? substr($key, 0, 5) . '...' : 'MISSING') . "\n";

if ($key) {
    echo "Testing Google Vision API...\n";
    try {
        // Create a simple blank image base64 for testing
        $imageContent = base64_encode(file_get_contents(__DIR__ . '/public/favicon.ico')); // Use any small file
        
        $response = Http::post(
            "https://vision.googleapis.com/v1/images:annotate?key={$key}",
            [
                'requests' => [
                    [
                        'image' => ['content' => $imageContent],
                        'features' => [['type' => 'TEXT_DETECTION', 'maxResults' => 1]],
                    ],
                ],
            ]
        );
        
        echo "Status: " . $response->status() . "\n";
        if (!$response->successful()) {
            echo "Error: " . $response->body() . "\n";
        } else {
            echo "Success! API is working.\n";
        }
    } catch (\Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
} else {
    echo "Skipping Google Vision test (Key missing).\n";
}

// 2. Check Tesseract
echo "\nChecking Tesseract...\n";
$commonPaths = [
    'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
    'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
];

$found = false;
foreach ($commonPaths as $path) {
    if (file_exists($path)) {
        echo "Found Tesseract at: $path\n";
        $found = true;
        break;
    }
}

if (!$found) {
    // Check PATH
    exec('where tesseract', $output, $return);
    if ($return === 0) {
        echo "Found Tesseract in PATH: " . $output[0] . "\n";
    } else {
        echo "Tesseract NOT found on system.\n";
    }
}

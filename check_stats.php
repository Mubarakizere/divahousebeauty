<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Counts:\n";
echo "Categories: " . \App\Models\Category::count() . "\n";
echo "Brands: " . \App\Models\Brand::count() . "\n";
echo "Products: " . \App\Models\Product::count() . "\n";

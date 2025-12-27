<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbName = config('database.connections.mysql.database'); // Assuming mysql as default
echo "Connected to DB: " . $dbName . "\n";

$filePath = database_path('data/legacy_export.json');
if (!File::exists($filePath)) {
    die("File not found: $filePath\n");
}

$jsonContent = File::get($filePath);
$data = json_decode($jsonContent, true);

if (!$data) {
    die("Invalid JSON\n");
}

// Disable FKs for this script
Schema::disableForeignKeyConstraints();

foreach ($data as $item) {
    if (!isset($item['type']) || $item['type'] !== 'table') {
        continue;
    }

    $tableName = $item['name'];
    $rows = $item['data'] ?? [];
    
    if (empty($rows)) continue;

    if (!Schema::hasTable($tableName)) {
        echo "Table $tableName skipped.\n";
        continue;
    }

    $tableColumns = Schema::getColumnListing($tableName);
    echo "\nProcessing $tableName (" . count($rows) . " rows)...\n";
    echo "Current count before: " . DB::table($tableName)->count() . "\n";

    try {
        $chunks = array_chunk($rows, 500);
        foreach ($chunks as $chunk) {
            $preparedData = [];
            foreach ($chunk as $row) {
                $cleanRow = [];
                foreach ($row as $key => $value) {
                    if (in_array($key, $tableColumns)) {
                        $cleanRow[$key] = $value;
                    }
                }
                $preparedData[] = $cleanRow;
            }
            
            DB::table($tableName)->upsert($preparedData, ['id'], $tableColumns);
        }
        echo "Inserted/Upserted.\n";
        echo "Current count after: " . DB::table($tableName)->count() . "\n";
        
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

Schema::enableForeignKeyConstraints();
echo "\nFinal Counts:\n";
echo "Categories: " . \App\Models\Category::count() . "\n";
echo "Brands: " . \App\Models\Brand::count() . "\n";
echo "Products: " . \App\Models\Product::count() . "\n";

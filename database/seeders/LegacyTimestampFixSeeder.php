<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class LegacyTimestampFixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('data/legacy_export.json');

        if (!File::exists($filePath)) {
            $this->command->error("File not found: $filePath");
            return;
        }

        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (!$data) {
            $this->command->error("Invalid JSON format in $filePath");
            return;
        }

        $this->command->info('Starting legacy timestamp restoration...');

        foreach ($data as $item) {
            if (!isset($item['type']) || $item['type'] !== 'table' || $item['name'] !== 'products') {
                continue;
            }

            $rows = $item['data'] ?? [];
            $count = 0;

            foreach ($rows as $row) {
                if (!isset($row['id'])) {
                    continue;
                }

                $createdAt = $row['created_at'] ?? '2024-01-01 00:00:00'; // Default to old date if missing
                
                // If created_at is null in JSON, use fallback old date
                if (is_null($createdAt)) {
                     $createdAt = '2024-01-01 00:00:00';
                }

                DB::table('products')
                    ->where('id', $row['id'])
                    ->update(['created_at' => $createdAt]);
                
                $count++;
            }
            
            $this->command->info("Restored timestamps for $count products.");
        }

        $this->command->info('Legacy timestamp restoration completed!');
    }
}

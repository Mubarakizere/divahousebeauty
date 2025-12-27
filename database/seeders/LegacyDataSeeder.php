<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LegacyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('data/legacy_export.json');

        if (!File::exists($filePath)) {
            $this->command->error("File not found: $filePath");
            $this->command->info("Please save your JSON export to 'database/data/legacy_export.json'");
            return;
        }

        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (!$data) {
            $this->command->error("Invalid JSON format in $filePath");
            return;
        }

        // Disable foreign key checks to prevent issues with order of insertion
        Schema::disableForeignKeyConstraints();

        $this->command->info('Starting legacy data import...');

        foreach ($data as $item) {
            if (!isset($item['type']) || $item['type'] !== 'table') {
                continue;
            }

            $tableName = $item['name'];
            $rows = $item['data'] ?? [];

            if (empty($rows)) {
                $this->command->warn("No data found for table: $tableName");
                continue;
            }

            $this->command->info("Importing $tableName (" . count($rows) . " rows)...");

            // Determine if the table exists in our new schema
            if (!Schema::hasTable($tableName)) {
                $this->command->warn("Table '$tableName' does not exist in the new database. Skipping.");
                continue;
            }

            // Get columns of the target table to avoid inserting unknown columns
            $tableColumns = Schema::getColumnListing($tableName);

            // Chunk inserts for performance
            $chunks = array_chunk($rows, 500);

            foreach ($chunks as $chunk) {
                $preparedData = [];
                foreach ($chunk as $row) {
                    $cleanRow = [];
                    foreach ($row as $key => $value) {
                        if (in_array($key, $tableColumns)) {
                             // Handle JSON fields if strictly needed, but usually DB::table()->insert handles stringified JSON fine if the column is JSON type
                             // However, if the source 'images' is a string like "[\"path/to/img\"]", we might want to keep it as is if it's already a valid JSON string.
                             // But wait, json_decode parses the outer JSON, so "images": "[\"foo\"]" becomes a string '["foo"]'.
                             // This IS the correct format to insert into a JSON column.
                            $cleanRow[$key] = $value;
                        }
                    }
                    $preparedData[] = $cleanRow;
                }

                // Use upsert to handle potential duplicates if ID exists, or just insertIgnore
                // Using upsert with 'id' as unique key
                DB::table($tableName)->upsert(
                    $preparedData, 
                    ['id'], 
                    $tableColumns // Update all columns if id exists
                );
            }
        }

        Schema::enableForeignKeyConstraints();
        $this->command->info('Legacy data import completed successfully!');
    }
}

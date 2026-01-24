<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public int $successCount = 0;
    public int $errorCount = 0;
    public array $errors = [];

    /**
     * Process the collection of rows.
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 for header row and 0-index

            try {
                // Get name from various possible column names
                $name = $this->getColumnValue($row, ['name', 'product_name', 'product', 'title', 'product_title']);
                
                // Skip empty rows
                if (empty($name)) {
                    $this->errorCount++;
                    $this->errors[] = "Row {$rowNumber}: Product name is empty or missing";
                    continue;
                }

                // Find or create category
                $category = null;
                $categoryName = $this->getColumnValue($row, ['category', 'category_name', 'cat']);
                if (!empty($categoryName)) {
                    $category = Category::firstOrCreate(
                        ['name' => trim($categoryName)],
                        ['slug' => Str::slug($categoryName)]
                    );
                }

                // Find or create brand
                $brand = null;
                $brandName = $this->getColumnValue($row, ['brand', 'brand_name']);
                if (!empty($brandName)) {
                    $brand = Brand::firstOrCreate(
                        ['name' => trim($brandName)],
                        [
                            'slug' => Str::slug($brandName),
                            'category_id' => $category?->id,
                        ]
                    );
                }

                // Determine shipping type
                $shippingType = strtolower(trim($this->getColumnValue($row, ['shipping_type', 'shipping']) ?? 'express_only'));
                if (!in_array($shippingType, ['both', 'express_only', 'standard_only'])) {
                    $shippingType = 'express_only';
                }

                // Parse prices - try multiple column names
                $expressPrice = $this->parsePrice($this->getColumnValue($row, ['express_price', 'price', 'unit_price', 'cost']));
                $standardPrice = $this->parsePrice($this->getColumnValue($row, ['standard_price', 'standard']));

                // Get stock
                $stock = (int) ($this->getColumnValue($row, ['stock', 'quantity', 'qty', 'inventory']) ?? 0);

                // Get description
                $description = $this->getColumnValue($row, ['description', 'desc', 'details', 'product_description']) ?? '';

                // Create product
                Product::create([
                    'name' => trim($name),
                    'slug' => Str::slug($name) . '-' . uniqid(),
                    'description' => trim($description),
                    'express_price' => $expressPrice ?: 0,
                    'standard_price' => $standardPrice ?: null,
                    'shipping_type' => $shippingType,
                    'stock' => $stock,
                    'category_id' => $category?->id,
                    'brand_id' => $brand?->id,
                    'images' => [], // Images uploaded later
                ]);

                $this->successCount++;
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }
    }

    /**
     * Get value from row using multiple possible column names.
     */
    private function getColumnValue($row, array $possibleNames): ?string
    {
        foreach ($possibleNames as $name) {
            // Try exact match
            if (isset($row[$name]) && !empty($row[$name])) {
                return (string) $row[$name];
            }
            
            // Try lowercase
            $lower = strtolower($name);
            if (isset($row[$lower]) && !empty($row[$lower])) {
                return (string) $row[$lower];
            }
            
            // Try with underscores replaced by spaces
            $spaced = str_replace('_', ' ', $name);
            if (isset($row[$spaced]) && !empty($row[$spaced])) {
                return (string) $row[$spaced];
            }
        }
        
        return null;
    }

    /**
     * Parse price value (handle commas, currency symbols, etc.)
     */
    private function parsePrice($value): float
    {
        if (empty($value)) {
            return 0;
        }

        // Remove currency symbols, commas, and spaces
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        return (float) $cleaned;
    }
}

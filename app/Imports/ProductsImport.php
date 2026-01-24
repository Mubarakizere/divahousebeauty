<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation
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
                // Skip empty rows
                if (empty($row['name'])) {
                    continue;
                }

                // Find or create category
                $category = null;
                if (!empty($row['category'])) {
                    $category = Category::firstOrCreate(
                        ['name' => trim($row['category'])],
                        ['slug' => Str::slug($row['category'])]
                    );
                }

                // Find or create brand
                $brand = null;
                if (!empty($row['brand'])) {
                    $brand = Brand::firstOrCreate(
                        ['name' => trim($row['brand'])],
                        [
                            'slug' => Str::slug($row['brand']),
                            'category_id' => $category?->id,
                        ]
                    );
                }

                // Determine shipping type
                $shippingType = strtolower(trim($row['shipping_type'] ?? 'express_only'));
                if (!in_array($shippingType, ['both', 'express_only', 'standard_only'])) {
                    $shippingType = 'express_only';
                }

                // Parse prices
                $expressPrice = $this->parsePrice($row['express_price'] ?? $row['price'] ?? 0);
                $standardPrice = $this->parsePrice($row['standard_price'] ?? 0);

                // Create product
                Product::create([
                    'name' => trim($row['name']),
                    'slug' => Str::slug($row['name']) . '-' . uniqid(),
                    'description' => trim($row['description'] ?? ''),
                    'express_price' => $expressPrice,
                    'standard_price' => $standardPrice ?: null,
                    'shipping_type' => $shippingType,
                    'stock' => (int) ($row['stock'] ?? 0),
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
     * Parse price value (handle commas, currency symbols, etc.)
     */
    private function parsePrice($value): float
    {
        if (empty($value)) {
            return 0;
        }

        // Remove currency symbols and commas
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        return (float) $cleaned;
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'express_price' => 'nullable|numeric|min:0',
            'standard_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'express_price.numeric' => 'Express price must be a number.',
            'standard_price.numeric' => 'Standard price must be a number.',
        ];
    }
}

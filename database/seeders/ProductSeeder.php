<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Create or get category
        $category = Category::firstOrCreate(
            ['slug' => 'skincare'],
            ['name' => 'Skincare', 'description' => 'Premium skincare products']
        );

        // Create or get brand
        $brand = Brand::firstOrCreate(
            ['slug' => 'diva-beauty'],
            ['name' => 'Diva Beauty', 'description' => 'Luxury beauty products']
        );

        // Define 5 products
        $products = [
            [
                'name' => 'Vitamin C Brightening Serum',
                'description' => 'A powerful brightening serum with 20% Vitamin C that reduces dark spots, evens skin tone, and promotes collagen production for a youthful, radiant glow. Suitable for all skin types.',
                'price' => 45000,
                'stock' => 50,
            ],
            [
                'name' => 'Hydrating Face Cream with Hyaluronic Acid',
                'description' => 'Ultra-moisturizing face cream enriched with hyaluronic acid and ceramides. Provides 24-hour hydration, plumps fine lines, and strengthens the skin barrier. Perfect for dry and sensitive skin.',
                'price' => 38000,
                'stock' => 40,
            ],
            [
                'name' => 'Gentle Cleansing Foam',
                'description' => 'pH-balanced foaming cleanser that effectively removes makeup, dirt, and excess oil without stripping natural moisture. Infused with green tea extract and aloe vera for soothing benefits.',
                'price' => 28000,
                'stock' => 60,
            ],
            [
                'name' => 'Anti-Aging Night Cream with Retinol',
                'description' => 'Rich, nourishing night cream formulated with retinol and peptides to reduce wrinkles, improve skin texture, and boost cell renewal while you sleep. Wake up to smoother, firmer skin.',
                'price' => 55000,
                'stock' => 30,
            ],
            [
                'name' => 'Exfoliating Toner - AHA/BHA Complex',
                'description' => 'Gentle chemical exfoliant with alpha and beta hydroxy acids that unclogs pores, removes dead skin cells, and evens skin tone. Use 2-3 times per week for best results. Alcohol-free formula.',
                'price' => 32000,
                'stock' => 45,
            ],
        ];

        // Create products
        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']], // Check by name to avoid duplicates
                [
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'images' => ['assets/images/default-product.jpg'], // Will be auto-cast to JSON
                ]
            );
        }

        $this->command->info('✅ Product seeding completed!');
        $this->command->info('📦 Created 5 skincare products');
        $this->command->info('📁 Category: Skincare');
        $this->command->info('🏷️  Brand: Diva Beauty');
        $this->command->info('');
        $this->command->info('🔗 Visit /category/skincare to see the products!');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ReviewTestSeeder extends Seeder
{
    public function run()
    {
        // Create test category
        $category = Category::firstOrCreate(
            ['slug' => 'skincare'],
            ['name' => 'Skincare', 'description' => 'Skincare products']
        );

        // Create test brand
        $brand = Brand::firstOrCreate(
            ['slug' => 'diva-beauty'],
            ['name' => 'Diva Beauty', 'description' => 'Premium beauty products']
        );

        // Create test customer role if it doesn't exist
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create 3 test users (they can log in to leave reviews)
        $users = [];
        for ($i = 1; $i <= 3; $i++) {
            $user = User::firstOrCreate(
                ['email' => "customer{$i}@test.com"],
                [
                    'name' => "Test Customer {$i}",
                    'password' => Hash::make('password'),
                ]
            );
            if ($user->role !== 'customer') {
                $user->assignRole('customer');
            }
            $users[] = $user;
        }

        // Create 5 test products
        $products = [
            [
                'name' => 'Vitamin C Serum',
                'description' => 'Brighten your skin with our powerful Vitamin C serum. Reduces dark spots and promotes collagen production for a youthful glow.',
                'price' => 45000,
                'stock' => 50,
            ],
            [
                'name' => 'Hydrating Face Cream',
                'description' => 'Ultra-moisturizing face cream with hyaluronic acid. Perfect for dry skin, provides 24-hour hydration and plumps fine lines.',
                'price' => 38000,
                'stock' => 40,
            ],
            [
                'name' => 'Gentle Cleansing Foam',
                'description' => 'pH-balanced cleansing foam that removes makeup and impurities without stripping natural oils. Suitable for all skin types.',
                'price' => 28000,
                'stock' => 60,
            ],
            [
                'name' => 'Anti-Aging Night Cream',
                'description' => 'Rich night cream with retinol and peptides. Works overnight to reduce wrinkles and improve skin texture.',
                'price' => 55000,
                'stock' => 30,
            ],
            [
                'name' => 'Exfoliating Toner',
                'description' => 'AHA/BHA toner that gently exfoliates dead skin cells, unclogs pores, and evens skin tone. Use 2-3 times per week.',
                'price' => 32000,
                'stock' => 45,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['name' => $productData['name']],
                [
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'images' => json_encode(['assets/images/default-product.jpg']),
                ]
            );

            // Create sample reviews with different ratings
            $reviewTexts = [
                [
                    'rating' => 5,
                    'title' => 'Absolutely love this product!',
                    'review' => 'This is hands down the best product I\'ve ever used. The results are visible within just a few days. Highly recommend to everyone!',
                ],
                [
                    'rating' => 4,
                    'title' => 'Great product, minor issue',
                    'review' => 'Works really well and I can see the difference. Only giving 4 stars because the packaging could be better. Otherwise, excellent quality!',
                ],
                [
                    'rating' => 5,
                    'title' => 'Worth every penny',
                    'review' => 'I was skeptical at first, but this product exceeded my expectations. My skin feels amazing and looks noticeably better.',
                ],
            ];

            // Add 1-3 reviews per product for variety
            $reviewCount = rand(1, 3);
            for ($i = 0; $i < $reviewCount; $i++) {
                $reviewData = $reviewTexts[$i];
                $user = $users[$i];

                // Check if review already exists
                $existingReview = Review::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();

                if (!$existingReview) {
                    Review::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'rating' => $reviewData['rating'],
                        'title' => $reviewData['title'],
                        'review' => $reviewData['review'],
                        'verified_purchase' => rand(0, 1) == 1, // Random verified status
                        'status' => 'approved',
                        'created_at' => now()->subDays(rand(1, 20)),
                    ]);
                }
            }
        }

        $this->command->info('✅ Test data created successfully!');
        $this->command->info('📦 Created 5 products with reviews');
        $this->command->info('👥 Created 3 test customers:');
        $this->command->info('   - customer1@test.com (password: password)');
        $this->command->info('   - customer2@test.com (password: password)');
        $this->command->info('   - customer3@test.com (password: password)');
        $this->command->info('⭐ Each product has 1-3 approved reviews');
        $this->command->info('');
        $this->command->info('🔗 Visit your site and browse the Skincare category to see the products!');
    }
}

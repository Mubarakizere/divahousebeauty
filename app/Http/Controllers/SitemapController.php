<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap for search engines
     */
    public function index()
    {
        // Cache sitemap for 24 hours to improve performance
        $sitemap = Cache::remember('sitemap_xml', 86400, function () {
            return $this->generateSitemap();
        });

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap XML content
     */
    private function generateSitemap()
    {
        try {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
            $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

            // Homepage - highest priority
            $xml .= $this->addUrl(route('home'), now(), 'daily', '1.0');

            // Static pages (with error handling)
            try {
                if (\Route::has('about')) {
                    $xml .= $this->addUrl(route('about'), now()->subDays(30), 'monthly', '0.7');
                }
                if (\Route::has('contact')) {
                    $xml .= $this->addUrl(route('contact'), now()->subDays(30), 'monthly', '0.6');
                }
                if (\Route::has('deals')) {
                    $xml .= $this->addUrl(route('deals'), now(), 'daily', '0.8');
                }
            } catch (\Exception $e) {
                \Log::warning('Sitemap: Error adding static pages: ' . $e->getMessage());
            }

            // Categories - high priority
            try {
                $categoryQuery = Category::query();
                
                // Check if is_active column exists
                if (\Schema::hasColumn('categories', 'is_active')) {
                    $categoryQuery->where('is_active', true);
                }
                
                $categoryQuery->each(function ($category) use (&$xml) {
                    try {
                        $xml .= $this->addUrl(
                            route('category.show', $category->slug),
                            $category->updated_at ?? now(),
                            'weekly',
                            '0.9'
                        );
                    } catch (\Exception $e) {
                        \Log::warning('Sitemap: Error adding category ' . $category->id . ': ' . $e->getMessage());
                    }
                });
            } catch (\Exception $e) {
                \Log::warning('Sitemap: Error processing categories: ' . $e->getMessage());
            }

            // Brands - medium-high priority
            try {
                Brand::whereHas('products')->each(function ($brand) use (&$xml) {
                    try {
                        $xml .= $this->addUrl(
                            route('brand.show', $brand->slug),
                            $brand->updated_at ?? now(),
                            'weekly',
                            '0.8'
                        );
                    } catch (\Exception $e) {
                        \Log::warning('Sitemap: Error adding brand ' . $brand->id . ': ' . $e->getMessage());
                    }
                });
            } catch (\Exception $e) {
                \Log::warning('Sitemap: Error processing brands: ' . $e->getMessage());
            }

            // Products - medium priority
            try {
                $productQuery = Product::query();
                
                // Only add filters if columns exist
                if (\Schema::hasColumn('products', 'is_active')) {
                    $productQuery->where('is_active', true);
                }
                if (\Schema::hasColumn('products', 'in_stock')) {
                    $productQuery->where('in_stock', true);
                } elseif (\Schema::hasColumn('products', 'stock')) {
                    $productQuery->where('stock', '>', 0);
                }
                
                $productQuery->orderBy('updated_at', 'desc')
                    ->limit(500) // Limit to prevent memory issues
                    ->each(function ($product) use (&$xml) {
                        try {
                            $xml .= $this->addProductUrl($product);
                        } catch (\Exception $e) {
                            \Log::warning('Sitemap: Error adding product ' . $product->id . ': ' . $e->getMessage());
                        }
                    });
            } catch (\Exception $e) {
                \Log::warning('Sitemap: Error processing products: ' . $e->getMessage());
            }

            $xml .= '</urlset>';

            return $xml;
            
        } catch (\Exception $e) {
            \Log::error('Sitemap generation failed: ' . $e->getMessage());
            
            // Return minimal valid sitemap on error
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $xml .= '<url><loc>' . route('home') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>';
            $xml .= '</urlset>';
            
            return $xml;
        }
    }

    /**
     * Add a standard URL to sitemap
     */
    private function addUrl($loc, $lastmod = null, $changefreq = 'weekly', $priority = '0.5')
    {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars($loc) . '</loc>';
        
        if ($lastmod) {
            $xml .= '<lastmod>' . $lastmod->format('Y-m-d') . '</lastmod>';
        }
        
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';

        return $xml;
    }

    /**
     * Add product URL with image information
     */
    private function addProductUrl($product)
    {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars(route('product', $product->slug)) . '</loc>';
        $xml .= '<lastmod>' . $product->updated_at->format('Y-m-d') . '</lastmod>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.7</priority>';

        // Add product images to sitemap
        if ($product->image_urls && is_array($product->image_urls)) {
            foreach (array_slice($product->image_urls, 0, 5) as $imageUrl) {
                $xml .= '<image:image>';
                $xml .= '<image:loc>' . htmlspecialchars($imageUrl) . '</image:loc>';
                $xml .= '<image:title>' . htmlspecialchars($product->name) . '</image:title>';
                $xml .= '</image:image>';
            }
        }

        $xml .= '</url>';

        return $xml;
    }

    /**
     * Clear sitemap cache (call this when products/categories/brands are updated)
     */
    public static function clearCache()
    {
        Cache::forget('sitemap_xml');
    }
}

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
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
        $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        // Homepage - highest priority
        $xml .= $this->addUrl(route('home'), now(), 'daily', '1.0');

        // Static pages
        $xml .= $this->addUrl(route('about'), now()->subDays(30), 'monthly', '0.7');
        $xml .= $this->addUrl(route('contact'), now()->subDays(30), 'monthly', '0.6');
        $xml .= $this->addUrl(route('deals'), now(), 'daily', '0.8');

        // Categories - high priority
        Category::where('is_active', true)->each(function ($category) use (&$xml) {
            $xml .= $this->addUrl(
                route('category.show', $category->slug),
                $category->updated_at,
                'weekly',
                '0.9'
            );
        });

        // Brands - medium-high priority
        Brand::whereHas('products')->each(function ($brand) use (&$xml) {
            $xml .= $this->addUrl(
                route('brand.show', $brand->slug),
                $brand->updated_at,
                'weekly',
                '0.8'
            );
        });

        // Products - medium priority, with images
        Product::where('is_active', true)
            ->where('in_stock', true)
            ->orderBy('updated_at', 'desc')
            ->each(function ($product) use (&$xml) {
                $xml .= $this->addProductUrl($product);
            });

        $xml .= '</urlset>';

        return $xml;
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

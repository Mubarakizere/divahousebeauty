<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap for SEO';

    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $sitemap .= $this->addUrl(route('home'), '1.0', 'daily');

        // Static Pages
        $sitemap .= $this->addUrl(route('about'), '0.8', 'monthly');
        $sitemap .= $this->addUrl(route('contact'), '0.7', 'monthly');
        $sitemap .= $this->addUrl(route('deals'), '0.9', 'daily');

        // Products
        $this->info('Adding products...');
        Product::chunk(100, function ($products) use (&$sitemap) {
            foreach ($products as $product) {
                $sitemap .= $this->addUrl(
                    route('product', $product->slug),
                    '0.8',
                    'weekly',
                    $product->updated_at
                );
            }
        });

        // Categories
        $this->info('Adding categories...');
        foreach (Category::all() as $category) {
            $sitemap .= $this->addUrl(
                route('category.show', $category->slug),
                '0.7',
                'weekly'
            );
        }

        // Brands
        $this->info('Adding brands...');
        foreach (Brand::all() as $brand) {
            $sitemap .= $this->addUrl(
                route('brand.show', $brand->slug),
                '0.6',
                'weekly'
            );
        }

        $sitemap .= '</urlset>';

        // Save sitemap
        $path = public_path('sitemap.xml');
        File::put($path, $sitemap);

        $this->info("✅ Sitemap generated successfully at: {$path}");
        return 0;
    }

    private function addUrl($loc, $priority = '0.5', $changefreq = 'weekly', $lastmod = null)
    {
        $url = "  <url>\n";
        $url .= "    <loc>{$loc}</loc>\n";
        
        if ($lastmod) {
            $url .= "    <lastmod>{$lastmod->format('Y-m-d')}</lastmod>\n";
        }
        
        $url .= "    <changefreq>{$changefreq}</changefreq>\n";
        $url .= "    <priority>{$priority}</priority>\n";
        $url .= "  </url>\n";
        
        return $url;
    }
}

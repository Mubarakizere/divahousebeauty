<?php

namespace App\Helpers;

class SEOHelper
{
    /**
     * Generate SEO meta tags for product pages
     */
    public static function productMeta($product)
    {
        $title = $product->name . ' | Diva House Beauty Rwanda';
        $description = strip_tags(substr($product->description, 0, 155)) . (strlen($product->description) > 155 ? '...' : '');
        $image = $product->first_image_url;
        $price = $product->is_on_sale ? $product->sale_price : $product->price;
        
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => implode(', ', array_filter([
                $product->name,
                optional($product->category)->name,
                optional($product->brand)->name,
                'Rwanda',
                'Kigali',
                'cosmetics',
                'beauty products'
            ])),
            'canonical' => route('product', $product->slug),
            'og' => [
                'title' => $product->name,
                'description' => $description,
                'image' => $image,
                'url' => route('product', $product->slug),
                'type' => 'product',
                'price:amount' => $price,
                'price:currency' => 'RWF',
            ],
            'schema' => self::productSchema($product),
        ];
    }

    /**
     * Generate product JSON-LD schema
     */
    public static function productSchema($product)
    {
        $price = $product->is_on_sale ? $product->sale_price : $product->price;
        
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image_urls,
            'description' => strip_tags($product->description),
            'brand' => [
                '@type' => 'Brand',
                'name' => optional($product->brand)->name ?? 'Diva House Beauty'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product', $product->slug),
                'priceCurrency' => 'RWF',
                'price' => $price,
                'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'Diva House Beauty'
                ]
            ]
        ];
    }

    /**
     * Generate SEO meta tags for category pages
     */
    public static function categoryMeta($category, $products = null)
    {
        $title = $category->name . ' | Diva House Beauty Rwanda';
        $description = $category->description ?? "Shop {$category->name} products at Diva House Beauty. Premium cosmetics and fashion in Rwanda with fast Kigali delivery.";
        
        return [
            'title' => $title,
            'description' => substr($description, 0, 155),
            'keywords' => "{$category->name}, Rwanda, Kigali, cosmetics, beauty products, online shopping",
            'canonical' => route('category.show', $category->slug),
            'og' => [
                'title' => $category->name,
                'description' => substr($description, 0, 155),
                'url' => route('category.show', $category->slug),
                'type' => 'website',
            ],
        ];
    }

    /**
     * Generate SEO meta tags for homepage
     */
    public static function homeMeta()
    {
        return [
            'title' => 'Diva House Beauty - Premium Cosmetics & Fashion in Rwanda',
            'description' => 'Shop premium cosmetics and fashion products in Rwanda. Authentic beauty products, fast Kigali delivery, and trusted quality. MTN Mobile Money & Airtel Money accepted.',
            'keywords' => 'cosmetics Rwanda, beauty products Kigali, fashion Rwanda, online shopping Rwanda, makeup Kigali, skincare Rwanda',
            'canonical' => route('home'),
            'og' => [
                'title' => 'Diva House Beauty - Rwanda\'s Premier Beauty & Fashion Shop',
                'description' => 'Premium cosmetics and fashion products with fast delivery in Kigali',
                'url' => route('home'),
                'type' => 'website',
                'image' => asset('assets/images/og-image.jpg'),
            ],
            'schema' => self::organizationSchema(),
        ];
    }

    /**
     * Generate organization schema for homepage
     */
    public static function organizationSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Diva House Beauty',
            'url' => url('/'),
            'logo' => asset('assets/images/logo.png'),
            'description' => 'Premium cosmetics and fashion products in Rwanda',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'RW',
                'addressLocality' => 'Kigali',
            ],
            'sameAs' => [
                // Add social media links here
            ],
        ];
    }

    /**
     * Generate breadcrumb schema
     */
    public static function breadcrumbSchema($items)
    {
        $listItems = [];
        foreach ($items as $index => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }

    /**
     * Render JSON-LD script tag
     */
    public static function jsonLd($schema)
    {
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
}

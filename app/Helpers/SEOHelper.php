<?php

namespace App\Helpers;

class SEOHelper
{
    /**
     * Generate SEO meta tags for product pages
     */
    public static function productMeta($product)
    {
        $category = optional($product->category)->name;
        $brand = optional($product->brand)->name;
        
        // Enhanced title for Rwanda market
        $title = $product->name . ' - ' . ($brand ?: 'Premium') . ' | Buy in Rwanda | Diva House Beauty';
        
        // Enhanced description with local keywords
        $baseDescription = strip_tags(substr($product->description, 0, 100));
        $description = $baseDescription . ' ✓ Authentic Products ✓ Fast Kigali Delivery ✓ MTN & Airtel Money Payment ✓ Rwanda\'s #1 Cosmetics Store';
        $description = substr($description, 0, 155);
        
        $image = $product->first_image_url;
        $price = $product->is_on_sale ? $product->sale_price : $product->price;
        
        return [
            'title' => $title,
            'description' => $description,
            'keywords' => implode(', ', array_filter([
                $product->name,
                $brand,
                $category,
                'buy ' . strtolower($product->name) . ' Rwanda',
                'cosmetics Rwanda',
                'beauty products Kigali',
                'makeup Rwanda',
                'skincare Kigali',
                'online shopping Rwanda',
                'Diva House Beauty',
                'authentic cosmetics',
                'premium beauty products Rwanda'
            ])),
            'canonical' => route('product', $product->slug),
            'og' => [
                'title' => $product->name . ' - Rwanda\'s Premier Beauty Store',
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
     * Generate product JSON-LD schema with enhanced data
     */
    public static function productSchema($product)
    {
        $price = $product->is_on_sale ? $product->sale_price : $product->price;
        
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image_urls,
            'description' => strip_tags($product->description),
            'sku' => $product->id,
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
                'itemCondition' => 'https://schema.org/NewCondition',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'Diva House Beauty'
                ]
            ]
        ];

        // Add aggregate rating if product has reviews
        if (isset($product->reviews_count) && $product->reviews_count > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->average_rating ?? 5,
                'reviewCount' => $product->reviews_count,
                'bestRating' => 5,
                'worstRating' => 1
            ];
        }

        return $schema;
    }

    /**
     * Generate SEO meta tags for category pages
     */
    public static function categoryMeta($category, $products = null)
    {
        $title = "Buy {$category->name} in Rwanda - Authentic Products | Diva House Beauty";
        
        $productCount = $products ? $products->count() : 'Premium';
        $description = $category->description ?? "Shop authentic {$category->name} in Rwanda ✓ {$productCount}+ Products ✓ Fast Kigali Delivery ✓ Genuine Brands ✓ MTN & Airtel Money Payment ✓ Rwanda's #1 Online Cosmetics Store";
        
        return [
            'title' => $title,
            'description' => substr($description, 0, 155),
            'keywords' => implode(', ', [
                "{$category->name} Rwanda",
                "buy {$category->name} Kigali",
                "{$category->name} online shopping Rwanda",
                "authentic {$category->name}",
                "cosmetics Rwanda",
                "beauty products Kigali",
                "makeup Rwanda",
                "skincare Kigali",
                "Diva House Beauty",
                "online shopping Rwanda"
            ]),
            'canonical' => route('category.show', $category->slug),
            'og' => [
                'title' => "{$category->name} - Rwanda's Premier Beauty Store",
                'description' => substr($description, 0, 155),
                'url' => route('category.show', $category->slug),
                'type' => 'website',
            ],
        ];
    }

    /**
     * Generate SEO meta tags for homepage - optimized for #1 Rwanda ranking
     */
    public static function homeMeta()
    {
        return [
            'title' => 'Diva House Beauty - Rwanda\'s #1 Online Cosmetics & Beauty Store | Authentic Products, Fast Kigali Delivery',
            'description' => '🇷🇼 Rwanda\'s Premier Online Cosmetics & Beauty E-commerce Store ✓ 100% Authentic Products ✓ Fast Kigali Delivery ✓ MTN & Airtel Money ✓ Trusted by 1000s ✓ Makeup, Skincare, Fashion & More',
            'keywords' => implode(', ', [
                'cosmetics Rwanda',
                'beauty products Kigali',
                'cosmetics online Rwanda',
                'buy makeup Rwanda',
                'skincare products Kigali',
                'beauty store Rwanda',
                'online cosmetics store Rwanda',
                'Rwanda cosmetics e-commerce',
                'beauty products online shopping Rwanda',
                'authentic cosmetics Kigali',
                'makeup store Rwanda',
                'fashion Rwanda',
                'beauty shop Kigali',
                'Diva House Beauty',
                'cosmetic shopping Rwanda',
                'Rwanda beauty e-commerce',
                'best cosmetics store Rwanda',
                'online beauty store Kigali',
                'genuine beauty products Rwanda',
                'MTN Mobile Money cosmetics',
                'Airtel Money beauty products'
            ]),
            'canonical' => route('home'),
            'og' => [
                'title' => 'Diva House Beauty - Rwanda\'s #1 Cosmetics & Beauty E-commerce Store',
                'description' => 'Shop authentic cosmetics & beauty products in Rwanda. Fast Kigali delivery, trusted brands, MTN & Airtel Money payment.',
                'url' => route('home'),
                'type' => 'website',
                'image' => asset('assets/images/og-image.jpg'),
            ],
            'schema' => self::organizationSchema(),
        ];
    }

    /**
     * Generate comprehensive organization and WebSite schema for homepage
     */
    public static function organizationSchema()
    {
        return [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Diva House Beauty',
                'alternateName' => 'Diva House',
                'url' => url('/'),
                'logo' => asset('assets/images/logo.png'),
                'description' => 'Rwanda\'s leading online cosmetics and beauty products e-commerce store. Authentic products, fast Kigali delivery, trusted quality.',
                'slogan' => 'Rwanda\'s #1 Cosmetics & Beauty E-commerce Store',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => 'RW',
                    'addressLocality' => 'Kigali',
                    'addressRegion' => 'Kigali City'
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => -1.9441,
                    'longitude' => 30.0619
                ],
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'Rwanda'
                ],
                'priceRange' => 'RWF',
                'paymentAccepted' => ['MTN Mobile Money', 'Airtel Money', 'Cash on Delivery'],
                'sameAs' => [
                    // Add social media URLs when available
                    // 'https://www.facebook.com/divahousebeauty',
                    // 'https://www.instagram.com/divahousebeauty',
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'Diva House Beauty',
                'url' => url('/'),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => url('/search?q={search_term_string}')
                    ],
                    'query-input' => 'required name=search_term_string'
                ]
            ]
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

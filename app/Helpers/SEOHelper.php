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
        
        // Enhanced title for East Africa market
        $title = $product->name . ' - ' . ($brand ?: 'Premium') . ' | Buy in East Africa | Diva House Beauty';
        
        // Enhanced description with local keywords
        $baseDescription = strip_tags(substr($product->description, 0, 100));
        $description = $baseDescription . ' | Authentic Products | Fast Kigali Delivery | MTN & Airtel Money Payment | East Africa\'s #1 Cosmetics Store';
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
                'buy ' . strtolower($product->name) . ' East Africa',
                'cosmetics East Africa',
                'beauty products Kigali',
                'makeup East Africa',
                'skincare Kigali',
                'online shopping East Africa',
                'Diva House Beauty',
                'authentic cosmetics',
                'premium beauty products East Africa'
            ])),
            'canonical' => route('product', $product->slug),
            'og' => [
                'title' => $product->name . ' - East Africa\'s Premier Beauty Store',
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
        $title = "Buy {$category->name} in East Africa - Authentic Products | Diva House Beauty";
        
        $productCount = $products ? $products->count() : 'Premium';
        $description = $category->description ?? "Shop authentic {$category->name} in East Africa | {$productCount}+ Products | Fast Kigali Delivery | Genuine Brands | MTN & Airtel Money Payment | East Africa's #1 Online Cosmetics Store";
        
        return [
            'title' => $title,
            'description' => substr($description, 0, 155),
            'keywords' => implode(', ', [
                "{$category->name} East Africa",
                "buy {$category->name} Kigali",
                "{$category->name} online shopping East Africa",
                "authentic {$category->name}",
                "cosmetics East Africa",
                "beauty products Kigali",
                "makeup East Africa",
                "skincare Kigali",
                "Diva House Beauty",
                "online shopping East Africa"
            ]),
            'canonical' => route('category.show', $category->slug),
            'og' => [
                'title' => "{$category->name} - East Africa's Premier Beauty Store",
                'description' => substr($description, 0, 155),
                'url' => route('category.show', $category->slug),
                'type' => 'website',
                'image' => asset('assets/images/og-image.jpg'),
            ],
        ];
    }

    /**
     * Generate SEO meta tags for homepage - optimized for #1 East Africa ranking
     */
    public static function homeMeta()
    {
        return [
            'title' => 'Diva House Beauty - East Africa\'s #1 Online Cosmetics & Beauty Store | Authentic Products, Fast Kigali Delivery',
            'description' => 'East Africa\'s Premier Online Cosmetics & Beauty E-commerce Store | 100% Authentic Products | Fast Kigali Delivery | MTN & Airtel Money | Trusted by 1000s | Makeup, Skincare, Fashion & More',
            'keywords' => implode(', ', [
                'cosmetics East Africa',
                'beauty products Kigali',
                'cosmetics online East Africa',
                'buy makeup East Africa',
                'skincare products Kigali',
                'beauty store East Africa',
                'online cosmetics store East Africa',
                'East Africa cosmetics e-commerce',
                'beauty products online shopping East Africa',
                'authentic cosmetics Kigali',
                'makeup store East Africa',
                'fashion East Africa',
                'beauty shop Kigali',
                'Diva House Beauty',
                'cosmetic shopping East Africa',
                'East Africa beauty e-commerce',
                'best cosmetics store East Africa',
                'online beauty store Kigali',
                'genuine beauty products East Africa',
                'MTN Mobile Money cosmetics',
                'Airtel Money beauty products'
            ]),
            'canonical' => route('home'),
            'og' => [
                'title' => 'Diva House Beauty - East Africa\'s #1 Cosmetics & Beauty E-commerce Store',
                'description' => 'Shop authentic cosmetics & beauty products in East Africa. Fast Kigali delivery, trusted brands, MTN & Airtel Money payment.',
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
                'description' => 'East Africa\'s leading online cosmetics and beauty products e-commerce store. Authentic products, fast Kigali delivery, trusted quality.',
                'slogan' => 'East Africa\'s #1 Cosmetics & Beauty E-commerce Store',
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
                    '@type' => 'Place',
                    'name' => 'East Africa'
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
     * Generate SEO meta tags for About page
     */
    public static function aboutMeta()
    {
        return [
            'title' => 'About Diva House Beauty - East Africa\'s Premier Cosmetics & Beauty Store | Our Story',
            'description' => 'Learn about Diva House Beauty — East Africa\'s #1 online cosmetics and beauty store based in Kigali. Authentic products, fast delivery, and trusted by thousands of happy customers.',
            'keywords' => implode(', ', [
                'about Diva House Beauty',
                'cosmetics store East Africa',
                'beauty store Kigali',
                'East Africa beauty company',
                'Diva House Beauty story',
                'online cosmetics East Africa',
                'authentic beauty products Kigali',
            ]),
            'canonical' => route('about'),
            'og' => [
                'title' => 'About Diva House Beauty - East Africa\'s #1 Cosmetics Store',
                'description' => 'Discover our story. Diva House Beauty is Kigali\'s trusted destination for premium cosmetics, skincare, haircare & fashion.',
                'url' => route('about'),
                'type' => 'website',
                'image' => asset('assets/images/og-image.jpg'),
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'About Diva House Beauty',
                'description' => 'East Africa\'s premier online cosmetics and beauty store, based in Kigali.',
                'url' => route('about'),
                'mainEntity' => [
                    '@type' => 'Organization',
                    'name' => 'Diva House Beauty',
                    'url' => url('/'),
                    'telephone' => '+250780159059',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => 'Kigali',
                        'addressCountry' => 'RW',
                    ],
                ],
            ],
        ];
    }

    /**
     * Generate SEO meta tags for Contact page
     */
    public static function contactMeta()
    {
        return [
            'title' => 'Contact Diva House Beauty - Reach Us in Kigali, East Africa | Phone, Email, WhatsApp',
            'description' => 'Get in touch with Diva House Beauty in Kigali, East Africa. Call +250 780 159 059, email info@divahousebeauty.com, or WhatsApp us. Fast response guaranteed.',
            'keywords' => implode(', ', [
                'contact Diva House Beauty',
                'Diva House Beauty phone',
                'cosmetics store Kigali contact',
                'beauty store East Africa phone number',
                'Diva House Beauty email',
                'Diva House Beauty WhatsApp',
                'Kigali beauty store location',
            ]),
            'canonical' => route('contact'),
            'og' => [
                'title' => 'Contact Diva House Beauty - Kigali, East Africa',
                'description' => 'Reach us at +250 780 159 059 or email info@divahousebeauty.com. Visit our store in Kigali, East Africa.',
                'url' => route('contact'),
                'type' => 'website',
                'image' => asset('assets/images/og-image.jpg'),
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Contact Diva House Beauty',
                'description' => 'Contact information for Diva House Beauty in Kigali, East Africa.',
                'url' => route('contact'),
                'mainEntity' => [
                    '@type' => 'Organization',
                    'name' => 'Diva House Beauty',
                    'url' => url('/'),
                    'email' => 'info@divahousebeauty.com',
                    'telephone' => '+250780159059',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => 'Kigali',
                        'addressCountry' => 'RW',
                    ],
                    'openingHoursSpecification' => [
                        [
                            '@type' => 'OpeningHoursSpecification',
                            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                            'opens' => '09:00',
                            'closes' => '20:00',
                        ],
                        [
                            '@type' => 'OpeningHoursSpecification',
                            'dayOfWeek' => 'Sunday',
                            'opens' => '12:00',
                            'closes' => '18:00',
                        ],
                    ],
                ],
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

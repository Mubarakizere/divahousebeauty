{{-- SEO Meta Tags Partial - East Africa Market Optimized --}}
@if(isset($seo))
    {{-- Basic Meta Tags --}}
    <title>{{ $seo['title'] ?? 'Diva House Beauty - East Africa\'s #1 Cosmetics Store' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    @if(isset($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    
    {{-- Geographic Targeting for East Africa --}}
    <meta name="geo.region" content="RW" />
    <meta name="geo.placename" content="Kigali, East Africa" />
    <meta name="geo.position" content="-1.9441;30.0619" />
    <meta name="ICBM" content="-1.9441, 30.0619" />
    
    {{-- Language & Region --}}
    <meta http-equiv="content-language" content="en-RW" />
    <link rel="alternate" hreflang="en-rw" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="fr-rw" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="rw" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    
    {{-- Author & Publisher --}}
    <meta name="author" content="Diva House Beauty">
    <meta name="publisher" content="Diva House Beauty">
    <meta name="copyright" content="Diva House Beauty">
    
    {{-- Open Graph Tags (Facebook, WhatsApp) --}}
    @php
        $shareImage = $seo['og']['image'] ?? asset('assets/images/og-image.jpg');
        $shareTitle = $seo['og']['title'] ?? $seo['title'] ?? 'Diva House Beauty';
        $shareDesc  = $seo['og']['description'] ?? $seo['description'] ?? 'East Africa\'s Premier Online Cosmetics & Beauty Store';
    @endphp
    @if(isset($seo['og']))
        <meta property="og:title" content="{{ $shareTitle }}" />
        <meta property="og:description" content="{{ $shareDesc }}" />
        <meta property="og:url" content="{{ $seo['og']['url'] ?? url()->current() }}" />
        <meta property="og:type" content="{{ $seo['og']['type'] ?? 'website' }}" />
        <meta property="og:site_name" content="Diva House Beauty" />
        <meta property="og:locale" content="en_RW" />
        <meta property="og:locale:alternate" content="fr_RW" />
        <meta property="og:image" content="{{ $shareImage }}" />
        <meta property="og:image:alt" content="{{ $shareTitle }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:image:type" content="image/jpeg" />
        
        {{-- Product specific OG tags --}}
        @if(isset($seo['og']['type']) && $seo['og']['type'] === 'product')
            @if(isset($seo['og']['price:amount']))
                <meta property="product:price:amount" content="{{ $seo['og']['price:amount'] }}" />
                <meta property="product:price:currency" content="{{ $seo['og']['price:currency'] ?? 'RWF' }}" />
                <meta property="product:availability" content="in stock" />
                <meta property="product:brand" content="Diva House Beauty" />
                <meta property="product:condition" content="new" />
                <meta property="product:retailer_item_id" content="{{ $seo['og']['product_id'] ?? '' }}" />
            @endif
        @endif
    @else
        <meta property="og:title" content="{{ $shareTitle }}" />
        <meta property="og:description" content="{{ $shareDesc }}" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="Diva House Beauty" />
        <meta property="og:image" content="{{ $shareImage }}" />
        <meta property="og:image:alt" content="{{ $shareTitle }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:image:type" content="image/jpeg" />
    @endif
    
    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $shareTitle }}">
    <meta name="twitter:description" content="{{ $shareDesc }}">
    <meta name="twitter:site" content="@divahousebeauty">
    <meta name="twitter:image" content="{{ $shareImage }}">
    <meta name="twitter:image:alt" content="{{ $shareTitle }}">
    
    {{-- Mobile & App Tags --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Diva House Beauty">
    
    {{-- Additional SEO Tags --}}
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
    <meta name="googlebot" content="index, follow" />
    <meta name="bingbot" content="index, follow" />
    
    {{-- JSON-LD Structured Data --}}
    @if(isset($seo['schema']))
        @if(is_array($seo['schema']) && isset($seo['schema'][0]))
            {{-- Multiple schema objects --}}
            @foreach($seo['schema'] as $schemaItem)
                {!! App\Helpers\SEOHelper::jsonLd($schemaItem) !!}
            @endforeach
        @else
            {{-- Single schema object --}}
            {!! App\Helpers\SEOHelper::jsonLd($seo['schema']) !!}
        @endif
    @endif
@endif

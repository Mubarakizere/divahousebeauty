{{-- SEO Meta Tags Partial - Rwanda Market Optimized --}}
@if(isset($seo))
    {{-- Basic Meta Tags --}}
    <title>{{ $seo['title'] ?? 'Diva House Beauty - Rwanda\'s #1 Cosmetics Store' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    @if(isset($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    
    {{-- Geographic Targeting for Rwanda --}}
    <meta name="geo.region" content="RW" />
    <meta name="geo.placename" content="Kigali, Rwanda" />
    <meta name="geo.position" content="-1.9441;30.0619" />
    <meta name="ICBM" content="-1.9441, 30.0619" />
    
    {{-- Language & Region --}}
    <meta http-equiv="content-language" content="en-RW" />
    <link rel="alternate" hreflang="en-rw" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="fr-rw" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="rw" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />
    
    {{-- Canonical URL --}}
    @if(isset($seo['canonical']))
        <link rel="canonical" href="{{ $seo['canonical'] }}">
    @endif
    
    {{-- Author & Publisher --}}
    <meta name="author" content="Diva House Beauty">
    <meta name="publisher" content="Diva House Beauty">
    <meta name="copyright" content="Diva House Beauty">
    
    {{-- Open Graph Tags (Facebook, WhatsApp) --}}
    @if(isset($seo['og']))
        <meta property="og:title" content="{{ $seo['og']['title'] ?? $seo['title'] }}" />
        <meta property="og:description" content="{{ $seo['og']['description'] ?? $seo['description'] }}" />
        <meta property="og:url" content="{{ $seo['og']['url'] ?? url()->current() }}" />
        <meta property="og:type" content="{{ $seo['og']['type'] ?? 'website' }}" />
        <meta property="og:site_name" content="Diva House Beauty" />
        <meta property="og:locale" content="en_RW" />
        <meta property="og:locale:alternate" content="fr_RW" />
        
        @if(isset($seo['og']['image']))
            <meta property="og:image" content="{{ $seo['og']['image'] }}" />
            <meta property="og:image:alt" content="{{ $seo['og']['title'] ?? 'Diva House Beauty Product' }}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta property="og:image:type" content="image/jpeg" />
        @endif
        
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
    @endif
    
    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['og']['title'] ?? $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['og']['description'] ?? $seo['description'] }}">
    <meta name="twitter:site" content="@divahousebeauty">
    @if(isset($seo['og']['image']))
        <meta name="twitter:image" content="{{ $seo['og']['image'] }}">
        <meta name="twitter:image:alt" content="{{ $seo['og']['title'] ?? 'Diva House Beauty Product' }}">
    @endif
    
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

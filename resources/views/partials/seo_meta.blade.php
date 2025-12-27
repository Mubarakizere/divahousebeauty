{{-- SEO Meta Tags Partial --}}
@if(isset($seo))
    {{-- Basic Meta Tags --}}
    <title>{{ $seo['title'] ?? 'Diva House Beauty' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? '' }}">
    @if(isset($seo['keywords']))
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    
    {{-- Canonical URL --}}
    @if(isset($seo['canonical']))
        <link rel="canonical" href="{{ $seo['canonical'] }}">
    @endif
    
    {{-- Open Graph Tags --}}
    @if(isset($seo['og']))
        <meta property="og:title" content="{{ $seo['og']['title'] ?? $seo['title'] }}" />
        <meta property="og:description" content="{{ $seo['og']['description'] ?? $seo['description'] }}" />
        <meta property="og:url" content="{{ $seo['og']['url'] ?? url()->current() }}" />
        <meta property="og:type" content="{{ $seo['og']['type'] ?? 'website' }}" />
        <meta property="og:site_name" content="Diva House Beauty" />
        
        @if(isset($seo['og']['image']))
            <meta property="og:image" content="{{ $seo['og']['image'] }}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
        @endif
        
        {{-- Product specific OG tags --}}
        @if(isset($seo['og']['type']) && $seo['og']['type'] === 'product')
            @if(isset($seo['og']['price:amount']))
                <meta property="product:price:amount" content="{{ $seo['og']['price:amount'] }}" />
                <meta property="product:price:currency" content="{{ $seo['og']['price:currency'] ?? 'RWF' }}" />
            @endif
        @endif
    @endif
    
    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['og']['title'] ?? $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['og']['description'] ?? $seo['description'] }}">
    @if(isset($seo['og']['image']))
        <meta name="twitter:image" content="{{ $seo['og']['image'] }}">
    @endif
    
    {{-- JSON-LD Structured Data --}}
    @if(isset($seo['schema']))
        {!! App\Helpers\SEOHelper::jsonLd($seo['schema']) !!}
    @endif
@endif

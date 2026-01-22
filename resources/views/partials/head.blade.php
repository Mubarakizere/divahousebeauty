<!-- resources/views/layouts/partials/head.blade.php -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Google Search Console Verification -->
    <meta name="google-site-verification" content="42_XUFLKGXoph33K7-fbfNzwRUFnFvInTzPQCuIJLYM" />
    
    <title>Diva House Beauty</title>
    <meta name="description" content="Diva House Beauty  Your destination for luxury beauty services.">
    <meta name="keywords" content="beauty, ecommerce, diva house, nails, skincare, booking, salon, Kigali, Rwanda">
    <meta name="author" content="Izere Mubarak">

    <!-- Open Graph -->
    <meta property="og:title" content="Diva House Beauty" />
    <meta property="og:description" content="Diva House Beauty  Your destination for luxury beauty services." />
    <meta property="og:image" content="{{ asset('images/feature-image.jpg') }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <!-- Theme Color -->
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-title" content="Diva House Beauty">
    <meta name="application-name" content="Diva House Beauty">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/icons/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta name="apple-mobile-web-app-title" content="Diva House Beauty">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW Registered!', reg.scope))
                    .catch(err => console.log('SW Registration Failed:', err));
            });
        }
    </script>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">

    <!-- Phosphor Icons (for modern icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Custom App CSS (optional, if you add custom styles) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Currency Converter Styles -->
    <link rel="stylesheet" href="{{ asset('css/currency-styles.css') }}">

    <!-- Alpine.js (for mobile toggle, dropdowns etc.) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Currency Converter Script -->
    <script src="{{ asset('js/currency-converter.js') }}"></script>
</head>

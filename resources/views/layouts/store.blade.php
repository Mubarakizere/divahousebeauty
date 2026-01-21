<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>@yield('title', 'Diva House Beauty')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind + Alpine --}}
  <script>
    tailwind = { config: { corePlugins: { preflight: false } } }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  
  {{-- Bootstrap (minimal for pagination) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

  {{-- Google Fonts: Playfair Display (Headings) + Inter (Body) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

  <style>
    /* Classic Luxury Palette */
    :root { 
      --gold: #C5A059; /* Muted, metallic gold */
      --black: #0F172A; /* Deep navy-black */
      --gray-light: #F9FAFB;
    }
    
    body { font-family: 'Inter', sans-serif; color: #334155; Background-color: #FAFAFA; }
    h1, h2, h3, h4, h5, h6 { font-family: 'Playfair Display', serif; }

    .shadow-ring { box-shadow: 0 0 0 1px rgba(0,0,0,.05), 0 2px 8px rgba(0,0,0,.04); }
    .no-scrollbar::-webkit-scrollbar { display: none } 
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none }
    [x-cloak] { display: none !important; }
    
    .badge { display: inline-flex; align-items: center; font-weight: 600; line-height: 1; padding: .35rem .65rem; font-size: .65rem; letter-spacing: 0.05em; text-transform: uppercase; }
    .btn-gold {
        background-color: var(--gold);
        color: white;
        transition: all 0.3s ease;
    }
    .btn-gold:hover {
        background-color: #B08D4C;
        transform: translateY(-1px);
    }
  </style>
  
  <!-- Currency Converter Styles -->
  <link rel="stylesheet" href="{{ asset('css/currency-styles.css') }}">
  
  @stack('head')
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- HEADER --}}
  @include('partials.header_home2')

  {{-- MAIN CONTENT --}}
  <main class="min-h-[60vh] py-12">
    <div class="container mx-auto px-4 max-w-7xl">
        {{-- Breadcrumb / Title Area --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-[var(--black)] mb-2">@yield('title')</h1>
            @hasSection('subtitle')
                <p class="text-slate-500">@yield('subtitle')</p>
            @endif
        </div>

        @yield('content')
    </div>
  </main>

  {{-- FOOTER --}}
  @include('partials.footer')
  
  {{-- AUTH MODAL --}}
  @includeIf('partials.auth_modal')

  <!-- Currency Converter Script -->
  <script src="{{ asset('js/currency-converter.js') }}"></script>

  @stack('scripts')
</body>
</html>

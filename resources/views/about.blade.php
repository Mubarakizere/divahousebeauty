<!DOCTYPE html>
<html lang="en">
@php
    // Header partial fallbacks
    $categories = $categories ?? \App\Models\Category::select('id','name','slug')->get();
    $count      = $count ?? (auth()->id() ? \App\Models\Cart::where('users_id', auth()->id())->count() : 0);

    // Simple stats fallback (replace with real figures whenever you want)
    $stats = [
        ['label' => 'Happy Clients', 'value' => '5k+'],
        ['label' => 'Products',      'value' => '1.2k+'],
        ['label' => 'Average Rating','value' => '4.8/5'],
        ['label' => 'Cities Served', 'value' => 'Kigali'],
    ];
@endphp
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>About Us</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind (preflight OFF) + Alpine for header/nav --}}
  <script>
    tailwind = { config: { corePlugins: { preflight: false } } }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Line Awesome icons (used throughout header/content) --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  {{-- Minimal Bootstrap only for pagination template (bootstrap-4) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

  {{-- noUiSlider for price filter --}}
  <link rel="stylesheet" href="{{ asset('assets/css/plugins/nouislider/nouislider.css') }}">

  <style>
    :root{ --gold:#cc9966; --black:#111827; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    .no-scrollbar::-webkit-scrollbar{display:none} .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
    .nav-pill{ border-radius:9999px }
    [x-cloak]{ display:none !important; }

    /* small helpers */
    .badge{ display:inline-flex; align-items:center; font-weight:700; line-height:1; border-radius:9999px; padding:.25rem .5rem; font-size:.675rem }
    .badge-new{ background:#10b981; color:#fff }
    .badge-sale{ background:#ef4444; color:#fff }
    .line-through { text-decoration: line-through; }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- ===================== HEADER (Tailwind) ===================== --}}
  @include('partials.header_home2')


  {{-- ================= HERO ================= --}}
  <section class="relative">
    <div class="relative">
      <img src="{{ asset('assets/images/page-header-bg.jpg') }}" alt="About Diva House Beauty"
           class="h-48 w-full object-cover md:h-56 lg:h-64">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-black/0"></div>
    </div>
    <div class="absolute inset-0 flex items-end">
      <div class="mx-auto w-full max-w-7xl px-3 sm:px-4 pb-4 md:pb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">
          About <span class="text-[var(--gold)]">Diva House Beauty</span>
        </h1>
        <nav class="mt-1 text-xs text-white/80">
          <a href="{{ route('home') }}" class="hover:text-[var(--gold)]">Home</a>
          <span class="mx-1">/</span>
          <span class="text-white">About</span>
        </nav>
      </div>
    </div>
  </section>

  {{-- ================= INTRO / BRAND PILLARS ================= --}}
  <main class="py-8">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 lg:gap-6 items-start">
        <div class="col-span-12 lg:col-span-7">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900">We celebrate beauty inside and out.</h2>
            <p class="mt-2 text-sm md:text-base text-slate-600">
              Based in Kigali, <strong>Diva House Beauty</strong> is your destination for
              <strong>premium cosmetics</strong>, trend forward <strong>fashion</strong>, and professional
              <strong>beauty services</strong>. Our mission is simple: help every client look great, feel confident,
              and enjoy a delightful experience from browsing products to booking a full glam session.
            </p>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="rounded-xl border border-slate-200 p-4">
                <i class="la la-magic text-2xl text-[var(--gold)]"></i>
                <h3 class="mt-2 text-sm font-semibold text-slate-900">Cosmetics</h3>
                <p class="mt-1 text-xs text-slate-600">Skincare, makeup, haircare & more from trusted brands and essentials we love.</p>
                <a href="{{ route('category') }}" class="mt-2 inline-block text-[var(--gold)] text-sm hover:opacity-90">Shop products →</a>
              </div>
              <div class="rounded-xl border border-slate-200 p-4">
                <i class="la la-tshirt text-2xl text-[var(--gold)]"></i>
                <h3 class="mt-2 text-sm font-semibold text-slate-900">Fashion</h3>
                <p class="mt-1 text-xs text-slate-600">Capsule pieces, accessories and seasonal looks to elevate your style.</p>
                <a href="{{ route('category') }}" class="mt-2 inline-block text-[var(--gold)] text-sm hover:opacity-90">Explore fashion →</a>
              </div>
              <div class="rounded-xl border border-slate-200 p-4">
                <i class="la la-spa text-2xl text-[var(--gold)]"></i>
                <h3 class="mt-2 text-sm font-semibold text-slate-900">Beauty Services</h3>
                <p class="mt-1 text-xs text-slate-600">Lashes, nails, hair, facials & spa delivered by friendly experts.</p>
                <a href="{{ route('booking.create') }}" class="mt-2 inline-block text-[var(--gold)] text-sm hover:opacity-90">Book appointment →</a>
              </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
              <span class="pill"><i class="la la-check mr-1"></i> Safe & hygienic</span>
              <span class="pill"><i class="la la-truck mr-1"></i> Fast delivery in Kigali</span>
              <span class="pill"><i class="la la-smile mr-1"></i> Friendly support</span>
            </div>
          </div>
        </div>

        {{-- Stats / Visual --}}
        <aside class="col-span-12 lg:col-span-5">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
            <h3 class="text-base md:text-lg font-semibold text-slate-900">Trusted by our community</h3>
            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-3">
              @foreach($stats as $s)
                <div class="rounded-xl border border-slate-200 p-3 text-center">
                  <div class="text-xl font-extrabold text-slate-900">{{ $s['value'] }}</div>
                  <div class="text-[12px] text-slate-500">{{ $s['label'] }}</div>
                </div>
              @endforeach
            </div>
            <img src="{{ asset('assets/images/demos/demo-14/bg-1.jpg') }}"
                 alt="Diva House Beauty"
                 class="mt-4 h-40 w-full rounded-xl object-cover">
          </div>
        </aside>
      </div>

      {{-- ================= VALUES ================= --}}
      <section class="mt-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
          <h3 class="text-base md:text-lg font-semibold text-slate-900">Our Values</h3>
          <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-xl border border-slate-200 p-4">
              <i class="la la-certificate text-2xl text-[var(--gold)]"></i>
              <h4 class="mt-2 font-semibold text-sm text-slate-900">Quality First</h4>
              <p class="mt-1 text-xs text-slate-600">We carefully select products and tools that deliver dependable results.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
              <i class="la la-shield-alt text-2xl text-[var(--gold)]"></i>
              <h4 class="mt-2 font-semibold text-sm text-slate-900">Hygiene & Safety</h4>
              <p class="mt-1 text-xs text-slate-600">Clean stations, sterilized tools, and client wellbeing come first.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
              <i class="la la-heart text-2xl text-[var(--gold)]"></i>
              <h4 class="mt-2 font-semibold text-sm text-slate-900">Client Care</h4>
              <p class="mt-1 text-xs text-slate-600">We listen, advise, and tailor services to your needs and schedule.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
              <i class="la la-users text-2xl text-[var(--gold)]"></i>
              <h4 class="mt-2 font-semibold text-sm text-slate-900">Community</h4>
              <p class="mt-1 text-xs text-slate-600">We support local talent and celebrate diverse beauty every day.</p>
            </div>
          </div>
        </div>
      </section>

      {{-- ================= TIMELINE ================= --}}
      <section class="mt-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
          <h3 class="text-base md:text-lg font-semibold text-slate-900">Our Journey</h3>
          <ol class="mt-3 relative border-l border-slate-200 pl-5 space-y-5">
            <li>
              <span class="absolute -left-2.5 top-1.5 h-5 w-5 rounded-full bg-[var(--gold)]"></span>
              <p class="text-sm font-semibold text-slate-900">The Idea</p>
              <p class="text-xs text-slate-600">We started with a simple goal bring reliable beauty products and caring services under one roof.</p>
            </li>
            <li>
              <span class="absolute -left-2.5 top-1.5 h-5 w-5 rounded-full bg-[var(--gold)]"></span>
              <p class="text-sm font-semibold text-slate-900">The Store</p>
              <p class="text-xs text-slate-600">We opened our doors in Kigali with curated cosmetics & a small fashion rack.</p>
            </li>
            <li>
              <span class="absolute -left-2.5 top-1.5 h-5 w-5 rounded-full bg-[var(--gold)]"></span>
              <p class="text-sm font-semibold text-slate-900">The Studio</p>
              <p class="text-xs text-slate-600">We expanded into a full service studio: lashes, nails, hair & skin treatments.</p>
            </li>
            <li>
              <span class="absolute -left-2.5 top-1.5 h-5 w-5 rounded-full bg-[var(--gold)]"></span>
              <p class="text-sm font-semibold text-slate-900">Today</p>
              <p class="text-xs text-slate-600">An inclusive space for cosmetics, fashion and professional care focused on results and experience.</p>
            </li>
          </ol>
        </div>
      </section>

      {{-- ================= LOCATION / HOURS + CTA ================= --}}
      <section class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
          <h3 class="text-base md:text-lg font-semibold text-slate-900">Visit Us</h3>
          <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl border border-slate-200 p-3">
              <div class="font-medium text-slate-900">Location</div>
              <p class="mt-1 text-slate-600">Kigali, Rwanda (city center area)</p>
              <a href="https://maps.google.com" target="_blank" class="mt-1 inline-flex items-center gap-1 text-[var(--gold)] hover:opacity-90">
                <i class="la la-map-marker"></i> Get Directions
              </a>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
              <div class="font-medium text-slate-900">Hours</div>
              <p class="mt-1 text-slate-600">Mon–Sat: 09:00–20:00<br>Sun: 12:00–18:00</p>
            </div>
          </div>
          <div class="mt-4 flex flex-wrap items-center gap-3">
            <a href="{{ route('category') }}" class="btn-outline"><i class="la la-shopping-bag"></i> Browse Products</a>
            <a href="{{ route('booking.create') }}" class="btn-primary"><i class="la la-calendar-check"></i> Book a Service</a>
            <a href="tel:0780159059" class="btn-outline"><i class="la la-phone"></i> +250 780 159 059</a>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
          <h3 class="text-base md:text-lg font-semibold text-slate-900">Need help choosing?</h3>
          <p class="mt-2 text-sm text-slate-600">
            Tell us your skin type, style goals, or the event you’re prepping for we’ll recommend products and book the right service.
          </p>
          <img src="{{ asset('assets/images/demos/demo-14/bg-1.jpg') }}" alt="" class="mt-3 h-36 w-full rounded-xl object-cover">
          <a href="https://wa.me/250780159059?text={{ urlencode('Hello Diva House Beauty, I need help choosing products/services.') }}"
             target="_blank" rel="noopener" class="mt-3 btn-primary">
            <i class="la la-whatsapp text-lg"></i> Chat on WhatsApp
          </a>
        </div>
      </section>

      {{-- ================= FAQ ================= --}}
      <section class="mt-6"
               x-data="{ open: null }">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
          <h3 class="text-base md:text-lg font-semibold text-slate-900">FAQ</h3>

          @php
            $faq = [
              ['q' => 'Do you offer same-day appointments?', 'a' => 'Yes depending on availability. Book online or call us and we’ll try to fit you in.'],
              ['q' => 'Do you deliver products in Kigali?', 'a' => 'Yes. We offer fast, affordable delivery across Kigali.'],
              ['q' => 'Which beauty services can I book?', 'a' => 'Lashes, nails, hair styling, facials/skin treatments, makeup and spa packages.'],
              ['q' => 'Are products genuine?', 'a' => 'Absolutely. We source from trusted distributors and stand behind every brand we stock.'],
            ];
          @endphp

          <ul class="mt-3 divide-y divide-slate-200">
            @foreach($faq as $i => $f)
              <li class="py-3">
                <button type="button" class="flex w-full items-center justify-between text-left"
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                  <span class="text-sm font-medium text-slate-900">{{ $f['q'] }}</span>
                  <i class="la" :class="open === {{ $i }} ? 'la-minus' : 'la-plus'"></i>
                </button>
                <div x-show="open === {{ $i }}" x-collapse class="mt-2 text-sm text-slate-600">
                  {{ $f['a'] }}
                </div>
              </li>
            @endforeach
          </ul>
        </div>
      </section>
    </div>
  </main>

  {{-- ================= FOOTER (Tailwind partial) ================= --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- ================= JSON-LD ================= --}}
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": ["Organization","BeautySalon"],
    "name": "Diva House Beauty",
    "description": "Cosmetics, fashion and professional beauty services in Kigali.",
    "url": "{{ url('/') }}",
    "telephone": "+250780159059",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Kigali",
      "addressCountry": "RW"
    },
    "sameAs": [
      "https://www.facebook.com/",
      "https://www.instagram.com/",
      "https://www.tiktok.com/"
    ]
  }
  </script>
</body>
</html>

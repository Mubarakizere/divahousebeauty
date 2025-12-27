<!DOCTYPE html>
<html lang="en">
@php
    $categories = $categories ?? \App\Models\Category::select('id','name','slug')->get();
    $count      = $count ?? (auth()->id() ? \App\Models\Cart::where('users_id', auth()->id())->count() : 0);
    
    // E-commerce/Community focused stats
    $stats = [
        ['label' => 'Happy Customers', 'value' => '5k+'],
        ['label' => 'Premium Products','value' => '1.2k+'],
        ['label' => 'Average Rating',  'value' => '4.8/5'],
        ['label' => 'Cities Served',   'value' => 'Kigali'],
    ];
@endphp
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>About Diva House Beauty</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Google Fonts: Inter + Playfair Display --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">


  {{-- Tailwind & Alpine --}}
  <script>
    tailwind = { config: { corePlugins: { preflight: false } } }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons --}}
  <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

  {{-- Plugins --}}
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/plugins/nouislider/nouislider.css') }}">

  <style>
    :root { --gold: #cc9966; --black: #111827; }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, h4, h5, h6, .font-playfair { font-family: 'Playfair Display', serif; }
    
    .shadow-luxury { box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1); }
    .no-scrollbar::-webkit-scrollbar{ display:none } 
    .no-scrollbar{ -ms-overflow-style:none; scrollbar-width:none }
    
    [x-cloak]{ display:none !important; }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- HEADER --}}
  @include('partials.header_home2')

  {{-- HERO --}}
  <section class="relative bg-slate-900 border-t border-white/10">
    <div class="relative h-48 md:h-64 overflow-hidden">
      <img src="{{ asset('assets/images/page-header-bg.jpg') }}" alt="About Diva House Beauty"
           class="h-full w-full object-cover opacity-60">
      <div class="absolute inset-0 bg-black/40"></div>
      
      <div class="absolute inset-0 flex flex-col justify-center px-6 md:px-12 text-center text-white">
        <h1 class="text-3xl md:text-5xl font-medium tracking-tight mb-2">
            About <span class="text-[var(--gold)] italic">Us</span>
        </h1>
        <nav class="flex justify-center text-sm md:text-base text-white/80 space-x-2">
            <a href="{{ route('home') }}" class="hover:text-[var(--gold)] transition-colors">Home</a>
            <span>/</span>
            <span>About</span>
        </nav>
      </div>
    </div>
  </section>

  {{-- MAIN CONTENT --}}
  <main class="py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      
      {{-- INTRO & STORY --}}
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        {{-- LEFT: Content --}}
        <div class="col-span-12 lg:col-span-7 space-y-8">
            <div class="bg-white rounded-xl shadow-luxury p-6 md:p-10 border border-slate-100">
                <h2 class="text-2xl md:text-3xl font-medium text-slate-900 mb-4">We celebrate beauty, inside and out.</h2>
                <div class="space-y-4 text-slate-600 leading-relaxed text-sm md:text-base">
                    <p>
                        Based in Kigali, <strong>Diva House Beauty</strong> is your premier destination for <strong>premium cosmetics</strong> and trend-forward <strong>fashion</strong>.
                    </p>
                    <p>
                        Our mission is simple: to help every client look great and feel confident. We curate the finest selection of beauty essentials and style staples, ensuring that everything you find here meets our high standards of quality and authenticity.
                    </p>
                </div>

                {{-- Shopping Focus Grid --}}
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('category') }}" class="group block p-5 rounded-lg border border-slate-200 hover:border-[var(--gold)] transition-all bg-slate-50 hover:bg-white">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[var(--gold)] shadow-sm mb-3 group-hover:scale-110 transition-transform">
                            <i class="la la-magic text-2xl"></i>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 group-hover:text-[var(--gold)] transition-colors">Cosmetics</h3>
                        <p class="mt-2 text-xs text-slate-500">Skincare, makeup, haircare & more from trusted international brands.</p>
                    </a>
                    
                    <a href="{{ route('category') }}" class="group block p-5 rounded-lg border border-slate-200 hover:border-[var(--gold)] transition-all bg-slate-50 hover:bg-white">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-[var(--gold)] shadow-sm mb-3 group-hover:scale-110 transition-transform">
                            <i class="la la-tshirt text-2xl"></i>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 group-hover:text-[var(--gold)] transition-colors">Fashion</h3>
                        <p class="mt-2 text-xs text-slate-500">Capsule pieces, elegant accessories and seasonal looks.</p>
                    </a>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-xs md:text-sm font-medium text-slate-500">
                    <span class="flex items-center gap-1"><i class="la la-check text-green-500"></i> Authentic Products</span>
                    <span class="flex items-center gap-1"><i class="la la-truck text-[var(--gold)]"></i> Fast Delivery in Kigali</span>
                </div>
            </div>

            {{-- VALUES --}}
            <div class="bg-white rounded-xl shadow-luxury p-6 md:p-8 border border-slate-100">
                <h3 class="text-xl font-medium text-slate-900 mb-6">Our Values</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center sm:text-left">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] mb-3">
                            <i class="la la-certificate text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-slate-900 text-sm">Authenticity</h4>
                        <p class="mt-1 text-xs text-slate-500">We only stock genuine products from trusted sources.</p>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] mb-3">
                            <i class="la la-star text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-slate-900 text-sm">Quality</h4>
                        <p class="mt-1 text-xs text-slate-500">Curated items that deliver real results.</p>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] mb-3">
                            <i class="la la-smile text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-slate-900 text-sm">Service</h4>
                        <p class="mt-1 text-xs text-slate-500">Friendly support to help you find what you need.</p>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] mb-3">
                            <i class="la la-heart text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-slate-900 text-sm">Passion</h4>
                        <p class="mt-1 text-xs text-slate-500">We love what we do and it shows in our collection.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Stats & Visuals --}}
        <aside class="col-span-12 lg:col-span-5 space-y-8">
            
            <div class="bg-white rounded-xl shadow-luxury p-6 md:p-8 border border-slate-100">
                <div class="relative rounded-lg overflow-hidden h-48 mb-6">
                    <img src="{{ asset('assets/images/demos/demo-14/bg-1.jpg') }}" alt="Diva House Beauty" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/20"></div>
                </div>
                
                <h3 class="text-lg font-medium text-slate-900 mb-4 text-center">Trusted by our Community</h3>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($stats as $s)
                    <div class="text-center p-3 rounded-lg bg-slate-50">
                        <div class="text-2xl font-bold text-slate-900 font-playfair">{{ $s['value'] }}</div>
                        <div class="text-[11px] uppercase tracking-wider text-slate-500 font-medium">{{ $s['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TIMELINE --}}
            <div class="bg-white rounded-xl shadow-luxury p-6 md:p-8 border border-slate-100">
                <h3 class="text-lg font-medium text-slate-900 mb-4">Our Journey</h3>
                <ol class="relative border-l border-slate-200 pl-6 space-y-6">
                    <li>
                        <span class="absolute -left-2 top-2 h-4 w-4 rounded-full border-2 border-white bg-[var(--gold)] box-content"></span>
                        <p class="text-sm font-semibold text-slate-900">The Idea</p>
                        <p class="text-xs text-slate-500 mt-1">We started with a simple goal: bring reliable beauty products to Kigali.</p>
                    </li>
                    <li>
                        <span class="absolute -left-2 top-2 h-4 w-4 rounded-full border-2 border-white bg-[var(--gold)] box-content"></span>
                        <p class="text-sm font-semibold text-slate-900">The Store</p>
                        <p class="text-xs text-slate-500 mt-1">We opened our doors with a curated selection of cosmetics and fashion.</p>
                    </li>
                    <li>
                        <span class="absolute -left-2 top-2 h-4 w-4 rounded-full border-2 border-white bg-[var(--gold)] box-content"></span>
                        <p class="text-sm font-semibold text-slate-900">Today</p>
                        <p class="text-xs text-slate-500 mt-1">A trusted destination for quality beauty and style essentials.</p>
                    </li>
                </ol>
            </div>

        </aside>

      </div>
      
      {{-- VISIT US / FAQ --}}
      <section class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <div class="bg-white rounded-xl shadow-luxury p-6 md:p-8 border border-slate-100">
              <h3 class="text-xl font-medium text-slate-900 mb-6 font-playfair">Visit Our Store</h3>
              <div class="flex flex-col md:flex-row gap-6">
                  <div class="flex-1 space-y-4 text-sm">
                      <div>
                          <p class="font-semibold text-slate-900 mb-1">Location</p>
                          <p class="text-slate-600">Kigali, Rwanda (city center area)</p>
                          <a href="https://maps.google.com" target="_blank" class="inline-flex items-center gap-1 text-[var(--gold)] mt-1 hover:underline">
                             <i class="la la-map-marker"></i> Get Directions
                          </a>
                      </div>
                      <div>
                          <p class="font-semibold text-slate-900 mb-1">Opening Hours</p>
                          <p class="text-slate-600">Mon–Sat: 09:00 – 20:00</p>
                          <p class="text-slate-600">Sun: 12:00 – 18:00</p>
                      </div>
                  </div>
                  <div class="flex-none">
                     <a href="tel:0780159059" class="block w-full text-center rounded-lg border border-slate-200 py-3 px-6 text-slate-700 font-medium hover:border-[var(--gold)] hover:text-[var(--gold)] transition-colors mb-2">
                        Call Us
                     </a>
                     <a href="https://wa.me/250780159059" target="_blank" class="block w-full text-center rounded-lg bg-[var(--gold)] py-3 px-6 text-white font-medium hover:opacity-90 transition-opacity">
                        WhatsApp Us
                     </a>
                  </div>
              </div>
          </div>

          <div class="bg-white rounded-xl shadow-luxury p-6 md:p-8 border border-slate-100" x-data="{ open: null }">
              <h3 class="text-xl font-medium text-slate-900 mb-6 font-playfair">Common Questions</h3>
              @php
                  $faq = [
                    ['q' => 'Do you deliver products in Kigali?', 'a' => 'Yes! We offer fast and affordable delivery across Kigali.'],
                    ['q' => 'Are your products genuine?', 'a' => 'Absolutely. We source from trusted distributors and stand behind every brand we stock.'],
                    ['q' => 'Do you have a physical shop?', 'a' => 'Yes, please see our location details to visit us in person.'],
                  ];
              @endphp
              <ul class="space-y-4">
                  @foreach($faq as $i => $f)
                  <li class="border-b border-slate-100 last:border-0 pb-4 last:pb-0">
                      <button type="button" class="flex w-full items-center justify-between text-left group"
                              @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                          <span class="text-sm font-medium text-slate-900 group-hover:text-[var(--gold)] transition-colors">{{ $f['q'] }}</span>
                          <span class="flex items-center justify-center w-6 h-6 rounded-full bg-slate-50 text-slate-400 group-hover:bg-[var(--gold)] group-hover:text-white transition-all">
                             <i class="la text-xs" :class="open === {{ $i }} ? 'la-minus' : 'la-plus'"></i>
                          </span>
                      </button>
                      <div x-show="open === {{ $i }}" x-collapse class="mt-3 text-xs md:text-sm text-slate-500 leading-relaxed">
                          {{ $f['a'] }}
                      </div>
                  </li>
                  @endforeach
              </ul>
          </div>

      </section>

    </div>
  </main>

  {{-- FOOTER --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- JSON-LD --}}
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Store",
    "name": "Diva House Beauty",
    "description": "Premium cosmetics and fashion in Kigali.",
    "url": "{{ url('/') }}",
    "telephone": "+250780159059",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Kigali",
      "addressCountry": "RW"
    }
  }
  </script>
</body>
</html>

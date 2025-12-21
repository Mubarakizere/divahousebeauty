<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Diva House Beauty</title>
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


{{-- ===================== HERO / WHAT WE DO ===================== --}}
@php
    // Friendly fallbacks in case ids ever change
    $catBeauty  = ($categories ?? collect())->firstWhere('id', 5)?->id ?? 5;
    $catFashion = ($categories ?? collect())->firstWhere('id', 15)?->id ?? 15;
@endphp

<main class="relative overflow-hidden">
  <section
    class="relative bg-white"
    x-data="{
      words: ['sell cosmetics', 'sell fashion', 'provide beauty services'],
      i: 0, txt: '', deleting: false,
      typeSpeed: 70, deleteSpeed: 40, pause: 900,
      tick(){
        const full = this.words[this.i % this.words.length];
        if(!this.deleting){
          this.txt = full.substring(0, this.txt.length + 1);
          if(this.txt === full){ this.deleting = true; setTimeout(()=>this.tick(), this.pause); return; }
          setTimeout(()=>this.tick(), this.typeSpeed);
        }else{
          this.txt = full.substring(0, this.txt.length - 1);
          if(this.txt === ''){ this.deleting = false; this.i++; }
          setTimeout(()=>this.tick(), this.deleteSpeed);
        }
      }
    }"
    x-init="tick()"
  >
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid lg:grid-cols-2 gap-8 items-center py-10 sm:py-14">

        {{-- Left: Copy + Type animation + CTAs --}}
        <div>
          <span class="inline-flex items-center gap-2 text-[12px] tracking-wide font-semibold uppercase text-[var(--gold)]">
            <i class="la la-star text-sm"></i> Diva House Beauty
          </span>

          <h1 class="mt-3 text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">
            We&nbsp;
            <span class="text-[var(--gold)]" x-text="txt"></span>
            <span class="ml-1 inline-block w-[2px] h-[1.35em] align-[-0.2em] bg-[var(--gold)] animate-pulse"></span>
          </h1>

          <p class="mt-4 text-slate-600 text-sm sm:text-base max-w-xl">
            Premium cosmetics, trend-forward fashion, and professional beauty services under one roof.
            Discover products you love, looks that fit your vibe, and services delivered with care.
          </p>

          <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('category', $catBeauty) }}"
               class="inline-flex items-center gap-2 rounded-md bg-[var(--gold)] px-4 py-2.5 text-sm font-semibold text-white hover:opacity-90">
              Shop Cosmetics <i class="la la-arrow-right text-base"></i>
            </a>
            <a href="{{ route('category', $catFashion) }}"
               class="inline-flex items-center gap-2 rounded-md border border-[var(--gold)] px-4 py-2.5 text-sm font-semibold text-[var(--gold)] hover:bg-[var(--gold)] hover:text-white">
              Shop Fashion
            </a>
            <a href="{{ route('booking.create') }}"
               class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
              Book a Service
            </a>
          </div>

          {{-- Small trust/benefits row --}}
          <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 text-[12px] text-slate-600">
            <div class="flex items-center gap-2">
              <i class="la la-shipping-fast text-[var(--gold)] text-lg"></i>
              Fast Kigali delivery
            </div>
            <div class="flex items-center gap-2">
              <i class="la la-badge-check text-[var(--gold)] text-lg"></i>
              Genuine products
            </div>
            <div class="flex items-center gap-2">
              <i class="la la-smile text-[var(--gold)] text-lg"></i>
              Pro beauty services
            </div>
          </div>
        </div>

        {{-- Right: Image collage (lazy) --}}
        <div class="relative">
          <div class="grid grid-cols-3 gap-3 lg:gap-4">
            <figure class="col-span-2 rounded-xl overflow-hidden shadow-ring">
              <img
                src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=1200&auto=format&fit=crop"
                alt="Cosmetics selection" loading="lazy" class="w-full h-64 sm:h-80 object-cover">
            </figure>
            <figure class="col-span-1 rounded-xl overflow-hidden shadow-ring">
              <img
                src="https://images.unsplash.com/photo-1520975916090-3105956dac38?q=80&w=800&auto=format&fit=crop"
                alt="Fashion look" loading="lazy" class="w-full h-64 sm:h-80 object-cover">
            </figure>
            <figure class="col-span-3 rounded-xl overflow-hidden shadow-ring">
              <img
                src="https://images.unsplash.com/photo-1519014816548-bf5fe059798b?q=80&w=1400&auto=format&fit=crop"
                alt="Beauty service in salon" loading="lazy" class="w-full h-48 sm:h-56 object-cover">
            </figure>
          </div>

          {{-- soft glow accent --}}
          <div class="pointer-events-none absolute -inset-6 -z-10 rounded-[2rem] bg-[var(--gold)]/10 blur-3xl"></div>
        </div>
      </div>

      {{-- Three quick feature cards --}}
      <div class="grid sm:grid-cols-3 gap-3 sm:gap-4 pb-10">
        <div class="rounded-lg bg-white shadow-ring p-4 flex items-start gap-3">
          <i class="la la-magic text-xl text-[var(--gold)]"></i>
          <div class="text-sm">
            <div class="font-semibold text-slate-900">Cosmetics</div>
            <div class="text-slate-500">Skincare, lashes, nails & more from trusted brands.</div>
          </div>
        </div>
        <div class="rounded-lg bg-white shadow-ring p-4 flex items-start gap-3">
          <i class="la la-tshirt text-xl text-[var(--gold)]"></i>
          <div class="text-sm">
            <div class="font-semibold text-slate-900">Fashion</div>
            <div class="text-slate-500">Curated looks, accessories, and everyday essentials.</div>
          </div>
        </div>
        <div class="rounded-lg bg-white shadow-ring p-4 flex items-start gap-3">
          <i class="la la-cut text-xl text-[var(--gold)]"></i>
          <div class="text-sm">
            <div class="font-semibold text-slate-900">Beauty Services</div>
            <div class="text-slate-500">Book lashes, nails, hair & spa—delivered professionally.</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

{{-- ============== HELPERS ============== --}}
@php
    use App\Models\Category;

    // --- Safe fallbacks so /home2 can render even without controller data ---
    $brandLatestAll = (isset($brandLatestAll) && $brandLatestAll instanceof \Illuminate\Support\Collection)
        ? $brandLatestAll
        : \App\Models\Brand::with(['category','latestProduct' => fn($q)=>$q->with('category')])
            ->has('latestProduct')
            ->get()
            ->sortByDesc(fn($b)=>optional($b->latestProduct)->created_at)
            ->values()
            ->take(12);

    $brandLatest5 = (isset($brandLatest5) && $brandLatest5 instanceof \Illuminate\Support\Collection)
        ? $brandLatest5
        : \App\Models\Brand::with(['category','latestProduct' => fn($q)=>$q->with('category')->where('category_id',5)])
            ->where('category_id',5)
            ->has('latestProduct')
            ->get()
            ->sortByDesc(fn($b)=>optional($b->latestProduct)->created_at)
            ->values()
            ->take(12);

    $brandLatest15 = (isset($brandLatest15) && $brandLatest15 instanceof \Illuminate\Support\Collection)
        ? $brandLatest15
        : \App\Models\Brand::with(['category','latestProduct' => fn($q)=>$q->with('category')->where('category_id',15)])
            ->where('category_id',15)
            ->has('latestProduct')
            ->get()
            ->sortByDesc(fn($b)=>optional($b->latestProduct)->created_at)
            ->values()
            ->take(12);

    // Read actual category names (fallbacks kept for preview)
    $catNames   = Category::whereIn('id', [5, 15])->pluck('name','id');
    $cat5Title  = $cat5Title  ?? ($catNames[5]  ?? 'Beauty & Personal Care');
    $cat15Title = $cat15Title ?? ($catNames[15] ?? 'Fashion');
@endphp

{{-- 1) All brands (one newest per brand) --}}
@include('partials.brand_slider', [
  'title'      => 'Trending Now   ',
  'subtitle'   => 'Fresh arrivals across all brands.',
  'collection' => $brandLatestAll
])

{{-- 2) Category 5 (Beauty & Personal Care) --}}
@include('partials.brand_slider', [
  'title'      => $cat5Title.'  New ',
  'subtitle'   => 'Newest uploads.',
  'collection' => $brandLatest5
])

{{-- 3) Category 15 (Fashion) — ensure at least 10 items --}}
@include('partials.brand_slider', [
  'title'               => $cat15Title.' New ',
  'subtitle'            => 'Newest uploads .',
  'collection'          => $brandLatest15,
  'minCount'            => 10,
  'fallbackCategoryId'  => 15,
])

@include('partials.services_grid')
@include('partials.whyus')
@include('partials.footer')
  {{-- ===================== AUTH MODAL (reuse Home2 if you extracted) ===================== --}}
  @includeIf('partials.auth_modal')


</body>
</html>

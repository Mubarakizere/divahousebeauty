<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Diva House Beauty — Home2</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind + Alpine (CDN for preview) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Line Awesome --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  <style>
    :root{ --gold:#cc9966; --black:#111827; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    .no-scrollbar::-webkit-scrollbar{display:none} .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
    .nav-pill{ border-radius:9999px }
    [x-cloak]{ display:none !important; }
  </style>
</head>
<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ 
        authOpen: {{ $errors->any() ? 'true':'false' }}, 
        authTab: '{{ old('_tab','signin') }}',
        showPwdIn:false, showPwdUp:false
      }"
      @keydown.escape.window="authOpen=false">

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
@include('partials.currency_modal')
@include('partials.footer')
{{-- ===================== AUTH MODAL (Sign In / Register) ===================== --}}
<div x-show="authOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40"></div>

<section x-show="authOpen" x-transition
         role="dialog" aria-modal="true" aria-labelledby="authModalTitle"
         class="fixed inset-0 z-50 grid place-items-end md:place-items-center p-0 md:p-4">
  <div class="w-full md:max-w-lg bg-white border border-slate-100 shadow-ring rounded-t-2xl md:rounded-xl
              max-h-[calc(100vh-20px)] md:max-h-[90vh] overflow-hidden pb-[env(safe-area-inset-bottom)]">
    <div class="relative flex items-center justify-between px-4 py-3 border-b border-slate-200">
      <h3 id="authModalTitle" class="text-base sm:text-lg font-semibold text-[var(--black)]">
        Welcome to Diva House Beauty
      </h3>
      <button @click="authOpen=false" class="p-2 -mr-1 text-slate-400 hover:text-slate-600">
        <i class="la la-close text-xl"></i><span class="sr-only">Close</span>
      </button>
    </div>

    <div class="flex border-b border-slate-200">
      <button @click="authTab='signin'"
              :class="authTab==='signin' ? 'text-[var(--gold)] border-[var(--gold)]' : 'text-slate-500 border-transparent'"
              class="flex-1 px-4 py-2 text-sm font-semibold border-b-2">Sign In</button>
      <button @click="authTab='register'"
              :class="authTab==='register' ? 'text-[var(--gold)] border-[var(--gold)]' : 'text-slate-500 border-transparent'"
              class="flex-1 px-4 py-2 text-sm font-semibold border-b-2">Register</button>
    </div>

    <div class="px-4 sm:px-6 py-4 overflow-y-auto" style="max-height: 70vh;">

      {{-- Sign In --}}
      <div x-show="authTab==='signin'">
        @if ($errors->any() && old('_tab','signin')==='signin')
          <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
            <i class="la la-exclamation-triangle mr-1"></i> There were issues with your sign in.
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
          @csrf
          <input type="hidden" name="_tab" value="signin">
          <div>
            <label for="signin-email" class="block text-sm font-medium text-slate-700">Email *</label>
            <input id="signin-email" name="email" type="email" autocomplete="email" required
                   value="{{ old('email') }}"
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none
                          focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/30
                          @error('email') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="signin-password" class="block text-sm font-medium text-slate-700">Password *</label>
            <div class="mt-1 relative">
              <input :type="showPwdIn ? 'text':'password'"
                     id="signin-password" name="password" autocomplete="current-password" required
                     class="w-full rounded-md border border-slate-300 px-3 py-2 pr-10 text-sm outline-none
                            focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/30
                            @error('password') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
              <button type="button" @click="showPwdIn=!showPwdIn"
                      class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600">
                <i :class="showPwdIn ? 'la la-eye-slash' : 'la la-eye'"></i><span class="sr-only">Toggle password</span>
              </button>
            </div>
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div class="flex items-center justify-between flex-wrap gap-2">
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                     class="rounded border-slate-300 text-[var(--gold)] focus:ring-[var(--gold)]/40">
              Remember Me
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-slate-600 hover:text-[var(--gold)]">
              Forgot Password?
            </a>

            <button type="submit"
                    class="ml-auto inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm font-semibold
                           border-[var(--gold)] text-[var(--gold)]
                           hover:bg-[var(--gold)] hover:text-white transition">
              LOG IN <i class="la la-arrow-right text-base"></i>
            </button>
          </div>
        </form>

        <div class="mt-5">
          <p class="text-center text-xs text-slate-500 mb-2">or sign in with</p>
          <a href="{{ route('google-auth') }}"
             class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50">
            <i class="la la-google text-red-500"></i> Login with Google
          </a>
        </div>
      </div>

      {{-- Register --}}
      <div x-show="authTab==='register'">
        @if ($errors->any() && old('_tab')==='register')
          <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
            <i class="la la-exclamation-triangle mr-1"></i> There were issues with your registration.
          </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
          @csrf
          <input type="hidden" name="_tab" value="register">

          <div>
            <label for="register-name" class="block text-sm font-medium text-slate-700">Your Name *</label>
            <input id="register-name" name="name" type="text" autocomplete="name" required
                   value="{{ old('name') }}"
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none
                          focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/30
                          @error('name') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="register-email" class="block text-sm font-medium text-slate-700">Your Email *</label>
            <input id="register-email" name="email" type="email" autocomplete="email" required
                   value="{{ old('email') }}"
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none
                          focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/30
                          @error('email') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="register-password-2" class="block text-sm font-medium text-slate-700">Password *</label>
            <div class="mt-1 relative">
              <input :type="showPwdUp ? 'text':'password'"
                     id="register-password-2" name="password" autocomplete="new-password" required
                     class="w-full rounded-md border border-slate-300 px-3 py-2 pr-10 text-sm outline-none
                            focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/30
                            @error('password') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror"
                     aria-describedby="password-help">
              <button type="button" @click="showPwdUp=!showPwdUp"
                      class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600">
                <i :class="showPwdUp ? 'la la-eye-slash' : 'la la-eye'"></i><span class="sr-only">Toggle password</span>
              </button>
            </div>
            <small id="password-help" class="block mt-1 text-xs text-slate-500">
              Use 8+ chars with upper, lower, number & symbol.
            </small>
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="register-password-confirm" class="block text sm font-medium text-slate-700">Confirm Password *</label>
            <input id="register-password-confirm" name="password_confirmation" type="password" autocomplete="new-password" required
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none
                          focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/30">
          </div>

          <div class="flex items-center justify-between flex-wrap gap-2">
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" required
                     class="rounded border-slate-300 text-[var(--gold)] focus:ring-[var(--gold)]/40">
              I agree to the <a href="#" class="text-[var(--gold)] hover:underline">privacy policy</a> *
            </label>

            <button type="submit"
                    class="ml-auto inline-flex items-center gap-2 rounded-md border px-4 py-2 text-sm font-semibold
                           border-[var(--gold)] text-[var(--gold)]
                           hover:bg-[var(--gold)] hover:text-white transition">
              SIGN UP <i class="la la-arrow-right text-base"></i>
            </button>
          </div>
        </form>

        <div class="mt-5">
          <p class="text-center text-xs text-slate-500 mb-2">or continue with</p>
          <a href="{{ route('google-auth') }}"
             class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50">
            <i class="la la-google text-red-500"></i> Continue with Google
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Create Account — Diva House Beauty</title>
  <meta name="description" content="Create your Diva House Beauty account to enjoy luxury cosmetics, fast Kigali shipping, and exclusive rewards across East Africa.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Tailwind CSS & Alpine JS --}}
  <script>
    tailwind = { config: { corePlugins: { preflight: true } } }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Line Awesome Icons --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>

  {{-- Google Fonts: Playfair Display + Inter --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

  {{-- Favicon --}}
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/icons/favicon-32x32.png') }}">

  <style>
    :root { 
      --gold: #C5A059;
      --gold-dark: #A8833E;
      --black: #0F172A;
    }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; }
    [x-cloak] { display: none !important; }

    .btn-gold {
      background-color: var(--gold);
      color: #ffffff;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-gold:hover {
      background-color: var(--gold-dark);
      box-shadow: 0 10px 15px -3px rgba(197, 160, 89, 0.3);
      transform: translateY(-1px);
    }
  </style>
</head>

<body class="h-full text-slate-800 antialiased selection:bg-[#C5A059] selection:text-white">
  <div class="min-h-screen flex">
    
    {{-- ===================== LEFT SIDE: BRAND VISUAL PANEL ===================== --}}
    <div class="hidden lg:flex lg:w-1/2 relative text-white flex-col justify-between p-12 overflow-hidden bg-cover bg-center"
         style="background-image: url('{{ asset('assets/images/demos/demo-14/slider/slide-2.jpg') }}');">
      
      {{-- Dark Overlay Gradients --}}
      <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A] via-[#0F172A]/85 to-[#0F172A]/70 z-0"></div>
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#C5A059]/20 rounded-full blur-3xl"></div>

      {{-- Header Logo (Footer/White Version for dark background) --}}
      <div class="relative z-10">
        <a href="{{ route('home') }}" class="inline-block">
          <img src="{{ asset('assets/images/demos/demo-14/logo-footer.png') }}" alt="Diva House Beauty" class="h-10 w-auto">
        </a>
      </div>

      {{-- Hero Text & Value Props --}}
      <div class="relative z-10 max-w-lg my-auto py-12">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold tracking-wider uppercase bg-[#C5A059]/20 text-[#D4AF37] border border-[#C5A059]/30 mb-6 backdrop-blur-sm">
          <i class="la la-user-plus"></i> Join Diva House
        </span>
        <h1 class="text-4xl xl:text-5xl font-serif leading-tight mb-6 text-white drop-shadow-md">
          Start Your Journey to Radiant Beauty
        </h1>
        <p class="text-slate-200 text-base leading-relaxed mb-8 font-light">
          Join thousands of beauty enthusiasts across East Africa. Create an account to enjoy seamless checkout, order tracking, and VIP privilege access.
        </p>

        {{-- Bullet Features --}}
        <div class="space-y-4">
          <div class="flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center shrink-0 text-[#D4AF37] border border-white/10">
              <i class="la la-heart text-xl"></i>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-white">Curated Wishlists</h4>
              <p class="text-xs text-slate-300">Save your favorite cosmetics, fragrances, and hair products for later.</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center shrink-0 text-[#D4AF37] border border-white/10">
              <i class="la la-truck text-xl"></i>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-white">Live Order Tracking</h4>
              <p class="text-xs text-slate-300">Track every stage of your shipment from Kigali dispatch to your door.</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-white/10 backdrop-blur-md flex items-center justify-center shrink-0 text-[#D4AF37] border border-white/10">
              <i class="la la-shield-alt text-xl"></i>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-white">Safe & Secure Payment</h4>
              <p class="text-xs text-slate-300">Supports Mobile Money (MTN & Airtel), Card Payment, and Cash on Delivery.</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Footer / Copyright --}}
      <div class="relative z-10 flex items-center justify-between text-xs text-slate-300 border-t border-white/15 pt-6">
        <p>&copy; {{ date('Y') }} Diva House Beauty. All rights reserved.</p>
        <a href="{{ route('home') }}" class="hover:text-white transition-colors flex items-center gap-1 font-medium">
          <i class="la la-arrow-left"></i> Back to Store
        </a>
      </div>
    </div>

    {{-- ===================== RIGHT SIDE: FORM PANEL ===================== --}}
    <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-white overflow-y-auto">
      
      {{-- Top Nav Bar --}}
      <div class="flex items-center justify-between mb-8">
        <a href="{{ route('home') }}" class="flex items-center">
          <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" alt="Diva House Beauty" class="h-8 w-auto">
        </a>
        <div class="text-xs sm:text-sm text-slate-500 ml-auto">
          Already have an account? 
          <a href="{{ route('login') }}" class="font-semibold text-[#C5A059] hover:underline ml-1">
            Sign In
          </a>
        </div>
      </div>

      {{-- Form Container --}}
      <div class="max-w-md w-full mx-auto my-auto py-4">
        
        {{-- Header Title --}}
        <div class="mb-8">
          <h2 class="text-2xl sm:text-3xl font-serif text-[var(--black)] mb-2">
            Create Your Account
          </h2>
          <p class="text-sm text-slate-500">
            Fill in your details below to set up your Diva House Beauty membership.
          </p>
        </div>

        {{-- Auth Tabs --}}
        <div class="flex border-b border-slate-200 mb-6">
          <a href="{{ route('login') }}" class="flex-1 py-3 text-center font-medium text-sm border-b-2 border-transparent text-slate-400 hover:text-slate-600 transition-colors">
            <i class="la la-sign-in mr-1 text-base"></i> Sign In
          </a>
          <a href="{{ route('register') }}" class="flex-1 py-3 text-center font-semibold text-sm border-b-2 border-[#C5A059] text-[#C5A059]">
            <i class="la la-user-plus mr-1 text-base"></i> Create Account
          </a>
        </div>

        {{-- Alert Messages --}}
        @if ($errors->any())
          <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <div class="font-semibold mb-1 flex items-center gap-2">
              <i class="la la-exclamation-triangle text-lg"></i> Please fix the following errors:
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Register Form --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ showPassword: false, showConfirmPassword: false }">
          @csrf

          {{-- Full Name Field --}}
          <div>
            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
              Full Name <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="la la-user text-lg"></i>
              </div>
              <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                     placeholder="John Doe"
                     class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl text-sm outline-none transition-all
                            focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20
                            @error('name') border-red-400 bg-red-50/30 @enderror">
            </div>
            @error('name')
              <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                <i class="la la-exclamation-circle"></i> {{ $message }}
              </p>
            @enderror
          </div>

          {{-- Email Address Field --}}
          <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
              Email Address <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="la la-envelope text-lg"></i>
              </div>
              <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                     placeholder="yourname@example.com"
                     class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl text-sm outline-none transition-all
                            focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20
                            @error('email') border-red-400 bg-red-50/30 @enderror">
            </div>
            @error('email')
              <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                <i class="la la-exclamation-circle"></i> {{ $message }}
              </p>
            @enderror
          </div>

          {{-- Password Field --}}
          <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
              Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="la la-lock text-lg"></i>
              </div>
              <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required autocomplete="new-password"
                     placeholder="Minimum 8 characters"
                     class="w-full pl-10 pr-12 py-3 border border-slate-300 rounded-xl text-sm outline-none transition-all
                            focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20
                            @error('password') border-red-400 bg-red-50/30 @enderror">
              <button type="button" @click="showPassword = !showPassword"
                      class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                <i class="la text-lg" :class="showPassword ? 'la-eye-slash' : 'la-eye'"></i>
              </button>
            </div>
            @error('password')
              <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                <i class="la la-exclamation-circle"></i> {{ $message }}
              </p>
            @enderror
          </div>

          {{-- Password Confirmation Field --}}
          <div>
            <label for="password-confirm" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
              Confirm Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="la la-shield-alt text-lg"></i>
              </div>
              <input :type="showConfirmPassword ? 'text' : 'password'" id="password-confirm" name="password_confirmation" required autocomplete="new-password"
                     placeholder="Re-enter password"
                     class="w-full pl-10 pr-12 py-3 border border-slate-300 rounded-xl text-sm outline-none transition-all
                            focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20">
              <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                <i class="la text-lg" :class="showConfirmPassword ? 'la-eye-slash' : 'la-eye'"></i>
              </button>
            </div>
          </div>

          {{-- Submit Button --}}
          <button type="submit"
                  class="btn-gold w-full py-3.5 px-4 rounded-xl font-semibold text-sm uppercase tracking-wider flex items-center justify-center gap-2 shadow-md mt-6">
            <span>Create Account</span>
            <i class="la la-arrow-right text-base"></i>
          </button>
        </form>

        {{-- Mobile Back Link --}}
        <div class="mt-8 text-center lg:hidden">
          <a href="{{ route('home') }}" class="text-xs text-slate-500 hover:text-slate-800 inline-flex items-center gap-1">
            <i class="la la-arrow-left"></i> Back to Store
          </a>
        </div>

      </div>

      {{-- Footer links --}}
      <div class="text-center text-xs text-slate-400 pt-6">
        By registering, you agree to Diva House Beauty's 
        <a href="{{ route('about') }}" class="underline hover:text-slate-600">Terms of Service</a> & 
        <a href="{{ route('contact') }}" class="underline hover:text-slate-600">Privacy Policy</a>.
      </div>

    </div>

  </div>
</body>
</html>

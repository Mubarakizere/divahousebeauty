<!DOCTYPE html>
<html lang="en">
@php
    $categories = $categories ?? \App\Models\Category::select('id','name','slug')->get();
    $count      = $count ?? (auth()->id() ? \App\Models\Cart::where('users_id', auth()->id())->count() : 0);
    $toastTypes = ['success', 'error', 'info', 'warning', 'message'];
@endphp
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Contact Us — Diva House Beauty</title>
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

  {{-- Hero --}}
  <section class="relative">
    <div class="relative">
      <img src="{{ asset('assets/images/page-header-bg.jpg') }}" alt="Contact"
           class="h-48 w-full object-cover md:h-56 lg:h-64">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-black/0"></div>
    </div>
    <div class="absolute inset-0 flex items-end">
      <div class="mx-auto w-full max-w-7xl px-3 sm:px-4 pb-4 md:pb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">
          Contact <span class="text-[var(--gold)]">Us</span>
        </h1>
        <nav class="mt-1 text-xs text-white/80">
          <a href="{{ route('home') }}" class="hover:text-[var(--gold)]">Home</a>
          <span class="mx-1">/</span>
          <span class="text-white">Contact</span>
        </nav>
      </div>
    </div>
  </section>

  {{-- Content --}}
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <div class="grid grid-cols-12 gap-4 lg:gap-6">
        {{-- Left: Form --}}
        <section class="col-span-12 lg:col-span-7">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
            <div class="mb-4 flex items-center gap-2">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[var(--gold)] text-white">
                <i class="la la-envelope text-xl"></i>
              </span>
              <h2 class="text-lg md:text-xl font-semibold text-slate-900">Send us a message</h2>
            </div>

            @if ($errors->any())
              <div class="mb-4 rounded-lg border border-rose-300 bg-rose-50 p-3 text-rose-800 text-sm">
                Please fix the errors below and try again.
              </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
              @csrf

              {{-- Honeypot (must stay empty) --}}
              <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" />

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="name" class="text-sm text-slate-700">Your Name</label>
                  <input id="name" name="name" value="{{ old('name') }}" required
                         class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[var(--gold)]/20 focus:border-[var(--gold)]">
                  @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                  <label for="email" class="text-sm text-slate-700">Email</label>
                  <input id="email" name="email" type="email" value="{{ old('email') }}" required
                         class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[var(--gold)]/20 focus:border-[var(--gold)]">
                  @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
              </div>

              <div>
                <label for="subject" class="text-sm text-slate-700">Subject (optional)</label>
                <input id="subject" name="subject" value="{{ old('subject') }}"
                       class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[var(--gold)]/20 focus:border-[var(--gold)]">
                @error('subject') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label for="message" class="text-sm text-slate-700">Message</label>
                <textarea id="message" name="message" rows="6" required
                          class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[var(--gold)]/20 focus:border-[var(--gold)]">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
              </div>

              <div class="pt-2 flex flex-wrap items-center gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-md bg-[var(--gold)] px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
                  <i class="la la-paper-plane text-lg"></i> Send Message
                </button>
                <a href="https://wa.me/250780159059?text={{ urlencode('Hello Diva House Beauty! I have a question.') }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-md border border-emerald-300 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                  <i class="la la-whatsapp text-lg"></i> Chat on WhatsApp
                </a>
              </div>
            </form>
          </div>
        </section>

        {{-- Right: Info --}}
        <aside class="col-span-12 lg:col-span-5">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
            <h3 class="text-base md:text-lg font-semibold text-slate-900">Contact Details</h3>
            <ul class="mt-3 space-y-3 text-sm">
              <li class="flex items-start gap-3">
                <i class="la la-phone text-[var(--gold)] text-xl"></i>
                <div>
                  <div class="font-medium text-slate-900">Phone</div>
                  <a href="tel:0780159059" class="text-slate-600 hover:text-[var(--gold)]">+250 780 159 059</a>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <i class="la la-envelope text-[var(--gold)] text-xl"></i>
                <div>
                  <div class="font-medium text-slate-900">Email</div>
                  <a href="mailto:info@divahousebeauty.com" class="text-slate-600 hover:text-[var(--gold)]">info@divahousebeauty.com</a>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <i class="la la-map-marker text-[var(--gold)] text-xl"></i>
                <div>
                  <div class="font-medium text-slate-900">Location</div>
                  <p class="text-slate-600">Kigali, Rwanda</p>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <i class="la la-clock text-[var(--gold)] text-xl"></i>
                <div>
                  <div class="font-medium text-slate-900">Hours</div>
                  <p class="text-slate-600">Mon–Sat: 09:00–20:00 • Sun: 12:00–18:00</p>
                </div>
              </li>
            </ul>

            <img src="{{ asset('assets/images/demos/demo-14/bg-1.jpg') }}"
                 alt="" class="mt-4 h-40 w-full rounded-xl object-cover">

            <div class="mt-4 flex items-center gap-2 text-[var(--gold)]">
              <a href="#" class="hover:opacity-80"><i class="la la-facebook text-2xl"></i></a>
              <a href="#" class="hover:opacity-80"><i class="la la-instagram text-2xl"></i></a>
              <a href="#" class="hover:opacity-80"><i class="la la-youtube text-2xl"></i></a>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>

  {{-- Footer (Tailwind partial) --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- SweetAlert toasts --}}
  @foreach ($toastTypes as $type)
    @if (session()->has($type))
      <script>
        const Toast = Swal.mixin({
          toast: true, position: 'top-right', iconColor: 'white',
          customClass: { popup: 'colored-toast' }, showConfirmButton: false,
          timer: 3500, timerProgressBar: true,
        });
        (async () => {
          await Toast.fire({
            icon: '{{ $type === "message" ? "success" : $type }}',
            title: @json(session($type)),
          });
        })();
      </script>
    @endif
  @endforeach

  {{-- SEO JSON-LD --}}
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": ["Organization","BeautySalon"],
    "name": "Diva House Beauty",
    "url": "{{ url('/') }}",
    "email": "info@divahousebeauty.com",
    "telephone": "+250780159059",
    "address": {"@type":"PostalAddress","addressLocality":"Kigali","addressCountry":"RW"}
  }
  </script>
</body>
</html>

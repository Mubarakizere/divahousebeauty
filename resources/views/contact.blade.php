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
  {{-- Fallback local if needed --}}
  {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/> --}}

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

    /* Custom Input Styles */
    .form-input-luxury {
        padding: 0.75rem 1rem;
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        width: 100%;
        transition: all 0.2s;
    }
    .form-input-luxury:focus {
        border-color: var(--gold);
        outline: none;
        box-shadow: 0 0 0 1px var(--gold);
    }
    
    /* Social Buttons */
    .social-btn {
        width: 2.5rem; height: 2.5rem;
        display: flex; align-items: center; justify-content: center;
        border-radius: 9999px; color: white;
        transition: opacity 0.2s;
    }
    .social-btn:hover { opacity: 0.9; }
    .bg-facebook { background-color: #3b5998; }
    .bg-instagram { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); }
    .bg-whatsapp { background-color: #25D366; }
    .bg-youtube { background-color: #FF0000; }
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
      <img src="{{ asset('assets/images/page-header-bg.jpg') }}" alt="Contact"
           class="h-full w-full object-cover opacity-60">
      <div class="absolute inset-0 bg-black/40"></div>
      
      <div class="absolute inset-0 flex flex-col justify-center px-6 md:px-12 text-center text-white">
        <h1 class="text-3xl md:text-5xl font-medium tracking-tight mb-2">
            Contact <span class="text-[var(--gold)] italic">Us</span>
        </h1>
        <nav class="flex justify-center text-sm md:text-base text-white/80 space-x-2">
            <a href="{{ route('home') }}" class="hover:text-[var(--gold)] transition-colors">Home</a>
            <span>/</span>
            <span>Contact</span>
        </nav>
      </div>
    </div>
  </section>

  {{-- CONTENT --}}
  <main class="py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
        
        {{-- LEFT: FORM --}}
        <section class="lg:col-span-7">
          <div class="bg-white rounded-xl shadow-luxury p-6 md:p-10 border border-slate-100">
            <div class="mb-8">
                <h2 class="text-2xl md:text-3xl text-slate-900 mb-2">Get in Touch</h2>
                <p class="text-slate-500 text-sm md:text-base">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>

            @if ($errors->any())
              <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-800 text-sm">
                Please fix the errors below and try again.
              </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
              @csrf
              <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" />

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Your Name</label>
                  <input id="name" name="name" value="{{ old('name') }}" required
                         class="form-input-luxury" placeholder="John Doe">
                  @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                  <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                  <input id="email" name="email" type="email" value="{{ old('email') }}" required
                         class="form-input-luxury" placeholder="john@example.com">
                  @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
              </div>

              <div>
                <label for="subject" class="block text-sm font-medium text-slate-700 mb-1">Subject <span class="text-slate-400 font-normal">(Optional)</span></label>
                <input id="subject" name="subject" value="{{ old('subject') }}"
                       class="form-input-luxury" placeholder="How can we help?">
                @error('subject') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label for="message" class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                <textarea id="message" name="message" rows="5" required
                          class="form-input-luxury resize-y" placeholder="Write your message here...">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
              </div>

              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-[var(--black)] px-8 py-3 text-sm font-medium text-white hover:bg-slate-800 transition-colors shadow-lg shadow-slate-200">
                  <span>Send Message</span>
                  <i class="la la-long-arrow-right text-lg"></i>
                </button>

                <div class="hidden sm:block text-slate-300">|</div>

                <a href="https://wa.me/250780159059?text={{ urlencode('Hello Diva House Beauty! I have a question.') }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium text-sm transition-colors">
                  <i class="la la-whatsapp text-xl"></i>
                  <span>Chat on WhatsApp</span>
                </a>
              </div>
            </form>
          </div>
        </section>

        {{-- RIGHT: INFO --}}
        <aside class="lg:col-span-5 space-y-8">
          
          {{-- Item 1: Contact Info --}}
          <div class="bg-white rounded-xl shadow-luxury p-6 md:p-8 border border-slate-100">
            <h3 class="text-xl font-medium text-slate-900 mb-6">Contact Information</h3>
            
            <ul class="space-y-6">
              <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] flex items-center justify-center">
                    <i class="la la-phone text-xl"></i>
                </div>
                <div>
                  <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-1">Phone / WhatsApp</div>
                  <a href="tel:0780159059" class="text-lg text-slate-900 hover:text-[var(--gold)] font-playfair transition-colors">+250 780 159 059</a>
                </div>
              </li>

              <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] flex items-center justify-center">
                    <i class="la la-envelope text-xl"></i>
                </div>
                <div>
                  <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-1">Email</div>
                  <a href="mailto:info@divahousebeauty.com" class="text-lg text-slate-900 hover:text-[var(--gold)] font-playfair transition-colors">info@divahousebeauty.com</a>
                </div>
              </li>

              <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] flex items-center justify-center">
                    <i class="la la-map-marker text-xl"></i>
                </div>
                <div>
                  <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-1">Location</div>
                  <p class="text-lg text-slate-900 font-playfair">Kigali, Rwanda</p>
                </div>
              </li>

              <li class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[var(--gold)]/10 text-[var(--gold)] flex items-center justify-center">
                    <i class="la la-clock text-xl"></i>
                </div>
                <div>
                  <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-1">Hours</div>
                  <p class="text-sm text-slate-700">Mon–Sat: 09:00 – 20:00<br>Sun: 12:00 – 18:00</p>
                </div>
              </li>
            </ul>
          </div>

          {{-- Item 2: Socials & Image --}}
          <div class="bg-white rounded-xl shadow-luxury p-6 md:p-8 border border-slate-100">
             <div class="relative rounded-lg overflow-hidden h-40 mb-6">
                <img src="{{ asset('assets/images/demos/demo-14/bg-1.jpg') }}" alt="Beauty" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/20"></div>
             </div>
             
             <h3 class="text-lg font-medium text-slate-900 mb-4">Follow Us</h3>
             <div class="flex items-center gap-3">
               <a href="#" class="social-btn bg-facebook" title="Facebook">
                 <i class="lab la-facebook-f text-lg"></i>
               </a>
               <a href="#" class="social-btn bg-instagram" title="Instagram">
                 <i class="lab la-instagram text-lg"></i>
               </a>
               <a href="#" class="social-btn bg-whatsapp" title="WhatsApp">
                 <i class="lab la-whatsapp text-xl"></i>
               </a>
               <a href="#" class="social-btn bg-youtube" title="YouTube">
                 <i class="lab la-youtube text-xl"></i>
               </a>
             </div>
          </div>

        </aside>
      </div>
    </div>
  </main>

  {{-- FOOTER --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- SweetAlert --}}
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

  {{-- JSON-LD --}}
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

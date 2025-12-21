<footer class="relative bg-[#0b0f1a] text-white">
  {{-- subtle top border glow --}}
  <div class="pointer-events-none absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-[var(--gold)]/60 to-transparent"></div>

  {{-- ================= Newsletter band ================= --}}
  <section class="bg-[rgba(255,255,255,0.03)] border-b border-white/10">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-10"
         x-data="newsletter()" x-init="init()">

      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
          <h3 class="text-2xl font-bold">Join our newsletter</h3>
          <p class="mt-1 text-white/70 text-sm">
            Get new arrivals, promos & exclusive coupons  no spam.
          </p>
        </div>

        <form @submit.prevent="submit"
              class="w-full lg:w-[520px]">
          <div class="flex rounded-xl overflow-hidden ring-1 ring-white/15 bg-white/5 backdrop-blur">
            <input type="email" x-model.trim="email" required
                   placeholder="Enter your email address"
                   class="w-full bg-transparent px-4 py-3 text-sm outline-none placeholder:text-white/50">
            {{-- honeypot for bots --}}
            <input type="text" x-ref="honeypot" class="hidden" tabindex="-1" autocomplete="off">
            <button type="submit"
                    :disabled="busy"
                    class="shrink-0 px-5 py-3 text-sm font-semibold bg-[var(--gold)] text-black hover:opacity-90 disabled:opacity-60">
              <span x-show="!busy">Subscribe</span>
              <span x-show="busy">…</span>
            </button>
          </div>

          {{-- status line --}}
          <div class="mt-2 text-xs"
               :class="status==='success' ? 'text-green-400' : (status==='error' ? 'text-red-400' : 'text-white/60')"
               x-text="message"></div>
        </form>
      </div>
    </div>
  </section>

  {{-- ================= Footer main ================= --}}
  <section class="mx-auto max-w-7xl px-3 sm:px-4 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
    {{-- Brand / About --}}
    <div>
      <a href="{{ route('home') }}" class="inline-block">
        <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" alt="Diva House Beauty" class="h-8 w-auto">
      </a>
      <p class="mt-3 text-sm text-white/70 leading-relaxed">
        Beauty services and premium shopping in one place  lashes, makeup, hair, nails, and more.
      </p>

      <ul class="mt-5 space-y-2 text-sm">
        <li class="flex items-start gap-2">
          {{-- map-pin --}}
          <svg class="w-4 h-4 mt-0.5 text-[var(--gold)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-5.4-7-11a7 7 0 1114 0c0 5.6-7 11-7 11z"/><circle cx="12" cy="10" r="3"/></svg>
          <span class="text-white/80">Kigali, Rwanda</span>
        </li>
        <li class="flex items-start gap-2">
          {{-- phone --}}
          <svg class="w-4 h-4 mt-0.5 text-[var(--gold)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92V21a2 2 0 01-2.18 2A19.86 19.86 0 013 5.18 2 2 0 015 3h4.09a1 1 0 011 .75l1.12 4.49a1 1 0 01-.27.95L9.91 10.91a16 16 0 006.18 6.18l1.72-1.72a1 1 0 01.95-.27l4.49 1.12a1 1 0 01.75 1z"/></svg>
          <a href="tel:+250780159059" class="hover:text-[var(--gold)]">+250 780 159 059</a>
        </li>
        <li class="flex items-start gap-2">
          {{-- mail --}}
          <svg class="w-4 h-4 mt-0.5 text-[var(--gold)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
          <a href="mailto:hello@divahousebeauty.com" class="hover:text-[var(--gold)]">info@divahousebeauty.com</a>
        </li>
      </ul>

      <div class="mt-6">
        <p class="text-xs text-white/50">Secure payments</p>
        <img src="{{ asset('assets/images/payments.png') }}" alt="Payments" class="mt-2 h-6 opacity-80">
      </div>
    </div>

    {{-- Shop / Info --}}
    <div>
      <h4 class="text-sm font-semibold text-white tracking-wide">Shop & Info</h4>
      <ul class="mt-4 space-y-2 text-sm text-white/75">
        <li><a class="hover:text-[var(--gold)]" href="{{ route('about') }}">About</a></li>
        <li><a class="hover:text-[var(--gold)]" href="{{ route('contact') }}">Contact</a></li>
        <li><a class="hover:text-[var(--gold)]" href="{{ route('blog') }}">Services & Pricing</a></li>
        <li><a class="hover:text-[var(--gold)]" href="{{ route('about') }}">How to shop</a></li>
        <li><a class="hover:text-[var(--gold)]" href="{{ route('about') }}">FAQ</a></li>
      </ul>
    </div>

    {{-- Services quick links (to Booking) --}}
    <div>
      <h4 class="text-sm font-semibold text-white tracking-wide">Beauty Services</h4>
      <ul class="mt-4 space-y-2 text-sm text-white/75">
        @foreach (['Lashes','Massage','Aesthetic & Wellness','Hairstyle','Barber','MakeUp','Nails','Tattoo'] as $s)
          <li><a class="hover:text-[var(--gold)]" href="{{ route('booking.create') }}">{{ $s }}</a></li>
        @endforeach
      </ul>
    </div>

    {{-- My account --}}
    <div>
      <h4 class="text-sm font-semibold text-white tracking-wide">My Account</h4>
      <ul class="mt-4 space-y-2 text-sm text-white/75">
        <li><a class="hover:text-[var(--gold)]" href="{{ route('cart') }}">View Cart</a></li>
        <li><a class="hover:text-[var(--gold)]" href="#">Wishlist</a></li>
        <li><a class="hover:text-[var(--gold)]" href="#">Track my order</a></li>
        <li><a class="hover:text-[var(--gold)]" href="#}">Help</a></li>
      </ul>

      {{-- Socials --}}
      <div class="mt-6 flex items-center gap-3">
        @php
          $socials = [
            ['#','facebook'], ['#','instagram'], ['#','twitter'], ['#','youtube']
          ];
        @endphp
        @foreach($socials as [$href,$name])
          <a href="{{ $href }}" target="_blank" class="p-2 rounded-full bg-white/5 border border-white/10 hover:bg-white/10">
            @switch($name)
              @case('facebook')
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 10-11.6 9.9v-7h-2.3V12h2.3V9.8c0-2.3 1.4-3.6 3.5-3.6.7 0 1.5.1 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.5V12h2.7l-.4 2.9h-2.3v7A10 10 0 0022 12z"/></svg>
              @break
              @case('instagram')
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5a5 5 0 100 10 5 5 0 000-10zm6.5-1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><circle cx="12" cy="12" r="3.5" fill="#0b0f1a"/></svg>
              @break
              @case('twitter')
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M23 5.9a9 9 0 01-2.6.7 4.5 4.5 0 002-2.5 9 9 0 01-2.8 1.1 4.5 4.5 0 00-7.7 4.1A12.9 12.9 0 013 4.8a4.5 4.5 0 001.4 6 4.5 4.5 0 01-2-.6v.1a4.5 4.5 0 003.6 4.4 4.5 4.5 0 01-2 .1 4.5 4.5 0 004.2 3.1A9 9 0 012 19a12.8 12.8 0 006.9 2"/></svg>
              @break
              @case('youtube')
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.2 3.5 12 3.5 12 3.5s-7.2 0-9.4.6A3 3 0 00.5 6.2 31.7 31.7 0 000 12a31.7 31.7 0 00.5 5.8 3 3 0 002.1 2.1c2.2.6 9.4.6 9.4.6s7.2 0 9.4-.6a3 3 0 002.1-2.1A31.7 31.7 0 0024 12a31.7 31.7 0 00-.5-5.8zM9.7 15.5v-7l6 3.5-6 3.5z"/></svg>
              @break
            @endswitch
          </a>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ================= Bottom bar ================= --}}
  <div class="border-t border-white/10">
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-6 text-xs sm:text-sm text-white/60 flex flex-col md:flex-row items-center justify-between gap-3">
      <p>&copy; {{ date('Y') }} Diva House Beauty. Designed by <span class="text-white">Izere Moubarak</span>.</p>
      <p>All prices in <span class="text-white">RWF</span>. Taxes included where applicable.</p>
    </div>
  </div>

  {{-- Floating back-to-top --}}
  <div x-data="{show:false}" x-init="window.addEventListener('scroll',()=>show=window.scrollY>240)">
    <button x-show="show" x-transition
            @click="window.scrollTo({top:0, behavior:'smooth'})"
            class="fixed bottom-5 right-5 z-50 rounded-full bg-[var(--gold)] text-black p-3 shadow-lg hover:opacity-90">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
    </button>
  </div>
</footer>

{{-- Newsletter Alpine helper --}}
<script>
  function newsletter(){
    return {
      email:'', busy:false, status:'idle', message:'',
      init(){},
      async submit(){
        // quick email check
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)){
          this.status='error'; this.message='Please enter a valid email.'; return;
        }
        this.busy=true; this.status='working'; this.message='';
        try{
          const res = await fetch('{{ route('newsletter.subscribe') }}', {
            method:'POST',
            headers:{
              'Content-Type':'application/json',
              'Accept':'application/json',
              'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: this.email, website: (this.$refs.honeypot?.value || '') })
          });
          if(!res.ok){ throw await res.json(); }
          this.status='success'; this.message='Thanks! You are subscribed.'; this.email='';
        }catch(e){
          this.status='error';
          this.message = (e?.message || 'Something went wrong. Please try again.');
        }finally{ this.busy=false; }
      }
    }
  }
</script>

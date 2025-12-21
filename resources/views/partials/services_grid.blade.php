{{-- ===== Beauty Services • minimal, classy band (typewriter) ===== --}}
<section class="bg-[var(--black)] text-white">
  <div class="mx-auto max-w-7xl px-3 sm:px-4 py-10"
       x-data="typewriterServicesLite()" x-init="start()">

    <div class="grid items-center gap-6 sm:gap-8 lg:grid-cols-2">
      {{-- Left: headline + copy + CTAs --}}
      <div class="text-center lg:text-left">
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Beauty Services</h2>
        <p class="mt-2 text-sm sm:text-base text-white/70">
          Lashes, massage, hair, nails, makeup & more delivered by trained professionals.
          See details on
          <a href="{{ route('booking.create') }}" class="underline decoration-[var(--gold)]/70 hover:text-[var(--gold)]">Booking</a>
          or
          <a href="{{ route('blog') }}" class="underline decoration-[var(--gold)]/70 hover:text-[var(--gold)]">Services & Pricing</a>.
        </p>

        <div class="mt-5 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-2">
          <a href="{{ route('booking.create') }}"
             class="rounded-md bg-[var(--gold)] px-3.5 py-2 text-sm font-semibold text-black hover:opacity-90">
            Book now
          </a>
          <a href="{{ route('blog') }}"
             class="rounded-md border border-white/20 px-3.5 py-2 text-sm font-semibold text-white hover:bg-white/10">
            Services & Pricing
          </a>
        </div>
      </div>

      {{-- Right: typewriter line + subtle chips --}}
      <div class="w-full">
        <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-4 sm:px-5 sm:py-5">
          <p class="text-[13px] sm:text-base font-semibold tracking-wide caret" x-text="phrase"></p>
        </div>

        <div class="mt-4 flex flex-wrap gap-2 justify-center lg:justify-start">
          @foreach (['Lashes','Massage','Aesthetic & Wellness','Hairstyle','Barber','MakeUp','Nails','Tattoo'] as $tag)
            <span class="px-2.5 py-1 text-[12px] rounded-full border border-white/15 text-white/80">
              {{ $tag }}
            </span>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Alpine: simple, natural type speed --}}
<script>
  function typewriterServicesLite() {
    return {
      items: [
        'Classic • Hybrid • Mega-volume lashes',
        'Relaxing • Deep-tissue • Aromatherapy massage',
        'Facials • Body treatments • Skin wellness',
        'Braids • Weaves • Professional styling',
        'Barber cuts • Shaves • Grooming',
        'Soft-glam • Bridal & event makeup',
        'Gel manicure • Pedicure • Builder/BIAB',
        'Fine-line • Custom tattoo work'
      ],
      prefix: 'We offer ',
      phrase: '',
      i: 0, j: 0, typing: true,
      delay: 32,     // keystroke speed (ms)
      hold: 1200,    // pause on full line (ms)
      start(){ this.loop(); },
      loop(){
        const full = this.prefix + this.items[this.i];
        if (this.typing) {
          this.phrase = full.slice(0, ++this.j);
          if (this.j === full.length) { this.typing = false; return setTimeout(()=>this.loop(), this.hold); }
        } else {
          this.phrase = full.slice(0, --this.j);
          if (this.j === this.prefix.length) { this.typing = true; this.i = (this.i + 1) % this.items.length; }
        }
        setTimeout(()=>this.loop(), this.delay);
      }
    }
  }
</script>

<style>
  /* understated caret */
  .caret::after {
    content: "|";
    margin-left: .14rem;
    color: #ffffffb3; /* white/70 */
    animation: blink 1s step-end infinite;
  }
  @keyframes blink { 50% { opacity: 0; } }
</style>

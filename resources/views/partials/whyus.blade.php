{{-- ===== WHY US (compact carousel with inline SVG icons + drag scroll) ===== --}}
<section id="why-us" class="bg-white">
  <div class="mx-auto max-w-7xl px-3 sm:px-4 py-12 sm:py-14"
       x-data="whyUsScroller()" x-init="start()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
      <div>
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-[12px] text-slate-600">
          <span class="h-2 w-2 rounded-full bg-[var(--gold)]"></span>
          Why choose Diva House Beauty
        </span>
        <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">
          Shop & book with confidence
        </h2>
        <p class="mt-1 text-slate-600 text-sm">
          Beauty services and shopping made simple  safe checkout, quick delivery, and friendly support.
        </p>
      </div>

      {{-- Desktop arrows --}}
      <div class="hidden sm:flex items-center gap-2">
        <button @click="scrollBy(-360)" :disabled="atStart"
                class="h-9 w-9 grid place-items-center rounded-md border border-slate-200 text-slate-600
                       hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="la la-angle-left text-xl"></i>
        </button>
        <button @click="scrollBy(360)" :disabled="atEnd"
                class="h-9 w-9 grid place-items-center rounded-md border border-slate-200 text-slate-600
                       hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="la la-angle-right text-xl"></i>
        </button>
      </div>
    </div>

    {{-- Track --}}
    <div class="mt-5 relative">
      {{-- Mobile overlay arrows --}}
      <div class="sm:hidden absolute inset-y-0 left-0 right-0 flex items-center justify-between pointer-events-none">
        <button @click="scrollBy(-280)" class="pointer-events-auto ml-1 h-8 w-8 grid place-items-center rounded-md bg-white/90 shadow-ring">
          <i class="la la-angle-left"></i>
        </button>
        <button @click="scrollBy(280)" class="pointer-events-auto mr-1 h-8 w-8 grid place-items-center rounded-md bg-white/90 shadow-ring">
          <i class="la la-angle-right"></i>
        </button>
      </div>

      <div x-ref="track"
           class="overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory select-none
                  cursor-grab active:cursor-grabbing"
           @mouseenter="pause()" @mouseleave="resume()"
           @mousedown="onDown" @mousemove="onMove" @mouseup="onUp" @mouseleave.window="onUp" @touchstart.passive="onTouchStart" @touchmove.passive="onTouchMove" @touchend.passive="onUp">
        <div class="flex gap-3 sm:gap-4 min-w-max pr-2">

          <template x-for="(f, i) in items" :key="i">
            <article class="snap-start w-60 sm:w-64 shrink-0 rounded-xl bg-white border border-slate-200 shadow-ring p-4">
              <div class="flex items-start gap-3">
                <div class="h-10 w-10 grid place-items-center rounded-lg bg-[var(--gold)]/10 text-[var(--gold)]">
                  <span class="w-5 h-5" x-html="svg(f.icon)"></span>
                </div>
                <div class="min-w-0">
                  <h3 class="text-sm font-semibold text-slate-900" x-text="f.title"></h3>
                  <p class="mt-1 text-[12px] text-slate-600 leading-relaxed" x-text="f.desc"></p>
                </div>
              </div>
            </article>
          </template>

        </div>
      </div>

      {{-- Dots --}}
      <div class="mt-3 flex justify-center gap-1.5">
        <template x-for="i in pages" :key="'dot-'+i">
          <span class="h-1.5 w-1.5 rounded-full"
                :class="pageIndex===i-1 ? 'bg-[var(--gold)]' : 'bg-slate-300'"></span>
        </template>
      </div>
    </div>
  </div>
</section>

<script>
  function whyUsScroller() {
    return {
      // cards
      items: [
        { icon: 'truck',   title: 'Fast delivery',     desc: 'Quick dispatch across Kigali for beauty & fashion orders.' },
        { icon: 'shield',  title: 'Secure payments',   desc: 'Protected checkout for peace of mind every time.' },
        { icon: 'card',    title: 'Multiple options',  desc: 'Pay with MoMo, cards, and more — easy & flexible.' },
        { icon: 'box',     title: 'Genuine products',  desc: 'Sourced from trusted brands — quality you can rely on.' },
        { icon: 'calendar',title: 'Easy booking',      desc: 'Simple online scheduling with helpful reminders.' },
        { icon: 'headset', title: 'Friendly support',  desc: 'Need help? We’re a message away on chat/WhatsApp.' },
        { icon: 'refresh', title: 'Hassle-free returns',desc:'Straightforward return policy on eligible products.' },
        { icon: 'bell',    title: 'Order updates',     desc: 'Get notified from purchase to doorstep.' },
      ],

      // icon pack (inline SVG, currentColor)
      svg(name){
        const s = {
          truck:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4M3 17h1M16 17h1M3 7h11v6H3z"/><path d="M14 7h3l3 3v3h-3"/><circle cx="6.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/></svg>`,
          shield:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
          card:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 9h20M6 15h3"/></svg>`,
          box:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/></svg>`,
          calendar:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>`,
          headset:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0118 0v5a2 2 0 01-2 2h-2v-7h4"/><path d="M7 19H5a2 2 0 01-2-2v-5"/><path d="M15 21a3 3 0 01-3 3"/></svg>`,
          refresh:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 10-3.35 6.95"/><path d="M21 12v6h-6"/></svg>`,
          bell:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 1112 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10.3 21a1.7 1.7 0 003.4 0"/></svg>`,
        };
        return s[name] || s.truck;
      },

      // scroller state
      el:null, atStart:true, atEnd:false,
      timer:null, interval:3500,
      pages: 6, pageIndex:0,

      update(){
        if (!this.el) return;
        this.atStart = this.el.scrollLeft <= 6;
        this.atEnd   = Math.ceil(this.el.scrollLeft + this.el.clientWidth) >= this.el.scrollWidth - 6;
        // rough page index for dots
        this.pages = Math.max(1, Math.ceil(this.el.scrollWidth / (this.el.clientWidth * 0.9)));
        this.pageIndex = Math.round(this.el.scrollLeft / (this.el.clientWidth * 0.9));
      },
      scrollBy(dx){ this.el?.scrollBy({ left: dx, behavior: 'smooth' }); },

      // autoplay
      autoplay(){
        if (!this.el) return;
        if (this.atEnd) { this.el.scrollTo({ left: 0, behavior:'smooth' }); }
        else            { this.scrollBy(this.el.clientWidth * 0.8); }
      },
      pause(){ if (this.timer) { clearInterval(this.timer); this.timer = null; } },
      resume(){ this.pause(); this.timer = setInterval(() => this.autoplay(), this.interval); },

      // drag to scroll
      dragging:false, startX:0, startLeft:0,
      onDown(e){ this.dragging = true; this.startX = e.pageX; this.startLeft = this.el.scrollLeft; },
      onMove(e){ if(!this.dragging) return; e.preventDefault(); this.el.scrollLeft = this.startLeft - (e.pageX - this.startX); this.update(); },
      onUp(){ this.dragging = false; },
      onTouchStart(e){ this.dragging = true; this.startX = e.touches[0].pageX; this.startLeft = this.el.scrollLeft; },
      onTouchMove(e){ if(!this.dragging) return; this.el.scrollLeft = this.startLeft - (e.touches[0].pageX - this.startX); this.update(); },

      start(){
        this.el = this.$refs.track;
        this.update();
        this.el.addEventListener('scroll', () => this.update(), { passive:true });
        window.addEventListener('resize', () => this.update());
        this.resume();
      }
    }
  }
</script>

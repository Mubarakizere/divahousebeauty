{{-- resources/views/media/index.blade.php (Blog & Media, Tailwind version) --}}
<!DOCTYPE html>
<html lang="en">
@php
    // Header partial fallbacks
    $categories = $categories ?? \App\Models\Category::select('id','name','slug')->get();
    $count      = $count ?? (auth()->id() ? \App\Models\Cart::where('users_id', auth()->id())->count() : 0);

    // Split media by type
    $menus  = ($media ?? collect())->where('type', 'menu');
    $videos = ($media ?? collect())->where('type', 'video');
    $photos = ($media ?? collect())->where('type', 'photo');

    $toastTypes = ['success', 'error', 'info', 'warning', 'message'];
@endphp
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Blog & Media — Diva House Beauty</title>
  <meta name="description" content="News, pricing menus, photos and videos from Diva House Beauty."/>
  <meta name="theme-color" content="#cc9966"/>

  {{-- Tailwind (preflight OFF) + Alpine --}}
  <script>tailwind = { config: { corePlugins: { preflight: false } } };</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons + SweetAlert2 --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css') }}"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root{ --gold:#cc9966; --black:#111827; }
    [x-cloak]{ display:none !important; }
    .shadow-ring{ box-shadow:0 0 0 1px rgba(0,0,0,.05),0 6px 20px rgba(0,0,0,.08) }
    .tab-btn{ @apply px-4 py-2 rounded-full text-sm font-medium border transition; }
    .tab-btn--on{ @apply border-[var(--gold)] text-[var(--gold)] bg-[var(--gold)]/10; }
    .tab-btn--off{ @apply border-slate-300 text-slate-600 hover:border-[var(--gold)] hover:text-[var(--gold)]; }
    .pill{ @apply inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700; }
    .modal-zoom{ transform: scale(.98); opacity:.0; transition: all .15s ease; }
    [x-show] .modal-zoom{ transform: scale(1); opacity:1; }
  </style>
</head>

<body class="bg-[#fafafa] text-slate-700 antialiased"
      x-data="{ authOpen:false, authTab:'signin' }"
      @open-auth.window="authOpen=true; authTab=$event.detail?.tab || 'signin'">

  {{-- ================= HEADER (Tailwind partial) ================= --}}
  @include('partials.header_home2')

  {{-- ================= HERO ================= --}}
  <section class="relative">
    <div class="relative">
      <img src="{{ asset('assets/images/page-header-bg.jpg') }}" alt="Blog & Media"
           class="h-48 w-full object-cover md:h-56 lg:h-64">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-black/0"></div>
    </div>
    <div class="absolute inset-0 flex items-end">
      <div class="mx-auto w-full max-w-7xl px-3 sm:px-4 pb-4 md:pb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">
          Blog & <span class="text-[var(--gold)]">Media</span>
        </h1>
        <nav class="mt-1 text-xs text-white/80">
          <a href="{{ route('home') }}" class="hover:text-[var(--gold)]">Home</a>
          <span class="mx-1">/</span>
          <span>IHURIRO</span>
          <span class="mx-1">/</span>
          <span class="text-white">Blog & Media</span>
        </nav>
      </div>
    </div>
  </section>

  {{-- ================= CONTENT ================= --}}
  <main class="py-6"
        x-data="mediaPage()"
        x-init="init()">

    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      {{-- Tabs --}}
      <div class="flex flex-col items-center gap-3">
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-full p-1 shadow-ring">
          <button type="button" @click="tab='menus'"
                  :class="tab==='menus' ? 'tab-btn tab-btn--on' : 'tab-btn tab-btn--off'">
            Pricings <span class="ml-2 pill">{{ $menus->count() }}</span>
          </button>
          <button type="button" @click="tab='videos'"
                  :class="tab==='videos' ? 'tab-btn tab-btn--on' : 'tab-btn tab-btn--off'">
            Videos <span class="ml-2 pill">{{ $videos->count() }}</span>
          </button>
          <button type="button" @click="tab='photos'"
                  :class="tab==='photos' ? 'tab-btn tab-btn--on' : 'tab-btn tab-btn--off'">
            Photos <span class="ml-2 pill">{{ $photos->count() }}</span>
          </button>
        </div>
        <p class="text-xs text-slate-500">Tap a card to view full-screen.</p>
      </div>

      {{-- Panels --}}
      <div class="mt-5">

        {{-- MENUS --}}
        <div x-show="tab==='menus'" x-cloak>
          @if($menus->count())
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
              @foreach($menus as $item)
                @php $src = Storage::url($item->file_path); @endphp
                <article class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-ring">
                  <button type="button" class="block w-full"
                          @click="open('image', @js($src), @js($item->title))">
                    <div class="aspect-[4/5] bg-slate-50 overflow-hidden">
                      <img src="{{ $src }}" alt="{{ $item->title }}"
                           class="h-full w-full object-cover hover:scale-105 transition">
                    </div>
                  </button>
                  <div class="p-3">
                    <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $item->title }}</h3>
                    @if($item->description)
                      <p class="mt-1 text-xs text-slate-600 line-clamp-2">{{ $item->description }}</p>
                    @endif
                  </div>
                </article>
              @endforeach
            </div>
          @else
            <div class="bg-white border border-slate-200 rounded-xl p-6 text-center shadow-ring">
              <i class="la la-file-alt text-2xl text-slate-400"></i>
              <p class="mt-1 text-sm text-slate-600">No pricing menus yet.</p>
            </div>
          @endif
        </div>

        {{-- VIDEOS --}}
        <div x-show="tab==='videos'" x-cloak>
          @if($videos->count())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
              @foreach($videos as $item)
                @php $src = Storage::url($item->file_path); @endphp
                <article class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-ring">
                  <button type="button" class="block w-full"
                          @click="open('video', @js($src), @js($item->title))">
                    <div class="aspect-video bg-slate-200 grid place-items-center">
                      <i class="la la-play-circle text-5xl text-white drop-shadow"></i>
                    </div>
                  </button>
                  <div class="p-3">
                    <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $item->title }}</h3>
                    @if($item->description)
                      <p class="mt-1 text-xs text-slate-600 line-clamp-2">{{ $item->description }}</p>
                    @endif
                  </div>
                </article>
              @endforeach
            </div>
          @else
            <div class="bg-white border border-slate-200 rounded-xl p-6 text-center shadow-ring">
              <i class="la la-video text-2xl text-slate-400"></i>
              <p class="mt-1 text-sm text-slate-600">No videos yet.</p>
            </div>
          @endif
        </div>

        {{-- PHOTOS --}}
        <div x-show="tab==='photos'" x-cloak>
          @if($photos->count())
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3">
              @foreach($photos as $item)
                @php $src = Storage::url($item->file_path); @endphp
                <article class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-ring">
                  <button type="button" class="block w-full"
                          @click="open('image', @js($src), @js($item->title))">
                    <div class="aspect-square bg-slate-50 overflow-hidden">
                      <img src="{{ $src }}" alt="{{ $item->title }}"
                           class="h-full w-full object-cover hover:scale-105 transition">
                    </div>
                  </button>
                  <div class="p-2">
                    <h3 class="text-xs font-medium text-slate-900 line-clamp-1">{{ $item->title }}</h3>
                    @if($item->description)
                      <p class="mt-0.5 text-[11px] text-slate-600 line-clamp-2">{{ $item->description }}</p>
                    @endif
                  </div>
                </article>
              @endforeach
            </div>
          @else
            <div class="bg-white border border-slate-200 rounded-xl p-6 text-center shadow-ring">
              <i class="la la-image text-2xl text-slate-400"></i>
              <p class="mt-1 text-sm text-slate-600">No photos yet.</p>
            </div>
          @endif
        </div>

      </div>
    </div>

    {{-- =============== FULLSCREEN MODAL (Alpine) =============== --}}
    <div x-show="modalOpen" x-cloak
         x-transition.opacity
         class="fixed inset-0 z-[80] bg-black/70">
      <div class="absolute inset-0 flex items-center justify-center p-3"
           @keydown.escape.window="close()">
        <div class="modal-zoom w-full max-w-5xl">
          <div class="bg-white rounded-2xl overflow-hidden shadow-ring">
            <div class="flex items-center justify-between px-3 py-2 border-b border-slate-200">
              <h3 class="text-sm font-semibold text-slate-900 truncate" x-text="modalTitle"></h3>
              <button @click="close()" class="h-9 w-9 grid place-items-center rounded-full hover:bg-slate-100">
                <i class="la la-close text-xl"></i>
              </button>
            </div>

            <template x-if="modalType==='image'">
              <div class="bg-black">
                <img :src="modalSrc" alt="" class="mx-auto max-h-[80vh] w-auto object-contain">
              </div>
            </template>

            <template x-if="modalType==='video'">
              <div class="bg-black">
                <video :src="modalSrc" controls class="mx-auto max-h-[80vh] w-full"></video>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </main>

  {{-- ================= FOOTER (Tailwind partial) ================= --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- ================= TOASTS ================= --}}
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

  <script>
    function mediaPage(){
      return {
        tab: 'menus',
        modalOpen: false,
        modalType: 'image', // 'image' | 'video'
        modalSrc: '',
        modalTitle: '',
        init(){ /* no-op for now */ },
        open(type, src, title){
          this.modalType = type;
          this.modalSrc = src;
          this.modalTitle = title || '';
          this.modalOpen = true;
          document.body.classList.add('overflow-hidden');
        },
        close(){
          this.modalOpen = false;
          this.modalSrc = '';
          document.body.classList.remove('overflow-hidden');
        }
      }
    }
  </script>
</body>
</html>

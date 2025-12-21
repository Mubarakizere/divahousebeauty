<!DOCTYPE html>
<html lang="en">
@php
    $toastTypes = ['success', 'error', 'info', 'warning', 'message'];
    $categories = $categories ?? \App\Models\Category::select('id','name','slug')->get(); // header partial
    $count      = $count ?? (auth()->id() ? \App\Models\Cart::where('users_id', auth()->id())->count() : 0);
@endphp
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Book Appointment</title>
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

  {{-- ===================== HERO ===================== --}}
  <section class="relative">
    <div class="relative">
      <img src="{{ asset('assets/images/page-header-bg.jpg') }}" alt="Book Appointment"
           class="h-48 w-full object-cover md:h-56 lg:h-64">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-black/0"></div>
    </div>
    <div class="absolute inset-0 flex items-end">
      <div class="mx-auto w-full max-w-7xl px-3 sm:px-4 pb-4 md:pb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">
          Book <span class="text-[var(--gold)]">Appointment</span>
        </h1>
        <nav class="mt-1 text-xs text-white/80">
          <a href="{{ route('home') }}" class="hover:text-[var(--gold)]">Home</a>
          <span class="mx-1">/</span>
          <span>Ihuriro</span>
          <span class="mx-1">/</span>
          <span class="text-white">Book</span>
        </nav>
      </div>
    </div>
  </section>

  {{-- ===================== CONTENT ===================== --}}
  <main class="py-6">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">

      {{-- Error summary --}}
      @if($errors->any())
        <div class="mb-4 rounded-lg border border-rose-300 bg-rose-50 p-3 text-rose-800">
          <div class="font-semibold">Please fix the following:</div>
          <ul class="mt-2 list-disc pl-5 text-sm">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Flash success --}}
      @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 p-3 text-emerald-800">
          {{ session('success') }}
        </div>
      @endif

      <div class="grid grid-cols-12 gap-4 lg:gap-6">

        {{-- ===================== FORM ===================== --}}
        <section class="col-span-12 lg:col-span-7">
          <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">

            {{-- Step header --}}
            <div class="mb-5 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[var(--gold)] text-white">
                  <i class="la la-calendar text-xl"></i>
                </span>
                <div>
                  <h2 class="text-lg md:text-xl font-semibold text-slate-900">Book a Beauty Service</h2>
                  <p class="text-xs text-slate-500">Fill your details, pick service & time. We’ll confirm shortly.</p>
                </div>
              </div>
              <div class="hidden sm:flex items-center gap-3">
                <span class="step-dot step-dot--active">1</span><span class="text-xs">Details</span>
                <span class="text-slate-300">—</span>
                <span class="step-dot">2</span><span class="text-xs">Service</span>
                <span class="text-slate-300">—</span>
                <span class="step-dot">3</span><span class="text-xs">Time</span>
              </div>
            </div>

            <form method="POST" id="bookingForm" action="{{ route('booking.store') }}" class="space-y-4">
              @csrf

              {{-- Name + Phone --}}
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="label" for="name">Your Name</label>
                  <div class="mt-1 relative">
                    <i class="la la-user absolute left-3 top-2.5 text-slate-400"></i>
                    <input type="text" id="name" name="user_name" value="{{ old('user_name') }}"
                           class="input pl-9" required autocomplete="name">
                  </div>
                  @error('user_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                  <label class="label" for="phone_number">Your Phone Number</label>
                  <div class="mt-1 relative">
                    <span class="absolute left-3 top-2.5 text-slate-400">+250</span>
                    <input type="text" id="phone_number" name="user_phone" value="{{ old('user_phone') }}"
                           placeholder="07XXXXXXXX" class="input pl-14" required inputmode="numeric" autocomplete="tel">
                  </div>
                  <p id="phone_error" class="mt-1 hidden text-xs text-rose-600">Enter a valid Rwandan phone (07 + 8 digits).</p>
                  @error('user_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
              </div>

              {{-- Service Type --}}
              <div>
                <label class="label" for="service_type">Select Service Type</label>
                <select id="service_type" name="service_type_id" class="input bg-white" required>
                  <option value="">Choose a service type</option>
                  @foreach ($serviceTypes as $serviceType)
                    <option value="{{ $serviceType->id }}" @selected(old('service_type_id')==$serviceType->id)>
                      {{ $serviceType->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- Service --}}
              <div>
                <label class="label" for="service">Select Service</label>
                <select id="service" name="service_id" class="input bg-white" required disabled>
                  <option value="">Choose a service</option>
                </select>
              </div>

              {{-- Provider --}}
              <div>
                <label class="label" for="provider">Select Provider</label>
                <select id="provider" name="provider_id" class="input bg-white" required disabled>
                  <option value="">Choose a provider</option>
                </select>
              </div>

              {{-- Price & Duration --}}
              <div id="serviceDetails" class="hidden rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                <div class="flex flex-wrap items-center gap-4">
                  <span class="badge badge-emerald"><i class="la la-check mr-1"></i> Selected</span>
                  <div class="flex items-center gap-2">
                    <i class="la la-money-bill text-[var(--gold)]"></i>
                    <p><strong>Price:</strong> <span id="selectedPrice">—</span> RWF</p>
                  </div>
                  <div class="flex items-center gap-2">
                    <i class="la la-hourglass-half text-[var(--gold)]"></i>
                    <p><strong>Duration:</strong> <span id="selectedDuration">—</span> minutes</p>
                  </div>
                  <div class="flex items-center gap-2">
                    <i class="la la-clock text-[var(--gold)]"></i>
                    <p><strong>Ends around:</strong> <span id="selectedEndTime">—</span></p>
                  </div>
                </div>
              </div>

              {{-- Date + Time --}}
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="label" for="date">Select Date</label>
                  <input type="date" id="date" name="preferred_time_date"
                         value="{{ old('preferred_time_date') }}" class="input" required>
                </div>
                <div>
                  <label class="label" for="time">Select Time</label>
                  <input type="time" id="time" name="preferred_time_time"
                         value="{{ old('preferred_time_time') }}" class="input" required>
                </div>
              </div>

              {{-- Note (optional) --}}
              <div>
                <label class="label" for="note">Notes (optional)</label>
                <textarea id="note" name="note" rows="3" class="input" placeholder="Any special requests…">{{ old('note') }}</textarea>
              </div>

              {{-- Actions --}}
              <div class="pt-2 flex flex-wrap items-center gap-3">
                <button id="submitBtn" type="submit" class="btn-primary">
                  <i class="la la-check-circle text-lg"></i> Book Now
                </button>
                <a href="https://wa.me/250780159059?text={{ urlencode('Hello Diva House Beauty, I want to book a service.') }}"
                   target="_blank" rel="noopener" class="btn-outline">
                  <i class="la la-whatsapp text-lg"></i> Book via WhatsApp
                </a>
              </div>
            </form>
          </div>
        </section>

        {{-- ===================== SIDEBAR ===================== --}}
        <aside class="col-span-12 lg:col-span-5">
          <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
            <h3 class="text-base md:text-lg font-semibold text-slate-900">Your selection</h3>
            <dl class="mt-3 grid grid-cols-1 gap-2 text-sm" id="summaryBox">
              <div class="flex justify-between"><dt class="text-slate-500">Service type</dt><dd id="sumType">—</dd></div>
              <div class="flex justify-between"><dt class="text-slate-500">Service</dt><dd id="sumService">—</dd></div>
              <div class="flex justify-between"><dt class="text-slate-500">Provider</dt><dd id="sumProvider">—</dd></div>
              <div class="flex justify-between"><dt class="text-slate-500">Price</dt><dd id="sumPrice">—</dd></div>
              <div class="flex justify-between"><dt class="text-slate-500">Duration</dt><dd id="sumDuration">—</dd></div>
              <div class="flex justify-between"><dt class="text-slate-500">Date</dt><dd id="sumDate">—</dd></div>
              <div class="flex justify-between"><dt class="text-slate-500">Time</dt><dd id="sumTime">—</dd></div>
              <div class="flex justify-between"><dt class="text-slate-500">Ends</dt><dd id="sumEnds">—</dd></div>
            </dl>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-ring">
            <h3 class="text-base md:text-lg font-semibold text-slate-900">Why book with us</h3>
            <ul class="mt-3 space-y-2 text-sm">
              <li class="flex items-start gap-2"><i class="la la-star text-[var(--gold)] mt-0.5"></i> Premium services: spa, lashes, nails, hair & more.</li>
              <li class="flex items-start gap-2"><i class="la la-clock text-[var(--gold)] mt-0.5"></i> On-time appointments and friendly experts.</li>
              <li class="flex items-start gap-2"><i class="la la-shield-alt text-[var(--gold)] mt-0.5"></i> Clean, safe & relaxing environment.</li>
            </ul>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="font-medium text-slate-900">Opening Hours</div>
                <p class="mt-1 text-slate-600">Mon–Sat: 09:00–20:00<br>Sun: 12:00–18:00</p>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="font-medium text-slate-900">Call us</div>
                <a class="mt-1 inline-flex items-center gap-2 text-[var(--gold)] hover:opacity-90" href="tel:0780159059">
                  <i class="la la-phone"></i> +250 780 159 059
                </a>
              </div>
            </div>
          </div>
        </aside>

      </div>
    </div>
  </main>

  {{-- ===================== FOOTER + AUTH MODAL ===================== --}}
  @include('partials.footer')
  @includeIf('partials.auth_modal')

  {{-- ===================== TOASTS ===================== --}}
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

  {{-- ===================== SCRIPTS ===================== --}}
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    // Format + validate Rwanda phone: keep digits, must be 07 + 8 digits
    const phoneInput = document.getElementById("phone_number");
    const phoneError = document.getElementById("phone_error");
    if (phoneInput) {
      phoneInput.addEventListener("input", function () {
        this.value = this.value.replace(/[^\d]/g,''); // digits only
        const ok = /^07\d{8}$/.test(this.value.trim());
        phoneError.classList.toggle('hidden', ok || this.value.trim()==='');
        this.classList.toggle('is-invalid', !ok && this.value.trim()!=='');
      });
    }

    // Date/time guard: disallow past times for today
    const dateInput = document.getElementById("date");
    const timeInput = document.getElementById("time");
    const sumDate = document.getElementById('sumDate');
    const sumTime = document.getElementById('sumTime');
    const sumEnds = document.getElementById('sumEnds');

    function todayISO(){ return new Date().toISOString().split("T")[0]; }
    function z(n){ return String(n).padStart(2,'0'); }

    function updateMinTime(){
      if(!dateInput || !timeInput) return;
      const now = new Date();
      const selected = new Date(dateInput.value);
      if (selected.toDateString() === now.toDateString()) {
        timeInput.min = `${z(now.getHours())}:${z(now.getMinutes())}`;
      } else {
        timeInput.removeAttribute("min");
      }
    }
    function updateSummaryEnd(){
      const dur = parseInt(document.getElementById('selectedDuration')?.textContent || '0', 10);
      if (!dateInput.value || !timeInput.value || !dur) { sumEnds.textContent = '—'; return; }
      const d = new Date(`${dateInput.value}T${timeInput.value}:00`);
      if (isNaN(d)) { sumEnds.textContent = '—'; return; }
      d.setMinutes(d.getMinutes() + dur);
      sumEnds.textContent = `${z(d.getHours())}:${z(d.getMinutes())}`;
    }

    if (dateInput) {
      dateInput.min = todayISO();
      dateInput.addEventListener('change', () => { updateMinTime(); sumDate.textContent = dateInput.value || '—'; updateSummaryEnd(); });
      sumDate.textContent = dateInput.value || '—';
      updateMinTime();
    }
    if (timeInput) {
      timeInput.addEventListener('change', () => { sumTime.textContent = timeInput.value || '—'; updateSummaryEnd(); });
      sumTime.textContent = timeInput.value || '—';
    }

    // Populate services by type
    const serviceType = document.getElementById('service_type');
    const serviceSelect = document.getElementById('service');
    const providerSelect = document.getElementById('provider');
    const serviceDetails = document.getElementById('serviceDetails');
    const selectedPrice = document.getElementById('selectedPrice');
    const selectedDuration = document.getElementById('selectedDuration');
    const selectedEndTime = document.getElementById('selectedEndTime');
    const sumType = document.getElementById('sumType');
    const sumService = document.getElementById('sumService');
    const sumProvider = document.getElementById('sumProvider');
    const sumPrice = document.getElementById('sumPrice');
    const sumDuration = document.getElementById('sumDuration');

    function resetSelect(select, placeholder='Choose...') {
      select.innerHTML = `<option value="">${placeholder}</option>`;
      select.disabled = true;
    }
    function enableSelect(select){ select.disabled = false; }

    if (serviceType) {
      serviceType.addEventListener('change', () => {
        sumType.textContent = serviceType.options[serviceType.selectedIndex]?.text || '—';
        resetSelect(serviceSelect, 'Loading…');
        resetSelect(providerSelect, 'Choose a provider');
        serviceDetails.classList.add('hidden');

        const id = serviceType.value;
        if (!id) { resetSelect(serviceSelect, 'Choose a service'); return; }

        fetch(`/services/${id}`)
          .then(r => r.ok ? r.json() : Promise.reject())
          .then(list => {
            resetSelect(serviceSelect, 'Choose a service');
            if (!Array.isArray(list) || !list.length) {
              serviceSelect.innerHTML += '<option disabled>No services found</option>';
              return;
            }
            list.forEach(s => { serviceSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`; });
            enableSelect(serviceSelect);
          })
          .catch(() => { resetSelect(serviceSelect, 'Error loading services'); });
      });
    }

    if (serviceSelect) {
      serviceSelect.addEventListener('change', () => {
        sumService.textContent = serviceSelect.options[serviceSelect.selectedIndex]?.text || '—';
        resetSelect(providerSelect, 'Loading…');
        serviceDetails.classList.add('hidden');

        const id = serviceSelect.value;
        if (!id) { resetSelect(providerSelect, 'Choose a provider'); return; }

        fetch(`/providers/${id}`)
          .then(r => r.ok ? r.json() : Promise.reject())
          .then(list => {
            resetSelect(providerSelect, 'Choose a provider');
            if (!Array.isArray(list) || !list.length) {
              providerSelect.innerHTML += '<option disabled>No providers found</option>';
              return;
            }
            list.forEach(p => {
              providerSelect.innerHTML += `
                <option value="${p.id}" data-price="${p.price}" data-duration="${p.duration_minutes}">
                  ${p.name} — RWF ${p.price} (${p.duration_minutes} min)
                </option>`;
            });
            enableSelect(providerSelect);
          })
          .catch(() => { resetSelect(providerSelect, 'Error loading providers'); });
      });
    }

    if (providerSelect) {
      providerSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const price = opt?.getAttribute('data-price');
        const dur   = opt?.getAttribute('data-duration');
        sumProvider.textContent = opt?.text?.split(' — ')[0] || '—';
        if (price && dur) {
          selectedPrice.textContent = price;
          selectedDuration.textContent = dur;
          sumPrice.textContent = `RWF ${price}`;
          sumDuration.textContent = `${dur} min`;
          serviceDetails.classList.remove('hidden');
          updateSummaryEnd();
        } else {
          serviceDetails.classList.add('hidden');
          sumPrice.textContent = '—'; sumDuration.textContent = '—';
        }
      });
    }

    // Show sign-in modal if there were validation errors tied to auth modal
    @if($errors->any())
      const modalEl = document.getElementById('signin-modal');
      if (modalEl && window.bootstrap) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    @endif
  </script>
</body>
</html>

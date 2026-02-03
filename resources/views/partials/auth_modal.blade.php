{{-- ===================== AUTH MODAL (Sign In / Register) ===================== --}}
<div x-show="authOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/40"></div>

<section x-show="authOpen" x-transition
         x-data="{ showPwdIn: false, showPwdUp: false }"
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
            <input id="signin-email" name="email" type="email" autocomplete="username" required
                   value="{{ old('email') }}"
                   class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none
                          focus:border-[var(--gold)] focus:ring-2 focus:ring-[var(--gold)]/30
                          @error('email') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="signin-password" class="block text-sm font-medium text-slate-700">Password *</label>
            <div class="mt-1 relative">
              <input :type="showPwdIn ? 'text':'password'" type="password"
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
              <input :type="showPwdUp ? 'text':'password'" type="password"
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
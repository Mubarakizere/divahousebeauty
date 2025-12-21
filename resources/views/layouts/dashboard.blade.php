<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') - Diva House Beauty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Inter font (clean, modern dashboard look) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Tailwind custom config (brand colors etc.) --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'Avenir', 'Helvetica', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#fef7ee',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            900: '#9a3412'
                        },
                        diva: {
                            primary: '#cc9966',
                            dark: '#b8845a'
                        }
                    },
                    boxShadow: {
                        card: '0 10px 24px -4px rgba(0,0,0,0.12)',
                    },
                    borderRadius: {
                        'xl': '0.75rem',
                        '2xl': '1rem',
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js (for sidebar toggle + modals) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font Awesome for icons --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>[x-cloak]{display:none!important}</style>

    @stack('head')
</head>

<body class="h-full bg-gray-50 text-gray-900 antialiased" x-data="{ mobileOpen: false }" x-cloak>

<div class="flex min-h-screen">

    {{-- ========================= --}}
    {{-- SIDEBAR: DESKTOP --}}
    {{-- ========================= --}}
    <aside class="hidden lg:flex lg:flex-shrink-0">
        <div class="flex flex-col w-64 bg-gray-900 text-gray-200 border-r border-black/20">
            {{-- Brand --}}
            <div class="flex items-center justify-between px-4 py-5 border-b border-gray-800">
                <div class="flex items-center space-x-2">
                    <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-diva-primary to-diva-dark flex items-center justify-center text-xs font-semibold text-white shadow-lg shadow-black/30 uppercase tracking-wide">
                        DH
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white leading-tight">Diva Dashboard</div>
                        <div class="text-[11px] text-gray-400 leading-none">Beauty Admin Panel</div>
                    </div>
                </div>
            </div>

            {{-- Nav scroll area --}}
            <nav class="flex-1 overflow-y-auto py-6 divide-y divide-gray-800 text-sm">
                <div class="px-2 space-y-1">

                    {{-- Back to Website --}}
                    <a href="{{ url('/') }}" target="_blank"
                       class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                              hover:bg-gray-700 hover:text-white text-gray-300">
                        <i class="fas fa-globe mr-3 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-300"></i>
                        <span>Back to Website</span>
                    </a>

                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                              {{ request()->is('dashboard')
                                    ? 'bg-gray-800 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-home mr-3 h-5 w-5 flex-shrink-0
                                  {{ request()->is('dashboard')
                                        ? 'text-white'
                                        : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    {{-- ADMIN SECTION --}}
                    @if(auth()->user()->hasRole('admin'))
                        <div class="pt-4">
                            <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                Admin
                            </p>

                            {{-- Products --}}
                            <a href="{{ route('admin.products.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                      {{ request()->is('admin/products*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-box mr-3 h-5 w-5 flex-shrink-0
                                          {{ request()->is('admin/products*')
                                                ? 'text-white'
                                                : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>Products</span>
                            </a>

                            {{-- Categories --}}
                            <a href="{{ route('admin.categories.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                      {{ request()->is('admin/categories*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-tags mr-3 h-5 w-5 flex-shrink-0
                                          {{ request()->is('admin/categories*')
                                                ? 'text-white'
                                                : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>Categories</span>
                            </a>

                            {{-- Brands --}}
                            <a href="{{ route('admin.brands.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                      {{ request()->is('admin/brands*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-industry mr-3 h-5 w-5 flex-shrink-0
                                          {{ request()->is('admin/brands*')
                                                ? 'text-white'
                                                : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>Brands</span>
                            </a>

                            {{-- Orders --}}
                            <a href="{{ route('admin.orders.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                      {{ request()->is('admin/orders*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-shopping-bag mr-3 h-5 w-5 flex-shrink-0
                                          {{ request()->is('admin/orders*')
                                                ? 'text-white'
                                                : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>Orders</span>
                            </a>
                        </div>
                    @endif

                    {{-- CUSTOMER SECTION --}}
                    @if(auth()->user()->hasRole('customer'))
                        <div class="pt-6">
                            <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                My Account
                            </p>

                            {{-- My Bookings --}}
                            <a href="{{ route('booking.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                      {{ request()->routeIs('booking.*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-calendar-alt mr-3 h-5 w-5 flex-shrink-0
                                          {{ request()->routeIs('booking.*')
                                                ? 'text-white'
                                                : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>My Bookings</span>
                            </a>

                            {{-- My Addresses --}}
                            <a href="{{ route('address.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                      {{ request()->routeIs('address.*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-map-marker-alt mr-3 h-5 w-5 flex-shrink-0
                                          {{ request()->routeIs('address.*')
                                                ? 'text-white'
                                                : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>My Addresses</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Logout --}}
                <div class="px-2 pt-8">
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                              text-gray-300 hover:bg-red-600 hover:text-white">
                        <i class="fas fa-sign-out-alt mr-3 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-white"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </nav>
        </div>
    </aside>

    {{-- ========================= --}}
    {{-- SIDEBAR: MOBILE (drawer) --}}
    {{-- ========================= --}}
    <div class="lg:hidden relative z-40" x-show="mobileOpen" x-transition.opacity.duration.200ms
         role="dialog" aria-modal="true">

        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"
             @click="mobileOpen = false"
             x-show="mobileOpen"
             x-transition.opacity.duration.200ms></div>

        <div class="fixed inset-0 flex z-40">
            {{-- Drawer --}}
            <div x-show="mobileOpen"
                 x-transition.duration.200ms
                 class="relative flex flex-col w-64 max-w-full bg-gray-900 text-gray-200 border-r border-black/20 shadow-xl shadow-black/40">

                {{-- Top brand + close --}}
                <div class="flex items-center justify-between px-4 py-5 border-b border-gray-800">
                    <div class="flex items-center space-x-2">
                        <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-diva-primary to-diva-dark flex items-center justify-center text-xs font-semibold text-white shadow-lg shadow-black/30 uppercase tracking-wide">
                            DH
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white leading-tight">Diva Dashboard</div>
                            <div class="text-[11px] text-gray-400 leading-none">Beauty Admin Panel</div>
                        </div>
                    </div>

                    <button @click="mobileOpen = false"
                            class="text-gray-400 hover:text-white transition-colors"
                            aria-label="Close menu">
                        <i class="fas fa-times h-6 w-6"></i>
                    </button>
                </div>

                {{-- Menu --}}
                <div class="flex-1 overflow-y-auto py-6 text-sm">
                    <nav class="px-2 space-y-1">
                        <a href="{{ url('/') }}" target="_blank"
                           class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                  text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-globe mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-300"></i>
                            <span>Back to Website</span>
                        </a>

                        <a href="{{ route('dashboard') }}"
                           class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                  {{ request()->is('dashboard')
                                        ? 'bg-gray-800 text-white'
                                        : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-home mr-3 h-6 w-6 flex-shrink-0
                                      {{ request()->is('dashboard')
                                            ? 'text-white'
                                            : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                            <span>Dashboard</span>
                        </a>

                        @if(auth()->user()->hasRole('admin'))
                            <div class="pt-4 pb-1">
                                <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                    Admin
                                </p>

                                {{-- Products --}}
                                <a href="{{ route('admin.products.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                          {{ request()->is('admin/products*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-box mr-3 h-6 w-6 flex-shrink-0
                                              {{ request()->is('admin/products*')
                                                    ? 'text-white'
                                                    : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>Products</span>
                                </a>

                                {{-- Categories --}}
                                <a href="{{ route('admin.categories.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                          {{ request()->is('admin/categories*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-tags mr-3 h-6 w-6 flex-shrink-0
                                              {{ request()->is('admin/categories*')
                                                    ? 'text-white'
                                                    : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>Categories</span>
                                </a>

                                {{-- Brands --}}
                                <a href="{{ route('admin.brands.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                          {{ request()->is('admin/brands*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-industry mr-3 h-6 w-6 flex-shrink-0
                                              {{ request()->is('admin/brands*')
                                                    ? 'text-white'
                                                    : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>Brands</span>
                                </a>

                                {{-- Orders --}}
                                <a href="{{ route('admin.orders.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                          {{ request()->is('admin/orders*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-shopping-bag mr-3 h-6 w-6 flex-shrink-0
                                              {{ request()->is('admin/orders*')
                                                    ? 'text-white'
                                                    : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>Orders</span>
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('customer'))
                            <div class="pt-6 pb-1">
                                <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                    My Account
                                </p>

                                <a href="{{ route('booking.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                          {{ request()->routeIs('booking.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-calendar-alt mr-3 h-6 w-6 flex-shrink-0
                                              {{ request()->routeIs('booking.*')
                                                    ? 'text-white'
                                                    : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>My Bookings</span>
                                </a>

                                <a href="{{ route('address.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                          {{ request()->routeIs('address.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-map-marker-alt mr-3 h-6 w-6 flex-shrink-0
                                              {{ request()->routeIs('address.*')
                                                    ? 'text-white'
                                                    : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>My Addresses</span>
                                </a>
                            </div>
                        @endif

                        {{-- Logout (mobile) --}}
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                           class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors
                                  text-gray-300 hover:bg-red-600 hover:text-white">
                            <i class="fas fa-sign-out-alt mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-white"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>

                    </nav>
                </div>
            </div>

            {{-- Clicking empty flex area also closes drawer --}}
            <div class="flex-1" @click="mobileOpen = false"></div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- MAIN COLUMN --}}
    {{-- ========================= --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Top header bar --}}
        <header class="bg-white border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    {{-- Mobile menu button --}}
                    <button
                        @click="mobileOpen = true"
                        class="lg:hidden -ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white"
                        aria-label="Open menu">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    {{-- Page title --}}
                    <h1 class="ml-4 lg:ml-0 text-2xl font-semibold text-gray-900 tracking-tight">
                        @yield('title', 'Dashboard')
                    </h1>
                </div>

                {{-- User info --}}
                <div class="flex items-center space-x-4">
                    <div class="text-right leading-tight hidden sm:block">
                        <div class="text-sm text-gray-700 font-medium">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-[11px] text-gray-500">
                            @if(auth()->user()->hasRole('admin'))
                                Admin
                            @elseif(auth()->user()->hasRole('customer'))
                                Customer
                            @else
                                User
                            @endif
                        </div>
                    </div>

                    {{-- Small badge / avatar circle --}}
                    <div class="relative flex items-center">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-700 text-xs font-semibold uppercase shadow-inner ring-1 ring-white/60">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>

                        @if(auth()->user()->hasRole('admin'))
                            <span class="absolute -bottom-1 -right-1 inline-flex items-center rounded-full bg-blue-600 px-1.5 py-0.5 text-[10px] font-medium text-white shadow ring-2 ring-white">
                                Admin
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto focus:outline-none">
            <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>

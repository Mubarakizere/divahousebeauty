{{-- resources/views/admin/products/index.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <title>Manage Products — Diva House Beauty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Inter font --}}
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
                        sans: ['Inter','system-ui','Avenir','Helvetica','Arial','sans-serif'],
                    },
                    colors: {
                        primary: { 500:'#f97316', 600:'#ea580c', 700:'#c2410c' },
                        diva: { primary:'#cc9966', dark:'#b8845a' }
                    },
                    boxShadow: {
                        card: '0 10px 24px -8px rgba(15,23,42,.15), 0 1px 2px rgba(15,23,42,.06)',
                    },
                    borderRadius: { xl:'0.75rem', '2xl':'1rem' }
                }
            }
        }
    </script>

    {{-- Alpine v3 --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        [x-cloak]{display:none!important}
    </style>
</head>

<body class="h-full bg-gray-50 text-gray-900 antialiased"
      x-data="{
          mobileOpen:false,
          del:{ show:false, name:'', action:'' },
          openDelete(action, name){ this.del.action = action; this.del.name = name; this.del.show = true; },
          closeDelete(){ this.del.show = false; }
      }"
      x-on:keydown.window.escape="closeDelete()">

<div class="flex min-h-screen">

    {{-- ========================= --}}
    {{-- SIDEBAR: DESKTOP         --}}
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

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto py-6 divide-y divide-gray-800 text-sm">
                <div class="px-2 space-y-1">

                    <a href="{{ url('/') }}" target="_blank"
                       class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors text-gray-300 hover:bg-gray-700 hover:text-white">
                        <i class="fas fa-globe mr-3 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-300"></i>
                        <span>Back to Website</span>
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-home mr-3 h-5 w-5 flex-shrink-0 {{ request()->is('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <div class="pt-4">
                            <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">Admin</p>

                            <a href="{{ route('admin.products.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('admin/products*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-box mr-3 h-5 w-5 flex-shrink-0 {{ request()->is('admin/products*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>Products</span>
                            </a>

                            <a href="{{ route('admin.categories.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('admin/categories*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-tags mr-3 h-5 w-5 flex-shrink-0 {{ request()->is('admin/categories*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>Categories</span>
                            </a>

                            <a href="{{ route('admin.orders.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('admin/orders*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas a-shopping-bag mr-3 h-5 w-5 flex-shrink-0 {{ request()->is('admin/orders*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>Orders</span>
                            </a>
                        </div>
                    @endif

                    @if(auth()->user()->role === 'customer')
                        <div class="pt-6">
                            <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">My Account</p>
                            <a href="{{ route('booking.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->routeIs('booking.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-calendar-alt mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('booking.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>My Bookings</span>
                            </a>
                            <a href="{{ route('address.index') }}"
                               class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->routeIs('address.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-map-marker-alt mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('address.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                <span>My Addresses</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Logout --}}
                <div class="px-2 pt-8">
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors text-gray-300 hover:bg-red-600 hover:text-white">
                        <i class="fas fa-sign-out-alt mr-3 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-white"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </nav>
        </div>
    </aside>

    {{-- ========================= --}}
    {{-- SIDEBAR: MOBILE (drawer) --}}
    {{-- ========================= --}}
    <div class="lg:hidden" x-cloak x-show="mobileOpen" x-transition.opacity.duration.200ms role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"
             @click="mobileOpen = false"
             x-show="mobileOpen"
             x-transition.opacity.duration.200ms></div>

        <div class="fixed inset-0 flex z-40">
            <div x-show="mobileOpen"
                 x-transition.duration.200ms
                 class="relative flex flex-col w-64 max-w-full bg-gray-900 text-gray-200 border-r border-black/20 shadow-xl shadow-black/40">
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
                    <button @click="mobileOpen = false" class="text-gray-400 hover:text-white" aria-label="Close menu">
                        <i class="fas fa-times h-6 w-6"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto py-6 text-sm">
                    <nav class="px-2 space-y-1">
                        <a href="{{ url('/') }}" target="_blank"
                           class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors text-gray-300 hover:bg-gray-700 hover:text-white">
                            <i class="fas fa-globe mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-300"></i>
                            <span>Back to Website</span>
                        </a>

                        <a href="{{ route('dashboard') }}"
                           class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <i class="fas fa-home mr-3 h-6 w-6 flex-shrink-0 {{ request()->is('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                            <span>Dashboard</span>
                        </a>

                        @if(auth()->user()->role === 'admin')
                            <div class="pt-4 pb-1">
                                <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">Admin</p>

                                <a href="{{ route('admin.products.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('admin/products*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-box mr-3 h-6 w-6 flex-shrink-0 {{ request()->is('admin/products*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>Products</span>
                                </a>

                                <a href="{{ route('admin.categories.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('admin/categories*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-tags mr-3 h-6 w-6 flex-shrink-0 {{ request()->is('admin/categories*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>Categories</span>
                                </a>

                                <a href="{{ route('admin.orders.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->is('admin/orders*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-shopping-bag mr-3 h-6 w-6 flex-shrink-0 {{ request()->is('admin/orders*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>Orders</span>
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->role === 'customer')
                            <div class="pt-6 pb-1">
                                <p class="px-2 mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">My Account</p>
                                <a href="{{ route('booking.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->routeIs('booking.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-calendar-alt mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('booking.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>My Bookings</span>
                                </a>
                                <a href="{{ route('address.index') }}"
                                   class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors {{ request()->routeIs('address.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-map-marker-alt mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('address.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    <span>My Addresses</span>
                                </a>
                            </div>
                        @endif

                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                           class="group flex items-center rounded-md px-2 py-2 font-medium transition-colors text-gray-300 hover:bg-red-600 hover:text-white">
                            <i class="fas fa-sign-out-alt mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-white"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </nav>
                </div>
            </div>

            <div class="flex-1" @click="mobileOpen = false"></div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- MAIN COLUMN               --}}
    {{-- ========================= --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Top header bar --}}
        <header class="bg-white border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center">
                    <button
                        @click="mobileOpen = true"
                        class="lg:hidden -ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white"
                        aria-label="Open menu">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <h1 class="ml-4 lg:ml-0 text-2xl font-semibold text-gray-900 tracking-tight">
                        Manage Products
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right leading-tight hidden sm:block">
                        <div class="text-sm text-gray-700 font-medium">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-[11px] text-gray-500">
                            @if(auth()->user()->role === 'admin') Admin
                            @elseif(auth()->user()->role === 'customer') Customer
                            @else User
                            @endif
                        </div>
                    </div>
                    <div class="relative flex items-center">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-700 text-xs font-semibold uppercase shadow-inner ring-1 ring-white/60">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        @if(auth()->user()->role === 'admin')
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

                {{-- ========================= --}}
                {{-- PAGE HEADER / SUMMARY     --}}
                {{-- ========================= --}}
                <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-indigo-700 text-white shadow-card ring-1 ring-white/10">
                    <div class="pointer-events-none absolute -top-8 -right-8 h-28 w-28 rounded-full bg-white/10 blur-xl"></div>
                    <div class="pointer-events-none absolute bottom-0 left-1/4 h-24 w-24 rounded-full bg-white/5 blur-2xl"></div>

                    <div class="relative z-10 p-6 sm:p-8 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-start gap-3">
                                <h2 class="text-2xl lg:text-3xl font-bold leading-tight tracking-tight text-white flex items-center">
                                    <i class="fa-solid fa-box-open mr-3 text-white/90"></i>
                                    Product Management
                                </h2>
                                <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-medium text-white ring-1 ring-white/30 backdrop-blur-sm">
                                    <i class="fa-solid fa-warehouse mr-1 text-[10px]"></i>
                                    Inventory Control
                                </span>
                            </div>

                            <p class="text-blue-100 text-sm sm:text-base mt-3 max-w-2xl">
                                View, edit, and organize all products currently in your store.
                            </p>

                            <div class="mt-4 flex flex-wrap gap-4 text-sm text-white/90">
                                <div class="flex items-center">
                                    <div class="text-left leading-tight">
                                        <div class="text-[11px] uppercase tracking-wide text-white/70 font-medium">Total Products</div>
                                        <div class="text-lg font-semibold text-white -mt-0.5">
                                            {{ number_format($products->total()) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="hidden sm:block w-px h-10 bg-white/20 rounded-full"></div>

                                <div class="flex items-center">
                                    <div class="text-left leading-tight">
                                        <div class="text-[11px] uppercase tracking-wide text-white/70 font-medium">Showing</div>
                                        <div class="text-lg font-semibold text-white -mt-0.5">
                                            {{ $products->firstItem() }} - {{ $products->lastItem() }}
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $lowStockCount = $products->filter(fn($p) => (int)$p->stock <= 3)->count();
                                @endphp

                                <div class="hidden sm:block w-px h-10 bg-white/20 rounded-full"></div>

                                <div class="flex items-center">
                                    <div class="text-left leading-tight">
                                        <div class="text-[11px] uppercase tracking-wide text-white/70 font-medium">Low Stock Items</div>
                                        <div class="text-lg font-semibold text-white -mt-0.5">{{ $lowStockCount }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 w-full sm:w-auto flex flex-col items-stretch sm:items-end gap-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.products.import') }}"
                                   class="inline-flex items-center justify-center rounded-lg bg-green-500/90 px-4 py-2 text-sm font-medium text-white shadow-card hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-white/60 focus:ring-offset-2 focus:ring-offset-indigo-700 transition">
                                    <i class="fa-solid fa-file-excel mr-2 text-[12px]"></i>
                                    Import
                                </a>
                                <a href="{{ route('admin.products.create') }}"
                                   class="inline-flex items-center justify-center rounded-lg bg-white/90 px-4 py-2 text-sm font-medium text-gray-900 shadow-card hover:bg-white focus:outline-none focus:ring-2 focus:ring-white/60 focus:ring-offset-2 focus:ring-offset-indigo-700 transition">
                                    <i class="fa-solid fa-plus mr-2 text-[12px]"></i>
                                    Add Product
                                </a>
                            </div>
                            <p class="text-[11px] text-white/70 leading-tight text-center sm:text-right">
                                Keep catalog fresh, updated and accurate ✨
                            </p>
                        </div>
                    </div>
                </section>

                {{-- ========================= --}}
                {{-- SEARCH / FILTER BAR       --}}
                {{-- ========================= --}}
                <section class="rounded-2xl bg-white border border-gray-100 shadow-card ring-1 ring-black/5 p-6 mt-6">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4 sm:items-end">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search products</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                </div>
                                <input
                                    type="text"
                                    name="search"
                                    id="search"
                                    value="{{ request('search') }}"
                                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30"
                                    placeholder="Search by name, brand, category..."
                                >
                            </div>
                        </div>

                        <div class="flex gap-3 sm:w-auto">
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-card hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900/20 focus:ring-offset-2 focus:ring-offset-white transition">
                                <i class="fa-solid fa-magnifying-glass mr-2 text-[12px]"></i>
                                Search
                            </button>

                            @if(request('search'))
                                <a href="{{ route('admin.products.index') }}"
                                   class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-gray-200/70 focus:outline-none focus:ring-2 focus:ring-gray-400/30 focus:ring-offset-2 focus:ring-offset-white transition">
                                    <i class="fa-solid fa-xmark mr-2 text-[12px]"></i>
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </section>

                {{-- ========================= --}}
                {{-- FLASH MESSAGE             --}}
                {{-- ========================= --}}
                @if(session('success'))
                    <section class="mt-6 rounded-xl bg-green-50 border border-green-200 text-green-800 shadow-card px-4 py-3 text-sm flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-green-500 text-base leading-none mt-0.5"></i>
                        <div class="flex-1">
                            <div class="font-semibold">Success</div>
                            <div>{{ session('success') }}</div>
                        </div>
                    </section>
                @endif

                {{-- ========================= --}}
                {{-- TABLE / LIST              --}}
                {{-- ========================= --}}
                @if($products->count())
                    <section class="mt-6 rounded-2xl bg-white border border-gray-100 shadow-card ring-1 ring-black/5 overflow-hidden">
                        {{-- Desktop header row --}}
                        <div class="hidden lg:block bg-gray-50 border-b border-gray-200">
                            <div class="px-6 py-3">
                                <div class="grid grid-cols-8 gap-4 text-[11px] font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="text-center">#</div>
                                    <div class="text-center">Image</div>
                                    <div class="text-left">Product</div>
                                    <div class="text-center">Price (RWF)</div>
                                    <div class="text-center">Stock</div>
                                    <div class="text-center">Category</div>
                                    <div class="text-center">Brand</div>
                                    <div class="text-center">Actions</div>
                                </div>
                            </div>
                        </div>

                        {{-- Rows --}}
                        <div class="divide-y divide-gray-200">
                            @foreach ($products as $index => $product)
                                @php
                                    $imgs = is_array($product->images) ? $product->images : (json_decode($product->images ?? '[]', true) ?: []);
                                    $image = $imgs[0] ?? 'default.jpg';
                                @endphp

                                {{-- Desktop row --}}
                                <div class="hidden lg:block px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                                    <div class="grid grid-cols-8 gap-4 items-center text-sm text-gray-700">
                                        <div class="text-center font-semibold text-gray-900">
                                            {{ $products->firstItem() + $index }}
                                        </div>

                                        <div class="flex justify-center">
                                            <div class="relative">
                                                <img src="{{ asset('storage/' . $image) }}"
                                                     alt="{{ $product->name }}"
                                                     class="h-12 w-12 rounded-lg object-cover ring-1 ring-gray-200 shadow-inner bg-gray-100">
                                                @if((int)$product->stock <= 3)
                                                    <span class="absolute -top-1 -right-1 inline-flex items-center rounded-full bg-red-600 text-white text-[10px] font-semibold px-1.5 py-0.5 ring-2 ring-white shadow-card">Low</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 truncate flex items-center gap-2">
                                                {{ $product->name }}
                                                @if($product->is_featured ?? false)
                                                    <span class="inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 text-[10px] font-medium px-1.5 py-0.5 ring-1 ring-yellow-200/60">
                                                        <i class="fa-solid fa-star mr-1 text-[9px]"></i> Featured
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-gray-500">ID: {{ $product->id }}</div>
                                        </div>

                                        <div class="text-center">
                                            @if($product->shipping_type === 'both')
                                                <div class="text-[11px] text-blue-600 font-medium"><i class="fa-solid fa-bolt text-[9px]"></i> {{ number_format($product->express_price) }}</div>
                                                <div class="text-[11px] text-green-600 font-medium"><i class="fa-solid fa-clock text-[9px]"></i> {{ number_format($product->standard_price ?? 0) }}</div>
                                            @elseif($product->shipping_type === 'standard_only')
                                                <div class="font-semibold text-gray-900">{{ number_format($product->standard_price ?? 0) }}</div>
                                                <span class="text-[10px] text-green-600"><i class="fa-solid fa-clock"></i> Std</span>
                                            @else
                                                <div class="font-semibold text-gray-900">{{ number_format($product->express_price) }}</div>
                                                <span class="text-[10px] text-blue-600"><i class="fa-solid fa-bolt"></i> Exp</span>
                                            @endif
                                        </div>

                                        <div class="text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                                {{ (int)$product->stock > 10
                                                    ? 'bg-green-100 text-green-800 ring-1 ring-green-200/60'
                                                    : ((int)$product->stock > 0
                                                        ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200/60'
                                                        : 'bg-red-100 text-red-800 ring-1 ring-red-200/60') }}">
                                                {{ $product->stock }}
                                            </span>
                                        </div>

                                        <div class="text-center text-gray-900 text-sm font-medium truncate">
                                            {{ $product->category->name ?? '—' }}
                                        </div>

                                        <div class="text-center text-gray-900 text-sm font-medium truncate">
                                            {{ $product->brand->name ?? '—' }}
                                        </div>

                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="inline-flex items-center rounded-lg bg-yellow-50 px-2.5 py-2 text-yellow-700 ring-1 ring-yellow-200/60 hover:bg-yellow-100 hover:text-yellow-800 hover:ring-yellow-300/70 text-[12px] font-medium transition"
                                               title="Edit Product">
                                                <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                            </a>

                                            <button type="button"
                                                    @click="openDelete('{{ route('admin.products.destroy', $product) }}','{{ addslashes($product->name) }}')"
                                                    class="inline-flex items-center rounded-lg bg-red-50 px-2.5 py-2 text-red-700 ring-1 ring-red-200/60 hover:bg-red-100 hover:text-red-800 hover:ring-red-300/70 text-[12px] font-medium transition"
                                                    title="Delete Product">
                                                <i class="fa-solid fa-trash text-[13px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mobile card --}}
                                <div class="lg:hidden p-4 border-b border-gray-200 last:border-b-0 bg-white hover:bg-gray-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start gap-4 flex-1 min-w-0">
                                            <div class="relative flex-shrink-0">
                                                <img src="{{ asset('storage/' . $image) }}"
                                                     alt="{{ $product->name }}"
                                                     class="h-16 w-16 rounded-lg object-cover ring-1 ring-gray-200 shadow-inner bg-gray-100">
                                                <div class="absolute -top-1 -left-1 bg-white text-[10px] text-gray-500 font-medium px-1.5 py-0.5 rounded-md ring-1 ring-gray-200/70 shadow-card">
                                                    #{{ $products->firstItem() + $index }}
                                                </div>
                                                @if((int)$product->stock <= 3)
                                                    <span class="absolute -bottom-1 -right-1 inline-flex items-center rounded-md bg-red-600 text-white text-[10px] font-semibold px-1.5 py-0.5 ring-2 ring-white shadow-card">Low</span>
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-start gap-2">
                                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                                                    @if($product->is_featured ?? false)
                                                        <span class="inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 text-[10px] font-medium px-1.5 py-0.5 ring-1 ring-yellow-200/60">
                                                            <i class="fa-solid fa-star mr-1 text-[9px]"></i> Featured
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="mt-1 flex flex-wrap text-[12px] text-gray-600 gap-x-4 gap-y-1">
                                                    <span class="flex items-center gap-1">
                                                        <span class="text-gray-500">Price:</span>
                                                        @if($product->shipping_type === 'both')
                                                            <span class="text-blue-600 font-semibold"><i class="fa-solid fa-bolt text-[9px]"></i> {{ number_format($product->express_price) }}</span>
                                                            <span class="text-gray-400">/</span>
                                                            <span class="text-green-600 font-semibold"><i class="fa-solid fa-clock text-[9px]"></i> {{ number_format($product->standard_price ?? 0) }}</span>
                                                        @elseif($product->shipping_type === 'standard_only')
                                                            <span class="font-semibold text-gray-900">{{ number_format($product->standard_price ?? 0) }} RWF</span>
                                                            <span class="text-green-600 text-[10px]"><i class="fa-solid fa-clock"></i></span>
                                                        @else
                                                            <span class="font-semibold text-gray-900">{{ number_format($product->express_price) }} RWF</span>
                                                            <span class="text-blue-600 text-[10px]"><i class="fa-solid fa-bolt"></i></span>
                                                        @endif
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <span class="text-gray-500">Stock:</span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[11px] font-semibold
                                                            {{ (int)$product->stock > 10
                                                                ? 'bg-green-100 text-green-800 ring-1 ring-green-200/60'
                                                                : ((int)$product->stock > 0
                                                                    ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200/60'
                                                                    : 'bg-red-100 text-red-800 ring-1 ring-red-200/60') }}">
                                                            {{ $product->stock }}
                                                        </span>
                                                    </span>
                                                </div>

                                                <div class="mt-1 text-[12px] text-gray-500 leading-relaxed">
                                                    <div>Category: <span class="font-medium text-gray-800">{{ $product->category->name ?? '—' }}</span></div>
                                                    <div>Brand: <span class="font-medium text-gray-800">{{ $product->brand->name ?? '—' }}</span></div>
                                                    <div class="text-[11px] text-gray-400">ID: {{ $product->id }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-end gap-2 ml-4 shrink-0">
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="p-2 rounded-lg bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200/60 hover:bg-yellow-100 hover:text-yellow-800 hover:ring-yellow-300/70 text-[12px] transition"
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                            </a>

                                            <button type="button"
                                                    @click="openDelete('{{ route('admin.products.destroy', $product) }}','{{ addslashes($product->name) }}')"
                                                    class="p-2 rounded-lg bg-red-50 text-red-700 ring-1 ring-red-200/60 hover:bg-red-100 hover:text-red-800 hover:ring-red-300/70 text-[12px] transition"
                                                    title="Delete">
                                                <i class="fa-solid fa-trash text-[13px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- ========================= --}}
                    {{-- PAGINATION               --}}
                    {{-- ========================= --}}
                    @if($products->hasPages())
                        <section class="rounded-2xl bg-white border border-gray-100 shadow-card ring-1 ring-black/5 px-4 py-4 sm:px-6 mt-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between text-sm text-gray-700">

                                <div class="text-center sm:text-left">
                                    <p class="text-gray-700 text-sm">
                                        Showing
                                        <span class="font-semibold text-gray-900">{{ $products->firstItem() }}</span>
                                        to
                                        <span class="font-semibold text-gray-900">{{ $products->lastItem() }}</span>
                                        of
                                        <span class="font-semibold text-gray-900">{{ $products->total() }}</span>
                                        products
                                    </p>
                                </div>

                                <nav class="flex flex-wrap items-center justify-center gap-2">
                                    @if($products->onFirstPage())
                                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-2 text-[13px] font-medium text-gray-400 ring-1 ring-inset ring-gray-200 cursor-default select-none">
                                            <i class="fa-solid fa-chevron-left text-[11px] mr-1.5"></i> Prev
                                        </span>
                                    @else
                                        <a href="{{ $products->previousPageUrl() }}"
                                           class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-[13px] font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 hover:text-gray-900 hover:ring-gray-400 transition">
                                            <i class="fa-solid fa-chevron-left text-[11px] mr-1.5"></i> Prev
                                        </a>
                                    @endif

                                    @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                        @if($page == $products->currentPage())
                                            <span class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-2 text-[13px] font-semibold text-blue-600 ring-1 ring-inset ring-blue-200">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}"
                                               class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-[13px] font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 hover:text-gray-900 hover:ring-gray-400 transition">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    @if($products->hasMorePages())
                                        <a href="{{ $products->nextPageUrl() }}"
                                           class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-[13px] font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 hover:text-gray-900 hover:ring-gray-400 transition">
                                            Next <i class="fa-solid fa-chevron-right text-[11px] ml-1.5"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-2 text-[13px] font-medium text-gray-400 ring-1 ring-inset ring-gray-200 cursor-default select-none">
                                            Next <i class="fa-solid fa-chevron-right text-[11px] ml-1.5"></i>
                                        </span>
                                    @endif
                                </nav>
                            </div>
                        </section>
                    @endif

                @else
                    {{-- EMPTY STATE --}}
                    <section class="rounded-2xl bg-white border border-gray-100 shadow-card ring-1 ring-black/5 p-12 text-center mt-6">
                        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 shadow-inner ring-1 ring-gray-200">
                            <i class="fa-solid fa-box-open text-2xl"></i>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No products found</h3>

                        <p class="text-sm text-gray-600 max-w-md mx-auto mb-8 leading-relaxed">
                            @if(request('search'))
                                No products match your search criteria. Try adjusting your keywords or clearing the filter.
                            @else
                                You haven’t added any products yet. Add your first product to start selling.
                            @endif
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            @if(request('search'))
                                <a href="{{ route('admin.products.index') }}"
                                   class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-card hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900/20 focus:ring-offset-2 focus:ring-offset-white transition">
                                    <i class="fa-solid fa-xmark mr-2 text-[12px]"></i>
                                    Clear Search
                                </a>
                            @endif

                            <a href="{{ route('admin.products.create') }}"
                               class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-card hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/30 focus:ring-offset-2 focus:ring-offset-white transition">
                                <i class="fa-solid fa-plus mr-2 text-[12px]"></i>
                                Add First Product
                            </a>
                        </div>
                    </section>
                @endif
            </div>
        </main>
    </div>
</div>

{{-- ========================= --}}
{{-- DELETE CONFIRMATION MODAL --}}
{{-- ========================= --}}
<div x-cloak x-show="del.show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" @click="closeDelete()"></div>

    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl ring-1 ring-black/10">
        <div class="px-6 pt-6 pb-4">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900">Delete product?</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        You’re about to delete <span class="font-medium text-gray-900" x-text="del.name"></span>.
                        This action can’t be undone.
                    </p>
                </div>
            </div>

            <form :action="del.action" method="POST" x-ref="delForm" class="sr-only">
                @csrf
                @method('DELETE')
            </form>
        </div>

        <div class="px-6 pb-6 pt-2 flex flex-col sm:flex-row sm:justify-end gap-3">
            <button type="button"
                    class="inline-flex justify-center items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition"
                    @click="closeDelete()">
                Cancel
            </button>
            <button type="button"
                    class="inline-flex justify-center items-center px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition"
                    @click="$refs.delForm.submit()">
                Delete
            </button>
        </div>
    </div>
</div>

</body>
</html>

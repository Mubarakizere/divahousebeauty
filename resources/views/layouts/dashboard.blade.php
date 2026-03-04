<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') - Diva House Beauty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/icons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/icons/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>[x-cloak]{display:none!important}</style>

    @stack('head')
</head>

<body class="h-full bg-gray-50 text-gray-900 antialiased" x-data="{ sidebarOpen: false }" x-cloak>

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    @php
        $navItems = [];

        if (auth()->user()->role === 'admin') {
            $navItems = [
                ['label' => 'Dashboard', 'icon' => 'fa-home', 'url' => route('dashboard'), 'match' => 'dashboard'],
                ['divider' => true],
                ['label' => 'Products', 'icon' => 'fa-box', 'url' => route('admin.products.index'), 'match' => 'admin/products*'],
                ['label' => 'Categories', 'icon' => 'fa-tags', 'url' => route('admin.categories.index'), 'match' => 'admin/categories*'],
                ['label' => 'Brands', 'icon' => 'fa-industry', 'url' => route('admin.brands.index'), 'match' => 'admin/brands*'],
                ['label' => 'Orders', 'icon' => 'fa-shopping-bag', 'url' => route('admin.orders.index'), 'match' => 'admin/orders*'],
                ['divider' => true],
                ['label' => 'Coupons', 'icon' => 'fa-ticket-alt', 'url' => route('admin.coupons.index'), 'match' => 'admin/coupons*'],
                ['label' => 'Banners', 'icon' => 'fa-images', 'url' => route('admin.banners.index'), 'match' => 'admin/banners*'],
                ['label' => 'Reviews', 'icon' => 'fa-star', 'url' => route('admin.reviews.index'), 'match' => 'admin/reviews*'],
                ['divider' => true],
                ['label' => 'Users', 'icon' => 'fa-users', 'url' => route('admin.users.index'), 'match' => 'admin/users*'],
                ['label' => 'Newsletter', 'icon' => 'fa-envelope', 'url' => route('admin.newsletter.index'), 'match' => 'admin/newsletter*'],
                ['label' => 'Bulk Import', 'icon' => 'fa-file-import', 'url' => route('admin.bulk-import.index'), 'match' => 'admin/bulk-import*'],
            ];
        } elseif (auth()->user()->role === 'customer') {
            $navItems = [
                ['label' => 'Dashboard', 'icon' => 'fa-home', 'url' => route('dashboard'), 'match' => 'dashboard'],
                ['divider' => true],
                ['label' => 'My Orders', 'icon' => 'fa-shopping-bag', 'url' => route('orders.index'), 'routeMatch' => 'orders.*'],
                ['label' => 'My Addresses', 'icon' => 'fa-map-marker-alt', 'url' => route('address.index'), 'routeMatch' => 'address.*'],
            ];
        }
    @endphp

    {{-- Desktop sidebar --}}
    <aside class="hidden lg:flex lg:flex-shrink-0">
        <div class="flex flex-col w-56 bg-slate-800 border-r border-slate-700">
            {{-- Brand --}}
            <div class="px-4 py-4 border-b border-slate-700">
                <div class="text-sm font-semibold text-white">Diva House</div>
                <div class="text-xs text-slate-400">Admin</div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto py-3 text-[13px]">
                <div class="px-2 space-y-0.5">
                    <a href="{{ url('/') }}" target="_blank"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                        <i class="fas fa-external-link-alt w-4 text-center text-xs"></i>
                        <span>View Site</span>
                    </a>

                    @foreach($navItems as $item)
                        @if(isset($item['divider']))
                            <div class="my-2 border-t border-slate-700"></div>
                        @else
                            @php
                                $isActive = isset($item['routeMatch'])
                                    ? request()->routeIs($item['routeMatch'])
                                    : request()->is($item['match']);
                            @endphp
                            <a href="{{ $item['url'] }}"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-md transition-colors
                                      {{ $isActive
                                            ? 'bg-slate-700 text-white'
                                            : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">
                                <i class="fas {{ $item['icon'] }} w-4 text-center text-xs {{ $isActive ? 'text-white' : 'text-slate-500' }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </nav>

            {{-- Bottom --}}
            <div class="border-t border-slate-700 px-2 py-3">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px] text-slate-400 hover:text-red-400 hover:bg-slate-700 transition-colors">
                    <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i>
                    <span>Log out</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </aside>

    {{-- Mobile sidebar overlay --}}
    <div class="lg:hidden relative z-40" x-show="sidebarOpen" x-cloak>
        <div class="fixed inset-0 bg-black/30" @click="sidebarOpen = false"
             x-show="sidebarOpen" x-transition.opacity.duration.200ms></div>

        <div class="fixed inset-0 flex z-40">
            <div x-show="sidebarOpen" x-transition.duration.200ms
                 class="relative flex flex-col w-56 max-w-full bg-slate-800 border-r border-slate-700 shadow-lg">

                <div class="flex items-center justify-between px-4 py-4 border-b border-slate-700">
                    <div>
                        <div class="text-sm font-semibold text-white">Diva House</div>
                        <div class="text-xs text-slate-400">Admin</div>
                    </div>
                    <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white transition-colors" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto py-3 text-[13px]">
                    <div class="px-2 space-y-0.5">
                        <a href="{{ url('/') }}" target="_blank"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                            <i class="fas fa-external-link-alt w-4 text-center text-xs"></i>
                            <span>View Site</span>
                        </a>

                        @foreach($navItems as $item)
                            @if(isset($item['divider']))
                                <div class="my-2 border-t border-gray-100"></div>
                            @else
                                @php
                                    $isActive = isset($item['routeMatch'])
                                        ? request()->routeIs($item['routeMatch'])
                                        : request()->is($item['match']);
                                @endphp
                                <a href="{{ $item['url'] }}"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-md transition-colors
                                          {{ $isActive
                                                ? 'bg-slate-700 text-white'
                                                : 'text-slate-300 hover:text-white hover:bg-slate-700' }}">
                                    <i class="fas {{ $item['icon'] }} w-4 text-center text-xs {{ $isActive ? 'text-white' : 'text-slate-500' }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </nav>

                <div class="border-t border-slate-700 px-2 py-3">
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px] text-slate-400 hover:text-red-400 hover:bg-slate-700 transition-colors">
                        <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i>
                        <span>Log out</span>
                    </a>
                    <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>

            <div class="flex-1" @click="sidebarOpen = false"></div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200">
            <div class="flex items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true"
                            class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 transition-colors"
                            aria-label="Open menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm text-gray-700">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-400">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>

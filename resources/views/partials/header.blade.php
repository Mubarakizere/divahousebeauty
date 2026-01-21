<header class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" class="h-8 w-auto" alt="Logo">
        </a>

        <!-- Mobile Toggle Button -->
        <button id="mobile-menu-toggle" class="md:hidden text-gray-700 focus:outline-none">
            <i class="ph ph-list text-3xl"></i>
        </button>

        <!-- Navigation -->
        <nav class="hidden md:flex space-x-6 items-center text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-pink-600">Home</a>
            <a href="{{ route('category') }}" class="hover:text-pink-600">Shop</a>
            <div class="relative group">
                <button class="hover:text-pink-600 flex items-center gap-1">
                    Ihuriro <i class="ph ph-caret-down text-xs mt-[1px]"></i>
                </button>
                <div class="absolute left-0 mt-2 hidden group-hover:block bg-white shadow-md rounded-md p-2 space-y-2 z-20">
                    <a href="{{ route('booking.create') }}" class="block hover:text-pink-600">Booking</a>
                </div>
            </div>
            @guest
                <a href="#signin-modal" class="hover:text-pink-600">Sign in / Sign up</a>
            @else
                <div class="relative group">
                    <button class="hover:text-pink-600">
                        {{ Auth::user()->name }}
                    </button>
                    <div class="absolute left-0 mt-2 hidden group-hover:block bg-white shadow-md rounded-md p-2 space-y-2 z-20">
                        <a href="{{ route('dashboard') }}" class="block hover:text-pink-600">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="block">
                            @csrf
                            <button type="submit" class="text-left w-full hover:text-red-600">Logout</button>
                        </form>
                    </div>
                </div>
            @endguest
            
            <!-- Currency Selector (Tailwind Design) -->
            <div x-data="{ open: false }" @click.away="open = false" class="relative">
                <button @click="open = !open" 
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-1">
                    <span class="text-lg leading-none">🇺🇸</span>
                    <span class="font-semibold">USD</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                     style="display: none;">
                    
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                        Select Currency
                    </div>
                    
                    <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('USD'); open = false" 
                       class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
                       data-currency="USD">
                        <span class="text-2xl leading-none">🇺🇸</span>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm">USD</div>
                            <div class="text-xs text-gray-500">US Dollar</div>
                        </div>
                    </a>
                    
                    <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('EUR'); open = false" 
                       class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
                       data-currency="EUR">
                        <span class="text-2xl leading-none">🇪🇺</span>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm">EUR</div>
                            <div class="text-xs text-gray-500">Euro</div>
                        </div>
                    </a>
                    
                    <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('GBP'); open = false" 
                       class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
                       data-currency="GBP">
                        <span class="text-2xl leading-none">🇬🇧</span>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm">GBP</div>
                            <div class="text-xs text-gray-500">British Pound</div>
                        </div>
                    </a>
                    
                    <a href="#" @click.prevent="window.currencyConverter?.changeCurrency('RWF'); open = false" 
                       class="currency-item flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors duration-150" 
                       data-currency="RWF">
                        <span class="text-2xl leading-none">🇷🇼</span>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm">RWF</div>
                            <div class="text-xs text-gray-500">Rwandan Franc</div>
                        </div>
                    </a>
                </div>
            </div>
            
            <a href="{{ route('cart') }}" class="relative hover:text-pink-600">
                <i class="ph ph-shopping-cart text-xl"></i>
                @if($count > 0)
                    <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $count }}</span>
                @endif
            </a>
        </nav>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden flex flex-col px-4 pb-4 space-y-3 bg-white border-t">
        <a href="{{ route('home') }}" class="block">Home</a>
        <a href="{{ route('category') }}" class="block">Shop</a>
        <a href="{{ route('booking.create') }}" class="block">Booking</a>
        @guest
            <a href="#signin-modal" class="block">Sign in / Sign up</a>
        @else
            <a href="{{ route('dashboard') }}" class="block">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-left w-full">Logout</button>
            </form>
        @endguest
        <a href="{{ route('cart') }}" class="block">Cart ({{ $count }})</a>
    </div>

    <!-- Toggle Script -->
    <script>
        const toggle = document.getElementById('mobile-menu-toggle');
        const menu = document.getElementById('mobile-menu');
        toggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</header>

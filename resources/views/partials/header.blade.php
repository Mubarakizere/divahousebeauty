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

<footer class="bg-gray-900 text-white pt-12">
    {{-- Newsletter --}}
    <div class="bg-cover bg-center py-10 px-4 sm:px-8" style="background-image: url('{{ asset('assets/images/demos/demo-14/bg-1.jpg') }}')">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-2xl font-semibold text-white mb-2">Join Our Newsletter</h2>
            <p class="mb-4 text-gray-300">Subscribe to get info about products and exclusive coupons</p>
            <form action="#" method="POST" class="max-w-lg mx-auto flex gap-2">
                <input type="email" required class="w-full px-4 py-2 rounded-lg text-black" placeholder="Enter your Email Address">
                <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition">Subscribe</button>
            </form>
        </div>
    </div>

    {{-- Footer Main --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
        {{-- About --}}
        <div class="col-span-2">
            <img src="{{ asset('assets/images/demos/demo-14/logo-footer.png') }}" alt="Logo" class="mb-4 w-32">
            <p class="text-sm text-gray-300 mb-4">
                At The One Diva Ltd, we provide top beauty services and premium products. From makeup to nails, explore our collection to enhance your style.
            </p>
            <p class="text-sm font-medium text-white">Call us 24/7: <a href="tel:+250780159059" class="text-pink-400 hover:underline">+250 780 159 059</a></p>
            <p class="text-sm mt-2 text-gray-400">Payment methods coming soon</p>
            <img src="{{ asset('assets/images/payments.png') }}" alt="Payments" class="mt-2 w-40">
        </div>

        {{-- Useful Links --}}
        <div>
            <h4 class="font-semibold mb-4 text-white">Useful Links</h4>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><a href="#" class="hover:text-pink-400">About</a></li>
                <li><a href="#" class="hover:text-pink-400">How to Shop</a></li>
                <li><a href="#" class="hover:text-pink-400">FAQ</a></li>
                <li><a href="#" class="hover:text-pink-400">Contact Us</a></li>
                <li><a href="#" class="hover:text-pink-400">Log in</a></li>
            </ul>
        </div>

        {{-- Services --}}
        <div>
            <h4 class="font-semibold mb-4 text-white">Services</h4>
            <ul class="space-y-2 text-sm text-gray-300">
                @foreach (['SPA', 'Hairstyle', 'Lashes', 'Nails', 'Barber Shop', 'Tattoo'] as $service)
                    <li><a href="{{ route('booking.create') }}" class="hover:text-pink-400">{{ $service }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- My Account --}}
        <div>
            <h4 class="font-semibold mb-4 text-white">My Account</h4>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><a href="#signin-modal" class="hover:text-pink-400">Sign in / Sign up</a></li>
                <li><a href="#" class="hover:text-pink-400">View Cart</a></li>
                <li><a href="#" class="hover:text-pink-400">My Wishlist</a></li>
                <li><a href="#" class="hover:text-pink-400">Track My Order</a></li>
                <li><a href="#" class="hover:text-pink-400">Help</a></li>
            </ul>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-gray-700 py-6 px-4 sm:px-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-400">
        <p>&copy; <span id="year">{{ date('Y') }}</span> Diva House Beauty. Designed by <strong class="text-white">Izere Moubarak</strong>.</p>
        <div class="flex gap-4 mt-4 md:mt-0">
            @foreach (['facebook' => 'facebook-f', 'twitter' => 'twitter', 'instagram' => 'instagram', 'youtube' => 'youtube', 'pinterest' => 'pinterest'] as $platform => $icon)
                <a href="#" class="hover:text-white" title="{{ ucfirst($platform) }}" target="_blank">
                    <i class="ph ph-{{ $icon }}"></i>
                </a>
            @endforeach
        </div>
    </div>
</footer>

<div x-data="{ open: false, tab: 'login' }" x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60" style="display: none;">
    <div class="bg-white w-full max-w-md mx-auto rounded-lg shadow-lg p-6 relative" @click.away="open = false">
        <button @click="open = false" class="absolute top-2 right-2 text-gray-500 hover:text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex justify-center mb-4">
            <button @click="tab = 'login'" :class="tab === 'login' ? 'bg-pink-600 text-white' : 'bg-gray-200'" class="w-1/2 py-2 rounded-l-lg">Sign In</button>
            <button @click="tab = 'register'" :class="tab === 'register' ? 'bg-pink-600 text-white' : 'bg-gray-200'" class="w-1/2 py-2 rounded-r-lg">Register</button>
        </div>

        {{-- Sign In Form --}}
        <div x-show="tab === 'login'">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-pink-300">
                </div>
                <div>
                    <label class="block text-sm font-medium">Password</label>
                    <input type="password" name="password" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-pink-300">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-gray-300">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="text-pink-600 hover:underline">Forgot password?</a>
                </div>
                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white py-2 rounded">Sign In</button>
            </form>
            <div class="text-center mt-4">
                <p class="text-gray-600">or continue with</p>
                <a href="{{ route('google-auth') }}" class="inline-flex mt-2 items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    <i class="ph ph-google-logo"></i> Google
                </a>
            </div>
        </div>

        {{-- Register Form --}}
        <div x-show="tab === 'register'">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input type="text" name="name" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-pink-300">
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-pink-300">
                </div>
                <div>
                    <label class="block text-sm font-medium">Password</label>
                    <input type="password" name="password" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-pink-300" id="register-password">
                    <small id="password-help" class="text-xs text-gray-500"></small>
                </div>
                <div>
                    <label class="block text-sm font-medium">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full border rounded px-3 py-2 focus:ring focus:ring-pink-300">
                </div>
                <label class="flex items-center text-sm gap-2">
                    <input type="checkbox" required class="rounded border-gray-300"> I agree to the <a href="#" class="text-pink-600 underline">privacy policy</a>
                </label>
                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white py-2 rounded">Sign Up</button>
            </form>
            <div class="text-center mt-4">
                <p class="text-gray-600">or continue with</p>
                <a href="{{ route('google-auth') }}" class="inline-flex mt-2 items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    <i class="ph ph-google-logo"></i> Google
                </a>
            </div>
        </div>
    </div>
</div>

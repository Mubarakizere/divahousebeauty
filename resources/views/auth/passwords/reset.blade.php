@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 py-20 min-h-[60vh]">
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-block">
            <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" alt="Diva House Beauty" width="105" height="25">
        </a>
    </div>
    
    <div class="bg-white shadow sm:rounded-lg border border-gray-100">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-100 bg-gray-50 rounded-t-lg">
            <h3 class="text-xl leading-6 font-serif text-gray-900">
                {{ __('Reset Password') }}
            </h3>
        </div>

        <div class="px-4 py-6 sm:p-8">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email Address') }}</label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                               class="appearance-none block w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-300 ring-red-200' : 'border-gray-300' }} rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm transition-colors">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                    <div class="mt-1">
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="appearance-none block w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-300 ring-red-200' : 'border-gray-300' }} rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm transition-colors">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password-confirm" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                    <div class="mt-1">
                        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm transition-colors">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors cursor-pointer">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

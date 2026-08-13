@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 py-20 min-h-[60vh]">
    <div class="bg-white shadow sm:rounded-lg border border-gray-100">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-100 bg-gray-50 rounded-t-lg">
            <h3 class="text-xl leading-6 font-serif text-gray-900">
                {{ __('Reset Password') }}
            </h3>
        </div>

        <div class="px-4 py-6 sm:p-8">
            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email Address') }}</label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               class="appearance-none block w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-300 ring-red-200' : 'border-gray-300' }} rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm transition-colors">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors cursor-pointer">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 py-20 min-h-[60vh]">
    <div class="bg-white shadow sm:rounded-lg border border-gray-100">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-100 bg-gray-50 rounded-t-lg">
            <h3 class="text-xl leading-6 font-serif text-gray-900">
                {{ __('Verify Your Email Address') }}
            </h3>
        </div>
        
        <div class="px-4 py-6 sm:p-8 text-gray-700">
            @if (session('resent'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ __('A fresh verification link has been sent to your email address.') }}</span>
                </div>
            @endif

            <p class="mb-4 text-base">
                {{ __('Before proceeding, please check your email for a verification link.') }}
            </p>
            <p class="text-base flex items-center flex-wrap gap-1">
                <span>{{ __('If you did not receive the email') }},</span>
                <form class="inline-block m-0 p-0" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="text-pink-600 hover:text-pink-700 hover:underline focus:outline-none font-medium bg-transparent border-0 p-0 inline align-baseline transition-colors cursor-pointer">
                        {{ __('click here to request another') }}
                    </button>.
                </form>
            </p>
        </div>
    </div>
</div>
@endsection

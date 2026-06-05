@extends('layouts.store')

@section('title', '404 - Page Not Found')

@section('subtitle', 'Oops! The page you are looking for does not exist.')

@section('content')
<div class="text-center py-12">
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 text-[var(--gold)] mb-6 shadow-sm">
        <i class="la la-search text-5xl"></i>
    </div>
    <h2 class="text-2xl font-serif text-[var(--black)] mb-4">We couldn't find what you were looking for.</h2>
    <p class="text-slate-500 mb-8 max-w-lg mx-auto">
        It seems we can't find the page you are looking for. It might have been removed, had its name changed, or is temporarily unavailable.
    </p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('home') }}" class="btn-gold px-8 py-3 uppercase text-xs font-bold tracking-wider rounded-md">
            Go to Homepage
        </a>
        <a href="{{ route('category') }}" class="px-8 py-3 border border-[var(--black)] text-[var(--black)] uppercase text-xs font-bold tracking-wider hover:bg-[var(--black)] hover:text-white transition-colors rounded-md">
            Continue Shopping
        </a>
    </div>
</div>
@endsection

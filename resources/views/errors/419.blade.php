@extends('layouts.store')

@section('title', '419 - Page Expired')

@section('subtitle', 'Your session has expired.')

@section('content')
<div class="text-center py-12">
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 text-[var(--gold)] mb-6 shadow-sm">
        <i class="la la-clock text-5xl"></i>
    </div>
    <h2 class="text-2xl font-serif text-[var(--black)] mb-4">Your session has expired due to inactivity.</h2>
    <p class="text-slate-500 mb-8 max-w-lg mx-auto">
        Please refresh the page or navigate back to the previous page and try your action again.
    </p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="window.history.back()" class="btn-gold px-8 py-3 uppercase text-xs font-bold tracking-wider rounded-md">
            Go Back
        </button>
        <a href="{{ route('home') }}" class="px-8 py-3 border border-[var(--black)] text-[var(--black)] uppercase text-xs font-bold tracking-wider hover:bg-[var(--black)] hover:text-white transition-colors rounded-md">
            Go to Homepage
        </a>
    </div>
</div>
@endsection

@extends('layouts.store')

@section('title', '500 - Server Error')

@section('subtitle', 'Oops! Something went wrong on our end.')

@section('content')
<div class="text-center py-12">
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-50 text-red-500 mb-6 shadow-sm">
        <i class="la la-exclamation-triangle text-5xl"></i>
    </div>
    <h2 class="text-2xl font-serif text-[var(--black)] mb-4">Internal Server Error</h2>
    <p class="text-slate-500 mb-8 max-w-lg mx-auto">
        We are experiencing an internal server problem. Please try again later. Our team has been notified and we are working to fix this immediately.
    </p>
    <a href="{{ route('home') }}" class="btn-gold px-8 py-3 uppercase text-xs font-bold tracking-wider rounded-md">
        Go to Homepage
    </a>
</div>
@endsection

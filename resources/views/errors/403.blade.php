@extends('layouts.store')

@section('title', '403 - Forbidden')

@section('subtitle', 'Access Denied')

@section('content')
<div class="text-center py-12">
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-orange-50 text-orange-500 mb-6 shadow-sm">
        <i class="la la-lock text-5xl"></i>
    </div>
    <h2 class="text-2xl font-serif text-[var(--black)] mb-4">You don't have permission to access this page.</h2>
    <p class="text-slate-500 mb-8 max-w-lg mx-auto">
        Please ensure you are logged into an account that has the required permissions, or contact support if you believe this is an error.
    </p>
    <a href="{{ route('home') }}" class="btn-gold px-8 py-3 uppercase text-xs font-bold tracking-wider rounded-md">
        Go to Homepage
    </a>
</div>
@endsection

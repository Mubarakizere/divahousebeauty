@extends('layouts.store')

@section('title', '503 - Service Unavailable')

@section('subtitle', 'Be right back.')

@section('content')
<div class="text-center py-12">
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 text-[var(--gold)] mb-6 shadow-sm">
        <i class="la la-tools text-5xl"></i>
    </div>
    <h2 class="text-2xl font-serif text-[var(--black)] mb-4">We are currently undergoing maintenance.</h2>
    <p class="text-slate-500 mb-8 max-w-lg mx-auto">
        Our site is temporarily down for scheduled maintenance or upgrades. Please check back shortly. Thank you for your patience!
    </p>
</div>
@endsection

@extends('layouts.store')

@section('title', 'My Dashboard')
@section('subtitle', 'Welcome back, ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-12">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Orders --}}
        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring flex items-center gap-5">
            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-[var(--gold)]">
                <i class="la la-shopping-bag text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Total Orders</p>
                <p class="text-2xl font-bold text-[var(--black)]">{{ $orders->count() }}</p>
            </div>
        </div>

        {{-- Total Spent --}}
        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring flex items-center gap-5">
            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-[var(--gold)]">
                <i class="la la-wallet text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Total Spent</p>
                <p class="text-2xl font-bold text-[var(--black)]">RWF {{ number_format($orders->sum('total'), 0) }}</p>
            </div>
        </div>

        {{-- Wishlist --}}
        <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring flex items-center gap-5">
            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-[var(--gold)]">
                <i class="la la-heart text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Wishlist</p>
                <p class="text-2xl font-bold text-[var(--black)]">{{ auth()->user()->wishlists()->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left: Recent Orders --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-xl font-bold text-[var(--black)]">Recent Orders</h3>
                <a href="{{ route('orders.index') }}" class="text-sm font-medium text-[var(--gold)] hover:text-[#B08D4C]">View All</a>
            </div>

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders->take(3) as $order)
                        <div class="bg-white border border-slate-200 rounded-lg p-5 shadow-ring hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-md font-bold text-[var(--black)]">Order #{{ $order->masked_order_id }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="badge {{ $order->status === 'pending_payment' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>

                            <div class="space-y-2 mb-4">
                                @foreach($order->items->take(2) as $item)
                                    <div class="flex justify-between text-sm text-slate-600">
                                        <span>{{ $item->product ? $item->product->name : 'Product' }} <span class="text-slate-400">× {{ $item->quantity }}</span></span>
                                        <span class="font-medium">RWF {{ number_format($item->price * $item->quantity, 0) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-3 border-t border-slate-50 flex justify-between items-center">
                                <span class="text-sm font-bold text-[var(--black)]">Total: RWF {{ number_format($order->total, 0) }}</span>
                                <a href="{{ route('orders.show', $order->id) }}" class="text-sm text-slate-500 hover:text-[var(--gold)]">Details &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg border border-slate-100">
                    <p class="text-slate-500 mb-4">You haven't placed any orders yet.</p>
                    <a href="{{ url('/') }}" class="btn-gold inline-flex items-center px-6 py-2 rounded text-sm font-medium">Start Shopping</a>
                </div>
            @endif
        </div>

        {{-- Right: Quick Actions & Profile --}}
        <div class="space-y-8">
            {{-- Quick Links --}}
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring">
                <h3 class="text-lg font-bold text-[var(--black)] mb-5">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('cart') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-50 transition-colors group">
                        <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-[var(--gold)] group-hover:text-white transition-colors">
                            <i class="la la-shopping-cart"></i>
                        </span>
                        <span class="text-sm font-medium text-slate-700">View Cart</span>
                    </a>
                    <a href="{{ route('address.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-50 transition-colors group">
                        <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-[var(--gold)] group-hover:text-white transition-colors">
                            <i class="la la-map-marker"></i>
                        </span>
                        <span class="text-sm font-medium text-slate-700">Manage Addresses</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-50 transition-colors group">
                        <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-[var(--gold)] group-hover:text-white transition-colors">
                            <i class="la la-user"></i>
                        </span>
                        <span class="text-sm font-medium text-slate-700">Edit Profile</span>
                    </a>
                </div>
            </div>

            {{-- Account Info --}}
            <div class="bg-slate-50 rounded-lg p-6 border border-slate-200">
                <h3 class="text-sm font-bold text-[var(--black)] mb-4 uppercase tracking-wider">My Account</h3>
                <div class="text-sm space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Name</span>
                        <span class="font-medium text-slate-700">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email</span>
                        <span class="font-medium text-slate-700">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Member Since</span>
                        <span class="font-medium text-slate-700">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    {{-- Welcome Header --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white shadow-card ring-1 ring-white/10">
        <div class="relative z-10 p-6 sm:p-8">
            <h1 class="text-2xl lg:text-3xl font-bold leading-tight tracking-tight text-white">
                Admin Dashboard
            </h1>
            <p class="mt-3 text-blue-100 text-base/relaxed max-w-xl">
                Manage your e-commerce platform, monitor sales, inventory, and customer orders.
            </p>
        </div>
    </section>

    {{-- KPI Cards --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Total Orders --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div class="p-3 rounded-xl bg-blue-500 text-white">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $ordersCount }}</span>
            </div>
            <p class="mt-3 text-sm text-gray-600">Total Orders</p>
        </div>

        {{-- Total Products --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div class="p-3 rounded-xl bg-green-500 text-white">
                    <i class="fa-solid fa-box text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $productsCount }}</span>
            </div>
            <p class="mt-3 text-sm text-gray-600">Total Products</p>
        </div>

        {{-- Total Users --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div class="p-3 rounded-xl bg-purple-500 text-white">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $usersCount }}</span>
            </div>
            <p class="mt-3 text-sm text-gray-600">Total Users</p>
        </div>

        {{-- Pending Orders --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div class="p-3 rounded-xl bg-yellow-500 text-white">
                    <i class="fa-solid fa-clock text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $pendingOrders }}</span>
            </div>
            <p class="mt-3 text-sm text-gray-600">Pending Orders</p>
        </div>
    </section>

    {{-- Quick Actions --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Manage Products --}}
        <a href="{{ route('admin.products.index') }}" class="group rounded-2xl bg-white border border-gray-100 p-6 shadow-card hover:shadow-xl transition">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-blue-500 text-white group-hover:scale-110 transition">
                    <i class="fa-solid fa-boxes-stacked text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                    <p class="mt-1 text-sm text-gray-600">Add, edit & manage products</p>
                    <p class="mt-2 text-xs text-blue-600 font-medium">{{ $productsCount }} products →</p>
                </div>
            </div>
        </a>

        {{-- Manage Orders --}}
        <a href="{{ route('admin.orders.index') }}" class="group rounded-2xl bg-white border border-gray-100 p-6 shadow-card hover:shadow-xl transition">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-green-500 text-white group-hover:scale-110 transition">
                    <i class="fa-solid fa-clipboard-list text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Orders</h3>
                    <p class="mt-1 text-sm text-gray-600">View & update order status</p>
                    <p class="mt-2 text-xs text-green-600 font-medium">{{ $ordersCount }} orders →</p>
                </div>
            </div>
        </a>

        {{-- Manage Categories --}}
        <a href="{{ route('admin.categories.index') }}" class="group rounded-2xl bg-white border border-gray-100 p-6 shadow-card hover:shadow-xl transition">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-purple-500 text-white group-hover:scale-110 transition">
                    <i class="fa-solid fa-tags text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Categories</h3>
                    <p class="mt-1 text-sm text-gray-600">Organize product catalog</p>
                    <p class="mt-2 text-xs text-purple-600 font-medium">Manage catalog →</p>
                </div>
            </div>
        </a>

        {{-- Manage Brands --}}
        <a href="{{ route('admin.brands.index') }}" class="group rounded-2xl bg-white border border-gray-100 p-6 shadow-card hover:shadow-xl transition">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-pink-500 text-white group-hover:scale-110 transition">
                    <i class="fa-solid fa-copyright text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Brands</h3>
                    <p class="mt-1 text-sm text-gray-600">Manage product brands</p>
                    <p class="mt-2 text-xs text-pink-600 font-medium">View brands →</p>
                </div>
            </div>
        </a>

        {{-- Manage Coupons --}}
        <a href="{{ route('admin.coupons.index') }}" class="group rounded-2xl bg-white border border-gray-100 p-6 shadow-card hover:shadow-xl transition">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-yellow-500 text-white group-hover:scale-110 transition">
                    <i class="fa-solid fa-ticket text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Coupons</h3>
                    <p class="mt-1 text-sm text-gray-600">Discount codes & promotions</p>
                    <p class="mt-2 text-xs text-yellow-600 font-medium">Manage discounts →</p>
                </div>
            </div>
        </a>

        {{-- Moderate Reviews --}}
        <a href="{{ route('admin.reviews.index') }}" class="group rounded-2xl bg-white border border-gray-100 p-6 shadow-card hover:shadow-xl transition">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl bg-amber-500 text-white group-hover:scale-110 transition">
                    <i class="fa-solid fa-star text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Reviews</h3>
                    <p class="mt-1 text-sm text-gray-600">Moderate customer reviews</p>
                    <p class="mt-2 text-xs text-amber-600 font-medium">View reviews →</p>
                </div>
            </div>
        </a>
    </section>

</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    {{-- ========================= --}}
    {{-- HERO / WELCOME --}}
    {{-- ========================= --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-600 via-amber-700 to-amber-800 text-white shadow-card ring-1 ring-white/10">
        {{-- soft orbs / decoration --}}
        <div class="pointer-events-none absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/10 blur-xl"></div>
        <div class="pointer-events-none absolute bottom-0 right-1/4 h-24 w-24 rounded-full bg-white/5 blur-2xl"></div>

        <div class="relative z-10 p-6 sm:p-8 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-start gap-3 flex-wrap">
                    <h1 class="text-2xl lg:text-3xl font-bold leading-tight tracking-tight text-white">
                        Welcome back, {{ $user->name }} 
                    </h1>

                    @if(auth()->user()->hasRole('admin'))
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-medium text-white ring-1 ring-white/30 backdrop-blur-sm">
                            <i class="fa-solid fa-shield-halved mr-1 text-xs"></i> Admin
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-1 text-[11px] font-medium text-white ring-1 ring-white/30 backdrop-blur-sm">
                            <i class="fa-solid fa-user-heart mr-1 text-xs"></i> Customer
                        </span>
                    @endif
                </div>

                <p class="mt-3 text-amber-100 text-base/relaxed max-w-xl">
                    @if(auth()->user()->hasRole('admin'))
                        Manage your business operations, monitor sales, and keep things running smoothly.
                    @else
                        Here's a snapshot of your beauty journey with us 
                    @endif
                </p>
            </div>

            <div class="shrink-0 w-full sm:w-auto">
                <div class="rounded-xl bg-white/15 backdrop-blur-md ring-1 ring-white/30 px-5 py-4 text-sm text-amber-50 flex flex-col min-w-[12rem]">
                    <span class="text-amber-100/80">Member since</span>
                    <span class="font-semibold text-white text-lg leading-tight">
                        {{ $user->created_at->format('M Y') }}
                    </span>
                    <span class="mt-3 inline-flex items-center text-[11px] font-medium text-white bg-black/20 ring-1 ring-white/20 rounded-full px-2 py-1 w-fit">
                        <i class="fa-solid fa-circle-check text-green-300 mr-1.5 text-[10px]"></i>
                        Account Active
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= --}}
    {{-- KPI / METRICS GRID --}}
    {{-- ========================= --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        @if(auth()->user()->hasRole('admin'))

            {{-- Total Orders --}}
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-blue-500 text-white shadow-inner shadow-blue-900/30 ring-1 ring-white/20">
                            <i class="fa-solid fa-bag-shopping text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $totalOrders ?? 0 }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-blue-600 bg-blue-50/80 border border-blue-100 rounded-full px-2 py-0.5 font-medium">
                        +75%
                    </span>
                </div>

                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>Fulfillment progress</span>
                        <span>75%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            {{-- Revenue --}}
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-green-500 text-white shadow-inner shadow-green-900/30 ring-1 ring-white/20">
                            <i class="fa-solid fa-coins text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Revenue (RWF)</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ number_format($totalRevenue ?? 0) }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-green-600 bg-green-50/80 border border-green-100 rounded-full px-2 py-0.5 font-medium">
                        +12% today
                    </span>
                </div>

                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>Target</span>
                        <span>85%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full" style="width: 85%"></div>
                    </div>
                </div>
            </div>

            {{-- Customers --}}
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-purple-500 text-white shadow-inner shadow-purple-900/30 ring-1 ring-white/20">
                            <i class="fa-solid fa-user-group text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Customers</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $totalCustomers ?? 0 }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-purple-600 bg-purple-50/80 border border-purple-100 rounded-full px-2 py-0.5 font-medium">
                        +24 new
                    </span>
                </div>

                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>Returning rate</span>
                        <span>60%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full" style="width: 60%"></div>
                    </div>
                </div>
            </div>

            {{-- Products --}}
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-yellow-500 text-white shadow-inner shadow-yellow-900/30 ring-1 ring-white/20">
                            <i class="fa-solid fa-boxes-stacked text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Products</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $totalProducts ?? 0 }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-yellow-700 bg-yellow-50 border border-yellow-100 rounded-full px-2 py-0.5 font-medium">
                        Stock OK
                    </span>
                </div>

                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>In stock</span>
                        <span>90%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-500 rounded-full" style="width: 90%"></div>
                    </div>
                </div>
            </div>

        @else
            {{-- CUSTOMER CARDS --}}

            {{-- Orders --}}
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-blue-500 text-white shadow-inner shadow-blue-900/30 ring-1 ring-white/20">
                            <i class="fa-solid fa-bag-shopping text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $orders->count() }}</p>
                        </div>
                    </div>
                </div>

                @php
                    $ordersProgress = min(($orders->count() / 10) * 100, 100);
                @endphp
                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>Progress to 10 orders</span>
                        <span>{{ number_format($ordersProgress, 0) }}%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ $ordersProgress }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Total Spent --}}
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-green-500 text-white shadow-inner shadow-green-900/30 ring-1 ring-white/20">
                            <i class="fa-solid fa-coins text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Total Spent (RWF)</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">
                                {{ number_format($orders->sum('total'), 0) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>Beauty investment 😌</span>
                        <span>75%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            {{-- Pending Payments --}}
            @php
                $pendingCount = $orders->where('status', 'pending_payment')->count();
            @endphp
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl {{ $pendingCount > 0 ? 'bg-yellow-500' : 'bg-green-500' }} text-white shadow-inner ring-1 ring-white/20">
                            @if($pendingCount > 0)
                                <i class="fa-solid fa-clock text-lg"></i>
                            @else
                                <i class="fa-solid fa-check text-lg"></i>
                            @endif
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Pending Payments</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $pendingCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>Status</span>
                        <span>{{ $pendingCount > 0 ? 'Action needed' : 'All paid' }}</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full {{ $pendingCount > 0 ? 'bg-yellow-500' : 'bg-green-500' }} rounded-full"
                             style="width: {{ $pendingCount > 0 ? '90' : '100' }}%">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bookings --}}
            <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-card transition hover:shadow-xl/50 hover:-translate-y-[1px] duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-purple-500 text-white shadow-inner shadow-purple-900/30 ring-1 ring-white/20">
                            <i class="fa-solid fa-spa text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-[13px] text-gray-500 font-medium">Bookings</p>
                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $bookings->count() ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex justify-between text-[11px] text-gray-500 font-medium mb-1.5">
                        <span>Loyalty vibes</span>
                        <span>65%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
            </div>

        @endif
    </section>

    {{-- ========================= --}}
    {{-- MAIN GRID (2/3 content + 1/3 sidebar) --}}
    {{-- ========================= --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- MAIN COLUMN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Admin: Recent Orders / Customer: My Orders --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-card overflow-hidden">
                <div class="flex items-start justify-between p-6 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fa-solid fa-bag-shopping text-blue-500 mr-2 text-base"></i>
                            @if(auth()->user()->hasRole('admin'))
                                Recent Orders
                            @else
                                My Orders
                            @endif
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            @if(auth()->user()->hasRole('admin'))
                                Latest activity across the store
                            @else
                                Your most recent purchases and payment status
                            @endif
                        </p>
                    </div>

                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.orders.index') }}"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium inline-flex items-center">
                            View All
                            <i class="fa-solid fa-chevron-right ml-1 text-[10px]"></i>
                        </a>
                    @else
                        @if($orders->count() > 3)
                            <a href="#"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium inline-flex items-center">
                                View All
                                <i class="fa-solid fa-chevron-right ml-1 text-[10px]"></i>
                            </a>
                        @endif
                    @endif
                </div>

                <div class="p-6">
                    @if(auth()->user()->hasRole('admin'))
                        @if(isset($recentOrders) && $recentOrders->count())
                            {{-- Admin table (recent orders) --}}
                            <div class="overflow-x-auto -mx-4 sm:mx-0">
                                <table class="min-w-full text-sm">
                                    <thead class="text-[11px] uppercase tracking-wide text-gray-500 font-medium bg-gray-50">
                                        <tr>
                                            <th class="text-left py-3 px-4">Order</th>
                                            <th class="text-left py-3 px-4">Customer</th>
                                            <th class="text-left py-3 px-4">Total</th>
                                            <th class="text-left py-3 px-4">Status</th>
                                            <th class="text-right py-3 px-4">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-gray-700">
                                        @foreach($recentOrders as $o)
                                        <tr class="hover:bg-gray-50/60">
                                            <td class="py-3 px-4 font-semibold text-gray-900">
                                                #{{ $o->masked_order_id ?? $o->id }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="font-medium text-gray-900">{{ $o->customer_name ?? '—' }}</div>
                                                <div class="text-[11px] text-gray-500">{{ $o->customer_email ?? '' }}</div>
                                            </td>
                                            <td class="py-3 px-4 font-semibold text-amber-600">
                                                RWF {{ number_format($o->total, 0) }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium
                                                    @class([
                                                        'bg-green-100 text-green-800' => $o->status === 'completed',
                                                        'bg-yellow-100 text-yellow-800' => $o->status === 'pending_payment',
                                                        'bg-blue-100 text-blue-800' => $o->status === 'confirmed',
                                                        'bg-red-100 text-red-800' => !in_array($o->status, ['completed','pending_payment','confirmed']),
                                                    ])">
                                                    {{ ucfirst(str_replace('_', ' ', $o->status)) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right text-[13px] text-gray-600 whitespace-nowrap">
                                                {{ $o->created_at->format('d M Y • H:i') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            {{-- Admin empty state --}}
                            <div class="text-center py-12 text-gray-500">
                                <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-400 shadow-inner">
                                    <i class="fa-solid fa-receipt text-xl"></i>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 mb-1">No recent orders</h3>
                                <p class="text-sm text-gray-600">Orders will show up here as soon as customers start checking out.</p>
                            </div>
                        @endif
                    @else
                        {{-- CUSTOMER RECENT ORDERS --}}
                        @if ($orders->count() > 0)
                            <div class="space-y-4">
                                @foreach ($orders->take(3) as $order)
                                <div class="rounded-xl bg-gray-50 ring-1 ring-gray-200/70 hover:bg-white hover:shadow-card hover:ring-gray-300 transition p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="min-w-0">
                                            <h3 class="font-semibold text-gray-900 text-sm sm:text-base">
                                                Order #{{ $order->masked_order_id ?? $order->id }}
                                            </h3>
                                            <p class="text-[12px] text-gray-500">
                                                {{ $order->created_at->format('d M Y • H:i') }}
                                            </p>
                                        </div>

                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium
                                            @class([
                                                'bg-green-100 text-green-800' => $order->status === 'completed',
                                                'bg-yellow-100 text-yellow-800' => $order->status === 'pending_payment',
                                                'bg-blue-100 text-blue-800' => $order->status === 'confirmed',
                                                'bg-red-100 text-red-800' => !in_array($order->status, ['completed','pending_payment','confirmed']),
                                            ])">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </div>

                                    {{-- Items --}}
                                    <div class="space-y-2 mb-4">
                                        @foreach ($order->items->take(2) as $item)
                                        <div class="flex justify-between items-start text-sm">
                                            <div class="min-w-0">
                                                <div class="font-medium text-gray-900 line-clamp-1">
                                                    {{ $item->product->name ?? 'Deleted Product' }}
                                                </div>
                                                <div class="text-[12px] text-gray-500 leading-tight">
                                                    Qty: {{ $item->quantity }} @ RWF {{ number_format($item->price, 0) }}
                                                </div>
                                            </div>
                                            <div class="text-right text-amber-600 font-semibold whitespace-nowrap text-sm">
                                                RWF {{ number_format($item->quantity * $item->price, 0) }}
                                            </div>
                                        </div>
                                        @endforeach

                                        @if($order->items->count() > 2)
                                        <p class="text-[11px] text-gray-500 text-center">
                                            + {{ $order->items->count() - 2 }} more item(s)
                                        </p>
                                        @endif
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-3 border-t border-gray-200">
                                        <span class="text-[13px] text-gray-600">
                                            Payment: <span class="font-medium text-gray-800">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                                        </span>

                                        <span class="text-base font-bold text-amber-600 tracking-tight">
                                            RWF {{ number_format($order->total, 0) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400 shadow-inner">
                                    <i class="fa-solid fa-bag-shopping text-2xl"></i>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 mb-1">No Orders Yet</h3>
                                <p class="text-sm text-gray-600 mb-6">
                                    You haven't placed any orders yet. Start shopping to see your orders here!
                                </p>
                                <a href="{{ route('category') }}"
                                   class="inline-flex items-center rounded-lg bg-amber-600 px-4 py-2 text-white text-sm font-medium shadow-card hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                    <i class="fa-solid fa-cart-shopping text-xs mr-2"></i>
                                    Start Shopping
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="space-y-6">

            {{-- QUICK ACTIONS --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-card overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fa-solid fa-bolt text-yellow-500 mr-2"></i>
                        Quick Actions
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Jump straight into common tasks
                    </p>
                </div>

                <div class="p-6">
                    <div class="space-y-3 text-sm">

                        @if(auth()->user()->hasRole('admin'))

                            {{-- Manage Products --}}
                            <a href="{{ route('admin.products.index') }}"
                               class="group flex items-start rounded-xl border border-gray-200/70 bg-gray-50 p-3 hover:bg-white hover:shadow-card hover:border-gray-300 transition">
                                <div class="p-2 rounded-lg bg-blue-500 text-white shadow-inner shadow-blue-900/30 ring-1 ring-white/20 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-box-open text-sm"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900 leading-tight">Manage Products</div>
                                    <div class="text-[12px] text-gray-600 leading-tight">Add, edit, or remove products</div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs group-hover:text-gray-600"></i>
                            </a>

                            {{-- Manage Orders --}}
                            <a href="{{ route('admin.orders.index') }}"
                               class="group flex items-start rounded-xl border border-gray-200/70 bg-gray-50 p-3 hover:bg-white hover:shadow-card hover:border-gray-300 transition">
                                <div class="p-2 rounded-lg bg-green-500 text-white shadow-inner shadow-green-900/30 ring-1 ring-white/20 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-clipboard-list text-sm"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900 leading-tight">Manage Orders</div>
                                    <div class="text-[12px] text-gray-600 leading-tight">View and update order status</div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs group-hover:text-gray-600"></i>
                            </a>

                            {{-- Manage Categories --}}
                            <a href="{{ route('admin.categories.index') }}"
                               class="group flex items-start rounded-xl border border-gray-200/70 bg-gray-50 p-3 hover:bg-white hover:shadow-card hover:border-gray-300 transition">
                                <div class="p-2 rounded-lg bg-purple-500 text-white shadow-inner shadow-purple-900/30 ring-1 ring-white/20 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-tags text-sm"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900 leading-tight">Manage Categories</div>
                                    <div class="text-[12px] text-gray-600 leading-tight">Organize product catalog</div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs group-hover:text-gray-600"></i>
                            </a>

                        @else
                            {{-- Browse Products --}}
                            <a href="{{ route('category') }}"
                               class="group flex items-start rounded-xl border border-gray-200/70 bg-gray-50 p-3 hover:bg-white hover:shadow-card hover:border-gray-300 transition">
                                <div class="p-2 rounded-lg bg-blue-500 text-white shadow-inner shadow-blue-900/30 ring-1 ring-white/20 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900 leading-tight">Browse Products</div>
                                    <div class="text-[12px] text-gray-600 leading-tight">Discover new beauty items</div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs group-hover:text-gray-600"></i>
                            </a>

                            {{-- My Bookings --}}
                            <a href="{{ route('booking.index') }}"
                               class="group flex items-start rounded-xl border border-gray-200/70 bg-gray-50 p-3 hover:bg-white hover:shadow-card hover:border-gray-300 transition">
                                <div class="p-2 rounded-lg bg-green-500 text-white shadow-inner shadow-green-900/30 ring-1 ring-white/20 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-calendar-check text-sm"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900 leading-tight">My Bookings</div>
                                    <div class="text-[12px] text-gray-600 leading-tight">View appointment history</div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs group-hover:text-gray-600"></i>
                            </a>

                            {{-- My Addresses --}}
                            <a href="{{ route('address.index') }}"
                               class="group flex items-start rounded-xl border border-gray-200/70 bg-gray-50 p-3 hover:bg-white hover:shadow-card hover:border-gray-300 transition">
                                <div class="p-2 rounded-lg bg-cyan-500 text-white shadow-inner shadow-cyan-900/30 ring-1 ring-white/20 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-location-dot text-sm"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900 leading-tight">My Addresses</div>
                                    <div class="text-[12px] text-gray-600 leading-tight">Manage delivery locations</div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs group-hover:text-gray-600"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ACCOUNT INFO --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-card overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fa-solid fa-user-circle text-cyan-500 mr-2"></i>
                        Account Info
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Your profile details</p>
                </div>

                <div class="p-6 text-sm">
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="text-gray-600">Email</span>
                            <span class="font-medium text-gray-900 text-right break-all">{{ $user->email }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Member Since</span>
                            <span class="font-medium text-gray-900">{{ $user->created_at->format('F Y') }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Account Status</span>
                            <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-2 py-0.5 text-[11px] font-medium">
                                <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i>
                                Active
                            </span>
                        </div>

                        @if(auth()->user()->hasRole('admin'))
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Role</span>
                            <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-800 px-2 py-0.5 text-[11px] font-medium">
                                <i class="fa-solid fa-shield-halved mr-1 text-[10px]"></i>
                                Administrator
                            </span>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <a href="#"
                           class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-[13px] font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:shadow-card transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                            <i class="fa-solid fa-pen-to-square text-[12px] mr-2"></i>
                            Update Profile
                        </a>
                    </div>
                </div>
            </div>

            {{-- TODAY OVERVIEW (ADMIN ONLY) --}}
            @if(auth()->user()->hasRole('admin'))
            <div class="rounded-2xl bg-white border border-gray-100 shadow-card overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fa-solid fa-chart-simple text-indigo-500 mr-2"></i>
                        Today's Overview
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Snapshot for {{ now()->format('d M Y') }}
                    </p>
                </div>

                <div class="p-6 text-sm">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">New Orders</span>
                            <span class="font-semibold text-blue-600 text-base leading-none">
                                {{ $todayOrders ?? 0 }}
                            </span>
                        </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Today's Revenue</span>
                        <span class="font-semibold text-green-600 text-base leading-none">
                            RWF {{ number_format($todayRevenue ?? 0) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">New Customers</span>
                        <span class="font-semibold text-purple-600 text-base leading-none">
                            {{ $todayCustomers ?? 0 }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pending Orders</span>
                        <span class="font-semibold text-yellow-600 text-base leading-none">
                            {{ $pendingOrders ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>

</div>
@endsection

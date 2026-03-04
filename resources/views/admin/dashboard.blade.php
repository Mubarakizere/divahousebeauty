@extends('layouts.dashboard')

@section('title', 'Overview')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes pulseSoft {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
        .animate-fade-up { animation: fadeInUp 0.5s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05);
            border-color: rgba(203, 213, 225, 1);
        }
        .stat-icon {
            transition: all 0.3s ease;
        }
        .glass-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }
    </style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">Welcome back, here's what's happening today.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-700 border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors">
                <i class="fas fa-shopping-bag text-xs text-blue-600"></i> View Orders
            </a>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-white bg-gray-900 rounded-md px-3 py-1.5 hover:bg-gray-700 transition-colors">
                <i class="fas fa-plus text-xs"></i> Add Product
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="glass-card rounded-xl p-5 relative overflow-hidden group animate-scale-in stagger-1">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50/50 rounded-full blur-2xl group-hover:bg-blue-100/50 transition-colors"></div>
            <div class="flex items-center justify-between mb-3 relative">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Revenue</p>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center stat-icon">
                    <i class="fas fa-wallet text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2 relative">
                <p class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalRevenue) }}</p>
                <p class="text-xs text-slate-400 font-bold uppercase">RWF</p>
            </div>
            <div class="mt-4 flex items-center gap-2 relative">
                @if($todayRevenue > 0)
                    <span class="flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        <i class="fas fa-arrow-up text-[9px]"></i> {{ number_format($todayRevenue) }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium">New today</span>
                @else
                    <span class="text-[10px] text-slate-400 font-medium italic">No new revenue today</span>
                @endif
            </div>
        </div>

        <div class="glass-card rounded-xl p-5 relative overflow-hidden group animate-scale-in stagger-2">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50/50 rounded-full blur-2xl group-hover:bg-indigo-100/50 transition-colors"></div>
            <div class="flex items-center justify-between mb-3 relative">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Orders</p>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center stat-icon relative" style="{{ $pendingOrders > 0 ? 'animation: pulseSoft 2s infinite;' : '' }}">
                    <i class="fas fa-shopping-cart text-indigo-600"></i>
                    @if($pendingOrders > 0)
                        <span class="absolute top-0 right-0 h-2.5 w-2.5 rounded-full bg-indigo-500 border-2 border-white"></span>
                    @endif
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 tracking-tight relative">{{ number_format($totalOrders) }}</p>
            <div class="mt-4 flex items-center gap-3 relative">
                @if($todayOrders > 0)
                    <span class="text-[11px] font-bold text-indigo-600">+{{ $todayOrders }} today</span>
                @endif
                <span class="text-[11px] font-bold text-slate-400">
                    <span class="text-indigo-500">{{ $pendingOrders }}</span> pending
                </span>
            </div>
        </div>

        <div class="glass-card rounded-xl p-5 relative overflow-hidden group animate-scale-in stagger-3">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50/50 rounded-full blur-2xl group-hover:bg-emerald-100/50 transition-colors"></div>
            <div class="flex items-center justify-between mb-3 relative">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Customers</p>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center stat-icon">
                    <i class="fas fa-users text-emerald-600"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 tracking-tight relative">{{ number_format($totalCustomers) }}</p>
            <div class="mt-4 relative">
                @if($newCustomersToday > 0)
                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                        +{{ $newCustomersToday }} new signups
                    </span>
                @else
                    <span class="text-[10px] text-slate-400 font-medium italic">No new signups today</span>
                @endif
            </div>
        </div>

        <div class="glass-card rounded-xl p-5 relative overflow-hidden group animate-scale-in stagger-4">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50/50 rounded-full blur-2xl group-hover:bg-rose-100/50 transition-colors"></div>
            <div class="flex items-center justify-between mb-3 relative">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Products</p>
                <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center stat-icon">
                    <i class="fas fa-box text-rose-600"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 tracking-tight relative">{{ number_format($totalProducts) }}</p>
            <div class="mt-4 relative">
                <a href="{{ route('admin.products.index') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 group/link transition-colors">
                    Manage catalog 
                    <i class="fas fa-chevron-right text-[8px] group-hover/link:translate-x-0.5 transition-transform"></i>
                </a>
            </div>
        </div>

    </div>

    {{-- Low Stock Alert --}}
    @if($lowStockCount > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-4">
        <div class="h-10 w-10 bg-amber-100 rounded-full flex items-center justify-center shrink-0 border border-amber-200">
            <i class="fas fa-exclamation-triangle text-amber-600"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-amber-900 leading-tight">Attention: Low Stock Items</h3>
            <p class="text-xs text-amber-700 mt-1">There are <span class="font-bold">{{ $lowStockCount }}</span> products currently below the stock threshold. Consider restocking soon to avoid order delays.</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($lowStockProducts->take(3) as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center gap-1.5 px-2 py-1 bg-white border border-amber-200 rounded text-[10px] font-medium text-amber-800 hover:bg-amber-50 transition-colors">
                        {{ $product->name }} ({{ $product->stock }})
                    </a>
                @endforeach
                @if($lowStockCount > 3)
                    <span class="text-[10px] text-amber-600 self-center">and {{ $lowStockCount - 3 }} more...</span>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.products.index') }}?stock=low" class="text-xs font-medium text-amber-800 hover:underline shrink-0">View All</a>
    </div>
    @endif

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-medium text-gray-900 border-l-2 border-gray-900 pl-3">Revenue Overview</h2>
                <div class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded border border-gray-200">Current Year</div>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="revenueChart"></canvas>
                <div id="revenueLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10 hidden">
                    <svg class="animate-spin h-6 w-6 text-gray-400" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Order Status Chart --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="text-sm font-medium text-gray-900 mb-4 border-l-2 border-gray-900 pl-3">Orders by Status</h2>
            <div class="relative h-48 w-full flex justify-center mt-6">
                <canvas id="statusChart"></canvas>
                <div id="statusLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10 hidden">
                    <svg class="animate-spin h-6 w-6 text-gray-400" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            
            <div id="statusLegend" class="mt-6 flex flex-col gap-2"></div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-sm font-medium text-gray-900">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View all activity</a>
            </div>
            
            @if($recentOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-white">
                                <th class="text-left py-3 px-5 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">Order ID</th>
                                <th class="text-left py-3 px-5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                                <th class="text-left py-3 px-5 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th class="text-right py-3 px-5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-slate-50/80 transition-all duration-300 group border-transparent hover:border-slate-200 border-l-4">
                                    <td class="py-4 px-5">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-gray-900 block group-hover:text-blue-600">
                                            #{{ $order->order_number ?? $order->id }}
                                            <span class="block text-[10px] text-gray-400 font-normal uppercase tracking-wider mt-0.5">{{ $order->created_at->format('M j, Y') }}</span>
                                        </a>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-semibold text-slate-600 ring-2 ring-white">
                                                {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}{{ strtoupper(substr(explode(' ', $order->user->name ?? ' ')[min(1, count(explode(' ', $order->user->name ?? ' '))-1)], 0, 1)) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-gray-900 font-medium leading-tight">{{ $order->user->name ?? 'Guest Customer' }}</span>
                                                <span class="text-xs text-gray-500">{{ $order->user->email ?? 'no-email@example.com' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        @php
                                            $statusColors = [
                                                'pending_payment' => 'bg-amber-100/50 text-amber-700 ring-amber-600/10',
                                                'processing' => 'bg-indigo-100/50 text-indigo-700 ring-indigo-600/10',
                                                'shipped' => 'bg-purple-100/50 text-purple-700 ring-purple-600/10',
                                                'out_for_delivery' => 'bg-orange-100/50 text-orange-700 ring-orange-600/10',
                                                'completed' => 'bg-emerald-100/50 text-emerald-700 ring-emerald-600/10',
                                                'cancelled' => 'bg-rose-100/50 text-rose-700 ring-rose-600/10',
                                                'refunded' => 'bg-slate-100/50 text-slate-700 ring-slate-600/10',
                                            ];
                                            $statusClass = $statusColors[$order->status] ?? 'bg-gray-100/50 text-gray-700 ring-gray-600/10';
                                            $statusLabel = str_replace('_', ' ', $order->status);
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold ring-1 ring-inset {{ $statusClass }} uppercase tracking-widest whitespace-nowrap">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right font-bold text-gray-900 whitespace-nowrap">
                                        {{ number_format($order->total) }} <span class="text-[10px] text-gray-400 font-normal">RWF</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                    <div class="h-12 w-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-4">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">No orders yet</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-[200px] mx-auto">When customers start ordering, they will appear here.</p>
                </div>
            @endif
        </div>

        {{-- Top Selling Products --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-sm font-medium text-gray-900">Top Performing Products</h2>
            </div>
            <div class="flex-1">
                @if($topProducts->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($topProducts as $product)
                            <div class="p-4 flex items-center gap-4 hover:bg-gray-50/50 transition-colors cursor-default">
                                <div class="h-12 w-12 rounded bg-gray-50 flex-shrink-0 border border-gray-100 overflow-hidden">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="h-full w-full object-cover" alt="">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-image text-lg"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-semibold text-gray-900 truncate leading-tight">{{ $product->name }}</h4>
                                    <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider font-medium">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-xs font-bold text-gray-900">{{ number_format($product->total_sold) }}</div>
                                    <div class="text-[9px] text-emerald-600 font-bold uppercase tracking-tighter">Units Sold</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center p-8 text-center">
                        <i class="fas fa-box-open text-gray-200 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-400">No sales data available</p>
                    </div>
                @endif
            </div>
            @if($topProducts->count() > 0)
                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    <a href="{{ route('admin.products.index') }}" class="text-[11px] font-semibold text-gray-600 hover:text-gray-900 flex items-center justify-center gap-2">
                        View Product Catalog <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Configuration for standard look
        Chart.defaults.font.family = "'Inter', 'system-ui', 'sans-serif'";
        Chart.defaults.color = '#64748b'; // slate-500
        Chart.defaults.scale.grid.color = '#f1f5f9'; // slate-100
        
        // --- REVENUE CHART ---
        const revCtx = document.getElementById('revenueChart');
        if (revCtx) {
            document.getElementById('revenueLoading').classList.remove('hidden');
            
            fetch('{{ route('admin.api.monthly-revenue') }}')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('revenueLoading').classList.add('hidden');
                    
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    
                    // Initialize empty data array for all 12 months
                    const revenueData = Array(12).fill(0);
                    
                    // Fill with real data
                    data.forEach(item => {
                        // month from db is 1-12, array is 0-11
                        const idx = parseInt(item.month) - 1;
                        if(idx >= 0 && idx < 12) {
                            revenueData[idx] = parseFloat(item.revenue);
                        }
                    });
                    
                    // Add a slight gradient fill
                    const ctx = revCtx.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(15, 23, 42, 0.1)'); // slate-900 at 10%
                    gradient.addColorStop(1, 'rgba(15, 23, 42, 0)');
                    
                    new Chart(revCtx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Revenue (RWF)',
                                data: revenueData,
                                borderColor: '#3b82f6', // blue-500
                                backgroundColor: gradient,
                                borderWidth: 3,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#3b82f6',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: '#3b82f6',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 10,
                                    titleFont: { size: 13 },
                                    bodyFont: { size: 14, weight: 'bold' },
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return new Intl.NumberFormat('en-US').format(context.parsed.y) + ' RWF';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    border: { display: false },
                                    ticks: {
                                        maxTicksLimit: 6,
                                        callback: function(value) {
                                            if (value >= 1000000) return (value / 1000000) + 'M';
                                            if (value >= 1000) return (value / 1000) + 'k';
                                            return value;
                                        }
                                    }
                                },
                                x: {
                                    border: { display: false },
                                    grid: { display: false }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                        }
                    });
                })
                .catch(err => {
                    console.error('Failed to load revenue data:', err);
                    document.getElementById('revenueLoading').innerHTML = '<span class="text-xs text-red-500">Failed to load data</span>';
                });
        }
        
        // --- STATUS CHART ---
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            document.getElementById('statusLoading').classList.remove('hidden');
            
            fetch('{{ route('admin.api.order-status') }}')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('statusLoading').classList.add('hidden');
                    
                    if (data.length === 0) {
                        statusCtx.parentElement.innerHTML = '<div class="flex h-full items-center justify-center text-sm text-gray-400">No order data yet</div>';
                        return;
                    }

                    // Vivid Colors mapping
                    const colorMap = {
                        'pending_payment': '#f59e0b', // amber-500
                        'processing': '#6366f1', // indigo-500
                        'shipped': '#8b5cf6', // violet-500
                        'out_for_delivery': '#f97316', // orange-500
                        'completed': '#10b981', // emerald-500
                        'cancelled': '#f43f5e', // rose-500
                        'refunded': '#94a3b8', // slate-400
                    };
                    
                    const labels = [];
                    const counts = [];
                    const colors = [];
                    
                    // Build custom legend HTML
                    let legendHtml = '';
                    
                    const total = data.reduce((sum, item) => sum + parseInt(item.count), 0);
                    
                    // Sort descending by count
                    data.sort((a,b) => b.count - a.count);
                    
                    data.forEach(item => {
                        const label = item.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        const color = colorMap[item.status] || '#cbd5e1';
                        
                        labels.push(label);
                        counts.push(item.count);
                        colors.push(color);
                        
                        const percent = Math.round((item.count / total) * 100) || '<1';
                        
                        legendHtml += `
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: ${color}"></span>
                                    <span class="text-gray-600">${label}</span>
                                </div>
                                <div class="flex gap-4">
                                    <span class="text-gray-900 font-medium">${item.count}</span>
                                    <span class="text-gray-400 w-8 text-right">${percent}%</span>
                                </div>
                            </div>
                        `;
                    });
                    
                    document.getElementById('statusLegend').innerHTML = legendHtml;
                    
                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: counts,
                                backgroundColor: colors,
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 10,
                                    bodyFont: { size: 13, weight: 'bold' },
                                    displayColors: true,
                                    boxPadding: 4,
                                    usePointStyle: true,
                                    callbacks: {
                                        label: function(context) {
                                            return ` ${context.parsed} orders`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(err => {
                    console.error('Failed to load status data:', err);
                    document.getElementById('statusLoading').innerHTML = '<span class="text-xs text-red-500">Failed to load data</span>';
                });
        }
    });
</script>
@endpush

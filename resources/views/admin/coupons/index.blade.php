@extends('layouts.dashboard')

@section('title', 'Coupons')

@push('head')
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes ticketEntrance {
            from { opacity: 0; transform: scale(0.9) rotate(-1deg); }
            to { opacity: 1; transform: scale(1) rotate(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.5s ease-out forwards; }
        .coupon-ticket {
            animation: ticketEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .coupon-ticket:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        /* Perforated edge effect */
        .ticket-edge {
            background-image: radial-gradient(circle at 0px 10px, transparent 0, transparent 10px, #ffffff 10px);
            background-size: 100% 20px;
            width: 15px;
            height: 100%;
            position: absolute;
            left: -8px;
            top: 0;
            z-index: 10;
        }
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
        .stagger-5 { animation-delay: 0.5s; }
    </style>
@endpush
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fade-up">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                <i class="fas fa-ticket-alt text-purple-600 mr-2"></i>
                Coupons & Discounts
            </h1>
            <p class="mt-1 text-slate-500 font-medium italic">
                Strategic promotion tools for Diva House Beauty
            </p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-purple-200 hover:shadow-purple-300 hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
            <i class="fas fa-plus-circle mr-2"></i>
            Launch New Coupon
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($coupons->count() > 0)
        <!-- Coupons Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($coupons as $index => $coupon)
                <div class="coupon-ticket rounded-2xl border border-slate-200/60 overflow-hidden stagger-{{ ($index % 5) + 1 }}">
                    <div class="ticket-edge"></div>
                    <div class="p-6 md:p-8">
                        <!-- Coupon Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-2xl font-black text-slate-900 font-mono tracking-widest uppercase bg-slate-50 px-3 py-1 rounded-lg border border-dashed border-slate-300">
                                        {{ $coupon->code }}
                                    </h3>
                                    @if($coupon->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-tighter">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                            Active Now
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-tighter">
                                            <i class="fas fa-pause-circle mr-2 text-[12px]"></i>
                                            Draft / Paused
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Discount Value -->
                                <div class="mt-4">
                                    @if($coupon->type === 'percentage')
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-br from-purple-600 to-indigo-700 tracking-tighter">{{ (int)$coupon->value }}%</span>
                                            <span class="text-sm font-bold text-slate-500 uppercase">Discount</span>
                                        </div>
                                    @elseif($coupon->type === 'fixed')
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-br from-purple-600 to-indigo-700 tracking-tighter">{{ number_format($coupon->value, 0) }}</span>
                                            <span class="text-sm font-bold text-slate-500 uppercase">RWF OFF</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3 text-purple-600">
                                            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center">
                                                <i class="fas fa-shipping-fast text-xl"></i>
                                            </div>
                                            <span class="text-2xl font-black italic tracking-tight uppercase">Free Shipping</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-2 opacity-60 hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                   class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-all group/edit"
                                   title="Edit Details">
                                    <i class="fas fa-pencil-alt group-hover/edit:rotate-12 transition-transform"></i>
                                </a>
                                <button type="button"
                                        @click="$dispatch('open-delete-coupon-modal', { couponId: {{ $coupon->id }}, couponCode: '{{ $coupon->code }}' })"
                                        class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-100 hover:bg-rose-50 shadow-sm transition-all"
                                        title="Delete Permanently">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Details Section (Vibrant List) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-6 border-y border-dashed border-slate-100">
                            <!-- Min Order -->
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500">
                                    <i class="fas fa-shopping-basket text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400 leading-none">Min Order</p>
                                    <p class="text-xs font-black text-slate-700 mt-0.5">
                                        @if($coupon->min_order_amount)
                                            {{ number_format($coupon->min_order_amount, 0) }} RWF
                                        @else
                                            No Minimum
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Validity -->
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500">
                                    <i class="fas fa-clock text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400 leading-none">Validity</p>
                                    <p class="text-xs font-black text-slate-700 mt-0.5">
                                        @if($coupon->expires_at)
                                            Ends {{ $coupon->expires_at->format('M d') }}
                                        @else
                                            Indefinite
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Usage count / Limit -->
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500">
                                    <i class="fas fa-users text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400 leading-none">Redemptions</p>
                                    <p class="text-xs font-black text-slate-700 mt-0.5">
                                        {{ $coupon->usage_count ?? 0 }} Used
                                    </p>
                                </div>
                            </div>

                            <!-- Limit -->
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-400">
                                    <i class="fas fa-shield-alt text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400 leading-none">Cap / Limit</p>
                                    <p class="text-xs font-black text-slate-700 mt-0.5">
                                        @if($coupon->usage_limit)
                                            Limit: {{ $coupon->usage_limit }}
                                        @else
                                            Unlimited
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar (Subtle) -->
                        @if($coupon->usage_limit)
                            @php
                                $usagePercent = min(100, (($coupon->usage_count ?? 0) / $coupon->usage_limit) * 100);
                                $progressColor = $usagePercent > 90 ? 'bg-rose-500' : ($usagePercent > 70 ? 'bg-amber-500' : 'bg-purple-600');
                            @endphp
                            <div class="mt-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Budget Utilization</span>
                                    <span class="text-[10px] font-black text-slate-800">{{ round($usagePercent) }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="{{ $progressColor }} h-full rounded-full transition-all duration-1000 shadow-sm" 
                                         style="width: {{ $usagePercent }}%; box-shadow: 0 0 8px {{ str_replace('bg-', '', $progressColor) }}44"></div>
                                </div>
                            </div>
                        @else
                             <!-- Description placeholder to keep height consistent -->
                             @if($coupon->description)
                                <div class="mt-6 line-clamp-2 text-xs text-slate-400 font-medium italic">
                                    "{{ $coupon->description }}"
                                </div>
                             @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $coupons->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-100 mb-4">
                <i class="fas fa-ticket-alt text-3xl text-purple-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Coupons Yet</h3>
            <p class="text-gray-600 mb-6">Create your first discount coupon to attract customers!</p>
            <a href="{{ route('admin.coupons.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-200 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Your First Coupon
            </a>
        </div>
    @endif
</div>

<!-- Delete Coupon Modal -->
@include('components.delete-coupon-modal')
@endsection


@extends('layouts.dashboard')

@section('title', 'Coupons')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-ticket-alt text-purple-600 mr-2"></i>
                Coupons
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Manage discount coupons for your store
            </p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-200 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create Coupon
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($coupons as $coupon)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
                    <div class="p-6">
                        <!-- Coupon Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-xl font-bold text-gray-900 font-mono">
                                        {{ $coupon->code }}
                                    </h3>
                                    @if($coupon->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Discount Value -->
                                <div class="mt-2">
                                    @if($coupon->type === 'percentage')
                                        <span class="text-3xl font-bold text-purple-600">{{ $coupon->value }}%</span>
                                        <span class="text-sm text-gray-600 ml-2">off</span>
                                    @elseif($coupon->type === 'fixed')
                                        <span class="text-3xl font-bold text-purple-600">RWF {{ number_format($coupon->value, 0) }}</span>
                                        <span class="text-sm text-gray-600 ml-2">off</span>
                                    @else
                                        <span class="text-2xl font-bold text-purple-600">
                                            <i class="fas fa-shipping-fast mr-2"></i>
                                            Free Shipping
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                   class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        @click="$dispatch('open-delete-coupon-modal', { couponId: {{ $coupon->id }}, couponCode: '{{ $coupon->code }}' })"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Coupon Details -->
                        <div class="space-y-3 pt-4 border-t border-gray-200">
                            <!-- Min Order Amount -->
                            @if($coupon->min_order_amount)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-shopping-cart w-5 mr-2"></i>
                                    <span>Min order: <strong>RWF {{ number_format($coupon->min_order_amount, 0) }}</strong></span>
                                </div>
                            @endif

                            <!-- Max Discount -->
                            @if($coupon->max_discount && $coupon->type === 'percentage')
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-tag w-5 mr-2"></i>
                                    <span>Max discount: <strong>RWF {{ number_format($coupon->max_discount, 0) }}</strong></span>
                                </div>
                            @endif

                            <!-- Usage Stats -->
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-chart-line w-5 mr-2"></i>
                                <span>
                                    Used <strong>{{ $coupon->usage_count ?? 0 }}</strong> 
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }} times
                                    @else
                                        times (unlimited)
                                    @endif
                                </span>
                            </div>

                            <!-- Valid Dates -->
                            @if($coupon->starts_at || $coupon->expires_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar w-5 mr-2"></i>
                                    <span>
                                        @if($coupon->starts_at && $coupon->expires_at)
                                            {{ $coupon->starts_at->format('M d') }} - {{ $coupon->expires_at->format('M d, Y') }}
                                        @elseif($coupon->starts_at)
                                            From {{ $coupon->starts_at->format('M d, Y') }}
                                        @else
                                            Until {{ $coupon->expires_at->format('M d, Y') }}
                                        @endif
                                    </span>
                                </div>
                            @endif

                            <!-- Description -->
                            @if($coupon->description)
                                <div class="flex items-start text-sm text-gray-600">
                                    <i class="fas fa-info-circle w-5 mr-2 mt-0.5"></i>
                                    <span class="flex-1">{{ $coupon->description }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Usage Progress Bar -->
                        @if($coupon->usage_limit)
                            <div class="mt-4">
                                @php
                                    $usagePercent = min(100, (($coupon->usage_count ?? 0) / $coupon->usage_limit) * 100);
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all" 
                                         style="width: {{ $usagePercent }}%"></div>
                                </div>
                            </div>
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


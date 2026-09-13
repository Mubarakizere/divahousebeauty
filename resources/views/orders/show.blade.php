@extends('layouts.store')

@section('title', 'Order #' . $order->masked_order_id)
@section('subtitle', 'Placed on ' . $order->created_at->format('F d, Y \a\t g:i A'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-[var(--gold)] transition-colors">
            <i class="la la-arrow-left mr-2"></i>
            Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Details --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Order Status / Timeline --}}
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring">
                <h3 class="text-lg font-bold text-[var(--black)] mb-6">Order Status</h3>
                
                <div class="relative pl-4 border-l bg-gradient-to-b from-slate-200 to-transparent">
                    @php
                        $statuses = [
                            'pending_payment' => ['icon' => 'clock', 'label' => 'Order Placed'],
                            'processing' => ['icon' => 'cog', 'label' => 'Processing'],
                            'shipped' => ['icon' => 'truck', 'label' => 'Shipped'],
                            'delivered' => ['icon' => 'check-circle', 'label' => 'Delivered'],
                        ];
                        if($order->status === 'cancelled') {
                             $statuses = ['cancelled' => ['icon' => 'times-circle', 'label' => 'Cancelled']];
                        }
                        
                        $currentFound = false;
                    @endphp

                    @foreach($statuses as $key => $info)
                        @php
                           $isActive = ($key === $order->status);
                           if($isActive) $currentFound = true;
                           $isPast = !$currentFound && $key !== $order->status; // simplistic approach, usually logic is complex
                           // Better simple logic:
                           // Just show the current status as active, others distinct
                        @endphp
                        <div class="mb-8 last:mb-0 relative">
                            <span class="absolute -left-[21px] top-1 w-4 h-4 rounded-full border-2 border-white 
                                {{ $key === $order->status ? 'bg-[var(--gold)] shadow-md' : 'bg-slate-300' }}"></span>
                            
                            <h4 class="text-sm font-bold {{ $key === $order->status ? 'text-[var(--black)]' : 'text-slate-500' }}">
                                {{ $info['label'] }}
                            </h4>
                            @if($key === $order->status)
                                <p class="text-xs text-slate-400 mt-1">
                                    Last updated: {{ $order->updated_at->format('M d, g:i A') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Items --}}
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring">
                <h3 class="text-lg font-bold text-[var(--black)] mb-6 border-b border-slate-100 pb-4">Items Ordered</h3>
                <div class="space-y-6">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-slate-50 rounded overflow-hidden flex-shrink-0 border border-slate-100">
                                @if($item->product && is_array($item->product->images) && count($item->product->images) > 0)
                                     <img src="{{ asset('storage/' . $item->product->images[0]) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <i class="la la-image text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-[var(--black)]">{{ $item->product ? $item->product->name : 'Product (Deleted)' }}</h4>
                                <p class="text-xs text-slate-500 mt-1">Qty: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-[var(--black)]">RWF {{ number_format($item->price * $item->quantity, 0) }}</div>
                                <div class="text-xs text-slate-400">{{ number_format($item->price, 0) }} / each</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Column: Summary & Actions --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Summary --}}
            <div class="bg-slate-50 rounded-lg p-6 border border-slate-200">
                <h3 class="text-base font-bold text-[var(--black)] mb-4">Summary</h3>
                <div class="space-y-3 text-sm border-b border-slate-200 pb-4 mb-4">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>RWF {{ number_format($order->subtotal ?? $order->total, 0) }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>-RWF {{ number_format($order->discount, 0) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-semibold text-[var(--black)]">
                        <span>Product Total</span>
                        <span>RWF {{ number_format($order->total, 0) }}</span>
                    </div>
                </div>

                {{-- Shipping Cost Section --}}
                @if($order->hasShippingCost())
                    <div class="space-y-3 text-sm border-b border-slate-200 pb-4 mb-4">
                        <div class="flex justify-between text-slate-600">
                            <span>Shipping (DHL)</span>
                            <span>RWF {{ number_format($order->shipping_cost, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 text-xs">
                            <span>Service Fee (5%)</span>
                            <span>RWF {{ number_format($order->shipping_service_fee, 0) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold {{ $order->isShippingPaid() ? 'text-green-700' : 'text-amber-700' }}">
                            <span class="flex items-center gap-1">
                                <i class="la {{ $order->isShippingPaid() ? 'la-check-circle' : 'la-clock' }} text-base"></i>
                                Shipping Total
                            </span>
                            <span>RWF {{ number_format($order->shipping_total, 0) }}</span>
                        </div>
                        @if($order->isShippingPaid() && $order->shipping_paid_at)
                            <div class="text-xs text-green-600">
                                Paid on {{ $order->shipping_paid_at->format('M d, Y') }}
                            </div>
                        @endif
                        @if($order->shipping_notes)
                            <div class="text-xs text-slate-500 italic">
                                {{ $order->shipping_notes }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="space-y-2 text-sm border-b border-slate-200 pb-4 mb-4">
                        <div class="flex justify-between text-slate-400">
                            <span class="flex items-center gap-1">
                                <i class="la la-truck text-base"></i>
                                Shipping
                            </span>
                            <span class="italic">Pending DHL quote</span>
                        </div>
                    </div>
                @endif

                <div class="flex justify-between items-center text-lg font-bold text-[var(--black)]">
                    <span>Grand Total</span>
                    <span>RWF {{ number_format($order->grand_total, 0) }}</span>
                </div>
            </div>

            {{-- Shipping Payment Action --}}
            @if($order->needsShippingPayment())
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="la la-truck text-amber-700 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-amber-900">Shipping Payment Required</p>
                            <p class="text-xs text-amber-700 mt-1">
                                Your shipping cost has been calculated. Please pay
                                <strong>RWF {{ number_format($order->shipping_total, 0) }}</strong>
                                to proceed with delivery.
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('shipping.pay') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit"
                            class="block w-full py-3 bg-[var(--gold)] hover:bg-[#B08D4C] text-white font-bold rounded shadow-sm text-sm transition-colors">
                            <i class="la la-credit-card mr-2 text-lg"></i>
                            Pay Shipping — RWF {{ number_format($order->shipping_total, 0) }}
                        </button>
                    </form>
                </div>
            @elseif($order->hasShippingCost() && $order->isShippingPaid())
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="flex items-center justify-center gap-2 text-green-700">
                        <i class="la la-check-circle text-xl"></i>
                        <span class="text-sm font-semibold">Shipping Paid ✓</span>
                    </div>
                </div>
            @endif

            {{-- Delivery Info --}}
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring">
                <h3 class="text-sm font-bold text-[var(--black)] mb-4 uppercase tracking-wider">Delivery Details</h3>
                <div class="space-y-4 text-sm">

                    {{-- Shipping address from checkout --}}
                    <div>
                        <span class="block text-xs text-slate-400 uppercase mb-1">Shipping To</span>
                        @if($order->shipping_address)
                            <p class="font-medium text-slate-700 leading-relaxed">{{ $order->shipping_address }}</p>
                        @else
                            <p class="text-slate-400 italic text-xs">No address recorded on this order.</p>
                        @endif
                    </div>

                    {{-- Contact --}}
                    <div>
                        <span class="block text-xs text-slate-400 uppercase mb-1">Contact</span>
                        <p class="font-medium text-slate-700">{{ $order->customer_email }}</p>
                        @if($order->customer_phone)
                            <p class="font-medium text-slate-700">{{ $order->customer_phone }}</p>
                        @endif
                    </div>

                    {{-- Saved account addresses --}}
                    @if($savedAddresses->isNotEmpty())
                        <div class="border-t border-slate-100 pt-4">
                            <span class="block text-xs text-slate-400 uppercase mb-2">Your Saved Addresses</span>
                            <div class="space-y-2">
                                @foreach($savedAddresses as $addr)
                                    <div class="rounded-md border {{ $addr->is_default ? 'border-[var(--gold)]/40 bg-amber-50' : 'border-slate-100 bg-slate-50' }} p-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-semibold text-slate-800">{{ $addr->name }}</span>
                                            @if($addr->is_default)
                                                <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">Default</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-600 leading-snug">
                                            {{ $addr->address_line_1 }}@if($addr->address_line_2), {{ $addr->address_line_2 }}@endif,
                                            {{ $addr->city }}@if($addr->state), {{ $addr->state }}@endif,
                                            {{ $addr->country }}
                                        </p>
                                        @if($addr->phone)
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $addr->phone }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Actions --}}
            @if($order->status === 'pending_payment')
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-center space-y-3">
                    <p class="text-sm text-amber-800 font-medium">Payment is pending</p>
                    <a href="{{ route('order.payment', $order->id) }}" 
                       class="block w-full py-3 bg-[var(--gold)] hover:bg-[#B08D4C] text-white font-bold rounded shadow-sm text-sm transition-colors">
                        Complete Payment
                    </a>
                </div>
            @endif

            @if(in_array($order->status, ['pending_payment', 'processing']))
                 <button type="button"
                        @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }}, orderNumber: '{{ $order->masked_order_id }}' })"
                        class="block w-full py-2 text-red-600 hover:text-red-700 text-sm font-medium border border-red-100 bg-red-50 hover:bg-red-100 rounded transition-colors">
                    Cancel Order
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
@include('components.cancel-order-modal')
@endsection

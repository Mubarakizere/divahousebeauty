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
                </div>
                <div class="flex justify-between items-center text-lg font-bold text-[var(--black)]">
                    <span>Total</span>
                    <span>RWF {{ number_format($order->total, 0) }}</span>
                </div>
            </div>

            {{-- Delivery Info --}}
            <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring">
                <h3 class="text-sm font-bold text-[var(--black)] mb-4 uppercase tracking-wider">Delivery Details</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="block text-xs text-slate-400 uppercase">Shipping To</span>
                        <p class="font-medium text-slate-700 mt-1">{{ $order->shipping_address ?? 'No address provided' }}</p>
                    </div>
                    <div>
                         <span class="block text-xs text-slate-400 uppercase">Contact</span>
                         <p class="font-medium text-slate-700 mt-1">{{ $order->customer_email }}</p>
                         <p class="font-medium text-slate-700">{{ $order->customer_phone }}</p>
                    </div>
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

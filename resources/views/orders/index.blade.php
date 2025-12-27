@extends('layouts.store')

@section('title', 'My Order History')
@section('subtitle', 'Track your past purchases and managing orders.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    @if($orders->count() > 0)
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-ring hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-3">
                                <h4 class="text-lg font-bold text-[var(--black)]">
                                    Order #{{ $order->masked_order_id }}
                                </h4>
                                <span class="badge {{ $order->status === 'pending_payment' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 mt-1">
                                Placed on {{ $order->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-[var(--gold)]">
                                RWF {{ number_format($order->total, 0) }}
                            </div>
                        </div>
                    </div>
                    
                    {{-- Order Items Preview --}}
                    <div class="space-y-3 mb-6">
                        @foreach($order->items->take(2) as $item)
                            <div class="flex items-center gap-4 text-sm text-slate-600">
                                <div class="w-12 h-12 bg-slate-50 rounded overflow-hidden flex-shrink-0">
                                    @if($item->product && is_array($item->product->images) && count($item->product->images) > 0)
                                         <img src="{{ asset('storage/' . $item->product->images[0]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                                            <i class="la la-box"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <span class="font-medium text-[var(--black)]">{{ $item->product ? $item->product->name : 'Product' }}</span>
                                    <span class="text-slate-400 mx-2">×</span>
                                    <span>{{ $item->quantity }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($order->items->count() > 2)
                            <div class="text-xs text-slate-500 pl-16">
                                + {{ $order->items->count() - 2 }} more item(s)
                            </div>
                        @endif
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('orders.show', $order->id) }}" 
                           class="inline-flex items-center px-4 py-2 border border-slate-300 rounded text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                            View Details
                        </a>
                        
                        @if($order->status === 'pending_payment')
                            <a href="{{ route('order.payment', $order->id) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded text-sm font-medium text-white bg-[var(--gold)] hover:bg-[#B08D4C] transition-colors shadow-sm">
                                <i class="la la-credit-card mr-2 text-lg"></i>
                                Complete Payment
                            </a>
                        @endif
                        
                        @if(in_array($order->status, ['pending_payment', 'processing']))
                            <button type="button"
                                    @click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }}, orderNumber: '{{ $order->masked_order_id }}' })"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium px-2">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-16 bg-white rounded-lg border border-slate-100 shadow-ring">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                <i class="la la-shopping-bag text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-[var(--black)] mb-2">No orders yet</h3>
            <p class="text-slate-500 mb-8 max-w-sm mx-auto">It looks like you haven't placed any orders yet. Explore our collection to find your new favorites.</p>
            <a href="{{ url('/') }}" 
               class="btn-gold inline-flex items-center px-8 py-3 rounded text-sm font-semibold shadow-lg shadow-orange-500/20">
                Start Shopping
            </a>
        </div>
    @endif
</div>

<!-- Cancel Order Modal -->
@include('components.cancel-order-modal')
@endsection

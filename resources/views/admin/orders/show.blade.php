@extends('layouts.dashboard')

@section('title', 'Order Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-file-invoice text-blue-600 mr-3"></i>
                Order Details
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Review and update this order.
            </p>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Orders
        </a>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $displayId = $order->masked_order_id ?: ('#'.$order->id);
    @endphp

    {{-- Top Summary Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-700">

            {{-- Left: Order Meta --}}
            <div>
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-hashtag text-gray-400"></i>
                    <span>Order</span>
                </h2>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Order ID:</span>
                        <span class="font-medium text-gray-900">{{ $displayId }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium text-gray-900">
                            {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created:</span>
                        <span class="font-medium text-gray-900">
                            {{ $order->created_at? $order->created_at->format('d M Y H:i') : '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created by user:</span>
                        <span class="font-medium text-gray-900">
                            {{ $order->user?->name ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Middle: Customer --}}
            <div>
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user text-gray-400"></i>
                    <span>Customer</span>
                </h2>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Name:</span>
                        <span class="font-medium text-gray-900">
                            {{ $order->customer_name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phone:</span>
                        <span class="font-medium text-gray-900">
                            {{ $order->customer_phone ?? '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email:</span>
                        <span class="font-medium text-gray-900">
                            {{ $order->customer_email ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right: Payment --}}
            <div>
                <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-credit-card text-gray-400"></i>
                    <span>Payment</span>
                </h2>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total:</span>
                        <span class="font-semibold text-gray-900">
                            {{ number_format($order->total, 2) }} RWF
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Method:</span>
                        <span class="font-medium text-gray-900">
                            {{ $order->payment_method ?? '—' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Transaction ID:</span>
                        <span class="font-medium text-gray-900 break-all">
                            {{ $order->transaction_id ?? '—' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Paid?</span>
                        <span class="font-medium text-gray-900 flex items-center gap-2">
                            @if($order->is_paid)
                                <span class="text-green-600 flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i>
                                    Yes
                                </span>
                            @else
                                <span class="text-red-600 flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    No
                                </span>
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Paid at:</span>
                        <span class="font-medium text-gray-900">
                            {{ $order->paid_at? $order->paid_at->format('d M Y H:i') : '—' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Status / Mark Paid Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        {{-- Update Status --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-pen text-gray-400"></i>
                <span>Update Order Status</span>
            </h2>

            <form action="{{ route('admin.orders.updateStatus', $order->id) }}"
                  method="POST"
                  class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        @php
                            $statuses = [
                                'pending_payment' => 'Pending payment',
                                'paid'            => 'Paid',
                                'shipped'         => 'Shipped',
                                'completed'       => 'Completed',
                                'cancelled'       => 'Cancelled',
                            ];
                        @endphp

                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($order->status === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Save Status
                    </button>
                </div>
            </form>
        </div>

        {{-- Mark Paid + Danger --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-coins text-gray-400"></i>
                <span>Payment Actions</span>
            </h2>

            <div class="mt-4 space-y-4">
                {{-- Mark as Paid --}}
                @if(!$order->is_paid)
                    <form action="{{ route('admin.orders.markPaid', $order->id) }}"
                          method="POST"
                          class="flex items-start justify-between gap-4 border border-yellow-200 bg-yellow-50 rounded-lg p-4">
                        @csrf
                        @method('PUT')

                        <div class="text-sm text-gray-700">
                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-money-bill text-yellow-600"></i>
                                <span>Mark this order as paid</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">
                                This will set <span class="font-medium">is_paid = 1</span>,
                                store <span class="font-medium">paid_at = now()</span>,
                                and if status is still "pending_payment"
                                we switch it to "paid".
                            </p>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-xs font-medium">
                            <i class="fas fa-check mr-2"></i>
                            Confirm
                        </button>
                    </form>
                @else
                    <div class="flex items-start justify-between gap-4 border border-green-200 bg-green-50 rounded-lg p-4 text-sm text-gray-700">
                        <div>
                            <div class="font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span>Already marked as paid</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">
                                Paid at:
                                <span class="font-medium text-gray-900">
                                    {{ $order->paid_at? $order->paid_at->format('d M Y H:i') : '—' }}
                                </span>
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Delete Order --}}
                <form action="{{ route('admin.orders.destroy', $order->id) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this order and its items? This cannot be undone.');"
                      class="flex items-start justify-between gap-4 border border-red-200 bg-red-50 rounded-lg p-4 text-sm text-gray-700">
                    @csrf
                    @method('DELETE')

                    <div>
                        <div class="font-semibold text-red-700 flex items-center gap-2">
                            <i class="fas fa-trash text-red-600"></i>
                            <span>Delete Order</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            This will <span class="font-semibold text-red-600">permanently remove</span>
                            the order and all of its items.
                        </p>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs font-medium">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>

    </div> {{-- /actions grid --}}

    {{-- Items Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-12">
        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2 mb-4">
            <i class="fas fa-list text-gray-400"></i>
            <span>Order Items</span>
        </h2>

        @if($order->items->count())
            {{-- Desktop table --}}
            <div class="hidden md:block">
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider grid grid-cols-5 gap-4">
                    <div>Product</div>
                    <div class="text-center">Price</div>
                    <div class="text-center">Qty</div>
                    <div class="text-center">Line Total</div>
                    <div class="text-center">Product ID</div>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="px-4 py-3 text-sm grid grid-cols-5 gap-4 items-center hover:bg-gray-50">
                            {{-- Product --}}
                            <div class="font-medium text-gray-900">
                                {{ $item->product?->name ?? 'Unknown product' }}
                            </div>

                            {{-- Price --}}
                            <div class="text-center text-gray-700">
                                {{ number_format($item->price, 2) }} RWF
                            </div>

                            {{-- Qty --}}
                            <div class="text-center text-gray-700">
                                {{ $item->quantity }}
                            </div>

                            {{-- Line Total --}}
                            <div class="text-center text-gray-900 font-semibold">
                                {{ number_format($item->line_total, 2) }} RWF
                            </div>

                            {{-- Product ID --}}
                            <div class="text-center text-xs text-gray-500">
                                ID: {{ $item->product_id }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Mobile cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                @foreach($order->items as $item)
                    <div class="py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $item->product?->name ?? 'Unknown product' }}
                                </div>
                                <div class="mt-1 text-xs text-gray-600 space-y-1">
                                    <div>
                                        Price:
                                        <span class="font-medium text-gray-900">
                                            {{ number_format($item->price, 2) }} RWF
                                        </span>
                                    </div>
                                    <div>
                                        Qty:
                                        <span class="font-medium text-gray-900">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                    <div>
                                        Line Total:
                                        <span class="font-semibold text-gray-900">
                                            {{ number_format($item->line_total, 2) }} RWF
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-gray-500">
                                        Product ID: {{ $item->product_id }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <div class="text-center py-12 text-gray-500 text-sm">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-box-open text-gray-400 text-lg"></i>
                </div>
                <div class="font-medium text-gray-900">No items in this order</div>
                <div class="text-xs text-gray-500 mt-1">This order has no products linked.</div>
            </div>
        @endif
    </div>

</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Manage Orders')

@section('content')
<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
    x-data="{
        confirmOpen: false,
        deleteUrl: null,
        displayId: null,
        openConfirm(url, idLabel) {
            this.deleteUrl = url;
            this.displayId = idLabel;
            this.confirmOpen = true;
        },
        closeConfirm() {
            this.confirmOpen = false;
        }
    }"
>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-receipt text-blue-600 mr-3"></i>
                Orders
            </h1>
            <p class="mt-1 text-sm text-gray-600">Track orders, payments, and status updates</p>
        </div>
    </div>

    {{-- Filters/Search --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET"
              action="{{ route('admin.orders.index') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Search box --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">
                    Search (name, phone, email, order ID, transaction...)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="e.g. 078..., Jane Doe, ORD-123..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                </div>
            </div>

            {{-- Status filter --}}
            <div>
                <label for="status" class="block text-xs font-medium text-gray-700 mb-1">
                    Status
                </label>
                <select
                    id="status"
                    name="status"
                    class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">All</option>
                    <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Pending payment</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-end gap-3">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium w-full justify-center">
                    <i class="fas fa-filter mr-2"></i>
                    Apply
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('admin.orders.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium w-full justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($orders->count())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            {{-- Desktop header row --}}
            <div class="hidden lg:block bg-gray-50 border-b border-gray-200 px-6 py-3">
                <div class="grid grid-cols-6 gap-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <div class="text-center w-10">#</div>
                    <div>Order / Customer</div>
                    <div class="text-center">Total</div>
                    <div class="text-center">Payment</div>
                    <div class="text-center">Created</div>
                    <div class="text-center">Actions</div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    @php
                        $displayId = $order->masked_order_id ?: ('#'.$order->id);
                    @endphp

                    {{-- Desktop row --}}
                    <div class="hidden lg:block px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="grid grid-cols-6 gap-4 items-center">

                            {{-- # --}}
                            <div class="text-center text-sm text-gray-900 font-medium">
                                {{ $rowStart + $loop->iteration }}
                            </div>

                            {{-- Order / Customer --}}
                            <div class="text-sm text-gray-900">
                                <div class="font-semibold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    <span>{{ $displayId }}</span>
                                    @if($order->status)
                                        <span class="text-[11px] px-2 py-0.5 rounded-full
                                            @if($order->status === 'paid')
                                                bg-green-100 text-green-700
                                            @elseif($order->status === 'pending_payment')
                                                bg-yellow-100 text-yellow-700
                                            @elseif($order->status === 'cancelled')
                                                bg-red-100 text-red-700
                                            @else
                                                bg-gray-100 text-gray-700
                                            @endif
                                        ">
                                            {{ Str::title(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-600 mt-1">
                                    {{ $order->customer_name ?? '—' }}
                                    <span class="text-gray-400">•</span>
                                    {{ $order->customer_phone ?? '—' }}
                                </div>
                            </div>

                            {{-- Total --}}
                            <div class="text-center text-sm font-semibold text-gray-900">
                                {{ number_format($order->total, 2) }} RWF
                                @if($order->is_paid)
                                    <div class="text-[11px] text-green-600 font-medium flex items-center justify-center gap-1">
                                        <i class="fas fa-check-circle"></i>
                                        Paid
                                    </div>
                                @else
                                    <div class="text-[11px] text-red-600 font-medium flex items-center justify-center gap-1">
                                        <i class="fas fa-clock"></i>
                                        Unpaid
                                    </div>
                                @endif
                            </div>

                            {{-- Payment --}}
                            <div class="text-center text-xs text-gray-700 leading-tight">
                                <div class="font-medium text-gray-900 text-sm">
                                    {{ $order->payment_method ?? '—' }}
                                </div>
                                <div class="text-[11px] text-gray-500 break-all">
                                    Tx: {{ $order->transaction_id ?? '—' }}
                                </div>
                            </div>

                            {{-- Created --}}
                            <div class="text-center text-xs text-gray-700 leading-tight">
                                <div class="text-gray-900 text-sm">
                                    {{ $order->created_at? $order->created_at->format('d M Y') : '—' }}
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    {{ $order->created_at? $order->created_at->format('H:i') : '' }}
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="inline-flex items-center p-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors"
                                   title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                <button
                                    type="button"
                                    @click="openConfirm('{{ route('admin.orders.destroy', $order->id) }}', '{{ $displayId }}')"
                                    class="inline-flex items-center p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                    title="Delete order">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile card --}}
                    <div class="lg:hidden p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-md bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-file-invoice text-gray-500 text-sm"></i>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 flex flex-wrap items-center gap-2">
                                            <span>{{ $displayId }}</span>

                                            @if($order->status)
                                                <span class="text-[11px] px-2 py-0.5 rounded-full
                                                    @if($order->status === 'paid')
                                                        bg-green-100 text-green-700
                                                    @elseif($order->status === 'pending_payment')
                                                        bg-yellow-100 text-yellow-700
                                                    @elseif($order->status === 'cancelled')
                                                        bg-red-100 text-red-700
                                                    @else
                                                        bg-gray-100 text-gray-700
                                                    @endif
                                                ">
                                                    {{ Str::title(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-1 text-xs text-gray-600">
                                            {{ $order->customer_name ?? '—' }} /
                                            {{ $order->customer_phone ?? '—' }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-600">
                                            Total:
                                            <span class="font-semibold text-gray-900">
                                                {{ number_format($order->total, 2) }} RWF
                                            </span>
                                            @if($order->is_paid)
                                                <span class="text-[11px] text-green-600 font-medium ml-1">
                                                    (Paid)
                                                </span>
                                            @else
                                                <span class="text-[11px] text-red-600 font-medium ml-1">
                                                    (Unpaid)
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-1 text-[11px] text-gray-500">
                                            {{ $order->created_at? $order->created_at->format('d M Y H:i') : '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col space-y-2 ml-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="p-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors"
                                   title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                <button
                                    type="button"
                                    @click="openConfirm('{{ route('admin.orders.destroy', $order->id) }}', '{{ $displayId }}')"
                                    class="p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                    title="Delete order">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div
            x-show="confirmOpen"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
            style="display: none;"
        >
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-triangle-exclamation text-red-600 mr-2"></i>
                    Delete Order
                </h2>

                <p class="text-sm text-gray-600 mt-2">
                    Are you sure you want to delete
                    <span class="font-semibold text-gray-900" x-text="displayId"></span>?
                    This will remove the order and its items.
                </p>

                <div class="mt-6 flex flex-col sm:flex-row sm:justify-end gap-3">
                    <button
                        type="button"
                        @click="closeConfirm()"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors w-full sm:w-auto justify-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>

                    <form :action="deleteUrl"
                          method="POST"
                          class="w-full sm:w-auto flex justify-center">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium w-full sm:w-auto justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if(
            $orders instanceof \Illuminate\Contracts\Pagination\Paginator &&
            $orders->hasPages()
        )
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    {{-- Mobile Prev/Next --}}
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($orders->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                Previous
                            </span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}"
                               class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                Next
                            </span>
                        @endif
                    </div>

                    {{-- Desktop Pagination --}}
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ $orders->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $orders->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $orders->total() }}</span>
                                orders
                            </p>
                        </div>

                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Prev --}}
                                @if($orders->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-default">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}"
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                {{-- Page numbers --}}
                                @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                    @if($page == $orders->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}"
                                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                {{-- Next --}}
                                @if($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl() }}"
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-default">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
            <p class="text-gray-600">
                No orders match the filters you entered.
            </p>
        </div>
    @endif
</div>
@endsection

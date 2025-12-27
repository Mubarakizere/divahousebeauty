@extends('layouts.dashboard')

@section('title', 'Reviews')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-star text-yellow-500 mr-2"></i>
            Product Reviews
        </h1>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Reviews</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <i class="fas fa-comments text-3xl text-blue-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                    <i class="fas fa-clock text-3xl text-yellow-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                    </div>
                    <i class="fas fa-times-circle text-3xl text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}" 
                   class="px-6 py-3 text-sm font-medium transition-colors border-b-2
                          {{ $status === 'all' 
                                ? 'border-blue-500 text-blue-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    All Reviews
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" 
                   class="px-6 py-3 text-sm font-medium transition-colors border-b-2
                          {{ $status === 'pending' 
                                ? 'border-yellow-500 text-yellow-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending
                    @if($stats['pending'] > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                            {{ $stats['pending'] }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" 
                   class="px-6 py-3 text-sm font-medium transition-colors border-b-2
                          {{ $status === 'approved' 
                                ? 'border-green-500 text-green-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Approved
                </a>
                <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}" 
                   class="px-6 py-3 text-sm font-medium transition-colors border-b-2
                          {{ $status === 'rejected' 
                                ? 'border-red-500 text-red-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Rejected
                </a>
            </nav>
        </div>

        <!-- Success Message -->
        @if(session('message'))
            <div class="p-4 bg-green-50 border-b border-green-200">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <span class="text-green-800 font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        <!-- Reviews List -->
        <div class="divide-y divide-gray-200">
            @forelse($reviews as $review)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Rating Stars -->
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $review->rating }}/5</span>
                                
                                <!-- Status Badge -->
                                @if($review->status === 'pending')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                @elseif($review->status === 'approved')
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Approved
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>
                                        Rejected
                                    </span>
                                @endif

                                <!-- Verified Purchase -->
                                @if($review->verified_purchase)
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        Verified Purchase
                                    </span>
                                @endif
                            </div>

                            <!-- Review Title -->
                            @if($review->title)
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $review->title }}</h3>
                            @endif

                            <!-- Review Content -->
                            <p class="text-gray-700 mb-3">{{ $review->review }}</p>

                            <!-- Meta Info -->
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $review->user->name ?? 'Guest' }}
                                </span>
                                <span>
                                    <i class="fas fa-box mr-1"></i>
                                    <a href="{{ route('product', $review->product->slug) }}" 
                                       class="text-blue-600 hover:underline"
                                       target="_blank">
                                        {{ $review->product->name }}
                                    </a>
                                </span>
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $review->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 ml-4">
                            @if($review->status !== 'approved')
                                <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                                            title="Approve">
                                        <i class="fas fa-check mr-1"></i>
                                        Approve
                                    </button>
                                </form>
                            @endif

                            @if($review->status !== 'rejected')
                                <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors"
                                            title="Reject">
                                        <i class="fas fa-times mr-1"></i>
                                        Reject
                                    </button>
                                </form>
                            @endif

                            <button type="button"
                                    @click="$dispatch('open-delete-review-modal', { reviewId: {{ $review->id }}, reviewUser: '{{ $review->user->name ?? 'Guest' }}' })"
                                    class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <i class="fas fa-star text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Reviews Found</h3>
                    <p class="text-gray-600">
                        @if($status === 'pending')
                            No pending reviews at the moment.
                        @elseif($status === 'approved')
                            No approved reviews yet.
                        @elseif($status === 'rejected')
                            No rejected reviews.
                        @else
                            No reviews have been submitted yet.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Review Modal -->
@include('components.delete-review-modal')
@endsection

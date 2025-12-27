@extends('layouts.dashboard')

@section('title', 'Banners')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ad Banners</h1>
            <p class="mt-1 text-sm text-gray-500">Manage promotional banners for your homepage</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add New Banner
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start" role="alert">
            <i class="fas fa-check-circle mt-0.5 mr-3"></i>
            <div class="flex-1">{{ session('success') }}</div>
            <button type="button" class="text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($banners->count() > 0)
        {{-- Banners Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($banners as $banner)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    {{-- Banner Image --}}
                    <div class="relative aspect-video bg-gray-100">
                        <img src="{{ $banner->image_url }}" 
                             alt="{{ $banner->name }}" 
                             class="w-full h-full object-cover">
                        
                        {{-- Status Badge --}}
                        <div class="absolute top-3 right-3">
                            @if(!$banner->is_active)
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                    Inactive
                                </span>
                            @elseif(!$banner->isCurrentlyValid())
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                    Scheduled
                                </span>
                            @elseif($banner->end_date && now()->addDays(7)->gte($banner->end_date))
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700">
                                    Expiring Soon
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                    Active
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Banner Details --}}
                    <div class="p-4">
                        {{-- Name & Position --}}
                        <div class="mb-3">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $banner->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $banner->position_name }}
                            </p>
                        </div>

                        {{-- Title (if exists) --}}
                        @if($banner->title)
                            <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $banner->title }}</p>
                        @endif

                        {{-- Order & Dates --}}
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span><i class="fas fa-sort mr-1"></i>Order: {{ $banner->order }}</span>
                            @if($banner->end_date)
                                <span><i class="far fa-calendar mr-1"></i>{{ $banner->end_date->format('M d') }}</span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 border-t border-gray-100 pt-3">
                            @if($banner->link)
                                <a href="{{ $banner->link }}" target="{{ $banner->target }}" 
                                   class="flex-1 text-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    <i class="fas fa-external-link-alt mr-1"></i>Preview
                                </a>
                            @endif
                            
                            <a href="{{ route('admin.banners.edit', $banner) }}" 
                               class="flex-1 text-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            
                            <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Delete this banner?');"
                                  class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-images text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Banners Yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                Start creating promotional banners to showcase on your homepage and increase conversions.
            </p>
            <a href="{{ route('admin.banners.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create First Banner
            </a>
        </div>
    @endif
</div>
@endsection

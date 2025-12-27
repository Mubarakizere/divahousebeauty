@extends('layouts.dashboard')

@section('title', 'Send Newsletter')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Compose Newsletter</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Send an email to all active subscribers.</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <form action="{{ route('admin.newsletter.send') }}" method="POST" class="space-y-6 sm:p-6">
                @csrf
                
                {{-- Subject --}}
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <div class="mt-1">
                        <input type="text" name="subject" id="subject" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-diva-gold focus:ring-diva-gold sm:text-sm" required placeholder="e.g., Weekly Deals & New Arrivals">
                    </div>
                </div>

                {{-- Body --}}
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Message Body</label>
                    <div class="mt-1">
                        <textarea id="body" name="body" rows="10" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-diva-gold focus:ring-diva-gold sm:text-sm" required placeholder="Write your message here..."></textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Plain text is supported. It will be formatted automatically.</p>
                </div>

                <div class="flex justify-end pt-4">
                    <a href="{{ route('admin.newsletter.index') }}" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Cancel</a>
                    <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-paper-plane mr-2"></i> Send Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Add New User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Create New User</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Add a new administrator or customer account.</p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6 sm:p-6">
                @csrf
                
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-diva-gold focus:ring-diva-gold sm:text-sm">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <div class="mt-1">
                        <input type="email" name="email" id="email" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-diva-gold focus:ring-diva-gold sm:text-sm">
                    </div>
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-diva-gold focus:outline-none focus:ring-diva-gold sm:text-sm">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input type="password" name="password" id="password" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-diva-gold focus:ring-diva-gold sm:text-sm">
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <div class="mt-1">
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-diva-gold focus:ring-diva-gold sm:text-sm">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <a href="{{ route('admin.users.index') }}" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Cancel</a>
                    <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

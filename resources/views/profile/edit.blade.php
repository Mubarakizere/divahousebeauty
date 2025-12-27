@extends('layouts.store')

@section('title', 'Edit Profile')
@section('subtitle', 'Manage your account information and password.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-[var(--gold)] transition-colors">
            <i class="la la-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        {{-- Main Form --}}
        <div class="flex-1">
            <div class="bg-white border border-slate-200 rounded-lg p-6 md:p-8 shadow-ring">
                <h2 class="text-xl font-bold text-[var(--black)] mb-6">Account Details</h2>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6 flex items-center text-sm">
                        <i class="la la-check-circle mr-2 text-lg"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    {{-- Personal Info Section --}}
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-[var(--gold)] uppercase tracking-wider border-b border-slate-100 pb-2">Personal Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-[var(--black)] mb-2">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-slate-700 transition-all">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-bold text-[var(--black)] mb-2">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-slate-700 transition-all">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Password Section --}}
                    <div class="space-y-4 pt-4">
                        <h3 class="text-sm font-bold text-[var(--gold)] uppercase tracking-wider border-b border-slate-100 pb-2">Change Password <span class="text-slate-400 font-normal normal-case ml-2">(Optional)</span></h3>
                        
                        <div>
                            <label for="current_password" class="block text-sm font-bold text-[var(--black)] mb-2">Current Password</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-slate-700 transition-all">
                            @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="new_password" class="block text-sm font-bold text-[var(--black)] mb-2">New Password</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-slate-700 transition-all">
                                @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-bold text-[var(--black)] mb-2">Confirm New Password</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-slate-700 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="btn-gold px-8 py-3 rounded font-medium shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar / Info --}}
        <div class="w-full md:w-80 space-y-6">
            <div class="bg-slate-50 border border-slate-100 rounded-lg p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-[var(--gold)] flex items-center justify-center text-white text-xl font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-[var(--black)]">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500">Member since {{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-slate-600">
                    <div class="flex items-center gap-2">
                        <i class="la la-envelope text-[var(--gold)]"></i>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="la la-phone text-[var(--gold)]"></i>
                        <span>{{ $user->phone ?? 'No phone added' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

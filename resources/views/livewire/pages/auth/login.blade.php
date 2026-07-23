<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    // Public User Google-Only Login Portal
}; ?>

<div>
    <!-- Session Status & Error Alert -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs rounded-lg p-3 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Header -->
    <div class="mb-6 text-center space-y-2">
        <div class="inline-flex items-center justify-center p-3 bg-red-50 dark:bg-red-950/40 text-[#cc6c3b] rounded-full mb-1">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Sign In to Getembe News</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs mx-auto">Bookmark stories, save preferences, and participate in community discussions.</p>
    </div>

    @php
        $googleEnabled = (bool) \App\Models\Setting::get('google_login', false);
        $facebookEnabled = (bool) \App\Models\Setting::get('facebook_login', false);
    @endphp

    <div class="space-y-3 pt-2">
        <!-- Google Sign In Button -->
        <a href="{{ route('social.redirect', 'google') }}" class="w-full flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-800 dark:text-gray-100 font-bold text-sm rounded-xl transition shadow-sm hover:shadow group">
            <svg class="h-5 w-5 mr-3 group-hover:scale-105 transition-transform" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.28v3.13C3.26 21.3 7.37 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.6H1.28C.46 8.23 0 10.06 0 12s.46 3.77 1.28 5.4l4-3.13z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.26 2.7 1.28 6.6l4 3.13c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span>Sign in with Google</span>
        </a>

        @if($facebookEnabled)
            <a href="{{ route('social.redirect', 'facebook') }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-[#1877F2] hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                <svg class="h-4 w-4 mr-2 fill-current" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Continue with Facebook</span>
            </a>
        @endif
    </div>

    <!-- Staff Access Link -->
    <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400 border-t border-gray-150 dark:border-gray-800/80 pt-4">
        {{ __('Staff, Author, Manager or Admin?') }}
        <a href="{{ route('access') }}" class="font-bold text-[#cc6c3b] hover:underline ml-1" wire:navigate>
            {{ __('Sign in via /access') }}
        </a>
    </div>
</div>

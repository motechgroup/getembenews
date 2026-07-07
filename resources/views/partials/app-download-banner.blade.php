@php
    $playStoreUrl = \App\Models\Setting::get('app_play_store_url', 'https://play.google.com/store');
    $appStoreUrl = \App\Models\Setting::get('app_app_store_url', 'https://www.apple.com/app-store');
    $title = \App\Models\Setting::get('app_banner_title', 'Download Getembe News Mobile App');
    $desc = \App\Models\Setting::get('app_banner_desc', 'Get fast, reliable, and breaking news alerts directly on your smartphone.');
@endphp

<!-- Sleek, Compact App Download Block -->
<div class="bg-gray-50 dark:bg-gray-955 border border-gray-200 dark:border-gray-850 rounded-xl p-4 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 mt-8 shadow-sm">
    <div class="space-y-1">
        <div class="flex items-center space-x-2">
            <span class="bg-[#C8102E]/10 dark:bg-[#C8102E]/20 text-[#C8102E] text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded">
                Mobile App
            </span>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                {{ $title }}
            </h4>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ $desc }}
        </p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3 shrink-0">
        <!-- Play Store Button (Compact) -->
        <a href="{{ $playStoreUrl }}" target="_blank" class="flex items-center space-x-2 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-900 dark:text-white px-3 py-1.5 rounded-lg transition border border-gray-200 dark:border-gray-800 shadow-sm text-xs font-bold">
            <svg class="h-4 w-4 shrink-0 text-gray-700 dark:text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                <path d="M5.23 3c-.18 0-.36.03-.53.1L13.56 12L4.7 20.9c.17.07.35.1.53.1c.36 0 .7-.14.95-.4l12-12c.26-.26.4-.6.4-.95s-.14-.7-.4-.95l-12-12c-.25-.26-.59-.4-.95-.4z"/>
            </svg>
            <span>Google Play</span>
        </a>

        <!-- App Store Button (Compact) -->
        <a href="{{ $appStoreUrl }}" target="_blank" class="flex items-center space-x-2 bg-black hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg transition shadow-sm border border-gray-800 text-xs font-bold">
            <svg class="h-4 w-4 shrink-0 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.17c.66-.81 1.11-1.93.99-3.06-1 .04-2.2.67-2.92 1.51-.62.73-1.16 1.87-1.01 2.98 1.11.08 2.24-.59 2.94-1.43z"/>
            </svg>
            <span>App Store</span>
        </a>
    </div>
</div>

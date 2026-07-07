@php
    $playStoreUrl = \App\Models\Setting::get('app_play_store_url', 'https://play.google.com/store');
    $appStoreUrl = \App\Models\Setting::get('app_app_store_url', 'https://www.apple.com/app-store');
    $title = \App\Models\Setting::get('app_banner_title', 'Download Getembe News Mobile App');
    $desc = \App\Models\Setting::get('app_banner_desc', 'Get fast, reliable, and breaking news alerts directly on your smartphone. Available now for Android and iOS devices.');
@endphp

<div class="bg-gradient-to-br from-gray-900 to-gray-950 text-white rounded-2xl p-6 sm:p-10 border border-gray-800 relative overflow-hidden shadow-xl mt-8">
    <!-- Background Decorative Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-[#C8102E]/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-[#FF7900]/5 rounded-full blur-3xl -ml-16 -mb-16 pointer-events-none"></div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-center relative z-10">
        <!-- Left: Text and Buttons -->
        <div class="lg:col-span-3 space-y-6">
            <span class="bg-[#C8102E]/20 text-[#FF7900] dark:text-[#FF7900] text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full border border-[#C8102E]/30">
                Get the App
            </span>
            <h2 class="text-2xl sm:text-3xl font-serif font-black text-white leading-tight">
                {{ $title }}
            </h2>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed max-w-xl">
                {{ $desc }}
            </p>
            
            <div class="flex flex-wrap gap-4 pt-2">
                <!-- Play Store Button -->
                <a href="{{ $playStoreUrl }}" target="_blank" class="flex items-center space-x-3 bg-white hover:bg-gray-100 text-black px-5 py-2.5 rounded-xl transition shadow-md border border-gray-200 shrink-0">
                    <svg class="h-6 w-6 text-black shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5.23 3c-.18 0-.36.03-.53.1L13.56 12L4.7 20.9c.17.07.35.1.53.1c.36 0 .7-.14.95-.4l12-12c.26-.26.4-.6.4-.95s-.14-.7-.4-.95l-12-12c-.25-.26-.59-.4-.95-.4z"/>
                    </svg>
                    <div class="text-left leading-none">
                        <span class="text-[9px] uppercase tracking-wider block text-gray-500 font-bold">GET IT ON</span>
                        <span class="text-sm font-black tracking-tight">Google Play</span>
                    </div>
                </a>

                <!-- App Store Button -->
                <a href="{{ $appStoreUrl }}" target="_blank" class="flex items-center space-x-3 bg-black hover:bg-gray-900 text-white px-5 py-2.5 rounded-xl transition shadow-md border border-gray-800 shrink-0">
                    <svg class="h-6 w-6 text-white shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.17c.66-.81 1.11-1.93.99-3.06-1 .04-2.2.67-2.92 1.51-.62.73-1.16 1.87-1.01 2.98 1.11.08 2.24-.59 2.94-1.43z"/>
                    </svg>
                    <div class="text-left leading-none">
                        <span class="text-[9px] uppercase tracking-wider block text-gray-400">Download on the</span>
                        <span class="text-sm font-black tracking-tight">App Store</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Right: Beautiful Smartphone Frame Mockup with CSS -->
        <div class="lg:col-span-2 hidden md:flex justify-center relative">
            <div class="relative w-48 h-96 bg-gray-900 border-4 border-gray-800 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col justify-between shrink-0">
                <!-- Phone Ear Speaker & Notch -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 h-4 w-24 bg-gray-800 rounded-b-xl z-20 flex items-center justify-center space-x-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-gray-900"></div>
                    <div class="w-8 h-1 bg-gray-900 rounded-full"></div>
                </div>

                <!-- Phone Screen Content -->
                <div class="flex-grow bg-white dark:bg-gray-900 p-3 pt-6 flex flex-col justify-between text-[8px]">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-800 pb-1 text-gray-955 dark:text-white font-serif font-black uppercase">
                        <span>Getembe App</span>
                        <span class="text-[#FF7900]">Live</span>
                    </div>

                    <div class="space-y-1.5 my-2">
                        <div class="aspect-[16/10] bg-gray-100 dark:bg-gray-800 rounded overflow-hidden relative">
                            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=200&h=120" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-bold text-gray-955 dark:text-white leading-tight">Fast News Alerts From Kisii Region</h4>
                        <p class="text-[6px] text-gray-500 line-clamp-2">Stay updated with breaking local politics, business reports, and sports achievements.</p>
                    </div>

                    <div class="bg-gray-950 text-white p-2 rounded border border-gray-800 space-y-1">
                        <div class="flex justify-between items-center text-[5px] font-bold uppercase tracking-wider text-red-500">
                            <span>Live Broadcast</span>
                            <span class="w-1 h-1 bg-red-600 rounded-full animate-ping"></span>
                        </div>
                        <h5 class="font-bold text-[7px] text-white">Getembe TV Stream</h5>
                    </div>

                    <div class="flex justify-around border-t border-gray-100 dark:border-gray-800 pt-1.5 text-gray-400 font-bold uppercase">
                        <span class="text-[#FF7900]">Home</span>
                        <span>TV</span>
                        <span>Radio</span>
                        <span>Saved</span>
                    </div>
                </div>

                <div class="absolute bottom-1.5 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-gray-800 rounded-full z-20"></div>
            </div>

            <!-- Floating mini badge -->
            <div class="absolute -top-4 -right-4 bg-[#FF7900] text-white text-[9px] font-black uppercase tracking-wider py-1.5 px-3 rounded-full shadow-lg transform rotate-6 animate-bounce">
                Free App!
            </div>
        </div>
    </div>
</div>

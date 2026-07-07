<x-news-layout>
    <x-slot name="title">Contact Us - Getembe News</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="border-b-4 border-[#C8102E] pb-4 mb-8">
            <h1 class="text-3xl font-serif font-black tracking-tight text-gray-900 dark:text-white">
                Contact Getembe News
            </h1>
            <p class="text-sm text-gray-500">Reach our newsroom, send editorial tips, or inquire about advertising.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Contact Information -->
            <div class="space-y-6 lg:col-span-1">
                <div class="space-y-2">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-[#C8102E]">Our Newsroom</h3>
                    <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                        {{ \App\Models\Setting::get('contact_address', "Getembe News Plaza, 3rd Floor\nHospital Road, Kisii Town\nP.O. Box 450 - 40200\nKisii, Kenya") }}
                    </p>
                </div>

                <div class="space-y-2 border-t border-gray-100 dark:border-gray-800 pt-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-[#C8102E]">Phone & Hotlines</h3>
                    <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed">
                        {{ \App\Models\Setting::get('contact_phone', '+254712345678') }}
                    </p>
                </div>

                <div class="space-y-2 border-t border-gray-100 dark:border-gray-800 pt-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-[#C8102E]">Email Desk</h3>
                    <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed font-mono">
                        {{ \App\Models\Setting::get('contact_email', 'contact@getembenews.com') }}
                    </p>
                </div>

                <div class="space-y-2 border-t border-gray-100 dark:border-gray-800 pt-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-[#C8102E]">Open Hours</h3>
                    <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                        {{ \App\Models\Setting::get('contact_open_hours', "Monday - Friday: 8:00 AM - 5:00 PM\nSaturday: 8:00 AM - 1:00 PM\nSunday: Closed") }}
                    </p>
                </div>

                <!-- Google Maps Mock / Office Photo -->
                <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 flex items-center justify-center text-xs text-gray-400 font-semibold tracking-wider">
                    MAP LOCATION (KISII TOWN)
                </div>

                <!-- App Download Links -->
                <div class="space-y-3 border-t border-gray-150 dark:border-gray-800 pt-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-[#C8102E]">Download Our App</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Stay connected with real-time news alerts from Kisii County by downloading the digital app.
                    </p>
                    <div class="flex items-center space-x-3 pt-1">
                        <a href="{{ \App\Models\Setting::get('app_play_store_url', 'https://play.google.com/store') }}" target="_blank" class="flex items-center space-x-2 bg-gray-900 hover:bg-gray-850 text-white px-3 py-1.5 rounded-lg border border-gray-800 transition shadow-sm">
                            <svg class="h-4 w-4 fill-current text-green-500" viewBox="0 0 24 24">
                                <path d="M3.609 1.814L13.783 12 3.609 22.186c-.185-.125-.306-.341-.306-.604V2.418c0-.263.121-.479.306-.604zM14.735 12.95l3.14 3.14-13.342 7.64c-.332.19-.74.076-.928-.255-.078-.139-.078-.309 0-.448l11.13-10.077zM4.605 1.613l13.342 7.64-3.14 3.14-11.13-10.077c-.078-.139-.078-.309 0-.448.188-.331.596-.445.928-.255zM15.688 12l3.447-3.447 3.522 2.016c.394.225.529.729.304 1.123-.075.132-.191.229-.33.278l-3.496 2.016L15.688 12z"/>
                            </svg>
                            <span class="text-[9px] font-black uppercase tracking-wider">Play Store</span>
                        </a>
                        <a href="{{ \App\Models\Setting::get('app_app_store_url', 'https://www.apple.com/app-store') }}" target="_blank" class="flex items-center space-x-2 bg-gray-950 hover:bg-gray-900 text-white px-3 py-1.5 rounded-lg border border-gray-900 transition shadow-sm">
                            <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.17c.66-.81 1.11-1.93.99-3.06-1 .04-2.2.67-2.92 1.51-.62.73-1.16 1.87-1.01 2.98 1.11.08 2.24-.59 2.94-1.43z"/>
                            </svg>
                            <span class="text-[9px] font-black uppercase tracking-wider">App Store</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side: Contact Form -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Send a Message</h3>
                <livewire:contact-form />
            </div>
        </div>
    </div>
</x-news-layout>

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
                    <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed">
                        Getembe News Plaza, 3rd Floor<br>
                        Hospital Road, Kisii Town<br>
                        P.O. Box 450 - 40200<br>
                        Kisii, Kenya
                    </p>
                </div>

                <div class="space-y-2 border-t border-gray-100 dark:border-gray-800 pt-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-[#C8102E]">Phone & Hotlines</h3>
                    <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed">
                        General Inquiries: +254 (0) 712 345 678<br>
                        Editorial Hotline: +254 (0) 789 012 345
                    </p>
                </div>

                <div class="space-y-2 border-t border-gray-100 dark:border-gray-800 pt-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-[#C8102E]">Email Desk</h3>
                    <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed font-mono">
                        news@getembenews.com<br>
                        tips@getembenews.com<br>
                        ads@getembenews.com
                    </p>
                </div>

                <!-- Google Maps Mock / Office Photo -->
                <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 flex items-center justify-center text-xs text-gray-400 font-semibold tracking-wider">
                    MAP LOCATION (KISII TOWN)
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

<x-news-layout>
    <x-slot name="title">Privacy Policy - Getembe News</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
        <h1 class="text-3xl font-serif font-black tracking-tight text-gray-900 dark:text-white border-b-4 border-[#C8102E] pb-3">
            Privacy Policy
        </h1>
        
        <div class="prose max-w-none dark:prose-invert text-gray-800 dark:text-gray-200 space-y-6 leading-relaxed">
            {!! \App\Models\Setting::get('privacy_content', \App\Models\Setting::defaultPrivacyContent()) !!}
        </div>
    </div>
</x-news-layout>

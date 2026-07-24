<x-news-layout>
    <x-slot name="title">Account Deletion Policy - Getembe News</x-slot>
    <x-slot name="metaDescription">Learn how to delete your Getembe News user account and manage your data privacy rights under our Account Deletion Policy.</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
        <div class="border-b-4 border-[#C8102E] pb-4 space-y-2">
            <span class="text-xs font-extrabold uppercase tracking-wider text-[#C8102E]">Data Privacy & Security</span>
            <h1 class="text-3xl sm:text-4xl font-serif font-black tracking-tight text-gray-900 dark:text-white">
                Account Deletion Policy
            </h1>
        </div>
        
        <div class="prose max-w-none dark:prose-invert text-gray-800 dark:text-gray-200 space-y-6 leading-relaxed bg-white dark:bg-gray-900 p-6 sm:p-8 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            {!! \App\Models\Setting::get('account_deletion_content', \App\Models\Setting::defaultAccountDeletionContent()) !!}
        </div>
    </div>
</x-news-layout>

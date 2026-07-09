@php
    $siteName = \App\Models\Setting::get('site_name', 'Getembe News');
    $parts = explode(' ', trim($siteName), 2);
    $firstWord = $parts[0] ?? 'Getembe';
    $secondWord = $parts[1] ?? 'News';
@endphp

<div class="flex items-center overflow-hidden rounded-md border border-gray-200 dark:border-gray-700 shadow-sm text-xs font-black uppercase tracking-tight select-none">
    <!-- Left: Orange -->
    <div class="bg-[#cc6c3b] px-3.5 py-1.5 text-white">
        {{ $firstWord }}
    </div>
    <!-- Right: Dark/Gray -->
    <div class="bg-gray-900 px-3.5 py-1.5 text-white border-l border-gray-200 dark:border-gray-700">
        {{ $secondWord }}
    </div>
</div>

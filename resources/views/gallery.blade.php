<x-news-layout>
    <x-slot name="title">Visual Albums & Gallery - Getembe News</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 space-y-8">
        <div class="text-center space-y-2 max-w-2xl mx-auto">
            <h1 class="text-4xl font-serif font-black tracking-tight text-gray-900 dark:text-white">
                Visual Albums & Galleries
            </h1>
            <p class="text-sm text-gray-500">Explore visual stories, photojournalism albums, and slideshow reports from Kisii and the regional community.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($articles as $album)
                <div class="group bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                    <a href="/articles/{{ $album->slug }}" class="block aspect-video overflow-hidden relative bg-gray-100 dark:bg-gray-950">
                        <img src="{{ $album->featured_image }}" alt="{{ $album->title }}" class="w-full h-full object-cover group-hover:scale-102 transition duration-350">
                        <!-- Number of images badge -->
                        <span class="absolute bottom-3 right-3 bg-black/75 text-white font-bold text-[10px] px-2 py-1 rounded flex items-center">
                            📷 {{ count($album->format_meta['gallery'] ?? []) }} Photos
                        </span>
                    </a>
                    <div class="p-4 space-y-2">
                        <span class="text-[9px] font-bold text-[#C8102E] uppercase">{{ $album->category->name }}</span>
                        <h3 class="text-base font-bold text-gray-950 dark:text-white line-clamp-2 leading-snug group-hover:text-[#C8102E] transition">
                            <a href="/articles/{{ $album->slug }}">{{ $album->title }}</a>
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                            {{ $album->subtitle ?? Str::limit(strip_tags($album->body), 100) }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center text-xs text-gray-400">
                    No visual gallery albums published yet. Check back soon!
                </div>
            @endforelse
        </div>
    </div>
</x-news-layout>

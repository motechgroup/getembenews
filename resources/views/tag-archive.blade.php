<x-news-layout>
    <x-slot name="title">#{{ $tag->name }} - Getembe News</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
        <div class="border-b-4 border-[#C8102E] pb-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest block">Topic Keyword Archive</span>
            <h1 class="text-3xl font-serif font-black tracking-tight text-gray-900 dark:text-white mt-1">
                #{{ $tag->name }}
            </h1>
        </div>

        <div class="space-y-6">
            @forelse($articles as $post)
                <article class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-900 border border-gray-250 dark:border-gray-800 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    @if($post->featured_image)
                        <div class="sm:w-1/3 aspect-[16/10] overflow-hidden rounded bg-gray-50 shrink-0">
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="flex-grow flex flex-col justify-between space-y-2">
                        <div>
                            <span class="text-[9px] font-bold text-[#C8102E] uppercase">{{ $post->category->name }}</span>
                            <h3 class="text-base font-bold text-gray-955 dark:text-white mt-1 leading-snug hover:text-[#C8102E] transition">
                                <a href="/articles/{{ $post->slug }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed">
                                {{ $post->subtitle ?? Str::limit(strip_tags($post->body), 150) }}
                            </p>
                        </div>
                        <div class="text-[10px] text-gray-400 flex items-center justify-between pt-2">
                            <span>By {{ $post->author->name }}</span>
                            <span>{{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="py-16 text-center text-xs text-gray-400">
                    No articles found matching this tag.
                </div>
            @endforelse
        </div>

        <div>
            {{ $articles->links() }}
        </div>
    </div>
</x-news-layout>

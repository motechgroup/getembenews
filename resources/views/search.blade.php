<x-news-layout>
    <x-slot name="title">Search Results for "{{ $query }}" - Getembe News</x-slot>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">
        
        <!-- Search Info Banner -->
        <div class="border-b border-gray-250 dark:border-gray-800 pb-4 space-y-4">
            <h1 class="text-3xl font-serif font-black tracking-tight text-gray-900 dark:text-white">
                Search Results
            </h1>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">
                Found {{ $articles->total() }} results for "<span class="text-[#C8102E]">{{ $query }}</span>"
            </p>

            <!-- Search Bar Inline -->
            <form action="/search" method="GET" class="flex max-w-md w-full">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search news..." class="flex-1 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-l-md py-1.5 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-gray-100">
                <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-r-md transition">
                    Search
                </button>
            </form>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($articles as $article)
                <article class="group space-y-3">
                    <div class="aspect-video overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-800">
                        <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-2 text-[10px] text-gray-400 font-semibold">
                            <span class="text-[#C8102E] uppercase font-bold">{{ $article->category?->name ?? 'News' }}</span>
                            <span>&bull;</span>
                            <span>{{ $article->published_at ? $article->published_at->diffForHumans() : '' }}</span>
                        </div>
                        <h2 class="text-base font-bold font-serif text-gray-900 dark:text-white leading-tight group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition line-clamp-2 break-words">
                            <a href="/articles/{{ $article->slug }}">{{ $article->title }}</a>
                        </h2>
                        <p class="text-xs text-gray-650 dark:text-gray-400 line-clamp-2 leading-relaxed">
                            {{ $article->subtitle }}
                        </p>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-16 text-center text-gray-400 dark:text-gray-650 text-sm">
                    No articles found matching your criteria. Try adjusting your keywords.
                </div>
            @endforelse
        </div>

        <!-- Pagination Links -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
            {{ $articles->links() }}
        </div>

    </div>
</x-news-layout>

<x-news-layout>
    <x-slot name="title">{{ $category->name }} Articles - Getembe News</x-slot>
    <x-slot name="metaDescription">{{ $category->description ?? 'Browse the latest articles in the ' . $category->name . ' category.' }}</x-slot>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">
        
        <!-- Category Banner -->
        <div class="border-b-4 border-[#C8102E] pb-4 space-y-2">
            <h1 class="text-3xl font-serif font-black tracking-tight text-gray-900 dark:text-white">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="text-sm text-gray-600 dark:text-gray-400 max-w-3xl leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif
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
                            <span>{{ $article->published_at->diffForHumans() }}</span>
                            <span>&bull;</span>
                            <span>{{ $article->read_time }} min read</span>
                        </div>
                        <h2 class="text-base font-bold font-serif text-gray-900 dark:text-white leading-tight group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition line-clamp-2">
                            <a href="/articles/{{ $article->slug }}">{{ $article->title }}</a>
                        </h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                            {{ $article->subtitle }}
                        </p>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-16 text-center text-gray-400 dark:text-gray-650 text-sm">
                    No articles found in this category.
                </div>
            @endforelse
        </div>

        <!-- Pagination Links -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
            {{ $articles->links() }}
        </div>

    </div>
</x-news-layout>

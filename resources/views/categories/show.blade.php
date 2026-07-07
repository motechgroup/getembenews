<x-news-layout>
    <x-slot name="title">{{ $category->name }} Articles - Getembe News</x-slot>
    <x-slot name="metaDescription">{{ $category->description ?? 'Browse the latest articles in the ' . $category->name . ' category.' }}</x-slot>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-10">
        
        <!-- Category Title (Centered Top) -->
        <div class="text-center py-6 border-b border-gray-150 dark:border-gray-850">
            <h1 class="text-4xl sm:text-5xl font-serif font-light text-gray-900 dark:text-white tracking-tight uppercase">
                {{ $category->name }} News
            </h1>
            @if($category->description)
                <p class="text-xs sm:text-sm text-gray-500 max-w-xl mx-auto mt-3 leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif
        </div>

        @php
            $items = collect($articles->items());
            $spotlight = $items->first();
            $centerFeatured = $items->get(1);
            $centerList = $items->slice(2, 4);
            $rightFeatured = $items->get(6);
            $rightList = $items->slice(7, 4);
            
            // For any remaining articles beyond index 10 on this page
            $remaining = $items->slice(11);
        @endphp

        @if($items->isNotEmpty())
            <!-- Al Jazeera-style 3-Column Magazine Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10 border-b border-gray-150 dark:border-gray-850">
                
                <!-- LEFT COLUMN: Large Spotlight -->
                <div class="lg:col-span-1">
                    @if($spotlight)
                        <article class="group space-y-3">
                            <div class="aspect-[16/10] overflow-hidden rounded-lg bg-gray-105 dark:bg-gray-850 border border-gray-200 dark:border-gray-800 relative z-10">
                                <img src="{{ $spotlight->featured_image }}" alt="{{ $spotlight->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                                @if($spotlight->views_count > 100)
                                    <!-- Dynamic Premium Live/Video aesthetic Badge -->
                                    <div class="absolute bottom-3 left-3 bg-black/80 text-white text-[10px] font-bold px-2 py-0.5 rounded flex items-center space-x-1 shadow">
                                        <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                        </svg>
                                        <span>02:15</span>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <span class="text-[10px] text-[#C8102E] font-bold uppercase tracking-wider block">From: {{ $spotlight->author_name ?? 'NewsFeed' }}</span>
                                <h3 class="text-xl sm:text-2xl font-bold font-serif text-gray-900 dark:text-white leading-tight group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition">
                                    <a href="/articles/{{ $spotlight->slug }}">{{ $spotlight->title }}</a>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-650 dark:text-gray-400 leading-relaxed font-medium line-clamp-3">
                                    {{ $spotlight->subtitle }}
                                </p>
                                <span class="text-[10px] text-gray-400 font-bold block">{{ $spotlight->published_at->format('j M Y') }}</span>
                            </div>
                        </article>
                    @endif
                </div>

                <!-- CENTER COLUMN: Explainer Spotlight & List -->
                <div class="lg:col-span-1 space-y-6">
                    @if($centerFeatured)
                        <article class="group space-y-3">
                            <div class="aspect-[16/10] overflow-hidden rounded-lg bg-gray-105 dark:bg-gray-850 border border-gray-200 dark:border-gray-800">
                                <img src="{{ $centerFeatured->featured_image }}" alt="{{ $centerFeatured->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                            </div>
                            <div class="space-y-1">
                                <span class="text-[9px] font-black text-yellow-600 dark:text-yellow-500 uppercase tracking-wider block">EXPLAINER</span>
                                <h3 class="text-base font-bold font-serif text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition">
                                    <a href="/articles/{{ $centerFeatured->slug }}">{{ $centerFeatured->title }}</a>
                                </h3>
                                <span class="text-[10px] text-gray-400 font-bold block">{{ $centerFeatured->published_at->format('j M Y') }}</span>
                            </div>
                        </article>
                    @endif

                    @if($centerList->isNotEmpty())
                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-850">
                            @foreach($centerList as $article)
                                <article class="group flex items-start gap-4 pb-4 border-b border-gray-100 dark:border-gray-855 last:border-0 last:pb-0">
                                    <div class="space-y-1 flex-grow min-w-0">
                                        @if($loop->first)
                                            <span class="text-[8px] font-black text-yellow-600 dark:text-yellow-500 uppercase tracking-widest block">EXPLAINER</span>
                                        @endif
                                        <h4 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition line-clamp-3">
                                            <a href="/articles/{{ $article->slug }}">{{ $article->title }}</a>
                                        </h4>
                                        <span class="text-[9px] text-gray-400 font-bold block">{{ $article->published_at->format('j M Y') }}</span>
                                    </div>
                                    <div class="w-20 sm:w-24 aspect-[16/10] overflow-hidden rounded bg-gray-105 dark:bg-gray-855 shrink-0 border border-gray-200 dark:border-gray-800">
                                        <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- RIGHT COLUMN: Spotlight & List -->
                <div class="lg:col-span-1 space-y-6">
                    @if($rightFeatured)
                        <article class="group space-y-3">
                            <div class="aspect-[16/10] overflow-hidden rounded-lg bg-gray-105 dark:bg-gray-850 border border-gray-200 dark:border-gray-800">
                                <img src="{{ $rightFeatured->featured_image }}" alt="{{ $rightFeatured->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                            </div>
                            <div class="space-y-1">
                                <h3 class="text-base font-bold font-serif text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition">
                                    <a href="/articles/{{ $rightFeatured->slug }}">{{ $rightFeatured->title }}</a>
                                </h3>
                                <span class="text-[10px] text-gray-400 font-bold block">{{ $rightFeatured->published_at->format('j M Y') }}</span>
                            </div>
                        </article>
                    @endif

                    @if($rightList->isNotEmpty())
                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-855">
                            @foreach($rightList as $article)
                                <article class="group flex items-start gap-4 pb-4 border-b border-gray-100 dark:border-gray-855 last:border-0 last:pb-0">
                                    <div class="space-y-1 flex-grow min-w-0">
                                        <h4 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition line-clamp-3">
                                            <a href="/articles/{{ $article->slug }}">{{ $article->title }}</a>
                                        </h4>
                                        <span class="text-[9px] text-gray-400 font-bold block">{{ $article->published_at->format('j M Y') }}</span>
                                    </div>
                                    <div class="w-20 sm:w-24 aspect-[16/10] overflow-hidden rounded bg-gray-105 dark:bg-gray-855 shrink-0 border border-gray-200 dark:border-gray-800">
                                        <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Bottom Section: Remaining Articles Grid -->
            @if($remaining->isNotEmpty())
                <div class="space-y-6 pt-6">
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-2 inline-block">
                        More from {{ $category->name }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($remaining as $article)
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
                                    <h2 class="text-base font-bold font-serif text-gray-900 dark:text-white leading-tight group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition line-clamp-2">
                                        <a href="/articles/{{ $article->slug }}">{{ $article->title }}</a>
                                    </h2>
                                    <p class="text-xs text-gray-650 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                        {{ $article->subtitle }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pagination Links -->
            <div class="pt-8 border-t border-gray-150 dark:border-gray-850">
                {{ $articles->links() }}
            </div>
        @else
            <div class="py-16 text-center text-gray-400 dark:text-gray-650 text-sm">
                No articles found in this category.
            </div>
        @endif

    </div>
</x-news-layout>

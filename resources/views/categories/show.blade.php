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
            $sidebarAd = \App\Models\Advertisement::active()->location('sidebar')->first();
            $items = collect($articles->items());
            $count = $items->count();

            // Set up containers
            $spotlight = null;
            $centerFeatured = null;
            $centerList = collect();
            $rightFeatured = null;
            $rightList = collect();
            $remaining = collect();

            if ($count > 0) {
                $spotlight = $items->first();
            }
            if ($count > 1) {
                $centerFeatured = $items->get(1);
            }
            if ($count > 2) {
                $rightFeatured = $items->get(2);
            }

            // Distribute remaining articles up to index 10 between center and right lists
            if ($count > 3) {
                $listArticles = $items->slice(3, 8); // index 3 to 10
                foreach ($listArticles as $idx => $article) {
                    if ($idx % 2 === 0) {
                        $centerList->push($article);
                    } else {
                        $rightList->push($article);
                    }
                }
            }

            // Articles beyond index 10 go to remaining
            if ($count > 11) {
                $remaining = $items->slice(11);
            }
        @endphp

        @if($items->isNotEmpty())
            <!-- Al Jazeera-style Magazine Grid Layout (Responsive Auto-centering Columns) -->
            <div class="@if($count === 1) max-w-2xl mx-auto pb-10 border-b border-gray-150 dark:border-gray-850 @elseif($count === 2) max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 pb-10 border-b border-gray-150 dark:border-gray-855 @else grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10 border-b border-gray-150 dark:border-gray-855 @endif">
                
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
                @if($centerFeatured)
                    <div class="lg:col-span-1 space-y-6">
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
                @endif

                <!-- RIGHT COLUMN: Spotlight & List -->
                @if($rightFeatured)
                    <div class="lg:col-span-1 space-y-6">
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
                @endif

            </div>

            <!-- Bottom Section: Main Feed & Sidebar Widgets -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-8 border-t border-gray-150 dark:border-gray-850">
                <!-- Left Main Area: More articles -->
                <div class="lg:col-span-2 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-2 inline-block">
                        More from {{ $category->name }}
                    </h3>

                    @if($remaining->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($remaining as $article)
                                <article class="group space-y-3">
                                    <div class="aspect-video overflow-hidden rounded-lg bg-gray-105 dark:bg-gray-850 border border-gray-250 dark:border-gray-800 relative">
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
                    @else
                        <div class="text-center py-12 bg-gray-50 dark:bg-gray-950 rounded border border-gray-150 dark:border-gray-850 text-xs text-gray-400">
                            No additional articles in this category.
                        </div>
                    @endif
                </div>

                <!-- Right Area: Ads & Necessary Widgets -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Sidebar Ad -->
                    <div class="bg-gray-50 dark:bg-gray-955 border border-gray-200 dark:border-gray-850 rounded-lg p-4 text-center">
                        <span class="text-[9px] text-gray-400 dark:text-gray-550 uppercase tracking-widest font-semibold block mb-3">Advertisement</span>
                        @if($sidebarAd)
                            <a href="{{ $sidebarAd->destination_url }}" target="_blank" class="block group">
                                <img src="{{ $sidebarAd->image_url }}" alt="{{ $sidebarAd->title }}" class="mx-auto rounded shadow-sm hover:opacity-95 transition">
                            </a>
                        @else
                            <!-- Premium Fallback Ad promoting dynamic content sponsorship -->
                            <a href="/contact" class="block w-full relative group">
                                <div class="relative bg-gradient-to-br from-gray-900 to-black border border-gray-800 rounded-lg overflow-hidden min-h-[180px] flex flex-col justify-between p-5 text-left">
                                    <div class="flex justify-center">
                                        <span class="bg-yellow-500 text-black font-extrabold text-[8px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">Sponsor Getembe</span>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="text-xs font-black text-white leading-tight">Your Banner Here</h4>
                                        <p class="text-[9px] text-gray-400 leading-snug">Place your ad here and reach Kisii's largest local digital news audience. Click to learn more.</p>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>

                    <!-- Necessary Widgets (Poll & Quiz) -->
                    @include('partials.sidebar-widgets')
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="pt-8 border-t border-gray-150 dark:border-gray-855">
                {{ $articles->links() }}
            </div>
        @else
            <div class="py-16 text-center text-gray-400 dark:text-gray-650 text-sm">
                No articles found in this category.
            </div>
        @endif

    </div>
</x-news-layout>

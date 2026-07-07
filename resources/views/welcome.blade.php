<x-news-layout>
    <!-- Homepage Main Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-10">

        <!-- Top Advertisement Banner -->
        @if($topAd)
            <div class="w-full text-center">
                <a href="{{ $topAd->destination_url }}" target="_blank" class="inline-block relative group">
                    <img src="{{ $topAd->image_url }}" alt="{{ $topAd->title }}" class="mx-auto rounded max-h-36 object-cover shadow-sm">
                    <span class="absolute top-1 left-1 bg-black/60 text-white text-[9px] px-1 rounded uppercase tracking-wider font-semibold">ADVERTISEMENT</span>
                </a>
            </div>
        @else
            <!-- Standard Placeholder Ad to make layout look premium -->
            <div class="w-full bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-center py-6 rounded text-xs text-gray-400 font-medium tracking-wide">
                ADVERTISEMENT BANNER (728x90)
            </div>
        @endif

        <!-- Hero Section: Layout Variants -->
        @if($layout === 'compact')
            <!-- 1. COMPACT / MINIMALIST LAYOUT VARIANT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: List style -->
                <div class="lg:col-span-2 space-y-6">
                    <h2 class="text-sm font-black border-b-2 border-gray-900 dark:border-white pb-2 text-gray-900 dark:text-white uppercase tracking-wider">Top Stories</h2>
                    <div class="space-y-6 divide-y divide-gray-150 dark:divide-gray-850">
                        @if($featuredArticle)
                            <article class="pt-0 group flex flex-col sm:flex-row gap-6">
                                <div class="w-full sm:w-1/3 aspect-[16/10] overflow-hidden rounded bg-gray-105 dark:bg-gray-850 shrink-0 border border-gray-200 dark:border-gray-800">
                                    <img src="{{ $featuredArticle->featured_image }}" alt="{{ $featuredArticle->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <div class="flex items-center space-x-2 text-[10px] font-bold text-[#C8102E] uppercase">
                                        <span>{{ $featuredArticle->category->name }}</span>
                                        <span class="text-gray-300 font-normal">&bull;</span>
                                        <span class="text-gray-400 font-normal">{{ $featuredArticle->published_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="text-lg font-serif font-bold text-gray-900 dark:text-white leading-tight group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition">
                                        <a href="/articles/{{ $featuredArticle->slug }}">{{ $featuredArticle->title }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">{{ $featuredArticle->subtitle }}</p>
                                </div>
                            </article>
                        @endif

                        @foreach($topStories as $story)
                            <article class="pt-6 group flex flex-col sm:flex-row gap-6">
                                <div class="w-full sm:w-1/3 aspect-[16/10] overflow-hidden rounded bg-gray-105 dark:bg-gray-850 shrink-0 border border-gray-200 dark:border-gray-800">
                                    <img src="{{ $story->featured_image }}" alt="{{ $story->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <div class="flex items-center space-x-2 text-[10px] font-bold text-[#C8102E] uppercase">
                                        <span>{{ $story->category->name }}</span>
                                        <span class="text-gray-300 font-normal">&bull;</span>
                                        <span class="text-gray-400 font-normal">{{ $story->published_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="text-base font-serif font-bold text-gray-900 dark:text-white leading-tight group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition">
                                        <a href="/articles/{{ $story->slug }}">{{ $story->title }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-650 dark:text-gray-450 line-clamp-2 leading-relaxed">{{ $story->subtitle }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <!-- Right Sidebar: Weather & Latest -->
                <div class="space-y-8">
                    <!-- Weather Widget -->
                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5" x-data="{ tempUnit: 'C', currentTemp: 24, high: 27, low: 18 }">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-800">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center space-x-1.5">
                                    <svg class="h-4.5 w-4.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 11-2 0v-1a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Kisii, Kenya</span>
                                </h3>
                                <span class="text-xs text-gray-500">Cloudy with showers</span>
                            </div>
                            <div class="flex items-center space-x-1 bg-gray-200 dark:bg-gray-800 rounded px-1.5 py-0.5 text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                <button @click="if (tempUnit === 'F') { currentTemp = 24; high = 27; low = 18; tempUnit = 'C'; }" :class="{ 'text-gray-900 dark:text-white': tempUnit === 'C' }">°C</button>
                                <span>/</span>
                                <button @click="if (tempUnit === 'C') { currentTemp = 75; high = 81; low = 64; tempUnit = 'F'; }" :class="{ 'text-gray-900 dark:text-white': tempUnit === 'F' }">°F</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-4">
                            <div class="flex items-center space-x-4">
                                <span class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter" x-text="currentTemp + '°'">24°</span>
                                <div class="text-[10px] text-gray-500 font-medium">
                                    <div>High: <span x-text="high + '°'">27°</span></div>
                                    <div>Low: <span x-text="low + '°'">18°</span></div>
                                </div>
                            </div>
                            <div class="text-xs text-right text-gray-500">
                                <div>Humidity: 82%</div>
                                <div>Wind: 12 km/h</div>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Stories List -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-2">
                            Latest News
                        </h3>
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($latestArticles as $latest)
                                <div class="py-3 first:pt-0 last:pb-0 group">
                                    <div class="flex items-center justify-between text-[10px] mb-1">
                                        <span class="text-[#C8102E] font-bold uppercase">{{ $latest->category->name }}</span>
                                        <span class="text-gray-400 font-medium">{{ $latest->published_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition">
                                        <a href="/articles/{{ $latest->slug }}">{{ $latest->title }}</a>
                                    </h4>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @include('partials.sidebar-widgets')
                </div>
            </div>

        @elseif($layout === 'visual')
            <!-- 2. VISUAL / MASONRY LAYOUT VARIANT -->
            <div class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Large spotlight cards grid -->
                    <div class="lg:col-span-2 space-y-6">
                        <h2 class="text-sm font-black border-b-2 border-gray-900 dark:border-white pb-2 text-gray-900 dark:text-white uppercase tracking-wider">Visual Spotlight</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($featuredArticle)
                                <article class="md:col-span-2 relative aspect-[16/10] overflow-hidden rounded-lg group shadow-md border border-gray-200 dark:border-gray-800 bg-gray-950">
                                    <img src="{{ $featuredArticle->featured_image }}" alt="{{ $featuredArticle->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-101 opacity-85 group-hover:opacity-75 transition duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-transparent flex flex-col justify-end p-6 space-y-1">
                                        <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest">{{ $featuredArticle->category->name }}</span>
                                        <h3 class="text-xl sm:text-2xl font-serif font-black text-white leading-tight">
                                            <a href="/articles/{{ $featuredArticle->slug }}">{{ $featuredArticle->title }}</a>
                                        </h3>
                                        <p class="text-xs text-gray-300 line-clamp-2 leading-relaxed">{{ $featuredArticle->subtitle }}</p>
                                    </div>
                                </article>
                            @endif

                            @foreach($topStories as $story)
                                <article class="relative aspect-[16/10] overflow-hidden rounded-lg group shadow-sm border border-gray-250 dark:border-gray-800 bg-gray-950">
                                    <img src="{{ $story->featured_image }}" alt="{{ $story->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 opacity-85 group-hover:opacity-75 transition duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent flex flex-col justify-end p-4 space-y-1">
                                        <span class="text-[9px] font-bold text-red-500 uppercase tracking-widest">{{ $story->category->name }}</span>
                                        <h3 class="text-sm font-serif font-bold text-white leading-tight line-clamp-2">
                                            <a href="/articles/{{ $story->slug }}">{{ $story->title }}</a>
                                        </h3>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="space-y-8">
                        <!-- Weather Widget -->
                        <div class="bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5" x-data="{ tempUnit: 'C', currentTemp: 24, high: 27, low: 18 }">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-800">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center space-x-1.5">
                                        <svg class="h-4.5 w-4.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Kisii, Kenya</span>
                                    </h3>
                                    <span class="text-xs text-gray-500">Cloudy with showers</span>
                                </div>
                                <div class="flex items-center space-x-1 bg-gray-200 dark:bg-gray-800 rounded px-1.5 py-0.5 text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                    <button @click="if (tempUnit === 'F') { currentTemp = 24; high = 27; low = 18; tempUnit = 'C'; }" :class="{ 'text-gray-900 dark:text-white': tempUnit === 'C' }">°C</button>
                                    <span>/</span>
                                    <button @click="if (tempUnit === 'C') { currentTemp = 75; high = 81; low = 64; tempUnit = 'F'; }" :class="{ 'text-gray-900 dark:text-white': tempUnit === 'F' }">°F</button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-4">
                                <div class="flex items-center space-x-4">
                                    <span class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter" x-text="currentTemp + '°'">24°</span>
                                    <div class="text-[10px] text-gray-500 font-medium">
                                        <div>High: <span x-text="high + '°'">27°</span></div>
                                        <div>Low: <span x-text="low + '°'">18°</span></div>
                                    </div>
                                </div>
                                <div class="text-xs text-right text-gray-500">
                                    <div>Humidity: 82%</div>
                                    <div>Wind: 12 km/h</div>
                                </div>
                            </div>
                        </div>

                        <!-- Latest Stories List -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-2">
                                Latest News
                            </h3>
                            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($latestArticles as $latest)
                                    <div class="py-3 first:pt-0 last:pb-0 group">
                                        <div class="flex items-center justify-between text-[10px] mb-1">
                                            <span class="text-[#C8102E] font-bold uppercase">{{ $latest->category->name }}</span>
                                            <span class="text-gray-400 font-medium">{{ $latest->published_at->diffForHumans() }}</span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition">
                                            <a href="/articles/{{ $latest->slug }}">{{ $latest->title }}</a>
                                        </h4>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @include('partials.sidebar-widgets')
                    </div>
                </div>
            </div>

        @else
            <!-- 3. STANDARD LAYOUT VARIANT (DEFAULT AL JAZEERA STYLE) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- COLUMN 1: Large Hero Spotlight + Live Updates -->
                <div class="space-y-6">
                    @if($featuredArticle)
                        <article class="group space-y-4">
                            <div class="aspect-video overflow-hidden rounded-t-lg bg-gray-105 dark:bg-gray-850 border border-gray-200 dark:border-gray-800">
                                <img src="{{ $featuredArticle->featured_image }}" alt="{{ $featuredArticle->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                            </div>
                            <div class="bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-850 p-5 rounded-b-lg shadow-sm -mt-6 relative z-10 space-y-3">
                                <div class="h-1 bg-[#FF7900] w-20 rounded-full"></div>
                                <h1 class="text-2xl font-serif font-black tracking-tight text-gray-900 dark:text-white leading-tight hover:text-[#C8102E] dark:hover:text-[#C8102E] transition">
                                    <a href="/articles/{{ $featuredArticle->slug }}">{{ $featuredArticle->title }}</a>
                                </h1>
                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                    {{ $featuredArticle->subtitle }}
                                </p>
                            </div>
                        </article>
                    @endif

                    <!-- Live Updates Timeline -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-850 p-5 rounded-lg shadow-sm space-y-4">
                        <h4 class="text-xs font-black uppercase text-[#C8102E] tracking-wider flex items-center border-b border-gray-100 dark:border-gray-800 pb-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#C8102E] animate-ping mr-2 inline-block"></span>
                            <span>Live Updates</span>
                        </h4>
                        <div class="border-l-2 border-gray-200 dark:border-gray-800 ml-1.5 pl-4 space-y-4 text-xs">
                            @foreach($latestArticles->take(3) as $latestItem)
                                <div class="relative">
                                    <span class="absolute w-2.5 h-2.5 rounded-full bg-[#FF7900] -left-[21.5px] top-1 border border-white dark:border-gray-900"></span>
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-[#FF7900] text-[10px]">{{ $latestItem->published_at->diffForHumans() }}:</span>
                                        <a href="/articles/{{ $latestItem->slug }}" class="hover:text-[#C8102E] text-gray-900 dark:text-gray-200 font-bold block leading-snug">{{ $latestItem->title }}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: Sub-featured + Small Lists with Thumbnails on the Right -->
                <div class="space-y-6">
                    @php
                        $subFeatured = $topStories->first();
                        $middleList = $topStories->skip(1)->concat($latestArticles->skip(3)->take(2));
                    @endphp

                    @if($subFeatured)
                        <article class="group space-y-3">
                            <div class="aspect-video overflow-hidden rounded bg-gray-105 dark:bg-gray-850 border border-gray-200 dark:border-gray-800">
                                <img src="{{ $subFeatured->featured_image }}" alt="{{ $subFeatured->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                            </div>
                            <h2 class="text-lg font-serif font-black text-gray-900 dark:text-white leading-snug hover:text-[#C8102E] dark:hover:text-[#C8102E] transition">
                                <a href="/articles/{{ $subFeatured->slug }}">{{ $subFeatured->title }}</a>
                            </h2>
                        </article>
                    @endif

                    <div class="divide-y divide-gray-150 dark:divide-gray-850 border-t border-gray-150 dark:border-gray-850 pt-2 space-y-4">
                        @foreach($middleList as $item)
                            <article class="flex justify-between items-start gap-4 pt-4 first:pt-0 group">
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-1.5 py-0.5 bg-red-100 text-[#C8102E] dark:bg-red-950/20 dark:text-red-400 text-[9px] font-bold rounded-sm uppercase tracking-wider">BREAKING</span>
                                        <span class="text-[9px] text-gray-400 font-medium">{{ $item->published_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition line-clamp-3">
                                        <a href="/articles/{{ $item->slug }}">{{ $item->title }}</a>
                                    </h3>
                                </div>
                                <div class="w-20 h-14 overflow-hidden rounded bg-gray-100 border border-gray-150 dark:border-gray-800 shrink-0">
                                    <img src="{{ $item->featured_image }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <!-- COLUMN 3: Categories & Special Sections Stacks -->
                <div class="space-y-8">
                    <!-- SECTION 1: Trending -->
                    <div class="space-y-3">
                        <h3 class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l-4 border-[#FF7900] pl-2 mb-3">Trending</h3>
                        <div class="divide-y divide-gray-150 dark:divide-gray-850">
                            @foreach($trendingArticles->take(3) as $index => $trending)
                                <article class="py-3 first:pt-0 group flex justify-between items-start gap-3">
                                    <div class="flex-grow space-y-1">
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] transition line-clamp-3">
                                            <a href="/articles/{{ $trending->slug }}">{{ $trending->title }}</a>
                                        </h4>
                                    </div>
                                    @if($index === 0)
                                        <div class="w-16 h-12 overflow-hidden rounded bg-gray-100 border border-gray-150 dark:border-gray-800 shrink-0">
                                            <img src="{{ $trending->featured_image }}" alt="{{ $trending->title }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <!-- SECTION 2: Must Read -->
                    <div class="space-y-3 pt-4 border-t border-gray-150 dark:border-gray-850">
                        <h3 class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l-4 border-[#FF7900] pl-2 mb-3">Must Read</h3>
                        <div class="divide-y divide-gray-150 dark:divide-gray-850">
                            @foreach($politicsArticles->take(3) as $index => $mustRead)
                                <article class="py-3 first:pt-0 group flex justify-between items-start gap-3">
                                    <div class="flex-grow space-y-1">
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] transition line-clamp-3">
                                            <a href="/articles/{{ $mustRead->slug }}">{{ $mustRead->title }}</a>
                                        </h4>
                                    </div>
                                    @if($index === 0)
                                        <div class="w-16 h-12 overflow-hidden rounded bg-gray-100 border border-gray-150 dark:border-gray-800 shrink-0">
                                            <img src="{{ $mustRead->featured_image }}" alt="{{ $mustRead->title }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <!-- SECTION 3: Opinion Columns -->
                    <div class="space-y-3 pt-4 border-t border-gray-150 dark:border-gray-850">
                        <h3 class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider border-l-4 border-[#FF7900] pl-2 mb-3">Opinion</h3>
                        <div class="divide-y divide-gray-150 dark:divide-gray-850">
                            @foreach($businessArticles->take(2) as $opinion)
                                <article class="py-3 first:pt-0 group flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-150 dark:bg-gray-800 shrink-0 overflow-hidden font-bold flex items-center justify-center text-xs text-gray-500 border border-gray-200 dark:border-gray-700">
                                        @if($opinion->user && $opinion->user->photo_url)
                                            <img src="{{ $opinion->user->photo_url }}" alt="{{ $opinion->user->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($opinion->user->name ?? 'A', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="space-y-0.5">
                                        <h4 class="text-xs font-serif font-black italic text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] transition">
                                            <a href="/articles/{{ $opinion->slug }}">"{{ $opinion->title }}"</a>
                                        </h4>
                                        <span class="text-[9px] text-gray-400 block font-medium">By {{ $opinion->user->name ?? 'Staff Writer' }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                    @include('partials.sidebar-widgets')
                </div>

        @endif
    </div>

        <!-- Section 3: Trending and Advertising Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-8 border-t border-gray-200 dark:border-gray-800">
            <!-- Trending Articles -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white border-b-2 border-[#C8102E] pb-2 inline-block">
                    Trending Stories
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($trendingArticles as $index => $trending)
                        <div class="flex space-x-4 items-start group">
                            <!-- Large Number -->
                            <div class="text-4xl font-serif font-black text-gray-250 dark:text-gray-800 w-10 text-center shrink-0 select-none leading-none">
                                {{ sprintf("%02d", $index + 1) }}
                            </div>
                            <!-- Title Block -->
                            <div class="flex-grow min-w-0">
                                <a href="/{{ $trending->category->slug }}" class="text-[9px] font-bold text-[#C8102E] dark:text-red-400 uppercase tracking-wider hover:underline block mb-1">
                                    {{ $trending->category->name }}
                                </a>
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition">
                                    <a href="/articles/{{ $trending->slug }}">{{ $trending->title }}</a>
                                </h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar Advertisement -->
            <div class="space-y-4">
                @if($sidebarAd)
                    <div class="w-full text-center">
                        <a href="{{ $sidebarAd->destination_url }}" target="_blank" class="inline-block relative group">
                            <img src="{{ $sidebarAd->image_url }}" alt="{{ $sidebarAd->title }}" class="mx-auto rounded max-w-full shadow-sm">
                            <span class="absolute top-1 left-1 bg-black/60 text-white text-[9px] px-1 rounded uppercase tracking-wider font-semibold">ADVERTISEMENT</span>
                        </a>
                    </div>
                @else
                    <div class="w-full bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 text-center py-16 rounded text-xs text-gray-400 font-medium tracking-wide">
                        SIDEBAR AD BANNER (300x250)
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 4: Live TV & Programme Schedule (Dark Mode Design Handoff) -->
        @php
            $tvUrl = \App\Models\Setting::get('live_tv_url', 'https://www.youtube.com/embed/5Peo-ivmupE');
        @endphp
        <div class="bg-gray-950 text-white rounded-lg p-6 sm:p-8 space-y-6 border border-gray-850">
            <div class="flex justify-between items-center border-b border-gray-850 pb-3">
                <h3 class="text-base font-black tracking-wider uppercase flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-ping inline-block"></span>
                    <span class="text-white font-serif">Getembe TV Live Coverage</span>
                </h3>
                <a href="/live-tv" class="text-xs font-semibold text-gray-400 hover:text-white transition flex items-center space-x-1">
                    <span>Full Screen TV</span>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- TV Stream Player -->
                <div class="lg:col-span-2 space-y-3">
                    <div class="aspect-video rounded-lg overflow-hidden bg-black relative border border-gray-800 shadow-2xl">
                        @if(Str::contains($tvUrl, 'youtube.com') || Str::contains($tvUrl, 'embed'))
                            <iframe src="{{ $tvUrl }}" title="Getembe Live TV" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        @else
                            <video controls autoplay class="w-full h-full">
                                <source src="{{ $tvUrl }}" type="application/x-mpegURL">
                                Your browser does not support HLS streaming.
                            </video>
                        @endif
                    </div>
                    <div class="bg-gray-900 p-4 border border-gray-850 rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div>
                            <span class="text-[9px] font-black text-red-500 uppercase tracking-widest block mb-0.5">Live Broadcast</span>
                            <h4 class="text-sm font-bold text-white">Currently Playing: <span class="text-[#FF7900]">News Hour Live</span></h4>
                            <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Broadcasting live from Getembe newsroom, Kisii, Kenya. Stay tuned for breaking bulletins, political debates, and regional briefs.</p>
                        </div>
                    </div>
                </div>

                <!-- Programme Schedule -->
                <div class="bg-gray-900 border border-gray-855 rounded-lg p-5 space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-wider text-white border-b border-gray-855 pb-2">
                        Today's Programme Schedule
                    </h3>
                    
                    <div class="space-y-4 text-[11px] overflow-y-auto max-h-[300px] scrollbar-none pr-1">
                        <div class="flex items-start space-x-3 text-gray-400">
                            <span class="font-bold w-20 shrink-0 text-gray-500">06:00 - 09:00</span>
                            <div>
                                <h4 class="font-bold text-gray-300">Getembe Morning Call</h4>
                                <p class="text-[10px] text-gray-500">Breakfast news and newspaper review.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 text-gray-400">
                            <span class="font-bold w-20 shrink-0 text-gray-500">09:00 - 12:00</span>
                            <div>
                                <h4 class="font-bold text-gray-300">Business Daily</h4>
                                <p class="text-[10px] text-gray-500">Economic trends, stock updates, and trade discussion.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 bg-red-950/20 border-l-2 border-red-600 pl-2 py-1">
                            <span class="font-bold w-20 shrink-0 text-red-500">12:00 - 14:00</span>
                            <div>
                                <h4 class="font-bold text-white flex items-center space-x-1.5">
                                    <span>News Hour Live</span>
                                    <span class="inline-block w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse"></span>
                                </h4>
                                <p class="text-[10px] text-gray-400">Midday headlines, market check, and regional briefs.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 text-gray-400">
                            <span class="font-bold w-20 shrink-0 text-gray-500">14:00 - 16:00</span>
                            <div>
                                <h4 class="font-bold text-gray-300">Health & Sports Highlights</h4>
                                <p class="text-[10px] text-gray-550">Wellness insights and sporting roundups.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 text-gray-400">
                            <span class="font-bold w-20 shrink-0 text-gray-500">16:00 - 19:00</span>
                            <div>
                                <h4 class="font-bold text-gray-300">Regional News Express</h4>
                                <p class="text-[10px] text-gray-555">Community spotlights and county assembly briefings.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 text-gray-400">
                            <span class="font-bold w-20 shrink-0 text-gray-500">19:00 - 21:00</span>
                            <div>
                                <h4 class="font-bold text-gray-300">Evening Prime Time News</h4>
                                <p class="text-[10px] text-gray-555">Comprehensive summary of major regional events.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Categories Grids (Al Jazeera Block Design) -->
        @php
            $sidebarAds = \App\Models\Advertisement::active()->location('sidebar')->get();
        @endphp
        <div class="space-y-12">
            @foreach($categoryBlocks as $index => $block)
                @if($block['articles']->isNotEmpty())
                    <div class="space-y-4">
                        <!-- Section Header -->
                        <div class="flex items-center space-x-2.5 border-b-2 border-gray-150 dark:border-gray-855 pb-2">
                            <span class="w-1.5 h-6 bg-[#FF7900] inline-block rounded-sm"></span>
                            <h2 class="text-lg font-serif font-black text-gray-900 dark:text-white uppercase tracking-tight flex items-center">
                                <span>{{ $block['category']->name }}</span>
                            </h2>
                        </div>

                        <!-- 3-Column Block Layout -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Left Column: 1 Large Spotlight -->
                            @php $first = $block['articles']->first(); @endphp
                            <article class="group space-y-3 lg:col-span-1">
                                <div class="aspect-video overflow-hidden rounded-lg bg-gray-105 dark:bg-gray-850 border border-gray-200 dark:border-gray-800 relative">
                                    <img src="{{ $first->featured_image }}" alt="{{ $first->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                                    <!-- Quote Overlay Icon (Matches Screenshot styling) -->
                                    <div class="absolute bottom-3 left-3 bg-black/85 text-white w-6 h-6 rounded-full flex items-center justify-center shadow-md select-none z-20">
                                        <span class="text-sm font-serif leading-none mt-1">“</span>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest block">{{ $block['category']->name }}</span>
                                    <h3 class="text-base font-bold font-serif text-gray-900 dark:text-white leading-tight group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition">
                                        <a href="/articles/{{ $first->slug }}">{{ $first->title }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-455 leading-relaxed line-clamp-2">
                                        {{ $first->subtitle }}
                                    </p>
                                </div>
                            </article>

                            <!-- Middle Column: 4 List Items with Thumbnail and Quote Overlays -->
                            <div class="lg:col-span-1 space-y-3">
                                @foreach($block['articles']->skip(1)->take(4) as $item)
                                    <article class="group flex items-start gap-4 pb-3 border-b border-gray-100 dark:border-gray-855 last:border-0 last:pb-0">
                                        <!-- Left Side: Tiny image thumbnail with quote overlay -->
                                        <div class="w-20 h-14 overflow-hidden rounded bg-gray-105 dark:bg-gray-850 shrink-0 border border-gray-200 dark:border-gray-800 relative">
                                            <img src="{{ $item->featured_image }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                            <div class="absolute bottom-1 left-1 bg-black/85 text-white w-4 h-4 rounded-full flex items-center justify-center shadow-md select-none z-20">
                                                <span class="text-[10px] font-serif leading-none mt-0.5">“</span>
                                            </div>
                                        </div>
                                        <div class="space-y-0.5 flex-grow min-w-0">
                                            <span class="text-[8px] font-black text-gray-500 dark:text-gray-455 uppercase tracking-widest block">{{ $block['category']->name }}</span>
                                            <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-red-400 transition line-clamp-2">
                                                <a href="/articles/{{ $item->slug }}">{{ $item->title }}</a>
                                            </h4>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <!-- Right Column: Premium Dynamic / Placeholder Advertisements -->
                            @php
                                $dbAd = $sidebarAds->isNotEmpty() && isset($sidebarAds[$index]) ? $sidebarAds[$index] : null;
                            @endphp
                            <div class="lg:col-span-1">
                                @if($dbAd)
                                    <a href="{{ $dbAd->destination_url }}" target="_blank" class="block w-full h-full relative group">
                                        <div class="bg-gray-100 dark:bg-gray-955 border border-gray-250 dark:border-gray-850 rounded-lg overflow-hidden h-full min-h-[220px] flex items-center justify-center relative">
                                            <img src="{{ $dbAd->image_url }}" alt="{{ $dbAd->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500">
                                            <span class="absolute top-2 left-2 bg-black/60 text-white text-[8px] px-1.5 py-0.5 rounded uppercase tracking-wider font-semibold z-10">ADVERTISEMENT</span>
                                        </div>
                                    </a>
                                @else
                                    @if($index % 4 === 0)
                                        <!-- Ad Variation 0: Kisii County Tourism -->
                                        <a href="/kisii" class="block w-full h-full relative group">
                                            <div class="relative bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg overflow-hidden h-full min-h-[220px] flex flex-col justify-end p-5">
                                                <img src="https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&q=80&w=400&h=300" alt="Visit Kisii" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-102 transition duration-500">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                                                <span class="absolute top-2 left-2 bg-black/60 text-white text-[8px] px-1.5 py-0.5 rounded uppercase tracking-wider font-semibold z-10">ADVERTISEMENT</span>
                                                <div class="relative z-10 space-y-1">
                                                    <span class="text-[9px] font-black text-yellow-500 uppercase tracking-widest">Explore Kenya</span>
                                                    <h4 class="text-sm font-serif font-black text-white leading-tight">Visit Kisii County</h4>
                                                    <p class="text-[10px] text-gray-300 leading-snug">Experience the legendary Soapstone hills, beautiful culture, and rich heritage of Southwestern Kenya.</p>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($index % 4 === 1)
                                        <!-- Ad Variation 1: Getembe News Mobile App -->
                                        <a href="/contact" class="block w-full h-full relative group">
                                            <div class="relative bg-gradient-to-br from-[#C8102E] to-red-950 border border-red-900 rounded-lg overflow-hidden h-full min-h-[220px] flex flex-col justify-between p-5">
                                                <span class="absolute top-2 left-2 bg-black/60 text-white text-[8px] px-1.5 py-0.5 rounded uppercase tracking-wider font-semibold z-10">ADVERTISEMENT</span>
                                                <div class="pt-2 flex justify-center">
                                                    <div class="flex items-center space-x-1.5 bg-white/10 px-3 py-1 rounded-full text-white text-[9px] font-bold">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span>Getembe News App</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <h4 class="text-sm font-black text-white leading-tight">Read Anywhere, Anytime</h4>
                                                    <p class="text-[10px] text-red-100 leading-snug">Read breaking stories on the go. Enable instant alerts to never miss news from Gusii region.</p>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($index % 4 === 2)
                                        <!-- Ad Variation 2: Advertise With Us -->
                                        <a href="/contact" class="block w-full h-full relative group">
                                            <div class="relative bg-gradient-to-br from-gray-900 to-black border border-gray-800 rounded-lg overflow-hidden h-full min-h-[220px] flex flex-col justify-between p-5">
                                                <span class="absolute top-2 left-2 bg-black/60 text-white text-[8px] px-1.5 py-0.5 rounded uppercase tracking-wider font-semibold z-10">ADVERTISEMENT</span>
                                                <div class="pt-2 flex justify-center">
                                                    <span class="bg-yellow-500 text-black font-extrabold text-[9px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">Grow Your Brand</span>
                                                </div>
                                                <div class="space-y-1">
                                                    <h4 class="text-sm font-black text-white leading-tight">Advertise With Us</h4>
                                                    <p class="text-[10px] text-gray-400 leading-snug">Reach over 1.2M active monthly visitors. Promote your business, services, or events across our digital network.</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <!-- Ad Variation 3: Getembe FM Radio -->
                                        <a href="/live-radio" class="block w-full h-full relative group">
                                            <div class="relative bg-gradient-to-br from-blue-900 via-indigo-950 to-gray-950 border border-indigo-900 rounded-lg overflow-hidden h-full min-h-[220px] flex flex-col justify-between p-5">
                                                <span class="absolute top-2 left-2 bg-black/60 text-white text-[8px] px-1.5 py-0.5 rounded uppercase tracking-wider font-semibold z-10">ADVERTISEMENT</span>
                                                <div class="pt-2 flex justify-center">
                                                    <div class="flex items-center space-x-1.5 bg-blue-500/20 px-3 py-1 rounded-full text-blue-300 text-[9px] font-bold animate-pulse">
                                                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                                                        <span>Getembe FM Live</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <h4 class="text-sm font-black text-white leading-tight">Listen to Getembe FM</h4>
                                                    <p class="text-[10px] text-blue-100 leading-snug">Enjoy dynamic talk shows, local Gusii music, cultural updates, and comprehensive live news reports.</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- App Download Promo Banner -->
        @include('partials.app-download-banner')

    </div>
</x-news-layout>

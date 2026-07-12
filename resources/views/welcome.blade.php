<x-news-layout>
    <!-- Homepage Main Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-10">

        <!-- Top Advertisement Banner -->
        @include('partials.render-ad', ['location' => 'top'])

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
                                        <span class="text-gray-500 dark:text-gray-400 font-normal">{{ $featuredArticle->published_at->diffForHumans() }}</span>
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
                                    <img src="{{ $story->featured_image }}" alt="{{ $story->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500" loading="lazy">
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <div class="flex items-center space-x-2 text-[9px] font-bold text-[#C8102E] uppercase">
                                        <span>{{ $story->category->name }}</span>
                                        <span class="text-gray-300 font-normal">&bull;</span>
                                        <span class="text-gray-500 dark:text-gray-400 font-normal">{{ $story->published_at->diffForHumans() }}</span>
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
                                        <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $latest->published_at->diffForHumans() }}</span>
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
                                    <img src="{{ $story->featured_image }}" alt="{{ $story->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 opacity-85 group-hover:opacity-75 transition duration-500" loading="lazy">
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
                                            <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $latest->published_at->diffForHumans() }}</span>
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
            @else
            <!-- 3. STANDARD LAYOUT VARIANT (VARIANT HIGH-FIDELITY REDESIGN) -->
            
            <!-- Hero Spotlight Grid (Matching Screenshot 1) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <!-- Left: Large Featured Article Card (Spans 2 columns) -->
                @if($featuredArticle)
                    <a href="/articles/{{ $featuredArticle->slug }}" class="lg:col-span-2 relative block aspect-[16/10] lg:aspect-auto lg:h-[450px] overflow-hidden rounded group bg-gray-950 shadow-md">
                        <img src="{{ $featuredArticle->featured_image }}" alt="{{ $featuredArticle->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition duration-500 opacity-90 group-hover:opacity-85">
                        <!-- Red category tag on top-left -->
                        <span class="absolute top-4 left-4 px-3 py-1 bg-[#C8102E] text-white text-[10px] font-black uppercase tracking-widest rounded shadow">
                            {{ $featuredArticle->category->name }}
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-transparent flex flex-col justify-end p-6 space-y-2">
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-serif font-black text-white leading-tight group-hover:text-red-400 transition break-words">
                                {{ $featuredArticle->title }}
                            </h2>
                            <div class="flex items-center space-x-3 text-[10px] text-gray-300 font-semibold">
                                @if($featuredArticle->author)
                                    <a href="/author/{{ $featuredArticle->author->id }}" class="hover:text-red-450 transition">{{ $featuredArticle->author->name }}</a>
                                @else
                                    <span>admin</span>
                                @endif
                                <span>&bull;</span>
                                <span>{{ $featuredArticle->published_at->format('M j, Y') }}</span>
                                <span>&bull;</span>
                                <span class="flex items-center space-x-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span>{{ $featuredArticle->comments->count() }}</span>
                                </span>
                                <span>&bull;</span>
                                <span class="flex items-center space-x-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>{{ number_format($featuredArticle->views_count) }}</span>
                                </span>
                            </div>
                        </div>
                        @if($featuredArticle->is_featured)
                            <span class="absolute top-4 right-4 bg-yellow-500 text-white p-1 rounded-full shadow-md z-20">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </span>
                        @endif
                    </a>
                @endif

                <!-- Right: 2x2 Grid of 4 spotlight cards (Spans 2 columns) -->
                <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($topStories->take(4) as $story)
                        <a href="/articles/{{ $story->slug }}" class="relative block aspect-[16/10] overflow-hidden rounded group bg-gray-950 shadow border border-gray-150 dark:border-gray-855">
                            <img src="{{ $story->featured_image }}" alt="{{ $story->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition duration-500 opacity-90 group-hover:opacity-85">
                            <!-- Category tag with custom color overlay -->
                            <span class="absolute top-3 left-3 px-2 py-0.5 bg-[#FF7900] text-white text-[9px] font-black uppercase tracking-wider rounded shadow">
                                {{ $story->category->name }}
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/25 to-transparent flex flex-col justify-end p-4 space-y-1.5">
                                <h3 class="text-xs sm:text-sm font-serif font-bold text-white leading-tight line-clamp-2 group-hover:text-red-400 transition break-words">
                                    {{ $story->title }}
                                </h3>
                                <div class="flex items-center space-x-2 text-[9px] text-gray-350">
                                    @if($story->author)
                                        <a href="/author/{{ $story->author->id }}" class="hover:text-red-400 transition">{{ $story->author->name }}</a>
                                    @else
                                        <span>staff</span>
                                    @endif
                                    <span>&bull;</span>
                                    <span>{{ $story->published_at->format('M j, Y') }}</span>
                                    <span>&bull;</span>
                                    <span class="flex items-center space-x-0.5">
                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>{{ number_format($story->views_count) }}</span>
                                    </span>
                                </div>
                            </div>
                            @if($story->is_featured)
                                <span class="absolute top-3 right-3 bg-yellow-500 text-white p-0.5 rounded-full shadow-md z-20">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Two-Column Main Feed Area (Matching Screenshot 2 & 3) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-8">
                
                <!-- LEFT 2/3 COLUMN: Categories blocks -->
                <div class="lg:col-span-2 space-y-10">
                    @foreach($categoryBlocks as $index => $block)
                        @if($block['articles']->isNotEmpty())
                            <div class="space-y-6">
                                <!-- Category Section Header tag -->
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-800 pb-2">
                                    <h2 class="text-xs font-black uppercase text-white bg-[#C8102E] px-3 py-1.5 tracking-wider inline-block">
                                        {{ $block['category']->name }}
                                    </h2>
                                    <!-- Right side subcategories tabs -->
                                    <div class="hidden sm:flex items-center space-x-3 text-[10px] font-bold text-gray-550 uppercase tracking-wide dark:text-gray-400 min-w-0 overflow-hidden">
                                        <span class="text-gray-900 dark:text-white cursor-pointer hover:text-[#C8102E] shrink-0">All</span>
                                        @foreach($block['articles']->take(2) as $fItem)
                                            <span class="hover:text-[#C8102E] cursor-pointer shrink-0">&bull;</span>
                                            <span class="hover:text-[#C8102E] cursor-pointer inline-block align-middle truncate max-w-[100px] sm:max-w-[150px] md:max-w-[220px]">{{ $fItem->title }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Sub Grid: 2 Medium Spotlight cards side-by-side -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    @foreach($block['articles']->take(2) as $medItem)
                                        <article class="group space-y-3">
                                            <a href="/articles/{{ $medItem->slug }}" class="block aspect-[16/10] overflow-hidden rounded bg-gray-105 dark:bg-gray-855 border border-gray-150 dark:border-gray-850 relative">
                                                <img src="{{ $medItem->featured_image }}" alt="{{ $medItem->title }}" class="w-full h-full object-cover group-hover:scale-101 transition duration-500" loading="lazy">
                                                @if($medItem->is_featured)
                                                    <span class="absolute top-2.5 right-2.5 bg-yellow-500 text-white p-0.5 rounded-full shadow-md z-10">
                                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    </span>
                                                @endif
                                            </a>
                                            <div class="space-y-1">
                                                <h3 class="text-sm font-bold font-serif text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] transition line-clamp-2">
                                                    <a href="/articles/{{ $medItem->slug }}">{{ $medItem->title }}</a>
                                                </h3>
                                                <div class="flex items-center space-x-2 text-[9px] text-gray-500 dark:text-gray-400 font-semibold">
                                                    @if($medItem->author)
                                                        <a href="/author/{{ $medItem->author->id }}" class="hover:text-[#C8102E] transition">{{ $medItem->author->name }}</a>
                                                    @else
                                                        <span>staff</span>
                                                    @endif
                                                    <span>&bull;</span>
                                                    <span>{{ $medItem->published_at->format('M j, Y') }}</span>
                                                    <span>&bull;</span>
                                                    <span class="flex items-center space-x-0.5">
                                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>{{ number_format($medItem->views_count) }}</span>
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-gray-550 dark:text-gray-400 leading-relaxed line-clamp-2">
                                                    {{ $medItem->subtitle }}
                                                </p>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>

                                <!-- Sub Grid: 2 Columns of row-based articles underneath (3 in col1, 3 in col2) -->
                                @php
                                    $remaining = $block['articles']->skip(2)->take(6);
                                    $col1 = $remaining->take(3);
                                    $col2 = $remaining->skip(3)->take(3);
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-100 dark:border-gray-855 pt-4">
                                    <!-- Column 1 -->
                                    <div class="space-y-3">
                                        @foreach($col1 as $item)
                                            <article class="group flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-gray-855 last:border-0 last:pb-0">
                                                <div class="w-20 h-14 overflow-hidden rounded bg-gray-105 dark:bg-gray-850 shrink-0 border border-gray-200 dark:border-gray-800 relative">
                                                    <img src="{{ $item->featured_image }}" alt="{{ $item->title }}" class="w-full h-full object-cover" loading="lazy">
                                                    @if($item->is_featured)
                                                        <span class="absolute top-1 right-1 bg-yellow-500 text-white p-0.5 rounded-full shadow z-10 scale-75">
                                                            <svg class="h-2 w-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="space-y-0.5 flex-grow min-w-0">
                                                    <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] transition line-clamp-2">
                                                        <a href="/articles/{{ $item->slug }}">{{ $item->title }}</a>
                                                    </h4>
                                                    <div class="flex items-center space-x-1.5 text-[8.5px] text-gray-500 dark:text-gray-400 font-semibold">
                                                        @if($item->author)
                                                            <a href="/author/{{ $item->author->id }}" class="hover:text-[#C8102E] transition">{{ $item->author->name }}</a>
                                                        @else
                                                            <span>staff</span>
                                                        @endif
                                                        <span>&bull;</span>
                                                        <span>{{ $item->published_at->format('M j, Y') }}</span>
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>

                                    <!-- Column 2 -->
                                    <div class="space-y-3">
                                        @foreach($col2 as $item)
                                            <article class="group flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-gray-855 last:border-0 last:pb-0">
                                                <div class="w-20 h-14 overflow-hidden rounded bg-gray-105 dark:bg-gray-850 shrink-0 border border-gray-200 dark:border-gray-800 relative">
                                                    <img src="{{ $item->featured_image }}" alt="{{ $item->title }}" class="w-full h-full object-cover" loading="lazy">
                                                    @if($item->is_featured)
                                                        <span class="absolute top-1 right-1 bg-yellow-500 text-white p-0.5 rounded-full shadow z-10 scale-75">
                                                            <svg class="h-2 w-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="space-y-0.5 flex-grow min-w-0">
                                                    <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] transition line-clamp-2">
                                                        <a href="/articles/{{ $item->slug }}">{{ $item->title }}</a>
                                                    </h4>
                                                    <div class="flex items-center space-x-1.5 text-[8.5px] text-gray-500 dark:text-gray-400 font-semibold">
                                                        @if($item->author)
                                                            <a href="/author/{{ $item->author->id }}" class="hover:text-[#C8102E] transition">{{ $item->author->name }}</a>
                                                        @else
                                                            <span>staff</span>
                                                        @endif
                                                        <span>&bull;</span>
                                                        <span>{{ $item->published_at->format('M j, Y') }}</span>
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- RIGHT 1/3 COLUMN: Sidebar with widgets -->
                <div class="lg:col-span-1 space-y-8">
                    
                    <!-- Sidebar Banner Ad (Custom blue box ad matching screenshot 2) -->
                    <div class="relative bg-gradient-to-br from-[#1E3A8A] to-[#1D4ED8] text-white rounded-lg p-6 flex flex-col justify-between border border-blue-900 shadow-md h-[220px]">
                        <span class="absolute top-2 left-2 bg-black/60 text-white text-[8px] px-1.5 py-0.5 rounded uppercase tracking-wider font-semibold z-10">ADVERTISEMENT</span>
                        <div class="space-y-1 pt-4">
                            <h3 class="text-lg font-black tracking-tight uppercase leading-none">VARIENT</h3>
                            <span class="text-[10px] text-blue-200 block font-bold">Best News & Magazine Script</span>
                        </div>
                        <div class="pt-6">
                            <a href="/contact" class="inline-block bg-white text-[#1D4ED8] hover:bg-gray-100 transition px-5 py-2 rounded text-xs font-black uppercase tracking-wider shadow">
                                BUY NOW
                            </a>
                        </div>
                    </div>

                    <!-- Popular Posts Widget -->
                    <div class="space-y-4">
                        <h3 class="bg-gray-900 text-white font-black text-xs uppercase px-4 py-2.5 tracking-wider">
                            Popular Posts
                        </h3>
                        <div class="space-y-3">
                            @foreach($trendingArticles->take(5) as $popItem)
                                <article class="group flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-gray-855 last:border-0 last:pb-0">
                                    <div class="w-20 h-14 overflow-hidden rounded bg-gray-105 dark:bg-gray-850 shrink-0 border border-gray-200 dark:border-gray-800 relative">
                                        <img src="{{ $popItem->featured_image }}" alt="{{ $popItem->title }}" class="w-full h-full object-cover" loading="lazy">
                                        @if($popItem->is_featured)
                                            <span class="absolute top-1 right-1 bg-yellow-500 text-white p-0.5 rounded-full shadow z-10 scale-75">
                                                <svg class="h-2 w-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-0.5 flex-grow min-w-0">
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] transition line-clamp-2">
                                            <a href="/articles/{{ $popItem->slug }}">{{ $popItem->title }}</a>
                                        </h4>
                                        <div class="flex items-center space-x-1.5 text-[8.5px] text-gray-500 dark:text-gray-400 font-semibold">
                                            @if($popItem->author)
                                                <a href="/author/{{ $popItem->author->id }}" class="hover:text-[#C8102E] transition">{{ $popItem->author->name }}</a>
                                            @else
                                                <span>admin</span>
                                            @endif
                                            <span>&bull;</span>
                                            <span>{{ $popItem->published_at->format('M j, Y') }}</span>
                                            <span>&bull;</span>
                                            <span class="flex items-center space-x-0.5">
                                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>{{ number_format($popItem->views_count) }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <!-- FOLLOW US Widget (Colored button layout grid matching Screenshot 3) -->
                    <div class="space-y-4">
                        <h3 class="bg-gray-900 text-white font-black text-xs uppercase px-4 py-2.5 tracking-wider">
                            Follow Us
                        </h3>
                        <div class="grid grid-cols-2 gap-2 text-[10px] font-black text-white">
                            @php
                                $fbStats = \App\Models\Setting::getStats('facebook', \App\Models\Setting::get('facebook'));
                                $twStats = \App\Models\Setting::getStats('twitter', \App\Models\Setting::get('twitter'));
                                $igStats = \App\Models\Setting::getStats('instagram', \App\Models\Setting::get('instagram'));
                                $ytStats = \App\Models\Setting::getStats('youtube', \App\Models\Setting::get('youtube'));
                                $waStats = \App\Models\Setting::getStats('whatsapp', \App\Models\Setting::get('whatsapp'));
                                $tkStats = \App\Models\Setting::getStats('tiktok', \App\Models\Setting::get('tiktok'));
                                $tgStats = \App\Models\Setting::getStats('telegram', \App\Models\Setting::get('telegram'));
                                $snapStats = \App\Models\Setting::getStats('snapchat', \App\Models\Setting::get('snapchat'));
                                $pinStats = \App\Models\Setting::getStats('pinterest', \App\Models\Setting::get('pinterest'));
                                $thStats = \App\Models\Setting::getStats('threads', \App\Models\Setting::get('threads'));
                            @endphp

                            <!-- X (Twitter) -->
                            @if(\App\Models\Setting::get('social_twitter_active', '1') == '1')
                            <a href="{{ $twStats['url'] }}" target="_blank" style="background-color: #000000;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">X (Twitter)</span>
                                <span class="text-[9px] font-medium text-gray-300 mt-0.5">{{ $twStats['formatted'] }} {{ $twStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- Instagram -->
                            @if(\App\Models\Setting::get('social_instagram_active', '1') == '1')
                            <a href="{{ $igStats['url'] }}" target="_blank" style="background-color: #E1306C;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">Instagram</span>
                                <span class="text-[9px] font-medium text-red-100 mt-0.5">{{ $igStats['formatted'] }} {{ $igStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- Facebook -->
                            @if(\App\Models\Setting::get('social_facebook_active', '1') == '1')
                            <a href="{{ $fbStats['url'] }}" target="_blank" style="background-color: #1877F2;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">Facebook</span>
                                <span class="text-[9px] font-medium text-blue-100 mt-0.5">{{ $fbStats['formatted'] }} {{ $fbStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- YouTube -->
                            @if(\App\Models\Setting::get('social_youtube_active', '1') == '1')
                            <a href="{{ $ytStats['url'] }}" target="_blank" style="background-color: #FF0000;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">YouTube</span>
                                <span class="text-[9px] font-medium text-red-100 mt-0.5">{{ $ytStats['formatted'] }} {{ $ytStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- WhatsApp -->
                            @if(\App\Models\Setting::get('social_whatsapp_active', '1') == '1')
                            <a href="{{ $waStats['url'] }}" target="_blank" style="background-color: #25D366;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">WhatsApp</span>
                                <span class="text-[9px] font-medium text-green-100 mt-0.5">{{ $waStats['formatted'] }} {{ $waStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- TikTok -->
                            @if(\App\Models\Setting::get('social_tiktok_active', '1') == '1')
                            <a href="{{ $tkStats['url'] }}" target="_blank" style="background-color: #111111;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">TikTok</span>
                                <span class="text-[9px] font-medium text-gray-300 mt-0.5">{{ $tkStats['formatted'] }} {{ $tkStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- Telegram -->
                            @if(\App\Models\Setting::get('social_telegram_active', '1') == '1')
                            <a href="{{ $tgStats['url'] }}" target="_blank" style="background-color: #26A69A;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">Telegram</span>
                                <span class="text-[9px] font-medium text-teal-100 mt-0.5">{{ $tgStats['formatted'] }} {{ $tgStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- Snapchat -->
                            @if(\App\Models\Setting::get('social_snapchat_active', '1') == '1')
                            <a href="{{ $snapStats['url'] }}" target="_blank" style="background-color: #FFFC00;" class="flex flex-col items-center justify-center hover:opacity-90 text-black transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase text-black">Snapchat</span>
                                <span class="text-[9px] font-medium text-gray-800 mt-0.5">{{ $snapStats['formatted'] }} {{ $snapStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- Pinterest -->
                            @if(\App\Models\Setting::get('social_pinterest_active', '1') == '1')
                            <a href="{{ $pinStats['url'] }}" target="_blank" style="background-color: #BD081C;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">Pinterest</span>
                                <span class="text-[9px] font-medium text-red-100 mt-0.5">{{ $pinStats['formatted'] }} {{ $pinStats['label'] }}</span>
                            </a>
                            @endif

                            <!-- Threads -->
                            @if(\App\Models\Setting::get('social_threads_active', '1') == '1')
                            <a href="{{ $thStats['url'] }}" target="_blank" style="background-color: #222222;" class="flex flex-col items-center justify-center hover:opacity-90 transition py-2 px-3 rounded shadow-sm text-center font-bold">
                                <span class="uppercase">Threads</span>
                                <span class="text-[9px] font-medium text-gray-300 mt-0.5">{{ $thStats['formatted'] }} {{ $thStats['label'] }}</span>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- RECOMMENDED POSTS Widget -->
                    <div class="space-y-4">
                        <h3 class="bg-gray-900 text-white font-black text-xs uppercase px-4 py-2.5 tracking-wider">
                            Recommended Posts
                        </h3>
                        @if($featuredArticle)
                            @php
                                $recItem = $latestArticles->first();
                            @endphp
                            @if($recItem)
                                <a href="/articles/{{ $recItem->slug }}" class="relative block aspect-[16/10] overflow-hidden rounded group bg-gray-950 shadow border border-gray-150 dark:border-gray-855">
                                    <img src="{{ $recItem->featured_image }}" alt="{{ $recItem->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition duration-500 opacity-90 group-hover:opacity-85" loading="lazy">
                                    <div class="absolute bottom-3 left-3 bg-[#C8102E] text-white text-[8px] font-black uppercase px-2 py-0.5 tracking-wider rounded z-20">
                                        {{ $recItem->category->name }}
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/20 to-transparent flex flex-col justify-end p-4 space-y-1">
                                        <h4 class="text-xs font-serif font-bold text-white leading-tight line-clamp-2 pt-4">
                                            {{ $recItem->title }}
                                        </h4>
                                    </div>
                                </a>
                            @endif
                        @endif
                    </div>

                    <!-- Reader Poll & Quizzes (Included from partials) -->
                    @include('partials.sidebar-widgets')

                </div>
            </div>

            <!-- Section 4: Live TV & Programme Schedule (Dark Mode Design Handoff) -->
            @if(\App\Models\Setting::get('live_tv_active', '1') == '1')
            @php
                $tvUrl = \App\Models\Setting::get('live_tv_url', 'https://www.youtube.com/embed/5Peo-ivmupE');
                $tvSchedule = \App\Models\Setting::get('tv_schedule', []);
                $currentDay = strtolower(now()->format('l'));
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $defaultTvFlat = [
                    ['time' => '06:00 AM - 09:00 AM', 'title' => 'Getembe Morning Call', 'desc' => 'Breakfast news and newspaper review.', 'is_playing' => false],
                    ['time' => '09:00 AM - 12:00 PM', 'title' => 'Business Daily', 'desc' => 'Economic trends, stock updates, and trade discussion.', 'is_playing' => false],
                    ['time' => '12:00 PM - 02:00 PM', 'title' => 'News Hour Live', 'desc' => 'Midday headlines, market check, and regional briefs.', 'is_playing' => true],
                    ['time' => '02:00 PM - 04:00 PM', 'title' => 'Health & Sports Highlights', 'desc' => 'Wellness insights and sporting roundups.', 'is_playing' => false],
                    ['time' => '04:00 PM - 07:00 PM', 'title' => 'Regional News Express', 'desc' => 'Community spotlights and county assembly briefings.', 'is_playing' => false],
                    ['time' => '07:00 PM - 09:00 PM', 'title' => 'Evening Prime Time News', 'desc' => 'Comprehensive summary of the day\'s major events.', 'is_playing' => false],
                    ['time' => '09:00 PM - 11:00 PM', 'title' => 'Late Night Spotlight', 'desc' => 'Documentary film showcases and talkshows.', 'is_playing' => false]
                ];
                if (!is_array($tvSchedule) || empty($tvSchedule)) {
                    $tvSchedule = array_fill_keys($days, $defaultTvFlat);
                } else {
                    $isGrouped = true;
                    foreach ($days as $day) {
                        if (!isset($tvSchedule[$day])) {
                            $isGrouped = false; break;
                        }
                    }
                    if (!$isGrouped) {
                        $tvSchedule = array_fill_keys($days, $tvSchedule);
                    }
                }
                $todaySchedule = $tvSchedule[$currentDay] ?? [];
                $currentlyPlayingShow = collect($todaySchedule)->firstWhere('is_playing', true) ?? [
                    'title' => 'Getembe Live Broadcast',
                    'desc' => 'Broadcasting live from Getembe newsroom, Kisii, Kenya. Stay tuned for breaking bulletins, political debates, and regional briefs.'
                ];
            @endphp
            <div class="bg-gray-950 text-white rounded-lg p-6 sm:p-8 space-y-6 border border-gray-850 mt-10">
                <div class="flex justify-between items-center border-b border-gray-850 pb-3">
                    <h3 class="text-base font-black tracking-wider uppercase flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-ping inline-block"></span>
                        <span class="text-white font-serif">Getembe TV Live Coverage</span>
                    </h3>
                    <a href="/tv" class="text-xs font-semibold text-gray-400 hover:text-white transition flex items-center space-x-1">
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
                        <div class="bg-gray-900 p-4 border border-gray-855 rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <span class="text-[9px] font-black text-red-500 uppercase tracking-widest block mb-0.5">Live Broadcast</span>
                                <h4 class="text-sm font-bold text-white">Currently Playing: <span class="text-[#FF7900]">{{ $currentlyPlayingShow['title'] }}</span></h4>
                                <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">{{ $currentlyPlayingShow['desc'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Programme Schedule -->
                    <div class="bg-gray-900 border border-gray-855 rounded-lg p-5 space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-wider text-white border-b border-gray-855 pb-2">
                            Today's Programme Schedule
                        </h3>
                        
                        <div class="space-y-4 text-[11px] overflow-y-auto max-h-[300px] scrollbar-none pr-1">
                            @forelse($todaySchedule as $item)
                                <div class="flex items-start space-x-3 {{ ($item['is_playing'] ?? false) ? 'bg-red-950/20 border-l-2 border-red-600 pl-2 py-1' : 'text-gray-400' }}">
                                    <span class="font-bold w-24 shrink-0 {{ ($item['is_playing'] ?? false) ? 'text-red-500' : 'text-gray-500' }}">{{ $item['time'] }}</span>
                                    <div>
                                        <h4 class="font-bold {{ ($item['is_playing'] ?? false) ? 'text-white flex items-center space-x-1.5' : 'text-gray-300' }}">
                                            <span>{{ $item['title'] }}</span>
                                            @if($item['is_playing'] ?? false)
                                                <span class="inline-block w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse"></span>
                                            @endif
                                        </h4>
                                        <p class="text-[10px] {{ ($item['is_playing'] ?? false) ? 'text-gray-400' : 'text-gray-550' }}">{{ $item['desc'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No programs scheduled for today.</p>
                            @endforelse
                    </div>
                </div>
                </div>
            </div>
            @endif
        @endif
        </div>

        <!-- App Download Promo Banner -->
        @include('partials.app-download-banner')

    </div>
</x-news-layout>

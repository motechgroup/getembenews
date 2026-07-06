<x-news-layout>
    <x-slot name="title">{{ $article->title }} - Getembe News</x-slot>
    <x-slot name="metaDescription">{{ $article->seo_description ?? Str::limit(strip_tags($article->body), 150) }}</x-slot>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6" x-data="{ copied: false }">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-gray-500 space-x-2 mb-6 uppercase tracking-wider font-semibold">
            <a href="/" class="hover:text-[#C8102E] transition">Home</a>
            <span>/</span>
            <a href="/category/{{ $article->category->slug }}" class="hover:text-[#C8102E] transition">{{ $article->category->name }}</a>
            <span>/</span>
            <span class="text-gray-400 dark:text-gray-600 truncate">{{ $article->title }}</span>
        </nav>

        <!-- Article Detail Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Side: Main Article Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Category Badge -->
                <a href="/category/{{ $article->category->slug }}" class="inline-block text-xs font-bold text-[#C8102E] uppercase hover:underline">
                    {{ $article->category->name }}
                </a>

                <!-- Headline -->
                <h1 class="text-3xl sm:text-4xl font-serif font-black tracking-tight text-gray-900 dark:text-white leading-tight">
                    {{ $article->title }}
                </h1>

                <!-- Subtitle -->
                @if($article->subtitle)
                    <p class="text-lg text-gray-650 dark:text-gray-400 font-medium leading-relaxed">
                        {{ $article->subtitle }}
                    </p>
                @endif

                <!-- Author, Date and Save button -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 border-y border-gray-100 dark:border-gray-800 gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center font-bold text-gray-600 dark:text-gray-400 overflow-hidden border border-gray-200 dark:border-gray-750">
                            @if($article->author->photo_url)
                                <img src="{{ $article->author->photo_url }}" alt="{{ $article->author->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($article->author->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $article->author->name }}</div>
                            <div class="text-xs text-gray-500">
                                <span>Published {{ $article->published_at->format('M j, Y \a\t g:i a') }}</span>
                                <span class="mx-1.5">&bull;</span>
                                <span>{{ $article->read_time }} min read</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bookmark Action -->
                    <livewire:save-article-button :article="$article" />
                </div>

                <!-- Featured Image -->
                @if($article->featured_image)
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-800">
                        <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Sharing & Toolbar -->
                <div class="flex items-center space-x-4 text-xs font-semibold text-gray-500 py-2 border-b border-gray-100 dark:border-gray-800">
                    <span>SHARE:</span>
                    <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="hover:text-[#C8102E] transition">Facebook</a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" target="_blank" class="hover:text-[#C8102E] transition">Twitter</a>
                    <button @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 2000)" class="hover:text-[#C8102E] transition flex items-center space-x-1">
                        <span x-text="copied ? 'Copied!' : 'Copy Link'">Copy Link</span>
                    </button>
                    <button onclick="window.print()" class="hover:text-[#C8102E] transition">Print</button>
                </div>

                <!-- Article Body (Content-First Typography) -->
                <div class="prose max-w-none dark:prose-invert prose-sm sm:prose-base leading-relaxed text-gray-800 dark:text-gray-200 space-y-4">
                    {!! $article->body !!}
                </div>

                <!-- Author Profile Card -->
                <div class="bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mt-10">
                    <div class="w-16 h-16 rounded-full bg-gray-250 dark:bg-gray-800 flex items-center justify-center font-bold text-gray-600 dark:text-gray-400 overflow-hidden shrink-0 border border-gray-250 dark:border-gray-750">
                        @if($article->author->photo_url)
                            <img src="{{ $article->author->photo_url }}" alt="{{ $article->author->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($article->author->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="space-y-2">
                        <div class="text-sm font-bold text-gray-900 dark:text-white">Written by {{ $article->author->name }}</div>
                        <p class="text-xs text-gray-650 dark:text-gray-400 leading-relaxed">
                            {{ $article->author->bio ?? 'Reporter and staff writer at Getembe News.' }}
                        </p>
                        @if($article->author->social_links)
                            <div class="flex space-x-3 text-[11px] font-bold text-gray-500">
                                @foreach($article->author->social_links as $platform => $url)
                                    <a href="{{ $url }}" target="_blank" class="hover:text-[#C8102E] uppercase">{{ $platform }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Comments Component -->
                <div class="pt-8 border-t border-gray-150 dark:border-gray-800 mt-10">
                    <livewire:comments-section :article="$article" />
                </div>

            </div>

            <!-- Right Side: Sidebar -->
            <div class="space-y-8">
                <!-- Sidebar Ad -->
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

                <!-- Related Articles -->
                <div class="space-y-4">
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 dark:text-white border-b-2 border-gray-900 dark:border-white pb-2">
                        Related Stories
                    </h3>
                    <div class="space-y-6">
                        @forelse($relatedArticles as $related)
                            <article class="space-y-2 group">
                                <div class="aspect-[16/10] overflow-hidden rounded bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-800">
                                    <img src="{{ $related->featured_image }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-102 transition duration-350">
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#C8102E] dark:group-hover:text-[#C8102E] transition">
                                        <a href="/articles/{{ $related->slug }}">{{ $related->title }}</a>
                                    </h4>
                                    <span class="text-[10px] text-gray-400">{{ $related->published_at->diffForHumans() }}</span>
                                </div>
                            </article>
                        @empty
                            <p class="text-gray-400 text-xs">No related stories available.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Getembe News Tips -->
                <div class="bg-[#C8102E]/5 border border-[#C8102E]/25 rounded-lg p-5 space-y-3">
                    <h4 class="text-sm font-bold text-[#C8102E] uppercase tracking-wide">Have a news tip?</h4>
                    <p class="text-xs text-gray-650 dark:text-gray-300 leading-relaxed">
                        Do you have an investigation, local story or news event you think we should cover? Contact our newsroom directly.
                    </p>
                    <a href="/contact" class="inline-block bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-3 py-1.5 rounded transition">
                        Submit Tip
                    </a>
                </div>

            </div>

        </div>

    </div>
</x-news-layout>

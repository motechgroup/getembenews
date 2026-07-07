<x-news-layout>
    <x-slot name="title">{{ $article->title }} - Getembe News</x-slot>
    <x-slot name="metaDescription">{{ $article->seo_description ?? Str::limit(strip_tags($article->body), 150) }}</x-slot>
    <x-slot name="metaImage">{{ $article->featured_image }}</x-slot>
    <x-slot name="metaUrl">{{ url()->current() }}</x-slot>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6" x-data="{ copied: false }">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-gray-500 space-x-2 mb-6 uppercase tracking-wider font-semibold">
            <a href="/" class="hover:text-[#C8102E] transition">Home</a>
            <span>/</span>
            <a href="/{{ $article->category->slug }}" class="hover:text-[#C8102E] transition">{{ $article->category->name }}</a>
            <span>/</span>
            <span class="text-gray-400 dark:text-gray-600 truncate">{{ $article->title }}</span>
        </nav>

        <!-- Article Detail Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Side: Main Article Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Category Badge -->
                <a href="/{{ $article->category->slug }}" class="inline-block text-xs font-bold text-[#C8102E] uppercase hover:underline">
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

                <!-- Sharing & Toolbar with Premium Social Icons -->
                <div class="flex flex-wrap items-center gap-2 py-3 border-b border-gray-100 dark:border-gray-800 text-xs font-bold text-gray-550 dark:text-gray-400">
                    <span class="mr-2 uppercase tracking-wider text-[10px]">Share:</span>
                    
                    <!-- Facebook Share Icon Button -->
                    <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" 
                       class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-600 text-blue-700 hover:text-white transition duration-200 transform hover:scale-105" title="Share on Facebook">
                        <svg class="h-4.5 w-4.5 fill-current" viewBox="0 0 24 24">
                            <path d="M9 8H7v3h2v9h4v-9h3.6l.4-3H13V6c0-.5.5-1 1-1h3V1h-4c-3 0-5 2-5 5v2z"/>
                        </svg>
                    </a>

                    <!-- Twitter / X Share Icon Button -->
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" target="_blank" 
                       class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-black hover:text-white text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white dark:hover:text-black transition duration-200 transform hover:scale-105" title="Share on X (Twitter)">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>

                    <!-- WhatsApp Share Icon Button -->
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' - ' . url()->current()) }}" target="_blank" 
                       class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 hover:bg-green-600 text-green-700 hover:text-white transition duration-200 transform hover:scale-105" title="Share on WhatsApp">
                        <svg class="h-4.5 w-4.5 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.968C16.634 1.97 14.161.945 11.536.945c-5.445 0-9.87 4.373-9.874 9.8.001 2.05.539 4.05 1.56 5.824l-1.02 3.722 3.829-1.002c1.724.94 3.447 1.44 5.126 1.44z"/>
                        </svg>
                    </a>

                    <!-- LinkedIn Share Icon Button -->
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" 
                       class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 hover:bg-blue-800 text-blue-600 hover:text-white transition duration-200 transform hover:scale-105" title="Share on LinkedIn">
                        <svg class="h-4.5 w-4.5 fill-current" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>

                    <!-- Copy Link Action Icon Button -->
                    <button @click="navigator.clipboard.writeText('{{ url()->current() }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-800 text-gray-700 hover:text-white dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white dark:hover:text-black transition duration-200 transform hover:scale-105" title="Copy Article URL">
                        <!-- Clipboard Icon -->
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                    </button>
                    
                    <!-- Print Action Icon Button -->
                    <button onclick="window.print()" 
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-500 text-gray-700 hover:text-white dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-200 dark:hover:text-black transition duration-200 transform hover:scale-105" title="Print Article">
                        <!-- Printer Icon -->
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </button>
                    
                    <span x-show="copied" x-transition class="text-[10px] text-green-600 bg-green-50 dark:bg-green-950/20 px-2 py-0.5 rounded border border-green-200 dark:border-green-900 ml-2" style="display: none;">Copied URL to Clipboard!</span>
                </div>

                <!-- Article Body (Content-First Typography) -->
                <div class="prose max-w-none dark:prose-invert prose-sm sm:prose-base leading-relaxed text-gray-800 dark:text-gray-200 space-y-4">
                    {!! $article->body !!}
                </div>

                <!-- Inline Article Body Advertisement -->
                @include('partials.render-ad', ['location' => 'inline'])

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
                    @include('partials.render-ad', ['location' => 'sidebar'])
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

        <!-- App Download Promo Banner -->
        @include('partials.app-download-banner')

    </div>
</x-news-layout>

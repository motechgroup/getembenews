<x-news-layout>
    <x-slot name="title">Articles by {{ $author->name }} - Getembe News</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
        
        <!-- Author Profile Card -->
        <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-800 rounded-xl p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-6 shadow-sm">
            <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center font-bold text-gray-600 dark:text-gray-400 overflow-hidden shrink-0 border-2 border-[#C8102E] shadow-sm">
                @if($author->photo_url)
                    <img src="{{ $author->photo_url }}" alt="{{ $author->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-3xl font-black text-gray-450 dark:text-gray-550">{{ strtoupper(substr($author->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="space-y-3 flex-grow">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 justify-center sm:justify-start">
                    <h1 class="text-2xl font-serif font-black tracking-tight text-gray-900 dark:text-white">
                        {{ $author->name }}
                    </h1>
                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-105 dark:bg-red-950/20 text-[#C8102E] w-max mx-auto sm:mx-0 border border-red-200 dark:border-red-900/30">
                        {{ $author->role ? $author->role : 'Author' }}
                    </span>
                </div>
                <p class="text-sm text-gray-650 dark:text-gray-400 leading-relaxed max-w-2xl">
                    {{ $author->bio ?? 'Reporter and staff writer at Getembe News.' }}
                </p>
                <div class="flex items-center justify-center sm:justify-start text-xs text-gray-500 gap-1.5 pt-1">
                    <span>📅</span>
                    <span>Member since {{ $author->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>

        <div class="border-b border-gray-200 dark:border-gray-800 pb-2">
            <h2 class="text-lg font-serif font-black tracking-tight text-gray-900 dark:text-white">
                Stories by this author
            </h2>
        </div>

        <div class="space-y-6">
            @forelse($articles as $post)
                <article class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-900 border border-gray-250 dark:border-gray-800 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    @if($post->featured_image)
                        <div class="sm:w-1/3 aspect-[16/10] overflow-hidden rounded bg-gray-50 shrink-0 border border-gray-100 dark:border-gray-800">
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="flex-grow flex flex-col justify-between space-y-2">
                        <div>
                            <span class="text-[9px] font-bold text-[#C8102E] uppercase">{{ $post->category->name }}</span>
                            <h3 class="text-base font-bold text-gray-955 dark:text-white mt-1 leading-snug hover:text-[#C8102E] transition break-words">
                                <a href="/articles/{{ $post->slug }}">{{ $post->title }}</a>
                            </h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed font-sans">
                                {{ $post->subtitle ?? Str::limit(strip_tags($post->body), 150) }}
                            </p>
                        </div>
                        <div class="text-[10px] text-gray-400 flex items-center justify-between pt-2">
                            <span>{{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}</span>
                            <span>{{ $post->read_time }} min read</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="py-16 text-center text-xs text-gray-400 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800">
                    This author hasn't published any articles yet.
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-news-layout>

<x-admin-layout>
    <x-slot name="title">Admin Dashboard - Getembe News</x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Articles -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-red-100 dark:bg-red-950/20 text-[#C8102E] rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 012 2v6a2 2 0 01-2 2h-2"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Articles</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ \App\Models\Article::count() }}</div>
            </div>
        </div>

        <!-- Total Views -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-950/20 text-blue-600 rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Page Views</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format(\App\Models\Article::sum('views_count')) }}</div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-green-100 dark:bg-green-950/20 text-green-600 rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Subscribers</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ \App\Models\User::count() }}</div>
            </div>
        </div>

        <!-- Total Comments -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-purple-100 dark:bg-purple-950/20 text-purple-600 rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Comments</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ \App\Models\Comment::count() }}</div>
            </div>
        </div>

    </div>

    <!-- Announcement & Agent Commercial Stats Grid -->
    <h2 class="text-[10px] font-black uppercase text-gray-400 tracking-wider mb-3">Announcements & Agent Performance</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Announcements -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-orange-100 dark:bg-orange-950/20 text-[#cc6c3b] rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Announcements</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ \App\Models\Announcement::count() }}</div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-green-100 dark:bg-green-950/20 text-green-600 rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 8H7m5 8h5M5 12a7 7 0 1114 0 7 7 0 01-14 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Paid Revenue</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">KSh {{ number_format(\App\Models\Announcement::where('payment_status', 'paid')->sum('total_amount')) }}</div>
            </div>
        </div>

        <!-- Pending Moderation -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-yellow-100 dark:bg-yellow-950/20 text-yellow-600 rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Pending Approval</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ \App\Models\Announcement::where('payment_status', 'paid')->where('is_approved', false)->count() }}</div>
            </div>
        </div>

        <!-- Active Agents -->
        <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-indigo-100 dark:bg-indigo-950/20 text-indigo-600 rounded-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Active Agents</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white">{{ \App\Models\Agent::count() }}</div>
            </div>
        </div>

    </div>

    <!-- Details Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Recent Articles -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-150 dark:border-gray-800 pb-2">
                Recent Articles
            </h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach(\App\Models\Article::orderBy('created_at', 'desc')->take(5)->get() as $art)
                    <div class="py-3 flex justify-between items-center text-xs">
                        <div class="space-y-0.5 pr-4">
                            <h4 class="font-bold text-gray-900 dark:text-white truncate max-w-[150px]">
                                <a href="/articles/{{ $art->slug }}" target="_blank" class="hover:underline">{{ $art->title }}</a>
                            </h4>
                            <p class="text-[10px] text-gray-400">By {{ $art->author->name }} &bull; {{ $art->category->name }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded font-bold uppercase text-[9px] {{ $art->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-950/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/20 dark:text-yellow-400' }}">
                            {{ $art->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-150 dark:border-gray-800 pb-2">
                Recent Comments
            </h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse(\App\Models\Comment::orderBy('created_at', 'desc')->take(5)->get() as $comment)
                    <div class="py-3 text-xs space-y-1">
                        <div class="flex justify-between items-center text-[10px] text-gray-400">
                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $comment->user->name }}</span>
                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                            {{ $comment->body }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-6">No comments posted yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-150 dark:border-gray-800 pb-2">
                Recent Announcements
            </h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse(\App\Models\Announcement::orderBy('created_at', 'desc')->take(5)->get() as $ann)
                    <div class="py-3 flex justify-between items-center text-xs">
                        <div class="space-y-0.5 pr-4">
                            <h4 class="font-bold text-gray-900 dark:text-white truncate max-w-[130px]">
                                {{ $ann->visitor_name }}
                            </h4>
                            <p class="text-[10px] text-gray-400">
                                {{ $ann->created_at->diffForHumans() }} &bull; {{ strtoupper($ann->media) }}
                            </p>
                        </div>
                        <div class="text-right space-y-1">
                            <div class="font-bold text-gray-900 dark:text-white">KSh {{ number_format($ann->total_amount) }}</div>
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase {{ $ann->payment_status === 'paid' ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-750 dark:bg-red-950/20 dark:text-red-400' }}">
                                {{ $ann->payment_status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-6">No announcements submitted yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</x-admin-layout>

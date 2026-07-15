<x-admin-layout>
    <x-slot name="title">Dashboard - Getembe News</x-slot>

    @php
        $earningsEnabled = (bool) \App\Models\Setting::get('earnings_enabled', true);
        $minArticles = (int) \App\Models\Setting::get('earnings_min_articles', 5);
        $minViews = (int) \App\Models\Setting::get('earnings_min_views', 1000);

        $myArticlesCount = \App\Models\Article::where('user_id', auth()->id())->count();
        $myViewsCount = \App\Models\Article::where('user_id', auth()->id())->sum('views_count');

        $isEligible = ($myArticlesCount >= $minArticles) && ($myViewsCount >= $minViews);

        $rewardRate = (float) \App\Models\Setting::get('author_reward_rate', '0.10');
        $totalEarnings = $isEligible ? ($myViewsCount * $rewardRate) : 0.0;
        $currencySymbol = \App\Models\Setting::get('currency_symbol', 'KSh');
    @endphp

    @if(auth()->user()->isAdmin())
        <!-- ================= ADMIN DASHBOARD ================= -->
        <h2 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-4">Site-Wide Executive Metrics</h2>
        
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

    @elseif(auth()->user()->isEditor())
        <!-- ================= EDITOR DASHBOARD ================= -->
        <h2 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-4">Editorial Content Performance</h2>
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
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4 relative group">
                <div class="p-3 bg-blue-100 dark:bg-blue-950/20 text-blue-600 rounded-md">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Page Views</div>
                        <!-- Tooltip -->
                        <div class="relative group cursor-help ml-2">
                            <span class="text-[10px] text-gray-400 hover:text-gray-650 bg-gray-100 dark:bg-gray-800 w-4 h-4 rounded-full flex items-center justify-center font-bold">?</span>
                            <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] rounded-lg shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-10 font-normal leading-relaxed">
                                Site-wide verified views represent unique reader visits filtered by our anti-cheat system.
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format(\App\Models\Article::sum('views_count')) }}</div>
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

            <!-- My Earnings / Eligibility Status -->
            @if(!$earningsEnabled)
                <!-- System Disabled message -->
                <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
                    <div class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">My Earnings</div>
                        <div class="text-xs font-bold text-gray-650 dark:text-gray-400 mt-2">Earning system is currently disabled by administrator.</div>
                    </div>
                </div>
            @elseif(!$isEligible)
                <!-- Eligibility Progress Card -->
                <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm relative group bg-gradient-to-br from-amber-50/10 to-white dark:from-amber-950/5 dark:to-gray-900 flex flex-col justify-between">
                    <div class="flex items-start space-x-3.5">
                        <div class="p-2 bg-amber-100 dark:bg-amber-950/30 text-amber-600 dark:text-amber-450 rounded-md shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Earnings Eligibility</div>
                            <div class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 mt-1.5 leading-relaxed">
                                You need <span class="text-amber-600 dark:text-amber-400 font-extrabold">{{ $minArticles }} articles</span> and <span class="text-amber-600 dark:text-amber-400 font-extrabold">{{ number_format($minViews) }} total views</span> to start earning.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Stats -->
                    <div class="mt-3 space-y-2 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                        <div class="flex justify-between items-center">
                            <span>Articles: {{ $myArticlesCount }} / {{ $minArticles }}</span>
                            <span class="text-gray-950 dark:text-white font-mono text-[9px]">{{ min(100, round(($myArticlesCount / max(1, $minArticles)) * 100)) }}%</span>
                        </div>
                        <div class="w-full bg-gray-150 dark:bg-gray-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, ($myArticlesCount / max(1, $minArticles)) * 100) }}%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-1">
                            <span>Views: {{ number_format($myViewsCount) }} / {{ number_format($minViews) }}</span>
                            <span class="text-gray-950 dark:text-white font-mono text-[9px]">{{ min(100, round(($myViewsCount / max(1, $minViews)) * 100)) }}%</span>
                        </div>
                        <div class="w-full bg-gray-150 dark:bg-gray-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, ($myViewsCount / max(1, $minViews)) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <!-- My Earnings Card (Regular) -->
                <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4 relative group bg-gradient-to-br from-green-50/20 to-white dark:from-green-950/5 dark:to-gray-900">
                    <div class="p-3 bg-green-100 dark:bg-green-950/20 text-green-600 rounded-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 8H7m5 8h5M5 12a7 7 0 1114 0 7 7 0 01-14 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">My Earnings</span>
                            <!-- Tooltip -->
                            <div class="relative group cursor-help ml-2">
                                <span class="text-[10px] text-gray-400 hover:text-gray-650 bg-gray-100 dark:bg-gray-800 w-4 h-4 rounded-full flex items-center justify-center font-bold">?</span>
                                <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] rounded-lg shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-10 font-normal leading-relaxed">
                                    Accumulated reward amount calculated as: My Personal Page Views × Current Reward Rate ({{ $currencySymbol }} {{ number_format($rewardRate, 2) }} per view).
                                </div>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-green-600 dark:text-green-400 mt-1">{{ $currencySymbol }} {{ number_format($totalEarnings, 2) }}</div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Creator Earnings Tips & Guidelines (Do's & Don'ts) -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-250 dark:border-gray-800 overflow-hidden mb-8">
            <div class="p-5 border-b border-gray-150 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/10 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center">
                        <span class="mr-2">💡</span> Creator Tips & Guidelines (Do's & Don'ts)
                    </h4>
                    <p class="text-[10px] text-gray-450 dark:text-gray-500 mt-1">Learn how to maximize your story reach and ensure all views qualify for rewards.</p>
                </div>
                <span class="text-[10px] font-black text-[#C8102E] bg-red-50 dark:bg-red-950/20 px-2 py-0.5 rounded uppercase">Manual</span>
            </div>
            
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-gray-150 dark:divide-gray-800">
                <!-- DO's Column -->
                <div class="space-y-3 pr-0 md:pr-6">
                    <h5 class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider flex items-center">
                        <span class="mr-1.5">👍</span> The Do's (Maximize Reach)
                    </h5>
                    <ul class="space-y-2.5 text-[11px] leading-relaxed">
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Write Engaging Headlines:</strong>
                                <p class="text-gray-500 mt-0.5">Use clear, factual, and captivating titles to trigger interest on social media feeds.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Embed High-Quality Media:</strong>
                                <p class="text-gray-500 mt-0.5">Incorporate images, audio clips, and event schedules to increase reader stay time (dwell time).</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Share on Social Channels:</strong>
                                <p class="text-gray-500 mt-0.5">Distribute your articles in local Facebook groups, Twitter/X threads, and WhatsApp news circles.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Respond to Reader Comments:</strong>
                                <p class="text-gray-500 mt-0.5">Build a community by answering questions in your article comments to invite repeat views.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- DON'Ts Column -->
                <div class="space-y-3 pt-4 md:pt-0 pl-0 md:pl-6 border-gray-150 dark:border-gray-800">
                    <h5 class="text-xs font-bold text-red-650 dark:text-red-400 uppercase tracking-wider flex items-center">
                        <span class="mr-1.5">⚠️</span> The Don'ts (Avoid Violations)
                    </h5>
                    <ul class="space-y-2.5 text-[11px] leading-relaxed">
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Strictly No Clickbait:</strong>
                                <p class="text-gray-500 mt-0.5">Deceptive headings or fake summaries violate editor guidelines and can trigger article rejection.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">No Auto-Refresh or Bot Traffic:</strong>
                                <p class="text-gray-500 mt-0.5">Our anti-cheat engine monitors user-agent behaviors and discards rapid clicks and non-human traffic.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">No Plagiarism:</strong>
                                <p class="text-gray-500 mt-0.5">Re-publishing copied articles without original input is prohibited and leads to account suspension.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">No Artificial Click Rings:</strong>
                                <p class="text-gray-500 mt-0.5">Participating in paid view networks or swap groups will void all earned rewards on your posts.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Articles -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-150 dark:border-gray-800 pb-2">
                    Recent Articles
                </h3>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach(\App\Models\Article::orderBy('created_at', 'desc')->take(5)->get() as $art)
                        <div class="py-3 flex justify-between items-center text-xs">
                            <div class="space-y-0.5 pr-4">
                                <h4 class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]">
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
                            <p class="text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed font-sans">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-6">No comments posted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

    @elseif(auth()->user()->isManager())
        <!-- ================= MANAGER DASHBOARD ================= -->
        <h2 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-4">Announcements & Transactions Dashboard</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            
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

        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-150 dark:border-gray-800 pb-2">
                Recent Announcements
            </h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse(\App\Models\Announcement::orderBy('created_at', 'desc')->take(10)->get() as $ann)
                    <div class="py-3 flex justify-between items-center text-xs">
                        <div class="space-y-0.5 pr-4">
                            <h4 class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]">{{ $ann->visitor_name }}</h4>
                            <p class="text-[10px] text-gray-400">{{ $ann->created_at->diffForHumans() }} &bull; {{ strtoupper($ann->media) }}</p>
                        </div>
                        <div class="text-right space-y-1">
                            <div class="font-bold text-gray-900 dark:text-white">KSh {{ number_format($ann->total_amount) }}</div>
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase {{ $ann->payment_status === 'paid' ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-750 dark:bg-red-950/20 dark:text-red-400' }}">{{ $ann->payment_status }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-6">No announcements submitted yet.</p>
                @endforelse
            </div>
        </div>

    @else
        <!-- ================= AUTHOR / WRITER DASHBOARD ================= -->
        <h2 class="text-xs font-black uppercase text-gray-400 tracking-wider mb-4">My Personal Content Performance</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Articles -->
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-red-100 dark:bg-red-950/20 text-[#C8102E] rounded-md">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 012 2v6a2 2 0 01-2 2h-2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">My Articles</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white">{{ \App\Models\Article::where('user_id', auth()->id())->count() }}</div>
                </div>
            </div>

            <!-- Total Views -->
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4 relative group">
                <div class="p-3 bg-blue-100 dark:bg-blue-950/20 text-blue-600 rounded-md">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">My Page Views</div>
                        <!-- Tooltip -->
                        <div class="relative group cursor-help ml-2">
                            <span class="text-[10px] text-gray-400 hover:text-gray-650 bg-gray-100 dark:bg-gray-850 w-4 h-4 rounded-full flex items-center justify-center font-bold">?</span>
                            <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] rounded-lg shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-10 font-normal leading-relaxed">
                                Verified views represent unique reader visits filtered by our anti-cheat system (excluding repeat clicks, bots, and rapid refreshes).
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format(\App\Models\Article::where('user_id', auth()->id())->sum('views_count')) }}</div>
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
                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Reader Comments</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white">
                        {{ \App\Models\Comment::whereHas('article', function($q) { $q->where('user_id', auth()->id()); })->count() }}
                    </div>
                </div>
            </div>

            <!-- My Earnings / Eligibility Status -->
            @if(!$earningsEnabled)
                <!-- System Disabled message -->
                <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4">
                    <div class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">My Earnings</div>
                        <div class="text-xs font-bold text-gray-650 dark:text-gray-400 mt-2">Earning system is currently disabled by administrator.</div>
                    </div>
                </div>
            @elseif(!$isEligible)
                <!-- Eligibility Progress Card -->
                <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm relative group bg-gradient-to-br from-amber-50/10 to-white dark:from-amber-950/5 dark:to-gray-900 flex flex-col justify-between">
                    <div class="flex items-start space-x-3.5">
                        <div class="p-2 bg-amber-100 dark:bg-amber-950/30 text-amber-600 dark:text-amber-450 rounded-md shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Earnings Eligibility</div>
                            <div class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 mt-1.5 leading-relaxed">
                                You need <span class="text-amber-600 dark:text-amber-400 font-extrabold">{{ $minArticles }} articles</span> and <span class="text-amber-600 dark:text-amber-400 font-extrabold">{{ number_format($minViews) }} total views</span> to start earning.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Stats -->
                    <div class="mt-3 space-y-2 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                        <div class="flex justify-between items-center">
                            <span>Articles: {{ $myArticlesCount }} / {{ $minArticles }}</span>
                            <span class="text-gray-950 dark:text-white font-mono text-[9px]">{{ min(100, round(($myArticlesCount / max(1, $minArticles)) * 100)) }}%</span>
                        </div>
                        <div class="w-full bg-gray-150 dark:bg-gray-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, ($myArticlesCount / max(1, $minArticles)) * 100) }}%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-1">
                            <span>Views: {{ number_format($myViewsCount) }} / {{ number_format($minViews) }}</span>
                            <span class="text-gray-950 dark:text-white font-mono text-[9px]">{{ min(100, round(($myViewsCount / max(1, $minViews)) * 100)) }}%</span>
                        </div>
                        <div class="w-full bg-gray-150 dark:bg-gray-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, ($myViewsCount / max(1, $minViews)) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <!-- My Earnings Card (Regular) -->
                <div class="bg-white dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm flex items-center space-x-4 relative group bg-gradient-to-br from-green-50/20 to-white dark:from-green-950/5 dark:to-gray-900">
                    <div class="p-3 bg-green-100 dark:bg-green-950/20 text-green-600 rounded-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 8H7m5 8h5M5 12a7 7 0 1114 0 7 7 0 01-14 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">My Earnings</span>
                            <!-- Tooltip -->
                            <div class="relative group cursor-help ml-2">
                                <span class="text-[10px] text-gray-400 hover:text-gray-650 bg-gray-100 dark:bg-gray-800 w-4 h-4 rounded-full flex items-center justify-center font-bold">?</span>
                                <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] rounded-lg shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-10 font-normal leading-relaxed">
                                    Accumulated reward amount calculated as: My Personal Page Views × Current Reward Rate ({{ $currencySymbol }} {{ number_format($rewardRate, 2) }} per view).
                                </div>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-green-600 dark:text-green-400 mt-1">{{ $currencySymbol }} {{ number_format($totalEarnings, 2) }}</div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Creator Earnings Tips & Guidelines (Do's & Don'ts) -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-250 dark:border-gray-800 overflow-hidden mb-8">
            <div class="p-5 border-b border-gray-150 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/10 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center">
                        <span class="mr-2">💡</span> Creator Tips & Guidelines (Do's & Don'ts)
                    </h4>
                    <p class="text-[10px] text-gray-450 dark:text-gray-500 mt-1">Learn how to maximize your story reach and ensure all views qualify for rewards.</p>
                </div>
                <span class="text-[10px] font-black text-[#C8102E] bg-red-50 dark:bg-red-950/20 px-2 py-0.5 rounded uppercase">Manual</span>
            </div>
            
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-gray-150 dark:divide-gray-800">
                <!-- DO's Column -->
                <div class="space-y-3 pr-0 md:pr-6">
                    <h5 class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider flex items-center">
                        <span class="mr-1.5">👍</span> The Do's (Maximize Reach)
                    </h5>
                    <ul class="space-y-2.5 text-[11px] leading-relaxed">
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Write Engaging Headlines:</strong>
                                <p class="text-gray-500 mt-0.5">Use clear, factual, and captivating titles to trigger interest on social media feeds.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Embed High-Quality Media:</strong>
                                <p class="text-gray-500 mt-0.5">Incorporate images, audio clips, and event schedules to increase reader stay time (dwell time).</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Share on Social Channels:</strong>
                                <p class="text-gray-500 mt-0.5">Distribute your articles in local Facebook groups, Twitter/X threads, and WhatsApp news circles.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Respond to Reader Comments:</strong>
                                <p class="text-gray-500 mt-0.5">Build a community by answering questions in your article comments to invite repeat views.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- DON'Ts Column -->
                <div class="space-y-3 pt-4 md:pt-0 pl-0 md:pl-6 border-gray-150 dark:border-gray-800">
                    <h5 class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider flex items-center">
                        <span class="mr-1.5">⚠️</span> The Don'ts (Avoid Violations)
                    </h5>
                    <ul class="space-y-2.5 text-[11px] leading-relaxed">
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">Strictly No Clickbait:</strong>
                                <p class="text-gray-500 mt-0.5">Deceptive headings or fake summaries violate editor guidelines and can trigger article rejection.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">No Auto-Refresh or Bot Traffic:</strong>
                                <p class="text-gray-500 mt-0.5">Our anti-cheat engine monitors user-agent behaviors and discards rapid clicks and non-human traffic.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">No Plagiarism:</strong>
                                <p class="text-gray-500 mt-0.5">Re-publishing copied articles without original input is prohibited and leads to account suspension.</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                            <div>
                                <strong class="text-gray-900 dark:text-white">No Artificial Click Rings:</strong>
                                <p class="text-gray-500 mt-0.5">Participating in paid view networks or swap groups will void all earned rewards on your posts.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-150 dark:border-gray-800 pb-2">
                My Recent Submissions
            </h3>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse(\App\Models\Article::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(10)->get() as $art)
                    <div class="py-3 flex justify-between items-center text-xs">
                        <div class="space-y-0.5 pr-4">
                            <h4 class="font-bold text-gray-900 dark:text-white truncate max-w-[300px]">
                                <a href="/articles/{{ $art->slug }}" target="_blank" class="hover:underline">{{ $art->title }}</a>
                            </h4>
                            <p class="text-[10px] text-gray-400">Published: {{ $art->created_at->format('M d, Y') }} &bull; Category: {{ $art->category->name }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded font-bold uppercase text-[9px] {{ $art->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-950/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/20 dark:text-yellow-400' }}">
                            {{ $art->status }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-6">You haven't written any articles yet.</p>
                @endforelse
            </div>
        </div>
    @endif
</x-admin-layout>

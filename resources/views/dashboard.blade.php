<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ auth()->user()->isStaff() ? __('Author Dashboard & Rewards') : __('User Dashboard & Member Stats') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(auth()->user()->isStaff())
                <!-- STAFF / AUTHOR / EDITOR DASHBOARD -->
                <!-- Welcome Alert Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold">Welcome back, {{ auth()->user()->name }}!</h3>
                            <p class="text-xs text-gray-500 mt-1">Here is the real-time status of your articles, audience reach, and earnings ledger.</p>
                        </div>
                        <span class="px-3 py-1 bg-[#C8102E] text-white text-xs font-black uppercase tracking-wider rounded shadow-sm">
                            Role: {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>

                <!-- Author Reward Module Widget Grid -->
                @php
                    $articlesCount = auth()->user()->articles()->count();
                    $verifiedViews = auth()->user()->articles()->sum('views_count');
                    $rewardRate = (float) \App\Models\Setting::get('author_reward_rate', '0.10');
                    $totalEarnings = $verifiedViews * $rewardRate;
                    $currencySymbol = \App\Models\Setting::get('currency_symbol', 'KSh');
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                    <!-- Card 1: Articles Published -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Articles Published</span>
                            <div class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $articlesCount }}</div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Total stories contributed to Getembe News.</p>
                    </div>

                    <!-- Card 2: Verified Human Views -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between relative group">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Verified Views</span>
                                <!-- Tooltip -->
                                <div class="relative group cursor-help">
                                    <span class="text-[10px] text-gray-400 hover:text-gray-650 bg-gray-100 dark:bg-gray-750 w-4 h-4 rounded-full flex items-center justify-center font-bold">?</span>
                                    <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] rounded-lg shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-10 font-normal leading-relaxed">
                                        Verified views represent unique reader visits filtered by our anti-cheat system (excluding repeat clicks, bots, and rapid refreshes).
                                    </div>
                                </div>
                            </div>
                            <div class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ number_format($verifiedViews) }}</div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Anti-cheat system filtered real reader views.</p>
                    </div>

                    <!-- Card 3: Accumulated Earnings -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between bg-gradient-to-br from-green-50/50 to-white dark:from-green-950/10 dark:to-gray-800 relative group">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider">Reward Earnings</span>
                                <!-- Tooltip -->
                                <div class="relative group cursor-help">
                                    <span class="text-[10px] text-green-500 hover:text-green-650 bg-green-100/50 dark:bg-green-950/40 w-4 h-4 rounded-full flex items-center justify-center font-bold">?</span>
                                    <div class="absolute right-0 bottom-full mb-2 w-48 p-2 bg-gray-900 text-white text-[10px] rounded-lg shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 z-10 font-normal leading-relaxed">
                                        Accumulated reward amount calculated as: Verified Views × Current Reward Rate. Payouts are updated monthly.
                                    </div>
                                </div>
                            </div>
                            <div class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-2">
                                {{ $currencySymbol }} {{ number_format($totalEarnings, 2) }}
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Accumulated payout balance from verified views.</p>
                    </div>

                    <!-- Card 4: Reward Rate -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Current Reward Rate</span>
                            <div class="text-sm font-bold text-gray-950 dark:text-white mt-4">
                                {{ $currencySymbol }} {{ number_format($rewardRate, 2) }} <span class="text-xs font-normal text-gray-400">per verified view</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Configured rate per valid behavior-verified view.</p>
                    </div>
                </div>

                <!-- Creator Earnings Tips & Guidelines -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-150 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/10 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center">
                                <span class="mr-2">💡</span> Creator Tips & Guidelines (Do's & Don'ts)
                            </h4>
                            <p class="text-[10px] text-gray-450 dark:text-gray-500 mt-1">Learn how to maximize your story reach and ensure all views qualify for rewards.</p>
                        </div>
                        <span class="text-xs font-bold text-[#C8102E] bg-red-50 dark:bg-red-950/20 px-2.5 py-1 rounded">Author Manual</span>
                    </div>
                    
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-gray-150 dark:divide-gray-700">
                        <div class="space-y-4 pr-0 md:pr-6">
                            <h5 class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider flex items-center">
                                <span class="mr-1.5">👍</span> The Do's (Maximize Reach)
                            </h5>
                            <ul class="space-y-3 text-xs">
                                <li class="flex items-start space-x-2">
                                    <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                                    <div>
                                        <strong class="text-gray-900 dark:text-white">Write Engaging Headlines:</strong>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Use clear, factual, and captivating titles to trigger interest on social media feeds.</p>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <span class="text-green-500 mt-0.5 shrink-0 font-bold">✓</span>
                                    <div>
                                        <strong class="text-gray-900 dark:text-white">Embed High-Quality Media:</strong>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Incorporate images, audio clips, and event schedules to increase reader stay time (dwell time).</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="space-y-4 pt-4 md:pt-0 pl-0 md:pl-6 border-gray-150 dark:border-gray-700">
                            <h5 class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider flex items-center">
                                <span class="mr-1.5">⚠️</span> The Don'ts (Avoid Violations)
                            </h5>
                            <ul class="space-y-3 text-xs">
                                <li class="flex items-start space-x-2">
                                    <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                                    <div>
                                        <strong class="text-gray-900 dark:text-white">Strictly No Clickbait:</strong>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Deceptive headings or fake summaries violate editor guidelines and can trigger article rejection.</p>
                                    </div>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <span class="text-red-500 mt-0.5 shrink-0 font-bold">✗</span>
                                    <div>
                                        <strong class="text-gray-900 dark:text-white">No Auto-Refresh or Bot Traffic:</strong>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Our anti-cheat engine monitors user-agent behaviors and discards rapid clicks and non-human traffic.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Author Article Performance Table -->
                @if($articlesCount > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-150 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Your Article Performance Stats</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-900 text-gray-400 font-bold border-b border-gray-150 dark:border-gray-700">
                                        <th class="p-4">Article Headline</th>
                                        <th class="p-4">Category</th>
                                        <th class="p-4 text-center">Verified Views</th>
                                        <th class="p-4 text-right">Earned Reward</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach(auth()->user()->articles()->orderBy('views_count', 'desc')->get() as $article)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                            <td class="p-4 font-bold text-gray-900 dark:text-white">
                                                <a href="/articles/{{ $article->slug }}" class="hover:text-[#C8102E] hover:underline" target="_blank">
                                                    {{ $article->title }}
                                                </a>
                                            </td>
                                            <td class="p-4 text-gray-500">{{ $article->category?->name ?? 'General' }}</td>
                                            <td class="p-4 text-center font-semibold text-gray-800 dark:text-gray-200">{{ number_format($article->views_count) }}</td>
                                            <td class="p-4 text-right font-black text-green-600 dark:text-green-400">
                                                {{ $currencySymbol }} {{ number_format($article->views_count * $rewardRate, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @else
                <!-- REGULAR USER / READER DASHBOARD -->
                <!-- User Welcome Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h3 class="text-xl font-bold flex items-center space-x-2">
                                <span>👋 Welcome back, {{ auth()->user()->name }}!</span>
                            </h3>
                            <p class="text-xs text-gray-500">Your reader dashboard tracks your bookmarked stories, posted comments, and account activity.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-950/40 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 text-xs font-bold rounded shadow-sm">
                                Verified Member
                            </span>
                            <a href="{{ route('profile') }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white text-xs font-bold rounded transition">
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Stats Overview Grid -->
                @php
                    $savedCount = auth()->user()->savedArticles()->count();
                    $commentsCount = auth()->user()->comments()->count();
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                    <!-- Stat 1: Bookmarks -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bookmarked Stories</span>
                            <div class="text-3xl font-extrabold text-[#C8102E] mt-2">{{ $savedCount }}</div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Articles saved in your reading list.</p>
                    </div>

                    <!-- Stat 2: Comments Posted -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Comments Posted</span>
                            <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-2">{{ $commentsCount }}</div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Discussions & reader feedback.</p>
                    </div>

                    <!-- Stat 3: Membership Status -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Member Since</span>
                            <div class="text-sm font-bold text-gray-900 dark:text-white mt-4">{{ auth()->user()->created_at->format('M d, Y') }}</div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Getembe News community account.</p>
                    </div>

                    <!-- Stat 4: Role Upgrade Request -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between bg-gradient-to-br from-red-50/50 to-white dark:from-red-950/10 dark:to-gray-800">
                        <div>
                            <span class="text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">Creator Program</span>
                            <div class="text-sm font-bold text-gray-900 dark:text-white mt-4">Become an Author</div>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-4">Request Author role to publish stories.</p>
                    </div>
                </div>

                <!-- Bookmarked Reading List -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-150 dark:border-gray-700 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center">
                            <span class="mr-2">🔖</span> My Saved Reading List
                        </h4>
                        <span class="text-xs text-gray-400 font-semibold">{{ $savedCount }} Saved</span>
                    </div>
                    <div class="p-6">
                        @php $savedArticles = auth()->user()->savedArticles()->latest()->get(); @endphp
                        @if($savedArticles->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($savedArticles as $item)
                                    <div class="bg-gray-50 dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-850 rounded-lg flex flex-col justify-between hover:shadow-md transition">
                                        <div>
                                            <span class="text-[9px] font-bold text-[#C8102E] uppercase">{{ $item->category?->name ?? 'General' }}</span>
                                            <h5 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white mt-1 line-clamp-2">
                                                <a href="/articles/{{ $item->slug }}" target="_blank" class="hover:underline">{{ $item->title }}</a>
                                            </h5>
                                        </div>
                                        <div class="flex items-center justify-between text-[10px] text-gray-500 mt-4 pt-3 border-t border-gray-150 dark:border-gray-800">
                                            <span>By {{ $item->author?->name ?? 'Editorial Staff' }}</span>
                                            <livewire:save-article-button :article="$item" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 space-y-2">
                                <div class="text-3xl">📖</div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">You haven't bookmarked any news stories yet.</p>
                                <p class="text-[11px] text-gray-400">Click the bookmark icon on any article while browsing to build your reading list.</p>
                                <a href="/" class="inline-block mt-2 px-4 py-2 bg-[#C8102E] text-white text-xs font-bold rounded hover:bg-red-700 transition">Explore Latest News</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Reader Comment History -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-150 dark:border-gray-700 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center">
                            <span class="mr-2">💬</span> My Posted Comments
                        </h4>
                        <span class="text-xs text-gray-400 font-semibold">{{ $commentsCount }} Comments</span>
                    </div>
                    <div class="p-6">
                        @php $comments = auth()->user()->comments()->with('article')->latest()->take(10)->get(); @endphp
                        @if($comments->count() > 0)
                            <div class="space-y-4">
                                @foreach($comments as $comment)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-gray-900 dark:text-white">
                                                Article: <a href="/articles/{{ $comment->article?->slug }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $comment->article?->title ?? 'News Article' }}</a>
                                            </span>
                                            <span class="text-[10px] text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-700 dark:text-gray-300 italic">"{{ $comment->body }}"</p>
                                        <div>
                                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded {{ $comment->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-950/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/30 dark:text-yellow-300' }}">
                                                {{ ucfirst($comment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 text-center py-6">You haven't posted any comments yet. Join the conversation under news stories!</p>
                        @endif
                    </div>
                </div>

                <!-- Creator Application Banner -->
                <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white rounded-lg p-6 shadow-md border border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="space-y-1 text-center sm:text-left">
                        <h4 class="text-base font-bold">Interested in Publishing Stories on Getembe News?</h4>
                        <p class="text-xs text-gray-300">Request an author role to draft news articles, report breaking stories, and earn creator rewards based on reader views.</p>
                    </div>
                    <a href="/contact" class="px-4 py-2 bg-[#C8102E] hover:bg-red-700 text-white text-xs font-extrabold rounded transition shrink-0 uppercase tracking-wider">
                        Apply for Author Role
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Author Dashboard & Rewards') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Alert Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold">Welcome back, {{ auth()->user()->name }}!</h3>
                    <p class="text-xs text-gray-500 mt-1">Here is the real-time status of your articles, audience reach, and earnings ledger.</p>
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
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Verified Views</span>
                        <div class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ number_format($verifiedViews) }}</div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-4">Anti-cheat system filtered real reader views.</p>
                </div>

                <!-- Card 3: Accumulated Earnings -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between bg-gradient-to-br from-green-50/50 to-white dark:from-green-950/10 dark:to-gray-800">
                    <div>
                        <span class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider">Reward Earnings</span>
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
                                        <td class="p-4 text-gray-500">{{ $article->category->name }}</td>
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

            <!-- Reading List / Bookmarks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-150 dark:border-gray-700">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center">
                        <span class="mr-2">🔖</span> My Bookmarked Reading List
                    </h4>
                </div>
                <div class="p-6">
                    @php $saved = auth()->user()->savedArticles()->get(); @endphp
                    @if($saved->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($saved as $item)
                                <div class="bg-gray-50 dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-850 rounded-lg flex flex-col justify-between hover:shadow-md transition">
                                    <div>
                                        <span class="text-[9px] font-bold text-[#C8102E] uppercase">{{ $item->category->name }}</span>
                                        <h5 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white mt-1 line-clamp-2">
                                            <a href="/articles/{{ $item->slug }}" target="_blank" class="hover:underline">{{ $item->title }}</a>
                                        </h5>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-gray-500 mt-4 pt-3 border-t border-gray-150 dark:border-gray-800">
                                        <span>By {{ $item->author->name }}</span>
                                        <livewire:save-article-button :article="$item" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 text-center py-6">You haven't bookmarked any articles yet. Bookmark your favorite stories to build a personalized reading list!</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

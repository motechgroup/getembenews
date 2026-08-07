<div class="space-y-6">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 rounded-xl shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="bg-orange-100 dark:bg-orange-950/40 text-[#cc6c3b] p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0v-8a2 2 0 00-2-2h-2a2 2 0 00-2 2v8a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                </span>
                <div>
                    <h1 class="text-xl font-serif font-black text-gray-900 dark:text-white uppercase tracking-wider">
                        Announcement Reports & Analytics
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Track submission performance, revenue metrics, agent commissions, and top performers.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Navigation link back to Announcements list -->
            <a href="/admin/announcements" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs px-3.5 py-2.5 rounded-lg transition flex items-center gap-1.5 border border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span>Announcements List</span>
            </a>

            <!-- Export Report CSV Button -->
            <button type="button" wire:click="exportCsv" class="bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Export Report (CSV)</span>
            </button>
        </div>
    </div>

    <!-- Date Period Filter Presets Bar -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 rounded-xl shadow-sm space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Preset Quick Filters -->
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 mr-1">Period:</span>
                
                @php
                    $presets = [
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'this_year' => 'This Year',
                        'all_time' => 'All Time',
                    ];
                @endphp

                @foreach($presets as $key => $label)
                    <button type="button" wire:click="setPeriod('{{ $key }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $period === $key ? 'bg-[#cc6c3b] text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Custom Date Range Pickers -->
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1">
                    <span class="text-[10px] uppercase font-bold text-gray-400">From:</span>
                    <input type="date" wire:model.live="date_from" wire:change="$set('period', 'custom')"
                           class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg p-1.5 focus:outline-none">
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-[10px] uppercase font-bold text-gray-400">To:</span>
                    <input type="date" wire:model.live="date_to" wire:change="$set('period', 'custom')"
                           class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg p-1.5 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Secondary Filters (Media, Type, Payment Status, Agent) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
            <div>
                <label class="text-[10px] uppercase font-bold text-gray-400">Media Target</label>
                <select wire:model.live="media" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg p-2 focus:outline-none mt-1">
                    <option value="">All Media Targets</option>
                    <option value="tv">TV Only</option>
                    <option value="radio">Radio Only</option>
                    <option value="both">Both (TV + Radio)</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] uppercase font-bold text-gray-400">Announcement Type</label>
                <select wire:model.live="type" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg p-2 focus:outline-none mt-1">
                    <option value="">All Types</option>
                    <option value="funeral">Funeral / Obituary</option>
                    <option value="general">General Notice</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] uppercase font-bold text-gray-400">Payment Status</label>
                <select wire:model.live="status" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg p-2 focus:outline-none mt-1">
                    <option value="">All Payment Statuses</option>
                    <option value="paid">Paid Announcements</option>
                    <option value="pending">Pending Payment</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] uppercase font-bold text-gray-400">Agent Origin</label>
                <select wire:model.live="agent_id" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white text-xs rounded-lg p-2 focus:outline-none mt-1">
                    <option value="">All Sources (Agents + Direct)</option>
                    <option value="direct">Direct Website Visitors</option>
                    @foreach($allAgents as $ag)
                        <option value="{{ $ag->id }}">{{ $ag->name }} {{ $ag->business_name ? "({$ag->business_name})" : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- KPI Summary Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Gross Revenue -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Gross Revenue Paid</p>
                    <h3 class="text-2xl font-serif font-black text-gray-900 dark:text-white mt-1">
                        KSh {{ number_format($stats['gross_revenue']) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-950/40 text-green-600 flex items-center justify-center font-bold text-sm">
                    KSh
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-2">
                Total paid announcements revenue
            </p>
        </div>

        <!-- Net Revenue (Platform Share) -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Net Platform Revenue</p>
                    <h3 class="text-2xl font-serif font-black text-emerald-600 dark:text-emerald-400 mt-1">
                        KSh {{ number_format($stats['net_revenue']) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    📈
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-2">
                Gross revenue minus agent commissions
            </p>
        </div>

        <!-- Agent Commissions Earned -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Agent Commissions</p>
                    <h3 class="text-2xl font-serif font-black text-[#cc6c3b] mt-1">
                        KSh {{ number_format($stats['total_commissions']) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-950/40 text-[#cc6c3b] flex items-center justify-center font-bold text-sm">
                    🤝
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-2">
                Total commissions earned by agents
            </p>
        </div>

        <!-- Total Submissions & Ticket Size -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Submissions</p>
                    <h3 class="text-2xl font-serif font-black text-gray-900 dark:text-white mt-1">
                        {{ number_format($stats['total_submissions']) }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center font-bold text-sm">
                    📜
                </div>
            </div>
            <div class="flex items-center justify-between text-[10px] text-gray-400 mt-2">
                <span>Paid: <strong class="text-green-600 dark:text-green-400">{{ $stats['paid_count'] }}</strong> &bull; Pending: <strong class="text-amber-500">{{ $stats['pending_count'] }}</strong></span>
                <span>Avg Ticket: <strong class="text-gray-700 dark:text-gray-300">KSh {{ number_format($stats['avg_ticket_size']) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Leaderboard & Breakdowns Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Top Agents Leaderboard (2 Columns wide on lg) -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🏆</span>
                    <div>
                        <h2 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider">
                            Top Performing Agents Leaderboard
                        </h2>
                        <p class="text-[10px] text-gray-400">Ranked by revenue generated during selected period</p>
                    </div>
                </div>
                <a href="/admin/agents" class="text-[10px] font-bold text-[#cc6c3b] hover:underline">Manage Agents &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-850 text-[10px] text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                            <th class="py-2.5 px-3">Rank / Agent</th>
                            <th class="py-2.5 px-3 text-center">Location</th>
                            <th class="py-2.5 px-3 text-center">Submissions</th>
                            <th class="py-2.5 px-3 text-right">Revenue Generated</th>
                            <th class="py-2.5 px-3 text-right">Commission Earned</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-semibold text-gray-700 dark:text-gray-300">
                        @forelse($topAgents as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-full font-extrabold text-[10px] flex items-center justify-center 
                                            {{ $index === 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($index === 1 ? 'bg-gray-200 text-gray-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400')) }}">
                                            #{{ $index + 1 }}
                                        </span>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $item->agent->name }}</div>
                                            @if($item->agent->business_name)
                                                <div class="text-[10px] text-[#cc6c3b] font-medium">{{ $item->agent->business_name }}</div>
                                            @endif
                                            <div class="text-[9px] text-gray-400 font-mono">{{ $item->agent->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center font-normal text-gray-500 dark:text-gray-400">
                                    {{ $item->agent->location }}
                                </td>
                                <td class="py-3 px-3 text-center font-bold">
                                    {{ number_format($item->total_announcements) }}
                                </td>
                                <td class="py-3 px-3 text-right font-serif font-bold text-gray-900 dark:text-white">
                                    KSh {{ number_format($item->total_revenue) }}
                                </td>
                                <td class="py-3 px-3 text-right font-serif font-bold text-[#cc6c3b]">
                                    KSh {{ number_format($item->total_commission) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400 font-normal italic">
                                    No agent transactions recorded for the selected period filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Media & Type Breakdown Cards (1 Column on lg) -->
        <div class="space-y-6">

            <!-- Media Target Distribution Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                    📺 Media Target Breakdown
                </h3>

                <div class="space-y-3 text-xs font-semibold">
                    <!-- TV -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 dark:text-gray-300">TV Station Only</span>
                            <span class="font-mono text-gray-900 dark:text-white font-bold">KSh {{ number_format($mediaBreakdown['tv']['revenue']) }} ({{ $mediaBreakdown['tv']['count'] }})</span>
                        </div>
                        @php
                            $tvPct = $stats['gross_revenue'] > 0 ? round(($mediaBreakdown['tv']['revenue'] / $stats['gross_revenue']) * 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $tvPct }}%"></div>
                        </div>
                    </div>

                    <!-- Radio -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 dark:text-gray-300">Radio Station Only</span>
                            <span class="font-mono text-gray-900 dark:text-white font-bold">KSh {{ number_format($mediaBreakdown['radio']['revenue']) }} ({{ $mediaBreakdown['radio']['count'] }})</span>
                        </div>
                        @php
                            $radioPct = $stats['gross_revenue'] > 0 ? round(($mediaBreakdown['radio']['revenue'] / $stats['gross_revenue']) * 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $radioPct }}%"></div>
                        </div>
                    </div>

                    <!-- Both TV & Radio -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 dark:text-gray-300">Combined (TV + Radio)</span>
                            <span class="font-mono text-gray-900 dark:text-white font-bold">KSh {{ number_format($mediaBreakdown['both']['revenue']) }} ({{ $mediaBreakdown['both']['count'] }})</span>
                        </div>
                        @php
                            $bothPct = $stats['gross_revenue'] > 0 ? round(($mediaBreakdown['both']['revenue'] / $stats['gross_revenue']) * 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#cc6c3b] h-2 rounded-full" style="width: {{ $bothPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcement Category Breakdown Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                    🏷️ Category Type Breakdown
                </h3>

                <div class="space-y-3 text-xs font-semibold">
                    <!-- Funeral / Obituary -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 dark:text-gray-300">Funeral & Obituary</span>
                            <span class="font-mono text-gray-900 dark:text-white font-bold">KSh {{ number_format($typeBreakdown['funeral']['revenue']) }} ({{ $typeBreakdown['funeral']['count'] }})</span>
                        </div>
                        @php
                            $funPct = $stats['gross_revenue'] > 0 ? round(($typeBreakdown['funeral']['revenue'] / $stats['gross_revenue']) * 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $funPct }}%"></div>
                        </div>
                    </div>

                    <!-- General Notice -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 dark:text-gray-300">General Notice</span>
                            <span class="font-mono text-gray-900 dark:text-white font-bold">KSh {{ number_format($typeBreakdown['general']['revenue']) }} ({{ $typeBreakdown['general']['count'] }})</span>
                        </div>
                        @php
                            $genPct = $stats['gross_revenue'] > 0 ? round(($typeBreakdown['general']['revenue'] / $stats['gross_revenue']) * 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $genPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Filtered Announcement Ledger Table -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden space-y-4">
        <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                <h3 class="text-xs font-black uppercase text-gray-900 dark:text-white tracking-wider">
                    Detailed Announcement Revenue Ledger
                </h3>
                <p class="text-[10px] text-gray-400">Showing announcements matching active date and category filters</p>
            </div>

            <!-- Search input inside ledger -->
            <div class="w-full sm:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search submitter name, phone or ref..."
                       class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-850 text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                        <th class="py-3 px-4">ID / Submitter</th>
                        <th class="py-3 px-4">Type & Media Target</th>
                        <th class="py-3 px-4 text-center">Airing Date</th>
                        <th class="py-3 px-4 text-center">Agent Origin</th>
                        <th class="py-3 px-4 text-right">Gross Amount</th>
                        <th class="py-3 px-4 text-right">Commission</th>
                        <th class="py-3 px-4 text-center">Payment Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-700 dark:text-gray-300">
                    @forelse($announcements as $ann)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900 dark:text-white">#{{ $ann->id }} - {{ $ann->visitor_name }}</div>
                                <div class="text-[10px] text-gray-500 font-mono">{{ $ann->visitor_phone }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase 
                                    {{ $ann->type === 'funeral' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300' }}">
                                    {{ $ann->type }}
                                </span>
                                <span class="ml-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    {{ strtoupper($ann->media) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-gray-600 dark:text-gray-400">
                                {{ $ann->airing_date ? $ann->airing_date->format('d M Y') : 'N/A' }}
                                <div class="text-[9px] text-gray-400">{{ $ann->days_count }} day(s) &bull; {{ $ann->word_count }} words</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($ann->agent)
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $ann->agent->name }}</div>
                                    @if($ann->agent->business_name)
                                        <div class="text-[9px] text-[#cc6c3b] font-medium">{{ $ann->agent->business_name }}</div>
                                    @endif
                                @else
                                    <span class="text-[10px] text-gray-400 font-normal">Direct (Website)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-serif font-bold text-gray-900 dark:text-white">
                                KSh {{ number_format($ann->total_amount) }}
                            </td>
                            <td class="py-3 px-4 text-right font-serif font-bold text-[#cc6c3b]">
                                KSh {{ number_format($ann->commission_amount ?? 0) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($ann->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-300">
                                        ✓ Paid
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                        ⏳ Pending
                                    </span>
                                @endif
                                @if($ann->payment_reference)
                                    <div class="text-[9px] text-gray-400 font-mono mt-0.5">{{ $ann->payment_reference }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 italic font-normal">
                                No announcement records found matching the current report criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

</div>

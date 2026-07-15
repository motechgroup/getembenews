<div class="space-y-6 text-xs">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 rounded-xl shadow-sm">
        <h1 class="text-lg font-serif font-black text-gray-900 dark:text-white uppercase tracking-wider">
            Announcements Management
        </h1>
        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">
            Moderation dashboard for visitors' submitted TV/Radio announcements.
        </p>
    </div>

    <!-- Session Feedback Banner -->
    @if(session()->has('message'))
        <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-700 dark:text-green-455 px-4 py-3 rounded-lg font-bold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Financial Statistics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Card 1: Total Paid Revenue -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Total Paid Revenue</span>
                <h3 class="text-base font-black text-green-600 dark:text-green-455 font-mono leading-none">
                    KSh {{ number_format($stats['total_paid'] ?? 0) }}
                </h3>
            </div>
            <div class="p-2 bg-green-50 dark:bg-green-950/20 text-green-600 rounded-lg shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 2: TV Revenue & Count -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">TV Revenue</span>
                <h3 class="text-base font-black text-blue-650 dark:text-blue-400 font-mono leading-none">
                    KSh {{ number_format($stats['tv_revenue'] ?? 0) }}
                </h3>
                <div class="text-[9px] text-gray-400 font-medium">
                    {{ number_format($stats['tv_count'] ?? 0) }} announcements
                </div>
            </div>
            <div class="p-2 bg-blue-50 dark:bg-blue-950/20 text-blue-650 dark:text-blue-450 rounded-lg shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
            </div>
        </div>

        <!-- Card 3: Radio Revenue & Count -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Radio Revenue</span>
                <h3 class="text-base font-black text-amber-600 dark:text-amber-450 font-mono leading-none">
                    KSh {{ number_format($stats['radio_revenue'] ?? 0) }}
                </h3>
                <div class="text-[9px] text-gray-400 font-medium">
                    {{ number_format($stats['radio_count'] ?? 0) }} announcements
                </div>
            </div>
            <div class="p-2 bg-amber-50 dark:bg-amber-955/20 text-amber-600 dark:text-amber-455 rounded-lg shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
            </div>
        </div>

        <!-- Card 4: TV & Radio (Both) -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Both Target Rev</span>
                <h3 class="text-base font-black text-indigo-650 dark:text-indigo-400 font-mono leading-none">
                    KSh {{ number_format($stats['both_revenue'] ?? 0) }}
                </h3>
                <div class="text-[9px] text-gray-400 font-medium">
                    {{ number_format($stats['both_count'] ?? 0) }} announcements
                </div>
            </div>
            <div class="p-2 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-650 dark:text-indigo-450 rounded-lg shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
        </div>

        <!-- Card 5: Agent Commissions -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Agent Commissions</span>
                <h3 class="text-base font-black text-purple-600 dark:text-purple-400 font-mono leading-none">
                    KSh {{ number_format($stats['total_commissions'] ?? 0) }}
                </h3>
                <div class="text-[9px] text-gray-400 font-medium">
                    Commissions paid
                </div>
            </div>
            <div class="p-2 bg-purple-50 dark:bg-purple-950/20 text-purple-600 rounded-lg shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 6: Awaiting Moderation -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Awaiting Moderation</span>
                <h3 class="text-base font-black text-red-600 dark:text-red-455 font-mono leading-none">
                    {{ number_format($stats['pending_approval'] ?? 0) }}
                </h3>
                <div class="text-[9px] text-gray-400 font-medium">
                    KSh {{ number_format($stats['total_pending'] ?? 0) }} pending
                </div>
            </div>
            <div class="p-2 bg-red-50 dark:bg-red-950/20 text-red-655 rounded-lg shrink-0">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 rounded-xl shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <!-- Search field -->
            <div class="relative w-full sm:max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Quick search announcements..." 
                       class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg pl-9 pr-4 py-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 justify-end">
                <!-- Advanced Filters Toggle Button -->
                <button type="button" 
                        wire:click="$toggle('showFilters')"
                        class="w-full sm:w-auto inline-flex items-center justify-center space-x-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-850 dark:hover:bg-gray-800 text-gray-750 dark:text-gray-200 font-bold py-2 px-4 rounded-lg text-xs transition uppercase tracking-wider border border-gray-200/50 dark:border-gray-800">
                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>{{ $showFilters ? 'Hide Filters' : 'Filters' }}</span>
                </button>

                <!-- Export CSV Button -->
                <button type="button" 
                        wire:click="exportRevenueReport"
                        class="w-full sm:w-auto inline-flex items-center justify-center space-x-1.5 bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg text-xs transition uppercase tracking-wider shadow-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export CSV</span>
                </button>
            </div>
        </div>

        @if($showFilters)
            <!-- Advanced Filters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pt-4 border-t border-gray-150 dark:border-gray-800 font-semibold">
                <!-- Status filter -->
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Payment Status</label>
                    <select wire:model.live="status" class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                        <option value="">All Payments</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>

                <!-- Type filter -->
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Announcement Type</label>
                    <select wire:model.live="type" class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                        <option value="">All Types</option>
                        <option value="funeral">Funeral</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <!-- Media Target filter -->
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Media Target</label>
                    <select wire:model.live="media" class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                        <option value="">All Media</option>
                        <option value="tv">TV Only</option>
                        <option value="radio">Radio Only</option>
                        <option value="both">Both (TV & Radio)</option>
                    </select>
                </div>

                <!-- Moderation Status filter -->
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Moderation Status</label>
                    <select wire:model.live="approved" class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="1">Approved</option>
                        <option value="0">Pending Approval</option>
                    </select>
                </div>

                <!-- Reset filters -->
                <div class="flex items-end">
                    <button type="button" 
                            wire:click="$set('search', ''); $set('status', ''); $set('type', ''); $set('media', ''); $set('approved', '');"
                            class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-2 rounded text-xs transition uppercase tracking-wider">
                        Reset Filters
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Announcements Table Section -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-850 text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                        <th class="py-3.5 px-4">Visitor</th>
                        <th class="py-3.5 px-4">Type / Media</th>
                        <th class="py-3.5 px-4">Schedule</th>
                        <th class="py-3.5 px-4 w-96">Content</th>
                        <th class="py-3.5 px-4 text-center">Cost Details</th>
                        <th class="py-3.5 px-4">Payment</th>
                        <th class="py-3.5 px-4 text-center">Moderation</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-semibold text-gray-700 dark:text-gray-300">
                    @forelse($announcements as $ann)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition duration-150">
                            <!-- Visitor -->
                            <td class="py-4 px-4 space-y-1">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $ann->visitor_name }}</div>
                                @if($ann->visitor_email)
                                    <div class="text-[10px] text-gray-400 font-medium">Email: {{ $ann->visitor_email }}</div>
                                @endif
                                <div class="text-[10px] text-gray-400 font-mono">{{ $ann->visitor_phone }}</div>
                                @if($ann->agent)
                                    <div class="text-[10px] text-purple-700 dark:text-purple-400 font-bold mt-1">
                                        Agent: {{ $ann->agent->name }} (Comm: KSh {{ number_format($ann->commission_amount) }})
                                    </div>
                                @endif
                            </td>

                            <!-- Type / Media -->
                            <td class="py-4 px-4 space-y-1">
                                <div>
                                    <span class="bg-orange-50 dark:bg-orange-950/20 text-[#cc6c3b] text-[9px] font-black uppercase px-2 py-0.5 rounded tracking-wide">
                                        {{ $ann->type }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-gray-400 font-medium">
                                    Target: <span class="uppercase font-bold text-gray-650 dark:text-gray-300">{{ $ann->media }}</span>
                                </div>
                            </td>

                            <!-- Schedule -->
                            <td class="py-4 px-4 space-y-1">
                                @if($ann->airing_date)
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        Start: <span class="font-normal font-mono text-[10px] text-gray-500 dark:text-gray-400">{{ $ann->airing_date->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        End: <span class="font-normal font-mono text-[10px] text-gray-500 dark:text-gray-400">{{ $ann->expiry_date->format('Y-m-d') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic font-medium">Not set</span>
                                @endif
                            </td>

                            <!-- Content -->
                            <td class="py-4 px-4">
                                <p class="line-clamp-3 leading-relaxed italic text-gray-650 dark:text-gray-350">
                                    "{{ $ann->content }}"
                                </p>
                            </td>

                            <!-- Cost Details -->
                            <td class="py-4 px-4 text-center space-y-1">
                                <div class="font-black text-[#cc6c3b] text-sm">KSh {{ number_format($ann->total_amount) }}</div>
                                <div class="text-[9px] text-gray-400 font-medium">
                                    {{ $ann->word_count }} words &times; {{ $ann->days_count }} days
                                </div>
                            </td>

                            <!-- Payment status -->
                            <td class="py-4 px-4 space-y-1.5">
                                @if($ann->payment_status === 'paid')
                                    <div>
                                        <span class="bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-455 text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                            Paid
                                        </span>
                                    </div>
                                    <div class="text-[9px] text-gray-400 font-mono font-medium">{{ $ann->payment_reference }}</div>
                                @else
                                    <div>
                                        <span class="bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-550 text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                            Unpaid
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Moderation status -->
                            <td class="py-4 px-4 text-center">
                                <button type="button" 
                                        wire:click="toggleApproval({{ $ann->id }})"
                                        class="inline-flex items-center px-3 py-1 rounded text-[10px] font-black uppercase tracking-wide transition border {{ $ann->is_approved ? 'bg-green-600 border-green-600 text-white hover:bg-green-700' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border-transparent text-gray-700 dark:text-gray-300' }}">
                                    {{ $ann->is_approved ? 'Approved' : 'Approve' }}
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right space-y-1.5">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($ann->payment_status !== 'paid')
                                        <button type="button" 
                                                wire:click="markAsPaid({{ $ann->id }})"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-2.5 rounded text-[9px] uppercase tracking-wider transition">
                                            Mark Paid
                                        </button>
                                    @endif
                                    <button type="button" 
                                            wire:confirm="Are you sure you want to delete this announcement?"
                                            wire:click="deleteAnnouncement({{ $ann->id }})"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2.5 rounded text-[9px] uppercase tracking-wider transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400 font-bold">
                                No announcements found matching filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>

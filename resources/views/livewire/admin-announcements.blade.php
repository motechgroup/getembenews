<div class="space-y-6 text-xs">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 rounded-xl shadow-sm">
        <div>
            <h1 class="text-lg font-serif font-black text-gray-900 dark:text-white uppercase tracking-wider">
                Announcements Management
            </h1>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">
                Moderation dashboard for visitors' submitted TV/Radio announcements.
            </p>
        </div>
    </div>

    <!-- Session Feedback Banner -->
    @if(session()->has('message'))
        <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-700 dark:text-green-455 px-4 py-3 rounded-lg font-bold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Financial Statistics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Paid Revenue -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Total Paid Revenue</span>
                <h3 class="text-base font-black text-green-600 dark:text-green-455 font-mono leading-none">
                    KSh {{ number_format($stats['total_paid'] ?? 0) }}
                </h3>
            </div>
            <div class="p-2 bg-green-50 dark:bg-green-950/20 text-green-600 rounded-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 2: Pending Revenue -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Pending Revenue</span>
                <h3 class="text-base font-black text-orange-600 dark:text-orange-455 font-mono leading-none">
                    KSh {{ number_format($stats['total_pending'] ?? 0) }}
                </h3>
            </div>
            <div class="p-2 bg-orange-50 dark:bg-orange-95/20 text-orange-600 rounded-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 3: Agent Commissions -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Agent Commissions</span>
                <h3 class="text-base font-black text-purple-600 dark:text-purple-400 font-mono leading-none">
                    KSh {{ number_format($stats['total_commissions'] ?? 0) }}
                </h3>
            </div>
            <div class="p-2 bg-purple-50 dark:bg-purple-950/20 text-purple-600 rounded-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 4: Awaiting Moderation -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm flex items-center justify-between transition duration-155">
            <div class="space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Awaiting Moderation</span>
                <h3 class="text-base font-black text-red-600 dark:text-red-455 font-mono leading-none">
                    {{ number_format($stats['pending_approval'] ?? 0) }}
                </h3>
            </div>
            <div class="p-2 bg-red-50 dark:bg-red-950/20 text-red-655 rounded-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 rounded-xl shadow-sm space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-6 gap-4 font-semibold">
            <!-- Search field -->
            <div class="space-y-1">
                <label class="text-[10px] uppercase font-bold text-gray-500">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, phone or text..." 
                       class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
            </div>

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

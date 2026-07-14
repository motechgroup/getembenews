<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8 text-xs font-semibold">
    
    <!-- Profile Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 rounded-xl shadow-sm">
        <div>
            <div class="flex items-center space-x-2">
                <span class="bg-[#cc6c3b] text-white font-black text-[9px] uppercase px-2 py-0.5 rounded tracking-wide">
                    Agent Portal
                </span>
            </div>
            <h1 class="text-xl font-serif font-black text-gray-900 dark:text-white uppercase tracking-wider mt-1.5">
                Welcome, {{ $agent->name }}
            </h1>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                Location: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $agent->location }}</span> &bull; 
                Commission Rate: <span class="font-bold text-gray-755 dark:text-gray-300">{{ $agent->commission_percentage }}%</span>
            </p>
        </div>
        <button type="button" 
                wire:click="logoutAgent"
                class="bg-red-50 hover:bg-red-150 border border-red-200 text-red-750 font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm uppercase tracking-wider">
            Logout Agent Session
        </button>
    </div>

    <!-- Performance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 font-bold text-center">
        <!-- Announcements count -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm space-y-1">
            <div class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Paid Announcements Submitted</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $agent->total_announcements }}</div>
        </div>

        <!-- Commission Earned -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm space-y-1">
            <div class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Total Commission Earned</div>
            <div class="text-2xl font-black text-green-700 dark:text-green-455">KSh {{ number_format($agent->total_commission) }}</div>
        </div>

        <!-- Total Paid Out -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm space-y-1">
            <div class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Total Commission Paid Out</div>
            <div class="text-2xl font-black text-blue-700 dark:text-blue-455">KSh {{ number_format($agent->total_payouts) }}</div>
        </div>

        <!-- Unpaid Balance -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm space-y-1">
            <div class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Unpaid Commission Balance</div>
            <div class="text-2xl font-black text-orange-600 dark:text-orange-455">KSh {{ number_format($agent->commission_balance) }}</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-250 dark:border-gray-800">
        <button type="button" 
                wire:click="setActiveTab('announcements')"
                class="px-4 py-2 border-b-2 text-xs font-black uppercase tracking-wider transition {{ $activeTab === 'announcements' ? 'border-[#cc6c3b] text-[#cc6c3b]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Submissions Log
        </button>
        <button type="button" 
                wire:click="setActiveTab('payouts')"
                class="px-4 py-2 border-b-2 text-xs font-black uppercase tracking-wider transition {{ $activeTab === 'payouts' ? 'border-[#cc6c3b] text-[#cc6c3b]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Payout History
        </button>
        <button type="button" 
                wire:click="setActiveTab('disputes')"
                class="px-4 py-2 border-b-2 text-xs font-black uppercase tracking-wider transition {{ $activeTab === 'disputes' ? 'border-[#cc6c3b] text-[#cc6c3b]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Disputes & Support
            @php
                $myOpenDisputes = $agent->disputes()->where('status', 'open')->count();
            @endphp
            @if($myOpenDisputes > 0)
                <span class="ml-1 bg-red-650 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">
                    {{ $myOpenDisputes }}
                </span>
            @endif
        </button>
    </div>

    <!-- Submissions tab content -->
    @if($activeTab === 'announcements')
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden space-y-4 p-6">
            <div>
                <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                    Your Announcement Submissions Log
                </h3>
                <p class="text-[10px] text-gray-550 mt-1">
                    Track status and commission logs for all announcements submitted using your Agent PIN.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-850 text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Visitor/User Name</th>
                            <th class="py-3 px-4">Contact Phone</th>
                            <th class="py-3 px-4 text-center">Media Target</th>
                            <th class="py-3 px-4 text-center">Amount</th>
                            <th class="py-3 px-4 text-center">Commission</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-770 dark:text-gray-300">
                        @forelse($announcements as $ann)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                                <td class="py-4 px-4 space-y-1">
                                    <div class="text-gray-550 dark:text-gray-405 font-mono">{{ $ann->created_at->format('M d, Y H:i') }}</div>
                                    @if($ann->airing_date)
                                        <div class="text-[9px] text-gray-450 dark:text-gray-400 font-semibold">
                                            Airing: <span class="font-mono text-[#cc6c3b] font-bold">{{ $ann->airing_date->format('M d') }} - {{ $ann->expiry_date->format('M d') }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                    {{ $ann->visitor_name }}
                                </td>
                                <td class="py-4 px-4 font-mono">
                                    {{ $ann->visitor_phone }}
                                </td>
                                <td class="py-4 px-4 text-center uppercase">
                                    {{ $ann->type }} / {{ $ann->media }}
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-gray-900 dark:text-white">
                                    KSh {{ number_format($ann->total_amount) }}
                                </td>
                                <td class="py-4 px-4 text-center font-black text-green-700 dark:text-green-455">
                                    KSh {{ number_format($ann->commission_amount) }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $ann->payment_status === 'paid' ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' }}">
                                        {{ $ann->payment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-gray-400 font-bold">
                                    No submissions registered under your Agent PIN.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($announcements->hasPages())
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Payout history tab content -->
    @if($activeTab === 'payouts')
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden p-6 space-y-4">
            <div>
                <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                    Commission Payouts ledger
                </h3>
                <p class="text-[10px] text-gray-550 mt-1">
                    Log of all commission payments sent to you by Getembe News.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-850 text-[10px] text-gray-555 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                            <th class="py-3 px-4">Paid Date</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Payment Method</th>
                            <th class="py-3 px-4">Reference/Tx ID</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-770 dark:text-gray-300">
                        @forelse($payouts as $po)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                                <td class="py-4 px-4 font-mono">{{ $po->paid_at->format('M d, Y H:i') }}</td>
                                <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">KSh {{ number_format($po->amount) }}</td>
                                <td class="py-4 px-4 uppercase">{{ $po->payment_method }}</td>
                                <td class="py-4 px-4 font-mono text-gray-400">{{ $po->reference }}</td>
                                <td class="py-4 px-4 text-right">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400">
                                        {{ $po->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-gray-400 font-bold">
                                    No payouts recorded for your agent profile.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payouts->hasPages())
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $payouts->links(data: ['pageName' => 'payoutsPage']) }}
                </div>
            @endif
        </div>
    @endif

    <!-- Disputes tab content -->
    @if($activeTab === 'disputes')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-semibold">
            <!-- Left: File Dispute Form -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 rounded-xl shadow-sm space-y-4 h-fit">
                <div>
                    <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                        Submit Dispute
                    </h3>
                    <p class="text-[10px] text-gray-550 mt-1">
                        Report issues regarding unpaid commission, incorrect rates, or missing logs.
                    </p>
                </div>

                @if(session()->has('dispute_message'))
                    <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-700 dark:text-green-455 p-3 rounded font-bold text-[10px]">
                        {{ session('dispute_message') }}
                    </div>
                @endif

                <form wire:submit.prevent="fileDispute" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-gray-500">Ticket Subject</label>
                        <input type="text" wire:model="dispute_subject" placeholder="e.g. Missing Commission for July 12" required
                               class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                        @error('dispute_subject') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-gray-500">Announcement ID (Optional)</label>
                        <input type="number" wire:model="dispute_announcement_id" placeholder="e.g. 45"
                               class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                        @error('dispute_announcement_id') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-gray-500">Detailed Description</label>
                        <textarea wire:model="dispute_description" placeholder="Provide full details of the issue..." required rows="4"
                                  class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none"></textarea>
                        @error('dispute_description') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold text-xs py-2.5 rounded-lg transition shadow-sm uppercase tracking-wider">
                        File Dispute Ticket
                    </button>
                </form>
            </div>

            <!-- Right: Disputes Log -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                <div>
                    <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                        Your Dispute Tickets Log
                    </h3>
                    <p class="text-[10px] text-gray-555 mt-1">
                        View status updates and official resolutions for disputes you have raised.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[10px]">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-850 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Subject</th>
                                <th class="py-3 px-4">Description</th>
                                <th class="py-3 px-4">Resolution Details</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-770 dark:text-gray-300">
                            @forelse($disputes as $disp)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                                    <td class="py-4 px-4 font-mono text-gray-400">{{ $disp->created_at->format('M d, Y H:i') }}</td>
                                    <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                        <div>{{ $disp->subject }}</div>
                                        @if($disp->announcement_id)
                                            <div class="text-[9px] text-gray-450 font-normal">Announcement ID: #{{ $disp->announcement_id }}</div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-gray-600 dark:text-gray-400 max-w-xs break-words">{{ $disp->description }}</td>
                                    <td class="py-4 px-4 font-mono text-gray-500 italic max-w-xs break-words">
                                        {{ $disp->resolution ?: 'Pending administrative review.' }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $disp->status === 'resolved' ? 'bg-green-50 text-green-700' : ($disp->status === 'closed' ? 'bg-gray-100 text-gray-700' : 'bg-red-50 text-red-700') }}">
                                            {{ $disp->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-gray-400 font-bold">
                                        No dispute tickets filed.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($disputes->hasPages())
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                        {{ $disputes->links(data: ['pageName' => 'disputesPage']) }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>

<div class="space-y-6 text-xs">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-6 rounded-xl shadow-sm">
        <div>
            <h1 class="text-lg font-serif font-black text-gray-900 dark:text-white uppercase tracking-wider">
                Agents & Commissions
            </h1>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">
                Manage registered agents, their commission percentages, and view performance reports.
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" 
                    wire:click="openImport()"
                    class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm border border-gray-300 dark:border-gray-700 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#cc6c3b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Bulk Agents
            </button>
            <button type="button" 
                    wire:click="openForm()"
                    class="bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm">
                Create Agent Account
            </button>
        </div>
    </div>

    <!-- Feedback Banner -->
    @if(session()->has('message'))
        <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-700 dark:text-green-455 px-4 py-3 rounded-lg font-bold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Import Modal / Block -->
    @if($isImportOpen)
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm space-y-4 font-semibold">
            <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-800 pb-3">
                <div>
                    <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider">
                        Import Bulk Agents (Excel / CSV)
                    </h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                        Upload a CSV, TXT, or Excel file containing agent data. Missing PINs will automatically be generated.
                    </p>
                </div>
                <button type="button" wire:click="downloadSampleCsv()" class="text-xs text-[#cc6c3b] hover:underline font-bold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Download Sample CSV Template
                </button>
            </div>

            <form wire:submit.prevent="importAgents" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Select File (.csv, .xlsx, .xls, .txt)</label>
                    <input type="file" wire:model="importFile" accept=".csv, .xlsx, .xls, .txt"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-bold file:bg-[#cc6c3b] file:text-white hover:file:bg-orange-700">
                    <div wire:loading wire:target="importFile" class="text-[10px] text-orange-600 font-bold">Uploading & analyzing file...</div>
                    @error('importFile') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                </div>

                @if($importResults)
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-850 border border-gray-200 dark:border-gray-750 text-xs space-y-2">
                        <div class="font-black text-gray-900 dark:text-white uppercase tracking-wider">Import Summary:</div>
                        <div class="flex space-x-4 text-xs font-bold">
                            <span class="text-green-700 dark:text-green-400">✓ Added: {{ $importResults['success'] }}</span>
                            <span class="text-gray-500">⏭ Skipped: {{ $importResults['skipped'] }}</span>
                            @if(count($importResults['errors']) > 0)
                                <span class="text-red-600">⚠ Errors: {{ count($importResults['errors']) }}</span>
                            @endif
                        </div>
                        @if(count($importResults['errors']) > 0)
                            <ul class="text-[10px] text-red-500 font-normal list-disc list-inside space-y-0.5 max-h-32 overflow-y-auto pt-1">
                                @foreach($importResults['errors'] as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <div class="pt-2 flex space-x-2">
                    <button type="submit" wire:loading.attr="disabled" class="bg-[#cc6c3b] hover:bg-orange-700 disabled:opacity-50 text-white font-bold px-4 py-2 rounded-lg transition text-xs">
                        Start Importing
                    </button>
                    <button type="button" wire:click="closeImport()" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-750 text-gray-700 dark:text-gray-300 font-bold px-4 py-2 rounded-lg transition text-xs">
                        Close
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Form modal / block -->
    @if($isFormOpen)
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm space-y-4 font-semibold">
            <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ $agentId ? 'Edit Agent Account' : 'Create New Agent' }}
            </h3>

            <form wire:submit.prevent="saveAgent" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Agent Name</label>
                    <input type="text" wire:model="name" required placeholder="e.g. Samuel Mogaka"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('name') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Business Name (Optional)</label>
                    <input type="text" wire:model="business_name" placeholder="e.g. Mogaka Enterprises"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('business_name') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Phone Number</label>
                    <input type="text" wire:model="phone" required placeholder="e.g. +254712345678"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('phone') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Agent Location</label>
                    <input type="text" wire:model="location" required placeholder="e.g. Kisii Town"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('location') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] uppercase font-bold text-gray-500">Agent PIN (Auto-Generated)</label>
                        <button type="button" wire:click="regeneratePin" class="text-[9px] font-bold text-[#cc6c3b] hover:underline">
                            ↻ Generate New
                        </button>
                    </div>
                    <div class="relative">
                        <input type="text" wire:model="pin" required placeholder="Auto-generated" maxlength="4"
                               class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs font-mono font-bold text-gray-900 dark:text-white focus:outline-none tracking-widest">
                    </div>
                    @error('pin') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Commission Percentage (%)</label>
                    <input type="number" wire:model="commission_percentage" required min="0" max="100"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('commission_percentage') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-3 pt-4 flex space-x-2">
                    <button type="submit" class="bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold px-4 py-2 rounded-lg transition text-xs">
                        {{ $agentId ? 'Save Changes' : 'Create Agent' }}
                    </button>
                    <button type="button" wire:click="closeForm()" class="bg-gray-100 hover:bg-gray-250 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-750 text-gray-700 dark:text-gray-300 font-bold px-4 py-2 rounded-lg transition text-xs">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Search Controls -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-4 rounded-xl shadow-sm">
        <div class="w-full font-semibold">
            <label class="text-[10px] uppercase font-bold text-gray-500">Search Agents</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, business, phone, location or PIN..." 
                   class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none mt-1">
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-850 text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                        <th class="py-3.5 px-4">Agent / Business Name</th>
                        <th class="py-3.5 px-4 text-center">Security PIN</th>
                        <th class="py-3.5 px-4 text-center">Location</th>
                        <th class="py-3.5 px-4 text-center">Commission Rate</th>
                        <th class="py-3.5 px-4 text-center">Announcements Submitted</th>
                        <th class="py-3.5 px-4 text-center">Total Revenue Generated</th>
                        <th class="py-3.5 px-4 text-center">Total Commission Earned</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-770 dark:text-gray-300">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                            <td class="py-4 px-4 cursor-pointer" wire:click="viewDetails({{ $agent->id }})">
                                <div class="font-bold text-gray-900 dark:text-white hover:text-[#cc6c3b] transition hover:underline">{{ $agent->name }}</div>
                                @if($agent->business_name)
                                    <div class="text-[10px] text-[#cc6c3b] font-semibold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $agent->business_name }}
                                    </div>
                                @endif
                                @if($agent->phone)
                                    <div class="text-[10px] text-gray-500 font-mono flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $agent->phone }}
                                    </div>
                                @endif
                                <div class="text-[9px] text-gray-400">Agent ID: {{ $agent->id }}</div>
                            </td>
                            <td class="py-4 px-4 text-center font-mono font-bold text-[#cc6c3b]">
                                {{ $agent->pin }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                {{ $agent->location }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="bg-orange-50 dark:bg-orange-950/20 text-[#cc6c3b] px-2 py-0.5 rounded text-[10px] font-bold">
                                    {{ $agent->commission_percentage }}%
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                {{ $agent->total_announcements }}
                            </td>
                            <td class="py-4 px-4 text-center text-gray-900 dark:text-white font-bold">
                                KSh {{ number_format($agent->total_revenue) }}
                            </td>
                            <td class="py-4 px-4 text-center text-green-700 dark:text-green-455 font-black text-sm">
                                KSh {{ number_format($agent->total_commission) }}
                            </td>
                            <td class="py-4 px-4 text-right space-x-2">
                                <button type="button" 
                                        wire:confirm="Are you sure you want to generate a new PIN code for {{ $agent->name }}?"
                                        wire:click="regenerateAgentPin({{ $agent->id }})" 
                                        class="text-orange-600 hover:text-orange-800 font-bold" title="Generate new 4-digit PIN">Reset PIN</button>
                                <button type="button" 
                                        wire:click="openForm({{ $agent->id }})" 
                                        class="text-blue-600 hover:text-blue-800 font-bold">Edit</button>
                                <button type="button" 
                                        wire:confirm="Are you sure you want to delete this agent? This will set associated announcements' agent field to null but preserve data."
                                        wire:click="deleteAgent({{ $agent->id }})" 
                                        class="text-red-650 hover:text-red-800 font-bold">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400 font-bold">
                                No agents registered.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agents->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $agents->links() }}
            </div>
        @endif
    </div>

    <!-- Details Panel / Modal -->
    @if($isDetailsOpen && $selectedAgentForDetails)
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-800 pb-3">
                <div>
                    <h2 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider">
                        Agent Performance Profile: {{ $selectedAgentForDetails->name }}
                    </h2>
                    <p class="text-[10px] text-gray-550 dark:text-gray-400 mt-1 flex flex-wrap items-center gap-2">
                        @if($selectedAgentForDetails->business_name)
                            Business: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $selectedAgentForDetails->business_name }}</span> &bull;
                        @endif
                        @if($selectedAgentForDetails->phone)
                            Phone: <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $selectedAgentForDetails->phone }}</span> &bull;
                        @endif
                        Location: {{ $selectedAgentForDetails->location }} &bull; PIN: <span class="font-mono font-bold text-[#cc6c3b]">{{ $selectedAgentForDetails->pin }}</span>
                        <button type="button" 
                                wire:confirm="Are you sure you want to generate a new PIN code for {{ $selectedAgentForDetails->name }}?"
                                wire:click="regenerateAgentPin({{ $selectedAgentForDetails->id }})" 
                                class="text-[9px] font-bold text-[#cc6c3b] hover:underline bg-orange-50 dark:bg-orange-950/30 px-1.5 py-0.5 rounded">
                            ↻ Regenerate PIN
                        </button>
                    </p>
                </div>
                <button type="button" wire:click="closeDetails()" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold px-3 py-1.5 rounded-lg transition text-xs uppercase tracking-wider">
                    Close Profile
                </button>
            </div>

            <!-- Stats grid inside profile -->
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 text-center font-bold">
                <div class="bg-gray-55 dark:bg-gray-850 p-4 rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="text-[9px] uppercase font-bold text-gray-400">Announcements Submitted</div>
                    <div class="text-xl font-black text-gray-900 dark:text-white mt-1">{{ $selectedAgentForDetails->total_announcements }}</div>
                </div>
                <div class="bg-gray-55 dark:bg-gray-850 p-4 rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="text-[9px] uppercase font-bold text-gray-400">Total Revenue Generated</div>
                    <div class="text-xl font-black text-[#cc6c3b] mt-1">KSh {{ number_format($selectedAgentForDetails->total_revenue) }}</div>
                </div>
                <div class="bg-gray-55 dark:bg-gray-850 p-4 rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="text-[9px] uppercase font-bold text-gray-400">Total Commission Earned</div>
                    <div class="text-xl font-black text-green-700 dark:text-green-455 mt-1">KSh {{ number_format($selectedAgentForDetails->total_commission) }}</div>
                </div>
                <div class="bg-gray-55 dark:bg-gray-850 p-4 rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="text-[9px] uppercase font-bold text-gray-400">Total Paid Out</div>
                    <div class="text-xl font-black text-blue-700 dark:text-blue-455 mt-1">KSh {{ number_format($selectedAgentForDetails->total_payouts) }}</div>
                </div>
                <div class="bg-gray-55 dark:bg-gray-850 p-4 rounded-lg border border-gray-100 dark:border-gray-800">
                    <div class="text-[9px] uppercase font-bold text-gray-400">Unpaid Balance</div>
                    <div class="text-xl font-black text-orange-600 dark:text-orange-455 mt-1">KSh {{ number_format($selectedAgentForDetails->commission_balance) }}</div>
                </div>
            </div>

            <!-- Profile Tabs -->
            <div class="flex border-b border-gray-200 dark:border-gray-800">
                <button type="button" 
                        wire:click="setDetailsTab('announcements')"
                        class="px-4 py-2 border-b-2 text-xs font-black uppercase tracking-wider transition {{ $activeDetailsTab === 'announcements' ? 'border-[#cc6c3b] text-[#cc6c3b]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Announcements History
                </button>
                <button type="button" 
                        wire:click="setDetailsTab('payouts')"
                        class="px-4 py-2 border-b-2 text-xs font-black uppercase tracking-wider transition {{ $activeDetailsTab === 'payouts' ? 'border-[#cc6c3b] text-[#cc6c3b]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Commissions & Payouts
                </button>
                <button type="button" 
                        wire:click="setDetailsTab('disputes')"
                        class="px-4 py-2 border-b-2 text-xs font-black uppercase tracking-wider transition {{ $activeDetailsTab === 'disputes' ? 'border-[#cc6c3b] text-[#cc6c3b]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Disputes Log
                    @php
                        $openDisputeCount = $selectedAgentForDetails->disputes()->where('status', 'open')->count();
                    @endphp
                    @if($openDisputeCount > 0)
                        <span class="ml-1 bg-red-650 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">
                            {{ $openDisputeCount }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- Tab Content: Announcements Log -->
            @if($activeDetailsTab === 'announcements')
                <div class="space-y-3 font-semibold">
                    <h3 class="text-xs font-bold uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                        Submitted Announcements History
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[10px]">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-850 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                    <th class="py-2 px-3">Date</th>
                                    <th class="py-2 px-3">Visitor Info</th>
                                    <th class="py-2 px-3 text-center">Type / Target</th>
                                    <th class="py-2 px-3 text-center">Amount</th>
                                    <th class="py-2 px-3 text-center">Commission</th>
                                    <th class="py-2 px-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-770 dark:text-gray-300">
                                @forelse($selectedAgentForDetails->announcements()->latest()->get() as $ann)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50">
                                        <td class="py-3 px-3 text-gray-400 font-mono">{{ $ann->created_at->format('M d, Y H:i') }}</td>
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $ann->visitor_name }}</div>
                                            <div class="text-[9px] text-gray-450 font-mono">{{ $ann->visitor_phone }}</div>
                                        </td>
                                        <td class="py-3 px-3 text-center uppercase font-bold">{{ $ann->type }} / {{ $ann->media }}</td>
                                        <td class="py-3 px-3 text-center font-bold text-gray-900 dark:text-white">KSh {{ number_format($ann->total_amount) }}</td>
                                        <td class="py-3 px-3 text-center font-black text-green-700 dark:text-green-455">KSh {{ number_format($ann->commission_amount) }}</td>
                                        <td class="py-3 px-3 text-right">
                                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $ann->payment_status === 'paid' ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' }}">
                                                {{ $ann->payment_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-6 text-gray-400 font-bold">
                                            No announcements submitted by this agent yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Tab Content: Payouts Ledger -->
            @if($activeDetailsTab === 'payouts')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-semibold">
                    <!-- Record Payout Form -->
                    <div class="bg-gray-50 dark:bg-gray-850 border border-gray-150 dark:border-gray-800 p-4 rounded-xl space-y-4">
                        <h3 class="text-xs font-bold uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-250 dark:border-gray-800 pb-1.5">
                            Record a Payout
                        </h3>

                        @if(session()->has('payout_message'))
                            <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-700 dark:text-green-455 p-2.5 rounded font-bold text-[10px]">
                                {{ session('payout_message') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="recordPayout" class="space-y-3">
                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-gray-500">Payout Amount (KSh)</label>
                                <input type="number" wire:model="payout_amount" required min="1" max="{{ $selectedAgentForDetails->commission_balance }}" placeholder="e.g. 500"
                                       class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                                @error('payout_amount') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-gray-500">Payment Method</label>
                                <select wire:model="payout_method" required class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                                    <option value="M-Pesa">M-Pesa STK/Paybill</option>
                                    <option value="Bank Transfer">Bank EFT/RTGS</option>
                                    <option value="Cash">Cash Handout</option>
                                </select>
                                @error('payout_method') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-gray-500">Reference / Tx Code</label>
                                <input type="text" wire:model="payout_reference" placeholder="e.g. RFX8932782"
                                       class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                                @error('payout_reference') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                            </div>

                            <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-bold text-xs py-2 rounded-lg transition shadow-sm uppercase tracking-wider">
                                Log Payout
                            </button>
                        </form>
                    </div>

                    <!-- Payout History -->
                    <div class="lg:col-span-2 space-y-3">
                        <h3 class="text-xs font-bold uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-1.5">
                            Payout History Logs
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-[10px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-850 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                        <th class="py-2 px-3">Date</th>
                                        <th class="py-2 px-3">Amount</th>
                                        <th class="py-2 px-3">Method</th>
                                        <th class="py-2 px-3">Reference</th>
                                        <th class="py-2 px-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-750 dark:text-gray-300">
                                    @forelse($selectedAgentForDetails->payouts()->latest()->get() as $po)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50">
                                            <td class="py-3 px-3 font-mono">{{ $po->paid_at->format('M d, Y H:i') }}</td>
                                            <td class="py-3 px-3 font-bold text-gray-900 dark:text-white">KSh {{ number_format($po->amount) }}</td>
                                            <td class="py-3 px-3 uppercase">{{ $po->payment_method }}</td>
                                            <td class="py-3 px-3 font-mono text-gray-400">{{ $po->reference }}</td>
                                            <td class="py-3 px-3 text-right">
                                                <button type="button" 
                                                        wire:click="deletePayout({{ $po->id }})"
                                                        wire:confirm="Are you sure you want to delete/void this payout record?"
                                                        class="text-red-500 hover:text-red-700 font-bold">Void</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-6 text-gray-400 font-bold">
                                                No payouts recorded for this agent.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tab Content: Disputes Log -->
            @if($activeDetailsTab === 'disputes')
                <div class="space-y-6 font-semibold">
                    <div class="space-y-3">
                        <h3 class="text-xs font-bold uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-1.5">
                            Reported Disputes List
                        </h3>

                        @if(session()->has('dispute_message'))
                            <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-700 dark:text-green-455 p-2.5 rounded font-bold text-[10px] mb-3">
                                {{ session('dispute_message') }}
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-[10px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-850 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                                        <th class="py-2 px-3">Date</th>
                                        <th class="py-2 px-3">Ticket Info</th>
                                        <th class="py-2 px-3">Description</th>
                                        <th class="py-2 px-3">Resolution Notes</th>
                                        <th class="py-2 px-3 text-center">Status</th>
                                        <th class="py-2 px-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-770 dark:text-gray-300">
                                    @forelse($selectedAgentForDetails->disputes()->latest()->get() as $disp)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50">
                                            <td class="py-3 px-3 font-mono text-gray-400">{{ $disp->created_at->format('M d, Y H:i') }}</td>
                                            <td class="py-3 px-3 font-bold text-gray-900 dark:text-white">
                                                <div>{{ $disp->subject }}</div>
                                                @if($disp->announcement_id)
                                                    <div class="text-[9px] text-gray-450 font-normal">Announcement ID: #{{ $disp->announcement_id }}</div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3 text-gray-600 dark:text-gray-400 max-w-xs break-words">{{ $disp->description }}</td>
                                            <td class="py-3 px-3 font-mono text-gray-500 italic max-w-xs break-words">
                                                {{ $disp->resolution ?: 'No resolution notes logged.' }}
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $disp->status === 'resolved' ? 'bg-green-50 text-green-700' : ($disp->status === 'closed' ? 'bg-gray-150 text-gray-700' : 'bg-red-50 text-red-700') }}">
                                                    {{ $disp->status }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-right space-y-1">
                                                @if($disp->status === 'open')
                                                    <!-- Inline Resolution Box -->
                                                    <div class="flex flex-col items-end space-y-1.5 pt-1">
                                                        <textarea wire:model="dispute_resolution" placeholder="Enter resolution details..." rows="2"
                                                                  class="bg-white dark:bg-gray-900 border border-gray-350 dark:border-gray-700 rounded p-1.5 text-[9px] w-48 text-gray-900 dark:text-white focus:outline-none"></textarea>
                                                        @error('dispute_resolution') <p class="text-red-550 text-[9px]">{{ $message }}</p> @enderror
                                                        <div class="flex space-x-1">
                                                            <button type="button" 
                                                                    wire:click="resolveDispute({{ $disp->id }}, 'resolved')"
                                                                    class="bg-green-755 text-white text-[9px] font-bold px-2 py-1 rounded transition hover:bg-green-800">Resolve</button>
                                                            <button type="button" 
                                                                    wire:click="resolveDispute({{ $disp->id }}, 'closed')"
                                                                    class="bg-gray-500 text-white text-[9px] font-bold px-2 py-1 rounded transition hover:bg-gray-600">Close</button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 font-bold">Closed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-gray-400 font-bold">
                                                No disputes logged for this agent.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

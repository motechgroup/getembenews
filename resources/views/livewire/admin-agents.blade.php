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
        <button type="button" 
                wire:click="openForm()"
                class="bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm">
            Create Agent Account
        </button>
    </div>

    <!-- Feedback Banner -->
    @if(session()->has('message'))
        <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 text-green-700 dark:text-green-455 px-4 py-3 rounded-lg font-bold">
            {{ session('message') }}
        </div>
    @endif

    <!-- Form modal / block -->
    @if($isFormOpen)
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm space-y-4 font-semibold">
            <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ $agentId ? 'Edit Agent Account' : 'Create New Agent' }}
            </h3>

            <form wire:submit.prevent="saveAgent" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Agent Name</label>
                    <input type="text" wire:model="name" required placeholder="e.g. Samuel Mogaka"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('name') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Agent Location</label>
                    <input type="text" wire:model="location" required placeholder="e.g. Kisii Town"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('location') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Agent PIN (4 digits)</label>
                    <input type="text" wire:model="pin" required placeholder="e.g. 1234" maxlength="4"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('pin') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase font-bold text-gray-500">Commission Percentage (%)</label>
                    <input type="number" wire:model="commission_percentage" required min="0" max="100"
                           class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('commission_percentage') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-4 pt-4 flex space-x-2">
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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, location or PIN..." 
                   class="w-full bg-gray-55 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none mt-1">
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-850 text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                        <th class="py-3.5 px-4">Agent Name</th>
                        <th class="py-3.5 px-4 text-center">Location</th>
                        <th class="py-3.5 px-4 text-center">Commission Rate</th>
                        <th class="py-3.5 px-4 text-center">Announcements Submitted</th>
                        <th class="py-3.5 px-4 text-center">Total Revenue Generated</th>
                        <th class="py-3.5 px-4 text-center">Total Commission Earned</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-700 dark:text-gray-300">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $agent->name }}</div>
                                <div class="text-[9px] text-gray-400">PIN: <span class="font-mono font-bold">{{ $agent->pin }}</span> &bull; Agent ID: {{ $agent->id }}</div>
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
</div>

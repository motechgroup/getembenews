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
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 font-bold text-center">
        <!-- Announcements count -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm space-y-1">
            <div class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Paid Announcements Submitted</div>
            <div class="text-2xl font-black text-gray-900 dark:text-white">{{ $agent->total_announcements }}</div>
        </div>

        <!-- Commission -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-xl shadow-sm space-y-1">
            <div class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Total Commission Earned</div>
            <div class="text-2xl font-black text-green-700 dark:text-green-455">KSh {{ number_format($agent->total_commission) }}</div>
        </div>
    </div>

    <!-- Submissions table -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden space-y-4 p-6">
        <div>
            <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                Your Announcement Submissions Log
            </h3>
            <p class="text-[10px] text-gray-500 mt-1">
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
                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 font-semibold text-gray-700 dark:text-gray-300">
                    @forelse($announcements as $ann)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-850/50 transition">
                            <td class="py-4 px-4 text-gray-500 font-mono">
                                {{ $ann->created_at->format('M d, Y H:i') }}
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

</div>

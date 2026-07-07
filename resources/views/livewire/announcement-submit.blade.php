<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8" x-data="{ wordLimit: 200 }">
    <!-- Center Header -->
    <div class="text-center max-w-2xl mx-auto space-y-3 mb-10">
        <span class="bg-[#cc6c3b]/10 text-[#cc6c3b] text-[10px] font-black uppercase tracking-widest px-3.5 py-1 rounded-full border border-[#cc6c3b]/20">
            Airing Announcements
        </span>
        <h1 class="text-3xl font-serif font-black text-gray-900 dark:text-white leading-tight">
            Getembe Announcements Desk
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Submit announcements (such as funeral or community alerts) to be aired on Getembe TV, Radio, or both networks. Pricing is calculated dynamically per word.
        </p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Submit Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-955 border border-gray-200 dark:border-gray-850 rounded-xl p-6 sm:p-8 shadow-sm space-y-6">
                <h3 class="text-sm font-black uppercase text-gray-900 dark:text-white tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                    Submit New Announcement
                </h3>

                <form wire:submit.prevent="submitAnnouncement" class="space-y-4 text-xs font-semibold">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold">Your Name</label>
                            <input type="text" wire:model="visitor_name" required placeholder="e.g. John Nyabuto" 
                                   class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                            @error('visitor_name') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold">Phone Number</label>
                            <input type="text" wire:model="visitor_phone" required placeholder="e.g. +2547XXXXXXXX" 
                                   class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                            @error('visitor_phone') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold block mb-1">Submitted By</label>
                            <div class="flex items-center space-x-4 py-2">
                                <label class="inline-flex items-center text-gray-900 dark:text-white cursor-pointer">
                                    <input type="radio" wire:model.live="submitter_type" value="self" class="form-radio text-[#cc6c3b] focus:ring-[#cc6c3b]">
                                    <span class="ml-2">Self</span>
                                </label>
                                <label class="inline-flex items-center text-gray-900 dark:text-white cursor-pointer">
                                    <input type="radio" wire:model.live="submitter_type" value="agent" class="form-radio text-[#cc6c3b] focus:ring-[#cc6c3b]">
                                    <span class="ml-2">Agent</span>
                                </label>
                            </div>
                            @error('submitter_type') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                        </div>

                        @if($submitter_type === 'agent')
                            <div class="space-y-1">
                                <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold">Agent PIN Code</label>
                                <input type="password" wire:model="agent_pin" required placeholder="Enter 4-digit Agent PIN" maxlength="4" 
                                       class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                                @error('agent_pin') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold">Announcement Type</label>
                            <select wire:model="type" class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                                <option value="funeral">Funeral Announcement</option>
                                <option value="general">General Announcement</option>
                            </select>
                            @error('type') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold">Media Target</label>
                            <select wire:model.live="media" class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                                <option value="tv">TV Only (KSh {{ \App\Models\Setting::get('announcement_rate_tv', 5) }}/word)</option>
                                <option value="radio">Radio Only (KSh {{ \App\Models\Setting::get('announcement_rate_radio', 3) }}/word)</option>
                                <option value="both">Both TV & Radio (KSh {{ \App\Models\Setting::get('announcement_rate_both', 7) }}/word)</option>
                            </select>
                            @error('media') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold">Airing Duration (Days)</label>
                            <input type="number" wire:model.live="days_count" min="1" max="30" required 
                                   class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                            @error('days_count') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Announcement Content Textarea -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center text-[10px] font-bold text-gray-500 uppercase">
                            <label>Announcement Text Content</label>
                            <span :class="{'text-red-500': $wire.word_count > wordLimit}">
                                Word Count: <span class="font-bold text-gray-900 dark:text-white" x-text="$wire.word_count"></span> / 200 max
                            </span>
                        </div>
                        <textarea wire:model.live="content" rows="6" required placeholder="Write your announcement content here. Note that pricing is calculated dynamically per word..." 
                                  class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-3 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b] leading-relaxed"></textarea>
                        @error('content') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
                    </div>

                    <!-- Price breakdown summary -->
                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="space-y-1 text-xs">
                            <p class="font-bold text-gray-800 dark:text-gray-200">Payment Breakdown:</p>
                            <p class="text-[10px] text-gray-500">
                                <span class="font-bold text-gray-850 dark:text-white" x-text="$wire.word_count"></span> words 
                                &times; KSh <span class="font-bold text-gray-850 dark:text-white" x-text="$wire.rate"></span> / word 
                                &times; <span class="font-bold text-gray-850 dark:text-white" x-text="$wire.days_count"></span> days
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] uppercase font-bold text-gray-400 block">Total cost</span>
                            <span class="text-2xl font-black text-[#cc6c3b] tracking-tight">
                                KSh <span x-text="$wire.total_price"></span>
                            </span>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold py-3 rounded-lg text-xs transition uppercase tracking-wider shadow-sm flex items-center justify-center space-x-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Submit & Pay via M-Pesa</span>
                    </button>
                </form>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-center">
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">
                        Are you a registered Kisii County news agent? 
                        <button type="button" wire:click="openAgentLogin" class="text-[#cc6c3b] hover:text-orange-700 font-black hover:underline ml-1">
                            Agent Portal Login &rarr;
                        </button>
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Announcements board -->
        <div class="space-y-6">
            <div class="bg-gray-50 dark:bg-gray-955 border border-gray-200 dark:border-gray-850 rounded-xl p-5 space-y-4">
                <h3 class="text-xs font-black uppercase text-[#cc6c3b] tracking-wider flex items-center border-b border-gray-100 dark:border-gray-800 pb-2">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Active Announcements Board</span>
                </h3>

                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-1">
                    @forelse($announcements as $ann)
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-850 rounded-lg p-4 space-y-2 shadow-sm text-xs">
                            <div class="flex justify-between items-center">
                                <span class="bg-orange-50 dark:bg-orange-950/20 text-[#cc6c3b] text-[9px] font-black uppercase px-2 py-0.5 rounded tracking-wide">
                                    {{ $ann->type }} announcement
                                </span>
                                <span class="text-[9px] text-gray-400 font-medium">
                                    Airing on: <span class="font-bold text-gray-700 dark:text-gray-300 uppercase">{{ $ann->media }}</span>
                                </span>
                            </div>
                            <p class="text-gray-800 dark:text-gray-250 leading-relaxed italic">
                                "{{ $ann->content }}"
                            </p>
                            <div class="flex justify-between items-center text-[9px] text-gray-400 font-semibold pt-1.5 border-t border-gray-50 dark:border-gray-850">
                                <span>By: {{ $ann->visitor_name }}</span>
                                <span>Runs: {{ $ann->days_count }} days</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400 text-xs font-semibold">
                            No announcements active on board today.
                        </div>
                    @endforelse
                </div>

                @if($announcements->hasPages())
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                        {{ $announcements->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- M-Pesa STK Push Checkout Modal Overlay -->
    @if($showCheckoutModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
             x-data="{ countdown: 2 }"
             x-init="
                $on('start-stk-timer', () => {
                    countdown = 2;
                    let timer = setInterval(() => {
                        countdown--;
                        if (countdown <= 0) {
                            clearInterval(timer);
                            $wire.confirmPaymentSuccess();
                        }
                    }, 1000);
                });
             ">
            <div class="bg-white dark:bg-gray-950 border border-gray-250 dark:border-gray-800 max-w-md w-full rounded-2xl p-6 sm:p-8 space-y-6 shadow-2xl text-xs text-center">
                <!-- Header -->
                <div class="space-y-1">
                    <span class="bg-green-100 dark:bg-green-950/30 text-green-700 dark:text-green-455 font-black uppercase text-[10px] tracking-wider px-3 py-1 rounded-full">
                        M-Pesa STK Push Integration
                    </span>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white font-serif">
                        Awaiting Payment Confirmation
                    </h3>
                </div>

                @if($mpesa_status === 'idle')
                    <!-- Idle state: confirm phone -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-850 text-left space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-bold">Total Amount:</span>
                                <span class="text-base font-black text-[#cc6c3b]">KSh {{ $total_price }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-gray-400">Word Count:</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $word_count }} words</span>
                            </div>
                        </div>

                        <div class="space-y-1 text-left">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">M-Pesa Phone Number</label>
                            <input type="text" wire:model="phone_for_mpesa" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg p-2.5 font-mono text-center font-bold text-sm tracking-widest focus:outline-none dark:text-white">
                            @error('phone_for_mpesa') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="button" 
                                wire:click="triggerMpesaStkPush"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition uppercase tracking-wider flex items-center justify-center space-x-1.5">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Send STK Push</span>
                        </button>
                    </div>
                @elseif($mpesa_status === 'sending')
                    <!-- Sending / awaiting STK input -->
                    <div class="space-y-6 py-6 flex flex-col items-center justify-center">
                        <!-- Loading spinner -->
                        <div class="relative flex items-center justify-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-green-200 border-t-green-600"></div>
                            <span class="absolute text-[8px] font-black text-green-700 uppercase" x-text="countdown + 's'"></span>
                        </div>
                        <div class="space-y-2 max-w-xs mx-auto">
                            <p class="font-bold text-gray-800 dark:text-gray-200">Sending M-Pesa STK Push...</p>
                            <p class="text-[10px] text-gray-500 leading-normal">
                                We've sent a payment prompt to <span class="font-bold font-mono text-gray-800 dark:text-white" x-text="$wire.phone_for_mpesa"></span>. Please check your phone, enter your PIN and wait.
                            </p>
                        </div>
                    </div>
                @elseif($mpesa_status === 'success')
                    <!-- Success receipt -->
                    <div class="space-y-6">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <div class="h-12 w-12 rounded-full bg-green-100 dark:bg-green-950/40 flex items-center justify-center text-green-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h4 class="font-black text-gray-900 dark:text-white text-base">Payment Completed!</h4>
                            <p class="text-[10px] text-gray-400">Your announcement has been scheduled for moderation.</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl border border-gray-150 dark:border-gray-850 text-left space-y-2.5 font-medium">
                            <div class="flex justify-between items-center text-gray-550 dark:text-gray-400">
                                <span>Transaction Reference:</span>
                                <span class="font-mono font-bold text-gray-950 dark:text-white uppercase">{{ \App\Models\Announcement::find($currentAnnouncementId)?->payment_reference }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-550 dark:text-gray-400">
                                <span>Amount Billed:</span>
                                <span class="font-bold text-gray-950 dark:text-white">KSh {{ $total_price }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-550 dark:text-gray-400">
                                <span>Phone Number:</span>
                                <span class="font-mono font-bold text-gray-950 dark:text-white">{{ $phone_for_mpesa }}</span>
                            </div>
                        </div>

                        <button type="button" 
                                @click="$wire.showCheckoutModal = false"
                                class="w-full bg-gray-900 hover:bg-gray-850 dark:bg-white dark:hover:bg-gray-100 dark:text-black text-white font-bold py-2.5 rounded-lg transition uppercase tracking-wider">
                            Close Receipt
                        </button>
                    </div>
                @endif

                <!-- Cancel / Close button -->
                @if($mpesa_status !== 'sending')
                    <button type="button" 
                            @click="$wire.showCheckoutModal = false"
                            class="text-gray-400 hover:text-gray-600 text-[10px] font-bold uppercase tracking-wider block mx-auto hover:underline mt-2">
                        Cancel & Go Back
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- Agent Login Modal -->
    @if($showAgentLoginModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-950 border border-gray-250 dark:border-gray-800 max-w-sm w-full rounded-2xl p-6 sm:p-8 space-y-6 shadow-2xl text-xs text-left">
                <!-- Header -->
                <div class="space-y-1 text-center font-bold">
                    <span class="bg-orange-50 dark:bg-orange-950/20 text-[#cc6c3b] font-black uppercase text-[9px] tracking-wider px-3 py-1 rounded-full">
                        Kisii County News Agents
                    </span>
                    <h3 class="text-base font-black text-gray-900 dark:text-white font-serif mt-2 uppercase">
                        Agent Portal Login
                    </h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold mt-1">
                        Enter your secure 4-digit Agent PIN code to access your performance dashboard and commission logs.
                    </p>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="loginAsAgent" class="space-y-4">
                    <div class="space-y-1 font-bold">
                        <label class="text-[10px] uppercase text-gray-500 dark:text-gray-400">Agent PIN Code</label>
                        <input type="password" wire:model="login_pin" required placeholder="Enter 4-digit PIN" maxlength="4"
                               class="w-full text-center tracking-widest text-lg font-mono bg-gray-55 dark:bg-gray-900 border border-gray-250 dark:border-gray-800 rounded-lg p-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                        @error('login_pin') <p class="text-red-550 text-[10px] text-center mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex space-x-2 pt-2">
                        <button type="submit" 
                                class="flex-1 bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold py-2.5 rounded-lg transition uppercase tracking-wider text-[11px] shadow-sm">
                            Verify & Log In
                        </button>
                        <button type="button" 
                                wire:click="closeAgentLogin"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-2.5 rounded-lg transition uppercase tracking-wider text-[11px]">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

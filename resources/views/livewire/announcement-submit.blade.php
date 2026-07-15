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
                    @if($errors->any())
                        <div class="p-4 bg-red-50 dark:bg-red-950/20 border border-red-250 dark:border-red-800 text-red-650 dark:text-red-400 rounded-xl space-y-1.5 font-bold">
                            <div class="flex items-center space-x-1.5 text-xs text-red-700 dark:text-red-400">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Please resolve the following submission errors:</span>
                            </div>
                            <ul class="list-disc list-inside text-[10px] font-semibold text-red-500 dark:text-red-450 space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
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
                            <label class="text-gray-700 dark:text-gray-300 uppercase tracking-wide text-[10px] font-bold">Airing Date</label>
                            <input type="date" wire:model="airing_date" required 
                                   class="w-full bg-gray-55 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#cc6c3b]">
                            @error('airing_date') <p class="text-red-550 text-[10px]">{{ $message }}</p> @enderror
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
                            <span>
                                Word Count: <span class="font-bold text-gray-900 dark:text-white" x-text="$wire.word_count"></span>
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

            <!-- App Store Badges -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 space-y-3 shadow-sm text-center">
                <h3 class="text-xs font-black uppercase text-gray-950 dark:text-white tracking-wider">Download Our Digital App</h3>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-relaxed">
                    Download the Getembe Digital app to listen to radio or watch live TV updates on Kisii County.
                </p>
                <div class="flex items-center justify-center space-x-3 pt-1">
                    <a href="{{ \App\Models\Setting::get('app_play_store_url', 'https://play.google.com/store') }}" target="_blank" class="flex items-center space-x-2 bg-gray-900 hover:bg-gray-800 text-white px-3.5 py-1.5 rounded-lg border border-gray-800 transition shadow-sm">
                        <svg class="h-4 w-4 fill-current text-green-500" viewBox="0 0 24 24">
                            <path d="M3.609 1.814L13.783 12 3.609 22.186c-.185-.125-.306-.341-.306-.604V2.418c0-.263.121-.479.306-.604zM14.735 12.95l3.14 3.14-13.342 7.64c-.332.19-.74.076-.928-.255-.078-.139-.078-.309 0-.448l11.13-10.077zM4.605 1.613l13.342 7.64-3.14 3.14-11.13-10.077c-.078-.139-.078-.309 0-.448.188-.331.596-.445.928-.255zM15.688 12l3.447-3.447 3.522 2.016c.394.225.529.729.304 1.123-.075.132-.191.229-.33.278l-3.496 2.016L15.688 12z"/>
                        </svg>
                        <span class="text-[9px] font-black uppercase tracking-wider">Play Store</span>
                    </a>
                    <a href="{{ \App\Models\Setting::get('app_app_store_url', 'https://www.apple.com/app-store') }}" target="_blank" class="flex items-center space-x-2 bg-gray-950 hover:bg-gray-900 text-white px-3.5 py-1.5 rounded-lg border border-gray-905 transition shadow-sm">
                        <svg class="h-4 w-4 fill-current text-white" viewBox="0 0 24 24">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.17c.66-.81 1.11-1.93.99-3.06-1 .04-2.2.67-2.92 1.51-.62.73-1.16 1.87-1.01 2.98 1.11.08 2.24-.59 2.94-1.43z"/>
                        </svg>
                        <span class="text-[9px] font-black uppercase tracking-wider">App Store</span>
                    </a>
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
                                <span>
                                    @if($ann->airing_date)
                                        Airing: {{ $ann->airing_date->format('M d, Y') }} ({{ $ann->days_count }} {{ Str::plural('day', $ann->days_count) }})
                                    @else
                                        Runs: {{ $ann->days_count }} {{ Str::plural('day', $ann->days_count) }}
                                    @endif
                                </span>
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
             x-data="{ countdown: 60, timerId: null }"
             @start-stk-timer.window="
                 countdown = 5;
                 if (timerId) clearInterval(timerId);
                 timerId = setInterval(() => {
                     countdown--;
                     if (countdown <= 0) {
                         clearInterval(timerId);
                         $wire.confirmPaymentSuccess();
                     }
                 }, 1000);
             "
             @start-stk-query-timer.window="
                 countdown = 60;
                 if (timerId) clearInterval(timerId);
                 timerId = setInterval(() => {
                     countdown--;
                     if ($wire.mpesa_status === 'success' || $wire.mpesa_status === 'error') {
                         clearInterval(timerId);
                         return;
                     }
                     if (countdown <= 0) {
                         clearInterval(timerId);
                         $wire.set('mpesa_status', 'error');
                         $wire.set('mpesa_error_message', 'Payment verification timed out. Safaricom did not confirm the transaction in time.');
                     } else if (countdown % 3 === 0) {
                         $wire.checkMpesaPaymentStatus();
                     }
                 }, 1000);
             ">
            <div class="bg-white dark:bg-gray-955 border border-gray-250 dark:border-gray-800 max-w-md w-full rounded-2xl p-6 sm:p-8 space-y-6 shadow-2xl text-xs text-center">
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
                                class="w-full bg-green-650 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition uppercase tracking-wider flex items-center justify-center space-x-1.5">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Send STK Push</span>
                        </button>
                    </div>
                @elseif($mpesa_status === 'sending')
                    <!-- Sending / awaiting STK input -->
                    <div class="space-y-6 py-4 flex flex-col items-center justify-center">
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
                        
                        <button type="button" 
                                wire:click="checkMpesaPaymentStatus"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 rounded-lg transition uppercase tracking-wider text-[10px] shadow-sm">
                            I have paid (Confirm Status)
                        </button>
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
                @elseif($mpesa_status === 'error')
                    <!-- Error report -->
                    <div class="space-y-6">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <div class="h-12 w-12 rounded-full bg-red-100 dark:bg-red-950/20 flex items-center justify-center text-red-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h4 class="font-black text-gray-900 dark:text-white text-base">Payment Unverified</h4>
                            <p class="text-[10px] text-gray-500 max-w-xs mx-auto leading-normal">
                                {{ $mpesa_error_message ?: 'We could not verify your M-Pesa transaction. Please ensure you received the prompt, entered your PIN, and try again.' }}
                            </p>
                        </div>

                        <button type="button" 
                                wire:click="$set('mpesa_status', 'idle')"
                                class="w-full bg-[#C8102E] hover:bg-red-700 text-white font-bold py-2.5 rounded-lg transition uppercase tracking-wider">
                            Try Again / Change Phone
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

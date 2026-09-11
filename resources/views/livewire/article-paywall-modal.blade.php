<div class="my-8 rounded-2xl bg-white dark:bg-slate-900 text-gray-900 dark:text-white p-6 sm:p-8 border border-gray-200 dark:border-slate-800 shadow-2xl relative overflow-hidden" 
     x-data="{ pollTimer: null }"
     x-init="
        $wire.on('articleUnlocked', () => {
            window.location.reload();
        });
     ">
    
    <!-- Decorative background glow -->
    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-red-500/10 dark:bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-emerald-500/10 dark:bg-emerald-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header / Paywall Title -->
    <div class="relative z-10 text-center space-y-3 max-w-xl mx-auto">
        <div class="inline-flex items-center space-x-2 px-3 py-1 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 rounded-full text-[#C8102E] dark:text-red-400 text-[11px] font-black uppercase tracking-widest">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Exclusive Premium Story</span>
        </div>

        <h3 class="text-2xl sm:text-3xl font-serif font-black text-gray-900 dark:text-white leading-tight">
            Unlock Full Article Access
        </h3>
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
            Support independent investigative journalism. Select an option below to unlock this story or get unlimited access with a Getembe Pass.
        </p>
    </div>

    <!-- Options Selection Grid -->
    <div class="relative z-10 grid grid-cols-2 sm:grid-cols-4 gap-3 mt-8 max-w-2xl mx-auto">
        <!-- Option 1: Single Article -->
        <button type="button" wire:click="selectOption('article')" 
                class="p-4 rounded-xl border text-left transition relative flex flex-col justify-between cursor-pointer {{ $selectedOption === 'article' ? 'border-[#C8102E] bg-red-50 dark:bg-red-950/40 ring-2 ring-[#C8102E]/30 text-gray-900 dark:text-white' : 'border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/80 hover:bg-gray-100 dark:hover:bg-slate-750 text-gray-800 dark:text-gray-200' }}">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-300 block">This Story</span>
                <span class="text-xl font-black text-gray-900 dark:text-white mt-1 block">KSh {{ number_format($this->getPriceForOption('article')) }}</span>
            </div>
            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-2 block font-medium">One-time payment</span>
        </button>

        <!-- Option 2: Daily Pass -->
        <button type="button" wire:click="selectOption('daily')" 
                class="p-4 rounded-xl border text-left transition relative flex flex-col justify-between cursor-pointer {{ $selectedOption === 'daily' ? 'border-[#C8102E] bg-red-50 dark:bg-red-950/40 ring-2 ring-[#C8102E]/30 text-gray-900 dark:text-white' : 'border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/80 hover:bg-gray-100 dark:hover:bg-slate-750 text-gray-800 dark:text-gray-200' }}">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Daily Pass</span>
                <span class="text-xl font-black text-gray-900 dark:text-white mt-1 block">KSh {{ number_format($this->getPriceForOption('daily')) }}</span>
            </div>
            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-2 block font-medium">24 hours access</span>
        </button>

        <!-- Option 3: Weekly Pass -->
        <button type="button" wire:click="selectOption('weekly')" 
                class="p-4 rounded-xl border text-left transition relative flex flex-col justify-between cursor-pointer {{ $selectedOption === 'weekly' ? 'border-[#C8102E] bg-red-50 dark:bg-red-950/40 ring-2 ring-[#C8102E]/30 text-gray-900 dark:text-white' : 'border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/80 hover:bg-gray-100 dark:hover:bg-slate-750 text-gray-800 dark:text-gray-200' }}">
            <span class="absolute -top-2.5 right-2 px-2 py-0.5 bg-emerald-600 text-white text-[8px] font-black uppercase tracking-wider rounded-full shadow">Popular</span>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Weekly Pass</span>
                <span class="text-xl font-black text-gray-900 dark:text-white mt-1 block">KSh {{ number_format($this->getPriceForOption('weekly')) }}</span>
            </div>
            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-2 block font-medium">7 days access</span>
        </button>

        <!-- Option 4: Monthly Pass -->
        <button type="button" wire:click="selectOption('monthly')" 
                class="p-4 rounded-xl border text-left transition relative flex flex-col justify-between cursor-pointer {{ $selectedOption === 'monthly' ? 'border-[#C8102E] bg-red-50 dark:bg-red-950/40 ring-2 ring-[#C8102E]/30 text-gray-900 dark:text-white' : 'border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/80 hover:bg-gray-100 dark:hover:bg-slate-750 text-gray-800 dark:text-gray-200' }}">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">Monthly Pass</span>
                <span class="text-xl font-black text-gray-900 dark:text-white mt-1 block">KSh {{ number_format($this->getPriceForOption('monthly')) }}</span>
            </div>
            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-2 block font-medium">30 days access</span>
        </button>
    </div>

    <!-- Main Action Button (Triggers Payment Modal for Logged-in / Redirects to Register for Guests) -->
    <div class="relative z-10 max-w-md mx-auto mt-6 text-center space-y-3">
        <button type="button" wire:click="openPaymentModal" 
                class="w-full py-4 px-6 bg-gradient-to-r from-[#C8102E] to-red-700 hover:from-red-600 hover:to-red-800 text-white font-black text-xs sm:text-sm uppercase tracking-widest rounded-xl transition shadow-xl flex items-center justify-center space-x-2 cursor-pointer transform hover:-translate-y-0.5 group">
            <svg class="w-4 h-4 fill-current text-white/90 group-hover:scale-110 transition" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
            </svg>
            <span>
                @if($selectedOption === 'article')
                    Unlock This Story (KSh {{ number_format($this->getPriceForOption('article')) }})
                @else
                    Subscribe to {{ ucfirst($selectedOption) }} Pass (KSh {{ number_format($this->getPriceForOption($selectedOption)) }})
                @endif
            </span>
        </button>

        @guest
            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                Already have an account? <a href="{{ route('login') }}" class="font-extrabold text-[#C8102E] hover:underline">Sign In</a> to activate pass
            </p>
        @endguest
    </div>

    <!-- Interactive Payment Modal Dialog Overlay -->
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
             x-data
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl relative space-y-5 text-gray-900 dark:text-white"
                 @click.away="$wire.closePaymentModal()">
                
                <!-- Modal Close Button (X) -->
                <button type="button" wire:click="closePaymentModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white p-1 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Modal Header -->
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-xl font-bold border border-emerald-200 dark:border-emerald-800/40 shadow-sm">
                        📱
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white">
                        Lipa Na M-Pesa Checkout
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-300">
                        @if($selectedOption === 'article')
                            Unlocking <span class="font-extrabold text-gray-900 dark:text-white">"{{ Str::limit($article->title, 40) }}"</span> for <span class="font-black text-emerald-600 dark:text-emerald-400">KSh {{ number_format($this->getPriceForOption('article')) }}</span>
                        @else
                            Subscribing to <span class="font-extrabold text-gray-900 dark:text-white">{{ ucfirst($selectedOption) }} Pass</span> for <span class="font-black text-emerald-600 dark:text-emerald-400">KSh {{ number_format($this->getPriceForOption($selectedOption)) }}</span>
                        @endif
                    </p>
                </div>

                <!-- Status Message Banner -->
                @if(!empty($statusMessage))
                    <div class="p-3.5 rounded-xl text-xs font-bold leading-relaxed flex items-start space-x-2 
                                {{ $mpesaStatus === 'success' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : '' }}
                                {{ $mpesaStatus === 'pending' ? 'bg-amber-100 text-amber-900 border border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800' : '' }}
                                {{ $mpesaStatus === 'error' ? 'bg-red-100 text-red-900 border border-red-300 dark:bg-red-950/60 dark:text-red-300 dark:border-red-800' : '' }}
                                {{ $mpesaStatus === 'sending' ? 'bg-blue-100 text-blue-900 border border-blue-300 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800' : '' }}">
                        @if($mpesaStatus === 'pending' || $mpesaStatus === 'sending')
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 animate-spin shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        @endif
                        <span>{{ $statusMessage }}</span>
                    </div>
                @endif

                <!-- Payment Form -->
                @if($mpesaStatus !== 'pending' && $mpesaStatus !== 'success')
                    <form wire:submit.prevent="initiatePayment" class="space-y-4 pt-1">
                        <div>
                            <label for="mpesa_phone_modal" class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5 uppercase tracking-wider">
                                Safaricom M-Pesa Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 dark:text-emerald-400 font-black text-xs">
                                    📱 M-PESA
                                </div>
                                <input type="text" wire:model="phone" id="mpesa_phone_modal" placeholder="e.g. 0712345678" 
                                       class="w-full pl-24 pr-4 py-3 bg-white dark:bg-slate-950 border border-gray-300 dark:border-slate-700 rounded-xl text-gray-900 dark:text-white font-mono text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition shadow-sm">
                            </div>
                            @error('phone')
                                <span class="text-[11px] text-red-600 dark:text-red-400 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center space-x-3 pt-2">
                            <button type="button" wire:click="closePaymentModal" class="w-1/3 py-3 px-4 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300 font-extrabold text-xs uppercase tracking-wider rounded-xl transition text-center">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" 
                                    class="w-2/3 py-3.5 px-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white font-extrabold text-xs uppercase tracking-widest rounded-xl transition shadow-lg flex items-center justify-center space-x-2 group cursor-pointer">
                                <span wire:loading.remove>Send STK Push</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </form>
                @elseif($mpesaStatus === 'pending')
                    <!-- Live Polling Controls -->
                    <div class="text-center space-y-3 py-3" wire:poll.3s="checkPaymentStatus">
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 font-medium">Waiting for M-Pesa PIN input on your phone. Checking status automatically...</p>
                        <button type="button" wire:click="checkPaymentStatus" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            Tap here to check status now
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>

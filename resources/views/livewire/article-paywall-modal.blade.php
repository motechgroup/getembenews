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
            Support independent investigative journalism. Unlock this specific story or get unlimited access with a Getembe Pass.
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

    <!-- Payment & Authentication Area -->
    <div class="relative z-10 max-w-md mx-auto mt-6 bg-gray-50 dark:bg-slate-800/90 border border-gray-200 dark:border-slate-700 p-5 sm:p-6 rounded-2xl space-y-4 shadow-sm">
        @guest
            <!-- Guest Sign In Required Callout -->
            <div class="text-center space-y-4 py-2">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-600/20 text-[#C8102E] dark:text-red-400 flex items-center justify-center mx-auto text-xl font-bold border border-red-200 dark:border-red-500/30 shadow-sm">
                    🔐
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">Sign In Required to Pay</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                        Please sign in to your Getembe News account or create a new one to complete payment via M-Pesa and unlock premium stories.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-[#C8102E] to-red-700 hover:from-red-600 hover:to-red-800 text-white font-extrabold text-xs uppercase tracking-widest rounded-xl transition shadow-md text-center">
                        Sign In to Unlock
                    </a>
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-6 py-3 bg-gray-900 hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 text-white dark:text-gray-200 font-extrabold text-xs uppercase tracking-widest rounded-xl transition border border-gray-800 dark:border-gray-700 text-center">
                        Create Account
                    </a>
                </div>
            </div>
        @else
            <!-- Authenticated User M-Pesa STK Push Form -->
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

            @if($mpesaStatus !== 'pending' && $mpesaStatus !== 'success')
                <form wire:submit.prevent="initiatePayment" class="space-y-4">
                    <div>
                        <label for="mpesa_phone" class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5 uppercase tracking-wider">
                            Safaricom M-Pesa Number
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 dark:text-emerald-400 font-black text-xs">
                                📱 M-PESA
                            </div>
                            <input type="text" wire:model="phone" id="mpesa_phone" placeholder="e.g. 0712345678" 
                                   class="w-full pl-24 pr-4 py-3 bg-white dark:bg-slate-950 border border-gray-300 dark:border-slate-700 rounded-xl text-gray-900 dark:text-white font-mono text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition shadow-sm">
                        </div>
                        @error('phone')
                            <span class="text-[11px] text-red-600 dark:text-red-400 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" 
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white font-extrabold text-xs uppercase tracking-widest rounded-xl transition shadow-lg flex items-center justify-center space-x-2 group cursor-pointer">
                        <span wire:loading.remove>Pay KSh {{ number_format($this->getPriceForOption($selectedOption)) }} with M-Pesa STK Push</span>
                        <span wire:loading>Processing Request...</span>
                    </button>
                </form>
            @elseif($mpesaStatus === 'pending')
                <!-- Live Polling Controls -->
                <div class="text-center space-y-3 py-2" wire:poll.3s="checkPaymentStatus">
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 font-medium">Waiting for M-Pesa PIN input. Checking status automatically...</p>
                    <button type="button" wire:click="checkPaymentStatus" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                        Tap here to check status now
                    </button>
                </div>
            @endif
        @endguest
    </div>

</div>

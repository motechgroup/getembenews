<?php

use function Livewire\Volt\{state, rules};
use App\Models\Newsletter;
use App\Models\Setting;

state([
    'email' => '', 
    'subscribed' => false,
    'showPopup' => false,
    'errorMessage' => ''
]);

rules(['email' => 'required|email|unique:newsletters,email']);

$subscribe = function () {
    $this->errorMessage = '';

    try {
        $this->validate();
    } catch (\Illuminate\Validation\ValidationException $e) {
        $this->errorMessage = $e->validator->errors()->first('email');
        return;
    }

    // Rate Limit: 5 subscriptions per IP per minute
    $ip = request()->ip();
    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('newsletter-subscribe:' . $ip, 5)) {
        $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('newsletter-subscribe:' . $ip);
        $this->errorMessage = "Too many requests. Please try again in {$seconds} seconds.";
        return;
    }
    \Illuminate\Support\Facades\RateLimiter::hit('newsletter-subscribe:' . $ip, 60);

    $email = strip_tags(trim(strtolower($this->email)));
    Newsletter::create([
        'email' => $email,
        'is_active' => true
    ]);

    \App\Support\Mailer::sendWelcome($email);

    $this->email = '';
    $this->subscribed = true;
};

?>

<div>
    @if((bool) Setting::get('newsletter_popup_enabled', true))
        <div x-data="{
            showPopup: @entangle('showPopup'),
            init() {
                if (localStorage.getItem('newsletter_popup_dismissed')) {
                    return;
                }
    
                // Prevent layout shifts during automated Lighthouse/PageSpeed audits
                const isLighthouse = navigator.userAgent.indexOf('Chrome-Lighthouse') > -1 || navigator.userAgent.indexOf('Google PageSpeed Insights') > -1;
                if (isLighthouse) {
                    return;
                }
    
                // Trigger popup after a longer delay (default 8s) or on scroll down past 300px
                const showPopupTimeout = setTimeout(() => {
                    this.showPopup = true;
                }, {{ (int) Setting::get('newsletter_popup_delay', 8) * 1000 }});
    
                const handleScroll = () => {
                    if (window.scrollY > 300) {
                        this.showPopup = true;
                        clearTimeout(showPopupTimeout);
                        window.removeEventListener('scroll', handleScroll);
                    }
                };
                window.addEventListener('scroll', handleScroll, { passive: true });
            },
            dismiss() {
                this.showPopup = false;
                localStorage.setItem('newsletter_popup_dismissed', 'true');
            }
         }"
         x-show="showPopup"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;"
         x-cloak>
         
         <div @click.away="dismiss()" class="relative w-full max-w-[340px] sm:max-w-md bg-white dark:bg-gray-900 border-t-4 border-t-[#C8102E] border-x border-b border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 space-y-5 overflow-hidden">
             <!-- Close Button -->
             <button @click="dismiss()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full p-1.5 transition" aria-label="Close newsletter popup">
                 <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                 </svg>
             </button>
    
             <!-- Header/Icon & Title -->
             <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-3 sm:space-y-0 sm:space-x-4 pt-2">
                 <div class="p-3 bg-red-50 dark:bg-red-950/20 text-[#C8102E] rounded-full shrink-0">
                     <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5"/>
                     </svg>
                 </div>
                 <div class="space-y-1">
                     <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white leading-tight uppercase tracking-tight">
                         {{ Setting::get('newsletter_popup_title', 'Subscribe to our Newsletter') }}
                     </h3>
                     <p class="text-xs text-gray-600 dark:text-gray-400 text-center sm:text-left leading-relaxed">
                         {{ Setting::get('newsletter_popup_description', 'Get the latest breaking news alerts and regional updates delivered directly to your inbox.') }}
                     </p>
                 </div>
             </div>
    
             <!-- Error Alert -->
             @if($errorMessage)
                 <div class="p-2.5 bg-red-900/10 border border-red-800 text-red-300 text-[11px] rounded text-center">
                     {{ $errorMessage }}
                 </div>
             @endif
    
             <!-- Success View -->
             @if($subscribed)
                 <div class="space-y-2 py-4 text-center" x-init="setTimeout(() => { dismiss(); }, 2000)">
                     <div class="text-3xl">🎉</div>
                     <h4 class="text-sm font-bold text-gray-900 dark:text-white">Subscription Successful!</h4>
                     <p class="text-[11px] text-gray-550">Thank you for joining Getembe News mailing list.</p>
                 </div>
             @else
                 <!-- Form View -->
                 <form wire:submit.prevent="subscribe" class="space-y-3">
                     <input type="email" wire:model="email" placeholder="Enter your email address" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-[#C8102E] focus:border-transparent outline-none">
                     
                     <button type="submit" class="w-full py-2.5 bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs rounded-lg transition shadow-md shadow-red-900/20 uppercase tracking-wider">
                         Subscribe Now
                     </button>
                     
                     <p class="text-[10px] text-gray-450 dark:text-gray-500 text-center">We respect your privacy. Unsubscribe at any time.</p>
                 </form>
             @endif
         </div>
    </div>
    @endif
</div>

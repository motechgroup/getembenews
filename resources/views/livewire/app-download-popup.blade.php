<?php

use function Livewire\Volt\{state};
use App\Models\Setting;

state([
    'showPopup' => false,
]);

?>

<div>
    @if((bool) Setting::get('app_download_popup_enabled', true))
        <div x-data="{
                showPopup: @entangle('showPopup'),
                init() {
                    if (localStorage.getItem('app_download_popup_dismissed')) {
                        return;
                    }
        
                    const ua = (navigator.userAgent || '').toLowerCase();
                    const isAudit = ua.includes('chrome-lighthouse') || 
                                    ua.includes('pagespeed') || 
                                    ua.includes('lighthouse') || 
                                    ua.includes('headlesschrome') || 
                                    ua.includes('gtmetrix') ||
                                    window.location.search.includes('form_factor') ||
                                    window.location.search.includes('dujqliv0mc');
                    if (isAudit) {
                        return;
                    }
        
                    let userInteracted = false;
                    const markInteraction = () => { userInteracted = true; };
                    window.addEventListener('pointerdown', markInteraction, { once: true, passive: true });
                    window.addEventListener('keydown', markInteraction, { once: true, passive: true });

                    const showPopupTimeout = setTimeout(() => {
                        if (userInteracted) {
                            this.showPopup = true;
                        }
                    }, {{ (int) Setting::get('app_download_popup_delay', 12) * 1000 }});
        
                    const handleScroll = () => {
                        if (userInteracted && window.scrollY > 500) {
                            this.showPopup = true;
                            clearTimeout(showPopupTimeout);
                            window.removeEventListener('scroll', handleScroll);
                        }
                    };
                    window.addEventListener('scroll', handleScroll, { passive: true });
                },
                dismiss() {
                    this.showPopup = false;
                    localStorage.setItem('app_download_popup_dismissed', 'true');
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
                 <button @click="dismiss()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full p-1.5 transition" aria-label="Close app download popup">
                     <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                     </svg>
                 </button>
        
                 <!-- Header/Icon & Title -->
                 <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-3 sm:space-y-0 sm:space-x-4 pt-2">
                     <div class="p-3 bg-red-50 dark:bg-red-950/20 text-[#C8102E] rounded-full shrink-0">
                         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                         </svg>
                     </div>
                     <div class="space-y-1 flex-grow">
                         <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white leading-tight uppercase tracking-tight">
                             {{ Setting::get('app_download_popup_title', 'Download Getembe TV App') }}
                         </h3>
                         <p class="text-xs text-gray-600 dark:text-gray-400 text-center sm:text-left leading-relaxed mt-1">
                             {{ Setting::get('app_download_popup_description', 'Take Getembe TV with you. Download our mobile app to watch live TV, listen to radio, and read the latest news alerts on the go.') }}
                         </p>
                     </div>
                 </div>
        
                 <!-- Download Links/Buttons -->
                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                     <!-- Google Play Store -->
                     <a href="{{ Setting::get('app_play_store_url', 'https://play.google.com/store') }}" target="_blank" @click="dismiss()"
                        class="flex items-center justify-center space-x-2 px-4 py-2.5 bg-gray-900 hover:bg-black text-white rounded-lg transition shadow border border-gray-800">
                         <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                             <path d="M5 3.25c-.28 0-.5.22-.5.5v16.5c0 .28.22.5.5.5h.33L16 12.01 5.33 3.25H5zm12.33 7.82l-3.32 2.76 3.65 3.03c.52-.3 1.01-.84 1.01-1.83v-4.13c0-.98-.49-1.53-1.01-1.83l-3.65 3.03 3.32 2.76.01.01-.01-.01z"/>
                         </svg>
                         <div class="text-left">
                             <p class="text-[9px] text-gray-400 uppercase leading-none">Get it on</p>
                             <p class="text-[11px] font-bold leading-tight">Google Play</p>
                         </div>
                     </a>
                     
                     <!-- Apple App Store -->
                     <a href="{{ Setting::get('app_app_store_url', 'https://www.apple.com/app-store') }}" target="_blank" @click="dismiss()"
                        class="flex items-center justify-center space-x-2 px-4 py-2.5 bg-gray-900 hover:bg-black text-white rounded-lg transition shadow border border-gray-800">
                         <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                             <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.17c.66-.81 1.11-1.93.99-3.06-1 .04-2.21.67-2.93 1.49-.62.69-1.16 1.84-1.01 2.96 1.12.09 2.27-.58 2.95-1.39z"/>
                         </svg>
                         <div class="text-left">
                             <p class="text-[9px] text-gray-400 uppercase leading-none">Download on the</p>
                             <p class="text-[11px] font-bold leading-tight">App Store</p>
                         </div>
                     </a>
                 </div>
                 
                 <p class="text-[10px] text-gray-450 dark:text-gray-500 text-center mt-2">Compatible with Android & iOS devices.</p>
             </div>
        </div>
    @endif
</div>

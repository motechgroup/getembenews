@php
    $location = $location ?? 'top';
    $adsenseEnabled = \App\Models\Setting::get('adsense_enabled', false);
    $facebookAdsEnabled = \App\Models\Setting::get('facebook_ads_enabled', false);
    $customAdsEnabled = \App\Models\Setting::get('custom_ads_enabled', true);
    
    // Retrieve custom banner image & destination
    $bannerImage = \App\Models\Setting::get("ad_{$location}_image");
    $bannerLink = \App\Models\Setting::get("ad_{$location}_link");
@endphp

@if($adsenseEnabled && \App\Models\Setting::get('adsense_code'))
    <!-- Google AdSense Ad Block -->
    <div class="w-full text-center my-4">
        <span class="text-[8px] text-gray-400 block mb-1">ADVERTISEMENT (ADSENSE)</span>
        {!! \App\Models\Setting::get('adsense_code') !!}
    </div>
@elseif($facebookAdsEnabled && \App\Models\Setting::get('facebook_ads_code'))
    <!-- Facebook Audience Network Ad Block -->
    <div class="w-full text-center my-4">
        <span class="text-[8px] text-gray-400 block mb-1">ADVERTISEMENT (FACEBOOK)</span>
        {!! \App\Models\Setting::get('facebook_ads_code') !!}
    </div>
@elseif($customAdsEnabled)
    <!-- Custom Ads are enabled, show banner if exists or show placeholder fallback -->
    @if($bannerImage)
        <!-- Custom Advertisement Banner -->
        <div class="w-full text-center my-4">
            <a href="{{ $bannerLink ?: '/contact' }}" target="_blank" class="inline-block relative group">
                <img src="{{ $bannerImage }}" alt="Advertisement" class="mx-auto rounded max-h-36 object-cover shadow-sm hover:opacity-95 transition" loading="lazy">
                <span class="absolute top-1 left-1 bg-black/60 text-white text-[8px] px-1 rounded uppercase tracking-wider font-semibold">ADVERTISEMENT</span>
            </a>
        </div>
    @else
        <!-- Premium placeholder ad fallback -->
        @if($location === 'top')
            <div class="w-full bg-gray-50 dark:bg-gray-950 border border-gray-150 dark:border-gray-850 text-center py-8 rounded text-xs text-gray-450 font-medium tracking-wide">
                ADVERTISEMENT BANNER (728x90)
            </div>
        @elseif($location === 'sidebar')
            <a href="/contact" class="block w-full relative group">
                <div class="relative bg-gradient-to-br from-gray-900 to-black border border-gray-855 rounded-lg overflow-hidden min-h-[180px] flex flex-col justify-between p-5 text-left">
                    <div class="flex justify-center">
                        <span class="bg-yellow-500 text-black font-extrabold text-[8px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">Sponsor Getembe</span>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-black text-white leading-tight">Your Banner Here</h4>
                        <p class="text-[9px] text-gray-400 leading-snug">Place your ad here and reach Kisii's largest local digital news audience. Click to learn more.</p>
                    </div>
                </div>
            </a>
        @else
            <div class="w-full bg-gray-50 dark:bg-gray-955 border border-gray-150 dark:border-gray-850 text-center py-6 rounded text-[10px] text-gray-400 uppercase tracking-widest font-bold">
                ADVERTISEMENT
            </div>
        @endif
    @endif
@endif

@php
    $location = $location ?? 'top';
    $adsenseEnabled = filter_var(\App\Models\Setting::get('adsense_enabled', false), FILTER_VALIDATE_BOOLEAN);
    $facebookAdsEnabled = filter_var(\App\Models\Setting::get('facebook_ads_enabled', false), FILTER_VALIDATE_BOOLEAN);
    $customAdsEnabled = filter_var(\App\Models\Setting::get('custom_ads_enabled', false), FILTER_VALIDATE_BOOLEAN);
    
    $adsenseCode = \App\Models\Setting::get('adsense_code');
    $adsenseClientId = \App\Models\Setting::get('adsense_client_id');
    if (!empty($adsenseClientId) && !\Illuminate\Support\Str::startsWith($adsenseClientId, 'ca-pub-') && !\Illuminate\Support\Str::startsWith($adsenseClientId, 'pub-')) {
        $adsenseClientId = 'ca-' . $adsenseClientId;
    }

    // Retrieve active database advertisement placement
    $dbAd = null;
    try {
        $dbAd = \App\Models\Advertisement::active()->location($location)->inRandomOrder()->first();
        if ($dbAd) {
            $dbAd->increment('impressions');
        }
    } catch (\Throwable $e) {
        $dbAd = null;
    }

    // Retrieve custom banner setting image & destination link (from Admin -> Settings -> Advertising)
    $settingBanner = \App\Models\Setting::get("ad_{$location}_image");
    $settingLink = \App\Models\Setting::get("ad_{$location}_link");

    // Determine final banner image and target link
    $bannerImage = !empty($settingBanner) ? $settingBanner : ($dbAd && $dbAd->image_url ? $dbAd->image_url : null);
    $bannerLink = !empty($settingLink) ? $settingLink : ($dbAd && $dbAd->destination_url ? $dbAd->destination_url : '/contact');
    $dbScript = ($dbAd && empty($settingBanner)) ? $dbAd->script_code : null;

    // Standardized dimension constraints to ensure clean UI alignment
    $adContainerStyle = match($location) {
        'top' => 'max-w-[728px] max-h-[140px]',
        'sidebar' => 'max-w-[300px] max-h-[600px]',
        'inline' => 'max-w-[728px] max-h-[140px]',
        'footer' => 'max-w-[728px] max-h-[120px]',
        'mobile_sticky' => 'max-w-[320px] max-h-[60px]',
        'mobile_native' => 'max-w-[336px] max-h-[280px]',
        default => 'max-w-full max-h-48',
    };
@endphp

@if(!empty($dbScript))
    <!-- Active Script Code Ad -->
    <div class="w-full text-center my-4 overflow-hidden">
        <span class="text-[9px] text-gray-400 dark:text-gray-500 block mb-1 font-bold tracking-wider uppercase">ADVERTISEMENT</span>
        {!! $dbScript !!}
    </div>
@elseif(!empty($bannerImage))
    <!-- Custom Uploaded Banner Image (From Admin Settings or Campaign Manager) -->
    <div class="w-full text-center my-4 flex justify-center">
        <a href="{{ $bannerLink }}" target="_blank" class="inline-block relative group overflow-hidden rounded-md shadow-sm border border-gray-150 dark:border-gray-800 transition transform hover:scale-[1.005] {{ $adContainerStyle }}">
            <img src="{{ $bannerImage }}" alt="Advertisement Banner" class="w-full h-auto object-contain mx-auto" loading="lazy">
            <span class="absolute top-1 left-1 bg-black/60 backdrop-blur-sm text-white text-[8px] px-1.5 py-0.5 rounded uppercase tracking-wider font-bold z-10">ADVERTISEMENT</span>
        </a>
    </div>
@elseif($adsenseEnabled && (!empty($adsenseCode) || !empty($adsenseClientId)))
    <!-- Google AdSense Ad Block -->
    <div class="w-full text-center my-4 overflow-hidden min-h-[90px] flex flex-col items-center justify-center">
        <span class="text-[9px] text-gray-400 dark:text-gray-500 block mb-1 font-bold tracking-wider uppercase">ADVERTISEMENT</span>
        @if(!empty($adsenseCode))
            {!! $adsenseCode !!}
        @else
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="{{ $adsenseClientId }}"
                 data-ad-slot="auto"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        @endif
    </div>
@elseif($facebookAdsEnabled && \App\Models\Setting::get('facebook_ads_code'))
    <!-- Facebook Audience Network Ad Block -->
    <div class="w-full text-center my-4">
        <span class="text-[9px] text-gray-700 dark:text-gray-300 block mb-1 font-bold tracking-wider">ADVERTISEMENT (FACEBOOK)</span>
        {!! \App\Models\Setting::get('facebook_ads_code') !!}
    </div>
@else
    <!-- Premium placeholder ad fallback -->
    @if($location === 'top')
        <div class="w-full max-w-[728px] mx-auto bg-gray-50 dark:bg-gray-950 border border-gray-150 dark:border-gray-850 text-center py-6 rounded text-xs text-gray-450 font-medium tracking-wide">
            TOP LEADERBOARD AD (728 × 90 px)
        </div>
    @elseif($location === 'sidebar')
        <a href="/contact" class="block w-full max-w-[300px] mx-auto relative group">
            <div class="relative bg-gradient-to-br from-gray-900 to-black border border-gray-855 rounded-lg overflow-hidden min-h-[180px] flex flex-col justify-between p-5 text-left">
                <div class="flex justify-center">
                    <span class="bg-yellow-500 text-black font-extrabold text-[8px] px-2.5 py-0.5 rounded-full uppercase tracking-wider">Sponsor Getembe</span>
                </div>
                <div class="space-y-1">
                    <h4 class="text-xs font-black text-white leading-tight">Your Banner Here (300 × 250 px)</h4>
                    <p class="text-[9px] text-gray-400 leading-snug">Place your ad here and reach Kisii's largest local digital news audience. Click to learn more.</p>
                </div>
            </div>
        </a>
    @else
        <div class="w-full max-w-[728px] mx-auto bg-gray-50 dark:bg-gray-955 border border-gray-150 dark:border-gray-850 text-center py-5 rounded text-[10px] text-gray-700 dark:text-gray-300 uppercase tracking-widest font-bold">
            ADVERTISEMENT
        </div>
    @endif
@endif

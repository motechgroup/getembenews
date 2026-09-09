@props([
    'url' => null,
    'embedCode' => null,
    'type' => 'auto',
])

@php
    $url = trim($url ?? '');
    $embedCode = trim($embedCode ?? '');
    $type = strtolower(trim($type ?? 'auto'));

    $currentHost = request()->getHost();

    // Determine mode: 'code' or 'url'
    $isCodeMode = false;
    if ($type === 'code' || (!empty($embedCode) && empty($url))) {
        $isCodeMode = true;
    } elseif ($type === 'auto' && !empty($embedCode)) {
        if (preg_match('/<(iframe|script|div|video|embed|object)/i', $embedCode)) {
            $isCodeMode = true;
        }
    }

    if ($isCodeMode && !empty($embedCode)) {
        // Automatically ensure Twitch iframe embeds have the current domain parent attribute
        if (Str::contains($embedCode, 'player.twitch.tv')) {
            if (preg_match('/parent=([^&"\'\s>]+)/i', $embedCode)) {
                $embedCode = preg_replace('/parent=([^&"\'\s>]+)/i', "parent={$currentHost}", $embedCode);
            } else {
                $embedCode = preg_replace('/(player\.twitch\.tv\/\?[^"\'\s>]+)/i', "$1&parent={$currentHost}", $embedCode);
            }
        }
    }

    // Stream URL Parsing logic if in URL mode
    $playerType = 'iframe';
    $iframeSrc = '';
    $videoSrc = '';

    if (!$isCodeMode && !empty($url)) {
        if (preg_match('/twitch\.tv\/([a-zA-Z0-9_]+)/i', $url, $matches)) {
            $pathOrChannel = $matches[1];
            if (strtolower($pathOrChannel) === 'videos' && preg_match('/twitch\.tv\/videos\/([0-9]+)/i', $url, $vMatches)) {
                $iframeSrc = "https://player.twitch.tv/?video={$vMatches[1]}&parent={$currentHost}&autoplay=true";
            } else {
                $iframeSrc = "https://player.twitch.tv/?channel={$pathOrChannel}&parent={$currentHost}&autoplay=true";
            }
            $playerType = 'iframe';
        } elseif (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches)) {
            $youtubeId = $matches[1];
            $iframeSrc = "https://www.youtube.com/embed/{$youtubeId}?autoplay=1&mute=0&rel=0";
            $playerType = 'iframe';
        } elseif (Str::contains(strtolower($url), '.m3u8') || Str::contains(strtolower($url), 'hls')) {
            $videoSrc = $url;
            $playerType = 'hls';
        } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $url)) {
            $videoSrc = $url;
            $playerType = 'video';
        } else {
            $iframeSrc = $url;
            $playerType = 'iframe';
        }
    }

    $playerId = 'tv-player-' . Str::random(8);
@endphp

<div id="{{ $playerId }}-wrapper" 
     x-data="{ 
         isFullscreen: false,
         toggleFullscreen() {
             var elem = document.getElementById('{{ $playerId }}-wrapper');
             if (!elem) return;
             if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                 if (elem.requestFullscreen) {
                     elem.requestFullscreen();
                 } else if (elem.webkitRequestFullscreen) {
                     elem.webkitRequestFullscreen();
                 } else if (elem.msRequestFullscreen) {
                     elem.msRequestFullscreen();
                 }
                 this.isFullscreen = true;
             } else {
                 if (document.exitFullscreen) {
                     document.exitFullscreen();
                 } else if (document.webkitExitFullscreen) {
                     document.webkitExitFullscreen();
                 } else if (document.msExitFullscreen) {
                     document.msExitFullscreen();
                 }
                 this.isFullscreen = false;
             }
         }
     }"
     @fullscreenchange.window="isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement)"
     @webkitfullscreenchange.window="isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement)"
     class="relative group w-full bg-black rounded-lg overflow-hidden border border-gray-800 shadow-2xl transition-all duration-300"
     :class="isFullscreen ? 'fixed inset-0 z-[999999] rounded-none border-0 w-screen h-screen' : 'aspect-video'">

    <style>
        #{{ $playerId }}-container,
        #{{ $playerId }}-container > div,
        #{{ $playerId }}-container iframe,
        #{{ $playerId }}-container video,
        #{{ $playerId }}-container embed,
        #{{ $playerId }}-container object {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            min-width: 100% !important;
            min-height: 100% !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            border: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            object-fit: fill !important;
        }
    </style>

    <!-- Player Inner Container -->
    <div id="{{ $playerId }}-container" class="absolute inset-0 w-full h-full bg-black flex items-center justify-center overflow-hidden">
        @if($isCodeMode)
            {!! $embedCode !!}
        @elseif(!empty($url))
            @if($playerType === 'iframe')
                <iframe 
                    src="{{ $iframeSrc }}" 
                    title="Getembe Live TV Broadcast" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" 
                    allowfullscreen>
                </iframe>
            @elseif($playerType === 'hls')
                <video id="{{ $playerId }}-video" controls autoplay playsinline class="w-full h-full object-contain bg-black">
                    <source src="{{ $videoSrc }}" type="application/x-mpegURL">
                    Your browser does not support HTML5 video streaming.
                </video>
                <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var video = document.getElementById('{{ $playerId }}-video');
                        var videoSrc = @json($videoSrc);
                        if (video) {
                            if (Hls.isSupported()) {
                                var hls = new Hls({ debug: false, enableWorker: true, lowLatencyMode: true });
                                hls.loadSource(videoSrc);
                                hls.attachMedia(video);
                                hls.on(Hls.Events.MANIFEST_PARSED, function () {
                                    video.play().catch(function(e) {});
                                });
                            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                video.src = videoSrc;
                                video.addEventListener('loadedmetadata', function () {
                                    video.play().catch(function(e) {});
                                });
                            }
                        }
                    });
                </script>
            @else
                <video controls autoplay playsinline class="w-full h-full object-contain bg-black">
                    <source src="{{ $videoSrc }}">
                    Your browser does not support HTML5 video.
                </video>
            @endif
        @else
            <div class="text-center p-6 space-y-3 text-gray-500">
                <svg class="h-12 w-12 mx-auto text-gray-700 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Live Stream Offline / Standby</p>
            </div>
        @endif
    </div>

    <!-- Sleek Overlay Controls (Full Screen Toggle) -->
    <div class="absolute top-3 right-3 z-30 opacity-80 hover:opacity-100 transition duration-300">
        <button type="button" 
                @click="toggleFullscreen()" 
                class="bg-black/80 hover:bg-[#C8102E] text-white p-2 sm:px-3 sm:py-1.5 rounded-lg border border-white/20 shadow-xl backdrop-blur-md flex items-center space-x-1.5 text-xs font-bold transition">
            <template x-if="!isFullscreen">
                <div class="flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <span class="hidden sm:inline">Fullscreen</span>
                </div>
            </template>
            <template x-if="isFullscreen">
                <div class="flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Exit Fullscreen</span>
                </div>
            </template>
        </button>
    </div>

</div>

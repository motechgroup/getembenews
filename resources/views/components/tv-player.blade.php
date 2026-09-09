@props([
    'url' => null,
    'embedCode' => null,
    'type' => 'auto',
])

@php
    $url = trim($url ?? '');
    $embedCode = trim($embedCode ?? '');
    $type = strtolower(trim($type ?? 'auto'));

    // Determine mode: 'code' or 'url'
    $isCodeMode = false;
    if ($type === 'code' || (!empty($embedCode) && empty($url))) {
        $isCodeMode = true;
    } elseif ($type === 'auto' && !empty($embedCode)) {
        // If embed code contains HTML tags like iframe, script, div, object, embed
        if (preg_match('/<(iframe|script|div|video|embed|object)/i', $embedCode)) {
            $isCodeMode = true;
        }
    }

    $currentHost = request()->getHost();

    // Stream URL Parsing logic if in URL mode
    $playerType = 'iframe';
    $iframeSrc = '';
    $videoSrc = '';
    $isHls = false;

    if (!$isCodeMode && !empty($url)) {
        // 1. Twitch Channel or Video URL
        if (preg_match('/twitch\.tv\/([a-zA-Z0-9_]+)/i', $url, $matches)) {
            $pathOrChannel = $matches[1];
            if (strtolower($pathOrChannel) === 'videos' && preg_match('/twitch\.tv\/videos\/([0-9]+)/i', $url, $vMatches)) {
                $iframeSrc = "https://player.twitch.tv/?video={$vMatches[1]}&parent={$currentHost}&autoplay=true";
            } else {
                $iframeSrc = "https://player.twitch.tv/?channel={$pathOrChannel}&parent={$currentHost}&autoplay=true";
            }
            $playerType = 'iframe';
        }
        // 2. YouTube URL
        elseif (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches)) {
            $youtubeId = $matches[1];
            $iframeSrc = "https://www.youtube.com/embed/{$youtubeId}?autoplay=1&mute=0&rel=0";
            $playerType = 'iframe';
        }
        // 3. HLS Stream (.m3u8)
        elseif (Str::contains(strtolower($url), '.m3u8') || Str::contains(strtolower($url), 'hls')) {
            $videoSrc = $url;
            $playerType = 'hls';
            $isHls = true;
        }
        // 4. MP4, WebM, OGG Video files
        elseif (preg_match('/\.(mp4|webm|ogg)$/i', $url)) {
            $videoSrc = $url;
            $playerType = 'video';
        }
        // 5. Generic Iframe Embed URL (e.g. OneStream, Kick, Vimeo, etc.)
        else {
            $iframeSrc = $url;
            $playerType = 'iframe';
        }
    }

    $playerId = 'tv-player-' . Str::random(8);
@endphp

<div class="aspect-video rounded-lg overflow-hidden bg-black relative border border-gray-800 shadow-2xl w-full h-full flex items-center justify-center [&_iframe]:w-full [&_iframe]:h-full [&_iframe]:border-0 [&_iframe]:aspect-video">

    @if($isCodeMode)
        {{-- Custom Embed Code (Twitch JS/Iframe, OneStream embed code, etc.) --}}
        <div class="w-full h-full flex items-center justify-center overflow-hidden [&_iframe]:w-full [&_iframe]:h-full [&_iframe]:aspect-video">
            {!! $embedCode !!}
        </div>

    @elseif(!empty($url))

        @if($playerType === 'iframe')
            {{-- Responsive Iframe Player (Twitch, YouTube, OneStream, Kick, Vimeo, etc.) --}}
            <iframe 
                src="{{ $iframeSrc }}" 
                title="Getembe Live TV Broadcast" 
                class="w-full h-full border-0" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                allowfullscreen>
            </iframe>

        @elseif($playerType === 'hls')
            {{-- HLS Stream (.m3u8) Player with HLS.js Fallback --}}
            <video id="{{ $playerId }}" controls autoplay playsinline class="w-full h-full object-contain bg-black">
                <source src="{{ $videoSrc }}" type="application/x-mpegURL">
                Your browser does not support HTML5 video streaming.
            </video>

            <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var video = document.getElementById('{{ $playerId }}');
                    var videoSrc = @json($videoSrc);

                    if (video) {
                        if (Hls.isSupported()) {
                            var hls = new Hls({
                                debug: false,
                                enableWorker: true,
                                lowLatencyMode: true
                            });
                            hls.loadSource(videoSrc);
                            hls.attachMedia(video);
                            hls.on(Hls.Events.MANIFEST_PARSED, function () {
                                video.play().catch(function(e) {
                                    console.log('Autoplay prevented:', e);
                                });
                            });
                        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                            video.src = videoSrc;
                            video.addEventListener('loadedmetadata', function () {
                                video.play().catch(function(e) {
                                    console.log('Autoplay prevented:', e);
                                });
                            });
                        }
                    }
                });
            </script>

        @else
            {{-- Standard HTML5 Video Player --}}
            <video controls autoplay playsinline class="w-full h-full object-contain bg-black">
                <source src="{{ $videoSrc }}">
                Your browser does not support HTML5 video.
            </video>
        @endif

    @else
        {{-- Fallback when no stream or code is provided --}}
        <div class="text-center p-6 space-y-3 text-gray-500">
            <svg class="h-12 w-12 mx-auto text-gray-700 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Live Stream Offline / Standby</p>
        </div>
    @endif

</div>

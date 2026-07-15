<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': $store.theme.darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Theme Detection -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- SEO Metadata -->
    @php
        $siteName = \App\Models\Setting::get('site_name', 'Getembe News');
        $parts = explode(' ', trim($siteName), 2);
        $firstWord = $parts[0] ?? 'Getembe';
        $secondWord = $parts[1] ?? 'News';

        $brandColor = \App\Models\Setting::get('brand_color', '#cc6c3b');
        $footerBgColor = \App\Models\Setting::get('footer_bg_color', '#111827');
        $footerTextColor = \App\Models\Setting::get('footer_text_color', '#9CA3AF');

        $defaultHeader = [
            ['label' => 'News', 'url' => '/'],
            ['label' => 'Counties', 'url' => '#counties'],
            ['label' => 'Politics', 'url' => '/politics'],
            ['label' => 'Business', 'url' => '/business'],
            ['label' => 'Entertainment', 'url' => '/entertainment'],
            ['label' => 'Sports', 'url' => '/sports'],
            ['label' => 'Video', 'url' => '/tv'],
        ];
        $filterMenu = function($items) use (&$filterMenu) {
            return array_values(array_filter(array_map(function($item) use ($filterMenu) {
                if (!empty($item['is_disabled'])) {
                    return null;
                }
                if (!empty($item['children'])) {
                    $item['children'] = $filterMenu($item['children']);
                }
                return $item;
            }, $items)));
        };
        $headerLinks = $filterMenu(\App\Models\Setting::get('header_menu', $defaultHeader));
    @endphp
    <title>{{ isset($title) ? $title : $siteName . ' - Fast, Reliable News & Analysis' }}</title>
    <meta name="description" content="{{ isset($metaDescription) ? $metaDescription : $siteName . ' is your leading source for politics, business, technology, sports, opinion, and global news.' }}">
    <link rel="canonical" href="{{ \App\Support\Seo::canonicalUrl() }}">

    <!-- Smart Indexing Logic -->
    @if(\App\Support\Seo::shouldIndex(get_defined_vars()))
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, follow">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ \App\Support\Seo::canonicalUrl() }}">
    <meta property="og:title" content="{{ isset($title) ? $title : $siteName }}">
    <meta property="og:description" content="{{ isset($metaDescription) ? $metaDescription : $siteName . ' is your leading source for politics, business, technology, sports, and global news.' }}">
    <meta property="og:image" content="{{ isset($metaImage) ? (\Illuminate\Support\Str::startsWith($metaImage, 'http') ? $metaImage : asset($metaImage)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=600&h=400' }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ \App\Support\Seo::canonicalUrl() }}">
    <meta property="twitter:title" content="{{ isset($title) ? $title : $siteName }}">
    <meta property="twitter:description" content="{{ isset($metaDescription) ? $metaDescription : $siteName . ' is your leading source for politics, business, technology, sports, and global news.' }}">
    <meta property="twitter:image" content="{{ isset($metaImage) ? (\Illuminate\Support\Str::startsWith($metaImage, 'http') ? $metaImage : asset($metaImage)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=600&h=400' }}">

    <!-- JSON-LD Structured Data Schema -->
    {!! \App\Support\Seo::generateSchema(get_defined_vars()) !!}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <!-- Instrument Sans is compiled via Vite, but fallback to Inter -->
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|playfair-display:700&display=swap" rel="stylesheet" />

    <!-- Dynamic Theme Engine -->
    <style>
        :root {
            --brand-color: {{ $brandColor }};
        }
        .text-\[\#C8102E\] { color: var(--brand-color) !important; }
        .bg-\[\#C8102E\] { background-color: var(--brand-color) !important; }
        .border-\[\#C8102E\] { border-color: var(--brand-color) !important; }
        
        .hover\:text-\[\#C8102E\]:hover { color: var(--brand-color) !important; }
        .hover\:bg-\[\#C8102E\]:hover { background-color: var(--brand-color) !important; }
        .hover\:border-\[\#C8102E\]:hover { border-color: var(--brand-color) !important; }
        
        .group:hover .group-hover\:text-\[\#C8102E\] { color: var(--brand-color) !important; }
        .group:hover .group-hover\:bg-\[\#C8102E\] { background-color: var(--brand-color) !important; }
        .group:hover .group-hover\:border-\[\#C8102E\] { border-color: var(--brand-color) !important; }
        
        .border-l-\[\#C8102E\] { border-left-color: var(--brand-color) !important; }
        .border-t-\[\#C8102E\] { border-top-color: var(--brand-color) !important; }
        .border-b-\[\#C8102E\] { border-bottom-color: var(--brand-color) !important; }
        .border-r-\[\#C8102E\] { border-right-color: var(--brand-color) !important; }
        .hover\:bg-red-700:hover { background-color: var(--brand-color) !important; filter: brightness(0.95); }
    </style>

    <!-- Google Analytics (gtag.js) -->
    @php
        $googleAnalyticsId = \App\Models\Setting::get('google_analytics_id');
    @endphp
    @if(!empty($googleAnalyticsId))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    const script = document.createElement('script');
                    script.src = "https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}";
                    script.async = true;
                    document.head.appendChild(script);

                    window.dataLayer = window.dataLayer || [];
                    window.gtag = function(){dataLayer.push(arguments);}
                    window.gtag('js', new Date());
                    window.gtag('config', '{{ $googleAnalyticsId }}');
                }, 3000);
            });
        </script>
    @endif

    <!-- Google AdSense Auto Ads -->
    @php
        $adsenseEnabled = \App\Models\Setting::get('adsense_enabled', false);
        $adsenseClientId = \App\Models\Setting::get('adsense_client_id');
    @endphp
    @if($adsenseEnabled && !empty($adsenseClientId))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    const script = document.createElement('script');
                    script.src = "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}";
                    script.async = true;
                    script.crossOrigin = "anonymous";
                    document.head.appendChild(script);
                }, 4000);
            });
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-gray-900 bg-white dark:bg-gray-900 dark:text-gray-100 antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Main Al Jazeera-style Header -->
    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-850 sticky top-0 z-50 transition-colors duration-200" x-data="{ mobileMenuOpen: false }">
        <!-- Top Info Bar (Black background) -->
        <div class="bg-gray-950 text-gray-400 text-[10px] sm:text-xs py-2 px-4 sm:px-6 border-b border-gray-900">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <!-- Left: Email & Phone -->
                <div class="flex items-center space-x-4">
                    <a href="mailto:info@getembetv.co.ke" class="flex items-center space-x-1 hover:text-white transition">
                        <svg class="h-3.5 w-3.5 text-[#cc6c3b]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="font-bold text-gray-300">info@getembetv.co.ke</span>
                    </a>
                    <a href="tel:+254143567165" class="flex items-center space-x-1 hover:text-white transition">
                        <svg class="h-3.5 w-3.5 text-[#cc6c3b]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="font-bold text-gray-300">+254143567165</span>
                    </a>
                </div>
                <!-- Right: Announcements | Contact Us -->
                <div class="flex items-center space-x-3 uppercase tracking-wider font-bold text-[9px] sm:text-[10px]">
                    <a href="/announcements" class="hover:text-white transition text-gray-300">Announcements</a>
                    <span class="text-gray-800">|</span>
                    <a href="/contact" class="hover:text-white transition text-gray-300">Contact Us</a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6">
            <!-- Left Side: Logo Emblem (Getembe Digital) -->
            <div class="flex items-center space-x-6 h-full shrink-0">
                <a href="/" class="flex items-center overflow-hidden rounded-md border border-gray-800 dark:border-gray-700 shadow-sm">
                    <!-- Left: Orange -->
                    <div class="bg-[#cc6c3b] px-3.5 py-1.5 text-white font-sans font-black tracking-tight text-xs sm:text-sm uppercase">
                        {{ $firstWord }}
                    </div>
                    <!-- Right: Dark Gray/Black -->
                    <div class="bg-gray-900 px-3.5 py-1.5 text-white font-sans font-black tracking-tight text-xs sm:text-sm uppercase border-l border-gray-800 dark:border-gray-700">
                        {{ $secondWord }}
                    </div>
                </a>
            </div>

            <!-- Center Side: Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-5 h-full text-xs font-black tracking-wider text-gray-900 dark:text-gray-200">
                @foreach($headerLinks as $link)
                    @if(!empty($link['children']))
                        <!-- Dynamic Dropdown Menu -->
                        <div class="relative group py-5" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false">
                            <button @click="open = !open" class="hover:text-[#cc6c3b] dark:hover:text-[#cc6c3b] transition flex items-center space-x-1 focus:outline-none font-black uppercase">
                                <span>{{ $link['label'] }}</span>
                                <svg class="h-3 w-3 text-gray-400 group-hover:text-[#cc6c3b]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-0 w-48 bg-white dark:bg-gray-955 border border-gray-200 dark:border-gray-800 rounded-lg shadow-lg py-2 z-50"
                                 style="display: none;">
                                @foreach($link['children'] as $child)
                                    <a href="{{ $child['url'] }}" class="block px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-[#cc6c3b] transition uppercase">{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @elseif(strtolower($link['label']) === 'counties' || strtolower($link['url']) === '#counties' || strtolower($link['url']) === 'counties')
                        <!-- COUNTIES Dropdown -->
                        <div class="relative group py-5" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.away="open = false">
                            <button @click="open = !open" class="hover:text-[#cc6c3b] dark:hover:text-[#cc6c3b] transition flex items-center space-x-1 focus:outline-none font-black uppercase">
                                <span>{{ $link['label'] }}</span>
                                <svg class="h-3 w-3 text-gray-400 group-hover:text-[#cc6c3b]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-0 w-48 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-lg py-2 z-50"
                                 style="display: none;">
                                <a href="/kisii" class="block px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-[#cc6c3b] transition">Kisii County</a>
                                <a href="/nyamira" class="block px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-[#cc6c3b] transition">Nyamira County</a>
                                <a href="/migori" class="block px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-[#cc6c3b] transition">Migori County</a>
                                <a href="/kisumu" class="block px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-[#cc6c3b] transition">Kisumu County</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ $link['url'] }}" class="hover:text-[#cc6c3b] dark:hover:text-[#cc6c3b] transition py-5 uppercase font-black">
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach

                <!-- TV link -->
                @if(\App\Models\Setting::get('live_tv_active', '1') == '1')
                <a href="/tv" class="hover:text-red-600 transition flex items-center space-x-1 py-5 text-red-500 font-extrabold tracking-wider">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>
                    <span>TV</span>
                </a>
                @endif

                <!-- RADIO link -->
                @if(\App\Models\Setting::get('live_radio_active', '1') == '1')
                <a href="/live-radio" class="hover:text-blue-600 transition flex items-center space-x-1 py-5 text-blue-500 font-extrabold tracking-wider">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                    <span>RADIO</span>
                </a>
                @endif
            </nav>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-4">


                <!-- Theme Toggle -->
                <button @click="$store.theme.toggle()" class="p-1.5 text-gray-700 dark:text-gray-300 hover:text-[#cc6c3b] focus:outline-none transition" aria-label="Toggle dark mode">
                    <svg x-show="!$store.theme.darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="$store.theme.darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                </button>

                <!-- Inline Search Form / Toggle -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-1.5 text-gray-700 dark:text-gray-300 hover:text-[#cc6c3b] focus:outline-none transition" aria-label="Toggle search bar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    <!-- Expandable search bar -->
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-900 border border-gray-205 dark:border-gray-800 rounded-lg shadow-lg p-2 z-50" style="display: none;">
                        <form action="/search" method="GET">
                            <input type="text" name="q" placeholder="Search news..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md py-1.5 px-3 text-xs focus:outline-none focus:ring-1 focus:ring-[#cc6c3b] dark:text-white">
                        </form>
                    </div>
                </div>

                <!-- Sign In / Dashboard Button (Vibrant screenshot match) -->
                @auth
                    <div class="relative shrink-0 hidden sm:block" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" type="button" class="bg-gray-105 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 font-bold text-xs px-4 py-2.5 rounded-lg transition border border-gray-200 dark:border-gray-700 shadow-sm flex items-center space-x-1.5 focus:outline-none uppercase tracking-wider">
                            <span>Profile</span>
                            <svg class="h-3.5 w-3.5 transform transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 py-1 z-50 focus:outline-none" style="display: none;">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-305 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#cc6c3b] transition">
                                📊 Dashboard
                            </a>
                            <a href="{{ route('profile') }}" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-350 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#cc6c3b] transition">
                                ⚙️ Settings
                            </a>
                            <hr class="border-gray-100 dark:border-gray-800 my-1">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-955/20 transition">
                                    🚪 Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-block bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold text-xs px-5 py-2.5 rounded-lg transition shadow-sm shrink-0 uppercase tracking-wider">
                        Sign In
                    </a>
                @endauth

                <!-- Mobile Menu Toggle Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-1.5 text-gray-700 dark:text-gray-300 hover:text-[#cc6c3b] focus:outline-none transition" aria-label="Toggle mobile menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" x-show="!mobileMenuOpen"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-show="mobileMenuOpen" style="display: none;"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Nav Menu -->
        <div x-show="mobileMenuOpen" x-transition class="lg:hidden bg-white dark:bg-gray-900 border-t border-gray-150 dark:border-gray-800 py-3" style="display: none;">
            <div class="px-4 space-y-1">
                @foreach($headerLinks as $link)
                    @if(!empty($link['children']))
                        <!-- Dynamic Mobile Submenu -->
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open" class="w-full text-left px-3 py-2 rounded text-sm font-bold text-gray-900 dark:text-gray-250 hover:bg-gray-100 dark:hover:bg-gray-855 hover:text-[#cc6c3b] flex justify-between items-center focus:outline-none uppercase">
                                <span>{{ $link['label'] }}</span>
                                <svg class="h-4 w-4 transform transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" class="pl-4 space-y-1" style="display: none;">
                                @foreach($link['children'] as $child)
                                    <a href="{{ $child['url'] }}" class="block px-3 py-1.5 rounded text-xs font-bold text-gray-650 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#cc6c3b] uppercase">{{ $child['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @elseif(strtolower($link['label']) === 'counties' || strtolower($link['url']) === '#counties' || strtolower($link['url']) === 'counties')
                        <!-- Counties submenu -->
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open" class="w-full text-left px-3 py-2 rounded text-sm font-bold text-gray-900 dark:text-gray-250 hover:bg-gray-100 dark:hover:bg-gray-855 hover:text-[#cc6c3b] flex justify-between items-center focus:outline-none uppercase">
                                <span>{{ $link['label'] }}</span>
                                <svg class="h-4 w-4 transform transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" class="pl-4 space-y-1" style="display: none;">
                                <a href="/kisii" class="block px-3 py-1.5 rounded text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#cc6c3b]">Kisii County</a>
                                <a href="/nyamira" class="block px-3 py-1.5 rounded text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#cc6c3b]">Nyamira County</a>
                                <a href="/migori" class="block px-3 py-1.5 rounded text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#cc6c3b]">Migori County</a>
                                <a href="/kisumu" class="block px-3 py-1.5 rounded text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#cc6c3b]">Kisumu County</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ $link['url'] }}" class="block px-3 py-2 rounded text-sm font-bold text-gray-900 dark:text-gray-250 hover:bg-gray-100 dark:hover:bg-gray-855 hover:text-[#cc6c3b] uppercase">
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
                @if(\App\Models\Setting::get('live_tv_active', '1') == '1')
                <a href="/tv" class="block px-3 py-2 rounded text-sm font-bold text-red-500 hover:bg-gray-100 dark:hover:bg-gray-855">TV</a>
                @endif
                @if(\App\Models\Setting::get('live_radio_active', '1') == '1')
                <a href="/live-radio" class="block px-3 py-2 rounded text-sm font-bold text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-855">RADIO</a>
                @endif
                @auth
                    <div class="border-t border-gray-150 dark:border-gray-800 my-2 pt-2 space-y-1">
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded text-sm font-bold text-gray-900 dark:text-gray-250 hover:bg-gray-100 dark:hover:bg-gray-855 hover:text-[#cc6c3b] uppercase">
                            Dashboard
                        </a>
                        <a href="{{ route('profile') }}" class="block px-3 py-2 rounded text-sm font-bold text-gray-900 dark:text-gray-250 hover:bg-gray-100 dark:hover:bg-gray-855 hover:text-[#cc6c3b] uppercase">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block pt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded text-sm font-bold text-red-650 hover:bg-red-50 dark:hover:bg-red-955/20 transition uppercase">
                                Sign Out
                            </button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-gray-150 dark:border-gray-800 my-2 pt-4 flex flex-col">
                        <a href="{{ route('login') }}" class="text-center bg-[#cc6c3b] hover:bg-orange-700 text-white font-bold text-xs py-2.5 rounded-lg transition shadow-sm uppercase tracking-wider">
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Sub-header: Trending Horizontal Bar -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-150 dark:border-gray-850 py-2.5 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-wrap items-center">
            <!-- Trending Header Label -->
            <div class="flex items-center space-x-1.5 font-black text-gray-950 dark:text-white mr-6 border-b-2 border-[#FF7900] pb-0.5 select-none uppercase tracking-wider text-[10px]">
                <svg class="h-3.5 w-3.5 text-[#FF7900]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span>Trending</span>
            </div>
            
            <!-- Trending Items Horizontal List -->
            @php
                $trendingItems = \App\Models\Article::where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->orderBy('views_count', 'desc')
                    ->take(5)
                    ->get();
            @endphp
            <div class="flex items-center space-x-6 overflow-x-auto scrollbar-none py-0.5 text-gray-600 dark:text-gray-300 font-semibold text-[11px]">
                @forelse($trendingItems as $item)
                    <a href="/articles/{{ $item->slug }}" class="hover:text-[#FF7900] whitespace-nowrap transition">
                        {{ $item->title }}
                    </a>
                @empty
                    <span class="text-gray-400 select-none">No trending stories at the moment.</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Breaking News Ticker -->
    <livewire:breaking-news-ticker />

    <!-- Content Slot -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Bottom Footer Banner Advertisement Spot -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 my-8">
        @include('partials.render-ad', ['location' => 'footer'])
    </div>

    <!-- Mobile Sticky Bottom Advertisement Spot -->
    @php
        $mobileStickyAd = \App\Models\Setting::get('ad_mobile_sticky_image');
        $mobileStickyLink = \App\Models\Setting::get('ad_mobile_sticky_link');
        $adsenseEnabled = \App\Models\Setting::get('adsense_enabled', false);
        $facebookAdsEnabled = \App\Models\Setting::get('facebook_ads_enabled', false);
    @endphp
    @if(($mobileStickyAd && \App\Models\Setting::get('custom_ads_enabled', false)) || ($adsenseEnabled && \App\Models\Setting::get('adsense_code')) || ($facebookAdsEnabled && \App\Models\Setting::get('facebook_ads_code')))
        <div class="fixed bottom-0 left-0 right-0 z-50 md:hidden bg-white/95 dark:bg-gray-950/95 backdrop-blur border-t border-gray-200 dark:border-gray-800 shadow-xl py-1 px-4 flex justify-between items-center" x-data="{ open: true }" x-show="open">
            <div class="flex-grow flex justify-center">
                @include('partials.render-ad', ['location' => 'mobile_sticky'])
            </div>
            <button @click="open = false" class="text-gray-450 hover:text-gray-700 dark:hover:text-white text-xs font-bold font-sans ml-2 select-none border border-gray-300 dark:border-gray-700 px-1.5 py-0.5 rounded">
                ✕
            </button>
        </div>
    @endif

    <!-- Footer -->
    <footer class="py-12 px-4 sm:px-6 border-t-4 transition-colors" style="background-color: {{ $footerBgColor }}; color: {{ $footerTextColor }}; border-top-color: {{ $brandColor }};">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-4">
                <a href="/" class="inline-flex items-center overflow-hidden rounded-md border border-gray-800 dark:border-gray-700 shadow-sm mb-2">
                    <!-- Left: Orange -->
                    <div class="bg-[#cc6c3b] px-3.5 py-1.5 text-white font-sans font-black tracking-tight text-xs uppercase">
                        {{ $firstWord }}
                    </div>
                    <!-- Right: Dark Gray/Black -->
                    <div class="bg-gray-900 px-3.5 py-1.5 text-white font-sans font-black tracking-tight text-xs uppercase border-l border-gray-800 dark:border-gray-700">
                        {{ $secondWord }}
                    </div>
                </a>
                <p class="text-xs text-gray-400 leading-relaxed">
                    {{ $siteName }} is a modern digital platform dedicated to bringing you timely, accurate, and independent news coverage from Kisii County, Kenya, and across the globe.
                </p>
                <div class="flex space-x-4 pt-2">
                    <a href="{{ \App\Models\Setting::getStats('facebook', \App\Models\Setting::get('facebook'))['url'] }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener" title="Facebook">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="{{ \App\Models\Setting::getStats('twitter', \App\Models\Setting::get('twitter'))['url'] }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener" title="Twitter / X">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="{{ \App\Models\Setting::getStats('instagram', \App\Models\Setting::get('instagram'))['url'] }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener" title="Instagram">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.01 3.71.054 1.14.051 1.96.23 2.673.506.77.3 1.44.75 2.05 1.36.6.6 1.05 1.27 1.35 2.05.28.72.45 1.54.5 2.67.04.93.05 1.28.05 3.71 0 2.43-.01 2.78-.05 3.71-.05 1.13-.22 1.96-.5 2.67-.3.77-.75 1.44-1.36 2.05-.6.6-1.27 1.05-2.05 1.35-.72.28-1.54.45-2.67.5-.93.04-1.28.05-3.71.05-2.43 0-2.78-.01-3.71-.05-1.13-.05-1.96-.22-2.67-.5-.77-.3-1.44-.75-2.05-1.36-.6-.6-1.05-1.27-1.35-2.05-.28-.72-.45-1.54-.5-2.67-.04-.93-.05-1.28-.05-3.71 0-2.43.01-2.78.05-3.71.05-1.13.22-1.96.5-2.67.3-.77.75-1.44 1.36-2.05.6-.6 1.27-1.05 2.05-1.35.72-.28 1.54-.45 2.67-.5.93-.04 1.28-.05 3.71-.05zm0 1.151c-2.43 0-2.784.009-3.71.053-.96.044-1.48.204-1.83.34-.46.18-.8.39-1.15.74-.35.35-.56.69-.74 1.15-.14.35-.3.87-.34 1.83-.04.93-.05 1.28-.05 3.71 0 2.43.01 2.78.05 3.71.04.96.2 1.48.34 1.83.18.46.39.8.74 1.15.35.35.69.56 1.15.74.35.14.87.3 1.83.34.93.04 1.28.05 3.71.05 2.43 0 2.78-.01 3.71-.05.96-.04 1.48-.2 1.83-.34.46-.18.8-.39 1.15-.74.35-.35.56-.69.74-1.15.14-.35.3-.87.34-1.83.04-.93.05-1.28.05-3.71 0-2.43-.01-2.78-.05-3.71-.04-.96-.2-1.48-.34-1.83-.18-.46-.39-.8-.74-1.15-.35-.35-.69-.56-1.15-.74-.35-.14-.87-.3-1.83-.34-.93-.04-1.28-.05-3.71-.05zm0 3.712a5.137 5.137 0 100 10.275 5.137 5.137 0 000-10.275zm0 9.124a3.987 3.987 0 110-7.975 3.987 3.987 0 010 7.975zm5.721-9.155a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="{{ \App\Models\Setting::getStats('youtube', \App\Models\Setting::get('youtube'))['url'] }}" class="text-gray-400 hover:text-white" target="_blank" rel="noopener" title="YouTube">
                        <span class="sr-only">YouTube</span>
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C22 8.68 22 12 22 12s0 3.32-.42 4.814c-.23.861-.907 1.538-1.768 1.768C18.32 19 12 19 12 19s-6.32 0-7.814-.42c-.861-.23-1.538-.907-1.768-1.768C2 15.32 2 12 2 12s0-3.32.42-4.814c.23-.861.907-1.538 1.768-1.768C5.68 5 12 5 12 5s6.32 0 7.812.418zM9.75 15.022L15.5 12 9.75 8.978v6.044z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="text-white font-semibold text-sm tracking-wider uppercase mb-4">News Categories</h4>
                <ul class="space-y-2 text-xs text-gray-400">
                    @foreach(\App\Models\Category::orderBy('order')->take(6)->get() as $cat)
                        <li><a href="/{{ $cat->slug }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Useful Links -->
            <div>
                <h4 class="text-white font-semibold text-sm tracking-wider uppercase mb-4">{{ $siteName }}</h4>
                <ul class="space-y-2 text-xs text-gray-400">
                    @php
                        $defaultFooter = [
                            ['label' => 'Live Stream TV', 'url' => '/tv'],
                            ['label' => 'Live Radio Audio', 'url' => '/live-radio'],
                            ['label' => 'About Us', 'url' => '/about'],
                            ['label' => 'Contact & Tips', 'url' => '/contact'],
                            ['label' => 'Privacy Policy', 'url' => '/privacy'],
                            ['label' => 'Terms of Service', 'url' => '/terms'],
                        ];
                        $footerLinks = $filterMenu(\App\Models\Setting::get('footer_menu', $defaultFooter));
                    @endphp
                    @foreach($footerLinks as $link)
                        <li><a href="{{ $link['url'] }}" class="hover:text-white transition">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Newsletter Column -->
            <div>
                <h4 class="text-white font-semibold text-sm tracking-wider uppercase mb-4">Newsletter</h4>
                <p class="text-xs text-gray-400 mb-4 leading-relaxed">
                    Subscribe to receive morning headlines and breaking news alerts directly in your inbox.
                </p>
                <livewire:newsletter-form />
            </div>
        </div>

        <div class="max-w-7xl mx-auto border-t border-gray-800 mt-10 pt-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved. Built for speed, accessibility and integrity.
        </div>
    </footer>

    <livewire:newsletter-popup />

    @livewireScripts

    <!-- Fallback handler for broken images (e.g. broken storage links) -->
    <script>
        window.addEventListener('error', function (e) {
            if (e.target.tagName === 'IMG') {
                const fallback = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=600&h=400';
                if (e.target.src !== fallback) {
                    e.target.src = fallback;
                }
            }
        }, true);
    </script>
</body>
</html>

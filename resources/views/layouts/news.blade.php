<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Metadata -->
    <title>{{ $title ?? 'Getembe News - Fast, Reliable News & Analysis' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Getembe News is your leading source for politics, business, technology, sports, opinion, and global news.' }}">
    <link rel="canonical" href="{{ $metaUrl ?? url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $metaUrl ?? url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Getembe News' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Getembe News is your leading source for politics, business, technology, sports, and global news.' }}">
    <meta property="og:image" content="{{ $metaImage ?? asset('images/default-og.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $metaUrl ?? url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? 'Getembe News' }}">
    <meta property="twitter:description" content="{{ $metaDescription ?? 'Getembe News is your leading source for politics, business, technology, sports, and global news.' }}">
    <meta property="twitter:image" content="{{ $metaImage ?? asset('images/default-og.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <!-- Instrument Sans is compiled via Vite, but fallback to Inter -->
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|playfair-display:700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-gray-900 bg-white dark:bg-gray-900 dark:text-gray-100 antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Main Al Jazeera-style Header -->
    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-850 sticky top-0 z-50 transition-colors duration-200" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-14 px-4 sm:px-6">
            <!-- Left Side: Logo Emblem -->
            <div class="flex items-center space-x-6 h-full shrink-0">
                <a href="/" class="flex items-center h-full group">
                    <!-- Gold Calligraphic background emblem block -->
                    <div class="bg-[#FF7900] h-14 w-12 flex items-center justify-center relative shadow-sm shrink-0">
                        <span class="text-white font-black text-2xl tracking-tighter transform group-hover:scale-105 transition font-serif">G</span>
                        <!-- Al Jazeera signature bottom triangle indent/accent -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500"></div>
                    </div>
                    <!-- Brand Typography -->
                    <span class="font-serif font-black text-lg sm:text-xl tracking-tight text-gray-950 dark:text-white ml-3 transition duration-150 uppercase">
                        GETEMBE <span class="text-[#FF7900]">NEWS</span>
                    </span>
                </a>
            </div>

            <!-- Center Side: Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-6 h-full text-xs font-black tracking-wide text-gray-900 dark:text-gray-200">
                @php
                    $defaultHeader = [
                        ['label' => 'Home', 'url' => '/'],
                        ['label' => 'Politics', 'url' => '/category/politics'],
                        ['label' => 'Business', 'url' => '/category/business'],
                        ['label' => 'Technology', 'url' => '/category/technology'],
                        ['label' => 'Sports', 'url' => '/category/sports'],
                    ];
                    $headerLinks = \App\Models\Setting::get('header_menu', $defaultHeader);
                @endphp
                @foreach($headerLinks as $link)
                    <a href="{{ $link['url'] }}" class="hover:text-[#FF7900] dark:hover:text-[#FF7900] transition py-5 flex items-center space-x-0.5">
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Right Side Actions: Live, Search, Dark mode, Sign in/up -->
            <div class="flex items-center space-x-4">
                <!-- LIVE TV Badge -->
                <a href="/live-tv" class="flex items-center space-x-1 text-xs font-black text-gray-900 dark:text-white hover:text-[#FF7900] transition uppercase tracking-wider group">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FF7900] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#FF7900]"></span>
                    </span>
                    <svg class="h-4 w-4 text-gray-550 dark:text-gray-400 group-hover:text-[#FF7900] transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                    <span class="hidden sm:inline">LIVE TV</span>
                </a>

                <!-- LIVE RADIO Badge -->
                <a href="/live-radio" class="flex items-center space-x-1 text-xs font-black text-gray-900 dark:text-white hover:text-[#FF7900] transition uppercase tracking-wider group">
                    <svg class="h-4 w-4 text-gray-550 dark:text-gray-400 group-hover:text-[#FF7900] transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                    <span class="hidden sm:inline">Radio</span>
                </a>

                <!-- Inline Search Form / Toggle -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-1.5 text-gray-700 dark:text-gray-300 hover:text-[#FF7900] focus:outline-none transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    <!-- Expandable search bar -->
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-900 border border-gray-205 dark:border-gray-800 rounded-lg shadow-lg p-2 z-50" style="display: none;">
                        <form action="/search" method="GET">
                            <input type="text" name="q" placeholder="Search news..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md py-1.5 px-3 text-xs focus:outline-none focus:ring-1 focus:ring-[#FF7900] dark:text-white">
                        </form>
                    </div>
                </div>

                <!-- Theme Toggle -->
                <button @click="$store.theme.toggle()" class="p-1.5 text-gray-700 dark:text-gray-300 hover:text-[#FF7900] focus:outline-none transition">
                    <svg x-show="!$store.theme.darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="$store.theme.darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                </button>

                <!-- Profile / Sign Up Button (Pill style) -->
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-black hover:bg-gray-850 text-white dark:bg-white dark:text-black dark:hover:bg-gray-150 text-[10px] uppercase font-bold tracking-wider px-4 py-2 rounded-full transition shadow-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-black hover:bg-gray-850 text-white dark:bg-white dark:text-black dark:hover:bg-gray-150 text-[10px] uppercase font-bold tracking-wider px-4 py-2 rounded-full transition shadow-sm">
                        Sign up
                    </a>
                @endauth

                <!-- Mobile Menu Toggle Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-1.5 text-gray-700 dark:text-gray-300 hover:text-[#FF7900] focus:outline-none transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" x-show="!mobileMenuOpen"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-show="mobileMenuOpen" style="display: none;"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Nav Menu -->
        <div x-show="mobileMenuOpen" x-transition class="lg:hidden bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 py-3" style="display: none;">
            <div class="px-4 space-y-1">
                @foreach($headerLinks as $link)
                    <a href="{{ $link['url'] }}" class="block px-3 py-2 rounded text-sm font-semibold text-gray-900 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-[#FF7900]">{{ $link['label'] }}</a>
                @endforeach
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
            <div class="flex items-center space-x-6 overflow-x-auto scrollbar-none py-0.5 text-gray-600 dark:text-gray-300 font-semibold text-[11px]">
                <a href="/category/politics" class="hover:text-[#FF7900] whitespace-nowrap transition">US-Israel war on Iran</a>
                <a href="/category/sports" class="hover:text-[#FF7900] whitespace-nowrap transition">World Cup 2026</a>
                <a href="/category/world" class="hover:text-[#FF7900] whitespace-nowrap transition">Tracking Israel's ceasefire violations</a>
                <a href="/category/politics" class="hover:text-[#FF7900] whitespace-nowrap transition">Donald Trump</a>
                <a href="/category/business" class="hover:text-[#FF7900] whitespace-nowrap transition">Getembe Development</a>
            </div>
        </div>
    </div>

    <!-- Breaking News Ticker -->
    <livewire:breaking-news-ticker />

    <!-- Content Slot -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 border-t-4 border-[#C8102E] py-12 px-4 sm:px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand Column -->
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <span class="bg-[#C8102E] text-white font-extrabold text-2xl px-2 py-0.5 rounded tracking-tighter">G</span>
                    <span class="font-serif font-black text-xl tracking-tight text-white">GETEMBE <span class="text-[#C8102E]">NEWS</span></span>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Getembe News is a modern digital platform dedicated to bringing you timely, accurate, and independent news coverage from Kisii County, Kenya, and across the globe.
                </p>
                <div class="flex space-x-4 pt-2">
                    <!-- Social icons (SVG placeholders) -->
                    <a href="#" class="text-gray-400 hover:text-white"><span class="sr-only">Facebook</span>FB</a>
                    <a href="#" class="text-gray-400 hover:text-white"><span class="sr-only">Twitter</span>TW</a>
                    <a href="#" class="text-gray-400 hover:text-white"><span class="sr-only">Instagram</span>IG</a>
                    <a href="#" class="text-gray-400 hover:text-white"><span class="sr-only">YouTube</span>YT</a>
                </div>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="text-white font-semibold text-sm tracking-wider uppercase mb-4">News Categories</h4>
                <ul class="space-y-2 text-xs text-gray-400">
                    @foreach(\App\Models\Category::orderBy('order')->take(6)->get() as $cat)
                        <li><a href="/category/{{ $cat->slug }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Useful Links -->
            <div>
                <h4 class="text-white font-semibold text-sm tracking-wider uppercase mb-4">Getembe News</h4>
                <ul class="space-y-2 text-xs text-gray-400">
                    @php
                        $defaultFooter = [
                            ['label' => 'Live Stream TV', 'url' => '/live-tv'],
                            ['label' => 'Live Radio Audio', 'url' => '/live-radio'],
                            ['label' => 'About Us', 'url' => '/about'],
                            ['label' => 'Contact & Tips', 'url' => '/contact'],
                            ['label' => 'Privacy Policy', 'url' => '/privacy'],
                        ];
                        $footerLinks = \App\Models\Setting::get('footer_menu', $defaultFooter);
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
            &copy; {{ date('Y') }} Getembe News. All rights reserved. Built for speed, accessibility and integrity.
        </div>
    </footer>

    @livewireScripts
</body>
</html>

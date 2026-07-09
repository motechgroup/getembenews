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

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-950">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/auth-bg.png') }}');">
            <!-- Overlay and blur -->
            <div class="absolute inset-0 bg-gray-900/60 dark:bg-gray-955/80 backdrop-blur-[6px] z-0"></div>

            <!-- Content Container -->
            <div class="relative z-10 w-full sm:max-w-md px-4 sm:px-0">
                <!-- Branded Logo Header -->
                <div class="flex justify-center mb-6">
                    @php
                        $siteName = \App\Models\Setting::get('site_name', 'Getembe News');
                        $parts = explode(' ', trim($siteName), 2);
                        $firstWord = $parts[0] ?? 'Getembe';
                        $secondWord = $parts[1] ?? 'News';
                    @endphp
                    <a href="/" class="flex items-center overflow-hidden rounded-md border border-gray-800 dark:border-gray-700 shadow-lg" wire:navigate>
                        <!-- Left: Orange -->
                        <div class="bg-[#cc6c3b] px-4 py-2 text-white font-sans font-black tracking-tight text-sm uppercase">
                            {{ $firstWord }}
                        </div>
                        <!-- Right: Dark Gray/Black -->
                        <div class="bg-gray-900 px-4 py-2 text-white font-sans font-black tracking-tight text-sm uppercase border-l border-gray-800 dark:border-gray-700">
                            {{ $secondWord }}
                        </div>
                    </a>
                </div>

                <!-- Glassmorphic Card -->
                <div class="w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur border border-gray-200/80 dark:border-gray-800/80 shadow-2xl p-6 sm:p-8 rounded-2xl">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Getembe News</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
        }
        .mesh-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(204, 108, 59, 0.15) 0%, transparent 45%),
                        radial-gradient(circle at 90% 80%, rgba(200, 16, 46, 0.1) 0%, transparent 45%);
        }
    </style>
</head>
<body class="mesh-gradient min-h-screen text-gray-150 flex flex-col justify-between relative overflow-hidden">
    <!-- Glow Effects -->
    <div class="absolute top-[-20%] left-[-20%] w-[60%] aspect-square rounded-full bg-[#cc6c3b]/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-20%] w-[60%] aspect-square rounded-full bg-[#C8102E]/10 blur-[120px] pointer-events-none"></div>

    <!-- Header -->
    <header class="max-w-7xl mx-auto w-full px-6 py-8 flex justify-between items-center z-10">
        <a href="/" class="flex items-center overflow-hidden rounded-md border border-gray-800 shadow-lg">
            <div class="bg-[#cc6c3b] px-3.5 py-1.5 text-white font-sans font-black tracking-tight text-xs sm:text-sm uppercase">
                Getembe
            </div>
            <div class="bg-gray-900 px-3.5 py-1.5 text-white font-sans font-black tracking-tight text-xs sm:text-sm uppercase border-l border-gray-800">
                News
            </div>
        </a>
        <a href="/" class="text-xs font-bold text-gray-400 hover:text-white transition uppercase tracking-wider">
            &larr; Back to Home
        </a>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-6 py-12 z-10">
        <div class="max-w-2xl w-full text-center space-y-8">
            <!-- Animated 404 badge -->
            <div class="relative inline-block">
                <span class="text-8xl sm:text-9xl font-serif font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-[#cc6c3b] to-[#C8102E] select-none">
                    404
                </span>
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1 py-0.5 px-3 bg-red-950/80 border border-red-550/40 text-red-400 font-extrabold text-[10px] uppercase tracking-widest rounded-full whitespace-nowrap animate-pulse shadow-lg">
                    🚨 Broadcast Interrupted
                </span>
            </div>

            <!-- Error message text -->
            <div class="space-y-3">
                <h1 class="text-2xl sm:text-3xl font-serif font-black text-white leading-tight">
                    Story Not Found / Page Missing
                </h1>
                <p class="text-sm text-gray-400 max-w-lg mx-auto leading-relaxed">
                    The news article, category desk, or media broadcast you are seeking has been relocated, retired, or never existed in our digital newsroom.
                </p>
            </div>

            <!-- Standalone Search Bar -->
            <div class="max-w-md mx-auto">
                <form action="/search" method="GET" class="flex items-center bg-gray-900/50 backdrop-blur-md border border-gray-800 rounded-lg overflow-hidden p-1.5 focus-within:ring-1 focus-within:ring-[#cc6c3b] focus-within:border-[#cc6c3b] transition">
                    <input type="text" name="query" placeholder="Search the archive..." required
                           class="w-full bg-transparent border-0 px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:ring-0">
                    <button type="submit" class="bg-[#cc6c3b] hover:bg-[#cc6c3b]/90 text-white font-bold text-xs px-5 py-2 rounded transition shrink-0">
                        Search
                    </button>
                </form>
            </div>

            <!-- Shortcut Navigation Links -->
            <div class="flex flex-wrap items-center justify-center gap-4 text-xs font-bold text-gray-400 pt-2">
                <a href="/" class="px-4 py-2 bg-gray-900 border border-gray-800 hover:border-gray-700 text-white rounded transition shadow-sm">
                    📰 News Home
                </a>
                <a href="/tv" class="px-4 py-2 bg-gray-900 border border-gray-800 hover:border-gray-700 text-white rounded transition shadow-sm flex items-center space-x-1">
                    <span>📺 Live TV</span>
                </a>
                <a href="/live-radio" class="px-4 py-2 bg-gray-900 border border-gray-800 hover:border-gray-700 text-white rounded transition shadow-sm flex items-center space-x-1">
                    <span>📻 Live Radio</span>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-7xl mx-auto w-full px-6 py-8 border-t border-gray-900 text-center text-xs text-gray-600 z-10">
        &copy; {{ date('Y') }} Getembe News. All rights reserved. Kisii County, Kenya.
    </footer>
</body>
</html>

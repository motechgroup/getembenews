<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Getembe News</title>
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
            &larr; Return to Home
        </a>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-6 py-12 z-10">
        <div class="max-w-2xl w-full text-center space-y-8">
            <!-- Animated 500 badge -->
            <div class="relative inline-block">
                <span class="text-8xl sm:text-9xl font-serif font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-[#cc6c3b] to-[#C8102E] select-none">
                    500
                </span>
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1 py-0.5 px-3 bg-orange-950/80 border border-orange-550/40 text-orange-400 font-extrabold text-[10px] uppercase tracking-widest rounded-full whitespace-nowrap animate-pulse shadow-lg">
                    ⚠️ Transmission Interrupted
                </span>
            </div>

            <!-- Error message text -->
            <div class="space-y-3">
                <h1 class="text-2xl sm:text-3xl font-serif font-black text-white leading-tight">
                    Internal Server Connection Issue
                </h1>
                <p class="text-sm text-gray-400 max-w-lg mx-auto leading-relaxed">
                    Our newsroom database or server encountered an unexpected hiccup. Our technical desk has been alerted and is currently debugging the issue.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-4 text-xs font-bold pt-2">
                <button onclick="window.location.reload();" class="px-5 py-2.5 bg-[#cc6c3b] hover:bg-[#cc6c3b]/90 text-white rounded transition shadow-md shadow-orange-950/20">
                    🔄 Refresh Broadcast
                </button>
                <a href="/" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-850 text-white border border-gray-800 rounded transition shadow-sm">
                    📰 News Homepage
                </a>
                <a href="/contact" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-850 text-gray-300 border border-gray-800 rounded transition shadow-sm">
                    ✉️ Report Outage
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

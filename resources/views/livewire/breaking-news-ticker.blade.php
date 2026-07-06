<?php

use function Livewire\Volt\{state};

state(['alerts' => fn() => \App\Models\BreakingNews::active()->get()]);

?>

<div class="w-full bg-red-50 dark:bg-red-950/20 border-b border-red-100 dark:border-red-950/50 py-1.5 overflow-hidden" x-show="($wire.alerts || []).length > 0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center">
        <!-- Badge -->
        <span class="inline-flex items-center px-2 py-0.5 bg-[#C8102E] text-white text-[10px] font-black tracking-wider uppercase rounded-sm animate-pulse mr-3 shrink-0">
            Breaking
        </span>

        <!-- Ticker -->
        <div class="relative flex-1 overflow-hidden h-5">
            <div class="absolute whitespace-nowrap animate-marquee flex space-x-12 items-center text-sm font-medium text-red-900 dark:text-red-200">
                @foreach($alerts as $alert)
                    <div class="flex items-center space-x-2">
                        @if($alert->link)
                            <a href="{{ $alert->link }}" class="hover:underline">{{ $alert->title }}</a>
                        @else
                            <span>{{ $alert->title }}</span>
                        @endif
                        <span class="text-red-400 dark:text-red-600 font-bold">&bull;</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>
    @keyframes marquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee {
        animation: marquee 25s linear infinite;
    }
    .animate-marquee:hover {
        animation-play-state: paused;
    }
    </style>
</div>

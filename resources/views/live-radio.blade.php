<x-news-layout>
    <x-slot name="title">Live Radio Stream - Getembe News</x-slot>

    <div class="bg-gray-900 text-white min-h-[calc(100vh-140px)] py-8 flex flex-col justify-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 w-full space-y-8">
            
            <!-- Page Header -->
            <div class="text-center space-y-2">
                <span class="inline-flex items-center px-2.5 py-0.5 bg-[#C8102E] text-white text-[10px] font-black tracking-wider uppercase rounded-full animate-pulse">
                    On Air
                </span>
                <h1 class="text-3xl sm:text-4xl font-serif font-black tracking-tight">
                    Getembe FM Live
                </h1>
                <p class="text-xs text-gray-400">Tuning in live, broadcasting high-fidelity audio regional feeds</p>
            </div>

            <!-- Custom Audio Player Widget -->
            <div class="bg-gray-950 border border-gray-800 rounded-2xl p-6 sm:p-8 shadow-2xl space-y-6" 
                 x-data="{ 
                     playing: false, 
                     muted: false, 
                     volume: 80, 
                     audio: null, 
                     init() { 
                         this.audio = new Audio('{{ $radioUrl }}'); 
                         this.audio.volume = this.volume / 100;
                     },
                     togglePlay() {
                         if (this.playing) {
                             this.audio.pause();
                             this.playing = false;
                         } else {
                             this.audio.play();
                             this.playing = true;
                         }
                     },
                     toggleMute() {
                         this.muted = !this.muted;
                         this.audio.muted = this.muted;
                     },
                     updateVolume() {
                         this.audio.volume = this.volume / 100;
                     }
                 }">
                
                <!-- Player Center Controls -->
                <div class="flex flex-col items-center space-y-4">
                    <!-- Audio Visualizer Mock -->
                    <div class="flex items-end justify-center space-x-1.5 h-12 w-full max-w-xs px-4">
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 48px; transform: scaleY(0.7)' : 'height: 8px'"></span>
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 32px; transform: scaleY(0.9)' : 'height: 8px'"></span>
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 40px; transform: scaleY(0.5)' : 'height: 8px'"></span>
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 16px; transform: scaleY(0.8)' : 'height: 8px'"></span>
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 48px; transform: scaleY(0.6)' : 'height: 8px'"></span>
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 36px; transform: scaleY(0.8)' : 'height: 8px'"></span>
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 24px; transform: scaleY(0.4)' : 'height: 8px'"></span>
                        <span class="w-1 bg-[#C8102E] rounded-full transition-all duration-300" :style="playing ? 'height: 44px; transform: scaleY(0.8)' : 'height: 8px'"></span>
                    </div>

                    <!-- Current Show Profile -->
                    <div class="text-center space-y-1">
                        <h3 class="text-lg font-bold text-white">Getembe Express Drive</h3>
                        <p class="text-xs text-gray-500">Host: <span class="text-gray-300 font-semibold">MC Getembe</span></p>
                    </div>

                    <!-- Play / Pause Main Button -->
                    <div class="pt-2">
                        <button @click="togglePlay()" class="h-16 w-16 bg-[#C8102E] hover:bg-red-700 text-white rounded-full flex items-center justify-center shadow-lg transition duration-200 focus:outline-none focus:ring-4 focus:ring-red-950">
                            <!-- Play Icon -->
                            <svg x-show="!playing" class="h-8 w-8 fill-current text-white translate-x-0.5" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                            <!-- Pause Icon -->
                            <svg x-show="playing" class="h-8 w-8 fill-current text-white" viewBox="0 0 20 20" style="display: none;">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Bottom Controls (Volume and Mute) -->
                <div class="flex items-center justify-between border-t border-gray-800 pt-5 text-xs text-gray-400">
                    <div class="flex items-center space-x-3 w-48">
                        <button @click="toggleMute()" class="text-gray-400 hover:text-white transition">
                            <!-- Speaker Icon -->
                            <svg x-show="!muted" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                            </svg>
                            <!-- Mute Icon -->
                            <svg x-show="muted" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zm10.707-8.707l-6 6m0-6l6 6"/>
                            </svg>
                        </button>
                        <!-- Volume Slider -->
                        <input type="range" min="0" max="100" x-model="volume" @input="updateVolume()" class="w-full accent-[#C8102E] h-1 bg-gray-800 rounded-lg cursor-pointer">
                    </div>

                    <div class="flex items-center space-x-1">
                        <span class="h-2.5 w-2.5 bg-green-500 rounded-full animate-ping mr-1"></span>
                        <span class="font-semibold text-gray-300">Live Audio Stream</span>
                    </div>
                </div>

            </div>

            <!-- Schedule -->
            <div class="bg-gray-955 border border-gray-800 rounded-2xl p-6 space-y-4" x-data="{ activeDay: '{{ strtolower(now()->format('l')) }}' }">
                <div class="flex justify-between items-center border-b border-gray-800 pb-2">
                    <h3 class="text-sm font-black uppercase tracking-wider text-white">
                        Show Schedule
                    </h3>
                </div>

                <!-- Day Navigation Tabs -->
                <div class="flex border-b border-gray-800 pb-1.5 overflow-x-auto gap-3 scrollbar-none text-[10px] font-bold uppercase tracking-wider">
                    @foreach(['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'] as $key => $name)
                        <button type="button" @click="activeDay = '{{ $key }}'" :class="activeDay === '{{ $key }}' ? 'text-[#C8102E] border-b border-[#C8102E] pb-1' : 'text-gray-500 hover:text-gray-300 pb-1'" class="shrink-0 transition focus:outline-none">
                            {{ $name }}
                        </button>
                    @endforeach
                </div>
                
                @php
                    $radioSchedule = \App\Models\Setting::get('radio_schedule', []);
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    $defaultRadioFlat = [
                        ['time' => '06:00 AM - 10:00 AM', 'title' => 'The Morning Drive', 'desc' => 'Kickstart the day with updates and music.', 'is_playing' => false],
                        ['time' => '10:00 AM - 01:00 PM', 'title' => 'Midday Request Show', 'desc' => 'Listener choices, request lines, and interviews.', 'is_playing' => false],
                        ['time' => '01:00 PM - 04:00 PM', 'title' => 'Getembe Express Drive', 'desc' => 'Mid-afternoon drive show with regional topics and guest experts.', 'is_playing' => true],
                        ['time' => '04:00 PM - 08:00 PM', 'title' => 'Evening Jam & Sports', 'desc' => 'Local sports bulletins and afternoon reviews.', 'is_playing' => false],
                        ['time' => '08:00 PM - 12:00 AM', 'title' => 'Late Night Soul Session', 'desc' => 'Slow jams, classic tracks, and quiet storm conversations.', 'is_playing' => false]
                    ];
                    if (!is_array($radioSchedule) || empty($radioSchedule)) {
                        $radioSchedule = array_fill_keys($days, $defaultRadioFlat);
                    } else {
                        $isGrouped = true;
                        foreach ($days as $day) {
                            if (!isset($radioSchedule[$day])) {
                                $isGrouped = false; break;
                            }
                        }
                        if (!$isGrouped) {
                            $radioSchedule = array_fill_keys($days, $radioSchedule);
                        }
                    }
                @endphp

                <!-- Schedule List Containers by Day -->
                <div class="text-xs">
                    @foreach($days as $dayKey)
                        <div x-show="activeDay === '{{ $dayKey }}'" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;" x-cloak>
                            @forelse(($radioSchedule[$dayKey] ?? []) as $item)
                                <div class="flex items-start space-x-3 {{ ($item['is_playing'] ?? false) ? 'bg-red-950/20 border-l-2 border-[#C8102E] pl-2 py-1 col-span-full' : 'text-gray-400' }}">
                                    <span class="font-bold w-20 shrink-0 {{ ($item['is_playing'] ?? false) ? 'text-[#C8102E]' : 'text-gray-500' }}">{{ $item['time'] }}</span>
                                    <div>
                                        <h4 class="font-bold {{ ($item['is_playing'] ?? false) ? 'text-white flex items-center space-x-1.5' : 'text-gray-300' }}">
                                            <span>{{ $item['title'] }}</span>
                                            @if($item['is_playing'] ?? false)
                                                <span class="inline-block w-1.5 h-1.5 bg-[#C8102E] rounded-full animate-pulse"></span>
                                            @endif
                                        </h4>
                                        <p class="text-[11px] {{ ($item['is_playing'] ?? false) ? 'text-gray-400' : 'text-gray-500' }}">{{ $item['desc'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-550 text-center py-4 col-span-full">No programs scheduled for {{ ucfirst($dayKey) }}.</p>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-news-layout>

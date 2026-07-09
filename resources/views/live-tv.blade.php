<x-news-layout>
    <x-slot name="title">Live TV Stream - Getembe News</x-slot>

    <div class="bg-gray-900 text-white min-h-[calc(100vh-140px)] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 space-y-8">
            
            <!-- Page Header -->
            <div class="border-b border-gray-800 pb-4 flex items-center justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-serif font-black tracking-tight flex items-center space-x-2">
                        <span class="w-3.5 h-3.5 bg-[#C8102E] rounded-full animate-ping mr-2"></span>
                        <span>Getembe TV Live</span>
                    </h1>
                    <p class="text-xs text-gray-400">Broadcasting live from Getembe newsroom, Kisii, Kenya</p>
                </div>
            </div>

            <!-- TV Player & Schedule Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Player (Left) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="aspect-video rounded-lg overflow-hidden bg-black relative border border-gray-800 shadow-2xl">
                        @if(Str::contains($tvUrl, 'youtube.com') || Str::contains($tvUrl, 'embed'))
                            <iframe src="{{ $tvUrl }}" title="Getembe Live TV" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        @else
                            <video controls autoplay class="w-full h-full">
                                <source src="{{ $tvUrl }}" type="application/x-mpegURL">
                                Your browser does not support HLS streaming.
                            </video>
                        @endif
                    </div>
                    <div class="bg-gray-950 p-4 border border-gray-800 rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div>
                            <h3 class="text-sm font-bold">Currently Playing: <span class="text-[#C8102E]">News Hour Live</span></h3>
                            <p class="text-xs text-gray-500">Live political debates, breaking bulletins and interviews.</p>
                        </div>
                        <button onclick="navigator.clipboard.writeText(window.location.href); alert('Stream link copied!')" class="text-xs font-semibold px-3 py-1.5 border border-gray-800 hover:border-gray-700 rounded bg-gray-900 transition flex items-center space-x-1">
                            <span>Share Stream</span>
                        </button>
                    </div>
                </div>

                <!-- Programme Schedule (Right) -->
                <div class="bg-gray-955 border border-gray-800 rounded-lg p-5 space-y-4" x-data="{ activeDay: '{{ strtolower(now()->format('l')) }}' }">
                    <div class="flex justify-between items-center border-b border-gray-800 pb-2">
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">
                            Programme Schedule
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
                        $tvSchedule = \App\Models\Setting::get('tv_schedule', []);
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $defaultTvFlat = [
                            ['time' => '06:00 AM - 09:00 AM', 'title' => 'Getembe Morning Call', 'desc' => 'Breakfast news and newspaper review.', 'is_playing' => false],
                            ['time' => '09:00 AM - 12:00 PM', 'title' => 'Business Daily', 'desc' => 'Economic trends, stock updates, and trade discussion.', 'is_playing' => false],
                            ['time' => '12:00 PM - 02:00 PM', 'title' => 'News Hour Live', 'desc' => 'Midday headlines, market check, and regional briefs.', 'is_playing' => true],
                            ['time' => '02:00 PM - 04:00 PM', 'title' => 'Health & Sports Highlights', 'desc' => 'Wellness insights and sporting roundups.', 'is_playing' => false],
                            ['time' => '04:00 PM - 07:00 PM', 'title' => 'Regional News Express', 'desc' => 'Community spotlights and county assembly briefings.', 'is_playing' => false],
                            ['time' => '07:00 PM - 09:00 PM', 'title' => 'Evening Prime Time News', 'desc' => 'Comprehensive summary of the day\'s major events.', 'is_playing' => false],
                            ['time' => '09:00 PM - 11:00 PM', 'title' => 'Late Night Spotlight', 'desc' => 'Documentary film showcases and talkshows.', 'is_playing' => false]
                        ];
                        if (!is_array($tvSchedule) || empty($tvSchedule)) {
                            $tvSchedule = array_fill_keys($days, $defaultTvFlat);
                        } else {
                            $isGrouped = true;
                            foreach ($days as $day) {
                                if (!isset($tvSchedule[$day])) {
                                    $isGrouped = false; break;
                                }
                            }
                            if (!$isGrouped) {
                                $tvSchedule = array_fill_keys($days, $tvSchedule);
                            }
                        }
                    @endphp

                    <!-- Schedule List Containers by Day -->
                    <div class="text-xs">
                        @foreach($days as $dayKey)
                            <div x-show="activeDay === '{{ $dayKey }}'" class="space-y-4" style="display: none;" x-cloak>
                                @forelse(($tvSchedule[$dayKey] ?? []) as $item)
                                    <div class="flex items-start space-x-3 {{ ($item['is_playing'] ?? false) ? 'bg-red-950/20 border-l-2 border-[#C8102E] pl-2 py-1' : 'text-gray-400' }}">
                                        <span class="font-bold w-24 shrink-0 {{ ($item['is_playing'] ?? false) ? 'text-[#C8102E]' : '' }}">{{ $item['time'] }}</span>
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
                                    <p class="text-gray-500 text-center py-4">No programs scheduled for {{ ucfirst($dayKey) }}.</p>
                                @endforelse
                            </div>
                        @endforeach
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-news-layout>

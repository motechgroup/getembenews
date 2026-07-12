<?php

use function Livewire\Volt\{state, rules, uses};
use Livewire\WithFileUploads;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Cache;

uses(WithFileUploads::class);

state([
    'advertisements' => fn() => Advertisement::orderBy('created_at', 'desc')->get(),
    'isEditing' => false,
    'adId' => null,

    // Form fields
    'title' => '',
    'image_url' => '',
    'script_code' => '',
    'destination_url' => '',
    'location' => 'sidebar',
    'is_active' => true,
    'starts_at' => '',
    'expires_at' => '',
    'uploadedImage' => null,
]);

$create = function () {
    $this->resetErrorBag();
    $this->reset(['adId', 'title', 'image_url', 'script_code', 'destination_url', 'location', 'is_active', 'starts_at', 'expires_at', 'uploadedImage']);
    $this->isEditing = true;
};

$edit = function ($id) {
    $this->resetErrorBag();
    $ad = Advertisement::findOrFail($id);

    $this->adId = $ad->id;
    $this->title = $ad->title;
    $this->image_url = $ad->image_url;
    $this->script_code = $ad->script_code;
    $this->destination_url = $ad->destination_url;
    $this->location = $ad->location;
    $this->is_active = $ad->is_active;
    $this->starts_at = $ad->starts_at ? $ad->starts_at->format('Y-m-d\TH:i') : '';
    $this->expires_at = $ad->expires_at ? $ad->expires_at->format('Y-m-d\TH:i') : '';
    $this->uploadedImage = null;

    $this->isEditing = true;
};

$save = function () {
    $this->validate([
        'title' => 'required|string|max:255',
        'location' => 'required|string|in:top,sidebar,inline,footer,mobile_sticky',
        'destination_url' => 'nullable|url',
        'starts_at' => 'nullable|date',
        'expires_at' => 'nullable|date|after_or_equal:starts_at',
        'uploadedImage' => 'nullable|image|max:2048',
    ]);

    if ($this->uploadedImage) {
        $path = $this->uploadedImage->store('ads', 'public');
        $this->image_url = asset('storage/' . $path);
        $this->uploadedImage = null;
    }

    $data = [
        'title' => $this->title,
        'image_url' => $this->image_url ?: null,
        'script_code' => $this->script_code ?: null,
        'destination_url' => $this->destination_url ?: null,
        'location' => $this->location,
        'is_active' => (bool)$this->is_active,
        'starts_at' => $this->starts_at ? Carbon\Carbon::parse($this->starts_at) : null,
        'expires_at' => $this->expires_at ? Carbon\Carbon::parse($this->expires_at) : null,
    ];

    if ($this->adId) {
        $ad = Advertisement::findOrFail($this->adId);
        $ad->update($data);
    } else {
        Advertisement::create($data);
    }

    Cache::forget('homepage_data_v1');
    $this->advertisements = Advertisement::orderBy('created_at', 'desc')->get();
    $this->isEditing = false;
};

$toggleActive = function ($id) {
    $ad = Advertisement::findOrFail($id);
    $ad->update(['is_active' => !$ad->is_active]);
    Cache::forget('homepage_data_v1');
    $this->advertisements = Advertisement::orderBy('created_at', 'desc')->get();
};

$delete = function ($id) {
    $ad = Advertisement::findOrFail($id);
    $ad->delete();
    Cache::forget('homepage_data_v1');
    $this->advertisements = Advertisement::orderBy('created_at', 'desc')->get();
};

?>

<div class="space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? ($adId ? 'Edit Advertisement' : 'Create Advertisement') : 'Campaign Advertisements' }}
        </h2>
        
        @if(!$isEditing)
            <button wire:click="create" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition">
                Create Ad Placement
            </button>
        @else
            <button @click="$wire.isEditing = false" class="bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold px-4 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                Back to List
            </button>
        @endif
    </div>

    @if(!$isEditing)
        <!-- TABLE LIST -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-850 text-gray-550 border-b border-gray-200 dark:border-gray-800 font-bold">
                            <th class="p-3">Ad Title</th>
                            <th class="p-3">Location Spot</th>
                            <th class="p-3">Ad Type</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Impressions</th>
                            <th class="p-3 text-center">Clicks</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($advertisements as $ad)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50">
                                <td class="p-3 font-semibold text-gray-900 dark:text-white">
                                    <div class="flex items-center space-x-3">
                                        @if($ad->image_url)
                                            <img src="{{ $ad->image_url }}" class="w-12 h-8 object-cover rounded border border-gray-200 dark:border-gray-700 bg-white">
                                        @else
                                            <div class="w-12 h-8 rounded border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-mono text-[9px] text-gray-400">
                                                HTML
                                            </div>
                                        @endif
                                        <div>
                                            <span class="block font-bold">{{ $ad->title }}</span>
                                            @if($ad->destination_url)
                                                <span class="block text-[10px] text-gray-450 truncate max-w-xs">{{ $ad->destination_url }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 font-semibold text-gray-700 dark:text-gray-300">
                                    <span class="bg-blue-50 dark:bg-blue-955/40 text-blue-700 dark:text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded capitalize">
                                        {{ str_replace('_', ' ', $ad->location) }}
                                    </span>
                                </td>
                                <td class="p-3 text-gray-550 font-medium">
                                    {{ $ad->script_code ? 'Script/AdSense' : 'Image Banner' }}
                                </td>
                                <td class="p-3 text-center">
                                    <button wire:click="toggleActive({{ $ad->id }})" class="focus:outline-none">
                                        @if($ad->is_active)
                                            <span class="bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-300 text-[10px] font-bold px-2 py-0.5 rounded-full">Active</span>
                                        @else
                                            <span class="bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded-full">Inactive</span>
                                        @endif
                                    </button>
                                </td>
                                <td class="p-3 text-center text-gray-550 dark:text-gray-455 font-bold font-mono">{{ number_format($ad->impressions) }}</td>
                                <td class="p-3 text-center text-gray-555 dark:text-gray-455 font-bold font-mono">{{ number_format($ad->clicks) }}</td>
                                <td class="p-3 text-right space-x-2">
                                    <button wire:click="edit({{ $ad->id }})" class="text-[#C8102E] font-bold hover:underline">Edit</button>
                                    <button wire:click="delete({{ $ad->id }})" wire:confirm="Are you sure you want to delete this ad placement?" class="text-red-500 font-bold hover:underline">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400">No custom advertisements managed yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- FORM VIEW -->
        <form wire:submit.prevent="save" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 max-w-2xl space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Campaign Title</label>
                    <input type="text" wire:model="title" placeholder="e.g. Highlands View Apartments"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    @error('title') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Placement Ad Spot</label>
                    <select wire:model="location" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold">
                        <option value="top">Top Header Leaderboard (728x90)</option>
                        <option value="sidebar">Sidebar Rectangle (300x250)</option>
                        <option value="inline">Inline Article Body (468x60)</option>
                        <option value="footer">Bottom Footer Banner (728x90)</option>
                        <option value="mobile_sticky">Mobile Sticky Bottom (320x50)</option>
                    </select>
                    @error('location') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Ad Type Selection (Custom Toggle View) -->
            <div x-data="{ type: 'banner' }" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 block">Ad Content Type</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center space-x-2 cursor-pointer text-xs font-semibold">
                            <input type="radio" x-model="type" value="banner" class="text-[#C8102E] border-gray-300">
                            <span>Image Banner</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer text-xs font-semibold">
                            <input type="radio" x-model="type" value="script" class="text-[#C8102E] border-gray-300">
                            <span>Custom HTML / Script Code</span>
                        </label>
                    </div>
                </div>

                <!-- Type: Banner Upload -->
                <div x-show="type === 'banner'" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Upload Banner Image</label>
                            <input type="file" wire:model="uploadedImage" accept="image/*" class="text-xs">
                            <input type="url" wire:model="image_url" placeholder="Or enter remote image URL"
                                   class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none dark:text-white mt-2">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Destination Redirect Link</label>
                            <input type="url" wire:model="destination_url" placeholder="https://example.com/target"
                                   class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white mt-1">
                            @error('destination_url') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Type: HTML / Script Code -->
                <div x-show="type === 'script'" class="space-y-1" style="display: none;">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">HTML Code / AdSense tags</label>
                    <textarea wire:model="script_code" rows="4" placeholder="Paste script from Google AdSense or HTML banner code here..."
                              class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-mono"></textarea>
                </div>
            </div>

            <!-- Ad scheduling -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Campaign Starts At (Optional)</label>
                    <input type="datetime-local" wire:model="starts_at"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Campaign Expires At (Optional)</label>
                    <input type="datetime-local" wire:model="expires_at"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none dark:text-white">
                    @error('expires_at') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" wire:model="is_active" id="is_active" class="rounded text-[#C8102E] border-gray-300">
                <label for="is_active" class="text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Set Placement Active Immediately</label>
            </div>

            <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-5 py-2.5 rounded transition">
                Save Advertisement Placement
            </button>
        </form>
    @endif
</div>

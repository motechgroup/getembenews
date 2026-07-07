<?php

use function Livewire\Volt\{state, mount};
use App\Models\Setting;

state([
    'activeMenu' => 'header', // header, footer
    'menuItems' => [],
    
    // New item inputs
    'newLabel' => '',
    'newUrl' => '',
    'selectedCategorySlug' => '',
    
    'saved' => false,
]);

// Load menu items on mount
mount(function () {
    $this->loadMenu();
});

$loadMenu = function () {
    $key = $this->activeMenu . '_menu';
    // Default fallback configurations
    $defaultHeader = [
        ['label' => 'News', 'url' => '/'],
        ['label' => 'Counties', 'url' => '#counties'],
        ['label' => 'Politics', 'url' => '/politics'],
        ['label' => 'Business', 'url' => '/business'],
        ['label' => 'Entertainment', 'url' => '/entertainment'],
        ['label' => 'Sports', 'url' => '/sports'],
        ['label' => 'Video', 'url' => '/live-tv'],
    ];

    $defaultFooter = [
        ['label' => 'About Us', 'url' => '/about'],
        ['label' => 'Contact & Tips', 'url' => '/contact'],
        ['label' => 'Privacy Policy', 'url' => '/privacy'],
    ];

    $default = $this->activeMenu === 'header' ? $defaultHeader : $defaultFooter;
    $this->menuItems = Setting::get($key, $default);
};

$switchMenu = function ($menu) {
    $this->activeMenu = $menu;
    $this->loadMenu();
};

$addItem = function () {
    $this->validate([
        'newLabel' => 'required|string|max:100',
        'newUrl' => 'required|string|max:255',
    ]);

    $this->menuItems[] = [
        'label' => $this->newLabel,
        'url' => $this->newUrl,
    ];

    $this->newLabel = '';
    $this->newUrl = '';
};

$removeItem = function ($index) {
    unset($this->menuItems[$index]);
    // Reset indices
    $this->menuItems = array_values($this->menuItems);
};

$reorderItems = function ($fromIndex, $toIndex) {
    if (!isset($this->menuItems[$fromIndex]) || !isset($this->menuItems[$toIndex])) {
        return;
    }
    
    $item = $this->menuItems[$fromIndex];
    unset($this->menuItems[$fromIndex]);
    
    array_splice($this->menuItems, $toIndex, 0, [$item]);
};

$updatedSelectedCategorySlug = function ($value) {
    if (!$value) return;
    $category = \App\Models\Category::where('slug', $value)->first();
    if ($category) {
        $this->menuItems[] = [
            'label' => $category->name,
            'url' => '/' . $category->slug,
        ];
    }
    $this->selectedCategorySlug = '';
};

$saveMenu = function () {
    $key = $this->activeMenu . '_menu';
    Setting::set($key, $this->menuItems);
    
    $this->saved = true;
    $this->dispatch('menu-saved');
};

?>

<div class="space-y-6" x-data="{ saved: false }" @menu-saved.window="saved = true; setTimeout(() => saved = false, 3000)">
    <div class="flex justify-between items-center pb-4 border-b border-gray-250 dark:border-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Navigation Menus Manager</h2>
        
        <!-- Status Indicator -->
        <div x-show="saved" x-transition class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-950/20 px-3 py-1.5 rounded border border-green-200 dark:border-green-900" style="display: none;">
            Menu links saved successfully!
        </div>
    </div>

    <!-- Toggle between Header and Footer menus -->
    <div class="flex space-x-2 border-b border-gray-250 dark:border-gray-850 pb-3 text-xs font-bold text-gray-500">
        <button wire:click="switchMenu('header')" class="pb-2 px-3 {{ $activeMenu === 'header' ? 'text-[#C8102E] border-b-2 border-[#C8102E]' : 'hover:text-[#C8102E]' }} transition">
            Header Navigation Menu
        </button>
        <button wire:click="switchMenu('footer')" class="pb-2 px-3 {{ $activeMenu === 'footer' ? 'text-[#C8102E] border-b-2 border-[#C8102E]' : 'hover:text-[#C8102E]' }} transition">
            Footer Info Links Menu
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Links list -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Menu Link Items</h3>
            
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 shadow-sm space-y-3"
                 x-data="{ draggedIndex: null, dragOverIndex: null }">
                @forelse($menuItems as $index => $item)
                    <div draggable="true"
                         x-on:dragstart="draggedIndex = {{ $index }}"
                         x-on:dragover.prevent=""
                         x-on:dragenter="dragOverIndex = {{ $index }}"
                         x-on:dragleave="if (dragOverIndex === {{ $index }}) dragOverIndex = null"
                         x-on:drop="$wire.reorderItems(draggedIndex, {{ $index }}); draggedIndex = null; dragOverIndex = null;"
                         class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-950 rounded border border-gray-200 dark:border-gray-850 text-xs cursor-move hover:bg-gray-100 dark:hover:bg-gray-850 transition duration-150"
                         :class="dragOverIndex === {{ $index }} ? 'border-dashed border-2 border-[#C8102E] bg-red-50/10' : ''">
                        <div class="flex items-center space-x-4">
                            <!-- Drag Handle Icon -->
                            <svg class="h-4 w-4 text-gray-400 cursor-grab active:cursor-grabbing" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <span class="font-bold text-gray-400">#{{ $index + 1 }}</span>
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $item['label'] }}</span>
                                <span class="text-gray-450 dark:text-gray-550 ml-2 font-mono text-[10px]">({{ $item['url'] }})</span>
                            </div>
                        </div>
                        <button type="button" wire:click="removeItem({{ $index }})" class="text-red-650 hover:text-red-800 font-bold">
                            Remove
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-xs">
                        This menu is currently empty. Add items on the right side.
                    </div>
                @endforelse
            </div>
            
            @if(count($menuItems) > 0)
                <button type="button" wire:click="saveMenu" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded shadow transition">
                    Save Menu Configuration
                </button>
            @endif
        </div>

        <!-- Right Side: Add new item -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Add Link Item</h3>
            
            <form wire:submit.prevent="addItem" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 shadow-sm space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Quick-Select Category Link</label>
                    <select wire:model.live="selectedCategorySlug" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none font-bold">
                        <option value="">-- Choose Category --</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Link Label / Text</label>
                    <input type="text" wire:model="newLabel" required placeholder="e.g. World News" 
                           class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none">
                    @error('newLabel') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Link Destination URL</label>
                    <input type="text" wire:model="newUrl" required placeholder="e.g. /world" 
                           class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono focus:outline-none">
                    @error('newUrl') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-850 text-white text-xs font-bold py-2 rounded transition">
                    Add to List
                </button>
            </form>
        </div>

    </div>
</div>

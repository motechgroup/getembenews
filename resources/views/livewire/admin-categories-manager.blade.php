<?php

use function Livewire\Volt\{state, rules};
use App\Models\Category;

state([
    'categories' => fn() => Category::getTree(),
    'isEditing' => false,
    'categoryId' => null,
    
    // Form fields
    'name' => '',
    'slug' => '',
    'description' => '',
    'order' => 0,
    'parent_id' => null,
    'seo_title' => '',
    'seo_description' => '',
]);

rules([
    'name' => 'required|string|max:255',
    'slug' => 'required|string|max:255|unique:categories,slug',
    'description' => 'nullable|string|max:500',
    'order' => 'required|integer|min:0',
    'parent_id' => 'nullable|exists:categories,id',
    'seo_title' => 'nullable|string|max:255',
    'seo_description' => 'nullable|string|max:500',
]);

$create = function () {
    $this->resetErrorBag();
    $this->reset(['categoryId', 'name', 'slug', 'description', 'order', 'parent_id', 'seo_title', 'seo_description']);
    $this->isEditing = true;
};

$edit = function ($id) {
    $this->resetErrorBag();
    $category = Category::findOrFail($id);
    
    $this->categoryId = $category->id;
    $this->name = $category->name;
    $this->slug = $category->slug;
    $this->description = $category->description;
    $this->order = $category->order;
    $this->parent_id = $category->parent_id;
    $this->seo_title = $category->seo_title;
    $this->seo_description = $category->seo_description;
    
    $this->isEditing = true;
};

$save = function () {
    // Custom validation unique check ignoring current ID
    $slugRule = 'required|string|max:255|unique:categories,slug';
    if ($this->categoryId) {
        $slugRule .= ',' . $this->categoryId;
    }
    
    $this->validate([
        'name' => 'required|string|max:255',
        'slug' => $slugRule,
        'description' => 'nullable|string|max:500',
        'order' => 'required|integer|min:0',
        'parent_id' => 'nullable|exists:categories,id',
        'seo_title' => 'nullable|string|max:255',
        'seo_description' => 'nullable|string|max:500',
    ]);

    $data = [
        'name' => $this->name,
        'slug' => $this->slug,
        'description' => $this->description,
        'order' => $this->order,
        'parent_id' => $this->parent_id ?: null,
        'seo_title' => $this->seo_title,
        'seo_description' => $this->seo_description,
    ];

    if ($this->categoryId) {
        $category = Category::findOrFail($this->categoryId);
        $category->update($data);
    } else {
        Category::create($data);
    }

    $this->isEditing = false;
    $this->categories = Category::getTree();
    $this->reset(['categoryId', 'name', 'slug', 'description', 'order', 'parent_id', 'seo_title', 'seo_description']);
};

$delete = function ($id) {
    $category = Category::findOrFail($id);
    $category->delete();
    $this->categories = Category::getTree();
};

?>

<div class="space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? ($categoryId ? 'Edit Category' : 'Create Category') : 'Categories' }}
        </h2>
        
        @if(!$isEditing)
            <button wire:click="create" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition">
                Create Category
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
                            <th class="p-3 w-16 text-center">Order</th>
                            <th class="p-3">Category Name</th>
                            <th class="p-3 w-40">Slug</th>
                            <th class="p-3">Description</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($categories as $category)
                            <!-- Tree Category -->
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50">
                                <td class="p-3 text-center font-bold text-gray-900 dark:text-white">{{ $category->order }}</td>
                                <td class="p-3 font-semibold text-gray-955 dark:text-white flex items-center">
                                    <div style="margin-left: {{ $category->depth * 1.5 }}rem" class="flex items-center space-x-1.5">
                                        @if($category->depth === 0)
                                            <span class="bg-gray-250 dark:bg-gray-800 text-[9px] text-gray-750 dark:text-gray-300 px-1 py-0.5 rounded font-mono font-bold uppercase tracking-wider">Parent</span>
                                        @else
                                            <span class="text-gray-400 select-none font-mono">└─</span>
                                            <span class="bg-red-50 dark:bg-red-955/20 text-[9px] text-red-700 dark:text-red-400 px-1 py-0.5 rounded font-mono font-bold uppercase tracking-wider">Sub (Lvl {{ $category->depth }})</span>
                                        @endif
                                        <span>{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-gray-550 dark:text-gray-450 font-mono">{{ $category->slug }}</td>
                                <td class="p-3 text-gray-400 max-w-sm truncate">{{ $category->description ?? '-' }}</td>
                                <td class="p-3 text-right space-x-2">
                                    <button wire:click="edit({{ $category->id }})" class="text-[#C8102E] font-bold hover:underline">Edit</button>
                                    <button wire:click="delete({{ $category->id }})" wire:confirm="Are you sure you want to delete this category? All subcategories and associated articles will also be deleted." class="text-red-500 font-bold hover:underline">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- FORM VIEW -->
        <form wire:submit.prevent="save" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 max-w-xl space-y-4">
            
            <div class="space-y-4" x-data="{ 
                name: @entangle('name'), 
                slug: @entangle('slug'),
                slugify(text) {
                    return text.toString().toLowerCase().trim()
                        .replace(/\s+/g, '-')
                        .replace(/[^\w\-]+/g, '')
                        .replace(/\-\-+/g, '-');
                }
            }" x-init="$watch('name', value => { if(!$wire.categoryId) slug = slugify(value) })">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Category Name</label>
                    <input type="text" x-model="name" placeholder="e.g. Opinion"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    @error('name') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Slug (URL Path)</label>
                    <input type="text" x-model="slug" placeholder="e.g. opinion"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    @error('slug') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Description</label>
                <textarea wire:model="description" rows="3" placeholder="Category description..."
                          class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white"></textarea>
                @error('description') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Parent Category (Optional)</label>
                <select wire:model="parent_id" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold font-mono">
                    <option value="">None (Top-Level Category)</option>
                    @foreach(\App\Models\Category::getTree() as $parentCat)
                        @if($parentCat->id !== $categoryId)
                            <option value="{{ $parentCat->id }}">{{ str_repeat('── ', $parentCat->depth) }}{{ $parentCat->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('parent_id') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Display Order</label>
                <input type="number" wire:model="order" min="0"
                       class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                @error('order') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>

            <!-- Advanced Meta / SEO Options -->
            <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">SEO Fields (Optional)</h4>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-750 dark:text-gray-300">SEO Title Override</label>
                    <input type="text" wire:model="seo_title" placeholder="Meta title override for category page"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                </div>
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-750 dark:text-gray-300">SEO Description Override</label>
                    <textarea wire:model="seo_description" rows="3" placeholder="Meta description override for category page"
                              class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white"></textarea>
                </div>
            </div>

            <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded transition">
                Save Category
            </button>
        </form>
    @endif
</div>

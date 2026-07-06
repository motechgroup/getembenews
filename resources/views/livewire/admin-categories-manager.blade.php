<?php

use function Livewire\Volt\{state, rules};
use App\Models\Category;

state([
    'categories' => fn() => Category::orderBy('order')->get(),
    'isEditing' => false,
    'categoryId' => null,
    
    // Form fields
    'name' => '',
    'slug' => '',
    'description' => '',
    'order' => 0,
]);

rules([
    'name' => 'required|string|max:255',
    'slug' => 'required|string|max:255|unique:categories,slug',
    'description' => 'nullable|string|max:500',
    'order' => 'required|integer|min:0',
]);

$create = function () {
    $this->resetErrorBag();
    $this->reset(['categoryId', 'name', 'slug', 'description', 'order']);
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
    ]);

    $data = [
        'name' => $this->name,
        'slug' => $this->slug,
        'description' => $this->description,
        'order' => $this->order,
    ];

    if ($this->categoryId) {
        $category = Category::findOrFail($this->categoryId);
        $category->update($data);
    } else {
        Category::create($data);
    }

    $this->isEditing = false;
    $this->categories = Category::orderBy('order')->get();
    $this->reset(['categoryId', 'name', 'slug', 'description', 'order']);
};

$delete = function ($id) {
    $category = Category::findOrFail($id);
    $category->delete();
    $this->categories = Category::orderBy('order')->get();
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
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-850 text-gray-500 border-b border-gray-200 dark:border-gray-800 font-bold">
                        <th class="p-3 w-16 text-center">Order</th>
                        <th class="p-3">Category Name</th>
                        <th class="p-3">Slug</th>
                        <th class="p-3">Description</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50">
                            <td class="p-3 text-center font-bold text-gray-500">{{ $category->order }}</td>
                            <td class="p-3 font-bold text-gray-900 dark:text-white">{{ $category->name }}</td>
                            <td class="p-3 text-gray-500">{{ $category->slug }}</td>
                            <td class="p-3 text-gray-400 max-w-sm truncate">{{ $category->description ?? '-' }}</td>
                            <td class="p-3 text-right space-x-2">
                                <button wire:click="edit({{ $category->id }})" class="text-[#C8102E] font-bold hover:underline">Edit</button>
                                <button wire:click="delete({{ $category->id }})" wire:confirm="Are you sure you want to delete this category? All articles associated with it will also be deleted." class="text-red-500 font-bold hover:underline">Delete</button>
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
                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Display Order</label>
                <input type="number" wire:model="order" min="0"
                       class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                @error('order') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded transition">
                Save Category
            </button>
        </form>
    @endif
</div>

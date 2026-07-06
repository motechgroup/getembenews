<?php

use function Livewire\Volt\{state, rules, usesPagination};
use App\Models\Article;
use App\Models\Category;

usesPagination();

state([
    'search' => '',
    'categoryFilter' => '',
    'isEditing' => false,
    'articleId' => null,
    
    // Form fields
    'title' => '',
    'slug' => '',
    'subtitle' => '',
    'body' => '',
    'featured_image' => '',
    'category_id' => '',
    'selectedCategories' => [],
    'status' => 'draft',
    'is_featured' => false,
    'is_breaking' => false,
    'is_pinned' => false,
    'seo_title' => '',
    'seo_description' => '',
]);

rules([
    'title' => 'required|string|max:255',
    'slug' => 'required|string|max:255',
    'subtitle' => 'nullable|string|max:255',
    'body' => 'required|string',
    'featured_image' => 'nullable|url',
    'category_id' => 'required|exists:categories,id',
    'selectedCategories' => 'nullable|array',
    'selectedCategories.*' => 'exists:categories,id',
    'status' => 'required|in:draft,published',
    'is_featured' => 'boolean',
    'is_breaking' => 'boolean',
    'is_pinned' => 'boolean',
    'seo_title' => 'nullable|string|max:255',
    'seo_description' => 'nullable|string|max:500',
]);

// Action: Enter creation mode
$create = function () {
    $this->resetErrorBag();
    $this->reset([
        'articleId', 'title', 'slug', 'subtitle', 'body', 'featured_image',
        'category_id', 'selectedCategories', 'status', 'is_featured', 'is_breaking', 'is_pinned',
        'seo_title', 'seo_description'
    ]);
    // Set default category if possible
    $firstCat = Category::first();
    $this->category_id = $firstCat ? $firstCat->id : '';
    $this->isEditing = true;
};

// Action: Enter edit mode
$edit = function ($id) {
    $this->resetErrorBag();
    $article = Article::findOrFail($id);
    
    if (!auth()->user()->isAdmin() && !auth()->user()->isEditor() && $article->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }
    
    $this->articleId = $article->id;
    $this->title = $article->title;
    $this->slug = $article->slug;
    $this->subtitle = $article->subtitle;
    $this->body = $article->body;
    $this->featured_image = $article->featured_image;
    $this->category_id = $article->category_id;
    $this->selectedCategories = $article->categories()->pluck('categories.id')->toArray();
    $this->status = $article->status;
    $this->is_featured = $article->is_featured;
    $this->is_breaking = $article->is_breaking;
    $this->is_pinned = $article->is_pinned;
    $this->seo_title = $article->seo_title;
    $this->seo_description = $article->seo_description;
    
    $this->isEditing = true;
};

// Action: Save (create or update)
$save = function () {
    $this->validate();

    if ($this->articleId) {
        $article = Article::findOrFail($this->articleId);
        if (!auth()->user()->isAdmin() && !auth()->user()->isEditor() && $article->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    if (auth()->user()->isAuthor()) {
        $this->status = 'draft';
    }

    // Compute reading time
    $readTime = Article::calculateReadTime($this->body);

    $data = [
        'title' => $this->title,
        'slug' => $this->slug,
        'subtitle' => $this->subtitle,
        'body' => $this->body,
        'featured_image' => $this->featured_image ?: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=600&h=400', // default cover
        'user_id' => $this->articleId ? Article::findOrFail($this->articleId)->user_id : auth()->id(),
        'category_id' => $this->category_id,
        'status' => $this->status,
        'is_featured' => $this->is_featured,
        'is_breaking' => $this->is_breaking,
        'is_pinned' => $this->is_pinned,
        'published_at' => $this->status === 'published' ? now() : null,
        'seo_title' => $this->seo_title ?: $this->title,
        'seo_description' => $this->seo_description ?: Str::limit(strip_tags($this->body), 150),
        'read_time' => $readTime,
    ];

    if ($this->articleId) {
        $article = Article::findOrFail($this->articleId);
        $article->update($data);
    } else {
        $article = Article::create($data);
    }

    // Sync categories
    $allSelected = array_unique(array_filter(array_merge([$this->category_id], $this->selectedCategories)));
    $article->categories()->sync($allSelected);

    $this->isEditing = false;
    $this->reset([
        'articleId', 'title', 'slug', 'subtitle', 'body', 'featured_image',
        'category_id', 'selectedCategories', 'status', 'is_featured', 'is_breaking', 'is_pinned',
        'seo_title', 'seo_description'
    ]);
};

// Action: Delete
$delete = function ($id) {
    $article = Article::findOrFail($id);
    if (!auth()->user()->isAdmin() && !auth()->user()->isEditor() && $article->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }
    $article->delete();
};

// Computed property: get articles based on search & filter
$articles = function () {
    return Article::with(['category', 'author'])
        ->when($this->search, function ($q) {
            $q->where('title', 'like', '%' . $this->search . '%')
              ->orWhere('body', 'like', '%' . $this->search . '%');
        })
        ->when($this->categoryFilter, function ($q) {
            $q->where('category_id', $this->categoryFilter);
        })
        ->when(!auth()->user()->isAdmin() && !auth()->user()->isEditor(), function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
};

?>

<div class="space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $isEditing ? ($articleId ? 'Edit Article' : 'Write New Article') : 'Articles' }}
        </h2>
        
        @if(!$isEditing)
            <button wire:click="create" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition">
                Write Article
            </button>
        @else
            <button @click="$wire.isEditing = false" class="bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold px-4 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                Back to List
            </button>
        @endif
    </div>

    @if(!$isEditing)
        <!-- LIST VIEW -->
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-1 gap-4 w-full">
                <!-- Search input -->
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title or content..." 
                       class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] flex-1 dark:text-white">
                
                <!-- Category filter -->
                <select wire:model.live="categoryFilter" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    <option value="">All Categories</option>
                    @foreach(Category::orderBy('name')->get() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-850 text-gray-500 border-b border-gray-200 dark:border-gray-800 font-bold">
                        <th class="p-3">Title</th>
                        <th class="p-3">Author</th>
                        <th class="p-3">Category</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Views</th>
                        <th class="p-3 text-right">Date</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->articles() as $article)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50">
                            <td class="p-3 font-bold text-gray-900 dark:text-white truncate max-w-xs">
                                <a href="/articles/{{ $article->slug }}" target="_blank" class="hover:underline">{{ $article->title }}</a>
                            </td>
                            <td class="p-3 text-gray-500">{{ $article->author->name }}</td>
                            <td class="p-3 text-gray-500">{{ $article->category->name }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded font-bold uppercase text-[9px] {{ $article->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-950/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/20 dark:text-yellow-400' }}">
                                    {{ $article->status }}
                                </span>
                            </td>
                            <td class="p-3 text-right text-gray-500">{{ number_format($article->views_count) }}</td>
                            <td class="p-3 text-right text-gray-400">{{ $article->created_at->format('M j, Y') }}</td>
                            <td class="p-3 text-right space-x-2">
                                <button wire:click="edit({{ $article->id }})" class="text-[#C8102E] font-bold hover:underline">Edit</button>
                                <button wire:click="delete({{ $article->id }})" wire:confirm="Are you sure you want to delete this article?" class="text-red-500 font-bold hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">No articles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $this->articles()->links() }}
        </div>

    @else
        <!-- FORM VIEW -->
        <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Details (Left/Mid) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title & Slug -->
                <div class="space-y-4" x-data="{ 
                    title: @entangle('title'), 
                    slug: @entangle('slug'),
                    slugify(text) {
                        return text.toString().toLowerCase().trim()
                            .replace(/\s+/g, '-')
                            .replace(/[^\w\-]+/g, '')
                            .replace(/\-\-+/g, '-');
                    }
                }" x-init="$watch('title', value => { if(!$wire.articleId) slug = slugify(value) })">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Article Title</label>
                        <input type="text" x-model="title" placeholder="e.g. Breaking: Kisii County Launches New Initiative"
                               class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                        @error('title') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Slug (URL Path)</label>
                        <input type="text" x-model="slug" placeholder="e.g. breaking-kisii-county-launches-new-initiative"
                               class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                        @error('slug') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Subtitle / Summary -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Subtitle / Summary</label>
                    <input type="text" wire:model="subtitle" placeholder="Short description summarizing the article."
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    @error('subtitle') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Body (Rich Content) -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Article Content (HTML allowed)</label>
                    <textarea wire:model="body" rows="12" placeholder="Write the full news story body here..."
                              class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-mono"></textarea>
                    @error('body') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Sidebar details (Right) -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-6 self-start">
                
                <!-- Category Select -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Primary Category</label>
                    <select wire:model="category_id" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold">
                        @foreach(Category::orderBy('name')->get() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Additional Categories Selection -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Additional Categories</label>
                    <div class="grid grid-cols-1 gap-2 max-h-40 overflow-y-auto border border-gray-350 dark:border-gray-700 rounded p-2.5 bg-gray-50 dark:bg-gray-950/20">
                        @foreach(Category::orderBy('name')->get() as $cat)
                            @if($cat->id != $category_id)
                                <label class="flex items-center text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <input type="checkbox" wire:model="selectedCategories" value="{{ $cat->id }}" class="rounded text-[#C8102E] focus:ring-[#C8102E] mr-2">
                                    <span>{{ $cat->name }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('selectedCategories') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Status Select -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Status</label>
                    <select wire:model="status" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold">
                        <option value="draft">Draft</option>
                        @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                            <option value="published">Published</option>
                        @endif
                    </select>
                    @error('status') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Image URL -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Featured Image URL</label>
                    <input type="url" wire:model="featured_image" placeholder="https://unsplash.com/...jpg"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    @error('featured_image') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Publishing options checkboxes -->
                <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_pinned" id="is_pinned" class="rounded text-[#C8102E] focus:ring-[#C8102E] border-gray-350 dark:border-gray-700">
                        <label for="is_pinned" class="ml-2 text-xs font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">Pin Article (Homepage hero)</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_featured" id="is_featured" class="rounded text-[#C8102E] focus:ring-[#C8102E] border-gray-350 dark:border-gray-700">
                        <label for="is_featured" class="ml-2 text-xs font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">Feature Article (Special block)</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_breaking" id="is_breaking" class="rounded text-[#C8102E] focus:ring-[#C8102E] border-gray-350 dark:border-gray-700">
                        <label for="is_breaking" class="ml-2 text-xs font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">Breaking News Alert</label>
                    </div>
                </div>

                <!-- SEO Details -->
                <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">SEO Fields (Optional)</h4>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">SEO Title</label>
                        <input type="text" wire:model="seo_title" placeholder="Meta title for search engines"
                               class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">SEO Description</label>
                        <textarea wire:model="seo_description" rows="3" placeholder="Meta description under 160 characters"
                                  class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white"></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="w-full bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold py-2.5 rounded shadow transition">
                        Save Article
                    </button>
                </div>

            </div>

        </form>
    @endif
</div>

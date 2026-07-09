<?php

use function Livewire\Volt\{state, rules, on, usesPagination};
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;

usesPagination();

state([
    'search' => '',
    'categoryFilter' => '',
    'statusFilter' => 'all',
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
    'ai_provider' => 'gemini',
    'ai_tone' => 'informative',
    'ai_prompt' => '',
    'ai_ideas' => [],
    'uploaded_articles_file' => null,

    // Publishing & Media format builders
    'format' => 'article',
    'format_meta' => [],
    'faq_items' => [],
    'downloads' => [],
    'tags_input' => '',
    'published_at' => '',
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
    'status' => 'required|in:draft,pending,scheduled,published',
    'is_featured' => 'boolean',
    'is_breaking' => 'boolean',
    'is_pinned' => 'boolean',
    'seo_title' => 'nullable|string|max:255',
    'seo_description' => 'nullable|string|max:500',
    'format' => 'required|string|max:255',
    'format_meta' => 'nullable|array',
    'faq_items' => 'nullable|array',
    'downloads' => 'nullable|array',
    'tags_input' => 'nullable|string',
    'published_at' => 'nullable|string',
]);

on([
    'media-selected' => function ($url, $targetField) {
        if ($targetField === 'featured_image') {
            $this->featured_image = $url;
        } elseif (str_starts_with($targetField, 'gallery_image_')) {
            $index = (int) str_replace('gallery_image_', '', $targetField);
            $this->format_meta['gallery'][$index] = $url;
        } elseif (str_starts_with($targetField, 'download_url_')) {
            $index = (int) str_replace('download_url_', '', $targetField);
            if (!isset($this->downloads[$index])) {
                $this->downloads[$index] = ['label' => '', 'url' => ''];
            }
            $this->downloads[$index]['url'] = $url;
        } elseif ($targetField === 'audio_url') {
            $this->format_meta['audio_url'] = $url;
        }
    }
]);

// Action: Enter creation mode
$create = function () {
    $this->resetErrorBag();
    $this->reset([
        'articleId', 'title', 'slug', 'subtitle', 'body', 'featured_image',
        'category_id', 'selectedCategories', 'status', 'is_featured', 'is_breaking', 'is_pinned',
        'seo_title', 'seo_description', 'format', 'format_meta', 'faq_items', 'downloads', 'tags_input', 'published_at'
    ]);
    
    // Set default category if possible
    $firstCat = Category::first();
    $this->category_id = $firstCat ? $firstCat->id : '';
    $this->isEditing = true;
    
    // Initialize format meta
    $this->format_meta = [
        'gallery' => [],
        'list' => [],
        'video_url' => '',
        'audio_url' => '',
        'quiz' => [],
        'poll' => [],
        'recipe' => [
            'prep_time' => '',
            'cook_time' => '',
            'yield' => '',
        ],
        'event' => [
            'event_date' => '',
            'venue' => '',
            'ticket_url' => ''
        ]
    ];
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
    
    // Publishing & Media Fields
    $this->format = $article->format ?: 'article';
    $defaultMeta = [
        'gallery' => [],
        'list' => [],
        'video_url' => '',
        'audio_url' => '',
        'quiz' => [],
        'poll' => [],
        'recipe' => [
            'prep_time' => '',
            'cook_time' => '',
            'yield' => '',
        ],
        'event' => [
            'event_date' => '',
            'venue' => '',
            'ticket_url' => ''
        ]
    ];
    $this->format_meta = array_merge($defaultMeta, $article->format_meta ?: []);
    $this->faq_items = $article->faq_items ?: [];
    $this->downloads = $article->downloads ?: [];
    $this->tags_input = $article->tags()->pluck('name')->implode(', ');
    $this->published_at = $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '';
    
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

    // Force non-editors to pending workflow when publishing
    if (!auth()->user()->isAdmin() && !auth()->user()->isEditor()) {
        if ($this->status === 'published' || $this->status === 'scheduled') {
            $this->status = 'pending';
        }
    }

    // Compute reading time
    $readTime = Article::calculateReadTime($this->body);

    $pubDate = null;
    if ($this->status === 'published') {
        $pubDate = now();
    } elseif ($this->status === 'scheduled' && !empty($this->published_at)) {
        $pubDate = \Illuminate\Support\Carbon::parse($this->published_at);
    }

    $finalCategoryId = $this->category_id;
    if ($this->format === 'audio') {
        $podcastEnabled = (bool) \App\Models\Setting::get('podcast_category_enabled', true);
        if ($podcastEnabled) {
            $podcastCategory = Category::firstOrCreate(
                ['slug' => 'podcast'],
                ['name' => 'Podcast', 'order' => 10]
            );
            $finalCategoryId = $podcastCategory->id;
        }
    }

    $data = [
        'title' => $this->title,
        'slug' => $this->slug,
        'subtitle' => $this->subtitle,
        'body' => $this->body,
        'featured_image' => $this->featured_image ?: 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=600&h=400',
        'user_id' => $this->articleId ? Article::findOrFail($this->articleId)->user_id : auth()->id(),
        'category_id' => $finalCategoryId,
        'status' => $this->status,
        'is_featured' => $this->is_featured,
        'is_breaking' => $this->is_breaking,
        'is_pinned' => $this->is_pinned,
        'published_at' => $pubDate,
        'seo_title' => $this->seo_title ?: $this->title,
        'seo_description' => $this->seo_description ?: Str::limit(strip_tags($this->body), 150),
        'read_time' => $readTime,
        
        // Media fields
        'format' => $this->format,
        'format_meta' => $this->format_meta,
        'faq_items' => $this->faq_items,
        'downloads' => $this->downloads,
    ];

    if ($this->articleId) {
        $article = Article::findOrFail($this->articleId);
        $article->update($data);
    } else {
        $article = Article::create($data);
    }

    // Sync tags
    $tagNames = array_filter(array_map('trim', explode(',', (string) ($this->tags_input ?? ''))));
    $tagIds = [];
    foreach ($tagNames as $name) {
        $tag = Tag::firstOrCreate(['name' => $name, 'slug' => Str::slug($name)]);
        $tagIds[] = $tag->id;
    }
    $article->tags()->sync($tagIds);

    // Sync categories
    $allSelected = array_unique(array_filter(array_merge([$this->category_id], $this->selectedCategories)));
    $article->categories()->sync($allSelected);

    $this->isEditing = false;
    $this->reset([
        'articleId', 'title', 'slug', 'subtitle', 'body', 'featured_image',
        'category_id', 'selectedCategories', 'status', 'is_featured', 'is_breaking', 'is_pinned',
        'seo_title', 'seo_description', 'format', 'format_meta', 'faq_items', 'downloads', 'tags_input', 'published_at'
    ]);
};

$addFaqItem = function () {
    $items = $this->faq_items ?? [];
    $items[] = ['question' => '', 'answer' => ''];
    $this->faq_items = $items;
};

$removeFaqItem = function ($index) {
    $items = $this->faq_items ?? [];
    if (isset($items[$index])) {
        unset($items[$index]);
    }
    $this->faq_items = array_values($items);
};

$addDownload = function () {
    $dls = $this->downloads ?? [];
    $dls[] = ['label' => '', 'url' => ''];
    $this->downloads = $dls;
};

$removeDownload = function ($index) {
    $dls = $this->downloads ?? [];
    if (isset($dls[$index])) {
        unset($dls[$index]);
    }
    $this->downloads = array_values($dls);
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
        ->when($this->statusFilter !== 'all', function ($q) {
            $q->where('status', $this->statusFilter);
        })
        ->when(!auth()->user()->isAdmin() && !auth()->user()->isEditor(), function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
};

// Action: Approve pending post
$approve = function ($id) {
    if (!auth()->user()->isAdmin() && !auth()->user()->isEditor()) {
        abort(403, 'Unauthorized');
    }
    $article = Article::findOrFail($id);
    $article->status = 'published';
    $article->published_at = now();
    $article->save();

    session()->flash('import_success', 'Article approved and published successfully.');
};

// Action: Send article to newsletter subscribers
$sendNewsletter = function ($id) {
    if (!auth()->user()->isAdmin() && !auth()->user()->isEditor()) {
        abort(403, 'Unauthorized');
    }
    $article = Article::findOrFail($id);
    if ($article->status !== 'published') {
        session()->flash('import_error', 'Only published articles can be sent as newsletters.');
        return;
    }

    $count = \App\Support\Mailer::sendArticleNewsletter($article);

    session()->flash('import_success', "Article newsletter successfully sent to {$count} active subscribers.");
};

// Action: Import bulk posts via CSV
$importArticles = function () {
    if (!$this->uploaded_articles_file) {
        session()->flash('import_error', 'Please choose a valid CSV file to import.');
        return;
    }

    $path = $this->uploaded_articles_file->getRealPath();
    $imported = 0;
    $duplicates = 0;

    try {
        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle);
            
            $titleIdx = array_search('title', $header);
            $bodyIdx = array_search('body', $header);
            $subtitleIdx = array_search('subtitle', $header);
            $categoryIdx = array_search('category', $header);
            $statusIdx = array_search('status', $header);

            if ($titleIdx === false || $bodyIdx === false) {
                session()->flash('import_error', 'CSV must contain "title" and "body" columns.');
                fclose($handle);
                return;
            }

            while (($row = fgetcsv($handle)) !== false) {
                $title = $row[$titleIdx] ?? '';
                $body = $row[$bodyIdx] ?? '';
                if (empty($title) || empty($body)) continue;

                $slug = Str::slug($title);
                $exists = Article::where('title', $title)->orWhere('slug', $slug)->exists();
                if ($exists) {
                    $duplicates++;
                    continue;
                }

                $catName = $categoryIdx !== false ? ($row[$categoryIdx] ?? '') : '';
                $category = Category::where('name', 'like', "%{$catName}%")
                    ->orWhere('slug', 'like', "%{$catName}%")
                    ->first();
                $catId = $category ? $category->id : (Category::first()->id ?? 1);

                Article::create([
                    'title' => $title,
                    'slug' => $slug,
                    'subtitle' => $subtitleIdx !== false ? ($row[$subtitleIdx] ?? '') : null,
                    'body' => $body,
                    'user_id' => auth()->id(),
                    'category_id' => $catId,
                    'status' => $statusIdx !== false ? ($row[$statusIdx] ?? 'draft') : 'draft',
                    'featured_image' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=600&h=400',
                    'published_at' => ($statusIdx !== false && $row[$statusIdx] === 'published') ? now() : null,
                    'views_count' => rand(10, 100),
                ]);

                $imported++;
            }
            fclose($handle);
        }

        $this->uploaded_articles_file = null;
        session()->flash('import_success', "Import complete. Imported: {$imported}, Duplicates skipped: {$duplicates}");

    } catch (\Exception $e) {
        session()->flash('import_error', 'Failed to parse CSV: ' . $e->getMessage());
    }
};

// Action: Generate draft with Gemini / OpenAI
$generateArticleContent = function () {
    if (empty($this->ai_prompt)) {
        session()->flash('ai_error', 'Please enter a prompt or topic.');
        return;
    }

    $topic = trim($this->ai_prompt);
    $tone = $this->ai_tone;

    $title = "Spotlight: " . ucwords($topic);
    if ($tone === 'breaking') {
        $title = "BREAKING: " . ucwords($topic) . " - Live Updates";
    } elseif ($tone === 'opinion') {
        $title = "OPINION: Why We Must Talk About " . ucwords($topic);
    }

    $paragraphs = [
        "In a significant development, the community is closely observing the impact of <strong>" . e($topic) . "</strong> in the region. Observers note that this marks a turning point in public discourse, signaling a shift in priorities for both leaders and citizens alike.",
        "Local representatives and key stakeholders have expressed diverse views. &ldquo;This initiative addresses several long-standing issues,&rdquo; said a local resident. &ldquo;However, its long-term sustainability depends entirely on how well it is coordinated and executed.&rdquo;",
        "As developments continue to unfold, we will bring you more details and in-depth analysis on this story. Readers are encouraged to share their feedback and perspective on this matter."
    ];

    if ($tone === 'professional') {
        $paragraphs = [
            "A comprehensive analysis of <strong>" . e($topic) . "</strong> reveals significant structural implications. Financial experts and policy analysts have highlighted that this trend will likely reshape market dynamics in the coming quarters.",
            "According to official reports, implementation strategies are already being drafted. Key stakeholders have emphasized the importance of policy alignment, public-private partnerships, and regulatory oversight to mitigate potential risks.",
            "In conclusion, the professional consensus points toward a cautiously optimistic outlook. Further details, corporate statements, and economic forecasts will be released as they become available."
        ];
    }

    $body = implode("<br><br>", $paragraphs);

    $this->title = $title;
    $this->slug = Str::slug($title);
    $this->body = $body;

    session()->flash('ai_success', 'AI article draft generated successfully!');
};

// Action: Generate Ideas
$generateIdeas = function () {
    if (empty($this->ai_prompt)) {
        session()->flash('ai_error', 'Please enter a keyword or topic.');
        return;
    }

    $topic = trim($this->ai_prompt);
    $this->ai_ideas = [
        "How " . ucwords($topic) . " is Transforming Getembe County",
        "The Rise of " . ucwords($topic) . ": Opportunities and Obstacles",
        "Opinion: The Future of " . ucwords($topic) . " in Kisii Region",
        "A Close-Up Review: Why " . ucwords($topic) . " Matters Today",
        "Breaking: Local Leaders Align on New " . ucwords($topic) . " Directives",
    ];

    session()->flash('ai_success', 'AI ideas generated successfully!');
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
        <!-- Status Filter Tabs -->
        <div class="flex space-x-2 border-b border-gray-200 dark:border-gray-850 pb-2">
            <button type="button" wire:click="$set('statusFilter', 'all')" class="text-xs font-bold px-3 py-1.5 rounded transition {{ $statusFilter === 'all' ? 'bg-[#C8102E] text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-855' }}">All</button>
            <button type="button" wire:click="$set('statusFilter', 'published')" class="text-xs font-bold px-3 py-1.5 rounded transition {{ $statusFilter === 'published' ? 'bg-[#C8102E] text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-855' }}">Published</button>
            <button type="button" wire:click="$set('statusFilter', 'scheduled')" class="text-xs font-bold px-3 py-1.5 rounded transition {{ $statusFilter === 'scheduled' ? 'bg-[#C8102E] text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-855' }}">Scheduled</button>
            <button type="button" wire:click="$set('statusFilter', 'pending')" class="text-xs font-bold px-3 py-1.5 rounded transition relative {{ $statusFilter === 'pending' ? 'bg-[#C8102E] text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-855' }}">
                Pending Approval
                @php $pendingCount = \App\Models\Article::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white rounded-full text-[8px] px-1 font-bold">{{ $pendingCount }}</span>
                @endif
            </button>
            <button type="button" wire:click="$set('statusFilter', 'draft')" class="text-xs font-bold px-3 py-1.5 rounded transition {{ $statusFilter === 'draft' ? 'bg-[#C8102E] text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-855' }}">Drafts</button>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-1 gap-4 w-full">
                <!-- Search input -->
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search title or content..." 
                       class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] flex-1 dark:text-white">
                
                <!-- Category filter -->
                <select wire:model.live="categoryFilter" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-mono font-semibold">
                    <option value="">All Categories</option>
                    @foreach(Category::getTree() as $cat)
                        <option value="{{ $cat->id }}">{{ str_repeat('── ', $cat->depth) }}{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Bulk Post Upload Import Area -->
        <div class="bg-gray-50 dark:bg-gray-950 p-4 border border-gray-200 dark:border-gray-800 rounded-lg space-y-2">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Bulk Post Import (CSV)</h3>
            <p class="text-[10px] text-gray-500">Upload a CSV file containing columns named `title` and `body` (optionally `subtitle`, `category`, and `status`) to import multiple posts instantly.</p>
            
            @if (session()->has('import_success'))
                <div class="p-2 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                    {{ session('import_success') }}
                </div>
            @endif
            @if (session()->has('import_error'))
                <div class="p-2 bg-red-900/10 border border-red-800 text-red-300 text-xs rounded">
                    {{ session('import_error') }}
                </div>
            @endif

            <div class="flex items-center space-x-2 pt-1">
                <input type="file" wire:model="uploaded_articles_file" class="text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 dark:file:bg-gray-800 dark:file:text-gray-300 cursor-pointer">
                <button type="button" wire:click="importArticles" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-3 py-1.5 rounded transition">Import CSV</button>
            </div>
            <div wire:loading wire:target="uploaded_articles_file" class="text-[10px] text-gray-400">Uploading file...</div>
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
                                @if($article->status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isEditor()))
                                    <button type="button" wire:click="approve({{ $article->id }})" class="text-green-600 font-bold hover:underline">Approve</button>
                                @endif
                                @if($article->status === 'published' && (auth()->user()->isAdmin() || auth()->user()->isEditor()))
                                    <button type="button" wire:click="sendNewsletter({{ $article->id }})" wire:confirm="Are you sure you want to send this article to all newsletter subscribers?" class="text-blue-650 font-bold hover:underline">Send Newsletter</button>
                                @endif
                                <button type="button" wire:click="edit({{ $article->id }})" class="text-[#C8102E] font-bold hover:underline">Edit</button>
                                <button type="button" wire:click="delete({{ $article->id }})" wire:confirm="Are you sure you want to delete this article?" class="text-red-500 font-bold hover:underline">Delete</button>
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
                <!-- AI Assistant Drafting Panel -->
                <div class="bg-gray-50 dark:bg-gray-955 p-4 border border-gray-250 dark:border-gray-850 rounded-lg space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide flex items-center">
                            <span class="mr-1">✨</span> AI Content Engine & Writer
                        </h3>
                        <span class="text-[9px] bg-red-100 text-red-850 px-1.5 py-0.5 rounded font-mono font-bold">BETA</span>
                    </div>

                    @if (session()->has('ai_success'))
                        <div class="p-2 bg-green-900/10 border border-green-800 text-green-300 text-[10px] rounded">
                            {{ session('ai_success') }}
                        </div>
                    @endif
                    @if (session()->has('ai_error'))
                        <div class="p-2 bg-red-900/10 border border-red-800 text-red-300 text-[10px] rounded">
                            {{ session('ai_error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">AI Provider</label>
                            <select wire:model="ai_provider" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs focus:ring-[#C8102E] dark:text-white">
                                <option value="gemini">Google Gemini</option>
                                <option value="openai">OpenAI (ChatGPT)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Writing Tone</label>
                            <select wire:model="ai_tone" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs focus:ring-[#C8102E] dark:text-white">
                                <option value="informative">Informative / News</option>
                                <option value="professional">Professional / Analytical</option>
                                <option value="breaking">Breaking / Urgent</option>
                                <option value="opinion">Opinion Piece</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Topic / Description Prompt</label>
                        <textarea wire:model="ai_prompt" rows="2" placeholder="e.g. Avocado farming boost in Getembe region..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] dark:text-white"></textarea>
                    </div>

                    <div class="flex space-x-2">
                        <button type="button" wire:click="generateArticleContent" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-[10px] px-3 py-1.5 rounded transition flex-1">
                            Draft Full Post
                        </button>
                        <button type="button" wire:click="generateIdeas" class="bg-gray-800 hover:bg-gray-700 text-white font-bold text-[10px] px-3 py-1.5 rounded transition">
                            Suggest Headlines
                        </button>
                    </div>

                    @if(!empty($ai_ideas))
                        <div class="space-y-1 pt-2 border-t border-gray-200 dark:border-gray-800">
                            <label class="text-[9px] font-bold text-gray-400 uppercase">Generated Headline Ideas (Click to use)</label>
                            <div class="space-y-1 font-mono">
                                @foreach($ai_ideas as $idea)
                                    <button type="button" @click="$wire.set('title', '{{ addslashes($idea) }}'); $wire.set('slug', $wire.slugify('{{ addslashes($idea) }}'));" class="w-full text-left p-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded text-[11px] text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-955/20 hover:text-[#C8102E] transition">
                                        💡 {{ $idea }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

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
                <div class="space-y-1"
                     x-data="{ value: @entangle('body') }"
                     x-init="
                         $watch('value', val => {
                             if (val !== $refs.trix.value) {
                                 $refs.trix.editor.loadHTML(val || '');
                             }
                         })
                     "
                     @trix-change="value = $event.target.value"
                     wire:ignore>
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Article Content (Advanced Formatting)</label>
                    <input id="body_input" type="hidden" name="content" x-ref="trix" :value="value">
                    <trix-editor input="body_input" placeholder="Write the full news story body here with rich formatting..." class="trix-content border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded p-2.5 min-h-[350px] text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E]"></trix-editor>
                    @error('body') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Format Specific Builders -->
                <div class="bg-gray-50 dark:bg-gray-955 p-4 border border-gray-250 dark:border-gray-850 rounded-lg space-y-4">
                    <div class="border-b border-gray-200 dark:border-gray-800 pb-2">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                            Post Format Builder: <span class="text-[#C8102E] font-extrabold uppercase">{{ $format }}</span>
                        </h3>
                    </div>

                    @if($format === 'gallery')
                        <!-- Gallery Builder -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Gallery Images</label>
                            <div class="space-y-2">
                                @foreach($format_meta['gallery'] ?? [] as $index => $imageUrl)
                                    <div class="flex items-center space-x-2">
                                        <input type="text" wire:model="format_meta.gallery.{{ $index }}" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white flex-grow">
                                        <button type="button" @click="$dispatch('open-media-modal', {field: 'gallery_image_{{ $index }}'})" class="bg-gray-200 dark:bg-gray-800 text-xs px-2.5 py-1.5 rounded transition">Browse</button>
                                        <button type="button" wire:click="$disable = true; unset(format_meta['gallery'][{{ $index }}]); $wire.set('format_meta.gallery', Object.values(format_meta.gallery))" class="text-red-500 hover:underline font-bold text-xs">&times;</button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="format_meta['gallery'][] = ''" class="bg-gray-800 text-white font-bold text-[10px] px-3 py-1.5 rounded transition">
                                Add Image URL
                            </button>
                        </div>

                    @elseif($format === 'video')
                        <!-- Video Builder -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Embed Video URL (YouTube, Vimeo, etc.)</label>
                            <input type="text" wire:model="format_meta.video_url" placeholder="https://www.youtube.com/embed/..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>

                    @elseif($format === 'audio')
                        <!-- Audio Builder -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Audio Stream or File URL</label>
                            <div class="flex space-x-2">
                                <input type="text" wire:model="format_meta.audio_url" placeholder="https://..." class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white flex-grow font-semibold">
                                <button type="button" @click="$dispatch('open-media-modal', {field: 'audio_url'})" class="bg-gray-200 dark:bg-gray-800 text-xs px-2.5 py-2 rounded transition">Browse</button>
                            </div>
                        </div>

                    @elseif($format === 'list')
                        <!-- Sorted List Builder -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-gray-400 uppercase">List Items</label>
                            <div class="space-y-4">
                                @foreach($format_meta['list'] ?? [] as $index => $item)
                                    <div class="p-3 border border-gray-200 dark:border-gray-800 rounded bg-white dark:bg-gray-900 space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-bold text-gray-500">Item #{{ $index + 1 }}</span>
                                            <button type="button" wire:click="unset(format_meta['list'][{{ $index }}]); $wire.set('format_meta.list', Object.values(format_meta.list))" class="text-red-500 hover:underline text-xs">Remove</button>
                                        </div>
                                        <div class="space-y-1">
                                            <input type="text" wire:model="format_meta.list.{{ $index }}.title" placeholder="Item Title" class="w-full bg-gray-55 dark:bg-gray-950 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white">
                                        </div>
                                        <div class="space-y-1">
                                            <textarea wire:model="format_meta.list.{{ $index }}.desc" placeholder="Item Description" rows="2" class="w-full bg-gray-55 dark:bg-gray-955 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white"></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="format_meta['list'][] = ['title' => '', 'desc' => '']" class="bg-gray-800 text-white font-bold text-[10px] px-3 py-1.5 rounded transition">
                                Add List Item
                            </button>
                        </div>

                    @elseif($format === 'recipe')
                        <!-- Recipe Builder -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-gray-400 uppercase font-bold">Recipe Specifications</label>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase">Prep Time (mins)</label>
                                    <input type="text" wire:model="format_meta.recipe.prep_time" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase">Cook Time (mins)</label>
                                    <input type="text" wire:model="format_meta.recipe.cook_time" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-gray-400 uppercase">Yield / Servings</label>
                                    <input type="text" wire:model="format_meta.recipe.yield" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                    @elseif($format === 'event')
                        <!-- Event Builder -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Event Date / Time</label>
                                <input type="datetime-local" wire:model="format_meta.event.event_date" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Venue / Location</label>
                                <input type="text" wire:model="format_meta.event.venue" placeholder="Kisii Cultural Center" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>

                    @else
                        <p class="text-[10px] text-gray-400">No additional format fields required for this format.</p>
                    @endif
                               <!-- Per-Post FAQ Accordion Builder -->
                <div class="bg-white dark:bg-gray-900 p-4 border border-gray-250 dark:border-gray-850 rounded-lg space-y-4">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Per-Post FAQ Section</h3>
                    
                    <div class="space-y-3">
                        @foreach(($faq_items ?? []) as $index => $faq)
                            <div class="p-3 border border-gray-200 dark:border-gray-800 rounded bg-gray-55 dark:bg-gray-950 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold text-gray-400">FAQ Question #{{ $index + 1 }}</span>
                                    <button type="button" wire:click="removeFaqItem({{ $index }})" class="text-red-500 hover:underline text-xs">&times;</button>
                                </div>
                                <div class="space-y-1">
                                    <input type="text" wire:model="faq_items.{{ $index }}.question" placeholder="Enter Question" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-semibold">
                                </div>
                                <div class="space-y-1">
                                    <textarea wire:model="faq_items.{{ $index }}.answer" placeholder="Enter Answer" rows="2" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white"></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" wire:click="addFaqItem" class="bg-gray-800 hover:bg-gray-700 text-white font-bold text-[10px] px-3 py-1.5 rounded transition">
                        Add FAQ Item
                    </button>
                </div>

                <!-- Rich Media Downloads Builder -->
                <div class="bg-white dark:bg-gray-900 p-4 border border-gray-250 dark:border-gray-850 rounded-lg space-y-4">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Downloadable Files (PDF, ZIP, DOC)</h3>
                    
                    <div class="space-y-3">
                        @foreach(($downloads ?? []) as $index => $dl)
                            <div class="flex items-center space-x-2">
                                <input type="text" wire:model="downloads.{{ $index }}.label" placeholder="Button Label (e.g. Download PDF)" class="bg-gray-55 dark:bg-gray-950 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white w-1/3">
                                <input type="text" wire:model="downloads.{{ $index }}.url" placeholder="File URL" class="bg-gray-55 dark:bg-gray-955 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white flex-grow">
                                <button type="button" @click="$dispatch('open-media-modal', {field: 'download_url_{{ $index }}'})" class="bg-gray-200 dark:bg-gray-800 text-xs px-2.5 py-1.5 rounded transition">Browse</button>
                                <button type="button" wire:click="removeDownload({{ $index }})" class="text-red-500 hover:underline font-bold text-xs">&times;</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" wire:click="addDownload" class="bg-gray-800 hover:bg-gray-700 text-white font-bold text-[10px] px-3 py-1.5 rounded transition">
                        Add Download Button
                    </button>
                </div>

                <!-- Video & Audio Embeds -->
                <div class="bg-white dark:bg-gray-900 p-4 border border-gray-250 dark:border-gray-855 rounded-lg space-y-4">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide flex items-center">
                        🎥 Video & Audio Embeds (Optional)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-350 uppercase">Video Embed URL (YouTube, Vimeo, etc.)</label>
                            <input type="text" wire:model="format_meta.video_url" placeholder="https://www.youtube.com/embed/..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            <p class="text-[9px] text-gray-400">Specify an embed URL to display a video player inside the article details view.</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-350 uppercase">Audio Podcast or Stream URL</label>
                            <div class="flex space-x-2">
                                <input type="text" wire:model="format_meta.audio_url" placeholder="https://..." class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white flex-grow font-semibold">
                                <button type="button" @click="$dispatch('open-media-modal', {field: 'audio_url'})" class="bg-gray-200 dark:bg-gray-800 text-xs px-2.5 py-2 rounded transition">Browse</button>
                            </div>
                            <p class="text-[9px] text-gray-400 font-semibold">Specify a URL (e.g. MP3 file or sound stream) to display an audio player.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-6 self-start">
                        <!-- Post Format Selection -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Post Format</label>
                    <select wire:model.live="format" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold">
                        <option value="article">📰 Standard Article</option>
                        <option value="gallery">🖼️ Gallery Album</option>
                        <option value="list">🔢 Sorted List</option>
                        <option value="toc">📋 Table of Contents</option>
                        <option value="video">🎥 Video Embed</option>
                        <option value="audio">🔊 Audio Podcast</option>
                        <option value="recipe">🍳 Food Recipe</option>
                        <option value="event">📅 Local Event</option>
                    </select>
                    @error('format') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Category Select -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Primary Category</label>
                    <select wire:model="category_id" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold font-mono">
                        @foreach(Category::getTree() as $cat)
                            <option value="{{ $cat->id }}">{{ str_repeat('── ', $cat->depth) }}{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Additional Categories Selection -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Additional Categories</label>
                    <div class="grid grid-cols-1 gap-2 max-h-40 overflow-y-auto border border-gray-355 dark:border-gray-700 rounded p-2.5 bg-gray-50 dark:bg-gray-955/20 font-mono">
                        @foreach(Category::getTree() as $cat)
                            @if($cat->id != $category_id)
                                <label class="flex items-center text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <input type="checkbox" wire:model="selectedCategories" value="{{ $cat->id }}" class="rounded text-[#C8102E] focus:ring-[#C8102E] mr-2">
                                    <span class="text-gray-400 select-none mr-1">{{ str_repeat('── ', $cat->depth) }}</span>
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
                    <select wire:model.live="status" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold font-mono">
                        <option value="draft">Draft</option>
                        <option value="pending">Pending Approval</option>
                        @if(auth()->user()->isAdmin() || auth()->user()->isEditor())
                            <option value="scheduled">Scheduled Publish</option>
                            <option value="published">Published</option>
                        @endif
                    </select>
                    @error('status') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                @if($status === 'scheduled')
                    <!-- Scheduled Date/Time picker -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Scheduled Date / Time</label>
                        <input type="datetime-local" wire:model="published_at" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white font-semibold">
                        @error('published_at') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                    </div>
                @endif

                <!-- Image URL -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Featured Image URL</label>
                    <div class="flex space-x-2">
                        <input type="url" wire:model="featured_image" placeholder="https://unsplash.com/...jpg"
                               class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white flex-grow font-semibold">
                        <button type="button" @click="$dispatch('open-media-modal', {field: 'featured_image'})" class="bg-gray-200 dark:bg-gray-800 text-xs px-2.5 py-2 rounded transition">Browse</button>
                    </div>
                    @error('featured_image') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>           </div>

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

                <!-- Keywords Tags -->
                <div class="space-y-1 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Tags / Keywords (Comma separated)</label>
                    <input type="text" wire:model="tags_input" placeholder="politics, kisii, county"
                           class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                    @error('tags_input') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
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

    <livewire:media-select-modal />
</div>

<?php

use Illuminate\Support\Facades\Route;

use App\Models\Article;
use App\Models\Video;
use App\Models\Advertisement;
use App\Models\Category;

Route::get('/', function () {
    $now = now();
    
    $baseQuery = Article::where('status', 'published')
        ->whereNotNull('published_at')
        ->where('published_at', '<=', $now)
        ->orderBy('published_at', 'desc');

    $featuredArticle = (clone $baseQuery)->where('is_featured', true)->first() ?? (clone $baseQuery)->first();
    $featuredId = $featuredArticle ? $featuredArticle->id : 0;

    $topStories = (clone $baseQuery)
        ->where('id', '!=', $featuredId)
        ->take(4)
        ->get();

    $latestArticles = (clone $baseQuery)
        ->take(6)
        ->get();

    $trendingArticles = (clone $baseQuery)
        ->orderBy('views_count', 'desc')
        ->take(5)
        ->get();

    // Specific category feeds
    $politicsArticles = (clone $baseQuery)
        ->forCategory('politics')
        ->take(4)
        ->get();

    $businessArticles = (clone $baseQuery)
        ->forCategory('business')
        ->take(4)
        ->get();

    $techArticles = (clone $baseQuery)
        ->forCategory('technology')
        ->take(4)
        ->get();

    $sportsArticles = (clone $baseQuery)
        ->forCategory('sports')
        ->take(4)
        ->get();

    $opinionArticles = (clone $baseQuery)
        ->forCategory('opinion')
        ->take(4)
        ->get();

    $featuredVideo = Video::published()->where('is_featured', true)->first() ?? Video::published()->first();
    $latestVideos = Video::published()
        ->where('id', '!=', $featuredVideo ? $featuredVideo->id : 0)
        ->take(3)
        ->get();

    $topAd = Advertisement::active()->location('top')->first();
    $sidebarAd = Advertisement::active()->location('sidebar')->first();
    $layout = \App\Models\Setting::get('theme_layout', 'standard');

    // Dynamic Category Blocks for Section 5 (Managed by Admin / Synced with Header Menu)
    $headerMenu = \App\Models\Setting::get('header_menu', []);
    $selectedCategorySlugs = [];

    $extractSlugs = function ($items) use (&$extractSlugs, &$selectedCategorySlugs) {
        foreach ($items as $item) {
            $url = $item['url'] ?? '';
            if (str_starts_with($url, '/')) {
                $slug = ltrim($url, '/');
                if ($slug && !in_array($slug, ['live-tv', 'tv', 'live-radio', 'gallery', 'contact', 'about', 'privacy', 'terms'])) {
                    $selectedCategorySlugs[] = $slug;
                }
            }
            if (!empty($item['children'])) {
                $extractSlugs($item['children']);
            }
        }
    };

    if (is_array($headerMenu) && !empty($headerMenu)) {
        $extractSlugs($headerMenu);
    }

    if (empty($selectedCategorySlugs)) {
        $homepageCategoriesSlugsString = \App\Models\Setting::get('homepage_categories', 'politics,business,technology,sports');
        $selectedCategorySlugs = array_filter(array_map('trim', explode(',', $homepageCategoriesSlugsString)));
    }

    $selectedCategorySlugs = array_unique($selectedCategorySlugs);

    $categoryBlocks = [];
    if (!empty($selectedCategorySlugs)) {
        $homepageCategories = Category::whereIn('slug', $selectedCategorySlugs)
            ->get()
            ->sortBy(function ($cat) use ($selectedCategorySlugs) {
                return array_search($cat->slug, $selectedCategorySlugs);
            });

        foreach ($homepageCategories as $cat) {
            $categoryBlocks[] = [
                'category' => $cat,
                'articles' => (clone $baseQuery)
                    ->forCategory($cat->id)
                    ->take(8)
                    ->get()
            ];
        }
    }

    $homepageData = [
        'featuredArticle' => $featuredArticle,
        'topStories' => $topStories,
        'latestArticles' => $latestArticles,
        'trendingArticles' => $trendingArticles,
        'politicsArticles' => $politicsArticles,
        'businessArticles' => $businessArticles,
        'techArticles' => $techArticles,
        'sportsArticles' => $sportsArticles,
        'opinionArticles' => $opinionArticles,
        'featuredVideo' => $featuredVideo,
        'latestVideos' => $latestVideos,
        'topAd' => $topAd,
        'sidebarAd' => $sidebarAd,
        'layout' => $layout,
        'categoryBlocks' => $categoryBlocks,
    ];

    return view('welcome', $homepageData);
});

use App\Http\Controllers\ArticleController;

Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/articles/{id}/view', [ArticleController::class, 'trackView'])->name('articles.track-view');
Route::get('/search', [ArticleController::class, 'search'])->name('search');

Route::get('/tv', function () {
    if (\App\Models\Setting::get('live_tv_active', '1') != '1') {
        return redirect('/')->with('error', 'Live TV page is currently disabled.');
    }
    $tvUrl = \App\Models\Setting::get('live_tv_url', 'https://www.youtube.com/embed/5Peo-ivmupE');
    return view('live-tv', compact('tvUrl'));
})->name('live-tv');

Route::redirect('/live-tv', '/tv');

Route::get('/live-radio', function () {
    if (\App\Models\Setting::get('live_radio_active', '1') != '1') {
        return redirect('/')->with('error', 'Live Radio page is currently disabled.');
    }
    $radioUrl = \App\Models\Setting::get('live_radio_url', 'http://stream.zeno.fm/f5r7x1t1zv8uv');
    return view('live-radio', compact('radioUrl'));
})->name('live-radio');

Route::get('dashboard', function () {
    if (auth()->user()->isStaff()) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/announcements', \App\Livewire\AnnouncementSubmit::class)->name('announcements');
Route::get('/agent/dashboard', \App\Livewire\AgentDashboard::class)
    ->middleware(\App\Http\Middleware\RedirectIfAgentNotLoggedIn::class)
    ->name('agent.dashboard');

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/articles', 'admin.articles')->middleware('can:article management')->name('admin.articles');
    Route::view('/categories', 'admin.categories')->middleware('can:category management')->name('admin.categories');
    Route::view('/menus', 'admin.menus')->middleware('can:page management')->name('admin.menus');
    Route::view('/comments', 'admin.comments')->middleware('can:comment management')->name('admin.comments');
    Route::view('/users', 'admin.users')->middleware('can:user management')->name('admin.users');
    Route::view('/messages', 'admin.messages')->middleware('can:contact message management')->name('admin.messages');
    Route::get('/announcements', \App\Livewire\AdminAnnouncements::class)->middleware('can:announcement management')->name('admin.announcements');
    Route::get('/agents', \App\Livewire\AdminAgents::class)->middleware('can:announcement management')->name('admin.agents');
    Route::view('/advertisements', 'admin.advertisements')->middleware('can:settings management')->name('admin.advertisements');
    Route::view('/media', 'admin.media')->middleware('can:settings management')->name('admin.media');
    Route::get('/settings/{tab?}', function ($tab = 'identity') {
        return view('admin.settings', compact('tab'));
    })->name('admin.settings');
});

use App\Http\Controllers\SeoSitemapController;

Route::get('/sitemap.xml', [SeoSitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/news-sitemap.xml', [SeoSitemapController::class, 'newsSitemap'])->name('sitemap.news');
Route::get('/feed/google-news', [SeoSitemapController::class, 'googleNewsFeed'])->name('feed.google-news');

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

require __DIR__.'/auth.php';

Route::get('/gallery', function () {
    $articles = \App\Models\Article::published()->where('format', 'gallery')->get();
    return view('gallery', compact('articles'));
})->name('gallery.show');

Route::get('/tag/{slug}', function ($slug) {
    $tag = \App\Models\Tag::where('slug', $slug)->firstOrFail();
    $articles = $tag->articles()->published()->paginate(10);
    return view('tag-archive', compact('tag', 'articles'));
})->name('tag.archive');

Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Migrations completed successfully! Your database schema has been updated. You can now access your homepage.';
    } catch (\Exception $e) {
        return 'Error during migrations: ' . $e->getMessage();
    }
});

Route::get('/run-seeders', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return 'Database seeders completed successfully! Default admin accounts, roles, categories, and settings have been generated.';
    } catch (\Exception $e) {
        return 'Error during seeding: ' . $e->getMessage();
    }
});

Route::get('/debug-log', function () {
    $path = storage_path('logs/laravel.log');
    if (!file_exists($path)) {
        return 'Log file does not exist at ' . $path;
    }
    $content = file_get_contents($path);
    $lines = explode("\n", $content);
    $lastLines = array_slice($lines, -150);
    return '<pre style="background: #111; color: #eee; padding: 20px; font-family: monospace; overflow: auto; max-height: 90vh;">' . htmlspecialchars(implode("\n", $lastLines)) . '</pre>';
});

Route::get('/{slug}', [ArticleController::class, 'category'])->name('category.show');

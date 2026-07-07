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
        ->take(3)
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

    $featuredVideo = Video::published()->where('is_featured', true)->first() ?? Video::published()->first();
    $latestVideos = Video::published()
        ->where('id', '!=', $featuredVideo ? $featuredVideo->id : 0)
        ->take(3)
        ->get();

    $topAd = Advertisement::active()->location('top')->first();
    $sidebarAd = Advertisement::active()->location('sidebar')->first();
    $layout = \App\Models\Setting::get('theme_layout', 'standard');

    // Dynamic Category Blocks for Section 5 (Managed by Admin)
    $homepageCategoriesSlugsString = \App\Models\Setting::get('homepage_categories', 'politics,business,technology,sports');
    $selectedCategorySlugs = array_filter(array_map('trim', explode(',', $homepageCategoriesSlugsString)));
    
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
                    ->take(5)
                    ->get()
            ];
        }
    }

    return view('welcome', compact(
        'featuredArticle',
        'topStories',
        'latestArticles',
        'trendingArticles',
        'politicsArticles',
        'businessArticles',
        'techArticles',
        'sportsArticles',
        'featuredVideo',
        'latestVideos',
        'topAd',
        'sidebarAd',
        'layout',
        'categoryBlocks'
    ));
});

use App\Http\Controllers\ArticleController;

Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/search', [ArticleController::class, 'search'])->name('search');

Route::get('/live-tv', function () {
    $tvUrl = \App\Models\Setting::get('live_tv_url', 'https://www.youtube.com/embed/5Peo-ivmupE');
    return view('live-tv', compact('tvUrl'));
})->name('live-tv');

Route::get('/live-radio', function () {
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

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/articles', 'admin.articles')->middleware('can:article management')->name('admin.articles');
    Route::view('/categories', 'admin.categories')->middleware('can:category management')->name('admin.categories');
    Route::view('/menus', 'admin.menus')->middleware('can:page management')->name('admin.menus');
    Route::view('/comments', 'admin.comments')->middleware('can:comment management')->name('admin.comments');
    Route::view('/users', 'admin.users')->middleware('can:user management')->name('admin.users');
    Route::view('/messages', 'admin.messages')->middleware('can:contact message management')->name('admin.messages');
    Route::get('/settings/{tab?}', function ($tab = 'identity') {
        return view('admin.settings', compact('tab'));
    })->name('admin.settings');
});

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/privacy', 'privacy')->name('privacy');

require __DIR__.'/auth.php';

Route::get('/{slug}', [ArticleController::class, 'category'])->name('category.show');

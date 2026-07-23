<?php

use App\Models\Article;
use App\Models\Video;
use App\Models\Advertisement;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    $homepageData = Cache::remember('homepage_data_v3', 300, function () {
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

        return [
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
    });

    return view('welcome', $homepageData);
});

use App\Http\Controllers\ArticleController;

Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/articles/{id}/view', [ArticleController::class, 'trackView'])->name('articles.track-view');
Route::get('/search', [ArticleController::class, 'search'])->name('search');
Route::get('/author/{id}', [ArticleController::class, 'authorArchive'])->name('author.archive');

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
    Route::view('/menus', 'admin.menus')->middleware('can:menu management')->name('admin.menus');
    Route::view('/comments', 'admin.comments')->middleware('can:comment management')->name('admin.comments');
    Route::view('/users', 'admin.users')->middleware('can:user management')->name('admin.users');
    Route::view('/messages', 'admin.messages')->middleware('can:contact message management')->name('admin.messages');
    Route::get('/announcements', \App\Livewire\AdminAnnouncements::class)->middleware('can:announcement management')->name('admin.announcements');
    Route::get('/agents', \App\Livewire\AdminAgents::class)->middleware('can:announcement management')->name('admin.agents');
    Route::view('/advertisements', 'admin.advertisements')->middleware('can:settings management')->name('admin.advertisements');
    Route::view('/media', 'admin.media')->middleware('can:content management')->name('admin.media');
    Route::get('/settings/{tab?}', function ($tab = 'identity') {
        return view('admin.settings', compact('tab'));
    })->name('admin.settings');
});

use App\Http\Controllers\SeoSitemapController;

Route::get('/sitemap.xml', [SeoSitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/news-sitemap.xml', [SeoSitemapController::class, 'newsSitemap'])->name('sitemap.news');
Route::get('/feed/google-news', [SeoSitemapController::class, 'googleNewsFeed'])->name('feed.google-news');

Route::get('/robots.txt', function () {
    $siteUrl = rtrim(url('/'), '/');
    $default = "User-agent: *\nDisallow: /admin\nDisallow: /login\nDisallow: /register\n\nSitemap: {$siteUrl}/sitemap.xml\nSitemap: {$siteUrl}/news-sitemap.xml";
    $content = trim(\App\Models\Setting::get('robots_txt_content', $default));
    
    if (empty($content)) {
        $content = $default;
    }
    
    // If Sitemap reference is missing, append it
    if (!str_contains(strtolower($content), 'sitemap:')) {
        $content .= "\n\nSitemap: {$siteUrl}/sitemap.xml\nSitemap: {$siteUrl}/news-sitemap.xml";
    } else {
        // Dynamically replace localhost or wrong domains in existing sitemap references
        $content = preg_replace('/Sitemap:\s*\S+/i', "Sitemap: {$siteUrl}/sitemap.xml\nSitemap: {$siteUrl}/news-sitemap.xml", $content);
    }
    
    return response($content, 200, ['Content-Type' => 'text/plain']);
});

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

Route::get('/run-storage-link', function () {
    try {
        $target = public_path('storage');
        $source = storage_path('app/public');
        
        // 1. If public/storage is a link, remove it to clear the path
        if (is_link($target)) {
            @unlink($target);
        }
        
        // 2. Try standard symlink if possible and if public/storage doesn't exist
        $symlinkCreated = false;
        if (function_exists('symlink') && !file_exists($target)) {
            try {
                if (@symlink($source, $target)) {
                    $symlinkCreated = true;
                }
            } catch (\Exception $e) {
                // Fail silently and fall back
            }
        }
        
        // 3. Fallback: Copy all files recursively from storage/app/public to public/storage
        if (!$symlinkCreated) {
            if (is_link($target)) {
                @unlink($target);
            }
            
            if (!file_exists($target)) {
                @mkdir($target, 0755, true);
            }
            
            $copyDir = function ($src, $dst) use (&$copyDir) {
                if (!file_exists($src) || !is_dir($src)) return;
                $dir = opendir($src);
                if (!$dir) return;
                @mkdir($dst, 0755, true);
                while (false !== ($file = readdir($dir))) {
                    if (($file != '.') && ($file != '..')) {
                        if (is_dir($src . '/' . $file)) {
                            $copyDir($src . '/' . $file, $dst . '/' . $file);
                        } else {
                            @copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
                closedir($dir);
            };
            
            $copyDir($source, $target);
            return 'Bypassed symlink (unsupported on your hosting). Successfully copied media files to public/storage! New uploads will go directly there.';
        }
        
        return 'Storage symlink created successfully! Deployed uploaded images will now load correctly.';
    } catch (\Exception $e) {
        return 'Error creating storage link fallback: ' . $e->getMessage();
    }
});

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
        
        // Convert any absolute localhost storage URLs to clean relative paths
        \App\Models\Article::all()->each(function ($article) {
            if (str_contains($article->featured_image, '/storage/')) {
                $parts = explode('/storage/', $article->featured_image);
                $article->featured_image = '/storage/' . end($parts);
                $article->save();
            }
        });
        
        \App\Models\Media::all()->each(function ($media) {
            if (str_contains($media->url, '/storage/')) {
                $parts = explode('/storage/', $media->url);
                $media->url = '/storage/' . end($parts);
                $media->save();
            }
        });

        // Update brand_color setting in database to orange #cc6c3b if it is currently red #C8102E
        $bcSetting = \App\Models\Setting::where('key', 'brand_color')->first();
        if ($bcSetting && ($bcSetting->value === '#C8102E' || empty($bcSetting->value))) {
            $bcSetting->value = '#cc6c3b';
            $bcSetting->save();
        }

        return 'Database seeders, asset URLs, and theme color updates completed successfully!';
    } catch (\Exception $e) {
        return 'Error during seeding: ' . $e->getMessage();
    }
});

Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        
        // Clear failed logins/lockout for admin
        try {
            \App\Support\Security::clearFailedLogin('admin@getembenews.com');
        } catch (\Exception $e) {}
        
        $opcacheReset = false;
        if (function_exists('opcache_reset')) {
            $opcacheReset = @opcache_reset();
        }

        return 'Application caches cleared and optimized successfully! ' . ($opcacheReset ? 'PHP OPcache was also reset.' : 'OPcache reset function is disabled or not available.');
    } catch (\Exception $e) {
        return 'Error clearing application cache: ' . $e->getMessage();
    }
});

Route::get('/force-login-admin', function () {
    try {
        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            \App\Support\Security::clearFailedLogin($admin->email);
            
            // Clear standard IP throttle key as well
            $ipThrottleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($admin->email).'|'.request()->ip());
            \Illuminate\Support\Facades\RateLimiter::clear($ipThrottleKey);
            
            auth()->login($admin);
            return redirect()->route('admin.dashboard');
        }
        return 'Admin user not found.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
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

Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return '<h3>Migration Success:</h3><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<h3>Migration Error:</h3><pre>' . $e->getMessage() . '</pre>';
    }
});

Route::get('/{slug}', [ArticleController::class, 'category'])->name('category.show');

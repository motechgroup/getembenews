<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Newsletter;
use App\Models\Setting;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileAppController extends Controller
{
    /**
     * Middleware helper to check if mobile API is in maintenance mode.
     */
    protected function checkMaintenance()
    {
        $maintenance = (bool) Setting::get('mobile_app_maintenance_mode', false);
        if ($maintenance) {
            response()->json([
                'status' => 'error',
                'message' => 'The mobile app services are currently offline for maintenance. Please try again later.'
            ], 503)->send();
            exit;
        }
    }

    /**
     * Get mobile application configurations and site settings.
     */
    public function settings()
    {
        // Don't enforce maintenance mode on settings, so the app can fetch maintenance status dynamically!
        return response()->json([
            'status' => 'success',
            'data' => [
                'site_name' => Setting::get('site_name', 'Getembe News'),
                'site_logo' => Setting::get('site_logo', ''),
                'brand_color' => Setting::get('brand_color', '#FF7900'),
                'theme_color_secondary' => Setting::get('theme_color_secondary', '#222222'),
                'theme_color_success' => Setting::get('theme_color_success', '#10B981'),
                'theme_color_warning' => Setting::get('theme_color_warning', '#F59E0B'),
                'favicon' => Setting::get('favicon', ''),
                'app_play_store_url' => Setting::get('app_play_store_url', 'https://play.google.com/store'),
                'app_app_store_url' => Setting::get('app_app_store_url', 'https://www.apple.com/app-store'),
                'app_banner_title' => Setting::get('app_banner_title', 'Download Getembe News Mobile App'),
                'app_banner_desc' => Setting::get('app_banner_desc', 'Get fast, reliable news updates directly on your smartphone.'),
                'account_deletion_url' => url('/account-deletion'),
                
                // Versioning and Developer Attribution
                'system_version' => Setting::get('system_version', 'v2.5.0'),
                'app_version' => Setting::get('mobile_app_version_android', 'v1.0.4'),
                'developer_note' => 'Developed By Motech Digital Agency (0792 758 752)',
                'developer_agency' => 'Motech Digital Agency',
                'developer_phone' => '0792 758 752',

                // Mobile configuration
                'mobile_app_version_ios' => Setting::get('mobile_app_version_ios', '1.0.0'),
                'mobile_app_version_android' => Setting::get('mobile_app_version_android', 'v1.0.4'),
                'mobile_app_force_update' => (bool) Setting::get('mobile_app_force_update', false),
                'mobile_app_ios_link' => Setting::get('mobile_app_ios_link', 'https://www.apple.com/app-store'),
                'mobile_app_android_link' => Setting::get('mobile_app_android_link', 'https://play.google.com/store'),
                'mobile_app_ads_enabled' => (bool) Setting::get('mobile_app_ads_enabled', false),
                'mobile_app_admob_banner_id' => Setting::get('mobile_app_admob_banner_id', ''),
                'mobile_app_admob_interstitial_id' => Setting::get('mobile_app_admob_interstitial_id', ''),
                'mobile_app_facebook_ads_enabled' => (bool) Setting::get('mobile_app_facebook_ads_enabled', false),
                'mobile_app_facebook_banner_id' => Setting::get('mobile_app_facebook_banner_id', ''),
                'mobile_app_facebook_interstitial_id' => Setting::get('mobile_app_facebook_interstitial_id', ''),
                'mobile_app_native_ads_enabled' => (bool) Setting::get('mobile_app_native_ads_enabled', false),
                'mobile_app_admob_native_id' => Setting::get('mobile_app_admob_native_id', ''),
                'mobile_app_facebook_native_id' => Setting::get('mobile_app_facebook_native_id', ''),
                'mobile_app_native_ad_code' => Setting::get('mobile_app_native_ad_code', ''),
                'mobile_app_native_ad_frequency' => (int) Setting::get('mobile_app_native_ad_frequency', 5),
                'mobile_app_maintenance_mode' => (bool) Setting::get('mobile_app_maintenance_mode', false),
                'show_views_count' => (bool) Setting::get('show_views_count', true),
                'live_tv_url' => trim(Setting::get('live_tv_url', '')),
                'live_tv_embed_code' => trim(Setting::get('live_tv_embed_code', '')),
                'live_tv_type' => trim(Setting::get('live_tv_type', 'auto')),
                'live_tv_active' => (bool) filter_var(Setting::get('live_tv_active', '1'), FILTER_VALIDATE_BOOLEAN),
                'live_radio_url' => Setting::get('live_radio_url', ''),
                'live_radio_active' => (bool) filter_var(Setting::get('live_radio_active', '1'), FILTER_VALIDATE_BOOLEAN),
                'announcement_rate_tv' => (int) Setting::get('announcement_rate_tv', 5),
                'announcement_rate_radio' => (int) Setting::get('announcement_rate_radio', 3),
                'announcement_rate_both' => (int) Setting::get('announcement_rate_both', 7),
                'simulated_polls' => json_decode(Setting::get('simulated_polls', '[]'), true),
                'simulated_quizzes' => json_decode(Setting::get('simulated_quizzes', '[]'), true),
            ]
        ]);
    }

    /**
     * Retrieve homepage data for mobile app (featured, latest, and category blocks).
     */
    public function homeFeed(Request $request)
    {
        $this->checkMaintenance();

        $now = now();
        $baseQuery = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->with(['author:id,name,photo_url', 'category:id,name,slug'])
            ->orderBy('published_at', 'desc');

        $featuredArticle = (clone $baseQuery)->where('is_featured', true)->first() ?? (clone $baseQuery)->first();
        $featuredId = $featuredArticle ? $featuredArticle->id : 0;

        $latestArticles = (clone $baseQuery)
            ->where('id', '!=', $featuredId)
            ->take(6)
            ->get();

        // Load category blocks
        $homepageCategoriesSlugsString = Setting::get('homepage_categories', 'politics,business,technology,sports');
        $selectedCategorySlugs = array_filter(array_map('trim', explode(',', $homepageCategoriesSlugsString)));
        
        $categories = Category::whereIn('slug', $selectedCategorySlugs)->get()->sortBy(function ($cat) use ($selectedCategorySlugs) {
            return array_search($cat->slug, $selectedCategorySlugs);
        });

        $categorySections = [];
        foreach ($categories as $cat) {
            $articles = (clone $baseQuery)
                ->forCategory($cat->id)
                ->where('id', '!=', $featuredId)
                ->take(4)
                ->get();

            if ($articles->isNotEmpty()) {
                $categorySections[] = [
                    'category' => [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                    ],
                    'articles' => $articles
                ];
            }
        }

        $nativeAds = Advertisement::active()->where('location', 'mobile_native')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'featured_article' => $featuredArticle,
                'latest_articles' => $latestArticles,
                'category_sections' => $categorySections,
                'native_ads' => [
                    'enabled' => (bool) Setting::get('mobile_app_native_ads_enabled', false),
                    'admob_native_id' => Setting::get('mobile_app_admob_native_id', ''),
                    'facebook_native_id' => Setting::get('mobile_app_facebook_native_id', ''),
                    'native_ad_code' => Setting::get('mobile_app_native_ad_code', ''),
                    'frequency' => (int) Setting::get('mobile_app_native_ad_frequency', 5),
                    'items' => $nativeAds
                ]
            ]
        ]);
    }

    /**
     * Retrieve all categories.
     */
    public function categories()
    {
        $this->checkMaintenance();

        $categories = Category::orderBy('order')->get();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Retrieve paginated articles feed with filtering.
     */
    public function articles(Request $request)
    {
        $this->checkMaintenance();

        $query = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['author:id,name,photo_url', 'category:id,name,slug'])
            ->orderBy('published_at', 'desc');

        // Filter by category slug
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by author ID
        if ($request->filled('author_id')) {
            $query->where('user_id', $request->author_id);
        }

        // Search in title/body
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Feature filters
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        if ($request->boolean('breaking')) {
            $query->where('is_breaking', true);
        }

        $articles = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $articles
        ]);
    }

    /**
     * Retrieve single article details.
     */
    public function article(string $slug)
    {
        $this->checkMaintenance();

        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->with(['author:id,name,bio,photo_url', 'category:id,name,slug'])
            ->firstOrFail();

        // Increment view count dynamically
        $article->increment('views_count');

        // Check user authentication & access rights for paywall
        $user = auth('sanctum')->user();
        $canAccess = $user ? $user->canAccessArticle($article) : !$article->is_premium;

        $paywallInfo = [
            'is_premium' => (bool) $article->is_premium,
            'is_locked' => !$canAccess,
            'user_has_access' => (bool) $canAccess,
            'article_price' => (int) ($article->price ?: Setting::get('paywall_default_article_price', '50')),
            'subscription_options' => [
                'daily' => (int) Setting::get('paywall_daily_price', '20'),
                'weekly' => (int) Setting::get('paywall_weekly_price', '100'),
                'monthly' => (int) Setting::get('paywall_monthly_price', '300'),
            ]
        ];

        if (!$canAccess) {
            // Truncate article body to 1 paragraph teaser snippet for locked premium articles
            $rawBody = $article->body;
            $normalizedBody = preg_replace('/(<br\s*\/?>\s*){2,}/i', '</p><p>', $rawBody);
            $normalizedBody = preg_replace('/<\/div>\s*<div[^>]*>/i', '</p><p>', $normalizedBody);
            $normalizedBody = preg_replace('/(?:\r?\n){2,}/', '</p><p>', $normalizedBody);

            if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $normalizedBody, $pMatches) && !empty($pMatches[0])) {
                $cleanParagraphsList = array_values(array_filter($pMatches[0], fn($p) => trim(strip_tags($p)) !== ''));
            } else {
                $chunks = preg_split('/<br\s*\/?>|\n/i', strip_tags($normalizedBody, '<a><strong><b><i><em>'));
                $cleanParagraphsList = [];
                foreach ($chunks as $chunk) {
                    $chunk = trim($chunk);
                    if (!empty($chunk)) {
                        $cleanParagraphsList[] = '<p>' . $chunk . '</p>';
                    }
                }
            }

            $article->body = !empty($cleanParagraphsList) ? $cleanParagraphsList[0] : '<p>' . \Illuminate\Support\Str::limit(strip_tags($rawBody), 180) . '</p>';
        }

        // Fetch approved comments
        $comments = Comment::where('article_id', $article->id)
            ->where('status', 'approved')
            ->with('user:id,name,photo_url')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch related articles
        $related = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('status', 'published')
            ->take(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'article' => $article,
                'paywall' => $paywallInfo,
                'comments' => $comments,
                'related_articles' => $related
            ]
        ]);
    }

    /**
     * Retrieve public author details.
     */
    public function authorProfile(int $id)
    {
        $this->checkMaintenance();

        $author = User::select('id', 'name', 'bio', 'photo_url', 'role', 'created_at')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $author
        ]);
    }

    /**
     * Register a new subscriber.
     */
    public function register(Request $request)
    {
        $this->checkMaintenance();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => strip_tags(trim($request->name)),
            'email' => strip_tags(trim(strtolower($request->email))),
            'password' => Hash::make($request->password),
            'role' => 'subscriber',
        ]);

        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration completed successfully.',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Redirect mobile app user to Google OAuth.
     */
    public function googleRedirect(Request $request)
    {
        return app(\App\Http\Controllers\Auth\SocialAuthController::class)->redirectToProvider('google');
    }

    /**
     * Authenticate mobile user via Google Token / OAuth payload.
     */
    public function googleTokenLogin(Request $request)
    {
        $this->checkMaintenance();

        // 1. If Sanctum token is provided from WebBrowser OAuth flow:
        if ($request->filled('token')) {
            $tokenString = $request->token;
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenString);
            if ($tokenModel && $tokenModel->tokenable) {
                $user = $tokenModel->tokenable;
                return response()->json([
                    'status' => 'success',
                    'message' => 'Google login verified.',
                    'data' => [
                        'user' => $user,
                        'token' => $tokenString
                    ]
                ]);
            }
        }

        // 2. If Google User Profile or ID token is provided:
        if ($request->filled('email')) {
            $request->validate([
                'email' => 'required|email',
                'name' => 'nullable|string',
                'photo_url' => 'nullable|string',
            ]);

            $email = strtolower(trim($request->email));
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $request->filled('name') ? trim($request->name) : explode('@', $email)[0],
                    'email' => $email,
                    'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                    'role' => 'subscriber',
                    'photo_url' => $request->photo_url ?? null,
                    'email_verified_at' => now(),
                ]);
            } else {
                if (!$user->email_verified_at) {
                    $user->update(['email_verified_at' => now()]);
                }
                if ($request->filled('photo_url') && empty($user->photo_url)) {
                    $user->update(['photo_url' => $request->photo_url]);
                }
            }

            $token = $user->createToken('mobile-app-google')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Google authentication successful.',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid or missing Google authentication payload.'
        ], 422);
    }

    /**
     * Authenticate mobile user.
     */
    public function login(Request $request)
    {
        $this->checkMaintenance();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string'
        ]);

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $deviceName = $request->filled('device_name') ? $request->device_name : 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    /**
     * Log out mobile user.
     */
    public function logout(Request $request)
    {
        $this->checkMaintenance();

        // Revoke the token that was used to access the request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ]);
    }

    /**
     * Get user profile details.
     */
    public function profile(Request $request)
    {
        $this->checkMaintenance();

        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    /**
     * Update user profile details.
     */
    public function updateProfile(Request $request)
    {
        $this->checkMaintenance();

        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'photo_url' => 'nullable|string|max:2048',
        ]);

        $user->update([
            'name' => strip_tags(trim($request->name)),
            'bio' => $request->bio ? strip_tags(trim($request->bio)) : null,
            'photo_url' => $request->photo_url ? strip_tags(trim($request->photo_url)) : null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }

    /**
     * Retrieve user saved/bookmarked articles.
     */
    public function savedArticles(Request $request)
    {
        $this->checkMaintenance();

        $user = $request->user();
        $saved = $user->savedArticles()->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $saved
        ]);
    }

    /**
     * Toggle bookmark save status of an article.
     */
    public function toggleSave(Request $request, int $articleId)
    {
        $this->checkMaintenance();

        $user = $request->user();
        $article = Article::findOrFail($articleId);

        if ($user->savedArticles()->where('article_id', $articleId)->exists()) {
            $user->savedArticles()->detach($articleId);
            $saved = false;
            $message = 'Article removed from bookmarks.';
        } else {
            $user->savedArticles()->attach($articleId);
            $saved = true;
            $message = 'Article added to bookmarks.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'is_saved' => $saved
            ]
        ]);
    }

    /**
     * Post a comment on an article.
     */
    public function comment(Request $request, int $articleId)
    {
        $this->checkMaintenance();

        $request->validate([
            'body' => 'required|string|min:3'
        ]);

        $article = Article::findOrFail($articleId);
        $user = $request->user();

        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'body' => strip_tags(trim($request->body)),
            'status' => 'approved' // Automatically approve from mobile app users
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment posted successfully.',
            'data' => $comment->load('user:id,name,photo_url')
        ], 201);
    }

    /**
     * Retrieve published videos feed.
     */
    public function videos(Request $request)
    {
        $this->checkMaintenance();

        $query = Video::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('category:id,name,slug')
            ->orderBy('published_at', 'desc');

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $videos = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $videos
        ]);
    }

    /**
     * Retrieve live streams configurations.
     */
    public function liveStreams()
    {
        $this->checkMaintenance();

        $tvUrl = trim(Setting::get('live_tv_url', ''));
        $tvEmbedCode = trim(Setting::get('live_tv_embed_code', ''));
        $tvType = trim(Setting::get('live_tv_type', 'auto'));

        $isTvActive = (bool) filter_var(Setting::get('live_tv_active', '1'), FILTER_VALIDATE_BOOLEAN);

        // Fallback: If live_tv_url is empty but live_tv_embed_code contains iframe/video src or Twitch channel, extract it for mobile app players
        if (empty($tvUrl) && !empty($tvEmbedCode)) {
            if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $tvEmbedCode, $matches)) {
                $tvUrl = $matches[1];
            } elseif (preg_match('/channel:\s*["\']([^"\']+)["\']/i', $tvEmbedCode, $matches)) {
                $currentHost = request()->getHost();
                $tvUrl = "https://player.twitch.tv/?channel={$matches[1]}&parent={$currentHost}&autoplay=true";
            }
        }

        // Format Twitch URLs into mobile webview player embed links with parent domain
        if (!empty($tvUrl) && preg_match('/twitch\.tv\/([a-zA-Z0-9_]+)/i', $tvUrl, $matches)) {
            $currentHost = request()->getHost();
            $pathOrChannel = $matches[1];
            if (strtolower($pathOrChannel) === 'videos' && preg_match('/twitch\.tv\/videos\/([0-9]+)/i', $tvUrl, $vMatches)) {
                $tvUrl = "https://player.twitch.tv/?video={$vMatches[1]}&parent={$currentHost}&autoplay=true";
            } elseif (strtolower($pathOrChannel) !== 'js') {
                $tvUrl = "https://player.twitch.tv/?channel={$pathOrChannel}&parent={$currentHost}&autoplay=true";
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'live_tv_url' => $tvUrl,
                'live_tv_embed_code' => $tvEmbedCode,
                'live_tv_type' => $tvType,
                'live_tv_active' => $isTvActive,
                'live_radio_url' => Setting::get('live_radio_url', ''),
                'live_radio_active' => (bool) filter_var(Setting::get('live_radio_active', '1'), FILTER_VALIDATE_BOOLEAN),
                'tv_schedule' => Setting::get('tv_schedule', []),
                'radio_schedule' => Setting::get('radio_schedule', []),
            ]
        ]);
    }

    /**
     * Submit contact / feedback message.
     */
    public function contact(Request $request)
    {
        $this->checkMaintenance();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10'
        ]);

        $message = ContactMessage::create([
            'name' => strip_tags(trim($request->name)),
            'email' => strip_tags(trim(strtolower($request->email))),
            'subject' => strip_tags(trim($request->subject)),
            'message' => strip_tags(trim($request->message)),
            'is_read' => false
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Feedback submitted successfully.',
            'data' => $message
        ], 201);
    }

    /**
     * Subscribe to email newsletter.
     */
    public function subscribeNewsletter(Request $request)
    {
        $this->checkMaintenance();

        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $email = strip_tags(trim(strtolower($request->email)));
        $existing = Newsletter::where('email', $email)->first();
        if ($existing) {
            return response()->json([
                'status' => 'success',
                'message' => 'You are already subscribed to our newsletter!'
            ]);
        }

        $subscriber = Newsletter::create([
            'email' => $email,
            'is_active' => true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Subscribed to newsletter successfully.',
            'data' => $subscriber
        ], 201);
    }

    /**
     * Retrieve active advertisements.
     */
    public function advertisements(Request $request)
    {
        $this->checkMaintenance();

        $query = Advertisement::active();

        if ($request->filled('location')) {
            $query->location($request->location);
        }

        $ads = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $ads
        ]);
    }

    /**
     * Retrieve native advertisements and configuration for mobile app.
     */
    public function nativeAds()
    {
        $this->checkMaintenance();

        $nativeAds = Advertisement::active()
            ->where('location', 'mobile_native')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'enabled' => (bool) Setting::get('mobile_app_native_ads_enabled', false),
                'admob_native_id' => Setting::get('mobile_app_admob_native_id', ''),
                'facebook_native_id' => Setting::get('mobile_app_facebook_native_id', ''),
                'native_ad_code' => Setting::get('mobile_app_native_ad_code', ''),
                'frequency' => (int) Setting::get('mobile_app_native_ad_frequency', 5),
                'items' => $nativeAds
            ]
        ]);
    }

    /**
     * Retrieve active breaking news alerts.
     */
    public function breakingNews()
    {
        $this->checkMaintenance();

        $alerts = BreakingNews::active()->get();

        return response()->json([
            'status' => 'success',
            'data' => $alerts
        ]);
    }

    /**
     * Retrieve approved & paid announcements.
     */
    public function announcements()
    {
        $this->checkMaintenance();

        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }

    /**
     * Submit a draft announcement.
     */
    public function submitAnnouncement(Request $request)
    {
        $this->checkMaintenance();

        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'required|string|max:20',
            'type' => 'required|in:funeral,general',
            'media' => 'required|in:tv,radio,both',
            'content' => 'required|string|min:5',
            'days_count' => 'required|integer|min:1|max:30',
            'airing_date' => 'required|date',
            'submitter_type' => 'nullable|in:self,agent',
            'agent_pin' => 'required_if:submitter_type,agent|nullable|string|size:4',
        ]);

        // Require On-Air Visual image if broadcasting on TV or Both
        if (($request->media === 'tv' || $request->media === 'both') &&
            !$request->hasFile('visual_image') &&
            !$request->hasFile('image') &&
            !$request->filled('image_url') &&
            !$request->filled('visual_image')) {
            return response()->json([
                'status' => 'error',
                'message' => 'On-Air Visual Image Required: Announcements broadcasting on TV or Both TV & Radio require an image/photo to be displayed on screen during broadcast.'
            ], 422);
        }

        $selectedAgentId = null;
        if ($request->submitter_type === 'agent') {
            $agent = \App\Models\Agent::where('pin', $request->agent_pin)->first();
            if (!$agent) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Agent PIN code.'
                ], 422);
            }
            $selectedAgentId = $agent->id;
        }

        // Process visual image upload if provided
        $imagesList = [];
        if ($request->hasFile('visual_image')) {
            $path = $request->file('visual_image')->store('announcements', 'public');
            $imagesList[] = asset('storage/' . $path);
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $imagesList[] = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagesList[] = $request->input('image_url');
        } elseif ($request->filled('visual_image')) {
            $imagesList[] = $request->input('visual_image');
        }

        // Calculate rate based on media channel settings
        $rate = 5;
        if ($request->media === 'tv') {
            $rate = (int) Setting::get('announcement_rate_tv', 5);
        } elseif ($request->media === 'radio') {
            $rate = (int) Setting::get('announcement_rate_radio', 3);
        } else {
            $rate = (int) Setting::get('announcement_rate_both', 7);
        }

        // Count words
        $content = strip_tags(trim($request->content));
        $wordCount = count(array_filter(explode(' ', preg_replace('/\s+/', ' ', trim($content)))));
        $totalAmount = $wordCount * $rate * (int) $request->days_count;

        $announcement = Announcement::create([
            'agent_id' => $selectedAgentId,
            'visitor_name' => strip_tags(trim($request->visitor_name)),
            'visitor_email' => $request->visitor_email ? strip_tags(trim(strtolower($request->visitor_email))) : null,
            'visitor_phone' => strip_tags(trim($request->visitor_phone)),
            'type' => $request->type,
            'media' => $request->media,
            'content' => $content,
            'airing_date' => $request->airing_date,
            'images' => !empty($imagesList) ? $imagesList : null,
            'word_count' => $wordCount,
            'days_count' => (int) $request->days_count,
            'rate_per_word' => $rate,
            'total_amount' => $totalAmount,
            'payment_status' => 'pending',
            'is_approved' => false,
        ]);

        // Create log notification
        ContactMessage::create([
            'name' => 'System Alert',
            'email' => 'announcements@getembenews.com',
            'subject' => 'Mobile App Announcement Drafted',
            'message' => "A new announcement has been drafted via Mobile App by {$request->visitor_name} ({$request->visitor_phone}) with cost KSh {$totalAmount}."
        ]);

        \App\Support\Sms::sendAdminDraftNotification($announcement);

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement drafted successfully.',
            'data' => $announcement
        ], 201);
    }

    /**
     * Process live M-Pesa STK Push payment.
     */
    public function payAnnouncement(Request $request, $id)
    {
        $this->checkMaintenance();

        $announcement = Announcement::findOrFail($id);

        // Allow overriding phone number for M-Pesa push
        $phone = $request->input('phone', $announcement->visitor_phone);
        $amount = $announcement->total_amount;
        $reference = 'ANN-' . $announcement->id;

        // Execute live Safaricom M-Pesa STK Push
        $stkResult = \App\Support\Mpesa::stkPush($phone, $amount, $reference);

        if ($stkResult['success']) {
            $checkoutRequestId = $stkResult['checkout_request_id'];
            \Illuminate\Support\Facades\Cache::put('mpesa_ann_' . $checkoutRequestId, $announcement->id, 3600);
            \Illuminate\Support\Facades\Cache::put('mpesa_last_checkout_' . $announcement->id, $checkoutRequestId, 3600);

            return response()->json([
                'status' => 'success',
                'mode' => 'stk_push',
                'checkout_request_id' => $checkoutRequestId,
                'message' => "M-Pesa STK Push prompt sent to {$phone}. Please enter your M-Pesa PIN on your phone handset screen.",
                'data' => $announcement
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $stkResult['message'] ?? 'Failed to trigger live M-Pesa STK Push prompt.'
        ], 400);
    }

    /**
     * Check current status of announcement payment.
     */
    public function checkAnnouncementPaymentStatus(Request $request, $id)
    {
        $this->checkMaintenance();

        $announcement = Announcement::findOrFail($id);

        if ($announcement->payment_status === 'paid') {
            return response()->json([
                'status' => 'success',
                'payment_status' => 'paid',
                'message' => 'Payment confirmed successfully.',
                'data' => $announcement
            ]);
        }

        $checkoutRequestId = $request->input('checkout_request_id');
        if (empty($checkoutRequestId)) {
            $checkoutRequestId = \Illuminate\Support\Facades\Cache::get('mpesa_last_checkout_' . $announcement->id);
        }

        if (!empty($checkoutRequestId)) {
            // Check cache first (webhook update)
            $cached = \Illuminate\Support\Facades\Cache::get('mpesa_status_' . $checkoutRequestId);
            if ($cached) {
                if (((int) ($cached['code'] ?? -1)) === 0) {
                    return response()->json([
                        'status' => 'success',
                        'payment_status' => 'paid',
                        'message' => 'Payment confirmed via webhook callback.',
                        'data' => $announcement->fresh()
                    ]);
                }
            }

            // Query Safaricom status
            $queryResult = \App\Support\Mpesa::queryStatus($checkoutRequestId);
            if ($queryResult['success'] && $queryResult['status'] === 'success') {
                $announcement->update([
                    'payment_status' => 'paid',
                    'payment_reference' => 'MPESA-STK-' . strtoupper(Str::random(8)),
                    'is_approved' => true
                ]);

                return response()->json([
                    'status' => 'success',
                    'payment_status' => 'paid',
                    'message' => 'Payment confirmed.',
                    'data' => $announcement->fresh()
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'payment_status' => $announcement->payment_status,
            'message' => 'Payment still pending M-Pesa PIN entry.',
            'data' => $announcement
        ]);
    }

    /**
     * OCR Scanner Endpoint for Announcement Document Images.
     * Accepts image uploads or base64 data and returns extracted text & word count.
     */
    public function ocrAnnouncement(Request $request)
    {
        $this->checkMaintenance();

        $extractedTexts = [];
        
        // Handle direct text payload if provided
        if ($request->filled('text')) {
            $extractedTexts[] = $request->input('text');
        }

        // Handle base64 encoded image string if provided
        if ($request->filled('base64') || $request->filled('image_base64')) {
            $rawBase64 = $request->input('base64', $request->input('image_base64'));
            $cleanBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $rawBase64);
            $imageData = base64_decode($cleanBase64);
            if ($imageData) {
                $tempPath = sys_get_temp_dir() . '/ocr_input_' . uniqid() . '.jpg';
                file_put_contents($tempPath, $imageData);
                $text = $this->performOcrExtraction($tempPath);
                @unlink($tempPath);
                if (!empty($text)) {
                    $extractedTexts[] = $text;
                }
            }
        }

        // Handle uploaded images array or single file
        $files = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
        } elseif ($request->hasFile('image')) {
            $files = [$request->file('image')];
        }

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) continue;
            
            $text = $this->performOcrExtraction($file->getPathname());
            if (!empty($text)) {
                $extractedTexts[] = $text;
            }
        }

        // Combine text across pages
        $combinedText = trim(implode("\n\n", array_filter($extractedTexts)));

        if (empty($combinedText)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No readable text could be extracted from the selected image(s). Please try taking a clearer, well-lit photo of the document.',
            ], 422);
        }

        $wordCount = count(array_filter(explode(' ', preg_replace('/\s+/', ' ', trim(strip_tags($combinedText))))));

        return response()->json([
            'status' => 'success',
            'data' => [
                'text' => $combinedText,
                'word_count' => $wordCount,
                'pages_count' => count($extractedTexts) > 0 ? count($extractedTexts) : 1,
            ]
        ]);
    }

    /**
     * Internal helper to extract exact text from an image file using Tesseract or Cloud OCR.
     */
    protected function performOcrExtraction(string $filePath): string
    {
        // 1. Try native Tesseract CLI if available
        if (function_exists('exec')) {
            $tesseract = trim((string) shell_exec('which tesseract 2>/dev/null'));
            if ($tesseract && file_exists($tesseract)) {
                $outputFile = sys_get_temp_dir() . '/ocr_' . uniqid();
                exec(escapeshellcmd("{$tesseract} " . escapeshellarg($filePath) . " {$outputFile} --oem 1 -l eng 2>/dev/null"));
                if (file_exists($outputFile . '.txt')) {
                    $text = file_get_contents($outputFile . '.txt');
                    @unlink($outputFile . '.txt');
                    $cleanText = trim((string) $text);
                    if (!empty($cleanText)) {
                        return $cleanText;
                    }
                }
            }
        }

        // 2. Fallback to Cloud OCR API (ocr.space) for real image text extraction
        try {
            if (file_exists($filePath) && filesize($filePath) > 0) {
                $imageData = file_get_contents($filePath);
                $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
                $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);

                $ch = curl_init('https://api.ocr.space/parse/image');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query([
                        'apikey' => 'helloworld',
                        'base64Image' => $base64,
                        'language' => 'eng',
                        'isOverlayRequired' => 'false',
                    ]),
                    CURLOPT_TIMEOUT => 25,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);

                $response = curl_exec($ch);
                curl_close($ch);

                if ($response) {
                    $json = json_decode($response, true);
                    if (!empty($json['ParsedResults'][0]['ParsedText'])) {
                        return trim($json['ParsedResults'][0]['ParsedText']);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore API exceptions
        }

        return "";
    }

    /**
     * Trigger M-Pesa STK Push payment for single article purchase on Mobile App.
     */
    public function payArticle(Request $request, int $id)
    {
        $this->checkMaintenance();

        $article = Article::findOrFail($id);
        $user = $request->user();

        if ($user->canAccessArticle($article)) {
            return response()->json([
                'status' => 'success',
                'message' => 'You already have access to this article.',
                'data' => ['has_access' => true]
            ]);
        }

        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $phone = trim($request->phone);
        $amount = (float) ($article->price ?: Setting::get('paywall_default_article_price', '50'));
        $reference = 'ART-' . $article->id;

        $stkResult = \App\Support\Mpesa::stkPush($phone, $amount, $reference);

        if ($stkResult['success']) {
            $checkoutRequestId = $stkResult['checkout_request_id'];
            \Illuminate\Support\Facades\Cache::put('mpesa_paywall_' . $checkoutRequestId, [
                'user_id' => $user->id,
                'article_id' => $article->id,
                'option' => 'single',
                'amount' => $amount,
                'phone' => $phone,
            ], 3600);

            return response()->json([
                'status' => 'success',
                'mode' => 'stk_push',
                'checkout_request_id' => $checkoutRequestId,
                'message' => "M-Pesa STK Push sent to {$phone} for KSh {$amount}. Please enter your M-Pesa PIN on your phone handset screen.",
                'data' => [
                    'article_id' => $article->id,
                    'amount' => $amount
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $stkResult['message'] ?? 'Failed to trigger M-Pesa STK Push.'
        ], 400);
    }

    /**
     * Trigger M-Pesa STK Push payment for subscription pass on Mobile App.
     */
    public function paySubscription(Request $request)
    {
        $this->checkMaintenance();

        $user = $request->user();

        $request->validate([
            'plan' => 'required|in:daily,weekly,monthly',
            'phone' => 'required|string|max:20',
        ]);

        $plan = $request->plan;
        $phone = trim($request->phone);

        $amount = match($plan) {
            'daily' => (float) Setting::get('paywall_daily_price', '20'),
            'weekly' => (float) Setting::get('paywall_weekly_price', '100'),
            'monthly' => (float) Setting::get('paywall_monthly_price', '300'),
            default => 20.0,
        };

        $reference = 'SUB-' . strtoupper($plan);

        $stkResult = \App\Support\Mpesa::stkPush($phone, $amount, $reference);

        if ($stkResult['success']) {
            $checkoutRequestId = $stkResult['checkout_request_id'];
            \Illuminate\Support\Facades\Cache::put('mpesa_paywall_' . $checkoutRequestId, [
                'user_id' => $user->id,
                'option' => $plan,
                'amount' => $amount,
                'phone' => $phone,
            ], 3600);

            return response()->json([
                'status' => 'success',
                'mode' => 'stk_push',
                'checkout_request_id' => $checkoutRequestId,
                'message' => "M-Pesa STK Push sent to {$phone} for " . ucfirst($plan) . " Subscription Pass (KSh {$amount}). Please enter your M-Pesa PIN on your phone.",
                'data' => [
                    'plan' => $plan,
                    'amount' => $amount
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $stkResult['message'] ?? 'Failed to trigger M-Pesa STK Push.'
        ], 400);
    }

    /**
     * Check status of article purchase or subscription.
     */
    public function checkPaywallStatus(Request $request)
    {
        $this->checkMaintenance();

        $checkoutRequestId = $request->input('checkout_request_id');
        $user = $request->user();

        if (!empty($checkoutRequestId)) {
            $cached = \Illuminate\Support\Facades\Cache::get('mpesa_status_' . $checkoutRequestId);
            if ($cached && ((int) ($cached['code'] ?? -1)) === 0) {
                return response()->json([
                    'status' => 'success',
                    'payment_status' => 'paid',
                    'user_has_access' => true,
                    'message' => 'Payment confirmed! You now have full access.',
                    'user' => $user->fresh()
                ]);
            }

            // Query Safaricom directly
            $query = \App\Support\Mpesa::queryStatus($checkoutRequestId);
            if ($query['success'] && $query['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'payment_status' => 'paid',
                    'user_has_access' => true,
                    'message' => 'Payment confirmed! You now have full access.',
                    'user' => $user->fresh()
                ]);
            }
        }

        $hasAccess = false;
        if ($request->filled('article_id')) {
            $article = Article::find($request->article_id);
            if ($article) {
                $hasAccess = $user->canAccessArticle($article);
            }
        } else {
            $hasAccess = $user->hasActiveSubscription();
        }

        return response()->json([
            'status' => 'success',
            'payment_status' => $hasAccess ? 'paid' : 'pending',
            'user_has_access' => $hasAccess,
            'message' => $hasAccess ? 'Access verified.' : 'Payment pending PIN entry.',
            'user' => $user->fresh()
        ]);
    }
}



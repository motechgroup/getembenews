<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Article;
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
                'brand_color' => Setting::get('brand_color', '#cc6c3b'),
                'favicon' => Setting::get('favicon', ''),
                'app_play_store_url' => Setting::get('app_play_store_url', 'https://play.google.com/store'),
                'app_app_store_url' => Setting::get('app_app_store_url', 'https://www.apple.com/app-store'),
                'app_banner_title' => Setting::get('app_banner_title', 'Download Getembe News Mobile App'),
                'app_banner_desc' => Setting::get('app_banner_desc', 'Get fast, reliable news updates directly on your smartphone.'),
                
                // Mobile configuration
                'mobile_app_version_ios' => Setting::get('mobile_app_version_ios', '1.0.0'),
                'mobile_app_version_android' => Setting::get('mobile_app_version_android', '1.0.0'),
                'mobile_app_force_update' => (bool) Setting::get('mobile_app_force_update', false),
                'mobile_app_ios_link' => Setting::get('mobile_app_ios_link', 'https://www.apple.com/app-store'),
                'mobile_app_android_link' => Setting::get('mobile_app_android_link', 'https://play.google.com/store'),
                'mobile_app_ads_enabled' => (bool) Setting::get('mobile_app_ads_enabled', false),
                'mobile_app_admob_banner_id' => Setting::get('mobile_app_admob_banner_id', ''),
                'mobile_app_admob_interstitial_id' => Setting::get('mobile_app_admob_interstitial_id', ''),
                'mobile_app_maintenance_mode' => (bool) Setting::get('mobile_app_maintenance_mode', false),
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
                'comments' => $comments,
                'related_articles' => $related
            ]
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
            'name' => $request->name,
            'email' => strtolower($request->email),
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
            'body' => $request->body,
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'live_tv_url' => Setting::get('live_tv_url', ''),
                'live_tv_active' => (bool) Setting::get('live_tv_active', false),
                'live_radio_url' => Setting::get('live_radio_url', ''),
                'live_radio_active' => (bool) Setting::get('live_radio_active', false),
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
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
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

        $existing = Newsletter::where('email', $request->email)->first();
        if ($existing) {
            return response()->json([
                'status' => 'success',
                'message' => 'You are already subscribed to our newsletter!'
            ]);
        }

        $subscriber = Newsletter::create([
            'email' => $request->email,
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
    public function advertisements()
    {
        $this->checkMaintenance();

        $ads = Advertisement::active()->get();

        return response()->json([
            'status' => 'success',
            'data' => $ads
        ]);
    }
}

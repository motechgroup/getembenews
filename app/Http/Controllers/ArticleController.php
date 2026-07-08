<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display the specified article.
     */
    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        
        // Views count is now tracked securely via client-side behavioral verification endpoint

        // Fetch related articles (same category, excluding current)
        $relatedArticles = Article::published()
            ->forCategory($article->category_id)
            ->where('id', '!=', $article->id)
            ->take(3)
            ->get();

        $sidebarAd = Advertisement::active()->location('sidebar')->first();

        return view('articles.show', compact('article', 'relatedArticles', 'sidebarAd'));
    }

    /**
     * Securely track and verify article views using anti-cheat signals.
     */
    public function trackView(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        
        // 1. Replay & Signature check
        $expectedHash = hash_hmac('sha256', $id . session()->getId(), config('app.key'));
        if ($request->input('hash') !== $expectedHash) {
            return response()->json(['error' => 'Invalid view signature'], 400);
        }

        // 2. Behavioral verification (anti-cheat)
        $timeSpent = (int) $request->input('time_spent', 0);
        $scrolled = (bool) $request->input('scrolled', false);
        $interacted = (bool) $request->input('interacted', false);

        if ($timeSpent < 10 || !$scrolled || !$interacted) {
            return response()->json(['error' => 'Human verification checks failed'], 403);
        }

        // 3. Throttle view count: 1 view per IP per article per hour to prevent inflation bots
        $ip = $request->ip();
        $throttleKey = "article_view_throttle:{$id}:{$ip}";
        if (\Illuminate\Support\Facades\Cache::has($throttleKey)) {
            return response()->json(['status' => 'already_counted']);
        }
        
        // Count the view
        \Illuminate\Support\Facades\Cache::put($throttleKey, true, 3600); // 1 hour throttle
        $article->increment('views_count');

        // 4. Calculate simulated Author Reward / Earnings
        $authorId = $article->user_id;
        $rewardRate = (float) \App\Models\Setting::get('author_reward_rate', '0.10');
        if ($rewardRate > 0) {
            $earningsKey = "author_earnings:{$authorId}";
            \Illuminate\Support\Facades\Cache::increment($earningsKey, $rewardRate);
        }

        return response()->json(['status' => 'success', 'views' => $article->views_count]);
    }

    /**
     * Display articles belonging to a specific category.
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $articles = Article::published()
            ->forCategory($category->id)
            ->paginate(17);

        return view('categories.show', compact('category', 'articles'));
    }

    /**
     * Search for articles.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $articles = Article::published()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('title', 'like', '%' . $query . '%')
                          ->orWhere('body', 'like', '%' . $query . '%')
                          ->orWhere('subtitle', 'like', '%' . $query . '%');
                });
            })
            ->paginate(12);

        return view('search', compact('articles', 'query'));
    }
}

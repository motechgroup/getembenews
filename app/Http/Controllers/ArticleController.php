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
        
        // Increment the view counter
        $article->increment('views_count');

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

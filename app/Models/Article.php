<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'title', 'slug', 'subtitle', 'body', 'featured_image', 'user_id', 
    'category_id', 'status', 'is_featured', 'is_breaking', 'is_pinned', 
    'published_at', 'seo_title', 'seo_description', 'read_time', 'views_count'
])]
class Article extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'is_pinned' => 'boolean',
            'views_count' => 'integer',
            'read_time' => 'integer',
        ];
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_articles', 'article_id', 'user_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    // Calculate reading time based on 200 words per minute
    public static function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200);
        return (int) max(1, $minutes);
    }
}

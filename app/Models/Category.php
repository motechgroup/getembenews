<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'order', 'image_url'];

    // A category can have a parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // A category can have many subcategories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    // A category has many articles
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    // Many-to-many relationship with articles
    public function posts()
    {
        return $this->belongsToMany(Article::class, 'article_category');
    }

    // A category has many videos
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}

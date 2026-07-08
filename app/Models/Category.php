<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'order', 'image_url', 'seo_title', 'seo_description'];

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

    // Get all nested subcategories recursively
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // Accessor to get full path name (e.g. News > Politics > Local)
    public function getPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    // Fetch ordered flat array of categories representing hierarchy tree with depth indicators
    public static function getTree()
    {
        $categories = self::whereNull('parent_id')->with('allChildren')->orderBy('order')->get();
        $tree = [];
        
        $traverse = function ($items, $depth = 0) use (&$tree, &$traverse) {
            foreach ($items as $item) {
                $item->depth = $depth;
                $tree[] = $item;
                $traverse($item->allChildren, $depth + 1);
            }
        };
        
        $traverse($categories);
        return $tree;
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

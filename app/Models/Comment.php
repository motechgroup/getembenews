<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['article_id', 'user_id', 'body', 'parent_id', 'status'])]
class Comment extends Model
{
    use HasFactory;

    // A comment belongs to an article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // A comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A comment can have a parent comment (replies)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // A comment can have many replies
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 'approved')->orderBy('created_at', 'asc');
    }
}

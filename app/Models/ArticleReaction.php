<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleReaction extends Model
{
    protected $fillable = ['article_id', 'type', 'ip_address'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}

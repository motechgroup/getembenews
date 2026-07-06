<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'image_url', 'script_code', 'destination_url', 'location', 'is_active', 'starts_at', 'expires_at', 'clicks', 'impressions'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'clicks' => 'integer',
            'impressions' => 'integer',
        ];
    }

    // Scope active ads
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    // Scope by position/location
    public function scopeLocation($query, string $location)
    {
        return $query->where('location', $location);
    }
}

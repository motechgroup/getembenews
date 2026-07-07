<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'type',
        'media',
        'content',
        'word_count',
        'days_count',
        'rate_per_word',
        'total_amount',
        'payment_status',
        'payment_reference',
        'is_approved',
        'agent_id',
        'commission_amount',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'word_count' => 'integer',
        'days_count' => 'integer',
        'rate_per_word' => 'integer',
        'total_amount' => 'integer',
        'agent_id' => 'integer',
        'commission_amount' => 'integer',
    ];

    /**
     * Scope a query to only include approved announcements.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include paid announcements.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Relationship: The agent who submitted this announcement.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}

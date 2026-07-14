<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'announcement_id',
        'subject',
        'description',
        'status',
        'resolution',
    ];

    /**
     * Relationship: The agent who raised this dispute.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Relationship: The specific announcement in dispute, if any.
     */
    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}

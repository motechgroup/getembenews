<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'amount',
        'payment_method',
        'reference',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationship: The agent who received this payout.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}

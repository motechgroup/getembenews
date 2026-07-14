<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'commission_percentage',
        'pin',
    ];

    protected $casts = [
        'commission_percentage' => 'integer',
    ];

    /**
     * Relationship: Announcements submitted by this agent.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Relationship: Payouts received by this agent.
     */
    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    /**
     * Relationship: Disputes raised by this agent.
     */
    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    /**
     * Paid announcements.
     */
    public function paidAnnouncements()
    {
        return $this->announcements()->where('payment_status', 'paid');
    }

    /**
     * Attribute: total count of paid announcements submitted.
     */
    public function getTotalAnnouncementsAttribute()
    {
        return $this->paidAnnouncements()->count();
    }

    /**
     * Attribute: total revenue generated from paid announcements.
     */
    public function getTotalRevenueAttribute()
    {
        return (int) $this->paidAnnouncements()->sum('total_amount');
    }

    /**
     * Attribute: total commission earned from paid announcements.
     */
    public function getTotalCommissionAttribute()
    {
        return (int) $this->paidAnnouncements()->sum('commission_amount');
    }

    /**
     * Attribute: total commission paid out to this agent.
     */
    public function getTotalPayoutsAttribute()
    {
        return (int) $this->payouts()->where('status', 'completed')->sum('amount');
    }

    /**
     * Attribute: remaining unpaid commission balance.
     */
    public function getCommissionBalanceAttribute()
    {
        return $this->total_commission - $this->total_payouts;
    }
}

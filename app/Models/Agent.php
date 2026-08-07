<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_name',
        'phone',
        'location',
        'commission_percentage',
        'pin',
    ];

    /**
     * Generate a unique 4-digit numeric PIN for agents.
     */
    public static function generateUniquePin(): string
    {
        do {
            $pin = str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('pin', $pin)->exists());

        return $pin;
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($agent) {
            if (empty($agent->pin)) {
                $agent->pin = static::generateUniquePin();
            }
        });
    }

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

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Support\Str;

class AnnouncementSubmit extends Component
{
    // Form Inputs
    public $visitor_name = '';
    public $visitor_email = '';
    public $visitor_phone = '';
    public $type = 'funeral'; // funeral, general
    public $media = 'tv'; // tv, radio, both
    public $content = '';
    public $days_count = 1;

    // Calculation states
    public $rate = 5;
    public $word_count = 0;
    public $total_price = 0;

    // Checkout & M-Pesa
    public $phone_for_mpesa = '';
    public $showCheckoutModal = false;
    public $mpesa_status = 'idle'; // idle, sending, success, error
    public $currentAnnouncementId = null;

    protected $rules = [
        'visitor_name' => 'required|string|max:255',
        'visitor_email' => 'required|email|max:255',
        'visitor_phone' => 'required|string|max:20',
        'type' => 'required|in:funeral,general',
        'media' => 'required|in:tv,radio,both',
        'content' => 'required|string|min:5',
        'days_count' => 'required|integer|min:1|max:30',
    ];

    public function mount()
    {
        $this->updateCalculations();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['content', 'media', 'days_count'])) {
            $this->updateCalculations();
        }
    }

    /**
     * Calculate rate, word count and total price dynamically.
     */
    public function updateCalculations()
    {
        // 1. Get Rate
        if ($this->media === 'tv') {
            $this->rate = (int) Setting::get('announcement_rate_tv', 5);
        } elseif ($this->media === 'radio') {
            $this->rate = (int) Setting::get('announcement_rate_radio', 3);
        } else {
            $this->rate = (int) Setting::get('announcement_rate_both', 7);
        }

        // 2. Word Count
        if (empty(trim($this->content))) {
            $this->word_count = 0;
        } else {
            $this->word_count = count(array_filter(explode(' ', preg_replace('/\s+/', ' ', trim($this->content)))));
        }

        // 3. Total Price
        $this->total_price = $this->word_count * $this->rate * (int) $this->days_count;
    }

    /**
     * Validate and submit announcement (pending payment).
     */
    public function submitAnnouncement()
    {
        $this->validate();

        $announcement = Announcement::create([
            'visitor_name' => $this->visitor_name,
            'visitor_email' => $this->visitor_email,
            'visitor_phone' => $this->visitor_phone,
            'type' => $this->type,
            'media' => $this->media,
            'content' => $this->content,
            'word_count' => $this->word_count,
            'days_count' => (int) $this->days_count,
            'rate_per_word' => $this->rate,
            'total_amount' => $this->total_price,
            'payment_status' => 'pending',
            'is_approved' => false,
        ]);

        $this->currentAnnouncementId = $announcement->id;
        $this->phone_for_mpesa = $this->visitor_phone;
        $this->showCheckoutModal = true;
        $this->mpesa_status = 'idle';
    }

    /**
     * Simulate triggering the M-Pesa STK Push.
     */
    public function triggerMpesaStkPush()
    {
        $this->validate([
            'phone_for_mpesa' => 'required|string|min:9|max:15'
        ]);

        $this->mpesa_status = 'sending';

        // We will dispatch a browser event or handle a simulated timeout to complete the payment
        $this->dispatch('start-stk-timer');
    }

    /**
     * Handle payment success webhook simulation.
     */
    public function confirmPaymentSuccess()
    {
        if (!$this->currentAnnouncementId) return;

        $announcement = Announcement::find($this->currentAnnouncementId);
        if ($announcement) {
            $ref = 'MPESA-STK-' . strtoupper(Str::random(10));
            $announcement->update([
                'payment_status' => 'paid',
                'payment_reference' => $ref,
            ]);

            $this->mpesa_status = 'success';
            
            // Reset input values
            $this->reset(['visitor_name', 'visitor_email', 'visitor_phone', 'content', 'days_count']);
            $this->updateCalculations();
        }
    }

    public function render()
    {
        $publishedAnnouncements = Announcement::approved()->paid()->latest()->get();

        return view('livewire.announcement-submit', [
            'announcements' => $publishedAnnouncements,
        ])->layout('layouts.news');
    }
}

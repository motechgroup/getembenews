<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class AnnouncementSubmit extends Component
{
    use WithPagination;

    // Form Inputs
    public $visitor_name = '';
    public $visitor_email = '';
    public $visitor_phone = '';
    public $type = 'funeral'; // funeral, general
    public $media = 'tv'; // tv, radio, both
    public $content = '';
    public $days_count = 1;
    public $airing_date = '';
    public $submitter_type = 'self'; // self, agent
    public $agent_pin = '';

    // Calculation states
    public $rate = 5;
    public $word_count = 0;
    public $total_price = 0;

    // Checkout & M-Pesa
    public $phone_for_mpesa = '';
    public $showCheckoutModal = false;
    public $mpesa_status = 'idle'; // idle, sending, success, error
    public $currentAnnouncementId = null;

    // Agent Login State
    public $showAgentLoginModal = false;
    public $login_pin = '';

    protected $rules = [
        'visitor_name' => 'required|string|max:255',
        'visitor_email' => 'nullable|email|max:255',
        'visitor_phone' => 'required|string|max:20',
        'type' => 'required|in:funeral,general',
        'media' => 'required|in:tv,radio,both',
        'content' => 'required|string|min:5',
        'days_count' => 'required|integer|min:1|max:30',
        'airing_date' => 'required|date|after_or_equal:today',
        'submitter_type' => 'required|in:self,agent',
        'agent_pin' => 'required_if:submitter_type,agent|nullable|string|size:4',
    ];

    public function mount()
    {
        $this->airing_date = now()->toDateString();
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

        // Rate Limiting: 3 submissions per IP per minute
        $ip = request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('announcement-submit:' . $ip, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('announcement-submit:' . $ip);
            $this->addError('content', "Too many announcements submitted. Please wait {$seconds} seconds.");
            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit('announcement-submit:' . $ip, 60);

        $selectedAgentId = null;
        if ($this->submitter_type === 'agent') {
            $agent = \App\Models\Agent::where('pin', $this->agent_pin)->first();
            if (!$agent) {
                $this->addError('agent_pin', 'Invalid Agent PIN code.');
                return;
            }
            $selectedAgentId = $agent->id;
        }

        $announcement = Announcement::create([
            'visitor_name' => $this->visitor_name,
            'visitor_email' => $this->visitor_email ?: null,
            'visitor_phone' => $this->visitor_phone,
            'type' => $this->type,
            'media' => $this->media,
            'content' => $this->content,
            'airing_date' => $this->airing_date,
            'word_count' => $this->word_count,
            'days_count' => (int) $this->days_count,
            'rate_per_word' => $this->rate,
            'total_amount' => $this->total_price,
            'payment_status' => 'pending',
            'is_approved' => false,
            'agent_id' => $selectedAgentId,
        ]);

        // Notify admin via ContactMessage log
        \App\Models\ContactMessage::create([
            'name' => 'System Alert',
            'email' => 'announcements@getembenews.com',
            'subject' => 'New Announcement Submitted (Pending Payment)',
            'message' => "A new announcement has been drafted by {$this->visitor_name} ({$this->visitor_phone}) with cost KSh {$this->total_price} for {$this->days_count} days."
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

        // Rate Limiting: 3 M-Pesa push attempts per IP per minute
        $ip = request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('mpesa-push:' . $ip, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('mpesa-push:' . $ip);
            $this->addError('phone_for_mpesa', "Too many payment attempts. Please wait {$seconds} seconds.");
            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit('mpesa-push:' . $ip, 60);

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

            $commissionAmount = 0;
            if ($announcement->agent_id) {
                $agent = \App\Models\Agent::find($announcement->agent_id);
                if ($agent) {
                    $commissionAmount = (int) round(($announcement->total_amount * $agent->commission_percentage) / 100);
                }
            }

            $announcement->update([
                'payment_status' => 'paid',
                'payment_reference' => $ref,
                'commission_amount' => $commissionAmount,
            ]);

            // Notify admin of payment success
            \App\Models\ContactMessage::create([
                'name' => 'System Alert',
                'email' => 'announcements@getembenews.com',
                'subject' => 'Announcement Paid (Ref: ' . $ref . ')',
                'message' => "Announcement ID: {$announcement->id} has been paid successfully. Visitor: {$announcement->visitor_name} ({$announcement->visitor_phone}). Amount: KSh {$announcement->total_amount}."
            ]);

            $this->mpesa_status = 'success';
            
            // Reset input values
            $this->reset(['visitor_name', 'visitor_email', 'visitor_phone', 'content', 'days_count', 'submitter_type', 'agent_pin']);
            $this->airing_date = now()->toDateString();
            $this->updateCalculations();
        }
    }

    public function openAgentLogin()
    {
        $this->resetValidation();
        $this->login_pin = '';
        $this->showAgentLoginModal = true;
    }

    public function closeAgentLogin()
    {
        $this->showAgentLoginModal = false;
        $this->login_pin = '';
        $this->resetValidation();
    }

    public function loginAsAgent()
    {
        $this->validate([
            'login_pin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
        ], [
            'login_pin.regex' => 'The PIN must consist of exactly 4 digits.',
            'login_pin.size' => 'The PIN must be exactly 4 digits.',
        ]);

        $agent = \App\Models\Agent::where('pin', $this->login_pin)->first();

        if (!$agent) {
            $this->addError('login_pin', 'Invalid PIN code.');
            return;
        }

        session(['agent_logged_in' => $agent->id]);
        return $this->redirect('/agent/dashboard');
    }

    public function render()
    {
        $publishedAnnouncements = Announcement::active()->latest()->paginate(10);

        return view('livewire.announcement-submit', [
            'announcements' => $publishedAnnouncements,
        ])->layout('layouts.news');
    }
}

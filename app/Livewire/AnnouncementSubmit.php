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
    public $mpesa_checkout_id = '';
    public $mpesa_error_message = '';
    public $manual_receipt_ref = '';

    // Agent Login State
    public $showAgentLoginModal = false;
    public $login_pin = '';

    protected $rules = [
        'visitor_name' => 'required|string|max:255',
        'visitor_email' => 'nullable|email|max:255',
        'visitor_phone' => 'required|string|max:20',
        'type' => 'required|in:funeral,general',
        'media' => 'required|in:tv,radio,both',
        'content' => 'required|string',
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

        \App\Support\Sms::sendAdminDraftNotification($announcement);

        $this->currentAnnouncementId = $announcement->id;
        $this->phone_for_mpesa = $this->visitor_phone;
        $this->showCheckoutModal = true;
        $this->mpesa_status = 'idle';
    }

    /**
     * Trigger the M-Pesa STK Push.
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
        $this->mpesa_error_message = '';

        // Fallback to simulation if consumer credentials are not set
        $key = \App\Models\Setting::get('mpesa_consumer_key', '');
        $secret = \App\Models\Setting::get('mpesa_consumer_secret', '');

        if (empty($key) || empty($secret)) {
            $this->dispatch('start-stk-timer');
            return;
        }

        // 0. Check database first to see if it is already paid
        if ($this->currentAnnouncementId) {
            $ann = Announcement::find($this->currentAnnouncementId);
            if ($ann && $ann->payment_status === 'paid') {
                $this->confirmPaymentSuccess($ann->payment_reference);
                return;
            }
        }

        // Trigger real Safaricom STK Push
        $announcement = Announcement::find($this->currentAnnouncementId);
        $amount = $announcement ? $announcement->total_amount : 1;
        $reference = $announcement ? 'ANN-' . $announcement->id : 'GetembeNews';

        $result = \App\Support\Mpesa::stkPush($this->phone_for_mpesa, $amount, $reference);

        if ($result['success']) {
            $this->mpesa_checkout_id = $result['checkout_request_id'];
            
            // Map CheckoutRequestID to announcement ID in Cache
            \Illuminate\Support\Facades\Cache::put('mpesa_ann_' . $this->mpesa_checkout_id, $this->currentAnnouncementId, 3600);
            \Illuminate\Support\Facades\Cache::put('mpesa_last_checkout_' . $this->currentAnnouncementId, $this->mpesa_checkout_id, 3600);

            $this->dispatch('start-stk-query-timer');
        } else {
            // Check if the error indicates a duplicate or active transaction still under processing
            $msg = $result['message'] ?? '';
            if (str_contains(strtolower($msg), 'processing') || str_contains(strtolower($msg), 'busy') || str_contains(strtolower($msg), 'active')) {
                $lastCheckout = \Illuminate\Support\Facades\Cache::get('mpesa_last_checkout_' . $this->currentAnnouncementId);
                if ($lastCheckout) {
                    $this->mpesa_checkout_id = $lastCheckout;
                    $this->mpesa_status = 'sending';
                    $this->dispatch('start-stk-query-timer');
                    return;
                }
            }

            $this->mpesa_status = 'error';
            $this->mpesa_error_message = $result['message'];
        }
    }

    /**
     * Check current status of M-Pesa transaction via Safaricom Query API or Webhook cache.
     */
    public function checkMpesaPaymentStatus()
    {
        // 0. Check database first to see if it is already paid
        if ($this->currentAnnouncementId) {
            $ann = Announcement::find($this->currentAnnouncementId);
            if ($ann && $ann->payment_status === 'paid') {
                $this->confirmPaymentSuccess($ann->payment_reference);
                return;
            }
        }

        if (empty($this->mpesa_checkout_id)) return;

        // 1. Check if callback webhook already verified payment
        $cachedResult = \Illuminate\Support\Facades\Cache::get('mpesa_status_' . $this->mpesa_checkout_id);
        if ($cachedResult) {
            $code = (int) $cachedResult['code'];
            if ($code === 0) {
                // Find reference if set
                $ref = 'MPESA-CB-' . \Illuminate\Support\Str::random(10);
                foreach (($cachedResult['metadata'] ?? []) as $item) {
                    if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                        $ref = $item['Value'];
                        break;
                    }
                }
                $this->confirmPaymentSuccess($ref);
                return;
            } else {
                $this->mpesa_status = 'error';
                $this->mpesa_error_message = $cachedResult['desc'] ?: 'Payment failed or cancelled.';
                return;
            }
        }

        // 2. Query Safaricom status endpoint
        $result = \App\Support\Mpesa::queryStatus($this->mpesa_checkout_id);

        if ($result['success'] && $result['status'] === 'success') {
            $this->confirmPaymentSuccess();
        } elseif ($result['status'] === 'failed') {
            $this->mpesa_status = 'error';
            $this->mpesa_error_message = $result['message'] ?: 'Payment failed or cancelled.';
        } elseif ($result['status'] === 'error') {
            \Illuminate\Support\Facades\Log::info("M-Pesa status query error: " . $result['message']);
        }
    }

    /**
     * Handle payment success webhook simulation.
     */
    public function confirmPaymentSuccess($customRef = null)
    {
        if (!$this->currentAnnouncementId) return;

        $announcement = Announcement::find($this->currentAnnouncementId);
        if ($announcement) {
            $ref = $customRef ?: 'MPESA-STK-' . strtoupper(Str::random(10));

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

            \App\Support\Sms::sendAdminPaymentNotification($announcement, $ref);

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

    public function confirmManualPayment()
    {
        $this->validate([
            'manual_receipt_ref' => 'required|string|min:8|max:20'
        ]);

        $ref = strtoupper(trim($this->manual_receipt_ref));
        $this->confirmPaymentSuccess($ref);
    }

    public function render()
    {
        $publishedAnnouncements = Announcement::active()->latest()->paginate(10);

        return view('livewire.announcement-submit', [
            'announcements' => $publishedAnnouncements,
        ])->layout('layouts.news');
    }
}

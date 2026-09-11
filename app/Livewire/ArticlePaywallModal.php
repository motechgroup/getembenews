<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\ArticlePurchase;
use App\Models\ArticleSubscription;
use App\Models\Setting;
use App\Support\Mpesa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use Livewire\Component;

class ArticlePaywallModal extends Component
{
    public Article $article;
    public string $selectedOption = 'article'; // 'article', 'daily', 'weekly', 'monthly'
    public string $phone = '';
    public string $mpesaStatus = 'idle'; // idle, sending, pending, success, error
    public string $statusMessage = '';
    public ?string $checkoutRequestId = null;

    public function mount(Article $article): void
    {
        $this->article = $article;
        
        if (Auth::check() && !empty(Auth::user()->mpesa_phone)) {
            $this->phone = Auth::user()->mpesa_phone;
        }
    }

    public function selectOption(string $option): void
    {
        $this->selectedOption = $option;
        $this->mpesaStatus = 'idle';
        $this->statusMessage = '';
    }

    public function getPriceForOption(string $option): float
    {
        switch ($option) {
            case 'daily':
                return (float) Setting::get('paywall_daily_price', 50);
            case 'weekly':
                return (float) Setting::get('paywall_weekly_price', 200);
            case 'monthly':
                return (float) Setting::get('paywall_monthly_price', 500);
            case 'article':
            default:
                return $this->article->getEffectivePrice();
        }
    }

    public function initiatePayment()
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        $this->validate([
            'phone' => ['required', 'string', 'regex:/^(?:254|\+254|0)?(7|1)\d{8}$/'],
        ], [
            'phone.required' => 'Please enter your M-Pesa phone number.',
            'phone.regex' => 'Please enter a valid Safaricom M-Pesa phone number (e.g. 0712345678).',
        ]);

        $amount = $this->getPriceForOption($this->selectedOption);
        $user = Auth::user();

        // Save phone to user profile if logged in
        if ($user && empty($user->mpesa_phone)) {
            $user->update(['mpesa_phone' => $this->phone]);
        }

        $this->mpesaStatus = 'sending';
        $this->statusMessage = 'Initiating M-Pesa STK Push request...';

        $reference = $this->selectedOption === 'article' 
            ? 'ART-' . $this->article->id 
            : 'SUB-' . strtoupper(substr($this->selectedOption, 0, 1));

        $res = Mpesa::stkPush($this->phone, $amount, $reference);

        if (!$res['success']) {
            $this->mpesaStatus = 'error';
            $this->statusMessage = $res['message'] ?? 'Failed to send M-Pesa STK push.';
            return;
        }

        $this->checkoutRequestId = $res['checkout_request_id'];
        $this->mpesaStatus = 'pending';
        $this->statusMessage = 'STK Push sent to ' . $this->phone . '. Please enter your M-Pesa PIN on your phone to complete payment.';

        // Store payment intent metadata in Cache for webhook callback or manual check
        Cache::put('mpesa_paywall_' . $this->checkoutRequestId, [
            'user_id' => $user?->id,
            'article_id' => $this->article->id,
            'option' => $this->selectedOption,
            'amount' => $amount,
            'phone' => $this->phone,
        ], 600);
    }

    public function checkPaymentStatus(): void
    {
        if (empty($this->checkoutRequestId)) {
            return;
        }

        // 1. Check if Webhook cached status
        $cachedStatus = Cache::get('mpesa_status_' . $this->checkoutRequestId);
        if ($cachedStatus) {
            if (isset($cachedStatus['code']) && (int)$cachedStatus['code'] === 0) {
                $this->fulfillPayment($cachedStatus['metadata'] ?? []);
                return;
            } elseif (isset($cachedStatus['code']) && (int)$cachedStatus['code'] !== 0) {
                $this->mpesaStatus = 'error';
                $this->statusMessage = $cachedStatus['desc'] ?? 'Payment was cancelled or failed on phone.';
                return;
            }
        }

        // 2. Query M-Pesa API status directly
        $queryRes = Mpesa::queryStatus($this->checkoutRequestId);

        if ($queryRes['status'] === 'success') {
            $this->fulfillPayment([]);
        } elseif ($queryRes['status'] === 'failed') {
            $this->mpesaStatus = 'error';
            $this->statusMessage = $queryRes['message'] ?? 'M-Pesa payment failed.';
        }
    }

    protected function fulfillPayment(array $metadata = []): void
    {
        $userId = Auth::id();
        $mpesaRef = 'MPESA-' . strtoupper(\Illuminate\Support\Str::random(8));

        foreach ($metadata as $item) {
            if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                $mpesaRef = $item['Value'];
                break;
            }
        }

        if ($this->selectedOption === 'article') {
            ArticlePurchase::create([
                'user_id' => $userId,
                'article_id' => $this->article->id,
                'amount' => $this->getPriceForOption('article'),
                'phone_number' => $this->phone,
                'mpesa_reference' => $mpesaRef,
                'checkout_request_id' => $this->checkoutRequestId,
                'status' => 'completed',
            ]);
        } else {
            $days = match($this->selectedOption) {
                'daily' => 1,
                'weekly' => 7,
                'monthly' => 30,
                default => 1,
            };

            $expiresAt = now()->addDays($days);

            if ($userId) {
                $user = Auth::user();
                $user->update([
                    'subscription_plan' => $this->selectedOption,
                    'subscription_expires_at' => $expiresAt,
                ]);

                ArticleSubscription::create([
                    'user_id' => $userId,
                    'plan' => $this->selectedOption,
                    'amount' => $this->getPriceForOption($this->selectedOption),
                    'phone_number' => $this->phone,
                    'starts_at' => now(),
                    'expires_at' => $expiresAt,
                    'mpesa_reference' => $mpesaRef,
                    'checkout_request_id' => $this->checkoutRequestId,
                    'status' => 'active',
                ]);
            }
        }

        $this->mpesaStatus = 'success';
        $this->statusMessage = 'Payment successful! Unlocking article...';

        // Refresh page to load full article
        $this->dispatch('articleUnlocked');
    }

    public function render()
    {
        return view('livewire.article-paywall-modal');
    }
}

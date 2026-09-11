<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Announcement;
use App\Models\ContactMessage;

class MpesaCallbackController extends Controller
{
    /**
     * Handle Safaricom STK Push Callback Webhook.
     */
    public function handleCallback(Request $request)
    {
        Log::info("M-Pesa Webhook Callback received: " . json_encode($request->all()));

        $callbackData = $request->input('Body.stkCallback');
        if (!$callbackData) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        $checkoutRequestId = $callbackData['CheckoutRequestID'] ?? null;
        $resultCode = $callbackData['ResultCode'] ?? null;
        $resultDesc = $callbackData['ResultDesc'] ?? null;

        if (!$checkoutRequestId) {
            return response()->json(['status' => 'error', 'message' => 'CheckoutRequestID missing'], 400);
        }

        // Cache the status for our polling query
        Cache::put('mpesa_status_' . $checkoutRequestId, [
            'code' => $resultCode,
            'desc' => $resultDesc,
            'metadata' => $callbackData['CallbackMetadata']['Item'] ?? []
        ], 300);

        if ($resultCode == 0) {
            // 1. Parse Safaricom metadata for receipt / Reference
            $ref = 'MPESA-CB-' . \Illuminate\Support\Str::random(10);
            $metadata = $callbackData['CallbackMetadata']['Item'] ?? [];
            foreach ($metadata as $item) {
                if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                    $ref = $item['Value'];
                    break;
                }
            }

            // 2. Check if Paywall Purchase/Subscription mapped to CheckoutRequestID
            $paywallData = Cache::get('mpesa_paywall_' . $checkoutRequestId);
            if ($paywallData && is_array($paywallData)) {
                $userId = $paywallData['user_id'] ?? null;
                $articleId = $paywallData['article_id'] ?? null;
                $option = $paywallData['option'] ?? 'article';
                $amount = (float) ($paywallData['amount'] ?? 0);
                $phone = $paywallData['phone'] ?? null;

                if (in_array($option, ['single', 'article']) && $articleId) {
                    \App\Models\ArticlePurchase::updateOrCreate([
                        'checkout_request_id' => $checkoutRequestId,
                    ], [
                        'user_id' => $userId,
                        'article_id' => $articleId,
                        'amount' => $amount,
                        'phone_number' => $phone,
                        'mpesa_reference' => $ref,
                        'status' => 'completed',
                    ]);
                    Log::info("Paywall Single Article ID {$articleId} purchased successfully via webhook. Ref: {$ref}");
                } elseif (in_array($option, ['daily', 'weekly', 'monthly']) && $userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $days = match($option) {
                            'daily' => 1,
                            'weekly' => 7,
                            'monthly' => 30,
                            default => 1,
                        };
                        $expiresAt = now()->addDays($days);
                        
                        $user->update([
                            'subscription_plan' => $option,
                            'subscription_expires_at' => $expiresAt,
                        ]);

                        \App\Models\ArticleSubscription::updateOrCreate([
                            'checkout_request_id' => $checkoutRequestId,
                        ], [
                            'user_id' => $user->id,
                            'plan' => $option,
                            'amount' => $amount,
                            'phone_number' => $phone,
                            'starts_at' => now(),
                            'expires_at' => $expiresAt,
                            'mpesa_reference' => $ref,
                            'status' => 'active',
                        ]);
                        Log::info("User ID {$user->id} subscribed to {$option} pass successfully via webhook. Ref: {$ref}");
                    }
                }
            }

            // 3. Find announcement mapped to this CheckoutRequestID
            $announcementId = Cache::get('mpesa_ann_' . $checkoutRequestId);
            
            if ($announcementId) {
                $announcement = Announcement::find($announcementId);
                if ($announcement && $announcement->payment_status !== 'paid') {
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

                    // Log System Alert Inbox Message
                    ContactMessage::create([
                        'name' => 'System Alert',
                        'email' => 'announcements@getembenews.com',
                        'subject' => 'Announcement Paid via Webhook (Ref: ' . $ref . ')',
                        'message' => "Announcement ID: {$announcement->id} has been paid via Safaricom Webhook Callback. Amount: KSh {$announcement->total_amount}."
                    ]);

                    // Send SMS Notifications
                    \App\Support\Sms::sendAdminPaymentNotification($announcement, $ref);
                    
                    Log::info("Announcement ID {$announcement->id} successfully paid via webhook. Ref: {$ref}");
                }
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}

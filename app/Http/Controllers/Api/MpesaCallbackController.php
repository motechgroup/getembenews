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
            // Find announcement mapped to this CheckoutRequestID
            $announcementId = Cache::get('mpesa_ann_' . $checkoutRequestId);
            
            if ($announcementId) {
                $announcement = Announcement::find($announcementId);
                if ($announcement && $announcement->payment_status !== 'paid') {
                    
                    // Parse Safaricom metadata for receipt / Reference
                    $ref = 'MPESA-CB-' . \Illuminate\Support\Str::random(10);
                    $metadata = $callbackData['CallbackMetadata']['Item'] ?? [];
                    foreach ($metadata as $item) {
                        if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                            $ref = $item['Value'];
                            break;
                        }
                    }

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

<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Mpesa
{
    /**
     * Generate access token from Safaricom API.
     */
    public static function getAccessToken(): ?string
    {
        $env = Setting::get('mpesa_env', 'sandbox');
        $consumerKey = trim((string) Setting::get('mpesa_consumer_key', ''));
        $consumerSecret = trim((string) Setting::get('mpesa_consumer_secret', ''));

        if (empty($consumerKey) || empty($consumerSecret)) {
            Log::error("M-Pesa API Consumer Key or Consumer Secret is missing.");
            return null;
        }

        $url = $env === 'production'
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        try {
            $response = Http::withoutVerifying()->withBasicAuth($consumerKey, $consumerSecret)->get($url);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            $errorMsg = $response->json('errorMessage') ?: $response->body();
            Log::error("Failed to generate M-Pesa token [Env: {$env}]. Response: " . $errorMsg);
        } catch (\Exception $e) {
            Log::error("M-Pesa Token Exception: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Trigger STK Push.
     * Returns array with status and message/checkout_id.
     */
    public static function stkPush(string $phone, float $amount, string $reference = 'GetembeNews'): array
    {
        $env = Setting::get('mpesa_env', 'sandbox');
        $shortcode = trim((string) Setting::get('mpesa_shortcode', '4346209'));
        $passkey = trim((string) Setting::get('mpesa_passkey', 'cc2b215ee738ab18e254db64058cfa06236f72cce95a8cf5a03f48fb14b2c9fe'));
        $txType = Setting::get('mpesa_transaction_type', 'CustomerPayBillOnline');

        // Clean phone number: Safaricom requires format 2547XXXXXXXX
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }
        if (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }

        $token = self::getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => "Authentication Failed (Wrong Credentials): Please verify your Live Consumer Key & Secret in Admin Settings."
            ];
        }

        $url = $env === 'production'
            ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);
        
        // Ensure amount is integer or positive float
        $amount = (int) max(1, round($amount));

        $callbackUrl = self::getCallbackUrl();

        $body = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $txType,
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => substr(preg_replace('/[^A-Za-z0-9]/', '', $reference), 0, 12) ?: 'Getembe',
            'TransactionDesc' => 'Announcement Payment'
        ];

        try {
            $response = Http::withoutVerifying()->withToken($token)->post($url, $body);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                    return [
                        'success' => true,
                        'checkout_request_id' => $data['CheckoutRequestID'],
                        'message' => 'STK Push prompt successfully sent to ' . $phone
                    ];
                }
            }

            // Retry with alternative transaction type if initial transaction type failed (e.g. Paybill vs Buy Goods)
            $altTxType = ($txType === 'CustomerPayBillOnline') ? 'CustomerBuyGoodsOnline' : 'CustomerPayBillOnline';
            $body['TransactionType'] = $altTxType;

            $retryResponse = Http::withoutVerifying()->withToken($token)->post($url, $body);
            if ($retryResponse->successful()) {
                $retryData = $retryResponse->json();
                if (isset($retryData['ResponseCode']) && $retryData['ResponseCode'] === '0') {
                    Setting::set('mpesa_transaction_type', $altTxType);
                    return [
                        'success' => true,
                        'checkout_request_id' => $retryData['CheckoutRequestID'],
                        'message' => 'STK Push prompt successfully sent to ' . $phone
                    ];
                }
            }

            Log::error("M-Pesa STK Push error. Request: " . json_encode($body) . " Response: " . $response->body());
            $errorMsg = $response->json('errorMessage') ?: ($response->json('ResponseDescription') ?: 'Error calling M-Pesa STK API. HTTP code ' . $response->status());
            
            if (str_contains(strtolower($errorMsg), 'wrong credentials') || str_contains(strtolower($errorMsg), 'invalid access token')) {
                $errorMsg = "STK Push Authorization Failed (Wrong Credentials / Invalid Passkey or Shortcode): Your OAuth Consumer Key is valid, but Safaricom rejected the Passkey or Shortcode combination ({$shortcode}). Please verify your Lipa Na M-Pesa Passkey in Admin Settings.";
            }

            return [
                'success' => false,
                'message' => $errorMsg
            ];
        } catch (\Exception $e) {
            Log::error("M-Pesa STK Push exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query status of an STK Push.
     */
    public static function queryStatus(string $checkoutRequestId): array
    {
        $env = Setting::get('mpesa_env', 'sandbox');
        $shortcode = trim((string) Setting::get('mpesa_shortcode', '4346209'));
        $passkey = trim((string) Setting::get('mpesa_passkey', 'cc2b215ee738ab18e254db64058cfa06236f72cce95a8cf5a03f48fb14b2c9fe'));

        $token = self::getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Authentication failed. Wrong M-Pesa credentials.'
            ];
        }

        $url = $env === 'production'
            ? 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        $body = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId
        ];

        try {
            $response = Http::withoutVerifying()->withToken($token)->post($url, $body);

            if ($response->successful()) {
                $data = $response->json();
                
                // ResultCode 0 means Success
                if (isset($data['ResultCode'])) {
                    $code = (int) $data['ResultCode'];
                    if ($code === 0) {
                        return [
                            'success' => true,
                            'status' => 'success',
                            'message' => $data['ResultDesc'] ?? 'Payment completed successfully.'
                        ];
                    }
                    return [
                        'success' => false,
                        'status' => 'failed',
                        'message' => $data['ResultDesc'] ?? 'Payment failed.'
                    ];
                }

                // If ResultCode is not set but ResponseCode/ResponseDescription is set (e.g. still processing)
                if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                    return [
                        'success' => false,
                        'status' => 'pending',
                        'message' => $data['ResponseDescription'] ?? 'Awaiting prompt input.'
                    ];
                }
            }

            Log::error("M-Pesa STK Query error. Response: " . $response->body());
            $errorMsg = $response->json('errorMessage') ?: ($response->json('ResultDesc') ?: 'Awaiting payment confirmation.');
            
            if (str_contains(strtolower($errorMsg), 'wrong credentials')) {
                $errorMsg = "Authentication Failed: Wrong credentials for {$env} mode.";
            }

            return [
                'success' => false,
                'status' => 'pending',
                'message' => $errorMsg
            ];
        } catch (\Exception $e) {
            Log::error("M-Pesa STK Query exception: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Resolve the best Webhook Callback URL for M-Pesa.
     */
    public static function getCallbackUrl(): string
    {
        // 1. Check custom settings URL
        $customUrl = Setting::get('mpesa_callback_url', '');
        if (!empty($customUrl)) {
            return rtrim($customUrl, '/');
        }

        // 2. Return current dynamic request URL (including localhost)
        $currentUrl = url('/api/v1/payments/mpesa/callback');

        if (str_contains($currentUrl, '127.0.0.1') || str_contains($currentUrl, 'localhost') || !str_starts_with($currentUrl, 'https')) {
            return 'https://localhost/api/v1/payments/mpesa/callback';
        }

        return $currentUrl;
    }
}

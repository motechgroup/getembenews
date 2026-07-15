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
        $consumerKey = Setting::get('mpesa_consumer_key', '');
        $consumerSecret = Setting::get('mpesa_consumer_secret', '');

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

            Log::error("Failed to generate M-Pesa token. Response: " . $response->body());
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
        $shortcode = Setting::get('mpesa_shortcode', '174379');
        $passkey = Setting::get('mpesa_passkey', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');

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
                'message' => 'Authentication failed. Please verify Consumer Key & Consumer Secret settings.'
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
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => substr($reference, 0, 12),
            'TransactionDesc' => 'Announcement Purchase'
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
                return [
                    'success' => false,
                    'message' => $data['ResponseDescription'] ?? 'Failed to trigger payment prompt.'
                ];
            }

            Log::error("M-Pesa STK Push error. Request: " . json_encode($body) . " Response: " . $response->body());
            return [
                'success' => false,
                'message' => $response->json('errorMessage') ?: 'Error calling M-Pesa STK API. HTTP code ' . $response->status()
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
        $shortcode = Setting::get('mpesa_shortcode', '174379');
        $passkey = Setting::get('mpesa_passkey', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');

        $token = self::getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Authentication failed.'
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
            return [
                'success' => false,
                'status' => 'pending',
                'message' => $response->json('errorMessage') ?: 'Awaiting payment confirmation.'
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

        // 2. Localhost auto-detection logic
        $currentUrl = url('/api/v1/payments/mpesa/callback');
        if (str_contains($currentUrl, '127.0.0.1') || str_contains($currentUrl, 'localhost') || !str_starts_with($currentUrl, 'https')) {
            try {
                // Look for running local Ngrok tunnel API
                $ngrokResponse = Http::timeout(1)->get('http://127.0.0.1:4040/api/tunnels');
                if ($ngrokResponse->successful()) {
                    $tunnels = $ngrokResponse->json('tunnels') ?: [];
                    foreach ($tunnels as $tunnel) {
                        if (($tunnel['proto'] ?? '') === 'https' && !empty($tunnel['public_url'])) {
                            return rtrim($tunnel['public_url'], '/') . '/api/v1/payments/mpesa/callback';
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ngrok not active
            }

            // Dummy public HTTPS fallback for sandbox validation bypass
            return 'https://getembenews.com/api/v1/payments/mpesa/callback';
        }

        return $currentUrl;
    }
}

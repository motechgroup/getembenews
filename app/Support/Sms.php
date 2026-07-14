<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Log;

class Sms
{
    /**
     * Send an SMS message using the configured provider.
     */
    public static function send(string $to, string $message): bool
    {
        $enabled = (bool) Setting::get('sms_notifications_enabled', false);
        if (!$enabled) {
            Log::info("SMS sending disabled. Recipient: {$to}, Message: {$message}");
            return false;
        }

        $provider = Setting::get('sms_provider', 'mock');

        switch ($provider) {
            case 'twilio':
                return self::sendTwilio($to, $message);
            case 'africastalking':
                return self::sendAfricasTalking($to, $message);
            case 'textsms':
                return self::sendTextSms($to, $message);
            case 'mock':
            default:
                return self::sendMock($to, $message);
        }
    }

    /**
     * Send an SMS alert to all configured admin recipient numbers.
     */
    public static function sendAdminNotification(string $message): void
    {
        $adminPhones = Setting::get('sms_admin_phone', '');
        if (empty($adminPhones)) {
            Log::warning("SMS Alert triggered but no admin recipient phone number configured.");
            return;
        }

        $numbers = array_filter(array_map('trim', explode(',', $adminPhones)));
        foreach ($numbers as $number) {
            self::send($number, $message);
        }
    }

    /**
     * Twilio Gateway Integration.
     */
    protected static function sendTwilio(string $to, string $message): bool
    {
        $sid = Setting::get('sms_twilio_sid');
        $token = Setting::get('sms_twilio_token');
        $from = Setting::get('sms_twilio_from');

        if (empty($sid) || empty($token) || empty($from)) {
            Log::error("Twilio credentials missing. Falling back to Mock.");
            return self::sendMock($to, $message . " [Twilio Config Error]");
        }

        try {
            // Real integration using file_get_contents/curl to avoid composer dependency issues
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
            $data = [
                'To' => $to,
                'From' => $from,
                'Body' => $message,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_USERPWD, "{$sid}:{$token}");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info("SMS successfully sent via Twilio to {$to}");
                return true;
            }

            Log::error("Twilio API failed with code {$httpCode}: {$response}");
            return self::sendMock($to, $message . " [Twilio Gateway Failure]");
        } catch (\Exception $e) {
            Log::error("Exception occurred sending Twilio SMS: " . $e->getMessage());
            return self::sendMock($to, $message . " [Twilio Exception]");
        }
    }

    /**
     * Africa's Talking Gateway Integration.
     */
    protected static function sendAfricasTalking(string $to, string $message): bool
    {
        $username = Setting::get('sms_at_username');
        $apiKey = Setting::get('sms_at_api_key');
        $from = Setting::get('sms_at_from'); // optional sender id

        if (empty($username) || empty($apiKey)) {
            Log::error("Africa's Talking credentials missing. Falling back to Mock.");
            return self::sendMock($to, $message . " [Africa's Talking Config Error]");
        }

        try {
            $url = "https://api.africastalking.com/version1/messaging";
            $data = [
                'username' => $username,
                'to' => $to,
                'message' => $message,
            ];
            if (!empty($from)) {
                $data['from'] = $from;
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "apikey: {$apiKey}",
                "Accept: application/json",
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info("SMS successfully sent via Africa's Talking to {$to}");
                return true;
            }

            Log::error("Africa's Talking API failed with code {$httpCode}: {$response}");
            return self::sendMock($to, $message . " [Africa's Talking Gateway Failure]");
        } catch (\Exception $e) {
            Log::error("Exception occurred sending Africa's Talking SMS: " . $e->getMessage());
            return self::sendMock($to, $message . " [Africa's Talking Exception]");
        }
    }

    /**
     * TextSMS Kenya Gateway Integration.
     */
    protected static function sendTextSms(string $to, string $message): bool
    {
        $apiKey = Setting::get('sms_textsms_api_key');
        $partnerId = Setting::get('sms_textsms_partner_id');
        $shortcode = Setting::get('sms_textsms_shortcode'); // sender id

        if (empty($apiKey) || empty($partnerId) || empty($shortcode)) {
            Log::error("TextSMS Kenya credentials missing. Falling back to Mock.");
            return self::sendMock($to, $message . " [TextSMS Config Error]");
        }

        try {
            $url = "https://sms.textsms.co.ke/api/services/sendsms/";
            $data = [
                'apikey'    => $apiKey,
                'partnerID' => $partnerId,
                'message'   => $message,
                'shortcode' => $shortcode,
                'mobile'    => $to,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Accept: application/json",
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info("SMS successfully sent via TextSMS to {$to}");
                return true;
            }

            Log::error("TextSMS API failed with code {$httpCode}: {$response}");
            return self::sendMock($to, $message . " [TextSMS Gateway Failure]");
        } catch (\Exception $e) {
            Log::error("Exception occurred sending TextSMS: " . $e->getMessage());
            return self::sendMock($to, $message . " [TextSMS Exception]");
        }
    }

    /**
     * Mock Gateway Integration (Logs + ContactMessage)
     */
    protected static function sendMock(string $to, string $message): bool
    {
        Log::info("SMS [MOCK-SEND] to {$to}: {$message}");

        ContactMessage::create([
            'name' => 'SMS Gateway (Simulated)',
            'email' => 'sms-gateway@getembenews.com',
            'subject' => "Admin SMS Notification (Recipient: {$to})",
            'message' => "Message:\n\"{$message}\""
        ]);

        return true;
    }
}

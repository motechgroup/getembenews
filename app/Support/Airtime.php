<?php

namespace App\Support;

use App\Models\AirtimeTopup;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Airtime
{
    /**
     * Standardize Kenyan / International phone number to E.164 format (+2547XXXXXXXX or +2541XXXXXXXX).
     */
    public static function formatE164(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', trim($phone));
        if (empty($digits)) {
            return null;
        }

        // 07XXXXXXXX or 01XXXXXXXX -> +2547XXXXXXXX / +2541XXXXXXXX
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '+254' . substr($digits, 1);
        }

        // 7XXXXXXXX or 1XXXXXXXX (9 digits without leading 0)
        if ((str_starts_with($digits, '7') || str_starts_with($digits, '1')) && strlen($digits) === 9) {
            return '+254' . $digits;
        }

        // 2547XXXXXXXX or 2541XXXXXXXX (12 digits)
        if (str_starts_with($digits, '254') && strlen($digits) === 12) {
            return '+' . $digits;
        }

        // Already has +
        if (str_starts_with(trim($phone), '+')) {
            return '+' . $digits;
        }

        // Fallback for Kenya if 9-10 digits
        if (strlen($digits) === 9) {
            return '+254' . $digits;
        }

        return '+' . $digits;
    }

    /**
     * Format phone to local 10-digit format (07XXXXXXXX or 01XXXXXXXX) for standard Kenyan display.
     */
    public static function formatLocal(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', trim($phone));
        if (str_starts_with($digits, '254') && strlen($digits) === 12) {
            return '0' . substr($digits, 3);
        }
        if (strlen($digits) === 9 && (str_starts_with($digits, '7') || str_starts_with($digits, '1'))) {
            return '0' . $digits;
        }
        return $digits;
    }

    /**
     * Get Africa's Talking Credentials & Configuration.
     */
    public static function getCredentials(): array
    {
        $username = trim((string) (Setting::get('airtime_at_username') ?: Setting::get('sms_at_username', '')));
        $apiKey = trim((string) (Setting::get('airtime_at_api_key') ?: Setting::get('sms_at_api_key', '')));
        $isSandbox = (bool) Setting::get('airtime_sandbox', false) || strtolower($username) === 'sandbox';
        $mode = Setting::get('airtime_mode', 'live'); // 'live', 'sandbox', 'mock'

        if (empty($username) || empty($apiKey)) {
            $mode = 'mock';
        }

        return [
            'username' => $username,
            'apiKey' => $apiKey,
            'isSandbox' => $isSandbox,
            'mode' => $mode,
        ];
    }

    /**
     * Dispatch Airtime Top-Up to a single recipient.
     */
    public static function send(string $phone, float $amount, ?User $user = null, ?int $adminId = null): array
    {
        $e164 = self::formatE164($phone);
        $localPhone = self::formatLocal($phone);

        if (!$e164 || strlen(preg_replace('/[^0-9]/', '', $e164)) < 10) {
            return [
                'success' => false,
                'message' => "Invalid phone number format: '{$phone}'. Please provide a valid 10-digit Kenyan phone number (e.g. 0712345678 or 0112345678)."
            ];
        }

        // Enforce minimum airtime in Kenya (KSh 5 or 10 minimum depending on network)
        $amount = (float) max(5.0, round($amount, 2));

        // If user was not passed, resolve user from phone number if registered
        if (!$user) {
            $user = User::where('phone', $localPhone)
                ->orWhere('phone', $e164)
                ->orWhere('mpesa_phone', $localPhone)
                ->orWhere('mpesa_phone', $e164)
                ->first();
        }

        // Create pending top-up audit record
        $topup = AirtimeTopup::create([
            'user_id' => $user?->id,
            'phone' => $localPhone ?: $phone,
            'phone_e164' => $e164,
            'amount' => $amount,
            'currency' => 'KES',
            'status' => 'pending',
            'provider' => 'africastalking',
            'sent_by' => $adminId,
        ]);

        $creds = self::getCredentials();

        // 1. Mock Mode (used for local testing or before credentials are entered in Admin Settings)
        if ($creds['mode'] === 'mock' || empty($creds['username']) || empty($creds['apiKey'])) {
            $mockRequestId = 'ATQid_MOCK_' . strtoupper(bin2hex(random_bytes(4)));
            $mockDiscount = round($amount * 0.04, 2); // 4% simulated discount

            $topup->update([
                'status' => 'sent',
                'request_id' => $mockRequestId,
                'discount' => $mockDiscount,
                'sent_at' => now(),
                'raw_response' => [
                    'simulation' => true,
                    'note' => 'Airtime sent via Africa\'s Talking Simulated Gateway (Mock Mode). Enter live Africa\'s Talking API credentials in Admin Settings to disburse real carrier credit.',
                    'status' => 'Sent',
                    'amount' => "KES {$amount}",
                    'requestId' => $mockRequestId
                ]
            ]);

            Log::info("Airtime Top-Up [MOCK]: KSh {$amount} sent to {$e164}. Ref: {$mockRequestId}");

            return [
                'success' => true,
                'message' => "Airtime of KSh {$amount} successfully simulated for {$localPhone} (Mock Mode).",
                'topup' => $topup,
                'is_mock' => true
            ];
        }

        // 2. Real Africa's Talking API Call
        $endpoint = ($creds['isSandbox'] || $creds['mode'] === 'sandbox')
            ? 'https://api.sandbox.africastalking.com/version1/airtime/send'
            : 'https://api.africastalking.com/version1/airtime/send';

        $payload = [
            'username' => $creds['username'],
            'recipients' => [
                [
                    'phoneNumber' => $e164,
                    'amount' => 'KES ' . number_format($amount, 2, '.', '')
                ]
            ]
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(20)
                ->withHeaders([
                    'apiKey' => $creds['apiKey'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            $data = $response->json();
            $statusCode = $response->status();

            if ($response->successful() && is_array($data)) {
                $recipientResponse = $data['responses'][0] ?? null;

                if ($recipientResponse) {
                    $status = strtolower($recipientResponse['status'] ?? '');
                    $discountStr = $recipientResponse['discount'] ?? '0';
                    $discountVal = (float) preg_replace('/[^0-9.]/', '', $discountStr);

                    if (in_array($status, ['sent', 'success'])) {
                        $topup->update([
                            'status' => 'sent',
                            'request_id' => $recipientResponse['requestId'] ?? null,
                            'discount' => $discountVal,
                            'sent_at' => now(),
                            'raw_response' => $data,
                        ]);

                        Log::info("Airtime Top-Up [SUCCESS]: KSh {$amount} sent to {$e164}. AT Request ID: " . ($recipientResponse['requestId'] ?? 'N/A'));

                        return [
                            'success' => true,
                            'message' => "Airtime of KSh {$amount} successfully sent to {$localPhone}.",
                            'topup' => $topup
                        ];
                    } else {
                        // Africa's Talking returned a failure status for this recipient (e.g. InsufficientBalance, InvalidPhoneNumber)
                        $errorMsg = $recipientResponse['errorMessage'] ?? $recipientResponse['status'] ?? 'Provider error';
                        $topup->update([
                            'status' => 'failed',
                            'error_message' => $errorMsg,
                            'raw_response' => $data,
                        ]);

                        Log::error("Airtime Top-Up [FAILED]: {$e164} - {$errorMsg}");

                        return [
                            'success' => false,
                            'message' => "Africa's Talking returned an error: {$errorMsg}",
                            'topup' => $topup
                        ];
                    }
                }
            }

            // HTTP error or unparseable response
            $errorMsg = $data['errorMessage'] ?? $data['message'] ?? ("HTTP " . $statusCode . ": " . $response->body());
            $topup->update([
                'status' => 'failed',
                'error_message' => $errorMsg,
                'raw_response' => is_array($data) ? $data : ['body' => $response->body()],
            ]);

            Log::error("Airtime Top-Up HTTP Error: {$errorMsg}");

            return [
                'success' => false,
                'message' => "Failed to dispatch airtime. {$errorMsg}",
                'topup' => $topup
            ];
        } catch (\Throwable $e) {
            $topup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Airtime Top-Up Exception: " . $e->getMessage());

            return [
                'success' => false,
                'message' => "Exception while calling Africa's Talking API: " . $e->getMessage(),
                'topup' => $topup
            ];
        }
    }

    /**
     * Dispatch Airtime Top-Up in batch/bulk to multiple users.
     * @param array $items Array of ['phone' => string, 'amount' => float, 'user_id' => ?int]
     */
    public static function sendBatch(array $items, ?int $adminId = null): array
    {
        if (empty($items)) {
            return ['success' => false, 'message' => 'No recipients provided.'];
        }

        $creds = self::getCredentials();
        $results = [
            'total' => count($items),
            'sent' => 0,
            'failed' => 0,
            'details' => []
        ];

        // Format and create pending records
        $recipientsPayload = [];
        $topupRecords = [];

        foreach ($items as $idx => $item) {
            $phone = $item['phone'] ?? '';
            $amount = (float) max(5.0, round($item['amount'] ?? 10.0, 2));
            $e164 = self::formatE164($phone);
            $localPhone = self::formatLocal($phone);

            if (!$e164) {
                $results['failed']++;
                $results['details'][] = ['phone' => $phone, 'status' => 'failed', 'message' => 'Invalid phone number format'];
                continue;
            }

            $topup = AirtimeTopup::create([
                'user_id' => $item['user_id'] ?? null,
                'phone' => $localPhone ?: $phone,
                'phone_e164' => $e164,
                'amount' => $amount,
                'currency' => 'KES',
                'status' => 'pending',
                'provider' => 'africastalking',
                'sent_by' => $adminId,
            ]);

            $topupRecords[$e164] = $topup;
            $recipientsPayload[] = [
                'phoneNumber' => $e164,
                'amount' => 'KES ' . number_format($amount, 2, '.', '')
            ];
        }

        if (empty($recipientsPayload)) {
            return ['success' => false, 'message' => 'All recipient phone numbers were invalid.'];
        }

        // Mock / Simulation Mode
        if ($creds['mode'] === 'mock' || empty($creds['username']) || empty($creds['apiKey'])) {
            foreach ($topupRecords as $e164 => $record) {
                $mockId = 'ATQid_MOCK_' . strtoupper(bin2hex(random_bytes(4)));
                $record->update([
                    'status' => 'sent',
                    'request_id' => $mockId,
                    'discount' => round($record->amount * 0.04, 2),
                    'sent_at' => now(),
                    'raw_response' => ['simulation' => true, 'status' => 'Sent']
                ]);
                $results['sent']++;
                $results['details'][] = ['phone' => $e164, 'status' => 'sent', 'requestId' => $mockId];
            }

            return [
                'success' => true,
                'message' => "Successfully simulated airtime for {$results['sent']} recipients (Mock Mode).",
                'results' => $results
            ];
        }

        // Real Batch API Call
        $endpoint = ($creds['isSandbox'] || $creds['mode'] === 'sandbox')
            ? 'https://api.sandbox.africastalking.com/version1/airtime/send'
            : 'https://api.africastalking.com/version1/airtime/send';

        $payload = [
            'username' => $creds['username'],
            'recipients' => $recipientsPayload
        ];

        try {
            $response = Http::withoutVerifying()
                ->timeout(45)
                ->withHeaders([
                    'apiKey' => $creds['apiKey'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            $data = $response->json();

            if ($response->successful() && is_array($data) && isset($data['responses'])) {
                foreach ($data['responses'] as $resp) {
                    $pNumber = $resp['phoneNumber'] ?? '';
                    $record = $topupRecords[$pNumber] ?? null;

                    if ($record) {
                        $status = strtolower($resp['status'] ?? '');
                        $discountVal = (float) preg_replace('/[^0-9.]/', '', $resp['discount'] ?? '0');

                        if (in_array($status, ['sent', 'success'])) {
                            $record->update([
                                'status' => 'sent',
                                'request_id' => $resp['requestId'] ?? null,
                                'discount' => $discountVal,
                                'sent_at' => now(),
                                'raw_response' => $resp,
                            ]);
                            $results['sent']++;
                            $results['details'][] = ['phone' => $pNumber, 'status' => 'sent', 'requestId' => $resp['requestId'] ?? ''];
                        } else {
                            $err = $resp['errorMessage'] ?? $resp['status'] ?? 'Failed';
                            $record->update([
                                'status' => 'failed',
                                'error_message' => $err,
                                'raw_response' => $resp,
                            ]);
                            $results['failed']++;
                            $results['details'][] = ['phone' => $pNumber, 'status' => 'failed', 'message' => $err];
                        }
                    }
                }

                return [
                    'success' => $results['sent'] > 0,
                    'message' => "Airtime batch processed: {$results['sent']} succeeded, {$results['failed']} failed.",
                    'results' => $results
                ];
            }

            // Entire batch request rejected
            $err = $data['errorMessage'] ?? 'Batch failed';
            foreach ($topupRecords as $record) {
                $record->update(['status' => 'failed', 'error_message' => $err]);
            }

            return ['success' => false, 'message' => "Batch API request failed: {$err}"];
        } catch (\Throwable $e) {
            foreach ($topupRecords as $record) {
                $record->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            }
            return ['success' => false, 'message' => 'Exception during batch airtime dispatch: ' . $e->getMessage()];
        }
    }

    /**
     * Trigger welcome airtime reward on user signup if enabled in settings.
     */
    public static function triggerSignupWelcomeAirtime(User $user): ?array
    {
        $enabled = (bool) Setting::get('airtime_auto_on_signup', false);
        if (!$enabled) {
            return null;
        }

        $phone = $user->phone ?: $user->mpesa_phone;
        if (empty($phone)) {
            return null;
        }

        // Ensure user hasn't already received airtime (prevent abuse/duplicate bonuses)
        if ($user->hasReceivedAirtime()) {
            Log::info("User #{$user->id} already received signup airtime bonus.");
            return null;
        }

        $amount = (float) Setting::get('airtime_auto_amount', 10);
        return self::send($phone, $amount, $user, null);
    }
}

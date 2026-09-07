<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Dispute;
use App\Models\Setting;
use App\Support\Mpesa;
use App\Support\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AgentApiController extends Controller
{
    /**
     * Authenticate an agent using their 4-digit PIN (and optional phone number).
     */
    public function login(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4',
            'phone' => 'nullable|string',
        ]);

        $pin = trim($request->pin);
        $query = Agent::where('pin', $pin);

        if ($request->filled('phone')) {
            $phone = preg_replace('/\D/', '', $request->phone);
            if (!empty($phone)) {
                $query->where(function ($q) use ($phone) {
                    $q->where('phone', 'like', "%{$phone}%");
                });
            }
        }

        $agent = $query->first();

        // Fallback: If phone provided and PIN didn't match directly, try by phone
        if (!$agent && $request->filled('phone')) {
            $phone = preg_replace('/\D/', '', $request->phone);
            $agentByPhone = Agent::where('phone', 'like', "%{$phone}%")->first();
            if ($agentByPhone && $agentByPhone->pin === $pin) {
                $agent = $agentByPhone;
            }
        }

        if (!$agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Agent PIN code. Please verify your PIN and try again.'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Agent login successful.',
            'data' => [
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'business_name' => $agent->business_name,
                    'phone' => $agent->phone,
                    'location' => $agent->location,
                    'commission_percentage' => $agent->commission_percentage,
                    'pin' => $agent->pin,
                    'total_announcements' => $agent->total_announcements,
                    'total_revenue' => $agent->total_revenue,
                    'total_commission' => $agent->total_commission,
                    'total_payouts' => $agent->total_payouts,
                    'commission_balance' => $agent->commission_balance,
                ],
                'rates' => [
                    'tv' => (int) Setting::get('announcement_rate_tv', 5),
                    'radio' => (int) Setting::get('announcement_rate_radio', 3),
                    'both' => (int) Setting::get('announcement_rate_both', 7),
                ]
            ]
        ]);
    }

    /**
     * Retrieve authenticated Agent profile and dynamic system rates.
     */
    public function profile(Request $request)
    {
        $agentId = $request->input('agent_id');
        $pin = $request->input('pin');

        $agent = null;
        if ($agentId) {
            $agent = Agent::find($agentId);
        } elseif ($pin) {
            $agent = Agent::where('pin', $pin)->first();
        }

        if (!$agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent account not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'business_name' => $agent->business_name,
                    'phone' => $agent->phone,
                    'location' => $agent->location,
                    'commission_percentage' => $agent->commission_percentage,
                    'pin' => $agent->pin,
                    'total_announcements' => $agent->total_announcements,
                    'total_revenue' => $agent->total_revenue,
                    'total_commission' => $agent->total_commission,
                    'total_payouts' => $agent->total_payouts,
                    'commission_balance' => $agent->commission_balance,
                ],
                'rates' => [
                    'tv' => (int) Setting::get('announcement_rate_tv', 5),
                    'radio' => (int) Setting::get('announcement_rate_radio', 3),
                    'both' => (int) Setting::get('announcement_rate_both', 7),
                ]
            ]
        ]);
    }

    /**
     * Regenerate 4-digit PIN for an agent.
     */
    public function regeneratePin(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|integer|exists:agents,id',
        ]);

        $agent = Agent::findOrFail($request->agent_id);
        $newPin = Agent::generateUniquePin();
        $agent->update(['pin' => $newPin]);

        return response()->json([
            'status' => 'success',
            'message' => "Security PIN regenerated successfully: {$newPin}",
            'data' => [
                'pin' => $newPin,
                'agent' => $agent->fresh()
            ]
        ]);
    }

    /**
     * Retrieve announcements submitted by the agent.
     */
    public function announcements(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|integer|exists:agents,id',
        ]);

        $query = Announcement::where('agent_id', $request->agent_id)->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('payment_status', 'paid');
            } elseif ($request->status === 'pending') {
                $query->where('payment_status', 'pending');
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('visitor_name', 'like', "%{$search}%")
                  ->orWhere('visitor_phone', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $announcements
        ]);
    }

    /**
     * Submit an announcement on behalf of a client by the agent.
     */
    public function submitAnnouncement(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|integer|exists:agents,id',
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'required|string|max:20',
            'type' => 'required|in:funeral,general',
            'media' => 'required|in:tv,radio,both',
            'content' => 'required|string|min:5',
            'days_count' => 'required|integer|min:1|max:30',
            'airing_date' => 'required|date',
        ]);

        $agent = Agent::findOrFail($request->agent_id);

        // Require visual image for TV or Both broadcasts
        if (($request->media === 'tv' || $request->media === 'both') &&
            !$request->hasFile('visual_image') &&
            !$request->hasFile('image') &&
            !$request->filled('image_url') &&
            !$request->filled('visual_image')) {
            return response()->json([
                'status' => 'error',
                'message' => 'On-Air Visual Image Required: TV & Radio announcements require an image/photo to be displayed on screen during broadcast.'
            ], 422);
        }

        // Process images
        $imagesList = [];
        if ($request->hasFile('visual_image')) {
            $path = $request->file('visual_image')->store('announcements', 'public');
            $imagesList[] = asset('storage/' . $path);
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $imagesList[] = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagesList[] = $request->input('image_url');
        }

        // Calculate rate based on media selection
        $rate = 5;
        if ($request->media === 'tv') {
            $rate = (int) Setting::get('announcement_rate_tv', 5);
        } elseif ($request->media === 'radio') {
            $rate = (int) Setting::get('announcement_rate_radio', 3);
        } else {
            $rate = (int) Setting::get('announcement_rate_both', 7);
        }

        // Count words
        $content = strip_tags(trim($request->content));
        $wordCount = count(array_filter(explode(' ', preg_replace('/\s+/', ' ', trim($content)))));
        $totalAmount = $wordCount * $rate * (int) $request->days_count;
        $commissionAmount = (int) round(($totalAmount * $agent->commission_percentage) / 100);

        $announcement = Announcement::create([
            'agent_id' => $agent->id,
            'visitor_name' => strip_tags(trim($request->visitor_name)),
            'visitor_email' => $request->visitor_email ? strip_tags(trim(strtolower($request->visitor_email))) : null,
            'visitor_phone' => strip_tags(trim($request->visitor_phone)),
            'type' => $request->type,
            'media' => $request->media,
            'content' => $content,
            'airing_date' => $request->airing_date,
            'images' => !empty($imagesList) ? $imagesList : null,
            'word_count' => $wordCount,
            'days_count' => (int) $request->days_count,
            'rate_per_word' => $rate,
            'total_amount' => $totalAmount,
            'commission_amount' => $commissionAmount,
            'payment_status' => 'pending',
            'is_approved' => false,
        ]);

        // Create log notification
        ContactMessage::create([
            'name' => 'Agent Alert',
            'email' => 'announcements@getembenews.com',
            'subject' => "Agent Announcement Drafted ({$agent->name})",
            'message' => "Agent {$agent->name} ({$agent->phone}) drafted announcement #{$announcement->id} for {$request->visitor_name} ({$request->visitor_phone}). Amount: KSh {$totalAmount}. Estimated Commission: KSh {$commissionAmount}."
        ]);

        Sms::sendAdminDraftNotification($announcement);

        return response()->json([
            'status' => 'success',
            'message' => 'Announcement created successfully.',
            'data' => $announcement
        ], 201);
    }

    /**
     * Trigger Safaricom M-Pesa STK push for announcement payment.
     */
    public function payAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $phone = $request->input('phone', $announcement->visitor_phone);
        $amount = $announcement->total_amount;
        $reference = 'ANN-' . $announcement->id;

        $stkResult = Mpesa::stkPush($phone, $amount, $reference);

        if ($stkResult['success']) {
            $checkoutRequestId = $stkResult['checkout_request_id'];
            \Illuminate\Support\Facades\Cache::put('mpesa_ann_' . $checkoutRequestId, $announcement->id, 3600);
            \Illuminate\Support\Facades\Cache::put('mpesa_last_checkout_' . $announcement->id, $checkoutRequestId, 3600);

            return response()->json([
                'status' => 'success',
                'mode' => 'stk_push',
                'checkout_request_id' => $checkoutRequestId,
                'message' => "M-Pesa STK Push prompt sent to {$phone}. Please enter M-Pesa PIN on handset.",
                'data' => $announcement
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $stkResult['message'] ?? 'Failed to trigger M-Pesa STK Push prompt.'
        ], 400);
    }

    /**
     * Retrieve agent earnings, payouts, and balance ledger.
     */
    public function earnings(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|integer|exists:agents,id',
        ]);

        $agent = Agent::findOrFail($request->agent_id);
        $payouts = $agent->payouts()->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'agent_id' => $agent->id,
                    'agent_name' => $agent->name,
                    'business_name' => $agent->business_name,
                    'commission_percentage' => $agent->commission_percentage,
                    'total_announcements' => $agent->total_announcements,
                    'total_revenue' => $agent->total_revenue,
                    'total_commission' => $agent->total_commission,
                    'total_payouts' => $agent->total_payouts,
                    'commission_balance' => $agent->commission_balance,
                ],
                'payouts' => $payouts
            ]
        ]);
    }

    /**
     * Get list of disputes or file a new support dispute ticket.
     */
    public function disputes(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|integer|exists:agents,id',
        ]);

        if ($request->isMethod('post')) {
            $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string|min:5',
                'announcement_id' => 'nullable|integer|exists:announcements,id',
            ]);

            $dispute = Dispute::create([
                'agent_id' => $request->agent_id,
                'announcement_id' => $request->announcement_id ?: null,
                'subject' => strip_tags(trim($request->subject)),
                'description' => strip_tags(trim($request->description)),
                'status' => 'open',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Dispute ticket submitted successfully. Support team will review it.',
                'data' => $dispute
            ], 201);
        }

        $disputes = Dispute::where('agent_id', $request->agent_id)->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $disputes
        ]);
    }
}

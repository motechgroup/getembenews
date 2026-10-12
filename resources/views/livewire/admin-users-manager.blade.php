<?php

use function Livewire\Volt\{state, with, rules};
use App\Models\User;
use App\Models\AirtimeTopup;
use App\Support\Airtime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

state([
    'search' => '',
    'roleFilter' => '',
    'airtimeFilter' => '', // '', 'received', 'not_received', 'has_phone'
    
    // Form fields
    'userId' => null,
    'name' => '',
    'email' => '',
    'phone' => '',
    'password' => '',
    'role' => 'user',
    
    'isFormOpen' => false,

    // Single Airtime Top-up Modal State
    'isAirtimeModalOpen' => false,
    'airtimeUserId' => null,
    'airtimeTargetName' => '',
    'airtimeTargetPhone' => '',
    'airtimeAmount' => 20,
    'isSendingAirtime' => false,

    // Bulk Airtime Top-up Modal State
    'isBulkAirtimeModalOpen' => false,
    'bulkAirtimeAmount' => 20,
    'bulkTarget' => 'not_received', // 'not_received', 'all_phone'
]);

rules(function () {
    return [
        'name' => 'required|string|max:255',
        'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
        'phone' => ['nullable', 'string', 'regex:/^(07|01)\d{8}$/', Rule::unique('users')->ignore($this->userId)],
        'password' => $this->userId ? 'nullable|string|min:8' : 'required|string|min:8',
        'role' => 'required|string|in:admin,editor,author,user,reporter,contributor,subscriber,manager,writing-article',
    ];
});

$openForm = function ($id = null) {
    $this->resetValidation();
    $this->userId = $id;

    if ($id) {
        $user = User::findOrFail($id);
        $this->name = $user->name;
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? $user->mpesa_phone ?? '';
        $this->role = $user->role;
        $this->password = '';
    } else {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = 'user';
        $this->password = '';
    }

    $this->isFormOpen = true;
};

$closeForm = function () {
    $this->isFormOpen = false;
};

$saveUser = function () {
    // Auto-clean phone input (strip non-numeric characters)
    if ($this->phone) {
        $this->phone = preg_replace('/[^0-9]/', '', trim($this->phone));
    }

    $this->validate();

    if (empty($this->email) && empty($this->phone)) {
        $this->addError('phone', 'Please provide either a 10-digit Kenyan Phone number or Email address.');
        return;
    }

    if ($this->userId) {
        $user = User::findOrFail($this->userId);
        $user->name = $this->name;
        $user->email = $this->email ? strtolower(trim($this->email)) : null;
        $user->phone = $this->phone ? trim($this->phone) : null;
        $user->role = $this->role;
        if ($this->password) {
            $user->password = Hash::make($this->password);
        }
        $user->save();
        session()->flash('message', 'User profile updated successfully.');
    } else {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'phone' => $this->phone ? trim($this->phone) : null,
            'mpesa_phone' => $this->phone ? trim($this->phone) : null,
            'role' => $this->role,
            'password' => Hash::make($this->password),
        ]);

        if (in_array($this->role, ['admin', 'editor', 'author', 'manager', 'writing-article']) && $user->email) {
            \App\Support\Mailer::sendNewAccountNotification($user, $this->password);
        }

        // Trigger welcome airtime if configured
        try {
            \App\Support\Airtime::triggerSignupWelcomeAirtime($user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Signup airtime error: ' . $e->getMessage());
        }

        session()->flash('message', 'User account created successfully.');
    }

    $this->closeForm();
};

$deleteUser = function ($id) {
    if (auth()->id() === $id) {
        session()->flash('error', 'You cannot delete your own account.');
        return;
    }

    User::findOrFail($id)->delete();
    session()->flash('message', 'User account deleted successfully.');
};

$quickChangeRole = function ($id, $newRole) {
    if (auth()->id() == $id && $newRole !== 'admin') {
        session()->flash('error', 'You cannot downgrade your own administrator account role.');
        return;
    }

    $validRoles = ['admin', 'editor', 'author', 'manager', 'user', 'writing-article'];
    if (!in_array($newRole, $validRoles)) {
        session()->flash('error', 'Invalid role selected.');
        return;
    }

    $targetUser = User::findOrFail($id);
    $oldRole = $targetUser->role;
    $targetUser->role = $newRole;
    $targetUser->save();

    session()->flash('message', "Converted {$targetUser->name}'s role from " . ucfirst($oldRole) . " to " . ucfirst($newRole) . " successfully.");
};

// Single Airtime Handlers
$openAirtimeModal = function ($id) {
    $user = User::findOrFail($id);
    $this->airtimeUserId = $user->id;
    $this->airtimeTargetName = $user->name;
    $this->airtimeTargetPhone = $user->phone ?? $user->mpesa_phone ?? '';
    $this->airtimeAmount = (float) \App\Models\Setting::get('airtime_default_amount', 20);
    $this->isAirtimeModalOpen = true;
};

$closeAirtimeModal = function () {
    $this->isAirtimeModalOpen = false;
    $this->airtimeUserId = null;
    $this->airtimeTargetName = '';
    $this->airtimeTargetPhone = '';
};

$sendAirtime = function () {
    if (empty($this->airtimeTargetPhone)) {
        session()->flash('error', 'Recipient does not have a registered phone number.');
        return;
    }

    $amount = (float) $this->airtimeAmount;
    if ($amount < 5) {
        session()->flash('error', 'Minimum airtime amount is KSh 5.00.');
        return;
    }

    $user = $this->airtimeUserId ? User::find($this->airtimeUserId) : null;
    $result = Airtime::send($this->airtimeTargetPhone, $amount, $user, auth()->id());

    if ($result['success']) {
        session()->flash('message', "🎉 " . $result['message']);
        $this->closeAirtimeModal();
    } else {
        session()->flash('error', "⚠️ " . $result['message']);
    }
};

// Bulk Airtime Handlers
$openBulkAirtimeModal = function () {
    $this->bulkAirtimeAmount = (float) \App\Models\Setting::get('airtime_default_amount', 20);
    $this->bulkTarget = 'not_received';
    $this->isBulkAirtimeModalOpen = true;
};

$closeBulkAirtimeModal = function () {
    $this->isBulkAirtimeModalOpen = false;
};

$sendBulkAirtime = function () {
    $query = User::query()
        ->where(function ($q) {
            $q->whereNotNull('phone')->where('phone', '!=', '')
              ->orWhereNotNull('mpesa_phone')->where('mpesa_phone', '!=', '');
        });

    if ($this->bulkTarget === 'not_received') {
        $query->whereDoesntHave('airtimeTopups', function ($q) {
            $q->where('status', 'sent');
        });
    }

    $eligibleUsers = $query->get();

    if ($eligibleUsers->isEmpty()) {
        session()->flash('error', 'No eligible users with registered phone numbers found.');
        $this->closeBulkAirtimeModal();
        return;
    }

    $items = [];
    foreach ($eligibleUsers as $u) {
        $phone = $u->phone ?: $u->mpesa_phone;
        $items[] = [
            'phone' => $phone,
            'amount' => (float) $this->bulkAirtimeAmount,
            'user_id' => $u->id,
        ];
    }

    $result = Airtime::sendBatch($items, auth()->id());

    if ($result['success']) {
        session()->flash('message', "🚀 Bulk Airtime Processed: " . $result['message']);
    } else {
        session()->flash('error', "Bulk Airtime Result: " . $result['message']);
    }

    $this->closeBulkAirtimeModal();
};

with(function () {
    $users = User::query()
        ->with(['latestAirtimeTopup'])
        ->when($this->search, function ($q) {
            $q->where(function($inner) {
                $inner->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('mpesa_phone', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->roleFilter, function ($q) {
            $q->where('role', $this->roleFilter);
        })
        ->when($this->airtimeFilter === 'received', function ($q) {
            $q->whereHas('airtimeTopups', fn($sub) => $sub->where('status', 'sent'));
        })
        ->when($this->airtimeFilter === 'not_received', function ($q) {
            $q->whereDoesntHave('airtimeTopups', fn($sub) => $sub->where('status', 'sent'))
              ->where(fn($p) => $p->whereNotNull('phone')->where('phone', '!=', '')->orWhereNotNull('mpesa_phone')->where('mpesa_phone', '!=', ''));
        })
        ->when($this->airtimeFilter === 'has_phone', function ($q) {
            $q->where(fn($p) => $p->whereNotNull('phone')->where('phone', '!=', '')->orWhereNotNull('mpesa_phone')->where('mpesa_phone', '!=', ''));
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    // Summary airtime stats
    $totalPhoneUsers = User::where(fn($p) => $p->whereNotNull('phone')->where('phone', '!=', '')->orWhereNotNull('mpesa_phone')->where('mpesa_phone', '!=', ''))->count();
    $totalAirtimeUsers = User::whereHas('airtimeTopups', fn($q) => $q->where('status', 'sent'))->count();
    $totalAirtimeSpent = (float) AirtimeTopup::where('status', 'sent')->sum('amount');

    return compact('users', 'totalPhoneUsers', 'totalAirtimeUsers', 'totalAirtimeSpent');
});

?>

<div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-gray-250 dark:border-gray-800">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span>User Accounts Manager</span>
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage user profiles, registered phone numbers, and Africa's Talking airtime disbursements.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <!-- Bulk Airtime Button -->
            <button wire:click="openBulkAirtimeModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3.5 py-2 rounded-lg transition shadow flex items-center gap-1.5">
                <span class="text-sm">⚡</span>
                <span>Bulk Airtime Top-Up</span>
            </button>

            <!-- Create User Button -->
            <button wire:click="openForm()" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow">
                Create User Account
            </button>
        </div>
    </div>

    <!-- Live Airtime & Phone User Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-3.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">Phone Registered Users</span>
                <span class="text-lg font-black text-gray-900 dark:text-white font-mono">{{ number_format($totalPhoneUsers) }}</span>
            </div>
            <div class="w-9 h-9 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">
                📱
            </div>
        </div>

        <div class="p-3.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Rewarded With Airtime</span>
                <span class="text-lg font-black text-emerald-700 dark:text-emerald-400 font-mono">{{ number_format($totalAirtimeUsers) }}</span>
            </div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                🟢
            </div>
        </div>

        <div class="p-3.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400 block">Total Airtime Disbursed</span>
                <span class="text-lg font-black text-gray-900 dark:text-white font-mono">KES {{ number_format($totalAirtimeSpent, 2) }}</span>
            </div>
            <div class="w-9 h-9 rounded-full bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold text-sm">
                💰
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-green-100 dark:bg-green-950/20 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-400 text-xs rounded font-semibold">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="p-3 bg-red-100 dark:bg-red-950/20 border border-red-200 dark:border-red-900 text-red-800 dark:text-red-400 text-xs rounded font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-3.5 shadow-sm">
        <div class="flex-grow">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users by name, email, or phone number..." 
                   class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#C8102E]">
        </div>

        <!-- Airtime Status Filter -->
        <div class="w-full sm:w-48">
            <select wire:model.live="airtimeFilter" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 font-semibold">
                <option value="">All Airtime States</option>
                <option value="received">🟢 Airtime Received</option>
                <option value="not_received">⚪ Not Received Yet</option>
                <option value="has_phone">📱 Has Phone Number</option>
            </select>
        </div>

        <!-- Role Filter -->
        <div class="w-full sm:w-44">
            <select wire:model.live="roleFilter" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#C8102E]">
                <option value="">All Roles</option>
                <option value="admin">Administrator</option>
                <option value="editor">Editor</option>
                <option value="manager">Manager</option>
                <option value="author">Author</option>
                <option value="writing-article">Writing Article</option>
                <option value="user">User</option>
                <option value="subscriber">Subscriber</option>
                <option value="reporter">Reporter (Legacy)</option>
                <option value="contributor">Contributor (Legacy)</option>
            </select>
        </div>
    </div>

    <!-- SINGLE AIRTIME TOP-UP MODAL -->
    @if($isAirtimeModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">⚡</div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Top Up Airtime</h3>
                            <p class="text-[10px] text-gray-500">Disbursed instantly via Africa's Talking</p>
                        </div>
                    </div>
                    <button wire:click="closeAirtimeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg font-bold">&times;</button>
                </div>

                <!-- Recipient Info Card -->
                <div class="p-3.5 bg-emerald-50/70 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-850 rounded-lg space-y-1">
                    <div class="text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-400 tracking-wider">Recipient Details</div>
                    <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $airtimeTargetName }}</div>
                    <div class="text-xs font-mono font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                        <span>📱</span>
                        <span>{{ $airtimeTargetPhone }}</span>
                        <span class="text-[9px] bg-emerald-200 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-200 px-1.5 py-0.2 rounded font-sans">Safaricom / Airtel</span>
                    </div>
                </div>

                <!-- Amount Selection -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Select or Enter Top-Up Amount (KES)</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach([10, 20, 50, 100] as $preset)
                            <button type="button" wire:click="$set('airtimeAmount', {{ $preset }})" 
                                    class="py-2 text-xs font-bold rounded-lg border transition {{ $airtimeAmount == $preset ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
                                KSh {{ $preset }}
                            </button>
                        @endforeach
                    </div>
                    
                    <div class="pt-1">
                        <label class="text-[10px] font-bold text-gray-400 block mb-1">Custom Amount</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-400 font-bold">KES</span>
                            <input type="number" wire:model="airtimeAmount" min="5" max="10000" step="5" 
                                   class="w-full pl-12 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 text-xs text-gray-900 dark:text-white font-mono font-bold focus:ring-1 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex space-x-2">
                    <button wire:click="sendAirtime" wire:loading.attr="disabled" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 rounded-lg transition shadow flex items-center justify-center gap-1.5 disabled:opacity-50">
                        <span wire:loading.remove wire:target="sendAirtime">⚡ Confirm & Send KSh {{ $airtimeAmount }}</span>
                        <span wire:loading wire:target="sendAirtime">Dispatching Airtime...</span>
                    </button>
                    <button wire:click="closeAirtimeModal()" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- BULK AIRTIME TOP-UP MODAL -->
    @if($isBulkAirtimeModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">🚀</div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Bulk Airtime Top-Up</h3>
                            <p class="text-[10px] text-gray-500">Send airtime to multiple phone users simultaneously</p>
                        </div>
                    </div>
                    <button wire:click="closeBulkAirtimeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg font-bold">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-gray-700 dark:text-gray-300 block mb-1">Target Recipients</label>
                        <select wire:model="bulkTarget" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 text-xs text-gray-900 dark:text-white font-semibold">
                            <option value="not_received">Users who have NOT received airtime yet (Avoid duplicates)</option>
                            <option value="all_phone">All registered users with a phone number</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 dark:text-gray-300 block mb-1">Airtime Amount Per User (KES)</label>
                        <div class="grid grid-cols-4 gap-2 mb-2">
                            @foreach([10, 20, 50, 100] as $preset)
                                <button type="button" wire:click="$set('bulkAirtimeAmount', {{ $preset }})" 
                                        class="py-2 text-xs font-bold rounded-lg border transition {{ $bulkAirtimeAmount == $preset ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300' }}">
                                    KSh {{ $preset }}
                                </button>
                            @endforeach
                        </div>
                        <input type="number" wire:model="bulkAirtimeAmount" min="5" max="10000" step="5" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 text-xs font-mono font-bold text-gray-900 dark:text-white">
                    </div>

                    <div class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-850 rounded-lg text-amber-800 dark:text-amber-300 text-[11px] leading-relaxed">
                        ⚠️ <strong>Notice:</strong> This action will send an immediate bulk API request to Africa's Talking. Ensure your Africa's Talking wallet balance has sufficient funds to cover the total disbursement.
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex space-x-2">
                    <button wire:click="sendBulkAirtime" wire:loading.attr="disabled" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 rounded-lg transition shadow flex items-center justify-center gap-1.5 disabled:opacity-50">
                        <span wire:loading.remove wire:target="sendBulkAirtime">🚀 Dispatch Bulk Airtime</span>
                        <span wire:loading wire:target="sendBulkAirtime">Processing Batch...</span>
                    </button>
                    <button wire:click="closeBulkAirtimeModal()" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-bold text-xs rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Users Form Modal -->
    @if($isFormOpen)
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 shadow space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                    {{ $userId ? 'Edit User Details' : 'Create New Account' }}
                </h3>
                <span class="text-[11px] text-gray-500 font-semibold">User can register using Kenyan Phone (07/01) or Email</span>
            </div>
            
            <form wire:submit.prevent="saveUser" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Full Name</label>
                    <input type="text" wire:model="name" required placeholder="e.g. John Doe" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:ring-1 focus:ring-[#C8102E]">
                    @error('name') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Featured Phone Number Input Card -->
                <div class="p-3.5 bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 rounded-lg space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-emerald-900 dark:text-emerald-300 flex items-center gap-1.5">
                            <span>📱</span> Kenyan Phone Number
                        </label>
                        <span class="text-[9px] font-bold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 px-1.5 py-0.5 rounded">Airtime & Auth</span>
                    </div>
                    <input type="text" wire:model="phone" placeholder="e.g. 0712345678 or 0112345678" class="w-full bg-white dark:bg-gray-800 border border-emerald-300 dark:border-emerald-800 rounded p-2 text-xs text-gray-900 dark:text-white font-mono focus:ring-1 focus:ring-emerald-500">
                    <p class="text-[10px] text-emerald-700 dark:text-emerald-400">Strictly 10 digits starting with 07 or 01 (No country code).</p>
                    @error('phone') <p class="text-red-500 text-[10px] font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Featured Email Address Input Card -->
                <div class="p-3.5 bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900 rounded-lg space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-blue-900 dark:text-blue-300 flex items-center gap-1.5">
                            <span>✉️</span> Email Address
                        </label>
                        <span class="text-[9px] font-bold bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 px-1.5 py-0.5 rounded">Web Auth</span>
                    </div>
                    <input type="email" wire:model="email" placeholder="e.g. user@example.com" class="w-full bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-800 rounded p-2 text-xs text-gray-900 dark:text-white font-mono focus:ring-1 focus:ring-blue-500">
                    <p class="text-[10px] text-blue-700 dark:text-blue-400">Required if Phone number is omitted.</p>
                    @error('email') <p class="text-red-500 text-[10px] font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Account Role</label>
                    <select wire:model="role" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        <option value="user">User (Reader / Mobile App)</option>
                        <option value="author">Author (Content Creator)</option>
                        <option value="writing-article">Writing Article</option>
                        <option value="manager">Manager</option>
                        <option value="editor">Editor</option>
                        <option value="admin">Administrator</option>
                    </select>
                    @error('role') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">
                        Password {{ $userId ? '(Leave blank to keep current)' : '' }}
                    </label>
                    <input type="password" wire:model="password" placeholder="At least 8 characters" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                    @error('password') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2 pt-4 flex space-x-2 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-5 py-2.5 rounded transition shadow">
                        {{ $userId ? 'Save Account Changes' : 'Create User Account' }}
                    </button>
                    <button type="button" wire:click="closeForm()" class="bg-gray-250 dark:bg-gray-800 hover:bg-gray-300 border border-gray-300 dark:border-gray-750 text-gray-700 dark:text-gray-300 text-xs font-bold px-4 py-2.5 rounded transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Users Table Listing -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-950 text-gray-500 font-bold border-b border-gray-200 dark:border-gray-800 uppercase tracking-wider text-[10px]">
                        <th class="p-4">User Details</th>
                        <th class="p-4">Airtime Status</th>
                        <th class="p-4">Reg Method</th>
                        <th class="p-4">Current Role</th>
                        <th class="p-4">Quick Convert Role</th>
                        <th class="p-4">Registered Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-850">
                    @foreach($users as $user)
                        @php
                            $userPhone = $user->phone ?? $user->mpesa_phone;
                            $hasAirtime = $user->hasReceivedAirtime();
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/50">
                            <!-- User Details -->
                            <td class="p-4 flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-bold text-gray-500 overflow-hidden shrink-0">
                                    @if($user->photo_url)
                                        <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-[10px] font-mono text-gray-500 flex items-center gap-1">
                                        <span>✉️</span>
                                        <span class="{{ $user->email ? 'text-gray-700 dark:text-gray-300 font-semibold' : 'text-gray-400 italic' }}">
                                            {{ $user->email ?? 'No Email' }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] font-mono text-gray-500 flex items-center gap-1">
                                        <span>📱</span>
                                        <span class="{{ $userPhone ? 'text-emerald-700 dark:text-emerald-400 font-bold' : 'text-gray-400 italic' }}">
                                            {{ $userPhone ?? 'No Phone' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Airtime Status Indicator -->
                            <td class="p-4">
                                @if($hasAirtime)
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Sent (KES {{ number_format($user->totalAirtimeReceived()) }})
                                        </span>
                                        @if($user->latestAirtimeTopup)
                                            <div class="text-[9px] font-mono text-gray-400" title="Request ID: {{ $user->latestAirtimeTopup->request_id }}">
                                                {{ $user->latestAirtimeTopup->sent_at?->format('M d, H:i') ?? 'Completed' }}
                                            </div>
                                        @endif
                                    </div>
                                @elseif($userPhone)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Not Sent
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">No phone</span>
                                @endif
                            </td>

                            <!-- Registration Method -->
                            <td class="p-4">
                                @if($userPhone && !$user->email)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-400">📱 Phone User</span>
                                @elseif($user->email && !$userPhone)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-400">✉️ Email User</span>
                                @elseif($user->email && $userPhone)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-400">📱+✉️ Full Account</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">Guest</span>
                                @endif
                            </td>

                            <!-- Current Role -->
                            <td class="p-4">
                                <button type="button" wire:click="$set('roleFilter', '{{ $user->role }}')" 
                                        title="Click to filter users by {{ $user->role }}" 
                                        aria-label="Filter by {{ $user->role }} role"
                                        class="px-2 py-0.5 rounded font-bold uppercase text-[9px] cursor-pointer hover:opacity-80 transition focus:outline-none focus:ring-1 focus:ring-[#C8102E]
                                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' : '' }}
                                            {{ $user->role === 'editor' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/20 dark:text-blue-400' : '' }}
                                            {{ $user->role === 'manager' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950/20 dark:text-purple-400' : '' }}
                                            {{ in_array($user->role, ['author', 'reporter', 'contributor']) ? 'bg-green-100 text-green-800 dark:bg-green-950/20 dark:text-green-400' : '' }}
                                            {{ in_array($user->role, ['user', 'subscriber']) ? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' : '' }}
                                        ">
                                    {{ $user->role }}
                                </button>
                            </td>

                            <!-- Quick Convert Role -->
                            <td class="p-4">
                                <select wire:change="quickChangeRole({{ $user->id }}, $event.target.value)" 
                                        class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-[11px] font-bold rounded px-2.5 py-1 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#C8102E]">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User (Reader)</option>
                                    <option value="author" {{ $user->role === 'author' ? 'selected' : '' }}>Convert to Author</option>
                                    <option value="editor" {{ $user->role === 'editor' ? 'selected' : '' }}>Convert to Editor</option>
                                    <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Convert to Manager</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Convert to Admin</option>
                                </select>
                            </td>

                            <!-- Registered Date -->
                            <td class="p-4 text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-right space-x-2">
                                @if($userPhone)
                                    <button wire:click="openAirtimeModal({{ $user->id }})" 
                                            class="inline-flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 px-2 py-1 rounded text-[11px] font-bold transition shadow-2xs" 
                                            title="Send Airtime via Africa's Talking">
                                        <span>⚡</span> Top-Up
                                    </button>
                                @endif

                                <button wire:click="openForm({{ $user->id }})" class="text-blue-600 hover:text-blue-800 font-bold">Edit</button>

                                @if(auth()->id() !== $user->id)
                                    <button onclick="confirm('Are you sure you want to delete this account?') || event.stopImmediatePropagation()" 
                                            wire:click="deleteUser({{ $user->id }})" class="text-red-650 hover:text-red-800 font-bold">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

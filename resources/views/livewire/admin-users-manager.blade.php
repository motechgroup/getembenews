<?php

use function Livewire\Volt\{state, with, rules};
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

state([
    'search' => '',
    'roleFilter' => '',
    
    // Form fields
    'userId' => null,
    'name' => '',
    'email' => '',
    'phone' => '',
    'password' => '',
    'role' => 'user',
    
    'isFormOpen' => false,
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
        $this->phone = $user->phone ?? '';
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

with(function () {
    $users = User::query()
        ->when($this->search, function ($q) {
            $q->where(function($inner) {
                $inner->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->roleFilter, function ($q) {
            $q->where('role', $this->roleFilter);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return compact('users');
});

?>

<div class="space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-gray-250 dark:border-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">User Accounts Manager</h2>
        
        <button wire:click="openForm()" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition shadow">
            Create User Account
        </button>
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
    <div class="flex flex-col sm:flex-row gap-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 shadow-sm">
        <div class="flex-grow">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users by name, email, or phone number..." 
                   class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#C8102E]">
        </div>
        <div class="w-full sm:w-48">
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
                        <span class="text-[9px] font-bold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 px-1.5 py-0.5 rounded">Mobile Auth</span>
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
                        <th class="p-4">Reg Method</th>
                        <th class="p-4">Current Role</th>
                        <th class="p-4">Quick Convert Role</th>
                        <th class="p-4">Registered Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-850">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/50">
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
                                    @if($user->email)
                                        <div class="text-[10px] text-gray-500 font-mono">✉️ {{ $user->email }}</div>
                                    @endif
                                    @if($user->phone)
                                        <div class="text-[10px] text-gray-600 dark:text-gray-400 font-mono">📱 {{ $user->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                @if($user->phone && !$user->email)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-400">📱 Phone User</span>
                                @elseif($user->email && !$user->phone)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-400">✉️ Email User</span>
                                @elseif($user->email && $user->phone)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-400">📱+✉️ Full Account</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">Guest</span>
                                @endif
                            </td>
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
                            <td class="p-4 text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button wire:click="openForm({{ $user->id }})" class="text-blue-600 hover:text-blue-850 font-bold">Edit</button>
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

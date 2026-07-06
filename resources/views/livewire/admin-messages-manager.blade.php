<?php

use function Livewire\Volt\{state, with};
use App\Models\ContactMessage;

state([
    'activeMessageId' => null,
    'saved' => false,
]);

$viewMessage = function ($id) {
    $this->activeMessageId = $id;
    $msg = ContactMessage::findOrFail($id);
    $msg->is_read = true;
    $msg->save();
};

$closeMessage = function () {
    $this->activeMessageId = null;
};

$deleteMessage = function ($id) {
    ContactMessage::findOrFail($id)->delete();
    if ($this->activeMessageId === $id) {
        $this->activeMessageId = null;
    }
    session()->flash('message', 'Message deleted successfully.');
};

with(function () {
    $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(15);
    $activeMessage = $this->activeMessageId ? ContactMessage::find($this->activeMessageId) : null;
    
    return compact('messages', 'activeMessage');
});

?>

<div class="space-y-6">
    <div class="pb-4 border-b border-gray-250 dark:border-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Contact Messages Inbox</h2>
    </div>

    @if (session()->has('message'))
        <div class="p-3 bg-green-100 dark:bg-green-950/20 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-400 text-xs rounded font-semibold">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Messages List -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Inbox</h3>
            
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm">
                <div class="divide-y divide-gray-150 dark:divide-gray-850">
                    @forelse($messages as $msg)
                        <div wire:click="viewMessage({{ $msg->id }})" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-950/50 cursor-pointer transition flex items-center justify-between text-xs {{ !$msg->is_read ? 'bg-red-50/20 border-l-4 border-[#C8102E] font-bold' : '' }}">
                            <div class="space-y-1 pr-4">
                                <div class="flex items-center space-x-2 text-gray-900 dark:text-white">
                                    <span>{{ $msg->name }}</span>
                                    <span class="text-gray-400 font-normal">({{ $msg->email }})</span>
                                </div>
                                <div class="text-[11px] text-gray-500 truncate max-w-md">{{ $msg->subject }} &bull; {{ Str::limit($msg->message, 80) }}</div>
                            </div>
                            <div class="flex items-center space-x-3 shrink-0 text-gray-400 text-[10px]">
                                <span>{{ $msg->created_at->diffForHumans() }}</span>
                                <button type="button" wire:click.stop="deleteMessage({{ $msg->id }})" class="text-red-650 hover:text-red-800 font-bold">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400">
                            No contact messages received yet.
                        </div>
                    @endforelse
                </div>

                @if($messages->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Active Message Details -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Reader Tip Details</h3>
            
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 shadow-sm min-h-64 flex flex-col justify-between">
                @if($activeMessage)
                    <div class="space-y-4">
                        <div class="border-b border-gray-100 dark:border-gray-800 pb-3 space-y-1">
                            <span class="text-[10px] uppercase font-bold text-[#C8102E] tracking-wider">{{ $activeMessage->subject }}</span>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white">{{ $activeMessage->name }}</h4>
                            <p class="text-[10px] text-gray-500 font-mono">{{ $activeMessage->email }}</p>
                        </div>
                        <p class="text-xs text-gray-705 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                            {{ $activeMessage->message }}
                        </p>
                    </div>
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs">
                        <span class="text-gray-450">{{ $activeMessage->created_at->format('M d, Y H:i') }}</span>
                        <a href="mailto:{{ $activeMessage->email }}?subject=RE: {{ rawurlencode($activeMessage->subject) }}" class="bg-gray-900 hover:bg-gray-850 text-white font-bold text-[10px] px-3.5 py-1.5 rounded transition">
                            Reply via Email
                        </a>
                    </div>
                @else
                    <div class="my-auto text-center text-gray-450 text-xs italic">
                        Select a message from the inbox queue to view full content.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

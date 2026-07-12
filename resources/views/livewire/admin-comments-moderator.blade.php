<?php

use function Livewire\Volt\{state};
use App\Models\Comment;

state([
    'comments' => fn() => Comment::with(['user', 'article'])->orderBy('created_at', 'desc')->get(),
]);

$approve = function ($id) {
    $comment = Comment::findOrFail($id);
    $comment->update(['status' => 'approved']);
    $this->comments = Comment::with(['user', 'article'])->orderBy('created_at', 'desc')->get();
};

$reject = function ($id) {
    $comment = Comment::findOrFail($id);
    $comment->update(['status' => 'rejected']);
    $this->comments = Comment::with(['user', 'article'])->orderBy('created_at', 'desc')->get();
};

$markSpam = function ($id) {
    $comment = Comment::findOrFail($id);
    $comment->update(['status' => 'spam']);
    $this->comments = Comment::with(['user', 'article'])->orderBy('created_at', 'desc')->get();
};

$delete = function ($id) {
    $comment = Comment::findOrFail($id);
    $comment->delete();
    $this->comments = Comment::with(['user', 'article'])->orderBy('created_at', 'desc')->get();
};

?>

<div class="space-y-6">
    <div class="pb-4 border-b border-gray-200 dark:border-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Comment Moderation</h2>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-850 text-gray-550 border-b border-gray-200 dark:border-gray-800 font-bold">
                        <th class="p-3">User</th>
                        <th class="p-3">Comment</th>
                        <th class="p-3">Article</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($comments as $comment)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50">
                            <td class="p-3 font-bold text-gray-900 dark:text-white">
                                <div>{{ $comment->user->name }}</div>
                                <div class="text-[10px] text-gray-400 font-normal">{{ $comment->user->email }}</div>
                            </td>
                            <td class="p-3 text-gray-700 dark:text-gray-300 max-w-md">
                                <p class="leading-relaxed">{{ $comment->body }}</p>
                                <span class="text-[9px] text-gray-400 block pt-1">{{ $comment->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="p-3 text-gray-500 truncate max-w-xs">
                                <a href="/articles/{{ $comment->article->slug }}" target="_blank" class="hover:underline">{{ $comment->article->title }}</a>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded font-bold uppercase text-[9px] 
                                    {{ $comment->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-950/20 dark:text-green-400' : '' }}
                                    {{ $comment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/20 dark:text-yellow-400' : '' }}
                                    {{ $comment->status === 'rejected' ? 'bg-orange-100 text-orange-850 dark:bg-orange-950/20 dark:text-orange-400' : '' }}
                                    {{ $comment->status === 'spam' ? 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400' : '' }}
                                ">
                                    {{ $comment->status }}
                                </span>
                            </td>
                            <td class="p-3 text-right space-x-2 whitespace-nowrap">
                                @if($comment->status !== 'approved')
                                    <button wire:click="approve({{ $comment->id }})" class="text-green-600 font-bold hover:underline">Approve</button>
                                @endif
                                @if($comment->status !== 'rejected')
                                    <button wire:click="reject({{ $comment->id }})" class="text-orange-500 font-bold hover:underline">Reject</button>
                                @endif
                                @if($comment->status !== 'spam')
                                    <button wire:click="markSpam({{ $comment->id }})" class="text-red-400 font-bold hover:underline">Spam</button>
                                @endif
                                <button wire:click="delete({{ $comment->id }})" wire:confirm="Are you sure you want to permanently delete this comment?" class="text-red-650 font-bold hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">No comments require moderation.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

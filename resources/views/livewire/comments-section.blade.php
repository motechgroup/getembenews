<?php

use function Livewire\Volt\{state, rules};
use App\Models\Comment;
use App\Models\Article;

state(['article', 'body' => '', 'comments' => fn($article) => $article->allComments()->whereNull('parent_id')->with(['user', 'replies.user'])->orderBy('created_at', 'desc')->get()]);

rules(['body' => 'required|string|max:1000']);

$postComment = function () {
    if (!auth()->check()) {
        return;
    }

    $this->validate();

    Comment::create([
        'article_id' => $this->article->id,
        'user_id' => auth()->id(),
        'body' => $this->body,
        'status' => 'approved', // auto-approve for now
    ]);

    $this->body = '';
    
    // Refresh comments list
    $this->comments = $this->article->allComments()->whereNull('parent_id')->with(['user', 'replies.user'])->orderBy('created_at', 'desc')->get();
};

$deleteComment = function ($id) {
    if (!auth()->check() || !auth()->user()->isStaff()) {
        return;
    }

    $comment = Comment::find($id);
    if ($comment) {
        $comment->delete();
    }

    // Refresh comments list
    $this->comments = $this->article->allComments()->whereNull('parent_id')->with(['user', 'replies.user'])->orderBy('created_at', 'desc')->get();
};

?>

<div class="space-y-6">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 flex items-center space-x-2">
        <span>Comments</span>
        <span class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs px-2.5 py-0.5 rounded-full font-semibold">
            {{ $article->allComments()->count() }}
        </span>
    </h3>

    <!-- Post Comment Form -->
    @auth
        <form wire:submit.prevent="postComment" class="space-y-3">
            <div>
                <textarea wire:model="body" rows="3" placeholder="Join the discussion..." required
                    class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-gray-100"></textarea>
                @error('body')
                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition">
                    Post Comment
                </button>
            </div>
        </form>
    @else
        <div class="bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center text-xs text-gray-500">
            Please <a href="{{ route('login') }}" class="text-[#C8102E] font-bold hover:underline">log in</a> to participate in the comments section.
        </div>
    @endauth

    <!-- Comments List -->
    <div class="space-y-6">
        @forelse($comments as $comment)
            <div class="space-y-4">
                <div class="flex space-x-3 items-start group">
                    <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-800 shrink-0 flex items-center justify-center font-bold text-gray-600 dark:text-gray-400 text-sm overflow-hidden border border-gray-200 dark:border-gray-750">
                        @if($comment->user->photo_url)
                            <img src="{{ $comment->user->photo_url }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1 space-y-1 bg-gray-50 dark:bg-gray-950 p-3 rounded-lg border border-gray-100 dark:border-gray-850">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                            <div class="flex items-center space-x-2 text-gray-400">
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                                @if(auth()->check() && auth()->user()->isStaff())
                                    <button wire:click="deleteComment({{ $comment->id }})" class="text-red-500 hover:text-red-700 hover:underline text-[10px]">Delete</button>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed pt-1">
                            {{ $comment->body }}
                        </p>
                    </div>
                </div>

                <!-- Comment Replies (Indented) -->
                @if($comment->replies->count() > 0)
                    <div class="ml-12 space-y-4 border-l-2 border-gray-100 dark:border-gray-800 pl-4">
                        @foreach($comment->replies as $reply)
                            <div class="flex space-x-3 items-start">
                                <div class="w-7.5 h-7.5 rounded-full bg-gray-200 dark:bg-gray-800 shrink-0 flex items-center justify-center font-bold text-gray-600 dark:text-gray-400 text-xs overflow-hidden border border-gray-200 dark:border-gray-750">
                                    @if($reply->user->photo_url)
                                        <img src="{{ $reply->user->photo_url }}" alt="{{ $reply->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="flex-1 space-y-1 bg-gray-50/50 dark:bg-gray-950/50 p-3 rounded-lg border border-gray-100/50 dark:border-gray-850/50">
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                                        <div class="flex items-center space-x-2 text-gray-400">
                                            <span>{{ $reply->created_at->diffForHumans() }}</span>
                                            @if(auth()->check() && auth()->user()->isStaff())
                                                <button wire:click="deleteComment({{ $reply->id }})" class="text-red-500 hover:text-red-700 hover:underline text-[9px]">Delete</button>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed pt-1">
                                        {{ $reply->body }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-400 text-center text-xs">No comments yet. Be the first to share your thoughts!</p>
        @endforelse
    </div>
</div>

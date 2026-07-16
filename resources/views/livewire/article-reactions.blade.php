<?php

use function Livewire\Volt\{state, mount};
use App\Models\Article;
use App\Models\ArticleReaction;

state([
    'articleId',
    'reactionsCount' => [],
    'userReaction' => null,
]);

mount(function (Article $article) {
    $this->articleId = $article->id;
    $this->loadReactions();
});

$loadReactions = function () {
    try {
        $reactions = ArticleReaction::where('article_id', $this->articleId)
            ->select('type', \DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $this->reactionsCount = [
            'like' => $reactions['like'] ?? 0,
            'love' => $reactions['love'] ?? 0,
            'wow' => $reactions['wow'] ?? 0,
            'sad' => $reactions['sad'] ?? 0,
            'angry' => $reactions['angry'] ?? 0,
        ];

        $ip = request()->ip();
        $reaction = ArticleReaction::where('article_id', $this->articleId)
            ->where('ip_address', $ip)
            ->first();

        $this->userReaction = $reaction ? $reaction->type : null;
    } catch (\Exception $e) {
        \Log::warning("ArticleReaction database load error: " . $e->getMessage());
        $this->reactionsCount = [
            'like' => 0,
            'love' => 0,
            'wow' => 0,
            'sad' => 0,
            'angry' => 0,
        ];
        $this->userReaction = null;
    }
};

$react = function ($type) {
    try {
        $ip = request()->ip();

        $existing = ArticleReaction::where('article_id', $this->articleId)
            ->where('ip_address', $ip)
            ->first();

        if ($existing) {
            if ($existing->type === $type) {
                // Toggle off
                $existing->delete();
            } else {
                // Update reaction
                $existing->update(['type' => $type]);
            }
        } else {
            // Create reaction
            ArticleReaction::create([
                'article_id' => $this->articleId,
                'type' => $type,
                'ip_address' => $ip
            ]);
        }

        $this->loadReactions();
    } catch (\Exception $e) {
        \Log::error("ArticleReaction database write error: " . $e->getMessage());
    }
};

?>

<div>
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
        <div class="flex flex-col items-center sm:flex-row sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center sm:text-left leading-none">How do you feel about this article?</h4>
                <p class="text-[10px] text-gray-450 dark:text-gray-500 mt-1.5 text-center sm:text-left">Let us know by choosing a reaction below.</p>
            </div>

            <div class="flex items-center space-x-2">
                @foreach([
                    'like' => ['emoji' => '👍', 'label' => 'Like', 'active_color' => 'bg-blue-50 border-blue-300 text-blue-600 dark:bg-blue-950/40 dark:border-blue-900/50 dark:text-blue-400'],
                    'love' => ['emoji' => '❤️', 'label' => 'Love', 'active_color' => 'bg-red-50 border-red-300 text-red-600 dark:bg-red-950/40 dark:border-red-900/50 dark:text-red-450'],
                    'wow' => ['emoji' => '😮', 'label' => 'Wow', 'active_color' => 'bg-amber-50 border-amber-300 text-amber-600 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-400'],
                    'sad' => ['emoji' => '😢', 'label' => 'Sad', 'active_color' => 'bg-indigo-50 border-indigo-300 text-indigo-600 dark:bg-indigo-950/40 dark:border-indigo-900/50 dark:text-indigo-400'],
                    'angry' => ['emoji' => '😡', 'label' => 'Angry', 'active_color' => 'bg-orange-50 border-orange-300 text-orange-600 dark:bg-orange-950/40 dark:border-orange-900/50 dark:text-orange-400']
                ] as $key => $meta)
                    <button wire:click="react('{{ $key }}')"
                            class="flex items-center space-x-1 px-2.5 py-1.5 rounded-full border text-[11px] font-bold transition duration-200 transform hover:scale-105 active:scale-95 focus:outline-none {{ $userReaction === $key ? $meta['active_color'] : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-850' }}"
                            title="{{ $meta['label'] }}">
                        <span class="text-sm leading-none">{{ $meta['emoji'] }}</span>
                        <span>{{ $reactionsCount[$key] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

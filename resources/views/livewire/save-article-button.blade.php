<?php

use function Livewire\Volt\{state, mount};
use App\Models\Article;

state(['article', 'isSaved' => false]);

mount(function (Article $article) {
    $this->article = $article;
    if (auth()->check()) {
        $this->isSaved = auth()->user()->savedArticles()->where('article_id', $article->id)->exists();
    }
});

$toggleSave = function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    
    if ($this->isSaved) {
        $user->savedArticles()->detach($this->article->id);
        $this->isSaved = false;
    } else {
        $user->savedArticles()->attach($this->article->id);
        $this->isSaved = true;
    }
};

?>

<div>
    <button wire:click="toggleSave" class="inline-flex items-center space-x-1.5 px-3 py-1.5 border rounded-full text-xs font-semibold transition focus:outline-none {{ $isSaved ? 'bg-red-50 dark:bg-red-950/20 text-[#C8102E] border-red-200 dark:border-red-900' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-755' }}">
        <!-- Heart / Bookmark Icon -->
        <svg class="h-4 w-4 {{ $isSaved ? 'fill-current text-[#C8102E]' : 'fill-none' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
        </svg>
        <span>{{ $isSaved ? 'Saved' : 'Save Story' }}</span>
    </button>
</div>

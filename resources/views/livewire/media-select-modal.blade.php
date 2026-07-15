<?php

use function Livewire\Volt\{state, rules, uses};
use Livewire\WithFileUploads;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

uses(WithFileUploads::class);

state([
    'search' => '',
    'filterType' => 'all',
    'isOpen' => false,
    'targetField' => '', // cover, rich-content, downloads, format-meta
    
    // Upload temporary file
    'uploadedFile' => null,
]);

$openModal = function ($field) {
    $this->targetField = $field;
    $this->isOpen = true;
};

$selectMedia = function ($url) {
    $this->dispatch('media-selected', url: $url, targetField: $this->targetField);
    $this->isOpen = false;
};

$uploadFile = function () {
    $this->validate([
        'uploadedFile' => 'required|file|max:10240',
    ]);

    $originalName = $this->uploadedFile->getClientOriginalName();
    $mimeType = $this->uploadedFile->getMimeType();
    $size = $this->uploadedFile->getSize();
    
    $cleanName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
    $path = $this->uploadedFile->storeAs('uploads', $cleanName, 'public');
    $url = '/storage/' . $path;

    $media = Media::create([
        'filename' => $originalName,
        'path' => $path,
        'url' => $url,
        'mime_type' => $mimeType,
        'size' => $size,
        'user_id' => auth()->id(),
    ]);

    $this->reset('uploadedFile');
    $this->selectMedia($url);
};

$mediaList = function () {
    return Media::when(!auth()->user()->isAdmin(), function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->orderBy('created_at', 'desc')
        ->when($this->search, function ($q) {
            $q->where('filename', 'like', '%' . $this->search . '%');
        })
        ->when($this->filterType !== 'all', function ($q) {
            if ($this->filterType === 'image') {
                $q->where('mime_type', 'like', 'image/%');
            } else {
                $q->where('mime_type', 'not like', 'image/%');
            }
        })
        ->get();
};

?>

<div x-data="{ open: @entangle('isOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" @open-media-modal.window="$wire.openModal($event.detail.field)">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-900 border border-gray-250 dark:border-gray-800 rounded-lg max-w-4xl w-full p-6 shadow-xl relative z-10 space-y-4 max-h-[85vh] overflow-y-auto">
            
            <!-- Header -->
            <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center">
                    <span class="mr-1.5">📂</span> Select File from Media Library
                </h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold text-lg">&times;</button>
            </div>

            <!-- Upload Shortcut & Search -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-950 p-3 rounded border border-gray-200 dark:border-gray-850">
                <!-- Search -->
                <div class="flex space-x-2 items-center">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search files..." class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white flex-grow focus:outline-none">
                    <select wire:model.live="filterType" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                        <option value="all">All</option>
                        <option value="image">Images</option>
                        <option value="document">Documents</option>
                    </select>
                </div>

                <!-- Instant Upload -->
                <form wire:submit.prevent="uploadFile" class="flex space-x-2 items-center justify-end">
                    <input type="file" wire:model="uploadedFile" class="text-[10px] text-gray-400 cursor-pointer file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 dark:file:bg-gray-800 dark:file:text-gray-300">
                    @if($uploadedFile)
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-[10px] px-2 py-1 rounded transition">
                            Upload & Select
                        </button>
                    @endif
                    @error('uploadedFile') <p class="text-red-500 text-[9px]">{{ $message }}</p> @enderror
                </form>
            </div>

            <!-- Files Grid -->
            <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-6 gap-3 pt-2">
                @forelse($this->mediaList() as $file)
                    @php $isImage = Str::startsWith($file->mime_type, 'image/'); @endphp
                    <div wire:click="selectMedia('{{ $file->url }}')" class="group bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded p-2 text-center cursor-pointer hover:border-[#C8102E] hover:bg-red-50/10 dark:hover:bg-red-950/10 transition relative">
                        <!-- Thumbnail -->
                        <div class="aspect-square w-full rounded overflow-hidden flex items-center justify-center bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-850">
                            @if($isImage)
                                <img src="{{ $file->url }}" alt="{{ $file->filename }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xs font-bold uppercase text-[#C8102E] font-mono">{{ pathinfo($file->filename, PATHINFO_EXTENSION) }}</span>
                            @endif
                        </div>
                        <p class="text-[9px] font-semibold text-gray-700 dark:text-gray-300 truncate max-w-full mt-1.5" title="{{ $file->filename }}">{{ $file->filename }}</p>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-xs text-gray-400">
                        No assets in library.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

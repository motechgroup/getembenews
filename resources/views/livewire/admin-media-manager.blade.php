<?php

use function Livewire\Volt\{state, rules, uses};
use Livewire\WithFileUploads;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

uses(WithFileUploads::class);

state([
    'mediaFiles' => fn() => Media::when(!auth()->user()->isAdmin(), function ($q) {
        $q->where('user_id', auth()->id());
    })->orderBy('created_at', 'desc')->get(),
    'search' => '',
    'filterType' => 'all', // all, image, document
    
    // Upload state
    'uploadedFile' => null,
]);

$upload = function () {
    $this->validate([
        'uploadedFile' => 'required|file|max:10240', // max 10MB
    ]);

    $originalName = $this->uploadedFile->getClientOriginalName();
    $extension = $this->uploadedFile->getClientOriginalExtension();
    $mimeType = $this->uploadedFile->getMimeType();
    $size = $this->uploadedFile->getSize();
    
    // Generate clean unique name
    $cleanName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
    
    // Store in public/uploads directory
    $path = $this->uploadedFile->storeAs('uploads', $cleanName, 'public');
    $url = '/storage/' . $path;

    // Process image: compress and apply watermark
    $absolutePath = Storage::disk('public')->path($path);
    \App\Support\ImageProcessor::process($absolutePath);
    if (file_exists($absolutePath)) {
        $size = filesize($absolutePath);
    }

    Media::create([
        'filename' => $originalName,
        'path' => $path,
        'url' => $url,
        'mime_type' => $mimeType,
        'size' => $size,
        'user_id' => auth()->id(),
    ]);

    $this->reset('uploadedFile');
    $this->mediaFiles = Media::when(!auth()->user()->isAdmin(), function ($q) {
        $q->where('user_id', auth()->id());
    })->orderBy('created_at', 'desc')->get();
    session()->flash('media_success', 'File uploaded successfully!');
};

$deleteMedia = function ($id) {
    $media = Media::findOrFail($id);
    
    // Authorization check: Only owner or admin can delete
    if (!auth()->user()->isAdmin() && $media->user_id !== auth()->id()) {
        session()->flash('media_error', 'You are not authorized to delete this media file.');
        return;
    }

    // Delete file from storage
    if (Storage::disk('public')->exists($media->path)) {
        Storage::disk('public')->delete($media->path);
    }
    
    $media->delete();
    $this->mediaFiles = Media::when(!auth()->user()->isAdmin(), function ($q) {
        $q->where('user_id', auth()->id());
    })->orderBy('created_at', 'desc')->get();
    session()->flash('media_success', 'File deleted.');
};

$filteredMedia = function () {
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

<div class="space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-800">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Central Media & File Library</h2>
            <p class="text-xs text-gray-500 mt-1">Manage and upload all server-side assets (images, PDFs, documents, audio clips).</p>
        </div>
    </div>

    <!-- Upload Panel & Filters -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Box -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Upload New Asset</h3>
            
            @if (session()->has('media_success'))
                <div class="p-2 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                    {{ session('media_success') }}
                </div>
            @endif
            @if (session()->has('media_error'))
                <div class="p-2 bg-red-900/10 border border-red-800 text-red-300 text-xs rounded">
                    {{ session('media_error') }}
                </div>
            @endif

            <form wire:submit.prevent="upload" class="space-y-4">
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-950/20 transition relative">
                    <input type="file" wire:model="uploadedFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-1">
                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Click or drag file to upload</p>
                        <p class="text-[10px] text-gray-500">Max size 10MB (Images, PDFs, ZIPs, Docs)</p>
                    </div>
                </div>

                @if($uploadedFile)
                    <div class="p-2 bg-gray-50 dark:bg-gray-950 rounded flex items-center justify-between text-xs">
                        <span class="truncate max-w-[180px] font-mono text-gray-600 dark:text-gray-400">{{ $uploadedFile->getClientOriginalName() }}</span>
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded transition text-[10px]">
                            Start Upload
                        </button>
                    </div>
                @endif
                @error('uploadedFile') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
            </form>
        </div>

        <!-- Filter / Search Box -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Search & Filter Library</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Search Filename</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search files..."
                               class="w-full bg-gray-50 dark:bg-gray-950 border border-gray-355 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">File Category</label>
                        <select wire:model.live="filterType" class="w-full bg-gray-50 dark:bg-gray-955 border border-gray-355 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            <option value="all">All File Types</option>
                            <option value="image">Images only</option>
                            <option value="document">Documents / Audio / ZIPs</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-[10px] text-gray-450 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                Tip: Media uploaded here is accessible globally and can be dynamically attached to any article format or text editor block.
            </div>
        </div>
    </div>

    <!-- Assets Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($this->filteredMedia() as $file)
            @php $isImage = Str::startsWith($file->mime_type, 'image/'); @endphp
            <div class="group bg-white dark:bg-gray-900 border border-gray-250 dark:border-gray-800 rounded-lg p-2.5 space-y-2 relative shadow-sm hover:shadow-md transition">
                
                <!-- Delete Button -->
                <button type="button" wire:click="deleteMedia({{ $file->id }})" wire:confirm="Are you sure you want to delete this media asset? The file will be removed permanently from the server." class="absolute top-2 right-2 bg-black/70 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 hover:bg-red-600 transition z-20">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>

                <!-- Thumbnail -->
                <div class="aspect-square w-full bg-gray-50 dark:bg-gray-950 rounded-md overflow-hidden flex items-center justify-center border border-gray-100 dark:border-gray-850 relative">
                    @if($isImage)
                        <img src="{{ $file->url }}" alt="{{ $file->filename }}" class="w-full h-full object-cover">
                    @else
                        <!-- File placeholder icon -->
                        <div class="text-center p-2">
                            <span class="text-xl font-bold uppercase text-[#C8102E] tracking-wider block font-mono">{{ pathinfo($file->filename, PATHINFO_EXTENSION) }}</span>
                            <span class="text-[9px] text-gray-400 mt-1 block">Document</span>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="space-y-0.5">
                    <p class="text-[10px] font-bold text-gray-900 dark:text-white truncate max-w-full" title="{{ $file->filename }}">{{ $file->filename }}</p>
                    <p class="text-[9px] text-gray-400 font-mono flex justify-between">
                        <span>{{ number_format($file->size / 1024, 1) }} KB</span>
                    </p>
                    <input type="text" readonly value="{{ $file->url }}" onclick="this.select(); document.execCommand('copy'); alert('URL Copied!');" class="w-full bg-gray-50 dark:bg-gray-950 text-[9px] text-gray-450 border border-gray-200 dark:border-gray-800 rounded p-1 font-mono cursor-pointer text-center select-all hover:bg-gray-100" title="Click to copy asset URL">
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-xs text-gray-400">
                No media assets found matching the criteria.
            </div>
        @endforelse
    </div>
</div>

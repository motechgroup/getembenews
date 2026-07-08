<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $photo_url = '';
    public $photoFile;
    public string $bio = '';
    public string $website = '';
    public string $facebook = '';
    public string $twitter = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->photo_url = $user->photo_url ?? '';
        $this->bio = $user->bio ?? '';
        
        $socials = $user->social_links ?? [];
        $this->website = $socials['website'] ?? '';
        $this->facebook = $socials['facebook'] ?? '';
        $this->twitter = $socials['twitter'] ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
        ];

        if ($this->photoFile) {
            $rules['photoFile'] = ['required', 'image', 'max:2048'];
        } else {
            $rules['photo_url'] = ['nullable', 'url', 'max:255'];
        }

        $validated = $this->validate($rules);

        if ($this->photoFile) {
            $path = $this->photoFile->store('profiles', 'public');
            $this->photo_url = Storage::url($path);
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'photo_url' => $this->photo_url,
            'bio' => $validated['bio'],
            'social_links' => [
                'website' => $validated['website'] ?? '',
                'facebook' => $validated['facebook'] ?? '',
                'twitter' => $validated['twitter'] ?? '',
            ]
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
        $this->reset('photoFile');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Export all personal information for GDPR compliance.
     */
    public function exportPersonalData()
    {
        $user = Auth::user();
        
        $data = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'bio' => $user->bio,
                'photo_url' => $user->photo_url,
                'social_links' => $user->social_links,
                'created_at' => $user->created_at->toIso8601String(),
            ],
            'saved_articles' => $user->savedArticles()->get()->map(function ($article) {
                return [
                    'title' => $article->title,
                    'url' => url('/articles/' . $article->slug),
                ];
            })->toArray(),
            'comments' => $user->comments()->get()->map(function ($comment) {
                return [
                    'article_title' => $comment->article->title ?? 'Deleted Article',
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->toIso8601String(),
                ];
            })->toArray()
        ];
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, 'gdpr-data-export-' . $user->id . '.json', [
            'Content-Type' => 'application/json',
        ]);
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="photoFile" :value="__('Profile Picture')" />
            
            <div class="mt-2 flex items-center space-x-4">
                <!-- Preview container -->
                <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-250 dark:border-gray-700">
                    @if ($photoFile)
                        <img src="{{ $photoFile->temporaryUrl() }}" class="h-full w-full object-cover">
                    @elseif ($photo_url)
                        <img src="{{ $photo_url }}" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full flex items-center justify-center text-gray-400 dark:text-gray-605 font-bold uppercase text-xl">
                            {{ strtoupper(substr($name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <!-- File upload input -->
                <div class="space-y-1">
                    <input type="file" wire:model="photoFile" id="photoFile" class="hidden" accept="image/*">
                    <button type="button" onclick="document.getElementById('photoFile').click()" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md py-1.5 px-3 text-xs font-bold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        Choose Local Photo
                    </button>
                    <p class="text-[10px] text-gray-500">JPG, PNG, WEBP or GIF. Max 2MB.</p>
                </div>
            </div>
            
            <x-input-error class="mt-2" :messages="$errors->get('photoFile')" />
        </div>

        <div>
            <x-input-label for="bio" :value="__('Biography / Author Bio')" />
            <textarea wire:model="bio" id="bio" name="bio" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-650 focus:ring-indigo-500 dark:focus:ring-indigo-650 rounded-md shadow-sm text-sm" rows="3" placeholder="Tell us about yourself..."></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-gray-100 dark:border-gray-800 pt-4">
            <div>
                <x-input-label for="website" :value="__('Website URL')" />
                <x-text-input wire:model="website" id="website" name="website" type="url" class="mt-1 block w-full text-xs" placeholder="https://..." />
                <x-input-error class="mt-2" :messages="$errors->get('website')" />
            </div>
            <div>
                <x-input-label for="facebook" :value="__('Facebook URL')" />
                <x-text-input wire:model="facebook" id="facebook" name="facebook" type="url" class="mt-1 block w-full text-xs" placeholder="https://facebook.com/..." />
                <x-input-error class="mt-2" :messages="$errors->get('facebook')" />
            </div>
            <div>
                <x-input-label for="twitter" :value="__('Twitter / X URL')" />
                <x-text-input wire:model="twitter" id="twitter" name="twitter" type="url" class="mt-1 block w-full text-xs" placeholder="https://x.com/..." />
                <x-input-error class="mt-2" :messages="$errors->get('twitter')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>

    <div class="mt-8 border-t border-gray-150 dark:border-gray-800 pt-6">
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-150 uppercase tracking-wider mb-2">GDPR Data Portability</h3>
        <p class="text-xs text-gray-500 mb-4">Request a download of all personal information and activity records stored on Getembe News.</p>
        <x-secondary-button wire:click.prevent="exportPersonalData">
            Download My Data (JSON)
        </x-secondary-button>
    </div>
</section>

<?php

use App\Models\User;
use App\Support\Security;
use App\Models\Setting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $captchaToken = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        // 1. Blacklist verification
        if (Security::isBlacklisted($this->email)) {
            throw ValidationException::withMessages([
                'email' => 'This email address or domain is blocked from registration.',
            ]);
        }

        // 2. Captcha verification
        if (Setting::get('captcha_driver', 'none') !== 'none' && !Security::verifyCaptcha($this->captchaToken)) {
            throw ValidationException::withMessages([
                'captcha' => 'The captcha verification failed. Please try again.',
            ]);
        }

        // 3. Standard and Dynamic validations
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Security::passwordRules()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Default role is subscriber for new signups
        $validated['role'] = 'subscriber';

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Load Captcha SDK if enabled -->
    @if(Setting::get('captcha_driver', 'none') === 'recaptcha')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @elseif(Setting::get('captcha_driver', 'none') === 'turnstile')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <!-- Form Title -->
    <div class="mb-6 text-center">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Create Account</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Join Getembe News to save articles and comment</p>
    </div>

    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Captcha Display -->
        @if(Setting::get('captcha_driver', 'none') !== 'none')
            <div wire:ignore class="mt-4 flex justify-center">
                @if(Setting::get('captcha_driver') === 'recaptcha')
                    <div class="g-recaptcha" data-sitekey="{{ Setting::get('recaptcha_site_key') }}" data-callback="onRegisterCaptchaVerified"></div>
                @elseif(Setting::get('captcha_driver') === 'turnstile')
                    <div class="cf-turnstile" data-sitekey="{{ Setting::get('turnstile_site_key') }}" data-callback="onRegisterCaptchaVerified"></div>
                @endif
            </div>
            <script>
                function onRegisterCaptchaVerified(token) {
                    @this.set('captchaToken', token);
                }
            </script>
            <x-input-error :messages="$errors->get('captcha')" class="mt-2 text-center" />
        @endif

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 bg-[#cc6c3b] hover:bg-orange-700 active:bg-orange-800 text-white font-bold text-xs uppercase tracking-widest rounded-lg transition shadow-sm focus:outline-none focus:ring-2 focus:ring-[#cc6c3b] focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                {{ __('Register') }}
            </button>
        </div>

        @php
            $googleEnabled = (bool) \App\Models\Setting::get('google_login', false);
            $facebookEnabled = (bool) \App\Models\Setting::get('facebook_login', false);
            $githubEnabled = (bool) \App\Models\Setting::get('github_login', false);
            $hasSocial = $googleEnabled || $facebookEnabled || $githubEnabled;
        @endphp

        @if($hasSocial)
            <div class="mt-6 space-y-3">
                <div class="relative flex items-center justify-center my-4">
                    <div class="border-t border-gray-200 dark:border-gray-800 w-full"></div>
                    <span class="bg-white dark:bg-gray-900 px-3 text-[10px] uppercase font-bold text-gray-400 shrink-0">Or register with</span>
                    <div class="border-t border-gray-200 dark:border-gray-800 w-full"></div>
                </div>

                <div class="space-y-2">
                    @if($googleEnabled)
                        <a href="{{ route('social.redirect', 'google') }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-200 font-bold text-xs rounded-lg transition shadow-sm">
                            <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.28v3.13C3.26 21.3 7.37 24 12 24z"/>
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.6H1.28C.46 8.23 0 10.06 0 12s.46 3.77 1.28 5.4l4-3.13z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.26 2.7 1.28 6.6l4 3.13c.95-2.83 3.6-4.98 6.72-4.98z"/>
                            </svg>
                            <span>Sign up with Google</span>
                        </a>
                    @endif

                    @if($facebookEnabled)
                        <a href="{{ route('social.redirect', 'facebook') }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-[#1877F2] hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                            <svg class="h-4 w-4 mr-2 fill-current" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span>Sign up with Facebook</span>
                        </a>
                    @endif

                    @if($githubEnabled)
                        <a href="{{ route('social.redirect', 'github') }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-gray-900 hover:bg-black text-white font-bold text-xs rounded-lg transition shadow-sm">
                            <svg class="h-4 w-4 mr-2 fill-current" viewBox="0 0 24 24">
                                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                            </svg>
                            <span>Sign up with GitHub</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Already registered? Link -->
        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400 border-t border-gray-150 dark:border-gray-800/80 pt-4">
            {{ __('Already registered?') }}
            <a href="{{ route('login') }}" class="font-bold text-[#cc6c3b] hover:underline ml-1" wire:navigate>
                {{ __('Log in') }}
            </a>
        </div>
    </form>
</div>

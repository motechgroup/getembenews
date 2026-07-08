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

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>

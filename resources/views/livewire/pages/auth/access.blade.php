<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming staff authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if (auth()->user()->isStaff()) {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div>
    <!-- Session Status & Error Alert -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs rounded-lg p-3 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Title -->
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center p-2.5 bg-[#cc6c3b]/10 text-[#cc6c3b] rounded-full mb-2">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Staff & Role Access</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Portal for Administrators, Editors, Authors, and Managers</p>
    </div>

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Staff Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-[#cc6c3b] focus:ring-[#cc6c3b] dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-gray-500 dark:text-gray-400 hover:text-[#cc6c3b] transition" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 bg-[#cc6c3b] hover:bg-orange-700 active:bg-orange-800 text-white font-bold text-xs uppercase tracking-widest rounded-lg transition shadow-sm focus:outline-none focus:ring-2 focus:ring-[#cc6c3b] focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                {{ __('Authenticate & Enter') }}
            </button>
        </div>

        <!-- Public Reader Link -->
        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400 border-t border-gray-150 dark:border-gray-800/80 pt-4">
            {{ __('Public Reader?') }}
            <a href="{{ route('login') }}" class="font-bold text-[#cc6c3b] hover:underline ml-1" wire:navigate>
                {{ __('Sign in with Google') }}
            </a>
        </div>
    </form>
</div>

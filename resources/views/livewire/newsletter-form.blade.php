<?php

use function Livewire\Volt\{state, rules};
use App\Models\Newsletter;

state(['email' => '', 'subscribed' => false]);

rules(['email' => 'required|email|unique:newsletters,email']);

$subscribe = function () {
    $this->validate();

    // Rate Limit: 5 newsletter subscriptions per IP per minute
    $ip = request()->ip();
    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('newsletter-subscribe:' . $ip, 5)) {
        $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('newsletter-subscribe:' . $ip);
        $this->addError('email', "Too many requests. Please try again in {$seconds} seconds.");
        return;
    }
    \Illuminate\Support\Facades\RateLimiter::hit('newsletter-subscribe:' . $ip, 60);

    Newsletter::create([
        'email' => $this->email,
        'is_active' => true
    ]);

    \App\Support\Mailer::sendWelcome($this->email);

    $this->email = '';
    $this->subscribed = true;
};

?>

<div>
    @if($subscribed)
        <div class="p-3 bg-green-900/20 border border-green-800 text-green-300 rounded text-xs">
            Thank you! You have successfully subscribed to our newsletter.
        </div>
    @else
        <form wire:submit.prevent="subscribe" class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
            <input type="email" wire:model="email" placeholder="Your email address" required
                   class="bg-gray-800 border border-gray-700 text-white rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] flex-1">
            <button type="submit" class="bg-[#C8102E] hover:opacity-90 text-white text-xs font-bold px-4 py-2 rounded transition">
                Subscribe
            </button>
        </form>
        @error('email')
            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
        @enderror
    @endif
</div>

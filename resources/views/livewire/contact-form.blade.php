<?php

use function Livewire\Volt\{state, rules};
use App\Models\Setting;
use App\Support\Security;

state(['name' => '', 'email' => '', 'subject' => 'General Inquiry', 'message' => '', 'captchaToken' => '', 'submitted' => false]);

rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'subject' => 'required|string|max:255',
    'message' => 'required|string|max:2000',
]);

$submit = function () {
    $this->validate();

    // 1. Blacklist check
    if (Security::isBlacklisted($this->email)) {
        $this->addError('email', 'This email address is currently blocked from sending messages.');
        return;
    }

    // 2. Captcha verification check
    if (Setting::get('captcha_driver', 'none') !== 'none' && !Security::verifyCaptcha($this->captchaToken)) {
        $this->addError('message', 'Human verification (Captcha) failed. Please try again.');
        return;
    }

    // Rate Limit: 3 contact messages per IP per minute
    $ip = request()->ip();
    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('contact-submit:' . $ip, 3)) {
        $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn('contact-submit:' . $ip);
        $this->addError('message', "Too many messages sent. Please try again in {$seconds} seconds.");
        return;
    }
    \Illuminate\Support\Facades\RateLimiter::hit('contact-submit:' . $ip, 60);

    $contactData = [
        'name' => strip_tags(trim($this->name)),
        'email' => strip_tags(trim(strtolower($this->email))),
        'subject' => strip_tags(trim($this->subject)),
        'message' => strip_tags(trim($this->message)),
    ];

    \App\Models\ContactMessage::create($contactData);
    
    // Trigger admin email alert
    \App\Support\Mailer::sendContactAlert($contactData);
    
    $this->reset(['name', 'email', 'subject', 'message', 'captchaToken']);
    $this->submitted = true;
};

?>

<div>
    <!-- Load Captcha SDK if enabled -->
    @if(Setting::get('captcha_driver', 'none') === 'recaptcha')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @elseif(Setting::get('captcha_driver', 'none') === 'turnstile')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 shadow-sm">
        @if($submitted)
            <div class="p-4 bg-green-100 dark:bg-green-950/20 border border-green-200 dark:border-green-900 text-green-800 dark:text-green-400 rounded text-xs font-semibold">
                Thank you for contacting Getembe News! Your message has been sent successfully. Our editorial desk will review your submission shortly.
            </div>
        @else
            <form wire:submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Your Name</label>
                        <input type="text" wire:model="name" required placeholder="John Doe"
                               class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                        @error('name') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Your Email</label>
                        <input type="email" wire:model="email" required placeholder="john@example.com"
                               class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                        @error('email') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Subject</label>
                    <select wire:model="subject" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Editorial News Tip">Editorial News Tip (Anonymous)</option>
                        <option value="Advertising & Sponsorship">Advertising & Sponsorship</option>
                        <option value="Technical Support">Technical Support</option>
                    </select>
                    @error('subject') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Message / Tip Details</label>
                    <textarea wire:model="message" rows="5" required placeholder="Write your message here..."
                              class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white"></textarea>
                    @error('message') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                </div>

                <!-- Captcha Display -->
                @if(Setting::get('captcha_driver', 'none') !== 'none')
                    <div wire:ignore class="my-4 flex justify-center">
                        @if(Setting::get('captcha_driver') === 'recaptcha')
                            <div class="g-recaptcha" data-sitekey="{{ Setting::get('recaptcha_site_key') }}" data-callback="onContactCaptchaVerified"></div>
                        @elseif(Setting::get('captcha_driver') === 'turnstile')
                            <div class="cf-turnstile" data-sitekey="{{ Setting::get('turnstile_site_key') }}" data-callback="onContactCaptchaVerified"></div>
                        @endif
                    </div>
                    <script>
                        function onContactCaptchaVerified(token) {
                            @this.set('captchaToken', token);
                        }
                    </script>
                @endif

                <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition w-full sm:w-auto">
                    Send Message
                </button>
            </form>
        @endif
    </div>
</div>

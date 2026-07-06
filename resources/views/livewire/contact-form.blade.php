<?php

use function Livewire\Volt\{state, rules};

state(['name' => '', 'email' => '', 'subject' => 'General Inquiry', 'message' => '', 'submitted' => false]);

rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'subject' => 'required|string|max:255',
    'message' => 'required|string|max:2000',
]);

$submit = function () {
    $this->validate();

    \App\Models\ContactMessage::create([
        'name' => $this->name,
        'email' => $this->email,
        'subject' => $this->subject,
        'message' => $this->message,
    ]);
    
    $this->reset(['name', 'email', 'subject', 'message']);
    $this->submitted = true;
};

?>

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

            <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition">
                Send Message
            </button>
        </form>
    @endif
</div>

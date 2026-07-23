<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

use App\Http\Controllers\Auth\SocialAuthController;

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')
        ->name('register');

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Volt::route('access', 'pages.auth.access')
        ->name('access');

    Volt::route('admin/login', 'pages.auth.access')
        ->name('admin.login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');

    Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
        ->name('social.redirect');

    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
        ->name('social.callback');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');

    Route::post('logout', function (\App\Livewire\Actions\Logout $logout) {
        $logout();
        return redirect('/');
    })->name('logout');
});

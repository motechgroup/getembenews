<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Support\Security;
use App\Models\Setting;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            // Track account-level failed login attempts (protect against credential stuffing)
            $maxAttempts = (int) Setting::get('login_max_attempts', 5);
            $lockoutSeconds = (int) Setting::get('login_lockout_duration', 900);
            
            $locked = Security::incrementFailedLogin($this->email, $maxAttempts, $lockoutSeconds);

            if ($locked) {
                throw ValidationException::withMessages([
                    'form.email' => "This account has been locked due to too many failed login attempts. Please try again in " . ceil($lockoutSeconds / 60) . " minutes.",
                ]);
            }

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Security::clearFailedLogin($this->email);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        // 1. Check account-level lockout first
        if (Security::isAccountLocked($this->email)) {
            $seconds = Security::lockoutRemaining($this->email);
            throw ValidationException::withMessages([
                'form.email' => "This account is temporarily locked. Please try again in {$seconds} seconds.",
            ]);
        }

        // 2. Check standard IP rate limiting
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}

<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Mailer
{
    /**
     * Dynamically override mailer configuration settings on-the-fly.
     */
    public static function configure(): void
    {
        $driver = Setting::get('email_driver', 'smtp');

        if ($driver === 'mailgun') {
            Config::set('mail.default', 'mailgun');
            Config::set('services.mailgun.domain', Setting::get('mailgun_domain'));
            Config::set('services.mailgun.secret', Setting::get('mailgun_secret'));
            Config::set('services.mailgun.endpoint', Setting::get('mailgun_endpoint', 'api.mailgun.net'));
        } elseif ($driver === 'brevo') {
            // Brevo uses direct SMTP relay configuration with API Key as the password
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', 'smtp-relay.brevo.com');
            Config::set('mail.mailers.smtp.port', 587);
            Config::set('mail.mailers.smtp.username', Setting::get('brevo_username', Setting::get('contact_email', 'no-reply@getembenews.com')));
            Config::set('mail.mailers.smtp.password', Setting::get('brevo_api_key'));
            Config::set('mail.mailers.smtp.encryption', 'tls');
        } else {
            // Standard SMTP Configuration
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', Setting::get('smtp_server', 'smtp.mailtrap.io'));
            Config::set('mail.mailers.smtp.port', (int) Setting::get('smtp_port', 2525));
            Config::set('mail.mailers.smtp.username', Setting::get('smtp_username'));
            Config::set('mail.mailers.smtp.password', Setting::get('smtp_password'));
            Config::set('mail.mailers.smtp.encryption', Setting::get('smtp_encryption', 'tls'));
        }

        // Standard Sender config
        $fromEmail = Setting::get('smtp_from_email', 'no-reply@getembenews.com');
        $fromName = Setting::get('smtp_from_name', 'Getembe News');
        Config::set('mail.from.address', $fromEmail);
        Config::set('mail.from.name', $fromName);
    }

    /**
     * Send welcoming email to a new newsletter subscriber.
     */
    public static function sendWelcome(string $email): bool
    {
        self::configure();

        try {
            Mail::send('emails.welcome', ['email' => $email], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Welcome to Getembe News Newsletter!');
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send welcome email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send periodic news digest email to a subscriber.
     */
    public static function sendDigest(string $email, $articles): bool
    {
        self::configure();

        try {
            Mail::send('emails.digest', ['email' => $email, 'articles' => $articles], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Your Weekly News Digest - Getembe News');
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send digest email to {$email}: " . $e->getMessage());
            return false;
        }
    }
}

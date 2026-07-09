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

        $subject = Setting::get('email_template_welcome_subject', 'Welcome to Getembe News Newsletter!');
        $body = Setting::get('email_template_welcome_body', "Thank you for subscribing to Getembe News. You are now part of our growing community of readers who value fast, reliable, and in-depth local news and analysis from Kisii County and beyond.");

        try {
            Mail::send('emails.welcome', ['email' => $email, 'body' => $body], function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject);
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

    /**
     * Send a test email to verify SMTP configuration is working.
     */
    public static function sendTestEmail(string $recipient): bool
    {
        self::configure();

        try {
            Mail::send('emails.test_email', ['recipient' => $recipient], function ($message) use ($recipient) {
                $message->to($recipient)
                        ->subject('SMTP Connection Test - Getembe News');
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("SMTP Test email to {$recipient} failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email alert to admin when a new contact message/news tip is received.
     */
    public static function sendContactAlert(array $contactMessage): bool
    {
        self::configure();

        $adminEmail = Setting::get('contact_email', 'admin@getembenews.com');
        $subject = Setting::get('email_template_contact_subject', 'New Contact Inquiry: [Subject]');
        $body = Setting::get('email_template_contact_body', "A new contact message / anonymous tip has been received through the Getembe News website form.\n\nSender Name: [Name]\nSender Email: [Email]\nSubject: [Subject]\n\nMessage details:\n[Message]");

        $subject = str_replace('[Subject]', $contactMessage['subject'], $subject);
        $body = str_replace(
            ['[Name]', '[Email]', '[Subject]', '[Message]'],
            [$contactMessage['name'], $contactMessage['email'], $contactMessage['subject'], $contactMessage['message']],
            $body
        );

        try {
            Mail::send('emails.contact_alert', ['msg' => $contactMessage, 'body' => $body], function ($message) use ($adminEmail, $subject) {
                $message->to($adminEmail)
                        ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send contact alert email to admin: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email notification to user when their announcement status is updated.
     */
    public static function sendAnnouncementStatus(object $announcement): bool
    {
        self::configure();

        $email = $announcement->visitor_email;
        if (!$email) return false;

        $subject = Setting::get('email_template_announcement_subject', 'Announcement Status Update - Getembe News');
        $body = Setting::get('email_template_announcement_body', "This is to inform you that the status of your announcement submitted to Getembe News has been updated.\n\nAnnouncement ID: #[ID]\nType / Category: [Type]\nPayment Status: [PaymentStatus]\nTotal Charged: [Amount]\nApproval: [ApprovalStatus]\n\nAnnouncement Content:\n\"[Content]\"");

        $body = str_replace(
            ['[Name]', '[ID]', '[Type]', '[PaymentStatus]', '[Amount]', '[ApprovalStatus]', '[Content]'],
            [
                $announcement->visitor_name,
                $announcement->id,
                ucfirst($announcement->type),
                strtoupper($announcement->payment_status),
                Setting::get('currency_symbol', 'KSh') . ' ' . number_format($announcement->total_amount),
                $announcement->is_approved ? 'APPROVED' : 'PENDING REVIEW',
                $announcement->content
            ],
            $body
        );

        try {
            Mail::send('emails.announcement_status', ['announcement' => $announcement, 'body' => $body], function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send announcement status email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a breaking news alert to a subscriber.
     */
    public static function sendBreakingNewsAlert(string $email, object $article): bool
    {
        self::configure();

        $subject = Setting::get('email_template_breaking_subject', '🔴 BREAKING NEWS: [Title]');
        $body = Setting::get('email_template_breaking_body', "We are reaching out to you with a major news alert just published on the Getembe News website.\n\n[Title]\n\n[Subtitle]\n\nOur editorial team is currently tracking this story and updates will be posted in real time.");

        $subject = str_replace('[Title]', $article->title, $subject);
        $body = str_replace(
            ['[Title]', '[Subtitle]'],
            [$article->title, $article->subtitle],
            $body
        );

        try {
            Mail::send('emails.breaking_news', ['email' => $email, 'article' => $article, 'body' => $body], function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send breaking news email alert to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a published article to all active newsletter subscribers.
     */
    public static function sendArticleNewsletter(object $article): int
    {
        self::configure();

        $subscribers = \App\Models\Newsletter::where('is_active', true)->get();
        if ($subscribers->isEmpty()) {
            return 0;
        }

        $subject = Setting::get('email_template_newsletter_article_subject', '📰 [Title] - Getembe News');
        $body = Setting::get('email_template_newsletter_article_body', "We are excited to share our latest published article with you:\n\n[Title]\n\n[Subtitle]\n\nClick the link below to read the full story on our website.");

        $subject = str_replace('[Title]', $article->title, $subject);
        $body = str_replace(
            ['[Title]', '[Subtitle]'],
            [$article->title, $article->subtitle],
            $body
        );

        $sentCount = 0;
        foreach ($subscribers as $sub) {
            try {
                Mail::send('emails.breaking_news', ['email' => $sub->email, 'article' => $article, 'body' => $body], function ($message) use ($sub, $subject) {
                    $message->to($sub->email)
                            ->subject($subject);
                });
                $sentCount++;
            } catch (\Exception $e) {
                Log::warning("Failed to send newsletter article email to {$sub->email}: " . $e->getMessage());
            }
        }

        return $sentCount;
    }

    /**
     * Send a password reset link to user.
     */
    public static function sendPasswordReset(string $email, string $token): bool
    {
        self::configure();

        $subject = Setting::get('email_template_password_reset_subject', 'Reset Password Notification - Getembe News');
        $body = Setting::get('email_template_password_reset_body', "You are receiving this email because we received a password reset request for your account.\n\nThis password reset link will expire in 60 minutes.\n\nIf you did not request a password reset, no further action is required.");
        
        $url = url(route('password.reset', ['token' => $token, 'email' => $email], false));

        try {
            Mail::send('emails.password_reset', ['email' => $email, 'body' => $body, 'url' => $url], function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send password reset email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email verification link to user.
     */
    public static function sendEmailVerification(string $email, string $url): bool
    {
        self::configure();

        $subject = Setting::get('email_template_verification_subject', 'Verify Email Address - Getembe News');
        $body = Setting::get('email_template_verification_body', "Please click the button below to verify your email address.\n\nIf you did not create an account, no further action is required.");

        try {
            Mail::send('emails.verification', ['email' => $email, 'body' => $body, 'url' => $url], function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send email verification to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send new staff account notification email with credentials details.
     */
    public static function sendNewAccountNotification(object $user, string $password): bool
    {
        self::configure();

        $subject = Setting::get('email_template_new_account_subject', 'Staff Account Created - Getembe News');
        $body = Setting::get('email_template_new_account_body', "Hello [Name],\n\nAn administrator has created a staff account for you on the Getembe News platform.\n\nHere are your access credentials:\n- Login Email: [Email]\n- Temporary Password: [Password]\n\nRole Assigned: [Role]");

        $body = str_replace(
            ['[Name]', '[Email]', '[Password]', '[Role]'],
            [$user->name, $user->email, $password, ucfirst($user->role)],
            $body
        );

        $url = url('/login');

        try {
            Mail::send('emails.new_account', ['email' => $user->email, 'body' => $body, 'url' => $url], function ($message) use ($user, $subject) {
                $message->to($user->email)
                        ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::warning("Failed to send new staff account email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }
}

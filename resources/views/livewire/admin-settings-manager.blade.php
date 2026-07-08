<?php

use function Livewire\Volt\{state, mount, uses};
use Livewire\WithFileUploads;
use App\Models\Setting;
use App\Models\Newsletter;
use App\Models\BreakingNews;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(WithFileUploads::class);

state([
    // Navigation active tab
    'activeTab' => 'identity',
    'saved' => false,

    // File Upload Temporary States
    'uploadedLogo' => null,
    'uploadedFavicon' => null,

    // Featured & Breaking News states
    'breaking_title' => '',
    'breaking_link' => '',
    'breaking_priority' => 1,
    'breaking_expires_at' => '',
    'breaking_news_list' => fn() => BreakingNews::orderBy('created_at', 'desc')->get(),
    'pinned_articles_list' => fn() => Article::where('is_pinned', true)->orWhere('is_featured', true)->get(),

    // 1. Site Identity & Theme Settings
    'site_name' => fn() => Setting::get('site_name', 'Getembe News'),
    'site_logo' => fn() => Setting::get('site_logo', ''),
    'brand_color' => fn() => Setting::get('brand_color', '#C8102E'),
    'favicon' => fn() => Setting::get('favicon', ''),
    'theme_font' => fn() => Setting::get('theme_font', 'Inter'),
    'theme_layout' => fn() => Setting::get('theme_layout', 'standard'),
    'theme_color_secondary' => fn() => Setting::get('theme_color_secondary', '#222222'),
    'theme_color_success' => fn() => Setting::get('theme_color_success', '#10B981'),
    'theme_color_warning' => fn() => Setting::get('theme_color_warning', '#F59E0B'),

    // Homepage, Weather & Stream urls
    'homepage_categories' => fn() => Setting::get('homepage_categories', 'politics,business,technology,sports'),
    'weather_city' => fn() => Setting::get('weather_city', 'Kisii'),
    'live_tv_url' => fn() => Setting::get('live_tv_url', 'https://www.youtube.com/embed/5Peo-ivmupE'),
    'live_radio_url' => fn() => Setting::get('live_radio_url', 'http://stream.zeno.fm/f5r7x1t1zv8uv'),

    // Mobile App Settings
    'app_play_store_url' => fn() => Setting::get('app_play_store_url', 'https://play.google.com/store'),
    'app_app_store_url' => fn() => Setting::get('app_app_store_url', 'https://www.apple.com/app-store'),
    'app_banner_title' => fn() => Setting::get('app_banner_title', 'Download Getembe News Mobile App'),
    'app_banner_desc' => fn() => Setting::get('app_banner_desc', 'Get fast, reliable, and breaking news alerts directly on your smartphone. Available now for Android and iOS devices.'),

    // 2. Social Links
    'website' => fn() => Setting::get('website', ''),
    'facebook' => fn() => Setting::get('facebook', ''),
    'twitter' => fn() => Setting::get('twitter', ''),
    'instagram' => fn() => Setting::get('instagram', ''),
    'linkedin' => fn() => Setting::get('linkedin', ''),
    'whatsapp' => fn() => Setting::get('whatsapp', ''),
    'youtube' => fn() => Setting::get('youtube', ''),
    'tiktok' => fn() => Setting::get('tiktok', ''),
    'snapchat' => fn() => Setting::get('snapchat', ''),
    'telegram' => fn() => Setting::get('telegram', ''),
    'pinterest' => fn() => Setting::get('pinterest', ''),
    'threads' => fn() => Setting::get('threads', ''),
    'other_social_links' => fn() => Setting::get('other_social_links', ''),

    // 3. Contact Settings
    'contact_email' => fn() => Setting::get('contact_email', 'contact@getembenews.com'),
    'contact_phone' => fn() => Setting::get('contact_phone', '+254712345678'),
    'contact_open_hours' => fn() => Setting::get('contact_open_hours', "Monday - Friday: 8:00 AM - 5:00 PM\nSaturday: 8:00 AM - 1:00 PM\nSunday: Closed"),
    'contact_address' => fn() => Setting::get('contact_address', 'Kisii, Kenya'),

    // 4. Payment Settings
    'payment_methods' => fn() => Setting::get('payment_methods', 'M-Pesa, Card'),
    'payment_gateways' => fn() => Setting::get('payment_gateways', 'Flutterwave, Stripe'),

    // 5. Currency Settings
    'currency' => fn() => Setting::get('currency', 'KES'),
    'currency_symbol' => fn() => Setting::get('currency_symbol', 'KSh'),

    // Announcement word rates
    'announcement_rate_tv' => fn() => Setting::get('announcement_rate_tv', '5'),
    'announcement_rate_radio' => fn() => Setting::get('announcement_rate_radio', '3'),
    'announcement_rate_both' => fn() => Setting::get('announcement_rate_both', '7'),

    // 6. Language Settings
    'language' => fn() => Setting::get('language', 'en'),

    // 7. SEO Settings
    'meta_title' => fn() => Setting::get('meta_title', 'Getembe News - Kisii County Leading Digital News Platform'),
    'meta_description' => fn() => Setting::get('meta_description', 'Your leading source for politics, business, technology, sports, and regional news.'),
    'meta_keywords' => fn() => Setting::get('meta_keywords', 'news, getembe, kisii, kenya, politics, sports'),
    'google_analytics_id' => fn() => Setting::get('google_analytics_id', ''),
    'google_indexing_api' => fn() => Setting::get('google_indexing_api', false),
    'sitemap_frequency' => fn() => Setting::get('sitemap_frequency', 'daily'),
    'robots_txt_enabled' => fn() => Setting::get('robots_txt_enabled', true),
    'robots_txt_content' => fn() => Setting::get('robots_txt_content', "User-agent: *\nDisallow: /admin\nSitemap: http://localhost:8000/sitemap.xml"),

    // 8. Cookie settings
    'cookie_banner_enabled' => fn() => Setting::get('cookie_banner_enabled', true),
    'cookie_position' => fn() => Setting::get('cookie_position', 'bottom'),
    'cookie_approval_required' => fn() => Setting::get('cookie_approval_required', false),
    'cookie_moderation_enabled' => fn() => Setting::get('cookie_moderation_enabled', false),

    // 9. Email SMTP Settings
    'smtp_server' => fn() => Setting::get('smtp_server', 'smtp.mailtrap.io'),
    'smtp_port' => fn() => Setting::get('smtp_port', '2525'),
    'smtp_username' => fn() => Setting::get('smtp_username', ''),
    'smtp_password' => fn() => Setting::get('smtp_password', ''),
    'smtp_encryption' => fn() => Setting::get('smtp_encryption', 'tls'),
    'smtp_auth_enabled' => fn() => Setting::get('smtp_auth_enabled', true),
    'smtp_from_name' => fn() => Setting::get('smtp_from_name', 'Getembe News'),
    'smtp_from_email' => fn() => Setting::get('smtp_from_email', 'no-reply@getembenews.com'),
    'smtp_reply_to_email' => fn() => Setting::get('smtp_reply_to_email', ''),
    'smtp_reply_to_name' => fn() => Setting::get('smtp_reply_to_name', ''),

    // 10. Facebook comments settings
    'fb_comments_widget' => fn() => Setting::get('fb_comments_widget', false),
    'fb_comments_position' => fn() => Setting::get('fb_comments_position', 'bottom'),
    'fb_comments_approval_required' => fn() => Setting::get('fb_comments_approval_required', false),
    'fb_comments_moderation_enabled' => fn() => Setting::get('fb_comments_moderation_enabled', false),

    // 11. Page Settings
    'home_page' => fn() => Setting::get('home_page', 'welcome'),
    'about_page' => fn() => Setting::get('about_page', 'about'),
    'contact_page' => fn() => Setting::get('contact_page', 'contact'),
    'privacy_page' => fn() => Setting::get('privacy_page', 'privacy'),
    'terms_page' => fn() => Setting::get('terms_page', 'terms'),
    'privacy_content' => fn() => Setting::get('privacy_content', Setting::defaultPrivacyContent()),
    'terms_content' => fn() => Setting::get('terms_content', Setting::defaultTermsContent()),
    'podcast_category_enabled' => fn() => (bool) Setting::get('podcast_category_enabled', true),

    // 12. Footer Settings
    'footer_copyright' => fn() => Setting::get('footer_copyright', 'Getembe News. All rights reserved.'),
    'footer_bg_color' => fn() => Setting::get('footer_bg_color', '#111827'),
    'footer_text_color' => fn() => Setting::get('footer_text_color', '#D1D5DB'),
    'footer_logo' => fn() => Setting::get('footer_logo', ''),
    'footer_link_color' => fn() => Setting::get('footer_link_color', '#3B82F6'),
    'footer_link_hover_color' => fn() => Setting::get('footer_link_hover_color', '#2563EB'),
    'footer_link_active_color' => fn() => Setting::get('footer_link_active_color', '#1D4ED8'),
    'footer_link_visited_color' => fn() => Setting::get('footer_link_visited_color', '#4B5563'),

    // 13. social login settings
    'google_login' => fn() => Setting::get('google_login', false),
    'facebook_login' => fn() => Setting::get('facebook_login', false),
    'twitter_login' => fn() => Setting::get('twitter_login', false),
    'github_login' => fn() => Setting::get('github_login', false),
    'linkedin_login' => fn() => Setting::get('linkedin_login', false),
    'whatsapp_login' => fn() => Setting::get('whatsapp_login', false),
    'apple_login' => fn() => Setting::get('apple_login', false),
    'pinterest_login' => fn() => Setting::get('pinterest_login', false),
    'threads_login' => fn() => Setting::get('threads_login', false),

    // OAuth Client Credentials
    'google_client_id' => fn() => Setting::get('google_client_id', ''),
    'google_client_secret' => fn() => Setting::get('google_client_secret', ''),
    'facebook_client_id' => fn() => Setting::get('facebook_client_id', ''),
    'facebook_client_secret' => fn() => Setting::get('facebook_client_secret', ''),
    'github_client_id' => fn() => Setting::get('github_client_id', ''),
    'github_client_secret' => fn() => Setting::get('github_client_secret', ''),
    'twitter_client_id' => fn() => Setting::get('twitter_client_id', ''),
    'twitter_client_secret' => fn() => Setting::get('twitter_client_secret', ''),

    // 15. Notification Settings
    'notifications_enabled' => fn() => Setting::get('notifications_enabled', true),
    'notifications_push' => fn() => Setting::get('notifications_push', false),
    'notifications_in_app' => fn() => Setting::get('notifications_in_app', true),
    'notifications_email' => fn() => Setting::get('notifications_email', true),

    // 16. Advertising Settings
    'adsense_enabled' => fn() => Setting::get('adsense_enabled', false),
    'adsense_client_id' => fn() => Setting::get('adsense_client_id', ''),
    'adsense_code' => fn() => Setting::get('adsense_code', ''),
    'facebook_ads_enabled' => fn() => Setting::get('facebook_ads_enabled', false),
    'facebook_ads_code' => fn() => Setting::get('facebook_ads_code', ''),
    'custom_ads_enabled' => fn() => Setting::get('custom_ads_enabled', true),
    'ad_top_image' => fn() => Setting::get('ad_top_image', ''),
    'ad_top_link' => fn() => Setting::get('ad_top_link', ''),
    'ad_sidebar_image' => fn() => Setting::get('ad_sidebar_image', ''),
    'ad_sidebar_link' => fn() => Setting::get('ad_sidebar_link', ''),
    'ad_inline_image' => fn() => Setting::get('ad_inline_image', ''),
    'ad_inline_link' => fn() => Setting::get('ad_inline_link', ''),
    'ad_footer_image' => fn() => Setting::get('ad_footer_image', ''),
    'ad_footer_link' => fn() => Setting::get('ad_footer_link', ''),
    'ad_mobile_sticky_image' => fn() => Setting::get('ad_mobile_sticky_image', ''),
    'ad_mobile_sticky_link' => fn() => Setting::get('ad_mobile_sticky_link', ''),
    'uploadedTopAd' => null,
    'uploadedSidebarAd' => null,
    'uploadedInlineAd' => null,
    'uploadedFooterAd' => null,
    'uploadedMobileStickyAd' => null,

    // Dynamic Lists inputs / States
    'newRoleName' => '',
    'newRoleSlug' => '',
    'newRoleDescription' => '',
    'selectedRoleForPermissions' => 'editor',
    'selectedRolePermissions' => [],

    'newSubscriberEmail' => '',
    'newsletterSearch' => '',

    'newPollQuestion' => '',
    'newPollOptions' => '',

    'newQuizTitle' => '',
    'newQuizQuestions' => '',

    'newRssFeedName' => '',
    'newRssFeedUrl' => '',

    'newWebhookName' => '',
    'newWebhookUrl' => '',

    'newApiKeyName' => '',

    // Stream Schedules
    'tv_schedule' => [],
    'radio_schedule' => [],
    'activeScheduleTab' => 'tv', // tv, radio
    'activeScheduleDay' => 'monday', // monday, tuesday, etc.

    // Temp program inputs
    'newTvTime' => '',
    'newTvTitle' => '',
    'newTvDesc' => '',

    'newRadioTime' => '',
    'newRadioTitle' => '',
    'newRadioDesc' => '',

    // Security & Privacy Settings States
    'captcha_driver' => fn() => Setting::get('captcha_driver', 'none'),
    'recaptcha_site_key' => fn() => Setting::get('recaptcha_site_key', ''),
    'recaptcha_secret_key' => fn() => Setting::get('recaptcha_secret_key', ''),
    'turnstile_site_key' => fn() => Setting::get('turnstile_site_key', ''),
    'turnstile_secret_key' => fn() => Setting::get('turnstile_secret_key', ''),
    'email_blacklist' => fn() => Setting::get('email_blacklist', ''),
    'password_min_length' => fn() => Setting::get('password_min_length', 8),
    'password_complexity_required' => fn() => Setting::get('password_complexity_required', false),
    'login_max_attempts' => fn() => Setting::get('login_max_attempts', 5),
    'login_lockout_duration' => fn() => Setting::get('login_lockout_duration', 900),
    'seo_nofollow_links' => fn() => Setting::get('seo_nofollow_links', true),
    'seo_strip_links' => fn() => Setting::get('seo_strip_links', false),
    'author_reward_rate' => fn() => Setting::get('author_reward_rate', '0.10'),
    'email_driver' => fn() => Setting::get('email_driver', 'smtp'),
    'mailgun_domain' => fn() => Setting::get('mailgun_domain', ''),
    'mailgun_secret' => fn() => Setting::get('mailgun_secret', ''),
    'mailgun_endpoint' => fn() => Setting::get('mailgun_endpoint', 'api.mailgun.net'),
    'brevo_username' => fn() => Setting::get('brevo_username', ''),
    'brevo_api_key' => fn() => Setting::get('brevo_api_key', ''),
    'newsletter_popup_enabled' => fn() => (bool) Setting::get('newsletter_popup_enabled', true),
    'newsletter_popup_title' => fn() => Setting::get('newsletter_popup_title', 'Subscribe to our Newsletter'),
    'newsletter_popup_description' => fn() => Setting::get('newsletter_popup_description', 'Get the latest breaking news alerts and regional updates delivered directly to your inbox.'),
    'newsletter_popup_delay' => fn() => (int) Setting::get('newsletter_popup_delay', 3),
    'uploaded_subscribers_file' => null,
]);

mount(function ($activeTab = 'identity') {
    $this->activeTab = $activeTab;

    $defaultTvFlat = [
        ['time' => '06:00 - 09:00', 'title' => 'Getembe Morning Call', 'desc' => 'Breakfast news and newspaper review.', 'is_playing' => false],
        ['time' => '09:00 - 12:00', 'title' => 'Business Daily', 'desc' => 'Economic trends, stock updates, and trade discussion.', 'is_playing' => false],
        ['time' => '12:00 - 14:00', 'title' => 'News Hour Live', 'desc' => 'Midday headlines, market check, and regional briefs.', 'is_playing' => true],
        ['time' => '14:00 - 16:00', 'title' => 'Health & Sports Highlights', 'desc' => 'Wellness insights and sporting roundups.', 'is_playing' => false],
        ['time' => '16:00 - 19:00', 'title' => 'Regional News Express', 'desc' => 'Community spotlights and county assembly briefings.', 'is_playing' => false],
        ['time' => '19:00 - 21:00', 'title' => 'Evening Prime Time News', 'desc' => 'Comprehensive summary of the day\'s major events.', 'is_playing' => false],
        ['time' => '21:00 - 23:00', 'title' => 'Late Night Spotlight', 'desc' => 'Documentary film showcases and talkshows.', 'is_playing' => false]
    ];

    $defaultRadioFlat = [
        ['time' => '06:00 - 10:00', 'title' => 'The Morning Drive', 'desc' => 'Kickstart the day with updates and music.', 'is_playing' => false],
        ['time' => '10:00 - 13:00', 'title' => 'Midday Request Show', 'desc' => 'Listener choices, request lines, and interviews.', 'is_playing' => false],
        ['time' => '13:00 - 16:00', 'title' => 'Getembe Express Drive', 'desc' => 'Mid-afternoon drive show with regional topics and guest experts.', 'is_playing' => true],
        ['time' => '16:00 - 20:00', 'title' => 'Evening Jam & Sports', 'desc' => 'Local sports bulletins and afternoon reviews.', 'is_playing' => false],
        ['time' => '20:00 - 00:00', 'title' => 'Late Night Soul Session', 'desc' => 'Slow jams, classic tracks, and quiet storm conversations.', 'is_playing' => false]
    ];

    $normalizeSchedule = function ($schedule, $defaultFlat) {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        if (!is_array($schedule) || empty($schedule)) {
            $grouped = [];
            foreach ($days as $day) {
                $grouped[$day] = $defaultFlat;
            }
            return $grouped;
        }

        $isGrouped = true;
        foreach ($days as $day) {
            if (!isset($schedule[$day])) {
                $isGrouped = false;
                break;
            }
        }

        if ($isGrouped) {
            return $schedule;
        }

        $grouped = [];
        foreach ($days as $day) {
            $grouped[$day] = $schedule;
        }
        return $grouped;
    };

    $this->tv_schedule = $normalizeSchedule(Setting::get('tv_schedule', []), $defaultTvFlat);
    $this->radio_schedule = $normalizeSchedule(Setting::get('radio_schedule', []), $defaultRadioFlat);

    $this->activeScheduleDay = strtolower(now()->format('l'));
    if (!in_array($this->activeScheduleDay, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
        $this->activeScheduleDay = 'monday';
    }

    $permissionMap = [
        'identity' => 'settings management',
        'socials' => 'settings management',
        'contact' => 'settings management',
        'pages' => 'settings management',
        'email' => 'email management',
        'social-login' => 'social login management',
        'payments' => 'payment management',
        'seo' => 'seo management',
        'fb-comments' => 'comment management',
        'roles' => 'roles and permissions management',
        'subscriptions' => 'subscription management',
        'polls' => 'polls management',
        'quizzes' => 'quizzes management',
        'rss' => 'rss management',
        'webhooks' => 'webhooks management',
        'api-keys' => 'api keys management',
        'cache' => 'cache management',
        'backup' => 'backup management',
        'audit' => 'audit logs management',
        'schedules' => 'settings management',
        'security' => 'settings management',
    ];

    if (isset($permissionMap[$activeTab])) {
        if (!auth()->user()->hasPermission($permissionMap[$activeTab])) {
            abort(403, 'Unauthorized access to settings tab.');
        }
    }

    if ($activeTab === 'roles') {
        $this->loadRolePermissions();
    }
});

// Helper: audit logging
$logAction = function ($action) {
    $logs = json_decode(Setting::get('simulated_audit_logs', '[]'), true);
    array_unshift($logs, [
        'id' => uniqid(),
        'action' => $action,
        'user' => auth()->user()->name . ' (' . auth()->user()->role . ')',
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);
    $logs = array_slice($logs, 0, 50); // limit logs
    Setting::set('simulated_audit_logs', json_encode($logs));
};

// 7. Sitemap generator
$generateSitemap = function () use ($logAction) {
    // Simulate generation
    Setting::set('sitemap_last_updated', now()->format('Y-m-d H:i:s'));
    $logAction("Generated sitemap.xml and updated robots.txt");
    session()->flash('sitemap_generated', 'Sitemap and Robots.txt generated successfully.');
};

// 14. Roles & Permissions actions
$loadRolePermissions = function () {
    $rolesPermissions = json_decode(Setting::get('roles_permissions', '{}'), true);
    if (isset($rolesPermissions[$this->selectedRoleForPermissions]['perms'])) {
        $this->selectedRolePermissions = $rolesPermissions[$this->selectedRoleForPermissions]['perms'];
    } else {
        $this->selectedRolePermissions = [];
    }
};

$addRole = function () use ($logAction) {
    if (!$this->newRoleSlug || !$this->newRoleName) return;

    $slug = Str::slug($this->newRoleSlug);
    $rolesPermissions = json_decode(Setting::get('roles_permissions', '{}'), true);

    if (empty($rolesPermissions)) {
        // seed defaults first
        $rolesPermissions = [
            'admin' => ['name' => 'Administrator', 'desc' => 'All permissions access', 'perms' => ['all']],
            'editor' => ['name' => 'Editor', 'desc' => 'Content control and moderation', 'perms' => ['content management', 'article management', 'category management', 'comment management', 'tag management', 'page management', 'contact message management']],
            'reporter' => ['name' => 'Reporter', 'desc' => 'News writer and creator', 'perms' => ['article management']],
            'contributor' => ['name' => 'Contributor', 'desc' => 'Guest writer status', 'perms' => ['article management']],
            'subscriber' => ['name' => 'Subscriber', 'desc' => 'Regular user', 'perms' => []]
        ];
    }

    if (isset($rolesPermissions[$slug])) {
        session()->flash('role_error', 'Role with this slug already exists.');
        return;
    }

    $rolesPermissions[$slug] = [
        'name' => $this->newRoleName,
        'desc' => $this->newRoleDescription ?: 'Custom user role',
        'perms' => []
    ];

    Setting::set('roles_permissions', json_encode($rolesPermissions));
    $logAction("Added new user role: {$this->newRoleName}");

    $this->reset(['newRoleSlug', 'newRoleName', 'newRoleDescription']);
    session()->flash('role_success', 'Role added successfully.');
};

$deleteRole = function ($slug) use ($logAction) {
    if (in_array($slug, ['admin', 'editor', 'reporter', 'contributor', 'subscriber'])) {
        session()->flash('role_error', 'You cannot delete built-in system roles.');
        return;
    }

    $rolesPermissions = json_decode(Setting::get('roles_permissions', '{}'), true);
    if (isset($rolesPermissions[$slug])) {
        $name = $rolesPermissions[$slug]['name'];
        unset($rolesPermissions[$slug]);
        Setting::set('roles_permissions', json_encode($rolesPermissions));
        $logAction("Deleted user role: {$name}");
        session()->flash('role_success', "Role {$name} deleted successfully.");
        $this->selectedRoleForPermissions = 'editor';
        $this->loadRolePermissions();
    }
};

$savePermissions = function () use ($logAction) {
    $rolesPermissions = json_decode(Setting::get('roles_permissions', '{}'), true);
    if (empty($rolesPermissions)) {
        $rolesPermissions = [
            'admin' => ['name' => 'Administrator', 'desc' => 'All permissions access', 'perms' => ['all']],
            'editor' => ['name' => 'Editor', 'desc' => 'Content control and moderation', 'perms' => ['content management', 'article management', 'category management', 'comment management', 'tag management', 'page management', 'contact message management']],
            'reporter' => ['name' => 'Reporter', 'desc' => 'News writer and creator', 'perms' => ['article management']],
            'contributor' => ['name' => 'Contributor', 'desc' => 'Guest writer status', 'perms' => ['article management']],
            'subscriber' => ['name' => 'Subscriber', 'desc' => 'Regular user', 'perms' => []]
        ];
    }

    if (isset($rolesPermissions[$this->selectedRoleForPermissions])) {
        // Prevent removing admin 'all' permission
        if ($this->selectedRoleForPermissions === 'admin') {
            $rolesPermissions[$this->selectedRoleForPermissions]['perms'] = ['all'];
        } else {
            $rolesPermissions[$this->selectedRoleForPermissions]['perms'] = $this->selectedRolePermissions;
        }

        Setting::set('roles_permissions', json_encode($rolesPermissions));
        $name = $rolesPermissions[$this->selectedRoleForPermissions]['name'];
        $logAction("Updated permissions for role: {$name}");
        session()->flash('permissions_success', 'Role permissions updated successfully.');
    }
};

// 17. Subscription actions
$addSubscriber = function () use ($logAction) {
    if (!$this->newSubscriberEmail) return;

    Newsletter::create(['email' => $this->newSubscriberEmail]);
    $logAction("Added subscriber: {$this->newSubscriberEmail}");
    $this->newSubscriberEmail = '';
    session()->flash('sub_success', 'Subscriber added successfully.');
};

$deleteSubscriber = function ($id) use ($logAction) {
    $sub = Newsletter::findOrFail($id);
    $email = $sub->email;
    $sub->delete();
    $logAction("Removed subscriber: {$email}");
    session()->flash('sub_success', 'Subscriber removed successfully.');
};

// 18. Polls actions
$addPoll = function () use ($logAction) {
    if (!$this->newPollQuestion) return;

    $polls = json_decode(Setting::get('simulated_polls', '[]'), true);
    array_unshift($polls, [
        'id' => uniqid(),
        'question' => $this->newPollQuestion,
        'options' => array_filter(array_map('trim', explode(',', $this->newPollOptions))),
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);
    Setting::set('simulated_polls', json_encode($polls));
    $logAction("Added simulated poll: {$this->newPollQuestion}");
    $this->reset(['newPollQuestion', 'newPollOptions']);
    session()->flash('poll_success', 'Poll added successfully.');
};

$deletePoll = function ($id) use ($logAction) {
    $polls = json_decode(Setting::get('simulated_polls', '[]'), true);
    $polls = array_filter($polls, fn($p) => $p['id'] !== $id);
    Setting::set('simulated_polls', json_encode(array_values($polls)));
    $logAction("Deleted simulated poll");
    session()->flash('poll_success', 'Poll removed successfully.');
};

// 19. Quizzes actions
$addQuiz = function () use ($logAction) {
    if (!$this->newQuizTitle) return;

    $quizzes = json_decode(Setting::get('simulated_quizzes', '[]'), true);
    array_unshift($quizzes, [
        'id' => uniqid(),
        'title' => $this->newQuizTitle,
        'questions_count' => (int) $this->newQuizQuestions ?: 5,
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);
    Setting::set('simulated_quizzes', json_encode($quizzes));
    $logAction("Added simulated quiz: {$this->newQuizTitle}");
    $this->reset(['newQuizTitle', 'newQuizQuestions']);
    session()->flash('quiz_success', 'Quiz added successfully.');
};

$deleteQuiz = function ($id) use ($logAction) {
    $quizzes = json_decode(Setting::get('simulated_quizzes', '[]'), true);
    $quizzes = array_filter($quizzes, fn($q) => $q['id'] !== $id);
    Setting::set('simulated_quizzes', json_encode(array_values($quizzes)));
    $logAction("Deleted simulated quiz");
    session()->flash('quiz_success', 'Quiz removed successfully.');
};

// 20. RSS actions
$addRssFeed = function () use ($logAction) {
    if (!$this->newRssFeedName || !$this->newRssFeedUrl) return;

    $feeds = json_decode(Setting::get('simulated_rss_feeds', '[]'), true);
    array_unshift($feeds, [
        'id' => uniqid(),
        'name' => $this->newRssFeedName,
        'url' => $this->newRssFeedUrl,
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);
    Setting::set('simulated_rss_feeds', json_encode($feeds));
    $logAction("Added RSS source feed: {$this->newRssFeedName}");
    $this->reset(['newRssFeedName', 'newRssFeedUrl']);
    session()->flash('rss_success', 'RSS Feed added successfully.');
};

$deleteRssFeed = function ($id) use ($logAction) {
    $feeds = json_decode(Setting::get('simulated_rss_feeds', '[]'), true);
    $feeds = array_filter($feeds, fn($f) => $f['id'] !== $id);
    Setting::set('simulated_rss_feeds', json_encode(array_values($feeds)));
    $logAction("Deleted RSS feed");
    session()->flash('rss_success', 'RSS Feed removed.');
};

// 21. Webhooks actions
$addWebhook = function () use ($logAction) {
    if (!$this->newWebhookName || !$this->newWebhookUrl) return;

    $webhooks = json_decode(Setting::get('simulated_webhooks', '[]'), true);
    array_unshift($webhooks, [
        'id' => uniqid(),
        'name' => $this->newWebhookName,
        'url' => $this->newWebhookUrl,
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);
    Setting::set('simulated_webhooks', json_encode($webhooks));
    $logAction("Registered webhook URL: {$this->newWebhookName}");
    $this->reset(['newWebhookName', 'newWebhookUrl']);
    session()->flash('webhook_success', 'Webhook registered successfully.');
};

$deleteWebhook = function ($id) use ($logAction) {
    $webhooks = json_decode(Setting::get('simulated_webhooks', '[]'), true);
    $webhooks = array_filter($webhooks, fn($w) => $w['id'] !== $id);
    Setting::set('simulated_webhooks', json_encode(array_values($webhooks)));
    $logAction("Deleted webhook registration");
    session()->flash('webhook_success', 'Webhook removed.');
};

// 22. API Keys actions
$generateApiKey = function () use ($logAction) {
    if (!$this->newApiKeyName) return;

    $keys = json_decode(Setting::get('simulated_api_keys', '[]'), true);
    $newToken = 'gn_' . Str::random(40);
    array_unshift($keys, [
        'id' => uniqid(),
        'name' => $this->newApiKeyName,
        'key' => substr($newToken, 0, 8) . '...' . substr($newToken, -8),
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);
    Setting::set('simulated_api_keys', json_encode($keys));
    $logAction("Generated new API key: {$this->newApiKeyName}");
    $this->reset('newApiKeyName');
    session()->flash('api_success', "API key generated successfully: {$newToken}");
};

$deleteApiKey = function ($id) use ($logAction) {
    $keys = json_decode(Setting::get('simulated_api_keys', '[]'), true);
    $keys = array_filter($keys, fn($k) => $k['id'] !== $id);
    Setting::set('simulated_api_keys', json_encode(array_values($keys)));
    $logAction("Revoked API key registration");
    session()->flash('api_success', 'API Key revoked successfully.');
};

// 24. Backup actions
$runBackup = function () use ($logAction) {
    $backups = json_decode(Setting::get('simulated_backups', '[]'), true);
    $name = 'backup_' . now()->format('Y_m_d_His') . '.zip';
    array_unshift($backups, [
        'id' => uniqid(),
        'name' => $name,
        'size' => rand(15, 60) . ' MB',
        'created_at' => now()->format('Y-m-d H:i:s')
    ]);
    Setting::set('simulated_backups', json_encode($backups));
    $logAction("Triggered manual backup creation: {$name}");
    session()->flash('backup_success', 'Backup zip archive compiled successfully.');
};

$deleteBackup = function ($id) use ($logAction) {
    $backups = json_decode(Setting::get('simulated_backups', '[]'), true);
    $backups = array_filter($backups, fn($b) => $b['id'] !== $id);
    Setting::set('simulated_backups', json_encode(array_values($backups)));
    $logAction("Deleted backup file archive");
    session()->flash('backup_success', 'Backup deleted.');
};

// 25. Restore actions
$restoreBackup = function ($id, $name) use ($logAction) {
    $restores = json_decode(Setting::get('simulated_restores', '[]'), true);
    array_unshift($restores, [
        'id' => uniqid(),
        'backup_name' => $name,
        'restored_at' => now()->format('Y-m-d H:i:s')
    ]);
    Setting::set('simulated_restores', json_encode($restores));
    $logAction("Restored system snapshot from backup: {$name}");
    session()->flash('backup_success', 'System restored successfully to backup point: ' . $name);
};

$addTvProgram = function () {
    if (!$this->newTvTime || !$this->newTvTitle) return;
    $day = $this->activeScheduleDay;
    if (!isset($this->tv_schedule[$day])) {
        $this->tv_schedule[$day] = [];
    }
    $this->tv_schedule[$day][] = [
        'time' => $this->newTvTime,
        'title' => $this->newTvTitle,
        'desc' => $this->newTvDesc,
        'is_playing' => false
    ];
    $this->newTvTime = '';
    $this->newTvTitle = '';
    $this->newTvDesc = '';
};

$removeTvProgram = function ($index) {
    $day = $this->activeScheduleDay;
    unset($this->tv_schedule[$day][$index]);
    $this->tv_schedule[$day] = array_values($this->tv_schedule[$day]);
};

$setTvPlaying = function ($index) {
    $day = $this->activeScheduleDay;
    foreach ($this->tv_schedule as $d => $slots) {
        foreach ($slots as $i => $item) {
            $this->tv_schedule[$d][$i]['is_playing'] = ($d === $day && $i === $index);
        }
    }
};

$addRadioProgram = function () {
    if (!$this->newRadioTime || !$this->newRadioTitle) return;
    $day = $this->activeScheduleDay;
    if (!isset($this->radio_schedule[$day])) {
        $this->radio_schedule[$day] = [];
    }
    $this->radio_schedule[$day][] = [
        'time' => $this->newRadioTime,
        'title' => $this->newRadioTitle,
        'desc' => $this->newRadioDesc,
        'is_playing' => false
    ];
    $this->newRadioTime = '';
    $this->newRadioTitle = '';
    $this->newRadioDesc = '';
};

$removeRadioProgram = function ($index) {
    $day = $this->activeScheduleDay;
    unset($this->radio_schedule[$day][$index]);
    $this->radio_schedule[$day] = array_values($this->radio_schedule[$day]);
};

$setRadioPlaying = function ($index) {
    $day = $this->activeScheduleDay;
    foreach ($this->radio_schedule as $d => $slots) {
        foreach ($slots as $i => $item) {
            $this->radio_schedule[$d][$i]['is_playing'] = ($d === $day && $i === $index);
        }
    }
};

// Save standard settings form
$save = function () use ($logAction) {
    if ($this->uploadedLogo) {
        $this->validate([
            'uploadedLogo' => 'image|max:2048',
        ]);
        $logoPath = $this->uploadedLogo->store('site', 'public');
        $this->site_logo = asset('storage/' . $logoPath);
        $this->uploadedLogo = null;
    }

    if ($this->uploadedFavicon) {
        $this->validate([
            'uploadedFavicon' => 'file|mimes:ico,png,svg,jpg,jpeg|max:1024',
        ]);
        $faviconPath = $this->uploadedFavicon->store('site', 'public');
        $this->favicon = asset('storage/' . $faviconPath);
        $this->uploadedFavicon = null;
    }

    if ($this->uploadedTopAd) {
        $this->validate(['uploadedTopAd' => 'image|max:2048']);
        $path = $this->uploadedTopAd->store('ads', 'public');
        $this->ad_top_image = asset('storage/' . $path);
        $this->uploadedTopAd = null;
    }

    if ($this->uploadedSidebarAd) {
        $this->validate(['uploadedSidebarAd' => 'image|max:2048']);
        $path = $this->uploadedSidebarAd->store('ads', 'public');
        $this->ad_sidebar_image = asset('storage/' . $path);
        $this->uploadedSidebarAd = null;
    }

    if ($this->uploadedInlineAd) {
        $this->validate(['uploadedInlineAd' => 'image|max:2048']);
        $path = $this->uploadedInlineAd->store('ads', 'public');
        $this->ad_inline_image = asset('storage/' . $path);
        $this->uploadedInlineAd = null;
    }

    if ($this->uploadedFooterAd) {
        $this->validate(['uploadedFooterAd' => 'image|max:2048']);
        $path = $this->uploadedFooterAd->store('ads', 'public');
        $this->ad_footer_image = asset('storage/' . $path);
        $this->uploadedFooterAd = null;
    }

    if ($this->uploadedMobileStickyAd) {
        $this->validate(['uploadedMobileStickyAd' => 'image|max:2048']);
        $path = $this->uploadedMobileStickyAd->store('ads', 'public');
        $this->ad_mobile_sticky_image = asset('storage/' . $path);
        $this->uploadedMobileStickyAd = null;
    }

    $fields = [
        'site_name', 'site_logo', 'brand_color', 'favicon',
        'website', 'facebook', 'twitter', 'instagram', 'linkedin', 'whatsapp', 'youtube', 'tiktok', 'snapchat', 'telegram', 'pinterest', 'threads', 'other_social_links',
        'contact_email', 'contact_phone', 'contact_open_hours', 'contact_address',
        'payment_methods', 'payment_gateways', 'currency', 'currency_symbol',
        'announcement_rate_tv', 'announcement_rate_radio', 'announcement_rate_both',
        'language',
        'meta_title', 'meta_description', 'meta_keywords', 'google_analytics_id', 'google_indexing_api', 'sitemap_frequency', 'robots_txt_enabled', 'robots_txt_content',
        'cookie_banner_enabled', 'cookie_position', 'cookie_approval_required', 'cookie_moderation_enabled',
        'theme_font', 'theme_layout', 'theme_color_secondary', 'theme_color_success', 'theme_color_warning',
        'smtp_server', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_auth_enabled', 'smtp_from_name', 'smtp_from_email', 'smtp_reply_to_email', 'smtp_reply_to_name',
        'fb_comments_widget', 'fb_comments_position', 'fb_comments_approval_required', 'fb_comments_moderation_enabled',
        'home_page', 'about_page', 'contact_page', 'privacy_page', 'terms_page', 'privacy_content', 'terms_content', 'podcast_category_enabled',
        'footer_copyright', 'footer_bg_color', 'footer_text_color', 'footer_logo', 'footer_link_color', 'footer_link_hover_color', 'footer_link_active_color', 'footer_link_visited_color',
        'google_login', 'facebook_login', 'twitter_login', 'github_login', 'linkedin_login', 'whatsapp_login', 'apple_login', 'pinterest_login', 'threads_login',
        'google_client_id', 'google_client_secret', 'facebook_client_id', 'facebook_client_secret', 'github_client_id', 'github_client_secret', 'twitter_client_id', 'twitter_client_secret',
        'notifications_enabled', 'notifications_push', 'notifications_in_app', 'notifications_email',
        'live_tv_url', 'live_radio_url', 'weather_city', 'homepage_categories',
        'app_play_store_url', 'app_app_store_url', 'app_banner_title', 'app_banner_desc',
        'tv_schedule', 'radio_schedule',
        'adsense_enabled', 'adsense_client_id', 'adsense_code',
        'facebook_ads_enabled', 'facebook_ads_code',
        'custom_ads_enabled',
        'ad_top_image', 'ad_top_link',
        'ad_sidebar_image', 'ad_sidebar_link',
        'ad_inline_image', 'ad_inline_link',
        'ad_footer_image', 'ad_footer_link',
        'ad_mobile_sticky_image', 'ad_mobile_sticky_link',
        'captcha_driver', 'recaptcha_site_key', 'recaptcha_secret_key', 'turnstile_site_key', 'turnstile_secret_key',
        'email_blacklist', 'password_min_length', 'password_complexity_required', 'login_max_attempts', 'login_lockout_duration',
        'seo_nofollow_links', 'seo_strip_links', 'author_reward_rate',
        'email_driver', 'mailgun_domain', 'mailgun_secret', 'mailgun_endpoint', 'brevo_username', 'brevo_api_key',
        'newsletter_popup_enabled', 'newsletter_popup_title', 'newsletter_popup_description', 'newsletter_popup_delay'
    ];

    foreach ($fields as $field) {
        Setting::set($field, $this->{$field});
    }

    \Illuminate\Support\Facades\Cache::forget('homepage_data_v1');

    $logAction("Saved general website settings configurations");
    $this->dispatch('settings-saved');
};

$clearCache = function () use ($logAction) {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    
    $logAction("Cleared system cache tables & mapping structures");
    session()->flash('cache_cleared', 'Application caches cleared successfully.');
};

$getSystemInfo = function () {
    return [
        'PHP Version' => PHP_VERSION,
        'Laravel Version' => app()->version(),
        'Server OS' => PHP_OS,
        'Database' => DB::connection()->getDriverName(),
        'Environment' => app()->environment(),
        'Database Version' => 'PostgreSQL (Supabase Ready)',
    ];
};

// 26. Newsletter Import/Export
$exportSubscribers = function ($format = 'csv') {
    $subscribers = Newsletter::all();

    if ($format === 'json') {
        $json = $subscribers->toJson(JSON_PRETTY_PRINT);
        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, 'subscribers-export-' . now()->format('YmdHis') . '.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="subscribers-export-' . now()->format('YmdHis') . '.csv"',
    ];

    $callback = function () use ($subscribers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID', 'Email', 'Active', 'Created At']);

        foreach ($subscribers as $sub) {
            fputcsv($file, [$sub->id, $sub->email, $sub->is_active ? 'Yes' : 'No', $sub->created_at->toIso8601String()]);
        }

        fclose($file);
    };

    return response()->streamDownload($callback, 'subscribers-export-' . now()->format('YmdHis') . '.csv', $headers);
};

$importSubscribers = function () use ($logAction) {
    if (!$this->uploaded_subscribers_file) {
        session()->flash('import_error', 'Please choose a file to upload first.');
        return;
    }

    $path = $this->uploaded_subscribers_file->getRealPath();
    $extension = $this->uploaded_subscribers_file->getClientOriginalExtension();

    $imported = 0;
    $duplicates = 0;
    $invalid = 0;

    try {
        if (strtolower($extension) === 'json') {
            $jsonContent = file_get_contents($path);
            $data = json_decode($jsonContent, true);
            
            if (!is_array($data)) {
                session()->flash('import_error', 'Invalid JSON array content.');
                return;
            }

            foreach ($data as $item) {
                $email = $item['email'] ?? $item['Email'] ?? null;
                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email = strtolower(trim($email));
                    $exists = Newsletter::where('email', $email)->exists();
                    if (!$exists) {
                        Newsletter::create(['email' => $email, 'is_active' => true]);
                        $imported++;
                    } else {
                        $duplicates++;
                    }
                } else {
                    $invalid++;
                }
            }
        } else {
            if (($handle = fopen($path, 'r')) !== false) {
                $header = fgetcsv($handle);
                $emailColIndex = -1;

                if ($header) {
                    foreach ($header as $index => $col) {
                        if (Str::contains(strtolower($col), ['email', 'mail'])) {
                            $emailColIndex = $index;
                            break;
                        }
                    }
                }

                if ($emailColIndex === -1) {
                    $emailColIndex = 0;
                    rewind($handle);
                }

                while (($row = fgetcsv($handle)) !== false) {
                    $email = $row[$emailColIndex] ?? null;
                    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $email = strtolower(trim($email));
                        $exists = Newsletter::where('email', $email)->exists();
                        if (!$exists) {
                            Newsletter::create(['email' => $email, 'is_active' => true]);
                            $imported++;
                        } else {
                            $duplicates++;
                        }
                    } else {
                        $invalid++;
                    }
                }
                fclose($handle);
            }
        }

        $this->uploaded_subscribers_file = null;
        $logAction("Imported newsletter subscribers list: {$imported} success, {$duplicates} duplicates skipped.");
        session()->flash('import_success', "Import completed. Success: {$imported}, Duplicates skipped: {$duplicates}, Invalid emails: {$invalid}");

    } catch (\Exception $e) {
        session()->flash('import_error', 'Failed to parse file: ' . $e->getMessage());
    }
};

$triggerRssAggregation = function () use ($logAction) {
    Artisan::call('rss:aggregate');
    $logAction("Triggered manual RSS feeds aggregation check");
    session()->flash('rss_aggregated_success', 'RSS feeds aggregation check ran successfully.');
};

$addBreakingNews = function () use ($logAction) {
    $this->validate([
        'breaking_title' => 'required|string|max:255',
        'breaking_link' => 'nullable|url',
        'breaking_priority' => 'required|integer|min:1',
        'breaking_expires_at' => 'nullable|date',
    ]);

    BreakingNews::create([
        'title' => $this->breaking_title,
        'link' => $this->breaking_link ?: null,
        'priority' => $this->breaking_priority,
        'is_active' => true,
        'expires_at' => $this->breaking_expires_at ?: null,
    ]);

    $logAction("Created new breaking news alert: " . $this->breaking_title);
    $this->reset(['breaking_title', 'breaking_link', 'breaking_priority', 'breaking_expires_at']);
    $this->breaking_news_list = BreakingNews::orderBy('created_at', 'desc')->get();
    session()->flash('breaking_success', 'Breaking news alert added successfully.');
};

$toggleBreakingNews = function ($id) use ($logAction) {
    $item = BreakingNews::findOrFail($id);
    $item->is_active = !$item->is_active;
    $item->save();

    $logAction("Toggled breaking news alert status: " . $item->title);
    $this->breaking_news_list = BreakingNews::orderBy('created_at', 'desc')->get();
};

$deleteBreakingNews = function ($id) use ($logAction) {
    $item = BreakingNews::findOrFail($id);
    $title = $item->title;
    $item->delete();

    $logAction("Deleted breaking news alert: " . $title);
    $this->breaking_news_list = BreakingNews::orderBy('created_at', 'desc')->get();
    session()->flash('breaking_success', 'Breaking news alert deleted.');
};

$toggleArticlePinned = function ($id, $field) use ($logAction) {
    $article = Article::findOrFail($id);
    $article->$field = !$article->$field;
    $article->save();

    $logAction("Toggled {$field} status on article: " . $article->title);
    $this->pinned_articles_list = Article::where('is_pinned', true)->orWhere('is_featured', true)->get();
};

?>

<div class="space-y-6" x-data="{ 
    activeTab: @entangle('activeTab'),
    saved: false
}" @settings-saved.window="saved = true; setTimeout(() => saved = false, 3000)">
    
    <div class="pb-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Website Administration Control Center</h2>
        
        <!-- Status Indicator -->
        <div x-show="saved" x-transition class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-950/20 px-3 py-1.5 rounded border border-green-200 dark:border-green-900" style="display: none;">
            Configuration options updated successfully!
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 shadow-sm">
        <form wire:submit.prevent="save" class="space-y-6">
                
                <!-- IDENTITY & THEME TAB -->
                <div x-show="activeTab === 'identity'" class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Site Identity & Theme Customizations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Website Name</label>
                            <input type="text" wire:model="site_name" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Favicon Icon</label>
                            <div class="flex flex-col space-y-2">
                                @if($favicon)
                                    <div class="flex items-center space-x-2">
                                        <img src="{{ $favicon }}" alt="Favicon Preview" class="w-8 h-8 object-contain border border-gray-200 dark:border-gray-700 rounded p-1 bg-white">
                                        <span class="text-[10px] text-gray-500 truncate max-w-[150px]">{{ basename($favicon) }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center space-x-2">
                                    <input type="file" wire:model="uploadedFavicon" class="hidden" id="upload-favicon-input" accept=".ico,.png,.svg,.jpg,.jpeg">
                                    <label for="upload-favicon-input" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold py-1.5 px-3 rounded cursor-pointer transition border border-gray-300 dark:border-gray-700">
                                        Choose File
                                    </label>
                                    <span class="text-[10px] text-gray-550" wire:loading wire:target="uploadedFavicon">Uploading...</span>
                                    @if($uploadedFavicon)
                                        <span class="text-[10px] text-green-600 font-bold">✓ Ready to Save</span>
                                    @endif
                                </div>
                                <input type="url" wire:model="favicon" placeholder="Or enter Favicon URL" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                @error('uploadedFavicon') <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Website Logo</label>
                        <div class="flex flex-col space-y-2">
                            @if($site_logo)
                                <div class="flex items-center space-x-2">
                                    <img src="{{ $site_logo }}" alt="Logo Preview" class="h-10 object-contain border border-gray-200 dark:border-gray-700 rounded p-1 bg-white">
                                    <span class="text-[10px] text-gray-500 truncate max-w-[300px]">{{ basename($site_logo) }}</span>
                                </div>
                            @endif
                            <div class="flex items-center space-x-2">
                                <input type="file" wire:model="uploadedLogo" class="hidden" id="upload-logo-input" accept="image/*">
                                <label for="upload-logo-input" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold py-1.5 px-3 rounded cursor-pointer transition border border-gray-300 dark:border-gray-700">
                                    Choose File
                                </label>
                                <span class="text-[10px] text-gray-555" wire:loading wire:target="uploadedLogo">Uploading...</span>
                                @if($uploadedLogo)
                                    <span class="text-[10px] text-green-600 font-bold">✓ Ready to Save</span>
                                @endif
                            </div>
                            <input type="url" wire:model="site_logo" placeholder="Or enter Logo URL" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            @error('uploadedLogo') <span class="text-red-500 text-[10px] block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Homepage Layout Grid</label>
                            <select wire:model="theme_layout" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                <option value="standard">Standard News Layout</option>
                                <option value="compact">Compact / Minimalist Layout</option>
                                <option value="visual">Visual Grid Layout</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Typography Typography Font</label>
                            <select wire:model="theme_font" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                <option value="Inter">Inter (Sans-serif)</option>
                                <option value="Source Sans Pro">Source Sans Pro</option>
                                <option value="IBM Plex Sans">IBM Plex Sans</option>
                                <option value="Playfair Display">Playfair Display (Serif)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Primary Color</label>
                            <input type="color" wire:model="brand_color" class="w-full h-8 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1 cursor-pointer">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Secondary Color</label>
                            <input type="color" wire:model="theme_color_secondary" class="w-full h-8 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1 cursor-pointer">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Success Color</label>
                            <input type="color" wire:model="theme_color_success" class="w-full h-8 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1 cursor-pointer">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Warning Color</label>
                            <input type="color" wire:model="theme_color_warning" class="w-full h-8 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1 cursor-pointer">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Homepage Categories (comma slugs)</label>
                            <input type="text" wire:model="homepage_categories" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Weather Widget City</label>
                            <input type="text" wire:model="weather_city" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Live TV stream URL</label>
                            <input type="url" wire:model="live_tv_url" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Live FM stream URL</label>
                            <input type="url" wire:model="live_radio_url" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                    </div>

                    <!-- Mobile App Downloads settings -->
                    <div class="border-t border-gray-150 dark:border-gray-850 pt-4 space-y-4">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Mobile App Downloads & Banner Settings</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Google Play Store Link</label>
                                <input type="url" wire:model="app_play_store_url" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Apple App Store Link</label>
                                <input type="url" wire:model="app_app_store_url" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">App Promo Banner Title</label>
                                <input type="text" wire:model="app_banner_title" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">App Promo Banner Description</label>
                                <textarea wire:model="app_banner_desc" rows="2" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- SOCIAL LINKS TAB -->
                <div x-show="activeTab === 'socials'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Social Network Connections</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Website URL</label>
                            <input type="url" wire:model="website" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Facebook URL</label>
                            <input type="url" wire:model="facebook" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Twitter / X URL</label>
                            <input type="url" wire:model="twitter" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Instagram URL</label>
                            <input type="url" wire:model="instagram" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">LinkedIn URL</label>
                            <input type="url" wire:model="linkedin" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">WhatsApp URL / API Link</label>
                            <input type="text" wire:model="whatsapp" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">YouTube Channel URL</label>
                            <input type="url" wire:model="youtube" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">TikTok Profile URL</label>
                            <input type="url" wire:model="tiktok" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Snapchat URL</label>
                            <input type="url" wire:model="snapchat" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Telegram Channel URL</label>
                            <input type="url" wire:model="telegram" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Pinterest URL</label>
                            <input type="url" wire:model="pinterest" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Threads URL</label>
                            <input type="url" wire:model="threads" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Other Social Links (newline or commas)</label>
                        <textarea wire:model="other_social_links" rows="2" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- CONTACT SETTINGS TAB -->
                <div x-show="activeTab === 'contact'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Contact Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Contact Email</label>
                            <input type="email" wire:model="contact_email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Contact Phone</label>
                            <input type="text" wire:model="contact_phone" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Open Hours</label>
                            <textarea wire:model="contact_open_hours" rows="3" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white" placeholder="e.g. Monday - Friday: 8am - 5pm"></textarea>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Contact Address</label>
                        <textarea wire:model="contact_address" rows="3" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white" placeholder="e.g. Getembe News Plaza, 3rd Floor..."></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- PAGES & FOOTER TAB -->
                <div x-show="activeTab === 'pages'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Page Layout & Footer Settings</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Home Page path</label>
                            <input type="text" wire:model="home_page" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">About Page path</label>
                            <input type="text" wire:model="about_page" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Contact Page path</label>
                            <input type="text" wire:model="contact_page" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Privacy Page path</label>
                            <input type="text" wire:model="privacy_page" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Terms Page path</label>
                            <input type="text" wire:model="terms_page" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider pt-4 border-t border-gray-100 dark:border-gray-800">Footer Custom Branding</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Footer Logo URL</label>
                            <input type="url" wire:model="footer_logo" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Copyright Text</label>
                            <input type="text" wire:model="footer_copyright" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900 rounded text-xs text-blue-800 dark:text-blue-200">
                        <strong>Note:</strong> Footer navigation menu links are managed separately under the <a href="/admin/menus" class="underline font-bold hover:text-blue-600">Navigation Menus Manager</a>.
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Footer BG Color</label>
                            <input type="text" wire:model="footer_bg_color" placeholder="#111827" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Footer Text Color</label>
                            <input type="text" wire:model="footer_text_color" placeholder="#D1D5DB" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Link Color</label>
                            <input type="text" wire:model="footer_link_color" placeholder="#3B82F6" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Link Hover Color</label>
                            <input type="text" wire:model="footer_link_hover_color" placeholder="#2563EB" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Link Active Color</label>
                            <input type="text" wire:model="footer_link_active_color" placeholder="#1D4ED8" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Link Visited Color</label>
                            <input type="text" wire:model="footer_link_visited_color" placeholder="#4B5563" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono">
                        </div>
                    </div>

                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider pt-4 border-t border-gray-100 dark:border-gray-800">Legal Pages Content</h4>
                    <div class="space-y-4 mt-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Privacy Policy Page Content (HTML allowed)</label>
                            <textarea wire:model="privacy_content" rows="12" class="w-full bg-gray-50 dark:bg-gray-850 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs text-gray-905 dark:text-white font-mono" placeholder="Write or paste your Privacy Policy HTML content here..."></textarea>
                            @error('privacy_content') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Terms of Service Page Content (HTML allowed)</label>
                            <textarea wire:model="terms_content" rows="12" class="w-full bg-gray-50 dark:bg-gray-850 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs text-gray-905 dark:text-white font-mono" placeholder="Write or paste your Terms of Service HTML content here..."></textarea>
                            @error('terms_content') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider pt-4 border-t border-gray-100 dark:border-gray-800">Podcast Integration Settings</h4>
                    <div class="space-y-4 mt-4">
                        <div class="flex items-center justify-between p-4 bg-gray-55 dark:bg-gray-850 rounded-lg border border-gray-200 dark:border-gray-800 text-xs">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Enable Default Podcast Category</p>
                                <p class="text-gray-400 mt-0.5">When enabled, any article in the Audio (Podcast) format will be automatically filed under the default "Podcast" category on save.</p>
                            </div>
                            <div>
                                <button type="button" wire:click="$set('podcast_category_enabled', !{{ $podcast_category_enabled ? 'true' : 'false' }})" 
                                        class="text-xs px-3.5 py-2 rounded font-bold transition {{ $podcast_category_enabled ? 'bg-green-100 text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-900/50 hover:bg-green-200' : 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900/50 hover:bg-red-200' }}">
                                    {{ $podcast_category_enabled ? 'Enabled' : 'Disabled' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- SMTP EMAIL TAB -->
                <div x-show="activeTab === 'email'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Email Services & Integrations</h3>
                    
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Email Delivery Method</label>
                        <select wire:model="email_driver" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            <option value="smtp">Standard SMTP Relay</option>
                            <option value="mailgun">Mailgun API Integration</option>
                            <option value="brevo">Brevo (Sendinblue) SMTP Relay</option>
                        </select>
                    </div>

                    <!-- SMTP Fields -->
                    <div x-show="email_driver === 'smtp'" class="space-y-4 pt-2">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2 space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">SMTP Host Server</label>
                                <input type="text" wire:model="smtp_server" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">SMTP Port</label>
                                <input type="number" wire:model="smtp_port" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">SMTP Username</label>
                                <input type="text" wire:model="smtp_username" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">SMTP Password</label>
                                <input type="password" wire:model="smtp_password" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">SMTP Encryption</label>
                                <select wire:model="smtp_encryption" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="flex items-center pt-5">
                                <input type="checkbox" wire:model="smtp_auth_enabled" id="smtp_auth_enabled" class="rounded text-[#C8102E] border-gray-300 dark:border-gray-700">
                                <label for="smtp_auth_enabled" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Require SMTP Authentication</label>
                            </div>
                        </div>
                    </div>

                    <!-- Mailgun Fields -->
                    <div x-show="email_driver === 'mailgun'" class="space-y-4 pt-2" x-cloak>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Mailgun Domain</label>
                                <input type="text" wire:model="mailgun_domain" placeholder="mg.yourdomain.com" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Mailgun API Key</label>
                                <input type="password" wire:model="mailgun_secret" placeholder="key-..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Mailgun API Endpoint</label>
                            <select wire:model="mailgun_endpoint" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                <option value="api.mailgun.net">US Region (api.mailgun.net)</option>
                                <option value="api.eu.mailgun.net">EU Region (api.eu.mailgun.net)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Brevo Fields -->
                    <div x-show="email_driver === 'brevo'" class="space-y-4 pt-2" x-cloak>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Brevo Username / Login Email</label>
                                <input type="text" wire:model="brevo_username" placeholder="your-login-email@domain.com" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Brevo API Key (SMTP Password)</label>
                                <input type="password" wire:model="brevo_api_key" placeholder="xkeysib-..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Common Sender Info -->
                    <div class="border-t border-gray-150 dark:border-gray-800 pt-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">From Name</label>
                                <input type="text" wire:model="smtp_from_name" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">From Email Address</label>
                                <input type="email" wire:model="smtp_from_email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Reply-To Name</label>
                                <input type="text" wire:model="smtp_reply_to_name" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Reply-To Email</label>
                                <input type="email" wire:model="smtp_reply_to_email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Email Configurations</button>
                    </div>
                </div>

                <!-- SOCIAL AUTH INTEGRATIONS TAB -->
                <div x-show="activeTab === 'social-login'" class="space-y-6" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">OAuth Login Connections</h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs text-gray-700 dark:text-gray-300">
                        
                        <!-- Google OAuth -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-955 rounded-lg border border-gray-200 dark:border-gray-850 space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.live="google_login" id="google_login" class="rounded text-[#C8102E] border-gray-300">
                                <label for="google_login" class="ml-2 font-bold cursor-pointer text-gray-900 dark:text-white">Enable Google Sign-in</label>
                            </div>
                            
                            @if($google_login)
                                <div class="space-y-3 pt-2 border-t border-gray-200 dark:border-gray-850">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Google Client ID</label>
                                        <input type="text" wire:model="google_client_id" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Google Client Secret</label>
                                        <input type="password" wire:model="google_client_secret" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Redirect URL (Callback)</label>
                                        <input type="text" readonly value="{{ url('/auth/google/callback') }}" class="w-full bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-400 font-mono select-all">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Facebook OAuth -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-955 rounded-lg border border-gray-200 dark:border-gray-850 space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.live="facebook_login" id="facebook_login" class="rounded text-[#C8102E] border-gray-300">
                                <label for="facebook_login" class="ml-2 font-bold cursor-pointer text-gray-900 dark:text-white">Enable Facebook Sign-in</label>
                            </div>
                            
                            @if($facebook_login)
                                <div class="space-y-3 pt-2 border-t border-gray-200 dark:border-gray-850">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Facebook App ID (Client ID)</label>
                                        <input type="text" wire:model="facebook_client_id" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Facebook App Secret (Client Secret)</label>
                                        <input type="password" wire:model="facebook_client_secret" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Redirect URL (Callback)</label>
                                        <input type="text" readonly value="{{ url('/auth/facebook/callback') }}" class="w-full bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-400 font-mono select-all">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- GitHub OAuth -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-955 rounded-lg border border-gray-200 dark:border-gray-855 space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.live="github_login" id="github_login" class="rounded text-[#C8102E] border-gray-300">
                                <label for="github_login" class="ml-2 font-bold cursor-pointer text-gray-900 dark:text-white">Enable GitHub Sign-in</label>
                            </div>
                            
                            @if($github_login)
                                <div class="space-y-3 pt-2 border-t border-gray-200 dark:border-gray-850">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">GitHub Client ID</label>
                                        <input type="text" wire:model="github_client_id" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">GitHub Client Secret</label>
                                        <input type="password" wire:model="github_client_secret" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Redirect URL (Callback)</label>
                                        <input type="text" readonly value="{{ url('/auth/github/callback') }}" class="w-full bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-400 font-mono select-all">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Twitter/X OAuth -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-955 rounded-lg border border-gray-200 dark:border-gray-850 space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model.live="twitter_login" id="twitter_login" class="rounded text-[#C8102E] border-gray-300">
                                <label for="twitter_login" class="ml-2 font-bold cursor-pointer text-gray-900 dark:text-white">Enable Twitter / X Sign-in</label>
                            </div>
                            
                            @if($twitter_login)
                                <div class="space-y-3 pt-2 border-t border-gray-200 dark:border-gray-850">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Twitter Client ID</label>
                                        <input type="text" wire:model="twitter_client_id" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Twitter Client Secret</label>
                                        <input type="password" wire:model="twitter_client_secret" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white font-mono">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Redirect URL (Callback)</label>
                                        <input type="text" readonly value="{{ url('/auth/twitter/callback') }}" class="w-full bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-400 font-mono select-all">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Other Quick Sign-in Checks -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-955 rounded-lg border border-gray-200 dark:border-gray-850 space-y-4 col-span-full">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Other OAuth Options</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="flex items-center p-2 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
                                    <input type="checkbox" wire:model="linkedin_login" id="linkedin_login" class="rounded text-[#C8102E] border-gray-300">
                                    <label for="linkedin_login" class="ml-2 cursor-pointer">Enable LinkedIn Sign-in</label>
                                </div>
                                <div class="flex items-center p-2 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
                                    <input type="checkbox" wire:model="whatsapp_login" id="whatsapp_login" class="rounded text-[#C8102E] border-gray-300">
                                    <label for="whatsapp_login" class="ml-2 cursor-pointer">Enable WhatsApp QuickSign</label>
                                </div>
                                <div class="flex items-center p-2 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
                                    <input type="checkbox" wire:model="apple_login" id="apple_login" class="rounded text-[#C8102E] border-gray-300">
                                    <label for="apple_login" class="ml-2 cursor-pointer">Enable Apple Sign-in</label>
                                </div>
                                <div class="flex items-center p-2 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
                                    <input type="checkbox" wire:model="pinterest_login" id="pinterest_login" class="rounded text-[#C8102E] border-gray-300">
                                    <label for="pinterest_login" class="ml-2 cursor-pointer">Enable Pinterest Logins</label>
                                </div>
                                <div class="flex items-center p-2 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
                                    <input type="checkbox" wire:model="threads_login" id="threads_login" class="rounded text-[#C8102E] border-gray-300">
                                    <label for="threads_login" class="ml-2 cursor-pointer">Enable Threads Identity Check</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="pt-4 border-t border-gray-150 dark:border-gray-850">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- PAYMENTS & CURRENCY TAB -->
                <div x-show="activeTab === 'payments'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Currency & Checkout Settings</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Default Currency (ISO)</label>
                            <input type="text" wire:model="currency" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Currency Symbol</label>
                            <input type="text" wire:model="currency_symbol" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Active Gateways (comma separated)</label>
                            <input type="text" wire:model="payment_gateways" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Allowed Payment Methods (comma separated)</label>
                            <input type="text" wire:model="payment_methods" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Announcements Pricing Rates -->
                    <div class="border-t border-gray-150 dark:border-gray-800 pt-4 space-y-4">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Announcement Rate Config (per word)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">TV Rate (KES)</label>
                                <input type="number" wire:model="announcement_rate_tv" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Radio Rate (KES)</label>
                                <input type="number" wire:model="announcement_rate_radio" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Combined TV & Radio Rate (KES)</label>
                                <input type="number" wire:model="announcement_rate_both" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- SEO & COOKIE CONSENTS TAB -->
                <div x-show="activeTab === 'seo'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">SEO Controls</h3>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Global Title Tag</label>
                        <input type="text" wire:model="meta_title" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Meta Keywords</label>
                        <input type="text" wire:model="meta_keywords" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Default Meta Description</label>
                        <textarea wire:model="meta_description" rows="3" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs text-gray-900 dark:text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Google Analytics tracking ID</label>
                            <input type="text" wire:model="google_analytics_id" placeholder="G-XXXXXXXXXX" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Sitemap Frequency</label>
                            <select wire:model="sitemap_frequency" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                <option value="always">Always / Realtime</option>
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6 pt-2">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="google_indexing_api" id="google_indexing_api" class="rounded text-[#C8102E] border-gray-300">
                            <label for="google_indexing_api" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Enable Google Indexing API</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="robots_txt_enabled" id="robots_txt_enabled" class="rounded text-[#C8102E] border-gray-300">
                            <label for="robots_txt_enabled" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Serve robots.txt file</label>
                        </div>
                    </div>

                    <!-- Sitemap last update block & actions -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg text-xs space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-700 dark:text-gray-300">XML Sitemap Status</span>
                            <span class="text-[10px] bg-green-900/20 text-green-400 font-bold px-2 py-0.5 rounded">Active</span>
                        </div>
                        <p class="text-gray-400">Last auto-calculated index update: <span class="font-mono text-gray-900 dark:text-white font-bold">{{ \App\Models\Setting::get('sitemap_last_updated') ?: 'N/A' }}</span></p>
                        
                        @if (session()->has('sitemap_generated'))
                            <div class="p-2 bg-green-900/10 border border-green-800 text-green-300 text-[10px] rounded">
                                {{ session('sitemap_generated') }}
                            </div>
                        @endif

                        <button type="button" wire:click="generateSitemap" class="mt-2 bg-gray-850 hover:bg-gray-800 text-white border border-gray-750 font-bold text-[10px] px-3 py-1.5 rounded transition">
                            Trigger Sitemap & Robots.txt Regeneration
                        </button>
                    </div>

                    <div class="space-y-1 pt-2">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">robots.txt File Configuration Content</label>
                        <textarea wire:model="robots_txt_content" rows="4" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white font-mono"></textarea>
                    </div>

                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2 pt-4">Cookie Consent Disclosures</h3>
                    <div class="flex items-center pt-2">
                        <input type="checkbox" wire:model="cookie_banner_enabled" id="cookie_banner_enabled" class="rounded text-[#C8102E] border-gray-300">
                        <label for="cookie_banner_enabled" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Display Cookie Banner Consent Popups</label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Banner Position placement</label>
                            <select wire:model="cookie_position" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                <option value="top">Top Header Bar</option>
                                <option value="bottom">Bottom Overlay Bar</option>
                                <option value="bottom-right">Bottom Right Card</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6 pt-2">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="cookie_approval_required" id="cookie_approval_required" class="rounded text-[#C8102E] border-gray-300">
                            <label for="cookie_approval_required" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Strict Cookie Approval Required</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="cookie_moderation_enabled" id="cookie_moderation_enabled" class="rounded text-[#C8102E] border-gray-300">
                            <label for="cookie_moderation_enabled" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Cookie Moderation Enabled</label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- ADVERTISING & BANNER ADS TAB -->
                <div x-show="activeTab === 'advertising'" class="space-y-6" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Advertising Channels & Custom Banners</h3>
                    
                    <!-- Channel Toggles -->
                    <div class="bg-gray-50 dark:bg-gray-850 p-4 rounded-lg border border-gray-150 dark:border-gray-800 space-y-4">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wide">Active Channels Selection</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-semibold">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="custom_ads_enabled" class="rounded text-[#C8102E] border-gray-300">
                                <span class="text-gray-700 dark:text-gray-300">Enable Custom Banners</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="adsense_enabled" class="rounded text-[#C8102E] border-gray-300">
                                <span class="text-gray-700 dark:text-gray-300">Enable Google AdSense</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" wire:model="facebook_ads_enabled" class="rounded text-[#C8102E] border-gray-300">
                                <span class="text-gray-700 dark:text-gray-300">Enable Facebook Audience Ads</span>
                            </label>
                        </div>
                    </div>

                    <!-- Google AdSense Integrations -->
                    <div class="space-y-4" x-show="adsense_enabled">
                        <h4 class="text-xs font-bold text-[#C8102E] uppercase tracking-wide border-b border-gray-100 dark:border-gray-800 pb-1">Google AdSense Configurations</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">AdSense Publisher/Client ID</label>
                                <input type="text" wire:model="adsense_client_id" placeholder="ca-pub-XXXXXXXXXXXXXXXX" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300">AdSense Injection/Script Code</label>
                                <textarea wire:model="adsense_code" rows="4" placeholder="Paste your Google AdSense <ins> or <script> tags here..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs font-mono text-gray-900 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Facebook Ads Integrations -->
                    <div class="space-y-4" x-show="facebook_ads_enabled">
                        <h4 class="text-xs font-bold text-[#FF7900] uppercase tracking-wide border-b border-gray-100 dark:border-gray-800 pb-1">Facebook Audience Network Configurations</h4>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Facebook Placement/Pixel Script Code</label>
                            <textarea wire:model="facebook_ads_code" rows="4" placeholder="Paste your Facebook Audience Network ads script code here..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-xs font-mono text-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>

                    <!-- Custom Ad Banners -->
                    <div class="space-y-6" x-show="custom_ads_enabled">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide border-b border-gray-100 dark:border-gray-800 pb-1">Custom Banners (Location Placements)</h4>
                        
                        <!-- Top Leaderboard Ad -->
                        <div class="bg-gray-50 dark:bg-gray-850 p-4 rounded-lg border border-gray-150 dark:border-gray-800 space-y-4">
                            <span class="text-[10px] font-black text-[#C8102E] uppercase tracking-wider block">Top Header Leaderboard (728x90)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Upload Banner Image</label>
                                    <div class="flex flex-col space-y-2">
                                        @if($ad_top_image)
                                            <img src="{{ $ad_top_image }}" class="max-h-16 object-contain rounded border border-gray-250 bg-white">
                                        @endif
                                        <input type="file" wire:model="uploadedTopAd" accept="image/*">
                                        <input type="url" wire:model="ad_top_image" placeholder="Or enter Image URL" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Destination Redirect Link</label>
                                    <input type="url" wire:model="ad_top_link" placeholder="https://example.com/promo" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Rectangle Ad -->
                        <div class="bg-gray-50 dark:bg-gray-850 p-4 rounded-lg border border-gray-150 dark:border-gray-800 space-y-4">
                            <span class="text-[10px] font-black text-[#C8102E] uppercase tracking-wider block">Sidebar Rectangle (300x250)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Upload Banner Image</label>
                                    <div class="flex flex-col space-y-2">
                                        @if($ad_sidebar_image)
                                            <img src="{{ $ad_sidebar_image }}" class="max-h-16 object-contain rounded border border-gray-250 bg-white">
                                        @endif
                                        <input type="file" wire:model="uploadedSidebarAd" accept="image/*">
                                        <input type="url" wire:model="ad_sidebar_image" placeholder="Or enter Image URL" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Destination Redirect Link</label>
                                    <input type="url" wire:model="ad_sidebar_link" placeholder="https://example.com/promo" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Inline Body Ad -->
                        <div class="bg-gray-50 dark:bg-gray-850 p-4 rounded-lg border border-gray-150 dark:border-gray-800 space-y-4">
                            <span class="text-[10px] font-black text-[#C8102E] uppercase tracking-wider block">Inline Article Body (468x60)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Upload Banner Image</label>
                                    <div class="flex flex-col space-y-2">
                                        @if($ad_inline_image)
                                            <img src="{{ $ad_inline_image }}" class="max-h-16 object-contain rounded border border-gray-250 bg-white">
                                        @endif
                                        <input type="file" wire:model="uploadedInlineAd" accept="image/*">
                                        <input type="url" wire:model="ad_inline_image" placeholder="Or enter Image URL" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Destination Redirect Link</label>
                                    <input type="url" wire:model="ad_inline_link" placeholder="https://example.com/promo" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-355 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Footer Banner Ad -->
                        <div class="bg-gray-50 dark:bg-gray-850 p-4 rounded-lg border border-gray-150 dark:border-gray-800 space-y-4">
                            <span class="text-[10px] font-black text-[#C8102E] uppercase tracking-wider block">Bottom Footer Banner (728x90)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Upload Banner Image</label>
                                    <div class="flex flex-col space-y-2">
                                        @if($ad_footer_image)
                                            <img src="{{ $ad_footer_image }}" class="max-h-16 object-contain rounded border border-gray-250 bg-white">
                                        @endif
                                        <input type="file" wire:model="uploadedFooterAd" accept="image/*">
                                        <input type="url" wire:model="ad_footer_image" placeholder="Or enter Image URL" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Destination Redirect Link</label>
                                    <input type="url" wire:model="ad_footer_link" placeholder="https://example.com/promo" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-355 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Sticky Bottom Ad -->
                        <div class="bg-gray-50 dark:bg-gray-850 p-4 rounded-lg border border-gray-150 dark:border-gray-800 space-y-4">
                            <span class="text-[10px] font-black text-[#C8102E] uppercase tracking-wider block">Mobile Sticky Bottom Banner (320x50)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Upload Banner Image</label>
                                    <div class="flex flex-col space-y-2">
                                        @if($ad_mobile_sticky_image)
                                            <img src="{{ $ad_mobile_sticky_image }}" class="max-h-12 object-contain rounded border border-gray-250 bg-white">
                                        @endif
                                        <input type="file" wire:model="uploadedMobileStickyAd" accept="image/*">
                                        <input type="url" wire:model="ad_mobile_sticky_image" placeholder="Or enter Image URL" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-350 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Destination Redirect Link</label>
                                    <input type="url" wire:model="ad_mobile_sticky_link" placeholder="https://example.com/promo" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-355 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- FACEBOOK COMMENTS TAB -->
                <div x-show="activeTab === 'fb-comments'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Facebook Comments Widget Configurations</h3>
                    
                    <div class="flex items-center pt-2">
                        <input type="checkbox" wire:model="fb_comments_widget" id="fb_comments_widget" class="rounded text-[#C8102E] border-gray-300">
                        <label for="fb_comments_widget" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Enable Facebook Comments Widget</label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Comments Position Placement</label>
                            <select wire:model="fb_comments_position" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                <option value="top">Top (Above article content)</option>
                                <option value="bottom">Bottom (Below article content)</option>
                                <option value="sidebar">Sidebar sidebar placement</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6 pt-2">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="fb_comments_approval_required" id="fb_comments_approval_required" class="rounded text-[#C8102E] border-gray-300">
                            <label for="fb_comments_approval_required" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Comments Approval Required</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="fb_comments_moderation_enabled" id="fb_comments_moderation_enabled" class="rounded text-[#C8102E] border-gray-300">
                            <label for="fb_comments_moderation_enabled" class="ml-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Comments Moderation Enabled</label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- ROLES & PERMISSIONS TAB -->
                <div x-show="activeTab === 'roles'" class="space-y-6" style="display: none;" wire:ignore.self>
                    @can('roles and permissions management')
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Dynamic Roles & Access Permissions</h3>
                    
                    @if (session()->has('role_error'))
                        <div class="p-3 bg-red-900/10 border border-red-800 text-red-300 text-xs rounded">
                            {{ session('role_error') }}
                        </div>
                    @endif
                    @if (session()->has('role_success'))
                        <div class="p-3 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('role_success') }}
                        </div>
                    @endif

                    <!-- Roles list -->
                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase mb-3">System Defined & Custom Roles</h4>
                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach(json_decode(\App\Models\Setting::get('roles_permissions', '{"admin":{"name":"Administrator","desc":"All permissions access","perms":["all"]},"editor":{"name":"Editor","desc":"Content control and moderation","perms":["content management","article management","category management","comment management","tag management","page management","contact message management"]},"reporter":{"name":"Reporter","desc":"News writer and creator","perms":["article management"]},"contributor":{"name":"Contributor","desc":"Guest writer status","perms":["article management"]},"subscriber":{"name":"Subscriber","desc":"Regular user","perms":[]}}'), true) as $slug => $role)
                                <div class="py-2.5 flex justify-between items-center text-xs">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                                            <span>{{ $role['name'] }}</span>
                                            <span class="text-[9px] font-mono bg-gray-200 dark:bg-gray-800 px-1 py-0.2 rounded font-normal text-gray-500">{{ $slug }}</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $role['desc'] }}</p>
                                    </div>
                                    <div class="space-x-3 text-[10px] font-bold">
                                        <button type="button" wire:click="$set('selectedRoleForPermissions', '{{ $slug }}'); loadRolePermissions()" class="text-blue-550 hover:underline">Configure Permissions</button>
                                        @if(!in_array($slug, ['admin', 'editor', 'reporter', 'contributor', 'subscriber']))
                                            <button type="button" wire:click="deleteRole('{{ $slug }}')" class="text-red-550 hover:underline">Delete Role</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Add role Form -->
                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-3">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Register New Administrative Role</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Role Name (Display Name)</label>
                                <input type="text" wire:model="newRoleName" placeholder="e.g. Chief Editor" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Role Key / Slug</label>
                                <input type="text" wire:model="newRoleSlug" placeholder="e.g. chief-editor" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Role Description</label>
                            <input type="text" wire:model="newRoleDescription" placeholder="Provide description" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                        </div>
                        <button type="button" wire:click="addRole" class="bg-gray-850 hover:bg-gray-850 text-white font-bold text-xs px-4 py-2 rounded transition">Add Role</button>
                    </div>

                    <!-- Configure permissions mapping -->
                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-4">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-800">
                            <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">
                                Permissions for role: <span class="text-[#C8102E] font-black">{{ strtoupper(data_get($this, 'selectedRoleForPermissions') ?? data_get($this, 'selected_role_for_permissions') ?? 'editor') }}</span>
                            </h4>
                            <span class="text-[10px] text-gray-400">Checkbox toggle automatically saves mapping matrix on save click.</span>
                        </div>

                        @if (session()->has('permissions_success'))
                            <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                                {{ session('permissions_success') }}
                            </div>
                        @endif

                        @if((data_get($this, 'selectedRoleForPermissions') ?? data_get($this, 'selected_role_for_permissions') ?? 'editor') === 'admin')
                            <div class="p-3 bg-red-950/20 border border-red-900 text-red-400 text-xs rounded">
                                Admin has absolute hardcoded access ('all') to all application capabilities. Permissions cannot be modified.
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                                @foreach([
                                    'user management', 'content management', 'settings management', 'theme management', 
                                    'email management', 'social login management', 'social media management', 
                                    'chat widget management', 'page management', 'footer management', 'seo management', 
                                    'cookie management', 'payment management', 'currency management', 'language management', 
                                    'roles and permissions management', 'article management', 'category management', 
                                    'tag management', 'comment management', 'notification management', 'contact message management', 
                                    'subscription management', 'polls management', 'quizzes management', 'rss management', 
                                    'webhooks management', 'api keys management', 'cache management', 'backup management', 
                                    'restore management', 'audit logs management', 'system information management'
                                ] as $perm)
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRolePermissions" value="{{ $perm }}" id="p_{{ Str::slug($perm) }}" class="rounded text-[#C8102E] border-gray-300">
                                        <label for="p_{{ Str::slug($perm) }}" class="ml-2 text-gray-700 dark:text-gray-300 cursor-pointer capitalize">{{ $perm }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pt-2 border-t border-gray-200 dark:border-gray-800">
                                <button type="button" wire:click="savePermissions" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">
                                    Save Permissions Configuration
                                </button>
                            </div>
                        @endif
                    </div>
                    @endcan
                </div>

                <!-- NEWSLETTER SUBSCRIPTIONS TAB -->
                <div x-show="activeTab === 'subscriptions'" class="space-y-4" style="display: none;">
                    @can('newsletter subscriptions management')
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Newsletter Subscription & Notification Management</h3>
                    
                    @if (session()->has('sub_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('sub_success') }}
                        </div>
                    @endif

                    @if (session()->has('import_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('import_success') }}
                        </div>
                    @endif

                    @if (session()->has('import_error'))
                        <div class="p-2.5 bg-red-900/10 border border-red-800 text-red-300 text-xs rounded">
                            {{ session('import_error') }}
                        </div>
                    @endif

                    <!-- Newsletter Popup Settings Group -->
                    <div class="space-y-4 bg-gray-50 dark:bg-gray-950 p-4 border border-gray-250 dark:border-gray-850 rounded-lg">
                        <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Newsletter Subscriber Signup Popup</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Popup Title</label>
                                <input type="text" wire:model="newsletter_popup_title" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Trigger Delay (seconds)</label>
                                <input type="number" wire:model="newsletter_popup_delay" min="0" max="60" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Popup Description</label>
                            <input type="text" wire:model="newsletter_popup_description" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="flex items-center pt-2">
                            <input type="checkbox" wire:model="newsletter_popup_enabled" id="newsletter_popup_enabled" class="rounded text-[#C8102E] border-gray-300">
                            <label for="newsletter_popup_enabled" class="ml-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">Enable Subscriber Popup Widget</label>
                        </div>
                    </div>

                    <!-- Import/Export Tools -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-950 p-4 border border-gray-250 dark:border-gray-850 rounded-lg">
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Import Subscribers List</h4>
                            <p class="text-[10px] text-gray-500">Upload a CSV or JSON file containing subscriber emails. In CSVs, we match columns named "email".</p>
                            
                            <div class="flex items-center space-x-2 pt-1">
                                <input type="file" wire:model="uploaded_subscribers_file" class="text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 dark:file:bg-gray-800 dark:file:text-gray-300 cursor-pointer">
                                <button type="button" wire:click="importSubscribers" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-3 py-1.5 rounded transition">Import File</button>
                            </div>
                            <div wire:loading wire:target="uploaded_subscribers_file" class="text-[10px] text-gray-400">Uploading file...</div>
                        </div>

                        <div class="space-y-2 border-l border-gray-200 dark:border-gray-800 pl-4">
                            <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Export Subscribers List</h4>
                            <p class="text-[10px] text-gray-500">Back up or migrate your active mailing list subscribers in either CSV or JSON formats.</p>
                            
                            <div class="flex space-x-2 pt-2">
                                <button type="button" wire:click="exportSubscribers('csv')" class="bg-gray-800 hover:bg-gray-750 text-white text-xs font-bold px-3 py-1.5 rounded transition">Export CSV</button>
                                <button type="button" wire:click="exportSubscribers('json')" class="bg-gray-800 hover:bg-gray-750 text-white text-xs font-bold px-3 py-1.5 rounded transition">Export JSON</button>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings Group -->
                    <div class="space-y-4 bg-gray-50 dark:bg-gray-950 p-4 border border-gray-250 dark:border-gray-850 rounded-lg">
                        <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">Notification & Push Configurations</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="notifications_enabled" id="notifications_enabled" class="rounded text-[#C8102E] border-gray-300">
                                <label for="notifications_enabled" class="ml-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">Enable System Notifications</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="notifications_push" id="notifications_push" class="rounded text-[#C8102E] border-gray-300">
                                <label for="notifications_push" class="ml-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">Allow Push Alerts</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="notifications_in_app" id="notifications_in_app" class="rounded text-[#C8102E] border-gray-300">
                                <label for="notifications_in_app" class="ml-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">Enable In-App Notifications</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="notifications_email" id="notifications_email" class="rounded text-[#C8102E] border-gray-300">
                                <label for="notifications_email" class="ml-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">Enable Email Alerts</label>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-800">
                            <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-[10px] px-3 py-1.5 rounded transition">
                                Save Notification Preferences
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 bg-gray-50 dark:bg-gray-950 p-4 border border-gray-250 dark:border-gray-850 rounded-lg">
                        <div class="flex-grow space-y-1">
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Manually Register Subscriber Email</label>
                            <div class="flex space-x-2">
                                <input type="email" wire:model="newSubscriberEmail" placeholder="user@domain.com" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs flex-grow text-gray-900 dark:text-white">
                                <button type="button" wire:click="addSubscriber" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded transition">Add</button>
                            </div>
                        </div>
                        <div class="w-full sm:w-48 space-y-1">
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Search Email Filter</label>
                            <input type="text" wire:model.live.debounce.250ms="newsletterSearch" placeholder="Search..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Database subscribers list -->
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-950 text-gray-500 font-bold border-b border-gray-200 dark:border-gray-800">
                                    <th class="p-3">Subscriber Email</th>
                                    <th class="p-3">Date Subscribed</th>
                                    <th class="p-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-850">
                                @forelse(\App\Models\Newsletter::query()->when(data_get($this, 'newsletterSearch') ?? data_get($this, 'newsletter_search'), fn($q) => $q->where('email', 'like', "%" . (data_get($this, 'newsletterSearch') ?? data_get($this, 'newsletter_search')) . "%"))->orderBy('created_at', 'desc')->get() as $sub)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/50">
                                        <td class="p-3 font-bold text-gray-900 dark:text-white">{{ $sub->email }}</td>
                                        <td class="p-3 text-gray-400">{{ $sub->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="p-3 text-right">
                                            <button type="button" wire:click="deleteSubscriber({{ $sub->id }})" class="text-red-550 font-bold hover:underline">Remove</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-gray-400">No active subscribers found in register database.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endcan
                </div>

                <!-- POLLS MANAGEMENT TAB -->
                <div x-show="activeTab === 'polls'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Polls Management Control</h3>
                    
                    @if (session()->has('poll_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('poll_success') }}
                        </div>
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-3">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Create New Audience Poll</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Poll Question</label>
                                <input type="text" wire:model="newPollQuestion" placeholder="e.g. Who will win the local election?" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Options (Comma separated)</label>
                                <input type="text" wire:model="newPollOptions" placeholder="e.g. Candidate A, Candidate B, Undecided" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                        </div>
                        <button type="button" wire:click="addPoll" class="bg-gray-850 hover:bg-gray-800 text-white font-bold text-xs px-4 py-2 rounded transition">Create Poll</button>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Active & Past Polls</h4>
                        <div class="space-y-3">
                            @forelse(json_decode(\App\Models\Setting::get('simulated_polls', '[]'), true) as $poll)
                                <div class="p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg flex justify-between items-start text-xs shadow-sm">
                                    <div class="space-y-2">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $poll['question'] }}</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($poll['options'] as $opt)
                                                <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded text-[10px]">{{ $opt }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button type="button" wire:click="deletePoll('{{ $poll['id'] }}')" class="text-red-550 font-bold text-[10px] hover:underline">Delete</button>
                                </div>
                            @empty
                                <p class="text-gray-400 text-center text-xs py-4">No polls recorded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- QUIZZES TAB -->
                <div x-show="activeTab === 'quizzes'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Quizzes Management Panel</h3>
                    
                    @if (session()->has('quiz_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('quiz_success') }}
                        </div>
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-3">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Create Interactive Reader Quiz</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Quiz Title</label>
                                <input type="text" wire:model="newQuizTitle" placeholder="e.g. Weekly News Trivia" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Number of Questions</label>
                                <input type="number" wire:model="newQuizQuestions" placeholder="e.g. 5" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                        </div>
                        <button type="button" wire:click="addQuiz" class="bg-gray-850 hover:bg-gray-800 text-white font-bold text-xs px-4 py-2 rounded transition">Generate Quiz</button>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Configured Reader Quizzes</h4>
                        <div class="space-y-3">
                            @forelse(json_decode(\App\Models\Setting::get('simulated_quizzes', '[]'), true) as $quiz)
                                <div class="p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg flex justify-between items-center text-xs shadow-sm">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $quiz['title'] }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1">Questions: <span class="font-bold font-mono text-gray-900 dark:text-white">{{ $quiz['questions_count'] }}</span></div>
                                    </div>
                                    <button type="button" wire:click="deleteQuiz('{{ $quiz['id'] }}')" class="text-red-550 font-bold text-[10px] hover:underline">Delete</button>
                                </div>
                            @empty
                                <p class="text-gray-400 text-center text-xs py-4">No quizzes recorded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- RSS FEED IMPORT TAB -->
                <div x-show="activeTab === 'rss'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">RSS Feed Auto-Import Management</h3>
                    
                    @if (session()->has('rss_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('rss_success') }}
                        </div>
                    @endif

                    @if (session()->has('rss_aggregated_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('rss_aggregated_success') }}
                        </div>
                    @endif

                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-950 p-4 border border-gray-250 dark:border-gray-850 rounded-lg">
                        <div>
                            <h4 class="text-xs font-bold text-gray-800 dark:text-gray-250 uppercase">Background Cron Job Aggregator</h4>
                            <p class="text-[10px] text-gray-500 mt-1">Last aggregated: {{ Setting::get('rss_last_aggregated_at', 'Never') }}</p>
                        </div>
                        <button type="button" wire:click="triggerRssAggregation" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">
                            Run Aggregation Now
                        </button>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-3">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Subscribe to external RSS feed channel</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Feed Label / Name</label>
                                <input type="text" wire:model="newRssFeedName" placeholder="e.g. BBC Africa" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">RSS XML Feed URL</label>
                                <input type="url" wire:model="newRssFeedUrl" placeholder="e.g. http://feeds.bbci.co.uk/news/world/africa/rss.xml" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                        </div>
                        <button type="button" wire:click="addRssFeed" class="bg-gray-850 hover:bg-gray-800 text-white font-bold text-xs px-4 py-2 rounded transition">Link Feed</button>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Subscribed Syndicated Feeds</h4>
                        <div class="space-y-2">
                            @forelse(json_decode(\App\Models\Setting::get('simulated_rss_feeds', '[]'), true) as $feed)
                                <div class="p-3.5 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg flex justify-between items-center text-xs shadow-sm">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $feed['name'] }}</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5 font-mono truncate max-w-sm sm:max-w-md">{{ $feed['url'] }}</div>
                                    </div>
                                    <button type="button" wire:click="deleteRssFeed('{{ $feed['id'] }}')" class="text-red-550 font-bold text-[10px] hover:underline">Unsubscribe</button>
                                </div>
                            @empty
                                <p class="text-gray-400 text-center text-xs py-4">No active RSS feeds registered.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- WEBHOOKS TAB -->
                <div x-show="activeTab === 'webhooks'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Outgoing Event Webhooks</h3>
                    
                    @if (session()->has('webhook_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('webhook_success') }}
                        </div>
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-3">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Register Webhook Endpoint URL</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Receiver Name</label>
                                <input type="text" wire:model="newWebhookName" placeholder="e.g. Telegram Channel Bridge" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Payload URL</label>
                                <input type="url" wire:model="newWebhookUrl" placeholder="e.g. https://api.myendpoint.com/webhook" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs">
                            </div>
                        </div>
                        <button type="button" wire:click="addWebhook" class="bg-gray-850 hover:bg-gray-800 text-white font-bold text-xs px-4 py-2 rounded transition">Save Endpoint</button>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Registered webhooks (dispatched on new articles)</h4>
                        <div class="space-y-2">
                            @forelse(json_decode(\App\Models\Setting::get('simulated_webhooks', '[]'), true) as $wh)
                                <div class="p-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg flex justify-between items-center text-xs">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $wh['name'] }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono truncate max-w-sm sm:max-w-md">{{ $wh['url'] }}</div>
                                    </div>
                                    <button type="button" wire:click="deleteWebhook('{{ $wh['id'] }}')" class="text-red-550 font-bold text-[10px] hover:underline">Revoke</button>
                                </div>
                            @empty
                                <p class="text-gray-400 text-center text-xs py-4">No outgoing webhooks registered.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- API KEYS TAB -->
                <div x-show="activeTab === 'api-keys'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Developer REST API Access Tokens</h3>
                    
                    @if (session()->has('api_success'))
                        <div class="p-2.5 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('api_success') }}
                        </div>
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-3">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">Generate secure developer access key</h4>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Token Application / Client Label</label>
                            <div class="flex space-x-2">
                                <input type="text" wire:model="newApiKeyName" placeholder="e.g. Mobile Application Client" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs flex-grow">
                                <button type="button" wire:click="generateApiKey" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Generate Token</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Active Access Tokens</h4>
                        <div class="space-y-2">
                            @forelse(json_decode(\App\Models\Setting::get('simulated_api_keys', '[]'), true) as $key)
                                <div class="p-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg flex justify-between items-center text-xs shadow-sm">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $key['name'] }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">Value mask: <span class="bg-gray-150 dark:bg-gray-800 px-1 py-0.2 rounded font-bold text-gray-800 dark:text-gray-200">{{ $key['key'] }}</span></div>
                                    </div>
                                    <button type="button" wire:click="deleteApiKey('{{ $key['id'] }}')" class="text-red-550 font-bold text-[10px] hover:underline">Revoke Access</button>
                                </div>
                            @empty
                                <p class="text-gray-400 text-center text-xs py-4">No active API keys generated.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- CACHE SYSTEM MANAGEMENT TAB -->
                <div x-show="activeTab === 'cache'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Cache Clear Controls</h3>
                    
                    @if (session()->has('cache_cleared'))
                        <div class="p-3 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('cache_cleared') }}
                        </div>
                    @endif

                    <div class="p-4 bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg space-y-3">
                        <div class="flex items-center space-x-4">
                            <button type="button" wire:click="clearCache" class="bg-gray-850 hover:bg-gray-800 border border-gray-750 text-white font-bold text-xs px-4 py-2 rounded transition">
                                Flush Application Caching Systems
                            </button>
                            <span class="text-xs text-gray-500">Purges compiled routing tables, view hashes, translation indexes, and standard session/model queries.</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg">
                            <span class="text-[10px] font-bold text-gray-400 block uppercase">OpCache Status</span>
                            <span class="text-gray-800 dark:text-white font-black text-sm">ENABLED</span>
                        </div>
                        <div class="p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg">
                            <span class="text-[10px] font-bold text-gray-400 block uppercase">Object Cache Store</span>
                            <span class="text-gray-800 dark:text-white font-black text-sm">PostgreSQL DB Cache (Ready)</span>
                        </div>
                    </div>
                </div>

                <!-- DATABASE BACKUP TAB -->
                <div x-show="activeTab === 'backup'" class="space-y-6" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">System Backup & Snapshot Restore Controls</h3>
                    
                    @if (session()->has('backup_success'))
                        <div class="p-3 bg-green-900/10 border border-green-800 text-green-300 text-xs rounded">
                            {{ session('backup_success') }}
                        </div>
                    @endif

                    <div class="p-4 bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white">Trigger Live Database and Assets Backup</h4>
                            <p class="text-[10px] text-gray-400 mt-1">Generates compressed ZIP snapshots containing upload directory file assets and SQL tables output.</p>
                        </div>
                        <button type="button" wire:click="runBackup" class="bg-[#C8102E] hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded transition shadow-sm">
                            Backup System Now
                        </button>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Archived Snapshots</h4>
                        <div class="space-y-2">
                            @forelse(json_decode(\App\Models\Setting::get('simulated_backups', '[]'), true) as $b)
                                <div class="p-4 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg flex justify-between items-center text-xs">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white font-mono">{{ $b['name'] }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1">Size: <span class="font-bold font-mono">{{ $b['size'] }}</span> &bull; Generated: {{ $b['created_at'] }}</div>
                                    </div>
                                    <div class="space-x-3 text-[10px] font-bold">
                                        <button type="button" wire:click="restoreBackup('{{ $b['id'] }}', '{{ $b['name'] }}')" class="text-blue-550 hover:underline">Restore</button>
                                        <button type="button" wire:click="deleteBackup('{{ $b['id'] }}')" class="text-red-550 hover:underline">Delete</button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 text-center text-xs py-4">No back-up archives recorded.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-750 dark:text-gray-250 uppercase">Recent restored records (restore verification)</h4>
                        <div class="space-y-1.5 font-mono text-[10px] text-gray-500">
                            @forelse(json_decode(\App\Models\Setting::get('simulated_restores', '[]'), true) as $res)
                                <div class="p-2.5 bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded">
                                    Restore from <span class="text-gray-850 dark:text-gray-200 font-bold">{{ $res['backup_name'] }}</span> was executed at {{ $res['restored_at'] }} [STATUS: OK]
                                </div>
                            @empty
                                <p class="text-gray-400 py-1">No restore actions logged in session.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <!-- STREAM SCHEDULES TAB -->
                <div x-show="activeTab === 'schedules'" class="space-y-6" style="display: none;">
                    <div class="flex justify-between items-center border-b border-gray-150 dark:border-gray-855 pb-2">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Live TV & Radio Broadcast Schedules</h3>
                        <div class="flex space-x-2 text-[10px] font-bold">
                            <button type="button" wire:click="$set('activeScheduleTab', 'tv')" class="px-3 py-1 rounded {{ $activeScheduleTab === 'tv' ? 'bg-[#C8102E] text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">TV Schedule</button>
                            <button type="button" wire:click="$set('activeScheduleTab', 'radio')" class="px-3 py-1 rounded {{ $activeScheduleTab === 'radio' ? 'bg-[#C8102E] text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">Radio Schedule</button>
                        </div>
                    </div>

                    <!-- Day selector tabs -->
                    <div class="flex flex-wrap gap-1 bg-gray-100 dark:bg-gray-800/40 p-1 rounded-lg text-xs">
                        @foreach(['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday', 'sunday' => 'Sunday'] as $key => $name)
                            <button type="button" wire:click="$set('activeScheduleDay', '{{ $key }}')" class="flex-grow sm:flex-none px-3 py-1.5 rounded-md font-bold transition-all {{ $activeScheduleDay === $key ? 'bg-[#C8102E] text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                {{ $name }}
                            </button>
                        @endforeach
                    </div>

                    @if($activeScheduleTab === 'tv')
                        <!-- TV Schedule Manager -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left: List -->
                            <div class="lg:col-span-2 space-y-3">
                                <h4 class="text-xs font-bold text-gray-550 dark:text-gray-400 uppercase tracking-wider">TV Program Slots ({{ ucfirst($activeScheduleDay) }})</h4>
                                <div class="space-y-3">
                                    @forelse(($tv_schedule[$activeScheduleDay] ?? []) as $index => $item)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-950 border border-gray-200 dark:border-gray-850 rounded-lg flex flex-col gap-3 text-xs {{ ($item['is_playing'] ?? false) ? 'border-l-4 border-[#C8102E]' : '' }}">
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full">
                                                <div>
                                                    <label class="text-[9px] font-bold text-gray-450 uppercase block mb-0.5">Time</label>
                                                    <input type="text" wire:model="tv_schedule.{{ $activeScheduleDay }}.{{ $index }}.time" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-bold text-gray-450 uppercase block mb-0.5">Title</label>
                                                    <input type="text" wire:model="tv_schedule.{{ $activeScheduleDay }}.{{ $index }}.title" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-bold text-gray-450 uppercase block mb-0.5">Description</label>
                                                    <input type="text" wire:model="tv_schedule.{{ $activeScheduleDay }}.{{ $index }}.desc" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between border-t border-gray-150 dark:border-gray-850 pt-2 text-[10px] font-bold">
                                                <div>
                                                    @if($item['is_playing'] ?? false)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-850 dark:bg-red-950/20 dark:text-[#C8102E]">ON AIR</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    @if(!($item['is_playing'] ?? false))
                                                        <button type="button" wire:click="setTvPlaying({{ $index }})" class="text-green-650 hover:underline">Set On-Air</button>
                                                    @endif
                                                    <button type="button" wire:click="removeTvProgram({{ $index }})" class="text-red-550 hover:underline">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-400 text-center py-4">No TV slots scheduled for {{ ucfirst($activeScheduleDay) }}.</p>
                                    @endforelse
                                </div>
                            </div>
                            <!-- Right: Add Form -->
                            <div class="bg-gray-50 dark:bg-gray-955 p-4 border border-gray-200 dark:border-gray-855 rounded-lg space-y-3 h-fit">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Add TV Program Slot</h4>
                                <div class="space-y-2">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Time Slot (e.g. 12:00 - 14:00)</label>
                                        <input type="text" wire:model="newTvTime" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Program Title</label>
                                        <input type="text" wire:model="newTvTitle" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Short Description</label>
                                        <textarea wire:model="newTvDesc" rows="2" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none"></textarea>
                                    </div>
                                    <button type="button" wire:click="addTvProgram" class="w-full bg-gray-900 hover:bg-gray-850 text-white font-bold text-[11px] py-1.5 rounded transition">Add Program</button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Radio Schedule Manager -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left: List -->
                            <div class="lg:col-span-2 space-y-3">
                                <h4 class="text-xs font-bold text-gray-550 dark:text-gray-400 uppercase tracking-wider">Radio Program Slots ({{ ucfirst($activeScheduleDay) }})</h4>
                                <div class="space-y-3">
                                    @forelse(($radio_schedule[$activeScheduleDay] ?? []) as $index => $item)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-955 border border-gray-200 dark:border-gray-850 rounded-lg flex flex-col gap-3 text-xs {{ ($item['is_playing'] ?? false) ? 'border-l-4 border-[#C8102E]' : '' }}">
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full">
                                                <div>
                                                    <label class="text-[9px] font-bold text-gray-455 uppercase block mb-0.5">Time</label>
                                                    <input type="text" wire:model="radio_schedule.{{ $activeScheduleDay }}.{{ $index }}.time" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-bold text-gray-455 uppercase block mb-0.5">Title</label>
                                                    <input type="text" wire:model="radio_schedule.{{ $activeScheduleDay }}.{{ $index }}.title" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-bold text-gray-455 uppercase block mb-0.5">Description</label>
                                                    <input type="text" wire:model="radio_schedule.{{ $activeScheduleDay }}.{{ $index }}.desc" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between border-t border-gray-150 dark:border-gray-850 pt-2 text-[10px] font-bold">
                                                <div>
                                                    @if($item['is_playing'] ?? false)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-855 dark:bg-red-950/20 dark:text-[#C8102E]">ON AIR</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    @if(!($item['is_playing'] ?? false))
                                                        <button type="button" wire:click="setRadioPlaying({{ $index }})" class="text-green-655 hover:underline">Set On-Air</button>
                                                    @endif
                                                    <button type="button" wire:click="removeRadioProgram({{ $index }})" class="text-red-555 hover:underline">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-400 text-center py-4">No Radio slots scheduled for {{ ucfirst($activeScheduleDay) }}.</p>
                                    @endforelse
                                </div>
                            </div>
                            <!-- Right: Add Form -->
                            <div class="bg-gray-50 dark:bg-gray-955 p-4 border border-gray-200 dark:border-gray-855 rounded-lg space-y-3 h-fit">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Add Radio Program Slot</h4>
                                <div class="space-y-2">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Time Slot (e.g. 13:00 - 16:00)</label>
                                        <input type="text" wire:model="newRadioTime" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Program Title</label>
                                        <input type="text" wire:model="newRadioTitle" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold text-gray-400">Short Description</label>
                                        <textarea wire:model="newRadioDesc" rows="2" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-1.5 text-xs text-gray-900 dark:text-white focus:outline-none"></textarea>
                                    </div>
                                    <button type="button" wire:click="addRadioProgram" class="w-full bg-gray-900 hover:bg-gray-850 text-white font-bold text-[11px] py-1.5 rounded transition">Add Program</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Schedules</button>
                    </div>
                </div>

                <!-- SECURITY & PRIVACY TAB -->
                <div x-show="activeTab === 'security'" class="space-y-6" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">Security & Privacy Controls</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Captcha Settings -->
                        <div class="bg-gray-50 dark:bg-gray-950 p-4 rounded-lg border border-gray-100 dark:border-gray-850 space-y-4">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Human Verification (Captcha)</h4>
                            
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-gray-400">Verification Driver</label>
                                <select wire:model="captcha_driver" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                                    <option value="none">Disabled (No Verification)</option>
                                    <option value="recaptcha">Google reCAPTCHA v2</option>
                                    <option value="turnstile">Cloudflare Turnstile</option>
                                </select>
                            </div>

                            <div x-show="captcha_driver === 'recaptcha'" class="space-y-3" x-cloak>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-400">reCAPTCHA Site Key</label>
                                    <input type="text" wire:model="recaptcha_site_key" placeholder="6L..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-400">reCAPTCHA Secret Key</label>
                                    <input type="password" wire:model="recaptcha_secret_key" placeholder="6L..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                                </div>
                            </div>

                            <div x-show="captcha_driver === 'turnstile'" class="space-y-3" x-cloak>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-400">Turnstile Site Key</label>
                                    <input type="text" wire:model="turnstile_site_key" placeholder="0x..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-bold text-gray-400">Turnstile Secret Key</label>
                                    <input type="password" wire:model="turnstile_secret_key" placeholder="0x..." class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Brute-Force & Lockouts -->
                        <div class="bg-gray-50 dark:bg-gray-950 p-4 rounded-lg border border-gray-100 dark:border-gray-850 space-y-4">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Brute-Force Lockout Protection</h4>
                            
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-gray-400">Max Failed Login Attempts</label>
                                <input type="number" wire:model="login_max_attempts" min="3" max="20" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-gray-400">Lockout Duration (seconds)</label>
                                <input type="number" wire:model="login_lockout_duration" min="60" max="86400" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                            </div>
                        </div>

                        <!-- Strict Password Rules -->
                        <div class="bg-gray-50 dark:bg-gray-950 p-4 rounded-lg border border-gray-100 dark:border-gray-850 space-y-4">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Password Complexity Rules</h4>
                            
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-gray-400">Minimum Password Length</label>
                                <input type="number" wire:model="password_min_length" min="6" max="30" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                            </div>

                            <div class="flex items-center space-x-2 pt-2">
                                <input type="checkbox" wire:model="password_complexity_required" id="password_complexity_required" class="rounded text-[#C8102E] focus:ring-[#C8102E] border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <label for="password_complexity_required" class="text-xs font-semibold text-gray-750 dark:text-gray-300">Require complexity (mixed case, numbers, symbols)</label>
                            </div>
                        </div>

                        <!-- Spam Protection & Rewards -->
                        <div class="bg-gray-50 dark:bg-gray-950 p-4 rounded-lg border border-gray-100 dark:border-gray-850 space-y-4">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">User Content & Author Rewards</h4>
                            
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="seo_nofollow_links" id="seo_nofollow_links" class="rounded text-[#C8102E] focus:ring-[#C8102E] border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <label for="seo_nofollow_links" class="text-xs font-semibold text-gray-750 dark:text-gray-300">Apply "nofollow" to external links in comments</label>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="seo_strip_links" id="seo_strip_links" class="rounded text-[#C8102E] focus:ring-[#C8102E] border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <label for="seo_strip_links" class="text-xs font-semibold text-gray-750 dark:text-gray-300">Completely strip links from comment text</label>
                            </div>

                            <div class="space-y-1 pt-2">
                                <label class="text-[11px] font-bold text-gray-400">Author Reward Rate (KES per valid view)</label>
                                <input type="text" wire:model="author_reward_rate" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Email Blacklist System -->
                    <div class="bg-gray-50 dark:bg-gray-950 p-4 rounded-lg border border-gray-100 dark:border-gray-850 space-y-3">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wide">Email Blacklist & Spam Filters</h4>
                        <p class="text-[11px] text-gray-500">Provide comma-separated email addresses or wildcard domains (e.g., `attacker@spammer.com, *@junk.ru, *@botmail.net`) to block registration or contact tips.</p>
                        
                        <textarea wire:model="email_blacklist" rows="4" placeholder="*@badactors.com, *@spam.org, badperson@gmail.com" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-3 text-xs font-mono focus:ring-[#C8102E] focus:border-[#C8102E] dark:text-white"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Security Settings</button>
                    </div>
                </div>

                <!-- AUDIT LOGS TAB -->
                <div x-show="activeTab === 'audit'" class="space-y-4" style="display: none;">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-800 pb-2">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Administrative Security Audit Log Trail</h3>
                        <button type="button" onclick="confirm('Clear audit logs?') && @this.set('simulated_audit_logs', '[]')" class="text-[10px] text-red-550 font-bold hover:underline">Clear Trail</button>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-950 text-gray-400 font-bold border-b border-gray-250 dark:border-gray-850 text-[10px]">
                                        <th class="p-3">Log Action Event</th>
                                        <th class="p-3">Triggered By User</th>
                                        <th class="p-3 text-right">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-850 font-mono text-[11px]">
                                    @forelse(json_decode(\App\Models\Setting::get('simulated_audit_logs', '[]'), true) as $log)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/50">
                                            <td class="p-3 font-semibold text-gray-800 dark:text-gray-200">{{ $log['action'] }}</td>
                                            <td class="p-3 text-gray-400">{{ $log['user'] }}</td>
                                            <td class="p-3 text-right text-gray-500">{{ $log['created_at'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-8 text-center text-gray-400 font-sans">No administrative actions logged in trail.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FEATURED LAYOUT & BREAKING ALERTS TAB -->
                <div x-show="activeTab === 'featured'" class="space-y-6" style="display: none;" wire:ignore.self>
                    <div class="border-b border-gray-100 dark:border-gray-800 pb-2">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Homepage Slider & Pinned Content Controls</h3>
                        <p class="text-[10px] text-gray-550 mt-1">Manage articles displaying in main sliders, hero blocks, and featured tickers.</p>
                    </div>

                    <!-- Pinned & Featured Sliders -->
                    <div class="bg-gray-50 dark:bg-gray-950 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-4">
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase">Slider & Featured Pinned Articles</h4>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-900 text-gray-400 font-bold border-b border-gray-250 dark:border-gray-850 text-[10px]">
                                        <th class="p-3">Article Title</th>
                                        <th class="p-3">Category</th>
                                        <th class="p-3 text-center">Pinned (Hero)</th>
                                        <th class="p-3 text-center">Featured (Grid)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-850 text-gray-700 dark:text-gray-300">
                                    @forelse($pinned_articles_list as $pinnedArt)
                                        <tr class="hover:bg-gray-100/50 dark:hover:bg-gray-900/50">
                                            <td class="p-3 font-semibold text-gray-900 dark:text-white">{{ $pinnedArt->title }}</td>
                                            <td class="p-3 text-gray-500">{{ $pinnedArt->category->name ?? 'Uncategorized' }}</td>
                                            <td class="p-3 text-center">
                                                <button type="button" wire:click="toggleArticlePinned({{ $pinnedArt->id }}, 'is_pinned')" class="text-xs font-bold px-2 py-0.5 rounded {{ $pinnedArt->is_pinned ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $pinnedArt->is_pinned ? 'Pinned' : 'Pin' }}
                                                </button>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button type="button" wire:click="toggleArticlePinned({{ $pinnedArt->id }}, 'is_featured')" class="text-xs font-bold px-2 py-0.5 rounded {{ $pinnedArt->is_featured ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $pinnedArt->is_featured ? 'Featured' : 'Feature' }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-gray-400 font-sans">No currently pinned or featured articles. Pin them from the Articles section.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Breaking News Ticker Manager -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Add Breaking Alert Form -->
                        <div class="bg-gray-50 dark:bg-gray-955 border border-gray-250 dark:border-gray-850 rounded-lg p-4 space-y-4 self-start">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase">Add Breaking Alert</h4>
                            
                            @if (session()->has('breaking_success'))
                                <div class="p-2 bg-green-900/10 border border-green-800 text-green-300 text-[10px] rounded">
                                    {{ session('breaking_success') }}
                                </div>
                            @endif

                            <div class="space-y-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Alert Title / Headline</label>
                                    <input type="text" wire:model="breaking_title" placeholder="e.g. Kisii County Assembly passes annual budget" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    @error('breaking_title') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Action link URL (Optional)</label>
                                    <input type="url" wire:model="breaking_link" placeholder="e.g. https://getembenews.com/articles/budget" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    @error('breaking_link') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Display Priority</label>
                                    <input type="number" wire:model="breaking_priority" min="1" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    @error('breaking_priority') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">Expiration Date (Optional)</label>
                                    <input type="date" wire:model="breaking_expires_at" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                                    @error('breaking_expires_at') <p class="text-red-500 text-[10px]">{{ $message }}</p> @enderror
                                </div>

                                <button type="button" wire:click="addBreakingNews" class="w-full bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs py-2 rounded transition">
                                    Save Breaking Alert
                                </button>
                            </div>
                        </div>

                        <!-- Active Alerts List -->
                        <div class="lg:col-span-2 bg-gray-50 dark:bg-gray-955 border border-gray-250 dark:border-gray-855 rounded-lg p-4 space-y-4">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase">Breaking Ticker Queue</h4>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-gray-100 dark:bg-gray-900 text-gray-400 font-bold border-b border-gray-250 dark:border-gray-855 text-[10px]">
                                            <th class="p-3">Alert Headline</th>
                                            <th class="p-3">Priority</th>
                                            <th class="p-3">Status</th>
                                            <th class="p-3 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-150 dark:divide-gray-850">
                                        @forelse($breaking_news_list as $alert)
                                            <tr class="hover:bg-gray-100/50 dark:hover:bg-gray-900/50">
                                                <td class="p-3 font-semibold text-gray-850 dark:text-gray-250">
                                                    <div>{{ $alert->title }}</div>
                                                    @if($alert->link)
                                                        <div class="text-[9px] text-[#C8102E] hover:underline truncate max-w-xs font-mono"><a href="{{ $alert->link }}" target="_blank">{{ $alert->link }}</a></div>
                                                    @endif
                                                </td>
                                                <td class="p-3 text-gray-550 font-bold">{{ $alert->priority }}</td>
                                                <td class="p-3">
                                                    <button type="button" wire:click="toggleBreakingNews({{ $alert->id }})" class="text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $alert->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $alert->is_active ? 'Active' : 'Paused' }}
                                                    </button>
                                                </td>
                                                <td class="p-3 text-right">
                                                    <button type="button" wire:click="deleteBreakingNews({{ $alert->id }})" class="text-red-550 hover:underline font-bold text-[10px]">Remove</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="p-8 text-center text-gray-400 font-sans">No breaking alerts found in database.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
    </div>
</div>

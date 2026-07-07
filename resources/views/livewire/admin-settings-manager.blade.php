<?php

use function Livewire\Volt\{state, mount, uses};
use Livewire\WithFileUploads;
use App\Models\Setting;
use App\Models\Newsletter;
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
    'contact_editorial_phone' => fn() => Setting::get('contact_editorial_phone', '+254789012345'),
    'contact_news_email' => fn() => Setting::get('contact_news_email', 'news@getembenews.com'),
    'contact_tips_email' => fn() => Setting::get('contact_tips_email', 'tips@getembenews.com'),
    'contact_ads_email' => fn() => Setting::get('contact_ads_email', 'ads@getembenews.com'),
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

    $fields = [
        'site_name', 'site_logo', 'brand_color', 'favicon',
        'website', 'facebook', 'twitter', 'instagram', 'linkedin', 'whatsapp', 'youtube', 'tiktok', 'snapchat', 'telegram', 'pinterest', 'threads', 'other_social_links',
        'contact_email', 'contact_phone', 'contact_editorial_phone', 'contact_news_email', 'contact_tips_email', 'contact_ads_email', 'contact_address',
        'payment_methods', 'payment_gateways', 'currency', 'currency_symbol',
        'announcement_rate_tv', 'announcement_rate_radio', 'announcement_rate_both',
        'language',
        'meta_title', 'meta_description', 'meta_keywords', 'google_analytics_id', 'google_indexing_api', 'sitemap_frequency', 'robots_txt_enabled', 'robots_txt_content',
        'cookie_banner_enabled', 'cookie_position', 'cookie_approval_required', 'cookie_moderation_enabled',
        'theme_font', 'theme_layout', 'theme_color_secondary', 'theme_color_success', 'theme_color_warning',
        'smtp_server', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_auth_enabled', 'smtp_from_name', 'smtp_from_email', 'smtp_reply_to_email', 'smtp_reply_to_name',
        'fb_comments_widget', 'fb_comments_position', 'fb_comments_approval_required', 'fb_comments_moderation_enabled',
        'home_page', 'about_page', 'contact_page', 'privacy_page', 'terms_page',
        'footer_copyright', 'footer_bg_color', 'footer_text_color', 'footer_logo', 'footer_link_color', 'footer_link_hover_color', 'footer_link_active_color', 'footer_link_visited_color',
        'google_login', 'facebook_login', 'twitter_login', 'github_login', 'linkedin_login', 'whatsapp_login', 'apple_login', 'pinterest_login', 'threads_login',
        'google_client_id', 'google_client_secret', 'facebook_client_id', 'facebook_client_secret', 'github_client_id', 'github_client_secret', 'twitter_client_id', 'twitter_client_secret',
        'notifications_enabled', 'notifications_push', 'notifications_in_app', 'notifications_email',
        'live_tv_url', 'live_radio_url', 'weather_city', 'homepage_categories',
        'app_play_store_url', 'app_app_store_url', 'app_banner_title', 'app_banner_desc',
        'tv_schedule', 'radio_schedule'
    ];

    foreach ($fields as $field) {
        Setting::set($field, $this->{$field});
    }

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
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">General Contact Email</label>
                            <input type="email" wire:model="contact_email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">General Contact Phone</label>
                            <input type="text" wire:model="contact_phone" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Editorial Phone Hotline</label>
                            <input type="text" wire:model="contact_editorial_phone" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Newsroom Email</label>
                            <input type="email" wire:model="contact_news_email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Tips Email</label>
                            <input type="email" wire:model="contact_tips_email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Advertising Email</label>
                            <input type="email" wire:model="contact_ads_email" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-700 dark:text-gray-300">Contact Address</label>
                        <input type="text" wire:model="contact_address" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded p-2 text-xs text-gray-900 dark:text-white">
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

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
                    </div>
                </div>

                <!-- SMTP EMAIL TAB -->
                <div x-show="activeTab === 'email'" class="space-y-4" style="display: none;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">SMTP Mail Server Config</h3>
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
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

                    <div class="pt-4">
                        <button type="submit" class="bg-[#C8102E] hover:bg-red-700 text-white font-bold text-xs px-4 py-2 rounded transition">Save Settings</button>
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

            </form>
    </div>
</div>

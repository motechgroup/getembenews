<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('access-admin', function (\App\Models\User $user) {
            return $user->isStaff();
        });

        $permissions = [
            'user management',
            'content management',
            'settings management',
            'theme management',
            'email management',
            'social login management',
            'social media management',
            'chat widget management',
            'page management',
            'footer management',
            'seo management',
            'cookie management',
            'payment management',
            'currency management',
            'language management',
            'roles and permissions management',
            'article management',
            'category management',
            'tag management',
            'comment management',
            'notification management',
            'contact message management',
            'subscription management',
            'polls management',
            'quizzes management',
            'rss management',
            'webhooks management',
            'api keys management',
            'cache management',
            'backup management',
            'restore management',
            'audit logs management',
            'system information management',
        ];

        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\Gate::define($permission, function (\App\Models\User $user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}

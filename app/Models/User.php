<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'bio', 'photo_url', 'social_links'];
    protected $hidden = ['password', 'remember_token'];

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        \App\Support\Mailer::sendPasswordReset($this->email, $token);
        
        if (app()->runningUnitTests()) {
            $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
        }
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );

        \App\Support\Mailer::sendEmailVerification($this->email, $url);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
        ];
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isReporter(): bool
    {
        return $this->role === 'reporter';
    }

    public function isContributor(): bool
    {
        return $this->role === 'contributor';
    }

    public function isSubscriber(): bool
    {
        return $this->role === 'subscriber';
    }

    public function isAuthor(): bool
    {
        return in_array($this->role, ['author', 'reporter', 'contributor']);
    }

    public function isUser(): bool
    {
        return in_array($this->role, ['user', 'subscriber']);
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    // Check if user has staff privileges
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'editor', 'reporter', 'contributor', 'author', 'manager']);
    }

    // Check dynamic permissions
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        $defaultRolesPermissions = [
            'admin' => ['all'],
            'editor' => [
                'content management',
                'article management',
                'category management',
                'comment management',
                'tag management',
                'page management',
                'contact message management',
                'settings management',
                'theme management',
                'email management',
                'seo management',
                'cookie management',
                'notification management',
                'subscription management',
                'polls management',
                'quizzes management',
                'rss management',
                'webhooks management',
                'api keys management',
                'cache management',
                'announcement management',
            ],
            'manager' => [
                'announcement management',
            ],
            'author' => [
                'content management',
                'article management',
                'writing article',
            ],
            'reporter' => [
                'content management',
                'article management',
            ],
            'contributor' => [
                'content management',
                'article management',
            ],
            'user' => [],
            'subscriber' => [],
        ];

        $rolesPermissions = json_decode(\App\Models\Setting::get('roles_permissions', '{}'), true);

        if (!is_array($rolesPermissions)) {
            $rolesPermissions = [];
        }

        $userRole = $this->role;
        
        $permissions = [];
        if (isset($rolesPermissions[$userRole]['perms'])) {
            $permissions = $rolesPermissions[$userRole]['perms'];
        } elseif (isset($rolesPermissions[$userRole]) && is_array($rolesPermissions[$userRole])) {
            $permissions = $rolesPermissions[$userRole];
        } elseif (isset($defaultRolesPermissions[$userRole])) {
            $permissions = $defaultRolesPermissions[$userRole];
        }

        return in_array('all', $permissions) || in_array($permission, $permissions);
    }

    // Relationships
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function savedArticles()
    {
        return $this->belongsToMany(Article::class, 'saved_articles', 'user_id', 'article_id');
    }
}

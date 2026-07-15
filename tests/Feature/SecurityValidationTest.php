<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Newsletter;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Volt\Volt;
use Tests\TestCase;

class SecurityValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test contact form sanitizes HTML tags from name, subject, and message.
     */
    public function test_contact_form_sanitizes_html_inputs(): void
    {
        Volt::test('contact-form')
            ->set('name', '<b>John Doe</b>')
            ->set('email', 'john@example.com')
            ->set('subject', '<i>General inquiry</i>')
            ->set('message', '<script>alert("hack")</script>This is a safe message.')
            ->call('submit');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Doe',
            'subject' => 'General inquiry',
            'message' => 'alert("hack")This is a safe message.',
        ]);
    }

    /**
     * Test newsletter subscribe form sanitizes and trims email input.
     */
    public function test_newsletter_form_sanitizes_email(): void
    {
        Volt::test('newsletter-form')
            ->set('email', 'HACKER@EXAMPLE.COM')
            ->call('subscribe');

        $this->assertDatabaseHas('newsletters', [
            'email' => 'hacker@example.com',
        ]);
    }

    /**
     * Test newsletter popup sanitizes and trims email input.
     */
    public function test_newsletter_popup_sanitizes_email(): void
    {
        Volt::test('newsletter-popup')
            ->set('email', 'POPUP-HACKER@EXAMPLE.COM')
            ->call('subscribe');

        $this->assertDatabaseHas('newsletters', [
            'email' => 'popup-hacker@example.com',
        ]);
    }

    /**
     * Test announcement submit component sanitizes visitor inputs and content.
     */
    public function test_announcement_submission_sanitizes_inputs(): void
    {
        Volt::test('announcement-submit')
            ->set('visitor_name', '<b>Hacker Visitor</b>')
            ->set('visitor_email', 'visitor@example.com')
            ->set('visitor_phone', '0712345678')
            ->set('type', 'general')
            ->set('media', 'tv')
            ->set('content', '<i>Funeral announcement info here.</i>')
            ->set('days_count', 1)
            ->set('submitter_type', 'self')
            ->call('submitAnnouncement');

        $this->assertDatabaseHas('announcements', [
            'visitor_name' => 'Hacker Visitor',
            'content' => 'Funeral announcement info here.',
        ]);
    }

    /**
     * Test API registration and update profile sanitize inputs.
     */
    public function test_api_endpoints_sanitize_inputs(): void
    {
        // 1. Register API
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '<b>Subscriber Hacker</b>',
            'email' => '  hacker@subscriber.com  ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'Subscriber Hacker',
            'email' => 'hacker@subscriber.com',
        ]);

        $user = User::where('email', 'hacker@subscriber.com')->first();
        $token = $response->json('data.token');

        // 2. Update Profile API
        $profileResponse = $this->putJson('/api/v1/auth/profile', [
            'name' => '<i>Subscriber Safe Name</i>',
            'bio' => '<b>Subscriber Bio</b>',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $profileResponse->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Subscriber Safe Name',
            'bio' => 'Subscriber Bio',
        ]);
    }

    /**
     * Test API feedback submission sanitizes inputs.
     */
    public function test_api_feedback_sanitizes_inputs(): void
    {
        $response = $this->postJson('/api/v1/contact', [
            'name' => '<b>John Feedback</b>',
            'email' => 'john@feedback.com',
            'subject' => '<i>Inquiry</i>',
            'message' => '<script>alert("hi")</script>My message content.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Feedback',
            'subject' => 'Inquiry',
            'message' => 'alert("hi")My message content.',
        ]);
    }

    /**
     * Test API newsletter subscribe sanitizes email.
     */
    public function test_api_newsletter_subscribe_sanitizes_email(): void
    {
        $response = $this->postJson('/api/v1/newsletter/subscribe', [
            'email' => 'SUBSCRIBE@API.COM',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('newsletters', [
            'email' => 'subscribe@api.com',
        ]);
    }

    /**
     * Test API comment post sanitizes body.
     */
    public function test_api_comment_sanitizes_body(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $category = Category::create(['name' => 'Politics', 'slug' => 'politics']);
        $article = Article::create([
            'title' => 'Article title',
            'slug' => 'article-title',
            'body' => 'body text',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'format' => 'article',
        ]);

        $response = $this->postJson("/api/v1/articles/{$article->id}/comment", [
            'body' => '<b>Hacker comment body</b>',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'body' => 'Hacker comment body',
        ]);
    }

    /**
     * Test API rate limiters are active.
     */
    public function test_api_rate_limiter_throttles_requests(): void
    {
        // Use a mock route or repeatedly hit registration to trigger throttling
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/v1/auth/login', [
                'email' => 'nonexistent@user.com',
                'password' => 'wrong-pass',
            ]);
            $response->assertStatus(422);
        }

        // The 6th attempt should be throttled (Too Many Requests - 429 status code)
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@user.com',
            'password' => 'wrong-pass',
        ]);
        $response->assertStatus(429);
    }
}

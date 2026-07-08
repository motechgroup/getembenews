<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PublishingAndMediaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test scheduled publishing visibility rules.
     */
    public function test_scheduled_publishing_only_visible_after_time_elapsed(): void
    {
        $category = Category::create([
            'name' => 'News',
            'slug' => 'news',
            'order' => 1
        ]);
        
        $author = User::factory()->create(['role' => 'admin']);

        // 1. Published post (immediately visible)
        $visiblePost = Article::create([
            'title' => 'Immediately Visible',
            'slug' => 'immediately-visible',
            'body' => 'Visible article body.',
            'category_id' => $category->id,
            'user_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subMinutes(5)
        ]);

        // 2. Scheduled post (future date, not visible)
        $scheduledPost = Article::create([
            'title' => 'Scheduled Future',
            'slug' => 'scheduled-future',
            'body' => 'Scheduled article body.',
            'category_id' => $category->id,
            'user_id' => $author->id,
            'status' => 'scheduled',
            'published_at' => now()->addHours(2)
        ]);

        // 3. Draft post (not visible)
        $draftPost = Article::create([
            'title' => 'Draft Story',
            'slug' => 'draft-story',
            'body' => 'Draft body.',
            'category_id' => $category->id,
            'user_id' => $author->id,
            'status' => 'draft'
        ]);

        $published = Article::published()->get();

        $this->assertCount(1, $published);
        $this->assertTrue($published->contains($visiblePost));
        $this->assertFalse($published->contains($scheduledPost));
        $this->assertFalse($published->contains($draftPost));
    }

    /**
     * Test admin media library manager upload functionality.
     */
    public function test_media_library_upload_and_deletion(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $fakeFile = UploadedFile::fake()->image('photo.jpg', 500, 500);

        Livewire::test('admin-media-manager')
            ->set('uploadedFile', $fakeFile)
            ->call('upload')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('media', 1);
        $media = Media::first();
        $this->assertEquals('photo.jpg', $media->filename);
        $this->assertEquals('image/jpeg', $media->mime_type);
        Storage::disk('public')->assertExists($media->path);

        // Delete media
        Livewire::test('admin-media-manager')
            ->call('deleteMedia', $media->id);

        $this->assertDatabaseCount('media', 0);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test dynamic post formats, tags, and workflow approval controls.
     */
    public function test_post_format_builders_tags_and_editor_approval_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reporter = User::factory()->create(['role' => 'reporter']);

        $category = Category::create([
            'name' => 'Cooking',
            'slug' => 'cooking',
            'order' => 1
        ]);

        // 1. Reporter publishes an article -> forced to pending status
        $this->actingAs($reporter);

        Livewire::test('admin-articles-manager')
            ->set('isEditing', true)
            ->set('title', 'Spaghetti Recipe')
            ->set('slug', 'spaghetti-recipe')
            ->set('body', 'Cook pasta in salted water.')
            ->set('category_id', $category->id)
            ->set('status', 'published') // request publishing
            ->set('format', 'recipe')
            ->set('format_meta', [
                'recipe' => [
                    'prep_time' => '15',
                    'cook_time' => '20',
                    'yield' => '4'
                ]
            ])
            ->set('faq_items', [
                ['question' => 'How much salt?', 'answer' => 'One tablespoon.']
            ])
            ->set('downloads', [
                ['label' => 'Recipe PDF', 'url' => 'http://localhost/downloads/recipe.pdf']
            ])
            ->set('tags_input', 'pasta, dinner, easy')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('articles', 1);
        $article = Article::first();

        // Must be in pending status, not published (since reporter created it)
        $this->assertEquals('pending', $article->status);
        $this->assertEquals('recipe', $article->format);
        $this->assertEquals('15', $article->format_meta['recipe']['prep_time']);
        $this->assertEquals('One tablespoon.', $article->faq_items[0]['answer']);
        $this->assertEquals('Recipe PDF', $article->downloads[0]['label']);

        // Check tags created
        $this->assertDatabaseCount('tags', 3);
        $this->assertCount(3, $article->tags);
        $this->assertEquals('pasta', $article->tags[0]->name);

        // 2. Admin logs in and approves the article
        $this->actingAs($admin);

        Livewire::test('admin-articles-manager')
            ->call('approve', $article->id)
            ->assertHasNoErrors();

        $article->refresh();
        $this->assertEquals('published', $article->status);
        $this->assertNotNull($article->published_at);
    }

    /**
     * Test per-post FAQ JSON schema markup creation.
     */
    public function test_post_faq_schemas_rendered_in_seo_metadata(): void
    {
        $author = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
            'order' => 1
        ]);

        $article = Article::create([
            'title' => 'Kisii Hub launch FAQ',
            'slug' => 'kisii-hub-launch-faq',
            'body' => 'Launch guidelines details.',
            'category_id' => $category->id,
            'user_id' => $author->id,
            'status' => 'published',
            'published_at' => now(),
            'faq_items' => [
                ['question' => 'Where is the hub?', 'answer' => 'Kisii Town center.']
            ]
        ]);

        $schemaJson = Seo::generateSchema(['article' => $article]);
        
        $this->assertStringContainsString('FAQPage', $schemaJson);
        $this->assertStringContainsString('Where is the hub?', $schemaJson);
        $this->assertStringContainsString('Kisii Town center.', $schemaJson);
    }

    /**
     * Test admin terms and privacy pages management.
     */
    public function test_admin_can_manage_terms_and_privacy_policy_pages(): void
    {
        \Illuminate\Support\Facades\Cache::flush();

        // 1. Check default terms and privacy policies render correctly
        $this->get('/privacy')
            ->assertOk()
            ->assertSee('At Getembe News, accessible from getembenews.com');

        $this->get('/terms')
            ->assertOk()
            ->assertSee('Welcome to Getembe News. By accessing or using our website');

        // 2. Admin logs in and updates legal page contents
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test('admin-settings-manager')
            ->set('activeTab', 'pages')
            ->set('privacy_content', '<h3>Custom Privacy Content Here</h3>')
            ->set('terms_content', '<h3>Custom Terms Content Here</h3>')
            ->call('save')
            ->assertHasNoErrors();

        // 3. Verify public routes reflect new settings contents
        $this->get('/privacy')
            ->assertOk()
            ->assertSeeHtml('<h3>Custom Privacy Content Here</h3>')
            ->assertDontSee('At Getembe News, accessible from getembenews.com');

        $this->get('/terms')
            ->assertOk()
            ->assertSeeHtml('<h3>Custom Terms Content Here</h3>')
            ->assertDontSee('Welcome to Getembe News. By accessing or using our website');
    }

    /**
     * Test auto-categorization of audio posts to podcast and toggle settings.
     */
    public function test_audio_post_format_auto_categorization_and_podcast_category_enabled_toggle(): void
    {
        \Illuminate\Support\Facades\Cache::flush();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $politicsCat = Category::create([
            'name' => 'Politics',
            'slug' => 'politics',
            'order' => 1
        ]);

        // 1. Podcast Category Enabled (Default): Audio post automatically assigned to "Podcast" category
        \App\Models\Setting::set('podcast_category_enabled', true);

        Livewire::test('admin-articles-manager')
            ->set('isEditing', true)
            ->set('title', 'My Audio Podcast Episode')
            ->set('slug', 'my-audio-podcast-episode')
            ->set('body', 'Podcast audio show notes.')
            ->set('category_id', $politicsCat->id)
            ->set('status', 'published')
            ->set('format', 'audio')
            ->set('format_meta', [
                'audio' => [
                    'audio_url' => 'http://localhost/podcast.mp3'
                ]
            ])
            ->call('save')
            ->assertHasNoErrors();

        $article = Article::where('slug', 'my-audio-podcast-episode')->first();
        $this->assertNotNull($article);
        $this->assertNotEquals($politicsCat->id, $article->category_id);
        $this->assertEquals('podcast', $article->category->slug);
        $this->assertEquals('Podcast', $article->category->name);

        // 2. Podcast Category Disabled: Audio post stays in the manually assigned category
        \App\Models\Setting::set('podcast_category_enabled', false);

        Livewire::test('admin-articles-manager')
            ->set('isEditing', true)
            ->set('title', 'Another Podcast Episode')
            ->set('slug', 'another-podcast-episode')
            ->set('body', 'Standard audio notes.')
            ->set('category_id', $politicsCat->id)
            ->set('status', 'published')
            ->set('format', 'audio')
            ->set('format_meta', [
                'audio' => [
                    'audio_url' => 'http://localhost/podcast2.mp3'
                ]
            ])
            ->call('save')
            ->assertHasNoErrors();

        $article2 = Article::where('slug', 'another-podcast-episode')->first();
        $this->assertNotNull($article2);
        $this->assertEquals($politicsCat->id, $article2->category_id);
    }

    /**
     * Test admin toggle on/off menu items hides/shows them in frontend.
     */
    public function test_navigation_menu_items_can_be_toggled_active_or_disabled(): void
    {
        \Illuminate\Support\Facades\Cache::flush();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 1. Set a header link and check it shows on public homepage
        \App\Models\Setting::set('header_menu', [
            ['label' => 'SHOULD BE SHOWN LINK', 'url' => '/shown-link', 'is_disabled' => false]
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('SHOULD BE SHOWN LINK');

        // 2. Toggle item to disabled using Menus Manager
        Livewire::test('admin-menus-manager')
            ->set('activeMenu', 'header')
            ->call('toggleItem', 0) // toggle to disabled (true)
            ->call('saveMenu')
            ->assertHasNoErrors();

        // 3. Verify public routes filter it out
        $this->get('/')
            ->assertOk()
            ->assertDontSee('SHOULD BE SHOWN LINK');
    }
}

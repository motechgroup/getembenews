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

    /**
     * Test rendering of inline image shortcodes in articles.
     */
    public function test_inline_image_shortcode_rendering(): void
    {
        $author = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
            'order' => 1
        ]);

        $article = Article::create([
            'title' => 'Article with Inline Image',
            'slug' => 'article-with-inline-image',
            'body' => 'Before the image [image url="https://example.com/test-image.jpg"] and after the image.',
            'category_id' => $category->id,
            'user_id' => $author->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/articles/article-with-inline-image')
            ->assertOk()
            ->assertSeeHtml('<img src="https://example.com/test-image.jpg" alt="Article Inline Image"');
    }

    /**
     * Test Trix editor image selection and media library connection.
     */
    public function test_trix_editor_image_selection_event_flows(): void
    {
        $author = User::factory()->create(['role' => 'admin']);
        $this->actingAs($author);

        Livewire::test('admin-articles-manager')
            ->dispatch('media-selected', url: 'https://example.com/selected-inline-image.jpg', targetField: 'trix_body')
            ->assertDispatched('insert-trix-image', url: 'https://example.com/selected-inline-image.jpg');
    }

    /**
     * Test media manager and select modal restrict files to the uploader unless user is admin.
     */
    public function test_media_isolation_by_user_role_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author1 = User::factory()->create(['role' => 'author']);
        $author2 = User::factory()->create(['role' => 'author']);

        // Upload media for author1
        $media1 = Media::create([
            'filename' => 'author1_photo.jpg',
            'path' => 'uploads/author1_photo.jpg',
            'url' => '/storage/uploads/author1_photo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'user_id' => $author1->id,
        ]);

        // Upload media for author2
        $media2 = Media::create([
            'filename' => 'author2_photo.jpg',
            'path' => 'uploads/author2_photo.jpg',
            'url' => '/storage/uploads/author2_photo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'user_id' => $author2->id,
        ]);

        // 1. Author 1 logged in -> sees only media1
        $this->actingAs($author1);
        Livewire::test('admin-media-manager')
            ->assertViewHas('mediaFiles', function ($files) use ($media1, $media2) {
                return $files->contains($media1) && !$files->contains($media2);
            });

        $testModal1 = Livewire::test('media-select-modal');
        $files1 = $testModal1->instance()->mediaList();
        $this->assertTrue($files1->contains($media1));
        $this->assertFalse($files1->contains($media2));

        // Author 1 tries to delete Author 2's media -> denied
        Livewire::test('admin-media-manager')
            ->call('deleteMedia', $media2->id)
            ->assertSee('You are not authorized to delete this media file.');

        $this->assertDatabaseHas('media', ['id' => $media2->id]);

        // 2. Admin logged in -> sees both media1 and media2
        $this->actingAs($admin);
        Livewire::test('admin-media-manager')
            ->assertViewHas('mediaFiles', function ($files) use ($media1, $media2) {
                return $files->contains($media1) && $files->contains($media2);
            });

        $testModal2 = Livewire::test('media-select-modal');
        $files2 = $testModal2->instance()->mediaList();
        $this->assertTrue($files2->contains($media1));
        $this->assertTrue($files2->contains($media2));
    }

    /**
     * Test image compression and watermarking on upload.
     */
    public function test_image_compression_and_watermarking_on_upload(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Create a fake heavy JPEG image (above 1MB) using uploader fake helper
        $fakeFile = UploadedFile::fake()->create('heavy_photo.jpg', 1200, 'image/jpeg');
        $tempPath = $fakeFile->getPathname();

        // Overwrite temp file with a valid complex JPEG image to ensure it is > 1MB and valid
        $im = imagecreatetruecolor(2000, 2000);
        for ($i = 0; $i < 2000; $i += 10) {
            for ($j = 0; $j < 2000; $j += 10) {
                $color = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefilledrectangle($im, $i, $j, $i + 9, $j + 9, $color);
            }
        }
        imagejpeg($im, $tempPath, 100);
        imagedestroy($im);

        $originalSize = filesize($tempPath);
        $this->assertTrue($originalSize > 1 * 1024 * 1024);

        Livewire::test('admin-media-manager')
            ->set('uploadedFile', $fakeFile)
            ->call('upload')
            ->assertHasNoErrors();

        // Verify image processed: watermark applied & compressed
        $this->assertDatabaseCount('media', 1);
        $media = Media::first();
        
        $savedPath = Storage::disk('public')->path($media->path);
        $this->assertTrue(file_exists($savedPath));
        
        // Assert file size has decreased
        $newSize = filesize($savedPath);
        $this->assertTrue($newSize < $originalSize, "Expected compressed size ($newSize) to be less than original size ($originalSize)");

        // Assert database size column matches the new file size
        $this->assertEquals($newSize, $media->size);

        // Assert watermark is written (the image can still be read by GD)
        $img = imagecreatefromjpeg($savedPath);
        $this->assertNotFalse($img);
        imagedestroy($img);

        // Cleanup temp file
        @unlink($tempPath);
    }

    /**
     * Test post-save watermarking on article featured image and inline body images.
     */
    public function test_post_save_watermarking_on_article_images(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $category = Category::create(['name' => 'General', 'slug' => 'general']);

        // 1. Create a raw image in public disk (unwatermarked)
        $im = imagecreatetruecolor(500, 500);
        $blue = imagecolorallocate($im, 0, 0, 255);
        imagefill($im, 0, 0, $blue);
        
        $featuredPath = 'uploads/featured.jpg';
        $inlinePath = 'uploads/inline.jpg';

        // Ensure directories exist
        Storage::disk('public')->makeDirectory('uploads');

        $featuredAbsPath = Storage::disk('public')->path($featuredPath);
        $inlineAbsPath = Storage::disk('public')->path($inlinePath);

        imagejpeg($im, $featuredAbsPath, 100);
        imagejpeg($im, $inlineAbsPath, 100);
        imagedestroy($im);

        // Record featured size before
        $originalFeaturedSize = filesize($featuredAbsPath);

        // Register a fake media record in the DB
        $mediaFeatured = Media::create([
            'filename' => 'featured.jpg',
            'path' => $featuredPath,
            'url' => '/storage/' . $featuredPath,
            'mime_type' => 'image/jpeg',
            'size' => $originalFeaturedSize,
            'user_id' => $user->id,
        ]);

        // Save article using livewire component with local URLs
        Livewire::test('admin-articles-manager')
            ->set('title', 'Article with Local Images')
            ->set('slug', 'article-with-local-images')
            ->set('category_id', $category->id)
            ->set('featured_image', '/storage/' . $featuredPath)
            ->set('body', 'Some text [image url="/storage/' . $inlinePath . '"] other text')
            ->call('save')
            ->assertHasNoErrors();

        // Verify images processed: watermarks applied
        $this->assertDatabaseHas('articles', ['slug' => 'article-with-local-images']);

        // Re-read file to verify GD can read it (valid JPEG) and it was processed
        $img1 = imagecreatefromjpeg($featuredAbsPath);
        $this->assertNotFalse($img1);
        imagedestroy($img1);

        $img2 = imagecreatefromjpeg($inlineAbsPath);
        $this->assertNotFalse($img2);
        imagedestroy($img2);

        // Verify that the media record size column was updated if size changed
        $mediaFeatured->refresh();
        $this->assertEquals(filesize($featuredAbsPath), $mediaFeatured->size);
    }

    /**
     * Test article reactions Livewire component.
     */
    public function test_article_reactions_component(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'News', 'slug' => 'news']);
        $article = Article::create([
            'title' => 'Reaction Test Article',
            'slug' => 'reaction-test-article',
            'body' => 'Article body content',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'format' => 'article',
            'published_at' => now(),
        ]);

        // 1. Initial reactions state should be all zero
        Livewire::test('article-reactions', ['article' => $article])
            ->assertSet('reactionsCount.love', 0)
            ->assertSet('reactionsCount.like', 0)
            ->assertSet('userReaction', null)
            // 2. React with 'love'
            ->call('react', 'love')
            ->assertSet('reactionsCount.love', 1)
            ->assertSet('userReaction', 'love')
            // 3. React with a different reaction: 'like'
            ->call('react', 'like')
            ->assertSet('reactionsCount.love', 0)
            ->assertSet('reactionsCount.like', 1)
            ->assertSet('userReaction', 'like')
            // 4. Click 'like' again to toggle/remove it
            ->call('react', 'like')
            ->assertSet('reactionsCount.like', 0)
            ->assertSet('userReaction', null);
    }

    /**
     * Test author dashboard rewards and creator tips rendering.
     */
    public function test_author_dashboard_rewards_and_creator_tips(): void
    {
        $user = User::factory()->create(['role' => 'author', 'email_verified_at' => now()]);
        $this->actingAs($user);

        // Configure setting rate and thresholds
        \App\Models\Setting::set('author_reward_rate', '0.25');
        \App\Models\Setting::set('earnings_enabled', '1');
        \App\Models\Setting::set('earnings_min_articles', '1');
        \App\Models\Setting::set('earnings_min_views', '100');

        $category = Category::create(['name' => 'General', 'slug' => 'general']);

        // Create article with views for the author
        Article::create([
            'title' => 'First Story Title',
            'slug' => 'first-story-title',
            'body' => 'Article body content',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'format' => 'article',
            'published_at' => now(),
            'views_count' => 100,
        ]);

        $this->get('/dashboard')
            ->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('My Personal Content Performance')
            ->assertSee('Creator Tips & Guidelines (Do\'s & Don\'ts)', false)
            ->assertSee('25.00')
            ->assertSee('First Story Title');
    }

    /**
     * Test earnings disabled and ineligibility conditions on author dashboard.
     */
    public function test_earnings_ineligibility_and_disabled(): void
    {
        $user = User::factory()->create(['role' => 'author', 'email_verified_at' => now()]);
        $this->actingAs($user);

        // Scenario 1: Earnings Enabled but Author is Ineligible
        \App\Models\Setting::set('author_reward_rate', '0.25');
        \App\Models\Setting::set('earnings_enabled', '1');
        \App\Models\Setting::set('earnings_min_articles', '5');
        \App\Models\Setting::set('earnings_min_views', '1000');

        $category = Category::create(['name' => 'General', 'slug' => 'general']);
        Article::create([
            'title' => 'Sample Post',
            'slug' => 'sample-post',
            'body' => 'Content here',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'format' => 'article',
            'published_at' => now(),
            'views_count' => 100,
        ]);

        // Should see eligibility progress panel, not 25.00 KSh
        $this->get('/admin')
            ->assertOk()
            ->assertSee('Earnings Eligibility')
            ->assertSee('You need')
            ->assertSee('articles')
            ->assertSee('total views')
            ->assertSee('to start earning')
            ->assertSee('Articles: 1 / 5')
            ->assertSee('Views: 100 / 1,000')
            ->assertDontSee('25.00');

        // Scenario 2: Earnings Disabled globally
        \App\Models\Setting::set('earnings_enabled', '0');

        $res = $this->get('/admin');
        $res->assertOk()
            ->assertSee('Earning system is currently disabled by administrator.')
            ->assertDontSee('Earnings Eligibility');
    }

    /**
     * Test menu access permissions for admin, editor, and author.
     */
    public function test_menu_access_permissions(): void
    {
        // 1. Admin should have access to /admin/menus
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $this->actingAs($admin);
        $this->get('/admin/menus')
            ->assertOk()
            ->assertSee('Navigation Menus Manager');

        // 2. Editor should have access to /admin/menus
        $editor = User::factory()->create(['role' => 'editor', 'email_verified_at' => now()]);
        $this->actingAs($editor);
        $this->get('/admin/menus')
            ->assertOk()
            ->assertSee('Navigation Menus Manager');

        // 3. Author should NOT have access to /admin/menus by default (403 or redirect)
        $author = User::factory()->create(['role' => 'author', 'email_verified_at' => now()]);
        $this->actingAs($author);
        $this->get('/admin/menus')
            ->assertStatus(403);
    }
}


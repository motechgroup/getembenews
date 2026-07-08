<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\BreakingNews;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentEngineTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test infinite category hierarchy traversal.
     */
    public function test_category_infinite_hierarchy_tree_returns_correct_depths(): void
    {
        $parent = Category::create([
            'name' => 'News',
            'slug' => 'news',
            'description' => 'Main news',
            'order' => 1
        ]);

        $child = Category::create([
            'name' => 'Politics',
            'slug' => 'politics',
            'description' => 'Political updates',
            'parent_id' => $parent->id,
            'order' => 1
        ]);

        $grandchild = Category::create([
            'name' => 'Local Elections',
            'slug' => 'local-elections',
            'description' => 'County polls',
            'parent_id' => $child->id,
            'order' => 1
        ]);

        $tree = Category::getTree();

        $this->assertCount(3, $tree);
        $this->assertEquals(0, $tree[0]->depth);
        $this->assertEquals('News', $tree[0]->name);
        
        $this->assertEquals(1, $tree[1]->depth);
        $this->assertEquals('Politics', $tree[1]->name);

        $this->assertEquals(2, $tree[2]->depth);
        $this->assertEquals('Local Elections', $tree[2]->name);
    }

    /**
     * Test advanced meta options on category pages.
     */
    public function test_category_seo_custom_meta_tags_override_default_values(): void
    {
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'order' => 1,
            'seo_title' => 'Custom Tech Title Override',
            'seo_description' => 'Custom Tech Meta Description Override'
        ]);

        $response = $this->get("/{$category->slug}");
        $response->assertStatus(200);
        $response->assertSee('Custom Tech Title Override');
        $response->assertSee('Custom Tech Meta Description Override');
    }

    /**
     * Test AI Writer assistant drafts content correctly.
     */
    public function test_ai_writer_drafts_article_and_suggests_headlines(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Ensure at least one category exists
        Category::create([
            'name' => 'General',
            'slug' => 'general',
            'order' => 1
        ]);

        $component = Livewire::test('admin-articles-manager')
            ->set('isEditing', true)
            ->set('ai_prompt', 'Digital Hubs in Getembe')
            ->set('ai_tone', 'breaking')
            ->call('generateArticleContent')
            ->assertSet('title', 'BREAKING: Digital Hubs In Getembe - Live Updates')
            ->assertSet('slug', 'breaking-digital-hubs-in-getembe-live-updates');

        $this->assertStringContainsString('strong', $component->get('body'));

        Livewire::test('admin-articles-manager')
            ->set('ai_prompt', 'Avocado Business')
            ->call('generateIdeas')
            ->assertHasNoErrors()
            ->assertSet('ai_ideas', [
                "How Avocado Business is Transforming Getembe County",
                "The Rise of Avocado Business: Opportunities and Obstacles",
                "Opinion: The Future of Avocado Business in Kisii Region",
                "A Close-Up Review: Why Avocado Business Matters Today",
                "Breaking: Local Leaders Align on New Avocado Business Directives"
            ]);
    }

    /**
     * Test breaking news management and ticker controls.
     */
    public function test_breaking_news_alerts_crud_and_status_toggles(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $this->assertDatabaseCount('breaking_news', 0);

        Livewire::test('admin-settings-manager')
            ->set('breaking_title', 'Heavy downpour expected in Kisii region')
            ->set('breaking_priority', 2)
            ->call('addBreakingNews')
            ->assertHasNoErrors()
            ->assertSet('breaking_title', '');

        $this->assertDatabaseCount('breaking_news', 1);
        $alert = BreakingNews::first();
        $this->assertEquals('Heavy downpour expected in Kisii region', $alert->title);
        $this->assertTrue($alert->is_active);

        // Test status toggle
        Livewire::test('admin-settings-manager')
            ->call('toggleBreakingNews', $alert->id);

        $alert->refresh();
        $this->assertFalse($alert->is_active);

        // Test deletion
        Livewire::test('admin-settings-manager')
            ->call('deleteBreakingNews', $alert->id);

        $this->assertDatabaseCount('breaking_news', 0);
    }
}

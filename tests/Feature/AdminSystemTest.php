<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AdminSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_git_updates_and_migrations_tab(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_sys_' . uniqid() . '@example.com']);

        $response = $this->actingAs($admin)->get('/admin/settings/updates');
        $response->assertOk();
        $response->assertSee('Git Code Updates');
        $response->assertSee('Pull Latest Git Code');
        $response->assertSee('Run Database Migrations');
    }

    public function test_non_admin_cannot_access_git_updates_tab(): void
    {
        $subscriber = User::factory()->create(['role' => 'subscriber', 'email' => 'sub_sys_' . uniqid() . '@example.com']);

        $response = $this->actingAs($subscriber)->get('/admin/settings/updates');
        $response->assertForbidden();
    }

    public function test_admin_can_check_git_status_and_run_migrations(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_git_' . uniqid() . '@example.com']);

        $component = Volt::actingAs($admin)->test('admin-settings-manager', ['tab' => 'updates']);
        $component->call('checkGitStatus');
        $component->assertSet('gitStatusSummary', function ($val) {
            return str_contains($val, 'Branch:');
        });

        $component->call('runDatabaseMigrations');
        $component->assertSet('gitTerminalOutput', function ($val) {
            return str_contains($val, 'MIGRATIONS');
        });
    }
}

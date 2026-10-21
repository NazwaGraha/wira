<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\KnowledgeSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class WiraAgentKnowledgeSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_wira_sync_knowledge_command_executes_successfully(): void
    {
        $this->artisan('wira:sync-knowledge')
            ->expectsOutputToContain('Memulai sinkronisasi seluruh konten website untuk Wira Agent')
            ->expectsOutputToContain('Pengetahuan Wira Agent berhasil disinkronkan dari seluruh konten website')
            ->assertExitCode(0);

        $path = storage_path('app/knowledge/knowledge.json');
        $this->assertTrue(File::exists($path));

        $data = json_decode(File::get($path), true);
        $this->assertIsArray($data);
        $this->assertGreaterThanOrEqual(25, count($data['topics']));
    }

    public function test_guest_cannot_trigger_wira_knowledge_sync(): void
    {
        $response = $this->post(route('admin.wira-agent.sync-knowledge'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_trigger_knowledge_sync(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test@pmrwirasmanic.or.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.wira-agent.sync-knowledge'));
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_authenticated_admin_can_trigger_knowledge_sync_via_ajax(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test@pmrwirasmanic.or.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.wira-agent.sync-knowledge'));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertGreaterThanOrEqual(25, $response->json('data.total_topics'));
    }

    public function test_admin_can_check_wira_knowledge_status(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test@pmrwirasmanic.or.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.wira-agent.knowledge-status'));
        $response->assertStatus(200);
        $response->assertJson([
            'synced' => true,
            'version' => '2.0.0',
        ]);
    }

    public function test_knowledge_search_finds_synced_website_article(): void
    {
        $searchService = app(KnowledgeSearchService::class);
        $results = $searchService->search('Panduan Penanganan Awal Cedera dan Pingsan Saat Upacara Sekolah');

        $this->assertNotEmpty($results);
        $top = $results[0];
        $this->assertStringContainsString('Pingsan', $top['content']);
    }
}

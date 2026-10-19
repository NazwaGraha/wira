<?php

namespace Tests\Feature;

use App\Services\KnowledgeSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KnowledgeSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_knowledge_search_loads_storage_json_correctly(): void
    {
        $service = app(KnowledgeSearchService::class);
        $path = $service->getStoragePath();

        $this->assertTrue(File::exists($path), "Knowledge storage JSON must exist at {$path}");

        $topics = $service->getKnowledge();
        $this->assertIsArray($topics);
        $this->assertGreaterThanOrEqual(25, count($topics));
    }

    public function test_knowledge_search_finds_pembina_and_biodata(): void
    {
        $service = app(KnowledgeSearchService::class);
        $results = $service->search('Siapa pembina PMR yang sekarang?');

        $this->assertNotEmpty($results);
        $top = $results[0];
        $this->assertEquals('biodata_pembina', $top['id']);
        $this->assertStringContainsString('Abdul Hamid Saputra', $top['content']);
    }

    public function test_knowledge_search_finds_mars_pmi_and_composer(): void
    {
        $service = app(KnowledgeSearchService::class);
        $results = $service->search('Lagu Mars PMI ciptaan Mochtar M');

        $this->assertNotEmpty($results);
        $ids = array_column($results, 'id');
        $this->assertContains('mars_pmi', $ids);
    }

    public function test_knowledge_search_finds_tandu_darurat_specifications(): void
    {
        $service = app(KnowledgeSearchService::class);
        $results = $service->search('Berapa ukuran panjang bambu tandu darurat?');

        $this->assertNotEmpty($results);
        $top = $results[0];
        $this->assertEquals('p3k_tandu_darurat', $top['id']);
        $this->assertStringContainsString('225 cm', $top['content']);
    }

    public function test_knowledge_search_finds_rjp_cpr_protocol(): void
    {
        $service = app(KnowledgeSearchService::class);
        $results = $service->search('Bagaimana teknik resusitasi jantung paru RJP CPR 30:2?');

        $this->assertNotEmpty($results);
        $top = $results[0];
        $this->assertEquals('p3k_resusitasi_jantung_paru', $top['id']);
        $this->assertStringContainsString('30 Kompresi : 2 Napas Buatan', $top['content']);
    }

    public function test_knowledge_search_finds_donor_darah_sukarela(): void
    {
        $service = app(KnowledgeSearchService::class);
        $results = $service->search('Apa saja syarat untuk donor darah sukarela?');

        $this->assertNotEmpty($results);
        $top = $results[0];
        $this->assertEquals('donor_darah_sukarela', $top['id']);
        $this->assertStringContainsString('45 kg', $top['content']);
    }

    public function test_knowledge_search_generates_formatted_markdown_context(): void
    {
        $service = app(KnowledgeSearchService::class);
        $context = $service->getRelevantContext('Bagaimana cara penanganan patah tulang pembidaian?', 3);

        $this->assertIsString($context);
        $this->assertStringContainsString('### [RAG-DOC-1]', $context);
        $this->assertStringContainsString('Pembidaian', $context);
    }

    public function test_wira_agent_chat_injects_rag_context_into_gemini_prompt(): void
    {
        Config::set('services.gemini.key', 'test-gemini-key');
        Config::set('services.gemini.model', 'gemini-1.5-flash');

        $capturedSystemInstruction = null;

        Http::fake([
            'generativelanguage.googleapis.com/*' => function ($request) use (&$capturedSystemInstruction) {
                $payload = json_decode($request->body(), true);
                $capturedSystemInstruction = $payload['systemInstruction']['parts'][0]['text'] ?? '';

                return Http::response([
                    'candidates' => [
                        [
                            'content' => [
                                'parts' => [
                                    ['text' => 'Halo Sahabat Kemanusiaan! Pembina PMR Wira SMAN 1 Ciawi adalah Bapak Abdul Hamid Saputra, S.Hum., M.Pd.'],
                                ],
                            ],
                        ],
                    ],
                ], 200);
            },
        ]);

        $response = $this->postJson('/api/wira-agent/chat', [
            'message' => 'Siapa pembina PMR yang sekarang?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'provider' => 'gemini',
        ]);

        $this->assertNotNull($capturedSystemInstruction);
        $this->assertStringContainsString('SMART KNOWLEDGE BASE WEBSITE (RAG RETRIEVAL)', $capturedSystemInstruction);
        $this->assertStringContainsString('Abdul Hamid Saputra', $capturedSystemInstruction);
    }

    public function test_wira_agent_uses_gemini_ai_for_open_domain_question_not_in_local_data(): void
    {
        Config::set('services.gemini.key', 'test-gemini-key');
        Config::set('services.gemini.model', 'gemini-1.5-flash');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Halo Sahabat Kemanusiaan! Teori relativitas umum dirumuskan oleh fisikawan Albert Einstein pada tahun 1915.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/wira-agent/chat', [
            'message' => 'Siapa yang merumuskan teori relativitas umum?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'provider' => 'gemini',
        ]);
        $this->assertStringContainsString('Albert Einstein', $response->json('reply'));
    }
}

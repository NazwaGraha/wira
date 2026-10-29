<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StorageRouteTest extends TestCase
{
    protected function tearDown(): void
    {
        $testDocDir = storage_path('app/public/competition/info-docs');
        if (File::exists($testDocDir.'/test-document.pdf')) {
            File::delete($testDocDir.'/test-document.pdf');
        }

        parent::tearDown();
    }

    public function test_can_access_competition_info_docs_via_storage_route(): void
    {
        $testDocDir = storage_path('app/public/competition/info-docs');
        File::ensureDirectoryExists($testDocDir, 0755, true);

        // Buat mock file PDF valid sederhana
        $pdfContent = "%PDF-1.4\n%âãÏÓ\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000015 00000 n\n0000000060 00000 n\n0000000111 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n180\n%%EOF";
        File::put($testDocDir.'/test-document.pdf', $pdfContent);

        $response = $this->get('/storage/competition/info-docs/test-document.pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline; filename="test-document.pdf"', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_blocked_folder_returns_404(): void
    {
        $response = $this->get('/storage/secret-folder/unauthorized.txt');
        $response->assertStatus(404);
    }

    public function test_path_traversal_returns_404(): void
    {
        $response = $this->get('/storage/../.env');
        $response->assertStatus(404);
    }
}

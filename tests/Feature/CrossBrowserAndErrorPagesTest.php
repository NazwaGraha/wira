<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CrossBrowserAndErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public pages respond with 200 OK and no 500 errors.
     */
    public function test_public_pages_load_successfully(): void
    {
        $routes = [
            '/',
            '/lomba',
            '/lomba/status',
            '/lomba/live-scoreboard',
            '/artikel',
            '/kegiatan',
            '/prestasi',
            '/galeri',
            '/donor-darah',
            '/pedoman-organisasi',
            '/tentang-kami',
            '/kontak',
            '/admin/login',
            '/juri/login',
            '/parkir/login',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertEquals(200, $response->getStatusCode(), "Route {$route} returned {$response->getStatusCode()} instead of 200");
        }

        // /lomba/daftar cleanly redirects to /lomba when no active event is open
        $regResponse = $this->get('/lomba/daftar');
        $this->assertTrue(in_array($regResponse->getStatusCode(), [200, 302]));
    }

    /**
     * Test unauthenticated access to protected areas redirects to login rather than 500.
     */
    public function test_unauthenticated_protected_routes_redirect_safely(): void
    {
        $adminResponse = $this->get('/admin/dashboard');
        $adminResponse->assertRedirect('/admin/login');

        $juryResponse = $this->get('/juri/dashboard');
        $juryResponse->assertRedirect('/juri/login');

        $parkingResponse = $this->get('/parkir');
        $parkingResponse->assertRedirect('/parkir/login');
    }

    /**
     * Test non-existent route returns 404 with custom error page.
     */
    public function test_non_existent_page_renders_custom_404(): void
    {
        $response = $this->get('/halaman-yang-tidak-pernah-ada-pmr-12345');
        $response->assertStatus(404);
        $response->assertSee('404 NOT FOUND', false);
        $response->assertSee('Halaman Tidak Ditemukan', false);
        $response->assertSee('PMR WIRA SMAN 1 CIAWI', false);
    }

    /**
     * Test custom error views render without blade errors.
     */
    public function test_error_views_render_cleanly(): void
    {
        $view403 = view('errors.403', ['exception' => new HttpException(403, 'Akses Ditolak')])->render();
        $this->assertStringContainsString('403 FORBIDDEN', $view403);
        $this->assertStringContainsString('PMR WIRA SMAN 1 CIAWI', $view403);

        $view404 = view('errors.404')->render();
        $this->assertStringContainsString('404 NOT FOUND', $view404);

        $view419 = view('errors.419')->render();
        $this->assertStringContainsString('419 PAGE EXPIRED', $view419);

        $view500 = view('errors.500')->render();
        $this->assertStringContainsString('500 INTERNAL SERVER ERROR', $view500);

        $view503 = view('errors.503')->render();
        $this->assertStringContainsString('503 SERVICE UNAVAILABLE', $view503);
    }

    /**
     * Test camera scanning and check-in pages render without errors.
     */
    public function test_camera_views_render_cleanly(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $checkinResponse = $this->get('/admin/competition-checkin');
        $checkinResponse->assertStatus(200);
        $checkinResponse->assertSee('webcam-video', false);
        $checkinResponse->assertSee('playsinline', false);

        $parkingScannerResponse = $this->get('/parkir');
        $parkingScannerResponse->assertStatus(200);
        $parkingScannerResponse->assertSee('qr-reader', false);
        $parkingScannerResponse->assertSee('playsinline', false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_return_successful_response(): void
    {
        $category = Category::create([
            'name' => 'Pertolongan Pertama',
            'slug' => 'pertolongan-pertama',
        ]);

        Article::create([
            'title' => 'Panduan P3K Upacara',
            'slug' => 'panduan-p3k-upacara',
            'category_id' => $category->id,
            'excerpt' => 'Langkah penanganan pingsan...',
            'body' => 'Isi materi p3k lengkap...',
            'status' => 'published',
            'is_featured' => true,
        ]);

        // 1. Home
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('RAGANA DWI PANTARA');

        // 2. Tentang Kami
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
        $response->assertSee('Tentang Kami');

        // 3. Kegiatan
        $response = $this->get('/kegiatan');
        $response->assertStatus(200);

        // 4. Galeri
        $response = $this->get('/galeri');
        $response->assertStatus(200);

        // 5. Donor Darah
        $response = $this->get('/donor-darah');
        $response->assertStatus(200);

        // 6. Kontak
        $response = $this->get('/kontak');
        $response->assertStatus(200);

        // 7. Artikel Index
        $response = $this->get('/artikel');
        $response->assertStatus(200);

        // 8. Single Artikel
        $response = $this->get('/artikel/panduan-p3k-upacara');
        $response->assertStatus(200);
        $response->assertSee('Panduan P3K Upacara');

        // 9. Pedoman Organisasi (GBHO & PPUO)
        $response = $this->get('/pedoman-organisasi');
        $response->assertStatus(200);
        $response->assertSee('GBHO');
        $response->assertSee('PPUO');

        // 10. Legacy /tentang-kami/pedoman redirect
        $response = $this->get('/tentang-kami/pedoman');
        $response->assertRedirect(route('pedoman-organisasi'));

        // 11. Prestasi & Rekam Jejak Juara
        $response = $this->get('/prestasi');
        $response->assertStatus(200);
        $response->assertSee('Jejak Prestasi');

        // 12. Admin Login Page
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_admin_backoffice_authenticated_access(): void
    {
        $user = User::factory()->create();

        // Access dashboard
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Dashboard CMS');

        // Access admin prestasi
        $response = $this->actingAs($user)->get('/admin/prestasi');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Prestasi');

        // Access articles list
        $response = $this->actingAs($user)->get('/admin/artikel');
        $response->assertStatus(200);

        // Access create article
        $response = $this->actingAs($user)->get('/admin/artikel/create');
        $response->assertStatus(200);

        // Access hero slides list
        $response = $this->actingAs($user)->get('/admin/hero-slides');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Slider Hero Banner');

        // Access create hero slide
        $response = $this->actingAs($user)->get('/admin/hero-slides/create');
        $response->assertStatus(200);

        // Access organization structure backoffice
        $response = $this->actingAs($user)->get('/admin/bagan-kepengurusan');
        $response->assertStatus(200);
        $response->assertSee('Kelola Bagan Kepengurusan');
        $response->assertSee('Pengaturan Judul');
    }

    public function test_admin_can_update_organization_settings_and_manage_members(): void
    {
        $user = User::factory()->create();

        // 1. Update Title and Subtitle
        $response = $this->actingAs($user)->put('/admin/bagan-kepengurusan/setting', [
            'badge' => 'Bagan Kepengurusan Baru',
            'title' => 'Struktur Organisasi 2027/2028',
            'subtitle' => 'Masa Bakti Wiratama 2027/2028',
        ]);
        $response->assertRedirect('/admin/bagan-kepengurusan');
        $this->assertDatabaseHas('organization_settings', [
            'title' => 'Struktur Organisasi 2027/2028',
        ]);

        // 2. Add Member
        $response = $this->actingAs($user)->post('/admin/bagan-kepengurusan', [
            'position' => 'Ketua Umum 2027/2028',
            'name' => 'Aditya Nugraha',
            'subtitle' => 'Kelas XI-MIPA 2',
            'level' => 2,
            'order_position' => 1,
            'is_active' => 1,
        ]);
        $response->assertRedirect('/admin/bagan-kepengurusan');
        $this->assertDatabaseHas('organization_members', [
            'name' => 'Aditya Nugraha',
            'position' => 'Ketua Umum 2027/2028',
        ]);

        // 3. Verify public page displays new title and member
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
        $response->assertSee('Struktur Organisasi 2027/2028');
        $response->assertSee('Aditya Nugraha');
    }

    public function test_admin_can_create_activity_and_display_on_kegiatan_page(): void
    {
        $user = User::factory()->create();

        // 1. Access admin activities list
        $response = $this->actingAs($user)->get('/admin/kegiatan');
        $response->assertStatus(200);
        $response->assertSee('Kelola Agenda');

        // 2. Access admin activities create form
        $response = $this->actingAs($user)->get('/admin/kegiatan/create');
        $response->assertStatus(200);
        $response->assertSee('Formulir Tambah Kegiatan');

        // 3. Post new activity into database
        $response = $this->actingAs($user)->post('/admin/kegiatan', [
            'title' => 'Simulasi Mitigasi Kebakaran dan Evakuasi Cepat',
            'category' => 'Kesiapsiagaan Bencana',
            'event_date' => '2026-10-15',
            'location' => 'Lapangan Utama SMAN 1 Ciawi',
            'description' => 'Latihan gabungan simulasi pemadaman api ringan dan rute evakuasi darurat.',
            'is_featured' => 1,
        ]);

        $response->assertRedirect('/admin/kegiatan');
        $this->assertDatabaseHas('activities', [
            'title' => 'Simulasi Mitigasi Kebakaran dan Evakuasi Cepat',
            'slug' => 'simulasi-mitigasi-kebakaran-dan-evakuasi-cepat',
            'category' => 'Kesiapsiagaan Bencana',
            'location' => 'Lapangan Utama SMAN 1 Ciawi',
        ]);

        // 4. Verify activity appears on public /kegiatan page
        $response = $this->get('/kegiatan');
        $response->assertStatus(200);
        $response->assertSee('Simulasi Mitigasi Kebakaran dan Evakuasi Cepat');
        $response->assertSee('Lapangan Utama SMAN 1 Ciawi');

        // 5. Access Edit form and check Rich Text Editor
        $activity = Activity::where('title', 'Simulasi Mitigasi Kebakaran dan Evakuasi Cepat')->first();
        $response = $this->actingAs($user)->get("/admin/kegiatan/{$activity->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('quill-editor');
        $response->assertSee('quill.snow.css');

        // 6. Update activity
        $response = $this->actingAs($user)->put("/admin/kegiatan/{$activity->id}", [
            'title' => 'Simulasi Mitigasi Kebakaran Revisi 2026',
            'category' => 'Kesiapsiagaan Bencana',
            'event_date' => '2026-10-20',
            'location' => 'Gedung Serbaguna',
            'description' => '<p><strong>Rincian terbaru</strong> pelatihan dan simulasi pemadaman api.</p>',
        ]);
        $response->assertRedirect('/admin/kegiatan');
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Simulasi Mitigasi Kebakaran Revisi 2026',
            'location' => 'Gedung Serbaguna',
        ]);

        // 7. Delete activity
        $response = $this->actingAs($user)->delete("/admin/kegiatan/{$activity->id}");
        $response->assertRedirect('/admin/kegiatan');
        $this->assertDatabaseMissing('activities', [
            'id' => $activity->id,
        ]);
    }

    public function test_admin_can_manage_organization_guidelines(): void
    {
        $user = User::factory()->create();

        // 1. Access admin pedoman organisasi
        $response = $this->actingAs($user)->get('/admin/pedoman-organisasi');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pedoman Organisasi (GBHO & PPUO)');
        $response->assertSee('Arah Kebijakan Umum');

        // 2. Update GBHO
        $response = $this->actingAs($user)->put('/admin/pedoman-organisasi/gbho', [
            'period' => '2027/2028',
            'established_date' => '10 Januari 2027',
            'established_place' => 'Ciawi, Bogor',
            'gbho_policy_heading' => 'Arah Kebijakan Terpilih',
            'gbho_policy_text' => 'Mewujudkan PMR Wira SMAN 1 Ciawi periode baru yang unggul dan berprestasi.',
        ]);
        $response->assertRedirect('/admin/pedoman-organisasi?tab=gbho');
        $this->assertDatabaseHas('organization_guidelines', [
            'period' => '2027/2028',
            'gbho_policy_heading' => 'Arah Kebijakan Terpilih',
        ]);

        // 3. Add a new GBHO strategy
        $response = $this->actingAs($user)->post('/admin/pedoman-organisasi/strategy', [
            'title' => 'Digitalisasi Layanan Medis Sekolah',
            'description' => 'Mengembangkan pencatatan inventaris obat dan penanganan siswa berbasis web.',
            'icon' => 'fa-solid fa-laptop-medical',
            'order_number' => 7,
        ]);
        $response->assertRedirect('/admin/pedoman-organisasi?tab=gbho');
        $this->assertDatabaseHas('gbho_strategies', [
            'title' => 'Digitalisasi Layanan Medis Sekolah',
            'order_number' => 7,
        ]);

        // 4. Add a new PPUO chapter
        $response = $this->actingAs($user)->post('/admin/pedoman-organisasi/chapter', [
            'chapter_number' => 'BAB XII',
            'title' => 'Kode Kehormatan dan Aturan Tambahan',
            'summary' => 'Ketentuan tata krama khusus relawan di lingkungan luar sekolah.',
            'badge' => 'Kode Etik Khusus',
            'order_number' => 12,
        ]);
        $response->assertRedirect('/admin/pedoman-organisasi?tab=ppuo');
        $this->assertDatabaseHas('ppuo_chapters', [
            'chapter_number' => 'BAB XII',
            'title' => 'Kode Kehormatan dan Aturan Tambahan',
        ]);

        // 5. Verify public pages reflect the updated period and new strategy
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
        $response->assertSee('2027/2028');
        $response->assertSee('Digitalisasi Layanan Medis Sekolah');

        $response = $this->get('/pedoman-organisasi');
        $response->assertStatus(200);
        $response->assertSee('2027/2028');
        $response->assertSee('Digitalisasi Layanan Medis Sekolah');
    }
}

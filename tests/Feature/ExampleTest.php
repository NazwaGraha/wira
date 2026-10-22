<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Category;
use App\Models\ContentSectionImage;
use App\Models\HeroSlide;
use App\Models\PopupBanner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        // Access create prestasi
        $response = $this->actingAs($user)->get('/admin/prestasi/create');
        $response->assertStatus(200);
        $response->assertSee('Tambah Prestasi Baru');

        $achievement = Achievement::create([
            'title' => 'Juara 1 Tingkat Nasional',
            'event_name' => 'Jabar Open',
            'event_date' => now(),
            'level' => 'Nasional',
            'rank' => 'Juara 1',
        ]);

        // Access edit prestasi
        $response = $this->actingAs($user)->get("/admin/prestasi/{$achievement->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Data Prestasi');

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

    public function test_popup_banner_feature_flow(): void
    {
        $admin = User::factory()->create();

        // 1. Admin visits popup banner management
        $response = $this->actingAs($admin)->get('/admin/popup-banner');
        $response->assertStatus(200);
        $response->assertSee('Popup Banner Iklan & Pengumuman');

        // 2. Admin creates a popup banner
        $banner = PopupBanner::create([
            'title' => 'Open Recruitment Relawan PMR 2026',
            'image_path' => '/mockups/mockup_popup_banner_publik.png',
            'target_url' => 'https://pmrwirasmanic.or.id/kontak',
            'button_text' => 'Daftar Sekarang',
            'open_in_new_tab' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('popup_banners', [
            'title' => 'Open Recruitment Relawan PMR 2026',
            'is_active' => true,
        ]);

        // 3. Public visitor views home and sees popup banner
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Open Recruitment Relawan PMR 2026');
        $response->assertSee('home-popup-banner-modal');
        $response->assertSee('Daftar Sekarang');
        $response->assertSeeText('Lewati & Tutup');

        // 4. Public impression tracking
        $response = $this->postJson("/popup-banner/{$banner->id}/impression");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(1, $banner->fresh()->impressions_count);

        // 5. Public click tracking and redirect
        $response = $this->get("/popup-banner/{$banner->id}/click");
        $response->assertRedirect('https://pmrwirasmanic.or.id/kontak');
        $this->assertEquals(1, $banner->fresh()->clicks_count);

        // 6. Admin toggles banner
        $response = $this->actingAs($admin)->post("/admin/popup-banner/{$banner->id}/toggle");
        $response->assertSessionHas('success');
        $this->assertFalse($banner->fresh()->is_active);
    }

    public function test_background_music_admin_and_public_floating_player(): void
    {
        $admin = User::factory()->create();

        // 1. Admin accesses background music page
        $response = $this->actingAs($admin)->get('/admin/background-music');
        $response->assertStatus(200);
        $response->assertSee('Sound Mars PMI');
        $response->assertSee('Mars Palang Merah Indonesia');

        // 2. Admin updates background music settings
        $response = $this->actingAs($admin)->put('/admin/background-music', [
            'title' => 'Mars Palang Merah Indonesia (Aransemen Resmi)',
            'subtitle' => 'Karya Mochtar H. S. - Korps Sukarela & PMR Wira',
            'default_volume' => 40,
            'is_active' => '1',
            'autoplay_on_first_interaction' => '1',
            'loop' => '1',
            'lyrics' => "Palang Merah Indonesia\nSumber kasih umat manusia...",
        ]);
        $response->assertRedirect('/admin/background-music');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('background_musics', [
            'title' => 'Mars Palang Merah Indonesia (Aransemen Resmi)',
            'default_volume' => 40,
            'is_active' => true,
        ]);

        // 3. Public visitor visits home and sees the floating Mars PMI player widget
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('mars-pmi-floating-widget');
        // 4. Admin resets audio to default
        $response = $this->actingAs($admin)->post('/admin/background-music/reset');
        $response->assertRedirect('/admin/background-music');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('background_musics', [
            'title' => 'Mars Palang Merah Indonesia',
            'audio_path' => null,
        ]);

        // 5. Test streaming route /mars-pmi-audio
        $response = $this->get('/mars-pmi-audio');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'audio/mpeg');
    }

    public function test_admin_can_view_edit_hero_slide_with_upload_guidelines(): void
    {
        $user = User::factory()->create();

        $slide = HeroSlide::create([
            'title' => 'Apel Akbar PMR Wira 2026',
            'image_path' => '/mockups/01_home.jpg',
            'caption' => 'Dokumentasi apel akbar',
            'order_position' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get("/admin/hero-slides/{$slide->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Panduan Format & Ukuran Gambar Banner Hero', false);
        $response->assertSee('1920 × 800 px', false);
        $response->assertSee('JPG, JPEG, PNG, WEBP', false);
        $response->assertSee('Maks. 5 MB', false);
        $response->assertSee('name="image"', false);
        $response->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_admin_can_upload_and_update_hero_slide_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $slide = HeroSlide::create([
            'title' => 'Slide Lama',
            'image_path' => '/mockups/01_home.jpg',
            'caption' => 'Caption Lama',
            'order_position' => 1,
            'is_active' => true,
        ]);

        $fakeImage = UploadedFile::fake()->image('new-banner.jpg', 1920, 800);

        $response = $this->actingAs($user)->put("/admin/hero-slides/{$slide->id}", [
            'title' => 'Slide Baru Apel Siaga',
            'image' => $fakeImage,
            'caption' => 'Caption Baru Berhasil Diupload',
            'order_position' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/hero-slides');
        $response->assertSessionHas('success');

        $slide->refresh();
        $this->assertEquals('Slide Baru Apel Siaga', $slide->title);
        $this->assertStringStartsWith('hero-slides/', $slide->image_path);
        Storage::disk('public')->assertExists($slide->image_path);
    }

    public function test_admin_can_upload_and_create_hero_slide_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $fakeImage = UploadedFile::fake()->image('create-banner.png', 1920, 800);

        $response = $this->actingAs($user)->post('/admin/hero-slides', [
            'title' => 'Slide Baru Dari Form Create',
            'image' => $fakeImage,
            'caption' => 'Caption create slide',
            'order_position' => 2,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/hero-slides');
        $response->assertSessionHas('success');

        $created = HeroSlide::where('title', 'Slide Baru Dari Form Create')->first();
        $this->assertNotNull($created);
        $this->assertStringStartsWith('hero-slides/', $created->image_path);
        Storage::disk('public')->assertExists($created->image_path);
    }

    public function test_admin_can_access_unified_media_manager(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/media-manager');
        $response->assertStatus(200);
        $response->assertSee('Pusat Pengaturan Gambar & Banner Website', false);
        $response->assertSee('Mode Multi-Gambar', false);
        $response->assertSee('Mode Gambar Tunggal', false);
        $response->assertSee('Profil Ekstrakurikuler', false);
        $response->assertSee('Mengenal PMR', false);
    }

    public function test_admin_can_upload_and_update_home_profile_single_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $fakeImage = UploadedFile::fake()->image('profil-relawan.jpg', 1200, 900);

        $response = $this->actingAs($user)->post('/admin/media-manager/section', [
            'section_key' => 'home_profile',
            'image' => $fakeImage,
            'badge_text' => 'Terakreditasi A Unggul',
            'badge_subtext' => 'Unit Teladan Tingkat Provinsi',
        ]);

        $response->assertRedirect('/admin/media-manager?tab=home_profile');
        $response->assertSessionHas('success');

        $section = ContentSectionImage::where('section_key', 'home_profile')->first();
        $this->assertNotNull($section);
        $this->assertEquals('Terakreditasi A Unggul', $section->badge_text);
        $this->assertStringStartsWith('content-images/', $section->image_path);
        Storage::disk('public')->assertExists($section->image_path);

        // Verify public home page reflects the updated image and badge
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee($section->image_url);
        $homeResponse->assertSee('Terakreditasi A Unggul');
    }

    public function test_admin_can_upload_and_update_about_intro_single_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $fakeImage = UploadedFile::fake()->image('mengenal-pmr.jpg', 1200, 900);

        $response = $this->actingAs($user)->post('/admin/media-manager/section', [
            'section_key' => 'about_intro',
            'image' => $fakeImage,
            'badge_text' => 'PMR Wira SMAN 1 Ciawi',
            'badge_subtext' => 'Garda Terdepan Kemanusiaan',
        ]);

        $response->assertRedirect('/admin/media-manager?tab=about_intro');
        $response->assertSessionHas('success');

        $section = ContentSectionImage::where('section_key', 'about_intro')->first();
        $this->assertNotNull($section);
        $this->assertEquals('PMR Wira SMAN 1 Ciawi', $section->badge_text);
        $this->assertStringStartsWith('content-images/', $section->image_path);
        Storage::disk('public')->assertExists($section->image_path);

        // Verify public tentang kami page reflects the updated image and badge
        $aboutResponse = $this->get('/tentang-kami');
        $aboutResponse->assertStatus(200);
        $aboutResponse->assertSee($section->image_url);
        $aboutResponse->assertSee('PMR Wira SMAN 1 Ciawi');
    }

    public function test_admin_can_reset_single_image_section_to_default(): void
    {
        $user = User::factory()->create();

        $section = ContentSectionImage::getSection('home_profile');
        $section->update(['image_path' => 'content-images/custom.jpg']);

        $response = $this->actingAs($user)->post('/admin/media-manager/section/reset', [
            'section_key' => 'home_profile',
        ]);

        $response->assertRedirect('/admin/media-manager?tab=home_profile');
        $response->assertSessionHas('success');

        $section->refresh();
        $this->assertEquals('/mockups/02_tentang_kami.jpg', $section->image_path);
    }
}

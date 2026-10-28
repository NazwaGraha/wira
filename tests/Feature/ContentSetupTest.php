<?php

namespace Tests\Feature;

use App\Models\ContentSectionImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContentSetupTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Content Test',
            'username' => 'admin_content_tester',
            'email' => 'admin_content_tester@pmrwirasmanic.or.id',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_guest_is_redirected_from_content_setup(): void
    {
        $response = $this->get(route('admin.content-setup.home'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_home_content_setup_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.content-setup.home'));

        $response->assertStatus(200);
        $response->assertSee('Setup Konten Halaman Beranda');
        $response->assertDontSee('Manajemen Artikel');
        $response->assertSee('Profil Ekstrakurikuler');
        $response->assertSee('Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi');
        $response->assertSee('Pratinjau Langsung (Live Preview)');
    }

    public function test_admin_can_update_home_profile_content(): void
    {
        $admin = $this->createAdmin();
        $fakeImage = UploadedFile::fake()->image('profil_baru.jpg', 600, 450);

        $response = $this->actingAs($admin)->post(route('admin.content-setup.home.profile.update'), [
            'tagline' => 'Profil Baru PMR',
            'tagline_icon' => 'fa-solid fa-heart-pulse',
            'headline' => 'Mencetak Relawan Tangguh dan Berakhlak Mulia',
            'body_text' => 'Deskripsi profil terbaru PMR Wira SMAN 1 Ciawi untuk uji coba pembaruan konten.',
            'checklist_items' => [
                'Keterampilan P3K berstandar nasional.',
                'Aksi donor darah berkala.',
                'Jiwa kepemimpinan dan kemanusiaan.',
            ],
            'button_text' => 'Pelajari Profil Kami',
            'button_url' => '/tentang-kami',
            'badge_text' => 'Juara 1 Teladan',
            'badge_subtext' => 'Tingkat Provinsi Jawa Barat',
            'image' => $fakeImage,
        ]);

        $response->assertRedirect(route('admin.content-setup.home', ['tab' => 'profile']));
        $response->assertSessionHas('success');

        $profile = ContentSectionImage::getSection('home_profile');
        $this->assertEquals('Profil Baru PMR', $profile->tagline);
        $this->assertEquals('fa-solid fa-heart-pulse', $profile->tagline_icon);
        $this->assertEquals('Mencetak Relawan Tangguh dan Berakhlak Mulia', $profile->headline);
        $this->assertEquals('Deskripsi profil terbaru PMR Wira SMAN 1 Ciawi untuk uji coba pembaruan konten.', $profile->body_text);
        $this->assertEquals('Juara 1 Teladan', $profile->badge_text);
        $this->assertEquals('Tingkat Provinsi Jawa Barat', $profile->badge_subtext);
        $this->assertCount(3, $profile->display_checklist_items);
        $this->assertEquals('Keterampilan P3K berstandar nasional.', $profile->display_checklist_items[0]);
    }

    public function test_admin_can_reset_home_profile_content_to_default(): void
    {
        $admin = $this->createAdmin();
        $profile = ContentSectionImage::getSection('home_profile');
        $profile->update([
            'headline' => 'Judul Sementara Yang Diubah',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.content-setup.home.profile.reset'));

        $response->assertRedirect(route('admin.content-setup.home', ['tab' => 'profile']));
        $response->assertSessionHas('success');

        $profile->refresh();
        $this->assertEquals('Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi', $profile->headline);
        $this->assertEquals('Profil Ekstrakurikuler', $profile->tagline);
    }

    public function test_homepage_renders_profile_content_faithfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Profil Ekstrakurikuler');
        $response->assertSee('Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi');
        $response->assertSee('Terakreditasi A');
        $response->assertSee('Unit PMR Wira Teladan Kab. Bogor');
        $response->assertSee('Baca Profil Selengkapnya');
        $response->assertSee('Pilar Aksi Kemanusiaan');
        $response->assertSee('Program Unggulan PMR Wira');
        $response->assertSee('Donor Darah Sukarela');
        $response->assertSee('Pertolongan Pertama (PP)');
        $response->assertSee('Siaga Bencana & Sosial');
    }

    public function test_admin_can_access_home_pillars_setup_tab(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.content-setup.home', ['tab' => 'pillars']));

        $response->assertStatus(200);
        $response->assertSee('Pilar Aksi Kemanusiaan');
        $response->assertSee('Program Unggulan PMR Wira');
        $response->assertSee('Donor Darah Sukarela');
        $response->assertSee('Pertolongan Pertama (PP)');
        $response->assertSee('Siaga Bencana & Sosial');
    }

    public function test_admin_can_update_home_pillars_content(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.content-setup.home.pillars.update'), [
            'tagline' => '3 Pilar Utama Relawan',
            'headline' => 'Program Aksi Kemanusiaan Unggulan',
            'body_text' => 'Deskripsi layanan baru untuk uji coba pembaruan konten pilar.',
            'cards' => [
                [
                    'icon' => 'fa-solid fa-droplet',
                    'title' => 'Donor Darah Rutin',
                    'description' => 'Aksi donor darah berkala.',
                    'button_text' => 'Lihat Jadwal',
                    'button_url' => '/donor-darah',
                ],
                [
                    'icon' => 'fa-solid fa-briefcase-medical',
                    'title' => 'Pelayanan P3K Sekolah',
                    'description' => 'Siaga P3K di setiap event sekolah.',
                    'button_text' => 'Info Layanan',
                    'button_url' => '/kegiatan',
                ],
                [
                    'icon' => 'fa-solid fa-people-roof',
                    'title' => 'Tanggap Darurat dan Mitigasi',
                    'description' => 'Edukasi kebencanaan dan mitigasi gempa.',
                    'button_text' => 'Baca Mitigasi',
                    'button_url' => '/artikel',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.content-setup.home', ['tab' => 'pillars']));
        $response->assertSessionHas('success');

        $pillars = ContentSectionImage::getSection('home_pillars');
        $this->assertEquals('3 Pilar Utama Relawan', $pillars->tagline);
        $this->assertEquals('Program Aksi Kemanusiaan Unggulan', $pillars->headline);
        $this->assertEquals('Deskripsi layanan baru untuk uji coba pembaruan konten pilar.', $pillars->body_text);
        $this->assertCount(3, $pillars->display_programs);
        $this->assertEquals('Donor Darah Rutin', $pillars->display_programs[0]['title']);
    }

    public function test_admin_can_reset_home_pillars_content_to_default(): void
    {
        $admin = $this->createAdmin();
        $pillars = ContentSectionImage::getSection('home_pillars');
        $pillars->update([
            'headline' => 'Judul Pilar Diubah',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.content-setup.home.pillars.reset'));

        $response->assertRedirect(route('admin.content-setup.home', ['tab' => 'pillars']));
        $response->assertSessionHas('success');

        $pillars->refresh();
        $this->assertEquals('Program Unggulan PMR Wira', $pillars->headline);
        $this->assertEquals('Pilar Aksi Kemanusiaan', $pillars->tagline);
    }

    public function test_admin_can_access_about_content_setup_pages(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.content-setup.about', ['tab' => 'intro']));
        $response->assertStatus(200);
        $response->assertSee('Setup Konten Halaman Tentang Kami');
        $response->assertDontSee('Manajemen Artikel');
        $response->assertSee('Setup Konten Tentang Kami');
        $response->assertSee('MENGENAL PMR');

        $responseTb = $this->actingAs($admin)->get(route('admin.content-setup.about', ['tab' => 'tri_bakti']));
        $responseTb->assertStatus(200);
        $responseTb->assertSee('Tri Bakti Palang Merah Remaja');

        $responsePr = $this->actingAs($admin)->get(route('admin.content-setup.about', ['tab' => 'principles']));
        $responsePr->assertStatus(200);
        $responsePr->assertSee('7 Dasar Prinsip Palang Merah');

        $responseVm = $this->actingAs($admin)->get(route('admin.content-setup.about', ['tab' => 'vision_mission']));
        $responseVm->assertStatus(200);
        $responseVm->assertSee('Visi & Misi Kami');
    }

    public function test_admin_can_update_and_reset_about_intro_content(): void
    {
        $admin = $this->createAdmin();
        $fakeImage = UploadedFile::fake()->image('about_test.jpg', 600, 800);

        $response = $this->actingAs($admin)->post(route('admin.content-setup.about.intro.update'), [
            'tagline' => 'PROFIL RESMI WIRA',
            'tagline_icon' => 'fa-solid fa-star',
            'headline' => 'Profil Singkat Relawan SMAN 1 Ciawi',
            'quote_text' => 'Kutipan baru untuk tes.',
            'body_text' => 'Paragraf satu pengenalan baru.',
            'body_text_secondary' => 'Paragraf dua kepengurusan baru.',
            'badge_text' => 'PMR Terfavorit',
            'badge_subtext' => 'Binaan Jawa Barat',
            'badge_icon' => 'fa-solid fa-award',
            'image' => $fakeImage,
        ]);

        $response->assertRedirect(route('admin.content-setup.about', ['tab' => 'intro']));
        $response->assertSessionHas('success');

        $intro = ContentSectionImage::getSection('about_intro');
        $this->assertEquals('PROFIL RESMI WIRA', $intro->tagline);
        $this->assertEquals('Profil Singkat Relawan SMAN 1 Ciawi', $intro->headline);

        // Test Reset
        $resetResponse = $this->actingAs($admin)->post(route('admin.content-setup.about.intro.reset'));
        $resetResponse->assertRedirect(route('admin.content-setup.about', ['tab' => 'intro']));
        $intro->refresh();
        $this->assertEquals('MENGENAL PMR', $intro->tagline);
    }

    public function test_admin_can_update_and_reset_about_tri_bakti(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.content-setup.about.tri-bakti.update'), [
            'tagline' => 'Tri Bakti Utama',
            'tagline_icon' => 'fa-solid fa-hands-holding',
            'headline' => 'Tri Bakti Relawan Muda',
            'body_text' => 'Tiga kewajiban suci relawan.',
            'cards' => [
                [
                    'sub_badge' => 'Pilar 1',
                    'icon' => 'fa-solid fa-hand-holding-heart',
                    'title' => 'Bakti Warga',
                    'description' => 'Aksi bakti sosial peduli sesama.',
                ],
                [
                    'sub_badge' => 'Pilar 2',
                    'icon' => 'fa-solid fa-kit-medical',
                    'title' => 'Kesehatan Sekolah',
                    'description' => 'Piket medis UKS.',
                ],
                [
                    'sub_badge' => 'Pilar 3',
                    'icon' => 'fa-solid fa-earth-asia',
                    'title' => 'Persahabatan Global',
                    'description' => 'Menjalin persahabatan tanpa batas.',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.content-setup.about', ['tab' => 'tri_bakti']));
        $response->assertSessionHas('success');

        $tb = ContentSectionImage::getSection('about_tri_bakti');
        $this->assertEquals('Tri Bakti Utama', $tb->tagline);
        $this->assertEquals('Bakti Warga', $tb->display_tri_bakti[0]['title']);

        // Reset
        $this->actingAs($admin)->post(route('admin.content-setup.about.tri-bakti.reset'));
        $tb->refresh();
        $this->assertEquals('Tri Bakti PMR', $tb->tagline);
    }

    public function test_admin_can_update_and_reset_about_principles(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.content-setup.about.principles.update'), [
            'tagline' => '7 Prinsip Pokok',
            'headline' => 'Prinsip Dasar Palang Merah Internasional',
            'body_text' => 'Tujuh prinsip yang dianut seluruh dunia.',
            'cards' => [
                ['icon' => 'fa-solid fa-heart', 'title' => 'KEMANUSIAAN', 'subtitle' => 'Humanity'],
                ['icon' => 'fa-solid fa-scale-balanced', 'title' => 'KESAMAAN', 'subtitle' => 'Impartiality'],
                ['icon' => 'fa-solid fa-dove', 'title' => 'KENETRALAN', 'subtitle' => 'Neutrality'],
                ['icon' => 'fa-solid fa-flag', 'title' => 'KEMANDIRIAN', 'subtitle' => 'Independence'],
                ['icon' => 'fa-solid fa-hand-holding-medical', 'title' => 'KESUKARELAAN', 'subtitle' => 'Voluntary Service'],
                ['icon' => 'fa-solid fa-users', 'title' => 'KESATUAN', 'subtitle' => 'Unity'],
                ['icon' => 'fa-solid fa-globe', 'title' => 'KESEMESTAAN', 'subtitle' => 'Universality'],
            ],
        ]);

        $response->assertRedirect(route('admin.content-setup.about', ['tab' => 'principles']));
        $response->assertSessionHas('success');

        $principles = ContentSectionImage::getSection('about_principles');
        $this->assertEquals('7 Prinsip Pokok', $principles->tagline);

        // Reset
        $this->actingAs($admin)->post(route('admin.content-setup.about.principles.reset'));
        $principles->refresh();
        $this->assertEquals('Landasan Moral Universal', $principles->tagline);
    }

    public function test_admin_can_update_and_reset_about_vision_mission(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.content-setup.about.vision-mission.update'), [
            'tagline' => 'Visi Misi Wira',
            'headline' => 'Arah Gerak dan Harapan Kami',
            'vision_text' => 'Menjadi unit relawan PMR terdepan di Kabupaten Bogor.',
            'missions' => [
                'Misi satu pembaruan.',
                'Misi dua pembaruan.',
                'Misi tiga pembaruan.',
                'Misi empat pembaruan.',
            ],
        ]);

        $response->assertRedirect(route('admin.content-setup.about', ['tab' => 'vision_mission']));
        $response->assertSessionHas('success');

        $vm = ContentSectionImage::getSection('about_vision_mission');
        $this->assertEquals('Menjadi unit relawan PMR terdepan di Kabupaten Bogor.', $vm->vision_text);
        $this->assertEquals('Misi satu pembaruan.', $vm->display_mission_items[0]);

        // Reset
        $this->actingAs($admin)->post(route('admin.content-setup.about.vision-mission.reset'));
        $vm->refresh();
        $this->assertEquals('Arah Gerak Organisasi', $vm->tagline);
    }

    public function test_tentang_kami_page_renders_all_sections_faithfully(): void
    {
        $response = $this->get(route('tentang-kami'));

        $response->assertStatus(200);
        $response->assertSee('MENGENAL PMR');
        $response->assertSee('Profil PMR Wira SMAN 1 Ciawi');
        $response->assertSee('PMR Tingkat Wira');
        $response->assertSee('Tri Bakti PMR');
        $response->assertSee('Tri Bakti Palang Merah Remaja');
        $response->assertSee('Berbakti kepada Masyarakat');
        $response->assertSee('Landasan Moral Universal');
        $response->assertSee('7 Dasar Prinsip Palang Merah');
        $response->assertSee('KEMANUSIAAN');
        $response->assertSee('Arah Gerak Organisasi');
        $response->assertSee('Visi & Misi Kami');
    }
}

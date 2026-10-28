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
        $response->assertSee('Setup Konten Beranda');
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

        $response->assertRedirect(route('admin.content-setup.home'));
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

        $response->assertRedirect(route('admin.content-setup.home'));
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
    }
}

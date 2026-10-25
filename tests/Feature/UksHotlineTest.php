<?php

namespace Tests\Feature;

use App\Models\UksHotline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UksHotlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_uks_hotline_settings(): void
    {
        $response = $this->get(route('admin.uks-hotline.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_uks_hotline_settings_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.uks-hotline.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Hotline & Piket Medis UKS', false);
        $response->assertSee('Pratinjau Live');
    }

    public function test_admin_can_update_uks_hotline_settings(): void
    {
        $user = User::factory()->create();

        $updateData = [
            'phone_number' => '083872506817',
            'label' => 'Hotline UKS Siaga 1',
            'service_name' => 'Piket P3K & Respon Cepat Sekolah',
            'officer_name' => 'Relawan PMR Tim Rajawali',
            'operational_hours' => 'Senin - Sabtu (06.30 - 17.00 WIB)',
            'location' => 'Gedung A, Ruang UKS SMAN 1 Ciawi',
            'is_active' => '1',
            'status_text' => 'Siaga Penuh',
            'whatsapp_message' => 'Halo UKS, butuh tandu darurat di Lapangan Basket!',
            'description' => 'Hubungi jika ada siswa yang butuh penanganan segera di ruang kelas.',
        ];

        $response = $this->actingAs($user)->put(route('admin.uks-hotline.update'), $updateData);

        $response->assertRedirect(route('admin.uks-hotline.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('uks_hotlines', [
            'phone_number' => '083872506817',
            'label' => 'Hotline UKS Siaga 1',
            'service_name' => 'Piket P3K & Respon Cepat Sekolah',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_toggle_uks_hotline_status(): void
    {
        $user = User::factory()->create();
        $hotline = UksHotline::current();
        $initialStatus = $hotline->is_active;

        $response = $this->actingAs($user)->patch(route('admin.uks-hotline.toggle'));

        $response->assertRedirect(route('admin.uks-hotline.index'));
        $this->assertDatabaseHas('uks_hotlines', [
            'id' => $hotline->id,
            'is_active' => ! $initialStatus,
        ]);
    }

    public function test_public_pages_render_hotline_data_correctly(): void
    {
        $hotline = UksHotline::current();
        $hotline->update([
            'phone_number' => '083872506817',
            'label' => 'Hotline UKS Khusus',
            'service_name' => 'Pos Medis Darurat',
            'officer_name' => 'Tim Medis PMR',
            'operational_hours' => '07.00 - 16.00 WIB',
            'location' => 'Ruang UKS SMAN 1 Ciawi',
            'is_active' => true,
            'status_text' => 'Siaga',
            'whatsapp_message' => 'Halo UKS',
            'description' => 'Layanan P3K darurat.',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Hotline UKS Khusus');
        $response->assertSee('0838-7250-6817');
        $response->assertSee('openUksHotlineModal');
        $response->assertSee('uks-hotline-modal');
    }

    public function test_uks_hotline_model_accessors_and_fallbacks(): void
    {
        $hotline = new UksHotline([
            'phone_number' => '081234567890',
            'whatsapp_message' => 'Halo UKS darurat!',
        ]);

        $this->assertEquals('6281234567890', $hotline->clean_phone);
        $this->assertEquals('0812-3456-7890', $hotline->formatted_phone);
        $this->assertStringContainsString('https://wa.me/6281234567890', $hotline->whatsapp_url);
        $this->assertEquals('tel:+6281234567890', $hotline->tel_url);
    }
}

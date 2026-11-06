<?php

namespace Tests\Feature;

use App\Models\CompetitionEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionActionButtonsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected CompetitionEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
            'is_registration_open' => true,
            'is_btn_register_visible' => true,
            'is_btn_status_visible' => true,
            'is_btn_scoreboard_visible' => true,
            'is_btn_handbook_visible' => true,
            'handbook_file' => 'documents/juklak.pdf',
        ]);
    }

    public function test_backoffice_renders_action_button_controls(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-event.index'));

        $response->assertStatus(200);
        $response->assertSee('Status Tombol Aksi Halaman Publik');
        $response->assertSee('Daftar Sekarang');
        $response->assertSee('Cek Status');
        $response->assertSee('Live Klasemen');
        $response->assertSee('Juklak Juknis');

        $editResponse = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-event.edit', $this->event->id));

        $editResponse->assertStatus(200);
        $editResponse->assertSee('Tombol Aksi Halaman Lomba (/lomba)');
        $editResponse->assertSee('is_btn_register_visible');
        $editResponse->assertSee('is_btn_status_visible');
        $editResponse->assertSee('is_btn_scoreboard_visible');
        $editResponse->assertSee('is_btn_handbook_visible');
    }

    public function test_admin_can_toggle_action_buttons_individually(): void
    {
        // 1. Toggle register button OFF
        $resp = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-event.toggle-button', $this->event->id), [
                'button' => 'register',
            ]);
        $resp->assertRedirect();
        $this->event->refresh();
        $this->assertFalse($this->event->is_btn_register_visible);

        // 2. Toggle status button OFF
        $resp = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-event.toggle-button', $this->event->id), [
                'button' => 'status',
            ]);
        $resp->assertRedirect();
        $this->event->refresh();
        $this->assertFalse($this->event->is_btn_status_visible);

        // 3. Toggle scoreboard button OFF
        $resp = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-event.toggle-button', $this->event->id), [
                'button' => 'scoreboard',
            ]);
        $resp->assertRedirect();
        $this->event->refresh();
        $this->assertFalse($this->event->is_btn_scoreboard_visible);

        // 4. Toggle handbook button OFF
        $resp = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-event.toggle-button', $this->event->id), [
                'button' => 'handbook',
            ]);
        $resp->assertRedirect();
        $this->event->refresh();
        $this->assertFalse($this->event->is_btn_handbook_visible);

        // 5. Toggle register button back ON
        $resp = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-event.toggle-button', $this->event->id), [
                'button' => 'register',
            ]);
        $resp->assertRedirect();
        $this->event->refresh();
        $this->assertTrue($this->event->is_btn_register_visible);
    }

    public function test_public_landing_page_reflects_button_visibility(): void
    {
        // All enabled
        $response = $this->get(route('lomba.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Sekarang');
        $response->assertSee('Cek Status Pendaftaran');
        $response->assertSee('Live Klasemen Juara');
        $response->assertSee('fa-file-pdf', false);

        // Disable Daftar Sekarang & Live Klasemen
        $this->event->update([
            'is_btn_register_visible' => false,
            'is_btn_scoreboard_visible' => false,
        ]);

        $responseAfter = $this->get(route('lomba.index'));
        $responseAfter->assertStatus(200);
        $responseAfter->assertDontSee('Daftar Sekarang');
        $responseAfter->assertSee('Cek Status Pendaftaran');
        $responseAfter->assertDontSee('Live Klasemen Juara');
        $responseAfter->assertSee('fa-file-pdf', false);

        // Disable Cek Status & Handbook
        $this->event->update([
            'is_btn_status_visible' => false,
            'is_btn_handbook_visible' => false,
        ]);

        $responseFinal = $this->get(route('lomba.index'));
        $responseFinal->assertStatus(200);
        $responseFinal->assertDontSee('Cek Status Pendaftaran');
        $responseFinal->assertDontSee('fa-file-pdf', false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\CompetitionEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionInfoMenuVisibilityTest extends TestCase
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
            'title' => 'Sua Bhakti Berkarya 2026',
            'slug' => 'sbb-2026',
            'is_active' => true,
            'is_registration_open' => true,
            'is_info_menu_visible' => true,
        ]);
    }

    public function test_admin_can_view_info_menu_visibility_setting_in_backoffice(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-info-menus.index'));

        $response->assertStatus(200);
        $response->assertSee('Konfigurasi Menu Informasi Lomba di Halaman Publik');
        $response->assertSee('STATUS: DITAYANGKAN (TAMPIL DI WEB)');
        $response->assertSee('Sembunyikan dari Web');
    }

    public function test_admin_can_toggle_info_menu_visibility_off_and_on(): void
    {
        // 1. Sembunyikan (Set to 0)
        $responseHide = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-info-menus.toggle-visibility'), [
                'competition_event_id' => $this->event->id,
                'is_info_menu_visible' => 0,
            ]);

        $responseHide->assertRedirect();
        $responseHide->assertSessionHas('success');

        $this->event->refresh();
        $this->assertFalse($this->event->isInfoMenuVisible());

        // 2. Tayangkan kembali (Set to 1)
        $responseShow = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-info-menus.toggle-visibility'), [
                'competition_event_id' => $this->event->id,
                'is_info_menu_visible' => 1,
            ]);

        $responseShow->assertRedirect();
        $responseShow->assertSessionHas('success');

        $this->event->refresh();
        $this->assertTrue($this->event->isInfoMenuVisible());
    }

    public function test_lomba_page_shows_or_hides_info_menu_based_on_setting(): void
    {
        // 1. Saat aktif: Tampil di halaman /lomba
        $this->event->update(['is_info_menu_visible' => true]);

        $responseActive = $this->get(route('lomba.index'));
        $responseActive->assertStatus(200);
        $responseActive->assertSee('Menu Informasi Lomba');
        $responseActive->assertSee('id="modal-informasi-lomba"', false);

        // 2. Saat dimatikan: Sembunyi dari halaman /lomba
        $this->event->update(['is_info_menu_visible' => false]);

        $responseHidden = $this->get(route('lomba.index'));
        $responseHidden->assertStatus(200);
        $responseHidden->assertDontSee('Menu Informasi Lomba');
        $responseHidden->assertDontSee('id="modal-informasi-lomba"', false);
    }

    public function test_admin_can_update_info_menu_visibility_via_event_edit_form(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.competition-event.update', $this->event->id), [
                'title' => 'Sua Bhakti Berkarya 2026 Updated',
                'is_active' => 1,
                'is_info_menu_visible' => 0,
                'is_registration_open' => 1,
            ]);

        $response->assertRedirect(route('admin.competition-event.index'));

        $this->event->refresh();
        $this->assertFalse($this->event->isInfoMenuVisible());
    }
}

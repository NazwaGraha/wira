<?php

namespace Tests\Feature;

use App\Models\CompetitionEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CompetitionCountdownMascotTest extends TestCase
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
            'is_waves_enabled' => true,
            'wave1_start_date' => now()->subDay(),
            'wave1_end_date' => now()->addDays(15),
            'is_countdown_mascot_visible' => true,
            'countdown_mascot_left' => 'images/mascot/countdown_mascot_left.png',
            'countdown_mascot_right' => 'images/mascot/countdown_mascot_right.png',
        ]);
    }

    public function test_mascot_settings_rendered_in_event_edit_and_mascot_index(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-event.edit', $this->event->id));

        $response->assertStatus(200);
        $response->assertSee('Maskot Kartu Hitung Mundur (/lomba)');
        $response->assertSee('countdown_mascot_left');
        $response->assertSee('countdown_mascot_right');

        $mascotIndexResponse = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-mascots.index'));

        $mascotIndexResponse->assertStatus(200);
        $mascotIndexResponse->assertSee('Maskot Kartu Hitung Mundur Halaman Utama (/lomba)');
    }

    public function test_admin_can_toggle_countdown_mascot_visibility(): void
    {
        // Toggle OFF
        $resp = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-event.toggle-button', $this->event->id), [
                'button' => 'countdown_mascot',
            ]);

        $resp->assertRedirect();
        $this->event->refresh();
        $this->assertFalse($this->event->is_countdown_mascot_visible);

        // Public page should not display the mascots
        $publicResp = $this->get(route('lomba.index'));
        $publicResp->assertStatus(200);
        $publicResp->assertDontSee('alt="Maskot PMR Putri"', false);
        $publicResp->assertDontSee('alt="Maskot PMR Putra"', false);

        // Toggle back ON
        $respOn = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-event.toggle-button', $this->event->id), [
                'button' => 'countdown_mascot',
            ]);

        $respOn->assertRedirect();
        $this->event->refresh();
        $this->assertTrue($this->event->is_countdown_mascot_visible);

        // Public page should display the mascots
        $publicRespOn = $this->get(route('lomba.index'));
        $publicRespOn->assertStatus(200);
        $publicRespOn->assertSee('alt="Maskot PMR Putri"', false);
        $publicRespOn->assertSee('alt="Maskot PMR Putra"', false);
    }

    public function test_admin_can_upload_and_replace_countdown_mascots(): void
    {
        $fakeLeft = UploadedFile::fake()->image('test_left_mascot.png', 500, 700);
        $fakeRight = UploadedFile::fake()->image('test_right_mascot.png', 500, 700);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-mascots.update-countdown'), [
                'is_countdown_mascot_visible' => '1',
                'countdown_mascot_left' => $fakeLeft,
                'countdown_mascot_right' => $fakeRight,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->event->refresh();
        $this->assertTrue($this->event->is_countdown_mascot_visible);
        $this->assertStringContainsString('countdown_mascot_left_', $this->event->countdown_mascot_left);
        $this->assertStringContainsString('countdown_mascot_right_', $this->event->countdown_mascot_right);

        // Clean up test generated files
        if (File::exists(public_path($this->event->countdown_mascot_left))) {
            File::delete(public_path($this->event->countdown_mascot_left));
        }
        if (File::exists(public_path($this->event->countdown_mascot_right))) {
            File::delete(public_path($this->event->countdown_mascot_right));
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionCheckinTest extends TestCase
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
        ]);
    }

    public function test_checkin_page_displays_total_payment_amount(): void
    {
        $registration = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SD BENGONG WAE',
            'registration_code' => 'SBB-U24826K',
            'level' => 'Mula',
            'advisor_name' => 'Pak Jujum',
            'advisor_phone' => '081298506111',
            'total_payment' => 600000,
            'status' => 'verified',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-checkin.index'));

        $response->assertStatus(200);
        $response->assertSee('SD BENGONG WAE');
        $response->assertSee('Rp 600.000');
        $response->assertDontSee('Biaya: Rp 0');
    }

    public function test_checkin_lookup_returns_formatted_payment_amount(): void
    {
        $registration = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SD SUKA JAIL SELALU',
            'registration_code' => 'SBB-U76922B',
            'level' => 'Mula',
            'advisor_name' => 'Pak Dudung',
            'advisor_phone' => '081298506111',
            'total_payment' => 1200000,
            'status' => 'verified',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-checkin.lookup', ['code' => 'SBB-U76922B']));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'registration' => [
                'registration_code' => 'SBB-U76922B',
                'school_name' => 'SD SUKA JAIL SELALU',
                'total_fee' => '1.200.000',
                'total_payment' => '1.200.000',
            ],
        ]);
    }
}

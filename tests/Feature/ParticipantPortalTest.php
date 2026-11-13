<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantPortalTest extends TestCase
{
    use RefreshDatabase;

    protected CompetitionEvent $event;

    protected CompetitionRegistration $registration;

    protected CompetitionCategory $category1;

    protected CompetitionCategory $category2;

    protected CompetitionParticipantTeam $team1;

    protected CompetitionParticipantTeam $team2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
            'is_registration_open' => true,
        ]);

        $this->registration = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 CIAWI BOGOR',
            'registration_code' => 'SBB-W54342H',
            'level' => 'Wira',
            'advisor_name' => 'Ibu Siti Rahmawati',
            'advisor_phone' => '081234567890',
            'advisor_email' => 'pembina@sman1ciawi.sch.id',
            'total_payment' => 500000,
            'status' => 'verified',
        ]);

        $this->category1 = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'name' => 'Pertolongan Pertama Putra',
            'code' => 'PP-PA',
            'level' => 'Wira',
            'price' => 100000,
            'is_active' => true,
        ]);

        $this->category2 = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'name' => 'Tandu Darurat Putra',
            'code' => 'TD-PA',
            'level' => 'Wira',
            'price' => 100000,
            'is_active' => true,
        ]);

        $this->team1 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $this->registration->id,
            'competition_category_id' => $this->category1->id,
            'order_number' => 'A-04',
            'team_name' => 'SMAN 1 Ciawi Regu A',
            'members_list' => [
                ['name' => 'Ahmad Rizky', 'role' => 'Ketua Regu'],
                ['name' => 'Fajar Maulana', 'role' => 'Anggota'],
                ['name' => 'Kevin Septian', 'role' => 'Anggota'],
            ],
            'is_active' => true,
        ]);

        $this->team2 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $this->registration->id,
            'competition_category_id' => $this->category2->id,
            'order_number' => 'B-02',
            'team_name' => 'SMAN 1 Ciawi Tandu',
            'members_list' => [
                ['name' => 'Dika Pratama', 'role' => 'Anggota 1'],
                ['name' => 'Reza Aditia', 'role' => 'Anggota 2'],
            ],
            'is_active' => true,
        ]);

        // Simpan nilai untuk team 1
        CompetitionScore::create([
            'competition_category_id' => $this->category1->id,
            'competition_participant_team_id' => $this->team1->id,
            'final_score' => 89.50,
            'time_recorded' => '04:32',
            'is_disqualified' => false,
        ]);
    }

    public function test_portal_login_page_renders_cleanly(): void
    {
        $response = $this->get(route('participant.portal'));

        $response->assertStatus(200);
        $response->assertSee('PORTAL PESERTA LOMBA');
        $response->assertSee('Alamat Email Pembina');
    }

    public function test_portal_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson(route('participant.login'), [
            'email' => 'wrong@email.com',
            'registration_code' => 'SBB-W54342H',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_portal_login_succeeds_and_sets_session(): void
    {
        $response = $this->postJson(route('participant.login'), [
            'email' => 'pembina@sman1ciawi.sch.id',
            'registration_code' => 'SBB-W54342H',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'school_name' => 'SMAN 1 CIAWI BOGOR',
            'registration_code' => 'SBB-W54342H',
            'level' => 'WIRA',
        ]);

        $this->assertEquals($this->registration->id, session('participant_registration_id'));
    }

    public function test_authenticated_participant_can_view_dashboard_with_scores_and_roster(): void
    {
        $response = $this->withSession(['participant_registration_id' => $this->registration->id])
            ->get(route('participant.portal'));

        $response->assertStatus(200);
        $response->assertSee('SMAN 1 CIAWI BOGOR');
        $response->assertSee('Pertolongan Pertama Putra');
        $response->assertSee('Tandu Darurat Putra');
        $response->assertSee('89.50');
        $response->assertSee('Ahmad Rizky');
        $response->assertSee('Menunggu Penilaian Juri');
    }

    public function test_live_data_polling_endpoint_returns_json(): void
    {
        $response = $this->withSession(['participant_registration_id' => $this->registration->id])
            ->getJson(route('participant.live-data'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'school_name' => 'SMAN 1 CIAWI BOGOR',
            'registration_code' => 'SBB-W54342H',
        ]);
        $response->assertJsonFragment([
            'category_name' => 'Pertolongan Pertama Putra',
            'formatted_score' => '89.50',
            'status_label' => 'Selesai Dinilai',
        ]);
    }

    public function test_export_pdf_report_generates_successfully(): void
    {
        $response = $this->withSession(['participant_registration_id' => $this->registration->id])
            ->get(route('participant.export-pdf'));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_export_excel_report_streams_csv_successfully(): void
    {
        $response = $this->withSession(['participant_registration_id' => $this->registration->id])
            ->get(route('participant.export-excel'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_portal_logout_clears_session_and_redirects(): void
    {
        $response = $this->withSession(['participant_registration_id' => $this->registration->id])
            ->post(route('participant.logout'));

        $response->assertRedirect(route('participant.portal'));
        $this->assertNull(session('participant_registration_id'));
    }

    public function test_portal_shortcut_redirects_to_participant_portal(): void
    {
        $response = $this->get('/portal-peserta');
        $response->assertRedirect(route('participant.portal'));
    }
}

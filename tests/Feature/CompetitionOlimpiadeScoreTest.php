<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionOlimpiadeScoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected CompetitionEvent $event;

    protected CompetitionCategory $wiraCategory;

    protected CompetitionCategory $madyaCategory;

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
        ]);

        $this->wiraCategory = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'code' => 'OLIM-WIRA',
            'name' => 'Olimpiade Kepalangmerahan',
            'level' => 'Wira',
            'gender_category' => 'Umum',
            'scoring_type' => 'written_time',
            'is_active' => true,
        ]);

        $this->madyaCategory = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'code' => 'OLIM-MADYA',
            'name' => 'Olimpiade Kepalangmerahan',
            'level' => 'Madya',
            'gender_category' => 'Umum',
            'scoring_type' => 'written_time',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_olimpiade_grid_for_madya_and_wira(): void
    {
        // 1. Wira Grid View
        $responseWira = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));

        $responseWira->assertStatus(200);
        $responseWira->assertSee('WIRA (SMA / SMK / MA)');
        $responseWira->assertSee('Nilai Akhir = (Benar');

        // 2. Madya Grid View
        $responseMadya = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-scores.olimpiade', $this->madyaCategory->id));

        $responseMadya->assertStatus(200);
        $responseMadya->assertSee('MADYA (SMP / MTs)');
    }

    public function test_admin_can_save_olimpiade_scores_and_ranking_is_calculated_correctly_with_tie_breaker(): void
    {
        $regA = CompetitionRegistration::create([
            'registration_code' => 'REG-001',
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 5 Bandung A',
            'level' => 'Wira',
            'advisor_name' => 'Pembina A',
            'advisor_phone' => '08123456789',
            'advisor_email' => 'pembinaA@test.com',
            'status' => 'verified',
        ]);
        $teamA = CompetitionParticipantTeam::create([
            'competition_category_id' => $this->wiraCategory->id,
            'competition_registration_id' => $regA->id,
            'team_name' => 'SMAN 5 Bandung A',
            'order_number' => 13,
            'is_active' => true,
        ]);

        $regB = CompetitionRegistration::create([
            'registration_code' => 'REG-002',
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 Cianjur',
            'level' => 'Wira',
            'advisor_name' => 'Pembina B',
            'advisor_phone' => '08123456789',
            'advisor_email' => 'pembinaB@test.com',
            'status' => 'verified',
        ]);
        $teamB = CompetitionParticipantTeam::create([
            'competition_category_id' => $this->wiraCategory->id,
            'competition_registration_id' => $regB->id,
            'team_name' => 'SMAN 1 Cianjur',
            'order_number' => 20,
            'is_active' => true,
        ]);

        $regC = CompetitionRegistration::create([
            'registration_code' => 'REG-003',
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 Karawang',
            'level' => 'Wira',
            'advisor_name' => 'Pembina C',
            'advisor_phone' => '08123456789',
            'advisor_email' => 'pembinaC@test.com',
            'status' => 'verified',
        ]);
        $teamC = CompetitionParticipantTeam::create([
            'competition_category_id' => $this->wiraCategory->id,
            'competition_registration_id' => $regC->id,
            'team_name' => 'SMAN 1 Karawang',
            'order_number' => 7,
            'is_active' => true,
        ]);

        // Simpan nilai:
        // Team A: 50 Benar, 0 Salah -> Skor (50 - 0) * 2 = 100, Waktu 07:54
        // Team B: 50 Benar, 0 Salah -> Skor (50 - 0) * 2 = 100, Waktu 10:33 (Tie-breaker kalah cepat)
        // Team C: 48 Benar, 0 Salah -> Skor (48 - 0) * 2 = 96, Waktu 11:16
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-save', $this->wiraCategory->id), [
                'benar' => [
                    $teamA->id => 50,
                    $teamB->id => 50,
                    $teamC->id => 48,
                ],
                'salah' => [
                    $teamA->id => 0,
                    $teamB->id => 0,
                    $teamC->id => 0,
                ],
                'waktu' => [
                    $teamA->id => '07:54',
                    $teamB->id => '10:33',
                    $teamC->id => '11:16',
                ],
                'formula_type' => 'standard',
            ]);

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));
        $response->assertSessionHas('success');

        // Verifikasi Team A Juara 1 (Skor 100, 07:54)
        $scoreA = CompetitionScore::where('competition_participant_team_id', $teamA->id)->first();
        $this->assertNotNull($scoreA);
        $this->assertEquals(100.0, $scoreA->final_score);
        $this->assertEquals(1, $scoreA->rank);

        // Verifikasi Team B Juara 2 (Skor 100, 10:33 -> Tie Breaker Waktu)
        $scoreB = CompetitionScore::where('competition_participant_team_id', $teamB->id)->first();
        $this->assertNotNull($scoreB);
        $this->assertEquals(100.0, $scoreB->final_score);
        $this->assertEquals(2, $scoreB->rank);

        // Verifikasi Team C Juara 3 (Skor 96)
        $scoreC = CompetitionScore::where('competition_participant_team_id', $teamC->id)->first();
        $this->assertNotNull($scoreC);
        $this->assertEquals(96.0, $scoreC->final_score);
        $this->assertEquals(3, $scoreC->rank);
    }

    public function test_admin_can_seed_olimpiade_simulation_data(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-seed-simulation', $this->wiraCategory->id));

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));
        $response->assertSessionHas('success');

        // Pastikan 20 tim simulasi terbuat dan memiliki nilai
        $this->assertEquals(20, CompetitionParticipantTeam::where('competition_category_id', $this->wiraCategory->id)->count());
        $this->assertEquals(20, CompetitionScore::where('competition_category_id', $this->wiraCategory->id)->count());

        // Cek bahwa ranking 1 adalah SMAN 5 Bandung A
        $rank1Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 1)
            ->first();
        $this->assertNotNull($rank1Score);
        $this->assertEquals(100.0, $rank1Score->final_score);
        $this->assertEquals('07:54', $rank1Score->time_recorded);
    }

    public function test_grid_route_redirects_to_olimpiade_view(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-scores.grid', $this->wiraCategory->id));

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));
    }

    public function test_jury_can_save_olimpiade_score_on_mobile(): void
    {
        $jury = User::factory()->create([
            'role' => 'jury',
        ]);

        $reg = CompetitionRegistration::create([
            'registration_code' => 'REG-JURY-1',
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMPN 1 Ciawi',
            'level' => 'Madya',
            'advisor_name' => 'Pembina SMP',
            'advisor_phone' => '08123456789',
            'advisor_email' => 'pembina@smp.sch.id',
            'status' => 'verified',
        ]);
        $team = CompetitionParticipantTeam::create([
            'competition_category_id' => $this->madyaCategory->id,
            'competition_registration_id' => $reg->id,
            'team_name' => 'SMPN 1 Ciawi Regu A',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($jury)
            ->post(route('jury.save-score', ['category' => $this->madyaCategory->id, 'team' => $team->id]), [
                'final_score' => 84,
                'minutes' => 7,
                'seconds' => 45,
                'rubric' => [
                    'benar' => 44,
                    'salah' => 2,
                ],
            ]);

        $response->assertRedirect();

        $score = CompetitionScore::where('competition_participant_team_id', $team->id)->first();
        $this->assertNotNull($score);
        $this->assertEquals(84.0, $score->final_score);
        $this->assertEquals('07:45', $score->time_recorded);
        $this->assertEquals(44, $score->score_details['benar']);
        $this->assertEquals(2, $score->score_details['salah']);
        $this->assertEquals(4, $score->score_details['kosong']);
    }
}

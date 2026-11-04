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
        $responseWira->assertSee('Nilai Akhir =');

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
        $this->assertEquals('Juara 1', $scoreA->predicate);

        // Verifikasi Team B Juara 2 (Skor 100, 10:33 -> Tie Breaker Waktu)
        $scoreB = CompetitionScore::where('competition_participant_team_id', $teamB->id)->first();
        $this->assertNotNull($scoreB);
        $this->assertEquals(100.0, $scoreB->final_score);
        $this->assertEquals(2, $scoreB->rank);
        $this->assertEquals('Juara 2', $scoreB->predicate);

        // Verifikasi Team C Juara 3 (Skor 96)
        $scoreC = CompetitionScore::where('competition_participant_team_id', $teamC->id)->first();
        $this->assertNotNull($scoreC);
        $this->assertEquals(96.0, $scoreC->final_score);
        $this->assertEquals(3, $scoreC->rank);
        $this->assertEquals('Juara 3', $scoreC->predicate);
    }

    public function test_admin_can_input_time_in_mm_ss_format_example_05_12(): void
    {
        $reg = CompetitionRegistration::create([
            'registration_code' => 'REG-TIME-1',
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 Ciawi Regu A',
            'level' => 'Wira',
            'advisor_name' => 'Pembina Time',
            'advisor_phone' => '08123456789',
            'advisor_email' => 'pembina.time@test.com',
            'status' => 'verified',
        ]);
        $team = CompetitionParticipantTeam::create([
            'competition_category_id' => $this->wiraCategory->id,
            'competition_registration_id' => $reg->id,
            'team_name' => 'SMAN 1 Ciawi Regu A',
            'order_number' => 1,
            'is_active' => true,
        ]);

        // Input 05:12
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-save', $this->wiraCategory->id), [
                'benar' => [$team->id => 45],
                'salah' => [$team->id => 5],
                'waktu' => [$team->id => '05:12'],
                'formula_type' => 'standard',
            ]);

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));

        $score = CompetitionScore::where('competition_participant_team_id', $team->id)->first();
        $this->assertNotNull($score);
        $this->assertEquals('05:12', $score->time_recorded);
        $this->assertEquals(312, $score->score_details['time_seconds']);
        $this->assertEquals(80.0, $score->final_score);
    }

    public function test_admin_can_seed_olimpiade_simulation_data_and_determines_single_session_winners(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-seed-simulation', $this->wiraCategory->id));

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));
        $response->assertSessionHas('success');

        // Pastikan 20 tim simulasi terbuat dan memiliki nilai
        $this->assertEquals(20, CompetitionParticipantTeam::where('competition_category_id', $this->wiraCategory->id)->count());
        $this->assertEquals(20, CompetitionScore::where('competition_category_id', $this->wiraCategory->id)->count());

        // Cek bahwa ranking 1 adalah SMAN 5 Bandung A (Juara 1 Emas, Skor 100, 07:54)
        $rank1Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 1)
            ->first();
        $this->assertNotNull($rank1Score);
        $this->assertEquals(100.0, $rank1Score->final_score);
        $this->assertEquals('07:54', $rank1Score->time_recorded);
        $this->assertEquals('Juara 1', $rank1Score->predicate);

        // Cek Ranking 2 adalah SMAN 1 Cianjur (Juara 2 Perak, Skor 100, 10:33)
        $rank2Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 2)
            ->first();
        $this->assertNotNull($rank2Score);
        $this->assertEquals(100.0, $rank2Score->final_score);
        $this->assertEquals('10:33', $rank2Score->time_recorded);
        $this->assertEquals('Juara 2', $rank2Score->predicate);

        // Cek Ranking 3 adalah SMAN 1 Karawang (Juara 3 Perunggu, Skor 98, 11:16)
        $rank3Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 3)
            ->first();
        $this->assertNotNull($rank3Score);
        $this->assertEquals(98.0, $rank3Score->final_score);
        $this->assertEquals('11:16', $rank3Score->time_recorded);
        $this->assertEquals('Juara 3', $rank3Score->predicate);

        // Cek Ranking 4 adalah SMAN 1 Cikarang Utara B (Juara Harapan 1, Skor 92, 07:45)
        $rank4Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 4)
            ->first();
        $this->assertNotNull($rank4Score);
        $this->assertEquals(92.0, $rank4Score->final_score);
        $this->assertEquals('07:45', $rank4Score->time_recorded);
        $this->assertEquals('Juara Harapan 1', $rank4Score->predicate);

        // Cek Ranking 5 adalah SMAN 3 Bekasi (Juara Harapan 2, Skor 80, 14:18)
        $rank5Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 5)
            ->first();
        $this->assertNotNull($rank5Score);
        $this->assertEquals(80.0, $rank5Score->final_score);
        $this->assertEquals('14:18', $rank5Score->time_recorded);
        $this->assertEquals('Juara Harapan 2', $rank5Score->predicate);

        // Cek Ranking 6 adalah SMAN 5 Bandung B (Juara Harapan 3, Skor 76, 07:00)
        $rank6Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 6)
            ->first();
        $this->assertNotNull($rank6Score);
        $this->assertEquals(76.0, $rank6Score->final_score);
        $this->assertEquals('07:00', $rank6Score->time_recorded);
        $this->assertEquals('Juara Harapan 3', $rank6Score->predicate);

        // Verifikasi getWinners() model mengembalikan pemenang secara langsung
        $winners = $this->wiraCategory->getWinners();
        $this->assertEquals('SMAN 5 Bandung A', $winners[0]?->team?->team_name);
        $this->assertEquals('SMAN 1 Cianjur', $winners[1]?->team?->team_name);
        $this->assertEquals('SMAN 1 Karawang', $winners[2]?->team?->team_name);
        $this->assertEquals('SMAN 1 Cikarang Utara B', $winners[3]?->team?->team_name);
        $this->assertEquals('SMAN 3 Bekasi', $winners[4]?->team?->team_name);
        $this->assertEquals('SMAN 5 Bandung B', $winners[5]?->team?->team_name);
    }

    public function test_grid_route_redirects_to_olimpiade_view(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-scores.grid', $this->wiraCategory->id));

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));
    }

    public function test_single_session_winners_podium_is_rendered_properly(): void
    {
        // 1. Seed simulasi olimpiade (20 tim)
        $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-seed-simulation', $this->wiraCategory->id));

        // 2. Akses halaman grid olimpiade
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));

        $response->assertStatus(200);
        $response->assertSee('JUARA 1 (EMAS)');
        $response->assertSee('JUARA 2 (PERAK)');
        $response->assertSee('JUARA 3 (PERUNGGU)');
        $response->assertSee('JUARA HARAPAN 1');
        $response->assertSee('JUARA HARAPAN 2');
        $response->assertSee('JUARA HARAPAN 3');
        $response->assertSee('SMAN 5 Bandung A');
        $response->assertSee('SMAN 1 Cianjur');
        $response->assertSee('SMAN 1 Karawang');
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

    public function test_admin_can_input_three_digit_scores_and_custom_kosong_value(): void
    {
        $reg = CompetitionRegistration::create([
            'registration_code' => 'REG-3DIGIT',
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 Tiga Digit',
            'level' => 'Wira',
            'advisor_name' => 'Pembina 3 Digit',
            'advisor_phone' => '08123456789',
            'advisor_email' => '3digit@test.com',
            'status' => 'verified',
        ]);
        $team = CompetitionParticipantTeam::create([
            'competition_category_id' => $this->wiraCategory->id,
            'competition_registration_id' => $reg->id,
            'team_name' => 'SMAN 1 Tiga Digit',
            'order_number' => 1,
            'is_active' => true,
        ]);

        // Input 3-digit: benar 200, salah 10, kosong 5
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-save', $this->wiraCategory->id), [
                'benar' => [$team->id => 200],
                'salah' => [$team->id => 10],
                'kosong' => [$team->id => 5],
                'waktu' => [$team->id => '12:34'],
                'formula_type' => 'standard',
            ]);

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));

        $score = CompetitionScore::where('competition_participant_team_id', $team->id)->first();
        $this->assertNotNull($score);
        $this->assertEquals(380.0, $score->final_score); // (200 - 10) * 2
        $this->assertEquals(200, $score->score_details['benar']);
        $this->assertEquals(10, $score->score_details['salah']);
        $this->assertEquals(5, $score->score_details['kosong']);
        $this->assertEquals('12:34', $score->time_recorded);
    }
}

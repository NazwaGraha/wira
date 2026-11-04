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

        $response->assertRedirect(route('admin.competition-scores.olimpiade', ['category' => $this->wiraCategory->id, 'stage' => 1]));
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

        $response->assertRedirect(route('admin.competition-scores.olimpiade', ['category' => $this->wiraCategory->id, 'stage' => 1]));

        $score = CompetitionScore::where('competition_participant_team_id', $team->id)->first();
        $this->assertNotNull($score);
        $this->assertEquals('05:12', $score->time_recorded);
        $this->assertEquals(312, $score->score_details['time_seconds']);
        $this->assertEquals(80.0, $score->final_score);
    }

    public function test_admin_can_seed_olimpiade_simulation_data(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-seed-simulation', $this->wiraCategory->id));

        $response->assertRedirect(route('admin.competition-scores.olimpiade', ['category' => $this->wiraCategory->id, 'stage' => 1]));
        $response->assertSessionHas('success');

        // Pastikan 20 tim simulasi terbuat dan memiliki nilai
        $this->assertEquals(20, CompetitionParticipantTeam::where('competition_category_id', $this->wiraCategory->id)->count());
        $this->assertEquals(20, CompetitionScore::where('competition_category_id', $this->wiraCategory->id)->count());

        // Cek bahwa ranking 1 adalah SMAN 5 Bandung A (100 poin, 07:54)
        $rank1Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 1)
            ->first();
        $this->assertNotNull($rank1Score);
        $this->assertEquals(100.0, $rank1Score->final_score);
        $this->assertEquals('07:54', $rank1Score->time_recorded);

        // Cek Ranking 4 adalah SMAN 1 Cikarang Utara B (44 benar, 2 salah -> 92 poin sesuai Draft Sheet Skema 2)
        $rank4Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 4)
            ->first();
        $this->assertNotNull($rank4Score);
        $this->assertEquals(92.0, $rank4Score->final_score);
        $this->assertEquals('07:45', $rank4Score->time_recorded);

        // Cek Ranking 9 adalah SMAN 1 Cikarang Utara A (32 benar, 12 salah -> 70 poin)
        $rank9Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 9)
            ->first();
        $this->assertNotNull($rank9Score);
        $this->assertEquals(70.0, $rank9Score->final_score);
        $this->assertEquals('07:28', $rank9Score->time_recorded);

        // Cek Ranking 20 adalah SMAN 1 Purwakarta (20 benar, 25 salah -> 45 poin)
        $rank20Score = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('rank', 20)
            ->first();
        $this->assertNotNull($rank20Score);
        $this->assertEquals(45.0, $rank20Score->final_score);
        $this->assertEquals('10:04', $rank20Score->time_recorded);
    }

    public function test_grid_route_redirects_to_olimpiade_view(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.competition-scores.grid', $this->wiraCategory->id));

        $response->assertRedirect(route('admin.competition-scores.olimpiade', $this->wiraCategory->id));
    }

    public function test_admin_can_generate_semifinal_from_top_9_with_anti_school_clash(): void
    {
        // 1. Seed simulasi olimpiade (20 tim)
        $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-seed-simulation', $this->wiraCategory->id));

        // 2. Generate Semifinal dari 9 tim teratas
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-generate-semifinal', $this->wiraCategory->id));

        $response->assertRedirect(route('admin.competition-scores.olimpiade', ['category' => $this->wiraCategory->id, 'stage' => 2]));
        $response->assertSessionHas('success');

        // Pastikan ada 9 tim yang masuk babak semifinal
        $semiScores = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('round_name', 'Semi Final')
            ->get();

        $this->assertCount(9, $semiScores);

        // Pastikan pembagian termin tidak membenturkan satu sekolah (SMAN 5 Bandung A & B harus di termin berbeda)
        $teamA = CompetitionParticipantTeam::where('competition_category_id', $this->wiraCategory->id)
            ->where('team_name', 'SMAN 5 Bandung A')
            ->first();
        $teamB = CompetitionParticipantTeam::where('competition_category_id', $this->wiraCategory->id)
            ->where('team_name', 'SMAN 5 Bandung B')
            ->first();

        $scoreSemiA = $semiScores->firstWhere('competition_participant_team_id', $teamA->id);
        $scoreSemiB = $semiScores->firstWhere('competition_participant_team_id', $teamB->id);

        $this->assertNotNull($scoreSemiA);
        $this->assertNotNull($scoreSemiB);
        $this->assertNotEquals($scoreSemiA->score_details['termin'], $scoreSemiB->score_details['termin']);
    }

    public function test_admin_can_save_olimpiade_semifinal_and_grand_final(): void
    {
        // Seed simulasi & generate semifinal
        $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-seed-simulation', $this->wiraCategory->id));
        $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-generate-semifinal', $this->wiraCategory->id));

        $semiScores = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('round_name', 'Semi Final')
            ->get();

        // Siapkan input semi
        $amplop = [];
        $rebutan = [];
        $balon = [];
        $termins = [];

        foreach ($semiScores as $idx => $sc) {
            $tId = $sc->competition_participant_team_id;
            $tNum = $sc->score_details['termin'];
            $termins[$tId] = $tNum;
            $amplop[$tId] = 100 + ($idx * 10);
            $rebutan[$tId] = 50;
            $balon[$tId] = 50;
        }

        $resSemi = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-save-semifinal', $this->wiraCategory->id), [
                'semi_amplop' => $amplop,
                'semi_rebutan' => $rebutan,
                'semi_balon' => $balon,
                'semi_termin' => $termins,
            ]);

        $resSemi->assertRedirect(route('admin.competition-scores.olimpiade', ['category' => $this->wiraCategory->id, 'stage' => 2]));

        // Pastikan 3 juara termin otomatis terdaftar di babak Final
        $finalScores = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('round_name', 'Final')
            ->get();
        $this->assertCount(3, $finalScores);

        // Simpan nilai Final
        $finalAmplop = [];
        $finalRebutan = [];
        $finalTaruh = [];
        $scoresToAssign = [300, 200, 100];

        foreach ($finalScores as $fIdx => $fs) {
            $fId = $fs->competition_participant_team_id;
            $finalAmplop[$fId] = $scoresToAssign[$fIdx] ?? 100;
            $finalRebutan[$fId] = 0;
            $finalTaruh[$fId] = 0;
        }

        $resFinal = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.olimpiade-save-final', $this->wiraCategory->id), [
                'final_amplop' => $finalAmplop,
                'final_rebutan' => $finalRebutan,
                'final_taruh' => $finalTaruh,
            ]);

        $resFinal->assertRedirect(route('admin.competition-scores.olimpiade', ['category' => $this->wiraCategory->id, 'stage' => 3]));

        // Cek Juara 1 Final
        $juara1Final = CompetitionScore::where('competition_category_id', $this->wiraCategory->id)
            ->where('round_name', 'Final')
            ->where('rank', 1)
            ->first();
        $this->assertNotNull($juara1Final);
        $this->assertEquals(300.0, $juara1Final->final_score);
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

        $response->assertRedirect(route('admin.competition-scores.olimpiade', ['category' => $this->wiraCategory->id, 'stage' => 1]));

        $score = CompetitionScore::where('competition_participant_team_id', $team->id)->first();
        $this->assertNotNull($score);
        $this->assertEquals(380.0, $score->final_score); // (200 - 10) * 2
        $this->assertEquals(200, $score->score_details['benar']);
        $this->assertEquals(10, $score->score_details['salah']);
        $this->assertEquals(5, $score->score_details['kosong']);
        $this->assertEquals('12:34', $score->time_recorded);
    }
}

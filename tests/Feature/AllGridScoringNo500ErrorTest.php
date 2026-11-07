<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AllGridScoringNo500ErrorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $jury;

    private CompetitionEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin PMR',
            'username' => 'admin_tester',
            'email' => 'admin@test.pmr',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $this->jury = User::create([
            'name' => 'Juri Lapangan',
            'username' => 'juri_tester',
            'email' => 'juri@test.pmr',
            'role' => 'jury',
            'password' => Hash::make('password'),
            'must_change_password' => false,
        ]);

        $this->event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
        ]);
    }

    private function createCategoryAndTeam(string $code, string $name, string $level, string $gender = 'Umum'): array
    {
        $category = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'code' => $code,
            'name' => $name,
            'level' => $level,
            'gender_category' => $gender,
            'scoring_type' => 'standard_time',
            'point_tier' => 'tier_2',
            'order_position' => 1,
            'is_active' => true,
        ]);

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMPN 1 CIAWI',
            'registration_code' => 'REG-'.$code,
            'level' => $level,
            'status' => 'verified',
            'advisor_name' => 'Pembina Test',
            'advisor_phone' => '08123456789',
        ]);

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $registration->id,
            'competition_category_id' => $category->id,
            'team_name' => 'Regu A '.$schoolName = 'SMPN 1 CIAWI',
            'team_label' => 'Regu A',
            'order_number' => 1,
            'session_order' => 'A-01',
            'is_active' => true,
        ]);

        $this->jury->assignedCategories()->syncWithoutDetaching([$category->id]);

        return [$category, $team];
    }

    /**
     * Test GET Admin Grid Score for all categories and levels (Ensure NO HTTP 500)
     */
    public function test_all_admin_grid_scoring_pages_render_without_500(): void
    {
        $this->actingAs($this->admin);

        $testCategories = [
            // LKTR
            ['LKTR-MULA-PA', 'Ketangkasan Tandu Reguler', 'Mula'],
            ['LKTR-MADYA-PA', 'Ketangkasan Tandu Reguler', 'Madya'],
            ['LKTR-WIRA-PA', 'Ketangkasan Tandu Reguler', 'Wira'],

            // Pertolongan Pertama (PP)
            ['LPP-MULA-PA', 'Pertolongan Pertama', 'Mula'],
            ['LPP-MADYA-PA', 'Pertolongan Pertama', 'Madya'],
            ['LPP-WIRA-PA', 'Pertolongan Pertama', 'Wira'],

            // Pendidikan Remaja Sebaya (PRS)
            ['PRS-MADYA', 'Pendidikan Remaja Sebaya', 'Madya'],
            ['PRS-WIRA', 'Pendidikan Remaja Sebaya', 'Wira'],

            // Mewarnai
            ['MEWARNAI-MULA', 'Lomba Mewarnai', 'Mula'],
        ];

        foreach ($testCategories as [$code, $name, $level]) {
            [$category, $team] = $this->createCategoryAndTeam($code, $name, $level);

            $response = $this->get("/admin/competition-scores/{$category->id}/grid");

            $this->assertNotEquals(500, $response->getStatusCode(), "GET /admin/competition-scores/{$category->id}/grid returned 500 for {$name} ({$level})");
            $response->assertOk();
        }

        // Test LCT Grid view specifically
        [$catLct, $teamLct] = $this->createCategoryAndTeam('LCT-MADYA', 'Lomba Cepat Tepat', 'Madya');
        $responseLct = $this->get("/admin/competition-scores/{$catLct->id}/lct");
        $this->assertNotEquals(500, $responseLct->getStatusCode(), "GET /admin/competition-scores/{$catLct->id}/lct returned 500");
        $responseLct->assertOk();
    }

    /**
     * Test GET Admin Grid Score when category has 0 teams registered/verified (Ensure NO HTTP 500)
     */
    public function test_admin_grid_scoring_pages_render_without_500_when_category_has_no_teams(): void
    {
        $this->actingAs($this->admin);

        $testCategories = [
            // LKTR
            ['LKTR-MULA-EMPTY', 'Ketangkasan Tandu Reguler', 'Mula'],
            ['LKTR-MADYA-EMPTY', 'Ketangkasan Tandu Reguler', 'Madya'],
            ['LKTR-WIRA-EMPTY', 'Ketangkasan Tandu Reguler', 'Wira'],

            // Pertolongan Pertama (PP)
            ['LPP-MULA-EMPTY', 'Pertolongan Pertama', 'Mula'],
            ['LPP-MADYA-EMPTY', 'Pertolongan Pertama', 'Madya'],
            ['LPP-WIRA-EMPTY', 'Pertolongan Pertama', 'Wira'],

            // Pendidikan Remaja Sebaya (PRS)
            ['PRS-MADYA-EMPTY', 'Pendidikan Remaja Sebaya', 'Madya'],
            ['PRS-WIRA-EMPTY', 'Pendidikan Remaja Sebaya', 'Wira'],

            // Mewarnai
            ['MEWARNAI-MULA-EMPTY', 'Lomba Mewarnai', 'Mula'],
        ];

        foreach ($testCategories as [$code, $name, $level]) {
            $category = CompetitionCategory::create([
                'competition_event_id' => $this->event->id,
                'code' => $code,
                'name' => $name,
                'level' => $level,
                'gender_category' => 'Umum',
                'scoring_type' => 'standard_time',
                'point_tier' => 'tier_2',
                'order_position' => 1,
                'is_active' => true,
            ]);

            $response = $this->get("/admin/competition-scores/{$category->id}/grid");

            $this->assertNotEquals(500, $response->getStatusCode(), "GET /admin/competition-scores/{$category->id}/grid returned 500 when empty for {$name} ({$level})");
            $response->assertOk();
        }
    }

    /**
     * Test POST Admin Save Grid Score for all categories (Ensure NO HTTP 500)
     */
    public function test_all_admin_grid_scoring_save_without_500(): void
    {
        $this->actingAs($this->admin);

        // 1. LKTR Madya
        [$catLktr, $teamLktr] = $this->createCategoryAndTeam('LKTR-MADYA-TEST', 'Ketangkasan Tandu Reguler', 'Madya');
        $payloadLktr = [
            'competition_participant_team_id' => $teamLktr->id,
            'round_name' => 'Utama',
            'action_after' => 'stay',
            'time_recorded' => '02:40', // Max time score 300
            'rubric' => [
                'kekuatan_1' => 80,
                'kekuatan_2' => 80,
                'kekuatan_3a' => 60,
                'kekuatan_3b' => 100,
                'kekuatan_4' => 80,
                'kerapihan_1' => 20,
                'kerapihan_2' => 20,
                'kerapihan_3' => 20,
                'kerapihan_4' => 45,
                'kerapihan_5' => 25,
                'kerapihan_6' => 25,
                'kerapihan_7' => 30, // Revised option
                'kerapihan_8' => 40,
                'kerapihan_9' => 25, // Revised option
                'kerapihan_10' => 25, // Revised option
                'kerapihan_11' => 25, // Revised option
            ],
            'subtotals' => [
                'kekuatan' => 400,
                'kerapihan' => 300,
                'teknis' => 700,
                'waktu' => 300,
                'total' => 1000,
            ],
            'notes' => 'Tandu sangat kokoh dan rapi sempurna.',
        ];

        $respLktr = $this->post("/admin/competition-scores/{$catLktr->id}/grid-save", $payloadLktr);
        $this->assertNotEquals(500, $respLktr->getStatusCode(), 'POST grid-save returned 500 for LKTR');
        $respLktr->assertRedirect();

        $this->assertDatabaseHas('competition_scores', [
            'competition_category_id' => $catLktr->id,
            'competition_participant_team_id' => $teamLktr->id,
            'final_score' => 867,
        ]);

        // 2. PP Madya
        [$catPP, $teamPP] = $this->createCategoryAndTeam('PP-MADYA-TEST', 'Pertolongan Pertama', 'Madya');
        $respPP = $this->post("/admin/competition-scores/{$catPP->id}/grid-save", [
            'competition_participant_team_id' => $teamPP->id,
            'round_name' => 'Utama',
            'action_after' => 'stay',
            'time_recorded' => '05:00',
            'rubric' => [
                'item_1' => 10,
                'item_2' => 15,
            ],
            'subtotals' => ['total' => 25],
        ]);
        $this->assertNotEquals(500, $respPP->getStatusCode(), 'POST grid-save returned 500 for PP');
        $respPP->assertRedirect();

        // 3. PRS
        [$catPRS, $teamPRS] = $this->createCategoryAndTeam('PRS-WIRA-TEST', 'Pendidikan Remaja Sebaya', 'Wira');
        $respPRS = $this->post("/admin/competition-scores/{$catPRS->id}/grid-save", [
            'competition_participant_team_id' => $teamPRS->id,
            'round_name' => 'Utama',
            'action_after' => 'stay',
            'rubric' => ['prs_1' => 20, 'prs_2' => 30],
            'subtotals' => ['total' => 50],
        ]);
        $this->assertNotEquals(500, $respPRS->getStatusCode(), 'POST grid-save returned 500 for PRS');
        $respPRS->assertRedirect();

        // 4. Mewarnai
        [$catMewarnai, $teamMewarnai] = $this->createCategoryAndTeam('MEWARNAI-TEST', 'Lomba Mewarnai', 'Mula');
        $respMewarnai = $this->post("/admin/competition-scores/{$catMewarnai->id}/grid-save", [
            'competition_participant_team_id' => $teamMewarnai->id,
            'round_name' => 'Utama',
            'action_after' => 'stay',
            'rubric' => ['mewarnai_1' => 25, 'mewarnai_2' => 25],
            'subtotals' => ['total' => 50],
        ]);
        $this->assertNotEquals(500, $respMewarnai->getStatusCode(), 'POST grid-save returned 500 for Mewarnai');
        $respMewarnai->assertRedirect();
    }

    /**
     * Test GET Jury Portal Scoring for all categories (Ensure NO HTTP 500)
     */
    public function test_all_jury_portal_scoring_pages_render_without_500(): void
    {
        $this->actingAs($this->jury);

        $testCategories = [
            ['LKTR-MULA-J', 'Ketangkasan Tandu Reguler', 'Mula'],
            ['LKTR-MADYA-J', 'Ketangkasan Tandu Reguler', 'Madya'],
            ['LKTR-WIRA-J', 'Ketangkasan Tandu Reguler', 'Wira'],
            ['LPP-MADYA-J', 'Pertolongan Pertama', 'Madya'],
            ['PRS-WIRA-J', 'Pendidikan Remaja Sebaya', 'Wira'],
            ['MEWARNAI-MULA-J', 'Lomba Mewarnai', 'Mula'],
        ];

        foreach ($testCategories as [$code, $name, $level]) {
            [$category, $team] = $this->createCategoryAndTeam($code, $name, $level);

            $response = $this->get("/juri/kategori/{$category->id}");
            $this->assertNotEquals(500, $response->getStatusCode(), "GET /juri/kategori/{$category->id} returned 500 for {$name} ({$level})");
            $response->assertOk();
        }
    }

    /**
     * Test POST Jury Save Score for LKTR and other categories (Ensure NO HTTP 500)
     */
    public function test_jury_can_save_score_without_500(): void
    {
        $this->actingAs($this->jury);

        [$catLktr, $teamLktr] = $this->createCategoryAndTeam('LKTR-J-SAVE', 'Ketangkasan Tandu Reguler', 'Madya');

        $response = $this->post("/juri/kategori/{$catLktr->id}/regu/{$teamLktr->id}/simpan", [
            'minutes' => 2,
            'seconds' => 40,
            'final_score' => 1000,
            'rubric' => [
                'kekuatan_1' => 80,
                'kekuatan_2' => 80,
                'kekuatan_3a' => 60,
                'kekuatan_3b' => 100,
                'kekuatan_4' => 80,
                'kerapihan_1' => 20,
                'kerapihan_2' => 20,
                'kerapihan_3' => 20,
                'kerapihan_4' => 45,
                'kerapihan_5' => 25,
                'kerapihan_6' => 25,
                'kerapihan_7' => 30, // Revised option
                'kerapihan_8' => 40,
                'kerapihan_9' => 25, // Revised option
                'kerapihan_10' => 25, // Revised option
                'kerapihan_11' => 25, // Revised option
            ],
            'subtotals' => [
                'kekuatan' => 400,
                'kerapihan' => 300,
                'teknis' => 700,
                'waktu' => 300,
            ],
            'notes' => 'Penilaian juri lapangan selesai.',
        ]);

        $this->assertNotEquals(500, $response->getStatusCode(), 'POST /juri/kategori/.../simpan returned 500');
        $response->assertRedirect();

        $this->assertDatabaseHas('competition_scores', [
            'competition_category_id' => $catLktr->id,
            'competition_participant_team_id' => $teamLktr->id,
            'final_score' => 867,
            'time_recorded' => '02:40',
        ]);
    }

    /**
     * Test GET Grid Score when team ALREADY has existing scores in DB (Re-visiting / Editing)
     */
    public function test_all_grid_scoring_pages_render_when_already_graded(): void
    {
        $this->actingAs($this->admin);

        [$catLktr, $teamLktr] = $this->createCategoryAndTeam('LKTR-ALREADY-GRADED', 'Ketangkasan Tandu Reguler', 'Madya');

        // Simpan skor pertama
        $this->post("/admin/competition-scores/{$catLktr->id}/grid-save", [
            'competition_participant_team_id' => $teamLktr->id,
            'round_name' => 'Utama',
            'action_after' => 'stay',
            'time_recorded' => '02:40',
            'rubric' => [
                'kekuatan_1' => 80,
                'kekuatan_2' => 80,
                'kekuatan_3a' => 60,
                'kekuatan_3b' => 100,
                'kekuatan_4' => 80,
                'kerapihan_7' => 30,
                'kerapihan_9' => 25,
            ],
            'subtotals' => ['total' => 1000],
        ]);

        // Akses kembali halaman grid
        $response = $this->get("/admin/competition-scores/{$catLktr->id}/grid");
        $this->assertNotEquals(500, $response->getStatusCode(), 'Re-visiting already graded LKTR returned 500');
        $response->assertOk();

        // Akses dari sisi juri yang sudah menilai
        $this->actingAs($this->jury);
        $responseJury = $this->get("/juri/kategori/{$catLktr->id}");
        $this->assertNotEquals(500, $responseJury->getStatusCode(), 'Re-visiting already graded LKTR in jury portal returned 500');
        $responseJury->assertOk();
    }

    /**
     * Test LCT Prelim Save with EXACT data from user screenshot
     */
    public function test_lct_save_prelim_with_user_screenshot_data(): void
    {
        $this->actingAs($this->admin);

        [$catLct, $team1] = $this->createCategoryAndTeam('LCT-M-01', 'Lomba Cepat Tepat', 'Madya');
        $team1->update(['team_name' => 'SMPN 1 BEKASI Regu A', 'order_number' => 'A-002']);

        $teamsData = [
            ['name' => 'SMPN 1 DEPOK Regu A', 'order' => 'A-003', 'score' => 80, 'time' => '0720'],
            ['name' => 'SMPN 2 BOGOR Regu A', 'order' => 'A-004', 'score' => 77, 'time' => '07:54'],
            ['name' => 'SMPN 2 BOGOR Regu B', 'order' => 'A-005', 'score' => 78, 'time' => '07:54'],
            ['name' => 'SMP OGAH BANGUN Regu A', 'order' => 'A-01', 'score' => 99, 'time' => '07:54'],
            ['name' => 'SMP OGAH BANGUN Regu B', 'order' => 'B-01', 'score' => 100, 'time' => '07:54'],
        ];

        $payloadScores = [$team1->id => 90];
        $payloadTimes = [$team1->id => '07.30'];
        $createdTeams = [$team1->id => $team1];

        foreach ($teamsData as $td) {
            $reg = CompetitionRegistration::create([
                'competition_event_id' => $this->event->id,
                'school_name' => $td['name'],
                'registration_code' => 'REG-'.Str::random(6),
                'level' => 'Madya',
                'status' => 'verified',
                'advisor_name' => 'Pembina Test',
                'advisor_phone' => '08123456789',
            ]);
            $t = CompetitionParticipantTeam::create([
                'competition_registration_id' => $reg->id,
                'competition_category_id' => $catLct->id,
                'team_name' => $td['name'],
                'team_label' => 'Regu',
                'order_number' => $td['order'],
                'is_active' => true,
            ]);
            $payloadScores[$t->id] = $td['score'];
            $payloadTimes[$t->id] = $td['time'];
            $createdTeams[$t->id] = $t;
        }

        $payload = [
            'prelim_scores' => $payloadScores,
            'prelim_times' => $payloadTimes,
        ];

        $response = $this->post("/admin/competition-scores/{$catLct->id}/lct-save-prelim", $payload);
        $this->assertNotEquals(500, $response->getStatusCode(), 'LCT save prelim returned 500');
        $response->assertRedirect();

        // Verifikasi peringkat setelah disimpan
        $topScore = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('round_name', 'Penyisihan')
            ->where('rank', 1)
            ->first();
        $this->assertNotNull($topScore);
        $this->assertEquals(100, (float) $topScore->final_score, 'Juara 1 penyisihan harus tim dengan skor 100');

        $secondScore = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('round_name', 'Penyisihan')
            ->where('rank', 2)
            ->first();
        $this->assertNotNull($secondScore);
        $this->assertEquals(99, (float) $secondScore->final_score, 'Juara 2 penyisihan harus tim dengan skor 99');

        // Verifikasi normalisasi input waktu '0720' menjadi '07:20'
        $depokTeam = collect($createdTeams)->firstWhere('team_name', 'SMPN 1 DEPOK Regu A');
        $depokScore = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('competition_participant_team_id', $depokTeam->id)
            ->where('round_name', 'Penyisihan')
            ->first();
        $this->assertNotNull($depokScore);
        $this->assertEquals('07:20', $depokScore->time_recorded);
        $this->assertEquals(440, $depokScore->score_details['time_seconds'] ?? null);

        // Check GET LCT view
        $viewResponse = $this->get("/admin/competition-scores/{$catLct->id}/lct");
        $this->assertNotEquals(500, $viewResponse->getStatusCode(), 'LCT view after save returned 500');
        $viewResponse->assertOk();
        $viewResponse->assertSee('Lolos Semi-Final');

        // Test Generate Semifinal juga tidak 500
        $genResponse = $this->post("/admin/competition-scores/{$catLct->id}/lct-generate-semifinal");
        $this->assertNotEquals(500, $genResponse->getStatusCode(), 'Generate semifinal returned 500');
        $genResponse->assertRedirect();
    }

    /**
     * Test ranking diurutkan dari nilai tertinggi dan waktu tercepat (tie-breaker)
     */
    public function test_lct_ranking_sorted_strictly_by_highest_score_and_fastest_time(): void
    {
        $this->actingAs($this->admin);

        [$catLct, $t1] = $this->createCategoryAndTeam('LCT-RANK-TEST', 'Lomba Cepat Tepat', 'Madya');
        $t1->update(['team_name' => 'SMPN 1 BEKASI Regu A', 'order_number' => 'A-002']);

        $dataset = [
            ['name' => 'SMPN 1 DEPOK Regu A', 'order' => 'A-003', 'score' => 89, 'time' => '05:40'],
            ['name' => 'SMPN 2 BOGOR Regu A', 'order' => 'A-004', 'score' => 90, 'time' => '60:32'],
            ['name' => 'SMPN 2 BOGOR Regu B', 'order' => 'A-005', 'score' => 89, 'time' => '05:21'], // Sama-sama 89, tapi 05:21 lebih cepat dari 05:40
            ['name' => 'SMP OGAH BANGUN Regu A', 'order' => 'A-01', 'score' => 99, 'time' => '05.22'],
            ['name' => 'SMP OGAH BANGUN Regu B', 'order' => 'B-01', 'score' => 77, 'time' => '06.23'],
        ];

        $payloadScores = [$t1->id => 68];
        $payloadTimes = [$t1->id => '06:45'];
        $teamsMap = [$t1->id => $t1];

        foreach ($dataset as $d) {
            $reg = CompetitionRegistration::create([
                'competition_event_id' => $this->event->id,
                'school_name' => $d['name'],
                'registration_code' => 'REG-'.Str::random(6),
                'level' => 'Madya',
                'status' => 'verified',
                'advisor_name' => 'Pembina Test',
                'advisor_phone' => '08123456789',
            ]);
            $t = CompetitionParticipantTeam::create([
                'competition_registration_id' => $reg->id,
                'competition_category_id' => $catLct->id,
                'team_name' => $d['name'],
                'team_label' => 'Regu',
                'order_number' => $d['order'],
                'is_active' => true,
            ]);
            $payloadScores[$t->id] = $d['score'];
            $payloadTimes[$t->id] = $d['time'];
            $teamsMap[$t->id] = $t;
        }

        $resSave = $this->post("/admin/competition-scores/{$catLct->id}/lct-save-prelim", [
            'prelim_scores' => $payloadScores,
            'prelim_times' => $payloadTimes,
        ]);
        $this->assertNotEquals(500, $resSave->getStatusCode());
        $resSave->assertRedirect();

        // 1. Verifikasi Rank 1: SMP OGAH BANGUN Regu A (Nilai 99)
        $rank1 = CompetitionScore::where('competition_category_id', $catLct->id)->where('rank', 1)->first();
        $this->assertEquals(99, (float) $rank1->final_score);
        $this->assertEquals('SMP OGAH BANGUN Regu A', $rank1->team->team_name);

        // 2. Verifikasi Rank 2: SMPN 2 BOGOR Regu A (Nilai 90)
        $rank2 = CompetitionScore::where('competition_category_id', $catLct->id)->where('rank', 2)->first();
        $this->assertEquals(90, (float) $rank2->final_score);
        $this->assertEquals('SMPN 2 BOGOR Regu A', $rank2->team->team_name);

        // 3. Verifikasi Tie-Breaker Nilai 89:
        // SMPN 2 BOGOR Regu B (05:21 = 321 detik) HARUS Rank 3 karena lebih cepat dari SMPN 1 DEPOK (05:40 = 340 detik) yang Rank 4
        $rank3 = CompetitionScore::where('competition_category_id', $catLct->id)->where('rank', 3)->first();
        $rank4 = CompetitionScore::where('competition_category_id', $catLct->id)->where('rank', 4)->first();

        $this->assertEquals(89, (float) $rank3->final_score);
        $this->assertEquals('SMPN 2 BOGOR Regu B', $rank3->team->team_name, 'Waktu 05:21 harus Rank 3 karena lebih cepat');

        $this->assertEquals(89, (float) $rank4->final_score);
        $this->assertEquals('SMPN 1 DEPOK Regu A', $rank4->team->team_name, 'Waktu 05:40 harus Rank 4 karena lebih lambat');

        // 4. Verifikasi Rank 5: SMP OGAH BANGUN Regu B (Nilai 77)
        $rank5 = CompetitionScore::where('competition_category_id', $catLct->id)->where('rank', 5)->first();
        $this->assertEquals(77, (float) $rank5->final_score);

        // 5. Verifikasi Rank 6: SMPN 1 BEKASI Regu A (Nilai 68)
        $rank6 = CompetitionScore::where('competition_category_id', $catLct->id)->where('rank', 6)->first();
        $this->assertEquals(68, (float) $rank6->final_score);

        // 6. Verifikasi di tampilan view bahwa sortedPrelim terurut dari Rank 1 ke Rank 6
        $resView = $this->get("/admin/competition-scores/{$catLct->id}/lct");
        $resView->assertOk();
        $sortedTeams = $resView->viewData('sortedPrelim');
        $this->assertEquals(99, (float) $sortedTeams[0]->prelim_score, 'Urutan baris pertama harus tim dengan skor 99');
        $this->assertEquals(90, (float) $sortedTeams[1]->prelim_score, 'Urutan baris kedua harus tim dengan skor 90');
        $this->assertEquals(89, (float) $sortedTeams[2]->prelim_score, 'Urutan baris ketiga harus tim skor 89 (waktu 05:21)');
        $this->assertEquals('05:21', $sortedTeams[2]->prelim_time);
        $this->assertEquals(89, (float) $sortedTeams[3]->prelim_score, 'Urutan baris keempat harus tim skor 89 (waktu 05:40)');
        $this->assertEquals('05:40', $sortedTeams[3]->prelim_time);
        $this->assertEquals(77, (float) $sortedTeams[4]->prelim_score, 'Urutan baris kelima harus tim dengan skor 77');
        $this->assertEquals(68, (float) $sortedTeams[5]->prelim_score, 'Urutan baris keenam harus tim dengan skor 68');
    }

    /**
     * Test Semifinal: Total Nilai Soal Amplop + Rebutan + Balon, ranking tertinggi lolos ke final
     */
    public function test_lct_semifinal_ranking_and_headers(): void
    {
        $this->actingAs($this->admin);

        [$catLct, $t1] = $this->createCategoryAndTeam('LCT-SEMI-TEST', 'Lomba Cepat Tepat', 'Madya');
        $t1->update(['team_name' => 'SMAN 2 TAMBUN SELATAN Regu A']);

        $reg2 = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 CIAWI',
            'registration_code' => 'REG-'.Str::random(6),
            'level' => 'Madya',
            'status' => 'verified',
            'advisor_name' => 'Pembina Test',
            'advisor_phone' => '08123456789',
        ]);
        $t2 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg2->id,
            'competition_category_id' => $catLct->id,
            'team_name' => 'SMAN 1 CIAWI Regu A',
            'team_label' => 'Regu A',
            'order_number' => 2,
            'is_active' => true,
        ]);

        $reg3 = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 GARUT',
            'registration_code' => 'REG-'.Str::random(6),
            'level' => 'Madya',
            'status' => 'verified',
            'advisor_name' => 'Pembina Test',
            'advisor_phone' => '08123456789',
        ]);
        $t3 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg3->id,
            'competition_category_id' => $catLct->id,
            'team_name' => 'SMAN 1 GARUT Regu A',
            'team_label' => 'Regu A',
            'order_number' => 3,
            'is_active' => true,
        ]);

        // Simpan nilai Semifinal Termin 1 persis seperti data screenshot user
        $response = $this->post("/admin/competition-scores/{$catLct->id}/lct-save-semifinal", [
            'semi_termin' => [
                $t1->id => 1,
                $t2->id => 1,
                $t3->id => 1,
            ],
            'semi_amplop' => [
                $t1->id => 45,
                $t2->id => 56,
                $t3->id => 0,
            ],
            'semi_rebutan' => [
                $t1->id => 800,
                $t2->id => 700,
                $t3->id => 0,
            ],
            'semi_balon' => [
                $t1->id => 4,
                $t2->id => 2,
                $t3->id => 0,
            ],
        ]);
        $this->assertNotEquals(500, $response->getStatusCode());
        $response->assertRedirect();

        // Verifikasi di database: T1 (849) harus Rank 1, T2 (758) Rank 2, T3 (0) Rank 3
        $scoreT1 = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('competition_participant_team_id', $t1->id)
            ->where('round_name', 'Semi Final')
            ->first();
        $this->assertEquals(849, (float) $scoreT1->final_score);
        $this->assertEquals(1, $scoreT1->rank, 'SMAN 2 TAMBUN SELATAN (849) harus Rank 1');

        $scoreT2 = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('competition_participant_team_id', $t2->id)
            ->where('round_name', 'Semi Final')
            ->first();
        $this->assertEquals(758, (float) $scoreT2->final_score);
        $this->assertEquals(2, $scoreT2->rank, 'SMAN 1 CIAWI (758) harus Rank 2');

        // Verifikasi tampilan view Stage 2
        $resView = $this->get("/admin/competition-scores/{$catLct->id}/lct?stage=2");
        $resView->assertOk();
        $resView->assertSee('TOTAL NILAI SOAL AMPLOP');
        $resView->assertSee('TOTAL NILAI SOAL REBUTAN');
        $resView->assertSee('TOTAL NILAI BALON');

        // Cek bahwa T1 terurut sebagai urutan pertama di termin 1 dan berstatus lolos final
        $termin1Teams = $resView->viewData('termin1Teams');
        $this->assertEquals($t1->id, $termin1Teams[0]->id, 'Tim pertama di Termin 1 harus SMAN 2 TAMBUN SELATAN');
        $this->assertEquals(1, $termin1Teams[0]->termin_rank);
    }

    /**
     * Test Grand Final: Penentuan Peringkat Juara 1, 2, 3 murni dari total skor tertinggi
     * Sesuai data input user:
     * - SMAN 2 TAMBUN SELATAN: 700 + 700 + 600 = 2.000 -> Juara 1
     * - SMAN 1 CIAWI: 602 + 703 + 600 = 1.905 -> Juara 2
     * - SMAN 1 GARUT: 600 + 700 + 600 = 1.900 -> Juara 3
     */
    public function test_lct_grand_final_ranking_and_champion_determination_strictly_by_highest_score(): void
    {
        $this->actingAs($this->admin);

        [$catLct, $t1] = $this->createCategoryAndTeam('LCT-FINAL-TEST', 'Lomba Cepat Tepat', 'Wira');
        $t1->update(['team_name' => 'SMAN 1 GARUT Regu A']);

        $reg2 = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 1 CIAWI',
            'registration_code' => 'REG-'.Str::random(6),
            'level' => 'Wira',
            'status' => 'verified',
            'advisor_name' => 'Pembina Test',
            'advisor_phone' => '08123456789',
        ]);
        $t2 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg2->id,
            'competition_category_id' => $catLct->id,
            'team_name' => 'SMAN 1 CIAWI Regu A',
            'team_label' => 'Regu A',
            'order_number' => 2,
            'is_active' => true,
        ]);

        $reg3 = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMAN 2 TAMBUN SELATAN',
            'registration_code' => 'REG-'.Str::random(6),
            'level' => 'Wira',
            'status' => 'verified',
            'advisor_name' => 'Pembina Test',
            'advisor_phone' => '08123456789',
        ]);
        $t3 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg3->id,
            'competition_category_id' => $catLct->id,
            'team_name' => 'SMAN 2 TAMBUN SELATAN Regu A',
            'team_label' => 'Regu A',
            'order_number' => 3,
            'is_active' => true,
        ]);

        // Simpan nilai Grand Final
        $response = $this->post("/admin/competition-scores/{$catLct->id}/lct-save-final", [
            'final_amplop' => [
                $t1->id => 600,
                $t2->id => 602,
                $t3->id => 700,
            ],
            'final_rebutan' => [
                $t1->id => 700,
                $t2->id => 703,
                $t3->id => 700,
            ],
            'final_taruh' => [
                $t1->id => 600,
                $t2->id => 600,
                $t3->id => 600,
            ],
        ]);

        $this->assertNotEquals(500, $response->getStatusCode());
        $response->assertRedirect();

        // 1. Verifikasi Database: T3 (2.000) adalah Juara 1
        $scoreT3 = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('competition_participant_team_id', $t3->id)
            ->where('round_name', 'Final')
            ->first();
        $this->assertNotNull($scoreT3);
        $this->assertEquals(2000, (float) $scoreT3->final_score);
        $this->assertEquals(1, $scoreT3->rank, 'SMAN 2 TAMBUN SELATAN (2.000) harus Juara 1');

        // 2. Verifikasi Database: T2 (1.905) adalah Juara 2
        $scoreT2 = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('competition_participant_team_id', $t2->id)
            ->where('round_name', 'Final')
            ->first();
        $this->assertNotNull($scoreT2);
        $this->assertEquals(1905, (float) $scoreT2->final_score);
        $this->assertEquals(2, $scoreT2->rank, 'SMAN 1 CIAWI (1.905) harus Juara 2');

        // 3. Verifikasi Database: T1 (1.900) adalah Juara 3
        $scoreT1 = CompetitionScore::where('competition_category_id', $catLct->id)
            ->where('competition_participant_team_id', $t1->id)
            ->where('round_name', 'Final')
            ->first();
        $this->assertNotNull($scoreT1);
        $this->assertEquals(1900, (float) $scoreT1->final_score);
        $this->assertEquals(3, $scoreT1->rank, 'SMAN 1 GARUT (1.900) harus Juara 3');

        // 4. Verifikasi Tampilan View Stage 3
        $resView = $this->get("/admin/competition-scores/{$catLct->id}/lct?stage=3");
        $resView->assertOk();
        $resView->assertSee('TOTAL NILAI SOAL AMPLOP');
        $resView->assertSee('TOTAL NILAI REBUTAN BERTINGKAT');
        $resView->assertSee('TOTAL NILAI TARUH NILAI');
        $resView->assertSee('🥇 Juara 1');
        $resView->assertSee('🥈 Juara 2');
        $resView->assertSee('🥉 Juara 3');

        $finalGrandTeams = $resView->viewData('finalGrandTeams');
        $this->assertCount(3, $finalGrandTeams);
        $this->assertEquals($t3->id, $finalGrandTeams[0]->id, 'Regu baris pertama harus SMAN 2 TAMBUN SELATAN');
        $this->assertEquals(2000, (float) $finalGrandTeams[0]->total_score);
        $this->assertEquals(1, $finalGrandTeams[0]->grand_rank);

        $this->assertEquals($t2->id, $finalGrandTeams[1]->id, 'Regu baris kedua harus SMAN 1 CIAWI');
        $this->assertEquals(1905, (float) $finalGrandTeams[1]->total_score);
        $this->assertEquals(2, $finalGrandTeams[1]->grand_rank);

        $this->assertEquals($t1->id, $finalGrandTeams[2]->id, 'Regu baris ketiga harus SMAN 1 GARUT');
        $this->assertEquals(1900, (float) $finalGrandTeams[2]->total_score);
        $this->assertEquals(3, $finalGrandTeams[2]->grand_rank);
    }
}

<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\User;
use App\Services\CompetitionSpreadsheetService;
use DOMDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use ZipArchive;

class CompetitionLeaderboardExportTest extends TestCase
{
    use RefreshDatabase;

    private function setupTestData(): array
    {
        // 1. Admin User
        $admin = User::create([
            'name' => 'Administrator PMR',
            'username' => 'admin',
            'email' => 'admin@pmrwirasmanic.or.id',
            'role' => 'admin',
            'password' => Hash::make('pmrwirasmanic'),
        ]);

        // 2. Event & Categories
        $event = CompetitionEvent::create([
            'name' => 'Sua Bhakti Berkarya IV',
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sua-bhakti-berkarya-iv',
            'start_date' => '2026-10-24',
            'end_date' => '2026-10-25',
            'is_active' => true,
        ]);

        $cat1 = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama (Putra)',
            'code' => 'PP-PA',
            'level' => 'Madya',
            'point_tier' => 'tier_1',
            'is_active' => true,
        ]);

        $cat2 = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Ketangkasan Tandu Darurat',
            'code' => 'TDU-PA',
            'level' => 'Madya',
            'point_tier' => 'tier_2',
            'is_active' => true,
        ]);

        // 3. Sekolah Registrasi
        $reg1 = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-2026-MDY-001',
            'school_name' => 'SMP Negeri 1 Ciawi',
            'level' => 'Madya',
            'advisor_name' => 'Budi Santoso',
            'advisor_phone' => '08123456789',
            'status' => 'verified',
        ]);

        $reg2 = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-2026-MDY-002',
            'school_name' => 'MTs Negeri 1 Bogor',
            'level' => 'Madya',
            'advisor_name' => 'Ahmad Dahlan',
            'advisor_phone' => '08129876543',
            'status' => 'verified',
        ]);

        // 4. Tim Peserta
        $team1A = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg1->id,
            'competition_category_id' => $cat1->id,
            'order_number' => 'A-01',
            'team_name' => 'Regu Garuda A',
            'is_active' => true,
        ]);

        $team1B = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg1->id,
            'competition_category_id' => $cat2->id,
            'order_number' => 'A-02',
            'team_name' => 'Regu Tandu Garuda',
            'is_active' => true,
        ]);

        $team2A = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg2->id,
            'competition_category_id' => $cat1->id,
            'order_number' => 'B-01',
            'team_name' => 'Regu Rajawali A',
            'is_active' => true,
        ]);

        // 5. Scores (SMPN 1 Ciawi Juara 1 PP dan Juara 1 Tandu -> 12 + 10 = 22 pts)
        CompetitionScore::create([
            'competition_category_id' => $cat1->id,
            'competition_participant_team_id' => $team1A->id,
            'round_name' => 'Utama',
            'final_score' => 95.50,
            'rank' => 1,
            'is_disqualified' => false,
        ]);

        CompetitionScore::create([
            'competition_category_id' => $cat2->id,
            'competition_participant_team_id' => $team1B->id,
            'round_name' => 'Utama',
            'final_score' => 98.00,
            'rank' => 1,
            'is_disqualified' => false,
        ]);

        // MTsN 1 Bogor Juara 2 PP -> 10 pts
        CompetitionScore::create([
            'competition_category_id' => $cat1->id,
            'competition_participant_team_id' => $team2A->id,
            'round_name' => 'Utama',
            'final_score' => 91.00,
            'rank' => 2,
            'is_disqualified' => false,
        ]);

        return [$admin, $event, $cat1, $cat2, $reg1, $reg2];
    }

    public function test_admin_can_download_leaderboard_standings_excel(): void
    {
        [$admin, $event] = $this->setupTestData();

        $response = $this->actingAs($admin)
            ->get(route('admin.competition-leaderboard.export-excel', [
                'event_id' => $event->id,
                'level' => 'Madya',
            ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Rekapitulasi_Klasemen_Juara_Umum_madya_', $contentDisposition);
        $this->assertStringContainsString('.xlsx', $contentDisposition);
    }

    public function test_leaderboard_service_generates_valid_openxml_and_matches_mockup(): void
    {
        [$admin, $event] = $this->setupTestData();

        $categories = CompetitionCategory::where('competition_event_id', $event->id)->get();
        $standings = collect([
            [
                'rank' => 1,
                'school_name' => 'SMP Negeri 1 Ciawi',
                'categories' => [$categories[0]->id => 12, $categories[1]->id => 10],
                'gold_count' => 2,
                'silver_count' => 0,
                'bronze_count' => 0,
                'total_points' => 22,
            ],
            [
                'rank' => 2,
                'school_name' => 'MTs Negeri 1 Bogor',
                'categories' => [$categories[0]->id => 10],
                'gold_count' => 0,
                'silver_count' => 1,
                'bronze_count' => 0,
                'total_points' => 10,
            ],
        ]);

        $service = app(CompetitionSpreadsheetService::class);
        $tempXlsx = $service->exportLeaderboardStandingsXlsx($event, 'Madya', $categories, $standings);

        $this->assertFileExists($tempXlsx);

        $zip = new ZipArchive;
        $openResult = $zip->open($tempXlsx);
        $this->assertTrue($openResult === true, 'Hasil export harus ZIP OpenXML valid');

        // Pastikan ada 3 sheet
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet2.xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet3.xml'));
        $this->assertNotFalse($zip->locateName('xl/styles.xml'));
        $this->assertNotFalse($zip->locateName('xl/workbook.xml'));

        // Validasi keutuhan XML dengan DOMDocument (tidak boleh ada parse error / entity error)
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $xmlContent = $zip->getFromName($name);
            if (str_ends_with($name, '.xml') || str_ends_with($name, '.rels')) {
                $dom = new DOMDocument;
                $loaded = @$dom->loadXML($xmlContent);
                $this->assertTrue($loaded, "File XML {$name} harus dapat diparse tanpa error");
            }
        }

        $sheet1Xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sheet2Xml = $zip->getFromName('xl/worksheets/sheet2.xml');
        $sheet3Xml = $zip->getFromName('xl/worksheets/sheet3.xml');
        $workbookXml = $zip->getFromName('xl/workbook.xml');

        $zip->close();
        @unlink($tempXlsx);

        // Verifikasi 3 tab sheet:
        $this->assertStringContainsString('Klasemen Juara Umum', $workbookXml);
        $this->assertStringContainsString('Distribusi Medali Lomba', $workbookXml);
        $this->assertStringContainsString('Daftar Kontingen Sekolah', $workbookXml);

        // Verifikasi konten Sheet 1 sesuai mockup:
        // Kop
        $this->assertStringContainsString('REKAPITULASI POIN &amp; KLASEMEN AKHIR JUARA UMUM', $sheet1Xml);
        $this->assertStringContainsString('PALANG MERAH INDONESIA', $sheet1Xml);
        $this->assertStringContainsString('OFFICIAL LEADERBOARD SHEET', $sheet1Xml);

        // Podium Cards
        $this->assertStringContainsString('JUARA UMUM I', $sheet1Xml);
        $this->assertStringContainsString('SMP Negeri 1 Ciawi', $sheet1Xml);

        // Headers Tabel
        $this->assertStringContainsString('RANK', $sheet1Xml);
        $this->assertStringContainsString('NAMA SEKOLAH / PANGKALAN', $sheet1Xml);
        $this->assertStringContainsString('TOTAL POIN', $sheet1Xml);
        $this->assertStringContainsString('STATUS PRESTASI', $sheet1Xml);

        // Data Sekolah & Total Poin
        $this->assertStringContainsString('SMP Negeri 1 Ciawi', $sheet1Xml);
        $this->assertStringContainsString('22', $sheet1Xml);
        $this->assertStringContainsString('MTs Negeri 1 Bogor', $sheet1Xml);
        $this->assertStringContainsString('10', $sheet1Xml);

        // Statistik Cards
        $this->assertStringContainsString('TOTAL PANGKALAN:', $sheet1Xml);
        $this->assertStringContainsString('TOTAL MEDALI DIPEREBUTKAN:', $sheet1Xml);

        // 4 Kolom TTD
        $this->assertStringContainsString('Koordinator Rekapitulasi &amp; Skoring,', $sheet1Xml);
        $this->assertStringContainsString('Dewan Juri Kehormatan,', $sheet1Xml);
        $this->assertStringContainsString('Pembina PMR Wira SMAN 1 Ciawi,', $sheet1Xml);
        $this->assertStringContainsString('Ketua Panitia Pelaksana,', $sheet1Xml);

        // Sheet 2: Distribusi Medali
        $this->assertStringContainsString('DISTRIBUSI PEROLEHAN MEDALI PER CABANG LOMBA', $sheet2Xml);

        // Sheet 3: Daftar Sekolah
        $this->assertStringContainsString('DAFTAR PANGKALAN SEKOLAH &amp; PEROLEHAN AKHIR', $sheet3Xml);
    }

    public function test_export_button_is_rendered_in_leaderboard_view(): void
    {
        [$admin] = $this->setupTestData();

        $response = $this->actingAs($admin)
            ->get(route('admin.competition-leaderboard.index', ['level' => 'Madya']));

        $response->assertStatus(200);
        $response->assertSee('Export Excel');
        $response->assertSee(route('admin.competition-leaderboard.export-excel', ['level' => 'Madya']));
    }
}

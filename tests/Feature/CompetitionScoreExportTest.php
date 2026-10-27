<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\User;
use App\Services\CompetitionSpreadsheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use ZipArchive;

class CompetitionScoreExportTest extends TestCase
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

        // 2. Dewan Juri
        $jury1 = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri1@pmrwirasmanic.or.id',
            'role' => 'juri',
            'password' => Hash::make('password123'),
        ]);

        $jury2 = User::create([
            'name' => 'Kak Siti Rahmawati, S.Pd',
            'username' => 'juri_tandu2',
            'email' => 'juri2@pmrwirasmanic.or.id',
            'role' => 'juri',
            'password' => Hash::make('password123'),
        ]);

        // 3. Event & Category
        $event = CompetitionEvent::create([
            'name' => 'SUA BHAKTI BERKARYA IV',
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'start_date' => '2026-10-24',
            'is_active' => true,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LPP-Madya-PA',
            'name' => 'Pertolongan Pertama',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'lpp',
            'order_position' => 1,
            'is_active' => true,
        ]);

        // Hubungkan Dewan Juri ke Kategori Lomba
        $category->assignedJuries()->attach([$jury1->id, $jury2->id]);

        // 4. Registrasi & Tim Peserta
        $reg1 = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-2026-MDY-004',
            'school_name' => 'SMP Negeri 1 Ciawi',
            'level' => 'Madya',
            'advisor_name' => 'Budi Santoso',
            'advisor_phone' => '08123456789',
            'status' => 'verified',
        ]);

        $reg2 = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-2026-MDY-012',
            'school_name' => 'MTs Negeri 1 Bogor',
            'level' => 'Madya',
            'advisor_name' => 'Ahmad Dahlan',
            'advisor_phone' => '08129876543',
            'status' => 'verified',
        ]);

        $team1 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg1->id,
            'competition_category_id' => $category->id,
            'order_number' => 'A-01',
            'team_name' => 'Regu Garuda Madya A',
            'is_active' => true,
        ]);

        $team2 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg2->id,
            'competition_category_id' => $category->id,
            'order_number' => 'B-01',
            'team_name' => 'Regu Rajawali Putera',
            'is_active' => true,
        ]);

        // 5. Input Nilai & Peringkat
        CompetitionScore::create([
            'competition_category_id' => $category->id,
            'competition_participant_team_id' => $team1->id,
            'jury_user_id' => $jury1->id,
            'round_name' => 'Utama',
            'score_details' => [
                'practical_score' => 95.50,
                'time_score' => 300.0,
            ],
            'time_recorded' => '03:45',
            'final_score' => 95.50,
            'rank' => 1,
            'notes' => 'Teknik balut bidai sangat rapi dan solid',
        ]);

        CompetitionScore::create([
            'competition_category_id' => $category->id,
            'competition_participant_team_id' => $team2->id,
            'jury_user_id' => $jury2->id,
            'round_name' => 'Utama',
            'score_details' => [
                'practical_score' => 92.00,
                'time_score' => 280.0,
            ],
            'time_recorded' => '04:10',
            'final_score' => 92.00,
            'rank' => 2,
            'notes' => 'Evakuasi tepat waktu',
        ]);

        return [$admin, $category, $team1, $team2, $jury1, $jury2];
    }

    public function test_admin_can_download_official_score_sheet_excel(): void
    {
        [$admin, $category] = $this->setupTestData();

        $response = $this->actingAs($admin)
            ->get(route('admin.competition-scores.export-excel', [
                'category' => $category->id,
                'round' => 'Utama',
            ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Pastikan nama file header Content-Disposition valid
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Lembar_Penilaian_', $contentDisposition);
        $this->assertStringContainsString('.xlsx', $contentDisposition);
    }

    public function test_score_sheet_service_generates_valid_openxml_with_all_elements(): void
    {
        [$admin, $category, $team1, $team2, $jury1, $jury2] = $this->setupTestData();

        $service = app(CompetitionSpreadsheetService::class);
        $tempXlsx = $service->exportCategoryScoreSheetXlsx($category, 'Utama');

        $this->assertFileExists($tempXlsx);

        // Buka file zip XLSX untuk memeriksa OpenXML internal
        $zip = new ZipArchive;
        $openResult = $zip->open($tempXlsx);
        $this->assertTrue($openResult === true, 'File hasil export harus merupakan ZIP OpenXML yang valid');

        // Periksa keberadaan file XML wajib
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));
        $this->assertNotFalse($zip->locateName('xl/styles.xml'));
        $this->assertNotFalse($zip->locateName('xl/workbook.xml'));

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $stylesXml = $zip->getFromName('xl/styles.xml');

        $zip->close();
        @unlink($tempXlsx);

        // Verifikasi elemen visual mockup dalam XML:
        // 1. Kop dan Judul
        $this->assertStringContainsString('LEMBAR PENILAIAN RESMI DEWAN JURI LAPANGAN', $sheetXml);
        $this->assertStringContainsString('PALANG MERAH INDONESIA', $sheetXml);

        // 2. Metadata Cabang & Dewan Juri
        $this->assertStringContainsString('Pertolongan Pertama', $sheetXml);
        $this->assertStringContainsString('Kak Hendra Kusuma', $sheetXml);
        $this->assertStringContainsString('Kak Siti Rahmawati', $sheetXml);
        $this->assertStringContainsString('@juri_tandu1', $sheetXml);
        $this->assertStringContainsString('@juri_tandu2', $sheetXml);

        // 3. Header Kolom
        $this->assertStringContainsString('NO. URUT', $sheetXml);
        $this->assertStringContainsString('KODE REG', $sheetXml);
        $this->assertStringContainsString('ASAL SEKOLAH / PANGKALAN', $sheetXml);
        $this->assertStringContainsString('NAMA REGU / KONTINGEN', $sheetXml);
        $this->assertStringContainsString('NILAI PRAKTIK', $sheetXml);
        $this->assertStringContainsString('POIN WAKTU', $sheetXml);
        $this->assertStringContainsString('NILAI AKHIR', $sheetXml);
        $this->assertStringContainsString('PERINGKAT', $sheetXml);
        $this->assertStringContainsString('JURI PENILAI', $sheetXml);

        // 4. Data Tim & Peringkat Juara
        $this->assertStringContainsString('A-01', $sheetXml);
        $this->assertStringContainsString('SMP Negeri 1 Ciawi', $sheetXml);
        $this->assertStringContainsString('Regu Garuda Madya A', $sheetXml);
        $this->assertStringContainsString('95.50', $sheetXml);
        $this->assertStringContainsString('JUARA 1', $sheetXml);

        $this->assertStringContainsString('B-01', $sheetXml);
        $this->assertStringContainsString('MTs Negeri 1 Bogor', $sheetXml);
        $this->assertStringContainsString('Regu Rajawali Putera', $sheetXml);
        $this->assertStringContainsString('92.00', $sheetXml);
        $this->assertStringContainsString('JUARA 2', $sheetXml);

        // 5. Ringkasan Statistik
        $this->assertStringContainsString('TOTAL PESERTA TAMPIL', $sheetXml);
        $this->assertStringContainsString('RATA-RATA NILAI AKHIR', $sheetXml);
        $this->assertStringContainsString('NILAI TERTINGGI', $sheetXml);

        // 6. 4 Kolom Tanda Tangan Pengesahan
        $this->assertStringContainsString('Dewan Juri I,', $sheetXml);
        $this->assertStringContainsString('Dewan Juri II,', $sheetXml);
        $this->assertStringContainsString('Koordinator Penjurian,', $sheetXml);
        $this->assertStringContainsString('Ketua Panitia Pelaksana,', $sheetXml);
        $this->assertStringContainsString('Drs. H. Mulyadi, M.Pd', $sheetXml);
        $this->assertStringContainsString('Muhammad Dwi Pantara', $sheetXml);

        // 7. Styling Hijau Khas Excel (#107C41)
        $this->assertStringContainsString('107C41', $stylesXml);
    }

    public function test_export_button_is_rendered_in_views(): void
    {
        [$admin, $category] = $this->setupTestData();

        // Cek di halaman input penilaian
        $responseInput = $this->actingAs($admin)
            ->get(route('admin.competition-scores.input', $category->id));

        $responseInput->assertStatus(200);
        $responseInput->assertSee('Export Excel');
        $responseInput->assertSee(route('admin.competition-scores.export-excel', ['category' => $category->id, 'round' => 'Utama']));

        // Cek di halaman index penilaian
        $responseIndex = $this->actingAs($admin)
            ->get(route('admin.competition-scores.index', ['level' => 'Madya']));

        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Export Excel');
        $responseIndex->assertSee(route('admin.competition-scores.export-excel', $category->id));
    }
}

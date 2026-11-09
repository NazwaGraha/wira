<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected CompetitionEvent $event;

    protected CompetitionCategory $categoryWira;

    protected CompetitionCategory $categoryMadya;

    protected CompetitionRegistration $registrationWira;

    protected CompetitionRegistration $registrationMadya;

    protected CompetitionParticipantTeam $teamWira;

    protected CompetitionParticipantTeam $teamMadya;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user admin
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Buat event lomba aktif
        $this->event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(12),
        ]);

        // Buat Kategori Wira & Madya
        $this->categoryWira = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'name' => 'Pertolongan Pertama Wira',
            'code' => 'PP-WIRA',
            'level' => 'Wira',
            'gender_category' => 'Campuran',
            'order_position' => 1,
        ]);

        $this->categoryMadya = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'name' => 'Tandu Darurat Madya',
            'code' => 'TD-MADYA',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'order_position' => 2,
        ]);

        // Registrasi Sekolah Wira
        $this->registrationWira = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'registration_code' => 'REG-WIRA-001',
            'school_name' => 'SMAN 1 Megamendung',
            'level' => 'Wira',
            'advisor_name' => 'Drs. Bambang Sudarsono',
            'advisor_phone' => '081234567890',
            'status' => 'verified',
            'is_checked_in' => true,
            'checked_in_at' => now(),
        ]);

        // Registrasi Sekolah Madya
        $this->registrationMadya = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'registration_code' => 'REG-MADYA-002',
            'school_name' => 'SMPN 2 Ciawi',
            'level' => 'Madya',
            'advisor_name' => 'Ibu Siti Aminah',
            'advisor_phone' => '089876543210',
            'status' => 'verified',
            'is_checked_in' => false,
        ]);

        // Buat Tim Peserta Wira dengan daftar nama anggota
        $this->teamWira = CompetitionParticipantTeam::create([
            'competition_registration_id' => $this->registrationWira->id,
            'competition_category_id' => $this->categoryWira->id,
            'order_number' => 'PP-W-01',
            'team_name' => 'Tim Garuda Wira',
            'team_label' => 'A',
            'is_active' => true,
            'members_list' => [
                ['name' => 'Arya Pratama', 'role' => 'Ketua Regu'],
                ['name' => 'Siti Rahmawati', 'role' => 'Anggota'],
                ['name' => 'Budi Santoso', 'role' => 'Anggota'],
            ],
        ]);

        // Buat Tim Peserta Madya
        $this->teamMadya = CompetitionParticipantTeam::create([
            'competition_registration_id' => $this->registrationMadya->id,
            'competition_category_id' => $this->categoryMadya->id,
            'order_number' => 'TD-M-01',
            'team_name' => 'Regu Melati Madya',
            'team_label' => 'A',
            'is_active' => true,
            'members_list' => [
                ['name' => 'Fajar Sidik', 'role' => 'Ketua Regu'],
                ['name' => 'Lestari Indah', 'role' => 'Anggota'],
            ],
        ]);
    }

    public function test_authenticated_admin_can_access_competition_reports_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.competition-reports.participants'));

        $response->assertStatus(200);
        $response->assertSee('Laporan & Rekap Peserta Lomba', false);
        $response->assertSee('PMR Mula');
        $response->assertSee('PMR Madya');
        $response->assertSee('PMR Wira');
        $response->assertSee('SMAN 1 Megamendung');
        $response->assertSee('SMPN 2 Ciawi');
        $response->assertSee('Arya Pratama');
        $response->assertSee('Fajar Sidik');
    }

    public function test_admin_can_filter_competition_reports_by_level(): void
    {
        // Filter khusus tingkat Wira
        $response = $this->actingAs($this->admin)
            ->get(route('admin.competition-reports.participants', ['level' => 'Wira']));

        $response->assertStatus(200);
        $response->assertSee('SMAN 1 Megamendung');
        $response->assertSee('Arya Pratama');
        $response->assertDontSee('SMPN 2 Ciawi');
        $response->assertDontSee('Fajar Sidik');
    }

    public function test_admin_can_filter_competition_reports_by_category(): void
    {
        // Filter khusus kategori Madya
        $response = $this->actingAs($this->admin)
            ->get(route('admin.competition-reports.participants', ['category_id' => $this->categoryMadya->id]));

        $response->assertStatus(200);
        $response->assertSee('SMPN 2 Ciawi');
        $response->assertSee('Regu Melati Madya');
        $response->assertDontSee('SMAN 1 Megamendung');
    }

    public function test_admin_can_export_competition_reports_to_excel(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.competition-reports.export-excel', ['level' => 'Wira']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Periksa isi CSV
        $content = $response->streamedContent();
        $this->assertStringContainsString('BUKU INDUK & DAFTAR PESERTA LOMBA', $content);
        $this->assertStringContainsString('SMAN 1 Megamendung', $content);
        $this->assertStringContainsString('Arya Pratama', $content);
    }

    public function test_admin_can_view_print_pdf_sheet(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.competition-reports.print-pdf', ['level' => 'Wira']));

        $response->assertStatus(200);
        $response->assertSee('logo_kop_laporan_pmr.png');
        $response->assertSee('Sekretariat: Jl. Veteran III Banjarwaru');
        $response->assertSee('BUKU INDUK & DAFTAR PESERTA LOMBA', false);
        $response->assertSee('SMAN 1 Megamendung');
        $response->assertSee('Arya Pratama');
        $response->assertSee('Drs. H. Mulyadi, M.Pd');
    }
}

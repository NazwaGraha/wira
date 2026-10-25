<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Services\CompetitionSpreadsheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompetitionParticipantUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_and_parse_participant_template(): void
    {
        $service = new CompetitionSpreadsheetService;
        $templatePath = $service->downloadTemplate();

        $this->assertFileExists($templatePath);
        $this->assertGreaterThan(0, filesize($templatePath));

        // Parse template
        $parsed = $service->parseParticipantFile($templatePath);

        $this->assertNotEmpty($parsed);
        $this->assertCount(6, $parsed);
        $this->assertEquals('Ahmad Fauzi Pratama', $parsed[0]['name']);
        $this->assertEquals('L', $parsed[0]['gender']);
        $this->assertEquals('VIII-A', $parsed[0]['class_grade']);
        $this->assertEquals('Ketua Regu', $parsed[0]['role']);

        @unlink($templatePath);
    }

    public function test_public_can_download_participant_excel_template(): void
    {
        $response = $this->get(route('lomba.template-peserta'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Template_Daftar_Peserta_Regu_PMR.xlsx');
    }

    public function test_can_parse_uploaded_excel_file_via_ajax(): void
    {
        $service = new CompetitionSpreadsheetService;
        $templatePath = $service->downloadTemplate();
        $fileContent = file_get_contents($templatePath);
        @unlink($templatePath);

        $uploadedFile = UploadedFile::fake()->createWithContent('peserta_regu_a.xlsx', $fileContent);

        $response = $this->postJson(route('lomba.parse-excel-peserta'), [
            'excel_file' => $uploadedFile,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 6,
            ]);
    }

    public function test_store_registration_saves_members_from_uploaded_excel(): void
    {
        Storage::fake('public');

        $event = CompetitionEvent::create([
            'title' => 'SUA BHAKTI BERKARYA 2026',
            'slug' => 'sua-bhakti-berkarya-2026',
            'is_active' => true,
            'is_registration_open' => true,
            'registration_fee' => 75000,
            'is_waves_enabled' => false,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'max_teams_per_school' => 2,
            'is_active' => true,
            'order_position' => 1,
        ]);

        $service = new CompetitionSpreadsheetService;
        $templatePath = $service->downloadTemplate();
        $fileContent = file_get_contents($templatePath);
        @unlink($templatePath);

        $excelFile = UploadedFile::fake()->createWithContent('peserta_pp_putra.xlsx', $fileContent);
        $proofFile = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->post(route('lomba.store'), [
            'school_name' => 'SMPN 1 CIAWI',
            'level' => 'Madya',
            'advisor_name' => 'Drs. Supardi',
            'advisor_phone' => '081234567890',
            'advisor_email' => 'pembina@smpn1ciawi.sch.id',
            'categories' => [$category->id],
            'team_labels' => [
                $category->id => ['Regu Putra A'],
            ],
            'team_files' => [
                $category->id => [$excelFile],
            ],
            'payment_proof' => $proofFile,
        ]);

        $response->assertRedirect();

        $registration = CompetitionRegistration::where('school_name', 'SMPN 1 CIAWI')->first();
        $this->assertNotNull($registration);

        $team = CompetitionParticipantTeam::where('competition_registration_id', $registration->id)->first();
        $this->assertNotNull($team);
        $this->assertNotEmpty($team->members_list);
        $this->assertCount(6, $team->members_list);
        $this->assertEquals('Ahmad Fauzi Pratama', $team->members_list[0]['name']);
        $this->assertNotNull($team->members_file);
    }
}

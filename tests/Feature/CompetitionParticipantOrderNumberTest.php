<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionParticipantOrderNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_1_generates_separated_sessions_for_regu_a_and_regu_b(): void
    {
        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya',
            'slug' => 'sbb-test-1',
            'year' => 2026,
            'is_active' => true,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LPP-MULA-PA',
            'name' => 'Pertolongan Pertama',
            'level' => 'Mula',
            'gender_category' => 'Putra',
            'is_active' => true,
        ]);

        // Sekolah 1 mendaftar Regu A dan Regu B
        $reg1 = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-TEST01',
            'school_name' => 'SDN CIPAYUNG 01',
            'level' => 'Mula',
            'advisor_name' => 'Pembina 1',
            'advisor_phone' => '08123456789',
            'status' => 'verified',
        ]);

        $orderA1 = $category->generateNextOrderNumber(0, 'Regu A');
        $this->assertEquals('A-01', $orderA1);

        $team1A = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg1->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SDN CIPAYUNG 01 Regu A',
            'team_label' => 'Regu A',
            'order_number' => $orderA1,
        ]);

        $orderB1 = $category->generateNextOrderNumber(1, 'Regu B');
        $this->assertEquals('B-01', $orderB1);

        $team1B = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg1->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SDN CIPAYUNG 01 Regu B',
            'team_label' => 'Regu B',
            'order_number' => $orderB1,
        ]);

        // Sekolah 2 mendaftar Regu A dan Regu B
        $reg2 = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-TEST02',
            'school_name' => 'SDN CIPAYUNG 02',
            'level' => 'Mula',
            'advisor_name' => 'Pembina 2',
            'advisor_phone' => '08123456780',
            'status' => 'verified',
        ]);

        $orderA2 = $category->generateNextOrderNumber(0, 'Regu A');
        $this->assertEquals('A-02', $orderA2);

        $team2A = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg2->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SDN CIPAYUNG 02 Regu A',
            'team_label' => 'Regu A',
            'order_number' => $orderA2,
        ]);

        $orderB2 = $category->generateNextOrderNumber(1, 'Regu B');
        $this->assertEquals('B-02', $orderB2);

        $team2B = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg2->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SDN CIPAYUNG 02 Regu B',
            'team_label' => 'Regu B',
            'order_number' => $orderB2,
        ]);

        // Sekolah 3 hanya mendaftar Regu A (1 regu saja)
        $orderA3 = $category->generateNextOrderNumber(0, 'Regu A');
        $this->assertEquals('A-03', $orderA3);

        // Verifikasi format full order code
        $this->assertEquals('LPP-MULA-PA-A01', $team1A->full_order_code);
        $this->assertEquals('LPP-MULA-PA-B01', $team1B->full_order_code);
        $this->assertEquals('LPP-MULA-PA-A02', $team2A->full_order_code);
        $this->assertEquals('LPP-MULA-PA-B02', $team2B->full_order_code);
    }

    public function test_participant_card_and_status_display_order_number_and_members(): void
    {
        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya',
            'slug' => 'sbb-test-2',
            'year' => 2026,
            'is_active' => true,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LPP-MULA-PA',
            'name' => 'Pertolongan Pertama',
            'level' => 'Mula',
            'gender_category' => 'Putra',
            'is_active' => true,
        ]);

        $reg = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-TESTCARD',
            'school_name' => 'SD NEGERI CONTOH',
            'level' => 'Mula',
            'advisor_name' => 'Ibu Guru Ani',
            'advisor_phone' => '08123456781',
            'status' => 'verified',
        ]);

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SD NEGERI CONTOH Regu A',
            'team_label' => 'Regu A',
            'order_number' => 'A-01',
            'members_list' => [
                ['name' => 'Ahmad Fauzi', 'role' => 'Ketua Regu', 'nisn' => '00812345'],
                ['name' => 'Dimas Arya', 'role' => 'Anggota', 'nisn' => '00812346'],
            ],
        ]);

        $responseCard = $this->get('/lomba/kartu-peserta/'.$reg->registration_code);
        $responseCard->assertStatus(200);
        $responseCard->assertSee('A-01');
        $responseCard->assertSee('LPP-MULA-PA-A01');
        $responseCard->assertSee('Ahmad Fauzi');
        $responseCard->assertSee('Ketua Regu');

        $responseStatus = $this->get('/lomba/status?code='.$reg->registration_code);
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('No. Urut: A-01');
        $responseStatus->assertSee('Kode: LPP-MULA-PA-A01');
    }
}

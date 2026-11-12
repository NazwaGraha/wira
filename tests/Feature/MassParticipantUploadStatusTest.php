<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MassParticipantUploadStatusTest extends TestCase
{
    use RefreshDatabase;

    private function createEventWithRegistration(string $status = 'verified'): array
    {
        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'year' => 2026,
            'is_active' => true,
            'is_registration_open' => true,
        ]);

        $cat1 = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LPP-MADYA-PA',
            'name' => 'Pertolongan Pertama',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'practical_time',
            'max_team_members' => 3,
            'is_active' => true,
        ]);

        $cat2 = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LKTR-MADYA-PA',
            'name' => 'Ketangkasan Tandu Reguler',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'standard_time',
            'max_team_members' => 2,
            'is_active' => true,
        ]);

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-M46214F',
            'school_name' => 'SMP NEGERI 1 CIAWI',
            'level' => 'Madya',
            'advisor_name' => 'Pembina PMR',
            'advisor_phone' => '08123456789',
            'advisor_email' => 'pembina@smpn1ciawi.sch.id',
            'school_address' => 'Jl. Veteran III No. 1 Ciawi',
            'status' => $status,
        ]);

        $team1 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $registration->id,
            'competition_category_id' => $cat1->id,
            'team_name' => 'SMP NEGERI 1 CIAWI Regu A',
            'team_label' => 'Regu A',
            'order_number' => 'A-01',
        ]);

        $team2 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $registration->id,
            'competition_category_id' => $cat2->id,
            'team_name' => 'SMP NEGERI 1 CIAWI Regu A',
            'team_label' => 'Regu A',
            'order_number' => 'A-01',
        ]);

        return compact('event', 'cat1', 'cat2', 'registration', 'team1', 'team2');
    }

    public function test_register_page_does_not_contain_mass_upload_section(): void
    {
        $this->createEventWithRegistration();

        $response = $this->get('/lomba/daftar');
        $response->assertStatus(200);
        $response->assertDontSee('id="mass-upload-section"', false);
        $response->assertSee('Pengisian Berkas & Nama Anggota Peserta', false);
        $response->assertSee('Di Halaman Cek Status', false);
    }

    public function test_verified_registration_shows_mass_upload_and_template_download(): void
    {
        $data = $this->createEventWithRegistration('verified');

        $response = $this->get('/lomba/status?code='.$data['registration']->registration_code);
        $response->assertStatus(200);
        $response->assertSee('Unggah Massal Berkas Peserta Lomba', false);
        $response->assertSee('Unduh Template Excel Massal (.xlsx)', false);
        $response->assertSee(route('lomba.status.download-template', $data['registration']->registration_code), false);
        $response->assertSee(route('lomba.status.upload-mass-participants'), false);
    }

    public function test_pending_registration_does_not_show_mass_upload(): void
    {
        $data = $this->createEventWithRegistration('pending');

        $response = $this->get('/lomba/status?code='.$data['registration']->registration_code);
        $response->assertStatus(200);
        $response->assertDontSee('Unggah Massal Berkas Peserta Lomba', false);
        $response->assertDontSee('Unduh Template Excel Massal (.xlsx)', false);
    }

    public function test_download_template_route_generates_spreadsheet(): void
    {
        $data = $this->createEventWithRegistration('verified');

        $response = $this->get('/lomba/status/download-template/'.$data['registration']->registration_code);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_upload_mass_participants_updates_teams_successfully(): void
    {
        $data = $this->createEventWithRegistration('verified');

        $csvContent = implode("\n", [
            'NO,KODE REGISTRASI,TINGKAT,NAMA SEKOLAH,CABANG LOMBA,KATEGORI,REGU,NISN,NAMA LENGKAP PESERTA,JK,KELAS,PERAN / JABATAN',
            '1,SBB-M46214F,Madya,SMP NEGERI 1 CIAWI,Pertolongan Pertama,Putra,Regu A,12345,Ahmad Fauzi,L,8A,Ketua Regu',
            '2,SBB-M46214F,Madya,SMP NEGERI 1 CIAWI,Pertolongan Pertama,Putra,Regu A,12346,Dimas Pratama,L,8B,Anggota',
            '3,SBB-M46214F,Madya,SMP NEGERI 1 CIAWI,Pertolongan Pertama,Putra,Regu A,12347,Rizky Ramadhan,L,8C,Anggota',
            '4,SBB-M46214F,Madya,SMP NEGERI 1 CIAWI,Ketangkasan Tandu Reguler,Putra,Regu A,12348,Fajar Hidayat,L,9A,Anggota 1',
            '5,SBB-M46214F,Madya,SMP NEGERI 1 CIAWI,Ketangkasan Tandu Reguler,Putra,Regu A,12349,Bayu Saputra,L,9B,Anggota 2',
        ]);

        $file = UploadedFile::fake()->createWithContent('peserta_massal.csv', $csvContent);

        $response = $this->post(route('lomba.status.upload-mass-participants'), [
            'registration_code' => $data['registration']->registration_code,
            'mass_participants_file' => $file,
        ]);

        $response->assertRedirect('/lomba/status?code='.$data['registration']->registration_code);
        $response->assertSessionHas('success');

        $team1 = $data['team1']->fresh();
        $this->assertNotEmpty($team1->members_list);
        $this->assertCount(3, $team1->members_list);
        $this->assertEquals('Ahmad Fauzi', $team1->members_list[0]['name']);

        $team2 = $data['team2']->fresh();
        $this->assertNotEmpty($team2->members_list);
        $this->assertCount(2, $team2->members_list);
        $this->assertEquals('Fajar Hidayat', $team2->members_list[0]['name']);
    }
}

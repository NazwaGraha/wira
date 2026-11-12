<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\CompetitionScorePhoto;
use App\Models\ParkingLocation;
use App\Models\ParkingTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompetitionCascadeIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_registration_cascades_and_deletes_teams_scores_photos_and_parking_tickets(): void
    {
        Storage::fake('public');

        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'year' => 2026,
            'is_active' => true,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LPP-MADYA-PA',
            'name' => 'Pertolongan Pertama',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'practical_time',
            'max_team_members' => 3,
            'is_active' => true,
        ]);

        $proofFile = UploadedFile::fake()->image('bukti_transfer.jpg')->store('payment_proofs', 'public');

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-M12345T',
            'school_name' => 'SMP NEGERI 1 CIAWI',
            'level' => 'Madya',
            'advisor_name' => 'Pembina PMR',
            'advisor_phone' => '08123456789',
            'status' => 'verified',
            'payment_proof' => $proofFile,
        ]);

        $memberFile = UploadedFile::fake()->create('members.xlsx')->store('competition_team_files', 'public');

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $registration->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SMP NEGERI 1 CIAWI Regu A',
            'team_label' => 'Regu A',
            'order_number' => 'A-01',
            'members_file' => $memberFile,
            'members_list' => [
                ['name' => 'Peserta Satu', 'role' => 'Ketua Regu'],
                ['name' => 'Peserta Dua', 'role' => 'Anggota 1'],
            ],
        ]);

        $score = CompetitionScore::create([
            'competition_category_id' => $category->id,
            'competition_participant_team_id' => $team->id,
            'final_score' => 95.5,
            'time_penalty_seconds' => 0,
            'score_details' => ['a' => 10],
        ]);

        $photoFile = UploadedFile::fake()->image('penilaian_juri.jpg')->store('jury_photos', 'public');
        $photo = CompetitionScorePhoto::create([
            'competition_score_id' => $score->id,
            'competition_participant_team_id' => $team->id,
            'photo_path' => $photoFile,
            'photo_type' => 'evidence',
        ]);

        $location = ParkingLocation::create([
            'name' => 'Kantong Parkir Lapangan Utama',
            'code' => 'KP-A',
            'capacity_r4' => 20,
            'capacity_r2' => 50,
            'is_active' => true,
        ]);

        $ticket1 = ParkingTicket::create([
            'competition_registration_id' => $registration->id,
            'parking_location_id' => $location->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'B 1234 SBB',
            'driver_name' => 'Pak Sopir Mobil',
        ]);

        $ticket2 = ParkingTicket::create([
            'competition_registration_id' => $registration->id,
            'parking_location_id' => $location->id,
            'vehicle_type' => 'r2_motor',
            'license_plate' => 'F 5678 SBB',
            'driver_name' => 'Siswa Motor',
        ]);

        // Verifikasi semua data awal ada
        $this->assertDatabaseHas('competition_registrations', ['id' => $registration->id]);
        $this->assertDatabaseHas('competition_participant_teams', ['id' => $team->id]);
        $this->assertDatabaseHas('competition_scores', ['id' => $score->id]);
        $this->assertDatabaseHas('competition_score_photos', ['id' => $photo->id]);
        $this->assertDatabaseHas('parking_tickets', ['id' => $ticket1->id]);
        $this->assertDatabaseHas('parking_tickets', ['id' => $ticket2->id]);
        Storage::disk('public')->assertExists($proofFile);
        Storage::disk('public')->assertExists($memberFile);
        Storage::disk('public')->assertExists($photoFile);

        // HAPUS SEKOLAH
        $registration->delete();

        // PASTIKAN SELURUH DATA TERKAIT HAPUS TOTAL (CASCADE DELETE)
        $this->assertDatabaseMissing('competition_registrations', ['id' => $registration->id]);
        $this->assertDatabaseMissing('competition_participant_teams', ['id' => $team->id]);
        $this->assertDatabaseMissing('competition_scores', ['id' => $score->id]);
        $this->assertDatabaseMissing('competition_score_photos', ['id' => $photo->id]);
        $this->assertDatabaseMissing('parking_tickets', ['id' => $ticket1->id]);
        $this->assertDatabaseMissing('parking_tickets', ['id' => $ticket2->id]);

        // PASTIKAN BERKAS FISIK IKUT TERHAPUS DARI STORAGE
        Storage::disk('public')->assertMissing($proofFile);
        Storage::disk('public')->assertMissing($memberFile);
        Storage::disk('public')->assertMissing($photoFile);
    }

    public function test_updating_school_name_synchronizes_team_names(): void
    {
        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'year' => 2026,
            'is_active' => true,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LPP-MADYA-PA',
            'name' => 'Pertolongan Pertama',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'is_active' => true,
        ]);

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-M99999X',
            'school_name' => 'SMPN 1 CIAWI',
            'advisor_name' => 'Pembina PMR',
            'advisor_phone' => '08123456789',
            'level' => 'Madya',
            'status' => 'verified',
        ]);

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $registration->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SMPN 1 CIAWI Regu A',
            'team_label' => 'Regu A',
        ]);

        $this->assertEquals('SMPN 1 CIAWI Regu A', $team->fresh()->team_name);

        // Update nama sekolah master
        $registration->update([
            'school_name' => 'SMP NEGERI 1 CIAWI BOGOR',
        ]);

        // Pastikan nama regu otomatis tersinkronisasi
        $this->assertEquals('SMP NEGERI 1 CIAWI BOGOR Regu A', $team->fresh()->team_name);
    }
}

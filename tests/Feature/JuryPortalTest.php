<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScorePhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JuryPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function createEventAndCategories(): array
    {
        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
        ]);

        $catTanduMula = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LKTR-Mula',
            'name' => 'Ketangkasan Tandu Reguler',
            'level' => 'Mula',
            'gender_category' => 'Umum',
            'scoring_type' => 'time_and_penalty',
            'order_position' => 1,
            'is_active' => true,
        ]);

        $catTanduMadya = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'LKTR-Madya-PA',
            'name' => 'Ketangkasan Tandu Reguler',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'time_and_penalty',
            'order_position' => 2,
            'is_active' => true,
        ]);

        $catOlimpiade = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'code' => 'OK-Wira',
            'name' => 'Olimpiade Kepalangmerahan',
            'level' => 'Wira',
            'gender_category' => 'Umum',
            'scoring_type' => 'numeric_score',
            'order_position' => 3,
            'is_active' => true,
        ]);

        return [$event, $catTanduMula, $catTanduMadya, $catOlimpiade];
    }

    public function test_admin_can_create_jury_and_map_categories(): void
    {
        [$event, $catTanduMula, $catTanduMadya, $catOlimpiade] = $this->createEventAndCategories();

        // Login sebagai admin
        $this->post('/admin/login', [
            'login_id' => 'admin',
            'password' => 'pmrwirasmanic',
        ]);

        $response = $this->post('/admin/competition-juries', [
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'password' => '123456',
            'phone' => '081234567890',
            'category_ids' => [$catTanduMula->id, $catTanduMadya->id],
        ]);

        $response->assertRedirect('/admin/competition-juries');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'role' => 'jury',
            'phone' => '081234567890',
        ]);

        $jury = User::where('username', 'juri_tandu1')->first();
        $this->assertNotNull($jury);
        $this->assertTrue(Hash::check('123456', $jury->password));
        $this->assertCount(2, $jury->assignedCategories);
        $this->assertTrue($jury->assignedCategories->contains('id', $catTanduMula->id));
        $this->assertTrue($jury->assignedCategories->contains('id', $catTanduMadya->id));
        $this->assertFalse($jury->assignedCategories->contains('id', $catOlimpiade->id));
    }

    public function test_jury_can_login_via_jury_portal(): void
    {
        [$event, $catTanduMula] = $this->createEventAndCategories();

        $jury = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri_tandu1@juri.pmr',
            'password' => Hash::make('123456'),
            'role' => 'jury',
        ]);
        $jury->assignedCategories()->attach($catTanduMula->id);

        // Akses halaman login juri
        $loginPage = $this->get('/juri/login');
        $loginPage->assertOk();
        $loginPage->assertSee('Portal Khusus Juri');

        // Submit login
        $response = $this->post('/juri/login', [
            'username' => 'juri_tandu1',
            'password' => '123456',
        ]);

        $response->assertRedirect('/juri/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals($jury->id, auth()->id());
    }

    public function test_jury_dashboard_only_shows_assigned_categories(): void
    {
        [$event, $catTanduMula, $catTanduMadya, $catOlimpiade] = $this->createEventAndCategories();

        $jury = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri_tandu1@juri.pmr',
            'password' => Hash::make('123456'),
            'role' => 'jury',
        ]);
        $jury->assignedCategories()->sync([$catTanduMula->id, $catTanduMadya->id]);

        $this->actingAs($jury);

        $response = $this->get('/juri/dashboard');
        $response->assertOk();
        $response->assertSee('Kak Hendra Kusuma');
        $response->assertSee('Ketangkasan Tandu Reguler');
        $response->assertSee('LKTR-Mula');
        $response->assertSee('LKTR-Madya-PA');
        // Kategori Olimpiade Wira tidak ditugaskan, jadi tidak boleh muncul
        $response->assertDontSee('OK-Wira');
    }

    public function test_jury_cannot_access_unassigned_category(): void
    {
        [$event, $catTanduMula, $catTanduMadya, $catOlimpiade] = $this->createEventAndCategories();

        $jury = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri_tandu1@juri.pmr',
            'password' => Hash::make('123456'),
            'role' => 'jury',
        ]);
        // Hanya ditugaskan ke Tandu Mula
        $jury->assignedCategories()->attach($catTanduMula->id);

        $this->actingAs($jury);

        // Akses kategori yang tidak ditugaskan harus ditolak 403
        $response = $this->get("/juri/kategori/{$catOlimpiade->id}");
        $response->assertStatus(403);
    }

    public function test_jury_scoring_form_displays_team_and_camera_buttons(): void
    {
        [$event, $catTanduMula] = $this->createEventAndCategories();

        $reg = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-001',
            'school_name' => 'SMPN 1 CIAWI',
            'level' => 'Mula',
            'status' => 'verified',
            'advisor_name' => 'Pembina A',
            'advisor_phone' => '08123456789',
        ]);

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg->id,
            'competition_category_id' => $catTanduMula->id,
            'order_number' => 'A-01',
            'team_name' => 'Regu A',
            'team_label' => 'A',
            'members_list' => [
                ['name' => 'Fajar Pratama'],
                ['name' => 'Dimas Anggara'],
            ],
            'is_active' => true,
        ]);

        $jury = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri_tandu1@juri.pmr',
            'password' => Hash::make('123456'),
            'role' => 'jury',
        ]);
        $jury->assignedCategories()->attach($catTanduMula->id);

        $this->actingAs($jury);

        $response = $this->get("/juri/kategori/{$catTanduMula->id}");
        $response->assertOk();
        $response->assertSee('SMPN 1 CIAWI');
        $response->assertSee('A-01');
        $response->assertSee('Buka Kamera HP');
        $response->assertSee('Pilih Galeri');
        $response->assertSee('Nilai Praktik (0 - 100)');
        $response->assertSee('Waktu Tempuh / Durasi');
    }

    public function test_jury_can_upload_photo_via_base64_from_camera(): void
    {
        [$event, $catTanduMula] = $this->createEventAndCategories();

        $reg = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-002',
            'school_name' => 'SDN Ciawi 01',
            'level' => 'Mula',
            'status' => 'verified',
            'advisor_name' => 'Pembina B',
            'advisor_phone' => '08123456789',
        ]);

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg->id,
            'competition_category_id' => $catTanduMula->id,
            'order_number' => 'A-02',
            'team_name' => 'Regu Putra',
            'team_label' => 'A',
            'is_active' => true,
        ]);

        $jury = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri_tandu1@juri.pmr',
            'password' => Hash::make('123456'),
            'role' => 'jury',
        ]);
        $jury->assignedCategories()->attach($catTanduMula->id);

        $this->actingAs($jury);

        // 1x1 transparent pixel GIF/PNG as base64
        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->postJson('/juri/upload-foto', [
            'team_id' => $team->id,
            'image_base64' => $fakeBase64,
            'caption' => 'Foto Hasil Simpul Tandu',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Foto berhasil disimpan sebagai dokumentasi.',
        ]);

        $this->assertDatabaseHas('competition_score_photos', [
            'competition_participant_team_id' => $team->id,
            'caption' => 'Foto Hasil Simpul Tandu',
        ]);
    }

    public function test_jury_can_save_score_and_advance(): void
    {
        [$event, $catTanduMula] = $this->createEventAndCategories();

        $reg = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-003',
            'school_name' => 'SDN Harapan 01',
            'level' => 'Mula',
            'status' => 'verified',
            'advisor_name' => 'Pembina C',
            'advisor_phone' => '08123456789',
        ]);

        $teamA = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg->id,
            'competition_category_id' => $catTanduMula->id,
            'order_number' => 'A-01',
            'team_name' => 'Regu A',
            'is_active' => true,
        ]);

        $teamB = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg->id,
            'competition_category_id' => $catTanduMula->id,
            'order_number' => 'A-02',
            'team_name' => 'Regu B',
            'is_active' => true,
        ]);

        $jury = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri_tandu1@juri.pmr',
            'password' => Hash::make('123456'),
            'role' => 'jury',
        ]);
        $jury->assignedCategories()->attach($catTanduMula->id);

        $this->actingAs($jury);

        $response = $this->post("/juri/kategori/{$catTanduMula->id}/regu/{$teamA->id}/simpan", [
            'final_score' => 88.5,
            'minutes' => 3,
            'seconds' => 45,
            'notes' => 'Tandu kuat dan simpul rapi',
        ]);

        // Harus redirect ke regu berikutnya (Team B)
        $response->assertRedirect(route('jury.scoring', ['category' => $catTanduMula->id, 'team_id' => $teamB->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('competition_scores', [
            'competition_category_id' => $catTanduMula->id,
            'competition_participant_team_id' => $teamA->id,
            'jury_user_id' => $jury->id,
            'final_score' => 88.50,
            'time_recorded' => '03:45',
            'notes' => 'Tandu kuat dan simpul rapi',
        ]);
    }

    public function test_jury_can_delete_photo(): void
    {
        [$event, $catTanduMula] = $this->createEventAndCategories();

        $reg = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'REG-004',
            'school_name' => 'SDN Harapan 02',
            'level' => 'Mula',
            'status' => 'verified',
            'advisor_name' => 'Pembina D',
            'advisor_phone' => '08123456789',
        ]);

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg->id,
            'competition_category_id' => $catTanduMula->id,
            'order_number' => 'A-01',
            'team_name' => 'Regu A',
            'is_active' => true,
        ]);

        $photo = CompetitionScorePhoto::create([
            'competition_participant_team_id' => $team->id,
            'photo_path' => 'competition_evidences/test_del.png',
            'caption' => 'Foto Mau Dihapus',
            'file_size' => '250 KB',
        ]);

        $jury = User::create([
            'name' => 'Kak Hendra Kusuma',
            'username' => 'juri_tandu1',
            'email' => 'juri_tandu1@juri.pmr',
            'password' => Hash::make('123456'),
            'role' => 'jury',
        ]);
        $jury->assignedCategories()->attach($catTanduMula->id);

        $this->actingAs($jury);

        $response = $this->deleteJson("/juri/foto/{$photo->id}");
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Foto dokumentasi berhasil dihapus.',
        ]);

        $this->assertDatabaseMissing('competition_score_photos', [
            'id' => $photo->id,
        ]);
    }
}

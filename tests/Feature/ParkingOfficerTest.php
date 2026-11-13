<?php

namespace Tests\Feature;

use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use App\Models\ParkingLocation;
use App\Models\ParkingOfficer;
use App\Models\ParkingTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParkingOfficerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private ParkingLocation $locationA;

    private ParkingLocation $locationB;

    private CompetitionRegistration $registration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Panitia',
            'username' => 'admin_panitia',
            'email' => 'admin_panitia@pmr.test',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
        ]);

        $this->registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'school_name' => 'SMAN 1 BOGOR',
            'registration_code' => 'REG-SMAN1BOGOR',
            'level' => 'Wira',
            'status' => 'verified',
            'advisor_name' => 'Pak Pembina',
            'advisor_phone' => '08123456789',
        ]);

        $this->locationA = ParkingLocation::create([
            'name' => 'Kantong Parkir A (Lapangan Depan)',
            'code' => 'KPA',
            'capacity_r4' => 30,
            'capacity_r2' => 80,
            'status' => 'open',
        ]);

        $this->locationB = ParkingLocation::create([
            'name' => 'Kantong Parkir B (Gedung Belakang)',
            'code' => 'KPB',
            'capacity_r4' => 20,
            'capacity_r2' => 50,
            'status' => 'open',
        ]);
    }

    public function test_admin_can_view_parking_management_with_officers(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Budi Santoso',
            'username' => 'petugas_budi',
            'password' => Hash::make('secret123'),
            'phone' => '081299887766',
            'is_active' => true,
        ]);
        $officer->locations()->attach($this->locationA->id);

        $response = $this->actingAs($this->admin)->get(route('admin.parking.index'));

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee('petugas_budi');
        $response->assertSee('KPA');
    }

    public function test_admin_can_create_new_parking_officer(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.parking.officers.store'), [
            'name' => 'Rian Hidayat',
            'username' => 'petugas_rian',
            'password' => 'password123',
            'phone' => '085711223344',
            'location_ids' => [$this->locationA->id, $this->locationB->id],
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.parking.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('parking_officers', [
            'name' => 'Rian Hidayat',
            'username' => 'petugas_rian',
            'phone' => '085711223344',
            'is_active' => 1,
        ]);

        $officer = ParkingOfficer::where('username', 'petugas_rian')->first();
        $this->assertNotNull($officer);
        $this->assertTrue(Hash::check('password123', $officer->password));
        $this->assertCount(2, $officer->locations);
    }

    public function test_admin_can_update_officer_and_sync_locations(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Dewi Lestari',
            'username' => 'petugas_dewi',
            'password' => Hash::make('oldpassword'),
            'phone' => '08123456789',
            'is_active' => true,
        ]);
        $officer->locations()->attach([$this->locationA->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.parking.officers.update', $officer->id), [
            'name' => 'Dewi Lestari Updated',
            'username' => 'dewi_pos_b',
            'phone' => '089988776655',
            'password' => 'newpassword123',
            'location_ids' => [$this->locationB->id],
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.parking.index'));
        $officer->refresh();

        $this->assertEquals('Dewi Lestari Updated', $officer->name);
        $this->assertEquals('dewi_pos_b', $officer->username);
        $this->assertTrue(Hash::check('newpassword123', $officer->password));
        $this->assertCount(1, $officer->locations);
        $this->assertEquals($this->locationB->id, $officer->locations->first()->id);
    }

    public function test_admin_can_toggle_officer_status(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Agus Priyono',
            'username' => 'agus_parkir',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.parking.officers.toggle-status', $officer->id));
        $response->assertRedirect(route('admin.parking.index'));

        $this->assertFalse($officer->fresh()->is_active);

        // Toggle back to active
        $this->actingAs($this->admin)->post(route('admin.parking.officers.toggle-status', $officer->id));
        $this->assertTrue($officer->fresh()->is_active);
    }

    public function test_admin_can_delete_officer(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Siti Aisyah',
            'username' => 'siti_parkir',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);
        $officer->locations()->attach([$this->locationA->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.parking.officers.destroy', $officer->id));
        $response->assertRedirect(route('admin.parking.index'));

        $this->assertDatabaseMissing('parking_officers', ['id' => $officer->id]);
        $this->assertDatabaseMissing('parking_officer_locations', ['parking_officer_id' => $officer->id]);
    }

    public function test_officer_can_login_at_field_portal(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Fajar Pratama',
            'username' => 'fajar_parkir',
            'password' => Hash::make('fajar12345'),
            'is_active' => true,
        ]);

        $response = $this->post(route('parking.login.submit'), [
            'username' => 'fajar_parkir',
            'password' => 'fajar12345',
        ]);

        $response->assertRedirect(route('parking.index'));
        $this->assertTrue(Auth::guard('parking_officer')->check());
        $this->assertEquals($officer->id, Auth::guard('parking_officer')->id());
    }

    public function test_inactive_officer_cannot_login(): void
    {
        ParkingOfficer::create([
            'name' => 'Hendro Nonaktif',
            'username' => 'hendro_parkir',
            'password' => Hash::make('hendro123'),
            'is_active' => false,
        ]);

        $response = $this->from(route('parking.login'))->post(route('parking.login.submit'), [
            'username' => 'hendro_parkir',
            'password' => 'hendro123',
        ]);

        $response->assertRedirect(route('parking.login'));
        $response->assertSessionHasErrors('username');
        $this->assertFalse(Auth::guard('parking_officer')->check());
    }

    public function test_officer_scanner_shows_assigned_location(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Joko Petugas',
            'username' => 'joko_kpa',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);
        $officer->locations()->attach([$this->locationA->id]);

        $response = $this->actingAs($officer, 'parking_officer')->get(route('parking.index'));

        $response->assertOk();
        $response->assertSee('Kantong Parkir A (Lapangan Depan)');
        $response->assertSee('Joko Petugas');
    }

    public function test_officer_can_checkin_ticket_at_assigned_location(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Slamet',
            'username' => 'slamet_pos',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);
        $officer->locations()->attach([$this->locationA->id]);

        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'B 1234 ABC',
            'driver_name' => 'Sopir Bus 1',
            'status' => 'booked',
            'is_public' => false,
            'fee_amount' => 0,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($officer, 'parking_officer')->postJson(route('parking.check-in'), [
            'ticket_id' => $ticket->id,
            'location_id' => $this->locationA->id,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $ticket->refresh();
        $this->assertEquals('parked', $ticket->status);
        $this->assertEquals($officer->id, $ticket->checked_by_officer_id);
    }

    public function test_officer_cannot_checkin_ticket_at_unassigned_location(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Eko Pos A Only',
            'username' => 'eko_pos_a',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);
        // Only assigned to Location A
        $officer->locations()->attach([$this->locationA->id]);

        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationB->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 9999 XYZ',
            'status' => 'booked',
            'is_public' => false,
            'fee_amount' => 0,
            'payment_status' => 'paid',
        ]);

        // Attempt check-in at Location B
        $response = $this->actingAs($officer, 'parking_officer')->postJson(route('parking.check-in'), [
            'ticket_id' => $ticket->id,
            'location_id' => $this->locationB->id,
        ]);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }

    public function test_officer_can_verify_ticket_by_code_and_scanned_qr_url(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Duke Petugas',
            'username' => 'duke',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);
        $officer->locations()->attach([$this->locationA->id]);

        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1234 FAB',
            'driver_name' => 'Ny. Naura Yusmira',
            'status' => 'booked',
            'is_public' => true,
            'fee_amount' => 10000,
            'payment_method' => 'qris',
            'payment_status' => 'paid',
        ]);

        $this->actingAs($officer, 'parking_officer');

        // 1. Verify dengan Ticket Code (misal manual input: PKR4-SBB4-0001)
        $resCode = $this->postJson(route('parking.verify'), [
            'code' => $ticket->ticket_code,
            'location_id' => $this->locationA->id,
        ]);
        $resCode->assertOk();
        $resCode->assertJson([
            'success' => true,
            'found' => true,
            'ticket' => [
                'ticket_code' => $ticket->ticket_code,
                'license_plate' => 'F 1234 FAB',
            ],
        ]);

        // 2. Verify dengan Scanned Full URL dari QR Code
        $fullQrUrl = route('parking.public-ticket', $ticket->qr_code_token);
        $resUrl = $this->postJson(route('parking.verify'), [
            'code' => $fullQrUrl,
            'location_id' => $this->locationA->id,
        ]);
        $resUrl->assertOk();
        $resUrl->assertJson([
            'success' => true,
            'found' => true,
            'ticket' => [
                'ticket_code' => $ticket->ticket_code,
            ],
        ]);

        // 3. Verify dengan Plat Nomor tanpa spasi
        $resPlate = $this->postJson(route('parking.verify'), [
            'code' => 'f1234fab',
            'location_id' => $this->locationA->id,
        ]);
        $resPlate->assertOk();
        $resPlate->assertJson([
            'success' => true,
            'found' => true,
            'ticket' => [
                'ticket_code' => $ticket->ticket_code,
            ],
        ]);

        // 4. Verify kode palsu
        $resInvalid = $this->postJson(route('parking.verify'), [
            'code' => 'KODE-PALSU-999',
            'location_id' => $this->locationA->id,
        ]);
        $resInvalid->assertStatus(404);
        $resInvalid->assertJson(['found' => false]);
    }

    public function test_officer_can_logout(): void
    {
        $officer = ParkingOfficer::create([
            'name' => 'Doni Petugas',
            'username' => 'doni_parkir',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);

        $this->actingAs($officer, 'parking_officer');
        $this->assertTrue(Auth::guard('parking_officer')->check());

        $response = $this->post(route('parking.logout'));

        $response->assertRedirect(route('parking.login'));
        $this->assertFalse(Auth::guard('parking_officer')->check());
    }
}

<?php

namespace Tests\Feature;

use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use App\Models\ParkingLocation;
use App\Models\ParkingTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParkingManagementTest extends TestCase
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
            'name' => 'Admin PMR',
            'username' => 'admin_parking_test',
            'email' => 'admin_parking@test.pmr',
            'role' => 'admin',
            'password' => Hash::make('password'),
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
            'capacity_r4' => 40,
            'capacity_r2' => 100,
            'officer_name' => 'Kang Farhan',
            'officer_phone' => '081288991122',
            'status' => 'open',
        ]);

        $this->locationB = ParkingLocation::create([
            'name' => 'Kantong Parkir B (Halaman Belakang)',
            'code' => 'KPB',
            'capacity_r4' => 30,
            'capacity_r2' => 120,
            'officer_name' => 'Kang Ridwan',
            'officer_phone' => '081399887766',
            'status' => 'open',
        ]);
    }

    public function test_admin_can_view_parking_dashboard(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.parking.index'));
        $response->assertOk();
        $response->assertSee('Kantong Parkir A');
        $response->assertSee('Total Kantong Parkir');
    }

    public function test_admin_can_create_update_and_delete_parking_location(): void
    {
        $this->actingAs($this->admin);

        // 1. Create
        $responseCreate = $this->post(route('admin.parking.locations.store'), [
            'name' => 'Kantong Parkir C (Kecamatan)',
            'code' => 'KPC',
            'capacity_r4' => 50,
            'capacity_r2' => 130,
            'officer_name' => 'Hendra',
            'officer_phone' => '0812345678',
            'status' => 'open',
        ]);
        $responseCreate->assertRedirect();
        $this->assertDatabaseHas('parking_locations', ['code' => 'KPC']);

        $locationC = ParkingLocation::where('code', 'KPC')->first();

        // 2. Update
        $responseUpdate = $this->put(route('admin.parking.locations.update', $locationC->id), [
            'name' => 'Kantong Parkir C (Kecamatan & Koramil)',
            'code' => 'KPC',
            'capacity_r4' => 60,
            'capacity_r2' => 150,
            'officer_name' => 'Hendra & Rizky',
            'officer_phone' => '0812345678',
            'status' => 'open',
        ]);
        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('parking_locations', ['capacity_r4' => 60]);

        // 3. Delete
        $responseDelete = $this->delete(route('admin.parking.locations.destroy', $locationC->id));
        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('parking_locations', ['id' => $locationC->id]);
    }

    public function test_admin_can_create_update_and_delete_parking_ticket(): void
    {
        $this->actingAs($this->admin);

        // 1. Create ticket
        $respCreate = $this->post(route('admin.parking.tickets.store'), [
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_elf',
            'license_plate' => 'F 1845 EA',
            'vehicle_brand' => 'Toyota HiAce',
            'vehicle_color' => 'Putih',
            'driver_name' => 'Pak Hendra',
            'driver_phone' => '081388991234',
        ]);
        $respCreate->assertRedirect();
        $this->assertDatabaseHas('parking_tickets', ['license_plate' => 'F 1845 EA']);

        $ticket = ParkingTicket::where('license_plate', 'F 1845 EA')->first();
        $this->assertNotNull($ticket->qr_code_token);
        $this->assertStringStartsWith('PKR4-SBB4-', $ticket->ticket_code);

        // 2. Update ticket
        $respUpdate = $this->put(route('admin.parking.tickets.update', $ticket->id), [
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationB->id,
            'vehicle_type' => 'r4_elf',
            'license_plate' => 'F 1845 EA',
            'vehicle_brand' => 'Toyota HiAce Premio',
            'vehicle_color' => 'Silver Metalik',
            'driver_name' => 'Pak Hendra Baru',
            'driver_phone' => '081388999999',
            'status' => 'booked',
        ]);
        $respUpdate->assertRedirect();
        $this->assertDatabaseHas('parking_tickets', [
            'id' => $ticket->id,
            'parking_location_id' => $this->locationB->id,
            'vehicle_brand' => 'Toyota HiAce Premio',
        ]);

        // 3. Delete ticket
        $respDelete = $this->delete(route('admin.parking.tickets.destroy', $ticket->id));
        $respDelete->assertRedirect();
        $this->assertDatabaseMissing('parking_tickets', ['id' => $ticket->id]);
    }

    public function test_admin_can_bulk_assign_tickets_to_location(): void
    {
        $this->actingAs($this->admin);

        $ticket1 = ParkingTicket::create([
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1111 AA',
            'status' => 'booked',
        ]);

        $ticket2 = ParkingTicket::create([
            'vehicle_type' => 'r2_motor',
            'license_plate' => 'F 2222 BB',
            'status' => 'booked',
        ]);

        $response = $this->post(route('admin.parking.tickets.assign-location'), [
            'ticket_ids' => [$ticket1->id, $ticket2->id],
            'parking_location_id' => $this->locationB->id,
        ]);
        $response->assertRedirect();

        $this->assertEquals($this->locationB->id, $ticket1->fresh()->parking_location_id);
        $this->assertEquals($this->locationB->id, $ticket2->fresh()->parking_location_id);
    }

    public function test_admin_can_export_parking_csv(): void
    {
        $this->actingAs($this->admin);

        ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'B 9999 EXP',
            'status' => 'booked',
        ]);

        $response = $this->get(route('admin.parking.export-excel'));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_printable_ticket(): void
    {
        $this->actingAs($this->admin);

        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 7777 PRINT',
            'status' => 'booked',
        ]);

        $response = $this->get(route('admin.parking.tickets.print', $ticket->id));
        $response->assertOk();
        $response->assertSee('F 7777 PRINT');
        $response->assertSee('E-TIKET PARKIR RESMI');
    }

    public function test_public_can_view_e_ticket_via_token(): void
    {
        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_elf',
            'license_plate' => 'F 5555 PUB',
            'vehicle_brand' => 'Toyota HiAce',
            'status' => 'booked',
        ]);

        $response = $this->get(route('parking.public-ticket', $ticket->qr_code_token));
        $response->assertOk();
        $response->assertSee('F 5555 PUB');
        $response->assertSee('Kantong Parkir A');
    }

    public function test_officer_can_view_scanner_and_verify_ticket(): void
    {
        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_elf',
            'license_plate' => 'F 1845 EA',
            'status' => 'booked',
        ]);

        // 1. Scanner view
        $respScanner = $this->get(route('parking.scanner', ['location_id' => $this->locationA->id]));
        $respScanner->assertOk();
        $respScanner->assertSee('PETUGAS PARKIR LAPANGAN');

        // 2. Verify with matching location (Location A)
        $respVerifyMatch = $this->postJson(route('parking.verify'), [
            'code' => $ticket->qr_code_token,
            'location_id' => $this->locationA->id,
        ]);
        $respVerifyMatch->assertOk();
        $respVerifyMatch->assertJson([
            'success' => true,
            'is_matching_location' => true,
        ]);

        // 3. Verify with UNMATCHING location (Location B)
        $respVerifyUnmatch = $this->postJson(route('parking.verify'), [
            'code' => $ticket->qr_code_token,
            'location_id' => $this->locationB->id,
        ]);
        $respVerifyUnmatch->assertOk();
        $respVerifyUnmatch->assertJson([
            'success' => true,
            'is_matching_location' => false,
        ]);

        // 4. Check-In
        $respCheckIn = $this->postJson(route('parking.check-in'), [
            'ticket_id' => $ticket->id,
            'location_id' => $this->locationA->id,
        ]);
        $respCheckIn->assertOk();
        $this->assertEquals('parked', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->check_in_at);

        // 5. Check-Out
        $respCheckOut = $this->postJson(route('parking.check-out'), [
            'ticket_id' => $ticket->id,
        ]);
        $respCheckOut->assertOk();
        $this->assertEquals('checked_out', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->check_out_at);
    }

    public function test_admin_can_view_parking_quotas_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.parking.quotas'));
        $response->assertOk();
        $response->assertSee('Pengaturan Kuota Parkir Sekolah');
        $response->assertSee('SMAN 1 BOGOR');
    }

    public function test_admin_can_update_quota_settings(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.parking.quotas.settings'), [
            'mode' => 'flat',
            'flat_default_r4' => 2,
            'flat_default_r2' => 3,
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('parking_quota_settings', [
            'mode' => 'flat',
            'flat_default_r4' => 2,
            'flat_default_r2' => 3,
        ]);
    }

    public function test_admin_can_override_and_reset_school_quota(): void
    {
        $this->actingAs($this->admin);

        // 1. Override custom
        $respCustom = $this->post(route('admin.parking.quotas.school', $this->registration->id), [
            'parking_quota_mode' => 'custom',
            'custom_quota_r4' => 4,
            'custom_quota_r2' => 3,
            'parking_notes' => 'Dispensasi kontingen membawa bus & rombongan kepala sekolah',
        ]);
        $respCustom->assertRedirect();

        $this->assertEquals(4, $this->registration->fresh()->allocated_quota_r4);
        $this->assertEquals(3, $this->registration->fresh()->allocated_quota_r2);
        $this->assertEquals('custom', $this->registration->fresh()->parking_quota_mode);

        // 2. Reset all
        $respReset = $this->post(route('admin.parking.quotas.reset-all'));
        $respReset->assertRedirect();

        $this->assertEquals('auto', $this->registration->fresh()->parking_quota_mode);
        $this->assertNull($this->registration->fresh()->custom_quota_r4);
    }

    public function test_quota_enforcement_blocks_ticket_issuance_when_quota_exceeded(): void
    {
        $this->actingAs($this->admin);

        // Set custom quota R4=1 for this registration
        $this->registration->update([
            'parking_quota_mode' => 'custom',
            'custom_quota_r4' => 1,
            'custom_quota_r2' => 1,
        ]);

        // Issue 1st ticket (should succeed)
        $resp1 = $this->post(route('admin.parking.tickets.store'), [
            'competition_registration_id' => $this->registration->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1001 OK',
        ]);
        $resp1->assertRedirect();
        $this->assertDatabaseHas('parking_tickets', ['license_plate' => 'F 1001 OK']);

        // Issue 2nd ticket (should be blocked by validation error because quota R4=1 is exhausted)
        $resp2 = $this->post(route('admin.parking.tickets.store'), [
            'competition_registration_id' => $this->registration->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1002 BLOCKED',
        ]);
        $resp2->assertSessionHasErrors(['license_plate']);
        $this->assertDatabaseMissing('parking_tickets', ['license_plate' => 'F 1002 BLOCKED']);
    }
}

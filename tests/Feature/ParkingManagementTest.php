<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\ParkingFareSetting;
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

        $this->actingAs($this->admin);

        // 1. Scanner view & /parkir root access
        $respRoot = $this->get('/parkir');
        $respRoot->assertOk();
        $respRoot->assertSee('PETUGAS PARKIR LAPANGAN');

        $respScanner = $this->get(route('parking.scanner', ['location_id' => $this->locationA->id]));
        $respScanner->assertRedirect(route('parking.index', ['location_id' => $this->locationA->id]));

        $respIndex = $this->get(route('parking.index', ['location_id' => $this->locationA->id]));
        $respIndex->assertOk();
        $respIndex->assertSee('PETUGAS PARKIR LAPANGAN');

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

    public function test_public_vehicle_registration_respects_quota_and_creates_tickets(): void
    {
        $this->registration->update([
            'parking_quota_mode' => 'custom',
            'custom_quota_r4' => 2,
            'custom_quota_r2' => 2,
        ]);

        $response = $this->post(route('lomba.register-vehicles'), [
            'registration_code' => $this->registration->registration_code,
            'vehicles' => [
                [
                    'vehicle_type' => 'r4_car',
                    'license_plate' => 'f 1845 ea',
                    'vehicle_brand' => 'Toyota HiAce',
                    'vehicle_color' => 'Putih',
                    'driver_name' => 'Pak Hendra',
                    'driver_phone' => '08123456789',
                ],
                [
                    'vehicle_type' => 'r2_motor',
                    'license_plate' => 'b 6543 zzz',
                    'vehicle_brand' => 'Honda Vario',
                    'vehicle_color' => 'Hitam',
                    'driver_name' => 'Kak Rian',
                    'driver_phone' => '08987654321',
                ],
            ],
        ]);

        $response->assertRedirect(route('lomba.status', ['code' => $this->registration->registration_code]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('parking_tickets', [
            'competition_registration_id' => $this->registration->id,
            'license_plate' => 'F 1845 EA',
            'vehicle_type' => 'r4_car',
            'driver_name' => 'Pak Hendra',
        ]);

        $this->assertDatabaseHas('parking_tickets', [
            'competition_registration_id' => $this->registration->id,
            'license_plate' => 'B 6543 ZZZ',
            'vehicle_type' => 'r2_motor',
            'driver_name' => 'Kak Rian',
        ]);
    }

    public function test_public_vehicle_registration_blocks_submission_exceeding_quota(): void
    {
        $this->registration->update([
            'parking_quota_mode' => 'custom',
            'custom_quota_r4' => 1,
            'custom_quota_r2' => 0,
        ]);

        // Submit 2 cars when quota is only 1
        $response = $this->post(route('lomba.register-vehicles'), [
            'registration_code' => $this->registration->registration_code,
            'vehicles' => [
                [
                    'vehicle_type' => 'r4_car',
                    'license_plate' => 'F 1111 AA',
                ],
                [
                    'vehicle_type' => 'r4_elf',
                    'license_plate' => 'F 2222 BB',
                ],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('parking_tickets', ['license_plate' => 'F 1111 AA']);
        $this->assertDatabaseMissing('parking_tickets', ['license_plate' => 'F 2222 BB']);
    }

    public function test_public_delete_vehicle_ticket(): void
    {
        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 9999 DEL',
            'status' => 'booked',
        ]);

        $response = $this->post(route('lomba.delete-vehicle-ticket'), [
            'registration_code' => $this->registration->registration_code,
            'ticket_id' => $ticket->id,
        ]);

        $response->assertRedirect(route('lomba.status', ['code' => $this->registration->registration_code]));
        $this->assertDatabaseMissing('parking_tickets', ['id' => $ticket->id]);
    }

    public function test_auto_allocate_priority_calculates_spps_and_allocates_top_ranker_to_kantong_a(): void
    {
        $this->actingAs($this->admin);

        // Mark Location A as priority
        $this->locationA->update(['is_priority' => true, 'priority_order' => 1, 'capacity_r4' => 5]);
        $this->locationB->update(['is_priority' => false, 'priority_order' => 2, 'capacity_r4' => 5]);

        // Create 2nd school with fewer teams
        $schoolB = CompetitionRegistration::create([
            'competition_event_id' => $this->registration->competition_event_id,
            'school_name' => 'SMAN 2 BOGOR',
            'registration_code' => 'REG-SMAN2BOGOR',
            'level' => 'Wira',
            'status' => 'verified',
            'advisor_name' => 'Pembina B',
            'advisor_phone' => '08129999999',
            'created_at' => now()->addHour(),
        ]);

        // Create categories and teams for school A (full commitment)
        $cat1 = CompetitionCategory::create([
            'competition_event_id' => $this->registration->competition_event_id,
            'name' => 'LKTR Wira Pa',
            'level' => 'Wira',
            'is_active' => true,
        ]);
        $cat2 = CompetitionCategory::create([
            'competition_event_id' => $this->registration->competition_event_id,
            'name' => 'PP Wira Pa',
            'level' => 'Wira',
            'is_active' => true,
        ]);

        CompetitionParticipantTeam::create([
            'competition_registration_id' => $this->registration->id,
            'competition_category_id' => $cat1->id,
            'team_name' => 'Tim 1',
        ]);
        CompetitionParticipantTeam::create([
            'competition_registration_id' => $this->registration->id,
            'competition_category_id' => $cat2->id,
            'team_name' => 'Tim 2',
        ]);

        // Add tickets for school A and B
        $ticketA = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1000 PRIO',
        ]);

        $ticketB = ParkingTicket::create([
            'competition_registration_id' => $schoolB->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 2000 LATE',
        ]);

        // Trigger Auto Allocate Priority
        $response = $this->post(route('admin.parking.auto-allocate-priority'));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check School A has priority rank 1 and assigned to location A (Kantong Parkir A)
        $this->assertEquals(1, $this->registration->fresh()->parking_priority_rank);
        $this->assertTrue($this->registration->fresh()->parking_priority_criteria['is_top_priority']);
        $this->assertEquals($this->locationA->id, $ticketA->fresh()->parking_location_id);

        // Check School B has priority rank 2
        $this->assertEquals(2, $schoolB->fresh()->parking_priority_rank);
    }

    public function test_school_parking_tickets_print_page(): void
    {
        ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 8888 PRINT',
        ]);

        $response = $this->get(route('parking.school-tickets', $this->registration->registration_code));
        $response->assertOk();
        $response->assertSee('F 8888 PRINT');
        $response->assertSee($this->registration->school_name);
    }

    public function test_status_page_renders_parking_section_and_priority_banner_for_verified_school(): void
    {
        // 1. Without tickets -> shows notice and register button
        $resp1 = $this->get(route('lomba.status', ['code' => $this->registration->registration_code]));
        $resp1->assertOk();
        $resp1->assertSee('FASILITAS PARKIR KONTINGEN RESMI');
        $resp1->assertSee('Daftarkan Kendaraan');

        // 2. With tickets and top priority
        $ticket = ParkingTicket::create([
            'competition_registration_id' => $this->registration->id,
            'parking_location_id' => $this->locationA->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1234 SBB',
            'driver_name' => 'Pak Hendra',
        ]);

        $this->registration->update([
            'parking_priority_score' => 95.0,
            'parking_priority_rank' => 1,
            'parking_priority_criteria' => [
                'all_events_participated' => true,
                'categories_count' => 5,
                'categories_total' => 5,
                'teams_count' => 6,
                'is_top_priority' => true,
                'assigned_location_name' => 'Kantong Parkir A (Lapangan Depan)',
            ],
        ]);

        $resp2 = $this->get(route('lomba.status', ['code' => $this->registration->registration_code]));
        $resp2->assertOk();
        $resp2->assertSee('PRIORITAS PARKIR UTAMA DISETUJUI');
        $resp2->assertSee('F 1234 SBB');
        $resp2->assertSee('Pak Hendra');
        $resp2->assertSee('Cetak Semua E-Tiket Sekaligus (PDF)');
    }

    public function test_admin_can_update_parking_fare_settings(): void
    {
        $this->actingAs($this->admin);

        $response = $this->from(route('admin.parking.index'))->post(route('admin.parking.fares.update'), [
            'is_public_parking_active' => '1',
            'fare_r2_motor' => 7000,
            'fare_r4_car' => 15000,
            'fare_r4_minibus' => 30000,
            'fare_r6_bus' => 60000,
            'allow_cash' => '1',
            'allow_qris' => '1',
            'lost_ticket_penalty' => 25000,
            'ticket_receipt_note' => 'Simpan karcis ini baik-baik.',
        ]);

        $response->assertRedirect(route('admin.parking.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('parking_fare_settings', [
            'id' => 1,
            'fare_r2_motor' => 7000,
            'fare_r4_car' => 15000,
            'fare_r4_minibus' => 30000,
            'fare_r6_bus' => 60000,
            'lost_ticket_penalty' => 25000,
            'ticket_receipt_note' => 'Simpan karcis ini baik-baik.',
        ]);
    }

    public function test_admin_can_create_public_parking_ticket_with_calculated_fare(): void
    {
        $this->actingAs($this->admin);

        // Create public ticket for minibus elf without school registration
        $response = $this->from(route('admin.parking.index'))->post(route('admin.parking.tickets.store'), [
            'competition_registration_id' => '',
            'license_plate' => 'B 9999 PMR',
            'vehicle_type' => 'r4_elf',
            'vehicle_brand' => 'Isuzu Elf Long',
            'vehicle_color' => 'Silver',
            'driver_name' => 'Bambang',
            'driver_phone' => '081299887766',
            'parking_location_id' => $this->locationA->id,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        $response->assertRedirect(route('admin.parking.index'));

        $this->assertDatabaseHas('parking_tickets', [
            'license_plate' => 'B 9999 PMR',
            'is_public' => true,
            'fee_amount' => 25000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);
    }

    public function test_public_user_can_purchase_parking_ticket_from_competition_portal(): void
    {
        $response = $this->post(route('lomba.buy-parking-ticket'), [
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1845 EA',
            'vehicle_brand' => 'Toyota Avanza',
            'vehicle_color' => 'Hitam Metalik',
            'driver_name' => 'Hendra Setiawan',
            'driver_phone' => '081234567890',
            'payment_method' => 'qris',
        ]);

        $this->assertDatabaseHas('parking_tickets', [
            'license_plate' => 'F 1845 EA',
            'is_public' => true,
            'vehicle_type' => 'r4_car',
            'fee_amount' => 10000,
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'driver_name' => 'Hendra Setiawan',
        ]);

        $ticket = ParkingTicket::where('license_plate', 'F 1845 EA')->first();
        $this->assertNotNull($ticket);
        $response->assertRedirect(route('parking.public-ticket', $ticket->qr_code_token));

        // Test public ticket page renders
        $ticketResp = $this->get(route('parking.public-ticket', $ticket->qr_code_token));
        $ticketResp->assertOk();
        $ticketResp->assertSee('F 1845 EA');
        $ticketResp->assertSee('Hendra Setiawan');
        $ticketResp->assertSee('Rp 10.000');
    }

    public function test_public_user_can_search_parking_ticket_by_plate(): void
    {
        $ticket = ParkingTicket::create([
            'vehicle_type' => 'r2_motor',
            'license_plate' => 'B 1234 XYZ',
            'driver_name' => 'Rian',
            'driver_phone' => '081299998888',
            'is_public' => true,
            'fee_amount' => 5000,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'status' => 'booked',
        ]);

        $response = $this->get(route('lomba.check-parking-ticket', ['ticket_search' => 'b 1234 xyz']));
        $response->assertRedirect(route('parking.public-ticket', $ticket->qr_code_token));
    }

    public function test_public_user_cannot_purchase_ticket_when_service_is_closed(): void
    {
        ParkingFareSetting::current()->update(['is_public_parking_active' => false]);

        $response = $this->post(route('lomba.buy-parking-ticket'), [
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 9999 AA',
            'driver_name' => 'Budi',
            'driver_phone' => '0812345678',
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('parking_tickets', ['license_plate' => 'F 9999 AA']);
    }

    public function test_parking_officer_can_issue_on_the_spot_ticket(): void
    {
        $officer = User::factory()->create(['role' => 'user']);
        $location = ParkingLocation::create([
            'name' => 'Lapangan Utama',
            'code' => 'LAP-UTAMA',
            'capacity_r4' => 50,
            'capacity_r2' => 100,
            'is_active' => true,
        ]);

        $response = $this->actingAs($officer)->post(route('parking.on-the-spot'), [
            'parking_location_id' => $location->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'D 1234 SPOT',
            'driver_name' => 'Pak Hendra',
            'driver_phone' => '08123456789',
            'payment_method' => 'cash',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'ticket' => [
                    'license_plate' => 'D 1234 SPOT',
                    'fee_amount' => 10000,
                    'status' => 'parked',
                ],
            ]);

        $this->assertDatabaseHas('parking_tickets', [
            'license_plate' => 'D 1234 SPOT',
            'parking_location_id' => $location->id,
            'vehicle_type' => 'r4_car',
            'fee_amount' => 10000,
            'status' => 'parked',
            'is_public' => true,
        ]);
    }

    public function test_contingent_vehicle_registration_respects_wave2_deadline_and_switches_to_public_fare(): void
    {
        $event = $this->registration->event;
        $event->update([
            'is_waves_enabled' => true,
            'wave2_end_date' => now()->addDays(3),
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama',
            'code' => 'PP-WIRA',
            'level' => 'Wira',
            'is_active' => true,
        ]);

        CompetitionParticipantTeam::create([
            'competition_registration_id' => $this->registration->id,
            'competition_category_id' => $category->id,
            'team_name' => 'Tim A',
            'level' => 'Wira',
        ]);

        // 1. Sebelum batas waktu: kuota gratis aktif
        $this->assertFalse($this->registration->fresh()->is_parking_registration_expired);
        $this->assertEquals(1, $this->registration->fresh()->remaining_quota_r4);

        $responseBeforeDeadline = $this->post(route('lomba.register-vehicles'), [
            'registration_code' => $this->registration->registration_code,
            'vehicles' => [
                [
                    'vehicle_type' => 'r4_car',
                    'license_plate' => 'F 1111 FREE',
                    'driver_name' => 'Sopir Resmi',
                    'driver_phone' => '0812345678',
                ],
            ],
        ]);

        $responseBeforeDeadline->assertRedirect(route('lomba.status', ['code' => $this->registration->registration_code]));
        $this->assertDatabaseHas('parking_tickets', [
            'license_plate' => 'F 1111 FREE',
            'competition_registration_id' => $this->registration->id,
            'is_public' => false,
            'fee_amount' => 0,
            'status' => 'booked',
        ]);

        // 2. Simulasikan batas waktu telah lewat (H+1 penutupan gelombang 2)
        $event->update([
            'wave2_end_date' => now()->subDays(2), // Lewat lebih dari 1 hari
        ]);

        $regFresh = $this->registration->fresh();
        $this->assertTrue($regFresh->is_parking_registration_expired);
        $this->assertEquals(0, $regFresh->remaining_quota_r4);
        $this->assertEquals(0, $regFresh->remaining_quota_r2);

        // Pendaftaran setelah deadline akan otomatis dialihkan ke tarif umum & floating parkir
        $responseAfterDeadline = $this->post(route('lomba.register-vehicles'), [
            'registration_code' => $this->registration->registration_code,
            'payment_method' => 'cash',
            'vehicles' => [
                [
                    'vehicle_type' => 'r4_car',
                    'license_plate' => 'B 9999 FLOAT',
                    'driver_name' => 'Sopir Floating',
                    'driver_phone' => '081299998888',
                ],
            ],
        ]);

        $responseAfterDeadline->assertRedirect(route('lomba.status', ['code' => $this->registration->registration_code]));
        $this->assertDatabaseHas('parking_tickets', [
            'license_plate' => 'B 9999 FLOAT',
            'competition_registration_id' => $this->registration->id,
            'is_public' => true,
            'fee_amount' => 10000,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'status' => 'booked',
        ]);
    }
}

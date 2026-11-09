<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\ParkingLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_user_permissions_index(): void
    {
        Member::factory()->create([
            'name' => 'Ahmad Fauzi',
            'position' => 'Staff Bidang IT',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-permissions.index'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('Manajemen Hak Akses Pengguna');
    }

    public function test_admin_can_view_edit_permissions_page(): void
    {
        $member = Member::factory()->create([
            'name' => 'Budi Santoso',
            'position' => 'Staff Bidang Humas',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.user-permissions.edit', $member->id));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Hak Akses Pengguna');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Template Preset Cepat');
    }

    public function test_admin_can_update_member_to_custom_permissions(): void
    {
        $member = Member::factory()->create([
            'name' => 'Citra Lestari',
            'position' => 'Anggota',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.user-permissions.update', $member->id), [
                'mode' => 'custom',
                'allowed_menus' => ['parking_locations.view', 'parking_tickets.view', 'parking_tickets.scan'],
                'can_delete' => '0',
                'can_export' => '1',
            ]);

        $response->assertRedirect(route('admin.user-permissions.index'));
        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertTrue($member->hasCustomPermissions());
        $this->assertTrue($member->hasPermission('parking_locations.view'));
        $this->assertTrue($member->hasPermission('parking_tickets.scan'));
        $this->assertFalse($member->hasPermission('articles.view'));
        $this->assertFalse($member->canDeleteData());
        $this->assertTrue($member->canExportData());
    }

    public function test_admin_can_reset_member_permissions_to_default(): void
    {
        $member = Member::factory()->create([
            'name' => 'Dewi Anggraini',
            'position' => 'Staff Bidang Logistik',
            'custom_permissions' => [
                'mode' => 'custom',
                'allowed_menus' => ['parking_locations.view'],
                'can_delete' => false,
                'can_export' => false,
            ],
        ]);

        $this->assertTrue($member->hasCustomPermissions());

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.user-permissions.reset', $member->id));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertFalse($member->hasCustomPermissions());
        $this->assertNull($member->custom_permissions);
    }

    public function test_staff_cannot_access_user_permissions_management(): void
    {
        $staffMember = Member::factory()->create([
            'name' => 'Eko Prasetyo',
            'position' => 'Staff Bidang IT',
        ]);

        $response = $this->actingAs($staffMember)
            ->get(route('admin.user-permissions.index'));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_custom_permissions_middleware_enforces_route_access(): void
    {
        $parkingOfficer = Member::factory()->create([
            'name' => 'Fajar Gunawan',
            'position' => 'Anggota',
            'custom_permissions' => [
                'mode' => 'custom',
                'allowed_menus' => ['parking_locations.view', 'parking_tickets.view'],
                'can_delete' => false,
                'can_export' => false,
            ],
        ]);

        // Accessing allowed route
        $allowedResponse = $this->actingAs($parkingOfficer)
            ->get(route('admin.parking.index'));
        $allowedResponse->assertStatus(200);

        // Accessing disallowed route (e.g. articles)
        $deniedResponse = $this->actingAs($parkingOfficer)
            ->get(route('admin.articles.index'));
        $deniedResponse->assertStatus(302);
        $deniedResponse->assertSessionHas('error');
    }

    public function test_custom_permissions_middleware_blocks_unauthorized_delete(): void
    {
        $member = Member::factory()->create([
            'name' => 'Gita Gutawa',
            'position' => 'Anggota',
            'custom_permissions' => [
                'mode' => 'custom',
                'allowed_menus' => ['parking_locations.view', 'parking_locations.manage'],
                'can_delete' => false, // No delete permission
                'can_export' => false,
            ],
        ]);

        $location = ParkingLocation::create([
            'name' => 'Zona Lapangan A',
            'capacity_car' => 10,
            'capacity_motorcycle' => 20,
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)
            ->delete(route('admin.parking.locations.destroy', $location->id));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('parking_locations', ['id' => $location->id]);
    }
}

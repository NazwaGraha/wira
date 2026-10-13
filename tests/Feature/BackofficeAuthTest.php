<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BackofficeAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test super user can login using email and password, even without database record.
     */
    public function test_superuser_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'login_id' => 'nazwagraha@gmail.com',
            'password' => '*Kake1978#!!!',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isSuperUser());
        $this->assertTrue(auth()->user()->canDelete());
    }

    /**
     * Test non-superuser email is rejected and redirected to homepage.
     */
    public function test_email_login_other_than_superuser_is_rejected_and_stays_on_home(): void
    {
        $response = $this->post('/admin/login', [
            'login_id' => 'randomuser@gmail.com',
            'password' => 'somepassword',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('auth_error');
        $this->assertGuest();
    }

    /**
     * Test first-time login when NRI is valid but password is null redirects to activation page.
     */
    public function test_first_time_login_with_null_password_redirects_to_activation(): void
    {
        $member = Member::factory()->create([
            'nri' => 'TEST.2026.001',
            'password' => null,
            'name' => 'Anggota Baru',
            'position' => 'Anggota',
        ]);

        $response = $this->post('/admin/login', [
            'login_id' => 'TEST.2026.001',
            'password' => 'anypassword',
        ]);

        $response->assertRedirect(route('admin.activate', ['nri' => 'TEST.2026.001']));
        $this->assertGuest();
    }

    /**
     * Test member can activate account and create password for first-time login.
     */
    public function test_member_can_activate_account_and_create_password(): void
    {
        $member = Member::factory()->create([
            'nri' => 'TEST.2026.002',
            'password' => null,
            'name' => 'Siswa Baru',
        ]);

        $response = $this->post('/admin/aktivasi-akun', [
            'nri' => 'TEST.2026.002',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertTrue(Hash::check('password123', $member->password));
    }

    /**
     * Test ordinary member after login is redirected to client area and cannot access backoffice.
     */
    public function test_ordinary_member_redirects_to_client_area_and_cannot_access_backoffice(): void
    {
        $member = Member::factory()->create([
            'nri' => 'TEST.2026.003',
            'password' => Hash::make('password123'),
            'name' => 'Anggota Biasa',
            'position' => 'Anggota',
        ]);

        $response = $this->post('/admin/login', [
            'login_id' => 'TEST.2026.003',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('member.profile'));
        $this->assertAuthenticatedAs($member);

        // Attempt to access admin dashboard should be blocked and redirected back to client area
        $adminResponse = $this->get('/admin/dashboard');
        $adminResponse->assertRedirect(route('member.profile'));
    }

    /**
     * Test Pembina with valid credentials can access backoffice with full CRUD.
     */
    public function test_pembina_can_access_backoffice_with_full_crud(): void
    {
        $pembina = Member::factory()->create([
            'nri' => 'PEMBINA.001',
            'nip' => '198001012005011001',
            'password' => Hash::make('password123'),
            'name' => 'Bapak Pembina',
            'position' => 'Pembina PMR',
        ]);

        // Login using NIP
        $response = $this->post('/admin/login', [
            'login_id' => '198001012005011001',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue($pembina->canDelete());
        $this->assertTrue($pembina->canAccessBackoffice());
    }

    /**
     * Test Ketua Bidang has CRU access but cannot delete data.
     */
    public function test_ketua_bidang_cannot_delete_data(): void
    {
        $ketuaBidang = Member::factory()->create([
            'nri' => 'KETUA.MARKAS.001',
            'password' => Hash::make('password123'),
            'name' => 'Ketua Bidang Markas',
            'position' => 'Ketua Bidang Markas',
        ]);

        $this->assertFalse($ketuaBidang->canDelete());

        // Acting as Ketua Bidang and trying to delete a member
        $targetMember = Member::factory()->create(['name' => 'Target Delete']);
        $deleteResponse = $this->actingAs($ketuaBidang)->delete("/admin/members/{$targetMember->id}");

        $deleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('members', ['id' => $targetMember->id]);
    }

    /**
     * Test Staff Bidang is restricted to articles module only.
     */
    public function test_staff_bidang_is_restricted_to_articles_module_only(): void
    {
        $staffBidang = Member::factory()->create([
            'nri' => 'STAFF.MARKAS.001',
            'password' => Hash::make('password123'),
            'name' => 'Staff Bidang Markas',
            'position' => 'Staf Bidang Markas',
        ]);

        $this->assertTrue($staffBidang->canOnlyManageArticles());

        // Can access articles index
        $response = $this->actingAs($staffBidang)->get('/admin/artikel');
        $response->assertStatus(200);

        // Cannot access members management
        $blockedResponse = $this->actingAs($staffBidang)->get('/admin/members');
        $blockedResponse->assertRedirect(route('admin.articles.index'));
        $blockedResponse->assertSessionHas('error');
    }
}

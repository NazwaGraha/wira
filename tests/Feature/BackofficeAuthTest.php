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

        // Verifikasi request berikutnya ke /admin/dashboard tetap authenticated dan 200 OK
        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Super Administrator');
    }

    /**
     * Test temporary emergency admin can login with NRI 'admin' and password 'pmrwirasmanic' and has full CRUD.
     */
    public function test_temporary_admin_can_login_with_nri_admin_and_has_full_crud(): void
    {
        $response = $this->post('/admin/login', [
            'login_id' => 'admin',
            'password' => 'pmrwirasmanic',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isSuperUser());
        $this->assertTrue(auth()->user()->canDelete());
        $this->assertFalse(auth()->user()->canOnlyManageArticles());

        // Verifikasi sesi bertahan pada request dashboard
        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Administrator');
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
     * Test ordinary active member can login to backoffice and access member data menu, but restricted from other modules.
     */
    public function test_active_ordinary_member_can_access_backoffice_and_member_data_menu(): void
    {
        $member = Member::factory()->create([
            'nri' => 'TEST.2026.003',
            'password' => Hash::make('password123'),
            'name' => 'Anggota Biasa Aktif',
            'position' => 'Anggota',
            'member_status' => 'Aktif',
            'entry_year' => (int) date('Y'),
        ]);

        $this->assertTrue($member->isAktif());
        $this->assertFalse($member->isPurna());
        $this->assertTrue($member->canAccessBackoffice());
        $this->assertTrue($member->canManageMembers());
        $this->assertFalse($member->canDelete());

        $response = $this->post('/admin/login', [
            'login_id' => 'TEST.2026.003',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($member);

        // Buka Daftar Anggota
        $membersResponse = $this->get('/admin/members');
        $membersResponse->assertOk();

        // Buka Input Data Anggota
        $createResponse = $this->get('/admin/members/create');
        $createResponse->assertOk();

        // Coba akses modul lain (misal pengaturan lomba) harus dibatasi
        $blockedResponse = $this->get('/admin/competition-event');
        $blockedResponse->assertRedirect(route('admin.members.index'));
        $blockedResponse->assertSessionHas('error');
    }

    /**
     * Test purna member is denied access to backoffice and redirected to client area.
     */
    public function test_purna_member_is_denied_access_to_backoffice(): void
    {
        $purnaMember = Member::factory()->create([
            'nri' => 'TEST.PURNA.001',
            'password' => Hash::make('password123'),
            'name' => 'Anggota Purna',
            'position' => 'Anggota',
            'member_status' => 'Purna',
            'entry_year' => 2020,
        ]);

        $this->assertTrue($purnaMember->isPurna());
        $this->assertFalse($purnaMember->isAktif());
        $this->assertFalse($purnaMember->canAccessBackoffice());

        $response = $this->post('/admin/login', [
            'login_id' => 'TEST.PURNA.001',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('member.profile'));
        $this->assertAuthenticatedAs($purnaMember);

        // Akses langsung ke backoffice ditolak dan dialihkan ke profil
        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertRedirect(route('member.profile'));
        $dashboardResponse->assertSessionHas('error');
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
            'member_status' => 'Aktif',
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
            'member_status' => 'Aktif',
        ]);

        $this->assertFalse($ketuaBidang->canDelete());

        // Acting as Ketua Bidang and trying to delete a member
        $targetMember = Member::factory()->create(['name' => 'Target Delete']);
        $deleteResponse = $this->actingAs($ketuaBidang)->delete("/admin/members/{$targetMember->id}");

        $deleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('members', ['id' => $targetMember->id]);
    }

    /**
     * Test Staff Bidang can access articles and member data, but restricted from other modules.
     */
    public function test_staff_bidang_can_access_articles_and_member_data(): void
    {
        $staffBidang = Member::factory()->create([
            'nri' => 'STAFF.MARKAS.001',
            'password' => Hash::make('password123'),
            'name' => 'Staff Bidang Markas',
            'position' => 'Staf Bidang Markas',
            'member_status' => 'Aktif',
        ]);

        $this->assertTrue($staffBidang->canOnlyManageArticles());
        $this->assertTrue($staffBidang->canManageMembers());

        // Can access articles index
        $response = $this->actingAs($staffBidang)->get('/admin/artikel');
        $response->assertStatus(200);

        // Can access members management
        $membersResponse = $this->actingAs($staffBidang)->get('/admin/members');
        $membersResponse->assertStatus(200);

        // Can access input members
        $createResponse = $this->actingAs($staffBidang)->get('/admin/members/create');
        $createResponse->assertStatus(200);

        // Cannot access competition settings
        $blockedResponse = $this->actingAs($staffBidang)->get('/admin/competition-event');
        $blockedResponse->assertRedirect(route('admin.articles.index'));
        $blockedResponse->assertSessionHas('error');
    }
}

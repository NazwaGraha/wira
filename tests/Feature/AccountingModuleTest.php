<?php

namespace Tests\Feature;

use App\Auth\PmrUserProvider;
use App\Models\AccountingAccount;
use App\Models\AccountingProject;
use App\Models\AccountingTransaction;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use App\Models\ParkingLocation;
use App\Models\ParkingTicket;
use App\Models\User;
use Database\Seeders\AccountingInitialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountingModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Bendahara Umum PMR',
            'username' => 'bendahara_pmr',
            'email' => 'bendahara@pmr.test',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->seed(AccountingInitialSeeder::class);
    }

    public function test_admin_can_access_accounting_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.accounting.index'));

        $response->assertStatus(200);
        $response->assertSee('Akuntansi &amp; Kas', false);
        $response->assertSee('PRJ-2026-SBB4');
        $response->assertSee('Kas Masuk');
        $response->assertSee('Kas Keluar');
    }

    public function test_admin_can_access_chart_of_accounts_and_create_account(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.accounting.accounts'));
        $response->assertStatus(200);
        $response->assertSee('Bagan Akun (Chart of Accounts)');

        $postData = [
            'code' => '5-2999',
            'name' => 'Beban Tak Terduga Lapangan',
            'type' => 'expense',
            'category' => 'Beban Operasional',
            'description' => 'Dana tak terduga penanganan lapangan',
        ];

        $postResponse = $this->actingAs($this->admin)->post(route('admin.accounting.accounts.store'), $postData);
        $postResponse->assertRedirect(route('admin.accounting.accounts'));

        $this->assertDatabaseHas('accounting_accounts', [
            'code' => '5-2999',
            'name' => 'Beban Tak Terduga Lapangan',
            'type' => 'expense',
        ]);
    }

    public function test_admin_can_create_project_and_update_budget(): void
    {
        $postData = [
            'code' => 'PRJ-2026-DIKLAT',
            'name' => 'Pendidikan & Latihan Dasar PMR XXIX',
            'category' => 'diklat',
            'status' => 'active',
            'budget_target_revenue' => 15000000,
            'budget_limit_expense' => 14000000,
            'person_in_charge' => 'Ketua Diklatdas',
            'treasurer_name' => 'Bendahara Diklatdas',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-05',
            'description' => 'Kegiatan pelantikan anggota baru PMR Wira',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.accounting.projects.store'), $postData);
        $response->assertRedirect(route('admin.accounting.projects'));

        $this->assertDatabaseHas('accounting_projects', [
            'code' => 'PRJ-2026-DIKLAT',
            'name' => 'Pendidikan & Latihan Dasar PMR XXIX',
            'status' => 'active',
        ]);
    }

    public function test_cash_in_creates_transaction_with_proper_double_entry_lines(): void
    {
        $project = AccountingProject::where('code', 'PRJ-2026-SBB4')->firstOrFail();
        $cashAccount = AccountingAccount::where('code', '1-1110')->firstOrFail(); // Bank BJB
        $revenueAccount = AccountingAccount::where('code', '4-2030')->firstOrFail(); // Sponsorship & Donatur

        $postData = [
            'accounting_project_id' => $project->id,
            'debit_account_id' => $cashAccount->id,
            'credit_account_id' => $revenueAccount->id,
            'amount' => 2500000,
            'transaction_date' => '2026-11-09',
            'recipient_or_payer' => 'Sponsor Hydro Coco',
            'description' => 'Penerimaan sponsorship minuman kegiatan SBB IV',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.accounting.cash-in.store'), $postData);
        $response->assertRedirect();

        $this->assertDatabaseHas('accounting_transactions', [
            'type' => 'cash_in',
            'accounting_project_id' => $project->id,
            'amount' => 2500000,
            'recipient_or_payer' => 'Sponsor Hydro Coco',
        ]);

        $transaction = AccountingTransaction::where('recipient_or_payer', 'Sponsor Hydro Coco')->firstOrFail();
        $this->assertStringStartsWith('BKM-', $transaction->voucher_number);

        // Verify double-entry lines: Debit Cash/Bank, Credit Revenue
        $this->assertDatabaseHas('accounting_transaction_lines', [
            'accounting_transaction_id' => $transaction->id,
            'accounting_account_id' => $cashAccount->id,
            'debit' => 2500000,
            'credit' => 0,
        ]);

        $this->assertDatabaseHas('accounting_transaction_lines', [
            'accounting_transaction_id' => $transaction->id,
            'accounting_account_id' => $revenueAccount->id,
            'debit' => 0,
            'credit' => 2500000,
        ]);
    }

    public function test_transaction_can_be_created_by_virtual_superuser(): void
    {
        $superUser = PmrUserProvider::createSuperUser();
        $project = AccountingProject::where('code', 'PRJ-2026-SBB4')->firstOrFail();
        $cashAccount = AccountingAccount::where('code', '1-1030')->firstOrFail();
        $revenueAccount = AccountingAccount::where('code', '4-2030')->firstOrFail();

        $postData = [
            'accounting_project_id' => $project->id,
            'debit_account_id' => $cashAccount->id,
            'credit_account_id' => $revenueAccount->id,
            'amount' => 1000000,
            'transaction_date' => '2026-11-09',
            'recipient_or_payer' => 'Duke',
            'description' => 'Uang Sponsor',
        ];

        $response = $this->actingAs($superUser)->post(route('admin.accounting.cash-in.store'), $postData);
        $response->assertRedirect();

        $this->assertDatabaseHas('accounting_transactions', [
            'type' => 'cash_in',
            'created_by_user_id' => 999999999,
            'amount' => 1000000,
            'recipient_or_payer' => 'Duke',
        ]);

        $tx = AccountingTransaction::where('recipient_or_payer', 'Duke')->firstOrFail();
        $this->assertEquals('Super Administrator', $tx->creator_name);
    }

    public function test_cash_out_creates_transaction_with_proper_double_entry_lines(): void
    {
        $project = AccountingProject::where('code', 'PRJ-2026-SBB4')->firstOrFail();
        $cashAccount = AccountingAccount::where('code', '1-1010')->firstOrFail(); // Kas Tunai BPH
        $expenseAccount = AccountingAccount::where('code', '5-2040')->firstOrFail(); // Beban Konsumsi Panitia

        $postData = [
            'accounting_project_id' => $project->id,
            'credit_account_id' => $cashAccount->id,
            'debit_account_id' => $expenseAccount->id,
            'amount' => 750000,
            'transaction_date' => '2026-11-09',
            'recipient_or_payer' => 'Kantin Bu Haji SMAN 1 Ciawi',
            'description' => 'Konsumsi makan siang panitia gladi kotor SBB IV',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.accounting.cash-out.store'), $postData);
        $response->assertRedirect();

        $this->assertDatabaseHas('accounting_transactions', [
            'type' => 'cash_out',
            'accounting_project_id' => $project->id,
            'amount' => 750000,
            'recipient_or_payer' => 'Kantin Bu Haji SMAN 1 Ciawi',
        ]);

        $transaction = AccountingTransaction::where('recipient_or_payer', 'Kantin Bu Haji SMAN 1 Ciawi')->firstOrFail();
        $this->assertStringStartsWith('BKK-', $transaction->voucher_number);

        // Verify double-entry lines: Debit Expense, Credit Cash/Bank
        $this->assertDatabaseHas('accounting_transaction_lines', [
            'accounting_transaction_id' => $transaction->id,
            'accounting_account_id' => $expenseAccount->id,
            'debit' => 750000,
            'credit' => 0,
        ]);

        $this->assertDatabaseHas('accounting_transaction_lines', [
            'accounting_transaction_id' => $transaction->id,
            'accounting_account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => 750000,
        ]);
    }

    public function test_fund_transfer_between_cash_accounts(): void
    {
        $fromAccount = AccountingAccount::where('code', '1-1110')->firstOrFail(); // Bank BJB
        $toAccount = AccountingAccount::where('code', '1-1020')->firstOrFail(); // Kas Kecil (Petty Cash)

        $postData = [
            'source_account_id' => $fromAccount->id,
            'destination_account_id' => $toAccount->id,
            'amount' => 1000000,
            'transaction_date' => '2026-11-09',
            'description' => 'Penarikan tunai dari BJB untuk pengisian kas kecil',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.accounting.transfer.store'), $postData);
        $response->assertRedirect();

        $this->assertDatabaseHas('accounting_transactions', [
            'type' => 'transfer',
            'amount' => 1000000,
        ]);

        $transaction = AccountingTransaction::where('type', 'transfer')->latest('id')->firstOrFail();
        $this->assertStringStartsWith('TRF-', $transaction->voucher_number);

        // Destination Cash debited, Source Cash credited
        $this->assertDatabaseHas('accounting_transaction_lines', [
            'accounting_transaction_id' => $transaction->id,
            'accounting_account_id' => $toAccount->id,
            'debit' => 1000000,
            'credit' => 0,
        ]);

        $this->assertDatabaseHas('accounting_transaction_lines', [
            'accounting_transaction_id' => $transaction->id,
            'accounting_account_id' => $fromAccount->id,
            'debit' => 0,
            'credit' => 1000000,
        ]);
    }

    public function test_admin_can_view_general_ledger_mode_with_running_balance(): void
    {
        $cashAccount = AccountingAccount::where('code', '1-1030')->firstOrFail();
        $revenueAccount = AccountingAccount::where('code', '4-2030')->firstOrFail();
        $project = AccountingProject::where('code', 'PRJ-2026-SBB4')->firstOrFail();

        // 1. Catat Transaksi BKM
        $this->actingAs($this->admin)->post(route('admin.accounting.cash-in.store'), [
            'accounting_project_id' => $project->id,
            'debit_account_id' => $cashAccount->id,
            'credit_account_id' => $revenueAccount->id,
            'amount' => 1000000,
            'transaction_date' => '2026-11-09',
            'recipient_or_payer' => 'Duke',
            'description' => 'Uang Sponsor Kegiatan SBB IV',
        ]);

        // 2. Akses halaman dalam mode Buku Besar
        $response = $this->actingAs($this->admin)->get(route('admin.accounting.transactions', [
            'view_mode' => 'ledger',
            'account_id' => $cashAccount->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Buku Besar (General Ledger)');
        $response->assertSee('1-1030');
        $response->assertSee('Kas Tunai Panitia Lomba (SBB IV)');
        $response->assertSee('Saldo Berjalan');
        $response->assertSee('Uang Sponsor Kegiatan SBB IV');
        $response->assertSee('Rp 1.000.000');
    }

    public function test_financial_reports_page_and_project_pdf(): void
    {
        $project = AccountingProject::where('code', 'PRJ-2026-SBB4')->firstOrFail();

        $response = $this->actingAs($this->admin)->get(route('admin.accounting.reports', ['project_id' => $project->id]));
        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan');
        $response->assertSee('PRJ-2026-SBB4');

        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.accounting.projects.lpj-pdf', $project));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('LAPORAN PERTANGGUNGJAWABAN');
        $pdfResponse->assertSee('Rapat Anggota Tahunan (RAT)');
    }

    public function test_sync_competition_and_parking_revenue(): void
    {
        $event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
        ]);

        CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'school_name' => 'SMAN 1 CIAWI',
            'registration_code' => 'REG-CIAWI-01',
            'level' => 'Wira',
            'status' => 'verified',
            'advisor_name' => 'Pak Pembina',
            'advisor_phone' => '08123456789',
            'total_payment' => 350000,
        ]);

        $location = ParkingLocation::create([
            'name' => 'Kantong Parkir Depan SMAN 1 Ciawi',
            'code' => 'KPA',
            'capacity_r4' => 10,
            'capacity_r2' => 50,
            'status' => 'open',
        ]);

        ParkingTicket::create([
            'ticket_code' => 'TKT-TEST-001',
            'parking_location_id' => $location->id,
            'vehicle_type' => 'r4_car',
            'license_plate' => 'F 1234 ABC',
            'fee_amount' => 10000,
            'status' => 'parked',
            'payment_status' => 'paid',
        ]);

        $compSyncResponse = $this->actingAs($this->admin)->post(route('admin.accounting.sync-competition'));
        $compSyncResponse->assertRedirect();
        $this->assertDatabaseHas('accounting_transactions', [
            'type' => 'cash_in',
            'amount' => 350000,
        ]);

        $parkingSyncResponse = $this->actingAs($this->admin)->post(route('admin.accounting.sync-parking'));
        $parkingSyncResponse->assertRedirect();
        $this->assertDatabaseHas('accounting_transactions', [
            'type' => 'cash_in',
            'amount' => 10000,
        ]);
    }
}

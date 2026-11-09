<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingAccount;
use App\Models\AccountingProject;
use App\Models\AccountingTransaction;
use App\Models\AccountingTransactionLine;
use App\Models\CompetitionRegistration;
use App\Models\ParkingTicket;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountingController extends Controller
{
    /**
     * Dashboard Utama Akuntansi & Pemantauan Proyek (Accurate Style)
     */
    public function index(Request $request): View
    {
        $projects = AccountingProject::orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 2 END")
            ->orderBy('id', 'desc')
            ->get();

        $selectedProjectId = $request->query('project_id');

        // Default ke proyek aktif pertama (misal SBB IV) jika ada
        if (! $selectedProjectId && $projects->isNotEmpty()) {
            $selectedProject = $projects->firstWhere('code', 'PRJ-2026-SBB4') ?: $projects->first();
            $selectedProjectId = $selectedProject?->id;
        } else {
            $selectedProject = $selectedProjectId ? $projects->firstWhere('id', $selectedProjectId) : null;
        }

        // Akun Kas & Bank untuk transaksi cepat
        $cashBankAccounts = AccountingAccount::where('type', 'asset')
            ->where('category', 'cash_and_bank')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        // Akun Pendapatan & Beban
        $revenueAccounts = AccountingAccount::where('type', 'revenue')->where('is_active', true)->orderBy('code')->get();
        $expenseAccounts = AccountingAccount::where('type', 'expense')->where('is_active', true)->orderBy('code')->get();

        // Data KPI & Realisasi
        $totalRevenue = 0;
        $totalExpense = 0;
        $netSurplus = 0;
        $revenueRealization = 0.0;
        $expenseAbsorption = 0.0;
        $availableCash = 0;

        $revenueBreakdown = collect([]);
        $expenseBreakdown = collect([]);
        $recentTransactions = collect([]);

        if ($selectedProject) {
            $totalRevenue = $selectedProject->total_revenue;
            $totalExpense = $selectedProject->total_expense;
            $netSurplus = $selectedProject->net_surplus;
            $revenueRealization = $selectedProject->revenue_realization_percent;
            $expenseAbsorption = $selectedProject->expense_absorption_percent;

            // Rincian Akun Pendapatan pada Proyek ini
            $revenueBreakdown = AccountingTransactionLine::whereHas('transaction', function ($q) use ($selectedProject) {
                $q->where('accounting_project_id', $selectedProject->id)->where('status', 'posted');
            })->whereHas('account', function ($q) {
                $q->where('type', 'revenue');
            })->selectRaw('accounting_account_id, sum(credit) as total')
                ->groupBy('accounting_account_id')
                ->with('account')
                ->get();

            // Rincian Akun Beban pada Proyek ini
            $expenseBreakdown = AccountingTransactionLine::whereHas('transaction', function ($q) use ($selectedProject) {
                $q->where('accounting_project_id', $selectedProject->id)->where('status', 'posted');
            })->whereHas('account', function ($q) {
                $q->where('type', 'expense');
            })->selectRaw('accounting_account_id, sum(debit) as total')
                ->groupBy('accounting_account_id')
                ->with('account')
                ->get();

            // Histori transaksi terbaru proyek
            $recentTransactions = AccountingTransaction::where('accounting_project_id', $selectedProject->id)
                ->with(['lines.account', 'creator'])
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(15)
                ->get();

            // Kas tersedia di tangan panitia proyek (Akun Kas Panitia SBB jika ada)
            $panitiaKasAccount = $cashBankAccounts->firstWhere('code', '1-1030');
            $availableCash = $panitiaKasAccount ? $panitiaKasAccount->current_balance : $cashBankAccounts->sum('current_balance');
        } else {
            // Konsolidasi Semua Proyek / Seluruh Kas Organisasi
            $totalRevenue = (int) AccountingTransactionLine::whereHas('transaction', fn ($q) => $q->where('status', 'posted'))
                ->whereHas('account', fn ($q) => $q->where('type', 'revenue'))
                ->sum('credit');

            $totalExpense = (int) AccountingTransactionLine::whereHas('transaction', fn ($q) => $q->where('status', 'posted'))
                ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
                ->sum('debit');

            $netSurplus = $totalRevenue - $totalExpense;
            $availableCash = $cashBankAccounts->sum('current_balance');

            $recentTransactions = AccountingTransaction::with(['project', 'lines.account', 'creator'])
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(15)
                ->get();
        }

        return view('admin.accounting.index', compact(
            'projects',
            'selectedProject',
            'selectedProjectId',
            'totalRevenue',
            'totalExpense',
            'netSurplus',
            'availableCash',
            'revenueRealization',
            'expenseAbsorption',
            'revenueBreakdown',
            'expenseBreakdown',
            'recentTransactions',
            'cashBankAccounts',
            'revenueAccounts',
            'expenseAccounts'
        ));
    }

    /**
     * Manajemen Master Proyek Kegiatan
     */
    public function projects(Request $request): View
    {
        $projects = AccountingProject::withCount('transactions')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.accounting.projects', compact('projects'));
    }

    public function storeProject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:accounting_projects,code',
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:50',
            'status' => 'required|in:planning,active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget_target_revenue' => 'required|numeric|min:0',
            'budget_limit_expense' => 'required|numeric|min:0',
            'person_in_charge' => 'nullable|string|max:100',
            'treasurer_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $project = AccountingProject::create($validated);

        return redirect()->route('admin.accounting.projects')->with('success', "Proyek kegiatan {$project->name} ({$project->code}) berhasil ditambahkan!");
    }

    public function updateProject(Request $request, AccountingProject $project): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:accounting_projects,code,'.$project->id,
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:50',
            'status' => 'required|in:planning,active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget_target_revenue' => 'required|numeric|min:0',
            'budget_limit_expense' => 'required|numeric|min:0',
            'person_in_charge' => 'nullable|string|max:100',
            'treasurer_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $project->update($validated);

        return redirect()->route('admin.accounting.projects')->with('success', "Data proyek {$project->name} berhasil diperbarui!");
    }

    public function destroyProject(AccountingProject $project): RedirectResponse
    {
        if ($project->transactions()->exists()) {
            return redirect()->route('admin.accounting.projects')->with('error', "Proyek {$project->name} tidak dapat dihapus karena telah memiliki riwayat transaksi jurnal. Anda dapat mengubah statusnya menjadi 'Diarsipkan'.");
        }

        $name = $project->name;
        $project->delete();

        return redirect()->route('admin.accounting.projects')->with('success', "Proyek {$name} berhasil dihapus.");
    }

    /**
     * Master Bagan Akun (Chart of Accounts)
     */
    public function accounts(Request $request): View
    {
        $filterType = $request->query('type', 'all');

        $query = AccountingAccount::with(['parent', 'children']);

        if ($filterType !== 'all') {
            $query->where('type', $filterType);
        }

        $accounts = $query->orderBy('code')->get();

        // Rekapitulasi per golongan
        $countAll = AccountingAccount::count();
        $countAssets = AccountingAccount::where('type', 'asset')->count();
        $countLiabilities = AccountingAccount::where('type', 'liability')->count();
        $countEquity = AccountingAccount::where('type', 'equity')->count();
        $countRevenue = AccountingAccount::where('type', 'revenue')->count();
        $countExpense = AccountingAccount::where('type', 'expense')->count();

        $parentAccounts = AccountingAccount::whereNull('parent_id')->orderBy('code')->get();

        return view('admin.accounting.accounts', compact(
            'accounts',
            'filterType',
            'countAll',
            'countAssets',
            'countLiabilities',
            'countEquity',
            'countRevenue',
            'countExpense',
            'parentAccounts'
        ));
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounting_accounts,code',
            'name' => 'required|string|max:150',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'category' => 'required|string|max:50',
            'parent_id' => 'nullable|exists:accounting_accounts,id',
            'description' => 'nullable|string|max:300',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $account = AccountingAccount::create($validated);

        return redirect()->route('admin.accounting.accounts')->with('success', "Akun {$account->code} - {$account->name} berhasil ditambahkan!");
    }

    public function updateAccount(Request $request, AccountingAccount $account): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounting_accounts,code,'.$account->id,
            'name' => 'required|string|max:150',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'category' => 'required|string|max:50',
            'parent_id' => 'nullable|exists:accounting_accounts,id',
            'description' => 'nullable|string|max:300',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $account->update($validated);

        return redirect()->route('admin.accounting.accounts')->with('success', "Data akun {$account->code} berhasil diperbarui!");
    }

    public function destroyAccount(AccountingAccount $account): RedirectResponse
    {
        if ($account->lines()->exists()) {
            return redirect()->route('admin.accounting.accounts')->with('error', "Akun {$account->code} - {$account->name} tidak dapat dihapus karena sudah memiliki histori jurnal transaksi.");
        }

        $code = $account->code;
        $name = $account->name;
        $account->delete();

        return redirect()->route('admin.accounting.accounts')->with('success', "Akun {$code} - {$name} berhasil dihapus.");
    }

    /**
     * Histori Voucher Kas & Buku Besar (General Ledger)
     */
    public function transactions(Request $request): View
    {
        $viewMode = $request->query('view_mode', 'voucher'); // 'voucher' or 'ledger'
        $selectedProjectId = $request->query('project_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Master data untuk formulir modal & filter
        $projects = AccountingProject::orderBy('name')->get();
        $cashBankAccounts = AccountingAccount::where('type', 'asset')
            ->where('category', 'cash_and_bank')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        $revenueAccounts = AccountingAccount::where('type', 'revenue')->where('is_active', true)->orderBy('code')->get();
        $expenseAccounts = AccountingAccount::where('type', 'expense')->where('is_active', true)->orderBy('code')->get();
        $allAccounts = AccountingAccount::where('is_active', true)->orderBy('code')->get();

        // 1. DATA UNTUK MODE JURNAL VOUCHER KRONOLOGIS
        $query = AccountingTransaction::with(['project', 'lines.account', 'creator']);

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('project_id')) {
            $query->where('accounting_project_id', $request->query('project_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->query('date_to'));
        }

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('voucher_number', 'LIKE', "%{$s}%")
                    ->orWhere('description', 'LIKE', "%{$s}%")
                    ->orWhere('recipient_or_payer', 'LIKE', "%{$s}%");
            });
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        // 2. DATA UNTUK MODE BUKU BESAR (GENERAL LEDGER PER AKUN)
        $selectedAccountId = $request->query('account_id');
        if (! $selectedAccountId) {
            $defaultAcc = $cashBankAccounts->firstWhere('code', '1-1030') ?: $cashBankAccounts->first() ?: $allAccounts->first();
            $selectedAccountId = $defaultAcc?->id;
        }

        $selectedAccount = $selectedAccountId ? $allAccounts->firstWhere('id', $selectedAccountId) : null;
        $ledgerLines = collect([]);
        $openingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $closingBalance = 0;

        if ($selectedAccount) {
            $normalBalance = $selectedAccount->normal_balance;

            // Saldo Awal sebelum $dateFrom jika difilter tanggal mulai
            if ($dateFrom) {
                $priorLines = AccountingTransactionLine::where('accounting_account_id', $selectedAccount->id)
                    ->whereHas('transaction', function ($q) use ($dateFrom, $selectedProjectId) {
                        $q->where('status', 'posted')
                            ->whereDate('transaction_date', '<', $dateFrom);
                        if ($selectedProjectId) {
                            $q->where('accounting_project_id', $selectedProjectId);
                        }
                    })->get();

                $priorDebit = (int) $priorLines->sum('debit');
                $priorCredit = (int) $priorLines->sum('credit');
                $openingBalance = $normalBalance === 'debit' ? ($priorDebit - $priorCredit) : ($priorCredit - $priorDebit);
            }

            // Transaksi periode
            $ledgerQuery = AccountingTransactionLine::where('accounting_account_id', $selectedAccount->id)
                ->whereHas('transaction', function ($q) use ($dateFrom, $dateTo, $selectedProjectId) {
                    $q->where('status', 'posted');
                    if ($dateFrom) {
                        $q->whereDate('transaction_date', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $q->whereDate('transaction_date', '<=', $dateTo);
                    }
                    if ($selectedProjectId) {
                        $q->where('accounting_project_id', $selectedProjectId);
                    }
                })
                ->with(['transaction.project', 'transaction.creator']);

            // Urutkan kronologis naik untuk kalkulasi saldo berjalan yang presisi
            $rawLines = $ledgerQuery->get()->sortBy(function ($line) {
                return $line->transaction->transaction_date->format('Y-m-d').'_'.str_pad($line->transaction->id, 8, '0', STR_PAD_LEFT);
            });

            $running = $openingBalance;
            foreach ($rawLines as $line) {
                $totalDebit += (int) $line->debit;
                $totalCredit += (int) $line->credit;
                if ($normalBalance === 'debit') {
                    $running += (int) $line->debit - (int) $line->credit;
                } else {
                    $running += (int) $line->credit - (int) $line->debit;
                }
                $line->running_balance = $running;
            }

            $closingBalance = $running;
            $ledgerLines = $rawLines;
        }

        return view('admin.accounting.transactions', compact(
            'viewMode',
            'transactions',
            'projects',
            'cashBankAccounts',
            'revenueAccounts',
            'expenseAccounts',
            'allAccounts',
            'selectedAccount',
            'selectedAccountId',
            'ledgerLines',
            'openingBalance',
            'totalDebit',
            'totalCredit',
            'closingBalance',
            'selectedProjectId',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Input Bukti Kas Masuk (Receive Money - BKM)
     */
    public function storeCashIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'accounting_project_id' => 'nullable|exists:accounting_projects,id',
            'debit_account_id' => 'required|exists:accounting_accounts,id', // Kas / Bank tujuan
            'credit_account_id' => 'required|exists:accounting_accounts,id', // Sumber Pendapatan
            'amount' => 'required|numeric|min:1',
            'recipient_or_payer' => 'nullable|string|max:150',
            'description' => 'required|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('accounting/attachments', 'public');
        }

        DB::transaction(function () use ($validated, $attachmentPath) {
            $voucherNumber = AccountingTransaction::generateVoucherNumber('cash_in', $validated['transaction_date']);

            $tx = AccountingTransaction::create([
                'voucher_number' => $voucherNumber,
                'type' => 'cash_in',
                'transaction_date' => $validated['transaction_date'],
                'accounting_project_id' => $validated['accounting_project_id'] ?? null,
                'recipient_or_payer' => $validated['recipient_or_payer'] ?? 'Penyetor',
                'description' => $validated['description'],
                'amount' => (int) $validated['amount'],
                'reference_number' => $validated['reference_number'] ?? null,
                'attachment_path' => $attachmentPath,
                'created_by_user_id' => Auth::id(),
                'status' => 'posted',
            ]);

            // Line 1: Debit Akun Kas/Bank
            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $validated['debit_account_id'],
                'debit' => (int) $validated['amount'],
                'credit' => 0,
                'memo' => 'Penerimaan Kas - '.$tx->voucher_number,
            ]);

            // Line 2: Kredit Akun Pendapatan
            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $validated['credit_account_id'],
                'debit' => 0,
                'credit' => (int) $validated['amount'],
                'memo' => $tx->description,
            ]);
        });

        return back()->with('success', 'Bukti Kas Masuk (BKM) berhasil disimpan dan diposting ke buku besar!');
    }

    /**
     * Input Bukti Kas Keluar (Pay Money - BKK)
     */
    public function storeCashOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'accounting_project_id' => 'nullable|exists:accounting_projects,id',
            'credit_account_id' => 'required|exists:accounting_accounts,id', // Kas / Bank sumber
            'debit_account_id' => 'required|exists:accounting_accounts,id', // Alokasi Akun Beban
            'amount' => 'required|numeric|min:1',
            'recipient_or_payer' => 'nullable|string|max:150', // Nama Vendor / Toko
            'description' => 'required|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('accounting/attachments', 'public');
        }

        DB::transaction(function () use ($validated, $attachmentPath) {
            $voucherNumber = AccountingTransaction::generateVoucherNumber('cash_out', $validated['transaction_date']);

            $tx = AccountingTransaction::create([
                'voucher_number' => $voucherNumber,
                'type' => 'cash_out',
                'transaction_date' => $validated['transaction_date'],
                'accounting_project_id' => $validated['accounting_project_id'] ?? null,
                'recipient_or_payer' => $validated['recipient_or_payer'] ?? 'Penerima / Vendor',
                'description' => $validated['description'],
                'amount' => (int) $validated['amount'],
                'reference_number' => $validated['reference_number'] ?? null,
                'attachment_path' => $attachmentPath,
                'created_by_user_id' => Auth::id(),
                'status' => 'posted',
            ]);

            // Line 1: Debit Akun Beban/Belanja
            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $validated['debit_account_id'],
                'debit' => (int) $validated['amount'],
                'credit' => 0,
                'memo' => $tx->description,
            ]);

            // Line 2: Kredit Akun Kas/Bank
            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $validated['credit_account_id'],
                'debit' => 0,
                'credit' => (int) $validated['amount'],
                'memo' => 'Pengeluaran Kas - '.$tx->voucher_number,
            ]);
        });

        return back()->with('success', 'Bukti Kas Keluar (BKK) berhasil disimpan dan diposting ke buku besar!');
    }

    /**
     * Pemindahan Kas Antar Rekening (Fund Transfer - TRF)
     */
    public function storeTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'source_account_id' => 'required|exists:accounting_accounts,id',
            'destination_account_id' => 'required|exists:accounting_accounts,id|different:source_account_id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $voucherNumber = AccountingTransaction::generateVoucherNumber('transfer', $validated['transaction_date']);

            $tx = AccountingTransaction::create([
                'voucher_number' => $voucherNumber,
                'type' => 'transfer',
                'transaction_date' => $validated['transaction_date'],
                'recipient_or_payer' => 'Transfer Internal Kas',
                'description' => $validated['description'],
                'amount' => (int) $validated['amount'],
                'created_by_user_id' => Auth::id(),
                'status' => 'posted',
            ]);

            // Line 1: Debit Akun Tujuan
            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $validated['destination_account_id'],
                'debit' => (int) $validated['amount'],
                'credit' => 0,
                'memo' => 'Penerimaan Transfer - '.$tx->voucher_number,
            ]);

            // Line 2: Kredit Akun Sumber
            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $validated['source_account_id'],
                'debit' => 0,
                'credit' => (int) $validated['amount'],
                'memo' => 'Pengeluaran Transfer - '.$tx->voucher_number,
            ]);
        });

        return back()->with('success', 'Transfer kas antar rekening berhasil dibukukan!');
    }

    public function destroyTransaction(AccountingTransaction $transaction): RedirectResponse
    {
        $voucher = $transaction->voucher_number;
        if ($transaction->attachment_path) {
            Storage::disk('public')->delete($transaction->attachment_path);
        }
        $transaction->delete();

        return back()->with('success', "Transaksi voucher {$voucher} berhasil dihapus dari jurnal.");
    }

    /**
     * Laporan Keuangan Standar (Laba Rugi, Neraca, Arus Kas, Perubahan Modal, CALK, LPJ Proyek)
     */
    public function reports(Request $request): View
    {
        $rawReportType = $request->query('report_type', 'income_statement');
        if ($rawReportType === 'general') {
            $rawReportType = 'income_statement';
        }
        $validTypes = ['income_statement', 'balance_sheet', 'cash_flow', 'equity_changes', 'calk', 'project'];
        $reportType = in_array($rawReportType, $validTypes) ? $rawReportType : 'income_statement';

        $projectId = $request->query('project_id');
        $dateFrom = $request->query('date_from', Carbon::now()->startOfYear()->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->toDateString());

        $projects = AccountingProject::orderBy('name')->get();
        $selectedProject = $projectId ? $projects->firstWhere('id', $projectId) : ($reportType === 'project' ? ($projects->firstWhere('code', 'PRJ-2026-SBB4') ?: $projects->first()) : null);

        // 1. DATA PENDAPATAN & BEBAN (LABA RUGI & UMUM)
        $revenueQuery = AccountingTransactionLine::whereHas('transaction', function ($q) use ($dateFrom, $dateTo, $reportType, $selectedProject) {
            $q->where('status', 'posted');
            if ($reportType === 'project' && $selectedProject) {
                $q->where('accounting_project_id', $selectedProject->id);
            } else {
                $q->whereDate('transaction_date', '>=', $dateFrom)
                    ->whereDate('transaction_date', '<=', $dateTo);
            }
        })->whereHas('account', fn ($q) => $q->where('type', 'revenue'))
            ->selectRaw('accounting_account_id, sum(credit) - sum(debit) as total')
            ->groupBy('accounting_account_id')
            ->with('account');

        $expenseQuery = AccountingTransactionLine::whereHas('transaction', function ($q) use ($dateFrom, $dateTo, $reportType, $selectedProject) {
            $q->where('status', 'posted');
            if ($reportType === 'project' && $selectedProject) {
                $q->where('accounting_project_id', $selectedProject->id);
            } else {
                $q->whereDate('transaction_date', '>=', $dateFrom)
                    ->whereDate('transaction_date', '<=', $dateTo);
            }
        })->whereHas('account', fn ($q) => $q->where('type', 'expense'))
            ->selectRaw('accounting_account_id, sum(debit) - sum(credit) as total')
            ->groupBy('accounting_account_id')
            ->with('account');

        $revenueLines = $revenueQuery->get()->filter(fn ($l) => (int) $l->total != 0);
        $expenseLines = $expenseQuery->get()->filter(fn ($l) => (int) $l->total != 0);

        $totalRevenue = (int) $revenueLines->sum('total');
        $totalExpense = (int) $expenseLines->sum('total');
        $netSurplus = $totalRevenue - $totalExpense;

        // 2. DATA NERACA / POSISI KEUANGAN (As of $dateTo)
        $assetAccounts = AccountingAccount::where('type', 'asset')->orderBy('code')->get();
        $assetLines = [];
        foreach ($assetAccounts as $acc) {
            $bal = (int) (AccountingTransactionLine::where('accounting_account_id', $acc->id)
                ->whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<=', $dateTo))
                ->selectRaw('sum(debit) - sum(credit) as bal')
                ->value('bal') ?? 0);

            if ($bal != 0 || $acc->category === 'cash_and_bank') {
                $assetLines[] = [
                    'account' => $acc,
                    'balance' => $bal,
                ];
            }
        }
        $cashBankLines = collect($assetLines)->filter(fn ($i) => $i['account']->category === 'cash_and_bank');
        $otherCurrentAssetLines = collect($assetLines)->filter(fn ($i) => $i['account']->category === 'current_asset');
        $fixedAssetLines = collect($assetLines)->filter(fn ($i) => $i['account']->category === 'fixed_asset');

        $totalCashBank = (int) $cashBankLines->sum('balance');
        $totalOtherCurrentAssets = (int) $otherCurrentAssetLines->sum('balance');
        $totalCurrentAssets = $totalCashBank + $totalOtherCurrentAssets;
        $totalFixedAssets = (int) $fixedAssetLines->sum('balance');
        $totalAssets = $totalCurrentAssets + $totalFixedAssets;

        $liabilityAccounts = AccountingAccount::where('type', 'liability')->orderBy('code')->get();
        $liabilityLines = [];
        foreach ($liabilityAccounts as $acc) {
            $bal = (int) (AccountingTransactionLine::where('accounting_account_id', $acc->id)
                ->whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<=', $dateTo))
                ->selectRaw('sum(credit) - sum(debit) as bal')
                ->value('bal') ?? 0);

            if ($bal != 0) {
                $liabilityLines[] = [
                    'account' => $acc,
                    'balance' => $bal,
                ];
            }
        }
        $totalLiabilities = (int) collect($liabilityLines)->sum('balance');

        $equityAccounts = AccountingAccount::where('type', 'equity')->orderBy('code')->get();
        $equityLines = [];
        foreach ($equityAccounts as $acc) {
            $bal = (int) (AccountingTransactionLine::where('accounting_account_id', $acc->id)
                ->whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<=', $dateTo))
                ->selectRaw('sum(credit) - sum(debit) as bal')
                ->value('bal') ?? 0);

            if ($bal != 0 || $acc->code === '3-1010') {
                $equityLines[] = [
                    'account' => $acc,
                    'balance' => $bal,
                ];
            }
        }
        $baseEquity = (int) collect($equityLines)->sum('balance');

        $cumulativeRevenue = (int) (AccountingTransactionLine::whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<=', $dateTo))
            ->whereHas('account', fn ($q) => $q->where('type', 'revenue'))
            ->selectRaw('sum(credit) - sum(debit) as total')
            ->value('total') ?? 0);

        $cumulativeExpense = (int) (AccountingTransactionLine::whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<=', $dateTo))
            ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
            ->selectRaw('sum(debit) - sum(credit) as total')
            ->value('total') ?? 0);

        $cumulativeSurplus = $cumulativeRevenue - $cumulativeExpense;
        $totalEquity = $baseEquity + $cumulativeSurplus;
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $isBalanced = ($totalAssets === $totalLiabilitiesAndEquity);

        // 3. DATA ARUS KAS (CASH FLOW STATEMENT)
        $cashAccountIds = AccountingAccount::where('type', 'asset')->where('category', 'cash_and_bank')->pluck('id');
        $openingCash = (int) (AccountingTransactionLine::whereIn('accounting_account_id', $cashAccountIds)
            ->whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<', $dateFrom))
            ->selectRaw('sum(debit) - sum(credit) as total')
            ->value('total') ?? 0);

        $operatingInflowTransactions = AccountingTransaction::where('type', 'cash_in')
            ->where('status', 'posted')
            ->whereDate('transaction_date', '>=', $dateFrom)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->with(['lines.account'])
            ->get();

        $cashInflowSummary = [];
        foreach ($operatingInflowTransactions as $tx) {
            $contraLine = $tx->lines->first(fn ($l) => ! in_array($l->accounting_account_id, $cashAccountIds->toArray()));
            $accName = $contraLine?->account?->name ?: $tx->description;
            $cashInflowSummary[$accName] = ($cashInflowSummary[$accName] ?? 0) + (int) $tx->amount;
        }

        $operatingOutflowTransactions = AccountingTransaction::where('type', 'cash_out')
            ->where('status', 'posted')
            ->whereDate('transaction_date', '>=', $dateFrom)
            ->whereDate('transaction_date', '<=', $dateTo)
            ->with(['lines.account'])
            ->get();

        $cashOutflowSummary = [];
        foreach ($operatingOutflowTransactions as $tx) {
            $contraLine = $tx->lines->first(fn ($l) => ! in_array($l->accounting_account_id, $cashAccountIds->toArray()));
            $accName = $contraLine?->account?->name ?: $tx->description;
            $cashOutflowSummary[$accName] = ($cashOutflowSummary[$accName] ?? 0) + (int) $tx->amount;
        }

        $totalOperatingIn = array_sum($cashInflowSummary);
        $totalOperatingOut = array_sum($cashOutflowSummary);
        $netOperatingCash = $totalOperatingIn - $totalOperatingOut;
        $netInvestingCash = 0;
        $netFinancingCash = 0;
        $netCashChange = $netOperatingCash + $netInvestingCash + $netFinancingCash;
        $closingCash = $openingCash + $netCashChange;

        // 4. DATA PERUBAHAN MODAL / EKUITAS
        $priorEquityBase = (int) (AccountingTransactionLine::whereHas('account', fn ($q) => $q->where('type', 'equity'))
            ->whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<', $dateFrom))
            ->selectRaw('sum(credit) - sum(debit) as total')
            ->value('total') ?? 0);

        $priorNetSurplus = (int) (AccountingTransactionLine::whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<', $dateFrom))
            ->whereHas('account', fn ($q) => $q->where('type', 'revenue'))
            ->selectRaw('sum(credit) - sum(debit) as total')
            ->value('total') ?? 0) - (int) (AccountingTransactionLine::whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '<', $dateFrom))
            ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
            ->selectRaw('sum(debit) - sum(credit) as total')
            ->value('total') ?? 0);

        $openingEquity = $priorEquityBase + $priorNetSurplus;
        $periodSurplus = $netSurplus;
        $periodDirectEquity = (int) (AccountingTransactionLine::whereHas('account', fn ($q) => $q->where('type', 'equity'))
            ->whereHas('transaction', fn ($q) => $q->where('status', 'posted')->whereDate('transaction_date', '>=', $dateFrom)->whereDate('transaction_date', '<=', $dateTo))
            ->selectRaw('sum(credit) - sum(debit) as total')
            ->value('total') ?? 0);
        $closingEquity = $openingEquity + $periodSurplus + $periodDirectEquity;

        // 5. DATA CALK (Catatan Atas Laporan Keuangan)
        $cashBankAccountsList = AccountingAccount::where('type', 'asset')
            ->where('category', 'cash_and_bank')
            ->orderBy('code')
            ->get();

        return view('admin.accounting.reports', compact(
            'reportType',
            'projects',
            'selectedProject',
            'dateFrom',
            'dateTo',
            'revenueLines',
            'expenseLines',
            'totalRevenue',
            'totalExpense',
            'netSurplus',
            'cashBankLines',
            'otherCurrentAssetLines',
            'fixedAssetLines',
            'totalCashBank',
            'totalOtherCurrentAssets',
            'totalCurrentAssets',
            'totalFixedAssets',
            'totalAssets',
            'liabilityLines',
            'totalLiabilities',
            'equityLines',
            'baseEquity',
            'cumulativeSurplus',
            'totalEquity',
            'totalLiabilitiesAndEquity',
            'isBalanced',
            'openingCash',
            'cashInflowSummary',
            'cashOutflowSummary',
            'totalOperatingIn',
            'totalOperatingOut',
            'netOperatingCash',
            'netInvestingCash',
            'netFinancingCash',
            'netCashChange',
            'closingCash',
            'openingEquity',
            'periodSurplus',
            'periodDirectEquity',
            'closingEquity',
            'cashBankAccountsList'
        ));
    }

    /**
     * Download / Cetak LPJ Keuangan Proyek Resmi (PDF / Printable View)
     */
    public function exportProjectPdf(AccountingProject $project): View
    {
        $revenueLines = AccountingTransactionLine::whereHas('transaction', function ($q) use ($project) {
            $q->where('accounting_project_id', $project->id)->where('status', 'posted');
        })->whereHas('account', fn ($q) => $q->where('type', 'revenue'))
            ->selectRaw('accounting_account_id, sum(credit) as total')
            ->groupBy('accounting_account_id')
            ->with('account')
            ->get();

        $expenseLines = AccountingTransactionLine::whereHas('transaction', function ($q) use ($project) {
            $q->where('accounting_project_id', $project->id)->where('status', 'posted');
        })->whereHas('account', fn ($q) => $q->where('type', 'expense'))
            ->selectRaw('accounting_account_id, sum(debit) as total')
            ->groupBy('accounting_account_id')
            ->with('account')
            ->get();

        $transactions = AccountingTransaction::where('accounting_project_id', $project->id)
            ->with(['lines.account'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        return view('admin.accounting.project-lpj-pdf', compact(
            'project',
            'revenueLines',
            'expenseLines',
            'transactions'
        ));
    }

    /**
     * Sinkronisasi Otomatis Kas dari Pendaftaran Lomba SBB IV
     */
    public function syncCompetitionRevenue(Request $request): RedirectResponse
    {
        $project = AccountingProject::where('code', 'PRJ-2026-SBB4')->first();
        if (! $project) {
            return back()->with('error', 'Proyek PRJ-2026-SBB4 tidak ditemukan.');
        }

        $revenueAccount = AccountingAccount::where('code', '4-2010')->first();
        $cashAccount = AccountingAccount::where('code', '1-1110')->first() ?: AccountingAccount::where('type', 'asset')->where('category', 'cash_and_bank')->first();

        if (! $revenueAccount || ! $cashAccount) {
            return back()->with('error', 'Akun pendapatan pendaftaran (4-2010) atau kas bank belum tersedia.');
        }

        // Hitung total pendaftaran verified
        $verifiedRegistrations = CompetitionRegistration::where('status', 'verified')->get();
        $totalVerifiedAmount = 0;
        foreach ($verifiedRegistrations as $reg) {
            // Hitung total fee dari total_payment
            $totalVerifiedAmount += (int) ($reg->total_payment ?? 0);
        }

        if ($totalVerifiedAmount <= 0) {
            $totalVerifiedAmount = 500000; // Contoh minimal pendaftaran jika belum ada registrasi riil
        }

        // Cek total yang sudah dibukukan di 4-2010
        $alreadyPosted = (int) AccountingTransactionLine::whereHas('transaction', function ($q) use ($project) {
            $q->where('accounting_project_id', $project->id)->where('status', 'posted');
        })->where('accounting_account_id', $revenueAccount->id)->sum('credit');

        $delta = $totalVerifiedAmount - $alreadyPosted;

        if ($delta <= 0) {
            return back()->with('info', 'Seluruh pendapatan pendaftaran kontingen (Rp '.number_format($totalVerifiedAmount, 0, ',', '.').') sudah sinkron dengan pembukuan akuntansi.');
        }

        DB::transaction(function () use ($project, $revenueAccount, $cashAccount, $delta, $totalVerifiedAmount) {
            $voucher = AccountingTransaction::generateVoucherNumber('cash_in');

            $tx = AccountingTransaction::create([
                'voucher_number' => $voucher,
                'type' => 'cash_in',
                'transaction_date' => now(),
                'accounting_project_id' => $project->id,
                'recipient_or_payer' => 'Kontingen Peserta Lomba SBB IV',
                'description' => 'Sinkronisasi Otomatis Pendapatan Pendaftaran Lomba Terverifikasi',
                'amount' => $delta,
                'created_by_user_id' => Auth::id(),
                'status' => 'posted',
            ]);

            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $cashAccount->id,
                'debit' => $delta,
                'credit' => 0,
                'memo' => 'Sinkronisasi Kas Masuk Pendaftaran SBB IV',
            ]);

            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => $delta,
                'memo' => 'Pendapatan Registrasi SBB IV (Akumulasi Rp '.number_format($totalVerifiedAmount, 0, ',', '.').')',
            ]);
        });

        return back()->with('success', 'Pendapatan pendaftaran kontingen sebesar Rp '.number_format($delta, 0, ',', '.').' berhasil disinkronkan ke Proyek SBB IV!');
    }

    /**
     * Sinkronisasi Otomatis Kas dari Karcis Parkir Lapangan SBB IV
     */
    public function syncParkingRevenue(Request $request): RedirectResponse
    {
        $project = AccountingProject::where('code', 'PRJ-2026-SBB4')->first();
        if (! $project) {
            return back()->with('error', 'Proyek PRJ-2026-SBB4 tidak ditemukan.');
        }

        $revenueAccount = AccountingAccount::where('code', '4-2020')->first();
        $cashAccount = AccountingAccount::where('code', '1-1030')->first() ?: AccountingAccount::where('type', 'asset')->where('category', 'cash_and_bank')->first();

        if (! $revenueAccount || ! $cashAccount) {
            return back()->with('error', 'Akun pendapatan parkir (4-2020) atau kas belum tersedia.');
        }

        $totalPaidParking = (int) ParkingTicket::where('payment_status', 'paid')->sum('fee_amount');
        if ($totalPaidParking <= 0) {
            $totalPaidParking = 150000;
        }

        $alreadyPosted = (int) AccountingTransactionLine::whereHas('transaction', function ($q) use ($project) {
            $q->where('accounting_project_id', $project->id)->where('status', 'posted');
        })->where('accounting_account_id', $revenueAccount->id)->sum('credit');

        $delta = $totalPaidParking - $alreadyPosted;

        if ($delta <= 0) {
            return back()->with('info', 'Seluruh pendapatan karcis parkir lapangan (Rp '.number_format($totalPaidParking, 0, ',', '.').') sudah sinkron dengan pembukuan akuntansi.');
        }

        DB::transaction(function () use ($project, $revenueAccount, $cashAccount, $delta, $totalPaidParking) {
            $voucher = AccountingTransaction::generateVoucherNumber('cash_in');

            $tx = AccountingTransaction::create([
                'voucher_number' => $voucher,
                'type' => 'cash_in',
                'transaction_date' => now(),
                'accounting_project_id' => $project->id,
                'recipient_or_payer' => 'Pengunjung & Petugas Parkir Lapangan',
                'description' => 'Sinkronisasi Otomatis Penerimaan Karcis Parkir Lapangan SBB IV',
                'amount' => $delta,
                'created_by_user_id' => Auth::id(),
                'status' => 'posted',
            ]);

            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $cashAccount->id,
                'debit' => $delta,
                'credit' => 0,
                'memo' => 'Penerimaan Kas Karcis Parkir Lapangan SBB IV',
            ]);

            AccountingTransactionLine::create([
                'accounting_transaction_id' => $tx->id,
                'accounting_account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => $delta,
                'memo' => 'Pendapatan Karcis Parkir SBB IV (Akumulasi Rp '.number_format($totalPaidParking, 0, ',', '.').')',
            ]);
        });

        return back()->with('success', 'Pendapatan karcis parkir lapangan sebesar Rp '.number_format($delta, 0, ',', '.').' berhasil disinkronkan ke Proyek SBB IV!');
    }
}

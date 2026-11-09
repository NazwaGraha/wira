<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingAccount;
use App\Models\AccountingProject;
use App\Models\AccountingTransaction;
use App\Models\AccountingTransactionLine;
use App\Models\Activity;
use App\Models\Article;
use App\Models\BloodStock;
use App\Models\Category;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use App\Models\MemberRegistration;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;
use App\Models\ParkingLocation;
use App\Models\ParkingTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalArticles = Article::count();
        $draftArticles = Article::where('status', 'draft')->count();
        $totalViews = Article::sum('views_count');
        $totalCategories = Category::count();
        $totalRegistrations = MemberRegistration::count();

        $recentArticles = Article::with('category')->latest()->take(5)->get();
        $recentRegistrations = MemberRegistration::latest()->take(5)->get();
        $bloodStocks = BloodStock::all();
        $totalOrgMembers = OrganizationMember::count();
        $orgSetting = OrganizationSetting::first();
        $totalActivities = Activity::count();
        $recentActivities = Activity::latest('event_date')->take(3)->get();

        $activeCompetitionEvent = Schema::hasTable('competition_events')
            ? (CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::latest('id')->first())
            : null;

        $totalJuries = (Schema::hasTable('users') && Schema::hasColumn('users', 'role'))
            ? User::where('role', 'jury')->count()
            : 0;

        // Metrik Keuangan & Accounting Wira
        $availableCash = 0;
        $netSurplus = 0;
        $recentTransactions = collect([]);
        $sbbProject = null;

        if (Schema::hasTable('accounting_accounts') && Schema::hasTable('accounting_transactions')) {
            $cashAccountIds = AccountingAccount::where('type', 'asset')
                ->where('category', 'cash_and_bank')
                ->pluck('id');

            $availableCash = (int) (AccountingTransactionLine::whereIn('accounting_account_id', $cashAccountIds)
                ->whereHas('transaction', fn ($q) => $q->where('status', 'posted'))
                ->selectRaw('sum(debit) - sum(credit) as total')
                ->value('total') ?? 0);

            $totalRevenue = (int) (AccountingTransactionLine::whereHas('account', fn ($q) => $q->where('type', 'revenue'))
                ->whereHas('transaction', fn ($q) => $q->where('status', 'posted'))
                ->selectRaw('sum(credit) - sum(debit) as total')
                ->value('total') ?? 0);

            $totalExpense = (int) (AccountingTransactionLine::whereHas('account', fn ($q) => $q->where('type', 'expense'))
                ->whereHas('transaction', fn ($q) => $q->where('status', 'posted'))
                ->selectRaw('sum(debit) - sum(credit) as total')
                ->value('total') ?? 0);

            $netSurplus = $totalRevenue - $totalExpense;

            $recentTransactions = AccountingTransaction::with(['lines.account', 'project'])
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->take(6)
                ->get();

            $sbbProject = AccountingProject::where('code', 'PRJ-2026-SBB4')->first() ?: AccountingProject::first();
        }

        // Metrik Lomba SBB IV & Parkir
        $sbbStats = [
            'total_teams' => 0,
            'verified' => 0,
            'pending' => 0,
        ];
        if (Schema::hasTable('competition_registrations')) {
            $sbbStats['total_teams'] = CompetitionRegistration::count();
            $sbbStats['verified'] = CompetitionRegistration::where('status', 'verified')->count();
            $sbbStats['pending'] = CompetitionRegistration::where('status', 'pending')->count();
        }

        $parkingStats = [
            'total_capacity' => 0,
            'currently_parked' => 0,
            'occupancy_percent' => 0,
        ];
        if (Schema::hasTable('parking_locations') && Schema::hasTable('parking_tickets')) {
            $capR2 = (int) ParkingLocation::sum('capacity_r2');
            $capR4 = (int) ParkingLocation::sum('capacity_r4');
            $parkingStats['total_capacity'] = $capR2 + $capR4;
            $parkingStats['currently_parked'] = ParkingTicket::where('status', 'parked')->count();
            $parkingStats['occupancy_percent'] = $parkingStats['total_capacity'] > 0
                ? (int) round(($parkingStats['currently_parked'] / $parkingStats['total_capacity']) * 100)
                : 0;
        }

        $knowledgePath = storage_path('app/knowledge/knowledge.json');
        $wiraKnowledgeStats = [
            'total_topics' => 0,
            'updated_at' => null,
            'synced' => false,
        ];
        if (File::exists($knowledgePath)) {
            try {
                $rawK = json_decode(File::get($knowledgePath), true);
                if (is_array($rawK)) {
                    $wiraKnowledgeStats['total_topics'] = count($rawK['topics'] ?? []);
                    $wiraKnowledgeStats['updated_at'] = isset($rawK['updated_at']) ? Carbon::parse($rawK['updated_at'])->translatedFormat('d M Y H:i') : null;
                    $wiraKnowledgeStats['synced'] = true;
                }
            } catch (\Throwable $e) {
            }
        }

        return view('admin.dashboard', compact(
            'totalArticles',
            'draftArticles',
            'totalViews',
            'totalCategories',
            'totalRegistrations',
            'recentArticles',
            'recentRegistrations',
            'bloodStocks',
            'totalOrgMembers',
            'orgSetting',
            'totalActivities',
            'recentActivities',
            'wiraKnowledgeStats',
            'activeCompetitionEvent',
            'totalJuries',
            'availableCash',
            'netSurplus',
            'recentTransactions',
            'sbbProject',
            'sbbStats',
            'parkingStats'
        ));
    }
}

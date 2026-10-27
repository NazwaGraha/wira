<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Article;
use App\Models\BloodStock;
use App\Models\Category;
use App\Models\CompetitionEvent;
use App\Models\MemberRegistration;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;
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
            'totalJuries'
        ));
    }
}

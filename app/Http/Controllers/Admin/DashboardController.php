<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Article;
use App\Models\BloodStock;
use App\Models\Category;
use App\Models\MemberRegistration;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;

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
            'recentActivities'
        ));
    }
}

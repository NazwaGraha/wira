<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use Illuminate\Http\Request;

class CompetitionQuotaController extends Controller
{
    public function index(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->first() ?? CompetitionEvent::first();

        $level = $request->query('level');

        $allCategoriesQuery = CompetitionCategory::query()
            ->where('is_active', true);

        if ($event) {
            $allCategoriesQuery->where('competition_event_id', $event->id);
        }

        $allCategories = $allCategoriesQuery->withCount(['teams' => function ($q) {
            $q->whereHas('registration', fn ($rq) => $rq->where('status', '!=', 'rejected'));
        }])->orderBy('order_position')->get();

        // Filtered categories for the table display
        $categories = $allCategories;
        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $categories = $allCategories->where('level', $level)->values();
        }

        // Global KPI Stats Calculation across all active categories in event
        $totalCapacity = 0;
        $totalRegistered = 0;
        $criticalOrFullCount = 0;
        $fullCount = 0;
        $criticalCount = 0;

        foreach ($allCategories as $cat) {
            $cap = $cat->getMaxQuota();
            $reg = $cat->teams_count ?? 0;
            $rem = max(0, $cap - $reg);

            $totalCapacity += $cap;
            $totalRegistered += $reg;

            if ($rem <= 0) {
                $criticalOrFullCount++;
                $fullCount++;
            } elseif ($rem === 1) {
                $criticalOrFullCount++;
                $criticalCount++;
            }
        }

        $totalRemaining = max(0, $totalCapacity - $totalRegistered);
        $percentageUsed = $totalCapacity > 0 ? round(($totalRegistered / $totalCapacity) * 100) : 0;

        return view('admin.competition.quotas.index', compact(
            'event',
            'categories',
            'level',
            'totalCapacity',
            'totalRegistered',
            'totalRemaining',
            'percentageUsed',
            'criticalOrFullCount',
            'fullCount',
            'criticalCount'
        ));
    }

    public function update(Request $request)
    {
        $quotas = $request->input('quotas', []);

        foreach ($quotas as $catId => $data) {
            $category = CompetitionCategory::find($catId);
            if ($category) {
                $category->update([
                    'max_quota' => isset($data['max_quota']) && $data['max_quota'] !== '' ? (int) $data['max_quota'] : 50,
                    'max_teams_per_school_pa' => isset($data['max_teams_per_school_pa']) ? (int) $data['max_teams_per_school_pa'] : 2,
                    'max_teams_per_school_pi' => isset($data['max_teams_per_school_pi']) ? (int) $data['max_teams_per_school_pi'] : 2,
                    'max_teams_per_school_mix' => isset($data['max_teams_per_school_mix']) ? (int) $data['max_teams_per_school_mix'] : 2,
                    'is_quota_enforced' => isset($data['is_quota_enforced']) ? (bool) $data['is_quota_enforced'] : true,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Pengaturan kuota regu dan batasan kontingen sekolah berhasil disimpan!');
    }

    public function quickUpdate(CompetitionCategory $category, Request $request)
    {
        $validated = $request->validate([
            'max_quota' => 'required|integer|min:1|max:500',
            'max_teams_per_school_pa' => 'nullable|integer|min:1|max:20',
            'max_teams_per_school_pi' => 'nullable|integer|min:1|max:20',
            'max_teams_per_school_mix' => 'nullable|integer|min:1|max:20',
        ]);

        $category->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Kuota untuk cabang '{$category->name}' berhasil diperbarui!",
            ]);
        }

        return redirect()->back()->with('success', "Kuota untuk cabang '{$category->name}' berhasil diperbarui!");
    }

    public function resetDefaults(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->first() ?? CompetitionEvent::first();

        if ($event) {
            CompetitionCategory::where('competition_event_id', $event->id)->update([
                'max_quota' => 50,
                'max_teams_per_school_pa' => 2,
                'max_teams_per_school_pi' => 2,
                'max_teams_per_school_mix' => 2,
                'is_quota_enforced' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Seluruh kuota berhasil di-reset ke nilai standar: 50 Regu per Cabang Lomba (Maksimal 2 Regu per Pangkalan).');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionScore;
use Illuminate\Http\Request;

class CompetitionLeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->first();
        if (!$event) {
            $event = CompetitionEvent::first();
        }

        $level = $request->query('level', 'Madya');

        $categories = CompetitionCategory::where('competition_event_id', $event?->id)
            ->where('level', $level)
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();

        // Calculate Juara Umum Matrix
        $matrix = [];
        $schoolTotals = [];

        foreach ($categories as $cat) {
            $topScores = CompetitionScore::where('competition_category_id', $cat->id)
                ->where('is_disqualified', false)
                ->whereIn('rank', [1, 2, 3])
                ->with('team.registration')
                ->get();

            foreach ($topScores as $score) {
                $schoolName = $score->team?->registration?->school_name ?? $score->team?->team_name;
                $cleanSchool = preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $schoolName);

                $points = 0;
                if ($cat->point_tier === 'tier_1') {
                    $points = match ($score->rank) { 1 => 10, 2 => 8, 3 => 6, default => 0 };
                } elseif ($cat->point_tier === 'tier_2') {
                    $points = match ($score->rank) { 1 => 8, 2 => 6, 3 => 4, default => 0 };
                } elseif ($cat->point_tier === 'tier_3') {
                    $points = match ($score->rank) { 1 => 3, 2 => 2, 3 => 1, default => 0 };
                }

                if (!isset($matrix[$cleanSchool])) {
                    $matrix[$cleanSchool] = [
                        'school_name' => $cleanSchool,
                        'categories' => [],
                        'total_points' => 0,
                    ];
                }

                $matrix[$cleanSchool]['categories'][$cat->id] = ($matrix[$cleanSchool]['categories'][$cat->id] ?? 0) + $points;
                $matrix[$cleanSchool]['total_points'] += $points;
            }
        }

        // Also fetch all schools that registered for this level to show in full table if they scored 0 points
        $registeredSchools = \App\Models\CompetitionRegistration::where('competition_event_id', $event?->id)
            ->where('level', $level)
            ->pluck('school_name')
            ->unique();

        foreach ($registeredSchools as $rSchool) {
            $cleanSchool = preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $rSchool);
            if (!isset($matrix[$cleanSchool])) {
                $matrix[$cleanSchool] = [
                    'school_name' => $cleanSchool,
                    'categories' => [],
                    'total_points' => 0,
                ];
            }
        }

        $standings = collect($matrix)->sortByDesc('total_points')->values()->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        return view('admin.competition.leaderboard.index', compact('event', 'level', 'categories', 'standings'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Services\CompetitionSpreadsheetService;
use Illuminate\Http\Request;

class CompetitionLeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $event = $this->resolveEvent($request, $allEvents);
        $level = $request->query('level', 'Madya');

        [$categories, $standings] = $this->calculateStandings($event, $level);

        return view('admin.competition.leaderboard.index', compact('event', 'allEvents', 'level', 'categories', 'standings'));
    }

    public function exportExcel(Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $event = $this->resolveEvent($request, $allEvents);
        $level = $request->query('level', 'Madya');

        [$categories, $standings] = $this->calculateStandings($event, $level);

        $tempFilePath = $spreadsheetService->exportLeaderboardStandingsXlsx($event, $level, $categories, $standings);

        $safeLevel = strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $level));
        $filename = 'Rekapitulasi_Klasemen_Juara_Umum_'.$safeLevel.'_'.date('Ymd_His').'.xlsx';

        return response()->download($tempFilePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    protected function resolveEvent(Request $request, $allEvents): ?CompetitionEvent
    {
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            return CompetitionEvent::find($selectedEventId) ?: (CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first());
        }

        return CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
    }

    protected function calculateStandings(?CompetitionEvent $event, string $level): array
    {
        $categories = CompetitionCategory::where('competition_event_id', $event?->id)
            ->where('level', $level)
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();

        $matrix = [];

        foreach ($categories as $cat) {
            $topScores = CompetitionScore::where('competition_category_id', $cat->id)
                ->where('is_disqualified', false)
                ->whereIn('rank', [1, 2, 3, 4, 5])
                ->with('team.registration')
                ->get();

            foreach ($topScores as $score) {
                $schoolName = $score->team?->registration?->school_name ?? $score->team?->team_name;
                $cleanSchool = preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $schoolName);

                $points = $cat->getPointsForRank($score->rank);

                if (! isset($matrix[$cleanSchool])) {
                    $matrix[$cleanSchool] = [
                        'school_name' => $cleanSchool,
                        'categories' => [],
                        'gold_count' => 0,
                        'silver_count' => 0,
                        'bronze_count' => 0,
                        'total_points' => 0,
                    ];
                }

                if ($score->rank == 1) {
                    $matrix[$cleanSchool]['gold_count']++;
                } elseif ($score->rank == 2) {
                    $matrix[$cleanSchool]['silver_count']++;
                } elseif ($score->rank == 3) {
                    $matrix[$cleanSchool]['bronze_count']++;
                }

                $matrix[$cleanSchool]['categories'][$cat->id] = ($matrix[$cleanSchool]['categories'][$cat->id] ?? 0) + $points;
                $matrix[$cleanSchool]['total_points'] += $points;
            }
        }

        // Ambil sekolah yang terdaftar pada tingkatan ini
        $registeredSchools = CompetitionRegistration::where('competition_event_id', $event?->id)
            ->where('level', $level)
            ->pluck('school_name')
            ->unique();

        foreach ($registeredSchools as $rSchool) {
            $cleanSchool = preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $rSchool);
            if (! isset($matrix[$cleanSchool])) {
                $matrix[$cleanSchool] = [
                    'school_name' => $cleanSchool,
                    'categories' => [],
                    'gold_count' => 0,
                    'silver_count' => 0,
                    'bronze_count' => 0,
                    'total_points' => 0,
                ];
            }
        }

        // Urutkan secara resmi: Poin tertinggi, lalu Emas, lalu Perak, lalu Perunggu
        $standings = collect($matrix)->sort(function ($a, $b) {
            if ($b['total_points'] !== $a['total_points']) {
                return $b['total_points'] <=> $a['total_points'];
            }
            if ($b['gold_count'] !== $a['gold_count']) {
                return $b['gold_count'] <=> $a['gold_count'];
            }
            if ($b['silver_count'] !== $a['silver_count']) {
                return $b['silver_count'] <=> $a['silver_count'];
            }

            return $b['bronze_count'] <=> $a['bronze_count'];
        })->values()->map(function ($item, $index) {
            $item['rank'] = $index + 1;

            return $item;
        });

        return [$categories, $standings];
    }
}

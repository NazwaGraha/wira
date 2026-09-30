<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use Illuminate\Http\Request;

class CompetitionScoreController extends Controller
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
            ->withCount('teams')
            ->orderBy('order_position')
            ->get();

        return view('admin.competition.scores.index', compact('event', 'level', 'categories'));
    }

    public function input(CompetitionCategory $category, Request $request)
    {
        $round = $request->query('round', 'Utama');

        // Fetch or create score rows for all active teams in this category
        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('order_number')
            ->get();

        $scores = CompetitionScore::where('competition_category_id', $category->id)
            ->where('round_name', $round)
            ->get()
            ->keyBy('competition_participant_team_id');

        return view('admin.competition.scores.input', compact('category', 'round', 'teams', 'scores'));
    }

    public function saveScores(CompetitionCategory $category, Request $request)
    {
        $round = $request->input('round_name', 'Utama');
        $scoresData = $request->input('scores', []);

        foreach ($scoresData as $teamId => $data) {
            $isDisqualified = isset($data['is_disqualified']) && $data['is_disqualified'] == '1';
            $finalScore = 0;
            $timeRecorded = $data['time_recorded'] ?? ($data['practical_time'] ?? ($data['written_time'] ?? null));

            // Auto-calculation logic based on category scoring type
            switch ($category->scoring_type) {
                case 'written_practical_time':
                    // e.g. LPP Madya/Wira: Tertulis + Praktik
                    $writtenScore = floatval($data['written_score'] ?? 0);
                    $practicalScore = floatval($data['practical_score'] ?? 0);
                    // Standard scoring formula or direct combined score:
                    // In Excel: e.g. Tertulis 70, Praktik 800 -> 76.5 or custom formula
                    // If practical is out of 1000, 30% written + 70% practical:
                    if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                        $finalScore = floatval($data['manual_final_score']);
                    } else {
                        // Formula: Tertulis (0-100) * 0.3 + (Praktik / 1000 * 100) * 0.7 (or direct average if 0-100)
                        $finalScore = round(($writtenScore * 0.3) + (($practicalScore / 10) * 0.7), 2);
                    }
                    break;

                case 'multi_criteria':
                    // e.g. Mading Kreasi, Mewarnai
                    $val1 = floatval($data['criteria_1'] ?? 0);
                    $val2 = floatval($data['criteria_2'] ?? 0);
                    $val3 = floatval($data['criteria_3'] ?? 0);
                    if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                        $finalScore = floatval($data['manual_final_score']);
                    } else {
                        $finalScore = round($val1 + $val2 + $val3, 2);
                    }
                    break;

                case 'social_engagement':
                    // PMR Favorite: likes & stickers
                    $likes = intval($data['likes'] ?? 0);
                    $stickers = intval($data['stickers'] ?? 0);
                    if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                        $finalScore = floatval($data['manual_final_score']);
                    } else {
                        $finalScore = round($likes + ($stickers * 1.5), 2);
                    }
                    break;

                case 'bracket_quiz':
                case 'standard_time':
                default:
                    // Direct score input or Total Nilai
                    $scoreVal = floatval($data['score'] ?? 0);
                    if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                        $finalScore = floatval($data['manual_final_score']);
                    } else {
                        $finalScore = $scoreVal;
                    }
                    break;
            }

            CompetitionScore::updateOrCreate(
                [
                    'competition_category_id' => $category->id,
                    'competition_participant_team_id' => $teamId,
                    'round_name' => $round,
                ],
                [
                    'score_details' => $data,
                    'final_score' => $isDisqualified ? 0 : $finalScore,
                    'time_recorded' => $timeRecorded,
                    'is_disqualified' => $isDisqualified,
                    'notes' => $data['notes'] ?? null,
                ]
            );
        }

        // Auto-recalculate ranks for this category and round!
        $this->autoRecalculateRanks($category->id, $round);

        return redirect()->back()->with('success', "Nilai untuk mata lomba '{$category->display_name}' ({$round}) berhasil disimpan dan peringkat telah diperbarui secara otomatis!");
    }

    private function autoRecalculateRanks($categoryId, $round)
    {
        $scores = CompetitionScore::where('competition_category_id', $categoryId)
            ->where('round_name', $round)
            ->where('is_disqualified', false)
            ->orderBy('final_score', 'desc')
            ->orderBy('time_recorded', 'asc')
            ->get();

        $rank = 1;
        foreach ($scores as $score) {
            $score->update(['rank' => $rank]);
            $rank++;
        }

        // Reset rank for disqualified teams
        CompetitionScore::where('competition_category_id', $categoryId)
            ->where('round_name', $round)
            ->where('is_disqualified', true)
            ->update(['rank' => null]);
    }

    public function quickAddTeam(CompetitionCategory $category, Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'order_number' => 'nullable|string|max:20',
            'team_label' => 'nullable|string|max:50',
        ]);

        // Find or create dummy registration for walk-in
        $registration = CompetitionRegistration::firstOrCreate(
            [
                'competition_event_id' => $category->competition_event_id,
                'school_name' => strtoupper(trim($validated['school_name'])),
            ],
            [
                'registration_code' => 'WALKIN-' . strtoupper(\Illuminate\Support\Str::random(5)),
                'level' => $category->level,
                'advisor_name' => 'Pembina OTS',
                'advisor_phone' => '-',
                'status' => 'verified',
            ]
        );

        $teamName = $registration->school_name . ($validated['team_label'] ? " {$validated['team_label']}" : '');

        CompetitionParticipantTeam::create([
            'competition_registration_id' => $registration->id,
            'competition_category_id' => $category->id,
            'order_number' => $validated['order_number'] ?? null,
            'team_name' => $teamName,
            'team_label' => $validated['team_label'] ?? null,
        ]);

        return redirect()->back()->with('success', "Peserta '{$teamName}' berhasil ditambahkan ke lembar penilaian.");
    }
}

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
            ->withCount(['teams' => function($q) {
                $q->where('is_active', true)->where(function($sq) {
                    $sq->whereHas('registration', function($rq) {
                        $rq->where('status', 'verified');
                    })->orWhereNull('competition_registration_id');
                });
            }])
            ->orderBy('order_position')
            ->get();

        return view('admin.competition.scores.index', compact('event', 'level', 'categories'));
    }

    public function input(CompetitionCategory $category, Request $request)
    {
        $round = $request->query('round', 'Utama');

        // Fetch verified participant teams for this specific competition category
        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereHas('registration', function($rq) {
                    $rq->where('status', 'verified');
                })->orWhereNull('competition_registration_id');
            })
            ->with('registration')
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

        $isTandu = str_contains(strtolower($category->name), 'tandu') || str_starts_with($category->code ?? '', 'LKTR');

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
                    if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                        $finalScore = floatval($data['manual_final_score']);
                    } else {
                        // Formula: Tertulis (0-100) * 0.3 + (Praktik / 1000 * 100) * 0.7
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

                case 'standard_time':
                case 'tandu_standard_time':
                default:
                    if ($isTandu || $category->scoring_type === 'tandu_standard_time' || (!empty($timeRecorded) && !empty($data['technical_score'] ?? $data['score'] ?? null))) {
                        // Ketangkasan Tandu Reguler Formula (Excel Exact):
                        // Total = Nilai Teknis + [300 - IF(Waktu <= 06:55; 0; ROUNDUP((Waktu - 06:55) / 15s; 0) * 10)]
                        $technicalScore = floatval($data['technical_score'] ?? ($data['score'] ?? 0));
                        $tanduCalc = self::calculateTanduScore($technicalScore, $timeRecorded);

                        $data['technical_score'] = $technicalScore;
                        $data['time_seconds'] = $tanduCalc['time_seconds'];
                        $data['time_penalty'] = $tanduCalc['time_penalty'];
                        $data['time_score'] = $tanduCalc['time_score'];

                        if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                            $finalScore = floatval($data['manual_final_score']);
                        } else {
                            $finalScore = $tanduCalc['final_score'];
                        }
                    } else {
                        $scoreVal = floatval($data['score'] ?? ($data['technical_score'] ?? 0));
                        if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                            $finalScore = floatval($data['manual_final_score']);
                        } else {
                            $finalScore = $scoreVal;
                        }
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

    /**
     * Parse arbitrary time string (05:44, 12:05:44 AM, 05.44, 00:05:44, raw seconds) into total seconds.
     */
    public static function parseTimeToSeconds(?string $timeStr): ?int
    {
        if (!$timeStr) return null;
        $timeStr = trim($timeStr);
        if ($timeStr === '') return null;

        // Raw numeric seconds
        if (is_numeric($timeStr) && !str_contains($timeStr, ':') && !str_contains($timeStr, '.')) {
            return (int) $timeStr;
        }

        $isAmPm = stripos($timeStr, 'AM') !== false || stripos($timeStr, 'PM') !== false;
        $cleanStr = trim(preg_replace('/[^\d:.]/', '', $timeStr));

        if (str_contains($cleanStr, ':')) {
            $parts = explode(':', $cleanStr);
            if (count($parts) === 2) {
                // MM:SS
                return ((int)$parts[0] * 60) + (int)$parts[1];
            } elseif (count($parts) === 3) {
                // HH:MM:SS (in Excel 12:05:44 AM -> hour 0, 5 min, 44 sec)
                $h = (int)$parts[0];
                $m = (int)$parts[1];
                $s = (int)$parts[2];
                if ($isAmPm && $h === 12) {
                    $h = 0;
                }
                return ($h * 3600) + ($m * 60) + $s;
            }
        } elseif (str_contains($cleanStr, '.')) {
            // MM.SS
            $parts = explode('.', $cleanStr);
            if (count($parts) === 2) {
                return ((int)$parts[0] * 60) + (int)$parts[1];
            }
        }

        return null;
    }

    /**
     * Calculate Ketangkasan Tandu Reguler Score based on Excel Formula:
     * Total = Nilai Teknis + [300 - IF(Waktu <= 06:55; 0; ROUNDUP((Waktu - 06:55) / 15s; 0) * 10)]
     */
    public static function calculateTanduScore(
        float $technicalScore,
        ?string $timeStr,
        int $standardLimitSeconds = 415,
        int $maxTimeBonus = 300,
        int $penaltyPerInterval = 10,
        int $intervalSeconds = 15
    ): array {
        $seconds = self::parseTimeToSeconds($timeStr);

        if ($seconds === null) {
            return [
                'technical_score' => $technicalScore,
                'time_seconds' => null,
                'time_penalty' => 0,
                'time_score' => 0,
                'final_score' => $technicalScore,
            ];
        }

        $excess = max(0, $seconds - $standardLimitSeconds);
        $penalty = 0;
        if ($excess > 0) {
            $intervals = (int) ceil($excess / $intervalSeconds);
            $penalty = $intervals * $penaltyPerInterval;
        }

        $timeScore = $maxTimeBonus - $penalty;
        $finalScore = $technicalScore + $timeScore;

        return [
            'technical_score' => $technicalScore,
            'time_seconds' => $seconds,
            'time_penalty' => $penalty,
            'time_score' => $timeScore,
            'final_score' => $finalScore,
        ];
    }

    private function autoRecalculateRanks($categoryId, $round)
    {
        $scores = CompetitionScore::where('competition_category_id', $categoryId)
            ->where('round_name', $round)
            ->where('is_disqualified', false)
            ->get();

        // Sort by final_score descending; if tied, sort by time_seconds ascending
        $sortedScores = $scores->sort(function ($a, $b) {
            if ($a->final_score != $b->final_score) {
                return $b->final_score <=> $a->final_score;
            }

            $secA = $a->score_details['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
            $secB = $b->score_details['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;
            return $secA <=> $secB;
        });

        $rank = 1;
        foreach ($sortedScores as $score) {
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

    public function resetScores(CompetitionCategory $category, Request $request)
    {
        $round = $request->input('round_name', 'Utama');

        CompetitionScore::where('competition_category_id', $category->id)
            ->where('round_name', $round)
            ->delete();

        return redirect()->back()->with('success', "Seluruh nilai dan peringkat untuk mata lomba '{$category->display_name}' ({$round}) berhasil di-reset dan dikosongkan.");
    }
}

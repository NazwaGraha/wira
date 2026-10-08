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
        // Resolve Event
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $level = $request->query('level', 'Madya');

        $categories = CompetitionCategory::where('competition_event_id', $event?->id)
            ->where('level', $level)
            ->where('is_active', true)
            ->withCount(['teams' => function($q) use ($event) {
                $q->where('is_active', true)->where(function($sq) use ($event) {
                    $sq->whereHas('registration', function($rq) use ($event) {
                        $rq->where('status', 'verified');
                        if ($event) {
                            $rq->where('competition_event_id', $event->id);
                        }
                    })->orWhereNull('competition_registration_id');
                });
            }])
            ->orderBy('order_position')
            ->get();

        return view('admin.competition.scores.index', compact('event', 'allEvents', 'level', 'categories'));
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

        // Get all verified registrations for this event / level
        $availableRegistrations = CompetitionRegistration::where('status', 'verified')
            ->when($category->level, function($q) use ($category) {
                $q->where('level', $category->level);
            })
            ->when($category->competition_event_id, function($q) use ($category) {
                $q->where('competition_event_id', $category->competition_event_id);
            })
            ->with(['teams' => function($tq) use ($category) {
                $tq->where('competition_category_id', $category->id)->where('is_active', true);
            }])
            ->orderBy('school_name')
            ->get();

        $scores = CompetitionScore::where('competition_category_id', $category->id)
            ->where('round_name', $round)
            ->get()
            ->keyBy('competition_participant_team_id');

        $isSemiFinal = str_contains(strtolower($round), 'semi final') || str_contains(strtolower($round), 'semifinal');
        $isFinal = (str_contains(strtolower($round), 'final') && !$isSemiFinal) || str_contains(strtolower($round), 'babak 3');

        // Fetch Prelim scores & qualified teams if in Semifinal
        $prelimScores = collect();
        $qualifiedTeams = collect();
        $termin1Teams = collect();
        $termin2Teams = collect();
        $termin3Teams = collect();
        $unassignedTeams = collect();
        $finalistTeams = collect();
        $hasSemiFinalResults = false;

        if ($isSemiFinal) {
            $prelimScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where(function ($q) {
                    $q->where('round_name', 'like', '%Penyisihan%')
                      ->orWhere('round_name', 'like', '%Babak 1%');
                })
                ->where('is_disqualified', false)
                ->orderByRaw('`rank` IS NULL, `rank` ASC')
                ->orderByDesc('final_score')
                ->get()
                ->keyBy('competition_participant_team_id');

            if ($prelimScores->isNotEmpty()) {
                foreach ($prelimScores as $teamId => $pScore) {
                    $t = $teams->firstWhere('id', $teamId);
                    if ($t) {
                        $t->prelim_rank = $pScore->rank;
                        $t->prelim_score = $pScore->final_score;
                        $t->prelim_time = $pScore->time_recorded;
                        $qualifiedTeams->push($t);
                    }
                }
            } else {
                foreach ($teams as $idx => $t) {
                    $t->prelim_rank = $idx + 1;
                    $t->prelim_score = '-';
                    $t->prelim_time = '-';
                    $qualifiedTeams->push($t);
                }
            }

            // Distribute into termin collections based on existing score records in Semifinal
            foreach ($teams as $team) {
                $score = $scores->get($team->id);
                $termin = $score?->score_details['termin'] ?? null;
                if ($termin == 1) {
                    $termin1Teams->push($team);
                } elseif ($termin == 2) {
                    $termin2Teams->push($team);
                } elseif ($termin == 3) {
                    $termin3Teams->push($team);
                } else {
                    if ($qualifiedTeams->contains('id', $team->id)) {
                        $unassignedTeams->push($team);
                    }
                }
            }
        } elseif ($isFinal) {
            // Babak 3 - Final: Automatically qualify the rank 1 winners of each Semifinal Termin!
            $semiScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where(function ($q) {
                    $q->where('round_name', 'like', '%Semi Final%')
                      ->orWhere('round_name', 'like', '%Semifinal%')
                      ->orWhere('round_name', 'like', '%Babak 2%');
                })
                ->where('is_disqualified', false)
                ->get();

            $byTermin = $semiScores->groupBy(function ($s) {
                return $s->score_details['termin'] ?? null;
            });

            foreach ([1, 2, 3] as $tNum) {
                $tGroup = $byTermin->get($tNum, collect());
                if ($tGroup->isNotEmpty()) {
                    // Pick the team with rank 1 or highest final_score
                    $winnerScore = $tGroup->where('rank', 1)->first()
                        ?? $tGroup->sortByDesc('final_score')->first();

                    if ($winnerScore && ($winnerScore->final_score > 0 || !empty($winnerScore->score_details['score']) || $winnerScore->rank == 1)) {
                        $tModel = $teams->firstWhere('id', $winnerScore->competition_participant_team_id);
                        if ($tModel) {
                            $finalist = clone $tModel;
                            $finalist->final_desk = chr(64 + $tNum); // 1 -> A, 2 -> B, 3 -> C
                            $finalist->semi_termin = $tNum;
                            $finalist->semi_score = $winnerScore->final_score;
                            $finalist->semi_rank = $winnerScore->rank ?? 1;
                            $finalistTeams->push($finalist);
                        }
                    }
                }
            }

            // Also include any teams that already have scores recorded in this Final round
            if ($scores->isNotEmpty()) {
                foreach ($scores as $fTeamId => $fScore) {
                    if (!$finalistTeams->contains('id', $fTeamId)) {
                        $exTeam = $teams->firstWhere('id', $fTeamId);
                        if ($exTeam) {
                            $finalist = clone $exTeam;
                            $finalist->final_desk = chr(65 + $finalistTeams->count());
                            $finalist->semi_termin = null;
                            $finalist->semi_score = null;
                            $finalistTeams->push($finalist);
                        }
                    }
                }
            }

            if ($finalistTeams->isNotEmpty()) {
                $hasSemiFinalResults = true;
                $teams = $finalistTeams;
            } elseif (request('mode') !== 'all_teams') {
                $teams = collect();
            }
        }

        $nextOrderNumber = $teams->count() + 1;

        return view('admin.competition.scores.input', compact(
            'category',
            'round',
            'teams',
            'scores',
            'availableRegistrations',
            'nextOrderNumber',
            'isSemiFinal',
            'isFinal',
            'hasSemiFinalResults',
            'finalistTeams',
            'prelimScores',
            'qualifiedTeams',
            'termin1Teams',
            'termin2Teams',
            'termin3Teams',
            'unassignedTeams'
        ));
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

            $existingScore = CompetitionScore::where([
                'competition_category_id' => $category->id,
                'competition_participant_team_id' => $teamId,
                'round_name' => $round,
            ])->first();

            $mergedDetails = array_merge($existingScore?->score_details ?? [], $data);

            CompetitionScore::updateOrCreate(
                [
                    'competition_category_id' => $category->id,
                    'competition_participant_team_id' => $teamId,
                    'round_name' => $round,
                ],
                [
                    'score_details' => $mergedDetails,
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
        $isSemiFinal = str_contains(strtolower($round), 'semi final') || str_contains(strtolower($round), 'semifinal');

        if ($isSemiFinal) {
            // Group by termin (1, 2, 3) and rank within each termin!
            $scores = CompetitionScore::where('competition_category_id', $categoryId)
                ->where('round_name', $round)
                ->get();

            $byTermin = $scores->groupBy(function ($s) {
                return $s->score_details['termin'] ?? 1;
            });

            foreach ($byTermin as $terminNum => $tScores) {
                $valid = $tScores->where('is_disqualified', false);
                $sorted = $valid->sort(function ($a, $b) {
                    if ($a->final_score != $b->final_score) {
                        return $b->final_score <=> $a->final_score;
                    }
                    $secA = $a->score_details['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
                    $secB = $b->score_details['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;
                    return $secA <=> $secB;
                });

                $rank = 1;
                foreach ($sorted as $s) {
                    $details = $s->score_details ?? [];
                    $details['termin'] = (int) $terminNum;
                    $details['termin_rank'] = $rank;
                    $s->update([
                        'rank' => $rank,
                        'score_details' => $details,
                    ]);
                    $rank++;
                }

                foreach ($tScores->where('is_disqualified', true) as $dq) {
                    $dq->update(['rank' => null]);
                }
            }
            return;
        }

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

    /**
     * Assign teams to Termin 1, 2, and 3 in Semifinal.
     * Modes: 'automatic' (Snake Seeding), 'random' (Pot Drawing), 'manual' (Custom).
     */
    public function assignTermins(CompetitionCategory $category, Request $request)
    {
        $round = $request->input('round_name', 'Babak 2 - Semi Final');
        $mode = $request->input('mode', 'automatic');

        // Fetch Prelim scores to get the qualified teams in rank order
        $prelimScores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'like', '%Penyisihan%')
                  ->orWhere('round_name', 'like', '%Babak 1%');
            })
            ->where('is_disqualified', false)
            ->orderByRaw('`rank` IS NULL, `rank` ASC')
            ->orderByDesc('final_score')
            ->get();

        $eligibleTeams = collect();
        if ($prelimScores->isNotEmpty()) {
            foreach ($prelimScores as $ps) {
                $t = CompetitionParticipantTeam::with('registration')->find($ps->competition_participant_team_id);
                if ($t) {
                    $t->prelim_rank = $ps->rank;
                    $t->prelim_score = $ps->final_score;
                    $eligibleTeams->push($t);
                }
            }
        } else {
            $eligibleTeams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->where('is_active', true)
                ->with('registration')
                ->orderBy('order_number')
                ->get();
        }

        $topTeams = $eligibleTeams->take(9);

        if ($mode === 'automatic') {
            // Snake Seeding (1-6-7, 2-5-8, 3-4-9) with School Protection
            $assignments = $this->calculateSnakeSeeding($topTeams);
        } elseif ($mode === 'random') {
            // Pot-based Drawing (Pot 1, Pot 2, Pot 3) with School Protection
            $assignments = $this->calculatePotRandomDrawing($topTeams);
        } else {
            // Manual assignments: array of team_id => termin
            $assignments = $request->input('assignments', []);
        }

        // Save assignments to CompetitionScore for Babak 2 - Semi Final
        foreach ($assignments as $teamId => $terminNum) {
            $terminNum = (int) $terminNum;
            if ($terminNum < 1 || $terminNum > 3) continue;

            $score = CompetitionScore::firstOrNew([
                'competition_category_id' => $category->id,
                'competition_participant_team_id' => $teamId,
                'round_name' => $round,
            ]);

            $details = $score->score_details ?? [];
            $details['termin'] = $terminNum;
            $score->score_details = $details;
            $score->save();
        }

        // Recalculate ranks if scores already exist
        $this->autoRecalculateRanks($category->id, $round);

        return redirect()->route('admin.competition-scores.input', [
            'category' => $category->id,
            'round' => $round,
        ])->with('success', 'Pembagian regu ke Termin 1, 2, dan 3 berhasil diperbarui dan diterapkan ke lembar skor!');
    }

    private function calculateSnakeSeeding($teams): array
    {
        $pattern = [
            0 => 1, // Rank 1 -> Termin 1
            1 => 2, // Rank 2 -> Termin 2
            2 => 3, // Rank 3 -> Termin 3
            3 => 3, // Rank 4 -> Termin 3
            4 => 2, // Rank 5 -> Termin 2
            5 => 1, // Rank 6 -> Termin 1
            6 => 1, // Rank 7 -> Termin 1
            7 => 2, // Rank 8 -> Termin 2
            8 => 3, // Rank 9 -> Termin 3
        ];

        $assignments = [];
        foreach ($teams->values() as $idx => $team) {
            $assignments[$team->id] = $pattern[$idx] ?? (($idx % 3) + 1);
        }

        return $this->resolveSchoolProtection($teams, $assignments);
    }

    private function calculatePotRandomDrawing($teams): array
    {
        $teamsArr = $teams->values();
        $pot1 = $teamsArr->slice(0, 3)->shuffle()->values();
        $pot2 = $teamsArr->slice(3, 3)->shuffle()->values();
        $pot3 = $teamsArr->slice(6, 3)->shuffle()->values();

        $assignments = [];
        for ($i = 0; $i < 3; $i++) {
            $termin = $i + 1;
            if (isset($pot1[$i])) $assignments[$pot1[$i]->id] = $termin;
            if (isset($pot2[$i])) $assignments[$pot2[$i]->id] = $termin;
            if (isset($pot3[$i])) $assignments[$pot3[$i]->id] = $termin;
        }

        return $this->resolveSchoolProtection($teams, $assignments);
    }

    private function resolveSchoolProtection($teams, array $assignments): array
    {
        $teamSchools = [];
        foreach ($teams as $t) {
            $school = $t->registration?->school_name ?? $t->team_name;
            $baseSchool = trim(preg_replace('/\s*(\((A|B|C|PUTRA|PUTRI|\d+)\)|Regu\s+[A-Z\d]+)$/i', '', $school));
            $teamSchools[$t->id] = strtoupper($baseSchool);
        }

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $hasCollision = false;
            $termins = [1 => [], 2 => [], 3 => []];
            foreach ($assignments as $tId => $tNum) {
                $termins[$tNum][] = $tId;
            }

            foreach ($termins as $tNum => $tIds) {
                $seenSchools = [];
                foreach ($tIds as $tId) {
                    $sch = $teamSchools[$tId] ?? $tId;
                    if (isset($seenSchools[$sch])) {
                        $hasCollision = true;
                        foreach ([1, 2, 3] as $targetTNum) {
                            if ($targetTNum == $tNum) continue;
                            foreach ($termins[$targetTNum] as $candId) {
                                $candSch = $teamSchools[$candId] ?? $candId;
                                $safeForCurrent = !in_array($candSch, array_map(fn($id) => $teamSchools[$id], array_diff($tIds, [$tId])));
                                $safeForTarget = !in_array($sch, array_map(fn($id) => $teamSchools[$id], array_diff($termins[$targetTNum], [$candId])));

                                if ($safeForCurrent && $safeForTarget) {
                                    $assignments[$tId] = $targetTNum;
                                    $assignments[$candId] = $tNum;
                                    break 3;
                                }
                            }
                        }
                    }
                    $seenSchools[$sch] = $tId;
                }
            }

            if (!$hasCollision) break;
        }

        return $assignments;
    }

    public function quickAddTeam(CompetitionCategory $category, Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'nullable|exists:competition_registrations,id',
            'school_name' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:20',
            'team_label' => 'nullable|string|max:50',
        ]);

        if (!empty($validated['registration_id'])) {
            $registration = CompetitionRegistration::findOrFail($validated['registration_id']);
            $label = trim($validated['team_label'] ?? '');
            $teamName = $registration->school_name . ($label ? " {$label}" : '');

            // Check if exact team name already exists in this category
            $existing = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->where('competition_registration_id', $registration->id)
                ->where('team_name', $teamName)
                ->first();

            if ($existing) {
                return redirect()->back()->with('error', "Regu '{$teamName}' dari {$registration->school_name} sudah ada di lembar penilaian ini.");
            }

            CompetitionParticipantTeam::create([
                'competition_registration_id' => $registration->id,
                'competition_category_id' => $category->id,
                'order_number' => $validated['order_number'] ?: null,
                'team_name' => $teamName,
                'team_label' => $label ?: null,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', "Regu '{$teamName}' ({$registration->school_name}) berhasil ditambahkan ke Lembar Penilaian Juri!");
        } else {
            // Walk-in OTS
            $schoolName = strtoupper(trim($validated['school_name'] ?? ''));
            if (!$schoolName) {
                return redirect()->back()->with('error', "Silakan pilih sekolah dari daftar terverifikasi atau masukkan nama sekolah OTS.");
            }

            $registration = CompetitionRegistration::firstOrCreate(
                [
                    'competition_event_id' => $category->competition_event_id,
                    'school_name' => $schoolName,
                ],
                [
                    'registration_code' => 'WALKIN-' . strtoupper(\Illuminate\Support\Str::random(5)),
                    'level' => $category->level,
                    'advisor_name' => 'Pembina OTS',
                    'advisor_phone' => '-',
                    'status' => 'verified',
                ]
            );

            $label = trim($validated['team_label'] ?? '');
            $teamName = $registration->school_name . ($label ? " {$label}" : '');

            CompetitionParticipantTeam::create([
                'competition_registration_id' => $registration->id,
                'competition_category_id' => $category->id,
                'order_number' => $validated['order_number'] ?: null,
                'team_name' => $teamName,
                'team_label' => $label ?: null,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', "Peserta OTS '{$teamName}' berhasil ditambahkan ke lembar penilaian.");
        }
    }

    public function removeTeam(CompetitionCategory $category, CompetitionParticipantTeam $team)
    {
        $teamName = $team->team_name;

        // Delete associated scores in this category
        $team->scores()->where('competition_category_id', $category->id)->delete();
        $team->delete();

        return redirect()->back()->with('success', "Regu '{$teamName}' telah dihapus dari lembar penilaian cabang ini.");
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

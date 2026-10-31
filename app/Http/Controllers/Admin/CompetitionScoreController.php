<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Services\CompetitionSpreadsheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            ->withCount(['teams' => function ($q) use ($event) {
                $q->where('is_active', true)->where(function ($sq) use ($event) {
                    $sq->whereHas('registration', function ($rq) use ($event) {
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
        $category->load('assignedJuries');

        $round = $request->query('round', 'Utama');

        // Fetch verified participant teams for this specific competition category and level
        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->where(function ($q) use ($category) {
                $q->whereHas('registration', function ($rq) use ($category) {
                    $rq->where('status', 'verified');
                    if (! empty($category->level)) {
                        $rq->where('level', $category->level);
                    }
                })->orWhereNull('competition_registration_id');
            })
            ->with('registration')
            ->orderBy('order_number')
            ->get();

        $isPutra = ($category->gender_category === 'Putra') || str_contains(strtoupper($category->code ?? ''), '-PA');
        $isPutri = ($category->gender_category === 'Putri') || str_contains(strtoupper($category->code ?? ''), '-PI');

        // Filter collection strictly untuk memastikan hanya tingkat dan kategori gender yang sesuai
        $teams = $teams->filter(function ($t) use ($category, $isPutra, $isPutri) {
            // 1. Filter Tingkat (Mula / Madya / Wira)
            if ($t->registration && ! empty($t->registration->level) && ! empty($category->level)) {
                if (strcasecmp(trim($t->registration->level), trim($category->level)) !== 0) {
                    return false;
                }
            }

            // 2. Filter Kategori Gender (Putra vs Putri)
            $combined = strtolower(($t->team_name ?? '').' '.($t->team_label ?? ''));
            if ($isPutra) {
                if (str_contains($combined, 'putri') || str_ends_with($combined, ' pi') || str_contains($combined, ' regu pi')) {
                    return false;
                }
            } elseif ($isPutri) {
                if (str_contains($combined, 'putra') || str_ends_with($combined, ' pa') || str_contains($combined, ' regu pa')) {
                    return false;
                }
            }

            return true;
        })->values();

        // Get all verified registrations for this event / level
        $availableRegistrations = CompetitionRegistration::where('status', 'verified')
            ->when($category->level, function ($q) use ($category) {
                $q->where('level', $category->level);
            })
            ->when($category->competition_event_id, function ($q) use ($category) {
                $q->where('competition_event_id', $category->competition_event_id);
            })
            ->with(['teams' => function ($tq) use ($category) {
                $tq->where('competition_category_id', $category->id)->where('is_active', true);
            }])
            ->orderBy('school_name')
            ->get();

        $scores = CompetitionScore::where('competition_category_id', $category->id)
            ->where('round_name', $round)
            ->with(['jury', 'photos'])
            ->get()
            ->keyBy('competition_participant_team_id');

        $isSemiFinal = str_contains(strtolower($round), 'semi final') || str_contains(strtolower($round), 'semifinal');
        $isFinal = (str_contains(strtolower($round), 'final') && ! $isSemiFinal) || str_contains(strtolower($round), 'babak 3');

        // Fetch Prelim scores & qualified teams if in Semifinal
        $prelimScores = collect();
        $qualifiedTeams = collect();
        $termin1Teams = collect();
        $termin2Teams = collect();
        $termin3Teams = collect();
        $unassignedTeams = collect();
        $finalistTeams = collect();
        $finalistOptionA = collect();
        $finalistOptionB = collect();
        $finalMode = 'termin_winners';
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
                $details = is_array($score?->score_details) ? $score->score_details : (json_decode($score?->score_details ?? '', true) ?? []);
                $termin = $details['termin'] ?? null;
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

            // If semifinal scores exist, recalculate ranks immediately so each termin is guaranteed ranks 1, 2, 3
            if ($scores->isNotEmpty()) {
                $this->autoRecalculateRanks($category->id, $round);
                $scores = CompetitionScore::where('competition_category_id', $category->id)
                    ->where('round_name', $round)
                    ->get()
                    ->keyBy('competition_participant_team_id');
            }
        } elseif ($isFinal) {
            // Babak 3 - Final Selection Modes:
            // Opsi A: 'termin_winners' (Juara 1 dari masing-masing Termin 1, 2, 3 - Sesuai Excel Panitia)
            // Opsi B: 'top_scores' (Top 3 Nilai Akhir Tertinggi Semifinal secara Global)
            $finalMode = $request->input('final_mode', session('final_mode_'.$category->id, 'termin_winners'));
            session(['final_mode_'.$category->id => $finalMode]);

            $finalistOptionA = collect();
            $finalistOptionB = collect();

            $semiScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where(function ($q) {
                    $q->where('round_name', 'like', '%Semi Final%')
                        ->orWhere('round_name', 'like', '%Semifinal%')
                        ->orWhere('round_name', 'like', '%Babak 2%');
                })
                ->where('is_disqualified', false)
                ->get();

            // === OPSI A: Juara Masing-Masing Termin (Termin 1, 2, 3) ===
            $byTermin = $semiScores->groupBy(function ($s) {
                return $s->score_details['termin'] ?? null;
            });

            foreach ([1, 2, 3] as $tNum) {
                $tGroup = $byTermin->get($tNum, collect());
                if ($tGroup->isNotEmpty()) {
                    $winnerScore = $tGroup->where('rank', 1)->first()
                        ?? $tGroup->sortByDesc('final_score')->first();

                    if ($winnerScore && ($winnerScore->final_score > 0 || ! empty($winnerScore->score_details['score']) || $winnerScore->rank == 1)) {
                        $tModel = $teams->firstWhere('id', $winnerScore->competition_participant_team_id);
                        if ($tModel) {
                            $f = clone $tModel;
                            $f->final_desk = chr(64 + $tNum); // 1 -> A, 2 -> B, 3 -> C
                            $f->qualification_badge = 'Juara Termin '.$tNum;
                            $f->semi_termin = $tNum;
                            $f->semi_score = $winnerScore->final_score;
                            $f->semi_rank = $winnerScore->rank ?? 1;
                            $finalistOptionA->push($f);
                        }
                    }
                }
            }

            // === OPSI B: Top 3 Nilai Tertinggi Seluruh Semifinal ===
            $sortedSemi = $semiScores->filter(function ($s) {
                return $s->final_score > 0 || ! empty($s->score_details['score']);
            })->sort(function ($a, $b) {
                if ($a->final_score != $b->final_score) {
                    return $b->final_score <=> $a->final_score;
                }
                $secA = $a->score_details['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
                $secB = $b->score_details['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;

                return $secA <=> $secB;
            })->take(3)->values();

            foreach ($sortedSemi as $bIdx => $bScore) {
                $tModel = $teams->firstWhere('id', $bScore->competition_participant_team_id);
                if ($tModel) {
                    $f = clone $tModel;
                    $f->final_desk = chr(65 + $bIdx); // 0 -> A, 1 -> B, 2 -> C
                    $f->qualification_badge = 'Rank '.($bIdx + 1).' Semifinal (Skor: '.$bScore->final_score.')';
                    $f->semi_termin = $bScore->score_details['termin'] ?? null;
                    $f->semi_score = $bScore->final_score;
                    $f->semi_rank = $bIdx + 1;
                    $finalistOptionB->push($f);
                }
            }

            // Pick active finalist list based on selected mode
            if ($finalMode === 'top_scores') {
                $finalistTeams = $finalistOptionB;
            } else {
                $finalistTeams = $finalistOptionA;
            }

            // Also include any teams that already have scores recorded in this Final round
            if ($scores->isNotEmpty()) {
                foreach ($scores as $fTeamId => $fScore) {
                    if (! $finalistTeams->contains('id', $fTeamId)) {
                        $exTeam = $teams->firstWhere('id', $fTeamId);
                        if ($exTeam) {
                            $finalist = clone $exTeam;
                            $finalist->final_desk = chr(65 + $finalistTeams->count());
                            $finalist->qualification_badge = 'Finalis Tambahan';
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

        $isPrelim = str_contains(strtolower($round), 'penyisihan') || str_contains(strtolower($round), 'babak 1');

        // Deteksi apakah ada bentrokan sekolah dalam tiap termin semifinal
        $terminCollisions = [];
        if ($isSemiFinal) {
            foreach ([1 => $termin1Teams, 2 => $termin2Teams, 3 => $termin3Teams] as $tNum => $tList) {
                $seenSch = [];
                foreach ($tList as $tm) {
                    $schKey = $this->extractCleanSchoolKey($tm);
                    if (isset($seenSch[$schKey])) {
                        $schName = $tm->registration?->school_name ?? $tm->team_name;
                        $terminCollisions[$tNum][] = $schName;
                    }
                    $seenSch[$schKey] = true;
                }
            }
        }

        // Tentukan Juara Harapan 1 & 2 dari Semifinal (dari regu yang tidak lolos ke final)
        $harapan1Team = null;
        $harapan2Team = null;
        $otherSemiFinalists = collect();

        if ($isFinal || $isSemiFinal) {
            $allSemiScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where(function ($q) {
                    $q->where('round_name', 'like', '%Semi Final%')
                        ->orWhere('round_name', 'like', '%Semifinal%')
                        ->orWhere('round_name', 'like', '%Babak 2%');
                })
                ->where('is_disqualified', false)
                ->get();

            // Kumpulkan ID regu yang lolos ke Final (Juara Termin 1, 2, 3 atau regu yang bertanding di Final)
            $finalistIds = [];
            if (isset($finalistTeams) && $finalistTeams->isNotEmpty()) {
                $finalistIds = $finalistTeams->pluck('id')->toArray();
            } else {
                $byTerminCalc = $allSemiScores->groupBy(fn ($s) => $s->score_details['termin'] ?? null);
                foreach ([1, 2, 3] as $tNum) {
                    $tGroup = $byTerminCalc->get($tNum, collect());
                    if ($tGroup->isNotEmpty()) {
                        $w = $tGroup->where('rank', 1)->first() ?? $tGroup->sortByDesc('final_score')->first();
                        if ($w && ($w->final_score > 0 || ! empty($w->score_details['score']))) {
                            $finalistIds[] = $w->competition_participant_team_id;
                        }
                    }
                }
            }
            $finalistIds = array_unique(array_filter($finalistIds));

            $nonFinalists = $allSemiScores->filter(function ($s) use ($finalistIds) {
                return ! in_array($s->competition_participant_team_id, $finalistIds) && ($s->final_score > 0 || ! empty($s->score_details['score']));
            })->sort(function ($a, $b) {
                if ($a->final_score != $b->final_score) {
                    return $b->final_score <=> $a->final_score;
                }
                $secA = $a->score_details['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
                $secB = $b->score_details['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;

                return $secA <=> $secB;
            })->values();

            $allCategoryTeams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->with('registration')
                ->get();

            if ($nonFinalists->isNotEmpty()) {
                if (isset($nonFinalists[0])) {
                    $h1Score = $nonFinalists[0];
                    $h1Model = $allCategoryTeams->firstWhere('id', $h1Score->competition_participant_team_id);
                    if ($h1Model) {
                        $harapan1Team = clone $h1Model;
                        $harapan1Team->semi_score = $h1Score->final_score;
                        $harapan1Team->semi_termin = $h1Score->score_details['termin'] ?? null;
                    }
                }
                if (isset($nonFinalists[1])) {
                    $h2Score = $nonFinalists[1];
                    $h2Model = $allCategoryTeams->firstWhere('id', $h2Score->competition_participant_team_id);
                    if ($h2Model) {
                        $harapan2Team = clone $h2Model;
                        $harapan2Team->semi_score = $h2Score->final_score;
                        $harapan2Team->semi_termin = $h2Score->score_details['termin'] ?? null;
                    }
                }

                foreach ($nonFinalists->slice(2) as $otherScore) {
                    $othModel = $allCategoryTeams->firstWhere('id', $otherScore->competition_participant_team_id);
                    if ($othModel) {
                        $clone = clone $othModel;
                        $clone->semi_score = $otherScore->final_score;
                        $clone->semi_termin = $otherScore->score_details['termin'] ?? null;
                        $otherSemiFinalists->push($clone);
                    }
                }
            }
        }

        $nextOrderNumber = $category->generateNextOrderNumber(0);

        return view('admin.competition.scores.input', compact(
            'category',
            'round',
            'teams',
            'scores',
            'availableRegistrations',
            'nextOrderNumber',
            'isPrelim',
            'isSemiFinal',
            'isFinal',
            'finalMode',
            'finalistOptionA',
            'finalistOptionB',
            'hasSemiFinalResults',
            'finalistTeams',
            'prelimScores',
            'qualifiedTeams',
            'termin1Teams',
            'termin2Teams',
            'termin3Teams',
            'unassignedTeams',
            'terminCollisions',
            'harapan1Team',
            'harapan2Team',
            'otherSemiFinalists'
        ));
    }

    public function saveScores(CompetitionCategory $category, Request $request)
    {
        $category->loadMissing('assignedJuries');
        $round = $request->input('round_name', 'Utama');
        $scoresData = $request->input('scores', []);

        $isTandu = str_contains(strtolower($category->name), 'tandu') || str_starts_with($category->code ?? '', 'LKTR');

        foreach ($scoresData as $teamId => $data) {
            $isDisqualified = isset($data['is_disqualified']) && $data['is_disqualified'] == '1';
            $finalScore = 0;
            $timeRecorded = $data['time_recorded'] ?? ($data['practical_time'] ?? ($data['written_time'] ?? null));

            // Auto-calculation logic based on category scoring type
            switch ($category->scoring_type) {
                case 'practical_time':
                    $practicalScore = floatval($data['practical_score'] ?? ($data['score'] ?? 0));
                    $practicalTime = $data['practical_time'] ?? ($data['time_recorded'] ?? null);
                    $data['practical_score'] = $practicalScore;
                    $data['practical_time'] = $practicalTime;
                    $timeRecorded = $practicalTime;
                    $data['time_seconds'] = self::parseTimeToSeconds($practicalTime);

                    if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                        $finalScore = floatval($data['manual_final_score']);
                    } else {
                        $finalScore = $practicalScore;
                    }
                    break;

                case 'written_time':
                    $writtenScore = floatval($data['written_score'] ?? ($data['score'] ?? 0));
                    $writtenTime = $data['written_time'] ?? ($data['time_recorded'] ?? null);
                    $data['written_score'] = $writtenScore;
                    $data['written_time'] = $writtenTime;
                    $timeRecorded = $writtenTime;
                    $data['time_seconds'] = self::parseTimeToSeconds($writtenTime);

                    if (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') {
                        $finalScore = floatval($data['manual_final_score']);
                    } else {
                        $finalScore = $writtenScore;
                    }
                    break;

                case 'written_practical_time':
                    // If Pertolongan Pertama or Olimpiade, honor new rules even under old type
                    $isPP = str_contains(strtolower($category->name), 'pertolongan pertama') || str_starts_with($category->code ?? '', 'LPP');
                    $isOlim = str_contains(strtolower($category->name), 'olimpiade') || str_starts_with($category->code ?? '', 'OLIM');

                    if ($isPP) {
                        $practicalScore = floatval($data['practical_score'] ?? ($data['score'] ?? 0));
                        $practicalTime = $data['practical_time'] ?? ($data['time_recorded'] ?? null);
                        $data['practical_score'] = $practicalScore;
                        $data['practical_time'] = $practicalTime;
                        $timeRecorded = $practicalTime;
                        $data['time_seconds'] = self::parseTimeToSeconds($practicalTime);
                        $finalScore = (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') ? floatval($data['manual_final_score']) : $practicalScore;
                        break;
                    }

                    if ($isOlim) {
                        $writtenScore = floatval($data['written_score'] ?? ($data['score'] ?? 0));
                        $writtenTime = $data['written_time'] ?? ($data['time_recorded'] ?? null);
                        $data['written_score'] = $writtenScore;
                        $data['written_time'] = $writtenTime;
                        $timeRecorded = $writtenTime;
                        $data['time_seconds'] = self::parseTimeToSeconds($writtenTime);
                        $finalScore = (isset($data['manual_final_score']) && $data['manual_final_score'] !== '') ? floatval($data['manual_final_score']) : $writtenScore;
                        break;
                    }

                    // Legacy combined Tertulis + Praktik
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
                    if ($isTandu || $category->scoring_type === 'tandu_standard_time' || (! empty($timeRecorded) && ! empty($data['technical_score'] ?? $data['score'] ?? null))) {
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

            $juryUserId = $existingScore?->jury_user_id;
            if (! $juryUserId && $category->assignedJuries->isNotEmpty()) {
                $teamModel = CompetitionParticipantTeam::find($teamId);
                $orderNo = strtoupper((string) ($teamModel?->order_number ?? ''));
                if (str_starts_with($orderNo, 'B') && isset($category->assignedJuries[1])) {
                    $juryUserId = $category->assignedJuries[1]->id;
                } else {
                    $juryUserId = $category->assignedJuries[0]->id;
                }
            }

            $mergedDetails = array_merge($existingScore?->score_details ?? [], $data);

            CompetitionScore::updateOrCreate(
                [
                    'competition_category_id' => $category->id,
                    'competition_participant_team_id' => $teamId,
                    'round_name' => $round,
                ],
                [
                    'jury_user_id' => $juryUserId,
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
        if (! $timeStr) {
            return null;
        }
        $timeStr = trim($timeStr);
        if ($timeStr === '') {
            return null;
        }

        // Raw numeric seconds
        if (is_numeric($timeStr) && ! str_contains($timeStr, ':') && ! str_contains($timeStr, '.')) {
            return (int) $timeStr;
        }

        $isAmPm = stripos($timeStr, 'AM') !== false || stripos($timeStr, 'PM') !== false;
        $cleanStr = trim(preg_replace('/[^\d:.]/', '', $timeStr));

        if (str_contains($cleanStr, ':')) {
            $parts = explode(':', $cleanStr);
            if (count($parts) === 2) {
                // MM:SS
                return ((int) $parts[0] * 60) + (int) $parts[1];
            } elseif (count($parts) === 3) {
                // HH:MM:SS (in Excel 12:05:44 AM -> hour 0, 5 min, 44 sec)
                $h = (int) $parts[0];
                $m = (int) $parts[1];
                $s = (int) $parts[2];
                if ($isAmPm && $h === 12) {
                    $h = 0;
                }

                return ($h * 3600) + ($m * 60) + $s;
            }
        } elseif (str_contains($cleanStr, '.')) {
            // MM.SS
            $parts = explode('.', $cleanStr);
            if (count($parts) === 2) {
                return ((int) $parts[0] * 60) + (int) $parts[1];
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
                $details = is_array($s->score_details) ? $s->score_details : (json_decode($s->score_details ?? '', true) ?? []);

                return $details['termin'] ?? 1;
            });

            foreach ($byTermin as $terminNum => $tScores) {
                $valid = $tScores->where('is_disqualified', false);
                $sorted = $valid->sort(function ($a, $b) {
                    if ($a->final_score != $b->final_score) {
                        return $b->final_score <=> $a->final_score;
                    }
                    $detailsA = is_array($a->score_details) ? $a->score_details : (json_decode($a->score_details ?? '', true) ?? []);
                    $detailsB = is_array($b->score_details) ? $b->score_details : (json_decode($b->score_details ?? '', true) ?? []);
                    $secA = $detailsA['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
                    $secB = $detailsB['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;

                    return $secA <=> $secB;
                });

                $rank = 1;
                foreach ($sorted as $s) {
                    $details = is_array($s->score_details) ? $s->score_details : (json_decode($s->score_details ?? '', true) ?? []);
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

            $detailsA = is_array($a->score_details) ? $a->score_details : (json_decode($a->score_details ?? '', true) ?? []);
            $detailsB = is_array($b->score_details) ? $b->score_details : (json_decode($b->score_details ?? '', true) ?? []);
            $secA = $detailsA['time_seconds'] ?? self::parseTimeToSeconds($detailsA['practical_time'] ?? ($detailsA['written_time'] ?? $a->time_recorded)) ?? 999999;
            $secB = $detailsB['time_seconds'] ?? self::parseTimeToSeconds($detailsB['practical_time'] ?? ($detailsB['written_time'] ?? $b->time_recorded)) ?? 999999;

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
            if ($terminNum < 1 || $terminNum > 3) {
                continue;
            }

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
            if (isset($pot1[$i])) {
                $assignments[$pot1[$i]->id] = $termin;
            }
            if (isset($pot2[$i])) {
                $assignments[$pot2[$i]->id] = $termin;
            }
            if (isset($pot3[$i])) {
                $assignments[$pot3[$i]->id] = $termin;
            }
        }

        return $this->resolveSchoolProtection($teams, $assignments);
    }

    /**
     * Ekstrak kunci normalisasi sekolah untuk proteksi bentrokan satu sekolah dalam satu termin.
     */
    public function extractCleanSchoolKey($team): string
    {
        if ($team->registration && ! empty($team->registration->school_name)) {
            $raw = $team->registration->school_name;
        } else {
            $raw = $team->team_name ?? '';
        }

        $clean = preg_replace('/\s*[-–]?\s*(\b(Regu|Tim|PMR)\b\s*)?[A-Z0-9]?\s*$/i', '', $raw);
        $clean = preg_replace('/\s*\(.*?\)\s*$/i', '', $clean);
        $clean = preg_replace('/\s+(Putra|Putri|PA|PI)$/i', '', $clean);
        $clean = trim($clean);

        if ($team->competition_registration_id) {
            return 'REG_'.$team->competition_registration_id.'_'.strtoupper($clean);
        }

        return strtoupper($clean);
    }

    private function resolveSchoolProtection($teams, array $assignments): array
    {
        $teamSchools = [];
        foreach ($teams as $t) {
            $teamSchools[$t->id] = $this->extractCleanSchoolKey($t);
        }

        $countCollisions = function (array $assigns) use ($teamSchools) {
            $termins = [1 => [], 2 => [], 3 => []];
            foreach ($assigns as $tId => $tNum) {
                $termins[$tNum][] = $teamSchools[$tId] ?? $tId;
            }
            $col = 0;
            foreach ($termins as $list) {
                $col += count($list) - count(array_unique($list));
            }

            return $col;
        };

        $minCol = $countCollisions($assignments);
        if ($minCol === 0) {
            return $assignments;
        }

        $teamIds = array_keys($assignments);
        $totalTeams = count($teamIds);

        // Algoritma Swap Otomatis untuk eliminasi bentrokan satu sekolah dalam satu termin
        for ($step = 0; $step < 100 && $minCol > 0; $step++) {
            $improved = false;
            for ($i = 0; $i < $totalTeams; $i++) {
                for ($j = $i + 1; $j < $totalTeams; $j++) {
                    $idA = $teamIds[$i];
                    $idB = $teamIds[$j];

                    if ($assignments[$idA] == $assignments[$idB]) {
                        continue;
                    }

                    // Coba swap termin regu A dan regu B
                    $candidate = $assignments;
                    $tmp = $candidate[$idA];
                    $candidate[$idA] = $candidate[$idB];
                    $candidate[$idB] = $tmp;

                    $cCol = $countCollisions($candidate);
                    if ($cCol < $minCol) {
                        $minCol = $cCol;
                        $assignments = $candidate;
                        $improved = true;
                        break 2;
                    }
                }
            }

            if (! $improved) {
                break;
            }
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

        if (! empty($validated['registration_id'])) {
            $registration = CompetitionRegistration::findOrFail($validated['registration_id']);

            if (! empty($category->level) && strcasecmp((string) $registration->level, (string) $category->level) !== 0) {
                return redirect()->back()->with('error', "Sekolah '{$registration->school_name}' terdaftar pada tingkat PMR {$registration->level}, tidak dapat ditambahkan ke cabang tingkat PMR {$category->level}.");
            }

            $label = trim($validated['team_label'] ?? '');
            $isPa = ($category->gender_category === 'Putra') || str_contains(strtoupper($category->code ?? ''), '-PA');
            $isPi = ($category->gender_category === 'Putri') || str_contains(strtoupper($category->code ?? ''), '-PI');
            if ($isPa && stripos($label, 'putri') !== false) {
                return redirect()->back()->with('error', 'Cabang ini khusus kategori Putra, tidak dapat menambahkan regu dengan label Putri.');
            }
            if ($isPi && stripos($label, 'putra') !== false) {
                return redirect()->back()->with('error', 'Cabang ini khusus kategori Putri, tidak dapat menambahkan regu dengan label Putra.');
            }
            $teamName = $registration->school_name.($label ? " {$label}" : '');

            $existingCount = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->where('competition_registration_id', $registration->id)
                ->count();
            $maxAllowed = $category->maxTeamsPerSchool();

            if (! $category->isUnlimitedTeams() && $existingCount >= $maxAllowed) {
                $genderLabel = $category->gender_category !== 'Umum' ? " {$category->gender_category}" : '';

                return redirect()->back()->with('error', "Sekolah '{$registration->school_name}' sudah mencapai batas maksimal {$maxAllowed} regu di cabang {$category->name}{$genderLabel}.");
            }

            // Check if exact team name already exists in this category
            $existing = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->where('competition_registration_id', $registration->id)
                ->where('team_name', $teamName)
                ->first();

            if ($existing) {
                return redirect()->back()->with('error', "Regu '{$teamName}' dari {$registration->school_name} sudah ada di lembar penilaian ini.");
            }

            $slotIdx = $existingCount;
            $orderNumber = ! empty($validated['order_number']) ? trim($validated['order_number']) : $category->generateNextOrderNumber($slotIdx, $label);

            CompetitionParticipantTeam::create([
                'competition_registration_id' => $registration->id,
                'competition_category_id' => $category->id,
                'order_number' => $orderNumber,
                'team_name' => $teamName,
                'team_label' => $label ?: null,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', "Regu '{$teamName}' ({$registration->school_name}) berhasil ditambahkan ke Lembar Penilaian Juri!");
        } else {
            // Walk-in OTS
            $schoolName = strtoupper(trim($validated['school_name'] ?? ''));
            if (! $schoolName) {
                return redirect()->back()->with('error', 'Silakan pilih sekolah dari daftar terverifikasi atau masukkan nama sekolah OTS.');
            }

            $registration = CompetitionRegistration::firstOrCreate(
                [
                    'competition_event_id' => $category->competition_event_id,
                    'school_name' => $schoolName,
                ],
                [
                    'registration_code' => 'WALKIN-'.strtoupper(Str::random(5)),
                    'level' => $category->level,
                    'advisor_name' => 'Pembina OTS',
                    'advisor_phone' => '-',
                    'status' => 'verified',
                ]
            );

            $label = trim($validated['team_label'] ?? '');
            $isPa = ($category->gender_category === 'Putra') || str_contains(strtoupper($category->code ?? ''), '-PA');
            $isPi = ($category->gender_category === 'Putri') || str_contains(strtoupper($category->code ?? ''), '-PI');
            if ($isPa && stripos($label, 'putri') !== false) {
                return redirect()->back()->with('error', 'Cabang ini khusus kategori Putra, tidak dapat menambahkan regu dengan label Putri.');
            }
            if ($isPi && stripos($label, 'putra') !== false) {
                return redirect()->back()->with('error', 'Cabang ini khusus kategori Putri, tidak dapat menambahkan regu dengan label Putra.');
            }
            $teamName = $registration->school_name.($label ? " {$label}" : '');

            $slotIdx = $existingCount;
            $orderNumber = ! empty($validated['order_number']) ? trim($validated['order_number']) : $category->generateNextOrderNumber($slotIdx, $label);

            CompetitionParticipantTeam::create([
                'competition_registration_id' => $registration->id,
                'competition_category_id' => $category->id,
                'order_number' => $orderNumber,
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

    public function exportExcel(CompetitionCategory $category, Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        $round = $request->query('round', 'Utama');
        $filePath = $spreadsheetService->exportCategoryScoreSheetXlsx($category, $round);

        $safeCatName = Str::slug($category->name, '_');
        $safeLevel = Str::slug($category->level, '_');
        $safeRound = Str::slug($round, '_');
        $fileName = "Lembar_Penilaian_{$safeCatName}_{$safeLevel}_{$safeRound}.xlsx";

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Tampilkan formulir Grid Nilai Pertolongan Pertama (Mula & Madya)
     */
    public function gridScore(CompetitionCategory $category, Request $request)
    {
        $category->loadMissing(['assignedJuries', 'event']);

        $round = $request->query('round', 'Utama');

        // Ambil seluruh tim terverifikasi khusus untuk tingkat PMR yang sesuai dengan kategori ini
        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->where(function ($q) use ($category) {
                $q->whereHas('registration', function ($rq) use ($category) {
                    $rq->where('status', 'verified');
                    if (! empty($category->level)) {
                        $rq->where('level', $category->level);
                    }
                })->orWhereNull('competition_registration_id');
            })
            ->with(['registration'])
            ->orderBy('order_number')
            ->get();

        // Filter collection strictly untuk memastikan hanya tingkat yang sesuai
        $isPutra = ($category->gender_category === 'Putra') || str_contains(strtoupper($category->code ?? ''), '-PA');
        $isPutri = ($category->gender_category === 'Putri') || str_contains(strtoupper($category->code ?? ''), '-PI');

        // Filter collection strictly untuk memastikan hanya tingkat dan kategori gender yang sesuai
        $teams = $teams->filter(function ($t) use ($category, $isPutra, $isPutri) {
            // 1. Filter Tingkat (Mula / Madya / Wira)
            if ($t->registration && ! empty($t->registration->level) && ! empty($category->level)) {
                if (strcasecmp(trim($t->registration->level), trim($category->level)) !== 0) {
                    return false;
                }
            }

            // 2. Filter Kategori Gender (Putra vs Putri)
            $combined = strtolower(($t->team_name ?? '').' '.($t->team_label ?? ''));
            if ($isPutra) {
                if (str_contains($combined, 'putri') || str_ends_with($combined, ' pi') || str_contains($combined, ' regu pi')) {
                    return false;
                }
            } elseif ($isPutri) {
                if (str_contains($combined, 'putra') || str_ends_with($combined, ' pa') || str_contains($combined, ' regu pa')) {
                    return false;
                }
            }

            return true;
        })->values();

        // Tentukan tim yang sedang dipilih/dinilai
        $selectedTeamId = $request->query('team_id');
        $currentTeam = $teams->firstWhere('id', $selectedTeamId) ?: $teams->first();

        // Hitung tim sebelumnya dan berikutnya untuk navigasi cepat
        $currentTeamIndex = $currentTeam ? $teams->search(fn ($t) => $t->id == $currentTeam->id) : false;
        $prevTeam = ($currentTeamIndex !== false && $currentTeamIndex > 0) ? $teams[$currentTeamIndex - 1] : null;
        $nextTeam = ($currentTeamIndex !== false && $currentTeamIndex < $teams->count() - 1) ? $teams[$currentTeamIndex + 1] : null;

        // Ambil nilai yang sudah tersimpan untuk tim yang sedang dinilai
        $score = null;
        $existingRubric = [];
        $existingSubtotals = [];
        $existingTime = '05:00';
        $existingNotes = '';

        if ($currentTeam) {
            $score = CompetitionScore::where('competition_category_id', $category->id)
                ->where('competition_participant_team_id', $currentTeam->id)
                ->where('round_name', $round)
                ->first();

            if ($score) {
                $details = is_array($score->score_details) ? $score->score_details : (json_decode($score->score_details ?? '', true) ?? []);
                $existingRubric = $details['grid_rubric'] ?? [];
                $existingSubtotals = $details['subtotals'] ?? [];
                $existingTime = $score->time_recorded ?? ($details['practical_time'] ?? '05:00');
                $existingNotes = $score->notes ?? ($details['notes'] ?? '');
            }
        }

        // Ambil seluruh skor dalam kategori ini untuk indikator status sudah/belum dinilai
        $scores = CompetitionScore::where('competition_category_id', $category->id)
            ->where('round_name', $round)
            ->get()
            ->keyBy('competition_participant_team_id');

        $viewName = match ($category->level) {
            'Madya' => 'admin.competition.scores.grid-madya',
            'Wira' => 'admin.competition.scores.grid-wira',
            default => 'admin.competition.scores.grid-mula',
        };

        return view($viewName, compact(
            'category',
            'round',
            'teams',
            'currentTeam',
            'prevTeam',
            'nextTeam',
            'score',
            'existingRubric',
            'existingSubtotals',
            'existingTime',
            'existingNotes',
            'scores'
        ));
    }

    /**
     * Alias method untuk backwards compatibility
     */
    public function gridMula(CompetitionCategory $category, Request $request)
    {
        return $this->gridScore($category, $request);
    }

    /**
     * Simpan nilai dari formulir Grid Nilai Pertolongan Pertama ke database
     */
    public function saveGridScore(CompetitionCategory $category, Request $request)
    {
        $teamId = $request->input('competition_participant_team_id');
        $round = $request->input('round_name', 'Utama');
        $timeRecorded = trim((string) $request->input('time_recorded', '05:00'));
        $rubric = $request->input('rubric', []);
        $subtotals = $request->input('subtotals', []);
        $notes = $request->input('notes');
        $actionAfter = $request->input('action_after', 'stay'); // 'stay', 'next', 'back'

        // Hitung total skor akumulasi dari rubrik
        $totalScore = 0;
        foreach ($rubric as $k => $v) {
            $totalScore += intval($v);
        }

        // Konversi waktu ke detik
        $timeSeconds = self::parseTimeToSeconds($timeRecorded);

        // Petakan Dewan Juri
        $category->loadMissing('assignedJuries');
        $juryUserId = auth()->id();
        if ($category->assignedJuries->isNotEmpty()) {
            $assignedIds = $category->assignedJuries->pluck('id')->toArray();
            if (! in_array($juryUserId, $assignedIds)) {
                $juryUserId = $category->assignedJuries->first()->id;
            }
        }

        $existingScore = CompetitionScore::where('competition_category_id', $category->id)
            ->where('competition_participant_team_id', $teamId)
            ->where('round_name', $round)
            ->first();

        $mergedDetails = array_merge($existingScore?->score_details ?? [], [
            'practical_score' => $totalScore,
            'practical_time' => $timeRecorded,
            'time_recorded' => $timeRecorded,
            'time_seconds' => $timeSeconds,
            'grid_rubric' => $rubric,
            'subtotals' => $subtotals,
            'notes' => $notes,
        ]);

        CompetitionScore::updateOrCreate(
            [
                'competition_category_id' => $category->id,
                'competition_participant_team_id' => $teamId,
                'round_name' => $round,
            ],
            [
                'jury_user_id' => $juryUserId,
                'score_details' => $mergedDetails,
                'final_score' => $totalScore,
                'time_recorded' => $timeRecorded,
                'is_disqualified' => false,
                'notes' => $notes,
            ]
        );

        // Perbarui peringkat secara otomatis
        $this->autoRecalculateRanks($category->id, $round);

        $team = CompetitionParticipantTeam::find($teamId);
        $teamName = $team?->team_name ?? 'Regu Peserta';

        $msg = "Nilai Grid Pertolongan Pertama untuk regu '{$teamName}' berhasil disimpan (Total Skor: {$totalScore}, Waktu: {$timeRecorded}) dan otomatis terisi di Lembar Skor Utama!";

        if ($actionAfter === 'next') {
            $nextTeamId = $request->input('next_team_id');
            if ($nextTeamId) {
                return redirect()->route('admin.competition-scores.grid', [
                    'category' => $category->id,
                    'team_id' => $nextTeamId,
                    'round' => $round,
                ])->with('success', $msg);
            }
        } elseif ($actionAfter === 'back') {
            return redirect()->route('admin.competition-scores.input', [
                'category' => $category->id,
                'round' => $round,
            ])->with('success', $msg);
        }

        return redirect()->route('admin.competition-scores.grid', [
            'category' => $category->id,
            'team_id' => $teamId,
            'round' => $round,
        ])->with('success', $msg);
    }

    /**
     * Alias method untuk backwards compatibility
     */
    public function saveGridMula(CompetitionCategory $category, Request $request)
    {
        return $this->saveGridScore($category, $request);
    }

    /**
     * Halaman Lembar Skor Utama (Master Score Sheet) dengan filter Tingkatan dan Nama Sekolah
     */
    public function masterSheet(Request $request)
    {
        // Resolve Event
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $level = $request->query('level', 'all');
        $schoolName = $request->query('school_name', 'all');
        $categoryId = $request->query('category_id', 'all');
        $search = trim((string) $request->query('search', ''));

        // Ambil daftar sekolah terdaftar untuk dropdown filter
        $schools = CompetitionRegistration::where('status', 'verified')
            ->when($event, fn ($q) => $q->where('competition_event_id', $event->id))
            ->when($level && $level !== 'all', fn ($q) => $q->where('level', $level))
            ->distinct()
            ->orderBy('school_name')
            ->pluck('school_name');

        // Ambil daftar kategori cabang lomba untuk dropdown filter
        $categories = CompetitionCategory::when($event, fn ($q) => $q->where('competition_event_id', $event->id))
            ->when($level && $level !== 'all', fn ($q) => $q->where('level', $level))
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();

        // Query seluruh regu peserta
        $teamsQuery = CompetitionParticipantTeam::where('is_active', true)
            ->whereHas('category', function ($q) use ($event, $level, $categoryId) {
                $q->where('is_active', true);
                if ($event) {
                    $q->where('competition_event_id', $event->id);
                }
                if ($level && $level !== 'all') {
                    $q->where('level', $level);
                }
                if ($categoryId && $categoryId !== 'all') {
                    $q->where('id', $categoryId);
                }
            })
            ->where(function ($q) use ($event) {
                $q->whereHas('registration', function ($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                })->orWhereNull('competition_registration_id');
            })
            ->with([
                'category.assignedJuries',
                'registration',
                'scores' => function ($sq) {
                    $sq->orderBy('round_name');
                },
                'scores.jury',
            ]);

        if ($schoolName && $schoolName !== 'all') {
            $teamsQuery->where(function ($q) use ($schoolName) {
                $q->whereHas('registration', fn ($rq) => $rq->where('school_name', $schoolName))
                    ->orWhere('team_name', 'like', "%{$schoolName}%");
            });
        }

        if ($search !== '') {
            $teamsQuery->where(function ($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%")
                            ->orWhere('registration_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $teams = $teamsQuery->orderBy('competition_category_id')
            ->orderByRaw('CAST(order_number AS UNSIGNED) ASC, order_number ASC')
            ->get();

        // Hitung statistik ringkasan
        $totalTeams = $teams->count();
        $scoredTeams = $teams->filter(function ($t) {
            return $t->scores->where('final_score', '>', 0)->isNotEmpty();
        })->count();
        $unscoredTeams = $totalTeams - $scoredTeams;

        return view('admin.competition.scores.master-sheet', compact(
            'event',
            'allEvents',
            'level',
            'schoolName',
            'schools',
            'categoryId',
            'categories',
            'search',
            'teams',
            'totalTeams',
            'scoredTeams',
            'unscoredTeams'
        ));
    }

    /**
     * Export data Lembar Skor Utama ke file Excel (.xlsx)
     */
    public function exportMasterSheetExcel(Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        // Resolve Event
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $level = $request->query('level', 'all');
        $schoolName = $request->query('school_name', 'all');
        $categoryId = $request->query('category_id', 'all');
        $search = trim((string) $request->query('search', ''));

        $teamsQuery = CompetitionParticipantTeam::where('is_active', true)
            ->whereHas('category', function ($q) use ($event, $level, $categoryId) {
                $q->where('is_active', true);
                if ($event) {
                    $q->where('competition_event_id', $event->id);
                }
                if ($level && $level !== 'all') {
                    $q->where('level', $level);
                }
                if ($categoryId && $categoryId !== 'all') {
                    $q->where('id', $categoryId);
                }
            })
            ->where(function ($q) use ($event) {
                $q->whereHas('registration', function ($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                })->orWhereNull('competition_registration_id');
            })
            ->with([
                'category.assignedJuries',
                'registration',
                'scores' => function ($sq) {
                    $sq->orderBy('round_name');
                },
                'scores.jury',
            ]);

        if ($schoolName && $schoolName !== 'all') {
            $teamsQuery->where(function ($q) use ($schoolName) {
                $q->whereHas('registration', fn ($rq) => $rq->where('school_name', $schoolName))
                    ->orWhere('team_name', 'like', "%{$schoolName}%");
            });
        }

        if ($search !== '') {
            $teamsQuery->where(function ($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%")
                            ->orWhere('registration_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $teams = $teamsQuery->orderBy('competition_category_id')
            ->orderByRaw('CAST(order_number AS UNSIGNED) ASC, order_number ASC')
            ->get();

        $filePath = $spreadsheetService->exportMasterScoreSheetXlsx($teams, $event);

        $safeLevel = Str::slug($level ?: 'Semua', '_');
        $safeSchool = $schoolName && $schoolName !== 'all' ? Str::slug($schoolName, '_') : 'Semua_Sekolah';
        $fileName = "Lembar_Skor_Utama_{$safeLevel}_{$safeSchool}.xlsx";

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}

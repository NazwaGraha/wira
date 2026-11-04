<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\User;
use App\Services\CompetitionSpreadsheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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

        if ($request->input('redirect_to') === 'input') {
            return redirect()->route('admin.competition-scores.input', $category->id)->with('success', "Nilai untuk mata lomba '{$category->display_name}' ({$round}) berhasil disimpan dan peringkat telah diperbarui secara otomatis!");
        } elseif ($request->input('redirect_to') === 'grid') {
            return redirect()->route('admin.competition-scores.grid', $category->id)->with('success', 'Seluruh nilai Grid Lomba Mewarnai berhasil disimpan dan peringkat telah diperbarui secara otomatis!');
        }

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

        $isAmPm = stripos($timeStr, 'AM') !== false || stripos($timeStr, 'PM') !== false;
        $cleanStr = trim(preg_replace('/[^\d:.]/', '', $timeStr));

        // Support formats without separator like "0720" (07:20) or "720" (07:20) where last 2 digits are seconds (00-59)
        if (strlen($cleanStr) >= 3 && strlen($cleanStr) <= 4 && preg_match('/^(\d{1,2})([0-5]\d)$/', $cleanStr, $matches) && ! str_contains($cleanStr, ':') && ! str_contains($cleanStr, '.')) {
            $min = (int) $matches[1];
            $sec = (int) $matches[2];

            return ($min * 60) + $sec;
        }

        // Raw numeric seconds
        if (is_numeric($timeStr) && ! str_contains($timeStr, ':') && ! str_contains($timeStr, '.')) {
            return (int) $timeStr;
        }

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
     * Normalisasi string waktu ke format standar MM:SS
     */
    public static function normalizeTimeString(?string $timeStr): ?string
    {
        $sec = self::parseTimeToSeconds($timeStr);
        if ($sec === null) {
            return ! empty($timeStr) ? trim($timeStr) : null;
        }
        $m = floor($sec / 60);
        $s = $sec % 60;

        return sprintf('%02d:%02d', $m, $s);
    }

    /**
     * Resolve a valid jury_user_id that satisfies foreign key constraints
     */
    private function resolveSafeJuryUserId(?CompetitionCategory $category = null): ?int
    {
        if (! Schema::hasColumn('competition_scores', 'jury_user_id')) {
            return null;
        }

        $id = auth()->id();
        if ($id && User::where('id', $id)->exists()) {
            return $id;
        }

        if ($category) {
            $category->loadMissing('assignedJuries');
            if ($category->assignedJuries->isNotEmpty()) {
                $juryId = $category->assignedJuries->first()->id;
                if (User::where('id', $juryId)->exists()) {
                    return $juryId;
                }
            }
        }

        return User::first()?->id ?? null;
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

            if (preg_match('/^([a-z])[-]?(\d+)$/i', $orderNumber, $m)) {
                $orderNumber = strtoupper($m[1]).'-'.sprintf('%03d', intval($m[2]));
            }

            // Validasi keunikan no urut di cabang lomba ini
            $duplicateOrder = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->where('order_number', $orderNumber)
                ->first();

            if ($duplicateOrder) {
                $usedBy = $duplicateOrder->registration?->school_name ?: $duplicateOrder->team_name;

                return redirect()->back()->with('error', "No. Urut Peserta '{$orderNumber}' sudah digunakan oleh regu {$duplicateOrder->team_name} ({$usedBy}) pada cabang lomba ini!");
            }

            CompetitionParticipantTeam::create([
                'competition_registration_id' => $registration->id,
                'competition_category_id' => $category->id,
                'order_number' => $orderNumber,
                'team_name' => $teamName,
                'team_label' => $label ?: null,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', "Regu '{$teamName}' ({$registration->school_name}) berhasil ditambahkan ke Lembar Penilaian Juri dengan No. Urut {$orderNumber}!");
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

            $existingCount = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->where('competition_registration_id', $registration->id)
                ->count();
            $slotIdx = $existingCount;
            $orderNumber = ! empty($validated['order_number']) ? trim($validated['order_number']) : $category->generateNextOrderNumber($slotIdx, $label);

            if (preg_match('/^([a-z])[-]?(\d+)$/i', $orderNumber, $m)) {
                $orderNumber = strtoupper($m[1]).'-'.sprintf('%03d', intval($m[2]));
            }

            // Validasi keunikan no urut di cabang lomba ini
            $duplicateOrder = CompetitionParticipantTeam::where('competition_category_id', $category->id)
                ->where('order_number', $orderNumber)
                ->first();

            if ($duplicateOrder) {
                $usedBy = $duplicateOrder->registration?->school_name ?: $duplicateOrder->team_name;

                return redirect()->back()->with('error', "No. Urut Peserta '{$orderNumber}' sudah digunakan oleh regu {$duplicateOrder->team_name} ({$usedBy}) pada cabang lomba ini!");
            }

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
        // Jika kategori adalah Cepat Tepat / Cerdas Cermat (LCT), arahkan ke sistem penilaian LCT khusus 3 tahap
        if (str_contains(strtolower($category->name), 'cepat tepat') || str_contains(strtolower($category->name), 'cerdas cermat') || str_starts_with($category->code ?? '', 'LCT')) {
            return redirect()->route('admin.competition-scores.lct', $category->id);
        }

        // Jika kategori adalah Olimpiade Kepalangmerahan (OLIM), arahkan ke sistem penilaian Olimpiade
        if (str_contains(strtolower($category->name), 'olimpiade') || str_contains(strtolower($category->name), 'olympiade') || str_starts_with($category->code ?? '', 'OLIM')) {
            return redirect()->route('admin.competition-scores.olimpiade', $category->id);
        }

        $category->loadMissing(['assignedJuries', 'event']);

        $round = $request->query('round', 'Utama');

        // Ambil seluruh tim terverifikasi khusus untuk tingkat PMR dan cabang kategori lomba ini
        $categoryCode = strtoupper(trim($category->code ?? ''));
        $catName = strtolower($category->name ?? '');
        $genderCat = $category->gender_category ?? '';

        $isPutri = ($genderCat === 'Putri')
            || str_ends_with($categoryCode, '-PI')
            || str_contains($categoryCode, '-PI-')
            || str_contains($catName, 'putri');

        $isPutra = ($genderCat === 'Putra')
            || str_ends_with($categoryCode, '-PA')
            || str_contains($categoryCode, '-PA-')
            || str_contains($catName, 'putra');

        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('is_active', true)
                    ->orWhereNull('is_active');
            })
            ->where(function ($q) use ($category) {
                $q->whereHas('registration', function ($rq) use ($category) {
                    $rq->where('status', 'verified');
                    if (! empty($category->level)) {
                        $rq->where('level', $category->level);
                    }
                })->orWhereNull('competition_registration_id');
            })
            ->with(['registration', 'scores'])
            ->orderBy('order_number')
            ->get();

        // Filter collection strictly untuk memastikan hanya regu tingkat dan kategori gender yang sesuai dengan kode ini
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

        $isPRS = str_contains(strtolower($category->name), 'pendidikan remaja sebaya') || str_contains(strtolower($category->name), 'prs') || str_starts_with($category->code ?? '', 'PRS');
        $isMewarnai = str_contains(strtolower($category->name), 'mewarnai') || str_starts_with($category->code ?? '', 'MEWARNAI');
        $isLKTR = str_contains(strtolower($category->name), 'tandu') || str_contains(strtolower($category->name), 'lktr') || str_starts_with($category->code ?? '', 'LKTR');

        if ($isPRS) {
            $viewName = 'admin.competition.scores.grid-prs';
        } elseif ($isMewarnai) {
            $viewName = 'admin.competition.scores.grid-mewarnai';
        } elseif ($isLKTR) {
            $viewName = 'admin.competition.scores.grid-lktr';
        } else {
            $viewName = match ($category->level) {
                'Madya' => 'admin.competition.scores.grid-madya',
                'Wira' => 'admin.competition.scores.grid-wira',
                default => 'admin.competition.scores.grid-mula',
            };
        }

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

        $team = CompetitionParticipantTeam::find($teamId);
        $teamName = $team?->team_name ?? 'Regu Peserta';

        $isPRS = str_contains(strtolower($category->name), 'pendidikan remaja sebaya') || str_contains(strtolower($category->name), 'prs') || str_starts_with($category->code ?? '', 'PRS');
        $isLKTR = str_contains(strtolower($category->name), 'tandu') || str_contains(strtolower($category->name), 'lktr') || str_starts_with($category->code ?? '', 'LKTR');

        if ($isLKTR) {
            $technicalScore = 0;
            foreach ($rubric as $k => $v) {
                $technicalScore += intval($v);
            }
            $timeScore = self::calculateLktrTimeScore($category->level ?? 'Madya', $timeRecorded);
            $totalScore = $technicalScore + $timeScore;

            $mergedDetails = array_merge($existingScore?->score_details ?? [], [
                'technical_score' => $technicalScore,
                'time_score' => $timeScore,
                'practical_score' => $technicalScore,
                'practical_time' => $timeRecorded,
                'time_recorded' => $timeRecorded,
                'time_seconds' => $timeSeconds,
                'grid_rubric' => $rubric,
                'subtotals' => $subtotals,
                'notes' => $notes,
            ]);

            $labelLomba = 'Ketangkasan Tandu Reguler (LKTR)';
            $msg = "Nilai Grid {$labelLomba} untuk regu '{$teamName}' berhasil disimpan (Teknis: {$technicalScore} + Waktu {$timeRecorded}: {$timeScore} = Total: {$totalScore}) dan otomatis terisi di Lembar Skor Utama!";
        } else {
            $totalScore = 0;
            foreach ($rubric as $k => $v) {
                $totalScore += intval($v);
            }

            $mergedDetails = array_merge($existingScore?->score_details ?? [], [
                'practical_score' => $totalScore,
                'practical_time' => $timeRecorded,
                'time_recorded' => $timeRecorded,
                'time_seconds' => $timeSeconds,
                'grid_rubric' => $rubric,
                'subtotals' => $subtotals,
                'notes' => $notes,
            ]);

            $labelLomba = $isPRS ? 'Pendidikan Remaja Sebaya' : 'Pertolongan Pertama';
            $waktuSuffix = $isPRS ? '' : ", Waktu: {$timeRecorded}";
            $msg = "Nilai Grid {$labelLomba} untuk regu '{$teamName}' berhasil disimpan (Total Skor: {$totalScore}{$waktuSuffix}) dan otomatis terisi di Lembar Skor Utama!";
        }

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
     * Konversi waktu tempuh ke nilai skor LKTR sesuai tingkatan (Mula vs Madya/Wira)
     */
    public static function calculateLktrTimeScore(string $level, string $timeRecorded): int
    {
        $seconds = self::parseTimeToSeconds($timeRecorded);
        if ($seconds === null || $seconds <= 0) {
            return 0;
        }

        if (strcasecmp(trim($level), 'Mula') === 0) {
            // Mula:
            // <= 03:00 (180s) => 300
            // 03:01 - 03:15 (181 - 195s) => 290
            // Interval 15 detik, berkurang 10 poin per interval
            // 09:46 - 10:00 (586 - 600s) => 20
            // > 10:00 (> 600s) => 0
            if ($seconds <= 180) {
                return 300;
            }
            if ($seconds > 600) {
                return 0;
            }
            $step = (int) ceil(($seconds - 180) / 15);
            $score = 300 - ($step * 10);

            return max($score, 20);
        } else {
            // Madya & Wira:
            // <= 02:40 (160s) => 300
            // 02:41 - 02:50 (161 - 170s) => 285
            // Interval 10 detik, berkurang 15 poin per interval
            // 05:21 - 05:30 (321 - 330s) => 45
            // > 05:30 (> 330s) => 0
            if ($seconds <= 160) {
                return 300;
            }
            if ($seconds > 330) {
                return 0;
            }
            $step = (int) ceil(($seconds - 160) / 10);
            $score = 300 - ($step * 15);

            return max($score, 45);
        }
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

    // =========================================================================
    // MODUL KHUSUS: SISTEM PENILAIAN LCT (LOMBA CEPAT TEPAT) 3 TAHAP
    // =========================================================================

    /**
     * Tampilan Utama Sistem Penilaian LCT 3 Tahap
     */
    public function lctView(CompetitionCategory $category, Request $request)
    {
        $category->loadMissing(['assignedJuries', 'event']);

        $activeStage = (int) $request->query('stage', 1);

        // Ambil semua tim peserta yang terdaftar pada kategori LCT ini
        $allTeams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->with('registration')
            ->orderBy('order_number')
            ->get();

        // 1. DATA SESI 1: TAHAP PENYISIHAN
        $prelimScores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Penyisihan')
                    ->orWhere('round_name', 'like', '%Penyisihan%')
                    ->orWhere('round_name', 'like', '%Babak 1%');
            })
            ->get()
            ->keyBy('competition_participant_team_id');

        // Petakan tim penyisihan dengan skor dan kalkulasi rank
        $prelimTeams = $allTeams->map(function ($team) use ($prelimScores) {
            $sc = $prelimScores->get($team->id);
            $teamCopy = clone $team;
            $teamCopy->prelim_score = $sc?->final_score !== null ? (float) $sc->final_score : null;
            $teamCopy->prelim_time = $sc?->time_recorded ?? null;
            $teamCopy->time_seconds = $sc ? ($sc->score_details['time_seconds'] ?? self::parseTimeToSeconds($sc->time_recorded) ?? 999999) : 999999;
            $teamCopy->prelim_rank = $sc?->rank ?? null;

            return $teamCopy;
        });

        // Urutkan klasemen penyisihan: Skor tertinggi -> Waktu tercepat (tie-breaker)
        $sortedPrelim = $prelimTeams->sort(function ($a, $b) {
            $scoreA = $a->prelim_score;
            $scoreB = $b->prelim_score;

            // Jika keduanya belum dinilai, urutkan berdasarkan order_number
            if ($scoreA === null && $scoreB === null) {
                return strnatcasecmp((string) ($a->order_number ?? ''), (string) ($b->order_number ?? ''));
            }
            // Jika salah satu belum dinilai, yang sudah dinilai diprioritaskan di atas
            if ($scoreA === null) {
                return 1;
            }
            if ($scoreB === null) {
                return -1;
            }

            // Keduanya sudah dinilai: Skor tertinggi di posisi teratas (descending)
            if ($scoreA != $scoreB) {
                return $scoreB <=> $scoreA;
            }

            // Jika nilai sama, waktu tempuh tercepat (ascending)
            return $a->time_seconds <=> $b->time_seconds;
        })->values();

        // Tetapkan ranking 1 s/d N dari skor tertinggi ke terendah dan flag lolos 9 besar
        $rankCounter = 1;
        $sortedPrelim->each(function ($t) use (&$rankCounter) {
            if ($t->prelim_score !== null) {
                $t->prelim_rank = $rankCounter;
                $t->is_top_9 = ($rankCounter <= 9);
                $rankCounter++;
            } else {
                $t->prelim_rank = null;
                $t->is_top_9 = false;
            }
        });

        $top9Teams = $sortedPrelim->filter(fn ($t) => $t->is_top_9)->values();

        // 2. DATA SESI 2: TAHAP SEMI-FINAL (3 TERMIN)
        $semiScores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Semi Final')
                    ->orWhere('round_name', 'like', '%Semi Final%')
                    ->orWhere('round_name', 'like', '%Semifinal%')
                    ->orWhere('round_name', 'like', '%Babak 2%');
            })
            ->get()
            ->keyBy('competition_participant_team_id');

        $termin1Teams = collect();
        $termin2Teams = collect();
        $termin3Teams = collect();

        // Jika data semifinal sudah ada di database, gunakan pembagian termin yang tersimpan
        foreach ($allTeams as $team) {
            $sc = $semiScores->get($team->id);
            if ($sc) {
                $details = is_array($sc->score_details) ? $sc->score_details : (json_decode($sc->score_details ?? '', true) ?? []);
                $termin = $details['termin'] ?? null;
                $tObj = clone $team;
                $tObj->soal_amplop = $details['soal_amplop'] ?? 0;
                $tObj->rebutan = $details['rebutan'] ?? 0;
                $tObj->balon = $details['balon'] ?? 0;
                $tObj->total_score = (float) $sc->final_score;
                $tObj->termin_rank = $sc->rank ?? null;
                $tObj->termin = $termin;

                if ($termin == 1) {
                    $termin1Teams->push($tObj);
                } elseif ($termin == 2) {
                    $termin2Teams->push($tObj);
                } elseif ($termin == 3) {
                    $termin3Teams->push($tObj);
                }
            }
        }

        // Hitung ranking per termin dan urutkan dari nilai tertinggi ke terendah
        $termin1Teams = $termin1Teams->sort(fn ($a, $b) => $b->total_score <=> $a->total_score)->values();
        $termin1Teams->each(function ($t, $idx) {
            $t->termin_rank = $idx + 1;
        });

        $termin2Teams = $termin2Teams->sort(fn ($a, $b) => $b->total_score <=> $a->total_score)->values();
        $termin2Teams->each(function ($t, $idx) {
            $t->termin_rank = $idx + 1;
        });

        $termin3Teams = $termin3Teams->sort(fn ($a, $b) => $b->total_score <=> $a->total_score)->values();
        $termin3Teams->each(function ($t, $idx) {
            $t->termin_rank = $idx + 1;
        });

        // Identifikasi 3 Finalis (Juara 1 masing-masing termin)
        $finalist1 = $termin1Teams->sortBy('termin_rank')->first();
        $finalist2 = $termin2Teams->sortBy('termin_rank')->first();
        $finalist3 = $termin3Teams->sortBy('termin_rank')->first();

        $finalistsList = collect([$finalist1, $finalist2, $finalist3])->filter()->values();

        // 6 Tim Non-Finalis diurutkan untuk Juara Harapan 1 & 2
        $nonFinalists = collect();
        foreach ([$termin1Teams, $termin2Teams, $termin3Teams] as $tCol) {
            foreach ($tCol as $t) {
                if ($t->termin_rank > 1) {
                    $nonFinalists->push($t);
                }
            }
        }

        $sortedNonFinalists = $nonFinalists->sortByDesc('total_score')->values();
        $sortedNonFinalists->each(function ($t, $idx) {
            $t->harapan_rank = $idx + 1;
            $t->overall_rank = $idx + 4; // Juara 1-3 untuk finalis, maka 4, 5, 6, 7, 8, 9
            $t->is_harapan_1 = ($idx === 0);
            $t->is_harapan_2 = ($idx === 1);
        });

        $harapan1Team = $sortedNonFinalists->firstWhere('is_harapan_1', true);
        $harapan2Team = $sortedNonFinalists->firstWhere('is_harapan_2', true);

        // 3. DATA SESI 3: GRAND FINAL
        $finalScores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Final')
                    ->orWhere('round_name', 'like', '%Babak 3%')
                    ->orWhere('round_name', 'like', '%Grand Final%');
            })
            ->get()
            ->keyBy('competition_participant_team_id');

        $finalGrandTeams = collect();
        if ($finalistsList->isNotEmpty()) {
            foreach ($finalistsList as $fTeam) {
                $sc = $finalScores->get($fTeam->id);
                $details = is_array($sc?->score_details) ? $sc->score_details : (json_decode($sc?->score_details ?? '', true) ?? []);
                $fObj = clone $fTeam;
                $fObj->soal_amplop = $details['soal_amplop'] ?? 0;
                $fObj->rebutan_bertingkat = $details['rebutan_bertingkat'] ?? 0;
                $fObj->taruh_nilai = $details['taruh_nilai'] ?? 0;
                $fObj->total_score = (float) ($sc?->final_score ?? 0);
                $fObj->grand_rank = $sc?->rank ?? null;
                $finalGrandTeams->push($fObj);
            }
        } elseif ($finalScores->isNotEmpty()) {
            // Fallback robust: Muat tim dari skor babak Final yang sudah tersimpan
            $finalTeamModels = CompetitionParticipantTeam::whereIn('id', $finalScores->keys())->with('registration')->get();
            foreach ($finalTeamModels as $fTeam) {
                $sc = $finalScores->get($fTeam->id);
                $details = is_array($sc?->score_details) ? $sc->score_details : (json_decode($sc?->score_details ?? '', true) ?? []);
                $fObj = clone $fTeam;
                $fObj->soal_amplop = $details['soal_amplop'] ?? 0;
                $fObj->rebutan_bertingkat = $details['rebutan_bertingkat'] ?? 0;
                $fObj->taruh_nilai = $details['taruh_nilai'] ?? 0;
                $fObj->total_score = (float) ($sc?->final_score ?? 0);
                $fObj->grand_rank = $sc?->rank ?? null;
                $finalGrandTeams->push($fObj);
            }
        }

        if ($finalGrandTeams->isNotEmpty()) {
            // Urutkan grand final: Skor tertinggi -> Juara 1, 2, 3
            if ($finalGrandTeams->where('total_score', '>', 0)->isNotEmpty()) {
                $finalGrandTeams = $finalGrandTeams->sortByDesc('total_score')->values();
                $finalGrandTeams->each(function ($t, $idx) {
                    $t->grand_rank = $idx + 1;
                });
            } else {
                $finalGrandTeams->each(function ($t) {
                    $t->grand_rank = null;
                });
            }
        }

        $hasSemiResults = $termin1Teams->isNotEmpty() || $termin2Teams->isNotEmpty() || $termin3Teams->isNotEmpty();
        $hasFinalResults = $finalGrandTeams->isNotEmpty() && $finalGrandTeams->where('total_score', '>', 0)->isNotEmpty();

        return view('admin.competition.scores.grid-lct', compact(
            'category',
            'activeStage',
            'allTeams',
            'sortedPrelim',
            'top9Teams',
            'termin1Teams',
            'termin2Teams',
            'termin3Teams',
            'sortedNonFinalists',
            'harapan1Team',
            'harapan2Team',
            'finalGrandTeams',
            'hasSemiResults',
            'hasFinalResults'
        ));
    }

    /**
     * Simpan Nilai Sesi 1: Babak Penyisihan LCT
     */
    public function lctSavePrelim(CompetitionCategory $category, Request $request)
    {
        $inputScores = $request->input('prelim_scores', []);
        $inputTimes = $request->input('prelim_times', []);

        $juryUserId = $this->resolveSafeJuryUserId($category);

        try {
            DB::beginTransaction();

            foreach ($inputScores as $teamId => $scoreVal) {
                if ($scoreVal === null || $scoreVal === '') {
                    continue;
                }

                $rawTime = trim((string) ($inputTimes[$teamId] ?? ''));
                $timeStr = self::normalizeTimeString($rawTime);
                $timeSeconds = self::parseTimeToSeconds($timeStr) ?? 999999;
                $scoreFloat = floatval($scoreVal);

                $scoreData = [
                    'final_score' => $scoreFloat,
                    'time_recorded' => $timeStr ?: null,
                    'score_details' => [
                        'score' => $scoreFloat,
                        'time_seconds' => $timeSeconds,
                    ],
                ];
                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $scoreData['jury_user_id'] = $juryUserId;
                }

                CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $teamId,
                        'round_name' => 'Penyisihan',
                    ],
                    $scoreData
                );
            }

            // Kalkulasi ulang ranking penyisihan
            $allPrelim = CompetitionScore::where('competition_category_id', $category->id)
                ->where('round_name', 'Penyisihan')
                ->get();

            $sorted = $allPrelim->sort(function ($a, $b) {
                if ($a->final_score != $b->final_score) {
                    return $b->final_score <=> $a->final_score;
                }
                $detailsA = is_array($a->score_details) ? $a->score_details : (json_decode($a->score_details ?? '', true) ?? []);
                $detailsB = is_array($b->score_details) ? $b->score_details : (json_decode($b->score_details ?? '', true) ?? []);
                $secA = $detailsA['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
                $secB = $detailsB['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;

                return $secA <=> $secB;
            })->values();

            foreach ($sorted as $idx => $sc) {
                $sc->rank = $idx + 1;
                $sc->save();
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 1])
                ->with('success', 'Nilai & Waktu Tempuh Babak Penyisihan berhasil disimpan! Ranking otomatis diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in lctSavePrelim: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan nilai penyisihan: '.$e->getMessage());
        }
    }

    /**
     * Generate Bagan Semifinal Otomatis (Algoritma Pemisahan Satu Pangkalan Sekolah)
     */
    public function lctGenerateSemifinal(CompetitionCategory $category, Request $request)
    {
        try {
            DB::beginTransaction();

            // Ambil 9 besar dari Penyisihan
            $prelimScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where('round_name', 'Penyisihan')
                ->orderByRaw('`rank` IS NULL, `rank` ASC')
                ->take(9)
                ->get();

            if ($prelimScores->count() < 3) {
                DB::rollBack();

                return redirect()->back()->with('error', 'Peserta Babak Penyisihan yang dinilai belum mencukupi (minimal 3 regu).');
            }

            $teamIds = $prelimScores->pluck('competition_participant_team_id');
            $topTeams = CompetitionParticipantTeam::whereIn('id', $teamIds)
                ->with('registration')
                ->get();

            // Urutkan sesuai rank penyisihan
            $topTeams = $topTeams->sortBy(function ($t) use ($prelimScores) {
                return $prelimScores->firstWhere('competition_participant_team_id', $t->id)?->rank ?? 99;
            })->values();

            // Jalankan Algoritma Smart Bracket
            $distributed = self::distributeTeamsToTermins($topTeams);

            $juryUserId = $this->resolveSafeJuryUserId($category);

            // Simpan penugasan termin ke database
            foreach ($distributed as $terminNum => $teamsInTermin) {
                foreach ($teamsInTermin as $team) {
                    $existingSemi = CompetitionScore::where('competition_category_id', $category->id)
                        ->where('competition_participant_team_id', $team->id)
                        ->where('round_name', 'Semi Final')
                        ->first();

                    $details = is_array($existingSemi?->score_details) ? $existingSemi->score_details : (json_decode($existingSemi?->score_details ?? '', true) ?? []);
                    $details['termin'] = $terminNum;
                    $details['soal_amplop'] = $details['soal_amplop'] ?? 0;
                    $details['rebutan'] = $details['rebutan'] ?? 0;
                    $details['balon'] = $details['balon'] ?? 0;

                    $semiScoreData = [
                        'final_score' => $existingSemi?->final_score ?? 0,
                        'score_details' => $details,
                    ];
                    if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                        $semiScoreData['jury_user_id'] = $juryUserId;
                    }

                    CompetitionScore::updateOrCreate(
                        [
                            'competition_category_id' => $category->id,
                            'competition_participant_team_id' => $team->id,
                            'round_name' => 'Semi Final',
                        ],
                        $semiScoreData
                    );
                }
            }

            DB::commit();

            $totalLolos = $topTeams->count();

            return redirect()->route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 2])
                ->with('success', "Bagan Semifinal Berhasil Dibuat! {$totalLolos} Tim telah didistribusikan ke 3 Termin dengan algoritma pemisahan satu pangkalan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in lctGenerateSemifinal: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Gagal generate bagan semifinal: '.$e->getMessage());
        }
    }

    /**
     * Simpan Nilai Sesi 2: Semi-Final (3 Termin)
     */
    public function lctSaveSemifinal(CompetitionCategory $category, Request $request)
    {
        $amplop = $request->input('semi_amplop', []);
        $rebutan = $request->input('semi_rebutan', []);
        $balon = $request->input('semi_balon', []);
        $termins = $request->input('semi_termin', []);

        $juryUserId = $this->resolveSafeJuryUserId($category);
        $savedScores = [];

        try {
            DB::beginTransaction();

            foreach ($termins as $teamId => $terminNum) {
                $sAmplop = floatval($amplop[$teamId] ?? 0);
                $sRebutan = floatval($rebutan[$teamId] ?? 0);
                $sBalon = floatval($balon[$teamId] ?? 0);
                $total = $sAmplop + $sRebutan + $sBalon;

                $details = [
                    'termin' => (int) $terminNum,
                    'soal_amplop' => $sAmplop,
                    'rebutan' => $sRebutan,
                    'balon' => $sBalon,
                    'total' => $total,
                ];

                $semiData = [
                    'final_score' => $total,
                    'score_details' => $details,
                ];
                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $semiData['jury_user_id'] = $juryUserId;
                }

                $sc = CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $teamId,
                        'round_name' => 'Semi Final',
                    ],
                    $semiData
                );

                $savedScores[(int) $terminNum][] = $sc;
            }

            // Hitung rank per termin & siapkan finalis
            $finalistTeamIds = [];

            foreach ([1, 2, 3] as $tNum) {
                if (isset($savedScores[$tNum])) {
                    usort($savedScores[$tNum], fn ($a, $b) => $b->final_score <=> $a->final_score);
                    foreach ($savedScores[$tNum] as $idx => $sc) {
                        $sc->rank = $idx + 1;
                        $sc->save();
                        if ($idx === 0) {
                            $finalistTeamIds[] = $sc->competition_participant_team_id;
                        }
                    }
                }
            }

            // Otomatis daftarkan 3 pemenang termin ke Babak Final jika belum ada
            foreach ($finalistTeamIds as $fId) {
                CompetitionScore::firstOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $fId,
                        'round_name' => 'Final',
                    ],
                    [
                        'jury_user_id' => $juryUserId,
                        'final_score' => 0,
                        'score_details' => [
                            'soal_amplop' => 0,
                            'rebutan_bertingkat' => 0,
                            'taruh_nilai' => 0,
                        ],
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 2])
                ->with('success', 'Nilai Semifinal untuk seluruh termin berhasil disimpan! Juara tiap termin otomatis melaju ke Grand Final.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in lctSaveSemifinal: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan nilai semifinal: '.$e->getMessage());
        }
    }

    /**
     * Simpan Nilai Sesi 3: Grand Final
     */
    public function lctSaveFinal(CompetitionCategory $category, Request $request)
    {
        $amplop = $request->input('final_amplop', []);
        $rebutan = $request->input('final_rebutan', []);
        $taruh = $request->input('final_taruh', []);

        $juryUserId = $this->resolveSafeJuryUserId($category);
        $finalScores = [];

        try {
            DB::beginTransaction();

            foreach ($amplop as $teamId => $val) {
                $sAmplop = floatval($val ?? 0);
                $sRebutan = floatval($rebutan[$teamId] ?? 0);
                $sTaruh = floatval($taruh[$teamId] ?? 0);
                $total = $sAmplop + $sRebutan + $sTaruh;

                $details = [
                    'soal_amplop' => $sAmplop,
                    'rebutan_bertingkat' => $sRebutan,
                    'taruh_nilai' => $sTaruh,
                    'total' => $total,
                ];

                $finalData = [
                    'final_score' => $total,
                    'score_details' => $details,
                ];
                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $finalData['jury_user_id'] = $juryUserId;
                }

                $sc = CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $teamId,
                        'round_name' => 'Final',
                    ],
                    $finalData
                );

                $finalScores[] = $sc;
            }

            // Tentukan Juara 1, Juara 2, Juara 3 (Berdasarkan Skor Tertinggi)
            usort($finalScores, fn ($a, $b) => floatval($b->final_score) <=> floatval($a->final_score));
            foreach ($finalScores as $idx => $sc) {
                $sc->rank = $idx + 1;
                $sc->save();
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 3])
                ->with('success', 'Nilai Grand Final Berhasil Disimpan! Juara 1, Juara 2, dan Juara 3 telah resmi ditetapkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in lctSaveFinal: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan nilai grand final: '.$e->getMessage());
        }
    }

    /**
     * Muat Data Simulasi LCT Sesuai Gambar / Sheet Panitia
     */
    public function lctSeedSimulation(CompetitionCategory $category, Request $request)
    {
        $simulationData = [
            ['name' => 'SMAN 5 Bandung A', 'order' => 13, 'score' => 100, 'time' => '07:54', 'termin' => 1, 'amplop' => 110, 'rebutan' => 190, 'balon' => 0],
            ['name' => 'SMAN 1 Cianjur', 'order' => 20, 'score' => 100, 'time' => '10:33', 'termin' => 2, 'amplop' => 150, 'rebutan' => 150, 'balon' => 70],
            ['name' => 'SMAN 1 Karawang', 'order' => 7, 'score' => 98, 'time' => '11:16', 'termin' => 3, 'amplop' => 280, 'rebutan' => 200, 'balon' => 0, 'f_amplop' => 210, 'f_rebutan' => 250, 'f_taruh' => 190],
            ['name' => 'SMAN 1 Cikarang Utara B', 'order' => 1, 'score' => 92, 'time' => '07:45', 'termin' => 2, 'amplop' => 130, 'rebutan' => 120, 'balon' => 20],
            ['name' => 'SMAN 3 Bekasi', 'order' => 6, 'score' => 80, 'time' => '14:18', 'termin' => 1, 'amplop' => 240, 'rebutan' => 140, 'balon' => 20],
            ['name' => 'SMAN 5 Bandung B', 'order' => 11, 'score' => 76, 'time' => '07:00', 'termin' => 3, 'amplop' => 130, 'rebutan' => 40, 'balon' => 80],
            ['name' => 'SMAN 1 Subang', 'order' => 10, 'score' => 76, 'time' => '10:22', 'termin' => 2, 'amplop' => 270, 'rebutan' => 150, 'balon' => 70, 'f_amplop' => 200, 'f_rebutan' => 160, 'f_taruh' => 60],
            ['name' => 'SMAN 1 Garut', 'order' => 18, 'score' => 71, 'time' => '09:34', 'termin' => 3, 'amplop' => 140, 'rebutan' => 180, 'balon' => 70],
            ['name' => 'SMAN 1 Cikarang Utara A', 'order' => 2, 'score' => 70, 'time' => '07:28', 'termin' => 1, 'amplop' => 200, 'rebutan' => 200, 'balon' => 70, 'f_amplop' => 200, 'f_rebutan' => 300, 'f_taruh' => 140],
            ['name' => 'SMAN 1 Bogor', 'order' => 14, 'score' => 66, 'time' => '15:00'],
            ['name' => 'SMAN 1 Depok', 'order' => 16, 'score' => 65, 'time' => '13:43'],
            ['name' => 'SMAN 3 Bogor', 'order' => 15, 'score' => 60, 'time' => '08:34'],
            ['name' => 'SMAN 1 Tasikmalaya', 'order' => 19, 'score' => 60, 'time' => '11:00'],
            ['name' => 'SMAN 2 Bekasi', 'order' => 4, 'score' => 59, 'time' => '09:22'],
            ['name' => 'SMAN 2 Bandung', 'order' => 12, 'score' => 56, 'time' => '13:00'],
            ['name' => 'SMAN 1 Sukabumi', 'order' => 17, 'score' => 55, 'time' => '13:42'],
            ['name' => 'SMAN 1 Bekasi', 'order' => 5, 'score' => 53, 'time' => '08:12'],
            ['name' => 'SMAN 1 Cikampek', 'order' => 8, 'score' => 53, 'time' => '14:59'],
            ['name' => 'SMAN 1 Tambun Selatan', 'order' => 3, 'score' => 47, 'time' => '07:23'],
            ['name' => 'SMAN 1 Purwakarta', 'order' => 9, 'score' => 45, 'time' => '10:04'],
        ];

        $juryUserId = $this->resolveSafeJuryUserId($category);

        try {
            DB::beginTransaction();

            // Buat registrasi dan tim peserta jika belum ada
            foreach ($simulationData as $idx => $sim) {
                $regCode = 'SIM-'.str_pad((string) ($sim['order']), 3, '0', STR_PAD_LEFT);
                $reg = CompetitionRegistration::firstOrCreate(
                    [
                        'competition_event_id' => $category->competition_event_id,
                        'school_name' => $sim['name'],
                    ],
                    [
                        'registration_code' => $regCode,
                        'level' => $category->level ?: 'Wira',
                        'advisor_name' => 'Pembina '.$sim['name'],
                        'advisor_phone' => '081234567890',
                        'advisor_email' => 'pembina@'.Str::slug($sim['name']).'.sch.id',
                        'status' => 'verified',
                        'total_fee' => 150000,
                    ]
                );

                $team = CompetitionParticipantTeam::firstOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_registration_id' => $reg->id,
                    ],
                    [
                        'team_name' => $sim['name'],
                        'team_label' => 'Regu '.chr(65 + ($idx % 3)),
                        'order_number' => $sim['order'],
                        'is_active' => true,
                    ]
                );

                // Sesi 1: Penyisihan Score
                $timeSec = self::parseTimeToSeconds($sim['time']);
                $simPrelimData = [
                    'final_score' => $sim['score'],
                    'time_recorded' => $sim['time'],
                    'rank' => $idx + 1,
                    'score_details' => [
                        'score' => $sim['score'],
                        'time_seconds' => $timeSec,
                    ],
                ];
                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $simPrelimData['jury_user_id'] = $juryUserId;
                }

                CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $team->id,
                        'round_name' => 'Penyisihan',
                    ],
                    $simPrelimData
                );

                // Sesi 2: Semi-Final (Khusus 9 tim teratas)
                if (isset($sim['termin'])) {
                    $semiTotal = $sim['amplop'] + $sim['rebutan'] + $sim['balon'];
                    $simSemiData = [
                        'final_score' => $semiTotal,
                        'score_details' => [
                            'termin' => $sim['termin'],
                            'soal_amplop' => $sim['amplop'],
                            'rebutan' => $sim['rebutan'],
                            'balon' => $sim['balon'],
                            'total' => $semiTotal,
                        ],
                    ];
                    if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                        $simSemiData['jury_user_id'] = $juryUserId;
                    }

                    CompetitionScore::updateOrCreate(
                        [
                            'competition_category_id' => $category->id,
                            'competition_participant_team_id' => $team->id,
                            'round_name' => 'Semi Final',
                        ],
                        $simSemiData
                    );
                }

                // Sesi 3: Grand Final (Khusus 3 finalis)
                if (isset($sim['f_amplop'])) {
                    $finalTotal = $sim['f_amplop'] + $sim['f_rebutan'] + $sim['f_taruh'];
                    $simFinalData = [
                        'final_score' => $finalTotal,
                        'score_details' => [
                            'soal_amplop' => $sim['f_amplop'],
                            'rebutan_bertingkat' => $sim['f_rebutan'],
                            'taruh_nilai' => $sim['f_taruh'],
                            'total' => $finalTotal,
                        ],
                    ];
                    if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                        $simFinalData['jury_user_id'] = $juryUserId;
                    }

                    CompetitionScore::updateOrCreate(
                        [
                            'competition_category_id' => $category->id,
                            'competition_participant_team_id' => $team->id,
                            'round_name' => 'Final',
                        ],
                        $simFinalData
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => 1])
                ->with('success', 'Data simulasi 20 regu LCT sesuai contoh Excel panitia berhasil dimuat ke sistem!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in lctSeedSimulation: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Gagal memuat data simulasi: '.$e->getMessage());
        }
    }

    /**
     * Reset Nilai Berdasarkan Tahap LCT
     */
    public function lctResetStage(CompetitionCategory $category, Request $request)
    {
        $stage = $request->input('stage');

        try {
            if ($stage === 'prelim') {
                CompetitionScore::where('competition_category_id', $category->id)
                    ->where('round_name', 'Penyisihan')
                    ->delete();
                $msg = 'Seluruh data Babak Penyisihan berhasil direset.';
                $targetStage = 1;
            } elseif ($stage === 'semifinal') {
                CompetitionScore::where('competition_category_id', $category->id)
                    ->where('round_name', 'Semi Final')
                    ->delete();
                $msg = 'Seluruh data Babak Semi-Final (3 Termin) berhasil direset.';
                $targetStage = 2;
            } elseif ($stage === 'final') {
                CompetitionScore::where('competition_category_id', $category->id)
                    ->where('round_name', 'Final')
                    ->delete();
                $msg = 'Seluruh data Babak Grand Final berhasil direset.';
                $targetStage = 3;
            } else {
                CompetitionScore::where('competition_category_id', $category->id)->delete();
                $msg = 'Seluruh data nilai LCT berhasil direset total.';
                $targetStage = 1;
            }

            return redirect()->route('admin.competition-scores.lct', ['category' => $category->id, 'stage' => $targetStage])
                ->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Error in lctResetStage: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Gagal mereset data: '.$e->getMessage());
        }
    }

    /**
     * Layar Proyektor / Panggung Live View LCT
     */
    public function lctLiveScreen(CompetitionCategory $category, Request $request)
    {
        $category->loadMissing(['event']);
        $stage = (int) $request->query('stage', 2);
        $termin = (int) $request->query('termin', 1);

        // Ambil data skor live
        $semiScores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Semi Final')
                    ->orWhere('round_name', 'like', '%Semi Final%');
            })
            ->with('team.registration')
            ->get();

        $terminTeams = $semiScores->filter(function ($s) use ($termin) {
            return ($s->score_details['termin'] ?? null) == $termin;
        })->sortByDesc('final_score')->values();

        $finalScores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Final')
                    ->orWhere('round_name', 'like', '%Final%');
            })
            ->with('team.registration')
            ->get()
            ->sortByDesc('final_score')
            ->values();

        return view('admin.competition.scores.lct-live', compact(
            'category',
            'stage',
            'termin',
            'terminTeams',
            'finalScores'
        ));
    }

    /**
     * Export Excel Lembar Penilaian LCT 3 Tahap
     */
    public function lctExportExcel(CompetitionCategory $category, Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        return $this->exportExcel($category, $spreadsheetService);
    }

    /**
     * Algoritma Smart Bracket Distribution (Pemisahan Sekolah yang Sama di Semifinal)
     */
    public static function distributeTeamsToTermins($top9Teams): array
    {
        // Kelompokkan tim berdasarkan nama sekolah
        $bySchool = [];
        foreach ($top9Teams as $team) {
            $schoolKey = strtoupper(trim($team->registration?->school_name ?? $team->school_name ?? $team->team_name));
            $bySchool[$schoolKey][] = $team;
        }

        // Urutkan grup sekolah berdasarkan jumlah tim terbanyak (sekolah dengan 2 tim diproses lebih dulu)
        uasort($bySchool, fn ($a, $b) => count($b) <=> count($a));

        $termins = [
            1 => collect(),
            2 => collect(),
            3 => collect(),
        ];

        // 1. Distribusikan sekolah dengan multiple tim (misal meloloskan 2 regu)
        foreach ($bySchool as $schoolName => $schoolTeams) {
            if (count($schoolTeams) > 1) {
                $availableTerminKeys = [1, 2, 3];
                // Urutkan termin berdasarkan jumlah tim yang paling sedikit
                usort($availableTerminKeys, fn ($a, $b) => $termins[$a]->count() <=> $termins[$b]->count());

                foreach ($schoolTeams as $idx => $team) {
                    $targetTermin = $availableTerminKeys[$idx % count($availableTerminKeys)];
                    $termins[$targetTermin]->push($team);
                }
            } else {
                // 2. Sekolah dengan 1 regu: tempatkan di termin yang belum ada sekolah ini dan kuota masih < 3
                $team = $schoolTeams[0];
                $availableTerminKeys = [1, 2, 3];
                usort($availableTerminKeys, function ($a, $b) use ($termins, $schoolName) {
                    $hasA = $termins[$a]->contains(fn ($t) => strtoupper(trim($t->registration?->school_name ?? '')) === $schoolName);
                    $hasB = $termins[$b]->contains(fn ($t) => strtoupper(trim($t->registration?->school_name ?? '')) === $schoolName);
                    if ($hasA !== $hasB) {
                        return $hasA ? 1 : -1;
                    }

                    return $termins[$a]->count() <=> $termins[$b]->count();
                });

                $targetTermin = $availableTerminKeys[0];
                $termins[$targetTermin]->push($team);
            }
        }

        return $termins;
    }

    // =========================================================================
    // MODUL KHUSUS: SISTEM PENILAIAN OLIMPIADE KEPALANGMERAHAN (OLIM)
    // =========================================================================

    /**
     * Tampilan Utama Grid Penilaian Olimpiade Kepalangmerahan (Madya & Wira) - Sistem 3 Tahap
     */
    public function olimpiadeView(CompetitionCategory $category, Request $request)
    {
        $category->loadMissing(['assignedJuries', 'event']);

        $activeStage = (int) $request->query('stage', 1);

        // Ambil seluruh tim peserta yang terdaftar pada kategori Olimpiade ini
        $allTeams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->with(['registration'])
            ->orderBy('order_number')
            ->get();

        // 1. DATA SESI 1: TAHAP PENYISIHAN (50 Soal)
        $scores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Penyisihan')
                    ->orWhere('round_name', 'Utama')
                    ->orWhere('round_name', 'Olimpiade')
                    ->orWhereNull('round_name');
            })
            ->get()
            ->keyBy('competition_participant_team_id');

        // Cek formula yang tersimpan atau dipilih (default: skema2 sesuai sheet panitia)
        $firstScoreWithFormula = $scores->first(function ($sc) {
            $d = is_array($sc?->score_details) ? $sc->score_details : (json_decode($sc?->score_details ?? '', true) ?? []);

            return ! empty($d['formula_type']);
        });
        $savedFormulaType = null;
        if ($firstScoreWithFormula) {
            $d = is_array($firstScoreWithFormula->score_details) ? $firstScoreWithFormula->score_details : (json_decode($firstScoreWithFormula->score_details ?? '', true) ?? []);
            $savedFormulaType = $d['formula_type'] ?? null;
        }
        $activeFormulaType = $request->input('formula') ?? $savedFormulaType ?? 'skema2';

        // Petakan data peserta dengan rincian Benar, Salah, Kosong, Waktu Tempuh, Nilai Akhir
        $mappedTeams = $allTeams->map(function ($team) use ($scores, $activeFormulaType) {
            $sc = $scores->get($team->id);
            $details = is_array($sc?->score_details)
                ? $sc->score_details
                : (json_decode($sc?->score_details ?? '', true) ?? []);

            $teamCopy = clone $team;
            $teamCopy->score_record = $sc;
            $teamCopy->has_score = ($sc !== null && $sc->final_score !== null);
            $teamCopy->benar = isset($details['benar']) ? (int) $details['benar'] : ($teamCopy->has_score ? (int) ($details['correct'] ?? 0) : null);
            $teamCopy->salah = isset($details['salah']) ? (int) $details['salah'] : ($teamCopy->has_score ? (int) ($details['wrong'] ?? 0) : null);

            $b = $teamCopy->benar ?? 0;
            $s = $teamCopy->salah ?? 0;
            $teamCopy->kosong = isset($details['kosong']) ? (int) $details['kosong'] : ($teamCopy->has_score ? 0 : null);

            $timeStr = $sc?->time_recorded ?: ($details['waktu_tempuh'] ?? null);
            $teamCopy->waktu = self::normalizeTimeString($timeStr);
            $teamCopy->time_seconds = $sc ? ($details['time_seconds'] ?? self::parseTimeToSeconds($timeStr) ?? 999999) : 999999;

            $standardScore = $teamCopy->has_score ? (float) (($b - $s) * 2) : null;
            $skema2Score = $teamCopy->has_score ? (float) (50 + $b - $s) : null;
            $teamCopy->score_standard = $standardScore;
            $teamCopy->score_skema2 = $skema2Score;

            if ($teamCopy->has_score) {
                if ($activeFormulaType === 'standard') {
                    $teamCopy->final_score = $standardScore;
                    $teamCopy->score_alt = $skema2Score;
                } else {
                    $teamCopy->final_score = $skema2Score;
                    $teamCopy->score_alt = $standardScore;
                }
            } else {
                $teamCopy->final_score = null;
                $teamCopy->score_alt = null;
            }

            $teamCopy->rank = $sc?->rank;

            return $teamCopy;
        });

        // Urutkan klasemen penyisihan: Nilai Tertinggi (DESC) -> Waktu Tempuh Tercepat (ASC)
        $sortedTeams = $mappedTeams->sort(function ($a, $b) {
            $scoreA = $a->final_score;
            $scoreB = $b->final_score;

            // Jika keduanya belum dinilai, urutkan berdasarkan order_number
            if ($scoreA === null && $scoreB === null) {
                return strnatcasecmp((string) ($a->order_number ?? ''), (string) ($b->order_number ?? ''));
            }
            // Prioritaskan yang sudah dinilai di atas
            if ($scoreA === null) {
                return 1;
            }
            if ($scoreB === null) {
                return -1;
            }

            // Keduanya dinilai: Nilai tertinggi di posisi teratas
            if ($scoreA != $scoreB) {
                return $scoreB <=> $scoreA;
            }

            // Jika nilai sama, waktu tempuh tercepat (ascending)
            return $a->time_seconds <=> $b->time_seconds;
        })->values();

        // Tetapkan peringkat & Predikat Juara langsung (Sistem 1 Sesi Olimpiade)
        $rankCounter = 1;
        $sortedTeams->each(function ($t) use (&$rankCounter) {
            if ($t->has_score) {
                $curRank = $rankCounter;
                $t->rank = $curRank;
                $t->award_title = match ($curRank) {
                    1 => 'Juara 1',
                    2 => 'Juara 2',
                    3 => 'Juara 3',
                    4 => 'Juara Harapan 1',
                    5 => 'Juara Harapan 2',
                    6 => 'Juara Harapan 3',
                    default => "Peringkat {$curRank}",
                };
                $t->is_winner = ($curRank <= 6);
                $rankCounter++;
            } else {
                $t->rank = null;
                $t->award_title = 'Belum Dinilai';
                $t->is_winner = false;
            }
        });

        // 6 Besar Pemenang Resmi Olimpiade (Langsung dari hasil 1 sesi)
        $juara1Team = $sortedTeams->firstWhere('rank', 1);
        $juara2Team = $sortedTeams->firstWhere('rank', 2);
        $juara3Team = $sortedTeams->firstWhere('rank', 3);
        $harapan1Team = $sortedTeams->firstWhere('rank', 4);
        $harapan2Team = $sortedTeams->firstWhere('rank', 5);
        $harapan3Team = $sortedTeams->firstWhere('rank', 6);

        // Statistik Penilaian Olimpiade
        $scoredList = $sortedTeams->where('has_score', true);
        $totalTeamsCount = $allTeams->count();
        $scoredCount = $scoredList->count();
        $highestScore = $scoredList->isNotEmpty() ? $scoredList->max('final_score') : 0;
        $highestTeam = $scoredList->firstWhere('final_score', $highestScore);
        $avgScore = $scoredList->isNotEmpty() ? round($scoredList->avg('final_score'), 1) : 0;

        $fastestTeam = $scoredList->sortBy('time_seconds')->first();
        $fastestTime = $fastestTeam?->waktu ?: '00:00';

        // Formula perhitungan nilai olimpiade
        $activeFormulaLabel = ($activeFormulaType === 'standard')
            ? '(Benar - Salah) * 2'
            : '50 + Benar - Salah (Skema 2)';

        return view('admin.competition.scores.grid-olimpiade', compact(
            'category',
            'activeStage',
            'activeFormulaType',
            'activeFormulaLabel',
            'allTeams',
            'sortedTeams',
            'juara1Team',
            'juara2Team',
            'juara3Team',
            'harapan1Team',
            'harapan2Team',
            'harapan3Team',
            'totalTeamsCount',
            'scoredCount',
            'highestScore',
            'highestTeam',
            'avgScore',
            'fastestTime',
            'fastestTeam'
        ));
    }

    /**
     * Simpan Seluruh Nilai Babak Penyisihan Olimpiade (Batch Save & Rank Calculation)
     */
    public function olimpiadeSave(CompetitionCategory $category, Request $request)
    {
        $inputBenar = $request->input('benar', []);
        $inputSalah = $request->input('salah', []);
        $inputKosong = $request->input('kosong', []);
        $inputWaktu = $request->input('waktu', []);
        $formulaType = $request->input('formula_type', 'skema2');

        $juryUserId = $this->resolveSafeJuryUserId($category);

        try {
            DB::beginTransaction();

            foreach ($inputBenar as $teamId => $bVal) {
                if ($bVal === null || $bVal === '') {
                    continue;
                }

                // Mendukung input hingga 3 digit (contoh: 200)
                $b = max(0, min(999, (int) $bVal));
                $s = max(0, min(999, (int) ($inputSalah[$teamId] ?? 0)));

                if (isset($inputKosong[$teamId]) && $inputKosong[$teamId] !== '') {
                    $k = max(0, min(999, (int) $inputKosong[$teamId]));
                } else {
                    $k = 0;
                }

                $rawTime = trim((string) ($inputWaktu[$teamId] ?? ''));
                $timeStr = self::normalizeTimeString($rawTime);
                $timeSec = self::parseTimeToSeconds($timeStr) ?? 999999;

                // Perhitungan Nilai Akhir sesuai formula aktif
                if ($formulaType === 'standard') {
                    $finalScore = (float) (($b - $s) * 2);
                    $formulaLabel = '(Benar - Salah) * 2';
                    $formulaDesc = 'Nilai Benar dikurangi Nilai Salah dikali 2';
                } else {
                    $finalScore = (float) (50 + $b - $s);
                    $formulaLabel = '50 + Benar - Salah';
                    $formulaDesc = 'Skema 2: Skor dasar 50 ditambah Benar dikurangi Salah (Draft Sheet Panitia)';
                }

                $scoreData = [
                    'final_score' => $finalScore,
                    'time_recorded' => $timeStr ?: null,
                    'score_details' => [
                        'benar' => $b,
                        'salah' => $s,
                        'kosong' => $k,
                        'waktu_tempuh' => $timeStr,
                        'time_seconds' => $timeSec,
                        'formula_type' => $formulaType,
                        'formula_label' => $formulaLabel,
                        'formula_desc' => $formulaDesc,
                    ],
                ];

                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $scoreData['jury_user_id'] = $juryUserId;
                }

                // Hapus duplikat ronde lama jika ada
                CompetitionScore::where('competition_category_id', $category->id)
                    ->where('competition_participant_team_id', $teamId)
                    ->whereIn('round_name', ['Penyisihan', 'Olimpiade'])
                    ->delete();

                CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $teamId,
                        'round_name' => 'Utama',
                    ],
                    $scoreData
                );
            }

            // Hitung ulang ranking resmi secara konsisten (Skor tertinggi -> Waktu tercepat)
            $allScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where(function ($q) {
                    $q->where('round_name', 'Utama')
                        ->orWhere('round_name', 'Penyisihan')
                        ->orWhere('round_name', 'Olimpiade');
                })
                ->where('is_disqualified', false)
                ->get();

            $sorted = $allScores->sort(function ($a, $b) {
                if ($a->final_score != $b->final_score) {
                    return $b->final_score <=> $a->final_score;
                }
                $detailsA = is_array($a->score_details) ? $a->score_details : (json_decode($a->score_details ?? '', true) ?? []);
                $detailsB = is_array($b->score_details) ? $b->score_details : (json_decode($b->score_details ?? '', true) ?? []);
                $secA = $detailsA['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
                $secB = $detailsB['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;

                return $secA <=> $secB;
            })->values();

            foreach ($sorted as $idx => $sc) {
                $curRank = $idx + 1;
                $sc->rank = $curRank;
                $awardTitle = match ($curRank) {
                    1 => 'Juara 1',
                    2 => 'Juara 2',
                    3 => 'Juara 3',
                    4 => 'Juara Harapan 1',
                    5 => 'Juara Harapan 2',
                    6 => 'Juara Harapan 3',
                    default => "Peringkat {$curRank}",
                };
                $details = is_array($sc->score_details) ? $sc->score_details : (json_decode($sc->score_details ?? '', true) ?? []);
                $details['award_title'] = $awardTitle;
                $details['predicate'] = $awardTitle;
                $sc->score_details = $details;
                $sc->save();
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.olimpiade', $category->id)
                ->with('success', 'Seluruh nilai Olimpiade berhasil disimpan! Juara 1 s.d. Harapan 3 dan seluruh peringkat telah resmi ditetapkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in olimpiadeSave: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan nilai olimpiade: '.$e->getMessage());
        }
    }

    /**
     * Generate Bagan Semifinal Olimpiade Otomatis (Top 9 & Anti-Bentrokan Satu Pangkalan)
     */
    public function olimpiadeGenerateSemifinal(CompetitionCategory $category, Request $request)
    {
        try {
            DB::beginTransaction();

            // Ambil 9 besar dari Penyisihan Olimpiade
            $prelimScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where(function ($q) {
                    $q->where('round_name', 'Penyisihan')
                        ->orWhere('round_name', 'Utama')
                        ->orWhereNull('round_name');
                })
                ->orderByRaw('`rank` IS NULL, `rank` ASC')
                ->take(9)
                ->get();

            if ($prelimScores->count() < 3) {
                DB::rollBack();

                return redirect()->back()->with('error', 'Peserta Babak Penyisihan Olimpiade yang dinilai belum mencukupi (minimal 3 regu untuk semifinal).');
            }

            $teamIds = $prelimScores->pluck('competition_participant_team_id');
            $topTeams = CompetitionParticipantTeam::whereIn('id', $teamIds)
                ->with('registration')
                ->get();

            // Urutkan sesuai rank penyisihan
            $topTeams = $topTeams->sortBy(function ($t) use ($prelimScores) {
                return $prelimScores->firstWhere('competition_participant_team_id', $t->id)?->rank ?? 99;
            })->values();

            // Jalankan Algoritma Smart Bracket
            $distributed = self::distributeTeamsToTermins($topTeams);

            $juryUserId = $this->resolveSafeJuryUserId($category);

            // Simpan penugasan termin ke database
            foreach ($distributed as $terminNum => $teamsInTermin) {
                foreach ($teamsInTermin as $team) {
                    $existingSemi = CompetitionScore::where('competition_category_id', $category->id)
                        ->where('competition_participant_team_id', $team->id)
                        ->where('round_name', 'Semi Final')
                        ->first();

                    $details = is_array($existingSemi?->score_details) ? $existingSemi->score_details : (json_decode($existingSemi?->score_details ?? '', true) ?? []);
                    $details['termin'] = $terminNum;
                    $details['soal_amplop'] = $details['soal_amplop'] ?? 0;
                    $details['rebutan'] = $details['rebutan'] ?? 0;
                    $details['balon'] = $details['balon'] ?? 0;

                    $semiScoreData = [
                        'final_score' => $existingSemi?->final_score ?? 0,
                        'score_details' => $details,
                    ];
                    if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                        $semiScoreData['jury_user_id'] = $juryUserId;
                    }

                    CompetitionScore::updateOrCreate(
                        [
                            'competition_category_id' => $category->id,
                            'competition_participant_team_id' => $team->id,
                            'round_name' => 'Semi Final',
                        ],
                        $semiScoreData
                    );
                }
            }

            DB::commit();

            $totalLolos = $topTeams->count();

            return redirect()->route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 2])
                ->with('success', "Bagan Semifinal Olimpiade Berhasil Dibuat! {$totalLolos} Tim (Top 9) telah didistribusikan ke 3 Termin dengan algoritma pemisahan satu pangkalan sekolah.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in olimpiadeGenerateSemifinal: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Gagal generate bagan semifinal olimpiade: '.$e->getMessage());
        }
    }

    /**
     * Simpan Nilai Sesi 2: Semi-Final Olimpiade (3 Termin @ 3 Tim)
     */
    public function olimpiadeSaveSemifinal(CompetitionCategory $category, Request $request)
    {
        $amplop = $request->input('semi_amplop', []);
        $rebutan = $request->input('semi_rebutan', []);
        $balon = $request->input('semi_balon', []);
        $termins = $request->input('semi_termin', []);

        $juryUserId = $this->resolveSafeJuryUserId($category);
        $savedScores = [];

        try {
            DB::beginTransaction();

            foreach ($termins as $teamId => $terminNum) {
                $sAmplop = floatval($amplop[$teamId] ?? 0);
                $sRebutan = floatval($rebutan[$teamId] ?? 0);
                $sBalon = floatval($balon[$teamId] ?? 0);
                $total = $sAmplop + $sRebutan + $sBalon;

                $details = [
                    'termin' => (int) $terminNum,
                    'soal_amplop' => $sAmplop,
                    'rebutan' => $sRebutan,
                    'balon' => $sBalon,
                    'total' => $total,
                ];

                $semiData = [
                    'final_score' => $total,
                    'score_details' => $details,
                ];
                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $semiData['jury_user_id'] = $juryUserId;
                }

                $sc = CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $teamId,
                        'round_name' => 'Semi Final',
                    ],
                    $semiData
                );

                $savedScores[(int) $terminNum][] = $sc;
            }

            // Hitung rank per termin & tentukan finalis
            $finalistTeamIds = [];

            foreach ([1, 2, 3] as $tNum) {
                if (isset($savedScores[$tNum])) {
                    usort($savedScores[$tNum], fn ($a, $b) => $b->final_score <=> $a->final_score);
                    foreach ($savedScores[$tNum] as $idx => $sc) {
                        $sc->rank = $idx + 1;
                        $sc->save();
                        if ($idx === 0) {
                            $finalistTeamIds[] = $sc->competition_participant_team_id;
                        }
                    }
                }
            }

            // Otomatis daftarkan 3 pemenang termin ke Babak Grand Final jika belum ada
            foreach ($finalistTeamIds as $fId) {
                CompetitionScore::firstOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $fId,
                        'round_name' => 'Final',
                    ],
                    [
                        'jury_user_id' => $juryUserId,
                        'final_score' => 0,
                        'score_details' => [
                            'soal_amplop' => 0,
                            'rebutan_bertingkat' => 0,
                            'taruh_nilai' => 0,
                        ],
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 2])
                ->with('success', 'Nilai Semifinal Olimpiade berhasil disimpan! Juara tiap termin otomatis melaju ke Grand Final.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in olimpiadeSaveSemifinal: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan nilai semifinal olimpiade: '.$e->getMessage());
        }
    }

    /**
     * Simpan Nilai Sesi 3: Grand Final Olimpiade
     */
    public function olimpiadeSaveFinal(CompetitionCategory $category, Request $request)
    {
        $amplop = $request->input('final_amplop', []);
        $rebutan = $request->input('final_rebutan', []);
        $taruh = $request->input('final_taruh', []);

        $juryUserId = $this->resolveSafeJuryUserId($category);
        $finalScores = [];

        try {
            DB::beginTransaction();

            foreach ($amplop as $teamId => $val) {
                $sAmplop = floatval($val ?? 0);
                $sRebutan = floatval($rebutan[$teamId] ?? 0);
                $sTaruh = floatval($taruh[$teamId] ?? 0);
                $total = $sAmplop + $sRebutan + $sTaruh;

                $details = [
                    'soal_amplop' => $sAmplop,
                    'rebutan_bertingkat' => $sRebutan,
                    'taruh_nilai' => $sTaruh,
                    'total' => $total,
                ];

                $finalData = [
                    'final_score' => $total,
                    'score_details' => $details,
                ];
                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $finalData['jury_user_id'] = $juryUserId;
                }

                $sc = CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $teamId,
                        'round_name' => 'Final',
                    ],
                    $finalData
                );

                $finalScores[] = $sc;
            }

            // Urutkan dan tentukan Juara 1, 2, 3 Grand Final
            usort($finalScores, fn ($a, $b) => $b->final_score <=> $a->final_score);
            foreach ($finalScores as $idx => $sc) {
                $sc->rank = $idx + 1;
                $sc->save();
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => 3])
                ->with('success', 'Nilai Grand Final Olimpiade berhasil disimpan! Juara 1, 2, dan 3 resmi ditetapkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in olimpiadeSaveFinal: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan nilai final olimpiade: '.$e->getMessage());
        }
    }

    /**
     * Reset Nilai Berdasarkan Tahap Olimpiade
     */
    public function olimpiadeResetStage(CompetitionCategory $category, Request $request)
    {
        $stage = $request->input('stage');

        try {
            if ($stage === 'prelim') {
                CompetitionScore::where('competition_category_id', $category->id)
                    ->where(function ($q) {
                        $q->where('round_name', 'Penyisihan')
                            ->orWhere('round_name', 'Utama')
                            ->orWhere('round_name', 'Olimpiade')
                            ->orWhereNull('round_name');
                    })
                    ->delete();
                $msg = 'Seluruh data Babak Penyisihan Olimpiade berhasil direset.';
                $targetStage = 1;
            } elseif ($stage === 'semifinal') {
                CompetitionScore::where('competition_category_id', $category->id)
                    ->where('round_name', 'Semi Final')
                    ->delete();
                $msg = 'Seluruh data Babak Semi-Final (3 Termin) berhasil direset.';
                $targetStage = 2;
            } elseif ($stage === 'final') {
                CompetitionScore::where('competition_category_id', $category->id)
                    ->where('round_name', 'Final')
                    ->delete();
                $msg = 'Seluruh data Babak Grand Final berhasil direset.';
                $targetStage = 3;
            } else {
                CompetitionScore::where('competition_category_id', $category->id)->delete();
                $msg = 'Seluruh data nilai Olimpiade berhasil direset total.';
                $targetStage = 1;
            }

            return redirect()->route('admin.competition-scores.olimpiade', ['category' => $category->id, 'stage' => $targetStage])
                ->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Error in olimpiadeResetStage: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Gagal mereset data: '.$e->getMessage());
        }
    }

    /**
     * Muat Data Simulasi Olimpiade Sesuai Draft Sheet Panitia (20 Tim Lengkap)
     */
    public function olimpiadeSeedSimulation(CompetitionCategory $category, Request $request)
    {
        $isMadya = strcasecmp((string) $category->level, 'Madya') === 0;

        // Data 20 regu sesuai sheet panitia
        $wiraSimulation = [
            ['name' => 'SMAN 1 Cikarang Utara B', 'order' => 1, 'benar' => 44, 'salah' => 2, 'kosong' => 4, 'waktu' => '07:45'],
            ['name' => 'SMAN 1 Cikarang Utara A', 'order' => 2, 'benar' => 32, 'salah' => 12, 'kosong' => 6, 'waktu' => '07:28'],
            ['name' => 'SMAN 1 Tambun Selatan', 'order' => 3, 'benar' => 21, 'salah' => 24, 'kosong' => 5, 'waktu' => '07:23'],
            ['name' => 'SMAN 2 Bekasi', 'order' => 4, 'benar' => 26, 'salah' => 17, 'kosong' => 7, 'waktu' => '09:22'],
            ['name' => 'SMAN 1 Bekasi', 'order' => 5, 'benar' => 24, 'salah' => 21, 'kosong' => 5, 'waktu' => '08:12'],
            ['name' => 'SMAN 3 Bekasi', 'order' => 6, 'benar' => 38, 'salah' => 8, 'kosong' => 4, 'waktu' => '14:18'],
            ['name' => 'SMAN 1 Karawang', 'order' => 7, 'benar' => 48, 'salah' => 0, 'kosong' => 2, 'waktu' => '11:16'],
            ['name' => 'SMAN 1 Cikampek', 'order' => 8, 'benar' => 23, 'salah' => 20, 'kosong' => 7, 'waktu' => '14:59'],
            ['name' => 'SMAN 1 Purwakarta', 'order' => 9, 'benar' => 20, 'salah' => 25, 'kosong' => 5, 'waktu' => '10:04'],
            ['name' => 'SMAN 1 Subang', 'order' => 10, 'benar' => 35, 'salah' => 9, 'kosong' => 6, 'waktu' => '10:22'],
            ['name' => 'SMAN 5 Bandung B', 'order' => 11, 'benar' => 36, 'salah' => 10, 'kosong' => 4, 'waktu' => '07:00'],
            ['name' => 'SMAN 2 Bandung', 'order' => 12, 'benar' => 25, 'salah' => 19, 'kosong' => 6, 'waktu' => '13:00'],
            ['name' => 'SMAN 5 Bandung A', 'order' => 13, 'benar' => 50, 'salah' => 0, 'kosong' => 0, 'waktu' => '07:54'],
            ['name' => 'SMAN 1 Bogor', 'order' => 14, 'benar' => 30, 'salah' => 14, 'kosong' => 6, 'waktu' => '15:00'],
            ['name' => 'SMAN 3 Bogor', 'order' => 15, 'benar' => 28, 'salah' => 18, 'kosong' => 4, 'waktu' => '08:34'],
            ['name' => 'SMAN 1 Depok', 'order' => 16, 'benar' => 30, 'salah' => 15, 'kosong' => 5, 'waktu' => '13:43'],
            ['name' => 'SMAN 1 Sukabumi', 'order' => 17, 'benar' => 25, 'salah' => 20, 'kosong' => 5, 'waktu' => '13:42'],
            ['name' => 'SMAN 1 Garut', 'order' => 18, 'benar' => 32, 'salah' => 11, 'kosong' => 7, 'waktu' => '09:34'],
            ['name' => 'SMAN 1 Tasikmalaya', 'order' => 19, 'benar' => 27, 'salah' => 17, 'kosong' => 6, 'waktu' => '11:00'],
            ['name' => 'SMAN 1 Cianjur', 'order' => 20, 'benar' => 50, 'salah' => 0, 'kosong' => 0, 'waktu' => '10:33'],
        ];

        $madyaSimulation = [
            ['name' => 'SMPN 1 Cikarang Utara B', 'order' => 1, 'benar' => 44, 'salah' => 2, 'kosong' => 4, 'waktu' => '07:45'],
            ['name' => 'SMPN 1 Cikarang Utara A', 'order' => 2, 'benar' => 32, 'salah' => 12, 'kosong' => 6, 'waktu' => '07:28'],
            ['name' => 'SMPN 1 Tambun Selatan', 'order' => 3, 'benar' => 21, 'salah' => 24, 'kosong' => 5, 'waktu' => '07:23'],
            ['name' => 'SMPN 2 Bekasi', 'order' => 4, 'benar' => 26, 'salah' => 17, 'kosong' => 7, 'waktu' => '09:22'],
            ['name' => 'SMPN 1 Bekasi', 'order' => 5, 'benar' => 24, 'salah' => 21, 'kosong' => 5, 'waktu' => '08:12'],
            ['name' => 'SMPN 3 Bekasi', 'order' => 6, 'benar' => 38, 'salah' => 8, 'kosong' => 4, 'waktu' => '14:18'],
            ['name' => 'SMPN 1 Karawang', 'order' => 7, 'benar' => 48, 'salah' => 0, 'kosong' => 2, 'waktu' => '11:16'],
            ['name' => 'SMPN 1 Cikampek', 'order' => 8, 'benar' => 23, 'salah' => 20, 'kosong' => 7, 'waktu' => '14:59'],
            ['name' => 'SMPN 1 Purwakarta', 'order' => 9, 'benar' => 20, 'salah' => 25, 'kosong' => 5, 'waktu' => '10:04'],
            ['name' => 'SMPN 1 Subang', 'order' => 10, 'benar' => 35, 'salah' => 9, 'kosong' => 6, 'waktu' => '10:22'],
            ['name' => 'SMPN 5 Bandung B', 'order' => 11, 'benar' => 36, 'salah' => 10, 'kosong' => 4, 'waktu' => '07:00'],
            ['name' => 'SMPN 2 Bandung', 'order' => 12, 'benar' => 25, 'salah' => 19, 'kosong' => 6, 'waktu' => '13:00'],
            ['name' => 'SMPN 5 Bandung A', 'order' => 13, 'benar' => 50, 'salah' => 0, 'kosong' => 0, 'waktu' => '07:54'],
            ['name' => 'SMPN 1 Ciawi (Bogor)', 'order' => 14, 'benar' => 30, 'salah' => 14, 'kosong' => 6, 'waktu' => '15:00'],
            ['name' => 'SMPN 3 Bogor', 'order' => 15, 'benar' => 28, 'salah' => 18, 'kosong' => 4, 'waktu' => '08:34'],
            ['name' => 'SMPN 1 Depok', 'order' => 16, 'benar' => 30, 'salah' => 15, 'kosong' => 5, 'waktu' => '13:43'],
            ['name' => 'SMPN 1 Sukabumi', 'order' => 17, 'benar' => 25, 'salah' => 20, 'kosong' => 5, 'waktu' => '13:42'],
            ['name' => 'SMPN 1 Garut', 'order' => 18, 'benar' => 32, 'salah' => 11, 'kosong' => 7, 'waktu' => '09:34'],
            ['name' => 'MTsN 1 Kota Bogor', 'order' => 19, 'benar' => 27, 'salah' => 17, 'kosong' => 6, 'waktu' => '11:00'],
            ['name' => 'SMPN 1 Cianjur', 'order' => 20, 'benar' => 50, 'salah' => 0, 'kosong' => 0, 'waktu' => '10:33'],
        ];

        $simulationData = $isMadya ? $madyaSimulation : $wiraSimulation;
        $juryUserId = $this->resolveSafeJuryUserId($category);

        try {
            DB::beginTransaction();

            foreach ($simulationData as $sim) {
                $reg = CompetitionRegistration::firstOrCreate(
                    [
                        'competition_event_id' => $category->competition_event_id,
                        'school_name' => $sim['name'],
                    ],
                    [
                        'registration_code' => 'SIM-OLIM-'.str_pad((string) $sim['order'], 3, '0', STR_PAD_LEFT),
                        'level' => $category->level ?: ($isMadya ? 'Madya' : 'Wira'),
                        'advisor_name' => 'Pembina '.$sim['name'],
                        'advisor_phone' => '081234567890',
                        'advisor_email' => 'pembina@'.Str::slug($sim['name']).'.sch.id',
                        'status' => 'verified',
                        'total_fee' => 150000,
                    ]
                );

                $team = CompetitionParticipantTeam::firstOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_registration_id' => $reg->id,
                    ],
                    [
                        'team_name' => $sim['name'],
                        'team_label' => 'Regu Utama',
                        'order_number' => $sim['order'],
                        'is_active' => true,
                    ]
                );

                $b = $sim['benar'];
                $s = $sim['salah'];
                $k = $sim['kosong'];
                // Hitung Nilai Akhir sesuai Skema 2 sheet panitia: 50 + Benar - Salah
                $finalScore = (float) (50 + $b - $s);
                $timeSec = self::parseTimeToSeconds($sim['waktu']);

                $scoreData = [
                    'final_score' => $finalScore,
                    'time_recorded' => $sim['waktu'],
                    'score_details' => [
                        'benar' => $b,
                        'salah' => $s,
                        'kosong' => $k,
                        'waktu_tempuh' => $sim['waktu'],
                        'time_seconds' => $timeSec,
                        'formula_type' => 'skema2',
                        'formula_label' => '50 + Benar - Salah',
                        'formula_desc' => 'Skema 2: Skor dasar 50 ditambah Benar dikurangi Salah (Draft Sheet Panitia)',
                    ],
                ];

                if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                    $scoreData['jury_user_id'] = $juryUserId;
                }

                CompetitionScore::where('competition_category_id', $category->id)
                    ->where('competition_participant_team_id', $team->id)
                    ->whereIn('round_name', ['Penyisihan', 'Olimpiade'])
                    ->delete();

                CompetitionScore::updateOrCreate(
                    [
                        'competition_category_id' => $category->id,
                        'competition_participant_team_id' => $team->id,
                        'round_name' => 'Utama',
                    ],
                    $scoreData
                );
            }

            // Hitung ulang ranking (Nilai Tertinggi -> Waktu Tercepat)
            $allScores = CompetitionScore::where('competition_category_id', $category->id)
                ->where(function ($q) {
                    $q->where('round_name', 'Utama')
                        ->orWhere('round_name', 'Penyisihan')
                        ->orWhere('round_name', 'Olimpiade');
                })
                ->where('is_disqualified', false)
                ->get();

            $sorted = $allScores->sort(function ($a, $b) {
                if ($a->final_score != $b->final_score) {
                    return $b->final_score <=> $a->final_score;
                }
                $detailsA = is_array($a->score_details) ? $a->score_details : (json_decode($a->score_details ?? '', true) ?? []);
                $detailsB = is_array($b->score_details) ? $b->score_details : (json_decode($b->score_details ?? '', true) ?? []);
                $secA = $detailsA['time_seconds'] ?? self::parseTimeToSeconds($a->time_recorded) ?? 999999;
                $secB = $detailsB['time_seconds'] ?? self::parseTimeToSeconds($b->time_recorded) ?? 999999;

                return $secA <=> $secB;
            })->values();

            foreach ($sorted as $idx => $sc) {
                $curRank = $idx + 1;
                $sc->rank = $curRank;
                $awardTitle = match ($curRank) {
                    1 => 'Juara 1',
                    2 => 'Juara 2',
                    3 => 'Juara 3',
                    4 => 'Juara Harapan 1',
                    5 => 'Juara Harapan 2',
                    6 => 'Juara Harapan 3',
                    default => "Peringkat {$curRank}",
                };
                $details = is_array($sc->score_details) ? $sc->score_details : (json_decode($sc->score_details ?? '', true) ?? []);
                $details['award_title'] = $awardTitle;
                $details['predicate'] = $awardTitle;
                $sc->score_details = $details;
                $sc->save();
            }

            DB::commit();

            return redirect()->route('admin.competition-scores.olimpiade', $category->id)
                ->with('success', 'Data simulasi 20 regu Olimpiade sesuai sheet panitia berhasil dimuat! Juara 1 s.d. Harapan 3 langsung resmi ditentukan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in olimpiadeSeedSimulation: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->back()
                ->with('error', 'Gagal memuat data simulasi: '.$e->getMessage());
        }
    }

    /**
     * Reset Seluruh Nilai Olimpiade
     */
    public function olimpiadeReset(CompetitionCategory $category, Request $request)
    {
        CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Utama')
                    ->orWhere('round_name', 'Olimpiade')
                    ->orWhereNull('round_name');
            })
            ->delete();

        return redirect()->route('admin.competition-scores.olimpiade', $category->id)
            ->with('success', 'Seluruh nilai dan data ranking Olimpiade berhasil di-reset dan dikosongkan.');
    }

    /**
     * Layar Proyektor / Panggung Live Olimpiade
     */
    public function olimpiadeLiveScreen(CompetitionCategory $category, Request $request)
    {
        $category->loadMissing(['event']);

        $allTeams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->with(['registration'])
            ->get();

        $scores = CompetitionScore::where('competition_category_id', $category->id)
            ->where(function ($q) {
                $q->where('round_name', 'Utama')
                    ->orWhere('round_name', 'Penyisihan')
                    ->orWhere('round_name', 'Olimpiade')
                    ->orWhereNull('round_name');
            })
            ->get()
            ->keyBy('competition_participant_team_id');

        $teams = $allTeams->map(function ($team) use ($scores) {
            $sc = $scores->get($team->id);
            $details = is_array($sc?->score_details) ? $sc->score_details : (json_decode($sc?->score_details ?? '', true) ?? []);

            $teamCopy = clone $team;
            $teamCopy->final_score = $sc?->final_score !== null ? (float) $sc->final_score : null;
            $teamCopy->waktu = $sc?->time_recorded ? self::normalizeTimeString($sc->time_recorded) : ($details['waktu_tempuh'] ?? '-');
            $teamCopy->time_seconds = $sc ? ($details['time_seconds'] ?? self::parseTimeToSeconds($teamCopy->waktu) ?? 999999) : 999999;
            $teamCopy->benar = $details['benar'] ?? null;
            $teamCopy->salah = $details['salah'] ?? null;
            $teamCopy->kosong = $details['kosong'] ?? null;
            $teamCopy->rank = $sc?->rank;

            return $teamCopy;
        })->filter(fn ($t) => $t->final_score !== null)
            ->sortBy(function ($t) {
                // Skor tertinggi -> Waktu tercepat
                return [-$t->final_score, $t->time_seconds];
            })->values();

        // Tetapkan medali & predikat juara langsung
        $teams->each(function ($t, $idx) {
            $r = $idx + 1;
            $t->live_rank = $r;
            $t->award_title = match ($r) {
                1 => 'Juara 1',
                2 => 'Juara 2',
                3 => 'Juara 3',
                4 => 'Juara Harapan 1',
                5 => 'Juara Harapan 2',
                6 => 'Juara Harapan 3',
                default => "Peringkat {$r}",
            };
        });

        return view('admin.competition.scores.olimpiade-live', compact('category', 'teams'));
    }

    /**
     * Export Excel Lembar Penilaian Olimpiade
     */
    public function olimpiadeExportExcel(CompetitionCategory $category, Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        return $this->exportExcel($category, $request, $spreadsheetService);
    }
}

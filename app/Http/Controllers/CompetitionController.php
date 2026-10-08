<?php

namespace App\Http\Controllers;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompetitionController extends Controller
{
    public function index()
    {
        $event = CompetitionEvent::where('is_active', true)->with(['categories' => function($q) {
            $q->where('is_active', true)->orderBy('order_position');
        }])->first();

        if (!$event) {
            $event = CompetitionEvent::first();
        }

        $categoriesByLevel = $event ? $event->categories->groupBy('level') : collect();

        return view('pages.lomba.index', compact('event', 'categoriesByLevel'));
    }

    public function register()
    {
        $event = CompetitionEvent::where('is_active', true)->with(['categories' => function($q) {
            $q->where('is_active', true)->orderBy('order_position');
        }])->first();

        if (!$event || !$event->is_registration_open) {
            return redirect()->route('lomba.index')->with('error', 'Pendaftaran lomba saat ini sedang ditutup.');
        }

        $categoriesByLevel = $event->categories->groupBy('level');

        return view('pages.lomba.register', compact('event', 'categoriesByLevel'));
    }

    public function store(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'level' => 'required|in:Mula,Madya,Wira',
            'advisor_name' => 'required|string|max:255',
            'advisor_phone' => 'required|string|max:20',
            'advisor_email' => 'required|email|max:255',
            'school_address' => 'nullable|string',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:competition_categories,id',
            'team_labels' => 'nullable|array',
            'team_members' => 'nullable|array',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $code = CompetitionRegistration::generateRegistrationCode($validated['level'] ?? null);

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = 'proof_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('competition_payments', $filename, 'public');
        }

        $teamsToCreate = [];
        foreach ($validated['categories'] as $catId) {
            $labels = $request->input("team_labels.{$catId}");
            if (is_array($labels)) {
                $filtered = array_values(array_filter($labels, fn($l) => $l !== null && trim($l) !== ''));
                if (empty($filtered)) {
                    $filtered = [''];
                }
                foreach ($filtered as $lbl) {
                    $teamsToCreate[] = [
                        'category_id' => $catId,
                        'label' => trim($lbl),
                    ];
                }
            } else {
                $lbl = trim($labels ?? '');
                $teamsToCreate[] = [
                    'category_id' => $catId,
                    'label' => $lbl,
                ];
            }
        }

        $categoriesMap = CompetitionCategory::whereIn('id', $validated['categories'])->get()->keyBy('id');

        $totalTeams = count($teamsToCreate);
        $totalPayment = 0;
        foreach ($teamsToCreate as $teamData) {
            $cat = $categoriesMap->get($teamData['category_id']);
            $fee = $cat ? floatval($cat->registration_fee ?: ($event->registration_fee ?: 150000)) : floatval($event->registration_fee ?: 150000);
            $totalPayment += $fee;
        }

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => $code,
            'school_name' => strtoupper(trim($validated['school_name'])),
            'level' => $validated['level'],
            'advisor_name' => $validated['advisor_name'],
            'advisor_phone' => $validated['advisor_phone'],
            'advisor_email' => $validated['advisor_email'] ?? null,
            'school_address' => $validated['school_address'] ?? null,
            'payment_proof' => $proofPath,
            'total_payment' => $totalPayment,
            'status' => 'pending',
        ]);

        foreach ($teamsToCreate as $teamData) {
            $catId = $teamData['category_id'];
            $label = $teamData['label'];
            $teamName = $registration->school_name . ($label ? " {$label}" : '');

            CompetitionParticipantTeam::create([
                'competition_registration_id' => $registration->id,
                'competition_category_id' => $catId,
                'team_name' => $teamName,
                'team_label' => $label,
                'members_list' => [],
            ]);
        }

        return redirect()->route('lomba.status', ['code' => $code])->with('success', "Pendaftaran berhasil dikirim untuk {$totalTeams} regu! Kode Pendaftaran Anda: {$code}. Silakan simpan kode ini untuk mengecek status verifikasi panitia.");
    }

    public function status(Request $request)
    {
        $code = trim($request->query('code', ''));

        if (strtoupper($code) === 'SBB-TOCSEA') {
            CompetitionRegistration::where('registration_code', 'SBB-TOCSEA')
                ->update(['registration_code' => 'SBB-W54342H']);
            return redirect()->route('lomba.status', ['code' => 'SBB-W54342H']);
        }

        $registration = null;

        if ($code) {
            $registration = CompetitionRegistration::where('registration_code', $code)
                ->with(['event', 'teams.category'])
                ->first();
        }

        return view('pages.lomba.status', compact('registration', 'code'));
    }

    public function receipt($code)
    {
        if (strtoupper($code) === 'SBB-TOCSEA') {
            CompetitionRegistration::where('registration_code', 'SBB-TOCSEA')
                ->update(['registration_code' => 'SBB-W54342H']);
            return redirect()->route('lomba.kwitansi', ['code' => 'SBB-W54342H']);
        }

        $registration = CompetitionRegistration::where('registration_code', $code)
            ->orWhere('id', $code)
            ->with(['event', 'teams.category'])
            ->firstOrFail();

        return view('pages.lomba.receipt', compact('registration'));
    }

    public function participantCards($code)
    {
        if (strtoupper($code) === 'SBB-TOCSEA') {
            CompetitionRegistration::where('registration_code', 'SBB-TOCSEA')
                ->update(['registration_code' => 'SBB-W54342H']);
            return redirect()->route('lomba.kartu-peserta', ['code' => 'SBB-W54342H']);
        }

        $registration = CompetitionRegistration::where('registration_code', $code)
            ->orWhere('id', $code)
            ->with(['event', 'teams.category'])
            ->firstOrFail();

        return view('pages.lomba.cards', compact('registration'));
    }

    public function liveScoreboard(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->first();
        if (!$event) {
            $event = CompetitionEvent::first();
        }

        $level = $request->query('level', 'Madya');
        $categoryId = $request->query('category_id');

        $categories = CompetitionCategory::where('competition_event_id', $event?->id)
            ->where('level', $level)
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();

        $selectedCategory = null;
        $scores = collect();

        if ($categoryId) {
            $selectedCategory = CompetitionCategory::find($categoryId);
        } elseif ($categories->isNotEmpty()) {
            $selectedCategory = $categories->first();
        }

        if ($selectedCategory) {
            $scores = CompetitionScore::where('competition_category_id', $selectedCategory->id)
                ->with('team')
                ->orderBy('is_disqualified', 'asc')
                ->orderByRaw('`rank` IS NULL, `rank` ASC')
                ->orderBy('final_score', 'desc')
                ->get();
        }

        // Juara Umum Calculation per level
        $overallStandings = $this->calculateOverallStandings($event?->id, $level);

        return view('pages.lomba.scoreboard', compact('event', 'level', 'categories', 'selectedCategory', 'scores', 'overallStandings'));
    }

    private function calculateOverallStandings($eventId, $level)
    {
        if (!$eventId) return collect();

        $categories = CompetitionCategory::where('competition_event_id', $eventId)
            ->where('level', $level)
            ->where('is_active', true)
            ->get();

        $schoolPoints = [];

        foreach ($categories as $cat) {
            $topScores = CompetitionScore::where('competition_category_id', $cat->id)
                ->where('is_disqualified', false)
                ->whereIn('rank', [1, 2, 3])
                ->with('team.registration')
                ->get();

            foreach ($topScores as $score) {
                $schoolName = $score->team?->registration?->school_name ?? $score->team?->team_name;
                // Remove team suffix (A), (B) for overall school accumulation
                $schoolNameClean = preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $schoolName);

                if (!isset($schoolPoints[$schoolNameClean])) {
                    $schoolPoints[$schoolNameClean] = [
                        'school_name' => $schoolNameClean,
                        'details' => [],
                        'total_points' => 0,
                    ];
                }

                $points = 0;
                if ($cat->point_tier === 'tier_1') {
                    $points = match ($score->rank) { 1 => 10, 2 => 8, 3 => 6, default => 0 };
                } elseif ($cat->point_tier === 'tier_2') {
                    $points = match ($score->rank) { 1 => 8, 2 => 6, 3 => 4, default => 0 };
                } elseif ($cat->point_tier === 'tier_3') {
                    $points = match ($score->rank) { 1 => 3, 2 => 2, 3 => 1, default => 0 };
                }

                $schoolPoints[$schoolNameClean]['details'][$cat->name . ($cat->gender_category !== 'Umum' ? " {$cat->gender_category}" : '')] = $points;
                $schoolPoints[$schoolNameClean]['total_points'] += $points;
            }
        }

        return collect($schoolPoints)->sortByDesc('total_points')->values()->map(function ($item, $index) {
            $item['overall_rank'] = $index + 1;
            return $item;
        });
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CompetitionParticipantController extends Controller
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

        $level = $request->query('level');
        $categoryId = $request->query('category_id');
        $gender = $request->query('gender');
        $search = trim($request->query('q', ''));

        // Query all categories for filter options
        $categoriesQuery = CompetitionCategory::orderBy('order_position');
        if ($event) {
            $categoriesQuery->where(function($q) use ($event) {
                $q->where('competition_event_id', $event->id)
                  ->orWhereNull('competition_event_id');
            });
        }
        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $categoriesQuery->where('level', $level);
        }
        $categories = $categoriesQuery->get();

        // Main participant teams query (verified only)
        $teamsQuery = CompetitionParticipantTeam::where('is_active', true)
            ->where(function($q) use ($event) {
                $q->whereHas('registration', function($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                });
                if ($event) {
                    $q->orWhere(function($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                           ->whereHas('category', fn($cq) => $cq->where('competition_event_id', $event->id));
                    });
                } else {
                    $q->orWhereNull('competition_registration_id');
                }
            })
            ->with(['registration', 'category']);

        // Filter by Level
        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $teamsQuery->whereHas('category', function($q) use ($level) {
                $q->where('level', $level);
            });
        }

        // Filter by specific Category ID
        if ($categoryId) {
            $teamsQuery->where('competition_category_id', $categoryId);
        }

        // Filter by Gender Category (Putra / Putri / Umum)
        if ($gender && in_array($gender, ['Putra', 'Putri', 'Umum', 'Campuran'])) {
            $teamsQuery->whereHas('category', function($q) use ($gender) {
                $q->where('gender_category', $gender);
            });
        }

        // Search by keyword
        if ($search) {
            $teamsQuery->where(function($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('team_label', 'like', "%{$search}%")
                  ->orWhereHas('registration', function($rq) use ($search) {
                      $rq->where('school_name', 'like', "%{$search}%")
                        ->orWhere('registration_code', 'like', "%{$search}%")
                        ->orWhere('advisor_name', 'like', "%{$search}%");
                  });
            });
        }

        // Order by category order, gender, order_number, team_name
        $teams = $teamsQuery->get()->sortBy(function($team) {
            $catPos = $team->category ? str_pad($team->category->order_position, 3, '0', STR_PAD_LEFT) : '999';
            $genderOrder = $team->category && $team->category->gender_category === 'Putra' ? '1' : ($team->category && $team->category->gender_category === 'Putri' ? '2' : '3');
            $orderNum = $team->order_number ? str_pad($team->order_number, 4, '0', STR_PAD_LEFT) : '9999';
            return "{$catPos}-{$genderOrder}-{$orderNum}-{$team->team_name}";
        });

        // Summary counts
        $allVerifiedTeams = CompetitionParticipantTeam::where('is_active', true)
            ->where(function($q) use ($event) {
                $q->whereHas('registration', function($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                });
                if ($event) {
                    $q->orWhere(function($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                           ->whereHas('category', fn($cq) => $cq->where('competition_event_id', $event->id));
                    });
                } else {
                    $q->orWhereNull('competition_registration_id');
                }
            })
            ->with('category')
            ->get();

        $schoolsCountQuery = CompetitionRegistration::where('status', 'verified');
        if ($event) {
            $schoolsCountQuery->where('competition_event_id', $event->id);
        }

        $stats = [
            'total_teams' => $allVerifiedTeams->count(),
            'putra_teams' => $allVerifiedTeams->filter(fn($t) => $t->category?->gender_category === 'Putra')->count(),
            'putri_teams' => $allVerifiedTeams->filter(fn($t) => $t->category?->gender_category === 'Putri')->count(),
            'umum_teams'  => $allVerifiedTeams->filter(fn($t) => in_array($t->category?->gender_category, ['Umum', 'Campuran']))->count(),
            'mula_teams'  => $allVerifiedTeams->filter(fn($t) => $t->category?->level === 'Mula')->count(),
            'madya_teams' => $allVerifiedTeams->filter(fn($t) => $t->category?->level === 'Madya')->count(),
            'wira_teams'  => $allVerifiedTeams->filter(fn($t) => $t->category?->level === 'Wira')->count(),
            'total_schools' => $schoolsCountQuery->count(),
        ];

        // Group teams by Category for clean accordion/table display
        $teamsByCategory = $teams->groupBy('competition_category_id');

        return view('admin.competition.participants.index', compact(
            'event',
            'allEvents',
            'level',
            'categoryId',
            'gender',
            'search',
            'categories',
            'teams',
            'teamsByCategory',
            'stats'
        ));
    }

    public function update(Request $request, CompetitionParticipantTeam $team)
    {
        $validated = $request->validate([
            'order_number' => 'nullable|string|max:20',
            'team_name' => 'required|string|max:150',
            'team_label' => 'nullable|string|max:50',
            'members_list' => 'nullable|array',
            'members_raw' => 'nullable|string',
        ]);

        $members = [];
        if ($request->filled('members_raw')) {
            $lines = explode("\n", str_replace("\r", "", $request->input('members_raw')));
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed) {
                    $members[] = ['name' => $trimmed];
                }
            }
        } elseif ($request->has('members_list')) {
            $members = $request->input('members_list');
        }

        $team->update([
            'order_number' => $validated['order_number'] ?? null,
            'team_name' => $validated['team_name'],
            'team_label' => $validated['team_label'] ?? null,
            'members_list' => $members,
        ]);

        return redirect()->back()->with('success', "Data peserta/regu {$team->team_name} berhasil diperbarui!");
    }

    public function bulkUpdateOrders(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'nullable|string|max:20',
        ]);

        $count = 0;
        foreach ($validated['orders'] as $teamId => $orderNum) {
            $team = CompetitionParticipantTeam::find($teamId);
            if ($team) {
                $team->update(['order_number' => trim($orderNum) ?: null]);
                $count++;
            }
        }

        return redirect()->back()->with('success', "Nomor urut tampil untuk {$count} regu berhasil diperbarui!");
    }

    public function autoAssignOrders(Request $request)
    {
        $categoryId = $request->input('category_id');
        $level = $request->input('level');

        $query = CompetitionParticipantTeam::where('is_active', true)
            ->where(function($q) {
                $q->whereHas('registration', function($rq) {
                    $rq->where('status', 'verified');
                })->orWhereNull('competition_registration_id');
            });

        if ($categoryId) {
            $query->where('competition_category_id', $categoryId);
        } elseif ($level) {
            $query->whereHas('category', function($q) use ($level) {
                $q->where('level', $level);
            });
        }

        $teamsByCategory = $query->with('category')->get()->groupBy('competition_category_id');

        $totalAssigned = 0;
        foreach ($teamsByCategory as $catId => $teamsList) {
            $num = 1;
            foreach ($teamsList as $team) {
                $formattedNum = str_pad($num, 2, '0', STR_PAD_LEFT);
                $team->update(['order_number' => $formattedNum]);
                $num++;
                $totalAssigned++;
            }
        }

        return redirect()->back()->with('success', "Nomor urut berhasil di-generate secara otomatis untuk {$totalAssigned} regu peserta!");
    }

    public function printSheet(Request $request)
    {
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::first();
        }

        $level = $request->query('level');
        $categoryId = $request->query('category_id');
        $gender = $request->query('gender');

        $selectedCategory = $categoryId ? CompetitionCategory::find($categoryId) : null;

        $query = CompetitionParticipantTeam::where('is_active', true)
            ->where(function($q) use ($event) {
                $q->whereHas('registration', function($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                });
                if ($event) {
                    $q->orWhere(function($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                           ->whereHas('category', fn($cq) => $cq->where('competition_event_id', $event->id));
                    });
                } else {
                    $q->orWhereNull('competition_registration_id');
                }
            })
            ->with(['registration', 'category']);

        if ($categoryId) {
            $query->where('competition_category_id', $categoryId);
        } elseif ($level) {
            $query->whereHas('category', function($q) use ($level) {
                $q->where('level', $level);
            });
        }

        if ($gender) {
            $query->whereHas('category', function($q) use ($gender) {
                $q->where('gender_category', $gender);
            });
        }

        $teams = $query->get()->sortBy(function($team) {
            $catPos = $team->category ? str_pad($team->category->order_position, 3, '0', STR_PAD_LEFT) : '999';
            $genderOrder = $team->category && $team->category->gender_category === 'Putra' ? '1' : ($team->category && $team->category->gender_category === 'Putri' ? '2' : '3');
            $orderNum = $team->order_number ? str_pad($team->order_number, 4, '0', STR_PAD_LEFT) : '9999';
            return "{$catPos}-{$genderOrder}-{$orderNum}-{$team->team_name}";
        });

        return view('admin.competition.participants.print', compact(
            'event',
            'level',
            'selectedCategory',
            'gender',
            'teams'
        ));
    }
}

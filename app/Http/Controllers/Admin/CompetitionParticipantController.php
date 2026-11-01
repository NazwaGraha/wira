<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Services\CompetitionSpreadsheetService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            $categoriesQuery->where(function ($q) use ($event) {
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
            ->where(function ($q) use ($event) {
                $q->whereHas('registration', function ($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                });
                if ($event) {
                    $q->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
                    });
                } else {
                    $q->orWhereNull('competition_registration_id');
                }
            })
            ->with(['registration', 'category']);

        // Filter by Level
        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $teamsQuery->whereHas('category', function ($q) use ($level) {
                $q->where('level', $level);
            });
        }

        // Filter by specific Category ID
        if ($categoryId) {
            $teamsQuery->where('competition_category_id', $categoryId);
        }

        // Filter by Gender Category (Putra / Putri / Umum)
        if ($gender && in_array($gender, ['Putra', 'Putri', 'Umum', 'Campuran'])) {
            $teamsQuery->whereHas('category', function ($q) use ($gender) {
                $q->where('gender_category', $gender);
            });
        }

        // Search by keyword
        if ($search) {
            $teamsQuery->where(function ($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('team_label', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%")
                            ->orWhere('registration_code', 'like', "%{$search}%")
                            ->orWhere('advisor_name', 'like', "%{$search}%");
                    });
            });
        }

        // Order by category order, gender, order_number, team_name
        $teams = $teamsQuery->get()->sortBy(function ($team) {
            $catPos = $team->category ? str_pad($team->category->order_position, 3, '0', STR_PAD_LEFT) : '999';
            $genderOrder = $team->category && $team->category->gender_category === 'Putra' ? '1' : ($team->category && $team->category->gender_category === 'Putri' ? '2' : '3');
            $orderNum = $team->order_number ?: 'ZZZZ';

            return "{$catPos}-{$genderOrder}-{$orderNum}-{$team->team_name}";
        });

        // Summary counts
        $allVerifiedTeams = CompetitionParticipantTeam::where('is_active', true)
            ->where(function ($q) use ($event) {
                $q->whereHas('registration', function ($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                });
                if ($event) {
                    $q->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
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
            'putra_teams' => $allVerifiedTeams->filter(fn ($t) => $t->category?->gender_category === 'Putra')->count(),
            'putri_teams' => $allVerifiedTeams->filter(fn ($t) => $t->category?->gender_category === 'Putri')->count(),
            'umum_teams' => $allVerifiedTeams->filter(fn ($t) => in_array($t->category?->gender_category, ['Umum', 'Campuran']))->count(),
            'mula_teams' => $allVerifiedTeams->filter(fn ($t) => $t->category?->level === 'Mula')->count(),
            'madya_teams' => $allVerifiedTeams->filter(fn ($t) => $t->category?->level === 'Madya')->count(),
            'wira_teams' => $allVerifiedTeams->filter(fn ($t) => $t->category?->level === 'Wira')->count(),
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

        $orderNumber = $validated['order_number'] ? trim($validated['order_number']) : null;
        if ($orderNumber) {
            // Normalisasi otomatis format A-001 jika polanya huruf dan angka
            if (preg_match('/^([a-z])[-]?(\d+)$/i', $orderNumber, $m)) {
                $orderNumber = strtoupper($m[1]) . '-' . sprintf('%03d', intval($m[2]));
            }

            // Validasi keunikan no. urut peserta di cabang lomba yang sama
            $duplicate = CompetitionParticipantTeam::where('competition_category_id', $team->competition_category_id)
                ->where('id', '!=', $team->id)
                ->where('order_number', $orderNumber)
                ->first();

            if ($duplicate) {
                $usedBy = $duplicate->registration?->school_name ?: $duplicate->team_name;
                return redirect()->back()
                    ->withInput()
                    ->with('error', "No. Urut Peserta '{$orderNumber}' sudah digunakan oleh regu {$duplicate->team_name} ({$usedBy}) pada cabang lomba ini!");
            }
        }

        $members = [];
        if ($request->filled('members_raw')) {
            $lines = explode("\n", str_replace("\r", '', $request->input('members_raw')));
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
            'order_number' => $orderNumber,
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

        $teamsToUpdate = CompetitionParticipantTeam::whereIn('id', array_keys($validated['orders']))->get()->keyBy('id');
        $categorizedOrders = [];

        // Validasi anti-duplikasi nomor urut per cabang lomba
        foreach ($validated['orders'] as $teamId => $rawOrder) {
            $orderNum = trim((string) $rawOrder);
            if ($orderNum === '') {
                continue;
            }

            if (preg_match('/^([a-z])[-]?(\d+)$/i', $orderNum, $m)) {
                $orderNum = strtoupper($m[1]) . '-' . sprintf('%03d', intval($m[2]));
            }

            $team = $teamsToUpdate->get($teamId);
            if (! $team) {
                continue;
            }

            $catId = $team->competition_category_id;
            if (isset($categorizedOrders[$catId][$orderNum])) {
                return redirect()->back()->with('error', "Terdapat nomor urut kembar '{$orderNum}' pada cabang lomba yang sama. Setiap regu harus memiliki nomor urut yang berbeda!");
            }

            // Cek juga terhadap database untuk tim yang tidak ikut di-submit saat ini
            $dbDuplicate = CompetitionParticipantTeam::where('competition_category_id', $catId)
                ->whereNotIn('id', array_keys($validated['orders']))
                ->where('order_number', $orderNum)
                ->first();

            if ($dbDuplicate) {
                $usedBy = $dbDuplicate->registration?->school_name ?: $dbDuplicate->team_name;
                return redirect()->back()->with('error', "No. Urut Peserta '{$orderNum}' sudah digunakan oleh regu {$dbDuplicate->team_name} ({$usedBy}) pada cabang lomba ini!");
            }

            $categorizedOrders[$catId][$orderNum] = $teamId;
        }

        $count = 0;
        foreach ($validated['orders'] as $teamId => $rawOrder) {
            $team = $teamsToUpdate->get($teamId);
            if ($team) {
                $orderNum = trim((string) $rawOrder);
                if ($orderNum !== '') {
                    if (preg_match('/^([a-z])[-]?(\d+)$/i', $orderNum, $m)) {
                        $orderNum = strtoupper($m[1]) . '-' . sprintf('%03d', intval($m[2]));
                    }
                    $team->update(['order_number' => $orderNum]);
                } else {
                    $team->update(['order_number' => null]);
                }
                $count++;
            }
        }

        return redirect()->back()->with('success', "No. Urut Peserta untuk {$count} regu berhasil diperbarui!");
    }

    public function autoAssignOrders(Request $request)
    {
        $categoryId = $request->input('category_id');
        $level = $request->input('level');
        $shuffle = (bool) $request->input('shuffle', false);

        $query = CompetitionParticipantTeam::where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('registration', function ($rq) {
                    $rq->where('status', 'verified');
                })->orWhereNull('competition_registration_id');
            });

        if ($categoryId) {
            $query->where('competition_category_id', $categoryId);
        } elseif ($level) {
            $query->whereHas('category', function ($q) use ($level) {
                $q->where('level', $level);
            });
        }

        $teamsByCategory = $query->with(['category', 'registration'])
            ->orderBy('competition_registration_id')
            ->orderBy('id')
            ->get()
            ->groupBy('competition_category_id');

        $totalAssigned = 0;
        foreach ($teamsByCategory as $catId => $teamsList) {
            // Kelompokkan regu berdasarkan pendaftaran sekolah
            $teamsByRegistration = $teamsList->groupBy(function ($t) {
                return $t->competition_registration_id ? 'reg_'.$t->competition_registration_id : 'ots_'.$t->id;
            });

            // Bagi menjadi slot regu: Slot 0 (Regu A), Slot 1 (Regu B), Slot 2 (Regu C)
            $slots = [];
            foreach ($teamsByRegistration as $regTeams) {
                foreach ($regTeams->values() as $idx => $team) {
                    $slotIdx = $idx;
                    if ($team->team_label && preg_match('/\b(?:regu\s+)?([B-Z])\b/i', $team->team_label, $m)) {
                        $slotIdx = ord(strtoupper($m[1])) - 65;
                    }
                    $slots[$slotIdx][] = $team;
                }
            }

            // Tetapkan nomor urut per slot: Regu A (A-001, A-002...), Regu B (B-001, B-002...), dst
            ksort($slots);
            foreach ($slots as $slotIdx => $teamsInSlot) {
                $prefix = chr(65 + $slotIdx);

                // Jika mode undian / acak aktif
                if ($shuffle) {
                    shuffle($teamsInSlot);
                }

                $num = 1;
                foreach ($teamsInSlot as $team) {
                    $formattedNum = sprintf('%s-%03d', $prefix, $num);
                    $team->update(['order_number' => $formattedNum]);
                    $num++;
                    $totalAssigned++;
                }
            }
        }

        $modeText = $shuffle ? 'Undian TM (Acak)' : 'Berurutan';
        return redirect()->back()->with('success', "No. Urut Peserta skema {$modeText} (Regu A: A-001.. Regu B: B-001..) berhasil di-generate secara otomatis untuk {$totalAssigned} regu peserta!");
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
            ->where(function ($q) use ($event) {
                $q->whereHas('registration', function ($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                });
                if ($event) {
                    $q->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
                    });
                } else {
                    $q->orWhereNull('competition_registration_id');
                }
            })
            ->with(['registration', 'category']);

        if ($categoryId) {
            $query->where('competition_category_id', $categoryId);
        } elseif ($level) {
            $query->whereHas('category', function ($q) use ($level) {
                $q->where('level', $level);
            });
        }

        if ($gender) {
            $query->whereHas('category', function ($q) use ($gender) {
                $q->where('gender_category', $gender);
            });
        }

        $teams = $query->get()->sortBy(function ($team) {
            $catPos = $team->category ? str_pad($team->category->order_position, 3, '0', STR_PAD_LEFT) : '999';
            $genderOrder = $team->category && $team->category->gender_category === 'Putra' ? '1' : ($team->category && $team->category->gender_category === 'Putri' ? '2' : '3');
            $orderNum = $team->order_number ?: 'ZZZZ';

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

    /**
     * Export data peserta & regu lomba terverifikasi ke MS Excel (.xlsx)
     */
    public function exportExcel(Request $request, CompetitionSpreadsheetService $spreadsheetService): BinaryFileResponse
    {
        // Resolve Event
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: CompetitionEvent::orderByDesc('id')->first();
        }

        $level = $request->query('level');
        $categoryId = $request->query('category_id');
        $gender = $request->query('gender');
        $search = trim($request->query('q', ''));

        $query = CompetitionParticipantTeam::where('is_active', true)
            ->where(function ($q) use ($event) {
                $q->whereHas('registration', function ($rq) use ($event) {
                    $rq->where('status', 'verified');
                    if ($event) {
                        $rq->where('competition_event_id', $event->id);
                    }
                });
                if ($event) {
                    $q->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
                    });
                } else {
                    $q->orWhereNull('competition_registration_id');
                }
            })
            ->with(['registration', 'category']);

        // Filter by Level
        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $query->whereHas('category', function ($q) use ($level) {
                $q->where('level', $level);
            });
        }

        // Filter by Category
        if ($categoryId) {
            $query->where('competition_category_id', $categoryId);
        }

        // Filter by Gender
        if ($gender && in_array($gender, ['Putra', 'Putri', 'Umum', 'Campuran'])) {
            $query->whereHas('category', function ($q) use ($gender) {
                $q->where('gender_category', $gender);
            });
        }

        // Search by keyword
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('team_label', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%")
                            ->orWhere('registration_code', 'like', "%{$search}%")
                            ->orWhere('advisor_name', 'like', "%{$search}%");
                    });
            });
        }

        $teams = $query->get()->sortBy(function ($team) {
            $catPos = $team->category ? str_pad($team->category->order_position, 3, '0', STR_PAD_LEFT) : '999';
            $genderOrder = $team->category && $team->category->gender_category === 'Putra' ? '1' : ($team->category && $team->category->gender_category === 'Putri' ? '2' : '3');
            $orderNum = $team->order_number ?: 'ZZZZ';

            return "{$catPos}-{$genderOrder}-{$orderNum}-{$team->team_name}";
        });

        $filePath = $spreadsheetService->exportParticipantTeamsXlsx($teams, $event);

        $suffixParts = [];
        if ($level) {
            $suffixParts[] = $level;
        }
        if ($gender) {
            $suffixParts[] = $gender;
        }
        $suffix = ! empty($suffixParts) ? '_'.implode('_', $suffixParts) : '';
        $filename = 'Data_Peserta_Lomba'.$suffix.'_'.date('Ymd_His').'.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}

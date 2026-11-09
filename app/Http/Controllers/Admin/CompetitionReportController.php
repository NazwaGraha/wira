<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompetitionReportController extends Controller
{
    /**
     * Halaman Utama Laporan & Rekapitulasi Rinci Peserta Lomba.
     */
    public function participants(Request $request)
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $level = $request->query('level');
        $categoryId = $request->query('category_id');
        $status = $request->query('status', 'all'); // all, verified, pending
        $checkin = $request->query('checkin', 'all'); // all, checked_in, not_checked_in
        $search = trim($request->query('q', ''));

        // Ambil kategori untuk filter dropdown/pills
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

        // Selected category object
        $selectedCategory = $categoryId ? CompetitionCategory::find($categoryId) : null;

        // Query Dasar Tim Peserta
        $teamsQuery = CompetitionParticipantTeam::with(['registration.event', 'category'])
            ->where('is_active', true);

        if ($event) {
            $teamsQuery->where(function ($q) use ($event) {
                $q->whereHas('registration', fn ($rq) => $rq->where('competition_event_id', $event->id))
                    ->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
                    });
            });
        }

        // Filter Tingkat (Level)
        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $teamsQuery->whereHas('category', fn ($q) => $q->where('level', $level));
        }

        // Filter Kategori Mata Lomba
        if ($categoryId) {
            $teamsQuery->where('competition_category_id', $categoryId);
        }

        // Filter Status Verifikasi Registrasi
        if ($status === 'verified') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('status', 'verified'));
        } elseif ($status === 'pending') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('status', 'pending'));
        }

        // Filter Status Check-in QR
        if ($checkin === 'checked_in') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('is_checked_in', true));
        } elseif ($checkin === 'not_checked_in') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('is_checked_in', false));
        }

        // Filter Pencarian Multi-Kriteria
        if ($search !== '') {
            $teamsQuery->where(function ($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('members_list', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%")
                            ->orWhere('registration_code', 'like', "%{$search}%")
                            ->orWhere('advisor_name', 'like', "%{$search}%")
                            ->orWhere('advisor_phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        // Urutkan berdasarkan Kategori, Nomor Urut, dan ID
        $teamsQuery->orderBy('competition_category_id')
            ->orderByRaw('order_number IS NULL, order_number ASC')
            ->orderBy('id');

        // Clone query untuk perhitungan KPI
        $allTeamsCountQuery = CompetitionParticipantTeam::where('is_active', true);
        if ($event) {
            $allTeamsCountQuery->where(function ($q) use ($event) {
                $q->whereHas('registration', fn ($rq) => $rq->where('competition_event_id', $event->id))
                    ->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
                    });
            });
        }

        $allTeams = $allTeamsCountQuery->with(['category', 'registration'])->get();

        // Hitung Metrik KPI
        $kpi = [
            'total_schools' => $allTeams->pluck('registration.school_name')->filter()->unique()->count(),
            'total_schools_verified' => $allTeams->where('registration.status', 'verified')->pluck('registration.school_name')->filter()->unique()->count(),
            'total_teams' => $allTeams->count(),
            'total_students' => $allTeams->sum(function ($team) {
                $m = $team->members_list;
                if (is_array($m) && count($m) > 0) {
                    return count($m);
                }

                return 1;
            }),
            'mula_teams' => $allTeams->where('category.level', 'Mula')->count(),
            'madya_teams' => $allTeams->where('category.level', 'Madya')->count(),
            'wira_teams' => $allTeams->where('category.level', 'Wira')->count(),
            'checked_in_teams' => $allTeams->where('registration.is_checked_in', true)->count(),
        ];

        // Paginate hasil tabel
        $teams = $teamsQuery->paginate(25)->withQueryString();

        return view('admin.competition.reports.participants', compact(
            'allEvents',
            'event',
            'level',
            'categoryId',
            'categories',
            'selectedCategory',
            'status',
            'checkin',
            'search',
            'teams',
            'kpi'
        ));
    }

    /**
     * Unduh Laporan Peserta dalam Format Excel (.csv dengan UTF-8 BOM).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $level = $request->query('level');
        $categoryId = $request->query('category_id');
        $status = $request->query('status', 'all');
        $checkin = $request->query('checkin', 'all');
        $search = trim($request->query('q', ''));

        $teamsQuery = CompetitionParticipantTeam::with(['registration.event', 'category'])
            ->where('is_active', true);

        if ($event) {
            $teamsQuery->where(function ($q) use ($event) {
                $q->whereHas('registration', fn ($rq) => $rq->where('competition_event_id', $event->id))
                    ->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
                    });
            });
        }

        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $teamsQuery->whereHas('category', fn ($q) => $q->where('level', $level));
        }

        if ($categoryId) {
            $teamsQuery->where('competition_category_id', $categoryId);
        }

        if ($status === 'verified') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('status', 'verified'));
        } elseif ($status === 'pending') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('status', 'pending'));
        }

        if ($checkin === 'checked_in') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('is_checked_in', true));
        } elseif ($checkin === 'not_checked_in') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('is_checked_in', false));
        }

        if ($search !== '') {
            $teamsQuery->where(function ($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('members_list', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%")
                            ->orWhere('registration_code', 'like', "%{$search}%")
                            ->orWhere('advisor_name', 'like', "%{$search}%")
                            ->orWhere('advisor_phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $teams = $teamsQuery->orderBy('competition_category_id')
            ->orderByRaw('order_number IS NULL, order_number ASC')
            ->orderBy('id')
            ->get();

        $selectedCategory = $categoryId ? CompetitionCategory::find($categoryId) : null;
        $levelSuffix = $level ? "-{$level}" : '';
        $catSuffix = $selectedCategory ? '-'.Str::slug($selectedCategory->name) : '';
        $filename = 'Laporan-Peserta-Lomba-SBB-IV'.($levelSuffix ?: '-Semua-Tingkat').$catSuffix.'-'.date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($teams, $event, $level, $selectedCategory) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header Informasi Laporan
            fputcsv($handle, ['BUKU INDUK & DAFTAR PESERTA LOMBA SUA BHAKTI BERKARYA IV']);
            fputcsv($handle, ['PMR WIRA UNIT SMAN 1 CIAWI - KABUPATEN BOGOR']);
            fputcsv($handle, ['Sekretariat: Jl. Veteran III Banjarwaru, Ciawi, Bogor, Jawa Barat 16720']);
            fputcsv($handle, ['Event:', $event?->title ?? 'Sua Bhakti Berkarya IV 2026']);
            fputcsv($handle, ['Filter Tingkat:', $level ?: 'Semua Tingkat (Mula, Madya, Wira)']);
            fputcsv($handle, ['Filter Cabang Lomba:', $selectedCategory?->name ?? 'Semua Cabang Lomba']);
            fputcsv($handle, ['Tanggal Cetak:', date('d F Y, H:i').' WIB']);
            fputcsv($handle, ['Total Regu Terindeks:', count($teams).' Regu']);
            fputcsv($handle, []); // Baris Kosong

            // Header Tabel
            fputcsv($handle, [
                'No',
                'No. Urut Lomba',
                'Kode Registrasi',
                'Asal Sekolah / Pangkalan',
                'Tingkat',
                'Cabang Mata Lomba',
                'Kategori Gender',
                'Nama Regu / Tim',
                'Nama Pembina Pendamping',
                'No. HP / WhatsApp Pembina',
                'Jumlah Siswa',
                'Daftar Lengkap Nama Siswa / Anggota Regu',
                'Status Verifikasi',
                'Status Check-in QR Hari-H',
            ]);

            foreach ($teams as $idx => $team) {
                $reg = $team->registration;
                $cat = $team->category;

                $members = is_array($team->members_list) ? $team->members_list : [];
                $memberNames = [];
                foreach ($members as $mIdx => $m) {
                    if (is_array($m)) {
                        $name = $m['name'] ?? ($m['nama'] ?? '-');
                        $role = ! empty($m['role']) ? " ({$m['role']})" : '';
                        $memberNames[] = ($mIdx + 1).". {$name}{$role}";
                    } else {
                        $memberNames[] = ($mIdx + 1).'. '.(string) $m;
                    }
                }
                $membersFormatted = ! empty($memberNames) ? implode("\n", $memberNames) : ($team->team_name ?: '-');

                fputcsv($handle, [
                    $idx + 1,
                    $team->order_number ?: '-',
                    $reg?->registration_code ?: '-',
                    $reg?->school_name ?: '-',
                    $cat?->level ?: ($reg?->level ?: '-'),
                    $cat?->name ?: '-',
                    $cat?->gender_category ?: '-',
                    $team->team_name ?: '-',
                    $reg?->advisor_name ?: '-',
                    $reg?->advisor_phone ?: '-',
                    count($members) ?: 1,
                    $membersFormatted,
                    $reg?->status === 'verified' ? 'Terverifikasi' : 'Pending',
                    $reg?->is_checked_in ? 'Hadir (Sudah Check-in)' : 'Belum Hadir',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Tampilan Pracetak Dokumen Resmi PDF Ber-Kop SMAN 1 Ciawi & PMI.
     */
    public function printPdf(Request $request): Response
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $level = $request->query('level');
        $categoryId = $request->query('category_id');
        $status = $request->query('status', 'all');
        $checkin = $request->query('checkin', 'all');
        $search = trim($request->query('q', ''));

        $teamsQuery = CompetitionParticipantTeam::with(['registration.event', 'category'])
            ->where('is_active', true);

        if ($event) {
            $teamsQuery->where(function ($q) use ($event) {
                $q->whereHas('registration', fn ($rq) => $rq->where('competition_event_id', $event->id))
                    ->orWhere(function ($sq) use ($event) {
                        $sq->whereNull('competition_registration_id')
                            ->whereHas('category', fn ($cq) => $cq->where('competition_event_id', $event->id));
                    });
            });
        }

        if ($level && in_array($level, ['Mula', 'Madya', 'Wira'])) {
            $teamsQuery->whereHas('category', fn ($q) => $q->where('level', $level));
        }

        if ($categoryId) {
            $teamsQuery->where('competition_category_id', $categoryId);
        }

        if ($status === 'verified') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('status', 'verified'));
        } elseif ($status === 'pending') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('status', 'pending'));
        }

        if ($checkin === 'checked_in') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('is_checked_in', true));
        } elseif ($checkin === 'not_checked_in') {
            $teamsQuery->whereHas('registration', fn ($q) => $q->where('is_checked_in', false));
        }

        if ($search !== '') {
            $teamsQuery->where(function ($q) use ($search) {
                $q->where('team_name', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('members_list', 'like', "%{$search}%")
                    ->orWhereHas('registration', function ($rq) use ($search) {
                        $rq->where('school_name', 'like', "%{$search}%")
                            ->orWhere('registration_code', 'like', "%{$search}%")
                            ->orWhere('advisor_name', 'like', "%{$search}%")
                            ->orWhere('advisor_phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $teams = $teamsQuery->orderBy('competition_category_id')
            ->orderByRaw('order_number IS NULL, order_number ASC')
            ->orderBy('id')
            ->get();

        $selectedCategory = $categoryId ? CompetitionCategory::find($categoryId) : null;

        return response()->view('admin.competition.reports.participants-pdf', compact(
            'event',
            'level',
            'categoryId',
            'selectedCategory',
            'status',
            'checkin',
            'teams'
        ));
    }
}

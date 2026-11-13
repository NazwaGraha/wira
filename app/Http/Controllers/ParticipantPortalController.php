<?php

namespace App\Http\Controllers;

use App\Models\CompetitionCategory;
use App\Models\CompetitionRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantPortalController extends Controller
{
    /**
     * Tampilan Utama Portal Peserta (Login atau Dashboard)
     */
    public function index(Request $request): View
    {
        $regId = Session::get('participant_registration_id');

        if (! $regId) {
            return view('participant.portal', [
                'registration' => null,
                'teamsData' => collect(),
            ]);
        }

        $registration = CompetitionRegistration::with([
            'event',
            'teams' => function ($q) {
                $q->where('is_active', true)->orderBy('order_number')->orderBy('id');
            },
            'teams.category',
            'teams.photos',
            'teams.scores' => function ($q) {
                $q->latest('id');
            },
            'teams.scores.photos',
        ])->find($regId);

        if (! $registration || $registration->status !== 'verified') {
            Session::forget('participant_registration_id');

            return view('participant.portal', [
                'registration' => null,
                'teamsData' => collect(),
            ]);
        }

        $teamsData = $this->formatTeamsData($registration);

        return view('participant.portal', [
            'registration' => $registration,
            'teamsData' => $teamsData,
        ]);
    }

    /**
     * Proses Login Peserta (Email + Nomor Bukti Pembayaran / Registrasi)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
            'registration_code' => 'required|string',
        ]);

        $email = trim(strtolower($request->email));
        $code = trim(strtoupper($request->registration_code));

        // Ekstraksi kode jika peserta paste URL lengkap (misal: .../kwitansi/SBB-W54342H)
        if (preg_match('/(?:kwitansi|kartu-peserta)\/([A-Za-z0-9\-_]+)/i', $code, $matches)) {
            $code = strtoupper($matches[1]);
        } elseif (preg_match('/(SBB-[A-Za-z0-9]+)/i', $code, $matches)) {
            $code = strtoupper($matches[1]);
        }

        $registration = CompetitionRegistration::where('registration_code', $code)
            ->where(function ($q) use ($email) {
                $q->whereRaw('LOWER(TRIM(advisor_email)) = ?', [$email]);
            })
            ->first();

        if (! $registration) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi Alamat Email dan Nomor Registrasi / Bukti Pembayaran tidak ditemukan dalam sistem.',
            ], 422);
        }

        if ($registration->status !== 'verified') {
            $statusLabel = [
                'pending' => 'Menunggu Pembayaran / Verifikasi',
                'rejected' => 'Ditolak',
            ][$registration->status] ?? $registration->status;

            return response()->json([
                'success' => false,
                'message' => "Pendaftaran kontingen {$registration->school_name} belum diverifikasi lunas oleh panitia. (Status: {$statusLabel})",
            ], 422);
        }

        // Set session
        Session::put('participant_registration_id', $registration->id);

        return response()->json([
            'success' => true,
            'school_name' => $registration->school_name,
            'registration_code' => $registration->registration_code,
            'level' => strtoupper($registration->level),
            'message' => "Selamat Datang, {$registration->school_name}",
            'redirect_url' => route('participant.portal'),
        ]);
    }

    /**
     * Logout Peserta
     */
    public function logout(Request $request): RedirectResponse
    {
        Session::forget('participant_registration_id');

        return redirect()->route('participant.portal')->with('success', 'Anda telah keluar dari Portal Peserta.');
    }

    /**
     * Endpoint Polling Otomatis (Live Update per 20 Detik)
     */
    public function liveData(Request $request): JsonResponse
    {
        $regId = Session::get('participant_registration_id');
        if (! $regId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi berakhir, silakan login kembali.',
            ], 401);
        }

        $registration = CompetitionRegistration::with([
            'teams' => function ($q) {
                $q->where('is_active', true)->orderBy('order_number')->orderBy('id');
            },
            'teams.category',
            'teams.photos',
            'teams.scores' => function ($q) {
                $q->latest('id');
            },
            'teams.scores.photos',
        ])->find($regId);

        if (! $registration || $registration->status !== 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran tidak valid atau belum diverifikasi.',
            ], 403);
        }

        $teamsData = $this->formatTeamsData($registration);

        return response()->json([
            'success' => true,
            'school_name' => $registration->school_name,
            'registration_code' => $registration->registration_code,
            'server_time' => now()->format('H:i:s'),
            'teams' => $teamsData,
        ]);
    }

    /**
     * Unduh Laporan Nilai Resmi (PDF)
     */
    public function exportPdf(Request $request)
    {
        $regId = Session::get('participant_registration_id');
        if (! $regId) {
            return redirect()->route('participant.portal');
        }

        $registration = CompetitionRegistration::with([
            'event',
            'teams' => function ($q) {
                $q->where('is_active', true)->orderBy('order_number')->orderBy('id');
            },
            'teams.category',
            'teams.photos',
            'teams.scores' => function ($q) {
                $q->latest('id');
            },
            'teams.scores.photos',
        ])->findOrFail($regId);

        $categoryId = $request->query('category_id');
        $filteredCategory = null;

        if ($categoryId) {
            $filteredCategory = CompetitionCategory::find($categoryId);
        }

        $teamsData = $this->formatTeamsData($registration, $categoryId);

        $fileName = 'Rekap_Nilai_'.$registration->registration_code.'_'.($filteredCategory ? str_replace(' ', '_', $filteredCategory->name) : 'Semua_Lomba').'_'.date('Ymd_His').'.pdf';

        $pdf = Pdf::loadView('participant.report-pdf', [
            'registration' => $registration,
            'teamsData' => $teamsData,
            'filteredCategory' => $filteredCategory,
            'printedAt' => now()->format('d F Y, H:i').' WIB',
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($fileName);
    }

    /**
     * Unduh Laporan Nilai Resmi (Excel / CSV UTF-8)
     */
    public function exportExcel(Request $request): StreamedResponse|RedirectResponse
    {
        $regId = Session::get('participant_registration_id');
        if (! $regId) {
            return redirect()->route('participant.portal');
        }

        $registration = CompetitionRegistration::with([
            'event',
            'teams' => function ($q) {
                $q->where('is_active', true)->orderBy('order_number')->orderBy('id');
            },
            'teams.category',
            'teams.photos',
            'teams.scores' => function ($q) {
                $q->latest('id');
            },
            'teams.scores.photos',
        ])->findOrFail($regId);

        $categoryId = $request->query('category_id');
        $filteredCategory = $categoryId ? CompetitionCategory::find($categoryId) : null;
        $teamsData = $this->formatTeamsData($registration, $categoryId);

        $fileName = 'Rekap_Nilai_'.$registration->registration_code.'_'.($filteredCategory ? str_replace(' ', '_', $filteredCategory->name) : 'Semua_Lomba').'_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($registration, $teamsData) {
            $file = fopen('php://output', 'w');
            // Write BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REKAPITULASI RESMI HASIL NILAI LOMBA PMR - SUA BHAKTI BERKARYA IV']);
            fputcsv($file, ['PMR WIRA SMAN 1 CIAWI BOGOR']);
            fputcsv($file, []);
            fputcsv($file, ['Asal Sekolah / Kontingen', $registration->school_name]);
            fputcsv($file, ['No. Registrasi / Bukti Pembayaran', $registration->registration_code]);
            fputcsv($file, ['Tingkatan', strtoupper($registration->level)]);
            fputcsv($file, ['Pembina Pendamping', $registration->advisor_name ?? '-']);
            fputcsv($file, ['Waktu Unduh', now()->format('d/m/Y H:i:s').' WIB']);
            fputcsv($file, []);

            // Table Header
            fputcsv($file, [
                'No',
                'Cabang Lomba',
                'No. Undian / Urut',
                'Nama Regu',
                'Anggota Regu',
                'Waktu Tampil',
                'Nilai Akhir',
                'Status Penilaian',
            ]);

            foreach ($teamsData as $idx => $t) {
                $membersClean = implode(', ', $t['member_names']);

                fputcsv($file, [
                    $idx + 1,
                    $t['category_name'],
                    $t['order_number'],
                    $t['team_name'],
                    $membersClean ?: '-',
                    $t['time_recorded'] ?: '-',
                    $t['has_score'] ? number_format($t['final_score'], 2, ',', '.') : 'Belum Dinilai',
                    $t['status_label'],
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['* Catatan: Data rekap ini hanya memuat nilai kontingen yang bersangkutan. Peringkat dan Juara Resmi diumumkan oleh Panitia pada penutupan acara.']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Format Data Regu & Nilai (Aman & Tidak Membocorkan Data Sekolah Lain)
     */
    protected function formatTeamsData(CompetitionRegistration $registration, ?int $categoryId = null)
    {
        return $registration->teams
            ->when($categoryId, function ($collection) use ($categoryId) {
                return $collection->where('competition_category_id', $categoryId);
            })
            ->map(function ($team) {
                $latestScore = $team->scores->first();

                // Format daftar anggota regu
                $members = is_array($team->members_list) ? $team->members_list : [];
                $memberNames = [];
                $membersFormatted = [];

                foreach ($members as $mIdx => $m) {
                    if (is_array($m)) {
                        $name = $m['name'] ?? ($m['nama'] ?? 'Peserta '.($mIdx + 1));
                        $role = ! empty($m['role']) ? $m['role'] : (! empty($m['jabatan']) ? $m['jabatan'] : '');
                        $nisn = ! empty($m['nisn']) ? $m['nisn'] : (! empty($m['nis']) ? $m['nis'] : '');
                        $class = ! empty($m['class_grade']) ? $m['class_grade'] : (! empty($m['kelas']) ? $m['kelas'] : '');

                        $memberNames[] = $name.($role ? " ({$role})" : '');
                        $membersFormatted[] = [
                            'no' => $mIdx + 1,
                            'name' => $name,
                            'role' => $role,
                            'nisn' => $nisn,
                            'class' => $class,
                        ];
                    } else {
                        $name = (string) $m;
                        $memberNames[] = $name;
                        $membersFormatted[] = [
                            'no' => $mIdx + 1,
                            'name' => $name,
                            'role' => '',
                            'nisn' => '',
                            'class' => '',
                        ];
                    }
                }

                $hasScore = ($latestScore !== null && $latestScore->final_score !== null);
                $finalScore = $hasScore ? (float) $latestScore->final_score : null;
                $isDisqualified = $hasScore && (bool) $latestScore->is_disqualified;

                $statusLabel = 'Menunggu Juri';
                if ($isDisqualified) {
                    $statusLabel = 'Diskualifikasi';
                } elseif ($hasScore) {
                    $statusLabel = 'Selesai Dinilai';
                }

                // Format foto bukti lembar nilai yang difoto juri/panitia
                $teamPhotos = $team->photos ?? collect();
                $scorePhotos = $team->scores->flatMap->photos ?? collect();
                $allPhotos = $teamPhotos->merge($scorePhotos)->unique('id')->values()->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'url' => $p->photo_url,
                        'caption' => $p->caption ?: 'Lembar Nilai Juri',
                        'created_at' => $p->created_at ? $p->created_at->format('d/m/Y H:i') : null,
                    ];
                })->toArray();

                return [
                    'team_id' => $team->id,
                    'category_id' => $team->competition_category_id,
                    'category_name' => $team->category?->name ?? 'Mata Lomba',
                    'category_code' => $team->category?->code ?? '-',
                    'order_number' => $team->formatted_order_number ?? ($team->order_number ?: '-'),
                    'team_name' => $team->team_name ?: ($team->team_label ?: 'Regu Utama'),
                    'members_count' => count($members) ?: 1,
                    'member_names' => $memberNames,
                    'members' => $membersFormatted,
                    'has_score' => $hasScore,
                    'final_score' => $finalScore,
                    'formatted_score' => $hasScore ? number_format($finalScore, 2, '.', '') : null,
                    'time_recorded' => $latestScore?->time_recorded,
                    'is_disqualified' => $isDisqualified,
                    'status_label' => $statusLabel,
                    'scored_at' => $latestScore?->updated_at ? $latestScore->updated_at->format('H:i') : null,
                    'photos' => $allPhotos,
                ];
            });
    }
}

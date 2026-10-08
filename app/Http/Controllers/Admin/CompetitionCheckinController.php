<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetitionCheckinController extends Controller
{
    /**
     * Tampilan utama meja daftar ulang (Check-In) peserta lomba hari-H
     */
    public function index(Request $request)
    {
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $tab = $request->query('tab', 'pending'); // 'pending' = belum hadir, 'checked_in' = sudah hadir, 'all' = semua
        $level = $request->query('level');
        $search = $request->query('q');

        // Base query: HANYA yang sudah terverifikasi / lunas
        $baseQuery = CompetitionRegistration::where('status', 'verified');
        if ($event) {
            $baseQuery->where('competition_event_id', $event->id);
        }

        // Hitung statistik
        $totalVerified = (clone $baseQuery)->count();
        $totalCheckedIn = (clone $baseQuery)->where('is_checked_in', true)->count();
        $totalPending = $totalVerified - $totalCheckedIn;
        $attendanceRate = $totalVerified > 0 ? round(($totalCheckedIn / $totalVerified) * 100, 1) : 0;

        // Query daftar peserta
        $query = (clone $baseQuery)->with(['event', 'teams.category'])->latest('checked_in_at')->latest('id');

        if ($tab === 'pending') {
            $query->where(function($q) {
                $q->whereNull('is_checked_in')->orWhere('is_checked_in', false);
            });
        } elseif ($tab === 'checked_in') {
            $query->where('is_checked_in', true);
        }

        if ($level) {
            $query->where('level', $level);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('school_name', 'like', "%{$search}%")
                  ->orWhere('registration_code', 'like', "%{$search}%")
                  ->orWhere('advisor_name', 'like', "%{$search}%")
                  ->orWhere('advisor_phone', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(20)->withQueryString();

        return view('admin.competition.checkin.index', compact(
            'event',
            'allEvents',
            'registrations',
            'tab',
            'level',
            'search',
            'totalVerified',
            'totalCheckedIn',
            'totalPending',
            'attendanceRate'
        ));
    }

    /**
     * AJAX Lookup data pendaftar via Scan QR Code atau Input Manual Nomor Registrasi
     */
    public function lookup(Request $request)
    {
        $rawCode = trim($request->query('code', ''));

        if (empty($rawCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi pendaftaran tidak boleh kosong.'
            ], 400);
        }

        // Ekstraksi kode jika hasil scan QR Code adalah full URL
        // Contoh URL: https://domain/lomba/kwitansi/SBB-W54342H atau ?code=SBB-W54342H
        $cleanCode = $rawCode;
        if (preg_match('/(?:kwitansi|kartu-peserta)\/([A-Za-z0-9\-_]+)/i', $rawCode, $matches)) {
            $cleanCode = $matches[1];
        } elseif (preg_match('/code=([A-Za-z0-9\-_]+)/i', $rawCode, $matches)) {
            $cleanCode = $matches[1];
        } elseif (preg_match('/(SBB-[A-Za-z0-9]+)/i', $rawCode, $matches)) {
            $cleanCode = $matches[1];
        }

        $cleanCode = strtoupper($cleanCode);

        $registration = CompetitionRegistration::with(['event', 'teams.category'])
            ->where('registration_code', $cleanCode)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => "Nomor Registrasi '{$cleanCode}' tidak ditemukan dalam database."
            ], 404);
        }

        if ($registration->status !== 'verified') {
            $statusLabel = [
                'pending' => 'Menunggu Pembayaran / Verifikasi',
                'rejected' => 'Ditolak',
            ][$registration->status] ?? $registration->status;

            return response()->json([
                'success' => false,
                'registration' => $registration,
                'message' => "Pendaftaran sekolah {$registration->school_name} belum diverifikasi lunas! Status: {$statusLabel}."
            ], 422);
        }

        return response()->json([
            'success' => true,
            'registration' => [
                'id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'school_name' => $registration->school_name,
                'level' => strtoupper($registration->level),
                'advisor_name' => $registration->advisor_name,
                'advisor_phone' => $registration->advisor_phone,
                'advisor_email' => $registration->advisor_email,
                'total_fee' => number_format($registration->total_fee, 0, ',', '.'),
                'is_checked_in' => (bool)$registration->is_checked_in,
                'checked_in_at' => $registration->checked_in_at ? $registration->checked_in_at->format('d M Y, H:i') : null,
                'checked_in_by' => $registration->checked_in_by,
                'checkin_notes' => $registration->checkin_notes,
                'teams_count' => $registration->teams->count(),
                'teams' => $registration->teams->map(function($team) {
                    return [
                        'id' => $team->id,
                        'name' => $team->team_name,
                        'category' => $team->category ? $team->category->name : '-',
                        'order_number' => $team->order_number ?? '-',
                        'members_count' => is_array($team->members) ? count($team->members) : 0,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Proses Check-In (Peserta Resmi Hadir di Lokasi Lomba)
     */
    public function process(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:competition_registrations,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $registration = CompetitionRegistration::findOrFail($request->registration_id);

        if ($registration->status !== 'verified') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pendaftaran yang berstatus terverifikasi/lunas yang dapat melakukan daftar ulang.'
                ], 422);
            }
            return back()->with('error', 'Pendaftaran belum berstatus lunas.');
        }

        $officerName = Auth::check() ? Auth::user()->name : 'Panitia Registrasi';

        $registration->update([
            'is_checked_in' => true,
            'checked_in_at' => now(),
            'checked_in_by' => $officerName,
            'checkin_notes' => $request->notes,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil! Kontingen {$registration->school_name} ({$registration->registration_code}) telah berhasil daftar ulang.",
                'registration' => $registration
            ]);
        }

        return back()->with('success', "Kontingen {$registration->school_name} ({$registration->registration_code}) berhasil daftar ulang.");
    }

    /**
     * Batalkan Check-In jika terjadi kekeliruan
     */
    public function cancel(Request $request, $id)
    {
        $registration = CompetitionRegistration::findOrFail($id);

        $registration->update([
            'is_checked_in' => false,
            'checked_in_at' => null,
            'checked_in_by' => null,
            'checkin_notes' => null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status daftar ulang kontingen {$registration->school_name} telah dibatalkan."
            ]);
        }

        return back()->with('info', "Status daftar ulang kontingen {$registration->school_name} telah dibatalkan.");
    }
}

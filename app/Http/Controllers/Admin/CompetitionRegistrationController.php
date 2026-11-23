<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationVerifiedMail;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CompetitionRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $level = $request->query('level');
        $search = $request->query('q');

        // Resolve Event
        $allEvents = CompetitionEvent::orderByDesc('id')->get();
        $selectedEventId = $request->query('event_id');
        if ($selectedEventId) {
            $event = CompetitionEvent::find($selectedEventId) ?: CompetitionEvent::where('is_active', true)->first();
        } else {
            $event = CompetitionEvent::where('is_active', true)->first() ?: $allEvents->first();
        }

        $query = CompetitionRegistration::with(['event', 'teams.category'])->latest();

        if ($event) {
            $query->where('competition_event_id', $event->id);
        }

        if ($status === 'followup') {
            $query->where(function ($q) {
                if (Schema::hasColumn('competition_registrations', 'has_pending_followup')) {
                    $q->where('has_pending_followup', true);
                }
                $q->orWhere('checkin_notes', 'like', '%"status":"pending"%');
            });
        } elseif ($status) {
            $query->where('status', $status);
        }

        if ($level) {
            $query->where('level', $level);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('school_name', 'like', "%{$search}%")
                    ->orWhere('registration_code', 'like', "%{$search}%")
                    ->orWhere('advisor_name', 'like', "%{$search}%")
                    ->orWhere('advisor_phone', 'like', "%{$search}%");
            });
        }

        $registrations = $query->paginate(15)->withQueryString();

        $countsQuery = CompetitionRegistration::query();
        if ($event) {
            $countsQuery->where('competition_event_id', $event->id);
        }

        $counts = [
            'all' => (clone $countsQuery)->count(),
            'pending' => (clone $countsQuery)->where('status', 'pending')->count(),
            'verified' => (clone $countsQuery)->where('status', 'verified')->count(),
            'rejected' => (clone $countsQuery)->where('status', 'rejected')->count(),
            'followup' => (clone $countsQuery)->where(function ($q) {
                if (Schema::hasColumn('competition_registrations', 'has_pending_followup')) {
                    $q->where('has_pending_followup', true);
                }
                $q->orWhere('checkin_notes', 'like', '%"status":"pending"%');
            })->count(),
        ];

        // Statistik perbandingan tahunan antar event / edisi
        $eventStats = CompetitionEvent::withCount([
            'registrations as total_registrations',
            'registrations as verified_registrations' => function ($q) {
                $q->where('status', 'verified');
            },
        ])->orderByDesc('id')->get()->map(function ($ev) {
            $totalTeams = DB::table('competition_participant_teams')
                ->join('competition_registrations', 'competition_participant_teams.competition_registration_id', '=', 'competition_registrations.id')
                ->where('competition_registrations.competition_event_id', $ev->id)
                ->count();

            $totalSchools = CompetitionRegistration::where('competition_event_id', $ev->id)
                ->distinct('school_name')
                ->count('school_name');

            $mulaCount = CompetitionRegistration::where('competition_event_id', $ev->id)->where('level', 'Mula')->count();
            $madyaCount = CompetitionRegistration::where('competition_event_id', $ev->id)->where('level', 'Madya')->count();
            $wiraCount = CompetitionRegistration::where('competition_event_id', $ev->id)->where('level', 'Wira')->count();

            return [
                'event' => $ev,
                'total_registrations' => $ev->total_registrations,
                'verified_registrations' => $ev->verified_registrations,
                'total_schools' => $totalSchools,
                'total_teams' => $totalTeams,
                'mula' => $mulaCount,
                'madya' => $madyaCount,
                'wira' => $wiraCount,
            ];
        });

        // Rekapitulasi daftar sekolah untuk event yang dipilih
        $schoolsSummary = CompetitionRegistration::withCount('teams')
            ->when($event, fn ($q) => $q->where('competition_event_id', $event->id))
            ->orderBy('school_name')
            ->get();

        return view('admin.competition.registrations.index', compact(
            'registrations',
            'counts',
            'status',
            'level',
            'search',
            'event',
            'allEvents',
            'eventStats',
            'schoolsSummary'
        ));
    }

    public function show($id)
    {
        $registration = CompetitionRegistration::with(['event', 'teams.category'])->findOrFail($id);

        return view('admin.competition.registrations.show', compact('registration'));
    }

    public function update(Request $request, $id)
    {
        $registration = CompetitionRegistration::with(['event', 'teams.category'])->findOrFail($id);

        $validated = $request->validate([
            'registration_wave' => 'required|in:1,2',
            'total_payment' => 'nullable|numeric|min:0',
            'recalculate_payment' => 'nullable|boolean',
        ]);

        $newWave = intval($validated['registration_wave']);
        $registration->registration_wave = $newWave;

        if ($request->boolean('recalculate_payment') || ! isset($validated['total_payment']) || $validated['total_payment'] === null) {
            $calculatedTotal = 0;
            foreach ($registration->teams as $team) {
                $cat = $team->category;
                $fee = $cat
                    ? $cat->getFeeForWave($newWave)
                    : floatval($registration->event->registration_fee ?: 150000);
                $calculatedTotal += $fee;
            }
            $registration->total_payment = $calculatedTotal;
        } else {
            $registration->total_payment = floatval($validated['total_payment']);
        }

        $registration->save();

        return redirect()->route('admin.competition-registrations.show', $registration->id)
            ->with('success', "Gelombang & nominal tagihan {$registration->school_name} berhasil diubah ke Gelombang {$newWave} dengan total nominal Rp ".number_format($registration->total_payment, 0, ',', '.').'.');
    }

    public function verify($id)
    {
        $registration = CompetitionRegistration::with(['event', 'teams.category'])->findOrFail($id);
        $registration->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->user()->name ?? 'Admin',
        ]);

        // Kirim otomatis email e-Kwitansi & Kartu Peserta ke email pendaftar
        $emailSent = false;
        if (! empty($registration->advisor_email) && filter_var($registration->advisor_email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($registration->advisor_email)->send(new RegistrationVerifiedMail($registration));
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim email verifikasi ke {$registration->advisor_email}: ".$e->getMessage());
            }
        }

        $successMsg = "Pendaftaran {$registration->school_name} ({$registration->registration_code}) berhasil diverifikasi! e-Kwitansi & Kartu Peserta kini sudah aktif.";
        if ($emailSent) {
            $successMsg .= " Dokumen e-Kwitansi & Kartu Peserta telah berhasil dikirimkan via email ke {$registration->advisor_email}.";
        } elseif (! empty($registration->advisor_email)) {
            $successMsg .= ' (Catatan: Pengiriman email notifikasi mengalami kendala koneksi server mail).';
        }

        return redirect()->back()->with('success', $successMsg);
    }

    public function resendEmail($id)
    {
        $registration = CompetitionRegistration::with(['event', 'teams.category'])->findOrFail($id);

        if (empty($registration->advisor_email) || ! filter_var($registration->advisor_email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', "Pendaftaran {$registration->school_name} tidak memiliki alamat email yang valid.");
        }

        try {
            Mail::to($registration->advisor_email)->send(new RegistrationVerifiedMail($registration));

            return redirect()->back()->with('success', "Dokumen e-Kwitansi & Kartu Peserta berhasil dikirimkan ke {$registration->advisor_email}!");
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim ulang email verifikasi ke {$registration->advisor_email}: ".$e->getMessage());

            return redirect()->back()->with('error', 'Gagal mengirim email: '.$e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $registration = CompetitionRegistration::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_at' => now(),
            'verified_by' => auth()->user()->name ?? 'Admin',
        ]);

        return redirect()->back()->with('success', "Pendaftaran {$registration->school_name} telah ditolak.");
    }

    public function verifyFollowup($id)
    {
        $registration = CompetitionRegistration::with(['event', 'teams.category'])->findOrFail($id);
        $history = $registration->getFollowupHistory();
        $adminName = auth()->user()->name ?? 'Admin';

        // Tandai seluruh entri susulan pending menjadi terverifikasi
        $updatedHistory = [];
        $verifiedCount = 0;
        foreach ($history as $entry) {
            if (($entry['status'] ?? '') === 'pending') {
                $entry['status'] = 'verified';
                $entry['verified_at'] = now()->toDateTimeString();
                $entry['verified_by'] = $adminName;
                $verifiedCount++;
            }
            $updatedHistory[] = $entry;
        }

        if (Schema::hasColumn('competition_registrations', 'has_pending_followup')) {
            $registration->has_pending_followup = false;
        }
        if (Schema::hasColumn('competition_registrations', 'followup_history')) {
            $registration->followup_history = $updatedHistory;
        }
        $registration->status = 'verified'; // Pastikan status kontingen lunas
        $registration->verified_at = now();
        $registration->verified_by = $adminName;

        // Catat riwayat di checkin_notes
        $catatan = "Pembayaran susulan telah diverifikasi oleh {$adminName} pada ".now()->format('d/m/Y H:i');
        $cleanOldNotes = trim(preg_replace('/\[FOLLOWUP_HISTORY\].*?\[\/FOLLOWUP_HISTORY\]/s', '', $registration->checkin_notes ?? ''));
        $newNotes = trim(($cleanOldNotes ? $cleanOldNotes.' | ' : '')."{$catatan}");
        $newNotes .= "\n[FOLLOWUP_HISTORY]".json_encode($updatedHistory).'[/FOLLOWUP_HISTORY]';
        $registration->checkin_notes = $newNotes;

        $registration->save();

        // Kirim email e-Kwitansi & Kartu Peserta terakumulasi terbaru ke pembina
        $emailSent = false;
        if (! empty($registration->advisor_email) && filter_var($registration->advisor_email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($registration->advisor_email)->send(new RegistrationVerifiedMail($registration));
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim email verifikasi susulan ke {$registration->advisor_email}: ".$e->getMessage());
            }
        }

        $successMsg = "Pembayaran susulan untuk {$registration->school_name} ({$registration->registration_code}) berhasil diverifikasi!";
        if ($emailSent) {
            $successMsg .= " Dokumen e-Kwitansi & Kartu Peserta terbaru telah dikirimkan ke {$registration->advisor_email}.";
        }

        return redirect()->back()->with('success', $successMsg);
    }

    public function proof(Request $request, CompetitionRegistration $registration)
    {
        $targetPath = $registration->payment_proof;

        $followupId = $request->query('followup_id');
        if ($followupId) {
            foreach ($registration->getFollowupHistory() as $idx => $fItem) {
                $proofCandidate = $fItem['payment_proof'] ?? ($fItem['proof_file'] ?? null);
                if (
                    ((string) ($fItem['id'] ?? '') === (string) $followupId || (string) ($idx + 1) === (string) $followupId || (string) $idx === (string) $followupId)
                    && ! empty($proofCandidate)
                ) {
                    $targetPath = $proofCandidate;
                    break;
                }
            }
        }

        if (! $targetPath) {
            abort(404, 'Bukti pembayaran belum diunggah untuk pendaftaran ini.');
        }

        $cleanPath = ltrim(str_replace('storage/', '', $targetPath), '/');

        $candidatePaths = [
            Storage::disk('public')->path($cleanPath),
            storage_path('app/public/'.$cleanPath),
            public_path('storage/'.$cleanPath),
            public_path($cleanPath),
            storage_path('app/'.$cleanPath),
        ];

        foreach ($candidatePaths as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                $mime = mime_content_type($candidate) ?: 'application/octet-stream';

                return response()->file($candidate, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="'.basename($candidate).'"',
                    'Cache-Control' => 'no-cache, private',
                ]);
            }
        }

        abort(404, 'Berkas fisik bukti transfer tidak ditemukan di server.');
    }

    public function destroy($id)
    {
        $registration = CompetitionRegistration::find($id);

        if (! $registration) {
            return redirect()->route('admin.competition-registrations.index')->with('success', 'Data pendaftaran telah dihapus.');
        }

        $school = $registration->school_name;

        DB::transaction(function () use ($registration) {
            $registration->delete();
        });

        return redirect()->route('admin.competition-registrations.index')->with('success', "Data pendaftaran {$school} berhasil dihapus beserta seluruh peserta, nilai juri, dan tiket kendaraannya.");
    }

    /**
     * Hapus masal data pendaftaran kontingen terpilih
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids) || ! is_array($ids)) {
            return redirect()->route('admin.competition-registrations.index')->with('error', 'Tidak ada data pendaftaran yang dipilih untuk dihapus.');
        }

        $ids = array_map('intval', array_filter($ids));

        if (empty($ids)) {
            return redirect()->route('admin.competition-registrations.index')->with('error', 'Data pendaftaran yang dipilih tidak valid.');
        }

        try {
            $registrations = CompetitionRegistration::whereIn('id', $ids)->get();
            $count = $registrations->count();

            if ($count === 0) {
                return redirect()->route('admin.competition-registrations.index')->with('error', 'Data pendaftaran terpilih tidak ditemukan.');
            }

            DB::transaction(function () use ($registrations) {
                foreach ($registrations as $reg) {
                    $reg->delete();
                }
            });

            return redirect()->route('admin.competition-registrations.index')->with('success', "Berhasil menghapus {$count} data pendaftaran kontingen terpilih beserta seluruh peserta, nilai juri, dan tiket kendaraannya.");
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data pendaftaran masal: '.$e->getMessage());

            return redirect()->route('admin.competition-registrations.index')->with('error', 'Gagal menghapus data pendaftaran terpilih: '.$e->getMessage());
        }
    }

    /**
     * Verifikasi masal data pendaftaran kontingen terpilih
     */
    public function verifyMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids) || ! is_array($ids)) {
            return redirect()->route('admin.competition-registrations.index')->with('error', 'Tidak ada data pendaftaran yang dipilih untuk diverifikasi.');
        }

        $ids = array_map('intval', array_filter($ids));

        if (empty($ids)) {
            return redirect()->route('admin.competition-registrations.index')->with('error', 'Data pendaftaran yang dipilih tidak valid.');
        }

        try {
            $registrations = CompetitionRegistration::with(['event', 'teams.category'])
                ->whereIn('id', $ids)
                ->where('status', '!=', 'verified')
                ->get();

            $count = $registrations->count();

            if ($count === 0) {
                return redirect()->route('admin.competition-registrations.index')->with('info', 'Semua data pendaftaran yang dipilih sudah berstatus terverifikasi (lunas).');
            }

            $verifierName = auth()->user()->name ?? 'Admin';
            $now = now();
            $emailsSent = 0;

            foreach ($registrations as $reg) {
                $reg->update([
                    'status' => 'verified',
                    'verified_at' => $now,
                    'verified_by' => $verifierName,
                ]);

                // Kirim notifikasi email e-Kwitansi & Kartu Peserta jika ada email valid
                if (! empty($reg->advisor_email) && filter_var($reg->advisor_email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        Mail::to($reg->advisor_email)->send(new RegistrationVerifiedMail($reg));
                        $emailsSent++;
                    } catch (\Throwable $e) {
                        Log::error("Gagal mengirim email verifikasi masal ke {$reg->advisor_email}: ".$e->getMessage());
                    }
                }
            }

            $msg = "Berhasil memverifikasi {$count} data pendaftaran kontingen terpilih!";
            if ($emailsSent > 0) {
                $msg .= " Dokumen e-Kwitansi & Kartu Peserta telah otomatis dikirimkan ke {$emailsSent} email pembina.";
            }

            return redirect()->route('admin.competition-registrations.index')->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Gagal verifikasi data pendaftaran masal: '.$e->getMessage());

            return redirect()->route('admin.competition-registrations.index')->with('error', 'Gagal memverifikasi pendaftaran terpilih: '.$e->getMessage());
        }
    }
}

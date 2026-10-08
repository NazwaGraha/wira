<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CompetitionBroadcastMail;
use App\Models\CompetitionEmailBroadcast;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CompetitionBroadcastController extends Controller
{
    public function index()
    {
        // Statistik Kontak Email dalam Database Pendaftaran
        $allRegistrationsWithEmail = CompetitionRegistration::whereNotNull('advisor_email')
            ->where('advisor_email', '!=', '')
            ->where('advisor_email', 'like', '%@%')
            ->get();

        $totalUniqueEmails = $allRegistrationsWithEmail->pluck('advisor_email')->map(fn($e) => strtolower(trim($e)))->unique()->count();
        $totalMulaEmails = $allRegistrationsWithEmail->where('level', 'Mula')->pluck('advisor_email')->map(fn($e) => strtolower(trim($e)))->unique()->count();
        $totalMadyaEmails = $allRegistrationsWithEmail->where('level', 'Madya')->pluck('advisor_email')->map(fn($e) => strtolower(trim($e)))->unique()->count();
        $totalWiraEmails = $allRegistrationsWithEmail->where('level', 'Wira')->pluck('advisor_email')->map(fn($e) => strtolower(trim($e)))->unique()->count();

        // Riwayat Siaran Email
        $broadcasts = CompetitionEmailBroadcast::with('event')->latest()->paginate(10);

        return view('admin.competition.broadcast.index', compact(
            'totalUniqueEmails',
            'totalMulaEmails',
            'totalMadyaEmails',
            'totalWiraEmails',
            'broadcasts'
        ));
    }

    public function create(Request $request)
    {
        $events = CompetitionEvent::orderByDesc('id')->get();
        $activeEvent = CompetitionEvent::where('is_active', true)->first() ?: $events->first();

        // Ambil daftar seluruh kontak email pendaftar tersimpan untuk live preview target
        $targetEvent = $request->query('target_event', 'all');
        $targetLevel = $request->query('target_level', 'all');
        $targetStatus = $request->query('target_status', 'all');

        $recipients = $this->resolveRecipients($targetEvent, $targetLevel, $targetStatus);

        return view('admin.competition.broadcast.create', compact('events', 'activeEvent', 'recipients', 'targetEvent', 'targetLevel', 'targetStatus'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'headline' => 'nullable|string|max:255',
            'content' => 'required|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'notes' => 'nullable|string|max:1000',
            'target_event' => 'required|string',
            'target_level' => 'required|in:all,Mula,Madya,Wira',
            'target_status' => 'required|in:all,verified',
            'is_test_mode' => 'nullable|boolean',
            'test_email' => 'nullable|email|max:255',
        ]);

        $isTestMode = $request->boolean('is_test_mode');
        $testEmail = trim($validated['test_email'] ?? '');

        if ($isTestMode && empty($testEmail)) {
            return redirect()->back()->withInput()->with('error', 'Silakan masukkan alamat email uji coba jika memilih mode test.');
        }

        // Tentukan daftar penerima
        if ($isTestMode) {
            $recipients = [
                $testEmail => [
                    'email' => $testEmail,
                    'school_name' => 'Uji Coba Panitia PMR',
                    'advisor_name' => auth()->user()->name ?? 'Administrator',
                    'level' => 'Test',
                    'event_id' => null,
                ]
            ];
        } else {
            $recipients = $this->resolveRecipients(
                $validated['target_event'],
                $validated['target_level'],
                $validated['target_status']
            );

            if (empty($recipients)) {
                return redirect()->back()->withInput()->with('error', 'Tidak ditemukan data kontak email yang sesuai dengan kriteria target.');
            }
        }

        $sentCount = 0;
        $failedCount = 0;
        $recipientsData = [];

        foreach ($recipients as $item) {
            try {
                Mail::to($item['email'])->send(new CompetitionBroadcastMail([
                    'subject' => $validated['subject'],
                    'headline' => $validated['headline'] ?: 'SUA BHAKTI BERKARYA',
                    'content' => $validated['content'],
                    'button_text' => $validated['button_text'] ?? null,
                    'button_url' => $validated['button_url'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ], $item));

                $sentCount++;
                $recipientsData[] = array_merge($item, ['status' => 'sent', 'sent_at' => now()->toDateTimeString()]);
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim siaran email ke {$item['email']}: " . $e->getMessage());
                $failedCount++;
                $recipientsData[] = array_merge($item, ['status' => 'failed', 'error' => $e->getMessage()]);
            }
        }

        $eventId = ($validated['target_event'] !== 'all' && is_numeric($validated['target_event'])) ? (int)$validated['target_event'] : null;

        $broadcast = CompetitionEmailBroadcast::create([
            'competition_event_id' => $eventId,
            'subject' => $validated['subject'],
            'headline' => $validated['headline'] ?: 'SUA BHAKTI BERKARYA',
            'content' => $validated['content'],
            'button_text' => $validated['button_text'] ?? null,
            'button_url' => $validated['button_url'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'target_scope' => $validated['target_event'],
            'target_level' => $validated['target_level'],
            'target_status' => $validated['target_status'],
            'recipient_count' => $sentCount,
            'recipients_data' => $recipientsData,
            'sent_by' => auth()->user()->name ?? 'Administrator',
            'status' => $sentCount > 0 ? 'sent' : ($failedCount > 0 ? 'failed' : 'sent'),
        ]);

        $statusMsg = $isTestMode 
            ? "Uji coba siaran email berhasil dikirim ke {$testEmail}!"
            : "Siaran email berhasil dikirimkan ke {$sentCount} kontak sekolah/pembina terdaftar" . ($failedCount > 0 ? " ({$failedCount} gagal dikirim)" : "") . "!";

        return redirect()->route('admin.competition-broadcast.show', $broadcast->id)->with('success', $statusMsg);
    }

    public function show($id)
    {
        $broadcast = CompetitionEmailBroadcast::with('event')->findOrFail($id);
        return view('admin.competition.broadcast.show', compact('broadcast'));
    }

    public function destroy($id)
    {
        $broadcast = CompetitionEmailBroadcast::findOrFail($id);
        $broadcast->delete();

        return redirect()->route('admin.competition-broadcast.index')->with('success', 'Riwayat siaran email berhasil dihapus.');
    }

    private function resolveRecipients($targetEvent, $targetLevel, $targetStatus): array
    {
        $query = CompetitionRegistration::whereNotNull('advisor_email')
            ->where('advisor_email', '!=', '')
            ->where('advisor_email', 'like', '%@%');

        if ($targetEvent !== 'all' && is_numeric($targetEvent)) {
            $query->where('competition_event_id', (int)$targetEvent);
        }

        if ($targetLevel !== 'all') {
            $query->where('level', $targetLevel);
        }

        if ($targetStatus === 'verified') {
            $query->where('status', 'verified');
        }

        $registrations = $query->orderBy('school_name')->get();

        $recipients = [];
        foreach ($registrations as $reg) {
            $email = strtolower(trim($reg->advisor_email));
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (!isset($recipients[$email])) {
                    $recipients[$email] = [
                        'email' => $email,
                        'school_name' => $reg->school_name,
                        'advisor_name' => $reg->advisor_name,
                        'advisor_phone' => $reg->advisor_phone,
                        'level' => $reg->level,
                        'event_id' => $reg->competition_event_id,
                        'status' => $reg->status,
                    ];
                }
            }
        }

        return $recipients;
    }
}

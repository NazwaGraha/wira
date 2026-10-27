<?php

namespace App\Http\Controllers\Jury;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionScore;
use App\Models\CompetitionScorePhoto;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JuryPortalController extends Controller
{
    /**
     * Tampilan login khusus juri (100% Mobile-Friendly)
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('jury.dashboard');
        }

        return view('jury.login');
    }

    /**
     * Proses autentikasi akun juri
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Silakan masukkan username juri Anda.',
            'password.required' => 'PIN / Kata sandi wajib diisi.',
        ]);

        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');
        $remember = $request->boolean('remember');

        // Cari user berdasarkan username atau email
        $user = User::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($user && Auth::attempt(['id' => $user->id, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            return redirect()->route('jury.dashboard');
        }

        // Coba login via credentials default
        if (Auth::attempt(['login_id' => $username, 'password' => $password], $remember)
            || Auth::attempt(['username' => $username, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            return redirect()->route('jury.dashboard');
        }

        return back()->withInput($request->only('username'))
            ->withErrors(['username' => 'Username atau PIN juri tidak cocok. Silakan tanyakan ke panitia.']);
    }

    /**
     * Logout juri
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('jury.login')->with('success', 'Anda telah berhasil keluar dari sesi penjurian.');
    }

    /**
     * Dashboard Utama Juri: Menampilkan mata lomba yang DITUGASKAN ke juri ini
     */
    public function dashboard(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Ambil event aktif
        $event = CompetitionEvent::where('is_active', true)->first()
            ?: CompetitionEvent::orderByDesc('id')->first();

        // Filter cabang lomba yang ditugaskan kepada juri
        if ($user instanceof User && $user->role === 'jury') {
            $categoriesQuery = $user->assignedCategories();
        } else {
            // Jika admin/superuser yang sedang membuka portal juri, tampilkan semua cabang lomba aktif
            $categoriesQuery = CompetitionCategory::query();
        }

        $categories = $categoriesQuery
            ->when($event, fn ($q) => $q->where('competition_event_id', $event->id))
            ->where('is_active', true)
            ->withCount(['teams' => function ($q) {
                $q->where('is_active', true)->where(function ($sq) {
                    $sq->whereHas('registration', fn ($rq) => $rq->where('status', 'verified'))
                        ->orWhereNull('competition_registration_id');
                });
            }])
            ->orderBy('level')
            ->orderBy('order_position')
            ->get();

        // Hitung progres penilaian untuk masing-masing cabang lomba
        $categoryIds = $categories->pluck('id')->toArray();
        $scoresGrouped = CompetitionScore::whereIn('competition_category_id', $categoryIds)
            ->select('competition_category_id', 'competition_participant_team_id')
            ->distinct()
            ->get()
            ->groupBy('competition_category_id');

        $totalTeamsAssigned = 0;
        $totalTeamsScored = 0;

        foreach ($categories as $cat) {
            $scoredCount = isset($scoresGrouped[$cat->id]) ? $scoresGrouped[$cat->id]->count() : 0;
            $cat->scored_teams_count = $scoredCount;
            $cat->progress_percent = $cat->teams_count > 0 ? round(($scoredCount / $cat->teams_count) * 100) : 0;

            $totalTeamsAssigned += $cat->teams_count;
            $totalTeamsScored += $scoredCount;
        }

        // Total foto bukti yang tersimpan
        $totalPhotosCount = CompetitionScorePhoto::whereHas('score', function ($q) use ($categoryIds) {
            $q->whereIn('competition_category_id', $categoryIds);
        })->count();

        return view('jury.dashboard', compact(
            'user',
            'event',
            'categories',
            'totalTeamsAssigned',
            'totalTeamsScored',
            'totalPhotosCount'
        ));
    }

    /**
     * Form Penilaian & Dokumentasi Foto Mobile (100% Mobile-First)
     */
    public function scoring(CompetitionCategory $category, Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Otorisasi: Juri hanya boleh membuka lomba tugasnya
        if ($user instanceof User && $user->role === 'jury') {
            $hasAccess = $user->assignedCategories()->where('competition_categories.id', $category->id)->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak ditugaskan untuk menilai cabang lomba '.$category->name.' ('.$category->level.').');
            }
        }

        // Ambil semua regu peserta terverifikasi berurutan no urut tampil
        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('registration', fn ($rq) => $rq->where('status', 'verified'))
                    ->orWhereNull('competition_registration_id');
            })
            ->with(['registration', 'scores', 'photos'])
            ->orderBy('order_number')
            ->get();

        // Tentukan regu aktif yang sedang dinilai
        $requestedTeamId = $request->query('team_id');
        if ($requestedTeamId) {
            $activeTeam = $teams->firstWhere('id', (int) $requestedTeamId) ?: $teams->first();
        } else {
            // Default ke regu pertama yang BELUM dinilai, atau regu pertama jika semua sudah
            $unscored = $teams->first(fn ($t) => $t->scores->isEmpty());
            $activeTeam = $unscored ?: $teams->first();
        }

        // Ambil skor aktif regu terpilih
        $activeScore = null;
        if ($activeTeam) {
            $activeScore = CompetitionScore::where('competition_category_id', $category->id)
                ->where('competition_participant_team_id', $activeTeam->id)
                ->first();
        }

        // Ambil foto dokumentasi aktif untuk regu terpilih
        $activePhotos = $activeTeam
            ? CompetitionScorePhoto::where('competition_participant_team_id', $activeTeam->id)->get()
            : collect();

        // Hitung menit dan detik dari time_recorded (format mm:ss)
        $recordedMinutes = '00';
        $recordedSeconds = '00';
        if ($activeScore && $activeScore->time_recorded) {
            $timeParts = explode(':', (string) $activeScore->time_recorded);
            if (count($timeParts) >= 2) {
                $recordedMinutes = str_pad(trim($timeParts[0]), 2, '0', STR_PAD_LEFT);
                $recordedSeconds = str_pad(trim($timeParts[1]), 2, '0', STR_PAD_LEFT);
            }
        }

        return view('jury.scoring', compact(
            'category',
            'teams',
            'activeTeam',
            'activeScore',
            'activePhotos',
            'recordedMinutes',
            'recordedSeconds'
        ));
    }

    /**
     * Simpan Nilai Regu & Lanjut ke Regu Berikutnya
     */
    public function saveScore(CompetitionCategory $category, CompetitionParticipantTeam $team, Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'final_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'seconds' => ['nullable', 'integer', 'min:0', 'max:59'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Cek jika kategori mewajibkan foto dokumentasi
        if ($category->is_photo_required) {
            $photoCount = CompetitionScorePhoto::where('competition_participant_team_id', $team->id)->count();
            if ($photoCount === 0) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Wajib melampirkan minimal 1 foto dokumentasi untuk cabang lomba ini sebelum nilai disimpan.',
                    ], 422);
                }

                return back()->with('error', 'Wajib melampirkan minimal 1 foto dokumentasi untuk cabang lomba ini sebelum nilai disimpan.');
            }
        }

        $minutes = (int) $request->input('minutes', 0);
        $seconds = (int) $request->input('seconds', 0);
        $timeRecorded = sprintf('%02d:%02d', $minutes, $seconds);

        $score = CompetitionScore::updateOrCreate(
            [
                'competition_category_id' => $category->id,
                'competition_participant_team_id' => $team->id,
                'round_name' => 'Utama',
            ],
            [
                'jury_user_id' => Auth::id(),
                'final_score' => $request->input('final_score'),
                'time_recorded' => $timeRecorded,
                'notes' => $request->input('notes'),
            ]
        );

        // Hubungkan semua foto regu ini ke competition_score_id
        CompetitionScorePhoto::where('competition_participant_team_id', $team->id)
            ->whereNull('competition_score_id')
            ->update(['competition_score_id' => $score->id]);

        // Cari regu berikutnya yang tampil
        $allTeams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('order_number')
            ->get();

        $nextTeam = $allTeams->first(fn ($t) => $t->order_number > $team->order_number);

        $teamDisplayName = $team->order_number
            ? "Regu {$team->order_number} ({$team->school_name})"
            : ($team->team_name ?: 'Regu '.$team->team_label);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Nilai {$teamDisplayName} berhasil disimpan!",
                'next_team_id' => $nextTeam?->id,
                'next_url' => $nextTeam ? route('jury.scoring', ['category' => $category->id, 'team_id' => $nextTeam->id]) : null,
            ]);
        }

        if ($nextTeam) {
            return redirect()->route('jury.scoring', ['category' => $category->id, 'team_id' => $nextTeam->id])
                ->with('success', "Nilai {$teamDisplayName} berhasil disimpan! Menampilkan regu berikutnya.");
        }

        return redirect()->route('jury.scoring', ['category' => $category->id, 'team_id' => $team->id])
            ->with('success', "Nilai {$teamDisplayName} berhasil disimpan! Seluruh regu telah dinilai.");
    }

    /**
     * Upload Foto Dokumentasi Regu (Mendukung Kamera Langsung Base64 & Upload Berkas File)
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $teamId = $request->input('team_id');
        $caption = $request->input('caption', 'Dokumentasi Lapangan');

        if (! $teamId) {
            return response()->json(['success' => false, 'message' => 'ID Regu peserta tidak valid.'], 400);
        }

        $team = CompetitionParticipantTeam::find($teamId);
        if (! $team) {
            return response()->json(['success' => false, 'message' => 'Data regu peserta tidak ditemukan.'], 404);
        }

        $destinationDir = storage_path("app/public/competition_evidences/{$team->competition_category_id}/{$team->id}");
        File::ensureDirectoryExists($destinationDir, 0755, true);

        $publicMirrorDir = public_path("storage/competition_evidences/{$team->competition_category_id}/{$team->id}");
        File::ensureDirectoryExists($publicMirrorDir, 0755, true);

        $filename = 'evidence_'.time().'_'.Str::random(8);
        $fileSizeFormatted = '300 KB';
        $relativeStoragePath = '';

        // Kasus 1: Upload via Base64 (Dari Bidik Kamera HP Langsung Canvas)
        if ($request->filled('image_base64')) {
            $base64Data = (string) $request->input('image_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $ext = strtolower($type[1]);
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = 'jpg';
                }

                $decodedData = base64_decode($base64Data);
                if ($decodedData === false) {
                    return response()->json(['success' => false, 'message' => 'Gagal memproses data gambar dari kamera.'], 422);
                }

                $filename .= '.'.$ext;
                $fullPath = $destinationDir.'/'.$filename;
                file_put_contents($fullPath, $decodedData);
                @copy($fullPath, $publicMirrorDir.'/'.$filename);

                $fileSize = strlen($decodedData);
                $fileSizeFormatted = $fileSize > 1048576
                    ? round($fileSize / 1048576, 1).' MB'
                    : round($fileSize / 1024).' KB';

                $relativeStoragePath = "competition_evidences/{$team->competition_category_id}/{$team->id}/".$filename;
            }
        }
        // Kasus 2: Upload via Berkas File (Pilih dari Galeri)
        elseif ($request->hasFile('photo_file')) {
            $request->validate([
                'photo_file' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:15360'],
            ]);

            $file = $request->file('photo_file');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename .= '.'.$ext;

            $file->move($destinationDir, $filename);
            @copy($destinationDir.'/'.$filename, $publicMirrorDir.'/'.$filename);

            $fileSize = File::size($destinationDir.'/'.$filename);
            $fileSizeFormatted = $fileSize > 1048576
                ? round($fileSize / 1048576, 1).' MB'
                : round($fileSize / 1024).' KB';

            $relativeStoragePath = "competition_evidences/{$team->competition_category_id}/{$team->id}/".$filename;
        } else {
            return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang dikirimkan.'], 400);
        }

        // Cari atau buat relasi score
        $existingScore = CompetitionScore::where('competition_participant_team_id', $team->id)->first();

        $photo = CompetitionScorePhoto::create([
            'competition_score_id' => $existingScore?->id,
            'competition_participant_team_id' => $team->id,
            'photo_path' => $relativeStoragePath,
            'caption' => $caption,
            'file_size' => $fileSizeFormatted,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil disimpan sebagai dokumentasi.',
            'photo' => [
                'id' => $photo->id,
                'photo_url' => $photo->photo_url,
                'caption' => $photo->caption,
                'file_size' => $photo->file_size,
                'created_at' => $photo->created_at->format('H:i:s'),
            ],
        ]);
    }

    /**
     * Hapus Foto Dokumentasi
     */
    public function deletePhoto(CompetitionScorePhoto $photo): JsonResponse
    {
        $filePath = storage_path('app/public/'.$photo->photo_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $publicPath = public_path('storage/'.$photo->photo_path);
        if (File::exists($publicPath)) {
            File::delete($publicPath);
        }

        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto dokumentasi berhasil dihapus.',
        ]);
    }
}

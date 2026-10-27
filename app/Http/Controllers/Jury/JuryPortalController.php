<?php

namespace App\Http\Controllers\Jury;

use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionScore;
use App\Models\CompetitionScorePhoto;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
            // Cek jika juri login pertama kali dan wajib ganti password
            if ($user->role === 'jury' && $user->must_change_password) {
                Auth::logout();
                $request->session()->put('jury_password_change_user_id', $user->id);

                return redirect()->route('jury.first-password-change')
                    ->with('info', 'Ini adalah login pertama Anda. Demi keamanan, Anda wajib mengganti password / PIN awal terlebih dahulu.');
            }

            $request->session()->regenerate();

            return redirect()->route('jury.dashboard');
        }

        // Coba login via credentials default
        if (Auth::attempt(['login_id' => $username, 'password' => $password], $remember)
            || Auth::attempt(['username' => $username, 'password' => $password], $remember)) {
            $authUser = Auth::user();
            if ($authUser instanceof User && $authUser->role === 'jury' && $authUser->must_change_password) {
                $uid = $authUser->id;
                Auth::logout();
                $request->session()->put('jury_password_change_user_id', $uid);

                return redirect()->route('jury.first-password-change')
                    ->with('info', 'Ini adalah login pertama Anda. Demi keamanan, Anda wajib mengganti password / PIN awal terlebih dahulu.');
            }

            $request->session()->regenerate();

            return redirect()->route('jury.dashboard');
        }

        return back()->withInput($request->only('username'))
            ->withErrors(['username' => 'Username atau PIN juri tidak cocok. Silakan tanyakan ke panitia.']);
    }

    /**
     * Tampilan form wajib ganti password saat login pertama kali bagi juri
     */
    public function showFirstPasswordChange(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('jury_password_change_user_id');
        if (! $userId) {
            return redirect()->route('jury.login')
                ->with('error', 'Sesi penggantian password tidak ditemukan atau telah kedaluwarsa. Silakan login kembali.');
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('jury.login');
        }

        return view('jury.first-password-change', compact('user'));
    }

    /**
     * Proses simpan password baru pada login pertama kali bagi juri
     */
    public function updateFirstPassword(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('jury_password_change_user_id');
        if (! $userId) {
            return redirect()->route('jury.login')
                ->with('error', 'Sesi penggantian password telah berakhir. Silakan login kembali.');
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('jury.login');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ], [
            'password.required' => 'Password / PIN baru wajib diisi.',
            'password.min' => 'Password / PIN baru minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make((string) $request->input('password')),
            'must_change_password' => false,
        ]);

        $request->session()->forget('jury_password_change_user_id');

        return redirect()->route('jury.login')
            ->with('success', 'Kata sandi / PIN berhasil diperbarui! Silakan login sekarang menggunakan username dan kata sandi baru Anda.');
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
     * Self-healing migration agar website juri di shared hosting tidak pernah Error 500
     */
    public static function ensureTablesExist(): void
    {
        try {
            // 1. Kolom tambahan tabel users
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    if (! Schema::hasColumn('users', 'username')) {
                        $table->string('username')->nullable()->unique()->after('name');
                    }
                    if (! Schema::hasColumn('users', 'role')) {
                        $table->string('role')->default('admin')->after('username');
                    }
                    if (! Schema::hasColumn('users', 'phone')) {
                        $table->string('phone')->nullable()->after('email');
                    }
                    if (! Schema::hasColumn('users', 'must_change_password')) {
                        $table->boolean('must_change_password')->default(false)->after('role');
                    }
                });
            }

            // 2. Tabel pivot pemetaan juri
            if (! Schema::hasTable('jury_category_assignments')) {
                Schema::create('jury_category_assignments', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                    $table->foreignId('competition_category_id')->constrained('competition_categories')->cascadeOnDelete();
                    $table->timestamps();

                    $table->unique(['user_id', 'competition_category_id'], 'uq_jury_category');
                });
            }

            // 3. Kolom jury_user_id pada competition_scores
            if (Schema::hasTable('competition_scores') && ! Schema::hasColumn('competition_scores', 'jury_user_id')) {
                Schema::table('competition_scores', function (Blueprint $table) {
                    $table->foreignId('jury_user_id')->nullable()->after('competition_participant_team_id')
                        ->constrained('users')->nullOnDelete();
                });
            }

            // 4. Kolom is_photo_required pada competition_categories
            if (Schema::hasTable('competition_categories') && ! Schema::hasColumn('competition_categories', 'is_photo_required')) {
                Schema::table('competition_categories', function (Blueprint $table) {
                    $table->boolean('is_photo_required')->default(false)->after('scoring_type');
                });
            }

            // 5. Tabel bukti foto competition_score_photos
            if (! Schema::hasTable('competition_score_photos')) {
                Schema::create('competition_score_photos', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('competition_score_id')->nullable()->constrained('competition_scores')->cascadeOnDelete();
                    $table->foreignId('competition_participant_team_id')->constrained('competition_participant_teams')->cascadeOnDelete();
                    $table->string('photo_path');
                    $table->string('caption')->nullable();
                    $table->string('file_size')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::warning('JuryPortalController ensureTablesExist: '.$e->getMessage());
        }
    }

    /**
     * Dashboard Utama Juri: Menampilkan mata lomba yang DITUGASKAN ke juri ini
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        self::ensureTablesExist();

        /** @var User $user */
        $user = Auth::user();

        if ($user instanceof User && ($user->role ?? null) === 'jury' && $user->must_change_password) {
            $uid = $user->id;
            Auth::logout();
            $request->session()->put('jury_password_change_user_id', $uid);

            return redirect()->route('jury.first-password-change')
                ->with('info', 'Ini adalah login pertama Anda. Demi keamanan, Anda wajib mengganti password / PIN awal terlebih dahulu.');
        }

        // Ambil event aktif
        $event = CompetitionEvent::where('is_active', true)->first()
            ?: CompetitionEvent::orderByDesc('id')->first();

        // Filter cabang lomba yang ditugaskan kepada juri
        if ($user instanceof User && ($user->role ?? null) === 'jury' && Schema::hasTable('jury_category_assignments')) {
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
        $scoresGrouped = collect();
        if (Schema::hasTable('competition_scores')) {
            try {
                $scoresGrouped = CompetitionScore::whereIn('competition_category_id', $categoryIds)
                    ->select('competition_category_id', 'competition_participant_team_id')
                    ->distinct()
                    ->get()
                    ->groupBy('competition_category_id');
            } catch (\Throwable $e) {
                Log::warning('JuryPortalController dashboard scoresGrouped error: '.$e->getMessage());
            }
        }

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
        $totalPhotosCount = 0;
        if (Schema::hasTable('competition_score_photos')) {
            try {
                $totalPhotosCount = CompetitionScorePhoto::whereHas('team', function ($q) use ($categoryIds) {
                    $q->whereIn('competition_category_id', $categoryIds);
                })->count();
            } catch (\Throwable $e) {
                Log::warning('JuryPortalController dashboard photos count error: '.$e->getMessage());
                $totalPhotosCount = 0;
            }
        }

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
        self::ensureTablesExist();

        /** @var User $user */
        $user = Auth::user();

        // Otorisasi: Juri hanya boleh membuka lomba tugasnya
        if ($user instanceof User && ($user->role ?? null) === 'jury' && Schema::hasTable('jury_category_assignments')) {
            $hasAccess = $user->assignedCategories()->where('competition_categories.id', $category->id)->exists();
            if (! $hasAccess) {
                abort(403, 'Anda tidak ditugaskan untuk menilai cabang lomba '.$category->name.' ('.$category->level.').');
            }
        }

        $withRelations = ['registration', 'scores'];
        if (Schema::hasTable('competition_score_photos')) {
            $withRelations[] = 'photos';
        }

        // Ambil semua regu peserta terverifikasi berurutan no urut tampil
        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('registration', fn ($rq) => $rq->where('status', 'verified'))
                    ->orWhereNull('competition_registration_id');
            })
            ->with($withRelations)
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
        self::ensureTablesExist();

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
        self::ensureTablesExist();

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

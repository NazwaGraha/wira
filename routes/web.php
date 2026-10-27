<?php

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AdvisorController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BackgroundMusicController;
use App\Http\Controllers\Admin\BloodDonationEventController;
use App\Http\Controllers\Admin\BloodDonorRegistrationController;
use App\Http\Controllers\Admin\BloodStockController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompetitionBankAccountController;
use App\Http\Controllers\Admin\CompetitionBroadcastController;
use App\Http\Controllers\Admin\CompetitionCheckinController;
use App\Http\Controllers\Admin\CompetitionEventController;
use App\Http\Controllers\Admin\CompetitionFeeController;
use App\Http\Controllers\Admin\CompetitionInfoMenuController;
use App\Http\Controllers\Admin\CompetitionJuryController;
use App\Http\Controllers\Admin\CompetitionLeaderboardController;
use App\Http\Controllers\Admin\CompetitionParticipantController;
use App\Http\Controllers\Admin\CompetitionRegistrationController;
use App\Http\Controllers\Admin\CompetitionScoreController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\MediaManagerController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberRegistrationController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationGuidelineController;
use App\Http\Controllers\Admin\PopupBannerController;
use App\Http\Controllers\Admin\UksHotlineController;
use App\Http\Controllers\Admin\WiraAgentKnowledgeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\Jury\JuryPortalController;
use App\Http\Controllers\MemberAreaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WiraAgentController;
use App\Services\OrganizationSyncService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Front-Office PMR WIRA SMAN 1 CIAWI)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/tentang-kami', [PageController::class, 'tentangKami'])->name('tentang-kami');
Route::get('/pedoman-organisasi', [PageController::class, 'pedomanOrganisasi'])->name('pedoman-organisasi');
Route::get('/tentang-kami/pedoman', fn () => redirect()->route('pedoman-organisasi'));
Route::get('/kegiatan', [PageController::class, 'kegiatan'])->name('kegiatan');
Route::get('/kegiatan/{slug}', [PageController::class, 'kegiatanShow'])->name('kegiatan.show');
Route::get('/galeri', [PageController::class, 'galeri'])->name('galeri');
Route::get('/donor-darah', [PageController::class, 'donorDarah'])->name('donor-darah');
Route::get('/kontak', [PageController::class, 'kontak'])->name('kontak');
Route::get('/prestasi', [AchievementController::class, 'publicIndex'])->name('prestasi.index');
Route::get('/popup-banner/{id}/click', [PageController::class, 'popupBannerClick'])->name('popup-banner.click');
Route::post('/popup-banner/{id}/impression', [PageController::class, 'popupBannerImpression'])->name('popup-banner.impression');
Route::get('/mars-pmi-audio', [BackgroundMusicController::class, 'streamAudio'])->name('mars-pmi.stream');
Route::post('/daftar-anggota', [PageController::class, 'storeRegistration'])->name('daftar-anggota.store');
Route::post('/donor-darah/daftar', [PageController::class, 'storeDonorRegistration'])->name('donor-darah.store');

// Artikel Publik
Route::get('/artikel', [ArticleController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('artikel.show');

// Chatbot AI Wira Agent
Route::post('/api/wira-agent/chat', [WiraAgentController::class, 'chat'])->name('wira-agent.chat');

// Fallback redirect for auth middleware default login route
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Backoffice / Admin Routes (CMS Pengelola Artikel & Konten)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Aktivasi Akun Pertama Kali (Buat Password Baru)
    Route::get('/aktivasi-akun', [AuthController::class, 'showActivationForm'])->name('activate');
    Route::post('/aktivasi-akun', [AuthController::class, 'activateAccount'])->name('activate.submit');

    // Lupa Kata Sandi & Reset Password via Tautan Email
    Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
    Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Protected Admin Routes with RBAC Middleware
    Route::middleware(['auth', 'backoffice.role'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manajemen Artikel
        Route::resource('artikel', AdminArticleController::class)->names([
            'index' => 'articles.index',
            'create' => 'articles.create',
            'store' => 'articles.store',
            'edit' => 'articles.edit',
            'update' => 'articles.update',
            'destroy' => 'articles.destroy',
        ]);

        // Manajemen Kategori Artikel
        Route::resource('kategori-artikel', CategoryController::class)->names([
            'index' => 'categories.index',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ]);
        Route::post('kategori-artikel/quick-store', [CategoryController::class, 'quickStore'])->name('categories.quick-store');

        // Pusat Kelola Gambar & Media Web Terpadu
        Route::get('/media-manager', [MediaManagerController::class, 'index'])->name('media-manager.index');
        Route::post('/media-manager/section', [MediaManagerController::class, 'updateSection'])->name('media-manager.update-section');
        Route::post('/media-manager/section/reset', [MediaManagerController::class, 'resetSection'])->name('media-manager.reset-section');

        // Pengaturan Slider Hero Banner
        Route::resource('hero-slides', HeroSlideController::class)->names([
            'index' => 'hero-slides.index',
            'create' => 'hero-slides.create',
            'store' => 'hero-slides.store',
            'edit' => 'hero-slides.edit',
            'update' => 'hero-slides.update',
            'destroy' => 'hero-slides.destroy',
        ]);

        // Pengaturan Popup Banner Iklan & Pengumuman
        Route::get('/popup-banner', [PopupBannerController::class, 'index'])->name('popup-banner.index');
        Route::post('/popup-banner', [PopupBannerController::class, 'store'])->name('popup-banner.store');
        Route::post('/popup-banner/{id}/toggle', [PopupBannerController::class, 'toggle'])->name('popup-banner.toggle');
        Route::delete('/popup-banner/{id}', [PopupBannerController::class, 'destroy'])->name('popup-banner.destroy');

        // Pengaturan Musik Latar (Mars PMI)
        Route::get('/background-music', [BackgroundMusicController::class, 'index'])->name('background-music.index');
        Route::put('/background-music', [BackgroundMusicController::class, 'update'])->name('background-music.update');
        Route::post('/background-music/reset', [BackgroundMusicController::class, 'resetDefault'])->name('background-music.reset');

        // Pengaturan Hotline & Siaga UKS
        Route::get('/uks-hotline', [UksHotlineController::class, 'index'])->name('uks-hotline.index');
        Route::put('/uks-hotline', [UksHotlineController::class, 'update'])->name('uks-hotline.update');
        Route::match(['post', 'patch'], '/uks-hotline/toggle', [UksHotlineController::class, 'toggleStatus'])->name('uks-hotline.toggle');

        // Kelola Kegiatan & Agenda
        Route::resource('kegiatan', ActivityController::class)->names([
            'index' => 'activities.index',
            'create' => 'activities.create',
            'store' => 'activities.store',
            'edit' => 'activities.edit',
            'update' => 'activities.update',
            'destroy' => 'activities.destroy',
        ]);

        // Bagan Kepengurusan (Struktur Organisasi)
        Route::get('/bagan-kepengurusan', [OrganizationController::class, 'index'])->name('organization.index');
        Route::post('/bagan-kepengurusan/sync-from-members', [OrganizationController::class, 'syncFromMembers'])->name('organization.sync-from-members');
        Route::put('/bagan-kepengurusan/setting', [OrganizationController::class, 'updateSetting'])->name('organization.setting.update');
        Route::get('/bagan-kepengurusan/create', [OrganizationController::class, 'create'])->name('organization.create');
        Route::post('/bagan-kepengurusan', [OrganizationController::class, 'store'])->name('organization.store');
        Route::get('/bagan-kepengurusan/{member}/edit', [OrganizationController::class, 'edit'])->name('organization.edit');
        Route::put('/bagan-kepengurusan/{member}', [OrganizationController::class, 'update'])->name('organization.update');
        Route::delete('/bagan-kepengurusan/{member}', [OrganizationController::class, 'destroy'])->name('organization.destroy');

        // Pedoman Organisasi (GBHO & PPUO)
        Route::get('/pedoman-organisasi', [OrganizationGuidelineController::class, 'index'])->name('organization-guidelines.index');
        Route::put('/pedoman-organisasi/gbho', [OrganizationGuidelineController::class, 'updateGbho'])->name('organization-guidelines.update-gbho');
        Route::post('/pedoman-organisasi/strategy', [OrganizationGuidelineController::class, 'storeStrategy'])->name('organization-guidelines.store-strategy');
        Route::put('/pedoman-organisasi/strategy/{strategy}', [OrganizationGuidelineController::class, 'updateStrategy'])->name('organization-guidelines.update-strategy');
        Route::delete('/pedoman-organisasi/strategy/{strategy}', [OrganizationGuidelineController::class, 'destroyStrategy'])->name('organization-guidelines.destroy-strategy');
        Route::post('/pedoman-organisasi/chapter', [OrganizationGuidelineController::class, 'storeChapter'])->name('organization-guidelines.store-chapter');
        Route::put('/pedoman-organisasi/chapter/{chapter}', [OrganizationGuidelineController::class, 'updateChapter'])->name('organization-guidelines.update-chapter');
        Route::delete('/pedoman-organisasi/chapter/{chapter}', [OrganizationGuidelineController::class, 'destroyChapter'])->name('organization-guidelines.destroy-chapter');
        Route::put('/pedoman-organisasi/files', [OrganizationGuidelineController::class, 'updateFilesAndSigners'])->name('organization-guidelines.update-files');

        // Data Pembina
        Route::resource('advisors', AdvisorController::class);

        // Prestasi & Penghargaan Organisasi
        Route::resource('prestasi', AchievementController::class)->names('prestasi');
        Route::post('/prestasi/{id}/toggle-featured', [AchievementController::class, 'toggleFeatured'])->name('prestasi.toggle-featured');

        // Pendaftar Calon Anggota Baru
        Route::get('/member-registrations', [MemberRegistrationController::class, 'index'])->name('member-registrations.index');
        Route::get('/calon-anggota', [MemberRegistrationController::class, 'index']);
        Route::patch('/member-registrations/{id}/status', [MemberRegistrationController::class, 'updateStatus'])->name('member-registrations.update-status');
        Route::delete('/member-registrations/{id}', [MemberRegistrationController::class, 'destroy'])->name('member-registrations.destroy');
        Route::post('/member-registrations/{id}/promote', [MemberRegistrationController::class, 'promoteToMember'])->name('member-registrations.promote');

        // Data Anggota
        Route::delete('/members/destroy-multiple', [MemberController::class, 'destroyMultiple'])->name('members.destroy-multiple');
        Route::get('/members/export', [MemberController::class, 'export'])->name('members.export');
        Route::get('/members/template', [MemberController::class, 'downloadTemplate'])->name('members.template');
        Route::post('/members/import', [MemberController::class, 'import'])->name('members.import');
        Route::get('/members/lookup', [MemberController::class, 'lookup'])->name('members.lookup');
        Route::resource('members', MemberController::class);

        // Donor Darah
        Route::resource('blood-stocks', BloodStockController::class);
        Route::resource('blood-donation-events', BloodDonationEventController::class);
        Route::resource('blood-donor-registrations', BloodDonorRegistrationController::class);

        // Galeri & Kategori Galeri
        Route::post('gallery/categories/quick-store', [GalleryCategoryController::class, 'quickStore'])->name('gallery-categories.quick-store');
        Route::resource('gallery/categories', GalleryCategoryController::class)->names('gallery-categories')->parameters([
            'categories' => 'category',
        ]);
        Route::resource('gallery', GalleryController::class);

        // Manajemen Lomba (SUA BHAKTI BERKARYA)
        // Database & Riwayat Event Lomba (CRUD & History)
        Route::resource('competition-events', CompetitionEventController::class)->names([
            'index' => 'competition-event.index',
            'create' => 'competition-event.create',
            'store' => 'competition-event.store',
            'show' => 'competition-event.show',
            'edit' => 'competition-event.edit',
            'update' => 'competition-event.update',
            'destroy' => 'competition-event.destroy',
        ])->parameters([
            'competition-events' => 'event',
        ]);
        Route::post('/competition-events/{event}/activate', [CompetitionEventController::class, 'activate'])->name('competition-event.activate');
        Route::post('/competition-events/{event}/categories', [CompetitionEventController::class, 'storeCategory'])->name('competition-event.categories.store');
        Route::put('/competition-events/{event}/categories/{category}', [CompetitionEventController::class, 'updateCategory'])->name('competition-event.categories.update');
        Route::delete('/competition-events/{event}/categories/{category}', [CompetitionEventController::class, 'destroyCategory'])->name('competition-event.categories.destroy');
        Route::post('/competition-events/{event}/categories-preset', [CompetitionEventController::class, 'addPresetCategories'])->name('competition-event.categories.preset');
        Route::get('/competition-event', function () {
            return redirect()->route('admin.competition-event.index');
        });

        Route::resource('competition-registrations', CompetitionRegistrationController::class)->parameters([
            'competition-registrations' => 'registration',
        ]);
        Route::get('/competition-registrations/{registration}/proof', [CompetitionRegistrationController::class, 'proof'])->name('competition-registrations.proof');
        Route::post('/competition-registrations/{registration}/verify', [CompetitionRegistrationController::class, 'verify'])->name('competition-registrations.verify');
        Route::post('/competition-registrations/{registration}/reject', [CompetitionRegistrationController::class, 'reject'])->name('competition-registrations.reject');
        Route::post('/competition-registrations/{registration}/resend-email', [CompetitionRegistrationController::class, 'resendEmail'])->name('competition-registrations.resend-email');

        // Daftar Ulang / Check-In Peserta Lomba (Hari-H)
        Route::get('/competition-checkin', [CompetitionCheckinController::class, 'index'])->name('competition-checkin.index');
        Route::get('/competition-checkin/lookup', [CompetitionCheckinController::class, 'lookup'])->name('competition-checkin.lookup');
        Route::post('/competition-checkin/process', [CompetitionCheckinController::class, 'process'])->name('competition-checkin.process');
        Route::post('/competition-checkin/{id}/cancel', [CompetitionCheckinController::class, 'cancel'])->name('competition-checkin.cancel');

        // Daftar Peserta & Regu Lomba Terverifikasi
        Route::get('/competition-participants', [CompetitionParticipantController::class, 'index'])->name('competition-participants.index');
        Route::post('/competition-participants/{team}', [CompetitionParticipantController::class, 'update'])->name('competition-participants.update');
        Route::post('/competition-participants-bulk-orders', [CompetitionParticipantController::class, 'bulkUpdateOrders'])->name('competition-participants.bulk-update-orders');
        Route::post('/competition-participants-auto-orders', [CompetitionParticipantController::class, 'autoAssignOrders'])->name('competition-participants.auto-assign-orders');
        Route::get('/competition-participants-print', [CompetitionParticipantController::class, 'printSheet'])->name('competition-participants.print');

        // Penilaian Lomba (Input Nilai Juri / Panitia)
        Route::get('/competition-scores', [CompetitionScoreController::class, 'index'])->name('competition-scores.index');
        Route::get('/competition-scores/{category}/input', [CompetitionScoreController::class, 'input'])->name('competition-scores.input');
        Route::post('/competition-scores/{category}/save', [CompetitionScoreController::class, 'saveScores'])->name('competition-scores.save');
        Route::post('/competition-scores/{category}/reset', [CompetitionScoreController::class, 'resetScores'])->name('competition-scores.reset');
        Route::post('/competition-scores/{category}/assign-termins', [CompetitionScoreController::class, 'assignTermins'])->name('competition-scores.assign-termins');
        Route::post('/competition-scores/{category}/quick-add-team', [CompetitionScoreController::class, 'quickAddTeam'])->name('competition-scores.quick-add-team');
        Route::delete('/competition-scores/{category}/teams/{team}', [CompetitionScoreController::class, 'removeTeam'])->name('competition-scores.remove-team');

        // Manajemen Akun Juri & Pemetaan Lomba
        Route::resource('competition-juries', CompetitionJuryController::class)->names('competition-juries');

        // Setup Biaya Pendaftaran Lomba
        Route::get('/competition-fees', [CompetitionFeeController::class, 'index'])->name('competition-fees.index');
        Route::post('/competition-fees', [CompetitionFeeController::class, 'update'])->name('competition-fees.update');

        // Setup Tujuan Transfer Rekening Lomba Per Tingkat (Mula, Madya, Wira)
        Route::get('/competition-bank-accounts', [CompetitionBankAccountController::class, 'index'])->name('competition-bank-accounts.index');
        Route::post('/competition-bank-accounts', [CompetitionBankAccountController::class, 'update'])->name('competition-bank-accounts.update');
        Route::get('/competition-bank-account', fn () => redirect()->route('admin.competition-bank-accounts.index'));
        Route::get('/rekening-lomba', fn () => redirect()->route('admin.competition-bank-accounts.index'));

        // Rekap Juara Umum & Klasemen
        Route::get('/competition-leaderboard', [CompetitionLeaderboardController::class, 'index'])->name('competition-leaderboard.index');

        // Siaran Email / Informasi Kegiatan & Undangan Lomba
        Route::get('/competition-broadcast', [CompetitionBroadcastController::class, 'index'])->name('competition-broadcast.index');
        Route::get('/competition-broadcast/create', [CompetitionBroadcastController::class, 'create'])->name('competition-broadcast.create');
        Route::post('/competition-broadcast/send', [CompetitionBroadcastController::class, 'send'])->name('competition-broadcast.send');
        Route::post('/competition-broadcast/upload-image', [CompetitionBroadcastController::class, 'uploadImage'])->name('competition-broadcast.upload-image');
        Route::get('/competition-broadcast/{id}', [CompetitionBroadcastController::class, 'show'])->name('competition-broadcast.show');
        Route::delete('/competition-broadcast/{id}', [CompetitionBroadcastController::class, 'destroy'])->name('competition-broadcast.destroy');

        // Pengelolaan Menu Informasi Lomba (CRUD Sub Menu & Dokumen Berkas)
        Route::resource('competition-info-menus', CompetitionInfoMenuController::class)->names('competition-info-menus');
        Route::post('/competition-info-menus/{id}/toggle-active', [CompetitionInfoMenuController::class, 'toggleActive'])->name('competition-info-menus.toggle-active');
        Route::post('/competition-info-menus/{id}/quick-upload', [CompetitionInfoMenuController::class, 'quickUpload'])->name('competition-info-menus.quick-upload');
        Route::post('/competition-info-menus-reset-defaults', [CompetitionInfoMenuController::class, 'resetDefaults'])->name('competition-info-menus.reset-defaults');

        // Otak & Pengetahuan Wira Agent AI (Sinkronisasi Konten Website)
        Route::post('/wira-agent/sync-knowledge', [WiraAgentKnowledgeController::class, 'sync'])->name('wira-agent.sync-knowledge');
        Route::get('/wira-agent/knowledge-status', [WiraAgentKnowledgeController::class, 'status'])->name('wira-agent.knowledge-status');

        // System Utility: Bersihkan Cache & Sinkronisasi Server
        Route::get('/clear-cache', function () {
            try {
                Artisan::call('optimize:clear');
                Artisan::call('migrate', ['--force' => true]);
                $output = Artisan::output();

                return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;'>
                    <h2 style='color:#059669;'>✅ Cache Berhasil Dibersihkan & Migrasi Selesai!</h2>
                    <pre style='background:#f1f5f9;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>".htmlspecialchars($output)."</pre>
                    <p><a href='".route('admin.competition-fees.index')."' style='display:inline-block;padding:10px 20px;background:#dc2626;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>Buka Halaman Setup Biaya &rarr;</a></p>
                </div>");
            } catch (Throwable $e) {
                return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;color:#dc2626;'>
                    <h2>❌ Terjadi Kendala:</h2>
                    <pre style='background:#fef2f2;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>".htmlspecialchars($e->getMessage()).'</pre>
                </div>', 500);
            }
        })->name('clear-cache');
    });
});

/*
|--------------------------------------------------------------------------
| Client Area (Area Biodata Pribadi Anggota PMR Wira)
|--------------------------------------------------------------------------
*/
Route::prefix('member')->name('member.')->middleware('auth')->group(function () {
    Route::get('/profile', [MemberAreaController::class, 'profile'])->name('profile');
    Route::put('/profile', [MemberAreaController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [MemberAreaController::class, 'updatePassword'])->name('password.update');
});

// Endpoint Darurat Sinkronisasi Server (Bisa diakses langsung jika cache routing terkunci)
Route::get('/server-sync-update-pmr', function () {
    try {
        $gitOutput = 'N/A';
        try {
            if (function_exists('shell_exec')) {
                $gitOutput = (string) shell_exec('git pull origin main 2>&1');
            }
        } catch (Throwable $e) {
            $gitOutput = 'Git pull skipped: '.$e->getMessage();
        }

        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');

        $migrationOutput = '';
        try {
            Artisan::call('migrate', ['--force' => true]);
            $migrationOutput = Artisan::output();
        } catch (Throwable $e) {
            $migrationOutput = 'Migration notice/error: '.$e->getMessage();
        }

        try {
            JuryPortalController::ensureTablesExist();
        } catch (Throwable $e) {
            Log::warning('JuryPortalController ensureTablesExist error: '.$e->getMessage());
        }

        if (Schema::hasTable('uks_hotlines')) {
            DB::table('uks_hotlines')->where('phone_number', 'like', '%857%')->orWhereNull('phone_number')->update(['phone_number' => '083872506817']);
        }

        try {
            BackgroundMusicController::ensureTableExists();
        } catch (Throwable $e) {
            // Ignore
        }

        // Seed data prestasi jika tabel kosong
        if (Schema::hasTable('achievements') && DB::table('achievements')->count() === 0) {
            try {
                Artisan::call('db:seed', ['--class' => 'AchievementSeeder', '--force' => true]);
            } catch (Throwable $e) {
                // Ignore seeder error if table already populated
            }
        }

        // Pastikan Tingkat 1 (Pembina PMR) selalu terdaftar di tabel organization_members
        if (Schema::hasTable('organization_members') && ! DB::table('organization_members')->where('level', 1)->exists()) {
            $advisorName = 'Drs. H. Mulyadi, M.Pd';
            $advisorPhoto = null;
            if (Schema::hasTable('advisors')) {
                $adv = DB::table('advisors')->where('is_active', 1)->orderBy('order_position')->first();
                if ($adv) {
                    $advisorName = $adv->name;
                    $advisorPhoto = $adv->photo;
                }
            }
            DB::table('organization_members')->insert([
                'name' => $advisorName,
                'position' => 'Pembina PMR',
                'subtitle' => 'Guru Pembina PMR',
                'level' => 1,
                'order_position' => 1,
                'photo' => $advisorPhoto,
                'icon' => 'fa-solid fa-shield-halved',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Pastikan folder storage publik dibuat
        File::ensureDirectoryExists(storage_path('app/public/advisors'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/members'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/competition_payments'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/competition_team_files'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/competition_evidences'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/guidelines'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/achievements/trophies'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/achievements/gallery'), 0755, true);
        File::ensureDirectoryExists(storage_path('app/public/achievements/certificates'), 0755, true);

        try {
            Artisan::call('storage:link');
        } catch (Throwable $e) {
            // Ignore if already linked or permission denied
        }

        // Salin darurat jika symlink tidak bekerja di shared hosting cPanel
        if (file_exists(public_path()) && ! is_link(public_path('storage'))) {
            File::ensureDirectoryExists(public_path('storage/advisors'), 0755, true);
            File::copyDirectory(storage_path('app/public/advisors'), public_path('storage/advisors'));
            File::ensureDirectoryExists(public_path('storage/members'), 0755, true);
            File::copyDirectory(storage_path('app/public/members'), public_path('storage/members'));
            File::ensureDirectoryExists(public_path('storage/achievements'), 0755, true);
            File::copyDirectory(storage_path('app/public/achievements'), public_path('storage/achievements'));
            File::ensureDirectoryExists(public_path('storage/competition_payments'), 0755, true);
            File::copyDirectory(storage_path('app/public/competition_payments'), public_path('storage/competition_payments'));
            File::ensureDirectoryExists(public_path('storage/competition_team_files'), 0755, true);
            File::copyDirectory(storage_path('app/public/competition_team_files'), public_path('storage/competition_team_files'));
            File::ensureDirectoryExists(public_path('storage/competition_evidences'), 0755, true);
            File::copyDirectory(storage_path('app/public/competition_evidences'), public_path('storage/competition_evidences'));
            File::ensureDirectoryExists(public_path('storage/hero-slides'), 0755, true);
            File::copyDirectory(storage_path('app/public/hero-slides'), public_path('storage/hero-slides'));
            File::ensureDirectoryExists(public_path('storage/popup-banners'), 0755, true);
            File::copyDirectory(storage_path('app/public/popup-banners'), public_path('storage/popup-banners'));
        }

        // Sinkronisasi otomatis struktur bagan kepengurusan dari Data Anggota & Pembina
        try {
            OrganizationSyncService::syncAll();
        } catch (Throwable $e) {
            Log::warning('Organization sync error on server-sync-update: '.$e->getMessage());
        }

        $output = Artisan::output();
        $juryTablesStatus = [
            'jury_category_assignments' => Schema::hasTable('jury_category_assignments') ? 'OK' : 'MISSING',
            'competition_score_photos' => Schema::hasTable('competition_score_photos') ? 'OK' : 'MISSING',
            'users.role' => (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) ? 'OK' : 'MISSING',
            'competition_scores.jury_user_id' => (Schema::hasTable('competition_scores') && Schema::hasColumn('competition_scores', 'jury_user_id')) ? 'OK' : 'MISSING',
        ];

        return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;'>
            <h2 style='color:#059669;'>✅ Cache Server Berhasil Dibersihkan, Database Dimigrasi & Storage Symlink Disinkronkan!</h2>
            <div style='margin-bottom:10px;'><strong>Git Pull:</strong> <pre style='background:#f1f5f9;padding:10px;border-radius:8px;font-size:12px;overflow:auto;'>".htmlspecialchars($gitOutput)."</pre></div>
            <div style='margin-bottom:10px;'><strong>Migration Output:</strong> <pre style='background:#f1f5f9;padding:10px;border-radius:8px;font-size:12px;overflow:auto;'>".htmlspecialchars($migrationOutput ?: '(No new migrations) - Status: '.json_encode($juryTablesStatus))."</pre></div>
            <div><strong>Artisan Output:</strong> <pre style='background:#f1f5f9;padding:10px;border-radius:8px;font-size:12px;overflow:auto;'>".htmlspecialchars($output)."</pre></div>
            <p><a href='/juri/dashboard' style='display:inline-block;padding:10px 20px;background:#dc2626;color:white;text-decoration:none;border-radius:8px;font-weight:bold;margin-right:10px;'>Buka Dashboard Juri &rarr;</a>
               <a href='/admin/competition-juries' style='display:inline-block;padding:10px 20px;background:#f59e0b;color:#1e293b;text-decoration:none;border-radius:8px;font-weight:bold;'>Kelola Akun Juri &rarr;</a></p>
        </div>");
    } catch (Throwable $e) {
        return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;color:#dc2626;'>
            <h2>❌ Terjadi Kendala:</h2>
            <pre style='background:#fef2f2;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>".htmlspecialchars($e->getMessage()).'</pre>
        </div>', 500);
    }
});

// Route Penyajian Langsung Berkas Storage (Anti 404 jika shared hosting / cPanel memblokir symlink)
Route::get('/storage/{path}', function ($path) {
    $cleanPath = ltrim(str_replace(['../', '..\\'], '', $path), '/');
    $firstFolder = explode('/', $cleanPath)[0];
    $allowedFolders = [
        'advisors', 'members', 'competition_payments', 'competition_team_files', 'competition_evidences', 'galleries',
        'donor-banners', 'broadcast_images', 'guidelines', 'achievements',
        'hero-slides', 'popup-banners', 'media',
    ];
    if (! in_array($firstFolder, $allowedFolders, true)) {
        abort(404);
    }

    $candidatePaths = [
        storage_path("app/public/{$cleanPath}"),
        public_path("storage/{$cleanPath}"),
        public_path($cleanPath),
        storage_path("app/{$cleanPath}"),
    ];

    $resolvedPath = null;
    foreach ($candidatePaths as $candidate) {
        if (file_exists($candidate) && is_file($candidate)) {
            $resolvedPath = $candidate;
            break;
        }
    }

    if (! $resolvedPath) {
        abort(404);
    }

    $mime = mime_content_type($resolvedPath) ?: 'application/octet-stream';

    return response()->file($resolvedPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');

// Rute Publik Lomba PMR
Route::prefix('lomba')->name('lomba.')->group(function () {
    Route::get('/', [CompetitionController::class, 'index'])->name('index');
    Route::get('/daftar', [CompetitionController::class, 'register'])->name('register');
    Route::post('/daftar', [CompetitionController::class, 'store'])->name('store');
    Route::get('/template-peserta', [CompetitionController::class, 'downloadParticipantTemplate'])->name('template-peserta');
    Route::get('/template-peserta-regu', [CompetitionController::class, 'downloadParticipantTemplate']);
    Route::post('/parse-excel-peserta', [CompetitionController::class, 'parseExcelPeserta'])->name('parse-excel-peserta');
    Route::post('/download-mass-template', [CompetitionController::class, 'downloadMassParticipantTemplate'])->name('download-mass-template');
    Route::post('/parse-mass-excel', [CompetitionController::class, 'parseMassExcelPeserta'])->name('parse-mass-excel');
    Route::get('/status', [CompetitionController::class, 'status'])->name('status');
    Route::get('/kwitansi/{code}', [CompetitionController::class, 'receipt'])->name('receipt');
    Route::get('/kwitansi/{code}', [CompetitionController::class, 'receipt'])->name('kwitansi');
    Route::get('/kartu-peserta/{code}', [CompetitionController::class, 'participantCards'])->name('cards');
    Route::get('/kartu-peserta/{code}', [CompetitionController::class, 'participantCards'])->name('kartu-peserta');
    Route::get('/live-scoreboard', [CompetitionController::class, 'liveScoreboard'])->name('scoreboard');
});

// ================= PORTAL PENILAIAN JURI LAPANGAN (MOBILE-FIRST) =================
Route::get('/juri/login', [JuryPortalController::class, 'showLogin'])->name('jury.login');
Route::post('/juri/login', [JuryPortalController::class, 'login'])->name('jury.login.submit');
Route::post('/juri/logout', [JuryPortalController::class, 'logout'])->name('jury.logout');

Route::middleware(['auth'])->prefix('juri')->name('jury.')->group(function () {
    Route::get('/', [JuryPortalController::class, 'dashboard'])->name('index');
    Route::get('/dashboard', [JuryPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/kategori/{category}', [JuryPortalController::class, 'scoring'])->name('scoring');
    Route::post('/kategori/{category}/regu/{team}/simpan', [JuryPortalController::class, 'saveScore'])->name('save-score');
    Route::post('/upload-foto', [JuryPortalController::class, 'uploadPhoto'])->name('upload-photo');
    Route::delete('/foto/{photo}', [JuryPortalController::class, 'deletePhoto'])->name('delete-photo');
});

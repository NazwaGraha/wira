<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BloodDonationEventController;
use App\Http\Controllers\Admin\BloodDonorRegistrationController;
use App\Http\Controllers\Admin\BloodStockController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompetitionBroadcastController;
use App\Http\Controllers\Admin\CompetitionCheckinController;
use App\Http\Controllers\Admin\CompetitionEventController;
use App\Http\Controllers\Admin\CompetitionFeeController;
use App\Http\Controllers\Admin\CompetitionInfoMenuController;
use App\Http\Controllers\Admin\CompetitionLeaderboardController;
use App\Http\Controllers\Admin\CompetitionParticipantController;
use App\Http\Controllers\Admin\CompetitionRegistrationController;
use App\Http\Controllers\Admin\CompetitionScoreController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryCategoryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationGuidelineController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\MemberAreaController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Artisan;
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
Route::post('/daftar-anggota', [PageController::class, 'storeRegistration'])->name('daftar-anggota.store');
Route::post('/donor-darah/daftar', [PageController::class, 'storeDonorRegistration'])->name('donor-darah.store');

// Artikel Publik
Route::get('/artikel', [ArticleController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('artikel.show');

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

        // Pengaturan Slider Hero Banner
        Route::resource('hero-slides', HeroSlideController::class)->names([
            'index' => 'hero-slides.index',
            'create' => 'hero-slides.create',
            'store' => 'hero-slides.store',
            'edit' => 'hero-slides.edit',
            'update' => 'hero-slides.update',
            'destroy' => 'hero-slides.destroy',
        ]);

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

        // Data Anggota
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

        // Setup Biaya Pendaftaran Lomba
        Route::get('/competition-fees', [CompetitionFeeController::class, 'index'])->name('competition-fees.index');
        Route::post('/competition-fees', [CompetitionFeeController::class, 'update'])->name('competition-fees.update');

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
        Artisan::call('optimize:clear');
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;'>
            <h2 style='color:#059669;'>✅ Cache Server Berhasil Dibersihkan & Database Dimigrasi!</h2>
            <pre style='background:#f1f5f9;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>".htmlspecialchars($output)."</pre>
            <p><a href='/admin/competition-fees' style='display:inline-block;padding:10px 20px;background:#dc2626;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>Buka Halaman Setup Biaya &rarr;</a></p>
        </div>");
    } catch (Throwable $e) {
        return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;color:#dc2626;'>
            <h2>❌ Terjadi Kendala:</h2>
            <pre style='background:#fef2f2;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>".htmlspecialchars($e->getMessage()).'</pre>
        </div>', 500);
    }
});

// Rute Publik Lomba PMR
Route::prefix('lomba')->name('lomba.')->group(function () {
    Route::get('/', [CompetitionController::class, 'index'])->name('index');
    Route::get('/daftar', [CompetitionController::class, 'register'])->name('register');
    Route::post('/daftar', [CompetitionController::class, 'store'])->name('store');
    Route::get('/status', [CompetitionController::class, 'status'])->name('status');
    Route::get('/kwitansi/{code}', [CompetitionController::class, 'receipt'])->name('receipt');
    Route::get('/kwitansi/{code}', [CompetitionController::class, 'receipt'])->name('kwitansi');
    Route::get('/kartu-peserta/{code}', [CompetitionController::class, 'participantCards'])->name('cards');
    Route::get('/kartu-peserta/{code}', [CompetitionController::class, 'participantCards'])->name('kartu-peserta');
    Route::get('/live-scoreboard', [CompetitionController::class, 'liveScoreboard'])->name('scoreboard');
});

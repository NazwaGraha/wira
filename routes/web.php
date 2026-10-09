<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;

/*
|--------------------------------------------------------------------------
| Public Routes (Front-Office PMR WIRA SMAN 1 CIAWI)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/tentang-kami', [PageController::class, 'tentangKami'])->name('tentang-kami');
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

    // Protected Admin Routes
    Route::middleware('auth')->group(function () {
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

        // Pengaturan Slider Hero Banner
        Route::resource('hero-slides', \App\Http\Controllers\Admin\HeroSlideController::class)->names([
            'index' => 'hero-slides.index',
            'create' => 'hero-slides.create',
            'store' => 'hero-slides.store',
            'edit' => 'hero-slides.edit',
            'update' => 'hero-slides.update',
            'destroy' => 'hero-slides.destroy',
        ]);

        // Kelola Kegiatan & Agenda
        Route::resource('kegiatan', \App\Http\Controllers\Admin\ActivityController::class)->names([
            'index' => 'activities.index',
            'create' => 'activities.create',
            'store' => 'activities.store',
            'edit' => 'activities.edit',
            'update' => 'activities.update',
            'destroy' => 'activities.destroy',
        ]);

        // Bagan Kepengurusan (Struktur Organisasi)
        Route::get('/bagan-kepengurusan', [\App\Http\Controllers\Admin\OrganizationController::class, 'index'])->name('organization.index');
        Route::put('/bagan-kepengurusan/setting', [\App\Http\Controllers\Admin\OrganizationController::class, 'updateSetting'])->name('organization.setting.update');
        Route::get('/bagan-kepengurusan/create', [\App\Http\Controllers\Admin\OrganizationController::class, 'create'])->name('organization.create');
        Route::post('/bagan-kepengurusan', [\App\Http\Controllers\Admin\OrganizationController::class, 'store'])->name('organization.store');
        Route::get('/bagan-kepengurusan/{member}/edit', [\App\Http\Controllers\Admin\OrganizationController::class, 'edit'])->name('organization.edit');
        Route::put('/bagan-kepengurusan/{member}', [\App\Http\Controllers\Admin\OrganizationController::class, 'update'])->name('organization.update');
        Route::delete('/bagan-kepengurusan/{member}', [\App\Http\Controllers\Admin\OrganizationController::class, 'destroy'])->name('organization.destroy');
        
        // Data Anggota
        Route::resource('members', \App\Http\Controllers\Admin\MemberController::class);

        // Donor Darah
        Route::resource('blood-stocks', \App\Http\Controllers\Admin\BloodStockController::class);
        Route::resource('blood-donation-events', \App\Http\Controllers\Admin\BloodDonationEventController::class);
        Route::resource('blood-donor-registrations', \App\Http\Controllers\Admin\BloodDonorRegistrationController::class);

        // Galeri
        Route::resource('gallery', \App\Http\Controllers\Admin\GalleryController::class);

        // Manajemen Lomba (SUA BHAKTI BERKARYA)
        // Database & Riwayat Event Lomba (CRUD & History)
        Route::resource('competition-events', \App\Http\Controllers\Admin\CompetitionEventController::class)->names([
            'index' => 'competition-event.index',
            'create' => 'competition-event.create',
            'store' => 'competition-event.store',
            'show' => 'competition-event.show',
            'edit' => 'competition-event.edit',
            'update' => 'competition-event.update',
            'destroy' => 'competition-event.destroy',
        ])->parameters([
            'competition-events' => 'event'
        ]);
        Route::post('/competition-events/{event}/activate', [\App\Http\Controllers\Admin\CompetitionEventController::class, 'activate'])->name('competition-event.activate');
        Route::post('/competition-events/{event}/categories', [\App\Http\Controllers\Admin\CompetitionEventController::class, 'storeCategory'])->name('competition-event.categories.store');
        Route::put('/competition-events/{event}/categories/{category}', [\App\Http\Controllers\Admin\CompetitionEventController::class, 'updateCategory'])->name('competition-event.categories.update');
        Route::delete('/competition-events/{event}/categories/{category}', [\App\Http\Controllers\Admin\CompetitionEventController::class, 'destroyCategory'])->name('competition-event.categories.destroy');
        Route::post('/competition-events/{event}/categories-preset', [\App\Http\Controllers\Admin\CompetitionEventController::class, 'addPresetCategories'])->name('competition-event.categories.preset');
        Route::get('/competition-event', function() { return redirect()->route('admin.competition-event.index'); });

        Route::resource('competition-registrations', \App\Http\Controllers\Admin\CompetitionRegistrationController::class)->parameters([
            'competition-registrations' => 'registration'
        ]);
        Route::post('/competition-registrations/{registration}/verify', [\App\Http\Controllers\Admin\CompetitionRegistrationController::class, 'verify'])->name('competition-registrations.verify');
        Route::post('/competition-registrations/{registration}/reject', [\App\Http\Controllers\Admin\CompetitionRegistrationController::class, 'reject'])->name('competition-registrations.reject');
        Route::post('/competition-registrations/{registration}/resend-email', [\App\Http\Controllers\Admin\CompetitionRegistrationController::class, 'resendEmail'])->name('competition-registrations.resend-email');

        // Daftar Ulang / Check-In Peserta Lomba (Hari-H)
        Route::get('/competition-checkin', [\App\Http\Controllers\Admin\CompetitionCheckinController::class, 'index'])->name('competition-checkin.index');
        Route::get('/competition-checkin/lookup', [\App\Http\Controllers\Admin\CompetitionCheckinController::class, 'lookup'])->name('competition-checkin.lookup');
        Route::post('/competition-checkin/process', [\App\Http\Controllers\Admin\CompetitionCheckinController::class, 'process'])->name('competition-checkin.process');
        Route::post('/competition-checkin/{id}/cancel', [\App\Http\Controllers\Admin\CompetitionCheckinController::class, 'cancel'])->name('competition-checkin.cancel');
        
        // Daftar Peserta & Regu Lomba Terverifikasi
        Route::get('/competition-participants', [\App\Http\Controllers\Admin\CompetitionParticipantController::class, 'index'])->name('competition-participants.index');
        Route::post('/competition-participants/{team}', [\App\Http\Controllers\Admin\CompetitionParticipantController::class, 'update'])->name('competition-participants.update');
        Route::post('/competition-participants-bulk-orders', [\App\Http\Controllers\Admin\CompetitionParticipantController::class, 'bulkUpdateOrders'])->name('competition-participants.bulk-update-orders');
        Route::post('/competition-participants-auto-orders', [\App\Http\Controllers\Admin\CompetitionParticipantController::class, 'autoAssignOrders'])->name('competition-participants.auto-assign-orders');
        Route::get('/competition-participants-print', [\App\Http\Controllers\Admin\CompetitionParticipantController::class, 'printSheet'])->name('competition-participants.print');

        // Penilaian Lomba (Input Nilai Juri / Panitia)
        Route::get('/competition-scores', [\App\Http\Controllers\Admin\CompetitionScoreController::class, 'index'])->name('competition-scores.index');
        Route::get('/competition-scores/{category}/input', [\App\Http\Controllers\Admin\CompetitionScoreController::class, 'input'])->name('competition-scores.input');
        Route::post('/competition-scores/{category}/save', [\App\Http\Controllers\Admin\CompetitionScoreController::class, 'saveScores'])->name('competition-scores.save');
        Route::post('/competition-scores/{category}/reset', [\App\Http\Controllers\Admin\CompetitionScoreController::class, 'resetScores'])->name('competition-scores.reset');
        Route::post('/competition-scores/{category}/assign-termins', [\App\Http\Controllers\Admin\CompetitionScoreController::class, 'assignTermins'])->name('competition-scores.assign-termins');
        Route::post('/competition-scores/{category}/quick-add-team', [\App\Http\Controllers\Admin\CompetitionScoreController::class, 'quickAddTeam'])->name('competition-scores.quick-add-team');
        Route::delete('/competition-scores/{category}/teams/{team}', [\App\Http\Controllers\Admin\CompetitionScoreController::class, 'removeTeam'])->name('competition-scores.remove-team');

        // Setup Biaya Pendaftaran Lomba
        Route::get('/competition-fees', [\App\Http\Controllers\Admin\CompetitionFeeController::class, 'index'])->name('competition-fees.index');
        Route::post('/competition-fees', [\App\Http\Controllers\Admin\CompetitionFeeController::class, 'update'])->name('competition-fees.update');

        // Rekap Juara Umum & Klasemen
        Route::get('/competition-leaderboard', [\App\Http\Controllers\Admin\CompetitionLeaderboardController::class, 'index'])->name('competition-leaderboard.index');

        // Siaran Email / Informasi Kegiatan & Undangan Lomba
        Route::get('/competition-broadcast', [\App\Http\Controllers\Admin\CompetitionBroadcastController::class, 'index'])->name('competition-broadcast.index');
        Route::get('/competition-broadcast/create', [\App\Http\Controllers\Admin\CompetitionBroadcastController::class, 'create'])->name('competition-broadcast.create');
        Route::post('/competition-broadcast/send', [\App\Http\Controllers\Admin\CompetitionBroadcastController::class, 'send'])->name('competition-broadcast.send');
        Route::post('/competition-broadcast/upload-image', [\App\Http\Controllers\Admin\CompetitionBroadcastController::class, 'uploadImage'])->name('competition-broadcast.upload-image');
        Route::get('/competition-broadcast/{id}', [\App\Http\Controllers\Admin\CompetitionBroadcastController::class, 'show'])->name('competition-broadcast.show');
        Route::delete('/competition-broadcast/{id}', [\App\Http\Controllers\Admin\CompetitionBroadcastController::class, 'destroy'])->name('competition-broadcast.destroy');

        // Pengelolaan Menu Informasi Lomba (CRUD Sub Menu & Dokumen Berkas)
        Route::resource('competition-info-menus', \App\Http\Controllers\Admin\CompetitionInfoMenuController::class)->names('competition-info-menus');
        Route::post('/competition-info-menus/{id}/toggle-active', [\App\Http\Controllers\Admin\CompetitionInfoMenuController::class, 'toggleActive'])->name('competition-info-menus.toggle-active');
        Route::post('/competition-info-menus/{id}/quick-upload', [\App\Http\Controllers\Admin\CompetitionInfoMenuController::class, 'quickUpload'])->name('competition-info-menus.quick-upload');
        Route::post('/competition-info-menus-reset-defaults', [\App\Http\Controllers\Admin\CompetitionInfoMenuController::class, 'resetDefaults'])->name('competition-info-menus.reset-defaults');

        // System Utility: Bersihkan Cache & Sinkronisasi Server
        Route::get('/clear-cache', function () {
            try {
                \Illuminate\Support\Facades\Artisan::call('optimize:clear');
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                $output = \Illuminate\Support\Facades\Artisan::output();
                return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;'>
                    <h2 style='color:#059669;'>✅ Cache Berhasil Dibersihkan & Migrasi Selesai!</h2>
                    <pre style='background:#f1f5f9;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>" . htmlspecialchars($output) . "</pre>
                    <p><a href='" . route('admin.competition-fees.index') . "' style='display:inline-block;padding:10px 20px;background:#dc2626;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>Buka Halaman Setup Biaya &rarr;</a></p>
                </div>");
            } catch (\Throwable $e) {
                return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;color:#dc2626;'>
                    <h2>❌ Terjadi Kendala:</h2>
                    <pre style='background:#fef2f2;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>" . htmlspecialchars($e->getMessage()) . "</pre>
                </div>", 500);
            }
        })->name('clear-cache');
    });
});

// Endpoint Darurat Sinkronisasi Server (Bisa diakses langsung jika cache routing terkunci)
Route::get('/server-sync-update-pmr', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;'>
            <h2 style='color:#059669;'>✅ Cache Server Berhasil Dibersihkan & Database Dimigrasi!</h2>
            <pre style='background:#f1f5f9;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>" . htmlspecialchars($output) . "</pre>
            <p><a href='/admin/competition-fees' style='display:inline-block;padding:10px 20px;background:#dc2626;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>Buka Halaman Setup Biaya &rarr;</a></p>
        </div>");
    } catch (\Throwable $e) {
        return response("<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:auto;color:#dc2626;'>
            <h2>❌ Terjadi Kendala:</h2>
            <pre style='background:#fef2f2;padding:15px;border-radius:10px;font-size:12px;overflow:auto;'>" . htmlspecialchars($e->getMessage()) . "</pre>
        </div>", 500);
    }
});

// Rute Publik Lomba PMR
Route::prefix('lomba')->name('lomba.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CompetitionController::class, 'index'])->name('index');
    Route::get('/daftar', [\App\Http\Controllers\CompetitionController::class, 'register'])->name('register');
    Route::post('/daftar', [\App\Http\Controllers\CompetitionController::class, 'store'])->name('store');
    Route::get('/status', [\App\Http\Controllers\CompetitionController::class, 'status'])->name('status');
    Route::get('/kwitansi/{code}', [\App\Http\Controllers\CompetitionController::class, 'receipt'])->name('receipt');
    Route::get('/kwitansi/{code}', [\App\Http\Controllers\CompetitionController::class, 'receipt'])->name('kwitansi');
    Route::get('/kartu-peserta/{code}', [\App\Http\Controllers\CompetitionController::class, 'participantCards'])->name('cards');
    Route::get('/kartu-peserta/{code}', [\App\Http\Controllers\CompetitionController::class, 'participantCards'])->name('kartu-peserta');
    Route::get('/live-scoreboard', [\App\Http\Controllers\CompetitionController::class, 'liveScoreboard'])->name('scoreboard');
});

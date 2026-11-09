<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Jadwal Otomatis Sinkronisasi Pengetahuan Wira Agent AI
 * Dijalankan setiap hari tepat pukul 24.00 WIB (00:00 Asia/Jakarta)
 * agar otak Wira Agent terus menyerap pembaruan data website secara otomatis.
 */
Schedule::command('wira:sync-knowledge')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/wira-knowledge-sync.log'))
    ->runInBackground();

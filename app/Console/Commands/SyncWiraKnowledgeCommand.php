<?php

namespace App\Console\Commands;

use App\Services\WiraKnowledgeSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncWiraKnowledgeCommand extends Command
{
    /**
     * Nama dan signature perintah console.
     *
     * @var string
     */
    protected $signature = 'wira:sync-knowledge';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Kompilasi dan sinkronkan seluruh isi konten website pmrwirasmanic.or.id ke dalam basis pengetahuan Wira Agent';

    /**
     * Eksekusi perintah console.
     */
    public function handle(WiraKnowledgeSyncService $syncService): int
    {
        $this->info('🧠 Memulai sinkronisasi seluruh konten website untuk Wira Agent...');

        try {
            $result = $syncService->syncAll();

            $this->table(
                ['Metrik Pengetahuan', 'Nilai'],
                [
                    ['Total Topik Terindeks', $result['total_topics']],
                    ['Artikel Terbit Diserap', $result['articles_count']],
                    ['Waktu Sinkronisasi', $result['synced_at']],
                    ['Lokasi Berkas Knowledge', $result['file_path']],
                ]
            );

            $this->info('✅ Pengetahuan Wira Agent berhasil disinkronkan dari seluruh konten website!');
            Log::info("Wira Agent automatic knowledge sync completed successfully at {$result['synced_at']}. Total topics: {$result['total_topics']}, Articles: {$result['articles_count']}.");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Terjadi kesalahan saat sinkronisasi pengetahuan: '.$e->getMessage());
            Log::error('Wira Agent knowledge sync failed: '.$e->getMessage(), ['exception' => $e]);

            return Command::FAILURE;
        }
    }
}

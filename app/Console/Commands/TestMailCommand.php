<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Alamat email penerima uji coba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji coba pengiriman email SMTP dan diagnosa status koneksi email secara langsung';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = $this->argument('email') ?: config('mail.from.address') ?: 'pmrsman1ciawibogor@gmail.com';

        $this->info('========================================================');
        $this->info('  DIAGNOSA KONEKSI EMAIL (SMTP) PMR WIRA SMAN 1 CIAWI  ');
        $this->info('========================================================');

        $driver = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $encryption = config('mail.mailers.smtp.encryption');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->table(
            ['Parameter', 'Nilai Saat Ini'],
            [
                ['MAIL_MAILER', $driver],
                ['MAIL_HOST', $host],
                ['MAIL_PORT', $port],
                ['MAIL_ENCRYPTION', $encryption ?: '(null)'],
                ['MAIL_USERNAME', $username ?: '(kosong)'],
                ['MAIL_PASSWORD', config('mail.mailers.smtp.password') ? '****** (terisi)' : '(kosong)'],
                ['MAIL_FROM_ADDRESS', $fromAddress],
                ['MAIL_FROM_NAME', $fromName],
                ['Target Penerima Test', $recipient],
            ]
        );

        if ($driver === 'log') {
            $this->warn('⚠️ PERHATIAN: MAIL_MAILER Anda masih disetel ke "log".');
            $this->warn('Email hanya akan dicatat di storage/logs/laravel.log dan TIDAK TERKIRIM ke internet!');
            $this->warn('Silakan ubah MAIL_MAILER=smtp di file .env server Anda.');
            return Command::FAILURE;
        }

        $this->info("\n⏳ Sedang mencoba mengirim email uji coba ke {$recipient}...");

        try {
            Mail::raw("Halo!\n\nIni adalah email uji coba dari Portal PMR Wira SMAN 1 Ciawi.\nJika email ini sampai di kotak masuk Anda, maka konfigurasi SMTP pengiriman email telah BERHASIL dan SIAP digunakan untuk notifikasi pendaftaran lomba beserta lampiran PDF e-Kwitansi & Kartu Peserta.\n\nWaktu Kirim: " . date('d F Y, H:i:s') . " WIB\nWebsite: https://pmrwirasmanic.or.id", function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('✅ Uji Coba Pengiriman Email Berhasil - PMR WIRA SMAN 1 CIAWI');
            });

            $this->info("\n🎉 BERHASIL! Email uji coba berhasil dikirim ke: {$recipient}");
            $this->info("Silakan cek kotak masuk (inbox) atau folder spam pada email tersebut.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("\n❌ GAGAL MENGIRIM EMAIL!");
            $this->error("Pesan Error: " . $e->getMessage());

            $this->line("\n--------------------------------------------------------");
            $this->warn("ANALISA & SOLUSI CEPAT:");

            if (str_contains($e->getMessage(), '535') || str_contains($e->getMessage(), 'Username and Password not accepted') || str_contains($e->getMessage(), 'BadCredentials')) {
                $this->line("1. 🔑 MASALAH GMAIL APP PASSWORD:");
                $this->line("   Google/Gmail melarang penggunaan password login biasa untuk SMTP.");
                $this->line("   Wajib membuat 'Sandi Aplikasi (App Password)' 16 digit:");
                $this->line("   a. Buka https://myaccount.google.com/apppasswords");
                $this->line("   b. Beri nama aplikasi 'Website PMR' lalu generate.");
                $this->line("   c. Salin 16 huruf yang muncul (misal: 'abcd efgh ijkl mnop').");
                $this->line("   d. Tempelkan ke MAIL_PASSWORD=\"abcdefghijklmnop\" di file .env.");
            } elseif (str_contains($e->getMessage(), 'Connection timed out') || str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'Operation timed out')) {
                $this->line("2. 🔌 MASALAH PORT SMTP TERBLOKIR FIREWALL HOSTING:");
                $this->line("   Banyak shared hosting/cPanel memblokir port 587 keluar.");
                $this->line("   Solusi: Coba ubah di .env:");
                $this->line("   MAIL_PORT=465");
                $this->line("   MAIL_ENCRYPTION=ssl");
                $this->line("   Atau gunakan Webmail cPanel sekolah (misal: mail.pmrwirasmanic.or.id).");
            } else {
                $this->line("3. 💡 Silakan pastikan kredensial di file .env sudah benar lalu jalankan: php artisan optimize:clear.");
            }
            $this->line("--------------------------------------------------------\n");

            return Command::FAILURE;
        }
    }
}

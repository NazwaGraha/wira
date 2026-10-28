<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('content_section_images')) {
            Schema::table('content_section_images', function (Blueprint $table) {
                if (! Schema::hasColumn('content_section_images', 'cards_data')) {
                    $table->json('cards_data')->nullable()->after('checklist_items');
                }
            });

            // Pastikan data default section 'home_pillars' terisi persis seperti tampilan beranda saat ini
            $homePillars = DB::table('content_section_images')->where('section_key', 'home_pillars')->first();
            $defaultPrograms = json_encode([
                [
                    'icon' => 'fa-solid fa-droplet',
                    'title' => 'Donor Darah Sukarela',
                    'description' => 'Edukasi pentingnya donor darah bagi remaja usia 17 tahun ke atas serta aksi donor darah berkala bekerja sama dengan PMI Kabupaten Bogor.',
                    'button_text' => 'Cek Stok & Jadwal',
                    'button_url' => '/donor-darah',
                ],
                [
                    'icon' => 'fa-solid fa-kit-medical',
                    'title' => 'Pertolongan Pertama (PP)',
                    'description' => 'Pelatihan intensif pembalutan, pembidaian, RJP/CPR, dan evakuasi korban untuk memastikan keselamatan di setiap kegiatan sekolah.',
                    'button_text' => 'Lihat Modul & Kegiatan',
                    'button_url' => '/kegiatan',
                ],
                [
                    'icon' => 'fa-solid fa-shield-halved',
                    'title' => 'Siaga Bencana & Sosial',
                    'description' => 'Simulasi evakuasi gempa, sekolah aman bencana, serta penggalangan donasi dan bakti sosial untuk warga terdampak musibah.',
                    'button_text' => 'Panduan Mitigasi',
                    'button_url' => '/artikel?kategori=kesiapsiagaan-bencana',
                ],
            ]);

            if ($homePillars) {
                DB::table('content_section_images')->where('section_key', 'home_pillars')->update([
                    'title' => 'Program Unggulan PMR Wira',
                    'tagline' => $homePillars->tagline ?: 'Pilar Aksi Kemanusiaan',
                    'headline' => $homePillars->headline ?: 'Program Unggulan PMR Wira',
                    'body_text' => $homePillars->body_text ?: 'Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.',
                    'cards_data' => $homePillars->cards_data ?: $defaultPrograms,
                ]);
            } else {
                DB::table('content_section_images')->insert([
                    'section_key' => 'home_pillars',
                    'title' => 'Program Unggulan PMR Wira',
                    'page_name' => 'Halaman Beranda',
                    'image_path' => '',
                    'tagline' => 'Pilar Aksi Kemanusiaan',
                    'headline' => 'Program Unggulan PMR Wira',
                    'body_text' => 'Tiga bidang layanan utama yang menjadi denyut nadi pengabdian kami di lingkungan SMAN 1 Ciawi.',
                    'cards_data' => $defaultPrograms,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('content_section_images')) {
            Schema::table('content_section_images', function (Blueprint $table) {
                if (Schema::hasColumn('content_section_images', 'cards_data')) {
                    $table->dropColumn('cards_data');
                }
            });
        }
    }
};

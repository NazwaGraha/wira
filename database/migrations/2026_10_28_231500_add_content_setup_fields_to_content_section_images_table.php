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
                if (! Schema::hasColumn('content_section_images', 'tagline')) {
                    $table->string('tagline')->nullable()->after('title');
                }
                if (! Schema::hasColumn('content_section_images', 'tagline_icon')) {
                    $table->string('tagline_icon')->nullable()->after('tagline');
                }
                if (! Schema::hasColumn('content_section_images', 'headline')) {
                    $table->string('headline')->nullable()->after('tagline_icon');
                }
                if (! Schema::hasColumn('content_section_images', 'body_text')) {
                    $table->text('body_text')->nullable()->after('headline');
                }
                if (! Schema::hasColumn('content_section_images', 'checklist_items')) {
                    $table->json('checklist_items')->nullable()->after('body_text');
                }
                if (! Schema::hasColumn('content_section_images', 'button_text')) {
                    $table->string('button_text')->nullable()->after('checklist_items');
                }
                if (! Schema::hasColumn('content_section_images', 'button_url')) {
                    $table->string('button_url')->nullable()->after('button_text');
                }
            });

            // Pastikan data default section 'home_profile' terisi persis seperti tampilan beranda saat ini
            $homeProfile = DB::table('content_section_images')->where('section_key', 'home_profile')->first();
            $defaultChecklists = json_encode([
                'Meningkatkan keterampilan pertolongan pertama (P3K) dan tanggap darurat bencana.',
                'Menyelenggarakan aksi donor darah sukarela secara berkala bersama UDD PMI.',
                'Membentuk jiwa kepemimpinan, kerelawanan, dan persahabatan antar siswa.',
            ]);

            if ($homeProfile) {
                DB::table('content_section_images')->where('section_key', 'home_profile')->update([
                    'tagline' => $homeProfile->tagline ?: 'Profil Ekstrakurikuler',
                    'tagline_icon' => $homeProfile->tagline_icon ?: 'fa-solid fa-shield-heart',
                    'headline' => $homeProfile->headline ?: 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi',
                    'body_text' => $homeProfile->body_text ?: 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.',
                    'checklist_items' => $homeProfile->checklist_items ?: $defaultChecklists,
                    'button_text' => $homeProfile->button_text ?: 'Baca Profil Selengkapnya',
                    'button_url' => $homeProfile->button_url ?: '/tentang-kami',
                    'badge_text' => $homeProfile->badge_text ?: 'Terakreditasi A',
                    'badge_subtext' => $homeProfile->badge_subtext ?: 'Unit PMR Wira Teladan Kab. Bogor',
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
                $columns = ['tagline', 'tagline_icon', 'headline', 'body_text', 'checklist_items', 'button_text', 'button_url'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('content_section_images', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

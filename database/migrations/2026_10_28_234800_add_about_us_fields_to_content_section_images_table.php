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
                if (! Schema::hasColumn('content_section_images', 'quote_text')) {
                    $table->text('quote_text')->nullable()->after('headline');
                }
                if (! Schema::hasColumn('content_section_images', 'body_text_secondary')) {
                    $table->text('body_text_secondary')->nullable()->after('body_text');
                }
                if (! Schema::hasColumn('content_section_images', 'badge_icon')) {
                    $table->string('badge_icon')->nullable()->after('badge_subtext');
                }
                if (! Schema::hasColumn('content_section_images', 'vision_text')) {
                    $table->text('vision_text')->nullable()->after('cards_data');
                }
                if (! Schema::hasColumn('content_section_images', 'mission_items')) {
                    $table->json('mission_items')->nullable()->after('vision_text');
                }
            });

            // 1. Inisialisasi default 'about_intro' (Mengenal PMR)
            $aboutIntro = DB::table('content_section_images')->where('section_key', 'about_intro')->first();
            $quoteIntro = 'Palang Merah Remaja atau PMR adalah suatu organisasi binaan dari Palang Merah Indonesia yang berpusat di sekolah-sekolah ataupun kelompok-kelompok masyarakat (sanggar, kelompok belajar, dll.) yang bertujuan membangun dan mengembangkan karakter Kepalangmerahan agar siap menjadi Relawan PMI pada masa depan.';
            $bodyIntro1 = 'Di SMA Negeri 1 Ciawi, unit PMR Wira berperan aktif sebagai garda kemanusiaan di lingkungan sekolah, siaga memberikan pertolongan pertama, mempromosikan perilaku hidup bersih dan sehat, serta membina kepedulian sosial melalui aksi donor darah sukarela dan tanggap darurat bencana.';
            $bodyIntro2 = 'Masa bakti Ragana Dwi Pantara 2026/2027 berkomitmen mengamalkan seluruh nilai kepalangmerahan ini secara profesional, modern, dan penuh dedikasi bagi seluruh warga sekolah dan masyarakat sekitar.';

            if ($aboutIntro) {
                DB::table('content_section_images')->where('section_key', 'about_intro')->update([
                    'tagline' => $aboutIntro->tagline ?: 'MENGENAL PMR',
                    'tagline_icon' => $aboutIntro->tagline_icon ?: 'fa-solid fa-book-open-reader',
                    'headline' => $aboutIntro->headline ?: 'Profil PMR Wira SMAN 1 Ciawi',
                    'quote_text' => $aboutIntro->quote_text ?: $quoteIntro,
                    'body_text' => $aboutIntro->body_text ?: $bodyIntro1,
                    'body_text_secondary' => $aboutIntro->body_text_secondary ?: $bodyIntro2,
                    'badge_text' => $aboutIntro->badge_text ?: 'PMR Tingkat Wira',
                    'badge_subtext' => $aboutIntro->badge_subtext ?: 'Binaan PMI Kab. Bogor & SMAN 1 Ciawi',
                    'badge_icon' => $aboutIntro->badge_icon ?: 'fa-solid fa-shield-halved',
                ]);
            }

            // 2. Inisialisasi default 'about_tri_bakti' (Tri Bakti PMR)
            $triBaktiData = json_encode([
                [
                    'sub_badge' => 'Tri Bakti 1',
                    'icon' => 'fa-solid fa-hand-holding-heart',
                    'title' => 'Berbakti kepada Masyarakat',
                    'description' => 'Turut serta dalam aksi kemanusiaan di masyarakat, seperti donor darah sukarela, bakti sosial peduli sesama, dan bantuan tanggap darurat saat bencana melanda.',
                ],
                [
                    'sub_badge' => 'Tri Bakti 2',
                    'icon' => 'fa-solid fa-kit-medical',
                    'title' => 'Mempertinggi Keterampilan dan Memelihara Kebersihan dan Kesehatan',
                    'description' => 'Mengasah keahlian Pertolongan Pertama (P3K), piket medis UKS, simulasi mitigasi bencana, serta membudayakan Pola Hidup Bersih dan Sehat (PHBS) di sekolah.',
                ],
                [
                    'sub_badge' => 'Tri Bakti 3',
                    'icon' => 'fa-solid fa-earth-asia',
                    'title' => 'Mempererat Persahabatan Nasional dan Internasional',
                    'description' => 'Menjalin persaudaraan relawan muda tanpa memandang perbedaan melalui temu akrab, JUMBARA (Jumpa Bakti Gembira), dan pertukaran informasi kepalangmerahan.',
                ],
            ]);

            $aboutTriBakti = DB::table('content_section_images')->where('section_key', 'about_tri_bakti')->first();
            if ($aboutTriBakti) {
                DB::table('content_section_images')->where('section_key', 'about_tri_bakti')->update([
                    'tagline' => $aboutTriBakti->tagline ?: 'Tri Bakti PMR',
                    'tagline_icon' => $aboutTriBakti->tagline_icon ?: 'fa-solid fa-hands-holding-child',
                    'headline' => $aboutTriBakti->headline ?: 'Tri Bakti Palang Merah Remaja',
                    'body_text' => $aboutTriBakti->body_text ?: 'Tiga pilar kewajiban utama yang senantiasa diamalkan oleh seluruh anggota PMR Wira SMAN 1 Ciawi dalam kehidupan sehari-hari:',
                    'cards_data' => $aboutTriBakti->cards_data ?: $triBaktiData,
                ]);
            } else {
                DB::table('content_section_images')->insert([
                    'section_key' => 'about_tri_bakti',
                    'title' => 'Tri Bakti PMR',
                    'page_name' => 'Halaman Tentang Kami',
                    'image_path' => '',
                    'tagline' => 'Tri Bakti PMR',
                    'tagline_icon' => 'fa-solid fa-hands-holding-child',
                    'headline' => 'Tri Bakti Palang Merah Remaja',
                    'body_text' => 'Tiga pilar kewajiban utama yang senantiasa diamalkan oleh seluruh anggota PMR Wira SMAN 1 Ciawi dalam kehidupan sehari-hari:',
                    'cards_data' => $triBaktiData,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3. Inisialisasi default 'about_principles' (7 Prinsip Dasar)
            $principlesData = json_encode([
                ['icon' => 'fa-solid fa-hand-holding-heart', 'title' => 'KEMANUSIAAN', 'subtitle' => 'Humanity'],
                ['icon' => 'fa-solid fa-scale-balanced', 'title' => 'KESAMAAN', 'subtitle' => 'Impartiality'],
                ['icon' => 'fa-solid fa-dove', 'title' => 'KENETRALAN', 'subtitle' => 'Neutrality'],
                ['icon' => 'fa-solid fa-flag', 'title' => 'KEMANDIRIAN', 'subtitle' => 'Independence'],
                ['icon' => 'fa-solid fa-heart', 'title' => 'KESUKARELAAN', 'subtitle' => 'Voluntary Service'],
                ['icon' => 'fa-solid fa-people-group', 'title' => 'KESATUAN', 'subtitle' => 'UNITY'],
                ['icon' => 'fa-solid fa-earth-americas', 'title' => 'KESEMESTAAN', 'subtitle' => 'Universality'],
            ]);

            $aboutPrinciples = DB::table('content_section_images')->where('section_key', 'about_principles')->first();
            if ($aboutPrinciples) {
                DB::table('content_section_images')->where('section_key', 'about_principles')->update([
                    'tagline' => $aboutPrinciples->tagline ?: 'Landasan Moral Universal',
                    'headline' => $aboutPrinciples->headline ?: '7 Dasar Prinsip Palang Merah',
                    'body_text' => $aboutPrinciples->body_text ?: 'Tujuh prinsip fundamental yang menjadi pedoman teguh setiap langkah dan tindakan relawan PMR Wira di seluruh dunia:',
                    'cards_data' => $aboutPrinciples->cards_data ?: $principlesData,
                ]);
            } else {
                DB::table('content_section_images')->insert([
                    'section_key' => 'about_principles',
                    'title' => '7 Dasar Prinsip Palang Merah',
                    'page_name' => 'Halaman Tentang Kami',
                    'image_path' => '',
                    'tagline' => 'Landasan Moral Universal',
                    'headline' => '7 Dasar Prinsip Palang Merah',
                    'body_text' => 'Tujuh prinsip fundamental yang menjadi pedoman teguh setiap langkah dan tindakan relawan PMR Wira di seluruh dunia:',
                    'cards_data' => $principlesData,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 4. Inisialisasi default 'about_vision_mission' (Visi & Misi)
            $visionText = 'Terwujudnya PMR Wira SMAN 1 Ciawi sebagai unit relawan muda yang berkarakter kemanusiaan tangguh, sigap, berintegritas, mandiri, dan berprestasi berlandaskan prinsip-prinsip dasar Palang Merah Internasional.';
            $missionItems = json_encode([
                'Menanamkan pemahaman dan pengamalan 7 Prinsip Dasar Palang Merah dan Tri Bakti PMR.',
                'Meningkatkan kapasitas anggota dalam keterampilan pertolongan pertama, sanitasi kesehatan, dan penanggulangan bencana.',
                'Aktif menyelenggarakan program donor darah sukarela dan bakti sosial bagi masyarakat.',
                'Menjalin sinergi erat dengan pembina sekolah, PMI Kabupaten Bogor, dan unit PMR lainnya.',
            ]);

            $aboutVisionMission = DB::table('content_section_images')->where('section_key', 'about_vision_mission')->first();
            if ($aboutVisionMission) {
                DB::table('content_section_images')->where('section_key', 'about_vision_mission')->update([
                    'tagline' => $aboutVisionMission->tagline ?: 'Arah Gerak Organisasi',
                    'headline' => $aboutVisionMission->headline ?: 'Visi & Misi Kami',
                    'vision_text' => $aboutVisionMission->vision_text ?: $visionText,
                    'mission_items' => $aboutVisionMission->mission_items ?: $missionItems,
                ]);
            } else {
                DB::table('content_section_images')->insert([
                    'section_key' => 'about_vision_mission',
                    'title' => 'Visi & Misi Organisasi',
                    'page_name' => 'Halaman Tentang Kami',
                    'image_path' => '',
                    'tagline' => 'Arah Gerak Organisasi',
                    'headline' => 'Visi & Misi Kami',
                    'vision_text' => $visionText,
                    'mission_items' => $missionItems,
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
                $columns = ['quote_text', 'body_text_secondary', 'badge_icon', 'vision_text', 'mission_items'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('content_section_images', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

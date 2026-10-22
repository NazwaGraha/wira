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
        if (! Schema::hasTable('content_section_images')) {
            Schema::create('content_section_images', function (Blueprint $table) {
                $table->id();
                $table->string('section_key')->unique();
                $table->string('title');
                $table->string('page_name');
                $table->string('image_path');
                $table->string('badge_text')->nullable();
                $table->string('badge_subtext')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Seed default initial records
            DB::table('content_section_images')->insert([
                [
                    'section_key' => 'home_profile',
                    'title' => 'Profil Ekstrakurikuler',
                    'page_name' => 'Halaman Beranda',
                    'image_path' => '/mockups/02_tentang_kami.jpg',
                    'badge_text' => 'Terakreditasi A',
                    'badge_subtext' => 'Unit PMR Wira Teladan Kab. Bogor',
                    'description' => 'Foto tim relawan di samping naskah profil Membangun Karakter Kemanusiaan.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'section_key' => 'about_intro',
                    'title' => 'Mengenal PMR',
                    'page_name' => 'Halaman Tentang Kami',
                    'image_path' => '/mockups/02_tentang_kami.jpg',
                    'badge_text' => 'PMR Tingkat Wira',
                    'badge_subtext' => 'Binaan PMI Kab. Bogor & SMAN 1 Ciawi',
                    'description' => 'Foto profil pengenalan di samping naskah definisi resmi PMR Wira.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_section_images');
    }
};

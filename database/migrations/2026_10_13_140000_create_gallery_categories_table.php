<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('gallery_categories')) {
            Schema::create('gallery_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('galleries') && !Schema::hasColumn('galleries', 'gallery_category_id')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->unsignedBigInteger('gallery_category_id')->nullable()->after('category');
                $table->foreign('gallery_category_id')->references('id')->on('gallery_categories')->nullOnDelete();
            });
        }

        // Seed kategori bawaan galeri yang sebelumnya hardcoded
        $defaultCategories = [
            'Pertolongan Pertama' => 'Dokumentasi simulasi, latihan penanganan medis darurat, dan pertolongan pertama di lapangan.',
            'Organisasi' => 'Dokumentasi rapat kerja, pelantikan pengurus, dan koordinasi internal relawan PMR.',
            'Donor Darah' => 'Dokumentasi pelaksanaan kegiatan donor darah sukarela bersama PMI.',
            'Upacara & Apel' => 'Dokumentasi apel pelepasan, upacara bendera, dan peringatan hari besar kepalangmerahan.',
            'Sosialisasi' => 'Dokumentasi sosialisasi kepalangmerahan, materi PHBS, dan edukasi kesehatan ke siswa.',
            'Bakti Sosial' => 'Dokumentasi aksi kemanusiaan, pembagian bantuan, dan bakti sosial masyarakat.',
            'Lainnya' => 'Dokumentasi berbagai agenda dan kegiatan lainnya.'
        ];

        foreach ($defaultCategories as $name => $desc) {
            $slug = Str::slug($name);
            $existing = DB::table('gallery_categories')->where('slug', $slug)->first();
            if (!$existing) {
                $catId = DB::table('gallery_categories')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $desc,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Sinkronkan data galleries yang ada sebelumnya
                if (Schema::hasTable('galleries')) {
                    DB::table('galleries')
                        ->where('category', $name)
                        ->whereNull('gallery_category_id')
                        ->update(['gallery_category_id' => $catId]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('galleries') && Schema::hasColumn('galleries', 'gallery_category_id')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->dropForeign(['gallery_category_id']);
                $table->dropColumn('gallery_category_id');
            });
        }

        Schema::dropIfExists('gallery_categories');
    }
};

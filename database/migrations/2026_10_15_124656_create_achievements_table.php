<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('event_name');
            $table->string('organizer')->nullable();
            $table->date('event_date');
            $table->string('level')->default('Provinsi'); // Nasional, Provinsi, Kabupaten/Kota, Kecamatan, Sekolah
            $table->string('rank')->default('Juara 1'); // Juara Umum I, Juara Umum, Juara 1, Juara 2, Juara 3, Harapan 1, Best Team, Favorit
            $table->string('category')->nullable(); // Pertolongan Pertama, Tandu Darurat, Cepat Tepat, Mading, Umum
            $table->json('contingent_members')->nullable(); // Array: [{name, class_grade, member_id}]
            $table->text('description')->nullable();
            $table->string('trophy_image')->nullable();
            $table->json('gallery_images')->nullable(); // Array foto serah terima piala
            $table->string('certificate_file')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order_position')->default(1);
            $table->timestamps();

            $table->index(['event_date', 'level', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};

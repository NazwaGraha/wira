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
        if (! Schema::hasTable('background_musics')) {
            Schema::create('background_musics', function (Blueprint $table) {
                $table->id();
                $table->string('title')->default('Mars Palang Merah Indonesia');
                $table->string('subtitle')->nullable()->default('Ciptaan: Mochtar H. S. • PMR Wira SMAN 1 Ciawi');
                $table->string('audio_path')->nullable(); // null uses default /audio/mars-pmi.m4a
                $table->boolean('is_active')->default(true);
                $table->boolean('autoplay_on_first_interaction')->default(true);
                $table->unsignedTinyInteger('default_volume')->default(30); // 0-100%
                $table->boolean('loop')->default(true);
                $table->text('lyrics')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_musics');
    }
};

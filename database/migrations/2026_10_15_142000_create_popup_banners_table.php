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
        if (! Schema::hasTable('popup_banners')) {
            Schema::create('popup_banners', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('image_path');
                $table->string('target_url', 500)->nullable();
                $table->string('button_text', 100)->default('Info Lebih Lanjut');
                $table->boolean('open_in_new_tab')->default(true);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('impressions_count')->default(0);
                $table->unsignedInteger('clicks_count')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popup_banners');
    }
};

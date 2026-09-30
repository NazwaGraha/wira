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
        Schema::create('competition_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_event_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable(); // e.g. LPP-MULA-PA, LKTR-MADYA-PI
            $table->string('name'); // e.g. Lomba Pertolongan Pertama
            $table->enum('level', ['Mula', 'Madya', 'Wira']);
            $table->enum('gender_category', ['Putra', 'Putri', 'Campuran', 'Umum'])->default('Umum');
            $table->string('scoring_type')->default('standard_time'); // standard_time, written_practical_time, multi_criteria, social_engagement, bracket_quiz
            $table->enum('point_tier', ['tier_1', 'tier_2', 'tier_3'])->default('tier_1'); // tier_1: 10/8/6, tier_2: 8/6/4, tier_3: 3/2/1
            $table->json('criteria_schema')->nullable(); // e.g. ["Kreativitas", "Presentasi"] or criteria configs
            $table->integer('max_team_members')->default(1);
            $table->boolean('has_rounds')->default(false); // e.g. LCT has multiple rounds
            $table->integer('order_position')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_categories');
    }
};

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
        Schema::create('competition_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('competition_category_id');
            $table->unsignedBigInteger('competition_participant_team_id');
            $table->string('round_name')->default('Utama'); // Utama, Penyisihan, Semi Final (Termin 1), Semi Final, Final
            $table->json('score_details')->nullable(); // flexible JSON storing test scores, practical, creativity, time, likes, stickers, etc.
            $table->decimal('final_score', 8, 2)->default(0); // auto-calculated final point/score
            $table->string('time_recorded')->nullable(); // e.g. "00:05:44" or "25.2" or "6.45"
            $table->integer('rank')->nullable(); // Peringkat 1, 2, 3, etc.
            $table->boolean('is_disqualified')->default(false); // Hitam di Excel
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('competition_category_id', 'fk_cs_category')
                ->references('id')->on('competition_categories')->cascadeOnDelete();
            $table->foreign('competition_participant_team_id', 'fk_cs_team')
                ->references('id')->on('competition_participant_teams')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_scores');
    }
};

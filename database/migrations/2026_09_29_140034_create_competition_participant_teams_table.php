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
        Schema::create('competition_participant_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('competition_registration_id');
            $table->unsignedBigInteger('competition_category_id');
            $table->string('order_number')->nullable(); // e.g. "1", "1.1", "2.1", "3", "17" as seen in excel
            $table->string('team_name'); // e.g. "SDN TEGALWARU 02 (A)" or "MTSN KOTA BOGOR"
            $table->string('team_label')->nullable(); // "(A)", "(B)", "(C) PUTRI"
            $table->json('members_list')->nullable(); // array of member names
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('competition_registration_id', 'fk_cpt_registration')
                ->references('id')->on('competition_registrations')->cascadeOnDelete();
            $table->foreign('competition_category_id', 'fk_cpt_category')
                ->references('id')->on('competition_categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_participant_teams');
    }
};

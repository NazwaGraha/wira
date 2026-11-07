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
        Schema::create('parking_quota_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mode', 50)->default('proportional_teams'); // proportional_teams, flat
            $table->unsignedInteger('flat_default_r4')->default(1);
            $table->unsignedInteger('flat_default_r2')->default(2);
            $table->json('matrix_rules')->nullable();
            $table->timestamps();
        });

        Schema::table('competition_registrations', function (Blueprint $table) {
            $table->string('parking_quota_mode', 20)->default('auto')->after('is_checked_in'); // auto, custom
            $table->unsignedInteger('custom_quota_r4')->nullable()->after('parking_quota_mode');
            $table->unsignedInteger('custom_quota_r2')->nullable()->after('custom_quota_r4');
            $table->text('parking_notes')->nullable()->after('custom_quota_r2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'parking_quota_mode',
                'custom_quota_r4',
                'custom_quota_r2',
                'parking_notes',
            ]);
        });

        Schema::dropIfExists('parking_quota_settings');
    }
};

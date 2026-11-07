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
        Schema::table('competition_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('competition_registrations', 'parking_priority_score')) {
                $table->decimal('parking_priority_score', 5, 2)->nullable()->after('parking_notes');
            }
            if (! Schema::hasColumn('competition_registrations', 'parking_priority_rank')) {
                $table->unsignedInteger('parking_priority_rank')->nullable()->after('parking_priority_score');
            }
            if (! Schema::hasColumn('competition_registrations', 'parking_priority_criteria')) {
                $table->json('parking_priority_criteria')->nullable()->after('parking_priority_rank');
            }
        });

        Schema::table('parking_locations', function (Blueprint $table) {
            if (! Schema::hasColumn('parking_locations', 'is_priority')) {
                $table->boolean('is_priority')->default(false)->after('capacity_r2');
            }
            if (! Schema::hasColumn('parking_locations', 'priority_order')) {
                $table->integer('priority_order')->default(100)->after('is_priority');
            }
        });

        // Set default priority for Kantong A
        DB::table('parking_locations')->where('code', 'KPA')->update([
            'is_priority' => true,
            'priority_order' => 1,
        ]);
        DB::table('parking_locations')->where('code', 'KPB')->update([
            'is_priority' => false,
            'priority_order' => 2,
        ]);
        DB::table('parking_locations')->where('code', 'KPC')->update([
            'is_priority' => false,
            'priority_order' => 3,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_registrations', function (Blueprint $table) {
            $table->dropColumn(['parking_priority_score', 'parking_priority_rank', 'parking_priority_criteria']);
        });

        Schema::table('parking_locations', function (Blueprint $table) {
            $table->dropColumn(['is_priority', 'priority_order']);
        });
    }
};

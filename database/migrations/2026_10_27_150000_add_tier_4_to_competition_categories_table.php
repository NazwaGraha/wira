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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `competition_categories` MODIFY COLUMN `point_tier` VARCHAR(20) NOT NULL DEFAULT 'tier_1'");
        } elseif (DB::getDriverName() !== 'sqlite') {
            Schema::table('competition_categories', function (Blueprint $table) {
                $table->string('point_tier', 20)->default('tier_1')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `competition_categories` MODIFY COLUMN `point_tier` ENUM('tier_1', 'tier_2', 'tier_3') NOT NULL DEFAULT 'tier_1'");
        }
    }
};

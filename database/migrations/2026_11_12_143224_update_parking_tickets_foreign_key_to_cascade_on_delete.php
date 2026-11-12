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
        if (Schema::hasTable('parking_tickets')) {
            if (DB::getDriverName() !== 'sqlite') {
                Schema::table('parking_tickets', function (Blueprint $table) {
                    $table->dropForeign(['competition_registration_id']);
                    $table->foreign('competition_registration_id')
                        ->references('id')
                        ->on('competition_registrations')
                        ->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parking_tickets')) {
            if (DB::getDriverName() !== 'sqlite') {
                Schema::table('parking_tickets', function (Blueprint $table) {
                    $table->dropForeign(['competition_registration_id']);
                    $table->foreign('competition_registration_id')
                        ->references('id')
                        ->on('competition_registrations')
                        ->nullOnDelete();
                });
            }
        }
    }
};

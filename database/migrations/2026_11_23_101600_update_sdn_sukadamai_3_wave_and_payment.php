<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('competition_registrations')
            ->where('registration_code', 'SBB-U27376Y')
            ->update([
                'registration_wave' => 1,
                'total_payment' => 534000.00,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('competition_registrations')
            ->where('registration_code', 'SBB-U27376Y')
            ->update([
                'registration_wave' => 2,
                'total_payment' => 590000.00,
            ]);
    }
};

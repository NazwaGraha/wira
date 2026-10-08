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
            ->where('registration_code', 'SBB-TOCSEA')
            ->update(['registration_code' => 'SBB-W54342H']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('competition_registrations')
            ->where('registration_code', 'SBB-W54342H')
            ->update(['registration_code' => 'SBB-TOCSEA']);
    }
};

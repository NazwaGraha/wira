<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('uks_hotlines')) {
            DB::table('uks_hotlines')
                ->where('phone_number', '085770246918')
                ->orWhereNull('phone_number')
                ->update(['phone_number' => '083872506817']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('uks_hotlines')) {
            DB::table('uks_hotlines')
                ->where('phone_number', '083872506817')
                ->update(['phone_number' => '085770246918']);
        }
    }
};

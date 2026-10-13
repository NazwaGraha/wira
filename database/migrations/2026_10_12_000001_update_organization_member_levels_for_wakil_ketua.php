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
        if (! Schema::hasTable('organization_members')) {
            return;
        }

        // Shift existing hierarchy levels to make room for Level 3 (Wakil Ketua):
        // Old: 1 = Pembina, 2 = Ketua, 3 = BPH, 4 = Bidang, 5 = Lainnya
        // New: 1 = Pembina, 2 = Ketua, 3 = Wakil Ketua, 4 = BPH, 5 = Bidang, 6 = Lainnya

        // Shift former Level 5 to Level 6
        DB::table('organization_members')
            ->where('level', 5)
            ->where('position', 'not like', '%Wakil%')
            ->update(['level' => 6]);

        // Shift former Level 4 to Level 5
        DB::table('organization_members')
            ->where('level', 4)
            ->where('position', 'not like', '%Wakil%')
            ->update(['level' => 5]);

        // Shift former Level 3 (Sekretaris / Bendahara) to Level 4
        DB::table('organization_members')
            ->where('level', 3)
            ->where('position', 'not like', '%Wakil%')
            ->update(['level' => 4]);

        // If any existing row is already Wakil Ketua, ensure its level is set to 3
        DB::table('organization_members')
            ->where('position', 'like', '%Wakil%')
            ->update(['level' => 3]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('organization_members')) {
            return;
        }

        DB::table('organization_members')
            ->where('level', 4)
            ->update(['level' => 3]);

        DB::table('organization_members')
            ->where('level', 5)
            ->update(['level' => 4]);

        DB::table('organization_members')
            ->where('level', 6)
            ->update(['level' => 5]);
    }
};

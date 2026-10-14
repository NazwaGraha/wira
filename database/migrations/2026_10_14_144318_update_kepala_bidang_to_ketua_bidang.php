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
        // 1. Update data pada tabel members
        if (Schema::hasTable('members') && Schema::hasColumn('members', 'position')) {
            DB::table('members')
                ->where('position', 'like', '%kepala bidang%')
                ->orWhere('position', 'like', '%kabid%')
                ->get()
                ->each(function ($m) {
                    $newPos = preg_replace('/kepala\s+bidang/i', 'Ketua Bidang', $m->position);
                    $newPos = preg_replace('/\bkabid\b/i', 'Ketua Bidang', $newPos);
                    DB::table('members')->where('id', $m->id)->update(['position' => $newPos]);
                });
        }

        // 2. Update data pada tabel organization_members
        if (Schema::hasTable('organization_members')) {
            DB::table('organization_members')
                ->where('position', 'like', '%kepala bidang%')
                ->orWhere('subtitle', 'like', '%kepala bidang%')
                ->orWhere('position', 'like', '%kabid%')
                ->orWhere('subtitle', 'like', '%kabid%')
                ->get()
                ->each(function ($om) {
                    $newPos = preg_replace('/kepala\s+bidang/i', 'Ketua Bidang', $om->position);
                    $newPos = preg_replace('/\bkabid\b/i', 'Ketua Bidang', $newPos);

                    $newSub = preg_replace('/kepala\s+bidang/i', 'Ketua Bidang', $om->subtitle ?? '');
                    $newSub = preg_replace('/\bkabid\b/i', 'Ketua Bidang', $newSub);

                    DB::table('organization_members')->where('id', $om->id)->update([
                        'position' => $newPos,
                        'subtitle' => $newSub,
                    ]);
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed for position label normalization
    }
};

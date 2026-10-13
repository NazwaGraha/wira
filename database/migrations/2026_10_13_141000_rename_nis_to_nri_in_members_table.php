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
        if (Schema::hasTable('members')) {
            if (Schema::hasColumn('members', 'nis') && ! Schema::hasColumn('members', 'nri')) {
                try {
                    // Coba rename langsung secara native
                    Schema::table('members', function (Blueprint $table) {
                        $table->renameColumn('nis', 'nri');
                    });
                } catch (Throwable $e) {
                    // Fallback aman untuk berbagai versi MySQL/MariaDB
                    Schema::table('members', function (Blueprint $table) {
                        $table->string('nri', 50)->nullable()->unique()->after('id');
                    });

                    DB::statement('UPDATE members SET nri = nis WHERE nri IS NULL');

                    try {
                        Schema::table('members', function (Blueprint $table) {
                            $table->dropColumn('nis');
                        });
                    } catch (Throwable $ex) {
                        // Jika drop gagal, kolom nri tetap aktif digunakan
                    }
                }
            } elseif (! Schema::hasColumn('members', 'nri')) {
                Schema::table('members', function (Blueprint $table) {
                    $table->string('nri', 50)->nullable()->unique()->after('id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('members') && Schema::hasColumn('members', 'nri') && ! Schema::hasColumn('members', 'nis')) {
            try {
                Schema::table('members', function (Blueprint $table) {
                    $table->renameColumn('nri', 'nis');
                });
            } catch (Throwable $e) {
                // Ignore
            }
        }
    }
};

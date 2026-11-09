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
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'custom_permissions')) {
                $table->json('custom_permissions')->nullable()->after('password');
            }
        });

        Schema::table('advisors', function (Blueprint $table) {
            if (! Schema::hasColumn('advisors', 'custom_permissions')) {
                $table->json('custom_permissions')->nullable()->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'custom_permissions')) {
                $table->dropColumn('custom_permissions');
            }
        });

        Schema::table('advisors', function (Blueprint $table) {
            if (Schema::hasColumn('advisors', 'custom_permissions')) {
                $table->dropColumn('custom_permissions');
            }
        });
    }
};

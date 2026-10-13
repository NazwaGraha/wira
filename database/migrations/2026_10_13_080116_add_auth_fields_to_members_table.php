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
            if (! Schema::hasColumn('members', 'nip')) {
                $table->string('nip')->nullable()->after('nri')->index();
            }
            if (! Schema::hasColumn('members', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (! Schema::hasColumn('members', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'nip')) {
                $table->dropColumn('nip');
            }
            if (Schema::hasColumn('members', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('members', 'remember_token')) {
                $table->dropRememberToken();
            }
        });
    }
};

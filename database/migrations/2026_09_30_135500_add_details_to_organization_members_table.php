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
        Schema::table('organization_members', function (Blueprint $table) {
            if (!Schema::hasColumn('organization_members', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('organization_members', 'photo')) {
                $table->string('photo')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('organization_members', 'staff_members')) {
                $table->json('staff_members')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('organization_members', 'work_program')) {
                $table->longText('work_program')->nullable()->after('staff_members');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_members', function (Blueprint $table) {
            $table->dropColumn(['member_id', 'photo', 'staff_members', 'work_program']);
        });
    }
};

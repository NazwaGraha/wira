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
        Schema::table('competition_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('competition_registrations', 'is_checked_in')) {
                $table->boolean('is_checked_in')->default(false)->after('verified_by');
            }
            if (!Schema::hasColumn('competition_registrations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('is_checked_in');
            }
            if (!Schema::hasColumn('competition_registrations', 'checked_in_by')) {
                $table->string('checked_in_by')->nullable()->after('checked_in_at');
            }
            if (!Schema::hasColumn('competition_registrations', 'checkin_notes')) {
                $table->text('checkin_notes')->nullable()->after('checked_in_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_registrations', function (Blueprint $table) {
            $cols = ['is_checked_in', 'checked_in_at', 'checked_in_by', 'checkin_notes'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('competition_registrations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

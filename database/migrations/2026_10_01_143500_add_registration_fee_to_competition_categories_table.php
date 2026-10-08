<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('competition_categories', 'registration_fee')) {
            Schema::table('competition_categories', function (Blueprint $table) {
                $table->decimal('registration_fee', 12, 2)->default(150000)->after('criteria_schema');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('competition_categories', 'registration_fee')) {
            Schema::table('competition_categories', function (Blueprint $table) {
                $table->dropColumn('registration_fee');
            });
        }
    }
};

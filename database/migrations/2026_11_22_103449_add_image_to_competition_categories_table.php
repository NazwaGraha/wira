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
        Schema::table('competition_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('competition_categories', 'image')) {
                $table->string('image')->nullable()->after('gender_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_categories', function (Blueprint $table) {
            if (Schema::hasColumn('competition_categories', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};

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
        Schema::table('blood_donation_events', function (Blueprint $table) {
            $table->string('registration_link')->nullable();
            $table->boolean('is_registration_link_active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_donation_events', function (Blueprint $table) {
            $table->dropColumn(['registration_link', 'is_registration_link_active']);
        });
    }
};

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
        Schema::create('parking_officers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('parking_officer_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_officer_id')
                ->constrained('parking_officers')
                ->cascadeOnDelete();
            $table->foreignId('parking_location_id')
                ->constrained('parking_locations')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['parking_officer_id', 'parking_location_id'], 'officer_location_unique');
        });

        Schema::table('parking_tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('parking_tickets', 'checked_by_officer_id')) {
                $table->foreignId('checked_by_officer_id')
                    ->nullable()
                    ->after('checked_by_user_id')
                    ->constrained('parking_officers')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('parking_tickets', 'checked_by_officer_id')) {
                $table->dropForeign(['checked_by_officer_id']);
                $table->dropColumn('checked_by_officer_id');
            }
        });

        Schema::dropIfExists('parking_officer_locations');
        Schema::dropIfExists('parking_officers');
    }
};

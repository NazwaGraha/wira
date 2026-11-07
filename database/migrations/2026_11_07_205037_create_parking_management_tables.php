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
        Schema::create('parking_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('address')->nullable();
            $table->string('gmaps_url')->nullable();
            $table->unsignedInteger('capacity_r4')->default(0);
            $table->unsignedInteger('capacity_r2')->default(0);
            $table->string('officer_name')->nullable();
            $table->string('officer_phone')->nullable();
            $table->string('status', 20)->default('open'); // open, full, closed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('parking_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 50)->unique();
            $table->string('qr_code_token', 64)->unique()->index();
            $table->foreignId('competition_registration_id')
                ->nullable()
                ->constrained('competition_registrations')
                ->nullOnDelete();
            $table->foreignId('parking_location_id')
                ->nullable()
                ->constrained('parking_locations')
                ->nullOnDelete();
            $table->string('vehicle_type', 30)->default('r4_car'); // r4_car, r4_elf, r4_bus, r2_motor
            $table->string('license_plate', 30);
            $table->string('vehicle_brand', 100)->nullable();
            $table->string('vehicle_color', 50)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 30)->nullable();
            $table->string('status', 20)->default('booked'); // booked, parked, checked_out, cancelled
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->foreignId('checked_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_tickets');
        Schema::dropIfExists('parking_locations');
    }
};

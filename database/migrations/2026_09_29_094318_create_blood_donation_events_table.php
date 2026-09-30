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
        Schema::create('blood_donation_events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. "Aksi Donor Darah PMR SMAN 1 Ciawi"
            $table->date('event_date');
            $table->time('time_start');
            $table->time('time_end')->nullable();
            $table->string('location');
            $table->integer('target_bags')->nullable();
            $table->boolean('is_active')->default(false); // To show on the countdown
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donation_events');
    }
};

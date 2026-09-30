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
        Schema::create('blood_donor_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_donation_event_id')->nullable()->constrained('blood_donation_events')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('blood_type'); // A, B, AB, O
            $table->string('rhesus')->nullable(); // +, -
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // Laki-laki, Perempuan
            $table->text('address')->nullable();
            $table->date('last_donation_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'attended', 'completed'])->default('pending');
            $table->text('medical_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donor_registrations');
    }
};

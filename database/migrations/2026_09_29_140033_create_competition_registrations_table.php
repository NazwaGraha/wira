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
        Schema::create('competition_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_event_id')->constrained()->cascadeOnDelete();
            $table->string('registration_code')->unique(); // e.g. REG-SBB-001
            $table->string('school_name');
            $table->enum('level', ['Mula', 'Madya', 'Wira']);
            $table->string('advisor_name'); // Nama Pembina / Pendamping
            $table->string('advisor_phone'); // WhatsApp
            $table->string('advisor_email')->nullable();
            $table->text('school_address')->nullable();
            $table->string('payment_proof')->nullable();
            $table->decimal('total_payment', 12, 2)->default(0);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_registrations');
    }
};

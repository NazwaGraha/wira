<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('class_grade'); // X-A, XI-MIPA 1, dll
            $table->string('nisn')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('interest_field')->nullable(); // Pertolongan Pertama, Dapur Umum, Logistik, Humas, dll
            $table->text('motivation')->nullable();
            $table->enum('status', ['pending', 'verified', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_registrations');
    }
};

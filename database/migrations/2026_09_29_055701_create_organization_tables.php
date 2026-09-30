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
        Schema::create('organization_settings', function (Blueprint $table) {
            $table->id();
            $table->string('badge')->default('Bagan Kepengurusan');
            $table->string('title')->default('Struktur Organisasi 2026/2027');
            $table->text('subtitle')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('subtitle')->nullable();
            $table->unsignedTinyInteger('level')->default(4); // 1: Pembina, 2: Ketua, 3: Sekretaris/Bendahara, 4: Seksi/Divisi, 5: Lainnya
            $table->integer('order_position')->default(0);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('organization_settings');
    }
};

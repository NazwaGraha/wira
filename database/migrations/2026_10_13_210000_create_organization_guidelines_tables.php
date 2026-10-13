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
        Schema::create('organization_guidelines', function (Blueprint $table) {
            $table->id();
            $table->string('period')->default('2026/2027');
            $table->string('established_date')->default('05 Januari 2026');
            $table->string('established_place')->default('Ciawi, Bogor');
            $table->string('gbho_policy_heading')->default('Arah Kebijakan Utama');
            $table->text('gbho_policy_text')->nullable();
            $table->string('gbho_pdf_path')->nullable();
            $table->string('ppuo_pdf_path')->nullable();
            $table->string('signer_presidium')->default('Presidium Sidang I, II, dan III');
            $table->string('signer_pembina')->default('Pembina PMR Wira SMAN 1 Ciawi');
            $table->string('signer_school')->default('Kepala SMA Negeri 1 Ciawi');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('gbho_strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_guideline_id')->nullable()->constrained('organization_guidelines')->cascadeOnDelete();
            $table->unsignedInteger('order_number')->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->default('fa-solid fa-check');
            $table->timestamps();
        });

        Schema::create('ppuo_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_guideline_id')->nullable()->constrained('organization_guidelines')->cascadeOnDelete();
            $table->unsignedInteger('order_number')->default(1);
            $table->string('chapter_number'); // e.g. "BAB I", "BAB II"
            $table->string('title'); // e.g. "Nama, Waktu, dan Kedudukan"
            $table->string('summary')->nullable();
            $table->string('badge')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppuo_chapters');
        Schema::dropIfExists('gbho_strategies');
        Schema::dropIfExists('organization_guidelines');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uks_hotlines', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->default('085770246918');
            $table->string('label')->default('Hotline UKS');
            $table->string('service_name')->default('Piket Medis & P3K Sekolah');
            $table->string('officer_name')->nullable()->default('Tim Relawan PMR Wira SMAN 1 Ciawi');
            $table->string('operational_hours')->default('Senin - Jumat (07.00 - 16.00 WIB)');
            $table->string('location')->default('Ruang UKS SMAN 1 Ciawi');
            $table->boolean('is_active')->default(true);
            $table->string('status_text')->default('Siaga di Jam Sekolah');
            $table->text('description')->nullable();
            $table->text('whatsapp_message')->nullable();
            $table->timestamps();
        });

        // Insert default initial hotline setting
        DB::table('uks_hotlines')->insert([
            'phone_number' => '085770246918',
            'label' => 'Hotline UKS',
            'service_name' => 'Piket Medis & P3K Sekolah',
            'officer_name' => 'Tim Relawan PMR Wira SMAN 1 Ciawi',
            'operational_hours' => 'Senin - Jumat (07.00 - 16.00 WIB)',
            'location' => 'Ruang UKS SMAN 1 Ciawi',
            'is_active' => true,
            'status_text' => 'Siaga di Jam Sekolah',
            'description' => 'Layanan cepat tanggap pertolongan pertama (P3K), penanganan siswa sakit/pingsan saat upacara/KBM, serta evakuasi medis darurat di lingkungan SMAN 1 Ciawi.',
            'whatsapp_message' => 'Halo Petugas Piket Medis PMR SMAN 1 Ciawi, saya membutuhkan bantuan medis/pertolongan pertama di lokasi: ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uks_hotlines');
    }
};

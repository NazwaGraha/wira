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
        Schema::create('parking_fare_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_public_parking_active')->default(true);
            $table->unsignedInteger('fare_r2_motor')->default(5000);
            $table->unsignedInteger('fare_r4_car')->default(10000);
            $table->unsignedInteger('fare_r4_minibus')->default(25000);
            $table->unsignedInteger('fare_r6_bus')->default(50000);
            $table->boolean('allow_cash')->default(true);
            $table->boolean('allow_qris')->default(true);
            $table->unsignedInteger('lost_ticket_penalty')->default(20000);
            $table->text('ticket_receipt_note')->nullable();
            $table->timestamps();
        });

        // Seed default row
        DB::table('parking_fare_settings')->insert([
            'id' => 1,
            'is_public_parking_active' => true,
            'fare_r2_motor' => 5000,
            'fare_r4_car' => 10000,
            'fare_r4_minibus' => 25000,
            'fare_r6_bus' => 50000,
            'allow_cash' => true,
            'allow_qris' => true,
            'lost_ticket_penalty' => 20000,
            'ticket_receipt_note' => 'Karcis parkir wajib disimpan sebagai bukti keluar. Kunci ganda kendaraan Anda. Kehilangan karcis dikenakan denda.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('parking_tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('parking_tickets', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('parking_location_id');
            }
            if (! Schema::hasColumn('parking_tickets', 'fee_amount')) {
                $table->unsignedInteger('fee_amount')->default(0)->after('is_public');
            }
            if (! Schema::hasColumn('parking_tickets', 'payment_method')) {
                $table->string('payment_method', 20)->default('free_contingent')->after('fee_amount');
            }
            if (! Schema::hasColumn('parking_tickets', 'payment_status')) {
                $table->string('payment_status', 20)->default('free')->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_fare_settings');

        Schema::table('parking_tickets', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'fee_amount', 'payment_method', 'payment_status']);
        });
    }
};

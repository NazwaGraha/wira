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
        Schema::table('competition_events', function (Blueprint $table) {
            // Rekening Khusus Tingkat Mula (SD / MI)
            if (! Schema::hasColumn('competition_events', 'bank_mula_name')) {
                $table->string('bank_mula_name', 100)->nullable()->after('bank_account_holder');
            }
            if (! Schema::hasColumn('competition_events', 'bank_mula_number')) {
                $table->string('bank_mula_number', 100)->nullable()->after('bank_mula_name');
            }
            if (! Schema::hasColumn('competition_events', 'bank_mula_holder')) {
                $table->string('bank_mula_holder', 150)->nullable()->after('bank_mula_number');
            }

            // Rekening Khusus Tingkat Madya (SMP / MTs)
            if (! Schema::hasColumn('competition_events', 'bank_madya_name')) {
                $table->string('bank_madya_name', 100)->nullable()->after('bank_mula_holder');
            }
            if (! Schema::hasColumn('competition_events', 'bank_madya_number')) {
                $table->string('bank_madya_number', 100)->nullable()->after('bank_madya_name');
            }
            if (! Schema::hasColumn('competition_events', 'bank_madya_holder')) {
                $table->string('bank_madya_holder', 150)->nullable()->after('bank_madya_number');
            }

            // Rekening Khusus Tingkat Wira (SMA / SMK / MA)
            if (! Schema::hasColumn('competition_events', 'bank_wira_name')) {
                $table->string('bank_wira_name', 100)->nullable()->after('bank_madya_holder');
            }
            if (! Schema::hasColumn('competition_events', 'bank_wira_number')) {
                $table->string('bank_wira_number', 100)->nullable()->after('bank_wira_name');
            }
            if (! Schema::hasColumn('competition_events', 'bank_wira_holder')) {
                $table->string('bank_wira_holder', 150)->nullable()->after('bank_wira_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_events', function (Blueprint $table) {
            $columns = [
                'bank_mula_name', 'bank_mula_number', 'bank_mula_holder',
                'bank_madya_name', 'bank_madya_number', 'bank_madya_holder',
                'bank_wira_name', 'bank_wira_number', 'bank_wira_holder',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('competition_events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

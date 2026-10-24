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
            // Rekening 2 Khusus Tingkat Mula (SD / MI)
            if (! Schema::hasColumn('competition_events', 'bank_mula_name_2')) {
                $table->string('bank_mula_name_2', 100)->nullable()->after('bank_mula_holder');
            }
            if (! Schema::hasColumn('competition_events', 'bank_mula_number_2')) {
                $table->string('bank_mula_number_2', 100)->nullable()->after('bank_mula_name_2');
            }
            if (! Schema::hasColumn('competition_events', 'bank_mula_holder_2')) {
                $table->string('bank_mula_holder_2', 150)->nullable()->after('bank_mula_number_2');
            }

            // Rekening 2 Khusus Tingkat Madya (SMP / MTs)
            if (! Schema::hasColumn('competition_events', 'bank_madya_name_2')) {
                $table->string('bank_madya_name_2', 100)->nullable()->after('bank_madya_holder');
            }
            if (! Schema::hasColumn('competition_events', 'bank_madya_number_2')) {
                $table->string('bank_madya_number_2', 100)->nullable()->after('bank_madya_name_2');
            }
            if (! Schema::hasColumn('competition_events', 'bank_madya_holder_2')) {
                $table->string('bank_madya_holder_2', 150)->nullable()->after('bank_madya_number_2');
            }

            // Rekening 2 Khusus Tingkat Wira (SMA / SMK / MA)
            if (! Schema::hasColumn('competition_events', 'bank_wira_name_2')) {
                $table->string('bank_wira_name_2', 100)->nullable()->after('bank_wira_holder');
            }
            if (! Schema::hasColumn('competition_events', 'bank_wira_number_2')) {
                $table->string('bank_wira_number_2', 100)->nullable()->after('bank_wira_name_2');
            }
            if (! Schema::hasColumn('competition_events', 'bank_wira_holder_2')) {
                $table->string('bank_wira_holder_2', 150)->nullable()->after('bank_wira_number_2');
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
                'bank_mula_name_2', 'bank_mula_number_2', 'bank_mula_holder_2',
                'bank_madya_name_2', 'bank_madya_number_2', 'bank_madya_holder_2',
                'bank_wira_name_2', 'bank_wira_number_2', 'bank_wira_holder_2',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('competition_events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

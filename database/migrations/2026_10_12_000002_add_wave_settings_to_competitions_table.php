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
        if (Schema::hasTable('competition_events')) {
            Schema::table('competition_events', function (Blueprint $table) {
                if (!Schema::hasColumn('competition_events', 'wave1_name')) {
                    $table->string('wave1_name')->default('Gelombang 1')->after('registration_fee');
                }
                if (!Schema::hasColumn('competition_events', 'wave1_start_date')) {
                    $table->dateTime('wave1_start_date')->nullable()->after('wave1_name');
                }
                if (!Schema::hasColumn('competition_events', 'wave1_end_date')) {
                    $table->dateTime('wave1_end_date')->nullable()->after('wave1_start_date');
                }
                if (!Schema::hasColumn('competition_events', 'wave2_name')) {
                    $table->string('wave2_name')->default('Gelombang 2')->after('wave1_end_date');
                }
                if (!Schema::hasColumn('competition_events', 'wave2_start_date')) {
                    $table->dateTime('wave2_start_date')->nullable()->after('wave2_name');
                }
                if (!Schema::hasColumn('competition_events', 'wave2_end_date')) {
                    $table->dateTime('wave2_end_date')->nullable()->after('wave2_start_date');
                }
                if (!Schema::hasColumn('competition_events', 'is_waves_enabled')) {
                    $table->boolean('is_waves_enabled')->default(true)->after('wave2_end_date');
                }
            });
        }

        if (Schema::hasTable('competition_categories')) {
            Schema::table('competition_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('competition_categories', 'registration_fee_wave1')) {
                    $table->decimal('registration_fee_wave1', 12, 2)->nullable()->after('registration_fee');
                }
                if (!Schema::hasColumn('competition_categories', 'registration_fee_wave2')) {
                    $table->decimal('registration_fee_wave2', 12, 2)->nullable()->after('registration_fee_wave1');
                }
            });
        }

        if (Schema::hasTable('competition_registrations')) {
            Schema::table('competition_registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('competition_registrations', 'registration_wave')) {
                    $table->unsignedTinyInteger('registration_wave')->default(1)->after('total_payment');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('competition_events')) {
            Schema::table('competition_events', function (Blueprint $table) {
                $columns = ['wave1_name', 'wave1_start_date', 'wave1_end_date', 'wave2_name', 'wave2_start_date', 'wave2_end_date', 'is_waves_enabled'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('competition_events', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('competition_categories')) {
            Schema::table('competition_categories', function (Blueprint $table) {
                $columns = ['registration_fee_wave1', 'registration_fee_wave2'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('competition_categories', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('competition_registrations')) {
            Schema::table('competition_registrations', function (Blueprint $table) {
                if (Schema::hasColumn('competition_registrations', 'registration_wave')) {
                    $table->dropColumn('registration_wave');
                }
            });
        }
    }
};

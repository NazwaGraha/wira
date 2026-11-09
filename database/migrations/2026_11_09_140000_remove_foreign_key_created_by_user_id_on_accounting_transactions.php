<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On MySQL / MariaDB, drop foreign key constraint if it exists
        if (DB::getDriverName() === 'mysql') {
            try {
                $hasConstraint = DB::table('information_schema.TABLE_CONSTRAINTS')
                    ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
                    ->where('TABLE_NAME', 'accounting_transactions')
                    ->where('CONSTRAINT_NAME', 'accounting_transactions_created_by_user_id_foreign')
                    ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
                    ->exists();

                if ($hasConstraint) {
                    Schema::table('accounting_transactions', function (Blueprint $table) {
                        $table->dropForeign('accounting_transactions_created_by_user_id_foreign');
                    });
                }
            } catch (Throwable $e) {
                // Ignore if constraint does not exist
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('accounting_transactions', function (Blueprint $table) {
                $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }
};

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
        // 1. Chart of Accounts (Bagan Akun)
        Schema::create('accounting_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->index();
            $table->string('name', 150);
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense'])->index();
            $table->string('category', 50)->default('current_asset')->index();
            $table->foreignId('parent_id')->nullable()->constrained('accounting_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Master Proyek Kegiatan / Cost Center
        Schema::create('accounting_projects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()->index();
            $table->string('name', 150);
            $table->string('category', 50)->default('competition'); // competition, diklat, social_service, routine_operational, other
            $table->string('status', 30)->default('active')->index(); // planning, active, completed, archived
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('budget_target_revenue')->default(0);
            $table->unsignedBigInteger('budget_limit_expense')->default(0);
            $table->string('person_in_charge', 100)->nullable(); // Ketua Pelaksana
            $table->string('treasurer_name', 100)->nullable(); // Bendahara Proyek
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Header Transaksi / Voucher Kas
        Schema::create('accounting_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number', 50)->unique()->index(); // BKM-..., BKK-..., TRF-...
            $table->string('type', 30)->index(); // cash_in, cash_out, transfer, journal
            $table->date('transaction_date')->index();
            $table->foreignId('accounting_project_id')->nullable()->constrained('accounting_projects')->nullOnDelete();
            $table->string('recipient_or_payer', 150)->nullable();
            $table->text('description');
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('reference_number', 100)->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('posted')->index(); // draft, posted, void
            $table->timestamps();
        });

        // 4. Detail Jurnal Berpasangan (Double Entry Ledger Lines)
        Schema::create('accounting_transaction_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_transaction_id')->constrained('accounting_transactions')->cascadeOnDelete();
            $table->foreignId('accounting_account_id')->constrained('accounting_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('debit')->default(0);
            $table->unsignedBigInteger('credit')->default(0);
            $table->string('memo', 255)->nullable();
            $table->timestamps();

            $table->index(['accounting_transaction_id', 'accounting_account_id'], 'acc_trans_lines_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_transaction_lines');
        Schema::dropIfExists('accounting_transactions');
        Schema::dropIfExists('accounting_projects');
        Schema::dropIfExists('accounting_accounts');
    }
};

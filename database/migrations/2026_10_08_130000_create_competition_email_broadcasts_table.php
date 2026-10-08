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
        Schema::create('competition_email_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->string('headline')->nullable();
            $table->longText('content');
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('target_scope')->default('all'); // all, current_event, selected_event
            $table->string('target_level')->default('all'); // all, Mula, Madya, Wira
            $table->string('target_status')->default('all'); // all, verified
            $table->integer('recipient_count')->default(0);
            $table->json('recipients_data')->nullable(); // detailed list of recipient emails & schools
            $table->string('sent_by')->nullable();
            $table->string('status')->default('sent'); // sent, failed, draft
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_email_broadcasts');
    }
};

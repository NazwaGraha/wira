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
        if (!Schema::hasTable('competition_info_menus')) {
            Schema::create('competition_info_menus', function (Blueprint $table) {
                $table->id();
                $table->foreignId('competition_event_id')->nullable()->constrained('competition_events')->nullOnDelete();
                $table->string('title');
                $table->string('category_badge')->default('Informasi');
                $table->string('icon')->default('fa-solid fa-folder-open');
                $table->string('color_theme')->default('red'); // red, sky, rose, amber, emerald, purple, cyan, indigo
                $table->text('description')->nullable();
                $table->string('action_type')->default('notice'); // file, link, whatsapp, notice
                $table->string('file_path')->nullable();
                $table->text('url_link')->nullable();
                $table->string('button_text')->default('Lihat Informasi');
                $table->integer('order_position')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_info_menus');
    }
};

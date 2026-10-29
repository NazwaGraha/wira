<?php

use App\Models\CompetitionInfoMenu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('competition_info_menus')) {
            CompetitionInfoMenu::where('category_badge', 'like', '%Galeri%')
                ->orWhere('title', 'like', '%Dokumentasi%')
                ->orWhere('button_text', 'like', '%Galeri%')
                ->update([
                    'action_type' => 'link',
                    'url_link' => 'https://www.instagram.com/pmr_sman1c',
                    'button_text' => 'Buka Galeri Foto & Video',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

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
        // 1. Tambah kolom role, username, phone ke tabel users
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('admin')->after('username'); // 'superuser', 'admin', 'jury'
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });

        // 2. Buat tabel pivot pemetaan tugas juri ke cabang lomba
        if (! Schema::hasTable('jury_category_assignments')) {
            Schema::create('jury_category_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('competition_category_id')->constrained('competition_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'competition_category_id'], 'uq_jury_category');
            });
        }

        // 3. Tambah kolom jury_user_id ke tabel competition_scores
        Schema::table('competition_scores', function (Blueprint $table) {
            if (! Schema::hasColumn('competition_scores', 'jury_user_id')) {
                $table->foreignId('jury_user_id')->nullable()->after('competition_participant_team_id')
                    ->constrained('users')->nullOnDelete();
            }
        });

        // 4. Tambah kolom is_photo_required ke competition_categories
        Schema::table('competition_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('competition_categories', 'is_photo_required')) {
                $table->boolean('is_photo_required')->default(false)->after('scoring_type');
            }
        });

        // 5. Buat tabel competition_score_photos untuk dokumentasi foto peserta
        if (! Schema::hasTable('competition_score_photos')) {
            Schema::create('competition_score_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('competition_score_id')->nullable()->constrained('competition_scores')->cascadeOnDelete();
                $table->foreignId('competition_participant_team_id')->constrained('competition_participant_teams')->cascadeOnDelete();
                $table->string('photo_path');
                $table->string('caption')->nullable();
                $table->string('file_size')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_score_photos');

        Schema::table('competition_categories', function (Blueprint $table) {
            if (Schema::hasColumn('competition_categories', 'is_photo_required')) {
                $table->dropColumn('is_photo_required');
            }
        });

        Schema::table('competition_scores', function (Blueprint $table) {
            if (Schema::hasColumn('competition_scores', 'jury_user_id')) {
                $table->dropForeign(['jury_user_id']);
                $table->dropColumn('jury_user_id');
            }
        });

        Schema::dropIfExists('jury_category_assignments');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};

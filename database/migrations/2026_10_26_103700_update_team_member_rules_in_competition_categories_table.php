<?php

use App\Models\CompetitionCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $rules = [
            'pertolongan pertama' => 3,
            'tandu' => 2,
            'cuci tangan' => 2,
            'mewarnai' => 1,
            'cepat tepat' => 3,
            'pendidikan remaja sebaya' => 2,
            'favorite' => 2,
            'olimpiade' => 1,
            'mading' => 2,
        ];

        foreach (CompetitionCategory::all() as $cat) {
            $nameLower = strtolower($cat->name ?? '');
            foreach ($rules as $keyword => $count) {
                if (str_contains($nameLower, $keyword)) {
                    $cat->update(['max_team_members' => $count]);
                    break;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        CompetitionCategory::query()->update(['max_team_members' => 1]);
    }
};

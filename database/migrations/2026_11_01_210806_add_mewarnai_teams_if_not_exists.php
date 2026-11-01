<?php

use App\Models\CompetitionCategory;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cat = CompetitionCategory::where('code', 'MEWARNAI-MULA')->first();
        if (! $cat) {
            return;
        }

        $registrations = CompetitionRegistration::where('level', 'Mula')->where('status', 'verified')->get();
        foreach ($registrations as $r) {
            $exists = CompetitionParticipantTeam::where('competition_category_id', $cat->id)
                ->where('competition_registration_id', $r->id)
                ->exists();

            if (! $exists) {
                $count = CompetitionParticipantTeam::where('competition_category_id', $cat->id)->count();
                CompetitionParticipantTeam::create([
                    'competition_category_id' => $cat->id,
                    'competition_registration_id' => $r->id,
                    'team_name' => 'Peserta Mewarnai '.$r->school_name,
                    'team_label' => $r->school_name,
                    'order_number' => $count + 1,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

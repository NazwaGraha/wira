<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompetitionScoreMascotImageTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::create([
            'name' => 'Administrator PMR',
            'username' => 'admin_test_mascot',
            'email' => 'admin_mascot@pmrwirasmanic.or.id',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);
    }

    private function createEventWithCategories(): CompetitionEvent
    {
        $event = CompetitionEvent::create([
            'name' => 'Sua Bhakti Berkarya IV',
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sua-bhakti-berkarya-iv',
            'start_date' => '2026-10-24',
            'end_date' => '2026-10-25',
            'is_active' => true,
        ]);

        // Mula
        CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama (Putra)',
            'code' => 'LPP-MULA-PA',
            'level' => 'Mula',
            'gender_category' => 'Putra',
            'scoring_type' => 'practical_time',
            'is_active' => true,
        ]);
        CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama (Putri)',
            'code' => 'LPP-MULA-PI',
            'level' => 'Mula',
            'gender_category' => 'Putri',
            'scoring_type' => 'practical_time',
            'is_active' => true,
        ]);

        // Madya
        CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama (Putra)',
            'code' => 'LPP-MADYA-PA',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'practical_time',
            'is_active' => true,
        ]);
        CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama (Putri)',
            'code' => 'LPP-MADYA-PI',
            'level' => 'Madya',
            'gender_category' => 'Putri',
            'scoring_type' => 'practical_time',
            'is_active' => true,
        ]);

        // Wira
        CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama (Putra)',
            'code' => 'LPP-WIRA-PA',
            'level' => 'Wira',
            'gender_category' => 'Putra',
            'scoring_type' => 'practical_time',
            'is_active' => true,
        ]);

        return $event;
    }

    public function test_scores_index_displays_correct_mascot_images_for_mula(): void
    {
        $admin = $this->createAdminUser();
        $this->createEventWithCategories();

        $response = $this->actingAs($admin)->get(route('admin.competition-scores.index', ['level' => 'Mula']));
        $response->assertStatus(200);
        $response->assertSee('images/mascot/MulaPutra.png', false);
        $response->assertSee('images/mascot/MulaPutri.png', false);
    }

    public function test_scores_index_displays_correct_mascot_images_for_madya(): void
    {
        $admin = $this->createAdminUser();
        $this->createEventWithCategories();

        $response = $this->actingAs($admin)->get(route('admin.competition-scores.index', ['level' => 'Madya']));
        $response->assertStatus(200);
        $response->assertSee('images/mascot/MadyaPutra.png', false);
        $response->assertSee('images/mascot/MadyaPutri.png', false);
    }

    public function test_scores_index_displays_correct_mascot_images_for_wira(): void
    {
        $admin = $this->createAdminUser();
        $this->createEventWithCategories();

        $response = $this->actingAs($admin)->get(route('admin.competition-scores.index', ['level' => 'Wira']));
        $response->assertStatus(200);
        $response->assertSee('images/mascot/WiraPutra.png', false);
    }
}

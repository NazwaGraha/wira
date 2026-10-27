<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompetitionCategoryTier4Test extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::create([
            'name' => 'Administrator PMR',
            'username' => 'admin_test',
            'email' => 'admin_test@pmrwirasmanic.or.id',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);
    }

    private function createEvent(): CompetitionEvent
    {
        return CompetitionEvent::create([
            'name' => 'Sua Bhakti Berkarya IV',
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sua-bhakti-berkarya-iv',
            'start_date' => '2026-10-24',
            'end_date' => '2026-10-25',
            'is_active' => true,
        ]);
    }

    public function test_category_tier_4_returns_zero_points_for_all_ranks(): void
    {
        $event = $this->createEvent();

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Lomba Eksibisi Baris Berbaris',
            'code' => 'PBB-EKS',
            'level' => 'Wira',
            'gender_category' => 'Umum',
            'scoring_type' => 'multi_criteria',
            'point_tier' => 'tier_4',
            'is_active' => true,
        ]);

        $this->assertEquals(0, $category->getPointsForRank(1));
        $this->assertEquals(0, $category->getPointsForRank(2));
        $this->assertEquals(0, $category->getPointsForRank(3));
        $this->assertEquals(0, $category->getPointsForRank(4));
        $this->assertEquals(0, $category->getPointsForRank(5));
        $this->assertEquals('0 Poin', $category->getPointTierLabel());
    }

    public function test_admin_can_store_category_with_tier_4(): void
    {
        $admin = $this->createAdminUser();
        $event = $this->createEvent();

        $response = $this->actingAs($admin)->post(route('admin.competition-event.categories.store', $event->id), [
            'name' => 'Lomba Suporter Terbaik',
            'code' => 'SUP-WIRA',
            'level' => 'Wira',
            'gender_category' => 'Umum',
            'scoring_type' => 'multi_criteria',
            'point_tier' => 'tier_4',
            'registration_fee' => 100000,
            'max_team_members' => 10,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.competition-event.edit', $event->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('competition_categories', [
            'competition_event_id' => $event->id,
            'name' => 'Lomba Suporter Terbaik',
            'point_tier' => 'tier_4',
        ]);
    }

    public function test_admin_can_update_category_to_tier_4(): void
    {
        $admin = $this->createAdminUser();
        $event = $this->createEvent();

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Lomba Yel-Yel Kreatif',
            'code' => 'YEL-WIRA',
            'level' => 'Wira',
            'gender_category' => 'Umum',
            'scoring_type' => 'multi_criteria',
            'point_tier' => 'tier_1',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.competition-event.categories.update', [
            'event' => $event->id,
            'category' => $category->id,
        ]), [
            'name' => 'Lomba Yel-Yel Kreatif Updated',
            'code' => 'YEL-WIRA',
            'level' => 'Wira',
            'gender_category' => 'Umum',
            'scoring_type' => 'multi_criteria',
            'point_tier' => 'tier_4',
            'registration_fee' => 150000,
            'max_team_members' => 5,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.competition-event.edit', $event->id));
        $response->assertSessionHas('success');

        $category->refresh();
        $this->assertEquals('tier_4', $category->point_tier);
        $this->assertEquals(0, $category->getPointsForRank(1));
    }
}

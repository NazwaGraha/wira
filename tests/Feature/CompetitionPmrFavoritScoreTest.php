<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionPmrFavoritScoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected CompetitionEvent $event;

    protected CompetitionCategory $favCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->event = CompetitionEvent::create([
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sbb-iv-2026',
            'is_active' => true,
        ]);

        $this->favCategory = CompetitionCategory::create([
            'competition_event_id' => $this->event->id,
            'code' => 'FAV-MADYA',
            'name' => 'PMR Favorite',
            'level' => 'Madya',
            'gender_category' => 'Umum',
            'scoring_type' => 'social_engagement',
            'point_tier' => 'tier_3',
            'is_active' => true,
        ]);
    }

    public function test_pmr_favorit_scoring_calculates_with_new_formula(): void
    {
        $reg = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMPN 1 BOGOR',
            'registration_code' => 'REG-SMPN1BOGOR',
            'level' => 'Madya',
            'advisor_name' => 'Pak Hendra',
            'advisor_phone' => '08123456789',
            'status' => 'verified',
        ]);

        $team = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg->id,
            'competition_category_id' => $this->favCategory->id,
            'team_name' => 'SMPN 1 BOGOR Regu A',
            'level' => 'Madya',
        ]);

        // Submit score via Admin scoring endpoint: 100 Likes, 20 Stickers
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.save', $this->favCategory->id), [
                'scores' => [
                    $team->id => [
                        'likes' => 100,
                        'stickers' => 20,
                    ],
                ],
            ]);

        $response->assertSessionHasNoErrors();

        // 100 * 1 + 20 * 2 = 140
        $score = CompetitionScore::where('competition_participant_team_id', $team->id)->first();
        $this->assertNotNull($score);
        $this->assertEquals(140, (int) $score->final_score);
        $this->assertEquals(100, $score->score_details['likes']);
        $this->assertEquals(20, $score->score_details['stickers']);
    }

    public function test_pmr_favorit_ranking_and_scoreboard_display(): void
    {
        $reg1 = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SDN CIAWI 01',
            'registration_code' => 'REG-CIAWI01',
            'level' => 'Madya',
            'advisor_name' => 'Ibu Siti',
            'advisor_phone' => '08123456788',
            'status' => 'verified',
        ]);
        $team1 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg1->id,
            'competition_category_id' => $this->favCategory->id,
            'team_name' => 'SDN CIAWI 01 Regu A',
            'level' => 'Madya',
        ]);

        $reg2 = CompetitionRegistration::create([
            'competition_event_id' => $this->event->id,
            'school_name' => 'SMPN 1 BOGOR',
            'registration_code' => 'REG-SMPN1',
            'level' => 'Madya',
            'advisor_name' => 'Pak Budi',
            'advisor_phone' => '08123456787',
            'status' => 'verified',
        ]);
        $team2 = CompetitionParticipantTeam::create([
            'competition_registration_id' => $reg2->id,
            'competition_category_id' => $this->favCategory->id,
            'team_name' => 'SMPN 1 BOGOR Regu A',
            'level' => 'Madya',
        ]);

        // Team 1: 120 Likes, 35 Stickers => 120 + 70 = 190 (Rank 1)
        // Team 2: 100 Likes, 20 Stickers => 100 + 40 = 140 (Rank 2)
        $this->actingAs($this->adminUser)
            ->post(route('admin.competition-scores.save', $this->favCategory->id), [
                'scores' => [
                    $team1->id => ['likes' => 120, 'stickers' => 35],
                    $team2->id => ['likes' => 100, 'stickers' => 20],
                ],
            ]);

        $score1 = CompetitionScore::where('competition_participant_team_id', $team1->id)->first();
        $score2 = CompetitionScore::where('competition_participant_team_id', $team2->id)->first();

        $this->assertEquals(190, (int) $score1->final_score);
        $this->assertEquals(1, $score1->rank);

        $this->assertEquals(140, (int) $score2->final_score);
        $this->assertEquals(2, $score2->rank);

        // Check public scoreboard view
        $scoreboardResponse = $this->get(route('lomba.scoreboard', ['category_id' => $this->favCategory->id]));
        $scoreboardResponse->assertStatus(200);
        $scoreboardResponse->assertSee('SDN CIAWI 01 Regu A');
        $scoreboardResponse->assertSee('190');
        $scoreboardResponse->assertSee('SMPN 1 BOGOR Regu A');
        $scoreboardResponse->assertSee('140');
        $scoreboardResponse->assertSee('Stiker: <strong>20</strong> (40 Poin)', false);
    }
}

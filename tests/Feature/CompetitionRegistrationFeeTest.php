<?php

namespace Tests\Feature;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompetitionRegistrationFeeTest extends TestCase
{
    use RefreshDatabase;

    private function createEventWithCategories(): array
    {
        $event = CompetitionEvent::create([
            'name' => 'Sua Bhakti Berkarya IV',
            'title' => 'Sua Bhakti Berkarya IV 2026',
            'slug' => 'sua-bhakti-berkarya-iv',
            'start_date' => '2026-10-24',
            'end_date' => '2026-10-25',
            'registration_fee' => 150000,
            'is_active' => true,
            'is_registration_open' => true,
            'is_waves_enabled' => false,
        ]);

        $catPaid1 = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama',
            'code' => 'LPP-MADYA-PA',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'practical_time',
            'registration_fee' => 75000,
            'is_active' => true,
        ]);

        $catPaid2 = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Ketangkasan Tandu Reguler',
            'code' => 'LKTR-MADYA-PA',
            'level' => 'Madya',
            'gender_category' => 'Putra',
            'scoring_type' => 'standard_time',
            'registration_fee' => 60000,
            'is_active' => true,
        ]);

        $catFree = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'PMR Favorite',
            'code' => 'FAV-MADYA',
            'level' => 'Madya',
            'gender_category' => 'Umum',
            'scoring_type' => 'social_engagement',
            'registration_fee' => 0,
            'is_active' => true,
        ]);

        return [$event, $catPaid1, $catPaid2, $catFree];
    }

    public function test_registration_page_contains_data_fee_zero_and_breakdown_container(): void
    {
        [$event, $catPaid1, $catPaid2, $catFree] = $this->createEventWithCategories();

        $response = $this->get(route('lomba.register', ['level' => 'Madya']));

        $response->assertStatus(200);
        $response->assertSee('data-fee="0"', false);
        $response->assertSee('data-fee="75000"', false);
        $response->assertSee('data-fee="60000"', false);
        $response->assertSee('fee-breakdown-list');
    }

    public function test_registration_store_calculates_zero_fee_category_correctly(): void
    {
        Storage::fake('public');
        [$event, $catPaid1, $catPaid2, $catFree] = $this->createEventWithCategories();

        $fakeProof = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->post(route('lomba.store'), [
            'school_name' => 'SMP OGAH HIRUP',
            'level' => 'Madya',
            'advisor_name' => 'Pembina Test',
            'advisor_phone' => '08123456789',
            'advisor_email' => 'pembina@test.com',
            'school_address' => 'Jl. Test No. 1',
            'categories' => [$catPaid1->id, $catPaid2->id, $catFree->id],
            'team_labels' => [
                $catPaid1->id => ['Regu A', 'Regu B'], // 2 x 75.000 = 150.000
                $catPaid2->id => ['Regu A'],          // 1 x 60.000 = 60.000
                $catFree->id => ['Regu A', 'Regu B'],  // 2 x 0 = 0
            ],
            'payment_proof' => $fakeProof,
        ]);

        $response->assertSessionHasNoErrors();

        $registration = CompetitionRegistration::where('school_name', 'SMP OGAH HIRUP')->first();
        $this->assertNotNull($registration);

        // Total seharusnya: 150.000 + 60.000 + 0 = 210.000 (bukan 510.000)
        $this->assertEquals(210000, floatval($registration->total_payment));
        $this->assertEquals(5, $registration->teams()->count());

        // Verifikasi receipt view
        $receiptResponse = $this->get(route('lomba.kwitansi', $registration->registration_code));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('Rp 210.000');
    }
}

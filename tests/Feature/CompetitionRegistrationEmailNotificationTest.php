<?php

namespace Tests\Feature;

use App\Mail\RegistrationSubmittedMail;
use App\Mail\RegistrationVerifiedMail;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompetitionRegistrationEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_sends_email_notification_with_pdf_attachments_on_registration(): void
    {
        Mail::fake();
        Storage::fake('public');

        $event = CompetitionEvent::create([
            'title' => 'SUA BHAKTI BERKARYA 2026',
            'slug' => 'sbb-2026',
            'year' => 2026,
            'is_active' => true,
            'registration_fee' => 150000,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Pertolongan Pertama',
            'level' => 'Wira',
            'gender_category' => 'Campuran',
            'fee_wave1' => 150000,
            'fee_wave2' => 175000,
            'is_active' => true,
        ]);

        $payload = [
            'school_name' => 'SMAN 1 Sukaraja',
            'level' => 'Wira',
            'advisor_name' => 'Drs. Hendra',
            'advisor_phone' => '081234567890',
            'advisor_email' => 'hendra@sman1sukaraja.sch.id',
            'school_address' => 'Jl. Sukaraja No. 10',
            'categories' => [$category->id],
            'team_labels' => [
                $category->id => ['Tim A'],
            ],
            'payment_proof' => UploadedFile::fake()->image('bukti_transfer.jpg'),
        ];

        $response = $this->post(route('lomba.store'), $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $registration = CompetitionRegistration::where('advisor_email', 'hendra@sman1sukaraja.sch.id')->first();
        $this->assertNotNull($registration);

        // Pastikan Mail::send dipanggil ke email pembina
        Mail::assertSent(RegistrationSubmittedMail::class, function ($mail) use ($registration) {
            return $mail->hasTo('hendra@sman1sukaraja.sch.id')
                && $mail->registration->id === $registration->id;
        });

        // Verifikasi lampiran PDF pada RegistrationSubmittedMail
        $mailable = new RegistrationSubmittedMail($registration);
        $attachments = $mailable->attachments();

        $this->assertCount(2, $attachments);
    }

    public function test_registration_verified_mail_contains_pdf_attachments(): void
    {
        $event = CompetitionEvent::create([
            'title' => 'SUA BHAKTI BERKARYA 2026',
            'slug' => 'sbb-2026',
            'year' => 2026,
            'is_active' => true,
            'registration_fee' => 150000,
        ]);

        $category = CompetitionCategory::create([
            'competition_event_id' => $event->id,
            'name' => 'Tandu Darurat',
            'level' => 'Wira',
            'gender_category' => 'Putra',
            'fee_wave1' => 150000,
            'fee_wave2' => 175000,
            'is_active' => true,
        ]);

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-TEST99',
            'school_name' => 'SMAN 1 Megamendung',
            'level' => 'Wira',
            'advisor_name' => 'Iwan',
            'advisor_phone' => '081299998888',
            'advisor_email' => 'iwan@megamendung.sch.id',
            'school_address' => 'Jl. Raya Puncak',
            'total_payment' => 150000,
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        CompetitionParticipantTeam::create([
            'competition_registration_id' => $registration->id,
            'competition_category_id' => $category->id,
            'team_name' => 'SMAN 1 Megamendung Regu A',
            'team_label' => 'Regu A',
            'members_list' => [],
        ]);

        $mailable = new RegistrationVerifiedMail($registration);
        $attachments = $mailable->attachments();

        $this->assertCount(2, $attachments);
    }
}

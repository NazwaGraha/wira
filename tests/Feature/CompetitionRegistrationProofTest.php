<?php

namespace Tests\Feature;

use App\Models\CompetitionEvent;
use App\Models\CompetitionRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompetitionRegistrationProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_payment_proof(): void
    {
        $event = CompetitionEvent::create([
            'title' => 'Lomba PMR 2026',
            'slug' => 'lomba-pmr-2026',
            'year' => 2026,
            'is_active' => true,
        ]);

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-TEST01',
            'school_name' => 'SMAN 1 Test',
            'level' => 'Wira',
            'advisor_name' => 'Budi',
            'advisor_phone' => '081234567890',
            'advisor_email' => 'budi@test.com',
            'school_address' => 'Jl. Test No. 1',
            'total_payment' => 150000,
            'status' => 'pending',
            'payment_proof' => 'competition_payments/test_proof.jpg',
        ]);

        $response = $this->get(route('admin.competition-registrations.proof', $registration->id));
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_uploaded_payment_proof_file(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('proof.jpg');
        $storedPath = $file->storeAs('competition_payments', 'proof_test_123.jpg', 'public');

        // Login as admin
        $this->post('/admin/login', [
            'login_id' => 'admin',
            'password' => 'pmrwirasmanic',
        ]);

        $event = CompetitionEvent::create([
            'title' => 'Lomba PMR 2026',
            'slug' => 'lomba-pmr-2026',
            'year' => 2026,
            'is_active' => true,
        ]);

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-TEST02',
            'school_name' => 'SMAN 2 Test',
            'level' => 'Wira',
            'advisor_name' => 'Siti',
            'advisor_phone' => '081298765432',
            'advisor_email' => 'siti@test.com',
            'school_address' => 'Jl. Test No. 2',
            'total_payment' => 150000,
            'status' => 'pending',
            'payment_proof' => $storedPath,
        ]);

        $response = $this->get(route('admin.competition-registrations.proof', $registration->id));
        $response->assertOk();
        $this->assertEquals('image/jpeg', $response->headers->get('content-type'));
    }

    public function test_payment_proof_returns_404_if_file_missing(): void
    {
        // Login as admin
        $this->post('/admin/login', [
            'login_id' => 'admin',
            'password' => 'pmrwirasmanic',
        ]);

        $event = CompetitionEvent::create([
            'title' => 'Lomba PMR 2026',
            'slug' => 'lomba-pmr-2026',
            'year' => 2026,
            'is_active' => true,
        ]);

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => 'SBB-TEST03',
            'school_name' => 'SMAN 3 Test',
            'level' => 'Wira',
            'advisor_name' => 'Andi',
            'advisor_phone' => '081211112222',
            'advisor_email' => 'andi@test.com',
            'school_address' => 'Jl. Test No. 3',
            'total_payment' => 150000,
            'status' => 'pending',
            'payment_proof' => 'competition_payments/non_existent.jpg',
        ]);

        $response = $this->get(route('admin.competition-registrations.proof', $registration->id));
        $response->assertNotFound();
    }
}

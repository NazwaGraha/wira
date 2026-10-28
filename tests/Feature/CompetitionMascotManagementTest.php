<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompetitionMascotManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Mascot Test',
            'username' => 'admin_mascot_tester',
            'email' => 'admin_mascot_tester@pmrwirasmanic.or.id',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_admin_can_access_mascot_management_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.competition-mascots.index', ['level' => 'Madya']));

        $response->assertStatus(200);
        $response->assertSee('Kelola Gambar Maskot Lomba');
        $response->assertSee('PMR MADYA');
        $response->assertSee('MadyaPutra.png');
    }

    public function test_admin_can_upload_and_update_mascot_image(): void
    {
        $admin = $this->createAdmin();

        $mascotPath = public_path('images/mascot/MulaPutra.png');
        $originalContent = File::exists($mascotPath) ? File::get($mascotPath) : null;

        try {
            $fakeImage = UploadedFile::fake()->image('test_mascot.png', 400, 600);

            $response = $this->actingAs($admin)->post(route('admin.competition-mascots.update'), [
                'level' => 'Mula',
                'gender_category' => 'Putra',
                'mascot_image' => $fakeImage,
            ]);

            $response->assertRedirect(route('admin.competition-mascots.index', ['level' => 'Mula']));
            $response->assertSessionHas('success');

            $this->assertTrue(File::exists($mascotPath));
        } finally {
            if ($originalContent !== null) {
                File::put($mascotPath, $originalContent);
                File::put(public_path('images/mascot/mula/Kategori_Putra.png'), $originalContent);
                File::put(public_path('images/mascot/pmr_mula_putra.png'), $originalContent);
            }
        }
    }

    public function test_upload_validates_required_fields_and_image_format(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.competition-mascots.update'), [
            'level' => 'InvalidLevel',
            'gender_category' => 'InvalidGender',
        ]);

        $response->assertSessionHasErrors(['level', 'gender_category', 'mascot_image']);
    }
}

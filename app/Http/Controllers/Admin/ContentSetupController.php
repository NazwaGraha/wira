<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSectionImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContentSetupController extends Controller
{
    /**
     * Tampilkan formulir pengaturan konten halaman Beranda.
     */
    public function home(): View
    {
        $homeProfile = ContentSectionImage::getSection('home_profile');

        return view('admin.content-setup.home', compact('homeProfile'));
    }

    /**
     * Perbarui konten section Profil Ekstrakurikuler pada halaman Beranda.
     */
    public function updateHomeProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tagline' => 'nullable|string|max:100',
            'tagline_icon' => 'nullable|string|max:100',
            'headline' => 'required|string|max:255',
            'body_text' => 'required|string|max:5000',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'badge_subtext' => 'nullable|string|max:150',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'headline.required' => 'Judul utama section (H1/H2) wajib diisi.',
            'body_text.required' => 'Deskripsi narasi profil (Text Area) wajib diisi.',
            'image.image' => 'Berkas yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran berkas gambar maksimal 5 MB.',
        ]);

        $homeProfile = ContentSectionImage::getSection('home_profile');

        // Filter checklist items agar tidak ada baris kosong
        $checklistItems = [];
        if (! empty($validated['checklist_items']) && is_array($validated['checklist_items'])) {
            $checklistItems = array_values(array_filter(
                $validated['checklist_items'],
                fn ($item) => filled(trim((string) $item))
            ));
        }

        $data = [
            'tagline' => $validated['tagline'] ?? $homeProfile->display_tagline,
            'tagline_icon' => $validated['tagline_icon'] ?? $homeProfile->display_tagline_icon,
            'headline' => $validated['headline'],
            'body_text' => $validated['body_text'],
            'checklist_items' => ! empty($checklistItems) ? $checklistItems : $homeProfile->display_checklist_items,
            'button_text' => $validated['button_text'] ?? $homeProfile->display_button_text,
            'button_url' => $validated['button_url'] ?? $homeProfile->display_button_url,
            'badge_text' => $validated['badge_text'] ?? $homeProfile->display_badge_text,
            'badge_subtext' => $validated['badge_subtext'] ?? $homeProfile->display_badge_subtext,
        ];

        // Jika mengunggah berkas gambar baru
        if ($request->hasFile('image')) {
            $this->deleteOldFile($homeProfile->image_path);
            $data['image_path'] = $this->uploadFile($request->file('image'), 'content-images');
        }

        $homeProfile->update($data);

        return redirect()->route('admin.content-setup.home')
            ->with('success', 'Konten Section Profil Ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Kembalikan konten Profil Ekstrakurikuler ke nilai bawaan.
     */
    public function resetHomeProfile(): RedirectResponse
    {
        $homeProfile = ContentSectionImage::getSection('home_profile');
        $this->deleteOldFile($homeProfile->image_path);

        $homeProfile->update([
            'tagline' => 'Profil Ekstrakurikuler',
            'tagline_icon' => 'fa-solid fa-shield-heart',
            'headline' => 'Membangun Karakter Kemanusiaan Generasi Muda SMAN 1 Ciawi',
            'body_text' => 'Palang Merah Remaja (PMR) Wira SMAN 1 Ciawi adalah wadah pembinaan dan pengembangan anggota remaja PMI tingkat SMA. Kami berkomitmen menanamkan nilai-nilai Tri Bakti PMR serta 7 Prinsip Dasar Palang Merah dan Bulan Sabit Merah Internasional.',
            'checklist_items' => [
                'Meningkatkan keterampilan pertolongan pertama (P3K) dan tanggap darurat bencana.',
                'Menyelenggarakan aksi donor darah sukarela secara berkala bersama UDD PMI.',
                'Membentuk jiwa kepemimpinan, kerelawanan, dan persahabatan antar siswa.',
            ],
            'button_text' => 'Baca Profil Selengkapnya',
            'button_url' => '/tentang-kami',
            'image_path' => '/mockups/02_tentang_kami.jpg',
            'badge_text' => 'Terakreditasi A',
            'badge_subtext' => 'Unit PMR Wira Teladan Kab. Bogor',
        ]);

        return redirect()->route('admin.content-setup.home')
            ->with('success', 'Konten Section Profil Ekstrakurikuler berhasil dikembalikan ke pengaturan bawaan.');
    }

    /**
     * Helper upload berkas ke storage publik.
     */
    private function uploadFile($file, string $targetFolder): string
    {
        $filename = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();

        if (! file_exists(storage_path("app/public/{$targetFolder}"))) {
            File::ensureDirectoryExists(storage_path("app/public/{$targetFolder}"), 0755, true);
        }
        if (! file_exists(public_path("storage/{$targetFolder}"))) {
            File::ensureDirectoryExists(public_path("storage/{$targetFolder}"), 0755, true);
        }

        $file->storeAs($targetFolder, $filename, 'public');

        try {
            $src = storage_path("app/public/{$targetFolder}/{$filename}");
            $dst = public_path("storage/{$targetFolder}/{$filename}");
            if (file_exists($src) && ! file_exists($dst)) {
                @copy($src, $dst);
            }
        } catch (\Throwable $e) {
            // Abaikan kesalahan penyalinan sekunder
        }

        return "{$targetFolder}/{$filename}";
    }

    /**
     * Hapus berkas lama jika tersimpan di storage lokal.
     */
    private function deleteOldFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/mockups')) {
            return;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $path), '/');

        try {
            $storageFile = storage_path('app/public/'.$cleanPath);
            $publicFile = public_path('storage/'.$cleanPath);

            if (file_exists($storageFile)) {
                @unlink($storageFile);
            }
            if (file_exists($publicFile)) {
                @unlink($publicFile);
            }
        } catch (\Throwable $e) {
            // Abaikan
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSectionImage;
use App\Models\HeroSlide;
use App\Models\PopupBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MediaManagerController extends Controller
{
    /**
     * Tampilkan antarmuka terpusat Manajemen Gambar & Banner Konten Web.
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'hero');
        if (! in_array($activeTab, ['hero', 'home_profile', 'about_intro', 'popup'], true)) {
            $activeTab = 'hero';
        }

        $heroSlides = HeroSlide::orderBy('order_position')->get();
        $homeProfileImage = ContentSectionImage::getSection('home_profile');
        $aboutIntroImage = ContentSectionImage::getSection('about_intro');

        $popupBanner = null;
        try {
            PopupBannerController::ensureTableExists();
            if (Schema::hasTable('popup_banners')) {
                $popupBanner = PopupBanner::latest()->first();
            }
        } catch (\Throwable $e) {
            $popupBanner = null;
        }

        return view('admin.media.index', compact(
            'activeTab',
            'heroSlides',
            'homeProfileImage',
            'aboutIntroImage',
            'popupBanner'
        ));
    }

    /**
     * Perbarui gambar tunggal (single-image) pada section konten tertentu.
     */
    public function updateSection(Request $request)
    {
        $validated = $request->validate([
            'section_key' => 'required|in:home_profile,about_intro',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_path' => 'nullable|string|max:500',
            'badge_text' => 'nullable|string|max:100',
            'badge_subtext' => 'nullable|string|max:150',
        ], [
            'image.image' => 'Berkas yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran berkas gambar maksimal 5 MB.',
        ]);

        $section = ContentSectionImage::getSection($validated['section_key']);

        $data = [
            'badge_text' => $validated['badge_text'] ?? $section->badge_text,
            'badge_subtext' => $validated['badge_subtext'] ?? $section->badge_subtext,
        ];

        // Jika upload berkas gambar baru
        if ($request->hasFile('image')) {
            $this->deleteOldFile($section->image_path);
            $data['image_path'] = $this->uploadFile($request->file('image'), 'content-images');
        } elseif (! empty($validated['image_path'])) {
            $data['image_path'] = $validated['image_path'];
        }

        $section->update($data);

        $tab = $section->section_key;

        return redirect()->route('admin.media-manager.index', ['tab' => $tab])
            ->with('success', "Gambar section {$section->title} berhasil diperbarui!");
    }

    /**
     * Kembalikan gambar section ke setelan bawaan sistem.
     */
    public function resetSection(Request $request)
    {
        $validated = $request->validate([
            'section_key' => 'required|in:home_profile,about_intro',
        ]);

        $section = ContentSectionImage::getSection($validated['section_key']);
        $this->deleteOldFile($section->image_path);

        $section->update([
            'image_path' => '/mockups/02_tentang_kami.jpg',
        ]);

        return redirect()->route('admin.media-manager.index', ['tab' => $section->section_key])
            ->with('success', "Gambar section {$section->title} berhasil dikembalikan ke bawaan sistem.");
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
            // Ignore copy failure
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
            // Ignore failure
        }
    }
}

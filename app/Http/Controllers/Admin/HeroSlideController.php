<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('order_position')->get();

        return view('admin.hero.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required_without:image_path|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_path' => 'nullable|string|max:500',
            'caption' => 'nullable|string|max:500',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.required_without' => 'Silakan upload berkas gambar banner slider.',
            'image.image' => 'Berkas yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran berkas gambar maksimal 5 MB.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->uploadFile($request->file('image'), 'hero-slides');
        } elseif (empty($validated['image_path'])) {
            return back()->withInput()->withErrors(['image' => 'Silakan upload berkas gambar banner slider terlebih dahulu.']);
        }

        $validated['order_position'] = $validated['order_position'] ?? (HeroSlide::max('order_position') + 1);
        $validated['is_active'] = $request->has('is_active');

        HeroSlide::create($validated);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Gambar slider banner berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $slide = HeroSlide::findOrFail($id);

        return view('admin.hero.edit', compact('slide'));
    }

    public function update(Request $request, $id)
    {
        $slide = HeroSlide::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_path' => 'nullable|string|max:500',
            'caption' => 'nullable|string|max:500',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.image' => 'Berkas yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran berkas gambar maksimal 5 MB.',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteOldFile($slide->image_path);
            $validated['image_path'] = $this->uploadFile($request->file('image'), 'hero-slides');
        } elseif (! empty($validated['image_path'])) {
            $validated['image_path'] = $validated['image_path'];
        } else {
            $validated['image_path'] = $slide->image_path;
        }

        $validated['is_active'] = $request->has('is_active');

        $slide->update($validated);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Gambar slider banner berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $this->deleteOldFile($slide->image_path);
        $slide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Gambar slider banner berhasil dihapus.');
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

        // Copy fallback jika cPanel symlink bermasalah
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
     * Hapus berkas lama jika ada di storage publik lokal.
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopupBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PopupBannerController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan popup banner & iklan.
     */
    public function index()
    {
        self::ensureTableExists();

        // Banner yang saat ini aktif atau banner terbaru
        $currentBanner = PopupBanner::currentlyActive()->latest()->first();
        if (! $currentBanner) {
            $currentBanner = PopupBanner::latest()->first();
        }

        $banners = PopupBanner::latest()->paginate(10);

        // Ringkasan Statistik
        $totalTayang = PopupBanner::sum('impressions_count');
        $totalKlik = PopupBanner::sum('clicks_count');
        $avgCtr = $totalTayang > 0 ? round(($totalKlik / $totalTayang) * 100, 1) : 0.0;

        return view('admin.popup-banner.index', compact(
            'currentBanner',
            'banners',
            'totalTayang',
            'totalKlik',
            'avgCtr'
        ));
    }

    /**
     * Simpan atau perbarui popup banner.
     */
    public function store(Request $request)
    {
        self::ensureTableExists();

        $id = $request->input('id');
        $banner = $id ? PopupBanner::find($id) : null;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => ($banner ? 'nullable' : 'required').'|image|mimes:jpeg,png,jpg,webp|max:5120',
            'target_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'open_in_new_tab' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable',
        ]);

        $targetUrl = ! empty($validated['target_url']) ? trim($validated['target_url']) : null;

        $data = [
            'title' => $validated['title'],
            'target_url' => $targetUrl,
            'button_text' => $validated['button_text'] ?: 'Info Lebih Lanjut',
            'open_in_new_tab' => $request->has('open_in_new_tab'),
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => $request->has('is_active') || $request->input('is_active') == '1' || ! $request->has('id'),
        ];

        // Jika upload gambar baru
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = $this->uploadFile($file, 'popup-banners');
            $data['image_path'] = $imagePath;
        }

        if ($banner) {
            $banner->update($data);
            $message = 'Pengaturan popup banner berhasil diperbarui!';
        } else {
            $data['impressions_count'] = 0;
            $data['clicks_count'] = 0;
            $banner = PopupBanner::create($data);
            $message = 'Popup banner baru berhasil dipublikasikan!';
        }

        return redirect()->route('admin.popup-banner.index')->with('success', $message);
    }

    /**
     * Saklar cepat aktifkan / nonaktifkan banner.
     */
    public function toggle($id)
    {
        self::ensureTableExists();

        $banner = PopupBanner::findOrFail($id);
        $banner->is_active = ! $banner->is_active;
        $banner->save();

        $statusText = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status popup banner berhasil {$statusText}!");
    }

    /**
     * Hapus popup banner.
     */
    public function destroy($id)
    {
        self::ensureTableExists();

        $banner = PopupBanner::findOrFail($id);

        if ($banner->image_path && ! str_starts_with($banner->image_path, 'http')) {
            try {
                $storageFile = storage_path('app/public/'.$banner->image_path);
                $publicFile = public_path('storage/'.$banner->image_path);
                if (file_exists($storageFile)) {
                    @unlink($storageFile);
                }
                if (file_exists($publicFile)) {
                    @unlink($publicFile);
                }
            } catch (\Throwable $e) {
                // Ignore deletion failure
            }
        }

        $banner->delete();

        return redirect()->route('admin.popup-banner.index')->with('success', 'Popup banner berhasil dihapus!');
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

        $path = $file->storeAs($targetFolder, $filename, 'public');

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
     * Pastikan tabel popup_banners siap digunakan.
     */
    public static function ensureTableExists(): void
    {
        if (! Schema::hasTable('popup_banners')) {
            Schema::create('popup_banners', function ($table) {
                $table->id();
                $table->string('title');
                $table->string('image_path');
                $table->string('target_url', 500)->nullable();
                $table->string('button_text', 100)->default('Info Lebih Lanjut');
                $table->boolean('open_in_new_tab')->default(true);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('impressions_count')->default(0);
                $table->unsignedInteger('clicks_count')->default(0);
                $table->timestamps();
            });

            try {
                PopupBanner::create([
                    'title' => 'Lomba Ketangkasan PMR Wira 2026 - Sua Bhakti Berkarya',
                    'image_path' => '/mockups/mockup_popup_banner_publik.png',
                    'target_url' => '/lomba',
                    'button_text' => 'Info Lebih Lanjut',
                    'open_in_new_tab' => false,
                    'is_active' => true,
                ]);
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
}

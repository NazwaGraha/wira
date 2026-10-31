<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Member;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AchievementController extends Controller
{
    /**
     * Pastikan tabel achievements selalu ada dan migrasi terjalankan otomatis
     */
    public static function ensureTableExists(): void
    {
        if (Schema::hasTable('achievements')) {
            return;
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            // Ignore
        }

        if (! Schema::hasTable('achievements')) {
            try {
                Schema::create('achievements', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('event_name');
                    $table->string('organizer')->nullable();
                    $table->date('event_date');
                    $table->enum('level', ['Nasional', 'Provinsi', 'Kabupaten/Kota', 'Kecamatan', 'Sekolah'])->default('Kabupaten/Kota');
                    $table->string('rank');
                    $table->string('category')->nullable();
                    $table->json('contingent_members')->nullable();
                    $table->text('description')->nullable();
                    $table->string('trophy_image')->nullable();
                    $table->json('gallery_images')->nullable();
                    $table->string('certificate_file')->nullable();
                    $table->boolean('is_featured')->default(false);
                    $table->boolean('is_active')->default(true);
                    $table->unsignedSmallInteger('order_position')->default(0);
                    $table->timestamps();
                });
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        if (Schema::hasTable('achievements') && DB::table('achievements')->count() === 0) {
            try {
                Artisan::call('db:seed', ['--class' => 'AchievementSeeder', '--force' => true]);
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }

    /**
     * Tampilan daftar prestasi di Backoffice Admin
     */
    public function index(Request $request)
    {
        self::ensureTableExists();

        $search = trim((string) $request->query('q', ''));
        $level = $request->query('level');
        $rank = $request->query('rank');
        $year = $request->query('year');

        $query = Achievement::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('event_name', 'like', "%{$search}%")
                    ->orWhere('organizer', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (! empty($level) && $level !== 'all') {
            $query->where('level', $level);
        }

        if (! empty($rank) && $rank !== 'all') {
            if ($rank === 'juara_umum') {
                $query->where('rank', 'like', '%Umum%');
            } elseif ($rank === 'juara_1') {
                $query->where('rank', 'like', '%1%');
            } else {
                $query->where('rank', $rank);
            }
        }

        if (! empty($year)) {
            $query->whereYear('event_date', $year);
        }

        $achievements = $query->orderBy('event_date', 'desc')->paginate(10)->withQueryString();

        // 4 KPI Card Metrics
        $totalPrestasi = Achievement::count();
        $totalJuaraUmum = Achievement::where('rank', 'like', '%Umum%')->count();
        $totalNasional = Achievement::where('level', 'Nasional')->count();

        // Hitung total dokumentasi foto (piala + galeri foto)
        $allAch = Achievement::select('trophy_image', 'gallery_images')->get();
        $totalFoto = 0;
        foreach ($allAch as $a) {
            if (! empty($a->trophy_image)) {
                $totalFoto++;
            }
            if (is_array($a->gallery_images)) {
                $totalFoto += count($a->gallery_images);
            }
        }

        // Daftar tahun untuk filter
        $availableYears = Achievement::query()
            ->whereNotNull('event_date')
            ->pluck('event_date')
            ->map(fn ($d) => is_string($d) ? substr($d, 0, 4) : $d->format('Y'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return view('admin.achievements.index', compact(
            'achievements',
            'search',
            'level',
            'rank',
            'year',
            'totalPrestasi',
            'totalJuaraUmum',
            'totalNasional',
            'totalFoto',
            'availableYears'
        ));
    }

    /**
     * Form tambah prestasi baru
     */
    public function create()
    {
        self::ensureTableExists();

        $members = collect();
        if (Schema::hasTable('members')) {
            try {
                $query = Member::query();
                if (Schema::hasColumn('members', 'position')) {
                    $query->where(function ($q) {
                        $q->whereNull('position')->orWhere('position', 'not like', '%Pembina%');
                    });
                }
                if (Schema::hasColumn('members', 'member_status')) {
                    $query->where('member_status', 'Aktif');
                }
                $cols = ['id', 'name'];
                if (Schema::hasColumn('members', 'class_grade')) {
                    $cols[] = 'class_grade';
                }
                if (Schema::hasColumn('members', 'nri')) {
                    $cols[] = 'nri';
                }
                $members = $query->orderBy('name')->get($cols);
            } catch (\Throwable $e) {
                $members = collect();
            }
        }

        return view('admin.achievements.create', compact('members'));
    }

    /**
     * Simpan data prestasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'level' => 'required|string|in:Nasional,Provinsi,Kabupaten/Kota,Kecamatan,Sekolah',
            'rank' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'trophy_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'certificate_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:10240',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'order_position' => 'nullable|integer',
        ]);

        $trophyPath = null;
        if ($request->hasFile('trophy_image')) {
            $trophyPath = $this->uploadFile($request->file('trophy_image'), 'trophies');
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $galleryPaths[] = $this->uploadFile($file, 'gallery');
                }
            }
        }

        $certificatePath = null;
        if ($request->hasFile('certificate_file')) {
            $certificatePath = $this->uploadFile($request->file('certificate_file'), 'certificates');
        }

        // Parsing kontingen anggota
        $contingentMembers = [];
        if ($request->filled('contingent_raw')) {
            $names = array_filter(array_map('trim', explode("\n", $request->input('contingent_raw'))));
            foreach ($names as $n) {
                $contingentMembers[] = ['name' => $n];
            }
        } elseif ($request->has('selected_members') && is_array($request->input('selected_members'))) {
            foreach ($request->input('selected_members') as $mStr) {
                $parts = explode('|', $mStr);
                $contingentMembers[] = [
                    'name' => $parts[0] ?? '',
                    'class_grade' => $parts[1] ?? '',
                ];
            }
        }

        Achievement::create([
            'title' => $validated['title'],
            'event_name' => $validated['event_name'],
            'organizer' => $validated['organizer'] ?? null,
            'event_date' => $validated['event_date'],
            'level' => $validated['level'],
            'rank' => $validated['rank'],
            'category' => $validated['category'] ?? 'Umum',
            'description' => $validated['description'] ?? null,
            'trophy_image' => $trophyPath,
            'gallery_images' => $galleryPaths,
            'certificate_file' => $certificatePath,
            'contingent_members' => $contingentMembers,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'order_position' => (int) ($validated['order_position'] ?? 1),
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', "Data prestasi '{$validated['title']}' berhasil ditambahkan!");
    }

    /**
     * Form edit prestasi
     */
    public function edit(Achievement $prestasi)
    {
        self::ensureTableExists();

        $members = collect();
        if (Schema::hasTable('members')) {
            try {
                $query = Member::query();
                if (Schema::hasColumn('members', 'position')) {
                    $query->where(function ($q) {
                        $q->whereNull('position')->orWhere('position', 'not like', '%Pembina%');
                    });
                }
                if (Schema::hasColumn('members', 'member_status')) {
                    $query->where('member_status', 'Aktif');
                }
                $cols = ['id', 'name'];
                if (Schema::hasColumn('members', 'class_grade')) {
                    $cols[] = 'class_grade';
                }
                if (Schema::hasColumn('members', 'nri')) {
                    $cols[] = 'nri';
                }
                $members = $query->orderBy('name')->get($cols);
            } catch (\Throwable $e) {
                $members = collect();
            }
        }

        return view('admin.achievements.edit', [
            'achievement' => $prestasi,
            'members' => $members,
        ]);
    }

    /**
     * Perbarui data prestasi
     */
    public function update(Request $request, Achievement $prestasi)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'level' => 'required|string|in:Nasional,Provinsi,Kabupaten/Kota,Kecamatan,Sekolah',
            'rank' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'trophy_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'certificate_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:10240',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'order_position' => 'nullable|integer',
        ]);

        $dataToUpdate = [
            'title' => $validated['title'],
            'event_name' => $validated['event_name'],
            'organizer' => $validated['organizer'] ?? null,
            'event_date' => $validated['event_date'],
            'level' => $validated['level'],
            'rank' => $validated['rank'],
            'category' => $validated['category'] ?? 'Umum',
            'description' => $validated['description'] ?? null,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'order_position' => (int) ($validated['order_position'] ?? 1),
        ];

        // Hapus foto utama piala jika dicentang atau diminta
        if ($request->boolean('delete_trophy_image')) {
            $this->deleteStoredFile($prestasi->trophy_image);
            $dataToUpdate['trophy_image'] = null;
        }

        // Upload foto utama piala baru jika diunggah
        if ($request->hasFile('trophy_image')) {
            if (! empty($prestasi->trophy_image)) {
                $this->deleteStoredFile($prestasi->trophy_image);
            }
            $dataToUpdate['trophy_image'] = $this->uploadFile($request->file('trophy_image'), 'trophies');
        }

        // Hapus foto galeri yang dipilih untuk dihapus
        $existingGallery = is_array($prestasi->gallery_images) ? $prestasi->gallery_images : [];
        $deletedGallery = $request->input('delete_gallery', []);
        if (! empty($deletedGallery) && is_array($deletedGallery)) {
            $remainingGallery = [];
            foreach ($existingGallery as $idx => $path) {
                if (in_array((string) $idx, array_map('strval', $deletedGallery), true) || in_array($path, $deletedGallery, true)) {
                    $this->deleteStoredFile($path);
                } else {
                    $remainingGallery[] = $path;
                }
            }
            $existingGallery = array_values($remainingGallery);
        }

        // Tambah foto galeri baru
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                if ($file->isValid()) {
                    $existingGallery[] = $this->uploadFile($file, 'gallery');
                }
            }
        }
        $dataToUpdate['gallery_images'] = array_values($existingGallery);

        // Upload sertifikat baru
        if ($request->hasFile('certificate_file')) {
            if (! empty($prestasi->certificate_file)) {
                $this->deleteStoredFile($prestasi->certificate_file);
            }
            $dataToUpdate['certificate_file'] = $this->uploadFile($request->file('certificate_file'), 'certificates');
        }

        // Update kontingen
        if ($request->filled('contingent_raw')) {
            $contingentMembers = [];
            $names = array_filter(array_map('trim', explode("\n", $request->input('contingent_raw'))));
            foreach ($names as $n) {
                $contingentMembers[] = ['name' => $n];
            }
            $dataToUpdate['contingent_members'] = $contingentMembers;
        }

        $prestasi->update($dataToUpdate);

        return redirect()->route('admin.prestasi.index')->with('success', "Data prestasi '{$prestasi->title}' berhasil diperbarui!");
    }

    /**
     * Hapus satu foto dari galeri penyerahan prestasi (via AJAX)
     */
    public function deleteGalleryImage(Request $request, Achievement $prestasi, $index)
    {
        $gallery = is_array($prestasi->gallery_images) ? $prestasi->gallery_images : [];
        $idx = (int) $index;

        if (! isset($gallery[$idx])) {
            return response()->json([
                'success' => false,
                'message' => 'Foto galeri pada indeks tersebut tidak ditemukan.',
            ], 404);
        }

        $removedPath = $gallery[$idx];
        $this->deleteStoredFile($removedPath);

        unset($gallery[$idx]);
        $prestasi->gallery_images = array_values($gallery);
        $prestasi->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto galeri berhasil dihapus.',
            'remaining_count' => count($prestasi->gallery_images),
        ]);
    }

    /**
     * Hapus foto utama piala prestasi (via AJAX)
     */
    public function deleteTrophyImage(Request $request, Achievement $prestasi)
    {
        if (! empty($prestasi->trophy_image)) {
            $this->deleteStoredFile($prestasi->trophy_image);
            $prestasi->trophy_image = null;
            $prestasi->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto utama piala berhasil dihapus.',
        ]);
    }

    /**
     * Helper hapus file tersimpan di storage dan public/storage
     */
    protected function deleteStoredFile(?string $path): void
    {
        if (empty($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $pubPath = public_path('storage/'.$path);
            if (file_exists($pubPath) && ! is_dir($pubPath)) {
                @unlink($pubPath);
            }
        } catch (\Throwable $e) {
            // Abaikan kesalahan penghapusan file
        }
    }

    /**
     * Hapus data prestasi
     */
    public function destroy(Achievement $prestasi)
    {
        $title = $prestasi->title;

        if (! empty($prestasi->trophy_image)) {
            $this->deleteStoredFile($prestasi->trophy_image);
        }
        if (is_array($prestasi->gallery_images)) {
            foreach ($prestasi->gallery_images as $gImg) {
                $this->deleteStoredFile($gImg);
            }
        }
        if (! empty($prestasi->certificate_file)) {
            $this->deleteStoredFile($prestasi->certificate_file);
        }

        $prestasi->delete();

        return redirect()->route('admin.prestasi.index')->with('success', "Data prestasi '{$title}' berhasil dihapus.");
    }

    /**
     * Toggle status highlight beranda
     */
    public function toggleFeatured(int $id)
    {
        $prestasi = Achievement::findOrFail($id);
        $prestasi->is_featured = ! $prestasi->is_featured;
        $prestasi->save();

        return response()->json([
            'success' => true,
            'is_featured' => $prestasi->is_featured,
            'message' => $prestasi->is_featured ? 'Ditampilkan di highlight beranda' : 'Dihapus dari highlight beranda',
        ]);
    }

    /**
     * Helper unggah file ke storage
     */
    protected function uploadFile($file, string $subfolder): string
    {
        $filename = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $targetFolder = "achievements/{$subfolder}";

        File::ensureDirectoryExists(storage_path("app/public/{$targetFolder}"), 0755, true);
        if (file_exists(public_path('storage'))) {
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
     * Halaman Publik: HALL OF FAME & GALERI PRESTASI (/prestasi)
     */
    public function publicIndex(Request $request)
    {
        self::ensureTableExists();

        $year = $request->query('tahun');
        $level = $request->query('tingkat');
        $category = $request->query('kategori');
        $search = trim((string) $request->query('q', ''));

        $query = Achievement::active();

        if (! empty($year) && $year !== 'all') {
            $query->whereYear('event_date', $year);
        }

        if (! empty($level) && $level !== 'all') {
            $query->where('level', $level);
        }

        if (! empty($category) && $category !== 'all') {
            $query->where(function ($q) use ($category) {
                $q->where('category', 'like', "%{$category}%")
                    ->orWhere('title', 'like', "%{$category}%");
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('event_name', 'like', "%{$search}%")
                    ->orWhere('organizer', 'like', "%{$search}%");
            });
        }

        $achievements = $query->orderBy('order_position', 'asc')->orderBy('event_date', 'desc')->paginate(9)->withQueryString();

        // 4 Statistik Counter untuk Hero Banner
        $totalPiala = Achievement::active()->count();
        $totalJuaraUmum = Achievement::active()->where('rank', 'like', '%Umum%')->count();
        $totalNasional = Achievement::active()->where('level', 'Nasional')->count();

        // Daftar tahun yang ada prestasi
        $yearsList = Achievement::active()
            ->whereNotNull('event_date')
            ->pluck('event_date')
            ->map(fn ($d) => is_string($d) ? substr($d, 0, 4) : $d->format('Y'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        // Prestasi unggulan untuk carousel / spotlight (jika ada)
        $featuredAchievements = Achievement::active()->featured()->orderBy('order_position', 'asc')->take(3)->get();

        return view('pages.prestasi.index', compact(
            'achievements',
            'totalPiala',
            'totalJuaraUmum',
            'totalNasional',
            'yearsList',
            'year',
            'level',
            'category',
            'search',
            'featuredAchievements'
        ));
    }
}

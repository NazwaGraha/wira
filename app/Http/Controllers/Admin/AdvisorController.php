<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvisorController extends Controller
{
    /**
     * Memastikan tabel advisors tersedia di database secara otomatis (self-healing)
     */
    protected function ensureTableExists(): void
    {
        if (Schema::hasTable('advisors')) {
            return;
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            // Ignore
        }

        if (! Schema::hasTable('advisors')) {
            try {
                Schema::create('advisors', function (Blueprint $table) {
                    $table->id();
                    $table->string('nip', 50)->nullable()->unique();
                    $table->string('name');
                    $table->string('position')->default('Pembina PMR');
                    $table->string('title_degree')->nullable();
                    $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
                    $table->string('birth_place')->nullable();
                    $table->date('birth_date')->nullable();
                    $table->text('address')->nullable();
                    $table->string('phone', 30)->nullable();
                    $table->string('email')->nullable();
                    $table->string('password')->nullable();
                    $table->rememberToken();
                    $table->text('motto')->nullable();
                    $table->text('vision')->nullable();
                    $table->text('mission')->nullable();
                    $table->string('photo')->nullable();
                    $table->unsignedSmallInteger('order_position')->default(0);
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });

                // Salin data pembina dari tabel members jika ada
                if (Schema::hasTable('members')) {
                    $pembinaMembers = DB::table('members')
                        ->where('position', 'like', '%pembina%')
                        ->orWhereNotNull('nip')
                        ->get();

                    foreach ($pembinaMembers as $pm) {
                        DB::table('advisors')->insert([
                            'nip' => $pm->nip ?? null,
                            'name' => $pm->name,
                            'position' => $pm->position ?: 'Pembina PMR',
                            'gender' => $pm->gender ?? null,
                            'birth_place' => $pm->birth_place ?? null,
                            'birth_date' => $pm->birth_date ?? null,
                            'address' => $pm->address ?? null,
                            'phone' => $pm->phone ?? null,
                            'email' => $pm->email ?? null,
                            'password' => $pm->password ?? null,
                            'motto' => $pm->motto ?? null,
                            'photo' => $pm->photo ?? null,
                            'order_position' => 1,
                            'is_active' => $pm->is_active ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }

    public function index(Request $request)
    {
        $this->ensureTableExists();

        $search = $request->query('q');
        $query = Advisor::query();
        $advisors = collect();
        $totalAdvisors = 0;
        $totalAktif = 0;

        try {
            if (Schema::hasTable('advisors')) {
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%")
                            ->orWhere('position', 'like', "%{$search}%");
                    });
                }

                $advisors = $query->orderBy('order_position')->orderBy('name')->paginate(15)->withQueryString();
                $totalAdvisors = Advisor::count();
                $totalAktif = Advisor::where('is_active', true)->count();
            } else {
                $advisors = new LengthAwarePaginator([], 0, 15);
            }
        } catch (\Throwable $e) {
            $advisors = new LengthAwarePaginator([], 0, 15);
        }

        return view('admin.advisors.index', compact('advisors', 'search', 'totalAdvisors', 'totalAktif'));
    }

    public function create()
    {
        $this->ensureTableExists();

        $positions = collect(['Pembina PMR', 'Pembina Utama', 'Pembina Teknis', 'Guru Pembina PMR']);

        try {
            if (Schema::hasTable('advisors')) {
                $dbPositions = Advisor::whereNotNull('position')->distinct()->pluck('position');
                if ($dbPositions->isNotEmpty()) {
                    $positions = $positions->merge($dbPositions)->unique()->values();
                }
            }
        } catch (\Throwable $e) {
            // Tetap gunakan default jika query gagal
        }

        return view('admin.advisors.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $this->ensureTableExists();

        $validated = $request->validate([
            'nip' => 'nullable|string|max:50|unique:advisors,nip',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'title_degree' => 'nullable|string|max:50',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'motto' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['position'] = $validated['position'] ?: 'Pembina PMR';
        $validated['is_active'] = $request->has('is_active');
        $validated['order_position'] = $validated['order_position'] ?? 1;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'advisor_'.time().'_'.Str::random(10).'.'.$photo->getClientOriginalExtension();

            $storageDir = storage_path('app/public/advisors');
            File::ensureDirectoryExists($storageDir, 0755, true);

            $publicDir = public_path('storage/advisors');
            File::ensureDirectoryExists($publicDir, 0755, true);

            $path = $photo->storeAs('advisors', $filename, 'public');
            $validated['photo'] = $path;

            try {
                $fullSourcePath = storage_path('app/public/'.$path);
                $fullDestPath = public_path('storage/'.$path);
                if (file_exists($fullSourcePath)) {
                    @copy($fullSourcePath, $fullDestPath);
                }
            } catch (\Throwable $e) {
                // Ignore fallback copy error
            }
        }

        Advisor::create($validated);

        return redirect()->route('admin.advisors.index')->with('success', 'Data Pembina berhasil ditambahkan!');
    }

    public function edit(Advisor $advisor)
    {
        $this->ensureTableExists();

        $positions = collect(['Pembina PMR', 'Pembina Utama', 'Pembina Teknis', 'Guru Pembina PMR']);
        try {
            if (Schema::hasTable('advisors')) {
                $dbPositions = Advisor::whereNotNull('position')->distinct()->pluck('position');
                if ($dbPositions->isNotEmpty()) {
                    $positions = $positions->merge($dbPositions)->unique()->values();
                }
            }
        } catch (\Throwable $e) {
            // Tetap gunakan default
        }

        return view('admin.advisors.edit', compact('advisor', 'positions'));
    }

    public function update(Request $request, Advisor $advisor)
    {
        $this->ensureTableExists();
        $validated = $request->validate([
            'nip' => 'nullable|string|max:50|unique:advisors,nip,'.$advisor->id,
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'title_degree' => 'nullable|string|max:50',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'motto' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order_position' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['position'] = $validated['position'] ?: 'Pembina PMR';
        $validated['is_active'] = $request->has('is_active');
        $validated['order_position'] = $validated['order_position'] ?? $advisor->order_position;

        if ($request->hasFile('photo')) {
            if ($advisor->photo && Storage::disk('public')->exists($advisor->photo)) {
                Storage::disk('public')->delete($advisor->photo);
                $oldPublic = public_path('storage/'.$advisor->photo);
                if (file_exists($oldPublic)) {
                    @unlink($oldPublic);
                }
            }

            $photo = $request->file('photo');
            $filename = 'advisor_'.time().'_'.Str::random(10).'.'.$photo->getClientOriginalExtension();

            $storageDir = storage_path('app/public/advisors');
            File::ensureDirectoryExists($storageDir, 0755, true);

            $publicDir = public_path('storage/advisors');
            File::ensureDirectoryExists($publicDir, 0755, true);

            $path = $photo->storeAs('advisors', $filename, 'public');
            $validated['photo'] = $path;

            try {
                $fullSourcePath = storage_path('app/public/'.$path);
                $fullDestPath = public_path('storage/'.$path);
                if (file_exists($fullSourcePath)) {
                    @copy($fullSourcePath, $fullDestPath);
                }
            } catch (\Throwable $e) {
                // Ignore fallback copy error
            }
        }

        $advisor->update($validated);

        return redirect()->route('admin.advisors.index')->with('success', 'Data Pembina berhasil diperbarui!');
    }

    public function destroy(Advisor $advisor)
    {
        $this->ensureTableExists();

        if ($advisor->photo && Storage::disk('public')->exists($advisor->photo)) {
            Storage::disk('public')->delete($advisor->photo);
            $oldPublic = public_path('storage/'.$advisor->photo);
            if (file_exists($oldPublic)) {
                @unlink($oldPublic);
            }
        }

        $advisor->delete();

        return redirect()->route('admin.advisors.index')->with('success', 'Data Pembina berhasil dihapus!');
    }
}

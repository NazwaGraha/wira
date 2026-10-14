<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvisorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $query = Advisor::query();

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

        return view('admin.advisors.index', compact('advisors', 'search', 'totalAdvisors', 'totalAktif'));
    }

    public function create()
    {
        $positions = Advisor::whereNotNull('position')->distinct()->pluck('position');
        if ($positions->isEmpty()) {
            $positions = collect(['Pembina PMR', 'Pembina Utama', 'Pembina Teknis', 'Guru Pembina PMR']);
        }

        return view('admin.advisors.create', compact('positions'));
    }

    public function store(Request $request)
    {
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
        $positions = Advisor::whereNotNull('position')->distinct()->pluck('position');
        if ($positions->isEmpty()) {
            $positions = collect(['Pembina PMR', 'Pembina Utama', 'Pembina Teknis', 'Guru Pembina PMR']);
        }

        return view('admin.advisors.edit', compact('advisor', 'positions'));
    }

    public function update(Request $request, Advisor $advisor)
    {
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

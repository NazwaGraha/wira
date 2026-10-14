<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\OrganizationMember;
use App\Services\MemberSpreadsheetService;
use App\Services\OrganizationSyncService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Pastikan kolom-kolom baru tersedia di tabel members secara aman (self-healing)
     */
    protected function ensureColumnsExist(): void
    {
        if (Schema::hasTable('members')) {
            if (! Schema::hasColumn('members', 'nri')) {
                try {
                    Schema::table('members', function (Blueprint $table) {
                        $table->string('nri', 50)->nullable()->unique()->after('id');
                    });
                } catch (\Throwable $e) {
                    // Ignore
                }
            }
            if (! Schema::hasColumn('members', 'nip')) {
                try {
                    Schema::table('members', function (Blueprint $table) {
                        $table->string('nip', 50)->nullable()->after('nri');
                    });
                } catch (\Throwable $e) {
                    // Ignore
                }
            }
            if (! Schema::hasColumn('members', 'entry_year')) {
                try {
                    Schema::table('members', function (Blueprint $table) {
                        $table->unsignedSmallInteger('entry_year')->nullable()->after('class_grade');
                    });
                } catch (\Throwable $e) {
                    // Ignore
                }
            }
            if (! Schema::hasColumn('members', 'member_status')) {
                try {
                    Schema::table('members', function (Blueprint $table) {
                        $table->string('member_status', 20)->default('Aktif')->after('entry_year');
                    });
                } catch (\Throwable $e) {
                    // Ignore
                }
            }
        }
    }

    public function index(Request $request)
    {
        $this->ensureColumnsExist();

        $search = $request->query('q');
        $status = $request->query('status');
        $year = $request->query('year');
        $query = Member::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('class_grade', 'like', "%{$search}%")
                    ->orWhere('entry_year', 'like', "%{$search}%");

                if (Schema::hasColumn('members', 'nri')) {
                    $q->orWhere('nri', 'like', "%{$search}%");
                }
                if (Schema::hasColumn('members', 'nis')) {
                    $q->orWhere('nis', 'like', "%{$search}%");
                }
            });
        }

        // Pisahkan data pembina dari daftar anggota siswa PMR
        $query->where(function ($q) {
            $q->whereNull('nip')->orWhere('nip', '');
        })->where(function ($q) {
            $q->whereNull('position')->orWhere('position', 'not like', '%Pembina%');
        });

        if ($status && in_array($status, ['Aktif', 'Purna'])) {
            $query->where('member_status', $status);
        }

        if ($year) {
            $query->where('entry_year', $year);
        }

        $members = $query->orderBy('name')->paginate(15)->withQueryString();

        $baseStudentQuery = Member::where(function ($q) {
            $q->whereNull('nip')->orWhere('nip', '');
        })->where(function ($q) {
            $q->whereNull('position')->orWhere('position', 'not like', '%Pembina%');
        });
        $totalMembers = (clone $baseStudentQuery)->count();
        $totalAktif = (clone $baseStudentQuery)->where('member_status', 'Aktif')->count();
        $totalPurna = (clone $baseStudentQuery)->where('member_status', 'Purna')->count();

        return view('admin.members.index', compact('members', 'search', 'status', 'year', 'totalMembers', 'totalAktif', 'totalPurna'));
    }

    /**
     * Endpoint Lookup Data Anggota via NRI / NIS
     */
    public function lookup(Request $request)
    {
        $nri = trim((string) $request->query('nri', $request->query('q', '')));
        if (! $nri) {
            return response()->json(['found' => false, 'message' => 'Parameter NRI tidak boleh kosong.'], 400);
        }

        $member = Member::where(function ($q) use ($nri) {
            if (Schema::hasColumn('members', 'nri')) {
                $q->where('nri', $nri);
            }
            if (Schema::hasColumn('members', 'nis')) {
                $q->orWhere('nis', $nri);
            }
        })->first();

        if (! $member) {
            return response()->json([
                'found' => false,
                'message' => "Data anggota dengan NRI '{$nri}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'found' => true,
            'member' => [
                'id' => $member->id,
                'nri' => $member->nri,
                'nis' => $member->nis,
                'name' => $member->name,
                'position' => $member->position ?: 'Anggota',
                'gender' => $member->gender,
                'class_grade' => $member->class_grade,
                'entry_year' => $member->entry_year,
                'member_status' => $member->effective_status,
                'birth_place' => $member->birth_place,
                'birth_date' => $member->birth_date ? $member->birth_date->format('Y-m-d') : null,
                'email' => $member->email,
                'phone' => $member->phone,
                'address' => $member->address,
                'photo_url' => $member->photo_url,
            ],
        ]);
    }

    public function create()
    {
        $this->ensureColumnsExist();

        $positions = OrganizationMember::select('position')->distinct()->pluck('position')->toArray();

        return view('admin.members.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $this->ensureColumnsExist();

        $nriInput = trim((string) $request->input('nri', $request->input('nis', '')));

        // Validasi keunikan NRI jika diisi
        if (! empty($nriInput)) {
            $duplicate = Member::where(function ($q) use ($nriInput) {
                if (Schema::hasColumn('members', 'nri')) {
                    $q->where('nri', $nriInput);
                }
                if (Schema::hasColumn('members', 'nis')) {
                    $q->orWhere('nis', $nriInput);
                }
            })->exists();

            if ($duplicate) {
                return redirect()->back()->withInput()->withErrors([
                    'nri' => "NRI '{$nriInput}' sudah digunakan oleh anggota lain. Satu anggota hanya boleh memiliki 1 NRI unik.",
                ]);
            }
        }

        $validated = $request->validate([
            'nri' => 'nullable|string|max:50',
            'nip' => 'nullable|string|max:50',
            'nis' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'class_grade' => 'nullable|string|max:50',
            'entry_year' => 'nullable|integer|min:1990|max:'.((int) date('Y') + 5),
            'member_status' => 'nullable|string|in:Aktif,Purna',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'motto' => 'nullable|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $dataToSave = [];

        if (Schema::hasColumn('members', 'nri')) {
            $dataToSave['nri'] = $nriInput ?: null;
        }
        if (Schema::hasColumn('members', 'nis')) {
            $dataToSave['nis'] = $nriInput ?: null;
        }
        if (Schema::hasColumn('members', 'nip')) {
            $dataToSave['nip'] = $request->input('nip') ? trim($request->input('nip')) : null;
        }

        $dataToSave['name'] = $validated['name'];
        $dataToSave['position'] = $validated['position'] ?? null;
        $dataToSave['address'] = $validated['address'] ?? null;
        $dataToSave['birth_place'] = $validated['birth_place'] ?? null;
        $dataToSave['birth_date'] = $validated['birth_date'] ?? null;
        $dataToSave['gender'] = ! empty($validated['gender']) ? $validated['gender'] : null;
        $dataToSave['class_grade'] = $validated['class_grade'] ?? null;
        $dataToSave['email'] = $validated['email'] ?? null;
        $dataToSave['phone'] = $validated['phone'] ?? null;
        $dataToSave['motto'] = $validated['motto'] ?? null;
        $dataToSave['vision'] = $validated['vision'] ?? null;
        $dataToSave['mission'] = $validated['mission'] ?? null;
        $dataToSave['is_active'] = $request->has('is_active');

        if (Schema::hasColumn('members', 'entry_year')) {
            $dataToSave['entry_year'] = ! empty($validated['entry_year']) ? (int) $validated['entry_year'] : null;
        }

        if (Schema::hasColumn('members', 'member_status')) {
            if (! empty($dataToSave['entry_year'])) {
                $currentYear = (int) date('Y');
                $dataToSave['member_status'] = ! empty($validated['member_status'])
                    ? $validated['member_status']
                    : (($currentYear >= ($dataToSave['entry_year'] + 3)) ? 'Purna' : 'Aktif');
            } else {
                $dataToSave['member_status'] = $validated['member_status'] ?? 'Aktif';
            }
        }

        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $filename = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();

                // Pastikan direktori target ada
                File::ensureDirectoryExists(storage_path('app/public/members'), 0755, true);
                if (file_exists(public_path('storage'))) {
                    File::ensureDirectoryExists(public_path('storage/members'), 0755, true);
                }

                // Simpan ke storage disk public
                $path = $file->storeAs('members', $filename, 'public');
                $dataToSave['photo'] = 'members/'.$filename;

                // Salin juga langsung ke public/storage/members jika public/storage adalah folder biasa (bukan symlink)
                try {
                    $directPublicTarget = public_path('storage/members/'.$filename);
                    $sourceStoragePath = storage_path('app/public/members/'.$filename);
                    if (! file_exists($directPublicTarget) && file_exists($sourceStoragePath)) {
                        @copy($sourceStoragePath, $directPublicTarget);
                    }
                } catch (\Throwable $e) {
                    // Ignore direct copy failure
                }
            } catch (\Throwable $e) {
                Log::error('Upload foto anggota gagal (store): '.$e->getMessage());
                session()->flash('warning', 'Data tersimpan, namun foto profil gagal diunggah karena izin folder server: '.$e->getMessage());
            }
        }

        try {
            Member::create($dataToSave);

            if (! empty($dataToSave['position'])) {
                try {
                    OrganizationSyncService::syncAll();
                } catch (\Throwable $syncEx) {
                    Log::warning('OrganizationSyncService::syncAll error after member create: '.$syncEx->getMessage());
                }
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan data anggota: '.$e->getMessage(),
            ]);
        }

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$validated['name']}' berhasil ditambahkan!");
    }

    public function edit(Member $member)
    {
        $this->ensureColumnsExist();

        $positions = OrganizationMember::select('position')->distinct()->pluck('position')->toArray();

        return view('admin.members.edit', compact('member', 'positions'));
    }

    public function update(Request $request, Member $member)
    {
        $this->ensureColumnsExist();

        $nriInput = trim((string) $request->input('nri', $request->input('nis', '')));

        // Validasi keunikan NRI jika diisi
        if (! empty($nriInput)) {
            $duplicate = Member::where('id', '!=', $member->id)
                ->where(function ($q) use ($nriInput) {
                    if (Schema::hasColumn('members', 'nri')) {
                        $q->where('nri', $nriInput);
                    }
                    if (Schema::hasColumn('members', 'nis')) {
                        $q->orWhere('nis', $nriInput);
                    }
                })->exists();

            if ($duplicate) {
                return redirect()->back()->withInput()->withErrors([
                    'nri' => "NRI '{$nriInput}' sudah digunakan oleh anggota lain. Satu anggota hanya boleh memiliki 1 NRI unik.",
                ]);
            }
        }

        $validated = $request->validate([
            'nri' => 'nullable|string|max:50',
            'nip' => 'nullable|string|max:50',
            'nis' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'class_grade' => 'nullable|string|max:50',
            'entry_year' => 'nullable|integer|min:1990|max:'.((int) date('Y') + 5),
            'member_status' => 'nullable|string|in:Aktif,Purna',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'motto' => 'nullable|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $dataToUpdate = [];

        if (Schema::hasColumn('members', 'nri')) {
            $dataToUpdate['nri'] = $nriInput ?: null;
        }
        if (Schema::hasColumn('members', 'nis')) {
            $dataToUpdate['nis'] = $nriInput ?: null;
        }
        if (Schema::hasColumn('members', 'nip')) {
            $dataToUpdate['nip'] = $request->input('nip') ? trim($request->input('nip')) : null;
        }

        $dataToUpdate['name'] = $validated['name'];
        $dataToUpdate['position'] = $validated['position'] ?? null;
        $dataToUpdate['address'] = $validated['address'] ?? null;
        $dataToUpdate['birth_place'] = $validated['birth_place'] ?? null;
        $dataToUpdate['birth_date'] = $validated['birth_date'] ?? null;
        $dataToUpdate['gender'] = ! empty($validated['gender']) ? $validated['gender'] : null;
        $dataToUpdate['class_grade'] = $validated['class_grade'] ?? null;
        $dataToUpdate['email'] = $validated['email'] ?? null;
        $dataToUpdate['phone'] = $validated['phone'] ?? null;
        $dataToUpdate['motto'] = $validated['motto'] ?? null;
        $dataToUpdate['vision'] = $validated['vision'] ?? null;
        $dataToUpdate['mission'] = $validated['mission'] ?? null;
        $dataToUpdate['is_active'] = $request->has('is_active');

        if (Schema::hasColumn('members', 'entry_year')) {
            $dataToUpdate['entry_year'] = ! empty($validated['entry_year']) ? (int) $validated['entry_year'] : null;
        }

        if (Schema::hasColumn('members', 'member_status')) {
            if (! empty($dataToUpdate['entry_year'])) {
                $currentYear = (int) date('Y');
                $dataToUpdate['member_status'] = ! empty($validated['member_status'])
                    ? $validated['member_status']
                    : (($currentYear >= ($dataToUpdate['entry_year'] + 3)) ? 'Purna' : 'Aktif');
            } else {
                $dataToUpdate['member_status'] = $validated['member_status'] ?? 'Aktif';
            }
        }

        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $filename = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();

                // Pastikan direktori target ada
                File::ensureDirectoryExists(storage_path('app/public/members'), 0755, true);
                if (file_exists(public_path('storage'))) {
                    File::ensureDirectoryExists(public_path('storage/members'), 0755, true);
                }

                // Hapus foto lama jika ada
                if (! empty($member->photo)) {
                    try {
                        Storage::disk('public')->delete($member->photo);
                        $oldPublicPath = public_path('storage/'.$member->photo);
                        if (file_exists($oldPublicPath)) {
                            @unlink($oldPublicPath);
                        }
                    } catch (\Throwable $e) {
                        // Ignore deletion error
                    }
                }

                // Simpan ke storage disk public
                $path = $file->storeAs('members', $filename, 'public');
                $dataToUpdate['photo'] = 'members/'.$filename;

                // Salin juga langsung ke public/storage/members jika public/storage adalah folder biasa (bukan symlink)
                try {
                    $directPublicTarget = public_path('storage/members/'.$filename);
                    $sourceStoragePath = storage_path('app/public/members/'.$filename);
                    if (! file_exists($directPublicTarget) && file_exists($sourceStoragePath)) {
                        @copy($sourceStoragePath, $directPublicTarget);
                    }
                } catch (\Throwable $e) {
                    // Ignore direct copy failure
                }
            } catch (\Throwable $e) {
                Log::error('Upload foto anggota gagal (update): '.$e->getMessage());
                session()->flash('warning', 'Data tersimpan, namun foto profil gagal diunggah karena izin folder server: '.$e->getMessage());
            }
        }

        try {
            $member->update($dataToUpdate);

            try {
                OrganizationSyncService::syncAll();
            } catch (\Throwable $syncEx) {
                Log::warning('OrganizationSyncService::syncAll error after member update: '.$syncEx->getMessage());
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Gagal memperbarui data anggota: '.$e->getMessage(),
            ]);
        }

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$member->name}' berhasil diperbarui!");
    }

    public function destroy(Member $member)
    {
        $name = $member->name;
        $member->delete();

        try {
            OrganizationSyncService::syncAll();
        } catch (\Throwable $e) {
            // Ignore
        }

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$name}' berhasil dihapus.");
    }

    /**
     * Hapus massal (bulk delete) anggota terpilih berdasarkan checkbox
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids) || ! is_array($ids)) {
            return redirect()->route('admin.members.index')->with('error', 'Tidak ada data anggota yang dipilih untuk dihapus.');
        }

        // Sanitasi IDs
        $ids = array_map('intval', array_filter($ids));

        if (empty($ids)) {
            return redirect()->route('admin.members.index')->with('error', 'Data anggota yang dipilih tidak valid.');
        }

        try {
            $count = Member::whereIn('id', $ids)->count();
            Member::whereIn('id', $ids)->delete();

            try {
                OrganizationSyncService::syncAll();
            } catch (\Throwable $e) {
                // Ignore
            }

            return redirect()->route('admin.members.index')->with('success', "Berhasil menghapus {$count} data anggota terpilih.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.members.index')->with('error', 'Gagal menghapus data anggota terpilih: '.$e->getMessage());
        }
    }

    /**
     * Export member list to Excel XLSX
     */
    public function export(Request $request, MemberSpreadsheetService $spreadsheetService)
    {
        $status = $request->query('status');
        $query = Member::orderBy('name');

        if ($status && in_array($status, ['Aktif', 'Purna'])) {
            $query->where('member_status', $status);
        }

        $members = $query->get();
        $filePath = $spreadsheetService->export($members);
        $filename = 'Data_Anggota_PMR_WIRA_'.date('Ymd_His').'.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download Excel template for bulk import
     */
    public function downloadTemplate(MemberSpreadsheetService $spreadsheetService)
    {
        $filePath = $spreadsheetService->exportTemplate();
        $filename = 'Template_Import_Anggota_PMR_WIRA.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Import members from Excel (.xlsx or .csv)
     */
    public function import(Request $request, MemberSpreadsheetService $spreadsheetService)
    {
        $this->ensureColumnsExist();

        $request->validate([
            'excel_file' => 'required|file|max:10240',
        ], [
            'excel_file.required' => 'Silakan pilih file Excel (.xlsx atau .csv) terlebih dahulu.',
            'excel_file.max' => 'Ukuran file Excel maksimal 10MB.',
        ]);

        $file = $request->file('excel_file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return redirect()->back()->with('error', 'Format file harus berupa file Excel (.xlsx) atau CSV (.csv).');
        }

        try {
            $result = $spreadsheetService->import($file);

            if ($result['total'] === 0 && ! empty($result['errors'])) {
                return redirect()->back()->with('error', implode('<br>', $result['errors']));
            }

            try {
                OrganizationSyncService::syncAll();
            } catch (\Throwable $e) {
                // Ignore
            }

            $message = "Berhasil memproses {$result['total']} data anggota (Baru: {$result['success']}, Diperbarui: {$result['updated']}).";
            if ($result['skipped'] > 0) {
                $message .= " <br><span class=\"text-xs text-amber-800 font-normal\">Catatan ({$result['skipped']} baris dilewati): ";
                if (! empty($result['errors'])) {
                    $detailErrors = array_slice($result['errors'], 0, 5);
                    $message .= implode(' | ', $detailErrors);
                    if (count($result['errors']) > 5) {
                        $message .= ' ...dan '.(count($result['errors']) - 5).' lainnya.';
                    }
                } else {
                    $message .= 'Baris kosong atau data tidak valid.';
                }
                $message .= '</span>';
            }

            return redirect()->route('admin.members.index')->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: '.$e->getMessage());
        }
    }
}

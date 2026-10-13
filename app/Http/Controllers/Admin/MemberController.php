<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\OrganizationMember;
use App\Services\MemberSpreadsheetService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan kolom baru tersedia secara aman
        if (Schema::hasTable('members')) {
            if (! Schema::hasColumn('members', 'nri')) {
                Schema::table('members', function (Blueprint $table) {
                    $table->string('nri', 50)->nullable()->unique()->after('id');
                });
            }
            if (! Schema::hasColumn('members', 'entry_year')) {
                Schema::table('members', function (Blueprint $table) {
                    $table->unsignedSmallInteger('entry_year')->nullable()->after('class_grade');
                });
            }
            if (! Schema::hasColumn('members', 'member_status')) {
                Schema::table('members', function (Blueprint $table) {
                    $table->string('member_status', 20)->default('Aktif')->after('entry_year');
                });
            }
        }

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

        if ($status && in_array($status, ['Aktif', 'Purna'])) {
            $query->where('member_status', $status);
        }

        if ($year) {
            $query->where('entry_year', $year);
        }

        $members = $query->orderBy('name')->paginate(15)->withQueryString();
        $totalMembers = Member::count();
        $totalAktif = Member::where('member_status', 'Aktif')->count();
        $totalPurna = Member::where('member_status', 'Purna')->count();

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
        $positions = OrganizationMember::select('position')->distinct()->pluck('position')->toArray();

        return view('admin.members.create', compact('positions'));
    }

    public function store(Request $request)
    {
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

        $validated['nri'] = $nriInput ?: null;
        $validated['nip'] = $request->input('nip') ? trim($request->input('nip')) : null;
        $validated['nis'] = $nriInput ?: null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('members', $filename, 'public');
            $validated['photo'] = 'members/'.$filename;
        }

        $validated['is_active'] = $request->has('is_active');

        // Otomatisasi Status Anggota: jika tahun masuk + 3 <= tahun sekarang -> Purna
        if (! empty($validated['entry_year'])) {
            $currentYear = (int) date('Y');
            if (empty($validated['member_status'])) {
                $validated['member_status'] = ($currentYear >= ((int) $validated['entry_year'] + 3)) ? 'Purna' : 'Aktif';
            }
        } else {
            $validated['member_status'] = $validated['member_status'] ?? 'Aktif';
        }

        Member::create($validated);

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$validated['name']}' berhasil ditambahkan!");
    }

    public function edit(Member $member)
    {
        $positions = OrganizationMember::select('position')->distinct()->pluck('position')->toArray();

        return view('admin.members.edit', compact('member', 'positions'));
    }

    public function update(Request $request, Member $member)
    {
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

        $validated['nri'] = $nriInput ?: null;
        $validated['nip'] = $request->input('nip') ? trim($request->input('nip')) : null;
        $validated['nis'] = $nriInput ?: null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('members', $filename, 'public');
            $validated['photo'] = 'members/'.$filename;
        }

        $validated['is_active'] = $request->has('is_active');

        // Otomatisasi Status Anggota: jika tahun masuk + 3 <= tahun sekarang -> Purna
        if (! empty($validated['entry_year'])) {
            $currentYear = (int) date('Y');
            if (empty($validated['member_status'])) {
                $validated['member_status'] = ($currentYear >= ((int) $validated['entry_year'] + 3)) ? 'Purna' : 'Aktif';
            }
        } else {
            $validated['member_status'] = $validated['member_status'] ?? 'Aktif';
        }

        $member->update($validated);

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$member->name}' berhasil diperbarui!");
    }

    public function destroy(Member $member)
    {
        $name = $member->name;
        $member->delete();

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$name}' berhasil dihapus.");
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

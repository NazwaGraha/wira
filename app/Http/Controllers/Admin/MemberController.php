<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan kolom baru tersedia secara aman
        if (Schema::hasTable('members')) {
            if (!Schema::hasColumn('members', 'entry_year')) {
                Schema::table('members', function (Blueprint $table) {
                    $table->unsignedSmallInteger('entry_year')->nullable()->after('class_grade');
                });
            }
            if (!Schema::hasColumn('members', 'member_status')) {
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
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('class_grade', 'like', "%{$search}%")
                  ->orWhere('entry_year', 'like', "%{$search}%");
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

    public function create()
    {
        $positions = \App\Models\OrganizationMember::select('position')->distinct()->pluck('position')->toArray();
        return view('admin.members.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:20|unique:members',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'class_grade' => 'nullable|string|max:50',
            'entry_year' => 'nullable|integer|min:1990|max:' . ((int)date('Y') + 5),
            'member_status' => 'nullable|string|in:Aktif,Purna',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'motto' => 'nullable|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('members', $filename, 'public');
            $validated['photo'] = 'members/' . $filename;
        }

        $validated['is_active'] = $request->has('is_active');

        // Otomatisasi Status Anggota: jika tahun masuk + 3 <= tahun sekarang -> Purna
        if (!empty($validated['entry_year'])) {
            $currentYear = (int) date('Y');
            if (empty($validated['member_status'])) {
                $validated['member_status'] = ($currentYear >= ((int)$validated['entry_year'] + 3)) ? 'Purna' : 'Aktif';
            }
        } else {
            $validated['member_status'] = $validated['member_status'] ?? 'Aktif';
        }

        Member::create($validated);

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$validated['name']}' berhasil ditambahkan!");
    }

    public function edit(Member $member)
    {
        $positions = \App\Models\OrganizationMember::select('position')->distinct()->pluck('position')->toArray();
        return view('admin.members.edit', compact('member', 'positions'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'nis' => 'nullable|string|max:20|unique:members,nis,' . $member->id,
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'class_grade' => 'nullable|string|max:50',
            'entry_year' => 'nullable|integer|min:1990|max:' . ((int)date('Y') + 5),
            'member_status' => 'nullable|string|in:Aktif,Purna',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'motto' => 'nullable|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('members', $filename, 'public');
            $validated['photo'] = 'members/' . $filename;
        }

        $validated['is_active'] = $request->has('is_active');

        // Otomatisasi Status Anggota: jika tahun masuk + 3 <= tahun sekarang -> Purna
        if (!empty($validated['entry_year'])) {
            $currentYear = (int) date('Y');
            if (empty($validated['member_status'])) {
                $validated['member_status'] = ($currentYear >= ((int)$validated['entry_year'] + 3)) ? 'Purna' : 'Aktif';
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
    public function export(Request $request, \App\Services\MemberSpreadsheetService $spreadsheetService)
    {
        $status = $request->query('status');
        $query = Member::orderBy('name');

        if ($status && in_array($status, ['Aktif', 'Purna'])) {
            $query->where('member_status', $status);
        }

        $members = $query->get();
        $filePath = $spreadsheetService->export($members);
        $filename = 'Data_Anggota_PMR_WIRA_' . date('Ymd_His') . '.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download Excel template for bulk import
     */
    public function downloadTemplate(\App\Services\MemberSpreadsheetService $spreadsheetService)
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
    public function import(Request $request, \App\Services\MemberSpreadsheetService $spreadsheetService)
    {
        $request->validate([
            'excel_file' => 'required|file|max:10240',
        ], [
            'excel_file.required' => 'Silakan pilih file Excel (.xlsx atau .csv) terlebih dahulu.',
            'excel_file.max' => 'Ukuran file Excel maksimal 10MB.',
        ]);

        $file = $request->file('excel_file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return redirect()->back()->with('error', 'Format file harus berupa file Excel (.xlsx) atau CSV (.csv).');
        }

        try {
            $result = $spreadsheetService->import($file);

            if ($result['total'] === 0 && !empty($result['errors'])) {
                return redirect()->back()->with('error', implode('<br>', $result['errors']));
            }

            $message = "Berhasil memproses {$result['total']} data anggota (Baru: {$result['success']}, Diperbarui: {$result['updated']}).";
            if ($result['skipped'] > 0) {
                $message .= " {$result['skipped']} baris dilewati karena kosong atau tidak valid.";
            }

            return redirect()->route('admin.members.index')->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use App\Models\Member;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $setting = OrganizationSetting::firstOrCreate(
            ['id' => 1],
            [
                'badge' => 'Bagan Kepengurusan',
                'title' => 'Struktur Organisasi 2026/2027',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.',
            ]
        );

        // Pastikan Tingkat 1 (Pembina PMR) terdaftar di tabel organization_members
        $hasLevel1 = OrganizationMember::where('level', 1)->exists();
        if (! $hasLevel1) {
            try {
                $firstAdvisor = Advisor::where('is_active', true)->orderBy('order_position')->first();
                OrganizationMember::create([
                    'name' => $firstAdvisor?->name ?: 'Drs. H. Mulyadi, M.Pd',
                    'position' => $firstAdvisor?->position ?: 'Pembina PMR',
                    'subtitle' => $firstAdvisor?->title_degree ? ('Pembina • '.$firstAdvisor->title_degree) : 'Guru Pembina PMR',
                    'level' => 1,
                    'order_position' => 1,
                    'photo' => $firstAdvisor?->photo ?: null,
                    'icon' => 'fa-solid fa-shield-halved',
                    'is_active' => true,
                ]);
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        $allMembers = OrganizationMember::with('member')->orderBy('level')->orderBy('order_position')->get();

        if ($statusFilter === 'active_only') {
            $allMembers = $allMembers->filter(fn ($m) => $m->isLeaderActive())->values();
        } elseif ($statusFilter === 'purna_only') {
            $allMembers = $allMembers->filter(fn ($m) => ! $m->isLeaderActive())->values();
        }

        $membersByLevel = $allMembers->groupBy('level');

        return view('admin.organization.index', compact('setting', 'allMembers', 'membersByLevel', 'statusFilter'));
    }

    public function updateSetting(Request $request)
    {
        $validated = $request->validate([
            'badge' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
        ]);

        $setting = OrganizationSetting::firstOrCreate(['id' => 1]);
        $setting->update($validated);

        return redirect()->route('admin.organization.index')->with('success', 'Judul dan informasi Bagan Kepengurusan berhasil diperbarui!');
    }

    public function create()
    {
        $members = collect();
        try {
            $query = Member::query();
            if (Schema::hasColumn('members', 'member_status')) {
                $query->where('member_status', 'Aktif');
            } elseif (Schema::hasColumn('members', 'is_active')) {
                $query->where('is_active', true);
            }
            $members = $query->orderBy('name')->get();
        } catch (\Throwable $e) {
            $members = collect();
        }

        // Kumpulkan ID dan nama anggota yang sudah ditugaskan di seluruh struktur organisasi (jabatan lain maupun staf lain)
        $allOrgMembers = OrganizationMember::all();
        $usedMemberIds = [];
        $usedMemberNames = [];
        foreach ($allOrgMembers as $org) {
            if (! empty($org->member_id)) {
                $usedMemberIds[] = (int) $org->member_id;
            }
            if (! empty($org->name)) {
                $usedMemberNames[] = trim(strtolower($org->name));
            }
            if (! empty($org->staff_members) && is_array($org->staff_members)) {
                foreach ($org->staff_members as $st) {
                    if (! empty($st['member_id'])) {
                        $usedMemberIds[] = (int) $st['member_id'];
                    }
                    if (! empty($st['name'])) {
                        $usedMemberNames[] = trim(strtolower($st['name']));
                    }
                }
            }
        }
        $usedMemberIds = array_values(array_unique($usedMemberIds));
        $usedMemberNames = array_values(array_unique($usedMemberNames));

        $membersJson = $members->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'class_grade' => $m->class_grade ?? '',
                'nri' => $m->nri ?? $m->nis ?? '',
                'nis' => $m->nri ?? $m->nis ?? '',
                'photo' => $m->photo ?? '',
                'photo_url' => $m->photo_url,
                'member_status' => $m->effective_status,
            ];
        })->values();

        $advisors = Advisor::where('is_active', true)->orderBy('order_position')->orderBy('name')->get();
        $advisorsJson = $advisors->map(function ($a) {
            return [
                'id' => $a->id,
                'name' => $a->name,
                'position' => $a->position,
                'title_degree' => $a->title_degree,
                'nip' => $a->nip,
                'photo_url' => $a->photo_url,
            ];
        })->values();

        return view('admin.organization.create', compact('members', 'membersJson', 'usedMemberIds', 'usedMemberNames', 'advisors', 'advisorsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|integer|exists:members,id',
            'position' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'level' => 'required|integer|in:1,2,3,4,5,6',
            'order_position' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'photo' => 'nullable|string',
            'work_program' => 'nullable|string',
            'staff_members' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        // Auto-populate from Member if member_id is selected
        if (! empty($validated['member_id'])) {
            $memberObj = Member::find($validated['member_id']);
            if ($memberObj) {
                $validated['name'] = $memberObj->name;
                if (empty($validated['photo']) && ! empty($memberObj->photo)) {
                    $validated['photo'] = $memberObj->photo;
                }
            }
        } elseif ((int) ($validated['level'] ?? 0) === 1 && ! empty($validated['name'])) {
            // Jika level 1 (Pembina) dan member_id kosong, coba sinkronisasi dari tabel Advisor
            $advisor = Advisor::where('name', $validated['name'])->first();
            if ($advisor && empty($validated['photo']) && ! empty($advisor->photo)) {
                $validated['photo'] = $advisor->photo;
            }
        }

        if (empty($validated['name'])) {
            $validated['name'] = 'Pejabat '.$validated['position'];
        }

        $validated['order_position'] = $validated['order_position'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        // Clean and auto-populate staff_members from Member
        if (! empty($validated['staff_members'])) {
            $cleanedStaff = [];
            foreach ($validated['staff_members'] as $staff) {
                $stMemberId = $staff['member_id'] ?? null;
                $stName = trim($staff['name'] ?? '');
                $stClass = $staff['class_grade'] ?? '';
                $stPhoto = $staff['photo'] ?? null;

                if ($stMemberId) {
                    $stMemberObj = Member::find($stMemberId);
                    if ($stMemberObj) {
                        $stName = $stMemberObj->name;
                        $stClass = $stClass ?: ($stMemberObj->class_grade ?? '');
                        $stPhoto = $stPhoto ?: $stMemberObj->photo;
                    }
                }

                if (! empty($stName)) {
                    $cleanedStaff[] = [
                        'member_id' => $stMemberId,
                        'name' => $stName,
                        'class_grade' => $stClass,
                        'photo' => $stPhoto,
                    ];
                }
            }
            $validated['staff_members'] = $cleanedStaff;
        } else {
            $validated['staff_members'] = null;
        }

        OrganizationMember::create($validated);

        return redirect()->route('admin.organization.index')->with('success', 'Data pengurus/bidang baru berhasil ditambahkan ke bagan!');
    }

    public function edit(OrganizationMember $member)
    {
        $members = collect();
        try {
            $query = Member::query();
            if (Schema::hasColumn('members', 'member_status')) {
                // Tampilkan anggota berstatus Aktif ATAU anggota yang saat ini terhubung ke jabatan ini
                $query->where(function ($q) use ($member) {
                    $q->where('member_status', 'Aktif');
                    if (! empty($member->member_id)) {
                        $q->orWhere('id', $member->member_id);
                    }
                });
            } elseif (Schema::hasColumn('members', 'is_active')) {
                $query->where(function ($q) use ($member) {
                    $q->where('is_active', true);
                    if (! empty($member->member_id)) {
                        $q->orWhere('id', $member->member_id);
                    }
                });
            }
            $members = $query->orderBy('name')->get();
        } catch (\Throwable $e) {
            $members = collect();
        }

        // Kumpulkan ID dan nama anggota yang sudah ditugaskan di jabatan lain maupun staf lain (kecuali record $member ini)
        $otherOrgMembers = OrganizationMember::where('id', '!=', $member->id)->get();
        $usedMemberIds = [];
        $usedMemberNames = [];
        foreach ($otherOrgMembers as $org) {
            if (! empty($org->member_id)) {
                $usedMemberIds[] = (int) $org->member_id;
            }
            if (! empty($org->name)) {
                $usedMemberNames[] = trim(strtolower($org->name));
            }
            if (! empty($org->staff_members) && is_array($org->staff_members)) {
                foreach ($org->staff_members as $st) {
                    if (! empty($st['member_id'])) {
                        $usedMemberIds[] = (int) $st['member_id'];
                    }
                    if (! empty($st['name'])) {
                        $usedMemberNames[] = trim(strtolower($st['name']));
                    }
                }
            }
        }
        $usedMemberIds = array_values(array_unique($usedMemberIds));
        $usedMemberNames = array_values(array_unique($usedMemberNames));

        $membersJson = $members->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'class_grade' => $m->class_grade ?? '',
                'nri' => $m->nri ?? $m->nis ?? '',
                'nis' => $m->nri ?? $m->nis ?? '',
                'photo' => $m->photo ?? '',
                'photo_url' => $m->photo_url,
                'member_status' => $m->effective_status,
            ];
        })->values();

        $advisors = Advisor::orderBy('order_position')->orderBy('name')->get();
        $advisorsJson = $advisors->map(function ($a) {
            return [
                'id' => $a->id,
                'name' => $a->name,
                'position' => $a->position,
                'title_degree' => $a->title_degree,
                'nip' => $a->nip,
                'photo_url' => $a->photo_url,
            ];
        })->values();

        return view('admin.organization.edit', compact('member', 'members', 'membersJson', 'usedMemberIds', 'usedMemberNames', 'advisors', 'advisorsJson'));
    }

    public function update(Request $request, OrganizationMember $member)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|integer|exists:members,id',
            'position' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'level' => 'required|integer|in:1,2,3,4,5,6',
            'order_position' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'photo' => 'nullable|string',
            'work_program' => 'nullable|string',
            'staff_members' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        // Auto-populate from Member if member_id is selected
        if (! empty($validated['member_id'])) {
            $memberObj = Member::find($validated['member_id']);
            if ($memberObj) {
                $validated['name'] = $memberObj->name;
                if (! empty($memberObj->photo)) {
                    $validated['photo'] = $memberObj->photo;
                }
            }
        } elseif ((int) ($validated['level'] ?? 0) === 1 && ! empty($validated['name'])) {
            // Jika level 1 (Pembina) dan member_id kosong, coba sinkronisasi foto dari tabel Advisor
            $advisor = Advisor::where('name', $validated['name'])->first();
            if ($advisor && empty($validated['photo']) && ! empty($advisor->photo)) {
                $validated['photo'] = $advisor->photo;
            }
        }

        if (empty($validated['name'])) {
            $validated['name'] = $member->name ?: ('Pejabat '.$validated['position']);
        }

        $validated['order_position'] = $validated['order_position'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        // Clean and auto-populate staff_members from Member
        if (! empty($validated['staff_members'])) {
            $cleanedStaff = [];
            foreach ($validated['staff_members'] as $staff) {
                $stMemberId = $staff['member_id'] ?? null;
                $stName = trim($staff['name'] ?? '');
                $stClass = $staff['class_grade'] ?? '';
                $stPhoto = $staff['photo'] ?? null;

                if ($stMemberId) {
                    $stMemberObj = Member::find($stMemberId);
                    if ($stMemberObj) {
                        $stName = $stMemberObj->name;
                        $stClass = $stClass ?: ($stMemberObj->class_grade ?? '');
                        $stPhoto = $stPhoto ?: $stMemberObj->photo;
                    }
                }

                if (! empty($stName)) {
                    $cleanedStaff[] = [
                        'member_id' => $stMemberId,
                        'name' => $stName,
                        'class_grade' => $stClass,
                        'photo' => $stPhoto,
                    ];
                }
            }
            $validated['staff_members'] = $cleanedStaff;
        } else {
            $validated['staff_members'] = null;
        }

        $member->update($validated);

        return redirect()->route('admin.organization.index')->with('success', "Data '{$member->position}' ({$member->name}) berhasil diperbarui!");
    }

    public function destroy(OrganizationMember $member)
    {
        $name = $member->name;
        $position = $member->position;

        if ($member->photo && Storage::disk('public')->exists($member->photo)) {
            Storage::disk('public')->delete($member->photo);
        }

        $member->delete();

        return redirect()->route('admin.organization.index')->with('success', "Pengurus '{$position}' ({$name}) telah berhasil dihapus dari bagan kepengurusan.");
    }
}

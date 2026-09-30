<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index()
    {
        $setting = OrganizationSetting::firstOrCreate(
            ['id' => 1],
            [
                'badge' => 'Bagan Kepengurusan',
                'title' => 'Struktur Organisasi 2026/2027',
                'subtitle' => 'Masa Bakti Ragana Dwi Pantara — Sinergi kepemimpinan dan dedikasi relawan siswa.',
            ]
        );

        $allMembers = OrganizationMember::with('member')->orderBy('level')->orderBy('order_position')->get();
        $membersByLevel = $allMembers->groupBy('level');

        return view('admin.organization.index', compact('setting', 'allMembers', 'membersByLevel'));
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
            $members = Member::orderBy('name')->get(['id', 'name', 'class_grade', 'nis', 'photo', 'position']);
        } catch (\Throwable $e) {
            $members = collect();
        }

        $membersJson = $members->map(function ($m) {
            $photoUrl = null;
            if (!empty($m->photo)) {
                $photoUrl = str_starts_with($m->photo, 'http') || str_starts_with($m->photo, '/')
                    ? $m->photo
                    : asset('storage/' . $m->photo);
            }
            return [
                'id' => $m->id,
                'name' => $m->name,
                'class_grade' => $m->class_grade ?? '',
                'nis' => $m->nis ?? '',
                'photo' => $m->photo ?? '',
                'photo_url' => $photoUrl,
            ];
        })->values();

        return view('admin.organization.create', compact('members', 'membersJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|integer',
            'position' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'level' => 'required|integer|in:1,2,3,4,5',
            'order_position' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'work_program' => 'nullable|string',
            'staff_members' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['order_position'] = $validated['order_position'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('organization', 'public');
        }

        // Clean staff_members array
        if (!empty($validated['staff_members'])) {
            $cleanedStaff = [];
            foreach ($validated['staff_members'] as $staff) {
                if (!empty($staff['name'])) {
                    $cleanedStaff[] = [
                        'member_id' => $staff['member_id'] ?? null,
                        'name' => trim($staff['name']),
                        'class_grade' => $staff['class_grade'] ?? '',
                        'photo' => $staff['photo'] ?? null,
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
            $members = Member::orderBy('name')->get(['id', 'name', 'class_grade', 'nis', 'photo', 'position']);
        } catch (\Throwable $e) {
            $members = collect();
        }

        $membersJson = $members->map(function ($m) {
            $photoUrl = null;
            if (!empty($m->photo)) {
                $photoUrl = str_starts_with($m->photo, 'http') || str_starts_with($m->photo, '/')
                    ? $m->photo
                    : asset('storage/' . $m->photo);
            }
            return [
                'id' => $m->id,
                'name' => $m->name,
                'class_grade' => $m->class_grade ?? '',
                'nis' => $m->nis ?? '',
                'photo' => $m->photo ?? '',
                'photo_url' => $photoUrl,
            ];
        })->values();

        return view('admin.organization.edit', compact('member', 'members', 'membersJson'));
    }

    public function update(Request $request, OrganizationMember $member)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|integer',
            'position' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'level' => 'required|integer|in:1,2,3,4,5',
            'order_position' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'work_program' => 'nullable|string',
            'staff_members' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['order_position'] = $validated['order_position'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            if ($member->photo && Storage::disk('public')->exists($member->photo)) {
                Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('organization', 'public');
        }

        // Clean staff_members array
        if (!empty($validated['staff_members'])) {
            $cleanedStaff = [];
            foreach ($validated['staff_members'] as $staff) {
                if (!empty($staff['name'])) {
                    $cleanedStaff[] = [
                        'member_id' => $staff['member_id'] ?? null,
                        'name' => trim($staff['name']),
                        'class_grade' => $staff['class_grade'] ?? '',
                        'photo' => $staff['photo'] ?? null,
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationMember;
use App\Models\OrganizationSetting;
use App\Models\Member;
use Illuminate\Http\Request;

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

        $allMembers = OrganizationMember::orderBy('level')->orderBy('order_position')->get();
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
            $members = Member::orderBy('name')->get(['id', 'name', 'class_grade', 'nis']);
        } catch (\Throwable $e) {
            $members = collect();
        }

        $membersJson = $members->map(function($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'class_grade' => $m->class_grade ?? '',
                'nis' => $m->nis ?? '',
            ];
        })->values();

        return view('admin.organization.create', compact('members', 'membersJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'level' => 'required|integer|in:1,2,3,4,5',
            'order_position' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['order_position'] = $validated['order_position'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        OrganizationMember::create($validated);

        return redirect()->route('admin.organization.index')->with('success', 'Data pengurus/pejabat baru berhasil ditambahkan!');
    }

    public function edit(OrganizationMember $member)
    {
        $members = collect();
        try {
            $members = Member::orderBy('name')->get(['id', 'name', 'class_grade', 'nis']);
        } catch (\Throwable $e) {
            $members = collect();
        }

        $membersJson = $members->map(function($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'class_grade' => $m->class_grade ?? '',
                'nis' => $m->nis ?? '',
            ];
        })->values();

        return view('admin.organization.edit', compact('member', 'members', 'membersJson'));
    }

    public function update(Request $request, OrganizationMember $member)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'level' => 'required|integer|in:1,2,3,4,5',
            'order_position' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['order_position'] = $validated['order_position'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $member->update($validated);

        return redirect()->route('admin.organization.index')->with('success', "Data pengurus '{$member->position}' ({$member->name}) berhasil diperbarui!");
    }

    public function destroy(OrganizationMember $member)
    {
        $name = $member->name;
        $position = $member->position;
        $member->delete();

        return redirect()->route('admin.organization.index')->with('success', "Pengurus '{$position}' ({$name}) telah berhasil dihapus dari bagan kepengurusan.");
    }
}

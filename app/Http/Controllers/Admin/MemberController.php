<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $query = Member::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('class_grade', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('name')->paginate(15)->withQueryString();
        $totalMembers = Member::count();

        return view('admin.members.index', compact('members', 'search', 'totalMembers'));
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

        $member->update($validated);

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$member->name}' berhasil diperbarui!");
    }

    public function destroy(Member $member)
    {
        $name = $member->name;
        $member->delete();

        return redirect()->route('admin.members.index')->with('success', "Data anggota '{$name}' berhasil dihapus.");
    }
}

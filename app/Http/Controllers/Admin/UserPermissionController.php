<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Support\PermissionRegistry;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * Tampilkan daftar anggota & status hak aksesnya
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $filterMode = $request->input('mode'); // 'custom', 'default', or null
        $filterStatus = $request->input('status'); // 'Aktif', 'Purna', or null

        $query = Member::query();

        // Search berdasarkan Nama, NRI, atau Jabatan
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nri', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter status keaktifan
        if (! empty($filterStatus) && in_array($filterStatus, ['Aktif', 'Purna'], true)) {
            $query->where('member_status', $filterStatus);
        }

        // Filter mode hak akses
        if ($filterMode === 'custom') {
            $query->whereNotNull('custom_permissions');
        } elseif ($filterMode === 'default') {
            $query->whereNull('custom_permissions');
        }

        $users = $query->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Statistik ringkasan
        $totalUsers = Member::count();
        $totalCustom = Member::whereNotNull('custom_permissions')->count();
        $totalDefault = Member::whereNull('custom_permissions')->count();

        return view('admin.user-permissions.index', compact(
            'users',
            'search',
            'filterMode',
            'filterStatus',
            'totalUsers',
            'totalCustom',
            'totalDefault'
        ));
    }

    /**
     * Tampilkan halaman/modal editor hak akses granular pengguna
     */
    public function edit(int $id)
    {
        $user = Member::findOrFail($id);
        $modules = PermissionRegistry::all();
        $presets = PermissionRegistry::presets();

        // Ambil daftar izin aktif saat ini
        $activePermissions = [];
        $canDelete = false;
        $canExport = true;
        $isCustom = $user->hasCustomPermissions();

        if ($isCustom && is_array($user->custom_permissions)) {
            $activePermissions = $user->custom_permissions['allowed_menus'] ?? [];
            $canDelete = (bool) ($user->custom_permissions['can_delete'] ?? false);
            $canExport = (bool) ($user->custom_permissions['can_export'] ?? true);
        } else {
            // Jika masih bawaan jabatan, kumpulkan permission bawaannya
            foreach ($modules as $module) {
                foreach ($module['items'] as $code => $perm) {
                    if ($user->hasPermission($code)) {
                        $activePermissions[] = $code;
                    }
                }
            }
            $canDelete = $user->canDelete();
            $canExport = true;
        }

        return view('admin.user-permissions.edit', compact(
            'user',
            'modules',
            'presets',
            'activePermissions',
            'canDelete',
            'canExport',
            'isCustom'
        ));
    }

    /**
     * Simpan pembaruan hak akses granular pengguna
     */
    public function update(Request $request, int $id)
    {
        $user = Member::findOrFail($id);

        $validated = $request->validate([
            'mode' => 'required|in:default,custom',
            'allowed_menus' => 'nullable|array',
            'allowed_menus.*' => 'string',
            'can_delete' => 'nullable|boolean',
            'can_export' => 'nullable|boolean',
        ]);

        if ($validated['mode'] === 'default') {
            // Reset kembali ke wewenang otomatis bawaan jabatan
            $user->custom_permissions = null;
            $user->save();

            return redirect()->route('admin.user-permissions.index')->with(
                'success',
                "Hak akses {$user->name} berhasil dikembalikan ke standar bawaan jabatannya ({$user->role_label})."
            );
        }

        // Simpan izin kustom granular
        $user->custom_permissions = [
            'mode' => 'custom',
            'allowed_menus' => $request->input('allowed_menus', []),
            'can_delete' => $request->boolean('can_delete'),
            'can_export' => $request->boolean('can_export'),
            'updated_at' => now()->toIso8601String(),
            'updated_by' => auth()->user()->name ?? 'Administrator',
        ];

        $user->save();

        $count = count($user->custom_permissions['allowed_menus']);

        return redirect()->route('admin.user-permissions.index')->with(
            'success',
            "Hak akses kustom untuk {$user->name} berhasil diperbarui ({$count} wewenang menu aktif)."
        );
    }

    /**
     * Reset hak akses pengguna ke bawaan jabatan
     */
    public function reset(Request $request, int $id)
    {
        $user = Member::findOrFail($id);
        $user->custom_permissions = null;
        $user->save();

        return redirect()->back()->with(
            'success',
            "Hak akses {$user->name} telah direset ke bawaan jabatannya ({$user->role_label})."
        );
    }
}

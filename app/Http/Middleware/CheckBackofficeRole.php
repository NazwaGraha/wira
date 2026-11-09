<?php

namespace App\Http\Middleware;

use App\Models\Member;
use App\Support\PermissionRegistry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBackofficeRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        /** @var Member $user */
        $user = Auth::user();

        // 1. Anggota dengan status Purna tidak diberikan akses ke backoffice
        if (! method_exists($user, 'canAccessBackoffice') || ! $user->canAccessBackoffice()) {
            if (method_exists($user, 'isPurna') && $user->isPurna()) {
                return redirect()->route('member.profile')->with(
                    'error',
                    'Akses Ditolak: Anggota dengan status Purna sudah tidak diberikan akses ke Backoffice.'
                );
            }

            return redirect()->route('member.profile')->with('info', 'Anda telah masuk ke Client Area Profil Anggota PMR.');
        }

        // Super User memiliki akses penuh tanpa batas
        if (method_exists($user, 'isSuperUser') && $user->isSuperUser()) {
            return $next($request);
        }

        $routeName = (string) ($request->route()?->getName() ?? '');

        // 2. Jika user memiliki Custom Permissions (Granular)
        if (method_exists($user, 'hasCustomPermissions') && $user->hasCustomPermissions()) {
            // Rute dasar yang selalu diizinkan untuk semua pengguna yang berhak masuk backoffice
            $universalRoutes = [
                'admin.index',
                'admin.logout',
            ];

            if (in_array($routeName, $universalRoutes, true)) {
                return $next($request);
            }

            // Kumpulkan pola rute yang diizinkan dari custom_permissions
            $allowedCodes = $user->custom_permissions['allowed_menus'] ?? [];
            $allModules = PermissionRegistry::all();
            $allowedRoutePatterns = [];

            foreach ($allModules as $module) {
                foreach ($module['items'] as $code => $perm) {
                    if (in_array($code, $allowedCodes, true)) {
                        foreach ($perm['routes'] as $pattern) {
                            $allowedRoutePatterns[] = $pattern;
                        }
                    }
                }
            }

            // Izinkan dashboard jika memiliki izin dashboard.view
            if (in_array('dashboard.view', $allowedCodes, true)) {
                $allowedRoutePatterns[] = 'admin.dashboard';
            }

            $isAllowed = false;
            foreach ($allowedRoutePatterns as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (! $isAllowed) {
                // Tentukan halaman fallback aman
                $fallbackRoute = in_array('dashboard.view', $allowedCodes, true)
                    ? 'admin.dashboard'
                    : 'member.profile';

                return redirect()->route($fallbackRoute)->with(
                    'error',
                    'Akses Dibatasi: Akun Anda tidak memiliki wewenang untuk membuka menu tersebut.'
                );
            }

            // Validasi izin aksi Hapus Data (DELETE)
            if ($request->isMethod('DELETE')) {
                if (method_exists($user, 'canDeleteData') && ! $user->canDeleteData()) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Akses Ditolak: Akun Anda tidak diberikan wewenang untuk menghapus data.',
                        ], 403);
                    }

                    return back()->with('error', 'Akses Ditolak: Akun Anda tidak diberikan wewenang untuk menghapus data.');
                }
            }

            return $next($request);
        }

        // 3. Fallback: Role Otomatis Bawaan Jabatan (Default RBAC)
        // Proteksi Modul Manajemen Hak Akses Pengguna (Hanya Super User, Pembina & Ketum)
        if (str_starts_with($routeName, 'admin.user-permissions.')) {
            if (method_exists($user, 'canManagePermissions') && ! $user->canManagePermissions()) {
                return redirect()->route('admin.dashboard')->with(
                    'error',
                    'Akses Ditolak: Hanya Super Administrator, Pembina, atau Ketua Umum yang dapat mengelola hak akses.'
                );
            }
        }

        // Anggota Aktif Biasa: Berhak mengakses Dashboard & Pengelolaan Data Anggota (Daftar & Input Anggota)
        if ($user->role === 'anggota') {
            $allowedRoutePatterns = [
                'admin.dashboard',
                'admin.members.*',
                'admin.logout',
            ];

            $isAllowed = false;
            foreach ($allowedRoutePatterns as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (! $isAllowed) {
                return redirect()->route('admin.members.index')->with(
                    'error',
                    'Akses Terbatas: Sebagai Anggota Aktif, wewenang Anda di Backoffice adalah pengelolaan Data Anggota.'
                );
            }
        }

        // Staff 5 Bidang: Hak akses untuk modul Artikel & modul Data Anggota
        if (method_exists($user, 'canOnlyManageArticles') && $user->canOnlyManageArticles()) {
            $allowedRoutePatterns = [
                'admin.dashboard',
                'admin.articles.*',
                'admin.categories.*',
                'admin.members.*',
                'admin.logout',
            ];

            $isAllowed = false;
            foreach ($allowedRoutePatterns as $pattern) {
                if (fnmatch($pattern, $routeName)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (! $isAllowed) {
                return redirect()->route('admin.articles.index')->with(
                    'error',
                    'Akses Dibatasi: Sebagai Staff Bidang, wewenang Anda terbatas pada pengelolaan Artikel dan Data Anggota.'
                );
            }
        }

        // Ketua Bidang & Staff Bidang: Tidak memiliki hak akses DELETE
        if ($request->isMethod('DELETE')) {
            if (method_exists($user, 'canDelete') && ! $user->canDelete()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Akses Ditolak: Jabatan Anda tidak memiliki wewenang untuk menghapus data.',
                    ], 403);
                }

                return back()->with('error', 'Akses Ditolak: Jabatan Anda tidak memiliki wewenang untuk menghapus data.');
            }
        }

        return $next($request);
    }
}

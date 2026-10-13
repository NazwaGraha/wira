<?php

namespace App\Http\Middleware;

use App\Models\Member;
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

        $routeName = (string) ($request->route()?->getName() ?? '');

        // 2. Anggota Aktif Biasa: Berhak mengakses Dashboard & Pengelolaan Data Anggota (Daftar & Input Anggota)
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

        // 3. Staff 5 Bidang: Hak akses untuk modul Artikel & modul Data Anggota
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

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

        // Anggota biasa diarahkan ke Client Area
        if (! method_exists($user, 'canAccessBackoffice') || ! $user->canAccessBackoffice()) {
            return redirect()->route('member.profile')->with('info', 'Anda telah masuk ke Client Area Profil Anggota PMR.');
        }

        // Staff 5 Bidang: Hak akses terbatas hanya untuk modul Artikel
        if (method_exists($user, 'canOnlyManageArticles') && $user->canOnlyManageArticles()) {
            $routeName = (string) ($request->route()?->getName() ?? '');

            $allowedRoutePatterns = [
                'admin.dashboard',
                'admin.articles.*',
                'admin.categories.*',
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
                    'Akses Dibatasi: Sebagai Staff Bidang, wewenang Anda terbatas pada pengelolaan Artikel & Tips P3K.'
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

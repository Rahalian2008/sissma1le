<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenagaKependidikan
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super Admin, Admin, and Kepala Sekolah always have administrative access
        if ($user->isAdmin() || in_array($user->role, ['super_admin', 'admin', 'kepala_sekolah'], true)) {
            return $next($request);
        }

        // Akses SPMB & PPDB diizinkan untuk Admin Kesiswaan / Wakasek Kesiswaan
        if (($request->is('spmb*') || $request->is('ppdb*')) && $user->isKesiswaanAdmin()) {
            return $next($request);
        }

        // Aturan: Tenaga Pendidik (primary role === 'guru') dilarang keras mengakses Adm Ketatausahaan
        if ($user->role === 'guru') {
            abort(403, 'Akses Ditolak: Aplikasi Adm. Ketatausahaan hanya untuk peran Tenaga Kependidikan (Staf TU). Tenaga Pendidik tidak memiliki akses ke modul ini.');
        }

        // Akses khusus Tenaga Kependidikan
        if (! $user->canAccessTuModule()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses aplikasi Adm. Ketatausahaan.');
        }

        return $next($request);
    }
}

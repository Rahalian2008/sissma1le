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
        if ($user->isAdmin() || $user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']) || in_array($user->role, ['super_admin', 'admin', 'kepala_sekolah'], true)) {
            return $next($request);
        }

        // Akses SPMB, PPDB, Kesiswaan & Beasiswa diizinkan untuk Admin Kesiswaan / Wakasek Kesiswaan
        if (($request->is('spmb*') || $request->is('ppdb*') || $request->is('tu/kesiswaan*') || $request->is('students/scholarships*') || $request->is('scholarships*')) && $user->isKesiswaanAdmin()) {
            return $next($request);
        }

        // Aturan: Tenaga Pendidik murni dilarang keras mengakses Adm Ketatausahaan (kecuali memiliki tugas wewenang TU/Sarpras/Kesiswaan yang sah)
        if ($user->role === 'guru' && ! $user->hasAnyRole(['kepala_sekolah', 'wakasek_sarpras', 'wakasek_kesiswaan', 'kesiswaan', 'ta_sarpras', 'ta_kesiswaan', 'kepala_administrasi', 'operator_dapodik'])) {
            abort(403, 'Akses Ditolak: Aplikasi Adm. Ketatausahaan hanya untuk peran Tenaga Kependidikan (Staf TU). Tenaga Pendidik tidak memiliki akses ke modul ini.');
        }

        // Akses khusus Tenaga Kependidikan
        if (! $user->canAccessTuModule()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses aplikasi Adm. Ketatausahaan.');
        }

        return $next($request);
    }
}

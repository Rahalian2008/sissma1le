<?php

namespace App\Support;

use App\Models\Extracurricular;
use App\Models\SchoolClass;
use App\Models\User;

class RolePermissionMatrix
{
    /**
     * Definisi matriks modul aplikasi dan role yang berhak mengaksesnya.
     *
     * @return array<string, array<string>>
     */
    public static function getModuleRoleMap(): array
    {
        return [
            'dashboard' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kurikulum', 'wakasek_kesiswaan', 'wakasek_humas', 'wakasek_sarpras',
                'kepala_administrasi', 'koordinator_bk', 'guru_bk', 'bk',
                'kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan',
                'wali_kelas', 'pembina_ekstra', 'koordinator_keagamaan',
                'guru_piket', 'tu_piket', 'petugas_piket',
                'bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji',
                'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras',
                'operator', 'operator_dapodik', 'kepala_laboratorium',
                'guru', 'staff', 'tu', 'tenaga_administrasi',
                'siswa', 'sekretaris_kelas', 'sekretaris_ekstra', 'orang_tua', 'adm_web',
            ],

            'master_data' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'kepala_administrasi', 'operator_dapodik', 'operator',
            ],

            'kesiswaan' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan', 'ta_kesiswaan',
                'wali_kelas', 'pembina_ekstra',
                'guru_piket', 'tu_piket', 'petugas_piket',
                'koordinator_keagamaan', 'guru', 'siswa', 'orang_tua',
            ],

            'attendance' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan', 'ta_kesiswaan',
                'guru_piket', 'tu_piket', 'petugas_piket',
                'wali_kelas', 'guru', 'staff', 'tu', 'siswa', 'orang_tua',
            ],

            'early_leave' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan', 'ta_kesiswaan',
                'guru_piket', 'tu_piket', 'petugas_piket',
                'wali_kelas', 'guru', 'siswa', 'orang_tua',
            ],

            'school_pass' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan', 'ta_kesiswaan',
                'guru_piket', 'tu_piket', 'petugas_piket',
                'wali_kelas', 'guru', 'siswa',
            ],

            'spmb' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan', 'ta_kesiswaan',
                'operator_dapodik', 'operator', 'kepala_administrasi',
            ],

            'extracurricular' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan',
                'pembina_ekstra', 'sekretaris_ekstra', 'siswa',
            ],

            'discipline' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan',
                'koordinator_bk', 'guru_bk', 'bk',
                'wali_kelas', 'guru_piket', 'tu_piket', 'petugas_piket',
                'guru', 'siswa', 'orang_tua',
            ],

            'tahfidz' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'koordinator_keagamaan',
                'wali_kelas', 'guru', 'siswa', 'orang_tua',
            ],

            'habits' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'koordinator_keagamaan',
                'wali_kelas', 'guru', 'siswa', 'orang_tua',
            ],

            'raport_digital' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'wali_kelas', 'siswa', 'orang_tua',
            ],

            'kiosk' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'guru_piket', 'tu_piket', 'petugas_piket',
                'kepala_administrasi',
            ],

            'kurikulum' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kurikulum', 'kurikulum', 'guru', 'wali_kelas', 'siswa',
            ],

            'lms' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kurikulum', 'kurikulum', 'guru', 'siswa',
            ],

            'rapor_kurnas' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kurikulum', 'kurikulum', 'wali_kelas', 'guru',
                'siswa', 'orang_tua',
            ],

            'bk' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'koordinator_bk', 'guru_bk', 'bk',
            ],

            'library' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan',
                'kepala_administrasi', 'staff', 'tu', 'guru', 'wali_kelas', 'siswa',
            ],

            'tu' => [
                'super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi',
                'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras',
                'wakasek_sarpras', 'kepala_laboratorium',
                'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'bendahara',
                'operator_dapodik', 'operator', 'tu_piket',
                'staff', 'tu', 'tenaga_administrasi',
            ],

            'tu_kesiswaan' => [
                'super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi',
                'ta_kesiswaan', 'operator_dapodik', 'operator',
            ],

            'tu_kepegawaian' => [
                'super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi',
                'ta_kepegawaian', 'operator_dapodik', 'operator',
            ],

            'tu_persuratan' => [
                'super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi',
                'ta_persuratan', 'tu_piket',
            ],

            'tu_sarpras' => [
                'super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi',
                'ta_sarpras', 'wakasek_sarpras', 'kepala_laboratorium',
            ],

            'tu_keuangan' => [
                'super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi',
                'bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji',
            ],

            'school_bell' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kesiswaan', 'kesiswaan',
                'wakasek_kurikulum', 'kurikulum',
                'wakasek_humas', 'humas',
                'wakasek_sarpras', 'sarpras',
                'tu_piket', 'guru_piket', 'petugas_piket',
            ],

            'website_content' => [
                'super_admin', 'admin', 'kepala_sekolah',
                'wakasek_kurikulum', 'kurikulum',
                'wakasek_kesiswaan', 'kesiswaan',
                'wakasek_humas', 'humas',
                'wakasek_sarpras', 'sarpras',
                'koordinator_bk', 'guru_bk', 'bk',
                'kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan',
                'koordinator_keagamaan',
                'guru', 'wali_kelas', 'pembina_ekstra',
                'kepala_administrasi', 'staff', 'tu', 'tenaga_administrasi',
                'operator_dapodik', 'operator',
                'tu_piket', 'guru_piket', 'adm_web',
            ],
        ];
    }

    /**
     * Evaluasi apakah user memiliki izin mengakses menu/fitur tertentu berdasarkan mode peran aktif.
     */
    public static function canAccess(User $user, string $menuKey, ?string $activeRole = null): bool
    {
        $map = self::getModuleRoleMap();
        $allowed = $map[$menuKey] ?? ['super_admin', 'admin'];

        // 1. Super Admin dan Admin selalu memiliki akses penuh ke seluruh modul
        if ($user->hasAnyRole(['super_admin', 'admin']) || in_array($user->role, ['super_admin', 'admin'], true)) {
            return true;
        }

        // 2. Kepala Sekolah memiliki akses supervisi ke seluruh modul aplikasi sekolah
        if ($user->hasRole('kepala_sekolah') || $user->role === 'kepala_sekolah') {
            return true;
        }

        $active = $activeRole ?? session('active_role') ?? $user->active_role;

        // 3. Batasan Tenaga Pendidik murni untuk modul Tata Usaha
        if (str_starts_with($menuKey, 'tu')) {
            $hasTuAuthority = $user->hasAnyRole([
                'kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras',
                'bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji',
                'wakasek_sarpras', 'kepala_laboratorium', 'operator_dapodik', 'operator',
                'staff', 'tu', 'tenaga_administrasi', 'tu_piket',
            ]);

            if (! $hasTuAuthority) {
                return false;
            }
        }

        // 4. Mode Semua Peran (Akses Lengkap)
        if ($active === 'all_roles') {
            return $user->hasAnyRole($allowed);
        }

        // 5. Mode Peran Aktif Tertentu (Role Switcher Aktif)
        return in_array($active, $allowed, true);
    }

    /**
     * Evaluasi granular hak akses ke 5 sub-bagian Tata Usaha.
     */
    public static function canAccessTuSubmodule(User $user, string $submodule, ?string $activeRole = null): bool
    {
        $menuKey = 'tu_'.$submodule;

        // KTU, Kepsek, Admin selalu punya akses penuh ke seluruh sub-bagian TU
        if ($user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi']) || in_array($user->role, ['super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi'], true)) {
            return true;
        }

        $active = $activeRole ?? session('active_role') ?? $user->active_role;
        $map = self::getModuleRoleMap()[$menuKey] ?? [];

        // Jika mode peran aktif spesifik (selain staff umum)
        if ($active !== 'all_roles' && ! in_array($active, ['staff', 'tu', 'tenaga_administrasi'], true)) {
            return in_array($active, $map, true);
        }

        // Jika dalam mode all_roles atau mode staff umum
        if ($user->hasAnyRole($map)) {
            return true;
        }

        // Fallback untuk staf umum yang belum memiliki sub-role spesifik
        $hasSpecificTuRole = $user->hasAnyRole([
            'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras',
            'bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji',
            'wakasek_sarpras', 'kepala_laboratorium',
        ]);

        if (! $hasSpecificTuRole && $user->hasAnyRole(['staff', 'tu', 'tenaga_administrasi'])) {
            return true;
        }

        return false;
    }

    /**
     * Menghasilkan judul peran yang formal, elegan, dan informatif untuk UI.
     */
    public static function getRoleTitle(string $role, ?User $user = null): string
    {
        return match ($role) {
            'all_roles' => 'Semua Peran (Akses Lengkap)',
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'kepala_sekolah' => 'Kepala Sekolah',
            'wakasek_kurikulum' => 'Wakasek Kurikulum',
            'wakasek_kesiswaan' => 'Wakasek Kesiswaan',
            'wakasek_humas' => 'Wakasek Humas',
            'wakasek_sarpras' => 'Wakasek Sarpras',
            'kepala_administrasi' => 'Kepala Tata Usaha (KTU)',
            'koordinator_bk' => 'Koordinator BP/BK',
            'guru_bk', 'bk' => 'Guru Bimbingan Konseling (BK)',
            'kepala_perpustakaan' => 'Kepala Perpustakaan',
            'pengelola_perpustakaan', 'pustakawan' => 'Pengelola Perpustakaan',
            'wali_kelas' => self::resolveHomeroomTitle($user),
            'pembina_ekstra' => self::resolveExtracurricularTitle($user),
            'koordinator_keagamaan' => 'Koord. Keagamaan & Tahfidz',
            'guru_piket' => 'Guru Piket Sekolah',
            'tu_piket' => 'TU Piket Sekolah',
            'petugas_piket' => 'Petugas Piket',
            'bendahara', 'bendahara_bosp' => 'Bendahara Sekolah (BOSP)',
            'bendahara_bopd' => 'Bendahara BOPD',
            'bendahara_gaji' => 'Bendahara Gaji',
            'ta_kesiswaan' => 'Adm. Kesiswaan',
            'ta_kepegawaian' => 'Adm. Kepegawaian',
            'ta_persuratan' => 'Adm. Persuratan & Arsip',
            'ta_sarpras' => 'Adm. Sarpras & BMD',
            'operator_dapodik' => 'Operator Dapodik',
            'operator' => 'Operator Data Sekolah',
            'kepala_laboratorium' => 'Kepala Laboratorium',
            'guru' => self::resolveTeacherTitle($user),
            'staff', 'tu', 'tenaga_administrasi' => 'Tenaga Kependidikan (Staf TU)',
            'siswa' => 'Peserta Didik (Siswa)',
            'sekretaris_kelas' => 'Sekretaris Kelas',
            'sekretaris_ekstra' => 'Sekretaris Ekstrakurikuler',
            'orang_tua' => 'Orang Tua / Wali Murid',
            'adm_web' => 'Pengelola Konten Web Siswa',
            default => ucwords(str_replace('_', ' ', $role)),
        };
    }

    private static function resolveHomeroomTitle(?User $user): string
    {
        if ($user && $user->teacher) {
            $class = SchoolClass::where('homeroom_teacher_id', $user->teacher->id)->first();
            if ($class) {
                return 'Wali Kelas '.$class->name;
            }
        }

        return 'Wali Kelas';
    }

    private static function resolveExtracurricularTitle(?User $user): string
    {
        if ($user && $user->teacher) {
            $ekstras = Extracurricular::where('teacher_id', $user->teacher->id)->pluck('name')->toArray();
            if (! empty($ekstras)) {
                return 'Pembina '.implode(' & ', array_slice($ekstras, 0, 2));
            }
        }

        return 'Pembina Ekstrakurikuler';
    }

    private static function resolveTeacherTitle(?User $user): string
    {
        if ($user && $user->teacher && ! empty($user->teacher->title)) {
            return 'Guru '.$user->teacher->title;
        }

        return 'Guru Mata Pelajaran';
    }
}

<?php

namespace App\Models;

use App\Support\RolePermissionMatrix;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'nip',
        'phone',
        'role',
        'roles',
        'avatar',
        'is_active',
        'active_role_id',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'active_role_id' => 'integer',
            'roles' => 'array',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
        ];
    }

    /**
     * Check if user has a specific role (checks both `role` and `roles` array and pivot).
     */
    public function hasRole(string $role): bool
    {
        if ($this->role === $role) {
            return true;
        }

        // Check against raw attributes or parsed roles
        $userRoles = is_array($this->roles) ? $this->roles : (json_decode($this->roles ?? '[]', true) ?: []);
        if (in_array($role, $userRoles, true)) {
            return true;
        }

        if ($this->relationLoaded('roles')) {
            $relationRoles = $this->getRelation('roles');
            if ($relationRoles instanceof \Illuminate\Support\Collection) {
                return $relationRoles->contains(function ($r) use ($role) {
                    if (is_object($r) && isset($r->name)) {
                        return $r->name === $role;
                    }
                    if (is_array($r) && isset($r['name'])) {
                        return $r['name'] === $role;
                    }
                    if (is_string($r)) {
                        return $r === $role;
                    }

                    return false;
                });
            }
        }

        try {
            return $this->roles()->where('name', $role)->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param  array<string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function isTu(): bool
    {
        return $this->hasAnyRole([
            'tu', 'staff', 'kepala_administrasi', 'tenaga_administrasi', 'admin', 'super_admin',
            'bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji',
            'operator', 'operator_dapodik', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_sarpras', 'ta_persuratan',
            'tu_piket', 'petugas_piket',
        ]);
    }

    /**
     * Check if user has active and confirmed Two-Factor Authentication.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! empty($this->two_factor_secret) && ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get decrypted recovery codes as an array.
     *
     * @return array<string>
     */
    public function getRecoveryCodes(): array
    {
        return is_array($this->two_factor_recovery_codes) ? $this->two_factor_recovery_codes : [];
    }

    /**
     * Consume a recovery code if valid.
     */
    public function useRecoveryCode(string $code): bool
    {
        $code = strtolower(trim($code));
        $codes = $this->getRecoveryCodes();

        $index = array_search($code, $codes, true);
        if ($index !== false) {
            unset($codes[$index]);
            $this->two_factor_recovery_codes = array_values($codes);
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Get all roles combining primary `role` and `roles` array.
     *
     * @return array<string>
     */
    public function getAllRolesAttribute(): array
    {
        $rawRoles = $this->attributes['roles'] ?? null;
        if (is_array($rawRoles)) {
            $roles = $rawRoles;
        } elseif (is_string($rawRoles)) {
            $roles = json_decode($rawRoles, true) ?: [];
        } else {
            $roles = [];
        }

        // Ensure all elements in $roles are strings
        $cleanRoles = [];
        foreach ($roles as $item) {
            if (is_string($item)) {
                $cleanRoles[] = $item;
            } elseif (is_array($item) && isset($item['name']) && is_string($item['name'])) {
                $cleanRoles[] = $item['name'];
            } elseif (is_object($item) && isset($item->name) && is_string($item->name)) {
                $cleanRoles[] = $item->name;
            }
        }
        $roles = $cleanRoles;

        if ($this->role && ! in_array($this->role, $roles, true)) {
            array_unshift($roles, $this->role);
        }

        if ($this->relationLoaded('roles')) {
            $relationRoles = $this->getRelation('roles');
            if ($relationRoles) {
                foreach ($relationRoles as $r) {
                    $roleName = is_object($r) ? ($r->name ?? null) : (is_array($r) ? ($r['name'] ?? null) : (is_string($r) ? $r : null));
                    if ($roleName && is_string($roleName) && ! in_array($roleName, $roles, true)) {
                        $roles[] = $roleName;
                    }
                }
            }
        }

        return ! empty($roles) ? array_values(array_unique(array_filter($roles, 'is_string'))) : ['user'];
    }

    public function getAllRoleNames(): array
    {
        return $this->all_roles;
    }

    /**
     * Get all active role options available for multi-role switching.
     */
    public function getActiveRoleOptions(): array
    {
        return $this->all_roles;
    }

    /**
     * Hak akses presensi manual (Presensi Utama, Shalat Dzuhur/Jum'at, Shalat Dhuha, Keputrian).
     * Aturan: Hanya bisa dilaksanakan oleh Wakasek Kesiswaan, Wali Kelas pada kelasnya,
     * dan Sekretaris Kelas pada kelasnya (serta Admin/Super Admin).
     */
    public function canRecordAttendanceManual(?int $classId = null): bool
    {
        // Admin & Super Admin selalu memiliki akses administratif penuh
        if ($this->hasAnyRole(['admin', 'super_admin'])) {
            return true;
        }

        // 1. Wakasek Kesiswaan / Staf Kesiswaan berwenang untuk seluruh kelas
        if ($this->hasAnyRole(['wakasek_kesiswaan', 'kesiswaan'])) {
            return true;
        }

        // 2. Wali Kelas hanya untuk kelas binaannya sendiri
        if ($this->hasRole('wali_kelas')) {
            $homeroomClassId = $this->teacher?->homeroomClass?->id;
            if (! $homeroomClassId) {
                return false;
            }

            return $classId === null || (int) $homeroomClassId === (int) $classId;
        }

        // 3. Sekretaris Kelas hanya untuk kelasnya sendiri
        if ($this->hasRole('sekretaris_kelas')) {
            $studentClassId = $this->student?->class_id;
            if (! $studentClassId) {
                return false;
            }

            return $classId === null || (int) $studentClassId === (int) $classId;
        }

        return false;
    }

    /**
     * Hak akses presensi manual ekstrakurikuler.
     * Aturan: Hanya bisa dilaksanakan oleh Wakasek Kesiswaan, Pembina Ekstrakurikuler,
     * dan Sekretaris Ekstrakurikuler (serta Admin/Super Admin).
     */
    public function canRecordExtracurricularManual(?int $extracurricularId = null): bool
    {
        if ($this->hasAnyRole(['admin', 'super_admin', 'wakasek_kesiswaan', 'kesiswaan'])) {
            return true;
        }

        if ($this->hasRole('pembina_ekstra')) {
            if ($extracurricularId !== null && $this->teacher) {
                $extra = Extracurricular::find($extracurricularId);
                if ($extra && $extra->all_teachers->isNotEmpty()) {
                    return $extra->all_teachers->contains('id', $this->teacher->id);
                }
            }

            return true;
        }

        if ($this->hasRole('sekretaris_ekstra')) {
            if ($extracurricularId !== null && $this->student) {
                return $this->isExtracurricularSecretary($extracurricularId)
                    || $this->isExtracurricularMember($extracurricularId);
            }

            return true;
        }

        return false;
    }

    public function canManageSchoolPermits(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan', 'kepala_sekolah', 'staff', 'tu', 'guru_piket', 'tu_piket', 'petugas_piket', 'ta_kesiswaan', 'kepala_administrasi']);
    }

    /**
     * Get the active role for current user session or primary fallback.
     */
    public function getActiveRoleAttribute(): string
    {
        $sessionRole = session('active_role');
        if ($sessionRole === 'all_roles') {
            return 'all_roles';
        }

        if ($sessionRole && ($this->hasRole($sessionRole) || $this->hasAnyRole(['super_admin', 'admin']))) {
            return $sessionRole;
        }

        if ($this->active_role_id) {
            try {
                $roleModel = Role::find($this->active_role_id);
                if ($roleModel && ($this->hasRole($roleModel->name) || $this->hasAnyRole(['super_admin', 'admin']))) {
                    return $roleModel->name;
                }
            } catch (\Throwable) {
                // Ignore fallback
            }
        }

        return $this->role ?: ($this->all_roles[0] ?? 'user');
    }

    public function switchActiveRole(string|int $role): bool
    {
        if ($role === 'all_roles') {
            session(['active_role' => 'all_roles']);

            return true;
        }

        if (is_numeric($role)) {
            $roleModel = Role::find($role);
            $roleName = $roleModel?->name;
        } else {
            $roleName = $role;
            $roleModel = Role::where('name', $roleName)->first();
        }

        if (! $roleName || (! $this->hasRole($roleName) && ! $this->hasAnyRole(['super_admin', 'admin']))) {
            return false;
        }

        session(['active_role' => $roleName]);
        if ($roleModel) {
            $this->updateQuietly(['active_role_id' => $roleModel->id]);
        }

        return true;
    }

    /**
     * Check if the given menu or feature is accessible in the user's currently active role.
     */
    public function canAccessInActiveRole(string $menuKey): bool
    {
        return RolePermissionMatrix::canAccess($this, $menuKey, $this->active_role);
    }

    public function canManageTahfidz(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'koordinator_keagamaan', 'kepala_sekolah']);
    }

    public function isExtracurricularMember(): bool
    {
        return $this->hasAnyRole(['pembina_ekstra', 'sekretaris_ekstra', 'admin', 'super_admin']);
    }

    public function isTenagaAdministrasi(): bool
    {
        return $this->hasAnyRole(['tu', 'staff', 'kepala_administrasi', 'tenaga_administrasi', 'admin', 'super_admin']);
    }

    public function canManageLibrary(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin', 'kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan']);
    }

    public function canManageWebsiteContent(): bool
    {
        if ($this->hasAnyRole(['admin', 'super_admin', 'kepala_sekolah']) || in_array($this->role, ['admin', 'super_admin', 'kepala_sekolah'], true)) {
            return true;
        }

        $active = $this->active_role;
        $rolesToCheck = ($active === 'all_roles') ? $this->all_roles : array_unique(array_filter([$active, $this->role]));

        $allowedRoles = [
            'admin', 'super_admin', 'kepala_sekolah',
            'wakasek_kurikulum', 'kurikulum',
            'wakasek_kesiswaan', 'kesiswaan',
            'wakasek_humas', 'humas',
            'wakasek_sarpras', 'sarpras',
            'koordinator_bk', 'bk_koordinator', 'guru_bk', 'bk',
            'kepala_perpustakaan', 'perpus_kepala',
            'koordinator_keagamaan', 'keagamaan',
            'guru', 'wali_kelas', 'pembina_ekstra', 'pembina_ekskul',
            'tu', 'staff', 'tenaga_administrasi', 'kepala_administrasi',
            'operator_dapodik', 'operator', 'tu_piket', 'guru_piket',
            'adm_web',
        ];

        return ! empty(array_intersect($rolesToCheck, $allowedRoles)) || $this->hasAnyRole($allowedRoles);
    }

    public function canAccessSchoolBell(): bool
    {
        if ($this->hasAnyRole(['admin', 'super_admin', 'kepala_sekolah']) || in_array($this->role, ['admin', 'super_admin', 'kepala_sekolah'], true)) {
            return true;
        }

        $active = $this->active_role;
        $rolesToCheck = ($active === 'all_roles') ? $this->all_roles : array_unique(array_filter([$active, $this->role]));

        $allowedRoles = [
            'admin', 'super_admin', 'kepala_sekolah',
            'wakasek_kesiswaan', 'kesiswaan',
            'wakasek_kurikulum', 'kurikulum',
            'wakasek_humas', 'humas',
            'wakasek_sarpras', 'sarpras',
            'tu_piket', 'guru_piket', 'petugas_piket',
        ];

        return ! empty(array_intersect($rolesToCheck, $allowedRoles)) || $this->hasAnyRole($allowedRoles);
    }

    /**
     * Mengambil daftar tab kelola konten website yang boleh diakses pengguna sesuai peran aktif/otoritasnya.
     *
     * @return array<string>
     */
    public function allowedWebsiteContentTabs(): array
    {
        // 1. Akun Admin/Super Admin & Kepala Sekolah: akses penuh semua fitur
        if ($this->hasAnyRole(['admin', 'super_admin', 'kepala_sekolah'])
            || in_array($this->role, ['admin', 'super_admin', 'kepala_sekolah'], true)
            || in_array($this->active_role, ['admin', 'super_admin', 'kepala_sekolah'], true)) {
            return ['news', 'hero', 'running_text', 'headmaster', 'vision', 'media', 'agendas', 'facilities', 'staff'];
        }

        $active = $this->active_role;
        $rolesToCheck = ($active === 'all_roles') ? $this->all_roles : array_unique(array_filter([$active, $this->role]));

        // 2. Akun Wakasek Sarpras: Berita & Kabar, Agenda, dan Fasilitas Sekolah
        if (! empty(array_intersect(['wakasek_sarpras', 'sarpras'], $rolesToCheck)) || $this->hasAnyRole(['wakasek_sarpras', 'sarpras'])) {
            return ['news', 'agendas', 'facilities'];
        }

        // 3. Akun Wakasek Kurikulum, Wakasek Kesiswaan, Wakasek Humas, Koordinator BK, Kepala Perpustakaan, Koordinator Keagamaan: Berita & Kabar dan Agenda
        $wakasekAndCoordRoles = [
            'wakasek_kurikulum', 'kurikulum',
            'wakasek_kesiswaan', 'kesiswaan',
            'wakasek_humas', 'humas',
            'koordinator_bk', 'bk_koordinator',
            'kepala_perpustakaan', 'perpus_kepala',
            'koordinator_keagamaan', 'keagamaan',
        ];
        if (! empty(array_intersect($wakasekAndCoordRoles, $rolesToCheck)) || $this->hasAnyRole($wakasekAndCoordRoles)) {
            return ['news', 'agendas'];
        }

        // 4. Akun Guru, TU, Wali Kelas, Pembina Ekstra, Guru BK: Berita & Kabar
        $staffAndTeacherRoles = [
            'guru', 'tu', 'staff', 'tenaga_administrasi', 'wali_kelas', 'pembina_ekstra', 'pembina_ekskul', 'guru_bk', 'bk',
            'kepala_administrasi', 'operator_dapodik', 'operator', 'tu_piket', 'guru_piket',
        ];
        if (! empty(array_intersect($staffAndTeacherRoles, $rolesToCheck)) || $this->hasAnyRole($staffAndTeacherRoles)) {
            return ['news'];
        }

        // 5. Akun Siswa dengan peran adm_web: khusus Berita & Kabar saja
        if (! empty(array_intersect(['adm_web'], $rolesToCheck)) || $this->hasAnyRole(['adm_web'])) {
            return ['news'];
        }

        return [];
    }

    public function canAccessWebsiteContentTab(string $tab): bool
    {
        return in_array($tab, $this->allowedWebsiteContentTabs(), true);
    }

    public function canAccessTuModule(): bool
    {
        return RolePermissionMatrix::canAccess($this, 'tu', $this->active_role);
    }

    /**
     * Hak akses granular ke 5 sub-modul Tata Usaha (Kesiswaan, Kepegawaian, Persuratan, Sarpras, Keuangan).
     */
    public function canAccessTuSubmodule(string $submodule): bool
    {
        return RolePermissionMatrix::canAccessTuSubmodule($this, $submodule, $this->active_role);
    }

    public function isKurikulumAdmin(): bool
    {
        $active = $this->active_role;
        if (in_array($active, ['wakasek_kurikulum', 'kurikulum', 'admin', 'super_admin', 'all_roles'], true)) {
            return true;
        }

        return $this->hasAnyRole(['wakasek_kurikulum', 'kurikulum', 'admin', 'super_admin'])
            || in_array($this->role, ['wakasek_kurikulum', 'kurikulum', 'admin', 'super_admin'], true);
    }

    public function isKesiswaanAdmin(): bool
    {
        $active = $this->active_role;
        if (in_array($active, ['wakasek_kesiswaan', 'kesiswaan', 'admin', 'super_admin'], true)) {
            return true;
        }

        if ($active === 'all_roles') {
            return $this->hasAnyRole(['wakasek_kesiswaan', 'kesiswaan', 'admin', 'super_admin'])
                || in_array($this->role, ['wakasek_kesiswaan', 'kesiswaan', 'admin', 'super_admin'], true);
        }

        return false;
    }

    public function isBkAdmin(): bool
    {
        $active = $this->active_role;
        $allowedBkRoles = ['koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah', 'admin', 'super_admin'];
        if (in_array($active, $allowedBkRoles, true)) {
            return true;
        }

        if ($active === 'all_roles') {
            return $this->hasAnyRole($allowedBkRoles);
        }

        return false;
    }

    /**
     * Memiliki hak akses penuh ke seluruh fitur Aplikasi Bimbingan Konseling (BK).
     * Sesuai ketentuan: Koordinator BK, Guru BK, Kepala Sekolah, Admin, dan Super Admin (Administrator).
     */
    public function hasFullBkAccess(): bool
    {
        $active = $this->active_role;
        $allowedBkRoles = ['koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah', 'admin', 'super_admin'];
        if (in_array($active, $allowedBkRoles, true)) {
            return true;
        }

        if ($active === 'all_roles') {
            return $this->hasAnyRole($allowedBkRoles);
        }

        if ($this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']) && in_array($active, ['super_admin', 'admin', 'kepala_sekolah'], true)) {
            return true;
        }

        return false;
    }

    public function canManageBk(): bool
    {
        return $this->hasFullBkAccess();
    }

    public function isLibraryAdmin(): bool
    {
        $active = $this->active_role;
        if (in_array($active, ['kepala_perpustakaan', 'admin', 'super_admin', 'all_roles'], true)) {
            return true;
        }

        return $this->hasAnyRole(['kepala_perpustakaan', 'admin', 'super_admin'])
            || in_array($this->role, ['kepala_perpustakaan', 'admin', 'super_admin'], true);
    }

    public function canManageExtracurricular(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin', 'pembina_ekstra', 'kesiswaan', 'wakasek_kesiswaan']);
    }

    public function isExtracurricularSecretary(): bool
    {
        return $this->hasAnyRole(['sekretaris_ekstra', 'sekretaris_kelas', 'admin', 'super_admin']);
    }

    public function isWakasek(): bool
    {
        return $this->hasAnyRole(['wakasek_kurikulum', 'wakasek_kesiswaan', 'wakasek_humas', 'wakasek_sarpras', 'admin', 'super_admin']);
    }

    public function isLibrarian(): bool
    {
        return $this->hasAnyRole(['kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan', 'admin', 'super_admin']);
    }

    public function isOperator(): bool
    {
        return $this->hasAnyRole(['operator_dapodik', 'operator', 'admin', 'super_admin']);
    }

    public function isWakasekKurikulum(): bool
    {
        return $this->hasAnyRole(['wakasek_kurikulum', 'kurikulum', 'admin', 'super_admin']);
    }

    public function isCurriculumAdmin(): bool
    {
        return $this->hasAnyRole(['wakasek_kurikulum', 'kurikulum', 'admin', 'super_admin']);
    }

    public function isWakasekKesiswaan(): bool
    {
        return $this->hasAnyRole(['wakasek_kesiswaan', 'kesiswaan', 'admin', 'super_admin']);
    }

    public function isWakasekSarpras(): bool
    {
        return $this->hasAnyRole(['wakasek_sarpras', 'ta_sarpras', 'admin', 'super_admin']);
    }

    public function isWakasekHumas(): bool
    {
        return $this->hasAnyRole(['wakasek_humas', 'admin', 'super_admin']);
    }

    public function isKepalaLaboratorium(): bool
    {
        return $this->hasAnyRole(['kepala_laboratorium', 'admin', 'super_admin']);
    }

    public function isKepalaPerpustakaan(): bool
    {
        return $this->hasAnyRole(['kepala_perpustakaan', 'admin', 'super_admin']);
    }

    public function isPengelolaPerpustakaan(): bool
    {
        return $this->hasAnyRole(['pengelola_perpustakaan', 'pustakawan', 'admin', 'super_admin']);
    }

    public function isKoordinatorBk(): bool
    {
        return $this->hasAnyRole(['koordinator_bk', 'admin', 'super_admin']);
    }

    public function isKoordinatorKeagamaan(): bool
    {
        return $this->hasAnyRole(['koordinator_keagamaan', 'admin', 'super_admin']);
    }

    public function isPembinaEkstra(): bool
    {
        return $this->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin']);
    }

    public function isGuruPiket(): bool
    {
        return $this->hasAnyRole(['guru_piket', 'petugas_piket', 'admin', 'super_admin']);
    }

    public function isTuPiket(): bool
    {
        return $this->hasAnyRole(['tu_piket', 'petugas_piket', 'admin', 'super_admin']);
    }

    public function isPiket(): bool
    {
        return $this->hasAnyRole(['guru_piket', 'tu_piket', 'petugas_piket', 'admin', 'super_admin']);
    }

    public function isKepalaAdministrasi(): bool
    {
        return $this->hasAnyRole(['kepala_administrasi', 'admin', 'super_admin']);
    }

    public function isBendahara(): bool
    {
        return $this->hasAnyRole(['bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'admin', 'super_admin']);
    }

    public function isBendaharaBosp(): bool
    {
        return $this->hasAnyRole(['bendahara_bosp', 'bendahara', 'admin', 'super_admin']);
    }

    public function isBendaharaBopd(): bool
    {
        return $this->hasAnyRole(['bendahara_bopd', 'bendahara', 'admin', 'super_admin']);
    }

    public function isBendaharaGaji(): bool
    {
        return $this->hasAnyRole(['bendahara_gaji', 'bendahara', 'admin', 'super_admin']);
    }

    public function isOperatorDapodik(): bool
    {
        return $this->hasAnyRole(['operator_dapodik', 'operator', 'admin', 'super_admin']);
    }

    public function isTaKesiswaan(): bool
    {
        return $this->hasAnyRole(['ta_kesiswaan', 'admin', 'super_admin']);
    }

    public function isTaKepegawaian(): bool
    {
        return $this->hasAnyRole(['ta_kepegawaian', 'admin', 'super_admin']);
    }

    public function isTaSarpras(): bool
    {
        return $this->hasAnyRole(['ta_sarpras', 'admin', 'super_admin']);
    }

    public function isTaPersuratan(): bool
    {
        return $this->hasAnyRole(['ta_persuratan', 'admin', 'super_admin']);
    }

    public function isStudentOrParent(): bool
    {
        return $this->hasAnyRole(['siswa', 'orang_tua']);
    }

    public function isHomeroomTeacher(): bool
    {
        return $this->hasRole('wali_kelas');
    }

    public function isCounselorTeacher(): bool
    {
        return $this->hasAnyRole(['bk', 'koordinator_bk']);
    }

    public function canEditLmsWeights(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'guru']);
    }

    /**
     * Get homeroom classes for this teacher/user.
     */
    public function getHomeroomClasses(): Collection
    {
        $teacher = $this->teacher ?? Teacher::where('user_id', $this->id)->first();
        if (! $teacher) {
            return SchoolClass::whereRaw('1 = 0')->get();
        }

        return SchoolClass::where('homeroom_teacher_id', $teacher->id)->get();
    }

    /**
     * Get primary homeroom class.
     */
    public function getHomeroomClass(): ?SchoolClass
    {
        return $this->getHomeroomClasses()->first();
    }

    public function libraryMember(): HasOne
    {
        return $this->hasOne(LibraryMember::class, 'user_id');
    }

    public function getOrCreateLibraryMember(): ?LibraryMember
    {
        $existing = LibraryMember::where('user_id', $this->id)->first();
        if ($existing) {
            return $existing;
        }

        $student = $this->student ?? Student::where('user_id', $this->id)->first();
        $teacher = $this->teacher ?? Teacher::where('user_id', $this->id)->first();

        $type = 'umum';
        $studentId = null;
        $teacherId = null;
        $idNumber = $this->username ?: (string) $this->id;

        if ($student) {
            $type = 'siswa';
            $studentId = $student->id;
            $idNumber = $student->nisn ?: ($student->nis ?: $idNumber);
        } elseif ($teacher) {
            $type = 'guru';
            $teacherId = $teacher->id;
            $idNumber = $teacher->nip ?: $idNumber;
        } elseif ($this->hasAnyRole(['pustakawan', 'pengelola_perpustakaan', 'kepala_perpustakaan', 'tu', 'staff'])) {
            $type = 'staf';
        }

        $code = LibraryMember::generateUniqueCode($type, $studentId ?: ($teacherId ?: $this->id));

        return LibraryMember::create([
            'member_code' => $code,
            'member_type' => $type,
            'user_id' => $this->id,
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'name' => $this->name,
            'identity_number' => $idNumber,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $student?->address ?: ($teacher?->address ?: 'SMA Negeri 1 Lengkong'),
            'joined_at' => now()->toDateString(),
            'is_active' => true,
        ]);
    }

    // Role checks
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin']);
    }

    public function isPrincipal(): bool
    {
        return $this->hasRole('kepala_sekolah');
    }

    public function isKepalaSekolah(): bool
    {
        return $this->hasRole('kepala_sekolah');
    }

    public function isTeacher(): bool
    {
        return $this->hasAnyRole(['guru', 'wali_kelas', 'kepala_sekolah', 'admin', 'super_admin', 'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras', 'pembina_ekstra', 'bk', 'koordinator_keagamaan']);
    }

    public function isHomeroom(): bool
    {
        return $this->hasRole('wali_kelas');
    }

    public function isCounselor(): bool
    {
        return $this->hasAnyRole(['bk', 'guru_bk', 'konselor', 'admin', 'super_admin']);
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->isAdmin() || $this->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        // Rule 6: Tenaga Pendidik tidak memiliki akses ke modul Adm Ketatausahaan
        if ($this->role === 'guru' && in_array($module, ['kepegawaian', 'persuratan', 'tamu', 'sarpras', 'keuangan', 'tu'], true)) {
            return false;
        }

        return match ($module) {
            'kesiswaan' => $this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'wakasek_kesiswaan', 'kesiswaan', 'ta_kesiswaan', 'wali_kelas', 'guru', 'tu', 'staff', 'kepala_administrasi', 'pembina_ekstra', 'guru_piket', 'tu_piket', 'petugas_piket', 'siswa', 'orang_tua']),
            'kepegawaian' => $this->canAccessTuModule() && $this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi', 'tu', 'ta_kepegawaian', 'staff', 'operator_dapodik']),
            'persuratan', 'tamu' => $this->canAccessTuModule() && $this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'kepala_administrasi', 'tu', 'ta_persuratan', 'staff', 'tu_piket']),
            'sarpras' => $this->canAccessTuModule() && $this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'wakasek_sarpras', 'kepala_administrasi', 'tu', 'ta_sarpras', 'staff', 'kepala_laboratorium']),
            'keuangan' => $this->canAccessTuModule() && $this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'bendahara', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'kepala_administrasi']),
            'bk' => $this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'wakasek_kesiswaan', 'bk', 'guru_bk', 'konselor', 'koordinator_bk', 'wali_kelas', 'kesiswaan']),
            'perpustakaan' => $this->canManageLibrary(),
            'kurikulum' => $this->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah', 'wakasek_kurikulum', 'kurikulum', 'guru', 'wali_kelas']),
            'piket' => $this->hasAnyRole(['super_admin', 'admin', 'guru_piket', 'tu_piket', 'petugas_piket', 'wali_kelas', 'kepala_sekolah', 'kepala_administrasi']),
            'tu' => $this->canAccessTuModule(),
            default => true,
        };
    }

    public function isStudentAffairs(): bool
    {
        return $this->hasAnyRole(['kesiswaan', 'wakasek_kesiswaan']);
    }

    public function isStudent(): bool
    {
        return $this->hasRole('siswa') && ! $this->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'kepala_sekolah', 'bk', 'kesiswaan', 'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras', 'tu', 'staff']);
    }

    public function isParent(): bool
    {
        return $this->hasRole('orang_tua');
    }

    // Relations
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_has_roles');
    }

    public function activeRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'active_role_id');
    }

    public function activeRoleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'active_role_id');
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentGuardian::class);
    }

    public function systemNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function lastLoginAudit(): HasOne
    {
        return $this->hasOne(AuditLog::class)->ofMany([
            'id' => 'max',
        ], function ($query) {
            $query->where('action', 'LOGIN');
        });
    }

    public function lastActivityAudit(): HasOne
    {
        return $this->hasOne(AuditLog::class)->latestOfMany();
    }

    public function getOfficialName(): string
    {
        if ($this->employee) {
            $name = $this->employee->name;
            if ($this->employee->title_prefix) {
                $name = $this->employee->title_prefix.' '.$name;
            }
            if ($this->employee->title_suffix) {
                $name = $name.', '.$this->employee->title_suffix;
            }

            return $name;
        }

        if ($this->teacher) {
            $name = $this->teacher->name;
            if ($this->teacher->title) {
                $name = $name.', '.$this->teacher->title;
            }

            return $name;
        }

        return $this->name ?? 'Petugas';
    }

    public function getOfficialNip(): ?string
    {
        return $this->employee?->nip ?? $this->teacher?->nip ?? $this->nip ?? '-';
    }

    public function getRoleTitleFor(string $role): string
    {
        return RolePermissionMatrix::getRoleTitle($role, $this);
    }

    public function getRoleDisplayName(): string
    {
        return $this->getRoleTitleFor($this->active_role);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->systemNotifications()->where('is_read', false)->count();
    }
}

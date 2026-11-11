<?php

namespace App\Console\Commands;

use App\Models\Extracurricular;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SyncUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-user-roles {--dry-run : Run simulation without writing changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and regenerate user roles and permissions based on authentic school duties';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('===========================================================');
        $this->info('  RESET & REGENERASI HAK AKSES PENGGUNA SIS SMA 1 LENGKONG ');
        $this->info('===========================================================');

        $isDryRun = (bool) $this->option('dry-run');
        if ($isDryRun) {
            $this->warn('[DRY-RUN MODE] Tidak ada perubahan data yang disimpan.');
        }

        // 0. Pastikan seluruh role sistem terdefinisi di tabel roles
        $systemRoles = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator Sistem',
            'kepala_sekolah' => 'Kepala Sekolah',
            'wakasek_kurikulum' => 'Wakasek Kurikulum',
            'wakasek_kesiswaan' => 'Wakasek Kesiswaan',
            'wakasek_humas' => 'Wakasek Humas',
            'wakasek_sarpras' => 'Wakasek Sarpras',
            'kepala_administrasi' => 'Kepala Tata Usaha (KTU)',
            'koordinator_bk' => 'Koordinator BP/BK',
            'bk' => 'Guru Bimbingan Konseling (BK)',
            'guru_bk' => 'Guru Bimbingan Konseling (BK)',
            'kepala_perpustakaan' => 'Kepala Perpustakaan',
            'pengelola_perpustakaan' => 'Pengelola Perpustakaan',
            'pustakawan' => 'Pengelola Perpustakaan',
            'wali_kelas' => 'Wali Kelas',
            'pembina_ekstra' => 'Pembina Ekstrakurikuler',
            'koordinator_keagamaan' => 'Koord. Keagamaan & Tahfidz',
            'guru_piket' => 'Guru Piket Sekolah',
            'tu_piket' => 'TU Piket Sekolah',
            'petugas_piket' => 'Petugas Piket',
            'bendahara' => 'Bendahara Sekolah',
            'bendahara_bosp' => 'Bendahara BOSP',
            'bendahara_bopd' => 'Bendahara BOPD',
            'bendahara_gaji' => 'Bendahara Gaji',
            'ta_kesiswaan' => 'Adm. Urusan Kesiswaan',
            'ta_kepegawaian' => 'Adm. Urusan Kepegawaian',
            'ta_persuratan' => 'Adm. Urusan Persuratan',
            'ta_sarpras' => 'Adm. Urusan Sarpras & BMD',
            'operator_dapodik' => 'Operator Dapodik',
            'operator' => 'Operator Data Sekolah',
            'kepala_laboratorium' => 'Kepala Laboratorium',
            'guru' => 'Guru Mata Pelajaran',
            'staff' => 'Tenaga Kependidikan (Staf TU)',
            'tu' => 'Staf Tata Usaha',
            'tenaga_administrasi' => 'Tenaga Administrasi',
            'siswa' => 'Peserta Didik (Siswa)',
            'sekretaris_kelas' => 'Sekretaris Kelas',
            'sekretaris_ekstra' => 'Sekretaris Ekstrakurikuler',
            'orang_tua' => 'Orang Tua / Wali Siswa',
            'adm_web' => 'Pengelola Konten Web Siswa',
        ];

        if (! $isDryRun) {
            foreach ($systemRoles as $name => $displayName) {
                Role::firstOrCreate(
                    ['name' => $name],
                    ['display_name' => $displayName, 'level' => 10]
                );
            }
        }

        $allRoles = Role::all()->keyBy('name');

        // 1. Sinkronisasi Akun Super Administrator
        $superAdmins = User::where('role', 'super_admin')
            ->orWhere('username', 'SENKS')
            ->orWhere('email', 'like', '%superadmin%')
            ->orWhere('email', 'seng@sma1le.sch.id')
            ->get();

        foreach ($superAdmins as $sa) {
            $this->assignRolesToUser($sa, 'super_admin', ['super_admin'], $allRoles, $isDryRun);
            $this->line("Super Admin: {$sa->name} ({$sa->username}) -> [super_admin]");
        }

        // 2. Pemetaan Penugasan Khusus Manajemen & Guru
        $specialTeacherAssignments = [
            // Kepala Sekolah
            811 => ['primary' => 'kepala_sekolah', 'roles' => ['kepala_sekolah', 'guru']], // Pian Sopian
            // Manajemen Sekolah (Wakasek)
            816 => ['primary' => 'guru', 'roles' => ['guru', 'wakasek_kurikulum']], // Anne Listiyani
            815 => ['primary' => 'guru', 'roles' => ['guru', 'wakasek_kesiswaan']], // Winda Dwi Atyani
            813 => ['primary' => 'guru', 'roles' => ['guru', 'wakasek_sarpras']], // Undang Mulyana
            823 => ['primary' => 'guru', 'roles' => ['guru', 'wakasek_humas', 'koordinator_keagamaan']], // Danti Sudianti
            // Bimbingan & Konseling (BP/BK)
            814 => ['primary' => 'guru', 'roles' => ['guru', 'koordinator_bk', 'bk']], // Ida Marttin Maolidah
            826 => ['primary' => 'guru', 'roles' => ['guru', 'bk']], // Melisa Irvianti
            829 => ['primary' => 'guru', 'roles' => ['guru', 'bk']], // Fajar Wahid Kurniawan
            // Perpustakaan
            822 => ['primary' => 'guru', 'roles' => ['guru', 'kepala_perpustakaan']], // Vamil Setiawati
            824 => ['primary' => 'guru', 'roles' => ['guru', 'pengelola_perpustakaan']], // Rani Ramayanti
        ];

        // 3. Ambil data Wali Kelas dari tabel school_classes
        $homeroomTeacherUserIds = [];
        $classes = SchoolClass::whereNotNull('homeroom_teacher_id')->with('homeroomTeacher')->get();
        foreach ($classes as $cls) {
            if ($cls->homeroomTeacher && $cls->homeroomTeacher->user_id) {
                $homeroomTeacherUserIds[$cls->homeroomTeacher->user_id] = $cls->name;
            }
        }

        // 4. Ambil data Pembina Ekstrakurikuler dari tabel extracurriculars
        $coachTeacherUserIds = [];
        if (DB::getSchemaBuilder()->hasTable('extracurriculars')) {
            $ekstras = Extracurricular::whereNotNull('teacher_id')->with('teacher')->get();
            foreach ($ekstras as $ek) {
                if ($ek->teacher && $ek->teacher->user_id) {
                    $coachTeacherUserIds[$ek->teacher->user_id][] = $ek->name;
                }
            }
        }

        // 5. Proses seluruh Guru (Pendidik)
        $teachers = Teacher::with('user')->get();
        $this->info("\nMemproses Data Guru & Tenaga Pendidik (".$teachers->count().' orang)...');

        foreach ($teachers as $teacher) {
            $user = $teacher->user;
            if (! $user) {
                continue;
            }

            // Jika termasuk dalam daftar staf TU yang tercatat di tabel teachers (legacy), lewati dan proses di section Staf
            if (in_array($user->id, [847, 848, 849, 850, 851, 852, 853], true)) {
                continue;
            }

            $primaryRole = 'guru';
            $userRoles = ['guru'];

            // Cek penugasan manajemen khusus
            if (isset($specialTeacherAssignments[$user->id])) {
                $primaryRole = $specialTeacherAssignments[$user->id]['primary'];
                $userRoles = array_merge($userRoles, $specialTeacherAssignments[$user->id]['roles']);
            }

            // Cek apakah Wali Kelas
            if (isset($homeroomTeacherUserIds[$user->id])) {
                $userRoles[] = 'wali_kelas';
            }

            // Cek apakah Pembina Ekstrakurikuler
            if (isset($coachTeacherUserIds[$user->id])) {
                $userRoles[] = 'pembina_ekstra';
            }

            $userRoles = array_values(array_unique($userRoles));
            $this->assignRolesToUser($user, $primaryRole, $userRoles, $allRoles, $isDryRun);

            $rolesLabel = implode(', ', $userRoles);
            $this->line("Guru: {$user->name} | Peran Utama: [{$primaryRole}] | Peran Tambahan: [{$rolesLabel}]");
        }

        // 6. Pemetaan Penugasan Staf Tata Usaha (Tenaga Kependidikan)
        $this->info("\nMemproses Data Staf Tata Usaha & Tenaga Kependidikan...");
        $staffAssignments = [
            // 847: Rachmat Sugiarto -> Kepala Administrasi (KTU), Operator Dapodik, Staf Kepegawaian
            847 => [
                'primary' => 'kepala_administrasi',
                'roles' => ['kepala_administrasi', 'operator_dapodik', 'ta_kepegawaian', 'staff'],
            ],
            // 848: Yeti Nurhayati -> Staf Adm Kesiswaan
            848 => [
                'primary' => 'ta_kesiswaan',
                'roles' => ['ta_kesiswaan', 'staff'],
            ],
            // 849: Rayi Nopian Rosadi -> Staf Sarpras & BMD
            849 => [
                'primary' => 'ta_sarpras',
                'roles' => ['ta_sarpras', 'staff'],
            ],
            // 850: Rian Hadiansyah -> Bendahara Sekolah & BOSP
            850 => [
                'primary' => 'bendahara',
                'roles' => ['bendahara', 'bendahara_bosp', 'staff'],
            ],
            // 851: Hasbi -> Staf Persuratan & Arsip
            851 => [
                'primary' => 'ta_persuratan',
                'roles' => ['ta_persuratan', 'staff'],
            ],
            // 852: Yadi -> Staf Pelaksana Umum TU
            852 => [
                'primary' => 'staff',
                'roles' => ['staff', 'tu'],
            ],
            // 853: Deti Kurnia -> Staf Pelaksana Umum TU
            853 => [
                'primary' => 'staff',
                'roles' => ['staff', 'tu'],
            ],
        ];

        foreach ($staffAssignments as $userId => $info) {
            $user = User::find($userId);
            if (! $user) {
                continue;
            }

            $this->assignRolesToUser($user, $info['primary'], $info['roles'], $allRoles, $isDryRun);
            $rolesLabel = implode(', ', $info['roles']);
            $this->line("Staf TU: {$user->name} | Peran Utama: [{$info['primary']}] | Sub-Role: [{$rolesLabel}]");
        }

        $this->info("\n[SELESAI] Seluruh hak akses pengguna berhasil disinkronkan & distandardisasi!");

        return Command::SUCCESS;
    }

    /**
     * Pasang peran utama dan peran tambahan ke User model dan pivot tabel user_has_roles.
     *
     * @param  array<string>  $roles
     * @param  Collection<string, Role>  $allRoles
     */
    private function assignRolesToUser(User $user, string $primaryRole, array $roles, $allRoles, bool $isDryRun): void
    {
        $uniqueRoles = array_values(array_unique(array_filter($roles)));
        if (! in_array($primaryRole, $uniqueRoles, true)) {
            array_unshift($uniqueRoles, $primaryRole);
        }

        if ($isDryRun) {
            return;
        }

        // 1. Simpan ke atribut User
        $user->role = $primaryRole;
        $user->roles = $uniqueRoles;

        // Atur active_role_id ke model role utama jika ada
        if (isset($allRoles[$primaryRole])) {
            $user->active_role_id = $allRoles[$primaryRole]->id;
        }

        $user->save();

        // 2. Sinkronkan ke tabel pivot user_has_roles
        $roleIdsToSync = [];
        foreach ($uniqueRoles as $roleName) {
            if (isset($allRoles[$roleName])) {
                $roleIdsToSync[] = $allRoles[$roleName]->id;
            }
        }

        if (! empty($roleIdsToSync)) {
            DB::table('user_has_roles')->where('user_id', $user->id)->delete();
            $records = array_map(fn ($rId) => [
                'user_id' => $user->id,
                'role_id' => $rId,
            ], $roleIdsToSync);
            DB::table('user_has_roles')->insert($records);
        }
    }
}

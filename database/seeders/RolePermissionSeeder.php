<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Administrator', 'description' => 'Akses penuh seluruh modul sistem', 'level' => 1],
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Pengelolaan data, master, dan operasional sekolah', 'level' => 2],
            ['name' => 'kepala_sekolah', 'display_name' => 'Kepala Sekolah', 'description' => 'Pengawasan eksekutif, rekapitulasi, dan monitoring', 'level' => 3],
            ['name' => 'wakasek_kurikulum', 'display_name' => 'Wakasek Kurikulum', 'description' => 'Akademik, pembelajaran, jadwal, dan kurikulum', 'level' => 4],
            ['name' => 'wakasek_kesiswaan', 'display_name' => 'Wakasek Kesiswaan', 'description' => 'Kesiswaan, tata tertib, presensi, dan ekstrakurikuler', 'level' => 4],
            ['name' => 'wakasek_humas', 'display_name' => 'Wakasek Humas', 'description' => 'Hubungan masyarakat, portal orang tua, dan kemitraan', 'level' => 4],
            ['name' => 'wakasek_sarpras', 'display_name' => 'Wakasek Sarpras', 'description' => 'Sarana prasarana, perangkat IoT, dan logistik sekolah', 'level' => 4],
            ['name' => 'kesiswaan', 'display_name' => 'Staf Kesiswaan', 'description' => 'Operasional pembinaan kesiswaan dan tata tertib', 'level' => 5],
            ['name' => 'bk', 'display_name' => 'Guru BK (Bimbingan Konseling)', 'description' => 'Bimbingan, konseling, dan pembinaan siswa', 'level' => 5],
            ['name' => 'wali_kelas', 'display_name' => 'Wali Kelas', 'description' => 'Pembina rombel, pemantauan presensi dan izin siswa binaan', 'level' => 5],
            ['name' => 'guru', 'display_name' => 'Guru Mata Pelajaran', 'description' => 'Pengajar mata pelajaran, jurnal KBM, dan presensi', 'level' => 6],
            ['name' => 'pembina_ekstra', 'display_name' => 'Pembina Ekstrakurikuler', 'description' => 'Pembimbing dan verifikator kegiatan ekstrakurikuler', 'level' => 6],
            ['name' => 'koordinator_keagamaan', 'display_name' => 'Koordinator Keagamaan & Tahfidz', 'description' => 'Pengelola kegiatan ibadah dan setoran hafalan Qur\'an', 'level' => 6],
            ['name' => 'kepala_administrasi', 'display_name' => 'Kepala Administrasi (KTU)', 'description' => 'Pimpinan tata usaha dan administrasi umum', 'level' => 5],
            ['name' => 'tu', 'display_name' => 'Staf Tata Usaha', 'description' => 'Layanan tata usaha, persuratan, dan kepegawaian', 'level' => 7],
            ['name' => 'staff', 'display_name' => 'Staf / Tenaga Kependidikan', 'description' => 'Tenaga administrasi dan kependidikan sekolah', 'level' => 7],
            ['name' => 'tenaga_administrasi', 'display_name' => 'Tenaga Administrasi', 'description' => 'Administrasi dan layanan operasional', 'level' => 7],
            ['name' => 'bendahara', 'display_name' => 'Bendahara Sekolah', 'description' => 'Pengelolaan keuangan dan administrasi kas', 'level' => 6],
            ['name' => 'pustakawan', 'display_name' => 'Pustakawan', 'description' => 'Pengelola sirkulasi dan katalog perpustakaan digital', 'level' => 7],
            ['name' => 'pengelola_perpustakaan', 'display_name' => 'Pengelola Perpustakaan', 'description' => 'Pengelola koleksi pustaka dan data pengunjung', 'level' => 7],
            ['name' => 'kepala_perpustakaan', 'display_name' => 'Kepala Perpustakaan', 'description' => 'Pimpinan unit perpustakaan sekolah', 'level' => 6],
            ['name' => 'ta_persuratan', 'display_name' => 'Staf Persuratan', 'description' => 'Administrasi surat masuk, surat keluar, dan disposisi', 'level' => 7],
            ['name' => 'ta_kepegawaian', 'display_name' => 'Staf Kepegawaian', 'description' => 'Administrasi data PTK dan kenaikan pangkat/gaji', 'level' => 7],
            ['name' => 'ta_kesiswaan', 'display_name' => 'Staf Administrasi Kesiswaan', 'description' => 'Buku induk dan administrasi peserta didik', 'level' => 7],
            ['name' => 'ta_sarpras', 'display_name' => 'Staf Sarpras', 'description' => 'Inventaris barang, ruang, dan sarana prasarana', 'level' => 7],
            ['name' => 'operator', 'display_name' => 'Operator Data Sekolah', 'description' => 'Pengelola sinkronisasi data dan sistem', 'level' => 6],
            ['name' => 'operator_dapodik', 'display_name' => 'Operator Dapodik', 'description' => 'Pengelolaan data pokok pendidikan (Dapodik)', 'level' => 6],
            ['name' => 'kepala_laboratorium', 'display_name' => 'Kepala Laboratorium', 'description' => 'Pengelola laboratorium sekolah', 'level' => 6],
            ['name' => 'koordinator_bk', 'display_name' => 'Koordinator BP/BK', 'description' => 'Koordinator bimbingan dan konseling', 'level' => 5],
            ['name' => 'guru_piket', 'display_name' => 'Petugas Piket (Guru)', 'description' => 'Petugas piket harian KBM dan izin siswa', 'level' => 6],
            ['name' => 'tu_piket', 'display_name' => 'Petugas Piket (TU)', 'description' => 'Petugas piket administrasi dan persuratan', 'level' => 7],
            ['name' => 'petugas_piket', 'display_name' => 'Petugas Piket', 'description' => 'Petugas piket ketertiban sekolah', 'level' => 6],
            ['name' => 'bendahara_bosp', 'display_name' => 'Bendahara BOSP', 'description' => 'Bendahara Bantuan Operasional Satuan Pendidikan', 'level' => 6],
            ['name' => 'bendahara_bopd', 'display_name' => 'Bendahara BOPD', 'description' => 'Bendahara Bantuan Operasional Pendidikan Daerah', 'level' => 6],
            ['name' => 'bendahara_gaji', 'display_name' => 'Bendahara Gaji', 'description' => 'Bendahara penggajian dan kesejahteraan pegawai', 'level' => 6],
            ['name' => 'sekretaris_kelas', 'display_name' => 'Sekretaris Kelas', 'description' => 'Perwakilan siswa pencatat presensi kelas harian', 'level' => 8],
            ['name' => 'sekretaris_ekstra', 'display_name' => 'Sekretaris Ekstrakurikuler', 'description' => 'Pencatat presensi kegiatan ekstrakurikuler', 'level' => 8],
            ['name' => 'adm_web', 'display_name' => 'Admin Web (Siswa)', 'description' => 'Akses kelola konten website sekolah terbatas pada Berita & Kabar', 'level' => 8],
            ['name' => 'siswa', 'display_name' => 'Peserta Didik (Siswa)', 'description' => 'Akses presensi selfie, kartu RFID, tugas, dan portofolio', 'level' => 9],
            ['name' => 'orang_tua', 'display_name' => 'Orang Tua / Wali Murid', 'description' => 'Pemantauan presensi anak, nilai, dan rekap 7 KAIH', 'level' => 9],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(
                ['name' => $r['name']],
                [
                    'display_name' => $r['display_name'],
                    'description' => $r['description'],
                    'level' => $r['level'],
                ]
            );
        }

        $allRolesMap = Role::all()->keyBy('name');

        // Sync all users to user_has_roles and active_role_id
        User::chunk(200, function ($users) use ($allRolesMap) {
            foreach ($users as $user) {
                $userRoles = $user->all_roles;
                $roleIds = [];
                foreach ($userRoles as $roleName) {
                    if (isset($allRolesMap[$roleName])) {
                        $roleIds[] = $allRolesMap[$roleName]->id;
                    }
                }

                $primaryName = $user->role ?: ($userRoles[0] ?? 'user');
                $primaryId = isset($allRolesMap[$primaryName]) ? $allRolesMap[$primaryName]->id : null;

                if (! empty($roleIds)) {
                    $user->roles()->syncWithoutDetaching($roleIds);
                }

                if ($primaryId && $user->active_role_id !== $primaryId) {
                    $user->updateQuietly(['active_role_id' => $primaryId]);
                }
            }
        });
    }
}

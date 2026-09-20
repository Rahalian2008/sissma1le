<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\Attendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\AttendanceSession;
use App\Models\GuidanceRecord;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\HabitSummary;
use App\Models\Notification;
use App\Models\ParentGuardian;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Models\ViolationItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. School Settings
        $settings = [
            ['key' => 'school_name', 'value' => 'SMA Negeri 1 Lengkong', 'group' => 'general', 'description' => 'Nama Resmi Sekolah'],
            ['key' => 'school_npsn', 'value' => '20539123', 'group' => 'general', 'description' => 'Nomor Pokok Sekolah Nasional (NPSN)'],
            ['key' => 'school_nss', 'value' => '301051408001', 'group' => 'general', 'description' => 'Nomor Statistik Sekolah (NSS)'],
            ['key' => 'school_accreditation', 'value' => 'A (Unggul)', 'group' => 'general', 'description' => 'Akreditasi Sekolah'],
            ['key' => 'headmaster_name', 'value' => 'Drs. H. Ahmad Sudrajat, M.Pd.', 'group' => 'general', 'description' => 'Nama Kepala Sekolah'],
            ['key' => 'headmaster_nip', 'value' => '196803151994031004', 'group' => 'general', 'description' => 'NIP Kepala Sekolah'],
            ['key' => 'school_address', 'value' => 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat', 'group' => 'general', 'description' => 'Alamat Sekolah'],
            ['key' => 'school_phone', 'value' => '(0266) 123456', 'group' => 'general', 'description' => 'Nomor Telepon Sekolah'],
            ['key' => 'school_email', 'value' => 'sman1lengkong@gmail.com', 'group' => 'general', 'description' => 'Email Resmi Sekolah'],
            ['key' => 'school_website', 'value' => 'https://sman1lengkong.sch.id', 'group' => 'general', 'description' => 'Situs Resmi Sekolah'],
            ['key' => 'school_branch', 'value' => 'CABANG DINAS PENDIDIKAN WILAYAH V', 'group' => 'general', 'description' => 'Cabang Dinas Pendidikan Wilayah'],
            ['key' => 'school_city', 'value' => 'Sukabumi', 'group' => 'general', 'description' => 'Kota/Kabupaten Administrasi'],
            // Pilihan Metode Presensi Aktif
            ['key' => 'enable_attendance_selfie', 'value' => '1', 'group' => 'attendance_methods', 'description' => 'Aktifkan Presensi Selfie Realtime + GPS Geofence'],
            ['key' => 'enable_attendance_qr', 'value' => '1', 'group' => 'attendance_methods', 'description' => 'Aktifkan Presensi QR Code Dinamis'],
            ['key' => 'enable_attendance_rfid', 'value' => '1', 'group' => 'attendance_methods', 'description' => 'Aktifkan Presensi Kartu RFID / Kiosk'],
            // Parameter Waktu & Geofencing
            ['key' => 'school_lat', 'value' => '-7.56845000', 'group' => 'attendance', 'description' => 'Garis Lintang Sekolah (Latitude)'],
            ['key' => 'school_lng', 'value' => '112.04612000', 'group' => 'attendance', 'description' => 'Garis Bujur Sekolah (Longitude)'],
            ['key' => 'geofence_radius_meters', 'value' => '100', 'group' => 'attendance', 'description' => 'Radius Maksimal Presensi Selfie (Meter)'],
            ['key' => 'attendance_start_time', 'value' => '06:00', 'group' => 'attendance', 'description' => 'Jam Mulai Buka Presensi'],
            ['key' => 'attendance_checkin_time', 'value' => '07:00', 'group' => 'attendance', 'description' => 'Batas Jam Masuk (Tepat Waktu)'],
            ['key' => 'attendance_late_cutoff_time', 'value' => '07:30', 'group' => 'attendance', 'description' => 'Batas Akhir Toleransi Terlambat'],
            ['key' => 'attendance_end_time', 'value' => '16:00', 'group' => 'attendance', 'description' => 'Jam Tutup Presensi'],
            ['key' => 'leave_request_cutoff_time', 'value' => '06:30', 'group' => 'attendance', 'description' => 'Batas Jam Pengajuan Izin/Sakit Hari H'],
            // Reward thresholds
            ['key' => 'reward_tier_1_min', 'value' => '125', 'group' => 'rewards', 'description' => 'Poin Minimal Sertifikat Siswa Berprestasi (125-175)'],
            ['key' => 'reward_tier_2_min', 'value' => '176', 'group' => 'rewards', 'description' => 'Poin Minimal Sertifikat dan Hadiah (176-199)'],
            ['key' => 'reward_tier_3_min', 'value' => '200', 'group' => 'rewards', 'description' => 'Poin Anugerah Waluya Utama (>=200)'],
            // Guidance thresholds
            ['key' => 'guidance_tier_1_min', 'value' => '75', 'group' => 'guidance', 'description' => 'Poin Bimbingan Wali Kelas (75-124)'],
            ['key' => 'guidance_tier_2_min', 'value' => '125', 'group' => 'guidance', 'description' => 'Poin SP 1 - BK (125-175)'],
            ['key' => 'guidance_tier_3_min', 'value' => '176', 'group' => 'guidance', 'description' => 'Poin SP 2 - BK (176-199)'],
            ['key' => 'guidance_tier_4_min', 'value' => '200', 'group' => 'guidance', 'description' => 'Poin SP 3 - BK & Kesiswaan (200)'],
            ['key' => 'guidance_tier_5_min', 'value' => '201', 'group' => 'guidance', 'description' => 'Poin Rapat Khusus Dewan Guru (>200)'],
        ];

        foreach ($settings as $s) {
            SchoolSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 2. Attendance Location
        $loc = AttendanceLocation::updateOrCreate(
            ['name' => 'SMA Negeri 1 Lengkong - Kampus Utama'],
            [
                'latitude' => -7.56845000,
                'longitude' => 112.04612000,
                'radius_meters' => 100,
                'is_active' => true,
                'description' => 'Area utama SMA Negeri 1 Lengkong (Gerbang, Lapangan, Gedung KBM)',
            ]
        );

        // 3. Attendance Devices
        AttendanceDevice::updateOrCreate(
            ['device_code' => 'RFID-GATE-01'],
            [
                'device_name' => 'Scanner RFID Gerbang Barat',
                'device_type' => 'RFID',
                'location_name' => 'Pintu Gerbang Utama SMAN 1 Lengkong',
                'ip_address' => '192.168.1.101',
                'status' => 'ONLINE',
                'last_ping_at' => now(),
                'secret_key' => 'sma1le_rfid_sec_key_2026',
            ]
        );

        AttendanceDevice::updateOrCreate(
            ['device_code' => 'KIOSK-LOBBY-01'],
            [
                'device_name' => 'Kiosk Mandiri Presensi Lobby',
                'device_type' => 'KIOSK',
                'location_name' => 'Lobby Gedung Tata Usaha',
                'ip_address' => '192.168.1.102',
                'status' => 'ONLINE',
                'last_ping_at' => now(),
                'secret_key' => 'sma1le_kiosk_sec_key_2026',
            ]
        );

        // 4. Academic Year
        $academicYear = AcademicYear::updateOrCreate(
            ['name' => '2026/2027', 'semester' => 'Ganjil'],
            [
                'is_active' => true,
                'start_date' => '2026-07-15',
                'end_date' => '2026-12-20',
            ]
        );

        // 5. User & Role Setup (NIP / NISN sebagai Username & Password Default)
        $defaultPassword = Hash::make('password');

        // Super Admin Utama (Rh Aseng / SENKS)
        $superAdmin = User::updateOrCreate(
            ['email' => 'seng@sma1le.sch.id'],
            [
                'name' => 'Rh Aseng',
                'username' => 'SENKS',
                'role' => 'super_admin',
                'phone' => '085887053005',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );

        // Super Admin Cadangan / Pengujian
        User::updateOrCreate(
            ['email' => 'superadmin@sma1le.sch.id'],
            [
                'name' => 'Super Administrator SMAN 1 Lengkong',
                'username' => 'superadmin',
                'role' => 'super_admin',
                'phone' => '081234567890',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );

        // Admin
        $nipAdmin = '198501012010011001';
        $admin = User::updateOrCreate(
            ['email' => 'admin@sma1le.sch.id'],
            [
                'name' => 'Admin Pengelola Sekolah',
                'username' => $nipAdmin,
                'role' => 'admin',
                'phone' => '081234567891',
                'password' => Hash::make($nipAdmin),
                'is_active' => true,
            ]
        );
        $teacherAdmin = Teacher::updateOrCreate(
            ['nip' => $nipAdmin],
            [
                'user_id' => $admin->id,
                'name' => 'Admin Pengelola Sekolah',
                'gender' => 'L',
                'phone' => '081234567891',
                'title' => 'Administrator Sekolah',
                'is_active' => true,
            ]
        );

        // Kepala Sekolah (Monitoring Murni)
        $nipKepsek = '196803151994031004';
        $userKepsek = User::updateOrCreate(
            ['email' => 'kepsek@sma1le.sch.id'],
            [
                'name' => 'Drs. H. Ahmad Sudrajat, M.Pd.',
                'username' => $nipKepsek,
                'role' => 'kepala_sekolah',
                'phone' => '081234567899',
                'password' => Hash::make($nipKepsek),
                'is_active' => true,
            ]
        );
        $teacherKepsek = Teacher::updateOrCreate(
            ['nip' => $nipKepsek],
            [
                'user_id' => $userKepsek->id,
                'name' => 'Drs. H. Ahmad Sudrajat, M.Pd.',
                'gender' => 'L',
                'phone' => '081234567899',
                'title' => 'Kepala SMA Negeri 1 Lengkong',
                'is_active' => true,
            ]
        );

        // Guru Mapel
        $nipGuru = '197804122005011003';
        $userGuru = User::updateOrCreate(
            ['email' => 'guru@sma1le.sch.id'],
            [
                'name' => 'Bambang Sugiantoro, S.Pd.',
                'username' => $nipGuru,
                'role' => 'guru',
                'phone' => '081234567892',
                'password' => Hash::make($nipGuru),
                'is_active' => true,
            ]
        );
        $teacherGuru = Teacher::updateOrCreate(
            ['nip' => $nipGuru],
            [
                'user_id' => $userGuru->id,
                'name' => 'Bambang Sugiantoro, S.Pd.',
                'gender' => 'L',
                'phone' => '081234567892',
                'title' => 'Guru Fisika & Pembina KIR',
                'is_active' => true,
            ]
        );

        // Wali Kelas
        $nipWali = '198205152008012004';
        $userWali = User::updateOrCreate(
            ['email' => 'walikelas@sma1le.sch.id'],
            [
                'name' => 'Siti Rahmawati, M.Pd.',
                'username' => $nipWali,
                'role' => 'wali_kelas',
                'phone' => '081234567893',
                'password' => Hash::make($nipWali),
                'is_active' => true,
            ]
        );
        $teacherWali = Teacher::updateOrCreate(
            ['nip' => $nipWali],
            [
                'user_id' => $userWali->id,
                'name' => 'Siti Rahmawati, M.Pd.',
                'gender' => 'P',
                'phone' => '081234567893',
                'title' => 'Wali Kelas XI-IPA-1 / Guru Matematika',
                'is_active' => true,
            ]
        );

        // Guru BK (Role disederhanakan ke Guru)
        $nipBk = '197503102002122002';
        $userBk = User::updateOrCreate(
            ['email' => 'bk@sma1le.sch.id'],
            [
                'name' => 'Dra. Nur Indah Pratiwi',
                'username' => $nipBk,
                'role' => 'guru',
                'phone' => '081234567894',
                'password' => Hash::make($nipBk),
                'is_active' => true,
            ]
        );
        $teacherBk = Teacher::updateOrCreate(
            ['nip' => $nipBk],
            [
                'user_id' => $userBk->id,
                'name' => 'Dra. Nur Indah Pratiwi',
                'gender' => 'P',
                'phone' => '081234567894',
                'title' => 'Guru BK (Bimbingan Konseling)',
                'is_active' => true,
            ]
        );

        // Kesiswaan (Role disederhanakan ke Guru)
        $nipKesiswaan = '197208181998021001';
        $userKesiswaan = User::updateOrCreate(
            ['email' => 'kesiswaan@sma1le.sch.id'],
            [
                'name' => 'Drs. Agus Hariyanto, M.M.',
                'username' => $nipKesiswaan,
                'role' => 'guru',
                'phone' => '081234567895',
                'password' => Hash::make($nipKesiswaan),
                'is_active' => true,
            ]
        );
        $teacherKesiswaan = Teacher::updateOrCreate(
            ['nip' => $nipKesiswaan],
            [
                'user_id' => $userKesiswaan->id,
                'name' => 'Drs. Agus Hariyanto, M.M.',
                'gender' => 'L',
                'phone' => '081234567895',
                'title' => 'Guru / Pembina Kesiswaan',
                'is_active' => true,
            ]
        );

        // Classes
        $classX1 = SchoolClass::updateOrCreate(
            ['name' => 'X-1'],
            [
                'grade' => 'X',
                'major' => 'Umum',
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teacherGuru->id,
            ]
        );

        $classXI1 = SchoolClass::updateOrCreate(
            ['name' => 'XI-IPA-1'],
            [
                'grade' => 'XI',
                'major' => 'IPA',
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teacherWali->id,
            ]
        );

        $classXII1 = SchoolClass::updateOrCreate(
            ['name' => 'XII-IPA-1'],
            [
                'grade' => 'XII',
                'major' => 'IPA',
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teacherKesiswaan->id,
            ]
        );

        // Siswa Utama (Ahmad Rifai - NISN sebagai Username & Password Default)
        $nisnSiswa1 = '0089234811';
        $userSiswa = User::updateOrCreate(
            ['email' => 'siswa@sma1le.sch.id'],
            [
                'name' => 'Ahmad Rifai',
                'username' => $nisnSiswa1,
                'role' => 'siswa',
                'phone' => '081234567896',
                'password' => Hash::make($nisnSiswa1),
                'is_active' => true,
            ]
        );
        $studentMain = Student::updateOrCreate(
            ['nis' => '202411001'],
            [
                'user_id' => $userSiswa->id,
                'nisn' => $nisnSiswa1,
                'name' => 'Ahmad Rifai',
                'gender' => 'L',
                'class_id' => $classXI1->id,
                'academic_year_id' => $academicYear->id,
                'rfid_uid' => 'A4B5C6D7',
                'birth_place' => 'Nganjuk',
                'birth_date' => '2008-05-14',
                'address' => 'Desa Lengkong RT 02 RW 01, Nganjuk',
                'phone' => '081234567896',
                'is_active' => true,
            ]
        );

        // Siswa Tambahan (Dewi Maharani - NISN sebagai Username & Password Default)
        $nisnSiswa2 = '0089234812';
        $userSiswa2 = User::updateOrCreate(
            ['email' => 'dewi@sma1le.sch.id'],
            [
                'name' => 'Dewi Maharani',
                'username' => $nisnSiswa2,
                'role' => 'siswa',
                'phone' => '081234567898',
                'password' => Hash::make($nisnSiswa2),
                'is_active' => true,
            ]
        );
        $student2 = Student::updateOrCreate(
            ['nis' => '202411002'],
            [
                'user_id' => $userSiswa2->id,
                'nisn' => $nisnSiswa2,
                'name' => 'Dewi Maharani',
                'gender' => 'P',
                'class_id' => $classXI1->id,
                'academic_year_id' => $academicYear->id,
                'rfid_uid' => 'B2C3D4E5',
                'birth_place' => 'Nganjuk',
                'birth_date' => '2008-08-20',
                'address' => 'Desa Bangle, Lengkong, Nganjuk',
                'phone' => '081234567898',
                'is_active' => true,
            ]
        );

        // Orang Tua Siswa Utama
        $userOrtu = User::updateOrCreate(
            ['email' => 'orangtua@sma1le.sch.id'],
            [
                'name' => 'Joko Susanto',
                'username' => 'jokosusanto',
                'role' => 'orang_tua',
                'phone' => '081234567897',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );
        ParentGuardian::updateOrCreate(
            ['student_id' => $studentMain->id, 'relation_type' => 'AYAH'],
            [
                'user_id' => $userOrtu->id,
                'name' => 'Joko Susanto',
                'phone' => '081234567897',
                'address' => 'Desa Lengkong RT 02 RW 01, Nganjuk',
                'occupation' => 'Wiraswasta',
            ]
        );

        // 6. Master Kategori Prestasi (R1 - R10) Berdasarkan Dokumen Tata Tertib SMAN 1 Lengkong
        $achievementCats = [
            ['code' => 'R1', 'name' => 'Pengembangan Keagamaan', 'desc' => 'Kegiatan keagamaan, hafalan Al-Quran/Kitab Suci, MTQ, dan aktivitas religi positif.'],
            ['code' => 'R2', 'name' => 'Kejujuran', 'desc' => 'Tindakan terpuji menjunjung nilai kejujuran, integritas, dan amanah.'],
            ['code' => 'R3', 'name' => 'Prestasi Akademis', 'desc' => 'Capaian kejuaraan sains, OSN, karya tulis, olimpiade mata pelajaran, dan peringkat sekolah.'],
            ['code' => 'R4', 'name' => 'Kedisiplinan', 'desc' => 'Ketertiban kehadiran, kepatuhan tata tertib, dan keteladanan harian.'],
            ['code' => 'R5', 'name' => 'Pengembangan Sosial', 'desc' => 'Aktivitas kepedulian sosial, bakti sosial, relawan kemanusiaan, dan donor darah.'],
            ['code' => 'R6', 'name' => 'Kepemimpinan', 'desc' => 'Peran kepengurusan OSIS, MPK, pradana Pramuka, dan pimpinan organisasi kesiswaan.'],
            ['code' => 'R7', 'name' => 'Kebangsaan', 'desc' => 'Pasukan Pengibar Bendera (Paskibra), lomba wawasan kebangsaan, dan bela negara.'],
            ['code' => 'R8', 'name' => 'Keaktifan dan Prestasi', 'desc' => 'Kejuaraan olahraga (O2SN), seni budaya (FLS2N), musik, tari, teater, dan ekstrakurikuler.'],
            ['code' => 'R9', 'name' => 'Peduli Lingkungan', 'desc' => 'Aktivitas Adiwiyata, konservasi lingkungan hidup sekolah, bank sampah, dan reboisasi.'],
            ['code' => 'R10', 'name' => 'Kewirausahaan', 'desc' => 'Partisipasi dan prestasi dalam pameran kewirausahaan siswa, inovasi produk, dan bisnis plan.'],
        ];

        $itemsSeed = [
            'R1' => [
                ['code' => 'R1.1', 'name' => 'Hafal Al-Quran minimal 1 Juz / Kitab Suci', 'points' => 30, 'level' => 'SEKOLAH'],
                ['code' => 'R1.2', 'name' => 'Juara I/II/III MTQ / Lomba Keagamaan Kabupaten', 'points' => 50, 'level' => 'KABUPATEN'],
                ['code' => 'R1.3', 'name' => 'Petugas Ibadah Rutin / Muadzin / Rohis Aktif', 'points' => 15, 'level' => 'SEKOLAH'],
            ],
            'R2' => [
                ['code' => 'R2.1', 'name' => 'Menemukan dan mengembalikan barang berharga / uang', 'points' => 25, 'level' => 'SEKOLAH'],
                ['code' => 'R2.2', 'name' => 'Mengakui kekeliruan/pelanggaran secara jujur dan berinisiatif memperbaiki', 'points' => 20, 'level' => 'SEKOLAH'],
            ],
            'R3' => [
                ['code' => 'R3.1', 'name' => 'Juara Olimpiade Sains / OSN Tingkat Kabupaten', 'points' => 75, 'level' => 'KABUPATEN'],
                ['code' => 'R3.2', 'name' => 'Juara Olimpiade Sains / OSN Tingkat Provinsi / Nasional', 'points' => 150, 'level' => 'NASIONAL'],
                ['code' => 'R3.3', 'name' => 'Peringkat 1 Paralel Nilai Rapor Semester', 'points' => 40, 'level' => 'SEKOLAH'],
                ['code' => 'R3.4', 'name' => 'Juara Lomba Karya Ilmiah Remaja (KIR)', 'points' => 60, 'level' => 'PROVINSI'],
            ],
            'R4' => [
                ['code' => 'R4.1', 'name' => 'Kehadiran 100% tanpa izin/sakit/alpa dalam satu semester', 'points' => 35, 'level' => 'SEKOLAH'],
                ['code' => 'R4.2', 'name' => 'Petugas Pemimpin Upacara Bendera Hari Besar Nasional', 'points' => 20, 'level' => 'SEKOLAH'],
            ],
            'R5' => [
                ['code' => 'R5.1', 'name' => 'Penggerak Utama Bakti Sosial Sekolah untuk Masyarakat Lengkong', 'points' => 30, 'level' => 'KECAMATAN'],
                ['code' => 'R5.2', 'name' => 'Relawan Bencana / PMR Tanggap Darurat', 'points' => 25, 'level' => 'KABUPATEN'],
            ],
            'R6' => [
                ['code' => 'R6.1', 'name' => 'Ketua / Pengurus Inti OSIS atau MPK Masa Bakti Aktif', 'points' => 50, 'level' => 'SEKOLAH'],
                ['code' => 'R6.2', 'name' => 'Pradana / Pradani Pramuka Pangkalan SMAN 1 Lengkong', 'points' => 40, 'level' => 'SEKOLAH'],
            ],
            'R7' => [
                ['code' => 'R7.1', 'name' => 'Anggota Paskibraka Tingkat Kabupaten Nganjuk', 'points' => 100, 'level' => 'KABUPATEN'],
                ['code' => 'R7.2', 'name' => 'Juara Lomba Cerdas Cermat 4 Pilar Kebangsaan', 'points' => 60, 'level' => 'PROVINSI'],
            ],
            'R8' => [
                ['code' => 'R8.1', 'name' => 'Juara O2SN Olahraga Tingkat Kabupaten', 'points' => 60, 'level' => 'KABUPATEN'],
                ['code' => 'R8.2', 'name' => 'Juara FLS2N Seni / Teater / Musik Tingkat Kabupaten / Provinsi', 'points' => 80, 'level' => 'PROVINSI'],
            ],
            'R9' => [
                ['code' => 'R9.1', 'name' => 'Kader Adiwiyata Aktif Pemelihara Taman & Biopori', 'points' => 25, 'level' => 'SEKOLAH'],
                ['code' => 'R9.2', 'name' => 'Inovator Pengelolaan Bank Sampah Mandiri Sekolah', 'points' => 35, 'level' => 'SEKOLAH'],
            ],
            'R10' => [
                ['code' => 'R10.1', 'name' => 'Juara Gelar Karya Kewirausahaan Siswa (Kreativitas Produk Lokal)', 'points' => 45, 'level' => 'SEKOLAH'],
            ],
        ];

        foreach ($achievementCats as $ac) {
            $cat = AchievementCategory::updateOrCreate(
                ['code' => $ac['code']],
                ['name' => $ac['name'], 'description' => $ac['desc'], 'is_active' => true]
            );

            if (isset($itemsSeed[$ac['code']])) {
                foreach ($itemsSeed[$ac['code']] as $item) {
                    AchievementItem::updateOrCreate(
                        ['category_id' => $cat->id, 'code' => $item['code']],
                        [
                            'name' => $item['name'],
                            'default_points' => $item['points'],
                            'level' => $item['level'],
                        ]
                    );
                }
            }
        }

        // 7. Master Kategori Pelanggaran (P1 - P6) Berdasarkan Dokumen Tata Tertib SMAN 1 Lengkong
        $violationCats = [
            ['code' => 'P1', 'name' => 'Kehadiran', 'desc' => 'Pelanggaran terkait kedatangan, presensi, absensi tanpa keterangan, dan bolos sekolah.'],
            ['code' => 'P2', 'name' => 'Sikap dan Perilaku', 'desc' => 'Pelanggaran etika sopan santun terhadap bapak/ibu guru, staf, maupun sesama siswa.'],
            ['code' => 'P3', 'name' => 'Seragam', 'desc' => 'Pelanggaran ketentuan penggunaan pakaian seragam sekolah, kelengkapan atribut, dan sepatu.'],
            ['code' => 'P4', 'name' => 'Kerapian dan Penampilan', 'desc' => 'Pelanggaran standar kerapian rambut putra, make-up berlebih putri, perhiasan, dan tato.'],
            ['code' => 'P5', 'name' => 'Kedisiplinan', 'desc' => 'Penyalahgunaan gawai/HP saat KBM, merokok, vape, bermain game, dan membawa barang terlarang.'],
            ['code' => 'P6', 'name' => 'Pelanggaran Berat', 'desc' => 'Perkelahian, narkoba, miras, asusila, pencurian, bullying, dan tindakan melawan hukum.'],
        ];

        $vItemsSeed = [
            'P1' => [
                ['code' => 'P1.1', 'name' => 'Terlambat hadir ke sekolah 1 s/d 15 menit', 'points' => 5, 'rec' => 'Peringatan lisan dan pembiasaan disiplin pagi.'],
                ['code' => 'P1.2', 'name' => 'Terlambat hadir ke sekolah lebih dari 15 menit', 'points' => 10, 'rec' => 'Pembinaan piket dan penugasan edukatif literasi.'],
                ['code' => 'P1.3', 'name' => 'Meninggalkan kelas/sekolah tanpa izin (Membolos)', 'points' => 20, 'rec' => 'Pemanggilan oleh Wali Kelas dan pemberitahuan orang tua.'],
                ['code' => 'P1.4', 'name' => 'Tidak masuk sekolah tanpa keterangan sah (Alpa)', 'points' => 15, 'rec' => 'Konfirmasi orang tua dan peringatan wali kelas.'],
            ],
            'P2' => [
                ['code' => 'P2.1', 'name' => 'Bersikap tidak sopan / membangkang arahan guru/karyawan', 'points' => 25, 'rec' => 'Bimbingan konseling dan surat pernyataan santun.'],
                ['code' => 'P2.2', 'name' => 'Mengeluarkan kata-kata kotor / umpatan di sekolah', 'points' => 15, 'rec' => 'Edukasi tutur kata santun dan refleksi karakter.'],
                ['code' => 'P2.3', 'name' => 'Mengolok-olok / mengejek kekurangan fisik teman', 'points' => 20, 'rec' => 'Konseling anti-perundungan dan mediasi kekeluargaan.'],
            ],
            'P3' => [
                ['code' => 'P3.1', 'name' => 'Mengenakan seragam tidak sesuai ketentuan hari', 'points' => 5, 'rec' => 'Peringatan dan pembetulan tata cara berpakaian.'],
                ['code' => 'P3.2', 'name' => 'Atribut seragam (badge OSIS, lokasi, dasi, ikat pinggang) tidak lengkap', 'points' => 5, 'rec' => 'Wajib melengkapi atribut saat upacara/KBM berikutnya.'],
                ['code' => 'P3.3', 'name' => 'Mengenakan sepatu warna warni / tidak sesuai ketentuan hitam', 'points' => 5, 'rec' => 'Teguran lisan dan penggantian sepatu bertali hitam.'],
                ['code' => 'P3.4', 'name' => 'Baju dikeluarkan tidak rapi / baju ketat tidak standar', 'points' => 5, 'rec' => 'Merapikan pakaian langsung di tempat.'],
            ],
            'P4' => [
                ['code' => 'P4.1', 'name' => 'Rambut gondrong / tidak rapi bagi siswa putra (melebihi kerah/telinga)', 'points' => 10, 'rec' => 'Batas waktu 2 hari untuk mencukur rapi model 3-2-1.'],
                ['code' => 'P4.2', 'name' => 'Mewarnai rambut dengan cat rambut mencolok (pirang/merah)', 'points' => 20, 'rec' => 'Wajib mengembalikan ke warna hitam alami dalam 3 hari.'],
                ['code' => 'P4.3', 'name' => 'Memakai perhiasan emas berlebihan / make-up tebal bagi siswi', 'points' => 10, 'rec' => 'Barang diamankan sementara dan dikembalikan kepada orang tua.'],
                ['code' => 'P4.4', 'name' => 'Tindik bagi siswa putra atau tato di anggota badan', 'points' => 50, 'rec' => 'Konseling khusus BK bersama orang tua.'],
            ],
            'P5' => [
                ['code' => 'P5.1', 'name' => 'Menggunakan gawai / HP saat pelajaran tanpa instruksi guru', 'points' => 10, 'rec' => 'HP dititipkan ke meja guru sampai jam pelajaran usai.'],
                ['code' => 'P5.2', 'name' => 'Merokok atau membawa rokok di lingkungan sekolah / masih berseragam', 'points' => 50, 'rec' => 'Pemanggilan orang tua ke sekolah dan pembinaan BK intensif.'],
                ['code' => 'P5.3', 'name' => 'Membawa rokok elektrik / vape / pod ke lingkungan sekolah', 'points' => 40, 'rec' => 'Penyitaan perangkat dan pembuatan surat komitmen disiplin.'],
                ['code' => 'P5.4', 'name' => 'Bermain game online / judi online di lingkungan sekolah', 'points' => 30, 'rec' => 'Pembinaan BK dan pendampingan digital sehat.'],
            ],
            'P6' => [
                ['code' => 'P6.1', 'name' => 'Terlibat perkelahian / tawuran antar siswa atau antar sekolah', 'points' => 100, 'rec' => 'SP 1 / SP 2 langsung, skorsing pembinaan bersama orang tua.'],
                ['code' => 'P6.2', 'name' => 'Membawa, mengonsumsi, atau mengedarkan minuman keras / narkoba', 'points' => 200, 'rec' => 'Rapat Pleno Dewan Guru untuk sanksi maksimal dikembalikan ke ortu.'],
                ['code' => 'P6.3', 'name' => 'Melakukan tindakan asusila / pelecehan seksual', 'points' => 150, 'rec' => 'Rapat khusus dewan guru dan penanganan hukum terpadu.'],
                ['code' => 'P6.4', 'name' => 'Melakukan pemerasan / intimidasi fisik (bullying berat)', 'points' => 100, 'rec' => 'SP 2 BK dan pendampingan psikologis korban & pelaku.'],
            ],
        ];

        foreach ($violationCats as $vc) {
            $cat = ViolationCategory::updateOrCreate(
                ['code' => $vc['code']],
                ['name' => $vc['name'], 'description' => $vc['desc'], 'is_active' => true]
            );

            if (isset($vItemsSeed[$vc['code']])) {
                foreach ($vItemsSeed[$vc['code']] as $vItem) {
                    ViolationItem::updateOrCreate(
                        ['category_id' => $cat->id, 'code' => $vItem['code']],
                        [
                            'name' => $vItem['name'],
                            'default_points' => $vItem['points'],
                            'guidance_recommendation' => $vItem['rec'],
                        ]
                    );
                }
            }
        }

        // 8. Master 7 Kebiasaan Anak Indonesia Hebat
        $habitsData = [
            [
                'order_number' => 1,
                'name' => 'Bangun Pagi',
                'tagline' => 'Awali hari dengan kesegaran dan kesiapan diri',
                'description' => 'Bangun sebelum subuh/fajar, merapikan tempat tidur, dan menghirup udara segar untuk menyongsong hari.',
                'target_time' => '04:30 - 05:30',
                'icon' => 'sun',
            ],
            [
                'order_number' => 2,
                'name' => 'Beribadah',
                'tagline' => 'Tingkatkan keimanan dan ketakwaan',
                'description' => 'Menjalankan sholat/doa wajib dan amalan ibadah sesuai dengan agama serta keyakinan masing-masing.',
                'target_time' => '05:00 - 06:00',
                'icon' => 'sparkles',
            ],
            [
                'order_number' => 3,
                'name' => 'Berolahraga',
                'tagline' => 'Tubuh bugar, pikiran cemerlang',
                'description' => 'Melakukan peregangan, senam, jalan pagi, lari ringan, atau aktivitas fisik minimal 15-30 menit.',
                'target_time' => '05:30 - 06:15',
                'icon' => 'heart-pulse',
            ],
            [
                'order_number' => 4,
                'name' => 'Makan Sehat dan Bergizi',
                'tagline' => 'Nutrisi seimbang untuk tumbuh kembang optimal',
                'description' => 'Sarapan pagi dengan menu bergizi seimbang (4 sehat 5 sempurna), minum air putih cukup, dan hindari jajanan berbahaya.',
                'target_time' => '06:30 - 07:00',
                'icon' => 'apple',
            ],
            [
                'order_number' => 5,
                'name' => 'Gemar Belajar',
                'tagline' => 'Rasa ingin tahu dan literasi tiada henti',
                'description' => 'Membaca buku non-pelajaran, mengulang materi KBM, mengerjakan tugas tepat waktu, atau mempelajari keterampilan baru.',
                'target_time' => '19:00 - 20:30',
                'icon' => 'book-open',
            ],
            [
                'order_number' => 6,
                'name' => 'Bermasyarakat',
                'tagline' => 'Peduli sesama, santun dalam kebersamaan',
                'description' => 'Membantu pekerjaan orang tua di rumah, bertegur sapa santun dengan tetangga, atau aktif dalam kegiatan sosial lingkungan.',
                'target_time' => '16:00 - 17:30',
                'icon' => 'users',
            ],
            [
                'order_number' => 7,
                'name' => 'Tidur Cepat',
                'tagline' => 'Istirahat berkualitas untuk regenerasi tubuh',
                'description' => 'Tidur malam maksimal pukul 21.30 atau 22.00, mematikan layar gawai 30 menit sebelum tidur demi istirahat berkualitas.',
                'target_time' => '21:30 - 22:00',
                'icon' => 'moon',
            ],
        ];

        foreach ($habitsData as $hd) {
            Habit::updateOrCreate(['order_number' => $hd['order_number']], $hd);
        }

        // 9. Sample Attendance History for Ahmad Rifai (Last 5 Days)
        $today = Carbon::today();
        for ($i = 4; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            $status = ($i === 1) ? 'TERLAMBAT' : 'HADIR';
            $checkTime = ($i === 1) ? '07:18:00' : '06:45:00';
            $method = ($i === 2) ? 'RFID' : (($i === 3) ? 'QR_CODE' : 'SELFIE');

            Attendance::updateOrCreate(
                ['student_id' => $studentMain->id, 'date' => $date->toDateString()],
                [
                    'nis' => $studentMain->nis,
                    'nisn' => $studentMain->nisn,
                    'student_name' => $studentMain->name,
                    'class_id' => $studentMain->class_id,
                    'time' => $checkTime,
                    'method' => $method,
                    'selfie_path' => ($method === 'SELFIE') ? 'attendance/sample_selfie.jpg' : null,
                    'latitude' => -7.568452,
                    'longitude' => 112.046124,
                    'gps_accuracy' => 8.5,
                    'device_info' => 'Mozilla/5.0 (Linux; Android 14; Mobile)',
                    'ip_address' => '192.168.1.150',
                    'status' => $status,
                    'verification_status' => 'VALID',
                    'notes' => ($status === 'TERLAMBAT') ? 'Terlambat 18 menit karena rantai sepeda kendur di jalan' : 'Presensi berhasil dan tepat waktu',
                ]
            );
        }

        // 10. Sample Prestasi for Ahmad Rifai (Total = 135 poin -> Eligible for Sertifikat Siswa Berprestasi)
        $catR3 = AchievementCategory::where('code', 'R3')->first();
        $itemR3 = AchievementItem::where('code', 'R3.1')->first();
        Achievement::updateOrCreate(
            ['student_id' => $studentMain->id, 'achievement_code' => 'PRESTASI-2026-001'],
            [
                'class_id' => $studentMain->class_id,
                'category_id' => $catR3->id,
                'item_id' => $itemR3?->id,
                'title' => 'Juara 1 Olimpiade Sains Nasional (OSN) Bidang Fisika Kabupaten Nganjuk',
                'level' => 'KABUPATEN',
                'points' => 75,
                'date' => '2026-08-14',
                'description' => 'Meraih medali emas seleksi OSN-K Fisika dan mewakili Kabupaten Nganjuk ke tingkat Provinsi Jawa Timur.',
                'certificate_path' => 'achievements/sertifikat_osn_2026.pdf',
                'reporter_id' => $userGuru->id,
                'status' => 'DIVERIFIKASI',
                'verified_by' => $userWali->id,
                'verified_at' => '2026-08-16 10:00:00',
            ]
        );

        $catR8 = AchievementCategory::where('code', 'R8')->first();
        $itemR8 = AchievementItem::where('code', 'R8.1')->first();
        Achievement::updateOrCreate(
            ['student_id' => $studentMain->id, 'achievement_code' => 'PRESTASI-2026-002'],
            [
                'class_id' => $studentMain->class_id,
                'category_id' => $catR8->id,
                'item_id' => $itemR8?->id,
                'title' => 'Juara 2 O2SN Cabang Bulutangkis Tunggal Putra Kabupaten Nganjuk',
                'level' => 'KABUPATEN',
                'points' => 60,
                'date' => '2026-08-28',
                'description' => 'Memperoleh medali perak cabang olahraga bulutangkis perorangan putra SMA.',
                'certificate_path' => 'achievements/sertifikat_o2sn_2026.pdf',
                'reporter_id' => $userGuru->id,
                'status' => 'DIVERIFIKASI',
                'verified_by' => $userKesiswaan->id,
                'verified_at' => '2026-08-30 09:15:00',
            ]
        );

        // 11. Sample Pelanggaran for Ahmad Rifai (Total = 15 Poin -> Status Disiplin Baik / Normal)
        $catP1 = ViolationCategory::where('code', 'P1')->first();
        $itemP1 = ViolationItem::where('code', 'P1.1')->first();
        Violation::updateOrCreate(
            ['student_id' => $studentMain->id, 'violation_code' => 'PELANGGARAN-2026-001'],
            [
                'class_id' => $studentMain->class_id,
                'category_id' => $catP1->id,
                'item_id' => $itemP1?->id,
                'title' => 'Terlambat Hadir Pagi Hari (P1.1)',
                'points' => 5,
                'date' => $today->copy()->subDay()->toDateString(),
                'time' => '07:18:00',
                'location' => 'Gerbang Masuk SMAN 1 Lengkong',
                'chronology' => 'Siswa tiba di gerbang pukul 07:18 WIB saat bel apel pagi telah selesai.',
                'reporter_id' => $userGuru->id,
                'evidence_path' => null,
                'guidance_notes' => 'Diingatkan agar berangkat 15 menit lebih awal.',
                'status' => 'DIVERIFIKASI',
                'verified_by' => $userWali->id,
                'verified_at' => now(),
            ]
        );

        $catP3 = ViolationCategory::where('code', 'P3')->first();
        $itemP3 = ViolationItem::where('code', 'P3.2')->first();
        Violation::updateOrCreate(
            ['student_id' => $studentMain->id, 'violation_code' => 'PELANGGARAN-2026-002'],
            [
                'class_id' => $studentMain->class_id,
                'category_id' => $catP3->id,
                'item_id' => $itemP3?->id,
                'title' => 'Atribut Dasi Sekolah Tertinggal Saat Upacara (P3.2)',
                'points' => 5,
                'date' => $today->copy()->subDays(7)->toDateString(),
                'time' => '06:50:00',
                'location' => 'Lapangan Upacara',
                'chronology' => 'Tidak mengenakan dasi abu-abu saat upacara hari Senin.',
                'reporter_id' => $userWali->id,
                'status' => 'DIVERIFIKASI',
                'verified_by' => $userWali->id,
                'verified_at' => now(),
            ]
        );

        // Sample student with higher violation to demo BK alert: Budi Santoso (80 poin -> Bimbingan Wali Kelas)
        $userBudi = User::updateOrCreate(
            ['email' => 'budi@sma1le.sch.id'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budisantoso',
                'role' => 'siswa',
                'phone' => '081234567899',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );
        $studentBudi = Student::updateOrCreate(
            ['nis' => '202411003'],
            [
                'user_id' => $userBudi->id,
                'nisn' => '0089234813',
                'name' => 'Budi Santoso',
                'gender' => 'L',
                'class_id' => $classXI1->id,
                'academic_year_id' => $academicYear->id,
                'rfid_uid' => 'C3D4E5F6',
                'birth_place' => 'Nganjuk',
                'birth_date' => '2008-01-11',
                'address' => 'Lengkong, Nganjuk',
                'phone' => '081234567899',
                'is_active' => true,
            ]
        );
        $catP5 = ViolationCategory::where('code', 'P5')->first();
        $itemP5 = ViolationItem::where('code', 'P5.2')->first();
        Violation::updateOrCreate(
            ['student_id' => $studentBudi->id, 'violation_code' => 'PELANGGARAN-2026-080'],
            [
                'class_id' => $studentBudi->class_id,
                'category_id' => $catP5->id,
                'item_id' => $itemP5?->id,
                'title' => 'Membawa dan Menghisap Rokok di Warung Dekat Sekolah (P5.2)',
                'points' => 50,
                'date' => $today->copy()->subDays(3)->toDateString(),
                'time' => '13:30:00',
                'location' => 'Warung Belakang Sekolah',
                'chronology' => 'Tertangkap patroli guru piket sedang berseragam sekolah.',
                'reporter_id' => $userGuru->id,
                'status' => 'DIVERIFIKASI',
                'verified_by' => $userKesiswaan->id,
                'verified_at' => now(),
            ]
        );
        $itemP1Bolos = ViolationItem::where('code', 'P1.3')->first();
        Violation::updateOrCreate(
            ['student_id' => $studentBudi->id, 'violation_code' => 'PELANGGARAN-2026-081'],
            [
                'class_id' => $studentBudi->class_id,
                'category_id' => $catP1->id,
                'item_id' => $itemP1Bolos?->id,
                'title' => 'Meninggalkan KBM Tanpa Izin / Membolos (P1.3)',
                'points' => 30,
                'date' => $today->copy()->subDays(2)->toDateString(),
                'time' => '11:00:00',
                'location' => 'Pagar Samping Sekolah',
                'chronology' => 'Melompati pagar saat jam istirahat kedua dan tidak kembali ke kelas.',
                'reporter_id' => $userWali->id,
                'status' => 'DIVERIFIKASI',
                'verified_by' => $userWali->id,
                'verified_at' => now(),
            ]
        );
        // Guidance record for Budi
        $guidanceData = [
            'counselor_id' => $userBk->id,
            'follow_up_type' => 'BIMBINGAN_WALI_KELAS',
            'recommendation' => 'Panggilan konseling wali kelas & bimbingan personal mengenai kedisiplinan dan pergaulan.',
            'notes' => 'Siswa berjanji tidak mengulangi dan bersedia membuat komitmen tertulis bersama orang tua.',
            'status' => 'DALAM_PROSES',
        ];
        $existingGuidance = GuidanceRecord::where('student_id', $studentBudi->id)->whereDate('date', $today)->first();
        if ($existingGuidance) {
            $existingGuidance->update($guidanceData);
        } else {
            GuidanceRecord::create(array_merge(['student_id' => $studentBudi->id, 'date' => $today->toDateString()], $guidanceData));
        }

        // 12. Sample 7 Habits Logs for Ahmad Rifai (Today)
        $habits = Habit::all();
        foreach ($habits as $habit) {
            $isDone = ($habit->order_number <= 5); // 5 out of 7 completed today so far
            $habitData = [
                'is_completed' => $isDone,
                'check_time' => $isDone ? '06:15:00' : null,
                'activity_name' => match ($habit->order_number) {
                    1 => 'Bangun pukul 04:30 WIB dan merapikan kamar',
                    2 => 'Sholat Subuh berjamaah di masjid terdekat',
                    3 => 'Jogging pagi 20 menit keliling desa',
                    4 => 'Sarapan nasi pecel sayur dan telur rebus',
                    5 => 'Membaca modul fisika persiapan KBM',
                    default => null,
                },
                'notes' => $isDone ? 'Terlaksana dengan lancar' : 'Akan dilaksanakan sore/malam nanti',
                'reflection' => $isDone ? 'Badan terasa lebih segar dan fokus belajar meningkat.' : null,
            ];

            $existingHabitLog = HabitDailyLog::where('student_id', $studentMain->id)
                ->where('habit_id', $habit->id)
                ->whereDate('date', $today)
                ->first();

            if ($existingHabitLog) {
                $existingHabitLog->update($habitData);
            } else {
                HabitDailyLog::create(array_merge([
                    'student_id' => $studentMain->id,
                    'habit_id' => $habit->id,
                    'date' => $today->toDateString(),
                ], $habitData));
            }
        }

        // Summary for Ahmad Rifai
        HabitSummary::updateOrCreate(
            ['student_id' => $studentMain->id, 'period_type' => 'WEEKLY', 'period_key' => $today->format('Y').'-W'.$today->weekOfYear],
            [
                'completed_count' => 32,
                'total_habits' => 35,
                'score_percentage' => 91.4,
                'status' => 'KONSISTEN',
            ]
        );

        // 13. Sample Notifications
        Notification::updateOrCreate(
            ['user_id' => $userSiswa->id, 'title' => 'Prestasi OSN Terverifikasi!'],
            [
                'message' => 'Selamat! Prestasi Juara 1 OSN Fisika Kabupaten telah diverifikasi oleh Wali Kelas (+75 Poin).',
                'type' => 'ACHIEVEMENT',
                'link_url' => '/achievements',
                'is_read' => false,
            ]
        );

        Notification::updateOrCreate(
            ['user_id' => $userSiswa->id, 'title' => 'Pengingat Jurnal 7 Kebiasaan'],
            [
                'message' => 'Jangan lupa mengisi jurnal 7 Kebiasaan Anak Indonesia Hebat untuk aktivitas malam hari ini.',
                'type' => 'HABIT',
                'link_url' => '/habits',
                'is_read' => false,
            ]
        );

        Notification::updateOrCreate(
            ['user_id' => $userOrtu->id, 'title' => 'Anak Anda Telah Hadir di Sekolah'],
            [
                'message' => 'Ahmad Rifai telah melakukan presensi di SMA Negeri 1 Lengkong pada pukul 06:45 WIB.',
                'type' => 'ATTENDANCE',
                'link_url' => '/parent/dashboard',
                'is_read' => false,
            ]
        );

        Notification::updateOrCreate(
            ['user_id' => $userBk->id, 'title' => 'Pemberitahuan Ambang Pembinaan Siswa'],
            [
                'message' => 'Siswa Budi Santoso (XI-IPA-1) telah mencapai total 80 poin pelanggaran. Diperlukan tindakan: Bimbingan Wali Kelas & BK.',
                'type' => 'GUIDANCE',
                'link_url' => '/counselor/dashboard',
                'is_read' => false,
            ]
        );

        // 14. Active QR Attendance Session by Guru
        AttendanceSession::updateOrCreate(
            ['class_id' => $classXI1->id, 'subject_or_activity' => 'Fisika Wajib (Hukum Newton)'],
            [
                'teacher_id' => $teacherGuru->id,
                'date' => $today->toDateString(),
                'start_time' => '07:30:00',
                'end_time' => '09:00:00',
                'qr_code_token' => 'SMA1LE_QR_'.Str::random(24),
                'qr_expires_at' => now()->addMinutes(45),
                'status' => 'ACTIVE',
            ]
        );
    }
}

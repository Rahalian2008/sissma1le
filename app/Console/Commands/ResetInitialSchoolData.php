<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ExtracurricularAttendance;
use App\Models\GuidanceRecord;
use App\Models\HabitDailyLog;
use App\Models\HabitSummary;
use App\Models\Notification;
use App\Models\ParentGuardian;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentAiRaport;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\WorshipAttendance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetInitialSchoolData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:reset-initial-data {--force : Paksa penghapusan data tanpa konfirmasi interaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus seluruh data siswa, guru, kepala sekolah, kelas, dan mereset identitas sekolah awal ke kondisi bersih (fresh setup)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('================================================================');
        $this->warn(' PERINGATAN PENGHAPUSAN MASTER DATA SEKOLAH (RESET AWAL)');
        $this->warn('================================================================');
        $this->line('Perintah ini akan menghapus:');
        $this->line('- Seluruh Data Siswa & Akun Pengguna Siswa');
        $this->line('- Seluruh Riwayat Presensi Siswa (Utama, Ekstra, Dhuha, Dzuhur, Keputrian)');
        $this->line('- Seluruh Catatan Pelanggaran, Prestasi, Tahfidz, AI Raport & 7 Karakter');
        $this->line('- Seluruh Data Kelas');
        $this->line('- Seluruh Data Guru & Akun Guru / Wali Kelas / Pembina');
        $this->line('- Seluruh Akun Kepala Sekolah');
        $this->line('- Mereset Identitas Sekolah (Nama Sekolah, NPSN, NSS, Kepsek, Alamat, Kontak, Logo, Kop)');
        $this->info('AKUN SUPER ADMIN AKAN TETAP AMAN & DIPERTAHANKAN agar Anda tetap bisa login.');
        $this->line('----------------------------------------------------------------');

        if (! $this->option('force')) {
            if (! $this->confirm('Apakah Anda yakin ingin melanjutkan penghapusan permanen ini?', false)) {
                $this->info('Penghapusan dibatalkan.');

                return self::SUCCESS;
            }
        }

        $this->info('Memulai proses pembersihan data...');

        DB::beginTransaction();

        try {
            // 1. Matikan pengecekan Foreign Key untuk pembersihan aman
            Schema::disableForeignKeyConstraints();

            // 2. Hapus Data Terkait Siswa
            $deletedAiRaport = StudentAiRaport::count();
            StudentAiRaport::truncate();

            $deletedAttMain = Attendance::count();
            Attendance::truncate();

            $deletedAttWorship = WorshipAttendance::count();
            WorshipAttendance::truncate();

            $deletedAttExtra = ExtracurricularAttendance::count();
            ExtracurricularAttendance::truncate();

            $deletedTahfidz = QuranTahfidzRecord::count();
            QuranTahfidzRecord::truncate();

            $deletedHabitLogs = HabitDailyLog::count();
            HabitDailyLog::truncate();

            $deletedHabitSum = HabitSummary::count();
            HabitSummary::truncate();

            $deletedViolations = Violation::count();
            Violation::truncate();

            $deletedAchievements = Achievement::count();
            Achievement::truncate();

            $deletedGuidance = GuidanceRecord::count();
            GuidanceRecord::truncate();

            $deletedParents = ParentGuardian::count();
            ParentGuardian::truncate();

            $deletedSessions = AttendanceSession::count();
            AttendanceSession::truncate();

            $deletedStudents = Student::count();
            Student::truncate();

            // 3. Hapus Data Kelas
            $deletedClasses = SchoolClass::count();
            SchoolClass::truncate();

            // 4. Hapus Data Guru
            $deletedTeachers = Teacher::count();
            Teacher::truncate();

            // 5. Hapus Akun Pengguna (Kecuali Super Admin)
            $deletedUsers = User::where('role', '!=', 'super_admin')->count();
            User::where('role', '!=', 'super_admin')->delete();

            // Hapus juga notifikasi lama
            Notification::truncate();

            // 6. Reset Identitas Sekolah ke nilai netral/bersih siap input
            $defaultSettings = [
                'school_name' => 'Nama Satuan Pendidikan',
                'school_npsn' => '-',
                'school_nss' => '-',
                'school_accreditation' => 'A',
                'school_education_form' => 'Sekolah Menengah Atas (SMA)',
                'headmaster_name' => 'Nama Kepala Sekolah, M.Pd.',
                'headmaster_nip' => '-',
                'school_address' => 'Alamat Lengkap Satuan Pendidikan',
                'school_phone' => '-',
                'school_email' => 'sekolah@sch.id',
                'school_website' => 'https://sekolah.sch.id',
                'school_branch' => 'CABANG DINAS PENDIDIKAN',
                'school_city' => 'Kota/Kabupaten',
                'school_province' => 'JAWA BARAT',
                'school_logo' => null,
                'school_kop' => null,
                'kop_surat_image' => null,
                'kop_image' => null,
            ];

            foreach ($defaultSettings as $k => $v) {
                SchoolSetting::updateOrCreate(['key' => $k], ['value' => $v, 'group' => 'general']);
            }

            // Aktifkan kembali Foreign Key
            Schema::enableForeignKeyConstraints();

            DB::commit();

            $this->info('================================================================');
            $this->info(' PEMBERSIHAN DATA BERHASIL DISELESAIKAN!');
            $this->info('================================================================');
            $this->line("- Siswa terhapus: {$deletedStudents}");
            $this->line("- Guru terhapus: {$deletedTeachers}");
            $this->line("- Kelas terhapus: {$deletedClasses}");
            $this->line("- Pengguna terhapus (Siswa/Guru/Kepsek/Ortu): {$deletedUsers}");
            $this->line("- Presensi terhapus: {$deletedAttMain}");
            $this->line("- Pelanggaran terhapus: {$deletedViolations}");
            $this->line("- Prestasi terhapus: {$deletedAchievements}");
            $this->line('- Identitas Sekolah: Berhasil direset ke template bersih siap pakai.');
            $this->info('Akun Super Admin tetap aktif untuk login dan konfigurasi.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            Schema::enableForeignKeyConstraints();
            $this->error('Gagal membersihkan data: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}

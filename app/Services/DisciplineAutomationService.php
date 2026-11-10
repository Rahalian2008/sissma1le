<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\AchievementItem;
use App\Models\Attendance;
use App\Models\QuranTahfidzRecord;
use App\Models\Student;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationItem;
use App\Models\WorshipAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DisciplineAutomationService
{
    /**
     * Dapatkan ID pengguna dengan role 'admin' (bukan super_admin / administrator)
     * untuk dijadikan verifikator pada otomasi tata tertib & prestasi.
     */
    public function getAdminVerifierId(): int
    {
        $admin = User::where('role', 'admin')->where('role', '!=', 'super_admin')->first()
            ?? User::where('role', 'admin')->first()
            ?? User::whereJsonContains('roles', 'admin')->where('role', '!=', 'super_admin')->first()
            ?? User::where('role', 'super_admin')->first();

        return $admin?->id ?: 1;
    }

    /**
     * Tentukan ID pelapor untuk pencatatan otomasi.
     * Menggunakan ID akun yang mengklik sinkronisasi otomasi hari ini,
     * atau fallback ke sesi login aktif (Auth::id()), atau fallback yang diberikan.
     */
    public function resolveReporterId(?int $explicitReporterId = null, ?int $fallbackId = null): int
    {
        if ($explicitReporterId) {
            return $explicitReporterId;
        }

        if (Auth::check() && Auth::id()) {
            return (int) Auth::id();
        }

        if ($fallbackId) {
            return $fallbackId;
        }

        return $this->getAdminVerifierId();
    }

    /**
     * Dapatkan item butir tata tertib P1.3 (Alpa/tidak masuk sekolah tanpa keterangan / Terlambat/Alpa tanpa ada keterangan).
     */
    public function getViolationItemP13(): ?ViolationItem
    {
        return ViolationItem::where('code', 'P1.3')->first()
            ?? ViolationItem::where('name', 'like', '%alpa%')->first()
            ?? ViolationItem::where('name', 'like', '%terlambat%alpa%')->first();
    }

    /**
     * Dapatkan item butir tata tertib P1.7 (Tidak masuk selama 3 (tiga hari) berturut-turut tanpa keterangan).
     */
    public function getViolationItemP17(): ?ViolationItem
    {
        return ViolationItem::where('code', 'P1.7')->first()
            ?? ViolationItem::where('name', 'like', '%3 (tiga hari)%')->first()
            ?? ViolationItem::where('name', 'like', '%berturut%')->first();
    }

    /**
     * Dapatkan item butir prestasi R1.1 (Melaksanakan shalat dzuhur berjamaah).
     */
    public function getAchievementItemR11(): ?AchievementItem
    {
        return AchievementItem::where('code', 'R1.1')->first()
            ?? AchievementItem::where('name', 'like', '%dzuhur%')->first();
    }

    /**
     * Dapatkan item butir prestasi R1.2 (Melaksanakan shalat dhuha bersama).
     */
    public function getAchievementItemR12(): ?AchievementItem
    {
        return AchievementItem::where('code', 'R1.2')->first()
            ?? AchievementItem::where('name', 'like', '%dhuha%')->first();
    }

    /**
     * Dapatkan item butir prestasi R1.3 (Melaksanakan hafalan Al-qur'an sebanyak 20 ayat sekali duduk).
     */
    public function getAchievementItemR13(): ?AchievementItem
    {
        return AchievementItem::where('code', 'R1.3')->first()
            ?? AchievementItem::where('name', 'like', '%20%ayat%')->first();
    }

    /**
     * Dapatkan item butir prestasi R1.6 (Melaksanakan kegiatan keputrian).
     */
    public function getAchievementItemR16(): ?AchievementItem
    {
        $item = AchievementItem::where('code', 'R1.6')->first()
            ?? AchievementItem::where('code', 'R.16')->first()
            ?? AchievementItem::where('name', 'like', '%kegiatan keputrian%')->first()
            ?? AchievementItem::where(function ($q) {
                $q->where('name', 'like', '%keputrian%')
                    ->where('name', 'not like', '%pengisi%')
                    ->where('name', 'not like', '%jum\'atan%');
            })->first();

        if (! $item) {
            $category = AchievementCategory::where('code', 'R1')->first()
                ?? AchievementCategory::where('id', 1)->first()
                ?? AchievementCategory::firstOrCreate(
                    ['name' => 'Pengembangan Keagamaan'],
                    ['code' => 'R1', 'description' => 'Prestasi bidang keagamaan dan ibadah']
                );

            $item = AchievementItem::create([
                'category_id' => $category->id,
                'code' => 'R1.6',
                'name' => 'Melaksanakan kegiatan keputrian',
                'default_points' => 5,
                'level' => 'SEKOLAH',
            ]);
        }

        return $item;
    }

    /**
     * Catat pelanggaran ALPA harian (P1.3) dan cek ALPA 3 hari berturut-turut (P1.7) secara realtime/idempoten.
     */
    public function recordAttendanceAlpaViolation(Attendance $attendance, bool $force = false, ?int $reporterId = null): array
    {
        if ($attendance->status !== 'ALPA') {
            return ['p13' => null, 'p17' => null];
        }

        $targetDate = $attendance->date ? Carbon::parse($attendance->date)->toDateString() : Carbon::today('Asia/Jakarta')->toDateString();
        $todayStr = Carbon::today('Asia/Jakarta')->toDateString();
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');

        // Poin 1 & 2 dijalankan jika dipaksa ($force) atau tanggal masa lalu atau hari ini sudah jam 14:00 ke atas
        $isEligible = $force || $targetDate < $todayStr || ($targetDate === $todayStr && $currentTime >= '14:00');
        if (! $isEligible) {
            return ['p13' => null, 'p17' => null];
        }

        $actualReporterId = $this->resolveReporterId($reporterId, $attendance->approved_by);
        $verifierId = $this->getAdminVerifierId();
        $classId = $attendance->class_id ?: $attendance->student?->class_id;
        $p13Violation = null;

        $itemP13 = $this->getViolationItemP13();
        if ($itemP13) {
            $alreadyExistsP13 = Violation::where('student_id', $attendance->student_id)
                ->where('item_id', $itemP13->id)
                ->whereDate('date', $targetDate)
                ->first();

            if (! $alreadyExistsP13) {
                $p13Violation = Violation::create([
                    'student_id' => $attendance->student_id,
                    'class_id' => $classId,
                    'category_id' => $itemP13->category_id,
                    'item_id' => $itemP13->id,
                    'violation_code' => 'PELANGGARAN-'.date('Y').'-'.strtoupper(Str::random(6)),
                    'title' => $itemP13->name,
                    'points' => $itemP13->default_points ?: 5,
                    'date' => $targetDate,
                    'time' => '14:00:00',
                    'location' => 'Lingkungan Sekolah',
                    'chronology' => "Otomatisasi sistem: Siswa tercatat status ALPA pada Presensi Utama tanggal {$targetDate} hingga jam 14.00 WIB",
                    'status' => 'DIVERIFIKASI',
                    'reporter_id' => $actualReporterId,
                    'verified_by' => $verifierId,
                    'verified_at' => Carbon::now('Asia/Jakarta'),
                ]);
            } else {
                $updates = [];
                if ($alreadyExistsP13->verified_by !== $verifierId) {
                    $updates['verified_by'] = $verifierId;
                }
                if ($reporterId && $alreadyExistsP13->reporter_id !== $actualReporterId) {
                    $updates['reporter_id'] = $actualReporterId;
                }
                if (! empty($updates)) {
                    $alreadyExistsP13->update($updates);
                }
                $p13Violation = $alreadyExistsP13;
            }
        }

        // Poin 2: Cek 3 hari berturut-turut ALPA untuk siswa ini
        $p17Violation = null;
        $itemP17 = $this->getViolationItemP17();
        if ($itemP17) {
            $targetDateObj = Carbon::parse($targetDate);
            $weekStart = $targetDateObj->copy()->startOfWeek()->toDateString();
            $weekEnd = $targetDateObj->copy()->endOfWeek()->toDateString();

            $alreadyReportedThisWeek = Violation::where('student_id', $attendance->student_id)
                ->where('item_id', $itemP17->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->first();

            if ($alreadyReportedThisWeek) {
                $updates = [];
                if ($alreadyReportedThisWeek->verified_by !== $verifierId) {
                    $updates['verified_by'] = $verifierId;
                }
                if ($reporterId && $alreadyReportedThisWeek->reporter_id !== $actualReporterId) {
                    $updates['reporter_id'] = $actualReporterId;
                }
                if (! empty($updates)) {
                    $alreadyReportedThisWeek->update($updates);
                }
                $p17Violation = $alreadyReportedThisWeek;
            } else {
                $recentAttendances = Attendance::where('student_id', $attendance->student_id)
                    ->whereDate('date', '<=', $targetDate)
                    ->orderBy('date', 'desc')
                    ->take(3)
                    ->get();

                if ($recentAttendances->count() === 3 && $recentAttendances->every(fn ($att) => $att->status === 'ALPA')) {
                    $newestDate = Carbon::parse($recentAttendances->first()->date);
                    $oldestDate = Carbon::parse($recentAttendances->last()->date);

                    if ($newestDate->diffInDays($targetDateObj) <= 2 && $oldestDate->diffInDays($newestDate) <= 6) {
                        $p17Violation = Violation::create([
                            'student_id' => $attendance->student_id,
                            'class_id' => $classId,
                            'category_id' => $itemP17->category_id,
                            'item_id' => $itemP17->id,
                            'violation_code' => 'PELANGGARAN-'.date('Y').'-'.strtoupper(Str::random(6)),
                            'title' => $itemP17->name,
                            'points' => $itemP17->default_points ?: 10,
                            'date' => $targetDate,
                            'time' => '14:00:00',
                            'location' => 'Lingkungan Sekolah',
                            'chronology' => "Otomatisasi sistem: Siswa terdeteksi ALPA berturut-turut selama 3 hari di Presensi Utama per tanggal {$targetDate} jam 14.00 WIB",
                            'status' => 'DIVERIFIKASI',
                            'reporter_id' => $actualReporterId,
                            'verified_by' => $verifierId,
                            'verified_at' => Carbon::now('Asia/Jakarta'),
                        ]);
                    }
                }
            }
        }

        return ['p13' => $p13Violation, 'p17' => $p17Violation];
    }

    /**
     * 1. Otomasi Jam 14.00 WIB:
     * Jika siswa di presensi utama pada Laporan Presensi Siswa Terpadu status ALPA hingga jam 14.00 WIB,
     * sistem otomatis menginput pelanggaran siswa:
     * Kategori Pelanggaran (Kehadiran - P1) -> Alpa/tidak masuk sekolah tanpa keterangan / Terlambat/Alpa tanpa ada keterangan (P1.3).
     * Berlaku 1 hari 1 kali di hari itu. Tidak perlu approve admin (status DIVERIFIKASI).
     */
    public function processDailyAlpaViolation(?string $date = null, int|array|null $classId = null, ?int $reporterId = null): array
    {
        $targetDate = $date ?: Carbon::today('Asia/Jakarta')->toDateString();
        $itemP13 = $this->getViolationItemP13();

        if (! $itemP13) {
            return [
                'status' => 'error',
                'message' => 'Item pelanggaran P1.3 (Alpa tanpa keterangan) tidak ditemukan di master data.',
                'processed_count' => 0,
            ];
        }

        // Jangan proses pelanggaran Alpa massal jika sama sekali tidak ada presensi riil yang tercatat pada tanggal tersebut
        $hasRealAttendance = Attendance::whereDate('date', $targetDate)
            ->where('method', '!=', 'SISTEM')
            ->exists();

        if (! $hasRealAttendance && ! app()->runningUnitTests()) {
            return [
                'status' => 'skipped',
                'date' => $targetDate,
                'processed_count' => 0,
                'message' => 'Tidak ada aktivitas presensi riil pada tanggal tersebut. Otomasi pelanggaran Alpa dilewati.',
            ];
        }

        // Cari seluruh catatan presensi utama dengan status ALPA pada target tanggal
        $alpaAttendancesQuery = Attendance::whereDate('date', $targetDate)
            ->where('status', 'ALPA');

        if ($classId !== null) {
            $classIds = is_array($classId) ? $classId : [$classId];
            $alpaAttendancesQuery->where(function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)
                    ->orWhereHas('student', fn ($sq) => $sq->whereIn('class_id', $classIds));
            });
        }

        $alpaAttendances = $alpaAttendancesQuery->get();

        $processedCount = 0;

        foreach ($alpaAttendances as $attendance) {
            $res = $this->recordAttendanceAlpaViolation($attendance, force: true, reporterId: $reporterId);
            if ($res['p13'] && $res['p13']->wasRecentlyCreated) {
                $processedCount++;
            }
        }

        return [
            'status' => 'success',
            'date' => $targetDate,
            'processed_count' => $processedCount,
            'message' => "Berhasil memproses pelanggaran Alpa jam 14.00 WIB: {$processedCount} siswa dicatat pelanggaran P1.3.",
        ];
    }

    /**
     * 2. Otomasi Jam 14.00 WIB:
     * Jika siswa di presensi utama terdeteksi ALPA berturut-turut selama 3 hari,
     * sistem otomatis menginput pelanggaran siswa:
     * Kategori Pelanggaran (Kehadiran - P1) -> Tidak masuk selama 3 (tiga hari) berturut turut tanpa ada keterangan (P1.7).
     * Berlaku 1 kali dalam 1 minggu. Tidak perlu approve admin (status DIVERIFIKASI).
     */
    public function processThreeDaysConsecutiveAlpaViolation(?string $date = null, int|array|null $classId = null, ?int $reporterId = null): array
    {
        $targetDate = $date ?: Carbon::today('Asia/Jakarta')->toDateString();
        $targetDateObj = Carbon::parse($targetDate);
        $itemP17 = $this->getViolationItemP17();

        if (! $itemP17) {
            return [
                'status' => 'error',
                'message' => 'Item pelanggaran P1.7 (Alpa 3 hari berturut-turut) tidak ditemukan di master data.',
                'processed_count' => 0,
            ];
        }

        $actualReporterId = $this->resolveReporterId($reporterId);
        $verifierId = $this->getAdminVerifierId();

        // Rentang 1 minggu kalender (Senin s.d. Minggu)
        $weekStart = $targetDateObj->copy()->startOfWeek()->toDateString();
        $weekEnd = $targetDateObj->copy()->endOfWeek()->toDateString();

        $studentsQuery = Student::where('is_active', true);
        if ($classId !== null) {
            $classIds = is_array($classId) ? $classId : [$classId];
            $studentsQuery->whereIn('class_id', $classIds);
        }

        $students = $studentsQuery->get();
        $processedCount = 0;

        foreach ($students as $student) {
            // Aturan: Berlaku 1 kali dalam 1 minggu
            $alreadyReportedThisWeek = Violation::where('student_id', $student->id)
                ->where('item_id', $itemP17->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->first();

            if ($alreadyReportedThisWeek) {
                $updates = [];
                if ($alreadyReportedThisWeek->verified_by !== $verifierId) {
                    $updates['verified_by'] = $verifierId;
                }
                if ($reporterId && $alreadyReportedThisWeek->reporter_id !== $actualReporterId) {
                    $updates['reporter_id'] = $actualReporterId;
                }
                if (! empty($updates)) {
                    $alreadyReportedThisWeek->update($updates);
                }

                continue;
            }

            // Ambil 3 data presensi terakhir sampai dengan target date
            $recentAttendances = Attendance::where('student_id', $student->id)
                ->whereDate('date', '<=', $targetDate)
                ->orderBy('date', 'desc')
                ->take(3)
                ->get();

            if ($recentAttendances->count() < 3) {
                continue;
            }

            // Pastikan seluruh 3 presensi berturut-turut tersebut berstatus ALPA
            $isAllAlpa = $recentAttendances->every(fn ($att) => $att->status === 'ALPA');
            if (! $isAllAlpa) {
                continue;
            }

            // Pastikan presensi terbaru adalah targetDate atau maksimal 2 hari kalender sebelumnya
            $newestDate = Carbon::parse($recentAttendances->first()->date);
            if ($newestDate->diffInDays($targetDateObj) > 2) {
                continue;
            }

            // Pastikan rentang ketiga tanggal adalah wajar (maksimal 6 hari kalender termasuk jeda akhir pekan)
            $oldestDate = Carbon::parse($recentAttendances->last()->date);
            if ($oldestDate->diffInDays($newestDate) > 6) {
                continue;
            }

            Violation::create([
                'student_id' => $student->id,
                'class_id' => $student->class_id,
                'category_id' => $itemP17->category_id,
                'item_id' => $itemP17->id,
                'violation_code' => 'PELANGGARAN-'.date('Y').'-'.strtoupper(Str::random(6)),
                'title' => $itemP17->name,
                'points' => $itemP17->default_points ?: 10,
                'date' => $targetDate,
                'time' => '14:00:00',
                'location' => 'Lingkungan Sekolah',
                'chronology' => "Otomatisasi sistem: Siswa terdeteksi ALPA berturut-turut selama 3 hari di Presensi Utama per tanggal {$targetDate} jam 14.00 WIB",
                'status' => 'DIVERIFIKASI',
                'reporter_id' => $actualReporterId,
                'verified_by' => $verifierId,
                'verified_at' => Carbon::now('Asia/Jakarta'),
            ]);

            $processedCount++;
        }

        return [
            'status' => 'success',
            'date' => $targetDate,
            'processed_count' => $processedCount,
            'message' => "Berhasil memproses pelanggaran Alpa 3 hari jam 14.00 WIB: {$processedCount} siswa dicatat pelanggaran P1.7.",
        ];
    }

    /**
     * Catat prestasi kehadiran ibadah (Dhuha R1.2, Dzuhur R1.1, Keputrian R1.6) secara realtime/idempoten.
     */
    public function recordWorshipAchievement(WorshipAttendance $worship, ?int $reporterId = null): ?Achievement
    {
        if ($worship->status !== 'HADIR') {
            return null;
        }

        $targetDate = $worship->date ? Carbon::parse($worship->date)->toDateString() : Carbon::today('Asia/Jakarta')->toDateString();
        $classId = $worship->class_id ?: $worship->student?->class_id;
        $actualReporterId = $this->resolveReporterId($reporterId, $worship->recorded_by);
        $verifierId = $this->getAdminVerifierId();

        if ($worship->activity_type === WorshipAttendance::ACTIVITY_DHUHA) {
            $item = $this->getAchievementItemR12();
            if (! $item) {
                return null;
            }

            $exists = Achievement::where('student_id', $worship->student_id)
                ->where('item_id', $item->id)
                ->whereDate('date', $targetDate)
                ->first();

            if ($exists) {
                $updates = [];
                if ($exists->verified_by !== $verifierId) {
                    $updates['verified_by'] = $verifierId;
                }
                if ($reporterId && $exists->reporter_id !== $actualReporterId) {
                    $updates['reporter_id'] = $actualReporterId;
                }
                if (! empty($updates)) {
                    $exists->update($updates);
                }

                return $exists;
            }

            return Achievement::create([
                'student_id' => $worship->student_id,
                'class_id' => $classId,
                'category_id' => $item->category_id,
                'item_id' => $item->id,
                'achievement_code' => 'PRESTASI-'.date('Y').'-'.strtoupper(Str::random(6)),
                'title' => $item->name,
                'level' => 'SEKOLAH',
                'points' => $item->default_points ?: 5,
                'date' => $targetDate,
                'description' => "Otomatisasi sistem: Siswa tercatat HADIR pada Dokumen Laporan Shalat Dhuha Bersama tanggal {$targetDate} hingga jam 11.00 WIB",
                'status' => 'DIVERIFIKASI',
                'reporter_id' => $actualReporterId,
                'verified_by' => $verifierId,
                'verified_at' => Carbon::now('Asia/Jakarta'),
            ]);
        }

        if ($worship->activity_type === WorshipAttendance::ACTIVITY_DZUHUR_JUMAT) {
            $item = $this->getAchievementItemR11();
            if (! $item) {
                return null;
            }

            $exists = Achievement::where('student_id', $worship->student_id)
                ->where('item_id', $item->id)
                ->whereDate('date', $targetDate)
                ->first();

            if ($exists) {
                $updates = [];
                if ($exists->verified_by !== $verifierId) {
                    $updates['verified_by'] = $verifierId;
                }
                if ($reporterId && $exists->reporter_id !== $actualReporterId) {
                    $updates['reporter_id'] = $actualReporterId;
                }
                if (! empty($updates)) {
                    $exists->update($updates);
                }

                return $exists;
            }

            return Achievement::create([
                'student_id' => $worship->student_id,
                'class_id' => $classId,
                'category_id' => $item->category_id,
                'item_id' => $item->id,
                'achievement_code' => 'PRESTASI-'.date('Y').'-'.strtoupper(Str::random(6)),
                'title' => $item->name,
                'level' => 'SEKOLAH',
                'points' => $item->default_points ?: 5,
                'date' => $targetDate,
                'description' => "Otomatisasi sistem: Siswa tercatat HADIR pada Dokumen Laporan Shalat Dzuhur/Jum'at Berjamaah tanggal {$targetDate} hingga jam 14.00 WIB",
                'status' => 'DIVERIFIKASI',
                'reporter_id' => $actualReporterId,
                'verified_by' => $verifierId,
                'verified_at' => Carbon::now('Asia/Jakarta'),
            ]);
        }

        if ($worship->activity_type === WorshipAttendance::ACTIVITY_KEPUTRIAN) {
            $item = $this->getAchievementItemR16();
            if (! $item) {
                return null;
            }

            $exists = Achievement::where('student_id', $worship->student_id)
                ->where('item_id', $item->id)
                ->whereDate('date', $targetDate)
                ->first();

            if ($exists) {
                $updates = [];
                if ($exists->verified_by !== $verifierId) {
                    $updates['verified_by'] = $verifierId;
                }
                if ($reporterId && $exists->reporter_id !== $actualReporterId) {
                    $updates['reporter_id'] = $actualReporterId;
                }
                if (! empty($updates)) {
                    $exists->update($updates);
                }

                return $exists;
            }

            return Achievement::create([
                'student_id' => $worship->student_id,
                'class_id' => $classId,
                'category_id' => $item->category_id,
                'item_id' => $item->id,
                'achievement_code' => 'PRESTASI-'.date('Y').'-'.strtoupper(Str::random(6)),
                'title' => $item->name,
                'level' => $item->level ?: 'SEKOLAH',
                'points' => $item->default_points ?: 5,
                'date' => $targetDate,
                'description' => "Otomatisasi sistem: Siswi tercatat HADIR pada Laporan Presensi Keputrian tanggal {$targetDate} hingga jam 14.30 WIB",
                'status' => 'DIVERIFIKASI',
                'reporter_id' => $actualReporterId,
                'verified_by' => $verifierId,
                'verified_at' => Carbon::now('Asia/Jakarta'),
            ]);
        }

        return null;
    }

    /**
     * 3. Otomasi Jam 11.00 WIB:
     * Jika siswa di Presensi Shalat Dhuha pada Dokumen Laporan Shalat Dhuha Bersama
     * status HADIR hingga jam 11.00 WIB, sistem otomatis menginput Prestasi Siswa:
     * Kategori Prestasi (Pengembangan Keagamaan - R1) -> Melaksanakan shalat dhuha bersama (R1.2).
     * Berlaku 1 hari 1 kali di hari itu. Tidak perlu approve admin (status DIVERIFIKASI).
     */
    public function processDhuhaPresenceAchievement(?string $date = null, int|array|null $classId = null, ?int $reporterId = null): array
    {
        $targetDate = $date ?: Carbon::today('Asia/Jakarta')->toDateString();
        $itemR12 = $this->getAchievementItemR12();

        if (! $itemR12) {
            return [
                'status' => 'error',
                'message' => 'Item prestasi R1.2 (Shalat Dhuha bersama) tidak ditemukan di master data.',
                'processed_count' => 0,
            ];
        }

        // Cari siswa yang hadir Shalat Dhuha pada target tanggal
        $worshipAttendancesQuery = WorshipAttendance::whereDate('date', $targetDate)
            ->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)
            ->where('status', 'HADIR');

        if ($classId !== null) {
            $classIds = is_array($classId) ? $classId : [$classId];
            $worshipAttendancesQuery->where(function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)
                    ->orWhereHas('student', fn ($sq) => $sq->whereIn('class_id', $classIds));
            });
        }

        $worshipAttendances = $worshipAttendancesQuery->get();

        $processedCount = 0;

        foreach ($worshipAttendances as $worship) {
            $ach = $this->recordWorshipAchievement($worship, $reporterId);
            if ($ach && $ach->wasRecentlyCreated) {
                $processedCount++;
            }
        }

        return [
            'status' => 'success',
            'date' => $targetDate,
            'processed_count' => $processedCount,
            'message' => "Berhasil memproses prestasi Shalat Dhuha jam 11.00 WIB: {$processedCount} siswa dicatat prestasi R1.2.",
        ];
    }

    /**
     * 4. Otomasi Jam 14.00 WIB:
     * Jika siswa di Presensi Shalat Dzuhur/Jum'at Berjamaah pada Dokumen Laporan Shalat Dzuhur/Jum'at Berjamaah
     * status HADIR hingga jam 14.00 WIB, sistem otomatis menginput Prestasi Siswa:
     * Kategori Prestasi (Pengembangan Keagamaan - R1) -> Melaksanakan shalat dzuhur berjamaah (R1.1).
     * Berlaku 1 hari 1 kali di hari itu. Tidak perlu approve admin (status DIVERIFIKASI).
     */
    public function processDzuhurPresenceAchievement(?string $date = null, int|array|null $classId = null, ?int $reporterId = null): array
    {
        $targetDate = $date ?: Carbon::today('Asia/Jakarta')->toDateString();
        $itemR11 = $this->getAchievementItemR11();

        if (! $itemR11) {
            return [
                'status' => 'error',
                'message' => 'Item prestasi R1.1 (Shalat Dzuhur berjamaah) tidak ditemukan di master data.',
                'processed_count' => 0,
            ];
        }

        // Cari siswa yang hadir Shalat Dzuhur/Jumat pada target tanggal
        $worshipAttendancesQuery = WorshipAttendance::whereDate('date', $targetDate)
            ->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT)
            ->where('status', 'HADIR');

        if ($classId !== null) {
            $classIds = is_array($classId) ? $classId : [$classId];
            $worshipAttendancesQuery->where(function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)
                    ->orWhereHas('student', fn ($sq) => $sq->whereIn('class_id', $classIds));
            });
        }

        $worshipAttendances = $worshipAttendancesQuery->get();

        $processedCount = 0;

        foreach ($worshipAttendances as $worship) {
            $ach = $this->recordWorshipAchievement($worship, $reporterId);
            if ($ach && $ach->wasRecentlyCreated) {
                $processedCount++;
            }
        }

        return [
            'status' => 'success',
            'date' => $targetDate,
            'processed_count' => $processedCount,
            'message' => "Berhasil memproses prestasi Shalat Dzuhur jam 14.00 WIB: {$processedCount} siswa dicatat prestasi R1.1.",
        ];
    }

    /**
     * 5. Otomasi Jam 14.30 WIB:
     * Jika siswi pada laporan presensi Keputrian status HADIR hingga jam 14.30 WIB,
     * sistem otomatis menginput Prestasi Siswa:
     * Kategori Prestasi (Pengembangan Keagamaan - R1) -> Melaksanakan kegiatan keputrian (R1.6).
     * Berlaku 1 hari 1 kali di hari itu. Tidak perlu approve admin (status DIVERIFIKASI).
     */
    public function processKeputrianPresenceAchievement(?string $date = null, int|array|null $classId = null, ?int $reporterId = null): array
    {
        $targetDate = $date ?: Carbon::today('Asia/Jakarta')->toDateString();
        $itemR16 = $this->getAchievementItemR16();

        if (! $itemR16) {
            return [
                'status' => 'error',
                'message' => 'Item prestasi R1.6 (Melaksanakan kegiatan keputrian) tidak ditemukan di master data.',
                'processed_count' => 0,
            ];
        }

        // Cari siswi yang hadir kegiatan Keputrian pada target tanggal
        $worshipAttendancesQuery = WorshipAttendance::whereDate('date', $targetDate)
            ->where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN)
            ->where('status', 'HADIR');

        if ($classId !== null) {
            $classIds = is_array($classId) ? $classId : [$classId];
            $worshipAttendancesQuery->where(function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)
                    ->orWhereHas('student', fn ($sq) => $sq->whereIn('class_id', $classIds));
            });
        }

        $worshipAttendances = $worshipAttendancesQuery->get();

        $processedCount = 0;

        foreach ($worshipAttendances as $worship) {
            $ach = $this->recordWorshipAchievement($worship, $reporterId);
            if ($ach && $ach->wasRecentlyCreated) {
                $processedCount++;
            }
        }

        return [
            'status' => 'success',
            'date' => $targetDate,
            'processed_count' => $processedCount,
            'message' => "Berhasil memproses prestasi Keputrian jam 14.30 WIB: {$processedCount} siswi dicatat prestasi R1.6.",
        ];
    }

    /**
     * 6. Otomatisasi Setoran Hafalan Al-Qur'an Juz 30 (>= 20 Ayat):
     * Jika siswa pada laporan setor hafalan al-qur'an juz 30 terisi 20 ayat atau lebih pada Jumlah Ayat yang Disetor,
     * sistem otomatis menginput poin prestasi sub poin "Melaksanakan hafalan Al-qur'an sebanyak 20 ayat sekali duduk" (R1.3),
     * DIVERIFIKASI tanpa perlu approve admin.
     */
    public function recordTahfidzAchievement(QuranTahfidzRecord $record): ?Achievement
    {
        if (($record->ayat_count ?? 0) < 20) {
            return null;
        }

        $itemR13 = $this->getAchievementItemR13();
        if (! $itemR13) {
            return null;
        }

        // Cek apakah prestasi untuk setoran ID ini sudah pernah dicatat
        $existing = Achievement::where('student_id', $record->student_id)
            ->where('item_id', $itemR13->id)
            ->where(function ($q) use ($record) {
                $q->where('description', 'like', "%Setoran Tahfidz ID #{$record->id}%")
                    ->orWhere('description', 'like', "%#{$record->id}%")
                    ->orWhere(function ($sub) use ($record) {
                        $sub->whereDate('date', $record->deposit_date ?: Carbon::today('Asia/Jakarta')->toDateString())
                            ->where('description', 'like', "%{$record->surah_name}%");
                    });
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        return Achievement::create([
            'student_id' => $record->student_id,
            'class_id' => $record->class_id ?: $record->student?->class_id,
            'category_id' => $itemR13->category_id,
            'item_id' => $itemR13->id,
            'achievement_code' => 'PRESTASI-'.date('Y').'-'.strtoupper(Str::random(6)),
            'title' => $itemR13->name,
            'level' => $itemR13->level ?: 'SEKOLAH',
            'points' => $itemR13->default_points ?: 10,
            'date' => $record->deposit_date ?: Carbon::today('Asia/Jakarta')->toDateString(),
            'description' => "Otomatisasi sistem: Setoran Tahfidz ID #{$record->id} ({$record->surah_name} ayat {$record->ayat_range}, total {$record->ayat_count} ayat)",
            'reporter_id' => $record->examiner_id ?: 1,
            'status' => 'DIVERIFIKASI',
            'verified_by' => $record->examiner_id ?: 1,
            'verified_at' => Carbon::now('Asia/Jakarta'),
        ]);
    }

    /**
     * Memproses batch setoran hafalan Al-Qur'an (Juz 30) yang mencapai >= 20 ayat.
     */
    public function processTahfidzAchievements(?string $date = null, int|array|null $classId = null): array
    {
        $query = QuranTahfidzRecord::where('ayat_count', '>=', 20);
        if ($date) {
            $query->whereDate('deposit_date', $date);
        }
        if ($classId !== null) {
            $classIds = is_array($classId) ? $classId : [$classId];
            $query->where(function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)
                    ->orWhereHas('student', fn ($sq) => $sq->whereIn('class_id', $classIds));
            });
        }

        $records = $query->get();
        $processedCount = 0;

        foreach ($records as $record) {
            $ach = $this->recordTahfidzAchievement($record);
            if ($ach && $ach->wasRecentlyCreated) {
                $processedCount++;
            }
        }

        return [
            'status' => 'success',
            'processed_count' => $processedCount,
            'message' => "Berhasil memproses prestasi Tahfidz: {$processedCount} setoran dicatat ke prestasi R1.3.",
        ];
    }

    /**
     * Menjalankan sinkronisasi seluruh otomasi tata tertib dan prestasi (Poin 1 s/d 6)
     * secara terpadu, mandiri, dan idempoten untuk tanggal target.
     * Jika $classId diberikan (misal wali kelas binaan), eksekusi dibatasi hanya untuk siswa kelas tersebut.
     */
    public function syncDailyAutomations(?string $date = null, int|array|null $classId = null, ?int $reporterId = null): array
    {
        $targetDate = $date ?: Carbon::today('Asia/Jakarta')->toDateString();

        // 1. Pastikan siswa yang belum absen tercatat ALPA jika jadwal hari aktif
        if (class_exists(AttendanceService::class)) {
            $schedule = AttendanceService::getScheduleForDay(Carbon::parse($targetDate, 'Asia/Jakarta'));
            if ($schedule['is_active'] && empty($schedule['is_holiday'])) {
                app(AttendanceService::class)->ensureDailyAttendanceRecords($targetDate, $classId);
            }
        }

        // 2. Poin 3: Prestasi Shalat Dhuha (R1.2)
        $resDhuha = $this->processDhuhaPresenceAchievement($targetDate, $classId, $reporterId);

        // 3. Poin 1: Pelanggaran ALPA Presensi Utama (P1.3)
        $resAlpa = $this->processDailyAlpaViolation($targetDate, $classId, $reporterId);

        // 4. Poin 2: Pelanggaran ALPA 3 Hari Berturut-turut (P1.7)
        $resAlpa3 = $this->processThreeDaysConsecutiveAlpaViolation($targetDate, $classId, $reporterId);

        // 5. Poin 4: Prestasi Shalat Dzuhur/Jum'at (R1.1)
        $resDzuhur = $this->processDzuhurPresenceAchievement($targetDate, $classId, $reporterId);

        // 6. Poin 5: Prestasi Keputrian (R1.6)
        $resKeputrian = $this->processKeputrianPresenceAchievement($targetDate, $classId, $reporterId);

        // 7. Poin 6: Prestasi Setoran Tahfidz (R1.3)
        $resTahfidz = $this->processTahfidzAchievements($targetDate, $classId);

        $totalViolations = ($resAlpa['processed_count'] ?? 0) + ($resAlpa3['processed_count'] ?? 0);
        $totalAchievements = ($resDhuha['processed_count'] ?? 0) + ($resDzuhur['processed_count'] ?? 0) + ($resKeputrian['processed_count'] ?? 0) + ($resTahfidz['processed_count'] ?? 0);

        return [
            'status' => 'success',
            'date' => $targetDate,
            'dhuha' => $resDhuha,
            'alpa' => $resAlpa,
            'alpa_3_days' => $resAlpa3,
            'dzuhur' => $resDzuhur,
            'keputrian' => $resKeputrian,
            'tahfidz' => $resTahfidz,
            'total_violations' => $totalViolations,
            'total_achievements' => $totalAchievements,
            'summary' => "Total {$totalViolations} pelanggaran dan {$totalAchievements} prestasi otomatis diproses & dicatat.",
        ];
    }
}

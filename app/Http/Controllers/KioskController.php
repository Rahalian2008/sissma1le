<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\ExtracurricularAttendance;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\WorshipAttendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KioskController extends Controller
{
    /**
     * Display the main Kiosk screen (Lobby / Monitor TV display)
     */
    public function index(Request $request): View
    {
        $school = [
            'name' => SchoolSetting::get('school_name', 'SMA NEGERI 1 LENGKONG'),
            'address' => SchoolSetting::get('school_address', 'Jl. Raya Lengkong No. 01, Nganjuk, Jawa Timur'),
            'logo' => SchoolSetting::get('school_logo'),
            'kop_image' => SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image')),
        ];

        $data = $this->gatherKioskData();

        return view('kiosk.index', compact('school', 'data'));
    }

    /**
     * JSON Endpoint for real-time polling (every 15-20 seconds)
     */
    public function data(Request $request): JsonResponse
    {
        return response()->json($this->gatherKioskData());
    }

    /**
     * Gather live data across 6 Kiosk display widgets
     */
    protected function gatherKioskData(): array
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        // 1. Presensi Kehadiran Harian
        $totalStudents = Student::where('is_active', true)->count();
        if ($totalStudents === 0) {
            $totalStudents = Student::count();
        }

        $dailyCounts = [
            'hadir' => Attendance::whereDate('date', $today)->whereIn('status', ['HADIR', 'hadir'])->count(),
            'terlambat' => Attendance::whereDate('date', $today)->whereIn('status', ['TERLAMBAT', 'terlambat'])->count(),
            'izin' => Attendance::whereDate('date', $today)->whereIn('status', ['IZIN', 'izin'])->count(),
            'sakit' => Attendance::whereDate('date', $today)->whereIn('status', ['SAKIT', 'sakit'])->count(),
            'alpa' => Attendance::whereDate('date', $today)->whereIn('status', ['ALPA', 'alpa'])->count(),
        ];
        $totalRecorded = array_sum($dailyCounts);
        $attendancePercentage = $totalStudents > 0
            ? round((($dailyCounts['hadir'] + $dailyCounts['terlambat']) / $totalStudents) * 100, 1)
            : 0;

        $recentDaily = Attendance::with('student.schoolClass')
            ->whereDate('date', $today)
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'name' => $r->student?->name ?? $r->student_name ?? 'Siswa',
                'class' => $r->student?->schoolClass?->name ?? '-',
                'time' => substr($r->time ?? $r->created_at->format('H:i:s'), 0, 5),
                'status' => strtoupper($r->status),
                'method' => $r->method ?? 'RFID',
            ]);

        // 2. Shalat Dhuha Bersama
        $dhuhaLock = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DHUHA, $now);
        $dhuhaToday = WorshipAttendance::with(['student.schoolClass'])
            ->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)
            ->whereDate('date', $today)
            ->latest('id')
            ->get();

        $recentDhuha = $dhuhaToday->take(10)->map(fn ($w) => [
            'name' => $w->student?->name ?? 'Siswa',
            'class' => $w->schoolClass?->name ?? $w->student?->schoolClass?->name ?? '-',
            'time' => substr($w->time ?? $w->created_at->format('H:i:s'), 0, 5),
            'method' => $w->method ?? 'RFID',
        ]);

        // 3. Shalat Dzuhur / Jum'at Berjamaah
        $dzuhurLock = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DZUHUR_JUMAT, $now);
        $dzuhurToday = WorshipAttendance::with(['student.schoolClass'])
            ->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT)
            ->whereDate('date', $today)
            ->latest('id')
            ->get();

        $recentDzuhur = $dzuhurToday->take(10)->map(fn ($w) => [
            'name' => $w->student?->name ?? 'Siswa',
            'class' => $w->schoolClass?->name ?? $w->student?->schoolClass?->name ?? '-',
            'time' => substr($w->time ?? $w->created_at->format('H:i:s'), 0, 5),
            'method' => $w->method ?? 'RFID',
        ]);

        // 4. Presensi Kegiatan Keputrian
        $keputrianLock = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_KEPUTRIAN, $now);
        $keputrianToday = WorshipAttendance::with(['student.schoolClass'])
            ->where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN)
            ->whereDate('date', $today)
            ->latest('id')
            ->get();

        $recentKeputrian = $keputrianToday->take(10)->map(fn ($w) => [
            'name' => $w->student?->name ?? 'Siswi',
            'class' => $w->schoolClass?->name ?? $w->student?->schoolClass?->name ?? '-',
            'time' => substr($w->time ?? $w->created_at->format('H:i:s'), 0, 5),
            'method' => $w->method ?? 'RFID',
        ]);

        // 5. Presensi Ekstrakurikuler Hari Ini
        $extraToday = ExtracurricularAttendance::with(['student.schoolClass', 'extracurricular'])
            ->whereDate('date', $today)
            ->latest('id')
            ->get();
        $recentExtra = $extraToday->take(10)->map(fn ($e) => [
            'name' => $e->student?->name ?? 'Siswa',
            'class' => $e->schoolClass?->name ?? $e->student?->schoolClass?->name ?? '-',
            'extracurricular' => $e->extracurricular?->name ?? 'Ekstrakurikuler',
            'time' => substr($e->time ?? $e->created_at->format('H:i:s'), 0, 5),
            'status' => strtoupper($e->status),
        ]);

        // 6. Buku Kontrol Digital Setor Hafalan Al-Qur'an (Tahfidz Juz 30)
        $tahfidzTodayCount = QuranTahfidzRecord::whereDate('deposit_date', $today)->count();
        $tahfidzPassedCount = QuranTahfidzRecord::whereDate('deposit_date', $today)->where('overall_grade', 'Lulus')->count();
        $recentTahfidz = QuranTahfidzRecord::with(['student.schoolClass'])
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'name' => $t->student?->name ?? 'Siswa',
                'class' => $t->schoolClass?->name ?? $t->student?->schoolClass?->name ?? '-',
                'surah' => $t->surah_name.($t->ayat_range ? " ({$t->ayat_range})" : ''),
                'grade' => $t->overall_grade,
                'kelancaran' => $t->kelancaran ?? 'Baik',
                'tajwid' => $t->tajwid ?? 'Baik',
                'date' => Carbon::parse($t->deposit_date)->translatedFormat('d M'),
                'time' => substr($t->deposit_time ?? $t->created_at->format('H:i:s'), 0, 5),
            ]);

        // Ranking Terbaik Tahfidz Juz 30 per Jenjang Kelas (X, XI, XII)
        $tahfidzRankings = [];
        foreach (['X', 'XI', 'XII'] as $grade) {
            $targetSurahs = QuranTahfidzRecord::getSurahsByGrade($grade);
            $targetSurahKeys = array_keys($targetSurahs);
            $totalTarget = count($targetSurahs);

            $topStudents = Student::where('is_active', true)
                ->whereHas('schoolClass', function ($q) use ($grade) {
                    $q->where('grade', $grade)
                        ->orWhere('grade', match ($grade) {
                            'X' => '10',
                            'XI' => '11',
                            'XII' => '12',
                            default => $grade
                        });
                })
                ->withCount([
                    'tahfidzRecords as lulus_count' => function ($q) use ($targetSurahKeys) {
                        $q->where('overall_grade', 'Lulus')
                            ->whereIn('surah_number', $targetSurahKeys);
                    },
                ])
                ->whereHas('tahfidzRecords', function ($q) use ($targetSurahKeys) {
                    $q->where('overall_grade', 'Lulus')
                        ->whereIn('surah_number', $targetSurahKeys);
                })
                ->orderByDesc('lulus_count')
                ->limit(5)
                ->with('schoolClass')
                ->get();

            $tahfidzRankings[$grade] = [
                'total_target' => $totalTarget,
                'students' => $topStudents->values()->map(function ($s, $idx) use ($totalTarget) {
                    $pct = $totalTarget > 0 ? round(($s->lulus_count / $totalTarget) * 100, 1) : 0;

                    return [
                        'rank' => $idx + 1,
                        'name' => $s->name,
                        'class' => $s->schoolClass?->name ?? '-',
                        'lulus_count' => (int) $s->lulus_count,
                        'total_target' => $totalTarget,
                        'pct' => $pct,
                        'badge' => match ($idx) {
                            0 => '👑 TERBAIK 1',
                            1 => '⭐ TERBAIK 2',
                            2 => '✨ TERBAIK 3',
                            default => 'TOP '.($idx + 1),
                        },
                    ];
                })->all(),
            ];
        }

        // 7. Ranking Poin Prestasi Positif Siswa (Top 10)
        $rankingData = Achievement::selectRaw('student_id, SUM(points) as total_points, COUNT(id) as total_achievements')
            ->where('status', 'DIVERIFIKASI')
            ->groupBy('student_id')
            ->orderByDesc('total_points')
            ->limit(10)
            ->with(['student.schoolClass'])
            ->get();

        $ranking = $rankingData->map(function ($item, $idx) {
            return [
                'rank' => $idx + 1,
                'name' => $item->student?->name ?? 'Siswa Berprestasi',
                'nis' => $item->student?->nis ?? '-',
                'class' => $item->student?->schoolClass?->name ?? '-',
                'points' => (int) $item->total_points,
                'achievements_count' => (int) $item->total_achievements,
                'badge' => match ($idx) {
                    0 => '🥇 JUARA 1',
                    1 => '🥈 JUARA 2',
                    2 => '🥉 JUARA 3',
                    default => '🎖️ TOP '.($idx + 1),
                },
            ];
        });

        // 8. Jadwal Aktivitas Berdasarkan Master Data & Pengaturan Sekolah
        $todaySchedule = AttendanceService::getScheduleForDay($now);

        $dhuhaCfg = WorshipAttendance::getScheduleConfig(WorshipAttendance::ACTIVITY_DHUHA, $now);
        $dzuhurCfg = WorshipAttendance::getScheduleConfig(WorshipAttendance::ACTIVITY_DZUHUR_JUMAT, $now);
        $keputrianCfg = WorshipAttendance::getScheduleConfig(WorshipAttendance::ACTIVITY_KEPUTRIAN, $now);

        $extraDays = WorshipAttendance::parseDays(SchoolSetting::get('extra_attendance_days', 'senin,selasa,rabu,kamis,jumat,sabtu'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
        $extraDaysLabel = WorshipAttendance::formatDaysLabel($extraDays);
        $extraStart = SchoolSetting::get('extra_attendance_start', SchoolSetting::get('extracurricular_start_time', '14:00'));
        $extraEnd = SchoolSetting::get('extra_attendance_end', SchoolSetting::get('extracurricular_end_time', '17:30'));

        $schedules = [
            'presensi_utama' => [
                'name' => 'Presensi Utama (Harian)',
                'time' => "Masuk: {$todaySchedule['in_start']} - {$todaySchedule['in_late_cutoff']} WIB • Pulang: {$todaySchedule['out_start']} - {$todaySchedule['out_end']} WIB",
                'cutoff' => "Tepat Waktu: s.d. {$todaySchedule['in_on_time']} WIB (Batas Terlambat: {$todaySchedule['in_late_cutoff']} WIB)",
                'days' => "Hari {$todaySchedule['day_name']} (Sesuai Master Data)",
                'target' => 'Seluruh Siswa/i',
                'is_active' => $todaySchedule['is_active'],
                'is_holiday' => $todaySchedule['is_holiday'],
                'holiday_name' => $todaySchedule['holiday_name'],
            ],
            'shalat_dhuha' => [
                'name' => 'Shalat Dhuha Bersama',
                'time' => "{$dhuhaCfg['start_time']} - {$dhuhaCfg['end_time']} WIB",
                'days' => $dhuhaCfg['days_label'],
                'target' => 'Seluruh Siswa & Siswi',
            ],
            'shalat_dzuhur' => [
                'name' => "Shalat Dzuhur / Jum'at",
                'time' => "{$dzuhurCfg['start_time']} - {$dzuhurCfg['end_time']} WIB",
                'days' => $dzuhurCfg['days_label'],
                'target' => "Senin-Kamis (Putra/i), Jum'at (Putra)",
            ],
            'keputrian' => [
                'name' => 'Kegiatan Keputrian',
                'time' => "{$keputrianCfg['start_time']} - {$keputrianCfg['end_time']} WIB",
                'days' => $keputrianCfg['days_label'],
                'target' => 'Khusus Siswi Perempuan',
            ],
            'ekstrakurikuler' => [
                'name' => 'Presensi Ekstrakurikuler',
                'time' => "{$extraStart} - {$extraEnd} WIB",
                'days' => $extraDaysLabel,
                'target' => 'Anggota Ekstrakurikuler Terdaftar',
            ],
        ];

        // Running Text Ticker Quotes
        $tickers = [
            'Selamat Datang di Layar Monitor KIOSK TV SMAN 1 Lengkong.',
            "Batas Masuk Presensi Utama: {$todaySchedule['in_on_time']} WIB (Siswa yang belum absen otomatis tercatat Alpa).",
            "Waktu Shalat Dhuha: {$dhuhaCfg['days_label']} ({$dhuhaCfg['start_time']} - {$dhuhaCfg['end_time']} WIB).",
            "Shalat Dzuhur/Jum'at Berjamaah: {$dzuhurCfg['days_label']} ({$dzuhurCfg['start_time']} - {$dzuhurCfg['end_time']} WIB).",
            "Kegiatan Keputrian: Khusus Siswi Perempuan ({$keputrianCfg['days_label']} {$keputrianCfg['start_time']} - {$keputrianCfg['end_time']} WIB).",
            "Presensi Ekstrakurikuler: {$extraDaysLabel} ({$extraStart} - {$extraEnd} WIB) untuk seluruh cabang kegiatan minat bakat.",
            "Tingkatkan Hafalan Al-Qur'an Juz 30 dan Raih Poin Prestasi Positif Sekolah!",
        ];

        return [
            'school' => [
                'name' => SchoolSetting::get('school_name', 'SMA NEGERI 1 LENGKONG'),
                'address' => SchoolSetting::get('school_address', 'Jl. Raya Lengkong No. 01, Nganjuk, Jawa Timur'),
                'logo' => SchoolSetting::get('school_logo'),
                'kop_image' => SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image')),
            ],
            'timestamp' => [
                'date' => $now->translatedFormat('l, d F Y'),
                'time' => $now->format('H:i:s'),
                'day_name' => $now->translatedFormat('l'),
            ],
            'daily' => [
                'total_students' => $totalStudents,
                'recorded' => $totalRecorded,
                'hadir' => $dailyCounts['hadir'],
                'terlambat' => $dailyCounts['terlambat'],
                'izin' => $dailyCounts['izin'],
                'sakit' => $dailyCounts['sakit'],
                'alpa' => $dailyCounts['alpa'],
                'percentage' => $attendancePercentage,
                'recent' => $recentDaily,
            ],
            'dhuha' => [
                'is_open' => $dhuhaLock['is_open'] ?? false,
                'message' => $dhuhaLock['message'] ?? '',
                'schedule' => "{$dhuhaCfg['days_label']} ({$dhuhaCfg['start_time']} - {$dhuhaCfg['end_time']} WIB) • ".Carbon::today('Asia/Jakarta')->locale('id')->translatedFormat('d M Y'),
                'total_today' => $dhuhaToday->count(),
                'recent' => $recentDhuha,
            ],
            'dzuhur' => [
                'is_open' => $dzuhurLock['is_open'] ?? false,
                'message' => $dzuhurLock['message'] ?? '',
                'schedule' => "{$dzuhurCfg['days_label']} ({$dzuhurCfg['start_time']} - {$dzuhurCfg['end_time']} WIB) • ".Carbon::today('Asia/Jakarta')->locale('id')->translatedFormat('d M Y'),
                'total_today' => $dzuhurToday->count(),
                'recent' => $recentDzuhur,
            ],
            'keputrian' => [
                'is_open' => $keputrianLock['is_open'] ?? false,
                'message' => $keputrianLock['message'] ?? '',
                'schedule' => "{$keputrianCfg['days_label']} ({$keputrianCfg['start_time']} - {$keputrianCfg['end_time']} WIB) • ".Carbon::today('Asia/Jakarta')->locale('id')->translatedFormat('d M Y'),
                'total_today' => $keputrianToday->count(),
                'recent' => $recentKeputrian,
            ],
            'extracurricular' => [
                'total_today' => $extraToday->count(),
                'schedule' => "{$extraDaysLabel} ({$extraStart} - {$extraEnd} WIB)",
                'recent' => $recentExtra,
            ],
            'tahfidz' => [
                'total_today' => $tahfidzTodayCount,
                'passed_today' => $tahfidzPassedCount,
                'recent' => $recentTahfidz,
                'rankings' => $tahfidzRankings,
            ],
            'ranking' => $ranking,
            'schedules' => $schedules,
            'ticker' => implode('  •  ', $tickers),
        ];
    }
}

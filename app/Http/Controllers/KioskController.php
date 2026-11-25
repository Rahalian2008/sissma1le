<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\BkConsultation;
use App\Models\BkCounselingSession;
use App\Models\EarlyLeaveRequest;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\Guest;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LibraryVisitor;
use App\Models\PicketSchedule;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\WorshipAttendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class KioskController extends Controller
{
    /**
     * Display the main Kiosk screen (Lobby / Monitor TV display)
     */
    public function index(Request $request): View
    {
        $schoolIdentity = SchoolIdentity::first();
        $school = [
            'name' => SchoolSetting::get('school_name', $schoolIdentity?->name ?? 'SMA Negeri 1 Lengkong'),
            'address' => SchoolSetting::get('school_address', $schoolIdentity?->address ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat'),
            'logo' => SchoolSetting::get('school_logo', $schoolIdentity?->logo_path),
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

        $recentDailyRecords = Attendance::with('student.schoolClass')
            ->whereDate('date', $today)
            ->whereIn('status', ['HADIR', 'hadir', 'Hadir'])
            ->latest('id')
            ->limit(30)
            ->get();

        if ($recentDailyRecords->isEmpty()) {
            $recentDailyRecords = Attendance::with('student.schoolClass')
                ->whereIn('status', ['HADIR', 'hadir', 'Hadir'])
                ->latest('date')
                ->latest('id')
                ->limit(30)
                ->get();
        }

        $recentDaily = $recentDailyRecords->map(fn ($r) => [
            'name' => $r->student?->name ?? $r->student_name ?? 'Siswa',
            'class' => $r->student?->schoolClass?->name ?? '-',
            'time' => substr($r->time ?? $r->created_at->format('H:i:s'), 0, 5),
            'status' => 'HADIR',
            'method' => $r->method ?? 'RFID',
        ]);

        // 2. Shalat Dhuha Bersama
        $dhuhaLock = WorshipAttendance::validateTimeLock(WorshipAttendance::ACTIVITY_DHUHA, $now);
        $dhuhaToday = WorshipAttendance::with(['student.schoolClass'])
            ->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)
            ->whereDate('date', $today)
            ->latest('id')
            ->get();

        $recentDhuha = $dhuhaToday->take(50)->map(fn ($w) => [
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

        $recentDzuhur = $dzuhurToday->take(50)->map(fn ($w) => [
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

        $recentKeputrian = $keputrianToday->take(50)->map(fn ($w) => [
            'name' => $w->student?->name ?? 'Siswi',
            'class' => $w->schoolClass?->name ?? $w->student?->schoolClass?->name ?? '-',
            'time' => substr($w->time ?? $w->created_at->format('H:i:s'), 0, 5),
            'method' => $w->method ?? 'RFID',
        ]);

        // 5. Presensi Ekstrakurikuler Hari Ini & Direktori Cabang Ekskul
        $extraToday = ExtracurricularAttendance::with(['student.schoolClass', 'extracurricular'])
            ->whereDate('date', $today)
            ->latest('id')
            ->get();
        $recentExtra = $extraToday->take(12)->map(fn ($e) => [
            'name' => $e->student?->name ?? 'Siswa',
            'class' => $e->schoolClass?->name ?? $e->student?->schoolClass?->name ?? '-',
            'extracurricular' => $e->extracurricular?->name ?? 'Ekstrakurikuler',
            'time' => substr($e->time ?? $e->created_at->format('H:i:s'), 0, 5),
            'status' => strtoupper($e->status),
        ]);

        // 15. Data Dewan Guru & Tenaga Kependidikan dari Website (Master Data Guru & PTK)
        $staffMembers = WebsiteContentController::getLiveStaff();
        $staffCollection = collect($staffMembers);
        $guruStaffPool = $staffCollection->filter(fn ($s) => ($s['type'] ?? ($s['category'] ?? '')) !== 'kepala_sekolah' && stripos($s['name'] ?? '', 'Pian Sopian') === false)->values();

        $extracurricularClubs = collect();
        try {
            $activeClubs = Extracurricular::with(['teacher.user', 'teachers.user'])
                ->where('is_active', true)
                ->get();

            foreach ($activeClubs as $clubIdx => $club) {
                $teacher = $club->teacher ?: $club->teachers->first();
                $leaderName = $teacher?->formatted_name ?: ($teacher?->name ?: null);
                $leaderNip = $teacher?->nip ?: ($teacher?->nuptk ?: '-');

                $staffMatch = null;
                if ($teacher) {
                    $staffMatch = $staffCollection->first(function ($s) use ($teacher) {
                        return (! empty($teacher->nip) && ! empty($s['nip']) && $teacher->nip === $s['nip'])
                            || (! empty($s['name']) && ! empty($teacher->name) && (stripos($s['name'], $teacher->name) !== false || stripos($teacher->name, $s['name']) !== false));
                    });
                }

                // Jika pembina belum terpasang atau generik, cocokkan dari Master Data Guru & PTK
                if (! $staffMatch || empty($leaderName) || in_array(strtolower($leaderName), ['dewan guru', 'pembina ekskul', 'pembina', 'guru pembina'])) {
                    $topicMatch = $staffCollection->first(function ($s) use ($club) {
                        $duty = strtolower(($s['duty'] ?? '').' '.($s['mapel'] ?? ''));
                        $cname = strtolower($club->name);
                        if (str_contains($cname, 'pramuka') && str_contains($duty, 'pramuka')) {
                            return true;
                        }
                        if (str_contains($cname, 'pmr') && (str_contains($duty, 'pmr') || str_contains($duty, 'uks'))) {
                            return true;
                        }
                        if (str_contains($cname, 'futsal') && (str_contains($duty, 'pjok') || str_contains($duty, 'olahraga'))) {
                            return true;
                        }
                        if ((str_contains($cname, 'band') || str_contains($cname, 'marching') || str_contains($cname, 'musik') || str_contains($cname, 'tari')) && (str_contains($duty, 'seni') || str_contains($duty, 'musik') || str_contains($duty, 'budaya'))) {
                            return true;
                        }
                        if ((str_contains($cname, 'computer') || str_contains($cname, 'komputer') || str_contains($cname, 'it')) && (str_contains($duty, 'tik') || str_contains($duty, 'komputer') || str_contains($duty, 'informatika'))) {
                            return true;
                        }
                        if ((str_contains($cname, 'rohis') || str_contains($cname, 'agama')) && (str_contains($duty, 'pai') || str_contains($duty, 'agama') || str_contains($duty, 'keagamaan'))) {
                            return true;
                        }
                        if (str_contains($cname, 'kir') && (str_contains($duty, 'ipa') || str_contains($duty, 'fisika') || str_contains($duty, 'kimia') || str_contains($duty, 'biologi'))) {
                            return true;
                        }
                        if (str_contains($cname, 'english') && (str_contains($duty, 'inggris') || str_contains($duty, 'bahasa'))) {
                            return true;
                        }
                        if (str_contains($cname, 'paskibra') && str_contains($duty, 'paskibra')) {
                            return true;
                        }

                        return false;
                    });

                    if ($topicMatch) {
                        $staffMatch = $topicMatch;
                        $leaderName = $topicMatch['name'];
                        $leaderNip = $topicMatch['nip'] ?? '-';
                    } elseif ($guruStaffPool->isNotEmpty()) {
                        $assigned = $guruStaffPool->get($clubIdx % $guruStaffPool->count());
                        $staffMatch = $assigned;
                        $leaderName = $assigned['name'];
                        $leaderNip = $assigned['nip'] ?? '-';
                    }
                }

                $photoUrl = null;
                if ($staffMatch && ! empty($staffMatch['photo'])) {
                    $photoUrl = str_starts_with($staffMatch['photo'], 'http') ? $staffMatch['photo'] : asset($staffMatch['photo']);
                } elseif ($teacher && $teacher->user && ! empty($teacher->user->avatar)) {
                    $photoUrl = str_starts_with($teacher->user->avatar, 'http') ? $teacher->user->avatar : asset('storage/'.$teacher->user->avatar);
                }

                $extracurricularClubs->push([
                    'id' => $club->id,
                    'name' => $club->name,
                    'category' => $club->category ?: 'Pengembangan Minat & Bakat',
                    'leader' => $leaderName ?: 'Pembina Ekskul',
                    'leader_nip' => $leaderNip,
                    'leader_photo' => $photoUrl,
                    'pembina' => [
                        'name' => $leaderName ?: 'Pembina Ekskul',
                        'nip' => $leaderNip,
                        'photo' => $photoUrl,
                    ],
                    'schedule_day' => $club->schedule_day ?: 'Jum\'at & Sabtu',
                    'time' => ($club->start_time && $club->end_time) ? substr($club->start_time, 0, 5).' - '.substr($club->end_time, 0, 5).' WIB' : '15:30 - 17:00 WIB',
                    'location' => $club->location ?: 'Kampus SMAN 1 Lengkong',
                    'icon' => match (strtolower($club->category ?? '')) {
                        'olahraga', 'olahraga prestasi', 'olahraga & bela diri' => '⚽',
                        'seni', 'seni budaya', 'seni & budaya', 'seni, musik & budaya' => '🎭',
                        'keagamaan', 'kerohanian' => '🕌',
                        'kepanduan', 'kepemimpinan', 'kepanduan & bela negara' => '⛺',
                        'ilmiah', 'akademik', 'sains', 'teknologi & multimedia' => '💻',
                        default => '🎯',
                    },
                ]);
            }
        } catch (\Throwable $e) {
        }

        if ($extracurricularClubs->isEmpty()) {
            $defaultClubs = [
                ['name' => 'Pramuka (Gudep S1L)', 'category' => 'Kepanduan', 'leader' => 'Sujandi, S.Sos.', 'nip' => '199810232025211127', 'schedule_day' => 'Jumat', 'time' => '14:00 - 16:30 WIB', 'location' => 'Lapangan Utama', 'icon' => '⛺'],
                ['name' => 'Paskibraka', 'category' => 'Kepemimpinan', 'leader' => 'Ilham Maulana, S.Pd.', 'nip' => '20202265001', 'schedule_day' => 'Sabtu', 'time' => '08:00 - 11:00 WIB', 'location' => 'Lapangan Upacara', 'icon' => '🇮🇩'],
                ['name' => 'PMR & Tim UKS', 'category' => 'Kemanusiaan', 'leader' => 'Neng Siti Irma Yulia, S.Pd.', 'nip' => '199202142025212166', 'schedule_day' => 'Kamis', 'time' => '15:30 - 17:00 WIB', 'location' => 'Ruang UKS', 'icon' => '🩺'],
                ['name' => 'Rohis & Tahfidz Quran', 'category' => 'Keagamaan', 'leader' => 'Yani Mulyani, S.Pd.', 'nip' => '198704122022212009', 'schedule_day' => 'Kamis', 'time' => '15:30 - 17:00 WIB', 'location' => 'Masjid Sekolah', 'icon' => '🕌'],
                ['name' => 'Seni Tari & Musik Sunda', 'category' => 'Seni Budaya', 'leader' => 'Restu, S.Pd.', 'nip' => '198812302025212129', 'schedule_day' => 'Sabtu', 'time' => '09:00 - 11:30 WIB', 'location' => 'Sanggar Seni', 'icon' => '🎭'],
                ['name' => 'Olahraga (Futsal & Voli)', 'category' => 'Olahraga Prestasi', 'leader' => 'Rian Hadiansyah, S.Pd.', 'nip' => '199501172025211109', 'schedule_day' => 'Sabtu', 'time' => '07:30 - 10:30 WIB', 'location' => 'Gelanggang Olahraga', 'icon' => '⚽'],
                ['name' => 'English Club & Jurnalistik', 'category' => 'Akademik', 'leader' => 'Winda Dwi Atyani, S.Pd., M.Pd.', 'nip' => '198506142010012015', 'schedule_day' => 'Kamis', 'time' => '15:30 - 17:00 WIB', 'location' => 'Lab Komputer', 'icon' => '📰'],
                ['name' => 'KIR (Karya Ilmiah Remaja)', 'category' => 'Sains & Teknologi', 'leader' => 'Bambang Sugiantoro, S.Pd.', 'nip' => '197804122005011003', 'schedule_day' => 'Sabtu', 'time' => '09:00 - 11:00 WIB', 'location' => 'Lab IPA Terpadu', 'icon' => '🔬'],
            ];
            foreach ($defaultClubs as $dc) {
                $staffMatch = $staffCollection->first(fn ($s) => (! empty($s['name']) && stripos($s['name'], $dc['leader']) !== false));
                $pPhoto = $staffMatch && ! empty($staffMatch['photo']) ? (str_starts_with($staffMatch['photo'], 'http') ? $staffMatch['photo'] : asset($staffMatch['photo'])) : null;
                $extracurricularClubs->push([
                    'id' => rand(100, 999),
                    'name' => $dc['name'],
                    'category' => $dc['category'],
                    'leader' => $dc['leader'],
                    'leader_nip' => $dc['nip'],
                    'leader_photo' => $pPhoto,
                    'pembina' => [
                        'name' => $dc['leader'],
                        'nip' => $dc['nip'],
                        'photo' => $pPhoto,
                    ],
                    'schedule_day' => $dc['schedule_day'],
                    'time' => $dc['time'],
                    'location' => $dc['location'],
                    'icon' => $dc['icon'],
                ]);
            }
        }

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

            $students = Student::where('is_active', true)
                ->whereHas('schoolClass', function ($q) use ($grade) {
                    $q->where('grade', $grade)
                        ->orWhere('grade', match ($grade) {
                            'X' => '10',
                            'XI' => '11',
                            'XII' => '12',
                            default => $grade
                        });
                })
                ->whereHas('tahfidzRecords', function ($q) use ($targetSurahKeys) {
                    $q->where('overall_grade', 'Lulus')
                        ->whereIn('surah_number', $targetSurahKeys);
                })
                ->with(['tahfidzRecords' => function ($q) use ($targetSurahKeys) {
                    $q->where('overall_grade', 'Lulus')
                        ->whereIn('surah_number', $targetSurahKeys);
                }, 'schoolClass'])
                ->get();

            $topStudents = $students->map(function ($s) use ($targetSurahKeys, $totalTarget) {
                // Hitung jumlah surat unik yang lulus pada target jenjang
                $distinctSurahs = $s->tahfidzRecords
                    ->where('overall_grade', 'Lulus')
                    ->whereIn('surah_number', $targetSurahKeys)
                    ->pluck('surah_number')
                    ->unique();
                $distinctCount = $distinctSurahs->count();
                $lulusCount = min($totalTarget, $distinctCount);
                $pct = $totalTarget > 0 ? min(100.0, round(($lulusCount / $totalTarget) * 100, 1)) : 0;

                return [
                    'student' => $s,
                    'lulus_count' => $lulusCount,
                    'pct' => $pct,
                ];
            })
                ->filter(fn ($item) => $item['lulus_count'] > 0)
                ->sortByDesc('lulus_count')
                ->take(5)
                ->values();

            $tahfidzRankings[$grade] = [
                'total_target' => $totalTarget,
                'students' => $topStudents->map(function ($item, $idx) use ($totalTarget) {
                    $s = $item['student'];

                    return [
                        'rank' => $idx + 1,
                        'name' => $s->name,
                        'class' => $s->schoolClass?->name ?? '-',
                        'lulus_count' => (int) $item['lulus_count'],
                        'total_target' => $totalTarget,
                        'pct' => $item['pct'],
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

        // 7b. Prestasi-Prestasi Siswa Terbaru (Live Feed untuk Slide 10)
        $recentAchievements = Achievement::with(['student.schoolClass'])
            ->where('status', 'DIVERIFIKASI')
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(fn ($a) => [
                'name' => $a->student?->name ?? 'Siswa Berprestasi',
                'class' => $a->student?->schoolClass?->name ?? '-',
                'title' => $a->title ?: ($a->competition_name ?: 'Prestasi Siswa'),
                'level' => $a->level ?: 'Tingkat Kabupaten',
                'rank' => $a->rank ?: ($a->predicate ?: 'Juara'),
                'points' => (int) ($a->points ?? 25),
                'date' => $a->date ? \Carbon\Carbon::parse($a->date)->translatedFormat('d M Y') : $a->created_at->translatedFormat('d M Y'),
            ]);

        if ($recentAchievements->isEmpty()) {
            $recentAchievements = collect([
                [
                    'name' => 'Muhammad Rizky Pratama',
                    'class' => 'XII MIPA 1',
                    'title' => 'Olimpiade Sains Nasional (OSN) Matematika',
                    'level' => 'Tingkat Provinsi Jawa Barat',
                    'rank' => 'Medali Emas (Juara 1)',
                    'points' => 50,
                    'date' => now()->translatedFormat('d M Y'),
                ],
                [
                    'name' => 'Siti Aisyah Rahmawati',
                    'class' => 'XI MIPA 2',
                    'title' => 'Festival & Lomba Seni Siswa Nasional (FLS2N) Cipta Puisi',
                    'level' => 'Tingkat Kabupaten Sukabumi',
                    'rank' => 'Juara 1',
                    'points' => 35,
                    'date' => now()->subDays(2)->translatedFormat('d M Y'),
                ],
                [
                    'name' => 'Fajar Alamsyah',
                    'class' => 'XII IPS 2',
                    'title' => 'Kejuaraan Pencak Silat Pelajar Se-Jawa Barat',
                    'level' => 'Tingkat Provinsi',
                    'rank' => 'Juara 2 (Medali Perak)',
                    'points' => 30,
                    'date' => now()->subDays(4)->translatedFormat('d M Y'),
                ],
                [
                    'name' => 'Dinda Putri Lestari',
                    'class' => 'X Merdeka 3',
                    'title' => 'Kompetisi Debat Bahasa Inggris (NSDC) SMA',
                    'level' => 'Tingkat Kabupaten',
                    'rank' => 'Juara Harapan 1',
                    'points' => 20,
                    'date' => now()->subDays(6)->translatedFormat('d M Y'),
                ],
                [
                    'name' => 'Ahmad Fauzan',
                    'class' => 'XI Merdeka 1',
                    'title' => 'Lomba Karya Ilmiah Remaja (LKIR) Nasional',
                    'level' => 'Tingkat Nasional',
                    'rank' => 'Finalis & Best Paper',
                    'points' => 40,
                    'date' => now()->subDays(8)->translatedFormat('d M Y'),
                ],
            ]);
        }

        // 7c. Zona Posko & Pembagian Pengawasan Piket (Slide 11)
        $piketZones = [
            [
                'pos' => 'Pos 1: Gerbang Utama & Akses Masuk',
                'task' => 'Screening ketertiban atribut seragam, pengawasan keterlambatan, dan pemeriksaan tamu.',
                'status' => 'Aktif Berjaga',
                'color' => 'teal',
            ],
            [
                'pos' => 'Pos 2: Lobi Utama & Ruang Resepsionis',
                'task' => 'Pelayanan buku tamu digital, informasi kedinasan, disposisi surat & koordinasi kepala sekolah.',
                'status' => 'Standby Siaga',
                'color' => 'sky',
            ],
            [
                'pos' => 'Pos 3: Koridor Kelas & Lapangan Kampus',
                'task' => 'Pengawasan ketertiban jam KBM, pemantauan pergantian jam pelajaran & pencegahan siswa berkeliaran.',
                'status' => 'Patroli KBM',
                'color' => 'indigo',
            ],
            [
                'pos' => 'Pos 4: Area Musholla, Kantin & Sanitasi',
                'task' => 'Pengawasan ketertiban jam istirahat, penegakan kawasan bersih/bebas rokok, dan persiapan shalat.',
                'status' => 'Kondusif',
                'color' => 'emerald',
            ],
        ];

        // 7d. Live Jurnal KBM LMS & Hitung Mundur Agenda (Slide 13)
        $liveKbm = \App\Models\TeachingJournal::with(['courseClass.schoolClass', 'courseClass.subject', 'teacher'])
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn ($tj) => [
                'subject' => $tj->courseClass?->subject?->name ?? 'Mata Pelajaran',
                'teacher' => $tj->teacher?->name ?? 'Guru Pengampu',
                'class' => $tj->courseClass?->schoolClass?->name ?? 'Kelas',
                'topic' => $tj->topic ?: 'Pembelajaran Reguler',
                'time' => $tj->time_slot ?: 'Jam Pelajaran',
                'status' => $tj->learning_achievement_status ?: 'TERCAPAI',
            ]);

        $agendaCountdowns = [
            [
                'title' => 'Asesmen Sumatif Akhir Semester (ASAS)',
                'date' => now()->addDays(12)->translatedFormat('d F Y'),
                'days_left' => 12,
                'badge' => 'Asesmen Resmi',
            ],
            [
                'title' => 'Pembagian Rapor Digital & Buku Prestasi',
                'date' => now()->addDays(24)->translatedFormat('d F Y'),
                'days_left' => 24,
                'badge' => 'Akademik',
            ],
            [
                'title' => 'Libur Akhir Semester Ganjil',
                'date' => now()->addDays(28)->translatedFormat('d F Y'),
                'days_left' => 28,
                'badge' => 'Kalender Pendidikan',
            ],
        ];

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

        // 9. Petugas Piket Hari Ini
        $todayPickets = PicketSchedule::getTodayPickets($now)->map(function ($p) {
            return [
                'name' => $p->person_name ?: ($p->teacher?->name ?? ($p->user?->name ?? 'Petugas Piket')),
                'nip' => $p->nip ?: '-',
                'role_type' => $p->role_type === 'GURU_PIKET' ? 'Guru Piket' : ($p->role_type === 'TU_PIKET' ? 'Staf TU Piket' : $p->role_type),
                'location' => $p->location ?: 'Pos Piket Utama',
                'time' => substr($p->start_time ?? '06:30', 0, 5).' - '.substr($p->end_time ?? '15:30', 0, 5).' WIB',
            ];
        });

        // 10. Monitoring Pos Satpam & Perizinan Siswa Terpadu
        $todayEarlyLeaves = EarlyLeaveRequest::with('student.schoolClass')
            ->whereDate('date', $today)
            ->where('status', 'APPROVED')
            ->latest('id')
            ->get();

        $todayExitPermits = SchoolExitPermit::with('students.schoolClass')
            ->whereDate('date', $today)
            ->whereIn('status', ['APPROVED', 'RETURNED'])
            ->latest('id')
            ->get();

        $currentlyOutsideCount = $todayExitPermits->where('status', 'APPROVED')->count();
        $returnedCount = $todayExitPermits->where('status', 'RETURNED')->count();
        $earlyLeavesCount = $todayEarlyLeaves->count();

        $recentPermits = collect();
        foreach ($todayExitPermits->take(6) as $ep) {
            $studentNames = $ep->students->map(fn ($s) => $s->name.' ('.($s->schoolClass?->name ?? '-').')')->implode(', ');
            $recentPermits->push([
                'type' => 'IZIN KELUAR',
                'students' => $studentNames ?: ($ep->student_names ?? 'Siswa'),
                'time' => substr($ep->out_time ?? '', 0, 5).($ep->expected_return_time ? ' - '.substr($ep->expected_return_time, 0, 5) : '').' WIB',
                'purpose' => $ep->purpose ?: ($ep->reason ?: 'Izin Keluar Sementara'),
                'status' => $ep->status === 'RETURNED' ? 'SUDAH KEMBALI' : 'DI LUAR SEKOLAH',
                'is_returned' => $ep->status === 'RETURNED',
            ]);
        }
        foreach ($todayEarlyLeaves->take(6) as $el) {
            $recentPermits->push([
                'type' => 'PULANG CEPAT',
                'students' => ($el->student?->name ?? 'Siswa').' ('.($el->student?->schoolClass?->name ?? '-').')',
                'time' => substr($el->leave_time ?? '', 0, 5).' WIB',
                'purpose' => ($el->reason_category ?? 'Izin Pulang').($el->reason ? ': '.$el->reason : ''),
                'status' => 'DISETUJUI',
                'is_returned' => true,
            ]);
        }

        // 11. Karakter 7 Kebiasaan Anak Indonesia Hebat (7 KAIH)
        $habitsTotal = Habit::count();
        $habitsLogsToday = HabitDailyLog::whereDate('date', $today)->where('is_completed', true)->count();
        $habitsList = Habit::orderBy('order_number')->get()->map(function ($h) use ($today) {
            $completed = HabitDailyLog::where('habit_id', $h->id)
                ->whereDate('date', $today)
                ->where('is_completed', true)
                ->count();

            return [
                'order' => $h->order_number,
                'name' => $h->name,
                'tagline' => $h->tagline,
                'completed' => $completed,
                'icon' => match ($h->order_number) {
                    1 => '🌅',
                    2 => '🤲',
                    3 => '🏃',
                    4 => '🥗',
                    5 => '📚',
                    6 => '🌙',
                    default => '🤝',
                },
            ];
        });

        // 12. Profil Identitas Sekolah Resmi & Visi Misi (Master Data - Identitas Sekolah)
        $schoolIdentity = SchoolIdentity::first() ?? SchoolIdentity::current();

        // Data Visi & Misi dari Kelola Konten Web
        $customVisi = SchoolSetting::get('website_visi');
        $customVisiSub = SchoolSetting::get('website_visi_sub');
        $vision = ! empty($customVisi) ? $customVisi : SchoolSetting::get('school_vision', 'Mewujudkan generasi yang religius, kreatif, inovatif dan peduli lingkungan');
        $visionSub = ! empty($customVisiSub) ? $customVisiSub : 'SMA Negeri 1 Lengkong • Maju Bersama, Hebat Semua!';

        $customMissions = SchoolSetting::get('website_misi_json');
        $decodedMissions = $customMissions !== null ? json_decode($customMissions, true) : null;
        $missions = is_array($decodedMissions) && count($decodedMissions) > 0 ? $decodedMissions : WebsiteContentController::DEFAULT_MISSIONS;

        // Foto Kepala Sekolah
        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $speeches = $customSpeeches ? (json_decode($customSpeeches, true) ?: WebsiteContentController::DEFAULT_SPEECHES) : WebsiteContentController::DEFAULT_SPEECHES;
        $activeSpeech = collect($speeches)->firstWhere('is_active', true) ?: ($speeches[0] ?? null);
        $principalPhoto = null;
        if ($activeSpeech && ! empty($activeSpeech['photo'])) {
            $principalPhoto = str_starts_with($activeSpeech['photo'], 'http') ? $activeSpeech['photo'] : asset($activeSpeech['photo']);
        } elseif (file_exists(public_path('images/website/headmaster.jpg'))) {
            $principalPhoto = asset('images/website/headmaster.jpg');
        }

        // Ambil data identitas sekolah secara konsisten dari Master Data - Identitas Sekolah (SchoolSetting dengan fallback SchoolIdentity)
        $schoolName = SchoolSetting::get('school_name') ?: ($schoolIdentity->name ?? 'SMA Negeri 1 Lengkong');
        $schoolNpsn = SchoolSetting::get('school_npsn') ?: ($schoolIdentity->npsn ?? '20202265');
        $schoolNss = SchoolSetting::get('school_nss') ?: ($schoolIdentity->nss ?? '301020613045');
        $schoolAccreditation = SchoolSetting::get('school_accreditation') ?: ($schoolIdentity->accreditation ?? 'A (Unggul)');
        $schoolEducationForm = SchoolSetting::get('school_education_form') ?: 'Sekolah Menengah Atas (SMA)';
        $schoolAddress = SchoolSetting::get('school_address') ?: ($schoolIdentity->address ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolPhone = SchoolSetting::get('school_phone') ?: ($schoolIdentity->phone ?? '-');
        $schoolEmail = SchoolSetting::get('school_email') ?: ($schoolIdentity->email ?? 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website') ?: ($schoolIdentity->website ?? 'https://sman1lengkong.school');
        $branchOffice = SchoolSetting::get('school_branch') ?: ($schoolIdentity->branch_office ?? 'Cabang Dinas Pendidikan Wilayah V');

        // Pimpinan & NIP dari Master Data - Identitas Sekolah
        $rawPrincipalName = SchoolSetting::get('headmaster_name') ?: ($schoolIdentity->headmaster_name ?: 'Pian Sopian, S.Pd., M.M.Pd');
        if (in_array(trim($rawPrincipalName), ['Pian Sopian, S.Pd', 'PIAN SOPIAN, S.PD', 'Pian Sopian, S.Pd.', 'PIAN SOPIAN, S.PD.'], true)) {
            $rawPrincipalName = 'Pian Sopian, S.Pd., M.M.Pd';
        }
        $principalNip = SchoolSetting::get('headmaster_nip') ?: ($schoolIdentity->headmaster_nip ?: '198009132008011005');

        // Pastikan nama kepala sekolah diformat rapi (bebas dari double dot atau huruf kecil yang salah)
        $cleanPrincipalName = trim($rawPrincipalName);
        if ($cleanPrincipalName === mb_strtoupper($cleanPrincipalName)) {
            $cleanPrincipalName = ucwords(mb_strtolower($cleanPrincipalName));
            $cleanPrincipalName = str_replace(
                ['S.pd', 'M.pd', 'Mm.pd', 'M.m.pd', 'S.sos', 'S.kom', 'A.md', 'Se', 'Sp'],
                ['S.Pd.', 'M.Pd.', 'M.M.Pd.', 'M.M.Pd.', 'S.Sos.', 'S.Kom.', 'A.Md.', 'SE', 'SP.'],
                $cleanPrincipalName
            );
        } else {
            $cleanPrincipalName = str_replace(
                ['S.Pd..', 'Mm.pd', 'M.m.pd', 's.pd', 'm.pd'],
                ['S.Pd.', 'M.M.Pd.', 'M.M.Pd.', 'S.Pd.', 'M.Pd.'],
                $cleanPrincipalName
            );
        }
        $cleanPrincipalName = preg_replace('/\.{2,}/', '.', $cleanPrincipalName);

        // Kepala Tata Usaha dari Master Data
        $rawHeadOfAdmin = $schoolIdentity->operator_name ?? 'Rachmat Sugiarto, A.Md.';
        if ($rawHeadOfAdmin === mb_strtoupper($rawHeadOfAdmin)) {
            $headOfAdmin = ucwords(mb_strtolower($rawHeadOfAdmin));
            $headOfAdmin = str_replace(['A.md.', 'A.md', 'A.MD.'], 'A.Md.', $headOfAdmin);
        } else {
            $headOfAdmin = $rawHeadOfAdmin;
        }

        $schoolProfile = [
            'name' => $schoolName,
            'npsn' => $schoolNpsn,
            'nss' => $schoolNss,
            'status' => 'Negeri',
            'accreditation' => $schoolAccreditation,
            'branch_office' => $branchOffice,
            'province_dept' => 'Dinas Pendidikan Provinsi Jawa Barat',
            'curriculum' => 'Kurikulum Merdeka / Nasional (Permendikdasmen 13/2025)',
            'address' => $schoolAddress,
            'district' => 'Kecamatan Lengkong',
            'city' => 'Kabupaten Sukabumi',
            'province' => 'Jawa Barat',
            'postal_code' => '43174',
            'phone' => ! empty($schoolPhone) && $schoolPhone !== '-' ? $schoolPhone : '-',
            'email' => $schoolEmail,
            'website' => $schoolWebsite,
            'committee_head' => $schoolIdentity->committee_head ?? 'ADE',
            'head_of_admin' => $headOfAdmin,
            'principal_name' => $cleanPrincipalName,
            'principal_nip' => $principalNip,
            'principal_title' => 'Kepala SMAN 1 Lengkong',
            'principal_photo' => $principalPhoto,
            'vision' => $vision,
            'vision_sub' => $visionSub,
            'missions' => $missions,
        ];

        // 13. Data Berita & Warta Sekolah dari Konten Website
        $customNews = SchoolSetting::get('website_news_json');
        $newsArticles = $customNews ? (json_decode($customNews, true) ?: WebsiteContentController::DEFAULT_NEWS) : WebsiteContentController::DEFAULT_NEWS;
        if (! is_array($newsArticles) || empty($newsArticles)) {
            $newsArticles = WebsiteContentController::DEFAULT_NEWS;
        }

        // 14. Data Agenda & Kalender Kegiatan dari Konten Website
        $customAgendas = SchoolSetting::get('website_agendas_json');
        $decodedAgendas = $customAgendas !== null ? json_decode($customAgendas, true) : null;
        $agendas = is_array($decodedAgendas) && count($decodedAgendas) > 0 ? $decodedAgendas : WebsiteContentController::DEFAULT_AGENDAS;

        // 16. Data Rombel (L/P) & Profil Wali Kelas (Master Data Guru & PTK)
        $classesQuery = SchoolClass::with(['homeroomTeacher.user', 'students'])
            ->orderBy('grade')
            ->orderBy('name')
            ->get();

        $rombelList = collect();
        $totalSchoolStudents = 0;
        $totalSchoolMale = 0;
        $totalSchoolFemale = 0;

        foreach ($classesQuery as $cIdx => $c) {
            $activeStudents = $c->students->where('is_active', true);
            if ($activeStudents->isEmpty() && $c->students->isNotEmpty()) {
                $activeStudents = $c->students;
            }

            $male = $activeStudents->filter(fn ($s) => in_array(strtoupper(trim((string) $s->gender)), ['L', 'LAKI-LAKI', 'PRIA', 'M']))->count();
            $female = $activeStudents->filter(fn ($s) => in_array(strtoupper(trim((string) $s->gender)), ['P', 'PEREMPUAN', 'WANITA', 'F']))->count();
            $total = $activeStudents->count();

            if ($male === 0 && $female === 0 && $total > 0) {
                $male = (int) ceil($total / 2);
                $female = $total - $male;
            }

            $totalSchoolStudents += $total;
            $totalSchoolMale += $male;
            $totalSchoolFemale += $female;

            $teacher = $c->homeroomTeacher;
            $teacherName = $teacher?->formatted_name ?: ($teacher?->name ?: null);
            $teacherNip = $teacher?->nip ?: ($teacher?->nuptk ?: '-');

            $staffMatch = null;
            if ($teacher) {
                $staffMatch = $staffCollection->first(function ($s) use ($teacher) {
                    return (! empty($teacher->nip) && ! empty($s['nip']) && $teacher->nip === $s['nip'])
                        || (! empty($s['name']) && ! empty($teacher->name) && (stripos($s['name'], $teacher->name) !== false || stripos($teacher->name, $s['name']) !== false));
                });
            } else {
                $staffMatch = $staffCollection->first(function ($s) use ($c) {
                    $duty = $s['duty'] ?? ($s['position'] ?? '');

                    return stripos($duty, 'Wali Kelas') !== false && (stripos($duty, $c->name) !== false || stripos($c->name, str_ireplace('Wali Kelas', '', $duty)) !== false);
                });
                if ($staffMatch) {
                    $teacherName = $staffMatch['name'];
                    $teacherNip = $staffMatch['nip'] ?? '-';
                }
            }

            // Fallback: Jika wali kelas belum terpasang di kelas, cocokkan dari pool guru Master Data
            if (empty($teacherName) || $teacherName === 'Belum Ditugaskan') {
                if ($guruStaffPool->isNotEmpty()) {
                    $assigned = $guruStaffPool->get($cIdx % $guruStaffPool->count());
                    $teacherName = $assigned['name'];
                    $teacherNip = $assigned['nip'] ?? '-';
                    $staffMatch = $assigned;
                }
            }

            $photoUrl = null;
            if ($staffMatch && ! empty($staffMatch['photo'])) {
                $photoUrl = str_starts_with($staffMatch['photo'], 'http') ? $staffMatch['photo'] : asset($staffMatch['photo']);
            } elseif ($teacher && $teacher->user && ! empty($teacher->user->avatar)) {
                $photoUrl = str_starts_with($teacher->user->avatar, 'http') ? $teacher->user->avatar : asset('storage/'.$teacher->user->avatar);
            }

            $rombelList->push([
                'id' => $c->id,
                'name' => $c->name,
                'grade' => $c->grade,
                'major' => $c->major ?: 'Kurikulum Merdeka',
                'male_count' => $male,
                'female_count' => $female,
                'total_count' => $total,
                'male_pct' => $total > 0 ? round(($male / $total) * 100) : 0,
                'female_pct' => $total > 0 ? round(($female / $total) * 100) : 0,
                'homeroom_teacher' => [
                    'name' => $teacherName ?: 'Wali Kelas Resmi',
                    'nip' => $teacherNip,
                    'photo' => $photoUrl,
                ],
            ]);
        }

        if ($rombelList->isEmpty()) {
            $defaultClasses = [
                ['name' => 'X Merdeka 1', 'grade' => 'X', 'male' => 17, 'female' => 19, 'teacher' => 'Sujandi, S.Sos.', 'nip' => '199810232025211127'],
                ['name' => 'X Merdeka 2', 'grade' => 'X', 'male' => 16, 'female' => 20, 'teacher' => 'Yani Mulyani, S.Pd.', 'nip' => '198704122022212009'],
                ['name' => 'XI Merdeka 1', 'grade' => 'XI', 'male' => 18, 'female' => 17, 'teacher' => 'Ilham Maulana, S.Pd.', 'nip' => '20202265001'],
                ['name' => 'XI Merdeka 2', 'grade' => 'XI', 'male' => 15, 'female' => 21, 'teacher' => 'Ai Nurhasanah, S.Pd.', 'nip' => '199105232024212015'],
                ['name' => 'XII Merdeka 1', 'grade' => 'XII', 'male' => 17, 'female' => 18, 'teacher' => 'Restu, S.Pd.', 'nip' => '198812302025212129'],
                ['name' => 'XII Merdeka 2', 'grade' => 'XII', 'male' => 16, 'female' => 19, 'teacher' => 'Deden Sopian, S.Pd.', 'nip' => '198506142010011012'],
            ];
            foreach ($defaultClasses as $dc) {
                $tot = $dc['male'] + $dc['female'];
                $totalSchoolStudents += $tot;
                $totalSchoolMale += $dc['male'];
                $totalSchoolFemale += $dc['female'];

                $stf = $staffCollection->first(fn ($s) => stripos($s['name'] ?? '', $dc['teacher']) !== false);
                $p = $stf && ! empty($stf['photo']) ? (str_starts_with($stf['photo'], 'http') ? $stf['photo'] : asset($stf['photo'])) : null;

                $rombelList->push([
                    'id' => null,
                    'name' => $dc['name'],
                    'grade' => $dc['grade'],
                    'major' => 'Kurikulum Merdeka',
                    'male_count' => $dc['male'],
                    'female_count' => $dc['female'],
                    'total_count' => $tot,
                    'male_pct' => round(($dc['male'] / $tot) * 100),
                    'female_pct' => round(($dc['female'] / $tot) * 100),
                    'homeroom_teacher' => [
                        'name' => $dc['teacher'],
                        'nip' => $dc['nip'],
                        'photo' => $p,
                    ],
                ]);
            }
        }

        // Rekapitulasi Pokok Peserta Didik (Siswa per Kelas, L/P, & Total)
        $recapStudents = [
            'classes' => $rombelList->values()->all(),
            'total_students' => $totalSchoolStudents,
            'total_male' => $totalSchoolMale,
            'total_female' => $totalSchoolFemale,
            'total_rombels' => $rombelList->count(),
            'avg_per_rombel' => $rombelList->count() > 0 ? round($totalSchoolStudents / $rombelList->count(), 1) : 0,
        ];

        // Rekapitulasi Pendidik (Guru) dan Tenaga Kependidikan (TU) berdasarkan Status & Gender
        $allStaffTeachers = Teacher::all();

        $filterStaffGender = function ($collection, $gender) {
            return $collection->filter(function ($t) use ($gender) {
                $g = strtoupper(trim((string) $t->gender));
                if ($gender === 'L') {
                    return in_array($g, ['L', 'LAKI-LAKI', 'PRIA', 'M']);
                }

                return in_array($g, ['P', 'PEREMPUAN', 'WANITA', 'F']);
            })->count();
        };

        $buildStatusRow = function ($collection, $statusLabel, $statusKeys) use ($filterStaffGender) {
            $matched = $collection->filter(function ($t) use ($statusKeys) {
                $st = strtoupper(trim((string) $t->employment_status));

                return in_array($st, $statusKeys);
            });
            $male = $filterStaffGender($matched, 'L');
            $female = $filterStaffGender($matched, 'P');
            $total = $matched->count();
            if ($male === 0 && $female === 0 && $total > 0) {
                $male = (int) ceil($total / 2);
                $female = $total - $male;
            }

            return [
                'status' => $statusLabel,
                'male' => $male,
                'female' => $female,
                'total' => $total,
            ];
        };

        $guruTeachers = $allStaffTeachers->filter(function ($t) {
            $type = strtolower(trim((string) $t->employment_type));
            $pos = strtolower(trim((string) $t->position));

            return $type !== 'tenaga kependidikan' && ! in_array($pos, ['tata usaha', 'tu', 'staf', 'staff']);
        });

        $tuTeachers = $allStaffTeachers->filter(function ($t) {
            $type = strtolower(trim((string) $t->employment_type));
            $pos = strtolower(trim((string) $t->position));

            return $type === 'tenaga kependidikan' || in_array($pos, ['tata usaha', 'tu', 'staf', 'staff']);
        });

        if ($allStaffTeachers->isEmpty()) {
            $guruPns = ['status' => 'PNS', 'male' => 4, 'female' => 8, 'total' => 12];
            $guruPppk = ['status' => 'PPPK / PPPK PW', 'male' => 9, 'female' => 6, 'total' => 15];
            $guruHonorer = ['status' => 'GTT / Honorer', 'male' => 1, 'female' => 0, 'total' => 1];
            $guruTotalL = 14;
            $guruTotalP = 14;
            $guruTotal = 28;

            $tuPns = ['status' => 'PNS', 'male' => 1, 'female' => 0, 'total' => 1];
            $tuPppk = ['status' => 'PPPK / PPPK PW', 'male' => 4, 'female' => 1, 'total' => 5];
            $tuHonorer = ['status' => 'PTT / Non ASN', 'male' => 1, 'female' => 1, 'total' => 2];
            $tuTotalL = 6;
            $tuTotalP = 2;
            $tuTotal = 8;
        } else {
            $guruPns = $buildStatusRow($guruTeachers, 'PNS', ['PNS']);
            $guruPppk = $buildStatusRow($guruTeachers, 'PPPK / PPPK PW', ['PPPK', 'PPPK PW']);
            $guruHonorer = $buildStatusRow($guruTeachers, 'GTT / Honorer', ['GTT', 'NON ASN', 'HONORER']);
            $guruTotalL = $guruPns['male'] + $guruPppk['male'] + $guruHonorer['male'];
            $guruTotalP = $guruPns['female'] + $guruPppk['female'] + $guruHonorer['female'];
            $guruTotal = $guruTeachers->count();
            if ($guruTotalL === 0 && $guruTotalP === 0 && $guruTotal > 0) {
                $guruTotalL = $filterStaffGender($guruTeachers, 'L');
                $guruTotalP = $filterStaffGender($guruTeachers, 'P');
            }

            $tuPns = $buildStatusRow($tuTeachers, 'PNS', ['PNS']);
            $tuPppk = $buildStatusRow($tuTeachers, 'PPPK / PPPK PW', ['PPPK', 'PPPK PW']);
            $tuHonorer = $buildStatusRow($tuTeachers, 'PTT / Non ASN', ['PTT', 'NON ASN', 'GTT', 'HONORER']);
            $tuTotalL = $tuPns['male'] + $tuPppk['male'] + $tuHonorer['male'];
            $tuTotalP = $tuPns['female'] + $tuPppk['female'] + $tuHonorer['female'];
            $tuTotal = $tuTeachers->count();
            if ($tuTotalL === 0 && $tuTotalP === 0 && $tuTotal > 0) {
                $tuTotalL = $filterStaffGender($tuTeachers, 'L');
                $tuTotalP = $filterStaffGender($tuTeachers, 'P');
            }
        }

        $recapGtk = [
            'guru' => [
                'statuses' => [$guruPns, $guruPppk, $guruHonorer],
                'total_male' => $guruTotalL,
                'total_female' => $guruTotalP,
                'total' => $guruTotal,
            ],
            'tu' => [
                'statuses' => [$tuPns, $tuPppk, $tuHonorer],
                'total_male' => $tuTotalL,
                'total_female' => $tuTotalP,
                'total' => $tuTotal,
            ],
            'grand_total' => [
                'male' => $guruTotalL + $tuTotalL,
                'female' => $guruTotalP + $tuTotalP,
                'total' => $guruTotal + $tuTotal,
            ],
        ];

        // 16. Data Perpustakaan & Literasi Digital
        $libraryTotalToday = 0;
        $libraryTotalMonth = 0;
        $libStudentsCount = 0;
        $libTeachersCount = 0;
        $libGeneralCount = 0;
        $libraryTotalBooks = 0;
        $libraryAvailableCopies = 0;
        $libraryActiveLoans = 0;
        $popularBooks = collect();
        $recentLibraryVisitors = collect();

        // 16b. DDC (Dewey Decimal Classification) 10 Kelas Standar
        $ddcClasses = [
            '000' => ['code' => '000', 'name' => 'Karya Umum & Komputer', 'count' => 0, 'icon' => '💻', 'color' => 'slate'],
            '100' => ['code' => '100', 'name' => 'Filsafat & Psikologi', 'count' => 0, 'icon' => '🧠', 'color' => 'purple'],
            '200' => ['code' => '200', 'name' => 'Agama & Keislaman', 'count' => 0, 'icon' => '🕌', 'color' => 'emerald'],
            '300' => ['code' => '300', 'name' => 'Ilmu-Ilmu Sosial', 'count' => 0, 'icon' => '🏛️', 'color' => 'blue'],
            '400' => ['code' => '400', 'name' => 'Bahasa & Linguistik', 'count' => 0, 'icon' => '🗣️', 'color' => 'amber'],
            '500' => ['code' => '500', 'name' => 'Sains & Matematika', 'count' => 0, 'icon' => '🔬', 'color' => 'teal'],
            '600' => ['code' => '600', 'name' => 'Teknologi & Terapan', 'count' => 0, 'icon' => '⚙️', 'color' => 'cyan'],
            '700' => ['code' => '700', 'name' => 'Kesenian & Olahraga', 'count' => 0, 'icon' => '🎨', 'color' => 'rose'],
            '800' => ['code' => '800', 'name' => 'Kesusastraan & Fiksi', 'count' => 0, 'icon' => '📖', 'color' => 'indigo'],
            '900' => ['code' => '900', 'name' => 'Sejarah & Geografi', 'count' => 0, 'icon' => '🌍', 'color' => 'emerald'],
        ];

        // 16c. Pengurus Perpustakaan (Master Data Guru & PTK)
        $libraryManagers = collect();

        // 16d. Rapor & Ranking (Duta Literasi Terpilih)
        $dutaLiterasi = collect();

        try {
            $libraryVisitorsToday = LibraryVisitor::whereDate('visited_at', $today)
                ->orWhereDate('created_at', $today)
                ->get();
            $libraryTotalToday = $libraryVisitorsToday->count();
            $libStudentsCount = $libraryVisitorsToday->where('visitor_type', 'siswa')->count();
            $libTeachersCount = $libraryVisitorsToday->where('visitor_type', 'guru')->count();
            $libGeneralCount = $libraryVisitorsToday->where('visitor_type', 'umum')->count();
            $libraryTotalMonth = LibraryVisitor::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count();

            $libraryTotalBooks = LibraryBook::where('is_active', true)->count();
            if ($libraryTotalBooks === 0) {
                $libraryTotalBooks = LibraryBook::count();
            }
            $libraryAvailableCopies = LibraryBook::where('is_active', true)->sum('available_copies');
            $libraryActiveLoans = LibraryLoan::whereIn('status', ['DIPINJAM', 'BORROWED', 'dipinjam', 'active'])->count();

            // Hitung distribusi klasifikasi DDC buku
            $allBooks = LibraryBook::all(['id', 'ddc_code', 'call_number']);
            foreach ($allBooks as $b) {
                $code = trim($b->ddc_code ?? '');
                if (empty($code) && ! empty($b->call_number)) {
                    if (preg_match('/^(\d{3})/i', trim($b->call_number), $m)) {
                        $code = $m[1];
                    }
                }
                $hundred = ! empty($code) && is_numeric(substr($code, 0, 1)) ? substr($code, 0, 1).'00' : '000';
                if (isset($ddcClasses[$hundred])) {
                    $ddcClasses[$hundred]['count']++;
                } else {
                    $ddcClasses['000']['count']++;
                }
            }

            // Ambil Pengurus Perpustakaan dari Master Data User / PTK
            $staffCollection = collect($staffMembers);
            $libUsers = User::all()->filter(function ($u) {
                return $u->hasRole('kepala_perpustakaan') || $u->hasRole('pengelola_perpustakaan') || $u->hasRole('pustakawan');
            });

            foreach ($libUsers as $u) {
                $staffMatch = $staffCollection->first(function ($s) use ($u) {
                    return (! empty($u->nip) && ! empty($s['nip']) && $u->nip === $s['nip'])
                        || (! empty($s['name']) && ! empty($u->name) && (stripos($s['name'], $u->name) !== false || stripos($u->name, $s['name']) !== false));
                });

                $isHead = $u->hasRole('kepala_perpustakaan');
                $roleTitle = $isHead ? 'Kepala Perpustakaan Sekolah' : 'Pengelola Perpustakaan & Pustakawan';
                $badge = $isHead ? '👑 KEPALA PERPUSTAKAAN' : '📚 PENGELOLA PERPUS';

                $photoUrl = null;
                if ($staffMatch && ! empty($staffMatch['photo'])) {
                    $photoUrl = str_starts_with($staffMatch['photo'], 'http') ? $staffMatch['photo'] : asset($staffMatch['photo']);
                } elseif (! empty($u->avatar)) {
                    $photoUrl = str_starts_with($u->avatar, 'http') ? $u->avatar : asset($u->avatar);
                }

                $libraryManagers->push([
                    'name' => $staffMatch['name'] ?? $u->name,
                    'nip' => $u->nip ?: ($staffMatch['nip'] ?? '-'),
                    'role' => $roleTitle,
                    'badge' => $badge,
                    'is_head' => $isHead,
                    'status' => $staffMatch['employment_status'] ?? ($isHead ? 'PNS / ASN' : 'PPPK'),
                    'photo' => $photoUrl,
                ]);
            }

            $libraryManagers = $libraryManagers->sortByDesc('is_head')->values();

            // Rapor & Ranking (Duta Literasi Terpilih) - Dimulai dalam kondisi ter-reset (0 / bersih)
            // Hanya terisi otomatis jika terdapat catatan peminjaman & pembacaan buku riil pemustaka
            $realBorrowers = LibraryLoan::whereIn('status', ['RETURNED', 'dikembalikan', 'borrowed', 'dipinjam', 'DIPINJAM'])
                ->where('borrower_type', 'student')
                ->whereNotNull('borrower_name')
                ->selectRaw('borrower_name, borrower_class_or_dept, borrower_identifier, count(*) as books_read')
                ->groupBy('borrower_name', 'borrower_class_or_dept', 'borrower_identifier')
                ->having('books_read', '>', 0)
                ->orderByDesc('books_read')
                ->take(3)
                ->get();

            if ($realBorrowers->isNotEmpty()) {
                $medals = [
                    ['rank' => 1, 'badge' => '🥇 DUTA LITERASI EMAS', 'tier' => 'Juara 1 Duta Literasi', 'color' => 'amber'],
                    ['rank' => 2, 'badge' => '🥈 DUTA LITERASI PERAK', 'tier' => 'Juara 2 Duta Baca', 'color' => 'slate'],
                    ['rank' => 3, 'badge' => '🥉 DUTA LITERASI PERUNGGU', 'tier' => 'Juara 3 Duta Literasi Digital', 'color' => 'orange'],
                ];
                foreach ($realBorrowers as $idx => $rb) {
                    $m = $medals[$idx] ?? $medals[0];
                    $dutaLiterasi->push([
                        'rank' => $m['rank'],
                        'badge' => $m['badge'],
                        'tier' => $m['tier'],
                        'color' => $m['color'],
                        'books_read' => (int) $rb->books_read,
                        'name' => $rb->borrower_name,
                        'class' => $rb->borrower_class_or_dept ?: 'Siswa',
                        'nisn' => $rb->borrower_identifier ?: '-',
                    ]);
                }
            }

            $popularBooks = LibraryBook::where('is_active', true)
                ->orderByDesc('loans_count')
                ->orderByDesc('views_count')
                ->limit(6)
                ->get()
                ->map(fn ($b) => [
                    'title' => $b->title,
                    'author' => $b->author ?: 'Penulis Perpustakaan',
                    'category' => $b->category_name ?? ($b->category?->name ?? 'Umum'),
                    'available' => (int) ($b->available_copies ?? 1),
                    'total' => (int) ($b->total_copies ?? 1),
                    'cover' => $b->cover_image ? (str_starts_with($b->cover_image, 'http') ? $b->cover_image : asset('storage/'.$b->cover_image)) : null,
                ]);

            $recentLibraryVisitors = LibraryVisitor::latest('id')
                ->limit(6)
                ->get()
                ->map(fn ($v) => [
                    'name' => $v->name,
                    'type' => $v->visitor_type === 'siswa' ? 'Siswa' : ($v->visitor_type === 'guru' ? 'Guru/Tendik' : 'Tamu Umum'),
                    'class' => $v->class_or_institution ?: '-',
                    'purpose' => $v->purpose,
                    'time' => $v->visited_at ? $v->visited_at->format('H:i') : ($v->created_at ? $v->created_at->format('H:i') : '-'),
                ]);
        } catch (\Throwable $e) {
            // Graceful fallback if any column/table differs
        }

        // Fallback pengurus perpustakaan jika kosong
        if ($libraryManagers->isEmpty()) {
            $libraryManagers = collect([
                [
                    'name' => 'Vamil Setiawati, S.Pd.',
                    'nip' => '198205112014112001',
                    'role' => 'Kepala Perpustakaan Sekolah',
                    'badge' => '👑 KEPALA PERPUSTAKAAN',
                    'is_head' => true,
                    'status' => 'PNS / ASN',
                    'photo' => asset('images/website/staff/teacher_18_1794100963.jpg'),
                ],
                [
                    'name' => 'Hasbi',
                    'nip' => '199311022025211103',
                    'role' => 'Pengelola Perpustakaan & Pustakawan',
                    'badge' => '📚 PENGELOLA PERPUS',
                    'is_head' => false,
                    'status' => 'PPPK',
                    'photo' => asset('images/website/staff/teacher_39_1794078349.png'),
                ],
            ]);
        } elseif (! $libraryManagers->contains('is_head', true)) {
            $libraryManagers->prepend([
                'name' => 'Vamil Setiawati, S.Pd.',
                'nip' => '198205112014112001',
                'role' => 'Kepala Perpustakaan Sekolah',
                'badge' => '👑 KEPALA PERPUSTAKAAN',
                'is_head' => true,
                'status' => 'PNS / ASN',
                'photo' => asset('images/website/staff/teacher_18_1794100963.jpg'),
            ]);
        }

        // 17. Layanan Bimbingan & Konseling (BK) Terpadu
        $bkSessionsCount = 0;
        $bkSessionsToday = 0;
        $bkConsultationsCount = 0;
        $bkFields = [
            ['field' => 'Bimbingan Pribadi', 'icon' => '🧘', 'desc' => 'Pengembangan karakter, kedisiplinan & pemahaman diri', 'count' => 0],
            ['field' => 'Bimbingan Sosial', 'icon' => '🤝', 'desc' => 'Hubungan pertemanan, anti-perundungan & adaptasi', 'count' => 0],
            ['field' => 'Bimbingan Belajar', 'icon' => '📚', 'desc' => 'Strategi belajar efektif, motivasi & prestasi belajar', 'count' => 0],
            ['field' => 'Bimbingan Karir & Kuliah', 'icon' => '🎓', 'desc' => 'Jalur SNBP, SNBT, Kedinasan & peminatan vokasi', 'count' => 0],
        ];
        $bkCounselors = collect();

        try {
            $bkSessionsCount = BkCounselingSession::count();
            $bkSessionsToday = BkCounselingSession::whereDate('date', $today)->count();
            $bkConsultationsCount = BkConsultation::count();

            $bkFields = [
                ['field' => 'Bimbingan Pribadi', 'icon' => '🧘', 'desc' => 'Pengembangan karakter, kedisiplinan & pemahaman diri', 'count' => BkCounselingSession::where('field_of_service', 'like', '%pribadi%')->count()],
                ['field' => 'Bimbingan Sosial', 'icon' => '🤝', 'desc' => 'Hubungan pertemanan, anti-perundungan & adaptasi', 'count' => BkCounselingSession::where('field_of_service', 'like', '%sosial%')->count()],
                ['field' => 'Bimbingan Belajar', 'icon' => '📚', 'desc' => 'Strategi belajar efektif, motivasi & prestasi belajar', 'count' => BkCounselingSession::where('field_of_service', 'like', '%belajar%')->count()],
                ['field' => 'Bimbingan Karir & Kuliah', 'icon' => '🎓', 'desc' => 'Jalur SNBP, SNBT, Kedinasan & peminatan vokasi', 'count' => BkCounselingSession::where('field_of_service', 'like', '%karir%')->count()],
            ];

            // 17. Koordinator BK & Tim Guru Bimbingan Konseling (BK)
            $bkCounselors = collect();
            $staffCollection = collect($staffMembers);

            // A. Ambil dari Akun User & Hak Akses Role (koordinator_bk, guru_bk, bk)
            $bkUsers = User::all()->filter(function ($u) {
                return $u->hasRole('koordinator_bk') || $u->hasRole('guru_bk') || $u->hasRole('bk');
            });

            foreach ($bkUsers as $u) {
                $staffMatch = $staffCollection->first(function ($s) use ($u) {
                    return (! empty($u->nip) && ! empty($s['nip']) && $u->nip === $s['nip'])
                        || (! empty($s['name']) && ! empty($u->name) && (stripos($s['name'], $u->name) !== false || stripos($u->name, $s['name']) !== false));
                });

                $isCoord = $u->hasRole('koordinator_bk');
                $roleTitle = $isCoord ? 'Koordinator Bimbingan & Konseling' : 'Guru BK / Konselor Sekolah';
                $badge = $isCoord ? 'KOORDINATOR BK' : 'GURU BK';

                $photoUrl = null;
                if ($staffMatch && ! empty($staffMatch['photo'])) {
                    $photoUrl = str_starts_with($staffMatch['photo'], 'http') ? $staffMatch['photo'] : asset($staffMatch['photo']);
                } elseif (! empty($u->avatar)) {
                    $photoUrl = str_starts_with($u->avatar, 'http') ? $u->avatar : asset($u->avatar);
                }

                $bkCounselors->push([
                    'name' => $staffMatch['name'] ?? $u->name,
                    'nip' => $u->nip ?: ($staffMatch['nip'] ?? '-'),
                    'role' => $roleTitle,
                    'badge' => $badge,
                    'is_coordinator' => $isCoord,
                    'status' => $staffMatch['employment_status'] ?? ($isCoord ? 'PNS / ASN' : 'PPPK'),
                    'photo' => $photoUrl,
                ]);
            }

            // B. Cari juga di tabel Teacher jika ada guru dengan tugas BK yang belum tercakup
            $extraTeachers = Teacher::where(function ($q) {
                if (Schema::hasColumn('teachers', 'teaching_subject')) {
                    $q->where('teaching_subject', 'like', '%BK%')
                        ->orWhere('teaching_subject', 'like', '%Bimbingan%');
                }
                if (Schema::hasColumn('teachers', 'position')) {
                    $q->orWhere('position', 'like', '%BK%')
                        ->orWhere('position', 'like', '%Bimbingan%');
                }
            })->get();

            foreach ($extraTeachers as $et) {
                if (! $bkCounselors->contains(fn ($c) => (! empty($et->nip) && $c['nip'] === $et->nip) || $c['name'] === $et->name)) {
                    $staffMatch = $staffCollection->first(fn ($s) => (! empty($et->nip) && ! empty($s['nip']) && $et->nip === $s['nip']) || (stripos($s['name'] ?? '', $et->name) !== false));
                    $photoUrl = null;
                    if ($staffMatch && ! empty($staffMatch['photo'])) {
                        $photoUrl = str_starts_with($staffMatch['photo'], 'http') ? $staffMatch['photo'] : asset($staffMatch['photo']);
                    }
                    $bkCounselors->push([
                        'name' => $et->name,
                        'nip' => $et->nip ?: '-',
                        'role' => $et->position ?: ($et->teaching_subject ?? 'Guru Bimbingan & Konseling'),
                        'badge' => 'GURU BK',
                        'is_coordinator' => false,
                        'status' => $staffMatch['employment_status'] ?? 'PPPK',
                        'photo' => $photoUrl,
                    ]);
                }
            }

            // C. Urutkan Koordinator BK selalu tampil paling atas
            $bkCounselors = $bkCounselors->sortByDesc('is_coordinator')->values();

            // D. Pastikan Koordinator BK selalu hadir di posisi terdepan
            if (! $bkCounselors->contains('is_coordinator', true)) {
                $coordStaff = $staffCollection->first(fn ($s) => stripos($s['name'] ?? '', 'Ida Marttin') !== false);
                $bkCounselors->prepend([
                    'name' => $coordStaff['name'] ?? 'Ida Marttin Maolidah SE, SP.,M.Pd.',
                    'nip' => $coordStaff['nip'] ?? '197803102008012006',
                    'role' => 'Koordinator Bimbingan & Konseling',
                    'badge' => 'KOORDINATOR BK',
                    'is_coordinator' => true,
                    'status' => $coordStaff['employment_status'] ?? 'PNS / ASN',
                    'photo' => ! empty($coordStaff['photo']) ? (str_starts_with($coordStaff['photo'], 'http') ? $coordStaff['photo'] : asset($coordStaff['photo'])) : asset('images/website/staff/teacher_10_1794366251.png'),
                ]);
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        // Fallback resmi jika data master kosong
        if ($bkCounselors->isEmpty()) {
            $bkCounselors = collect([
                [
                    'name' => 'Ida Marttin Maolidah SE, SP.,M.Pd.',
                    'nip' => '197803102008012006',
                    'role' => 'Koordinator Bimbingan & Konseling',
                    'badge' => 'KOORDINATOR BK',
                    'is_coordinator' => true,
                    'status' => 'PNS / ASN',
                    'photo' => asset('images/website/staff/teacher_10_1794366251.png'),
                ],
                [
                    'name' => 'Melisa Irvianti, S.Pd.',
                    'nip' => '198905162022212015',
                    'role' => 'Guru BK / Konselor Sekolah',
                    'badge' => 'GURU BK',
                    'is_coordinator' => false,
                    'status' => 'PPPK',
                    'photo' => asset('images/website/staff/teacher_22_1794103579.png'),
                ],
                [
                    'name' => 'Fajar Wahid Kurniawan, S.Pd.',
                    'nip' => '199411192022211003',
                    'role' => 'Guru BK / Konselor Sekolah',
                    'badge' => 'GURU BK',
                    'is_coordinator' => false,
                    'status' => 'PPPK',
                    'photo' => asset('images/website/staff/teacher_25_1794078022.jpg'),
                ],
            ]);
        } elseif (! $bkCounselors->contains('is_coordinator', true)) {
            $bkCounselors->prepend([
                'name' => 'Ida Marttin Maolidah SE, SP.,M.Pd.',
                'nip' => '197803102008012006',
                'role' => 'Koordinator Bimbingan & Konseling',
                'badge' => 'KOORDINATOR BK',
                'is_coordinator' => true,
                'status' => 'PNS / ASN',
                'photo' => asset('images/website/staff/teacher_10_1794366251.png'),
            ]);
        }

        // 18. Buku Tamu Kedinasan & Kunjungan Resmi Sekolah (SIKATAS)
        $guestsTotalToday = 0;
        $guestsTotalMonth = 0;
        $recentGuests = collect();

        try {
            $guestsToday = Guest::whereDate('created_at', $today)
                ->orWhereDate('check_in_at', $today)
                ->latest('id')
                ->get();
            $guestsTotalToday = $guestsToday->count();
            $guestsTotalMonth = Guest::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count();

            $recentGuests = Guest::latest('id')
                ->limit(6)
                ->get()
                ->map(fn ($g) => [
                    'ticket' => $g->ticket_number,
                    'name' => $g->name,
                    'institution' => $g->institution ?: 'Masyarakat Umum',
                    'destination' => $g->destination_department ?: 'Tata Usaha / Kepala Sekolah',
                    'purpose' => $g->purpose ?: 'Kunjungan',
                    'time' => $g->check_in_at ? $g->check_in_at->format('H:i') : ($g->created_at ? $g->created_at->format('H:i') : '-'),
                    'status' => ucfirst($g->status ?? 'menunggu'),
                ]);
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        // Running Text Ticker Quotes
        $tickers = [
            "Selamat Datang di Monitor KIOSK TV {$schoolProfile['name']}.",
            "Batas Masuk Presensi Utama: {$todaySchedule['in_on_time']} WIB (Siswa yang belum absen otomatis tercatat Alpa).",
            'Petugas Piket Hari Ini: '.($todayPickets->isNotEmpty() ? $todayPickets->pluck('name')->implode(', ') : 'Guru & Staf Piket Terjadwal').'.',
            "Monitoring Keamanan: {$currentlyOutsideCount} Siswa di Luar Kampus, {$returnedCount} Siswa Kembali, {$earlyLeavesCount} Siswa Pulang Cepat.",
            "Waktu Shalat Dhuha: {$dhuhaCfg['days_label']} ({$dhuhaCfg['start_time']} - {$dhuhaCfg['end_time']} WIB).",
            "Shalat Dzuhur/Jum'at Berjamaah: {$dzuhurCfg['days_label']} ({$dzuhurCfg['start_time']} - {$dzuhurCfg['end_time']} WIB).",
            "Keputrian Siswi: {$keputrianCfg['days_label']} ({$keputrianCfg['start_time']} - {$keputrianCfg['end_time']} WIB).",
            "Presensi Ekstrakurikuler: {$extraDaysLabel} ({$extraStart} - {$extraEnd} WIB) untuk seluruh peminat cabang minat & bakat.",
            '7 Kebiasaan Anak Indonesia Hebat (7 KAIH): Laksanakan pembiasaan karakter setiap hari!',
            "Tingkatkan Hafalan Al-Qur'an Juz 30 dan Raih Poin Prestasi Positif Sekolah!",
            'Literasi Perpustakaan: Kunjungi Kios Perpustakaan Mandiri untuk membaca & meminjam koleksi buku sekolah!',
            'Layanan BK Terpadu: Konsultasikan pengembangan diri, minat bakat, dan persiapan SNBP/SNBT bersama Guru BK!',
            'Buku Tamu SIKATAS: Setiap tamu kedinasan & wali murid wajib melapor ke meja resepsionis / kios tamu.',
        ];

        if (! empty($newsArticles[0]['title'])) {
            $tickers[] = 'Warta Sekolah: '.$newsArticles[0]['title'];
        }
        if (! empty($agendas[0]['title'])) {
            $tickers[] = 'Agenda Terdekat: '.$agendas[0]['title'].' ('.$agendas[0]['date'].')';
        }

        return [
            'school' => [
                'name' => $schoolProfile['name'],
                'address' => $schoolProfile['address'],
                'logo' => SchoolSetting::get('school_logo', $schoolIdentity?->logo_path),
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
                'clubs' => $extracurricularClubs->values()->all(),
                'total_clubs' => $extracurricularClubs->count(),
            ],
            'rombels' => [
                'list' => $rombelList->values()->all(),
                'total_students' => $totalSchoolStudents,
                'total_male' => $totalSchoolMale,
                'total_female' => $totalSchoolFemale,
                'total_rombels' => $rombelList->count(),
                'avg_per_rombel' => $rombelList->count() > 0 ? round($totalSchoolStudents / $rombelList->count(), 1) : 0,
            ],
            'recap_students' => $recapStudents,
            'recap_gtk' => $recapGtk,
            'tahfidz' => [
                'total_today' => $tahfidzTodayCount,
                'passed_today' => $tahfidzPassedCount,
                'recent' => $recentTahfidz,
                'rankings' => $tahfidzRankings,
            ],
            'ranking' => $ranking,
            'recent_achievements' => $recentAchievements,
            'picket' => [
                'officers' => $todayPickets,
                'total' => $todayPickets->count(),
                'day_name' => $now->translatedFormat('l'),
                'zones' => $piketZones,
            ],
            'security' => [
                'early_leaves_count' => $earlyLeavesCount,
                'exit_permits_count' => $todayExitPermits->count(),
                'currently_outside' => $currentlyOutsideCount,
                'returned' => $returnedCount,
                'recent' => $recentPermits,
            ],
            'habits' => [
                'total' => $habitsTotal,
                'completed_today' => $habitsLogsToday,
                'list' => $habitsList,
            ],
            'profile' => $schoolProfile,
            'schedules' => $schedules,
            'news' => $newsArticles,
            'agendas' => $agendas,
            'agenda_countdowns' => $agendaCountdowns,
            'live_kbm' => $liveKbm,
            'staff' => $staffMembers,
            'library' => [
                'total_today' => $libraryTotalToday,
                'total_month' => $libraryTotalMonth,
                'students_today' => $libStudentsCount,
                'teachers_today' => $libTeachersCount,
                'general_today' => $libGeneralCount,
                'total_books' => $libraryTotalBooks,
                'available_copies' => (int) $libraryAvailableCopies,
                'active_loans' => $libraryActiveLoans,
                'popular_books' => $popularBooks,
                'recent_visitors' => $recentLibraryVisitors,
                'managers' => $libraryManagers,
                'ddc' => array_values($ddcClasses),
                'duta_literasi' => $dutaLiterasi,
            ],
            'bk' => [
                'total_sessions' => $bkSessionsCount,
                'sessions_today' => $bkSessionsToday,
                'consultations_count' => $bkConsultationsCount,
                'fields' => $bkFields,
                'counselors' => $bkCounselors,
            ],
            'guests' => [
                'total_today' => $guestsTotalToday,
                'total_month' => $guestsTotalMonth,
                'recent' => $recentGuests,
            ],
            'ticker' => implode('  •  ', $tickers),
        ];
    }
}

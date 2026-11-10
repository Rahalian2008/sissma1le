<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\BkCounselingSession;
use App\Models\BkPsychologicalTestResult;
use App\Models\ClassLearningAgenda;
use App\Models\CourseClass;
use App\Models\ExtracurricularAttendance;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\KurnasCocurricularScore;
use App\Models\KurnasCourseScore;
use App\Models\KurnasHomeroomRecord;
use App\Models\LibraryLoan;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WorshipAttendance;
use App\Services\AiStudentAnalysisService;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPortalController extends Controller
{
    public function __construct(
        protected AiStudentAnalysisService $aiService
    ) {}

    /**
     * Portal Gateway: Akses Portal Terpadu SIS-SMA1LE
     */
    public function portalSelection(): View
    {
        $schoolName = SchoolSetting::get('school_name', 'SMAN 1 Lengkong');
        $appName = SchoolSetting::get('app_name', 'SIS-SMA1LE');
        $appLogo = SchoolSetting::get('app_logo');
        $appLogoUrl = $appLogo ? asset('storage/'.$appLogo) : null;

        return view('portal.select', compact('schoolName', 'appName', 'appLogoUrl'));
    }

    /**
     * Portal Orang Tua Publik (Tanpa Login, Berbasis NISN Siswa)
     */
    public function index(Request $request): View
    {
        $nisn = trim((string) $request->input('nisn', $request->query('nisn', '')));
        $student = null;
        $todayAttendance = null;
        $recentAttendances = collect();
        $attendanceStats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];
        $attendanceRate = 0.0;
        $punctualityRate = 0.0;
        $habits = collect();
        $todayHabitLogs = collect();
        $achievements = collect();
        $violations = collect();
        $guidanceRecords = collect();
        $errorMessage = null;

        $isFemale = false;
        $hasExtracurriculars = false;
        $extraStats = [
            'total' => 0,
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'rate' => 0.0,
        ];
        $periodExtraAttendances = collect();
        $targetGrade = 'X';
        $gradeSurahs = QuranTahfidzRecord::getSurahsByGrade('X');
        $targetSurahsCount = count($gradeSurahs);
        $completedSurahsCount = 0;
        $tahfidzProgressPct = 0.0;
        $gradeSurahsWithStatus = collect();
        $allStudentTahfidzRecords = collect();

        $overallRank = 1;
        $totalStudentsCount = 0;
        $classRank = 1;
        $classStudentsCount = 0;
        $aiAnalysis = null;
        $worshipTahfidzSummary = null;

        // Default Filter & Variabel Rekapitulasi Presensi Periode
        $now = Carbon::now('Asia/Jakarta');
        $attPeriod = (string) $request->input('att_period', 'monthly');
        if (! in_array($attPeriod, ['daily', 'weekly', 'monthly', 'custom'], true)) {
            $attPeriod = 'monthly';
        }
        $attDate = $request->input('att_date', $now->toDateString());
        $attWeekDate = $request->input('att_week_date', $now->toDateString());
        $attMonth = (int) $request->input('att_month', $now->month);
        $attYear = (int) $request->input('att_year', $now->year);
        $attStartDate = $request->input('att_start_date', $now->copy()->subDays(14)->toDateString());
        $attEndDate = $request->input('att_end_date', $now->toDateString());
        $startDate = $now->copy()->startOfMonth()->toDateString();
        $endDate = $now->copy()->endOfMonth()->toDateString();
        $periodLabel = 'Bulanan: '.$now->translatedFormat('F Y');
        $periodAttendances = collect();
        $attStats = [
            'total' => 0,
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'pulang_cepat' => 0,
            'rate' => 0.0,
        ];
        $worshipStats = [
            'dhuha' => ['total' => 0, 'hadir' => 0, 'rate' => 0.0, 'records' => collect()],
            'dzuhur' => ['total' => 0, 'hadir' => 0, 'rate' => 0.0, 'records' => collect()],
            'keputrian' => ['total' => 0, 'hadir' => 0, 'rate' => 0.0, 'records' => collect()],
            'tahfidz' => [
                'period_count' => 0,
                'completed_surahs_count' => 0,
                'target_surahs_count' => 37,
                'progress_pct' => 0.0,
                'student_grade' => 'X',
                'records' => collect(),
            ],
            'all_worship_records' => collect(),
        ];

        $schoolSetting = SchoolSetting::first();
        $schoolName = $schoolSetting->school_name ?? config('app.name', 'SISSMA1LE');

        if (! empty($nisn)) {
            $student = Student::with(['schoolClass.homeroomTeacher', 'academicYear', 'extracurriculars.teacher'])
                ->where('nisn', $nisn)
                ->first();

            if ($student) {
                app(AttendanceService::class)->ensureDailyAttendanceRecords();
                $today = Carbon::today('Asia/Jakarta')->toDateString();

                // Hitung Rentang Tanggal Rekapitulasi Presensi Periode
                if ($attPeriod === 'daily') {
                    try {
                        $parsedDate = Carbon::parse($attDate, 'Asia/Jakarta');
                    } catch (\Exception $e) {
                        $parsedDate = $now->copy();
                    }
                    $startDate = $parsedDate->toDateString();
                    $endDate = $parsedDate->toDateString();
                    $periodLabel = 'Harian: '.$parsedDate->translatedFormat('l, d F Y');
                } elseif ($attPeriod === 'weekly') {
                    try {
                        $parsedWeek = Carbon::parse($attWeekDate, 'Asia/Jakarta');
                    } catch (\Exception $e) {
                        $parsedWeek = $now->copy();
                    }
                    $startWeek = $parsedWeek->copy()->startOfWeek(Carbon::MONDAY);
                    $endWeek = $parsedWeek->copy()->endOfWeek(Carbon::SUNDAY);
                    $startDate = $startWeek->toDateString();
                    $endDate = $endWeek->toDateString();
                    $periodLabel = 'Mingguan: '.$startWeek->translatedFormat('d M').' - '.$endWeek->translatedFormat('d M Y');
                } elseif ($attPeriod === 'monthly') {
                    if ($attMonth < 1 || $attMonth > 12) {
                        $attMonth = (int) $now->month;
                    }
                    if ($attYear < 2020 || $attYear > 2035) {
                        $attYear = (int) $now->year;
                    }
                    $parsedMonth = Carbon::createFromDate($attYear, $attMonth, 1, 'Asia/Jakarta');
                    $startDate = $parsedMonth->copy()->startOfMonth()->toDateString();
                    $endDate = $parsedMonth->copy()->endOfMonth()->toDateString();
                    $periodLabel = 'Bulanan: '.$parsedMonth->translatedFormat('F Y');
                } else { // 'custom'
                    try {
                        $parsedStart = Carbon::parse($attStartDate, 'Asia/Jakarta');
                        $parsedEnd = Carbon::parse($attEndDate, 'Asia/Jakarta');
                    } catch (\Exception $e) {
                        $parsedStart = $now->copy()->subDays(14);
                        $parsedEnd = $now->copy();
                    }
                    if ($parsedStart->gt($parsedEnd)) {
                        $temp = $parsedStart;
                        $parsedStart = $parsedEnd;
                        $parsedEnd = $temp;
                    }
                    $startDate = $parsedStart->toDateString();
                    $endDate = $parsedEnd->toDateString();
                    $periodLabel = 'Kustom: '.$parsedStart->translatedFormat('d M Y').' s.d. '.$parsedEnd->translatedFormat('d M Y');
                }

                // Query Rekapitulasi Presensi Kehadiran Siswa
                $periodAttendances = Attendance::where('student_id', $student->id)
                    ->whereDate('date', '>=', $startDate)
                    ->whereDate('date', '<=', $endDate)
                    ->orderByDesc('date')
                    ->orderByDesc('time')
                    ->get();

                $totalRecorded = $periodAttendances->count();
                $totalHadir = $periodAttendances->where('status', 'HADIR')->count();
                $totalTerlambat = $periodAttendances->where('status', 'TERLAMBAT')->count();
                $totalIzin = $periodAttendances->where('status', 'IZIN')->count();
                $totalSakit = $periodAttendances->where('status', 'SAKIT')->count();
                $totalAlpa = $periodAttendances->where('status', 'ALPA')->count();
                $totalPulangCepat = $periodAttendances->where('is_early_leave', true)->count();
                $periodAttendanceRate = $totalRecorded > 0
                    ? round((($totalHadir + $totalTerlambat) / $totalRecorded) * 100, 1)
                    : 0.0;

                $attStats = [
                    'total' => $totalRecorded,
                    'hadir' => $totalHadir,
                    'terlambat' => $totalTerlambat,
                    'izin' => $totalIzin,
                    'sakit' => $totalSakit,
                    'alpa' => $totalAlpa,
                    'pulang_cepat' => $totalPulangCepat,
                    'rate' => $periodAttendanceRate,
                ];

                // Rekapitulasi Ibadah & Tahfidz Periode Terpilih
                $periodWorshipAttendances = WorshipAttendance::where('student_id', $student->id)
                    ->whereDate('date', '>=', $startDate)
                    ->whereDate('date', '<=', $endDate)
                    ->orderByDesc('date')
                    ->orderByDesc('time')
                    ->get();

                $dhuhaRecords = $periodWorshipAttendances->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA);
                $dhuhaTotal = $dhuhaRecords->count();
                $dhuhaHadir = $dhuhaRecords->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
                $dhuhaRate = $dhuhaTotal > 0 ? round(($dhuhaHadir / $dhuhaTotal) * 100, 1) : 0.0;

                $dzuhurRecords = $periodWorshipAttendances->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT);
                $dzuhurTotal = $dzuhurRecords->count();
                $dzuhurHadir = $dzuhurRecords->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
                $dzuhurRate = $dzuhurTotal > 0 ? round(($dzuhurHadir / $dzuhurTotal) * 100, 1) : 0.0;

                $keputrianRecords = $periodWorshipAttendances->where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN);
                $keputrianTotal = $keputrianRecords->count();
                $keputrianHadir = $keputrianRecords->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
                $keputrianRate = $keputrianTotal > 0 ? round(($keputrianHadir / $keputrianTotal) * 100, 1) : 0.0;

                // Gender & Ekstrakurikuler Siswa
                $isFemale = in_array(strtoupper((string) $student->gender), ['P', 'PEREMPUAN', 'FEMALE'], true);
                $hasExtracurriculars = $student->extracurriculars->isNotEmpty();

                if ($hasExtracurriculars) {
                    $periodExtraAttendances = ExtracurricularAttendance::with(['extracurricular', 'recorder'])
                        ->where('student_id', $student->id)
                        ->whereDate('date', '>=', $startDate)
                        ->whereDate('date', '<=', $endDate)
                        ->orderByDesc('date')
                        ->orderByDesc('time')
                        ->get();

                    $extraTotal = $periodExtraAttendances->count();
                    $extraHadir = $periodExtraAttendances->where('status', 'HADIR')->count();
                    $extraTerlambat = $periodExtraAttendances->where('status', 'TERLAMBAT')->count();
                    $extraIzin = $periodExtraAttendances->where('status', 'IZIN')->count();
                    $extraSakit = $periodExtraAttendances->where('status', 'SAKIT')->count();
                    $extraAlpa = $periodExtraAttendances->where('status', 'ALPA')->count();
                    $extraRate = $extraTotal > 0 ? round((($extraHadir + $extraTerlambat) / $extraTotal) * 100, 1) : 0.0;

                    $extraStats = [
                        'total' => $extraTotal,
                        'hadir' => $extraHadir,
                        'terlambat' => $extraTerlambat,
                        'izin' => $extraIzin,
                        'sakit' => $extraSakit,
                        'alpa' => $extraAlpa,
                        'rate' => $extraRate,
                    ];
                }

                $periodTahfidzRecords = QuranTahfidzRecord::where('student_id', $student->id)
                    ->whereDate('deposit_date', '>=', $startDate)
                    ->whereDate('deposit_date', '<=', $endDate)
                    ->orderByDesc('deposit_date')
                    ->orderByDesc('deposit_time')
                    ->get();

                $targetGrade = $student->getNormalizedGrade() ?: 'X';
                $gradeSurahs = QuranTahfidzRecord::getSurahsByGrade($targetGrade);
                $targetSurahsCount = count($gradeSurahs) ?: 37;

                $allStudentTahfidzRecords = QuranTahfidzRecord::where('student_id', $student->id)
                    ->with('examiner')
                    ->orderByDesc('deposit_date')
                    ->orderByDesc('deposit_time')
                    ->get();

                $passedSurahsMap = $allStudentTahfidzRecords->where('overall_grade', 'Lulus')->keyBy('surah_number');
                $latestSurahsMap = $allStudentTahfidzRecords->keyBy('surah_number');

                $completedSurahsCount = $passedSurahsMap->filter(function ($rec, $num) use ($gradeSurahs) {
                    return isset($gradeSurahs[$num]);
                })->count();

                $tahfidzProgressPct = $targetSurahsCount > 0 ? round(($completedSurahsCount / $targetSurahsCount) * 100, 1) : 0.0;

                $gradeSurahsWithStatus = collect($gradeSurahs)->map(function ($s, $num) use ($passedSurahsMap, $latestSurahsMap) {
                    $passed = $passedSurahsMap->get($num);
                    $latest = $latestSurahsMap->get($num);
                    $activeRecord = $passed ?? $latest;

                    return [
                        'number' => $s['number'],
                        'name' => $s['name'],
                        'total_ayah' => $s['total_ayah'],
                        'meaning' => $s['meaning'],
                        'grade' => $s['grade'],
                        'grade_label' => $s['grade_label'] ?? ('Kelas '.$s['grade']),
                        'is_completed' => ! empty($passed),
                        'status' => $passed ? 'LULUS' : ($latest ? strtoupper($latest->overall_grade ?? 'MENGULANG') : 'BELUM_SETOR'),
                        'record' => $activeRecord,
                    ];
                })->values();

                $worshipStats = [
                    'dhuha' => [
                        'total' => $dhuhaTotal,
                        'hadir' => $dhuhaHadir,
                        'rate' => $dhuhaRate,
                        'records' => $dhuhaRecords,
                    ],
                    'dzuhur' => [
                        'total' => $dzuhurTotal,
                        'hadir' => $dzuhurHadir,
                        'rate' => $dzuhurRate,
                        'records' => $dzuhurRecords,
                    ],
                    'keputrian' => [
                        'total' => $keputrianTotal,
                        'hadir' => $keputrianHadir,
                        'rate' => $keputrianRate,
                        'records' => $keputrianRecords,
                    ],
                    'tahfidz' => [
                        'period_count' => $periodTahfidzRecords->count(),
                        'completed_surahs_count' => $completedSurahsCount,
                        'target_surahs_count' => $targetSurahsCount,
                        'progress_pct' => $tahfidzProgressPct,
                        'student_grade' => $targetGrade,
                        'records' => $periodTahfidzRecords,
                    ],
                    'all_worship_records' => $periodWorshipAttendances,
                ];

                $worshipTahfidzSummary = app(DashboardController::class)->getWorshipTahfidzDashboardData(null, $student->id);
                $today = Carbon::today('Asia/Jakarta')->toDateString();

                // Presensi Hari Ini
                $todayAttendance = Attendance::where('student_id', $student->id)
                    ->where('date', $today)
                    ->first();

                // Riwayat Presensi 30 Hari Terakhir
                $recentAttendances = Attendance::where('student_id', $student->id)
                    ->orderByDesc('date')
                    ->take(30)
                    ->get();

                // Statistik Kehadiran 30 Hari
                $hadirCount = $recentAttendances->where('status', 'HADIR')->count();
                $terlambatCount = $recentAttendances->where('status', 'TERLAMBAT')->count();
                $izinCount = $recentAttendances->where('status', 'IZIN')->count();
                $sakitCount = $recentAttendances->where('status', 'SAKIT')->count();
                $alpaCount = $recentAttendances->where('status', 'ALPA')->count();

                $attendanceStats = [
                    'hadir' => $hadirCount,
                    'terlambat' => $terlambatCount,
                    'izin' => $izinCount,
                    'sakit' => $sakitCount,
                    'alpa' => $alpaCount,
                ];

                $totalRecordedDays = $hadirCount + $terlambatCount + $izinCount + $sakitCount + $alpaCount;
                if ($totalRecordedDays > 0) {
                    $attendanceRate = round((($hadirCount + $terlambatCount) / $totalRecordedDays) * 100, 1);
                }
                if (($hadirCount + $terlambatCount) > 0) {
                    $punctualityRate = round(($hadirCount / ($hadirCount + $terlambatCount)) * 100, 1);
                }

                // 7 Kebiasaan Anak Hebat Hari Ini
                $habits = Habit::orderBy('order_number')->get();
                $todayHabitLogs = HabitDailyLog::where('student_id', $student->id)
                    ->where('date', $today)
                    ->get()
                    ->keyBy('habit_id');

                // Prestasi Terverifikasi (Lengkap dengan relasi kategori & butir)
                $achievements = $student->achievements()
                    ->with(['category', 'item'])
                    ->where('status', 'DIVERIFIKASI')
                    ->orderByDesc('date')
                    ->get();

                // Pelanggaran Terverifikasi (Lengkap dengan relasi kategori & butir)
                $violations = $student->violations()
                    ->with(['category', 'item'])
                    ->where('status', 'DIVERIFIKASI')
                    ->orderByDesc('date')
                    ->get();

                // Catatan Bimbingan / Konseling
                $guidanceRecords = $student->guidanceRecords()
                    ->with('counselor')
                    ->orderByDesc('date')
                    ->get();

                // Perhitungan Rangking Prestasi Siswa Secara Keseluruhan (Se-Sekolah & Se-Kelas)
                $studentAchPoints = $student->totalAchievementPoints();
                $totalStudentsCount = Student::where('is_active', true)->count();

                $studentsWithMorePoints = Student::where('is_active', true)
                    ->whereRaw('(SELECT COALESCE(SUM(points), 0) FROM achievements WHERE achievements.student_id = students.id AND achievements.status = ?) > ?', ['DIVERIFIKASI', $studentAchPoints])
                    ->count();
                $overallRank = $studentsWithMorePoints + 1;

                if ($student->class_id) {
                    $classStudentsCount = Student::where('class_id', $student->class_id)
                        ->where('is_active', true)
                        ->count();

                    $classStudentsWithMorePoints = Student::where('class_id', $student->class_id)
                        ->where('is_active', true)
                        ->whereRaw('(SELECT COALESCE(SUM(points), 0) FROM achievements WHERE achievements.student_id = students.id AND achievements.status = ?) > ?', ['DIVERIFIKASI', $studentAchPoints])
                        ->count();
                    $classRank = $classStudentsWithMorePoints + 1;
                }

                // Sintesis Analisis Raport Digital Siswa (Multi-Dimensi AI)
                $activeYear = AcademicYear::where('is_active', true)->first();
                $defaultStart = $activeYear?->start_date ? $activeYear->start_date->toDateString() : Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();
                $student->load('aiRaport');
                $latestAiRaport = $student->aiRaport;
                if (! $latestAiRaport) {
                    $latestAiRaport = $this->aiService->generateAndSaveStudentReport($student, null, 'SYSTEM');
                }
                $aiAnalysis = $this->aiService->analyzeStudent($student, $defaultStart, $today);

                // Rekapitulasi Progres 7 KAIH Komprehensif
                $allHabitLogs = HabitDailyLog::where('student_id', $student->id)->get();
                $habitStatsSummary = $habits->map(function ($habit) use ($allHabitLogs, $todayHabitLogs) {
                    $logsForHabit = $allHabitLogs->where('habit_id', $habit->id);
                    $totalLogs = $logsForHabit->count();
                    $completedCount = $logsForHabit->where('is_completed', true)->count();
                    $rate = $totalLogs > 0 ? round(($completedCount / $totalLogs) * 100, 1) : 0.0;
                    $todayLog = $todayHabitLogs->get($habit->id);

                    return [
                        'id' => $habit->id,
                        'name' => $habit->title ?? $habit->name,
                        'description' => $habit->description,
                        'target_time' => $habit->target_time,
                        'order_number' => $habit->order_number,
                        'total_logs' => $totalLogs,
                        'completed_count' => $completedCount,
                        'rate' => $rate,
                        'today_completed' => (bool) ($todayLog && $todayLog->is_completed),
                        'today_time' => $todayLog?->logged_time ? substr($todayLog->logged_time, 0, 5) : null,
                    ];
                });

                // Rapor Kurnas & Capaian Akademik (Permendikdasmen No. 13 Tahun 2025)
                $kurnasScores = KurnasCourseScore::where('student_id', $student->id)
                    ->with(['courseClass.subject', 'courseClass.teacher'])
                    ->get();
                $kurnasAverage = $kurnasScores->isNotEmpty() ? round((float) $kurnasScores->avg('score'), 1) : null;
                $kurnasHomeroom = KurnasHomeroomRecord::where('student_id', $student->id)
                    ->latest()
                    ->first();
                $kurnasP5Projects = KurnasCocurricularScore::where('student_id', $student->id)
                    ->with('project')
                    ->get();

                // Hasil Asesmen Psikotes & Konseling BK Terpadu
                $bkPsychologicalResults = BkPsychologicalTestResult::where('student_id', $student->id)
                    ->with('test')
                    ->latest('taken_at')
                    ->get();
                $bkCounselingSessions = BkCounselingSession::where('student_id', $student->id)
                    ->with('counselor')
                    ->latest('date')
                    ->get();

                // Aktivitas Literasi & Peminjaman Perpustakaan Sekolah
                $libraryLoans = LibraryLoan::where(function ($q) use ($student) {
                    if ($student->user_id) {
                        $q->where('user_id', $student->user_id);
                    }
                    $q->orWhere('member_id', $student->id);
                })
                    ->with('book')
                    ->orderByDesc('loan_date')
                    ->take(15)
                    ->get();

                // Jadwal Pelajaran Mingguan & Agenda KBM Kelas
                $classSchedules = $student->class_id
                    ? CourseClass::where('class_id', $student->class_id)
                        ->with(['subject', 'teacher'])
                        ->orderBy('day_of_week')
                        ->orderBy('start_time')
                        ->get()
                    : collect();

                $classAgendas = $student->schoolClass
                    ? ClassLearningAgenda::where('rombel', $student->schoolClass->name)
                        ->orderByDesc('date')
                        ->take(15)
                        ->get()
                    : collect();

                $homeroomTeacher = $student->schoolClass?->homeroomTeacher;
                $bkCounselor = Teacher::whereHas('user', function ($q) {
                    $q->where('role', 'bk')->orWhere('roles', 'like', '%"bk"%');
                })->first();

                // Saran Khusus Pendampingan Orang Tua / Wali di Rumah dari Hasil Raport AI Terbaru
                $rawParentAdvice = $latestAiRaport?->rekomendasi['orang_tua']['actions']
                    ?? ($aiAnalysis['rekomendasi']['orang_tua']['actions'] ?? null);

                $parentAdvice = is_array($rawParentAdvice) && ! empty($rawParentAdvice)
                    ? $rawParentAdvice
                    : [
                        'Dampingi anak secara konsisten dalam rutinitas pagi (bangun sebelum subuh & sarapan sehat) dan waktu tidur malam maksimal pukul 22.00 WIB demi menjaga kebugaran tubuh.',
                        'Luangkan waktu 10-15 menit setiap hari untuk berdialog hangat dengan penuh empati mengenai kegiatan sekolah, tantangan belajar, dan perasaan ananda.',
                        'Dukung dan apresiasi pembiasaan ibadah shalat fardhu berjamaah, Shalat Dhuha, serta murajaah hafalan Al-Qur\'an Juz 30 bersama keluarga di rumah.',
                        'Bimbing anak mematuhi tata tertib sekolah serta salurkan minat dan bakat positifnya secara produktif melalui kegiatan positif madrasah.',
                    ];
            } else {
                $latestAiRaport = null;
                $habitStatsSummary = collect();
                $parentAdvice = [];
                $kurnasScores = collect();
                $kurnasAverage = null;
                $kurnasHomeroom = null;
                $kurnasP5Projects = collect();
                $bkPsychologicalResults = collect();
                $bkCounselingSessions = collect();
                $libraryLoans = collect();
                $classSchedules = collect();
                $classAgendas = collect();
                $homeroomTeacher = null;
                $bkCounselor = null;
                $errorMessage = "Siswa dengan NISN '{$nisn}' tidak ditemukan. Mohon periksa kembali nomor NISN yang Anda masukkan atau hubungi wali kelas/pihak sekolah.";
            }
        } else {
            $latestAiRaport = null;
            $habitStatsSummary = collect();
            $parentAdvice = [];
            $kurnasScores = collect();
            $kurnasAverage = null;
            $kurnasHomeroom = null;
            $kurnasP5Projects = collect();
            $bkPsychologicalResults = collect();
            $bkCounselingSessions = collect();
            $libraryLoans = collect();
            $classSchedules = collect();
            $classAgendas = collect();
            $homeroomTeacher = null;
            $bkCounselor = null;
        }

        return view('public.parent_portal', compact(
            'latestAiRaport',
            'habitStatsSummary',
            'parentAdvice',
            'nisn',
            'student',
            'isFemale',
            'hasExtracurriculars',
            'extraStats',
            'periodExtraAttendances',
            'targetGrade',
            'targetSurahsCount',
            'completedSurahsCount',
            'tahfidzProgressPct',
            'gradeSurahsWithStatus',
            'gradeSurahs',
            'allStudentTahfidzRecords',
            'todayAttendance',
            'recentAttendances',
            'attendanceStats',
            'attendanceRate',
            'punctualityRate',
            'habits',
            'todayHabitLogs',
            'achievements',
            'violations',
            'guidanceRecords',
            'overallRank',
            'totalStudentsCount',
            'classRank',
            'classStudentsCount',
            'aiAnalysis',
            'worshipTahfidzSummary',
            'attPeriod',
            'periodLabel',
            'startDate',
            'endDate',
            'attStats',
            'periodAttendances',
            'worshipStats',
            'attDate',
            'attWeekDate',
            'attMonth',
            'attYear',
            'attStartDate',
            'attEndDate',
            'errorMessage',
            'kurnasScores',
            'kurnasAverage',
            'kurnasHomeroom',
            'kurnasP5Projects',
            'bkPsychologicalResults',
            'bkCounselingSessions',
            'libraryLoans',
            'classSchedules',
            'classAgendas',
            'homeroomTeacher',
            'bkCounselor',
            'schoolSetting',
            'schoolName'
        ));
    }

    /**
     * Cetak Berkas Resmi Raport Digital Siswa dari Portal Orang Tua (Format A4)
     */
    public function printRaport(Student $student, Request $request): View
    {
        $nisn = trim((string) $request->input('nisn', $request->query('nisn', '')));

        if ($nisn === '' || $student->nisn !== $nisn) {
            abort(403, 'Akses ditolak. Nomor NISN tidak valid atau tidak sesuai dengan data siswa.');
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $defaultStart = $activeYear?->start_date ? $activeYear->start_date->toDateString() : Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();
        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
        $paper = strtolower($request->input('paper', 'a4')) === 'f4' ? 'f4' : 'a4';

        $student->load(['aiRaport', 'schoolClass']);
        $latestAiRaport = $student->aiRaport;

        // Analisis Perkembangan AI
        $analysis = $this->aiService->analyzeStudent($student, $startDate, $endDate);

        // Rekapitulasi Rincian Prestasi & Pelanggaran Periode
        $periodAchievements = $student->achievements()
            ->with(['category', 'item'])
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date')
            ->get();

        $periodViolations = $student->violations()
            ->with(['category', 'item'])
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date')
            ->get();

        // Rekapitulasi Ibadah Bersama
        $worshipRecords = $student->worshipAttendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        $worshipSummary = [
            'total' => $worshipRecords->count(),
            'hadir' => $worshipRecords->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'dhuha' => $worshipRecords->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'dzuhur' => $worshipRecords->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'keputrian' => $worshipRecords->where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'rate' => $worshipRecords->count() > 0 ? round(($worshipRecords->whereIn('status', ['HADIR', 'TERLAMBAT'])->count() / $worshipRecords->count()) * 100, 1) : 0.0,
        ];

        // Rekapitulasi Setoran Tahfidz Al-Qur'an Juz 30
        $tahfidzRecords = $student->tahfidzRecords()
            ->whereBetween('deposit_date', [$startDate, $endDate])
            ->orderByDesc('deposit_date')
            ->orderByDesc('deposit_time')
            ->get();

        // Hitung progres berdasarkan surat target kelas (X=25, XI=7, XII=5 surat)
        $studentGrade = $student->getNormalizedGrade() ?: 'X';
        $targetGradeSurahs = QuranTahfidzRecord::getSurahsByGrade($studentGrade);
        $targetSurahNumbers = array_keys($targetGradeSurahs);
        $totalTargetSurahs = count($targetGradeSurahs);

        $completedSurahs = $student->tahfidzRecords()
            ->where('overall_grade', 'Lulus')
            ->whereIn('surah_number', $targetSurahNumbers)
            ->get()
            ->unique('surah_number');

        $tahfidzSummary = [
            'completed_surahs_count' => $completedSurahs->count(),
            'total_juz30' => $totalTargetSurahs,
            'student_grade' => $studentGrade,
            'progress_pct' => $totalTargetSurahs > 0 ? round(($completedSurahs->count() / $totalTargetSurahs) * 100, 1) : 0.0,
            'period_count' => $tahfidzRecords->count(),
            'period_lulus' => $tahfidzRecords->where('overall_grade', 'Lulus')->count(),
            'period_mengulang' => $tahfidzRecords->where('overall_grade', 'Mengulang')->count(),
            'completed_surahs' => $completedSurahs,
        ];

        // Data Sekolah & KOP
        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolCity = SchoolSetting::get('school_city', 'Sukabumi');
        $schoolBranch = SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
        $schoolEmail = SchoolSetting::get('school_email', 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id');
        $headmaster = Teacher::getHeadmaster();
        $headmasterName = $headmaster['name'];
        $headmasterNip = $headmaster['nip'];
        $kopImage = SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image'));
        $kopImageUrl = $kopImage ? asset('storage/'.$kopImage) : null;
        $fromPortal = true;

        return view('raport.print', compact(
            'student',
            'latestAiRaport',
            'analysis',
            'startDate',
            'endDate',
            'paper',
            'periodAchievements',
            'periodViolations',
            'worshipRecords',
            'worshipSummary',
            'tahfidzRecords',
            'tahfidzSummary',
            'schoolName',
            'schoolAddress',
            'schoolCity',
            'schoolBranch',
            'schoolEmail',
            'schoolWebsite',
            'headmasterName',
            'headmasterNip',
            'kopImageUrl',
            'fromPortal'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\AttendanceSession;
use App\Models\AuditLog;
use App\Models\GuidanceRecord;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Violation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Main dashboard entrypoint: routes automatically by user role.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role) {
            'siswa' => $this->studentDashboard(),
            'guru' => $this->teacherDashboard(),
            'wali_kelas' => $this->homeroomDashboard(),
            'kepala_sekolah' => $this->principalDashboard(),
            'bk' => $this->counselorDashboard(),
            'kesiswaan' => $this->studentAffairsDashboard(),
            'orang_tua' => $this->parentDashboard(),
            'admin', 'super_admin' => $this->adminDashboard(),
            default => redirect()->route('login'),
        };
    }

    /**
     * Dashboard Siswa
     */
    public function studentDashboard(): View
    {
        $user = Auth::user();
        $student = $user->student ?? Student::first();
        $today = Carbon::today()->toDateString();

        // Status presensi hari ini
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first();

        // Persentase kehadiran
        $attendanceRate = $student->attendancePercentage();

        // Total poin prestasi & pelanggaran (DIPISAH TEGAS!)
        $totalAchievementPoints = $student->totalAchievementPoints();
        $totalViolationPoints = $student->totalViolationPoints();

        // Status tata tertib
        $rewardStatus = $student->reward_status;
        $guidanceStatus = $student->guidance_status;

        // Progress 7 Kebiasaan Hari Ini
        $habits = Habit::orderBy('order_number')->get();
        $todayHabitLogs = HabitDailyLog::where('student_id', $student->id)
            ->where('date', $today)
            ->get()
            ->keyBy('habit_id');

        $completedHabitsCount = $todayHabitLogs->where('is_completed', true)->count();
        $habitProgressPercentage = count($habits) > 0 ? round(($completedHabitsCount / count($habits)) * 100) : 0;

        // Notifikasi terbaru
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Riwayat presensi 7 hari terakhir
        $recentAttendances = Attendance::where('student_id', $student->id)
            ->orderByDesc('date')
            ->take(7)
            ->get();

        // Papan Peringkat Prestasi Keseluruhan Siswa (Top Achievers Sekolah)
        $allAchievers = Student::with(['schoolClass', 'achievements' => fn ($q) => $q->where('status', 'DIVERIFIKASI')])
            ->where('is_active', true)
            ->get()
            ->map(function ($st) {
                $points = (int) $st->achievements->sum('points');

                return [
                    'student' => $st,
                    'points' => $points,
                    'count' => $st->achievements->count(),
                    'reward_status' => $st->reward_status,
                ];
            })
            ->sortByDesc('points')
            ->values();

        $studentRank = $allAchievers->search(fn ($item) => $item['student']->id === $student->id);
        $studentRank = $studentRank !== false ? ($studentRank + 1) : null;
        $topAchievers = $allAchievers->take(10);

        return view('dashboards.student', compact(
            'student',
            'todayAttendance',
            'attendanceRate',
            'totalAchievementPoints',
            'totalViolationPoints',
            'rewardStatus',
            'guidanceStatus',
            'habits',
            'todayHabitLogs',
            'completedHabitsCount',
            'habitProgressPercentage',
            'notifications',
            'recentAttendances',
            'topAchievers',
            'studentRank'
        ));
    }

    /**
     * Dashboard Guru
     */
    public function teacherDashboard(): View
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        $today = Carbon::today()->toDateString();

        // Sesi presensi aktif hari ini oleh guru ini
        $activeSessions = AttendanceSession::with('schoolClass')
            ->where('date', $today)
            ->orderByDesc('start_time')
            ->get();

        // Statistik input prestasi & pelanggaran
        $pendingAchievements = Achievement::where('status', 'MENUNGGU_VERIFIKASI')->count();
        $pendingViolations = Violation::where('status', 'MENUNGGU_VERIFIKASI')->count();

        // Siswa yang hadir hari ini
        $todayPresentCount = Attendance::where('date', $today)
            ->whereIn('status', ['HADIR', 'TERLAMBAT'])
            ->count();

        return view('dashboards.teacher', compact(
            'teacher',
            'activeSessions',
            'pendingAchievements',
            'pendingViolations',
            'todayPresentCount'
        ));
    }

    /**
     * Dashboard Wali Kelas
     */
    /**
     * Dashboard Wali Kelas
     */
    public function homeroomDashboard(): View
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        $class = $teacher?->homeroomClass;
        $today = Carbon::today()->toDateString();
        $classId = $class?->id ?? -1;

        $students = $class ? $class->students()->with([
            'attendances' => fn ($q) => $q->where('date', $today),
            'achievements',
            'violations',
        ])->get() : collect();

        $presentToday = 0;
        $lateToday = 0;
        $absentToday = 0;

        foreach ($students as $s) {
            $att = $s->attendances->first();
            if ($att) {
                if ($att->status === 'HADIR') {
                    $presentToday++;
                } elseif ($att->status === 'TERLAMBAT') {
                    $lateToday++;
                } else {
                    $absentToday++;
                }
            } else {
                $absentToday++;
            }
        }

        // Siswa yang perlu perhatian (Poin pelanggaran >= 75)
        $needGuidanceStudents = $students->filter(fn ($s) => $s->totalViolationPoints() >= 75);

        $analytics = $this->getDashboardAnalytics($class ? $class->id : -1);

        return view('dashboards.homeroom', array_merge(compact(
            'teacher',
            'class',
            'students',
            'presentToday',
            'lateToday',
            'absentToday',
            'needGuidanceStudents'
        ), $analytics));
    }

    /**
     * Dashboard BK (Bimbingan Konseling)
     */
    public function counselorDashboard(): View
    {
        $allStudents = Student::with(['schoolClass', 'violations', 'guidanceRecords'])->get();

        // Filter siswa yang mencapai ambang pembinaan: 75-124 (Wali Kelas), 125-175 (SP1), 176-199 (SP2), 200 (SP3), >200 (Rapat)
        $studentsNeedingAction = $allStudents->filter(fn ($s) => $s->totalViolationPoints() >= 75)
            ->sortByDesc(fn ($s) => $s->totalViolationPoints());

        $activeGuidanceCases = GuidanceRecord::with(['student.schoolClass', 'counselor'])
            ->where('status', 'DALAM_PROSES')
            ->orderByDesc('date')
            ->get();

        $recentViolations = Violation::with(['student.schoolClass', 'category'])
            ->orderByDesc('date')
            ->take(10)
            ->get();

        $analytics = $this->getDashboardAnalytics(null);

        return view('dashboards.counselor', array_merge(compact(
            'studentsNeedingAction',
            'activeGuidanceCases',
            'recentViolations'
        ), $analytics));
    }

    /**
     * Dashboard Kepala Sekolah (Monitoring & Pengawasan Eksekutif)
     */
    public function principalDashboard(): View
    {
        $today = Carbon::today()->toDateString();
        $totalStudents = Student::where('is_active', true)->count();
        $todayAttendances = Attendance::whereDate('date', $today)->get();

        $hadirCount = $todayAttendances->where('status', 'HADIR')->count();
        $terlambatCount = $todayAttendances->where('status', 'TERLAMBAT')->count();
        $izinCount = $todayAttendances->where('status', 'IZIN')->count();
        $sakitCount = $todayAttendances->where('status', 'SAKIT')->count();
        $alpaCount = max(0, $totalStudents - ($hadirCount + $terlambatCount + $izinCount + $sakitCount));

        $attendanceStats = [
            'total_students' => $totalStudents,
            'hadir' => $hadirCount,
            'terlambat' => $terlambatCount,
            'izin' => $izinCount,
            'sakit' => $sakitCount,
            'alpa' => $alpaCount,
            'hadir_pct' => $totalStudents > 0 ? round((($hadirCount + $terlambatCount) / $totalStudents) * 100, 1) : 0,
            'izin_pct' => $totalStudents > 0 ? round(($izinCount / $totalStudents) * 100, 1) : 0,
            'sakit_pct' => $totalStudents > 0 ? round(($sakitCount / $totalStudents) * 100, 1) : 0,
            'alpa_pct' => $totalStudents > 0 ? round(($alpaCount / $totalStudents) * 100, 1) : 0,
        ];

        // Kehadiran per Tingkat Kelas (X, XI, XII)
        $grades = ['X', 'XI', 'XII'];
        $gradeStats = [];
        foreach ($grades as $grade) {
            $classIds = SchoolClass::where('grade', $grade)->pluck('id');
            $gradeStudentCount = Student::whereIn('class_id', $classIds)->where('is_active', true)->count();
            $gradePresent = Attendance::whereIn('student_id', Student::whereIn('class_id', $classIds)->pluck('id'))
                ->whereDate('date', $today)
                ->whereIn('status', ['HADIR', 'TERLAMBAT'])
                ->count();
            $gradeStats[$grade] = [
                'total' => $gradeStudentCount,
                'present' => $gradePresent,
                'percentage' => $gradeStudentCount > 0 ? round(($gradePresent / $gradeStudentCount) * 100, 1) : 0,
            ];
        }

        // Statistik Tata Tertib
        $totalVerifiedAchievements = Achievement::where('status', 'DIVERIFIKASI')->count();
        $totalAchievementPoints = (int) Achievement::where('status', 'DIVERIFIKASI')->sum('points');
        $totalVerifiedViolations = Violation::where('status', 'DIVERIFIKASI')->count();
        $totalViolationPoints = (int) Violation::where('status', 'DIVERIFIKASI')->sum('points');

        // Siswa yang perlu perhatian / bimbingan khusus (Poin pelanggaran >= 75)
        $studentsNeedingAttention = Student::with(['schoolClass', 'violations'])
            ->where('is_active', true)
            ->get()
            ->filter(fn ($s) => $s->totalViolationPoints() >= 75)
            ->sortByDesc(fn ($s) => $s->totalViolationPoints())
            ->take(8);

        // Top 8 Siswa Berprestasi Sekolah
        $topAchievers = Student::with(['schoolClass', 'achievements' => fn ($q) => $q->where('status', 'DIVERIFIKASI')])
            ->where('is_active', true)
            ->get()
            ->map(function ($st) {
                $points = (int) $st->achievements->sum('points');

                return [
                    'student' => $st,
                    'points' => $points,
                    'count' => $st->achievements->count(),
                    'reward_status' => $st->reward_status,
                ];
            })
            ->sortByDesc('points')
            ->take(8);

        // 7 Kebiasaan Anak Indonesia Hebat
        $habits = Habit::orderBy('order_number')->get();
        $todayHabitLogs = HabitDailyLog::where('date', $today)->get();
        $completedHabitsCount = $todayHabitLogs->where('is_completed', true)->count();
        $totalExpectedHabits = max(1, $totalStudents * count($habits));
        $habitCompletionRate = round(($completedHabitsCount / $totalExpectedHabits) * 100, 1);

        // Kasus Pembinaan Aktif
        $activeGuidanceCases = GuidanceRecord::with(['student.schoolClass', 'counselor'])
            ->where('status', 'DALAM_PROSES')
            ->orderByDesc('date')
            ->take(5)
            ->get();

        // 5 Pelanggaran & Prestasi Terkini untuk Monitoring
        $recentViolations = Violation::with(['student.schoolClass', 'category'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
        $recentAchievements = Achievement::with(['student.schoolClass', 'category'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $analytics = $this->getDashboardAnalytics(null);

        return view('dashboards.principal', array_merge(compact(
            'totalStudents',
            'attendanceStats',
            'gradeStats',
            'totalVerifiedAchievements',
            'totalAchievementPoints',
            'totalVerifiedViolations',
            'totalViolationPoints',
            'studentsNeedingAttention',
            'topAchievers',
            'habits',
            'completedHabitsCount',
            'habitCompletionRate',
            'activeGuidanceCases',
            'recentViolations',
            'recentAchievements'
        ), $analytics));
    }

    /**
     * Dashboard Kesiswaan
     */
    public function studentAffairsDashboard(): View
    {
        $today = Carbon::today()->toDateString();
        $totalStudents = Student::count();

        $todayAttendances = Attendance::where('date', $today)->get();
        $presentToday = $todayAttendances->where('status', 'HADIR')->count();
        $lateToday = $todayAttendances->where('status', 'TERLAMBAT')->count();
        $attendanceRateToday = $totalStudents > 0 ? round((($presentToday + $lateToday) / $totalStudents) * 100, 1) : 0;

        $totalVerifiedAchievements = Achievement::where('status', 'DIVERIFIKASI')->count();
        $totalAchievementPoints = (int) Achievement::where('status', 'DIVERIFIKASI')->sum('points');

        $totalVerifiedViolations = Violation::where('status', 'DIVERIFIKASI')->count();
        $totalViolationPoints = (int) Violation::where('status', 'DIVERIFIKASI')->sum('points');

        $highRiskStudents = Student::with(['schoolClass'])
            ->get()
            ->filter(fn ($s) => $s->totalViolationPoints() >= 125)
            ->sortByDesc(fn ($s) => $s->totalViolationPoints());

        $analytics = $this->getDashboardAnalytics(null);

        return view('dashboards.kesiswaan', array_merge(compact(
            'totalStudents',
            'presentToday',
            'lateToday',
            'attendanceRateToday',
            'totalVerifiedAchievements',
            'totalAchievementPoints',
            'totalVerifiedViolations',
            'totalViolationPoints',
            'highRiskStudents'
        ), $analytics));
    }

    /**
     * Dashboard Orang Tua
     */
    public function parentDashboard(): View
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile ?? ParentGuardian::first();
        $student = $parentProfile?->student ?? Student::first();
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first();

        $achievements = $student->achievements()->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get();
        $violations = $student->violations()->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get();
        $guidanceRecords = $student->guidanceRecords()->orderByDesc('date')->get();

        // 7 Kebiasaan Hari Ini
        $habits = Habit::orderBy('order_number')->get();
        $todayHabitLogs = HabitDailyLog::where('student_id', $student->id)
            ->where('date', $today)
            ->get()
            ->keyBy('habit_id');

        // Papan Peringkat Prestasi Keseluruhan Siswa (Top Achievers Sekolah)
        $allAchievers = Student::with(['schoolClass', 'achievements' => fn ($q) => $q->where('status', 'DIVERIFIKASI')])
            ->where('is_active', true)
            ->get()
            ->map(function ($st) {
                $points = (int) $st->achievements->sum('points');

                return [
                    'student' => $st,
                    'points' => $points,
                    'count' => $st->achievements->count(),
                    'reward_status' => $st->reward_status,
                ];
            })
            ->sortByDesc('points')
            ->values();

        $studentRank = $allAchievers->search(fn ($item) => $item['student']->id === $student->id);
        $studentRank = $studentRank !== false ? ($studentRank + 1) : null;
        $totalSchoolStudents = $allAchievers->count();
        $topAchievers = $allAchievers->take(10);

        return view('dashboards.parent', compact(
            'parentProfile',
            'student',
            'todayAttendance',
            'achievements',
            'violations',
            'guidanceRecords',
            'habits',
            'todayHabitLogs',
            'topAchievers',
            'studentRank',
            'totalSchoolStudents'
        ));
    }

    /**
     * Dashboard Admin & Super Admin
     */
    public function adminDashboard(): View
    {
        $today = Carbon::today()->toDateString();
        $totalStudents = Student::where('is_active', true)->count();
        $todayAttendances = Attendance::whereDate('date', $today)->get();

        $hadirCount = $todayAttendances->where('status', 'HADIR')->count();
        $terlambatCount = $todayAttendances->where('status', 'TERLAMBAT')->count();
        $izinCount = $todayAttendances->where('status', 'IZIN')->count();
        $sakitCount = $todayAttendances->where('status', 'SAKIT')->count();
        $alpaCount = max(0, $totalStudents - ($hadirCount + $terlambatCount + $izinCount + $sakitCount));

        $attendanceStats = [
            'total_students' => $totalStudents,
            'hadir' => $hadirCount,
            'terlambat' => $terlambatCount,
            'izin' => $izinCount,
            'sakit' => $sakitCount,
            'alpa' => $alpaCount,
            'hadir_pct' => $totalStudents > 0 ? round((($hadirCount + $terlambatCount) / $totalStudents) * 100, 1) : 0,
            'izin_pct' => $totalStudents > 0 ? round(($izinCount / $totalStudents) * 100, 1) : 0,
            'sakit_pct' => $totalStudents > 0 ? round(($sakitCount / $totalStudents) * 100, 1) : 0,
            'alpa_pct' => $totalStudents > 0 ? round(($alpaCount / $totalStudents) * 100, 1) : 0,
        ];

        // Realtime Prestasi & Pelanggaran
        $totalVerifiedAchievements = Achievement::where('status', 'DIVERIFIKASI')->count();
        $totalAchievementPoints = (int) Achievement::where('status', 'DIVERIFIKASI')->sum('points');

        $totalVerifiedViolations = Violation::where('status', 'DIVERIFIKASI')->count();
        $totalViolationPoints = (int) Violation::where('status', 'DIVERIFIKASI')->sum('points');

        $disciplineStats = [
            'total_achievements' => $totalVerifiedAchievements,
            'total_achievement_points' => $totalAchievementPoints,
            'total_violations' => $totalVerifiedViolations,
            'total_violation_points' => $totalViolationPoints,
        ];

        // 5 Pelanggaran Terkini
        $recentViolations = Violation::with(['student.schoolClass', 'category'])
            ->where('status', 'DIVERIFIKASI')
            ->orderByDesc('date')
            ->take(5)
            ->get();

        $stats = [
            'total_students' => $totalStudents,
            'total_teachers' => Teacher::count(),
            'total_classes' => SchoolClass::count(),
            'today_attendances' => $todayAttendances->count(),
            'today_present' => $hadirCount,
            'today_late' => $terlambatCount,
            'pending_verifications' => Attendance::where('verification_status', 'PERLU_VERIFIKASI')->count()
                + Achievement::where('status', 'MENUNGGU_VERIFIKASI')->count()
                + Violation::where('status', 'MENUNGGU_VERIFIKASI')->count(),
        ];

        $location = AttendanceLocation::primary();
        $devices = AttendanceDevice::all();
        $recentAudits = AuditLog::with('user')->orderByDesc('created_at')->take(8)->get();

        $analytics = $this->getDashboardAnalytics(null);

        return view('dashboards.admin', array_merge(compact(
            'stats',
            'attendanceStats',
            'disciplineStats',
            'recentViolations',
            'location',
            'devices',
            'recentAudits'
        ), $analytics));
    }

    /**
     * Helper analytics for dashboards (Presensi ranking, Tata Tertib ranking & frequent items, 7 Karakter)
     */
    protected function getDashboardAnalytics(?int $classId = null): array
    {
        $today = Carbon::today()->toDateString();

        // 1. Presensi Rankings: Alpa, Izin, Sakit
        $topAlpaStudents = Student::with('schoolClass')
            ->when($classId !== null, fn ($q) => $q->where('class_id', $classId))
            ->whereHas('attendances', fn ($q) => $q->where('status', 'ALPA'))
            ->withCount(['attendances as count' => fn ($q) => $q->where('status', 'ALPA')])
            ->orderByDesc('count')
            ->take(5)
            ->get();

        $topIzinStudents = Student::with('schoolClass')
            ->when($classId !== null, fn ($q) => $q->where('class_id', $classId))
            ->whereHas('attendances', fn ($q) => $q->where('status', 'IZIN'))
            ->withCount(['attendances as count' => fn ($q) => $q->where('status', 'IZIN')])
            ->orderByDesc('count')
            ->take(5)
            ->get();

        $topSakitStudents = Student::with('schoolClass')
            ->when($classId !== null, fn ($q) => $q->where('class_id', $classId))
            ->whereHas('attendances', fn ($q) => $q->where('status', 'SAKIT'))
            ->withCount(['attendances as count' => fn ($q) => $q->where('status', 'SAKIT')])
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // 2. Tata Tertib: Rangking Prestasi, Pelanggaran, Mendekati/Mendapatkan Penghargaan & Peringatan
        $allStudents = Student::with(['schoolClass', 'achievements', 'violations'])
            ->where('is_active', true)
            ->when($classId !== null, fn ($q) => $q->where('class_id', $classId))
            ->get();

        $topAchievers = $allStudents->map(function ($s) {
            return [
                'student' => $s,
                'points' => $s->totalAchievementPoints(),
                'count' => $s->achievements->where('status', 'DIVERIFIKASI')->count(),
                'reward_status' => $s->reward_status,
            ];
        })->filter(fn ($x) => $x['points'] > 0)->sortByDesc('points')->take(5)->values();

        $topViolators = $allStudents->map(function ($s) {
            return [
                'student' => $s,
                'points' => $s->totalViolationPoints(),
                'count' => $s->violations->where('status', 'DIVERIFIKASI')->count(),
                'guidance_status' => $s->guidance_status,
            ];
        })->filter(fn ($x) => $x['points'] > 0)->sortByDesc('points')->take(5)->values();

        // Siswa Mendekati (>= 100) atau Mendapatkan Penghargaan (>= 125)
        $approachingOrAwardedStudents = $allStudents->map(function ($s) {
            $pts = $s->totalAchievementPoints();

            return [
                'student' => $s,
                'points' => $pts,
                'reward_status' => $s->reward_status,
                'is_awarded' => $pts >= 125,
            ];
        })->filter(fn ($x) => $x['points'] >= 100)->sortByDesc('points')->take(8)->values();

        // Siswa Mendekati Peringatan (>= 40) atau Mendapatkan Sanksi/Pembinaan (>= 75)
        $approachingOrWarnedStudents = $allStudents->map(function ($s) {
            $pts = $s->totalViolationPoints();

            return [
                'student' => $s,
                'points' => $pts,
                'guidance_status' => $s->guidance_status,
                'is_warned' => $pts >= 75,
            ];
        })->filter(fn ($x) => $x['points'] >= 40)->sortByDesc('points')->take(8)->values();

        // Prestasi & Pelanggaran sering dilakukan
        $frequentAchievements = Achievement::where('status', 'DIVERIFIKASI')
            ->when($classId !== null, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->select('title', 'level', DB::raw('count(*) as total_count'), DB::raw('sum(points) as total_points'))
            ->groupBy('title', 'level')
            ->orderByDesc('total_count')
            ->take(5)
            ->get();

        $frequentViolations = Violation::where('status', 'DIVERIFIKASI')
            ->when($classId !== null, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->select('title', DB::raw('count(*) as total_count'), DB::raw('sum(points) as total_points'))
            ->groupBy('title')
            ->orderByDesc('total_count')
            ->take(5)
            ->get();

        // 3. Karakter 7 Kebiasaan Anak Indonesia Hebat
        $habits = Habit::orderBy('order_number')->get();
        $totalEligibleStudents = $classId !== null
            ? Student::where('class_id', $classId)->where('is_active', true)->count()
            : Student::where('is_active', true)->count();

        $habitStats = $habits->map(function ($h) use ($totalEligibleStudents, $classId, $today) {
            $q = HabitDailyLog::where('habit_id', $h->id)
                ->where('date', $today)
                ->where('is_completed', true);
            if ($classId !== null) {
                $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId));
            }
            $completedCount = $q->count();
            $percentage = $totalEligibleStudents > 0 ? round(($completedCount / $totalEligibleStudents) * 100, 1) : 0;

            return [
                'habit' => $h,
                'completed_count' => $completedCount,
                'total_students' => $totalEligibleStudents,
                'percentage' => $percentage,
            ];
        });

        $overallHabitPercentage = count($habitStats) > 0 ? round($habitStats->avg('percentage'), 1) : 0;
        $totalCompletedHabits = $habitStats->sum('completed_count');
        $totalExpectedHabits = $totalEligibleStudents * count($habits);
        $highestHabit = $habitStats->sortByDesc('percentage')->first();
        $lowestHabit = $habitStats->sortBy('percentage')->first();

        return compact(
            'topAlpaStudents',
            'topIzinStudents',
            'topSakitStudents',
            'topAchievers',
            'topViolators',
            'frequentAchievements',
            'frequentViolations',
            'approachingOrAwardedStudents',
            'approachingOrWarnedStudents',
            'habits',
            'habitStats',
            'overallHabitPercentage',
            'totalCompletedHabits',
            'totalExpectedHabits',
            'highestHabit',
            'lowestHabit'
        );
    }
}

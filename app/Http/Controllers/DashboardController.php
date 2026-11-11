<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\AttendanceQrLog;
use App\Models\AttendanceRfidLog;
use App\Models\AttendanceSelfie;
use App\Models\AttendanceSession;
use App\Models\AuditLog;
use App\Models\BkCounselingSession;
use App\Models\ClassLearningAgenda;
use App\Models\CourseClass;
use App\Models\EarlyLeaveRequest;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\GuidanceRecord;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\LibraryVisitor;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsMaterial;
use App\Models\LmsQuiz;
use App\Models\Notification;
use App\Models\ParentGuardian;
use App\Models\PicketSchedule;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\SchoolHoliday;
use App\Models\Student;
use App\Models\StudentAiRaport;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Violation;
use App\Models\WorshipAttendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Main dashboard entrypoint: routes automatically by user role.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $this->attendanceService->ensureDailyAttendanceRecords();

        $user = Auth::user();

        // Resolve active role from session or database active_role
        $activeRole = session('active_role') ?? $user->active_role;
        $isSuperOrAdmin = $user->hasAnyRole(['super_admin', 'admin']);

        if ($activeRole && ($isSuperOrAdmin || $user->role === $activeRole || $user->hasRole($activeRole) || $activeRole === 'all_roles')) {
            if ($activeRole === 'all_roles') {
                $hasOperationalRoles = $user->hasAnyRole([
                    'guru', 'wali_kelas', 'bk', 'koordinator_bk', 'guru_bk',
                    'wakasek_kesiswaan', 'kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras',
                    'kepala_sekolah', 'tu', 'staff', 'tenaga_administrasi', 'kepala_administrasi',
                    'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras',
                    'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'bendahara',
                    'pembina_ekstra', 'guru_piket', 'tu_piket', 'petugas_piket',
                    'kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan',
                    'orang_tua', 'siswa',
                ]);

                if (! $hasOperationalRoles && $isSuperOrAdmin) {
                    return $this->adminDashboard();
                }

                return $this->combinedMultiRoleDashboard($request);
            }

            if ($activeRole === 'super_admin' || $activeRole === 'admin') {
                return $this->adminDashboard();
            }
            if ($activeRole === 'kepala_sekolah') {
                return $this->principalDashboard();
            }
            if ($activeRole === 'wakasek_kesiswaan' || $activeRole === 'kesiswaan') {
                return $this->studentAffairsDashboard();
            }
            if ($activeRole === 'wakasek_kurikulum') {
                return $this->curriculumDashboard();
            }
            if ($activeRole === 'wakasek_humas') {
                return $this->publicRelationsDashboard();
            }
            if ($activeRole === 'wakasek_sarpras' || $activeRole === 'kepala_laboratorium') {
                return $this->facilitiesDashboard();
            }
            if ($activeRole === 'wali_kelas') {
                return $this->homeroomDashboard();
            }
            if (in_array($activeRole, ['bk', 'koordinator_bk', 'guru_bk'], true)) {
                return $this->counselorDashboard();
            }
            if ($activeRole === 'pembina_ekstra') {
                return $this->teacherDashboard();
            }
            if ($activeRole === 'koordinator_keagamaan') {
                return redirect()->route('tahfidz.index');
            }
            if (in_array($activeRole, ['guru_piket', 'tu_piket', 'petugas_piket'], true)) {
                return redirect()->route('attendance.index');
            }
            if (in_array($activeRole, ['kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan'], true)) {
                return redirect()->route('library.index');
            }
            if (in_array($activeRole, ['kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'bendahara', 'tu', 'staff', 'tenaga_administrasi'], true)) {
                return redirect()->route('tu.dashboard');
            }
            if (in_array($activeRole, ['operator_dapodik', 'operator'], true)) {
                return $this->adminDashboard();
            }
            if ($activeRole === 'guru') {
                return $this->teacherDashboard();
            }
            if ($activeRole === 'orang_tua') {
                return $this->parentDashboard($request);
            }
            if ($activeRole === 'siswa' || $activeRole === 'sekretaris_kelas' || $activeRole === 'sekretaris_ekstra') {
                return $this->studentDashboard();
            }
        }

        // Multi-role prioritized dispatching based on primary role first
        $primary = $user->role;
        if ($primary === 'kepala_sekolah') {
            return $this->principalDashboard();
        }
        if ($primary === 'wakasek_kesiswaan' || $primary === 'kesiswaan') {
            return $this->studentAffairsDashboard();
        }
        if ($primary === 'wakasek_kurikulum') {
            return $this->curriculumDashboard();
        }
        if ($primary === 'wakasek_humas') {
            return $this->publicRelationsDashboard();
        }
        if ($primary === 'wakasek_sarpras' || $primary === 'kepala_laboratorium') {
            return $this->facilitiesDashboard();
        }
        if ($primary === 'wali_kelas') {
            return $this->homeroomDashboard();
        }
        if (in_array($primary, ['bk', 'koordinator_bk', 'guru_bk'], true)) {
            return $this->counselorDashboard();
        }
        if ($primary === 'guru') {
            return $this->teacherDashboard();
        }
        if ($primary === 'orang_tua') {
            return $this->parentDashboard($request);
        }
        if ($primary === 'siswa' || $primary === 'sekretaris_kelas' || $primary === 'sekretaris_ekstra') {
            return $this->studentDashboard();
        }
        if (in_array($primary, ['kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'bendahara', 'tu', 'staff', 'tenaga_administrasi'], true)) {
            return redirect()->route('tu.dashboard');
        }
        if (in_array($primary, ['kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan'], true)) {
            return redirect()->route('library.index');
        }
        if (in_array($primary, ['guru_piket', 'tu_piket', 'petugas_piket'], true)) {
            return redirect()->route('attendance.index');
        }
        if ($primary === 'pembina_ekstra') {
            return $this->teacherDashboard();
        }
        if ($primary === 'koordinator_keagamaan') {
            return redirect()->route('tahfidz.index');
        }

        // Multi-role prioritized dispatching
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $this->adminDashboard();
        }
        if ($user->hasRole('kepala_sekolah')) {
            return $this->principalDashboard();
        }
        if ($user->hasAnyRole(['wakasek_kesiswaan', 'kesiswaan'])) {
            return $this->studentAffairsDashboard();
        }
        if ($user->hasRole('wakasek_kurikulum')) {
            return $this->curriculumDashboard();
        }
        if ($user->hasRole('wakasek_humas')) {
            return $this->publicRelationsDashboard();
        }
        if ($user->hasRole('wakasek_sarpras') || $user->hasRole('kepala_laboratorium')) {
            return $this->facilitiesDashboard();
        }
        if ($user->hasRole('wali_kelas')) {
            return $this->homeroomDashboard();
        }
        if ($user->hasRole('bk') || $user->hasRole('koordinator_bk') || $user->hasRole('guru_bk')) {
            return $this->counselorDashboard();
        }
        if ($user->hasAnyRole(['kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'bendahara', 'tu', 'staff', 'tenaga_administrasi'])) {
            return redirect()->route('tu.dashboard');
        }
        if ($user->hasAnyRole(['kepala_perpustakaan', 'pengelola_perpustakaan', 'pustakawan'])) {
            return redirect()->route('library.index');
        }
        if ($user->hasAnyRole(['guru_piket', 'tu_piket', 'petugas_piket'])) {
            return redirect()->route('attendance.index');
        }
        if ($user->hasRole('pembina_ekstra')) {
            return redirect()->route('extracurricular.index');
        }
        if ($user->hasRole('koordinator_keagamaan')) {
            return redirect()->route('tahfidz.index');
        }
        if ($user->hasRole('guru')) {
            return $this->teacherDashboard();
        }
        if ($user->hasRole('orang_tua')) {
            return $this->parentDashboard($request);
        }
        if ($user->hasRole('siswa') || $user->hasRole('sekretaris_kelas') || $user->hasRole('sekretaris_ekstra')) {
            return $this->studentDashboard();
        }
        if ($user->canManageLibrary()) {
            return redirect()->route('library.index');
        }
        if ($user->isTenagaAdministrasi()) {
            return redirect()->route('tu.dashboard');
        }

        return redirect()->route('login');
    }

    /**
     * Dashboard Siswa
     */
    public function studentDashboard(): View
    {
        $user = Auth::user();
        $student = $user->student()->first() ?? Student::where('user_id', $user->id)->first() ?? Student::first();
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
        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData(null, $student->id);

        $studentExtracurriculars = collect();
        $todayExtraAttendances = collect();

        if ($student) {
            $studentExtracurriculars = $student->extracurriculars()
                ->where('is_active', true)
                ->with(['teacher.user', 'teachers.user'])
                ->get()
                ->sortByDesc(fn ($ex) => in_array($ex->pivot->role ?? '', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true) ? 1 : 0)
                ->values();

            if ($studentExtracurriculars->isNotEmpty()) {
                $todayExtraAttendances = ExtracurricularAttendance::where('student_id', $student->id)
                    ->whereDate('date', $today)
                    ->get()
                    ->keyBy('extracurricular_id');
            }
        }

        $schedule = AttendanceService::getScheduleForDay(Carbon::now('Asia/Jakarta'));
        $lmsLibraryData = $this->getLmsLibraryDashboardData($user);

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
            'studentRank',
            'worshipTahfidzSummary',
            'studentExtracurriculars',
            'todayExtraAttendances',
            'schedule',
            'lmsLibraryData'
        ));
    }

    /**
     * Dashboard Gabungan Multi-Peran (Mode Semua Akses)
     * Menggabungkan tanggung jawab dan layar pantau peran-peran aktif pengguna (Guru Mapel, Wali Kelas, Wakasek, BK, dll.)
     */
    public function combinedMultiRoleDashboard(Request $request): View
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        $roleLabels = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'kepala_sekolah' => 'Kepala Sekolah',
            'wakasek_kurikulum' => 'Wakasek Kurikulum',
            'wakasek_kesiswaan' => 'Wakasek Kesiswaan',
            'wakasek_sarpras' => 'Wakasek Sarpras & BMD',
            'wakasek_humas' => 'Wakasek Humas & Kemitraan',
            'guru' => 'Tenaga Pendidik (Guru Mapel)',
            'wali_kelas' => 'Wali Kelas',
            'koordinator_bk' => 'Koordinator Bimbingan Konseling',
            'guru_bk' => 'Guru BK',
            'bk' => 'Bimbingan Konseling',
            'kepala_perpustakaan' => 'Kepala Perpustakaan',
            'pengelola_perpustakaan' => 'Pengelola Perpustakaan',
            'pustakawan' => 'Pustakawan',
            'kepala_administrasi' => 'Kepala Tata Usaha',
            'tu' => 'Staf Tata Usaha',
            'staff' => 'Tenaga Kependidikan',
            'tenaga_administrasi' => 'Tenaga Administrasi',
            'ta_kesiswaan' => 'Adm. Kesiswaan',
            'ta_kepegawaian' => 'Adm. Kepegawaian',
            'ta_persuratan' => 'Adm. Persuratan',
            'ta_sarpras' => 'Adm. Sarpras',
            'bendahara_bosp' => 'Bendahara BOSP',
            'bendahara_bopd' => 'Bendahara BOPD',
            'bendahara_gaji' => 'Bendahara Gaji',
            'bendahara' => 'Bendahara Sekolah',
            'pembina_ekstra' => 'Pembina Ekstrakurikuler',
            'guru_piket' => 'Guru Piket',
            'tu_piket' => 'TU Piket',
            'petugas_piket' => 'Petugas Piket',
            'orang_tua' => 'Orang Tua Siswa',
            'siswa' => 'Siswa',
        ];

        $allUserRoles = $user->all_roles;
        if (empty($allUserRoles)) {
            $allUserRoles = [$user->role];
        }

        $activeRoleNames = array_map(fn ($r) => $roleLabels[$r] ?? ucfirst(str_replace('_', ' ', (string) $r)), $allUserRoles);

        // Guru Mapel data
        $activeSessions = collect();
        $todayPresentCount = 0;
        $pendingAchievements = 0;
        $pendingViolations = 0;
        if ($user->hasRole('guru') || $user->role === 'guru') {
            $activeSessions = AttendanceSession::with('schoolClass')
                ->where('date', $today)
                ->orderByDesc('start_time')
                ->get();
            $todayPresentCount = Attendance::where('date', $today)
                ->whereIn('status', ['HADIR', 'TERLAMBAT'])
                ->count();
            $pendingAchievements = Achievement::where('status', 'MENUNGGU_VERIFIKASI')->count();
            $pendingViolations = Violation::where('status', 'MENUNGGU_VERIFIKASI')->count();
        }

        // Wali Kelas data
        $class = null;
        $students = collect();
        $totalStudents = 0;
        $presentToday = 0;
        $lateToday = 0;
        $permissionToday = 0;
        $sickToday = 0;
        $alphaToday = 0;
        $absentToday = 0;
        $needGuidanceStudents = collect();
        $analytics = [];

        if ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') {
            $class = $teacher?->homeroomClass ?? SchoolClass::where('homeroom_teacher_id', $teacher?->id)->first();
            if ($class) {
                $students = $class->students()->with([
                    'schoolClass',
                    'attendances' => fn ($q) => $q->where('date', $today),
                    'worshipAttendances' => fn ($q) => $q->where('date', $today),
                    'extracurricularAttendances' => fn ($q) => $q->where('date', $today)->with('extracurricular'),
                    'tahfidzRecords',
                    'achievements',
                    'violations',
                ])->get();

                foreach ($students as $s) {
                    $att = $s->attendances->first();
                    if ($att) {
                        if ($att->status === 'HADIR') {
                            $presentToday++;
                        } elseif ($att->status === 'TERLAMBAT') {
                            $lateToday++;
                        } elseif ($att->status === 'IZIN') {
                            $permissionToday++;
                        } elseif ($att->status === 'SAKIT') {
                            $sickToday++;
                        } else {
                            $alphaToday++;
                        }
                    } else {
                        $alphaToday++;
                    }
                }

                $totalStudents = $students->count();
                $absentToday = $permissionToday + $sickToday + $alphaToday;
                $needGuidanceStudents = $students->filter(fn ($s) => $s->totalViolationPoints() >= 75);
                $analytics = $this->getDashboardAnalytics($class->id);
            }
        }

        // BK Data
        $bkCasesCount = 0;
        if ($user->hasAnyRole(['bk', 'koordinator_bk', 'guru_bk'])) {
            if (Schema::hasTable('bk_counseling_sessions')) {
                $bkCasesCount = BkCounselingSession::count();
            }
        }

        // Wakasek Kesiswaan Data
        $totalEarlyLeavesToday = 0;
        $totalSchoolPassesToday = 0;
        if ($user->hasAnyRole(['wakasek_kesiswaan', 'kesiswaan'])) {
            if (Schema::hasTable('early_leave_requests')) {
                $totalEarlyLeavesToday = EarlyLeaveRequest::whereDate('date', $today)->count();
            }
            if (Schema::hasTable('school_exit_permits')) {
                $totalSchoolPassesToday = SchoolExitPermit::whereDate('date', $today)->count();
            }
        }

        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData($class?->id ?? null, null);
        $lmsLibraryData = $this->getLmsLibraryDashboardData($user);
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

        return view('dashboards.combined_multi_role', array_merge(compact(
            'user',
            'teacher',
            'today',
            'allUserRoles',
            'activeRoleNames',
            'activeSessions',
            'todayPresentCount',
            'pendingAchievements',
            'pendingViolations',
            'class',
            'students',
            'totalStudents',
            'presentToday',
            'lateToday',
            'permissionToday',
            'sickToday',
            'alphaToday',
            'absentToday',
            'needGuidanceStudents',
            'bkCasesCount',
            'totalEarlyLeavesToday',
            'totalSchoolPassesToday',
            'worshipTahfidzSummary',
            'lmsLibraryData',
            'schoolAgendasData'
        ), $analytics));
    }

    /**
     * Dashboard Guru
     */
    public function teacherDashboard(): View
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        $today = Carbon::today('Asia/Jakarta')->toDateString();

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

        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData(null, null);
        $lmsLibraryData = $this->getLmsLibraryDashboardData(Auth::user());
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

        return view('dashboards.teacher', compact(
            'teacher',
            'activeSessions',
            'pendingAchievements',
            'pendingViolations',
            'todayPresentCount',
            'worshipTahfidzSummary',
            'lmsLibraryData',
            'schoolAgendasData'
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
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $classId = $class?->id ?? -1;

        $students = $class ? $class->students()->with([
            'schoolClass',
            'attendances' => fn ($q) => $q->where('date', $today),
            'worshipAttendances' => fn ($q) => $q->where('date', $today),
            'extracurricularAttendances' => fn ($q) => $q->where('date', $today)->with('extracurricular'),
            'tahfidzRecords',
            'achievements',
            'violations',
        ])->get() : collect();

        $presentToday = 0;
        $lateToday = 0;
        $permissionToday = 0;
        $sickToday = 0;
        $alphaToday = 0;

        foreach ($students as $s) {
            $att = $s->attendances->first();
            if ($att) {
                if ($att->status === 'HADIR') {
                    $presentToday++;
                } elseif ($att->status === 'TERLAMBAT') {
                    $lateToday++;
                } elseif ($att->status === 'IZIN') {
                    $permissionToday++;
                } elseif ($att->status === 'SAKIT') {
                    $sickToday++;
                } else {
                    $alphaToday++;
                }
            } else {
                $alphaToday++;
            }
        }

        $totalStudents = $students->count();
        $absentToday = $permissionToday + $sickToday + $alphaToday;

        // Siswa yang perlu perhatian (Poin pelanggaran >= 75)
        $needGuidanceStudents = $students->filter(fn ($s) => $s->totalViolationPoints() >= 75);

        $analytics = $this->getDashboardAnalytics($class ? $class->id : -1);
        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData($class?->id, null);
        $lmsLibraryData = $this->getLmsLibraryDashboardData(Auth::user());
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

        return view('dashboards.homeroom', array_merge(compact(
            'today',
            'teacher',
            'class',
            'students',
            'totalStudents',
            'presentToday',
            'lateToday',
            'permissionToday',
            'sickToday',
            'alphaToday',
            'absentToday',
            'needGuidanceStudents',
            'worshipTahfidzSummary',
            'lmsLibraryData',
            'schoolAgendasData'
        ), $analytics));
    }

    /**
     * Dashboard BK (Bimbingan Konseling)
     */
    public function counselorDashboard(): View
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $totalStudents = Student::where('is_active', true)->count();

        // 1. Presensi Utama
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

        // Deteksi Dini BK: Siswa dengan Alpa Berulang (>= 3 Alpa)
        $chronicAlpaStudents = Student::with('schoolClass')
            ->where('is_active', true)
            ->whereHas('attendances', function ($q) {
                $q->where('status', 'ALPA');
            }, '>=', 3)
            ->withCount(['attendances as total_alpa' => function ($q) {
                $q->where('status', 'ALPA');
            }])
            ->orderByDesc('total_alpa')
            ->take(8)
            ->get();

        // Deteksi Dini BK: Siswa Sering Terlambat (>= 3 Kali)
        $chronicLateStudents = Student::with('schoolClass')
            ->where('is_active', true)
            ->whereHas('attendances', function ($q) {
                $q->where('status', 'TERLAMBAT');
            }, '>=', 3)
            ->withCount(['attendances as total_terlambat' => function ($q) {
                $q->where('status', 'TERLAMBAT');
            }])
            ->orderByDesc('total_terlambat')
            ->take(8)
            ->get();

        // 2. Tata Tertib & Kasus Konseling
        $allStudents = Student::with(['schoolClass', 'violations', 'guidanceRecords'])->get();

        // Filter siswa yang mencapai ambang pembinaan: 75-124 (Wali Kelas), 125-175 (SP1), 176-199 (SP2), 200 (SP3), >200 (Rapat)
        $studentsNeedingAction = $allStudents->filter(fn ($s) => $s->totalViolationPoints() >= 75)
            ->sortByDesc(fn ($s) => $s->totalViolationPoints());

        $totalVerifiedAchievements = Achievement::where('status', 'DIVERIFIKASI')->count();
        $totalAchievementPoints = (int) Achievement::where('status', 'DIVERIFIKASI')->sum('points');
        $totalVerifiedViolations = Violation::where('status', 'DIVERIFIKASI')->count();
        $totalViolationPoints = (int) Violation::where('status', 'DIVERIFIKASI')->sum('points');

        $activeGuidanceCases = GuidanceRecord::with(['student.schoolClass', 'counselor'])
            ->where('status', 'DALAM_PROSES')
            ->orderByDesc('date')
            ->get();

        $recentViolations = Violation::with(['student.schoolClass', 'category'])
            ->orderByDesc('date')
            ->take(10)
            ->get();

        // 3. Karakter 7 Kebiasaan Anak Indonesia Hebat (7 KAIH)
        $habits = Habit::orderBy('order_number')->get();
        $todayHabitLogs = HabitDailyLog::where('date', $today)->get();
        $completedHabitsCount = $todayHabitLogs->where('is_completed', true)->count();
        $totalExpectedHabits = max(1, $totalStudents * count($habits));
        $habitCompletionRate = round(($completedHabitsCount / $totalExpectedHabits) * 100, 1);

        // 4. Peribadatan (Dzuhur, Dhuha, Keputrian), Ekstrakurikuler, dan Tahfidz Juz 30
        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData(null, null);

        $analytics = $this->getDashboardAnalytics(null);
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

        return view('dashboards.counselor', array_merge(compact(
            'totalStudents',
            'attendanceStats',
            'chronicAlpaStudents',
            'chronicLateStudents',
            'studentsNeedingAction',
            'totalVerifiedAchievements',
            'totalAchievementPoints',
            'totalVerifiedViolations',
            'totalViolationPoints',
            'activeGuidanceCases',
            'recentViolations',
            'habits',
            'completedHabitsCount',
            'habitCompletionRate',
            'worshipTahfidzSummary',
            'schoolAgendasData'
        ), $analytics));
    }

    /**
     * Dashboard Kepala Sekolah (Monitoring & Pengawasan Eksekutif)
     */
    public function principalDashboard(): View
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
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

        // Siswa Anomali: Sering Alpa / Terindikasi Membolos (Total Alpa >= 3)
        $chronicAlpaStudents = Student::with('schoolClass')
            ->where('is_active', true)
            ->whereHas('attendances', function ($q) {
                $q->where('status', 'ALPA');
            }, '>=', 3)
            ->withCount(['attendances as total_alpa' => function ($q) {
                $q->where('status', 'ALPA');
            }])
            ->orderByDesc('total_alpa')
            ->take(6)
            ->get();

        // Siswa Ambang Batas Kritis (SP 1, SP 2, SP 3: Poin >= 125)
        $criticalGuidanceStudents = Student::with(['schoolClass', 'violations'])
            ->where('is_active', true)
            ->get()
            ->filter(fn ($s) => $s->totalViolationPoints() >= 125)
            ->sortByDesc(fn ($s) => $s->totalViolationPoints())
            ->take(6);

        $analytics = $this->getDashboardAnalytics(null);
        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData(null, null);
        $lmsLibraryData = $this->getLmsLibraryDashboardData(Auth::user());
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

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
            'recentAchievements',
            'worshipTahfidzSummary',
            'chronicAlpaStudents',
            'criticalGuidanceStudents',
            'lmsLibraryData',
            'schoolAgendasData'
        ), $analytics));
    }

    /**
     * Dashboard Kesiswaan
     */
    public function studentAffairsDashboard(): View
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
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
        $lmsLibraryData = $this->getLmsLibraryDashboardData(Auth::user());
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

        return view('dashboards.kesiswaan', array_merge(compact(
            'totalStudents',
            'presentToday',
            'lateToday',
            'attendanceRateToday',
            'totalVerifiedAchievements',
            'totalAchievementPoints',
            'totalVerifiedViolations',
            'totalViolationPoints',
            'highRiskStudents',
            'lmsLibraryData',
            'schoolAgendasData'
        ), $analytics));
    }

    /**
     * Dashboard Wakasek Bid. Kurikulum (Akademik, KBM, Tahfidz & AI Raport)
     */
    public function curriculumDashboard(): View
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $totalStudents = Student::where('is_active', true)->count();
        $totalClasses = SchoolClass::count();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $todayAttendances = Attendance::where('date', $today)->get();
        $presentToday = $todayAttendances->where('status', 'HADIR')->count();
        $lateToday = $todayAttendances->where('status', 'TERLAMBAT')->count();
        $attendanceRateToday = $totalStudents > 0 ? round((($presentToday + $lateToday) / $totalStudents) * 100, 1) : 0;

        // Kehadiran per jenjang (Tingkat X, XI, XII)
        $classes = SchoolClass::withCount(['students' => fn ($q) => $q->where('is_active', true)])->get();
        $gradeStats = [
            'X' => ['total' => 0, 'present' => 0, 'rate' => 0],
            'XI' => ['total' => 0, 'present' => 0, 'rate' => 0],
            'XII' => ['total' => 0, 'present' => 0, 'rate' => 0],
        ];

        foreach (['X', 'XI', 'XII'] as $grade) {
            $gradeClasses = $classes->filter(fn ($c) => str_starts_with(strtoupper($c->name), $grade.' ') || strtoupper($c->name) === $grade || str_starts_with(strtoupper($c->name), $grade.'-'));
            $gradeClassIds = $gradeClasses->pluck('id');
            $gradeTotalStudents = Student::whereIn('class_id', $gradeClassIds)->where('is_active', true)->count();
            $gradePresent = Attendance::whereIn('class_id', $gradeClassIds)->where('date', $today)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
            $gradeStats[$grade] = [
                'total' => $gradeTotalStudents,
                'present' => $gradePresent,
                'rate' => $gradeTotalStudents > 0 ? round(($gradePresent / $gradeTotalStudents) * 100, 1) : 0,
            ];
        }

        // Raport Digital AI
        $totalAiRaport = StudentAiRaport::count();
        $aiCompletionRate = $totalStudents > 0 ? round(($totalAiRaport / $totalStudents) * 100, 1) : 0;

        // Tahfidz & Keagamaan
        $totalTahfidzRecords = QuranTahfidzRecord::count();
        $recentTahfidz = QuranTahfidzRecord::with(['student.schoolClass', 'examiner'])
            ->orderByDesc('deposit_date')
            ->orderByDesc('deposit_time')
            ->take(6)
            ->get();

        // Top Capaian Siswa & Analytics
        $analytics = $this->getDashboardAnalytics(null);
        $lmsLibraryData = $this->getLmsLibraryDashboardData(Auth::user());
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

        return view('dashboards.wakasek_kurikulum', array_merge(compact(
            'totalStudents',
            'totalClasses',
            'activeYear',
            'presentToday',
            'lateToday',
            'attendanceRateToday',
            'gradeStats',
            'totalAiRaport',
            'aiCompletionRate',
            'totalTahfidzRecords',
            'recentTahfidz',
            'classes',
            'lmsLibraryData',
            'schoolAgendasData'
        ), $analytics));
    }

    /**
     * Dashboard Wakasek Bid. Humas (Kemitraan, Publikasi Prestasi & Portal Orang Tua)
     */
    public function publicRelationsDashboard(): View
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $totalStudents = Student::where('is_active', true)->count();
        $totalParents = ParentGuardian::count();

        // Prestasi Siswa Terverifikasi (R1-R10)
        $totalVerifiedAchievements = Achievement::where('status', 'DIVERIFIKASI')->count();
        $totalAchievementPoints = (int) Achievement::where('status', 'DIVERIFIKASI')->sum('points');
        $recentAchievements = Achievement::with(['student.schoolClass', 'category'])
            ->where('status', 'DIVERIFIKASI')
            ->orderByDesc('date')
            ->take(8)
            ->get();

        // Top 10 Bintang Prestasi (Top Achievers) Siap Publikasi
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
            ->filter(fn ($item) => $item['points'] > 0)
            ->sortByDesc('points')
            ->take(10)
            ->values();

        // Tingkat Kehadiran Sekolah
        $todayAttendances = Attendance::where('date', $today)->get();
        $presentToday = $todayAttendances->where('status', 'HADIR')->count();
        $lateToday = $todayAttendances->where('status', 'TERLAMBAT')->count();
        $attendanceRateToday = $totalStudents > 0 ? round((($presentToday + $lateToday) / $totalStudents) * 100, 1) : 0;

        $analytics = $this->getDashboardAnalytics(null);

        return view('dashboards.wakasek_humas', array_merge(compact(
            'totalStudents',
            'totalParents',
            'totalVerifiedAchievements',
            'totalAchievementPoints',
            'recentAchievements',
            'topAchievers',
            'presentToday',
            'lateToday',
            'attendanceRateToday'
        ), $analytics));
    }

    /**
     * Dashboard Wakasek Bid. Sarpras (Infrastruktur Mesin IoT, Scanner, & Geofence)
     */
    public function facilitiesDashboard(): View
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $totalStudents = Student::where('is_active', true)->count();

        // IoT Devices (10 Mesin RFID & 10 Mesin QR Code)
        $devices = AttendanceDevice::orderBy('device_type')->orderBy('device_name')->get();
        $rfidDevicesCount = AttendanceDevice::rfid()->count();
        $qrDevicesCount = AttendanceDevice::barcode()->count();
        try {
            $activeDevicesCount = AttendanceDevice::active()->count();
        } catch (\Throwable) {
            $activeDevicesCount = AttendanceDevice::whereIn('status', ['ONLINE', '1', 'TRUE', 'AKTIF'])->count();
        }

        // Metode Presensi Hari Ini
        $rfidScansToday = AttendanceRfidLog::whereDate('scanned_at', $today)->count();
        $qrScansToday = AttendanceQrLog::whereDate('scanned_at', $today)->count();
        $selfieScansToday = AttendanceSelfie::whereDate('created_at', $today)->count();

        $todayAttendances = Attendance::where('date', $today)->get();
        $presentToday = $todayAttendances->where('status', 'HADIR')->count();
        $lateToday = $todayAttendances->where('status', 'TERLAMBAT')->count();
        $attendanceRateToday = $totalStudents > 0 ? round((($presentToday + $lateToday) / $totalStudents) * 100, 1) : 0;

        // Lokasi Geofence Presensi
        $location = AttendanceLocation::first();

        // Fasilitas Ekstrakurikuler
        $extracurricularsCount = Extracurricular::where('is_active', true)->count();

        $analytics = $this->getDashboardAnalytics(null);

        return view('dashboards.wakasek_sarpras', array_merge(compact(
            'totalStudents',
            'devices',
            'rfidDevicesCount',
            'qrDevicesCount',
            'activeDevicesCount',
            'rfidScansToday',
            'qrScansToday',
            'selfieScansToday',
            'presentToday',
            'lateToday',
            'attendanceRateToday',
            'location',
            'extracurricularsCount'
        ), $analytics));
    }

    /**
     * Dashboard Orang Tua
     */
    public function parentDashboard(?Request $request = null): View
    {
        $req = $request ?? request();
        $user = Auth::user();
        $parentProfile = $user->parentProfile ?? ParentGuardian::first();
        $student = $parentProfile?->student ?? Student::first();
        $now = Carbon::today('Asia/Jakarta');
        $today = $now->toDateString();

        $todayAttendance = $student ? Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first() : null;

        $achievements = $student ? $student->achievements()->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get() : collect();
        $violations = $student ? $student->violations()->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get() : collect();
        $guidanceRecords = $student ? $student->guidanceRecords()->orderByDesc('date')->get() : collect();

        // 7 Kebiasaan Hari Ini
        $habits = Habit::orderBy('order_number')->get();
        $todayHabitLogs = $student ? HabitDailyLog::where('student_id', $student->id)
            ->where('date', $today)
            ->get()
            ->keyBy('habit_id') : collect();

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

        $studentRank = ($student && $allAchievers->isNotEmpty())
            ? $allAchievers->search(fn ($item) => $item['student']->id === $student->id)
            : false;
        $studentRank = $studentRank !== false ? ($studentRank + 1) : null;
        $totalSchoolStudents = $allAchievers->count();
        $topAchievers = $allAchievers->take(10);
        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData(null, $student?->id);

        // Rekapitulasi Presensi Kehadiran Anak (Harian, Mingguan, Bulanan, Custom)
        $attPeriod = $req->input('att_period', 'monthly');
        if (! in_array($attPeriod, ['daily', 'weekly', 'monthly', 'custom'], true)) {
            $attPeriod = 'monthly';
        }

        $attDate = $req->input('att_date', $now->toDateString());
        $attWeekDate = $req->input('att_week_date', $now->toDateString());
        $attMonth = (int) $req->input('att_month', $now->month);
        $attYear = (int) $req->input('att_year', $now->year);
        $attStartDate = $req->input('att_start_date', $now->copy()->subDays(14)->toDateString());
        $attEndDate = $req->input('att_end_date', $now->toDateString());

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

        $periodAttendances = collect();
        if ($student) {
            $periodAttendances = Attendance::where('student_id', $student->id)
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->get();
        }

        $totalRecorded = $periodAttendances->count();
        $totalHadir = $periodAttendances->where('status', 'HADIR')->count();
        $totalTerlambat = $periodAttendances->where('status', 'TERLAMBAT')->count();
        $totalIzin = $periodAttendances->where('status', 'IZIN')->count();
        $totalSakit = $periodAttendances->where('status', 'SAKIT')->count();
        $totalAlpa = $periodAttendances->where('status', 'ALPA')->count();
        $totalPulangCepat = $periodAttendances->where('is_early_leave', true)->count();
        $attendanceRate = $totalRecorded > 0
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
            'rate' => $attendanceRate,
        ];

        // Rekapitulasi Ibadah & Tahfidz Periode Terpilih
        $periodWorshipAttendances = collect();
        $periodTahfidzRecords = collect();
        $dhuhaTotal = 0;
        $dhuhaHadir = 0;
        $dhuhaRate = 0.0;
        $dzuhurTotal = 0;
        $dzuhurHadir = 0;
        $dzuhurRate = 0.0;
        $keputrianTotal = 0;
        $keputrianHadir = 0;
        $keputrianRate = 0.0;
        $completedSurahsCount = 0;
        $targetSurahsCount = 37;
        $tahfidzProgressPct = 0.0;

        if ($student) {
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

            $periodTahfidzRecords = QuranTahfidzRecord::where('student_id', $student->id)
                ->whereDate('deposit_date', '>=', $startDate)
                ->whereDate('deposit_date', '<=', $endDate)
                ->orderByDesc('deposit_date')
                ->orderByDesc('deposit_time')
                ->get();

            $targetGrade = $student->getNormalizedGrade() ?: 'X';
            $gradeSurahs = QuranTahfidzRecord::getSurahsByGrade($targetGrade);
            $targetSurahsCount = count($gradeSurahs) ?: 37;
            $completedSurahsCount = QuranTahfidzRecord::where('student_id', $student->id)
                ->where('overall_grade', 'Lulus')
                ->whereIn('surah_number', array_keys($gradeSurahs))
                ->distinct('surah_number')
                ->count('surah_number');
            $tahfidzProgressPct = $targetSurahsCount > 0 ? round(($completedSurahsCount / $targetSurahsCount) * 100, 1) : 0.0;
        }

        $worshipStats = [
            'dhuha' => [
                'total' => $dhuhaTotal,
                'hadir' => $dhuhaHadir,
                'rate' => $dhuhaRate,
                'records' => $student ? $periodWorshipAttendances->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA) : collect(),
            ],
            'dzuhur' => [
                'total' => $dzuhurTotal,
                'hadir' => $dzuhurHadir,
                'rate' => $dzuhurRate,
                'records' => $student ? $periodWorshipAttendances->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT) : collect(),
            ],
            'keputrian' => [
                'total' => $keputrianTotal,
                'hadir' => $keputrianHadir,
                'rate' => $keputrianRate,
                'records' => $student ? $periodWorshipAttendances->where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN) : collect(),
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
            'totalSchoolStudents',
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
            'attEndDate'
        ));
    }

    /**
     * Dashboard Admin & Super Admin
     */
    public function adminDashboard(): View
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $totalStudents = Student::where('is_active', true)->count();
        $todayAttendances = Attendance::whereDate('date', $today)->get();

        $hadirCount = $todayAttendances->where('status', 'HADIR')->count();
        $terlambatCount = $todayAttendances->where('status', 'TERLAMBAT')->count();
        $izinCount = $todayAttendances->where('status', 'IZIN')->count();
        $sakitCount = $todayAttendances->where('status', 'SAKIT')->count();
        $alpaCount = max(0, $totalStudents - ($hadirCount + $terlambatCount + $izinCount + $sakitCount));

        $attendanceStats = [
            'total_students' => $totalStudents,
            'hadir' => $hadirCount + $terlambatCount,
            'hadir_tepat_waktu' => $hadirCount,
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
            'today_present' => $hadirCount + $terlambatCount,
            'today_on_time' => $hadirCount,
            'today_late' => $terlambatCount,
            'pending_verifications' => Attendance::where('verification_status', 'PERLU_VERIFIKASI')->count()
                + Achievement::where('status', 'MENUNGGU_VERIFIKASI')->count()
                + Violation::where('status', 'MENUNGGU_VERIFIKASI')->count(),
        ];

        $location = AttendanceLocation::primary();
        $devices = AttendanceDevice::all();
        $recentAudits = AuditLog::with('user')->orderByDesc('created_at')->take(8)->get();

        $analytics = $this->getDashboardAnalytics(null);
        $worshipTahfidzSummary = $this->getWorshipTahfidzDashboardData(null, null);
        $lmsLibraryData = $this->getLmsLibraryDashboardData(Auth::user());
        $schoolAgendasData = $this->getTodayAgendasAndActivitiesData();

        return view('dashboards.admin', array_merge(compact(
            'stats',
            'attendanceStats',
            'disciplineStats',
            'recentViolations',
            'location',
            'devices',
            'recentAudits',
            'worshipTahfidzSummary',
            'lmsLibraryData',
            'schoolAgendasData'
        ), $analytics));
    }

    /**
     * Helper analytics for dashboards (Presensi ranking, Tata Tertib ranking & frequent items, 7 Karakter)
     */
    protected function getDashboardAnalytics(?int $classId = null): array
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();

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

    /**
     * Data Agenda, Kalender Akademik, & Kegiatan Sekolah Terupdate Hari Ini
     */
    public function getTodayAgendasAndActivitiesData(): array
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $todayCarbon = Carbon::today('Asia/Jakarta');
        $todayDayOfWeek = $todayCarbon->dayOfWeek;
        $dayNameMap = [
            Carbon::SUNDAY => 'Minggu',
            Carbon::MONDAY => 'Senin',
            Carbon::TUESDAY => 'Selasa',
            Carbon::WEDNESDAY => 'Rabu',
            Carbon::THURSDAY => 'Kamis',
            Carbon::FRIDAY => 'Jumat',
            Carbon::SATURDAY => 'Sabtu',
        ];
        $todayDayName = $dayNameMap[$todayDayOfWeek] ?? 'Senin';

        // 1. Agenda Pembelajaran Kelas Hari Ini (ClassLearningAgenda)
        $todayLearningAgendas = class_exists(ClassLearningAgenda::class) && Schema::hasTable('class_learning_agendas')
            ? ClassLearningAgenda::with(['teacher'])->whereDate('date', $today)->orderBy('time_slot')->take(10)->get()
            : collect();

        // 2. Kalender & Agenda Resmi Sekolah Hari Ini (SchoolHoliday active)
        $activeHolidaysToday = class_exists(SchoolHoliday::class) && Schema::hasTable('school_holidays')
            ? SchoolHoliday::active()
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->get()
            : collect();

        // 3. Agenda Mendatang (Upcoming 14 Hari)
        $upcomingEvents = class_exists(SchoolHoliday::class) && Schema::hasTable('school_holidays')
            ? SchoolHoliday::active()
                ->whereDate('start_date', '>', $today)
                ->whereDate('start_date', '<=', $todayCarbon->copy()->addDays(14)->toDateString())
                ->orderBy('start_date')
                ->take(5)
                ->get()
            : collect();

        // 4. Petugas Piket Hari Ini
        $todayPicketOfficers = class_exists(PicketSchedule::class) && Schema::hasTable('picket_schedules')
            ? PicketSchedule::active()->forDay($todayDayName)->with(['teacher', 'user'])->get()
            : collect();

        // 5. Konseling BK Terjadwal Hari Ini
        $todayCounselingSessions = class_exists(BkCounselingSession::class) && Schema::hasTable('bk_counseling_sessions')
            ? BkCounselingSession::with(['student.schoolClass', 'counselor'])
                ->whereDate('date', $today)
                ->take(5)
                ->get()
            : collect();

        return [
            'today_date' => $today,
            'today_formatted' => $todayCarbon->locale('id')->translatedFormat('l, d F Y'),
            'today_day_name' => $todayDayName,
            'learning_agendas' => $todayLearningAgendas,
            'active_holidays' => $activeHolidaysToday,
            'upcoming_events' => $upcomingEvents,
            'picket_officers' => $todayPicketOfficers,
            'counseling_sessions' => $todayCounselingSessions,
            'is_holiday_today' => $activeHolidaysToday->isNotEmpty(),
            'total_learning_agendas' => $todayLearningAgendas->count(),
            'total_counseling' => $todayCounselingSessions->count(),
        ];
    }

    /**
     * Ringkasan & Progres Ibadah Bersama (Dhuha, Dzuhur/Jum'at, Keputrian) & Tahfidz Al-Qur'an Juz 30
     * Barometer berfokus pada jumlah siswa (Hadir, Sakit, Izin, Alpa, Udzur)
     */
    public function getWorshipTahfidzDashboardData(?int $classId = null, ?int $studentId = null): array
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        // Target Populasi Siswa
        $baseStudentQuery = Student::where('is_active', true);
        if ($studentId) {
            $baseStudentQuery->where('id', $studentId);
        } elseif ($classId) {
            $baseStudentQuery->where('class_id', $classId);
        }
        $targetTotalStudents = (clone $baseStudentQuery)->count();
        $targetFemaleStudents = (clone $baseStudentQuery)->where('gender', 'P')->count();

        // 1. Shalat Dhuha Bersama
        $dhuhaQuery = WorshipAttendance::where('activity_type', WorshipAttendance::ACTIVITY_DHUHA);
        if ($studentId) {
            $dhuhaQuery->where('student_id', $studentId);
        } elseif ($classId) {
            $dhuhaQuery->where('class_id', $classId);
        }

        $dhuhaToday = (clone $dhuhaQuery)->whereDate('date', $today)->get();
        $dhuhaTodayHadir = $dhuhaToday->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $dhuhaTodaySakit = $dhuhaToday->where('status', 'SAKIT')->count();
        $dhuhaTodayIzin = $dhuhaToday->where('status', 'IZIN')->count();
        $dhuhaTodayAlpa = $dhuhaToday->where('status', 'ALPA')->count();
        $dhuhaTodayUdzur = $dhuhaToday->whereIn('status', ['UDZUR', 'HALANGAN'])->count();
        $dhuhaRecordedToday = $dhuhaToday->pluck('student_id')->unique()->count();
        $dhuhaBelumAbsenToday = max(0, $targetTotalStudents - $dhuhaRecordedToday);

        $dhuhaTotalAll = (clone $dhuhaQuery)->count();
        $dhuhaHadirAll = (clone $dhuhaQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $dhuhaRate = $targetTotalStudents > 0
            ? round(($dhuhaTodayHadir / $targetTotalStudents) * 100, 1)
            : ($dhuhaTotalAll > 0 ? round(($dhuhaHadirAll / $dhuhaTotalAll) * 100, 1) : 0.0);

        // 2. Shalat Dzuhur / Jum'at Berjamaah
        $dzuhurQuery = WorshipAttendance::where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT);
        if ($studentId) {
            $dzuhurQuery->where('student_id', $studentId);
        } elseif ($classId) {
            $dzuhurQuery->where('class_id', $classId);
        }

        $dzuhurToday = (clone $dzuhurQuery)->whereDate('date', $today)->get();
        $dzuhurTodayHadir = $dzuhurToday->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $dzuhurTodaySakit = $dzuhurToday->where('status', 'SAKIT')->count();
        $dzuhurTodayIzin = $dzuhurToday->where('status', 'IZIN')->count();
        $dzuhurTodayAlpa = $dzuhurToday->where('status', 'ALPA')->count();
        $dzuhurTodayUdzur = $dzuhurToday->whereIn('status', ['UDZUR', 'HALANGAN'])->count();
        $dzuhurRecordedToday = $dzuhurToday->pluck('student_id')->unique()->count();
        $dzuhurBelumAbsenToday = max(0, $targetTotalStudents - $dzuhurRecordedToday);

        $dzuhurTotalAll = (clone $dzuhurQuery)->count();
        $dzuhurHadirAll = (clone $dzuhurQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $dzuhurRate = $targetTotalStudents > 0
            ? round(($dzuhurTodayHadir / $targetTotalStudents) * 100, 1)
            : ($dzuhurTotalAll > 0 ? round(($dzuhurHadirAll / $dzuhurTotalAll) * 100, 1) : 0.0);

        // 3. Kegiatan Keputrian (Hari Jum'at)
        $keputrianQuery = WorshipAttendance::where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN);
        if ($studentId) {
            $keputrianQuery->where('student_id', $studentId);
        } elseif ($classId) {
            $keputrianQuery->where('class_id', $classId);
        }

        $keputrianToday = (clone $keputrianQuery)->whereDate('date', $today)->get();
        $keputrianTodayHadir = $keputrianToday->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $keputrianTodaySakit = $keputrianToday->where('status', 'SAKIT')->count();
        $keputrianTodayIzin = $keputrianToday->where('status', 'IZIN')->count();
        $keputrianTodayAlpa = $keputrianToday->where('status', 'ALPA')->count();
        $keputrianTodayUdzur = $keputrianToday->whereIn('status', ['UDZUR', 'HALANGAN'])->count();
        $keputrianRecordedToday = $keputrianToday->pluck('student_id')->unique()->count();
        $keputrianBelumAbsenToday = max(0, $targetFemaleStudents - $keputrianRecordedToday);

        $keputrianTotalAll = (clone $keputrianQuery)->count();
        $keputrianHadirAll = (clone $keputrianQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $keputrianRate = $targetFemaleStudents > 0
            ? round(($keputrianTodayHadir / $targetFemaleStudents) * 100, 1)
            : ($keputrianTotalAll > 0 ? round(($keputrianHadirAll / $keputrianTotalAll) * 100, 1) : 0.0);

        // 4. Presensi Ekstrakurikuler
        $extraQuery = ExtracurricularAttendance::query();
        if ($studentId) {
            $extraQuery->where('student_id', $studentId);
        } elseif ($classId) {
            $extraQuery->where('class_id', $classId);
        }

        $extraToday = (clone $extraQuery)->whereDate('date', $today)->get();
        $extraTodayHadir = $extraToday->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $extraTodaySakit = $extraToday->where('status', 'SAKIT')->count();
        $extraTodayIzin = $extraToday->where('status', 'IZIN')->count();
        $extraTodayAlpa = $extraToday->where('status', 'ALPA')->count();
        $extraTotalAll = (clone $extraQuery)->count();
        $extraHadirAll = (clone $extraQuery)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $extraRate = $targetTotalStudents > 0
            ? round(($extraTodayHadir / $targetTotalStudents) * 100, 1)
            : ($extraTotalAll > 0 ? round(($extraHadirAll / $extraTotalAll) * 100, 1) : 0.0);

        // 5. Tahfidz Al-Qur'an Juz 30
        $tahfidzQuery = QuranTahfidzRecord::query();
        if ($studentId) {
            $tahfidzQuery->where('student_id', $studentId);
        } elseif ($classId) {
            $tahfidzQuery->where('class_id', $classId);
        }

        $totalTahfidzDeposits = (clone $tahfidzQuery)->count();
        $lulusTahfidzDeposits = (clone $tahfidzQuery)->where('overall_grade', 'Lulus')->count();
        $recentTahfidz = (clone $tahfidzQuery)->with(['student.schoolClass'])->orderByDesc('deposit_date')->orderByDesc('deposit_time')->take(5)->get();

        $targetGrade = null;
        $targetSurahsCount = 37;
        if ($studentId) {
            $studentModel = Student::with('schoolClass')->find($studentId);
            $targetGrade = $studentModel?->getNormalizedGrade() ?: 'X';
            $gradeSurahs = QuranTahfidzRecord::getSurahsByGrade($targetGrade);
            $targetSurahsCount = count($gradeSurahs) ?: 37;
            $completedSurahsCount = (clone $tahfidzQuery)->where('overall_grade', 'Lulus')
                ->whereIn('surah_number', array_keys($gradeSurahs))
                ->distinct('surah_number')
                ->count('surah_number');
            $tahfidzProgressPct = $targetSurahsCount > 0 ? round(($completedSurahsCount / $targetSurahsCount) * 100, 1) : 0.0;
            $latestDeposit = (clone $tahfidzQuery)->orderByDesc('deposit_date')->first();
        } else {
            $completedSurahsCount = $lulusTahfidzDeposits;
            $tahfidzProgressPct = $totalTahfidzDeposits > 0 ? round(($lulusTahfidzDeposits / $totalTahfidzDeposits) * 100, 1) : 0;
            $latestDeposit = null;
        }

        return [
            'dzuhur' => [
                'today_hadir' => $dzuhurTodayHadir,
                'today_sakit' => $dzuhurTodaySakit,
                'today_izin' => $dzuhurTodayIzin,
                'today_alpa' => $dzuhurTodayAlpa,
                'today_udzur' => $dzuhurTodayUdzur,
                'today_total_target' => $targetTotalStudents,
                'today_recorded' => $dzuhurRecordedToday,
                'today_belum_absen' => $dzuhurBelumAbsenToday,
                'today_total' => $dzuhurRecordedToday,
                'today_record' => $studentId ? $dzuhurToday->first() : null,
                'total_records' => $targetTotalStudents,
                'hadir_records' => $dzuhurTodayHadir,
                'rate' => $dzuhurRate,
            ],
            'dhuha' => [
                'today_hadir' => $dhuhaTodayHadir,
                'today_sakit' => $dhuhaTodaySakit,
                'today_izin' => $dhuhaTodayIzin,
                'today_alpa' => $dhuhaTodayAlpa,
                'today_udzur' => $dhuhaTodayUdzur,
                'today_total_target' => $targetTotalStudents,
                'today_recorded' => $dhuhaRecordedToday,
                'today_belum_absen' => $dhuhaBelumAbsenToday,
                'today_total' => $dhuhaRecordedToday,
                'today_record' => $studentId ? $dhuhaToday->first() : null,
                'total_records' => $targetTotalStudents,
                'hadir_records' => $dhuhaTodayHadir,
                'rate' => $dhuhaRate,
            ],
            'keputrian' => [
                'today_hadir' => $keputrianTodayHadir,
                'today_sakit' => $keputrianTodaySakit,
                'today_izin' => $keputrianTodayIzin,
                'today_alpa' => $keputrianTodayAlpa,
                'today_udzur' => $keputrianTodayUdzur,
                'today_total_target' => $targetFemaleStudents,
                'today_recorded' => $keputrianRecordedToday,
                'today_belum_absen' => $keputrianBelumAbsenToday,
                'today_total' => $keputrianRecordedToday,
                'today_record' => $studentId ? $keputrianToday->first() : null,
                'total_records' => $targetFemaleStudents,
                'hadir_records' => $keputrianTodayHadir,
                'rate' => $keputrianRate,
            ],
            'extracurricular' => [
                'today_hadir' => $extraTodayHadir,
                'today_sakit' => $extraTodaySakit,
                'today_izin' => $extraTodayIzin,
                'today_alpa' => $extraTodayAlpa,
                'today_total' => $extraToday->count(),
                'today_record' => $studentId ? $extraToday->first() : null,
                'total_records' => $targetTotalStudents,
                'hadir_records' => $extraTodayHadir,
                'rate' => $extraRate,
            ],
            'tahfidz' => [
                'total_deposits' => $totalTahfidzDeposits,
                'lulus_deposits' => $lulusTahfidzDeposits,
                'completed_surahs_count' => $completedSurahsCount,
                'target_surahs_count' => $targetSurahsCount,
                'student_grade' => $targetGrade,
                'progress_pct' => $tahfidzProgressPct,
                'recent' => $recentTahfidz,
                'latest_deposit' => $latestDeposit,
            ],
        ];
    }

    /**
     * Ringkasan & Metrik Terpadu LMS Pembelajaran & Perpustakaan Digital untuk Dashboard
     */
    public function getLmsLibraryDashboardData(?User $user = null): array
    {
        $user = $user ?? Auth::user();
        $hasLms = false;
        $hasLibrary = false;

        try {
            $hasLms = Schema::hasTable('lms_course_classes') && Schema::hasTable('lms_materials') && Schema::hasTable('lms_assignments');
            $hasLibrary = Schema::hasTable('library_books') && Schema::hasTable('library_loans');
        } catch (\Throwable) {
            $hasLms = false;
            $hasLibrary = false;
        }

        $data = [
            'has_lms' => $hasLms,
            'has_library' => $hasLibrary,
            'lms' => [
                'total_courses' => 0,
                'total_materials' => 0,
                'total_assignments' => 0,
                'total_quizzes' => 0,
                'user_pending_assignments' => collect(),
                'user_upcoming_quizzes' => collect(),
                'teacher_pending_grading_count' => 0,
                'recent_materials' => collect(),
            ],
            'library' => [
                'total_books' => 0,
                'total_copies' => 0,
                'digital_books_count' => 0,
                'active_loans_count' => 0,
                'pending_loans_count' => 0,
                'today_visitors_count' => 0,
                'user_active_loans' => collect(),
                'recent_digital_books' => collect(),
            ],
        ];

        try {
            if ($hasLms) {
                $data['lms']['total_courses'] = CourseClass::where('is_active', true)->count();
                $data['lms']['total_materials'] = LmsMaterial::where('is_published', true)->count();
                $data['lms']['total_assignments'] = LmsAssignment::where('is_published', true)->count();
                $data['lms']['total_quizzes'] = LmsQuiz::where('is_published', true)->count();
                $data['lms']['recent_materials'] = LmsMaterial::with('courseClass.subject')
                    ->where('is_published', true)
                    ->orderByDesc('created_at')
                    ->take(3)
                    ->get();

                if ($user && $user->role === 'siswa' && $user->student) {
                    $classId = $user->student->class_id;
                    $courseClassIds = CourseClass::where('class_id', $classId)->pluck('id');
                    $submittedAssignmentIds = LmsAssignmentSubmission::where('student_id', $user->student->id)->pluck('assignment_id');
                    $data['lms']['user_pending_assignments'] = LmsAssignment::with('courseClass.subject')
                        ->whereIn('course_class_id', $courseClassIds)
                        ->where('is_published', true)
                        ->whereNotIn('id', $submittedAssignmentIds)
                        ->where(function ($q) {
                            $q->whereNull('due_date')->orWhere('due_date', '>=', now());
                        })
                        ->orderBy('due_date')
                        ->take(3)
                        ->get();

                    $data['lms']['user_upcoming_quizzes'] = LmsQuiz::with('courseClass.subject')
                        ->whereIn('course_class_id', $courseClassIds)
                        ->where('is_published', true)
                        ->orderByDesc('created_at')
                        ->take(3)
                        ->get();
                } elseif ($user && in_array($user->role, ['guru', 'wali_kelas', 'wakasek_kurikulum', 'admin', 'super_admin'])) {
                    if ($user->teacher) {
                        $myCourseClassIds = CourseClass::where('teacher_id', $user->teacher->id)->pluck('id');
                        $myAssignmentIds = LmsAssignment::whereIn('course_class_id', $myCourseClassIds)->pluck('id');
                        $data['lms']['teacher_pending_grading_count'] = LmsAssignmentSubmission::whereIn('assignment_id', $myAssignmentIds)
                            ->where('status', 'DIKUMPULKAN')
                            ->count();
                    }
                }
            }

            if ($hasLibrary) {
                $data['library']['total_books'] = LibraryBook::where('is_active', true)->count();
                $data['library']['total_copies'] = (int) LibraryBook::where('is_active', true)->sum('total_copies');
                $data['library']['digital_books_count'] = 0;
                if (Schema::hasColumn('library_books', 'book_type')) {
                    $data['library']['digital_books_count'] = LibraryBook::where('is_active', true)
                        ->where(function ($q) {
                            $q->where('book_type', 'digital');
                            if (Schema::hasColumn('library_books', 'is_digital_flip')) {
                                $q->orWhere('is_digital_flip', true);
                            }
                            if (Schema::hasColumn('library_books', 'digital_file_path')) {
                                $q->orWhereNotNull('digital_file_path');
                            }
                        })->count();
                } elseif (Schema::hasColumn('library_books', 'is_digital_flip')) {
                    $data['library']['digital_books_count'] = LibraryBook::where('is_active', true)
                        ->where('is_digital_flip', true)
                        ->count();
                }
                $data['library']['active_loans_count'] = LibraryLoan::whereIn('status', ['DIPINJAM', 'TERLAMBAT'])->count();
                $data['library']['pending_loans_count'] = LibraryLoan::where('status', 'MENUNGGU')->count();

                if (Schema::hasTable('library_visitors')) {
                    $data['library']['today_visitors_count'] = LibraryVisitor::whereDate('visited_at', Carbon::today())->count();
                }

                $recentDigitalQ = LibraryBook::where('is_active', true);
                if (Schema::hasColumn('library_books', 'book_type') || Schema::hasColumn('library_books', 'is_digital_flip')) {
                    $recentDigitalQ->where(function ($q) {
                        if (Schema::hasColumn('library_books', 'book_type')) {
                            $q->where('book_type', 'digital');
                        }
                        if (Schema::hasColumn('library_books', 'is_digital_flip')) {
                            $q->orWhere('is_digital_flip', true);
                        }
                        if (Schema::hasColumn('library_books', 'digital_file_path')) {
                            $q->orWhereNotNull('digital_file_path');
                        }
                    });
                }
                $data['library']['recent_digital_books'] = $recentDigitalQ
                    ->orderByDesc('id')
                    ->take(4)
                    ->get();

                if ($user) {
                    $member = LibraryMember::where('user_id', $user->id)
                        ->orWhere('student_id', $user->student?->id ?? -1)
                        ->orWhere('teacher_id', $user->teacher?->id ?? -1)
                        ->first();
                    if ($member) {
                        $data['library']['user_active_loans'] = LibraryLoan::with('book')
                            ->where('member_id', $member->id)
                            ->whereIn('status', ['MENUNGGU', 'DISETUJUI', 'DIPINJAM', 'TERLAMBAT'])
                            ->orderByDesc('id')
                            ->take(3)
                            ->get();
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Dashboard LMS/Library data error: '.$e->getMessage());
        }

        return $data;
    }
}

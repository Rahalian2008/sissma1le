<?php

namespace App\Services;

namespace App\Http\Controllers;

use App\Models\BkAkpd;
use App\Models\BkCaseConference;
use App\Models\BkCounselingSession;
use App\Models\BkHomeVisit;
use App\Models\BkParentCall;
use App\Models\BkPsychologicalTestResult;
use App\Models\BkReferral;
use App\Models\BkRpl;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BkDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $activeRole = session('active_role') ?? $user?->active_role;
        $allowedBkRoles = ['koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah', 'admin', 'super_admin', 'wali_kelas'];

        $canAccessBk = in_array($activeRole, $allowedBkRoles, true)
            || ($activeRole === 'all_roles' && $user?->hasAnyRole($allowedBkRoles))
            || ($user?->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']) && in_array($activeRole, array_merge($allowedBkRoles, ['all_roles']), true));

        if (! $canAccessBk) {
            abort(403, 'Akses Bimbingan Konseling hanya diperbolehkan untuk Koordinator BK, Guru BK, Kepala Sekolah, Administrator, dan Wali Kelas.');
        }

        $isHomeroomOnly = $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        $homeroomClass = $isHomeroomOnly ? ($user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first()) : null;
        $classId = $homeroomClass?->id;

        // 1. Metrik 4 Bidang Layanan POP-BK
        $pribadiCount = BkCounselingSession::where('field_of_service', 'PRIBADI')
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $sosialCount = BkCounselingSession::where('field_of_service', 'SOSIAL')
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $belajarCount = BkCounselingSession::where('field_of_service', 'BELAJAR')
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $karierCount = BkCounselingSession::where('field_of_service', 'KARIER')
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $totalSessions = BkCounselingSession::when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();

        // 2. Metrik Responsif & Pendukung
        $activeSessionsCount = BkCounselingSession::whereIn('status', ['TERJADWAL', 'SEDANG_BERLANGSUNG'])
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $homeVisitsCount = BkHomeVisit::when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $parentCallsCount = BkParentCall::when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $totalPsychResults = BkPsychologicalTestResult::when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->count();
        $totalAkpd = $isHomeroomOnly ? 0 : BkAkpd::count();
        $totalRpl = $isHomeroomOnly ? 0 : BkRpl::count();
        $totalCaseConferences = $isHomeroomOnly ? 0 : BkCaseConference::count();
        $totalReferrals = $isHomeroomOnly ? 0 : BkReferral::count();

        // 3. Distribusi Gaya Belajar VAK
        $vakDistribution = [
            'VISUAL' => BkPsychologicalTestResult::where('dominant_dimension', 'VISUAL')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
            'AUDITORI' => BkPsychologicalTestResult::where('dominant_dimension', 'AUDITORI')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
            'KINESTETIK' => BkPsychologicalTestResult::where('dominant_dimension', 'KINESTETIK')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
        ];

        // 4. Distribusi Minat Karier Holland RIASEC
        $riasecDistribution = [
            'R' => BkPsychologicalTestResult::where('dominant_dimension', 'R')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
            'I' => BkPsychologicalTestResult::where('dominant_dimension', 'I')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
            'A' => BkPsychologicalTestResult::where('dominant_dimension', 'A')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
            'S' => BkPsychologicalTestResult::where('dominant_dimension', 'S')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
            'E' => BkPsychologicalTestResult::where('dominant_dimension', 'E')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
            'C' => BkPsychologicalTestResult::where('dominant_dimension', 'C')->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))->count(),
        ];

        // 5. Siswa Prioritas Bimbingan (Siswa dengan akumulasi poin pelanggaran tinggi atau absensi bermasalah)
        $priorityStudents = Student::with(['schoolClass', 'violations'])
            ->where('is_active', true)
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->where('class_id', $classId))
            ->withCount(['violations as verified_violations_sum_points' => function ($query) {
                $query->where('status', 'DIVERIFIKASI')->select(\DB::raw('COALESCE(SUM(points), 0)'));
            }])
            ->orderByDesc('verified_violations_sum_points')
            ->take(8)
            ->get();

        // 6. Sesi Konseling Terbaru
        $recentSessions = BkCounselingSession::with(['student.schoolClass', 'counselor'])
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->latest('date')
            ->take(6)
            ->get();

        // 7. Jadwal Pertemuan Rencana Tindak Lanjut (RTL) Mendatang
        $hasFollowUpDate = Schema::hasColumn('bk_counseling_sessions', 'follow_up_date');
        $upcomingRtlSessions = $hasFollowUpDate
            ? BkCounselingSession::with(['student.schoolClass', 'counselor'])
                ->whereNotNull('follow_up_date')
                ->whereDate('follow_up_date', '>=', now()->toDateString())
                ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
                ->orderBy('follow_up_date', 'asc')
                ->take(6)
                ->get()
            : collect();

        // 8. Hasil Psikotes Terkini
        $recentPsychResults = BkPsychologicalTestResult::with(['student.schoolClass', 'test'])
            ->when($isHomeroomOnly && $classId, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->latest('taken_at')
            ->take(6)
            ->get();

        $classes = ($isHomeroomOnly && $classId) ? SchoolClass::where('id', $classId)->get() : SchoolClass::orderBy('name')->get();

        return view('bk.dashboard', compact(
            'pribadiCount',
            'sosialCount',
            'belajarCount',
            'karierCount',
            'totalSessions',
            'activeSessionsCount',
            'homeVisitsCount',
            'parentCallsCount',
            'totalPsychResults',
            'totalAkpd',
            'totalRpl',
            'totalCaseConferences',
            'totalReferrals',
            'vakDistribution',
            'riasecDistribution',
            'priorityStudents',
            'recentSessions',
            'upcomingRtlSessions',
            'recentPsychResults',
            'classes'
        ));
    }

    /**
     * Profil 360 Derajat Siswa untuk Guru BK
     * Mengintegrasikan data holistik: Pelanggaran, Prestasi, Absensi, Kebiasaan 7 KAIH, Tahfidz, Rapor Akademik, dan Psikotes.
     */
    public function studentProfile360(Student $student): View
    {
        $user = Auth::user();
        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        if ($isHomeroomOnly) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                abort(403, 'Akses terbatas untuk peserta didik di kelas binaan Anda.');
            }
        }

        $student->load([
            'schoolClass',
            'violations.category',
            'achievements.category',
            'counselingSessions.counselor',
            'homeVisits',
            'parentCalls',
            'psychologicalResults.test',
            'user',
        ]);

        // Hitung total poin
        $totalViolationPoints = $student->violations()->where('status', 'DIVERIFIKASI')->sum('points');
        $totalAchievementPoints = $student->achievements()->where('status', 'DIVERIFIKASI')->sum('points');

        // Presensi summary
        $totalAttendances = \DB::table('attendances')
            ->where('student_id', $student->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'HADIR' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'IZIN' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'SAKIT' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'ALPA' THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN status = 'TERLAMBAT' THEN 1 ELSE 0 END) as terlambat
            ")
            ->first();

        // Kebiasaan 7 KAIH rata-rata 30 hari terakhir
        $recentHabitsCount = \DB::table('habit_daily_logs')
            ->where('student_id', $student->id)
            ->where('date', '>=', now()->subDays(30)->toDateString())
            ->where('is_completed', true)
            ->count();

        // Setoran Tahfidz terakhir
        $tahfidzRecords = \DB::table('quran_tahfidz_records')
            ->where('student_id', $student->id)
            ->orderByDesc('deposit_date')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Hasil Psikotes Terkini
        $vakResult = $student->psychologicalResults()->whereHas('test', fn ($q) => $q->where('slug', 'vak'))->latest('taken_at')->first();
        $riasecResult = $student->psychologicalResults()->whereHas('test', fn ($q) => $q->where('slug', 'riasec'))->latest('taken_at')->first();
        $gardnerResult = $student->psychologicalResults()->whereHas('test', fn ($q) => $q->where('slug', 'gardner'))->latest('taken_at')->first();

        return view('bk.profile360', compact(
            'student',
            'totalViolationPoints',
            'totalAchievementPoints',
            'totalAttendances',
            'recentHabitsCount',
            'tahfidzRecords',
            'vakResult',
            'riasecResult',
            'gardnerResult'
        ));
    }
}

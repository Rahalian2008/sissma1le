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
use Illuminate\View\View;

class BkDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $activeRole = session('active_role') ?? $user?->active_role;
        $allowedBkRoles = ['koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah', 'admin', 'super_admin'];

        $canAccessBk = in_array($activeRole, $allowedBkRoles, true)
            || ($activeRole === 'all_roles' && $user?->hasAnyRole($allowedBkRoles))
            || ($user?->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']) && in_array($activeRole, array_merge($allowedBkRoles, ['all_roles']), true));

        if (! $canAccessBk) {
            abort(403, 'Akses Bimbingan Konseling hanya diperbolehkan untuk Koordinator BK, Guru BK, Kepala Sekolah, dan Administrator.');
        }

        // 1. Metrik 4 Bidang Layanan POP-BK
        $pribadiCount = BkCounselingSession::where('field_of_service', 'PRIBADI')->count();
        $sosialCount = BkCounselingSession::where('field_of_service', 'SOSIAL')->count();
        $belajarCount = BkCounselingSession::where('field_of_service', 'BELAJAR')->count();
        $karierCount = BkCounselingSession::where('field_of_service', 'KARIER')->count();
        $totalSessions = BkCounselingSession::count();

        // 2. Metrik Responsif & Pendukung
        $activeSessionsCount = BkCounselingSession::whereIn('status', ['TERJADWAL', 'SEDANG_BERLANGSUNG'])->count();
        $homeVisitsCount = BkHomeVisit::count();
        $parentCallsCount = BkParentCall::count();
        $totalPsychResults = BkPsychologicalTestResult::count();
        $totalAkpd = BkAkpd::count();
        $totalRpl = BkRpl::count();
        $totalCaseConferences = BkCaseConference::count();
        $totalReferrals = BkReferral::count();

        // 3. Distribusi Gaya Belajar VAK
        $vakDistribution = [
            'VISUAL' => BkPsychologicalTestResult::where('dominant_dimension', 'VISUAL')->count(),
            'AUDITORI' => BkPsychologicalTestResult::where('dominant_dimension', 'AUDITORI')->count(),
            'KINESTETIK' => BkPsychologicalTestResult::where('dominant_dimension', 'KINESTETIK')->count(),
        ];

        // 4. Distribusi Minat Karier Holland RIASEC
        $riasecDistribution = [
            'R' => BkPsychologicalTestResult::where('dominant_dimension', 'R')->count(),
            'I' => BkPsychologicalTestResult::where('dominant_dimension', 'I')->count(),
            'A' => BkPsychologicalTestResult::where('dominant_dimension', 'A')->count(),
            'S' => BkPsychologicalTestResult::where('dominant_dimension', 'S')->count(),
            'E' => BkPsychologicalTestResult::where('dominant_dimension', 'E')->count(),
            'C' => BkPsychologicalTestResult::where('dominant_dimension', 'C')->count(),
        ];

        // 5. Siswa Prioritas Bimbingan (Siswa dengan akumulasi poin pelanggaran tinggi atau absensi bermasalah)
        $priorityStudents = Student::with(['schoolClass', 'violations'])
            ->where('is_active', true)
            ->withCount(['violations as verified_violations_sum_points' => function ($query) {
                $query->where('status', 'DIVERIFIKASI')->select(\DB::raw('COALESCE(SUM(points), 0)'));
            }])
            ->orderByDesc('verified_violations_sum_points')
            ->take(8)
            ->get();

        // 6. Sesi Konseling Terbaru
        $recentSessions = BkCounselingSession::with(['student.schoolClass', 'counselor'])
            ->latest('date')
            ->take(6)
            ->get();

        // 7. Hasil Psikotes Terkini
        $recentPsychResults = BkPsychologicalTestResult::with(['student.schoolClass', 'test'])
            ->latest('taken_at')
            ->take(6)
            ->get();

        $classes = SchoolClass::orderBy('name')->get();

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

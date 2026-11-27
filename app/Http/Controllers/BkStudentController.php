<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BkStudentController extends Controller
{
    /**
     * Tampilkan Direktori Data Siswa untuk Bimbingan Konseling & Buku Induk
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        $homeroomClass = $isHomeroomOnly ? ($user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first()) : null;
        $classId = $homeroomClass?->id;

        $query = Student::with(['schoolClass', 'counselingSessions'])
            ->where('is_active', true);

        if ($isHomeroomOnly && $classId) {
            $query->where('class_id', $classId);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($filterClass = $request->query('class_id')) {
            $query->where('class_id', $filterClass);
        }

        if ($gradeLevel = $request->query('grade_level')) {
            $query->where(function ($q) use ($gradeLevel) {
                $q->where('current_class_level', $gradeLevel)
                    ->orWhereHas('schoolClass', fn ($sq) => $sq->where('grade', $gradeLevel));
            });
        }

        $totalActiveStudents = (clone $query)->count();
        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        $classes = ($isHomeroomOnly && $classId)
            ? SchoolClass::where('id', $classId)->get()
            : SchoolClass::orderBy('name')->get();

        return view('bk.students.index', compact(
            'students',
            'classes',
            'totalActiveStudents'
        ));
    }

    /**
     * Tampilan Buku Induk Siswa Digital dalam Modul BK
     */
    public function showBukuInduk(Student $student): View
    {
        $this->authorizeStudentAccess($student);

        $student->load([
            'schoolClass.homeroomTeacher',
            'academicYear',
            'family',
            'parents',
            'mutations',
            'scholarships',
            'alumnus',
            'achievements',
            'violations.category',
            'counselingSessions.counselor',
            'homeVisits',
            'parentCalls',
            'psychologicalResults.test',
            'latestVakResult',
            'latestRiasecResult',
            'habitSummaries',
            'tahfidzRecords',
        ]);

        $school = SchoolIdentity::current();

        // Rekapitulasi Presensi
        $attendanceSummary = DB::table('attendances')
            ->where('student_id', $student->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('HADIR', 'hadir') THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status IN ('IZIN', 'izin') THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status IN ('SAKIT', 'sakit') THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status IN ('ALPA', 'alpa') THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN status IN ('TERLAMBAT', 'terlambat') THEN 1 ELSE 0 END) as terlambat
            ")
            ->first();

        // 7 KAIH Habits count
        $habitsCount = DB::table('habit_daily_logs')
            ->where('student_id', $student->id)
            ->where('is_completed', true)
            ->count();

        $verificationUrl = route('verify.public', ['type' => 'buku-induk', 'token' => $student->qr_code_token ?? $student->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 130);

        AuditTrailService::log('view', 'buku_induk_bk', "Melihat Buku Induk Digital BK: {$student->name} (NIS: {$student->nis})");

        return view('bk.students.show_buku_induk', compact(
            'student',
            'school',
            'attendanceSummary',
            'habitsCount',
            'qrCodeSvg',
            'verificationUrl'
        ));
    }

    /**
     * Cetak Lembar Buku Induk Siswa Resmi & Lengkap untuk BK
     */
    public function printBukuInduk(Student $student): View
    {
        $this->authorizeStudentAccess($student);

        $student->load([
            'schoolClass.homeroomTeacher',
            'academicYear',
            'family',
            'parents',
            'mutations',
            'scholarships',
            'alumnus',
            'achievements',
            'violations.category',
            'counselingSessions.counselor',
            'homeVisits',
            'parentCalls',
            'psychologicalResults.test',
            'latestVakResult',
            'latestRiasecResult',
            'habitSummaries',
            'tahfidzRecords',
        ]);

        $school = SchoolIdentity::current();
        $headmaster = Teacher::getHeadmaster();
        $bkCoordinator = Teacher::getBkCoordinator();

        // Rekapitulasi Presensi
        $attendanceSummary = DB::table('attendances')
            ->where('student_id', $student->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('HADIR', 'hadir') THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status IN ('IZIN', 'izin') THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status IN ('SAKIT', 'sakit') THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status IN ('ALPA', 'alpa') THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN status IN ('TERLAMBAT', 'terlambat') THEN 1 ELSE 0 END) as terlambat
            ")
            ->first();

        // 7 KAIH Habits count
        $habitsCount = DB::table('habit_daily_logs')
            ->where('student_id', $student->id)
            ->where('is_completed', true)
            ->count();

        $verificationUrl = route('verify.public', ['type' => 'buku-induk', 'token' => $student->qr_code_token ?? $student->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 110);

        AuditTrailService::log('print', 'buku_induk_bk', "Mencetak Lembar Buku Induk Siswa BK Lengkap: {$student->name} (NIS: {$student->nis})");

        return view('bk.students.print_buku_induk', compact(
            'student',
            'school',
            'headmaster',
            'bkCoordinator',
            'attendanceSummary',
            'habitsCount',
            'qrCodeSvg',
            'verificationUrl'
        ));
    }

    /**
     * Proteksi otorisasi jika wali kelas hanya bisa mengakses siswa kelas binaannya
     */
    protected function authorizeStudentAccess(Student $student): void
    {
        $user = Auth::user();
        if ($user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah'])) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                abort(403, 'Akses Buku Induk terbatas untuk peserta didik di kelas binaan Anda.');
            }
        }
    }
}

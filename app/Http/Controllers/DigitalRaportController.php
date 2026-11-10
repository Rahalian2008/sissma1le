<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WorshipAttendance;
use App\Services\AiStudentAnalysisService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DigitalRaportController extends Controller
{
    public function __construct(
        protected AiStudentAnalysisService $aiService
    ) {}

    /**
     * Tampilan Interaktif Raport Digital Analisis Perkembangan AI
     */
    public function index(Request $request, $targetStudent = null): View
    {
        $user = Auth::user();
        $student = $targetStudent instanceof Student ? $targetStudent : ($targetStudent ? Student::find($targetStudent) : null);
        $classId = $request->input('class_id', $student?->class_id);

        // Rentang tanggal default: awal semester/bulan sampai hari ini
        $activeYear = AcademicYear::where('is_active', true)->first();
        $defaultStart = $activeYear?->start_date ? $activeYear->start_date->toDateString() : Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();

        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());

        // Role-based scoping
        if ($user->hasAnyRole(['siswa', 'sekretaris_kelas']) && ! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'koordinator_keagamaan'])) {
            $student = $user->student;
            $classes = $student?->schoolClass ? collect([$student->schoolClass]) : collect();
            $classId = $student?->class_id;
            $studentsList = collect();
        } elseif ($user->hasRole('orang_tua') && ! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas'])) {
            $student = $user->parentProfile?->student;
            $classes = $student?->schoolClass ? collect([$student->schoolClass]) : collect();
            $classId = $student?->class_id;
            $studentsList = collect();
        } elseif ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            $classes = $homeroomClass ? collect([$homeroomClass]) : collect();
            $classId = $homeroomClass?->id;

            $studentsQuery = Student::with('schoolClass')
                ->where('class_id', $classId)
                ->where('is_active', true);

            $search = trim((string) $request->input('search'));
            if ($search !== '') {
                $studentsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('initials', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            }

            $studentsList = $studentsQuery->orderBy('name')->get();

            $studentId = $request->input('student_id');
            if ($studentId) {
                $student = Student::where('class_id', $classId)->find($studentId);
            }
            if (! $student && $studentsList->isNotEmpty()) {
                $student = $studentsList->first();
            }
        } else {
            // Admin, Super Admin, Kepala Sekolah, Guru, BK, Kesiswaan, Koordinator Keagamaan
            $classes = SchoolClass::orderBy('name')->get();

            $studentsQuery = Student::with('schoolClass')->where('is_active', true);

            if ($classId) {
                $studentsQuery->where('class_id', $classId);
            }

            $search = trim((string) $request->input('search'));
            if ($search !== '') {
                $studentsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('initials', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            }

            $studentsList = $studentsQuery->orderBy('name')->get();

            if (! $student) {
                $studentId = $request->input('student_id');
                if ($studentId) {
                    $student = Student::find($studentId);
                } elseif ($studentsList->isNotEmpty()) {
                    $student = $studentsList->first();
                } else {
                    $student = Student::where('is_active', true)->orderBy('name')->first();
                }
            }
        }

        // Jalankan Analisis AI Multi-Dimensi jika siswa tersedia
        $analysis = null;
        $periodAchievements = collect();
        $periodViolations = collect();
        $worshipRecords = collect();
        $worshipSummary = [
            'total' => 0,
            'hadir' => 0,
            'dhuha' => 0,
            'dzuhur' => 0,
            'keputrian' => 0,
            'rate' => 0.0,
        ];
        $tahfidzRecords = collect();
        $tahfidzSummary = [
            'completed_surahs_count' => 0,
            'total_juz30' => 37,
            'progress_pct' => 0.0,
            'period_count' => 0,
            'period_lulus' => 0,
            'period_mengulang' => 0,
            'completed_surahs' => collect(),
        ];

        $latestAiRaport = null;
        if ($student) {
            $student->load(['aiRaport', 'schoolClass']);
            $latestAiRaport = $student->aiRaport;
            if (! $latestAiRaport || $request->has('refresh_ai')) {
                $latestAiRaport = $this->aiService->generateAndSaveStudentReport($student, $user?->id, 'SYSTEM');
            }

            $analysis = $this->aiService->analyzeStudent($student, $startDate, $endDate);

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

            $tahfidzRecords = $student->tahfidzRecords()
                ->whereBetween('deposit_date', [$startDate, $endDate])
                ->orderByDesc('deposit_date')
                ->orderByDesc('deposit_time')
                ->get();

            // Ambil surat target sesuai kelas siswa (X=25, XI=7, XII=5 surat)
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
        }

        return view('raport.index', compact(
            'student',
            'latestAiRaport',
            'analysis',
            'classes',
            'classId',
            'studentsList',
            'startDate',
            'endDate',
            'periodAchievements',
            'periodViolations',
            'worshipRecords',
            'worshipSummary',
            'tahfidzRecords',
            'tahfidzSummary',
            'user'
        ));
    }

    /**
     * Generate Raport Digital AI terbaru untuk satu siswa tertentu
     */
    public function generateSingle(Request $request, Student $student)
    {
        $user = Auth::user();
        if ($user->hasRole('siswa') || $user->hasRole('orang_tua') || $user->role === 'siswa' || $user->role === 'orang_tua') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk men-generate Raport AI.');
        }

        try {
            $this->aiService->generateAndSaveStudentReport(
                student: $student,
                generatedById: $user->id,
                generationType: 'MANUAL',
                skipExternalAi: true
            );

            return redirect()->back()->with('success', "⚡ Berhasil men-generate Raport Digital AI terbaru untuk {$student->name} ({$student->nis})!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal men-generate raport: '.$e->getMessage());
        }
    }

    /**
     * Generate Raport Digital AI terbaru untuk seluruh siswa aktif (7 Pilar Ringkasan Keseluruhan Waktu)
     */
    public function generateAll(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('siswa') || $user->hasRole('orang_tua') || $user->role === 'siswa' || $user->role === 'orang_tua') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk men-generate Raport AI massal.');
        }

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $students = Student::where('is_active', true)->with('schoolClass')->get();
        $count = 0;

        foreach ($students as $s) {
            try {
                $this->aiService->generateAndSaveStudentReport(
                    student: $s,
                    generatedById: $user->id,
                    generationType: 'MANUAL',
                    skipExternalAi: true
                );
                $count++;
            } catch (\Throwable $e) {
                // Lanjutkan siswa berikutnya
            }
        }

        return redirect()->back()->with('success', "⚡ Berhasil men-generate Raport Digital AI terbaru untuk {$count} siswa aktif! Ringkasan 8 pilar keseluruhan waktu kini aktif di seluruh portal sekolah.");
    }

    /**
     * Cetak Berkas Resmi Raport Digital (Pilihan Ukuran Kertas A4 & F4 / Folio)
     */
    public function print(Student $student, Request $request): View
    {
        $user = Auth::user();

        // Otorisasi hak akses cetak per role
        if ($user->hasRole('siswa') || $user->role === 'siswa') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        } elseif ($user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'koordinator_keagamaan']) && $user->student && $user->student->id !== $student->id) {
            abort(403, 'Sekretaris kelas hanya berhak mencetak raport digital miliknya sendiri.');
        } elseif ($user->hasRole('orang_tua') && ! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas']) && $user->parentProfile && $user->parentProfile->student_id !== $student->id) {
            abort(403, 'Orang tua hanya berhak mencetak raport digital putra/putrinya.');
        } elseif ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']) && $user->teacher?->homeroomClass && $student->class_id !== $user->teacher->homeroomClass->id) {
            abort(403, 'Wali kelas hanya berhak mencetak raport digital siswa di kelas binaannya.');
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $defaultStart = $activeYear?->start_date ? $activeYear->start_date->toDateString() : Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();

        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
        $paper = strtolower($request->input('paper', 'a4')) === 'f4' ? 'f4' : 'a4';

        $student->load(['aiRaport', 'schoolClass']);
        $latestAiRaport = $student->aiRaport;
        if (! $latestAiRaport) {
            $latestAiRaport = $this->aiService->generateAndSaveStudentReport($student, $user?->id, 'SYSTEM');
        }

        // Analisis Perkembangan AI Multi-Dimensi (8 Pilar) - Gunakan cache / skip external jika proses cetak
        $analysis = $this->aiService->analyzeStudent($student, $startDate, $endDate, false, true);

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

        // Rekapitulasi Setoran Tahfidz Al-Qur'an Juz 30 (sesuai target kelas siswa)
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
            'user'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Violation;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Helper to resolve class scoping by user role:
     * - wali_kelas: locked to homeroomClass
     * - siswa / orang_tua: locked to student's class
     * - others: full access
     */
    protected function resolveScoping(Request $request): array
    {
        $user = Auth::user();
        if ($user->role === 'wali_kelas') {
            $classId = $user->teacher?->homeroomClass?->id;
            $classes = $user->teacher?->homeroomClass ? collect([$user->teacher->homeroomClass]) : collect();
        } elseif (in_array($user->role, ['siswa', 'orang_tua'])) {
            $classId = $user->role === 'siswa' ? $user->student?->class_id : $user->parentProfile?->student?->class_id;
            $classes = collect();
        } else {
            $classId = $request->input('class_id');
            $classes = SchoolClass::all();
        }

        return [$classId, $classes];
    }

    public function index(Request $request): View
    {
        $type = $request->input('type', 'attendance');
        [$classId, $classes] = $this->resolveScoping($request);
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $students = Student::with('schoolClass')
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->get();

        $data = match ($type) {
            'achievements' => $this->reportService->getAchievementReport($classId),
            'violations' => $this->reportService->getViolationReport($classId),
            default => $this->reportService->getAttendanceReport($classId, $startDate, $endDate),
        };

        return view('reports.index', compact('type', 'classes', 'students', 'data', 'startDate', 'endDate', 'classId'));
    }

    public function studentDossier(Student $student): View
    {
        $user = Auth::user();
        if ($user->role === 'wali_kelas' && $user->teacher?->homeroomClass && $student->class_id !== $user->teacher->homeroomClass->id) {
            abort(403, 'Wali kelas hanya dapat mengakses berkas perkembangan siswa di kelas binaannya.');
        } elseif ($user->role === 'siswa' && $user->student && $user->student->id !== $student->id) {
            abort(403, 'Siswa hanya dapat mengakses berkas perkembangannya sendiri.');
        } elseif ($user->role === 'orang_tua' && $user->parentProfile && $user->parentProfile->student_id !== $student->id) {
            abort(403, 'Orang tua hanya dapat mengakses berkas perkembangan putra/putrinya.');
        }

        $dossier = $this->reportService->getStudentProgressDossier($student);
        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');

        return view('reports.student_dossier', compact('student', 'dossier', 'schoolName'));
    }

    public function printStudentDossier(Student $student): View
    {
        $user = Auth::user();
        if ($user->role === 'wali_kelas' && $user->teacher?->homeroomClass && $student->class_id !== $user->teacher->homeroomClass->id) {
            abort(403, 'Wali kelas hanya dapat mencetak berkas perkembangan siswa di kelas binaannya.');
        } elseif ($user->role === 'siswa' && $user->student && $user->student->id !== $student->id) {
            abort(403, 'Siswa hanya dapat mencetak berkas perkembangannya sendiri.');
        } elseif ($user->role === 'orang_tua' && $user->parentProfile && $user->parentProfile->student_id !== $student->id) {
            abort(403, 'Orang tua hanya dapat mencetak berkas perkembangan putra/putrinya.');
        }

        $dossier = $this->reportService->getStudentProgressDossier($student);
        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolCity = SchoolSetting::get('school_city', 'Sukabumi');

        return view('reports.print_student_dossier', compact('student', 'dossier', 'schoolName', 'schoolAddress', 'schoolCity'));
    }

    public function exportCsv(Request $request): Response
    {
        $type = $request->input('type', 'attendance');
        $filename = "laporan_{$type}_".date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');
            // BOM for Excel compatibility with UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'attendance') {
                fputcsv($file, ['No', 'Tanggal', 'Jam', 'NIS', 'Nama Siswa', 'Kelas', 'Metode', 'Status', 'Keterangan']);
                $records = Attendance::with('student.schoolClass')->orderBy('date', 'desc')->get();
                foreach ($records as $idx => $r) {
                    fputcsv($file, [
                        $idx + 1,
                        $r->date->format('Y-m-d'),
                        $r->time,
                        $r->nis,
                        $r->student_name,
                        $r->schoolClass?->name ?? '-',
                        $r->method,
                        $r->status,
                        $r->notes,
                    ]);
                }
            } elseif ($type === 'achievements') {
                fputcsv($file, ['No', 'Kode', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Kategori', 'Judul Prestasi', 'Tingkat', 'Poin', 'Status']);
                $records = Achievement::with(['student.schoolClass', 'category'])->where('status', 'DIVERIFIKASI')->get();
                foreach ($records as $idx => $r) {
                    fputcsv($file, [
                        $idx + 1,
                        $r->achievement_code,
                        $r->date->format('Y-m-d'),
                        $r->student->nis,
                        $r->student->name,
                        $r->schoolClass?->name ?? '-',
                        $r->category?->name ?? '-',
                        $r->title,
                        $r->level,
                        $r->points,
                        $r->status,
                    ]);
                }
            } elseif ($type === 'violations') {
                fputcsv($file, ['No', 'Kode', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Kategori', 'Bentuk Pelanggaran', 'Poin', 'Lokasi', 'Status']);
                $records = Violation::with(['student.schoolClass', 'category'])->where('status', 'DIVERIFIKASI')->get();
                foreach ($records as $idx => $r) {
                    fputcsv($file, [
                        $idx + 1,
                        $r->violation_code,
                        $r->date->format('Y-m-d'),
                        $r->student->nis,
                        $r->student->name,
                        $r->schoolClass?->name ?? '-',
                        $r->category?->name ?? '-',
                        $r->title,
                        $r->points,
                        $r->location,
                        $r->status,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cetak Lembar Laporan Presensi Resmi (A4 Print-Ready)
     */
    public function printAttendance(Request $request): View
    {
        [$classId, $classes] = $this->resolveScoping($request);
        $periodType = $request->input('period_type');

        if ($periodType === 'daily') {
            $targetDate = $request->input('date', Carbon::now('Asia/Jakarta')->toDateString());
            $startDate = $targetDate;
            $endDate = $targetDate;
            $periodTitle = 'Laporan Harian ('.Carbon::parse($targetDate)->translatedFormat('l, d F Y').')';
        } elseif ($periodType === 'weekly') {
            $refDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now('Asia/Jakarta');
            $startDate = $request->input('start_date', $refDate->copy()->startOfWeek()->toDateString());
            $endDate = $request->input('end_date', $refDate->copy()->endOfWeek()->toDateString());
            $periodTitle = 'Laporan Mingguan ('.Carbon::parse($startDate)->translatedFormat('d M Y').' s.d. '.Carbon::parse($endDate)->translatedFormat('d M Y').')';
        } elseif ($periodType === 'monthly') {
            if ($request->input('month') && $request->input('year')) {
                $monthDate = Carbon::createFromDate((int) $request->input('year'), (int) $request->input('month'), 1);
                $startDate = $monthDate->startOfMonth()->toDateString();
                $endDate = $monthDate->endOfMonth()->toDateString();
            } else {
                $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
                $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->endOfMonth()->toDateString());
            }
            $periodTitle = 'Laporan Bulanan ('.Carbon::parse($startDate)->translatedFormat('F Y').')';
        } else {
            $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
            $periodTitle = ($startDate === $endDate)
                ? 'Laporan Harian ('.Carbon::parse($startDate)->translatedFormat('l, d F Y').')'
                : 'Periode: '.Carbon::parse($startDate)->translatedFormat('d F Y').' s.d. '.Carbon::parse($endDate)->translatedFormat('d F Y');
        }

        $selectedClass = $classId ? SchoolClass::with('homeroomTeacher')->find($classId) : null;

        $students = Student::with('schoolClass')
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $studentIds = $students->pluck('id');

        $attendances = Attendance::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $studentRecaps = $students->map(function ($student) use ($attendances) {
            $studentAtts = $attendances->where('student_id', $student->id);
            $hadir = $studentAtts->where('status', 'HADIR')->count();
            $terlambat = $studentAtts->where('status', 'TERLAMBAT')->count();
            $izin = $studentAtts->where('status', 'IZIN')->count();
            $sakit = $studentAtts->where('status', 'SAKIT')->count();
            $alpa = $studentAtts->where('status', 'ALPA')->count();
            $totalRecorded = $hadir + $terlambat + $izin + $sakit + $alpa;
            $presencePct = $totalRecorded > 0 ? round((($hadir + $terlambat) / $totalRecorded) * 100, 1) : 0;

            return [
                'student' => $student,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total' => $totalRecorded,
                'percentage' => $presencePct,
            ];
        });

        $totals = [
            'students' => $students->count(),
            'hadir' => $studentRecaps->sum('hadir'),
            'terlambat' => $studentRecaps->sum('terlambat'),
            'izin' => $studentRecaps->sum('izin'),
            'sakit' => $studentRecaps->sum('sakit'),
            'alpa' => $studentRecaps->sum('alpa'),
            'avg_percentage' => $studentRecaps->count() > 0 ? round($studentRecaps->avg('percentage'), 1) : 0,
        ];

        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolCity = SchoolSetting::get('school_city', 'Sukabumi');
        $schoolBranch = SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
        $schoolEmail = SchoolSetting::get('school_email', 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id');
        $headmasterName = SchoolSetting::get('headmaster_name', 'Drs. H. Sukardi, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip', '19680512 199403 1 004');

        return view('reports.print_attendance', compact(
            'startDate',
            'endDate',
            'periodType',
            'periodTitle',
            'classId',
            'selectedClass',
            'classes',
            'studentRecaps',
            'totals',
            'schoolName',
            'schoolAddress',
            'schoolCity',
            'schoolBranch',
            'schoolEmail',
            'schoolWebsite',
            'headmasterName',
            'headmasterNip'
        ));
    }

    /**
     * Cetak Lembar Laporan Tata Tertib Resmi (Prestasi & Pelanggaran)
     */
    public function printDiscipline(Request $request): View
    {
        [$classId, $classes] = $this->resolveScoping($request);
        $periodType = $request->input('period_type');

        if ($periodType === 'daily') {
            $targetDate = $request->input('date', Carbon::now('Asia/Jakarta')->toDateString());
            $startDate = $targetDate;
            $endDate = $targetDate;
            $periodTitle = 'Laporan Harian ('.Carbon::parse($targetDate)->translatedFormat('l, d F Y').')';
        } elseif ($periodType === 'weekly') {
            $refDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now('Asia/Jakarta');
            $startDate = $request->input('start_date', $refDate->copy()->startOfWeek()->toDateString());
            $endDate = $request->input('end_date', $refDate->copy()->endOfWeek()->toDateString());
            $periodTitle = 'Laporan Mingguan ('.Carbon::parse($startDate)->translatedFormat('d M Y').' s.d. '.Carbon::parse($endDate)->translatedFormat('d M Y').')';
        } elseif ($periodType === 'monthly') {
            if ($request->input('month') && $request->input('year')) {
                $monthDate = Carbon::createFromDate((int) $request->input('year'), (int) $request->input('month'), 1);
                $startDate = $monthDate->startOfMonth()->toDateString();
                $endDate = $monthDate->endOfMonth()->toDateString();
            } else {
                $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
                $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->endOfMonth()->toDateString());
            }
            $periodTitle = 'Laporan Bulanan ('.Carbon::parse($startDate)->translatedFormat('F Y').')';
        } else {
            $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
            $periodTitle = ($startDate === $endDate)
                ? 'Laporan Harian ('.Carbon::parse($startDate)->translatedFormat('l, d F Y').')'
                : 'Periode: '.Carbon::parse($startDate)->translatedFormat('d F Y').' s.d. '.Carbon::parse($endDate)->translatedFormat('d F Y');
        }

        $selectedClass = $classId ? SchoolClass::with('homeroomTeacher')->find($classId) : null;

        $achievements = Achievement::with(['student.schoolClass', 'category', 'item'])
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate])
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->orderBy('date', 'desc')
            ->get();

        $violations = Violation::with(['student.schoolClass', 'category', 'item'])
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate])
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->orderBy('date', 'desc')
            ->get();

        $students = Student::with(['schoolClass'])
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $studentRecaps = $students->map(function ($student) use ($achievements, $violations) {
            $studentAchs = $achievements->where('student_id', $student->id);
            $studentVios = $violations->where('student_id', $student->id);

            return [
                'student' => $student,
                'ach_count' => $studentAchs->count(),
                'ach_points' => $studentAchs->sum('points'),
                'vio_count' => $studentVios->count(),
                'vio_points' => $studentVios->sum('points'),
                'reward_status' => $student->reward_status,
                'guidance_status' => $student->guidance_status,
            ];
        })->filter(fn ($item) => $item['ach_count'] > 0 || $item['vio_count'] > 0)->values();

        $stats = [
            'total_achievements' => $achievements->count(),
            'total_achievement_points' => $achievements->sum('points'),
            'total_violations' => $violations->count(),
            'total_violation_points' => $violations->sum('points'),
        ];

        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolCity = SchoolSetting::get('school_city', 'Sukabumi');
        $schoolBranch = SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
        $schoolEmail = SchoolSetting::get('school_email', 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id');
        $headmasterName = SchoolSetting::get('headmaster_name', 'Drs. H. Sukardi, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip', '19680512 199403 1 004');

        return view('reports.print_discipline', compact(
            'startDate',
            'endDate',
            'periodType',
            'periodTitle',
            'classId',
            'selectedClass',
            'classes',
            'achievements',
            'violations',
            'studentRecaps',
            'stats',
            'schoolName',
            'schoolAddress',
            'schoolCity',
            'schoolBranch',
            'schoolEmail',
            'schoolWebsite',
            'headmasterName',
            'headmasterNip'
        ));
    }

    /**
     * Cetak Lembar Laporan Karakter 7 Kebiasaan Anak Indonesia Hebat
     */
    public function printHabits(Request $request): View
    {
        [$classId, $classes] = $this->resolveScoping($request);
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $selectedClass = $classId ? SchoolClass::with('homeroomTeacher')->find($classId) : null;
        $habits = Habit::orderBy('order_number')->get();

        $students = Student::with('schoolClass')
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $days = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
        $totalPossible = count($habits) * $days;

        $studentIds = $students->pluck('id');
        $logs = HabitDailyLog::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_completed', true)
            ->get();

        $studentPerformances = $students->map(function ($s) use ($logs, $totalPossible, $habits) {
            $sLogs = $logs->where('student_id', $s->id);
            $done = $sLogs->count();
            $pct = $totalPossible > 0 ? round(($done / $totalPossible) * 100, 1) : 0;

            $perHabit = [];
            foreach ($habits as $h) {
                $perHabit[$h->id] = $sLogs->where('habit_id', $h->id)->count();
            }

            return [
                'student' => $s,
                'completed' => $done,
                'target' => $totalPossible,
                'percentage' => $pct,
                'per_habit' => $perHabit,
                'badge' => match (true) {
                    $pct >= 85 => 'Teladan Unggul',
                    $pct >= 70 => 'Konsisten',
                    $pct >= 50 => 'Berkembang',
                    default => 'Perlu Pembiasaan',
                },
            ];
        })->sortByDesc('percentage')->values();

        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolCity = SchoolSetting::get('school_city', 'Sukabumi');
        $schoolBranch = SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
        $schoolEmail = SchoolSetting::get('school_email', 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id');
        $headmasterName = SchoolSetting::get('headmaster_name', 'Drs. H. Sukardi, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip', '19680512 199403 1 004');

        return view('reports.print_habits', compact(
            'startDate',
            'endDate',
            'classId',
            'selectedClass',
            'classes',
            'habits',
            'days',
            'studentPerformances',
            'schoolName',
            'schoolAddress',
            'schoolCity',
            'schoolBranch',
            'schoolEmail',
            'schoolWebsite',
            'headmasterName',
            'headmasterNip'
        ));
    }

    /**
     * Cetak 1 Dokumen Terpadu Resmi (Presensi + Tata Tertib + Karakter 7 Kebiasaan)
     */
    public function printCombined(Request $request): View
    {
        [$classId, $classes] = $this->resolveScoping($request);

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $selectedClass = $classId ? SchoolClass::with('homeroomTeacher')->find($classId) : null;
        $habits = Habit::orderBy('order_number')->get();

        $students = Student::with('schoolClass')
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $studentIds = $students->pluck('id');

        // 1. Presensi Data
        $attendances = Attendance::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // 2. Tata Tertib Data
        $achievements = Achievement::whereIn('student_id', $studentIds)
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $violations = Violation::whereIn('student_id', $studentIds)
            ->where('status', 'DIVERIFIKASI')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // 3. Karakter 7 Kebiasaan Data
        $days = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
        $totalPossibleHabits = count($habits) * $days;

        $habitLogs = HabitDailyLog::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_completed', true)
            ->get();

        // Combine per student
        $combinedRecaps = $students->map(function ($student) use (
            $attendances, $achievements, $violations, $habitLogs, $totalPossibleHabits, $habits
        ) {
            // Attendance
            $sAtts = $attendances->where('student_id', $student->id);
            $hadir = $sAtts->where('status', 'HADIR')->count();
            $terlambat = $sAtts->where('status', 'TERLAMBAT')->count();
            $izin = $sAtts->where('status', 'IZIN')->count();
            $sakit = $sAtts->where('status', 'SAKIT')->count();
            $alpa = $sAtts->where('status', 'ALPA')->count();
            $totalAtt = $hadir + $terlambat + $izin + $sakit + $alpa;
            $presencePct = $totalAtt > 0 ? round((($hadir + $terlambat) / $totalAtt) * 100, 1) : 0;

            // Discipline
            $sAchs = $achievements->where('student_id', $student->id);
            $sVios = $violations->where('student_id', $student->id);
            $achCount = $sAchs->count();
            $achPoints = $sAchs->sum('points');
            $vioCount = $sVios->count();
            $vioPoints = $sVios->sum('points');

            // Habits
            $sHLogs = $habitLogs->where('student_id', $student->id);
            $habitsDone = $sHLogs->count();
            $habitsPct = $totalPossibleHabits > 0 ? round(($habitsDone / $totalPossibleHabits) * 100, 1) : 0;

            $perHabit = [];
            foreach ($habits as $h) {
                $perHabit[$h->id] = $sHLogs->where('habit_id', $h->id)->count();
            }

            return [
                'student' => $student,
                // Presensi
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'presence_pct' => $presencePct,
                // Tata Tertib
                'ach_count' => $achCount,
                'ach_points' => $achPoints,
                'vio_count' => $vioCount,
                'vio_points' => $vioPoints,
                'reward_status' => $student->reward_status,
                'guidance_status' => $student->guidance_status,
                // Karakter
                'habits_done' => $habitsDone,
                'habits_target' => $totalPossibleHabits,
                'habits_pct' => $habitsPct,
                'per_habit' => $perHabit,
                'habit_badge' => match (true) {
                    $habitsPct >= 85 => 'Teladan',
                    $habitsPct >= 70 => 'Konsisten',
                    $habitsPct >= 50 => 'Berkembang',
                    default => 'Perlu Pembiasaan',
                },
            ];
        });

        $totals = [
            'students' => $students->count(),
            'hadir' => $combinedRecaps->sum('hadir'),
            'terlambat' => $combinedRecaps->sum('terlambat'),
            'izin' => $combinedRecaps->sum('izin'),
            'sakit' => $combinedRecaps->sum('sakit'),
            'alpa' => $combinedRecaps->sum('alpa'),
            'ach_count' => $achievements->count(),
            'ach_points' => $achievements->sum('points'),
            'vio_count' => $violations->count(),
            'vio_points' => $violations->sum('points'),
            'avg_presence' => $combinedRecaps->count() > 0 ? round($combinedRecaps->avg('presence_pct'), 1) : 0,
            'avg_habits' => $combinedRecaps->count() > 0 ? round($combinedRecaps->avg('habits_pct'), 1) : 0,
        ];

        $schoolName = SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong');
        $schoolAddress = SchoolSetting::get('school_address', 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat');
        $schoolCity = SchoolSetting::get('school_city', 'Sukabumi');
        $schoolBranch = SchoolSetting::get('school_branch', 'CABANG DINAS PENDIDIKAN WILAYAH V');
        $schoolEmail = SchoolSetting::get('school_email', 'sman1lengkong@gmail.com');
        $schoolWebsite = SchoolSetting::get('school_website', 'https://sman1lengkong.sch.id');
        $headmasterName = SchoolSetting::get('headmaster_name', 'Drs. H. Sukardi, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip', '19680512 199403 1 004');

        return view('reports.print_combined', compact(
            'startDate',
            'endDate',
            'classId',
            'selectedClass',
            'classes',
            'habits',
            'days',
            'combinedRecaps',
            'totals',
            'schoolName',
            'schoolAddress',
            'schoolCity',
            'schoolBranch',
            'schoolEmail',
            'schoolWebsite',
            'headmasterName',
            'headmasterNip'
        ));
    }
}

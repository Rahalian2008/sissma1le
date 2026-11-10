<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\QuranTahfidzRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Violation;
use App\Models\WorshipAttendance;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get Attendance Report filtered by class and date range.
     */
    public function getAttendanceReport(?int $classId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        app(AttendanceService::class)->ensureDailyAttendanceRecords();

        $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now()->endOfMonth()->toDateString();

        $query = Attendance::with(['student.schoolClass'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $records = $query->orderBy('date', 'desc')->orderBy('time', 'asc')->get();

        $stats = [
            'total' => $records->count(),
            'hadir' => $records->where('status', 'HADIR')->count(),
            'terlambat' => $records->where('status', 'TERLAMBAT')->count(),
            'izin' => $records->where('status', 'IZIN')->count(),
            'sakit' => $records->where('status', 'SAKIT')->count(),
            'alpa' => $records->where('status', 'ALPA')->count(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        return [
            'stats' => $stats,
            'records' => $records,
            'classes' => SchoolClass::all(),
            'selected_class' => $classId ? SchoolClass::find($classId) : null,
        ];
    }

    /**
     * Get Achievements Report.
     */
    public function getAchievementReport(?int $classId = null, ?int $categoryId = null): array
    {
        $query = Achievement::with(['student.schoolClass', 'category', 'item', 'verifier'])
            ->where('status', 'DIVERIFIKASI');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $records = $query->orderBy('date', 'desc')->get();

        return [
            'total_points' => (int) $records->sum('points'),
            'total_count' => $records->count(),
            'records' => $records,
        ];
    }

    /**
     * Get Violations Report.
     */
    public function getViolationReport(?int $classId = null, ?int $categoryId = null): array
    {
        $query = Violation::with(['student.schoolClass', 'category', 'item', 'verifier'])
            ->where('status', 'DIVERIFIKASI');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $records = $query->orderBy('date', 'desc')->get();

        return [
            'total_points' => (int) $records->sum('points'),
            'total_count' => $records->count(),
            'records' => $records,
        ];
    }

    /**
     * Get Comprehensive Student Progress Dossier (Terintegrasi Raport Digital AI).
     */
    public function getStudentProgressDossier(Student $student): array
    {
        $aiService = app(AiStudentAnalysisService::class);
        $student->load(['schoolClass.homeroomTeacher', 'parents', 'aiRaport', 'extracurriculars']);

        $latestAiRaport = $student->aiRaport;
        if (! $latestAiRaport) {
            $latestAiRaport = $aiService->generateAndSaveStudentReport($student, null, 'SYSTEM');
        }

        $aiAnalysis = $aiService->analyzeStudent($student);

        $attendances = $student->attendances()->orderByDesc('date')->take(30)->get();
        $achievements = $student->achievements()->with(['category', 'item'])->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get();
        $violations = $student->violations()->with(['category', 'item'])->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get();
        $guidances = $student->guidanceRecords()->with('counselor')->orderByDesc('date')->get();
        $habitLogs = $student->habitDailyLogs()->with('habit')->where('date', Carbon::today()->toDateString())->get();

        // Rekapitulasi Ibadah Bersama
        $worshipRecords = $student->worshipAttendances()->orderByDesc('date')->take(50)->get();
        $worshipSummary = [
            'total' => $student->worshipAttendances()->count(),
            'hadir' => $student->worshipAttendances()->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'dhuha' => $student->worshipAttendances()->where('activity_type', WorshipAttendance::ACTIVITY_DHUHA)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'dzuhur' => $student->worshipAttendances()->where('activity_type', WorshipAttendance::ACTIVITY_DZUHUR_JUMAT)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'keputrian' => $student->worshipAttendances()->where('activity_type', WorshipAttendance::ACTIVITY_KEPUTRIAN)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count(),
            'rate' => $latestAiRaport?->worship_dzuhur_rate ?? ($aiAnalysis['metrics']['worship_dzuhur_rate'] ?? 0.0),
        ];

        // Rekapitulasi Tahfidz Juz 30 sesuai target kelas
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
            'status' => $latestAiRaport?->tahfidz_status ?? ($aiAnalysis['metrics']['tahfidz_status'] ?? 'Pemula'),
            'completed_surahs' => $completedSurahs,
        ];

        return [
            'student' => $student,
            'latest_ai_raport' => $latestAiRaport,
            'latestAiRaport' => $latestAiRaport,
            'ai_analysis' => $aiAnalysis,
            'attendances' => $attendances,
            'achievements' => $achievements,
            'violations' => $violations,
            'guidances' => $guidances,
            'habit_logs' => $habitLogs,
            'worship_records' => $worshipRecords,
            'worship_summary' => $worshipSummary,
            'tahfidz_summary' => $tahfidzSummary,
            'reward_status' => $student->reward_status,
            'guidance_status' => $student->guidance_status,
        ];
    }

    /**
     * Get Aggregated Attendance Recapitulation for all classes without individual student names.
     */
    public function getClassAttendanceRecap(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now('Asia/Jakarta')->toDateString();

        if ($startDate === $endDate) {
            app(AttendanceService::class)->ensureDailyAttendanceRecords($startDate);
        } else {
            app(AttendanceService::class)->ensureDailyAttendanceRecords();
        }

        $classes = SchoolClass::with(['homeroomTeacher', 'students' => function ($q) {
            $q->where('is_active', true);
        }])->orderBy('name')->get();

        $attendances = Attendance::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get(['id', 'student_id', 'class_id', 'status', 'date']);

        $classRecaps = $classes->map(function ($class) use ($attendances) {
            $studentCount = $class->students->count();
            $studentIds = $class->students->pluck('id');
            $classAtts = $attendances->filter(fn ($a) => $a->class_id == $class->id || $studentIds->contains($a->student_id));

            $hadir = $classAtts->where('status', 'HADIR')->count();
            $terlambat = $classAtts->where('status', 'TERLAMBAT')->count();
            $izin = $classAtts->where('status', 'IZIN')->count();
            $sakit = $classAtts->where('status', 'SAKIT')->count();
            $alpa = $classAtts->where('status', 'ALPA')->count();
            $totalRecorded = $hadir + $terlambat + $izin + $sakit + $alpa;
            $percentage = $totalRecorded > 0 ? round((($hadir + $terlambat) / $totalRecorded) * 100, 1) : 0.0;

            return [
                'class' => $class,
                'class_id' => $class->id,
                'class_name' => $class->name,
                'level' => $class->grade,
                'major' => $class->major,
                'homeroom_teacher' => $class->homeroomTeacher?->name ?? '-',
                'homeroom_nip' => $class->homeroomTeacher?->nip ?? '-',
                'student_count' => $studentCount,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total_records' => $totalRecorded,
                'percentage' => $percentage,
            ];
        });

        $totals = [
            'classes_count' => $classes->count(),
            'student_count' => $classRecaps->sum('student_count'),
            'hadir' => $classRecaps->sum('hadir'),
            'terlambat' => $classRecaps->sum('terlambat'),
            'izin' => $classRecaps->sum('izin'),
            'sakit' => $classRecaps->sum('sakit'),
            'alpa' => $classRecaps->sum('alpa'),
            'total_records' => $classRecaps->sum('total_records'),
            'avg_percentage' => $classRecaps->count() > 0 ? round($classRecaps->avg('percentage'), 1) : 0.0,
        ];

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'class_recaps' => $classRecaps,
            'totals' => $totals,
        ];
    }
}

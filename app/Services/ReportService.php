<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Violation;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get Attendance Report filtered by class and date range.
     */
    public function getAttendanceReport(?int $classId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? Carbon::now()->endOfMonth()->toDateString();

        $query = Attendance::with(['student.schoolClass'])
            ->whereBetween('date', [$startDate, $endDate]);

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
     * Get Comprehensive Student Progress Dossier.
     */
    public function getStudentProgressDossier(Student $student): array
    {
        $aiService = app(AiStudentAnalysisService::class);
        $aiAnalysis = $aiService->analyzeStudent($student);

        $attendances = $student->attendances()->orderByDesc('date')->take(30)->get();
        $achievements = $student->achievements()->with(['category', 'item'])->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get();
        $violations = $student->violations()->with(['category', 'item'])->where('status', 'DIVERIFIKASI')->orderByDesc('date')->get();
        $guidances = $student->guidanceRecords()->with('counselor')->orderByDesc('date')->get();
        $habitLogs = $student->habitDailyLogs()->with('habit')->where('date', Carbon::today()->toDateString())->get();

        return [
            'student' => $student->load(['schoolClass.homeroomTeacher', 'parents']),
            'ai_analysis' => $aiAnalysis,
            'attendances' => $attendances,
            'achievements' => $achievements,
            'violations' => $violations,
            'guidances' => $guidances,
            'habit_logs' => $habitLogs,
            'reward_status' => $student->reward_status,
            'guidance_status' => $student->guidance_status,
        ];
    }
}

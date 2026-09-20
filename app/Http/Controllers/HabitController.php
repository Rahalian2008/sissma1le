<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Habit;
use App\Models\HabitDailyLog;
use App\Models\HabitNote;
use App\Models\HabitSummary;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HabitController extends Controller
{
    /**
     * Tampilan Jurnal Harian 7 Kebiasaan Anak Indonesia Hebat
     */
    public function journal(Request $request): View
    {
        $user = Auth::user();
        $date = $request->input('date', Carbon::today()->toDateString());

        $student = null;
        if ($user->role === 'siswa') {
            $student = $user->student ?? Student::first();
        } elseif ($user->role === 'orang_tua') {
            $student = $user->parentProfile?->student ?? Student::first();
        } elseif ($user->role === 'wali_kelas') {
            $homeroomClass = $user->teacher?->homeroomClass;
            $studentId = $request->input('student_id');
            $student = $studentId ? Student::where('class_id', $homeroomClass?->id)->find($studentId) : ($homeroomClass?->students()->first());
        } else {
            // For teacher/admin/BK viewing a student
            $studentId = $request->input('student_id');
            $student = $studentId ? Student::find($studentId) : Student::first();
        }

        $habits = Habit::orderBy('order_number')->get();
        $dailyLogs = $student ? HabitDailyLog::where('student_id', $student->id)
            ->where('date', $date)
            ->get()
            ->keyBy('habit_id') : collect();

        $notes = $student ? HabitNote::with('author')
            ->where('student_id', $student->id)
            ->where('date', $date)
            ->orderByDesc('created_at')
            ->get() : collect();

        $completedCount = $dailyLogs->where('is_completed', true)->count();
        $progressPct = count($habits) > 0 ? round(($completedCount / count($habits)) * 100) : 0;

        $allStudents = match ($user->role) {
            'wali_kelas' => $user->teacher?->homeroomClass ? $user->teacher->homeroomClass->students : collect(),
            'siswa', 'orang_tua' => collect(),
            default => in_array($user->role, ['guru', 'bk', 'kesiswaan', 'admin', 'super_admin'])
                ? Student::with('schoolClass')->get()
                : collect(),
        };

        return view('habits.journal', compact('habits', 'dailyLogs', 'student', 'date', 'notes', 'completedCount', 'progressPct', 'allStudents'));
    }

    /**
     * Simpan / Toggle Status Jurnal Kebiasaan Siswa
     */
    /**
     * Simpan / Toggle Status Jurnal Kebiasaan Siswa
     */
    public function updateLog(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'habit_id' => ['required', 'exists:habits,id'],
            'date' => ['required', 'date'],
            'is_completed' => ['required', 'boolean'],
            'check_time' => ['nullable', 'date_format:H:i'],
            'activity_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'reflection' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $student = $user->student ?? Student::first();
        $habit = Habit::findOrFail($validated['habit_id']);
        $targetDate = Carbon::parse($validated['date'])->toDateString();
        $todayDate = Carbon::today('Asia/Jakarta')->toDateString();

        // Validasi penguncian waktu khusus untuk akun Siswa
        if ($user->role === 'siswa') {
            if ($validated['is_completed'] && $targetDate !== $todayDate) {
                $msg = 'Pengisian jurnal kebiasaan mandiri siswa hanya dapat dilakukan pada tanggal hari ini.';

                return $request->wantsJson()
                    ? response()->json(['success' => false, 'message' => $msg], 422)
                    : back()->with('error', $msg);
            }

            if ($validated['is_completed'] && ! $habit->isWithinTimeWindow()) {
                $status = $habit->getTimeWindowStatus();
                $msg = "Pembiasaan '{$habit->name}' hanya dapat diceklis pada rentang waktu {$status['range']}. Saat ini: {$status['message']}.";

                return $request->wantsJson()
                    ? response()->json(['success' => false, 'message' => $msg], 422)
                    : back()->with('error', $msg);
            }
        }

        $log = HabitDailyLog::where('student_id', $student->id)
            ->where('habit_id', $validated['habit_id'])
            ->whereDate('date', $targetDate)
            ->first();

        $attributes = [
            'is_completed' => $validated['is_completed'],
            'check_time' => $validated['check_time'] ?? ($validated['is_completed'] ? now('Asia/Jakarta')->format('H:i:s') : null),
            'activity_name' => $validated['activity_name'] ?? ($log?->activity_name ?? $habit->default_activity),
            'notes' => $validated['notes'] ?? ($log?->notes ?? null),
            'reflection' => $validated['reflection'] ?? ($log?->reflection ?? null),
        ];

        if ($log) {
            $log->update($attributes);
        } else {
            $log = HabitDailyLog::create(array_merge([
                'student_id' => $student->id,
                'habit_id' => $validated['habit_id'],
                'date' => $targetDate,
            ], $attributes));
        }

        // Update Weekly Summary
        $this->updateHabitSummary($student, $validated['date']);

        AuditLog::log('UPDATE_HABIT_LOG', 'HabitDailyLog', $log->id, null, [
            'habit_id' => $validated['habit_id'],
            'is_completed' => $validated['is_completed'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jurnal kebiasaan berhasil disimpan.',
                'log' => $log,
            ]);
        }

        return back()->with('success', 'Jurnal kebiasaan berhasil disimpan.');
    }

    /**
     * Tampilan Rekapitulasi Progres 7 Kebiasaan (Ringkasan Mingguan & Bulanan)
     */
    public function summary(Request $request): View
    {
        $user = Auth::user();
        $mode = $request->input('mode', 'weekly');
        if (! in_array($mode, ['weekly', 'monthly'])) {
            $mode = 'weekly';
        }

        $student = null;
        if ($user->role === 'siswa') {
            $student = $user->student ?? Student::first();
        } elseif ($user->role === 'orang_tua') {
            $student = $user->parentProfile?->student ?? Student::first();
        } else {
            $studentId = $request->input('student_id');
            if ($user->role === 'wali_kelas') {
                $homeroomClass = $user->teacher?->homeroomClass;
                $student = $studentId ? Student::where('class_id', $homeroomClass?->id)->find($studentId) : ($homeroomClass?->students()->first());
            } else {
                $student = $studentId ? Student::find($studentId) : Student::first();
            }
        }

        $habits = Habit::orderBy('order_number')->get();
        $daysCount = $mode === 'weekly' ? 7 : 30;
        $today = Carbon::today();

        // Tren harian sesuai mode (7 hari untuk mingguan, 30 hari untuk bulanan)
        $trendDays = collect();
        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i)->toDateString();
            $count = $student ? HabitDailyLog::where('student_id', $student->id)
                ->where('date', $d)
                ->where('is_completed', true)
                ->count() : 0;

            $trendDays->push([
                'date' => $d,
                'label' => Carbon::parse($d)->translatedFormat($mode === 'weekly' ? 'D, d M' : 'd M'),
                'completed' => $count,
                'percentage' => round(($count / max(1, count($habits))) * 100),
            ]);
        }

        // Per-kebiasaan rate dalam periode aktif
        $habitStats = [];
        foreach ($habits as $h) {
            $done = $student ? HabitDailyLog::where('student_id', $student->id)
                ->where('habit_id', $h->id)
                ->where('date', '>=', $today->copy()->subDays($daysCount)->toDateString())
                ->where('is_completed', true)
                ->count() : 0;

            $pct = round(($done / $daysCount) * 100);
            $habitStats[] = [
                'habit' => $h,
                'completed_days' => $done,
                'days_count' => $daysCount,
                'percentage' => $pct,
                'status' => match (true) {
                    $pct >= 85 => 'KONSISTEN',
                    $pct >= 70 => 'BERKEMBANG',
                    $pct >= 50 => 'PERLU PEMBIASAAN',
                    default => 'BELUM TERPANTAU',
                },
            ];
        }

        $allStudents = match ($user->role) {
            'wali_kelas' => $user->teacher?->homeroomClass ? $user->teacher->homeroomClass->students : collect(),
            'siswa', 'orang_tua' => collect(),
            default => in_array($user->role, ['guru', 'bk', 'kesiswaan', 'admin', 'super_admin'])
                ? Student::with('schoolClass')->get()
                : collect(),
        };

        return view('habits.summary', compact('student', 'habits', 'trendDays', 'habitStats', 'allStudents', 'mode'));
    }

    /**
     * Tambah Catatan Pembiasaan oleh Wali Kelas / Guru / Orang Tua
     */
    public function storeNote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        HabitNote::create([
            'student_id' => $validated['student_id'],
            'date' => $validated['date'],
            'author_id' => $user->id,
            'author_role' => $user->role,
            'comment' => $validated['comment'],
        ]);

        AuditLog::log('ADD_HABIT_NOTE', 'Student', $validated['student_id'], null, [
            'author' => $user->name,
            'role' => $user->role,
        ]);

        return back()->with('success', 'Catatan pembiasaan berhasil dikirimkan.');
    }

    /**
     * Helper to compute and update weekly habit summary
     */
    protected function updateHabitSummary(Student $student, string $dateString): void
    {
        $date = Carbon::parse($dateString);
        $periodKey = $date->format('Y').'-W'.$date->weekOfYear;

        $startOfWeek = $date->copy()->startOfWeek()->startOfDay();
        $endOfWeek = $date->copy()->endOfWeek()->endOfDay();

        $completed = HabitDailyLog::where('student_id', $student->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('is_completed', true)
            ->count();

        $totalExpected = 7 * 7; // 7 habits x 7 days
        $pct = round(($completed / $totalExpected) * 100, 1);

        $status = match (true) {
            $pct >= 85 => 'KONSISTEN',
            $pct >= 70 => 'BERKEMBANG',
            $pct >= 50 => 'PERLU_PEMBIASAAN',
            default => 'BELUM_TERPANTAU',
        };

        HabitSummary::updateOrCreate(
            ['student_id' => $student->id, 'period_type' => 'WEEKLY', 'period_key' => $periodKey],
            [
                'completed_count' => $completed,
                'total_habits' => $totalExpected,
                'score_percentage' => $pct,
                'status' => $status,
            ]
        );
    }

    /**
     * Tampilan Diagram & Persentase Perkembangan Karakter 7 Kebiasaan Anak Hebat
     */
    public function charts(Request $request): View
    {
        $user = Auth::user();
        $days = (int) $request->input('days', 30);
        if ($days < 7 || $days > 90) {
            $days = 30;
        }

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

        $search = $request->input('search');
        $habits = Habit::orderBy('order_number')->get();
        $startDate = Carbon::today()->subDays($days - 1)->toDateString();
        $endDate = Carbon::today()->toDateString();

        // Query students
        $studentsQuery = Student::with('schoolClass')->where('is_active', true);
        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        }
        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }
        $students = $studentsQuery->get();
        $studentIds = $students->pluck('id');
        $studentCount = max(1, $students->count());

        // 1. Overall & Per-Habit Completion Rate (Pilar 1 - 7)
        $habitBreakdowns = [];
        $totalPossibleLogs = $studentCount * $days;

        foreach ($habits as $h) {
            $completedCount = HabitDailyLog::whereIn('student_id', $studentIds)
                ->where('habit_id', $h->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('is_completed', true)
                ->count();

            $percentage = $totalPossibleLogs > 0 ? round(($completedCount / $totalPossibleLogs) * 100, 1) : 0;
            $habitBreakdowns[] = [
                'habit' => $h,
                'completed_count' => $completedCount,
                'target_total' => $totalPossibleLogs,
                'percentage' => $percentage,
                'status' => match (true) {
                    $percentage >= 80 => 'Sangat Baik',
                    $percentage >= 65 => 'Berkembang Baik',
                    $percentage >= 50 => 'Cukup',
                    default => 'Perlu Pembiasaan',
                },
                'color' => match ($h->order_number) {
                    1 => 'amber',
                    2 => 'emerald',
                    3 => 'blue',
                    4 => 'rose',
                    5 => 'purple',
                    6 => 'indigo',
                    default => 'cyan',
                },
            ];
        }

        // Overall Average Consistency
        $avgConsistency = count($habitBreakdowns) > 0 ? round(collect($habitBreakdowns)->avg('percentage'), 1) : 0;

        // 2. Class Comparison Rates
        $classComparisons = [];
        foreach ($classes as $c) {
            $cStudentIds = Student::where('class_id', $c->id)->where('is_active', true)->pluck('id');
            $cCount = $cStudentIds->count();
            if ($cCount > 0) {
                $cPossible = $cCount * $days * max(1, count($habits));
                $cDone = HabitDailyLog::whereIn('student_id', $cStudentIds)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('is_completed', true)
                    ->count();
                $cPct = round(($cDone / $cPossible) * 100, 1);
                $classComparisons[] = [
                    'class_name' => $c->name,
                    'student_count' => $cCount,
                    'percentage' => $cPct,
                ];
            }
        }

        // 3. Top Consistent Students (Leaderboard Karakter Hebat)
        $studentPerformances = $students->map(function ($s) use ($habits, $startDate, $endDate, $days) {
            $possible = max(1, count($habits) * $days);
            $done = HabitDailyLog::where('student_id', $s->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('is_completed', true)
                ->count();
            $pct = round(($done / $possible) * 100, 1);

            return [
                'student' => $s,
                'completed' => $done,
                'percentage' => $pct,
                'badge' => match (true) {
                    $pct >= 85 => 'Teladan Unggul',
                    $pct >= 70 => 'Konsisten',
                    $pct >= 50 => 'Berkembang',
                    default => 'Perlu Pembiasaan',
                },
            ];
        })->sortByDesc('percentage')->values();

        return view('habits.charts', compact(
            'classes',
            'habits',
            'habitBreakdowns',
            'avgConsistency',
            'classComparisons',
            'studentPerformances',
            'classId',
            'search',
            'days',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Laporan Capaian 7 Kebiasaan Siswa per Kelas (Cetak & Tinjauan)
     */
    public function report(Request $request): View
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

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

        $selectedClass = $classId ? SchoolClass::with('homeroomTeacher')->find($classId) : null;
        $habits = Habit::orderBy('order_number')->get();

        $studentsQuery = Student::with('schoolClass')->where('is_active', true);
        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        }
        $students = $studentsQuery->orderBy('name')->get();

        $days = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
        $totalPossiblePerStudent = count($habits) * $days;

        $studentIds = $students->pluck('id');
        $logs = HabitDailyLog::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_completed', true)
            ->get();

        $studentReports = $students->map(function ($s) use ($logs, $totalPossiblePerStudent, $habits) {
            $sLogs = $logs->where('student_id', $s->id);
            $done = $sLogs->count();
            $pct = $totalPossiblePerStudent > 0 ? round(($done / $totalPossiblePerStudent) * 100, 1) : 0;

            $perHabit = [];
            foreach ($habits as $h) {
                $perHabit[$h->id] = $sLogs->where('habit_id', $h->id)->count();
            }

            return [
                'student' => $s,
                'completed' => $done,
                'target' => $totalPossiblePerStudent,
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

        return view('habits.report', compact(
            'classes',
            'selectedClass',
            'classId',
            'habits',
            'studentReports',
            'startDate',
            'endDate',
            'days'
        ));
    }

    /**
     * Pengaturan Target Waktu, Aktivitas, & Refleksi 7 Kebiasaan (Admin & Super Admin)
     */
    public function updateTargetTimes(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== 'super_admin') {
            abort(403, 'Hanya Super Admin yang memiliki hak akses mengatur konfigurasi 7 Kebiasaan.');
        }

        $validated = $request->validate([
            'targets' => ['required', 'array'],
            'targets.*.id' => ['required', 'exists:habits,id'],
            'targets.*.start_time' => ['nullable', 'string', 'max:10'],
            'targets.*.end_time' => ['nullable', 'string', 'max:10'],
            'targets.*.target_time' => ['nullable', 'string', 'max:100'],
            'targets.*.default_activity' => ['nullable', 'string', 'max:255'],
            'targets.*.reflection_prompt' => ['nullable', 'string'],
            'targets.*.is_time_restricted' => ['nullable', 'boolean'],
        ]);

        foreach ($validated['targets'] as $item) {
            $startTime = ! empty($item['start_time']) ? trim($item['start_time']) : null;
            $endTime = ! empty($item['end_time']) ? trim($item['end_time']) : null;

            $computedTarget = ($startTime && $endTime)
                ? "{$startTime} - {$endTime} WIB"
                : ($item['target_time'] ?? null);

            Habit::where('id', $item['id'])->update([
                'start_time' => $startTime,
                'end_time' => $endTime,
                'target_time' => $computedTarget,
                'default_activity' => $item['default_activity'] ?? null,
                'reflection_prompt' => $item['reflection_prompt'] ?? null,
                'is_time_restricted' => isset($item['is_time_restricted']) ? (bool) $item['is_time_restricted'] : false,
            ]);
        }

        AuditLog::log('UPDATE_HABIT_TARGETS', 'Habit', null, null, [
            'admin' => $user->name,
            'targets' => $validated['targets'],
        ]);

        return back()->with('success', 'Pengaturan waktu, aktivitas, dan refleksi 7 Kebiasaan Anak Hebat berhasil diperbarui.');
    }
}

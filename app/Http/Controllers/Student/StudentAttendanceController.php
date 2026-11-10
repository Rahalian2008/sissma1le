<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\ReferenceItem;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->query('date', date('Y-m-d'));
        $rombels = ReferenceItem::where('category', 'rombel')->where('is_active', true)->orderBy('sort_order')->pluck('name')->toArray();
        if (empty($rombels)) {
            $rombels = Student::distinct()->whereNotNull('current_rombel')->pluck('current_rombel')->toArray();
        }
        if (empty($rombels)) {
            $rombels = ['X Merdeka 1', 'X Merdeka 2', 'XI Merdeka 1', 'XII Merdeka 1'];
        }

        $selectedRombel = $request->query('rombel', $rombels[0] ?? 'X Merdeka 1');

        $students = Student::active()
            ->where(function ($q) use ($selectedRombel) {
                $q->where('current_rombel', $selectedRombel)
                    ->orWhere('admission_class', $selectedRombel);
            })
            ->orderBy('name')
            ->get();

        // If no students match exact rombel, get students with current_class_level or general active
        if ($students->isEmpty()) {
            $level = explode(' ', $selectedRombel)[0] ?? 'X';
            $students = Student::active()->where('current_class_level', $level)->orderBy('name')->get();
        }
        if ($students->isEmpty()) {
            $students = Student::active()->orderBy('name')->take(30)->get();
        }

        $existingAttendances = StudentAttendance::where('date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Recap counts for this class on this date
        $summary = [
            'Hadir' => 0,
            'Sakit' => 0,
            'Izin' => 0,
            'Alpa' => 0,
            'Dispensasi' => 0,
        ];
        foreach ($students as $student) {
            $status = $existingAttendances[$student->id]->status ?? 'Hadir';
            if (isset($summary[$status])) {
                $summary[$status]++;
            }
        }

        $nextMonthDate = Carbon::parse($date)->addMonth();
        $nextMonth = (int) $nextMonthDate->month;
        $nextMonthYear = (int) $nextMonthDate->year;
        $nextMonthLabel = $nextMonthDate->translatedFormat('F Y');

        $currentMonth = (int) date('n', strtotime($date));
        $currentYear = (int) date('Y', strtotime($date));
        $currentMonthLabel = Carbon::parse($date)->translatedFormat('F Y');

        return view('students.attendance.index', compact(
            'date',
            'rombels',
            'selectedRombel',
            'students',
            'existingAttendances',
            'summary',
            'nextMonth',
            'nextMonthYear',
            'nextMonthLabel',
            'currentMonth',
            'currentYear',
            'currentMonthLabel'
        ));
    }

    public function storeBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'rombel' => ['required', 'string'],
            'attendances' => ['required', 'array'],
            'attendances.*' => ['required', 'in:Hadir,Sakit,Izin,Alpa,Dispensasi'],
            'notes' => ['nullable', 'array'],
        ]);

        $date = $validated['date'];
        $rombel = $validated['rombel'];
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classLevel = explode(' ', $rombel)[0] ?? 'X';

        foreach ($validated['attendances'] as $studentId => $status) {
            $note = $validated['notes'][$studentId] ?? null;

            StudentAttendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'academic_year_id' => $activeYear?->id,
                    'class_level' => $classLevel,
                    'rombel' => $rombel,
                    'status' => $status,
                    'notes' => $note,
                    'recorded_by_user_id' => Auth::id(),
                ]
            );
        }

        AuditTrailService::log('create', 'absensi', "Mencatat presensi siswa kelas {$rombel} tanggal {$date}");

        return back()->with('success', "Presensi siswa kelas {$rombel} tanggal ".Carbon::parse($date)->translatedFormat('d F Y').' berhasil disimpan.');
    }

    public function print(Request $request): View
    {
        $rombel = $request->query('rombel', 'X Merdeka 1');
        $mode = $request->query('mode', 'blank'); // blank or recap

        $school = SchoolIdentity::current();

        $students = Student::active()
            ->where(function ($q) use ($rombel) {
                $q->where('current_rombel', $rombel)
                    ->orWhere('admission_class', $rombel);
            })
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            $level = explode(' ', $rombel)[0] ?? 'X';
            $students = Student::active()->where('current_class_level', $level)->orderBy('name')->get();
        }
        if ($students->isEmpty()) {
            $students = Student::active()->orderBy('name')->take(36)->get();
        }

        // Support Custom Date Range or Month/Year fallback
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->query('start_date'));
            $endDate = Carbon::parse($request->query('end_date'));
        } elseif ($request->filled('month') && $request->filled('year')) {
            $month = (int) $request->query('month');
            $year = (int) $request->query('year');
            $startDate = Carbon::createFromDate($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            // Default 1 bulan kedepan atau bulan aktif
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        if ($endDate < $startDate) {
            $endDate = $startDate->copy()->addDays(30);
        }

        // Batasi rentang tanggal maksimal 31 hari agar cetakan pas 1 lembar presisi A4 landscape
        if ($startDate->diffInDays($endDate) > 31) {
            $endDate = $startDate->copy()->addDays(30);
        }

        $periodLabel = $startDate->translatedFormat('d F Y').' s.d. '.$endDate->translatedFormat('d F Y');
        $monthLabel = $startDate->translatedFormat('F Y');
        $month = (int) $startDate->month;
        $year = (int) $startDate->year;

        // Generate calendar days for the custom range
        $days = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $shortDay = match ($current->dayOfWeek) {
                Carbon::SUNDAY => 'Mg',
                Carbon::MONDAY => 'Sn',
                Carbon::TUESDAY => 'Sl',
                Carbon::WEDNESDAY => 'Rb',
                Carbon::THURSDAY => 'Km',
                Carbon::FRIDAY => 'Jm',
                Carbon::SATURDAY => 'Sb',
            };

            $days[] = [
                'day' => count($days) > 31 ? $current->format('d/m') : $current->day,
                'shortName' => $shortDay,
                'isWeekend' => $current->isWeekend(),
                'dateString' => $current->toDateString(),
            ];
            $current->addDay();
        }

        $daysInMonth = count($days);

        // Get attendances in date range if in recap mode
        $attendances = StudentAttendance::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $waliKelas = Employee::getWaliKelasForRombel($rombel);

        return view('students.attendance.print', compact(
            'rombel',
            'month',
            'year',
            'startDate',
            'endDate',
            'periodLabel',
            'monthLabel',
            'days',
            'daysInMonth',
            'mode',
            'school',
            'students',
            'attendances',
            'waliKelas'
        ));
    }
}

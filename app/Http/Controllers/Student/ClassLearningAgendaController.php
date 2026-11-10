<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassLearningAgenda;
use App\Models\Employee;
use App\Models\ReferenceItem;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Services\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ClassLearningAgendaController extends Controller
{
    /**
     * Tampilkan agenda pembelajaran kelas dengan filter rombel dan periode
     */
    public function index(Request $request): View
    {
        $rombels = ReferenceItem::where('category', 'rombel')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();

        if (empty($rombels) && Schema::hasColumn('students', 'current_rombel')) {
            $rombels = Student::distinct()->whereNotNull('current_rombel')->orderBy('current_rombel')->pluck('current_rombel')->toArray();
        }

        if (empty($rombels)) {
            $rombels = ['X Merdeka 1', 'X Merdeka 2', 'XI Merdeka 1', 'XII Merdeka 1'];
        }

        $selectedRombel = $request->query('rombel', $rombels[0] ?? 'X Merdeka 1');
        $selectedDate = $request->query('date');
        $selectedMonth = $request->query('month', now()->format('Y-m'));
        $searchSubject = $request->query('subject');

        $academicYear = AcademicYear::where('is_active', true)->first();
        $teachers = Employee::where('is_active', true)->orderBy('name')->get();
        $waliKelas = Employee::getWaliKelasForRombel($selectedRombel);
        $totalStudentsInRombel = 0;
        if (Schema::hasColumn('students', 'current_rombel')) {
            $totalStudentsInRombel = Student::active()->where('current_rombel', $selectedRombel)->count();
        } else {
            $totalStudentsInRombel = Student::active()->count();
        }

        $query = ClassLearningAgenda::with(['teacher', 'academicYear'])
            ->where('rombel', $selectedRombel);

        if ($selectedDate) {
            $query->whereDate('date', $selectedDate);
        } elseif ($selectedMonth) {
            $query->where('date', 'like', "{$selectedMonth}%");
        }

        if ($searchSubject) {
            $query->where('subject', 'like', "%{$searchSubject}%");
        }

        $agendas = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Statistik Kehadiran & KBM pada Rombel & Periode Terpilih
        $statsQuery = ClassLearningAgenda::where('rombel', $selectedRombel);
        if ($selectedDate) {
            $statsQuery->whereDate('date', $selectedDate);
        } elseif ($selectedMonth) {
            $statsQuery->where('date', 'like', "{$selectedMonth}%");
        }

        $totalSessions = (clone $statsQuery)->count();
        $sumPresent = (clone $statsQuery)->sum('students_present');
        $sumSick = (clone $statsQuery)->sum('students_sick');
        $sumPermitted = (clone $statsQuery)->sum('students_permitted');
        $sumAbsent = (clone $statsQuery)->sum('students_absent');
        $totalAttendanceEntries = $sumPresent + $sumSick + $sumPermitted + $sumAbsent;
        $attendanceRate = $totalAttendanceEntries > 0 ? round(($sumPresent / $totalAttendanceEntries) * 100, 1) : 100;

        return view('students.learning_agendas.index', compact(
            'rombels',
            'selectedRombel',
            'selectedDate',
            'selectedMonth',
            'searchSubject',
            'academicYear',
            'teachers',
            'waliKelas',
            'totalStudentsInRombel',
            'agendas',
            'totalSessions',
            'sumPresent',
            'sumSick',
            'sumPermitted',
            'sumAbsent',
            'attendanceRate'
        ));
    }

    /**
     * Simpan entri agenda/jurnal pembelajaran kelas baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rombel' => ['required', 'string', 'max:50'],
            'date' => ['required', 'date'],
            'time_slot' => ['required', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:100'],
            'teacher_employee_id' => ['nullable', 'exists:employees,id'],
            'teacher_name' => ['nullable', 'string', 'max:150'],
            'learning_topic' => ['required', 'string'],
            'activities_summary' => ['nullable', 'string'],
            'students_present' => ['nullable', 'integer', 'min:0'],
            'students_sick' => ['nullable', 'integer', 'min:0'],
            'students_permitted' => ['nullable', 'integer', 'min:0'],
            'students_absent' => ['nullable', 'integer', 'min:0'],
            'absent_notes' => ['nullable', 'string'],
            'class_notes' => ['nullable', 'string'],
            'teacher_signed' => ['nullable'],
        ]);

        if (! empty($validated['teacher_employee_id'])) {
            $emp = Employee::find($validated['teacher_employee_id']);
            if ($emp && empty($validated['teacher_name'])) {
                $validated['teacher_name'] = $emp->formatted_name;
            }
        }

        if (empty($validated['teacher_name'])) {
            $validated['teacher_name'] = auth()->user()->name ?? 'Guru Pengampu';
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $validated['academic_year_id'] = $activeYear?->id;
        $validated['students_present'] = (int) ($validated['students_present'] ?? 0);
        $validated['students_sick'] = (int) ($validated['students_sick'] ?? 0);
        $validated['students_permitted'] = (int) ($validated['students_permitted'] ?? 0);
        $validated['students_absent'] = (int) ($validated['students_absent'] ?? 0);
        $validated['teacher_signed'] = $request->boolean('teacher_signed', true);

        $agenda = ClassLearningAgenda::create($validated);

        AuditTrailService::log(
            'create',
            'agenda_kelas',
            "Menambahkan agenda pembelajaran rombel {$agenda->rombel}: {$agenda->subject} tgl {$agenda->date->format('d/m/Y')}",
            null,
            $validated
        );

        $month = Carbon::parse($validated['date'])->format('Y-m');

        return redirect()->route('students.learning-agendas.index', [
            'rombel' => $validated['rombel'],
            'month' => $month,
        ])->with('success', 'Agenda pembelajaran kelas berhasil disimpan.');
    }

    /**
     * Perbarui entri agenda/jurnal pembelajaran kelas
     */
    public function update(Request $request, ClassLearningAgenda $learningAgenda): RedirectResponse
    {
        $validated = $request->validate([
            'rombel' => ['required', 'string', 'max:50'],
            'date' => ['required', 'date'],
            'time_slot' => ['required', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:100'],
            'teacher_employee_id' => ['nullable', 'exists:employees,id'],
            'teacher_name' => ['nullable', 'string', 'max:150'],
            'learning_topic' => ['required', 'string'],
            'activities_summary' => ['nullable', 'string'],
            'students_present' => ['nullable', 'integer', 'min:0'],
            'students_sick' => ['nullable', 'integer', 'min:0'],
            'students_permitted' => ['nullable', 'integer', 'min:0'],
            'students_absent' => ['nullable', 'integer', 'min:0'],
            'absent_notes' => ['nullable', 'string'],
            'class_notes' => ['nullable', 'string'],
            'teacher_signed' => ['nullable'],
        ]);

        if (! empty($validated['teacher_employee_id'])) {
            $emp = Employee::find($validated['teacher_employee_id']);
            if ($emp && empty($validated['teacher_name'])) {
                $validated['teacher_name'] = $emp->formatted_name;
            }
        }

        if (empty($validated['teacher_name'])) {
            $validated['teacher_name'] = $learningAgenda->teacher_name;
        }

        $validated['students_present'] = (int) ($validated['students_present'] ?? 0);
        $validated['students_sick'] = (int) ($validated['students_sick'] ?? 0);
        $validated['students_permitted'] = (int) ($validated['students_permitted'] ?? 0);
        $validated['students_absent'] = (int) ($validated['students_absent'] ?? 0);
        $validated['teacher_signed'] = $request->boolean('teacher_signed', true);

        $old = $learningAgenda->toArray();
        $learningAgenda->update($validated);

        AuditTrailService::log(
            'update',
            'agenda_kelas',
            "Memperbarui agenda pembelajaran #{$learningAgenda->id} rombel {$learningAgenda->rombel}",
            $old,
            $validated
        );

        $month = Carbon::parse($validated['date'])->format('Y-m');

        return redirect()->route('students.learning-agendas.index', [
            'rombel' => $validated['rombel'],
            'month' => $month,
        ])->with('success', 'Agenda pembelajaran kelas berhasil diperbarui.');
    }

    /**
     * Hapus entri agenda/jurnal pembelajaran
     */
    public function destroy(ClassLearningAgenda $learningAgenda): RedirectResponse
    {
        $rombel = $learningAgenda->rombel;
        $month = $learningAgenda->date ? $learningAgenda->date->format('Y-m') : now()->format('Y-m');

        $old = $learningAgenda->toArray();
        $learningAgenda->delete();

        AuditTrailService::log(
            'delete',
            'agenda_kelas',
            "Menghapus agenda pembelajaran #{$learningAgenda->id} rombel {$rombel}",
            $old
        );

        return redirect()->route('students.learning-agendas.index', [
            'rombel' => $rombel,
            'month' => $month,
        ])->with('success', 'Entri agenda pembelajaran kelas telah dihapus.');
    }

    /**
     * Cetak Buku Agenda / Jurnal Pembelajaran Kelas (Format Resmi Kurikulum Merdeka)
     */
    public function print(Request $request): View
    {
        $school = SchoolIdentity::current();
        $rombel = $request->query('rombel', 'X Merdeka 1');
        $month = $request->query('month', now()->format('Y-m'));

        $isBlank = $request->boolean('blank', false) || $request->query('mode') === 'blank';
        $rowCount = (int) $request->query('rows', 10);
        if ($rowCount < 5 || $rowCount > 50) {
            $rowCount = 10;
        }

        $agendas = $isBlank ? collect([]) : ClassLearningAgenda::with('teacher')
            ->where('rombel', $rombel)
            ->where('date', 'like', "{$month}%")
            ->orderBy('date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();

        $academicYear = AcademicYear::where('is_active', true)->first();
        $waliKelas = Employee::getWaliKelasForRombel($rombel);
        $wakasekKurikulum = Employee::where('position', 'like', '%Kurikulum%')->first();
        $kepalaSekolah = Employee::where('position', 'like', '%Kepala Sekolah%')
            ->where('position', 'not like', '%Wakil%')
            ->first();

        // Hitung total ringkasan presensi kelas
        $totalSessions = $agendas->count();
        $totalHadir = $agendas->sum('students_present');
        $totalSakit = $agendas->sum('students_sick');
        $totalIzin = $agendas->sum('students_permitted');
        $totalAlpa = $agendas->sum('students_absent');

        AuditTrailService::log('print', 'agenda_kelas', "Mencetak Format Agenda Pembelajaran Kelas Rombel {$rombel} (".($isBlank ? 'Format Kosong 10 Baris' : "Periode {$month}").')');

        return view('students.learning_agendas.print', compact(
            'school',
            'rombel',
            'month',
            'agendas',
            'academicYear',
            'waliKelas',
            'wakasekKurikulum',
            'kepalaSekolah',
            'totalSessions',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlpa',
            'isBlank',
            'rowCount'
        ));
    }
}

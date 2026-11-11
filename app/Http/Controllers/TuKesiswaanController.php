<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolHoliday;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentDiploma;
use App\Models\StudentLegalization;
use App\Models\StudentMutation;
use App\Models\StudentScholarship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TuKesiswaanController extends Controller
{
    private function authorizeKesiswaan(): void
    {
        if (! Auth::user()?->canAccessTuSubmodule('kesiswaan')) {
            abort(403, 'Akses tidak diizinkan. Menu ini dikhususkan untuk staf Administrasi Kesiswaan.');
        }
    }

    public function index(Request $request): View
    {
        $this->authorizeKesiswaan();

        $masterAvailableCount = Student::where('is_active', true)->count();

        // 1. Metrik Utama Kesiswaan (Hanya yang terdaftar di Administrasi Kesiswaan)
        $baseQuery = Student::tuRegistered();
        $totalStudents = (clone $baseQuery)->count();
        $activeStudents = (clone $baseQuery)->where('is_active', true)->count();
        $maleStudents = (clone $baseQuery)->where('gender', 'L')->count();
        $femaleStudents = (clone $baseQuery)->where('gender', 'P')->count();

        // 2. Rombel Tingkat X, XI, XII dari Master Data Kelas
        $classXCount = (clone $baseQuery)->whereHas('schoolClass', fn ($q) => $q->whereIn('grade', ['X', '10']))->count();
        $classXICount = (clone $baseQuery)->whereHas('schoolClass', fn ($q) => $q->whereIn('grade', ['XI', '11']))->count();
        $classXIICount = (clone $baseQuery)->whereHas('schoolClass', fn ($q) => $q->whereIn('grade', ['XII', '12']))->count();

        // 3. Mutasi & Beasiswa & Ijazah & Legalisir
        $mutationsIn = Schema::hasTable('student_mutations') ? (Schema::hasColumn('student_mutations', 'mutation_type') ? StudentMutation::where('mutation_type', 'MASUK')->count() : StudentMutation::where('type', 'MASUK')->count()) : 0;
        $mutationsOut = Schema::hasTable('student_mutations') ? (Schema::hasColumn('student_mutations', 'mutation_type') ? StudentMutation::where('mutation_type', 'KELUAR')->count() : StudentMutation::where('type', 'KELUAR')->count()) : 0;
        $totalDiplomas = Schema::hasTable('student_diplomas') ? StudentDiploma::count() : 0;
        $pipCount = Schema::hasTable('student_scholarships') ? StudentScholarship::count() : 0;
        $totalLegalizations = Schema::hasTable('student_legalizations') ? StudentLegalization::count() : 0;
        $pendingLegalizations = Schema::hasTable('student_legalizations') ? StudentLegalization::where('status', 'diajukan')->count() : 0;

        // 4. Daftar Siswa di Administrasi Kesiswaan
        $query = Student::tuRegistered()->with(['schoolClass', 'academicYear'])->orderBy('name');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('grade')) {
            $grade = $request->grade;
            $query->whereHas('schoolClass', fn ($q) => $q->whereIn('grade', [$grade, str_replace(['X', 'XI', 'XII'], ['10', '11', '12'], $grade)]));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(12)->withQueryString();
        $classes = SchoolClass::orderBy('name')->get();
        $school = SchoolIdentity::current();

        return view('tu.kesiswaan.index', compact(
            'totalStudents',
            'activeStudents',
            'maleStudents',
            'femaleStudents',
            'classXCount',
            'classXICount',
            'classXIICount',
            'mutationsIn',
            'mutationsOut',
            'totalDiplomas',
            'pipCount',
            'totalLegalizations',
            'pendingLegalizations',
            'students',
            'classes',
            'school',
            'masterAvailableCount'
        ));
    }

    /**
     * Tarik data siswa & RFID dari Master Data ke Administrasi Kesiswaan
     */
    public function pullFromMaster(Request $request): RedirectResponse
    {
        $this->authorizeKesiswaan();

        if (Schema::hasColumn('students', 'is_tu_registered')) {
            $count = Student::where('is_active', true)->update([
                'is_tu_registered' => true,
                'tu_registered_at' => now(),
            ]);
        } else {
            $count = Student::where('is_active', true)->count();
        }

        return redirect()->route('tu.kesiswaan.index')
            ->with('success', "Berhasil menarik {$count} data Siswa & Kartu RFID dari Master Data ke Administrasi Kesiswaan!");
    }

    /**
     * Kosongkan data di Administrasi Kesiswaan
     */
    public function clearData(Request $request): RedirectResponse
    {
        $this->authorizeKesiswaan();

        if (Schema::hasColumn('students', 'is_tu_registered')) {
            Student::query()->update([
                'is_tu_registered' => false,
                'tu_registered_at' => null,
            ]);
        }

        return redirect()->route('tu.kesiswaan.index')
            ->with('success', 'Data Administrasi Kesiswaan berhasil dikosongkan. Data master siswa & RFID tetap aman dan utuh.');
    }

    /**
     * Dashboard Administrasi Legalisir Dokumen Siswa
     */
    public function legalisirIndex(Request $request): View
    {
        $this->authorizeKesiswaan();

        $query = StudentLegalization::with(['student.schoolClass', 'verifier'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', "%{$search}%")
                    ->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $legalizations = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => StudentLegalization::count(),
            'diajukan' => StudentLegalization::where('status', 'diajukan')->count(),
            'diverifikasi' => StudentLegalization::where('status', 'diverifikasi')->count(),
            'selesai' => StudentLegalization::where('status', 'selesai')->count(),
            'ditolak' => StudentLegalization::where('status', 'ditolak')->count(),
        ];

        // Daftar siswa aktif untuk autocomplete / pilihan cepat
        $students = Student::select('id', 'name', 'nisn', 'nis', 'class_id')
            ->with('schoolClass:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(200)
            ->get();

        $school = SchoolIdentity::current();

        return view('tu.kesiswaan.legalisir.index', compact(
            'legalizations',
            'stats',
            'students',
            'school'
        ));
    }

    /**
     * Simpan Permohonan Legalisir Dokumen Baru
     */
    public function legalisirStore(Request $request): RedirectResponse
    {
        $this->authorizeKesiswaan();

        $validated = $request->validate([
            'applicant_name' => ['required', 'string', 'max:150'],
            'student_id' => ['nullable', 'exists:students,id'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'nis' => ['nullable', 'string', 'max:20'],
            'graduation_year' => ['nullable', 'string', 'max:10'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'document_type' => ['required', 'string', 'max:100'],
            'copies_count' => ['required', 'integer', 'min:1', 'max:50'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $student = null;
        if (! empty($validated['student_id'])) {
            $student = Student::find($validated['student_id']);
        } elseif (! empty($validated['nisn'])) {
            $student = Student::where('nisn', $validated['nisn'])->first();
        }

        $legalization = StudentLegalization::create([
            'receipt_number' => StudentLegalization::generateReceiptNumber(),
            'student_id' => $student?->id,
            'applicant_name' => trim($validated['applicant_name']),
            'nisn' => $validated['nisn'] ?? $student?->nisn,
            'nis' => $validated['nis'] ?? $student?->nis,
            'graduation_year' => $validated['graduation_year'] ?? ($student?->schoolClass?->name ? 'Siswa Aktif' : null),
            'phone_number' => $validated['phone_number'] ?? $student?->phone,
            'document_type' => $validated['document_type'],
            'copies_count' => (int) $validated['copies_count'],
            'purpose' => $validated['purpose'] ?? 'Melanjutkan Pendidikan / Keperluan Resmi',
            'status' => 'diajukan',
            'notes' => $validated['notes'] ?? null,
            'verified_by' => null,
            'completed_at' => null,
        ]);

        return redirect()->route('tu.kesiswaan.legalisir.index')
            ->with('success', "Permohonan legalisir berhasil dicatat dengan Nomor Agenda: {$legalization->receipt_number}");
    }

    /**
     * Update Status Permohonan Legalisir
     */
    public function legalisirUpdateStatus(Request $request, StudentLegalization $legalization): RedirectResponse
    {
        $this->authorizeKesiswaan();

        $validated = $request->validate([
            'status' => ['required', 'in:diajukan,diverifikasi,selesai,ditolak'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $data = [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $legalization->notes,
        ];

        if (in_array($validated['status'], ['diverifikasi', 'selesai'], true)) {
            $data['verified_by'] = Auth::id();
        }

        if ($validated['status'] === 'selesai' && ! $legalization->completed_at) {
            $data['completed_at'] = now();
        }

        $legalization->update($data);

        return redirect()->back()
            ->with('success', "Status permohonan {$legalization->receipt_number} berhasil diperbarui menjadi {$validated['status']}.");
    }

    /**
     * Cetak Tanda Terima / Resi Permohonan Legalisir Resmi
     */
    public function legalisirPrintReceipt(StudentLegalization $legalization): View
    {
        $this->authorizeKesiswaan();

        $school = SchoolIdentity::current();
        $headmasterName = SchoolSetting::get('headmaster_name') ?? $school?->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd.';
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?? $school?->headmaster_nip ?? '197205121998021001';
        $paper = request('paper', 'a4');

        return view('tu.kesiswaan.legalisir.print_receipt', compact(
            'legalization',
            'school',
            'headmasterName',
            'headmasterNip',
            'paper'
        ));
    }

    /**
     * Cetak Buku Agenda / Rekapitulasi Legalisir Dokumen Siswa
     */
    public function legalisirPrintBook(Request $request): View
    {
        $this->authorizeKesiswaan();

        $query = StudentLegalization::with(['student', 'verifier'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date.' 00:00:00',
                $request->end_date.' 23:59:59',
            ]);
        }

        $legalizations = $query->get();
        $school = SchoolIdentity::current();
        $headmasterName = SchoolSetting::get('headmaster_name') ?? $school?->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd.';
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?? $school?->headmaster_nip ?? '197205121998021001';
        $paper = request('paper', 'f4');

        return view('tu.kesiswaan.legalisir.print_book', compact(
            'legalizations',
            'school',
            'headmasterName',
            'headmasterNip',
            'paper'
        ));
    }

    /**
     * Hapus Data Permohonan Legalisir
     */
    public function legalisirDestroy(StudentLegalization $legalization): RedirectResponse
    {
        $this->authorizeKesiswaan();

        $receipt = $legalization->receipt_number;
        $legalization->delete();

        return redirect()->route('tu.kesiswaan.legalisir.index')
            ->with('success', "Data legalisir {$receipt} berhasil dihapus.");
    }

    /**
     * Halaman Konfigurasi Cetak Absen Manual Perkelas (Model SIKATAS)
     */
    public function manualAttendanceIndex(Request $request): View
    {
        $this->authorizeKesiswaan();

        $classes = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $selectedClass = $classes->firstWhere('id', $selectedClassId);

        $currentMonth = (int) $request->get('month', date('n'));
        $currentYear = (int) $request->get('year', date('Y'));
        $periodType = $request->get('period_type', 'monthly');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        // Siswa di kelas terpilih
        $studentsCount = $selectedClassId ? Student::where('class_id', $selectedClassId)->where('is_active', true)->count() : 0;

        $school = SchoolIdentity::current();
        $academicYear = SchoolSetting::get('academic_year') ?? '2026/2027';
        $semester = SchoolSetting::get('semester') ?? 'Ganjil';

        return view('tu.kesiswaan.absen_manual.index', compact(
            'classes',
            'selectedClass',
            'selectedClassId',
            'currentMonth',
            'currentYear',
            'periodType',
            'startDate',
            'endDate',
            'studentsCount',
            'school',
            'academicYear',
            'semester'
        ));
    }

    /**
     * Lembar Cetak Presensi Manual Siswa Resmi Model SIKATAS
     */
    public function manualAttendancePrint(Request $request): View
    {
        $this->authorizeKesiswaan();

        $request->validate([
            'class_id' => ['required'],
            'period_type' => ['required', 'in:monthly,weekly,custom'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2020,2035'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'paper' => ['nullable', 'in:f4,a4'],
            'orientation' => ['nullable', 'in:landscape,portrait'],
            'kop' => ['nullable', 'in:0,1'],
            'signer' => ['nullable', 'string'],
        ]);

        $schoolClass = SchoolClass::with(['homeroomTeacher.user'])->findOrFail($request->class_id);
        $homeroomTeacher = $schoolClass->homeroomTeacher;

        $students = Student::where('class_id', $schoolClass->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            $students = Student::where('class_id', $schoolClass->id)
                ->orderBy('name')
                ->get();
        }

        $periodType = $request->period_type;
        $dates = [];
        $periodLabel = '';

        if ($periodType === 'monthly') {
            $month = (int) ($request->month ?: date('n'));
            $year = (int) ($request->year ?: date('Y'));
            $firstDay = Carbon::createFromDate($year, $month, 1);
            $daysInMonth = $firstDay->daysInMonth;
            $monthName = $firstDay->translatedFormat('F Y');
            $periodLabel = "Bulan {$monthName}";

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $cDate = Carbon::createFromDate($year, $month, $d);
                $isSunday = $cDate->isSunday();
                $holiday = SchoolHoliday::getHoliday($cDate->toDateString(), $schoolClass->id);
                $isHoliday = $isSunday || (bool) $holiday;
                $holidayName = $holiday?->name ?? ($isSunday ? 'Minggu' : null);

                $dates[] = [
                    'date_string' => $cDate->toDateString(),
                    'day_number' => $d,
                    'day_name_short' => match ($cDate->dayOfWeek) {
                        0 => 'Mg',
                        1 => 'Sn',
                        2 => 'Sl',
                        3 => 'Rb',
                        4 => 'Km',
                        5 => 'Jm',
                        6 => 'Sb',
                    },
                    'is_sunday' => $isSunday,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                ];
            }
        } elseif ($periodType === 'weekly') {
            $baseDate = Carbon::parse($request->start_date ?: date('Y-m-d'));
            $startOfWeek = $baseDate->copy()->startOfWeek(); // Senin
            $endOfWeek = $startOfWeek->copy()->addDays(5); // Sabtu
            $periodLabel = 'Pekan: '.$startOfWeek->translatedFormat('d M Y').' s.d. '.$endOfWeek->translatedFormat('d M Y');

            for ($i = 0; $i < 6; $i++) {
                $cDate = $startOfWeek->copy()->addDays($i);
                $isSunday = $cDate->isSunday();
                $holiday = SchoolHoliday::getHoliday($cDate->toDateString(), $schoolClass->id);
                $isHoliday = $isSunday || (bool) $holiday;
                $holidayName = $holiday?->name ?? ($isSunday ? 'Minggu' : null);

                $dates[] = [
                    'date_string' => $cDate->toDateString(),
                    'day_number' => $cDate->day,
                    'day_name_short' => match ($cDate->dayOfWeek) {
                        0 => 'Mg',
                        1 => 'Sn',
                        2 => 'Sl',
                        3 => 'Rb',
                        4 => 'Km',
                        5 => 'Jm',
                        6 => 'Sb',
                    },
                    'is_sunday' => $isSunday,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                ];
            }
        } else {
            // custom range
            $start = Carbon::parse($request->start_date ?: date('Y-m-01'));
            $end = Carbon::parse($request->end_date ?: date('Y-m-t'));
            if ($start->diffInDays($end) > 31) {
                $end = $start->copy()->addDays(30);
            }
            $periodLabel = $start->translatedFormat('d M Y').' s.d. '.$end->translatedFormat('d M Y');

            $curr = $start->copy();
            while ($curr->lte($end)) {
                $isSunday = $curr->isSunday();
                $holiday = SchoolHoliday::getHoliday($curr->toDateString(), $schoolClass->id);
                $isHoliday = $isSunday || (bool) $holiday;
                $holidayName = $holiday?->name ?? ($isSunday ? 'Minggu' : null);

                $dates[] = [
                    'date_string' => $curr->toDateString(),
                    'day_number' => $curr->day,
                    'day_name_short' => match ($curr->dayOfWeek) {
                        0 => 'Mg',
                        1 => 'Sn',
                        2 => 'Sl',
                        3 => 'Rb',
                        4 => 'Km',
                        5 => 'Jm',
                        6 => 'Sb',
                    },
                    'is_sunday' => $isSunday,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                ];
                $curr->addDay();
            }
        }

        $school = SchoolIdentity::current();
        $academicYear = SchoolSetting::get('academic_year') ?? '2026/2027';
        $semester = SchoolSetting::get('semester') ?? 'Ganjil';
        $headmasterName = SchoolSetting::get('headmaster_name') ?? $school?->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd.';
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?? $school?->headmaster_nip ?? '197205121998021001';

        $wakasekUser = User::where(function ($q) {
            $q->where('role', 'wakasek_kesiswaan')
                ->orWhereJsonContains('roles', 'wakasek_kesiswaan');
        })->first();
        $wakasekName = $wakasekUser?->name ?? 'Wakasek Bidang Kesiswaan';
        $wakasekNip = $wakasekUser?->nip ?: '-';

        $paper = $request->get('paper', 'f4');
        $showKop = $request->get('kop', '1') !== '0';
        $signerRole = $request->get('signer', 'wakasek');

        return view('tu.kesiswaan.absen_manual.print', compact(
            'schoolClass',
            'homeroomTeacher',
            'students',
            'dates',
            'periodType',
            'periodLabel',
            'school',
            'academicYear',
            'semester',
            'headmasterName',
            'headmasterNip',
            'wakasekName',
            'wakasekNip',
            'paper',
            'showKop',
            'signerRole'
        ));
    }
}

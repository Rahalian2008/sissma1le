<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReferenceItem;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentFamily;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $masterAvailableCount = Student::where('is_active', true)->count();
        $query = Student::tuRegistered()->with(['schoolClass', 'academicYear']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $hasCurrentClassLevel = Schema::hasColumn('students', 'current_class_level');
        $hasCurrentRombel = Schema::hasColumn('students', 'current_rombel');
        $hasStatus = Schema::hasColumn('students', 'status');

        if ($classId = $request->query('class_id')) {
            $query->where('class_id', $classId);
        } elseif ($classLevel = $request->query('class_level')) {
            $query->whereHas('schoolClass', function ($q) use ($classLevel) {
                $q->where('name', $classLevel)
                    ->orWhere('grade', $classLevel)
                    ->orWhere('grade', str_replace(['10', '11', '12'], ['X', 'XI', 'XII'], $classLevel))
                    ->orWhere('id', $classLevel);
            });
        }

        if ($status = $request->query('status')) {
            if ($hasStatus) {
                $query->where('status', $status);
            } elseif ($status === 'Aktif') {
                $query->where('is_active', true);
            }
        }

        $students = $query->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::orderBy('name')->get();
        $statuses = ReferenceItem::category('status_siswa')->pluck('name')->toArray();
        if (empty($statuses)) {
            $statuses = ['Aktif', 'Mutasi Masuk', 'Mutasi Keluar', 'Lulus', 'Mengundurkan Diri', 'Dikeluarkan'];
        }

        // Summary Demografi Siswa di Administrasi Kesiswaan
        $tuActiveQuery = Student::tuRegistered()->active();
        $totalActive = (clone $tuActiveQuery)->count();
        $maleActive = (clone $tuActiveQuery)->where('gender', 'L')->count();
        $femaleActive = (clone $tuActiveQuery)->where('gender', 'P')->count();

        // Rekapitulasi per Tingkat Kelas
        if ($hasCurrentClassLevel) {
            $classStats = Student::tuRegistered()->active()
                ->select('current_class_level',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy('current_class_level')
                ->orderBy('current_class_level')
                ->get();
        } elseif (Schema::hasTable('school_classes')) {
            $classStats = Student::tuRegistered()->active()
                ->leftJoin('school_classes', 'students.class_id', '=', 'school_classes.id')
                ->select(
                    DB::raw("COALESCE(school_classes.grade, 'X') as current_class_level"),
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy('current_class_level')
                ->orderBy('current_class_level')
                ->get();
        } else {
            $classStats = collect();
        }

        // Rekapitulasi per Rombel
        if ($hasCurrentRombel && $hasCurrentClassLevel) {
            $rombelStats = Student::tuRegistered()->active()
                ->select('current_rombel', 'current_class_level',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->whereNotNull('current_rombel')
                ->where('current_rombel', '!=', '')
                ->groupBy('current_class_level', 'current_rombel')
                ->orderBy('current_class_level')
                ->orderBy('current_rombel')
                ->get();
        } elseif (Schema::hasTable('school_classes')) {
            $rombelStats = Student::tuRegistered()->active()
                ->leftJoin('school_classes', 'students.class_id', '=', 'school_classes.id')
                ->select(
                    DB::raw("COALESCE(school_classes.name, 'Kelas') as current_rombel"),
                    DB::raw("COALESCE(school_classes.grade, 'X') as current_class_level"),
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->whereNotNull('students.class_id')
                ->groupBy('current_class_level', 'current_rombel')
                ->orderBy('current_class_level')
                ->orderBy('current_rombel')
                ->get();
        } else {
            $rombelStats = collect();
        }

        // Distribusi Usia Siswa Aktif
        $activeWithBirth = Student::tuRegistered()->active()->whereNotNull('birth_date')->get();
        $ageBrackets = [
            '< 15 Th' => 0,
            '15 Th' => 0,
            '16 Th' => 0,
            '17 Th' => 0,
            '18 Th' => 0,
            '> 18 Th' => 0,
        ];
        $ageGenderBrackets = [
            '< 15 Th' => ['L' => 0, 'P' => 0],
            '15 Th' => ['L' => 0, 'P' => 0],
            '16 Th' => ['L' => 0, 'P' => 0],
            '17 Th' => ['L' => 0, 'P' => 0],
            '18 Th' => ['L' => 0, 'P' => 0],
            '> 18 Th' => ['L' => 0, 'P' => 0],
        ];

        foreach ($activeWithBirth as $st) {
            $age = $st->birth_date ? (int) $st->birth_date->age : null;
            if ($age === null) {
                continue;
            }

            if ($age < 15) {
                $b = '< 15 Th';
            } elseif ($age == 15) {
                $b = '15 Th';
            } elseif ($age == 16) {
                $b = '16 Th';
            } elseif ($age == 17) {
                $b = '17 Th';
            } elseif ($age == 18) {
                $b = '18 Th';
            } else {
                $b = '> 18 Th';
            }

            $ageBrackets[$b]++;
            if ($st->gender === 'L') {
                $ageGenderBrackets[$b]['L']++;
            } elseif ($st->gender === 'P') {
                $ageGenderBrackets[$b]['P']++;
            }
        }

        $summaryStats = [
            'totalActive' => $totalActive,
            'maleActive' => $maleActive,
            'femaleActive' => $femaleActive,
            'classStats' => $classStats,
            'rombelStats' => $rombelStats,
            'ageBrackets' => $ageBrackets,
            'ageGenderBrackets' => $ageGenderBrackets,
        ];

        return view('students.index', compact('students', 'classes', 'statuses', 'summaryStats', 'masterAvailableCount'));
    }

    public function create(): View
    {
        $classes = ReferenceItem::category('kelas')->get();
        $rombels = ReferenceItem::category('rombel')->get();
        $religions = ReferenceItem::category('agama')->get();
        $statuses = ReferenceItem::category('status_siswa')->get();

        return view('students.create', compact('classes', 'rombels', 'religions', 'statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:students'],
            'nisn' => ['nullable', 'string', 'max:30', 'unique:students'],
            'nik' => ['nullable', 'string', 'max:30'],
            'family_card_number' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'religion' => ['nullable', 'string', 'max:30'],
            'child_order' => ['nullable', 'integer', 'min:1'],
            'siblings_count' => ['nullable', 'integer', 'min:0'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'height' => ['nullable', 'integer', 'min:50', 'max:250'],
            'weight' => ['nullable', 'integer', 'min:20', 'max:200'],
            'address' => ['nullable', 'string'],
            'rt_rw' => ['nullable', 'string', 'max:20'],
            'residence_district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'living_with' => ['nullable', 'string', 'max:50'],
            'transportation' => ['nullable', 'string', 'max:50'],
            'distance_to_school' => ['nullable', 'string', 'max:50'],
            'special_needs' => ['nullable', 'string', 'max:100'],
            'school_origin' => ['nullable', 'string', 'max:150'],
            'junior_diploma_number' => ['nullable', 'string', 'max:50'],
            'junior_exam_number' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'admission_class' => ['nullable', 'string', 'max:50'],
            'entry_path' => ['nullable', 'string', 'max:50'],
            'is_pip' => ['nullable', 'boolean'],
            'pip_number' => ['nullable', 'string', 'max:50'],
            'current_class_level' => ['required', 'string', 'max:10'],
            'current_rombel' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'max:30'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'father_nik' => ['nullable', 'string', 'max:30'],
            'father_education' => ['nullable', 'string', 'max:50'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'father_income' => ['nullable', 'string', 'max:50'],
            'father_phone' => ['nullable', 'string', 'max:30'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'mother_nik' => ['nullable', 'string', 'max:30'],
            'mother_education' => ['nullable', 'string', 'max:50'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'mother_income' => ['nullable', 'string', 'max:50'],
            'mother_phone' => ['nullable', 'string', 'max:30'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_nik' => ['nullable', 'string', 'max:30'],
            'guardian_education' => ['nullable', 'string', 'max:50'],
            'guardian_occupation' => ['nullable', 'string', 'max:100'],
            'guardian_income' => ['nullable', 'string', 'max:50'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $studentData = $request->only([
            'nis', 'nisn', 'nik', 'family_card_number', 'name', 'gender', 'birth_place', 'birth_date',
            'religion', 'child_order', 'siblings_count', 'blood_type', 'height', 'weight',
            'address', 'rt_rw', 'residence_district', 'postal_code', 'phone', 'email',
            'living_with', 'transportation', 'distance_to_school', 'special_needs',
            'school_origin', 'junior_diploma_number', 'junior_exam_number',
            'admission_date', 'admission_class', 'entry_path', 'is_pip', 'pip_number',
            'current_class_level', 'current_rombel', 'status',
        ]);
        $studentData['is_pip'] = $request->boolean('is_pip');
        $studentData['qr_code_token'] = Str::uuid()->toString();

        $student = Student::create($studentData);

        StudentFamily::create([
            'student_id' => $student->id,
            'father_name' => $request->input('father_name'),
            'father_nik' => $request->input('father_nik'),
            'father_education' => $request->input('father_education'),
            'father_occupation' => $request->input('father_occupation'),
            'father_income' => $request->input('father_income'),
            'father_phone' => $request->input('father_phone'),
            'mother_name' => $request->input('mother_name'),
            'mother_nik' => $request->input('mother_nik'),
            'mother_education' => $request->input('mother_education'),
            'mother_occupation' => $request->input('mother_occupation'),
            'mother_income' => $request->input('mother_income'),
            'mother_phone' => $request->input('mother_phone'),
            'guardian_name' => $request->input('guardian_name'),
            'guardian_nik' => $request->input('guardian_nik'),
            'guardian_education' => $request->input('guardian_education'),
            'guardian_occupation' => $request->input('guardian_occupation'),
            'guardian_income' => $request->input('guardian_income'),
            'guardian_phone' => $request->input('guardian_phone'),
        ]);

        AuditTrailService::log('create', 'kesiswaan', "Menambahkan peserta didik: {$student->name} (NIS: {$student->nis})");

        return redirect()->route('students.index')->with('success', 'Data peserta didik berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $student->load(['family', 'mutations', 'achievements', 'violations', 'scholarships', 'alumnus']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $student->load('family');
        $classes = ReferenceItem::category('kelas')->get();
        $rombels = ReferenceItem::category('rombel')->get();
        $religions = ReferenceItem::category('agama')->get();
        $statuses = ReferenceItem::category('status_siswa')->get();

        return view('students.edit', compact('student', 'classes', 'rombels', 'religions', 'statuses'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:students,nis,'.$student->id],
            'nisn' => ['nullable', 'string', 'max:30', 'unique:students,nisn,'.$student->id],
            'nik' => ['nullable', 'string', 'max:30'],
            'family_card_number' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'religion' => ['nullable', 'string', 'max:30'],
            'child_order' => ['nullable', 'integer', 'min:1'],
            'siblings_count' => ['nullable', 'integer', 'min:0'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'height' => ['nullable', 'integer', 'min:50', 'max:250'],
            'weight' => ['nullable', 'integer', 'min:20', 'max:200'],
            'address' => ['nullable', 'string'],
            'rt_rw' => ['nullable', 'string', 'max:20'],
            'residence_district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
            'living_with' => ['nullable', 'string', 'max:50'],
            'transportation' => ['nullable', 'string', 'max:50'],
            'distance_to_school' => ['nullable', 'string', 'max:50'],
            'special_needs' => ['nullable', 'string', 'max:100'],
            'school_origin' => ['nullable', 'string', 'max:150'],
            'junior_diploma_number' => ['nullable', 'string', 'max:50'],
            'junior_exam_number' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'admission_class' => ['nullable', 'string', 'max:50'],
            'entry_path' => ['nullable', 'string', 'max:50'],
            'is_pip' => ['nullable', 'boolean'],
            'pip_number' => ['nullable', 'string', 'max:50'],
            'current_class_level' => ['required', 'string', 'max:10'],
            'current_rombel' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $oldValues = $student->toArray();
        $studentData = $request->only([
            'nis', 'nisn', 'nik', 'family_card_number', 'name', 'gender', 'birth_place', 'birth_date',
            'religion', 'child_order', 'siblings_count', 'blood_type', 'height', 'weight',
            'address', 'rt_rw', 'residence_district', 'postal_code', 'phone', 'email',
            'living_with', 'transportation', 'distance_to_school', 'special_needs',
            'school_origin', 'junior_diploma_number', 'junior_exam_number',
            'admission_date', 'admission_class', 'entry_path', 'is_pip', 'pip_number',
            'current_class_level', 'current_rombel', 'status',
        ]);
        $studentData['is_pip'] = $request->boolean('is_pip');

        $student->update($studentData);

        $familyData = [
            'father_name' => $request->input('father_name'),
            'father_nik' => $request->input('father_nik'),
            'father_education' => $request->input('father_education'),
            'father_occupation' => $request->input('father_occupation'),
            'father_income' => $request->input('father_income'),
            'father_phone' => $request->input('father_phone'),
            'mother_name' => $request->input('mother_name'),
            'mother_nik' => $request->input('mother_nik'),
            'mother_education' => $request->input('mother_education'),
            'mother_occupation' => $request->input('mother_occupation'),
            'mother_income' => $request->input('mother_income'),
            'mother_phone' => $request->input('mother_phone'),
            'guardian_name' => $request->input('guardian_name'),
            'guardian_nik' => $request->input('guardian_nik'),
            'guardian_education' => $request->input('guardian_education'),
            'guardian_occupation' => $request->input('guardian_occupation'),
            'guardian_income' => $request->input('guardian_income'),
            'guardian_phone' => $request->input('guardian_phone'),
        ];

        if ($student->family) {
            $student->family->update($familyData);
        } else {
            $familyData['student_id'] = $student->id;
            StudentFamily::create($familyData);
        }

        AuditTrailService::log('update', 'kesiswaan', "Memperbarui data siswa: {$student->name}", $oldValues, $studentData);

        return redirect()->route('students.show', $student)->with('success', 'Data siswa dan Buku Induk berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $name = $student->name;
        $student->delete();

        AuditTrailService::log('delete', 'kesiswaan', "Menghapus data siswa: {$name}");

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function exportCsv(): StreamedResponse
    {
        $students = Student::orderBy('current_class_level')->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="data-siswa-sma1le-'.date('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($students) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility

            fputcsv($output, ['No', 'NIS', 'NISN', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Kelas', 'Rombel', 'Status', 'Alamat']);

            foreach ($students as $index => $s) {
                fputcsv($output, [
                    $index + 1,
                    $s->nis,
                    $s->nisn ?? '-',
                    $s->name,
                    $s->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    $s->birth_place ?? '-',
                    $s->birth_date ? $s->birth_date->format('d/m/Y') : '-',
                    $s->religion ?? '-',
                    $s->current_class_level,
                    $s->current_rombel ?? '-',
                    $s->status,
                    $s->address ?? '-',
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Halaman Interaktif Demografi Peserta Didik SMA Lengkap & Real-time
     */
    public function demographics(): View
    {
        $school = SchoolIdentity::first() ?? new SchoolIdentity;

        // 1. Data Pokok & Rasio Gender
        $totalActive = Student::active()->count();
        $maleActive = Student::active()->where('gender', 'L')->count();
        $femaleActive = Student::active()->where('gender', 'P')->count();
        $malePercentage = $totalActive > 0 ? round(($maleActive / $totalActive) * 100, 1) : 0;
        $femalePercentage = $totalActive > 0 ? round(($femaleActive / $totalActive) * 100, 1) : 0;

        // 2. Tingkat Kelas (X, XI, XII)
        if (Schema::hasColumn('students', 'current_class_level')) {
            $classStats = Student::active()
                ->select('current_class_level',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy('current_class_level')
                ->orderBy('current_class_level')
                ->get();

            // 3. Rombel
            $rombelStats = Student::active()
                ->select('current_rombel', 'current_class_level',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->whereNotNull('current_rombel')
                ->where('current_rombel', '!=', '')
                ->groupBy('current_class_level', 'current_rombel')
                ->orderBy('current_class_level')
                ->orderBy('current_rombel')
                ->get();
        } else {
            $classStats = DB::table('students')
                ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                ->where('students.is_active', true)
                ->select(
                    DB::raw("COALESCE(school_classes.grade, 'X') as current_class_level"),
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when students.gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when students.gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy(DB::raw("COALESCE(school_classes.grade, 'X')"))
                ->orderBy('current_class_level')
                ->get();

            $rombelStats = DB::table('students')
                ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                ->where('students.is_active', true)
                ->select(
                    'school_classes.name as current_rombel',
                    DB::raw("COALESCE(school_classes.grade, 'X') as current_class_level"),
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when students.gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when students.gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy('school_classes.name', 'school_classes.grade')
                ->orderBy('current_class_level')
                ->orderBy('current_rombel')
                ->get();
        }

        // 4. Distribusi Usia
        $activeWithBirth = Student::active()->whereNotNull('birth_date')->get();
        $ageBrackets = [
            '< 15 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '15 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '16 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '17 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '18 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '> 18 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
        ];

        foreach ($activeWithBirth as $st) {
            $age = $st->birth_date ? (int) $st->birth_date->age : null;
            if ($age === null) {
                continue;
            }

            $b = match (true) {
                $age < 15 => '< 15 Th',
                $age == 15 => '15 Th',
                $age == 16 => '16 Th',
                $age == 17 => '17 Th',
                $age == 18 => '18 Th',
                default => '> 18 Th',
            };

            $ageBrackets[$b]['total']++;
            if ($st->gender === 'L') {
                $ageBrackets[$b]['L']++;
            } elseif ($st->gender === 'P') {
                $ageBrackets[$b]['P']++;
            }
        }

        // 5. Jalur Masuk PPDB
        $entryPathStats = collect();
        if (Schema::hasColumn('students', 'entry_path')) {
            $entryPathStats = Student::active()
                ->select('entry_path', DB::raw('count(*) as total'))
                ->whereNotNull('entry_path')
                ->where('entry_path', '!=', '')
                ->groupBy('entry_path')
                ->orderByDesc('total')
                ->get();
        }

        // 6. Bantuan PIP / Afirmasi
        $pipActive = 0;
        if (Schema::hasColumn('students', 'is_pip')) {
            $pipActive = Student::active()->where('is_pip', true)->count();
        }
        $nonPipActive = $totalActive - $pipActive;

        // 7. Domisili Kecamatan
        $districtStats = Student::active()
            ->select('residence_district', DB::raw('count(*) as total'))
            ->whereNotNull('residence_district')
            ->where('residence_district', '!=', '')
            ->groupBy('residence_district')
            ->orderByDesc('total')
            ->get();

        // 8. Moda Transportasi
        $transportStats = Student::active()
            ->select('transportation', DB::raw('count(*) as total'))
            ->whereNotNull('transportation')
            ->where('transportation', '!=', '')
            ->groupBy('transportation')
            ->orderByDesc('total')
            ->get();

        // 9. Jarak ke Sekolah
        $distanceStats = Student::active()
            ->select('distance_to_school', DB::raw('count(*) as total'))
            ->whereNotNull('distance_to_school')
            ->where('distance_to_school', '!=', '')
            ->groupBy('distance_to_school')
            ->orderBy('distance_to_school')
            ->get();

        // 10. Agama
        $religionStats = Student::active()
            ->select('religion', DB::raw('count(*) as total'))
            ->whereNotNull('religion')
            ->groupBy('religion')
            ->orderByDesc('total')
            ->get();

        return view('students.demographics', compact(
            'school', 'totalActive', 'maleActive', 'femaleActive', 'malePercentage', 'femalePercentage',
            'classStats', 'rombelStats', 'ageBrackets', 'entryPathStats', 'pipActive', 'nonPipActive',
            'districtStats', 'transportStats', 'distanceStats', 'religionStats'
        ));
    }

    /**
     * Cetak Laporan Resmi Demografi & Profil Peserta Didik SMAN 1 Lengkong
     */
    public function printDemographics(): View
    {
        $school = SchoolIdentity::first() ?? new SchoolIdentity;

        // 1. Data Pokok & Rasio Gender
        $totalActive = Student::active()->count();
        $maleActive = Student::active()->where('gender', 'L')->count();
        $femaleActive = Student::active()->where('gender', 'P')->count();
        $malePercentage = $totalActive > 0 ? round(($maleActive / $totalActive) * 100, 1) : 0;
        $femalePercentage = $totalActive > 0 ? round(($femaleActive / $totalActive) * 100, 1) : 0;

        // 2. Tingkat Kelas (X, XI, XII)
        if (Schema::hasColumn('students', 'current_class_level')) {
            $classStats = Student::active()
                ->select('current_class_level',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy('current_class_level')
                ->orderBy('current_class_level')
                ->get();

            // 3. Rombel
            $rombelStats = Student::active()
                ->select('current_rombel', 'current_class_level',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when gender = "P" then 1 else 0 end) as female')
                )
                ->whereNotNull('current_rombel')
                ->where('current_rombel', '!=', '')
                ->groupBy('current_class_level', 'current_rombel')
                ->orderBy('current_class_level')
                ->orderBy('current_rombel')
                ->get();
        } else {
            $classStats = DB::table('students')
                ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                ->where('students.is_active', true)
                ->select(
                    DB::raw("COALESCE(school_classes.grade, 'X') as current_class_level"),
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when students.gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when students.gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy(DB::raw("COALESCE(school_classes.grade, 'X')"))
                ->orderBy('current_class_level')
                ->get();

            $rombelStats = DB::table('students')
                ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
                ->where('students.is_active', true)
                ->select(
                    'school_classes.name as current_rombel',
                    DB::raw("COALESCE(school_classes.grade, 'X') as current_class_level"),
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when students.gender = "L" then 1 else 0 end) as male'),
                    DB::raw('sum(case when students.gender = "P" then 1 else 0 end) as female')
                )
                ->groupBy('school_classes.name', 'school_classes.grade')
                ->orderBy('current_class_level')
                ->orderBy('current_rombel')
                ->get();
        }

        // 4. Distribusi Usia
        $activeWithBirth = Student::active()->whereNotNull('birth_date')->get();
        $ageBrackets = [
            '< 15 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '15 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '16 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '17 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '18 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
            '> 18 Th' => ['L' => 0, 'P' => 0, 'total' => 0],
        ];

        foreach ($activeWithBirth as $st) {
            $age = $st->birth_date ? (int) $st->birth_date->age : null;
            if ($age === null) {
                continue;
            }

            $b = match (true) {
                $age < 15 => '< 15 Th',
                $age == 15 => '15 Th',
                $age == 16 => '16 Th',
                $age == 17 => '17 Th',
                $age == 18 => '18 Th',
                default => '> 18 Th',
            };

            $ageBrackets[$b]['total']++;
            if ($st->gender === 'L') {
                $ageBrackets[$b]['L']++;
            } elseif ($st->gender === 'P') {
                $ageBrackets[$b]['P']++;
            }
        }

        // 5. Jalur Masuk PPDB
        $entryPathStats = collect();
        if (Schema::hasColumn('students', 'entry_path')) {
            $entryPathStats = Student::active()
                ->select('entry_path', DB::raw('count(*) as total'))
                ->whereNotNull('entry_path')
                ->where('entry_path', '!=', '')
                ->groupBy('entry_path')
                ->orderByDesc('total')
                ->get();
        }

        // 6. Bantuan PIP / Afirmasi
        $pipActive = 0;
        if (Schema::hasColumn('students', 'is_pip')) {
            $pipActive = Student::active()->where('is_pip', true)->count();
        }
        $nonPipActive = $totalActive - $pipActive;

        // 7. Domisili Kecamatan
        $districtStats = Student::active()
            ->select('residence_district', DB::raw('count(*) as total'))
            ->whereNotNull('residence_district')
            ->where('residence_district', '!=', '')
            ->groupBy('residence_district')
            ->orderByDesc('total')
            ->get();

        // 8. Moda Transportasi
        $transportStats = Student::active()
            ->select('transportation', DB::raw('count(*) as total'))
            ->whereNotNull('transportation')
            ->where('transportation', '!=', '')
            ->groupBy('transportation')
            ->orderByDesc('total')
            ->get();

        // 9. Jarak ke Sekolah
        $distanceStats = Student::active()
            ->select('distance_to_school', DB::raw('count(*) as total'))
            ->whereNotNull('distance_to_school')
            ->where('distance_to_school', '!=', '')
            ->groupBy('distance_to_school')
            ->orderBy('distance_to_school')
            ->get();

        // 10. Agama
        $religionStats = Student::active()
            ->select('religion', DB::raw('count(*) as total'))
            ->whereNotNull('religion')
            ->groupBy('religion')
            ->orderByDesc('total')
            ->get();

        return view('students.demographics_print', compact(
            'school', 'totalActive', 'maleActive', 'femaleActive', 'malePercentage', 'femalePercentage',
            'classStats', 'rombelStats', 'ageBrackets', 'entryPathStats', 'pipActive', 'nonPipActive',
            'districtStats', 'transportStats', 'distanceStats', 'religionStats'
        ));
    }
}

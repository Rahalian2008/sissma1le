<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentAlumnus;
use App\Models\StudentDiploma;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDiplomaController extends Controller
{
    public function index(Request $request): View
    {
        $query = StudentDiploma::with(['student', 'handedOverBy']);

        if ($year = $request->query('graduation_year')) {
            $query->where('graduation_year', $year);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('diploma_number', 'like', "%{$search}%")
                    ->orWhere('skl_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
            });
        }

        $diplomas = $query->latest('created_at')->paginate(15)->withQueryString();

        // Eligible students: XII or all active who don't have a diploma entry yet
        $registeredStudentIds = StudentDiploma::pluck('student_id')->toArray();
        $eligibleStudents = Student::whereHas('schoolClass', function ($q) {
            $q->where('name', 'like', '%XII%')->orWhere('name', 'like', '%12%');
        })
            ->whereNotIn('id', $registeredStudentIds)
            ->orderBy('name')
            ->get();

        if ($eligibleStudents->isEmpty()) {
            $eligibleStudents = Student::whereNotIn('id', $registeredStudentIds)->orderBy('name')->take(50)->get();
        }

        $stats = [
            'total' => StudentDiploma::count(),
            'sudah_diambil' => StudentDiploma::where('status', 'Sudah Diambil')->count(),
            'belum_diambil' => StudentDiploma::where('status', 'Belum Diambil')->count(),
            'cap_tiga_jari' => StudentDiploma::where('three_fingerprint_status', true)->count(),
        ];

        $graduationYears = StudentDiploma::distinct()
            ->whereNotNull('graduation_year')
            ->pluck('graduation_year')
            ->toArray();

        if (empty($graduationYears)) {
            $graduationYears = [date('Y') - 1, date('Y'), (date('Y') - 1).'/'.date('Y'), date('Y').'/'.(date('Y') + 1)];
        }

        return view('students.diplomas.index', compact('diplomas', 'eligibleStudents', 'stats', 'graduationYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id', 'unique:student_diplomas,student_id'],
            'diploma_number' => ['nullable', 'string', 'max:100'],
            'skl_number' => ['nullable', 'string', 'max:100'],
            'graduation_year' => ['required', 'string', 'max:20'],
            'handover_date' => ['nullable', 'date'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'recipient_relation' => ['required', 'string', 'max:50'],
            'three_fingerprint_status' => ['nullable', 'boolean'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['three_fingerprint_status'] = $request->boolean('three_fingerprint_status');
        if ($validated['status'] === 'Sudah Diambil') {
            $validated['handed_over_by'] = auth()->id();
            if (empty($validated['handover_date'])) {
                $validated['handover_date'] = now()->toDateString();
            }
        }

        $diploma = StudentDiploma::create($validated);
        $student = Student::find($validated['student_id']);

        // Keterkaitan: Jika sudah diambil atau nomor ijazah diisi, sinkronkan ke Master Siswa & Tracer Alumni
        if ($diploma->status === 'Sudah Diambil' || ! empty($diploma->diploma_number)) {
            $student->update([
                'status' => 'Lulus',
                'diploma_number' => $diploma->diploma_number ?: $student->diploma_number,
                'graduation_year' => $diploma->graduation_year ?: date('Y'),
            ]);

            // Buat entri otomatis ke Tracer Study Alumni jika belum ada
            if (! $student->alumnus) {
                StudentAlumnus::create([
                    'student_id' => $student->id,
                    'graduation_year' => $diploma->graduation_year ?: date('Y'),
                    'activity_status' => 'Kuliah',
                    'phone' => $student->phone,
                    'email' => $student->email,
                    'address' => $student->address,
                ]);
            }
        }

        AuditTrailService::log('create', 'penyerahan_ijazah', "Mencatat tanda terima ijazah untuk siswa: {$student->name} (No Ijazah: {$diploma->diploma_number})");

        return back()->with('success', "Data penyerahan ijazah untuk {$student->name} berhasil dicatat dan terintegrasi dengan status siswa.");
    }

    public function update(Request $request, StudentDiploma $diploma): RedirectResponse
    {
        $validated = $request->validate([
            'diploma_number' => ['nullable', 'string', 'max:100'],
            'skl_number' => ['nullable', 'string', 'max:100'],
            'graduation_year' => ['required', 'string', 'max:20'],
            'handover_date' => ['nullable', 'date'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'recipient_relation' => ['required', 'string', 'max:50'],
            'three_fingerprint_status' => ['nullable', 'boolean'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['three_fingerprint_status'] = $request->boolean('three_fingerprint_status');
        if ($validated['status'] === 'Sudah Diambil') {
            $validated['handed_over_by'] = auth()->id();
            if (empty($validated['handover_date'])) {
                $validated['handover_date'] = now()->toDateString();
            }
        }

        $diploma->update($validated);
        $student = $diploma->student;

        if ($student) {
            $student->update([
                'diploma_number' => $diploma->diploma_number ?: $student->diploma_number,
                'graduation_year' => $diploma->graduation_year ?: $student->graduation_year,
            ]);

            if ($diploma->status === 'Sudah Diambil') {
                $student->update(['status' => 'Lulus']);

                if (! $student->alumnus) {
                    StudentAlumnus::create([
                        'student_id' => $student->id,
                        'graduation_year' => $diploma->graduation_year ?: date('Y'),
                        'activity_status' => 'Kuliah',
                        'phone' => $student->phone,
                        'email' => $student->email,
                        'address' => $student->address,
                    ]);
                }
            }
        }

        AuditTrailService::log('update', 'penyerahan_ijazah', "Memperbarui tanda terima ijazah: {$diploma->student?->name}");

        return back()->with('success', 'Data penyerahan ijazah berhasil diperbarui.');
    }

    public function destroy(StudentDiploma $diploma): RedirectResponse
    {
        $name = $diploma->student?->name ?? 'Siswa';
        $diploma->delete();

        AuditTrailService::log('delete', 'penyerahan_ijazah', "Menghapus data tanda terima ijazah: {$name}");

        return back()->with('success', 'Data penyerahan ijazah berhasil dihapus.');
    }

    /**
     * Cetak Buku Ekspedisi Penyerahan Ijazah & SKL (Format Standar Kemendikbud & Disdik Jabar)
     */
    public function printBook(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = StudentDiploma::with(['student', 'handedOverBy']);

        if ($year = $request->query('graduation_year')) {
            $query->where('student_diplomas.graduation_year', $year);
        }

        if ($status = $request->query('status')) {
            $query->where('student_diplomas.status', $status);
        }

        $diplomas = $query->join('students', 'student_diplomas.student_id', '=', 'students.id')
            ->orderBy('students.name')
            ->select('student_diplomas.*')
            ->get();

        $selectedYear = $request->query('graduation_year', date('Y'));

        AuditTrailService::log('print', 'penyerahan_ijazah', "Mencetak Buku Ekspedisi Tanda Terima Ijazah Tahun {$selectedYear}");

        return view('students.diplomas.print_book', compact('school', 'diplomas', 'selectedYear'));
    }

    /**
     * Cetak Lembar Tanda Terima Pengambilan Ijazah Perorangan
     */
    public function printReceipt(StudentDiploma $diploma): View
    {
        $school = SchoolIdentity::current();
        $diploma->load(['student', 'handedOverBy']);

        $verificationUrl = route('verify.public', ['type' => 'student', 'token' => $diploma->student?->qr_code_token ?? $diploma->student?->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 100);

        AuditTrailService::log('print', 'tanda_terima_ijazah', "Mencetak Tanda Terima Ijazah perorangan: {$diploma->student?->name}");

        return view('students.diplomas.print_receipt', compact('school', 'diploma', 'qrCodeSvg'));
    }
}

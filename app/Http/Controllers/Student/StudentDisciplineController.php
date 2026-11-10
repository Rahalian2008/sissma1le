<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentScholarship;
use App\Models\StudentViolation;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDisciplineController extends Controller
{
    public function achievements(Request $request): View
    {
        $achievements = StudentAchievement::with('student')
            ->latest('date')
            ->paginate(15);
        $students = Student::active()->orderBy('name')->get();

        return view('students.achievements', compact('achievements', 'students'));
    }

    public function storeAchievement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'level' => ['required', 'string'],
            'rank' => ['required', 'string'],
            'date' => ['required', 'date'],
            'organizer' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $achievement = StudentAchievement::create($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('create', 'prestasi', "Mencatat prestasi [{$achievement->name}] untuk siswa: {$student->name}");

        return back()->with('success', 'Data prestasi siswa berhasil ditambahkan.');
    }

    public function updateAchievement(Request $request, StudentAchievement $achievement): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'level' => ['required', 'string'],
            'rank' => ['required', 'string'],
            'date' => ['required', 'date'],
            'organizer' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $achievement->update($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('update', 'prestasi', "Memperbarui data prestasi [{$achievement->name}] untuk siswa: {$student->name}");

        return back()->with('success', 'Data prestasi siswa berhasil diperbarui.');
    }

    public function violations(Request $request): View
    {
        $violations = StudentViolation::with('student')
            ->latest('violation_date')
            ->paginate(15);
        $students = Student::active()->orderBy('name')->get();

        return view('students.violations', compact('violations', 'students'));
    }

    public function storeViolation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'violation_name' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            'violation_date' => ['required', 'date'],
            'chronology' => ['nullable', 'string'],
            'action_taken' => ['nullable', 'string'],
            'counseling_notes' => ['nullable', 'string'],
            'resolution_status' => ['required', 'string'],
            'officer_name' => ['nullable', 'string'],
        ]);

        $violation = StudentViolation::create($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('create', 'pelanggaran', "Mencatat pelanggaran ({$violation->points} poin) untuk siswa: {$student->name}");

        return back()->with('success', 'Data pelanggaran kedisiplinan berhasil dicatat.');
    }

    public function updateViolation(Request $request, StudentViolation $violation): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'violation_name' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            'violation_date' => ['required', 'date'],
            'chronology' => ['nullable', 'string'],
            'action_taken' => ['nullable', 'string'],
            'counseling_notes' => ['nullable', 'string'],
            'resolution_status' => ['required', 'string'],
            'officer_name' => ['nullable', 'string'],
        ]);

        $violation->update($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('update', 'pelanggaran', "Memperbarui catatan pelanggaran ({$violation->points} poin) untuk siswa: {$student->name}");

        return back()->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function scholarships(Request $request): View
    {
        $scholarships = StudentScholarship::with('student')
            ->latest()
            ->paginate(15);
        $students = Student::active()->orderBy('name')->get();

        return view('students.scholarships', compact('scholarships', 'students'));
    }

    public function storeScholarship(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'scholarship_name' => ['required', 'string', 'max:150'],
            'source' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'period' => ['required', 'string', 'max:30'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $scholarship = StudentScholarship::create($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('create', 'beasiswa', "Mencatat beasiswa [{$scholarship->scholarship_name}] untuk siswa: {$student->name}");

        return back()->with('success', 'Data beasiswa berhasil ditambahkan.');
    }

    public function updateScholarship(Request $request, StudentScholarship $scholarship): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'scholarship_name' => ['required', 'string', 'max:150'],
            'source' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'period' => ['required', 'string', 'max:30'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $scholarship->update($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('update', 'beasiswa', "Memperbarui beasiswa [{$scholarship->scholarship_name}] untuk siswa: {$student->name}");

        return back()->with('success', 'Data beasiswa berhasil diperbarui.');
    }

    public function destroyAchievement(StudentAchievement $achievement): RedirectResponse
    {
        $name = $achievement->name;
        $studentName = $achievement->student?->name ?? 'Siswa';
        $achievement->delete();

        AuditTrailService::log('delete', 'prestasi', "Menghapus data prestasi [{$name}] siswa {$studentName}");

        return back()->with('success', 'Data prestasi berhasil dihapus.');
    }

    public function destroyViolation(StudentViolation $violation): RedirectResponse
    {
        $name = $violation->violation_name;
        $studentName = $violation->student?->name ?? 'Siswa';
        $violation->delete();

        AuditTrailService::log('delete', 'pelanggaran', "Menghapus data pelanggaran [{$name}] siswa {$studentName}");

        return back()->with('success', 'Data pelanggaran berhasil dihapus.');
    }

    public function destroyScholarship(StudentScholarship $scholarship): RedirectResponse
    {
        $name = $scholarship->scholarship_name;
        $studentName = $scholarship->student?->name ?? 'Siswa';
        $scholarship->delete();

        AuditTrailService::log('delete', 'beasiswa', "Menghapus data beasiswa [{$name}] siswa {$studentName}");

        return back()->with('success', 'Data beasiswa berhasil dihapus.');
    }

    /**
     * Cetak Surat Peringatan (SP 1, SP 2, atau SP 3) Siswa Resmi
     */
    public function printSp(StudentViolation $violation, Request $request): View
    {
        $school = SchoolIdentity::current();
        $violation->load(['student.family']);
        $spLevel = $request->query('level', $violation->resolution_status);
        if (! in_array($spLevel, ['SP 1', 'SP 2', 'SP 3', 'SP1', 'SP2', 'SP3'])) {
            $spLevel = 'SP 1';
        }

        $allViolations = StudentViolation::where('student_id', $violation->student_id)
            ->orderBy('violation_date')
            ->get();

        $totalPoints = $allViolations->sum('points');

        $verificationUrl = route('verify.public', ['type' => 'student', 'token' => $violation->student?->qr_code_token ?? $violation->student?->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 90);

        $waliKelas = Employee::getWaliKelasForRombel($violation->student?->current_rombel);

        AuditTrailService::log('print', 'surat_peringatan', "Mencetak Surat Peringatan ({$spLevel}) siswa: {$violation->student?->name}");

        return view('students.violations.print_sp', compact('school', 'violation', 'spLevel', 'allViolations', 'totalPoints', 'qrCodeSvg', 'waliKelas'));
    }

    /**
     * Cetak Surat Panggilan Orang Tua / Wali Siswa
     */
    public function printParentCall(StudentViolation $violation, Request $request): View
    {
        $school = SchoolIdentity::current();
        $violation->load(['student.family']);

        $callDate = $request->query('call_date', now()->addDays(2)->translatedFormat('l, d F Y'));
        $callTime = $request->query('call_time', '09:00 WIB');
        $callRoom = $request->query('call_room', 'Ruang Bimbingan Konseling (BK) / Tata Usaha');

        $verificationUrl = route('verify.public', ['type' => 'student', 'token' => $violation->student?->qr_code_token ?? $violation->student?->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 90);

        $waliKelas = Employee::getWaliKelasForRombel($violation->student?->current_rombel);

        AuditTrailService::log('print', 'panggilan_ortu', "Mencetak Surat Panggilan Orang Tua siswa: {$violation->student?->name}");

        return view('students.violations.print_parent_call', compact('school', 'violation', 'callDate', 'callTime', 'callRoom', 'qrCodeSvg', 'waliKelas'));
    }

    /**
     * Cetak Buku Catatan Pelanggaran & Tata Tertib Siswa
     */
    public function printDisciplineBook(Request $request): View
    {
        $school = SchoolIdentity::current();
        $violations = StudentViolation::with('student')
            ->latest('violation_date')
            ->get();

        AuditTrailService::log('print', 'buku_pelanggaran', 'Mencetak Buku Catatan Pelanggaran & Tata Tertib Siswa');

        return view('students.violations.print_book', compact('school', 'violations'));
    }
}

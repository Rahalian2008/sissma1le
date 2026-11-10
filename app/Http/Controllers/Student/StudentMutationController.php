<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentMutation;
use App\Services\AuditTrailService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentMutationController extends Controller
{
    public function index(Request $request): View
    {
        $query = StudentMutation::with(['student', 'approver']);

        if ($type = $request->query('type')) {
            $query->where('mutation_type', $type);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                    ->orWhere('school_target_or_origin', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
            });
        }

        $mutations = $query->latest('mutation_date')->paginate(15)->withQueryString();
        $students = Student::active()->orderBy('name')->get();

        $stats = [
            'total' => StudentMutation::count(),
            'masuk' => StudentMutation::where('mutation_type', 'like', '%Masuk%')->count(),
            'keluar' => StudentMutation::where('mutation_type', 'like', '%Keluar%')->orWhere('mutation_type', 'like', '%Pindah%')->count(),
            'lulus' => StudentMutation::where('mutation_type', 'like', '%Lulus%')->count(),
        ];

        return view('mutations.index', compact('mutations', 'students', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'mutation_type' => ['required', 'string'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'mutation_date' => ['required', 'date'],
            'school_target_or_origin' => ['nullable', 'string', 'max:150'],
            'reason' => ['nullable', 'string'],
        ]);

        if (empty($validated['document_number'])) {
            $seq = StudentMutation::whereYear('created_at', date('Y'))->count() + 1;
            $validated['document_number'] = '421.3/'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT).'-SMA1LE/MUT/'.date('Y');
        }

        $validated['status'] = 'Disetujui';
        $validated['approved_by'] = auth()->id();

        $mutation = StudentMutation::create($validated);

        // Update student status according to mutation type
        $student = Student::find($validated['student_id']);
        $mType = strtolower($validated['mutation_type']);
        if (str_contains($mType, 'keluar') || str_contains($mType, 'pindah')) {
            $student->update(['status' => 'Mutasi Keluar']);
        } elseif (str_contains($mType, 'mengundurkan') || str_contains($mType, 'dropout') || str_contains($mType, 'dikeluarkan')) {
            $student->update(['status' => 'Mengundurkan Diri']);
        } elseif (str_contains($mType, 'lulus')) {
            $student->update(['status' => 'Lulus', 'graduation_year' => date('Y')]);
        } elseif (str_contains($mType, 'masuk')) {
            $student->update(['status' => 'Aktif']);
        }

        AuditTrailService::log('create', 'mutasi_siswa', "Mencatat mutasi [{$mutation->mutation_type}] untuk siswa: {$student->name}");

        return back()->with('success', "Data mutasi siswa {$student->name} berhasil disimpan dan status siswa diperbarui.");
    }

    public function update(Request $request, StudentMutation $mutation): RedirectResponse
    {
        $validated = $request->validate([
            'mutation_type' => ['required', 'string'],
            'mutation_date' => ['required', 'date'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'school_target_or_origin' => ['nullable', 'string', 'max:150'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $mutation->update($validated);

        // Update student status
        $student = $mutation->student;
        if ($student) {
            $mType = strtolower($validated['mutation_type']);
            if (str_contains($mType, 'keluar') || str_contains($mType, 'pindah')) {
                $student->update(['status' => 'Mutasi Keluar']);
            } elseif (str_contains($mType, 'mengundurkan') || str_contains($mType, 'dropout') || str_contains($mType, 'dikeluarkan')) {
                $student->update(['status' => 'Mengundurkan Diri']);
            } elseif (str_contains($mType, 'lulus')) {
                $student->update(['status' => 'Lulus']);
            } elseif (str_contains($mType, 'masuk')) {
                $student->update(['status' => 'Aktif']);
            }
        }

        AuditTrailService::log('update', 'mutasi_siswa', "Memperbarui mutasi untuk: {$student?->name}");

        return back()->with('success', 'Catatan mutasi berhasil diperbarui.');
    }

    public function destroy(StudentMutation $mutation): RedirectResponse
    {
        $studentName = $mutation->student?->name ?? 'Siswa';
        $mutation->delete();

        AuditTrailService::log('delete', 'mutasi_siswa', "Menghapus catatan mutasi untuk: {$studentName}");

        return back()->with('success', 'Catatan mutasi berhasil dihapus.');
    }

    /**
     * Cetak Buku Catatan Mutasi Siswa (Buku Kendali Mutasi Format Standar TU Kemendikbud & Disdik Jabar)
     */
    public function printBook(Request $request): View
    {
        $school = SchoolIdentity::current();
        $query = StudentMutation::with(['student', 'approver']);

        if ($type = $request->query('type')) {
            $query->where('mutation_type', $type);
        }

        $mutations = $query->latest('mutation_date')->get();

        AuditTrailService::log('print', 'mutasi_siswa', 'Mencetak Buku Catatan Mutasi Peserta Didik');

        return view('mutations.print_book', compact('school', 'mutations'));
    }

    /**
     * Cetak Surat Keterangan Pindah Sekolah / Rekomendasi Mutasi Siswa Resmi Disdik Jabar
     */
    public function printLetter(StudentMutation $mutation): View
    {
        $school = SchoolIdentity::current();
        $mutation->load(['student.family', 'approver']);

        $verificationUrl = route('verify.public', ['type' => 'student', 'token' => $mutation->student?->qr_code_token ?? $mutation->student?->nis]);
        $qrCodeSvg = QrCodeService::svg($verificationUrl, 100);

        AuditTrailService::log('print', 'surat_mutasi', "Mencetak Surat Keterangan Pindah Sekolah untuk siswa: {$mutation->student?->name}");

        return view('mutations.print_letter', compact('school', 'mutation', 'qrCodeSvg'));
    }
}

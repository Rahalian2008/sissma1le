<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\StudentScholarship;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentScholarshipController extends Controller
{
    public function index(Request $request): View
    {
        if (! Schema::hasTable('student_scholarships')) {
            $scholarships = new LengthAwarePaginator([], 0, 15);
            $stats = ['total' => 0, 'pip' => 0, 'ketm' => 0, 'cair' => 0, 'total_amount' => 0];
            $students = Student::active()->orderBy('name')->get();

            return view('students.scholarships.index', compact('scholarships', 'stats', 'students'));
        }

        $query = StudentScholarship::with(['student.schoolClass'])->latest();

        if ($request->filled('type')) {
            $query->where('scholarship_name', 'like', "%{$request->type}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('scholarship_name', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $scholarships = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => StudentScholarship::count(),
            'pip' => StudentScholarship::where('scholarship_name', 'like', '%PIP%')->count(),
            'ketm' => StudentScholarship::where('scholarship_name', 'like', '%KETM%')->count(),
            'cair' => StudentScholarship::whereIn('status', ['Cair', 'Tersalurkan', 'Diterima'])->count(),
            'total_amount' => (float) StudentScholarship::sum('amount'),
        ];

        $students = Student::active()->with('schoolClass')->orderBy('name')->get();
        if ($students->isEmpty()) {
            $students = Student::with('schoolClass')->orderBy('name')->take(50)->get();
        }

        return view('students.scholarships.index', compact('scholarships', 'stats', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'scholarship_name' => ['required', 'string', 'max:100'],
            'source' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'period' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'max:50'],
            'document_path' => ['nullable', 'string', 'max:255'],
        ]);

        $scholarship = StudentScholarship::create($validated);
        $student = Student::find($validated['student_id']);

        AuditTrailService::log('create', 'beasiswa', "Mencatat beasiswa {$scholarship->scholarship_name} untuk siswa {$student?->name}");

        return back()->with('success', "Data beasiswa {$scholarship->scholarship_name} untuk siswa {$student?->name} berhasil disimpan.");
    }

    public function update(Request $request, StudentScholarship $scholarship): RedirectResponse
    {
        $validated = $request->validate([
            'scholarship_name' => ['required', 'string', 'max:100'],
            'source' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'period' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'max:50'],
            'document_path' => ['nullable', 'string', 'max:255'],
        ]);

        $scholarship->update($validated);

        AuditTrailService::log('update', 'beasiswa', "Memperbarui data beasiswa ID #{$scholarship->id} untuk siswa {$scholarship->student?->name}");

        return back()->with('success', 'Data penerima beasiswa berhasil diperbarui.');
    }

    public function destroy(StudentScholarship $scholarship): RedirectResponse
    {
        $name = $scholarship->student?->name;
        $scholarship->delete();

        AuditTrailService::log('delete', 'beasiswa', "Menghapus data beasiswa siswa {$name}");

        return back()->with('success', 'Data penerima beasiswa berhasil dihapus.');
    }

    public function print(Request $request): View
    {
        $school = SchoolIdentity::current();

        if (! Schema::hasTable('student_scholarships')) {
            $scholarships = collect();

            return view('students.scholarships.print', compact('school', 'scholarships'));
        }

        $query = StudentScholarship::with(['student.schoolClass'])->latest();

        if ($request->filled('type')) {
            $query->where('scholarship_name', 'like', "%{$request->type}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $scholarships = $query->get();

        AuditTrailService::log('print', 'beasiswa', 'Mencetak Rekapitulasi Data Penerima Beasiswa / Bantuan Siswa');

        return view('students.scholarships.print', compact('school', 'scholarships'));
    }
}

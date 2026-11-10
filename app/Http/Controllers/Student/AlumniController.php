<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAlumnus;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AlumniController extends Controller
{
    public function index(Request $request): View
    {
        $query = StudentAlumnus::with('student');

        if ($year = $request->query('graduation_year')) {
            $query->where('graduation_year', $year);
        }

        if ($status = $request->query('activity_status')) {
            $query->where('activity_status', $status);
        }

        $alumni = $query->latest('graduation_year')->paginate(15);
        $graduatedQuery = Student::query();
        if (Schema::hasColumn('students', 'status')) {
            $graduatedQuery->whereIn('status', ['Lulus']);
        } else {
            $graduatedQuery->where('is_active', false);
        }
        $graduatedStudents = $graduatedQuery->orderBy('name')->get();

        return view('alumni.index', compact('alumni', 'graduatedStudents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'graduation_year' => ['required', 'string', 'max:10'],
            'activity_status' => ['required', 'string', 'max:50'],
            'campus_name' => ['nullable', 'string', 'max:150'],
            'major' => ['nullable', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:100'],
        ]);

        $alumnus = StudentAlumnus::updateOrCreate(
            ['student_id' => $validated['student_id']],
            $validated
        );

        $student = Student::find($validated['student_id']);
        AuditTrailService::log('create', 'alumni', "Memperbarui tracer alumni: {$student->name} (Lulus: {$alumnus->graduation_year})");

        return back()->with('success', 'Data tracer alumni berhasil disimpan.');
    }
}

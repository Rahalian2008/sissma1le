<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\AiStudentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AiAnalysisController extends Controller
{
    public function __construct(
        protected AiStudentAnalysisService $aiService
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $student = null;

        if ($user->role === 'siswa') {
            $student = $user->student ?? Student::first();
        } elseif ($user->role === 'orang_tua') {
            $student = $user->parentProfile?->student ?? Student::first();
        } else {
            $studentId = $request->input('student_id');
            $student = $studentId ? Student::find($studentId) : Student::first();
        }

        $allStudents = in_array($user->role, ['guru', 'wali_kelas', 'bk', 'kesiswaan', 'admin', 'super_admin'])
            ? Student::with('schoolClass')->get()
            : collect();

        $analysis = $student ? $this->aiService->analyzeStudent($student) : null;

        return view('ai.analysis', compact('student', 'analysis', 'allStudents'));
    }
}

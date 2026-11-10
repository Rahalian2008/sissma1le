<?php

namespace App\Http\Controllers;

use App\Models\AcademicSupervision;
use App\Models\CourseClass;
use App\Models\LessonPlan;
use App\Models\QuestionBank;
use App\Models\RemediationEnrichmentPlan;
use App\Models\Teacher;
use App\Models\TeacherReflection;
use App\Models\TeachingJournal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupervisionController extends Controller
{
    /**
     * Dashboard Pengawasan & Supervisi Akademik Kurikulum Merdeka.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $supervisionsQuery = AcademicSupervision::with(['teacher', 'supervisor', 'courseClass.subject'])
            ->latest('supervision_date');

        if (! $isLeader && $teacher) {
            $supervisionsQuery->where('teacher_id', $teacher->id);
        }

        $supervisions = $supervisionsQuery->paginate(10)->withQueryString();

        // 1. Matriks Kesiapan Administrasi & Perangkat Ajar Guru (5 Dimensi Kurikulum Merdeka)
        $teachersQuery = Teacher::with(['courseClasses.subject']);
        if (! $isLeader && $teacher) {
            $teachersQuery->where('id', $teacher->id);
        }
        $teachers = $teachersQuery->orderBy('name')->get();

        $auditMatrix = $teachers->map(function ($t) {
            $classIds = $t->courseClasses->pluck('id');

            // 1. Perencanaan (Modul Ajar)
            $totalModul = LessonPlan::whereIn('course_class_id', $classIds)->count();
            $approvedModul = LessonPlan::whereIn('course_class_id', $classIds)->where('status', 'APPROVED')->count();

            // 2. Pelaksanaan KBM (Jurnal Harian)
            $totalJournal = TeachingJournal::where('teacher_id', $t->id)->count();
            $verifiedJournal = TeachingJournal::where('teacher_id', $t->id)->where('is_verified_by_kurikulum', true)->count();

            // 3. Asesmen (Bank Soal Terhubung TP)
            $totalQuestions = QuestionBank::where('teacher_id', $t->id)->count();
            $tpQuestions = QuestionBank::where('teacher_id', $t->id)->whereNotNull('learning_objective_id')->count();

            // 4. Refleksi KBM
            $totalReflections = TeacherReflection::where('teacher_id', $t->id)->count();

            // 5. Remedial & Pengayaan
            $totalRemediation = RemediationEnrichmentPlan::where('teacher_id', $t->id)->count();

            // Indeks kelengkapan persentase
            $score = 0;
            if ($totalModul > 0) {
                $score += 25;
            }
            if ($totalJournal > 0) {
                $score += 25;
            }
            if ($totalQuestions > 0) {
                $score += 20;
            }
            if ($totalReflections > 0) {
                $score += 15;
            }
            if ($totalRemediation > 0) {
                $score += 15;
            }

            return [
                'teacher' => $t,
                'total_classes' => $classIds->count(),
                'modul_ajar' => ['total' => $totalModul, 'approved' => $approvedModul],
                'journal' => ['total' => $totalJournal, 'verified' => $verifiedJournal],
                'questions' => ['total' => $totalQuestions, 'with_tp' => $tpQuestions],
                'reflections' => $totalReflections,
                'remediation' => $totalRemediation,
                'readiness_percent' => $score,
            ];
        });

        // 2. Statistik Supervisi
        $allSupervisions = AcademicSupervision::all();
        $stats = [
            'total_teachers' => $teachers->count(),
            'supervision_count' => $allSupervisions->count(),
            'average_score' => $allSupervisions->count() > 0 ? round($allSupervisions->avg('final_score'), 1) : 0,
            'high_performing' => $allSupervisions->whereIn('predicate', ['AMAT BAIK', 'BAIK'])->count(),
        ];

        $courseClasses = CourseClass::with(['subject', 'schoolClass'])->where('is_active', true)->get();

        return view('lms.supervision.index', compact(
            'supervisions',
            'auditMatrix',
            'teachers',
            'courseClasses',
            'stats',
            'isLeader'
        ));
    }

    /**
     * Simpan hasil observasi supervisi akademik oleh pimpinan/pengawas.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])) {
            abort(403, 'Hanya Pimpinan Sekolah atau Pengawas yang berhak mencatat hasil supervisi.');
        }

        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'course_class_id' => 'nullable|exists:course_classes,id',
            'supervision_date' => 'required|date',
            'instrument_type' => 'nullable|string|max:50',
            'planning_score' => 'required|numeric|min:0|max:100',
            'execution_score' => 'required|numeric|min:0|max:100',
            'assessment_score' => 'required|numeric|min:0|max:100',
            'reflection_score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:3000',
            'recommendation' => 'nullable|string|max:3000',
        ]);

        $finalScore = round(($validated['planning_score'] + $validated['execution_score'] + $validated['assessment_score'] + $validated['reflection_score']) / 4, 2);

        $predicate = match (true) {
            $finalScore >= 91 => 'AMAT BAIK',
            $finalScore >= 81 => 'BAIK',
            $finalScore >= 71 => 'CUKUP',
            default => 'KURANG',
        };

        AcademicSupervision::create([
            'teacher_id' => $validated['teacher_id'],
            'course_class_id' => $validated['course_class_id'] ?? null,
            'supervisor_id' => $user->id,
            'academic_year' => '2026/2027',
            'semester' => 'GANJIL',
            'supervision_date' => $validated['supervision_date'],
            'instrument_type' => $validated['instrument_type'] ?? 'LENGKAP',
            'planning_score' => $validated['planning_score'],
            'execution_score' => $validated['execution_score'],
            'assessment_score' => $validated['assessment_score'],
            'reflection_score' => $validated['reflection_score'],
            'final_score' => $finalScore,
            'predicate' => $predicate,
            'notes' => $validated['notes'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'status' => 'FINAL',
        ]);

        return redirect()->back()->with('success', 'Instrumen supervisi akademik guru berhasil dicatat dan disahkan.');
    }

    /**
     * Hapus instrumen supervisi.
     */
    public function destroy(AcademicSupervision $supervision): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $supervision->delete();

        return redirect()->back()->with('success', 'Hasil supervisi berhasil dihapus.');
    }

    /**
     * Lembar Cetak Resmi Hasil Supervisi Akademik Guru (A4).
     */
    public function print(AcademicSupervision $supervision): View
    {
        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);
        if (! $isLeader) {
            if (! $user->teacher || $user->teacher->id !== $supervision->teacher_id) {
                abort(403, 'Anda tidak berhak melihat hasil supervisi guru lain.');
            }
        }

        $supervision->load(['teacher', 'supervisor', 'courseClass.subject', 'courseClass.schoolClass']);

        return view('lms.supervision.print', compact('supervision'));
    }
}

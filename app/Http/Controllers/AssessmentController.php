<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LmsQuiz;
use App\Models\QuestionBank;
use App\Models\RemediationEnrichmentPlan;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Services\Ai\AiCurriculumManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(
        protected AiCurriculumManager $aiManager
    ) {}

    /**
     * Halaman Utama Bank Soal Terhubung Tujuan Pembelajaran (TP)
     */
    public function bankIndex(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user->isTeacher();
        $isKurikulum = $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $subjectFilter = $request->query('subject_id');
        $gradeFilter = $request->query('grade_level');
        $typeFilter = $request->query('question_type');
        $cognitiveFilter = $request->query('cognitive_level');

        $subjectsQuery = Subject::where('is_active', true)->orderBy('name');
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $mySubjectIds = CourseClass::where('teacher_id', $teacher->id)->pluck('subject_id')->unique();
            if ($mySubjectIds->isNotEmpty()) {
                $subjectsQuery->whereIn('id', $mySubjectIds);
            }
        }
        $subjects = $subjectsQuery->get();

        $questionsQuery = QuestionBank::with(['subject', 'teacher.user', 'learningObjective']);

        if ($subjectFilter) {
            $questionsQuery->where('subject_id', $subjectFilter);
        }
        if ($gradeFilter) {
            $questionsQuery->where('grade_level', $gradeFilter);
        }
        if ($typeFilter) {
            $questionsQuery->where('question_type', $typeFilter);
        }
        if ($cognitiveFilter) {
            $questionsQuery->where('cognitive_level', $cognitiveFilter);
        }

        $questions = $questionsQuery->latest()->paginate(15);

        // Tujuan Pembelajaran untuk dropdown form tambah soal
        $objectivesQuery = LearningObjective::where('is_active', true)->orderBy('tp_code');
        if ($subjectFilter) {
            $objectivesQuery->where('subject_id', $subjectFilter);
        }
        $objectives = $objectivesQuery->get();

        // Statistik Bank Soal
        $stats = [
            'total' => QuestionBank::count(),
            'pg' => QuestionBank::where('question_type', 'multiple_choice')->count(),
            'essay' => QuestionBank::whereIn('question_type', ['essay', 'uraian'])->count(),
            'hots' => QuestionBank::where('cognitive_level', 'L3')->count(),
        ];

        return view('lms.assessment.bank', compact(
            'questions',
            'subjects',
            'objectives',
            'stats',
            'isKurikulum',
            'isTeacher',
            'subjectFilter',
            'gradeFilter',
            'typeFilter',
            'cognitiveFilter'
        ));
    }

    /**
     * Simpan Butir Soal Baru ke Bank Soal
     */
    public function storeQuestion(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'guru', 'kurikulum'])) {
            abort(403, 'Hanya pendidik yang dapat menambahkan butir soal ke Bank Soal.');
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'grade_level' => 'required|in:X,XI,XII',
            'topic' => 'required|string|max:255',
            'question_type' => 'required|in:multiple_choice,essay',
            'cognitive_level' => 'required|in:L1,L2,L3',
            'indicator' => 'nullable|string',
            'stimulus' => 'nullable|string',
            'question_text' => 'required|string',
            'score_weight' => 'required|integer|min:1|max:100',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'options' => 'nullable|array',
        ]);

        $teacher = $user->teacher;

        $question = QuestionBank::create([
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $teacher?->id,
            'learning_objective_id' => $validated['learning_objective_id'] ?? null,
            'grade_level' => $validated['grade_level'],
            'topic' => $validated['topic'],
            'question_type' => $validated['question_type'],
            'cognitive_level' => $validated['cognitive_level'],
            'indicator' => $validated['indicator'] ?? null,
            'stimulus' => $validated['stimulus'] ?? null,
            'question_text' => $validated['question_text'],
            'score_weight' => $validated['score_weight'],
            'options' => $validated['options'] ?? null,
            'correct_answer' => $validated['correct_answer'] ?? null,
            'explanation' => $validated['explanation'] ?? null,
            'is_active' => true,
        ]);

        AuditLog::log('QUESTION_BANK_CREATED', 'QuestionBank', $question->id, null, [
            'topic' => $question->topic,
            'type' => $question->question_type,
        ]);

        return redirect()->route('lms.assessment.bank')
            ->with('success', 'Butir soal berhasil ditambahkan ke Bank Soal.');
    }

    /**
     * Hapus Butir Soal dari Bank Soal
     */
    public function destroyQuestion(QuestionBank $question): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum'])) {
            if (! $user->teacher || $question->teacher_id !== $user->teacher->id) {
                abort(403, 'Anda tidak berhak menghapus butir soal ini.');
            }
        }

        $question->delete();

        return redirect()->route('lms.assessment.bank')
            ->with('success', 'Butir soal berhasil dihapus dari Bank Soal.');
    }

    /**
     * Asisten AI Generator Soal Terpetakan TP & Level Kognitif
     */
    public function aiGenerateQuestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_name' => 'required|string',
            'topic' => 'required|string',
            'grade' => 'required|string|in:X,XI,XII',
            'cognitive_level' => 'required|string|in:L1,L2,L3',
            'tp_context' => 'nullable|string',
        ]);

        $levelDesc = match ($validated['cognitive_level']) {
            'L1' => 'Level 1: Mengingat & Memahami (C1-C2)',
            'L3' => 'Level 3: HOTS / Penalaran & Analisis Kritis (C4-C6)',
            default => 'Level 2: Aplikasi Konsep (C3)',
        };

        $prompt = "Sebagai pembuat soal kurikulum SMA berstandar nasional, buatkan 2 butir soal ujian untuk mata pelajaran {$validated['subject_name']} Kelas {$validated['grade']} dengan topik: '{$validated['topic']}'.\n"
            .($validated['tp_context'] ? "Tujuan Pembelajaran: {$validated['tp_context']}\n" : '')
            ."Target Kognitif: {$levelDesc}.\n"
            ."Sertakan:\n"
            ."1. Soal Pilihan Ganda: Stimulus studi kasus ringkas, teks soal, opsi A-E, kunci jawaban, dan pembahasan.\n"
            .'2. Soal Uraian: Pertanyaan pemecahan masalah terbuka dan pedoman penskoran (rubrik).';

        $result = $this->aiManager->generateDraft($prompt, [
            'task' => 'bank_soal',
            'topic' => $validated['topic'],
            'grade' => $validated['grade'],
            'subject' => $validated['subject_name'],
            'cognitive' => $validated['cognitive_level'],
        ]);

        return response()->json(array_merge($result, [
            'status' => ($result['success'] ?? false) ? 'ok' : 'error',
        ]));
    }

    /**
     * Cetak Dokumen Resmi Kisi-Kisi Asesmen SMAN 1 Lengkong
     */
    public function printKisiKisi(Request $request): View
    {
        $quizId = $request->query('quiz_id');
        $subjectId = $request->query('subject_id');

        $quiz = null;
        $items = collect();

        if ($quizId) {
            $quiz = LmsQuiz::with(['courseClass.subject', 'courseClass.schoolClass', 'courseClass.teacher.user', 'learningObjective', 'questions.learningObjective'])->findOrFail($quizId);
            $user = Auth::user();
            if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'kepala_sekolah'])) {
                if (! $user->teacher || $quiz->courseClass->teacher_id !== $user->teacher->id) {
                    abort(403, 'Anda tidak memiliki hak akses mencetak kisi-kisi asesmen ini.');
                }
            }
            $items = $quiz->questions;
        } elseif ($subjectId) {
            $items = QuestionBank::with(['learningObjective', 'subject'])->where('subject_id', $subjectId)->get();
        }

        $settings = SchoolSetting::whereIn('key', [
            'school_name',
            'school_kop',
            'school_principal',
            'principal_nip',
            'wakasek_kurikulum_name',
            'wakasek_kurikulum_nip',
        ])->pluck('value', 'key');

        return view('lms.assessment.print_kisi_kisi', compact('quiz', 'items', 'settings'));
    }

    /**
     * Halaman Pengelolaan Remedial & Pengayaan Terhubung TP
     */
    public function remediationIndex(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user->isTeacher();
        $isKurikulum = $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $classFilter = $request->query('course_class_id');

        $classesQuery = CourseClass::with(['subject', 'schoolClass', 'teacher'])->where('is_active', true);
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $classesQuery->where('teacher_id', $teacher->id);
        }
        $myClasses = $classesQuery->get();

        $plansQuery = RemediationEnrichmentPlan::with(['courseClass.subject', 'courseClass.schoolClass', 'teacher.user', 'objective']);

        if ($isTeacher && ! $isKurikulum && $teacher) {
            $plansQuery->where('teacher_id', $teacher->id);
        }
        if ($classFilter) {
            $plansQuery->where('course_class_id', $classFilter);
        }

        $plans = $plansQuery->latest()->paginate(15);

        // Siswa dan TP untuk form tambah rencana remedial
        $availableStudents = collect();
        $objectives = collect();
        if ($classFilter) {
            $selectedClass = CourseClass::find($classFilter);
            if ($selectedClass) {
                $availableStudents = Student::where('class_id', $selectedClass->class_id)->orderBy('name')->get();
                $objectives = LearningObjective::where('subject_id', $selectedClass->subject_id)->where('is_active', true)->get();
            }
        }

        return view('lms.assessment.remediation', compact(
            'plans',
            'myClasses',
            'availableStudents',
            'objectives',
            'isKurikulum',
            'isTeacher',
            'classFilter'
        ));
    }

    /**
     * Simpan Rencana Program Remedial / Pengayaan Baru
     */
    public function storeRemediationPlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'type' => 'required|in:REMEDIAL,PENGAYAAN',
            'topic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remedial_method' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $user = Auth::user();
        $courseClass = CourseClass::findOrFail($validated['course_class_id']);

        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum'])) {
            if (! $user->teacher || $courseClass->teacher_id !== $user->teacher->id) {
                abort(403, 'Anda tidak memiliki hak akses mengajar pada kelas ini.');
            }
        }

        $teacherId = $courseClass->teacher_id ?: $user->teacher?->id;

        $plan = RemediationEnrichmentPlan::create([
            'course_class_id' => $courseClass->id,
            'teacher_id' => $teacherId,
            'learning_objective_id' => $validated['learning_objective_id'] ?? null,
            'type' => $validated['type'],
            'topic' => $validated['topic'],
            'description' => $validated['description'] ?? 'Pelaksanaan pendampingan penguasaan kompetensi TP.',
            'remedial_method' => $validated['remedial_method'] ?? 'Bimbingan tutor sebaya & tes ulang butir soal setara.',
            'student_ids' => $validated['student_ids'],
            'scheduled_date' => $validated['scheduled_date'] ?: now()->toDateString(),
            'status' => 'PLANNED',
        ]);

        AuditLog::log('REMEDIATION_PLAN_CREATED', 'RemediationEnrichmentPlan', $plan->id, null, [
            'type' => $plan->type,
            'topic' => $plan->topic,
        ]);

        return redirect()->route('lms.assessment.remediation', ['course_class_id' => $courseClass->id])
            ->with('success', "Program {$plan->type} berhasil direncanakan untuk ".count($validated['student_ids']).' siswa.');
    }

    /**
     * Tandai Program Remedial Selesai Dilaksanakan
     */
    public function completeRemediationPlan(RemediationEnrichmentPlan $plan): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum'])) {
            if (! $user->teacher || $plan->teacher_id !== $user->teacher->id) {
                abort(403, 'Anda tidak berhak mengubah status rencana remedial ini.');
            }
        }

        $plan->update([
            'status' => 'COMPLETED',
        ]);

        return back()->with('success', "Program {$plan->type} telah ditandai tuntas dilaksanakan.");
    }
}

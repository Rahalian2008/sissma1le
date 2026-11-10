<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LearningObjectiveFlow;
use App\Models\LearningOutcome;
use App\Models\LessonPlan;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Services\Ai\AiCurriculumManager;
use App\Services\CurriculumPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CurriculumPlanController extends Controller
{
    public function __construct(
        protected CurriculumPlanService $curriculumService,
        protected AiCurriculumManager $aiManager
    ) {}

    /**
     * Halaman Utama Pusat Perencanaan Pembelajaran SMAN 1 Lengkong
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user->isTeacher();
        $isKurikulum = $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum']);
        $teacher = $user->teacher;

        $activeTab = $request->query('tab', 'lesson_plans'); // outcomes, objectives, flows, lesson_plans, prota_promes
        $subjectFilter = $request->query('subject_id');
        $phaseFilter = $request->query('phase');
        $semesterFilter = $request->query('semester');

        // Daftar Mata Pelajaran untuk Filter & Pilihan Form
        $subjectsQuery = Subject::where('is_active', true)->orderBy('name');
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $mySubjectIds = CourseClass::where('teacher_id', $teacher->id)->pluck('subject_id')->unique();
            if ($mySubjectIds->isNotEmpty()) {
                $subjectsQuery->whereIn('id', $mySubjectIds);
            }
        }
        $subjects = $subjectsQuery->get();

        // 1. Capaian Pembelajaran (CP)
        $outcomesQuery = LearningOutcome::with(['subject', 'creator'])->where('is_active', true);
        if ($subjectFilter) {
            $outcomesQuery->where('subject_id', $subjectFilter);
        }
        if ($phaseFilter) {
            $outcomesQuery->where('phase', $phaseFilter);
        }
        $outcomes = $outcomesQuery->latest()->get();

        // 2. Tujuan Pembelajaran (TP)
        $objectivesQuery = LearningObjective::with(['outcome', 'subject', 'teacher'])->where('is_active', true);
        if ($subjectFilter) {
            $objectivesQuery->where('subject_id', $subjectFilter);
        }
        if ($semesterFilter) {
            $objectivesQuery->where('target_semester', $semesterFilter);
        }
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $objectivesQuery->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)->orWhereNull('teacher_id');
            });
        }
        $objectives = $objectivesQuery->orderBy('tp_code')->get();

        // 3. Alur Tujuan Pembelajaran (ATP)
        $flowsQuery = LearningObjectiveFlow::with(['subject', 'teacher']);
        if ($subjectFilter) {
            $flowsQuery->where('subject_id', $subjectFilter);
        }
        $flows = $flowsQuery->latest()->get();

        // 4. Modul Ajar / RPP Digital Guru
        $classesQuery = CourseClass::with(['subject', 'schoolClass', 'teacher'])->where('is_active', true);
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $classesQuery->where('teacher_id', $teacher->id);
        }
        $myClasses = $classesQuery->get();
        $classIds = $myClasses->pluck('id');

        $plansQuery = LessonPlan::with(['courseClass.subject', 'courseClass.schoolClass', 'teacher', 'reviewer', 'objectives']);
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $plansQuery->where('teacher_id', $teacher->id);
        } elseif ($subjectFilter) {
            $plansQuery->whereHas('courseClass', fn ($q) => $q->where('subject_id', $subjectFilter));
        }
        $lessonPlans = $plansQuery->orderBy('meeting_number')->latest()->paginate(15);

        // Statistik Status Modul Ajar untuk Tabs & Monitoring
        $statsPlansQuery = LessonPlan::query();
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $statsPlansQuery->where('teacher_id', $teacher->id);
        }
        $planStats = [
            'total' => (clone $statsPlansQuery)->count(),
            'draft' => (clone $statsPlansQuery)->where('status', 'DRAFT')->count(),
            'submitted' => (clone $statsPlansQuery)->where('status', 'SUBMITTED')->count(),
            'revised' => (clone $statsPlansQuery)->where('status', 'REVISED')->count(),
            'approved' => (clone $statsPlansQuery)->where('status', 'APPROVED')->count(),
        ];

        return view('lms.planning.index', compact(
            'activeTab',
            'subjects',
            'outcomes',
            'objectives',
            'flows',
            'myClasses',
            'lessonPlans',
            'planStats',
            'isKurikulum',
            'isTeacher'
        ));
    }

    /**
     * Simpan Rumusan Capaian Pembelajaran (CP) Baru
     */
    public function storeOutcome(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'guru', 'kurikulum'])) {
            abort(403, 'Hanya pendidik dan kurikulum yang dapat merumuskan Capaian Pembelajaran.');
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'phase' => 'required|in:E,F',
            'grade_level' => 'required|in:X,XI,XII',
            'code' => 'nullable|string|max:50',
            'element' => 'nullable|string|max:100',
            'description' => 'required|string',
        ]);

        $outcome = LearningOutcome::create(array_merge($validated, [
            'created_by' => Auth::id(),
            'is_active' => true,
        ]));

        AuditLog::log('CURRICULUM_CP_CREATED', 'LearningOutcome', $outcome->id, null, [
            'code' => $outcome->code,
            'subject_id' => $outcome->subject_id,
        ]);

        return redirect()->route('lms.planning.index', ['tab' => 'outcomes'])
            ->with('success', 'Capaian Pembelajaran (CP) berhasil disimpan.');
    }

    /**
     * Simpan Rumusan Tujuan Pembelajaran (TP) Baru
     */
    public function storeObjective(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'guru', 'kurikulum'])) {
            abort(403, 'Hanya pendidik dan kurikulum yang dapat merumuskan Tujuan Pembelajaran.');
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'learning_outcome_id' => 'nullable|exists:learning_outcomes,id',
            'tp_code' => 'required|string|max:30',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'competency' => 'nullable|string',
            'scope_material' => 'nullable|string',
            'hours_allocation' => 'required|integer|min:1|max:20',
            'target_semester' => 'required|in:GANJIL,GENAP',
        ]);

        $teacher = Auth::user()->teacher;

        $objective = LearningObjective::create(array_merge($validated, [
            'teacher_id' => $teacher?->id,
            'is_active' => true,
        ]));

        AuditLog::log('CURRICULUM_TP_CREATED', 'LearningObjective', $objective->id, null, [
            'tp_code' => $objective->tp_code,
            'title' => $objective->title,
        ]);

        return redirect()->route('lms.planning.index', ['tab' => 'objectives'])
            ->with('success', "Tujuan Pembelajaran '{$objective->tp_code}' berhasil ditambahkan.");
    }

    /**
     * Generator Bantuan AI untuk Draf Tujuan Pembelajaran (TP)
     */
    public function aiGenerateObjective(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_name' => 'required|string',
            'topic' => 'required|string|max:200',
            'grade' => 'required|string|in:X,XI,XII',
            'outcome_text' => 'nullable|string',
        ]);

        $outcomeText = $validated['outcome_text'] ?? null;
        $prompt = "Sebagai pakar Kurikulum Merdeka SMA di Indonesia, buatkan 3 rumusan Tujuan Pembelajaran (TP) operasional untuk mata pelajaran {$validated['subject_name']} Kelas {$validated['grade']} dengan topik: '{$validated['topic']}'.\n"
            .($outcomeText ? "Mengacu pada Capaian Pembelajaran: {$outcomeText}\n" : '')
            .'Sertakan kode TP bertingkat (contoh: 10.1, 10.2), kompetensi inti (C1-C4), serta materi lingkupnya.';

        $result = $this->aiManager->generateDraft($prompt, [
            'task' => 'tp',
            'topic' => $validated['topic'],
            'grade' => $validated['grade'],
            'subject' => $validated['subject_name'],
        ]);

        return response()->json(array_merge($result, [
            'status' => ($result['success'] ?? false) ? 'ok' : 'error',
        ]));
    }

    /**
     * Simpan Draf Modul Ajar / RPP Digital Guru
     */
    public function storeLessonPlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
            'title' => 'required|string|max:255',
            'meeting_number' => 'required|integer|min:1',
            'time_allocation_minutes' => 'required|integer|min:30|max:240',
            'grade_level' => 'required|in:X,XI,XII',
            'semester' => 'required|in:GANJIL,GENAP',
            'target_profile' => 'nullable|array',
            'learning_model' => 'nullable|string|max:100',
            'learning_methods' => 'nullable|array',
            'media_and_resources' => 'nullable|string',
            'step_introduction' => 'required|string',
            'step_core' => 'required|string',
            'step_closing' => 'required|string',
            'assessment_diagnostic' => 'nullable|string',
            'assessment_formative' => 'nullable|string',
            'assessment_summative' => 'nullable|string',
            'lkpd_content' => 'nullable|string',
            'objective_ids' => 'nullable|array',
            'objective_ids.*' => 'exists:learning_objectives,id',
        ]);

        $user = Auth::user();
        $courseClass = CourseClass::findOrFail($validated['course_class_id']);

        // Verifikasi kepemilikan kelas guru
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum'])) {
            if (! $user->teacher || $courseClass->teacher_id !== $user->teacher->id) {
                abort(403, 'Anda tidak memiliki hak akses mengajar pada kelas ini.');
            }
        }

        $steps = [
            'introduction' => $validated['step_introduction'],
            'core' => $validated['step_core'],
            'closing' => $validated['step_closing'],
        ];

        $assessments = [
            'diagnostic' => $validated['assessment_diagnostic'] ?? null,
            'formative' => $validated['assessment_formative'] ?? null,
            'summative' => $validated['assessment_summative'] ?? null,
        ];

        $plan = LessonPlan::create([
            'course_class_id' => $courseClass->id,
            'teacher_id' => $courseClass->teacher_id,
            'title' => $validated['title'],
            'meeting_number' => $validated['meeting_number'],
            'time_allocation_minutes' => $validated['time_allocation_minutes'],
            'grade_level' => $validated['grade_level'],
            'semester' => $validated['semester'],
            'target_profile' => $validated['target_profile'] ?? [],
            'learning_model' => $validated['learning_model'] ?? 'Problem-Based Learning (PBL)',
            'learning_methods' => $validated['learning_methods'] ?? ['Diskusi', 'Praktik'],
            'media_and_resources' => $validated['media_and_resources'] ?? null,
            'learning_steps' => $steps,
            'assessment_plan' => $assessments,
            'lkpd_content' => $validated['lkpd_content'] ?? null,
            'status' => 'DRAFT',
            'is_published' => false,
            'version' => 1,
        ]);

        if (! empty($validated['objective_ids'])) {
            $plan->objectives()->sync($validated['objective_ids']);
        }

        AuditLog::log('LESSON_PLAN_CREATED', 'LessonPlan', $plan->id, null, [
            'title' => $plan->title,
            'meeting' => $plan->meeting_number,
        ]);

        return redirect()->route('lms.planning.index', ['tab' => 'lesson_plans'])
            ->with('success', "Draf Modul Ajar '{$plan->title}' pertemuan ke-{$plan->meeting_number} berhasil disimpan.");
    }

    /**
     * Generator Bantuan AI untuk Draf Modul Ajar / RPP Digital
     */
    public function aiGenerateLessonPlan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_name' => 'required|string',
            'topic' => 'required|string|max:200',
            'grade' => 'required|string|in:X,XI,XII',
            'duration' => 'nullable|integer',
        ]);

        $duration = $validated['duration'] ?? 90;

        $prompt = "Sebagai perancang kurikulum SMA berpengalaman, susunlah draf Modul Ajar Kurikulum Merdeka untuk mata pelajaran {$validated['subject_name']} Kelas {$validated['grade']} dengan topik: '{$validated['topic']}'.\n"
            ."Durasi pembelajaran: {$duration} menit. Susunlah:\n"
            ."1. Profil Pelajar Pancasila yang disasar\n"
            ."2. Model Pembelajaran yang relevan (misal: Problem Based Learning)\n"
            ."3. Rincian Langkah Pembelajaran Terstruktur (Apersepsi 15 menit, Kegiatan Inti 60 menit, Penutup 15 menit)\n"
            ."4. Lembar Kerja Peserta Didik (LKPD) ringkas pemantik diskusi\n"
            .'5. Rencana Asesmen Formatif.';

        $result = $this->aiManager->generateDraft($prompt, [
            'task' => 'modul_ajar',
            'topic' => $validated['topic'],
            'grade' => $validated['grade'],
            'subject' => $validated['subject_name'],
        ]);

        return response()->json(array_merge($result, [
            'status' => ($result['success'] ?? false) ? 'ok' : 'error',
        ]));
    }

    /**
     * Guru Mengajukan Modul Ajar untuk Ditinjau oleh Wakasek Kurikulum
     */
    public function submitLessonPlan(LessonPlan $plan): RedirectResponse
    {
        Gate::authorize('update', $plan);

        $plan->update([
            'status' => 'SUBMITTED',
        ]);

        AuditLog::log('LESSON_PLAN_SUBMITTED', 'LessonPlan', $plan->id, null, [
            'title' => $plan->title,
        ]);

        return redirect()->route('lms.planning.index', ['tab' => 'lesson_plans'])
            ->with('success', "Modul Ajar '{$plan->title}' telah diajukan untuk ditinjau oleh Wakasek Kurikulum.");
    }

    /**
     * Peninjauan dan Persetujuan Modul Ajar oleh Wakasek Kurikulum / Admin
     */
    public function reviewLessonPlan(Request $request, LessonPlan $plan): RedirectResponse
    {
        Gate::authorize('review', $plan);

        $validated = $request->validate([
            'action' => 'required|in:APPROVE,REVISE',
            'review_notes' => 'nullable|string',
        ]);

        if ($validated['action'] === 'APPROVE') {
            $plan->update([
                'status' => 'APPROVED',
                'is_published' => true,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes'] ?: 'Modul ajar telah diverifikasi dan disetujui untuk KBM.',
            ]);

            $message = "Modul Ajar '{$plan->title}' berhasil disetujui & dipublikasikan untuk KBM.";
        } else {
            $plan->update([
                'status' => 'REVISED',
                'is_published' => false,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes'] ?: 'Mohon periksa dan lengkapi rincian asesmen atau langkah KBM.',
            ]);

            $message = 'Catatan revisi telah dikirimkan kepada guru penyusun.';
        }

        AuditLog::log('LESSON_PLAN_REVIEWED', 'LessonPlan', $plan->id, null, [
            'status' => $plan->status,
            'action' => $validated['action'],
        ]);

        return redirect()->route('lms.planning.index', ['tab' => 'lesson_plans'])
            ->with('success', $message);
    }

    /**
     * Tampilan Cetak Resmi Dokumen Modul Ajar SMAN 1 Lengkong
     */
    public function printLessonPlan(LessonPlan $plan): View
    {
        Gate::authorize('view', $plan);

        $plan->load(['courseClass.subject', 'courseClass.schoolClass', 'teacher.user', 'reviewer', 'objectives']);

        $settings = SchoolSetting::whereIn('key', [
            'school_name',
            'school_kop',
            'school_address',
            'school_principal',
            'principal_nip',
            'wakasek_kurikulum_name',
            'wakasek_kurikulum_nip',
        ])->pluck('value', 'key');

        return view('lms.planning.print_lesson_plan', compact('plan', 'settings'));
    }
}

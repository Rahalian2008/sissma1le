<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\LessonPlan;
use App\Models\TeacherReflection;
use App\Services\Ai\AiCurriculumManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReflectionController extends Controller
{
    /**
     * Dashboard Refleksi KBM Guru Pasca Pembelajaran.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $classesQuery = CourseClass::with(['subject', 'schoolClass'])
            ->where('is_active', true);

        if (! $isLeader && $teacher) {
            $classesQuery->where('teacher_id', $teacher->id);
        }

        $classes = $classesQuery->orderBy('id', 'desc')->get();

        $selectedClassId = $request->query('course_class_id');
        $selectedStatus = $request->query('status');

        $reflectionsQuery = TeacherReflection::with(['courseClass.subject', 'courseClass.schoolClass', 'teacher', 'lessonPlan'])
            ->latest('reflection_date');

        if (! $isLeader && $teacher) {
            $reflectionsQuery->where('teacher_id', $teacher->id);
        }

        if ($selectedClassId) {
            $reflectionsQuery->where('course_class_id', $selectedClassId);
        }

        if ($selectedStatus) {
            $reflectionsQuery->where('status', $selectedStatus);
        }

        $reflections = $reflectionsQuery->paginate(15)->withQueryString();

        // Metrik statistik refleksi
        $stats = [
            'total' => (clone $reflectionsQuery)->count(),
            'completed' => (clone $reflectionsQuery)->where('status', 'SELESAI')->count(),
            'this_month' => (clone $reflectionsQuery)->whereMonth('reflection_date', now()->month)->count(),
            'classes_covered' => (clone $reflectionsQuery)->distinct('course_class_id')->count('course_class_id'),
        ];

        // Modul ajar yang relevan untuk dikaitkan jika kelas dipilih
        $lessonPlans = collect();
        if ($selectedClassId) {
            $lessonPlans = LessonPlan::where('course_class_id', $selectedClassId)->get();
        } elseif ($classes->isNotEmpty()) {
            $lessonPlans = LessonPlan::where('course_class_id', $classes->first()->id)->get();
        }

        return view('lms.reflections.index', compact(
            'reflections',
            'classes',
            'selectedClassId',
            'selectedStatus',
            'stats',
            'lessonPlans',
            'isLeader'
        ));
    }

    /**
     * Simpan catatan evaluasi & refleksi guru pasca KBM.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
            'lesson_plan_id' => 'nullable|exists:lesson_plans,id',
            'reflection_date' => 'required|date',
            'success_notes' => 'required|string|max:3000',
            'challenges_notes' => 'required|string|max:3000',
            'follow_up_action' => 'required|string|max:3000',
            'status' => 'nullable|in:DRAFT,SELESAI',
        ]);

        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);
        $courseClass = CourseClass::findOrFail($validated['course_class_id']);

        $teacherId = $courseClass->teacher_id;
        if (! $isLeader) {
            if (! $user->teacher || $user->teacher->id !== $teacherId) {
                abort(403, 'Anda tidak berhak mengisi refleksi pada kelas pengampu lain.');
            }
        } elseif ($user->teacher) {
            $teacherId = $user->teacher->id;
        }

        TeacherReflection::create([
            'course_class_id' => $courseClass->id,
            'teacher_id' => $teacherId,
            'lesson_plan_id' => $validated['lesson_plan_id'] ?? null,
            'reflection_date' => $validated['reflection_date'],
            'success_notes' => $validated['success_notes'],
            'challenges_notes' => $validated['challenges_notes'],
            'follow_up_action' => $validated['follow_up_action'],
            'status' => $validated['status'] ?? 'SELESAI',
        ]);

        return redirect()->back()->with('success', 'Catatan refleksi pembelajaran berhasil disimpan.');
    }

    /**
     * Hapus catatan refleksi pembelajaran.
     */
    public function destroy(TeacherReflection $reflection): RedirectResponse
    {
        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);

        if (! $isLeader && (! $user->teacher || $user->teacher->id !== $reflection->teacher_id)) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus refleksi ini.');
        }

        $reflection->delete();

        return redirect()->back()->with('success', 'Catatan refleksi berhasil dihapus.');
    }

    /**
     * Bantuan Asisten AI untuk perumusan draf kalimat refleksi & rencana tindak lanjut KBM.
     */
    public function aiGenerateReflection(Request $request, AiCurriculumManager $aiManager): JsonResponse
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'challenges' => 'nullable|string|max:500',
            'grade' => 'nullable|string|max:10',
        ]);

        $prompt = "Buatkan draf catatan refleksi pembelajaran guru pasca KBM untuk topik '{$validated['topic']}' "
            .'pada tingkat kelas '.($validated['grade'] ?? 'X').'. '
            .'Sertakan: 1) Aspek yang berhasil/berjalan efektif, 2) Kendala siswa (Catatan kendala: '.($validated['challenges'] ?? 'pemahaman analitis dan variasi fokus').'), dan 3) Rencana tindak lanjut perbaikan.';

        $result = $aiManager->generateDraft($prompt, [
            'task' => 'refleksi',
            'topic' => $validated['topic'],
            'grade' => $validated['grade'] ?? 'X',
        ]);

        return response()->json($result);
    }

    /**
     * Dokumen cetak rekapitulasi refleksi pembelajaran guru (A4 Landscape).
     */
    public function print(Request $request): View
    {
        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $classId = $request->query('course_class_id');
        $courseClass = $classId ? CourseClass::with(['subject', 'schoolClass', 'teacher'])->find($classId) : null;

        $query = TeacherReflection::with(['courseClass.subject', 'courseClass.schoolClass', 'teacher', 'lessonPlan'])
            ->orderBy('reflection_date', 'asc');

        if ($courseClass) {
            if (! $isLeader && $teacher && $courseClass->teacher_id !== $teacher->id) {
                abort(403, 'Anda tidak berhak mencetak refleksi kelas ini.');
            }
            $query->where('course_class_id', $courseClass->id);
        } elseif (! $isLeader && $teacher) {
            $query->where('teacher_id', $teacher->id);
        }

        $reflections = $query->get();

        return view('lms.reflections.print', compact(
            'reflections',
            'courseClass',
            'teacher'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AiGenerationLog;
use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\Subject;
use App\Services\Ai\AiCurriculumManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AiCurriculumCenterController extends Controller
{
    public function __construct(
        protected AiCurriculumManager $aiManager
    ) {}

    /**
     * Dashboard Pusat Layanan AI Kurikulum & Pedagogi SMA1LE.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user->isTeacher() && ! $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])) {
            abort(403, 'Akses terbatas untuk pendidik dan tim kurikulum.');
        }

        $stats = $this->aiManager->getUsageStatistics();

        $selectedTask = $request->query('task_type');
        $logsQuery = AiGenerationLog::with('user')->latest();

        if ($selectedTask) {
            $logsQuery->where('task_type', $selectedTask);
        }

        $logs = $logsQuery->paginate(15)->withQueryString();

        $subjects = Subject::orderBy('name')->get();
        $courseClasses = CourseClass::with(['subject', 'schoolClass'])->where('is_active', true)->get();
        $recentObjectives = LearningObjective::latest()->take(20)->get();

        return view('lms.ai.index', compact(
            'stats',
            'logs',
            'selectedTask',
            'subjects',
            'courseClasses',
            'recentObjectives'
        ));
    }

    /**
     * Endpoint Generator Soal HOTS Berbasis Kearifan Lokal (Sukabumi / Jawa Barat).
     */
    public function generateHots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'grade' => 'nullable|string|max:10',
            'local_context' => 'nullable|string|max:255',
        ]);

        $result = $this->aiManager->generateHotsQuestionsWithLocalContext(
            $validated['topic'],
            $validated['grade'] ?? 'X',
            $validated['local_context'] ?: 'Sukabumi / Jawa Barat'
        );

        return response()->json($result);
    }

    /**
     * Endpoint Generator Modul Ajar Berdiferensiasi (Konten, Proses, Produk).
     */
    public function generateDifferentiated(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'grade' => 'nullable|string|max:10',
            'learning_model' => 'nullable|string|max:100',
        ]);

        $result = $this->aiManager->generateDifferentiatedLessonPlan(
            $validated['topic'],
            $validated['grade'] ?? 'X',
            $validated['learning_model'] ?: 'PBL'
        );

        return response()->json($result);
    }

    /**
     * Endpoint Analisis Diagnostik & Rekomendasi Remedial Berdasarkan TP.
     */
    public function analyzeDiagnostic(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tp_title' => 'required|string|max:255',
            'failed_aspects' => 'nullable|array',
            'failed_aspects.*' => 'string|max:255',
        ]);

        $result = $this->aiManager->analyzeDiagnosticRemediation(
            $validated['tp_title'],
            $validated['failed_aspects'] ?? []
        );

        return response()->json($result);
    }

    /**
     * Uji Latensi & Kesiapan Koneksi Provider AI.
     */
    public function testConnection(): JsonResponse
    {
        $startTime = microtime(true);
        $activeAdapter = $this->aiManager->getActiveAdapter();
        $isAvailable = $activeAdapter->isAvailable();
        $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'status' => 'ok',
            'provider' => $activeAdapter->getProviderName(),
            'is_available' => $isAvailable,
            'latency_ms' => $latencyMs,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

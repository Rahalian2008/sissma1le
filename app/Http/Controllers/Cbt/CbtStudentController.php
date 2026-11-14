<?php

namespace App\Http\Controllers\Cbt;

use App\Http\Controllers\Controller;
use App\Models\CbtAnswer;
use App\Models\CbtExam;
use App\Models\CbtSession;
use App\Models\CbtViolation;
use App\Models\Student;
use App\Services\CbtGeofenceService;
use App\Services\CbtGradingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CbtStudentController extends Controller
{
    /**
     * Portal Ujian Siswa (Daftar Ujian Tersedia & Riwayat).
     */
    public function index(): View
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (! $student) {
            abort(403, 'Profil siswa tidak ditemukan untuk akun ini.');
        }

        $classId = $student->class_id;

        // Ambil ujian yang ditugaskan ke kelas siswa
        $exams = CbtExam::with(['subject', 'teacher.user'])
            ->where('status', 'published')
            ->whereHas('classes', function ($q) use ($classId) {
                $q->where('school_classes.id', $classId);
            })
            ->latest('start_time')
            ->get();

        // Ambil sesi ujian siswa yang sudah ada
        $sessions = CbtSession::where('student_id', $student->id)
            ->whereIn('cbt_exam_id', $exams->pluck('id'))
            ->get()
            ->keyBy('cbt_exam_id');

        return view('cbt.student.index', compact('student', 'exams', 'sessions'));
    }

    /**
     * Mulai atau Lanjutkan Ujian (Validasi Token, Geofence & Inisialisasi Sesi).
     */
    public function start(Request $request, CbtExam $exam, CbtGeofenceService $geofence): RedirectResponse
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (! $student) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // 1. Validasi Akses Perorangan Siswa
        $existingSession = CbtSession::where('cbt_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingSession && $existingSession->is_allowed === false) {
            return back()->with('error', '❌ Akses Ujian Dinonaktifkan: Akses ujian Anda saat ini sedang dinonaktifkan oleh Guru/Pengawas ruang. Silakan hubungi pengawas ruang untuk mengaktifkannya.');
        }

        // 2. Validasi Token Ujian jika ujian MEWAJIBKAN token (Bypass jika require_token = false)
        if ($exam->require_token && ! empty($exam->token)) {
            $inputToken = strtoupper(trim($request->input('token', '')));
            if ($inputToken !== strtoupper($exam->token)) {
                return back()->with('error', 'Token ujian yang Anda masukkan salah. Silakan tanyakan token kepada pengawas ujian.');
            }
        }

        // 3. Validasi Jadwal Ujian
        if ($exam->start_time && now()->lt($exam->start_time)) {
            return back()->with('error', "Ujian belum dimulai. Ujian dibuka pada: {$exam->start_time->translatedFormat('d F Y H:i')}.");
        }

        if ($exam->end_time && now()->gt($exam->end_time)) {
            return back()->with('error', 'Waktu ujian telah berakhir.');
        }

        // 3. Validasi Geofence (Penguncian Lokasi Sekolah)
        if ($exam->enable_geofence) {
            $lat = $request->filled('latitude') ? (float) $request->latitude : null;
            $lng = $request->filled('longitude') ? (float) $request->longitude : null;

            $geoResult = $geofence->verifyLocation($exam, $lat, $lng);
            if (! $geoResult['is_allowed']) {
                $dist = $geoResult['distance_meters'];
                $rad = $geoResult['allowed_radius'];

                return back()->with('error', "❌ Lokasi Tidak Valid: Anda terdeteksi berada di luar area sekolah ({$dist} meter dari pusat sekolah, batas maksimal {$rad} meter). Ujian ini mewajibkan Anda berada di lingkungan sekolah.");
            }
        }

        // 4. Buat atau Lanjutkan Sesi Ujian
        $session = CbtSession::firstOrCreate(
            [
                'cbt_exam_id' => $exam->id,
                'student_id' => $student->id,
            ],
            [
                'session_token' => Str::random(40),
                'status' => 'in_progress',
                'started_at' => now(),
                'remaining_seconds' => $exam->duration_minutes * 60,
                'student_latitude' => $request->latitude,
                'student_longitude' => $request->longitude,
                'student_distance_meters' => $geoResult['distance_meters'] ?? null,
                'student_ip' => $request->ip(),
                'student_user_agent' => $request->userAgent(),
            ]
        );

        // Jika sudah selesai sebelumnya
        if ($session->status === 'submitted') {
            return redirect()->route('cbt.student.result', $exam->id)
                ->with('info', 'Anda telah menyelesaikan ujian ini.');
        }

        // Jika terblokir
        if ($session->is_blocked) {
            return redirect()->route('cbt.student.take', $exam->id);
        }

        // Jika baru dimulai
        if ($session->status === 'not_started') {
            $session->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'remaining_seconds' => $exam->duration_minutes * 60,
                'student_latitude' => $request->latitude,
                'student_longitude' => $request->longitude,
                'student_ip' => $request->ip(),
                'student_user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('cbt.student.take', $exam->id);
    }

    /**
     * Lembar Mengerjakan Ujian CBT (Test Player Interaktif).
     */
    public function take(CbtExam $exam): View|RedirectResponse
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (! $student) {
            return redirect()->route('cbt.student.index');
        }

        $session = CbtSession::where('cbt_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $session) {
            return redirect()->route('cbt.student.index')
                ->with('error', 'Sesi ujian belum dimulai. Silakan klik Masuk Ujian untuk memulai.');
        }

        if ($session->is_allowed === false) {
            return redirect()->route('cbt.student.index')
                ->with('error', '❌ Akses Ujian Dinonaktifkan: Akses ujian Anda sedang dinonaktifkan oleh Guru/Pengawas.');
        }

        if ($session->status === 'submitted') {
            return redirect()->route('cbt.student.result', $exam->id);
        }

        // Ambil soal ujian
        $questionsQuery = $exam->questions();
        if ($exam->randomize_questions) {
            // Gunakan seed berbasis student_id agar urutan acak konsisten saat refresh
            $questions = $questionsQuery->inRandomOrder($student->id)->get();
        } else {
            $questions = $questionsQuery->orderBy('question_number')->get();
        }

        // Ambil jawaban yang sudah tersimpan
        $answers = $session->answers()->get()->keyBy('cbt_exam_question_id');

        return view('cbt.student.take', compact('exam', 'session', 'student', 'questions', 'answers'));
    }

    /**
     * Simpan Jawaban Siswa per Soal (Auto-Save Endpoint).
     */
    public function saveAnswer(Request $request, CbtExam $exam, CbtGradingService $grading): JsonResponse
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $session = CbtSession::where('cbt_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $session || $session->status === 'submitted') {
            return response()->json(['success' => false, 'message' => 'Sesi ujian telah berakhir'], 400);
        }

        if ($session->is_blocked) {
            return response()->json(['success' => false, 'message' => 'Ujian terblokir', 'is_blocked' => true], 403);
        }

        $questionId = (int) $request->input('question_id');
        $answerData = $request->input('answer');
        $isDoubtful = $request->boolean('is_doubtful');
        $remainingSeconds = $request->filled('remaining_seconds') ? (int) $request->remaining_seconds : null;

        // Update sisa waktu jika dikirimkan
        if ($remainingSeconds !== null && $remainingSeconds >= 0) {
            $session->update(['remaining_seconds' => $remainingSeconds]);
        }

        // Simpan atau update jawaban
        $answer = CbtAnswer::updateOrCreate(
            [
                'cbt_session_id' => $session->id,
                'cbt_exam_question_id' => $questionId,
            ],
            [
                'answer' => $answerData,
                'is_doubtful' => $isDoubtful,
                'answered_at' => now(),
            ]
        );

        // Langsung lakukan penilaian objektif di background
        $grading->gradeAnswer($answer);

        $answeredCount = $session->answers()->whereNotNull('answer')->count();

        return response()->json([
            'success' => true,
            'answered_count' => $answeredCount,
            'is_doubtful' => $isDoubtful,
        ]);
    }

    /**
     * Catat Pelanggaran Keamanan Siswa (Tab Switch, Minimize, Fullscreen Exit).
     */
    public function reportViolation(Request $request, CbtExam $exam): JsonResponse
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (! $student) {
            return response()->json(['success' => false], 403);
        }

        $session = CbtSession::where('cbt_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $session || $session->status === 'submitted') {
            return response()->json(['success' => false], 400);
        }

        $violationType = $request->input('type', 'tab_switch');
        $description = $request->input('description', 'Membuka tab baru atau aplikasi lain saat ujian');

        // Catat di log pelanggaran
        CbtViolation::create([
            'cbt_session_id' => $session->id,
            'violation_type' => $violationType,
            'description' => $description,
            'violation_time' => now(),
        ]);

        $newCount = $session->violation_count + 1;
        $maxViolations = $exam->max_violations ?? 3;
        $shouldBlock = $exam->enable_security_block && ($newCount >= $maxViolations);

        $session->update([
            'violation_count' => $newCount,
            'is_blocked' => $shouldBlock ? true : $session->is_blocked,
            'status' => $shouldBlock ? 'blocked' : $session->status,
            'blocked_reason' => $shouldBlock ? "Terdeteksi melakukan kecurangan ({$newCount}x pelanggaran: {$description})" : null,
            'blocked_at' => $shouldBlock ? now() : $session->blocked_at,
        ]);

        return response()->json([
            'success' => true,
            'violation_count' => $newCount,
            'max_violations' => $maxViolations,
            'is_blocked' => $shouldBlock,
            'message' => $shouldBlock
                ? '🚫 Ujian Anda telah terblokir otomatis karena terdeteksi beralih tab atau membuka aplikasi lain. Silakan hubungi pengawas ujian untuk membuka blokir.'
                : "⚠️ Peringatan Keamanan ({$newCount}/{$maxViolations}): Dilarang membuka tab baru, beralih jendela, atau meminimalkan browser saat ujian!",
        ]);
    }

    /**
     * Selesaikan Ujian Siswa (Kirim Jawaban Akhir).
     */
    public function finish(Request $request, CbtExam $exam, CbtGradingService $grading): RedirectResponse
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (! $student) {
            return redirect()->route('cbt.student.index');
        }

        $session = CbtSession::where('cbt_exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $session) {
            return redirect()->route('cbt.student.index');
        }

        $session->update([
            'status' => 'submitted',
            'finished_at' => now(),
            'remaining_seconds' => 0,
            'is_blocked' => false,
        ]);

        // Rekap nilai akhir
        $grading->recalculateSession($session);

        return redirect()->route('cbt.student.result', $exam->id)
            ->with('success', 'Ujian Anda telah berhasil diselesaikan dan terkirim ke server.');
    }

    /**
     * Tampilan Hasil / Konfirmasi Selesai Ujian.
     */
    public function result(CbtExam $exam): View
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $session = CbtSession::with(['answers.question'])
            ->where('cbt_exam_id', $exam->id)
            ->where('student_id', $student?->id)
            ->firstOrFail();

        return view('cbt.student.result', compact('exam', 'session', 'student'));
    }
}

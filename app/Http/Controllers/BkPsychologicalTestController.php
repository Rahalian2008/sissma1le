<?php

namespace App\Http\Controllers;

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestQuestion;
use App\Models\BkPsychologicalTestResult;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\BkPsychologicalAiAnalysisService;
use App\Services\BkPsychologicalTestService;
use Database\Seeders\BkPsychologicalTestSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkPsychologicalTestController extends Controller
{
    public function __construct(
        protected BkPsychologicalTestService $testService
    ) {}

    /**
     * Memastikan instrumen dan butir soal psikotes standar selalu tersedia
     */
    public static function ensureDefaultTests(): void
    {
        if (BkPsychologicalTest::count() === 0 || BkPsychologicalTestQuestion::count() === 0) {
            try {
                (new BkPsychologicalTestSeeder)->run();
            } catch (\Throwable $e) {
                // Diamkan jika terjadi error seeder agar tidak crash
            }
        }
    }

    public function index(Request $request): View
    {
        self::ensureDefaultTests();
        $user = Auth::user();
        $tests = BkPsychologicalTest::withCount('questions')->where('is_active', true)->get();

        // Jika login sebagai siswa, tampilkan riwayat tes siswa tersebut
        $student = null;
        if ($user->isStudent() || $user->student) {
            $student = $user->student;
        }

        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        $homeroomClass = $isHomeroomOnly ? ($user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first()) : null;

        $query = BkPsychologicalTestResult::with(['student.schoolClass', 'test'])
            ->latest('taken_at');

        if ($student) {
            $query->where('student_id', $student->id);
        } elseif ($isHomeroomOnly && $homeroomClass) {
            $query->whereHas('student', fn ($sq) => $sq->where('class_id', $homeroomClass->id));
        } elseif ($request->filled('class_id')) {
            $query->whereHas('student', fn ($sq) => $sq->where('class_id', $request->class_id));
        }

        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
        }

        $results = $query->paginate(15)->withQueryString();
        $classes = ($isHomeroomOnly && $homeroomClass) ? SchoolClass::where('id', $homeroomClass->id)->get() : SchoolClass::orderBy('name')->get();

        $testResultsByTestId = [];
        if ($student) {
            $testResultsByTestId = BkPsychologicalTestResult::where('student_id', $student->id)
                ->get()
                ->keyBy('test_id');
        }

        return view('bk.psychological.index', compact('tests', 'results', 'student', 'classes', 'testResultsByTestId'));
    }

    /**
     * Siswa mengambil/mengerjakan tes psikotes (atau simulasi/pendampingan oleh Guru BK/Admin).
     */
    public function takeTest(Request $request, string $slug): View|RedirectResponse
    {
        self::ensureDefaultTests();
        $user = Auth::user();
        $student = $user->student;

        $isAdminOrCounselor = $user->hasRole('admin')
            || $user->hasRole('super_admin')
            || (method_exists($user, 'isCounselor') && $user->isCounselor())
            || (method_exists($user, 'canManageBk') && $user->canManageBk());

        if (! $student && $isAdminOrCounselor) {
            if ($request->filled('student_id')) {
                $student = Student::with('schoolClass')->find($request->student_id);
            }
            if (! $student) {
                $student = Student::with('schoolClass')->where('is_active', true)->first();
            }
        }

        if (! $student) {
            return redirect()->route('bk.psychological.index')
                ->with('error', 'Peserta didik belum dipilih atau belum ada data siswa aktif.');
        }

        $test = BkPsychologicalTest::with('questions')->where('slug', $slug)->firstOrFail();

        // Batasi siswa hanya dapat mengisi 1 kali
        if (($user->isStudent() || $user->student) && ! $isAdminOrCounselor) {
            $existingResult = BkPsychologicalTestResult::where('test_id', $test->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingResult) {
                return redirect()->route('bk.psychological.result', $existingResult)
                    ->with('warning', "Anda sudah menyelesaikan asesmen [{$test->name}] ini sebelumnya (hanya dapat diisi 1 kali). Untuk melakukan pengisian ulang, silakan hubungi Guru BK atau Administrator untuk melakukan reset pengerjaan.");
            }
        }

        $availableStudents = $isAdminOrCounselor ? Student::where('is_active', true)->orderBy('name')->take(50)->get() : collect();

        return view('bk.psychological.take', compact('test', 'student', 'isAdminOrCounselor', 'availableStudents'));
    }

    /**
     * Submit jawaban tes psikotes siswa dan evaluasi otomatis.
     */
    public function submitTest(Request $request, string $slug): RedirectResponse
    {
        self::ensureDefaultTests();
        $user = Auth::user();
        $student = $user->student;

        $isAdminOrCounselor = $user->hasRole('admin')
            || $user->hasRole('super_admin')
            || (method_exists($user, 'isCounselor') && $user->isCounselor())
            || (method_exists($user, 'canManageBk') && $user->canManageBk());

        if (! $student && $isAdminOrCounselor) {
            if ($request->filled('student_id')) {
                $student = Student::find($request->student_id);
            }
            if (! $student) {
                $student = Student::where('is_active', true)->first();
            }
        }

        if (! $student) {
            return redirect()->back()->with('error', 'Siswa tidak ditemukan untuk akun ini.');
        }

        $test = BkPsychologicalTest::where('slug', $slug)->firstOrFail();

        // Validasi pembatasan 1 kali pengisian untuk siswa
        if (($user->isStudent() || $user->student) && ! $isAdminOrCounselor) {
            $existingResult = BkPsychologicalTestResult::where('test_id', $test->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingResult) {
                return redirect()->route('bk.psychological.result', $existingResult)
                    ->with('warning', "Anda sudah menyelesaikan tes [{$test->name}] ini sebelumnya (hanya dapat diisi 1 kali). Silakan hubungi Guru BK atau Administrator jika membutuhkan reset pengerjaan.");
            }
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'numeric', 'min:1', 'max:5'],
            'student_id' => ['nullable', 'exists:students,id'],
        ]);

        $result = $this->testService->evaluateTest($test, $student, $validated['answers']);

        return redirect()->route('bk.psychological.result', $result)
            ->with('success', "Asesmen [{$test->name}] untuk {$student->name} berhasil diselesaikan! Berikut profil hasil analisis psikologismu.");
    }

    /**
     * Tampilkan laporan hasil psikotes siswa lengkap dengan Analisis AI & Rencana Tindak Lanjut (RTL).
     */
    public function result(BkPsychologicalTestResult $result): View
    {
        $user = Auth::user();
        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        if ($isHomeroomOnly) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass && $result->student && $result->student->class_id !== $homeroomClass->id) {
                abort(403, 'Akses terbatas untuk hasil psikotes siswa kelas binaan Anda.');
            }
        }

        $result->load(['student.schoolClass', 'test']);

        // Jika belum ada analisis AI master dan action plan, generate otomatis
        if (empty($result->ai_report) || empty($result->action_plan) || empty($result->scientific_sources)) {
            $this->testService->generateAiAnalysisForResult($result);
            $result->refresh();
        }

        return view('bk.psychological.result', compact('result'));
    }

    /**
     * Laporan Grand Holistic 360 Asesmen Psikologis & Rekam Jejak Terpadu Siswa
     */
    public function holistic360(Student $student, BkPsychologicalAiAnalysisService $aiService): View
    {
        $user = Auth::user();
        if ($user && $user->isStudent() && $user->student && (int) $user->student->id !== (int) $student->id) {
            abort(403, 'Akses terbatas untuk profil diri ananda sendiri.');
        }

        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        if ($isHomeroomOnly) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                abort(403, 'Akses terbatas untuk peserta didik di kelas binaan Anda.');
            }
        }

        $synthesis = $aiService->analyzeHolistic360($student);
        $report = $synthesis['report'] ?? [];
        $provider = $synthesis['provider'] ?? 'AI Engine';
        $model = $synthesis['model'] ?? 'default';

        $school = SchoolIdentity::first();
        $headmaster = Teacher::getHeadmaster();
        $counselor = Teacher::getBkCoordinator();

        return view('bk.psychological.holistic', compact('student', 'report', 'provider', 'model', 'school', 'headmaster', 'counselor'));
    }

    /**
     * Generate ulang analisis AI psikometri & rencana tindak lanjut (RTL) BK terstandar.
     */
    public function generateAi(BkPsychologicalTestResult $result): RedirectResponse
    {
        $this->testService->generateAiAnalysisForResult($result);

        return redirect()->route('bk.psychological.result', $result)
            ->with('success', 'Analisis Psikometri & Rencana Tindak Lanjut (RTL) AI terstandar POP-BK berhasil digenerate!');
    }

    /**
     * Generate data simulasi/sampel hasil psikotes untuk pengujian Guru BK/Admin.
     */
    public function demoSeed(Request $request): RedirectResponse
    {
        self::ensureDefaultTests();
        $student = Student::where('is_active', true)->first();
        if (! $student) {
            return redirect()->back()->with('error', 'Tidak ditemukan data siswa aktif untuk simulasi asesmen.');
        }

        $slug = $request->input('slug', 'vak');
        $test = BkPsychologicalTest::with('questions')->where('slug', $slug)->first();
        if (! $test) {
            $test = BkPsychologicalTest::with('questions')->first();
        }

        if (! $test || $test->questions->isEmpty()) {
            return redirect()->back()->with('error', 'Instrumen tes atau butir soal belum tersedia.');
        }

        $simulatedAnswers = [];
        foreach ($test->questions as $index => $q) {
            $simulatedAnswers[$q->id] = ($index % 3 === 0) ? 4 : (($index % 2 === 0) ? 3 : 2);
        }

        $result = $this->testService->evaluateTest($test, $student, $simulatedAnswers);

        return redirect()->route('bk.psychological.result', $result)
            ->with('success', "Simulasi asesmen [{$test->name}] untuk {$student->name} berhasil dibuat lengkap dengan analisis AI & RTL!");
    }

    /**
     * Cetak Laporan Resmi Hasil Psikotes Siswa.
     */
    public function printResult(BkPsychologicalTestResult $result): View
    {
        $user = Auth::user();
        if ($user && $user->isStudent() && $user->student && (int) $user->student->id !== (int) $result->student_id) {
            abort(403, 'Anda tidak berhak mengakses atau mencetak hasil asesmen siswa lain.');
        }

        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        if ($isHomeroomOnly) {
            $homeroomClass = $user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first();
            if ($homeroomClass && $result->student && $result->student->class_id !== $homeroomClass->id) {
                abort(403, 'Akses terbatas untuk hasil psikotes siswa kelas binaan Anda.');
            }
        }

        $result->load(['student.schoolClass', 'test']);

        if (empty($result->action_plan) || empty($result->scientific_sources)) {
            $this->testService->generateAiAnalysisForResult($result);
            $result->refresh();
        }

        $school = SchoolIdentity::first();
        $headmaster = Teacher::getHeadmaster();
        $counselor = Teacher::getBkCoordinator();

        return view('bk.psychological.print', compact('result', 'school', 'headmaster', 'counselor'));
    }

    /**
     * Hapus / Reset data hasil asesmen siswa dari Bank Data
     */
    public function destroyResult(BkPsychologicalTestResult $result): RedirectResponse
    {
        $user = Auth::user();
        $canReset = $user && (
            $user->hasAnyRole(['super_admin', 'admin', 'koordinator_bk', 'guru_bk', 'bk'])
            || in_array($user->role, ['super_admin', 'admin', 'koordinator_bk', 'guru_bk', 'bk'], true)
            || (method_exists($user, 'isCounselor') && $user->isCounselor())
        );

        if (! $canReset) {
            abort(403, 'Hanya Super Admin, Admin, Koordinator BK, atau Guru BK yang berhak mereset hasil asesmen psikotes.');
        }

        $studentName = $result->student?->name ?? 'Siswa';
        $testName = $result->test?->name ?? 'Asesmen Psikotes';

        $result->delete();

        return redirect()->route('bk.psychological.index')
            ->with('success', "Data hasil asesmen {$testName} untuk {$studentName} berhasil di-reset. Siswa kini dapat mengerjakan kembali asesmen tersebut.");
    }

    /**
     * Rekapitulasi Gaya Belajar & Profil Kelas untuk Guru BK & Wali Kelas.
     */
    public function classReport(Request $request): View
    {
        $user = Auth::user();
        $isHomeroomOnly = $user && $user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_bk', 'guru_bk', 'bk', 'kepala_sekolah']);
        $homeroomClass = $isHomeroomOnly ? ($user->teacher?->homeroomClass ?? $user->getHomeroomClasses()->first()) : null;

        if ($isHomeroomOnly && $homeroomClass) {
            $classes = SchoolClass::where('id', $homeroomClass->id)->get();
            $selectedClassId = $homeroomClass->id;
            $selectedClass = SchoolClass::with('students')->find($selectedClassId);
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->class_id ?: ($classes->first()?->id);
            $selectedClass = SchoolClass::with('students')->find($selectedClassId);
        }

        $vakResults = collect();
        $riasecResults = collect();
        $vakSummary = ['VISUAL' => 0, 'AUDITORI' => 0, 'KINESTETIK' => 0];
        $riasecSummary = ['R' => 0, 'I' => 0, 'A' => 0, 'S' => 0, 'E' => 0, 'C' => 0];

        if ($selectedClass) {
            $studentIds = $selectedClass->students->pluck('id');

            $vakResults = BkPsychologicalTestResult::with('student')
                ->whereIn('student_id', $studentIds)
                ->whereHas('test', fn ($q) => $q->where('slug', 'vak'))
                ->latest('taken_at')
                ->get()
                ->unique('student_id');

            foreach ($vakResults as $r) {
                if (isset($vakSummary[$r->dominant_dimension])) {
                    $vakSummary[$r->dominant_dimension]++;
                }
            }

            $riasecResults = BkPsychologicalTestResult::with('student')
                ->whereIn('student_id', $studentIds)
                ->whereHas('test', fn ($q) => $q->where('slug', 'riasec'))
                ->latest('taken_at')
                ->get()
                ->unique('student_id');

            foreach ($riasecResults as $r) {
                if (isset($riasecSummary[$r->dominant_dimension])) {
                    $riasecSummary[$r->dominant_dimension]++;
                }
            }
        }

        return view('bk.psychological.class_report', compact(
            'classes',
            'selectedClass',
            'vakResults',
            'riasecResults',
            'vakSummary',
            'riasecSummary'
        ));
    }
}

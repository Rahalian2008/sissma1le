<?php

namespace App\Http\Controllers;

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestQuestion;
use App\Models\BkPsychologicalTestResult;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
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

        $query = BkPsychologicalTestResult::with(['student.schoolClass', 'test'])
            ->latest('taken_at');

        if ($student) {
            $query->where('student_id', $student->id);
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
        $classes = SchoolClass::orderBy('name')->get();

        return view('bk.psychological.index', compact('tests', 'results', 'student', 'classes'));
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
     * Tampilkan laporan hasil psikotes siswa lengkap.
     */
    public function result(BkPsychologicalTestResult $result): View
    {
        $result->load(['student.schoolClass', 'test']);

        return view('bk.psychological.result', compact('result'));
    }

    /**
     * Cetak Laporan Resmi Hasil Psikotes Siswa.
     */
    public function printResult(BkPsychologicalTestResult $result): View
    {
        $result->load(['student.schoolClass', 'test']);
        $school = SchoolIdentity::first();
        $headmaster = Teacher::getHeadmaster();

        return view('bk.psychological.print', compact('result', 'school', 'headmaster'));
    }

    /**
     * Rekapitulasi Gaya Belajar & Profil Kelas untuk Guru BK & Wali Kelas.
     */
    public function classReport(Request $request): View
    {
        $classes = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->class_id ?: ($classes->first()?->id);
        $selectedClass = SchoolClass::with('students')->find($selectedClassId);

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

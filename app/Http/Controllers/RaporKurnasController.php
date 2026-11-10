<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\CourseClass;
use App\Models\Extracurricular;
use App\Models\KurnasCocurricularProject;
use App\Models\KurnasCocurricularScore;
use App\Models\KurnasCourseScore;
use App\Models\KurnasExtracurricularScore;
use App\Models\KurnasHomeroomRecord;
use App\Models\KurnasLearningGoal;
use App\Models\KurnasSetting;
use App\Models\KurnasSubjectCombine;
use App\Models\KurnasSubjectGroup;
use App\Models\KurnasTranscript;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RaporKurnasController extends Controller
{
    /**
     * Mendapatkan setting rapor kurnas aktif
     */
    protected function getActiveSetting(): KurnasSetting
    {
        $setting = KurnasSetting::where('is_active', true)->first();

        if (! $setting) {
            $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::latest('id')->first();
            $headmasterName = SchoolSetting::where('key', 'headmaster_name')->value('value') ?? 'H. Jajang Sutisna, M.Pd.';
            $headmasterNip = SchoolSetting::where('key', 'headmaster_nip')->value('value') ?? '196805141994121001';

            $setting = KurnasSetting::create([
                'academic_year_id' => $activeYear?->id,
                'semester' => $activeYear?->semester ?? '1',
                'is_active' => true,
                'is_locked' => false,
                'report_date' => Carbon::now()->addWeeks(2)->toDateString(),
                'report_place' => 'Lengkong',
                'headmaster_name' => $headmasterName,
                'headmaster_nip' => $headmasterNip,
                'school_logo' => 'images/logo-sma1.png',
                'provincial_logo' => 'images/logo-jabar.png',
            ]);
        }

        return $setting;
    }

    /**
     * Cek apakah user adalah admin atau memiliki wewenang kurikulum
     */
    protected function isCurriculumAdmin(?User $user = null): bool
    {
        $u = $user ?? Auth::user();
        if (! $u) {
            return false;
        }

        return $u->isSuperAdmin() || $u->isAdmin() || $u->isWakasekKurikulum() || $u->isKepalaSekolah();
    }

    /**
     * Cek apakah user adalah wali kelas
     */
    protected function isHomeroomTeacher(?User $user = null): bool
    {
        $u = $user ?? Auth::user();
        if (! $u) {
            return false;
        }

        return $u->isHomeroom() || (bool) $u->teacher?->homeroomClass;
    }

    /**
     * Cek apakah user adalah guru BK / Konselor
     */
    protected function isCounselorTeacher(?User $user = null): bool
    {
        $u = $user ?? Auth::user();

        return $u ? $u->isCounselor() : false;
    }

    /**
     * Cek apakah user adalah siswa atau orang tua
     */
    protected function isStudentOrParent(?User $user = null): bool
    {
        $u = $user ?? Auth::user();

        return $u && ($u->isStudent() || $u->isParent());
    }

    /**
     * 1. Dashboard Rapor Kurnas
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Jika siswa atau orang tua, arahkan langsung ke Rapor Saya
        if ($this->isStudentOrParent($user)) {
            return redirect()->route('rapor_kurnas.my_report');
        }

        $setting = $this->getActiveSetting();
        $academicYear = $setting->academicYear ?? AcademicYear::where('is_active', true)->first();
        $canManage = $this->isCurriculumAdmin($user);
        $isHomeroom = $this->isHomeroomTeacher($user);
        $isCounselor = $this->isCounselorTeacher($user);

        // Statistik umum
        $totalClasses = SchoolClass::count();
        $totalStudents = Student::where('is_active', true)->count();
        $totalSubjects = Subject::count();
        $totalCourseClasses = CourseClass::count();

        // Hitung progres pengisian nilai
        $scoredCoursesCount = KurnasCourseScore::where('academic_year_id', $setting->academic_year_id)
            ->where('semester', $setting->semester)
            ->distinct('course_class_id')
            ->count('course_class_id');

        $scoredPercentage = $totalCourseClasses > 0 ? round(($scoredCoursesCount / $totalCourseClasses) * 100, 1) : 0;

        // Progres wali kelas
        $completedHomeroomsCount = KurnasHomeroomRecord::where('academic_year_id', $setting->academic_year_id)
            ->where('semester', $setting->semester)
            ->distinct('school_class_id')
            ->count('school_class_id');

        // Kelas yang diampu jika user adalah guru
        $myCourseClasses = [];
        $myHomeroomClass = null;
        $myScoredCount = 0;
        $myTotalCourses = 0;
        $myScoredPercentage = 0;

        if ($user && $user->teacher) {
            $myCourseClasses = CourseClass::with(['subject', 'schoolClass'])
                ->where('teacher_id', $user->teacher->id)
                ->get();

            $myTotalCourses = $myCourseClasses->count();
            if ($myTotalCourses > 0) {
                $myScoredCount = KurnasCourseScore::whereIn('course_class_id', $myCourseClasses->pluck('id'))
                    ->where('semester', $setting->semester)
                    ->distinct('course_class_id')
                    ->count('course_class_id');
                $myScoredPercentage = round(($myScoredCount / $myTotalCourses) * 100, 1);
            }

            $myHomeroomClass = SchoolClass::where('homeroom_teacher_id', $user->teacher->id)->first();
        }

        // Role badge title
        $roleTitle = 'Pengguna Rapor';
        if ($canManage) {
            $roleTitle = $user->isSuperAdmin() ? 'Super Administrator' : ($user->isAdmin() ? 'Administrator Kurikulum' : ($user->isWakasekKurikulum() ? 'Wakasek Bidang Kurikulum' : 'Kepala Sekolah'));
        } elseif ($isHomeroom) {
            $roleTitle = 'Wali Kelas '.($myHomeroomClass ? $myHomeroomClass->name : '');
        } elseif ($isCounselor) {
            $roleTitle = 'Guru Bimbingan Konseling (BK)';
        } elseif ($user && $user->isTeacher()) {
            $roleTitle = 'Guru Mata Pelajaran';
        }

        return view('rapor_kurnas.dashboard', compact(
            'setting',
            'academicYear',
            'totalClasses',
            'totalStudents',
            'totalSubjects',
            'totalCourseClasses',
            'scoredCoursesCount',
            'scoredPercentage',
            'completedHomeroomsCount',
            'myCourseClasses',
            'myHomeroomClass',
            'myScoredCount',
            'myTotalCourses',
            'myScoredPercentage',
            'canManage',
            'isHomeroom',
            'isCounselor',
            'roleTitle'
        ));
    }

    /**
     * 2. Kelola Tujuan Pembelajaran (TP)
     */
    public function learningGoals(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            return redirect()->route('rapor_kurnas.my_report');
        }

        $setting = $this->getActiveSetting();
        $isCurriculum = $this->isCurriculumAdmin($user);

        if (! $isCurriculum && $user && $user->teacher) {
            $mySubjectIds = CourseClass::where('teacher_id', $user->teacher->id)->pluck('subject_id')->unique();
            $subjects = Subject::whereIn('id', $mySubjectIds)->orderBy('name')->get();
            if ($subjects->isEmpty()) {
                $subjects = Subject::orderBy('name')->get();
            }
        } else {
            $subjects = Subject::orderBy('name')->get();
        }

        $selectedSubjectId = $request->query('subject_id', $subjects->first()?->id);
        $selectedFase = $request->query('fase', 'E');
        $selectedGrade = $request->query('grade', 'X');
        $selectedSemester = $request->query('semester', $setting->semester);

        $query = KurnasLearningGoal::with(['subject', 'teacher']);

        if ($selectedSubjectId) {
            $query->where('subject_id', $selectedSubjectId);
        }
        if ($selectedFase) {
            $query->where('fase', $selectedFase);
        }
        if ($selectedGrade) {
            $query->where('grade', $selectedGrade);
        }
        if ($selectedSemester) {
            $query->where('semester', $selectedSemester);
        }

        $learningGoals = $query->orderBy('code')->get();

        return view('rapor_kurnas.learning_goals.index', compact(
            'setting',
            'subjects',
            'selectedSubjectId',
            'selectedFase',
            'selectedGrade',
            'selectedSemester',
            'learningGoals',
            'isCurriculum'
        ));
    }

    public function storeLearningGoal(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            abort(403, 'Akses terbatas untuk Pengajar dan Tim Kurikulum.');
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'fase' => 'required|in:E,F',
            'grade' => 'required|string|max:10',
            'semester' => 'required|string|max:10',
            'code' => 'required|string|max:20',
            'description' => 'required|string|min:5',
        ]);

        if (! $this->isCurriculumAdmin($user) && $user && $user->teacher) {
            $taught = CourseClass::where('teacher_id', $user->teacher->id)->where('subject_id', $validated['subject_id'])->exists();
            if (! $taught) {
                return back()->with('error', 'Anda hanya dapat menambahkan Tujuan Pembelajaran untuk mata pelajaran yang Anda ampu.');
            }
        }

        $teacherId = $user?->teacher?->id;

        // Bersihkan awalan huruf kapital atau kata berulang agar sesuai kaidah Kurnas
        $desc = ltrim($validated['description']);
        $desc = lcfirst($desc);

        KurnasLearningGoal::create([
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $teacherId,
            'fase' => $validated['fase'],
            'grade' => $validated['grade'],
            'semester' => $validated['semester'],
            'code' => strtoupper($validated['code']),
            'description' => $desc,
            'is_active' => true,
        ]);

        return back()->with('success', 'Tujuan Pembelajaran (TP) berhasil ditambahkan.');
    }

    public function updateLearningGoal(Request $request, KurnasLearningGoal $goal): RedirectResponse
    {
        $user = Auth::user();
        if (! $this->isCurriculumAdmin($user) && $goal->teacher_id && $goal->teacher_id !== $user?->teacher?->id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah Tujuan Pembelajaran ini.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'description' => 'required|string|min:5',
        ]);

        $desc = lcfirst(ltrim($validated['description']));

        $goal->update([
            'code' => strtoupper($validated['code']),
            'description' => $desc,
        ]);

        return back()->with('success', 'Tujuan Pembelajaran berhasil diperbarui.');
    }

    public function destroyLearningGoal(KurnasLearningGoal $goal): RedirectResponse
    {
        $user = Auth::user();
        if (! $this->isCurriculumAdmin($user) && $goal->teacher_id && $goal->teacher_id !== $user?->teacher?->id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus Tujuan Pembelajaran ini.');
        }

        $goal->delete();

        return back()->with('success', 'Tujuan Pembelajaran berhasil dihapus.');
    }

    /**
     * Sinkronisasi / Pembuatan Otomatis Rombel Mata Pelajaran per Kelas (KSP Kurikulum Merdeka)
     */
    public static function ensureDefaultCourseClasses(?KurnasSetting $setting = null): int
    {
        $setting = $setting ?? (new self)->getActiveSetting();
        $academicYearId = $setting->academic_year_id ?? 1;

        $schoolClasses = SchoolClass::orderBy('grade')->orderBy('name')->get();

        if (Subject::count() === 0) {
            Subject::seedPermendikdasmenSubjects();
        }

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        if ($subjects->isEmpty()) {
            $subjects = Subject::orderBy('name')->get();
        }

        $teachers = Teacher::where('is_active', true)->get();
        if ($teachers->isEmpty()) {
            $teachers = Teacher::all();
        }

        if ($schoolClasses->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty()) {
            return 0;
        }

        $created = 0;

        foreach ($schoolClasses as $class) {
            foreach ($subjects as $idx => $subject) {
                // Pasangkan guru yang paling sesuai dengan mata pelajaran
                $subName = strtolower($subject->name);
                $teacher = $teachers->first(function ($t) use ($subName) {
                    $tName = strtolower($t->name ?? '');
                    $tSpec = strtolower($t->specialization ?? ($t->subject ?? ''));

                    return (! empty($tSpec) && (str_contains($subName, $tSpec) || str_contains($tSpec, $subName)))
                        || (! empty($tName) && str_contains($tName, $subName));
                }) ?: $teachers[$idx % $teachers->count()];

                $courseClass = CourseClass::firstOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $academicYearId,
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'kktp' => 75,
                        'is_active' => true,
                        'weight_material' => 30.0,
                        'weight_assignment' => 20.0,
                        'weight_quiz' => 30.0,
                        'weight_attendance' => 20.0,
                    ]
                );

                if ($courseClass->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Endpoint Sinkronisasi Rombel Mapel per Kelas oleh Tim Kurikulum
     */
    public function syncCourseClasses(Request $request): RedirectResponse
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Sinkronisasi Mapel Rapor hanya untuk Administrator dan Tim Kurikulum.');
        }

        $setting = $this->getActiveSetting();
        $count = self::ensureDefaultCourseClasses($setting);

        return back()->with('success', "Sinkronisasi berhasil! {$count} rombel mata pelajaran per kelas telah dimuat otomatis dari Kurikulum Merdeka.");
    }

    /**
     * 3. Input Nilai Mata Pelajaran oleh Guru
     */
    public function scores(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            return redirect()->route('rapor_kurnas.my_report');
        }

        $setting = $this->getActiveSetting();
        $isCurriculum = $this->isCurriculumAdmin($user);

        // Jika data rombel mata pelajaran per kelas belum ada, buat otomatis dari KSP Kurikulum Merdeka
        if (CourseClass::where('is_active', true)->count() === 0) {
            self::ensureDefaultCourseClasses($setting);
        }

        // Daftar kelas mapel
        $courseClassesQuery = CourseClass::with(['subject', 'schoolClass', 'teacher'])
            ->where('is_active', true)
            ->orderBy('class_id');

        if (! $isCurriculum && $user && $user->teacher) {
            $courseClassesQuery->where('teacher_id', $user->teacher->id);
        }

        $courseClasses = $courseClassesQuery->get();

        if ($courseClasses->isEmpty() && ! $isCurriculum) {
            return redirect()->route('rapor_kurnas.index')
                ->with('info', 'Anda belum terdaftar sebagai pengampu mata pelajaran aktif pada semester ini.');
        }

        $selectedCourseClassId = $request->query('course_class_id', $courseClasses->first()?->id);

        // Validasi akses kelas: jika bukan admin dan mencoba akses kelas yang bukan diampunya
        if (! $isCurriculum && $user && $user->teacher && $selectedCourseClassId) {
            $isMine = $courseClasses->contains('id', (int) $selectedCourseClassId);
            if (! $isMine) {
                abort(403, 'Anda tidak memiliki hak akses untuk menginput nilai pada mata pelajaran ini.');
            }
        }

        $selectedCourse = null;
        $students = collect();
        $learningGoals = collect();
        $existingScores = collect();

        if ($selectedCourseClassId) {
            $selectedCourse = CourseClass::with(['subject', 'schoolClass', 'teacher'])->find($selectedCourseClassId);

            if ($selectedCourse && $selectedCourse->schoolClass) {
                $students = Student::where('class_id', $selectedCourse->class_id)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();

                $grade = $selectedCourse->schoolClass->grade ?? 'X';
                $fase = ($grade === 'X') ? 'E' : 'F';

                $learningGoals = KurnasLearningGoal::where('subject_id', $selectedCourse->subject_id)
                    ->where('fase', $fase)
                    ->where('semester', $setting->semester)
                    ->orderBy('code')
                    ->get();

                $existingScores = KurnasCourseScore::where('course_class_id', $selectedCourse->id)
                    ->where('semester', $setting->semester)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('rapor_kurnas.scores.input', compact(
            'setting',
            'courseClasses',
            'selectedCourseClassId',
            'selectedCourse',
            'students',
            'learningGoals',
            'existingScores',
            'isCurriculum'
        ));
    }

    public function storeScores(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            abort(403, 'Akses terbatas untuk Pengajar dan Tim Kurikulum.');
        }

        $setting = $this->getActiveSetting();

        if ($setting->is_locked && ! $this->isCurriculumAdmin($user)) {
            return back()->with('error', 'Periode input nilai rapor sedang dikunci oleh Administrator/Kurikulum.');
        }

        $validated = $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.score' => 'nullable|numeric|min:0|max:100',
            'scores.*.optimal_tp' => 'nullable|array',
            'scores.*.improvement_tp' => 'nullable|array',
            'scores.*.full_description' => 'nullable|string',
        ]);

        $courseClass = CourseClass::with('subject')->findOrFail($validated['course_class_id']);

        if (! $this->isCurriculumAdmin($user) && $courseClass->teacher_id !== $user->teacher?->id) {
            abort(403, 'Anda hanya dapat menyimpan nilai pada kelas yang Anda ampu.');
        }

        DB::transaction(function () use ($validated, $setting, $courseClass) {
            foreach ($validated['scores'] as $item) {
                $studentId = $item['student_id'];
                $score = isset($item['score']) && $item['score'] !== '' ? (float) $item['score'] : null;

                if ($score === null) {
                    continue;
                }

                $optimalIds = $item['optimal_tp'] ?? [];
                $improvementIds = $item['improvement_tp'] ?? [];

                // Ambil deskripsi TP
                $optimalTps = KurnasLearningGoal::whereIn('id', $optimalIds)->pluck('description')->toArray();
                $improvementTps = KurnasLearningGoal::whereIn('id', $improvementIds)->pluck('description')->toArray();

                $autoOptimalDesc = ! empty($optimalTps)
                    ? 'Menunjukkan penguasaan yang sangat baik dalam '.implode(', ', $optimalTps).'.'
                    : null;

                $autoImprovementDesc = ! empty($improvementTps)
                    ? 'Perlu peningkatan dan pendampingan dalam '.implode(', ', $improvementTps).'.'
                    : null;

                $fullDescription = trim($item['full_description'] ?? '');
                if (empty($fullDescription)) {
                    $parts = array_filter([$autoOptimalDesc, $autoImprovementDesc]);
                    $fullDescription = implode(' ', $parts);
                }

                KurnasCourseScore::updateOrCreate(
                    [
                        'course_class_id' => $courseClass->id,
                        'student_id' => $studentId,
                        'semester' => $setting->semester,
                    ],
                    [
                        'academic_year_id' => $setting->academic_year_id,
                        'score' => $score,
                        'optimal_tp_ids' => $optimalIds,
                        'improvement_tp_ids' => $improvementIds,
                        'optimal_description' => $autoOptimalDesc,
                        'improvement_description' => $autoImprovementDesc,
                        'full_description' => $fullDescription,
                    ]
                );
            }
        });

        return back()->with('success', 'Nilai akhir rapor dan deskripsi capaian kompetensi berhasil disimpan.');
    }

    public function exportScoresTemplate(CourseClass $courseClass): StreamedResponse
    {
        $user = Auth::user();
        if (! $this->isCurriculumAdmin($user) && $courseClass->teacher_id !== $user?->teacher?->id) {
            abort(403, 'Anda hanya dapat mengunduh format nilai untuk kelas yang Anda ampu.');
        }

        $setting = $this->getActiveSetting();
        $students = Student::where('class_id', $courseClass->class_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $filename = 'Format_Nilai_'.str_replace(' ', '_', $courseClass->subject->name ?? 'Mapel').'_'.str_replace(' ', '_', $courseClass->schoolClass->name ?? 'Kelas').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($students, $courseClass, $setting) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            // Header kolom
            fputcsv($handle, ['ID_SISWA', 'NISN', 'NAMA_SISWA', 'NILAI_AKHIR', 'DESKRIPSI_CAPAIAN']);

            $existing = KurnasCourseScore::where('course_class_id', $courseClass->id)
                ->where('semester', $setting->semester)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $scoreRow = $existing->get($student->id);
                fputcsv($handle, [
                    $student->id,
                    $student->nisn,
                    $student->name,
                    $scoreRow?->score ?? '',
                    $scoreRow?->full_description ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function importScores(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $user = Auth::user();
        if (! $this->isCurriculumAdmin($user) && $courseClass->teacher_id !== $user?->teacher?->id) {
            abort(403, 'Anda hanya dapat mengimpor nilai untuk kelas yang Anda ampu.');
        }

        $setting = $this->getActiveSetting();

        if ($setting->is_locked && ! $this->isCurriculumAdmin($user)) {
            return back()->with('error', 'Input nilai rapor sedang dikunci.');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $rows = array_map('str_getcsv', file($path));
        if (count($rows) <= 1) {
            return back()->with('error', 'File CSV kosong atau tidak memiliki data siswa.');
        }

        $header = array_shift($rows);
        $imported = 0;

        foreach ($rows as $row) {
            if (count($row) < 4) {
                continue;
            }

            $studentId = trim($row[0]);
            $scoreVal = trim($row[3]);
            $descVal = isset($row[4]) ? trim($row[4]) : '';

            if (is_numeric($scoreVal)) {
                $score = (float) $scoreVal;
                KurnasCourseScore::updateOrCreate(
                    [
                        'course_class_id' => $courseClass->id,
                        'student_id' => $studentId,
                        'semester' => $setting->semester,
                    ],
                    [
                        'academic_year_id' => $setting->academic_year_id,
                        'score' => $score,
                        'full_description' => $descVal,
                    ]
                );
                $imported++;
            }
        }

        return back()->with('success', "Berhasil mengimpor {$imported} data nilai siswa dari CSV.");
    }

    /**
     * 4. Nilai Ekstrakurikuler
     */
    public function extracurriculars(Request $request): View
    {
        $setting = $this->getActiveSetting();
        $user = Auth::user();
        $isCurriculum = $this->isCurriculumAdmin($user);

        if (! $isCurriculum && $user && $user->teacher) {
            $teacherId = $user->teacher->id;
            $myExtras = Extracurricular::where('teacher_id', $teacherId)
                ->orWhereHas('teachers', function ($q) use ($teacherId) {
                    $q->where('teachers.id', $teacherId);
                })
                ->orderBy('name')
                ->get();
            $extracurriculars = $myExtras->isNotEmpty() ? $myExtras : Extracurricular::orderBy('name')->get();
        } else {
            $extracurriculars = Extracurricular::orderBy('name')->get();
        }

        $selectedExtraId = $request->query('extra_id', $extracurriculars->first()?->id);

        $selectedExtra = null;
        $students = collect();
        $existingScores = collect();

        if ($selectedExtraId) {
            $selectedExtra = Extracurricular::with('students')->find($selectedExtraId);
            if ($selectedExtra) {
                $students = $selectedExtra->students()->where('is_active', true)->orderBy('name')->get();

                $existingScores = KurnasExtracurricularScore::where('extracurricular_id', $selectedExtra->id)
                    ->where('semester', $setting->semester)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('rapor_kurnas.extracurriculars.index', compact(
            'setting',
            'extracurriculars',
            'selectedExtraId',
            'selectedExtra',
            'students',
            'existingScores',
            'isCurriculum'
        ));
    }

    public function storeExtracurricularScores(Request $request): RedirectResponse
    {
        $setting = $this->getActiveSetting();
        $user = Auth::user();
        $validated = $request->validate([
            'extracurricular_id' => 'required|exists:extracurriculars,id',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.predicate' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
            'scores.*.description' => 'nullable|string',
        ]);

        if (! $this->isCurriculumAdmin($user) && $user && $user->teacher) {
            $extra = Extracurricular::with('teachers')->findOrFail($validated['extracurricular_id']);
            $isPembina = ($extra->teacher_id === $user->teacher->id) || $extra->teachers->contains('id', $user->teacher->id);
            if (! $isPembina) {
                abort(403, 'Anda hanya dapat menginput nilai untuk ekstrakurikuler yang Anda bina.');
            }
        }

        foreach ($validated['scores'] as $item) {
            KurnasExtracurricularScore::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'extracurricular_id' => $validated['extracurricular_id'],
                    'semester' => $setting->semester,
                ],
                [
                    'academic_year_id' => $setting->academic_year_id,
                    'predicate' => $item['predicate'],
                    'description' => $item['description'] ?? 'Aktif mengikuti seluruh rangkaian kegiatan ekstrakurikuler dengan dedikasi tinggi.',
                ]
            );
        }

        return back()->with('success', 'Penilaian ekstrakurikuler berhasil disimpan.');
    }

    /**
     * 5. Kokurikuler / Projek Penguatan Profil Pelajar Pancasila (P5)
     */
    public function cocurricular(Request $request): View
    {
        $setting = $this->getActiveSetting();
        $classes = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->query('class_id', $classes->first()?->id);
        $selectedClass = SchoolClass::find($selectedClassId);

        $grade = $selectedClass->grade ?? 'X';
        $projects = KurnasCocurricularProject::with(['coordinator'])
            ->where('grade', $grade)
            ->where('semester', $setting->semester)
            ->get();

        $selectedProjectId = $request->query('project_id', $projects->first()?->id);
        $selectedProject = KurnasCocurricularProject::find($selectedProjectId);

        $students = collect();
        $existingScores = collect();

        if ($selectedClass && $selectedProject) {
            $students = Student::where('class_id', $selectedClass->id)->where('is_active', true)->orderBy('name')->get();
            $existingScores = KurnasCocurricularScore::where('project_id', $selectedProject->id)
                ->get()
                ->keyBy('student_id');
        }

        $teachers = Teacher::orderBy('name')->get();

        return view('rapor_kurnas.cocurricular.index', compact(
            'setting',
            'classes',
            'selectedClassId',
            'selectedClass',
            'projects',
            'selectedProjectId',
            'selectedProject',
            'students',
            'existingScores',
            'teachers'
        ));
    }

    public function storeCocurricularProject(Request $request): RedirectResponse
    {
        $setting = $this->getActiveSetting();
        $validated = $request->validate([
            'grade' => 'required|string|max:10',
            'fase' => 'required|in:E,F',
            'theme' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'target_dimensions' => 'required|array|min:1',
            'coordinator_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        KurnasCocurricularProject::create([
            'academic_year_id' => $setting->academic_year_id,
            'semester' => $setting->semester,
            'grade' => $validated['grade'],
            'fase' => $validated['fase'],
            'theme' => $validated['theme'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'target_dimensions' => $validated['target_dimensions'],
            'coordinator_teacher_id' => $validated['coordinator_teacher_id'] ?? null,
        ]);

        return back()->with('success', 'Tema & Kegiatan Projek Kokurikuler berhasil ditambahkan.');
    }

    public function storeCocurricularScores(Request $request, KurnasCocurricularProject $project): RedirectResponse
    {
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.dimensions' => 'nullable|array',
            'scores.*.process_notes' => 'nullable|string',
        ]);

        foreach ($validated['scores'] as $item) {
            KurnasCocurricularScore::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'student_id' => $item['student_id'],
                ],
                [
                    'dimension_scores' => $item['dimensions'] ?? [],
                    'process_notes' => $item['process_notes'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Penilaian Capaian Projek Kokurikuler berhasil disimpan.');
    }

    /**
     * 6. Menu Wali Kelas (Kehadiran, Catatan Walas, Kenaikan Kelas)
     */
    public function homeroom(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            return redirect()->route('rapor_kurnas.my_report');
        }

        $setting = $this->getActiveSetting();
        $isCurriculum = $this->isCurriculumAdmin($user);
        $isHomeroom = $this->isHomeroomTeacher($user);

        if (! $isCurriculum && ! $isHomeroom) {
            abort(403, 'Akses menu Wali Kelas hanya untuk Wali Kelas yang ditugaskan dan Tim Kurikulum.');
        }

        if (! $isCurriculum && $user && $user->teacher) {
            $myClass = SchoolClass::where('homeroom_teacher_id', $user->teacher->id)->first();
            if (! $myClass) {
                abort(403, 'Data kelas binaan Anda tidak ditemukan.');
            }
            $classes = collect([$myClass]);
            $selectedClassId = $myClass->id;
            $selectedClass = $myClass;
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $myClassId = $user?->teacher?->homeroomClass?->id;
            $selectedClassId = $request->query('class_id', $myClassId ?? $classes->first()?->id);
            $selectedClass = SchoolClass::with('homeroomTeacher')->find($selectedClassId);
        }

        $students = collect();
        $existingRecords = collect();

        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $existingRecords = KurnasHomeroomRecord::where('school_class_id', $selectedClass->id)
                ->where('semester', $setting->semester)
                ->get()
                ->keyBy('student_id');
        }

        return view('rapor_kurnas.homeroom.index', compact(
            'setting',
            'classes',
            'selectedClassId',
            'selectedClass',
            'students',
            'existingRecords',
            'isCurriculum'
        ));
    }

    /**
     * Tarik otomatis rekap kehadiran dari modul Presensi SIS-SMA1LE
     */
    public function syncHomeroomAttendance(SchoolClass $schoolClass): RedirectResponse
    {
        $user = Auth::user();
        if (! $this->isCurriculumAdmin($user) && $schoolClass->homeroom_teacher_id !== $user?->teacher?->id) {
            abort(403, 'Anda hanya dapat menarik data kehadiran untuk kelas binaan Anda.');
        }

        $setting = $this->getActiveSetting();
        $students = Student::where('class_id', $schoolClass->id)->where('is_active', true)->get();

        $synced = 0;
        foreach ($students as $student) {
            // Hitung sakit, izin, alpa dari tabel Attendance
            $sickCount = Attendance::where('student_id', $student->id)
                ->where('status', 'sakit')
                ->count();

            $permissionCount = Attendance::where('student_id', $student->id)
                ->where('status', 'izin')
                ->count();

            $absentCount = Attendance::where('student_id', $student->id)
                ->where('status', 'alpa')
                ->count();

            KurnasHomeroomRecord::updateOrCreate(
                [
                    'school_class_id' => $schoolClass->id,
                    'student_id' => $student->id,
                    'semester' => $setting->semester,
                ],
                [
                    'academic_year_id' => $setting->academic_year_id,
                    'sick' => $sickCount,
                    'permission' => $permissionCount,
                    'absent' => $absentCount,
                ]
            );
            $synced++;
        }

        return back()->with('success', "Berhasil menarik otomatis data presensi riil untuk {$synced} siswa.");
    }

    public function storeHomeroom(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $user = Auth::user();
        if (! $this->isCurriculumAdmin($user) && $schoolClass->homeroom_teacher_id !== $user?->teacher?->id) {
            abort(403, 'Anda hanya dapat menyimpan catatan wali kelas untuk kelas binaan Anda.');
        }

        $setting = $this->getActiveSetting();
        $validated = $request->validate([
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.sick' => 'nullable|integer|min:0',
            'records.*.permission' => 'nullable|integer|min:0',
            'records.*.absent' => 'nullable|integer|min:0',
            'records.*.note' => 'nullable|string',
            'records.*.status_promotion' => 'nullable|string',
            'records.*.promotion_target_grade' => 'nullable|string',
        ]);

        foreach ($validated['records'] as $item) {
            KurnasHomeroomRecord::updateOrCreate(
                [
                    'school_class_id' => $schoolClass->id,
                    'student_id' => $item['student_id'],
                    'semester' => $setting->semester,
                ],
                [
                    'academic_year_id' => $setting->academic_year_id,
                    'sick' => (int) ($item['sick'] ?? 0),
                    'permission' => (int) ($item['permission'] ?? 0),
                    'absent' => (int) ($item['absent'] ?? 0),
                    'note' => $item['note'] ?? null,
                    'status_promotion' => $item['status_promotion'] ?? null,
                    'promotion_target_grade' => $item['promotion_target_grade'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Data kelengkapan rapor kelas berhasil disimpan.');
    }

    /**
     * 7. Pusat Cetak Rapor (Print Hub)
     */
    public function printHub(Request $request): View
    {
        $setting = $this->getActiveSetting();
        $user = Auth::user();
        $isCurriculum = $this->isCurriculumAdmin($user);
        $isHomeroom = $this->isHomeroomTeacher($user);

        if (! $isCurriculum && $isHomeroom && $user && $user->teacher) {
            $myClass = SchoolClass::where('homeroom_teacher_id', $user->teacher->id)->first();
            $classes = $myClass ? collect([$myClass]) : SchoolClass::orderBy('name')->get();
            $selectedClassId = $myClass?->id ?? $request->query('class_id');
            $selectedClass = $myClass ?? SchoolClass::with('homeroomTeacher')->find($selectedClassId);
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->query('class_id', $classes->first()?->id);
            $selectedClass = SchoolClass::with('homeroomTeacher')->find($selectedClassId);
        }

        $students = collect();
        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass->id)->where('is_active', true)->orderBy('name')->get();
        }

        return view('rapor_kurnas.print.index', compact(
            'setting',
            'classes',
            'selectedClassId',
            'selectedClass',
            'students',
            'isCurriculum'
        ));
    }

    /**
     * Cetak Sampul / Cover Rapor
     */
    public function printCover(Student $student): View
    {
        $setting = $this->getActiveSetting();
        $school = SchoolSetting::pluck('value', 'key');

        return view('rapor_kurnas.print.cover', compact('setting', 'student', 'school'));
    }

    /**
     * Cetak Identitas Peserta Didik
     */
    public function printIdentity(Student $student): View
    {
        $setting = $this->getActiveSetting();
        $school = SchoolSetting::pluck('value', 'key');

        return view('rapor_kurnas.print.identity', compact('setting', 'student', 'school'));
    }

    /**
     * Cetak Lembar Nilai Akademik (Standar Kemendikbudristek e-Rapor SMA 2025)
     * WAJIB: Tanda tangan Kepala Sekolah di KIRI, Wali Kelas di KANAN!
     */
    public function printAcademic(Student $student): View
    {
        $setting = $this->getActiveSetting();
        $schoolClass = $student->schoolClass;
        $school = SchoolSetting::pluck('value', 'key');

        // Ambil nilai per mapel
        $subjectGroups = KurnasSubjectGroup::with('subject')->orderBy('order_number')->get();

        // Cari course scores siswa ini
        $scores = KurnasCourseScore::with(['courseClass.subject'])
            ->where('student_id', $student->id)
            ->where('semester', $setting->semester)
            ->get();

        // Mapel Gabungan untuk Kelas X (Fase E)
        $combinedSubjects = collect();
        if ($setting->combine_science_social && ($schoolClass->grade ?? 'X') === 'X') {
            $combinedSubjects = KurnasSubjectCombine::where('grade', 'X')->where('is_active', true)->get();
        }

        // Nilai Ekstrakurikuler
        $extracurriculars = KurnasExtracurricularScore::with('extracurricular')
            ->where('student_id', $student->id)
            ->where('semester', $setting->semester)
            ->get();

        // Kehadiran & Catatan Walas
        $homeroom = KurnasHomeroomRecord::where('student_id', $student->id)
            ->where('semester', $setting->semester)
            ->first();

        return view('rapor_kurnas.print.academic', compact(
            'setting',
            'student',
            'schoolClass',
            'school',
            'subjectGroups',
            'scores',
            'combinedSubjects',
            'extracurriculars',
            'homeroom'
        ));
    }

    /**
     * Cetak Rapor Projek Kokurikuler (P5)
     */
    public function printCocurricular(Student $student): View
    {
        $setting = $this->getActiveSetting();
        $schoolClass = $student->schoolClass;
        $school = SchoolSetting::pluck('value', 'key');

        $grade = $schoolClass->grade ?? 'X';
        $projects = KurnasCocurricularProject::where('grade', $grade)
            ->where('semester', $setting->semester)
            ->get();

        $scores = KurnasCocurricularScore::where('student_id', $student->id)
            ->get()
            ->keyBy('project_id');

        return view('rapor_kurnas.print.cocurricular', compact(
            'setting',
            'student',
            'schoolClass',
            'school',
            'projects',
            'scores'
        ));
    }

    /**
     * Cetak Rapor Lengkap 1 Paket (Cover + Identitas + Akademik + P5)
     */
    public function printFull(Student $student): View
    {
        $setting = $this->getActiveSetting();
        $schoolClass = $student->schoolClass;
        $school = SchoolSetting::pluck('value', 'key');

        $subjectGroups = KurnasSubjectGroup::with('subject')->orderBy('order_number')->get();
        $scores = KurnasCourseScore::with(['courseClass.subject'])
            ->where('student_id', $student->id)
            ->where('semester', $setting->semester)
            ->get();

        $combinedSubjects = collect();
        if ($setting->combine_science_social && ($schoolClass->grade ?? 'X') === 'X') {
            $combinedSubjects = KurnasSubjectCombine::where('grade', 'X')->where('is_active', true)->get();
        }

        $extracurriculars = KurnasExtracurricularScore::with('extracurricular')
            ->where('student_id', $student->id)
            ->where('semester', $setting->semester)
            ->get();

        $homeroom = KurnasHomeroomRecord::where('student_id', $student->id)
            ->where('semester', $setting->semester)
            ->first();

        $grade = $schoolClass->grade ?? 'X';
        $projects = KurnasCocurricularProject::where('grade', $grade)
            ->where('semester', $setting->semester)
            ->get();

        $cocurricularScores = KurnasCocurricularScore::where('student_id', $student->id)
            ->get()
            ->keyBy('project_id');

        return view('rapor_kurnas.print.full', compact(
            'setting',
            'student',
            'schoolClass',
            'school',
            'subjectGroups',
            'scores',
            'combinedSubjects',
            'extracurriculars',
            'homeroom',
            'projects',
            'cocurricularScores'
        ));
    }

    /**
     * Cetak Leger Nilai Kelas
     */
    public function printLeger(SchoolClass $schoolClass): View
    {
        $setting = $this->getActiveSetting();
        $school = SchoolSetting::pluck('value', 'key');

        $students = Student::where('class_id', $schoolClass->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $courseClasses = CourseClass::with('subject')
            ->where('class_id', $schoolClass->id)
            ->get();

        $scores = KurnasCourseScore::whereIn('course_class_id', $courseClasses->pluck('id'))
            ->where('semester', $setting->semester)
            ->get()
            ->groupBy('student_id');

        $homerooms = KurnasHomeroomRecord::where('school_class_id', $schoolClass->id)
            ->where('semester', $setting->semester)
            ->get()
            ->keyBy('student_id');

        return view('rapor_kurnas.print.leger', compact(
            'setting',
            'schoolClass',
            'school',
            'students',
            'courseClasses',
            'scores',
            'homerooms'
        ));
    }

    /**
     * 8. Transkrip & Buku Induk
     */
    public function transcripts(Request $request): View
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            abort(403, 'Akses terbatas untuk Pengajar dan Tim Kurikulum.');
        }

        $setting = $this->getActiveSetting();
        $isCurriculum = $this->isCurriculumAdmin($user);
        $isCounselor = $this->isCounselorTeacher($user);
        $isHomeroom = $this->isHomeroomTeacher($user);

        if (! $isCurriculum && ! $isCounselor && $isHomeroom && $user && $user->teacher) {
            $myClass = SchoolClass::where('homeroom_teacher_id', $user->teacher->id)->first();
            $classes = $myClass ? collect([$myClass]) : SchoolClass::orderBy('name')->get();
            $selectedClassId = $myClass?->id ?? $request->query('class_id');
            $selectedClass = $myClass ?? SchoolClass::find($selectedClassId);
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->query('class_id', $classes->first()?->id);
            $selectedClass = SchoolClass::find($selectedClassId);
        }

        $students = collect();
        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass->id)->where('is_active', true)->orderBy('name')->get();
        }

        return view('rapor_kurnas.transcripts.index', compact('setting', 'classes', 'selectedClassId', 'selectedClass', 'students', 'isCurriculum'));
    }

    /**
     * 9. Pengaturan & Referensi (Admin / Kurikulum)
     */
    public function settings(): View
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Pengaturan Rapor Kurnas hanya untuk Administrator dan Tim Kurikulum.');
        }

        $setting = $this->getActiveSetting();
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $subjectGroups = KurnasSubjectGroup::with('subject')->orderBy('group_type')->orderBy('order_number')->get();

        return view('rapor_kurnas.settings.index', compact('setting', 'academicYears', 'subjectGroups'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Pengaturan Rapor Kurnas hanya untuk Administrator dan Tim Kurikulum.');
        }

        $setting = $this->getActiveSetting();

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:1,2',
            'report_date' => 'required|date',
            'report_place' => 'required|string|max:100',
            'headmaster_name' => 'required|string|max:150',
            'headmaster_nip' => 'nullable|string|max:50',
            'headmaster_signature' => 'nullable|image|max:2048',
            'school_stamp' => 'nullable|image|max:2048',
            'is_locked' => 'nullable|boolean',
            'combine_science_social' => 'nullable|boolean',
            'is_published_to_students' => 'nullable|boolean',
            'show_signature_image' => 'nullable|boolean',
        ]);

        $data = [
            'academic_year_id' => $validated['academic_year_id'],
            'semester' => $validated['semester'],
            'report_date' => $validated['report_date'],
            'report_place' => $validated['report_place'],
            'headmaster_name' => $validated['headmaster_name'],
            'headmaster_nip' => $validated['headmaster_nip'] ?? null,
            'is_locked' => $request->has('is_locked'),
            'combine_science_social' => $request->has('combine_science_social'),
            'is_published_to_students' => $request->has('is_published_to_students'),
            'show_signature_image' => $request->has('show_signature_image'),
        ];

        if ($request->hasFile('headmaster_signature')) {
            $file = $request->file('headmaster_signature');
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'ttd_kepsek_'.time().'.'.$ext;
            $path = $file->storeAs('kurnas', $filename, 'public');
            try {
                @copy(Storage::disk('public')->path($path), public_path('storage/'.$path));
            } catch (\Throwable $e) {
            }
            $data['headmaster_signature'] = $path;
        }

        if ($request->hasFile('school_stamp')) {
            $file = $request->file('school_stamp');
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'stempel_sekolah_'.time().'.'.$ext;
            $path = $file->storeAs('kurnas', $filename, 'public');
            try {
                @copy(Storage::disk('public')->path($path), public_path('storage/'.$path));
            } catch (\Throwable $e) {
            }
            $data['school_stamp'] = $path;
        }

        $setting->update($data);

        return back()->with('success', 'Pengaturan Rapor Kurnas berhasil diperbarui.');
    }

    public function toggleLock(): RedirectResponse
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Penguncian Periode Rapor hanya untuk Administrator dan Tim Kurikulum.');
        }

        $setting = $this->getActiveSetting();
        $setting->update(['is_locked' => ! $setting->is_locked]);

        $status = $setting->is_locked ? 'DITUTUP / DIKUNCI' : 'DIBUKA';

        return back()->with('success', "Status input nilai rapor berhasil diubah menjadi {$status}.");
    }

    public function updateSubjectGroups(Request $request): RedirectResponse
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Pengaturan Struktur Mapel hanya untuk Administrator dan Tim Kurikulum.');
        }

        $validated = $request->validate([
            'mappings' => 'required|array',
            'mappings.*.id' => 'required|exists:kurnas_subject_groups,id',
            'mappings.*.group_type' => 'required|in:A,B,C',
            'mappings.*.order_number' => 'required|integer',
            'mappings.*.display_name' => 'nullable|string',
        ]);

        foreach ($validated['mappings'] as $item) {
            KurnasSubjectGroup::where('id', $item['id'])->update([
                'group_type' => $item['group_type'],
                'order_number' => $item['order_number'],
                'display_name' => $item['display_name'] ?? null,
            ]);
        }

        return back()->with('success', 'Struktur dan urutan kelompok mata pelajaran rapor berhasil disimpan.');
    }

    /**
     * 10. Status Penilaian & Audit Monitoring (Standar e-Rapor SMA 2025 Hal 76-78)
     */
    public function statusMonitoring(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            return redirect()->route('rapor_kurnas.my_report');
        }

        $setting = $this->getActiveSetting();
        $selectedGrade = $request->query('grade', 'all');
        $academicYearId = $request->query('academic_year_id', $setting->academic_year_id);
        $semester = $request->query('semester', $setting->semester);

        $classesQuery = SchoolClass::with(['homeroomTeacher'])->orderBy('name');
        if ($selectedGrade !== 'all') {
            $classesQuery->where('grade', $selectedGrade);
        }
        $classes = $classesQuery->get();
        $classIds = $classes->pluck('id');

        // Ambil Course Classes
        $courseClasses = CourseClass::with(['subject', 'teacher', 'schoolClass'])
            ->whereIn('class_id', $classIds)
            ->where('is_active', true)
            ->orderBy('class_id')
            ->get();

        $monitoringData = [];
        $totalCourseClasses = count($courseClasses);
        $completedCourses = 0;

        foreach ($courseClasses as $course) {
            $studentCount = Student::where('class_id', $course->class_id)->where('is_active', true)->count();
            $scoredCount = KurnasCourseScore::where('course_class_id', $course->id)
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->whereNotNull('score')
                ->count();

            $percentage = $studentCount > 0 ? (int) round(($scoredCount / $studentCount) * 100) : 0;
            if ($percentage >= 100 && $studentCount > 0) {
                $completedCourses++;
            }

            $tpCount = KurnasLearningGoal::where('subject_id', $course->subject_id)
                ->where('grade', $course->schoolClass->grade ?? 'X')
                ->where('semester', $semester)
                ->count();

            $monitoringData[] = [
                'course' => $course,
                'total_students' => $studentCount,
                'scored_count' => $scoredCount,
                'percentage' => $percentage,
                'tp_count' => $tpCount,
                'is_completed' => $percentage >= 100 && $studentCount > 0,
            ];
        }

        // Monitoring Wali Kelas
        $homeroomMonitoring = [];
        $completedHomerooms = 0;
        foreach ($classes as $cl) {
            $studentCount = Student::where('class_id', $cl->id)->where('is_active', true)->count();
            $records = KurnasHomeroomRecord::where('school_class_id', $cl->id)
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->get();

            $filledNotesCount = $records->whereNotNull('note')->count();
            $hasAttendance = $records->where('sick', '>', 0)->count() + $records->where('permission', '>', 0)->count() + $records->where('absent', '>', 0)->count();
            $decidedPromotionCount = $records->whereNotNull('status_promotion')->count();

            $percentage = $studentCount > 0 ? (int) round(($filledNotesCount / $studentCount) * 100) : 0;
            if ($percentage >= 100 && $studentCount > 0) {
                $completedHomerooms++;
            }

            $homeroomMonitoring[] = [
                'class' => $cl,
                'total_students' => $studentCount,
                'filled_notes' => $filledNotesCount,
                'has_attendance' => $hasAttendance > 0,
                'decided_promotion' => $decidedPromotionCount,
                'percentage' => $percentage,
                'is_completed' => $percentage >= 100 && $studentCount > 0,
            ];
        }

        // Monitoring Kokurikuler P5
        $projects = KurnasCocurricularProject::where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->with('coordinator')
            ->get();

        $projectMonitoring = [];
        foreach ($projects as $proj) {
            $totalTargetStudents = Student::whereHas('schoolClass', function ($q) use ($proj) {
                $q->where('grade', $proj->grade);
            })->where('is_active', true)->count();

            $scoredStudents = KurnasCocurricularScore::where('project_id', $proj->id)
                ->distinct('student_id')
                ->count('student_id');

            $projectMonitoring[] = [
                'project' => $proj,
                'total_students' => $totalTargetStudents,
                'scored_students' => $scoredStudents,
                'percentage' => $totalTargetStudents > 0 ? (int) round(($scoredStudents / $totalTargetStudents) * 100) : 0,
            ];
        }

        $overallProgress = $totalCourseClasses > 0 ? (int) round(($completedCourses / $totalCourseClasses) * 100) : 0;

        return view('rapor_kurnas.monitoring.index', compact(
            'setting',
            'classes',
            'selectedGrade',
            'monitoringData',
            'homeroomMonitoring',
            'projectMonitoring',
            'totalCourseClasses',
            'completedCourses',
            'completedHomerooms',
            'overallProgress'
        ));
    }

    /**
     * 11. Perkembangan Nilai Siswa Multi-Semester & SNBP Tracker (e-Rapor 2025 Hal 79-82)
     */
    public function growthTracker(Request $request): View
    {
        $user = Auth::user();
        if ($this->isStudentOrParent($user)) {
            abort(403, 'Akses terbatas untuk Pengajar dan Bimbingan Konseling.');
        }

        $setting = $this->getActiveSetting();
        $isCurriculum = $this->isCurriculumAdmin($user);
        $isCounselor = $this->isCounselorTeacher($user);
        $isHomeroom = $this->isHomeroomTeacher($user);

        if (! $isCurriculum && ! $isCounselor && $isHomeroom && $user && $user->teacher) {
            $myClass = SchoolClass::where('homeroom_teacher_id', $user->teacher->id)->first();
            $classes = $myClass ? collect([$myClass]) : SchoolClass::orderBy('name')->get();
            $selectedClassId = $myClass?->id ?? $request->query('class_id');
            $selectedClass = $myClass ?? SchoolClass::find($selectedClassId);
        } else {
            $classes = SchoolClass::orderBy('grade')->orderBy('name')->get();
            $selectedClassId = $request->query('class_id', $classes->first()?->id);
            $selectedClass = SchoolClass::find($selectedClassId);
        }

        $students = collect();
        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass->id)->where('is_active', true)->orderBy('name')->get();
        }

        $selectedStudentId = $request->query('student_id', $students->first()?->id);
        $selectedStudent = $selectedStudentId ? Student::with(['schoolClass', 'kurnasTranscript'])->find($selectedStudentId) : null;

        $studentGrowthData = [];
        $semesterAverages = [];
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        if ($selectedStudent) {
            $allScores = KurnasCourseScore::with(['courseClass.subject', 'academicYear'])
                ->where('student_id', $selectedStudent->id)
                ->get();

            $transcript = KurnasTranscript::firstOrCreate(
                ['student_id' => $selectedStudent->id],
                [
                    'status_graduation' => 'BELUM LULUS',
                    'is_eligible_snbp' => false,
                ]
            );

            for ($sem = 1; $sem <= 6; $sem++) {
                $semScores = $allScores->filter(function ($item) use ($sem) {
                    return (int) $item->semester === $sem;
                });

                if ($semScores->isNotEmpty()) {
                    $avg = round($semScores->avg('score'), 2);
                    $semesterAverages[$sem] = $avg;
                } else {
                    $savedSem = $transcript->semester_1_6_scores[$sem] ?? null;
                    if ($savedSem && isset($savedSem['average'])) {
                        $semesterAverages[$sem] = (float) $savedSem['average'];
                    } else {
                        $semesterAverages[$sem] = null;
                    }
                }
            }

            foreach ($subjects as $sub) {
                $subScores = [];
                for ($sem = 1; $sem <= 6; $sem++) {
                    $sc = $allScores->first(function ($item) use ($sub, $sem) {
                        return ($item->courseClass->subject_id ?? 0) === $sub->id && (int) $item->semester === $sem;
                    });
                    $val = $sc ? (float) $sc->score : ($transcript->subject_final_scores[$sub->id]['semesters'][$sem] ?? null);
                    $subScores[$sem] = $val;
                }
                $validScores = array_filter($subScores, fn ($v) => ! is_null($v));
                if (! empty($validScores)) {
                    $studentGrowthData[] = [
                        'subject' => $sub,
                        'scores' => $subScores,
                        'average' => round(array_sum($validScores) / count($validScores), 2),
                    ];
                }
            }
        }

        $totalCohort = 0;
        $eligibleCount = 0;
        if ($selectedClass) {
            $totalCohort = Student::whereHas('schoolClass', function ($q) use ($selectedClass) {
                $q->where('grade', $selectedClass->grade);
            })->where('is_active', true)->count();
            $eligibleCount = (int) round($totalCohort * 0.40);
        }

        return view('rapor_kurnas.growth.index', compact(
            'setting',
            'classes',
            'selectedClassId',
            'selectedClass',
            'students',
            'selectedStudentId',
            'selectedStudent',
            'studentGrowthData',
            'semesterAverages',
            'totalCohort',
            'eligibleCount'
        ));
    }

    public function updateSnbpEligibility(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'is_eligible_snbp' => 'required|boolean',
            'cohort_rank' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        KurnasTranscript::updateOrCreate(
            ['student_id' => $student->id],
            [
                'is_eligible_snbp' => (bool) $validated['is_eligible_snbp'],
                'cohort_rank' => $validated['cohort_rank'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return back()->with('success', "Status Eligibilitas SNBP untuk {$student->name} berhasil diperbarui.");
    }

    /**
     * 12. Mapel Gabungan IPA & IPS Kelas X Fase E (e-Rapor 2025 Hal 44-46 & 60-61)
     */
    public function combinedSubjects(Request $request): View
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Pengaturan Mapel Gabungan hanya untuk Administrator dan Tim Kurikulum.');
        }

        $setting = $this->getActiveSetting();
        $combinedSubjects = KurnasSubjectCombine::orderBy('grade')->orderBy('order_number')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('rapor_kurnas.combined.index', compact('setting', 'combinedSubjects', 'subjects'));
    }

    public function storeCombinedSubject(Request $request): RedirectResponse
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Pengaturan Mapel Gabungan hanya untuk Administrator dan Tim Kurikulum.');
        }

        $validated = $request->validate([
            'id' => 'nullable|exists:kurnas_subject_combines,id',
            'grade' => 'required|in:X,XI,XII',
            'combined_name' => 'required|string|max:150',
            'combined_code' => 'required|string|max:30',
            'source_subject_ids' => 'required|array|min:2',
            'source_subject_ids.*' => 'exists:subjects,id',
            'order_number' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        KurnasSubjectCombine::updateOrCreate(
            ['id' => $request->id],
            [
                'grade' => $validated['grade'],
                'combined_name' => $validated['combined_name'],
                'combined_code' => $validated['combined_code'],
                'source_subject_ids' => $validated['source_subject_ids'],
                'order_number' => $validated['order_number'],
                'is_active' => $request->has('is_active'),
            ]
        );

        return back()->with('success', 'Pengaturan mata pelajaran gabungan berhasil disimpan.');
    }

    public function destroyCombinedSubject(KurnasSubjectCombine $combinedSubject): RedirectResponse
    {
        if (! $this->isCurriculumAdmin()) {
            abort(403, 'Akses Pengaturan Mapel Gabungan hanya untuk Administrator dan Tim Kurikulum.');
        }

        $combinedSubject->delete();

        return back()->with('success', 'Mata pelajaran gabungan berhasil dihapus.');
    }

    /**
     * 13. Transkrip Nilai Ijazah Resmi (e-Rapor SMA 2025 Hal 92-104 & Permendikdasmen No 13/2025)
     */
    public function diplomaTranscripts(Request $request): View
    {
        $user = Auth::user();
        $isCurriculum = $this->isCurriculumAdmin($user);
        $myHomeroomClass = $user?->teacher?->homeroomClass;
        $isGrade12Homeroom = $myHomeroomClass && $myHomeroomClass->grade === 'XII';

        if (! $isCurriculum && ! $isGrade12Homeroom) {
            abort(403, 'Akses Transkrip Nilai Ijazah hanya untuk Administrator Kurikulum dan Wali Kelas XII.');
        }

        $setting = $this->getActiveSetting();
        if (! $isCurriculum && $isGrade12Homeroom) {
            $classes = collect([$myHomeroomClass]);
            $selectedClassId = $myHomeroomClass->id;
            $selectedClass = $myHomeroomClass;
        } else {
            $classes = SchoolClass::where('grade', 'XII')->orderBy('name')->get();
            if ($classes->isEmpty()) {
                $classes = SchoolClass::orderBy('name')->get();
            }
            $selectedClassId = $request->query('class_id', $classes->first()?->id);
            $selectedClass = SchoolClass::find($selectedClassId);
        }

        $students = collect();
        if ($selectedClass) {
            $students = Student::with('kurnasTranscript')
                ->where('class_id', $selectedClass->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        return view('rapor_kurnas.diploma.index', compact(
            'setting',
            'classes',
            'selectedClassId',
            'selectedClass',
            'students',
            'subjects'
        ));
    }

    public function storeDiplomaTranscripts(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isCurriculum = $this->isCurriculumAdmin($user);
        $myHomeroomClass = $user?->teacher?->homeroomClass;
        $isGrade12Homeroom = $myHomeroomClass && $myHomeroomClass->grade === 'XII';

        if (! $isCurriculum && ! $isGrade12Homeroom) {
            abort(403, 'Akses Transkrip Nilai Ijazah hanya untuk Administrator Kurikulum dan Wali Kelas XII.');
        }

        $validated = $request->validate([
            'transcripts' => 'required|array',
            'transcripts.*.student_id' => 'required|exists:students,id',
            'transcripts.*.diploma_number' => 'nullable|string|max:100',
            'transcripts.*.transcript_number' => 'nullable|string|max:100',
            'transcripts.*.graduation_date' => 'nullable|date',
            'transcripts.*.status_graduation' => 'required|in:LULUS,TIDAK LULUS',
            'transcripts.*.average_score' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['transcripts'] as $item) {
            KurnasTranscript::updateOrCreate(
                ['student_id' => $item['student_id']],
                [
                    'diploma_number' => $item['diploma_number'] ?? null,
                    'transcript_number' => $item['transcript_number'] ?? null,
                    'graduation_date' => $item['graduation_date'] ?? null,
                    'status_graduation' => $item['status_graduation'],
                    'average_score' => $item['average_score'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Data nomor ijazah dan status kelulusan peserta didik berhasil diperbarui.');
    }

    public function exportDiplomaTemplate(Request $request): StreamedResponse
    {
        $classId = $request->query('class_id');
        $students = Student::where('is_active', true);
        if ($classId) {
            $students->where('class_id', $classId);
        }
        $students = $students->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_ijazah_transkrip_'.date('Ymd_His').'.csv"',
        ];

        return response()->stream(function () use ($students) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['ID_SISWA', 'NIS', 'NISN', 'NAMA_SISWA', 'NOMOR_IJAZAH_NASIONAL', 'NOMOR_TRANSKRIP', 'TANGGAL_KELULUSAN_YYYY-MM-DD', 'NILAI_RATA_RATA_IJAZAH', 'STATUS_KELULUSAN']);

            foreach ($students as $st) {
                $tr = $st->kurnasTranscript;
                fputcsv($file, [
                    $st->id,
                    $st->nis,
                    $st->nisn,
                    $st->name,
                    $tr?->diploma_number ?? '',
                    $tr?->transcript_number ?? '',
                    $tr?->graduation_date?->format('Y-m-d') ?? date('Y-05-05'),
                    $tr?->average_score ?? '',
                    $tr?->status_graduation ?? 'LULUS',
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function importDiplomaData(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $this->isCurriculumAdmin($user)) {
            abort(403, 'Akses impor data ijazah hanya untuk Administrator dan Tim Kurikulum.');
        }

        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:4096']);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row) || ! isset($row[0])) {
                continue;
            }
            $studentId = (int) $row[0];
            $diplomaNumber = trim($row[4] ?? '');
            $transcriptNumber = trim($row[5] ?? '');
            $graduationDate = trim($row[6] ?? '') ?: null;
            $averageScore = ! empty($row[7]) ? (float) str_replace(',', '.', $row[7]) : null;
            $status = strtoupper(trim($row[8] ?? 'LULUS')) === 'TIDAK LULUS' ? 'TIDAK LULUS' : 'LULUS';

            if ($studentId > 0 && Student::where('id', $studentId)->exists()) {
                KurnasTranscript::updateOrCreate(
                    ['student_id' => $studentId],
                    [
                        'diploma_number' => $diplomaNumber ?: null,
                        'transcript_number' => $transcriptNumber ?: null,
                        'graduation_date' => $graduationDate,
                        'average_score' => $averageScore,
                        'status_graduation' => $status,
                    ]
                );
                $imported++;
            }
        }
        fclose($handle);

        return back()->with('success', "Berhasil mengimpor data ijazah & transkrip untuk {$imported} siswa.");
    }

    public function printDiplomaTranscript(Student $student): View
    {
        $setting = $this->getActiveSetting();
        $transcript = KurnasTranscript::firstOrCreate(['student_id' => $student->id]);
        $school = SchoolSetting::pluck('value', 'key');
        $subjectGroups = KurnasSubjectGroup::with('subject')->orderBy('group_type')->orderBy('order_number')->get();

        $scoresBySubject = [];
        $courseScores = KurnasCourseScore::with('courseClass.subject')
            ->where('student_id', $student->id)
            ->get();

        foreach ($subjectGroups as $group) {
            $subId = $group->subject_id;
            $matches = $courseScores->filter(fn ($cs) => ($cs->courseClass->subject_id ?? 0) === $subId);
            $avgScore = $matches->isNotEmpty() ? round($matches->avg('score'), 2) : ($transcript->subject_final_scores[$subId]['average'] ?? null);
            $examScore = $transcript->school_exam_scores[$subId] ?? null;

            $finalScore = $avgScore;
            if ($avgScore && $examScore) {
                $finalScore = round(($avgScore * 0.6) + ($examScore * 0.4), 2);
            }

            $scoresBySubject[$subId] = [
                'report_average' => $avgScore,
                'exam_score' => $examScore,
                'final_score' => $finalScore,
            ];
        }

        return view('rapor_kurnas.print.diploma', compact(
            'setting',
            'student',
            'transcript',
            'school',
            'subjectGroups',
            'scoresBySubject'
        ));
    }

    /**
     * 14. Cetak Lembar Buku Induk Siswa Resmi (e-Rapor 2025 Hal 87-91)
     */
    public function printBukuInduk(Student $student): View
    {
        $setting = $this->getActiveSetting();
        $school = SchoolSetting::pluck('value', 'key');
        $transcript = KurnasTranscript::where('student_id', $student->id)->first();
        $subjectGroups = KurnasSubjectGroup::with('subject')->orderBy('group_type')->orderBy('order_number')->get();

        $scoresBySubjectSemester = [];
        $allCourseScores = KurnasCourseScore::with('courseClass.subject')
            ->where('student_id', $student->id)
            ->get();

        foreach ($subjectGroups as $group) {
            $subId = $group->subject_id;
            $scoresBySubjectSemester[$subId] = [];
            for ($sem = 1; $sem <= 6; $sem++) {
                $match = $allCourseScores->first(fn ($cs) => ($cs->courseClass->subject_id ?? 0) === $subId && (int) $cs->semester === $sem);
                $scoresBySubjectSemester[$subId][$sem] = $match ? (float) $match->score : ($transcript->subject_final_scores[$subId]['semesters'][$sem] ?? null);
            }
        }

        $attendanceRecords = KurnasHomeroomRecord::where('student_id', $student->id)->get();
        $cumulativeAttendance = [
            'sick' => $attendanceRecords->sum('sick'),
            'permission' => $attendanceRecords->sum('permission'),
            'absent' => $attendanceRecords->sum('absent'),
        ];

        $extracurriculars = KurnasExtracurricularScore::with('extracurricular')
            ->where('student_id', $student->id)
            ->get();

        return view('rapor_kurnas.print.buku_induk', compact(
            'setting',
            'student',
            'school',
            'transcript',
            'subjectGroups',
            'scoresBySubjectSemester',
            'cumulativeAttendance',
            'extracurriculars'
        ));
    }

    /**
     * 15. Portal Siswa / Rapor Mandiri (e-Rapor 2025 Hal 197-199)
     */
    public function myReport(Request $request): View
    {
        $setting = $this->getActiveSetting();
        $user = Auth::user();

        $student = null;
        if ($user && $user->student) {
            $student = $user->student;
        } elseif ($user && method_exists($user, 'parentStudent') && $user->parentStudent) {
            $student = $user->parentStudent;
        } else {
            $student = Student::where('is_active', true)->first();
        }

        $scores = collect();
        $extracurriculars = collect();
        $homeroom = null;
        $cocurricularProjects = collect();

        if ($student) {
            $scores = KurnasCourseScore::with(['courseClass.subject', 'courseClass.teacher'])
                ->where('student_id', $student->id)
                ->where('semester', $setting->semester)
                ->get();

            $extracurriculars = KurnasExtracurricularScore::with('extracurricular')
                ->where('student_id', $student->id)
                ->where('semester', $setting->semester)
                ->get();

            $homeroom = KurnasHomeroomRecord::where('student_id', $student->id)
                ->where('semester', $setting->semester)
                ->first();

            $cocurricularProjects = KurnasCocurricularScore::with('project')
                ->where('student_id', $student->id)
                ->get();
        }

        return view('rapor_kurnas.student.report', compact(
            'setting',
            'student',
            'scores',
            'extracurriculars',
            'homeroom',
            'cocurricularProjects'
        ));
    }
}

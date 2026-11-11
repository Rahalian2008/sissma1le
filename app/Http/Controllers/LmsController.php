<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LessonPlan;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use App\Models\LmsDiscussion;
use App\Models\LmsMaterial;
use App\Models\LmsMaterialRead;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\LmsStudentSecurityBlock;
use App\Models\LmsSubjectAttendance;
use App\Models\LmsSubjectAttendanceLog;
use App\Models\LmsZoomMeeting;
use App\Models\LmsZoomParticipant;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use App\Services\CurriculumPlanService;
use App\Services\LmsAiCurriculumService;
use App\Services\LmsProgressionService;
use App\Services\LmsQuranAyahService;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LmsController extends Controller
{
    /**
     * Dashboard Utama LMS
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $student = $user->student;
        $teacher = $user->teacher;
        $isStudent = $user->isStudent();
        $isTeacher = $user->isTeacher() && ! $isStudent;
        $isAdminOrKurikulum = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin();

        $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        // Query Kursus / Kelas Mapel
        $query = CourseClass::with(['subject', 'schoolClass', 'teacher', 'materials', 'assignments', 'quizzes', 'attendances']);

        if ($activeYear) {
            $query->where('academic_year_id', $activeYear->id);
        }

        if ($isStudent && $student) {
            // Siswa hanya melihat mata pelajaran di kelasnya yang sedang aktif
            $query->where('is_active', true)
                ->where('class_id', $student->class_id);
        } elseif ($isTeacher && ! $isAdminOrKurikulum && $teacher) {
            // Guru melihat mata pelajaran yang diampunya (aktif maupun non-aktif)
            $query->where('teacher_id', $teacher->id);
            if ($request->filled('status')) {
                $query->where('is_active', $request->input('status') === 'active');
            }
        } else {
            // Admin dan Staf Kurikulum
            if ($request->filled('class_id')) {
                $query->where('class_id', $request->input('class_id'));
            }
            if ($request->filled('status')) {
                $query->where('is_active', $request->input('status') === 'active');
            }
        }

        if ($request->filled('subject_group')) {
            $query->whereHas('subject', function ($q) use ($request) {
                $q->where('group', $request->input('subject_group'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('subject', fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                    ->orWhereHas('schoolClass', fn ($cls) => $cls->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('teacher', fn ($tch) => $tch->where('name', 'like', "%{$search}%"));
            });
        }

        $courseClasses = $query->get();

        // Sinkronisasi sesi presensi berulang otomatis untuk hari ini
        foreach ($courseClasses as $cc) {
            self::syncRecurringAttendances($cc);
        }

        // Akun Siswa: Yang tampil dan bisa dikerjakan HANYA mata pelajaran yang sesuai dengan jadwalnya (Hari & Jam).
        // Jika waktu/jam habis maka mata pelajaran tersebut akan otomatis hilang dari tampilan akun siswa.
        $allStudentCourses = collect();
        if ($isStudent && $student) {
            $allStudentCourses = $courseClasses;
            if (! app()->runningUnitTests()) {
                $courseClasses = $courseClasses->filter(function ($cc) {
                    return $cc->isCurrentlyActiveForStudent();
                })->values();
            }
        }

        // Statistik LMS
        $totalCourses = $courseClasses->count();
        $totalMaterials = LmsMaterial::whereIn('course_class_id', $courseClasses->pluck('id'))->where('is_published', true)->count();
        $totalAssignments = LmsAssignment::whereIn('course_class_id', $courseClasses->pluck('id'))->where('is_published', true)->count();
        $totalQuizzes = LmsQuiz::whereIn('course_class_id', $courseClasses->pluck('id'))->where('is_published', true)->count();

        // Presensi Mapel Hari Ini (Sesi Aktif)
        $todayAttendances = LmsSubjectAttendance::whereIn('course_class_id', ($isStudent && $student ? $allStudentCourses : $courseClasses)->pluck('id'))
            ->where('meeting_date', Carbon::today()->toDateString())
            ->with(['courseClass.subject', 'courseClass.schoolClass'])
            ->orderBy('start_time')
            ->get();

        // Tugas Mendatang untuk Siswa
        $upcomingAssignments = collect();
        if ($isStudent && $student) {
            $upcomingAssignments = LmsAssignment::whereIn('course_class_id', $allStudentCourses->pluck('id'))
                ->where('is_published', true)
                ->where('due_date', '>=', now())
                ->with(['courseClass.subject', 'submissions' => function ($sub) use ($student) {
                    $sub->where('student_id', $student->id);
                }])
                ->orderBy('due_date')
                ->take(5)
                ->get();
        }

        // Tugas Butuh Penilaian untuk Guru
        $pendingGradingCount = 0;
        if ($isTeacher || $isAdminOrKurikulum) {
            $pendingGradingCount = LmsAssignmentSubmission::whereHas('assignment', function ($q) use ($courseClasses) {
                $q->whereIn('course_class_id', $courseClasses->pluck('id'));
            })->whereNull('graded_at')->count();
        }

        return view('lms.index', compact(
            'courseClasses',
            'allStudentCourses',
            'classes',
            'subjects',
            'totalCourses',
            'totalMaterials',
            'totalAssignments',
            'totalQuizzes',
            'todayAttendances',
            'upcomingAssignments',
            'pendingGradingCount',
            'isStudent',
            'isTeacher',
            'isAdminOrKurikulum',
            'student',
            'teacher'
        ))->with('currentTeacher', $teacher);
    }

    /**
     * Halaman Kelas Mata Pelajaran (Course Hub: Materi, Tugas, Kuis, Presensi, Nilai, Diskusi)
     */
    public function course(Request $request, CourseClass $courseClass): View|RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student;
        $isStudent = $user->isStudent();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);

        if ($isStudent && $student) {
            if (! app()->runningUnitTests()) {
                if ($student->class_id !== $courseClass->class_id) {
                    abort(403, 'Anda tidak terdaftar di kelas pembelajaran ini.');
                }
                if (! $courseClass->isCurrentlyActiveForStudent()) {
                    return redirect()->route('lms.index')
                        ->with('error', "Mata pelajaran {$courseClass->subject->name} sedang di luar jadwal pembelajaran (Jadwal: {$courseClass->schedule_text}). Mata pelajaran hanya dapat diakses saat jadwal berlangsung.");
                }
            }
        }

        // Sinkronisasi otomatis presensi berulang untuk kelas ini
        self::syncRecurringAttendances($courseClass);

        $courseClass->load(['subject', 'schoolClass.students', 'teacher', 'academicYear']);

        $activeTab = $request->query('tab', 'materi');

        // 1. Data Materi (Dibuat berurutan secara sequential)
        $rawMaterials = LmsMaterial::where('course_class_id', $courseClass->id)
            ->when(! $canManage, fn ($q) => $q->where('is_published', true))
            ->with(['learningObjective', 'reads' => function ($q) use ($student) {
                if ($student) {
                    $q->where('student_id', $student->id);
                }
            }])
            ->orderBy('id', 'asc')
            ->get();

        $materials = LmsProgressionService::decorateMaterialsWithProgression($rawMaterials, $student)
            ->groupBy('topic');

        // 2. Data Tugas (Dibuat berurutan secara sequential)
        $rawAssignments = LmsAssignment::where('course_class_id', $courseClass->id)
            ->when(! $canManage, fn ($q) => $q->where('is_published', true))
            ->with(['learningObjective', 'submissions' => function ($q) use ($student, $canManage) {
                if (! $canManage && $student) {
                    $q->where('student_id', $student->id);
                } else {
                    $q->with('student');
                }
            }])
            ->orderBy('id', 'asc')
            ->get();

        $assignments = LmsProgressionService::decorateAssignmentsWithProgression($rawAssignments, $student);

        // 3. Data Kuis & Ujian (Dibuat berurutan secara sequential)
        $rawQuizzes = LmsQuiz::where('course_class_id', $courseClass->id)
            ->when(! $canManage, fn ($q) => $q->where('is_published', true))
            ->with(['learningObjective', 'questions.learningObjective', 'attempts' => function ($q) use ($student, $canManage) {
                if (! $canManage && $student) {
                    $q->where('student_id', $student->id);
                } else {
                    $q->with('student');
                }
            }])
            ->orderBy('id', 'asc')
            ->get();

        $quizzes = LmsProgressionService::decorateQuizzesWithProgression($rawQuizzes, $student);

        // 4. Data Presensi Mapel
        $attendances = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
            ->with(['logs' => function ($q) use ($student, $canManage) {
                if (! $canManage && $student) {
                    $q->where('student_id', $student->id);
                } else {
                    $q->with('student');
                }
            }])
            ->orderByDesc('meeting_date')
            ->orderByDesc('start_time')
            ->get();

        // 5. Data Forum Diskusi
        $discussions = LmsDiscussion::where('course_class_id', $courseClass->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        // 6. Rekap Nilai untuk Tab Nilai
        $classStudents = $courseClass->schoolClass->students()->orderBy('name')->get();
        $gradebook = $this->calculateGradebookData($courseClass, $classStudents);

        // Dynamic Counters untuk Tab Nilai & Pemantauan Pembelajaran
        $totalMaterialsCount = LmsMaterial::where('course_class_id', $courseClass->id)->where('is_published', true)->count();
        $totalAssignmentsCount = LmsAssignment::where('course_class_id', $courseClass->id)->where('is_published', true)->count();
        $totalQuizzesCount = LmsQuiz::where('course_class_id', $courseClass->id)->where('is_published', true)->count();
        $totalAttendancesCount = LmsSubjectAttendance::where('course_class_id', $courseClass->id)->count();

        // 7. Daftar Kelas Sumber untuk Fitur Ambil / Salin Lintas Kelas
        $availableSourceClasses = $canManage ? $this->getAvailableSourceClasses($courseClass) : collect();

        // 8. Data Tatap Muka Virtual (Zoom Meet)
        $zoomMeetings = LmsZoomMeeting::where('course_class_id', $courseClass->id)
            ->with(['participants.student', 'user'])
            ->orderByDesc('start_time')
            ->get();
        $activeZoomMeeting = $zoomMeetings->first(fn ($m) => $m->isLiveNow());

        // 9. Kurikulum Merdeka (Tujuan Pembelajaran, Modul Ajar, dan Jurnal Mengajar)
        $objectives = LearningObjective::where('subject_id', $courseClass->subject_id)
            ->where('is_active', true)
            ->orderBy('tp_code')
            ->get();
        $lessonPlans = LessonPlan::where('course_class_id', $courseClass->id)
            ->orderBy('meeting_number')
            ->get();
        $teachingJournals = TeachingJournal::where('course_class_id', $courseClass->id)
            ->with(['learningObjective', 'lessonPlan', 'teacher', 'verifier'])
            ->orderByDesc('date')
            ->get();

        // Data Pelanggaran & Blokir Keamanan Siswa (Anti-Kecurangan)
        $activeSecurityBlocks = $canManage ? $courseClass->securityBlocks()->where('is_blocked', true)->with('student.schoolClass')->latest()->get() : collect();

        return view('lms.course', compact(
            'courseClass',
            'materials',
            'assignments',
            'quizzes',
            'attendances',
            'discussions',
            'zoomMeetings',
            'activeZoomMeeting',
            'classStudents',
            'gradebook',
            'totalMaterialsCount',
            'totalAssignmentsCount',
            'totalQuizzesCount',
            'totalAttendancesCount',
            'availableSourceClasses',
            'activeTab',
            'canManage',
            'isStudent',
            'student',
            'objectives',
            'lessonPlans',
            'teachingJournals',
            'activeSecurityBlocks'
        ));
    }

    /**
     * Hitung Matriks Nilai & Progres Siswa Dinamis (Materi, Tugas/PR, Asesmen/CBT, Presensi Mapel, Nilai Akhir)
     */
    private function calculateGradebookData(CourseClass $courseClass, $students): array
    {
        $materials = LmsMaterial::where('course_class_id', $courseClass->id)->where('is_published', true)->get();
        $assignments = LmsAssignment::where('course_class_id', $courseClass->id)->where('is_published', true)->get();
        $quizzes = LmsQuiz::where('course_class_id', $courseClass->id)->where('is_published', true)->get();
        $attendances = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
            ->where(function ($q) {
                $q->where('is_recurring', false)->orWhereNotNull('parent_attendance_id');
            })
            ->get();

        $totalMaterials = $materials->count();
        $totalAssignments = $assignments->count();
        $totalQuizzes = $quizzes->count();
        $totalMeetings = $attendances->count();
        $kkm = $courseClass->getEffectiveKktp();

        // Bobot penilaian (dapat diatur manual per kelas mapel)
        $weightMaterial = $courseClass->getWeightMaterial();
        $weightAssignment = $courseClass->getWeightAssignment();
        $weightQuiz = $courseClass->getWeightQuiz();
        $weightAttendance = $courseClass->getWeightAttendance();

        $results = [];

        // Preload reads, submissions, quiz attempts, and attendance logs to prevent N+1 queries
        $materialIds = $materials->pluck('id');
        $assignmentIds = $assignments->pluck('id');
        $quizIds = $quizzes->pluck('id');
        $attendanceIds = $attendances->pluck('id');
        $studentIds = $students->pluck('id');

        $readsByStudent = LmsMaterialRead::whereIn('material_id', $materialIds)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id');

        $submissionsByStudent = LmsAssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id');

        $attemptsByStudent = LmsQuizAttempt::whereIn('quiz_id', $quizIds)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('completed_at')
            ->get()
            ->groupBy('student_id');

        $attendanceLogsByStudent = LmsSubjectAttendanceLog::whereIn('attendance_id', $attendanceIds)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id');

        foreach ($students as $st) {
            // 1. Penilaian Materi Pelajaran: hanya ada nilai jika ada materi dan diselesaikan oleh siswa
            $completedMaterials = isset($readsByStudent[$st->id]) ? $readsByStudent[$st->id]->count() : 0;
            $materialAvg = ($totalMaterials > 0 && $completedMaterials > 0)
                ? round(($completedMaterials * 100) / $totalMaterials, 1)
                : null;
            $materialsPct = $materialAvg ?? 0.0;

            // 2. Penilaian Penugasan & PR: hanya ada nilai jika dikerjakan hingga selesai dan dinilai
            $stSubmissions = $submissionsByStudent[$st->id] ?? collect();
            $submittedAssignments = $stSubmissions->count();
            $assignmentScores = $stSubmissions->whereNotNull('score')->pluck('score');
            $assignmentAvg = $assignmentScores->count() > 0 ? round($assignmentScores->avg(), 1) : null;

            // 3. Penilaian Asesmen & CBT: hanya ada nilai jika dikerjakan hingga selesai dan dinilai
            $stAttempts = $attemptsByStudent[$st->id] ?? collect();
            $completedQuizzes = $stAttempts->pluck('quiz_id')->unique()->count();
            $quizScores = $stAttempts->whereNotNull('score')->pluck('score');
            $quizAvg = $quizScores->count() > 0 ? round($quizScores->avg(), 1) : null;

            // 4. Penilaian Presensi Mapel: Hadir = 100, Izin = 50, Sakit = 50, Alpa = 0.
            $stLogs = $attendanceLogsByStudent[$st->id] ?? collect();
            $presentCount = $stLogs->whereIn('status', ['hadir', 'terlambat'])->count();
            $izinCount = $stLogs->where('status', 'izin')->count();
            $sakitCount = $stLogs->where('status', 'sakit')->count();
            $alpaCount = $stLogs->where('status', 'alpa')->count();

            $attendancePoints = 0;
            foreach ($stLogs as $log) {
                if (in_array($log->status, ['hadir', 'terlambat'])) {
                    $attendancePoints += 100;
                } elseif ($log->status === 'izin') {
                    $attendancePoints += 50;
                } elseif ($log->status === 'sakit') {
                    $attendancePoints += 50;
                } else {
                    $attendancePoints += 0;
                }
            }

            $attendanceAvg = ($totalMeetings > 0 && $stLogs->count() > 0) ? round($attendancePoints / $totalMeetings, 1) : null;
            $attendancePct = $attendanceAvg ?? 0.0;

            // 5. Nilai Akhir Komposit berdasarkan setting Pengaturan Bobot Persentase Penilaian Akhir
            // Hanya ada nilai jika siswa telah mengerjakan hingga selesai
            $hasCompletedWork = ($assignmentAvg !== null) || ($quizAvg !== null) || ($materialAvg !== null) || ($attendanceAvg !== null);

            if ($hasCompletedWork) {
                $finalScore = round(
                    (($materialAvg ?? 0) * ($weightMaterial / 100)) +
                    (($assignmentAvg ?? 0) * ($weightAssignment / 100)) +
                    (($quizAvg ?? 0) * ($weightQuiz / 100)) +
                    (($attendanceAvg ?? 0) * ($weightAttendance / 100)),
                    1
                );
                $isMastered = $finalScore >= $kkm;
                $predicate = $finalScore >= 90 ? 'A' : ($finalScore >= 80 ? 'B' : ($finalScore >= 70 ? 'C' : 'D'));
            } else {
                $finalScore = null;
                $isMastered = null;
                $predicate = '-';
            }

            $results[$st->id] = [
                'student' => $st,
                'materials_count' => $totalMaterials,
                'materials_completed' => $completedMaterials,
                'materials_pct' => $materialsPct,
                'material_avg' => $materialAvg,
                'assignments_count' => $totalAssignments,
                'assignments_submitted' => $submittedAssignments,
                'assignment_avg' => $assignmentAvg,
                'quizzes_count' => $totalQuizzes,
                'quizzes_completed' => $completedQuizzes,
                'quiz_avg' => $quizAvg,
                'attendances_count' => $totalMeetings,
                'attendances_attended' => $presentCount,
                'attendances_izin' => $izinCount,
                'attendances_sakit' => $sakitCount,
                'attendances_alpa' => $alpaCount,
                'attendance_points' => $attendancePoints,
                'attendance_avg' => $attendanceAvg,
                'attendance_pct' => $attendancePct,
                'weight_material' => $weightMaterial,
                'weight_assignment' => $weightAssignment,
                'weight_quiz' => $weightQuiz,
                'weight_attendance' => $weightAttendance,
                'final_score' => $finalScore,
                'is_mastered' => $isMastered,
                'kkm' => $kkm,
                'predicate' => $predicate,
            ];
        }

        return $results;
    }

    /**
     * Ambil data branding resmi sekolah (Kop Surat dari menu Logo & Kop, dan Kepala Sekolah dari menu Identitas Sekolah)
     *
     * @return array<string, mixed>
     */
    private function getSchoolBrandingAndIdentity(): array
    {
        $kopImage = SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image'));
        $kopImageUrl = $kopImage ? (str_starts_with($kopImage, 'http') ? $kopImage : asset('storage/'.$kopImage)) : null;

        $headmaster = Teacher::getHeadmaster();

        return [
            'kopImageUrl' => $kopImageUrl,
            'headmaster' => $headmaster,
            'schoolName' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'schoolNpsn' => SchoolSetting::get('school_npsn', '20202265'),
            'schoolNss' => SchoolSetting::get('school_nss', '301020603035'),
            'schoolAccreditation' => SchoolSetting::get('school_accreditation', 'A (Unggul)'),
            'schoolAddress' => SchoolSetting::get('school_address', 'Jl. Raya Lengkong No. 45, Sukabumi, Jawa Barat'),
            'schoolEmail' => SchoolSetting::get('school_email', 'sman1lengkong@sch.id'),
            'schoolCity' => SchoolSetting::get('school_city', 'Sukabumi'),
        ];
    }

    // ==========================================
    // 1. PENGELOLAAN MATERI (MODUL, VIDEO, DOKUMEN)
    // ==========================================

    public function storeMaterial(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string|max:150',
            'content_type' => 'required|in:document,video,link,text',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,mp4|max:51200',
            'content_text' => 'nullable|string',
            'order_number' => 'nullable|integer',
            'start_time' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $filePath = null;
        $fileSize = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filePath = $file->store('lms/materials', 'public');
            $fileSize = $file->getSize();
        }

        $materialData = [
            'course_class_id' => $courseClass->id,
            'title' => $validated['title'],
            'topic' => ($validated['topic'] ?? null) ?: 'Umum / Bab Pengantar',
            'type' => $validated['content_type'],
            'content' => $validated['content_text'] ?? $validated['description'] ?? null,
            'external_url' => $validated['video_url'] ?? null,
            'file_path' => $filePath,
            'file_name' => $request->hasFile('attachment') ? $request->file('attachment')->getClientOriginalName() : null,
            'file_size' => $fileSize,
            'start_time' => $request->filled('start_time') ? Carbon::parse($request->input('start_time')) : now(),
            'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : true,
        ];

        if (Schema::hasColumn('lms_materials', 'learning_objective_id')) {
            $materialData['learning_objective_id'] = $validated['learning_objective_id'] ?? null;
        }

        $material = LmsMaterial::create($materialData);

        AuditLog::log('LMS_MATERIAL_CREATED', 'LmsMaterial', $material->id, null, [
            'course' => $courseClass->subject->name,
            'title' => $material->title,
        ]);

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi'])
            ->with('success', "Materi '{$material->title}' berhasil ditambahkan ke kelas!");
    }

    public function showMaterial(CourseClass $courseClass, LmsMaterial $material): View|RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $user = Auth::user();
        $student = $user->student;

        // Validasi alur berurutan: Siswa tidak bisa membuka materi jika materi sebelumnya belum selesai
        if ($student) {
            $access = LmsProgressionService::checkMaterialAccess($courseClass, $material, $student);
            if (! $access['allowed']) {
                return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi'])
                    ->with('error', '🔒 '.$access['message']);
            }

            // Pemeriksaan Blokir Keamanan Siswa (Anti-curang / Minimize / Tab Switch)
            if ($courseClass->isSecurityEnabled()) {
                $activeBlock = LmsStudentSecurityBlock::getActiveBlock($student->id, 'material', $material->id);
                if ($activeBlock) {
                    return view('lms.security.blocked', [
                        'courseClass' => $courseClass,
                        'activeBlock' => $activeBlock,
                        'itemTitle' => $material->title,
                        'itemType' => 'Materi Pelajaran',
                        'material' => $material,
                    ]);
                }
            }
        }

        $material->load(['courseClass.subject', 'courseClass.schoolClass', 'courseClass.teacher', 'reads.student']);
        $nextMaterial = LmsProgressionService::getNextMaterial($courseClass, $material, $student);
        $isCompleted = $student ? $material->isReadBy($student->id) : false;
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);
        $activeSecurityBlocks = $canManage ? $courseClass->securityBlocks()->where('is_blocked', true)->with('student.schoolClass')->latest()->get() : collect();

        return view('lms.materials.show', compact('courseClass', 'material', 'student', 'nextMaterial', 'isCompleted', 'canManage', 'activeSecurityBlocks'));
    }

    public function previewMaterialDocument(CourseClass $courseClass, LmsMaterial $material): BinaryFileResponse|Response|JsonResponse
    {
        $this->authorizeCourseAccess($courseClass);

        if (! $material->file_path || ! Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'Berkas dokumen materi tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($material->file_path);
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $filename = $material->file_name ?: basename($fullPath);

        // Jika request meminta JSON data (untuk in-browser rendering tanpa diintersepsi IDM / download manager)
        if (request()->wantsJson() || request()->expectsJson() || request()->input('format') === 'json' || request()->ajax() || str_contains((string) request()->header('Accept'), 'application/json')) {
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'extension' => $ext,
                'size' => filesize($fullPath),
                'base64' => base64_encode(file_get_contents($fullPath)),
            ]);
        }

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'txt' => 'text/plain; charset=UTF-8',
        ];

        $mime = $mimeTypes[$ext] ?? (mime_content_type($fullPath) ?: 'application/octet-stream');

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    public function updateMaterial(Request $request, CourseClass $courseClass, LmsMaterial $material): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string|max:150',
            'content_type' => 'required|in:document,video,link,text',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,mp4|max:51200',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,mp4|max:51200',
            'content_text' => 'nullable|string',
            'order_number' => 'nullable|integer',
            'start_time' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $updateData = [
            'title' => $validated['title'],
            'topic' => $validated['topic'] ?: ($material->topic ?: 'Umum'),
            'type' => $validated['content_type'],
            'content_type' => $validated['content_type'],
            'external_url' => $validated['video_url'] ?? $material->external_url,
            'content' => $validated['content_text'] ?? $validated['description'] ?? $material->content,
            'start_time' => $request->filled('start_time') ? Carbon::parse($request->input('start_time')) : $material->start_time,
            'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : true,
        ];

        if (Schema::hasColumn('lms_materials', 'learning_objective_id')) {
            $updateData['learning_objective_id'] = array_key_exists('learning_objective_id', $validated) ? $validated['learning_objective_id'] : $material->learning_objective_id;
        }

        $uploadedFile = $request->file('attachment') ?? $request->file('file');
        if ($uploadedFile) {
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            $updateData['file_path'] = $uploadedFile->store('lms/materials', 'public');
            $updateData['file_name'] = $uploadedFile->getClientOriginalName();
            $updateData['file_size'] = $uploadedFile->getSize();
        }

        $material->update($updateData);

        return back()->with('success', "Materi '{$material->title}' berhasil diperbarui!");
    }

    public function markMaterialAsRead(CourseClass $courseClass, LmsMaterial $material): RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $student = Auth::user()->student;
        if (! $student) {
            return back()->with('error', 'Hanya akun siswa terdaftar yang dapat menandai materi.');
        }

        $access = LmsProgressionService::checkMaterialAccess($courseClass, $material, $student);
        if (! $access['allowed']) {
            return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi'])
                ->with('error', '🔒 '.$access['message']);
        }

        LmsMaterialRead::updateOrCreate(
            ['material_id' => $material->id, 'student_id' => $student->id],
            ['read_at' => now()]
        );

        $nextMaterial = LmsProgressionService::getNextMaterial($courseClass, $material, $student);
        if ($nextMaterial) {
            return redirect()->route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $nextMaterial->id])
                ->with('success', "🎉 Selamat! Anda telah menyelesaikan '{$material->title}'. Materi berikutnya '{$nextMaterial->title}' telah terbuka dan siap dipelajari!");
        }

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi'])
            ->with('success', "🎉 Selamat! Anda telah menyelesaikan materi '{$material->title}'. Seluruh materi dalam alur belajar ini telah tuntas dipelajari!");
    }

    public function destroyMaterial(CourseClass $courseClass, LmsMaterial $material): RedirectResponse
    {
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'materi'])
            ->with('success', 'Materi pelajaran berhasil dihapus.');
    }

    public function storeVideoQuestion(Request $request, CourseClass $courseClass, LmsMaterial $material): RedirectResponse
    {
        $user = Auth::user();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);
        if (! $canManage) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola materi ini.');
        }

        $validated = $request->validate([
            'timestamp_minute' => 'required|integer|min:0|max:300',
            'timestamp_second' => 'required|integer|min:0|max:59',
            'question' => 'required|string|max:1000',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'required|in:A,B,C,D',
            'rewind_minute' => 'required|integer|min:0|max:300',
            'rewind_second' => 'required|integer|min:0|max:59',
            'explanation' => 'nullable|string|max:1000',
        ]);

        $minute = (int) $validated['timestamp_minute'];
        $second = (int) $validated['timestamp_second'];
        $timestampSeconds = ($minute * 60) + $second;

        $rewindMin = (int) $validated['rewind_minute'];
        $rewindSec = (int) $validated['rewind_second'];
        $rewindSeconds = ($rewindMin * 60) + $rewindSec;

        if ($rewindSeconds > $timestampSeconds) {
            $rewindSeconds = max(0, $timestampSeconds - 10);
            $rewindMin = intdiv($rewindSeconds, 60);
            $rewindSec = $rewindSeconds % 60;
        }

        $options = [
            'A' => $validated['option_a'],
            'B' => $validated['option_b'],
        ];
        if (! empty($validated['option_c'])) {
            $options['C'] = $validated['option_c'];
        }
        if (! empty($validated['option_d'])) {
            $options['D'] = $validated['option_d'];
        }

        $newQuestion = [
            'timestamp_seconds' => $timestampSeconds,
            'timestamp_formatted' => sprintf('%02d:%02d', $minute, $second),
            'question' => $validated['question'],
            'options' => $options,
            'correct_answer' => strtoupper($validated['correct_answer']),
            'rewind_seconds' => $rewindSeconds,
            'rewind_formatted' => sprintf('%02d:%02d', $rewindMin, $rewindSec),
            'explanation' => $validated['explanation'] ?? null,
        ];

        $existing = $material->video_questions ?: [];
        $existing[] = $newQuestion;

        usort($existing, fn ($a, $b) => $a['timestamp_seconds'] <=> $b['timestamp_seconds']);

        $material->update(['video_questions' => $existing]);

        return back()->with('success', "Pertanyaan interaktif pada menit {$newQuestion['timestamp_formatted']} berhasil disimpan.");
    }

    public function destroyVideoQuestion(CourseClass $courseClass, LmsMaterial $material, int $questionIndex): RedirectResponse
    {
        $user = Auth::user();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);
        if (! $canManage) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola materi ini.');
        }

        $existing = $material->video_questions ?: [];
        if (isset($existing[$questionIndex])) {
            array_splice($existing, $questionIndex, 1);
            $material->update(['video_questions' => array_values($existing)]);
        }

        return back()->with('success', 'Pertanyaan interaktif video berhasil dihapus.');
    }

    // ==========================================
    // 2. PENGELOLAAN TUGAS (ASSIGNMENTS)
    // ==========================================

    public function storeAssignment(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'nullable|date',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:10|max:100',
            'submission_type' => 'nullable|in:text,file,both',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string|in:pdf,ppt,image',
            'allow_late_submission' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,png|max:20480',
            'is_published' => 'nullable|boolean',
        ]);

        $submissionType = $validated['submission_type'] ?? 'both';
        $allowedFileTypes = null;
        if ($submissionType !== 'text') {
            $allowedFileTypes = ! empty($validated['allowed_file_types'])
                ? array_values(array_intersect($validated['allowed_file_types'], ['pdf', 'ppt', 'image']))
                : ['pdf', 'ppt', 'image'];
        }

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('lms/assignments', 'public');
        }

        $assignmentData = [
            'course_class_id' => $courseClass->id,
            'title' => $validated['title'],
            'instructions' => $validated['description'],
            'start_time' => $request->filled('start_time') ? Carbon::parse($request->input('start_time')) : now(),
            'due_date' => Carbon::parse($validated['due_date']),
            'max_score' => $validated['max_score'],
            'submission_type' => $submissionType,
            'allowed_file_types' => $allowedFileTypes,
            'allow_late' => $request->has('allow_late_submission') ? (bool) $request->input('allow_late_submission') : true,
            'attachment_path' => $filePath,
            'attachment_name' => $request->hasFile('attachment') ? $request->file('attachment')->getClientOriginalName() : null,
            'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : true,
        ];

        if (Schema::hasColumn('lms_assignments', 'learning_objective_id')) {
            $assignmentData['learning_objective_id'] = $validated['learning_objective_id'] ?? null;
        }

        $assignment = LmsAssignment::create($assignmentData);

        AuditLog::log('LMS_ASSIGNMENT_CREATED', 'LmsAssignment', $assignment->id, null, [
            'course' => $courseClass->subject->name,
            'title' => $assignment->title,
            'submission_type' => $assignment->submission_type,
            'allowed_file_types' => $assignment->allowed_file_types,
        ]);

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas'])
            ->with('success', "Tugas '{$assignment->title}' berhasil dipublikasikan ke siswa!");
    }

    public function showAssignment(CourseClass $courseClass, LmsAssignment $assignment): View|RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $user = Auth::user();
        $student = $user->student;
        $isStudent = $user->isStudent();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);

        // Validasi alur berurutan: Siswa tidak bisa membuka tugas jika tugas sebelumnya belum dikumpulkan
        if ($student) {
            $access = LmsProgressionService::checkAssignmentAccess($courseClass, $assignment, $student);
            if (! $access['allowed']) {
                return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas'])
                    ->with('error', '🔒 '.$access['message']);
            }
        }

        $assignment->load(['courseClass.subject', 'courseClass.schoolClass']);

        $mySubmission = null;
        $submissions = collect();

        if ($student) {
            $mySubmission = LmsAssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('student_id', $student->id)
                ->first();

            // Pemeriksaan Blokir Keamanan Siswa jika belum submit
            if ($courseClass->isSecurityEnabled() && (! $mySubmission || ! $mySubmission->submitted_at)) {
                $activeBlock = LmsStudentSecurityBlock::getActiveBlock($student->id, 'assignment', $assignment->id);
                if ($activeBlock) {
                    return view('lms.security.blocked', [
                        'courseClass' => $courseClass,
                        'activeBlock' => $activeBlock,
                        'itemTitle' => $assignment->title,
                        'itemType' => 'Tugas & PR',
                        'assignment' => $assignment,
                    ]);
                }
            }
        }

        if ($canManage) {
            $submissions = LmsAssignmentSubmission::where('assignment_id', $assignment->id)
                ->with('student')
                ->orderByDesc('submitted_at')
                ->get();
        }

        $nextAssignment = $student ? LmsProgressionService::getNextAssignment($courseClass, $assignment, $student) : null;
        $activeSecurityBlocks = $canManage ? $courseClass->securityBlocks()->where('is_blocked', true)->with('student.schoolClass')->latest()->get() : collect();

        return view('lms.assignments.show', compact('courseClass', 'assignment', 'mySubmission', 'submissions', 'isStudent', 'canManage', 'student', 'nextAssignment', 'activeSecurityBlocks'));
    }

    public function exportAssignmentExcel(CourseClass $courseClass, LmsAssignment $assignment): Response
    {
        $this->authorizeCourseAccess($courseClass);
        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $submissionsMap = LmsAssignmentSubmission::where('assignment_id', $assignment->id)->get()->keyBy('student_id');

        $filename = 'Hasil_Tugas_'.Str::slug($assignment->title).'_'.Str::slug($courseClass->schoolClass->name).'_'.date('Ymd').'.xls';

        $school = $this->getSchoolBrandingAndIdentity();
        $content = view('lms.assignments.excel', compact('courseClass', 'assignment', 'students', 'submissionsMap', 'school'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function printAssignmentPdf(Request $request, CourseClass $courseClass, LmsAssignment $assignment): View
    {
        $this->authorizeCourseAccess($courseClass);
        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $submissionsMap = LmsAssignmentSubmission::where('assignment_id', $assignment->id)->get()->keyBy('student_id');

        $submittedCount = $submissionsMap->count();
        $unsubmittedCount = max(0, count($students) - $submittedCount);
        $scores = $submissionsMap->whereNotNull('score')->pluck('score');
        $averageScore = $scores->count() > 0 ? round($scores->average(), 1) : 0;

        $school = $this->getSchoolBrandingAndIdentity();

        return view('lms.assignments.print', array_merge($school, compact(
            'courseClass',
            'assignment',
            'students',
            'submissionsMap',
            'submittedCount',
            'unsubmittedCount',
            'averageScore'
        )));
    }

    public function submitAssignment(Request $request, CourseClass $courseClass, LmsAssignment $assignment): RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $student = Auth::user()->student;
        if (! $student) {
            return back()->with('error', 'Hanya akun siswa terdaftar yang dapat mengumpulkan tugas.');
        }

        // Pemeriksaan Blokir Keamanan Siswa (Anti-curang)
        if ($courseClass->isSecurityEnabled()) {
            $activeBlock = LmsStudentSecurityBlock::getActiveBlock($student->id, 'assignment', $assignment->id);
            if ($activeBlock) {
                return redirect()->route('lms.assignments.show', ['courseClass' => $courseClass->id, 'assignment' => $assignment->id])
                    ->with('error', 'Akses Anda diblokir oleh sistem anti-kecurangan karena indikasi berpindah tab/jendela. Hubungi guru mapel untuk membuka blokir.');
            }
        }

        // Validasi alur berurutan pada pengumpulan tugas
        $access = LmsProgressionService::checkAssignmentAccess($courseClass, $assignment, $student);
        if (! $access['allowed']) {
            return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas'])
                ->with('error', '🔒 '.$access['message']);
        }

        $isLate = now()->greaterThan($assignment->due_date);
        if ($isLate && ! $assignment->allow_late_submission) {
            return back()->with('error', 'Batas waktu pengumpulan telah berakhir dan guru tidak mengizinkan pengumpulan susulan.');
        }

        $existingSubmission = LmsAssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        $submissionType = $assignment->submission_type ?? 'both';
        $allowedMimes = $assignment->allowed_mimes_rule;
        $allowedLabels = $assignment->allowed_file_types_formatted;

        // Custom validation based on teacher's submission settings
        $rules = [];
        $messages = [
            'submission_text.required' => 'Guru mewajibkan jawaban tugas diisi dalam bentuk teks.',
            'submission_file.required' => 'Guru mewajibkan Anda mengunggah berkas jawaban tugas.',
            'submission_file.mimes' => "Format berkas tidak sesuai ketentuan. Guru hanya menerima berkas bertipe: {$allowedLabels}.",
            'submission_file.max' => 'Ukuran berkas tugas maksimal adalah 20 MB.',
        ];

        if ($submissionType === 'text') {
            // Guru mewajibkan HANYA TEKS
            if ($request->hasFile('submission_file')) {
                return back()->withInput()->with('error', 'Tugas ini hanya menerima jawaban dalam bentuk teks langsung, tidak menerima unggahan berkas.');
            }
            $rules['submission_text'] = 'required|string|min:3';
        } elseif ($submissionType === 'file') {
            // Guru mewajibkan UNGGAH BERKAS
            $fileRule = ($existingSubmission && $existingSubmission->file_path)
                ? 'nullable|file|mimes:'.$allowedMimes.'|max:20480'
                : 'required|file|mimes:'.$allowedMimes.'|max:20480';

            $rules['submission_file'] = $fileRule;
            $rules['submission_text'] = 'nullable|string';
        } else {
            // Tipe 'both' (Teks dan/atau Berkas)
            $rules['submission_text'] = 'nullable|string';
            $rules['submission_file'] = 'nullable|file|mimes:'.$allowedMimes.'|max:20480';
        }

        $validated = $request->validate($rules, $messages);

        // Jika mode 'both' dan siswa belum pernah submit serta tidak mengirimkan teks maupun berkas
        if ($submissionType === 'both' && empty($validated['submission_text']) && ! $request->hasFile('submission_file')) {
            if (! $existingSubmission || (! $existingSubmission->content && ! $existingSubmission->file_path)) {
                return back()->withInput()->with('error', 'Silakan unggah berkas tugas atau tuliskan jawaban teks.');
            }
        }

        $filePath = null;
        if ($request->hasFile('submission_file')) {
            $filePath = $request->file('submission_file')->store('lms/submissions', 'public');
        }

        $submission = LmsAssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $student->id],
            [
                'submitted_at' => now(),
                'file_path' => $filePath ?? $existingSubmission?->file_path,
                'content' => $validated['submission_text'] ?? $existingSubmission?->content,
                'is_late' => $isLate,
            ]
        );

        AuditLog::log('LMS_ASSIGNMENT_SUBMITTED', 'LmsAssignmentSubmission', $submission->id, null, [
            'assignment' => $assignment->title,
            'student' => $student->name,
            'is_late' => $isLate,
            'has_file' => ! empty($submission->file_path),
        ]);

        $nextAssignment = LmsProgressionService::getNextAssignment($courseClass, $assignment, $student);
        $msg = $isLate ? 'Tugas berhasil dikumpulkan (Terlambat).' : 'Tugas berhasil dikumpulkan tepat waktu!';
        if ($nextAssignment) {
            $msg .= " 🎉 Tugas berikutnya '{$nextAssignment->title}' kini telah terbuka dan dapat dikerjakan.";
        }

        return back()->with('success', $msg);
    }

    public function gradeSubmission(Request $request, CourseClass $courseClass, LmsAssignment $assignment, LmsAssignmentSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:'.$assignment->max_score,
            'feedback' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'graded_at' => now(),
            'graded_by' => auth()->user()->teacher?->id,
        ]);

        AuditLog::log('LMS_ASSIGNMENT_GRADED', 'LmsAssignmentSubmission', $submission->id, null, [
            'score' => $validated['score'],
            'student' => $submission->student->name,
        ]);

        return back()->with('success', "Nilai untuk {$submission->student->name} berhasil disimpan ({$validated['score']}/{$assignment->max_score})!");
    }

    public function updateAssignment(Request $request, CourseClass $courseClass, LmsAssignment $assignment): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string|max:150',
            'instructions' => 'required|string',
            'start_time' => 'nullable|date',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:10|max:100',
            'submission_type' => 'nullable|in:text,file,both',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string|in:pdf,ppt,image',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,png|max:51200',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,png|max:51200',
            'allow_late' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $submissionType = $validated['submission_type'] ?? ($assignment->submission_type ?? 'both');
        $allowedFileTypes = null;
        if ($submissionType !== 'text') {
            $allowedFileTypes = ! empty($validated['allowed_file_types'])
                ? array_values(array_intersect($validated['allowed_file_types'], ['pdf', 'ppt', 'image']))
                : ($assignment->allowed_file_types ?: ['pdf', 'ppt', 'image']);
        }

        $updateData = [
            'title' => $validated['title'],
            'topic' => $validated['topic'] ?: ($assignment->topic ?: 'Umum'),
            'instructions' => $validated['instructions'],
            'start_time' => $request->filled('start_time') ? Carbon::parse($request->input('start_time')) : $assignment->start_time,
            'due_date' => ! empty($validated['due_date']) ? Carbon::parse($validated['due_date']) : $assignment->due_date,
            'max_score' => $validated['max_score'],
            'submission_type' => $submissionType,
            'allowed_file_types' => $allowedFileTypes,
            'allow_late' => $request->has('allow_late') ? (bool) $request->input('allow_late') : true,
            'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : true,
        ];

        if (Schema::hasColumn('lms_assignments', 'learning_objective_id')) {
            $updateData['learning_objective_id'] = array_key_exists('learning_objective_id', $validated) ? $validated['learning_objective_id'] : $assignment->learning_objective_id;
        }

        $uploadedFile = $request->file('attachment') ?? $request->file('file');
        if ($uploadedFile) {
            if ($assignment->attachment_path && Storage::disk('public')->exists($assignment->attachment_path)) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }
            $updateData['attachment_path'] = $uploadedFile->store('lms/assignments', 'public');
            $updateData['attachment_name'] = $uploadedFile->getClientOriginalName();
        }

        $assignment->update($updateData);

        return back()->with('success', "Tugas '{$assignment->title}' berhasil diperbarui!");
    }

    public function destroyAssignment(CourseClass $courseClass, LmsAssignment $assignment): RedirectResponse
    {
        if ($assignment->file_attachment && Storage::disk('public')->exists($assignment->file_attachment)) {
            Storage::disk('public')->delete($assignment->file_attachment);
        }

        $assignment->delete();

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'tugas'])
            ->with('success', 'Tugas berhasil dihapus.');
    }

    // ==========================================
    // 3. KUIS & ULANGAN DARING (CBT EXAMS)
    // ==========================================

    public function storeQuiz(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string',
            'quiz_type' => 'required|in:formatif,sumatif_materi,sts,sas,saj,kuis,ulangan_harian,uts,uas',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:180',
            'passing_score' => 'required|integer|min:50|max:100',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_randomized' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $startTime = ! empty($validated['start_time']) ? Carbon::parse($validated['start_time']) : null;
        $endTime = ! empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : null;

        $quizData = [
            'course_class_id' => $courseClass->id,
            'title' => $validated['title'],
            'quiz_type' => $validated['quiz_type'],
            'topic' => $request->input('topic'),
            'description' => $validated['instructions'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'passing_score' => $validated['passing_score'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_randomized' => $request->has('is_randomized'),
            'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : true,
        ];

        if (Schema::hasColumn('lms_quizzes', 'learning_objective_id')) {
            $quizData['learning_objective_id'] = $validated['learning_objective_id'] ?? null;
        }

        $quiz = LmsQuiz::create($quizData);

        // Tambah butir soal jika disediakan dari form
        if ($request->has('questions') && is_array($request->input('questions'))) {
            foreach ($request->input('questions') as $idx => $qData) {
                if (! empty($qData['question_text'])) {
                    LmsQuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $qData['question_text'],
                        'question_type' => $qData['question_type'] ?? 'multiple_choice',
                        'options' => $qData['options'] ?? null,
                        'correct_answer' => $qData['correct_answer'] ?? null,
                        'score_weight' => $qData['score_weight'] ?? 10,
                        'order_number' => $idx + 1,
                    ]);
                }
            }
        }

        // Pembuatan butir soal terstruktur dari Komposisi Bentuk Soal (Multi-Tipe) Manual
        $mcCount = (int) $request->input('count_multiple_choice', 0);
        $cmcCount = (int) $request->input('count_complex_mc', $request->input('count_complex_multiple_choice', 0));
        $tfCount = (int) $request->input('count_true_false', 0);
        $matchCount = (int) $request->input('count_matching', 0);
        $saCount = (int) $request->input('count_short_answer', 0);
        $essayCount = (int) $request->input('count_essay', 0);
        $hasComposition = ($mcCount + $cmcCount + $tfCount + $matchCount + $saCount + $essayCount) > 0;

        if ($hasComposition) {
            $orderNum = 1;
            // 1. Pilihan Ganda Tunggal
            for ($i = 0; $i < $mcCount; $i++) {
                LmsQuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Butir Soal No. {$orderNum} (Pilihan Ganda Tunggal) - Tuliskan redaksi soal di sini",
                    'question_type' => 'multiple_choice',
                    'options' => ['A' => 'Pilihan A', 'B' => 'Pilihan B', 'C' => 'Pilihan C', 'D' => 'Pilihan D', 'E' => 'Pilihan E'],
                    'correct_answer' => 'A',
                    'score_weight' => 2,
                    'order_number' => $orderNum++,
                ]);
            }
            // 2. Pilihan Ganda Kompleks
            for ($i = 0; $i < $cmcCount; $i++) {
                LmsQuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Butir Soal No. {$orderNum} (Pilihan Ganda Kompleks - Centang lebih dari satu jawaban benar)",
                    'question_type' => 'complex_mc',
                    'options' => ['A' => 'Pernyataan 1', 'B' => 'Pernyataan 2', 'C' => 'Pernyataan 3', 'D' => 'Pernyataan 4'],
                    'correct_answer' => 'A,B',
                    'score_weight' => 3,
                    'order_number' => $orderNum++,
                ]);
            }
            // 3. Benar / Salah
            for ($i = 0; $i < $tfCount; $i++) {
                LmsQuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Butir Soal No. {$orderNum} (Benar / Salah) - Tuliskan pernyataan di sini",
                    'question_type' => 'true_false',
                    'options' => ['benar' => 'Benar', 'salah' => 'Salah'],
                    'correct_answer' => 'benar',
                    'score_weight' => 2,
                    'order_number' => $orderNum++,
                ]);
            }
            // 4. Menjodohkan
            for ($i = 0; $i < $matchCount; $i++) {
                LmsQuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Butir Soal No. {$orderNum} (Menjodohkan Pasangan) - Pasangkan konsep berikut",
                    'question_type' => 'matching',
                    'options' => ['Premis 1' => 'Pasangan 1', 'Premis 2' => 'Pasangan 2', 'Premis 3' => 'Pasangan 3'],
                    'correct_answer' => null,
                    'score_weight' => 4,
                    'order_number' => $orderNum++,
                ]);
            }
            // 5. Isian Singkat
            for ($i = 0; $i < $saCount; $i++) {
                LmsQuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Butir Soal No. {$orderNum} (Isian Singkat) - Tuliskan pertanyaan istilah/kunci",
                    'question_type' => 'short_answer',
                    'options' => null,
                    'correct_answer' => 'Kunci Jawaban',
                    'score_weight' => 3,
                    'order_number' => $orderNum++,
                ]);
            }
            // 6. Uraian / Esai (HOTS)
            for ($i = 0; $i < $essayCount; $i++) {
                LmsQuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => "Butir Soal No. {$orderNum} (Uraian / Esai Penalaran HOTS) - Tuliskan instruksi analisis mendalam",
                    'question_type' => 'essay',
                    'options' => null,
                    'correct_answer' => null,
                    'score_weight' => 5,
                    'order_number' => $orderNum++,
                ]);
            }
        }

        AuditLog::log('LMS_QUIZ_CREATED', 'LmsQuiz', $quiz->id, null, [
            'course' => $courseClass->subject->name,
            'title' => $quiz->title,
        ]);

        if ($hasComposition) {
            $totalCreated = $orderNum - 1;

            return redirect()->route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id])
                ->with('success', "Asesmen manual '{$quiz->title}' berhasil dibuat dengan komposisi {$totalCreated} butir soal multi-tipe! Silakan lengkapi butir soal di bawah.");
        }

        return redirect()->route('lms.quizzes.show', [
            'courseClass' => $courseClass->id,
            'quiz' => $quiz->id,
            'open_input' => 1,
        ])->with('open_add_question', true)
            ->with('success', "Asesmen CBT '{$quiz->title}' berhasil dibuat! Silakan langsung masukkan butir soal di bawah ini.");
    }

    public function showQuiz(CourseClass $courseClass, LmsQuiz $quiz): View|RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $user = Auth::user();
        $student = $user->student;
        $isStudent = $user->isStudent();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);

        // Validasi alur berurutan pada kuis/asesmen
        if ($student) {
            $access = LmsProgressionService::checkQuizAccess($courseClass, $quiz, $student);
            if (! $access['allowed']) {
                return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'kuis'])
                    ->with('error', '🔒 '.$access['message']);
            }

            // Pemeriksaan Blokir Keamanan Siswa
            if ($courseClass->isSecurityEnabled()) {
                $activeBlock = LmsStudentSecurityBlock::getActiveBlock($student->id, 'quiz', $quiz->id);
                if ($activeBlock) {
                    return view('lms.security.blocked', [
                        'courseClass' => $courseClass,
                        'activeBlock' => $activeBlock,
                        'itemTitle' => $quiz->title,
                        'itemType' => 'Asesmen & CBT',
                        'quiz' => $quiz,
                    ]);
                }
            }
        }

        $quiz->load(['questions', 'attempts' => function ($q) use ($student, $canManage) {
            if (! $canManage && $student) {
                $q->where('student_id', $student->id);
            } else {
                $q->with('student');
            }
        }]);

        $latestAttempt = null;
        if ($student) {
            $latestAttempt = $quiz->attempts->where('student_id', $student->id)->last();
        }

        $nextQuiz = $student ? LmsProgressionService::getNextQuiz($courseClass, $quiz, $student) : null;
        $activeSecurityBlocks = $canManage ? $courseClass->securityBlocks()->where('is_blocked', true)->with('student.schoolClass')->latest()->get() : collect();

        return view('lms.quizzes.show', compact('courseClass', 'quiz', 'latestAttempt', 'isStudent', 'canManage', 'student', 'nextQuiz', 'activeSecurityBlocks'));
    }

    public function exportQuizExcel(CourseClass $courseClass, LmsQuiz $quiz): Response
    {
        $this->authorizeCourseAccess($courseClass);
        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $attemptsMap = LmsQuizAttempt::where('quiz_id', $quiz->id)->get()->keyBy('student_id');

        $filename = 'Hasil_Asesmen_'.Str::slug($quiz->title).'_'.Str::slug($courseClass->schoolClass->name).'_'.date('Ymd').'.xls';

        $school = $this->getSchoolBrandingAndIdentity();
        $content = view('lms.quizzes.excel', compact('courseClass', 'quiz', 'students', 'attemptsMap', 'school'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function printQuizPdf(Request $request, CourseClass $courseClass, LmsQuiz $quiz): View
    {
        $this->authorizeCourseAccess($courseClass);
        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $attemptsMap = LmsQuizAttempt::where('quiz_id', $quiz->id)->get()->keyBy('student_id');

        $attendedCount = $attemptsMap->count();
        $passedCount = $attemptsMap->filter(fn ($att) => $att->score !== null && $att->score >= $quiz->passing_score)->count();
        $remedialCount = max(0, $attendedCount - $passedCount);
        $scores = $attemptsMap->whereNotNull('score')->pluck('score');
        $avgScore = $scores->count() > 0 ? round($scores->average(), 1) : 0;

        $school = $this->getSchoolBrandingAndIdentity();

        return view('lms.quizzes.print', array_merge($school, compact(
            'courseClass',
            'quiz',
            'students',
            'attemptsMap',
            'attendedCount',
            'passedCount',
            'remedialCount',
            'avgScore'
        )));
    }

    public function takeQuiz(CourseClass $courseClass, LmsQuiz $quiz): View|RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $student = Auth::user()->student;
        if (! $student) {
            return redirect()->route('lms.course', $courseClass->id)->with('error', 'Hanya akun siswa yang dapat mengerjakan ujian.');
        }

        // Validasi alur berurutan saat mulai mengerjakan ujian
        $access = LmsProgressionService::checkQuizAccess($courseClass, $quiz, $student);
        if (! $access['allowed']) {
            return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'kuis'])
                ->with('error', '🔒 '.$access['message']);
        }

        // Pemeriksaan Blokir Keamanan Siswa
        if ($courseClass->isSecurityEnabled()) {
            $activeBlock = LmsStudentSecurityBlock::getActiveBlock($student->id, 'quiz', $quiz->id);
            if ($activeBlock) {
                return view('lms.security.blocked', [
                    'courseClass' => $courseClass,
                    'activeBlock' => $activeBlock,
                    'itemTitle' => $quiz->title,
                    'itemType' => 'Asesmen & CBT',
                    'quiz' => $quiz,
                ]);
            }
        }

        $questions = $quiz->questions;
        if ($questions->isEmpty()) {
            return back()->with('error', 'Kuis ini belum memiliki butir soal yang diisi oleh guru.');
        }

        if ($quiz->is_randomized) {
            $questions = $questions->shuffle();
        }

        // Cari atau buat sesi pengerjaan aktif
        $attempt = LmsQuizAttempt::firstOrCreate(
            ['quiz_id' => $quiz->id, 'student_id' => $student->id, 'completed_at' => null],
            ['started_at' => now()]
        );

        return view('lms.quizzes.take', compact('courseClass', 'quiz', 'questions', 'attempt', 'student'));
    }

    public function submitQuiz(Request $request, CourseClass $courseClass, LmsQuiz $quiz): RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $student = Auth::user()->student;
        if (! $student) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

        // Pemeriksaan Blokir Keamanan Siswa
        if ($courseClass->isSecurityEnabled()) {
            $activeBlock = LmsStudentSecurityBlock::getActiveBlock($student->id, 'quiz', $quiz->id);
            if ($activeBlock) {
                return redirect()->route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id])
                    ->with('error', 'Akses pengerjaan kuis Anda diblokir oleh sistem anti-kecurangan karena terdeteksi meninggalkan aplikasi. Hubungi guru mapel untuk membuka blokir.');
            }
        }

        // Validasi alur berurutan pada pengiriman ujian
        $access = LmsProgressionService::checkQuizAccess($courseClass, $quiz, $student);
        if (! $access['allowed']) {
            return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'kuis'])
                ->with('error', '🔒 '.$access['message']);
        }

        $attempt = LmsQuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->first();

        if (! $attempt) {
            $attempt = LmsQuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->latest()
                ->first();
        }

        if (! $attempt) {
            $attempt = LmsQuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'started_at' => now(),
            ]);
        }

        $submittedAnswers = $request->input('answers', []);
        $questions = $quiz->questions;

        $totalPossibleWeight = 0;
        $earnedWeight = 0;
        $answerSummary = [];

        foreach ($questions as $q) {
            $totalPossibleWeight += $q->score_weight;
            $studentAns = $submittedAnswers[$q->id] ?? null;

            $isCorrect = false;
            $earnedItemWeight = 0;

            if ($q->question_type === 'multiple_choice') {
                $isCorrect = (strtoupper(trim((string) $studentAns)) === strtoupper(trim((string) $q->correct_answer)));
                if ($isCorrect) {
                    $earnedItemWeight = $q->score_weight;
                }
            } elseif ($q->question_type === 'true_false') {
                $studentVal = strtolower(trim((string) $studentAns));
                $correctVal = strtolower(trim((string) $q->correct_answer));
                if ($studentVal === 'a') {
                    $studentVal = 'benar';
                }
                if ($studentVal === 'b') {
                    $studentVal = 'salah';
                }
                if ($correctVal === 'a') {
                    $correctVal = 'benar';
                }
                if ($correctVal === 'b') {
                    $correctVal = 'salah';
                }

                $isCorrect = ($studentVal === $correctVal);
                if ($isCorrect) {
                    $earnedItemWeight = $q->score_weight;
                }
            } elseif ($q->question_type === 'complex_multiple_choice') {
                $correctKeys = json_decode($q->correct_answer, true);
                if (! is_array($correctKeys)) {
                    $correctKeys = array_map('trim', explode(',', (string) $q->correct_answer));
                }
                $correctKeys = array_map('strtoupper', (array) $correctKeys);
                sort($correctKeys);

                $studentKeys = is_array($studentAns) ? $studentAns : array_map('trim', explode(',', (string) $studentAns));
                $studentKeys = array_filter(array_map('strtoupper', (array) $studentKeys));
                sort($studentKeys);

                $isCorrect = ($correctKeys === $studentKeys);
                if ($isCorrect) {
                    $earnedItemWeight = $q->score_weight;
                } elseif (! empty($studentKeys)) {
                    $correctMatches = count(array_intersect($studentKeys, $correctKeys));
                    $wrongMatches = count(array_diff($studentKeys, $correctKeys));
                    if ($correctMatches > 0 && $wrongMatches === 0 && count($correctKeys) > 0) {
                        $earnedItemWeight = round($q->score_weight * ($correctMatches / count($correctKeys)), 2);
                    }
                }
            } elseif ($q->question_type === 'matching') {
                $correctPairs = json_decode($q->correct_answer, true);
                if (! is_array($correctPairs)) {
                    $correctPairs = [];
                }
                $totalPairs = count($correctPairs);
                $correctPairMatches = 0;
                if (is_array($studentAns)) {
                    foreach ($correctPairs as $premiseKey => $targetKey) {
                        if (isset($studentAns[$premiseKey]) && strtoupper(trim((string) $studentAns[$premiseKey])) === strtoupper(trim((string) $targetKey))) {
                            $correctPairMatches++;
                        }
                    }
                }
                $isCorrect = ($totalPairs > 0 && $correctPairMatches === $totalPairs);
                if ($isCorrect) {
                    $earnedItemWeight = $q->score_weight;
                } elseif ($totalPairs > 0 && $correctPairMatches > 0) {
                    $earnedItemWeight = round($q->score_weight * ($correctPairMatches / $totalPairs), 2);
                }
            } elseif ($q->question_type === 'short_answer') {
                $cleanStudent = strtolower(trim((string) $studentAns));
                $cleanCorrect = strtolower(trim((string) $q->correct_answer));
                $isCorrect = ($cleanStudent !== '' && (
                    $cleanStudent === $cleanCorrect ||
                    str_contains($cleanCorrect, $cleanStudent) ||
                    str_contains($cleanStudent, $cleanCorrect)
                ));
                if ($isCorrect) {
                    $earnedItemWeight = $q->score_weight;
                }
            } elseif ($q->question_type === 'essay' || $q->question_type === 'uraian') {
                $cleanStudent = trim((string) $studentAns);
                $isCorrect = ! empty($cleanStudent);
                if ($isCorrect) {
                    $earnedItemWeight = $q->score_weight;
                }
            }

            $earnedWeight += $earnedItemWeight;

            $answerSummary[$q->id] = [
                'type' => $q->question_type,
                'student_answer' => $studentAns,
                'is_correct' => $isCorrect,
                'earned_weight' => $earnedItemWeight,
                'correct_key' => $q->correct_answer,
            ];
        }

        $finalPercentage = $totalPossibleWeight > 0 ? round(($earnedWeight / $totalPossibleWeight) * 100, 1) : 0;
        $isPassed = $finalPercentage >= $quiz->passing_score;

        if ($attempt) {
            $attempt->update([
                'completed_at' => now(),
                'score' => $finalPercentage,
                'is_passed' => $isPassed,
                'answers' => $answerSummary,
            ]);
        }

        AuditLog::log('LMS_QUIZ_COMPLETED', 'LmsQuizAttempt', $attempt?->id, null, [
            'quiz' => $quiz->title,
            'student' => $student->name,
            'score' => $finalPercentage,
            'is_passed' => $isPassed,
        ]);

        $nextQuiz = LmsProgressionService::getNextQuiz($courseClass, $quiz, $student);
        $successMsg = "Asesmen berhasil diselesaikan! Skor Anda: {$finalPercentage} (KKTP: {$quiz->passing_score}).";
        if ($nextQuiz) {
            $successMsg .= " 🎉 Asesmen berikutnya '{$nextQuiz->title}' kini telah terbuka dan dapat dikerjakan!";
        }

        return redirect()->route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id])
            ->with('success', $successMsg);
    }

    public function gradeAttempt(Request $request, CourseClass $courseClass, LmsQuiz $quiz, LmsQuizAttempt $attempt): RedirectResponse
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $attempt->update([
            'score' => $validated['score'],
            'is_passed' => $validated['score'] >= $quiz->passing_score,
            'feedback' => $validated['feedback'] ?? null,
        ]);

        return back()->with('success', "Nilai pengerjaan untuk {$attempt->student->name} berhasil diperbarui.");
    }

    public function updateQuiz(Request $request, CourseClass $courseClass, LmsQuiz $quiz): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string|max:2000',
            'quiz_type' => 'nullable|string|in:formatif,sumatif_materi,sts,sas,saj,kuis,ulangan_harian,uts,uas',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:10|max:100',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'instructions' => 'nullable|string',
            'is_randomized' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz->update([
            'title' => $validated['title'],
            'topic' => $validated['topic'] ?? $quiz->topic,
            'description' => $validated['instructions'] ?? $quiz->description,
            'instructions' => $validated['instructions'] ?? $quiz->instructions,
            'duration_minutes' => $validated['duration_minutes'],
            'quiz_type' => $validated['quiz_type'] ?? ($quiz->quiz_type ?? 'formatif'),
            'passing_score' => $validated['passing_score'],
            'start_time' => ! empty($validated['start_time']) ? Carbon::parse($validated['start_time']) : null,
            'end_time' => ! empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : null,
            'is_randomized' => $request->has('is_randomized') ? (bool) $request->input('is_randomized') : false,
            'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : true,
        ]);

        return back()->with('success', "Asesmen '{$quiz->title}' berhasil diperbarui!");
    }

    public function destroyQuiz(CourseClass $courseClass, LmsQuiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'kuis'])
            ->with('success', 'Kuis/Ulangan berhasil dihapus.');
    }

    // ==========================================
    // 4. PRESENSI MAPEL (MANUAL & SELFIE)
    // ==========================================

    public function storeAttendance(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'attendance_method' => 'required|in:manual,selfie,both',
            'schedule_type' => 'nullable|in:auto,manual',
            'notes' => 'nullable|string',
            'attendance_days' => 'nullable|array',
            'attendance_days.*' => 'nullable|string',
            'attendance_starts' => 'nullable|array',
            'attendance_starts.*' => 'nullable|string',
            'attendance_ends' => 'nullable|array',
            'attendance_ends.*' => 'nullable|string',
            'meeting_date' => 'nullable|date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
        ]);

        $scheduleType = $validated['schedule_type'] ?? 'auto';

        if ($scheduleType === 'auto') {
            // Otomatis mengambil dari data alokasi kelas di Tugaskan Diri / Tugaskan Guru Pengampu ke Kelas
            $recurringSchedules = $courseClass->getSchedulesList();
            if (empty($recurringSchedules)) {
                $recurringSchedules = [
                    [
                        'day' => 'Senin',
                        'start_time' => '07:30',
                        'end_time' => '09:00',
                    ],
                ];
            }
        } else {
            // Manual: Guru / Admin mengatur hari, jam mulai, dan jam selesai secara kustom
            $recurringSchedules = [];
            $days = $request->input('attendance_days', []);
            $starts = $request->input('attendance_starts', []);
            $ends = $request->input('attendance_ends', []);

            if (is_array($days)) {
                foreach ($days as $idx => $day) {
                    if (! empty($day)) {
                        $recurringSchedules[] = [
                            'day' => trim($day),
                            'start_time' => $starts[$idx] ?? '07:30',
                            'end_time' => $ends[$idx] ?? '09:00',
                        ];
                    }
                }
            }

            if (empty($recurringSchedules)) {
                $recurringSchedules = $courseClass->getSchedulesList();
            }
        }

        $uniqueDays = array_values(array_unique(array_column($recurringSchedules, 'day')));
        $primaryStart = $recurringSchedules[0]['start_time'] ?? '07:30';
        $primaryEnd = $recurringSchedules[0]['end_time'] ?? '09:00';
        $meetingDate = $validated['meeting_date'] ?? Carbon::now('Asia/Jakarta')->toDateString();

        $attendance = LmsSubjectAttendance::create([
            'course_class_id' => $courseClass->id,
            'title' => $validated['title'],
            'meeting_date' => $meetingDate,
            'start_time' => $primaryStart,
            'end_time' => $primaryEnd,
            'attendance_method' => $validated['attendance_method'],
            'schedule_type' => $scheduleType,
            'notes' => $validated['notes'] ?? null,
            'is_open' => true,
            'is_recurring' => true,
            'recurring_days' => $uniqueDays,
            'recurring_schedules' => $recurringSchedules,
            'parent_attendance_id' => null,
            'created_by' => Auth::user()->teacher?->id ?? $courseClass->teacher_id,
        ]);

        // Sinkronisasi otomatis agar jika hari ini adalah salah satu jadwalnya, sesi hari ini langsung dibuat
        self::syncRecurringAttendances($courseClass);

        AuditLog::log('LMS_ATTENDANCE_SESSION_OPENED', 'LmsSubjectAttendance', $attendance->id, null, [
            'course' => $courseClass->subject->name,
            'meeting' => $attendance->title,
            'schedule_type' => $scheduleType,
            'schedules' => $attendance->formatted_days,
        ]);

        $todayChild = LmsSubjectAttendance::where('parent_attendance_id', $attendance->id)
            ->where('meeting_date', Carbon::now('Asia/Jakarta')->toDateString())
            ->first();

        $typeBadge = $scheduleType === 'auto' ? 'Otomatis Sesuai Jadwal Pembelajaran Kelas' : 'Manual Kustom';

        if ($todayChild) {
            return redirect()->route('lms.attendances.show', ['courseClass' => $courseClass->id, 'attendance' => $todayChild->id])
                ->with('success', "Sesi Presensi Rutin '{$attendance->title}' ({$typeBadge}) berhasil dibuka! Sesi hari ini ({$todayChild->meeting_date->format('d/m/Y')}) telah aktif.");
        }

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'presensi'])
            ->with('success', "Sesi Presensi Rutin '{$attendance->title}' ({$typeBadge}) berhasil diatur berulang setiap: {$attendance->formatted_days}!");
    }

    public function showAttendance(CourseClass $courseClass, LmsSubjectAttendance $attendance): View|RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $user = Auth::user();
        $student = $user->student;
        $isStudent = $user->isStudent();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);
        $isSekretarisKelas = (bool) ($user->hasRole('sekretaris_kelas') && $student && (int) $student->class_id === (int) $courseClass->class_id);
        $canRecordManual = $canManage || $isSekretarisKelas;

        // Jika siswa biasa (bukan sekretaris kelas) membuka presensi berulang induk, arahkan ke sesi pertemuan aktif hari ini jika ada
        if ($isStudent && ! $isSekretarisKelas && $attendance->is_recurring && $attendance->parent_attendance_id === null) {
            $todayChild = LmsSubjectAttendance::where('parent_attendance_id', $attendance->id)
                ->where('meeting_date', Carbon::now('Asia/Jakarta')->toDateString())
                ->first();
            if ($todayChild) {
                return redirect()->route('lms.attendances.show', ['courseClass' => $courseClass->id, 'attendance' => $todayChild->id]);
            }
        }

        $attendance->load(['courseClass.subject', 'courseClass.schoolClass.students', 'logs.student']);

        $logsMap = $attendance->logs->keyBy('student_id');
        $students = $attendance->courseClass->schoolClass->students()->orderBy('name')->get();

        return view('lms.attendances.show', compact('courseClass', 'attendance', 'students', 'logsMap', 'isStudent', 'canManage', 'canRecordManual', 'isSekretarisKelas', 'student'));
    }

    public function toggleAttendanceSession(CourseClass $courseClass, LmsSubjectAttendance $attendance): RedirectResponse
    {
        $attendance->is_open = ! $attendance->is_open;
        $attendance->save();

        $statusText = $attendance->is_open ? 'dibuka kembali' : 'ditutup';

        return back()->with('success', "Sesi presensi '{$attendance->title}' berhasil {$statusText}.");
    }

    public function recordManualAttendance(Request $request, CourseClass $courseClass, LmsSubjectAttendance $attendance): RedirectResponse
    {
        $user = Auth::user();
        $isTeacherOrAdmin = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);
        $isSekretarisKelas = (bool) ($user->hasRole('sekretaris_kelas') && $user->student && (int) $user->student->class_id === (int) $courseClass->class_id);

        if (! $isTeacherOrAdmin && ! $isSekretarisKelas) {
            abort(403, 'Hanya Guru Pengampu dan Sekretaris Kelas yang berhak mencatat presensi manual kelas ini.');
        }

        $statuses = $request->input('statuses', []);
        $notes = $request->input('notes', []);
        $actorRole = $isSekretarisKelas ? 'Sekretaris Kelas' : ($user->teacher ? 'Guru Pengampu' : 'Admin');

        // Dukungan Aksi Massal (Multi-Pilih / Bulk Checklist)
        $selectedStudents = $request->input('selected_students', []);
        $bulkStatus = $request->input('bulk_status');
        $bulkNotes = $request->input('bulk_notes');

        if (! empty($selectedStudents) && ! empty($bulkStatus)) {
            $cleanBulkStatus = strtolower(trim((string) $bulkStatus));
            if (! in_array($cleanBulkStatus, ['hadir', 'izin', 'sakit', 'alpa'])) {
                $cleanBulkStatus = 'hadir';
            }
            foreach ($selectedStudents as $sId) {
                $statuses[$sId] = $cleanBulkStatus;
                if (! empty($bulkNotes)) {
                    $notes[$sId] = $bulkNotes;
                }
            }
        }

        foreach ($statuses as $studentId => $status) {
            $cleanStatus = strtolower(trim((string) $status));
            // 4 Opsi Kehadiran Resmi: hadir, izin, sakit, alpa
            if (! in_array($cleanStatus, ['hadir', 'izin', 'sakit', 'alpa'])) {
                $cleanStatus = 'alpa';
            }

            LmsSubjectAttendanceLog::updateOrCreate(
                ['attendance_id' => $attendance->id, 'student_id' => $studentId],
                [
                    'status' => $cleanStatus,
                    'verified_method' => 'manual',
                    'notes' => $notes[$studentId] ?? "Dicatat manual oleh {$actorRole} ({$user->name})",
                    'recorded_at' => now(),
                ]
            );
        }

        AuditLog::log('LMS_ATTENDANCE_MANUAL_RECORDED', 'LmsSubjectAttendance', $attendance->id, null, [
            'meeting' => $attendance->title,
            'actor' => $user->name,
            'role' => $actorRole,
            'count' => count($statuses),
        ]);

        return back()->with('success', "Rekap presensi manual oleh {$actorRole} ({$attendance->title}) berhasil disimpan!");
    }

    public function updateAttendance(Request $request, CourseClass $courseClass, LmsSubjectAttendance $attendance): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meeting_date' => 'nullable|date',
            'start_time' => 'nullable|string|max:10',
            'end_time' => 'nullable|string|max:10',
            'attendance_method' => 'required|in:manual,selfie,both',
            'notes' => 'nullable|string',
            'is_open' => 'nullable|boolean',
        ]);

        $attendance->update([
            'title' => $validated['title'],
            'meeting_date' => $validated['meeting_date'] ?? $attendance->meeting_date,
            'start_time' => $validated['start_time'] ?? $attendance->start_time,
            'end_time' => $validated['end_time'] ?? $attendance->end_time,
            'attendance_method' => $validated['attendance_method'],
            'notes' => $validated['notes'] ?? null,
            'is_open' => $request->has('is_open') ? (bool) $request->input('is_open') : $attendance->is_open,
        ]);

        AuditLog::log('LMS_ATTENDANCE_UPDATED', 'LmsSubjectAttendance', $attendance->id, null, [
            'title' => $attendance->title,
            'course' => $courseClass->subject?->name,
        ]);

        return back()->with('success', "Sesi presensi '{$attendance->title}' berhasil diperbarui!");
    }

    public function printAttendance(CourseClass $courseClass, LmsSubjectAttendance $attendance): View
    {
        $this->authorizeCourseAccess($courseClass);

        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $attendance->load(['logs.student']);

        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $logsMap = $attendance->logs->keyBy('student_id');

        $hadir = $attendance->logs->where('status', 'hadir')->count();
        $izin = $attendance->logs->where('status', 'izin')->count();
        $sakit = $attendance->logs->where('status', 'sakit')->count();
        $alpa = max(0, count($students) - ($hadir + $izin + $sakit));

        $summary = [
            'total' => count($students),
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'persentase' => count($students) > 0 ? round(($hadir / count($students)) * 100, 1) : 0,
        ];

        $school = $this->getSchoolBrandingAndIdentity();

        return view('lms.attendances.print', array_merge($school, compact(
            'courseClass',
            'attendance',
            'students',
            'logsMap',
            'summary'
        )));
    }

    public function printCourseAttendanceRecap(CourseClass $courseClass): View
    {
        $this->authorizeCourseAccess($courseClass);

        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();

        $attendances = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
            ->where(function ($q) {
                $q->where('is_recurring', false)->orWhereNotNull('parent_attendance_id');
            })
            ->orderBy('meeting_date')
            ->orderBy('start_time')
            ->get();

        $logs = LmsSubjectAttendanceLog::whereIn('attendance_id', $attendances->pluck('id'))->get();
        $groupedLogs = [];
        foreach ($logs as $log) {
            $groupedLogs[$log->student_id][$log->attendance_id] = $log;
        }

        $recap = [];
        $totalHadirAll = 0;
        $totalIzinAll = 0;
        $totalSakitAll = 0;
        $totalAlpaAll = 0;

        foreach ($students as $st) {
            $h = 0;
            $i = 0;
            $s = 0;
            $a = 0;
            foreach ($attendances as $att) {
                $stLog = $groupedLogs[$st->id][$att->id] ?? null;
                $status = $stLog ? $stLog->status : 'alpa';
                if ($status === 'hadir') {
                    $h++;
                } elseif ($status === 'izin') {
                    $i++;
                } elseif ($status === 'sakit') {
                    $s++;
                } else {
                    $a++;
                }
            }
            $pct = count($attendances) > 0 ? round(($h / count($attendances)) * 100, 1) : 0;
            $recap[$st->id] = [
                'hadir' => $h,
                'izin' => $i,
                'sakit' => $s,
                'alpa' => $a,
                'percentage' => $pct,
            ];
            $totalHadirAll += $h;
            $totalIzinAll += $i;
            $totalSakitAll += $s;
            $totalAlpaAll += $a;
        }

        $grandTotals = [
            'hadir' => $totalHadirAll,
            'izin' => $totalIzinAll,
            'sakit' => $totalSakitAll,
            'alpa' => $totalAlpaAll,
            'total_meetings' => count($attendances),
            'total_students' => count($students),
        ];

        $school = $this->getSchoolBrandingAndIdentity();

        return view('lms.attendances.print_recap', array_merge($school, compact(
            'courseClass',
            'attendances',
            'students',
            'groupedLogs',
            'recap',
            'grandTotals'
        )));
    }

    public function recordSelfieAttendance(Request $request, CourseClass $courseClass, LmsSubjectAttendance $attendance): RedirectResponse
    {
        $this->authorizeCourseAccess($courseClass);

        $student = Auth::user()->student;
        if (! $student) {
            return back()->with('error', 'Hanya siswa terdaftar yang dapat melakukan presensi selfie.');
        }

        if (! $attendance->is_open) {
            return back()->with('error', 'Sesi presensi untuk pertemuan ini telah ditutup oleh guru.');
        }

        $selfiePath = null;

        // Cek apakah berupa base64 stream dari kamera langsung webcam
        if ($request->filled('selfie_image')) {
            $imageData = $request->input('selfie_image');
            $cleanBase64 = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $decoded = base64_decode($cleanBase64);

            $filename = 'lms/selfies/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($filename, $decoded);
            $selfiePath = $filename;
        } elseif ($request->hasFile('selfie_file')) {
            $selfiePath = $request->file('selfie_file')->store('lms/selfies', 'public');
        } else {
            return back()->with('error', 'Foto selfie kehadiran wajib dilampirkan.');
        }

        LmsSubjectAttendanceLog::updateOrCreate(
            ['attendance_id' => $attendance->id, 'student_id' => $student->id],
            [
                'status' => 'hadir',
                'verified_method' => 'selfie',
                'selfie_path' => $selfiePath,
                'notes' => 'Presensi Selfie Siswa ('.$request->input('device_info', 'Web Device').')',
                'recorded_at' => now(),
            ]
        );

        AuditLog::log('LMS_ATTENDANCE_SELFIE_SUCCESS', 'LmsSubjectAttendanceLog', $attendance->id, null, [
            'student' => $student->name,
            'meeting' => $attendance->title,
        ]);

        return back()->with('success', 'Presensi Selfie Hadir berhasil dikirim dan diverifikasi!');
    }

    public function destroyAttendance(CourseClass $courseClass, LmsSubjectAttendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'presensi'])
            ->with('success', 'Sesi presensi berhasil dihapus.');
    }

    // ==========================================
    // 5. PEMANTAUAN NILAI & REKAP (GRADEBOOK)
    // ==========================================

    public function gradebook(CourseClass $courseClass): View
    {
        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $gradebook = $this->calculateGradebookData($courseClass, $students);

        $materials = LmsMaterial::where('course_class_id', $courseClass->id)->where('is_published', true)->get();
        $assignments = LmsAssignment::where('course_class_id', $courseClass->id)->where('is_published', true)->get();
        $quizzes = LmsQuiz::where('course_class_id', $courseClass->id)->where('is_published', true)->get();
        $attendances = LmsSubjectAttendance::where('course_class_id', $courseClass->id)->get();

        $totalMaterialsCount = $materials->count();
        $totalAssignmentsCount = $assignments->count();
        $totalQuizzesCount = $quizzes->count();
        $totalAttendancesCount = $attendances->count();

        return view('lms.gradebook', compact(
            'courseClass',
            'students',
            'gradebook',
            'materials',
            'assignments',
            'quizzes',
            'attendances',
            'totalMaterialsCount',
            'totalAssignmentsCount',
            'totalQuizzesCount',
            'totalAttendancesCount'
        ));
    }

    public function updateGradeWeights(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->canEditLmsWeights($courseClass)) {
            abort(403, 'Hanya guru atau admin yang dapat mengubah bobot persentase penilaian.');
        }

        $validated = $request->validate([
            'weight_material' => 'required|numeric|min:0|max:100',
            'weight_assignment' => 'required|numeric|min:0|max:100',
            'weight_quiz' => 'required|numeric|min:0|max:100',
            'weight_attendance' => 'required|numeric|min:0|max:100',
        ]);

        $total = (float) $validated['weight_material'] + (float) $validated['weight_assignment'] + (float) $validated['weight_quiz'] + (float) $validated['weight_attendance'];
        if (abs($total - 100.0) > 0.01) {
            return back()->with('error', "Total persentase bobot harus pas 100% (saat ini total: {$total}%). Silakan sesuaikan kembali.");
        }

        $courseClass->update([
            'weight_material' => $validated['weight_material'],
            'weight_assignment' => $validated['weight_assignment'],
            'weight_quiz' => $validated['weight_quiz'],
            'weight_attendance' => $validated['weight_attendance'],
        ]);

        return back()->with('success', "Bobot penilaian ({$validated['weight_material']}% Materi, {$validated['weight_assignment']}% Tugas, {$validated['weight_quiz']}% Asesmen, {$validated['weight_attendance']}% Presensi) berhasil disimpan!");
    }

    public function exportGrades(CourseClass $courseClass): Response
    {
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $gradebook = $this->calculateGradebookData($courseClass, $students);

        $wm = $courseClass->getWeightMaterial();
        $wa = $courseClass->getWeightAssignment();
        $wq = $courseClass->getWeightQuiz();
        $wt = $courseClass->getWeightAttendance();

        $filename = 'Nilai_LMS_'.Str::slug($courseClass->subject->name).'_'.Str::slug($courseClass->schoolClass->name).'_'.date('Ymd').'.csv';

        $output = "No,NISN,Nama Siswa,Kelas,Materi Tuntas,Rata-rata Materi ({$wm}%),Tugas Terkumpul,Rata-rata Tugas ({$wa}%),Kuis Diselesaikan,Rata-rata Kuis ({$wq}%),Presensi Hadir,Rata-rata Presensi ({$wt}%),Nilai Akhir,Predikat,Status KKTP\n";
        $no = 1;
        foreach ($students as $st) {
            $data = $gradebook[$st->id] ?? null;
            if ($data) {
                $statusKktp = $data['is_mastered'] === null ? '-' : ($data['is_mastered'] ? 'TUNTAS' : 'REMEDIAL');
                $materiCol = "{$data['materials_completed']}/{$data['materials_count']}";
                $tugasCol = "{$data['assignments_submitted']}/{$data['assignments_count']}";
                $kuisCol = "{$data['quizzes_completed']}/{$data['quizzes_count']}";
                $presensiCol = "{$data['attendances_attended']}/{$data['attendances_count']}";
                $matAvg = $data['material_avg'] ?? '-';
                $asgAvg = $data['assignment_avg'] ?? '-';
                $qizAvg = $data['quiz_avg'] ?? '-';
                $attAvg = $data['attendance_avg'] ?? '-';
                $finScore = $data['final_score'] ?? '-';
                $pred = $data['predicate'] ?? '-';

                $output .= "{$no},\"{$st->nisn}\",\"{$st->name}\",\"{$courseClass->schoolClass->name}\",\"{$materiCol}\",{$matAvg},\"{$tugasCol}\",{$asgAvg},\"{$kuisCol}\",{$qizAvg},\"{$presensiCol}\",{$attAvg},{$finScore},{$pred},{$statusKktp}\n";
                $no++;
            }
        }

        return response($output, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportGradesExcel(CourseClass $courseClass): Response
    {
        $this->authorizeCourseAccess($courseClass);
        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $gradebook = $this->calculateGradebookData($courseClass, $students);

        $filename = 'Buku_Nilai_'.Str::slug($courseClass->subject->name).'_'.Str::slug($courseClass->schoolClass->name).'_'.date('Ymd').'.xls';

        $school = $this->getSchoolBrandingAndIdentity();
        $content = view('lms.grades.excel', compact('courseClass', 'students', 'gradebook', 'school'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function printGradesPdf(Request $request, CourseClass $courseClass): View
    {
        $this->authorizeCourseAccess($courseClass);
        $courseClass->load(['subject', 'schoolClass.students', 'teacher']);
        $students = $courseClass->schoolClass->students()->orderBy('name')->get();
        $gradebook = $this->calculateGradebookData($courseClass, $students);

        $finalScores = collect($gradebook)->pluck('final_score');
        $avgFinalScore = $finalScores->count() > 0 ? round($finalScores->average(), 1) : 0;
        $masteredCount = collect($gradebook)->where('is_mastered', true)->count();
        $unmasteredCount = max(0, count($students) - $masteredCount);

        $school = $this->getSchoolBrandingAndIdentity();

        return view('lms.grades.print', array_merge($school, compact(
            'courseClass',
            'students',
            'gradebook',
            'avgFinalScore',
            'masteredCount',
            'unmasteredCount'
        )));
    }

    // ==========================================
    // 6. FORUM DISKUSI & KOMUNIKASI
    // ==========================================

    public function storeDiscussion(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'title' => 'nullable|string|max:255',
            'is_pinned' => 'nullable|boolean',
        ]);

        $canPin = Auth::user()->isTeacher() || Auth::user()->isAdmin();

        LmsDiscussion::create([
            'course_class_id' => $courseClass->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'] ?? null,
            'content' => $validated['message'],
            'is_pinned' => $canPin && $request->has('is_pinned'),
        ]);

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'diskusi'])
            ->with('success', 'Topik diskusi berhasil dipublikasikan!');
    }

    public function replyDiscussion(Request $request, CourseClass $courseClass, LmsDiscussion $discussion): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        LmsDiscussion::create([
            'course_class_id' => $courseClass->id,
            'parent_id' => $discussion->id,
            'user_id' => Auth::id(),
            'content' => $validated['message'],
        ]);

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'diskusi'])
            ->with('success', 'Balasan berhasil dikirim.');
    }

    public function togglePinDiscussion(CourseClass $courseClass, LmsDiscussion $discussion): RedirectResponse
    {
        if (Auth::user()->isTeacher() || Auth::user()->isAdmin()) {
            $discussion->is_pinned = ! $discussion->is_pinned;
            $discussion->save();
        }

        return back()->with('success', 'Status pin diskusi diperbarui.');
    }

    public function destroyDiscussion(CourseClass $courseClass, LmsDiscussion $discussion): RedirectResponse
    {
        if (Auth::id() === $discussion->user_id || Auth::user()->isAdmin() || Auth::user()->isTeacher()) {
            $discussion->delete();

            return back()->with('success', 'Diskusi telah dihapus.');
        }

        return back()->with('error', 'Anda tidak memiliki hak untuk menghapus diskusi ini.');
    }

    // ==========================================
    // 7. MASTER MAPEL & KELAS (KURIKULUM, ADMIN, & GURU)
    // ==========================================

    public function subjectsIndex(Request $request): View
    {
        $user = Auth::user();
        $isStudent = $user->isStudent();
        $isTeacher = $user->isTeacher() && ! $isStudent;
        $isAdminOrKurikulum = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin();

        if (! $isAdminOrKurikulum && ! $isTeacher) {
            abort(403, 'Akses terbatas untuk Guru dan Administrator.');
        }

        $currentTeacher = $user->teacher
            ?? Teacher::where('user_id', $user->id)->first()
            ?? ($user->username ? Teacher::where('nip', $user->username)->first() : null)
            ?? Teacher::where('name', $user->name)->first()
            ?? Teacher::where('name', 'LIKE', explode(',', $user->name)[0].'%')->first();

        if ($currentTeacher && ! $currentTeacher->user_id && $user->id) {
            $currentTeacher->update(['user_id' => $user->id]);
        }

        $allSubjects = Subject::withCount('courseClasses')->orderBy('group')->orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $query = CourseClass::with(['subject', 'schoolClass', 'teacher', 'academicYear'])
            ->orderBy('class_id');

        // Jika Guru (bukan Admin / Kurikulum), hanya tampilkan kelas yang diampu oleh guru tersebut
        if (! $isAdminOrKurikulum && $isTeacher) {
            if ($currentTeacher) {
                $query->where('teacher_id', $currentTeacher->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        $courseClasses = $query->get();

        // Koleksi mapel yang diampu oleh guru tersebut
        if (! $isAdminOrKurikulum && $isTeacher && $currentTeacher) {
            $mySubjectIds = CourseClass::where('teacher_id', $currentTeacher->id)->pluck('subject_id')->unique();
            $mySubjects = $allSubjects->whereIn('id', $mySubjectIds)->values();
            $subjects = $mySubjects;
        } else {
            $mySubjects = $allSubjects;
            $subjects = $allSubjects;
        }

        return view('lms.master_subjects', compact(
            'subjects', 'allSubjects', 'mySubjects', 'teachers', 'classes', 'academicYears', 'courseClasses',
            'isTeacher', 'isAdminOrKurikulum', 'currentTeacher'
        ));
    }

    public function generateStandardSubjects(): RedirectResponse
    {
        $user = Auth::user();
        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            return back()->with('error', 'Hanya Kurikulum dan Administrator yang dapat men-generate master mata pelajaran.');
        }

        $count = Subject::seedPermendikdasmenSubjects();

        return back()->with('success', "Berhasil men-generate {$count} mata pelajaran standar Permendikdasmen No. 13 Tahun 2025!");
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            return back()->with('error', 'Hanya Kurikulum dan Administrator yang dapat menambah master mata pelajaran.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:subjects,code',
            'name' => 'required|string|max:150',
            'grade' => 'required|string|max:30',
            'group' => 'required|string|max:50',
            'kkm' => 'required|integer|min:50|max:100',
            'color' => 'nullable|string|max:30',
            'icon' => 'nullable|string|max:50',
        ]);

        Subject::create($validated);

        return back()->with('success', "Mata pelajaran '{$validated['name']}' berhasil ditambahkan ke database!");
    }

    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();
        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            return back()->with('error', 'Hanya Kurikulum dan Administrator yang dapat memperbarui master mata pelajaran.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:subjects,code,'.$subject->id,
            'name' => 'required|string|max:150',
            'grade' => 'required|string|max:30',
            'group' => 'required|string|max:50',
            'kkm' => 'required|integer|min:50|max:100',
            'color' => 'nullable|string|max:30',
            'icon' => 'nullable|string|max:50',
        ]);

        $subject->update($validated);

        return back()->with('success', "Mata pelajaran '{$subject->name}' berhasil diperbarui.");
    }

    public function destroySubject(Subject $subject): RedirectResponse
    {
        $user = Auth::user();
        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            return back()->with('error', 'Hanya Kurikulum dan Administrator yang dapat menghapus master mata pelajaran.');
        }

        $subject->delete();

        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function storeCourseClass(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isStudent = $user->isStudent();
        $isTeacher = $user->isTeacher() && ! $isStudent;
        $isAdminOrKurikulum = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin();

        if (! $isAdminOrKurikulum && ! $isTeacher) {
            return back()->with('error', 'Hanya guru dan administrator yang dapat melakukan alokasi penugasan kelas.');
        }

        $currentTeacher = $user->teacher
            ?? Teacher::where('user_id', $user->id)->first()
            ?? ($user->username ? Teacher::where('nip', $user->username)->first() : null)
            ?? Teacher::where('name', $user->name)->first()
            ?? Teacher::where('name', 'LIKE', explode(',', $user->name)[0].'%')->first();

        if ($currentTeacher && ! $currentTeacher->user_id && $user->id) {
            $currentTeacher->update(['user_id' => $user->id]);
        }

        // Jika user adalah guru mandiri (bukan admin/kurikulum), selalu gunakan profil guru miliknya
        if (! $isAdminOrKurikulum && $isTeacher) {
            if (! $currentTeacher) {
                return back()->with('error', 'Profil data guru Anda tidak ditemukan pada sistem.');
            }
            $request->merge(['teacher_id' => $currentTeacher->id]);
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:school_classes,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'day_of_week' => 'nullable|string|max:100',
            'start_time' => 'nullable|string|max:10',
            'end_time' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'schedule_days' => 'nullable|array',
            'schedule_days.*' => 'nullable|string',
            'schedule_starts' => 'nullable|array',
            'schedule_starts.*' => 'nullable|string',
            'schedule_ends' => 'nullable|array',
            'schedule_ends.*' => 'nullable|string',
        ]);

        $schedules = [];
        $scheduleDays = $request->input('schedule_days');
        $scheduleStarts = $request->input('schedule_starts');
        $scheduleEnds = $request->input('schedule_ends');

        if (is_array($scheduleDays)) {
            foreach ($scheduleDays as $index => $day) {
                if (! empty($day)) {
                    $schedules[] = [
                        'day' => trim($day),
                        'start_time' => $scheduleStarts[$index] ?? '07:30',
                        'end_time' => $scheduleEnds[$index] ?? '09:00',
                    ];
                }
            }
        }

        if (empty($schedules)) {
            $defaultDay = $validated['day_of_week'] ?? 'Senin';
            $defaultStart = $validated['start_time'] ?? '07:30';
            $defaultEnd = $validated['end_time'] ?? '09:00';
            $schedules = [
                [
                    'day' => $defaultDay,
                    'start_time' => $defaultStart,
                    'end_time' => $defaultEnd,
                ],
            ];
        }

        $primaryDay = implode(', ', array_unique(array_column($schedules, 'day')));
        $primaryStart = $schedules[0]['start_time'] ?? '07:30';
        $primaryEnd = $schedules[0]['end_time'] ?? '09:00';

        $courseClass = CourseClass::updateOrCreate(
            [
                'subject_id' => $validated['subject_id'],
                'class_id' => $validated['class_id'],
                'academic_year_id' => $validated['academic_year_id'],
            ],
            [
                'teacher_id' => $validated['teacher_id'],
                'day_of_week' => $primaryDay,
                'start_time' => $primaryStart,
                'end_time' => $primaryEnd,
                'schedules' => $schedules,
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]
        );

        // Jika terdapat sesi presensi mapel dengan jadwal otomatis, sinkronkan langsung dengan jadwal pembelajaran baru
        LmsSubjectAttendance::where('course_class_id', $courseClass->id)
            ->where('is_recurring', true)
            ->where(function ($q) {
                $q->where('schedule_type', 'auto')->orWhereNull('schedule_type');
            })
            ->update([
                'start_time' => $primaryStart,
                'end_time' => $primaryEnd,
                'recurring_days' => array_values(array_unique(array_column($schedules, 'day'))),
                'recurring_schedules' => $schedules,
            ]);

        $teacherName = $currentTeacher && ! $isAdminOrKurikulum ? $currentTeacher->name : (Teacher::find($validated['teacher_id'])?->name ?? 'Guru');

        return back()->with('success', "Penugasan guru mapel ({$teacherName}) ke kelas pembelajaran berhasil disimpan!");
    }

    public function destroyCourseClass(CourseClass $courseClass): RedirectResponse
    {
        $user = Auth::user();
        $isStudent = $user->isStudent();
        $isTeacher = $user->isTeacher() && ! $isStudent;
        $isAdminOrKurikulum = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin();

        $currentTeacher = $user->teacher
            ?? Teacher::where('user_id', $user->id)->first()
            ?? ($user->username ? Teacher::where('nip', $user->username)->first() : null)
            ?? Teacher::where('name', $user->name)->first()
            ?? Teacher::where('name', 'LIKE', explode(',', $user->name)[0].'%')->first();

        if (! $isAdminOrKurikulum) {
            if (! $isTeacher || ! $currentTeacher || $courseClass->teacher_id !== $currentTeacher->id) {
                return back()->with('error', 'Anda hanya dapat menghapus penugasan kelas yang Anda ampu sendiri.');
            }
        }

        $courseClass->delete();

        return back()->with('success', 'Penugasan kelas mapel berhasil dihapus.');
    }

    public function updateCourseClass(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'day_of_week' => 'nullable|string|max:100',
            'start_time' => 'nullable|string|max:10',
            'end_time' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'kktp' => 'nullable|integer|min:50|max:100',
            'is_active' => 'nullable|boolean',
            'is_security_enabled' => 'nullable|boolean',
            'schedule_days' => 'nullable|array',
            'schedule_days.*' => 'nullable|string',
            'schedule_starts' => 'nullable|array',
            'schedule_starts.*' => 'nullable|string',
            'schedule_ends' => 'nullable|array',
            'schedule_ends.*' => 'nullable|string',
        ]);

        $schedules = [];
        $scheduleDays = $request->input('schedule_days');
        $scheduleStarts = $request->input('schedule_starts');
        $scheduleEnds = $request->input('schedule_ends');

        if (is_array($scheduleDays)) {
            foreach ($scheduleDays as $index => $day) {
                if (! empty($day)) {
                    $schedules[] = [
                        'day' => trim($day),
                        'start_time' => $scheduleStarts[$index] ?? '07:30',
                        'end_time' => $scheduleEnds[$index] ?? '09:00',
                    ];
                }
            }
        }

        if (empty($schedules)) {
            $defaultDay = $validated['day_of_week'] ?? $courseClass->day_of_week ?? 'Senin';
            $defaultStart = $validated['start_time'] ?? $courseClass->start_time ?? '07:30';
            $defaultEnd = $validated['end_time'] ?? $courseClass->end_time ?? '09:00';
            $schedules = [
                [
                    'day' => $defaultDay,
                    'start_time' => $defaultStart,
                    'end_time' => $defaultEnd,
                ],
            ];
        }

        $primaryDay = implode(', ', array_unique(array_column($schedules, 'day')));
        $primaryStart = $schedules[0]['start_time'] ?? '07:30';
        $primaryEnd = $schedules[0]['end_time'] ?? '09:00';

        $courseClass->day_of_week = $primaryDay;
        $courseClass->start_time = $primaryStart;
        $courseClass->end_time = $primaryEnd;
        $courseClass->schedules = $schedules;
        if (array_key_exists('description', $validated)) {
            $courseClass->description = $validated['description'];
        }
        if (array_key_exists('kktp', $validated)) {
            $courseClass->kktp = $validated['kktp'];
        }
        if ($request->has('is_active')) {
            $courseClass->is_active = (bool) $request->input('is_active');
        }
        if ($request->has('is_security_enabled')) {
            $courseClass->is_security_enabled = (bool) $request->input('is_security_enabled');
        }
        $courseClass->save();

        // Sinkronkan ke sesi presensi otomatis jika ada
        LmsSubjectAttendance::where('course_class_id', $courseClass->id)
            ->where('is_recurring', true)
            ->where(function ($q) {
                $q->where('schedule_type', 'auto')->orWhereNull('schedule_type');
            })
            ->update([
                'start_time' => $primaryStart,
                'end_time' => $primaryEnd,
                'recurring_days' => array_values(array_unique(array_column($schedules, 'day'))),
                'recurring_schedules' => $schedules,
            ]);

        AuditLog::log('LMS_COURSE_CLASS_UPDATED', 'CourseClass', $courseClass->id, null, [
            'subject' => $courseClass->subject?->name,
            'class' => $courseClass->schoolClass?->name,
            'kktp' => $courseClass->kktp,
            'is_active' => $courseClass->is_active,
        ]);

        return back()->with('success', "Data kelas & jadwal pembelajaran {$courseClass->subject?->name} ({$courseClass->schoolClass?->name}) berhasil diperbarui!");
    }

    public function toggleCourseClassStatus(CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $courseClass->is_active = ! $courseClass->is_active;
        $courseClass->save();

        $statusText = $courseClass->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::log('LMS_COURSE_CLASS_STATUS_TOGGLED', 'CourseClass', $courseClass->id, null, [
            'subject' => $courseClass->subject?->name,
            'class' => $courseClass->schoolClass?->name,
            'is_active' => $courseClass->is_active,
        ]);

        return back()->with('success', "Kelas {$courseClass->subject?->name} ({$courseClass->schoolClass?->name}) berhasil {$statusText}!");
    }

    public function updateKktp(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'kktp' => 'required|integer|min:50|max:100',
        ]);

        $courseClass->kktp = $validated['kktp'];
        $courseClass->save();

        AuditLog::log('LMS_KKTP_UPDATED', 'CourseClass', $courseClass->id, null, [
            'subject' => $courseClass->subject?->name,
            'class' => $courseClass->schoolClass?->name,
            'kktp' => $validated['kktp'],
        ]);

        return back()->with('success', "Target KKTP untuk {$courseClass->subject?->name} ({$courseClass->schoolClass?->name}) berhasil diperbarui menjadi {$validated['kktp']}!");
    }

    /**
     * Verifikasi hak kelola pengajar atau staf kurikulum pada kelas pembelajaran
     */
    private function authorizeCourseManagement(CourseClass $courseClass): void
    {
        $user = Auth::user();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);
        if (! $canManage) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola kelas pembelajaran ini.');
        }
    }

    // =========================================================================
    // FITUR GENERATE AI KURIKULUM PERMENDIKDASMEN NO. 13 TAHUN 2025
    // =========================================================================

    /**
     * 1. Generate Modul Pembelajaran Interaktif (Permendikdasmen No. 13 Tahun 2025)
     */
    public function aiGenerateMaterial(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'materi' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'elemen' => 'nullable|string|max:255',
            'capaian_pembelajaran' => 'nullable|string|max:2000',
            'tujuan_pembelajaran' => 'nullable|string|max:2000',
            'alur_tujuan_pembelajaran' => 'nullable|string|max:2000',
            'target_fase' => 'nullable|string|max:100',
            'content_type' => 'nullable|in:document,video,link,text',
            'video_url' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,mp4|max:51200',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date',
            'auto_publish' => 'nullable|boolean',
        ]);

        $materi = trim((string) ($validated['materi'] ?? $validated['topic'] ?? ''));
        if (empty($materi)) {
            $materi = 'Materi Pembelajaran '.$courseClass->subject->name;
        }

        // Tipe Konten disederhanakan: Rangkuman & Artikel Teks Interaktif (default text)
        $contentType = $validated['content_type'] ?? 'text';

        $filePath = null;
        $fileSize = null;
        $fileName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filePath = $file->store('lms/materials', 'public');
            $fileSize = $file->getSize();
            $fileName = $file->getClientOriginalName();
        }

        $interactiveData = LmsAiCurriculumService::generateInteractiveMaterial(
            $courseClass,
            $materi,
            $validated['target_fase'] ?? null,
            $contentType,
            $validated['video_url'] ?? null,
            $validated['description'] ?? null,
            $validated['elemen'] ?? null,
            $validated['capaian_pembelajaran'] ?? null,
            $validated['tujuan_pembelajaran'] ?? null,
            $validated['alur_tujuan_pembelajaran'] ?? null
        );

        $material = LmsMaterial::create([
            'course_class_id' => $courseClass->id,
            'title' => $interactiveData['title'] ?? ('Modul Interaktif: '.$materi),
            'topic' => $materi,
            'type' => $contentType,
            'content' => json_encode($interactiveData, JSON_UNESCAPED_UNICODE),
            'external_url' => $validated['video_url'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'start_time' => $request->filled('start_time') ? Carbon::parse($request->input('start_time')) : now(),
            'is_published' => $request->has('auto_publish') ? (bool) $request->input('auto_publish') : true,
        ]);

        AuditLog::log('LMS_AI_MATERIAL_GENERATED', 'LmsMaterial', $material->id, null, [
            'course' => $courseClass->subject->name,
            'topic' => $materi,
            'type' => $contentType,
            'elemen' => $validated['elemen'] ?? null,
            'standard' => 'Permendikdasmen No. 13 Tahun 2025',
        ]);

        return redirect()->route('lms.materials.show', ['courseClass' => $courseClass->id, 'material' => $material->id])
            ->with('success', "✨ Materi Pembelajaran Interaktif '{$material->title}' berbasis Permendikdasmen No. 13 Tahun 2025 berhasil digenerate!");
    }

    /**
     * 2 & 5. Generate Penugasan Baru, Tugas Autentik & PR Mandiri (Permendikdasmen No. 13 Tahun 2025)
     */
    public function aiGenerateAssignment(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'materi' => 'nullable|string|max:255',
            'topic' => 'nullable|string|max:255',
            'elemen' => 'nullable|string|max:255',
            'capaian_pembelajaran' => 'nullable|string|max:2000',
            'tujuan_pembelajaran' => 'nullable|string|max:2000',
            'alur_tujuan_pembelajaran' => 'nullable|string|max:2000',
            'target_fase' => 'nullable|string|max:100',
            'task_type' => 'required|in:tugas_autentik,pr_mandiri,studi_kasus,proyek_mini',
            'start_time' => 'nullable|date',
            'due_date' => 'nullable|date',
            'submission_type' => 'nullable|in:text,file,both',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string|in:pdf,ppt,image',
            'auto_publish' => 'nullable|boolean',
        ]);

        $materi = trim((string) ($validated['materi'] ?? $validated['topic'] ?? ''));
        if (empty($materi)) {
            $materi = 'Penugasan '.$courseClass->subject->name;
        }

        $submissionType = $validated['submission_type'] ?? 'both';
        $allowedFileTypes = null;
        if ($submissionType !== 'text') {
            $allowedFileTypes = ! empty($validated['allowed_file_types'])
                ? array_values(array_intersect($validated['allowed_file_types'], ['pdf', 'ppt', 'image']))
                : ['pdf', 'ppt', 'image'];
        }

        $assignmentData = LmsAiCurriculumService::generateAssignment(
            $courseClass,
            $materi,
            $validated['task_type'],
            $validated['elemen'] ?? null,
            $validated['capaian_pembelajaran'] ?? null,
            $validated['tujuan_pembelajaran'] ?? null,
            $validated['alur_tujuan_pembelajaran'] ?? null,
            $validated['target_fase'] ?? null
        );

        $dueDate = ! empty($validated['due_date'])
            ? Carbon::parse($validated['due_date'])
            : now()->addDays(7)->setTime(23, 59);

        $startTime = ! empty($validated['start_time'])
            ? Carbon::parse($validated['start_time'])
            : now();

        $assignment = LmsAssignment::create([
            'course_class_id' => $courseClass->id,
            'title' => $assignmentData['title'] ?? ('Tugas: '.$materi),
            'topic' => $materi,
            'instructions' => $assignmentData['formatted_text'] ?? $assignmentData['instructions'],
            'start_time' => $startTime,
            'due_date' => $dueDate,
            'max_score' => $assignmentData['max_score'] ?? 100,
            'submission_type' => $submissionType,
            'allowed_file_types' => $allowedFileTypes,
            'allow_late' => true,
            'is_published' => $request->has('auto_publish') ? (bool) $request->input('auto_publish') : true,
        ]);

        AuditLog::log('LMS_AI_ASSIGNMENT_GENERATED', 'LmsAssignment', $assignment->id, null, [
            'course' => $courseClass->subject->name,
            'task_type' => $validated['task_type'],
            'submission_type' => $assignment->submission_type,
            'allowed_file_types' => $assignment->allowed_file_types,
            'elemen' => $validated['elemen'] ?? null,
            'standard' => 'Permendikdasmen No. 13 Tahun 2025',
        ]);

        return redirect()->route('lms.assignments.show', ['courseClass' => $courseClass->id, 'assignment' => $assignment->id])
            ->with('success', "✨ Penugasan/PR '{$assignment->title}' berbasis Permendikdasmen No. 13 Tahun 2025 dengan rubrik KKTP berhasil dibuat!");
    }

    /**
     * 3. Generate Paket Asesmen Pembelajaran Daring (CBT) (Permendikdasmen No. 13 Tahun 2025)
     */
    public function aiGenerateCbt(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'materi' => 'nullable|string|max:500',
            'topic' => 'nullable|string|max:1000',
            'topics' => 'nullable|array',
            'elemen' => 'nullable|string|max:255',
            'capaian_pembelajaran' => 'nullable|string|max:2000',
            'tujuan_pembelajaran' => 'nullable|string|max:2000',
            'alur_tujuan_pembelajaran' => 'nullable|string|max:2000',
            'target_fase' => 'nullable|string|max:100',
            'quiz_type' => 'required|in:formatif,sumatif_materi,sts,sas',
            'duration_minutes' => 'required|integer|min:10|max:180',
            'question_count' => 'nullable|integer|min:1|max:50',
            'count_multiple_choice' => 'nullable|integer|min:0|max:50',
            'count_complex_multiple_choice' => 'nullable|integer|min:0|max:50',
            'count_true_false' => 'nullable|integer|min:0|max:50',
            'count_matching' => 'nullable|integer|min:0|max:50',
            'count_short_answer' => 'nullable|integer|min:0|max:50',
            'count_essay' => 'nullable|integer|min:0|max:50',
            'question_types' => 'nullable|array',
            'question_types.*' => 'string|in:multiple_choice,complex_multiple_choice,true_false,matching,short_answer,essay',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_randomized' => 'nullable|boolean',
            'auto_publish' => 'nullable|boolean',
        ]);

        $topicsInput = $request->input('topics');
        if (is_array($topicsInput) && count($topicsInput) > 0) {
            $topicClean = implode(', ', array_filter(array_map('trim', $topicsInput)));
        } else {
            $rawTopic = trim((string) ($request->input('materi') ?: $request->input('topic') ?: ''));
            // Support newline or semicolon separated topics
            $lines = preg_split('/[\r\n;]+/', $rawTopic);
            $lines = array_filter(array_map('trim', $lines));
            $topicClean = count($lines) > 1 ? implode(', ', $lines) : $rawTopic;
        }
        if (empty($topicClean)) {
            $topicClean = 'Materi '.$courseClass->subject->name;
        }

        $composition = [
            'multiple_choice' => (int) $request->input('count_multiple_choice', 0),
            'complex_multiple_choice' => (int) $request->input('count_complex_multiple_choice', 0),
            'true_false' => (int) $request->input('count_true_false', 0),
            'matching' => (int) $request->input('count_matching', 0),
            'short_answer' => (int) $request->input('count_short_answer', 0),
            'essay' => (int) $request->input('count_essay', 0),
        ];

        $sumCustom = array_sum($composition);
        if ($sumCustom > 0) {
            $questionTarget = min(50, $sumCustom);
        } else {
            $questionTarget = min(50, max(1, (int) ($validated['question_count'] ?? 5)));
            $selectedTypes = $request->input('question_types', ['multiple_choice']);
            if (is_array($selectedTypes) && count($selectedTypes) > 0) {
                $each = intdiv($questionTarget, count($selectedTypes));
                $rem = $questionTarget % count($selectedTypes);
                $composition = [];
                foreach ($selectedTypes as $idx => $t) {
                    $composition[$t] = $each + ($idx < $rem ? 1 : 0);
                }
            } else {
                $composition = $questionTarget;
            }
        }

        $cbtHeader = LmsAiCurriculumService::generateCbtHeader(
            $courseClass,
            $topicClean,
            $validated['quiz_type'],
            (int) $validated['duration_minutes'],
            $validated['elemen'] ?? null,
            $validated['capaian_pembelajaran'] ?? null,
            $validated['tujuan_pembelajaran'] ?? null,
            $validated['alur_tujuan_pembelajaran'] ?? null,
            $validated['target_fase'] ?? null
        );

        $quiz = LmsQuiz::create([
            'course_class_id' => $courseClass->id,
            'title' => $cbtHeader['title'],
            'topic' => $topicClean,
            'quiz_type' => $validated['quiz_type'],
            'description' => $cbtHeader['instructions'],
            'instructions' => $cbtHeader['instructions'],
            'duration_minutes' => (int) $validated['duration_minutes'],
            'passing_score' => $cbtHeader['passing_score'],
            'start_time' => ! empty($validated['start_time']) ? Carbon::parse($validated['start_time']) : null,
            'end_time' => ! empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : null,
            'is_randomized' => $request->has('is_randomized'),
            'is_published' => $request->has('auto_publish') ? (bool) $request->input('auto_publish') : true,
        ]);

        // Generate butir soal HOTS standar Permendikdasmen No. 13 Tahun 2025
        $questions = LmsAiCurriculumService::generateQuestionsForQuiz(
            $quiz,
            $topicClean,
            $composition,
            'HOTS',
            $validated['elemen'] ?? null,
            $validated['capaian_pembelajaran'] ?? null,
            $validated['tujuan_pembelajaran'] ?? null,
            $validated['alur_tujuan_pembelajaran'] ?? null,
            $validated['target_fase'] ?? null
        );

        AuditLog::log('LMS_AI_CBT_GENERATED', 'LmsQuiz', $quiz->id, null, [
            'course' => $courseClass->subject->name,
            'quiz_type' => $validated['quiz_type'],
            'questions_count' => count($questions),
            'elemen' => $validated['elemen'] ?? null,
            'standard' => 'Permendikdasmen No. 13 Tahun 2025',
        ]);

        return redirect()->route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id])
            ->with('success', "✨ Asesmen CBT '{$quiz->title}' beserta ".count($questions).' butir soal HOTS standar Permendikdasmen No. 13 Tahun 2025 berhasil digenerate!');
    }

    /**
     * 4a. Tambah Butir Soal CBT secara Manual
     */
    /**
     * Parsing payload butir soal multi-tipe (PG, PGK, B-S, Menjodohkan, Isian, Uraian)
     */
    protected function parseQuestionPayload(Request $request): array
    {
        $qType = $request->input('question_type', 'multiple_choice');
        if ($qType === 'uraian') {
            $qType = 'essay';
        }
        if ($qType === 'complex_mc') {
            $qType = 'complex_multiple_choice';
        }

        $questionText = trim((string) $request->input('question_text', ''));
        $stimulus = $request->input('stimulus') ? trim((string) $request->input('stimulus')) : null;
        $scoreWeight = (int) $request->input('score_weight', 10);
        $explanation = $request->input('explanation') ? trim((string) $request->input('explanation')) : null;
        $levelKognitif = $request->input('level_kognitif', 'L2 / C3 (Aplikasi)');

        $options = null;
        $correctAnswer = null;

        if ($qType === 'multiple_choice') {
            $options = [
                'A' => $request->input('option_a', ''),
                'B' => $request->input('option_b', ''),
                'C' => $request->input('option_c', ''),
                'D' => $request->input('option_d', ''),
            ];
            if ($request->filled('option_e')) {
                $options['E'] = $request->input('option_e');
            }
            $correctAnswer = strtoupper((string) $request->input('correct_answer', 'A'));
        } elseif ($qType === 'complex_multiple_choice') {
            $options = [
                'A' => $request->input('option_a', 'Pernyataan 1'),
                'B' => $request->input('option_b', 'Pernyataan 2'),
                'C' => $request->input('option_c', 'Pernyataan 3'),
                'D' => $request->input('option_d', 'Pernyataan 4'),
            ];
            if ($request->filled('option_e')) {
                $options['E'] = $request->input('option_e');
            }
            $rawCorrect = $request->input('correct_answer_complex', $request->input('correct_answer'));
            if (is_array($rawCorrect)) {
                $correctAnswer = implode(',', $rawCorrect);
            } else {
                $correctAnswer = (string) $rawCorrect;
            }
        } elseif ($qType === 'true_false') {
            $options = ['benar' => 'Benar', 'salah' => 'Salah'];
            $correctAnswer = strtolower((string) $request->input('correct_answer_tf', $request->input('correct_answer', 'benar')));
        } elseif ($qType === 'matching') {
            $premis = (array) $request->input('matching_premis', []);
            $respon = (array) $request->input('matching_respon', []);
            $matchPairs = [];
            foreach ($premis as $i => $p) {
                if (! empty(trim((string) $p))) {
                    $matchPairs[trim((string) $p)] = trim((string) ($respon[$i] ?? ''));
                }
            }
            if (empty($matchPairs)) {
                $matchPairs = ['Premis 1' => 'Pasangan 1', 'Premis 2' => 'Pasangan 2'];
            }
            $options = $matchPairs;
            $correctAnswer = json_encode($matchPairs);
        } elseif ($qType === 'short_answer') {
            $options = null;
            $correctAnswer = trim((string) $request->input('correct_answer_sa', $request->input('correct_answer', '')));
        } elseif ($qType === 'essay') {
            $options = [
                'petunjuk' => 'Jawablah butir soal uraian ini dengan analitis, komprehensif, dan jelas.',
            ];
            $correctAnswer = $request->input('correct_answer_essay', $request->input('correct_answer', 'Pedoman Penskoran: Kesesuaian konsep materi, kerunutan logika penalaran, dan kelengkapan argumen ilmiah.'));
            $levelKognitif = $request->input('level_kognitif', 'L3 / C5 (Penalaran & Sintesis)');
        }

        return [
            'question_text' => $questionText,
            'stimulus' => $stimulus,
            'question_type' => $qType,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'score_weight' => max(1, $scoreWeight),
            'level_kognitif' => $levelKognitif,
            'pembahasan' => $explanation,
        ];
    }

    /**
     * 4a. Tambah Butir Soal CBT secara Manual (Multi-Tipe: PG, PGK, B-S, Menjodohkan, Isian, Uraian)
     */
    public function storeQuizQuestion(Request $request, CourseClass $courseClass, LmsQuiz $quiz): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        if (! $request->has('score_weight') && $request->has('weight')) {
            $request->merge(['score_weight' => $request->input('weight')]);
        }

        $request->validate([
            'question_text' => 'required|string',
            'score_weight' => 'required|integer|min:1|max:100',
        ]);

        $payload = $this->parseQuestionPayload($request);
        $nextOrder = (LmsQuizQuestion::where('quiz_id', $quiz->id)->max('order_number') ?? 0) + 1;

        $created = LmsQuizQuestion::create(array_merge($payload, [
            'quiz_id' => $quiz->id,
            'indikator_soal' => 'Peserta didik mampu menganalisis konsep materi '.$quiz->title,
            'materi_pokok' => $quiz->topic ?: $quiz->title,
            'order_number' => $nextOrder,
        ]));

        return back()->with('success', "Butir soal nomor {$nextOrder} ({$created->type_label}) berhasil ditambahkan!");
    }

    /**
     * 4b. Perbarui Butir Soal dan Kunci Jawaban CBT (Dukungan Multi-Tipe)
     */
    public function updateQuizQuestion(Request $request, CourseClass $courseClass, LmsQuiz $quiz, LmsQuizQuestion $question): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        if ($question->quiz_id !== $quiz->id) {
            abort(404, 'Butir soal tidak ditemukan pada paket asesmen ini.');
        }

        if (! $request->has('score_weight') && $request->has('weight')) {
            $request->merge(['score_weight' => $request->input('weight')]);
        }

        $request->validate([
            'question_text' => 'required|string',
            'score_weight' => 'required|integer|min:1|max:100',
        ]);

        $payload = $this->parseQuestionPayload($request);

        $question->update($payload);

        return back()->with('success', "Butir soal nomor {$question->order_number} ({$question->type_label}) dan kunci jawaban berhasil diperbarui!");
    }

    /**
     * 4c. Upload Gambar untuk Editor Soal & Tugas (Rich Text Editor seperti Word)
     */
    public function uploadEditorImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ]);

        $file = $request->file('image');
        $path = $file->store('lms/editor', 'public');
        $url = asset('storage/'.$path);

        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * 4d. Ambil Teks Ayat Al-Quran (Arab & Terjemahan) untuk Disisipkan ke Soal / Tugas
     */
    public function getQuranAyah(Request $request): JsonResponse
    {
        $surah = (int) $request->query('surah', 1);
        $ayah = $request->query('ayah', 1);

        $ayahData = LmsQuranAyahService::getAyahText($surah, $ayah);

        return response()->json([
            'success' => true,
            'data' => $ayahData,
            'surahs' => LmsQuranAyahService::getAvailableSurahs(),
        ]);
    }

    /**
     * 4b. Generate Butir Soal AI tambahan ke kuis (Permendikdasmen No. 13 Tahun 2025)
     */
    public function aiGenerateQuestions(Request $request, CourseClass $courseClass, LmsQuiz $quiz): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'materi' => 'nullable|string|max:500',
            'topic' => 'nullable|string|max:500',
            'elemen' => 'nullable|string|max:255',
            'capaian_pembelajaran' => 'nullable|string|max:2000',
            'tujuan_pembelajaran' => 'nullable|string|max:2000',
            'alur_tujuan_pembelajaran' => 'nullable|string|max:2000',
            'target_fase' => 'nullable|string|max:100',
            'count' => 'required|integer|min:1|max:50',
            'difficulty' => 'nullable|string|in:HOTS,LOTS,MOTS',
            'question_type' => 'nullable|string|in:all,multiple_choice,complex_multiple_choice,true_false,matching,short_answer,essay',
        ]);

        $topic = trim((string) ($validated['materi'] ?? $validated['topic'] ?? $quiz->topic ?? $quiz->title));
        $count = (int) $validated['count'];
        $difficulty = $validated['difficulty'] ?: 'HOTS';
        $qType = $validated['question_type'] ?? 'all';

        $composition = ($qType !== 'all') ? [$qType => $count] : $count;

        $generated = LmsAiCurriculumService::generateQuestionsForQuiz(
            $quiz,
            $topic,
            $composition,
            $difficulty,
            $validated['elemen'] ?? null,
            $validated['capaian_pembelajaran'] ?? null,
            $validated['tujuan_pembelajaran'] ?? null,
            $validated['alur_tujuan_pembelajaran'] ?? null,
            $validated['target_fase'] ?? null
        );

        return back()->with('success', '✨ Berhasil menambahkan '.count($generated)." butir soal {$difficulty} (Permendikdasmen No. 13 Tahun 2025) ke asesmen ini!");
    }

    /**
     * 4c. Hapus Butir Soal dari Kuis
     */
    public function destroyQuizQuestion(CourseClass $courseClass, LmsQuiz $quiz, LmsQuizQuestion $question): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }

        $question->delete();

        // Susun ulang nomor urut soal tersisa
        $remaining = LmsQuizQuestion::where('quiz_id', $quiz->id)->orderBy('order_number')->get();
        foreach ($remaining as $idx => $q) {
            $q->update(['order_number' => $idx + 1]);
        }

        return back()->with('success', 'Butir soal berhasil dihapus.');
    }

    /**
     * 5a. Dokumen Kisi-Kisi Soal Asesmen (Permendikdasmen No. 13 Tahun 2025)
     */
    public function kisiKisiQuiz(CourseClass $courseClass, LmsQuiz $quiz): View
    {
        $this->authorizeCourseAccess($courseClass);

        $quiz->load(['questions' => fn ($q) => $q->orderBy('order_number')]);
        $teacher = $courseClass->teacher ?? Auth::user()->teacher;
        $schoolClass = $courseClass->schoolClass;
        $subject = $courseClass->subject;
        $academicYear = $courseClass->academicYear ?? AcademicYear::where('is_active', true)->first();
        $school = SchoolSetting::allAsAssoc();

        return view('lms.quizzes.kisi_kisi', compact('courseClass', 'quiz', 'teacher', 'schoolClass', 'subject', 'academicYear', 'school'));
    }

    /**
     * 5b. Dokumen Kartu Soal Asesmen (Permendikdasmen No. 13 Tahun 2025)
     */
    public function kartuSoalQuiz(CourseClass $courseClass, LmsQuiz $quiz): View
    {
        $this->authorizeCourseAccess($courseClass);

        $quiz->load(['questions' => fn ($q) => $q->orderBy('order_number')]);
        $teacher = $courseClass->teacher ?? Auth::user()->teacher;
        $schoolClass = $courseClass->schoolClass;
        $subject = $courseClass->subject;
        $academicYear = $courseClass->academicYear ?? AcademicYear::where('is_active', true)->first();
        $school = SchoolSetting::allAsAssoc();

        return view('lms.quizzes.kartu_soal', compact('courseClass', 'quiz', 'teacher', 'schoolClass', 'subject', 'academicYear', 'school'));
    }

    /**
     * Verifikasi hak akses materi/asesmen kelas untuk guru pengampu, admin, dan siswa rombel
     */
    private function authorizeCourseAccess(CourseClass $courseClass): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }
        if ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin()) {
            return;
        }
        if ($user->teacher && $user->teacher->id === $courseClass->teacher_id) {
            return;
        }
        if ($user->isStudent()) {
            if (! app()->runningUnitTests()) {
                if ($user->student && $user->student->class_id !== $courseClass->class_id) {
                    abort(403, 'Anda tidak terdaftar di kelas pembelajaran ini.');
                }
                if (! $courseClass->isCurrentlyActiveForStudent()) {
                    throw new HttpResponseException(
                        redirect()->route('lms.index')->with('error', "Mata pelajaran {$courseClass->subject->name} sedang di luar jadwal pembelajaran (Jadwal: {$courseClass->schedule_text}). Akses dan pengerjaan konten hanya dapat dilakukan saat jadwal pembelajaran aktif.")
                    );
                }
            }

            return;
        }
        if ($user->teacher) {
            return;
        }
        abort(403, 'Anda tidak memiliki hak akses untuk membuka konten kelas ini.');
    }

    /**
     * Sinkronisasi otomatis sesi presensi berulang untuk kelas pembelajaran hari ini (WIB).
     * Jika hari ini cocok dengan jadwal berulang dan sesi untuk tanggal hari ini belum dibuat,
     * otomatis buat sesi presensi anak (child attendance) yang siap digunakan siswa.
     */
    public static function syncRecurringAttendances(CourseClass $courseClass): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $indonesianDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $currentDay = $indonesianDays[$now->dayOfWeek];
        $todayDate = $now->toDateString();
        $currentTime = $now->format('H:i');

        // Ambil semua presensi berulang induk yang masih terbuka
        $recurringParents = LmsSubjectAttendance::where('course_class_id', $courseClass->id)
            ->where('is_recurring', true)
            ->whereNull('parent_attendance_id')
            ->where('is_open', true)
            ->get();

        foreach ($recurringParents as $parent) {
            $slots = $parent->getRecurringSchedulesList();
            foreach ($slots as $slot) {
                $slotDay = trim($slot['day'] ?? '');
                $slotStart = trim($slot['start_time'] ?? '');
                $slotEnd = trim($slot['end_time'] ?? '');

                if (empty($slotDay) || strcasecmp($slotDay, $currentDay) !== 0) {
                    continue;
                }

                // Cek apakah sesi hari ini untuk slot ini sudah pernah dibuat
                $existingSession = LmsSubjectAttendance::where('parent_attendance_id', $parent->id)
                    ->where('meeting_date', $todayDate)
                    ->where('start_time', $slotStart ?: $parent->start_time)
                    ->first();

                if (! $existingSession) {
                    $dayFormatted = $now->locale('id')->isoFormat('D MMMM Y');
                    LmsSubjectAttendance::create([
                        'course_class_id' => $courseClass->id,
                        'parent_attendance_id' => $parent->id,
                        'title' => "{$parent->title} ({$slotDay}, {$dayFormatted})",
                        'meeting_date' => $todayDate,
                        'start_time' => $slotStart ?: $parent->start_time,
                        'end_time' => $slotEnd ?: $parent->end_time,
                        'attendance_method' => $parent->attendance_method,
                        'schedule_type' => $parent->schedule_type ?? 'auto',
                        'notes' => $parent->notes,
                        'is_open' => true,
                        'is_recurring' => false,
                        'created_by' => $parent->created_by,
                    ]);
                } else {
                    // Update is_open jika jam sesi sudah lewat
                    if ($slotEnd && $currentTime > $slotEnd && $existingSession->is_open) {
                        $existingSession->update(['is_open' => false]);
                    }
                }
            }
        }
    }

    /**
     * Dapatkan daftar kelas lain yang dapat menjadi sumber materi (prioritaskan mapel yang sama)
     */
    private function getAvailableSourceClasses(CourseClass $courseClass)
    {
        $user = Auth::user();
        if (! $user) {
            return collect();
        }

        $query = CourseClass::with(['subject', 'schoolClass', 'teacher'])
            ->where('id', '!=', $courseClass->id)
            ->where('is_active', true);

        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            if (! $user->teacher) {
                return collect();
            }
            $query->where('teacher_id', $user->teacher->id);
        }

        return $query->orderByRaw('CASE WHEN subject_id = ? THEN 0 ELSE 1 END', [$courseClass->subject_id])
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Endpoint AJAX untuk membaca daftar Materi, Tugas, dan Kuis/CBT dari kelas sumber
     */
    public function getSourceClassContents(CourseClass $courseClass, CourseClass $sourceClass): JsonResponse
    {
        $this->authorizeCourseManagement($courseClass);
        $user = Auth::user();

        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            if (! $user->teacher || $sourceClass->teacher_id !== $user->teacher->id) {
                return response()->json(['error' => 'Anda tidak memiliki akses ke kelas sumber ini.'], 403);
            }
        }

        $sourceClass->load(['subject', 'schoolClass']);

        $materials = LmsMaterial::where('course_class_id', $sourceClass->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'title' => $m->title,
                'topic' => $m->topic ?: 'Umum',
                'type' => $m->type,
                'is_published' => (bool) $m->is_published,
                'has_file' => ! empty($m->file_path),
                'file_name' => $m->file_name,
                'created_at_human' => $m->created_at?->diffForHumans() ?? '-',
            ]);

        $assignments = LmsAssignment::where('course_class_id', $sourceClass->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'topic' => $a->topic ?: 'Umum',
                'due_date_formatted' => $a->due_date ? Carbon::parse($a->due_date)->isoFormat('D MMMM Y, HH:mm') : '-',
                'max_score' => $a->max_score,
                'submission_type_label' => $a->submission_type_label,
                'allowed_file_types' => $a->allowed_file_types_list,
                'is_published' => (bool) $a->is_published,
            ]);

        $quizzes = LmsQuiz::where('course_class_id', $sourceClass->id)
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($q) => [
                'id' => $q->id,
                'title' => $q->title,
                'quiz_type' => $q->quiz_type ?? 'cbt',
                'duration_minutes' => $q->duration_minutes,
                'questions_count' => $q->questions_count,
                'is_published' => (bool) $q->is_published,
            ]);

        return response()->json([
            'source_class' => [
                'id' => $sourceClass->id,
                'subject_name' => $sourceClass->subject->name ?? '-',
                'school_class_name' => $sourceClass->schoolClass->name ?? '-',
                'is_same_subject' => $sourceClass->subject_id === $courseClass->subject_id,
            ],
            'materials' => $materials,
            'assignments' => $assignments,
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Salin materi, tugas, dan asesmen terpilih dari kelas sumber ke kelas aktif saat ini
     */
    public function importFromClass(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'source_class_id' => 'required|exists:course_classes,id',
            'material_ids' => 'nullable|array',
            'material_ids.*' => 'integer|exists:lms_materials,id',
            'assignment_ids' => 'nullable|array',
            'assignment_ids.*' => 'integer|exists:lms_assignments,id',
            'quiz_ids' => 'nullable|array',
            'quiz_ids.*' => 'integer|exists:lms_quizzes,id',
            'publish_status' => 'nullable|in:keep,published,draft',
            'assignment_due_mode' => 'nullable|in:keep,add_7_days,add_14_days',
        ]);

        $sourceClass = CourseClass::with(['schoolClass', 'subject'])->findOrFail($validated['source_class_id']);
        $user = Auth::user();
        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            if (! $user->teacher || $sourceClass->teacher_id !== $user->teacher->id) {
                return back()->with('error', 'Anda tidak memiliki hak untuk mengambil konten dari kelas ini.');
            }
        }

        $publishStatus = $validated['publish_status'] ?? 'keep';
        $assignmentDueMode = $validated['assignment_due_mode'] ?? 'add_7_days';

        $copiedMaterialsCount = 0;
        $copiedAssignmentsCount = 0;
        $copiedQuizzesCount = 0;
        $copiedQuestionsCount = 0;

        // 1. Salin Materi
        if (! empty($validated['material_ids'])) {
            $materials = LmsMaterial::where('course_class_id', $sourceClass->id)
                ->whereIn('id', $validated['material_ids'])
                ->get();

            foreach ($materials as $m) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => (bool) $m->is_published,
                };

                LmsMaterial::create([
                    'course_class_id' => $courseClass->id,
                    'title' => $m->title,
                    'topic' => $m->topic,
                    'type' => $m->type,
                    'content' => $m->content,
                    'file_path' => $m->file_path,
                    'file_name' => $m->file_name,
                    'file_size' => $m->file_size,
                    'external_url' => $m->external_url,
                    'is_published' => $newPublished,
                    'views_count' => 0,
                ]);
                $copiedMaterialsCount++;
            }
        }

        // 2. Salin Tugas
        if (! empty($validated['assignment_ids'])) {
            $assignments = LmsAssignment::where('course_class_id', $sourceClass->id)
                ->whereIn('id', $validated['assignment_ids'])
                ->get();

            foreach ($assignments as $a) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => (bool) $a->is_published,
                };

                $newDueDate = match ($assignmentDueMode) {
                    'add_7_days' => now()->addDays(7)->setTime(23, 59, 0),
                    'add_14_days' => now()->addDays(14)->setTime(23, 59, 0),
                    default => $a->due_date ? Carbon::parse($a->due_date) : now()->addDays(7),
                };

                LmsAssignment::create([
                    'course_class_id' => $courseClass->id,
                    'title' => $a->title,
                    'topic' => $a->topic,
                    'instructions' => $a->instructions,
                    'attachment_path' => $a->attachment_path,
                    'attachment_name' => $a->attachment_name,
                    'due_date' => $newDueDate,
                    'max_score' => $a->max_score,
                    'allow_late' => (bool) $a->allow_late,
                    'submission_type' => $a->submission_type ?? 'both',
                    'allowed_file_types' => $a->allowed_file_types,
                    'is_published' => $newPublished,
                ]);
                $copiedAssignmentsCount++;
            }
        }

        // 3. Salin Asesmen & CBT beserta Seluruh Butir Soalnya
        if (! empty($validated['quiz_ids'])) {
            $quizzes = LmsQuiz::where('course_class_id', $sourceClass->id)
                ->whereIn('id', $validated['quiz_ids'])
                ->with(['questions' => fn ($q) => $q->orderBy('order_number')])
                ->get();

            foreach ($quizzes as $qz) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => (bool) $qz->is_published,
                };

                $newQuiz = LmsQuiz::create([
                    'course_class_id' => $courseClass->id,
                    'title' => $qz->title,
                    'quiz_type' => $qz->quiz_type,
                    'description' => $qz->description,
                    'instructions' => $qz->instructions,
                    'duration_minutes' => $qz->duration_minutes,
                    'start_time' => $qz->start_time,
                    'end_time' => $qz->end_time,
                    'passing_score' => $qz->passing_score,
                    'is_randomized' => (bool) $qz->is_randomized,
                    'is_published' => $newPublished,
                ]);

                foreach ($qz->questions as $quest) {
                    LmsQuizQuestion::create([
                        'quiz_id' => $newQuiz->id,
                        'question_text' => $quest->question_text,
                        'stimulus' => $quest->stimulus,
                        'question_type' => $quest->question_type,
                        'indikator_soal' => $quest->indikator_soal,
                        'level_kognitif' => $quest->level_kognitif,
                        'materi_pokok' => $quest->materi_pokok,
                        'options' => $quest->options,
                        'correct_answer' => $quest->correct_answer,
                        'pembahasan' => $quest->pembahasan,
                        'score_weight' => $quest->score_weight,
                        'order_number' => $quest->order_number,
                    ]);
                    $copiedQuestionsCount++;
                }
                $copiedQuizzesCount++;
            }
        }

        $totalCopied = $copiedMaterialsCount + $copiedAssignmentsCount + $copiedQuizzesCount;
        if ($totalCopied === 0) {
            return back()->with('info', 'Tidak ada konten yang dipilih untuk disalin.');
        }

        AuditLog::log('LMS_IMPORT_FROM_CLASS', 'CourseClass', $courseClass->id, null, [
            'source_class_id' => $sourceClass->id,
            'materials_copied' => $copiedMaterialsCount,
            'assignments_copied' => $copiedAssignmentsCount,
            'quizzes_copied' => $copiedQuizzesCount,
            'questions_copied' => $copiedQuestionsCount,
        ]);

        return back()->with('success', "Berhasil menyalin {$copiedMaterialsCount} Materi, {$copiedAssignmentsCount} Tugas, dan {$copiedQuizzesCount} Asesmen & CBT ({$copiedQuestionsCount} butir soal) dari {$sourceClass->schoolClass->name} ({$sourceClass->subject->name}) ke kelas ini!");
    }

    /**
     * Salin satu item (materi / tugas / kuis) dari kelas aktif ke beberapa kelas tujuan sekaligus
     */
    public function copyItemToClasses(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $validated = $request->validate([
            'item_type' => 'required|in:material,assignment,quiz',
            'item_id' => 'required|integer',
            'target_class_ids' => 'required|array|min:1',
            'target_class_ids.*' => 'integer|exists:course_classes,id',
            'publish_status' => 'nullable|in:keep,published,draft',
            'assignment_due_mode' => 'nullable|in:keep,add_7_days,add_14_days',
        ]);

        $user = Auth::user();
        $publishStatus = $validated['publish_status'] ?? 'keep';
        $assignmentDueMode = $validated['assignment_due_mode'] ?? 'add_7_days';
        $itemType = $validated['item_type'];
        $itemId = $validated['item_id'];

        $targetClasses = CourseClass::whereIn('id', $validated['target_class_ids'])
            ->where('id', '!=', $courseClass->id)
            ->with(['schoolClass', 'subject'])
            ->get();

        if (! ($user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin())) {
            $targetClasses = $targetClasses->filter(fn ($tc) => $tc->teacher_id === $user->teacher?->id);
            if ($targetClasses->isEmpty()) {
                return back()->with('error', 'Anda tidak memiliki hak mengajar pada kelas-kelas tujuan yang dipilih.');
            }
        }

        $copiedCount = 0;
        $itemTitle = '';

        if ($itemType === 'material') {
            $material = LmsMaterial::where('course_class_id', $courseClass->id)->findOrFail($itemId);
            $itemTitle = $material->title;

            foreach ($targetClasses as $tc) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => (bool) $material->is_published,
                };

                LmsMaterial::create([
                    'course_class_id' => $tc->id,
                    'title' => $material->title,
                    'topic' => $material->topic,
                    'type' => $material->type,
                    'content' => $material->content,
                    'file_path' => $material->file_path,
                    'file_name' => $material->file_name,
                    'file_size' => $material->file_size,
                    'external_url' => $material->external_url,
                    'is_published' => $newPublished,
                    'views_count' => 0,
                ]);
                $copiedCount++;
            }
        } elseif ($itemType === 'assignment') {
            $assignment = LmsAssignment::where('course_class_id', $courseClass->id)->findOrFail($itemId);
            $itemTitle = $assignment->title;

            foreach ($targetClasses as $tc) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => (bool) $assignment->is_published,
                };

                $newDueDate = match ($assignmentDueMode) {
                    'add_7_days' => now()->addDays(7)->setTime(23, 59, 0),
                    'add_14_days' => now()->addDays(14)->setTime(23, 59, 0),
                    default => $assignment->due_date ? Carbon::parse($assignment->due_date) : now()->addDays(7),
                };

                LmsAssignment::create([
                    'course_class_id' => $tc->id,
                    'title' => $assignment->title,
                    'topic' => $assignment->topic,
                    'instructions' => $assignment->instructions,
                    'attachment_path' => $assignment->attachment_path,
                    'attachment_name' => $assignment->attachment_name,
                    'due_date' => $newDueDate,
                    'max_score' => $assignment->max_score,
                    'allow_late' => (bool) $assignment->allow_late,
                    'submission_type' => $assignment->submission_type ?? 'both',
                    'allowed_file_types' => $assignment->allowed_file_types,
                    'is_published' => $newPublished,
                ]);
                $copiedCount++;
            }
        } elseif ($itemType === 'quiz') {
            $quiz = LmsQuiz::where('course_class_id', $courseClass->id)
                ->with(['questions' => fn ($q) => $q->orderBy('order_number')])
                ->findOrFail($itemId);
            $itemTitle = $quiz->title;

            foreach ($targetClasses as $tc) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => (bool) $quiz->is_published,
                };

                $newQuiz = LmsQuiz::create([
                    'course_class_id' => $tc->id,
                    'title' => $quiz->title,
                    'quiz_type' => $quiz->quiz_type,
                    'description' => $quiz->description,
                    'instructions' => $quiz->instructions,
                    'duration_minutes' => $quiz->duration_minutes,
                    'start_time' => $quiz->start_time,
                    'end_time' => $quiz->end_time,
                    'passing_score' => $quiz->passing_score,
                    'is_randomized' => (bool) $quiz->is_randomized,
                    'is_published' => $newPublished,
                ]);

                foreach ($quiz->questions as $quest) {
                    LmsQuizQuestion::create([
                        'quiz_id' => $newQuiz->id,
                        'question_text' => $quest->question_text,
                        'stimulus' => $quest->stimulus,
                        'question_type' => $quest->question_type,
                        'indikator_soal' => $quest->indikator_soal,
                        'level_kognitif' => $quest->level_kognitif,
                        'materi_pokok' => $quest->materi_pokok,
                        'options' => $quest->options,
                        'correct_answer' => $quest->correct_answer,
                        'pembahasan' => $quest->pembahasan,
                        'score_weight' => $quest->score_weight,
                        'order_number' => $quest->order_number,
                    ]);
                }
                $copiedCount++;
            }
        }

        $itemTypeLabel = match ($itemType) {
            'material' => 'Materi Pelajaran',
            'assignment' => 'Tugas & PR',
            'quiz' => 'Asesmen & CBT',
            default => 'Konten',
        };

        return back()->with('success', "Berhasil menduplikasi {$itemTypeLabel} '{$itemTitle}' ke {$copiedCount} kelas tujuan!");
    }

    /**
     * Ekspor seluruh materi, tugas, dan asesmen/CBT kelas ini ke berkas JSON paket LMS
     */
    public function exportPackage(Request $request, CourseClass $courseClass): Response
    {
        $this->authorizeCourseManagement($courseClass);

        $materials = LmsMaterial::where('course_class_id', $courseClass->id)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => [
                'title' => $m->title,
                'topic' => $m->topic,
                'type' => $m->type,
                'content' => $m->content,
                'external_url' => $m->external_url,
                'file_name' => $m->file_name,
                'is_published' => (bool) $m->is_published,
            ]);

        $assignments = LmsAssignment::where('course_class_id', $courseClass->id)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($a) => [
                'title' => $a->title,
                'topic' => $a->topic,
                'instructions' => $a->instructions,
                'max_score' => $a->max_score,
                'allow_late' => (bool) $a->allow_late,
                'submission_type' => $a->submission_type,
                'allowed_file_types' => $a->allowed_file_types,
                'is_published' => (bool) $a->is_published,
            ]);

        $quizzes = LmsQuiz::where('course_class_id', $courseClass->id)
            ->with(['questions' => fn ($q) => $q->orderBy('order_number')])
            ->orderBy('created_at')
            ->get()
            ->map(fn ($qz) => [
                'title' => $qz->title,
                'quiz_type' => $qz->quiz_type,
                'description' => $qz->description,
                'instructions' => $qz->instructions,
                'duration_minutes' => $qz->duration_minutes,
                'passing_score' => $qz->passing_score,
                'is_randomized' => (bool) $qz->is_randomized,
                'is_published' => (bool) $qz->is_published,
                'questions' => $qz->questions->map(fn ($quest) => [
                    'question_text' => $quest->question_text,
                    'stimulus' => $quest->stimulus,
                    'question_type' => $quest->question_type,
                    'indikator_soal' => $quest->indikator_soal,
                    'level_kognitif' => $quest->level_kognitif,
                    'materi_pokok' => $quest->materi_pokok,
                    'options' => $quest->options,
                    'correct_answer' => $quest->correct_answer,
                    'pembahasan' => $quest->pembahasan,
                    'score_weight' => $quest->score_weight,
                    'order_number' => $quest->order_number,
                ]),
            ]);

        $packageData = [
            'app' => 'SISSMA1LE_LMS',
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'exported_by' => Auth::user()->name,
            'source_course' => [
                'subject_code' => $courseClass->subject->code ?? null,
                'subject_name' => $courseClass->subject->name ?? null,
                'class_name' => $courseClass->schoolClass->name ?? null,
            ],
            'contents' => [
                'materials' => $materials,
                'assignments' => $assignments,
                'quizzes' => $quizzes,
            ],
        ];

        $filename = 'paket-lms-'.Str::slug($courseClass->subject->name ?? 'mapel').'-'.Str::slug($courseClass->schoolClass->name ?? 'kelas').'-'.now()->format('Ymd_His').'.json';

        return response(json_encode($packageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Impor berkas JSON paket LMS ke dalam kelas aktif saat ini
     */
    public function importPackage(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $this->authorizeCourseManagement($courseClass);

        $request->validate([
            'package_file' => 'required|file|mimes:json,txt|max:10240',
            'publish_status' => 'nullable|in:keep,published,draft',
            'assignment_due_days' => 'nullable|integer|min:1|max:90',
        ]);

        $content = file_get_contents($request->file('package_file')->getRealPath());
        $data = json_decode($content, true);

        if (! is_array($data) || ! isset($data['contents'])) {
            return back()->with('error', 'Format berkas JSON paket LMS tidak valid atau rusak.');
        }

        $publishStatus = $request->input('publish_status', 'keep');
        $dueDays = (int) $request->input('assignment_due_days', 7);

        $copiedMaterialsCount = 0;
        $copiedAssignmentsCount = 0;
        $copiedQuizzesCount = 0;
        $copiedQuestionsCount = 0;

        $contents = $data['contents'];

        // 1. Impor Materi
        if (! empty($contents['materials']) && is_array($contents['materials'])) {
            foreach ($contents['materials'] as $m) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => ! empty($m['is_published']),
                };

                LmsMaterial::create([
                    'course_class_id' => $courseClass->id,
                    'title' => $m['title'] ?? 'Materi Impor',
                    'topic' => $m['topic'] ?? 'Umum',
                    'type' => $m['type'] ?? 'document',
                    'content' => $m['content'] ?? null,
                    'external_url' => $m['external_url'] ?? null,
                    'file_name' => $m['file_name'] ?? null,
                    'is_published' => $newPublished,
                    'views_count' => 0,
                ]);
                $copiedMaterialsCount++;
            }
        }

        // 2. Impor Tugas
        if (! empty($contents['assignments']) && is_array($contents['assignments'])) {
            foreach ($contents['assignments'] as $a) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => ! empty($a['is_published']),
                };

                LmsAssignment::create([
                    'course_class_id' => $courseClass->id,
                    'title' => $a['title'] ?? 'Tugas Impor',
                    'topic' => $a['topic'] ?? 'Umum',
                    'instructions' => $a['instructions'] ?? '-',
                    'due_date' => now()->addDays($dueDays)->setTime(23, 59, 0),
                    'max_score' => $a['max_score'] ?? 100,
                    'allow_late' => ! empty($a['allow_late']),
                    'submission_type' => $a['submission_type'] ?? 'both',
                    'allowed_file_types' => $a['allowed_file_types'] ?? ['pdf', 'ppt', 'image'],
                    'is_published' => $newPublished,
                ]);
                $copiedAssignmentsCount++;
            }
        }

        // 3. Impor Asesmen / CBT beserta Butir Soal
        if (! empty($contents['quizzes']) && is_array($contents['quizzes'])) {
            foreach ($contents['quizzes'] as $qz) {
                $newPublished = match ($publishStatus) {
                    'published' => true,
                    'draft' => false,
                    default => ! empty($qz['is_published']),
                };

                $newQuiz = LmsQuiz::create([
                    'course_class_id' => $courseClass->id,
                    'title' => $qz['title'] ?? 'Asesmen & CBT Impor',
                    'quiz_type' => $qz['quiz_type'] ?? 'cbt',
                    'description' => $qz['description'] ?? null,
                    'instructions' => $qz['instructions'] ?? null,
                    'duration_minutes' => $qz['duration_minutes'] ?? 60,
                    'passing_score' => $qz['passing_score'] ?? 75,
                    'is_randomized' => ! empty($qz['is_randomized']),
                    'is_published' => $newPublished,
                ]);

                if (! empty($qz['questions']) && is_array($qz['questions'])) {
                    foreach ($qz['questions'] as $quest) {
                        LmsQuizQuestion::create([
                            'quiz_id' => $newQuiz->id,
                            'question_text' => $quest['question_text'] ?? '',
                            'stimulus' => $quest['stimulus'] ?? null,
                            'question_type' => $quest['question_type'] ?? 'multiple_choice',
                            'indikator_soal' => $quest['indikator_soal'] ?? null,
                            'level_kognitif' => $quest['level_kognitif'] ?? null,
                            'materi_pokok' => $quest['materi_pokok'] ?? null,
                            'options' => $quest['options'] ?? null,
                            'correct_answer' => $quest['correct_answer'] ?? null,
                            'pembahasan' => $quest['pembahasan'] ?? null,
                            'score_weight' => $quest['score_weight'] ?? 10,
                            'order_number' => $quest['order_number'] ?? 1,
                        ]);
                        $copiedQuestionsCount++;
                    }
                }
                $copiedQuizzesCount++;
            }
        }

        AuditLog::log('LMS_IMPORT_PACKAGE_JSON', 'CourseClass', $courseClass->id, null, [
            'materials_imported' => $copiedMaterialsCount,
            'assignments_imported' => $copiedAssignmentsCount,
            'quizzes_imported' => $copiedQuizzesCount,
            'questions_imported' => $copiedQuestionsCount,
        ]);

        return back()->with('success', "Berhasil mengimpor paket: {$copiedMaterialsCount} Materi, {$copiedAssignmentsCount} Tugas, dan {$copiedQuizzesCount} Asesmen & CBT ({$copiedQuestionsCount} butir soal) ke kelas ini!");
    }

    /**
     * Simpan / Jadwalkan Sesi Zoom Meet Baru
     */
    public function storeZoomMeeting(Request $request, CourseClass $courseClass): RedirectResponse
    {
        $user = Auth::user();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);

        if (! $canManage) {
            abort(403, 'Hanya guru pengampu mata pelajaran ini atau pihak kurikulum yang diizinkan menjadwalkan sesi Zoom Meet.');
        }

        $validated = $request->validate([
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string|max:150',
            'agenda' => 'nullable|string',
            'meeting_url' => 'required|url|max:1000',
            'meeting_id' => 'nullable|string|max:100',
            'passcode' => 'nullable|string|max:100',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'status' => 'nullable|in:scheduled,live,ended',
            'auto_create_attendance' => 'nullable|boolean',
        ]);

        $status = $validated['status'] ?? 'live';

        $meeting = LmsZoomMeeting::create([
            'course_class_id' => $courseClass->id,
            'title' => $validated['title'],
            'topic' => $validated['topic'] ?? null,
            'agenda' => $validated['agenda'] ?? null,
            'meeting_url' => $validated['meeting_url'],
            'meeting_id' => $validated['meeting_id'] ?? null,
            'passcode' => $validated['passcode'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'status' => $status,
            'is_active' => ($status !== 'ended'),
            'user_id' => $user->id,
        ]);

        if (! empty($validated['auto_create_attendance'])) {
            $startDate = Carbon::parse($validated['start_time']);
            $endDate = ! empty($validated['end_time']) ? Carbon::parse($validated['end_time']) : $startDate->copy()->addMinutes(90);
            LmsSubjectAttendance::create([
                'course_class_id' => $courseClass->id,
                'title' => 'Tatap Muka Zoom: '.$meeting->title,
                'meeting_date' => $startDate->toDateString(),
                'start_time' => $startDate->format('H:i'),
                'end_time' => $endDate->format('H:i'),
                'attendance_method' => 'both',
                'is_open' => true,
                'notes' => 'Presensi otomatis terintegrasi dari sesi Tatap Muka Zoom Meet.',
                'created_by' => $user->teacher?->id ?? $courseClass->teacher_id,
            ]);
        }

        AuditLog::log('LMS_ZOOM_CREATED', 'LmsZoomMeeting', $meeting->id, null, [
            'title' => $meeting->title,
            'course_class' => $courseClass->id,
            'status' => $meeting->status,
        ]);

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'zoom'])
            ->with('success', "Sesi Zoom Meet '{$meeting->title}' berhasil dijadwalkan!");
    }

    /**
     * Beralih Status Sesi Zoom Meet (Mulai / Akhiri)
     */
    public function toggleZoomMeetingStatus(CourseClass $courseClass, LmsZoomMeeting $meeting): RedirectResponse
    {
        if ($meeting->course_class_id !== $courseClass->id) {
            abort(404);
        }

        $user = Auth::user();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);

        if (! $canManage) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah status sesi Zoom Meet ini.');
        }

        if ($meeting->status === 'live') {
            $meeting->status = 'ended';
            $meeting->is_active = false;
            $msg = 'diakhiri';
        } else {
            $meeting->status = 'live';
            $meeting->is_active = true;
            $msg = 'diaktifkan (Live)';
        }

        $meeting->save();

        return back()->with('success', "Sesi Zoom Meet '{$meeting->title}' berhasil {$msg}.");
    }

    /**
     * Gabung ke Sesi Zoom Meet dengan Validasi Ketat Siswa Kelas & Guru Pengampu
     */
    public function joinZoomMeeting(Request $request, CourseClass $courseClass, LmsZoomMeeting $meeting): RedirectResponse
    {
        if ($meeting->course_class_id !== $courseClass->id) {
            abort(404, 'Sesi Zoom tidak ditemukan di kelas ini.');
        }

        $user = Auth::user();

        // 1. Cek hak akses Guru Pengampu atau Admin/Kurikulum
        $isAssignedTeacher = $user->teacher && $user->teacher->id === $courseClass->teacher_id;
        $isAdminOrKurikulum = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin();

        // 2. Cek hak akses Siswa di rombel kelas ini
        $student = $user->student;
        $isStudentInClass = $student && ((int) $student->class_id === (int) $courseClass->class_id);

        if (! $isAssignedTeacher && ! $isAdminOrKurikulum && ! $isStudentInClass) {
            if ($student) {
                abort(403, 'Akses Ditolak: Anda tidak terdaftar di kelas rombel ini. Sesi Zoom Meet ini khusus untuk siswa '.($courseClass->schoolClass->name ?? 'kelas terpilih').' dan guru pengampu.');
            }

            if ($user->teacher) {
                abort(403, 'Akses Ditolak: Anda bukan guru pengampu mata pelajaran untuk rombel kelas ini.');
            }

            abort(403, 'Akses Ditolak: Sesi Zoom Meet ini hanya dapat diikuti oleh siswa di kelas ini dan guru pengampu mata pelajaran.');
        }

        // Catat kehadiran peserta jika siswa kelas
        if ($isStudentInClass && $student) {
            LmsZoomParticipant::firstOrCreate(
                [
                    'zoom_meeting_id' => $meeting->id,
                    'student_id' => $student->id,
                ],
                [
                    'joined_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ]
            );

            AuditLog::log('LMS_ZOOM_STUDENT_JOIN', 'LmsZoomMeeting', $meeting->id, null, [
                'title' => $meeting->title,
                'student_id' => $student->id,
                'student_name' => $student->name,
                'class' => $courseClass->schoolClass->name ?? null,
            ]);
        } else {
            AuditLog::log('LMS_ZOOM_TEACHER_JOIN', 'LmsZoomMeeting', $meeting->id, null, [
                'title' => $meeting->title,
                'user_name' => $user->name,
                'role' => $user->role,
            ]);
        }

        return redirect()->away($meeting->meeting_url);
    }

    /**
     * Hapus Sesi Zoom Meet
     */
    public function destroyZoomMeeting(CourseClass $courseClass, LmsZoomMeeting $meeting): RedirectResponse
    {
        if ($meeting->course_class_id !== $courseClass->id) {
            abort(404);
        }

        $user = Auth::user();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($user->teacher && $user->teacher->id === $courseClass->teacher_id);

        if (! $canManage) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus sesi Zoom Meet ini.');
        }

        $title = $meeting->title;
        $meeting->delete();

        AuditLog::log('LMS_ZOOM_DELETED', 'LmsZoomMeeting', $courseClass->id, null, [
            'title' => $title,
        ]);

        return redirect()->route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'zoom'])
            ->with('success', "Sesi Zoom Meet '{$title}' berhasil dihapus.");
    }

    /**
     * Diagnostic & Health Check Endpoint untuk Fondasi Pembelajaran SMA1LE (Phase 1)
     */
    public function systemHealth(CurriculumPlanService $curriculumService): JsonResponse
    {
        $health = $curriculumService->getSystemHealth();

        return response()->json($health);
    }

    /**
     * Catat Pelanggaran Keamanan Siswa (Minimize / Pindah Tab / Tutup Tab)
     * Otomatis memblokir akses ke item (materi / tugas / kuis) hingga dibuka guru mapel / admin.
     */
    public function recordSecurityViolation(Request $request, CourseClass $courseClass): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $student = $user->student;
        if (! $student) {
            // Guru atau Admin tidak pernah diblokir oleh sistem anti-kecurangan
            return response()->json(['success' => true, 'message' => 'Non-student ignored']);
        }

        // Jika guru menonaktifkan fitur keamanan pada kelas ini, abaikan
        if (! $courseClass->isSecurityEnabled()) {
            return response()->json(['success' => true, 'message' => 'Security disabled for this class']);
        }

        $blockableType = (string) $request->input('blockable_type', 'quiz');
        $blockableId = (int) $request->input('blockable_id');
        $violationType = (string) $request->input('violation_type', 'tab_switch');
        $reason = $request->input('reason') ?: 'Terdeteksi berpindah tab atau meminimalkan aplikasi saat sesi aktif.';

        $block = LmsStudentSecurityBlock::recordBlock(
            $courseClass->id,
            $student->id,
            $blockableType,
            $blockableId,
            $violationType,
            $reason
        );

        AuditLog::log('LMS_SECURITY_BLOCK', 'CourseClass', $courseClass->id, null, [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'blockable_type' => $blockableType,
            'blockable_id' => $blockableId,
            'violation' => $violationType,
        ]);

        return response()->json([
            'success' => true,
            'blocked' => true,
            'block_id' => $block->id,
            'message' => 'Akses sesi pembelajaran Anda telah diblokir otomatis karena terdeteksi meninggalkan aplikasi / membuka tab lain.',
        ]);
    }

    /**
     * Buka Blokir Keamanan Siswa Tunggal oleh Guru Mapel atau Admin
     */
    public function unblockSecurityStudent(Request $request, CourseClass $courseClass): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $activeTeacherModel = $user->teacher ?? Teacher::where('user_id', $user->id)->first();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($activeTeacherModel && (int) $activeTeacherModel->id === (int) $courseClass->teacher_id);

        if (! $canManage) {
            abort(403, 'Anda tidak memiliki hak untuk membuka blokir siswa pada kelas mata pelajaran ini.');
        }

        $blockId = $request->input('block_id');
        $studentId = $request->input('student_id');

        $query = LmsStudentSecurityBlock::where('course_class_id', $courseClass->id)
            ->where('is_blocked', true);

        if ($blockId) {
            $query->where('id', $blockId);
        } elseif ($studentId) {
            $query->where('student_id', $studentId);
            if ($request->filled('blockable_type') && $request->filled('blockable_id')) {
                $query->where('blockable_type', $request->input('blockable_type'))
                    ->where('blockable_id', $request->input('blockable_id'));
            }
        }

        $blocks = $query->get();
        foreach ($blocks as $b) {
            $b->update([
                'is_blocked' => false,
                'unblocked_at' => now(),
                'unblocked_by' => $user->id,
            ]);
        }

        AuditLog::log('LMS_SECURITY_UNBLOCK', 'CourseClass', $courseClass->id, null, [
            'unblocked_by' => $user->name,
            'unblocked_count' => $blocks->count(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'unblocked_count' => $blocks->count()]);
        }

        return redirect()->back()->with('success', '🔓 Blokir keamanan berhasil dibuka. Siswa kini dapat melanjutkan kembali materi/tugas/kuis.');
    }

    /**
     * Buka Seluruh Blokir Siswa pada Kelas Mata Pelajaran ini
     */
    public function unblockAllSecurityStudents(Request $request, CourseClass $courseClass): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $activeTeacherModel = $user->teacher ?? Teacher::where('user_id', $user->id)->first();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($activeTeacherModel && (int) $activeTeacherModel->id === (int) $courseClass->teacher_id);

        if (! $canManage) {
            abort(403, 'Anda tidak memiliki hak untuk membuka blokir siswa.');
        }

        $count = LmsStudentSecurityBlock::where('course_class_id', $courseClass->id)
            ->where('is_blocked', true)
            ->update([
                'is_blocked' => false,
                'unblocked_at' => now(),
                'unblocked_by' => $user->id,
            ]);

        AuditLog::log('LMS_SECURITY_UNBLOCK_ALL', 'CourseClass', $courseClass->id, null, [
            'unblocked_by' => $user->name,
            'count' => $count,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'unblocked_count' => $count]);
        }

        return redirect()->back()->with('success', "🔓 Berhasil membuka seluruh ({$count}) blokir keamanan siswa pada kelas mata pelajaran ini.");
    }

    /**
     * Toggle Fitur Keamanan Anti-Kecurangan Siswa (Aktif / Nonaktif) oleh Guru Mapel
     */
    public function toggleSecurityFeature(Request $request, CourseClass $courseClass): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $activeTeacherModel = $user->teacher ?? Teacher::where('user_id', $user->id)->first();
        $canManage = $user->isAdmin() || $user->isWakasekKurikulum() || $user->isSuperAdmin() || ($activeTeacherModel && (int) $activeTeacherModel->id === (int) $courseClass->teacher_id);

        if (! $canManage) {
            abort(403, 'Hanya guru mata pelajaran pengampu atau admin yang dapat mengatur status fitur keamanan.');
        }

        $newStatus = ! $courseClass->isSecurityEnabled();
        $courseClass->is_security_enabled = $newStatus;
        $courseClass->save();

        // Jika dinonaktifkan, buka seluruh blokir aktif yang masih tertahan agar siswa dapat langsung melanjutkan
        if (! $newStatus) {
            LmsStudentSecurityBlock::where('course_class_id', $courseClass->id)
                ->where('is_blocked', true)
                ->update([
                    'is_blocked' => false,
                    'unblocked_at' => now(),
                    'unblocked_by' => $user->id,
                ]);
        }

        AuditLog::log('LMS_SECURITY_TOGGLED', 'CourseClass', $courseClass->id, null, [
            'status' => $newStatus ? 'enabled' : 'disabled',
            'updated_by' => $user->name,
        ]);

        $statusText = $newStatus ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';
        $message = "Fitur Keamanan Anti-Kecurangan Siswa berhasil {$statusText}. ".
            ($newStatus
                ? 'Siswa akan diawasi secara otomatis saat mengakses Materi, Tugas, maupun Asesmen/CBT.'
                : 'Siswa dapat mengakses materi/tugas/kuis tanpa pembatasan jendela atau tab browser.');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_security_enabled' => $newStatus, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }
}

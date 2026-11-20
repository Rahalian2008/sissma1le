<?php

namespace App\Http\Controllers;

use App\Models\BkAkpd;
use App\Models\BkAkpdQuestion;
use App\Models\BkAkpdResponse;
use App\Models\SchoolClass;
use App\Models\SchoolIdentity;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BkAkpdController extends Controller
{
    /**
     * Memverifikasi hak akses siswa: AKPD Diagnostik hanya untuk peserta didik Kelas X (Fase E).
     */
    protected function authorizeStudentGradeX(): ?RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        // Guru, Guru BK, Koordinator BK, Admin, Super Admin, Kepala Sekolah, Wali Kelas bebas akses
        $isStaffOrAdmin = $user->hasAnyRole([
            'admin', 'super_admin', 'guru', 'wali_kelas', 'kesiswaan',
            'wakasek_kesiswaan', 'wakasek_kurikulum', 'wakasek_humas', 'wakasek_sarpras',
            'kepala_sekolah', 'koordinator_bk', 'guru_bk', 'bk',
        ]) || (method_exists($user, 'isCounselor') && $user->isCounselor());

        if ($isStaffOrAdmin) {
            return null;
        }

        if ($user->isStudent() || $user->role === 'siswa') {
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            if (! $student || ! $student->isGradeX()) {
                return redirect()->route('bk.psychological.index')
                    ->with('warning', 'Instrumen AKPD Diagnostik (Angket Kebutuhan Peserta Didik) dikhususkan untuk peserta didik Kelas X (Fase E) untuk pemetaan awal kebutuhan bimbingan. Peserta didik Kelas XI & XII dipersilakan mengakses Tes Psikotes Minat Bakat atau berkonsultasi melalui Ruang Curhat BK.');
            }
        }

        return null;
    }

    public function index(Request $request): View|RedirectResponse
    {
        $redirect = $this->authorizeStudentGradeX();
        if ($redirect) {
            return $redirect;
        }

        $akpds = BkAkpd::withCount(['questions', 'responses'])->latest()->get();
        $totalStudents = Student::active()->count();
        $totalResponses = BkAkpdResponse::count();

        // Needs breakdown across all responses
        $fieldsCount = [
            'PRIBADI' => BkAkpdResponse::where('highest_need_field', 'PRIBADI')->count(),
            'SOSIAL' => BkAkpdResponse::where('highest_need_field', 'SOSIAL')->count(),
            'BELAJAR' => BkAkpdResponse::where('highest_need_field', 'BELAJAR')->count(),
            'KARIER' => BkAkpdResponse::where('highest_need_field', 'KARIER')->count(),
        ];

        $user = Auth::user();
        $answeredAkpdIds = [];
        $studentResponseMap = [];
        $currentStudent = null;

        if ($user && ($user->isStudent() || $user->student || $user->role === 'siswa')) {
            $currentStudent = $user->student ?? Student::where('user_id', $user->id)->first();
            if ($currentStudent) {
                $responses = BkAkpdResponse::where('student_id', $currentStudent->id)->get();
                $answeredAkpdIds = $responses->pluck('akpd_id')->toArray();
                foreach ($responses as $resp) {
                    $studentResponseMap[$resp->akpd_id] = $resp;
                }
            }
        }

        return view('bk.akpd.index', compact(
            'akpds',
            'totalStudents',
            'totalResponses',
            'fieldsCount',
            'answeredAkpdIds',
            'studentResponseMap',
            'currentStudent'
        ));
    }

    public function show(Request $request, BkAkpd $akpd): View|RedirectResponse
    {
        $redirect = $this->authorizeStudentGradeX();
        if ($redirect) {
            return $redirect;
        }

        $user = Auth::user();

        // Jika user adalah siswa, arahkan langsung ke laporan individu miliknya
        if ($user && ($user->isStudent() || $user->role === 'siswa')) {
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            if ($student) {
                $hasResponse = BkAkpdResponse::where('akpd_id', $akpd->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if ($hasResponse) {
                    return redirect()->route('bk.akpd.individual', [$akpd, $student]);
                }

                return redirect()->route('bk.akpd.take', $akpd);
            }
        }

        $akpd->load(['questions', 'responses.student.schoolClass']);

        // Prioritaskan kelas tingkat X sesuai sasaran AKPD
        $gradeXClasses = SchoolClass::where('grade', 'X')
            ->orWhere('name', 'like', 'X %')
            ->orderBy('name')
            ->get();

        $otherClasses = SchoolClass::where('grade', '!=', 'X')
            ->where('name', 'not like', 'X %')
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->query('class_id');
        $responsesQuery = $akpd->responses();

        if ($selectedClassId) {
            $responsesQuery->whereHas('student', fn ($q) => $q->where('class_id', $selectedClassId));
        }

        $responses = $responsesQuery->with('student.schoolClass')->latest()->paginate(20)->withQueryString();

        // Calculate item frequencies (Top 10 highest chosen items)
        $allSelected = [];
        $totalRespondentsFiltered = $selectedClassId
            ? $akpd->responses()->whereHas('student', fn ($q) => $q->where('class_id', $selectedClassId))->count()
            : $akpd->responses()->count();

        $filteredResponses = $selectedClassId
            ? $akpd->responses->filter(fn ($r) => $r->student?->class_id == $selectedClassId)
            : $akpd->responses;

        foreach ($filteredResponses as $resp) {
            $ids = is_array($resp->selected_question_ids) ? $resp->selected_question_ids : [];
            foreach ($ids as $id) {
                $allSelected[$id] = ($allSelected[$id] ?? 0) + 1;
            }
        }

        arsort($allSelected);
        $topQuestionIds = array_slice(array_keys($allSelected), 0, 10);
        $topQuestions = BkAkpdQuestion::whereIn('id', $topQuestionIds)->get()->map(function ($q) use ($allSelected, $totalRespondentsFiltered) {
            $q->chosen_count = $allSelected[$q->id] ?? 0;
            $divisor = max($totalRespondentsFiltered, 1);
            $q->percentage = round(($q->chosen_count / $divisor) * 100, 1);

            return $q;
        })->sortByDesc('chosen_count');

        return view('bk.akpd.show', compact('akpd', 'responses', 'gradeXClasses', 'otherClasses', 'selectedClassId', 'topQuestions', 'totalRespondentsFiltered'));
    }

    public function take(BkAkpd $akpd): View|RedirectResponse
    {
        $redirect = $this->authorizeStudentGradeX();
        if ($redirect) {
            return $redirect;
        }

        $akpd->load('questions');
        $user = Auth::user();

        // Determine student
        $student = null;
        if ($user && ($user->isStudent() || $user->role === 'siswa')) {
            $student = $user->student ?? Student::where('user_id', $user->id)->first() ?? Student::where('id', $user->student_id)->first();
        }

        if (! $student) {
            $student = Student::whereHas('schoolClass', fn ($q) => $q->where('grade', 'X')->orWhere('name', 'like', 'X %'))->first() ?? Student::first();
        }

        $existing = BkAkpdResponse::where('akpd_id', $akpd->id)
            ->where('student_id', $student?->id)
            ->first();

        // Batasi siswa hanya dapat mengisi 1 kali
        $isAdminOrCounselor = $user && (
            $user->hasRole('admin')
            || $user->hasRole('super_admin')
            || (method_exists($user, 'isCounselor') && $user->isCounselor())
            || (method_exists($user, 'canManageBk') && $user->canManageBk())
        );

        if ($existing && ($user?->isStudent() || $user?->role === 'siswa') && ! $isAdminOrCounselor) {
            return redirect()->route('bk.akpd.individual', [$akpd, $student])
                ->with('info', "Anda sudah menyelesaikan pengisian instrumen AKPD [{$akpd->title}]. Berikut adalah Laporan Profil Kebutuhan Individual Anda.");
        }

        return view('bk.akpd.take', compact('akpd', 'student', 'existing'));
    }

    public function submit(Request $request, BkAkpd $akpd): RedirectResponse
    {
        $redirect = $this->authorizeStudentGradeX();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'selected_questions' => ['nullable', 'array'],
            'selected_questions.*' => ['integer', 'exists:bk_akpd_questions,id'],
        ]);

        $user = Auth::user();
        $isAdminOrCounselor = $user && (
            $user->hasRole('admin')
            || $user->hasRole('super_admin')
            || (method_exists($user, 'isCounselor') && $user->isCounselor())
            || (method_exists($user, 'canManageBk') && $user->canManageBk())
        );

        // Cek jika siswa sudah mengisi dan bukan admin/guru BK
        if (($user?->isStudent() || $user?->role === 'siswa') && ! $isAdminOrCounselor) {
            $alreadyExists = BkAkpdResponse::where('akpd_id', $akpd->id)
                ->where('student_id', $validated['student_id'])
                ->first();

            if ($alreadyExists) {
                $student = Student::find($validated['student_id']);

                return redirect()->route('bk.akpd.individual', [$akpd, $student])
                    ->with('info', "Anda sudah mengisi instrumen AKPD [{$akpd->title}] sebelumnya.");
            }
        }

        $selectedIds = $validated['selected_questions'] ?? [];

        // Calculate breakdown by field
        $questions = BkAkpdQuestion::whereIn('id', $selectedIds)->get();
        $totalByField = [
            'PRIBADI' => $akpd->questions()->where('field_of_service', 'PRIBADI')->count() ?: 1,
            'SOSIAL' => $akpd->questions()->where('field_of_service', 'SOSIAL')->count() ?: 1,
            'BELAJAR' => $akpd->questions()->where('field_of_service', 'BELAJAR')->count() ?: 1,
            'KARIER' => $akpd->questions()->where('field_of_service', 'KARIER')->count() ?: 1,
        ];

        $chosenByField = [
            'PRIBADI' => $questions->where('field_of_service', 'PRIBADI')->count(),
            'SOSIAL' => $questions->where('field_of_service', 'SOSIAL')->count(),
            'BELAJAR' => $questions->where('field_of_service', 'BELAJAR')->count(),
            'KARIER' => $questions->where('field_of_service', 'KARIER')->count(),
        ];

        $pctByField = [];
        foreach ($chosenByField as $field => $count) {
            $pctByField[$field] = [
                'count' => $count,
                'total_field' => $totalByField[$field],
                'percentage' => round(($count / $totalByField[$field]) * 100, 1),
            ];
        }

        // Determine highest need
        uasort($pctByField, fn ($a, $b) => $b['percentage'] <=> $a['percentage']);
        $highestField = array_key_first($pctByField) ?? 'PRIBADI';

        BkAkpdResponse::updateOrCreate(
            [
                'akpd_id' => $akpd->id,
                'student_id' => $validated['student_id'],
            ],
            [
                'selected_question_ids' => $selectedIds,
                'score_per_field' => $pctByField,
                'highest_need_field' => $highestField,
                'submitted_at' => now(),
            ]
        );

        $student = Student::find($validated['student_id']);
        AuditTrailService::log('create', 'akpd', "Siswa {$student?->name} mengisi instrumen AKPD {$akpd->title}");

        // Jika siswa mengisi sendiri, langsung arahkan ke Laporan Individu miliknya
        if ($user && ($user->isStudent() || $user->role === 'siswa')) {
            return redirect()->route('bk.akpd.individual', [$akpd, $student])
                ->with('success', "Asesmen AKPD berhasil disimpan! Kebutuhan bimbingan prioritas tertinggi Ananda: Bidang {$highestField}.");
        }

        return redirect()->route('bk.akpd.show', $akpd)
            ->with('success', "Asesmen AKPD untuk {$student?->name} berhasil disimpan. Kebutuhan prioritas: Bidang {$highestField}.");
    }

    /**
     * Laporan Profil Kebutuhan Individual Peserta Didik (Web View).
     */
    public function individualReport(BkAkpd $akpd, Student $student): View|RedirectResponse
    {
        $redirect = $this->authorizeStudentGradeX();
        if ($redirect) {
            return $redirect;
        }

        $user = Auth::user();
        if ($user && ($user->isStudent() || $user->role === 'siswa')) {
            $myStudent = $user->student ?? Student::where('user_id', $user->id)->first();
            if ($myStudent && $myStudent->id !== $student->id) {
                abort(403, 'Anda hanya berhak melihat Laporan Profil AKPD milik Anda sendiri.');
            }
        }

        $akpd->load('questions');
        $student->load('schoolClass');

        $response = BkAkpdResponse::where('akpd_id', $akpd->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $selectedIds = is_array($response->selected_question_ids) ? $response->selected_question_ids : [];
        $selectedQuestions = BkAkpdQuestion::whereIn('id', $selectedIds)
            ->orderBy('field_of_service')
            ->orderBy('sort_order')
            ->get();

        $groupedQuestions = [
            'PRIBADI' => $selectedQuestions->where('field_of_service', 'PRIBADI'),
            'SOSIAL' => $selectedQuestions->where('field_of_service', 'SOSIAL'),
            'BELAJAR' => $selectedQuestions->where('field_of_service', 'BELAJAR'),
            'KARIER' => $selectedQuestions->where('field_of_service', 'KARIER'),
        ];

        $domainAnalysis = $this->computeDomainAnalysis($response, $akpd);
        $recommendations = $this->generateRecommendations($domainAnalysis, $selectedQuestions);

        return view('bk.akpd.individual', compact(
            'akpd',
            'student',
            'response',
            'selectedQuestions',
            'groupedQuestions',
            'domainAnalysis',
            'recommendations'
        ));
    }

    /**
     * Cetak Laporan Profil Kebutuhan Individual Peserta Didik (Format Resmi A4).
     */
    public function printIndividual(BkAkpd $akpd, Student $student): View|RedirectResponse
    {
        $redirect = $this->authorizeStudentGradeX();
        if ($redirect) {
            return $redirect;
        }

        $user = Auth::user();
        if ($user && ($user->isStudent() || $user->role === 'siswa')) {
            $myStudent = $user->student ?? Student::where('user_id', $user->id)->first();
            if ($myStudent && $myStudent->id !== $student->id) {
                abort(403, 'Anda hanya berhak mencetak Laporan Profil AKPD milik Anda sendiri.');
            }
        }

        $school = SchoolIdentity::current();
        $bkSign = Teacher::getBkCoordinator();

        $akpd->load('questions');
        $student->load('schoolClass');

        $response = BkAkpdResponse::where('akpd_id', $akpd->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $selectedIds = is_array($response->selected_question_ids) ? $response->selected_question_ids : [];
        $selectedQuestions = BkAkpdQuestion::whereIn('id', $selectedIds)
            ->orderBy('field_of_service')
            ->orderBy('sort_order')
            ->get();

        $groupedQuestions = [
            'PRIBADI' => $selectedQuestions->where('field_of_service', 'PRIBADI'),
            'SOSIAL' => $selectedQuestions->where('field_of_service', 'SOSIAL'),
            'BELAJAR' => $selectedQuestions->where('field_of_service', 'BELAJAR'),
            'KARIER' => $selectedQuestions->where('field_of_service', 'KARIER'),
        ];

        $domainAnalysis = $this->computeDomainAnalysis($response, $akpd);
        $recommendations = $this->generateRecommendations($domainAnalysis, $selectedQuestions);

        AuditTrailService::log('print', 'akpd', "Mencetak Laporan AKPD Individu Siswa {$student->name}");

        return view('bk.akpd.print_individual', compact(
            'school',
            'bkSign',
            'akpd',
            'student',
            'response',
            'selectedQuestions',
            'groupedQuestions',
            'domainAnalysis',
            'recommendations'
        ));
    }

    /**
     * Laporan Profil Kebutuhan Kelas / Rombel (Web View dengan Visual Chart & Topik RPL).
     */
    public function classReport(Request $request, BkAkpd $akpd): View
    {
        $user = Auth::user();
        if ($user && ($user->isStudent() || $user->role === 'siswa')) {
            abort(403, 'Akses Laporan Kelas hanya untuk Pendidik, Guru BK, dan Administrator.');
        }

        $akpd->load('questions');

        // Daftar kelas tingkat X
        $classes = SchoolClass::where('grade', 'X')
            ->orWhere('name', 'like', 'X %')
            ->orderBy('name')
            ->get();

        if ($classes->isEmpty()) {
            $classes = SchoolClass::orderBy('name')->get();
        }

        $selectedClassId = $request->query('class_id', $classes->first()?->id);
        $selectedClass = SchoolClass::find($selectedClassId) ?? $classes->first();

        // Data statistik kelas
        $classData = $this->buildClassAnalytics($akpd, $selectedClass?->id);

        return view('bk.akpd.class_report', compact(
            'akpd',
            'classes',
            'selectedClass',
            'classData'
        ));
    }

    /**
     * Cetak Laporan Profil Kebutuhan Kelas / Rombel (Format Resmi A4 POP-BK).
     */
    public function printClassReport(Request $request, BkAkpd $akpd): View
    {
        $user = Auth::user();
        if ($user && ($user->isStudent() || $user->role === 'siswa')) {
            abort(403, 'Akses Cetak Laporan Kelas hanya untuk Pendidik, Guru BK, dan Administrator.');
        }

        $school = SchoolIdentity::current();
        $bkSign = Teacher::getBkCoordinator();

        $selectedClassId = $request->query('class_id');
        $selectedClass = $selectedClassId ? SchoolClass::with('homeroomTeacher')->find($selectedClassId) : null;

        $classData = $this->buildClassAnalytics($akpd, $selectedClass?->id);

        AuditTrailService::log('print', 'akpd', "Mencetak Laporan Profil Kebutuhan Kelas AKPD {$selectedClass?->name}");

        return view('bk.akpd.print_class', compact(
            'school',
            'bkSign',
            'akpd',
            'selectedClass',
            'classData'
        ));
    }

    /**
     * Hapus / Reset respons AKPD siswa sehingga siswa dapat mengisi ulang.
     */
    public function destroyResponse(BkAkpd $akpd, BkAkpdResponse $response): RedirectResponse
    {
        $user = Auth::user();
        $canReset = $user && (
            $user->hasAnyRole(['super_admin', 'admin', 'koordinator_bk', 'guru_bk', 'bk'])
            || in_array($user->role, ['super_admin', 'admin', 'koordinator_bk', 'guru_bk', 'bk'], true)
            || (method_exists($user, 'isCounselor') && $user->isCounselor())
        );

        if (! $canReset) {
            abort(403, 'Hanya Super Admin, Admin, Koordinator BK, atau Guru BK yang berhak mereset respons AKPD siswa.');
        }

        $studentName = $response->student?->name ?? 'Siswa';
        $response->delete();

        AuditTrailService::log('delete', 'akpd', "Reset respons AKPD {$akpd->title} untuk siswa {$studentName}");

        return redirect()->route('bk.akpd.show', $akpd)
            ->with('success', "Respons AKPD untuk {$studentName} berhasil di-reset. Siswa kini dapat mengisi ulang instrumen AKPD ini.");
    }

    /**
     * Cetak Analisis Kebutuhan Umum (Format Klasik).
     */
    public function printAnalysis(Request $request, BkAkpd $akpd): View
    {
        return $this->printClassReport($request, $akpd);
    }

    /**
     * Hitung analisis persentase & derajat kebutuhan 4 bidang layanan.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function computeDomainAnalysis(BkAkpdResponse $response, BkAkpd $akpd): array
    {
        $scorePerField = $response->score_per_field ?? [];
        $domains = ['PRIBADI', 'SOSIAL', 'BELAJAR', 'KARIER'];
        $analysis = [];

        foreach ($domains as $domain) {
            $totalField = $akpd->questions()->where('field_of_service', $domain)->count() ?: 10;
            $count = $scorePerField[$domain]['count'] ?? 0;
            $pct = round(($count / $totalField) * 100, 1);

            $degree = 'Rendah';
            $prio = 'Prioritas Rendah';
            $color = 'emerald';
            $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300';
            $desc = 'Kondisi perkembangan stabil, mampu beradaptasi secara mandiri.';

            if ($pct >= 50.0) {
                $degree = 'Sangat Tinggi';
                $prio = 'Prioritas Utama';
                $color = 'rose';
                $badgeClass = 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950 dark:text-rose-300';
                $desc = 'Memerlukan intervensi konseling dan bimbingan terprogram secara intensif.';
            } elseif ($pct >= 26.0) {
                $degree = 'Sedang';
                $prio = 'Perlu Pendampingan';
                $color = 'amber';
                $badgeClass = 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950 dark:text-amber-300';
                $desc = 'Memerlukan pemantauan berkala dan materi bimbingan kelompok/klasikal.';
            }

            $analysis[$domain] = [
                'count' => $count,
                'total_field' => $totalField,
                'percentage' => $pct,
                'degree' => $degree,
                'priority' => $prio,
                'color' => $color,
                'badge' => $badgeClass,
                'description' => $desc,
            ];
        }

        return $analysis;
    }

    /**
     * Rekomendasi layanan BK berbasis POP-BK sesuai butir masalah yang dialami siswa.
     *
     * @param  array<string, array<string, mixed>>  $domainAnalysis
     * @return array<string, array<int, string>>
     */
    protected function generateRecommendations(array $domainAnalysis, Collection $selectedQuestions): array
    {
        $recommendations = [
            'layanan_dasar' => [],
            'layanan_responsif' => [],
            'peminatan_perencanaan' => [],
            'dukungan_sistem' => [],
        ];

        // Layanan Dasar (Bimbingan Klasikal & Kelompok)
        if (($domainAnalysis['BELAJAR']['percentage'] ?? 0) >= 30) {
            $recommendations['layanan_dasar'][] = 'Bimbingan Klasikal: Strategi Manajemen Waktu dan Mengatasi Prokrastinasi Tugas di SMA.';
            $recommendations['layanan_dasar'][] = 'Bimbingan Kelompok: Pembentukan Kelompok Belajar Sebaya (Peer Tutoring) untuk Penguatan Mata Pelajaran Pilihan.';
        }
        if (($domainAnalysis['PRIBADI']['percentage'] ?? 0) >= 30) {
            $recommendations['layanan_dasar'][] = 'Bimbingan Klasikal: Pengenalan Regulasi Emosi, Resiliensi Mental, dan Konsep Diri Positif.';
        }
        if (($domainAnalysis['SOSIAL']['percentage'] ?? 0) >= 30) {
            $recommendations['layanan_dasar'][] = 'Bimbingan Klasikal: Keterampilan Komunikasi Asertif dan Membangun Relasi Sehat Tanpa Perundungan (Anti-Bullying).';
        }
        if (empty($recommendations['layanan_dasar'])) {
            $recommendations['layanan_dasar'][] = 'Bimbingan Klasikal: Pemeliharaan Kebiasaan Belajar Efektif dan Pengembangan Potensi Diri Mandiri.';
        }

        // Layanan Responsif (Konseling Individual & Konsultasi)
        if (($domainAnalysis['PRIBADI']['percentage'] ?? 0) >= 50) {
            $recommendations['layanan_responsif'][] = 'Konseling Individual: Pendampingan intensif pengelolaan beban pikiran, kecemasan, atau konflik emosional.';
        }
        if (($domainAnalysis['SOSIAL']['percentage'] ?? 0) >= 50) {
            $recommendations['layanan_responsif'][] = 'Konseling Individual/Kelompok: Mediasi adaptasi sosial, rasa terasing, atau hambatan pergaulan teman sebaya.';
        }
        if (($domainAnalysis['BELAJAR']['percentage'] ?? 0) >= 50) {
            $recommendations['layanan_responsif'][] = 'Konsultasi Belajar: Diagnosis kesulitan belajar spesifik bersama Guru Mata Pelajaran dan Guru BK.';
        }
        if (empty($recommendations['layanan_responsif'])) {
            $recommendations['layanan_responsif'][] = 'Konsultasi Berkala: Pemantauan umum kemajuan belajar dan adaptasi lingkungan sekolah setiap semester.';
        }

        // Layanan Peminatan & Perencanaan Individual
        if (($domainAnalysis['KARIER']['percentage'] ?? 0) >= 30) {
            $recommendations['peminatan_perencanaan'][] = 'Eksplorasi Peminatan Fase F: Bimbingan pemilihan paket mata pelajaran pilihan kelas XI sesuai minat & bakat.';
            $recommendations['peminatan_perencanaan'][] = 'Orientasi Perguruan Tinggi: Pengenalan jalur seleksi SNBP, SNBT, sekolah kedinasan, dan profil karier modern.';
        } else {
            $recommendations['peminatan_perencanaan'][] = 'Penyusunan Portofolio Prestasi: Dokumentasi capaian akademik dan ekstrakurikuler untuk persiapan masa depan.';
        }

        // Dukungan Sistem
        $recommendations['dukungan_sistem'][] = 'Kolaborasi Wali Kelas: Informasi perkembangan siswa dikomunikasikan secara berkala.';
        $recommendations['dukungan_sistem'][] = 'Parenting & Komunikasi Orang Tua: Edukasi pola asuh suportif untuk mendukung target belajar siswa di rumah.';

        return $recommendations;
    }

    /**
     * Bangun data analitik kelas lengkap dengan persentase rata-rata, top butir, dan rekomendasi topik RPL.
     *
     * @return array<string, mixed>
     */
    protected function buildClassAnalytics(BkAkpd $akpd, ?int $classId): array
    {
        $studentsQuery = Student::active();
        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        } else {
            $studentsQuery->whereHas('schoolClass', fn ($q) => $q->where('grade', 'X')->orWhere('name', 'like', 'X %'));
        }

        $allStudents = $studentsQuery->with('schoolClass')->orderBy('name')->get();
        $totalStudents = $allStudents->count();

        $studentIds = $allStudents->pluck('id')->toArray();
        $responses = BkAkpdResponse::where('akpd_id', $akpd->id)
            ->whereIn('student_id', $studentIds)
            ->with('student.schoolClass')
            ->get();

        $respondentsCount = $responses->count();
        $participationRate = $totalStudents > 0 ? round(($respondentsCount / $totalStudents) * 100, 1) : 0;

        // Rata-rata persentase per bidang
        $fieldTotals = ['PRIBADI' => 0, 'SOSIAL' => 0, 'BELAJAR' => 0, 'KARIER' => 0];
        foreach ($responses as $resp) {
            $scores = $resp->score_per_field ?? [];
            foreach ($fieldTotals as $f => $sum) {
                $fieldTotals[$f] += $scores[$f]['percentage'] ?? 0;
            }
        }

        $fieldAverages = [];
        foreach ($fieldTotals as $f => $sum) {
            $avg = $respondentsCount > 0 ? round($sum / $respondentsCount, 1) : 0;
            $prio = $avg >= 50.0 ? 'Sangat Tinggi' : ($avg >= 26.0 ? 'Sedang' : 'Rendah');
            $color = $avg >= 50.0 ? 'rose' : ($avg >= 26.0 ? 'amber' : 'emerald');

            $fieldAverages[$f] = [
                'percentage' => $avg,
                'priority' => $prio,
                'color' => $color,
            ];
        }

        // Top 10 butir pertanyaan terbanyak
        $questionCounts = [];
        foreach ($responses as $resp) {
            $ids = is_array($resp->selected_question_ids) ? $resp->selected_question_ids : [];
            foreach ($ids as $id) {
                $questionCounts[$id] = ($questionCounts[$id] ?? 0) + 1;
            }
        }

        arsort($questionCounts);
        $topIds = array_slice(array_keys($questionCounts), 0, 10);
        $topQuestions = BkAkpdQuestion::whereIn('id', $topIds)->get()->map(function ($q) use ($questionCounts, $respondentsCount) {
            $q->chosen_count = $questionCounts[$q->id] ?? 0;
            $divisor = max($respondentsCount, 1);
            $q->percentage = round(($q->chosen_count / $divisor) * 100, 1);

            return $q;
        })->sortByDesc('chosen_count');

        // Rekomendasi Topik RPL BK Kelas berdasarkan butir tertinggi
        $suggestedRplTopics = [];
        foreach ($topQuestions->take(5) as $q) {
            $topic = match ($q->field_of_service) {
                'PRIBADI' => "Bimbingan Klasikal: {$q->profile_dimension} - Membangun Regulasi Diri dan Resiliensi Siswa",
                'SOSIAL' => "Bimbingan Klasikal: {$q->profile_dimension} - Keterampilan Relasi Sosial Positif dan Komunikasi Efektif",
                'BELAJAR' => "Bimbingan Klasikal: {$q->profile_dimension} - Manajemen Strategi Belajar Efektif di SMA",
                'KARIER' => "Bimbingan Klasikal: {$q->profile_dimension} - Eksplorasi Arah Peminatan & Karier Masa Depan",
                default => "Bimbingan Klasikal: {$q->statement}",
            };
            $suggestedRplTopics[] = [
                'field' => $q->field_of_service,
                'code' => $q->code,
                'dimension' => $q->profile_dimension,
                'statement' => $q->statement,
                'topic' => $topic,
                'priority' => $q->percentage.'%',
            ];
        }

        // Response map untuk tabel siswa
        $responseMap = [];
        foreach ($responses as $r) {
            $responseMap[$r->student_id] = $r;
        }

        return [
            'totalStudents' => $totalStudents,
            'respondentsCount' => $respondentsCount,
            'participationRate' => $participationRate,
            'fieldAverages' => $fieldAverages,
            'topQuestions' => $topQuestions,
            'suggestedRplTopics' => $suggestedRplTopics,
            'allStudents' => $allStudents,
            'responseMap' => $responseMap,
        ];
    }
}

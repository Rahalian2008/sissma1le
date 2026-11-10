<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\LessonPlan;
use App\Models\QuestionBank;
use App\Models\RemediationEnrichmentPlan;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherReflection;
use App\Models\TeachingJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CurriculumDocumentController extends Controller
{
    /**
     * Portal Pusat Cetak & Arsip Dokumen Kurikulum Merdeka (One-Stop Printing Hub).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $classesQuery = CourseClass::with(['subject', 'schoolClass', 'teacher'])->where('is_active', true);
        if (! $isLeader && $teacher) {
            $classesQuery->where('teacher_id', $teacher->id);
        }
        $courseClasses = $classesQuery->get();

        $selectedClassId = $request->query('course_class_id');
        $selectedCourseClass = $selectedClassId ? CourseClass::with(['subject', 'schoolClass', 'teacher'])->find($selectedClassId) : $courseClasses->first();

        // Statistik Dokumen
        $stats = [
            'total_approved_modules' => LessonPlan::where('status', 'APPROVED')->count(),
            'total_journals' => TeachingJournal::count(),
            'total_questions_tp' => QuestionBank::whereNotNull('learning_objective_id')->count(),
            'total_reflections' => TeacherReflection::count(),
            'total_remediation' => RemediationEnrichmentPlan::count(),
        ];

        return view('lms.documents.index', compact(
            'courseClasses',
            'selectedCourseClass',
            'stats',
            'isLeader'
        ));
    }

    /**
     * Dokumen Cetak Resmi Program Tahunan (PROTA) Kurikulum Merdeka (A4 Landscape).
     */
    public function printProta(Request $request): View
    {
        $classId = $request->query('course_class_id');
        $subjectId = $request->query('subject_id');

        $courseClass = $classId ? CourseClass::with(['subject', 'schoolClass', 'teacher'])->find($classId) : null;
        $subject = $subjectId ? Subject::find($subjectId) : ($courseClass?->subject ?? Subject::first());
        $teacher = $courseClass?->teacher ?? Auth::user()->teacher;

        // Ambil butir TP mapel
        $objectives = LearningObjective::where('subject_id', $subject->id)
            ->where('is_active', true)
            ->orderBy('target_semester')
            ->orderBy('tp_code')
            ->get();

        $ganjilObjectives = $objectives->where('target_semester', 'GANJIL');
        $genapObjectives = $objectives->where('target_semester', 'GENAP');

        $ganjilHours = $ganjilObjectives->sum('hours_allocation');
        $genapHours = $genapObjectives->sum('hours_allocation');
        $totalHours = $objectives->sum('hours_allocation');

        return view('lms.documents.print_prota', compact(
            'subject',
            'courseClass',
            'teacher',
            'ganjilObjectives',
            'genapObjectives',
            'ganjilHours',
            'genapHours',
            'totalHours'
        ));
    }

    /**
     * Dokumen Cetak Resmi Program Semester (PROMES) Kurikulum Merdeka (A4 Landscape).
     */
    public function printPromes(Request $request): View
    {
        $classId = $request->query('course_class_id');
        $semester = strtoupper($request->query('semester', 'GANJIL'));

        $courseClass = $classId ? CourseClass::with(['subject', 'schoolClass', 'teacher'])->find($classId) : null;
        $subject = $courseClass?->subject ?? Subject::first();
        $teacher = $courseClass?->teacher ?? Auth::user()->teacher;

        $objectives = LearningObjective::where('subject_id', $subject->id)
            ->where('is_active', true)
            ->where('target_semester', $semester)
            ->orderBy('tp_code')
            ->get();

        $totalHours = $objectives->sum('hours_allocation');

        // Bulan-bulan semester
        $months = $semester === 'GANJIL'
            ? ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

        return view('lms.documents.print_promes', compact(
            'subject',
            'courseClass',
            'teacher',
            'semester',
            'objectives',
            'totalHours',
            'months'
        ));
    }

    /**
     * Dokumen Cetak Resmi Alur Tujuan Pembelajaran (ATP) Kurikulum Merdeka (A4 Landscape).
     */
    public function printAtp(Request $request): View
    {
        $classId = $request->query('course_class_id');
        $subjectId = $request->query('subject_id');

        $courseClass = $classId ? CourseClass::with(['subject', 'schoolClass', 'teacher'])->find($classId) : null;
        $subject = $subjectId ? Subject::find($subjectId) : ($courseClass?->subject ?? Subject::first());
        $teacher = $courseClass?->teacher ?? Auth::user()->teacher;

        $outcomes = LearningOutcome::where('subject_id', $subject->id)
            ->where('is_active', true)
            ->with(['objectives' => function ($q) {
                $q->where('is_active', true)->orderBy('tp_code');
            }])
            ->get();

        $allObjectives = LearningObjective::where('subject_id', $subject->id)
            ->where('is_active', true)
            ->orderBy('target_semester')
            ->orderBy('tp_code')
            ->get();

        $totalHours = $allObjectives->sum('hours_allocation');

        return view('lms.documents.print_atp', compact(
            'subject',
            'courseClass',
            'teacher',
            'outcomes',
            'allObjectives',
            'totalHours'
        ));
    }

    /**
     * Ekspor Matriks Kesiapan Perangkat Ajar Seluruh Guru (Format CSV Spreadsheet).
     */
    public function exportCurriculumMatrix(): StreamedResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah'])) {
            abort(403, 'Akses terbatas untuk pimpinan dan tim kurikulum.');
        }

        $teachers = Teacher::with(['courseClasses.subject'])->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="matriks_kesiapan_kurikulum_sma1le_'.date('Ymd_His').'.csv"',
        ];

        return new StreamedResponse(function () use ($teachers) {
            $handle = fopen('php://output', 'w');
            // Byte Order Mark (BOM) UTF-8 agar karakter terbaca sempurna di Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Nama Pendidik',
                'NIP',
                'Jumlah Kelas Ampu',
                'Total Modul Ajar',
                'Modul Disahkan (Approved)',
                'Total Jurnal KBM',
                'Jurnal Terverifikasi',
                'Butir Soal TP',
                'Catatan Refleksi',
                'Program Remedial',
                'Indeks Kesiapan (%)',
            ]);

            foreach ($teachers as $idx => $t) {
                $classIds = $t->courseClasses->pluck('id');
                $totalModul = LessonPlan::whereIn('course_class_id', $classIds)->count();
                $approvedModul = LessonPlan::whereIn('course_class_id', $classIds)->where('status', 'APPROVED')->count();
                $totalJournal = TeachingJournal::where('teacher_id', $t->id)->count();
                $verifiedJournal = TeachingJournal::where('teacher_id', $t->id)->where('is_verified_by_kurikulum', true)->count();
                $tpQuestions = QuestionBank::where('teacher_id', $t->id)->whereNotNull('learning_objective_id')->count();
                $totalReflections = TeacherReflection::where('teacher_id', $t->id)->count();
                $totalRemediation = RemediationEnrichmentPlan::where('teacher_id', $t->id)->count();

                $score = 0;
                if ($totalModul > 0) {
                    $score += 25;
                }
                if ($totalJournal > 0) {
                    $score += 25;
                }
                if ($tpQuestions > 0) {
                    $score += 20;
                }
                if ($totalReflections > 0) {
                    $score += 15;
                }
                if ($totalRemediation > 0) {
                    $score += 15;
                }

                fputcsv($handle, [
                    $idx + 1,
                    $t->name,
                    $t->nip ?: '-',
                    $classIds->count(),
                    $totalModul,
                    $approvedModul,
                    $totalJournal,
                    $verifiedJournal,
                    $tpQuestions,
                    $totalReflections,
                    $totalRemediation,
                    $score.'%',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Ekspor Agenda Jurnal Mengajar KBM (Format CSV).
     */
    public function exportJournals(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $isLeader = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kurikulum', 'kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $classId = $request->query('course_class_id');
        $query = TeachingJournal::with(['courseClass.subject', 'courseClass.schoolClass', 'teacher', 'learningObjective'])
            ->latest('date');

        if (! $isLeader && $teacher) {
            $query->where('teacher_id', $teacher->id);
        }

        if ($classId) {
            $query->where('course_class_id', $classId);
        }

        $journals = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="jurnal_mengajar_kbm_'.date('Ymd_His').'.csv"',
        ];

        return new StreamedResponse(function () use ($journals) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Tanggal',
                'Pertemuan Ke',
                'Mata Pelajaran',
                'Kelas',
                'Nama Guru',
                'Tujuan Pembelajaran (TP)',
                'Topik Pembelajaran',
                'Ketercapaian TP',
                'Kehadiran Siswa',
                'Status Supervisi',
            ]);

            foreach ($journals as $idx => $j) {
                fputcsv($handle, [
                    $idx + 1,
                    $j->date ? $j->date->format('Y-m-d') : '-',
                    $j->meeting_number,
                    $j->courseClass->subject->name ?? '-',
                    $j->courseClass->schoolClass->name ?? '-',
                    $j->teacher->name ?? '-',
                    $j->learningObjective ? "TP {$j->learningObjective->tp_code}" : 'Umum',
                    $j->topic,
                    $j->learning_achievement_status,
                    $j->student_attendance_summary ? json_encode($j->student_attendance_summary) : '-',
                    $j->is_verified_by_kurikulum ? 'Terverifikasi' : 'Belum Diverifikasi',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CourseClass;
use App\Models\LmsSubjectAttendance;
use App\Models\SchoolSetting;
use App\Models\TeachingJournal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeachingJournalController extends Controller
{
    /**
     * Halaman Utama Rekap Jurnal Mengajar Harian Guru SMAN 1 Lengkong
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user->isTeacher();
        $isKurikulum = $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $classFilter = $request->query('course_class_id');
        $statusFilter = $request->query('status');

        $classesQuery = CourseClass::with(['subject', 'schoolClass', 'teacher'])->where('is_active', true);
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $classesQuery->where('teacher_id', $teacher->id);
        }
        $myClasses = $classesQuery->get();

        $journalsQuery = TeachingJournal::with(['courseClass.subject', 'courseClass.schoolClass', 'teacher.user', 'learningObjective', 'lessonPlan', 'verifier']);

        if ($isTeacher && ! $isKurikulum && $teacher) {
            $journalsQuery->where('teacher_id', $teacher->id);
        }

        if ($classFilter) {
            $journalsQuery->where('course_class_id', $classFilter);
        }

        if ($statusFilter) {
            $journalsQuery->where('learning_achievement_status', $statusFilter);
        }

        $journals = $journalsQuery->orderByDesc('date')->orderByDesc('meeting_number')->paginate(20);

        // Rekap statistik jurnal
        $statsQuery = TeachingJournal::query();
        if ($isTeacher && ! $isKurikulum && $teacher) {
            $statsQuery->where('teacher_id', $teacher->id);
        }
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'tercapai' => (clone $statsQuery)->where('learning_achievement_status', 'TERCAPAI')->count(),
            'sebagian' => (clone $statsQuery)->where('learning_achievement_status', 'SEBAGIAN_TERCAPAI')->count(),
            'belum' => (clone $statsQuery)->where('learning_achievement_status', 'BELUM_TERCAPAI')->count(),
            'verified' => (clone $statsQuery)->where('is_verified_by_kurikulum', true)->count(),
        ];

        return view('lms.journals.index', compact(
            'journals',
            'myClasses',
            'stats',
            'isKurikulum',
            'isTeacher',
            'classFilter',
            'statusFilter'
        ));
    }

    /**
     * Simpan Jurnal Mengajar Baru Per Pertemuan KBM
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_class_id' => 'required|exists:course_classes,id',
            'date' => 'required|date',
            'meeting_number' => 'required|integer|min:1',
            'time_slot' => 'nullable|string|max:100',
            'topic' => 'required|string|max:255',
            'learning_objective_id' => 'nullable|exists:learning_objectives,id',
            'lesson_plan_id' => 'nullable|exists:lesson_plans,id',
            'subject_attendance_id' => 'nullable|exists:lms_subject_attendances,id',
            'teaching_activities' => 'required|string',
            'hadir_count' => 'nullable|integer|min:0',
            'sakit_count' => 'nullable|integer|min:0',
            'izin_count' => 'nullable|integer|min:0',
            'alfa_count' => 'nullable|integer|min:0',
            'learning_achievement_status' => 'required|in:TERCAPAI,SEBAGIAN_TERCAPAI,BELUM_TERCAPAI',
            'classroom_dynamics_notes' => 'nullable|string',
            'follow_up_action' => 'nullable|string',
        ]);

        $user = Auth::user();
        $courseClass = CourseClass::findOrFail($validated['course_class_id']);

        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum'])) {
            if (! $user->teacher || $courseClass->teacher_id !== $user->teacher->id) {
                abort(403, 'Anda tidak memiliki hak akses mengajar pada kelas ini.');
            }
        }

        $teacherId = $courseClass->teacher_id ?: ($user->teacher?->id);

        $attendanceSummary = [
            'hadir' => $validated['hadir_count'] ?? 0,
            'sakit' => $validated['sakit_count'] ?? 0,
            'izin' => $validated['izin_count'] ?? 0,
            'alfa' => $validated['alfa_count'] ?? 0,
        ];

        // Jika ada relasi presensi kelas, kita bisa ambil rekap otomatisnya jika belum diisi manual
        if (! empty($validated['subject_attendance_id']) && empty($validated['hadir_count'])) {
            $attendance = LmsSubjectAttendance::with('logs')->find($validated['subject_attendance_id']);
            if ($attendance) {
                $logs = $attendance->logs;
                $attendanceSummary = [
                    'hadir' => $logs->where('status', 'HADIR')->count(),
                    'sakit' => $logs->where('status', 'SAKIT')->count(),
                    'izin' => $logs->where('status', 'IZIN')->count(),
                    'alfa' => $logs->where('status', 'ALFA')->count(),
                ];
            }
        }

        $journal = TeachingJournal::create([
            'course_class_id' => $courseClass->id,
            'teacher_id' => $teacherId,
            'lesson_plan_id' => $validated['lesson_plan_id'] ?? null,
            'learning_objective_id' => $validated['learning_objective_id'] ?? null,
            'subject_attendance_id' => $validated['subject_attendance_id'] ?? null,
            'date' => $validated['date'],
            'meeting_number' => $validated['meeting_number'],
            'time_slot' => $validated['time_slot'] ?: '07:30 - 09:00 WIB',
            'topic' => $validated['topic'],
            'teaching_activities' => $validated['teaching_activities'],
            'student_attendance_summary' => $attendanceSummary,
            'learning_achievement_status' => $validated['learning_achievement_status'],
            'classroom_dynamics_notes' => $validated['classroom_dynamics_notes'] ?? null,
            'follow_up_action' => $validated['follow_up_action'] ?? null,
            'is_verified_by_kurikulum' => false,
        ]);

        AuditLog::log('TEACHING_JOURNAL_CREATED', 'TeachingJournal', $journal->id, null, [
            'topic' => $journal->topic,
            'meeting' => $journal->meeting_number,
        ]);

        $redirectRoute = $request->input('redirect_to_course')
            ? route('lms.course', ['courseClass' => $courseClass->id, 'tab' => 'jurnal'])
            : route('lms.journals.index');

        return redirect($redirectRoute)->with('success', "Jurnal mengajar pertemuan ke-{$journal->meeting_number} berhasil dicatat.");
    }

    /**
     * Hapus Catatan Jurnal Mengajar
     */
    public function destroy(TeachingJournal $journal): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum'])) {
            if (! $user->teacher || $journal->teacher_id !== $user->teacher->id) {
                abort(403, 'Anda tidak berhak menghapus jurnal ini.');
            }
        }

        $journal->delete();

        return back()->with('success', 'Jurnal mengajar berhasil dihapus.');
    }

    /**
     * Supervisi & Verifikasi Jurnal Mengajar oleh Kurikulum / Pimpinan
     */
    public function verify(Request $request, TeachingJournal $journal): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'kepala_sekolah'])) {
            abort(403, 'Hanya tim supervisi kurikulum dan pimpinan yang dapat memverifikasi jurnal.');
        }

        $validated = $request->validate([
            'verification_notes' => 'nullable|string',
        ]);

        $journal->update([
            'is_verified_by_kurikulum' => true,
            'verified_by' => $user->id,
            'verified_at' => now(),
            'verification_notes' => $validated['verification_notes'] ?: 'Jurnal KBM terverifikasi dan memenuhi standar pelaksanaan.',
        ]);

        AuditLog::log('TEACHING_JOURNAL_VERIFIED', 'TeachingJournal', $journal->id, null, [
            'verifier' => $user->name,
        ]);

        return back()->with('success', "Jurnal KBM pertemuan ke-{$journal->meeting_number} berhasil diverifikasi.");
    }

    /**
     * Cetak Resmi Agenda & Jurnal Mengajar Guru SMAN 1 Lengkong
     */
    public function print(Request $request): View
    {
        $user = Auth::user();
        $isTeacher = $user->isTeacher();
        $isKurikulum = $user->hasAnyRole(['super_admin', 'admin', 'wakasek_kurikulum', 'kepala_sekolah']);
        $teacher = $user->teacher;

        $classId = $request->query('course_class_id');
        $targetClass = null;

        $journalsQuery = TeachingJournal::with(['courseClass.subject', 'courseClass.schoolClass', 'teacher.user', 'learningObjective', 'lessonPlan', 'verifier']);

        if ($classId) {
            $targetClass = CourseClass::with(['subject', 'schoolClass', 'teacher.user'])->findOrFail($classId);
            if ($isTeacher && ! $isKurikulum && $teacher && $targetClass->teacher_id !== $teacher->id) {
                abort(403, 'Anda tidak memiliki hak akses mencetak jurnal kelas ini.');
            }
            $journalsQuery->where('course_class_id', $classId);
        } elseif ($isTeacher && ! $isKurikulum && $teacher) {
            $journalsQuery->where('teacher_id', $teacher->id);
        }

        $journals = $journalsQuery->orderBy('date')->orderBy('meeting_number')->get();

        $settings = SchoolSetting::whereIn('key', [
            'school_name',
            'school_kop',
            'school_address',
            'school_principal',
            'principal_nip',
            'wakasek_kurikulum_name',
            'wakasek_kurikulum_nip',
        ])->pluck('value', 'key');

        return view('lms.journals.print', compact('journals', 'targetClass', 'settings', 'teacher'));
    }
}

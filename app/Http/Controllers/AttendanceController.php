<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\AttendanceSession;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\SchoolHoliday;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Tampilan Presensi Selfie (Kamera Langsung Realtime & GPS Geofencing)
     * STRICT BUSINESS RULE: Kamera aktif perangkat wajib, dilarang upload file / galeri.
     */
    public function selfie(): View
    {
        $user = Auth::user();
        $student = $user->student ?? Student::first();
        $location = AttendanceLocation::primary();
        $schedule = AttendanceService::getScheduleForDay(Carbon::now());

        $settings = [
            'school_name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'school_lat' => (float) ($location->latitude ?? SchoolSetting::get('school_lat', -7.56845)),
            'school_lng' => (float) ($location->longitude ?? SchoolSetting::get('school_lng', 112.04612)),
            'radius_meters' => (int) ($location->radius_meters ?? SchoolSetting::get('geofence_radius_meters', 100)),
            'start_time' => $schedule['in_start'],
            'checkin_time' => $schedule['in_on_time'],
            'late_cutoff_time' => $schedule['in_late_cutoff'],
            'out_start' => $schedule['out_start'],
            'out_end' => $schedule['out_end'],
            'end_time' => $schedule['out_end'],
            'is_enabled' => SchoolSetting::get('enable_attendance_selfie', '1') === '1',
            'is_active_day' => $schedule['is_active'],
            'is_holiday' => $schedule['is_holiday'] ?? false,
            'holiday_name' => $schedule['holiday_name'] ?? null,
            'holiday_type' => $schedule['holiday_type'] ?? null,
            'day_name' => ucfirst($schedule['day']),
        ];

        $today = Carbon::today()->toDateString();
        $existingAttendance = $student ? Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first() : null;

        $currentTime = Carbon::now()->format('H:i:s');
        $isCheckoutTime = $currentTime >= $schedule['out_start'];

        return view('attendance.selfie', compact('student', 'settings', 'existingAttendance', 'schedule', 'isCheckoutTime'));
    }

    /**
     * Simpan Presensi Selfie dari Kamera Langsung
     */
    public function storeSelfie(Request $request): JsonResponse
    {
        $user = Auth::user();
        $student = $user->student ?? Student::first();

        $validated = $request->validate([
            'selfie_image' => ['required', 'string'], // base64 stream from live canvas
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'device_info' => ['nullable', 'string'],
            'liveness_score' => ['nullable', 'numeric'],
            'session_type' => ['nullable', 'string'],
        ]);

        $result = $this->attendanceService->processSelfieAttendance($student, $validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Tampilan Scanner QR Code Siswa
     */
    public function qrScanner(): View
    {
        $user = Auth::user();
        $student = $user->student ?? Student::first();
        $isEnabled = SchoolSetting::get('enable_attendance_qr', '1') === '1';

        return view('attendance.qr_scanner', compact('student', 'isEnabled'));
    }

    /**
     * Proses Scan QR Code oleh Siswa
     */
    public function storeQr(Request $request): JsonResponse
    {
        $user = Auth::user();
        $student = $user->student ?? Student::first();

        $validated = $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $result = $this->attendanceService->processQrAttendance($student, $validated['qr_token']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Halaman Proyektor / Pembuat Sesi QR Code Guru
     */
    public function qrSession(Request $request): View
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        $classes = SchoolClass::all();

        $activeSessions = AttendanceSession::with('schoolClass')
            ->where('date', Carbon::today()->toDateString())
            ->orderByDesc('created_at')
            ->get();

        $selectedSession = $request->has('session_id')
            ? AttendanceSession::with(['schoolClass', 'attendances.student'])->find($request->session_id)
            : $activeSessions->first();

        return view('attendance.qr_session', compact('teacher', 'classes', 'activeSessions', 'selectedSession'));
    }

    /**
     * Buat Sesi Presensi QR Baru oleh Guru/Admin
     */
    public function createQrSession(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['nullable', 'exists:school_classes,id'],
            'subject_or_activity' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:240'],
        ]);

        $user = Auth::user();
        $teacher = $user->teacher;
        $now = Carbon::now();

        $session = AttendanceSession::create([
            'class_id' => $validated['class_id'] ?? null,
            'subject_or_activity' => $validated['subject_or_activity'],
            'teacher_id' => $teacher?->id,
            'date' => $now->toDateString(),
            'start_time' => $now->format('H:i:s'),
            'end_time' => $now->copy()->addMinutes($validated['duration_minutes'])->format('H:i:s'),
            'qr_code_token' => 'SMA1LE_'.strtoupper(Str::random(24)),
            'qr_expires_at' => $now->copy()->addMinutes($validated['duration_minutes']),
            'status' => 'ACTIVE',
        ]);

        AuditLog::log('CREATE_QR_SESSION', 'AttendanceSession', $session->id, null, [
            'subject' => $session->subject_or_activity,
        ]);

        return redirect()->route('attendance.qr.session', ['session_id' => $session->id])
            ->with('success', 'Sesi presensi QR dinamis berhasil dibuat dan siap ditampilkan.');
    }

    /**
     * Simulator & Pengujian Scanner RFID
     */
    public function rfidSimulator(): View
    {
        $devices = AttendanceDevice::all();
        $students = Student::with('schoolClass')->get();
        $isEnabled = SchoolSetting::get('enable_attendance_rfid', '1') === '1';
        $todayLogs = Attendance::where('method', 'RFID')
            ->where('date', Carbon::today()->toDateString())
            ->orderByDesc('created_at')
            ->take(15)
            ->get();

        return view('attendance.rfid_simulator', compact('devices', 'students', 'todayLogs', 'isEnabled'));
    }

    /**
     * Endpoint API Scan RFID dari Perangkat Fisik / Simulator
     */
    public function scanRfid(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rfid_uid' => ['required', 'string'],
            'device_code' => ['nullable', 'string'],
        ]);

        $result = $this->attendanceService->processRfidAttendance($validated['rfid_uid'], $validated['device_code'] ?? 'RFID-GATE-01');

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Riwayat Presensi dengan filter
     */
    public function history(Request $request): View
    {
        $user = Auth::user();
        $query = Attendance::with(['student.schoolClass', 'schoolClass'])->orderByDesc('date')->orderByDesc('time');

        if ($user->role === 'siswa') {
            $student = $user->student ?? Student::first();
            $query->where('student_id', $student?->id);
            $classes = collect();
            $classId = $student?->class_id;
        } elseif ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            $classId = $request->input('class_id', $class?->id);
            $classes = $class ? collect([$class]) : collect();
            if ($classId) {
                $query->where('class_id', $classId);
            }
        } elseif ($user->role === 'orang_tua') {
            $student = $user->parentProfile?->student ?? Student::first();
            $query->where('student_id', $student?->id);
            $classes = collect();
            $classId = $student?->class_id;
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $classId = $request->input('class_id');
            if ($classId) {
                $query->where('class_id', $classId);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(20)->withQueryString();

        return view('attendance.history', compact('attendances', 'classes', 'classId'));
    }

    /**
     * Laporan Presensi Lengkap & Real-time
     */
    public function report(Request $request): View
    {
        $user = Auth::user();
        $date = $request->input('date', Carbon::today()->toDateString());

        $query = Attendance::with(['student.schoolClass', 'selfie'])
            ->whereDate('date', $date);

        if ($user->role === 'siswa') {
            $student = $user->student ?? Student::first();
            $query->where('student_id', $student?->id);
            $classes = collect();
            $classId = $student?->class_id;
        } elseif ($user->role === 'wali_kelas') {
            $class = $user->teacher?->homeroomClass;
            $classId = $class?->id;
            $classes = $class ? collect([$class]) : collect();
            if ($class) {
                $query->where('class_id', $class->id);
            }
        } elseif ($user->role === 'orang_tua') {
            $student = $user->parentProfile?->student ?? Student::first();
            $query->where('student_id', $student?->id);
            $classes = collect();
            $classId = $student?->class_id;
        } else {
            $classes = SchoolClass::all();
            $classId = $request->input('class_id');
            if ($classId) {
                $query->where('class_id', $classId);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('time')->paginate(25)->withQueryString();

        // Real-time summary metrics for that date
        $statsQuery = Attendance::whereDate('date', $date);
        if ($classId) {
            $statsQuery->where('class_id', $classId);
        }

        $totalActiveStudents = Student::where('is_active', true)
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->count();

        $stats = [
            'date' => $date,
            'total_students' => $totalActiveStudents,
            'total_recorded' => (clone $statsQuery)->count(),
            'hadir' => (clone $statsQuery)->where('status', 'HADIR')->count(),
            'terlambat' => (clone $statsQuery)->where('status', 'TERLAMBAT')->count(),
            'izin' => (clone $statsQuery)->where('status', 'IZIN')->count(),
            'sakit' => (clone $statsQuery)->where('status', 'SAKIT')->count(),
            'alpa' => (clone $statsQuery)->where('status', 'ALPA')->count(),
        ];

        $totalPresent = $stats['hadir'] + $stats['terlambat'];
        $stats['percentage'] = $stats['total_students'] > 0
            ? round(($totalPresent / $stats['total_students']) * 100, 1)
            : 0;

        return view('attendance.report', compact('attendances', 'classes', 'classId', 'stats', 'date'));
    }

    /**
     * Verifikasi Presensi Mencurigakan / Perlu Verifikasi
     */
    public function verify(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'verification_status' => ['required', 'in:VALID,DIVERIFIKASI,DITOLAK'],
            'status' => ['required', 'in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA,DITOLAK'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance->update([
            'verification_status' => $validated['verification_status'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $attendance->notes,
        ]);

        AuditLog::log('VERIFY_ATTENDANCE', 'Attendance', $attendance->id, null, [
            'status' => $validated['status'],
            'verification_status' => $validated['verification_status'],
        ]);

        return back()->with('success', 'Status presensi siswa berhasil diverifikasi.');
    }

    /**
     * Tampilan Ajuan Izin, Sakit & Pulang Cepat Siswa
     * Wajib kamera langsung (anti-upload) & realtime GPS (tanpa batas radius geofencing).
     */
    public function leaveForm(Request $request): View
    {
        $user = Auth::user();
        $student = $user->student ?? Student::first();
        $location = AttendanceLocation::primary();

        $settings = [
            'school_name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'school_lat' => (float) ($location->latitude ?? SchoolSetting::get('school_lat', -7.56845)),
            'school_lng' => (float) ($location->longitude ?? SchoolSetting::get('school_lng', 112.04612)),
        ];

        $today = Carbon::today()->toDateString();
        $existingAttendance = Attendance::where('student_id', $student?->id)
            ->where('date', $today)
            ->first();

        $defaultType = strtoupper($request->query('type', 'IZIN'));
        if (! in_array($defaultType, ['IZIN', 'SAKIT', 'PULANG_CEPAT'])) {
            $defaultType = 'IZIN';
        }

        $todayHoliday = SchoolHoliday::getHoliday($today);
        $activeHolidays = SchoolHoliday::active()->where('end_date', '>=', $today)->orderBy('start_date')->take(10)->get();

        $historyLeaves = Attendance::where('student_id', $student?->id)
            ->where(function ($q) {
                $q->where('method', 'IZIN_SAKIT')
                    ->orWhereIn('status', ['IZIN', 'SAKIT', 'PULANG_CEPAT'])
                    ->orWhere('is_early_leave', true);
            })
            ->orderByDesc('date')
            ->take(10)
            ->get();

        return view('attendance.leave', compact('student', 'settings', 'existingAttendance', 'historyLeaves', 'defaultType', 'todayHoliday', 'activeHolidays'));
    }

    /**
     * Proses Kirim Ajuan Izin, Sakit & Pulang Cepat Siswa
     */
    public function storeLeave(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $student = $user->student ?? Student::first();

        if (! $student) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Profil siswa tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Profil siswa tidak ditemukan.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:IZIN,SAKIT,PULANG_CEPAT'],
            'selfie_image' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'notes' => ['required', 'string', 'max:1000'],
            'date' => ['nullable', 'date'],
            'early_leave_time' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $result = $this->attendanceService->submitLeaveRequest(
            $student,
            $validated,
            $request->file('attachment')
        );

        if ($request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()->route('attendance.leave')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    /**
     * Daftar Approval Ajuan Izin, Sakit & Pulang Cepat (Wali Kelas, Admin, Super Admin)
     */
    public function leaveApprovals(Request $request): View
    {
        $user = Auth::user();
        $statusFilter = $request->input('status', 'PERLU_VERIFIKASI');
        $classId = $request->input('class_id');
        $homeroomClass = null;

        if ($user->role === 'wali_kelas') {
            $homeroomClass = $user->teacher?->homeroomClass;
            $classes = $homeroomClass ? collect([$homeroomClass]) : collect();
            $classId = $homeroomClass?->id;
        } else {
            $classes = SchoolClass::all();
        }

        $query = Attendance::with(['student.schoolClass', 'selfie', 'approver'])
            ->where(function ($q) {
                $q->where('method', 'IZIN_SAKIT')
                    ->orWhereIn('status', ['IZIN', 'SAKIT', 'PULANG_CEPAT'])
                    ->orWhere('is_early_leave', true);
            });

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($statusFilter !== 'ALL') {
            $query->where('verification_status', $statusFilter);
        }

        $leaves = $query->orderByDesc('date')->orderByDesc('time')->paginate(15)->withQueryString();

        $leaveCondition = fn ($q) => $q->where('method', 'IZIN_SAKIT')
            ->orWhereIn('status', ['IZIN', 'SAKIT', 'PULANG_CEPAT'])
            ->orWhere('is_early_leave', true);

        $stats = [
            'pending' => Attendance::where($leaveCondition)
                ->where('verification_status', 'PERLU_VERIFIKASI')
                ->when($classId, fn ($q) => $q->where('class_id', $classId))
                ->count(),
            'approved' => Attendance::where($leaveCondition)
                ->where('verification_status', 'DIVERIFIKASI')
                ->when($classId, fn ($q) => $q->where('class_id', $classId))
                ->count(),
            'rejected' => Attendance::where($leaveCondition)
                ->where('verification_status', 'DITOLAK')
                ->when($classId, fn ($q) => $q->where('class_id', $classId))
                ->count(),
        ];

        return view('attendance.leave_approvals', compact('leaves', 'classes', 'homeroomClass', 'statusFilter', 'classId', 'stats'));
    }

    /**
     * Setujui Ajuan Izin & Sakit Siswa
     */
    public function approveLeave(Attendance $attendance): RedirectResponse
    {
        if (Auth::user()->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        $result = $this->attendanceService->approveLeave($attendance, Auth::user());

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Tolak Ajuan Izin & Sakit Siswa
     */
    public function rejectLeave(Request $request, Attendance $attendance): RedirectResponse
    {
        if (Auth::user()->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $result = $this->attendanceService->rejectLeave($attendance, Auth::user(), $validated['reason']);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Form Absen Manual (Wali Kelas, Admin, Super Admin)
     */
    public function manualForm(Request $request): View
    {
        $user = Auth::user();
        if ($user->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        $homeroomClass = null;

        if ($user->role === 'wali_kelas') {
            $homeroomClass = $user->teacher?->homeroomClass;
            $classes = $homeroomClass ? collect([$homeroomClass]) : collect();
            $selectedClassId = $homeroomClass?->id;
        } else {
            $classes = SchoolClass::all();
            $selectedClassId = $request->input('class_id', $classes->first()?->id);
        }

        $students = $selectedClassId
            ? Student::where('class_id', $selectedClassId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        $recentManuals = Attendance::with(['student.schoolClass', 'approver'])
            ->where('method', 'MANUAL')
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->take(10)
            ->get();

        return view('attendance.manual', compact('classes', 'homeroomClass', 'selectedClassId', 'students', 'recentManuals'));
    }

    /**
     * Simpan Absen Manual Siswa
     */
    public function storeManual(Request $request): RedirectResponse
    {
        if (Auth::user()->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->attendanceService->recordManualAttendance($validated, Auth::user());

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}

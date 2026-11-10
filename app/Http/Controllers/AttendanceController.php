<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDevice;
use App\Models\AttendanceLocation;
use App\Models\AttendanceSession;
use App\Models\AuditLog;
use App\Models\EarlyLeaveRequest;
use App\Models\PicketSchedule;
use App\Models\SchoolClass;
use App\Models\SchoolExitPermit;
use App\Models\SchoolHoliday;
use App\Models\SchoolIdentity;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Dashboard Presensi Terpadu Utama (Desain Komprehensif Berbasis Tab Seperti Shalat Dzuhur)
     * Mengintegrasikan Scanner RFID & QR, Presensi Kamera Selfie, Absen Manual Rombel,
     * Pengajuan Izin/Sakit/Pulang Cepat, Persetujuan Ajuan, dan Riwayat Presensi Real-Time.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $selectedDate = $request->query('date', $today);
        $selectedDateCarbon = Carbon::parse($selectedDate, 'Asia/Jakarta');
        $selectedDateFormatted = $selectedDateCarbon->locale('id')->translatedFormat('l, d F Y');
        $todayFormatted = Carbon::parse($today, 'Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y');

        $isStudent = $user->isStudent();
        $activeTab = $request->query('tab', $isStudent ? 'selfie' : 'scanner');

        $student = $user->student ?? Student::where('is_active', true)->first();
        $isHomeroom = $user->hasRole('wali_kelas');
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        $isSecretary = $user->hasRole('sekretaris_kelas');
        $canRecordManual = $user->canRecordAttendanceManual();
        $canApproveLeaves = $user->hasAnyRole(['wali_kelas', 'guru', 'kesiswaan', 'admin', 'super_admin']);

        if ($activeTab === 'manual' && ! $canRecordManual) {
            $activeTab = $isStudent ? 'selfie' : 'scanner';
        }

        // Kelola Daftar Kelas yang dapat diakses
        $isGlobalAdmin = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kesiswaan', 'kesiswaan']);
        if (! $isGlobalAdmin && $isHomeroom && $homeroomClass) {
            $classes = collect([$homeroomClass]);
            $selectedClassId = $request->query('class_id', $homeroomClass->id);
        } elseif (! $isGlobalAdmin && $isSecretary && $user->student?->schoolClass) {
            $classes = collect([$user->student->schoolClass]);
            $selectedClassId = $request->query('class_id', $user->student->class_id);
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->query('class_id');
        }

        // Konfigurasi Jadwal & Status Waktu Presensi Harian
        $schedule = AttendanceService::getScheduleForDay($selectedDateCarbon);
        $now = Carbon::now('Asia/Jakarta');
        $currentTime = $now->format('H:i');
        $currentDayName = $now->locale('id')->translatedFormat('l');
        $currentTimeFormatted = $now->locale('id')->translatedFormat('l, d F Y • H:i').' WIB';

        $inStart = $schedule['in_start'];
        $inOnTime = $schedule['in_on_time'];
        $inLateCutoff = $schedule['in_late_cutoff'];
        $outStart = $schedule['out_start'];
        $outEnd = $schedule['out_end'];

        // Validasi Sesi Terbuka / Sesi Terkunci
        $isToday = $selectedDate === $today;
        $isHoliday = ! empty($schedule['is_holiday']);
        $isActiveDay = ! empty($schedule['is_active']);

        $isWithinInSession = $isToday && $currentTime >= $inStart && $currentTime <= $inLateCutoff;
        $isWithinOutSession = $isToday && $currentTime >= $outStart && $currentTime <= $outEnd;
        $isAllowedTime = $isToday && $isActiveDay && ($isWithinInSession || $isWithinOutSession);

        $sessionPhase = 'TERKUNCI';
        if ($isToday && $isActiveDay) {
            if ($currentTime >= $inStart && $currentTime <= $inOnTime) {
                $sessionPhase = 'MASUK_TEPAT_WAKTU';
            } elseif ($currentTime > $inOnTime && $currentTime <= $inLateCutoff) {
                $sessionPhase = 'MASUK_TERLAMBAT';
            } elseif ($currentTime >= $outStart && $currentTime <= $outEnd) {
                $sessionPhase = 'PULANG';
            }
        }

        $timeValidation = [
            'allowed' => $isAllowedTime,
            'current_time' => $currentTime,
            'current_day_name' => $currentDayName,
            'current_date' => $today,
            'current_day_date' => $currentTimeFormatted,
            'current_date_formatted' => $selectedDateFormatted,
            'session_phase' => $sessionPhase,
            'is_holiday' => $isHoliday,
            'holiday_name' => $schedule['holiday_name'] ?? null,
            'is_active_day' => $isActiveDay,
        ];

        // Target Siswa
        $targetStudentsQuery = Student::with('schoolClass')->where('is_active', true)->orderBy('name');
        if ($selectedClassId) {
            $targetStudentsQuery->where('class_id', $selectedClassId);
        }
        $targetStudents = $targetStudentsQuery->get();
        $totalTarget = $targetStudents->count();

        // Otomatisasi Status ALPA saat Jadwal Presensi Dibuka:
        // Status otomatis alpa akan mulai tampil disaat jadwal presensi dibuka (currentTime >= in_start).
        // Siswa yang tidak/belum melaksanakan absen atau pengajuan izin/sakitnya belum diapprove otomatis berstatus ALPA.
        if ($isActiveDay && ! $isHoliday) {
            if (! $isToday || ($isToday && $currentTime >= $inStart)) {
                $this->attendanceService->ensureDailyAttendanceRecords($selectedDate);
            }
        }

        // Data Presensi pada Tanggal & Kelas Terpilih
        $dayAttendancesQuery = Attendance::with(['student.schoolClass', 'selfie', 'approver'])
            ->whereDate('date', $selectedDate);
        if ($selectedClassId) {
            $dayAttendancesQuery->where('class_id', $selectedClassId);
        }

        $selectedMethod = $request->input('method');
        if (! empty($selectedMethod)) {
            $upperMethod = strtoupper(trim($selectedMethod));
            if ($upperMethod === 'BARCODE' || $upperMethod === 'QR_CODE') {
                $dayAttendancesQuery->whereIn('method', ['BARCODE', 'QR_CODE']);
            } elseif ($upperMethod === 'SELFIE') {
                $dayAttendancesQuery->whereIn('method', ['SELFIE', 'SELFIE_CAMERA']);
            } elseif ($upperMethod === 'RFID') {
                $dayAttendancesQuery->where('method', 'RFID');
            } elseif ($upperMethod === 'FACE_RECOGNITION' || $upperMethod === 'FACE') {
                $dayAttendancesQuery->where('method', 'FACE_RECOGNITION');
            } elseif ($upperMethod === 'MANUAL') {
                $dayAttendancesQuery->whereIn('method', ['MANUAL', 'IZIN_SAKIT']);
            } else {
                $dayAttendancesQuery->where('method', $upperMethod);
            }
        }

        $dayAttendances = $dayAttendancesQuery->get();
        $attendanceByStudentId = $dayAttendances->keyBy('student_id');

        // Statistik Presensi Terpadu
        $totalHadir = $dayAttendances->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $totalTepatWaktu = $dayAttendances->where('status', 'HADIR')->count();
        $totalTerlambat = $dayAttendances->where('status', 'TERLAMBAT')->count();
        $totalIzinSakit = $dayAttendances->whereIn('status', ['IZIN', 'SAKIT', 'UDZUR'])->count();
        $totalPulangCepat = $dayAttendances->where('status', 'PULANG_CEPAT')->count();
        $totalAlpa = $dayAttendances->where('status', 'ALPA')->count();
        $totalBelum = max(0, $totalTarget - ($totalHadir + $totalIzinSakit + $totalPulangCepat + $totalAlpa));
        $percentHadir = $totalTarget > 0 ? round(($totalHadir / $totalTarget) * 100, 1) : 0;

        // Data Tab 1: Scanner Hardware & Feed
        $devices = AttendanceDevice::orderBy('device_code')->get();
        $recentLogs = Attendance::with('student.schoolClass')
            ->whereDate('date', $today)
            ->orderByDesc('updated_at')
            ->take(25)
            ->get();

        // Pengaturan Hardware & Geofence
        $enableRfid = SchoolSetting::get('enable_attendance_rfid', '1') === '1';
        $enableBarcode = (SchoolSetting::get('enable_attendance_barcode', '1') === '1' || SchoolSetting::get('enable_attendance_qr', '1') === '1');
        $enableQr = $enableBarcode;
        $enableSelfie = SchoolSetting::get('enable_attendance_selfie', '1') === '1';
        $enableFaceRecognition = SchoolSetting::get('enable_attendance_face_recognition', '1') === '1';
        $enableManual = SchoolSetting::get('enable_attendance_manual', '1') === '1';

        $location = AttendanceLocation::primary();
        $schoolLat = (float) ($location->latitude ?? SchoolSetting::get('school_lat', -7.56845));
        $schoolLng = (float) ($location->longitude ?? SchoolSetting::get('school_lng', 112.04612));
        $geofenceRadius = (int) ($location->radius_meters ?? SchoolSetting::get('geofence_radius_meters', 100));

        // Data Tab 4: Pengajuan Izin / Pulang Cepat
        $defaultLeaveType = strtoupper($request->query('type', 'IZIN'));
        if (! in_array($defaultLeaveType, ['IZIN', 'SAKIT', 'PULANG_CEPAT'])) {
            $defaultLeaveType = 'IZIN';
        }
        $existingAttendance = $student ? Attendance::where('student_id', $student->id)->whereDate('date', $today)->first() : null;
        $historyLeaves = $student ? Attendance::where('student_id', $student->id)
            ->where(function ($q) {
                $q->where('method', 'IZIN_SAKIT')
                    ->orWhereIn('status', ['IZIN', 'SAKIT', 'PULANG_CEPAT'])
                    ->orWhereNotNull('requested_status')
                    ->orWhere('is_early_leave', true);
            })
            ->orderByDesc('date')
            ->take(10)
            ->get() : collect();

        // Data Tab 5: Persetujuan Izin & Pulang (Wali Kelas & Admin)
        $leaveApprovalQuery = Attendance::with(['student.schoolClass', 'selfie', 'approver'])
            ->where(function ($q) {
                $q->where('method', 'IZIN_SAKIT')
                    ->orWhereIn('status', ['IZIN', 'SAKIT', 'PULANG_CEPAT'])
                    ->orWhereNotNull('requested_status')
                    ->orWhere('is_early_leave', true);
            });
        if ($isHomeroom && $homeroomClass) {
            $leaveApprovalQuery->where('class_id', $homeroomClass->id);
        } elseif ($selectedClassId) {
            $leaveApprovalQuery->where('class_id', $selectedClassId);
        }

        $pendingLeaves = (clone $leaveApprovalQuery)->where('verification_status', 'PERLU_VERIFIKASI')->orderByDesc('date')->orderByDesc('time')->get();
        $recentApprovedLeaves = (clone $leaveApprovalQuery)->whereIn('verification_status', ['DIVERIFIKASI', 'DITOLAK'])->orderByDesc('updated_at')->take(10)->get();

        $settings = [
            'school_name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'school_lat' => $schoolLat,
            'school_lng' => $schoolLng,
        ];
        $targetClassIdForHoliday = $student ? $student->class_id : ($selectedClassId ?? null);
        $todayHoliday = SchoolHoliday::getHoliday($today, $targetClassIdForHoliday);

        // Siapkan seluruh siswa aktif & identitas sekolah untuk modal ajuan pulang cepat & izin keluar
        $allStudents = Student::where('is_active', true)->with('schoolClass')->orderBy('name')->get();
        $school = SchoolIdentity::current();
        $canManagePermits = $user ? $user->canManageSchoolPermits() : false;

        // Data Laporan & Persetujuan Pulang Cepat & Izin Keluar Lingkungan Sekolah
        $todayEarlyLeaves = Schema::hasTable('early_leave_requests')
            ? EarlyLeaveRequest::with(['student.schoolClass', 'approver', 'creator'])
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->take(30)
                ->get()
            : collect();

        $todayExitPermits = Schema::hasTable('school_exit_permits')
            ? SchoolExitPermit::with(['students.schoolClass', 'approver', 'creator'])
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->take(30)
                ->get()
            : collect();

        $pendingEarlyLeavesCount = Schema::hasTable('early_leave_requests')
            ? EarlyLeaveRequest::where('status', 'PENDING')->count()
            : 0;

        $pendingExitPermitsCount = Schema::hasTable('school_exit_permits')
            ? SchoolExitPermit::where('status', 'PENDING')->count()
            : 0;

        $todayPickets = Schema::hasTable('picket_schedules')
            ? PicketSchedule::getTodayPickets($selectedDateCarbon)
            : collect();

        $weeklyPickets = Schema::hasTable('picket_schedules')
            ? PicketSchedule::active()->with(['teacher', 'user'])->orderBy('day_number')->orderBy('role_type')->get()->groupBy('day_name')
            : collect();

        $allPicketTeachers = Teacher::where('is_active', true)->orderBy('name')->get();
        $allPicketStaff = User::whereIn('role', ['staff', 'tu', 'tu_piket', 'admin'])->orderBy('name')->get();

        return view('attendance.index', compact(
            'user',
            'isStudent',
            'student',
            'isHomeroom',
            'homeroomClass',
            'isSecretary',
            'canRecordManual',
            'canApproveLeaves',
            'canManagePermits',
            'todayEarlyLeaves',
            'todayExitPermits',
            'pendingEarlyLeavesCount',
            'pendingExitPermitsCount',
            'classes',
            'allStudents',
            'school',
            'selectedClassId',
            'today',
            'todayFormatted',
            'selectedDate',
            'selectedDateFormatted',
            'activeTab',
            'schedule',
            'timeValidation',
            'targetStudents',
            'totalTarget',
            'dayAttendances',
            'attendanceByStudentId',
            'totalHadir',
            'totalTepatWaktu',
            'totalTerlambat',
            'totalIzinSakit',
            'totalPulangCepat',
            'totalAlpa',
            'totalBelum',
            'percentHadir',
            'devices',
            'recentLogs',
            'enableRfid',
            'enableQr',
            'enableBarcode',
            'enableSelfie',
            'enableFaceRecognition',
            'enableManual',
            'schoolLat',
            'schoolLng',
            'geofenceRadius',
            'defaultLeaveType',
            'existingAttendance',
            'historyLeaves',
            'pendingLeaves',
            'recentApprovedLeaves',
            'settings',
            'todayHoliday',
            'selectedMethod',
            'todayPickets',
            'weeklyPickets',
            'allPicketTeachers',
            'allPicketStaff'
        ));
    }

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

        $isGeofenceEnabled = SchoolSetting::get('enable_geofence_radius', '1') === '1';

        $settings = [
            'school_name' => SchoolSetting::get('school_name', 'SMA Negeri 1 Lengkong'),
            'school_lat' => (float) ($location->latitude ?? SchoolSetting::get('school_lat', -7.56845)),
            'school_lng' => (float) ($location->longitude ?? SchoolSetting::get('school_lng', 112.04612)),
            'radius_meters' => (int) ($location->radius_meters ?? SchoolSetting::get('geofence_radius_meters', 100)),
            'is_geofence_enabled' => $isGeofenceEnabled,
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
        if (SchoolSetting::get('enable_attendance_selfie', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Metode Presensi Selfie Kamera sedang dinonaktifkan oleh kebijakan sekolah.',
            ], 403);
        }

        $user = Auth::user();
        $student = $user->student ?? Student::first();

        $isGeofenceEnabled = SchoolSetting::get('enable_geofence_radius', '1') === '1';

        $validated = $request->validate([
            'selfie_image' => ['required', 'string'], // base64 stream from live canvas
            'latitude' => [$isGeofenceEnabled ? 'required' : 'nullable', 'numeric'],
            'longitude' => [$isGeofenceEnabled ? 'required' : 'nullable', 'numeric'],
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
        $student = $user?->student;
        $isEnabled = SchoolSetting::get('enable_attendance_qr', '1') === '1';
        $devices = AttendanceDevice::qr()->orderBy('device_code')->get();
        if ($devices->isEmpty()) {
            $devices = AttendanceDevice::orderBy('device_code')->get();
        }

        $todayScans = Attendance::where('method', 'QR_CODE')
            ->where('date', Carbon::today()->toDateString())
            ->with(['student.schoolClass'])
            ->orderByDesc('updated_at')
            ->take(10)
            ->get();

        $schedule = AttendanceService::getScheduleForDay(Carbon::now('Asia/Jakarta'));

        return view('attendance.qr_scanner', compact('student', 'isEnabled', 'devices', 'todayScans', 'schedule'));
    }

    /**
     * Proses Scan QR Code oleh Siswa / Petugas
     */
    public function storeQr(Request $request): JsonResponse
    {
        if (SchoolSetting::get('enable_attendance_qr', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Metode Presensi QR Code sedang dinonaktifkan oleh kebijakan sekolah.',
            ], 403);
        }

        $user = Auth::user();
        $student = $user?->student;

        $validated = $request->validate([
            'qr_token' => ['required', 'string'],
            'device_code' => ['nullable', 'string'],
        ]);

        $meta = [
            'device_code' => $validated['device_code'] ?? null,
            'device_info' => $validated['device_code'] ?? request()->userAgent(),
        ];

        $result = $this->attendanceService->processQrAttendance($student, $validated['qr_token'], $meta);

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
        if (SchoolSetting::get('enable_attendance_qr', '1') !== '1') {
            return back()->with('error', 'Metode Presensi QR Code sedang dinonaktifkan oleh kebijakan sekolah.');
        }

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
        $devices = AttendanceDevice::rfid()->orderBy('device_code')->get();
        if ($devices->isEmpty()) {
            $devices = AttendanceDevice::orderBy('device_code')->get();
        }
        $students = Student::with('schoolClass')->get();
        $isEnabled = SchoolSetting::get('enable_attendance_rfid', '1') === '1';
        $todayLogs = Attendance::where('method', 'RFID')
            ->where('date', Carbon::today()->toDateString())
            ->orderByDesc('created_at')
            ->take(15)
            ->get();

        $schedule = AttendanceService::getScheduleForDay(Carbon::now('Asia/Jakarta'));

        return view('attendance.rfid_simulator', compact('devices', 'students', 'todayLogs', 'isEnabled', 'schedule'));
    }

    /**
     * Endpoint API Scan RFID dari Perangkat Fisik / Simulator
     */
    public function scanRfid(Request $request): JsonResponse
    {
        if (SchoolSetting::get('enable_attendance_rfid', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Metode Presensi Kartu RFID sedang dinonaktifkan oleh kebijakan sekolah.',
            ], 403);
        }

        $validated = $request->validate([
            'rfid_uid' => ['required', 'string'],
            'device_code' => ['nullable', 'string'],
        ]);

        $result = $this->attendanceService->processRfidAttendance($validated['rfid_uid'], $validated['device_code'] ?? 'RFID-GATE-01');

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Endpoint API Scan Barcode Siswa (Scanner USB / Barcode Reader Sekolah)
     */
    public function scanBarcode(Request $request): JsonResponse
    {
        if (SchoolSetting::get('enable_attendance_barcode', '1') !== '1' && SchoolSetting::get('enable_attendance_qr', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Metode Presensi Barcode Scanner sedang dinonaktifkan oleh kebijakan sekolah.',
                'error_code' => 'METHOD_DISABLED',
            ], 422);
        }

        $validated = $request->validate([
            'barcode' => ['required', 'string'],
            'device_code' => ['nullable', 'string'],
        ]);

        $result = $this->attendanceService->processBarcodeAttendance($validated['barcode'], $validated['device_code'] ?? 'BARCODE-SCAN-01');

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Endpoint API Scan Face Recognition Siswa (Terminal AI Wajah Sekolah)
     */
    public function scanFaceRecognition(Request $request): JsonResponse
    {
        if (SchoolSetting::get('enable_attendance_face_recognition', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Metode Presensi Face Recognition sedang dinonaktifkan oleh kebijakan sekolah.',
                'error_code' => 'METHOD_DISABLED',
            ], 422);
        }

        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'device_code' => ['nullable', 'string'],
        ]);

        $result = $this->attendanceService->processFaceRecognitionAttendance($validated['identifier'], $validated['device_code'] ?? 'FACE-CAM-01');

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Riwayat Presensi dengan filter
     */
    public function history(Request $request): View
    {
        $this->attendanceService->ensureDailyAttendanceRecords();
        $user = Auth::user();
        $query = Attendance::with(['student.schoolClass', 'schoolClass'])->orderByDesc('date')->orderByDesc('time');

        $isStaff = $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'bk', 'kesiswaan', 'kepala_sekolah', 'koordinator_keagamaan']);
        $isStudentUser = ($user->hasRole('siswa') || $user->hasRole('sekretaris_kelas') || $user->role === 'siswa') && ! $isStaff;
        $student = null;

        if ($isStudentUser) {
            // Akun siswa hanya menampilkan riwayat presensi dirinya sendiri
            $student = $user->student ?? Student::where('user_id', $user->id)
                ->orWhere('nis', $user->username)
                ->orWhere('nisn', $user->username)
                ->first();

            if ($student) {
                $query->where('student_id', $student->id);
                $classId = $student->class_id;
            } else {
                $query->whereRaw('1 = 0');
                $classId = null;
            }
            $classes = collect();
        } elseif (($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin'])) {
            $classes = $user->getHomeroomClasses();
            $requestedClassId = $request->input('class_id');
            if ($requestedClassId && $classes->contains('id', (int) $requestedClassId)) {
                $classId = (int) $requestedClassId;
            } else {
                $classId = $classes->first()?->id;
            }
            if ($classId) {
                $query->where('class_id', $classId);
            } elseif ($classes->isNotEmpty()) {
                $query->whereIn('class_id', $classes->pluck('id'));
            }
        } elseif ($user->hasRole('orang_tua') || $user->role === 'orang_tua') {
            $student = $user->parentProfile?->student;
            if ($student) {
                $query->where('student_id', $student->id);
                $classId = $student->class_id;
            } else {
                $query->whereRaw('1 = 0');
                $classId = null;
            }
            $classes = collect();
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
            $selectedMethod = strtoupper(trim($request->method));
            if ($selectedMethod === 'BARCODE' || $selectedMethod === 'QR_CODE') {
                $query->whereIn('method', ['BARCODE', 'QR_CODE']);
            } elseif ($selectedMethod === 'SELFIE') {
                $query->whereIn('method', ['SELFIE', 'SELFIE_CAMERA']);
            } elseif ($selectedMethod === 'RFID') {
                $query->where('method', 'RFID');
            } elseif ($selectedMethod === 'FACE_RECOGNITION' || $selectedMethod === 'FACE') {
                $query->where('method', 'FACE_RECOGNITION');
            } elseif ($selectedMethod === 'MANUAL') {
                $query->whereIn('method', ['MANUAL', 'IZIN_SAKIT']);
            } else {
                $query->where('method', $selectedMethod);
            }
        }

        // Pencarian nama/NIS siswa hanya diizinkan untuk staf sekolah agar siswa tidak bisa mengintip akun lain
        if (! $isStudentUser && $request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(20)->withQueryString();

        return view('attendance.history', compact('attendances', 'classes', 'classId', 'isStudentUser', 'student'));
    }

    /**
     * Laporan Presensi Lengkap & Real-time
     */
    public function report(Request $request): View
    {
        $user = Auth::user();
        $date = $request->input('date', Carbon::today()->toDateString());
        $this->attendanceService->ensureDailyAttendanceRecords($date);

        $query = Attendance::with(['student.schoolClass', 'selfie'])
            ->whereDate('date', $date);

        $isStaff = $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'bk', 'kesiswaan', 'kepala_sekolah', 'koordinator_keagamaan']);
        $isSecretary = $user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas']);
        $isStudentUser = ($user->hasRole('siswa') || $user->role === 'siswa') && ! $isStaff && ! $isSecretary;

        if ($isStudentUser) {
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            if ($student) {
                $query->where('student_id', $student->id);
                $classId = $student->class_id;
            } else {
                $query->whereRaw('1 = 0');
                $classId = null;
            }
            $classes = collect();
        } elseif (($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin'])) {
            $classes = $user->getHomeroomClasses();
            $requestedClassId = $request->input('class_id');
            if ($requestedClassId && $classes->contains('id', (int) $requestedClassId)) {
                $classId = (int) $requestedClassId;
            } else {
                $classId = $classes->first()?->id;
            }
            if ($classId) {
                $query->where('class_id', $classId);
            } elseif ($classes->isNotEmpty()) {
                $query->whereIn('class_id', $classes->pluck('id'));
            }
        } elseif ($isSecretary) {
            // Sekretaris Kelas dapat memantau presensi harian seluruh siswa di kelasnya sendiri (Read-Only)
            $student = $user->student ?? Student::where('user_id', $user->id)->first();
            $class = $student?->schoolClass;
            $classId = $class?->id;
            $classes = $class ? collect([$class]) : collect();
            if ($classId) {
                $query->where('class_id', $classId);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->hasRole('orang_tua') || $user->role === 'orang_tua') {
            $student = $user->parentProfile?->student;
            if ($student) {
                $query->where('student_id', $student->id);
                $classId = $student->class_id;
            } else {
                $query->whereRaw('1 = 0');
                $classId = null;
            }
            $classes = collect();
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
            $selectedMethod = strtoupper(trim($request->method));
            if ($selectedMethod === 'BARCODE' || $selectedMethod === 'QR_CODE') {
                $query->whereIn('method', ['BARCODE', 'QR_CODE']);
            } elseif ($selectedMethod === 'SELFIE') {
                $query->whereIn('method', ['SELFIE', 'SELFIE_CAMERA']);
            } elseif ($selectedMethod === 'RFID') {
                $query->where('method', 'RFID');
            } elseif ($selectedMethod === 'FACE_RECOGNITION' || $selectedMethod === 'FACE') {
                $query->where('method', 'FACE_RECOGNITION');
            } elseif ($selectedMethod === 'MANUAL') {
                $query->whereIn('method', ['MANUAL', 'IZIN_SAKIT']);
            } else {
                $query->where('method', $selectedMethod);
            }
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

        return view('attendance.report', compact('attendances', 'classes', 'classId', 'stats', 'date', 'isSecretary'));
    }

    /**
     * Verifikasi Presensi Mencurigakan / Perlu Verifikasi
     */
    public function verify(Request $request, Attendance $attendance): RedirectResponse
    {
        $user = Auth::user();
        if ($user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'guru', 'wali_kelas', 'bk', 'kesiswaan'])) {
            abort(403, 'Akses Ditolak: Akun Sekretaris Kelas hanya berhak melihat (monitoring) laporan presensi, tidak berhak memverifikasi atau mengubah data.');
        }

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

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        if (! Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Akses Ditolak: Hanya Admin atau Super Admin yang dapat mengubah data secara penuh.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA,DITOLAK'],
            'time' => ['nullable', 'date_format:H:i'],
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
            'verification_status' => ['nullable', 'in:VALID,DIVERIFIKASI,DITOLAK,PERLU_VERIFIKASI'],
        ]);

        $timeVal = $validated['time'] ?? $validated['time_in'] ?? null;
        $timeData = ! empty($timeVal) ? (strlen($timeVal) === 5 ? $timeVal.':00' : $timeVal) : $attendance->time;
        $timeOutVal = $validated['time_out'] ?? null;
        $timeOutData = ! empty($timeOutVal) ? (strlen($timeOutVal) === 5 ? $timeOutVal.':00' : $timeOutVal) : $attendance->time_out;

        $updateData = [
            'status' => $validated['status'],
            'time' => $timeData,
            'time_out' => $timeOutData,
            'notes' => $validated['notes'] ?? $attendance->notes,
        ];
        if (! empty($validated['verification_status'])) {
            $updateData['verification_status'] = $validated['verification_status'];
        } else {
            $updateData['verification_status'] = 'DIVERIFIKASI';
        }

        $attendance->update($updateData);

        AuditLog::log('UPDATE_ATTENDANCE', 'Attendance', $attendance->id, null, $validated);

        return back()->with('success', 'Data presensi berhasil diperbarui oleh Administrator.');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        if (! Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Akses Ditolak: Hanya Admin atau Super Admin yang dapat menghapus data.');
        }

        AuditLog::log('DELETE_ATTENDANCE', 'Attendance', $attendance->id, null, $attendance->toArray());
        $attendance->delete();

        return back()->with('success', 'Data presensi berhasil dihapus.');
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

        $todayHoliday = SchoolHoliday::getHoliday($today, $student?->class_id);
        $activeHolidays = SchoolHoliday::active()
            ->where('end_date', '>=', $today)
            ->orderBy('start_date')
            ->take(15)
            ->get()
            ->filter(fn ($h) => $student ? $h->appliesToClass($student->class_id) : true)
            ->values();

        $historyLeaves = Attendance::where('student_id', $student?->id)
            ->where(function ($q) {
                $q->where('method', 'IZIN_SAKIT')
                    ->orWhereIn('status', ['IZIN', 'SAKIT', 'PULANG_CEPAT'])
                    ->orWhereNotNull('requested_status')
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
        $student = null;
        if ($request->filled('student_id')) {
            $student = Student::find($request->student_id);
        }
        if (! $student) {
            $student = $user->student ?? Student::first();
        }

        if (! $student) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Profil siswa tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Profil siswa tidak ditemukan.');
        }

        if ($request->filled('type') && ! $request->filled('status')) {
            $request->merge(['status' => $request->input('type')]);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:IZIN,SAKIT,PULANG_CEPAT'],
            'selfie_image' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'notes' => ['required', 'string', 'max:1000'],
            'date' => ['nullable', 'date'],
            'early_leave_time' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment');
        }

        if (empty($validated['latitude']) || empty($validated['longitude'])) {
            $validated['latitude'] = (float) SchoolSetting::get('school_lat', -7.56845);
            $validated['longitude'] = (float) SchoolSetting::get('school_lng', 112.04612);
        }

        $result = $this->attendanceService->submitLeaveRequest(
            $student,
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            $referer = $request->header('referer', '');
            if (str_contains($referer, '/attendance') && ! str_contains($referer, '/attendance/leave')) {
                return redirect()->route('attendance.index', ['tab' => 'leave'])->with('success', $result['message']);
            }

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

        if ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'kesiswaan'])) {
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
                    ->orWhereNotNull('requested_status')
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
            ->orWhereNotNull('requested_status')
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
        if (Auth::user()->hasRole('kepala_sekolah') && ! Auth::user()->hasAnyRole(['admin', 'super_admin', 'wali_kelas', 'guru'])) {
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
        if (Auth::user()->hasRole('kepala_sekolah') && ! Auth::user()->hasAnyRole(['admin', 'super_admin', 'wali_kelas', 'guru'])) {
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
     * Mendukung: Absensi Masal Per Rombel/Kelas & Absensi Per Siswa
     */
    public function manualForm(Request $request): View
    {
        $user = Auth::user();
        if ($user->hasRole('kepala_sekolah') && ! $user->hasAnyRole(['admin', 'super_admin', 'wali_kelas', 'guru'])) {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        if (! $user->canRecordAttendanceManual()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses form presensi manual Presensi Utama.');
        }

        $homeroomClass = null;

        // Prioritas: admin/super_admin dapat lihat semua, wali_kelas dibatasi kelas sendiri,
        // sekretaris_kelas dibatasi kelas sendiri (dari data siswa)
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->input('class_id', $classes->first()?->id);
        } elseif ($user->hasRole('wali_kelas')) {
            $homeroomClass = $user->teacher?->homeroomClass;
            $classes = $homeroomClass ? collect([$homeroomClass]) : collect();
            $selectedClassId = $homeroomClass?->id;
        } elseif ($user->hasRole('sekretaris_kelas')) {
            $studentClass = $user->student?->schoolClass;
            $classes = $studentClass ? collect([$studentClass]) : collect();
            $selectedClassId = $studentClass?->id;
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->input('class_id', $classes->first()?->id);
        }

        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;

        $students = $selectedClassId
            ? Student::with('schoolClass')->where('class_id', $selectedClassId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        // Ambil data presensi yang sudah tercatat pada tanggal dan kelas terpilih
        $attendances = Attendance::where('class_id', $selectedClassId)
            ->whereDate('date', $selectedDate)
            ->get()
            ->keyBy('student_id');

        // Statistik Kehadiran Kelas Hari Ini
        $totalStudents = $students->count();
        $totalHadir = $attendances->where('status', 'HADIR')->count();
        $totalTerlambat = $attendances->where('status', 'TERLAMBAT')->count();
        $totalIzin = $attendances->where('status', 'IZIN')->count();
        $totalSakit = $attendances->where('status', 'SAKIT')->count();
        $totalAlpa = $attendances->where('status', 'ALPA')->count();
        $totalBelum = max(0, $totalStudents - ($totalHadir + $totalTerlambat + $totalIzin + $totalSakit + $totalAlpa));

        $stats = [
            'total' => $totalStudents,
            'hadir' => $totalHadir,
            'terlambat' => $totalTerlambat,
            'izin' => $totalIzin,
            'sakit' => $totalSakit,
            'alpa' => $totalAlpa,
            'belum' => $totalBelum,
        ];

        $recentManuals = Attendance::with(['student.schoolClass', 'approver'])
            ->where('method', 'MANUAL')
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->take(10)
            ->get();

        $allStudents = Student::with('schoolClass')
            ->where('is_active', true)
            ->when($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']), function ($q) use ($homeroomClass) {
                $q->where('class_id', $homeroomClass?->id);
            })
            ->when($user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']), function ($q) use ($user) {
                $q->where('class_id', $user->student?->class_id);
            })
            ->orderBy('name')
            ->get();

        $isSecretary = $user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'wali_kelas']);
        $isEnabled = SchoolSetting::get('enable_attendance_manual', '1') === '1';
        $schedule = AttendanceService::getScheduleForDay(Carbon::parse($selectedDate, 'Asia/Jakarta'));

        return view('attendance.manual', compact(
            'classes',
            'homeroomClass',
            'selectedClass',
            'selectedClassId',
            'selectedDate',
            'students',
            'allStudents',
            'attendances',
            'stats',
            'recentManuals',
            'isSecretary',
            'isEnabled',
            'schedule'
        ));
    }

    /**
     * Simpan Absen Manual Siswa
     */
    public function storeManual(Request $request): RedirectResponse
    {
        if (SchoolSetting::get('enable_attendance_manual', '1') !== '1') {
            return back()->with('error', 'Metode presensi manual sedang dinonaktifkan oleh kebijakan sekolah.');
        }

        $user = Auth::user();
        $targetStudent = Student::find($request->student_id);
        $targetClassId = $targetStudent?->class_id;

        if (! $user->canRecordAttendanceManual($targetClassId)) {
            abort(403, 'Akses Ditolak: Fitur absen manual presensi hanya dapat dilaksanakan oleh Wakasek Kesiswaan, Wali Kelas pada kelasnya, atau Sekretaris Kelas pada kelasnya.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA'],
            'notes' => ['nullable', 'string', 'max:500'],
            'selfie_image' => ['nullable', 'string'],
        ]);

        $payload = $validated;
        if ($request->hasFile('selfie_file') && $request->file('selfie_file')->isValid()) {
            $payload['selfie_image'] = $request->file('selfie_file');
        }

        $result = $this->attendanceService->recordManualAttendance($payload, Auth::user());

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Simpan Absensi Manual Masal Per Rombel / Kelas (Absen Harian Rombel)
     */
    public function storeManualBulk(Request $request): RedirectResponse
    {
        if (SchoolSetting::get('enable_attendance_manual', '1') !== '1') {
            return back()->with('error', 'Metode presensi manual sedang dinonaktifkan oleh kebijakan sekolah.');
        }

        $user = Auth::user();
        if ($user->role === 'kepala_sekolah') {
            abort(403, 'Akun Kepala Sekolah bersifat monitoring.');
        }

        if (! $user->canRecordAttendanceManual()) {
            abort(403, 'Anda tidak memiliki wewenang untuk melakukan absensi manual.');
        }

        $validated = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'statuses' => ['nullable', 'array'],
            'notes' => ['nullable', 'array'],
            'attendances' => ['nullable', 'array'],
        ]);

        if (SchoolSetting::get('enable_attendance_manual', '1') !== '1') {
            return back()->with('error', 'Metode presensi manual sedang dinonaktifkan oleh kebijakan sekolah.');
        }

        $statuses = $validated['statuses'] ?? [];
        $notes = $validated['notes'] ?? [];

        if (empty($statuses) && ! empty($validated['attendances'])) {
            foreach ($validated['attendances'] as $studentId => $item) {
                if (is_array($item) && ! empty($item['status'])) {
                    $statuses[$studentId] = $item['status'];
                    if (! empty($item['notes'])) {
                        $notes[$studentId] = $item['notes'];
                    }
                }
            }
        }

        if (empty($statuses)) {
            return back()->with('warning', 'Silakan pilih status presensi (Hadir, Terlambat, Izin, Sakit, atau Alpa) minimal untuk 1 siswa sebelum menyimpan.');
        }

        if (! $user->canRecordAttendanceManual((int) $validated['class_id'])) {
            abort(403, 'Akses Ditolak: Fitur absen manual presensi hanya dapat dilaksanakan oleh Wakasek Kesiswaan, Wali Kelas pada kelasnya, atau Sekretaris Kelas pada kelasnya.');
        }

        $date = Carbon::parse($validated['date'], 'Asia/Jakarta')->toDateString();
        $dateObj = Carbon::parse($date, 'Asia/Jakarta');
        $schedule = AttendanceService::getScheduleForDay($dateObj);

        // 1. Validasi Hari Aktif Sekolah & Tanggal Merah
        if (! $schedule['is_active']) {
            $msg = ! empty($schedule['is_holiday'])
                ? "Tanggal {$date} adalah hari libur ({$schedule['holiday_name']}). Presensi manual tidak dapat dilakukan."
                : "Tanggal {$date} ({$schedule['day_name']}) sekolah libur atau bukan merupakan hari aktif presensi.";

            return back()->with('error', $msg);
        }

        $time = ! empty($validated['time']) ? $validated['time'].':00' : Carbon::now('Asia/Jakarta')->format('H:i:s');
        $timeStr = substr($time, 0, 5);
        $inStartStr = substr($schedule['in_start'], 0, 5);
        $inLateCutoffStr = substr($schedule['in_late_cutoff'], 0, 5);
        $outStartStr = substr($schedule['out_start'], 0, 5);
        $outEndStr = substr($schedule['out_end'], 0, 5);

        // Validasi Jam Operasional jika terdapat status kehadiran fisik (HADIR / TERLAMBAT)
        $hasPhysical = false;
        foreach ($statuses as $st) {
            if (in_array(strtoupper($st), ['HADIR', 'TERLAMBAT'], true)) {
                $hasPhysical = true;
                break;
            }
        }

        if ($hasPhysical) {
            if ($timeStr < $inStartStr) {
                return back()->with('error', "Presensi masuk belum dibuka. Jam presensi dimulai pukul {$inStartStr} WIB.");
            }

            if ($timeStr > $outEndStr) {
                return back()->with('error', "Waktu presensi ({$timeStr} WIB) berada di luar batas akhir jam operasional sekolah ({$outEndStr} WIB).");
            }

            if ($timeStr > $inLateCutoffStr && $timeStr < $outStartStr) {
                return back()->with('error', "Batas akhir presensi masuk untuk hari {$schedule['day_name']} telah berakhir pada pukul {$inLateCutoffStr} WIB.");
            }
        }

        $targetClass = SchoolClass::find($validated['class_id']);

        $savedCount = 0;
        foreach ($statuses as $studentId => $status) {
            $student = Student::find($studentId);
            if (! $student || (string) $student->class_id !== (string) $validated['class_id']) {
                continue;
            }

            $existing = Attendance::where('student_id', $student->id)->whereDate('date', $date)->first();

            $statusUpper = strtoupper($status);
            if (! in_array($statusUpper, ['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'ALPA'], true)) {
                $statusUpper = 'HADIR';
            }

            // Lindungi izin / sakit resmi yang sudah diverifikasi dari aksi borongan Hadir tanpa catatan
            if ($existing && in_array($existing->status, ['IZIN', 'SAKIT'], true) && $existing->verification_status === 'DIVERIFIKASI') {
                if ($statusUpper === 'HADIR' && empty($notes[$studentId])) {
                    continue;
                }
            }

            $note = ! empty($notes[$studentId])
                ? trim($notes[$studentId])
                : ($existing?->notes ?? "Absen manual rombel oleh {$user->name} ({$user->role}).");

            $savedAttendance = Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $date,
                ],
                [
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'student_name' => $student->name,
                    'class_id' => $student->class_id,
                    'time' => $time,
                    'method' => ($existing && $existing->method === 'IZIN_SAKIT') ? 'IZIN_SAKIT' : 'MANUAL',
                    'status' => $statusUpper,
                    'verification_status' => 'DIVERIFIKASI',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'notes' => $note,
                ]
            );

            $savedCount++;
        }

        AuditLog::log('ATTENDANCE_BULK_MANUAL_RECORDED', 'Attendance', null, null, [
            'class_id' => $validated['class_id'],
            'class_name' => $targetClass?->name,
            'date' => $date,
            'count' => $savedCount,
            'recorded_by' => $user->name,
        ]);

        $redirectUrl = route('attendance.index', [
            'tab' => 'manual',
            'class_id' => $validated['class_id'],
            'date' => $date,
        ]);

        if (str_contains($request->headers->get('referer', ''), '/attendance/manual')) {
            $redirectUrl = route('attendance.manual', [
                'class_id' => $validated['class_id'],
                'date' => $date,
            ]);
        }

        return redirect($redirectUrl)->with('success', "Alhamdulillah! Berhasil menyimpan absensi manual untuk {$savedCount} siswa kelas {$targetClass?->name} tanggal ".Carbon::parse($date)->translatedFormat('d F Y').'.');
    }

    /**
     * Tampilkan atau API Jadwal Petugas Piket
     */
    public function picketSchedulesIndex(Request $request)
    {
        $school = SchoolIdentity::current();
        $weeklyPickets = PicketSchedule::with(['teacher', 'user'])
            ->orderBy('day_number')
            ->orderBy('role_type')
            ->get()
            ->groupBy('day_name');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $weeklyPickets,
            ]);
        }

        return redirect()->route('attendance.index');
    }

    /**
     * Tambah Petugas Piket Baru ke Jadwal Harian
     */
    public function storePicketSchedule(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki hak untuk mengatur jadwal piket.');
        }

        $validated = $request->validate([
            'day_name' => ['required', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'person_name' => ['required', 'string', 'max:150'],
            'role_type' => ['required', 'in:GURU_PIKET,TU_PIKET,KOORDINATOR_PIKET'],
            'location' => ['required', 'string', 'max:150'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'notes' => ['nullable', 'string'],
        ]);

        $dayNumbers = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6,
        ];
        $validated['day_number'] = $dayNumbers[$validated['day_name']] ?? 1;
        $validated['is_active'] = true;

        if (! empty($validated['teacher_id'])) {
            $t = Teacher::find($validated['teacher_id']);
            if ($t) {
                $validated['person_name'] = $t->name;
                $validated['user_id'] = $validated['user_id'] ?? $t->user_id;
            }
        } elseif (! empty($validated['user_id'])) {
            $u = User::find($validated['user_id']);
            if ($u) {
                $validated['person_name'] = $u->name;
                $validated['teacher_id'] = $validated['teacher_id'] ?? $u->teacher?->id;
            }
        } else {
            $t = Teacher::where('name', $validated['person_name'])
                ->orWhere('name', 'like', "%{$validated['person_name']}%")
                ->first();
            if ($t) {
                $validated['teacher_id'] = $t->id;
                $validated['user_id'] = $t->user_id;
            } else {
                $u = User::where('name', $validated['person_name'])
                    ->orWhere('name', 'like', "%{$validated['person_name']}%")
                    ->first();
                if ($u) {
                    $validated['user_id'] = $u->id;
                    $validated['teacher_id'] = $u->teacher?->id;
                }
            }
        }

        PicketSchedule::create($validated);

        AuditLog::log('PICKET_SCHEDULE_CREATED', 'PicketSchedule', null, null, [
            'person_name' => $validated['person_name'],
            'day_name' => $validated['day_name'],
            'role_type' => $validated['role_type'],
        ]);

        return back()->with('success', "Jadwal petugas piket {$validated['person_name']} untuk hari {$validated['day_name']} berhasil ditambahkan.");
    }

    /**
     * Perbarui Data Petugas Piket
     */
    public function updatePicketSchedule(Request $request, PicketSchedule $picketSchedule): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki hak untuk mengatur jadwal piket.');
        }

        $validated = $request->validate([
            'day_name' => ['required', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'person_name' => ['required', 'string', 'max:150'],
            'role_type' => ['required', 'in:GURU_PIKET,TU_PIKET,KOORDINATOR_PIKET'],
            'location' => ['required', 'string', 'max:150'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $dayNumbers = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6,
        ];
        $validated['day_number'] = $dayNumbers[$validated['day_name']] ?? 1;
        $validated['is_active'] = $request->boolean('is_active', true);

        if (! empty($validated['teacher_id'])) {
            $t = Teacher::find($validated['teacher_id']);
            if ($t) {
                $validated['person_name'] = $t->name;
                $validated['user_id'] = $validated['user_id'] ?? $t->user_id;
            }
        } elseif (! empty($validated['user_id'])) {
            $u = User::find($validated['user_id']);
            if ($u) {
                $validated['person_name'] = $u->name;
                $validated['teacher_id'] = $validated['teacher_id'] ?? $u->teacher?->id;
            }
        } else {
            $t = Teacher::where('name', $validated['person_name'])
                ->orWhere('name', 'like', "%{$validated['person_name']}%")
                ->first();
            if ($t) {
                $validated['teacher_id'] = $t->id;
                $validated['user_id'] = $t->user_id;
            } else {
                $u = User::where('name', $validated['person_name'])
                    ->orWhere('name', 'like', "%{$validated['person_name']}%")
                    ->first();
                if ($u) {
                    $validated['user_id'] = $u->id;
                    $validated['teacher_id'] = $u->teacher?->id;
                }
            }
        }

        $picketSchedule->update($validated);

        return back()->with('success', "Jadwal petugas piket {$picketSchedule->person_name} berhasil diperbarui.");
    }

    /**
     * Hapus Petugas Piket dari Jadwal
     */
    public function destroyPicketSchedule(PicketSchedule $picketSchedule): RedirectResponse
    {
        $user = Auth::user();
        if (! $user->canManageSchoolPermits()) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus jadwal piket.');
        }

        $name = $picketSchedule->person_name;
        $day = $picketSchedule->day_name;
        $picketSchedule->delete();

        return back()->with('success', "Petugas piket {$name} untuk hari {$day} berhasil dihapus dari jadwal.");
    }

    /**
     * Cetak Jadwal Petugas Piket Resmi Sekolah (Ber-Kop Sekolah)
     */
    public function printPicketSchedule(Request $request): View
    {
        $school = SchoolIdentity::current();
        $weeklyPickets = PicketSchedule::with(['teacher', 'user'])
            ->orderBy('day_number')
            ->orderBy('role_type')
            ->get()
            ->groupBy('day_name');

        return view('attendance.picket_schedule.print', compact('school', 'weeklyPickets'));
    }
}

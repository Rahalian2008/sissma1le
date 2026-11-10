<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WorshipAttendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorshipAttendanceController extends Controller
{
    /**
     * Normalisasi slug URL ke konstanta aktivitas
     */
    protected function normalizeActivity(string $slug): string
    {
        return match (strtolower(str_replace('-', '_', $slug))) {
            'dhuha' => WorshipAttendance::ACTIVITY_DHUHA,
            'dzuhur_jumat', 'dzuhur' => WorshipAttendance::ACTIVITY_DZUHUR_JUMAT,
            'keputrian' => WorshipAttendance::ACTIVITY_KEPUTRIAN,
            default => abort(404, 'Aktivitas keagamaan tidak ditemukan.'),
        };
    }

    /**
     * Menghitung jarak radius koordinat GPS (Haversine formula dalam meter)
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Dashboard / Halaman Utama Presensi Kegiatan Keagamaan
     */
    public function index(Request $request, string $activity): View
    {
        $activityType = $this->normalizeActivity($activity);
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $user = Auth::user();
        $role = $user->role ?? 'guest';

        $selectedDate = $request->query('date', $today);
        $config = WorshipAttendance::getScheduleConfig($activityType, $selectedDate);
        $timeValidation = WorshipAttendance::validateTimeLock($activityType);

        // Strict class isolation for sekretaris_kelas & wali_kelas (kecuali admin/wakasek kesiswaan)
        $isRegularStudent = ($user->hasRole('siswa') || $user->role === 'siswa') && ! $user->hasAnyRole(['sekretaris_kelas', 'admin', 'super_admin', 'wakasek_kesiswaan', 'kesiswaan', 'wali_kelas', 'guru', 'kepala_sekolah']);
        $isGlobalAdmin = $user->hasAnyRole(['admin', 'super_admin', 'wakasek_kesiswaan', 'kesiswaan']);

        if ($isRegularStudent) {
            $studentClass = $user->student?->schoolClass;
            $classes = $studentClass ? collect([$studentClass]) : collect();
            $selectedClassId = $user->student?->class_id;
        } elseif (! $isGlobalAdmin && $user->hasRole('sekretaris_kelas')) {
            $studentClass = $user->student?->schoolClass;
            $classes = $studentClass ? collect([$studentClass]) : collect();
            $selectedClassId = $user->student?->class_id;
        } elseif (! $isGlobalAdmin && $user->hasRole('wali_kelas')) {
            $homeroomClass = $user->teacher?->homeroomClass;
            $classes = $homeroomClass ? collect([$homeroomClass]) : collect();
            $selectedClassId = $homeroomClass?->id;
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->query('class_id');
        }

        // Filter siswa target berdasarkan gender aktivitas
        $studentsQuery = Student::with('schoolClass')
            ->where('is_active', true);

        $allowedGender = WorshipAttendance::getAllowedGender($activityType, $selectedDate);
        if ($allowedGender !== 'ALL') {
            $studentsQuery->where('gender', $allowedGender);
        }

        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        }

        if ($isRegularStudent) {
            $studentsQuery->where('id', $user->student?->id);
        }

        $targetStudents = $studentsQuery->orderBy('name')->get();
        $targetStudentIds = $targetStudents->pluck('id')->toArray();

        // Data presensi hari/tanggal terpilih
        $attendances = WorshipAttendance::with(['student.schoolClass', 'recorder'])
            ->where('activity_type', $activityType)
            ->whereDate('date', $selectedDate)
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->when($isRegularStudent, fn ($q) => $q->where('student_id', $user->student?->id))
            ->orderByDesc('time')
            ->get();

        $attendanceByStudentId = $attendances->keyBy('student_id');

        // Ringkasan Statistik
        $totalTarget = $targetStudents->count();
        $totalHadir = $attendances->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $totalIzin = $attendances->whereIn('status', ['IZIN', 'SAKIT', 'UDZUR'])->count();
        $totalAlpa = $attendances->where('status', 'ALPA')->count();
        $totalBelum = max(0, $totalTarget - ($totalHadir + $totalIzin + $totalAlpa));
        $percentHadir = $totalTarget > 0 ? round(($totalHadir / $totalTarget) * 100, 1) : 0;

        // Riwayat log terbaru (15 log terakhir untuk scanner visual)
        $recentLogs = WorshipAttendance::with(['student.schoolClass'])
            ->where('activity_type', $activityType)
            ->whereDate('date', $today)
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->when($isRegularStudent, fn ($q) => $q->where('student_id', $user->student?->id))
            ->orderByDesc('created_at')
            ->take(15)
            ->get();

        $selectedDateFormatted = Carbon::parse($selectedDate)->locale('id')->translatedFormat('l, d F Y');
        $todayFormatted = Carbon::parse($today)->locale('id')->translatedFormat('l, d F Y');

        // Pengaturan & Perangkat Bersama (Sama dengan Presensi Harian)
        $devices = AttendanceDevice::orderBy('device_code')->get();
        $enableRfid = SchoolSetting::get('enable_attendance_rfid', '1') === '1';
        $enableBarcode = (SchoolSetting::get('enable_attendance_barcode', '1') === '1' || SchoolSetting::get('enable_attendance_qr', '1') === '1');
        $enableQr = $enableBarcode;
        $enableSelfie = SchoolSetting::get('enable_attendance_selfie', '1') === '1';
        $enableFaceRecognition = SchoolSetting::get('enable_attendance_face_recognition', '1') === '1';
        $enableManual = SchoolSetting::get('enable_attendance_manual', '1') === '1';
        $schoolLat = (float) SchoolSetting::get('school_lat', -7.56845);
        $schoolLng = (float) SchoolSetting::get('school_lng', 112.04612);
        $geofenceRadius = (int) SchoolSetting::get('geofence_radius_meters', 100);

        $isHomeroom = $user->hasRole('wali_kelas');
        $homeroomClass = $isHomeroom ? $user->teacher?->homeroomClass : null;
        $isStudent = $isRegularStudent;
        $activeTab = $request->query('tab', $isStudent ? 'selfie' : 'scanner');

        return view('worship.index', compact(
            'activity',
            'activityType',
            'config',
            'timeValidation',
            'classes',
            'selectedClassId',
            'selectedDate',
            'selectedDateFormatted',
            'today',
            'todayFormatted',
            'targetStudents',
            'attendances',
            'attendanceByStudentId',
            'totalTarget',
            'totalHadir',
            'totalIzin',
            'totalAlpa',
            'totalBelum',
            'percentHadir',
            'recentLogs',
            'devices',
            'enableRfid',
            'enableQr',
            'enableBarcode',
            'enableSelfie',
            'enableFaceRecognition',
            'enableManual',
            'schoolLat',
            'schoolLng',
            'geofenceRadius',
            'isHomeroom',
            'homeroomClass',
            'isStudent',
            'activeTab'
        ));
    }

    /**
     * Endpoint API Scan Presensi (Mendukung RFID, Barcode NISN, dan QR Code)
     */
    public function scan(Request $request, string $activity): JsonResponse
    {
        $activityType = $this->normalizeActivity($activity);
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'identifier' => ['required', 'string'], // NISN, RFID UID, atau NIS
            'method' => ['nullable', 'in:RFID,QR_CODE,BARCODE,FACE_RECOGNITION,MANUAL'],
            'device_code' => ['nullable', 'string'],
            'bypass_time_lock' => ['nullable', 'boolean'],
        ]);

        $method = $validated['method'] ?? 'RFID';

        // 0. Validasi Pengaturan Metode Presensi Aktif (Sama dengan Presensi Harian)
        if ($method === 'RFID') {
            if (SchoolSetting::get('enable_attendance_rfid', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi kartu RFID saat ini sedang dinonaktifkan oleh administrator sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        } elseif (in_array($method, ['QR_CODE', 'BARCODE'], true)) {
            if (SchoolSetting::get('enable_attendance_barcode', '1') !== '1' && SchoolSetting::get('enable_attendance_qr', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi Barcode Scanner saat ini sedang dinonaktifkan oleh administrator sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        } elseif ($method === 'FACE_RECOGNITION') {
            if (SchoolSetting::get('enable_attendance_face_recognition', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi Face Recognition saat ini sedang dinonaktifkan oleh administrator sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        } elseif ($method === 'MANUAL') {
            if (SchoolSetting::get('enable_attendance_manual', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi manual saat ini sedang dinonaktifkan oleh administrator sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        }

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $nowTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // 1. Validasi Time-Lock Ketat (Wajib Mengikuti Master Data Pengaturan)
        $timeLock = WorshipAttendance::validateTimeLock($activityType, Carbon::now('Asia/Jakarta'));
        if (! $timeLock['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $timeLock['reason'],
                'error_code' => 'TIME_LOCKED',
                'details' => $timeLock,
            ], 422);
        }

        // 2. Temukan Siswa berdasarkan UID RFID atau NISN atau NIS
        $identifier = trim($validated['identifier']);
        $student = Student::with('schoolClass')
            ->where('rfid_uid', $identifier)
            ->orWhere('nisn', $identifier)
            ->orWhere('nis', $identifier)
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => "Kartu/Data Siswa tidak terdaftar pada sistem (ID: {$identifier}).",
                'error_code' => 'STUDENT_NOT_FOUND',
            ], 404);
        }

        // 2b. Validasi Pembatasan Kelas untuk Akun Wali Kelas & Sekretaris Kelas
        if ($user?->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                return response()->json([
                    'success' => false,
                    'message' => "Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mencatat presensi untuk siswa kelas binaan Anda.",
                    'error_code' => 'CLASS_RESTRICTION',
                ], 403);
            }
        } elseif ($user?->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $studentClass = $user->student?->schoolClass;
            if ($studentClass && $student->class_id !== $studentClass->id) {
                return response()->json([
                    'success' => false,
                    'message' => "Sebagai Sekretaris Kelas {$studentClass->name}, Anda hanya dapat mencatat presensi untuk rekan sekelas Anda.",
                    'error_code' => 'CLASS_RESTRICTION',
                ], 403);
            }
        }

        // 3. Validasi Jenis Kelamin Ketat Sesuai Aturan Bisnis
        $allowedGender = WorshipAttendance::getAllowedGender($activityType, $today);
        if ($allowedGender !== 'ALL' && $student->gender !== $allowedGender) {
            $msg = ($allowedGender === 'L')
                ? "Pada hari Jum'at, Shalat Jum'at khusus untuk siswa laki-laki. Siswi perempuan melaksanakan Kegiatan Keputrian."
                : "Presensi Kegiatan Keputrian hanya berlaku untuk siswi perempuan. Siswa laki-laki melaksanakan Shalat Dzuhur/Jum'at berjamaah.";

            return response()->json([
                'success' => false,
                'message' => $msg,
                'error_code' => 'GENDER_RESTRICTION',
                'student' => [
                    'name' => $student->name,
                    'gender' => $student->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                ],
            ], 422);
        }

        // 4. Deteksi Perangkat Scanner IoT Bersama
        $deviceInfo = $request->userAgent();
        $deviceCode = $request->input('device_code');
        if ($deviceCode) {
            $device = AttendanceDevice::where('device_code', $deviceCode)->first();
            if ($device) {
                $deviceInfo = "[{$device->device_code}] {$device->device_name} ({$device->location_name})";
                $device->update(['last_ping_at' => now(), 'status' => 'ONLINE']);
            }
        }

        // 5. Validasi Duplikasi (Cegah scan ganda dalam 1 sesi hari ini)
        $existing = WorshipAttendance::where('student_id', $student->id)
            ->where('activity_type', $activityType)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            if ($existing->status === 'ALPA') {
                $existing->update([
                    'time' => $nowTime,
                    'status' => 'HADIR',
                    'method' => $method,
                    'device_info' => $deviceInfo,
                    'recorded_by' => $user?->id,
                    'notes' => 'Presensi mandiri melalui scanner '.$method,
                ]);

                AuditLog::log('WORSHIP_ATTENDANCE_LOGGED', 'WorshipAttendance', $existing->id, null, [
                    'activity' => $activityType,
                    'student' => $student->name,
                    'method' => $method,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Presensi {$student->name} berhasil dicatat sebagai HADIR.",
                    'attendance' => $existing,
                ]);
            }

            $scanTime = substr($existing->time, 0, 5);

            return response()->json([
                'success' => false,
                'message' => "Siswa {$student->name} ({$student->schoolClass?->name}) sudah tercatat presensi sebelumnya pada pukul {$scanTime} WIB.",
                'error_code' => 'ALREADY_RECORDED',
                'student' => $student,
                'existing' => $existing,
            ], 422);
        }

        $attendance = WorshipAttendance::create([
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'activity_type' => $activityType,
            'date' => $today,
            'time' => $nowTime,
            'status' => 'HADIR',
            'method' => $method,
            'device_info' => $deviceInfo,
            'recorded_by' => $user?->id,
            'notes' => 'Presensi mandiri melalui scanner '.$method,
        ]);

        AuditLog::log('WORSHIP_ATTENDANCE_LOGGED', 'WorshipAttendance', $attendance->id, null, [
            'activity' => $activityType,
            'student' => $student->name,
            'method' => $method,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Alhamdulillah! Presensi {$student->name} ({$student->schoolClass?->name}) berhasil dicatat.",
            'student' => [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'class_name' => $student->schoolClass?->name ?? '-',
                'photo_url' => $student->photo ? asset('storage/'.$student->photo) : null,
            ],
            'attendance' => $attendance,
            'formatted_time' => substr($nowTime, 0, 5).' WIB',
        ]);
    }

    /**
     * Presensi Kamera Selfie & Geofence GPS Langsung
     */
    public function storeSelfie(Request $request, string $activity): JsonResponse
    {
        $activityType = $this->normalizeActivity($activity);
        $user = Auth::user();
        $student = $user->student ?? Student::find($request->student_id);

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Data profil siswa tidak ditemukan.',
            ], 404);
        }

        // Validasi Pembatasan Kelas untuk Akun Wali Kelas
        if ($user?->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass && $student->class_id !== $homeroomClass->id) {
                return response()->json([
                    'success' => false,
                    'message' => "Sebagai Wali Kelas {$homeroomClass->name}, Anda hanya dapat mencatat presensi untuk siswa kelas binaan Anda.",
                ], 403);
            }
        }

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $nowTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // 0. Validasi Pengaturan Metode Presensi Selfie Aktif (Sama dengan Presensi Harian)
        if (SchoolSetting::get('enable_attendance_selfie', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Metode presensi selfie kamera saat ini sedang dinonaktifkan oleh administrator sekolah.',
                'error_code' => 'METHOD_DISABLED',
            ], 422);
        }

        // 1. Validasi Time-Lock Ketat (Wajib Mengikuti Master Data Pengaturan)
        $timeLock = WorshipAttendance::validateTimeLock($activityType, Carbon::now('Asia/Jakarta'));
        if (! $timeLock['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $timeLock['reason'],
                'error_code' => 'TIME_LOCKED',
            ], 422);
        }

        // 2. Validasi Gender
        $allowedGender = WorshipAttendance::getAllowedGender($activityType, $today);
        if ($allowedGender !== 'ALL' && $student->gender !== $allowedGender) {
            $msg = ($allowedGender === 'L')
                ? "Pada hari Jum'at, Shalat Jum'at khusus untuk siswa laki-laki. Siswi perempuan melaksanakan Kegiatan Keputrian."
                : 'Presensi Kegiatan Keputrian hanya untuk siswi perempuan.';

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 422);
        }

        $validated = $request->validate([
            'selfie_image' => ['required', 'string'], // base64 stream from live canvas
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        // Validasi Koordinat GPS & Radius Geofence (Sama dengan Presensi Harian)
        $isGeofenceEnabled = SchoolSetting::get('enable_geofence_radius', '1') === '1';
        $schoolLat = (float) SchoolSetting::get('school_lat', -7.56845);
        $schoolLng = (float) SchoolSetting::get('school_lng', 112.04612);
        $geofenceRadius = (int) SchoolSetting::get('geofence_radius_meters', 100);

        if ($isGeofenceEnabled && ! empty($validated['latitude']) && ! empty($validated['longitude'])) {
            $distance = $this->calculateDistance($schoolLat, $schoolLng, (float) $validated['latitude'], (float) $validated['longitude']);
            if ($distance > $geofenceRadius) {
                return response()->json([
                    'success' => false,
                    'message' => "Posisi Anda berada di luar radius sekolah yang diizinkan ({$distance} meter, batas maksimal {$geofenceRadius} meter). Presensi hanya dapat dilakukan di lingkungan sekolah.",
                    'error_code' => 'OUT_OF_GEOFENCE_RADIUS',
                    'distance_meters' => $distance,
                    'max_radius_meters' => $geofenceRadius,
                ], 422);
            }
        }

        // Cek duplikasi
        $existing = WorshipAttendance::where('student_id', $student->id)
            ->where('activity_type', $activityType)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->status !== 'ALPA') {
            return response()->json([
                'success' => false,
                'message' => 'Anda telah tercatat presensi untuk sesi ini hari ini.',
            ], 422);
        }

        // Simpan foto selfie
        $selfiePath = null;
        if (! empty($validated['selfie_image']) && str_starts_with($validated['selfie_image'], 'data:image/')) {
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $validated['selfie_image']);
            $decoded = base64_decode($imageData);
            if ($decoded !== false) {
                $filename = 'worship_'.strtolower($activityType).'_'.$student->id.'_'.time().'.jpg';
                Storage::disk('public')->put('worship_selfies/'.$filename, $decoded);
                $selfiePath = 'worship_selfies/'.$filename;
            }
        }

        $attendanceData = [
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'activity_type' => $activityType,
            'date' => $today,
            'time' => $nowTime,
            'status' => 'HADIR',
            'method' => 'SELFIE',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'selfie_path' => $selfiePath,
            'device_info' => $request->userAgent(),
            'recorded_by' => $user?->id,
            'notes' => 'Presensi selfie kamera langsung',
        ];

        if ($existing) {
            $existing->update($attendanceData);
            $attendance = $existing;
        } else {
            $attendance = WorshipAttendance::create($attendanceData);
        }

        return response()->json([
            'success' => true,
            'message' => "Presensi selfie {$student->name} berhasil disimpan!",
            'attendance' => $attendance,
        ]);
    }

    /**
     * Input Presensi Manual Masal (Khusus Wali Kelas, Guru, Admin)
     */
    public function storeManual(Request $request, string $activity): RedirectResponse
    {
        $activityType = $this->normalizeActivity($activity);
        $user = Auth::user();

        if (SchoolSetting::get('enable_attendance_manual', '1') !== '1') {
            return back()->with('error', 'Metode presensi manual saat ini sedang dinonaktifkan oleh administrator sekolah.');
        }

        $validated = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id'],
            'date' => ['required', 'date'],
            'statuses' => ['nullable', 'array'],
            'notes' => ['nullable', 'array'],
        ]);

        if (! $user->canRecordAttendanceManual((int) $validated['class_id'])) {
            abort(403, 'Akses Ditolak: Fitur absen manual ibadah hanya dapat dilaksanakan oleh Wakasek Kesiswaan, Wali Kelas pada kelasnya, atau Sekretaris Kelas pada kelasnya.');
        }

        $date = $validated['date'];
        $nowTime = Carbon::now('Asia/Jakarta')->format('H:i:s');
        $allowedGender = WorshipAttendance::getAllowedGender($activityType, $date);

        // 1. Validasi Time-Lock Ketat untuk Presensi Manual (Wajib Mengikuti Master Data Pengaturan)
        $targetDateTime = Carbon::parse($date.' '.$nowTime, 'Asia/Jakarta');
        $timeLock = WorshipAttendance::validateTimeLock($activityType, $targetDateTime);
        if (! $timeLock['allowed']) {
            return redirect()->route('worship.index', [
                'activity' => $activity,
                'class_id' => $validated['class_id'],
                'date' => $date,
                'tab' => 'manual',
            ])->with('error', $timeLock['reason']);
        }

        if (empty($validated['statuses'])) {
            return redirect()->route('worship.index', [
                'activity' => $activity,
                'class_id' => $validated['class_id'],
                'date' => $date,
                'tab' => 'manual',
            ])->with('warning', 'Tidak ada status presensi siswa yang dipilih untuk disimpan.');
        }

        $savedCount = 0;
        foreach ($validated['statuses'] as $studentId => $status) {
            $student = Student::find($studentId);
            if (! $student || ($allowedGender !== 'ALL' && $student->gender !== $allowedGender)) {
                continue;
            }

            if ($user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
                if ($student->class_id !== $user->student?->class_id) {
                    continue;
                }
            }

            if ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
                if ($student->class_id !== $user->teacher?->homeroomClass?->id) {
                    continue;
                }
            }

            $worshipRecord = WorshipAttendance::where('student_id', $student->id)
                ->where('activity_type', $activityType)
                ->whereDate('date', $date)
                ->first();

            $worshipData = [
                'class_id' => $student->class_id,
                'time' => $nowTime,
                'status' => in_array($status, ['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'UDZUR', 'ALPA']) ? $status : 'HADIR',
                'method' => 'MANUAL',
                'notes' => $validated['notes'][$studentId] ?? null,
                'recorded_by' => $user->id,
            ];

            if ($worshipRecord) {
                $worshipRecord->update($worshipData);
            } else {
                WorshipAttendance::create(array_merge([
                    'student_id' => $student->id,
                    'activity_type' => $activityType,
                    'date' => $date,
                ], $worshipData));
            }

            $savedCount++;
        }

        return redirect()->route('worship.index', [
            'activity' => $activity,
            'class_id' => $validated['class_id'],
            'date' => $date,
            'tab' => 'manual',
        ])->with('success', "Berhasil menyimpan presensi manual untuk {$savedCount} siswa.");
    }

    /**
     * Tampilan Rekap & Dokumen Laporan Resmi (Siap Cetak A4 / KOP Surat)
     */
    public function report(Request $request, string $activity): View
    {
        $activityType = $this->normalizeActivity($activity);
        $config = WorshipAttendance::getScheduleConfig($activityType);
        $user = Auth::user();
        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);

        if ($user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan', 'wali_kelas'])) {
            $studentClass = $user->student?->schoolClass;
            $classes = $studentClass ? collect([$studentClass]) : collect();
            $selectedClassId = $studentClass?->id;
        } elseif ($isWaliKelas) {
            $classes = $user->getHomeroomClasses();
            $requestedClassId = $request->query('class_id');
            if ($requestedClassId && $classes->contains('id', (int) $requestedClassId)) {
                $selectedClassId = (int) $requestedClassId;
            } else {
                $selectedClassId = $classes->first()?->id;
            }
        } else {
            $classes = SchoolClass::orderBy('name')->get();
            $selectedClassId = $request->query('class_id');
        }
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $statusFilter = $request->query('status');

        $query = WorshipAttendance::with(['student.schoolClass', 'recorder'])
            ->where('activity_type', $activityType)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($selectedClassId) {
            $query->where('class_id', $selectedClassId);
        } elseif ($isWaliKelas && $classes->isNotEmpty()) {
            $query->whereIn('class_id', $classes->pluck('id'));
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $records = $query->orderByDesc('date')->orderBy('time')->paginate(30)->withQueryString();

        $statsClassFilter = $selectedClassId ? [$selectedClassId] : ($isWaliKelas && $classes->isNotEmpty() ? $classes->pluck('id')->toArray() : null);

        // Statistik Keseluruhan Rentang Tanggal
        $stats = [
            'total' => WorshipAttendance::where('activity_type', $activityType)->whereBetween('date', [$startDate, $endDate])->when($statsClassFilter, fn ($q) => $q->whereIn('class_id', $statsClassFilter))->count(),
            'hadir' => WorshipAttendance::where('activity_type', $activityType)->whereBetween('date', [$startDate, $endDate])->when($statsClassFilter, fn ($q) => $q->whereIn('class_id', $statsClassFilter))->where('status', 'HADIR')->count(),
            'terlambat' => WorshipAttendance::where('activity_type', $activityType)->whereBetween('date', [$startDate, $endDate])->when($statsClassFilter, fn ($q) => $q->whereIn('class_id', $statsClassFilter))->where('status', 'TERLAMBAT')->count(),
            'izin_udzur' => WorshipAttendance::where('activity_type', $activityType)->whereBetween('date', [$startDate, $endDate])->when($statsClassFilter, fn ($q) => $q->whereIn('class_id', $statsClassFilter))->whereIn('status', ['IZIN', 'SAKIT', 'UDZUR'])->count(),
            'alpa' => WorshipAttendance::where('activity_type', $activityType)->whereBetween('date', [$startDate, $endDate])->when($statsClassFilter, fn ($q) => $q->whereIn('class_id', $statsClassFilter))->where('status', 'ALPA')->count(),
        ];

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;

        return view('worship.report', compact(
            'activity',
            'activityType',
            'config',
            'classes',
            'selectedClass',
            'selectedClassId',
            'startDate',
            'endDate',
            'statusFilter',
            'records',
            'stats'
        ));
    }

    /**
     * Cetak Berkas Dokumen Laporan Resmi Presensi Keagamaan (Format A4 KOP Lengkap)
     */
    public function printReport(Request $request, string $activity): View
    {
        $activityType = $this->normalizeActivity($activity);
        $config = WorshipAttendance::getScheduleConfig($activityType);
        $user = Auth::user();
        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);

        if ($user->hasRole('siswa') || $user->role === 'siswa') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        }

        if ($user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan', 'wali_kelas'])) {
            $selectedClassId = $user->student?->class_id;
        } elseif ($isWaliKelas) {
            $homeroomClasses = $user->getHomeroomClasses();
            $requestedClassId = $request->input('class_id') ?: $request->query('class_id');
            if ($requestedClassId && $homeroomClasses->contains('id', (int) $requestedClassId)) {
                $selectedClassId = (int) $requestedClassId;
            } else {
                $selectedClassId = $homeroomClasses->first()?->id;
            }
        } else {
            $selectedClassId = $request->input('class_id') ?: $request->query('class_id');
        }

        $periodType = $request->input('period_type');

        if ($periodType === 'daily') {
            $targetDate = $request->input('date', Carbon::now('Asia/Jakarta')->toDateString());
            $startDate = $targetDate;
            $endDate = $targetDate;
            $periodTitle = 'Laporan Harian ('.Carbon::parse($targetDate)->translatedFormat('l, d F Y').')';
        } elseif ($periodType === 'weekly') {
            $refDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now('Asia/Jakarta');
            $startDate = $request->input('start_date', $refDate->copy()->startOfWeek()->toDateString());
            $endDate = $request->input('end_date', $refDate->copy()->endOfWeek()->toDateString());
            $periodTitle = 'Laporan Mingguan ('.Carbon::parse($startDate)->translatedFormat('d M Y').' s.d. '.Carbon::parse($endDate)->translatedFormat('d M Y').')';
        } elseif ($periodType === 'monthly') {
            if ($request->input('month') && $request->input('year')) {
                $monthDate = Carbon::createFromDate((int) $request->input('year'), (int) $request->input('month'), 1);
                $startDate = $monthDate->startOfMonth()->toDateString();
                $endDate = $monthDate->endOfMonth()->toDateString();
            } else {
                $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
                $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->endOfMonth()->toDateString());
            }
            $periodTitle = 'Laporan Bulanan ('.Carbon::parse($startDate)->translatedFormat('F Y').')';
        } else {
            $startDate = $request->input('start_date', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::now('Asia/Jakarta')->toDateString());
            $periodTitle = ($startDate === $endDate)
                ? 'Laporan Harian ('.Carbon::parse($startDate)->translatedFormat('l, d F Y').')'
                : 'Periode: '.Carbon::parse($startDate)->translatedFormat('d F Y').' s.d. '.Carbon::parse($endDate)->translatedFormat('d F Y');
        }

        $statusFilter = $request->input('status') ?: $request->query('status');

        $query = WorshipAttendance::with(['student.schoolClass', 'recorder'])
            ->where('activity_type', $activityType)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($selectedClassId) {
            $query->where('class_id', $selectedClassId);
        } elseif ($isWaliKelas && isset($homeroomClasses) && $homeroomClasses->isNotEmpty()) {
            $query->whereIn('class_id', $homeroomClasses->pluck('id'));
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $records = $query->orderBy('date')->orderBy('time')->get();

        $selectedClass = $selectedClassId ? SchoolClass::with('homeroomTeacher')->find($selectedClassId) : null;
        $school = SchoolSetting::allAsAssoc();
        $headmasterName = SchoolSetting::get('headmaster_name') ?: ($school['headmaster_name'] ?? 'Drs. H. Sukardi, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?: ($school['headmaster_nip'] ?? '19680512 199403 1 004');

        $stats = [
            'total' => $records->count(),
            'hadir' => $records->where('status', 'HADIR')->count(),
            'terlambat' => $records->where('status', 'TERLAMBAT')->count(),
            'izin_udzur' => $records->whereIn('status', ['IZIN', 'SAKIT', 'UDZUR'])->count(),
            'alpa' => $records->where('status', 'ALPA')->count(),
        ];

        // Hitung Rekapitulasi Per-Siswa (Format Standar Dinas Presensi Utama)
        $studentsQuery = Student::with('schoolClass')->where('is_active', true);
        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        } elseif ($isWaliKelas && isset($homeroomClasses) && $homeroomClasses->isNotEmpty()) {
            $studentsQuery->whereIn('class_id', $homeroomClasses->pluck('id'));
        }
        if (! empty($config['gender_target'])) {
            $studentsQuery->where('gender', $config['gender_target']);
        }
        $targetStudents = $studentsQuery->orderBy('name')->get();

        $studentRecaps = $targetStudents->map(function ($student) use ($records) {
            $stRecords = $records->where('student_id', $student->id);
            $hadir = $stRecords->where('status', 'HADIR')->count();
            $terlambat = $stRecords->where('status', 'TERLAMBAT')->count();
            $izin = $stRecords->whereIn('status', ['IZIN', 'SAKIT', 'UDZUR'])->count();
            $alpa = $stRecords->where('status', 'ALPA')->count();
            $total = $hadir + $terlambat + $izin + $alpa;
            $presencePct = $total > 0 ? round((($hadir + $terlambat) / $total) * 100, 1) : 0;

            return [
                'student' => $student,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin_udzur' => $izin,
                'alpa' => $alpa,
                'total' => $total,
                'percentage' => $presencePct,
            ];
        });

        $totals = [
            'students' => $targetStudents->count(),
            'hadir' => $studentRecaps->sum('hadir'),
            'terlambat' => $studentRecaps->sum('terlambat'),
            'izin_udzur' => $studentRecaps->sum('izin_udzur'),
            'alpa' => $studentRecaps->sum('alpa'),
            'avg_percentage' => $studentRecaps->count() > 0 ? round($studentRecaps->avg('percentage'), 1) : 0,
        ];

        $kopImage = SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image'));
        $actualKopUrl = null;
        if ($kopImage && (Storage::disk('public')->exists($kopImage) || file_exists(public_path('storage/'.$kopImage)))) {
            $actualKopUrl = asset('storage/'.$kopImage);
        }

        $religionCoordinator = Teacher::getReligionCoordinator();

        return view('worship.print_report', compact(
            'activity',
            'activityType',
            'config',
            'selectedClass',
            'startDate',
            'endDate',
            'periodType',
            'periodTitle',
            'records',
            'studentRecaps',
            'totals',
            'school',
            'actualKopUrl',
            'headmasterName',
            'headmasterNip',
            'religionCoordinator',
            'stats'
        ));
    }

    public function update(Request $request, string $activity, int $id): RedirectResponse
    {
        $attendance = WorshipAttendance::with('student')->findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $homeroomClass = $user->teacher?->homeroomClass;
            if ($homeroomClass && $attendance->student?->class_id !== $homeroomClass->id) {
                abort(403, 'Akses Ditolak: Anda hanya dapat mengubah presensi siswa kelas binaan Anda.');
            }
        } elseif ($user->hasRole('sekretaris_kelas') && ! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan'])) {
            $studentClass = $user->student?->schoolClass;
            if ($studentClass && $attendance->student?->class_id !== $studentClass->id) {
                abort(403, 'Akses Ditolak: Anda hanya dapat mengubah presensi rekan sekelas Anda.');
            }
        } elseif (! $user->hasAnyRole(['admin', 'super_admin', 'koordinator_keagamaan', 'wali_kelas', 'sekretaris_kelas'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk mengubah data.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:HADIR,TERLAMBAT,IZIN,SAKIT,UDZUR,ALPA'],
            'time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'time' => $validated['time'] ? $validated['time'].':00' : $attendance->time,
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Data presensi berhasil diperbarui.');
    }

    public function destroy(string $activity, int $id): RedirectResponse
    {
        $attendance = WorshipAttendance::with('student')->findOrFail($id);
        $user = Auth::user();

        if (! $user->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Akses Ditolak: Hanya Admin atau Super Admin yang dapat menghapus data.');
        }

        $attendance->delete();

        return back()->with('success', 'Data presensi berhasil dihapus.');
    }
}

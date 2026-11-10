<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDevice;
use App\Models\AuditLog;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAttendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExtracurricularAttendanceController extends Controller
{
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
     * Dashboard / Halaman Utama Presensi Kegiatan Ekstrakurikuler
     * Menggunakan metodologi yang sama persis dengan presensi shalat dhuha:
     * - Multi-Tab: Scanner RFID/QR, Live WebRTC Selfie Camera, Input Manual per Anggota, Riwayat Sesi
     * - Time-lock jadwal latihan
     * - Kartu statistik kehadiran
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $selectedDate = $request->query('date', $today);

        // Ambil daftar ekstrakurikuler yang dapat diakses oleh pengguna
        $allExtrasQuery = Extracurricular::where('is_active', true)->with(['teacher', 'teachers']);

        // Deteksi peran sekretaris ekstra dan sekretaris kelas (double role)
        $isDoubleSecretary = $user->hasRole('sekretaris_ekstra') && $user->hasRole('sekretaris_kelas');
        $isSingleExtraSecretary = ($user->hasRole('sekretaris_ekstra') || $user->isExtracurricularSecretary())
            && ! $isDoubleSecretary
            && ! $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            && ! in_array($user->role, ['admin', 'super_admin'], true);

        // Deteksi apakah siswa menjabat sebagai sekretaris pada salah satu ekstrakurikuler
        $secretaryExtra = null;
        if ($user->student) {
            $secretaryExtra = $user->student->extracurriculars()
                ->where('is_active', true)
                ->wherePivotIn('role', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'])
                ->first();
        }

        // Tentukan ekstrakurikuler terpilih
        $selectedExtraId = $request->query('extra_id');
        if (! $selectedExtraId && $secretaryExtra) {
            $selectedExtraId = $secretaryExtra->id;
        }

        $provisionalExtra = $selectedExtraId ? Extracurricular::find($selectedExtraId) : ($secretaryExtra ?? (clone $allExtrasQuery)->first());
        $isExtraSecretary = (bool) ($user->student && $provisionalExtra && ($user->isExtracurricularSecretary($provisionalExtra->id) || $user->hasRole('sekretaris_ekstra')));

        // Target Siswa: Anggota terdaftar dari ekstrakurikuler ini
        $isRegularStudent = ($user->hasRole('siswa') || $user->role === 'siswa')
            && ! $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            && ! in_array($user->role, ['admin', 'super_admin'], true)
            && ! $isExtraSecretary
            && ! $isDoubleSecretary;

        // Jika user adalah Pembina Ekstra murni dan bukan admin, prioritaskan ekstra yang dibinanya
        $isPembinaOnly = $user->hasRole('pembina_ekstra') && ! $user->hasAnyRole(['admin', 'super_admin', 'kepala_sekolah']) && ! in_array($user->role, ['admin', 'super_admin'], true);
        $myExtraIds = [];
        if ($user->teacher) {
            $myExtraIds = ($user->teacher->all_extracurriculars ?? collect())->pluck('id')->toArray();
        }

        // Presensi ekstrakurikuler hanya aktif dan tampil pada akun siswa yang menjadi anggota ekstrakurikuler
        // KECUALI jika siswa memiliki double role (sekretaris ekstra DAN sekretaris kelas) atau pembina/guru/admin
        if ($isRegularStudent || $isSingleExtraSecretary) {
            $student = $user->student;
            if (! $student || $student->extracurriculars()->where('is_active', true)->count() === 0) {
                return redirect()->route('dashboard')->with('error', 'Presensi ekstrakurikuler hanya aktif dan dapat diakses oleh siswa yang terdaftar sebagai anggota ekstrakurikuler.');
            }

            $myExtraIds = $student->extracurriculars()->where('is_active', true)->pluck('extracurriculars.id')->toArray();
            $availableExtras = $allExtrasQuery->whereIn('id', $myExtraIds)->orderBy('name')->get();
        } else {
            $availableExtras = $allExtrasQuery->orderBy('name')->get();
        }

        // Tentukan ekstrakurikuler terpilih
        if (! $selectedExtraId || (($isRegularStudent || $isSingleExtraSecretary) && ! in_array((int) $selectedExtraId, $myExtraIds, true))) {
            if ($secretaryExtra && in_array($secretaryExtra->id, $myExtraIds, true)) {
                $selectedExtraId = $secretaryExtra->id;
            } elseif ($isPembinaOnly && ! empty($myExtraIds)) {
                $selectedExtraId = $myExtraIds[0];
            } elseif (($isRegularStudent || $isSingleExtraSecretary) && ! empty($myExtraIds)) {
                $selectedExtraId = $myExtraIds[0];
            } else {
                $selectedExtraId = $availableExtras->first()?->id;
            }
        }

        $extra = Extracurricular::with(['teacher.user', 'teachers.user', 'students.schoolClass'])
            ->find($selectedExtraId);

        if (! $extra && $availableExtras->isNotEmpty()) {
            $extra = $availableExtras->first();
            $selectedExtraId = $extra->id;
        }

        // Hitung ulang status sekretaris untuk ekstra yang fix terpilih
        $isExtraSecretary = $isDoubleSecretary || (bool) ($user->student && $extra && ($user->isExtracurricularSecretary($extra->id) || $user->hasRole('sekretaris_ekstra')));
        $isRegularStudent = ($user->hasRole('siswa') || $user->role === 'siswa')
            && ! $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            && ! in_array($user->role, ['admin', 'super_admin'], true)
            && ! $isExtraSecretary;

        // Cek apakah user berwenang mengatur jadwal ekstrakurikuler ini (Pembina atau Admin)
        $canManageSchedule = $user->hasAnyRole(['admin', 'super_admin'])
            || in_array($user->role, ['admin', 'super_admin'], true)
            || ($user->teacher && $extra && $extra->all_teachers->contains('id', $user->teacher->id))
            || ($user->hasRole('pembina_ekstra') && $extra && ($extra->teacher_id == $user->teacher?->id || $extra->teachers()->where('teachers.id', $user->teacher?->id)->exists()));

        // Time-Lock validation untuk ekstra terpilih
        $timeValidation = $extra ? $extra->validateTimeLock() : ['allowed' => true, 'status' => 'OPEN', 'reason' => ''];

        // Daftar kelas untuk filter tampilan anggota
        $classes = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->query('class_id');

        $targetStudents = collect();
        if ($extra) {
            $studentsQuery = $extra->students()->with('schoolClass')->where('is_active', true);

            if ($selectedClassId) {
                $studentsQuery->where('class_id', $selectedClassId);
            }

            if ($isRegularStudent && $user->student) {
                $studentsQuery->where('students.id', $user->student->id);
            }

            $targetStudents = $studentsQuery->orderBy('name')->get();
        }

        // Data Presensi pada tanggal terpilih
        $attendances = collect();
        if ($extra) {
            $attQuery = ExtracurricularAttendance::with(['student.schoolClass', 'recorder'])
                ->where('extracurricular_id', $extra->id)
                ->whereDate('date', $selectedDate);

            if ($selectedClassId) {
                $attQuery->where('class_id', $selectedClassId);
            }

            if ($isRegularStudent && $user->student) {
                $attQuery->where('student_id', $user->student->id);
            }

            $attendances = $attQuery->orderByDesc('time')->get();
        }

        $attendanceByStudentId = $attendances->keyBy('student_id');

        // Statistik Kehadiran
        $totalTarget = $targetStudents->count();
        $totalHadir = $attendances->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $totalIzin = $attendances->whereIn('status', ['IZIN', 'SAKIT'])->count();
        $totalAlpa = $attendances->where('status', 'ALPA')->count();
        $totalBelum = max(0, $totalTarget - ($totalHadir + $totalIzin + $totalAlpa));
        $percentHadir = $totalTarget > 0 ? round(($totalHadir / $totalTarget) * 100, 1) : 0;

        // Riwayat Log Terbaru (15 log terakhir untuk scanner visual)
        $recentLogs = collect();
        if ($extra) {
            $recentLogs = ExtracurricularAttendance::with(['student.schoolClass'])
                ->where('extracurricular_id', $extra->id)
                ->whereDate('date', $today)
                ->orderByDesc('created_at')
                ->take(15)
                ->get();
        }

        $selectedDateFormatted = Carbon::parse($selectedDate)->locale('id')->translatedFormat('l, d F Y');
        $todayFormatted = Carbon::parse($today)->locale('id')->translatedFormat('l, d F Y');

        // Pengaturan Sistem Scanner & Geofence
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

        $isStudent = $isRegularStudent;
        $activeTab = $request->query('tab', $isExtraSecretary ? 'manual' : ($isStudent ? 'selfie' : 'scanner'));

        $allStudents = Student::where('is_active', true)->with('schoolClass')->orderBy('name')->get();

        return view('extracurricular.index', compact(
            'availableExtras',
            'extra',
            'selectedExtraId',
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
            'isPembinaOnly',
            'isStudent',
            'isExtraSecretary',
            'isDoubleSecretary',
            'canManageSchedule',
            'activeTab',
            'allStudents'
        ));
    }

    /**
     * Endpoint API Scan Presensi Ekstrakurikuler (RFID, Barcode NISN, dan QR Code)
     */
    public function scan(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'extracurricular_id' => ['required', 'exists:extracurriculars,id'],
            'identifier' => ['required', 'string'],
            'method' => ['nullable', 'in:RFID,QR_CODE,BARCODE,FACE_RECOGNITION,MANUAL'],
            'device_code' => ['nullable', 'string'],
            'bypass_time_lock' => ['nullable', 'boolean'],
        ]);

        $extra = Extracurricular::findOrFail($validated['extracurricular_id']);
        $method = $validated['method'] ?? 'RFID';

        // Validasi Status Metode Presensi
        if ($method === 'RFID') {
            if (SchoolSetting::get('enable_attendance_rfid', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi kartu RFID saat ini dinonaktifkan di pengaturan sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        } elseif (in_array($method, ['QR_CODE', 'BARCODE'], true)) {
            if (SchoolSetting::get('enable_attendance_barcode', '1') !== '1' && SchoolSetting::get('enable_attendance_qr', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi Barcode Scanner saat ini dinonaktifkan di pengaturan sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        } elseif ($method === 'FACE_RECOGNITION') {
            if (SchoolSetting::get('enable_attendance_face_recognition', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi Face Recognition saat ini dinonaktifkan di pengaturan sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        } elseif ($method === 'MANUAL') {
            if (SchoolSetting::get('enable_attendance_manual', '1') !== '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode presensi manual saat ini dinonaktifkan di pengaturan sekolah.',
                    'error_code' => 'METHOD_DISABLED',
                ], 422);
            }
        }

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $nowTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // Validasi Time-Lock
        $timeLock = $extra->validateTimeLock();
        if (! $timeLock['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $timeLock['reason'],
                'error_code' => 'TIME_LOCKED',
                'details' => $timeLock,
            ], 422);
        }

        // Temukan Siswa
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

        // Validasi Keanggotaan Ekstrakurikuler
        $isMember = $extra->students()->where('students.id', $student->id)->exists();
        if (! $isMember) {
            return response()->json([
                'success' => false,
                'message' => "Siswa {$student->name} ({$student->schoolClass?->name}) belum terdaftar sebagai anggota {$extra->name}.",
                'error_code' => 'NOT_A_MEMBER',
                'student' => $student,
            ], 422);
        }

        // Cek Duplikasi Presensi Hari Ini
        $existing = ExtracurricularAttendance::where('extracurricular_id', $extra->id)
            ->where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            $scanTime = substr($existing->time, 0, 5);

            return response()->json([
                'success' => false,
                'message' => "Siswa {$student->name} sudah tercatat presensi {$extra->name} pada pukul {$scanTime} WIB.",
                'error_code' => 'ALREADY_RECORDED',
                'student' => $student,
                'existing' => $existing,
            ], 422);
        }

        // Deteksi Perangkat IoT / Terminal
        $deviceInfo = $request->userAgent();
        $deviceCode = $request->input('device_code');
        if ($deviceCode) {
            $device = AttendanceDevice::where('device_code', $deviceCode)->first();
            if ($device) {
                $deviceInfo = "[{$device->device_code}] {$device->device_name} ({$device->location_name})";
                $device->update(['last_ping_at' => now(), 'status' => 'ONLINE']);
            }
        }

        $attendance = ExtracurricularAttendance::create([
            'extracurricular_id' => $extra->id,
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'date' => $today,
            'time' => $nowTime,
            'status' => 'HADIR',
            'method' => $method,
            'device_info' => $deviceInfo,
            'recorded_by' => $user?->id,
            'notes' => 'Presensi mandiri ekstra via '.$method,
        ]);

        AuditLog::log('EXTRACURRICULAR_ATTENDANCE_SCAN', 'ExtracurricularAttendance', $attendance->id, null, [
            'extra' => $extra->name,
            'student' => $student->name,
            'method' => $method,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Presensi {$student->name} ({$student->schoolClass?->name}) untuk {$extra->name} berhasil dicatat!",
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
     * Endpoint API Presensi Kamera Selfie & Geofence GPS Ekstrakurikuler
     */
    public function storeSelfie(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'extracurricular_id' => ['required', 'exists:extracurriculars,id'],
            'student_id' => ['nullable', 'exists:students,id'],
            'selfie_image' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'bypass_time_lock' => ['nullable', 'boolean'],
        ]);

        $extra = Extracurricular::findOrFail($validated['extracurricular_id']);
        $student = $user->student ?? Student::find($validated['student_id'] ?? null);

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Data profil siswa tidak ditemukan.',
            ], 404);
        }

        // Validasi keanggotaan
        $isMember = $extra->students()->where('students.id', $student->id)->exists();
        if (! $isMember) {
            return response()->json([
                'success' => false,
                'message' => "Siswa {$student->name} belum terdaftar sebagai anggota {$extra->name}.",
                'error_code' => 'NOT_A_MEMBER',
            ], 422);
        }

        // Validasi metode selfie aktif
        if (SchoolSetting::get('enable_attendance_selfie', '1') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'Metode presensi selfie kamera saat ini dinonaktifkan oleh administrator.',
                'error_code' => 'METHOD_DISABLED',
            ], 422);
        }

        // Validasi time-lock
        $timeLock = $extra->validateTimeLock();
        if (! $timeLock['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $timeLock['reason'],
                'error_code' => 'TIME_LOCKED',
            ], 422);
        }

        // Validasi GPS Geofence
        $isGeofenceEnabled = SchoolSetting::get('enable_geofence_radius', '1') === '1';
        $schoolLat = (float) SchoolSetting::get('school_lat', -7.56845);
        $schoolLng = (float) SchoolSetting::get('school_lng', 112.04612);
        $geofenceRadius = (int) SchoolSetting::get('geofence_radius_meters', 100);

        if ($isGeofenceEnabled && ! empty($validated['latitude']) && ! empty($validated['longitude'])) {
            $distance = $this->calculateDistance($schoolLat, $schoolLng, (float) $validated['latitude'], (float) $validated['longitude']);
            if ($distance > $geofenceRadius) {
                return response()->json([
                    'success' => false,
                    'message' => "Posisi Anda berada di luar radius lokasi sekolah ({$distance} meter, batas maksimal {$geofenceRadius} meter).",
                    'error_code' => 'OUT_OF_GEOFENCE_RADIUS',
                    'distance_meters' => $distance,
                    'max_radius_meters' => $geofenceRadius,
                ], 422);
            }
        }

        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $nowTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // Cek duplikasi
        $existing = ExtracurricularAttendance::where('extracurricular_id', $extra->id)
            ->where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "Anda telah tercatat presensi {$extra->name} hari ini.",
                'error_code' => 'ALREADY_RECORDED',
            ], 422);
        }

        // Simpan foto selfie
        $selfiePath = null;
        if (! empty($validated['selfie_image']) && str_starts_with($validated['selfie_image'], 'data:image/')) {
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $validated['selfie_image']);
            $decoded = base64_decode($imageData);
            if ($decoded !== false) {
                $filename = 'extra_'.$extra->id.'_'.$student->id.'_'.time().'.jpg';
                Storage::disk('public')->put('extracurricular_selfies/'.$filename, $decoded);
                $selfiePath = 'extracurricular_selfies/'.$filename;
            }
        }

        $attendance = ExtracurricularAttendance::create([
            'extracurricular_id' => $extra->id,
            'student_id' => $student->id,
            'class_id' => $student->class_id,
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
        ]);

        return response()->json([
            'success' => true,
            'message' => "Presensi selfie {$student->name} untuk {$extra->name} berhasil disimpan!",
            'attendance' => $attendance,
        ]);
    }

    /**
     * Presensi Manual Masal Anggota Ekstrakurikuler
     */
    public function storeManual(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (SchoolSetting::get('enable_attendance_manual', '1') !== '1') {
            return back()->with('error', 'Metode presensi manual saat ini sedang dinonaktifkan oleh administrator sekolah.');
        }

        $validated = $request->validate([
            'extracurricular_id' => ['required', 'exists:extracurriculars,id'],
            'date' => ['required', 'date'],
            'statuses' => ['nullable', 'array'],
            'notes' => ['nullable', 'array'],
            'bypass_time_lock' => ['nullable', 'boolean'],
        ]);

        $extra = Extracurricular::findOrFail($validated['extracurricular_id']);

        $isDoubleSecretary = $user->hasRole('sekretaris_ekstra') && $user->hasRole('sekretaris_kelas');
        $isSingleExtraSecretary = ($user->hasRole('sekretaris_ekstra') || $user->isExtracurricularSecretary($extra->id))
            && ! $isDoubleSecretary
            && ! $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            && ! in_array($user->role, ['admin', 'super_admin'], true);

        $isExtraSecretary = $isDoubleSecretary || $isSingleExtraSecretary || (bool) ($user->student && ($user->isExtracurricularSecretary($extra->id) || $user->hasRole('sekretaris_ekstra')));

        if (! $user->canRecordExtracurricularManual($extra->id)) {
            abort(403, 'Akses Ditolak: Presensi manual ekstrakurikuler hanya dapat dilaksanakan oleh Wakasek Kesiswaan, Pembina Ekstrakurikuler, atau Sekretaris Ekstrakurikuler.');
        }

        // Requirement 2: Role sekretaris ekskul hanya bisa mengabsenkan manual pada ekstrakurikuler yang diikutinya saja,
        // kecuali posisinya double role menjadi sekretaris ekskul dan sekretaris kelas
        if ($isSingleExtraSecretary) {
            $isMember = $user->isExtracurricularMember($extra->id)
                || ($user->student && $extra->students()->where('students.id', $user->student->id)->exists());

            if (! $isMember) {
                abort(403, 'Sekretaris ekstrakurikuler hanya dapat mengabsenkan manual pada ekstrakurikuler yang diikutinya saja.');
            }
        }

        $date = $validated['date'];
        $nowTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // Validasi Time-Lock Ketat untuk Presensi Manual (Bisa dibypass jika bypass_time_lock dicentang oleh Pembina / Sekretaris / Admin)
        $bypassTimeLock = $request->boolean('bypass_time_lock');
        if (! $bypassTimeLock) {
            $targetDateTime = Carbon::parse($date.' '.$nowTime, 'Asia/Jakarta');
            $timeLock = $extra->validateTimeLock($targetDateTime);
            if (! $timeLock['allowed']) {
                return redirect()->route('extracurricular.index', [
                    'extra_id' => $extra->id,
                    'date' => $date,
                    'tab' => 'manual',
                ])->with('error', $timeLock['reason'].' (Centang opsi Bypass Jadwal jika melakukan absensi di luar jadwal rutin).');
            }
        }

        if (empty($validated['statuses'])) {
            return redirect()->route('extracurricular.index', [
                'extra_id' => $extra->id,
                'date' => $date,
                'tab' => 'manual',
            ])->with('warning', 'Tidak ada status presensi siswa yang dipilih.');
        }

        $savedCount = 0;
        foreach ($validated['statuses'] as $studentId => $status) {
            if (! in_array($status, ['HADIR', 'TERLAMBAT', 'IZIN', 'SAKIT', 'ALPA'], true)) {
                continue;
            }

            $student = Student::find($studentId);
            if (! $student) {
                continue;
            }

            $extraRecord = ExtracurricularAttendance::where('extracurricular_id', $extra->id)
                ->where('student_id', $student->id)
                ->whereDate('date', $date)
                ->first();

            $extraData = [
                'class_id' => $student->class_id,
                'time' => $nowTime,
                'status' => $status,
                'method' => 'MANUAL',
                'notes' => $validated['notes'][$studentId] ?? null,
                'recorded_by' => $user->id,
            ];

            if ($extraRecord) {
                $extraRecord->update($extraData);
            } else {
                ExtracurricularAttendance::create(array_merge([
                    'extracurricular_id' => $extra->id,
                    'student_id' => $student->id,
                    'date' => $date,
                ], $extraData));
            }

            $savedCount++;
        }

        AuditLog::log('MANUAL_EXTRACURRICULAR_ATTENDANCE', 'ExtracurricularAttendance', null, null, [
            'extracurricular_id' => $extra->id,
            'extracurricular_name' => $extra->name,
            'date' => $date,
            'count' => $savedCount,
            'recorded_by' => $user->name,
            'role' => $user->role ?? (is_array($user->roles) ? implode(',', $user->roles) : ''),
        ]);

        return redirect()->route('extracurricular.index', [
            'extra_id' => $extra->id,
            'date' => $date,
            'tab' => 'manual',
        ])->with('success', "Berhasil mencatat presensi manual untuk {$savedCount} anggota {$extra->name}. Data telah tersimpan.");
    }

    /**
     * Update Record Presensi Tunggal
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $att = ExtracurricularAttendance::findOrFail($id);

        $isDoubleSecretary = $user->hasRole('sekretaris_ekstra') && $user->hasRole('sekretaris_kelas');
        $isSingleExtraSecretary = ($user->hasRole('sekretaris_ekstra') || $user->isExtracurricularSecretary($att->extracurricular_id))
            && ! $isDoubleSecretary
            && ! $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            && ! in_array($user->role, ['admin', 'super_admin'], true);

        $isExtraSecretary = $isDoubleSecretary || $isSingleExtraSecretary || (bool) ($user->student && ($user->isExtracurricularSecretary($att->extracurricular_id) || $user->hasRole('sekretaris_ekstra')));

        $canRecordExtra = $user->canRecordAttendanceManual()
            || $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            || in_array($user->role, ['admin', 'super_admin'], true)
            || ($user->teacher && $att->extracurricular?->all_teachers->contains('id', $user->teacher->id))
            || $isExtraSecretary;

        if (! $canRecordExtra) {
            abort(403, 'Akses ditolak.');
        }

        if ($isSingleExtraSecretary) {
            $isMember = $user->isExtracurricularMember($att->extracurricular_id)
                || ($user->student && $att->extracurricular?->students()->where('students.id', $user->student->id)->exists());

            if (! $isMember) {
                abort(403, 'Sekretaris ekstrakurikuler hanya dapat mengelola presensi pada ekstrakurikuler yang diikutinya saja.');
            }
        }

        $validated = $request->validate([
            'status' => ['required', 'in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $att->update($validated);

        return back()->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Hapus Record Presensi Tunggal
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        $att = ExtracurricularAttendance::findOrFail($id);

        $isDoubleSecretary = $user->hasRole('sekretaris_ekstra') && $user->hasRole('sekretaris_kelas');
        $isSingleExtraSecretary = ($user->hasRole('sekretaris_ekstra') || $user->isExtracurricularSecretary($att->extracurricular_id))
            && ! $isDoubleSecretary
            && ! $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            && ! in_array($user->role, ['admin', 'super_admin'], true);

        $isExtraSecretary = $isDoubleSecretary || $isSingleExtraSecretary || (bool) ($user->student && ($user->isExtracurricularSecretary($att->extracurricular_id) || $user->hasRole('sekretaris_ekstra')));

        $canRecordExtra = $user->canRecordAttendanceManual()
            || $user->hasAnyRole(['pembina_ekstra', 'admin', 'super_admin', 'guru', 'wali_kelas'])
            || in_array($user->role, ['admin', 'super_admin'], true)
            || ($user->teacher && $att->extracurricular?->all_teachers->contains('id', $user->teacher->id))
            || $isExtraSecretary;

        if (! $canRecordExtra) {
            abort(403, 'Akses ditolak.');
        }

        if ($isSingleExtraSecretary) {
            $isMember = $user->isExtracurricularMember($att->extracurricular_id)
                || ($user->student && $att->extracurricular?->students()->where('students.id', $user->student->id)->exists());

            if (! $isMember) {
                abort(403, 'Sekretaris ekstrakurikuler hanya dapat mengelola presensi pada ekstrakurikuler yang diikutinya saja.');
            }
        }

        if ($att->selfie_path) {
            Storage::disk('public')->delete($att->selfie_path);
        }
        $att->delete();

        return back()->with('success', 'Data presensi berhasil dihapus.');
    }

    /**
     * Pembina Ekstrakurikuler atau Admin Mengatur / Mengubah Jadwal Ekstrakurikuler
     */
    public function updateSchedule(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $extra = Extracurricular::with(['teachers', 'teacher'])->findOrFail($id);

        $isAuthorized = $user->hasAnyRole(['admin', 'super_admin'])
            || in_array($user->role, ['admin', 'super_admin'], true)
            || ($user->teacher && $extra->all_teachers->contains('id', $user->teacher->id))
            || ($user->hasRole('pembina_ekstra') && ($extra->teacher_id == $user->teacher?->id || $extra->teachers()->where('teachers.id', $user->teacher?->id)->exists()));

        if (! $isAuthorized) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengatur jadwal kegiatan ekstrakurikuler ini.');
        }

        $validated = $request->validate([
            'schedule_days' => ['nullable', 'array'],
            'schedule_days.*' => ['string'],
            'schedule_day' => ['nullable', 'string', 'max:150'],
            'start_time' => ['nullable', 'string'],
            'end_time' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $daysInput = $request->input('schedule_days') ?? $request->input('schedule_day');
        if (is_array($daysInput)) {
            $validated['schedule_day'] = implode(', ', array_filter($daysInput));
        }
        unset($validated['schedule_days']);

        if (! empty($validated['start_time']) && strlen($validated['start_time']) === 5) {
            $validated['start_time'] .= ':00';
        }
        if (! empty($validated['end_time']) && strlen($validated['end_time']) === 5) {
            $validated['end_time'] .= ':00';
        }

        $extra->update($validated);

        AuditLog::log('UPDATE_EXTRACURRICULAR_SCHEDULE', 'Extracurricular', $extra->id, null, $validated);

        return redirect()->route('extracurricular.index', [
            'extra_id' => $extra->id,
        ])->with('success', "Jadwal kegiatan {$extra->name} berhasil diperbarui.");
    }

    /**
     * Rekap & Laporan Presensi Ekstrakurikuler
     */
    public function report(Request $request): View
    {
        $user = Auth::user();
        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);
        $extras = Extracurricular::where('is_active', true)->orderBy('name')->get();

        $selectedExtraId = $request->query('extra_id', $extras->first()?->id);
        $selectedExtra = $selectedExtraId ? Extracurricular::with('teacher')->find($selectedExtraId) : null;

        if ($isWaliKelas) {
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

        $query = ExtracurricularAttendance::with(['student.schoolClass', 'recorder', 'extracurricular'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($selectedExtraId) {
            $query->where('extracurricular_id', $selectedExtraId);
        }

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

        // Ringkasan Statistik
        $baseStats = ExtracurricularAttendance::whereBetween('date', [$startDate, $endDate])
            ->when($selectedExtraId, fn ($q) => $q->where('extracurricular_id', $selectedExtraId))
            ->when($statsClassFilter, fn ($q) => $q->whereIn('class_id', $statsClassFilter));

        $stats = [
            'total' => (clone $baseStats)->count(),
            'hadir' => (clone $baseStats)->where('status', 'HADIR')->count(),
            'terlambat' => (clone $baseStats)->where('status', 'TERLAMBAT')->count(),
            'izin' => (clone $baseStats)->whereIn('status', ['IZIN', 'SAKIT'])->count(),
            'alpa' => (clone $baseStats)->where('status', 'ALPA')->count(),
        ];

        return view('extracurricular.report', compact(
            'extras',
            'selectedExtra',
            'selectedExtraId',
            'classes',
            'selectedClassId',
            'startDate',
            'endDate',
            'statusFilter',
            'records',
            'stats'
        ));
    }

    /**
     * Cetak Berkas Dokumen Laporan Resmi Presensi Ekstrakurikuler (Format A4 KOP Lengkap)
     */
    public function printReport(Request $request): View
    {
        $user = Auth::user();
        if ($user->hasRole('siswa') || $user->role === 'siswa') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencetak dokumen.');
        }

        $isWaliKelas = ($user->hasRole('wali_kelas') || $user->role === 'wali_kelas') && ! $user->hasAnyRole(['admin', 'super_admin']);

        $selectedExtraId = $request->input('extra_id') ?: $request->query('extra_id');
        if (! $selectedExtraId) {
            $firstExtra = Extracurricular::where('is_active', true)->first();
            $selectedExtraId = $firstExtra?->id;
        }
        $extra = Extracurricular::with(['teacher', 'teachers'])->findOrFail($selectedExtraId);

        $selectedClassId = $request->input('class_id') ?: $request->query('class_id');
        if ($isWaliKelas) {
            $homeroomClasses = $user->getHomeroomClasses();
            $requestedClassId = $request->input('class_id') ?: $request->query('class_id');
            if ($requestedClassId && $homeroomClasses->contains('id', (int) $requestedClassId)) {
                $selectedClassId = (int) $requestedClassId;
            } else {
                $selectedClassId = $homeroomClasses->first()?->id;
            }
        }

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;

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

        $query = ExtracurricularAttendance::with(['student.schoolClass', 'recorder'])
            ->where('extracurricular_id', $extra->id)
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

        // Data Siswa Anggota Ekstra untuk Rekapitulasi Individu
        $memberStudents = $extra->students()
            ->with('schoolClass')
            ->when($selectedClassId, fn ($q) => $q->where('class_id', $selectedClassId))
            ->when($isWaliKelas && isset($homeroomClasses) && $homeroomClasses->isNotEmpty(), fn ($q) => $q->whereIn('class_id', $homeroomClasses->pluck('id')))
            ->where('students.is_active', true)
            ->orderBy('students.name')
            ->get();

        $studentRecaps = $memberStudents->map(function ($student) use ($records) {
            $stRecords = $records->where('student_id', $student->id);
            $hadir = $stRecords->where('status', 'HADIR')->count();
            $terlambat = $stRecords->where('status', 'TERLAMBAT')->count();
            $izin = $stRecords->where('status', 'IZIN')->count();
            $sakit = $stRecords->where('status', 'SAKIT')->count();
            $alpa = $stRecords->where('status', 'ALPA')->count();
            $total = $hadir + $terlambat + $izin + $sakit + $alpa;
            $presencePct = $total > 0 ? round((($hadir + $terlambat) / $total) * 100, 1) : 0;

            return [
                'student' => $student,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total' => $total,
                'percentage' => $presencePct,
            ];
        });

        $totals = [
            'students' => $memberStudents->count(),
            'hadir' => $studentRecaps->sum('hadir'),
            'terlambat' => $studentRecaps->sum('terlambat'),
            'izin' => $studentRecaps->sum('izin'),
            'sakit' => $studentRecaps->sum('sakit'),
            'alpa' => $studentRecaps->sum('alpa'),
            'avg_percentage' => $studentRecaps->count() > 0 ? round($studentRecaps->avg('percentage'), 1) : 0,
        ];

        // Data Sekolah & KOP Resmi
        $school = SchoolSetting::allAsAssoc();
        $schoolName = SchoolSetting::get('school_name') ?: ($school['school_name'] ?? 'SMA NEGERI 1 SUKABUMI');
        $schoolNpsn = SchoolSetting::get('school_npsn') ?: ($school['school_npsn'] ?? '20202265');
        $schoolAddress = SchoolSetting::get('school_address') ?: ($school['school_address'] ?? 'Sukabumi, Jawa Barat');
        $schoolCity = SchoolSetting::get('school_city') ?: ($school['school_city'] ?? 'Sukabumi');
        $headmasterName = SchoolSetting::get('headmaster_name') ?: ($school['headmaster_name'] ?? 'Drs. H. Mamat Rachmat, M.Pd.');
        $headmasterNip = SchoolSetting::get('headmaster_nip') ?: ($school['headmaster_nip'] ?? '-');
        $schoolLogo = SchoolSetting::get('school_logo', null);

        // KOP Surat dari Master Data - Logo dan Kop Surat
        $kopImage = SchoolSetting::get('school_kop') ?: (SchoolSetting::get('kop_surat_image') ?: SchoolSetting::get('kop_image'));
        $kopImageUrl = null;
        if ($kopImage && (Storage::disk('public')->exists($kopImage) || file_exists(public_path('storage/'.$kopImage)))) {
            $kopImageUrl = asset('storage/'.$kopImage);
        }

        $printDateFormatted = Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y');
        $startDateFormatted = Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y');
        $endDateFormatted = Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y');

        return view('extracurricular.print', compact(
            'extra',
            'selectedClass',
            'startDate',
            'endDate',
            'periodType',
            'periodTitle',
            'startDateFormatted',
            'endDateFormatted',
            'printDateFormatted',
            'records',
            'studentRecaps',
            'totals',
            'schoolName',
            'schoolNpsn',
            'schoolAddress',
            'schoolCity',
            'headmasterName',
            'headmasterNip',
            'schoolLogo',
            'kopImageUrl'
        ));
    }

    /**
     * Tambah Anggota Siswa ke Ekstrakurikuler (Bisa memilih beberapa siswa sekaligus / Bulk Add)
     */
    public function addMember(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $extra = Extracurricular::findOrFail($id);

        $isAuthorized = $user->hasAnyRole(['admin', 'super_admin'])
            || in_array($user->role, ['admin', 'super_admin'], true)
            || ($user->teacher && $user->teacher->all_extracurriculars->contains('id', $extra->id))
            || $user->hasRole('pembina_ekstra');

        if (! $isAuthorized) {
            abort(403, 'Anda tidak berwenang mengelola anggota ekstrakurikuler ini.');
        }

        $validated = $request->validate([
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'student_id' => ['nullable', 'exists:students,id'],
            'role' => ['nullable', 'string', 'max:50'],
        ]);

        $role = $validated['role'] ?? 'Anggota';
        $studentIds = $request->input('student_ids', []);
        if ($request->filled('student_id')) {
            $studentIds[] = (int) $request->input('student_id');
        }

        $studentIds = array_values(array_unique(array_filter(array_map('intval', (array) $studentIds))));

        if (empty($studentIds)) {
            return redirect()->route('extracurricular.index', [
                'extra_id' => $extra->id,
                'tab' => 'members',
            ])->with('error', 'Silakan pilih minimal 1 siswa calon anggota terlebih dahulu.');
        }

        $syncData = [];
        $today = now()->toDateString();
        foreach ($studentIds as $sId) {
            $syncData[$sId] = [
                'role' => $role,
                'joined_at' => $today,
            ];
        }

        $extra->students()->syncWithoutDetaching($syncData);

        // Jika peran yang diberikan adalah Sekretaris, sinkronkan role sekretaris_ekstra pada akun siswa
        if (in_array($role, ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true)) {
            $targetStudents = Student::with('user')->whereIn('id', $studentIds)->get();
            foreach ($targetStudents as $targetStudent) {
                if ($targetStudent->user) {
                    $studentUser = $targetStudent->user;
                    $roles = $studentUser->roles ?? ['siswa'];
                    if (! in_array('sekretaris_ekstra', $roles, true)) {
                        $roles[] = 'sekretaris_ekstra';
                        $studentUser->roles = array_values(array_unique($roles));
                        if ($studentUser->role === 'siswa') {
                            $studentUser->role = 'sekretaris_ekstra';
                        }
                        $studentUser->save();
                    }
                }
            }
        }

        AuditLog::log('ADD_EXTRACURRICULAR_MEMBER', 'Extracurricular', $extra->id, null, [
            'student_ids' => $studentIds,
            'count' => count($studentIds),
            'role' => $role,
        ]);

        $count = count($studentIds);
        $message = $count === 1
            ? 'Siswa berhasil ditambahkan sebagai '.$role.' '.$extra->name.'.'
            : $count.' siswa berhasil ditambahkan sekaligus sebagai '.$role.' '.$extra->name.'.';

        return redirect()->route('extracurricular.index', [
            'extra_id' => $extra->id,
            'tab' => 'members',
        ])->with('success', $message);
    }

    /**
     * Hapus / Keluarkan Anggota Siswa dari Ekstrakurikuler
     */
    public function removeMember(int $id, int $studentId): RedirectResponse
    {
        $user = Auth::user();
        $extra = Extracurricular::findOrFail($id);

        $isAuthorized = $user->hasAnyRole(['admin', 'super_admin'])
            || in_array($user->role, ['admin', 'super_admin'], true)
            || ($user->teacher && $user->teacher->all_extracurriculars->contains('id', $extra->id))
            || $user->hasRole('pembina_ekstra');

        if (! $isAuthorized) {
            abort(403, 'Anda tidak berwenang mengelola anggota ekstrakurikuler ini.');
        }

        $extra->students()->detach($studentId);

        $targetStudent = Student::with('user')->find($studentId);
        if ($targetStudent && $targetStudent->user) {
            $isStillSecretaryInOther = $targetStudent->extracurriculars()
                ->where('extracurriculars.id', '!=', $extra->id)
                ->wherePivotIn('role', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'])
                ->exists();

            if (! $isStillSecretaryInOther) {
                $studentUser = $targetStudent->user;
                $roles = array_values(array_diff($studentUser->roles ?? ['siswa'], ['sekretaris_ekstra']));
                $studentUser->roles = $roles;
                if ($studentUser->role === 'sekretaris_ekstra') {
                    $studentUser->role = 'siswa';
                }
                $studentUser->save();
            }
        }

        AuditLog::log('REMOVE_EXTRACURRICULAR_MEMBER', 'Extracurricular', $extra->id, null, [
            'student_id' => $studentId,
        ]);

        return redirect()->route('extracurricular.index', [
            'extra_id' => $extra->id,
            'tab' => 'members',
        ])->with('success', 'Anggota berhasil dikeluarkan dari '.$extra->name.'.');
    }

    /**
     * Ubah / Perbarui Peran Anggota (misal: Anggota <-> Sekretaris)
     */
    public function updateMemberRole(Request $request, int $id, int $studentId): RedirectResponse
    {
        $user = Auth::user();
        $extra = Extracurricular::findOrFail($id);

        $isAuthorized = $user->hasAnyRole(['admin', 'super_admin'])
            || in_array($user->role, ['admin', 'super_admin'], true)
            || ($user->teacher && $user->teacher->all_extracurriculars->contains('id', $extra->id))
            || $user->hasRole('pembina_ekstra');

        if (! $isAuthorized) {
            abort(403, 'Anda tidak berwenang mengelola anggota ekstrakurikuler ini.');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'max:50'],
        ]);

        $extra->students()->updateExistingPivot($studentId, [
            'role' => $validated['role'],
        ]);

        $targetStudent = Student::with('user')->find($studentId);
        if ($targetStudent && $targetStudent->user) {
            $studentUser = $targetStudent->user;
            $roles = $studentUser->roles ?? ['siswa'];
            if (in_array($validated['role'], ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'], true)) {
                if (! in_array('sekretaris_ekstra', $roles, true)) {
                    $roles[] = 'sekretaris_ekstra';
                    $studentUser->roles = array_values(array_unique($roles));
                    if ($studentUser->role === 'siswa') {
                        $studentUser->role = 'sekretaris_ekstra';
                    }
                    $studentUser->save();
                }
            } else {
                $isStillSecretaryInOther = $targetStudent->extracurriculars()
                    ->where('extracurriculars.id', '!=', $extra->id)
                    ->wherePivotIn('role', ['Sekretaris', 'sekretaris_ekstra', 'Sekretaris Ekstrakurikuler'])
                    ->exists();

                if (! $isStillSecretaryInOther) {
                    $roles = array_values(array_diff($roles, ['sekretaris_ekstra']));
                    $studentUser->roles = $roles;
                    if ($studentUser->role === 'sekretaris_ekstra') {
                        $studentUser->role = 'siswa';
                    }
                    $studentUser->save();
                }
            }
        }

        AuditLog::log('UPDATE_EXTRACURRICULAR_MEMBER_ROLE', 'Extracurricular', $extra->id, null, [
            'student_id' => $studentId,
            'role' => $validated['role'],
        ]);

        return redirect()->route('extracurricular.index', [
            'extra_id' => $extra->id,
            'tab' => 'members',
        ])->with('success', 'Peran siswa berhasil diperbarui menjadi '.$validated['role'].'.');
    }
}

<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\AttendanceSelfie;
use App\Models\AttendanceSession;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceService
{
    /**
     * Calculate Haversine distance between two coordinates in meters.
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Get active schedule for a given day (Senin s.d. Sabtu) with fallback to global settings.
     */
    public static function getScheduleForDay(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::now();
        $dayMap = [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ];
        $dayKey = $dayMap[$date->dayOfWeek] ?? 'senin';

        // Legacy global fallbacks
        $legacyStart = SchoolSetting::get('attendance_start_time', '06:00');
        $legacyCheckin = SchoolSetting::get('attendance_checkin_time', '07:00');
        $legacyLate = SchoolSetting::get('attendance_late_cutoff_time', '07:30');
        $legacyEnd = SchoolSetting::get('attendance_end_time', '17:30');

        $defaultOutStart = match ($dayKey) {
            'jumat' => '11:45',
            'sabtu' => '13:00',
            default => '15:00',
        };

        $isActive = $dayKey === 'minggu'
            ? false
            : SchoolSetting::get("attendance_{$dayKey}_is_active", '1') === '1';

        return [
            'day' => $dayKey,
            'is_active' => $isActive,
            'in_start' => SchoolSetting::get("attendance_{$dayKey}_in_start", $legacyStart),
            'in_on_time' => SchoolSetting::get("attendance_{$dayKey}_in_on_time", $legacyCheckin),
            'in_late_cutoff' => SchoolSetting::get("attendance_{$dayKey}_in_late_cutoff", $legacyLate),
            'out_start' => SchoolSetting::get("attendance_{$dayKey}_out_start", $defaultOutStart),
            'out_end' => SchoolSetting::get("attendance_{$dayKey}_out_end", $legacyEnd),
        ];
    }

    /**
     * Process Realtime Selfie Camera Attendance with GPS Geofencing (Sesi Masuk & Sesi Pulang).
     *
     * @param  array  $data  Contains 'selfie_image' (base64 from canvas), 'latitude', 'longitude', 'accuracy', 'device_info', 'liveness_score', 'session_type'
     */
    public function processSelfieAttendance(Student $student, array $data): array
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $schedule = self::getScheduleForDay($now);

        // 0. Validasi Pengaturan Metode Presensi
        if (SchoolSetting::get('enable_attendance_selfie', '1') !== '1') {
            return [
                'success' => false,
                'message' => 'Metode presensi selfie kamera saat ini sedang dinonaktifkan oleh administrator sekolah.',
                'error_code' => 'METHOD_DISABLED',
            ];
        }

        // Validasi Hari Aktif Sekolah
        if (! $schedule['is_active']) {
            return [
                'success' => false,
                'message' => "Hari ini ({$schedule['day']}) sekolah dinyatakan libur atau bukan merupakan hari aktif presensi.",
                'error_code' => 'DAY_OFF',
            ];
        }

        // 1. Validasi Kamera Langsung (Strict Anti-Upload Rule)
        $selfieBase64 = $data['selfie_image'] ?? null;
        if (empty($selfieBase64) || ! str_starts_with($selfieBase64, 'data:image/')) {
            AuditLog::log('ATTENDANCE_REJECTED', 'Student', $student->id, null, [
                'reason' => 'Invalid camera feed. No base64 stream.',
            ]);

            return [
                'success' => false,
                'message' => 'Presensi selfie wajib diambil langsung dari kamera aktif perangkat. Mengunggah atau memilih foto dilarang.',
                'error_code' => 'INVALID_CAMERA_STREAM',
            ];
        }

        // 2. Validasi GPS & Geofencing
        $location = AttendanceLocation::primary();
        if (! $location) {
            $location = (object) [
                'latitude' => (float) SchoolSetting::get('school_lat', -7.56845),
                'longitude' => (float) SchoolSetting::get('school_lng', 112.04612),
                'radius_meters' => (int) SchoolSetting::get('geofence_radius_meters', 100),
                'name' => 'SMA Negeri 1 Lengkong',
            ];
        }

        $userLat = isset($data['latitude']) ? (float) $data['latitude'] : null;
        $userLng = isset($data['longitude']) ? (float) $data['longitude'] : null;

        if ($userLat === null || $userLng === null) {
            return [
                'success' => false,
                'message' => 'Izin lokasi GPS diperlukan untuk validasi kehadiran di area sekolah.',
                'error_code' => 'GPS_REQUIRED',
            ];
        }

        $distance = $this->calculateDistance($location->latitude, $location->longitude, $userLat, $userLng);

        if ($distance > $location->radius_meters) {
            AuditLog::log('ATTENDANCE_OUT_OF_GEOFENCE', 'Student', $student->id, null, [
                'user_distance' => $distance,
                'allowed_radius' => $location->radius_meters,
            ]);

            return [
                'success' => false,
                'message' => "Anda berada di luar area presensi sekolah. Jarak Anda {$distance} meter dari titik sekolah (Maksimal radius: {$location->radius_meters} meter).",
                'error_code' => 'OUT_OF_RADIUS',
                'distance' => $distance,
                'allowed_radius' => $location->radius_meters,
            ];
        }

        // 3. Deteksi Sesi Presensi: Sesi Masuk vs Sesi Pulang
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        $sessionType = $data['session_type'] ?? null;

        // Jika siswa sudah check-in dan sudah check-out hari ini
        if ($existingAttendance && ! empty($existingAttendance->time) && ! empty($existingAttendance->time_out)) {
            return [
                'success' => false,
                'message' => 'Anda telah menyelesaikan presensi masuk (pukul '.substr($existingAttendance->time, 0, 5).' WIB) dan presensi pulang (pukul '.substr($existingAttendance->time_out, 0, 5).' WIB) untuk hari ini.',
                'error_code' => 'ALREADY_COMPLETED',
                'attendance' => $existingAttendance,
            ];
        }

        // VALIDASI JADWAL PULANG VS MASUK KEMBALI
        if ($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT']) && empty($existingAttendance->time_out)) {
            if ($sessionType === 'checkout') {
                if ($currentTime < $schedule['out_start']) {
                    return [
                        'success' => false,
                        'message' => "Presensi pulang belum dibuka. Sesi presensi pulang hari {$schedule['day']} baru dibuka pukul {$schedule['out_start']} WIB.",
                        'error_code' => 'CHECKOUT_TOO_EARLY',
                        'attendance' => $existingAttendance,
                    ];
                }

                if ($currentTime > $schedule['out_end']) {
                    return [
                        'success' => false,
                        'message' => "Sesi presensi pulang untuk hari ini telah berakhir pada pukul {$schedule['out_end']} WIB.",
                        'error_code' => 'CHECKOUT_CLOSED',
                        'attendance' => $existingAttendance,
                    ];
                }
            } else {
                // Siswa mencoba check-in lagi sebelum waktu pulang
                if ($currentTime < $schedule['out_start']) {
                    return [
                        'success' => false,
                        'message' => 'Anda telah melakukan presensi masuk hari ini pada pukul '.substr($existingAttendance->time, 0, 5)." WIB. Sesi presensi pulang dibuka pukul {$schedule['out_start']} WIB.",
                        'error_code' => 'ALREADY_ATTENDED',
                        'attendance' => $existingAttendance,
                    ];
                }
            }
        }

        $isCheckout = ($sessionType === 'checkout') ||
            ($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT']) && empty($existingAttendance->time_out) && $currentTime >= $schedule['out_start']);

        // PROSES SESI PULANG
        if ($isCheckout && $existingAttendance && ! empty($existingAttendance->time)) {
            $imagePath = $this->saveBase64Image($selfieBase64, 'selfies');

            $existingAttendance->time_out = $currentTime;
            $existingAttendance->out_method = 'SELFIE';
            $existingAttendance->out_selfie_path = $imagePath;
            $existingAttendance->out_latitude = $userLat;
            $existingAttendance->out_longitude = $userLng;
            $existingAttendance->out_device_info = $data['device_info'] ?? request()->userAgent();
            $existingAttendance->notes = ($existingAttendance->notes ? $existingAttendance->notes.' • ' : '')."Pulang: {$currentTime} WIB.";
            $existingAttendance->save();

            AuditLog::log('ATTENDANCE_CHECKOUT_SUCCESS', 'Attendance', $existingAttendance->id, null, [
                'time_out' => $currentTime,
                'distance' => $distance,
            ]);

            return [
                'success' => true,
                'session' => 'checkout',
                'message' => "Presensi pulang berhasil dicatat pada pukul {$currentTime} WIB! Selamat beristirahat.",
                'status' => $existingAttendance->status,
                'verification_status' => 'VALID',
                'time' => $existingAttendance->time,
                'time_out' => $currentTime,
                'distance' => $distance,
                'attendance' => $existingAttendance,
            ];
        }

        // PROSES SESI MASUK
        $startTime = $schedule['in_start'];
        $checkinTime = $schedule['in_on_time'];
        $lateCutoffTime = $schedule['in_late_cutoff'];

        if ($currentTime < $startTime) {
            return [
                'success' => false,
                'message' => "Presensi masuk belum dibuka. Jam presensi dimulai pukul {$startTime} WIB.",
                'error_code' => 'TIME_TOO_EARLY',
            ];
        }

        $status = ($currentTime <= $checkinTime) ? 'HADIR' : 'TERLAMBAT';
        // Aturan: Siswa yang hadir di dalam radius sekolah statusnya otomatis VALID tanpa perlu validasi manual!
        $verificationStatus = 'VALID';
        $diffMinutes = ($status === 'TERLAMBAT') ? Carbon::parse($checkinTime)->diffInMinutes($now) : 0;
        $notes = ($status === 'HADIR')
            ? 'Presensi masuk selfie kamera langsung tervalidasi.'
            : "Terlambat {$diffMinutes} menit (Batas masuk: {$checkinTime} WIB). Tervalidasi otomatis di area sekolah.";

        // Simpan Foto Selfie Masuk
        $imagePath = $this->saveBase64Image($selfieBase64, 'selfies');

        // Simpan Data Presensi Masuk
        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->id, 'date' => $today->toDateString()],
            [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'student_name' => $student->name,
                'class_id' => $student->class_id,
                'time' => $currentTime,
                'method' => 'SELFIE',
                'selfie_path' => $imagePath,
                'latitude' => $userLat,
                'longitude' => $userLng,
                'gps_accuracy' => $data['accuracy'] ?? null,
                'device_info' => $data['device_info'] ?? request()->userAgent(),
                'ip_address' => request()->ip(),
                'status' => $status,
                'verification_status' => $verificationStatus,
                'notes' => $notes,
            ]
        );

        // Simpan Metadata Selfie
        AttendanceSelfie::create([
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
            'image_path' => $imagePath,
            'face_detected' => true,
            'liveness_score' => $data['liveness_score'] ?? 1.00,
            'client_metadata' => [
                'distance_meters' => $distance,
                'accuracy' => $data['accuracy'] ?? null,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        // Audit Trail
        AuditLog::log('ATTENDANCE_SELFIE_SUCCESS', 'Attendance', $attendance->id, null, [
            'status' => $status,
            'time' => $currentTime,
            'distance' => $distance,
        ]);

        // Notifikasi untuk Orang Tua jika ada
        $parent = $student->parents()->first();
        if ($parent && $parent->user_id) {
            Notification::create([
                'user_id' => $parent->user_id,
                'title' => ($status === 'HADIR') ? 'Anak Anda Telah Hadir di Sekolah' : 'Pemberitahuan Keterlambatan Siswa',
                'message' => "{$student->name} melakukan presensi selfie pada pukul {$currentTime} WIB dengan status: {$status} (Otomatis VALID).",
                'type' => 'ATTENDANCE',
                'link_url' => '/parent/dashboard',
            ]);
        }

        return [
            'success' => true,
            'session' => 'checkin',
            'message' => ($status === 'HADIR') ? 'Presensi masuk selfie berhasil dicatat tepat waktu (Status: VALID)!' : "Presensi masuk tercatat ({$notes}).",
            'status' => $status,
            'verification_status' => 'VALID',
            'time' => $currentTime,
            'distance' => $distance,
            'attendance' => $attendance,
        ];
    }

    /**
     * Process Dynamic QR Code Attendance.
     */
    public function processQrAttendance(Student $student, string $qrToken, array $meta = []): array
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');

        // 0. Validasi Pengaturan Metode Presensi
        if (SchoolSetting::get('enable_attendance_qr', '1') !== '1') {
            return [
                'success' => false,
                'message' => 'Metode presensi scan QR Code saat ini sedang dinonaktifkan oleh administrator sekolah.',
                'error_code' => 'METHOD_DISABLED',
            ];
        }

        $session = AttendanceSession::where('qr_code_token', $qrToken)->first();

        if (! $session) {
            return [
                'success' => false,
                'message' => 'QR Code tidak valid atau sesi presensi tidak ditemukan.',
                'error_code' => 'INVALID_QR_TOKEN',
            ];
        }

        if (! $session->isQrValid()) {
            return [
                'success' => false,
                'message' => 'QR Code telah kedaluwarsa. Silakan minta guru untuk memperbarui QR Code sesi.',
                'error_code' => 'EXPIRED_QR_CODE',
            ];
        }

        // Validasi apakah siswa termasuk dalam kelas sesi tersebut
        if ($session->class_id && $session->class_id !== $student->class_id) {
            return [
                'success' => false,
                'message' => 'Sesi presensi QR ini ditujukan untuk kelas yang berbeda.',
                'error_code' => 'CLASS_MISMATCH',
            ];
        }

        // Deteksi Sesi Masuk vs Sesi Pulang
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance && ! empty($existingAttendance->time) && ! empty($existingAttendance->time_out)) {
            return [
                'success' => false,
                'message' => 'Anda telah menyelesaikan presensi masuk dan pulang untuk hari ini.',
                'error_code' => 'ALREADY_COMPLETED',
                'attendance' => $existingAttendance,
            ];
        }

        $schedule = self::getScheduleForDay($now);
        if (! $schedule['is_active']) {
            return [
                'success' => false,
                'message' => "Hari ini ({$schedule['day']}) bukan merupakan hari aktif presensi sekolah.",
                'error_code' => 'DAY_OFF',
            ];
        }

        $sessionType = $meta['session_type'] ?? null;
        $wantsCheckout = ($sessionType === 'checkout') ||
            ($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT']) && empty($existingAttendance->time_out));

        if ($wantsCheckout && $existingAttendance && ! empty($existingAttendance->time)) {
            if ($currentTime < $schedule['out_start']) {
                return [
                    'success' => false,
                    'message' => "Presensi pulang belum dibuka. Sesi presensi pulang hari {$schedule['day']} baru dibuka pukul {$schedule['out_start']} WIB.",
                    'error_code' => 'CHECKOUT_TOO_EARLY',
                    'attendance' => $existingAttendance,
                ];
            }

            if ($currentTime > $schedule['out_end']) {
                return [
                    'success' => false,
                    'message' => "Sesi presensi pulang untuk hari ini telah berakhir pada pukul {$schedule['out_end']} WIB.",
                    'error_code' => 'CHECKOUT_CLOSED',
                    'attendance' => $existingAttendance,
                ];
            }
        }

        $isCheckout = $wantsCheckout && $currentTime >= $schedule['out_start'];

        if ($isCheckout && $existingAttendance && ! empty($existingAttendance->time)) {
            $existingAttendance->time_out = $currentTime;
            $existingAttendance->out_method = 'QR_CODE';
            $existingAttendance->out_device_info = $meta['device_info'] ?? request()->userAgent();
            $existingAttendance->notes = ($existingAttendance->notes ? $existingAttendance->notes.' • ' : '')."Pulang via QR: {$currentTime} WIB.";
            $existingAttendance->save();

            AuditLog::log('ATTENDANCE_QR_CHECKOUT_SUCCESS', 'Attendance', $existingAttendance->id, null, [
                'session_id' => $session->id,
                'time_out' => $currentTime,
            ]);

            return [
                'success' => true,
                'session' => 'checkout',
                'message' => "Presensi pulang QR Code berhasil dicatat pada pukul {$currentTime} WIB.",
                'status' => $existingAttendance->status,
                'verification_status' => 'VALID',
                'time' => $existingAttendance->time,
                'time_out' => $currentTime,
                'attendance' => $existingAttendance,
            ];
        }

        if ($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT'])) {
            return [
                'success' => false,
                'message' => 'Anda telah melakukan presensi masuk hari ini pada pukul '.substr($existingAttendance->time, 0, 5).' WIB.',
                'error_code' => 'ALREADY_ATTENDED',
                'attendance' => $existingAttendance,
            ];
        }

        if ($currentTime < $schedule['in_start']) {
            return [
                'success' => false,
                'message' => "Presensi masuk belum dibuka. Jam presensi dimulai pukul {$schedule['in_start']} WIB.",
                'error_code' => 'TIME_TOO_EARLY',
            ];
        }

        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->id, 'date' => $today->toDateString()],
            [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'student_name' => $student->name,
                'class_id' => $student->class_id,
                'time' => $currentTime,
                'method' => 'QR_CODE',
                'device_info' => $meta['device_info'] ?? request()->userAgent(),
                'ip_address' => request()->ip(),
                'status' => 'HADIR',
                'verification_status' => 'VALID',
                'notes' => "Presensi QR Code sesi '{$session->subject_or_activity}' tervalidasi.",
                'session_id' => $session->id,
            ]
        );

        AuditLog::log('ATTENDANCE_QR_SUCCESS', 'Attendance', $attendance->id, null, [
            'session_id' => $session->id,
            'qr_token' => substr($qrToken, 0, 10).'...',
        ]);

        return [
            'success' => true,
            'message' => "Presensi QR Code berhasil dicatat untuk sesi {$session->subject_or_activity}.",
            'status' => 'HADIR',
            'attendance' => $attendance,
        ];
    }

    /**
     * Process RFID UID Attendance Scan.
     */
    public function processRfidAttendance(string $rfidUid, ?string $deviceCode = null): array
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');

        // 0. Validasi Pengaturan Metode Presensi
        if (SchoolSetting::get('enable_attendance_rfid', '1') !== '1') {
            return [
                'success' => false,
                'message' => 'Metode presensi kartu RFID saat ini sedang dinonaktifkan oleh administrator sekolah.',
                'error_code' => 'METHOD_DISABLED',
            ];
        }

        $student = Student::where('rfid_uid', $rfidUid)->first();
        if (! $student) {
            AuditLog::log('RFID_UNKNOWN_UID', null, null, null, ['rfid_uid' => $rfidUid]);

            return [
                'success' => false,
                'message' => 'Kartu RFID belum terdaftar pada data siswa mana pun.',
                'error_code' => 'RFID_NOT_FOUND',
            ];
        }

        $existingAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance && ! empty($existingAttendance->time) && ! empty($existingAttendance->time_out)) {
            return [
                'success' => false,
                'message' => "Siswa {$student->name} telah menyelesaikan presensi masuk dan pulang hari ini.",
                'error_code' => 'ALREADY_COMPLETED',
                'attendance' => $existingAttendance,
            ];
        }

        $schedule = self::getScheduleForDay($now);
        if (! $schedule['is_active']) {
            return [
                'success' => false,
                'message' => "Hari ini ({$schedule['day']}) bukan merupakan hari aktif presensi sekolah.",
                'error_code' => 'DAY_OFF',
            ];
        }

        if ($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT']) && empty($existingAttendance->time_out)) {
            if ($currentTime < $schedule['out_start']) {
                return [
                    'success' => false,
                    'message' => "Siswa {$student->name} sudah melakukan presensi masuk pukul ".substr($existingAttendance->time, 0, 5)." WIB. Sesi presensi pulang dibuka pukul {$schedule['out_start']} WIB.",
                    'error_code' => 'CHECKOUT_TOO_EARLY',
                    'attendance' => $existingAttendance,
                ];
            }

            if ($currentTime > $schedule['out_end']) {
                return [
                    'success' => false,
                    'message' => "Sesi presensi pulang untuk hari ini telah berakhir pada pukul {$schedule['out_end']} WIB.",
                    'error_code' => 'CHECKOUT_CLOSED',
                    'attendance' => $existingAttendance,
                ];
            }

            $existingAttendance->time_out = $currentTime;
            $existingAttendance->out_method = 'RFID';
            $existingAttendance->notes = ($existingAttendance->notes ? $existingAttendance->notes.' • ' : '')."Pulang via RFID ({$deviceCode}): {$currentTime} WIB.";
            $existingAttendance->save();

            AuditLog::log('ATTENDANCE_RFID_CHECKOUT_SUCCESS', 'Attendance', $existingAttendance->id, null, [
                'rfid_uid' => $rfidUid,
                'time_out' => $currentTime,
                'device' => $deviceCode,
            ]);

            return [
                'success' => true,
                'session' => 'checkout',
                'message' => "Presensi pulang kartu RFID berhasil dicatat untuk siswa {$student->name} pada pukul {$currentTime} WIB.",
                'status' => $existingAttendance->status,
                'verification_status' => 'VALID',
                'time' => $existingAttendance->time,
                'time_out' => $currentTime,
                'student' => [
                    'name' => $student->name,
                    'nis' => $student->nis,
                    'class' => $student->schoolClass?->name,
                ],
                'attendance' => $existingAttendance,
            ];
        }

        if ($existingAttendance && in_array($existingAttendance->status, ['HADIR', 'TERLAMBAT'])) {
            return [
                'success' => false,
                'message' => "Siswa {$student->name} sudah melakukan presensi masuk hari ini pada pukul ".substr($existingAttendance->time, 0, 5).' WIB.',
                'error_code' => 'ALREADY_ATTENDED',
                'attendance' => $existingAttendance,
            ];
        }

        if ($currentTime < $schedule['in_start']) {
            return [
                'success' => false,
                'message' => "Presensi masuk belum dibuka. Jam presensi dimulai pukul {$schedule['in_start']} WIB.",
                'error_code' => 'TIME_TOO_EARLY',
            ];
        }

        $checkinTime = $schedule['in_on_time'];
        $status = ($currentTime <= $checkinTime) ? 'HADIR' : 'TERLAMBAT';

        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->id, 'date' => $today->toDateString()],
            [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'student_name' => $student->name,
                'class_id' => $student->class_id,
                'time' => $currentTime,
                'method' => 'RFID',
                'status' => $status,
                'verification_status' => 'VALID',
                'notes' => "Presensi scan kartu RFID otomatis (Perangkat: {$deviceCode}).",
            ]
        );

        AuditLog::log('ATTENDANCE_RFID_SUCCESS', 'Attendance', $attendance->id, null, [
            'rfid_uid' => $rfidUid,
            'device' => $deviceCode,
        ]);

        return [
            'success' => true,
            'message' => "Presensi RFID berhasil dicatat untuk siswa {$student->name}.",
            'status' => $status,
            'student' => [
                'name' => $student->name,
                'nis' => $student->nis,
                'class' => $student->schoolClass?->name,
            ],
            'attendance' => $attendance,
        ];
    }

    /**
     * Submit Student Leave/Sick Request with Live Camera Selfie & Real-time GPS.
     * No radius geofence restriction, but strictly requires live selfie camera and coordinates.
     */
    public function submitLeaveRequest(Student $student, array $data, ?UploadedFile $attachmentFile = null): array
    {
        $today = Carbon::today();
        $currentTime = Carbon::now()->format('H:i:s');
        $status = strtoupper($data['status'] ?? 'IZIN');
        if (! in_array($status, ['IZIN', 'SAKIT', 'PULANG_CEPAT'])) {
            $status = 'IZIN';
        }

        // 1. Strict Live Selfie Camera Validation (Anti-Upload Rule)
        $selfieBase64 = $data['selfie_image'] ?? null;
        if (empty($selfieBase64) || ! str_starts_with($selfieBase64, 'data:image/')) {
            AuditLog::log('LEAVE_REQUEST_REJECTED', 'Student', $student->id, null, [
                'reason' => 'Invalid camera feed. No live selfie base64.',
            ]);

            return [
                'success' => false,
                'message' => 'Foto selfie wajib diambil secara langsung melalui kamera aktif. Mengambil atau mengunggah gambar dari galeri dilarang.',
                'error_code' => 'INVALID_CAMERA_STREAM',
            ];
        }

        // 2. Real-time GPS Tracking (Required, but without radius geofence blocking)
        $userLat = isset($data['latitude']) && is_numeric($data['latitude']) ? (float) $data['latitude'] : null;
        $userLng = isset($data['longitude']) && is_numeric($data['longitude']) ? (float) $data['longitude'] : null;
        $gpsAccuracy = isset($data['accuracy']) && is_numeric($data['accuracy']) ? (float) $data['accuracy'] : null;

        if ($userLat === null || $userLng === null) {
            return [
                'success' => false,
                'message' => 'Koordinat GPS posisi Anda diperlukan untuk merekam lokasi pengajuan izin/sakit secara realtime.',
                'error_code' => 'GPS_REQUIRED',
            ];
        }

        // Optional: calculate distance to school for metadata/informational display
        $location = AttendanceLocation::primary();
        $distance = 0;
        if ($location) {
            $distance = $this->calculateDistance($location->latitude, $location->longitude, $userLat, $userLng);
        }

        // 3. Validasi Tanggal dan Batas Waktu Pengajuan Izin/Sakit/Pulang Cepat
        $targetDate = ! empty($data['date']) ? Carbon::parse($data['date'])->toDateString() : $today->toDateString();

        if ($status !== 'PULANG_CEPAT') {
            // A. Dilarang mengajukan izin/sakit untuk tanggal yang sudah lewat
            if ($targetDate < $today->toDateString()) {
                return [
                    'success' => false,
                    'message' => 'Pengajuan izin atau sakit tidak dapat dilakukan untuk tanggal yang telah lewat.',
                    'error_code' => 'DATE_PAST',
                ];
            }

            // B. Pengajuan untuk Hari H (Hari Ini): Wajib diajukan sebelum pukul 06:30 WIB
            $cutoffTime = SchoolSetting::get('leave_request_cutoff_time', '06:30');
            if ($targetDate === $today->toDateString() && $currentTime > $cutoffTime.':00') {
                return [
                    'success' => false,
                    'message' => "Pengajuan izin atau sakit untuk hari ini maksimal diajukan sebelum pukul {$cutoffTime} WIB. Untuk keperluan mendesak saat kegiatan belajar mengajar berlangsung, silakan ajukan Izin Pulang Cepat atau hubungi pihak sekolah.",
                    'error_code' => 'LEAVE_CUTOFF_EXCEEDED',
                ];
            }

            // C. Pengajuan Jauh Hari / Malam Harinya ($targetDate > $today->toDateString()):
            // Bebas diajukan kapan saja tanpa batasan jam (Advance submission allowed).
        } else {
            // Khusus PULANG_CEPAT: Wajib diajukan untuk hari ini saat KBM berlangsung
            if ($targetDate !== $today->toDateString()) {
                return [
                    'success' => false,
                    'message' => 'Pengajuan izin pulang cepat hanya dapat diajukan untuk hari ini saat kegiatan belajar mengajar berlangsung.',
                    'error_code' => 'EARLY_LEAVE_TODAY_ONLY',
                ];
            }
        }

        // 4. Save Selfie Image to storage
        $imagePath = $this->saveBase64Image($selfieBase64, 'selfies');

        // 5. Handle Optional Attachment (Doctor's Note / Parent Letter)
        $attachmentPath = null;
        if ($attachmentFile && $attachmentFile->isValid()) {
            $attachmentPath = $attachmentFile->store('leaves/attachments', 'public');
        }

        // 6. Create or Update Attendance Record with Pending Verification

        if ($status === 'PULANG_CEPAT') {
            $earlyLeaveTime = $data['early_leave_time'] ?? $currentTime;
            $earlyNotes = $data['notes'] ?? 'Pengajuan izin pulang cepat siswa';
            $existing = Attendance::where('student_id', $student->id)->where('date', $targetDate)->first();

            if ($existing && in_array($existing->status, ['HADIR', 'TERLAMBAT'])) {
                // Preserve morning arrival check-in data
                $attendance = $existing;
                $attendance->is_early_leave = true;
                $attendance->early_leave_time = $earlyLeaveTime;
                $attendance->early_leave_reason = $earlyNotes;
                $attendance->status = 'PULANG_CEPAT';
                $attendance->verification_status = 'PERLU_VERIFIKASI';
                $attendance->out_selfie_path = $imagePath;
                $attendance->out_latitude = $userLat;
                $attendance->out_longitude = $userLng;
                $attendance->attachment_path = $attachmentPath ?? $attendance->attachment_path;
                $attendance->notes = ($attendance->notes ? $attendance->notes.' • ' : '')."Ajuan Izin Pulang Cepat: {$earlyNotes} (Rencana: {$earlyLeaveTime} WIB)";
                $attendance->save();
            } else {
                $attendance = Attendance::updateOrCreate(
                    ['student_id' => $student->id, 'date' => $targetDate],
                    [
                        'nis' => $student->nis,
                        'nisn' => $student->nisn,
                        'student_name' => $student->name,
                        'class_id' => $student->class_id,
                        'time' => $currentTime,
                        'method' => 'SELFIE',
                        'selfie_path' => $imagePath,
                        'latitude' => $userLat,
                        'longitude' => $userLng,
                        'gps_accuracy' => $gpsAccuracy,
                        'device_info' => $data['device_info'] ?? request()->userAgent(),
                        'ip_address' => request()->ip(),
                        'status' => 'PULANG_CEPAT',
                        'is_early_leave' => true,
                        'early_leave_time' => $earlyLeaveTime,
                        'early_leave_reason' => $earlyNotes,
                        'verification_status' => 'PERLU_VERIFIKASI',
                        'notes' => "Ajuan Izin Pulang Cepat: {$earlyNotes} (Rencana: {$earlyLeaveTime} WIB)",
                        'attachment_path' => $attachmentPath,
                        'approved_by' => null,
                        'approved_at' => null,
                        'rejection_reason' => null,
                    ]
                );
            }
        } else {
            $notes = $data['notes'] ?? ($status === 'SAKIT' ? 'Ajuan izin sakit siswa' : 'Ajuan izin keperluan siswa');
            $attendance = Attendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => $targetDate],
                [
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'student_name' => $student->name,
                    'class_id' => $student->class_id,
                    'time' => $currentTime,
                    'method' => 'IZIN_SAKIT',
                    'selfie_path' => $imagePath,
                    'latitude' => $userLat,
                    'longitude' => $userLng,
                    'gps_accuracy' => $gpsAccuracy,
                    'device_info' => $data['device_info'] ?? request()->userAgent(),
                    'ip_address' => request()->ip(),
                    'status' => $status,
                    'is_early_leave' => false,
                    'verification_status' => 'PERLU_VERIFIKASI',
                    'notes' => $notes,
                    'attachment_path' => $attachmentPath,
                    'approved_by' => null,
                    'approved_at' => null,
                    'rejection_reason' => null,
                ]
            );
        }

        // Record metadata in AttendanceSelfie
        AttendanceSelfie::create([
            'attendance_id' => $attendance->id,
            'student_id' => $student->id,
            'image_path' => $imagePath,
            'face_detected' => true,
            'liveness_score' => 1.00,
            'client_metadata' => [
                'is_leave_request' => true,
                'status' => $status,
                'distance_meters' => $distance,
                'accuracy' => $gpsAccuracy,
                'attachment' => $attachmentPath,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        // Audit Trail
        AuditLog::log('ATTENDANCE_LEAVE_SUBMITTED', 'Attendance', $attendance->id, null, [
            'status' => $status,
            'date' => $targetDate,
            'latitude' => $userLat,
            'longitude' => $userLng,
        ]);

        // 6. Notify Homeroom Teacher (Wali Kelas) & Administrators
        $homeroomTeacher = $student->schoolClass?->homeroomTeacher;
        if ($homeroomTeacher && $homeroomTeacher->user_id) {
            Notification::create([
                'user_id' => $homeroomTeacher->user_id,
                'title' => "Ajuan {$status}: {$student->name} ({$student->schoolClass?->name})",
                'message' => "Siswa {$student->name} mengajukan {$status} pada tanggal {$targetDate}. Menunggu verifikasi Anda.",
                'type' => 'ATTENDANCE',
                'link_url' => '/attendance/leaves',
            ]);
        }

        // Notify Admins & Super Admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => "Ajuan {$status}: {$student->name} ({$student->schoolClass?->name})",
                'message' => "Siswa {$student->name} mengajukan {$status} pada tanggal {$targetDate}. Menunggu persetujuan.",
                'type' => 'ATTENDANCE',
                'link_url' => '/attendance/leaves',
            ]);
        }

        return [
            'success' => true,
            'message' => "Ajuan {$status} Anda berhasil dikirim dan sedang menunggu persetujuan Wali Kelas atau Administrator.",
            'attendance' => $attendance,
        ];
    }

    /**
     * Approve Leave Request by Homeroom Teacher or Admin.
     */
    public function approveLeave(Attendance $attendance, User $actor): array
    {
        if ($actor->role === 'wali_kelas') {
            $homeroomClass = $actor->teacher?->homeroomClass;
            if (! $homeroomClass || $attendance->class_id !== $homeroomClass->id) {
                return [
                    'success' => false,
                    'message' => 'Anda hanya berhak menyetujui ajuan siswa dari kelas binaan Anda.',
                ];
            }
        } elseif (! in_array($actor->role, ['admin', 'super_admin'])) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk menyetujui ajuan izin/sakit.',
            ];
        }

        $updateData = [
            'verification_status' => 'DIVERIFIKASI',
            'approved_by' => $actor->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ];

        // Jika ajuan Pulang Cepat, otomatis catat jam kepulangan (time_out) dan metode checkout
        if ($attendance->status === 'PULANG_CEPAT' || $attendance->is_early_leave) {
            $updateData['time_out'] = $attendance->early_leave_time ?? now()->format('H:i:s');
            $updateData['out_method'] = 'PULANG_CEPAT';
            if (empty($attendance->out_selfie_path) && $attendance->selfie_path) {
                $updateData['out_selfie_path'] = $attendance->selfie_path;
            }
        }

        $attendance->update($updateData);

        AuditLog::log('ATTENDANCE_LEAVE_APPROVED', 'Attendance', $attendance->id, null, [
            'approved_by' => $actor->name,
            'role' => $actor->role,
            'status' => $attendance->status,
        ]);

        // Notify Student if user exists
        $studentUser = $attendance->student?->user;
        if ($studentUser) {
            Notification::create([
                'user_id' => $studentUser->id,
                'title' => "Ajuan {$attendance->status} Disetujui",
                'message' => "Ajuan {$attendance->status} Anda untuk tanggal {$attendance->date->format('d/m/Y')} telah disetujui oleh {$actor->name}.",
                'type' => 'ATTENDANCE',
                'link_url' => '/attendance/history',
            ]);
        }

        return [
            'success' => true,
            'message' => "Ajuan {$attendance->status} siswa {$attendance->student_name} berhasil disetujui.",
        ];
    }

    /**
     * Reject Leave Request by Homeroom Teacher or Admin.
     */
    public function rejectLeave(Attendance $attendance, User $actor, string $reason): array
    {
        if ($actor->role === 'wali_kelas') {
            $homeroomClass = $actor->teacher?->homeroomClass;
            if (! $homeroomClass || $attendance->class_id !== $homeroomClass->id) {
                return [
                    'success' => false,
                    'message' => 'Anda hanya berhak memproses ajuan siswa dari kelas binaan Anda.',
                ];
            }
        } elseif (! in_array($actor->role, ['admin', 'super_admin'])) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk menolak ajuan izin/sakit.',
            ];
        }

        $updateData = [
            'verification_status' => 'DITOLAK',
            'approved_by' => $actor->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ];

        // Jika ajuan pulang cepat ditolak namun siswa sebelumnya sudah hadir, pulihkan status ke HADIR
        if (($attendance->status === 'PULANG_CEPAT' || $attendance->is_early_leave) && $attendance->time) {
            $updateData['status'] = 'HADIR';
            $updateData['is_early_leave'] = false;
        }

        $attendance->update($updateData);

        AuditLog::log('ATTENDANCE_LEAVE_REJECTED', 'Attendance', $attendance->id, null, [
            'rejected_by' => $actor->name,
            'reason' => $reason,
        ]);

        $studentUser = $attendance->student?->user;
        if ($studentUser) {
            Notification::create([
                'user_id' => $studentUser->id,
                'title' => "Ajuan {$attendance->status} Ditolak",
                'message' => "Ajuan {$attendance->status} Anda untuk tanggal {$attendance->date->format('d/m/Y')} ditolak. Alasan: {$reason}",
                'type' => 'ATTENDANCE',
                'link_url' => '/attendance/history',
            ]);
        }

        return [
            'success' => true,
            'message' => "Ajuan {$attendance->status} siswa {$attendance->student_name} telah ditolak.",
        ];
    }

    /**
     * Record Manual Attendance by Homeroom Teacher or Admin.
     */
    public function recordManualAttendance(array $data, User $actor): array
    {
        $student = Student::with('schoolClass')->findOrFail($data['student_id']);

        if ($actor->role === 'wali_kelas') {
            $homeroomClass = $actor->teacher?->homeroomClass;
            if (! $homeroomClass || $student->class_id !== $homeroomClass->id) {
                return [
                    'success' => false,
                    'message' => 'Wali Kelas hanya berhak menginput absensi manual untuk siswa di kelas binaannya.',
                ];
            }
        } elseif (! in_array($actor->role, ['admin', 'super_admin'])) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk melakukan absensi manual.',
            ];
        }

        $date = ! empty($data['date']) ? Carbon::parse($data['date'])->toDateString() : Carbon::today()->toDateString();
        $time = ! empty($data['time']) ? $data['time'] : Carbon::now()->format('H:i:s');
        $status = strtoupper($data['status'] ?? 'HADIR');
        $notes = $data['notes'] ?? "Presensi manual dicatat oleh {$actor->name} ({$actor->role}).";

        $attendance = Attendance::updateOrCreate(
            ['student_id' => $student->id, 'date' => $date],
            [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'student_name' => $student->name,
                'class_id' => $student->class_id,
                'time' => $time,
                'method' => 'MANUAL',
                'status' => $status,
                'verification_status' => 'DIVERIFIKASI',
                'approved_by' => $actor->id,
                'approved_at' => now(),
                'notes' => $notes,
            ]
        );

        AuditLog::log('ATTENDANCE_MANUAL_RECORDED', 'Attendance', $attendance->id, null, [
            'student' => $student->name,
            'status' => $status,
            'recorded_by' => $actor->name,
        ]);

        return [
            'success' => true,
            'message' => "Absensi manual untuk {$student->name} ({$status}) berhasil disimpan.",
            'attendance' => $attendance,
        ];
    }

    /**
     * Helper to store base64 image data to public storage.
     */
    protected function saveBase64Image(string $base64Data, string $folder = 'selfies'): string
    {
        $imageParts = explode(';base64,', $base64Data);
        $imageTypeAux = explode('image/', $imageParts[0]);
        $imageType = $imageTypeAux[1] ?? 'jpg';
        $imageBase64 = base64_decode($imageParts[1]);

        $fileName = $folder.'/'.Str::random(32).'.'.$imageType;
        Storage::disk('public')->put($fileName, $imageBase64);

        return $fileName;
    }
}

<?php

use App\Models\AttendanceDevice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendance_devices', function (Blueprint $table) {
            $table->string('device_type', 30)->default('RFID')->change();
            if (! Schema::hasColumn('attendance_devices', 'notes')) {
                $table->string('notes')->nullable()->after('secret_key');
            }
        });

        // Seed 10 RFID Devices
        $rfidDevices = [
            ['code' => 'RFID-01', 'name' => 'Mesin RFID 01 - Gerbang Utama Masuk', 'location' => 'Pos Satpam Gerbang Utama (Jalur Masuk)', 'ip' => '192.168.1.101'],
            ['code' => 'RFID-02', 'name' => 'Mesin RFID 02 - Gerbang Utama Keluar', 'location' => 'Pos Satpam Gerbang Utama (Jalur Keluar)', 'ip' => '192.168.1.102'],
            ['code' => 'RFID-03', 'name' => 'Mesin RFID 03 - Gerbang Barat', 'location' => 'Pintu Gerbang Barat (Gedung Lab IPA)', 'ip' => '192.168.1.103'],
            ['code' => 'RFID-04', 'name' => 'Mesin RFID 04 - Gerbang Timur', 'location' => 'Pintu Gerbang Timur (Area Lapangan Olahraga)', 'ip' => '192.168.1.104'],
            ['code' => 'RFID-05', 'name' => 'Mesin RFID 05 - Lobby Gedung Utama', 'location' => 'Lobby Gedung Utama (Ruang Guru / TU)', 'ip' => '192.168.1.105'],
            ['code' => 'RFID-06', 'name' => 'Mesin RFID 06 - Koridor Sayap Kelas X', 'location' => 'Koridor Kelas X (Lantai 1)', 'ip' => '192.168.1.106'],
            ['code' => 'RFID-07', 'name' => 'Mesin RFID 07 - Koridor Sayap Kelas XI', 'location' => 'Koridor Kelas XI (Lantai 2)', 'ip' => '192.168.1.107'],
            ['code' => 'RFID-08', 'name' => 'Mesin RFID 08 - Koridor Sayap Kelas XII', 'location' => 'Koridor Kelas XII (Lantai 2)', 'ip' => '192.168.1.108'],
            ['code' => 'RFID-09', 'name' => 'Mesin RFID 09 - Perpustakaan & Lab Komputer', 'location' => 'Gedung Perpustakaan & Lab TIK', 'ip' => '192.168.1.109'],
            ['code' => 'RFID-10', 'name' => 'Mesin RFID 10 - Kiosk Cadangan Serbaguna', 'location' => 'Ruang BK & Kiosk Presensi Cadangan', 'ip' => '192.168.1.110'],
        ];

        foreach ($rfidDevices as $dev) {
            AttendanceDevice::firstOrCreate(
                ['device_code' => $dev['code']],
                [
                    'device_name' => $dev['name'],
                    'device_type' => 'RFID',
                    'location_name' => $dev['location'],
                    'ip_address' => $dev['ip'],
                    'status' => 'ONLINE',
                    'last_ping_at' => now(),
                    'notes' => 'Perangkat Pembaca Kartu RFID Siswa Terpadu',
                ]
            );
        }

        // Seed 10 QR Code Scanner Devices
        $qrDevices = [
            ['code' => 'QR-01', 'name' => 'Mesin Scan QR 01 - Lobby Utama', 'location' => 'Lobby Gedung Utama / Resepsionis', 'ip' => '192.168.1.201'],
            ['code' => 'QR-02', 'name' => 'Mesin Scan QR 02 - Pintu Timur', 'location' => 'Pintu Masuk Area Parkir Timur', 'ip' => '192.168.1.202'],
            ['code' => 'QR-03', 'name' => 'Mesin Scan QR 03 - Pintu Barat', 'location' => 'Pintu Masuk Area Kantin & Lab Barat', 'ip' => '192.168.1.203'],
            ['code' => 'QR-04', 'name' => 'Mesin Scan QR 04 - Aula Pertemuan', 'location' => 'Pintu Depan Gedung Aula Serbaguna', 'ip' => '192.168.1.204'],
            ['code' => 'QR-05', 'name' => 'Mesin Scan QR 05 - Kiosk Gedung Guru', 'location' => 'Koridor Depan Ruang Guru & Kesiswaan', 'ip' => '192.168.1.205'],
            ['code' => 'QR-06', 'name' => 'Mesin Scan QR 06 - Kiosk Kelas X', 'location' => 'Area Tengah Koridor Kelas X', 'ip' => '192.168.1.206'],
            ['code' => 'QR-07', 'name' => 'Mesin Scan QR 07 - Kiosk Kelas XI', 'location' => 'Area Tengah Koridor Kelas XI', 'ip' => '192.168.1.207'],
            ['code' => 'QR-08', 'name' => 'Mesin Scan QR 08 - Kiosk Kelas XII', 'location' => 'Area Tengah Koridor Kelas XII', 'ip' => '192.168.1.208'],
            ['code' => 'QR-09', 'name' => 'Mesin Scan QR 09 - Lab Komputer & Multimedia', 'location' => 'Ruang Praktikum Komputer 1 & 2', 'ip' => '192.168.1.209'],
            ['code' => 'QR-10', 'name' => 'Mesin Scan QR 10 - Kiosk QR Cadangan', 'location' => 'Kiosk Portabel Scanner Acara / Cadangan', 'ip' => '192.168.1.210'],
        ];

        foreach ($qrDevices as $dev) {
            AttendanceDevice::firstOrCreate(
                ['device_code' => $dev['code']],
                [
                    'device_name' => $dev['name'],
                    'device_type' => 'QR_CODE',
                    'location_name' => $dev['location'],
                    'ip_address' => $dev['ip'],
                    'status' => 'ONLINE',
                    'last_ping_at' => now(),
                    'notes' => 'Perangkat Pembaca / Scanner QR Code Presensi Dinamis',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_devices', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_devices', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};

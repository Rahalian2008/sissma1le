<?php

namespace App\Console\Commands;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncStudentDapodikIdentity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:sync-dapodik-identity {--json= : Path ke file parsed_students.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi identitas siswa lengkap (Tempat/Tanggal Lahir, NIK, Agama, Alamat Lengkap, Nama Ayah/Ibu, Sekolah Asal) dari file Dapodik';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $jsonPath = $this->option('json') ?: 'C:\Users\RAHALIAN\.gemini\antigravity\brain\0eaac5d1-b019-4977-a37d-c5f78cd3709d\scratch\parsed_students.json';

        if (! file_exists($jsonPath)) {
            $this->error("File JSON data siswa tidak ditemukan di: {$jsonPath}");

            return self::FAILURE;
        }

        $raw = file_get_contents($jsonPath);
        // Strip UTF-8 BOM if present
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        $records = json_decode($raw, true);

        if (! is_array($records)) {
            $this->error('Gagal membaca format JSON: '.json_last_error_msg());

            return self::FAILURE;
        }

        $this->info('Memulai sinkronisasi data identitas '.count($records).' siswa dari Dapodik SMAN 1 LENGKONG...');

        $updatedCount = 0;
        $notFoundCount = 0;

        foreach ($records as $item) {
            $nama = trim($item['nama'] ?? '');
            $nisn = trim($item['nisn'] ?? '');
            $nis = trim($item['nis'] ?? '');

            if (empty($nama)) {
                continue;
            }

            // 1. Cari berdasarkan NISN
            $student = null;
            if (! empty($nisn)) {
                $student = Student::where('nisn', $nisn)->first();
            }

            // 2. Cari berdasarkan NIS jika belum ketemu
            if (! $student && ! empty($nis)) {
                $student = Student::where('nis', $nis)->first();
            }

            // 3. Cari berdasarkan Nama (case-insensitive)
            if (! $student) {
                $student = Student::whereRaw('LOWER(name) = ?', [strtolower($nama)])->first();
            }

            // 4. Cari dengan LIKE nama jika ada perbedaan spasi / gelar
            if (! $student) {
                $cleanNama = preg_replace('/\s+/', ' ', $nama);
                $student = Student::where('name', 'LIKE', $cleanNama.'%')->first();
            }

            if (! $student) {
                $notFoundCount++;

                continue;
            }

            $birthDate = null;
            if (! empty($item['tanggal_lahir'])) {
                try {
                    $birthDate = Carbon::parse($item['tanggal_lahir'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $birthDate = null;
                }
            }

            $student->update([
                'birth_place' => ! empty($item['tempat_lahir']) ? $item['tempat_lahir'] : $student->birth_place,
                'birth_date' => $birthDate ?: $student->birth_date,
                'nik' => ! empty($item['nik']) ? $item['nik'] : $student->nik,
                'religion' => ! empty($item['agama']) ? $item['agama'] : $student->religion,
                'address' => ! empty($item['alamat_lengkap']) ? $item['alamat_lengkap'] : $student->address,
                'father_name' => ! empty($item['nama_ayah']) ? $item['nama_ayah'] : $student->father_name,
                'mother_name' => ! empty($item['nama_ibu']) ? $item['nama_ibu'] : $student->mother_name,
                'previous_school' => ! empty($item['sekolah_asal']) ? $item['sekolah_asal'] : $student->previous_school,
            ]);

            $updatedCount++;
        }

        $this->info('Sinkronisasi Selesai!');
        $this->info("- Berhasil diperbarui: {$updatedCount} siswa");
        $this->info("- Siswa tidak ditemukan: {$notFoundCount} siswa");

        return self::SUCCESS;
    }
}

<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentFamily;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DapodikImportService
{
    /**
     * Import and update students identity and demographic data from Dapodik Excel file
     */
    public function importFromFile(string $filePath): array
    {
        if (! file_exists($filePath)) {
            return [
                'status' => 'error',
                'message' => "Berkas tidak ditemukan pada path: {$filePath}",
            ];
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return [
                'status' => 'error',
                'message' => 'Gagal membuka berkas Excel (.xlsx). Pastikan format berkas valid.',
            ];
        }

        // 1. Load shared strings
        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml) {
            $xmlStrings = simplexml_load_string($sharedXml);
            foreach ($xmlStrings->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } elseif (isset($si->r)) {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string) $r->t;
                    }
                    $sharedStrings[] = $text;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }

        // 2. Load worksheet sheet1.xml
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (! $sheetXml) {
            return [
                'status' => 'error',
                'message' => 'Lembar kerja sheet1.xml tidak ditemukan di dalam berkas Excel.',
            ];
        }

        $xmlSheet = simplexml_load_string($sheetXml);

        $totalRows = 0;
        $updatedStudents = 0;
        $createdStudents = 0;
        $familyUpdated = 0;

        DB::beginTransaction();

        try {
            foreach ($xmlSheet->sheetData->row as $row) {
                $rNum = (int) $row['r'];
                if ($rNum < 7) {
                    continue;
                }

                $cells = [];
                foreach ($row->c as $c) {
                    $cellRef = (string) $c['r'];
                    $col = preg_replace('/[0-9]/', '', $cellRef);
                    $type = (string) $c['t'];
                    $val = (string) $c->v;
                    $cells[$col] = ($type === 's') ? ($sharedStrings[(int) $val] ?? '') : $val;
                }

                $name = trim($cells['B'] ?? '');
                $nipd = trim($cells['C'] ?? '');
                $gender = trim($cells['D'] ?? 'L');
                $nisn = trim($cells['E'] ?? '');
                $birthPlace = trim($cells['F'] ?? '');
                $rawBirthDate = trim($cells['G'] ?? '');
                $nik = trim($cells['H'] ?? '');
                $religion = trim($cells['I'] ?? 'Islam');
                $addressStreet = trim($cells['J'] ?? '');
                $rt = trim($cells['K'] ?? '');
                $rw = trim($cells['L'] ?? '');
                $dusun = trim($cells['M'] ?? '');
                $kelurahan = trim($cells['N'] ?? '');
                $kecamatan = trim($cells['O'] ?? '');
                $postalCode = trim($cells['P'] ?? '');
                $livingWith = trim($cells['Q'] ?? '');
                $phone = trim($cells['T'] ?? ($cells['S'] ?? ''));
                $email = trim($cells['U'] ?? '');
                $rombel = trim($cells['AQ'] ?? '');
                $examNumber = trim($cells['AR'] ?? ($cells['V'] ?? ''));
                $diplomaNumber = trim($cells['AS'] ?? '');
                $schoolOrigin = trim($cells['BE'] ?? '');
                $childOrder = ! empty($cells['BF']) ? (int) $cells['BF'] : null;
                $familyCardNumber = trim($cells['BI'] ?? '');
                $weight = ! empty($cells['BJ']) ? (int) $cells['BJ'] : null;
                $height = ! empty($cells['BK']) ? (int) $cells['BK'] : null;
                $siblingsCount = ! empty($cells['BM']) ? (int) $cells['BM'] : null;

                // Format Tanggal Lahir
                $birthDate = null;
                if ($rawBirthDate) {
                    try {
                        $birthDate = Carbon::parse($rawBirthDate)->format('Y-m-d');
                    } catch (\Throwable) {
                        $birthDate = null;
                    }
                }

                // Format RT/RW
                $rtRw = null;
                if ($rt || $rw) {
                    $rtRw = 'RT '.($rt ?: '00').' / RW '.($rw ?: '00');
                }

                // Format Alamat Lengkap
                $fullAddressParts = array_filter([$addressStreet, $dusun, $kelurahan, $kecamatan]);
                $fullAddress = ! empty($fullAddressParts) ? implode(', ', $fullAddressParts) : 'Kec. Lengkong, Kab. Sukabumi';

                if (empty($name) && empty($nisn)) {
                    continue;
                }

                $totalRows++;

                // Cari siswa berdasarkan NISN terlebih dahulu, lalu NIPD / NIS, lalu Nama
                $student = null;
                if ($nisn) {
                    $student = Student::where('nisn', $nisn)->first();
                }
                if (! $student && $nipd) {
                    $student = Student::where('nis', $nipd)->first();
                }
                if (! $student && $name) {
                    $student = Student::where('name', $name)->first();
                }

                $classLevel = explode(' ', $rombel)[0] ?? 'X';

                if ($student) {
                    $student->update([
                        'nis' => $nipd ?: $student->nis,
                        'nisn' => $nisn ?: $student->nisn,
                        'nik' => $nik ?: $student->nik,
                        'family_card_number' => $familyCardNumber ?: $student->family_card_number,
                        'name' => $name ?: $student->name,
                        'gender' => ($gender === 'P' || stripos($gender, 'Perempuan') !== false) ? 'P' : 'L',
                        'birth_place' => $birthPlace ?: $student->birth_place,
                        'birth_date' => $birthDate ?: $student->birth_date,
                        'religion' => $religion ?: $student->religion,
                        'child_order' => $childOrder ?: $student->child_order,
                        'siblings_count' => $siblingsCount ?: $student->siblings_count,
                        'height' => $height ?: $student->height,
                        'weight' => $weight ?: $student->weight,
                        'living_with' => $livingWith ?: $student->living_with,
                        'address' => $fullAddress ?: $student->address,
                        'rt_rw' => $rtRw ?: $student->rt_rw,
                        'postal_code' => $postalCode ?: $student->postal_code,
                        'phone' => $phone ?: $student->phone,
                        'email' => $email ?: $student->email,
                        'school_origin' => $schoolOrigin ?: $student->school_origin,
                        'junior_diploma_number' => $diplomaNumber ?: $student->junior_diploma_number,
                        'junior_exam_number' => $examNumber ?: $student->junior_exam_number,
                        'current_rombel' => $rombel ?: $student->current_rombel,
                        'current_class_level' => $classLevel ?: $student->current_class_level,
                    ]);
                    $updatedStudents++;
                } else {
                    $newNis = $nipd;
                    if (! $newNis) {
                        $newNis = $nisn ? 'NIS-'.$nisn : 'DPK-'.str_pad((string) $totalRows, 6, '0', STR_PAD_LEFT);
                        $suffix = 1;
                        while (Student::where('nis', $newNis)->exists()) {
                            $newNis = 'DPK-'.str_pad((string) $totalRows, 5, '0', STR_PAD_LEFT).'-'.$suffix;
                            $suffix++;
                        }
                    }

                    $student = Student::create([
                        'nis' => $newNis,
                        'nisn' => $nisn,
                        'nik' => $nik,
                        'family_card_number' => $familyCardNumber,
                        'name' => $name,
                        'gender' => ($gender === 'P' || stripos($gender, 'Perempuan') !== false) ? 'P' : 'L',
                        'birth_place' => $birthPlace ?: 'Sukabumi',
                        'birth_date' => $birthDate ?: '2008-01-01',
                        'religion' => $religion ?: 'Islam',
                        'child_order' => $childOrder,
                        'siblings_count' => $siblingsCount,
                        'height' => $height,
                        'weight' => $weight,
                        'living_with' => $livingWith ?: 'Bersama Orang Tua',
                        'address' => $fullAddress,
                        'rt_rw' => $rtRw,
                        'postal_code' => $postalCode,
                        'phone' => $phone,
                        'email' => $email,
                        'school_origin' => $schoolOrigin,
                        'junior_diploma_number' => $diplomaNumber,
                        'junior_exam_number' => $examNumber,
                        'current_rombel' => $rombel ?: "{$classLevel} Merdeka 1",
                        'current_class_level' => $classLevel,
                        'admission_class' => $rombel ?: "{$classLevel} Merdeka 1",
                        'status' => 'Aktif',
                    ]);
                    $createdStudents++;
                }

                // Data Orang Tua & Wali
                $fatherName = trim($cells['Y'] ?? '');
                $fatherNik = trim($cells['AD'] ?? '');
                $fatherEdu = trim($cells['AA'] ?? '');
                $fatherJob = trim($cells['AB'] ?? '');
                $fatherIncome = trim($cells['AC'] ?? '');

                $motherName = trim($cells['AE'] ?? '');
                $motherNik = trim($cells['AJ'] ?? '');
                $motherEdu = trim($cells['AG'] ?? '');
                $motherJob = trim($cells['AH'] ?? '');
                $motherIncome = trim($cells['AI'] ?? '');

                $guardianName = trim($cells['AK'] ?? '');
                $guardianEdu = trim($cells['AM'] ?? '');
                $guardianJob = trim($cells['AN'] ?? '');
                $guardianIncome = trim($cells['AO'] ?? '');

                if ($fatherName || $motherName || $guardianName || $fatherNik || $motherNik) {
                    StudentFamily::updateOrCreate(
                        ['student_id' => $student->id],
                        [
                            'father_name' => $fatherName ?: null,
                            'father_nik' => $fatherNik ?: null,
                            'father_education' => $fatherEdu ?: null,
                            'father_occupation' => $fatherJob ?: null,
                            'father_income' => $fatherIncome ?: null,
                            'mother_name' => $motherName ?: null,
                            'mother_nik' => $motherNik ?: null,
                            'mother_education' => $motherEdu ?: null,
                            'mother_occupation' => $motherJob ?: null,
                            'mother_income' => $motherIncome ?: null,
                            'guardian_name' => $guardianName ?: null,
                            'guardian_education' => $guardianEdu ?: null,
                            'guardian_occupation' => $guardianJob ?: null,
                            'guardian_income' => $guardianIncome ?: null,
                        ]
                    );
                    $familyUpdated++;
                }
            }

            DB::commit();

            AuditTrailService::log('update', 'siswa', "Memperbarui identitas lengkap {$updatedStudents} siswa & {$familyUpdated} data keluarga dari berkas Dapodik");

            return [
                'status' => 'success',
                'message' => "Berhasil memperbarui data identitas {$updatedStudents} siswa & {$familyUpdated} data orang tua/wali dari berkas Dapodik.",
                'total_rows' => $totalRows,
                'updated_students' => $updatedStudents,
                'created_students' => $createdStudents,
                'family_records' => $familyUpdated,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DapodikImportService Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return [
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses data: '.$e->getMessage(),
            ];
        }
    }
}

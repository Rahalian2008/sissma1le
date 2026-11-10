<?php

namespace Database\Seeders;

use App\Models\PpdbRegistration;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpmbSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $year = date('Y');

        $tkaStudents = [
            [
                'full_name' => 'Ahmad Fauzan Pratama',
                'nisn' => '0081234567',
                'nik' => '3202123456780001',
                'gender' => 'L',
                'birth_place' => 'Sukabumi',
                'birth_date' => '2010-05-14',
                'school_origin' => 'SMP Negeri 1 Lengkong',
                'address' => 'Kp. Cijambe RT 02/01 Ds. Lengkong',
                'parent_name' => 'Dedi Pratama',
                'parent_phone' => '081234567801',
                'average_score' => 88.50,
                'selection_track' => 'Tes Kemampuan Akademik (TKA)',
                'tka_exam_session' => 'Sesi 1 (Lab CBT 1)',
                'tka_exam_date' => Carbon::now()->subDays(2)->toDateString(),
                'tka_score_literacy' => 92.00,
                'tka_score_numeracy' => 95.00,
                'tka_score_science' => 89.00,
                'tka_status' => 'Selesai Ujian',
                'status' => 'Lolos Seleksi',
                'assigned_class' => 'X Merdeka 1',
            ],
            [
                'full_name' => 'Siti Aisyah Rahmawati',
                'nisn' => '0081234568',
                'nik' => '3202123456780002',
                'gender' => 'P',
                'birth_place' => 'Sukabumi',
                'birth_date' => '2010-08-21',
                'school_origin' => 'MTs Negeri 2 Sukabumi',
                'address' => 'Jl. Sindangresmi No. 12',
                'parent_name' => 'Rahmat Hidayat',
                'parent_phone' => '081234567802',
                'average_score' => 90.00,
                'selection_track' => 'Tes Kemampuan Akademik (TKA)',
                'tka_exam_session' => 'Sesi 1 (Lab CBT 1)',
                'tka_exam_date' => Carbon::now()->subDays(2)->toDateString(),
                'tka_score_literacy' => 94.00,
                'tka_score_numeracy' => 91.00,
                'tka_score_science' => 93.00,
                'tka_status' => 'Selesai Ujian',
                'status' => 'Lolos Seleksi',
                'assigned_class' => 'X Merdeka 1',
            ],
            [
                'full_name' => 'Rizky Muhammad Al-Ghifari',
                'nisn' => '0081234569',
                'nik' => '3202123456780003',
                'gender' => 'L',
                'birth_place' => 'Bandung',
                'birth_date' => '2010-02-11',
                'school_origin' => 'SMP IT Al-Madinah',
                'address' => 'Komplek Griya Indah Blok B3',
                'parent_name' => 'dr. Agus Setiawan',
                'parent_phone' => '081234567803',
                'average_score' => 86.20,
                'selection_track' => 'Tes Kemampuan Akademik (TKA)',
                'tka_exam_session' => 'Sesi 2 (Lab CBT 2)',
                'tka_exam_date' => Carbon::now()->subDays(2)->toDateString(),
                'tka_score_literacy' => 88.00,
                'tka_score_numeracy' => 87.50,
                'tka_score_science' => 91.00,
                'tka_status' => 'Selesai Ujian',
                'status' => 'Lolos Seleksi',
                'assigned_class' => 'X Merdeka 2',
            ],
            [
                'full_name' => 'Nabila Putri Zahra',
                'nisn' => '0081234570',
                'nik' => '3202123456780004',
                'gender' => 'P',
                'birth_place' => 'Sukabumi',
                'birth_date' => '2010-11-03',
                'school_origin' => 'SMP Negeri 2 Lengkong',
                'address' => 'Jl. Raya Simpenan Km. 4',
                'parent_name' => 'H. Endang',
                'parent_phone' => '081234567804',
                'average_score' => 84.00,
                'selection_track' => 'Tes Kemampuan Akademik (TKA)',
                'tka_exam_session' => 'Sesi 2 (Lab CBT 2)',
                'tka_exam_date' => Carbon::now()->subDays(2)->toDateString(),
                'tka_score_literacy' => 82.00,
                'tka_score_numeracy' => 80.00,
                'tka_score_science' => 85.00,
                'tka_status' => 'Selesai Ujian',
                'status' => 'Cadangan',
                'assigned_class' => null,
            ],
            [
                'full_name' => 'Bintang Arya Nugraha',
                'nisn' => '0081234571',
                'nik' => '3202123456780005',
                'gender' => 'L',
                'birth_place' => 'Sukabumi',
                'birth_date' => '2010-07-28',
                'school_origin' => 'SMP Pasundan Lengkong',
                'address' => 'Kp. Pasir Salam RT 03/02',
                'parent_name' => 'Ujang Nugraha',
                'parent_phone' => '081234567805',
                'average_score' => 85.00,
                'selection_track' => 'Tes Kemampuan Akademik (TKA)',
                'tka_exam_session' => 'Sesi 3 (Lab CBT 1)',
                'tka_exam_date' => Carbon::now()->addDays(3)->toDateString(),
                'tka_score_literacy' => null,
                'tka_score_numeracy' => null,
                'tka_score_science' => null,
                'tka_status' => 'Belum Ujian',
                'status' => 'Terverifikasi',
                'assigned_class' => null,
            ],
        ];

        foreach ($tkaStudents as $idx => $data) {
            $existing = PpdbRegistration::where('nisn', $data['nisn'])->first();
            if (! $existing) {
                $regNumber = 'SPMB-'.$year.'-TKA-'.str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT);
                $examNumber = 'TKA-'.$year.'-'.str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT);

                $record = PpdbRegistration::create(array_merge($data, [
                    'registration_number' => $regNumber,
                    'tka_exam_number' => $examNumber,
                    'qr_code_token' => Str::uuid()->toString(),
                    'verified_at' => now(),
                ]));

                if ($record->tka_score_literacy !== null) {
                    $record->computeTkaScores(0.60, 0.40);
                    $record->save();
                }
            }
        }

        // Jalur Lain (Zonasi, Afirmasi, Prestasi, Perpindahan)
        $otherStudents = [
            [
                'full_name' => 'Dimas Aditya Maulana',
                'nisn' => '0082234572',
                'gender' => 'L',
                'school_origin' => 'SMPN 1 Lengkong',
                'selection_track' => 'Zonasi',
                'average_score' => 82.50,
                'distance_km' => 0.85,
                'status' => 'Lolos Seleksi',
            ],
            [
                'full_name' => 'Putri Melani Safitri',
                'nisn' => '0082234573',
                'gender' => 'P',
                'school_origin' => 'SMPN 2 Lengkong',
                'selection_track' => 'Afirmasi KETM',
                'average_score' => 80.00,
                'kip_pip_number' => 'KIP-3202-00918',
                'status' => 'Lolos Seleksi',
            ],
            [
                'full_name' => 'Galih Raka Ramadhan',
                'nisn' => '0082234574',
                'gender' => 'L',
                'school_origin' => 'SMP IT Bina Insani',
                'selection_track' => 'Prestasi Nilai Rapor',
                'average_score' => 93.80,
                'achievement_name' => 'Juara 1 O2SN Pencak Silat Tingkat Kabupaten',
                'achievement_level' => 'Kabupaten/Kota',
                'status' => 'Lolos Seleksi',
            ],
        ];

        foreach ($otherStudents as $idx => $item) {
            $existing = PpdbRegistration::where('nisn', $item['nisn'])->first();
            if (! $existing) {
                PpdbRegistration::create(array_merge($item, [
                    'registration_number' => 'SPMB-'.$year.'-REG-'.str_pad((string) ($idx + 10), 4, '0', STR_PAD_LEFT),
                    'parent_name' => 'Orang Tua Siswa',
                    'parent_phone' => '0812998877'.str_pad((string) $idx, 2, '0', STR_PAD_LEFT),
                    'qr_code_token' => Str::uuid()->toString(),
                    'verified_at' => now(),
                ]));
            }
        }

        // Perangkingan TKA
        $participants = PpdbRegistration::tkaTrack()
            ->orderByRaw('COALESCE(tka_weighted_score, 0) DESC')
            ->orderBy('average_score', 'desc')
            ->get();

        $rank = 1;
        foreach ($participants as $p) {
            $p->tka_rank = $rank++;
            $p->saveQuietly();
        }
    }
}

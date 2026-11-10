<?php

namespace Database\Seeders;

use App\Models\Extracurricular;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ExtracurricularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = Teacher::with('user')->get();
        $students = Student::where('is_active', true)->take(60)->get();

        $items = [
            [
                'code' => 'EKS-PRA',
                'name' => 'Gerakan Pramuka',
                'category' => 'Kepemimpinan & Bela Negara',
                'description' => 'Pendidikan kepanduan, kepemimpinan, kemandirian, dan keterampilan survival outdoor.',
                'schedule_day' => 'Jumat',
                'start_time' => '15:00:00',
                'end_time' => '17:00:00',
                'location' => 'Lapangan Utama Sekolah',
            ],
            [
                'code' => 'EKS-PMR',
                'name' => 'PMR (Palang Merah Remaja)',
                'category' => 'Kepemimpinan & Kemanusiaan',
                'description' => 'Pertolongan pertama, kesehatan remaja, donor darah, dan bakti kemanusiaan.',
                'schedule_day' => 'Jumat',
                'start_time' => '15:30:00',
                'end_time' => '17:00:00',
                'location' => 'Ruang UKS & Aula',
            ],
            [
                'code' => 'EKS-PAS',
                'name' => 'Paskibra (Pasukan Pengibar Bendera)',
                'category' => 'Bela Negara & Kedisiplinan',
                'description' => 'Latihan baris-berbaris (PBB), formasi pengibaran bendera, dan pembentukan disiplin karakter.',
                'schedule_day' => 'Sabtu',
                'start_time' => '08:00:00',
                'end_time' => '11:00:00',
                'location' => 'Lapangan Upacara',
            ],
            [
                'code' => 'EKS-FUT',
                'name' => 'Futsal',
                'category' => 'Olahraga',
                'description' => 'Pengembangan bakat sepak bola futsal, taktik permainan, dan kebugaran fisik.',
                'schedule_day' => 'Kamis',
                'start_time' => '15:30:00',
                'end_time' => '17:30:00',
                'location' => 'Lapangan Futsal SMAN 1 Lengkong',
            ],
            [
                'code' => 'EKS-BSK',
                'name' => 'Bola Basket',
                'category' => 'Olahraga',
                'description' => 'Teknik dasar dribble, shooting, strategi tanding tim, dan persiapan kompetisi DBL.',
                'schedule_day' => 'Rabu',
                'start_time' => '15:30:00',
                'end_time' => '17:30:00',
                'location' => 'Lapangan Basket Outdoor',
            ],
            [
                'code' => 'EKS-ROH',
                'name' => 'Rohis (Kerohanian Islam)',
                'category' => 'Keagamaan',
                'description' => 'Kajian keislaman, pembiasaan ibadah, hadroh sholawat, dan pengelolaan kegiatan hari besar Islam.',
                'schedule_day' => 'Jumat',
                'start_time' => '13:00:00',
                'end_time' => '14:30:00',
                'location' => 'Masjid / Musholla Sekolah',
            ],
            [
                'code' => 'EKS-TAR',
                'name' => 'Seni Tari Tradisional',
                'category' => 'Seni & Budaya',
                'description' => 'Pelestarian tari Jaipong, tari kreasi nusantara, dan pagelaran seni budaya Sunda.',
                'schedule_day' => 'Selasa',
                'start_time' => '15:30:00',
                'end_time' => '17:00:00',
                'location' => 'Sanggar Seni / Aula',
            ],
            [
                'code' => 'EKS-KIR',
                'name' => 'KIR (Karya Ilmiah Remaja)',
                'category' => 'Sains & Riset',
                'description' => 'Penelitian ilmiah, inovasi teknologi terapan, olimpiade riset sains, dan penulisan karya tulis.',
                'schedule_day' => 'Rabu',
                'start_time' => '15:00:00',
                'end_time' => '16:30:00',
                'location' => 'Laboratorium IPA / Komputer',
            ],
            [
                'code' => 'EKS-ENG',
                'name' => 'English Club',
                'category' => 'Bahasa & Komunikasi',
                'description' => 'Public speaking, debate, storytelling, dan peningkatan kecakapan percakapan bahasa Inggris.',
                'schedule_day' => 'Senin',
                'start_time' => '15:30:00',
                'end_time' => '17:00:00',
                'location' => 'Laboratorium Bahasa',
            ],
            [
                'code' => 'EKS-MUS',
                'name' => 'Paduan Suara & Musik',
                'category' => 'Seni & Budaya',
                'description' => 'Olah vokal paduan suara, instrumen musik akustik / band, dan persiapan upacara bendera.',
                'schedule_day' => 'Kamis',
                'start_time' => '15:30:00',
                'end_time' => '17:00:00',
                'location' => 'Ruang Musik / Kesenian',
            ],
        ];

        foreach ($items as $idx => $data) {
            // Assign a teacher as pembina if available
            $pembina = $teachers->get($idx % max(1, $teachers->count()));
            $data['teacher_id'] = $pembina?->id;

            $extra = Extracurricular::updateOrCreate(
                ['code' => $data['code']],
                $data
            );

            // Give pembina teacher the role 'pembina_ekstra'
            if ($pembina) {
                $extra->teachers()->syncWithoutDetaching([$pembina->id => ['role' => 'Pembina Utama']]);

                if ($pembina->user) {
                    $u = $pembina->user;
                    $roles = is_array($u->roles) ? $u->roles : [];
                    if (! in_array('pembina_ekstra', $roles, true)) {
                        $roles[] = 'pembina_ekstra';
                        $u->roles = array_values(array_unique($roles));
                        $u->save();
                    }
                }
            }

            // Enroll 10-15 sample students as members
            if ($students->isNotEmpty()) {
                $studentSlice = $students->slice(($idx * 5) % $students->count(), 12);
                $syncData = [];
                foreach ($studentSlice as $st) {
                    $syncData[$st->id] = [
                        'role' => 'Anggota',
                        'joined_at' => now()->subMonths(2)->toDateString(),
                    ];
                }
                $extra->students()->syncWithoutDetaching($syncData);
            }
        }
    }
}

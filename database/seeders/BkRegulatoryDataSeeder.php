<?php

namespace Database\Seeders;

use App\Models\BkAkpd;
use App\Models\BkAkpdQuestion;
use App\Models\BkRpl;
use Illuminate\Database\Seeder;

class BkRegulatoryDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed AKPD Standar POP-BK SMA
        $akpd = BkAkpd::firstOrCreate(
            ['grade_level' => 'X'],
            [
                'title' => 'Instrumen AKPD Standar POP-BK SMA (Fase E)',
                'description' => 'Angket Kebutuhan Peserta Didik untuk Asesmen Diagnostik Non-Tes BK mencakup 4 Bidang Layanan (Pribadi, Sosial, Belajar, Karier) sesuai Panduan Operasional Penyelenggaraan BK Ditjen GTK Kemdikbudristek.',
                'total_items' => 40,
                'is_active' => true,
            ]
        );

        if ($akpd->questions()->count() === 0) {
            $questions = [
                // PRIBADI (1-10)
                ['P01', 'PRIBADI', 'Saya merasa belum mampu mengendalikan emosi atau rasa marah saat menghadapi masalah.', 'Kematangan Emosi', 1],
                ['P02', 'PRIBADI', 'Saya merasa kurang percaya diri saat harus tampil berbicara di depan banyak orang.', 'Pengembangan Diri', 2],
                ['P03', 'PRIBADI', 'Saya merasa sulit disiplin dan sering menunda-nunda kewajiban ibadah serta tugas pribadi.', 'Kedisiplinan & Spiritual', 3],
                ['P04', 'PRIBADI', 'Saya belum memahami potensi, bakat, serta kelebihan diri saya sendiri secara jelas.', 'Pemahaman Diri', 4],
                ['P05', 'PRIBADI', 'Saya sering merasa cemas, takut gagal, atau overthinking memikirkan masa depan.', 'Kesehatan Mental', 5],
                ['P06', 'PRIBADI', 'Saya kesulitan mengelola pengeluaran uang saku secara hemat dan terencana.', 'Kemandirian Finansial', 6],
                ['P07', 'PRIBADI', 'Saya merasa kecanduan bermain gawai (gadget/game/medsos) hingga larut malam.', 'Pengelolaan Diri', 7],
                ['P08', 'PRIBADI', 'Saya sering merasa rendah diri saat membandingkan diri saya dengan keberhasilan orang lain.', 'Penerimaan Diri', 8],
                ['P09', 'PRIBADI', 'Saya belum tahu cara menyalurkan hobi positif menjadi karya yang bermanfaat.', 'Pengembangan Bakat', 9],
                ['P10', 'PRIBADI', 'Saya memerlukan bimbingan tentang cara menjaga kesehatan reproduksi dan pergaulan remaja.', 'Pendidikan Seksual & Karakter', 10],

                // SOSIAL (11-20)
                ['S01', 'SOSIAL', 'Saya merasa sulit memulai percakapan atau menjalin pertemanan baru di lingkungan sekolah.', 'Keterampilan Bergaul', 11],
                ['S02', 'SOSIAL', 'Saya pernah atau sering menjadi korban ejekan/cemoohan (bullying) dari teman sebaya.', 'Keamanan Sosial', 12],
                ['S03', 'SOSIAL', 'Saya merasa kesulitan menolak ajakan negatif dari teman pergaulan sebaya (peer pressure).', 'Asertivitas', 13],
                ['S04', 'SOSIAL', 'Saya sering berselisih paham atau bertengkar dengan teman sekelas tanpa penyelesaian baik.', 'Manajemen Konflik', 14],
                ['S05', 'SOSIAL', 'Saya merasa canggung atau kurang nyaman bergaul dengan teman yang berbeda latar belakang budaya/agama.', 'Toleransi Sosial', 15],
                ['S06', 'SOSIAL', 'Saya merasa kurang diperhatikan atau memiliki hambatan komunikasi terbuka dengan orang tua di rumah.', 'Harmonisasi Keluarga', 16],
                ['S07', 'SOSIAL', 'Saya ingin meningkatkan kemampuan berbicara dan kepemimpinan dalam organisasi sekolah.', 'Kepemimpinan', 17],
                ['S08', 'SOSIAL', 'Saya merasa terisolasi atau diabaikan oleh kelompok teman dalam kegiatan kelompok kelas.', 'Penyesuaian Sosial', 18],
                ['S09', 'SOSIAL', 'Saya belum tahu etika yang benar dan aman dalam berkomunikasi di media sosial.', 'Etika Digital', 19],
                ['S10', 'SOSIAL', 'Saya ingin belajar cara bersikap empati dan menolong teman yang sedang tertimpa musibah.', 'Kepedulian Sosial', 20],

                // BELAJAR (21-30)
                ['B01', 'BELAJAR', 'Saya kesulitan mengatur jadwal waktu antara belajar mandiri, tugas sekolah, dan kegiatan ekstrakurikuler.', 'Manajemen Waktu', 21],
                ['B02', 'BELAJAR', 'Saya sulit mempertahankan konsentrasi belajar di kelas terutama saat jam pelajaran siang.', 'Fokus Belajar', 22],
                ['B03', 'BELAJAR', 'Saya merasa sangat malas belajar dan sering menunda penyelesaian pekerjaan rumah/proyek sekolah.', 'Motivasi Belajar', 23],
                ['B04', 'BELAJAR', 'Saya belum mengetahui gaya belajar yang paling cocok bagi saya (Visual, Auditori, atau Kinestetik).', 'Strategi Belajar', 24],
                ['B05', 'BELAJAR', 'Saya merasa panik, cemas berlebihan, atau tegang luar biasa setiap kali menghadapi ujian asesmen sumatif.', 'Kesiapan Evaluasi', 25],
                ['B06', 'BELAJAR', 'Saya kesulitan memahami pelajaran numerasi matematika atau sains yang banyak rumus.', 'Kesulitan Belajar Akademik', 26],
                ['B07', 'BELAJAR', 'Suasana dan fasilitas belajar di rumah saya kurang tenang atau kurang mendukung fokus belajar.', 'Lingkungan Belajar', 27],
                ['B08', 'BELAJAR', 'Saya sungkan atau malu bertanya kepada bapak/ibu guru ketika materi pelajaran belum saya mengerti.', 'Partisipasi Kelas', 28],
                ['B09', 'BELAJAR', 'Saya belum terbiasa membuat rangkuman/mind map catatan materi yang efektif dan sistematis.', 'Teknik Merangkum', 29],
                ['B10', 'BELAJAR', 'Saya ingin meningkatkan keterampilan berpikir kritis dan literasi membaca bacaan ilmiah.', 'Literasi Akademik', 30],

                // KARIER (31-40)
                ['K01', 'KARIER', 'Saya masih bingung menentukan cita-cita profesi atau karir masa depan saya yang pasti.', 'Orientasi Cita-cita', 31],
                ['K02', 'KARIER', 'Saya belum mengetahui jalur masuk perguruan tinggi negeri (SNBP, SNBT, Mandiri, dan Kedinasan).', 'Wawasan PTN/PTS', 32],
                ['K03', 'KARIER', 'Saya ingin tahu prospek peluang kerja dan tren profesi masa depan di era digital dan AI.', 'Eksplorasi Dunia Kerja', 33],
                ['K04', 'KARIER', 'Pilihan jurusan kuliah yang saya inginkan berbeda dengan keinginan orang tua saya.', 'Kompromi Minat Karir', 34],
                ['K05', 'KARIER', 'Saya memerlukan informasi tentang program beasiswa kuliah (KIP-Kuliah, Beasiswa Jabar, dsb).', 'Akses Pembiayaan Kuliah', 35],
                ['K06', 'KARIER', 'Saya ingin mengetahui mata pelajaran pendukung jurusan kuliah pilihan saya pada Kurikulum Merdeka.', 'Pilihan Mata Pelajaran', 36],
                ['K07', 'KARIER', 'Saya tertarik bekerja langsung atau berwirausaha setelah lulus SMA namun belum tahu modal keahliannya.', 'Kesiapan Wirausaha', 37],
                ['K08', 'KARIER', 'Saya belum pernah mengikuti tes psikotes minat bakat karir yang valid dan terpercaya.', 'Asesmen Psikotes', 38],
                ['K09', 'KARIER', 'Saya ingin memahami syarat seleksi fisik, psikis, dan akademik masuk Sekolah Kedinasan / TNI-Polri.', 'Karier Kedinasan', 39],
                ['K10', 'KARIER', 'Saya memerlukan bimbingan intensif cara membuat rencana karir individual (Individual Career Plan).', 'Perencanaan Karir', 40],
            ];

            foreach ($questions as $q) {
                BkAkpdQuestion::create([
                    'akpd_id' => $akpd->id,
                    'code' => $q[0],
                    'field_of_service' => $q[1],
                    'statement' => $q[2],
                    'profile_dimension' => $q[3],
                    'sort_order' => $q[4],
                ]);
            }
        }

        // 2. Seed Contoh RPL BK 1 Lembar Kurikulum Merdeka
        if (BkRpl::count() === 0) {
            BkRpl::create([
                'rpl_code' => 'RPL-BK-2026-001',
                'topic' => 'Kiat Sukses Mengatur Waktu Belajar Mandiri di SMA',
                'service_component' => 'LAYANAN_DASAR',
                'service_type' => 'KLASIKAL',
                'field_of_service' => 'BELAJAR',
                'grade_target' => 'Fase E / Kelas X',
                'semester' => 'Ganjil',
                'time_allocation' => '1 Pertemuan x 45 Menit',
                'profile_pelajar_pancasila' => 'Mandiri dan Bernalar Kritis',
                'learning_objectives' => "1. Peserta didik dapat mengidentifikasi pemborosan waktu dalam rutinitas harian.\n2. Peserta didik dapat merancang jadwal belajar mandiri mingguan dengan teknik Matriks Eisenhower.\n3. Peserta didik berkomitmen menerapkan disiplin manajemen waktu belajar.",
                'materials_summary' => 'Konsep dasar manajemen waktu, kuadran prioritas penting-mendesak, kiat mengatasi distraksi smartphone.',
                'method_and_media' => 'Curah Pendapat (Brainstorming), Diskusi Kelompok, Media LKPD Jadwal Harian & Video Animasi.',
                'step_introduction' => 'Guru BK membuka dengan salam, apersepsi, ice breaking singkat, serta menyampaikan tujuan layanan.',
                'step_core' => 'Siswa menyimak studi kasus prokrastinasi, berdiskusi dalam kelompok 4 orang menyusun prioritas, dan mempresentasikan simulasi jadwal belajar.',
                'step_closing' => 'Guru BK bersama siswa menyimpulkan materi, melakukan refleksi manfaat layanan, dan memberikan apresiasi.',
                'evaluation_process' => 'Observasi keaktifan dan antusiasme siswa selama kegiatan klasikal berlangsung.',
                'evaluation_result' => 'Siswa mengisi angket penilaian hasil (Understanding, Comfortable, Action) di akhir pertemuan.',
                'status' => 'DISETUJUI',
            ]);

            BkRpl::create([
                'rpl_code' => 'RPL-BK-2026-002',
                'topic' => 'Mengenal Jalur Sukses Masuk Perguruan Tinggi Negeri (PTN)',
                'service_component' => 'PERENCANAAN_INDIVIDUAL',
                'service_type' => 'KLASIKAL',
                'field_of_service' => 'KARIER',
                'grade_target' => 'Fase F / Kelas XI-XII',
                'semester' => 'Ganjil',
                'time_allocation' => '1 Pertemuan x 45 Menit',
                'profile_pelajar_pancasila' => 'Bernalar Kritis dan Kreatif',
                'learning_objectives' => "1. Peserta didik memahami perbedaan mekanisme seleksi SNBP, SNBT, dan Seleksi Mandiri PTN.\n2. Peserta didik mampu memetakan nilai rapor dan sertifikat prestasi untuk persiapan portofolio.\n3. Peserta didik menyusun target awal pilihan perguruan tinggi dan program studi.",
                'materials_summary' => 'Sosialisasi SNPMB Kemdikbudristek, penghitungan kuota sekolah akreditasi A, strategi pemilihan prodi sesuai minat.',
                'method_and_media' => 'Presentasi Interaktif, Tanya Jawab, Simulasi Skoring SNBP menggunakan slide proyektor.',
                'step_introduction' => 'Salam pembuka, mengabsen siswa, menyampaikan urgensi perencanaan studi lanjut sedini mungkin.',
                'step_core' => 'Pemaparan infografis SNPMB, bedah kriteria penilaian, workshop pengisian lembar kerja rencana studi lanjut (Study Plan Sheet).',
                'step_closing' => 'Refleksi komitmen belajar, tanya jawab singkat, dan tindak lanjut konsultasi individual di ruang BK.',
                'evaluation_process' => 'Antusiasme dan kualitas pertanyaan siswa seputar jurusan dan passing grade.',
                'evaluation_result' => 'Terkumpulnya lembar pemetaan rencana perguruan tinggi seluruh siswa kelas.',
                'status' => 'DISETUJUI',
            ]);
        }
    }
}

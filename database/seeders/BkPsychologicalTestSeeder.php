<?php

namespace Database\Seeders;

use App\Models\BkPsychologicalTest;
use App\Models\BkPsychologicalTestQuestion;
use Illuminate\Database\Seeder;

class BkPsychologicalTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. INSTRUMEN TES GAYA BELAJAR VAK (Visual, Auditori, Kinestetik)
        $vakTest = BkPsychologicalTest::updateOrCreate(
            ['slug' => 'vak'],
            [
                'name' => 'Tes Gaya Belajar VAK (Visual, Auditori, Kinestetik)',
                'category' => 'GAYA_BELAJAR',
                'short_desc' => 'Asesmen preferensi modalitas sensorik siswa dalam menyerap, mengolah, dan mengingat informasi materi pelajaran.',
                'instructions' => 'Pilihlah salah satu jawaban yang paling menggambarkan kebiasaan belajarmu sehari-hari (1 = Sangat Tidak Sesuai, 2 = Kurang Sesuai, 3 = Sesuai, 4 = Sangat Sesuai). Tidak ada jawaban benar atau salah.',
                'duration_minutes' => 20,
                'total_questions' => 24,
                'is_active' => true,
            ]
        );

        $vakQuestions = [
            // VISUAL (8 butir)
            [1, 'Saya lebih mudah mengingat pelajaran jika guru menampilkan bagan, diagram, infografis, atau video pembelajaran.', 'VISUAL', 'Gaya Belajar Visual'],
            [2, 'Catatan buku saya rapi, sering menggunakan pulpen aneka warna atau highlighter (stabilo) untuk kata kunci.', 'VISUAL', 'Gaya Belajar Visual'],
            [3, 'Saya lebih mudah mengingat wajah orang baru dibandingkan mengingat namanya.', 'VISUAL', 'Gaya Belajar Visual'],
            [4, 'Ketika menghafal sesuatu, saya membayangkan tulisan atau posisi letak tulisan tersebut di dalam buku.', 'VISUAL', 'Gaya Belajar Visual'],
            [5, 'Saya mudah terganggu atau sulit konsentrasi belajar jika ruangan kelas/meja belajar saya berantakan.', 'VISUAL', 'Gaya Belajar Visual'],
            [6, 'Saya lebih suka membaca sendiri buku teks daripada mendengarkan orang lain membacakannya untuk saya.', 'VISUAL', 'Gaya Belajar Visual'],
            [7, 'Saya cepat memahami petunjuk peta arah (denah grafis) daripada arahan lisan belok kanan-kiri.', 'VISUAL', 'Gaya Belajar Visual'],
            [8, 'Saya menyukai penyajian materi yang memanfaatkan gambar ilustrasi nyata, mind map, dan grafik data.', 'VISUAL', 'Gaya Belajar Visual'],

            // AUDITORI (8 butir)
            [9, 'Saya lebih mudah mengerti penjelasan guru dengan mendengarkan secara saksama di kelas dibandingkan hanya membaca buku.', 'AUDITORI', 'Gaya Belajar Auditori'],
            [10, 'Saya suka membaca materi pelajaran dengan bersuara atau mengulang ucapan secara berbisik.', 'AUDITORI', 'Gaya Belajar Auditori'],
            [11, 'Saya sangat menikmati diskusi kelompok, bertukar pendapat lisan, dan sesi tanya jawab di kelas.', 'AUDITORI', 'Gaya Belajar Auditori'],
            [12, 'Saya mudah terganggu oleh suara gaduh, bisikan, atau suara musik saat sedang fokus belajar.', 'AUDITORI', 'Gaya Belajar Auditori'],
            [13, 'Saya lebih mudah mengingat nama orang, lirik lagu, atau lelucon lisan yang pernah saya dengar.', 'AUDITORI', 'Gaya Belajar Auditori'],
            [14, 'Jika mendapat masalah atau materi sulit, saya lebih suka menceritakan dan membicarakannya dengan teman atau guru.', 'AUDITORI', 'Gaya Belajar Auditori'],
            [15, 'Saya mampu menyerap intisari pembelajaran melalui rekaman suara podcast ceramah atau penjelasan audio.', 'AUDITORI', 'Gaya Belajar Auditori'],
            [16, 'Saya peka terhadap intonasi, nada bicara, dan pilihan kata yang diucapkan orang lain.', 'AUDITORI', 'Gaya Belajar Auditori'],

            // KINESTETIK (8 butir)
            [17, 'Saya paling cepat memahami pelajaran saat melakukan praktik langsung di laboratorium atau eksperimen lapangan.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
            [18, 'Saya merasa gelisah, mudah bosan, atau mengantuk jika harus duduk diam mendengarkan ceramah lebih dari 30 menit.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
            [19, 'Saat membaca buku atau menghafal, saya sering menggunakan jari untuk menunjuk baris tulisan atau sambil berjalan-jalan.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
            [20, 'Saya banyak menggerakkan anggota tubuh (tangan, kepala, mimik wajah) ketika berbicara atau menjelaskan sesuatu.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
            [21, 'Saya suka menyentuh, merakit, membongkar barang, atau membuat alat peraga nyata dari materi yang dipelajari.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
            [22, 'Saya lebih menyukai kegiatan olahraga, bermain drama, menari, atau simulasi peran daripada belajar pasif di bangku.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
            [23, 'Saya sering mengetuk-ngetukkan pena, menggerakkan kaki, atau memegang benda di meja saat berpikir keras.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
            [24, 'Saya belajar paling efektif jika langsung mengerjakan soal-soal latihan dan studi kasus nyata secara bertahap.', 'KINESTETIK', 'Gaya Belajar Kinestetik'],
        ];

        foreach ($vakQuestions as $q) {
            BkPsychologicalTestQuestion::updateOrCreate(
                ['test_id' => $vakTest->id, 'question_number' => $q[0]],
                [
                    'question_text' => $q[1],
                    'dimension' => $q[2],
                    'dimension_name' => $q[3],
                    'scale_type' => 'LIKERT_4',
                    'options' => [
                        ['label' => 'Sangat Tidak Sesuai', 'score' => 1],
                        ['label' => 'Kurang Sesuai', 'score' => 2],
                        ['label' => 'Sesuai', 'score' => 3],
                        ['label' => 'Sangat Sesuai', 'score' => 4],
                    ],
                ]
            );
        }

        // 2. INSTRUMEN TES MINAT BAKAT KARIER HOLLAND RIASEC
        $riasecTest = BkPsychologicalTest::updateOrCreate(
            ['slug' => 'riasec'],
            [
                'name' => 'Tes Minat Bakat Karier Holland RIASEC',
                'category' => 'MINAT_BAKAT',
                'short_desc' => 'Asesmen minat vokasional dan kepribadian karier berdasarkan teori John Holland untuk rekomendasi jurusan kuliah dan profesi masa depan.',
                'instructions' => 'Nyatakan seberapa besar ketertarikan atau kesesuaian dirimu terhadap aktivitas-aktivitas berikut (1 = Sangat Tidak Tertarik, 2 = Kurang Tertarik, 3 = Tertarik, 4 = Sangat Tertarik).',
                'duration_minutes' => 25,
                'total_questions' => 36,
                'is_active' => true,
            ]
        );

        $riasecQuestions = [
            // REALISTIC (R) - 6 butir
            [1, 'Memperbaiki peralatan elektronik, sepeda motor, komputer, atau merakit perangkat mekanis fisik.', 'R', 'Realistic (Praktikal & Mekanikal)'],
            [2, 'Bekerja di luar ruangan (alam terbuka), berkebun, pertukangan, atau proyek survei konstruksi fisik.', 'R', 'Realistic (Praktikal & Mekanikal)'],
            [3, 'Mengoperasikan mesin-mesin industri, drone pemetaan, perkakas teknik, atau perangkat laboratorium presisi.', 'R', 'Realistic (Praktikal & Mekanikal)'],
            [4, 'Mempelajari cara kerja sistem kelistrikan bangunan, arsitektur pondasi, atau instalasi perpipaan.', 'R', 'Realistic (Praktikal & Mekanikal)'],
            [5, 'Melakukan tugas-tugas lapangan yang menuntut ketahanan fisik, ketangkasan manual, dan keahlian motorik.', 'R', 'Realistic (Praktikal & Mekanikal)'],
            [6, 'Mengikuti lomba robotik teknik, rancang bangun purwarupa (prototype), atau modifikasi mesin.', 'R', 'Realistic (Praktikal & Mekanikal)'],

            // INVESTIGATIVE (I) - 6 butir
            [7, 'Menganalisis fenomena sains, reaksi kimia, struktur biologi sel, atau hukum-hukum fisika alam semesta.', 'I', 'Investigative (Analitis & Ilmiah)'],
            [8, 'Meneliti data ilmiah, menyusun hipotesis, dan memecahkan soal-soal matematika atau teka-teki logika rumit.', 'I', 'Investigative (Analitis & Ilmiah)'],
            [9, 'Membaca jurnal ilmiah, artikel teknologi terkini, atau ensiklopedia tentang luar angkasa dan kedokteran.', 'I', 'Investigative (Analitis & Ilmiah)'],
            [10, 'Menyelidiki akar masalah dari suatu fenomena menggunakan metode riset sistematis dan pembuktian empiris.', 'I', 'Investigative (Analitis & Ilmiah)'],
            [11, 'Melakukan coding pemrograman komputer, membangun algoritma data, atau riset keamanan siber.', 'I', 'Investigative (Analitis & Ilmiah)'],
            [12, 'Merancang eksperimen riset laboratorium untuk menemukan obat baru atau solusi energi terbarukan.', 'I', 'Investigative (Analitis & Ilmiah)'],

            // ARTISTIC (A) - 6 butir
            [13, 'Menciptakan karya seni grafis, ilustrasi, lukisan, poster visual, atau animasi digital.', 'A', 'Artistic (Kreatif & Ekspresif)'],
            [14, 'Menulis puisi, cerpen, skenario drama film, atau artikel opini yang menggugah emosi pembaca.', 'A', 'Artistic (Kreatif & Ekspresif)'],
            [15, 'Memainkan alat musik, menyanyi, menggubah aransemen lagu, atau memproduksi rekaman audio.', 'A', 'Artistic (Kreatif & Ekspresif)'],
            [16, 'Merancang tata panggung, desain busana (fashion), fotografi estetis, atau sinematografi videografi.', 'A', 'Artistic (Kreatif & Ekspresif)'],
            [17, 'Mengekspresikan gagasan secara bebas dan orisinal tanpa terikat aturan baku yang kaku.', 'A', 'Artistic (Kreatif & Ekspresif)'],
            [18, 'Mengapresiasi pameran seni, teater panggung, orkestra musik, dan karya arsitektur bernilai seni tinggi.', 'A', 'Artistic (Kreatif & Ekspresif)'],

            // SOCIAL (S) - 6 butir
            [19, 'Membimbing, mengajari, atau melatih teman yang mengalami kesulitan memahami suatu mata pelajaran.', 'S', 'Social (Pemberdayaan & Menolong)'],
            [20, 'Mendengarkan curahan hati teman dengan empati serta memberikan masukan dan dukungan moral positif.', 'S', 'Social (Pemberdayaan & Menolong)'],
            [21, 'Terlibat aktif dalam kegiatan bakti sosial kemanusiaan, palang merah remaja (PMR), atau kerelawanan peduli sesama.', 'S', 'Social (Pemberdayaan & Menolong)'],
            [22, 'Mempelajari psikologi perkembangan manusia, ilmu sosiologi hubungan masyarakat, dan konseling pribadi.', 'S', 'Social (Pemberdayaan & Menolong)'],
            [23, 'Menengahi perselisihan atau pertengkaran antar teman agar tercipta perdamaian dan kerukunan bersama.', 'S', 'Social (Pemberdayaan & Menolong)'],
            [24, 'Bekerja sama dalam tim kerja sosial yang berorientasi meningkatkan kesejahteraan hidup orang banyak.', 'S', 'Social (Pemberdayaan & Menolong)'],

            // ENTERPRISING (E) - 6 butir
            [25, 'Memimpin organisasi siswa, membagi tugas kelompok kerja, dan memotivasi anggota mencapai target.', 'E', 'Enterprising (Kepemimpinan & Bisnis)'],
            [26, 'Mempromosikan produk, mengelola bisnis jual-beli wirausaha mandiri, dan bernegosiasi harga.', 'E', 'Enterprising (Kepemimpinan & Bisnis)'],
            [27, 'Berbicara di depan umum (public speaking), berpidato persuasif, memandu acara (MC), atau debat kompetitif.', 'E', 'Enterprising (Kepemimpinan & Bisnis)'],
            [28, 'Mengambil keputusan penting dalam kondisi mendesak dan berani menanggung risiko strategi terukur.', 'E', 'Enterprising (Kepemimpinan & Bisnis)'],
            [29, 'Menyusun proposal sponsorship, mencari relasi mitra kerjasama eksternal, dan menggalang dana kegiatan.', 'E', 'Enterprising (Kepemimpinan & Bisnis)'],
            [30, 'Mempelajari strategi pemasaran digital, manajemen eksekutif perusahaan, dan investasi pasar modal.', 'E', 'Enterprising (Kepemimpinan & Bisnis)'],

            // CONVENTIONAL (C) - 6 butir
            [31, 'Menata arsip dokumen, menginput data pembukuan ke tabel excel/spreadsheet dengan rapi dan teliti.', 'C', 'Conventional (Terstruktur & Prosedural)'],
            [32, 'Menghitung anggaran belanja kas keuangan secara akurat tanpa ada selisih nominal satu rupiah pun.', 'C', 'Conventional (Terstruktur & Prosedural)'],
            [33, 'Mematuhi standar operasional prosedur (SOP) yang jelas dan bekerja teratur sesuai jadwal baku.', 'C', 'Conventional (Terstruktur & Prosedural)'],
            [34, 'Memeriksa kembali (cross-check) naskah surat dinas, laporan administrasi, dan rincian transaksi agar nihil kekeliruan.', 'C', 'Conventional (Terstruktur & Prosedural)'],
            [35, 'Menyukai pekerjaan yang menuntut keteraturan sistematis, pengarsipan kode katalog, dan rekapitulasi data.', 'C', 'Conventional (Terstruktur & Prosedural)'],
            [36, 'Mengelola sistem administrasi kepegawaian, perpajakan, atau verifikasi faktur kuitansi resmi.', 'C', 'Conventional (Terstruktur & Prosedural)'],
        ];

        foreach ($riasecQuestions as $q) {
            BkPsychologicalTestQuestion::updateOrCreate(
                ['test_id' => $riasecTest->id, 'question_number' => $q[0]],
                [
                    'question_text' => $q[1],
                    'dimension' => $q[2],
                    'dimension_name' => $q[3],
                    'scale_type' => 'LIKERT_4',
                    'options' => [
                        ['label' => 'Sangat Tidak Tertarik', 'score' => 1],
                        ['label' => 'Kurang Tertarik', 'score' => 2],
                        ['label' => 'Tertarik', 'score' => 3],
                        ['label' => 'Sangat Tertarik', 'score' => 4],
                    ],
                ]
            );
        }

        // 3. INSTRUMEN TES KECERDASAN MAJEMUK (Howard Gardner Multiple Intelligences)
        $gardnerTest = BkPsychologicalTest::updateOrCreate(
            ['slug' => 'gardner'],
            [
                'name' => 'Tes Kecerdasan Majemuk (Howard Gardner Multiple Intelligences)',
                'category' => 'KECERDASAN_MAJEMUK',
                'short_desc' => 'Pemetaan 8 profil kecerdasan majemuk siswa untuk mengoptimalkan potensi belajar dan pengembangan diri holistik.',
                'instructions' => 'Pilihlah tingkat kecocokan setiap pernyataan dengan potensi dirimu (1 = Tidak Sesuai, 2 = Kadang-kadang, 3 = Sering, 4 = Sangat Menonjol).',
                'duration_minutes' => 25,
                'total_questions' => 32,
                'is_active' => true,
            ]
        );

        $gardnerQuestions = [
            // 1. Linguistik-Verbal (4 butir)
            [1, 'Saya senang bermain kata-kata, teka-teki silang, membaca buku sastra, dan mahir menyusun kalimat lisan maupun tulisan.', 'LINGUISTIK', 'Kecerdasan Linguistik-Verbal'],
            [2, 'Saya mudah mengingat kutipan kata bijak, menghafal peribahasa, atau belajar kosakata bahasa asing baru.', 'LINGUISTIK', 'Kecerdasan Linguistik-Verbal'],
            [3, 'Teman-teman menilai saya pandai bercerita, menulis esai, atau berargumentasi dengan kosa kata yang kaya.', 'LINGUISTIK', 'Kecerdasan Linguistik-Verbal'],
            [4, 'Pelajaran Bahasa Indonesia, Bahasa Inggris, atau Sejarah adalah salah satu mata pelajaran favorit saya.', 'LINGUISTIK', 'Kecerdasan Linguistik-Verbal'],

            // 2. Logis-Matematis (4 butir)
            [5, 'Saya menikmati memecahkan teka-teki logika, bermain catur, dan mencari pola hubungan sebab-akibat.', 'LOGIS_MATEMATIS', 'Kecerdasan Logis-Matematis'],
            [6, 'Saya senang menghitung angka di luar kepala dan tertarik dengan rumus eksakta (Matematika/Fisika/Kimia).', 'LOGIS_MATEMATIS', 'Kecerdasan Logis-Matematis'],
            [7, 'Saya selalu meminta penjelasan rasional, data ilmiah, dan bukti konkret sebelum mempercayai suatu klaim.', 'LOGIS_MATEMATIS', 'Kecerdasan Logis-Matematis'],
            [8, 'Saya mampu mengorganisasikan langkah pemecahan masalah secara runtut, logis, dan terstruktur.', 'LOGIS_MATEMATIS', 'Kecerdasan Logis-Matematis'],

            // 3. Visual-Spasial (4 butir)
            [9, 'Saya memiliki daya imajinasi visual yang kuat dan mampu memvisualisasikan objek 3 dimensi dalam pikiran.', 'VISUAL_SPASIAL', 'Kecerdasan Visual-Spasial'],
            [10, 'Saya pandai menggambar, membuat sketsa desain, membaca peta topografi, atau menata tata ruang kamar.', 'VISUAL_SPASIAL', 'Kecerdasan Visual-Spasial'],
            [11, 'Saya peka terhadap keindahan kombinasi warna, proporsi bentuk geometris, dan estetika fotografi.', 'VISUAL_SPASIAL', 'Kecerdasan Visual-Spasial'],
            [12, 'Saya lebih mudah mengingat rute perjalanan jalan raya setelah satu kali melewatinya.', 'VISUAL_SPASIAL', 'Kecerdasan Visual-Spasial'],

            // 4. Kinestetik-Jasmani (4 butir)
            [13, 'Saya memiliki koordinasi motorik tubuh yang baik dalam berolahraga, senam, atau menari.', 'KINESTETIK', 'Kecerdasan Kinestetik-Jasmani'],
            [14, 'Saya terampil menggunakan kedua tangan untuk membuat kerajinan, memperbaiki barang, atau memainkan alat musik perkusi.', 'KINESTETIK', 'Kecerdasan Kinestetik-Jasmani'],
            [15, 'Saya belajar paling baik dengan cara bergerak, mencoba langsung secara fisik, dan melakukan simulasi.', 'KINESTETIK', 'Kecerdasan Kinestetik-Jasmani'],
            [16, 'Saya gemar beraktivitas fisik di luar ruangan dan mudah merasa bosan jika pasif duduk seharian.', 'KINESTETIK', 'Kecerdasan Kinestetik-Jasmani'],

            // 5. Musikal-Ritmik (4 butir)
            [17, 'Saya peka terhadap nada fals, ketukan ritme, tempo drum, dan warna suara instrumen musik.', 'MUSIKAL', 'Kecerdasan Musikal-Ritmik'],
            [18, 'Saya sering bersenandung, mengetukkan jari mengikuti irama lagu, atau mendengarkan musik saat beraktivitas.', 'MUSIKAL', 'Kecerdasan Musikal-Ritmik'],
            [19, 'Saya cepat menghafal melodi musik baru atau mampu menyelaraskan harmoni vokal paduan suara.', 'MUSIKAL', 'Kecerdasan Musikal-Ritmik'],
            [20, 'Musik sangat memengaruhi suasana hati (mood) saya dan membantu membangkitkan inspirasi belajar.', 'MUSIKAL', 'Kecerdasan Musikal-Ritmik'],

            // 6. Interpersonal (Sosial) (4 butir)
            [21, 'Saya mudah bergaul, luwes membuka percakapan dengan orang baru, dan memiliki banyak teman dari berbagai kalangan.', 'INTERPERSONAL', 'Kecerdasan Interpersonal'],
            [22, 'Saya peka terhadap perasaan, suasana hati, dan kebutuhan orang lain meskipun mereka tidak mengungkapkannya.', 'INTERPERSONAL', 'Kecerdasan Interpersonal'],
            [23, 'Saya sering dipercaya menjadi pemimpin kelompok, perantara damai, atau koordinator acara kegiatan.', 'INTERPERSONAL', 'Kecerdasan Interpersonal'],
            [24, 'Saya lebih bersemangat menyelesaikan tugas lewat kerja sama tim yang solid dibandingkan bekerja sendirian.', 'INTERPERSONAL', 'Kecerdasan Interpersonal'],

            // 7. Intrapersonal (Reflektif) (4 butir)
            [25, 'Saya sangat memahami kelebihan, kelemahan, minat, dan prinsip hidup yang saya pegang teguh.', 'INTRAPERSONAL', 'Kecerdasan Intrapersonal'],
            [26, 'Saya gemar menulis buku harian (jurnal refleksi), merenungkan tujuan masa depan, dan mengevaluasi diri sendiri.', 'INTRAPERSONAL', 'Kecerdasan Intrapersonal'],
            [27, 'Saya memiliki kemandirian belajar yang tinggi, tidak mudah terpengaruh tren teman sebaya, dan berpendirian kuat.', 'INTRAPERSONAL', 'Kecerdasan Intrapersonal'],
            [28, 'Saya mampu mengendalikan emosi dengan bijak saat menghadapi tekanan berat atau kegagalan.', 'INTRAPERSONAL', 'Kecerdasan Intrapersonal'],

            // 8. Naturalis (Lingkungan Alam) (4 butir)
            [29, 'Saya sangat menyukai alam terbuka, mengamati flora-fauna, mendaki gunung, atau berkemah di hutan.', 'NATURALIS', 'Kecerdasan Naturalis'],
            [30, 'Saya peka terhadap tanda-tanda perubahan cuaca alam dan peduli terhadap kelestarian lingkungan hidup bebas sampah.', 'NATURALIS', 'Kecerdasan Naturalis'],
            [31, 'Saya senang memelihara hewan peliharaan, merawat tanaman hias, atau berkebun organik.', 'NATURALIS', 'Kecerdasan Naturalis'],
            [32, 'Pelajaran Biologi, Geografi, dan Ilmu Pengetahuan Kebumian adalah topik yang sangat menarik bagi saya.', 'NATURALIS', 'Kecerdasan Naturalis'],
        ];

        foreach ($gardnerQuestions as $q) {
            BkPsychologicalTestQuestion::updateOrCreate(
                ['test_id' => $gardnerTest->id, 'question_number' => $q[0]],
                [
                    'question_text' => $q[1],
                    'dimension' => $q[2],
                    'dimension_name' => $q[3],
                    'scale_type' => 'LIKERT_4',
                    'options' => [
                        ['label' => 'Tidak Sesuai', 'score' => 1],
                        ['label' => 'Kadang-kadang', 'score' => 2],
                        ['label' => 'Sering', 'score' => 3],
                        ['label' => 'Sangat Menonjol', 'score' => 4],
                    ],
                ]
            );
        }
    }
}

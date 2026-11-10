<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SchoolSetting;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WebsiteContentController extends Controller
{
    /**
     * Default Fallback Data for Website
     */
    public const DEFAULT_SPEECHES = [
        [
            'id' => 'speech_utama',
            'title' => 'Menghidupkan Potensi, Meneguhkan Budi Pekerti di Era Transformasi Digital',
            'speaker_name' => 'Pian Sopian, S.Pd., M.M.Pd.',
            'speaker_title' => 'Kepala SMAN 1 Lengkong Kabupaten Sukabumi',
            'speech_date' => '2026-10-26',
            'quote' => 'Pendidikan sejati bukan sekadar mengisi wadah dengan informasi, melainkan menyalakan lentera budi pekerti, nalar kritis, dan integritas yang abadi.',
            'speech_p1' => 'Puji dan syukur senantiasa kita panjatkan ke hadirat Allah SWT. Selamat datang di portal resmi SMAN 1 Lengkong, wadah silaturahmi informasi, transparansi akademik, serta pusat inovasi pembelajaran bagi seluruh warga sekolah, orang tua, dan masyarakat luas.',
            'speech_p2' => 'Di era revolusi digital dan kurikulum nasional terkini berlandaskan Permendikdasmen No. 13 Tahun 2025, kami meyakini bahwa teknologi harus berpihak pada kemanusiaan dan kearifan budi pekerti. Sistem pembelajaran yang kami bangun dirancang untuk memfasilitasi Deep Learning (Pembelajaran Mendalam), melatih nalar kritis, serta membentengi generasi muda dengan nilai keimanan yang kokoh.',
            'speech_extended' => 'Melalui aplikasi sekolah mandiri ini—dari presensi cerdas 5 metode hingga materi interaktif berbasis AI—kami ingin memastikan setiap siswa mendapatkan ruang bertumbuh secara adil dan optimal. Kerjasama harmonis antara sekolah dan orang tua adalah kunci keberhasilan putra-putri kita. Terima kasih atas kepercayaan seluruh orang tua dan masyarakat Pajampangan. Mari bersama melangkah mewujudkan sekolah yang unggul, santun, dan mendunia.',
            'photo' => 'images/website/headmaster.jpg',
            'is_active' => true,
        ],
    ];

    public const DEFAULT_AGENDAS = [
        [
            'id' => 'agenda_1',
            'title' => 'Asesmen Sumatif Akhir Semester (ASAS) Berbasis CBT',
            'date' => '2026-11-25',
            'end_date' => '2026-11-30',
            'tag' => 'Akademik & CBT',
            'description' => 'Pelaksanaan ujian digital berbasis LMS Permendikdasmen No. 13 Tahun 2025 untuk seluruh siswa Fase E dan Fase F.',
            'icon' => 'laptop',
            'image' => null,
        ],
        [
            'id' => 'agenda_2',
            'title' => 'Gelar Karya P5 (Projek Penguatan Profil Pelajar Pancasila)',
            'date' => '2026-11-12',
            'end_date' => '2026-11-14',
            'tag' => 'Karakter & Karya',
            'description' => 'Pameran kreasi inovasi teknologi, kearifan lokal, dan kewirausahaan siswa SMAN 1 Lengkong.',
            'icon' => 'sparkles',
            'image' => null,
        ],
        [
            'id' => 'agenda_3',
            'title' => 'Peringatan Hari Guru Nasional & Apresiasi Pendidik',
            'date' => '2026-11-20',
            'end_date' => null,
            'tag' => 'Kegiatan Sekolah',
            'description' => 'Upacara bendera terpadu, pentas seni tradisi Sunda, dan penganugerahan guru inspiratif.',
            'icon' => 'academic-cap',
            'image' => null,
        ],
        [
            'id' => 'agenda_4',
            'title' => 'Lomba Prestasi Ekstrakurikuler & Turnamen Olahraga',
            'date' => '2026-12-05',
            'end_date' => '2026-12-10',
            'tag' => 'Kesiswaan',
            'description' => 'Ajang kejuaraan antar kelas (Classmeeting) mencakup Futsal, Voli, Robotik, PMR, dan Seni Tradisional.',
            'icon' => 'trophy',
            'image' => null,
        ],
    ];

    public const DEFAULT_FACILITIES = [
        [
            'id' => 'facility_1',
            'name' => 'Laboratorium Komputer & Multimedia',
            'desc' => 'Dilengkapi perangkat PC modern, koneksi serat optik kecepatan tinggi, server CBT mandiri, dan workstation grafis.',
            'icon' => 'desktop-computer',
            'image' => 'images/website/facilities/lab_komputer.jpg',
        ],
        [
            'id' => 'facility_2',
            'name' => 'Laboratorium Sains & Bioteknologi',
            'desc' => 'Fasilitas praktikum Fisika, Kimia, dan Biologi lengkap dengan mikroskop digital dan instrumen riset terstandar.',
            'icon' => 'beaker',
            'image' => 'images/website/facilities/lab_sains.jpg',
        ],
        [
            'id' => 'facility_3',
            'name' => 'Perpustakaan Digital Ramah Literasi',
            'desc' => 'Koleksi ribuan buku cetak, e-book kurikulum nasional, ruang baca ber-AC, dan area deep reading yang nyaman.',
            'icon' => 'book-open',
            'image' => 'images/website/facilities/perpustakaan.jpg',
        ],
        [
            'id' => 'facility_4',
            'name' => 'Masjid Sekolah & Pusat Tahfidz',
            'desc' => "Sarana ibadah representatif untuk shalat berjamaah, pembinaan akhlak mulia, dan setoran tahfidz Al-Qur'an Juz 30.",
            'icon' => 'library',
            'image' => 'images/website/facilities/masjid_sekolah.jpg',
        ],
        [
            'id' => 'facility_5',
            'name' => 'Gelanggang Olahraga & Lapangan Terpadu',
            'desc' => 'Sarana kebugaran untuk Futsal, Bola Voli, Basket, dan latihan kepanduan Pramuka serta Paskibra.',
            'icon' => 'trophy',
            'image' => 'images/website/facilities/gelanggang_olahraga.jpg',
        ],
        [
            'id' => 'facility_6',
            'name' => 'Smart Classroom & Kios Informasi Digital',
            'desc' => 'Ruang kelas interaktif berfasilitas proyektor cerdas, audio visual, serta monitor display TV informasi sekolah.',
            'icon' => 'sparkles',
            'image' => 'images/website/facilities/smart_classroom.jpg',
        ],
    ];

    public const DEFAULT_MISSIONS = [
        [
            'number' => '01',
            'title' => 'Iman & Ketaqwaan',
            'desc' => "Menumbuhkan penghayatan ajaran agama melalui pembiasaan shalat berjamaah, tahfidz Qur'an Juz 30, dan nilai toleransi.",
        ],
        [
            'number' => '02',
            'title' => 'Deep Learning',
            'desc' => 'Melaksanakan pembelajaran mendalam, berpikir kritis, eksploratif, dan pembinaan intensif ajang olimpiade sains/seni/olahraga.',
        ],
        [
            'number' => '03',
            'title' => 'Literasi Digital',
            'desc' => 'Membekali peserta didik dengan kecakapan komputasi, etika bermedia sosial, pemanfaatan AI yang bijak, dan literasi data.',
        ],
        [
            'number' => '04',
            'title' => 'Sekolah Hijau',
            'desc' => 'Membangun budaya Adiwiyata, pengelolaan sampah plastik mandiri, efisiensi energi, dan kelestarian alam Pajampangan.',
        ],
    ];

    public const DEFAULT_NEWS = [
        [
            'id' => 'news_1',
            'title' => 'SMAN 1 Lengkong Sukses Raih Juara 1 Olimpiade Robotik & AI Pelajar Jawa Barat 2026',
            'slug' => 'sman-1-lengkong-sukses-raih-juara-1-olimpiade-robotik-ai-2026',
            'category' => 'Prestasi',
            'date' => '2026-10-20',
            'author' => 'Humas & Tim Robotik',
            'excerpt' => 'Tim inovasi teknologi siswa berhasil merancang prototipe pemilah sampah cerdas berbasis computer vision yang memukau dewan juri tingkat provinsi.',
            'content' => "Prestasi gemilang kembali ditorehkan oleh peserta didik SMAN 1 Lengkong dalam ajang bergengsi Olimpiade Robotik dan Kecerdasan Buatan (AI) Pelajar Tingkat Jawa Barat 2026.\n\nDalam kompetisi yang diikuti oleh ratusan sekolah menengah se-Jawa Barat ini, tim perwakilan sekolah yang dipimpin oleh pembina ekstrakurikuler robotik berhasil mempresentasikan inovasi bertajuk 'Smart Eco-Sorter'. Robot ini dirancang untuk mendeteksi, memilah, dan mengklasifikasikan sampah organik, anorganik, dan limbah elektronik secara otomatis menggunakan sensor optik dan algoritma machine learning mandiri.\n\nKepala Sekolah menyampaikan rasa bangga dan apresiasi setinggi-tingginya kepada seluruh pembina dan siswa yang terlibat. 'Prestasi ini membuktikan bahwa siswa di daerah Pajampangan memiliki potensi dan daya saing teknologi yang setara dengan kota-kota besar. Ini adalah wujud nyata penerapan Kurikulum Nasional berorientasi Deep Learning,' ungkapnya.",
            'image' => 'images/website/classroom.jpg',
            'is_featured' => true,
        ],
        [
            'id' => 'news_2',
            'title' => 'Implementasi Kurikulum Nasional Permendikdasmen No. 13/2025 dengan Pendekatan Deep Learning',
            'slug' => 'implementasi-kurikulum-nasional-permendikdasmen-13-2025-deep-learning',
            'category' => 'Akademik',
            'date' => '2026-10-18',
            'author' => 'Tim Kurikulum',
            'excerpt' => 'Seluruh dewan guru menuntaskan lokakarya penyelarasan Elemen, Capaian Pembelajaran (CP), Tujuan Pembelajaran (TP), dan rubrik asesmen KKTP pada LMS mandiri.',
            'content' => "Sebagai langkah proaktif menyongsong regulasi pendidikan terkini, SMAN 1 Lengkong resmi menyelenggarakan pelatihan intensif implementasi Permendikdasmen No. 13 Tahun 2025 bagi seluruh guru mata pelajaran Fase E dan Fase F.\n\nPelatihan ini memfokuskan pada tiga pilar utama: Mindful Learning (Pembelajaran Bermakna), Meaningful Learning (Pembelajaran Kontekstual), dan Joyful Learning (Pembelajaran Menyenangkan). Selain itu, sistem LMS sekolah kini telah dilengkapi asisten pembuatan bahan ajar berbasis AI yang membantu guru merumuskan diferensiasi pembelajaran dan penilaian autentik.\n\nDengan integrasi ini, proses belajar mengajar tidak lagi berorientasi pada hafalan mekanis, melainkan pada penalaran kritis dan pemecahan masalah dunia nyata.",
            'image' => 'images/website/hero_school.jpg',
            'is_featured' => false,
        ],
        [
            'id' => 'news_3',
            'title' => 'Gelar Karya P5: Merajut Kearifan Budaya Sunda & Inovasi Wirausaha Kreatif Pelajar',
            'slug' => 'gelar-karya-p5-merajut-kearifan-budaya-sunda-wirausaha-kreatif',
            'category' => 'Kegiatan',
            'date' => '2026-10-15',
            'author' => 'Koordinator P5 & Kesiswaan',
            'excerpt' => 'Ratusan siswa memamerkan kerajinan kriya bambu lokal, olahan kuliner tradisional khas Pajampangan, serta pertunjukan seni gamelan dan tari merak.',
            'content' => "Suasana halaman utama SMAN 1 Lengkong semarak dengan Gelar Karya Projek Penguatan Profil Pelajar Pancasila (P5). Mengusung tema 'Kearifan Lokal Berdaya Saing Global', festival ini menjadi panggung kreasi bagi seluruh siswa kelas X dan XI.\n\nBeragam stan pameran memamerkan produk inovasi ramah lingkungan, mulai dari anyaman bambu modern, pupuk kompos daun sekolah, hingga makanan tradisional yang dikemas secara higienis dan memiliki nilai jual ekonomi.\n\nKegiatan ini turut dihadiri oleh Komite Sekolah, tokoh masyarakat, dan perwakilan orang tua murid yang memberikan respon sangat antusias terhadap kreativitas para pelajar.",
            'image' => 'images/website/extracurriculars.jpg',
            'is_featured' => false,
        ],
        [
            'id' => 'news_4',
            'title' => 'Portal Publik Orang Tua (SIP-Ortu) Dioptimalkan untuk Transparansi Kehadiran & Nilai Siswa',
            'slug' => 'portal-publik-orang-tua-dioptimalkan-transparansi-kehadiran-nilai',
            'category' => 'Pengumuman',
            'date' => '2026-10-10',
            'author' => 'Tim Pengembang IT',
            'excerpt' => 'Kini orang tua siswa dapat memantau presensi harian, buku kendali karakter 7 kebiasaan hebat, dan mencetak e-raport cukup dengan nomor NISN.',
            'content' => "Guna memperkuat sinergi antara sekolah dan keluarga, SMAN 1 Lengkong meluncurkan pembaruan besar pada Portal Publik Orang Tua. Layanan ini memungkinkan wali murid memantau rekapitulasi kehadiran anak secara real-time—termasuk presensi selfie radius GPS, kartu RFID, dan scan barcode.\n\nYang menarik, wali murid tidak diwajibkan menghafal password rumit; cukup memasukkan 10 digit NISN putra-putri, maka seluruh riwayat presensi jam per jam, rekap buku pembiasaan karakter, dan lembar capaian nilai formatif dapat diakses secara langsung dan transparan.\n\nSekolah berharap inovasi ini semakin mempererat rasa kepemilikan dan keterlibatan orang tua dalam mendampingi tumbuh kembang anak.",
            'image' => 'images/website/headmaster.jpg',
            'is_featured' => false,
        ],
    ];

    /**
     * Data Standar Dewan Guru & Staf Tata Usaha (TU)
     */
    /**
     * Staf Tata Usaha (TU) Bawaan dengan Tugas Pengganti Form Mapel
     */
    public static function getDefaultTuStaff(int $startOrder = 1): array
    {
        $order = $startOrder;

        return [
            [
                'id' => 'tu_1',
                'category' => 'staf_tu',
                'name' => 'Hj. Enok Hernawati, S.AP.',
                'nip' => '197608152007012011',
                'gender' => 'P',
                'employment_status' => 'PNS / ASN',
                'mapel' => '',
                'duty' => 'Kepala Urusan Tata Usaha',
                'tu_duty' => 'Kepala Urusan Tata Usaha (Kaur TU)',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ],
            [
                'id' => 'tu_2',
                'category' => 'staf_tu',
                'name' => 'Asep Saepuloh, S.Kom.',
                'nip' => '198504122010011018',
                'gender' => 'L',
                'employment_status' => 'PNS / ASN',
                'mapel' => '',
                'duty' => 'Administrasi Kesiswaan & Dapodik',
                'tu_duty' => 'Pengadministrasi Layanan Kesiswaan & Operator Dapodik',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ],
            [
                'id' => 'tu_3',
                'category' => 'staf_tu',
                'name' => 'Rina Marlina, S.E.',
                'nip' => '198909202022212008',
                'gender' => 'P',
                'employment_status' => 'PPPK',
                'mapel' => '',
                'duty' => 'Bendahara Sekolah & BOS/BOP',
                'tu_duty' => 'Bendahara Pengeluaran Pembantu & BOP/BOS',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ],
            [
                'id' => 'tu_4',
                'category' => 'staf_tu',
                'name' => 'Dedi Kurniawan, A.Md.',
                'nip' => '199103152020121009',
                'gender' => 'L',
                'employment_status' => 'PPPK',
                'mapel' => '',
                'duty' => 'Layanan Persuratan & Kepegawaian',
                'tu_duty' => 'Pengadministrasi Layanan Kepegawaian & Persuratan',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ],
            [
                'id' => 'tu_5',
                'category' => 'staf_tu',
                'name' => 'Hendra Gunawan, S.Kom.',
                'nip' => '199307252022211005',
                'gender' => 'L',
                'employment_status' => 'PPPK',
                'mapel' => '',
                'duty' => 'Teknisi Jaringan & Lab Komputer',
                'tu_duty' => 'Teknisi Jaringan, Server CBT & Laboran Komputer',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ],
            [
                'id' => 'tu_6',
                'category' => 'staf_tu',
                'name' => 'Siti Fatimah, S.I.Pust.',
                'nip' => '199511102024212015',
                'gender' => 'P',
                'employment_status' => 'PPPK',
                'mapel' => '',
                'duty' => 'Pengelola Perpustakaan Digital',
                'tu_duty' => 'Pengelola & Pustakawan Perpustakaan Digital',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Data Standar Dewan Guru & Staf Tata Usaha (TU)
     */
    public static function getDefaultStaff(): array
    {
        $staff = [];
        $order = 1;

        try {
            $teachers = Teacher::with('homeroomClass', 'user')->where('is_active', true)->orderBy('name')->get();
        } catch (\Throwable $e) {
            $teachers = collect();
        }

        if ($teachers->isNotEmpty()) {
            foreach ($teachers as $t) {
                $duty = 'Guru Pengajar';
                if ($t->title && stripos($t->title, 'Kepala') !== false) {
                    $duty = 'Kepala SMAN 1 Lengkong';
                } elseif ($t->user && in_array('wakasek_kurikulum', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Kurikulum';
                } elseif ($t->user && in_array('wakasek_kesiswaan', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Kesiswaan';
                } elseif ($t->user && in_array('wakasek_humas', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Humas';
                } elseif ($t->user && in_array('wakasek_sarpras', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Sarpras';
                } elseif ($t->user && in_array('bk', $t->user->all_roles, true)) {
                    $duty = 'Guru Bimbingan & Konseling (BK)';
                } elseif ($t->homeroomClass) {
                    $duty = 'Wali Kelas '.$t->homeroomClass->name;
                }

                $mapel = $t->title ?: 'Mata Pelajaran Umum';
                if (stripos($t->name, 'Pian Sopian') !== false) {
                    $duty = 'Kepala SMAN 1 Lengkong';
                    $mapel = 'Kimia';
                }

                $staff[] = [
                    'id' => 'teacher_'.$t->id,
                    'category' => 'guru',
                    'name' => $t->name,
                    'nip' => $t->nip ?: '-',
                    'gender' => $t->gender ?: 'L',
                    'employment_status' => 'PNS / ASN',
                    'mapel' => $mapel,
                    'duty' => $duty,
                    'tu_duty' => '',
                    'photo' => null,
                    'phone' => $t->phone ?: '',
                    'order' => $order++,
                    'is_active' => true,
                ];
            }
        } else {
            $staff[] = [
                'id' => 'sample_guru_1',
                'category' => 'guru',
                'name' => 'Pian Sopian, S.Pd., M.M.Pd.',
                'nip' => '198009132008011005',
                'gender' => 'L',
                'employment_status' => 'PNS / ASN',
                'mapel' => 'Kimia',
                'duty' => 'Kepala SMAN 1 Lengkong',
                'tu_duty' => '',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ];
            $staff[] = [
                'id' => 'sample_guru_2',
                'category' => 'guru',
                'name' => 'Eti Nurhayati, M.Pd.',
                'nip' => '197006121991032004',
                'gender' => 'P',
                'employment_status' => 'PNS / ASN',
                'mapel' => 'Bahasa Indonesia',
                'duty' => 'Wakasek Bid. Humas',
                'tu_duty' => '',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ];
            $staff[] = [
                'id' => 'sample_guru_3',
                'category' => 'guru',
                'name' => 'Duduh Aliansah, ST., S.Pd.',
                'nip' => '197405212014101002',
                'gender' => 'L',
                'employment_status' => 'PNS / ASN',
                'mapel' => 'Matematika',
                'duty' => 'Wali Kelas & Pembina Ekstra',
                'tu_duty' => '',
                'photo' => null,
                'phone' => '',
                'order' => $order++,
                'is_active' => true,
            ];
        }

        return array_merge($staff, self::getDefaultTuStaff($order));
    }

    /**
     * Mengambil daftar seluruh dewan guru & staf secara live, real-time tersinkronisasi
     * dengan database master Teacher & User SIS-SMA1LE, digabungkan dengan kustomisasi (foto, urutan).
     */
    /**
     * Membangun daftar awal seluruh dewan guru & staf dari Master Data Teacher & User (PTK).
     */
    public static function buildInitialStaffFromMaster(): array
    {
        $staff = [];
        $order = 1;

        try {
            $teachers = Teacher::with('homeroomClass', 'user')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            $teachers = collect();
        }

        if ($teachers->isNotEmpty()) {
            foreach ($teachers as $t) {
                $duty = 'Guru Pengajar';
                if ($t->title && stripos($t->title, 'Kepala') !== false) {
                    $duty = 'Kepala SMAN 1 Lengkong';
                } elseif ($t->user && in_array('wakasek_kurikulum', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Kurikulum';
                } elseif ($t->user && in_array('wakasek_kesiswaan', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Kesiswaan';
                } elseif ($t->user && in_array('wakasek_humas', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Humas';
                } elseif ($t->user && in_array('wakasek_sarpras', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Sarpras';
                } elseif ($t->user && in_array('bk', $t->user->all_roles, true)) {
                    $duty = 'Guru Bimbingan & Konseling (BK)';
                } elseif ($t->homeroomClass) {
                    $duty = 'Wali Kelas '.$t->homeroomClass->name;
                }

                if (stripos($t->name, 'Pian Sopian') !== false) {
                    $duty = 'Kepala SMAN 1 Lengkong';
                }

                $mapel = $t->title ?: 'Mata Pelajaran Umum';
                if (stripos($t->name, 'Pian Sopian') !== false) {
                    $mapel = 'Kimia';
                }

                $photo = null;
                if ($t->user?->avatar) {
                    $photo = 'storage/'.$t->user->avatar;
                }

                $staff[] = [
                    'id' => 'teacher_'.$t->id,
                    'category' => 'guru',
                    'name' => $t->name,
                    'nip' => $t->nip ?: '-',
                    'gender' => $t->gender ?: 'L',
                    'employment_status' => 'PNS / ASN',
                    'mapel' => $mapel,
                    'duty' => $duty,
                    'tu_duty' => '',
                    'photo' => $photo,
                    'phone' => $t->phone ?: '',
                    'order' => $order++,
                    'is_active' => true,
                ];
            }
        } else {
            $staff = self::getDefaultStaff();
            $order = count($staff) + 1;
        }

        // Ambil Staf TU dari User atau default
        try {
            $tuUsers = User::where('is_active', true)
                ->where(function ($q) {
                    $q->whereIn('role', ['tu', 'staff', 'kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'operator_dapodik', 'operator', 'tu_piket']);
                })
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            $tuUsers = collect();
        }

        if ($tuUsers->isNotEmpty()) {
            foreach ($tuUsers as $u) {
                $dutyTitle = $u->getRoleTitleFor($u->active_role ?: $u->role);
                $staff[] = [
                    'id' => 'tu_user_'.$u->id,
                    'category' => 'staf_tu',
                    'name' => $u->name,
                    'nip' => $u->nip ?: '-',
                    'gender' => $u->gender ?: 'L',
                    'employment_status' => 'Staf Tata Usaha',
                    'mapel' => '',
                    'duty' => $dutyTitle ?: 'Staf Administrasi',
                    'tu_duty' => $dutyTitle ?: 'Staf Tata Usaha',
                    'photo' => $u->avatar ? 'storage/'.$u->avatar : null,
                    'phone' => $u->phone ?: '',
                    'order' => $order++,
                    'is_active' => true,
                ];
            }
        } else {
            $defaultTu = self::getDefaultTuStaff($order);
            foreach ($defaultTu as $tu) {
                $staff[] = $tu;
            }
        }

        usort($staff, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        return $staff;
    }

    /**
     * Mengambil daftar master data Guru & PTK untuk autocomplete modal form
     */
    public static function getPtkMasterList(): array
    {
        $list = [];

        // 1. Data Dewan Guru
        try {
            $teachers = Teacher::with('homeroomClass', 'user')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            foreach ($teachers as $t) {
                $duty = 'Guru Pengajar';
                if ($t->title && stripos($t->title, 'Kepala') !== false) {
                    $duty = 'Kepala SMAN 1 Lengkong';
                } elseif ($t->user && in_array('wakasek_kurikulum', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Kurikulum';
                } elseif ($t->user && in_array('wakasek_kesiswaan', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Kesiswaan';
                } elseif ($t->user && in_array('wakasek_humas', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Humas';
                } elseif ($t->user && in_array('wakasek_sarpras', $t->user->all_roles, true)) {
                    $duty = 'Wakasek Bid. Sarpras';
                } elseif ($t->user && in_array('bk', $t->user->all_roles, true)) {
                    $duty = 'Guru Bimbingan & Konseling (BK)';
                } elseif ($t->homeroomClass) {
                    $duty = 'Wali Kelas '.$t->homeroomClass->name;
                }

                $list[] = [
                    'id' => 'teacher_'.$t->id,
                    'type' => 'guru',
                    'name' => $t->name,
                    'nip' => $t->nip ?: '-',
                    'gender' => $t->gender ?: 'L',
                    'employment_status' => 'PNS / ASN',
                    'duty' => $duty,
                    'mapel' => $t->title ?: 'Mata Pelajaran Umum',
                    'phone' => $t->phone ?: '',
                    'photo' => $t->user?->avatar ? 'storage/'.$t->user->avatar : null,
                ];
            }
        } catch (\Throwable $e) {
        }

        // 2. Data Tenaga Kependidikan / Staf TU
        try {
            $tuUsers = User::where('is_active', true)
                ->where(function ($q) {
                    $q->whereIn('role', ['tu', 'staff', 'kepala_administrasi', 'ta_kesiswaan', 'ta_kepegawaian', 'ta_persuratan', 'ta_sarpras', 'bendahara_bosp', 'bendahara_bopd', 'bendahara_gaji', 'operator_dapodik', 'operator', 'tu_piket']);
                })
                ->orderBy('name')
                ->get();

            foreach ($tuUsers as $u) {
                $dutyTitle = $u->getRoleTitleFor($u->active_role ?: $u->role);
                $list[] = [
                    'id' => 'user_'.$u->id,
                    'type' => 'staf_tu',
                    'name' => $u->name,
                    'nip' => $u->nip ?: '-',
                    'gender' => $u->gender ?: 'L',
                    'employment_status' => 'Staf Tata Usaha',
                    'duty' => $dutyTitle ?: 'Staf Administrasi',
                    'mapel' => '',
                    'phone' => $u->phone ?: '',
                    'photo' => $u->avatar ? 'storage/'.$u->avatar : null,
                ];
            }
        } catch (\Throwable $e) {
        }

        return $list;
    }

    /**
     * Mengambil daftar dewan guru & staf website.
     * Menggunakan website_staff_json yang tersimpan sebagai sumber data otoritatif
     * sehingga aksi edit dan hapus dari admin tidak pernah terhapus atau kembali ke awal.
     */
    public static function getLiveStaff(): array
    {
        $customStaff = SchoolSetting::get('website_staff_json');
        if ($customStaff !== null) {
            $decoded = json_decode($customStaff, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return self::buildInitialStaffFromMaster();
    }

    /**
     * Memastikan pengguna memiliki izin untuk mengelola bagian/tab tertentu.
     */
    protected function authorizeTab(string $tab): void
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageWebsiteContent() || ! $user->canAccessWebsiteContentTab($tab)) {
            abort(403, "Anda tidak memiliki hak akses untuk mengelola modul konten website ini ({$tab}).");
        }
    }

    /**
     * Tampilan Pusat Kelola Konten Website Sekolah
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        if (! $user || ! $user->canManageWebsiteContent()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengakses Kelola Konten Website.');
        }

        $allowedTabs = $user->allowedWebsiteContentTabs();
        if (empty($allowedTabs)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola konten website.');
        }

        // Default tab disesuaikan dengan tab pertama yang boleh diakses pengguna
        $defaultTab = in_array('news', $allowedTabs, true) ? 'news' : $allowedTabs[0];
        $requestedTab = $request->query('tab');
        if ($requestedTab !== null && ! in_array($requestedTab, $allowedTabs, true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk tab konten ini.');
        }
        $activeTab = $requestedTab ?: $defaultTab;

        $settings = SchoolSetting::allAsAssoc();

        // Sambutan Kepala Sekolah
        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $decodedSpeeches = $customSpeeches !== null ? json_decode($customSpeeches, true) : null;
        $speeches = is_array($decodedSpeeches) ? $decodedSpeeches : self::DEFAULT_SPEECHES;
        $hasActive = collect($speeches)->contains(fn ($s) => ! empty($s['is_active']));
        if (! $hasActive && count($speeches) > 0) {
            $speeches[0]['is_active'] = true;
        }

        // Agendas
        $customAgendas = SchoolSetting::get('website_agendas_json');
        $decodedAgendas = $customAgendas !== null ? json_decode($customAgendas, true) : null;
        $agendas = is_array($decodedAgendas) ? $decodedAgendas : self::DEFAULT_AGENDAS;
        foreach ($agendas as $i => &$a) {
            if (empty($a['id'])) {
                $a['id'] = 'agenda_'.($i + 1);
            }
            if (! array_key_exists('image', $a)) {
                $a['image'] = null;
            }
            if (! array_key_exists('end_date', $a)) {
                $a['end_date'] = null;
            }
        }
        unset($a);

        // Facilities
        $customFacilities = SchoolSetting::get('website_facilities_json');
        $decodedFacilities = $customFacilities !== null ? json_decode($customFacilities, true) : null;
        $facilities = is_array($decodedFacilities) ? $decodedFacilities : self::DEFAULT_FACILITIES;
        foreach ($facilities as $i => &$f) {
            if (empty($f['id'])) {
                $f['id'] = 'facility_'.($i + 1);
            }
            if (! array_key_exists('image', $f)) {
                $f['image'] = null;
            }
        }
        unset($f);

        // Missions
        $customMissions = SchoolSetting::get('website_misi_json');
        $decodedMissions = $customMissions !== null ? json_decode($customMissions, true) : null;
        $missions = is_array($decodedMissions) ? $decodedMissions : self::DEFAULT_MISSIONS;

        // News Articles
        $customNews = SchoolSetting::get('website_news_json');
        $decodedNews = $customNews !== null ? json_decode($customNews, true) : null;
        $newsArticles = is_array($decodedNews) ? $decodedNews : self::DEFAULT_NEWS;

        // Staff & Guru
        $staffMembers = self::getLiveStaff();

        // Master Data Guru & PTK untuk Pilihan Cepat di Modal
        $ptkMasterList = self::getPtkMasterList();

        return view('master.website_content.index', compact(
            'settings',
            'speeches',
            'agendas',
            'facilities',
            'missions',
            'newsArticles',
            'staffMembers',
            'ptkMasterList',
            'activeTab',
            'allowedTabs'
        ));
    }

    /**
     * Update Slogan & Hero Banner Depan
     */
    public function updateHero(Request $request): RedirectResponse
    {
        $this->authorizeTab('hero');

        $validated = $request->validate([
            'website_hero_headline' => ['required', 'string', 'max:255'],
            'website_hero_subheadline' => ['required', 'string', 'max:1000'],
            'website_humas_phone' => ['required', 'string', 'max:30'],
            'website_humas_message' => ['required', 'string', 'max:255'],
            'website_service_hours' => ['nullable', 'string', 'max:100'],
        ]);

        SchoolSetting::set('website_hero_headline', $validated['website_hero_headline'], 'website', 'Headline besar hero website');
        SchoolSetting::set('website_hero_subheadline', $validated['website_hero_subheadline'], 'website', 'Deskripsi subheadline hero');
        SchoolSetting::set('website_humas_phone', preg_replace('/[^0-9]/', '', $validated['website_humas_phone']), 'website', 'No WhatsApp Humas');
        SchoolSetting::set('website_humas_message', $validated['website_humas_message'], 'website', 'Pesan template chat WA');
        if (array_key_exists('website_service_hours', $validated) && filled($validated['website_service_hours'])) {
            SchoolSetting::set('website_service_hours', $validated['website_service_hours'], 'website', 'Jam Layanan Pelajar');
        }

        AuditLog::log('UPDATE_WEBSITE_HERO', 'SchoolSetting', null, null, $validated);

        return redirect()->route('master.website_content', ['tab' => 'hero'])
            ->with('success', 'Slogan dan informasi hero banner berhasil diperbarui!');
    }

    /**
     * Update Running Text Header Website
     */
    public function updateRunningText(Request $request): RedirectResponse
    {
        $this->authorizeTab('running_text');

        $validated = $request->validate([
            'website_running_text' => ['required', 'string', 'max:1000'],
            'website_running_text_badge' => ['nullable', 'string', 'max:50'],
            'website_running_text_speed' => ['nullable', 'string', 'in:slow,normal,fast'],
        ]);

        SchoolSetting::set('website_running_text', trim($validated['website_running_text']), 'website', 'Teks pengumuman berjalan header');
        SchoolSetting::set('website_running_text_badge', trim($validated['website_running_text_badge'] ?? 'INFO SEKOLAH'), 'website', 'Badge label running text header');
        SchoolSetting::set('website_running_text_speed', $validated['website_running_text_speed'] ?? 'normal', 'website', 'Kecepatan running text header');

        AuditLog::log('UPDATE_WEBSITE_RUNNING_TEXT', 'SchoolSetting', null, null, $validated);

        return redirect()->route('master.website_content', ['tab' => 'running_text'])
            ->with('success', 'Teks pengumuman berjalan (running text) header website berhasil diperbarui!');
    }

    /**
     * Update Sambutan Kepala Sekolah
     */
    public function updateHeadmaster(Request $request): RedirectResponse
    {
        $this->authorizeTab('headmaster');

        $validated = $request->validate([
            'website_headmaster_name' => ['required', 'string', 'max:150'],
            'website_headmaster_title' => ['required', 'string', 'max:150'],
            'website_headmaster_speech_title' => ['required', 'string', 'max:255'],
            'website_headmaster_speech_p1' => ['required', 'string', 'max:2000'],
            'website_headmaster_speech_p2' => ['nullable', 'string', 'max:2000'],
            'website_headmaster_speech_extended' => ['nullable', 'string', 'max:3000'],
            'website_headmaster_quote' => ['required', 'string', 'max:500'],
            'headmaster_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        SchoolSetting::set('website_headmaster_name', $validated['website_headmaster_name'], 'website', 'Nama Kepala Sekolah di Website');
        SchoolSetting::set('website_headmaster_title', $validated['website_headmaster_title'], 'website', 'Gelar & Jabatan Kepala Sekolah');
        SchoolSetting::set('website_headmaster_speech_title', $validated['website_headmaster_speech_title'], 'website', 'Judul Sambutan Kepala Sekolah');
        SchoolSetting::set('website_headmaster_speech_p1', $validated['website_headmaster_speech_p1'], 'website', 'Paragraf Sambutan 1');
        SchoolSetting::set('website_headmaster_speech_p2', $validated['website_headmaster_speech_p2'] ?? '', 'website', 'Paragraf Sambutan 2');
        SchoolSetting::set('website_headmaster_speech_extended', $validated['website_headmaster_speech_extended'] ?? '', 'website', 'Paragraf Sambutan Selengkapnya');
        SchoolSetting::set('website_headmaster_quote', $validated['website_headmaster_quote'], 'website', 'Mutiara Kata Kepala Sekolah');

        // Update global headmaster_name setting
        SchoolSetting::set('headmaster_name', $validated['website_headmaster_name'], 'general', 'Nama Kepala Sekolah');

        if ($request->hasFile('headmaster_photo')) {
            $this->saveWebsiteImage($request->file('headmaster_photo'), 'headmaster.jpg', 'website_headmaster_photo');
        }

        // Sync with active speech in website_speeches_json
        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $speeches = $customSpeeches ? (json_decode($customSpeeches, true) ?: self::DEFAULT_SPEECHES) : self::DEFAULT_SPEECHES;
        $updatedActive = false;
        foreach ($speeches as &$s) {
            if (! empty($s['is_active'])) {
                $s['title'] = $validated['website_headmaster_speech_title'];
                $s['speaker_name'] = $validated['website_headmaster_name'];
                $s['speaker_title'] = $validated['website_headmaster_title'];
                $s['quote'] = $validated['website_headmaster_quote'];
                $s['speech_p1'] = $validated['website_headmaster_speech_p1'];
                $s['speech_p2'] = $validated['website_headmaster_speech_p2'] ?? '';
                $s['speech_extended'] = $validated['website_headmaster_speech_extended'] ?? '';
                $updatedActive = true;
                break;
            }
        }
        unset($s);

        if (! $updatedActive && count($speeches) > 0) {
            $speeches[0]['title'] = $validated['website_headmaster_speech_title'];
            $speeches[0]['speaker_name'] = $validated['website_headmaster_name'];
            $speeches[0]['speaker_title'] = $validated['website_headmaster_title'];
            $speeches[0]['quote'] = $validated['website_headmaster_quote'];
            $speeches[0]['speech_p1'] = $validated['website_headmaster_speech_p1'];
            $speeches[0]['speech_p2'] = $validated['website_headmaster_speech_p2'] ?? '';
            $speeches[0]['speech_extended'] = $validated['website_headmaster_speech_extended'] ?? '';
            $speeches[0]['is_active'] = true;
        }
        SchoolSetting::set('website_speeches_json', json_encode(array_values($speeches)), 'website', 'Daftar Sambutan Kepala Sekolah');

        AuditLog::log('UPDATE_WEBSITE_HEADMASTER', 'SchoolSetting', null, null, [
            'name' => $validated['website_headmaster_name'],
            'title' => $validated['website_headmaster_title'],
        ]);

        return redirect()->route('master.website_content', ['tab' => 'headmaster'])
            ->with('success', 'Sambutan dan profil Kepala Sekolah berhasil diperbarui!');
    }

    /**
     * Tambah Sambutan Baru
     */
    public function storeSpeech(Request $request): RedirectResponse
    {
        $this->authorizeTab('headmaster');

        $validated = $request->validate([
            'speech_title' => ['required', 'string', 'max:255'],
            'speaker_name' => ['required', 'string', 'max:150'],
            'speaker_title' => ['required', 'string', 'max:150'],
            'speech_date' => ['nullable', 'date'],
            'speech_quote' => ['required', 'string', 'max:500'],
            'speech_p1' => ['required', 'string', 'max:2000'],
            'speech_p2' => ['nullable', 'string', 'max:2000'],
            'speech_extended' => ['nullable', 'string', 'max:3000'],
            'speech_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $speeches = $customSpeeches ? (json_decode($customSpeeches, true) ?: self::DEFAULT_SPEECHES) : self::DEFAULT_SPEECHES;

        $speechId = 'speech_'.time().'_'.Str::random(4);
        $photoPath = 'images/website/headmaster.jpg';

        if ($request->hasFile('speech_photo')) {
            $file = $request->file('speech_photo');
            $dir = public_path('images/website/speeches');
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            $filename = $speechId.'.'.($file->getClientOriginalExtension() ?: 'jpg');
            $file->move($dir, $filename);
            $photoPath = 'images/website/speeches/'.$filename.'?v='.time();
        }

        $isActive = $request->boolean('is_active') || empty($speeches);

        if ($isActive) {
            foreach ($speeches as &$s) {
                $s['is_active'] = false;
            }
            unset($s);

            // Sync to global settings
            SchoolSetting::set('website_headmaster_name', $validated['speaker_name'], 'website', 'Nama Kepala Sekolah');
            SchoolSetting::set('website_headmaster_title', $validated['speaker_title'], 'website', 'Gelar & Jabatan Kepala Sekolah');
            SchoolSetting::set('website_headmaster_speech_title', $validated['speech_title'], 'website', 'Judul Sambutan');
            SchoolSetting::set('website_headmaster_speech_p1', $validated['speech_p1'], 'website', 'Paragraf Sambutan 1');
            SchoolSetting::set('website_headmaster_speech_p2', $validated['speech_p2'] ?? '', 'website', 'Paragraf Sambutan 2');
            SchoolSetting::set('website_headmaster_speech_extended', $validated['speech_extended'] ?? '', 'website', 'Paragraf Sambutan Selengkapnya');
            SchoolSetting::set('website_headmaster_quote', $validated['speech_quote'], 'website', 'Mutiara Kata Kepala Sekolah');
            SchoolSetting::set('headmaster_name', $validated['speaker_name'], 'general', 'Nama Kepala Sekolah');
            if ($request->hasFile('speech_photo')) {
                SchoolSetting::set('website_headmaster_photo', $photoPath, 'website', 'Foto Kepala Sekolah');
            }
        }

        $newSpeech = [
            'id' => $speechId,
            'title' => trim($validated['speech_title']),
            'speaker_name' => trim($validated['speaker_name']),
            'speaker_title' => trim($validated['speaker_title']),
            'speech_date' => $validated['speech_date'] ?: date('Y-m-d'),
            'quote' => trim($validated['speech_quote']),
            'speech_p1' => trim($validated['speech_p1']),
            'speech_p2' => trim($validated['speech_p2'] ?? ''),
            'speech_extended' => trim($validated['speech_extended'] ?? ''),
            'photo' => $photoPath,
            'is_active' => $isActive,
        ];

        array_unshift($speeches, $newSpeech);
        SchoolSetting::set('website_speeches_json', json_encode(array_values($speeches)), 'website', 'Daftar Sambutan Kepala Sekolah');

        AuditLog::log('CREATE_WEBSITE_SPEECH', 'SchoolSetting', null, null, ['id' => $speechId, 'title' => $newSpeech['title']]);

        return redirect()->route('master.website_content', ['tab' => 'headmaster'])
            ->with('success', 'Sambutan baru berhasil ditambahkan!');
    }

    /**
     * Update Sambutan
     */
    public function updateSpeech(Request $request, string $id): RedirectResponse
    {
        $this->authorizeTab('headmaster');

        $validated = $request->validate([
            'speech_title' => ['required', 'string', 'max:255'],
            'speaker_name' => ['required', 'string', 'max:150'],
            'speaker_title' => ['required', 'string', 'max:150'],
            'speech_date' => ['nullable', 'date'],
            'speech_quote' => ['required', 'string', 'max:500'],
            'speech_p1' => ['required', 'string', 'max:2000'],
            'speech_p2' => ['nullable', 'string', 'max:2000'],
            'speech_extended' => ['nullable', 'string', 'max:3000'],
            'speech_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $speeches = $customSpeeches ? (json_decode($customSpeeches, true) ?: self::DEFAULT_SPEECHES) : self::DEFAULT_SPEECHES;

        $isActive = $request->boolean('is_active');
        if ($isActive) {
            foreach ($speeches as &$s) {
                $s['is_active'] = false;
            }
            unset($s);
        }

        $found = false;
        foreach ($speeches as &$s) {
            if ($s['id'] === $id) {
                $s['title'] = trim($validated['speech_title']);
                $s['speaker_name'] = trim($validated['speaker_name']);
                $s['speaker_title'] = trim($validated['speaker_title']);
                $s['speech_date'] = $validated['speech_date'] ?: ($s['speech_date'] ?? date('Y-m-d'));
                $s['quote'] = trim($validated['speech_quote']);
                $s['speech_p1'] = trim($validated['speech_p1']);
                $s['speech_p2'] = trim($validated['speech_p2'] ?? '');
                $s['speech_extended'] = trim($validated['speech_extended'] ?? '');
                if ($isActive) {
                    $s['is_active'] = true;
                }

                if ($request->hasFile('speech_photo')) {
                    $file = $request->file('speech_photo');
                    $dir = public_path('images/website/speeches');
                    if (! File::isDirectory($dir)) {
                        File::makeDirectory($dir, 0755, true, true);
                    }
                    $filename = $id.'_'.time().'.'.($file->getClientOriginalExtension() ?: 'jpg');
                    $file->move($dir, $filename);
                    $s['photo'] = 'images/website/speeches/'.$filename.'?v='.time();
                }

                if (! empty($s['is_active'])) {
                    SchoolSetting::set('website_headmaster_name', $s['speaker_name'], 'website', 'Nama Kepala Sekolah');
                    SchoolSetting::set('website_headmaster_title', $s['speaker_title'], 'website', 'Gelar & Jabatan Kepala Sekolah');
                    SchoolSetting::set('website_headmaster_speech_title', $s['title'], 'website', 'Judul Sambutan');
                    SchoolSetting::set('website_headmaster_speech_p1', $s['speech_p1'], 'website', 'Paragraf Sambutan 1');
                    SchoolSetting::set('website_headmaster_speech_p2', $s['speech_p2'] ?? '', 'website', 'Paragraf Sambutan 2');
                    SchoolSetting::set('website_headmaster_speech_extended', $s['speech_extended'] ?? '', 'website', 'Paragraf Sambutan Selengkapnya');
                    SchoolSetting::set('website_headmaster_quote', $s['quote'], 'website', 'Mutiara Kata Kepala Sekolah');
                    SchoolSetting::set('headmaster_name', $s['speaker_name'], 'general', 'Nama Kepala Sekolah');
                    if (! empty($s['photo'])) {
                        SchoolSetting::set('website_headmaster_photo', $s['photo'], 'website', 'Foto Kepala Sekolah');
                    }
                }

                $found = true;
                break;
            }
        }
        unset($s);

        if ($found) {
            SchoolSetting::set('website_speeches_json', json_encode(array_values($speeches)), 'website', 'Daftar Sambutan Kepala Sekolah');
            AuditLog::log('UPDATE_WEBSITE_SPEECH', 'SchoolSetting', null, null, ['id' => $id]);
        }

        return redirect()->route('master.website_content', ['tab' => 'headmaster'])
            ->with('success', 'Sambutan kepala sekolah berhasil diperbarui!');
    }

    /**
     * Hapus Sambutan
     */
    public function destroySpeech(string $id): RedirectResponse
    {
        $this->authorizeTab('headmaster');

        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $speeches = $customSpeeches ? (json_decode($customSpeeches, true) ?: self::DEFAULT_SPEECHES) : self::DEFAULT_SPEECHES;

        if (count($speeches) <= 1) {
            return redirect()->route('master.website_content', ['tab' => 'headmaster'])
                ->with('error', 'Tidak dapat menghapus satu-satunya sambutan. Silakan edit atau tambahkan sambutan pengganti terlebih dahulu.');
        }

        $wasActive = false;
        $filtered = [];
        foreach ($speeches as $s) {
            if ((string) ($s['id'] ?? '') === (string) $id) {
                if (! empty($s['is_active'])) {
                    $wasActive = true;
                }
            } else {
                $filtered[] = $s;
            }
        }

        if ($wasActive && count($filtered) > 0) {
            $filtered[0]['is_active'] = true;
            $s = $filtered[0];
            SchoolSetting::set('website_headmaster_name', $s['speaker_name'], 'website', 'Nama Kepala Sekolah');
            SchoolSetting::set('website_headmaster_title', $s['speaker_title'], 'website', 'Gelar & Jabatan Kepala Sekolah');
            SchoolSetting::set('website_headmaster_speech_title', $s['title'], 'website', 'Judul Sambutan');
            SchoolSetting::set('website_headmaster_speech_p1', $s['speech_p1'], 'website', 'Paragraf Sambutan 1');
            SchoolSetting::set('website_headmaster_speech_p2', $s['speech_p2'] ?? '', 'website', 'Paragraf Sambutan 2');
            SchoolSetting::set('website_headmaster_speech_extended', $s['speech_extended'] ?? '', 'website', 'Paragraf Sambutan Selengkapnya');
            SchoolSetting::set('website_headmaster_quote', $s['quote'], 'website', 'Mutiara Kata Kepala Sekolah');
            SchoolSetting::set('headmaster_name', $s['speaker_name'], 'general', 'Nama Kepala Sekolah');
        }

        SchoolSetting::set('website_speeches_json', json_encode(array_values($filtered)), 'website', 'Daftar Sambutan Kepala Sekolah');
        AuditLog::log('DELETE_WEBSITE_SPEECH', 'SchoolSetting', null, null, ['id' => $id]);

        return redirect()->route('master.website_content', ['tab' => 'headmaster'])
            ->with('success', 'Sambutan berhasil dihapus.');
    }

    /**
     * Jadikan Sambutan Aktif
     */
    public function activateSpeech(string $id): RedirectResponse
    {
        $this->authorizeTab('headmaster');

        $customSpeeches = SchoolSetting::get('website_speeches_json');
        $speeches = $customSpeeches ? (json_decode($customSpeeches, true) ?: self::DEFAULT_SPEECHES) : self::DEFAULT_SPEECHES;

        $targetSpeech = null;
        foreach ($speeches as &$s) {
            if ($s['id'] === $id) {
                $s['is_active'] = true;
                $targetSpeech = $s;
            } else {
                $s['is_active'] = false;
            }
        }
        unset($s);

        if ($targetSpeech) {
            SchoolSetting::set('website_speeches_json', json_encode(array_values($speeches)), 'website', 'Daftar Sambutan Kepala Sekolah');
            SchoolSetting::set('website_headmaster_name', $targetSpeech['speaker_name'], 'website', 'Nama Kepala Sekolah');
            SchoolSetting::set('website_headmaster_title', $targetSpeech['speaker_title'], 'website', 'Gelar & Jabatan Kepala Sekolah');
            SchoolSetting::set('website_headmaster_speech_title', $targetSpeech['title'], 'website', 'Judul Sambutan');
            SchoolSetting::set('website_headmaster_speech_p1', $targetSpeech['speech_p1'], 'website', 'Paragraf Sambutan 1');
            SchoolSetting::set('website_headmaster_speech_p2', $targetSpeech['speech_p2'] ?? '', 'website', 'Paragraf Sambutan 2');
            SchoolSetting::set('website_headmaster_speech_extended', $targetSpeech['speech_extended'] ?? '', 'website', 'Paragraf Sambutan Selengkapnya');
            SchoolSetting::set('website_headmaster_quote', $targetSpeech['quote'], 'website', 'Mutiara Kata Kepala Sekolah');
            SchoolSetting::set('headmaster_name', $targetSpeech['speaker_name'], 'general', 'Nama Kepala Sekolah');
            if (! empty($targetSpeech['photo'])) {
                SchoolSetting::set('website_headmaster_photo', $targetSpeech['photo'], 'website', 'Foto Kepala Sekolah');
            }
            AuditLog::log('ACTIVATE_WEBSITE_SPEECH', 'SchoolSetting', null, null, ['id' => $id]);
        }

        return redirect()->route('master.website_content', ['tab' => 'headmaster'])
            ->with('success', 'Sambutan "'.($targetSpeech['title'] ?? '').'" sekarang aktif ditayangkan di portal utama!');
    }

    /**
     * Update Visi & Misi Sekolah
     */
    public function updateVisionMission(Request $request): RedirectResponse
    {
        $this->authorizeTab('vision');

        $validated = $request->validate([
            'website_visi' => ['required', 'string', 'max:1000'],
            'website_visi_sub' => ['nullable', 'string', 'max:500'],
            'misi_title' => ['required', 'array', 'min:1'],
            'misi_title.*' => ['required', 'string', 'max:100'],
            'misi_desc' => ['required', 'array', 'min:1'],
            'misi_desc.*' => ['required', 'string', 'max:500'],
        ]);

        SchoolSetting::set('website_visi', $validated['website_visi'], 'website', 'Visi Sekolah');
        SchoolSetting::set('website_visi_sub', $validated['website_visi_sub'] ?? '', 'website', 'Sub Visi');

        $missions = [];
        foreach ($validated['misi_title'] as $index => $title) {
            $missions[] = [
                'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'title' => trim($title),
                'desc' => trim($validated['misi_desc'][$index] ?? ''),
            ];
        }
        SchoolSetting::set('website_misi_json', json_encode($missions), 'website', 'Daftar Misi Sekolah');

        AuditLog::log('UPDATE_WEBSITE_VISION_MISSION', 'SchoolSetting');

        return redirect()->route('master.website_content', ['tab' => 'vision'])
            ->with('success', 'Visi dan Misi sekolah berhasil diperbarui!');
    }

    /**
     * Upload & Ganti Foto / Media Website
     */
    public function updateMedia(Request $request): RedirectResponse
    {
        $this->authorizeTab('media');

        $request->validate([
            'image_type' => ['required', 'in:hero,headmaster,classroom,extracurriculars'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $file = $request->file('photo');
        $type = $request->input('image_type');

        $map = [
            'hero' => ['filename' => 'hero_school.jpg', 'setting' => 'website_hero_photo', 'name' => 'Gedung Utama / Hero Banner'],
            'headmaster' => ['filename' => 'headmaster.jpg', 'setting' => 'website_headmaster_photo', 'name' => 'Kepala Sekolah'],
            'classroom' => ['filename' => 'classroom.jpg', 'setting' => 'website_classroom_photo', 'name' => 'Ruang Kelas & Digital'],
            'extracurriculars' => ['filename' => 'extracurriculars.jpg', 'setting' => 'website_extracurriculars_photo', 'name' => 'Kegiatan Ekstrakurikuler'],
        ];

        $target = $map[$type];
        $this->saveWebsiteImage($file, $target['filename'], $target['setting']);

        AuditLog::log('UPDATE_WEBSITE_MEDIA', 'SchoolSetting', null, null, ['type' => $type]);

        return redirect()->route('master.website_content', ['tab' => 'media'])
            ->with('success', "Foto {$target['name']} berhasil diperbarui!");
    }

    /**
     * Tambah Berita Baru
     */
    public function storeNews(Request $request): RedirectResponse
    {
        $this->authorizeTab('news');

        $validated = $request->validate([
            'news_title' => ['required', 'string', 'max:255'],
            'news_category' => ['required', 'string', 'max:50'],
            'news_date' => ['required', 'date'],
            'news_author' => ['required', 'string', 'max:100'],
            'news_excerpt' => ['required', 'string', 'max:1000'],
            'news_content' => ['required', 'string', 'max:250000'],
            'news_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $customNews = SchoolSetting::get('website_news_json');
        $newsArticles = $customNews ? (json_decode($customNews, true) ?: self::DEFAULT_NEWS) : self::DEFAULT_NEWS;

        $newsId = 'news_'.time().'_'.Str::random(5);
        $imagePath = 'images/website/classroom.jpg'; // default visual

        if ($request->hasFile('news_photo')) {
            $file = $request->file('news_photo');
            $dir = public_path('images/website/news');
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            $filename = $newsId.'.'.($file->getClientOriginalExtension() ?: 'jpg');
            $file->move($dir, $filename);
            $imagePath = 'images/website/news/'.$filename.'?v='.time();
        }

        $user = auth()->user();
        $creatorName = $user ? $user->name : 'Administrator';
        $creatorRole = $user ? ($user->active_role ?: $user->role) : 'admin';
        $creatorRoleTitle = $user ? $user->getRoleTitleFor($creatorRole) : 'Administrator';

        $newArticle = [
            'id' => $newsId,
            'title' => trim($validated['news_title']),
            'slug' => Str::slug($validated['news_title']),
            'category' => trim($validated['news_category']),
            'date' => $validated['news_date'],
            'author' => trim($validated['news_author']),
            'created_by_user_id' => $user?->id,
            'created_by_name' => $creatorName,
            'created_by_role' => $creatorRoleTitle,
            'published_by' => $creatorName.' ('.$creatorRoleTitle.')',
            'created_at' => now()->toIso8601String(),
            'excerpt' => trim($validated['news_excerpt']),
            'content' => trim($validated['news_content']),
            'image' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
        ];

        // Prepend to top
        array_unshift($newsArticles, $newArticle);
        SchoolSetting::set('website_news_json', json_encode(array_values($newsArticles)), 'website', 'Daftar Berita & Kabar Website');

        AuditLog::log('CREATE_WEBSITE_NEWS', 'SchoolSetting', null, null, ['id' => $newsId, 'title' => $newArticle['title']]);

        return redirect()->route('master.website_content', ['tab' => 'news'])
            ->with('success', 'Berita berhasil diterbitkan ke portal website sekolah!');
    }

    /**
     * Update Berita Sekolah
     */
    public function updateNews(Request $request, string $id): RedirectResponse
    {
        $this->authorizeTab('news');

        $validated = $request->validate([
            'news_title' => ['required', 'string', 'max:255'],
            'news_category' => ['required', 'string', 'max:50'],
            'news_date' => ['required', 'date'],
            'news_author' => ['required', 'string', 'max:100'],
            'news_excerpt' => ['required', 'string', 'max:1000'],
            'news_content' => ['required', 'string', 'max:250000'],
            'news_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $customNews = SchoolSetting::get('website_news_json');
        $newsArticles = $customNews ? (json_decode($customNews, true) ?: self::DEFAULT_NEWS) : self::DEFAULT_NEWS;

        $user = auth()->user();
        $editorName = $user ? $user->name : 'Administrator';
        $editorRole = $user ? ($user->active_role ?: $user->role) : 'admin';
        $editorRoleTitle = $user ? $user->getRoleTitleFor($editorRole) : 'Administrator';

        $found = false;
        foreach ($newsArticles as &$article) {
            if ($article['id'] === $id) {
                $article['title'] = trim($validated['news_title']);
                $article['slug'] = Str::slug($validated['news_title']);
                $article['category'] = trim($validated['news_category']);
                $article['date'] = $validated['news_date'];
                $article['author'] = trim($validated['news_author']);
                $article['excerpt'] = trim($validated['news_excerpt']);
                $article['content'] = trim($validated['news_content']);
                $article['is_featured'] = $request->boolean('is_featured');
                $article['updated_by_user_id'] = $user?->id;
                $article['updated_by_name'] = $editorName;
                $article['updated_by_role'] = $editorRoleTitle;
                $article['last_modified_by'] = $editorName.' ('.$editorRoleTitle.')';
                $article['updated_at'] = now()->toIso8601String();

                if ($request->hasFile('news_photo')) {
                    $file = $request->file('news_photo');
                    $dir = public_path('images/website/news');
                    if (! File::isDirectory($dir)) {
                        File::makeDirectory($dir, 0755, true, true);
                    }
                    $filename = $id.'_'.time().'.'.($file->getClientOriginalExtension() ?: 'jpg');
                    $file->move($dir, $filename);
                    $article['image'] = 'images/website/news/'.$filename.'?v='.time();
                }

                $found = true;
                break;
            }
        }
        unset($article);

        if ($found) {
            SchoolSetting::set('website_news_json', json_encode(array_values($newsArticles)), 'website', 'Daftar Berita & Kabar Website');
            AuditLog::log('UPDATE_WEBSITE_NEWS', 'SchoolSetting', null, null, ['id' => $id]);
        }

        return redirect()->route('master.website_content', ['tab' => 'news'])
            ->with('success', 'Berita berhasil diperbarui!');
    }

    /**
     * Hapus Berita Sekolah
     */
    public function destroyNews(string $id): RedirectResponse
    {
        $this->authorizeTab('news');

        $customNews = SchoolSetting::get('website_news_json');
        $decodedNews = $customNews !== null ? json_decode($customNews, true) : null;
        $newsArticles = is_array($decodedNews) ? $decodedNews : self::DEFAULT_NEWS;

        $filtered = array_filter($newsArticles, fn ($item) => (string) ($item['id'] ?? '') !== (string) $id);

        SchoolSetting::set('website_news_json', json_encode(array_values($filtered)), 'website', 'Daftar Berita & Kabar Website');
        AuditLog::log('DELETE_WEBSITE_NEWS', 'SchoolSetting', null, null, ['id' => $id]);

        return redirect()->route('master.website_content', ['tab' => 'news'])
            ->with('success', 'Berita berhasil dihapus dari website.');
    }

    /**
     * Upload Gambar Inline untuk Editor Berita Kaya
     */
    public function uploadNewsInlineImage(Request $request): JsonResponse
    {
        $this->authorizeTab('news');

        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        $file = $request->file('image');
        $dir = public_path('images/website/news_inline');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $filename = 'inline_'.time().'_'.Str::random(8).'.'.($file->getClientOriginalExtension() ?: 'jpg');
        $file->move($dir, $filename);
        $url = asset('images/website/news_inline/'.$filename);

        return response()->json([
            'success' => true,
            'url' => $url,
            'filename' => $filename,
        ]);
    }

    /**
     * Tambah Agenda Sekolah Baru
     */
    public function storeAgenda(Request $request): RedirectResponse
    {
        $this->authorizeTab('agendas');

        $validated = $request->validate([
            'agenda_title' => ['required', 'string', 'max:200'],
            'agenda_date' => ['required', 'date'],
            'agenda_end_date' => ['nullable', 'date', 'after_or_equal:agenda_date'],
            'agenda_tag' => ['required', 'string', 'max:50'],
            'agenda_description' => ['required', 'string', 'max:1000'],
            'agenda_icon' => ['nullable', 'string', 'max:30'],
            'agenda_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $customAgendas = SchoolSetting::get('website_agendas_json');
        $decodedAgendas = $customAgendas !== null ? json_decode($customAgendas, true) : null;
        $agendas = is_array($decodedAgendas) ? $decodedAgendas : self::DEFAULT_AGENDAS;

        $agendaId = 'agenda_'.time().'_'.Str::random(4);
        $imagePath = null;

        if ($request->hasFile('agenda_image')) {
            $file = $request->file('agenda_image');
            $dir = public_path('images/website/agendas');
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            $filename = $agendaId.'.'.($file->getClientOriginalExtension() ?: 'jpg');
            $file->move($dir, $filename);
            $imagePath = 'images/website/agendas/'.$filename.'?v='.time();
        }

        $user = auth()->user();
        $creatorName = $user ? $user->name : 'Administrator';
        $creatorRole = $user ? ($user->active_role ?: $user->role) : 'admin';
        $creatorRoleTitle = $user ? $user->getRoleTitleFor($creatorRole) : 'Administrator';

        $newAgenda = [
            'id' => $agendaId,
            'title' => trim($validated['agenda_title']),
            'date' => $validated['agenda_date'],
            'end_date' => ! empty($validated['agenda_end_date']) ? $validated['agenda_end_date'] : null,
            'tag' => trim($validated['agenda_tag']),
            'description' => trim($validated['agenda_description']),
            'icon' => ! empty($validated['agenda_icon']) ? $validated['agenda_icon'] : 'sparkles',
            'image' => $imagePath,
            'created_by_user_id' => $user?->id,
            'created_by_name' => $creatorName,
            'created_by_role' => $creatorRoleTitle,
            'published_by' => $creatorName.' ('.$creatorRoleTitle.')',
            'created_at' => now()->toIso8601String(),
        ];

        // Insert at beginning
        array_unshift($agendas, $newAgenda);
        SchoolSetting::set('website_agendas_json', json_encode(array_values($agendas)), 'website', 'Daftar Agenda Kegiatan Website');

        AuditLog::log('CREATE_WEBSITE_AGENDA', 'SchoolSetting', null, null, ['id' => $agendaId, 'title' => $newAgenda['title']]);

        return redirect()->route('master.website_content', ['tab' => 'agendas'])
            ->with('success', 'Agenda kegiatan berhasil ditambahkan ke kalender website!');
    }

    /**
     * Update Agenda Sekolah
     */
    public function updateAgenda(Request $request, string $id): RedirectResponse
    {
        $this->authorizeTab('agendas');

        $validated = $request->validate([
            'agenda_title' => ['required', 'string', 'max:200'],
            'agenda_date' => ['required', 'date'],
            'agenda_end_date' => ['nullable', 'date', 'after_or_equal:agenda_date'],
            'agenda_tag' => ['required', 'string', 'max:50'],
            'agenda_description' => ['required', 'string', 'max:1000'],
            'agenda_icon' => ['nullable', 'string', 'max:30'],
            'agenda_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $customAgendas = SchoolSetting::get('website_agendas_json');
        $decodedAgendas = $customAgendas !== null ? json_decode($customAgendas, true) : null;
        $agendas = is_array($decodedAgendas) ? $decodedAgendas : self::DEFAULT_AGENDAS;

        $user = auth()->user();
        $editorName = $user ? $user->name : 'Administrator';
        $editorRole = $user ? ($user->active_role ?: $user->role) : 'admin';
        $editorRoleTitle = $user ? $user->getRoleTitleFor($editorRole) : 'Administrator';

        $found = false;
        foreach ($agendas as $idx => &$item) {
            $itemId = $item['id'] ?? ('agenda_'.$idx);
            if ($itemId === $id || (string) $idx === (string) $id) {
                $item['id'] = $itemId;
                $item['title'] = trim($validated['agenda_title']);
                $item['date'] = $validated['agenda_date'];
                $item['end_date'] = ! empty($validated['agenda_end_date']) ? $validated['agenda_end_date'] : null;
                $item['tag'] = trim($validated['agenda_tag']);
                $item['description'] = trim($validated['agenda_description']);
                $item['updated_by_user_id'] = $user?->id;
                $item['updated_by_name'] = $editorName;
                $item['updated_by_role'] = $editorRoleTitle;
                $item['last_modified_by'] = $editorName.' ('.$editorRoleTitle.')';
                $item['updated_at'] = now()->toIso8601String();

                if (! empty($validated['agenda_icon'])) {
                    $item['icon'] = $validated['agenda_icon'];
                }

                if ($request->hasFile('agenda_image')) {
                    $file = $request->file('agenda_image');
                    $dir = public_path('images/website/agendas');
                    if (! File::isDirectory($dir)) {
                        File::makeDirectory($dir, 0755, true, true);
                    }
                    $filename = $itemId.'_'.time().'.'.($file->getClientOriginalExtension() ?: 'jpg');
                    $file->move($dir, $filename);
                    $item['image'] = 'images/website/agendas/'.$filename.'?v='.time();
                }

                $found = true;
                break;
            }
        }
        unset($item);

        if ($found) {
            SchoolSetting::set('website_agendas_json', json_encode(array_values($agendas)), 'website', 'Daftar Agenda Kegiatan Website');
            AuditLog::log('UPDATE_WEBSITE_AGENDA', 'SchoolSetting', null, null, ['id' => $id]);
        }

        return redirect()->route('master.website_content', ['tab' => 'agendas'])
            ->with('success', 'Agenda kegiatan berhasil diperbarui!');
    }

    /**
     * Hapus Agenda Sekolah
     */
    public function destroyAgenda(string $id): RedirectResponse
    {
        $this->authorizeTab('agendas');
        $customAgendas = SchoolSetting::get('website_agendas_json');
        $decodedAgendas = $customAgendas !== null ? json_decode($customAgendas, true) : null;
        $agendas = is_array($decodedAgendas) ? $decodedAgendas : self::DEFAULT_AGENDAS;

        $filtered = [];
        foreach ($agendas as $idx => $item) {
            $itemId = $item['id'] ?? ('agenda_'.($idx + 1));
            if ((string) $itemId === (string) $id || (string) ($item['id'] ?? '') === (string) $id || (string) $idx === (string) $id || (string) ($idx + 1) === (string) $id) {
                if (! empty($item['image'])) {
                    $cleanPath = explode('?', $item['image'])[0];
                    if (File::exists(public_path($cleanPath))) {
                        File::delete(public_path($cleanPath));
                    }
                }
            } else {
                $filtered[] = $item;
            }
        }

        SchoolSetting::set('website_agendas_json', json_encode(array_values($filtered)), 'website', 'Daftar Agenda Kegiatan Website');
        AuditLog::log('DELETE_WEBSITE_AGENDA', 'SchoolSetting', null, null, ['id' => $id]);

        return redirect()->route('master.website_content', ['tab' => 'agendas'])
            ->with('success', 'Agenda kegiatan berhasil dihapus.');
    }

    /**
     * Tambah Fasilitas Sekolah
     */
    public function storeFacility(Request $request): RedirectResponse
    {
        $this->authorizeTab('facilities');

        $validated = $request->validate([
            'facility_name' => ['required', 'string', 'max:150'],
            'facility_desc' => ['required', 'string', 'max:1000'],
            'facility_icon' => ['nullable', 'string', 'max:30'],
            'facility_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $customFacilities = SchoolSetting::get('website_facilities_json');
        $decodedFacilities = $customFacilities !== null ? json_decode($customFacilities, true) : null;
        $facilities = is_array($decodedFacilities) ? $decodedFacilities : self::DEFAULT_FACILITIES;

        $facilityId = 'facility_'.time().'_'.Str::random(4);
        $imagePath = null;

        if ($request->hasFile('facility_image')) {
            $file = $request->file('facility_image');
            $dir = public_path('images/website/facilities');
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            $filename = $facilityId.'.'.($file->getClientOriginalExtension() ?: 'jpg');
            $file->move($dir, $filename);
            $imagePath = 'images/website/facilities/'.$filename.'?v='.time();
        }

        $user = auth()->user();
        $creatorName = $user ? $user->name : 'Administrator';
        $creatorRole = $user ? ($user->active_role ?: $user->role) : 'admin';
        $creatorRoleTitle = $user ? $user->getRoleTitleFor($creatorRole) : 'Administrator';

        $newFacility = [
            'id' => $facilityId,
            'name' => trim($validated['facility_name']),
            'desc' => trim($validated['facility_desc']),
            'icon' => ! empty($validated['facility_icon']) ? $validated['facility_icon'] : 'building-library',
            'image' => $imagePath,
            'created_by_user_id' => $user?->id,
            'created_by_name' => $creatorName,
            'created_by_role' => $creatorRoleTitle,
            'published_by' => $creatorName.' ('.$creatorRoleTitle.')',
            'created_at' => now()->toIso8601String(),
        ];

        $facilities[] = $newFacility;
        SchoolSetting::set('website_facilities_json', json_encode(array_values($facilities)), 'website', 'Daftar Fasilitas Website');

        AuditLog::log('CREATE_WEBSITE_FACILITY', 'SchoolSetting', null, null, ['id' => $facilityId, 'name' => $newFacility['name']]);

        return redirect()->route('master.website_content', ['tab' => 'facilities'])
            ->with('success', 'Fasilitas baru berhasil ditambahkan!');
    }

    /**
     * Update Fasilitas Sekolah
     */
    public function updateFacility(Request $request, string $id): RedirectResponse
    {
        $this->authorizeTab('facilities');

        $validated = $request->validate([
            'facility_name' => ['required', 'string', 'max:150'],
            'facility_desc' => ['required', 'string', 'max:1000'],
            'facility_icon' => ['nullable', 'string', 'max:30'],
            'facility_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $customFacilities = SchoolSetting::get('website_facilities_json');
        $decodedFacilities = $customFacilities !== null ? json_decode($customFacilities, true) : null;
        $facilities = is_array($decodedFacilities) ? $decodedFacilities : self::DEFAULT_FACILITIES;

        $user = auth()->user();
        $editorName = $user ? $user->name : 'Administrator';
        $editorRole = $user ? ($user->active_role ?: $user->role) : 'admin';
        $editorRoleTitle = $user ? $user->getRoleTitleFor($editorRole) : 'Administrator';

        $found = false;
        foreach ($facilities as $idx => &$item) {
            $itemId = $item['id'] ?? ('facility_'.$idx);
            if ($itemId === $id || (string) $idx === (string) $id) {
                $item['id'] = $itemId;
                $item['name'] = trim($validated['facility_name']);
                $item['desc'] = trim($validated['facility_desc']);
                $item['updated_by_user_id'] = $user?->id;
                $item['updated_by_name'] = $editorName;
                $item['updated_by_role'] = $editorRoleTitle;
                $item['last_modified_by'] = $editorName.' ('.$editorRoleTitle.')';
                $item['updated_at'] = now()->toIso8601String();

                if (! empty($validated['facility_icon'])) {
                    $item['icon'] = $validated['facility_icon'];
                }

                if ($request->hasFile('facility_image')) {
                    $file = $request->file('facility_image');
                    $dir = public_path('images/website/facilities');
                    if (! File::isDirectory($dir)) {
                        File::makeDirectory($dir, 0755, true, true);
                    }
                    $filename = $itemId.'_'.time().'.'.($file->getClientOriginalExtension() ?: 'jpg');
                    $file->move($dir, $filename);
                    $item['image'] = 'images/website/facilities/'.$filename.'?v='.time();
                }

                $found = true;
                break;
            }
        }
        unset($item);

        if ($found) {
            SchoolSetting::set('website_facilities_json', json_encode(array_values($facilities)), 'website', 'Daftar Fasilitas Website');
            AuditLog::log('UPDATE_WEBSITE_FACILITY', 'SchoolSetting', null, null, ['id' => $id]);
        }

        return redirect()->route('master.website_content', ['tab' => 'facilities'])
            ->with('success', 'Data fasilitas berhasil diperbarui!');
    }

    /**
     * Hapus Fasilitas Sekolah
     */
    public function destroyFacility(string $id): RedirectResponse
    {
        $this->authorizeTab('facilities');
        $customFacilities = SchoolSetting::get('website_facilities_json');
        $decodedFacilities = $customFacilities !== null ? json_decode($customFacilities, true) : null;
        $facilities = is_array($decodedFacilities) ? $decodedFacilities : self::DEFAULT_FACILITIES;

        $filtered = [];
        foreach ($facilities as $idx => $item) {
            $itemId = $item['id'] ?? ('facility_'.($idx + 1));
            if ((string) $itemId === (string) $id || (string) ($item['id'] ?? '') === (string) $id || (string) $idx === (string) $id || (string) ($idx + 1) === (string) $id) {
                if (! empty($item['image'])) {
                    $cleanPath = explode('?', $item['image'])[0];
                    if (File::exists(public_path($cleanPath))) {
                        File::delete(public_path($cleanPath));
                    }
                }
            } else {
                $filtered[] = $item;
            }
        }

        SchoolSetting::set('website_facilities_json', json_encode(array_values($filtered)), 'website', 'Daftar Fasilitas Website');
        AuditLog::log('DELETE_WEBSITE_FACILITY', 'SchoolSetting', null, null, ['id' => $id]);

        return redirect()->route('master.website_content', ['tab' => 'facilities'])
            ->with('success', 'Fasilitas berhasil dihapus.');
    }

    /**
     * Tambah Guru atau Staf TU Baru
     */
    public function storeStaff(Request $request): RedirectResponse
    {
        $this->authorizeTab('staff');

        $isGuru = $request->input('category') === 'guru';
        $validated = $request->validate([
            'category' => ['required', 'in:guru,staf_tu'],
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'gender' => ['required', 'in:L,P'],
            'employment_status' => ['nullable', 'string', 'max:50'],
            'mapel' => [$isGuru ? 'required' : 'nullable', 'string', 'max:100'],
            'duty' => ['nullable', 'string', 'max:150'],
            'tu_duty' => [! $isGuru ? 'required' : 'nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $staffMembers = self::getLiveStaff();

        $staffId = ($validated['category'] === 'guru' ? 'teacher_' : 'tu_').time().'_'.Str::random(4);
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $dir = public_path('images/website/staff');
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            $filename = $staffId.'.'.($file->getClientOriginalExtension() ?: 'jpg');
            $file->move($dir, $filename);
            $photoPath = 'images/website/staff/'.$filename;
        }

        $newMember = [
            'id' => $staffId,
            'category' => $validated['category'],
            'name' => trim($validated['name']),
            'nip' => ! empty($validated['nip']) ? trim($validated['nip']) : '-',
            'gender' => $validated['gender'],
            'employment_status' => $validated['employment_status'] ?? ($validated['category'] === 'guru' ? 'PNS / ASN' : 'Staf TU'),
            'mapel' => $validated['category'] === 'guru' ? trim($validated['mapel'] ?? '') : '',
            'duty' => $validated['category'] === 'guru' ? trim($validated['duty'] ?? 'Guru Pengajar') : trim($validated['tu_duty'] ?? 'Staf Tata Usaha'),
            'tu_duty' => $validated['category'] === 'staf_tu' ? trim($validated['tu_duty'] ?? '') : '',
            'photo' => $photoPath,
            'phone' => $validated['phone'] ?? '',
            'order' => (int) ($validated['order'] ?? (count($staffMembers) + 1)),
            'is_active' => $request->boolean('is_active', true),
        ];

        $staffMembers[] = $newMember;
        usort($staffMembers, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        SchoolSetting::set('website_staff_json', json_encode(array_values($staffMembers)), 'website', 'Daftar Guru & Staf TU Website');

        AuditLog::log('CREATE_WEBSITE_STAFF', 'SchoolSetting', null, null, ['id' => $staffId, 'name' => $newMember['name']]);

        $roleLabel = $validated['category'] === 'guru' ? 'Guru' : 'Staf TU';

        return redirect()->route('master.website_content', ['tab' => 'staff'])
            ->with('success', "Data {$roleLabel} '{$newMember['name']}' berhasil ditambahkan ke website!");
    }

    /**
     * Update Guru atau Staf TU
     */
    public function updateStaff(Request $request, string $id): RedirectResponse
    {
        $this->authorizeTab('staff');

        $isGuru = $request->input('category') === 'guru';
        $validated = $request->validate([
            'category' => ['required', 'in:guru,staf_tu'],
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'gender' => ['required', 'in:L,P'],
            'employment_status' => ['nullable', 'string', 'max:50'],
            'mapel' => [$isGuru ? 'required' : 'nullable', 'string', 'max:100'],
            'duty' => ['nullable', 'string', 'max:150'],
            'tu_duty' => [! $isGuru ? 'required' : 'nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $staffMembers = self::getLiveStaff();

        $found = false;
        foreach ($staffMembers as &$member) {
            if ((string) ($member['id'] ?? '') === (string) $id) {
                $member['category'] = $validated['category'];
                $member['name'] = trim($validated['name']);
                $member['nip'] = ! empty($validated['nip']) ? trim($validated['nip']) : '-';
                $member['gender'] = $validated['gender'];
                $member['employment_status'] = $validated['employment_status'] ?? ($validated['category'] === 'guru' ? 'PNS / ASN' : 'Staf TU');
                $member['mapel'] = $validated['category'] === 'guru' ? trim($validated['mapel'] ?? '') : '';
                $member['duty'] = $validated['category'] === 'guru' ? trim($validated['duty'] ?? 'Guru Pengajar') : trim($validated['tu_duty'] ?? 'Staf Tata Usaha');
                $member['tu_duty'] = $validated['category'] === 'staf_tu' ? trim($validated['tu_duty'] ?? '') : '';
                $member['phone'] = $validated['phone'] ?? '';
                $member['order'] = (int) ($validated['order'] ?? ($member['order'] ?? 1));
                $member['is_active'] = $request->boolean('is_active', true);

                if ($request->hasFile('photo')) {
                    $file = $request->file('photo');
                    $dir = public_path('images/website/staff');
                    if (! File::isDirectory($dir)) {
                        File::makeDirectory($dir, 0755, true, true);
                    }
                    $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $id);
                    $filename = $safeId.'_'.time().'.'.($file->getClientOriginalExtension() ?: 'jpg');
                    $file->move($dir, $filename);
                    $member['photo'] = 'images/website/staff/'.$filename;
                }

                $found = true;
                break;
            }
        }
        unset($member);

        if (! $found) {
            $newMember = [
                'id' => $id,
                'category' => $validated['category'],
                'name' => trim($validated['name']),
                'nip' => ! empty($validated['nip']) ? trim($validated['nip']) : '-',
                'gender' => $validated['gender'],
                'employment_status' => $validated['employment_status'] ?? ($validated['category'] === 'guru' ? 'PNS / ASN' : 'Staf TU'),
                'mapel' => $validated['category'] === 'guru' ? trim($validated['mapel'] ?? '') : '',
                'duty' => $validated['category'] === 'guru' ? trim($validated['duty'] ?? 'Guru Pengajar') : trim($validated['tu_duty'] ?? 'Staf Tata Usaha'),
                'tu_duty' => $validated['category'] === 'staf_tu' ? trim($validated['tu_duty'] ?? '') : '',
                'phone' => $validated['phone'] ?? '',
                'photo' => null,
                'order' => (int) ($validated['order'] ?? 99),
                'is_active' => $request->boolean('is_active', true),
            ];
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $dir = public_path('images/website/staff');
                if (! File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true, true);
                }
                $safeId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $id);
                $filename = $safeId.'_'.time().'.'.($file->getClientOriginalExtension() ?: 'jpg');
                $file->move($dir, $filename);
                $newMember['photo'] = 'images/website/staff/'.$filename;
            }
            $staffMembers[] = $newMember;
        }

        usort($staffMembers, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        SchoolSetting::set('website_staff_json', json_encode(array_values($staffMembers)), 'website', 'Daftar Guru & Staf TU Website');
        AuditLog::log('UPDATE_WEBSITE_STAFF', 'SchoolSetting', null, null, ['id' => $id]);

        return redirect()->route('master.website_content', ['tab' => 'staff'])
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    /**
     * Hapus Guru atau Staf TU
     */
    public function destroyStaff(string $id): RedirectResponse
    {
        $this->authorizeTab('staff');

        $staffMembers = self::getLiveStaff();

        $filtered = array_values(array_filter($staffMembers, fn ($item) => (string) ($item['id'] ?? '') !== (string) $id));

        SchoolSetting::set('website_staff_json', json_encode($filtered), 'website', 'Daftar Guru & Staf TU Website');
        AuditLog::log('DELETE_WEBSITE_STAFF', 'SchoolSetting', null, null, ['id' => $id]);

        return redirect()->route('master.website_content', ['tab' => 'staff'])
            ->with('success', 'Data pegawai berhasil dihapus dari website.');
    }

    /**
     * Sinkronkan / Impor Seluruh Data Guru & PTK dari Master Database
     */
    public function syncStaffFromTeachers(): RedirectResponse
    {
        $this->authorizeTab('staff');

        $liveStaff = self::buildInitialStaffFromMaster();
        SchoolSetting::set('website_staff_json', json_encode(array_values($liveStaff)), 'website', 'Daftar Guru & Staf TU Website');
        AuditLog::log('SYNC_WEBSITE_STAFF', 'SchoolSetting', null, null, ['count' => count($liveStaff)]);

        return redirect()->route('master.website_content', ['tab' => 'staff'])
            ->with('success', 'Sinkronisasi berhasil! Seluruh data dewan guru & staf PTK ('.count($liveStaff).' pegawai) telah diperbarui dari Master Data SIS-SMA1LE.');
    }

    /**
     * Reset Bagian ke Data Bawaan Sistem
     */
    public function resetSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section' => ['required', 'in:agendas,facilities,vision,headmaster,hero,news,staff,running_text'],
        ]);

        $section = $validated['section'];

        $tabMap = [
            'agendas' => 'agendas',
            'facilities' => 'facilities',
            'vision' => 'vision',
            'headmaster' => 'headmaster',
            'hero' => 'hero',
            'news' => 'news',
            'staff' => 'staff',
            'running_text' => 'running_text',
        ];
        $this->authorizeTab($tabMap[$section] ?? 'hero');

        switch ($section) {
            case 'running_text':
                SchoolSetting::whereIn('key', ['website_running_text', 'website_running_text_badge', 'website_running_text_speed'])->delete();
                $tab = 'running_text';
                $label = 'Running Text Header';
                break;
            case 'staff':
                SchoolSetting::where('key', 'website_staff_json')->delete();
                $tab = 'staff';
                $label = 'Guru & Staf Tata Usaha';
                break;
            case 'news':
                SchoolSetting::where('key', 'website_news_json')->delete();
                $tab = 'news';
                $label = 'Kabar & Berita Sekolah';
                break;
            case 'agendas':
                SchoolSetting::where('key', 'website_agendas_json')->delete();
                $tab = 'agendas';
                $label = 'Agenda Kegiatan';
                break;
            case 'facilities':
                SchoolSetting::where('key', 'website_facilities_json')->delete();
                $tab = 'facilities';
                $label = 'Fasilitas Sekolah';
                break;
            case 'vision':
                SchoolSetting::whereIn('key', ['website_visi', 'website_visi_sub', 'website_misi_json'])->delete();
                $tab = 'vision';
                $label = 'Visi & Misi';
                break;
            case 'headmaster':
                SchoolSetting::whereIn('key', [
                    'website_headmaster_name',
                    'website_headmaster_title',
                    'website_headmaster_speech_title',
                    'website_headmaster_speech_p1',
                    'website_headmaster_speech_p2',
                    'website_headmaster_speech_extended',
                    'website_headmaster_quote',
                    'website_speeches_json',
                ])->delete();
                $tab = 'headmaster';
                $label = 'Sambutan Kepala Sekolah';
                break;
            case 'hero':
                SchoolSetting::whereIn('key', [
                    'website_hero_headline',
                    'website_hero_subheadline',
                    'website_humas_phone',
                    'website_humas_message',
                ])->delete();
                $tab = 'hero';
                $label = 'Slogan & Hero Banner';
                break;
        }

        AuditLog::log('RESET_WEBSITE_SECTION', 'SchoolSetting', null, null, ['section' => $section]);

        return redirect()->route('master.website_content', ['tab' => $tab])
            ->with('success', "Bagian {$label} berhasil direset ke standar bawaan sistem.");
    }

    /**
     * Helper to safely save uploaded images to public/images/website and update setting
     */
    protected function saveWebsiteImage($uploadedFile, string $filename, string $settingKey): void
    {
        $dir = public_path('images/website');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        // Move uploaded file to directory
        $uploadedFile->move($dir, $filename);

        // Store with version hash query to bust browser caches
        $relativeUrl = 'images/website/'.$filename.'?v='.time();
        SchoolSetting::set($settingKey, $relativeUrl, 'website', 'URL foto website');
    }
}

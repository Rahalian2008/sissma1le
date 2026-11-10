<?php

namespace App\Services;

class AccountCodeService
{
    /**
     * Katalog Resmi Kode Rekening Standar Pendidikan (BOSP & SIPD Kemendagri).
     * Memuat: Kode Rekening, Nama Program (8 Standar Nasional Pendidikan), dan Uraian Nama Kegiatan.
     *
     * @return array<int, array{code: string, program: string, activity: string, category: string, description: string}>
     */
    public static function all(): array
    {
        return [
            // 1. Standar Kompetensi Lulusan (SKL)
            [
                'code' => '5.1.02.01.01.0026',
                'program' => 'Standar Kompetensi Lulusan',
                'activity' => 'Penggandaan Bahan Ujian Sekolah & Asesmen Akhir Jenjang',
                'category' => 'SKL',
                'description' => 'Penyusunan, telaah kisi-kisi, dan penggandaan lembar asesmen akhir satuan pendidikan jenjang SMA.',
            ],
            [
                'code' => '5.1.02.02.01.0031',
                'program' => 'Standar Kompetensi Lulusan',
                'activity' => 'Penyelenggaraan Sertifikasi Kompetensi & Pelatihan Lulusan',
                'category' => 'SKL',
                'description' => 'Pelaksanaan uji kompetensi, penguatan literasi digital, dan pembekalan alumni peserta didik.',
            ],
            [
                'code' => '5.1.02.04.01.0001',
                'program' => 'Standar Kompetensi Lulusan',
                'activity' => 'Transport dan Akomodasi Pembinaan Prestasi Lomba Siswa (OSN/O2SN/FLS2N)',
                'category' => 'SKL',
                'description' => 'Biaya kontingen dan pendampingan peserta didik mewakili sekolah pada kompetisi tingkat kabupaten/provinsi.',
            ],

            // 2. Standar Isi
            [
                'code' => '5.1.02.01.01.0024',
                'program' => 'Standar Isi',
                'activity' => 'Penyusunan Kurikulum Operasional Satuan Pendidikan (KOSP)',
                'category' => 'Standar Isi',
                'description' => 'Pengembangan dokumen kurikulum satuan pendidikan berbasis Kurikulum Merdeka dan capaian pembelajaran.',
            ],
            [
                'code' => '5.1.02.01.01.0025',
                'program' => 'Standar Isi',
                'activity' => 'Penggandaan Perangkat Ajar, Modul, dan Lembar Kerja Siswa (LKS)',
                'category' => 'Standar Isi',
                'description' => 'Penggandaan modul ajar berdiferensiasi dan instrumen materi belajar pendukung.',
            ],
            [
                'code' => '5.1.02.02.01.0031',
                'program' => 'Standar Isi',
                'activity' => 'Konsumsi Rapat Review dan Finalisasi KOSP Kurikulum Merdeka',
                'category' => 'Standar Isi',
                'description' => 'Penyediaan makan minum rapat peninjauan kurikulum bersama pengawas pembina dan komite sekolah.',
            ],

            // 3. Standar Proses
            [
                'code' => '5.1.02.01.01.0027',
                'program' => 'Standar Proses',
                'activity' => 'Belanja Bahan Habis Pakai Praktik Laboratorium IPA (Kimia, Fisika, Biologi)',
                'category' => 'Standar Proses',
                'description' => 'Pengadaan bahan kimia, preparat mikroskop, reagen uji coba, dan alat praktikum laboratorium sains.',
            ],
            [
                'code' => '5.1.02.01.01.0028',
                'program' => 'Standar Proses',
                'activity' => 'Belanja Bahan Praktik Komputer, Multimedia, dan Informatika',
                'category' => 'Standar Proses',
                'description' => 'Penyediaan kabel LAN, konektor RJ45, flashdisk instruktur, dan perlengkapan lab komputer.',
            ],
            [
                'code' => '5.1.02.02.01.0035',
                'program' => 'Standar Proses',
                'activity' => 'Penyelenggaraan Kegiatan Ekstrakurikuler Siswa (Pramuka, PMR, Paskibra, Seni, Olahraga)',
                'category' => 'Standar Proses',
                'description' => 'Honor pelatih ekstrakurikuler luar, pendaftaran event, dan peralatan latihan ekskul.',
            ],
            [
                'code' => '5.1.02.04.01.0001',
                'program' => 'Standar Proses',
                'activity' => 'Kegiatan Pembelajaran Luar Kelas (Outdoor Learning) & Projek P5',
                'category' => 'Standar Proses',
                'description' => 'Pelaksanaan Projek Penguatan Profil Pelajar Pancasila (P5) dan kunjungan edukasi kontekstual.',
            ],

            // 4. Standar Penilaian Pendidikan
            [
                'code' => '5.1.02.01.01.0026',
                'program' => 'Standar Penilaian Pendidikan',
                'activity' => 'Penggandaan Naskah Asesmen Sumatif Tengah Semester & Akhir Semester (ASTS/ASAS)',
                'category' => 'Standar Penilaian',
                'description' => 'Pencetakan lembar soal, master penilaian, dan berkas analisis evaluasi belajar.',
            ],
            [
                'code' => '5.1.02.02.01.0014',
                'program' => 'Standar Penilaian Pendidikan',
                'activity' => 'Penyelenggaraan Asesmen Nasional Berbasis Komputer (ANBK) & Sulingjar',
                'category' => 'Standar Penilaian',
                'description' => 'Sinkronisasi VHD, gladi bersih, kuota internet backup, dan pelaksanaan tes ANBK resmi Kemendikbud.',
            ],
            [
                'code' => '5.1.02.02.01.0026',
                'program' => 'Standar Penilaian Pendidikan',
                'activity' => 'Honorarium Proktor dan Teknisi Asesmen Berbasis Komputer (ANBK)',
                'category' => 'Standar Penilaian',
                'description' => 'Pemberian honor penugasan proktor ruang dan teknisi jaringan selama pelaksanaan simulasi dan tes.',
            ],

            // 5. Standar Pendidik dan Tenaga Kependidikan
            [
                'code' => '5.1.02.02.01.0026',
                'program' => 'Standar Pendidik dan Tenaga Kependidikan',
                'activity' => 'Honorarium Guru Tidak Tetap (GTT) / Pendidik Non-ASN',
                'category' => 'Standar PTK',
                'description' => 'Pembayaran honor bulanan guru honorer yang terdaftar pada Dapodik sesuai ketentuan Juknis BOSP.',
            ],
            [
                'code' => '5.1.02.02.01.0027',
                'program' => 'Standar Pendidik dan Tenaga Kependidikan',
                'activity' => 'Honorarium Tenaga Kependidikan (PTT) / Tata Usaha Non-ASN',
                'category' => 'Standar PTK',
                'description' => 'Pembayaran honor bulanan staf administrasi TU, pustakawan, dan laboran sekolah.',
            ],
            [
                'code' => '5.1.02.02.01.0032',
                'program' => 'Standar Pendidik dan Tenaga Kependidikan',
                'activity' => 'In-House Training (IHT) & Pelatihan Peningkatan Kompetensi Guru',
                'category' => 'Standar PTK',
                'description' => 'Narasumber ahli, sertifikat pelatihan, dan materi pendampingan implementasi Kurikulum Merdeka.',
            ],
            [
                'code' => '5.1.02.04.01.0001',
                'program' => 'Standar Pendidik dan Tenaga Kependidikan',
                'activity' => 'Transport dan Kegiatan Musyawarah Guru Mata Pelajaran (MGMP)',
                'category' => 'Standar PTK',
                'description' => 'Dukungan partisipasi pendidik dalam kegiatan forum MGMP tingkat Kabupaten Sukabumi / Wilayah V.',
            ],

            // 6. Standar Sarana dan Prasarana
            [
                'code' => '5.1.02.01.01.0029',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Pengadaan Perlengkapan Kebersihan Lingkungan & Sanitasi Sekolah',
                'category' => 'Standar Sarpras',
                'description' => 'Sapu, alat pel, tempat sampah terpilah, cairan disinfektan, sabun cuci tangan, dan pembersih toilet.',
            ],
            [
                'code' => '5.1.02.01.01.0030',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Pengadaan Obat-obatan dan Perlengkapan Medis Unit Kesehatan Sekolah (UKS)',
                'category' => 'Standar Sarpras',
                'description' => 'Obat pertolongan pertama (P3K), perban, antiseptik, plester, tabung oksigen kecil, dan tensimeter.',
            ],
            [
                'code' => '5.1.02.02.01.0013',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Pemeliharaan dan Perbaikan Ringan Bangunan Gedung / Ruang Belajar',
                'category' => 'Standar Sarpras',
                'description' => 'Pengecatan kusen, perbaikan genteng bocor, penggantian kunci pintu kelas, dan instalasi listrik ringan.',
            ],
            [
                'code' => '5.1.02.02.01.0014',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Pemeliharaan Perangkat Komputer, Access Point, dan Server Sekolah',
                'category' => 'Standar Sarpras',
                'description' => 'Service printer, instalasi ulang PC lab, pembersihan router, dan penggantian komponen hardware rusak.',
            ],
            [
                'code' => '5.2.02.05.01.0005',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Belanja Modal Pengadaan Laptop / Komputer PC Pembelajaran',
                'category' => 'Standar Sarpras',
                'description' => 'Pengadaan komputer desktop atau laptop Chromebook inventaris sarana penunjang pembelajaran.',
            ],
            [
                'code' => '5.2.02.05.01.0007',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Belanja Modal Pengadaan Proyektor LCD & Layar Gantung',
                'category' => 'Standar Sarpras',
                'description' => 'Alat presentasi multimedia untuk ruang kelas interaktif dan laboratorium multimedia.',
            ],
            [
                'code' => '5.2.02.08.01.0001',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Belanja Modal Pengadaan Meja dan Kursi Siswa (Perabot Kelas)',
                'category' => 'Standar Sarpras',
                'description' => 'Penggantian dan penambahan set meja kursi siswa di ruang kelas pembelajaran.',
            ],
            [
                'code' => '5.2.05.01.01.0001',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Belanja Modal Pengadaan Buku Teks Pelajaran Kurikulum Merdeka',
                'category' => 'Standar Sarpras',
                'description' => 'Buku teks utama siswa dan buku panduan guru terbitan resmi Kemendikbudristek untuk perpustakaan.',
            ],
            [
                'code' => '5.2.02.05.01.0005',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Belanja Modal Komputer, Laptop, Chromebook, dan Server UNBK/ANBK',
                'category' => 'Standar Sarpras',
                'description' => 'Pengadaan aset modal TIK sekolah untuk mendukung digitalisasi pembelajaran dan laboratorium komputer.',
            ],
            [
                'code' => '5.2.02.08.01.0003',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Belanja Modal LCD Proyektor dan Sound System Audio Ruang Kelas',
                'category' => 'Standar Sarpras',
                'description' => 'Pengadaan perangkat proyektor multimedia dan sistem audio pembelajaran interaktif kelas.',
            ],
            [
                'code' => '5.2.02.10.01.0002',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Belanja Modal Meja dan Kursi Belajar Siswa serta Guru',
                'category' => 'Standar Sarpras',
                'description' => 'Pengadaan perabot mebeler ruang kelas baru atau peremajaan mebeler yang rusak berat.',
            ],
            [
                'code' => '5.1.02.03.02.0035',
                'program' => 'Standar Sarana dan Prasarana',
                'activity' => 'Pemeliharaan Rutin / Berkala Gedung Sekolah, Ruang Kelas, dan Toilet Sanitasi',
                'category' => 'Standar Sarpras',
                'description' => 'Pengecatan dinding kelas, perbaikan plafon, perbaikan instalasi pipa air kotor dan bersih, serta sanitasi siswa.',
            ],

            // 7. Standar Pengelolaan
            [
                'code' => '5.1.02.01.01.0024',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Pengadaan Alat Tulis Kantor (ATK) dan Perlengkapan Administrasi Sekolah',
                'category' => 'Standar Pengelolaan',
                'description' => 'Buku folio, spidol whiteboard, map arsip, ordner, staples, dan perlengkapan tulis TU.',
            ],
            [
                'code' => '5.1.02.01.01.0025',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Pengadaan Kertas HVS dan Tinta / Toner Printer Kantor',
                'category' => 'Standar Pengelolaan',
                'description' => 'Kertas A4/F4 75-80 gsm dan isi ulang tinta printer cetak dokumen persuratan dan laporan.',
            ],
            [
                'code' => '5.1.02.02.01.0003',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Langganan Daya dan Jasa Akses Internet (Bandwidth Dedicated)',
                'category' => 'Standar Pengelolaan',
                'description' => 'Pembayaran tagihan bulanan ISP internet sekolah untuk mendukung KBM online dan pelaporan Dapodik.',
            ],
            [
                'code' => '5.1.02.02.01.0004',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Pembayaran Tagihan Rekening Listrik (PLN) Satuan Pendidikan',
                'category' => 'Standar Pengelolaan',
                'description' => 'Pembayaran operasional listrik pascabayar/prabayar gedung sekolah.',
            ],
            [
                'code' => '5.1.02.02.01.0005',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Pembayaran Tagihan Rekening Air Bersih (PDAM)',
                'category' => 'Standar Pengelolaan',
                'description' => 'Pembayaran operasional air bersih untuk sanitasi siswa dan kantor.',
            ],
            [
                'code' => '5.1.02.02.01.0031',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Konsumsi Rapat Kedinasan Dewan Guru dan Pertemuan Komite Sekolah',
                'category' => 'Standar Pengelolaan',
                'description' => 'Snack dan makan rapat pembinaan rutin dinas, rapat penetapan RKAS, dan rapat kelulusan.',
            ],
            [
                'code' => '5.1.02.02.01.0040',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Pengembangan Sistem Informasi Manajemen Sekolah, Domain & Hosting Web',
                'category' => 'Standar Pengelolaan',
                'description' => 'Sewa hosting, registrasi domain .sch.id, dan pemeliharaan server aplikasi sekolah SIKATAS.',
            ],
            [
                'code' => '5.1.02.01.01.0029',
                'program' => 'Standar Pengelolaan',
                'activity' => 'Penyelenggaraan PPDB (Penerimaan Peserta Didik Baru)',
                'category' => 'Standar Pengelolaan',
                'description' => 'Publikasi informasi PPDB, banner sosialisasi, formulir pendaftaran, dan verifikasi berkas PPDB.',
            ],

            // 8. Standar Pembiayaan
            [
                'code' => '5.1.02.02.01.0028',
                'program' => 'Standar Pembiayaan',
                'activity' => 'Honorarium Petugas Keamanan (Satpam) dan Tenaga Kebersihan Sekolah',
                'category' => 'Standar Pembiayaan',
                'description' => 'Kompensasi bulanan tenaga penjaga malam sekolah dan petugas kebersihan lingkungan.',
            ],
            [
                'code' => '5.1.02.02.01.0030',
                'program' => 'Standar Pembiayaan',
                'activity' => 'Biaya Administrasi Transaksi Perbankan dan Pembukuan Kas Sekolah',
                'category' => 'Standar Pembiayaan',
                'description' => 'Biaya administrasi rekening giro bank, kliring, dan materai pengesahan SPJ BOSP.',
            ],
            [
                'code' => '5.1.02.02.01.0060',
                'program' => 'Standar Pembiayaan',
                'activity' => 'Penyusunan Laporan Pertanggungjawaban (SPJ) BOSP dan Pembukuan BKU',
                'category' => 'Standar Pembiayaan',
                'description' => 'Penggandaan dokumen bukti kuitansi, jilid berkas SPJ, dan sinkronisasi ARKAS Kemendikbud.',
            ],
        ];
    }

    /**
     * Cari Kode Rekening berdasarkan kata kunci atau kategori SNP.
     *
     * @return array<int, array{code: string, program: string, activity: string, category: string, description: string}>
     */
    public static function search(?string $keyword = null, ?string $category = null): array
    {
        $all = self::all();

        return array_values(array_filter($all, function ($item) use ($keyword, $category) {
            if ($category && $category !== 'ALL' && $item['category'] !== $category) {
                return false;
            }

            if ($keyword) {
                $kw = strtolower(trim($keyword));
                $haystack = strtolower($item['code'].' '.$item['program'].' '.$item['activity'].' '.$item['description'].' '.$item['category']);

                return str_contains($haystack, $kw);
            }

            return true;
        }));
    }

    /**
     * Daftar Kategori 8 Standar Nasional Pendidikan.
     *
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'SKL' => '1. Standar Kompetensi Lulusan',
            'Standar Isi' => '2. Standar Isi',
            'Standar Proses' => '3. Standar Proses',
            'Standar Penilaian' => '4. Standar Penilaian',
            'Standar PTK' => '5. Standar Pendidik & Tenaga Kependidikan',
            'Standar Sarpras' => '6. Standar Sarana & Prasarana',
            'Standar Pengelolaan' => '7. Standar Pengelolaan',
            'Standar Pembiayaan' => '8. Standar Pembiayaan',
        ];
    }
}

<?php

namespace App\Services;

class HigherEducationService
{
    /**
     * Daftar Perguruan Tinggi Terakreditasi di Seluruh Pulau Jawa
     * (PTN, PTS Terkemuka, Politeknik Negeri, PTKIN/UIN, dan Kedinasan)
     * Lengkap dengan rumpun wilayah, tipe, akronim, dan daftar jurusan/program studi.
     *
     * @return array<int, array{name: string, short_name: string, province: string, type: string, majors: array<int, string>}>
     */
    public static function getJavaUniversities(): array
    {
        return [
            // ==========================================
            // JAWA BARAT
            // ==========================================
            [
                'name' => 'Institut Teknologi Bandung (ITB)',
                'short_name' => 'ITB',
                'province' => 'Jawa Barat',
                'type' => 'PTN',
                'majors' => [
                    'Teknik Informatika (STEI)', 'Sistem & Teknologi Informasi (STEI)', 'Teknik Elektro', 'Teknik Biomedis',
                    'Teknik Mesin', 'Teknik Dirgantara / Penerbangan', 'Teknik Material', 'Teknik Kimia', 'Teknik Fisika',
                    'Teknik Industri', 'Manajemen Rekayasa Industri', 'Teknik Sipil', 'Teknik Lingkungan', 'Perencanaan Wilayah & Kota (PWK)',
                    'Arsitektur', 'Desain Komunikasi Visual (DKV)', 'Desain Interior', 'Desain Produk', 'Seni Rupa',
                    'Farmasi Klinik & Komunitas', 'Sains & Teknologi Farmasi', 'Biologi', 'Mikrobiologi', 'Rekayasa Hayati',
                    'Rekayasa Pertanian', 'Rekayasa Kehutanan', 'Astronomi', 'Fisika', 'Kimia', 'Matematika', 'Aktuaria',
                    'Manajemen (SBM ITB)', 'Kewirausahaan (SBM ITB)', 'Teknik Geologi', 'Teknik Geodesi & Geomatika',
                    'Teknik Perminyakan', 'Teknik Pertambangan', 'Teknik Metalurgi', 'Meteorologi', 'Oseanografi',
                ],
            ],
            [
                'name' => 'Universitas Padjadjaran (Unpad)',
                'short_name' => 'Unpad',
                'province' => 'Jawa Barat',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Kedokteran Gigi', 'Farmasi', 'Keperawatan', 'Kebidanan', 'Psikologi',
                    'Teknik Informatika', 'Statistika', 'Matematika', 'Biologi', 'Kimia', 'Fisika', 'Geofisika', 'Teknik Elektro',
                    'Teknik Geologi', 'Agroteknologi', 'Agribisnis', 'Teknologi Pangan', 'Teknik Pertanian', 'Peternakan',
                    'Perikanan', 'Ilmu Kelautan', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Bisnis Digital', 'Ekonomi Islam',
                    'Ilmu Hukum', 'Ilmu Hubungan Internasional (HI)', 'Ilmu Komunikasi', 'Jurnalistik', 'Hubungan Masyarakat (Humas)',
                    'Manajemen Komunikasi', 'Televisi & Film', 'Ilmu Administrasi Publik', 'Ilmu Administrasi Bisnis',
                    'Ilmu Pemerintahan', 'Kesejahteraan Sosial', 'Sosiologi', 'Antropologi', 'Sastra Indonesia', 'Sastra Inggris',
                    'Sastra Jepang', 'Sastra Jerman', 'Sastra Prancis', 'Sastra Arab', 'Sastra Rusia', 'Sastra Sunda', 'Ilmu Sejarah',
                ],
            ],
            [
                'name' => 'Universitas Pendidikan Indonesia (UPI)',
                'short_name' => 'UPI Bandung',
                'province' => 'Jawa Barat',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Ilmu Komputer', 'Ilmu Komputer', 'Sistem Informasi Kelautan', 'Teknik Elektro',
                    'Pendidikan Teknik Elektro', 'Teknik Sipil', 'Pendidikan Teknik Bangunan', 'Arsitektur',
                    'Pendidikan Teknik Mesin', 'Teknik Otomasi Industri', 'Desain Komunikasi Visual (DKV)', 'Desain Produk Industri',
                    'Pendidikan Matematika', 'Matematika', 'Pendidikan Biologi', 'Biologi', 'Pendidikan Kimia', 'Kimia',
                    'Pendidikan Fisika', 'Fisika', 'Pendidikan IPA', 'Pendidikan Bahasa & Sastra Indonesia', 'Pendidikan Bahasa Inggris',
                    'Pendidikan Bahasa Jepang', 'Pendidikan Bahasa Korea', 'Pendidikan Bahasa Jerman', 'Pendidikan Bahasa Prancis',
                    'Pendidikan Bahasa Arab', 'Pendidikan Guru Sekolah Dasar (PGSD)', 'Pendidikan Guru PAUD (PGPAUD)',
                    'Bimbingan dan Konseling (BK)', 'Psikologi', 'Manajemen', 'Akuntansi', 'Ilmu Ekonomi & Keuangan Islam',
                    'Pendidikan Ekonomi', 'Pendidikan Bisnis', 'Ilmu Komunikasi', 'Manajemen Pemasaran Pariwisata',
                    'Manajemen Resort & Leisure', 'Ilmu Keolahragaan', 'Pendidikan Kepelatihan Olahraga', 'Pendidikan Jasmani (PJKR)',
                    'Pendidikan Seni Rupa', 'Pendidikan Seni Musik', 'Pendidikan Seni Tari',
                ],
            ],
            [
                'name' => 'IPB University (Institut Pertanian Bogor)',
                'short_name' => 'IPB',
                'province' => 'Jawa Barat',
                'type' => 'PTN',
                'majors' => [
                    'Ilmu Komputer', 'Sains Data', 'Statistika & Sains Data', 'Aktuaria', 'Matematika', 'Fisika', 'Biokimia', 'Biologi', 'Kimia',
                    'Pendidikan Dokter (Kedokteran)', 'Kedokteran Hewan', 'Teknologi Pangan', 'Teknologi Industri Pertanian (TIN)',
                    'Teknik Sipil & Lingkungan', 'Teknik Mesin & Biosistem', 'Teknik Pertanian & Biosistem', 'Agronomi & Hortikultura',
                    'Proteksi Tanaman', 'Arsitektur Lanskap', 'Ilmu Gizi', 'Manajemen Sumberdaya Keluarga', 'Manajemen', 'Bisnis',
                    'Agribisnis', 'Ekonomi Pembangunan', 'Ekonomi Sumberdaya & Lingkungan', 'Ekonomi Syariah',
                    'Komunikasi & Pengembangan Masyarakat (KPM)', 'Teknologi Hasil Perairan', 'Manajemen Sumberdaya Perairan',
                    'Budidaya Perairan (Akuakultur)', 'Ilmu & Teknologi Kelautan', 'Teknologi Produksi Ternak', 'Nutrisi & Teknologi Pakan',
                    'Silvikultur', 'Manajemen Hutan', 'Konservasi Sumberdaya Hutan & Ekowisata', 'Teknologi Hasil Hutan',
                ],
            ],
            [
                'name' => 'UIN Sunan Gunung Djati Bandung (UIN SGD)',
                'short_name' => 'UIN SGD',
                'province' => 'Jawa Barat',
                'type' => 'PTKIN',
                'majors' => [
                    'Teknik Informatika', 'Teknik Elektro', 'Matematika', 'Biologi', 'Kimia', 'Fisika', 'Agroteknologi',
                    'Ilmu Hukum', 'Hukum Ekonomi Syariah (Muamalah)', 'Hukum Tata Negara (Siyasah)', 'Hukum Keluarga Islam (Ahwal Syakhshiyyah)',
                    'Ilmu Komunikasi (Jurnalistik)', 'Ilmu Komunikasi (Humas)', 'Komunikasi Penyiaran Islam (KPI)', 'Manajemen',
                    'Akuntansi Syariah', 'Ekonomi Syariah', 'Psikologi', 'Bimbingan dan Konseling Islam (BKI)',
                    'Pendidikan Agama Islam (PAI)', 'Pendidikan Bahasa Arab', 'Pendidikan Bahasa Inggris', 'Manajemen Pendidikan Islam',
                ],
            ],
            [
                'name' => 'Universitas Siliwangi (Unsil Tasikmalaya)',
                'short_name' => 'Unsil',
                'province' => 'Jawa Barat',
                'type' => 'PTN',
                'majors' => [
                    'Informatika', 'Sistem Informasi', 'Teknik Sipil', 'Teknik Elektro', 'Agroteknologi', 'Agribisnis',
                    'Pendidikan Matematika', 'Pendidikan Biologi', 'Pendidikan Fisika', 'Pendidikan Bahasa Indonesia',
                    'Pendidikan Bahasa Inggris', 'Pendidikan Geografi', 'Pendidikan Sejarah', 'Pendidikan Ekonomi',
                    'Pendidikan Jasmani (Penjas)', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Kesehatan Masyarakat', 'Ilmu Gizi',
                ],
            ],
            [
                'name' => 'Universitas Singaperbangsa Karawang (Unsika)',
                'short_name' => 'Unsika',
                'province' => 'Jawa Barat',
                'type' => 'PTN',
                'majors' => [
                    'Informatika', 'Sistem Informasi', 'Teknik Industri', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Kimia', 'Teknik Lingkungan',
                    'Agroteknologi', 'Agribisnis', 'Ilmu Hukum', 'Manajemen', 'Akuntansi', 'Ilmu Komunikasi', 'Hubungan Internasional',
                    'Pendidikan Matematika', 'Pendidikan Bahasa Inggris', 'Pendidikan Jasmani Kesehatan & Rekreasi', 'Farmasi', 'Ilmu Gizi',
                ],
            ],
            [
                'name' => 'Politeknik Negeri Bandung (Polban)',
                'short_name' => 'Polban',
                'province' => 'Jawa Barat',
                'type' => 'Politeknik',
                'majors' => [
                    'D4 Teknik Informatika', 'D4 Rekayasa Perangkat Lunak', 'D3 Teknik Informatika', 'D4 Teknik Telekomunikasi',
                    'D4 Teknik Elektronika', 'D4 Teknik Otomasi Industri', 'D4 Teknik Perancangan Jalan & Jembatan', 'D4 Manajemen Konstruksi',
                    'D4 Teknik Refrigerasi & Tata Udara', 'D4 Teknik Konservasi Energi', 'D4 Manajemen Pemasaran', 'D4 Administrasi Bisnis',
                    'D4 Akuntansi Manajemen Pemerintahan', 'D4 Keuangan Syariah', 'D4 Manajemen Aset', 'D3 Bahasa Inggris',
                ],
            ],
            [
                'name' => 'Politeknik Manufaktur Bandung (Polman)',
                'short_name' => 'Polman Bandung',
                'province' => 'Jawa Barat',
                'type' => 'Politeknik',
                'majors' => [
                    'D4 Rekayasa Perancangan Mekanik', 'D4 Rekayasa Manufaktur', 'D4 Otomasi Manufaktur & Mekatronika',
                    'D4 Teknologi Rekayasa Pengecoran Logam', 'D3 Pemeliharaan Mesin', 'D3 Teknik Pola Cor',
                ],
            ],
            [
                'name' => 'Telkom University (Bandung)',
                'short_name' => 'Tel-U',
                'province' => 'Jawa Barat',
                'type' => 'PTS',
                'majors' => [
                    'Informatika', 'Software Engineering (Rekayasa Perangkat Lunak)', 'Teknologi Informasi', 'Data Science (Sains Data)',
                    'Sistem Informasi', 'Teknik Komputer', 'Teknik Telekomunikasi', 'Teknik Elektro', 'Smart Science & Technology',
                    'Teknik Biomedis', 'Teknik Industri', 'Sistem Informasi Industri', 'Manajemen Bisnis Telekomunikasi & Informatika (MBTI)',
                    'Akuntansi', 'Digital Public Relations', 'Ilmu Komunikasi', 'Desain Komunikasi Visual (DKV)', 'Desain Interior', 'Desain Produk',
                ],
            ],
            [
                'name' => 'Universitas Katolik Parahyangan (Unpar Bandung)',
                'short_name' => 'Unpar',
                'province' => 'Jawa Barat',
                'type' => 'PTS',
                'majors' => [
                    'Teknik Sipil', 'Arsitektur', 'Teknik Industri', 'Teknik Kimia', 'Informatika', 'Data Science',
                    'Ilmu Hukum', 'Ilmu Administrasi Publik', 'Ilmu Administrasi Bisnis', 'Hubungan Internasional (HI)',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Filsafat',
                ],
            ],
            [
                'name' => 'Universitas Pasundan (Unpas Bandung)',
                'short_name' => 'Unpas',
                'province' => 'Jawa Barat',
                'type' => 'PTS',
                'majors' => [
                    'Teknik Informatika', 'Teknik Industri', 'Teknologi Pangan', 'Teknik Mesin', 'Teknik Lingkungan', 'Perencanaan Wilayah & Kota',
                    'Kedokteran', 'Ilmu Komunikasi', 'Hubungan Internasional', 'Ilmu Administrasi Publik', 'Ilmu Administrasi Bisnis',
                    'Kesejahteraan Sosial', 'Ilmu Hukum', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'PGSD', 'Pendidikan Biologi',
                ],
            ],
            [
                'name' => 'Universitas Islam Bandung (Unisba)',
                'short_name' => 'Unisba',
                'province' => 'Jawa Barat',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Farmasi', 'Psikologi', 'Teknik Industri', 'Teknik Pertambangan', 'Perencanaan Wilayah & Kota',
                    'Ilmu Komunikasi (Jurnalistik / Humas / Manajemen Komunikasi)', 'Ilmu Hukum', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ekonomi Syariah',
                ],
            ],
            [
                'name' => 'Universitas Widyatama (Bandung)',
                'short_name' => 'Widyatama',
                'province' => 'Jawa Barat',
                'type' => 'PTS',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Industri', 'Teknik Sipil', 'Manajemen', 'Akuntansi',
                    'Desain Grafis / DKV', 'Film & Animasi', 'Bahasa Inggris', 'Bahasa Jepang', 'Perdagangan Internasional',
                ],
            ],
            [
                'name' => 'Universitas Jenderal Achmad Yani (Unjani Cimahi)',
                'short_name' => 'Unjani',
                'province' => 'Jawa Barat',
                'type' => 'PTS',
                'majors' => [
                    'Kedokteran Umum', 'Kedokteran Gigi', 'Farmasi', 'Teknologi Laboratorium Medis', 'Teknik Informatika',
                    'Teknik Elektro', 'Teknik Sipil', 'Teknik Mesin', 'Teknik Industri', 'Teknik Kimia', 'Hubungan Internasional',
                    'Ilmu Pemerintahan', 'Ilmu Hukum', 'Manajemen', 'Akuntansi', 'Psikologi',
                ],
            ],
            [
                'name' => 'Universitas Pakuan (Unpak Bogor)',
                'short_name' => 'Unpak',
                'province' => 'Jawa Barat',
                'type' => 'PTS',
                'majors' => [
                    'Ilmu Komputer', 'Teknik Geodesi', 'Teknik Geologi', 'Teknik Sipil', 'Teknik Elektro', 'Farmasi', 'Biologi', 'Kimia', 'Matematika',
                    'Ilmu Hukum', 'Ekonomi Manajemen', 'Akuntansi', 'Ilmu Komunikasi', 'PGSD', 'Pendidikan Bahasa Inggris',
                ],
            ],

            // ==========================================
            // DKI JAKARTA
            // ==========================================
            [
                'name' => 'Universitas Indonesia (UI Depok / Salemba)',
                'short_name' => 'UI',
                'province' => 'DKI Jakarta',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Farmasi', 'Ilmu Keperawatan', 'Ilmu Gizi', 'Kesehatan Masyarakat',
                    'Kesehatan Lingkungan', 'Keselamatan & Kesehatan Kerja (K3)', 'Psikologi', 'Ilmu Komputer', 'Sistem Informasi',
                    'Teknik Elektro', 'Teknik Komputer', 'Teknik Biomedis', 'Teknik Mesin', 'Teknik Perkapalan', 'Teknik Industri',
                    'Teknik Kimia', 'Teknik Bioproses', 'Teknik Sipil', 'Teknik Lingkungan', 'Teknik Metalurgi & Material', 'Arsitektur',
                    'Arsitektur Interior', 'Matematika', 'Statistika', 'Aktuaria', 'Fisika', 'Kimia', 'Biologi', 'Geofisika', 'Geologi', 'Geografi',
                    'Ilmu Hukum', 'Manajemen', 'Akuntansi', 'Ilmu Ekonomi', 'Bisnis Islam', 'Ilmu Komunikasi', 'Ilmu Hubungan Internasional (HI)',
                    'Ilmu Politik', 'Ilmu Administrasi Negara', 'Ilmu Administrasi Niaga', 'Ilmu Administrasi Fiskal', 'Sosiologi',
                    'Kriminologi', 'Antropologi Sosial', 'Kesejahteraan Sosial', 'Sastra Indonesia', 'Sastra Inggris', 'Sastra Jepang',
                    'Sastra Jerman', 'Sastra Prancis', 'Sastra Arab', 'Sastra Cina', 'Sastra Rusia', 'Ilmu Sejarah', 'Arkeologi', 'Ilmu Perpustakaan',
                ],
            ],
            [
                'name' => 'Universitas Negeri Jakarta (UNJ)',
                'short_name' => 'UNJ',
                'province' => 'DKI Jakarta',
                'type' => 'PTN',
                'majors' => [
                    'Ilmu Komputer', 'Sistem & Teknologi Informasi', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil',
                    'Pendidikan Matematika', 'Matematika', 'Pendidikan Fisika', 'Fisika', 'Pendidikan Kimia', 'Kimia',
                    'Pendidikan Biologi', 'Biologi', 'Pendidikan Guru Sekolah Dasar (PGSD)', 'Pendidikan Guru PAUD (PGPAUD)',
                    'Bimbingan dan Konseling (BK)', 'Psikologi', 'Pendidikan Khusus (PLB)', 'Pendidikan Bahasa & Sastra Indonesia',
                    'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa Arab', 'Pendidikan Bahasa Prancis', 'Pendidikan Bahasa Jerman',
                    'Manajemen', 'Akuntansi', 'Bisnis Digital', 'Ilmu Komunikasi', 'Pendidikan Sosiologi', 'Pendidikan Sejarah',
                    'Pendidikan Geografi', 'Pendidikan Pancasila & Kewarganegaraan (PPKn)', 'Ilmu Keolahragaan', 'Pendidikan Kepelatihan Olahraga',
                    'Pendidikan Jasmani (Penjas)', 'Seni Rupa', 'Pendidikan Seni Tari', 'Pendidikan Seni Musik', 'Tata Boga', 'Tata Busana', 'Tata Rias',
                ],
            ],
            [
                'name' => 'UPN "Veteran" Jakarta',
                'short_name' => 'UPNVJ',
                'province' => 'DKI Jakarta',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Farmasi', 'Ilmu Keperawatan', 'Ilmu Gizi', 'Kesehatan Masyarakat', 'Fisioterapi',
                    'Informatika', 'Sistem Informasi', 'Sains Data', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Industri', 'Teknik Perkapalan',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ekonomi Syariah', 'Ilmu Komunikasi', 'Hubungan Internasional (HI)',
                    'Ilmu Politik', 'Sains Informasi', 'Ilmu Hukum',
                ],
            ],
            [
                'name' => 'UIN Syarif Hidayatullah Jakarta',
                'short_name' => 'UIN Jakarta',
                'province' => 'DKI Jakarta',
                'type' => 'PTKIN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Farmasi', 'Ilmu Keperawatan', 'Kesehatan Masyarakat',
                    'Teknik Informatika', 'Sistem Informasi', 'Agribisnis', 'Matematika', 'Kimia', 'Fisika', 'Biologi',
                    'Akuntansi', 'Manajemen', 'Ekonomi Pembangunan', 'Perbankan Syariah', 'Ilmu Hukum', 'Hukum Keluarga (Ahwal Syakhshiyyah)',
                    'Hukum Ekonomi Syariah', 'Ilmu Hubungan Internasional', 'Ilmu Politik', 'Jurnalistik', 'Komunikasi Penyiaran Islam (KPI)',
                    'Psikologi', 'Sosiologi', 'Bimbingan dan Penyuluhan Islam', 'Pendidikan Agama Islam',
                ],
            ],
            [
                'name' => 'Politeknik Negeri Jakarta (PNJ Depok)',
                'short_name' => 'PNJ',
                'province' => 'DKI Jakarta',
                'type' => 'Politeknik',
                'majors' => [
                    'D4 Teknik Informatika', 'D4 Teknik Multimedia Digital', 'D4 Broadband Multimedia', 'D4 Teknik Otomasi Listrik Industri',
                    'D4 Konstruksi Gedung', 'D4 Perancangan Jalan & Jembatan', 'D4 Teknik Manufaktur', 'D4 Pembangkit Tenaga Listrik',
                    'D4 Akuntansi Keuangan', 'D4 Keuangan & Perbankan', 'D4 Manajemen Pemasaran (MICE)', 'D4 Jurnalistik', 'D4 Penerbitan',
                ],
            ],
            [
                'name' => 'Politeknik Keuangan Negara STAN (PKN STAN)',
                'short_name' => 'PKN STAN',
                'province' => 'Banten / DKI Jakarta',
                'type' => 'Kedinasan',
                'majors' => [
                    'D4 Akuntansi Sektor Publik', 'D4 Manajemen Keuangan Negara', 'D4 Manajemen Aset Publik',
                ],
            ],
            [
                'name' => 'Politeknik Statistika STIS (Jakarta)',
                'short_name' => 'STIS',
                'province' => 'DKI Jakarta',
                'type' => 'Kedinasan',
                'majors' => [
                    'D4 Statistika Terapan', 'D4 Komputasi Statistik (Data Science)', 'D3 Statistika',
                ],
            ],
            [
                'name' => 'Universitas Bina Nusantara (BINUS University)',
                'short_name' => 'BINUS',
                'province' => 'DKI Jakarta',
                'type' => 'PTS',
                'majors' => [
                    'Computer Science (Teknik Informatika)', 'Cyber Security', 'Game Application & Technology', 'Data Science',
                    'Artificial Intelligence (AI)', 'Information Systems', 'Business Information Technology', 'Finance', 'Accounting',
                    'International Business & Management', 'Marketing Communication', 'Mass Communication', 'Digital Journalism',
                    'Visual Communication Design (DKV)', 'Animation', 'Interactive Design & Technology', 'Interior Design', 'Architecture',
                    'Civil Engineering', 'Industrial Engineering', 'Computer Engineering', 'Food Technology', 'Biotechnology', 'Psychology',
                ],
            ],
            [
                'name' => 'Universitas Trisakti',
                'short_name' => 'Trisakti',
                'province' => 'DKI Jakarta',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Teknik Perminyakan', 'Teknik Pertambangan', 'Teknik Geologi',
                    'Teknik Mesin', 'Teknik Elektro', 'Teknik Industri', 'Teknik Sipil', 'Arsitektur', 'Arsitektur Lanskap',
                    'Perencanaan Wilayah & Kota (PWK)', 'Teknik Lingkungan', 'Sistem Informasi', 'Informatika',
                    'Desain Komunikasi Visual (DKV)', 'Desain Interior', 'Desain Produk', 'Fotografi', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ilmu Hukum',
                ],
            ],
            [
                'name' => 'Universitas Tarumanagara (Untar Jakarta)',
                'short_name' => 'Untar',
                'province' => 'DKI Jakarta',
                'type' => 'PTS',
                'majors' => [
                    'Kedokteran Umum', 'Teknik Sipil', 'Arsitektur', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Industri',
                    'Perencanaan Wilayah & Kota', 'Informatika', 'Sistem Informasi', 'Manajemen', 'Akuntansi', 'Bisnis Digital',
                    'Ilmu Hukum', 'Psikologi', 'Ilmu Komunikasi (Humas / Jurnalistik)', 'Desain Komunikasi Visual (DKV)', 'Desain Interior',
                ],
            ],
            [
                'name' => 'Universitas Mercu Buana (UMB Jakarta)',
                'short_name' => 'Mercu Buana',
                'province' => 'DKI Jakarta',
                'type' => 'PTS',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Sipil', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Industri',
                    'Arsitektur', 'Desain Produk', 'Desain Interior', 'Desain Komunikasi Visual (DKV)',
                    'Manajemen', 'Akuntansi', 'Ilmu Komunikasi (Broadcasting / Public Relations / Digital Media)', 'Psikologi',
                ],
            ],

            // ==========================================
            // BANTEN
            // ==========================================
            [
                'name' => 'Universitas Sultan Ageng Tirtayasa (Untirta Cilegon / Serang)',
                'short_name' => 'Untirta',
                'province' => 'Banten',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Keperawatan', 'Gizi', 'Farmasi', 'Teknik Kimia', 'Teknik Mesin',
                    'Teknik Elektro', 'Teknik Metalurgi', 'Teknik Industri', 'Teknik Sipil', 'Informatika',
                    'Agribisnis', 'Agroekoteknologi', 'Perikanan', 'Teknologi Pangan', 'Manajemen', 'Akuntansi',
                    'Ekonomi Pembangunan', 'Ekonomi Syariah', 'Ilmu Komunikasi', 'Ilmu Administrasi Publik', 'Ilmu Pemerintahan',
                    'Ilmu Hukum', 'PGSD', 'Pendidikan Matematika', 'Pendidikan Biologi', 'Pendidikan Fisika', 'Pendidikan Bahasa Inggris',
                ],
            ],
            [
                'name' => 'UIN Sultan Maulana Hasanuddin Banten (UIN SMH)',
                'short_name' => 'UIN Banten',
                'province' => 'Banten',
                'type' => 'PTKIN',
                'majors' => [
                    'Informatika', 'Sistem Informasi', 'Hukum Ekonomi Syariah', 'Hukum Tata Negara Islam', 'Ekonomi Syariah',
                    'Perbankan Syariah', 'Asuransi Syariah', 'Komunikasi Penyiaran Islam', 'Bimbingan Konseling Islam',
                    'Pendidikan Agama Islam', 'Pendidikan Bahasa Arab', 'Pendidikan Bahasa Inggris',
                ],
            ],
            [
                'name' => 'Universitas Multimedia Nusantara (UMN Serpong)',
                'short_name' => 'UMN',
                'province' => 'Banten',
                'type' => 'PTS',
                'majors' => [
                    'Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro', 'Teknik Fisika',
                    'Desain Komunikasi Visual (DKV)', 'Animasi (Film & Animation)', 'Film & Televisi', 'Arsitektur',
                    'Jurnalistik (Digital Journalism)', 'Strategic Communication (Public Relations)', 'Manajemen', 'Akuntansi',
                ],
            ],
            [
                'name' => 'Universitas Pelita Harapan (UPH Karawaci)',
                'short_name' => 'UPH',
                'province' => 'Banten',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Keperawatan', 'Farmasi', 'Teknik Informatika', 'Sistem Informasi',
                    'Teknik Elektro', 'Teknik Industri', 'Teknik Sipil', 'Arsitektur', 'Desain Produk', 'Desain Komunikasi Visual (DKV)',
                    'Desain Interior', 'Manajemen', 'Akuntansi', 'Ilmu Hukum', 'Hubungan Internasional', 'Ilmu Komunikasi', 'Psikologi', 'Seni Musik',
                ],
            ],
            [
                'name' => 'Universitas Pamulang (Unpam Tangerang Selatan)',
                'short_name' => 'Unpam',
                'province' => 'Banten',
                'type' => 'PTS',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Kimia', 'Teknik Industri',
                    'Manajemen', 'Akuntansi', 'Ilmu Hukum', 'Sastra Inggris', 'Sastra Indonesia', 'Pendidikan Ekonomi', 'Pendidikan Pancasila & Kewarganegaraan',
                ],
            ],

            // ==========================================
            // JAWA TENGAH
            // ==========================================
            [
                'name' => 'Universitas Diponegoro (Undip Semarang)',
                'short_name' => 'Undip',
                'province' => 'Jawa Tengah',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Kedokteran Gigi', 'Farmasi', 'Keperawatan', 'Gizi', 'Kesehatan Masyarakat',
                    'Teknik Sipil', 'Arsitektur', 'Teknik Mesin', 'Teknik Kimia', 'Teknik Elektro', 'Perencanaan Wilayah & Kota (PWK)',
                    'Teknik Industri', 'Teknik Lingkungan', 'Teknik Perkapalan', 'Teknik Geologi', 'Teknik Geodesi', 'Teknik Komputer',
                    'Informatika', 'Matematika', 'Biologi', 'Kimia', 'Fisika', 'Statistika', 'Bioteknologi', 'Manajemen', 'Akuntansi',
                    'Ekonomi Pembangunan', 'Bisnis Digital', 'Ekonomi Islam', 'Ilmu Hukum', 'Ilmu Komunikasi', 'Hubungan Internasional',
                    'Administrasi Publik', 'Administrasi Bisnis', 'Ilmu Pemerintahan', 'Psikologi', 'Oseanografi', 'Ilmu Kelautan',
                    'Akuakultur', 'Manajemen Sumberdaya Perairan', 'Teknologi Hasil Perikanan', 'Peternakan', 'Teknologi Pangan', 'Agroteknologi', 'Agribisnis',
                ],
            ],
            [
                'name' => 'Universitas Sebelas Maret (UNS Surakarta)',
                'short_name' => 'UNS Solo',
                'province' => 'Jawa Tengah',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Kebidanan', 'Farmasi', 'Psikologi', 'Teknik Sipil', 'Arsitektur', 'Teknik Industri',
                    'Teknik Mesin', 'Teknik Kimia', 'Teknik Elektro', 'Perencanaan Wilayah & Kota', 'Informatika', 'Sains Data',
                    'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Statistika', 'Ilmu Lingkungan', 'Agroteknologi', 'Agribisnis',
                    'Ilmu Tanah', 'Peternakan', 'Pengelolaan Hutan', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Bisnis Digital',
                    'Ilmu Hukum', 'Ilmu Komunikasi', 'Hubungan Internasional', 'Sosiologi', 'Ilmu Administrasi Negara',
                    'Desain Komunikasi Visual (DKV)', 'Desain Interior', 'Seni Rupa Murni', 'Kriya Seni', 'Pendidikan Guru Sekolah Dasar (PGSD)',
                    'Bimbingan dan Konseling (BK)', 'Pendidikan Luar Biasa (PLB)', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa Indonesia',
                ],
            ],
            [
                'name' => 'Universitas Negeri Semarang (UNNES)',
                'short_name' => 'UNNES',
                'province' => 'Jawa Tengah',
                'type' => 'PTN',
                'majors' => [
                    'Ilmu Komputer', 'Sistem Informasi', 'Teknik Informatika', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Sipil', 'Teknik Kimia', 'Arsitektur',
                    'Pendidikan Matematika', 'Matematika', 'Pendidikan Biologi', 'Biologi', 'Pendidikan Kimia', 'Kimia', 'Pendidikan Fisika', 'Fisika',
                    'Farmasi', 'Kesehatan Masyarakat', 'Ilmu Gizi', 'Kedokteran', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Bisnis Digital',
                    'Ilmu Hukum', 'PGSD', 'PGPAUD', 'Bimbingan dan Konseling', 'Psikologi', 'Ilmu Keolahragaan', 'Pendidikan Kepelatihan Olahraga',
                    'Pendidikan Jasmani (PJKR)', 'Desain Komunikasi Visual (DKV)', 'Seni Rupa', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa Indonesia',
                ],
            ],
            [
                'name' => 'Universitas Jenderal Soedirman (Unsoed Purwokerto)',
                'short_name' => 'Unsoed',
                'province' => 'Jawa Tengah',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Kedokteran Gigi', 'Farmasi', 'Keperawatan', 'Ilmu Gizi', 'Kesehatan Masyarakat',
                    'Informatika', 'Teknik Elektro', 'Teknik Sipil', 'Teknik Geologi', 'Teknik Industri', 'Matematika', 'Fisika', 'Kimia', 'Biologi',
                    'Agroteknologi', 'Agribisnis', 'Ilmu Kelautan', 'Akuakultur', 'Peternakan', 'Teknologi Pangan', 'Teknik Pertanian',
                    'Manajemen', 'Akuntansi', 'Ilmu Ekonomi Pembangunan', 'Ilmu Hukum', 'Ilmu Komunikasi', 'Hubungan Internasional',
                    'Administrasi Publik', 'Sosiologi', 'Ilmu Politik', 'Sastra Inggris', 'Sastra Jepang', 'Sastra Indonesia',
                ],
            ],
            [
                'name' => 'UIN Walisongo Semarang',
                'short_name' => 'UIN Walisongo',
                'province' => 'Jawa Tengah',
                'type' => 'PTKIN',
                'majors' => [
                    'Teknologi Informasi', 'Biologi', 'Fisika', 'Kimia', 'Matematika', 'Gizi', 'Kesehatan Lingkungan',
                    'Manajemen', 'Akuntansi Syariah', 'Ekonomi Islam', 'Perbankan Syariah', 'Ilmu Hukum', 'Hukum Keluarga Islam',
                    'Hukum Ekonomi Syariah', 'Komunikasi Penyiaran Islam', 'Bimbingan Penyuluhan Islam', 'Psikologi', 'Sosiologi Agama',
                ],
            ],
            [
                'name' => 'Universitas Tidar (Untidar Magelang)',
                'short_name' => 'Untidar',
                'province' => 'Jawa Tengah',
                'type' => 'PTN',
                'majors' => [
                    'Teknik Mesin', 'Teknik Elektro', 'Teknik Sipil', 'Teknologi Informasi', 'Mekatronika', 'Agroteknologi', 'Peternakan', 'Akuakultur',
                    'Ekonomi Pembangunan', 'Manajemen', 'Akuntansi', 'Ilmu Administrasi Negara', 'Ilmu Komunikasi', 'Hukum',
                    'Pendidikan Bahasa & Sastra Indonesia', 'Pendidikan Bahasa Inggris', 'Pendidikan IPA', 'Pendidikan Biologi', 'Pendidikan Matematika',
                ],
            ],
            [
                'name' => 'Politeknik Negeri Semarang (Polines)',
                'short_name' => 'Polines',
                'province' => 'Jawa Tengah',
                'type' => 'Politeknik',
                'majors' => [
                    'D4 Teknik Informatika', 'D4 Teknik Telekomunikasi', 'D4 Teknik Mesin Produksi & Perawatan', 'D4 Perancangan Jalan & Jembatan',
                    'D4 Komputerisasi Akuntansi', 'D4 Perbankan Syariah', 'D4 Manajemen Bisnis Internasional', 'D4 Analisis Keuangan',
                ],
            ],
            [
                'name' => 'Universitas Muhammadiyah Surakarta (UMS)',
                'short_name' => 'UMS',
                'province' => 'Jawa Tengah',
                'type' => 'PTS',
                'majors' => [
                    'Kedokteran Umum', 'Kedokteran Gigi', 'Farmasi', 'Fisioterapi', 'Ilmu Keperawatan', 'Ilmu Gizi',
                    'Teknik Informatika', 'Teknik Mesin', 'Teknik Sipil', 'Teknik Elektro', 'Teknik Kimia', 'Arsitektur',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ilmu Hukum', 'Psikologi', 'Ilmu Komunikasi',
                    'PGSD', 'Pendidikan Bahasa Inggris', 'Pendidikan Matematika', 'Pendidikan Biologi',
                ],
            ],
            [
                'name' => 'Universitas Dian Nuswantoro (Udinus Semarang)',
                'short_name' => 'Udinus',
                'province' => 'Jawa Tengah',
                'type' => 'PTS',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Desain Komunikasi Visual (DKV)', 'Animasi (Film & Animasi)', 'Game Application',
                    'Film & Televisi', 'Teknik Biomedis', 'Teknik Elektro', 'Teknik Industri', 'Kesehatan Masyarakat', 'Rekam Medis & Info Kesehatan',
                    'Manajemen', 'Akuntansi', 'Ilmu Komunikasi', 'Sastra Inggris', 'Sastra Jepang',
                ],
            ],
            [
                'name' => 'Universitas Islam Sultan Agung (Unissula Semarang)',
                'short_name' => 'Unissula',
                'province' => 'Jawa Tengah',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Farmasi', 'Kebidanan', 'Ilmu Keperawatan',
                    'Teknik Sipil', 'Teknik Industri', 'Teknik Informatika', 'Perencanaan Wilayah & Kota', 'Manajemen', 'Akuntansi',
                    'Ilmu Hukum', 'Psikologi', 'Ilmu Komunikasi', 'Tarbiyah / PAI',
                ],
            ],
            [
                'name' => 'Universitas Kristen Satya Wacana (UKSW Salatiga)',
                'short_name' => 'UKSW',
                'province' => 'Jawa Tengah',
                'type' => 'PTS',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro', 'Teknik Biomedis',
                    'Hubungan Internasional', 'Psikologi', 'Manajemen', 'Akuntansi', 'Ilmu Ekonomi', 'Ilmu Hukum',
                    'Desain Komunikasi Visual (DKV)', 'Pendidikan Bahasa Inggris', 'PGSD', 'Biologi', 'Kedokteran',
                ],
            ],

            // ==========================================
            // DI YOGYAKARTA
            // ==========================================
            [
                'name' => 'Universitas Gadjah Mada (UGM)',
                'short_name' => 'UGM',
                'province' => 'DI Yogyakarta',
                'type' => 'PTN',
                'majors' => [
                    'Kedokteran (Pendidikan Dokter)', 'Kedokteran Gigi', 'Kedokteran Hewan', 'Farmasi', 'Ilmu Keperawatan', 'Gizi Kesehatan',
                    'Ilmu Komputer', 'Teknologi Informasi', 'Teknik Elektro', 'Teknik Biomedis', 'Teknik Mesin', 'Teknik Industri',
                    'Teknik Sipil', 'Teknik Lingkungan', 'Arsitektur', 'Perencanaan Wilayah & Kota (PWK)', 'Teknik Geodesi', 'Teknik Geologi',
                    'Teknik Nuklir', 'Teknik Fisika', 'Teknik Kimia', 'Matematika', 'Statistika', 'Aktuaria', 'Fisika', 'Geofisika', 'Kimia', 'Biologi',
                    'Ilmu Hukum', 'Manajemen', 'Akuntansi', 'Ilmu Ekonomi', 'Ilmu Komunikasi', 'Ilmu Hubungan Internasional (HI)',
                    'Politik & Pemerintahan (Polgov)', 'Manajemen & Kebijakan Publik (MKP)', 'Sosiologi', 'Pembangunan Sosial & Kesejahteraan (PSdK)',
                    'Psikologi', 'Agronomi', 'Akuakultur', 'Ilmu Tanah', 'Proteksi Tanaman', 'Teknologi Industri Pertanian (TIP)',
                    'Teknologi Pangan & Hasil Pertanian', 'Teknik Pertanian', 'Kehutanan', 'Peternakan', 'Arkeologi', 'Sastra Inggris',
                    'Sastra Indonesia', 'Sastra Jepang', 'Sastra Prancis', 'Sastra Arab', 'Antropologi Budaya', 'Ilmu Sejarah', 'Filsafat',
                ],
            ],
            [
                'name' => 'Universitas Negeri Yogyakarta (UNY)',
                'short_name' => 'UNY',
                'province' => 'DI Yogyakarta',
                'type' => 'PTN',
                'majors' => [
                    'Teknologi Informasi', 'Teknik Komputer', 'Teknik Elektro', 'Teknik Elektronika', 'Teknik Mesin', 'Teknik Otomotif', 'Teknik Sipil', 'Arsitektur',
                    'Pendidikan Matematika', 'Matematika', 'Statistika', 'Pendidikan Fisika', 'Fisika', 'Pendidikan Kimia', 'Kimia', 'Pendidikan Biologi', 'Biologi',
                    'Pendidikan Guru Sekolah Dasar (PGSD)', 'Pendidikan Guru PAUD (PGPAUD)', 'Bimbingan dan Konseling (BK)', 'Psikologi',
                    'Pendidikan Luar Biasa (PLB)', 'Manajemen', 'Akuntansi', 'Pendidikan Akuntansi', 'Pendidikan Ekonomi', 'Bisnis Digital',
                    'Ilmu Komunikasi', 'Administrasi Publik', 'Pendidikan Sejarah', 'Pendidikan Geografi', 'Pendidikan Sosiologi',
                    'Ilmu Keolahragaan', 'Pendidikan Kepelatihan Olahraga', 'Pendidikan Jasmani (PJOK)', 'Desain Komunikasi Visual (DKV)',
                    'Pendidikan Seni Rupa', 'Pendidikan Seni Musik', 'Pendidikan Seni Tari', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa Indonesia',
                ],
            ],
            [
                'name' => 'UPN "Veteran" Yogyakarta',
                'short_name' => 'UPNYK',
                'province' => 'DI Yogyakarta',
                'type' => 'PTN',
                'majors' => [
                    'Teknik Perminyakan', 'Teknik Pertambangan', 'Teknik Geologi', 'Teknik Geofisika', 'Teknik Metalurgi', 'Teknik Geomatika',
                    'Teknik Kimia', 'Teknik Industri', 'Informatika', 'Sistem Informasi', 'Sains Data',
                    'Agroteknologi', 'Agribisnis', 'Ilmu Tanah', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan',
                    'Ilmu Komunikasi', 'Hubungan Internasional (HI)', 'Administrasi Bisnis', 'Administrasi Publik', 'Hubungan Masyarakat (Humas)',
                ],
            ],
            [
                'name' => 'UIN Sunan Kalijaga Yogyakarta',
                'short_name' => 'UIN Suka',
                'province' => 'DI Yogyakarta',
                'type' => 'PTKIN',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Teknik Industri',
                    'Ilmu Perpustakaan', 'Manajemen', 'Akuntansi Syariah', 'Perbankan Syariah', 'Ekonomi Syariah',
                    'Ilmu Hukum', 'Hukum Keluarga Islam', 'Hukum Ekonomi Syariah', 'Hukum Tata Negara Islam',
                    'Ilmu Komunikasi', 'Komunikasi Penyiaran Islam', 'Psikologi', 'Sosiologi', 'Bimbingan Konseling Islam',
                ],
            ],
            [
                'name' => 'Institut Seni Indonesia Yogyakarta (ISI Jogja)',
                'short_name' => 'ISI Jogja',
                'province' => 'DI Yogyakarta',
                'type' => 'PTN',
                'majors' => [
                    'Desain Komunikasi Visual (DKV)', 'Desain Produk', 'Desain Interior', 'Televisi & Film', 'Animasi', 'Fotografi',
                    'Seni Murni (Seni Lukis / Patung / Grafis)', 'Kriya Seni (Batik / Logam / Kayu / Keramik)', 'Seni Musik',
                    'Pendidikan Musik', 'Seni Tari', 'Seni Teater', 'Etnomusikologi', 'Tata Kelola Seni',
                ],
            ],
            [
                'name' => 'Universitas Islam Indonesia (UII Yogyakarta)',
                'short_name' => 'UII',
                'province' => 'DI Yogyakarta',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Farmasi', 'Teknik Informatika', 'Sistem Informasi', 'Teknik Industri', 'Teknik Kimia',
                    'Teknik Sipil', 'Teknik Mesin', 'Teknik Lingkungan', 'Teknik Elektro', 'Arsitektur', 'Statistika', 'Kimia',
                    'Bisnis Digital', 'Manajemen', 'Akuntansi', 'Ilmu Ekonomi', 'Ilmu Hukum', 'Hukum Bisnis', 'Hubungan Internasional',
                    'Ilmu Komunikasi', 'Psikologi', 'Pendidikan Bahasa Inggris',
                ],
            ],
            [
                'name' => 'Universitas Muhammadiyah Yogyakarta (UMY)',
                'short_name' => 'UMY',
                'province' => 'DI Yogyakarta',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Farmasi', 'Keperawatan',
                    'Teknologi Informasi', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Agroteknologi', 'Agribisnis',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Perbankan Syariah', 'Ilmu Hukum', 'Hubungan Internasional',
                    'Ilmu Komunikasi', 'Ilmu Pemerintahan', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa Arab',
                ],
            ],
            [
                'name' => 'Universitas Atma Jaya Yogyakarta (UAJY)',
                'short_name' => 'UAJY',
                'province' => 'DI Yogyakarta',
                'type' => 'PTS',
                'majors' => [
                    'Informatika', 'Sistem Informasi', 'Teknik Sipil', 'Arsitektur', 'Teknik Industri', 'Teknologi Pangan',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ilmu Hukum', 'Ilmu Komunikasi', 'Sosiologi',
                ],
            ],
            [
                'name' => 'Universitas Sanata Dharma (USD Yogyakarta)',
                'short_name' => 'Sanata Dharma',
                'province' => 'DI Yogyakarta',
                'type' => 'PTS',
                'majors' => [
                    'Farmasi', 'Psikologi', 'Teknik Informatika', 'Teknik Elektro', 'Teknik Mesin', 'Mekatronika',
                    'Manajemen', 'Akuntansi', 'Ekonomi', 'Pendidikan Bahasa Inggris', 'Pendidikan Guru Sekolah Dasar (PGSD)',
                    'Pendidikan Matematika', 'Sastra Indonesia', 'Sastra Inggris', 'Ilmu Sejarah', 'Filsafat Keilahian',
                ],
            ],
            [
                'name' => 'Universitas Ahmad Dahlan (UAD Yogyakarta)',
                'short_name' => 'UAD',
                'province' => 'DI Yogyakarta',
                'type' => 'PTS',
                'majors' => [
                    'Kedokteran', 'Farmasi', 'Kesehatan Masyarakat', 'Gizi', 'Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Teknik Kimia', 'Teknik Industri',
                    'Manajemen', 'Akuntansi', 'Bisnis Jasa Pariwisata', 'Ilmu Hukum', 'Ilmu Komunikasi', 'Psikologi', 'PGSD', 'Pendidikan Bahasa Inggris',
                ],
            ],

            // ==========================================
            // JAWA TIMUR
            // ==========================================
            [
                'name' => 'Universitas Airlangga (Unair Surabaya)',
                'short_name' => 'Unair',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Kedokteran Hewan', 'Farmasi', 'Keperawatan', 'Kebidanan',
                    'Kesehatan Masyarakat', 'Gizi', 'Rekayasa Nanoteknologi', 'Teknik Biomedis', 'Sains Data', 'Teknik Robotika & Kecerdasan Buatan (AI)',
                    'Teknik Industri', 'Teknik Lingkungan', 'Matematika', 'Statistika', 'Fisika', 'Kimia', 'Biologi', 'Sistem Informasi',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ekonomi Islam', 'Perpajakan', 'Ilmu Hukum', 'Ilmu Komunikasi',
                    'Hubungan Internasional (HI)', 'Ilmu Politik', 'Ilmu Administrasi Negara', 'Sosiologi', 'Antropologi', 'Psikologi',
                    'Akuakultur', 'Teknologi Hasil Perikanan', 'Sastra Indonesia', 'Sastra Inggris', 'Sastra Jepang', 'Ilmu Sejarah',
                ],
            ],
            [
                'name' => 'Institut Teknologi Sepuluh Nopember (ITS Surabaya)',
                'short_name' => 'ITS',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Teknologi Informasi', 'Sains Data', 'Rekayasa Perangkat Lunak',
                    'Teknik Komputer', 'Teknik Elektro', 'Teknik Biomedis', 'Teknik Telekomunikasi', 'Teknik Mesin', 'Teknik Industri',
                    'Teknik Material & Metalurgi', 'Teknik Kimia', 'Teknik Fisika', 'Teknik Sistem Perkapalan', 'Teknik Perkapalan',
                    'Teknik Kelautan', 'Teknik Transportasi Laut', 'Teknik Lepas Pantai', 'Teknik Sipil', 'Teknik Arsitektur',
                    'Perencanaan Wilayah & Kota (PWK)', 'Teknik Lingkungan', 'Teknik Geomatika', 'Teknik Geofisika', 'Statistika',
                    'Sains Aktuaria', 'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Manajemen Bisnis', 'Studi Pembangunan',
                    'Desain Komunikasi Visual (DKV)', 'Desain Produk Industri', 'Desain Interior', 'Pendidikan Dokter (Kedokteran)',
                ],
            ],
            [
                'name' => 'Universitas Brawijaya (UB Malang)',
                'short_name' => 'UB',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Kedokteran Hewan', 'Farmasi', 'Keperawatan', 'Gizi Kesehatan', 'Kebidanan',
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknologi Informasi', 'Pendidikan Teknologi Informasi',
                    'Teknik Sipil', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Industri', 'Teknik Pengairan', 'Arsitektur',
                    'Perencanaan Wilayah & Kota (PWK)', 'Teknik Kimia', 'Teknik Lingkungan', 'Matematika', 'Statistika', 'Aktuaria', 'Fisika',
                    'Instrumentasi', 'Geofisika', 'Kimia', 'Biologi', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ekonomi Islam',
                    'Kewirausahaan', 'Keuangan & Perbankan', 'Ilmu Hukum', 'Ilmu Komunikasi', 'Hubungan Internasional', 'Ilmu Pemerintahan',
                    'Ilmu Politik', 'Psikologi', 'Sosiologi', 'Administrasi Publik', 'Administrasi Bisnis', 'Perpajakan', 'Ilmu Perpustakaan',
                    'Agroteknologi', 'Agribisnis', 'Peternakan', 'Teknologi Hasil Pertanian', 'Teknologi Industri Pertanian (TIP)',
                    'Teknik Biosistem', 'Pemanfaatan Sumberdaya Perikanan', 'Budidaya Perairan', 'Teknologi Hasil Perikanan', 'Ilmu Kelautan',
                ],
            ],
            [
                'name' => 'Universitas Negeri Malang (UM)',
                'short_name' => 'UM',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Teknik Informatika', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Teknik Industri', 'Arsitektur',
                    'Pendidikan Matematika', 'Matematika', 'Pendidikan Fisika', 'Fisika', 'Pendidikan Kimia', 'Kimia',
                    'Pendidikan Biologi', 'Biologi', 'Pendidikan IPA', 'Kedokteran', 'Kesehatan Masyarakat', 'Ilmu Gizi', 'Farmasi',
                    'Pendidikan Guru Sekolah Dasar (PGSD)', 'Pendidikan Guru PAUD', 'Bimbingan dan Konseling (BK)', 'Psikologi',
                    'Pendidikan Luar Biasa (PLB)', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Pendidikan Ekonomi',
                    'Ilmu Komunikasi', 'Pendidikan Bahasa & Sastra Indonesia', 'Pendidikan Bahasa Inggris', 'Desain Komunikasi Visual (DKV)',
                    'Pendidikan Seni Rupa', 'Ilmu Keolahragaan', 'Pendidikan Kepelatihan Olahraga', 'Pendidikan Jasmani (PJKR)',
                ],
            ],
            [
                'name' => 'Universitas Negeri Surabaya (Unesa)',
                'short_name' => 'Unesa',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Sains Data', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil',
                    'Pendidikan Matematika', 'Matematika', 'Pendidikan Biologi', 'Biologi', 'Pendidikan Kimia', 'Kimia', 'Pendidikan Fisika', 'Fisika',
                    'Kedokteran', 'Fisioterapi', 'Ilmu Keolahragaan', 'Pendidikan Kepelatihan Olahraga', 'Pendidikan Jasmani (Penjas)',
                    'Manajemen', 'Akuntansi', 'Bisnis Digital', 'Ekonomi Islam', 'Ilmu Hukum', 'Ilmu Komunikasi', 'PGSD', 'PGPAUD',
                    'Bimbingan dan Konseling (BK)', 'Psikologi', 'Pendidikan Khusus (PLB)', 'Desain Komunikasi Visual (DKV)',
                    'Pendidikan Seni Rupa', 'Pendidikan Seni Musik', 'Pendidikan Seni Tari', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa Indonesia',
                ],
            ],
            [
                'name' => 'UPN "Veteran" Jawa Timur (Surabaya)',
                'short_name' => 'UPN Jatim',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Informatika', 'Sistem Informasi', 'Sains Data', 'Bisnis Digital', 'Teknik Kimia', 'Teknik Industri', 'Teknik Sipil',
                    'Teknik Lingkungan', 'Teknik Mesin', 'Arsitektur', 'Desain Komunikasi Visual (DKV)', 'Desain Interior',
                    'Agroteknologi', 'Agribisnis', 'Teknologi Pangan', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan',
                    'Ilmu Komunikasi', 'Hubungan Internasional', 'Administrasi Publik', 'Administrasi Bisnis', 'Ilmu Hukum', 'Kedokteran',
                ],
            ],
            [
                'name' => 'Universitas Jember (Unej)',
                'short_name' => 'Unej',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Farmasi', 'Keperawatan', 'Kesehatan Masyarakat', 'Gizi',
                    'Informatika', 'Sistem Informasi', 'Teknologi Informasi', 'Teknik Sipil', 'Teknik Mesin', 'Teknik Elektro',
                    'Teknik Kimia', 'Teknik Pertambangan', 'Teknik Perminyakan', 'Teknik Perkapalan', 'Teknik Lingkungan',
                    'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Agroteknologi', 'Agribisnis', 'Teknologi Hasil Pertanian', 'Teknik Pertanian',
                    'Peternakan', 'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ekonomi Syariah', 'Ilmu Hukum', 'Hubungan Internasional',
                    'Ilmu Komunikasi', 'Administrasi Publik', 'Administrasi Bisnis', 'Sosiologi', 'PGSD', 'Pendidikan Bahasa Inggris',
                ],
            ],
            [
                'name' => 'UIN Sunan Ampel Surabaya (UINSA)',
                'short_name' => 'UINSA',
                'province' => 'Jawa Timur',
                'type' => 'PTKIN',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Lingkungan', 'Arsitektur', 'Matematika', 'Biologi', 'Ilmu Kelautan',
                    'Manajemen', 'Akuntansi', 'Ekonomi Syariah', 'Perbankan Syariah', 'Ilmu Hukum', 'Hukum Keluarga Islam',
                    'Hukum Ekonomi Syariah', 'Hukum Tata Negara', 'Ilmu Komunikasi', 'Hubungan Internasional', 'Komunikasi Penyiaran Islam',
                    'Psikologi', 'Sosiologi', 'Bimbingan Konseling Islam', 'Pendidikan Agama Islam',
                ],
            ],
            [
                'name' => 'UIN Maulana Malik Ibrahim Malang (UIN Malang)',
                'short_name' => 'UIN Malang',
                'province' => 'Jawa Timur',
                'type' => 'PTKIN',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Farmasi', 'Teknik Informatika', 'Teknik Arsitektur', 'Matematika', 'Fisika', 'Kimia', 'Biologi',
                    'Manajemen', 'Akuntansi', 'Perbankan Syariah', 'Hukum Bisnis Syariah', 'Hukum Keluarga Islam',
                    'Psikologi', 'Pendidikan Agama Islam', 'Pendidikan Bahasa Arab', 'Pendidikan Guru Madrasah Ibtidaiyah (PGMI)',
                ],
            ],
            [
                'name' => 'Universitas Trunojoyo Madura (UTM Bangkalan)',
                'short_name' => 'UTM Madura',
                'province' => 'Jawa Timur',
                'type' => 'PTN',
                'majors' => [
                    'Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Teknik Industri', 'Teknik Mekatronika',
                    'Agroteknologi', 'Agribisnis', 'Teknologi Industri Pertanian', 'Ilmu Kelautan', 'Manajemen Sumberdaya Perairan',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Ekonomi Syariah', 'Ilmu Hukum', 'Ilmu Komunikasi', 'Sosiologi', 'PGSD',
                ],
            ],
            [
                'name' => 'Politeknik Negeri Malang (Polinema)',
                'short_name' => 'Polinema',
                'province' => 'Jawa Timur',
                'type' => 'Politeknik',
                'majors' => [
                    'D4 Teknik Informatika', 'D4 Sistem Informasi Bisnis', 'D4 Jaringan Telekomunikasi Digital', 'D4 Teknik Elektronika',
                    'D4 Teknik Otomasi Listrik', 'D4 Manajemen Rekayasa Konstruksi', 'D4 Teknik Mesin Produksi & Perawatan', 'D4 Teknik Kimia Industri',
                    'D4 Akuntansi Manajemen', 'D4 Keuangan', 'D4 Manajemen Pemasaran', 'D3 Bahasa Inggris',
                ],
            ],
            [
                'name' => 'Politeknik Elektronika Negeri Surabaya (PENS)',
                'short_name' => 'PENS',
                'province' => 'Jawa Timur',
                'type' => 'Politeknik',
                'majors' => [
                    'D4 Teknik Informatika', 'D4 Sains Data Terapan', 'D4 Rekayasa Perangkat Lunak', 'D4 Game Technology',
                    'D4 Teknik Telekomunikasi', 'D4 Teknik Elektro Industri', 'D4 Teknik Mekatronika', 'D4 Teknologi Rekayasa Multimedia',
                ],
            ],
            [
                'name' => 'Universitas Muhammadiyah Malang (UMM)',
                'short_name' => 'UMM',
                'province' => 'Jawa Timur',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Farmasi', 'Keperawatan', 'Fisioterapi',
                    'Informatika', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Teknik Industri',
                    'Agroteknologi', 'Agribisnis', 'Teknologi Pangan', 'Peternakan', 'Kehutanan', 'Akuakultur',
                    'Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Bisnis Digital', 'Ilmu Hukum', 'Hubungan Internasional',
                    'Ilmu Komunikasi', 'Ilmu Pemerintahan', 'Sosiologi', 'Kesejahteraan Sosial', 'Psikologi', 'PGSD', 'Pendidikan Bahasa Inggris',
                ],
            ],
            [
                'name' => 'Universitas Surabaya (Ubaya)',
                'short_name' => 'Ubaya',
                'province' => 'Jawa Timur',
                'type' => 'PTS',
                'majors' => [
                    'Pendidikan Dokter (Kedokteran)', 'Farmasi', 'Bioteknologi', 'Teknik Informatika', 'Sistem Informasi',
                    'Teknik Industri', 'Teknik Kimia', 'Teknik Mesin & Manufaktur', 'Manajemen', 'Akuntansi', 'Ekonomi',
                    'Ilmu Hukum', 'Psikologi', 'Desain & Manajemen Produk', 'Desain Fashion & Produk Gaya Hidup',
                ],
            ],
            [
                'name' => 'Universitas Kristen Petra (Surabaya)',
                'short_name' => 'UK Petra',
                'province' => 'Jawa Timur',
                'type' => 'PTS',
                'majors' => [
                    'Informatika', 'Sistem Informasi Bisnis', 'Data Science & AI', 'Desain Komunikasi Visual (DKV)', 'Desain Interior',
                    'Arsitektur', 'Teknik Sipil', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Industri', 'Manajemen Bisnis', 'Akuntansi Bisnis',
                    'Ilmu Komunikasi', 'Sastra Inggris', 'International Business Engineering (IBE)',
                ],
            ],
            [
                'name' => 'Universitas Ciputra (UC Surabaya)',
                'short_name' => 'UC Surabaya',
                'province' => 'Jawa Timur',
                'type' => 'PTS',
                'majors' => [
                    'Kedokteran', 'Informatics (Teknik Informatika)', 'Information Systems', 'Visual Communication Design (DKV)',
                    'Interior Architecture', 'International Business Management (IBM)', 'Accounting', 'Communication Science',
                    'Psychology', 'Culinary Business', 'Fashion Product Design', 'Hotel & Tourism Business',
                ],
            ],
        ];
    }

    /**
     * Daftar Rumpun Program Studi Nasional Indonesia
     * Terbagi dalam rumpun Saintek, Medis/Kesehatan, Soshum, Ekonomi/Bisnis, Keguruan/Pendidikan, Seni/Desain, dan Vokasi/Teknologi Terapan.
     *
     * @return array<string, array<int, string>>
     */
    public static function getAllStandardMajors(): array
    {
        return [
            'Teknologi Informasi & Komputer' => [
                'Teknik Informatika', 'Ilmu Komputer', 'Sistem Informasi', 'Teknologi Informasi',
                'Sains Data (Data Science)', 'Rekayasa Perangkat Lunak (Software Engineering)',
                'Teknik Komputer', 'Cyber Security / Keamanan Siber', 'Kecerdasan Buatan (Artificial Intelligence)',
                'Game Application & Technology', 'Sistem Informasi Bisnis', 'Bisnis Digital',
            ],
            'Kedokteran & Kesehatan' => [
                'Pendidikan Dokter (Kedokteran)', 'Pendidikan Dokter Gigi', 'Kedokteran Hewan', 'Farmasi',
                'Ilmu Keperawatan', 'Ilmu Kebidanan', 'Kesehatan Masyarakat', 'Ilmu Gizi / Gizi Kesehatan',
                'Fisioterapi', 'Teknologi Laboratorium Medis', 'Radiologi', 'Rekam Medis & Manajemen Informasi Kesehatan',
            ],
            'Teknik & Rekayasa' => [
                'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Arsitektur', 'Teknik Industri', 'Teknik Kimia',
                'Teknik Lingkungan', 'Perencanaan Wilayah & Kota (PWK / Planologi)', 'Teknik Pertambangan',
                'Teknik Perminyakan', 'Teknik Geologi', 'Teknik Geofisika', 'Teknik Geodesi & Geomatika',
                'Teknik Material & Metalurgi', 'Teknik Biomedis', 'Teknik Mekatronika', 'Teknik Penerbangan / Dirgantara',
                'Teknik Perkapalan', 'Teknik Kelautan', 'Teknik Nuklir', 'Teknik Fisika', 'Teknik Otomotif',
            ],
            'MIPA & Sains Murni' => [
                'Matematika', 'Statistika', 'Aktuaria (Sains Aktuaria)', 'Fisika', 'Kimia', 'Biologi',
                'Astronomi', 'Meteorologi', 'Geofisika', 'Bioteknologi', 'Biokimia', 'Mikrobiologi',
            ],
            'Pertanian, Peternakan & Kehutanan' => [
                'Agroteknologi', 'Agribisnis', 'Teknologi Pangan', 'Teknologi Industri Pertanian (TIN/TIP)',
                'Teknik Pertanian & Biosistem', 'Agronomi & Hortikultura', 'Proteksi Tanaman', 'Ilmu Tanah',
                'Peternakan', 'Nutrisi & Teknologi Pakan', 'Teknologi Hasil Ternak', 'Akuakultur (Budidaya Perairan)',
                'Ilmu Kelautan', 'Pemanfaatan Sumberdaya Perikanan', 'Kehutanan', 'Silvikultur', 'Konservasi Sumberdaya Hutan',
            ],
            'Ekonomi & Bisnis' => [
                'Manajemen', 'Akuntansi', 'Ilmu Ekonomi / Ekonomi Pembangunan', 'Bisnis Internasional',
                'Keuangan & Perbankan', 'Perpajakan', 'Ekonomi Islam / Syariah', 'Perbankan Syariah',
                'Kewirausahaan / Entrepreneurship', 'Manajemen Pemasaran', 'Manajemen Logistik & Rantai Pasok',
            ],
            'Sosial, Hukum & Humaniora' => [
                'Ilmu Hukum', 'Ilmu Komunikasi', 'Hubungan Internasional (HI)', 'Ilmu Administrasi Publik / Negara',
                'Ilmu Administrasi Bisnis / Niaga', 'Ilmu Administrasi Fiskal', 'Ilmu Pemerintahan', 'Ilmu Politik',
                'Psikologi', 'Sosiologi', 'Kriminologi', 'Antropologi Sosial', 'Kesejahteraan Sosial',
                'Jurnalistik', 'Hubungan Masyarakat (Public Relations)', 'Penyiaran / Broadcasting', 'Ilmu Perpustakaan',
            ],
            'Bahasa & Sastra' => [
                'Sastra Indonesia', 'Sastra Inggris', 'Sastra Jepang', 'Sastra Arab', 'Sastra Jerman',
                'Sastra Prancis', 'Sastra Korea', 'Sastra Cina / Mandarin', 'Sastra Rusia', 'Sastra Sunda',
                'Sastra Jawa', 'Ilmu Sejarah', 'Arkeologi', 'Filsafat',
            ],
            'Pendidikan & Keguruan (FKIP)' => [
                'Pendidikan Guru Sekolah Dasar (PGSD)', 'Pendidikan Guru PAUD (PGPAUD)',
                'Bimbingan dan Konseling (BK)', 'Pendidikan Khusus (PLB / Luar Biasa)',
                'Pendidikan Matematika', 'Pendidikan Biologi', 'Pendidikan Fisika', 'Pendidikan Kimia', 'Pendidikan IPA',
                'Pendidikan Bahasa & Sastra Indonesia', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa Arab',
                'Pendidikan Sejarah', 'Pendidikan Geografi', 'Pendidikan Sosiologi', 'Pendidikan Ekonomi',
                'Pendidikan Pancasila & Kewarganegaraan (PPKn)', 'Pendidikan Teknik Informatika / Komputer',
                'Pendidikan Teknik Elektro', 'Pendidikan Teknik Mesin', 'Pendidikan Teknik Bangunan',
                'Pendidikan Jasmani Kesehatan & Rekreasi (PJKR)', 'Pendidikan Kepelatihan Olahraga (PKO)',
                'Pendidikan Seni Rupa', 'Pendidikan Seni Musik', 'Pendidikan Seni Tari',
            ],
            'Seni, Desain & Media Kreatif' => [
                'Desain Komunikasi Visual (DKV)', 'Desain Interior', 'Desain Produk', 'Arsitektur Interior',
                'Animasi (Film & Animasi)', 'Film & Televisi', 'Fotografi', 'Seni Rupa Murni', 'Kriya Seni',
                'Seni Musik', 'Seni Tari', 'Seni Teater', 'Tata Kelola Seni',
            ],
            'Kedinasan & Vokasi Terapan' => [
                'Akuntansi Sektor Publik (PKN STAN)', 'Manajemen Keuangan Negara (PKN STAN)',
                'Komputasi Statistik & Sains Data (STIS)', 'Statistika Terapan (STIS)',
                'Administrasi Pemerintahan Daerah (IPDN)', 'Manajemen Keamanan Siber',
                'Rekayasa Perangkat Lunak Terapan', 'Teknologi Rekayasa Multimedia',
            ],
        ];
    }
}

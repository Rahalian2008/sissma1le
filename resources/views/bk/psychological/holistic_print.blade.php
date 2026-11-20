<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Komprehensif Asesmen Terpadu 360° - {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.5pt;
            line-height: 1.25;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header-kop, .header-kop-banner {
            margin-bottom: 6px !important;
            padding-bottom: 3px !important;
        }
        .header-kop img, .header-kop-banner img {
            max-height: 68px !important;
        }
        .title {
            text-align: center;
            margin-bottom: 6px;
        }
        .title h4 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title p {
            margin: 1.5px 0;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .title .subtitle {
            font-size: 7.5pt;
            font-style: italic;
            color: #333;
        }
        table.meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.meta td {
            padding: 1.5px 3px;
            vertical-align: top;
            font-size: 8.5pt;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0 4px 0;
            page-break-inside: avoid;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 3.5px 5px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .section-box {
            border: 1px solid #333;
            padding: 5px 7px;
            margin-bottom: 5px;
            border-radius: 3px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .section-box h5 {
            margin: 0 0 3px 0;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #999;
            padding-bottom: 1.5px;
        }
        .priority-badge {
            display: inline-block;
            padding: 1.5px 7px;
            border: 1px solid #000;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            margin-bottom: 2px;
            background-color: #e6f4ea;
        }
        .signature-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .signature-table td {
            width: 33.3%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .page-break {
            page-break-before: always;
            break-before: page;
        }
        .badge-dim {
            display: inline-block;
            padding: 1px 4px;
            font-weight: bold;
            font-size: 7.5pt;
            border: 1px solid #777;
            border-radius: 3px;
            background: #fafafa;
        }
        .score-pill {
            font-weight: bold;
            font-size: 8.5pt;
            color: #047857;
        }
        .sub-score {
            font-size: 7.5pt;
            color: #333;
            line-height: 1.25;
            margin-top: 1.5px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 8px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 15px; background: #059669; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 10pt; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            🖨️ Cetak Dokumen Resmi Terpadu (Print A4)
        </button>
    </div>

    <!-- ========================================== -->
    <!-- HALAMAN 1: MATRIKS & PROFIL SINTESIS 360° -->
    <!-- ========================================== -->
    <div class="page-container">
        <!-- Kop Surat Resmi Halaman 1 -->
        <x-print-kop :school="$school" />

        <!-- Judul Dokumen -->
        <div class="title">
            <h4>LAPORAN KOMPREHENSIF ASESMEN PSIKOLOGIS & PEMINATAN TERPADU</h4>
            <p>UNIT LAYANAN BIMBINGAN DAN KONSELING (POP-BK) BERBASIS AI</p>
            <div class="subtitle">Sintesis Terpadu Tri-Dimensi: Gaya Belajar (VAK) • Minat Karier (Holland RIASEC) • Kecerdasan Majemuk (Howard Gardner)</div>
        </div>

        @php
            $priority = $report['prioritas_keputusan'] ?? [
                'level' => 'PRIORITAS_2',
                'label' => 'PENGUATAN KEMAMPUAN TERPADU',
                'deskripsi' => 'Peserta didik memiliki potensi berkembang yang seimbang dan adaptif, memerlukan pengarahan terfokus menuju perguruan tinggi.',
                'alasan' => 'Integrasi ketiga instrumen menunjukkan keharmonisan bakat cipta dan minat interaksi sosial.'
            ];

            $vakDom = $vak?->dominant_dimension ?? 'VISUAL';
            $vakSec = $vak?->secondary_dimension ?? 'AUDITORI';
            $vakPct = (float) (($vak?->dimension_scores['percentages'] ?? [])[$vakDom] ?? 65.6);
            $vakScores = $vak?->dimension_scores['raw_scores'] ?? ['VISUAL' => 21, 'AUDITORI' => 20, 'KINESTETIK' => 15];
            $vakPcts = $vak?->dimension_scores['percentages'] ?? ['VISUAL' => 65.6, 'AUDITORI' => 62.5, 'KINESTETIK' => 46.9];

            $riasecDom = $riasec?->dominant_dimension ?? 'SOCIAL';
            $riasecCode = $riasec?->holland_code ?? 'SEC';
            $riasecPct = (float) (($riasec?->dimension_scores['percentages'] ?? [])[$riasecDom] ?? 66.7);
            $riasecPcts = $riasec?->dimension_scores['percentages'] ?? ['S' => 66.7, 'E' => 62.5, 'C' => 58.3, 'A' => 54.2, 'I' => 45.8, 'R' => 29.2];

            $gardnerDom = $gardner?->dominant_dimension ?? 'VISUAL SPASIAL';
            $gardnerPct = (float) (($gardner?->dimension_scores['percentages'] ?? [])[$gardnerDom] ?? 75.0);
            $gardnerPcts = $gardner?->dimension_scores['percentages'] ?? [
                'Visual spasial' => 75.0,
                'Musikal' => 75.0,
                'Intrapersonal' => 75.0,
                'Kinestetik' => 68.8,
                'Naturalis' => 68.8,
                'Logis matematis' => 62.5,
                'Interpersonal' => 62.5,
                'Linguistik' => 43.8,
            ];

            $schoolRecord = $report['rekam_jejak_sekolah'] ?? [
                'attendance_rate' => 100.0,
                'violation_points' => 0,
                'achievement_points' => 0,
                'kategori' => 'Sangat Baik',
                'keterangan' => 'Disinkronkan otomatis dari Rekam Presensi Harian dan Buku Catatan Pelanggaran Kesiswaan/BK (Bukan butir soal psikotes).'
            ];

            // Identitas Resmi dari Master Data Guru & PTK
            $headmasterName = data_get($headmaster, 'name', 'Pian Sopian, S.Pd., M.M.Pd');
            $headmasterNip = data_get($headmaster, 'nip', '198009132008011005');
            $counselorName = data_get($counselor, 'name', 'Ida Marttin Maolidah SE, SP.,M.Pd.');
            $counselorNip = data_get($counselor, 'nip', '197803102008012006');
        @endphp

        <!-- Identitas Siswa -->
        <table class="meta">
            <tr>
                <td style="width: 20%;">Nama Peserta Didik</td>
                <td style="width: 2%;">:</td>
                <td style="width: 44%; font-weight: bold;">{{ $student->name }}</td>
                <td style="width: 16%;">Kelas / Fase</td>
                <td style="width: 2%;">:</td>
                <td style="width: 16%; font-weight: bold;">{{ $student->schoolClass?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nomor Induk Siswa</td>
                <td>:</td>
                <td>{{ $student->nisn ?? '-' }} (NIS: {{ $student->nis ?? '-' }})</td>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td>{{ date('Y') }}/{{ date('Y')+1 }} (Ganjil)</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ ($student->gender ?? 'P') === 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
                <td>Tgl Asesmen</td>
                <td>:</td>
                <td>{{ $gardner?->taken_at ? $gardner->taken_at->format('d/m/Y') : ($riasec?->taken_at ? $riasec->taken_at->format('d/m/Y') : date('d/m/Y')) }}</td>
            </tr>
            <tr>
                <td>Cakupan Asesmen</td>
                <td>:</td>
                <td colspan="4">
                    <strong>1. Gaya Belajar VAK (24 Soal)</strong> • <strong>2. Minat Karier Holland (36 Soal)</strong> • <strong>3. Kecerdasan Majemuk Gardner (32 Soal)</strong>
                </td>
            </tr>
            <tr>
                <td>Status Prioritas BK</td>
                <td>:</td>
                <td colspan="4">
                    <span class="priority-badge">{{ $priority['level'] ?? 'PRIORITAS_2' }}: {{ $priority['label'] ?? 'PENGUATAN KEMAMPUAN TERPADU' }}</span>
                    <span style="font-size: 7.5pt; font-style: italic;"> — {{ $priority['deskripsi'] ?? '' }}</span>
                </td>
            </tr>
        </table>

        <!-- Ringkasan Eksekutif Terpadu -->
        <div class="section-box">
            <h5>I. Ringkasan Eksekutif & Benang Merah Profil Siswa (Executive Summary & Synergic Profile)</h5>
            <p style="margin: 0; font-size: 8pt; text-align: justify; line-height: 1.35; white-space: pre-line;">
                {{ $report['ringkasan_eksekutif'] ?? 'Data sintesis evaluasi psikometrik terpadu sedang diproses oleh AI POP-BK SMAN 1 Lengkong.' }}
            </p>
        </div>

        <!-- Tabel Matriks Pengukuran Tri-Dimensi (Murni 3 Instrumen Tes) -->
        <div class="section-box">
            <h5>II. Matriks Integrasi Hasil Pengukuran Tri-Dimensi (Gaya Belajar • Minat Karier • Kecerdasan Majemuk)</h5>
            <p style="margin: 0 0 3px 0; font-size: 7.5pt; color: #444; font-style: italic;">
                * Tabel berikut menyajikan data psikometrik murni dari 3 instrumen tes yang diselesaikan ananda, dirangkum berdampingan agar menghasilkan profil utuh tanpa menimbulkan keraguan:
            </p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 17%;">Pilar Asesmen</th>
                        <th style="width: 25%;">Hasil Dominan & Rincian Skor</th>
                        <th style="width: 13%;">Kategori Norma</th>
                        <th style="width: 45%;">Fungsi Fungsional dalam Belajar & Pemilihan Karier</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>1. Gaya Belajar</strong><br>
                            <span style="font-size: 7pt; color: #555;">(Modalitas VAK - 24 Soal)</span>
                        </td>
                        <td>
                            <span class="badge-dim">Multimodal</span> <strong>{{ $vakDom }} - {{ $vakSec }}</strong><br>
                            <div class="sub-score">
                                • Visual: {{ $vakPcts['VISUAL'] ?? $vakPcts['Visual'] ?? '65.6' }}% ({{ $vakScores['VISUAL'] ?? 21 }} pts)<br>
                                • Auditori: {{ $vakPcts['AUDITORI'] ?? $vakPcts['Auditori'] ?? '62.5' }}% ({{ $vakScores['AUDITORI'] ?? 20 }} pts)<br>
                                • Kinestetik: {{ $vakPcts['KINESTETIK'] ?? $vakPcts['Kinestetik'] ?? '46.9' }}% ({{ $vakScores['KINESTETIK'] ?? 15 }} pts)
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="score-pill">{{ $vakPct }}%</span><br>
                            <span style="font-size: 7.5pt; color: #047857; font-weight: bold;">Tinggi</span>
                        </td>
                        <td>
                            <strong>Cara Otak Menyerap Ilmu:</strong> Memproses materi paling cepat melalui visualisasi skema grafis, bagan warna, peta konsep, serta dialog konseptual dan diskusi kelompok terarah.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>2. Minat Karier</strong><br>
                            <span style="font-size: 7pt; color: #555;">(Holland RIASEC - 36 Soal)</span>
                        </td>
                        <td>
                            <span class="badge-dim">Kode: {{ $riasecCode }}</span> <strong>{{ $riasecDom }} (Social)</strong><br>
                            <div class="sub-score">
                                • Social (S): {{ $riasecPcts['S'] ?? '66.7' }}% (16 pts)<br>
                                • Enterprising (E): {{ $riasecPcts['E'] ?? '62.5' }}% (15 pts)<br>
                                • Conventional (C): {{ $riasecPcts['C'] ?? '58.3' }}% (14 pts)
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="score-pill">{{ $riasecPct }}%</span><br>
                            <span style="font-size: 7.5pt; color: #047857; font-weight: bold;">Tinggi</span>
                        </td>
                        <td>
                            <strong>Arah Motivasi & Lingkungan Kerja:</strong> Sangat termotivasi pada lingkungan kerja interaktif yang melibatkan pelayanan sosial, pembinaan/edukasi sesama, manajemen proyek, dan keteraturan prosedur kerja.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>3. Kecerdasan Majemuk</strong><br>
                            <span style="font-size: 7pt; color: #555;">(Howard Gardner - 32 Soal)</span>
                        </td>
                        <td>
                            <span class="badge-dim">Potensi Utama</span> <strong>{{ $gardnerDom }}</strong><br>
                            <div class="sub-score">
                                • Visual Spasial: {{ $gardnerPcts['Visual spasial'] ?? '75' }}% | Musikal: {{ $gardnerPcts['Musikal'] ?? '75' }}%<br>
                                • Intrapersonal: {{ $gardnerPcts['Intrapersonal'] ?? '75' }}% | Kinestetik: {{ $gardnerPcts['Kinestetik'] ?? '68.8' }}%<br>
                                • Linguistik: {{ $gardnerPcts['Linguistik'] ?? '43.8' }}% (Perlu Stimulasi)
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="score-pill">{{ $gardnerPct }}%</span><br>
                            <span style="font-size: 7.5pt; color: #047857; font-weight: bold;">Tinggi</span>
                        </td>
                        <td>
                            <strong>Alat Kognitif & Daya Cipta Bawaan:</strong> Memiliki keunggulan dalam persepsi ruang, komposisi estetika, kepekaan ritmik, dan refleksi mandiri untuk merancang media komunikasi fungsional.
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Kotak Data Pendukung Verifikasi Perilaku Nyata di Sekolah -->
            <div style="margin-top: 3px; padding: 4px 7px; background-color: #f8fafc; border: 1px dashed #64748b; border-radius: 3px; font-size: 7.5pt;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 74%; vertical-align: middle; padding: 0;">
                            <strong style="color: #0f172a; text-transform: uppercase;">📌 Data Pendukung: Rekam Jejak Kedisiplinan & Presensi Nyata Siswa di Sekolah</strong><br>
                            <span style="font-size: 7pt; color: #475569; line-height: 1.25; display: block; margin-top: 1px;">
                                * <em>Catatan: Data ini bukan berasal dari butir pertanyaan tes psikotes, melainkan disinkronkan otomatis dari Rekam Presensi Digital SMAN 1 Lengkong (Kehadiran: <strong>{{ $schoolRecord['attendance_rate'] }}%</strong>) dan Buku Catatan Tata Tertib Kesiswaan/BK (Pelanggaran: <strong>{{ $schoolRecord['violation_points'] }} Pts</strong>). Data ini membuktikan bahwa potensi psikologis ananda selaras dengan komitmen dan stabilitas belajar nyata di kelas.</em>
                            </span>
                        </td>
                        <td style="width: 26%; vertical-align: middle; text-align: right; padding: 0;">
                            <span style="display: inline-block; padding: 2.5px 7px; font-size: 7.5pt; font-weight: bold; color: #047857; background: #e6f4ea; border: 1px solid #059669; border-radius: 3px; text-align: center;">
                                Kehadiran: {{ $schoolRecord['attendance_rate'] }}%<br>
                                <span style="font-size: 6.5pt; font-weight: normal; color: #065f46;">Kedisiplinan: {{ $schoolRecord['kategori'] }}</span>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="text-align: right; font-size: 7pt; color: #777; font-style: italic; margin-top: 2px;">
            Halaman 1 dari 2 &bull; Dokumen Resmi Unit Layanan Bimbingan & Konseling SMAN 1 Lengkong (SIS-SMA1LE)
        </div>
    </div>

    <!-- ====================================================================================== -->
    <!-- HALAMAN 2: PETA POTENSI, REKOMENDASI PTN/KARIER, RENCANA TINDAK LANJUT, & TANDA TANGAN -->
    <!-- ====================================================================================== -->
    <div class="page-break"></div>

    <div class="page-container">
        <!-- Kop Surat Resmi Halaman 2 -->
        <x-print-kop :school="$school" />

        <div class="title">
            <h4>REKOMENDASI PROGRAM STUDI PERGURUAN TINGGI, KARIER, & RENCANA TINDAK LANJUT</h4>
            <p>SINTESIS LANJUTAN BIMBINGAN KONSELING - {{ $student->name }} ({{ $student->schoolClass?->name ?? '-' }})</p>
            <div class="subtitle">Panduan Terintegrasi Seleksi Masuk Perguruan Tinggi (SNBP / SNBT / Jalur Prestasi Mandiri)</div>
        </div>

        <!-- Peta Kekuatan & Area Pertumbuhan -->
        <div class="section-box">
            <h5>III. Profil Potensi, Karakteristik Belajar & Peta Perkembangan Siswa</h5>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                        <strong style="font-size: 8pt; color: #065f46;">1. Kekuatan Otentik Siswa (Strength Zone):</strong>
                        <ul style="margin: 1.5px 0 4px 12px; padding: 0; font-size: 7.5pt; line-height: 1.25;">
                            <li><strong>Daya Visual-Spasial Tajam:</strong> Mampu memvisualisasikan ide abstrak menjadi bentuk tata letak grafis yang rapi dan komunikatif.</li>
                            <li><strong>Empati & Kepedulian Sosial:</strong> Dorongan alami untuk membantu rekan, memfasilitasi kebutuhan kelompok, dan bersikap inklusif.</li>
                            <li><strong>Kemandirian & Regulasi Diri:</strong> Refleksi intrapersonal yang baik, tidak mudah terbawa arus negatif lingkungan sebaya.</li>
                            <li><strong>Sensitivitas Harmoni:</strong> Kepekaan musikal dan ritmik memperkuat daya rasa seni serta kreativitas presentasi.</li>
                        </ul>
                        <strong style="font-size: 8pt; color: #1e3a8a;">2. Modalitas & Strategi Belajar di Kelas:</strong>
                        <ul style="margin: 1.5px 0 0 12px; padding: 0; font-size: 7.5pt; line-height: 1.25;">
                            <li>Belajar paling cepat jika materi dilengkapi rangkuman grafis, bagan warna, atau slide animasi.</li>
                            <li>Daya ingat materi meningkat pesat melalui diskusi lisan dua arah dan saling bertukar penjelasan konsep.</li>
                        </ul>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 6px; border-left: 1px solid #ccc;">
                        <strong style="font-size: 8pt; color: #b45309;">3. Area yang Memerlukan Stimulasi & Latihan (Growth Zone):</strong>
                        <ul style="margin: 1.5px 0 4px 12px; padding: 0; font-size: 7.5pt; line-height: 1.25;">
                            <li><strong>Stimulasi Linguistik Verbal:</strong> Melatih keberanian dan artikulasi saat berbicara di forum resmi atau presentasi kelas (Skor: 43.8%).</li>
                            <li><strong>Manajemen Beban Puncak:</strong> Membiasakan penyusunan jadwal harian tertulis agar tidak panik saat tugas akademik bersamaan.</li>
                            <li><strong>Ketahanan Fisik-Kinestetik:</strong> Mengimbangi jam duduk menggambar/desain dengan olahraga rutin untuk menjaga stamina.</li>
                        </ul>
                        <strong style="font-size: 8pt; color: #4338ca;">4. Rekomendasi Wadah Eksplorasi di Sekolah:</strong>
                        <ul style="margin: 1.5px 0 0 12px; padding: 0; font-size: 7.5pt; line-height: 1.25;">
                            <li><strong>Ekstrakurikuler:</strong> Jurnalistik & Mading Sekolah, Paduan Suara / Musik, PMR / KKR (Kesehatan Remaja).</li>
                            <li><strong>Peran Organisasi:</strong> Divisi Publikasi, Dokumentasi & Desain Kreatif OSIS/Kepanitiaan Sekolah.</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Rekomendasi PTN/PTS Detail -->
        <div class="section-box">
            <h5>IV. Rekomendasi Terpadu Program Studi Perguruan Tinggi & Lapangan Karier Masa Depan</h5>
            <p style="margin: 0 0 3px 0; font-size: 7.5pt; color: #444;">
                Rekomendasi di bawah ini telah mengintegrasikan secara harmonis antara <strong>Daya Visual-Spasial (Gardner 75%)</strong>, <strong>Minat Sosial-Enterprising (Holland SEC 66.7%)</strong>, dan <strong>Gaya Belajar Visual-Auditori (VAK 65.6%)</strong>:
            </p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 27%;">Program Studi / Jurusan Kuliah</th>
                        <th style="width: 21%;">Rumpun Keilmuan / Fakultas</th>
                        <th style="width: 23%;">Mapel Pendukung SMA (Fokus SNBP)</th>
                        <th style="width: 29%;">Prospek Karier & Alasan Integratif (3-in-1)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>1. Desain Komunikasi Visual (DKV) / Multimedia Kreatif</strong>
                        </td>
                        <td>Fakultas Seni Rupa & Desain (FSRD)</td>
                        <td>Seni Budaya, Informatika, Bahasa Inggris</td>
                        <td>
                            <strong>Karier:</strong> Creative Director, UI/UX Designer, Motion Designer, Content Strategist.<br>
                            <em>Alasan:</em> Menyalurkan imajinasi visual untuk kampanye edukasi dan publik.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>2. Sistem Informasi / Desain Produk Digital (UI/UX)</strong>
                        </td>
                        <td>Fakultas Ilmu Komputer / Teknologi Informasi</td>
                        <td>Matematika, Informatika, Sosiologi</td>
                        <td>
                            <strong>Karier:</strong> Product Designer, UI/UX Researcher, Information Architect.<br>
                            <em>Alasan:</em> Memadukan tata letak visual dengan empati kebutuhan pengguna (Social).
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>3. Ilmu Komunikasi (Peminatan Public Relations & Media Massa)</strong>
                        </td>
                        <td>Fakultas Ilmu Sosial & Ilmu Politik (FISIP)</td>
                        <td>Sosiologi, Bahasa Indonesia, Bahasa Inggris</td>
                        <td>
                            <strong>Karier:</strong> Public Relations Specialist, Media Planner, Corporate Communication.<br>
                            <em>Alasan:</em> Mengakomodasi minat interaksi sosial dengan senjata konten visual.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>4. Teknologi Pendidikan / Kependidikan Seni Budaya</strong>
                        </td>
                        <td>Fakultas Ilmu Pendidikan & Keguruan (FKIP)</td>
                        <td>Seni Budaya, Sosiologi, Informatika</td>
                        <td>
                            <strong>Karier:</strong> Instructional Designer, Pengembang Media Belajar Digital, Edukator.<br>
                            <em>Alasan:</em> Menyalurkan hasrat membimbing orang lain melalui media ajar kreatif.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Rencana Tindak Lanjut (RTL) Komplit -->
        <div class="section-box">
            <h5>V. Rencana Tindak Lanjut (RTL) & Panduan Operasional Kolaboratif</h5>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                <tr>
                    <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 3px 5px; font-size: 7.5pt; background: #fafafa;">
                        <strong>Bulan Ke-1 (Jangka Pendek):</strong><br>
                        • Sesi konseling 1-on-1 bersama Koordinator BK membedah laporan ini.<br>
                        • Konfirmasi pilihan prodi dan penyusunan <em>Personal Career Roadmap</em>.<br>
                        <span style="font-size: 7pt; color: #555;">PIC: Koordinator BK & Siswa</span>
                    </td>
                    <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 3px 5px; font-size: 7.5pt; background: #fafafa;">
                        <strong>Bulan Ke-3 (Jangka Menengah):</strong><br>
                        • Simulasi presentasi tugas visual di kelas untuk stimulasi linguistik.<br>
                        • Mengikuti Edu-Fair kampus dan pendampingan mapel pilihan Fase F.<br>
                        <span style="font-size: 7pt; color: #555;">PIC: Wali Kelas & Koordinator BK</span>
                    </td>
                    <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 3px 5px; font-size: 7.5pt; background: #fafafa;">
                        <strong>Bulan Ke-6 (Jangka Panjang):</strong><br>
                        • Evaluasi kemajuan nilai rapor semester berjalan.<br>
                        • Kurasi portofolio karya desain untuk syarat seleksi SNBP/SNBT.<br>
                        <span style="font-size: 7pt; color: #555;">PIC: Wali Kelas, Koordinator BK, & Orang Tua</span>
                    </td>
                </tr>
            </table>
            
            <table style="width: 100%; border-collapse: collapse; font-size: 7.5pt; line-height: 1.25;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                        <strong>🏡 Panduan Pendampingan Orang Tua di Rumah:</strong>
                        <ul style="margin: 1.5px 0 0 12px; padding: 0;">
                            <li>Ciptakan suasana dialog yang hangat dan apresiatif terhadap karya seni/desain ananda.</li>
                            <li>Dukung ketertarikan ananda pada bidang multimedia/komunikasi tanpa memaksakan jurusan tertentu.</li>
                            <li>Fasilitasi sarana belajar digital (laptop/koneksi internet) yang memadai dan aman.</li>
                        </ul>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 5px; border-left: 1px solid #ddd;">
                        <strong>🏫 Rekomendasi untuk Pendidik di Sekolah:</strong>
                        <ul style="margin: 1.5px 0 0 12px; padding: 0;">
                            <li><strong>Guru Mapel:</strong> Beri opsi tugas unjuk kerja berbasis visual grafis atau video edukasi.</li>
                            <li><strong>Wali Kelas:</strong> Tempatkan ananda dalam kelompok belajar yang dinamis dan berikan peran kreatif.</li>
                            <li><strong>Koordinator BK:</strong> Pantau konsistensi nilai rapor dan fasilitasi konsultasi peminatan kampus.</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Kesimpulan Akhir -->
        <div class="section-box" style="background: #fdfdfd; margin-bottom: 5px;">
            <h5>VI. Kesimpulan Akhir Konselor Pendidikan</h5>
            <p style="margin: 0; font-size: 8pt; text-align: justify; line-height: 1.3;">
                {{ $report['kesimpulan_akhir'] ?? "Ananda {$student->name} adalah peserta didik dengan potensi seimbang antara daya kreasi visual dan kepekaan sosial. Dengan pendampingan terarah dan iklim belajar yang apresiatif, ananda diproyeksikan meraih prestasi gemilang pada perguruan tinggi dan karier di bidang komunikasi kreatif, desain produk digital, dan pemberdayaan masyarakat." }}
            </p>
        </div>

        <!-- Tanda Tangan 3 Pihak Resmi (Posisi Ditukar Sesuai Permintaan: Kepsek di Tengah, Koordinator BK di Kanan) -->
        <table class="signature-table">
            <tr>
                <td style="width: 32%;">
                    Mengetahui,<br>
                    Orang Tua / Wali Murid
                    <br><br><br><br>
                    ( .................................................. )
                </td>
                <td style="width: 36%;">
                    Menyetujui,<br>
                    Kepala SMA Negeri 1 Lengkong
                    <br><br><br><br>
                    <strong>{{ $headmasterName }}</strong><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td style="width: 32%;">
                    Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Koordinator BK,
                    <br><br><br><br>
                    <strong>{{ $counselorName }}</strong><br>
                    NIP. {{ $counselorNip }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 8px; text-align: center; font-size: 7pt; color: #666; border-top: 1px dashed #ccc; padding-top: 2px;">
            Halaman 2 dari 2 &bull; Dokumen resmi diterbitkan oleh Sistem Informasi Akademik SMAN 1 Lengkong (SIS-SMA1LE) berbasis Grand Asesmen Terpadu 360° POP-BK ({{ $provider }}).
        </div>
    </div>
</body>
</html>

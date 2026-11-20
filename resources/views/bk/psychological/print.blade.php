<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Asesmen Psikotes AI - {{ $result->student?->name }}</title>
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
        .page-container {
            page-break-after: always;
            break-after: page;
            position: relative;
            padding: 3mm 5mm;
        }
        .page-container:last-child {
            page-break-after: auto;
            break-after: auto;
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
        .bar-container {
            width: 100%;
            height: 8px;
            background-color: #eee;
            border: 1px solid #777;
            border-radius: 2px;
        }
        .bar-fill {
            height: 100%;
            background-color: #222;
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
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 8px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 15px; background: #059669; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 10pt; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            🖨️ Cetak Dokumen Resmi (Print A4)
        </button>
    </div>

    @php
        $report = $result->ai_report ?? [];
        $priority = $report['prioritas_keputusan'] ?? [
            'level' => $result->priority_level ?? 'PRIORITAS_1',
            'label' => $result->priority_label ?? 'PENGEMBANGAN POTENSI',
            'deskripsi' => 'Peserta didik memiliki potensi unggul yang siap diakselerasi melalui pembinaan bakat berkesinambungan.',
            'alasan' => 'Skor dominan berada pada rentang tinggi dan konsisten.'
        ];

        // Format nama & NIP dari Master Data Guru & PTK
        $headmasterName = data_get($headmaster, 'name', 'Pian Sopian, S.Pd., M.M.Pd');
        $headmasterNip = data_get($headmaster, 'nip', '198009132008011005');
        $counselorName = data_get($counselor, 'name', 'Ida Marttin Maolidah SE, SP.,M.Pd.');
        $counselorNip = data_get($counselor, 'nip', '197803102008012006');
    @endphp

    <!-- ========================================== -->
    <!-- HALAMAN 1: IDENTITAS & HASIL SKOR DIMENSI -->
    <!-- ========================================== -->
    <div class="page-container">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <!-- Judul Dokumen -->
        <div class="title">
            <h4>LAPORAN ASESMEN PSIKOLOGIS & REKOMENDASI KARIER PESERTA DIDIK</h4>
            <p>UNIT LAYANAN BIMBINGAN DAN KONSELING (POP-BK) BERBASIS AI</p>
        </div>

        <!-- Identitas Siswa -->
        <table class="meta">
            <tr>
                <td style="width: 22%;">Nama Peserta Didik</td>
                <td style="width: 2%;">:</td>
                <td style="width: 44%; font-weight: bold;">{{ $result->student?->name }}</td>
                <td style="width: 16%;">Kelas / Fase</td>
                <td style="width: 2%;">:</td>
                <td style="width: 16%; font-weight: bold;">{{ $result->student?->schoolClass?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nomor Induk Siswa (NISN)</td>
                <td>:</td>
                <td>{{ $result->student?->nisn ?? '-' }} (NIS: {{ $result->student?->nis ?? '-' }})</td>
                <td>Tgl Pengujian</td>
                <td>:</td>
                <td>{{ $result->taken_at ? $result->taken_at->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Instrumen Asesmen</td>
                <td>:</td>
                <td colspan="4"><strong>{{ $result->test?->name }}</strong> ({{ $result->profile_label }})</td>
            </tr>
            <tr>
                <td>Prioritas Keputusan BK</td>
                <td>:</td>
                <td colspan="4">
                    <span class="priority-badge">{{ $priority['level'] ?? 'PRIORITAS_1' }}: {{ $priority['label'] ?? 'PENGEMBANGAN POTENSI' }}</span>
                    <span style="font-size: 7.5pt; font-style: italic;"> — {{ $priority['deskripsi'] ?? '' }}</span>
                </td>
            </tr>
        </table>

        <!-- Ringkasan Eksekutif -->
        <div class="section-box">
            <h5>I. Ringkasan Eksekutif (Executive Summary)</h5>
            <p style="margin: 0; font-size: 8pt; text-align: justify; line-height: 1.35;">
                {{ $report['ringkasan_eksekutif'] ?? $result->analysis_summary }}
            </p>
        </div>

        <!-- Tabel Persebaran Skor Dimensi -->
        <div class="section-box">
            <h5>II. Hasil Pengukuran Dimensi & Kategori Norma</h5>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Aspek / Dimensi yang Diukur</th>
                        <th style="width: 14%;">Skor Mentah</th>
                        <th style="width: 14%;">Persentase (%)</th>
                        <th style="width: 14%;">Kategori</th>
                        <th style="width: 18%;">Proporsi Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $percentages = $result->dimension_scores['percentages'] ?? [];
                        $rawScores = $result->dimension_scores['raw'] ?? [];
                        $no = 1;
                    @endphp
                    @foreach($percentages as $dim => $pct)
                        @php
                            $kategori = match(true) {
                                $pct >= 80 => 'Sangat Tinggi',
                                $pct >= 65 => 'Tinggi',
                                $pct >= 50 => 'Sedang',
                                default => 'Perlu Stimulasi',
                            };
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $no++ }}</td>
                            <td><strong>{{ ucfirst(strtolower(str_replace('_', ' ', $dim))) }}</strong></td>
                            <td style="text-align: center;">{{ $rawScores[$dim] ?? 0 }} pts</td>
                            <td style="text-align: center; font-weight: bold;">{{ $pct }}%</td>
                            <td style="text-align: center;">{{ $kategori }}</td>
                            <td>
                                <div class="bar-container">
                                    <div class="bar-fill" style="width: {{ $pct }}%;"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="text-align: right; font-size: 7pt; color: #777; font-style: italic; margin-top: 4px;">
            Halaman 1 dari 2 &bull; Lanjutan analisis potensi, rekomendasi studi, dan lembar pengesahan ada di Halaman 2
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 2: PROFIL POTENSI, REKOMENDASI KARIER, RTL, & PENGESAHAN 3 PIHAK -->
    <!-- ========================================================================= -->
    <div class="page-break"></div>

    <div class="page-container">
        <!-- Kop Surat Resmi Halaman 2 -->
        <x-print-kop :school="$school" />

        <div class="title">
            <h4>ANALISIS POTENSI, REKOMENDASI PROGRAM STUDI & RENCANA TINDAK LANJUT</h4>
            <p>ASESMEN {{ strtoupper($result->test?->name ?? 'PSIKOTES') }} - {{ $result->student?->name }} ({{ $result->student?->schoolClass?->name ?? '-' }})</p>
        </div>

        <!-- Profil Potensi & Kekuatan -->
        @php
            $potensi = $report['profil_potensi'] ?? [];
            $growth = $report['kekuatan_dan_area_pengembangan'] ?? [];
        @endphp
        <div class="section-box">
            <h5>III. Profil Potensi & Kekuatan Siswa</h5>
            <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                        <strong>Potensi Intelektual & Bakat:</strong>
                        <p style="margin: 1.5px 0 4px 0; line-height: 1.25;">{{ $potensi['potensi_intelektual'] ?? '-' }}</p>
                        <strong>Kekuatan Utama Siswa:</strong>
                        <ul style="margin: 1.5px 0 0 12px; padding: 0; line-height: 1.25;">
                            @foreach($growth['kekuatan_utama'] ?? ['Konsentrasi tinggi pada bidang peminatan'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 6px; border-left: 1px solid #ccc;">
                        <strong>Potensi Sosial & Kepemimpinan:</strong>
                        <p style="margin: 1.5px 0 4px 0; line-height: 1.25;">{{ $potensi['potensi_sosial'] ?? '-' }}</p>
                        <strong>Area yang Perlu Dikembangkan:</strong>
                        <ul style="margin: 1.5px 0 0 12px; padding: 0; line-height: 1.25;">
                            @foreach($growth['area_pengembangan'] ?? ['Manajemen waktu dan penyusunan prioritas belajar'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Rekomendasi PTN & Karier -->
        @php
            $ptKarir = $report['rekomendasi_perguruan_tinggi_dan_karir'] ?? [];
        @endphp
        <div class="section-box">
            <h5>IV. Rekomendasi Program Studi Perguruan Tinggi & Arah Karier Masa Depan</h5>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 32%;">Program Studi / Jurusan</th>
                        <th style="width: 25%;">Rumpun Keilmuan</th>
                        <th style="width: 43%;">Alasan Kesesuaian & Prospek</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ptKarir['program_studi'] ?? [
                        ['jurusan' => 'Teknologi Informasi / Komputer', 'fakultas' => 'Teknologi', 'alasan' => 'Sesuai dengan cara berpikir analitis.'],
                        ['jurusan' => 'Manajemen & Komunikasi Bisnis', 'fakultas' => 'Ekonomi & Sosial', 'alasan' => 'Mendukung bakat persuasi.'],
                        ['jurusan' => 'Sains Terapan & Desain', 'fakultas' => 'MIPA / Seni', 'alasan' => 'Menyalurkan daya kreativitas fungsional.']
                    ] as $ps)
                        <tr>
                            <td style="font-weight: bold;">{{ $ps['jurusan'] ?? ($ps['major'] ?? '-') }}</td>
                            <td>{{ $ps['fakultas'] ?? ($ps['field'] ?? '-') }}</td>
                            <td>{{ $ps['alasan'] ?? ($ps['prospect'] ?? '-') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Rencana Tindak Lanjut (RTL) & Panduan Kolaborasi -->
        @php
            $rtl = $report['rencana_tindak_lanjut'] ?? [];
            $stakeholders = $report['rekomendasi_pihak_sekolah'] ?? [];
        @endphp
        <div class="section-box">
            <h5>V. Rencana Tindak Lanjut (RTL) & Rekomendasi Kolaboratif</h5>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 3px;">
                <tr>
                    <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 3px 5px; font-size: 7.5pt; background: #fafafa;">
                        <strong>Bulan Ke-1 (Jangka Pendek):</strong><br>
                        {{ $rtl['jangka_pendek_1_bulan'][0]['aktivitas'] ?? 'Sesi bimbingan konseling individu membedah hasil asesmen.' }}<br>
                        <span style="font-size: 7pt; color: #555;">PIC: {{ $rtl['jangka_pendek_1_bulan'][0]['pic'] ?? 'Guru BK' }}</span>
                    </td>
                    <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 3px 5px; font-size: 7.5pt; background: #fafafa;">
                        <strong>Bulan Ke-3 (Jangka Menengah):</strong><br>
                        {{ $rtl['jangka_menengah_3_bulan'][0]['aktivitas'] ?? 'Eksplorasi wawasan karier dan penentuan target SNBP/SNBT.' }}<br>
                        <span style="font-size: 7pt; color: #555;">PIC: {{ $rtl['jangka_menengah_3_bulan'][0]['pic'] ?? 'Guru BK & Wali Kelas' }}</span>
                    </td>
                    <td style="width: 33.3%; vertical-align: top; border: 1px solid #ddd; padding: 3px 5px; font-size: 7.5pt; background: #fafafa;">
                        <strong>Bulan Ke-6 (Jangka Panjang):</strong><br>
                        {{ $rtl['jangka_panjang_6_bulan'][0]['aktivitas'] ?? 'Evaluasi kemajuan nilai rapor dan penyusunan portofolio prestasi.' }}<br>
                        <span style="font-size: 7pt; color: #555;">PIC: {{ $rtl['jangka_panjang_6_bulan'][0]['pic'] ?? 'Orang Tua & Guru' }}</span>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 3px; font-size: 7.5pt; line-height: 1.25;">
                <strong>Catatan Pendampingan Orang Tua:</strong> {{ $stakeholders['orang_tua'] ?? 'Mendukung dengan iklim keluarga hangat dan mengapresiasi proses belajar ananda.' }}
            </div>
        </div>

        <!-- Tanda Tangan Resmi 3 Pihak (Posisi Ditukar Sesuai Permintaan: Kepsek di Tengah, Koordinator BK di Kanan) -->
        <table class="signature-table">
            <tr>
                <td style="width: 32%;">
                    Mengetahui,<br>
                    Orang Tua / Wali Murid<br><br><br><br>
                    ( .................................................. )
                </td>
                <td style="width: 36%;">
                    Menyetujui,<br>
                    Kepala SMA Negeri 1 Lengkong<br><br><br><br>
                    <strong>{{ $headmasterName }}</strong><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td style="width: 32%;">
                    Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
                    Koordinator BK,<br><br><br><br>
                    <strong>{{ $counselorName }}</strong><br>
                    NIP. {{ $counselorNip }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 8px; font-size: 7pt; text-align: center; color: #666; border-top: 1px dashed #ccc; padding-top: 2px;">
            Halaman 2 dari 2 &bull; Dokumen resmi diproses secara otomatis oleh Sistem Informasi Akademik SMAN 1 Lengkong (SIS-SMA1LE) berbasis {{ $result->ai_provider ?? 'Google Gemini AI' }}.
        </div>
    </div>
</body>
</html>

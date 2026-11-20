<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan AKPD Individu - {{ $student->name }} - {{ $student->schoolClass?->name ?? 'Kelas X' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #111;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header-title {
            text-align: center;
            margin: 10px 0 14px 0;
        }
        .header-title h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            color: #444;
            font-style: italic;
        }
        .student-box {
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 12px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .student-box td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 4px 0;
            border-bottom: 1.5px solid #000;
            padding-bottom: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
        }
        table.data-table th {
            background-color: #f2f2f2 !important;
            font-weight: bold;
            text-align: center;
        }
        .bar-container {
            width: 100%;
            background-color: #e5e7eb;
            height: 10px;
            border-radius: 3px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
        }
        .bar-fill-rose {
            background-color: #e11d48 !important;
            height: 100%;
        }
        .bar-fill-amber {
            background-color: #d97706 !important;
            height: 100%;
        }
        .bar-fill-emerald {
            background-color: #059669 !important;
            height: 100%;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .recom-box {
            border: 1px solid #777;
            padding: 6px 10px;
            background-color: #fafafa !important;
            font-size: 8.5pt;
            margin-bottom: 12px;
        }
        .recom-box ul {
            margin: 3px 0 3px 18px;
            padding: 0;
        }
        .recom-box li {
            margin-bottom: 2px;
        }

        .ttd-container {
            margin-top: 18px;
            width: 100%;
            display: table;
            font-size: 9.5pt;
            page-break-inside: avoid;
        }
        .ttd-row {
            display: table-row;
        }
        .ttd-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 5px;
        }
        .ttd-space {
            height: 55px;
        }
        .footer-note {
            margin-top: 14px;
            border-top: 1px dashed #999;
            padding-top: 4px;
            font-size: 7.5pt;
            color: #666;
            text-align: center;
            font-style: italic;
        }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <x-print-kop :school="$school" />

    <div class="header-title">
        <h2>Laporan Hasil Asesmen Kebutuhan Peserta Didik (AKPD) Individual</h2>
        <p>Instrumen Asesmen Diagnostik Non-Tes Berbasis Panduan Operasional Penyelenggaraan BK (POP-BK) SMA</p>
    </div>

    <!-- Identitas Peserta Didik -->
    <table class="student-box">
        <tr>
            <td width="18%"><strong>Nama Lengkap</strong></td>
            <td width="2%">:</td>
            <td width="38%"><strong>{{ strtoupper($student->name) }}</strong></td>
            <td width="18%"><strong>Kelas / Rombel</strong></td>
            <td width="2%">:</td>
            <td width="22%">{{ $student->schoolClass?->name ?? 'Kelas X' }} (Fase E)</td>
        </tr>
        <tr>
            <td><strong>NISN / NIS</strong></td>
            <td>:</td>
            <td>{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</td>
            <td><strong>Tahun Pelajaran</strong></td>
            <td>:</td>
            <td>{{ \App\Models\AcademicYear::active()?->name ?? date('Y').'/'.(date('Y')+1) }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Kelamin</strong></td>
            <td>:</td>
            <td>{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td><strong>Tanggal Asesmen</strong></td>
            <td>:</td>
            <td>{{ $response->submitted_at ? $response->submitted_at->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    <!-- Bagian I: Matriks Persentase Kebutuhan 4 Bidang Layanan -->
    <div class="section-title">I. Profil Kebutuhan Per Bidang Layanan Bimbingan &amp; Konseling</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="24%">Bidang Layanan BK</th>
                <th width="12%">Butir Dipilih</th>
                <th width="12%">Skor Maksimal</th>
                <th width="12%">Persentase</th>
                <th width="18%">Derajat Kebutuhan</th>
                <th width="17%">Visualisasi Tingkat</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach(['PRIBADI' => 'Bidang Pribadi', 'SOSIAL' => 'Bidang Sosial', 'BELAJAR' => 'Bidang Belajar', 'KARIER' => 'Bidang Karier'] as $fieldKey => $fieldTitle)
                @php
                    $d = $domainAnalysis[$fieldKey] ?? [
                        'count' => 0, 'total_field' => 10, 'percentage' => 0, 'degree' => 'Rendah', 'color' => 'emerald'
                    ];
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td><strong>{{ $fieldTitle }}</strong></td>
                    <td class="text-center">{{ $d['count'] }} butir</td>
                    <td class="text-center">{{ $d['total_field'] }} butir</td>
                    <td class="text-center font-bold">{{ $d['percentage'] }}%</td>
                    <td class="text-center font-bold" style="color: {{ $d['percentage'] >= 50 ? '#be123c' : ($d['percentage'] >= 26 ? '#b45309' : '#047857') }};">
                        {{ strtoupper($d['degree']) }}
                    </td>
                    <td class="text-center">
                        <div class="bar-container">
                            <div class="{{ $d['percentage'] >= 50 ? 'bar-fill-rose' : ($d['percentage'] >= 26 ? 'bar-fill-amber' : 'bar-fill-emerald') }}" style="width: {{ min($d['percentage'], 100) }}%;"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="font-size: 8pt; color: #444; margin-top: -6px; margin-bottom: 8px;">
        * Kriteria Derajat Kebutuhan: &ge; 50% = Sangat Tinggi (Prioritas Utama) | 26% - 49% = Sedang (Perlu Pendampingan) | &le; 25% = Rendah (Stabil/Mandiri).
    </div>

    <!-- Bagian II: Butir Kebutuhan yang Dipilih -->
    <div class="section-title">II. Butir Kebutuhan &amp; Masalah Spesifik yang Dialami Peserta Didik</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="9%">Kode</th>
                <th width="15%">Bidang Layanan</th>
                <th width="23%">Aspek Perkembangan</th>
                <th width="48%">Pernyataan Butir Kebutuhan / Masalah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($selectedQuestions as $idx => $q)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">[{{ $q->code }}]</td>
                    <td class="text-center">{{ $q->field_of_service }}</td>
                    <td>{{ $q->profile_dimension ?? '-' }}</td>
                    <td>{{ $q->statement }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 10px; font-style: italic; color: #666;">
                        Peserta didik tidak memilih butir kebutuhan khusus (perkembangan mandiri dan adaptasi baik).
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bagian III: Rekomendasi Program Layanan BK -->
    <div class="section-title">III. Rekomendasi Program Layanan Bimbingan &amp; Konseling (POP-BK)</div>
    <div class="recom-box">
        <p style="margin: 0 0 4px 0;"><strong>1. Layanan Dasar (Bimbingan Klasikal &amp; Kelompok):</strong></p>
        <ul>
            @foreach($recommendations['layanan_dasar'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>

        <p style="margin: 6px 0 4px 0;"><strong>2. Layanan Responsif (Konseling Individual / Mediasi):</strong></p>
        <ul>
            @foreach($recommendations['layanan_responsif'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>

        <p style="margin: 6px 0 4px 0;"><strong>3. Layanan Peminatan &amp; Perencanaan Individual (Fase F):</strong></p>
        <ul>
            @foreach($recommendations['peminatan_perencanaan'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>

        <p style="margin: 6px 0 4px 0;"><strong>4. Dukungan Sistem:</strong></p>
        <ul>
            @foreach($recommendations['dukungan_sistem'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Tanda Tangan Tripartit -->
    <div class="ttd-container">
        <div class="ttd-row">
            <!-- Kolom 1: Orang Tua -->
            <div class="ttd-cell">
                Mengetahui,<br>
                Orang Tua / Wali Murid
                <div class="ttd-space"></div>
                <strong>( .................................................... )</strong><br>
                <span style="font-size: 8pt; color: #555;">Tanda Tangan &amp; Nama Terang</span>
            </div>

            <!-- Kolom 2: Kepala Sekolah (Tengah) -->
            <div class="ttd-cell">
                Menyetujui,<br>
                Kepala {{ $school->name ?? 'SMA Negeri 1 Lengkong' }}
                <div class="ttd-space"></div>
                <strong><u>{{ $school->headmaster_name ?? 'Drs. H. Pimpinan, M.Pd.' }}</u></strong><br>
                NIP. {{ $school->headmaster_nip ?? '19700101 199501 1 001' }}
            </div>

            <!-- Kolom 3: Koordinator BK (Kanan) -->
            <div class="ttd-cell">
                Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Koordinator BK,
                <div class="ttd-space"></div>
                <strong><u>{{ $bkSign['name'] ?? 'Ida Marttin Maolidah SE, SP.,M.Pd.' }}</u></strong><br>
                NIP. {{ $bkSign['nip'] ?? '197803102008012006' }}
            </div>
        </div>
    </div>

    <div class="footer-note">
        Dokumen resmi asesmen diagnostik non-tes diterbitkan secara digital oleh Sistem Informasi Akademik SMAN 1 Lengkong (SIS-SMA1LE) berbasis POP-BK Kurikulum Merdeka.
    </div>
</body>
</html>

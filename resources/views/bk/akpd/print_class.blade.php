<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan AKPD Kelas - {{ $selectedClass?->name ?? 'Kelas X' }}</title>
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
        .info-box {
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 12px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .info-box td {
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
        <h2>Laporan Profil Kebutuhan Peserta Didik (AKPD) Tingkat Kelas</h2>
        <p>Rekapitulasi Kebutuhan Bimbingan &amp; Konseling Berbasis POP-BK Kurikulum Merdeka Fase E</p>
    </div>

    <!-- Info Kelas -->
    <table class="info-box">
        <tr>
            <td width="20%"><strong>Rombel / Kelas</strong></td>
            <td width="2%">:</td>
            <td width="38%"><strong>{{ $selectedClass?->name ?? 'Seluruh Kelas X' }} (Fase E)</strong></td>
            <td width="18%"><strong>Tahun Pelajaran</strong></td>
            <td width="2%">:</td>
            <td width="20%">{{ \App\Models\AcademicYear::active()?->name ?? date('Y').'/'.(date('Y')+1) }}</td>
        </tr>
        <tr>
            <td><strong>Wali Kelas</strong></td>
            <td>:</td>
            <td>{{ $selectedClass?->homeroomTeacher?->name ?? 'Belum Ditentukan' }}</td>
            <td><strong>Jumlah Peserta Didik</strong></td>
            <td>:</td>
            <td>{{ $classData['totalStudents'] }} Siswa Terdaftar</td>
        </tr>
        <tr>
            <td><strong>Instrumen AKPD</strong></td>
            <td>:</td>
            <td>{{ $akpd->title }}</td>
            <td><strong>Partisipasi Angket</strong></td>
            <td>:</td>
            <td><strong>{{ $classData['respondentsCount'] }} Siswa ({{ $classData['participationRate'] }}%)</strong></td>
        </tr>
    </table>

    <!-- Bagian I: Rata-rata Kebutuhan 4 Bidang Layanan -->
    <div class="section-title">I. Rekapitulasi Rata-rata Kebutuhan Per Bidang Layanan BK</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="6%">No</th>
                <th width="30%">Bidang Layanan BK</th>
                <th width="20%">Rata-rata Kebutuhan (%)</th>
                <th width="22%">Kategori Derajat Kebutuhan</th>
                <th width="22%">Visualisasi Tingkat</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach(['PRIBADI' => 'Bidang Layanan Pribadi', 'SOSIAL' => 'Bidang Layanan Sosial', 'BELAJAR' => 'Bidang Layanan Belajar', 'KARIER' => 'Bidang Layanan Karier'] as $fieldKey => $fieldTitle)
                @php
                    $stat = $classData['fieldAverages'][$fieldKey] ?? ['percentage' => 0, 'priority' => 'Rendah'];
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td><strong>{{ $fieldTitle }}</strong></td>
                    <td class="text-center font-bold" style="font-size: 10pt;">{{ $stat['percentage'] }}%</td>
                    <td class="text-center font-bold" style="color: {{ $stat['percentage'] >= 50 ? '#be123c' : ($stat['percentage'] >= 26 ? '#b45309' : '#047857') }};">
                        {{ strtoupper($stat['priority']) }}
                    </td>
                    <td class="text-center">
                        <div class="bar-container">
                            <div class="{{ $stat['percentage'] >= 50 ? 'bar-fill-rose' : ($stat['percentage'] >= 26 ? 'bar-fill-amber' : 'bar-fill-emerald') }}" style="width: {{ min($stat['percentage'], 100) }}%;"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="font-size: 8pt; color: #444; margin-top: -6px; margin-bottom: 8px;">
        * Kriteria Derajat Kebutuhan: &ge; 50% = Sangat Tinggi (Prioritas Utama) | 26% - 49% = Sedang (Perlu Pendampingan) | &le; 25% = Rendah (Stabil/Mandiri).
    </div>

    <!-- Bagian II: 10 Butir Kebutuhan Tertinggi -->
    <div class="section-title">II. 10 Butir Masalah / Kebutuhan Tertinggi di Kelas (Prioritas RPL BK)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="8%">Kode</th>
                <th width="12%">Bidang</th>
                <th width="20%">Aspek Perkembangan</th>
                <th width="41%">Pernyataan Butir Kebutuhan Siswa</th>
                <th width="14%">Frekuensi / %</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classData['topQuestions'] as $idx => $q)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">[{{ $q->code }}]</td>
                    <td class="text-center">{{ $q->field_of_service }}</td>
                    <td>{{ $q->profile_dimension ?? '-' }}</td>
                    <td>{{ $q->statement }}</td>
                    <td class="text-center font-bold">
                        {{ $q->chosen_count }} siswa<br>
                        <span style="color: #be123c;">({{ $q->percentage }}%)</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 10px; font-style: italic; color: #666;">
                        Belum ada respon yang diinputkan untuk kelas ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bagian III: Rekomendasi Topik Materi RPL BK -->
    <div class="section-title">III. Usulan Topik Materi Rencana Pelaksanaan Layanan (RPL BK 1 Lembar)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Bidang Layanan</th>
                <th width="45%">Usulan Topik Materi Bimbingan Klasikal</th>
                <th width="35%">Tujuan Perkembangan / Sasaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classData['suggestedRplTopics'] as $tIdx => $t)
                <tr>
                    <td class="text-center">{{ $tIdx + 1 }}</td>
                    <td class="text-center font-bold">{{ $t['field'] }}</td>
                    <td><strong>{{ $t['topic'] }}</strong></td>
                    <td>Pengentasan masalah: "{{ $t['statement'] }}"</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 8px; font-style: italic;">
                        Belum ada usulan topik materi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Tripartit -->
    <div class="ttd-container">
        <div class="ttd-row">
            <!-- Kolom 1: Wali Kelas -->
            <div class="ttd-cell">
                Mengetahui,<br>
                Wali Kelas {{ $selectedClass?->name ?? 'Kelas X' }}
                <div class="ttd-space"></div>
                <strong><u>{{ $selectedClass?->homeroomTeacher?->name ?? '( ............................................... )' }}</u></strong><br>
                NIP. {{ $selectedClass?->homeroomTeacher?->nip ?? '..................................................' }}
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
        Dokumen resmi diterbitkan oleh Sistem Informasi Akademik SMAN 1 Lengkong (SIS-SMA1LE) berbasis Panduan POP-BK Kemdikbudristek.
    </div>
</body>
</html>

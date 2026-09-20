<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berkas Perkembangan Siswa - {{ $student->name }} ({{ $student->nis }})</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: 11pt;
            line-height: 1.35;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 130px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: normal;
            letter-spacing: 0.5px;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 15pt;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 9.5pt;
            color: #333;
        }
        .doc-title {
            text-align: center;
            margin: 14px 0 16px 0;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
            font-weight: bold;
        }
        .doc-title span {
            font-size: 10pt;
        }
        table.meta-table {
            width: 100%;
            margin-bottom: 14px;
            font-size: 10.5pt;
        }
        table.meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
            font-size: 10pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 14px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .ai-summary-box {
            border: 1px solid #777;
            padding: 8px 10px;
            background: #fafafa;
            font-size: 10pt;
            margin-bottom: 14px;
            border-radius: 4px;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 10pt;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 4px;
        }
        .sign-space {
            height: 60px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Floating Print Button (Hidden on actual print) -->
    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 999; background: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; font-weight: bold; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 12px;">
            🖨️ Cetak Dokumen Sekarang
        </button>
    </div>

    <!-- Official School Header / Kop Surat -->
    @if(!empty($kopImageUrl))
        <div class="kop-image-wrapper">
            <img src="{{ $kopImageUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
        </div>
    @else
        <div class="header">
            <h3>PEMERINTAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</h3>
            <h3>DINAS PENDIDIKAN</h3>
            <h4 style="margin: 2px 0; font-size: 11pt; font-weight: normal;">{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</h4>
            <h2>{{ strtoupper($schoolName) }}</h2>
            <p>NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}</p>
            <p>{{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat' }} • Email: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }} • Web: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }}</p>
        </div>
    @endif

    <div class="doc-title">
        <h4>LAPORAN PERKEMBANGAN SISWA TERPADU</h4>
        <span>Tahun Ajaran {{ $student->academicYear?->name ?? '2026/2027' }} — Semester Ganjil</span>
    </div>

    <!-- Biodata Siswa -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%;">Nama Lengkap</td>
            <td style="width: 2%;">:</td>
            <td style="width: 40%; font-weight: bold;">{{ $student->name }}</td>
            <td style="width: 18%;">Tahun Pelajaran</td>
            <td style="width: 2%;">:</td>
            <td style="width: 20%;">{{ $student->academicYear?->name ?? '2026/2027' }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>:</td>
            <td>{{ $student->nis }} / {{ $student->nisn }}</td>
            <td>Kelas / Jurusan</td>
            <td>:</td>
            <td>{{ $student->schoolClass?->name ?? '-' }} / {{ $student->schoolClass?->major ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td>Wali Kelas</td>
            <td>:</td>
            <td>{{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}</td>
            <td>Status Karakter</td>
            <td>:</td>
            <td>{{ $dossier['ai_analysis']['metrics']['habit_status'] }}</td>
        </tr>
    </table>

    <!-- Ringkasan Nilai dan Metrik Terpisah -->
    <div class="section-title">I. REKAPITULASI 4 PILAR PERKEMBANGAN SISWA</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Presensi Kehadiran</th>
                <th>Total Poin Prestasi</th>
                <th>Total Poin Pelanggaran</th>
                <th>Konsistensi 7 Kebiasaan</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align: center; font-weight: bold;">
                <td>{{ $student->attendancePercentage() }}% Hadir</td>
                <td style="color: #b45309;">{{ $student->totalAchievementPoints() }} Poin ({{ $dossier['reward_status']['level'] }})</td>
                <td style="color: #b91c1c;">{{ $student->totalViolationPoints() }} Poin ({{ $dossier['guidance_status']['level'] }})</td>
                <td style="color: #047857;">{{ $dossier['ai_analysis']['metrics']['habit_score'] }}% ({{ $dossier['ai_analysis']['metrics']['habit_status'] }})</td>
            </tr>
        </tbody>
    </table>

    <!-- AI Synthesis Summary -->
    <div class="section-title">II. RINGKASAN ANALISIS PERKEMBANGAN (AI SYSTEM SUMMARY)</div>
    <div class="ai-summary-box">
        <strong>Uraian Objektif Sistem:</strong><br>
        {{ $dossier['ai_analysis']['ringkasan'] }}
    </div>

    <!-- Catatan Prestasi -->
    <div class="section-title">III. DAFTAR PRESTASI TERVERIFIKASI (KATEGORI R1 - R10)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 12%;">Kategori</th>
                <th>Nama Capaian Prestasi</th>
                <th style="width: 15%;">Tingkat</th>
                <th style="width: 10%;">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dossier['achievements'] as $idx => $ach)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $ach->date->format('d/m/Y') }}</td>
                    <td>{{ $ach->category->code }}</td>
                    <td>{{ $ach->title }}</td>
                    <td>{{ $ach->level }}</td>
                    <td style="text-align: center; font-weight: bold;">+{{ $ach->points }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; font-style: italic;">Belum ada catatan prestasi yang terverifikasi.</td>
                </tr>
            @endforelse
            <tr style="font-weight: bold; background: #fcfcfc;">
                <td colspan="5" style="text-align: right;">TOTAL POIN PRESTASI KUMULATIF:</td>
                <td style="text-align: center;">{{ $student->totalAchievementPoints() }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Catatan Pelanggaran Tata Tertib -->
    <div class="section-title">IV. DAFTAR PELANGGARAN TATA TERTIB (KATEGORI P1 - P6)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 12%;">Kategori</th>
                <th>Bentuk Pelanggaran</th>
                <th style="width: 20%;">Lokasi</th>
                <th style="width: 10%;">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dossier['violations'] as $idx => $viol)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $viol->date->format('d/m/Y') }}</td>
                    <td>{{ $viol->category->code }}</td>
                    <td>{{ $viol->title }}</td>
                    <td>{{ $viol->location ?? '-' }}</td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">{{ $viol->points }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada catatan pelanggaran tata tertib (Disiplin Bersih).</td>
                </tr>
            @endforelse
            <tr style="font-weight: bold; background: #fcfcfc;">
                <td colspan="5" style="text-align: right;">TOTAL POIN PELANGGARAN KUMULATIF:</td>
                <td style="text-align: center; color: #b91c1c;">{{ $student->totalViolationPoints() }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Orang Tua / Wali Siswa,
                <div class="sign-space"></div>
                <strong>( .................................................. )</strong>
            </td>
            <td>
                {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Wali Kelas,
                <div class="sign-space"></div>
                <strong>{{ $student->schoolClass?->homeroomTeacher?->name ?? '( .................................................. )' }}</strong><br>
                NIP. {{ $student->schoolClass?->homeroomTeacher?->nip ?? '........................' }}
            </td>
            <td>
                Mengetahui,<br>
                Kepala SMAN 1 Lengkong,
                <div class="sign-space"></div>
                <strong>( .................................................. )</strong><br>
                NIP. ........................................
            </td>
        </tr>
    </table>

</body>
</html>

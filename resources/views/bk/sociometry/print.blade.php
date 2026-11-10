<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sosiometri Kelas - {{ $sociometry->schoolClass?->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 15mm 20mm; }
        .title { text-align: center; margin-bottom: 15px; }
        .title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 2px 0; font-size: 10pt; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { padding: 3px 4px; vertical-align: top; font-size: 10pt; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #333; padding: 5px 6px; font-size: 9.5pt; }
        table.data-table th { background-color: #f1f5f9; text-transform: uppercase; font-weight: bold; text-align: center; }
        .section-box { border: 1px solid #333; padding: 8px 10px; margin-bottom: 15px; font-size: 10pt; }
        .section-box h5 { margin: 0 0 4px 0; font-size: 10.5pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
        .signature-table { width: 100%; margin-top: 25px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 50%; vertical-align: top; font-size: 10.5pt; }
        @media print {
            body { margin: 10mm 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Dokumen Sosiometri (Print)
        </button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <div class="title">
        <h4>LAPORAN HASIL ASESMEN SOSIOMETRI & DINAMIKA KELOMPOK</h4>
        <p>Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }} - Layanan Bimbingan dan Konseling</p>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 22%;">Rombongan Belajar / Kelas</td>
            <td style="width: 2%;">:</td>
            <td style="width: 26%; font-weight: bold;">{{ $sociometry->schoolClass?->name ?: '-' }}</td>
            <td style="width: 22%;">Tanggal Pelaksanaan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 26%;">{{ \Carbon\Carbon::parse($sociometry->date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Tema Hubungan Sosial</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ $sociometry->theme }}</td>
            <td>Guru BK Pembimbing</td>
            <td>:</td>
            <td>{{ $sociometry->counselor?->name ?: 'Guru BK SMAN 1 Lengkong' }}</td>
        </tr>
        <tr>
            <td>Judul Instrumen</td>
            <td>:</td>
            <td colspan="4">{{ $sociometry->title }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 15%;">NIS / NISN</th>
                <th>Nama Peserta Didik</th>
                <th style="width: 8%;">Pilihan 1 (+3)</th>
                <th style="width: 8%;">Pilihan 2 (+2)</th>
                <th style="width: 8%;">Pilihan 3 (+1)</th>
                <th style="width: 10%;">Total Skor</th>
                <th style="width: 20%;">Kategori Penerimaan</th>
            </tr>
        </thead>
        <tbody>
            @php $rank = 1; @endphp
            @forelse($popularityScores as $item)
                @php
                    $score = $item['total_score'];
                    if ($score >= 6) {
                        $cat = 'Bintang (Sangat Disukai)';
                        $catColor = '#15803d';
                    } elseif ($score >= 3) {
                        $cat = 'Diterima Baik';
                        $catColor = '#0369a1';
                    } elseif ($score >= 1) {
                        $cat = 'Cukup Diterima';
                        $catColor = '#b45309';
                    } else {
                        $cat = 'Terisolir (Perlu Bimbingan)';
                        $catColor = '#b91c1c';
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $rank++ }}</td>
                    <td style="text-align: center;">{{ $item['student']->nis ?: ($item['student']->nisn ?: '-') }}</td>
                    <td><strong>{{ $item['student']->name }}</strong></td>
                    <td style="text-align: center;">{{ $item['first_count'] }}</td>
                    <td style="text-align: center;">{{ $item['second_count'] }}</td>
                    <td style="text-align: center;">{{ $item['third_count'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $score }}</td>
                    <td style="color: {{ $catColor }}; font-weight: 600;">{{ $cat }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px;">Belum ada respon siswa yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-box">
        <h5>Rencana Tindak Lanjut Layanan Bimbingan & Konseling</h5>
        <p style="margin: 0; text-align: justify;">
            1. Peserta didik dengan kategori <em>Bintang</em> diberdayakan sebagai tutor sebaya atau peer counselor dalam dinamika kelompok.<br>
            2. Peserta didik dalam kategori <em>Terisolir</em> akan diberikan bimbingan konseling individual dan pelibatan kolaboratif terarah dalam proyek kelas/kelompok kecil.<br>
            3. Monitoring secara berkala dilakukan bersama Wali Kelas untuk menjamin terciptanya lingkungan belajar yang inklusif dan bebas perundungan.
        </p>
    </div>

    <!-- Tanda Tangan Resmi: Kepala Sekolah di KIRI (Mengetahui), Guru BK di KANAN -->
    <table class="signature-table">
        <tr>
            <td style="text-align: left;">
                Mengetahui,<br>
                Kepala {{ $school?->name ?: 'SMA Negeri 1 Lengkong' }},<br><br><br><br><br>
                <strong><u>{{ $school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.' }}</u></strong><br>
                NIP. {{ $school?->headmaster_nip ?: '198009132008011005' }}
            </td>
            <td style="text-align: right;">
                Lengkong, {{ \Carbon\Carbon::parse($sociometry->date)->translatedFormat('d F Y') }}<br>
                Guru Bimbingan dan Konseling,<br><br><br><br><br>
                <strong><u>{{ $sociometry->counselor?->name ?: Auth::user()->name }}</u></strong><br>
                NIP. {{ $sociometry->counselor?->nip ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Asesmen Psikotes - {{ $result->student?->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #000; margin: 15mm 20mm; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; }
        .kop h2 { margin: 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .kop h3 { margin: 0; font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 9.5pt; }
        .title { text-align: center; margin-bottom: 15px; }
        .title h4 { margin: 0; font-size: 12.5pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 2px 0; font-size: 10pt; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { padding: 3px 5px; vertical-align: top; font-size: 10.5pt; }
        table.data-table { width: 100%; border-collapse: collapse; margin: 8px 0 12px 0; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 5px 8px; font-size: 10pt; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .bar-container { width: 100%; height: 12px; background-color: #eee; border: 1px solid #999; border-radius: 2px; }
        .bar-fill { height: 100%; background-color: #333; }
        .section-box { border: 1px solid #000; padding: 8px 10px; margin-bottom: 12px; }
        .section-box h5 { margin: 0 0 5px 0; font-size: 10.5pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
        .signature-table { width: 100%; margin-top: 25px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; font-size: 10.5pt; }
        @media print {
            body { margin: 10mm 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 7px 15px; background: #059669; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak Laporan (Print)
        </button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>LAPORAN HASIL ASESMEN PSIKOLOGIS PESERTA DIDIK</h4>
        <p>UNIT LAYANAN BIMBINGAN DAN KONSELING (BK)</p>
    </div>

    <!-- Identitas Siswa -->
    <table class="meta">
        <tr>
            <td style="width: 28%;">Nama Lengkap Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 70%; font-weight: bold;">{{ $result->student?->name }}</td>
        </tr>
        <tr>
            <td>Nomor Induk Siswa Nasional (NISN)</td>
            <td>:</td>
            <td>{{ $result->student?->nisn }} / {{ $result->student?->nipd ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kelas / Tingkat</td>
            <td>:</td>
            <td>{{ $result->student?->schoolClass?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Instrumen Asesmen</td>
            <td>:</td>
            <td><strong>{{ $result->test?->name }}</strong> ({{ $result->test?->category }})</td>
        </tr>
        <tr>
            <td>Waktu Pengambilan Tes</td>
            <td>:</td>
            <td>{{ $result->taken_at ? $result->taken_at->translatedFormat('l, d F Y - H:i') : '-' }} WIB</td>
        </tr>
        <tr>
            <td>Profil Dominan Terbentuk</td>
            <td>:</td>
            <td style="font-weight: bold; font-size: 11pt;">{{ $result->profile_label }}</td>
        </tr>
    </table>

    <!-- Tabel Persebaran Skor Dimensi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 32%;">Aspek / Dimensi Psikologis</th>
                <th style="width: 15%;">Skor Mentah</th>
                <th style="width: 15%;">Persentase (%)</th>
                <th style="width: 30%;">Grafik Proporsi Skor</th>
            </tr>
        </thead>
        <tbody>
            @php
                $percentages = $result->dimension_scores['percentages'] ?? [];
                $rawScores = $result->dimension_scores['raw'] ?? [];
                $no = 1;
            @endphp
            @foreach($percentages as $dim => $pct)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td><strong>{{ ucfirst(strtolower(str_replace('_', ' ', $dim))) }}</strong></td>
                    <td style="text-align: center;">{{ $rawScores[$dim] ?? 0 }} pts</td>
                    <td style="text-align: center; font-weight: bold;">{{ $pct }}%</td>
                    <td>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: {{ $pct }}%;"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ringkasan Interpretasi Psikologis -->
    <div class="section-box">
        <h5>I. Ulasan Interpretasi & Dinamika Psikologis</h5>
        <p style="margin: 0; font-size: 10pt; text-align: justify;">
            {{ $result->analysis_summary }}
        </p>
    </div>

    <!-- Strategi & Rekomendasi Belajar -->
    <div class="section-box">
        <h5>II. Strategi Pembelajaran & Optimalisasi Potensi</h5>
        <p style="margin: 0; font-size: 10pt; white-space: pre-line;">
            {{ $result->study_recommendations }}
        </p>
    </div>

    <!-- Rekomendasi Jurusan & Karier -->
    @if(!empty($result->career_recommendations))
        <div class="section-box">
            <h5>III. Rekomendasi Penjurusan Kuliah & Arah Karier Masa Depan</h5>
            <table class="data-table" style="margin-top: 5px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 35%;">Program Studi / Jurusan</th>
                        <th style="width: 25%;">Rumpun Keilmuan</th>
                        <th style="width: 40%;">Prospek Profesi / Pekerjaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result->career_recommendations as $c)
                        <tr>
                            <td><strong>{{ $c['major'] ?? '-' }}</strong></td>
                            <td>{{ $c['field'] ?? '-' }}</td>
                            <td>{{ $c['prospect'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Catatan Khusus Guru BK -->
    <div class="section-box" style="margin-top: 10px;">
        <h5>IV. Catatan Verifikasi Guru Bimbingan dan Konseling</h5>
        <p style="margin: 0; font-size: 10pt;">
            {{ $result->counselor_notes ?: 'Peserta didik telah menyelesaikan asesmen psikologis terstandar secara mandiri dan hasilnya telah tercatat dalam Rekam Jejak Perkembangan Siswa SISSMA1LE sebagai dasar tindak lanjut bimbingan konseling individual dan perencanaan studi lanjut.' }}
        </p>
    </div>

    <!-- Tanda Tangan Resmi (Kepala Sekolah Selalu di Sebelah Kiri & Berstatus Mengetahui) -->
    <table class="signature-table">
        <tr>
            <td style="text-align: left;">
                Mengetahui,<br>
                Kepala {{ $school?->name ?: 'SMA Negeri 1 Lengkong' }}<br><br><br><br>
                <strong><u>{{ is_array($headmaster) ? ($headmaster['name'] ?? ($school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.')) : ($headmaster?->name ?: ($school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.')) }}</u></strong><br>
                NIP. {{ is_array($headmaster) ? ($headmaster['nip'] ?? ($school?->headmaster_nip ?: '198009132008011005')) : ($headmaster?->nip ?: ($school?->headmaster_nip ?: '198009132008011005')) }}
            </td>
            <td style="text-align: right;">
                Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
                Guru Bimbingan dan Konseling (BK)<br><br><br><br>
                <strong><u>{{ auth()->user()->name }}</u></strong><br>
                NIP/NUPTK: {{ auth()->user()->nip ?? auth()->user()->teacher?->nip ?? '-' }}
            </td>
        </tr>
    </table>
</body>
</html>


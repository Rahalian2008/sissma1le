<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Home Visit - {{ $visit->visit_code }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; margin: 20mm; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h2 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop h3 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 10pt; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h4 { margin: 0; font-size: 13pt; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 2px 0; font-size: 11pt; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.meta td { padding: 4px 6px; vertical-align: top; }
        .section-box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; }
        .section-box h5 { margin: 0 0 6px 0; font-size: 11pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .signature-table { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; }
        @media print {
            body { margin: 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #1d4ed8; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak Laporan (Print)
        </button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>LAPORAN HASIL KUNJUNGAN RUMAH (HOME VISIT)</h4>
        <p>Nomor Surat Tugas: {{ $visit->letter_number ?: ('ST-BK/' . $visit->visit_code) }}</p>
    </div>

    <p>Pada hari ini, <strong>{{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('d F Y') }}</strong>, telah dilaksanakan kegiatan Kunjungan Rumah (Home Visit) oleh Tim Bimbingan dan Konseling {{ $school?->name ?: 'SMA Negeri 1 Lengkong' }}:</p>

    <!-- Data Siswa & Keluarga -->
    <table class="meta">
        <tr>
            <td style="width: 30%;">Nama Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 68%; font-weight: bold;">{{ $visit->student?->name }}</td>
        </tr>
        <tr>
            <td>NISN / NIS</td>
            <td>:</td>
            <td>{{ $visit->student?->nisn ?: '-' }} / {{ $visit->student?->nis ?: '-' }}</td>
        </tr>
        <tr>
            <td>Kelas / Rombel</td>
            <td>:</td>
            <td>{{ $visit->student?->schoolClass?->name ?: '-' }}</td>
        </tr>
        <tr>
            <td>Orang Tua / Wali Ditemui</td>
            <td>:</td>
            <td>{{ $visit->parent_name ?: ($visit->student?->father_name ?: 'Orang Tua') }}</td>
        </tr>
        <tr>
            <td>Alamat Kunjungan</td>
            <td>:</td>
            <td>{{ $visit->address_visited ?: ($visit->student?->address ?: '-') }}</td>
        </tr>
        <tr>
            <td>Guru BK Pelaksana</td>
            <td>:</td>
            <td>{{ $visit->counselor?->name ?: 'Tim Konselor Sekolah' }}</td>
        </tr>
        <tr>
            <td>Wali Kelas Pendamping</td>
            <td>:</td>
            <td>{{ $visit->homeroom?->name ?: ($visit->student?->schoolClass?->homeroomTeacher?->name ?: '-') }}</td>
        </tr>
    </table>

    <!-- Kasus & Temuan Lapangan -->
    <div class="section-box">
        <h5>1. Latar Belakang / Urgensi Kunjungan Rumah</h5>
        <p style="margin: 0; text-align: justify;">{{ $visit->case_background }}</p>
    </div>

    @if($visit->family_condition)
    <div class="section-box">
        <h5>2. Hasil Observasi Kondisi Keluarga & Lingkungan Tempat Tinggal</h5>
        <p style="margin: 0; text-align: justify;">{{ $visit->family_condition }}</p>
    </div>
    @endif

    <div class="section-box">
        <h5>3. Tanggapan & Komitmen Orang Tua / Wali Murid</h5>
        <p style="margin: 0; text-align: justify;">{{ $visit->parent_commitment ?: 'Orang tua menyambut baik kunjungan sekolah dan berkomitmen untuk lebih memperhatikan kedisiplinan dan absensi belajar putra/putrinya di rumah.' }}</p>
    </div>

    <div class="section-box">
        <h5>4. Kesimpulan & Rekomendasi Solusi Bersama</h5>
        <p style="margin: 0; text-align: justify;">{{ $visit->visit_result ?: 'Disepakati pemantauan bersama antara orang tua, wali kelas, dan guru BK untuk memastikan kehadiran dan perkembangan peserta didik.' }}</p>
    </div>

    <!-- Tanda Tangan Resmi (Kepala Sekolah Selalu di Sebelah Kiri & Berstatus Mengetahui) -->
    <table class="signature-table" style="width: 100%; margin-top: 30px; border-collapse: collapse;">
        <tr>
            <td style="width: 34%; text-align: left; vertical-align: top;">
                Mengetahui,<br>
                Kepala {{ $school?->name ?: 'SMA Negeri 1 Lengkong' }},<br><br><br><br><br>
                <strong><u>{{ $school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.' }}</u></strong><br>
                NIP. {{ $school?->headmaster_nip ?: '198009132008011005' }}
            </td>
            <td style="width: 33%; text-align: center; vertical-align: top;">
                Orang Tua / Wali Murid,<br><br><br><br><br>
                <strong><u>{{ $visit->parent_name ?: ($visit->student?->father_name ?: 'Orang Tua Siswa') }}</u></strong>
            </td>
            <td style="width: 33%; text-align: right; vertical-align: top;">
                Lengkong, {{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('d F Y') }}<br>
                Guru BK Pelaksana,<br><br><br><br><br>
                <strong><u>{{ $visit->counselor?->name ?: 'Konselor Sekolah' }}</u></strong><br>
                NIP. {{ $visit->counselor?->nip ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>


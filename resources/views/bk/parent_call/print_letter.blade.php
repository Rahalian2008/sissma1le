<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Panggilan Orang Tua - {{ $parentCall->letter_number }}</title>
    <style>
        @page {
            size: {{ request('paper', 'a4') === 'f4' ? '215mm 330mm' : 'A4' }};
            margin: 15mm;
        }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.6; color: #000; margin: 15mm; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop h2 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop h3 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 10pt; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.meta td { padding: 3px 6px; vertical-align: top; }
        .schedule-box { margin: 15px 0 15px 25px; }
        .schedule-box table td { padding: 3px 6px; }
        .signature-table { width: 100%; margin-top: 35px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <x-print-paper-toolbar :defaultPaper="request('paper', 'a4')" />

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <div style="text-align: center; margin: 15px 0 20px 0;">
        <span style="font-weight: bold; text-decoration: underline; font-size: 13pt; letter-spacing: 0.5px;">SURAT PANGGILAN ORANG TUA / WALI MURID</span><br>
        <span style="font-size: 10pt; color: #444;">Nomor: {{ $parentCall->letter_number ?: $parentCall->call_code }}</span>
    </div>

    <!-- Nomor & Tujuan Surat -->
    <table class="meta">
        <tr>
            <td style="width: 15%;">Nomor</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%; font-weight: bold;">{{ $parentCall->letter_number }}</td>
            <td style="width: 35%; text-align: right;">Lengkong, {{ \Carbon\Carbon::parse($parentCall->created_at)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
            <td></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td><strong>Undangan Permohonan Kehadiran Orang Tua / Wali</strong></td>
            <td></td>
        </tr>
    </table>

    <div style="margin: 20px 0;">
        Kepada Yth.<br>
        <strong>Bapak / Ibu Orang Tua / Wali dari:</strong><br>
        <span style="font-weight: bold; font-size: 13pt;">{{ $parentCall->student?->name }}</span> (Kelas {{ $parentCall->student?->schoolClass?->name }})<br>
        di Tempat
    </div>

    <p style="text-indent: 30px; text-align: justify; margin: 0 0 10px 0;">
        Dengan hormat,<br>
        Sehubungan dengan pentingnya koordinasi serta musyawarah pembinaan perkembangan belajar dan kedisiplinan putra/putri Bapak/Ibu di sekolah, maka melalui surat ini kami mengharapkan kehadiran Bapak/Ibu Orang Tua/Wali murid pada:
    </p>

    <!-- Rincian Waktu & Tempat -->
    <div class="schedule-box">
        <table>
            <tr>
                <td style="width: 140px; font-weight: bold;">Hari, Tanggal</td>
                <td style="width: 15px;">:</td>
                <td><strong>{{ \Carbon\Carbon::parse($parentCall->call_date)->translatedFormat('l, d F Y') }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Waktu / Pukul</td>
                <td>:</td>
                <td>{{ $parentCall->call_time ?: '08.30 WIB s.d Selesai' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tempat</td>
                <td>:</td>
                <td>{{ $parentCall->place ?: ('Ruang Bimbingan Konseling ' . ($school?->name ?: 'SMA Negeri 1 Lengkong')) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Bertemu Dengan</td>
                <td>:</td>
                <td>Guru Bimbingan Konseling (BK) & Wali Kelas</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Keperluan / Hal</td>
                <td>:</td>
                <td><em>{{ $parentCall->reason }}</em></td>
            </tr>
        </table>
    </div>

    <p style="text-indent: 30px; text-align: justify; margin: 10px 0;">
        Mengingat arti pentingnya pembahasan masa depan pendidikan putra/putri Bapak/Ibu, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktu yang telah ditentukan dan <strong>tidak diwakilkan</strong>.
    </p>

    <p style="text-indent: 30px; text-align: justify; margin: 10px 0 0 0;">
        Demikian surat undangan pemanggilan ini kami sampaikan. Atas perhatian, kerja sama, dan kehadiran Bapak/Ibu, kami haturkan terima kasih.
    </p>

    <!-- Tanda Tangan Resmi (Kepala Sekolah Selalu di Sebelah Kiri & Berstatus Mengetahui) -->
    <table class="signature-table" style="width: 100%; margin-top: 35px; border-collapse: collapse;">
        <tr>
            <td style="width: 34%; text-align: left; vertical-align: top;">
                Mengetahui,<br>
                Kepala {{ $school?->name ?: 'SMA Negeri 1 Lengkong' }},<br><br><br><br><br>
                <strong><u>{{ is_array($headmaster) ? ($headmaster['name'] ?? ($school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.')) : ($headmaster?->name ?: ($school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.')) }}</u></strong><br>
                NIP. {{ is_array($headmaster) ? ($headmaster['nip'] ?? ($school?->headmaster_nip ?: '198009132008011005')) : ($headmaster?->nip ?: ($school?->headmaster_nip ?: '198009132008011005')) }}
            </td>
            <td style="width: 33%; text-align: center; vertical-align: top;">
                Wakasek Bidang Kesiswaan,<br><br><br><br><br>
                <strong><u>{{ is_array($wakasekKesiswaan) ? ($wakasekKesiswaan['name'] ?? 'Wakasek Kesiswaan') : ($wakasekKesiswaan?->name ?: 'Wakasek Kesiswaan') }}</u></strong><br>
                NIP. {{ is_array($wakasekKesiswaan) ? ($wakasekKesiswaan['nip'] ?? '-') : ($wakasekKesiswaan?->nip ?: '-') }}
            </td>
            <td style="width: 33%; text-align: right; vertical-align: top;">
                Guru BK / Konselor Sekolah,<br><br><br><br><br>
                <strong><u>{{ $parentCall->counselor?->name ?: 'Guru BK' }}</u></strong><br>
                NIP. {{ $parentCall->counselor?->nip ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>


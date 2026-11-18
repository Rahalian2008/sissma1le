<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Panggilan Orang Tua - {{ $parentCall->letter_number ?: $parentCall->call_code }}</title>
    <style>
        @page {
            size: {{ request('paper', 'a4') === 'f4' ? '215mm 330mm' : 'A4' }};
            margin: 8mm 15mm 8mm 15mm;
        }
        * {
            box-sizing: border-box;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 10px 15mm;
            background: #fff;
        }
        .printable-sheet {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .letter-title {
            text-align: center;
            margin: 8px 0 10px 0;
            line-height: 1.25;
        }
        .letter-title-text {
            font-weight: bold;
            text-decoration: underline;
            font-size: 12pt;
            letter-spacing: 0.5px;
        }
        .letter-title-sub {
            font-size: 9.5pt;
            color: #333;
        }
        table.meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.meta td {
            padding: 1.5px 4px;
            vertical-align: top;
            font-size: 11pt;
            line-height: 1.3;
        }
        .recipient-box {
            margin: 8px 0;
            line-height: 1.35;
        }
        p.letter-body {
            text-indent: 28px;
            text-align: justify;
            margin: 4px 0;
            line-height: 1.35;
        }
        .schedule-box {
            margin: 6px 0 6px 20px;
        }
        .schedule-box table {
            border-collapse: collapse;
        }
        .schedule-box table td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 11pt;
            line-height: 1.3;
        }
        .signature-table {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .signature-table td {
            vertical-align: top;
            padding: 0 4px;
            font-size: 11pt;
            line-height: 1.3;
        }
        @media print {
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: transparent !important;
            }
            .no-print {
                display: none !important;
            }
            .printable-sheet {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>
    <x-print-paper-toolbar :defaultPaper="request('paper', 'a4')" />

    <div class="printable-sheet">
        <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
        <x-print-kop :school="$school" />

        <div class="letter-title">
            <span class="letter-title-text">SURAT PANGGILAN ORANG TUA / WALI MURID</span><br>
            <span class="letter-title-sub">Nomor: {{ $parentCall->letter_number ?: $parentCall->call_code }}</span>
        </div>

        <!-- Nomor & Tujuan Surat -->
        <table class="meta">
            <tr>
                <td style="width: 14%;">Nomor</td>
                <td style="width: 2%;">:</td>
                <td style="width: 48%; font-weight: bold;">{{ $parentCall->letter_number ?: $parentCall->call_code }}</td>
                <td style="width: 36%; text-align: right;">Lengkong, {{ \Carbon\Carbon::parse($parentCall->created_at)->translatedFormat('d F Y') }}</td>
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

        <div class="recipient-box">
            Kepada Yth.<br>
            <strong>Bapak / Ibu Orang Tua / Wali dari:</strong><br>
            <span style="font-weight: bold; font-size: 11.5pt;">{{ $parentCall->student?->name }}</span> (Kelas {{ $parentCall->student?->schoolClass?->name ?? '-' }})<br>
            di Tempat
        </div>

        <p class="letter-body">
            Dengan hormat, sehubungan dengan pentingnya koordinasi serta musyawarah pembinaan perkembangan belajar dan kedisiplinan putra/putri Bapak/Ibu di sekolah, maka melalui surat ini kami mengharapkan kehadiran Bapak/Ibu Orang Tua/Wali murid pada:
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
                    <td>Koordinator BK &amp; Wali Kelas</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Keperluan / Hal</td>
                    <td>:</td>
                    <td><em>{{ $parentCall->reason }}</em></td>
                </tr>
            </table>
        </div>

        <p class="letter-body">
            Mengingat arti pentingnya pembahasan masa depan pendidikan putra/putri Bapak/Ibu, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktu yang telah ditentukan dan <strong>tidak diwakilkan</strong>.
        </p>

        <p class="letter-body">
            Demikian surat undangan pemanggilan ini kami sampaikan. Atas perhatian, kerja sama, dan kehadiran Bapak/Ibu, kami haturkan terima kasih.
        </p>

        <!-- Tanda Tangan Resmi: Kepala Sekolah (Kiri), Koordinator BK (Tengah), Wali Kelas (Kanan) -->
        <table class="signature-table">
            <tr>
                <td style="width: 34%; text-align: left;">
                    Mengetahui,<br>
                    Kepala {{ $school?->name ?: 'SMA Negeri 1 Lengkong' }},
                    <div style="height: 52px;"></div>
                    <strong><u>{{ is_array($headmaster) ? ($headmaster['name'] ?? ($school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.')) : ($headmaster?->name ?: ($school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.')) }}</u></strong><br>
                    NIP. {{ is_array($headmaster) ? ($headmaster['nip'] ?? ($school?->headmaster_nip ?: '198009132008011005')) : ($headmaster?->nip ?: ($school?->headmaster_nip ?: '198009132008011005')) }}
                </td>
                <td style="width: 33%; text-align: center;">
                    <div style="visibility: hidden;">Mengetahui,</div>
                    Koordinator BK,
                    <div style="height: 52px;"></div>
                    @php
                        $bkName = is_array($bkCoordinator) ? ($bkCoordinator['name'] ?? 'Ida Marttin Maolidah SE, SP.,M.Pd.') : ($bkCoordinator?->name ?? 'Ida Marttin Maolidah SE, SP.,M.Pd.');
                        $bkNip = is_array($bkCoordinator) ? ($bkCoordinator['nip'] ?? '197803102008012006') : ($bkCoordinator?->nip ?? '197803102008012006');
                    @endphp
                    <strong><u>{{ $bkName }}</u></strong><br>
                    NIP. {{ $bkNip ?: '-' }}
                </td>
                <td style="width: 33%; text-align: right;">
                    <div style="visibility: hidden;">Mengetahui,</div>
                    Wali Kelas {{ $parentCall->student?->schoolClass?->name ?? '' }},
                    <div style="height: 52px;"></div>
                    @php
                        $homeroom = $homeroomTeacher ?? $parentCall->student?->schoolClass?->homeroomTeacher;
                        $homeroomName = $homeroom?->formatted_name ?: ($homeroom?->name ?: 'Wali Kelas');
                        $homeroomNip = $homeroom?->nip ?: '-';
                    @endphp
                    <strong><u>{{ $homeroomName }}</u></strong><br>
                    NIP. {{ $homeroomNip }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

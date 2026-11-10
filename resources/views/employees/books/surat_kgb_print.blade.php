<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pemberitahuan Kenaikan Gaji Berkala - {{ $employee->formatted_name }}</title>
    <style>
        @page { size: portrait; margin: 20mm 20mm 20mm 20mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.5; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9.5pt; }
        .meta-table { width: 100%; border: none; margin-bottom: 14px; font-size: 11pt; }
        .meta-table td { border: none; padding: 2px 0; vertical-align: top; }
        .content-table { width: 100%; border: none; margin: 10px 0 16px 20px; }
        .content-table td { border: none; padding: 3px 0; vertical-align: top; font-size: 10.5pt; }
        .signature-box { float: right; width: 280px; text-align: center; margin-top: 25px; page-break-inside: avoid; }
        .tembusan { margin-top: 30px; font-size: 9.5pt; clear: both; }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Surat Usulan KGB: ' . $employee->formatted_name" orientation="portrait" defaultPaper="f4" />

    <div class="printable-sheet portrait-sheet" style="padding: 15mm 20mm; max-width: 215mm; margin: 0 auto;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <table class="meta-table">
            <tr>
                <td style="width: 80px;">Nomor</td>
                <td style="width: 15px;">:</td>
                <td style="width: 380px;"><strong>822.2 / {{ str_pad((string)$employee->id, 3, '0', STR_PAD_LEFT) }} / KPG.14 - {{ $school->name ?? 'SMAN 1 Lengkong' }} / {{ \App\Services\LetterNumberService::toRomanMonth() }} / {{ date('Y') }}</strong></td>
                <td style="text-align: right;">{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
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
            <td><strong><u>PEMBERITAHUAN KENAIKAN GAJI BERKALA</u></strong></td>
            <td>Kepada Yth.<br>
                Kepala {{ $school->branch_office ?? 'Cabang Dinas Pendidikan Wilayah V' }}<br>
                Provinsi Jawa Barat<br>
                di - Tempat
            </td>
        </tr>
    </table>

    <p style="text-align: justify; text-indent: 30px;">
        Dengan ini diberitahukan bahwa berhubung dengan telah dipenuhinya masa kerja dan syarat-syarat lainnya kepada Pegawai Negeri Sipil / Pegawai Pemerintah dengan Perjanjian Kerja di bawah ini:
    </p>

    <table class="content-table">
        <tr>
            <td style="width: 200px;">1. Nama Pegawai</td>
            <td style="width: 15px;">:</td>
            <td><strong>{{ $employee->formatted_name }}</strong></td>
        </tr>
        <tr>
            <td>2. NIP / NI PPPK</td>
            <td>:</td>
            <td>{{ $employee->nip ?: ($employee->ni_pppk ?: '-') }}</td>
        </tr>
        <tr>
            <td>3. Pangkat / Golongan Ruang</td>
            <td>:</td>
            <td>{{ $employee->rank_group ?? '-' }}</td>
        </tr>
        <tr>
            <td>4. Jabatan Dinas</td>
            <td>:</td>
            <td>{{ $employee->position ?? '-' }}</td>
        </tr>
        <tr>
            <td>5. Unit Kerja</td>
            <td>:</td>
            <td>{{ $school->name }}</td>
        </tr>
        <tr>
            <td>6. Gaji Pokok Lama</td>
            <td>:</td>
            <td>Rp {{ number_format($employee->last_kgb_salary ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>7. TMT KGB Terakhir</td>
            <td>:</td>
            <td>{{ $employee->last_kgb_tmt ? $employee->last_kgb_tmt->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td>8. Gaji Pokok Baru</td>
            <td>:</td>
            <td><strong>Rp {{ number_format(($employee->last_kgb_salary ?? 3000000) * 1.05, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>9. Terhitung Mulai Tanggal (TMT)</td>
            <td>:</td>
            <td><strong>{{ $employee->next_kgb_tmt ? $employee->next_kgb_tmt->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong></td>
        </tr>
        <tr>
            <td>10. Kenaikan Gaji Berkala Berikutnya</td>
            <td>:</td>
            <td>{{ $employee->next_kgb_tmt ? $employee->next_kgb_tmt->copy()->addYears(2)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    <p style="text-align: justify; text-indent: 30px;">
        Diharapkan agar kepada yang bersangkutan dapat dibayarkan penghasilannya berdasarkan ketentuan kenaikan gaji berkala yang berlaku sesuai PP No. 5 Tahun 2024 / Perpres No. 11 Tahun 2024.
    </p>

    <div class="signature-box">
        <p>Kepala Sekolah,</p>
        <br><br><br>
        <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
        Pembina Tk. I, IV/b<br>
        NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
    </div>

    <div class="tembusan">
        <strong>Tembusan disampaikan kepada Yth:</strong>
        <ol style="margin-top: 3px; padding-left: 18px;">
            <li>Kepala BPKAD Provinsi Jawa Barat;</li>
            <li>Kepala BKD Provinsi Jawa Barat;</li>
            <li>Bank Pengelola Penggajian (Bank BJB);</li>
            <li>Pegawai yang bersangkutan;</li>
            <li>Arsip Kepegawaian Sekolah.</li>
        </ol>
    </div>
    </div>
</body>
</html>

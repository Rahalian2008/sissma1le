<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPPD - {{ $sppd->sppd_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.25;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-kop p {
            margin: 1px 0;
            font-size: 9.5pt;
        }
        .header-kop h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 12px;
        }
        .title-doc h3 {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .title-doc p {
            font-size: 9.5pt;
            margin: 2px 0 0 0;
            font-family: monospace;
        }
        table.sppd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        table.sppd-table th, table.sppd-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        table.visum-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        table.visum-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
        .print-btn-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: sans-serif;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .print-btn-bar button {
            background: #2563eb;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Surat Perintah Perjalanan Dinas (SPPD): ' . $sppd->sppd_number" orientation="portrait" defaultPaper="f4" />

    <!-- ==================== LEMBAR I (RINCIAN SPPD) ==================== -->
    <div style="padding: 5px;">
        <!-- KOP SURAT RESMI -->
        <x-print-kop :school="$school" />

        <table style="width: 100%; font-size: 9pt; margin-bottom: 6px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    Lembar Ke : I (Pertama)<br>
                    Kode No &nbsp;&nbsp;&nbsp;: 094 / SPPD<br>
                    Nomor &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ $sppd->sppd_number }}</strong>
                </td>
            </tr>
        </table>

        <div class="title-doc">
            <h3>SURAT PERINTAH PERJALANAN DINAS (SPPD)</h3>
        </div>

        <table class="sppd-table">
            <tr>
                <td style="width: 25px; text-align: center;">1.</td>
                <td style="width: 42%;">Pejabat Pembuat Komitmen / Yang Memberi Perintah</td>
                <td style="width: 55%;">Kepala {{ $school->name }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">2.</td>
                <td>Nama Pegawai yang diperintahkan</td>
                <td><strong>{{ $sppd->employee->formatted_name }}</strong></td>
            </tr>
            <tr>
                <td style="text-align: center;">3.</td>
                <td>
                    a. Pangkat dan Golongan<br>
                    b. Jabatan / Instansi<br>
                    c. Tingkat Biaya Perjalanan Dinas
                </td>
                <td>
                    a. {{ $sppd->employee->rank_group ?? '-' }}<br>
                    b. {{ $sppd->employee->position ?? '-' }} / {{ $school->name }}<br>
                    c. Tingkat C (Standar Perjalanan Dinas Pendidikan)
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">4.</td>
                <td>Maksud Perjalanan Dinas</td>
                <td>{{ $sppd->assignment_purpose }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">5.</td>
                <td>Alat angkutan yang dipergunakan</td>
                <td>{{ $sppd->transportation_type }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">6.</td>
                <td>
                    a. Tempat berangkat<br>
                    b. Tempat tujuan
                </td>
                <td>
                    a. {{ $sppd->departure_location }}<br>
                    b. <strong>{{ $sppd->destination_location }}</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">7.</td>
                <td>
                    a. Lamanya Perjalanan Dinas<br>
                    b. Tanggal berangkat<br>
                    c. Tanggal harus kembali
                </td>
                <td>
                    a. {{ $sppd->duration_days }} ({{ $sppd->duration_spell }}) hari<br>
                    b. {{ $sppd->departure_date->translatedFormat('d F Y') }}<br>
                    c. {{ $sppd->return_date->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">8.</td>
                <td>Pengikut / Rombongan</td>
                <td>{{ $sppd->followers ?: '-' }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">9.</td>
                <td>
                    Pembebanan Anggaran<br>
                    a. Instansi<br>
                    b. Akun / Mata Anggaran
                </td>
                <td>
                    <br>
                    a. {{ $school->name }}<br>
                    b. {{ $sppd->budget_source }} {{ $sppd->budget_account ? '('.$sppd->budget_account.')' : '' }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">10.</td>
                <td>Keterangan lain-lain</td>
                <td>
                    @if($sppd->letter_task_number)
                        Dasar Surat Tugas: <strong>{{ $sppd->letter_task_number }}</strong><br>
                    @endif
                    {{ $sppd->notes ?? 'Setelah melaksanakan tugas agar segera menyampaikan laporan pertanggungjawaban.' }}
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan PPK -->
        <table style="width: 100%; margin-top: 20px; font-size: 9.5pt;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    Dikeluarkan di : {{ $sppd->departure_location }}<br>
                    Pada tanggal &nbsp;: {{ $sppd->departure_date->translatedFormat('d F Y') }}<br><br>
                    Kepala Sekolah / Pejabat Pembuat Komitmen,<br><br><br><br>
                    <strong><u>{{ $sppd->ppk_name }}</u></strong><br>
                    NIP. {{ $sppd->ppk_nip }}
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== LEMBAR II (VISUM PERJALANAN DINAS) ==================== -->
    <div class="page-break" style="padding: 10px 5px; margin-top: 25px;">
        <table style="width: 100%; font-size: 8.5pt; margin-bottom: 8px;">
            <tr>
                <td style="width: 55%;">
                    <strong>{{ strtoupper($school->name) }}</strong><br>
                    Lampiran SPPD No : {{ $sppd->sppd_number }}
                </td>
                <td style="width: 45%; text-align: right;">
                    Lembar Ke : II (Kedua - Visum)
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-bottom: 10px;">
            <h4 style="margin: 0; font-size: 10.5pt; text-transform: uppercase; text-decoration: underline;">LEMBAR VISUM SURAT PERINTAH PERJALANAN DINAS</h4>
        </div>

        <table class="visum-table">
            <tr>
                <td style="width: 50%;">
                    I. Berangkat dari &nbsp;&nbsp;&nbsp;: {{ $sppd->departure_location }}<br>
                    &nbsp;&nbsp;&nbsp;Ke &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->destination_location }}<br>
                    &nbsp;&nbsp;&nbsp;Pada tanggal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->departure_date->translatedFormat('d F Y') }}<br><br>
                    <div style="text-align: center;">
                        Kepala Sekolah,<br><br><br><br>
                        <strong><u>{{ $sppd->ppk_name }}</u></strong><br>
                        NIP. {{ $sppd->ppk_nip }}
                    </div>
                </td>
                <td style="width: 50%;">
                    II. Tiba di &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->destination_location }}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Pada tanggal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->departure_date->translatedFormat('d F Y') }}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;Kepala / Pejabat Berwenang Tempat Tujuan,<br><br><br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;( ..................................................................... )<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;NIP.
                </td>
            </tr>
            <tr>
                <td>
                    III. Berangkat dari : {{ $sppd->destination_location }}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ke &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->departure_location }}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pada tanggal &nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->return_date->translatedFormat('d F Y') }}<br><br>
                    <div style="text-align: center;">
                        Pejabat Tempat Tujuan,<br><br><br><br>
                        ( ..................................................................... )<br>
                        NIP.
                    </div>
                </td>
                <td>
                    IV. Tiba di &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->departure_location }} (Tempat Kedudukan)<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pada tanggal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $sppd->return_date->translatedFormat('d F Y') }}<br><br>
                    <div style="text-align: center;">
                        Kepala Sekolah,<br><br><br><br>
                        <strong><u>{{ $sppd->ppk_name }}</u></strong><br>
                        NIP. {{ $sppd->ppk_nip }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    V. Telah diperiksa dengan keterangan bahwa perjalanan tersebut di atas benar-benar dilakukan atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya.<br><br>
                    <table style="width: 100%; border: none;">
                        <tr style="border: none;">
                            <td style="width: 50%; border: none;"></td>
                            <td style="width: 50%; text-align: center; border: none;">
                                Pejabat Pembuat Komitmen,<br><br><br><br>
                                <strong><u>{{ $sppd->ppk_name }}</u></strong><br>
                                NIP. {{ $sppd->ppk_nip }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>VI. CATATAN LAIN-LAIN:</strong><br>
                    {{ $sppd->notes ?? 'Tidak ada catatan pelanggaran pelaksanaan rute perjalanan dinas.' }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 7.5pt; color: #334155; line-height: 1.3;">
                    <strong>VII. PERHATIAN:</strong><br>
                    Pejabat Pembuat Komitmen yang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba serta bendahara pengeluaran bertanggung jawab berdasarkan peraturan-peraturan Keuangan Negara apabila negara menderita rugi akibat kesalahan, kelalaian, dan kealpaannya.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

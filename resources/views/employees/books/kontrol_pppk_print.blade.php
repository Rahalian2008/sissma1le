<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kontrol ASN PPPK (MHPK) - {{ $school->name }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 10pt; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header h3 { margin: 0; font-size: 12pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .doc-title { text-align: center; margin: 12px 0 16px 0; }
        .doc-title h4 { margin: 0; font-size: 12pt; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { margin: 2px 0; font-size: 9.5pt; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 5px 6px; font-size: 8.5pt; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; font-size: 8.5pt; }
        .signature-section { margin-top: 25px; width: 100%; page-break-inside: avoid; }
        .signature-box { float: right; width: 300px; text-align: center; font-size: 9.5pt; }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Kontrol ASN PPPK (MHPK)'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>BUKU KONTROL MASA HUBUNGAN PERJANJIAN KERJA (MHPK) ASN PPPK</h4>
        <p>Berdasarkan Peraturan Pemerintah No. 49 Tahun 2018 - Tahun {{ date('Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th>Nama Pegawai & NI PPPK</th>
                <th>Golongan</th>
                <th>Jabatan Dinas</th>
                <th>Nomor SK Perjanjian Kerja</th>
                <th>TMT Mulai Kontrak</th>
                <th>TMT Akhir Kontrak</th>
                <th>Sisa Masa Kontrak</th>
                <th>Status Kinerja / Evaluasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $idx => $e)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $e->formatted_name }}</strong><br>
                        <span class="font-mono">{{ $e->ni_pppk ?: ($e->nip ?: '-') }}</span>
                    </td>
                    <td class="text-center">{{ $e->rank_group ?? 'Golongan IX' }}</td>
                    <td>{{ $e->position ?? '-' }}</td>
                    <td class="font-mono">{{ $e->pppk_contract_number ?: '814.1/045-BKD/PPPK/2022' }}</td>
                    <td class="text-center">{{ $e->pppk_contract_start ? $e->pppk_contract_start->format('d/m/Y') : '-' }}</td>
                    <td class="text-center"><strong>{{ $e->pppk_contract_end ? $e->pppk_contract_end->format('d/m/Y') : '-' }}</strong></td>
                    <td class="text-center">{{ $e->pppk_contract_end ? $e->pppk_contract_end->diffForHumans() : '-' }}</td>
                    <td class="text-center">{{ $e->isPppkContractExpiring(6) ? 'Evaluasi Perpanjangan (< 6 bln)' : 'Aktif Berjalan' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala Sekolah,</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>
    </div>
</body>
</html>

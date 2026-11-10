<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kontrol Kenaikan Gaji Berkala (KGB) - {{ $school->name }}</title>
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
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; font-size: 8.5pt; }
        .signature-section { margin-top: 25px; width: 100%; page-break-inside: avoid; }
        .signature-box { float: right; width: 300px; text-align: center; font-size: 9.5pt; }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Kontrol Kenaikan Gaji Berkala (KGB)'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>BUKU KONTROL KENAIKAN GAJI BERKALA (KGB)</h4>
        <p>Berdasarkan Peraturan Pemerintah No. 5 Tahun 2024 & PermenPAN-RB No. 7 Tahun 2023 - Tahun {{ date('Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th>Nama Pegawai & NIP/NI PPPK</th>
                <th>Status</th>
                <th>Pangkat / Gol.</th>
                <th>Jabatan</th>
                <th>TMT KGB Terakhir</th>
                <th>Gaji Pokok Lama</th>
                <th>Jadwal KGB Berikutnya</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $idx => $e)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $e->formatted_name }}</strong><br>
                        <span class="font-mono">{{ $e->nip ?: ($e->ni_pppk ?: '-') }}</span>
                    </td>
                    <td class="text-center">{{ $e->employment_status }}</td>
                    <td class="text-center">{{ $e->rank_group ?? '-' }}</td>
                    <td>{{ $e->position ?? '-' }}</td>
                    <td class="text-center">{{ $e->last_kgb_tmt ? $e->last_kgb_tmt->format('d/m/Y') : '-' }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($e->last_kgb_salary ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center"><strong>{{ $e->next_kgb_tmt ? $e->next_kgb_tmt->format('d/m/Y') : '-' }}</strong></td>
                    <td class="text-center">{{ $e->isKgbDue(3) ? 'Jatuh tempo (segera diproses)' : 'Terjadwal' }}</td>
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

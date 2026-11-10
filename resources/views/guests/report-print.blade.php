<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Buku Tamu Sekolah | {{ $school->name ?? 'SIKATAS' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            line-height: 1.3;
            margin: 20px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header-kop {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .header-kop img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 15px;
        }
        .header-text {
            flex: 1;
            text-align: center;
        }
        .header-text h3 { margin: 0; font-size: 11pt; font-weight: normal; letter-spacing: 1px; }
        .header-text h2 { margin: 2px 0; font-size: 12pt; font-weight: bold; }
        .header-text h1 { margin: 2px 0; font-size: 14pt; font-weight: 800; }
        .header-text p { margin: 2px 0; font-size: 8.5pt; font-style: italic; }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }

        .ttd-section {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .ttd-table {
            width: 100%;
            text-align: center;
            font-size: 9pt;
        }
        .ttd-table td {
            vertical-align: top;
            padding: 5px;
        }
        .ttd-space {
            height: 60px;
        }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <x-print-toolbar orientation="landscape" default-size="f4" title="Buku Tamu Umum Sekolah (Format Disdik Jabar)" />

    <!-- Kop Surat Resmi -->
    <x-print-kop :school="$school" />

    <div class="text-center" style="margin-bottom: 15px;">
        <div class="font-bold" style="font-size: 13pt; text-decoration: underline; letter-spacing: 0.5px;">BUKU TAMU UMUM SEKOLAH</div>
        <div style="font-size: 9pt; margin-top: 2px;">
            Periode Tanggal: {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 10%;">Waktu Masuk</th>
                <th style="width: 11%;">No. Tiket Tamu</th>
                <th style="width: 15%;">Nama Lengkap Tamu</th>
                <th style="width: 11%;">No. Identitas & HP</th>
                <th style="width: 16%;">Asal Instansi / Alamat</th>
                <th style="width: 12%;">Tujuan Bertemu</th>
                <th style="width: 14%;">Keperluan / Maksud</th>
                <th style="width: 8%;">Waktu Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guests as $idx => $g)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $g->check_in_at?->format('d/m/y H:i') }}</td>
                    <td class="font-mono text-center font-bold">{{ $g->ticket_number }}</td>
                    <td class="font-bold">
                        {{ $g->name }}
                        @if($g->number_of_guests > 1)
                            <span style="font-weight: normal; font-size: 7.5pt; color: #475569;">(+{{ $g->number_of_guests - 1 }} org)</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-family: monospace;">{{ $g->identity_type }}: {{ $g->identity_number }}</div>
                        <div style="font-size: 7.5pt; color: #475569;">WA: {{ $g->phone }}</div>
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $g->institution }}</div>
                        <div style="font-size: 7.5pt; color: #64748b;">{{ $g->institution_type }}</div>
                    </td>
                    <td>
                        <div style="font-weight: bold; color: #2563eb;">{{ $g->destination_department }}</div>
                        @if($g->destination_person_name)
                            <div style="font-size: 7.5pt;">D.h.i: {{ $g->destination_person_name }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $g->purpose }}</div>
                        <div style="font-size: 7.5pt; color: #334155;">{{ Str::limit($g->purpose_detail, 60) }}</div>
                    </td>
                    <td class="text-center font-mono">
                        {{ $g->check_out_at ? $g->check_out_at->format('H:i').' WIB' : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data kunjungan tamu pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f8fafc;">
                <td colspan="7" class="text-right" style="padding: 6px;">TOTAL KUNJUNGAN TAMU:</td>
                <td colspan="2" class="text-center">{{ $guests->count() }} Orang / Kunjungan</td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-section">
        <table class="ttd-table">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <div class="ttd-space"></div>
                    <strong><u>{{ $school->headmaster_name ?? $school->principal_name ?? '................................................' }}</u></strong><br>
                    NIP. {{ $school->headmaster_nip ?? $school->principal_nip ?? '-' }}
                </td>
                <td style="width: 50%;">
                    {{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    {{ auth()->user()?->getRoleDisplayName() ?? 'Petugas Pelayanan / Resepsionis' }}
                    <div class="ttd-space"></div>
                    <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Pelayanan Tamu') }}</u></strong><br>
                    NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

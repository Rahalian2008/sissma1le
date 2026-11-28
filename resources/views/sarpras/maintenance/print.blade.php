<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Pemeliharaan Barang Inventaris (BMD) - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page { size: landscape; margin: 12mm 15mm 12mm 15mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 10pt; line-height: 1.35; color: #000; margin: 0; padding: 0; }
        .doc-title { text-align: center; margin: 10px 0 14px 0; }
        .doc-title h3 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; text-decoration: underline; }
        .doc-title p { margin: 3px 0; font-size: 10pt; }
        table.data-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 4.5px 6px; font-size: 9pt; vertical-align: middle; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .signatures { margin-top: 25px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 320px; text-align: center; font-size: 10pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Pemeliharaan Barang Inventaris (BMD)'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 15px;">
        <x-print-kop :school="$school" />

        <div class="doc-title">
            <h3>BUKU REGISTER PEMELIHARAAN &amp; SERVIS BARANG INVENTARIS (BMD)</h3>
            <p>Standar Pengelolaan Sarana dan Prasarana Pendidikan Sesuai Permendagri No. 47 Tahun 2021</p>
            @if($status)
                <p style="font-size: 9pt; font-style: italic;">Filter Status: <strong>{{ $status }}</strong></p>
            @endif
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No.</th>
                    <th style="width: 75px;">Tgl. Ajuan</th>
                    <th style="width: 130px;">Kode &amp; Nama Barang</th>
                    <th style="width: 90px;">Lokasi Ruang</th>
                    <th>Uraian Kerusakan / Masalah</th>
                    <th>Rencana Tindakan</th>
                    <th style="width: 90px;">Estimasi Biaya</th>
                    <th style="width: 90px;">Biaya Riil</th>
                    <th style="width: 110px;">Teknisi / Rekanan</th>
                    <th style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maintenances as $idx => $m)
                    <tr>
                        <td class="text-center font-mono">{{ $idx + 1 }}</td>
                        <td class="text-center font-mono">{{ \Carbon\Carbon::parse($m->request_date)->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $m->asset?->name ?? 'Aset' }}</strong><br>
                            <span class="font-mono text-xs" style="color: #444;">{{ $m->asset?->code ?? '-' }}</span>
                        </td>
                        <td>{{ $m->asset?->room?->name ?? 'Ruang Umum' }}</td>
                        <td>{{ $m->issue_description }}</td>
                        <td>{{ $m->action_plan ?: '-' }}</td>
                        <td class="text-right font-mono">Rp {{ number_format($m->estimated_cost ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right font-mono">{{ $m->actual_cost ? 'Rp ' . number_format($m->actual_cost, 0, ',', '.') : '-' }}</td>
                        <td>{{ $m->technician_or_vendor ?: '-' }}</td>
                        <td class="text-center">
                            <strong>{{ $m->status }}</strong>
                            @if($m->completed_date)
                                <br><span class="font-mono" style="font-size: 8pt;">{{ \Carbon\Carbon::parse($m->completed_date)->format('d/m/Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px; font-style: italic;">
                            Belum ada catatan pemeliharaan atau servis barang inventaris.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #fafafa;">
                    <td colspan="6" class="text-right">TOTAL BIAYA PEMELIHARAAN:</td>
                    <td class="text-right font-mono">Rp {{ number_format($maintenances->sum('estimated_cost'), 0, ',', '.') }}</td>
                    <td class="text-right font-mono">Rp {{ number_format($maintenances->sum('actual_cost'), 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        @php
            $headmaster = $headmaster ?? \App\Models\Teacher::getHeadmaster();
            $sarprasManager = $sarprasManager ?? \App\Models\Teacher::getSarprasManager();
        @endphp
        <div class="signatures">
            <div class="sig-box float-left">
                <p>Mengetahui,<br>Kepala Sekolah,</p>
                <br><br><br>
                <p><strong><u>{{ $headmaster['name'] ?? $school->headmaster_name }}</u></strong><br>
                NIP. {{ $headmaster['nip'] ?? $school->headmaster_nip }}</p>
            </div>

            <div class="sig-box float-right">
                <p>{{ $school->city ?? 'Kabupaten Sukabumi' }}, {{ now()->translatedFormat('d F Y') }}<br>Pengelola Sarana &amp; Prasarana,</p>
                <br><br><br>
                <p><strong><u>{{ $sarprasManager['name'] ?? 'Pengelola Sarpras' }}</u></strong><br>
                NIP. {{ $sarprasManager['nip'] ?? '-' }}</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>

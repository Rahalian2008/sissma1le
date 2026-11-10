<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Ekspedisi Surat Dinas - {{ $school->name }}</title>
    <style>
        @page { size: landscape; margin: 12mm 10mm 12mm 10mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 9.5pt; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header h3 { margin: 0; font-size: 11pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 2px 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 8.5pt; }
        .doc-title { text-align: center; margin: 10px 0 14px 0; }
        .doc-title h4 { margin: 0; font-size: 12pt; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { margin: 2px 0; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 5px 6px; font-size: 8.5pt; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; font-size: 8pt; }
        .signatures { margin-top: 25px; width: 100%; page-break-inside: avoid; }
        .sig-box { width: 300px; text-align: center; font-size: 9.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Ekspedisi Surat - Tahun ' . $filterYear" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10mm 12mm; max-width: 330mm; margin: 0 auto;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>BUKU EKSPEDISI PENYAMPAIAN SURAT DINAS</h4>
        <p>Satuan Pendidikan: {{ $school->name }} | Tahun Anggaran: {{ $filterYear }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th style="width: 85px;">No. Ekspedisi</th>
                <th style="width: 75px;">Tgl Kirim</th>
                <th style="width: 140px;">Nomor & Tgl Surat</th>
                <th style="width: 150px;">Kepada / Tujuan</th>
                <th>Perihal Naskah Dinas</th>
                <th style="width: 110px;">Nama Penerima</th>
                <th style="width: 100px;">Paraf / Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expeditions as $idx => $exp)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $exp->expedition_number }}</td>
                    <td class="text-center">{{ $exp->delivery_date?->format('d/m/Y') }}</td>
                    <td>
                        <strong class="font-mono">{{ $exp->letter_number }}</strong><br>
                        <small>Tgl: {{ $exp->letter_date?->format('d/m/Y') }}</small>
                    </td>
                    <td>
                        <strong>{{ $exp->recipient_name }}</strong><br>
                        <small>({{ $exp->destination_type }})</small>
                    </td>
                    <td>{{ $exp->subject }}</td>
                    <td>
                        @if($exp->receiver_name)
                            <strong>{{ $exp->receiver_name }}</strong><br>
                            <small>{{ $exp->received_at?->format('d/m/Y H:i') }}</small>
                        @else
                            <span style="color: #6b7280; font-style: italic;">.....................................</span>
                        @endif
                    </td>
                    <td class="text-center" style="vertical-align: middle; height: 35px;">
                        @if($exp->receipt_status === 'Diterima')
                            <span style="color: #047857; font-weight: bold; font-size: 8pt;">[ DITERIMA ]</span>
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px;">Tidak ada catatan ekspedisi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box float-left">
            <p>Mengetahui,<br>{{ auth()->user()?->getRoleDisplayName() ?? 'Pengelola Persuratan & Ekspedisi' }},</p>
            <br><br><br>
            <p><strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Ekspedisi') }}</u></strong><br>
            NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}</p>
        </div>
        <div class="sig-box float-right">
            <p>{{ $school->district ?: 'Lengkong' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala {{ $school->name }},</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name }}</u></strong><br>
            Pembina Tk. I, IV/b<br>
            NIP. {{ $school->headmaster_nip }}</p>
        </div>
        <div class="clear"></div>
    </div>
    </div>
</body>
</html>

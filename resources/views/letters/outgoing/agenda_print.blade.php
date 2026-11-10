<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Agenda Surat Keluar - Tahun {{ $selectedYear }} - {{ $school->name }}</title>
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
    <x-print-toolbar :title="'Buku Agenda Surat Keluar - Tahun ' . $selectedYear" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10mm 12mm; max-width: 330mm; margin: 0 auto;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>BUKU AGENDA SURAT KELUAR</h4>
        <p>Satuan Pendidikan: {{ $school->name }} | Tahun Anggaran: <strong>{{ $selectedYear }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th style="width: 85px;">No. Agenda</th>
                <th style="width: 75px;">Tgl Surat</th>
                <th style="width: 150px;">Nomor Surat Keluar</th>
                <th style="width: 150px;">Kepada / Tujuan</th>
                <th>Perihal / Isi Ringkas Surat</th>
                <th style="width: 75px;">Kode Klasifikasi</th>
                <th style="width: 120px;">Penandatangan</th>
                <th style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($letters as $idx => $l)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono font-bold">{{ $l->agenda_number }}</td>
                    <td class="text-center">{{ $l->letter_date?->format('d/m/Y') }}</td>
                    <td><strong class="font-mono">{{ $l->letter_number }}</strong></td>
                    <td><strong>{{ $l->recipient }}</strong></td>
                    <td>{{ $l->subject }}</td>
                    <td class="text-center font-mono">{{ $l->classification_code ?: '-' }}</td>
                    <td>
                        {{ $l->signer_name }}<br>
                        <small class="font-mono">{{ $l->signer_nip ?: '-' }}</small>
                    </td>
                    <td class="text-center">
                        {{ $l->status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px;">Belum ada surat keluar pada tahun ini.</td>
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

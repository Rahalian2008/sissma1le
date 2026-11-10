<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Ekspedisi Penyerahan Ijazah - {{ $school->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9.5pt; font-style: italic; }
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; font-size: 13pt; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .signatures { margin-top: 30px; width: 100%; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: center; }
        @media print {
            body { padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Buku Ekspedisi Ijazah & SKL - ' . $selectedYear" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />

        <div class="title">
        BUKU EKSPEDISI TANDA TERIMA PENYERAHAN IJAZAH & SKL
    </div>
    <div style="text-align: center; margin-top: -10px; margin-bottom: 20px; font-size: 11pt;">
        Tahun Ajaran / Tahun Kelulusan: <strong>{{ $selectedYear }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 20%;">Nama Lengkap Peserta Didik</th>
                <th style="width: 14%;">NIS / NISN</th>
                <th style="width: 18%;">Nomor Seri Ijazah</th>
                <th style="width: 12%;">Tanggal Penyerahan</th>
                <th style="width: 10%;">Cap 3 Jari</th>
                <th style="width: 22%;">Tanda Tangan & Nama Penerima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($diplomas as $index => $d)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $d->student?->name }}</strong>
                        <div style="font-size: 8.5pt; color: #444;">{{ $d->student?->current_rombel ?? 'Lulusan' }}</div>
                    </td>
                    <td style="text-align: center; font-family: monospace;">
                        {{ $d->student?->nis }}<br>
                        {{ $d->student?->nisn }}
                    </td>
                    <td style="font-family: monospace; font-size: 9.5pt;">
                        <strong>{{ $d->diploma_number ?: '-' }}</strong>
                    </td>
                    <td style="text-align: center;">
                        {{ $d->handover_date ? $d->handover_date->format('d/m/Y') : '-' }}
                    </td>
                    <td style="text-align: center;">
                        {{ $d->three_fingerprint_status ? '✓ Sudah' : '○ Belum' }}
                    </td>
                    <td>
                        @if($d->status === 'Sudah Diambil')
                            <div style="height: 35px; border-bottom: 1px dotted #888;"></div>
                            <div style="font-size: 9pt; font-weight: bold; margin-top: 3px;">
                                ({{ $d->recipient_name ?: $d->student?->name }})
                            </div>
                            <div style="font-size: 8pt; color: #666;">Hubungan: {{ $d->recipient_relation }}</div>
                        @else
                            <em style="color: #666; font-size: 9pt;">[Belum Diambil / Tersimpan di Brankas TU]</em>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Belum ada catatan ekspedisi ijazah pada tahun ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-block">
            Mengetahui,<br>
            Kepala Sekolah<br><br><br><br>
            <strong><u>{{ $school->headmaster_name ?? '....................................' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '....................................' }}
        </div>
        <div class="sig-block">
            {{ $school->city ?? $school->district ?? 'Lengkong' }}, {{ now()->translatedFormat('d F Y') }}<br>
            {{ auth()->user()?->getRoleDisplayName() ?? 'Kepala Urusan Tata Usaha / Petugas Ijazah' }}<br><br><br><br>
            <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Ijazah') }}</u></strong><br>
            NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
        </div>
    </div>
    </div>
</body>
</html>

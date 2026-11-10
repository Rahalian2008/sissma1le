<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Mutasi Peserta Didik - {{ $school->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5pt; line-height: 1.3; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 12.5pt; text-transform: uppercase; font-weight: normal; }
        .header h2 { margin: 0; font-size: 13.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 0; font-size: 15.5pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0 0 0; font-size: 9pt; font-style: italic; }
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; font-size: 13pt; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9.5pt; }
        th, td { border: 1px solid #000; padding: 6px 7px; vertical-align: middle; }
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
    <x-print-toolbar :title="'Buku Catatan Mutasi Peserta Didik'" orientation="landscape" defaultPaper="f4" />

    <div class="printable-sheet landscape-sheet" style="padding: 10px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="title">
        BUKU CATATAN MUTASI PESERTA DIDIK
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 11%;">Tanggal</th>
                <th style="width: 20%;">Nama Lengkap Peserta Didik</th>
                <th style="width: 14%;">NIS / NISN</th>
                <th style="width: 14%;">Jenis Mutasi</th>
                <th style="width: 15%;">No. Surat Mutasi</th>
                <th style="width: 22%;">Sekolah Asal / Tujuan & Alasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutations as $idx => $m)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center;">{{ $m->mutation_date ? $m->mutation_date->format('d/m/Y') : '-' }}</td>
                    <td>
                        <strong>{{ $m->student?->name }}</strong>
                        <div style="font-size: 8.5pt; color: #555;">{{ $m->student?->current_rombel ?? 'Siswa' }}</div>
                    </td>
                    <td style="text-align: center; font-family: monospace;">
                        {{ $m->student?->nis }}<br>
                        {{ $m->student?->nisn }}
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $m->mutation_type }}
                    </td>
                    <td style="font-family: monospace; font-size: 8.5pt;">
                        {{ $m->document_number ?: '-' }}
                    </td>
                    <td>
                        <strong>{{ $m->school_target_or_origin ?: '-' }}</strong>
                        @if($m->reason)
                            <div style="font-size: 8.5pt; color: #555;">Ket: {{ $m->reason }}</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Belum ada riwayat mutasi siswa yang tercatat.</td>
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
            {{ auth()->user()?->getRoleDisplayName() ?? 'Petugas Kesiswaan / Tata Usaha' }}<br><br><br><br>
            <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Kesiswaan') }}</u></strong><br>
            NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
        </div>
    </div>
    </div>
</body>
</html>

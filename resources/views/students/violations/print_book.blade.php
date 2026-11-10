<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Catatan Kedisiplinan & Pelanggaran Siswa - {{ $school->name }}</title>
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
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <x-print-toolbar orientation="landscape" default-size="f4" title="Buku Catatan Kedisiplinan & Pelanggaran Siswa" />

    <!-- Kop Surat Resmi -->
    <x-print-kop :school="$school" />


    <div class="title">
        BUKU CATATAN KEDISIPLINAN & PELANGGARAN TATA TERTIB SISWA
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 20%;">Nama Lengkap Peserta Didik</th>
                <th style="width: 12%;">NIS / Rombel</th>
                <th style="width: 24%;">Bentuk Pelanggaran Tata Tertib</th>
                <th style="width: 8%;">Poin</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 10%;">Petugas BK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($violations as $idx => $v)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center;">{{ $v->violation_date ? $v->violation_date->format('d/m/Y') : '-' }}</td>
                    <td><strong>{{ $v->student?->name }}</strong></td>
                    <td style="text-align: center; font-family: monospace;">
                        {{ $v->student?->nis }}<br>
                        <span style="font-size: 8.5pt;">{{ $v->student?->current_rombel ?: '-' }}</span>
                    </td>
                    <td>
                        {{ $v->violation_name }}
                        @if($v->action_taken)
                            <div style="font-size: 8.5pt; color: #555;">Tindakan: {{ $v->action_taken }}</div>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #dc2626;">+{{ $v->points }}</td>
                    <td style="text-align: center; font-size: 8.5pt;">{{ $v->resolution_status }}</td>
                    <td>{{ $v->officer_name ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Belum ada catatan pelanggaran siswa.</td>
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
            {{ auth()->user()?->getRoleDisplayName() ?? 'Koordinator Bimbingan Konseling (BK)' }}<br><br><br><br>
            <strong><u>{{ strtoupper(auth()->user()?->getOfficialName() ?? 'Petugas Kesiswaan / BK') }}</u></strong><br>
            NIP. {{ auth()->user()?->getOfficialNip() ?? '-' }}
        </div>
    </div>
</body>
</html>

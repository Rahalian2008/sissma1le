<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Pelanggaran &amp; Kedisiplinan Siswa - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #111; line-height: 1.3; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 4px; font-size: 12pt; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 9.5pt; color: #333; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9pt; }
        th, td { border: 1px solid #000; padding: 5px 6px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .signatures-table { width: 100%; margin-top: 25px; border: none; font-size: 10pt; }
        .signatures-table td { border: none; padding: 0; vertical-align: top; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Kop Surat Resmi dari Master Data Logo & Kop Sekolah -->
    <x-print-kop :school="$school" />

    <div class="title">DAFTAR REKAPITULASI PELANGGARAN &amp; KEDISIPLINAN SISWA</div>
    <div class="subtitle">Catatan Kejadian Pelanggaran Tata Tertib, Akumulasi Poin, dan Langkah Pembinaan Kesiswaan</div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="19%">Nama Siswa &amp; NISN</th>
                <th width="8%">Kelas</th>
                <th width="24%">Jenis Pelanggaran</th>
                <th width="15%">Kategori</th>
                <th width="6%">Poin</th>
                <th width="17%">Langkah Pembinaan</th>
                <th width="8%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPoin = 0; @endphp
            @forelse($violations as $idx => $v)
                @php $totalPoin += $v->points; @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $v->student?->name ?? 'Siswa' }}</strong>
                        <div style="font-size: 8pt; color: #444;">NISN: {{ $v->student?->nisn ?: '-' }}</div>
                    </td>
                    <td class="text-center">{{ $v->student?->schoolClass?->name ?? '-' }}</td>
                    <td>
                        <strong>{{ $v->title }}</strong>
                        @if($v->chronology)<div style="font-size: 8pt; color: #555;">{{ $v->chronology }}</div>@endif
                    </td>
                    <td>{{ $v->category?->name ?? '-' }}</td>
                    <td class="text-center font-bold" style="color: #b91c1c;">{{ $v->points }}</td>
                    <td>{{ $v->guidance_notes ?: ($v->status === 'selesai' ? 'Selesai' : 'Dalam Pembinaan') }}</td>
                    <td class="text-center">{{ $v->date?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada data pelanggaran yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
        @if($violations->isNotEmpty())
            <tfoot>
                <tr style="font-weight: bold; background-color: #f7f7f7;">
                    <td colspan="5" class="text-center">TOTAL AKUMULASI POIN PELANGGARAN</td>
                    <td class="text-center font-bold" style="color: #b91c1c;">{{ $totalPoin }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <table class="signatures-table">
        <tr>
            <td width="50%" class="text-center">
                Mengetahui,<br>
                Kepala SMA Negeri 1 Lengkong
                <br><br><br><br>
                <strong><u>{{ $headmaster['name'] ?? ($school->headmaster_name ?? 'Pian Sopian, S.Pd., M.M.Pd') }}</u></strong><br>
                NIP. {{ $headmaster['nip'] ?? ($school->headmaster_nip ?? '198009132008011005') }}
            </td>
            <td width="50%" class="text-center">
                Lengkong, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                Wakil Kepala Sekolah Bidang Kesiswaan
                <br><br><br><br>
                <strong><u>{{ $kesiswaanTeacher?->name ?? 'Winda Dwi Atyani, S.Pd., M.Pd.' }}</u></strong><br>
                NIP. {{ $kesiswaanTeacher?->nip ?? '197911162008012007' }}
            </td>
        </tr>
    </table>
</body>
</html>

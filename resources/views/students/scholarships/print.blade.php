<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Penerima Beasiswa & Bantuan Siswa (PIP / KETM)</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5pt; color: #111; line-height: 1.3; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 4px; font-size: 12pt; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 9.5pt; color: #333; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9.5pt; }
        th, td { border: 1px solid #000; padding: 4.5px 6px; }
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

    <div class="title">DAFTAR REKAPITULASI PENERIMA BEASISWA & BANTUAN SISWA</div>
    <div class="subtitle">Program Indonesia Pintar (PIP), BPMU / KETM Pemprov Jawa Barat, dan Bantuan Pendidikan Siswa</div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="18%">Nama Siswa & NISN</th>
                <th width="8%">Kelas</th>
                <th width="16%">Program Beasiswa</th>
                <th width="13%">Sumber Dana</th>
                <th width="11%">Nominal (Rp)</th>
                <th width="7%">Periode</th>
                <th width="8%">Tgl Usulan</th>
                <th width="8%">Tgl SK</th>
                <th width="8%">Tgl Cair</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDana = 0; @endphp
            @forelse ($scholarships as $index => $sch)
                @php $totalDana += $sch->amount; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $sch->student?->name ?? '-' }}</strong>
                        <div style="font-size: 8.5pt; color: #444;">NISN: {{ $sch->student?->nisn ?? '-' }}</div>
                    </td>
                    <td class="text-center">{{ $sch->student?->schoolClass?->name ?? '-' }}</td>
                    <td>
                        {{ $sch->scholarship_name }}
                        @if($sch->sk_number)
                            <div style="font-size: 8pt; color: #555;">SK: {{ $sch->sk_number }}</div>
                        @endif
                    </td>
                    <td>{{ $sch->source }}</td>
                    <td class="text-right">{{ number_format($sch->amount, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $sch->period }}</td>
                    <td class="text-center">{{ $sch->proposal_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center">{{ $sch->sk_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center">{{ $sch->disbursement_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center"><strong>{{ $sch->status }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Belum ada data beasiswa/bantuan yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
        @if($scholarships->isNotEmpty())
            <tfoot>
                <tr style="font-weight: bold; background-color: #f7f7f7;">
                    <td colspan="5" class="text-center">TOTAL KESELURUHAN DANA BANTUAN TERCATAT</td>
                    <td class="text-right">Rp {{ number_format($totalDana, 0, ',', '.') }}</td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    @php
        $headmaster = \App\Models\Teacher::getHeadmaster();
        $kesiswaanTeacher = \App\Models\Teacher::where(function($q) {
            $q->where('position', 'like', '%Kesiswaan%')
              ->orWhere('additional_tasks', 'like', '%Kesiswaan%');
        })->first();
    @endphp

    <table class="signatures-table">
        <tr>
            <td width="50%" class="text-center">
                Mengetahui,<br>
                Kepala Sekolah
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

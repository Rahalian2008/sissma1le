<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Guru & Tendik - Bulan {{ $monthName }} {{ $year }} - {{ $school->name }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 8.5pt; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2.5px double #000; padding-bottom: 5px; margin-bottom: 8px; }
        .header h3 { margin: 0; font-size: 10.5pt; text-transform: uppercase; }
        .header h2 { margin: 1px 0; font-size: 11.5pt; text-transform: uppercase; font-weight: bold; }
        .header h1 { margin: 1px 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 1px 0; font-size: 8pt; }
        .doc-title { text-align: center; margin: 8px 0 10px 0; }
        .doc-title h4 { margin: 0; font-size: 11pt; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { margin: 2px 0; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #000; padding: 3px 2px; font-size: 7.5pt; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; font-size: 7.5pt; }
        .weekend { background-color: #e5e7eb; color: #6b7280; font-weight: bold; }
        .signature-section { margin-top: 15px; width: 100%; page-break-inside: avoid; }
        .signature-box { width: 320px; text-align: center; font-size: 8.5pt; }
        .float-left { float: left; }
        .float-right { float: right; }
        .clear { clear: both; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Daftar Hadir GTK - ' . $monthName . ' ' . $year" orientation="landscape" defaultPaper="f4" margin="8mm 6mm" />

    <div class="printable-sheet landscape-sheet" style="padding: 6px;">
        <!-- Kop Surat Resmi -->
        <x-print-kop :school="$school" />


    <div class="doc-title">
        <h4>DAFTAR HADIR KERJA {{ strtoupper($type === 'Semua' ? 'GURU DAN TENAGA KEPENDIDIKAN' : $type) }}</h4>
        <p>Bulan: <strong>{{ strtoupper($monthName) }} {{ $year }}</strong> | Satuan Pendidikan: {{ $school->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No.</th>
                <th rowspan="2" style="width: 140px;">Nama Pegawai & NIP</th>
                <th rowspan="2" style="width: 45px;">Gol. / Stat</th>
                <th rowspan="2" style="width: 80px;">Jabatan</th>
                <th colspan="{{ $daysInMonth }}">Tanggal Pelaksanaan Tugas Bulan {{ $monthName }} {{ $year }}</th>
                <th colspan="5">Rekapitulasi</th>
                <th rowspan="2" style="width: 50px;">Ket</th>
            </tr>
            <tr>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeekIso; // 6 = Saturday, 7 = Sunday
                        $isWeekend = ($dayOfWeek >= 6);
                    @endphp
                    <th style="width: 17px;" class="{{ $isWeekend ? 'weekend' : '' }}">{{ $d }}</th>
                @endfor
                <th style="width: 18px;">H</th>
                <th style="width: 18px;">S</th>
                <th style="width: 18px;">I</th>
                <th style="width: 18px;">C</th>
                <th style="width: 18px;">TK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $idx => $e)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $e->formatted_name }}</strong><br>
                        <span class="font-mono">{{ $e->nip ?: '-' }}</span>
                    </td>
                    <td class="text-center">
                        {{ $e->rank_group ?: '-' }}<br>
                        <small style="color: #4b5563;">{{ $e->employment_status }}</small>
                    </td>
                    <td>{{ Str::limit($e->position ?: ($e->employment_type === 'Guru' ? 'Guru Mata Pelajaran' : 'Tenaga Administrasi'), 25) }}</td>
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dayOfWeek = \Carbon\Carbon::createFromDate($year, $month, $d)->dayOfWeekIso;
                            $isWeekend = ($dayOfWeek >= 6);
                        @endphp
                        <td class="text-center {{ $isWeekend ? 'weekend' : '' }}">
                            @if($isWeekend)
                                &bull;
                            @else
                                &nbsp;
                            @endif
                        </td>
                    @endfor
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $daysInMonth + 10 }}" class="text-center" style="padding: 15px;">Tidak ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box float-left">
            <p>Mengetahui,<br>Pengelola Kepegawaian / Kasubag TU</p>
            <br><br><br>
            <p><strong><u>....................................................</u></strong><br>
            NIP. ................................................</p>
        </div>

        <div class="signature-box float-right">
            <p>{{ $school->district ?: 'Lengkong' }}, {{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->translatedFormat('d F Y') }}<br>Kepala {{ $school->name }},</p>
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

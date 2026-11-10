<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Agenda Pembelajaran Kelas {{ $rombel }} {{ $isBlank ? '(Format Kosong 10 Baris)' : 'Periode ' . \Carbon\Carbon::parse($month.'-01')->translatedFormat('F Y') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.5pt;
            line-height: 1.15;
            color: #000;
            background: #fff;
        }
        body {
            padding: 6px 8px;
        }
        
        /* Penyesuaian Kop Surat Khusus Mode Lanskap 1 Lembar Pas */
        .header-kop-banner {
            text-align: center;
            margin-bottom: 8px !important;
            padding-bottom: 3px !important;
            border-bottom: 2.5px double #000 !important;
        }
        .header-kop-banner img {
            max-width: 100%;
            width: 100%;
            height: auto;
            max-height: 120px !important;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .header-kop {
            text-align: center;
            margin-bottom: 8px !important;
            padding-bottom: 3px !important;
            border-bottom: 2.5px double #000 !important;
        }
        .header-kop table td {
            padding: 0 6px !important;
        }
        .header-kop img {
            max-height: 65px !important;
            width: auto !important;
        }
        .header-kop h2 {
            font-size: 13pt !important;
            margin: 2px 0 !important;
        }
        .header-kop p {
            margin: 1px 0 !important;
            font-size: 8.5pt !important;
        }

        .title-box {
            text-align: center;
            margin-bottom: 4px;
        }
        .title-box h2 {
            margin: 0;
            font-size: 11pt;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.4px;
            line-height: 1.15;
        }
        .title-box p {
            margin: 1px 0 0 0;
            font-size: 8pt;
            font-weight: bold;
            color: #222;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 3px;
            font-size: 8pt;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 1px 2px;
            vertical-align: middle;
            border: none;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.8pt;
            margin-top: 2px;
        }
        table.data-table th, 
        table.data-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.2pt;
            line-height: 1.1;
        }
        .blank-row {
            height: 25px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .recap-box {
            margin-top: 3px;
            font-size: 7.2pt;
            line-height: 1.15;
            border: 1px solid #000;
            padding: 2.5px 6px;
            background-color: #fafafa;
            page-break-inside: avoid;
        }

        .signatures {
            margin-top: 6px;
            width: 100%;
            display: table;
            page-break-inside: avoid;
        }
        .sig-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
            line-height: 1.15;
        }

        @media print {
            body { 
                padding: 0 !important; 
            }
            .no-print { 
                display: none !important; 
            }
            html, body {
                width: 100%;
                height: 100%;
                overflow: hidden;
            }
            .page-sheet {
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                break-inside: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body>
    <x-print-toolbar :title="'Agenda Pembelajaran Kelas ' . $rombel" orientation="landscape" defaultPaper="f4" margin="5mm 8mm 4mm 8mm" />

    <div class="no-print" style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-family: sans-serif; font-size: 12px;">
        <div>
            <strong style="color: #0f172a;">Mode:</strong>
            <span style="color: #475569; margin-left: 4px;">
                {{ $isBlank ? 'Format Kosong Siap Tulis Manual (10 Baris)' : 'Format Terisi Sesuai Data KBM Digital' }}
            </span>
        </div>
        <div>
            @if($isBlank)
                <a href="{{ route('students.learning-agendas.print', ['rombel' => $rombel, 'month' => $month, 'blank' => 0]) }}" style="padding: 4px 10px; background-color: #0284c7; color: white; border-radius: 6px; text-decoration: none; font-weight: bold;">
                    👁️ Lihat Format Berisi Data
                </a>
            @else
                <a href="{{ route('students.learning-agendas.print', ['rombel' => $rombel, 'month' => $month, 'blank' => 1]) }}" style="padding: 4px 10px; background-color: #475569; color: white; border-radius: 6px; text-decoration: none; font-weight: bold;">
                    📝 Beralih ke Format Kosong (10 Baris)
                </a>
            @endif
        </div>
    </div>

    <div class="page-sheet printable-sheet landscape-sheet">
        <!-- Kop Surat Resmi Lembaga -->
        <x-print-kop :school="$school" />

        <div class="title-box">
            <h2>BUKU AGENDA & JURNAL HARIAN PEMBELAJARAN KELAS</h2>
            <p>STANDAR KURIKULUM MERDEKA - TAHUN AJARAN {{ $academicYear?->year ?? date('Y').'/'.(date('Y')+1) }} (SEMESTER {{ strtoupper($academicYear?->semester ?? 'GANJIL') }})</p>
        </div>

        <!-- Identitas Administrasi Kelas -->
        <table class="meta-table">
            <tr>
                <td style="width: 16%;">Satuan Pendidikan</td>
                <td style="width: 2%;">:</td>
                <td style="width: 37%;"><strong>{{ $school->name }}</strong></td>
                <td style="width: 16%;">Bulan / Periode</td>
                <td style="width: 2%;">:</td>
                <td style="width: 27%;"><strong>{{ \Carbon\Carbon::parse($month.'-01')->translatedFormat('F Y') }}</strong></td>
            </tr>
            <tr>
                <td>Rombongan Belajar</td>
                <td>:</td>
                <td><strong>{{ $rombel }}</strong></td>
                <td>Wali Kelas</td>
                <td>:</td>
                <td><strong>{{ $waliKelas?->formatted_name ?? $waliKelas?->name ?? '....................................' }}</strong></td>
            </tr>
            <tr>
                <td>Kurikulum</td>
                <td>:</td>
                <td>Kurikulum Merdeka</td>
                <td>NIP Wali Kelas</td>
                <td>:</td>
                <td>{{ $waliKelas?->nip ?: '....................................' }}</td>
            </tr>
        </table>

        <!-- Tabel Agenda & Jurnal Pembelajaran (10 Baris) -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;" rowspan="2">No</th>
                    <th style="width: 9%;" rowspan="2">Hari / Tanggal</th>
                    <th style="width: 7%;" rowspan="2">Jam Ke-</th>
                    <th style="width: 14%;" rowspan="2">Mata Pelajaran & Guru Pengampu</th>
                    <th style="width: 18%;" rowspan="2">Tujuan Pembelajaran (TP) /<br>Lingkup Materi Pokok</th>
                    <th style="width: 15%;" rowspan="2">Ringkasan Kegiatan & Refleksi</th>
                    <th colspan="4" style="width: 9%;">Presensi</th>
                    <th style="width: 13%;" rowspan="2">Nama Siswa Tidak Hadir<br>(S/I/A) / Keterangan</th>
                    <th style="width: 7%;" rowspan="2">Catatan Khusus</th>
                    <th style="width: 5%;" rowspan="2">Paraf</th>
                </tr>
                <tr>
                    <th style="width: 2.25%;">H</th>
                    <th style="width: 2.25%;">S</th>
                    <th style="width: 2.25%;">I</th>
                    <th style="width: 2.25%;">A</th>
                </tr>
            </thead>
            <tbody>
                @if($isBlank)
                    {{-- Mode Format Kosong Tepat 10 Baris untuk Isian Tulis Tangan Manual --}}
                    @for($i = 1; $i <= ($rowCount ?? 10); $i++)
                        <tr class="blank-row">
                            <td class="text-center font-bold">{{ $i }}</td>
                            <td></td>
                            <td class="text-center"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                @else
                    {{-- Mode Format Berisi Catatan KBM Digital --}}
                    @forelse($agendas as $idx => $agenda)
                        <tr>
                            <td class="text-center font-bold">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $agenda->date ? $agenda->date->translatedFormat('l') : '-' }}</strong><br>
                                {{ $agenda->date ? $agenda->date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">{{ $agenda->time_slot }}</td>
                            <td>
                                <strong>{{ $agenda->subject }}</strong><br>
                                <span style="font-size: 7.5pt; color: #333;">
                                    {{ $agenda->teacher?->formatted_name ?? $agenda->teacher_name }}
                                </span>
                                @if($agenda->teacher?->nip)
                                    <br><span style="font-size: 7pt; color: #555;">NIP. {{ $agenda->teacher->nip }}</span>
                                @endif
                            </td>
                            <td>{{ $agenda->learning_topic }}</td>
                            <td>{{ $agenda->activities_summary ?: '-' }}</td>
                            <td class="text-center font-bold">{{ $agenda->students_present }}</td>
                            <td class="text-center">{{ $agenda->students_sick ?: '-' }}</td>
                            <td class="text-center">{{ $agenda->students_permitted ?: '-' }}</td>
                            <td class="text-center">{{ $agenda->students_absent ?: '-' }}</td>
                            <td>{{ $agenda->absent_notes ?: '-' }}</td>
                            <td>{{ $agenda->class_notes ?: '-' }}</td>
                            <td class="text-center" style="vertical-align: middle;">
                                @if($agenda->teacher_signed)
                                    <span style="font-size: 10pt;">✓</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        @for($i = 1; $i <= 10; $i++)
                            <tr class="blank-row">
                                <td class="text-center font-bold">{{ $i }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endfor
                    @endforelse
                @endif
            </tbody>
            @if(!$isBlank && $agendas->isNotEmpty())
                <tfoot>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td colspan="6" class="text-right">JUMLAH REKAPITULASI PRESENSI :</td>
                        <td class="text-center">{{ $totalHadir }}</td>
                        <td class="text-center">{{ $totalSakit }}</td>
                        <td class="text-center">{{ $totalIzin }}</td>
                        <td class="text-center">{{ $totalAlpa }}</td>
                        <td colspan="3" class="text-center">{{ $totalSessions }} Sesi Pembelajaran Terlaksana</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <!-- Kotak Ringkasan & Catatan Supervisi -->
        <div class="recap-box">
            <strong>Catatan Supervisi KBM Kelas:</strong> Lembar agenda pembelajaran kelas ini mencatat pelaksanaan kurikulum secara faktual di ruang kelas, mencakup ketercapaian Tujuan Pembelajaran (TP), ketertiban jam tatap muka, serta pemantauan kehadiran dan nama siswa yang berhalangan hadir.
        </div>

        <!-- Tanda Tangan 3 Pihak (Kepala Sekolah, Wakasek Kurikulum, Wali Kelas) -->
        <div class="signatures">
            <div class="sig-col">
                Mengetahui,<br>
                Kepala Sekolah
                <div style="height: 38px;"></div>
                <strong><u>{{ $kepalaSekolah?->formatted_name ?? $school->headmaster_name ?? 'Pian Sopian, S.Pd. MM.Pd.' }}</u></strong><br>
                NIP. {{ $kepalaSekolah?->nip ?? $school->headmaster_nip ?? '198009132008011005' }}
            </div>
            <div class="sig-col">
                Mengetahui,<br>
                Wakil Kepala Sekolah Bid. Kurikulum
                <div style="height: 38px;"></div>
                <strong><u>{{ $wakasekKurikulum?->formatted_name ?? '....................................' }}</u></strong><br>
                NIP. {{ $wakasekKurikulum?->nip ?? '....................................' }}
            </div>
            <div class="sig-col">
                Lengkong, {{ now()->translatedFormat('d F Y') }}<br>
                Wali Kelas {{ $rombel }}
                <div style="height: 38px;"></div>
                <strong><u>{{ $waliKelas?->formatted_name ?? $waliKelas?->name ?? '....................................' }}</u></strong><br>
                NIP. {{ $waliKelas?->nip ?? '....................................' }}
            </div>
        </div>
    </div>
</body>
</html>

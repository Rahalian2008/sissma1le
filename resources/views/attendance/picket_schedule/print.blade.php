<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Resmi Petugas Piket - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
        }
        .container {
            max-width: 820px;
            margin: 0 auto;
        }
        .no-print-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
        }
        .btn-print {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #4338ca; }
        .btn-close {
            background: #64748b;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 6px;
        }
        .title-block {
            text-align: center;
            margin: 14px 0 16px 0;
        }
        .title-block h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .title-block p {
            margin: 4px 0 0 0;
            font-size: 10.5pt;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 10pt;
        }
        .schedule-table th, .schedule-table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }
        .schedule-table th {
            background: #f8fafc;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            padding: 0 10px;
            font-size: 10.5pt;
        }
        .sig-space {
            height: 65px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sig-title {
            font-size: 9.5pt;
            color: #222;
        }
        @media print {
            .no-print-bar { display: none !important; }
            body { padding: 0; }
            .schedule-table th { background: transparent !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Toolbar Non-Print -->
        <div class="no-print-bar">
            <div>
                <strong>Jadwal Petugas Piket Sekolah</strong> (Guru & TU Piket Mingguan)
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn-print">
                    🖨️ Cetak Jadwal (Print)
                </button>
                <button type="button" onclick="window.close()" class="btn-close">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Kop Surat Resmi Sekolah -->
        <x-print-kop :school="$school" />

        <!-- Judul -->
        <div class="title-block">
            <h3>JADWAL PETUGAS PIKET KETERTIBAN & PELAYANAN SEKOLAH</h3>
            <p>Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }} &bull; SMA Negeri 1 Lengkong</p>
        </div>

        <!-- Tabel Jadwal Mingguan -->
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Hari</th>
                    <th style="width: 32%;">Nama Petugas Piket</th>
                    <th style="width: 18%;">Tugas / Peran</th>
                    <th style="width: 23%;">Pos / Lokasi Tugas</th>
                    <th style="width: 15%;">Jam Tugas</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $allDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                @endphp
                @foreach($allDays as $dayName)
                    @php
                        $dayList = $weeklyPickets->get($dayName, collect());
                        $rowspan = max(1, $dayList->count());
                    @endphp
                    @forelse($dayList as $idx => $p)
                        <tr>
                            @if($idx === 0)
                                <td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold; vertical-align: top; background: #fafafa;">
                                    {{ $dayName }}
                                </td>
                            @endif
                            <td>
                                <strong>{{ $p->person_name }}</strong>
                                @if($p->teacher?->nip)
                                    <br><small style="color: #475569;">NIP: {{ $p->teacher->nip }}</small>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($p->role_type === 'GURU_PIKET')
                                    <span style="font-weight: bold;">Guru Piket</span>
                                @elseif($p->role_type === 'TU_PIKET')
                                    <span>TU Piket</span>
                                @else
                                    <span>Koordinator Piket</span>
                                @endif
                            </td>
                            <td>{{ $p->location }}</td>
                            <td style="text-align: center; font-family: monospace;">
                                {{ substr($p->start_time, 0, 5) }} - {{ substr($p->end_time, 0, 5) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $dayName }}</td>
                            <td colspan="4" style="text-align: center; color: #64748b; font-style: italic;">
                                Belum ada penugasan piket pada hari {{ $dayName }}
                            </td>
                        </tr>
                    @endforelse
                @endforeach
            </tbody>
        </table>

        <!-- Catatan Tugas Pokok Piket -->
        <div style="font-size: 9pt; margin-top: 10px; color: #333;">
            <strong>Tugas Pokok & Fungsi Petugas Piket:</strong>
            <ol style="margin: 2px 0 0 18px; padding: 0;">
                <li>Mengawasi ketertiban gerbang, seragam, dan kedisiplinan siswa pada jam masuk sekolah.</li>
                <li>Mencatat dan memberikan verifikasi surat izin keluar lingkungan sekolah atau pulang cepat bagi siswa.</li>
                <li>Melakukan supervisi keliling kelas pada saat pergantian jam pelajaran.</li>
                <li>Menerima tamu dinas / orang tua siswa dan mendokumentasikan pada buku tamu piket.</li>
            </ol>
        </div>

        <!-- Tanda Tangan Mengetahui Kepala Sekolah (Kiri) & Wakasek Kesiswaan (Kanan) -->
        <table class="signatures">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala SMA Negeri 1 Lengkong,
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $school?->headmaster_name ?: 'PIAN SOPIAN, S.Pd.' }}</div>
                    <div class="sig-title">NIP. {{ $school?->headmaster_nip ?: '198009132008011005' }}</div>
                </td>
                <td style="text-align: right;">
                    Lengkong, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                    Wakasek Bid. Kesiswaan,
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ \App\Models\Teacher::getWakasekKesiswaan()['name'] ?? 'Wakasek Kesiswaan, S.Pd.' }}</div>
                    <div class="sig-title">NIP. {{ \App\Models\Teacher::getWakasekKesiswaan()['nip'] ?? '197805122005011004' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>


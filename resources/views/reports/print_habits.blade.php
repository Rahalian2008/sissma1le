<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Karakter 7 Kebiasaan - {{ $selectedClass?->name ?? 'Semua Kelas' }} ({{ $startDate }} s.d. {{ $endDate }})</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: 10pt;
            line-height: 1.3;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .no-print-bar form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .no-print-bar input, .no-print-bar select {
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #475569;
            background: #0f172a;
            color: #fff;
            font-size: 12px;
        }
        .no-print-bar button {
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            transition: 0.2s;
        }
        .btn-print { background: #10b981; color: #fff; }
        .btn-back { background: #475569; color: #fff; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: bold; }
        .btn-filter { background: #6366f1; color: #fff; }

        .doc-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15px 25px;
        }
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 130px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-header h4 { margin: 0; font-size: 11pt; font-weight: normal; }
        .kop-header h3 { margin: 2px 0; font-size: 13pt; font-weight: bold; }
        .kop-header h2 { margin: 2px 0; font-size: 16pt; font-weight: 900; }
        .kop-header p { margin: 2px 0 0 0; font-size: 8.5pt; color: #222; }

        .doc-title { text-align: center; margin: 14px 0 12px 0; }
        .doc-title h3 { margin: 0; font-size: 13pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { margin: 3px 0 0 0; font-size: 10pt; }

        .habit-legend {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 8pt;
            margin-bottom: 12px;
            border-radius: 4px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 9pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 4px 5px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
        }
        table.data-table td.center { text-align: center; }

        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signatures td { vertical-align: top; font-size: 10pt; padding: 0 10px; }

        @media print {
            .no-print-bar { display: none !important; }
            body { background: none; }
            .doc-container { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Controls Bar (No Print) -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('reports.index') }}" class="btn-back">⬅ Kembali</a>
            <span style="font-weight: bold; font-size: 14px;">CETAK LAPORAN 7 KEBIASAAN KARAKTER ANAK HEBAT</span>
        </div>

        <form action="{{ route('reports.print.habits') }}" method="GET">
            <label>Mulai:</label>
            <input type="date" name="start_date" value="{{ $startDate }}">
            
            <label>Selesai:</label>
            <input type="date" name="end_date" value="{{ $endDate }}">

            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
                <label>Kelas:</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="{{ $selectedClass?->name ?? 'Kelas Binaan' }}" readonly style="background: #334155; color: #fff; border: 1px solid #475569; padding: 5px 10px; border-radius: 6px; cursor: not-allowed; font-weight: bold; width: 120px;">
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            @endif

            <button type="submit" class="btn-filter">Terapkan Filter</button>
            <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
        </form>
    </div>

    <!-- Printable Paper Area -->
    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($kopImageUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $kopImageUrl }}" alt="KOP Surat Resmi {{ $schoolName }}">
            </div>
        @else
            <div class="kop-header">
                <h4>PEMERINTAH PROVINSI {{ $schoolProvince ?? 'JAWA BARAT' }}</h4>
                <h3>DINAS PENDIDIKAN</h3>
                <h4>{{ $schoolBranch ?? 'CABANG DINAS PENDIDIKAN WILAYAH V' }}</h4>
                <h2>{{ strtoupper($schoolName) }}</h2>
                <p>NPSN: {{ $schoolNpsn ?? '20539123' }} • NSS: {{ $schoolNss ?? '301051408001' }} • Akreditasi: {{ $schoolAccreditation ?? 'A (Unggul)' }}</p>
                <p>{{ $schoolAddress ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Provinsi Jawa Barat' }} • Email: {{ $schoolEmail ?? 'sman1lengkong@gmail.com' }} • Web: {{ $schoolWebsite ?? 'https://sman1lengkong.sch.id' }}</p>
            </div>
        @endif

        <!-- Judul Laporan -->
        <div class="doc-title">
            <h3>LAPORAN PERKEMBANGAN 7 KEBIASAAN ANAK INDONESIA HEBAT</h3>
            <p>
                <strong>Periode Pantauan:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }} ({{ $days }} Hari Efektif)
                &nbsp;|&nbsp;
                <strong>Kelas:</strong> {{ $selectedClass?->name ?? 'Semua Kelas' }}
            </p>
        </div>

        <!-- Keterangan 7 Pilar Kebiasaan -->
        <div class="habit-legend">
            <div><strong>K1:</strong> Bangun Pagi</div>
            <div><strong>K2:</strong> Beribadah Tepat Waktu</div>
            <div><strong>K3:</strong> Berolahraga Teratur</div>
            <div><strong>K4:</strong> Makan Sehat & Bergizi</div>
            <div><strong>K5:</strong> Gemar Membaca</div>
            <div><strong>K6:</strong> Membantu Orang Tua</div>
            <div><strong>K7:</strong> Istirahat Tepat Waktu</div>
            <div><strong>Target:</strong> {{ count($habits) }} pilar × {{ $days }} hari</div>
        </div>

        <!-- Tabel Data Kebiasaan Siswa -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" rowspan="2">No</th>
                    <th style="width: 65px;" rowspan="2">NIS</th>
                    <th rowspan="2">Nama Lengkap Siswa</th>
                    <th style="width: 55px;" rowspan="2">Kelas</th>
                    <th colspan="{{ count($habits) }}">Frekuensi Pilar Kebiasaan</th>
                    <th style="width: 45px;" rowspan="2">Total</th>
                    <th style="width: 50px;" rowspan="2">% Skor</th>
                    <th style="width: 90px;" rowspan="2">Predikat Karakter</th>
                </tr>
                <tr>
                    @foreach($habits as $h)
                        <th style="width: 28px;" title="{{ $h->name }}">K{{ $h->order_number }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($studentPerformances as $idx => $sp)
                    <tr>
                        <td class="center">{{ $idx + 1 }}</td>
                        <td class="center">{{ $sp['student']->nis }}</td>
                        <td>{{ $sp['student']->name }}</td>
                        <td class="center">{{ $sp['student']->schoolClass?->name ?? '-' }}</td>
                        @foreach($habits as $h)
                            <td class="center">{{ $sp['per_habit'][$h->id] ?? 0 }}</td>
                        @endforeach
                        <td class="center font-bold">{{ $sp['completed'] }}</td>
                        <td class="center font-bold">{{ $sp['percentage'] }}%</td>
                        <td class="center font-semibold" style="font-size: 8.5pt;">
                            @if($sp['badge'] === 'Teladan Unggul')
                                <span style="color: #047857;">Teladan Unggul</span>
                            @elseif($sp['badge'] === 'Konsisten')
                                <span style="color: #0284c7;">Konsisten</span>
                            @elseif($sp['badge'] === 'Berkembang')
                                <span style="color: #d97706;">Berkembang</span>
                            @else
                                <span style="color: #dc2626;">Perlu Bimbingan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 7 + count($habits) }}" class="center" style="padding: 12px;">Tidak ada catatan jurnal karakter pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan Pengesahan -->
        <table class="signatures">
            <tr>
                <td style="width: 50%;">
                    <br>
                    Mengetahui,<br>
                    <strong>Kepala {{ $schoolName }}</strong>
                    <br><br><br><br>
                    <span style="font-weight: bold; text-decoration: underline;">{{ $headmasterName }}</span><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td style="width: 50%; text-align: right;">
                    {{ $schoolCity ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    @if($selectedClass && $selectedClass->homeroomTeacher)
                        Wali Kelas {{ $selectedClass->name }}
                        <br><br><br><br>
                        <span style="font-weight: bold; text-decoration: underline;">{{ $selectedClass->homeroomTeacher->name }}</span><br>
                        NIP. {{ $selectedClass->homeroomTeacher->nip ?? '-' }}
                    @else
                        Koordinator Pembiasaan Karakter / Kesiswaan
                        <br><br><br><br>
                        <span style="font-weight: bold; text-decoration: underline;">{{ Auth::user()->name }}</span><br>
                        NIP/ID. {{ Auth::user()->username }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

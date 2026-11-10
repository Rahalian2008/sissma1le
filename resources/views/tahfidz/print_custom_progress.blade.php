<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kustom Progres Cicilan Tahfidz Juz 30 - {{ $student->name }} (NISN: {{ $student->nisn }}) [{{ strtoupper(request('paper', 'a4')) }}]</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = $kopImageUrl ?? (!empty($school['kop_image']) ? $school['kop_image'] : (!empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image'))))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
        $headmasterName = $headmasterName ?? (\App\Models\SchoolSetting::get('headmaster_name') ?? ($school['headmaster_name'] ?? 'Pian Sopian, S.Pd., M.M.Pd'));
        $headmasterNip = $headmasterNip ?? (\App\Models\SchoolSetting::get('headmaster_nip') ?? ($school['headmaster_nip'] ?? '198009132008011005'));
        $schoolProvince = $schoolProvince ?? ($school['school_province'] ?? 'JAWA BARAT');
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
            margin: {{ $isF4 ? '1.0cm 1.2cm' : '1.0cm 1.2cm' }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: {{ $isF4 ? '9.5pt' : '9pt' }};
            line-height: 1.25;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 18px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            flex-wrap: wrap;
            gap: 10px;
        }
        .paper-toggle-group {
            display: inline-flex;
            align-items: center;
            background: #1e293b;
            border-radius: 6px;
            padding: 2px;
            border: 1px solid #334155;
        }
        .paper-toggle-btn {
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .paper-toggle-btn.active {
            background: #059669;
            color: #ffffff;
        }
        .paper-toggle-btn.inactive {
            color: #94a3b8;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back {
            background: #475569;
            color: #fff;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .doc-container {
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 0 auto;
            padding: 6px 14px;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-header h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-header p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
        }
        .title-block {
            text-align: center;
            margin-bottom: 12px;
        }
        .title-block h2 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .title-block p {
            margin: 2px 0 0 0;
            font-size: 9pt;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9pt;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .summary-box {
            border: 1px solid #333;
            background: #f8fafc;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 2px 6px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 12px;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 3px 4px;
        }
        .data-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .section-header {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
            margin: 10px 0 4px 0;
            border-left: 4px solid #059669;
            padding-left: 6px;
            background: #f1f5f9;
            padding-top: 2px;
            padding-bottom: 2px;
        }
        .explanation-box {
            border: 1.5px solid #059669;
            background: #fcfdfd;
            padding: 8px 10px;
            margin-top: 10px;
            margin-bottom: 14px;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }
        .explanation-box h4 {
            margin: 0 0 4px 0;
            font-size: 9pt;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .signature-grid {
            width: 100%;
            margin-top: 14px;
            page-break-inside: avoid;
            font-size: 8.5pt;
        }
        .signature-grid td {
            vertical-align: top;
            text-align: center;
            width: 33.33%;
        }
        @media print {
            .no-print-bar { display: none !important; }
            .doc-container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div>
            <strong>Laporan Kustom Cicilan {{ $target_grade_label ?? ('Kelas ' . $student_grade) }}:</strong> {{ $student->name }} (Tuntas: {{ $total_completed_ayat }}/{{ $total_target_ayat }} Ayat - {{ $overall_percentage }}%)
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">
                    📄 A4 (210×297mm)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">
                    📜 F4 / Folio (215×330mm)
                </a>
            </div>
            <a href="{{ route('tahfidz.student_custom_progress', ['student' => $student->id, 'grade' => $student_grade]) }}" class="btn-back">← Kembali ke Tampilan Web</a>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen Resmi (Print/PDF)</button>
        </div>
    </div>

    <div class="doc-container">
        <!-- KOP Surat Resmi -->
        @if(!empty($actualKopUrl))
            <div style="text-align: center; margin-bottom: 10px; border-bottom: 3px double #000; padding-bottom: 6px;">
                <img src="{{ $actualKopUrl }}" alt="KOP Surat" style="max-width: 100%; max-height: 110px; object-fit: contain;">
            </div>
        @else
            <div class="kop-header">
                <p style="text-transform: uppercase; font-size: 9pt; margin: 0;">PEMERINTAH PROVINSI {{ $schoolProvince }}<br>DINAS PENDIDIKAN<br>CABANG DINAS PENDIDIKAN WILAYAH V</p>
                <h3>{{ $school['school_name'] ?? 'SMA NEGERI 1 LENGKONG' }}</h3>
                <p>{{ $school['school_address'] ?? 'Jalan Raya Tegalega, Kecamatan Lengkong, Kabupaten Sukabumi, Jawa Barat' }} | Telp: {{ $school['school_phone'] ?? '-' }}</p>
            </div>
        @endif

        <div class="title-block">
            <h2>LAPORAN KUSTOM PROGRES & SETORAN CICILAN HAFALAN {{ strtoupper($target_grade_label ?? ('KELAS ' . $student_grade)) }}</h2>
            <p>Program Pembiasaan & Buku Kontrol Digital Keagamaan Siswa SMAN 1 Lengkong</p>
        </div>

        <!-- Identitas Siswa -->
        <table class="meta-table">
            <tr>
                <td style="width: 16%;"><strong>Nama Siswa</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 42%;"><strong>{{ $student->name }}</strong></td>
                <td style="width: 16%;"><strong>Kelas / Rombel</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 22%;">{{ $student->schoolClass?->name ?? 'Tanpa Kelas' }}</td>
            </tr>
            <tr>
                <td><strong>NISN / NIS</strong></td>
                <td>:</td>
                <td>{{ $student->nisn }} / {{ $student->nis ?? '-' }}</td>
                <td><strong>Wali Kelas</strong></td>
                <td>:</td>
                <td>{{ $student->schoolClass?->homeroomTeacher?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Target Kurikulum</strong></td>
                <td>:</td>
                <td>Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} ({{ $total_surahs }} Surat / {{ $total_target_ayat }} Ayat)</td>
                <td><strong>Tanggal Cetak</strong></td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y') }}</td>
            </tr>
        </table>

        <!-- Summary KPI Angka -->
        <div class="summary-box">
            <table>
                <tr>
                    <td style="width: 25%;">
                        <strong>Total Ayat Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }}:</strong><br>
                        <span class="font-mono font-bold">{{ $total_target_ayat }} Ayat</span>
                    </td>
                    <td style="width: 25%;">
                        <strong>Ayat Selesai (Lulus):</strong><br>
                        <span class="font-mono font-bold" style="color: #059669;">{{ $total_completed_ayat }} Ayat ({{ $overall_percentage }}%)</span>
                    </td>
                    <td style="width: 25%;">
                        <strong>Ayat Belum Selesai:</strong><br>
                        <span class="font-mono font-bold" style="color: #dc2626;">{{ $total_pending_ayat }} Ayat</span>
                    </td>
                    <td style="width: 25%;">
                        <strong>Status Surat:</strong><br>
                        <span>{{ $total_completed_surahs }} Selesai, {{ $total_in_installment_surahs }} Dicicil, {{ $total_unstarted_surahs }} Belum</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 1. Log Riwayat Setoran Cicilan (Angsuran Ayat) -->
        <div class="section-header">1. Riwayat Setoran Cicilan Hafalan (Angsuran Ayat)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 15%;">Tanggal & Waktu</th>
                    <th style="width: 20%;">Surat yang Disetor</th>
                    <th style="width: 18%;">Rentang Ayat Cicilan</th>
                    <th style="width: 8%;">Jumlah</th>
                    <th style="width: 15%;">Penilaian Kualitas</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 12%;">Penguji</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $idx => $r)
                    <tr>
                        <td class="text-center font-bold">{{ $idx + 1 }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($r->deposit_date)->isoFormat('D MMM Y') }}<br>
                            <span style="font-size: 7.5pt; color: #555;">{{ substr($r->deposit_time, 0, 5) }} WIB</span>
                        </td>
                        <td>
                            <strong>{{ $r->surah_name }}</strong><br>
                            <span style="font-size: 7.5pt; color: #555;">Surat No. {{ $r->surah_number }}</span>
                        </td>
                        <td class="font-mono font-bold text-center">
                            {{ $r->ayat_range }}
                        </td>
                        <td class="text-center font-mono font-bold">
                            {{ $r->ayat_count ?? '-' }} Ayat
                        </td>
                        <td style="font-size: 7.5pt;">
                            Makhraj: {{ $r->makhraj }}<br>
                            Tajwid: {{ $r->tajwid }}<br>
                            Kelancaran: {{ $r->kelancaran }}
                        </td>
                        <td class="text-center font-bold">
                            {{ $r->overall_grade }}
                        </td>
                        <td>{{ $r->examiner_name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 10px; color: #777;">
                            Belum ada riwayat setoran cicilan yang tercatat untuk siswa ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- 2. Rincian Progres Cicilan Sesuai Pengelompokan Kelas Siswa -->
        <div class="section-header">2. Analisis Capaian Surat Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} ({{ $total_surahs }} Surat)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 20%;">Nama Surat</th>
                    <th style="width: 9%;">Total Ayat</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 24%;">Ayat yang SUDAH Selesai</th>
                    <th style="width: 24%;">Ayat yang BELUM Selesai</th>
                    <th style="width: 9%;">Progres</th>
                </tr>
            </thead>
            <tbody>
                @foreach($surah_details as $num => $sd)
                    <tr>
                        <td class="text-center font-bold">{{ $num }}</td>
                        <td>
                            <strong>{{ $sd['name'] }}</strong>
                            <span style="font-size: 7.5pt; color: #666;">({{ $sd['meaning'] }})</span>
                        </td>
                        <td class="text-center font-mono">{{ $sd['total_ayah'] }} Ayat</td>
                        <td class="text-center font-bold" style="font-size: 7.5pt;">
                            @if($sd['status'] === 'SELESAI')
                                <span style="color: #059669;">SELESAI</span>
                            @elseif($sd['status'] === 'SEDANG_DICICIL')
                                <span style="color: #d97706;">DICICIL</span>
                            @else
                                <span style="color: #64748b;">BELUM</span>
                            @endif
                        </td>
                        <td style="font-size: 7.5pt;">
                            @if($sd['completed_count'] > 0)
                                <strong class="font-mono">{{ $sd['completed_verses_text'] }}</strong>
                                <span style="color: #555;">({{ $sd['completed_count'] }}/{{ $sd['total_ayah'] }} Ayat)</span>
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                        <td style="font-size: 7.5pt;">
                            @if($sd['pending_count'] > 0)
                                <span class="font-mono font-bold" style="color: #b91c1c;">{{ $sd['pending_verses_text'] }}</span>
                                <span style="color: #555;">(Sisa {{ $sd['pending_count'] }} Ayat)</span>
                            @else
                                <span style="color: #059669; font-weight: bold;">Tuntas 100%</span>
                            @endif
                        </td>
                        <td class="text-center font-mono font-bold">{{ $sd['progress_percent'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- 3. PENJELASAN AKHIR RESMI PROGRES HAFALAN SISWA (SESUAI TARGET KELAS) -->
        <div class="explanation-box">
            <h4 style="text-transform: uppercase; color: #047857; border-bottom: 1px solid #059669; padding-bottom: 3px;">
                3. PENJELASAN & REKAPITULASI AKHIR PROGRES HAFALAN {{ strtoupper($target_grade_label ?? ('KELAS ' . $student_grade)) }}
            </h4>

            <div style="margin-top: 6px;">
                <strong>A. Surat yang SUDAH SELESAI (100% Tuntas):</strong>
                <p style="margin: 2px 0 6px 0; font-size: 8.5pt;">
                    @if(count($completed_surahs_list) > 0)
                        Terdapat <strong>{{ $total_completed_surahs }} surat</strong> target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }} yang telah tuntas seluruh ayatnya:
                        <br>
                        <em>{{ collect($completed_surahs_list)->map(fn($c) => $c['name'] . ' (' . $c['total_ayah'] . ' Ayat)')->implode(', ') }}.</em>
                    @else
                        <em>Belum ada surat yang tuntas 100% pada tingkatan ini.</em>
                    @endif
                </p>

                <strong>B. Surat yang BELUM SELESAI:</strong>
                <div style="margin: 2px 0 6px 0; font-size: 8.5pt; padding-left: 10px;">
                    <!-- 1. Sedang dicicil -->
                    @php
                        $inInstallment = collect($pending_surahs_list)->where('status', 'SEDANG_DICICIL');
                    @endphp
                    <div>
                        <strong>1. Surat yang Sedang Dicicil (Sebagian Ayat Selesai, Sebagian Belum):</strong>
                        @if($inInstallment->isNotEmpty())
                            <ul style="margin: 2px 0 4px 16px; padding: 0;">
                                @foreach($inInstallment as $ins)
                                    <li>
                                        <strong>Surat {{ $ins['name'] }}</strong> (Total {{ $ins['total_ayah'] }} Ayat):
                                        <br>
                                        &bull; Ayat yang <strong>SUDAH</strong> selesai: <strong>{{ $ins['completed_verses_text'] }}</strong> (Jumlah: {{ $ins['completed_count'] }} Ayat).
                                        <br>
                                        &bull; Ayat yang <strong>BELUM</strong> selesai: <strong>{{ $ins['pending_verses_text'] }}</strong> (Jumlah sisa: {{ $ins['pending_count'] }} Ayat).
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p style="margin: 2px 0 4px 0;"><em>Tidak ada surat yang berstatus sedang dicicil sebagian.</em></p>
                        @endif
                    </div>

                    <!-- 2. Belum disetor -->
                    @php
                        $unstarted = collect($pending_surahs_list)->where('status', 'BELUM_DISETOR');
                    @endphp
                    <div style="margin-top: 4px;">
                        <strong>2. Surat yang Belum Pernah Disetor Sama Sekali ({{ $total_unstarted_surahs }} Surat):</strong>
                        @if($unstarted->isNotEmpty())
                            <p style="margin: 2px 0 0 0;">
                                <em>{{ $unstarted->map(fn($u) => $u['name'] . ' (' . $u['total_ayah'] . ' Ayat)')->implode(', ') }}.</em>
                            </p>
                        @else
                            <p style="margin: 2px 0 0 0;"><em>Semua surat target kelas ini sudah pernah disetorkan!</em></p>
                        @endif
                    </div>
                </div>

                <div style="border-top: 1px dashed #94a3b8; padding-top: 5px; margin-top: 6px;">
                    <strong>C. Rekapitulasi Jumlah Ayat Target {{ $target_grade_label ?? ('Kelas ' . $student_grade) }}:</strong>
                    <table style="width: 100%; margin-top: 3px; font-size: 8.5pt;">
                        <tr>
                            <td style="width: 50%;">
                                &bull; Total Ayat yang <strong>SUDAH SELESAI</strong>: <strong class="font-mono">{{ $total_completed_ayat }} Ayat</strong> ({{ $overall_percentage }}%).
                            </td>
                            <td style="width: 50%;">
                                &bull; Total Ayat yang <strong>BELUM SELESAI</strong>: <strong class="font-mono">{{ $total_pending_ayat }} Ayat</strong> ({{ round(100 - $overall_percentage, 1) }}%).
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Tanda Tangan Resmi 3 Pihak (Kepala Sekolah Selalu di Kiri) -->
        <table class="signature-grid">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala SMAN 1 Lengkong</strong>
                    <div style="height: 50px;"></div>
                    <strong><u>{{ $headmasterName }}</u></strong><br>
                    <span>NIP. {{ $headmasterNip }}</span>
                </td>
                <td>
                    Mengetahui,<br>
                    <strong>Wali Kelas</strong>
                    <div style="height: 50px;"></div>
                    <strong><u>{{ $student->schoolClass?->homeroomTeacher?->name ?? '................................................' }}</u></strong><br>
                    <span>NIP. {{ $student->schoolClass?->homeroomTeacher?->nip ?? '-' }}</span>
                </td>
                <td>
                    Lengkong, {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y') }}<br>
                    <strong>Guru Penguji / Koordinator</strong>
                    <div style="height: 50px;"></div>
                    <strong><u>{{ Auth::user()->teacher?->name ?: Auth::user()->name }}</u></strong><br>
                    <span>NIP. {{ Auth::user()->teacher?->nip ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>


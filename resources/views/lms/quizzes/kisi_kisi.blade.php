<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisi-Kisi Soal Asesmen - {{ $quiz->title }} (Permendikdasmen 13/2025)</title>
    @php
        $isF4 = strtolower(request('paper', 'a4')) === 'f4';
        $actualKopPath = !empty($school['school_kop']) ? $school['school_kop'] : (!empty($school['kop_surat_image']) ? $school['kop_surat_image'] : (!empty($school['kop_image']) ? $school['kop_image'] : \App\Models\SchoolSetting::get('school_kop', \App\Models\SchoolSetting::get('kop_surat_image', \App\Models\SchoolSetting::get('kop_image')))));
        $actualKopUrl = $actualKopPath ? (str_starts_with($actualKopPath, 'http') ? $actualKopPath : asset('storage/'.$actualKopPath)) : null;
        $schoolName = $school['school_name'] ?? ($school['name'] ?? \App\Models\SchoolSetting::get('school_name', 'SMA NEGERI 1 LENGKONG'));
        $headmasterName = $school['headmaster_name'] ?? \App\Models\SchoolSetting::get('headmaster_name', 'Pian Sopian, S.Pd., M.M.Pd');
        $headmasterNip = $school['headmaster_nip'] ?? \App\Models\SchoolSetting::get('headmaster_nip', '198009132008011005');
    @endphp
    <style>
        @page {
            size: {{ $isF4 ? '330mm 215mm' : 'A4' }} landscape;
            margin: 1cm 1.2cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            font-size: 9.5pt;
            line-height: 1.35;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
            background: #4f46e5;
            color: #ffffff;
        }
        .paper-toggle-btn.inactive {
            color: #94a3b8;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            padding: 7px 16px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #059669; }
        .btn-back {
            background: #475569;
            color: #fff;
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back:hover { background: #334155; }
        .doc-container {
            max-width: {{ $isF4 ? '330mm' : '297mm' }};
            margin: 20px auto;
            background: #fff;
            padding: 20px 28px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border-radius: 4px;
        }
        .kop-image-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
        }
        .kop-image-wrapper img {
            width: 100%;
            max-height: 110px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-header .provinsi {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .dinas {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .sekolah {
            font-size: 15pt;
            font-weight: bold;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-header .alamat {
            font-size: 8.5pt;
            font-style: italic;
            color: #333;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            margin: 12px 0 16px 0;
        }
        .doc-title h2 {
            font-size: 12.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9pt;
            margin: 3px 0 0 0;
            font-style: italic;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9.5pt;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .info-table .label {
            width: 18%;
            font-weight: bold;
        }
        .info-table .separator {
            width: 2%;
            text-align: center;
        }
        .info-table .value {
            width: 30%;
        }
        .kisi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 8.5pt;
        }
        .kisi-table th, .kisi-table td {
            border: 1px solid #111;
            padding: 6px 7px;
            vertical-align: top;
        }
        .kisi-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .center {
            text-align: center;
        }
        .badge-type {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #64748b;
            background: #f8fafc;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
            page-break-inside: avoid;
            font-size: 9.5pt;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 8px 16px;
        }
        .signature-space {
            height: 65px;
        }
        @media print {
            body {
                background: #fff;
            }
            .no-print-bar {
                display: none !important;
            }
            .doc-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .kisi-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ route('lms.quizzes.show', ['courseClass' => $courseClass->id, 'quiz' => $quiz->id]) }}" class="btn-back">
                ← Kembali ke Detail Asesmen
            </a>
            <span style="font-weight: 600; font-size: 13px;">📋 Dokumen Kisi-Kisi Soal Asesmen (Permendikdasmen No. 13 Tahun 2025)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div class="paper-toggle-group">
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'a4']) }}" class="paper-toggle-btn {{ !$isF4 ? 'active' : 'inactive' }}">A4</a>
                <a href="{{ request()->fullUrlWithQuery(['paper' => 'f4']) }}" class="paper-toggle-btn {{ $isF4 ? 'active' : 'inactive' }}">F4 / Folio</a>
            </div>
            <button onclick="window.print()" class="btn-print">
                🖨️ Cetak / Unduh PDF
            </button>
        </div>
    </div>

    <div class="doc-container">
        <!-- KOP SURAT RESMI -->
        @if(!empty($actualKopUrl))
            <div class="kop-image-wrapper">
                <img src="{{ $actualKopUrl }}" alt="Kop Surat Resmi Sekolah">
            </div>
        @else
            <div class="kop-header">
                <p class="provinsi">PEMERINTAH DAERAH PROVINSI {{ $school['school_province'] ?? ($school['province'] ?? 'JAWA BARAT') }}</p>
                <p class="dinas">DINAS PENDIDIKAN - {{ $school['school_branch'] ?? ($school['branch'] ?? 'CABANG DINAS PENDIDIKAN WILAYAH V') }}</p>
                <h1 class="sekolah">{{ $schoolName }}</h1>
                <p class="alamat">{{ $school['school_address'] ?? ($school['address'] ?? 'Jalan Raya Tegalega, Kec. Lengkong, Kab. Sukabumi') }} | Email: {{ $school['school_email'] ?? ($school['email'] ?? 'sman1lengkong@gmail.com') }}</p>
            </div>
        @endif

        <div class="doc-title">
            <h2>KISI-KISI PENULISAN SOAL ASESMEN PEMBELAJARAN</h2>
            <p>Standar Kurikulum Nasional & AKM - Sesuai Permendikdasmen No. 13 Tahun 2025</p>
        </div>

        <!-- INFORMASI UMUM ASESMEN -->
        <table class="info-table">
            <tr>
                <td class="label">Satuan Pendidikan</td>
                <td class="separator">:</td>
                <td class="value">{{ $schoolName }}</td>
                <td class="label">Alokasi Waktu</td>
                <td class="separator">:</td>
                <td class="value">{{ $quiz->duration_minutes }} Menit</td>
            </tr>
            <tr>
                <td class="label">Mata Pelajaran</td>
                <td class="separator">:</td>
                <td class="value">{{ $subject->name }}</td>
                <td class="label">Bentuk Asesmen</td>
                <td class="separator">:</td>
                <td class="value">{{ $quiz->type_label ?? 'Asesmen CBT Digital' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas / Fase</td>
                <td class="separator">:</td>
                <td class="value">{{ $schoolClass->name }} ({{ $subject->grade ?? 'Fase E / F' }})</td>
                <td class="label">Jumlah Butir Soal</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $quiz->questions->count() }} Butir</strong> (HOTS C3–C5)</td>
            </tr>
            <tr>
                <td class="label">Semester / TP</td>
                <td class="separator">:</td>
                <td class="value">{{ $academicYear->semester ?? 'Ganjil' }} / {{ $academicYear->name ?? '2025/2026' }}</td>
                <td class="label">Guru Pengampu</td>
                <td class="separator">:</td>
                <td class="value">{{ $teacher->name ?? 'Guru Mata Pelajaran' }} (NIP: {{ $teacher->nip ?? '-' }})</td>
            </tr>
        </table>

        <!-- MATRIKS KISI-KISI SOAL -->
        <table class="kisi-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No.</th>
                    <th style="width: 22%;">Capaian Pembelajaran (CP) / Elemen</th>
                    <th style="width: 16%;">Materi Pokok</th>
                    <th style="width: 28%;">Indikator Soal</th>
                    <th style="width: 10%;">Level Kognitif</th>
                    <th style="width: 10%;">Bentuk Soal</th>
                    <th style="width: 5%;">No. Soal</th>
                    <th style="width: 5%;">Skor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quiz->questions as $index => $q)
                    <tr>
                        <td class="center font-bold">{{ $index + 1 }}</td>
                        <td>
                            Peserta didik mampu menganalisis konsep {{ $q->materi_pokok ?? $quiz->title }}, menghubungkan prinsip keilmuan dengan fenomena kontekstual, serta menunjukkan penalaran kritis sesuai standar Capaian Pembelajaran Permendikdasmen No. 13 Tahun 2025.
                        </td>
                        <td>
                            <strong>{{ $q->materi_pokok ?: $quiz->title }}</strong>
                        </td>
                        <td>
                            {{ $q->indikator_soal ?: ('Disajikan stimulus kontekstual mengenai ' . ($q->materi_pokok ?: $quiz->title) . ', peserta didik dapat menganalisis dan menentukan penyelesaian masalah secara tepat.') }}
                        </td>
                        <td class="center">
                            <strong>{{ $q->level_kognitif ?: 'L3 / C4' }}</strong>
                        </td>
                        <td class="center">
                            <span class="badge-type">{{ $q->type_label }}</span>
                        </td>
                        <td class="center font-bold">{{ $q->order_number ?: ($index + 1) }}</td>
                        <td class="center">{{ $q->score_weight }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="center" style="padding: 24px; color: #64748b;">
                            Belum ada butir soal dalam paket asesmen ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="7" style="text-align: right; padding-right: 12px;">Total Bobot Skor:</td>
                    <td class="center">{{ $quiz->questions->sum('score_weight') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- LEMBAR PENGESAHAN TANDA TANGAN -->
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <div class="signature-space"></div>
                    <strong><u>{{ $headmasterName }}</u></strong><br>
                    NIP. {{ $headmasterNip }}
                </td>
                <td>
                    Sukabumi, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    <strong>Guru Mata Pelajaran</strong>
                    <div class="signature-space"></div>
                    <strong><u>{{ $teacher->name ?? 'Guru Pengampu' }}</u></strong><br>
                    NIP. {{ $teacher->nip ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

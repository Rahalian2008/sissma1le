<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Soal Asesmen - {{ $quiz->title }} (Permendikdasmen 13/2025)</title>
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
            size: {{ $isF4 ? '215mm 330mm' : 'A4' }} portrait;
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
            max-width: {{ $isF4 ? '215mm' : '210mm' }};
            margin: 20px auto;
            background: #fff;
            padding: 24px 28px;
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
            max-height: 100px;
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
            font-size: 10.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .dinas {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-header .sekolah {
            font-size: 14pt;
            font-weight: bold;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }
        .kop-header .alamat {
            font-size: 8pt;
            font-style: italic;
            color: #333;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0 16px 0;
        }
        .doc-title h2 {
            font-size: 12.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-title p {
            font-size: 8.5pt;
            margin: 3px 0 0 0;
            font-style: italic;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9pt;
            border: 1px solid #111;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
            border: 1px solid #cbd5e1;
        }
        .info-table .label {
            font-weight: bold;
            background: #f8fafc;
            width: 20%;
        }
        .soal-card {
            border: 1.5px solid #111;
            margin-bottom: 22px;
            page-break-inside: avoid;
            background: #fff;
        }
        .card-top-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            border-bottom: 1px solid #111;
            background: #f8fafc;
        }
        .card-top-table td {
            padding: 4px 8px;
            border: 1px solid #94a3b8;
        }
        .card-body {
            padding: 10px 14px;
            font-size: 9pt;
        }
        .stimulus-box {
            background-color: #f1f5f9;
            border-left: 3px solid #475569;
            padding: 8px 12px;
            margin-bottom: 10px;
            font-style: italic;
            font-size: 8.5pt;
            line-height: 1.35;
        }
        .stimulus-tag {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b;
            font-style: normal;
            display: block;
            margin-bottom: 2px;
        }
        .question-text {
            font-size: 9.5pt;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .options-list {
            margin: 8px 0 12px 14px;
            padding: 0;
            list-style: none;
        }
        .options-list li {
            margin-bottom: 4px;
            font-size: 9pt;
            line-height: 1.3;
        }
        .matching-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px 0;
            font-size: 8.5pt;
        }
        .matching-table th, .matching-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: top;
        }
        .matching-table th {
            background: #f1f5f9;
            font-weight: bold;
        }
        .card-footer {
            border-top: 1px solid #111;
            background: #fbfcfe;
            padding: 8px 14px;
            font-size: 8.5pt;
        }
        .key-badge {
            background: #dbeafe;
            color: #1e40af;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
            border: 1px solid #93c5fd;
        }
        .explanation-box {
            margin-top: 6px;
            color: #334155;
            line-height: 1.35;
            background: #f1f5f9;
            padding: 6px 10px;
            border-radius: 3px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 26px;
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
            .card-top-table, .stimulus-box, .explanation-box {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .soal-card {
                page-break-inside: avoid;
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
            <span style="font-weight: 600; font-size: 13px;">🗂️ Dokumen Kartu Soal Asesmen (Permendikdasmen No. 13 Tahun 2025)</span>
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
            <h2>KARTU SOAL ASESMEN PEMBELAJARAN (PER BUTIR SOAL)</h2>
            <p>Standar Kurikulum Nasional & AKM - Sesuai Permendikdasmen No. 13 Tahun 2025</p>
        </div>

        <!-- INFORMASI UMUM PAKET SOAL -->
        <table class="info-table">
            <tr>
                <td class="label">Satuan Pendidikan</td>
                <td>{{ $schoolName }}</td>
                <td class="label">Mata Pelajaran</td>
                <td><strong>{{ $subject->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Kelas / Fase</td>
                <td>{{ $schoolClass->name }} ({{ $subject->grade ?? 'Fase E / F' }})</td>
                <td class="label">Tahun Ajaran / Sem.</td>
                <td>{{ $academicYear->name ?? '2025/2026' }} / {{ $academicYear->semester ?? 'Ganjil' }}</td>
            </tr>
            <tr>
                <td class="label">Paket Asesmen</td>
                <td>{{ $quiz->title }} ({{ $quiz->type_label }})</td>
                <td class="label">Penyusun / Guru</td>
                <td>{{ $teacher->name ?? 'Guru Mata Pelajaran' }} (NIP: {{ $teacher->nip ?? '-' }})</td>
            </tr>
        </table>

        <!-- LOOP KARTU SOAL PER BUTIR -->
        @forelse($quiz->questions as $index => $q)
            <div class="soal-card">
                <!-- HEADER MATRIKS BUTIR -->
                <table class="card-top-table">
                    <tr>
                        <td style="width: 25%;"><strong>Nomor Soal:</strong> Butir {{ $q->order_number ?: ($index + 1) }}</td>
                        <td style="width: 30%;"><strong>Bentuk Soal:</strong> {{ $q->type_label }}</td>
                        <td style="width: 30%;"><strong>Level Kognitif:</strong> {{ $q->level_kognitif ?: 'L3 / C4 (Penalaran)' }}</td>
                        <td style="width: 15%; text-align: center;"><strong>Bobot:</strong> {{ $q->score_weight }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <strong>Capaian Pembelajaran (CP):</strong>
                            Peserta didik mampu menganalisis konsep {{ $q->materi_pokok ?: $quiz->title }}, memecahkan persoalan kontekstual, serta bernalar kritis sesuai standar Permendikdasmen No. 13 Tahun 2025.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Materi Pokok:</strong> {{ $q->materi_pokok ?: $quiz->title }}
                        </td>
                        <td colspan="2">
                            <strong>Indikator Soal:</strong> {{ $q->indikator_soal ?: ('Disajikan stimulus fenomena kontekstual, peserta didik dapat menentukan solusi masalah terkait ' . ($q->materi_pokok ?: $quiz->title)) }}
                        </td>
                    </tr>
                </table>

                <!-- BODY SOAL -->
                <div class="card-body">
                    @if(!empty($q->stimulus))
                        <div class="stimulus-box">
                            <span class="stimulus-tag">📌 Stimulus / Fenomena Kontekstual:</span>
                            {!! \App\Models\LmsQuizQuestion::renderRichText($q->stimulus) !!}
                        </div>
                    @endif

                    <div class="question-text">
                        <strong>Rumusan Butir Soal:</strong><br>
                        {!! $q->formatted_question_text !!}
                    </div>

                    <!-- OPSIONAL PILIHAN JAWABAN BERDASARKAN TIPE SOAL -->
                    @if($q->question_type === 'multiple_choice' || $q->question_type === 'complex_multiple_choice')
                        @if(is_array($q->options))
                            <ul class="options-list">
                                @foreach($q->options as $key => $opt)
                                    <li>
                                        <strong>{{ $key }}.</strong> {!! $opt !!}
                                        @if($q->question_type === 'complex_multiple_choice')
                                            @php
                                                $correctList = json_decode($q->correct_answer, true);
                                                if(!is_array($correctList)) $correctList = array_map('trim', explode(',', (string)$q->correct_answer));
                                            @endphp
                                            @if(in_array($key, (array)$correctList))
                                                <span style="color: #059669; font-weight: bold; font-size: 8pt;">✓ [Pernyataan Benar]</span>
                                            @endif
                                        @elseif($q->question_type === 'multiple_choice' && strtoupper($q->correct_answer) === strtoupper($key))
                                            <span style="color: #059669; font-weight: bold; font-size: 8pt;">✓ [Kunci Jawaban]</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @elseif($q->question_type === 'true_false')
                        <div style="margin: 8px 0; font-size: 9pt;">
                            <strong>Format Pilihan:</strong>
                            <span style="display: inline-block; margin-left: 12px; padding: 2px 8px; border: 1px solid #cbd5e1; border-radius: 3px;">
                                [A] Benar
                            </span>
                            <span style="display: inline-block; margin-left: 8px; padding: 2px 8px; border: 1px solid #cbd5e1; border-radius: 3px;">
                                [B] Salah
                            </span>
                        </div>
                    @elseif($q->question_type === 'matching')
                        @php
                            $premises = $q->options['premises'] ?? [];
                            $choices = $q->options['choices'] ?? [];
                            $correctPairs = json_decode($q->correct_answer, true) ?: [];
                        @endphp
                        <table class="matching-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Premis / Konsep (Kolom Kiri)</th>
                                    <th style="width: 50%;">Pilihan Pasangan (Kolom Kanan)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($premises as $pKey => $pVal)
                                    <tr>
                                        <td><strong>{{ $pKey }}.</strong> {!! \App\Models\LmsQuizQuestion::renderRichText($pVal) !!}</td>
                                        <td>
                                            @if(isset($choices[$pKey]))
                                                <strong>{{ $pKey }}.</strong> {!! \App\Models\LmsQuizQuestion::renderRichText($choices[$pKey]) !!}
                                            @elseif(isset($choices[chr(64 + (int)$pKey)]))
                                                <strong>{{ chr(64 + (int)$pKey) }}.</strong> {!! \App\Models\LmsQuizQuestion::renderRichText($choices[chr(64 + (int)$pKey)]) !!}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($q->question_type === 'short_answer')
                        <div style="margin: 8px 0; padding: 8px 12px; border: 1px dashed #94a3b8; border-radius: 4px; background: #fafafa; font-size: 8.5pt;">
                            <em>Format Lembar Jawaban Siswa: Uraian / Isian Singkat Konseptual</em>
                        </div>
                    @endif
                </div>

                <!-- KUNCI JAWABAN & PEMBAHASAN -->
                <div class="card-footer">
                    <div>
                        <strong>Kunci Jawaban / Pedoman Penskoran:</strong>
                        @if($q->question_type === 'complex_multiple_choice')
                            @php
                                $cList = json_decode($q->correct_answer, true);
                                if (!is_array($cList)) $cList = explode(',', (string)$q->correct_answer);
                            @endphp
                            <span class="key-badge">Opsi Benar: {{ implode(', ', (array)$cList) }}</span>
                        @elseif($q->question_type === 'matching')
                            <span class="key-badge">Pasangan Tepat:
                                @php
                                    $cp = json_decode($q->correct_answer, true) ?: [];
                                    $pairStrs = [];
                                    foreach($cp as $k => $v) { $pairStrs[] = "{$k} → {$v}"; }
                                    echo implode(' | ', $pairStrs);
                                @endphp
                            </span>
                        @else
                            <span class="key-badge">{!! \App\Models\LmsQuizQuestion::renderRichText($q->correct_answer) !!}</span>
                        @endif
                    </div>
                    @if(!empty($q->pembahasan))
                        <div class="explanation-box">
                            <strong>Pembahasan Ilmiah & Analisis HOTS:</strong><br>
                            {!! \App\Models\LmsQuizQuestion::renderRichText($q->pembahasan) !!}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="padding: 30px; text-align: center; color: #64748b;">
                Belum ada butir soal dalam asesmen ini.
            </div>
        @endforelse

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

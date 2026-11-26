<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Layanan Bimbingan & Konseling - {{ $school->name ?? 'SMAN 1 Lengkong' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #000;
            line-height: 1.3;
            margin: 15px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }

        .summary-cards-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        .summary-cards-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-career { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-personal { background-color: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
        .badge-social { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-study { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        .ttd-section {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .ttd-table {
            width: 100%;
            text-align: center;
            font-size: 9pt;
        }
        .ttd-table td {
            vertical-align: top;
            padding: 5px 10px;
        }
        .ttd-space {
            height: 55px;
        }

        @media print {
            body { margin: 10mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <x-print-toolbar orientation="landscape" default-paper="{{ strtolower($filters['paper_size'] ?? 'f4') }}" title="Rekapitulasi Layanan Bimbingan & Konseling (POP-BK)" />

    <!-- Kop Surat Resmi Sekolah -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen & Metadata Filter -->
    <div class="text-center" style="margin-bottom: 12px;">
        <div class="font-bold uppercase" style="font-size: 12pt; text-decoration: underline; letter-spacing: 0.5px;">
            REKAPITULASI DOKUMENTASI LAYANAN BIMBINGAN & KONSELING
        </div>
        <div style="font-size: 8.5pt; color: #333; margin-top: 2px;">
            Berstandar Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK)
        </div>
        <div style="font-size: 8pt; margin-top: 4px; font-weight: bold;">
            @if(!empty($filters['date_from']) && !empty($filters['date_to']))
                Periode: {{ \Carbon\Carbon::parse($filters['date_from'])->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($filters['date_to'])->translatedFormat('d F Y') }}
            @elseif(!empty($filters['date_from']))
                Mulai Tanggal: {{ \Carbon\Carbon::parse($filters['date_from'])->translatedFormat('d F Y') }}
            @elseif(!empty($filters['date_to']))
                Sampai Tanggal: {{ \Carbon\Carbon::parse($filters['date_to'])->translatedFormat('d F Y') }}
            @else
                Periode: Seluruh Data Rekam Konseling
            @endif

            @if($selectedClass)
                | Kelas: {{ $selectedClass->name }}
            @endif

            @if(!empty($filters['field_of_service']))
                | Bidang: {{ ucfirst(strtolower($filters['field_of_service'])) }}
            @endif

            @if(!empty($filters['career_path']))
                | Arah Karier: {{ $filters['career_path'] === 'KULIAH' ? 'Kuliah' : 'Bekerja' }}
            @endif
        </div>
    </div>

    <!-- Ringkasan Statistik Singkat -->
    <table class="summary-cards-table">
        <tr>
            <td style="width: 20%;">
                <span style="font-size: 7.5pt; color: #555; display: block;">Total Layanan Konseling:</span>
                <strong style="font-size: 11pt; color: #0284c7;">{{ $stats['total'] }} Sesi</strong>
                <span style="font-size: 7pt; color: #666;">({{ $stats['unique_students'] }} Konseli Unik)</span>
            </td>
            <td style="width: 20%;">
                <span style="font-size: 7.5pt; color: #555; display: block;">Bimbingan Karier:</span>
                <strong style="font-size: 11pt; color: #0369a1;">{{ $stats['karier'] }} Sesi</strong>
                <span style="font-size: 7pt; color: #666;">(Kuliah: {{ $stats['kuliah'] }} | Kerja: {{ $stats['bekerja'] }})</span>
            </td>
            <td style="width: 20%;">
                <span style="font-size: 7.5pt; color: #555; display: block;">Bimbingan Belajar:</span>
                <strong style="font-size: 11pt; color: #b45309;">{{ $stats['belajar'] }} Sesi</strong>
            </td>
            <td style="width: 20%;">
                <span style="font-size: 7.5pt; color: #555; display: block;">Bimbingan Pribadi & Sosial:</span>
                <strong style="font-size: 11pt; color: #15803d;">{{ $stats['pribadi'] + $stats['sosial'] }} Sesi</strong>
                <span style="font-size: 7pt; color: #666;">(Pribadi: {{ $stats['pribadi'] }} | Sosial: {{ $stats['sosial'] }})</span>
            </td>
            <td style="width: 20%;">
                <span style="font-size: 7.5pt; color: #555; display: block;">Status Sesi Selesai:</span>
                <strong style="font-size: 11pt; color: #059669;">{{ $stats['selesai'] }} / {{ $stats['total'] }}</strong>
            </td>
        </tr>
    </table>

    <!-- Tabel Rekapitulasi Utama -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 85px;">Kode & Tanggal</th>
                <th style="width: 140px;">Nama Konseli & Kelas</th>
                <th style="width: 90px;">Bidang & Jenis Layanan</th>
                <th style="width: 130px;">Topik / Pokok Bahasan</th>
                <th style="width: 150px;">Peminatan Karier (Jika Karier)</th>
                <th style="width: 150px;">Hasil & Komitmen Siswa</th>
                <th style="width: 140px;">Rencana Tindak Lanjut (RTL)</th>
                <th style="width: 90px;">Konselor BK</th>
                <th style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $idx => $s)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    <strong style="font-family: monospace; font-size: 7.5pt;">{{ $s->session_code }}</strong><br>
                    <span>{{ \Carbon\Carbon::parse($s->date)->translatedFormat('d/m/Y') }}</span>
                    @if($s->start_time)
                        <br><span style="font-size: 7pt; color: #666;">{{ $s->start_time }} WIB</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $s->student?->name }}</strong>
                    <div style="font-size: 7.5pt; color: #444;">
                        Kelas: {{ $s->student?->schoolClass?->name ?: '-' }}<br>
                        NISN: {{ $s->student?->nisn ?: '-' }}
                    </div>
                </td>
                <td>
                    @php
                        $badgeClass = match($s->field_of_service) {
                            'KARIER' => 'badge-career',
                            'BELAJAR' => 'badge-study',
                            'SOSIAL' => 'badge-social',
                            default => 'badge-personal',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst(strtolower($s->field_of_service)) }}</span>
                    <div style="font-size: 7pt; color: #555; margin-top: 2px;">
                        {{ str_replace('_', ' ', $s->session_type) }}
                    </div>
                </td>
                <td>
                    <strong>{{ $s->topic }}</strong>
                    <div style="font-size: 7pt; color: #666; margin-top: 2px;">
                        {{ Str::limit($s->problem_description, 80) }}
                    </div>
                    @if($s->counseling_approach)
                        <div style="font-size: 6.5pt; color: #047857; margin-top: 2px;">
                            <em>Teknik: {{ $s->counseling_approach }}</em>
                        </div>
                    @endif
                </td>
                <td>
                    @if($s->field_of_service === 'KARIER' && $s->career_path)
                        @if($s->career_path === 'KULIAH')
                            <strong style="color: #0369a1;">🏛️ Kuliah:</strong> {{ $s->study_admission_track ?: 'Jalur Mandiri/Umum' }}<br>
                            @if(!empty($s->campus_choices) && is_array($s->campus_choices))
                                <div style="font-size: 6.8pt; line-height: 1.25; margin-top: 2px;">
                                @foreach($s->campus_choices as $cIdx => $c)
                                    <div><strong>P{{ $c['order'] ?? ($cIdx + 1) }}:</strong> {{ $c['campus'] ?: '-' }} (<em>{{ $c['major'] ?: '-' }}</em>)</div>
                                @endforeach
                                </div>
                            @else
                                @if($s->campus_name)
                                    <span>PTN/PTS: {{ $s->campus_name }}</span><br>
                                @endif
                                @if($s->study_program)
                                    <span style="font-style: italic;">Prodi: {{ $s->study_program }}</span>
                                @endif
                            @endif
                        @else
                            <strong style="color: #15803d;">💼 Bekerja / Wirausaha:</strong><br>
                            <span>{{ $s->work_field ?: 'Penelusuran lowongan / wirausaha mandiri' }}</span>
                        @endif
                    @else
                        <span style="color: #999;">-</span>
                    @endif
                </td>
                <td>
                    {{ $s->agreement_result ? Str::limit($s->agreement_result, 120) : '-' }}
                </td>
                <td>
                    @if($s->follow_up_date)
                        <div style="font-weight: bold; color: #92400e; font-size: 7.5pt;">
                            📅 RTL: {{ \Carbon\Carbon::parse($s->follow_up_date)->translatedFormat('d M Y') }}
                        </div>
                    @endif
                    <div>{{ $s->follow_up_plan ? Str::limit($s->follow_up_plan, 100) : '-' }}</div>
                </td>
                <td>
                    {{ $s->counselor?->name ?: 'Guru BK' }}
                </td>
                <td class="text-center">
                    <span style="font-size: 7pt; font-weight: bold; {{ $s->status === 'SELESAI' ? 'color: #15803d;' : 'color: #b45309;' }}">
                        {{ $s->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px; font-style: italic; color: #777;">
                    Tidak ada catatan sesi konseling yang cocok dengan filter kustom yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi Sesuai Panduan POP-BK -->
    <div class="ttd-section">
        <table class="ttd-table">
            <tr>
                <td style="width: 33%;">
                    Mengetahui,<br>
                    Kepala SMAN 1 Lengkong
                    <div class="ttd-space"></div>
                    <strong><u>{{ $headmaster['name'] ?? 'Pian Sopian, S.Pd., MM.Pd.' }}</u></strong><br>
                    NIP. {{ $headmaster['nip'] ?? '198009132008011005' }}
                </td>
                <td style="width: 34%;">
                    Disetujui Oleh,<br>
                    Koordinator Bimbingan & Konseling
                    <div class="ttd-space"></div>
                    <strong><u>{{ $bkCoordinator['name'] ?? 'Ida Marttin Maolidah SE, SP.,M.Pd.' }}</u></strong><br>
                    NIP. {{ $bkCoordinator['nip'] ?? '197803102008012006' }}
                </td>
                <td style="width: 33%;">
                    Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Guru Bimbingan & Konseling
                    <div class="ttd-space"></div>
                    <strong><u>{{ Auth::user()->name }}</u></strong><br>
                    NIP. {{ Auth::user()->nip ?? (Auth::user()->username ?? '-') }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Konseling - {{ $session->session_code }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5pt;
            line-height: 1.45;
            color: #000;
            margin: 20mm 20mm 15mm 20mm;
        }
        .kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .kop h2 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop h3 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 10pt; }
        
        .title {
            text-align: center;
            margin-bottom: 16px;
        }
        .title h4 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title p {
            margin: 3px 0 0 0;
            font-size: 10.5pt;
            font-family: monospace, 'Courier New', Courier;
            font-weight: bold;
        }

        .narrative {
            text-align: justify;
            margin-bottom: 12px;
            text-indent: 30px;
        }

        table.meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.meta td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 11pt;
        }

        .section-box {
            border: 1px solid #111;
            padding: 8px 10px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .section-box h5 {
            margin: 0 0 5px 0;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #777;
            padding-bottom: 3px;
        }
        .section-box p {
            margin: 0;
            font-size: 11pt;
            text-align: justify;
        }

        table.choice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 10.5pt;
        }
        table.choice-table th, table.choice-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            vertical-align: middle;
        }
        table.choice-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5pt;
        }

        .confidential-note {
            font-size: 9.5pt;
            font-style: italic;
            margin-top: 8px;
            padding: 4px 8px;
            border-left: 3px solid #333;
            background-color: #fafafa;
        }

        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            font-size: 11pt;
        }
        .ttd-space {
            height: 60px;
        }

        @media print {
            body { margin: 12mm 15mm 12mm 15mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 18px; text-align: right;">
        <button onclick="window.print()" style="padding: 9px 18px; background: #059669; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 13px; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
            🖨️ Cetak Dokumen (Print / Save as PDF)
        </button>
    </div>

    <!-- Kop Surat Resmi dari Master Data Logo & Kop Surat -->
    <x-print-kop :school="$school" />

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>BERITA ACARA LAYANAN BIMBINGAN &amp; KONSELING</h4>
        <p>Nomor Registrasi: {{ $session->session_code }}</p>
    </div>

    <!-- Paragraf Pengantar Resmi Berstandar POP-BK -->
    <p class="narrative">
        Pada hari ini, <strong>{{ \Carbon\Carbon::parse($session->date)->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}</strong> bertempat di <strong>{{ $session->place ?: 'Ruang Bimbingan Konseling' }}</strong>@if($session->start_time) pukul <strong>{{ $session->start_time }} @if($session->end_time) s.d. {{ $session->end_time }} @endif WIB</strong>@endif, telah diselenggarakan layanan Bimbingan dan Konseling sesuai dengan Panduan Operasional Penyelenggaraan Bimbingan dan Konseling (POP-BK) serta Kode Etik Asosiasi Bimbingan dan Konseling Indonesia (ABKIN), bagi peserta didik di bawah ini:
    </p>

    <!-- Data Siswa & Administrasi Layanan -->
    <table class="meta">
        <tr>
            <td style="width: 32%;">Nama Lengkap Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 66%; font-weight: bold;">{{ $session->student?->name }}</td>
        </tr>
        <tr>
            <td>Nomor Induk Siswa Nasional (NISN)</td>
            <td>:</td>
            <td>{{ $session->student?->nisn ?: '-' }} @if($session->student?->nis)/ NIS: {{ $session->student?->nis }}@endif</td>
        </tr>
        <tr>
            <td>Kelas / Rombongan Belajar</td>
            <td>:</td>
            <td>{{ $session->student?->schoolClass?->name ?: '-' }}</td>
        </tr>
        <tr>
            <td>Bidang Layanan BK</td>
            <td>:</td>
            <td><strong>Bimbingan {{ ucfirst(strtolower($session->field_of_service)) }}</strong></td>
        </tr>
        <tr>
            <td>Format / Jenis Layanan</td>
            <td>:</td>
            <td>{{ str_replace('_', ' ', $session->session_type) }}</td>
        </tr>
        <tr>
            <td>Komponen Program (POP-BK)</td>
            <td>:</td>
            <td>{{ str_replace('_', ' ', $session->service_category) }}</td>
        </tr>
        <tr>
            <td>Guru BK / Konselor Pelaksana</td>
            <td>:</td>
            <td>{{ $session->counselor?->name ?: 'Konselor BK' }} @if($session->counselor?->nip)(NIP. {{ $session->counselor->nip }})@endif</td>
        </tr>
        <tr>
            <td>Topik / Masalah Bimbingan</td>
            <td>:</td>
            <td><strong>{{ $session->topic }}</strong></td>
        </tr>
        <tr>
            <td>Status Sesi Layanan</td>
            <td>:</td>
            <td><strong>{{ $session->status }}</strong></td>
        </tr>
    </table>

    <!-- Khusus Bidang Bimbingan Karier: Tabel Pilihan Multi-Kampus / Pekerjaan -->
    @if($session->field_of_service === 'KARIER' && $session->career_path)
    <div class="section-box">
        <h5>Hasil Peminatan Bimbingan Karier &amp; Arah Masa Depan Siswa</h5>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 11pt;">
            <tr>
                <td style="width: 30%; vertical-align: top;">Rencana Arah Pasca Lulus</td>
                <td style="width: 2%; vertical-align: top;">:</td>
                <td style="width: 68%; font-weight: bold; vertical-align: top;">
                    {{ $session->career_path === 'KULIAH' ? 'Melanjutkan Pendidikan ke Perguruan Tinggi (Kuliah)' : 'Memasuki Dunia Kerja / Wirausaha Mandiri' }}
                </td>
            </tr>
            @if($session->career_path === 'KULIAH')
            <tr>
                <td style="vertical-align: top;">Jalur Masuk Kuliah Target</td>
                <td style="vertical-align: top;">:</td>
                <td style="font-weight: bold; vertical-align: top;">
                    {{ $session->study_admission_track ?: 'Reguler / Mandiri' }}
                </td>
            </tr>
            @elseif($session->career_path === 'BEKERJA')
            <tr>
                <td style="vertical-align: top;">Bidang Kerja / Minat Usaha</td>
                <td style="vertical-align: top;">:</td>
                <td style="font-weight: bold; vertical-align: top;">
                    {{ $session->work_field ?: 'Penelusuran lowongan / wirausaha mandiri' }}
                </td>
            </tr>
            @endif
        </table>

        @if($session->career_path === 'KULIAH')
        <div style="font-size: 10.5pt; font-weight: bold; margin-top: 4px; margin-bottom: 2px;">
            Daftar Prioritas Pilihan Perguruan Tinggi &amp; Program Studi:
        </div>
        <table class="choice-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Prioritas</th>
                    <th style="width: 47%;">Perguruan Tinggi Target (PTN / PTS / Politeknik)</th>
                    <th style="width: 35%;">Program Studi / Jurusan</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($session->campus_choices) && is_array($session->campus_choices))
                    @foreach($session->campus_choices as $idx => $choice)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">
                            Pilihan {{ $choice['order'] ?? ($idx + 1) }}
                            <div style="font-size: 8pt; font-weight: normal; color: #444;">
                                {{ $idx === 0 ? '(Prioritas Utama)' : '(Alternatif Cadangan)' }}
                            </div>
                        </td>
                        <td><strong>{{ $choice['campus'] ?: '-' }}</strong></td>
                        <td>{{ $choice['major'] ?: '-' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="text-align: center; font-weight: bold;">Pilihan 1 (Prioritas Utama)</td>
                        <td><strong>{{ $session->campus_name ?: '-' }}</strong></td>
                        <td>{{ $session->study_program ?: '-' }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @endif
    </div>
    @endif

    <!-- Uraian Konseling Berstandar POP-BK -->
    <div class="section-box">
        <h5>1. Deskripsi Gejala, Latar Belakang Masalah &amp; Kebutuhan Konseli</h5>
        <p>{{ $session->problem_description }}</p>
    </div>

    @if($session->counseling_approach)
    <div class="section-box">
        <h5>2. Pendekatan &amp; Teknik Konseling yang Diterapkan (Standar POP-BK)</h5>
        <p><strong>{{ $session->counseling_approach }}</strong></p>
    </div>
    @endif

    <div class="section-box">
        <h5>3. Kesepakatan &amp; Komitmen Tindakan Siswa (Konseli)</h5>
        <p>{{ $session->agreement_result ?: 'Siswa menyatakan komitmen untuk memperbaiki sikap, mematuhi tata tertib, dan melaksanakan jadwal bimbingan secara berkala.' }}</p>
    </div>

    @if($session->follow_up_plan || $session->follow_up_date)
    <div class="section-box">
        <h5>4. Rencana Tindak Lanjut (RTL) &amp; Pemantauan Perkembangan</h5>
        @if($session->follow_up_date)
            <div style="font-weight: bold; margin-bottom: 4px; font-size: 10.5pt;">
                📅 Jadwal Pertemuan Lanjutan: {{ \Carbon\Carbon::parse($session->follow_up_date)->translatedFormat('l, d F Y') }}
                <span style="font-size: 9.5pt; font-weight: normal; color: #333;">
                    (Status RTL: {{ $session->follow_up_status === 'SELESAI' ? 'Selesai Ditindaklanjuti' : ($session->follow_up_status === 'DIBATALKAN' ? 'Dibatalkan' : 'Menunggu Pertemuan Lanjutan / Dalam Pemantauan') }})
                </span>
            </div>
        @endif
        @if($session->follow_up_plan)
            <p>{{ $session->follow_up_plan }}</p>
        @endif
    </div>
    @endif

    <!-- Catatan Asas Kerahasiaan ABKIN -->
    <div class="confidential-note">
        * Dokumen Berita Acara ini bersifat <strong>RAHASIA (Confidential)</strong> dan dilindungi oleh Undang-Undang serta Kode Etik Profesi Bimbingan dan Konseling Indonesia (ABKIN). Informasi yang tercantum hanya dipergunakan untuk kepentingan layanan pembinaan peserta didik secara profesional.
    </div>

    <!-- Tanda Tangan Resmi Sesuai Format Sekolah & POP-BK -->
    <table class="signature-table">
        <tr>
            <td style="width: 48%;">
                Peserta Didik (Konseli),
                <div class="ttd-space"></div>
                <strong><u>{{ $session->student?->name }}</u></strong><br>
                NISN. {{ $session->student?->nisn ?: '-' }}
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%;">
                Lengkong, {{ \Carbon\Carbon::parse($session->date)->translatedFormat('d F Y') }}<br>
                Guru BK / Konselor Sekolah,
                <div class="ttd-space"></div>
                <strong><u>{{ $session->counselor?->name ?: 'Konselor BK' }}</u></strong><br>
                NIP. {{ $session->counselor?->nip ?: '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 20px;">
                Mengetahui,<br>
                Kepala SMAN 1 Lengkong
                <div class="ttd-space"></div>
                <strong><u>{{ $headmaster['name'] ?? 'Pian Sopian, S.Pd., MM.Pd.' }}</u></strong><br>
                NIP. {{ $headmaster['nip'] ?? '198009132008011005' }}
            </td>
        </tr>
    </table>
</body>
</html>

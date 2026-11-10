<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Kebutuhan Peserta Didik (AKPD) - {{ $className }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #111; line-height: 1.3; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
        .kop h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .kop h3 { margin: 2px 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .kop p { margin: 2px 0; font-size: 9pt; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 4px; font-size: 12pt; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 10pt; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        th, td { border: 1px solid #000; padding: 5px 7px; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .ttd-container { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .ttd-box { width: 220px; text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <x-print-kop :school="$school" />

    <div class="title" style="margin-top: 14px;">PROFIL KEBUTUHAN PESERTA DIDIK (HASIL AKPD)</div>
    <div class="subtitle">Sasaran: {{ $className }} | Total Responden: {{ $totalRespondents }} Peserta Didik</div>

    <h4 style="margin-bottom: 5px; font-size: 10pt;">1. Rekapitulasi Rata-rata Kebutuhan Per Bidang Layanan BK</h4>
    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="35%">Bidang Layanan BK</th>
                <th width="25%">Rata-rata Kebutuhan (%)</th>
                <th width="32%">Kategori Prioritas</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($fieldAverages as $field => $avg)
                @php
                    $prio = $avg >= 50 ? 'PRIORITAS TINGGI' : ($avg >= 25 ? 'PRIORITAS SEDANG' : 'PRIORITAS RENDAH');
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td><strong>Bidang Layanan {{ $field }}</strong></td>
                    <td class="text-center font-bold">{{ $avg }}%</td>
                    <td class="text-center">{{ $prio }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4 style="margin-top: 20px; margin-bottom: 5px; font-size: 10pt;">2. Top 10 Butir Masalah/Kebutuhan Paling Banyak Dialami Siswa</h4>
    <table>
        <thead>
            <tr>
                <th width="6%">No</th>
                <th width="10%">Kode</th>
                <th width="15%">Bidang</th>
                <th width="45%">Pernyataan Butir Kebutuhan</th>
                <th width="12%">Frekuensi</th>
                <th width="12%">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topQuestions as $index => $q)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center"><strong>{{ $q->code }}</strong></td>
                    <td>{{ $q->field_of_service }}</td>
                    <td>{{ $q->statement }}</td>
                    <td class="text-center">{{ $q->chosen_count }} Siswa</td>
                    <td class="text-center font-bold">{{ $q->percentage }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada respon yang diinputkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 9.5pt;">
        <strong>Kesimpulan Tindak Lanjut Program BK:</strong>
        <p style="margin: 3px 0;">Berdasarkan hasil analisis AKPD di atas, layanan bimbingan klasikal dan bimbingan kelompok diutamakan pada butir-butir dengan persentase tertinggi, dan dimasukkan ke dalam Rencana Pelaksanaan Layanan (RPL) BK Semester Aktif.</p>
    </div>

    <div class="ttd-container" style="display: flex; justify-content: space-between; margin-top: 25px;">
        <div class="ttd-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <br><br><br>
            <p><strong><u>{{ $school->headmaster_name ?? 'Drs. H. Pimpinan, M.Pd.' }}</u></strong><br>
            NIP. {{ $school->headmaster_nip ?? '19700101 199501 1 001' }}</p>
        </div>
        <div class="ttd-box">
            <p>Lengkong, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Guru Bimbingan Konseling,</p>
            <br><br><br>
            <p><strong><u>{{ Auth::user()?->name ?? 'Guru BK' }}</u></strong><br>
            NIP. {{ Auth::user()?->nip ?? '-' }}</p>
        </div>
    </div>
</body>
</html>


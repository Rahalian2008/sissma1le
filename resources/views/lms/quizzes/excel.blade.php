<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        .header-title { font-size: 14pt; font-weight: bold; text-align: center; }
        .header-sub { font-size: 11pt; text-align: center; color: #333; }
        .meta-label { font-weight: bold; }
        table.excel-table { border-collapse: collapse; width: 100%; }
        table.excel-table th { background-color: #6366f1; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: center; padding: 6px; }
        table.excel-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="7" class="header-title">{{ strtoupper($school['schoolName'] ?? 'SMA NEGERI 1 LENGKONG') }}</td>
        </tr>
        <tr>
            <td colspan="7" class="header-sub">REKAPITULASI HASIL ASESMEN &amp; CBT SISWA</td>
        </tr>
        <tr>
            <td colspan="7" class="header-sub">Sistem Informasi &amp; Pembelajaran Digital (LMS)</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td class="meta-label">Mata Pelajaran:</td>
            <td colspan="2">{{ $courseClass->subject->name }}</td>
            <td class="meta-label">Standar KKTP:</td>
            <td colspan="3" class="font-bold">{{ $quiz->passing_score }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kelas / Rombel:</td>
            <td colspan="2">{{ $courseClass->schoolClass->name }}</td>
            <td class="meta-label">Durasi Ujian:</td>
            <td colspan="3">{{ $quiz->duration_minutes }} Menit</td>
        </tr>
        <tr>
            <td class="meta-label">Judul Asesmen:</td>
            <td colspan="2" class="font-bold">{{ $quiz->title }}</td>
            <td class="meta-label">Guru Pengampu:</td>
            <td colspan="3">{{ $courseClass->teacher->name }}</td>
        </tr>
        @if($quiz->topic)
        <tr>
            <td class="meta-label">Topik / Materi:</td>
            <td colspan="6">{{ $quiz->topic }}</td>
        </tr>
        @endif
        <tr><td colspan="7"></td></tr>
    </table>

    <table class="excel-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 120px;">NISN</th>
                <th style="width: 250px;">Nama Lengkap Siswa</th>
                <th style="width: 150px;">Waktu Mulai</th>
                <th style="width: 150px;">Waktu Selesai</th>
                <th style="width: 100px;">Skor Akhir</th>
                <th style="width: 140px;">Status KKTP</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($students as $st)
                @php
                    $attempt = $attemptsMap->get($st->id);
                    $score = $attempt ? $attempt->score : null;
                    $isPassed = $score !== null ? ($score >= $quiz->passing_score) : null;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center" style="mso-number-format:'\@';">{{ $st->nisn }}</td>
                    <td class="font-bold">{{ $st->name }}</td>
                    <td class="text-center">
                        {{ $attempt && $attempt->started_at ? $attempt->started_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        @if($attempt && $attempt->completed_at)
                            {{ $attempt->completed_at->format('d/m/Y H:i') }}
                        @elseif($attempt)
                            Sedang Ujian
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center font-bold">
                        {{ $score !== null ? $score : '-' }}
                    </td>
                    <td class="text-center font-bold">
                        @if($score !== null)
                            {{ $isPassed ? 'TUNTAS' : 'REMEDIAL' }}
                        @else
                            Belum Ujian
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="3" style="text-align: left;">
                Mengetahui,<br/>
                <b>Kepala Sekolah</b><br/><br/><br/><br/>
                <b><u>{{ $school['headmaster']['name'] ?? 'Pian Sopian, S.Pd., M.M.Pd' }}</u></b><br/>
                NIP. {{ $school['headmaster']['nip'] ?? '-' }}
            </td>
            <td></td>
            <td colspan="3" style="text-align: right;">
                {{ $school['schoolCity'] ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br/>
                <b>Guru Mata Pelajaran</b><br/><br/><br/><br/>
                <b><u>{{ $courseClass->teacher->name }}</u></b><br/>
                NIP. {{ $courseClass->teacher->nip ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>

<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @php
        $wm = $courseClass->getWeightMaterial();
        $wa = $courseClass->getWeightAssignment();
        $wq = $courseClass->getWeightQuiz();
        $wt = $courseClass->getWeightAttendance();
        $kkm = $courseClass->subject->kkm ?? 75;
    @endphp
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        .header-title { font-size: 14pt; font-weight: bold; text-align: center; }
        .header-sub { font-size: 11pt; text-align: center; color: #333; }
        .meta-label { font-weight: bold; }
        table.excel-table { border-collapse: collapse; width: 100%; }
        table.excel-table th { background-color: #1e1b4b; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: center; padding: 6px; }
        table.excel-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="10" class="header-title">{{ strtoupper($school['schoolName'] ?? 'SMA NEGERI 1 LENGKONG') }}</td>
        </tr>
        <tr>
            <td colspan="10" class="header-sub">BUKU REKAPITULASI NILAI AKHIR &amp; PEMANTAUAN KETUNTASAN SISWA (GRADEBOOK)</td>
        </tr>
        <tr>
            <td colspan="10" class="header-sub">Sistem Informasi &amp; Pembelajaran Digital (LMS)</td>
        </tr>
        <tr><td colspan="10"></td></tr>
        <tr>
            <td class="meta-label">Mata Pelajaran:</td>
            <td colspan="3">{{ $courseClass->subject->name }}</td>
            <td class="meta-label">Standar KKTP:</td>
            <td colspan="5" class="font-bold">{{ $kkm }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kelas / Rombel:</td>
            <td colspan="3">{{ $courseClass->schoolClass->name }}</td>
            <td class="meta-label">Guru Pengampu:</td>
            <td colspan="5">{{ $courseClass->teacher->name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Bobot Komulatif:</td>
            <td colspan="9">Materi ({{ $wm }}%), Tugas ({{ $wa }}%), CBT ({{ $wq }}%), Presensi ({{ $wt }}%)</td>
        </tr>
        <tr><td colspan="10"></td></tr>
    </table>

    <table class="excel-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 120px;">NISN</th>
                <th style="width: 250px;">Nama Lengkap Siswa</th>
                <th style="width: 110px;">Rerata Materi ({{ $wm }}%)</th>
                <th style="width: 110px;">Rerata Tugas ({{ $wa }}%)</th>
                <th style="width: 110px;">Rerata CBT ({{ $wq }}%)</th>
                <th style="width: 110px;">Rerata Presensi ({{ $wt }}%)</th>
                <th style="width: 100px;">Nilai Akhir</th>
                <th style="width: 80px;">Predikat</th>
                <th style="width: 130px;">Status KKTP</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($students as $st)
                @php $grade = $gradebook[$st->id] ?? null; @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center" style="mso-number-format:'\@';">{{ $st->nisn }}</td>
                    <td class="font-bold">{{ $st->name }}</td>
                    <td class="text-center">{{ $grade['material_avg'] ?? '-' }}</td>
                    <td class="text-center">{{ $grade['assignment_avg'] ?? '-' }}</td>
                    <td class="text-center">{{ $grade['quiz_avg'] ?? '-' }}</td>
                    <td class="text-center">{{ $grade['attendance_avg'] ?? '-' }}</td>
                    <td class="text-center font-bold" style="color: #4338ca;">{{ $grade['final_score'] ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $grade['predicate'] ?? '-' }}</td>
                    <td class="text-center font-bold">
                        @if(isset($grade['is_mastered']) && $grade['is_mastered'] !== null)
                            {{ $grade['is_mastered'] ? 'TUNTAS' : 'REMEDIAL' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr><td colspan="10"></td></tr>
        <tr>
            <td colspan="4" style="text-align: left;">
                Mengetahui,<br/>
                <b>Kepala Sekolah</b><br/><br/><br/><br/>
                <b><u>{{ $school['headmaster']['name'] ?? 'Pian Sopian, S.Pd., M.M.Pd' }}</u></b><br/>
                NIP. {{ $school['headmaster']['nip'] ?? '-' }}
            </td>
            <td colspan="2"></td>
            <td colspan="4" style="text-align: right;">
                {{ $school['schoolCity'] ?? 'Sukabumi' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br/>
                <b>Guru Mata Pelajaran</b><br/><br/><br/><br/>
                <b><u>{{ $courseClass->teacher->name }}</u></b><br/>
                NIP. {{ $courseClass->teacher->nip ?: '-' }}
            </td>
        </tr>
    </table>
</body>
</html>

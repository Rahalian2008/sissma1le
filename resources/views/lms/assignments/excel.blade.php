<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        .header-title { font-size: 14pt; font-weight: bold; text-align: center; }
        .header-sub { font-size: 11pt; text-align: center; color: #333; }
        .meta-label { font-weight: bold; }
        table.excel-table { border-collapse: collapse; width: 100%; }
        table.excel-table th { background-color: #2563eb; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: center; padding: 6px; }
        table.excel-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .status-graded { color: #16a34a; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .status-unsubmitted { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="7" class="header-title">{{ strtoupper($school['schoolName'] ?? 'SMA NEGERI 1 LENGKONG') }}</td>
        </tr>
        <tr>
            <td colspan="7" class="header-sub">REKAPITULASI HASIL PENUGASAN &amp; PR SISWA</td>
        </tr>
        <tr>
            <td colspan="7" class="header-sub">Sistem Informasi &amp; Pembelajaran Digital (LMS)</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td class="meta-label">Mata Pelajaran:</td>
            <td colspan="2">{{ $courseClass->subject->name }}</td>
            <td class="meta-label">Batas Waktu:</td>
            <td colspan="3">{{ $assignment->due_date ? $assignment->due_date->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kelas / Rombel:</td>
            <td colspan="2">{{ $courseClass->schoolClass->name }}</td>
            <td class="meta-label">Maks Skor:</td>
            <td colspan="3">{{ $assignment->max_score }} Poin</td>
        </tr>
        <tr>
            <td class="meta-label">Judul Tugas:</td>
            <td colspan="2" class="font-bold">{{ $assignment->title }}</td>
            <td class="meta-label">Guru Pengampu:</td>
            <td colspan="3">{{ $courseClass->teacher->name }}</td>
        </tr>
        @if($assignment->topic)
        <tr>
            <td class="meta-label">Topik / Materi:</td>
            <td colspan="6">{{ $assignment->topic }}</td>
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
                <th style="width: 160px;">Waktu Pengumpulan</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 80px;">Skor (Maks {{ $assignment->max_score }})</th>
                <th style="width: 250px;">Catatan / Feedback Guru</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($students as $st)
                @php $sub = $submissionsMap->get($st->id); @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center" style="mso-number-format:'\@';">{{ $st->nisn }}</td>
                    <td class="font-bold">{{ $st->name }}</td>
                    <td class="text-center">
                        @if($sub && $sub->submitted_at)
                            {{ $sub->submitted_at->format('d/m/Y H:i') }}
                            @if($sub->is_late) (Terlambat) @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($sub)
                            {{ $sub->status === 'graded' ? 'Dinilai' : 'Menunggu' }}
                        @else
                            Belum Kumpul
                        @endif
                    </td>
                    <td class="text-center font-bold">
                        {{ ($sub && $sub->score !== null) ? $sub->score : '-' }}
                    </td>
                    <td>{{ $sub?->feedback ?: '-' }}</td>
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

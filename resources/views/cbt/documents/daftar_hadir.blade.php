<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Hadir Ujian - {{ $exam->title }} - {{ $school['name'] }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
            .no-print {
                display: none !important;
            }
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="p-6 max-w-4xl mx-auto">

    <!-- FLOATING ACTION BUTTONS (NO-PRINT) -->
    <div class="no-print fixed top-4 right-4 z-50 flex items-center gap-2 bg-slate-900/90 text-white p-2.5 rounded-2xl shadow-xl backdrop-blur-sm">
        <a href="{{ route('cbt.exams.show', $exam->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-sans font-bold transition">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-sans font-bold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Daftar Hadir
        </button>
    </div>

    <!-- FILTER PER KELAS (NO-PRINT) -->
    <div class="no-print mb-6 p-4 rounded-2xl bg-slate-100 border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs font-sans">
        <div>
            <strong>Pilih Rombel / Kelas:</strong> Sesuaikan daftar hadir per kelas.
        </div>
        <form method="GET" action="{{ route('cbt.documents.daftar_hadir', $exam->id) }}" class="flex items-center gap-2">
            <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-300 py-1.5 px-3">
                <option value="">-- Semua Kelas Terdaftar --</option>
                @foreach($exam->classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- KOP RESMI SEKOLAH -->
    <div class="border-b-4 border-double border-black pb-3 mb-4">
        <div class="flex items-center justify-between gap-4">
            <div class="w-20 h-20 flex items-center justify-center flex-shrink-0">
                @if(!empty($school['logo_url']))
                    <img src="{{ $school['logo_url'] }}" alt="Logo" class="max-h-20 max-w-20 object-contain">
                @else
                    <div class="w-16 h-16 border-2 border-black rounded-full flex items-center justify-center font-bold text-xs text-center font-sans">
                        LOGO
                    </div>
                @endif
            </div>
            <div class="flex-1 text-center font-serif leading-tight">
                <div class="text-xs uppercase tracking-wider">Pemerintah Daerah Provinsi Jawa Barat</div>
                <div class="text-xs uppercase tracking-wider">Dinas Pendidikan - Cabang Dinas Wilayah V</div>
                <div class="text-xl font-bold uppercase tracking-wide mt-0.5">{{ $school['name'] }}</div>
                <div class="text-[11px] mt-0.5">{{ $school['address'] }} • NPSN: {{ $school['npsn'] }}</div>
                <div class="text-[10px] italic text-slate-700">Surel: sman1lengkong@gmail.com</div>
            </div>
            <div class="w-20 flex-shrink-0"></div>
        </div>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="text-center mb-4">
        <h2 class="text-base font-bold uppercase underline tracking-wider">DAFTAR HADIR PESERTA ASESMEN UJIAN ONLINE (CBT)</h2>
        <div class="text-xs uppercase font-bold mt-0.5 tracking-wider">{{ $exam->title }}</div>
    </div>

    <!-- IDENTITAS UJIAN & RUANG -->
    <div class="grid grid-cols-2 gap-x-8 text-xs mb-4">
        <table class="w-full">
            <tr>
                <td class="w-32 py-0.5 font-bold">Mata Pelajaran</td>
                <td class="w-3">:</td>
                <td>{{ $exam->subject?->name ?? 'Umum' }}</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Kelas / Rombel</td>
                <td>:</td>
                <td>{{ $selectedClass?->name ?? $exam->classes->pluck('name')->implode(', ') }}</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Hari, Tanggal</td>
                <td>:</td>
                <td>{{ $exam->start_time ? $exam->start_time->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y') }}</td>
            </tr>
        </table>
        <table class="w-full">
            <tr>
                <td class="w-32 py-0.5 font-bold">Waktu / Durasi</td>
                <td class="w-3">:</td>
                <td>{{ $exam->duration_minutes }} Menit</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Ruang / Sesi</td>
                <td>:</td>
                <td>Ruang Lab CBT / Sesi 1</td>
            </tr>
            <tr>
                <td class="py-0.5 font-bold">Rekap Kehadiran</td>
                <td>:</td>
                <td><strong>{{ $stats['present'] }} Hadir</strong> / {{ $stats['total'] }} Siswa ({{ $stats['absent'] }} Tidak Hadir)</td>
            </tr>
        </table>
    </div>

    <!-- TABEL DAFTAR HADIR -->
    <table class="w-full border-collapse border border-black text-xs mb-6">
        <thead>
            <tr class="bg-slate-100 text-center font-bold">
                <th class="border border-black p-2 w-10">No</th>
                <th class="border border-black p-2 w-32">No. Peserta</th>
                <th class="border border-black p-2 w-20">NIS</th>
                <th class="border border-black p-2">Nama Lengkap Peserta</th>
                <th class="border border-black p-2 w-36 text-center" colspan="2">Tanda Tangan</th>
                <th class="border border-black p-2 w-24">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                @php $student = $row['student']; @endphp
                <tr class="h-10">
                    <td class="border border-black p-1 text-center font-bold">{{ $row['no'] }}</td>
                    <td class="border border-black p-1 text-center font-mono">{{ $row['participant_number'] }}</td>
                    <td class="border border-black p-1 text-center font-mono">{{ $student->nis }}</td>
                    <td class="border border-black p-1 font-semibold uppercase">{{ $student->name }}</td>
                    
                    <!-- KOLOM TANDA TANGAN ZIG-ZAG -->
                    @if($row['no'] % 2 !== 0)
                        <td class="border border-black p-1 text-left align-top w-20 text-[10px]">
                            <span class="font-bold">{{ $row['no'] }}.</span>
                        </td>
                        <td class="border border-black p-1 w-20"></td>
                    @else
                        <td class="border border-black p-1 w-20"></td>
                        <td class="border border-black p-1 text-left align-top w-20 text-[10px]">
                            <span class="font-bold">{{ $row['no'] }}.</span>
                        </td>
                    @endif

                    <td class="border border-black p-1 text-center text-[10px]">
                        @if($row['has_attended'])
                            <span class="font-bold text-emerald-800">HADIR</span>
                        @else
                            <span class="text-slate-400 italic">Belum Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border border-black p-6 text-center italic">Tidak ada siswa terdaftar pada rombel ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN PENGAWAS RUANG -->
    <div class="flex justify-between items-start text-xs pt-4" style="page-break-inside: avoid;">
        <div class="text-center w-64">
            <div>Mengetahui,</div>
            <div class="font-bold">Kepala SMA Negeri 1 Lengkong</div>
            <div class="h-20"></div>
            <div class="font-bold underline">{{ $school['headmaster_name'] }}</div>
            <div>NIP. {{ $school['headmaster_nip'] }}</div>
        </div>
        <div class="text-center w-64">
            <div>Lengkong, {{ now()->translatedFormat('d F Y') }}</div>
            <div class="font-bold">Pengawas Ruang Ujian</div>
            <div class="h-20"></div>
            <div class="font-bold underline">(...................................................)</div>
            <div>NIP. .................................................</div>
        </div>
    </div>

</body>
</html>

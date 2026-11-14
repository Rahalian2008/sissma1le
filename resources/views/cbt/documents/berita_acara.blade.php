<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Acara Ujian (BAP) - {{ $exam->title }} - {{ $selectedRoom ?: 'Semua Ruang' }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])

    <!-- KaTeX with mhchem Extension -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/mhchem.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof renderMathInElement === 'function') {
                renderMathInElement(document.body, {
                    delimiters: [
                        {left: '$$', right: '$$', display: true},
                        {left: '$', right: '$', display: false},
                        {left: '\\(', right: '\\)', display: false},
                        {left: '\\[', right: '\\]', display: true}
                    ],
                    throwOnError: false
                });
            }
        });
    </script>

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 16mm;
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
<body class="p-8 max-w-4xl mx-auto text-sm leading-relaxed">

    <!-- FLOATING ACTION BUTTONS (NO-PRINT) -->
    <div class="no-print fixed top-4 right-4 z-50 flex items-center gap-2 bg-slate-900/90 text-white p-2.5 rounded-2xl shadow-xl backdrop-blur-sm">
        <a href="{{ route('cbt.exams.show', $exam->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-sans font-bold transition">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-sans font-bold flex items-center gap-1.5 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Berita Acara
        </button>
    </div>

    <!-- FILTER PER RUANG & FORMAT MANUAL (NO-PRINT) -->
    <div class="no-print mb-6 p-4 rounded-2xl bg-slate-100 border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs font-sans">
        <div>
            <strong>Pilihan Cetak Berita Acara:</strong> Sesuaikan ruang atau gunakan format blanko manual.
        </div>
        <form method="GET" action="{{ route('cbt.documents.berita_acara', $exam->id) }}" class="flex items-center gap-2 flex-wrap">
            <select name="room" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-300 py-1.5 px-3 bg-white font-bold">
                <option value="">-- Semua Ruang --</option>
                @foreach($availableRooms as $r)
                    <option value="{{ $r }}" {{ ($selectedRoom === $r) ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>

            <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-300 py-1.5 px-3 bg-white font-bold">
                <option value="">-- Semua Kelas Terdaftar --</option>
                @foreach($exam->classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="is_manual" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-300 py-1.5 px-3 bg-white font-bold {{ $is_manual ? 'text-amber-700 border-amber-400' : 'text-slate-800' }}">
                <option value="0" {{ ! $is_manual ? 'selected' : '' }}>Format Otomatis (Terisi)</option>
                <option value="1" {{ $is_manual ? 'selected' : '' }}>Format Manual / Blanko Kosong</option>
            </select>
        </form>
    </div>

    <!-- KOP RESMI SEKOLAH -->
    <div class="border-b-4 border-double border-black pb-3 mb-6">
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
    <div class="text-center mb-6">
        <h2 class="text-base font-bold uppercase underline tracking-wider">
            BERITA ACARA PELAKSANAAN ASESMEN UJIAN ONLINE (CBT)
            @if($is_manual)
                <span class="no-print text-amber-700 text-xs block font-sans lowercase mt-0.5">(Format Blanko Manual Tulis Tangan)</span>
            @endif
        </h2>
        <div class="text-xs uppercase font-bold mt-0.5 tracking-wider">{{ $exam->title }}</div>
    </div>

    @php
        $examDate = $exam->start_time ?? now();
    @endphp

    <p class="mb-4 text-justify">
        Pada hari ini <strong>{{ $examDate->translatedFormat('l') }}</strong>, tanggal <strong>{{ $examDate->translatedFormat('d') }}</strong> bulan <strong>{{ $examDate->translatedFormat('F') }}</strong> tahun <strong>{{ $examDate->translatedFormat('Y') }}</strong>, di SMA Negeri 1 Lengkong telah diselenggarakan Asesmen Ujian Berbasis Komputer (CBT) dengan rincian data pelaksanaan sebagai berikut:
    </p>

    <!-- RINCIAN IDENTITAS UJIAN -->
    <table class="w-full mb-5 text-xs">
        <tr>
            <td class="w-48 py-1 font-bold">1. Satuan Pendidikan</td>
            <td class="w-3">:</td>
            <td>{{ $school['name'] }}</td>
        </tr>
        <tr>
            <td class="py-1 font-bold">2. Mata Pelajaran</td>
            <td>:</td>
            <td>{{ $exam->subject?->name ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td class="py-1 font-bold">3. Kelas / Rombel</td>
            <td>:</td>
            <td>{{ $selectedClass?->name ?? $exam->classes->pluck('name')->implode(', ') }}</td>
        </tr>
        <tr>
            <td class="py-1 font-bold">4. Ruang Ujian</td>
            <td>:</td>
            <td class="font-bold uppercase">{{ $selectedRoom ?: 'Ruang 01' }}</td>
        </tr>
        <tr>
            <td class="py-1 font-bold">5. Alokasi Waktu</td>
            <td>:</td>
            <td>{{ $exam->duration_minutes }} Menit (Pukul {{ $examDate->format('H:i') }} WIB s.d Selesai)</td>
        </tr>
        <tr>
            <td class="py-1 font-bold">6. Jumlah Peserta Terdaftar</td>
            <td>:</td>
            <td><strong>{{ $total_registered }}</strong> orang</td>
        </tr>
        <tr>
            <td class="py-1 font-bold">7. Jumlah Peserta Hadir</td>
            <td>:</td>
            <td><strong>{{ $total_present }}</strong> orang</td>
        </tr>
        <tr>
            <td class="py-1 font-bold">8. Jumlah Peserta Tidak Hadir</td>
            <td>:</td>
            <td><strong>{{ $total_absent }}</strong> orang</td>
        </tr>
    </table>

    <!-- DAFTAR SISWA TIDAK HADIR -->
    @if(! $is_manual && !empty($absent_students))
        <div class="mb-4">
            <div class="font-bold text-xs mb-1">Rincian Peserta Tidak Hadir:</div>
            <table class="w-full border-collapse border border-black text-xs">
                <thead>
                    <tr class="bg-slate-100 text-center font-bold">
                        <th class="border border-black p-1 w-10">No</th>
                        <th class="border border-black p-1 w-24">NIS</th>
                        <th class="border border-black p-1">Nama Peserta</th>
                        <th class="border border-black p-1 w-32">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absent_students as $idx => $absent)
                        <tr>
                            <td class="border border-black p-1 text-center">{{ $idx + 1 }}</td>
                            <td class="border border-black p-1 text-center font-mono">{{ $absent->nis }}</td>
                            <td class="border border-black p-1 uppercase">{{ $absent->name }}</td>
                            <td class="border border-black p-1 italic text-slate-600 text-center">Tidak Mengikuti Sesi</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($is_manual)
        <div class="mb-4">
            <div class="font-bold text-xs mb-1">Rincian Peserta Tidak Hadir (Diisi oleh Pengawas Ruang):</div>
            <table class="w-full border-collapse border border-black text-xs">
                <thead>
                    <tr class="bg-slate-100 text-center font-bold">
                        <th class="border border-black p-1 w-10">No</th>
                        <th class="border border-black p-1 w-28">NIS</th>
                        <th class="border border-black p-1">Nama Peserta</th>
                        <th class="border border-black p-1 w-32">Alasan / Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @for($m = 1; $m <= 4; $m++)
                        <tr class="h-7">
                            <td class="border border-black p-1 text-center">{{ $m }}</td>
                            <td class="border border-black p-1"></td>
                            <td class="border border-black p-1"></td>
                            <td class="border border-black p-1"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    @endif

    <!-- CATATAN PELAKSANAAN UJIAN -->
    <div class="border border-black p-3 mb-6">
        <div class="font-bold underline text-xs mb-2">CATATAN KHUSUS SELAMA UJIAN BERLANGSUNG:</div>
        @if($is_manual)
            <div class="min-h-[70px] space-y-4 pt-1">
                <div class="border-b border-dotted border-black/40 h-4"></div>
                <div class="border-b border-dotted border-black/40 h-4"></div>
                <div class="border-b border-dotted border-black/40 h-4"></div>
            </div>
        @else
            <p class="text-xs leading-relaxed text-slate-800 min-h-[50px]">
                Pelaksanaan asesmen ujian berbasis komputer (CBT) di {{ $selectedRoom ?: 'ruang ujian' }} berlangsung dengan tertib, lancar, dan aman. Sistem keamanan pengawasan aktif dan terdistribusi dengan baik.
            </p>
        @endif
    </div>

    <p class="mb-6 text-xs text-justify">
        Demikian Berita Acara Pelaksanaan Asesmen Ujian Berbasis Komputer (CBT) ini dibuat dengan sesungguhnya untuk dipergunakan sebagaimana mestinya.
    </p>

    <!-- TANDA TANGAN PENGAWAS RUANG -->
    <div class="flex justify-between items-start text-xs pt-2" style="page-break-inside: avoid;">
        <div class="text-center w-64">
            <div>Mengetahui,</div>
            <div class="font-bold">Kepala SMA Negeri 1 Lengkong</div>
            <div class="h-20"></div>
            <div class="font-bold underline">{{ $school['headmaster_name'] }}</div>
            <div>NIP. {{ $school['headmaster_nip'] }}</div>
        </div>
        <div class="text-center w-64">
            <div>Lengkong, {{ now()->translatedFormat('d F Y') }}</div>
            <div class="font-bold">Pengawas Ruang ({{ $selectedRoom ?: 'Ruang 01' }})</div>
            <div class="h-20"></div>
            <div class="font-bold underline">(...................................................)</div>
            <div>NIP. .................................................</div>
        </div>
    </div>

</body>
</html>

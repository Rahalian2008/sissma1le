@extends('layouts.app')

@section('title', 'Buku Tamu Pengunjung Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs">
        <div>
            <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">Presensi Pengunjung</div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>📝 Buku Tamu & Presensi Perpustakaan</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pencatatan kehadiran pengunjung perpustakaan mandiri melalui Scan Barcode, Master Data Siswa & Guru, atau Entri Tamu Manual.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span>🖥️ Buka Kiosk Tamu Mandiri</span>
            </a>
            <a href="{{ route('library.reports.visitors') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span>📊 Laporan Pengunjung</span>
            </a>
            <a href="{{ route('library.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                &larr; Dashboard
            </a>
        </div>
    </div>

    <!-- Form Presensi Kunjungan Cepat -->
    <div class="bg-gradient-to-br from-indigo-950/40 via-slate-900 to-indigo-900/40 border border-indigo-800/40 rounded-3xl p-6 shadow-sm">
        <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
            <span>✨ Catat Kunjungan Baru Sekarang</span>
        </h2>

        <form action="{{ route('library.visitors.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Mode Pemilihan Pengunjung -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="scan" checked onchange="switchVisitorMode('scan')" class="peer sr-only">
                    <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-lg mb-1">⚡</div>
                        <div class="text-xs font-bold">Scan Barcode / NISN</div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="student" onchange="switchVisitorMode('student')" class="peer sr-only">
                    <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-lg mb-1">🎓</div>
                        <div class="text-xs font-bold">Pilih Siswa (Master)</div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="teacher" onchange="switchVisitorMode('teacher')" class="peer sr-only">
                    <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-lg mb-1">👨‍🏫</div>
                        <div class="text-xs font-bold">Pilih Guru (Master)</div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="manual" onchange="switchVisitorMode('manual')" class="peer sr-only">
                    <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-lg mb-1">✍️</div>
                        <div class="text-xs font-bold">Entri Tamu Manual</div>
                    </div>
                </label>
            </div>

            <!-- Panel Input Dinamis Sesuai Mode -->
            <!-- 1. Mode Scan -->
            <div id="panel-scan" class="space-y-1">
                <label class="block text-xs font-bold text-slate-300 mb-1">Scan Barcode Kartu / Ketik NISN / NIP / Nomor Anggota</label>
                <div class="relative">
                    <input type="text" name="scan_code" autofocus placeholder="Tempelkan kartu ke barcode scanner atau ketik NISN..." class="w-full pl-9 pr-4 py-2.5 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-white font-mono">
                    <svg class="w-4 h-4 text-cyan-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
            </div>

            <!-- 2. Mode Siswa -->
            <div id="panel-student" class="hidden space-y-1">
                <label class="block text-xs font-bold text-slate-300 mb-1">Pilih Siswa dari Master Data</label>
                <select name="student_id" class="w-full px-3 py-2.5 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none text-white">
                    <option value="">-- Pilih Nama Siswa --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->schoolClass->name ?? '-' }} - NISN: {{ $s->nisn }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Mode Guru -->
            <div id="panel-teacher" class="hidden space-y-1">
                <label class="block text-xs font-bold text-slate-300 mb-1">Pilih Guru / Pegawai dari Master Data</label>
                <select name="teacher_id" class="w-full px-3 py-2.5 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none text-white">
                    <option value="">-- Pilih Nama Guru / Pegawai --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->title ?? 'Guru' }} - NIP: {{ $t->nip }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Mode Manual -->
            <div id="panel-manual" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nama Lengkap Pengunjung</label>
                    <input type="text" name="manual_name" placeholder="Nama tamu pengunjung..." class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Asal Kelas / Instansi / Lembaga</label>
                    <input type="text" name="manual_class" placeholder="Contoh: SMAN 2 / Alumni / Pengawas..." class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none text-white">
                </div>
            </div>

            <!-- Pilihan Keperluan Kunjungan & Tombol Submit -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">Keperluan Kunjungan <span class="text-rose-500">*</span></label>
                    <select name="purpose" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none text-white">
                        <option value="Membaca Buku Digital (Flip HTML)">Membaca Buku Digital (Flip HTML)</option>
                        <option value="Membaca Buku Fisik di Ruang Baca">Membaca Buku Fisik di Ruang Baca</option>
                        <option value="Meminjam / Mengembalikan Buku">Meminjam / Mengembalikan Buku</option>
                        <option value="Mengerjakan Tugas & Riset Portofolio">Mengerjakan Tugas & Riset Portofolio</option>
                        <option value="Membaca Majalah / Koran Sekolah">Membaca Majalah / Koran Sekolah</option>
                        <option value="Diskusi Kelompok Literasi">Diskusi Kelompok Literasi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-xs shadow-md transition transform active:scale-95 cursor-pointer">
                        ✓ Catat Presensi Hadir
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Rekap Statistik Kunjungan & Tabel Riwayat -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Rekap Keperluan Hari Ini -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-xs space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rekap Keperluan Hari Ini</h3>
            <div class="space-y-2">
                @forelse($purposeStats as $stat)
                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-700 dark:text-slate-300 truncate pr-2">{{ $stat->purpose }}</span>
                        <span class="px-2 py-0.5 rounded-full bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-bold shrink-0">{{ $stat->total }}</span>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Belum ada data kunjungan hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tabel Riwayat Pengunjung -->
        <div class="lg:col-span-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋 Daftar Riwayat Pengunjung</span>
                </h3>

                <form action="{{ route('library.visitors.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama..." class="px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold">Cari</button>
                </form>
            </div>

            @if($visitors->isEmpty())
                <div class="py-12 text-center text-slate-400 text-xs">
                    Tidak ada catatan kunjungan pada tanggal ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="text-[11px] text-slate-400 uppercase bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Waktu Kunjungan</th>
                                <th class="px-4 py-3">Nama Pengunjung</th>
                                <th class="px-4 py-3">Tipe & Identitas</th>
                                <th class="px-4 py-3">Kelas / Instansi</th>
                                <th class="px-4 py-3">Keperluan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($visitors as $v)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-slate-500 font-mono">
                                        {{ $v->visited_at->format('H:i') }} WIB
                                        <div class="text-[10px] text-slate-400">{{ $v->visited_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                        {{ $v->name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $v->visitor_type === 'siswa' ? 'bg-cyan-500/15 text-cyan-600' : ($v->visitor_type === 'guru' ? 'bg-emerald-500/15 text-emerald-600' : 'bg-purple-500/15 text-purple-600') }}">
                                            {{ $v->visitor_type }}
                                        </span>
                                        @if($v->identity_number)
                                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $v->identity_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                        {{ $v->class_or_institution ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                            {{ $v->purpose }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $visitors->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function switchVisitorMode(mode) {
        document.getElementById('panel-scan').classList.add('hidden');
        document.getElementById('panel-student').classList.add('hidden');
        document.getElementById('panel-teacher').classList.add('hidden');
        document.getElementById('panel-manual').classList.add('hidden');

        if (mode === 'scan') {
            document.getElementById('panel-scan').classList.remove('hidden');
        } else if (mode === 'student') {
            document.getElementById('panel-student').classList.remove('hidden');
        } else if (mode === 'teacher') {
            document.getElementById('panel-teacher').classList.remove('hidden');
        } else if (mode === 'manual') {
            document.getElementById('panel-manual').classList.remove('hidden');
        }
    }
</script>
@endsection


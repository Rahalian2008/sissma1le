@extends('layouts.app')

@section('title', 'Buku Tamu Pengunjung Perpustakaan')

@section('content')
<div class="space-y-6">
    <!-- Sub-Navigasi Perpustakaan Terpadu -->
    @include('library.partials.nav')

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4 p-4 sm:p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl sm:rounded-3xl shadow-xs">
        <div class="min-w-0">
            <div class="text-[11px] sm:text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Presensi Pengunjung</div>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>Buku Tamu & Presensi Perpustakaan</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Pencatatan kehadiran pengunjung perpustakaan mandiri melalui Scan Barcode, Master Data Siswa & Guru, atau Entri Tamu Manual.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full lg:w-auto shrink-0">
            <a href="{{ route('library.guest_book.index') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-semibold text-xs transition flex items-center justify-center gap-1.5 text-center shadow-2xs">
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Kiosk Tamu</span>
                <span class="text-xs">&nearr;</span>
            </a>
            <a href="{{ route('library.reports.visitors') }}" class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-semibold text-xs transition flex items-center justify-center gap-1.5 text-center shadow-2xs">
                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Laporan</span>
            </a>
            <a href="{{ route('library.index') }}" class="col-span-2 sm:col-auto px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition text-center border border-slate-200/80 dark:border-slate-700">
                &larr; Dashboard
            </a>
        </div>
    </div>

    <!-- Form Presensi Kunjungan Cepat -->
    <div class="bg-gradient-to-br from-indigo-950/40 via-slate-900 to-indigo-900/40 border border-indigo-800/40 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-sm">
        <h2 class="text-sm sm:text-base font-bold text-white mb-3 sm:mb-4 flex items-center gap-2">
            <span>✨ Catat Kunjungan Baru Sekarang</span>
        </h2>

        <form action="{{ route('library.visitors.store') }}" method="POST" class="space-y-3.5 sm:space-y-4">
            @csrf

            <!-- Mode Pemilihan Pengunjung -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="scan" checked onchange="switchVisitorMode('scan')" class="peer sr-only">
                    <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-base sm:text-lg mb-0.5">⚡</div>
                        <div class="text-[11px] sm:text-xs font-bold leading-tight">Scan Barcode / NISN</div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="student" onchange="switchVisitorMode('student')" class="peer sr-only">
                    <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-base sm:text-lg mb-0.5">🎓</div>
                        <div class="text-[11px] sm:text-xs font-bold leading-tight">Pilih Siswa (Master)</div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="teacher" onchange="switchVisitorMode('teacher')" class="peer sr-only">
                    <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-base sm:text-lg mb-0.5">👨‍🏫</div>
                        <div class="text-[11px] sm:text-xs font-bold leading-tight">Pilih Guru (Master)</div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="visitor_mode" value="manual" onchange="switchVisitorMode('manual')" class="peer sr-only">
                    <div class="p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-300 text-center peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-500 peer-checked:shadow-md transition">
                        <div class="text-base sm:text-lg mb-0.5">✍️</div>
                        <div class="text-[11px] sm:text-xs font-bold leading-tight">Entri Tamu Manual</div>
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

            <!-- 2. Mode Siswa Khusus: Pilihan Kelas, Pencarian Cepat, & Multi-Select Siswa -->
            <div id="panel-student" class="hidden space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="student_class_filter" class="block text-xs font-bold text-slate-300 mb-1 flex items-center gap-1.5">
                            <span>🏫 Pilihan / Filter Kelas</span>
                        </label>
                        <select id="student_class_filter" onchange="filterStudentList()" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-white font-medium">
                            <option value="all">-- Semua Kelas ({{ count($students) }} Siswa) --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="student_keyword_filter" class="block text-xs font-bold text-slate-300 mb-1 flex items-center gap-1.5">
                            <span>🔍 Cari Nama / NISN Siswa</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="student_keyword_filter" oninput="filterStudentList()" placeholder="Ketik nama atau NISN siswa..." class="w-full pl-8 pr-8 py-2 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none focus:border-indigo-500 text-white">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <button type="button" id="clear_student_keyword" onclick="clearStudentKeyword()" class="hidden absolute right-2.5 top-2 text-slate-400 hover:text-white text-xs font-bold">✕</button>
                        </div>
                    </div>
                </div>

                <!-- Toolbar Pilihan & Counter -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 pb-0.5">
                    <div class="flex items-center justify-between sm:justify-start gap-2">
                        <span id="selected-student-count-badge" class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-400 font-bold text-xs border border-slate-700 transition">
                            0 Siswa Dipilih
                        </span>
                        <span id="visible-student-count-badge" class="text-[11px] text-slate-400">
                            (Menampilkan {{ count($students) }} siswa)
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:flex sm:items-center gap-1.5 w-full sm:w-auto">
                        <button type="button" id="btn-select-all-class" onclick="selectAllFilteredStudents(true)" class="px-2.5 py-2 sm:py-1 rounded-lg bg-indigo-600/80 hover:bg-indigo-600 text-white text-[11px] font-bold shadow-xs transition flex items-center justify-center gap-1 text-center cursor-pointer">
                            <span>☑️ Pilih Semua</span>
                        </button>
                        <button type="button" onclick="selectAllFilteredStudents(false)" class="px-2.5 py-2 sm:py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-semibold border border-slate-700 transition flex items-center justify-center cursor-pointer">
                            <span>Kosongkan</span>
                        </button>
                    </div>
                </div>

                <!-- Container Chips Siswa Terpilih -->
                <div id="selected-students-chips-wrapper" class="hidden p-2.5 rounded-2xl bg-indigo-950/40 border border-indigo-800/40 space-y-1.5">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-300 flex items-center justify-between">
                        <span>Daftar Siswa yang Akan Dipresensikan:</span>
                        <span class="text-[10px] text-slate-400 font-normal">Klik ✕ untuk batal</span>
                    </div>
                    <div id="selected-students-chips" class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto"></div>
                </div>

                <!-- Container Daftar Siswa Scrollable -->
                <div class="border border-slate-700/80 rounded-2xl bg-slate-950/70 p-2 overflow-hidden shadow-inner">
                    <div id="student-list-container" class="max-h-60 sm:max-h-68 overflow-y-auto space-y-1.5 pr-1 divide-y divide-slate-800/60">
                        @foreach($students as $s)
                            <label class="student-item flex items-center justify-between p-2.5 sm:p-2 rounded-xl border border-slate-800/60 bg-slate-900/50 hover:bg-slate-800/70 hover:border-indigo-500/50 cursor-pointer transition select-none group min-h-[48px]"
                                   id="student-item-{{ $s->id }}"
                                   data-id="{{ $s->id }}"
                                   data-class-id="{{ $s->class_id }}"
                                   data-class-name="{{ $s->schoolClass->name ?? '-' }}"
                                   data-name="{{ strtolower($s->name) }}"
                                   data-raw-name="{{ $s->name }}"
                                   data-nisn="{{ $s->nisn }}"
                                   data-nis="{{ $s->nis }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" onchange="handleStudentCheckboxChange(this)" class="student-checkbox w-4.5 h-4.5 rounded text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900 bg-slate-800 border-slate-700 cursor-pointer shrink-0">
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-white group-hover:text-indigo-300 transition truncate">{{ $s->name }}</div>
                                        <div class="text-[10.5px] text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded bg-indigo-500/20 text-indigo-300 font-semibold text-[10px]">Kelas {{ $s->schoolClass->name ?? '-' }}</span>
                                            <span class="font-mono text-[10px]">NISN: {{ $s->nisn ?: ($s->nis ?: '-') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="student-status-badge text-[10px] font-bold text-slate-500 group-hover:text-slate-400 shrink-0">
                                    Pilih
                                </span>
                            </label>
                        @endforeach

                        <div id="no-students-found" class="hidden py-8 text-center text-xs text-slate-400">
                            🔍 Tidak ada siswa yang sesuai dengan filter kelas atau kata kunci pencarian.
                        </div>
                    </div>
                </div>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 sm:gap-3 pt-2">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">Keperluan Kunjungan <span class="text-rose-500">*</span></label>
                    <select name="purpose" required class="w-full px-3 py-2.5 sm:py-2 text-xs bg-slate-950 border border-slate-700 rounded-xl outline-none text-white font-medium">
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
                    <button type="submit" class="w-full py-3 sm:py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-xs sm:text-sm shadow-md transition transform active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                        <span>✓ Catat Presensi Hadir</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Rekap Statistik Kunjungan & Tabel Riwayat -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Rekap Keperluan Hari Ini -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rekap Keperluan Hari Ini</h3>
                <span class="text-[10px] font-bold text-indigo-500">{{ count($purposeStats) }} Kategori</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-1 gap-2">
                @forelse($purposeStats as $stat)
                    <div class="p-2 sm:p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-700 dark:text-slate-300 truncate pr-1 text-[11px] sm:text-xs">{{ $stat->purpose }}</span>
                        <span class="px-1.5 py-0.5 rounded-full bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-black text-[10px] sm:text-xs shrink-0">{{ $stat->total }}</span>
                    </div>
                @empty
                    <div class="col-span-full py-4 text-center text-slate-400 text-xs">
                        Belum ada data kunjungan hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tabel & Kartu Riwayat Pengunjung (Adaptive: Card View on Mobile, Table on Desktop) -->
        <div class="lg:col-span-3 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl sm:rounded-3xl overflow-hidden shadow-xs">
            <div class="p-3.5 sm:p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋 Daftar Riwayat Pengunjung</span>
                </h3>

                <form action="{{ route('library.visitors.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                    <div class="grid grid-cols-1 xs:grid-cols-2 sm:flex sm:items-center gap-2 flex-1">
                        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="px-2.5 py-2 sm:py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama..." class="px-2.5 py-2 sm:py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl outline-none text-slate-800 dark:text-slate-200">
                    </div>
                    <button type="submit" class="px-4 py-2 sm:py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1">
                        <span>🔍 Cari</span>
                    </button>
                </form>
            </div>

            @if($visitors->isEmpty())
                <div class="py-12 text-center text-slate-400 text-xs">
                    Tidak ada catatan kunjungan pada tanggal ini.
                </div>
            @else
                <!-- 1. Tampilan Mobile Phone (Adaptive Card View - No Horizontal Scroll) -->
                <div class="md:hidden space-y-2.5 p-3 sm:p-4">
                    @foreach($visitors as $v)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 space-y-2 transition hover:border-indigo-400">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">{{ $v->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                        <span>{{ $v->class_or_institution ?? '-' }}</span>
                                        @if($v->identity_number)
                                            <span class="text-slate-300 dark:text-slate-600">•</span>
                                            <span class="font-mono text-[10px] text-slate-400">{{ $v->identity_number }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase shrink-0 {{ $v->visitor_type === 'siswa' ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400' : ($v->visitor_type === 'guru' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-purple-500/15 text-purple-600 dark:text-purple-400') }}">
                                    {{ $v->visitor_type }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-1.5 border-t border-slate-200/60 dark:border-slate-700/60 flex-wrap gap-1">
                                <span class="font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700/80 text-[10.5px]">
                                    📖 {{ $v->purpose }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">
                                    🕒 {{ $v->visited_at->format('H:i') }} WIB • {{ $v->visited_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 2. Tampilan Tablet & Desktop (Multi-Column Structured Table) -->
                <div class="hidden md:block overflow-x-auto">
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
            filterStudentList();
        } else if (mode === 'teacher') {
            document.getElementById('panel-teacher').classList.remove('hidden');
        } else if (mode === 'manual') {
            document.getElementById('panel-manual').classList.remove('hidden');
        }
    }

    function filterStudentList() {
        const classFilter = document.getElementById('student_class_filter') ? document.getElementById('student_class_filter').value : 'all';
        const keyword = (document.getElementById('student_keyword_filter') ? document.getElementById('student_keyword_filter').value : '').toLowerCase().trim();
        const clearBtn = document.getElementById('clear_student_keyword');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', keyword.length === 0);
        }

        const items = document.querySelectorAll('.student-item');
        let visibleCount = 0;

        items.forEach(item => {
            const itemClassId = item.getAttribute('data-class-id');
            const itemName = item.getAttribute('data-name') || '';
            const itemNisn = item.getAttribute('data-nisn') || '';
            const itemNis = item.getAttribute('data-nis') || '';

            const matchClass = (classFilter === 'all') || (itemClassId === classFilter);
            const matchKeyword = !keyword || itemName.includes(keyword) || itemNisn.includes(keyword) || itemNis.includes(keyword);

            if (matchClass && matchKeyword) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const noFound = document.getElementById('no-students-found');
        if (noFound) {
            noFound.classList.toggle('hidden', visibleCount > 0);
        }

        const visibleBadge = document.getElementById('visible-student-count-badge');
        if (visibleBadge) {
            visibleBadge.textContent = `(Menampilkan ${visibleCount} siswa)`;
        }

        const btnSelectAll = document.getElementById('btn-select-all-class');
        if (btnSelectAll) {
            const classSelect = document.getElementById('student_class_filter');
            if (classSelect && classFilter !== 'all') {
                const selectedText = classSelect.options[classSelect.selectedIndex].text;
                btnSelectAll.innerHTML = `<span>☑️ Pilih Semua (${selectedText.replace('--', '').trim()})</span>`;
            } else {
                btnSelectAll.innerHTML = `<span>☑️ Pilih Semua</span>`;
            }
        }
    }

    function clearStudentKeyword() {
        const input = document.getElementById('student_keyword_filter');
        if (input) {
            input.value = '';
            filterStudentList();
            input.focus();
        }
    }

    function handleStudentCheckboxChange(checkbox) {
        const item = checkbox.closest('.student-item');
        const badge = item.querySelector('.student-status-badge');

        if (checkbox.checked) {
            item.classList.add('bg-indigo-950/60', 'border-indigo-500/80');
            item.classList.remove('bg-slate-900/50', 'border-slate-800/60');
            if (badge) {
                badge.textContent = '✓ Terpilih';
                badge.className = 'student-status-badge text-[10px] font-bold text-emerald-400 shrink-0';
            }
        } else {
            item.classList.remove('bg-indigo-950/60', 'border-indigo-500/80');
            item.classList.add('bg-slate-900/50', 'border-slate-800/60');
            if (badge) {
                badge.textContent = 'Pilih';
                badge.className = 'student-status-badge text-[10px] font-bold text-slate-500 group-hover:text-slate-400 shrink-0';
            }
        }

        updateSelectedStudentsUI();
    }

    function selectAllFilteredStudents(select) {
        const visibleItems = document.querySelectorAll('.student-item:not(.hidden)');
        visibleItems.forEach(item => {
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox && checkbox.checked !== select) {
                checkbox.checked = select;
                const badge = item.querySelector('.student-status-badge');
                if (select) {
                    item.classList.add('bg-indigo-950/60', 'border-indigo-500/80');
                    item.classList.remove('bg-slate-900/50', 'border-slate-800/60');
                    if (badge) {
                        badge.textContent = '✓ Terpilih';
                        badge.className = 'student-status-badge text-[10px] font-bold text-emerald-400 shrink-0';
                    }
                } else {
                    item.classList.remove('bg-indigo-950/60', 'border-indigo-500/80');
                    item.classList.add('bg-slate-900/50', 'border-slate-800/60');
                    if (badge) {
                        badge.textContent = 'Pilih';
                        badge.className = 'student-status-badge text-[10px] font-bold text-slate-500 group-hover:text-slate-400 shrink-0';
                    }
                }
            }
        });
        updateSelectedStudentsUI();
    }

    function removeSelectedStudent(studentId) {
        const checkbox = document.querySelector(`.student-checkbox[value="${studentId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            handleStudentCheckboxChange(checkbox);
        }
    }

    function updateSelectedStudentsUI() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        const count = checkedBoxes.length;

        const countBadge = document.getElementById('selected-student-count-badge');
        if (countBadge) {
            countBadge.textContent = `${count} Siswa Dipilih`;
            if (count > 0) {
                countBadge.className = 'px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 font-black text-xs border border-emerald-500/40 shadow-xs transition';
            } else {
                countBadge.className = 'px-2.5 py-1 rounded-lg bg-slate-800 text-slate-400 font-bold text-xs border border-slate-700 transition';
            }
        }

        const chipsWrapper = document.getElementById('selected-students-chips-wrapper');
        const chipsContainer = document.getElementById('selected-students-chips');

        if (chipsWrapper && chipsContainer) {
            if (count === 0) {
                chipsWrapper.classList.add('hidden');
                chipsContainer.innerHTML = '';
            } else {
                chipsWrapper.classList.remove('hidden');
                let chipsHtml = '';
                checkedBoxes.forEach(cb => {
                    const item = cb.closest('.student-item');
                    const rawName = item.getAttribute('data-raw-name') || '';
                    const className = item.getAttribute('data-class-name') || '';
                    const id = cb.value;

                    chipsHtml += `
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-indigo-600/30 border border-indigo-500/40 text-white text-[11px] font-semibold">
                            <span class="truncate max-w-[150px] sm:max-w-[200px]">${rawName}</span>
                            <span class="text-[9px] text-indigo-300 font-mono">(${className})</span>
                            <button type="button" onclick="removeSelectedStudent('${id}')" class="text-indigo-300 hover:text-white hover:bg-indigo-500/30 rounded-full w-3.5 h-3.5 inline-flex items-center justify-center text-[10px] ml-0.5 cursor-pointer" title="Hapus">✕</button>
                        </span>
                    `;
                });
                chipsContainer.innerHTML = chipsHtml;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="{{ route('library.visitors.store') }}"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                const modeInput = form.querySelector('input[name="visitor_mode"]:checked');
                if (modeInput && modeInput.value === 'student') {
                    const checkedBoxes = form.querySelectorAll('.student-checkbox:checked');
                    if (checkedBoxes.length === 0) {
                        e.preventDefault();
                        alert('Silakan pilih minimal 1 siswa dari daftar sebelum mencatat presensi.');
                    }
                }
            });
        }
    });
</script>
@endsection


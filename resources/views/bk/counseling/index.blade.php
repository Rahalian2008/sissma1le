@extends('layouts.app')

@section('title', 'Layanan Bimbingan & Konseling - BK')

@section('content')
<div class="space-y-6">
    @include('bk.partials.nav')

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('bk.dashboard') }}" class="hover:text-emerald-600 font-medium">Bimbingan Konseling</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-white font-bold">Layanan Konseling</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Layanan Konseling Siswa (POP-BK)</h1>
            <p class="text-xs text-slate-400 mt-0.5">Dokumentasi operasional konseling pribadi, sosial, belajar, dan penelusuran karier & studi lanjut</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Tombol Cetak Rekapitulasi (Keseluruhan & Custom) -->
            <button type="button" onclick="openRecapModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rekapitulasi</span>
            </button>

            <!-- Tombol Catat Sesi Baru -->
            <a href="{{ route('bk.counseling.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Catat Sesi Konseling</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik Cepat (Summary Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0 text-lg font-bold">
                📋
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">Total Layanan</span>
                <span class="text-lg font-black text-slate-900 dark:text-white">{{ $summary['total'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Sesi</span></span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center shrink-0 text-lg font-bold">
                🎓
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">Bimbingan Karier</span>
                <span class="text-lg font-black text-sky-600">{{ $summary['career'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Siswa</span></span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 text-lg font-bold">
                🏛️
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">Target Kuliah / Kerja</span>
                <span class="text-sm font-black text-slate-900 dark:text-white">{{ $summary['college'] ?? 0 }} <span class="text-xs text-sky-500 font-semibold">Kuliah</span> / {{ $summary['work'] ?? 0 }} <span class="text-xs text-emerald-500 font-semibold">Kerja</span></span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0 text-lg font-bold">
                📅
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">Jadwal RTL Mendatang</span>
                <span class="text-lg font-black text-amber-600">{{ $summary['upcoming_rtl'] ?? 0 }} <span class="text-xs font-normal text-slate-400">Agenda</span></span>
            </div>
        </div>
    </div>

    <!-- Filter Bar Interaktif -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('bk.counseling.index') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
            <!-- Filter Bidang Layanan -->
            <select name="field_of_service" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                <option value="">-- Semua Bidang Layanan --</option>
                <option value="PRIBADI" {{ request('field_of_service') == 'PRIBADI' ? 'selected' : '' }}>Pribadi</option>
                <option value="SOSIAL" {{ request('field_of_service') == 'SOSIAL' ? 'selected' : '' }}>Sosial</option>
                <option value="BELAJAR" {{ request('field_of_service') == 'BELAJAR' ? 'selected' : '' }}>Belajar</option>
                <option value="KARIER" {{ request('field_of_service') == 'KARIER' ? 'selected' : '' }}>Karier (Kuliah / Kerja)</option>
            </select>

            <!-- Filter Pilihan Arah Karier -->
            <select name="career_path" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                <option value="">-- Semua Arah Karier --</option>
                <option value="KULIAH" {{ request('career_path') == 'KULIAH' ? 'selected' : '' }}>🏛️ Kuliah (Perguruan Tinggi)</option>
                <option value="BEKERJA" {{ request('career_path') == 'BEKERJA' ? 'selected' : '' }}>💼 Bekerja / Wirausaha</option>
            </select>

            <!-- Filter Kelas / Rombel -->
            <select name="class_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                <option value="">-- Semua Kelas --</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                <option value="">-- Semua Status --</option>
                <option value="SELESAI" {{ request('status') == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                <option value="SEDANG_BERLANGSUNG" {{ request('status') == 'SEDANG_BERLANGSUNG' ? 'selected' : '' }}>Sedang Berlangsung</option>
                <option value="TERJADWAL" {{ request('status') == 'TERJADWAL' ? 'selected' : '' }}>Terjadwal</option>
                <option value="DIRUJUK" {{ request('status') == 'DIRUJUK' ? 'selected' : '' }}>Dirujuk</option>
            </select>

            <!-- Search Bar -->
            <div class="relative grow max-w-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NISN, kampus, prodi..." class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 pl-8 pr-3 py-2 text-xs">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="submit" class="px-3.5 py-2 rounded-xl bg-teal-600 text-white font-bold hover:bg-teal-700 transition">Filter</button>
            @if(request()->hasAny(['field_of_service', 'career_path', 'class_id', 'status', 'search', 'date_from', 'date_to']))
                <a href="{{ route('bk.counseling.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Sesi Konseling -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Kode Sesi</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Siswa & Kelas</th>
                        <th class="py-3 px-4">Bidang Layanan</th>
                        <th class="py-3 px-4">Peminatan Karier</th>
                        <th class="py-3 px-4">Topik Konseling</th>
                        <th class="py-3 px-4">Jadwal RTL</th>
                        <th class="py-3 px-4">Konselor</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($sessions as $s)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white text-[11px]">
                            {{ $s->session_code }}
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($s->date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('bk.student.profile360', $s->student) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600">
                                {{ $s->student?->name }}
                            </a>
                            <span class="block text-[10px] text-slate-400">{{ $s->student?->schoolClass?->name }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->field_badge['class'] }}">
                                {{ $s->field_badge['label'] }}
                            </span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">
                                {{ str_replace('_', ' ', $s->session_type) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($s->field_of_service === 'KARIER' && $s->career_path)
                                @if($s->career_path === 'KULIAH')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold text-[10px] border border-sky-300 dark:border-sky-800">
                                        🏛️ Kuliah: {{ $s->study_admission_track ?: 'Mandiri' }}
                                    </span>
                                    @if($s->campus_name || $s->study_program)
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate max-w-[150px]" title="{{ $s->campus_name }} - {{ $s->study_program }}">
                                            {{ $s->campus_name }} {{ $s->study_program ? '('.$s->study_program.')' : '' }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-[10px] border border-emerald-300 dark:border-emerald-800">
                                        💼 Bekerja
                                    </span>
                                    @if($s->work_field)
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 truncate max-w-[150px]" title="{{ $s->work_field }}">
                                            {{ $s->work_field }}
                                        </div>
                                    @endif
                                @endif
                            @else
                                <span class="text-slate-400 text-[11px]">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800 dark:text-white max-w-xs truncate">
                            {{ $s->topic }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($s->follow_up_date)
                                <div class="flex items-center gap-1 text-[11px] font-bold text-amber-700 dark:text-amber-400">
                                    <span>📅</span>
                                    <span>{{ \Carbon\Carbon::parse($s->follow_up_date)->translatedFormat('d/m/Y') }}</span>
                                </div>
                                <span class="text-[9px] px-1.5 py-0.2 rounded font-extrabold {{ $s->follow_up_status === 'SELESAI' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-500' }}">
                                    {{ $s->follow_up_status === 'SELESAI' ? 'SELESAI' : 'PENDING' }}
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px]">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                            {{ $s->counselor?->name ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $s->status === 'SELESAI' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('bk.counseling.show', $s) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 font-bold hover:bg-teal-100 transition text-[11px]">
                                Detail
                            </a>
                            <a href="{{ route('bk.counseling.edit', $s) }}" class="inline-flex items-center px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-bold hover:bg-indigo-100 transition text-[11px]">
                                Edit
                            </a>
                            <a href="{{ route('bk.counseling.print', $s) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 transition text-[11px]" title="Cetak Berita Acara Resmi">
                                Cetak
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-8 text-center text-slate-400">Belum ada sesi konseling yang sesuai kriteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $sessions->links() }}
        </div>
    </div>
</div>

<!-- MODAL CETAK REKAPITULASI LAYANAN KONSELING -->
<div id="modal-recap-print" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden p-6 sm:p-7 space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-base">
                    🖨️
                </div>
                <div>
                    <h3 class="font-black text-slate-900 dark:text-white text-base">Cetak Rekapitulasi Layanan Konseling</h3>
                    <p class="text-xs text-slate-400">Pilih format cetak keseluruhan atau sesuaikan kriteria kustom</p>
                </div>
            </div>
            <button type="button" onclick="closeRecapModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition">
                &times;
            </button>
        </div>

        <!-- Opsi 1: Cetak Seluruh Data Konseling -->
        <div class="p-4 rounded-2xl bg-sky-50/60 dark:bg-sky-950/20 border border-sky-200 dark:border-sky-900/40 flex items-center justify-between gap-3">
            <div>
                <h4 class="text-xs font-bold text-sky-950 dark:text-sky-300">1. Cetak Seluruh Data Konseling</h4>
                <p class="text-[11px] text-sky-700 dark:text-sky-400">Cetak semua riwayat layanan konseling tanpa batasan filter</p>
            </div>
            <a href="{{ route('bk.counseling.recap.print') }}" target="_blank" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow transition whitespace-nowrap">
                Cetak Semua &rarr;
            </a>
        </div>

        <!-- Opsi 2: Cetak Kustom (Berdasarkan Filter Khusus) -->
        <form action="{{ route('bk.counseling.recap.print') }}" method="GET" target="_blank" class="space-y-4 pt-2">
            <div class="pb-1 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs font-bold text-slate-900 dark:text-white">2. Cetak Kustom (Filter Khusus)</h4>
                <p class="text-[11px] text-slate-400">Pilih rentang tanggal, rombel, bidang layanan, atau peminatan karier</p>
            </div>

            <!-- Rentang Tanggal -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                </div>
            </div>

            <!-- Kelas & Bidang Layanan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Kelas</label>
                    <select name="class_id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bidang Layanan</label>
                    <select name="field_of_service" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">Semua Bidang</option>
                        <option value="KARIER">Bimbingan Karier</option>
                        <option value="BELAJAR">Bimbingan Belajar</option>
                        <option value="PRIBADI">Bimbingan Pribadi</option>
                        <option value="SOSIAL">Bimbingan Sosial</option>
                    </select>
                </div>
            </div>

            <!-- Arah Karier & Format Kertas -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Khusus Peminatan Karier</label>
                    <select name="career_path" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="">Semua (Kuliah & Kerja)</option>
                        <option value="KULIAH">🏛️ Rencana Kuliah</option>
                        <option value="BEKERJA">💼 Rencana Bekerja</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ukuran Kertas Dokumen</label>
                    <select name="paper_size" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 py-2 px-3">
                        <option value="F4" selected>F4 / Folio (215 × 330 mm)</option>
                        <option value="A4">A4 Standar (210 × 297 mm)</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeRecapModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Buka & Cetak Rekap Kustom</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRecapModal() {
    document.getElementById('modal-recap-print').classList.remove('hidden');
}
function closeRecapModal() {
    document.getElementById('modal-recap-print').classList.add('hidden');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRecapModal();
});
</script>
@endsection

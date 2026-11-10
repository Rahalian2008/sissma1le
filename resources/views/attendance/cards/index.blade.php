@extends('layouts.app')

@section('title', 'Cetak Kartu Absensi Siswa (RFID & QR Code)')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold mb-3">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>IDENTITAS & PRESENSI DIGITAL</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Kartu Presensi Siswa Terpadu</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                Generate dan cetak kartu absensi resmi standar kartu nama/KTP (CR80) dengan pilihan <strong>QR Code (otomatis dari NISN)</strong>, <strong>Chip RFID</strong>, atau <strong>Kombinasi Keduanya</strong>.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap gap-2.5 shrink-0">
            <a href="{{ route('attendance.cards.print', ['class_id' => $classId, 'mode' => $mode, 'side' => 'both']) }}" target="_blank"
                class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-emerald-600 hover:from-indigo-500 hover:to-emerald-500 text-white font-bold rounded-2xl text-xs shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Lembar Kartu ({{ ($selectedClass ?? null) ? $selectedClass->name : 'Sesuai Filter' }})</span>
            </a>
            <a href="{{ route('attendance.qr.scanner') }}"
                class="px-4 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-2xl text-xs border border-slate-700 transition flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Buka Scanner Kamera</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">Total Siswa Aktif</div>
                <div class="text-lg font-black text-slate-900 dark:text-white">{{ number_format($stats['total_students']) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">QR Code Siap Digenerate (NISN)</div>
                <div class="text-lg font-black text-sky-600 dark:text-sky-400">{{ number_format($stats['with_nisn']) }} Siswa (100%)</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div>
                <div class="text-[11px] font-semibold text-slate-500">Kartu RFID Terdaftar</div>
                <div class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ number_format($stats['with_rfid']) }} Siswa</div>
            </div>
        </div>
    </div>

    <!-- Filter & Configuration Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <form method="GET" action="{{ route('attendance.cards.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Filter Rombel / Kelas -->
            @php
                $isWaliKelasUser = (Auth::user()->hasRole('wali_kelas') || Auth::user()->role === 'wali_kelas') && !Auth::user()->hasAnyRole(['admin', 'super_admin']);
            @endphp
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilihan Rombel Kelas:</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-white outline-none focus:border-indigo-500">
                    @if(!$isWaliKelasUser)
                        <option value="">Semua Kelas ({{ $stats['total_students'] }} Siswa)</option>
                    @endif
                    @forelse($classes as $c)
                        <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                            Kelas {{ $c->name }} ({{ $c->students_count ?? $c->students()->count() }} Siswa)
                        </option>
                    @empty
                        <option value="">Belum ada kelas binaan</option>
                    @endforelse
                </select>
            </div>

            <!-- Mode Kartu -->
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilihan Tipe Kartu Presensi:</label>
                <div class="grid grid-cols-3 gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                    <button type="button" onclick="setCardMode('combo')" id="btn-mode-combo"
                        class="px-2 py-1.5 rounded-lg text-xs font-bold transition text-center {{ $mode === 'combo' ? 'bg-white dark:bg-slate-700 shadow-xs text-indigo-600 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400' }}">
                        Kombinasi
                    </button>
                    <button type="button" onclick="setCardMode('qr')" id="btn-mode-qr"
                        class="px-2 py-1.5 rounded-lg text-xs font-bold transition text-center {{ $mode === 'qr' ? 'bg-white dark:bg-slate-700 shadow-xs text-sky-600 dark:text-sky-300' : 'text-slate-600 dark:text-slate-400' }}">
                        QR Code
                    </button>
                    <button type="button" onclick="setCardMode('rfid')" id="btn-mode-rfid"
                        class="px-2 py-1.5 rounded-lg text-xs font-bold transition text-center {{ $mode === 'rfid' ? 'bg-white dark:bg-slate-700 shadow-xs text-indigo-600 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400' }}">
                        RFID
                    </button>
                </div>
                <input type="hidden" name="mode" id="input-mode" value="{{ $mode }}">
            </div>

            <!-- Cari Siswa -->
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Cari Nama / Inisial / NISN / UID:</label>
                <div class="flex gap-2">
                    <input type="text" name="search" id="cardSearchInput" value="{{ $search }}" placeholder="Ketik nama, inisial, atau NISN..."
                        class="flex-1 px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition">
                        Cari
                    </button>
                    @if($search || $classId)
                        <a href="{{ route('attendance.cards.index') }}" class="px-3 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold flex items-center">
                            Reset
                        </a>
                    @endif
                </div>

                <!-- Quick Alphabet Initials Filter Bar (A-Z Chips) -->
                <div class="mt-2 flex items-center gap-1 overflow-x-auto pb-1 pt-0.5 text-xs no-scrollbar">
                    <span class="text-[10px] font-bold text-slate-400 shrink-0 mr-1">Inisial:</span>
                    <button type="button" onclick="filterCardByInitial('')"
                        class="px-2 py-0.5 rounded-lg {{ empty($search) ? 'bg-indigo-600 text-white font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700' }} text-[11px] shrink-0 transition shadow-xs">
                        Semua
                    </button>
                    @foreach(range('A', 'Z') as $char)
                        <button type="button" onclick="filterCardByInitial('{{ $char }}')"
                            class="px-2 py-0.5 rounded-lg {{ strtoupper($search ?? '') === $char ? 'bg-indigo-600 text-white font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700' }} text-[11px] shrink-0 transition">
                            {{ $char }}
                        </button>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <!-- Student Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($students as $st)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Mini Card Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <span class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
                            {{ $st->schoolClass?->name ?? 'Tanpa Kelas' }}
                        </span>
                        <span class="text-[10px] font-mono text-slate-400">
                            NISN: {{ $st->nisn }}
                        </span>
                    </div>

                    <!-- Student Info & Avatar -->
                    <div class="flex items-center gap-3 mt-3">
                        <div class="w-12 h-14 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0 flex items-center justify-center font-bold text-slate-400 text-xs">
                            @if(!empty($st->photo_path))
                                <img src="{{ asset('storage/'.$st->photo_path) }}" class="w-full h-full object-cover" alt="{{ $st->name }}">
                            @else
                                <span class="text-indigo-600 dark:text-indigo-400">{{ strtoupper(substr($st->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xs font-black text-slate-900 dark:text-white truncate" title="{{ $st->name }}">{{ $st->name }}</h3>
                            <p class="text-[11px] text-slate-500 font-mono mt-0.5">NIS: {{ $st->nis }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold {{ $st->gender === 'L' ? 'bg-sky-50 text-sky-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $st->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                </span>
                                @if(!empty($st->rfid_uid))
                                    <span class="text-[9.5px] text-emerald-600 font-bold flex items-center gap-0.5" title="UID: {{ $st->rfid_uid }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> RFID
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <a href="{{ route('attendance.cards.single', ['student' => $st->id, 'mode' => $mode, 'side' => 'both']) }}" target="_blank"
                        class="flex-1 py-2 px-3 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold rounded-xl text-xs text-center transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Lihat & Cetak</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-800">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak ada data siswa ditemukan</h3>
                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan pilihan kelas atau kata kunci pencarian Anda.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
        <div class="pt-4">
            {{ $students->links() }}
        </div>
    @endif
</div>

<script>
function setCardMode(m) {
    document.getElementById('input-mode').value = m;
    document.getElementById('btn-mode-combo').className = m === 'combo' ? 'px-2 py-1.5 rounded-lg text-xs font-bold transition text-center bg-white dark:bg-slate-700 shadow-xs text-indigo-600 dark:text-indigo-300' : 'px-2 py-1.5 rounded-lg text-xs font-bold transition text-center text-slate-600 dark:text-slate-400';
    document.getElementById('btn-mode-qr').className = m === 'qr' ? 'px-2 py-1.5 rounded-lg text-xs font-bold transition text-center bg-white dark:bg-slate-700 shadow-xs text-sky-600 dark:text-sky-300' : 'px-2 py-1.5 rounded-lg text-xs font-bold transition text-center text-slate-600 dark:text-slate-400';
    document.getElementById('btn-mode-rfid').className = m === 'rfid' ? 'px-2 py-1.5 rounded-lg text-xs font-bold transition text-center bg-white dark:bg-slate-700 shadow-xs text-indigo-600 dark:text-indigo-300' : 'px-2 py-1.5 rounded-lg text-xs font-bold transition text-center text-slate-600 dark:text-slate-400';
    document.forms[0].submit();
}

function filterCardByInitial(letter) {
    const input = document.getElementById('cardSearchInput');
    if (input) {
        input.value = letter;
        document.forms[0].submit();
    }
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'Jadwal Gelombang Pendaftaran SPMB - SMA Negeri 1 Lengkong')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Gelombang SPMB -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-teal-700 via-emerald-800 to-slate-900 p-6 sm:p-8 text-white shadow-xl shadow-teal-500/10">
        <div class="absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('spmb.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md text-xs font-semibold text-teal-100 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Dashboard SPMB</span>
                    </a>
                    <a href="{{ route('spmb.tracks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md text-xs font-semibold text-teal-100 transition-all">
                        <span>🎯 Jalur Seleksi</span>
                    </a>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-400/25 text-emerald-200 text-xs font-bold border border-emerald-300/30">
                        <span>🗓️ Multi-Gelombang & Jadwal</span>
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Jadwal Gelombang Pendaftaran & Daya Tampung</h1>
                <p class="text-teal-100 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed">
                    Atur jadwal periode pendaftaran per gelombang (Gelombang 1, 2, dst.), tentukan jalur pendaftaran yang dibuka pada setiap gelombang, aktifkan/nonaktifkan status, serta kelola kuota daya tampung sekolah secara fleksibel.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="openCapacityModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Daya Tampung ({{ number_format($totalCapacity) }})</span>
                </button>

                <a href="{{ route('spmb.public.register') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition-all">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Formulir Pendaftar ↗</span>
                </a>

                <button type="button" onclick="openCreateWaveModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-400 to-teal-300 hover:from-emerald-300 hover:to-teal-200 text-slate-950 font-black text-xs shadow-lg transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Buat Gelombang Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistik Ringkasan Gelombang & Kapasitas -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Gelombang</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ count($waves) }} <span class="text-xs font-normal text-slate-400">Tahap</span></div>
            <div class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold mt-0.5">{{ $waves->where('is_active', true)->count() }} Gelombang Aktif</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sedang Dibuka</span>
            @php
                $currentlyOpenCount = $waves->filter(fn($w) => $w->isOpen())->count();
            @endphp
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                {{ $currentlyOpenCount }} <span class="text-xs font-normal text-slate-400">Gelombang</span>
            </div>
            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Pendaftaran Online Aktif</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Daya Tampung Total</span>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1 flex items-center justify-between">
                <span>{{ number_format($totalCapacity) }} <span class="text-xs font-normal text-slate-400">Kursi</span></span>
                <button type="button" onclick="openCapacityModal()" class="text-xs px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold hover:underline cursor-pointer">Ubah</button>
            </div>
            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Seluruh Rombel Kelas X SMA</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Pendaftar</span>
            @php
                $totalRegs = $waves->sum('registrations_count');
            @endphp
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalRegs) }} <span class="text-xs font-normal text-slate-400">Calon Siswa</span></div>
            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">Tersebar di seluruh gelombang</div>
        </div>
    </div>

    <!-- Daftar Gelombang Pendaftaran -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>🗓️</span>
                <span>Daftar Jadwal Gelombang Pendaftaran SPMB</span>
            </h2>
            <span class="text-xs text-slate-500">Pendaftar dapat memilih jalur sesuai gelombang yang aktif</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($waves as $w)
                @php
                    $isOpen = $w->isOpen();
                    $isUpcoming = $w->isUpcoming();
                    $isPast = $w->isPast();
                    $allowedTracks = $w->allowedTracksCollection();
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-3xl border {{ $w->is_active ? ($isOpen ? 'border-emerald-300 dark:border-emerald-700 shadow-sm ring-1 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-800 shadow-2xs') : 'border-dashed border-slate-300 dark:border-slate-800 opacity-65' }} p-6 flex flex-col justify-between transition-all hover:shadow-md">
                    <div>
                        <!-- Header Gelombang -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-10 h-10 rounded-2xl bg-teal-100 dark:bg-teal-950/80 text-teal-800 dark:text-teal-200 font-black text-sm flex items-center justify-center shrink-0">
                                    G-{{ $w->wave_number }}
                                </span>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base leading-tight">
                                        {{ $w->name }}
                                    </h3>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Tahun Ajaran: <strong>{{ $w->academic_year }}</strong></p>
                                </div>
                            </div>

                            <!-- Badge Status Keaktifan -->
                            <div class="flex flex-col items-end gap-1">
                                @if(!$w->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        🔒 Nonaktif
                                    </span>
                                @elseif($isOpen)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-700 animate-pulse">
                                        🟢 Pendaftaran Dibuka
                                    </span>
                                @elseif($isUpcoming)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-700">
                                        🟡 Segera Dibuka
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-700">
                                        🔴 Pendaftaran Ditutup
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Deskripsi Gelombang -->
                        @if($w->description)
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                {{ $w->description }}
                            </p>
                        @endif

                        <!-- Jadwal Detail Tanggal -->
                        <div class="space-y-2 mb-4 text-xs">
                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                                <span class="font-semibold text-slate-500">📅 Periode Pendaftaran:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($w->start_date)->translatedFormat('d M Y') }} s.d. {{ \Carbon\Carbon::parse($w->end_date)->translatedFormat('d M Y') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                                <span class="font-semibold text-slate-500">📢 Pengumuman Hasil:</span>
                                <span class="font-bold text-indigo-700 dark:text-indigo-300">
                                    {{ $w->announcement_date ? \Carbon\Carbon::parse($w->announcement_date)->translatedFormat('d M Y') : '-' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800/40">
                                <span class="font-semibold text-slate-500">📝 Jadwal Daftar Ulang:</span>
                                <span class="font-bold text-emerald-700 dark:text-emerald-300">
                                    @if($w->re_registration_start && $w->re_registration_end)
                                        {{ \Carbon\Carbon::parse($w->re_registration_start)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($w->re_registration_end)->translatedFormat('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Kuota & Pendaftar Realtime -->
                        <div class="mb-4 p-3.5 rounded-2xl bg-teal-50/60 dark:bg-teal-950/40 border border-teal-200/60 dark:border-teal-800/60">
                            <div class="flex items-center justify-between mb-1.5 text-xs">
                                <span class="font-bold text-teal-950 dark:text-teal-200">Kuota Gelombang:</span>
                                <span class="font-black text-teal-800 dark:text-teal-300">
                                    {{ $w->registrations_count }} / {{ number_format($w->quota_seats) }} Calon Siswa
                                </span>
                            </div>
                            @php
                                $fillPercent = $w->quota_seats > 0 ? min(100, round(($w->registrations_count / $w->quota_seats) * 100, 1)) : 0;
                            @endphp
                            <div class="w-full bg-teal-200 dark:bg-teal-900 h-2 rounded-full overflow-hidden">
                                <div class="bg-teal-600 h-full rounded-full transition-all duration-300" style="width: {{ $fillPercent }}%"></div>
                            </div>
                        </div>

                        <!-- Jalur yang Dibuka Pada Gelombang Ini -->
                        <div class="mb-4">
                            <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                                Jalur Pendaftaran yang Dibuka ({{ count($allowedTracks) }} Jalur):
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($allowedTracks as $tr)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800">
                                        @if($tr->is_tka)
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        @endif
                                        <span>{{ $tr->name }}</span>
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">Semua jalur seleksi aktif dapat dipilih</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Footer Kartu: Switch Toggle & Tombol Aksi -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 flex-wrap">
                        <!-- 1-Click Status Switch -->
                        <form action="{{ route('spmb.waves.toggle-status', $w->id) }}" method="POST" class="inline m-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer {{ $w->is_active ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 border border-slate-200 dark:border-slate-700' }}"
                                title="Klik untuk beralih status Aktif / Nonaktif">
                                <span>{{ $w->is_active ? '✓ Status: Aktif' : '✕ Status: Nonaktif' }}</span>
                            </button>
                        </form>

                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="openEditWaveModal({{ json_encode($w) }})" class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold text-xs border border-indigo-200 dark:border-indigo-800 transition cursor-pointer">
                                ✏️ Edit
                            </button>

                            <form action="{{ route('spmb.waves.destroy', $w->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Hapus gelombang {{ $w->name }}? Jika sudah memiliki pendaftar, gelombang akan dinonaktifkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950 font-bold text-xs transition cursor-pointer">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
                    <span class="text-4xl block mb-2">🗓️</span>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">Belum Ada Gelombang Pendaftaran</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Buat jadwal gelombang pertama (Gelombang 1) agar pendaftar dapat memilih jalur seleksi yang dibuka.</p>
                    <button type="button" onclick="openCreateWaveModal()" class="mt-4 px-5 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs transition cursor-pointer">
                        + Buat Gelombang Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Buat Gelombang Baru -->
<div id="modal-wave-create" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-start justify-center p-2 sm:p-4 py-4 sm:py-8 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[85vh] sm:max-h-[88vh] overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-base">Tambah Gelombang Pendaftaran SPMB</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tentukan nama gelombang, periode pendaftaran, dan jalur yang dibuka</p>
            </div>
            <button type="button" onclick="closeCreateWaveModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('spmb.waves.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
            @csrf
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 min-h-0 space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Gelombang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Gelombang 1 - Jalur Prestasi, Afirmasi & Nilai TKA" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none focus:bg-white focus:border-teal-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Gelombang Ke- <span class="text-rose-500">*</span></label>
                        <input type="number" name="wave_number" value="{{ count($waves) + 1 }}" min="1" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Ajaran <span class="text-rose-500">*</span></label>
                        <input type="text" name="academic_year" value="2026/2027" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Daya Tampung (Kursi) <span class="text-rose-500">*</span></label>
                        <input type="number" name="quota_seats" value="180" min="1" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai Pendaftaran <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai Pendaftaran <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pengumuman Kelulusan</label>
                        <input type="date" name="announcement_date" value="{{ date('Y-m-d', strtotime('+17 days')) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mulai Daftar Ulang</label>
                        <input type="date" name="re_registration_start" value="{{ date('Y-m-d', strtotime('+18 days')) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Selesai Daftar Ulang</label>
                        <input type="date" name="re_registration_end" value="{{ date('Y-m-d', strtotime('+20 days')) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                </div>

                <!-- Pilihan Jalur Pendaftaran per Gelombang -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-slate-800 dark:text-white">Pilih Jalur Pendaftaran yang Dibuka pada Gelombang Ini:</label>
                        <button type="button" onclick="selectAllTracks('create')" class="text-[11px] font-bold text-teal-600 hover:underline">Pilih Semua</button>
                    </div>
                    <p class="text-[11px] text-slate-500">Pendaftar pada gelombang ini hanya dapat memilih jalur yang dicentang di bawah ini.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 max-h-48 overflow-y-auto">
                        @foreach($tracks as $t)
                            <label class="flex items-center gap-2.5 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/60 cursor-pointer hover:border-teal-400 transition">
                                <input type="checkbox" name="allowed_tracks[]" value="{{ $t->id }}" class="create-wave-track-cb rounded text-teal-600 focus:ring-teal-500 w-4 h-4" checked>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $t->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $t->code }} • {{ $t->is_tka ? 'Wajib Nilai TKA' : 'Non-TKA' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan Tambahan (Opsional)</label>
                    <textarea name="description" rows="2" placeholder="Catatan panduan pendaftaran untuk gelombang ini..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 cursor-pointer">
                    <label for="create_is_active" class="font-semibold text-slate-700 dark:text-slate-300 text-xs cursor-pointer">
                        Aktifkan gelombang pendaftaran ini sekarang
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0 sticky bottom-0">
                <button type="button" onclick="closeCreateWaveModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Gelombang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Gelombang -->
<div id="modal-wave-edit" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-start justify-center p-2 sm:p-4 py-4 sm:py-8 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[85vh] sm:max-h-[88vh] overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-base">Edit Gelombang Pendaftaran SPMB</h3>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui jadwal, daya tampung, atau jalur seleksi yang dibuka</p>
            </div>
            <button type="button" onclick="closeEditWaveModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                ✕
            </button>
        </div>

        <form id="form-edit-wave" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
            @csrf
            @method('PUT')
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 min-h-0 space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Gelombang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_wave_name" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none focus:bg-white focus:border-teal-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Gelombang Ke- <span class="text-rose-500">*</span></label>
                        <input type="number" name="wave_number" id="edit_wave_number" min="1" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Ajaran <span class="text-rose-500">*</span></label>
                        <input type="text" name="academic_year" id="edit_academic_year" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Daya Tampung (Kursi) <span class="text-rose-500">*</span></label>
                        <input type="number" name="quota_seats" id="edit_quota_seats" min="1" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai Pendaftaran <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" id="edit_start_date" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai Pendaftaran <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" id="edit_end_date" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pengumuman Kelulusan</label>
                        <input type="date" name="announcement_date" id="edit_announcement_date" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mulai Daftar Ulang</label>
                        <input type="date" name="re_registration_start" id="edit_re_registration_start" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Selesai Daftar Ulang</label>
                        <input type="date" name="re_registration_end" id="edit_re_registration_end" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-semibold outline-none">
                    </div>
                </div>

                <!-- Pilihan Jalur Pendaftaran per Gelombang -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-slate-800 dark:text-white">Pilih Jalur Pendaftaran yang Dibuka pada Gelombang Ini:</label>
                        <button type="button" onclick="selectAllTracks('edit')" class="text-[11px] font-bold text-teal-600 hover:underline">Pilih Semua</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 max-h-48 overflow-y-auto">
                        @foreach($tracks as $t)
                            <label class="flex items-center gap-2.5 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/60 cursor-pointer hover:border-teal-400 transition">
                                <input type="checkbox" name="allowed_tracks[]" value="{{ $t->id }}" id="edit_track_{{ $t->id }}" class="edit-wave-track-cb rounded text-teal-600 focus:ring-teal-500 w-4 h-4">
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $t->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $t->code }} • {{ $t->is_tka ? 'Wajib Nilai TKA' : 'Non-TKA' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan Tambahan (Opsional)</label>
                    <textarea name="description" id="edit_wave_description" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 cursor-pointer">
                    <label for="edit_is_active" class="font-semibold text-slate-700 dark:text-slate-300 text-xs cursor-pointer">
                        Gelombang ini berstatus aktif
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0 sticky bottom-0">
                <button type="button" onclick="closeEditWaveModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Daya Tampung Total Sekolah -->
<div id="modal-capacity-edit" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-start justify-center p-2 sm:p-4 py-4 sm:py-8 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[85vh] sm:max-h-[88vh] overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between p-5 pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-base">Edit Daya Tampung Sekolah (Kapasitas SPMB)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Ubah total kuota penerimaan murid baru SMA Negeri 1 Lengkong</p>
            </div>
            <button type="button" onclick="closeCapacityModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('spmb.capacity.update') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
            @csrf
            <div class="p-5 overflow-y-auto flex-1 min-h-0 space-y-4 text-xs">
                <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-900 dark:text-amber-200">
                    <p class="font-bold">Standar Rombel Jenjang SMA:</p>
                    <p class="text-[11px] mt-0.5">Maksimal 36 siswa per rombel. Contoh: 10 Rombel = 360 Kursi, 9 Rombel = 324 Kursi, 8 Rombel = 288 Kursi.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Daya Tampung Total Sekolah (Kursi) <span class="text-rose-500">*</span></label>
                    <input type="number" name="total_capacity" id="input-total-capacity" value="{{ $totalCapacity }}" min="10" max="2000" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-black text-lg text-indigo-700 dark:text-indigo-300 outline-none focus:bg-white focus:border-indigo-500">
                </div>

                <div class="space-y-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300">Penetapan Kuota per Jalur Seleksi (Kursi):</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto p-1">
                        @foreach($tracks as $tr)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-slate-200">{{ $tr->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $tr->code }} (Alokasi: {{ $tr->quota_percentage }}%)</p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <input type="number" name="track_quotas[{{ $tr->id }}]" value="{{ $tr->quota_seats }}" min="0"
                                        class="w-20 px-2.5 py-1 text-right bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg font-bold text-xs">
                                    <span class="text-slate-400 text-[11px]">Kursi</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0 sticky bottom-0">
                <button type="button" onclick="closeCapacityModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Daya Tampung</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateWaveModal() {
        document.getElementById('modal-wave-create').classList.remove('hidden');
    }

    function closeCreateWaveModal() {
        document.getElementById('modal-wave-create').classList.add('hidden');
    }

    function openEditWaveModal(w) {
        document.getElementById('form-edit-wave').action = "{{ url('spmb/waves') }}/" + w.id;
        document.getElementById('edit_wave_name').value = w.name;
        document.getElementById('edit_wave_number').value = w.wave_number;
        document.getElementById('edit_academic_year').value = w.academic_year;
        document.getElementById('edit_quota_seats').value = w.quota_seats;
        document.getElementById('edit_start_date').value = w.start_date ? w.start_date.substring(0, 10) : '';
        document.getElementById('edit_end_date').value = w.end_date ? w.end_date.substring(0, 10) : '';
        document.getElementById('edit_announcement_date').value = w.announcement_date ? w.announcement_date.substring(0, 10) : '';
        document.getElementById('edit_re_registration_start').value = w.re_registration_start ? w.re_registration_start.substring(0, 10) : '';
        document.getElementById('edit_re_registration_end').value = w.re_registration_end ? w.re_registration_end.substring(0, 10) : '';
        document.getElementById('edit_wave_description').value = w.description || '';
        document.getElementById('edit_is_active').checked = Boolean(w.is_active);

        // Set allowed tracks checkboxes
        const allowed = Array.isArray(w.allowed_tracks) ? w.allowed_tracks.map(Number) : [];
        document.querySelectorAll('.edit-wave-track-cb').forEach(cb => {
            cb.checked = (allowed.length === 0) || allowed.includes(Number(cb.value));
        });

        document.getElementById('modal-wave-edit').classList.remove('hidden');
    }

    function closeEditWaveModal() {
        document.getElementById('modal-wave-edit').classList.add('hidden');
    }

    function openCapacityModal() {
        document.getElementById('modal-capacity-edit').classList.remove('hidden');
    }

    function closeCapacityModal() {
        document.getElementById('modal-capacity-edit').classList.add('hidden');
    }

    function selectAllTracks(prefix) {
        document.querySelectorAll('.' + prefix + '-wave-track-cb').forEach(cb => cb.checked = true);
    }
</script>
@endsection


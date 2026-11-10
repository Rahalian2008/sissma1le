@extends('layouts.app')

@section('title', 'SPMB - Seleksi Penerimaan Murid Baru SMA')

@section('content')
<div class="space-y-6">
    <!-- Header Hero SPMB SMA -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-sky-800 p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10">
        <div class="absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-sky-100 mb-3 border border-white/20">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span>SPMB Jenjang SMA &bull; Regulasi Resmi Kemendikbudristek & Disdik Jabar</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Sistem Penerimaan Murid Baru (SPMB)</h1>
                <p class="text-sky-100 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed">
                    Aplikasi seleksi dan penerimaan siswa baru {{ $school->name ?? 'SMA Negeri 1 Lengkong' }} terintegrasi jalur Zonasi, Afirmasi KETM, Perpindahan Tugas Orang Tua, Prestasi Kejuaraan/Rapor, dan <strong>Jalur Nilai Tes Kemampuan Akademik (TKA)</strong>.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('spmb.waves.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-amber-950 font-black text-xs shadow-md transition-all">
                    <span>🗓️ Jadwal Gelombang</span>
                </a>

                <a href="{{ route('spmb.tracks.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/20 hover:bg-white/30 text-white font-bold text-xs backdrop-blur-md border border-white/30 transition-all">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span>⚙️ Kelola Jalur Seleksi</span>
                </a>

                <a href="{{ route('spmb.public.register') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-black text-xs shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>🌐 Form Web SPMB</span>
                </a>

                <a href="{{ route('spmb.tka.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-amber-950 font-black text-xs shadow-lg shadow-amber-500/20 transition-all">
                    <span>🎯 Portal Jalur Nilai TKA</span>
                    <span class="px-2 py-0.5 rounded-full bg-amber-950/20 text-[11px] font-bold">{{ $countTka }} Peserta</span>
                </a>
                
                <a href="{{ route('spmb.print-recap', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs backdrop-blur-md border border-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap</span>
                </a>

                <a href="{{ route('spmb.export-csv', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ekspor CSV</span>
                </a>

                <button onclick="document.getElementById('modal-spmb-create').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white text-blue-700 hover:bg-blue-50 font-black text-xs shadow-lg transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Daftar Siswa Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alokasi Kuota & Daya Tampung SMA -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs relative group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase">Daya Tampung SMA</span>
                <button type="button" onclick="document.getElementById('modal-edit-capacity').classList.remove('hidden')" title="Edit Daya Tampung" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 font-bold cursor-pointer">
                    ✏️ Edit
                </button>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($totalCapacity) }}</div>
            <div class="text-[11px] text-blue-600 font-semibold mt-0.5">10 Rombel (36 Siswa/Kelas)</div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-blue-600 uppercase">Zonasi (50%)</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-bold">Kuota {{ $quotaZonasi }}</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($countZonasi) }}</div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, round(($countZonasi / max(1, $quotaZonasi)) * 100)) }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-purple-600 uppercase">Afirmasi KETM (15%)</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded bg-purple-100 text-purple-800 font-bold">Kuota {{ $quotaAfirmasi }}</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($countAfirmasi) }}</div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ min(100, round(($countAfirmasi / max(1, $quotaAfirmasi)) * 100)) }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-rose-600 uppercase">Perpindahan Ortu (5%)</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded bg-rose-100 text-rose-800 font-bold">Kuota {{ $quotaPerpindahan }}</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($countPerpindahan) }}</div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-rose-600 h-1.5 rounded-full" style="width: {{ min(100, round(($countPerpindahan / max(1, $quotaPerpindahan)) * 100)) }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-600 uppercase">Prestasi (10%)</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold">Kuota {{ $quotaPrestasi }}</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ number_format($countPrestasi) }}</div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min(100, round(($countPrestasi / max(1, $quotaPrestasi)) * 100)) }}%"></div>
            </div>
        </div>

        <!-- Jalur Khusus Nilai TKA -->
        <div class="bg-amber-50 dark:bg-amber-950/30 border-2 border-amber-300 dark:border-amber-700/60 p-4 rounded-2xl shadow-xs relative">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-black text-amber-800 dark:text-amber-300 uppercase">Jalur Nilai TKA (20%)</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-200 text-amber-900 font-black">Kuota {{ $quotaTka }}</span>
            </div>
            <div class="text-2xl font-black text-amber-900 dark:text-amber-200 mt-1">{{ number_format($countTka) }}</div>
            <div class="w-full bg-amber-200/80 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-amber-600 h-1.5 rounded-full" style="width: {{ min(100, round(($countTka / max(1, $quotaTka)) * 100)) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Filter & Navigasi Tab Jalur SPMB -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Tab Jalur -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('spmb.index') }}" class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ !request('track') ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Semua Jalur ({{ $totalRegistered }})
                </a>
                <a href="{{ route('spmb.index', ['track' => 'Zonasi']) }}" class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('track') === 'Zonasi' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    📍 Zonasi ({{ $countZonasi }})
                </a>
                <a href="{{ route('spmb.index', ['track' => 'TKA']) }}" class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('track') === 'TKA' ? 'bg-amber-500 text-amber-950 shadow-xs' : 'bg-amber-100 text-amber-900 hover:bg-amber-200' }}">
                    🎯 Jalur Nilai TKA ({{ $countTka }})
                </a>
                <a href="{{ route('spmb.index', ['track' => 'Afirmasi KETM']) }}" class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('track') === 'Afirmasi KETM' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    🤝 Afirmasi KETM ({{ $countAfirmasi }})
                </a>
                <a href="{{ route('spmb.index', ['track' => 'Prestasi Nilai Rapor']) }}" class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('track') === 'Prestasi Nilai Rapor' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    🏆 Prestasi Rapor / Kejuaraan ({{ $countPrestasi }})
                </a>
                <a href="{{ route('spmb.index', ['track' => 'Perpindahan Tugas Orang Tua']) }}" class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition {{ request('track') === 'Perpindahan Tugas Orang Tua' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    🏢 Mutasi Ortu ({{ $countPerpindahan }})
                </a>
            </div>

            <form action="{{ route('spmb.index') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
                @if(request('track'))
                    <input type="hidden" name="track" value="{{ request('track') }}">
                @endif
                <select name="wave_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 font-medium">
                    <option value="">-- Semua Gelombang --</option>
                    @foreach(($waves ?? \App\Models\SpmbWave::orderBy('wave_number')->get()) as $w)
                        <option value="{{ $w->id }}" {{ request('wave_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }} {{ $w->is_active ? '(Aktif)' : '(Non-aktif)' }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 font-medium">
                    <option value="">-- Semua Status --</option>
                    <option value="Terdaftar" {{ request('status') === 'Terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                    <option value="Terverifikasi" {{ request('status') === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="Lolos Seleksi" {{ request('status') === 'Lolos Seleksi' ? 'selected' : '' }}>Lolos Seleksi</option>
                    <option value="Cadangan" {{ request('status') === 'Cadangan' ? 'selected' : '' }}>Cadangan</option>
                    <option value="Daftar Ulang" {{ request('status') === 'Daftar Ulang' ? 'selected' : '' }}>Daftar Ulang (Diterima)</option>
                    <option value="Tidak Lolos" {{ request('status') === 'Tidak Lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NISN / No. TKA..." class="px-3 py-2 pl-8 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 text-xs w-56">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">Filter</button>
            </form>
        </div>
    </div>

    <!-- Tabel Data Pendaftar SPMB -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Daftar Calon Peserta Didik Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Memuat seluruh pendaftar SPMB dan penilaian Jalur Nilai TKA</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">Menampilkan {{ $registrations->firstItem() ?? 0 }}-{{ $registrations->lastItem() ?? 0 }} dari {{ $registrations->total() }} pendaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-100 dark:border-slate-800 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">No. Registrasi</th>
                        <th class="py-3.5 px-4">Calon Siswa</th>
                        <th class="py-3.5 px-4">Asal Sekolah</th>
                        <th class="py-3.5 px-4">Jalur Seleksi</th>
                        <th class="py-3.5 px-4">Nilai & Skor TKA</th>
                        <th class="py-3.5 px-4 text-center">Status Seleksi</th>
                        <th class="py-3.5 px-4 text-right">Aksi & Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($registrations as $reg)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                            {{ $reg->registration_number }}
                            <div class="text-[10px] text-slate-400 font-normal">{{ $reg->created_at->format('d/m/Y H:i') }}</div>
                        </td>

                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $reg->full_name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                NISN: {{ $reg->nisn }} &bull; {{ $reg->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </div>
                        </td>

                        <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200">
                            {{ $reg->school_origin ?? '-' }}
                            @if($reg->distance_km)
                                <div class="text-[10px] text-slate-400">Jarak: {{ $reg->distance_km }} KM</div>
                            @endif
                        </td>

                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold border {{ $reg->getTrackBadgeClass() }}">
                                {{ $reg->selection_track }}
                            </span>
                            @if(str_contains($reg->selection_track, 'TKA') && $reg->tka_exam_number)
                                <div class="text-[10px] font-mono font-semibold text-amber-700 dark:text-amber-400 mt-1">
                                    No. TKA: {{ $reg->tka_exam_number }}
                                </div>
                            @endif
                        </td>

                        <td class="py-3 px-4">
                            <div class="font-semibold text-slate-900 dark:text-white">Rapor SMP: <span class="font-mono text-blue-700 dark:text-blue-400">{{ number_format((float) $reg->average_score, 2) }}</span></div>
                            @if(str_contains($reg->selection_track, 'TKA'))
                                @if($reg->tka_weighted_score)
                                    <div class="text-[11px] font-bold text-amber-700 dark:text-amber-400 mt-0.5">
                                        Skor Akhir: {{ number_format((float) $reg->tka_weighted_score, 2) }} 
                                        @if($reg->tka_rank)
                                            <span class="px-1.5 py-0.2 rounded bg-amber-200 text-amber-900 text-[10px]">Rank #{{ $reg->tka_rank }}</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400">TKA Rata-rata: {{ number_format((float) $reg->tka_score_total, 2) }}</div>
                                @else
                                    <div class="text-[11px] text-slate-400 italic mt-0.5">Belum ada skor TKA</div>
                                @endif
                            @endif
                        </td>

                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $reg->getStatusBadgeClass() }}">
                                {{ $reg->status }}
                            </span>
                            @if($reg->assigned_class)
                                <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 mt-1">Rombel: {{ $reg->assigned_class }}</div>
                            @endif
                        </td>

                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('spmb.show', $reg) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold transition" title="Detail Calon Siswa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <a href="{{ route('spmb.print-card', $reg) }}" target="_blank" class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold transition" title="Cetak Kartu Peserta SPMB / TKA">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </a>

                                @if(in_array($reg->status, ['Lolos Seleksi', 'Diterima', 'Daftar Ulang']))
                                    <a href="{{ route('spmb.print-letter', $reg) }}" target="_blank" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold transition" title="Cetak Surat Keputusan Diterima">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                @endif

                                <!-- Quick Action Modal Status Trigger -->
                                <button onclick="openStatusModal('{{ $reg->id }}', '{{ $reg->full_name }}', '{{ $reg->status }}', '{{ $reg->assigned_class }}')" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg text-xs transition cursor-pointer">
                                    Status
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400">
                            Belum ada calon siswa yang terdaftar pada kriteria ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $registrations->links() }}
        </div>
    </div>
</div>

<!-- Modal Pendaftaran Baru SPMB -->
<div id="modal-spmb-create" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Formulir Pendaftaran Siswa Baru (SPMB)</h3>
                <p class="text-xs text-slate-500">Isi data calon siswa secara lengkap sesuai kartu keluarga dan ijazah/SKL.</p>
            </div>
            <button onclick="document.getElementById('modal-spmb-create').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('spmb.store') }}" method="POST" class="space-y-4 mt-4 text-xs">
            @csrf

            <!-- Jalur Seleksi Dinamis / Custom -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase">Pilih Jalur Pendaftaran SPMB *</label>
                    <a href="{{ route('spmb.tracks.index') }}" target="_blank" class="text-[11px] text-blue-600 hover:underline font-semibold">+ Kustomisasi Jalur</a>
                </div>
                <select name="selection_track" id="select-track" onchange="toggleTrackFields(this.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-bold text-sm text-slate-800 dark:text-white" required>
                    @if(isset($tracks) && count($tracks) > 0)
                        @foreach($tracks as $t)
                            <option value="{{ $t->name }}" data-istka="{{ $t->is_tka ? '1' : '0' }}">
                                {{ $t->is_tka ? '🎯' : '📍' }} {{ $t->name }} &bull; Kuota {{ $t->quota_percentage }}% ({{ $t->getCalculatedSeats($totalCapacity) }} Kursi) {{ $t->is_tka ? '[Wajib TKA]' : '' }}
                            </option>
                        @endforeach
                    @else
                        <option value="Tes Kemampuan Akademik (TKA)">🎯 Jalur Nilai Tes Kemampuan Akademik (TKA) - Kuota 20%</option>
                        <option value="Zonasi">📍 Jalur Zonasi (Jarak Tempat Tinggal) - Kuota 50%</option>
                        <option value="Afirmasi KETM">🤝 Jalur Afirmasi KETM / Disabilitas - Kuota 15%</option>
                        <option value="Prestasi Nilai Rapor">🏆 Jalur Prestasi Nilai Rapor SMP - Kuota 10%</option>
                        <option value="Prestasi Kejuaraan">🏅 Jalur Prestasi Kejuaraan / Perlombaan</option>
                        <option value="Perpindahan Tugas Orang Tua">🏢 Jalur Perpindahan Tugas Orang Tua / Anak Guru - Kuota 5%</option>
                    @endif
                </select>
                <p id="track-helper" class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 font-medium">
                    Jalur TKA: Calon siswa akan memperoleh Nomor Peserta Ujian TKA untuk seleksi berbasis tes literasi, numerasi, dan logika sains.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Siswa *</label>
                    <input type="text" name="full_name" placeholder="Sesuai Ijazah / Akta Lahir" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NISN (10 Digit) *</label>
                    <input type="text" name="nisn" placeholder="Nomor Induk Siswa Nasional" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">NIK Siswa (16 Digit)</label>
                    <input type="text" name="nik" placeholder="Sesuai Kartu Keluarga" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin *</label>
                    <select name="gender" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" placeholder="Kota / Kabupaten Lahir" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Asal Sekolah (SMP / MTs) *</label>
                    <input type="text" name="school_origin" placeholder="Contoh: SMP Negeri 1 Sukabumi" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nilai Rata-rata Rapor SMP (Skala 0-100) *</label>
                    <input type="number" step="0.01" min="0" max="100" name="average_score" placeholder="Contoh: 86.50" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-bold" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp / HP Ortu *</label>
                    <input type="text" name="parent_phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs" required>
                </div>
            </div>

            <!-- Field Tambahan Zonasi & Alamat -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Tempat Tinggal</label>
                <textarea name="address" rows="2" placeholder="Nama Jalan, RT/RW, Desa/Kelurahan, Kecamatan" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jarak Tempat Tinggal ke Sekolah (KM)</label>
                    <input type="number" step="0.01" name="distance_km" placeholder="Contoh: 2.45" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No KIP / PIP / KKS (Jalur Afirmasi)</label>
                    <input type="text" name="kip_pip_number" placeholder="Nomor Kartu Indonesia Pintar jika ada" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Khusus Panitia SPMB</label>
                <input type="text" name="notes" placeholder="Catatan berkas, nomor antrian, dsb" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs">
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('modal-spmb-create').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black shadow-md transition">Simpan Pendaftaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Quick Update Status SPMB -->
<div id="modal-spmb-status" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Update Status Seleksi SPMB</h3>
        <p id="status-student-name" class="text-xs text-blue-600 font-semibold mb-4"></p>

        <form id="form-update-status" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PATCH')

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Status Seleksi *</label>
                <select name="status" id="input-status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 font-bold" required>
                    <option value="Terdaftar">Terdaftar (Menunggu Verifikasi Berkas)</option>
                    <option value="Terverifikasi">Terverifikasi (Berkas Lengkap & Valid)</option>
                    <option value="Lolos Seleksi">Lolos Seleksi (Diterima di SMAN 1 Lengkong)</option>
                    <option value="Cadangan">Cadangan (Menunggu Kuota Tersedia)</option>
                    <option value="Daftar Ulang">Daftar Ulang (Otomatis Masuk Siswa Aktif SISSMA1LE)</option>
                    <option value="Tidak Lolos">Tidak Lolos / Gugur</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alokasi Rombel Kelas X (Jika Diterima)</label>
                <select name="assigned_class" id="input-assigned-class" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 font-medium">
                    <option value="">-- Belum Ditentukan --</option>
                    @foreach($availableClasses as $c)
                        <option value="{{ $c->name }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Alasan (Opsional)</label>
                <textarea name="rejection_reason" rows="2" placeholder="Catatan verifikasi berkas atau alasan cadangan..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-spmb-status').classList.add('hidden')" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">Batal</button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-blue-600 text-white font-black shadow-xs">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Daya Tampung Total Sekolah -->
<div id="modal-edit-capacity" class="hidden fixed inset-0 z-50 flex items-start justify-center p-3 sm:p-4 py-6 sm:py-10 bg-slate-950/70 backdrop-blur-xs overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 w-full max-w-md shadow-2xl p-6 space-y-5 my-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="font-black text-slate-900 dark:text-white text-base">✏️ Edit Daya Tampung Total SPMB</h3>
                <p class="text-xs text-slate-500">Kapasitas total kuota penerimaan murid baru</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-capacity').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">✕</button>
        </div>
        <form action="{{ route('spmb.capacity.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                    Total Daya Tampung Sekolah (Kursi) *
                </label>
                <input type="number" name="total_capacity" value="{{ $totalCapacity }}" min="10" max="2000" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm font-bold text-slate-900 dark:text-white">
                <p class="text-[11px] text-slate-400 mt-1">Standar SMA Negeri: 10 Rombel x 36 Siswa = 360 Kursi.</p>
            </div>
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('modal-edit-capacity').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs shadow-md transition cursor-pointer">
                    Simpan Daya Tampung
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatusModal(id, name, status, assignedClass) {
    document.getElementById('status-student-name').innerText = name;
    document.getElementById('input-status').value = status;
    document.getElementById('input-assigned-class').value = assignedClass || '';
    document.getElementById('form-update-status').action = '/spmb/' + id + '/status';
    document.getElementById('modal-spmb-status').classList.remove('hidden');
}

function toggleTrackFields(track) {
    const helper = document.getElementById('track-helper');
    if (track.includes('TKA')) {
        helper.innerText = '🎯 Jalur TKA: Calon siswa akan memperoleh Nomor Peserta Ujian TKA untuk seleksi berbasis tes literasi, numerasi, dan logika sains.';
        helper.className = 'text-[11px] text-amber-600 dark:text-amber-400 mt-1 font-medium';
    } else if (track.includes('Zonasi')) {
        helper.innerText = '📍 Jalur Zonasi: Seleksi diperhitungkan berdasarkan jarak radius domisili ke SMA Negeri 1 Lengkong.';
        helper.className = 'text-[11px] text-blue-600 dark:text-blue-400 mt-1 font-medium';
    } else if (track.includes('Afirmasi')) {
        helper.innerText = '🤝 Jalur Afirmasi: Ditujukan bagi keluarga ekonomi tidak mampu (KETM) pemegang KIP/KKS/PKH atau disabilitas.';
        helper.className = 'text-[11px] text-purple-600 dark:text-purple-400 mt-1 font-medium';
    } else {
        helper.innerText = '📋 Jalur Seleksi sesuai juknis SPMB Dinas Pendidikan Jawa Barat.';
        helper.className = 'text-[11px] text-slate-500 mt-1 font-medium';
    }
}
</script>
@endsection


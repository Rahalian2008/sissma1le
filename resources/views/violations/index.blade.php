@extends('layouts.app')

@section('title', 'Pelanggaran Siswa (P1-P6)')

@section('content')
<div class="space-y-5 w-full max-w-full min-w-0">
    @include('discipline.partials.nav')
    <!-- Header with Guidance Tier Info -->
    <div class="bg-gradient-to-r from-rose-700 via-rose-600 to-slate-900 rounded-2xl p-5 sm:p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 bg-white/20 rounded-full text-[11px] font-bold text-white backdrop-blur-md">
                MODUL TATA TERTIB & PEMBINAAN
            </span>
            <h2 class="text-xl sm:text-2xl font-black text-white mt-1.5">Pelanggaran Tata Tertib Siswa</h2>
            <p class="text-xs text-rose-100 mt-1 max-w-xl leading-relaxed">
                Pencatatan pelanggaran berdasarkan Dokumen Tata Tertib SMAN 1 Lengkong (P1 s/d P6). Poin pelanggaran dicatat terpisah dan digunakan untuk memicu <strong>NOTIFIKASI PERLU TINDAKAN</strong> pembinaan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'guru', 'wali_kelas']) || auth()->user()->isKesiswaanAdmin())
            <button onclick="document.getElementById('importViolationModal').classList.remove('hidden')" class="px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-extrabold rounded-xl shadow-md backdrop-blur-md text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Pelanggaran
            </button>
            @endif
            @if(!in_array(auth()->user()->role, ['siswa', 'orang_tua', 'kepala_sekolah']))
            <a href="{{ route('violations.create') }}" class="px-4 py-2.5 bg-white text-slate-950 hover:bg-slate-100 font-extrabold rounded-xl shadow-md text-xs flex items-center justify-center gap-1.5 transition">
                <span>+</span> Catat Pelanggaran Baru
            </a>
            @endif
        </div>
    </div>

    <!-- Guidance Tiers Reference Banner -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">Ketentuan Tahapan Pembinaan (Tata Tertib Sekolah)</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 text-xs">
            <div class="p-2.5 bg-yellow-50 dark:bg-yellow-950/40 border border-yellow-200 dark:border-yellow-900/50 rounded-xl">
                <span class="font-extrabold text-yellow-900 dark:text-yellow-300 text-xs sm:text-sm">75 – 124 Poin</span>
                <p class="text-yellow-800 dark:text-yellow-400 font-semibold mt-0.5 text-[11px]">Bimbingan Wali Kelas</p>
            </div>
            <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 rounded-xl">
                <span class="font-extrabold text-amber-900 dark:text-amber-300 text-xs sm:text-sm">125 – 175 Poin</span>
                <p class="text-amber-800 dark:text-amber-400 font-semibold mt-0.5 text-[11px]">SP 1 - BK</p>
            </div>
            <div class="p-2.5 bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-900/50 rounded-xl">
                <span class="font-extrabold text-orange-900 dark:text-orange-300 text-xs sm:text-sm">176 – 199 Poin</span>
                <p class="text-orange-800 dark:text-orange-400 font-semibold mt-0.5 text-[11px]">SP 2 - BK</p>
            </div>
            <div class="p-2.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-xl">
                <span class="font-extrabold text-rose-900 dark:text-rose-300 text-xs sm:text-sm">200 Poin</span>
                <p class="text-rose-800 dark:text-rose-400 font-bold mt-0.5 text-[11px]">SP 3 - BK & Kesiswaan</p>
            </div>
            <div class="p-2.5 bg-red-100 dark:bg-red-950/50 border border-red-300 dark:border-red-900/60 rounded-xl col-span-2 sm:col-span-1">
                <span class="font-extrabold text-red-950 dark:text-red-200 text-xs sm:text-sm">> 200 Poin</span>
                <p class="text-red-900 dark:text-red-300 font-bold mt-0.5 text-[11px]">Rapat Khusus Dewan Guru</p>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('violations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 text-xs">
            <div class="sm:col-span-2">
                <label class="block text-slate-500 dark:text-slate-400 font-semibold mb-1">Cari Pelanggaran (Ketik Nama Siswa / Bentuk / NIS)</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama siswa atau bentuk pelanggaran..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-indigo-500 text-xs">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-slate-500 dark:text-slate-400 font-semibold mb-1">Kategori (P1 - P6)</label>
                <select name="category_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->code }} - {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-500 dark:text-slate-400 font-semibold mb-1">Status Verifikasi</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                    <option value="">-- Semua Status --</option>
                    <option value="MENUNGGU_VERIFIKASI" {{ request('status') === 'MENUNGGU_VERIFIKASI' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="DIVERIFIKASI" {{ request('status') === 'DIVERIFIKASI' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            <div>
                <label class="block text-slate-500 dark:text-slate-400 font-semibold mb-1">Kelas</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Binaan' }}" class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 cursor-not-allowed" readonly>
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            @endif
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition">Filter</button>
                <a href="{{ route('violations.index') }}" class="py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-center">Reset</a>
            </div>
        </form>
    </div>

    <!-- Violations Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden max-w-full">
        <div class="overflow-x-auto max-w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/95 dark:bg-slate-800/95 sticky top-0 z-10 backdrop-blur-xs border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-3 whitespace-nowrap w-24">Tanggal & Kode</th>
                        <th class="py-3 px-3 min-w-[130px] max-w-[170px]">Nama Siswa / Kelas</th>
                        <th class="py-3 px-3 min-w-[160px] max-w-xs md:max-w-sm">Kategori & Bentuk Pelanggaran</th>
                        <th class="py-3 px-2 text-center whitespace-nowrap w-20">Lokasi</th>
                        <th class="py-3 px-2 text-center whitespace-nowrap w-16">Poin</th>
                        <th class="py-3 px-2 text-center whitespace-nowrap w-24">Status</th>
                        <th class="py-3 px-2 text-center whitespace-nowrap w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($violations as $viol)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $viol->date->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $viol->violation_code }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-900 dark:text-white truncate max-w-[160px]" title="{{ $viol->student->name }}">{{ $viol->student->name }}</div>
                                <div class="text-[10.5px] text-slate-400 dark:text-slate-400 truncate max-w-[160px]">Kelas {{ $viol->schoolClass?->name ?? '-' }} • NIS: {{ $viol->student->nis }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="flex items-start gap-1.5 max-w-xs md:max-w-sm">
                                    <span class="px-1.5 py-0.5 rounded bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-extrabold text-[10px] shrink-0 mt-0.5">
                                        {{ $viol->category->code }}
                                    </span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-100 leading-snug line-clamp-2" title="{{ $viol->title }}">{{ $viol->title }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap text-slate-600 dark:text-slate-400 text-[11px]">
                                {{ $viol->location ?? '-' }}
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <span class="font-black text-rose-600 dark:text-rose-400 text-xs sm:text-sm">{{ $viol->points }} P</span>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $viol->status === 'DIVERIFIKASI' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' : ($viol->status === 'MENUNGGU_VERIFIKASI' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 animate-pulse' : 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300') }}">
                                    {{ str_replace('_', ' ', $viol->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('violations.show', $viol->id) }}" class="w-7 h-7 bg-slate-900 hover:bg-rose-600 dark:bg-slate-800 dark:hover:bg-rose-600 text-white rounded-lg transition inline-flex items-center justify-center shadow-2xs" title="Lihat Detail">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    @php
                                        $curActiveRole = session('active_role') ?? auth()->user()->active_role;
                                        $canManageViol = in_array($curActiveRole, ['admin', 'super_admin', 'wakasek_kesiswaan', 'kesiswaan'], true)
                                            || ($curActiveRole === 'all_roles' && auth()->user()->hasAnyRole(['admin', 'super_admin', 'wakasek_kesiswaan', 'kesiswaan']));
                                    @endphp
                                    @if($canManageViol)
                                        @if($viol->status === 'MENUNGGU_VERIFIKASI')
                                            <form action="{{ route('violations.verify', $viol->id) }}" method="POST" class="inline" onsubmit="return confirm('Setujui (Approve) verifikasi pelanggaran: {{ addslashes($viol->title) }} ({{ $viol->points }} Poin) untuk siswa {{ addslashes($viol->student->name) }}?');">
                                                @csrf
                                                <input type="hidden" name="status" value="DIVERIFIKASI">
                                                <button type="submit" class="w-7 h-7 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition inline-flex items-center justify-center shadow-2xs cursor-pointer" title="Langsung Approve / Setujui">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                            <button type="button" onclick="openRejectViolationModal({{ $viol->id }}, '{{ addslashes($viol->title) }}', '{{ addslashes($viol->student->name) }}')" class="w-7 h-7 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition inline-flex items-center justify-center shadow-2xs cursor-pointer" title="Tolak Laporan Pelanggaran">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @else
                                            <button type="button" onclick="openStatusViolationModal({{ $viol->id }}, '{{ $viol->status }}', '{{ addslashes($viol->title) }}', '{{ addslashes($viol->student->name) }}')" class="w-7 h-7 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 rounded-lg transition inline-flex items-center justify-center cursor-pointer shadow-2xs" title="Ubah Status Verifikasi">
                                                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </button>
                                        @endif

                                        <a href="{{ route('violations.edit', $viol->id) }}" class="w-7 h-7 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition inline-flex items-center justify-center shadow-2xs" title="Edit Data">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('violations.destroy', $viol->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data pelanggaran ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-7 h-7 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition inline-flex items-center justify-center cursor-pointer shadow-2xs" title="Hapus Data">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400 dark:text-slate-500">Belum ada data pelanggaran yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($violations->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $violations->links() }}
            </div>
        @endif
    </div>
</div>
<!-- Modal Impor Pelanggaran Siswa -->
<div id="importViolationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 dark:text-white text-sm">Impor Massal Pelanggaran Siswa</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-400">Unggah file CSV catatan pelanggaran tata tertib siswa</p>
                </div>
            </div>
            <button onclick="document.getElementById('importViolationModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700 dark:text-slate-200">
                <span>Format File & Panduan:</span>
                <a href="{{ route('violations.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                Kolom: <code class="bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-rose-700 dark:text-rose-400 font-mono">nis_siswa</code> (NIS terdaftar), <code class="bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-rose-700 dark:text-rose-400 font-mono">kode_pelanggaran</code> (misal P1.1), <code class="bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-rose-700 dark:text-rose-400 font-mono">judul_pelanggaran</code>, <code class="bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">poin</code>, <code class="bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">tingkat</code>, <code class="bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">tanggal</code> (YYYY-MM-DD), <code class="bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">keterangan</code>.
            </p>
        </div>

        <form action="{{ route('violations.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-600 file:text-white hover:file:bg-rose-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('importViolationModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Laporan Pelanggaran -->
<div id="rejectViolationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-black text-slate-800 dark:text-white text-sm">Tolak Catatan Pelanggaran</h3>
            <button type="button" onclick="closeRejectViolationModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold">✕</button>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400">
            Pelanggaran: <strong id="rejectViolTitle" class="text-slate-800 dark:text-white"></strong><br>
            Siswa: <span id="rejectViolStudent" class="text-slate-700 dark:text-slate-300"></span>
        </p>
        <form id="rejectViolationForm" method="POST" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="status" value="DITOLAK">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" required rows="3" placeholder="Sebutkan alasan penolakan pelanggaran..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none focus:border-rose-500"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeRejectViolationModal()" class="px-4 py-2 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-xs transition">Tolak Pelanggaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Status Pelanggaran -->
<div id="statusViolationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-black text-slate-800 dark:text-white text-sm">Ubah Status Verifikasi Pelanggaran</h3>
            <button type="button" onclick="closeStatusViolationModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold">✕</button>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400">
            Pelanggaran: <strong id="statusViolTitle" class="text-slate-800 dark:text-white"></strong><br>
            Siswa: <span id="statusViolStudent" class="text-slate-700 dark:text-slate-300"></span>
        </p>
        <form id="statusViolationForm" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Status Baru <span class="text-rose-500">*</span></label>
                <select name="status" id="statusViolSelect" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-800 dark:text-slate-100">
                    <option value="DIVERIFIKASI">DIVERIFIKASI (Disetujui)</option>
                    <option value="DITOLAK">DITOLAK</option>
                </select>
            </div>
            <div id="statusViolReasonContainer">
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Alasan Penolakan</label>
                <textarea name="rejection_reason" rows="2" placeholder="Catatan bila ditolak..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeStatusViolationModal()" class="px-4 py-2 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-xs transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectViolationModal(id, title, student) {
        document.getElementById('rejectViolTitle').innerText = title;
        document.getElementById('rejectViolStudent').innerText = student;
        document.getElementById('rejectViolationForm').action = '/violations/' + id + '/verify';
        document.getElementById('rejectViolationModal').classList.remove('hidden');
    }
    function closeRejectViolationModal() {
        document.getElementById('rejectViolationModal').classList.add('hidden');
    }
    function openStatusViolationModal(id, status, title, student) {
        document.getElementById('statusViolTitle').innerText = title;
        document.getElementById('statusViolStudent').innerText = student;
        document.getElementById('statusViolSelect').value = status;
        document.getElementById('statusViolationForm').action = '/violations/' + id + '/verify';
        document.getElementById('statusViolationModal').classList.remove('hidden');
    }
    function closeStatusViolationModal() {
        document.getElementById('statusViolationModal').classList.add('hidden');
    }
</script>
@endsection

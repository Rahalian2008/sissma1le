@extends('layouts.app')

@section('title', 'Pelanggaran Siswa (P1-P6)')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header with Guidance Tier Info -->
    <div class="bg-gradient-to-r from-rose-700 via-rose-600 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold text-white backdrop-blur-md">
                MODUL TATA TERTIB & PEMBINAAN
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">Pelanggaran Tata Tertib Siswa</h2>
            <p class="text-xs text-rose-100 mt-1 max-w-xl leading-relaxed">
                Pencatatan pelanggaran berdasarkan Dokumen Tata Tertib SMAN 1 Lengkong (P1 s/d P6). Poin pelanggaran dicatat terpisah dan digunakan untuk memicu <strong>NOTIFIKASI PERLU TINDAKAN</strong> pembinaan.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'guru', 'wali_kelas']))
            <button onclick="document.getElementById('importViolationModal').classList.remove('hidden')" class="px-5 py-3 bg-white/20 hover:bg-white/30 text-white font-extrabold rounded-2xl shadow-lg backdrop-blur-md text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Pelanggaran
            </button>
            @endif
            @if(!in_array(auth()->user()->role, ['siswa', 'orang_tua', 'kepala_sekolah']))
            <a href="{{ route('violations.create') }}" class="px-5 py-3 bg-white text-slate-950 hover:bg-slate-100 font-extrabold rounded-2xl shadow-lg shadow-black/10 text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <span>+</span> Catat Pelanggaran Baru
            </a>
            @endif
        </div>
    </div>

    <!-- Guidance Tiers Reference Banner -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Ketentuan Tahapan Pembinaan (Tata Tertib Sekolah)</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-2xl">
                <span class="font-extrabold text-yellow-900">75 – 124 Poin</span>
                <p class="text-yellow-800 font-semibold mt-0.5">Bimbingan Wali Kelas</p>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl">
                <span class="font-extrabold text-amber-900">125 – 175 Poin</span>
                <p class="text-amber-800 font-semibold mt-0.5">SP 1 - BK</p>
            </div>
            <div class="p-3 bg-orange-50 border border-orange-200 rounded-2xl">
                <span class="font-extrabold text-orange-900">176 – 199 Poin</span>
                <p class="text-orange-800 font-semibold mt-0.5">SP 2 - BK</p>
            </div>
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl">
                <span class="font-extrabold text-rose-900">200 Poin</span>
                <p class="text-rose-800 font-bold mt-0.5">SP 3 - BK & Kesiswaan</p>
            </div>
            <div class="p-3 bg-red-100 border border-red-300 rounded-2xl">
                <span class="font-extrabold text-red-950">> 200 Poin</span>
                <p class="text-red-900 font-bold mt-0.5">Rapat Khusus Dewan Guru</p>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('violations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div class="sm:col-span-2">
                <label class="block text-slate-500 font-semibold mb-1">Cari Pelanggaran (Ketik Nama Siswa / Bentuk / NIS)</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama siswa atau bentuk pelanggaran..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 text-xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Kategori (P1 - P6)</label>
                <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->code }} - {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Status Verifikasi</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="">-- Semua Status --</option>
                    <option value="MENUNGGU_VERIFIKASI" {{ request('status') === 'MENUNGGU_VERIFIKASI' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="DIVERIFIKASI" {{ request('status') === 'DIVERIFIKASI' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            @if(!in_array(Auth::user()->role, ['siswa', 'orang_tua']))
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Kelas</label>
                @if(Auth::user()->role === 'wali_kelas')
                    <input type="text" value="Kelas {{ $classes->first()?->name ?? 'Binaan' }}" class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed" readonly>
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @else
                    <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
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
                <a href="{{ route('violations.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 rounded-xl text-center">Reset</a>
            </div>
        </form>
    </div>

    <!-- Violations Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal & Kode</th>
                        <th class="py-3.5 px-4">Nama Siswa / Kelas</th>
                        <th class="py-3.5 px-4">Kategori & Bentuk Pelanggaran</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">Poin</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($violations as $viol)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $viol->date->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $viol->violation_code }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $viol->student->name }}</div>
                                <div class="text-[11px] text-slate-400">Kelas {{ $viol->schoolClass?->name ?? '-' }} • NIS: {{ $viol->student->nis }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold text-[10px] mr-1">
                                    {{ $viol->category->code }}
                                </span>
                                <span class="font-bold text-slate-800">{{ $viol->title }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $viol->location ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-black text-rose-600 text-sm">{{ $viol->points }} Poin</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $viol->status === 'DIVERIFIKASI' ? 'bg-emerald-100 text-emerald-800' : ($viol->status === 'MENUNGGU_VERIFIKASI' ? 'bg-amber-100 text-amber-800 animate-pulse' : 'bg-rose-100 text-rose-800') }}">
                                    {{ str_replace('_', ' ', $viol->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('violations.show', $viol->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-rose-600 text-white font-bold rounded-xl text-[11px] transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">Belum ada data pelanggaran yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($violations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $violations->links() }}
            </div>
        @endif
    </div>
</div>
<!-- Modal Impor Pelanggaran Siswa -->
<div id="importViolationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Massal Pelanggaran Siswa</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV catatan pelanggaran tata tertib siswa</p>
                </div>
            </div>
            <button onclick="document.getElementById('importViolationModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('violations.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom: <code class="bg-white px-1.5 py-0.5 rounded text-rose-700 font-mono">nis_siswa</code> (NIS terdaftar), <code class="bg-white px-1.5 py-0.5 rounded text-rose-700 font-mono">kode_pelanggaran</code> (misal P1.1), <code class="bg-white px-1.5 py-0.5 rounded text-rose-700 font-mono">judul_pelanggaran</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">poin</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">tingkat</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">tanggal</code> (YYYY-MM-DD), <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">keterangan</code>.
            </p>
        </div>

        <form action="{{ route('violations.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-600 file:text-white hover:file:bg-rose-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importViolationModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Pelanggaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

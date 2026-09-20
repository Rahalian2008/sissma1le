@extends('layouts.app')

@section('title', 'Catatan Pembinaan Siswa (BK & Wali Kelas)')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold text-white backdrop-blur-md">
                MODUL PEMBINAAN SISWA
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">Catatan Pembinaan & Konseling</h2>
            <p class="text-xs text-purple-200 mt-1 max-w-xl leading-relaxed">
                Dokumentasi pembinaan terpadu oleh Guru BK dan Wali Kelas untuk siswa yang membutuhkan bimbingan khusus atau mencapai ambang peringatan tata tertib.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
            @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'wali_kelas', 'guru']))
            <button onclick="document.getElementById('importGuidanceModal').classList.remove('hidden')" class="px-5 py-3 bg-white/20 hover:bg-white/30 text-white font-extrabold rounded-2xl shadow-lg backdrop-blur-md text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Impor Pembinaan
            </button>
            @endif
            @if(!in_array(auth()->user()->role, ['siswa', 'orang_tua', 'kepala_sekolah']))
            <a href="{{ route('guidance.create') }}" class="px-5 py-3 bg-white text-slate-950 hover:bg-slate-100 font-extrabold rounded-2xl shadow-lg text-xs sm:text-sm flex items-center justify-center gap-2 transition">
                <span>+</span> Tambah Catatan Pembinaan
            </a>
            @endif
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('guidance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Cari Pembinaan (Nama / Rekomendasi)</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama siswa atau catatan..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-purple-500 text-xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Status Penanganan</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="">-- Semua Status --</option>
                    <option value="DALAM_PROSES" {{ request('status') === 'DALAM_PROSES' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                    <option value="PEMANTAUAN_LANJUTAN" {{ request('status') === 'PEMANTAUAN_LANJUTAN' ? 'selected' : '' }}>Pemantauan Lanjutan</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-500 font-semibold mb-1">Tingkat Tindak Lanjut</label>
                <select name="follow_up_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="">-- Semua Jenis Tindakan --</option>
                    <option value="BIMBINGAN_WALI_KELAS">Bimbingan Wali Kelas</option>
                    <option value="SP1_BK">SP 1 - BK</option>
                    <option value="SP2_BK">SP 2 - BK</option>
                    <option value="SP3_BK_KESISWAAN">SP 3 - BK & Kesiswaan</option>
                    <option value="RAPAT_KHUSUS">Rapat Khusus Sekolah</option>
                    <option value="KONSELING_RUTIN">Konseling Rutin</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl transition">Filter</button>
                <a href="{{ route('guidance.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 rounded-xl text-center">Reset</a>
            </div>
        </form>
    </div>

    <!-- Guidance Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Nama Siswa / Kelas</th>
                        <th class="py-3.5 px-4">Tindakan / Tingkat Pembinaan</th>
                        <th class="py-3.5 px-4">Konselor / Pembina</th>
                        <th class="py-3.5 px-4">Rekomendasi & Komitmen</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-slate-500 font-medium whitespace-nowrap">{{ $rec->date->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $rec->student->name }}</div>
                                <div class="text-[11px] text-slate-400">Kelas {{ $rec->student->schoolClass?->name ?? '-' }} • NIS: {{ $rec->student->nis }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-bold text-[10px]">
                                    {{ $rec->followUpLabel() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                {{ $rec->counselor->name ?? 'Konselor' }}
                            </td>
                            <td class="py-3.5 px-4 max-w-xs truncate text-slate-600">
                                {{ $rec->recommendation }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $rec->status === 'SELESAI' ? 'bg-emerald-100 text-emerald-800' : ($rec->status === 'DALAM_PROSES' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ str_replace('_', ' ', $rec->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('guidance.show', $rec->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-purple-600 text-white font-bold rounded-xl text-[11px] transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">Belum ada catatan pembinaan siswa yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
<!-- Modal Impor Catatan Pembinaan -->
<div id="importGuidanceModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Impor Catatan Pembinaan Siswa</h3>
                    <p class="text-[11px] text-slate-400">Unggah file CSV catatan bimbingan konseling siswa</p>
                </div>
            </div>
            <button onclick="document.getElementById('importGuidanceModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-700">
                <span>Format File & Panduan:</span>
                <a href="{{ route('guidance.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-600 hover:bg-purple-500 text-white rounded-lg text-[11px] font-bold shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Kolom: <code class="bg-white px-1.5 py-0.5 rounded text-purple-700 font-mono">nis_siswa</code> (NIS terdaftar), <code class="bg-white px-1.5 py-0.5 rounded text-purple-700 font-mono">tanggal</code> (YYYY-MM-DD), <code class="bg-white px-1.5 py-0.5 rounded text-purple-700 font-mono">tindakan_penanganan</code>, <code class="bg-white px-1.5 py-0.5 rounded text-purple-700 font-mono">kasus_masalah</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">catatan</code>, <code class="bg-white px-1.5 py-0.5 rounded text-slate-600 font-mono">status</code> (DALAM_PROSES/SELESAI/PEMANTAUAN_LANJUTAN).
            </p>
        </div>

        <form action="{{ route('guidance.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Spreadsheet (CSV) <span class="text-rose-500">*</span></label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importGuidanceModal').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Mulai Impor Pembinaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

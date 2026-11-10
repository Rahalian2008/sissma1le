@extends('layouts.app')

@section('title', 'Daftar Struktur Organisasi (DSO)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Daftar Struktur Organisasi (DSO)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Bagan tata kelola manajerial sekolah, garis komando, dan rincian tugas pokok & fungsi (Tupoksi).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dso.print') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Bagan DSO
            </a>
            <button onclick="document.getElementById('modal-dso').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Jabatan Struktur
            </button>
        </div>
    </div>

    <!-- Tree Visual Organization Chart -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <h2 class="text-sm font-bold text-slate-900 mb-6 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Hierarki Manajerial & Garis Koordinasi
        </h2>

        <div class="space-y-6">
            @forelse ($rootNodes as $root)
                <!-- Root Node Card (e.g. Kepala Sekolah) -->
                <div class="border-2 border-indigo-200 bg-indigo-50/50 rounded-2xl p-5 relative">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-lg shadow-md">
                                👑
                            </div>
                            <div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-100 text-indigo-800">
                                    Pimpinan Utama
                                </span>
                                <h3 class="text-base font-bold text-slate-900 mt-0.5">{{ $root->position_name }}</h3>
                                <p class="text-xs font-semibold text-slate-700">{{ $root->officer_name }} @if($root->officer_nip) <span class="text-slate-400 font-normal">(NIP: {{ $root->officer_nip }})</span> @endif</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-mono">{{ $root->sk_number ?? 'SK Definitif' }}</span>
                        </div>
                    </div>

                    @if($root->duties_and_functions)
                        <div class="mt-3 text-xs text-slate-600 bg-white/80 p-3 rounded-xl border border-indigo-100">
                            <strong>Tupoksi:</strong> {{ $root->duties_and_functions }}
                        </div>
                    @endif

                    <!-- Level 2 Children (Wakasek, Ka TU, Komite) -->
                    @if($root->children->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-indigo-200/80 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($root->children as $child)
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs space-y-2 relative hover:border-blue-300 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold text-[10px] rounded-md border border-blue-200">
                                            Wakil / Staf
                                        </span>
                                        <form action="{{ route('dso.destroy', $child) }}" method="POST" onsubmit="return confirm('Hapus jabatan ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-rose-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-xs">{{ $child->position_name }}</h4>
                                    <div class="text-[11px] font-semibold text-slate-700">{{ $child->officer_name }}</div>
                                    @if($child->duties_and_functions)
                                        <p class="text-[11px] text-slate-500 line-clamp-2">{{ $child->duties_and_functions }}</p>
                                    @endif

                                    <!-- Sub-children if any (Level 3) -->
                                    @if($child->children->isNotEmpty())
                                        <div class="pt-2 border-t border-slate-100 space-y-1">
                                            @foreach ($child->children as $sub)
                                                <div class="p-1.5 bg-slate-50 rounded-lg text-[10px]">
                                                    <span class="font-bold text-slate-800">{{ $sub->position_name }}:</span> {{ $sub->officer_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 text-slate-400 text-xs">
                    Belum ada struktur organisasi yang disusun. Klik tombol Tambah Jabatan Struktur di atas.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah Jabatan DSO -->
<div id="modal-dso" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Posisi Jabatan Struktur</h2>
        <p class="text-xs text-slate-500 mb-4">Input posisi bagan organisasi dan atasan langsung.</p>

        <form action="{{ route('dso.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Jabatan Organisasi <span class="text-rose-500">*</span></label>
                <input type="text" name="position_name" placeholder="Contoh: Wakasek Bidang Kurikulum" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Atasan Langsung (Parent Node)</label>
                <select name="parent_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Paling Atas (Pucuk Pimpinan / Kepala Sekolah) --</option>
                    @foreach ($allNodes as $node)
                        <option value="{{ $node->id }}">{{ $node->position_name }} ({{ $node->officer_name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Pejabat dari Pegawai (PTK)</label>
                <select name="employee_id" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih dari Database Pegawai (Atau isi manual di bawah) --</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->formatted_name }} (NIP: {{ $emp->nip ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor SK Pengangkatan</label>
                    <input type="text" name="sk_number" placeholder="800/012/SK/SMA1/2026" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Urutan Tampilan (Sort Order)</label>
                    <input type="number" name="sort_order" value="1" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tugas Pokok & Fungsi (Tupoksi)</label>
                <textarea name="duties_and_functions" rows="2" placeholder="Uraian ringkas kewenangan dan tanggung jawab..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-dso').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Jabatan</button>
            </div>
        </form>
    </div>
</div>
@endsection

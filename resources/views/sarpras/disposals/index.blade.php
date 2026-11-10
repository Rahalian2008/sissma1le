@extends('layouts.app')

@section('title', 'Penghapusan Aset (Afkir BMD)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">Pengelolaan BMD Jabar</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Permendagri No. 19/2016</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Usulan Penghapusan Aset (Afkir BMD)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penatausahaan usulan pemusnahan/penghapusan barang inventaris sekolah yang rusak berat, hilang, atau usang beserta penerbitan Berita Acara (BA).</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('modalAddDisposal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Usulan Penghapusan
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Total Usulan</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Berkas diajukan</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-medium text-amber-600 uppercase tracking-wider">Menunggu Persetujuan</div>
            <div class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($stats['pending']) }}</div>
            <div class="text-[11px] text-amber-500 mt-1">Verifikasi Disdik/Sekolah</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-medium text-emerald-600 uppercase tracking-wider">Disetujui BPKAD/Disdik</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1">{{ number_format($stats['approved']) }}</div>
            <div class="text-[11px] text-emerald-600 mt-1">Aset resmi dihapus/afkir</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-medium text-rose-600 uppercase tracking-wider">Taksiran Nilai Residu</div>
            <div class="text-xl font-bold text-rose-700 mt-1">Rp {{ number_format($stats['total_scrap_value'], 0, ',', '.') }}</div>
            <div class="text-[11px] text-rose-500 mt-1">Taksiran rongsok/besi tua</div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('sarpras.disposals.index') }}" class="flex flex-wrap items-center gap-2.5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. usulan, aset, kode..." class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-rose-500 text-slate-700 w-52">
                
                <select name="status" onchange="this.form.submit()" class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-rose-500 text-slate-700">
                    <option value="">-- Semua Status --</option>
                    <option value="Diusulkan" {{ request('status') === 'Diusulkan' ? 'selected' : '' }}>Diusulkan</option>
                    <option value="Diverifikasi Kepala Sekolah" {{ request('status') === 'Diverifikasi Kepala Sekolah' ? 'selected' : '' }}>Diverifikasi Kepala Sekolah</option>
                    <option value="Disetujui Disdik/BPKAD" {{ request('status') === 'Disetujui Disdik/BPKAD' ? 'selected' : '' }}>Disetujui Disdik/BPKAD</option>
                    <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <select name="reason" onchange="this.form.submit()" class="text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:ring-1 focus:ring-rose-500 text-slate-700">
                    <option value="">-- Semua Alasan --</option>
                    <option value="Rusak Berat" {{ request('reason') === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    <option value="Hilang" {{ request('reason') === 'Hilang' ? 'selected' : '' }}>Hilang</option>
                    <option value="Kadaluarsa / Usang" {{ request('reason') === 'Kadaluarsa / Usang' ? 'selected' : '' }}>Kadaluarsa / Usang</option>
                    <option value="Bencana Alam" {{ request('reason') === 'Bencana Alam' ? 'selected' : '' }}>Bencana Alam</option>
                    <option value="Renovasi / Pembongkaran" {{ request('reason') === 'Renovasi / Pembongkaran' ? 'selected' : '' }}>Renovasi / Pembongkaran</option>
                </select>

                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition">Filter</button>
                @if(request()->hasAny(['search', 'status', 'reason']))
                <a href="{{ route('sarpras.disposals.index') }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">No. Usulan & Tgl</th>
                        <th class="px-4 py-3">Data Aset Inventaris</th>
                        <th class="px-4 py-3">Alasan & Kronologi</th>
                        <th class="px-4 py-3">Taksiran Nilai Sisa</th>
                        <th class="px-4 py-3">Pengusul & Tim Peneliti</th>
                        <th class="px-4 py-3">Status Usulan</th>
                        <th class="px-4 py-3 text-right">Aksi & Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($disposals as $disposal)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <div class="font-mono font-bold text-slate-900">{{ $disposal->disposal_number }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Tgl: {{ $disposal->disposal_date->format('d/m/Y') }}</div>
                            @if($disposal->official_report_number)
                            <div class="text-[10px] text-indigo-600 font-mono mt-0.5">BA: {{ $disposal->official_report_number }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $disposal->asset->name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $disposal->asset->code }}</div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono bg-blue-50 text-blue-700 border border-blue-200">KIB {{ $disposal->asset->kib_type }}</span>
                                <span class="text-[11px] text-slate-400">Rp {{ number_format($disposal->asset->price, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold 
                                @if($disposal->reason === 'Rusak Berat') bg-rose-50 text-rose-700 border border-rose-200
                                @elseif($disposal->reason === 'Hilang') bg-amber-50 text-amber-700 border border-amber-200
                                @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                {{ $disposal->reason }}
                            </span>
                            <div class="text-[11px] text-slate-600 mt-1 line-clamp-2" title="{{ $disposal->chronology_description }}">
                                {{ $disposal->chronology_description }}
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-rose-700 font-mono">
                            Rp {{ number_format($disposal->estimated_scrap_value, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $disposal->proposed_by_name }}</div>
                            @if($disposal->inspector_names)
                            <div class="text-[10px] text-slate-500 mt-0.5">Tim: {{ $disposal->inspector_names }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($disposal->status === 'Disetujui Disdik/BPKAD')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center w-max gap-1">
                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    Disetujui BPKAD
                                </span>
                            @elseif($disposal->status === 'Diverifikasi Kepala Sekolah')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 flex items-center w-max gap-1">
                                    Diverifikasi KS
                                </span>
                            @elseif($disposal->status === 'Ditolak')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200 flex items-center w-max gap-1">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center w-max gap-1">
                                    Diusulkan
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('sarpras.disposals.print', $disposal) }}" target="_blank" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-[11px] transition inline-flex items-center gap-1" title="Cetak Berita Acara Usulan Penghapusan">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak BA
                                </a>

                                <form action="{{ route('sarpras.disposals.archive', $disposal) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg text-[11px] transition inline-flex items-center gap-1 cursor-pointer" title="Simpan ke Kearsipan Digital">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                        Arsip
                                    </button>
                                </form>

                                <button onclick="openStatusModal('{{ $disposal->id }}', '{{ $disposal->disposal_number }}', '{{ $disposal->status }}', '{{ $disposal->official_report_number }}')" class="px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-[11px] transition cursor-pointer">
                                    Ubah Status
                                </button>

                                @if($disposal->status !== 'Disetujui Disdik/BPKAD')
                                <form action="{{ route('sarpras.disposals.destroy', $disposal) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan usulan penghapusan ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Hapus Usulan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                            Belum ada usulan penghapusan barang inventaris (afkir) yang diajukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($disposals->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $disposals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Buat Usulan Penghapusan Baru -->
<div id="modalAddDisposal" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] overflow-hidden my-auto animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between p-5 pb-3 border-b border-slate-200 shrink-0">
            <h3 class="text-base font-bold text-slate-900">Form Usulan Penghapusan Aset (BMD)</h3>
            <button onclick="document.getElementById('modalAddDisposal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('sarpras.disposals.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden m-0">
            @csrf
            <div class="p-5 space-y-4 text-xs overflow-y-auto max-h-[calc(90vh-140px)] flex-1">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Aset Inventaris yang Diusulkan *</label>
                    <select name="asset_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                        <option value="">-- Pilih Aset Inventaris --</option>
                        @foreach($assets as $ast)
                        <option value="{{ $ast->id }}" {{ request('asset_id') == $ast->id ? 'selected' : '' }}>
                            [{{ $ast->code }}] {{ $ast->name }} - Kondisi: {{ $ast->condition }} ({{ $ast->room?->name ?? 'Gudang/Belum Ditempatkan' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Pengajuan *</label>
                        <input type="date" name="disposal_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alasan Penghapusan *</label>
                        <select name="reason" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                            <option value="Rusak Berat">Rusak Berat (Tidak Ekonomis Diperbaiki)</option>
                            <option value="Hilang">Hilang (Ada Surat Keterangan Polisi)</option>
                            <option value="Kadaluarsa / Usang">Kadaluarsa / Teknologi Usang</option>
                            <option value="Bencana Alam">Bencana Alam / Force Majeure</option>
                            <option value="Renovasi / Pembongkaran">Renovasi / Pembongkaran Gedung</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Taksiran Nilai Residu / Rongsokan (Rp)</label>
                        <input type="number" name="estimated_scrap_value" min="0" step="1000" placeholder="Contoh: 150000" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Pengurus Barang / Pemohon *</label>
                        <input type="text" name="proposed_by_name" value="{{ auth()->user()?->name ?? 'Pengurus Barang Sekolah' }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tim / Panitia Peneliti Penghapusan</label>
                        <input type="text" name="inspector_names" placeholder="Contoh: Waka Sarpras, Kepala TU, Staf Sarpras" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Berita Acara Pemeriksaan Fisik</label>
                        <input type="text" name="official_report_number" placeholder="Contoh: BA-PEMERIKSAAN/028/2026" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kronologi Kerusakan / Alasan Teknis Fisik *</label>
                    <textarea name="chronology_description" rows="3" required placeholder="Jelaskan kondisi fisik barang mengapa harus diusulkan penghapusan/afkir..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                    <input type="text" name="notes" placeholder="Lokasi penampungan rongsok sementara, dll." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-rose-500 text-slate-800">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 p-4 bg-slate-50/80 border-t border-slate-200 shrink-0">
                <button type="button" onclick="document.getElementById('modalAddDisposal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-100 transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">Simpan Usulan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Status Usulan -->
<div id="modalUpdateStatus" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 my-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <h3 class="text-base font-bold text-slate-900">Ubah Status Usulan Penghapusan</h3>
            <button onclick="document.getElementById('modalUpdateStatus').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formUpdateStatus" method="POST" class="space-y-4 mt-4 text-xs">
            @csrf
            @method('PATCH')
            
            <div>
                <span class="text-slate-500">Nomor Usulan:</span>
                <span id="labelDisposalNumber" class="font-mono font-bold text-slate-900 ml-1"></span>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Usulan Baru *</label>
                <select name="status" id="selectStatus" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800">
                    <option value="Diusulkan">Diusulkan (Penyusunan Berkas Sekolah)</option>
                    <option value="Diverifikasi Kepala Sekolah">Diverifikasi Kepala Sekolah</option>
                    <option value="Disetujui Disdik/BPKAD">Disetujui Disdik / BPKAD (Aset Resmi Dihapus)</option>
                    <option value="Ditolak">Ditolak</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1 leading-tight">Jika dipilih "Disetujui Disdik/BPKAD", sistem otomatis menandai aset sebagai 'Dihapus/Afkir' dan mengeluarkannya dari rekapitulasi aktif KIR Ruangan.</p>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nomor SK / Surat Persetujuan Penghapusan</label>
                <input type="text" name="official_report_number" id="inputOfficialReport" placeholder="Contoh: SK-BPKAD/028/2026" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Verifikasi</label>
                <textarea name="notes" rows="2" placeholder="Catatan hasil verifikasi fisik atau rekomendasi..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 text-slate-800"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="document.getElementById('modalUpdateStatus').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-100 transition cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition cursor-pointer">Perbarui Status</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
    function openStatusModal(id, disposalNum, currentStatus, reportNum) {
        const form = document.getElementById('formUpdateStatus');
        form.action = `/sarpras/disposals/${id}/status`;
        document.getElementById('labelDisposalNumber').textContent = disposalNum;
        document.getElementById('selectStatus').value = currentStatus;
        document.getElementById('inputOfficialReport').value = reportNum || '';
        document.getElementById('modalUpdateStatus').classList.remove('hidden');
    }
</script>
@endpush

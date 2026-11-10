@extends('layouts.app')

@section('title', 'Master Referensi Sistem')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Master Referensi Sistem</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar kodefikasi dan parameter baku ketatausahaan sekolah.</p>
        </div>
        <div>
            <x-import-modal type="references" title="Data Referensi" />
        </div>
    </div>

    <!-- Category Tabs Navigation -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-2 border-b border-slate-200 text-xs font-semibold">
        @foreach($categories as $key => $label)
            <a href="{{ route('master.references.index', ['category' => $key]) }}" 
               class="px-3 py-2 rounded-lg whitespace-nowrap transition {{ $currentCategory === $key ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-200 border border-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Integration & Reference Indicator Banner -->
    @php
        $usage = $moduleUsage[$currentCategory] ?? null;
    @endphp
    @if($usage)
        <div class="bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-slate-50 border border-blue-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-blue-950 uppercase tracking-wider">Single Source of Truth (Data Master Acuan)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Terintegrasi Langsung</span>
                    </div>
                    <div class="text-xs text-slate-700 mt-1 leading-relaxed">
                        <span class="font-semibold text-slate-900">Dasar Regulasi:</span> {{ $usage['rule'] }}
                    </div>
                    <div class="text-[11px] text-slate-600 mt-0.5">
                        <span class="font-semibold text-blue-700">Rujukan Menu Operasional:</span> {{ $usage['target'] }}
                    </div>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-[11px] font-semibold text-slate-600 shadow-2xs">
                    Kategori: <strong class="text-slate-900">{{ $categories[$currentCategory] }}</strong> ({{ $items->count() }} Data)
                </span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
                Tambah [{{ $categories[$currentCategory] }}]
            </h2>
            <form action="{{ route('master.references.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="category" value="{{ $currentCategory }}">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kode Baku Referensi</label>
                    <input type="text" name="code" placeholder="{{ match($currentCategory) {
                        'jenis_surat' => 'cth: SD, ST, SK, SU, SKET',
                        'jenis_barang' => 'cth: BHP-ATK, KIB-A, KIB-B',
                        'satuan' => 'cth: RIM, BOX, PCS, UNIT',
                        'kondisi_barang' => 'cth: B, RR, RS, RB',
                        'sumber_dana' => 'cth: BOSP-REG, BOPD-JBR',
                        'status_kepegawaian' => 'cth: PNS, PPPK, GTT',
                        'status_siswa' => 'cth: AKTIF, MUTASI-IN',
                        'jabatan' => 'cth: KS, WKS-KSW, KTU',
                        default => 'Kode referensi unik'
                    } }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama / Nilai Referensi *</label>
                    <input type="text" name="name" required placeholder="Nama lengkap referensi..." class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan / Penjelasan</label>
                    <textarea name="description" rows="2" placeholder="Uraian penjelasan atau nomor regulasi terkait..." class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ $items->count() + 1 }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <button type="submit" class="w-full mt-2 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm">
                    Simpan Referensi
                </button>
            </form>
        </div>

        <!-- Tabel Referensi -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 font-bold text-sm text-slate-800 flex justify-between items-center">
                <span>Daftar Data: {{ $categories[$currentCategory] }}</span>
                <span class="text-xs text-slate-400 font-normal">Total: {{ $items->count() }} Data</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3">Kode Baku</th>
                            <th class="px-4 py-3">Nama / Nilai Referensi</th>
                            <th class="px-4 py-3">Keterangan / Regulasi</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $idx => $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 text-slate-400 text-center font-medium">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-mono font-bold text-[11px] border border-blue-200 inline-block">
                                        {{ $item->code ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $item->name }}</td>
                                <td class="px-4 py-3 text-slate-500 leading-relaxed">{{ $item->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    <button type="button" onclick="openEditReference({{ json_encode([
                                        'id' => $item->id,
                                        'code' => $item->code,
                                        'name' => $item->name,
                                        'description' => $item->description,
                                        'sort_order' => $item->sort_order,
                                    ]) }})" class="text-amber-600 hover:underline font-semibold cursor-pointer">Edit</button>
                                    <form action="{{ route('master.references.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus referensi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:underline cursor-pointer">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada item referensi untuk kategori ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Referensi -->
<div id="modal-edit-reference" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Item Referensi</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui kode atau label master referensi.</p>

        <form id="form-edit-reference" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kode Baku Referensi</label>
                <input type="text" name="code" id="edit-ref-code" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama / Nilai Referensi <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit-ref-name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Keterangan / Penjelasan</label>
                <textarea name="description" id="edit-ref-desc" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Urutan Tampil</label>
                <input type="number" name="sort_order" id="edit-ref-sort" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-reference').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditReference(data) {
        document.getElementById('form-edit-reference').action = '/master/references/' + data.id;
        document.getElementById('edit-ref-code').value = data.code || '';
        document.getElementById('edit-ref-name').value = data.name;
        document.getElementById('edit-ref-desc').value = data.description || '';
        document.getElementById('edit-ref-sort').value = data.sort_order || 1;
        document.getElementById('modal-edit-reference').classList.remove('hidden');
    }
</script>
@endsection

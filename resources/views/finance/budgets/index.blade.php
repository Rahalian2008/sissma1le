@extends('layouts.app')

@section('title', 'Perencanaan Anggaran & RKAS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Rencana Kegiatan & Anggaran Sekolah (RKAS)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penetapan pagu anggaran BOSP Reguler, Kinerja, BOPD Pemprov, dan rincian alokasi program.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.bku.index') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl border border-indigo-200 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Buku Kas Umum (BKU)
            </a>
            <button onclick="document.getElementById('modal-budget').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pagu Anggaran
            </button>
        </div>
    </div>

    <!-- Year Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
        <form action="{{ route('finance.budgets.index') }}" method="GET" class="flex items-center gap-2 text-xs">
            <span class="text-slate-500 font-semibold">Tahun Anggaran:</span>
            <select name="year" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl font-bold">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $fiscalYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Budgets List -->
    <div class="space-y-6">
        @forelse ($budgets as $b)
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $b->funding_source }}
                            </span>
                            <span class="text-xs text-slate-500 font-semibold">Tahun {{ $b->fiscal_year }}</span>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 mt-1">Total Pagu: Rp {{ number_format($b->total_allocation, 0, ',', '.') }}</h2>
                        <p class="text-xs text-slate-500">{{ $b->description ?? 'Rencana Kerja dan Anggaran Sekolah' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="openItemModal({{ $b->id }}, '{{ $b->funding_source }}')" class="px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold border border-blue-200 transition cursor-pointer">
                            + Tambah Sub Kegiatan
                        </button>
                        <button onclick="openEditBudgetModal({{ json_encode($b) }})" class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition cursor-pointer" title="Edit Pagu Anggaran">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <form action="{{ route('finance.budgets.destroy', $b) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pagu anggaran ini beserta seluruh sub kegiatannya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer" title="Hapus Pagu Anggaran">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sub Activities Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2.5">Kode Rekening</th>
                                <th class="px-4 py-2.5">Nama Program (8 SNP)</th>
                                <th class="px-4 py-2.5">Uraian Nama Kegiatan</th>
                                <th class="px-4 py-2.5 text-right">Alokasi Anggaran</th>
                                <th class="px-4 py-2.5 text-right">Realisasi (SPJ)</th>
                                <th class="px-4 py-2.5 text-right">Sisa Pagu</th>
                                <th class="px-4 py-2.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($b->items as $item)
                                @php $remaining = $item->allocated_amount - $item->realized_amount; @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-2.5">
                                        <span class="font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-xs inline-block">
                                            {{ $item->account_code ?? '5.1.02' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-[11px] inline-block">
                                            {{ $item->program_name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-slate-900">{{ $item->activity_name }}</div>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono font-semibold text-slate-800">
                                        Rp {{ number_format($item->allocated_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono font-bold text-blue-600">
                                        Rp {{ number_format($item->realized_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono font-semibold {{ $remaining < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button onclick="openEditItemModal({{ json_encode($item) }})" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Sub Kegiatan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <form action="{{ route('finance.budgets.items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub kegiatan anggaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Sub Kegiatan">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                                        Belum ada alokasi sub-kegiatan untuk sumber dana ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center text-slate-400 text-xs border border-slate-200">
                Belum ada rencana anggaran yang ditetapkan untuk tahun {{ $fiscalYear }}. Klik tombol Tambah Pagu Anggaran di atas.
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Tambah Pagu Anggaran -->
<div id="modal-budget" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tetapkan Pagu Sumber Anggaran</h2>
        <p class="text-xs text-slate-500 mb-4">Input pagu dana operasional sekolah.</p>

        <form action="{{ route('finance.budgets.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Anggaran <span class="text-rose-500">*</span></label>
                    <input type="number" name="fiscal_year" value="{{ $fiscalYear }}" min="2020" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Dana <span class="text-rose-500">*</span></label>
                    <select name="funding_source" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="BOSP Reguler">BOSP Reguler</option>
                        <option value="BOSP Kinerja">BOSP Kinerja</option>
                        <option value="BOPD Pemprov">BOPD Pemprov</option>
                        <option value="DAK Fisik">DAK Fisik</option>
                        <option value="Hibah">Hibah</option>
                        <option value="Komite Sekolah">Komite Sekolah</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Total Pagu Alokasi (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="total_allocation" value="1250000000" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Uraian / Keterangan</label>
                <textarea name="description" rows="2" placeholder="Alokasi pagu dana operasional..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-budget').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Pagu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Sub Kegiatan Anggaran -->
<div id="modal-item" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Sub Kegiatan RKAS</h2>
        <p class="text-xs text-slate-500 mb-4" id="item-modal-sub">Alokasi kegiatan.</p>

        <form id="form-item" action="" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-semibold text-slate-700">Kode Rekening Standar</label>
                    <button type="button" onclick="openAccountCodeModal('account-code-modal', {targetCodeId: 'item_account_code', targetProgramId: 'item_program_name', targetActivityId: 'item_activity_name'})" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-bold border border-emerald-200 cursor-pointer transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari Kode Rekening BOSP/SIPD</span>
                    </button>
                </div>
                <input type="text" name="account_code" id="item_account_code" placeholder="5.1.02.01.01.0024" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                <span class="text-[10px] text-slate-400 mt-1 block">💡 Gunakan tombol pencarian katalog atau ketik langsung secara manual jika memiliki kode rekening sendiri.</span>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Standar Program <span class="text-rose-500">*</span></label>
                <input type="text" name="program_name" id="item_program_name" placeholder="Standar Pengelolaan" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Uraian Nama Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="activity_name" id="item_activity_name" placeholder="Pengadaan Perlengkapan Habis Pakai dan ATK Sekolah" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alokasi Anggaran (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="allocated_amount" value="25000000" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-item').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Kegiatan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openItemModal(budgetId, sourceName) {
    document.getElementById('form-item').action = `/finance/budgets/${budgetId}/items`;
    document.getElementById('item-modal-sub').innerText = `Sumber: ${sourceName}`;
    document.getElementById('item_account_code').value = '';
    document.getElementById('item_program_name').value = '';
    document.getElementById('item_activity_name').value = '';
    document.getElementById('modal-item').classList.remove('hidden');
}

function openEditBudgetModal(b) {
    document.getElementById('form-edit-budget').action = `/finance/budgets/${b.id}`;
    document.getElementById('edit_budget_year').value = b.fiscal_year;
    document.getElementById('edit_budget_source').value = b.funding_source;
    document.getElementById('edit_budget_allocation').value = b.total_allocation;
    document.getElementById('edit_budget_desc').value = b.description || '';
    document.getElementById('modal-edit-budget').classList.remove('hidden');
}

function openEditItemModal(item) {
    document.getElementById('form-edit-item').action = `/finance/budgets/items/${item.id}`;
    document.getElementById('edit_item_account').value = item.account_code || '';
    document.getElementById('edit_item_program').value = item.program_name || '';
    document.getElementById('edit_item_activity').value = item.activity_name || '';
    document.getElementById('edit_item_amount').value = item.allocated_amount || 0;
    document.getElementById('modal-edit-item').classList.remove('hidden');
}
</script>

<!-- Modal Edit Pagu Anggaran -->
<div id="modal-edit-budget" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Pagu Sumber Anggaran</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui pagu dana operasional sekolah.</p>

        <form id="form-edit-budget" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Anggaran <span class="text-rose-500">*</span></label>
                    <input type="number" name="fiscal_year" id="edit_budget_year" min="2020" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Dana <span class="text-rose-500">*</span></label>
                    <select name="funding_source" id="edit_budget_source" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="BOSP Reguler">BOSP Reguler</option>
                        <option value="BOSP Kinerja">BOSP Kinerja</option>
                        <option value="BOPD Pemprov">BOPD Pemprov</option>
                        <option value="DAK Fisik">DAK Fisik</option>
                        <option value="Hibah">Hibah</option>
                        <option value="Komite Sekolah">Komite Sekolah</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Total Pagu Alokasi (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="total_allocation" id="edit_budget_allocation" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Uraian / Keterangan</label>
                <textarea name="description" id="edit_budget_desc" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-budget').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Sub Kegiatan Anggaran -->
<div id="modal-edit-item" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Sub Kegiatan RKAS</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui rincian kegiatan.</p>

        <form id="form-edit-item" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-semibold text-slate-700">Kode Rekening Standar</label>
                    <button type="button" onclick="openAccountCodeModal('account-code-modal', {targetCodeId: 'edit_item_account', targetProgramId: 'edit_item_program', targetActivityId: 'edit_item_activity'})" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-bold border border-emerald-200 cursor-pointer transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari Kode Rekening BOSP/SIPD</span>
                    </button>
                </div>
                <input type="text" name="account_code" id="edit_item_account" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                <span class="text-[10px] text-slate-400 mt-1 block">💡 Gunakan tombol pencarian katalog atau ketik langsung secara manual jika memiliki kode rekening sendiri.</span>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Standar Program <span class="text-rose-500">*</span></label>
                <input type="text" name="program_name" id="edit_item_program" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Uraian Nama Kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="activity_name" id="edit_item_activity" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Alokasi Anggaran (Rp) <span class="text-rose-500">*</span></label>
                <input type="number" name="allocated_amount" id="edit_item_amount" min="0" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-item').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<x-account-code-search-modal modal-id="account-code-modal" target-code-id="item_account_code" target-program-id="item_program_name" target-activity-id="item_activity_name" />
@endpush


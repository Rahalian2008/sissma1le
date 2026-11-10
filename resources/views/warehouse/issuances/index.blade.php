@extends('layouts.app')

@section('title', 'Pengeluaran Barang Keluar (BK)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pengeluaran Barang Keluar (BK)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan distribusi barang logistik kepada guru, unit kerja, atau kegiatan kesiswaan.</p>
        </div>
        <div>
            <button onclick="document.getElementById('modal-issuance').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Keluarkan Barang
            </button>
        </div>
    </div>

    <!-- Issuances Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Bukti (BK)</th>
                        <th class="px-5 py-3.5">Tanggal Keluar</th>
                        <th class="px-5 py-3.5">Nama Penerima / Unit</th>
                        <th class="px-5 py-3.5">Barang yang Dikeluarkan</th>
                        <th class="px-5 py-3.5">Keperluan / Tujuan Penggunaan</th>
                        <th class="px-5 py-3.5">Petugas Penyetuju</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($issuances as $iss)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-rose-700">
                                {{ $iss->issuance_number }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $iss->issuance_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $iss->recipient_name }}
                            </td>
                            <td class="px-5 py-3.5">
                                @foreach($iss->items as $item)
                                    <div class="font-semibold text-slate-800">{{ $item->good?->name }}: <span class="text-rose-700 font-mono font-bold">-{{ $item->quantity }} {{ $item->good?->unit }}</span></div>
                                @endforeach
                            </td>
                            <td class="px-5 py-3.5 text-slate-700">
                                {{ $iss->purpose }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $iss->approver?->name ?? 'Admin Sarpras' }}
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1">
                                <button type="button" onclick="openEditIssuanceModal({{ json_encode($iss) }})" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-lg text-[11px] border border-amber-200 transition inline-flex items-center gap-1 cursor-pointer" title="Edit Data Pengeluaran">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </button>
                                <a href="{{ route('warehouse.issuances.print', $iss) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-[11px] transition inline-flex items-center gap-1" title="Cetak Surat Bukti Barang Keluar (SBB)">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak SBB
                                </a>
                                <form action="{{ route('warehouse.issuances.destroy', $iss) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengeluaran barang ini? Stok barang akan dikembalikan ke gudang.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Pengeluaran">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada transaksi pengeluaran barang keluar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($issuances->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $issuances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Catat Pengeluaran Barang (Multi-Item Selection) -->
<div id="modal-issuance" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] my-auto overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Pengeluaran Barang Keluar (BK)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Mendukung pemilihan lebih dari satu item barang sekaligus.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-issuance').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('warehouse.issuances.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 m-0">
            @csrf
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1 max-h-[calc(90vh-140px)]">
            
            <!-- Multi-Goods Section -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Daftar Barang yang Dikeluarkan (Bisa Lebih Dari Satu)
                    </span>
                    <button type="button" onclick="addIssuanceItemRow()" class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-[11px] rounded-lg border border-rose-200 transition">
                        + Tambah Barang
                    </button>
                </div>

                <div id="issuance-items-container" class="space-y-2.5">
                    <!-- Row 1 Default -->
                    <div class="issuance-item-row flex items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200">
                        <div class="flex-1">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Nama Barang</label>
                            <select name="items[0][good_id]" required class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white">
                                <option value="">-- Pilih Barang --</option>
                                @foreach($goods as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }} (Stok: {{ $g->current_stock }} {{ $g->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Jumlah (Qty)</label>
                            <input type="number" name="items[0][quantity]" value="1" min="1" required class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs text-center font-bold">
                        </div>
                        <div class="pt-4">
                            <button type="button" onclick="removeIssuanceItemRow(this)" class="p-1.5 text-slate-300 hover:text-rose-600 rounded-lg transition" title="Hapus baris ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Keluar <span class="text-rose-500">*</span></label>
                    <input type="date" name="issuance_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Penerima / Unit Pemohon <span class="text-rose-500">*</span></label>
                    <input type="text" name="recipient_name" placeholder="Contoh: Panitia Ujian / Guru Fisika" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Keperluan / Tujuan Penggunaan <span class="text-rose-500">*</span></label>
                <input type="text" name="purpose" placeholder="Kegiatan Penilaian Akhir Semester (PAS) / KBM Harian" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Keterangan..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>

            </div>

            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-issuance').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold cursor-pointer flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Keluarkan Barang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let issuanceItemIndex = 1;
    function addIssuanceItemRow() {
        const container = document.getElementById('issuance-items-container');
        const firstRow = container.querySelector('.issuance-item-row');
        if (!firstRow) return;

        const newRow = firstRow.cloneNode(true);
        const select = newRow.querySelector('select');
        const qty = newRow.querySelector('input[type="number"]');
        
        select.name = `items[${issuanceItemIndex}][good_id]`;
        select.value = '';
        select.classList.remove('search-enhanced');
        
        qty.name = `items[${issuanceItemIndex}][quantity]`;
        qty.value = '1';

        // Remove any custom select wrapper if cloned
        const customWrapper = newRow.querySelector('.custom-searchable-select');
        if (customWrapper) {
            customWrapper.replaceWith(select);
            select.style.display = 'block';
        }

        container.appendChild(newRow);
        issuanceItemIndex++;
    }

    function removeIssuanceItemRow(btn) {
        const container = document.getElementById('issuance-items-container');
        const rows = container.querySelectorAll('.issuance-item-row');
        if (rows.length > 1) {
            btn.closest('.issuance-item-row').remove();
        } else {
            alert('Minimal 1 barang harus dipilih.');
        }
    }

    function openEditIssuanceModal(iss) {
        document.getElementById('form-edit-issuance').action = '/warehouse/issuances/' + iss.id;
        document.getElementById('edit-issuance-title').innerText = 'Edit Pengeluaran ' + iss.issuance_number;
        document.getElementById('edit-issuance-recipient').value = iss.recipient_name || '';
        document.getElementById('edit-issuance-date').value = iss.issuance_date ? iss.issuance_date.substring(0, 10) : '';
        document.getElementById('edit-issuance-purpose').value = iss.purpose || '';
        document.getElementById('edit-issuance-notes').value = iss.notes || '';
        document.getElementById('modal-edit-issuance').classList.remove('hidden');
    }
</script>

<!-- Modal Edit Pengeluaran Barang -->
<div id="modal-edit-issuance" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] my-auto overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h3 id="edit-issuance-title" class="font-bold text-slate-900 dark:text-white text-base">Edit Data Pengeluaran Barang</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui penerima, tanggal & keperluan pengeluaran</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-issuance').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="form-edit-issuance" method="POST" class="flex flex-col flex-1 min-h-0 m-0">
            @csrf
            @method('PUT')
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1 max-h-[calc(90vh-140px)]">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Penerima / Unit / Guru / Ruangan *</label>
                    <input type="text" id="edit-issuance-recipient" name="recipient_name" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pengeluaran Barang *</label>
                    <input type="date" id="edit-issuance-date" name="issuance_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keperluan / Tujuan Penggunaan *</label>
                    <input type="text" id="edit-issuance-purpose" name="purpose" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan</label>
                    <textarea id="edit-issuance-notes" name="notes" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-edit-issuance').classList.add('hidden')" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold shadow-sm cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endpush


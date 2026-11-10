@extends('layouts.app')

@section('title', 'Peminjaman Barang & Sarana')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Peminjaman Barang & Sarana</h1>
            <p class="text-xs text-slate-500 mt-0.5">Peminjaman proyektor, speaker portabel, laptop, dan perlengkapan upacara/kegiatan.</p>
        </div>
        <div>
            <button onclick="document.getElementById('modal-borrow').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Peminjaman
            </button>
        </div>
    </div>

    <!-- Borrowings Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Bukti Pinjam</th>
                        <th class="px-5 py-3.5">Nama Peminjam</th>
                        <th class="px-5 py-3.5">Barang yang Dipinjam</th>
                        <th class="px-5 py-3.5">Tgl Pinjam</th>
                        <th class="px-5 py-3.5">Batas Pengembalian</th>
                        <th class="px-5 py-3.5">Status & Kondisi Kembali</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($borrowings as $b)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
                                {{ $b->borrowing_number }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $b->borrower_name }}</div>
                                <div class="text-[11px] text-slate-400">Tipe: {{ $b->borrower_type }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $b->good_or_asset_name }} ({{ $b->quantity }} unit)
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $b->borrow_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-mono {{ $b->expected_return_date?->isPast() && $b->status !== 'Dikembalikan' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                {{ $b->expected_return_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($b->status === 'Dikembalikan') bg-emerald-100 text-emerald-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $b->status }}
                                </span>
                                @if($b->condition_on_return)
                                    <div class="text-[10px] text-slate-500 mt-0.5">Kondisi: {{ $b->condition_on_return }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openEditBorrowingModal({{ json_encode($b) }})" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-semibold rounded-lg text-[10px] transition inline-flex items-center gap-1 cursor-pointer" title="Edit Peminjaman">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                    <a href="{{ route('warehouse.borrowings.print', $b) }}" target="_blank" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-[10px] transition inline-flex items-center gap-1" title="Cetak Surat Bukti Peminjaman">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak
                                    </a>

                                    @if($b->status !== 'Dikembalikan')
                                        <form action="{{ route('warehouse.borrowings.return', $b) }}" method="POST" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="actual_return_date" value="{{ date('Y-m-d') }}">
                                            <select name="condition_on_return" class="px-2 py-1 bg-slate-50 border border-slate-300 rounded-lg text-[11px]">
                                                <option value="Baik & Lengkap">Baik & Lengkap</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Ada Komponen Hilang">Komponen Hilang</option>
                                            </select>
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[10px] cursor-pointer">
                                                Kembalikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-emerald-600 text-xs font-semibold">Selesai</span>
                                    @endif

                                    <form action="{{ route('warehouse.borrowings.destroy', $b) }}" method="POST" class="inline" onsubmit="return confirm('Hapus riwayat peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Peminjaman">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada riwayat peminjaman barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($borrowings->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Catat Peminjaman -->
<div id="modal-borrow" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] my-auto overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Catat Peminjaman Barang</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Mendukung peminjaman lebih dari satu jenis barang sekaligus.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-borrow').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('warehouse.borrowings.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 m-0">
            @csrf
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1 max-h-[calc(90vh-140px)]">
            
            <!-- Multi-Goods Borrowing Section -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Daftar Barang yang Dipinjam (Bisa Lebih Dari Satu)
                    </span>
                    <button type="button" onclick="addBorrowItemRow()" class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold text-[11px] rounded-lg border border-indigo-200 transition">
                        + Tambah Barang
                    </button>
                </div>

                <div id="borrow-items-container" class="space-y-2">
                    <!-- Row 1 -->
                    <div class="borrow-item-row flex items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200">
                        <div class="flex-1">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Nama Barang / Peralatan <span class="text-rose-500">*</span></label>
                            <input type="text" name="items[0][name]" placeholder="Contoh: Proyektor Epson EB-X500" required class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs">
                        </div>
                        <div class="w-20">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">Unit (Qty)</label>
                            <input type="number" name="items[0][quantity]" value="1" min="1" required class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs text-center font-bold">
                        </div>
                        <div class="pt-4">
                            <button type="button" onclick="removeBorrowItemRow(this)" class="p-1.5 text-slate-300 hover:text-rose-600 rounded-lg transition" title="Hapus baris ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Peminjam <span class="text-rose-500">*</span></label>
                    <input type="text" name="borrower_name" placeholder="Nama Guru / Siswa / Panitia" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tipe Peminjam <span class="text-rose-500">*</span></label>
                    <select name="borrower_type" required class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                        <option value="Guru">Guru</option>
                        <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                        <option value="Siswa / Pengurus OSIS">Siswa / Pengurus OSIS</option>
                        <option value="Pihak Luar">Pihak Luar</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                    <input type="date" name="borrow_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Batas Waktu Pengembalian <span class="text-rose-500">*</span></label>
                    <input type="date" name="expected_return_date" value="{{ now()->addDays(2)->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Keperluan / Lokasi Penggunaan</label>
                <input type="text" name="notes" placeholder="Kegiatan KBM Ruang Lab Komputer / Presentasi Siswa" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>

            </div>

            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-borrow').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let borrowItemIndex = 1;
    function addBorrowItemRow() {
        const container = document.getElementById('borrow-items-container');
        const firstRow = container.querySelector('.borrow-item-row');
        if (!firstRow) return;

        const newRow = firstRow.cloneNode(true);
        const nameInput = newRow.querySelector('input[type="text"]');
        const qtyInput = newRow.querySelector('input[type="number"]');
        
        nameInput.name = `items[${borrowItemIndex}][name]`;
        nameInput.value = '';
        
        qtyInput.name = `items[${borrowItemIndex}][quantity]`;
        qtyInput.value = '1';

        container.appendChild(newRow);
        borrowItemIndex++;
        nameInput.focus();
    }

    function removeBorrowItemRow(btn) {
        const container = document.getElementById('borrow-items-container');
        const rows = container.querySelectorAll('.borrow-item-row');
        if (rows.length > 1) {
            btn.closest('.borrow-item-row').remove();
        } else {
            alert('Minimal 1 barang harus diisi.');
        }
    }

    function openEditBorrowingModal(b) {
        document.getElementById('form-edit-borrowing').action = '/warehouse/borrowings/' + b.id;
        document.getElementById('edit-borrow-title').innerText = 'Edit Peminjaman ' + b.borrowing_number;
        document.getElementById('edit-borrower-name').value = b.borrower_name || '';
        document.getElementById('edit-borrower-type').value = b.borrower_type || 'Guru';
        document.getElementById('edit-good-name').value = b.good_or_asset_name || '';
        document.getElementById('edit-borrow-qty').value = b.quantity || 1;
        document.getElementById('edit-borrow-date').value = b.borrow_date ? b.borrow_date.substring(0, 10) : '';
        document.getElementById('edit-return-date').value = b.expected_return_date ? b.expected_return_date.substring(0, 10) : '';
        document.getElementById('edit-borrow-status').value = b.status || 'Dipinjam';
        document.getElementById('edit-borrow-notes').value = b.notes || '';
        document.getElementById('modal-edit-borrowing').classList.remove('hidden');
    }
</script>

<!-- Modal Edit Peminjaman Sarpras -->
<div id="modal-edit-borrowing" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] my-auto overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-4 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
            <div>
                <h3 id="edit-borrow-title" class="font-bold text-slate-900 dark:text-white text-base">Edit Peminjaman Sarpras</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui informasi peminjam, barang & tenggat waktu</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-borrowing').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="form-edit-borrowing" method="POST" class="flex flex-col flex-1 min-h-0 m-0">
            @csrf
            @method('PUT')
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1 max-h-[calc(90vh-140px)]">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Peminjam *</label>
                        <input type="text" id="edit-borrower-name" name="borrower_name" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Peminjam *</label>
                        <select id="edit-borrower-type" name="borrower_type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                            <option value="Guru">Guru / Tenaga Pendidik</option>
                            <option value="Siswa">Siswa / Siswi (OSIS/Ekskul)</option>
                            <option value="Tenaga Kependidikan">Tenaga Kependidikan (TU)</option>
                            <option value="Pihak Luar">Pihak Luar / Instansi Mitra</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Sarana / Barang *</label>
                        <input type="text" id="edit-good-name" name="good_or_asset_name" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Unit *</label>
                        <input type="number" id="edit-borrow-qty" name="quantity" min="1" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pinjam *</label>
                        <input type="date" id="edit-borrow-date" name="borrow_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tenggat Kembali *</label>
                        <input type="date" id="edit-return-date" name="expected_return_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Pinjam *</label>
                        <select id="edit-borrow-status" name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100">
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Dikembalikan">Dikembalikan</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Keperluan / Kondisi Awal</label>
                    <textarea id="edit-borrow-notes" name="notes" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 text-slate-800 dark:text-slate-100"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0">
                <button type="button" onclick="document.getElementById('modal-edit-borrowing').classList.add('hidden')" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold shadow-sm cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endpush


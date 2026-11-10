@extends('layouts.app')

@section('title', 'Buku Ekspedisi Surat (Tanda Terima Naskah Dinas)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">Tata Naskah Dinas</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Pengiriman & Bukti Serah Terima Surat</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Buku Ekspedisi Surat Dinas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan resmi bukti penyampaian dan serah terima surat dinas keluar kepada pihak internal (Guru/Tendik) maupun eksternal (Dinas/Lembaga).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('letters.expeditions.print', ['month' => request('month'), 'year' => request('year'), 'destination_type' => request('destination_type')]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Ekspedisi
            </a>
            <button onclick="document.getElementById('modalAddExpedition').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Pengiriman Ekspedisi
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Ekspedisi</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalExpeditions) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Surat Tercatat Dikirim</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-emerald-200 bg-emerald-50/20">
            <div class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Sudah Diterima</div>
            <div class="text-2xl font-bold text-emerald-800 mt-1">{{ number_format($receivedCount) }}</div>
            <div class="text-[10px] text-emerald-600/80 mt-0.5">Tanda Terima Lengkap</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-amber-200 bg-amber-50/20">
            <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Pending / Dalam Perjalanan</div>
            <div class="text-2xl font-bold text-amber-800 mt-1">{{ number_format($pendingCount) }}</div>
            <div class="text-[10px] text-amber-600/80 mt-0.5">Belum Dikonfirmasi</div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200">
            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Internal Sekolah</div>
            <div class="text-2xl font-bold text-indigo-700 mt-1">{{ number_format($internalCount) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Penyampaian ke PTK/Siswa</div>
        </div>
    </div>

    <!-- Filter & Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
            <form action="{{ route('letters.expeditions.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor surat, penerima, perihal..." class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 w-full sm:w-64">
                <select name="destination_type" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Tujuan</option>
                    <option value="Internal" {{ request('destination_type') === 'Internal' ? 'selected' : '' }}>Internal (PTK / Siswa)</option>
                    <option value="Eksternal" {{ request('destination_type') === 'Eksternal' ? 'selected' : '' }}>Eksternal (Dinas / Instansi)</option>
                </select>
                <select name="receipt_status" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="">Semua Status</option>
                    <option value="Terkirim" {{ request('receipt_status') === 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                    <option value="Diterima" {{ request('receipt_status') === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="Pending" {{ request('receipt_status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded-xl text-xs font-semibold">Filter</button>
                @if(request()->hasAny(['search', 'destination_type', 'receipt_status']))
                    <a href="{{ route('letters.expeditions.index') }}" class="px-3 py-1.5 text-slate-500 hover:text-slate-700 text-xs">Reset</a>
                @endif
            </form>
            <div class="text-xs text-slate-500">
                Menampilkan <strong>{{ $expeditions->total() }}</strong> catatan ekspedisi
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3 text-center" style="width: 40px;">No</th>
                        <th class="px-4 py-3">No. Ekspedisi & Tgl</th>
                        <th class="px-4 py-3">Nomor Surat Keluar</th>
                        <th class="px-4 py-3">Kepada / Tujuan</th>
                        <th class="px-4 py-3">Perihal Naskah Dinas</th>
                        <th class="px-4 py-3">Pengantar & Penerima</th>
                        <th class="px-4 py-3 text-center">Status Tanda Terima</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($expeditions as $idx => $exp)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 text-center text-slate-400 font-mono">
                                {{ $expeditions->firstItem() + $idx }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono font-bold text-indigo-900 block">{{ $exp->expedition_number }}</span>
                                <span class="text-[11px] text-slate-500">{{ $exp->delivery_date?->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono font-semibold text-slate-800 block">{{ $exp->letter_number }}</span>
                                <span class="text-[10px] text-slate-400">Tgl Surat: {{ $exp->letter_date?->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 block">{{ $exp->recipient_name }}</span>
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold {{ $exp->destination_type === 'Internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $exp->destination_type }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800 line-clamp-2 max-w-xs">{{ $exp->subject }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-[11px]">
                                    <span class="text-slate-400">Kurir:</span> <strong>{{ $exp->courier_name ?: 'Staf TU' }}</strong>
                                </div>
                                <div class="text-[11px] mt-0.5">
                                    <span class="text-slate-400">Penerima:</span> 
                                    @if($exp->receiver_name)
                                        <strong class="text-emerald-700">{{ $exp->receiver_name }}</strong>
                                        <span class="text-[10px] text-slate-400">({{ $exp->received_at?->format('d/m/Y H:i') }})</span>
                                    @else
                                        <span class="text-amber-600 italic">Belum Diterima</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $exp->receipt_status === 'Diterima' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($exp->receipt_status === 'Pending' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                                    {{ $exp->receipt_status }}
                                </span>
                                @if($exp->receipt_file_path)
                                    <a href="{{ Storage::url($exp->receipt_file_path) }}" target="_blank" class="block text-[10px] text-indigo-600 hover:underline mt-1">
                                        📎 Bukti Terima
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-1.5">
                                <button type="button" 
                                        onclick="openEditExpeditionModal({{ json_encode($exp) }})"
                                        class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-lg text-xs transition border border-amber-200">
                                    Edit
                                </button>
                                <button type="button" 
                                        onclick="openReceiptModal({{ json_encode([
                                            'id' => $exp->id,
                                            'expedition_number' => $exp->expedition_number,
                                            'recipient' => $exp->recipient_name,
                                            'receiver_name' => $exp->receiver_name ?? '',
                                            'status' => $exp->receipt_status,
                                            'received_at' => $exp->received_at ? $exp->received_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'),
                                            'notes' => $exp->notes ?? '',
                                            'action_url' => route('letters.expeditions.receipt', $exp),
                                        ]) }})"
                                        class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg text-xs transition border border-indigo-200">
                                    Konfirmasi Terima
                                </button>
                                <form action="{{ route('letters.expeditions.destroy', $exp) }}" method="POST" class="inline" onsubmit="return confirm('Hapus catatan ekspedisi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                Belum ada catatan ekspedisi pengiriman surat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $expeditions->links() }}
        </div>
    </div>
</div>

<!-- Modal Catat Ekspedisi Baru -->
<div id="modalAddExpedition" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="px-5 py-4 bg-indigo-900 text-white flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm">Catat Pengiriman Buku Ekspedisi Surat</h3>
                <p class="text-[11px] text-indigo-200">Pencatatan serah terima naskah dinas keluar</p>
            </div>
            <button onclick="document.getElementById('modalAddExpedition').classList.add('hidden')" class="text-indigo-200 hover:text-white">&times;</button>
        </div>
        <form action="{{ route('letters.expeditions.store') }}" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih dari Surat Keluar Terdaftar (Opsional)</label>
                <select id="selectOutgoingLetter" onchange="fillFromOutgoingLetter(this)" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                    <option value="">-- Input Bebas / Pilih Surat Keluar --</option>
                    @foreach($recentOutgoingLetters as $out)
                        <option value="{{ $out->id }}" 
                                data-number="{{ $out->letter_number }}" 
                                data-date="{{ $out->letter_date?->format('Y-m-d') }}" 
                                data-recipient="{{ $out->recipient }}" 
                                data-subject="{{ $out->subject }}">
                            {{ $out->letter_number }} - {{ Str::limit($out->recipient, 30) }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="outgoing_letter_id" id="inputOutgoingLetterId">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Surat *</label>
                    <input type="text" name="letter_number" id="inputLetterNumber" required placeholder="Contoh: 421.3/045/SMA1LE/2026" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Surat *</label>
                    <input type="date" name="letter_date" id="inputLetterDate" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kepada / Instansi Tujuan *</label>
                    <input type="text" name="recipient_name" id="inputRecipientName" required placeholder="Contoh: Cabang Dinas Pendidikan Wilayah IX" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Tujuan *</label>
                    <select name="destination_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="Eksternal">Eksternal (Dinas / Instansi / Ortu)</option>
                        <option value="Internal">Internal (Guru / Tendik / Siswa)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Perihal Naskah Dinas *</label>
                <input type="text" name="subject" id="inputSubject" required placeholder="Contoh: Laporan Kepegawaian & Sarpras Triwulan III" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Pengiriman *</label>
                    <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Pengantar / Kurir</label>
                    <input type="text" name="courier_name" value="{{ auth()->user()?->name ?? 'Staf Tata Usaha' }}" placeholder="Nama staf pengirim" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <input type="text" name="notes" placeholder="Catatan alamat pengiriman atau kontak..." class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddExpedition').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-sm">Simpan ke Buku Ekspedisi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Penerimaan / Tanda Terima -->
<div id="modalReceipt" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 bg-emerald-700 text-white flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm">Konfirmasi Tanda Terima Ekspedisi</h3>
                <p id="receiptModalExpNo" class="text-[11px] text-emerald-100"></p>
            </div>
            <button onclick="document.getElementById('modalReceipt').classList.add('hidden')" class="text-emerald-200 hover:text-white">&times;</button>
        </div>
        <form id="formReceipt" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PATCH')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Orang Yang Menerima Surat *</label>
                <input type="text" id="receiptReceiverName" name="receiver_name" required placeholder="Nama lengkap penerima" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-bold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Waktu Serah Terima *</label>
                    <input type="datetime-local" id="receiptReceivedAt" name="received_at" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Ekspedisi *</label>
                    <select id="receiptStatus" name="receipt_status" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-bold">
                        <option value="Diterima">Diterima</option>
                        <option value="Terkirim">Terkirim (Menunggu Paraf)</option>
                        <option value="Pending">Pending / Tertunda</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Upload Bukti Foto Paraf / Resi / Dokumen (Opsional)</label>
                <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Serah Terima</label>
                <textarea id="receiptNotes" name="notes" rows="2" placeholder="Catatan kondisi dokumen, diserahkan di ruangan mana..." class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalReceipt').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-sm">Simpan Bukti Terima</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Catatan Ekspedisi -->
<div id="modalEditExpedition" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200">
        <div class="p-4 bg-amber-600 text-white flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm">Edit Catatan Buku Ekspedisi</h3>
                <p id="editExpNo" class="text-[11px] text-amber-100 font-mono"></p>
            </div>
            <button onclick="document.getElementById('modalEditExpedition').classList.add('hidden')" class="text-amber-200 hover:text-white">&times;</button>
        </div>
        <form id="formEditExpedition" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Surat *</label>
                    <input type="text" id="editLetterNumber" name="letter_number" required class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Surat *</label>
                    <input type="date" id="editLetterDate" name="letter_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Pengiriman *</label>
                    <input type="date" id="editDeliveryDate" name="delivery_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Tujuan *</label>
                    <select id="editDestinationType" name="destination_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="Internal">Internal</option>
                        <option value="Eksternal">Eksternal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tujuan / Nama Instansi Penerima *</label>
                <input type="text" id="editRecipientName" name="recipient_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Perihal Isi Surat *</label>
                <input type="text" id="editSubject" name="subject" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kurir / Petugas Pengantar</label>
                    <input type="text" id="editCourierName" name="courier_name" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Pengiriman *</label>
                    <select id="editReceiptStatus" name="receipt_status" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        <option value="Terkirim">Terkirim</option>
                        <option value="Diterima">Diterima</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Penerima (Jika ada)</label>
                <input type="text" id="editReceiverName" name="receiver_name" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan</label>
                <textarea id="editExpNotes" name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditExpedition').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function fillFromOutgoingLetter(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('inputOutgoingLetterId').value = selectedOption.value;
        document.getElementById('inputLetterNumber').value = selectedOption.dataset.number || '';
        document.getElementById('inputLetterDate').value = selectedOption.dataset.date || '';
        document.getElementById('inputRecipientName').value = selectedOption.dataset.recipient || '';
        document.getElementById('inputSubject').value = selectedOption.dataset.subject || '';
    } else {
        document.getElementById('inputOutgoingLetterId').value = '';
    }
}

function openReceiptModal(data) {
    document.getElementById('formReceipt').action = data.action_url;
    document.getElementById('receiptModalExpNo').innerText = 'No. Ekspedisi: ' + data.expedition_number + ' • Tujuan: ' + data.recipient;
    document.getElementById('receiptReceiverName').value = data.receiver_name;
    document.getElementById('receiptStatus').value = data.status;
    document.getElementById('receiptReceivedAt').value = data.received_at;
    document.getElementById('receiptNotes').value = data.notes;
    document.getElementById('modalReceipt').classList.remove('hidden');
}

function openEditExpeditionModal(exp) {
    document.getElementById('formEditExpedition').action = '/letters/expeditions/' + exp.id;
    document.getElementById('editExpNo').innerText = 'No. Ekspedisi: ' + exp.expedition_number;
    document.getElementById('editLetterNumber').value = exp.letter_number || '';
    document.getElementById('editLetterDate').value = exp.letter_date ? exp.letter_date.substring(0, 10) : '';
    document.getElementById('editDeliveryDate').value = exp.delivery_date ? exp.delivery_date.substring(0, 10) : '';
    document.getElementById('editDestinationType').value = exp.destination_type || 'Internal';
    document.getElementById('editRecipientName').value = exp.recipient_name || '';
    document.getElementById('editSubject').value = exp.subject || '';
    document.getElementById('editCourierName').value = exp.courier_name || '';
    document.getElementById('editReceiptStatus').value = exp.receipt_status || 'Terkirim';
    document.getElementById('editReceiverName').value = exp.receiver_name || '';
    document.getElementById('editExpNotes').value = exp.notes || '';
    document.getElementById('modalEditExpedition').classList.remove('hidden');
}
</script>
@endsection

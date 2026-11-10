@extends('layouts.app')

@section('title', 'Buku Penyerahan Ijazah & SKL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-600 text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                Buku Ekspedisi Penyerahan Ijazah & SKL
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Buku kendali tanda terima penyerahan blanko ijazah dan SKL lulusan sesuai standar Permendikbud & Disdik Jabar.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('students.diplomas.print-book', ['graduation_year' => request('graduation_year', date('Y'))]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Ekspedisi
            </a>
            <button onclick="document.getElementById('modal-diploma-new').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Tanda Terima Ijazah
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs">
            <span class="text-xs text-slate-500 font-medium">Total Ijazah Terdata</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-2xs">
            <span class="text-xs text-emerald-600 font-medium">Sudah Diambil Siswa</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['sudah_diambil']) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-amber-100 shadow-2xs">
            <span class="text-xs text-amber-600 font-medium">Belum Diambil (Di Brankas)</span>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ number_format($stats['belum_diambil']) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-sky-100 shadow-2xs">
            <span class="text-xs text-sky-600 font-medium">Sudah Cap 3 Jari</span>
            <div class="text-2xl font-black text-sky-700 mt-1">{{ number_format($stats['cap_tiga_jari']) }}</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('students.diplomas.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-2">
                <select name="graduation_year" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Tahun Lulus</option>
                    @foreach($graduationYears as $y)
                        <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                    <option value="">Semua Status Pengambilan</option>
                    <option value="Sudah Diambil" {{ request('status') === 'Sudah Diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                    <option value="Belum Diambil" {{ request('status') === 'Belum Diambil' ? 'selected' : '' }}>Belum Diambil / Di Brankas</option>
                    <option value="Dititipkan" {{ request('status') === 'Dititipkan' ? 'selected' : '' }}>Dititipkan</option>
                </select>
            </div>

            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No Seri Ijazah..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-xl text-xs bg-slate-50">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Siswa Lulusan</th>
                        <th class="px-5 py-3.5">No. Seri Ijazah & SKL</th>
                        <th class="px-5 py-3.5">Tahun Lulus</th>
                        <th class="px-5 py-3.5 text-center">Cap 3 Jari</th>
                        <th class="px-5 py-3.5">Status Pengambilan</th>
                        <th class="px-5 py-3.5">Penerima & Tgl Serah</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($diplomas as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $item->student?->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">NIS: {{ $item->student?->nis }} &bull; NISN: {{ $item->student?->nisn }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-mono font-bold text-amber-800">{{ $item->diploma_number ?: '-' }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">SKL: {{ $item->skl_number ?: '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $item->graduation_year }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($item->three_fingerprint_status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        ✓ Sudah
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        ○ Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($item->status === 'Sudah Diambil') bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif($item->status === 'Belum Diambil') bg-amber-100 text-amber-800 border border-amber-200
                                    @else bg-sky-100 text-sky-800 border border-sky-200 @endif">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($item->status === 'Sudah Diambil')
                                    <div class="font-semibold text-slate-900">{{ $item->recipient_name ?: $item->student?->name }}</div>
                                    <div class="text-[10px] text-slate-500">({{ $item->recipient_relation }}) &bull; {{ $item->handover_date?->format('d/m/Y') }}</div>
                                @else
                                    <span class="text-slate-400 italic">Tersimpan di Brankas TU</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <a href="{{ route('students.diplomas.print-receipt', $item) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer inline-block" title="Cetak Kwitansi / Tanda Terima">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                <button type="button" onclick="openEditDiploma({{ json_encode([
                                    'id' => $item->id,
                                    'student_name' => $item->student?->name,
                                    'diploma_number' => $item->diploma_number,
                                    'skl_number' => $item->skl_number,
                                    'graduation_year' => $item->graduation_year,
                                    'handover_date' => $item->handover_date?->format('Y-m-d'),
                                    'recipient_name' => $item->recipient_name,
                                    'recipient_relation' => $item->recipient_relation,
                                    'three_fingerprint_status' => (bool)$item->three_fingerprint_status,
                                    'status' => $item->status,
                                    'notes' => $item->notes,
                                ]) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Edit Tanda Terima">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('students.diplomas.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data penyerahan ijazah ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data buku ekspedisi ijazah. Klik <strong>"Catat Tanda Terima Ijazah"</strong> untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($diplomas->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $diplomas->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Catat Baru -->
<div id="modal-diploma-new" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Catat Penyerahan Ijazah / SKL</h2>
            <button type="button" onclick="document.getElementById('modal-diploma-new').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('students.diplomas.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa Lulusan <span class="text-rose-500">*</span></label>
                <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                    <option value="">-- Pilih Siswa Kelas XII / Lulus --</option>
                    @foreach($eligibleStudents as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} (NIS: {{ $st->nis }} - {{ $st->current_rombel ?? 'Lulus' }})</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Hanya menampilkan siswa yang belum memiliki pencatatan ijazah.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Seri Ijazah</label>
                    <input type="text" name="diploma_number" placeholder="DN-02/M-SMA/K13/2026/..." class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Surat SKL</label>
                    <input type="text" name="skl_number" placeholder="421.3/089-SMA1LE/SKL/2026" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Lulus <span class="text-rose-500">*</span></label>
                    <input type="text" name="graduation_year" value="{{ date('Y') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Pengambilan <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                        <option value="Belum Diambil">Belum Diambil / Di Brankas</option>
                        <option value="Sudah Diambil">Sudah Diambil</option>
                        <option value="Dititipkan">Dititipkan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Serah Terima</label>
                    <input type="date" name="handover_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hubungan Penerima</label>
                    <select name="recipient_relation" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                        <option value="Siswa Bersangkutan">Siswa Bersangkutan</option>
                        <option value="Orang Tua / Wali">Orang Tua / Wali</option>
                        <option value="Penerima Kuasa">Penerima Kuasa (Surat Kuasa)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Pengambil / Penerima</label>
                <input type="text" name="recipient_name" placeholder="Kosongkan jika diambil siswa sendiri" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
            </div>

            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="three_fingerprint_status" value="1" class="w-4 h-4 text-amber-600 rounded">
                    <span class="font-semibold text-slate-700">Sudah Melakukan Cap 3 Jari Tangan Kiri</span>
                </label>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Nomor surat kuasa / kondisi dokumen..." class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-diploma-new').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Tanda Terima</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-diploma-edit" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Perbarui Tanda Terima Ijazah</h2>
                <p id="edit-student-name" class="text-xs text-amber-700 font-semibold"></p>
            </div>
            <button type="button" onclick="document.getElementById('modal-diploma-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form id="form-diploma-edit" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Seri Ijazah</label>
                    <input type="text" name="diploma_number" id="edit-diploma-number" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Surat SKL</label>
                    <input type="text" name="skl_number" id="edit-skl-number" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tahun Lulus</label>
                    <input type="text" name="graduation_year" id="edit-graduation-year" required class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Pengambilan</label>
                    <select name="status" id="edit-status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                        <option value="Belum Diambil">Belum Diambil / Di Brankas</option>
                        <option value="Sudah Diambil">Sudah Diambil</option>
                        <option value="Dititipkan">Dititipkan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Serah Terima</label>
                    <input type="date" name="handover_date" id="edit-handover-date" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hubungan Penerima</label>
                    <select name="recipient_relation" id="edit-recipient-relation" class="w-full px-3 py-2 border border-slate-300 rounded-xl bg-white">
                        <option value="Siswa Bersangkutan">Siswa Bersangkutan</option>
                        <option value="Orang Tua / Wali">Orang Tua / Wali</option>
                        <option value="Penerima Kuasa">Penerima Kuasa (Surat Kuasa)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Pengambil / Penerima</label>
                <input type="text" name="recipient_name" id="edit-recipient-name" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs">
            </div>

            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="three_fingerprint_status" id="edit-three-fingerprint" value="1" class="w-4 h-4 text-amber-600 rounded">
                    <span class="font-semibold text-slate-700">Sudah Melakukan Cap 3 Jari Tangan Kiri</span>
                </label>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" id="edit-notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-diploma-edit').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditDiploma(data) {
    document.getElementById('edit-student-name').innerText = data.student_name;
    document.getElementById('edit-diploma-number').value = data.diploma_number || '';
    document.getElementById('edit-skl-number').value = data.skl_number || '';
    document.getElementById('edit-graduation-year').value = data.graduation_year || '';
    document.getElementById('edit-handover-date').value = data.handover_date || '';
    document.getElementById('edit-recipient-name').value = data.recipient_name || '';
    document.getElementById('edit-recipient-relation').value = data.recipient_relation || 'Siswa Bersangkutan';
    document.getElementById('edit-status').value = data.status || 'Belum Diambil';
    document.getElementById('edit-three-fingerprint').checked = Boolean(data.three_fingerprint_status);
    document.getElementById('edit-notes').value = data.notes || '';

    document.getElementById('form-diploma-edit').action = `/students/diplomas/${data.id}`;
    document.getElementById('modal-diploma-edit').classList.remove('hidden');
}
</script>
@endpush
@endsection

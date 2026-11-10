@extends('layouts.app')

@section('title', 'Data Ruangan & Gedung')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Data Ruangan & Sarana Sekolah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar ruang kelas, laboratorium sains, perpustakaan, dan penanggung jawab ruangan.</p>
        </div>
        <div>
            <button onclick="document.getElementById('modal-room').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Ruangan
            </button>
        </div>
    </div>

    <!-- Rooms Grid / Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Kode Ruang</th>
                        <th class="px-5 py-3.5">Nama Ruangan</th>
                        <th class="px-5 py-3.5">Penanggung Jawab (PIC)</th>
                        <th class="px-5 py-3.5 text-center">Kapasitas</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Aset Terdata</th>
                        <th class="px-5 py-3.5">Kondisi Ruang</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rooms as $r)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
                                {{ $r->code }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                {{ $r->name }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-semibold">
                                {{ $r->person_in_charge ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-900">
                                {{ $r->capacity }} Siswa/Orang
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 font-mono">
                                    {{ $r->assets_count }} Item
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($r->condition === 'Baik') bg-emerald-100 text-emerald-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $r->condition }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <button type="button" onclick="openEditRoom({{ json_encode([
                                    'id' => $r->id,
                                    'code' => $r->code,
                                    'name' => $r->name,
                                    'capacity' => $r->capacity,
                                    'person_in_charge' => $r->person_in_charge,
                                    'condition' => $r->condition,
                                ]) }})" class="p-1 text-slate-400 hover:text-amber-600 transition cursor-pointer" title="Edit Ruangan">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('sarpras.rooms.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada ruangan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rooms->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Tambah Ruang -->
<div id="modal-room" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[92vh] sm:max-h-[88vh] overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900 z-10">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Tambah Data Ruangan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Input kode dan nama ruangan sekolah untuk penempatan KIR.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-room').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('sarpras.rooms.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 m-0 overflow-hidden">
            @csrf
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1 min-h-0 overscroll-contain">
                <!-- Preset Cepat Ruangan Sekolah (1-Klik Isi Form) -->
                <div class="p-3 bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-900/60 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-900 dark:text-blue-200 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                            Pilih Cepat Ruangan Populer (1-Klik):
                        </span>
                        <span class="text-[10px] text-blue-700 dark:text-blue-300 bg-blue-100/80 dark:bg-blue-900/50 px-2 py-0.5 rounded-full font-bold">Otomatis Terisi</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="presetRoom('R-LAB-KOM', 'Laboratorium Komputer & Multimedia', 36, 'Budi Santoso, S.Kom.')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            💻 Lab Komputer
                        </button>
                        <button type="button" onclick="presetRoom('R-LAB-IPA', 'Laboratorium IPA & Sains Terpadu', 36, 'Dra. Siti Rahmah, M.Pd.')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            🔬 Lab IPA/Sains
                        </button>
                        <button type="button" onclick="presetRoom('R-PERPUS', 'Perpustakaan Ki Hajar Dewantara', 50, 'Hj. Nurhayati, S.Pd.')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            📚 Perpustakaan
                        </button>
                        <button type="button" onclick="presetRoom('R-GURU', 'Ruang Guru & Tenaga Pendidik', 45, 'Wakasek Kurikulum')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            👨‍🏫 Ruang Guru
                        </button>
                        <button type="button" onclick="presetRoom('R-TU', 'Ruang Tata Usaha & Pelayanan', 15, 'Kepala Tata Usaha')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            💼 Ruang TU
                        </button>
                        <button type="button" onclick="presetRoom('R-UKS', 'Ruang Unit Kesehatan Sekolah (UKS)', 10, 'Pembina UKS')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            🏥 Ruang UKS
                        </button>
                        <button type="button" onclick="presetRoom('R-KELAS-X', 'Ruang Belajar Kelas X', 36, 'Koordinator Wali Kelas')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            🏫 Ruang Kelas
                        </button>
                        <button type="button" onclick="presetRoom('R-GUDANG', 'Gudang Sarpras & Logistik', 10, 'Pengelola Logistik')" class="px-2.5 py-1 bg-white dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg text-[11px] font-medium text-blue-800 dark:text-blue-200 transition cursor-pointer shadow-2xs">
                            📦 Gudang Sarpras
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Ruang <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" id="create-room-code" placeholder="Contoh: R-LAB-KOM" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas (Orang) <span class="text-rose-500">*</span></label>
                        <input type="number" name="capacity" id="create-room-capacity" value="36" min="1" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Ruangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="create-room-name" placeholder="Contoh: Laboratorium Komputer & Multimedia" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Penanggung Jawab (PIC)</label>
                            <span class="text-[10px] text-slate-400 font-medium">Boleh Kosong</span>
                        </div>
                        <input type="text" name="person_in_charge" id="create-room-pic" list="ptk-list-options" placeholder="Ketik nama atau pilih dari daftar..." class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <select onchange="if(this.value){ document.getElementById('create-room-pic').value = this.value; }" class="w-full mt-1.5 px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-[11px] focus:bg-white focus:border-blue-500">
                            <option value="">-- Pilih Cepat dari Guru / Tendik / Jabatan --</option>
                            <optgroup label="🏛️ Jabatan & Pengelola Sekolah">
                                <option value="Wakasek Kurikulum">Wakasek Kurikulum</option>
                                <option value="Wakasek Kesiswaan">Wakasek Kesiswaan</option>
                                <option value="Wakasek Sarpras">Wakasek Sarpras</option>
                                <option value="Wakasek Humas">Wakasek Humas</option>
                                <option value="Kepala Tata Usaha">Kepala Tata Usaha</option>
                                <option value="Kepala Perpustakaan">Kepala Perpustakaan</option>
                                <option value="Pengelola Laboratorium Komputer">Pengelola Laboratorium Komputer</option>
                                <option value="Pengelola Laboratorium IPA">Pengelola Laboratorium IPA</option>
                                <option value="Pengelola Sarpras & Logistik">Pengelola Sarpras & Logistik</option>
                                <option value="Pembina UKS">Pembina UKS</option>
                                <option value="Pembina OSIS">Pembina OSIS</option>
                                <option value="Koordinator Guru BK">Koordinator Guru BK</option>
                            </optgroup>
                            <optgroup label="👨‍🏫 Tenaga Pendidik (Guru)">
                                @foreach($teachers as $emp)
                                    <option value="{{ $emp->formatted_name ?? $emp->name }}">{{ $emp->formatted_name ?? $emp->name }} @if(!empty($emp->position)) — {{ $emp->position }} @endif ({{ $emp->nip ?: 'Non-NIP' }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="💼 Tenaga Kependidikan (Tendik / Tata Usaha)">
                                @foreach($staff as $emp)
                                    <option value="{{ $emp->formatted_name ?? $emp->name }}">{{ $emp->formatted_name ?? $emp->name }} @if(!empty($emp->position)) — {{ $emp->position }} @endif ({{ $emp->nip ?: 'Non-NIP' }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kondisi Fisik Ruang <span class="text-rose-500">*</span></label>
                        <select name="condition" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="Baik" selected>Baik (Siap Pakai)</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Sedang">Rusak Sedang</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0 z-10">
                <button type="button" onclick="document.getElementById('modal-room').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Ruangan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Ruangan -->
<div id="modal-edit-room" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[92vh] sm:max-h-[88vh] overflow-hidden">
        <div class="flex items-center justify-between p-5 sm:p-6 pb-3 border-b border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900 z-10">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Edit Data Ruangan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui kode ruang, kapasitas, dan kondisi fisik ruangan.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-edit-room').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="form-edit-room" method="POST" class="flex flex-col flex-1 min-h-0 m-0 overflow-hidden">
            @csrf
            @method('PUT')
            <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1 min-h-0 overscroll-contain">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Ruang <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" id="edit-room-code" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl font-mono focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas (Orang) <span class="text-rose-500">*</span></label>
                        <input type="number" name="capacity" id="edit-room-capacity" min="1" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Ruangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit-room-name" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Penanggung Jawab (PIC)</label>
                            <span class="text-[10px] text-slate-400 font-medium">Boleh Kosong</span>
                        </div>
                        <input type="text" name="person_in_charge" id="edit-room-pic" list="ptk-list-options" placeholder="Ketik nama atau pilih..." class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <select onchange="if(this.value){ document.getElementById('edit-room-pic').value = this.value; }" class="w-full mt-1.5 px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 text-[11px] focus:bg-white focus:border-blue-500">
                            <option value="">-- Pilih Cepat dari Guru / Tendik / Jabatan --</option>
                            <optgroup label="🏛️ Jabatan & Pengelola Sekolah">
                                <option value="Wakasek Kurikulum">Wakasek Kurikulum</option>
                                <option value="Wakasek Kesiswaan">Wakasek Kesiswaan</option>
                                <option value="Wakasek Sarpras">Wakasek Sarpras</option>
                                <option value="Wakasek Humas">Wakasek Humas</option>
                                <option value="Kepala Tata Usaha">Kepala Tata Usaha</option>
                                <option value="Kepala Perpustakaan">Kepala Perpustakaan</option>
                                <option value="Pengelola Laboratorium Komputer">Pengelola Laboratorium Komputer</option>
                                <option value="Pengelola Laboratorium IPA">Pengelola Laboratorium IPA</option>
                                <option value="Pengelola Sarpras & Logistik">Pengelola Sarpras & Logistik</option>
                                <option value="Pembina UKS">Pembina UKS</option>
                                <option value="Pembina OSIS">Pembina OSIS</option>
                                <option value="Koordinator Guru BK">Koordinator Guru BK</option>
                            </optgroup>
                            <optgroup label="👨‍🏫 Tenaga Pendidik (Guru)">
                                @foreach($teachers as $emp)
                                    <option value="{{ $emp->formatted_name ?? $emp->name }}">{{ $emp->formatted_name ?? $emp->name }} @if(!empty($emp->position)) — {{ $emp->position }} @endif ({{ $emp->nip ?: 'Non-NIP' }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="💼 Tenaga Kependidikan (Tendik / Tata Usaha)">
                                @foreach($staff as $emp)
                                    <option value="{{ $emp->formatted_name ?? $emp->name }}">{{ $emp->formatted_name ?? $emp->name }} @if(!empty($emp->position)) — {{ $emp->position }} @endif ({{ $emp->nip ?: 'Non-NIP' }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kondisi Fisik Ruang <span class="text-rose-500">*</span></label>
                        <select name="condition" id="edit-room-condition" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="Baik">Baik (Siap Pakai)</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Sedang">Rusak Sedang</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/60 shrink-0 z-10">
                <button type="button" onclick="document.getElementById('modal-edit-room').classList.add('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Datalist untuk Autocomplete Penanggung Jawab Ruangan -->
<datalist id="ptk-list-options">
    @foreach($allEmployees as $emp)
        <option value="{{ $emp->formatted_name ?? $emp->name }}">{{ $emp->employment_type ?? 'Pegawai' }} - {{ $emp->position ?? 'PTK' }} ({{ $emp->nip ?: 'Non-NIP' }})</option>
    @endforeach
</datalist>

<script>
    function presetRoom(code, name, capacity, pic) {
        document.getElementById('create-room-code').value = code;
        document.getElementById('create-room-name').value = name;
        document.getElementById('create-room-capacity').value = capacity;
        if (pic) {
            document.getElementById('create-room-pic').value = pic;
        }
    }

    function openEditRoom(data) {
        document.getElementById('form-edit-room').action = '/sarpras/rooms/' + data.id;
        document.getElementById('edit-room-code').value = data.code;
        document.getElementById('edit-room-capacity').value = data.capacity;
        document.getElementById('edit-room-name').value = data.name;
        document.getElementById('edit-room-pic').value = data.person_in_charge || '';
        document.getElementById('edit-room-condition').value = data.condition;
        document.getElementById('modal-edit-room').classList.remove('hidden');
    }
</script>
@endpush

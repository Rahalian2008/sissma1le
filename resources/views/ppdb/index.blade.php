@extends('layouts.app')

@section('title', 'Penerimaan Peserta Didik Baru (PPDB)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Penerimaan Peserta Didik Baru (PPDB)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Sistem seleksi zonasi, afirmasi, prestasi, dan mutasi orang tua tahun ajaran {{ date('Y') }}/{{ date('Y')+1 }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-import-modal type="ppdb" title="PPDB Calon Siswa" />
            <button onclick="document.getElementById('modal-ppdb').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Pendaftaran Calon Siswa
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('ppdb.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <select name="track" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                <option value="">Semua Jalur Seleksi</option>
                <option value="Zonasi" {{ request('track') === 'Zonasi' ? 'selected' : '' }}>Jalur Zonasi</option>
                <option value="Prestasi" {{ request('track') === 'Prestasi' ? 'selected' : '' }}>Jalur Prestasi</option>
                <option value="Afirmasi" {{ request('track') === 'Afirmasi' ? 'selected' : '' }}>Jalur Afirmasi</option>
                <option value="Mutasi Orang Tua" {{ request('track') === 'Mutasi Orang Tua' ? 'selected' : '' }}>Jalur Mutasi Orang Tua</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl">
                <option value="">Semua Status Seleksi</option>
                <option value="Terdaftar" {{ request('status') === 'Terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                <option value="Lolos Seleksi" {{ request('status') === 'Lolos Seleksi' ? 'selected' : '' }}>Lolos Seleksi</option>
                <option value="Daftar Ulang" {{ request('status') === 'Daftar Ulang' ? 'selected' : '' }}>Daftar Ulang (Diterima Jadi Siswa)</option>
                <option value="Tidak Lolos" {{ request('status') === 'Tidak Lolos' ? 'selected' : '' }}>Tidak Lolos</option>
            </select>
        </form>
    </div>

    <!-- PPDB Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Pendaftaran</th>
                        <th class="px-5 py-3.5">Nama Calon Siswa</th>
                        <th class="px-5 py-3.5">Asal Sekolah</th>
                        <th class="px-5 py-3.5">Jalur & Nilai Rapor</th>
                        <th class="px-5 py-3.5">Status Seleksi</th>
                        <th class="px-5 py-3.5 text-right">Kelola Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registrations as $reg)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-blue-700">
                                {{ $reg->registration_number }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $reg->full_name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">NISN: {{ $reg->nisn ?? '-' }} &bull; {{ $reg->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-semibold">
                                {{ $reg->school_origin ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $reg->selection_track }}
                                </span>
                                <div class="text-[11px] text-slate-500 mt-1">Rata-rata: <strong class="text-slate-800">{{ $reg->average_score }}</strong></div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                    @if($reg->status === 'Daftar Ulang') bg-emerald-100 text-emerald-800
                                    @elseif($reg->status === 'Lolos Seleksi') bg-blue-100 text-blue-800
                                    @elseif($reg->status === 'Terdaftar') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $reg->status }}
                                </span>
                                @if($reg->assigned_class)
                                    <div class="text-[10px] text-slate-500 mt-0.5 font-semibold">Kelas: {{ $reg->assigned_class }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <form action="{{ route('ppdb.update-status', $reg) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="px-2 py-1 bg-slate-50 border border-slate-300 rounded-lg text-[11px]">
                                        <option value="Terdaftar" {{ $reg->status === 'Terdaftar' ? 'selected' : '' }}>Terdaftar</option>
                                        <option value="Lolos Seleksi" {{ $reg->status === 'Lolos Seleksi' ? 'selected' : '' }}>Lolos</option>
                                        <option value="Daftar Ulang" {{ $reg->status === 'Daftar Ulang' ? 'selected' : '' }}>Daftar Ulang & Penerimaan</option>
                                        <option value="Tidak Lolos" {{ $reg->status === 'Tidak Lolos' ? 'selected' : '' }}>Gugur</option>
                                    </select>
                                    <input type="hidden" name="assigned_class" value="X Merdeka 1">
                                    <button type="submit" class="px-2 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold text-[10px] transition cursor-pointer">
                                        Update
                                    </button>
                                </form>
                                <button type="button" onclick="openEditPpdb({{ json_encode([
                                    'id' => $reg->id,
                                    'full_name' => $reg->full_name,
                                    'nisn' => $reg->nisn,
                                    'nik' => $reg->nik,
                                    'gender' => $reg->gender,
                                    'birth_place' => $reg->birth_place,
                                    'birth_date' => $reg->birth_date?->format('Y-m-d'),
                                    'school_origin' => $reg->school_origin,
                                    'selection_track' => $reg->selection_track,
                                    'average_score' => $reg->average_score,
                                    'parent_name' => $reg->parent_name,
                                    'parent_phone' => $reg->parent_phone,
                                    'status' => $reg->status,
                                ]) }})" class="p-1 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer ml-1 inline-flex items-center" title="Edit Calon Siswa">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('ppdb.destroy', $reg) }}" method="POST" onsubmit="return confirm('Hapus calon siswa ini?')" class="inline ml-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Calon Siswa">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada pendaftaran calon siswa baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($registrations->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Pendaftaran Calon Siswa Baru -->
<div id="modal-ppdb" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Formulir Pendaftaran PPDB</h2>
        <p class="text-xs text-slate-500 mb-4">Pendaftaran peserta didik baru jenjang SMA.</p>

        <form action="{{ route('ppdb.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="full_name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NISN</label>
                    <input type="text" name="nisn" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIK</label>
                    <input type="text" name="nik" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Sekolah Asal (SMP) <span class="text-rose-500">*</span></label>
                    <input type="text" name="school_origin" required placeholder="SMP Negeri 1..." class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jalur Seleksi <span class="text-rose-500">*</span></label>
                    <select name="selection_track" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Zonasi">Zonasi Domisili</option>
                        <option value="Prestasi">Prestasi Nilai / Kejuaraan</option>
                        <option value="Afirmasi">Afirmasi (KIP / Prasejahtera)</option>
                        <option value="Mutasi Orang Tua">Mutasi Tugas Orang Tua</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nilai Rata-rata Rapor <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="average_score" value="88.5" min="0" max="100" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Orang Tua</label>
                    <input type="text" name="parent_name" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No. Telp Orang Tua</label>
                    <input type="text" name="parent_phone" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-ppdb').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Daftarkan Calon Siswa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Calon Siswa PPDB -->
<div id="modal-edit-ppdb" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Data Calon Siswa PPDB</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui identitas calon peserta didik dan data pendaftaran.</p>

        <form id="form-edit-ppdb" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="full_name" id="edit-ppdb-name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NISN</label>
                    <input type="text" name="nisn" id="edit-ppdb-nisn" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIK (KTP/KK)</label>
                    <input type="text" name="nik" id="edit-ppdb-nik" class="w-full px-3 py-2 border border-slate-300 rounded-xl font-mono">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="edit-ppdb-birth_place" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="edit-ppdb-birth_date" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" id="edit-ppdb-gender" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Sekolah Asal (SMP) <span class="text-rose-500">*</span></label>
                    <input type="text" name="school_origin" id="edit-ppdb-school_origin" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Jalur Seleksi <span class="text-rose-500">*</span></label>
                    <select name="selection_track" id="edit-ppdb-track" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Zonasi">Zonasi Domisili</option>
                        <option value="Prestasi">Prestasi Nilai / Kejuaraan</option>
                        <option value="Afirmasi">Afirmasi (KIP / Prasejahtera)</option>
                        <option value="Mutasi Orang Tua">Mutasi Tugas Orang Tua</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nilai Rata-rata <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="average_score" id="edit-ppdb-score" min="0" max="100" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Orang Tua</label>
                    <input type="text" name="parent_name" id="edit-ppdb-parent_name" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No. Telp Orang Tua</label>
                    <input type="text" name="parent_phone" id="edit-ppdb-parent_phone" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Pendaftaran <span class="text-rose-500">*</span></label>
                <select name="status" id="edit-ppdb-status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="Terdaftar">Terdaftar</option>
                    <option value="Lolos Seleksi">Lolos Seleksi</option>
                    <option value="Daftar Ulang">Daftar Ulang</option>
                    <option value="Tidak Lolos">Tidak Lolos</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-ppdb').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditPpdb(data) {
        document.getElementById('form-edit-ppdb').action = '/ppdb/' + data.id;
        document.getElementById('edit-ppdb-name').value = data.full_name;
        document.getElementById('edit-ppdb-nisn').value = data.nisn || '';
        document.getElementById('edit-ppdb-nik').value = data.nik || '';
        document.getElementById('edit-ppdb-gender').value = data.gender;
        document.getElementById('edit-ppdb-birth_place').value = data.birth_place || '';
        document.getElementById('edit-ppdb-birth_date').value = data.birth_date || '';
        document.getElementById('edit-ppdb-school_origin').value = data.school_origin;
        document.getElementById('edit-ppdb-track').value = data.selection_track;
        document.getElementById('edit-ppdb-score').value = data.average_score;
        document.getElementById('edit-ppdb-parent_name').value = data.parent_name || '';
        document.getElementById('edit-ppdb-parent_phone').value = data.parent_phone || '';
        document.getElementById('edit-ppdb-status').value = data.status;
        document.getElementById('modal-edit-ppdb').classList.remove('hidden');
    }
</script>
@endsection

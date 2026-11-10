@extends('layouts.app')

@section('title', 'Prestasi Peserta Didik')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Prestasi Peserta Didik</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan prestasi akademik dan non-akademik siswa di tingkat kota, provinsi, nasional, dan internasional.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('sis.sync-achievements') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer" title="Tarik catatan prestasi dari SIS-SMA1LE">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    ⚡ Tarik dari SIS-SMA1LE
                </button>
            </form>
            <x-import-modal type="achievements" title="Prestasi Siswa" />
            <button onclick="document.getElementById('modal-achievement').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Prestasi
            </button>
        </div>
    </div>

    <!-- Achievements Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">Nama Prestasi / Kejuaraan</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Peringkat & Tingkat</th>
                        <th class="px-5 py-3.5">Penyelenggara</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($achievements as $ach)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $ach->date?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $ach->student?->name }}
                                <div class="text-[11px] text-slate-400 font-mono font-normal">NIS: {{ $ach->student?->nis }} &bull; Kelas {{ $ach->student?->current_class_level }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">
                                {{ $ach->name }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $ach->category }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-amber-600">Juara {{ $ach->rank }}</span>
                                <span class="text-slate-500">({{ $ach->level }})</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $ach->organizer ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <button type="button" onclick="openEditAchievement({{ json_encode([
                                    'id' => $ach->id,
                                    'student_id' => $ach->student_id,
                                    'name' => $ach->name,
                                    'category' => $ach->category,
                                    'level' => $ach->level,
                                    'rank' => $ach->rank,
                                    'date' => $ach->date?->format('Y-m-d'),
                                    'organizer' => $ach->organizer,
                                    'notes' => $ach->notes,
                                ]) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Edit Prestasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('achievements.destroy', $ach) }}" method="POST" onsubmit="return confirm('Hapus catatan prestasi ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Prestasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Belum ada catatan prestasi siswa yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($achievements->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $achievements->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Prestasi -->
<div id="modal-achievement" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Tambah Catatan Prestasi</h2>
        <p class="text-xs text-slate-500 mb-4">Masukkan data kejuaraan atau prestasi yang diraih oleh siswa.</p>

        <form action="{{ route('students.achievements.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih Peserta Didik --</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} - Kelas {{ $s->current_class_level }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Prestasi / Lomba <span class="text-rose-500">*</span></label>
                <input type="text" name="name" placeholder="Contoh: Olimpiade Sains Nasional Bidang Matematika" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Akademik">Akademik</option>
                        <option value="Olahraga">Olahraga</option>
                        <option value="Seni & Budaya">Seni & Budaya</option>
                        <option value="Teknologi">Teknologi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tingkat <span class="text-rose-500">*</span></label>
                    <select name="level" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Sekolah">Sekolah</option>
                        <option value="Kecamatan">Kecamatan</option>
                        <option value="Kota/Kab">Kota / Kab</option>
                        <option value="Provinsi">Provinsi</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Peringkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="rank" placeholder="Contoh: 1 / Emas" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Perolehan <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Penyelenggara</label>
                    <input type="text" name="organizer" placeholder="Kemendikbudristek / Pemprov" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Keterangan medali, sertifikat, dsb..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-achievement').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold cursor-pointer">Simpan Prestasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Prestasi -->
<div id="modal-edit-achievement" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Catatan Prestasi</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui data kejuaraan atau capaian siswa.</p>

        <form id="form-edit-achievement" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="student_id" id="edit-ach-student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih Peserta Didik --</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} - Kelas {{ $s->current_class_level }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama Prestasi / Lomba <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit-ach-name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category" id="edit-ach-category" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Akademik">Akademik</option>
                        <option value="Olahraga">Olahraga</option>
                        <option value="Seni & Budaya">Seni & Budaya</option>
                        <option value="Teknologi">Teknologi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tingkat <span class="text-rose-500">*</span></label>
                    <select name="level" id="edit-ach-level" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Sekolah">Sekolah</option>
                        <option value="Kecamatan">Kecamatan</option>
                        <option value="Kota/Kab">Kota / Kab</option>
                        <option value="Provinsi">Provinsi</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Peringkat <span class="text-rose-500">*</span></label>
                    <input type="text" name="rank" id="edit-ach-rank" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Perolehan <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" id="edit-ach-date" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Penyelenggara</label>
                    <input type="text" name="organizer" id="edit-ach-organizer" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" id="edit-ach-notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-achievement').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditAchievement(data) {
        document.getElementById('form-edit-achievement').action = '/achievements/' + data.id;
        document.getElementById('edit-ach-student_id').value = data.student_id;
        document.getElementById('edit-ach-name').value = data.name;
        document.getElementById('edit-ach-category').value = data.category;
        document.getElementById('edit-ach-level').value = data.level;
        document.getElementById('edit-ach-rank').value = data.rank;
        document.getElementById('edit-ach-date').value = data.date;
        document.getElementById('edit-ach-organizer').value = data.organizer || '';
        document.getElementById('edit-ach-notes').value = data.notes || '';
        document.getElementById('modal-edit-achievement').classList.remove('hidden');
    }
</script>
@endsection

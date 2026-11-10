@extends('layouts.app')

@section('title', 'Kedisiplinan & Pelanggaran Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Kedisiplinan & Pelanggaran Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Sistem poin pelanggaran tata tertib, tindak lanjut bimbingan konseling, dan pembinaan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('sis.sync-violations') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer" title="Tarik catatan pelanggaran dari SIS-SMA1LE">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    ⚡ Tarik dari SIS-SMA1LE
                </button>
            </form>
            <a href="{{ route('violations.print-book') }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Buku Pelanggaran
            </a>
            <x-import-modal type="violations" title="Pelanggaran Siswa" />
            <button onclick="document.getElementById('modal-violation').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Pelanggaran
            </button>
        </div>
    </div>

    <!-- Violations Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">Bentuk Pelanggaran</th>
                        <th class="px-5 py-3.5 text-center">Bobot Poin</th>
                        <th class="px-5 py-3.5">Tindakan / Sanksi</th>
                        <th class="px-5 py-3.5">Status Penyelesaian</th>
                        <th class="px-5 py-3.5">Guru / Petugas BK</th>
                        <th class="px-5 py-3.5 text-right">Aksi & Surat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($violations as $vio)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $vio->violation_date?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $vio->student?->name }}
                                <div class="text-[11px] text-slate-400 font-mono font-normal">NIS: {{ $vio->student?->nis }} &bull; Kelas {{ $vio->student?->current_class_level }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $vio->violation_name }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-md font-bold text-xs bg-rose-100 text-rose-800 border border-rose-200">
                                    +{{ $vio->points }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $vio->action_taken ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($vio->resolution_status === 'Selesai' || $vio->resolution_status === 'Tuntas') bg-emerald-100 text-emerald-800
                                    @elseif($vio->resolution_status === 'Dalam Pembinaan') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $vio->resolution_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $vio->officer_name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('violations.print-sp', $vio) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg text-rose-700 hover:bg-rose-50 border border-rose-200 transition text-[11px] font-semibold" title="Cetak Surat Peringatan">
                                    SP
                                </a>
                                <a href="{{ route('violations.print-call', $vio) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded-lg text-amber-700 hover:bg-amber-50 border border-amber-200 transition text-[11px] font-semibold" title="Cetak Surat Panggilan Orang Tua">
                                    Panggilan Ortu
                                </a>
                                <button type="button" onclick="openEditViolation({{ json_encode([
                                    'id' => $vio->id,
                                    'student_id' => $vio->student_id,
                                    'violation_name' => $vio->violation_name,
                                    'points' => $vio->points,
                                    'violation_date' => $vio->violation_date?->format('Y-m-d'),
                                    'chronology' => $vio->chronology,
                                    'action_taken' => $vio->action_taken,
                                    'resolution_status' => $vio->resolution_status,
                                    'officer_name' => $vio->officer_name,
                                ]) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Edit Pelanggaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('violations.destroy', $vio) }}" method="POST" onsubmit="return confirm('Hapus catatan pelanggaran ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Hapus Pelanggaran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada catatan pelanggaran tata tertib (Siswa tertib dan disiplin).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($violations->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $violations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Catat Pelanggaran -->
<div id="modal-violation" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Catat Pelanggaran Kedisiplinan</h2>
        <p class="text-xs text-slate-500 mb-4">Input data pelanggaran tata tertib dan tindakan pembinaan.</p>

        <form action="{{ route('students.violations.store') }}" method="POST" class="space-y-4 text-xs">
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
                <label class="block font-semibold text-slate-700 mb-1">Nama / Bentuk Pelanggaran <span class="text-rose-500">*</span></label>
                <input type="text" name="violation_name" placeholder="Contoh: Terlambat masuk sekolah > 15 menit" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Bobot Poin Pelanggaran <span class="text-rose-500">*</span></label>
                    <input type="number" name="points" min="1" value="5" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Kejadian <span class="text-rose-500">*</span></label>
                    <input type="date" name="violation_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kronologi Kejadian</label>
                <textarea name="chronology" rows="2" placeholder="Uraian singkat waktu dan situasi pelanggaran..." class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tindakan / Sanksi Edukatif</label>
                <input type="text" name="action_taken" placeholder="Contoh: Peringatan lisan dan tugas kebersihan perpustakaan" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Penanganan <span class="text-rose-500">*</span></label>
                    <select name="resolution_status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Selesai">Selesai (Tuntas)</option>
                        <option value="Dalam Pembinaan">Dalam Pembinaan</option>
                        <option value="Panggilan Orang Tua">Panggilan Orang Tua</option>
                        <option value="Skorsing">Skorsing</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Petugas / Guru BK</label>
                    <input type="text" name="officer_name" value="{{ auth()->user()->name }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-violation').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold cursor-pointer">Simpan Pelanggaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pelanggaran -->
<div id="modal-edit-violation" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-xl">
        <h2 class="text-base font-bold text-slate-900 mb-1">Edit Catatan Pelanggaran</h2>
        <p class="text-xs text-slate-500 mb-4">Perbarui rincian pelanggaran kedisiplinan dan pembinaan.</p>

        <form id="form-edit-violation" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                <select name="student_id" id="edit-vio-student_id" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                    <option value="">-- Pilih Peserta Didik --</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} (NIS: {{ $s->nis }} - Kelas {{ $s->current_class_level }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Nama / Bentuk Pelanggaran <span class="text-rose-500">*</span></label>
                <input type="text" name="violation_name" id="edit-vio-name" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Bobot Poin Pelanggaran <span class="text-rose-500">*</span></label>
                    <input type="number" name="points" id="edit-vio-points" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Kejadian <span class="text-rose-500">*</span></label>
                    <input type="date" name="violation_date" id="edit-vio-date" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kronologi Kejadian</label>
                <textarea name="chronology" id="edit-vio-chronology" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tindakan / Sanksi Edukatif</label>
                <input type="text" name="action_taken" id="edit-vio-action" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Penanganan <span class="text-rose-500">*</span></label>
                    <select name="resolution_status" id="edit-vio-status" required class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        <option value="Selesai">Selesai (Tuntas)</option>
                        <option value="Dalam Pembinaan">Dalam Pembinaan</option>
                        <option value="Panggilan Orang Tua">Panggilan Orang Tua</option>
                        <option value="Skorsing">Skorsing</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Petugas / Guru BK</label>
                    <input type="text" name="officer_name" id="edit-vio-officer" class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-violation').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditViolation(data) {
        document.getElementById('form-edit-violation').action = '/violations/' + data.id;
        document.getElementById('edit-vio-student_id').value = data.student_id;
        document.getElementById('edit-vio-name').value = data.violation_name;
        document.getElementById('edit-vio-points').value = data.points;
        document.getElementById('edit-vio-date').value = data.violation_date;
        document.getElementById('edit-vio-chronology').value = data.chronology || '';
        document.getElementById('edit-vio-action').value = data.action_taken || '';
        document.getElementById('edit-vio-status').value = data.resolution_status;
        document.getElementById('edit-vio-officer').value = data.officer_name || '';
        document.getElementById('modal-edit-violation').classList.remove('hidden');
    }
</script>
@endsection
